#!/usr/bin/env bash
set -euo pipefail

BASE_URL="${BASE_URL:-http://127.0.0.1:3000}"

# Generate a random 43-char base64url token that matches ^[A-Za-z0-9_-]{32,64}$
INVALID_TOKEN="${INVALID_TOKEN:-$(node -e "process.stdout.write(require('crypto').randomBytes(32).toString('base64url'))")}"

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

check_route() {
  local token="$1"
  local expected_status="$2"
  local label="$3"
  local hdr="$TMP_DIR/${label}.hdr"
  local body="$TMP_DIR/${label}.body"

  local status
  status="$(curl -sS -o "$body" -D "$hdr" -w '%{http_code}' "$BASE_URL/r/$token")"

  if [[ "$status" != "$expected_status" ]]; then
    echo "FAIL [$label]: expected HTTP $expected_status, got $status" >&2
    exit 1
  fi

  assert_security_headers "$hdr" "$label"
  echo "PASS [$label]: HTTP $status + security headers verified"
}

check_route "$INVALID_TOKEN" "404" "invalid_token"
check_route "too_short" "404" "malformed_token"

if [[ -n "${EXPIRED_TOKEN:-}" ]]; then
  check_route "$EXPIRED_TOKEN" "404" "expired_token"
else
  echo "SKIP [expired_token]: EXPIRED_TOKEN env var not set"
fi

if [[ -n "${VALID_TOKEN:-}" ]]; then
  check_route "$VALID_TOKEN" "200" "valid_token"
else
  echo "SKIP [valid_token]: VALID_TOKEN env var not set"
fi
