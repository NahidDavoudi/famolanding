#!/usr/bin/env bash
set -euo pipefail

BASE_URL="${BASE_URL:-http://127.0.0.1:80}"

# Generate a random 43-char base64url token that matches ^[A-Za-z0-9_-]{32,64}$
if [[ -z "${INVALID_TOKEN:-}" ]]; then
  if command -v openssl >/dev/null 2>&1; then
    INVALID_TOKEN="$(openssl rand -base64 32 | tr '+/' '-_' | tr -d '=')"
  elif command -v python3 >/dev/null 2>&1; then
    INVALID_TOKEN="$(python3 -c 'import os,base64;print(base64.urlsafe_b64encode(os.urandom(32)).decode().rstrip("="))')"
  else
    INVALID_TOKEN="$(node -e "process.stdout.write(require('crypto').randomBytes(32).toString('base64url'))")"
  fi
fi

TMP_DIR="$(mktemp -d)"
trap 'rm -rf "$TMP_DIR"' EXIT

assert_security_headers() {
  local headers_file="$1"
  local label="$2"

  grep -qiE '^Referrer-Policy:\s*no-referrer\b' "$headers_file" || {
    echo "FAIL [$label]: missing Referrer-Policy: no-referrer" >&2
    cat "$headers_file" >&2
    exit 1
  }
  grep -qiE '^X-Robots-Tag:\s*noindex,\s*nofollow\b' "$headers_file" || {
    echo "FAIL [$label]: missing X-Robots-Tag: noindex, nofollow" >&2
    cat "$headers_file" >&2
    exit 1
  }
  grep -qiE '^Cache-Control:.*\bno-store\b' "$headers_file" || {
    echo "FAIL [$label]: missing Cache-Control: no-store" >&2
    cat "$headers_file" >&2
    exit 1
  }
  grep -qiE '^X-Content-Type-Options:\s*nosniff\b' "$headers_file" || {
    echo "FAIL [$label]: missing X-Content-Type-Options: nosniff" >&2
    cat "$headers_file" >&2
    exit 1
  }
}

check_url() {
  local url="$1"
  local expected_status="$2"
  local label="$3"
  local hdr="$TMP_DIR/${label}.hdr"
  local body="$TMP_DIR/${label}.body"

  local status
  status="$(curl -sS -o "$body" -D "$hdr" -w '%{http_code}' "$url")"

  if [[ "$status" != "$expected_status" ]]; then
    echo "FAIL [$label]: expected HTTP $expected_status, got $status" >&2
    exit 1
  fi

  assert_security_headers "$hdr" "$label"
  echo "PASS [$label]: HTTP $status + security headers verified"
}

check_url "$BASE_URL/r/$INVALID_TOKEN" "404" "invalid_token"
check_url "$BASE_URL/r.php?token=too_short" "404" "malformed_token"

if [[ -n "${EXPIRED_TOKEN:-}" ]]; then
  check_url "$BASE_URL/r/$EXPIRED_TOKEN" "404" "expired_token"
else
  echo "SKIP [expired_token]: EXPIRED_TOKEN env var not set"
fi

if [[ -n "${VALID_TOKEN:-}" ]]; then
  check_url "$BASE_URL/r/$VALID_TOKEN" "200" "valid_token"
else
  echo "SKIP [valid_token]: VALID_TOKEN env var not set"
fi
