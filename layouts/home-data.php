<?php
declare(strict_types=1);

require_once __DIR__ . '/../config.php';

/**
 * Extensible endpoint map for homepage SSR data.
 * Adding another homepage feed (e.g. 'admissions' => '/public/admissions?featured=1')
 * is a single-line entry here.
 */
const FAMO_HOME_DATA_ENDPOINTS = [
    'courses'      => '/public/courses',
    'instructors'  => '/public/instructors',
    'supporters'   => '/public/supporters',
    'testimonials' => '/public/testimonials?page=1&per_page=3',
];

const FAMO_HOME_CACHE_TTL_SECONDS        = 60;
const FAMO_HOME_CACHE_MAX_STALE_SECONDS  = 3600;
const FAMO_HOME_CACHE_RETRY_AFTER_SECOND = 15;
const FAMO_HOME_CACHE_FILENAME           = 'home-data-v1.json';
const FAMO_HOME_LOCK_FILENAME            = 'home-data-v1.lock';
const FAMO_TESTI_CACHE_FILENAME          = 'testimonials-page1-v1.json';
const FAMO_TESTI_LOCK_FILENAME           = 'testimonials-page1-v1.lock';

/**
 * Return an array keyed by every configured endpoint, all set to null.
 *
 * @return array<string, ?array<mixed>>
 */
function famo_home_empty_result(): array
{
    $out = [];
    foreach (FAMO_HOME_DATA_ENDPOINTS as $key => $_endpoint) {
        $out[$key] = null;
    }
    return $out;
}

/**
 * Normalize an endpoint list value: only non-empty arrays are returned; empty arrays become null.
 *
 * @return ?array<int, mixed>
 */
function famo_home_normalize_list(mixed $value): ?array
{
    if (!is_array($value) || count($value) === 0) {
        return null;
    }
    return array_values($value);
}

/**
 * Normalize a testimonials payload from either the live API shape
 * (`{ data: { items: [...], summary: {...} }, pagination: {...} }`) or a flat list (`{ data: [...] }`).
 * Returns null when there are no valid testimonial items.
 *
 * @return ?array{items: array<int, array<string, mixed>>, summary: ?array<string, mixed>, pagination: ?array<string, mixed>}
 */
function famo_home_normalize_testimonials(mixed $dataValue, mixed $rootPayload = null): ?array
{
    if (!is_array($dataValue)) {
        return null;
    }

    $rawItems = null;
    $summary = null;
    $pagination = null;

    if (array_key_exists('items', $dataValue)) {
        $rawItems = is_array($dataValue['items']) ? $dataValue['items'] : [];
        if (isset($dataValue['summary']) && is_array($dataValue['summary'])) {
            $summary = $dataValue['summary'];
        }
        if (isset($dataValue['pagination']) && is_array($dataValue['pagination'])) {
            $pagination = $dataValue['pagination'];
        }
    } elseif (array_is_list($dataValue)) {
        $rawItems = $dataValue;
    } else {
        return null;
    }

    if (is_array($rootPayload)) {
        if ($summary === null && isset($rootPayload['summary']) && is_array($rootPayload['summary'])) {
            $summary = $rootPayload['summary'];
        }
        if ($pagination === null && isset($rootPayload['pagination']) && is_array($rootPayload['pagination'])) {
            $pagination = $rootPayload['pagination'];
        }
    }

    $items = array_values(array_filter($rawItems, 'is_array'));
    if (count($items) === 0) {
        return null;
    }

    return [
        'items'      => $items,
        'summary'    => $summary,
        'pagination' => $pagination,
    ];
}

/**
 * Normalize any configured homepage endpoint value by key.
 *
 * @return ?array<mixed>
 */
function famo_home_normalize_endpoint_value(string $key, mixed $dataValue, mixed $rootPayload = null): ?array
{
    if ($key === 'testimonials') {
        return famo_home_normalize_testimonials($dataValue, $rootPayload);
    }
    return famo_home_normalize_list($dataValue);
}

/**
 * Read and decode the JSON cache file if present and valid.
 *
 * @return ?array<string, mixed>
 */
function famo_home_read_cache_file(string $cacheFile): ?array
{
    if (!is_file($cacheFile) || !is_readable($cacheFile)) {
        return null;
    }

    $raw = @file_get_contents($cacheFile);
    if (!is_string($raw) || $raw === '') {
        return null;
    }

    $decoded = json_decode($raw, true);
    if (!is_array($decoded)) {
        return null;
    }

    return $decoded;
}

/**
 * Extract per-key data from a cached payload, enforcing the 1-hour max stale limit per key.
 *
 * @param array<string, mixed> $cached
 * @return array<string, ?array<mixed>>
 */
function famo_home_extract_cached_data(array $cached, int $now): array
{
    $out = famo_home_empty_result();
    $dataMap = isset($cached['data']) && is_array($cached['data']) ? $cached['data'] : [];
    $updatedMap = isset($cached['key_updated_at']) && is_array($cached['key_updated_at']) ? $cached['key_updated_at'] : [];
    $fallbackTs = isset($cached['fetched_at']) && is_int($cached['fetched_at'])
        ? $cached['fetched_at']
        : (int) ($cached['fetched_at'] ?? 0);

    foreach (FAMO_HOME_DATA_ENDPOINTS as $key => $_endpoint) {
        $keyTs = isset($updatedMap[$key]) ? (int) $updatedMap[$key] : $fallbackTs;
        if ($keyTs <= 0 || ($now - $keyTs) > FAMO_HOME_CACHE_MAX_STALE_SECONDS) {
            $out[$key] = null;
            continue;
        }
        $out[$key] = famo_home_normalize_endpoint_value($key, $dataMap[$key] ?? null);
    }

    return $out;
}

/**
 * Fetch all configured homepage endpoints concurrently using curl_multi.
 * Never forwards visitor headers/IP. Never throws.
 *
 * @return array<string, array{ok: bool, data: ?array<mixed>, status: int}>
 */
function famo_home_fetch_endpoints_concurrent(): array
{
    $baseUrl = rtrim(famo_internal_api_url(), '/');
    $results = [];
    foreach (FAMO_HOME_DATA_ENDPOINTS as $key => $_endpoint) {
        $results[$key] = ['ok' => false, 'data' => null, 'status' => 0];
    }

    if (function_exists('curl_multi_init') && function_exists('curl_init')) {
        $mh = curl_multi_init();
        if ($mh === false) {
            return $results;
        }

        $handles = [];
        foreach (FAMO_HOME_DATA_ENDPOINTS as $key => $endpoint) {
            $url = $baseUrl . '/' . ltrim($endpoint, '/');
            $ch = curl_init($url);
            if ($ch === false) {
                continue;
            }
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => false,
                CURLOPT_CONNECTTIMEOUT => 1,
                CURLOPT_TIMEOUT        => 2,
                CURLOPT_HTTPHEADER     => ['Accept: application/json'],
            ]);
            curl_multi_add_handle($mh, $ch);
            $handles[$key] = $ch;
        }

        $active = null;
        do {
            $mrc = curl_multi_exec($mh, $active);
            if ($active > 0 && $mrc === CURLM_OK) {
                $selected = curl_multi_select($mh, 0.2);
                if ($selected === -1) {
                    usleep(10000);
                }
            }
        } while ($active > 0 && $mrc === CURLM_OK);

        foreach ($handles as $key => $ch) {
            $errno = curl_errno($ch);
            $status = (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
            $body = curl_multi_getcontent($ch);
            curl_multi_remove_handle($mh, $ch);
            curl_close($ch);

            if ($errno !== 0 || $status !== 200 || !is_string($body) || $body === '') {
                $results[$key] = ['ok' => false, 'data' => null, 'status' => $status];
                continue;
            }

            $decoded = json_decode($body, true);
            if (!is_array($decoded) || !array_key_exists('data', $decoded) || !is_array($decoded['data'])) {
                $results[$key] = ['ok' => false, 'data' => null, 'status' => $status];
                continue;
            }

            $results[$key] = [
                'ok'     => true,
                'data'   => famo_home_normalize_endpoint_value($key, $decoded['data'], $decoded),
                'status' => 200,
            ];
        }

        curl_multi_close($mh);
        return $results;
    }

    // Fallback if curl_multi is unavailable: reuse famo_internal_api_get()
    foreach (FAMO_HOME_DATA_ENDPOINTS as $key => $endpoint) {
        $res = famo_internal_api_get($endpoint, 2);
        $decoded = $res['json'] ?? null;
        if (($res['status'] ?? 0) === 200 && is_array($decoded) && array_key_exists('data', $decoded) && is_array($decoded['data'])) {
            $results[$key] = [
                'ok'     => true,
                'data'   => famo_home_normalize_endpoint_value($key, $decoded['data'], $decoded),
                'status' => 200,
            ];
        }
    }

    return $results;
}

/**
 * Atomically write cache payload via a sibling temp file + rename().
 *
 * @param array<string, mixed> $payload
 */
function famo_home_write_cache_atomic(string $cacheDir, string $cacheFile, array $payload): void
{
    $json = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    if (!is_string($json)) {
        return;
    }

    try {
        $suffix = bin2hex(random_bytes(4));
    } catch (\Throwable) {
        $suffix = str_replace('.', '', uniqid('', true));
    }

    $prefix = pathinfo($cacheFile, PATHINFO_FILENAME);
    $tmpFile = $cacheDir . '/' . $prefix . '.' . getmypid() . '.' . $suffix . '.tmp';
    $written = @file_put_contents($tmpFile, $json);
    if ($written === false) {
        @unlink($tmpFile);
        return;
    }

    if (!@rename($tmpFile, $cacheFile)) {
        @unlink($tmpFile);
    }
}

/**
 * Merge freshly fetched endpoint results with previous cached values (up to 1 hour old on failure).
 *
 * @param array<string, array{ok: bool, data: ?array<mixed>, status: int}> $fetched
 * @param ?array<string, mixed> $previousCache
 * @return array{payload: array<string, mixed>, data: array<string, ?array<mixed>>}
 */
function famo_home_merge_results(array $fetched, ?array $previousCache, int $now): array
{
    $prevData = is_array($previousCache) && isset($previousCache['data']) && is_array($previousCache['data'])
        ? $previousCache['data']
        : [];
    $prevUpdated = is_array($previousCache) && isset($previousCache['key_updated_at']) && is_array($previousCache['key_updated_at'])
        ? $previousCache['key_updated_at']
        : [];
    $prevFetchedAt = is_array($previousCache) ? (int) ($previousCache['fetched_at'] ?? 0) : 0;

    $newData = [];
    $newUpdated = [];
    $anyFailure = false;

    foreach (FAMO_HOME_DATA_ENDPOINTS as $key => $_endpoint) {
        $item = $fetched[$key] ?? ['ok' => false, 'data' => null, 'status' => 0];
        if ($item['ok']) {
            $newData[$key] = $item['data'];
            $newUpdated[$key] = $now;
        } else {
            $anyFailure = true;
            $keyTs = isset($prevUpdated[$key]) ? (int) $prevUpdated[$key] : $prevFetchedAt;
            $prevVal = famo_home_normalize_endpoint_value($key, $prevData[$key] ?? null);
            if ($prevVal !== null && $keyTs > 0 && ($now - $keyTs) <= FAMO_HOME_CACHE_MAX_STALE_SECONDS) {
                $newData[$key] = $prevVal;
                $newUpdated[$key] = $keyTs;
            } else {
                $newData[$key] = null;
                $newUpdated[$key] = 0;
            }
        }
    }

    $payload = [
        'fetched_at'     => $anyFailure ? $prevFetchedAt : $now,
        'retry_after'    => $anyFailure ? ($now + FAMO_HOME_CACHE_RETRY_AFTER_SECOND) : 0,
        'data'           => $newData,
        'key_updated_at' => $newUpdated,
    ];

    return [
        'payload' => $payload,
        'data'    => $newData,
    ];
}

/**
 * Fetch and cache homepage SSR data (courses, instructors, supporters, testimonials).
 *
 * Returns an associative array keyed by FAMO_HOME_DATA_ENDPOINTS.
 * Empty arrays/items are treated as null. Never throws; degrades gracefully on any issue.
 *
 * @return array<string, ?array<mixed>>
 */
function famo_home_data(): array
{
    try {
        if (trim(famo_env('HOME_SSR', '')) === '0') {
            return famo_home_empty_result();
        }

        $now = time();
        $cacheDir = __DIR__ . '/../storage/cache';
        $cacheFile = $cacheDir . '/' . FAMO_HOME_CACHE_FILENAME;
        $lockFile = $cacheDir . '/' . FAMO_HOME_LOCK_FILENAME;

        $canCache = is_dir($cacheDir) && is_writable($cacheDir);
        if (!$canCache) {
            // Directory missing or read-only: degrade cleanly to no caching without errors.
            $fetched = famo_home_fetch_endpoints_concurrent();
            $out = famo_home_empty_result();
            foreach (FAMO_HOME_DATA_ENDPOINTS as $key => $_endpoint) {
                $out[$key] = ($fetched[$key]['ok'] ?? false) ? $fetched[$key]['data'] : null;
            }
            return $out;
        }

        $cached = famo_home_read_cache_file($cacheFile);
        if ($cached !== null) {
            $fetchedAt = (int) ($cached['fetched_at'] ?? 0);
            $retryAfter = (int) ($cached['retry_after'] ?? 0);

            if ($fetchedAt > 0 && ($now - $fetchedAt) < FAMO_HOME_CACHE_TTL_SECONDS) {
                return famo_home_extract_cached_data($cached, $now);
            }
            if ($retryAfter > 0 && $now < $retryAfter) {
                return famo_home_extract_cached_data($cached, $now);
            }
        }

        $lockFp = @fopen($lockFile, 'c');
        if ($lockFp === false) {
            // Unable to open lock file: degrade to no caching without errors.
            $fetched = famo_home_fetch_endpoints_concurrent();
            $out = famo_home_empty_result();
            foreach (FAMO_HOME_DATA_ENDPOINTS as $key => $_endpoint) {
                $out[$key] = ($fetched[$key]['ok'] ?? false) ? $fetched[$key]['data'] : null;
            }
            return $out;
        }

        try {
            if (!@flock($lockFp, LOCK_EX | LOCK_NB)) {
                // Another worker is refreshing: serve existing cache (up to 1h stale) or all nulls without waiting.
                return $cached !== null
                    ? famo_home_extract_cached_data($cached, $now)
                    : famo_home_empty_result();
            }

            // Re-read inside lock in case the previous holder just refreshed the cache.
            $freshCheck = famo_home_read_cache_file($cacheFile);
            if ($freshCheck !== null) {
                $fetchedAt = (int) ($freshCheck['fetched_at'] ?? 0);
                $retryAfter = (int) ($freshCheck['retry_after'] ?? 0);
                if (
                    ($fetchedAt > 0 && ($now - $fetchedAt) < FAMO_HOME_CACHE_TTL_SECONDS)
                    || ($retryAfter > 0 && $now < $retryAfter)
                ) {
                    return famo_home_extract_cached_data($freshCheck, $now);
                }
                $cached = $freshCheck;
            }

            $fetched = famo_home_fetch_endpoints_concurrent();
            $merged = famo_home_merge_results($fetched, $cached, $now);
            famo_home_write_cache_atomic($cacheDir, $cacheFile, $merged['payload']);

            return $merged['data'];
        } finally {
            @flock($lockFp, LOCK_UN);
            @fclose($lockFp);
        }
    } catch (\Throwable) {
        return famo_home_empty_result();
    }
}

/**
 * Fetch and cache page 1 of public testimonials for /results/ SSR (60s TTL, 1h stale, 15s backoff).
 * Respects HOME_SSR=0 kill switch. Never forwards visitor headers/IP. Never throws.
 *
 * @return ?array{items: array<int, array<string, mixed>>, summary: ?array<string, mixed>, pagination: ?array<string, mixed>}
 */
function famo_testimonials_page_data(int $perPage = 9): ?array
{
    try {
        if (trim(famo_env('HOME_SSR', '')) === '0') {
            return null;
        }

        $safePerPage = max(1, min(30, $perPage));
        $endpoint = '/public/testimonials?page=1&per_page=' . $safePerPage;
        $now = time();
        $cacheDir = __DIR__ . '/../storage/cache';
        $cacheFile = $cacheDir . '/' . FAMO_TESTI_CACHE_FILENAME;
        $lockFile = $cacheDir . '/' . FAMO_TESTI_LOCK_FILENAME;

        $extractStale = static function (?array $cachedPayload, int $ts): ?array {
            if (!is_array($cachedPayload)) {
                return null;
            }
            $fetchedAt = (int) ($cachedPayload['fetched_at'] ?? 0);
            if ($fetchedAt <= 0 || ($ts - $fetchedAt) > FAMO_HOME_CACHE_MAX_STALE_SECONDS) {
                return null;
            }
            return famo_home_normalize_testimonials($cachedPayload['data'] ?? null);
        };

        $fetchOnce = static function () use ($endpoint): array {
            $res = famo_internal_api_get($endpoint, 2);
            $decoded = $res['json'] ?? null;
            if (
                ($res['status'] ?? 0) === 200
                && is_array($decoded)
                && array_key_exists('data', $decoded)
                && is_array($decoded['data'])
            ) {
                return [
                    'ok'   => true,
                    'data' => famo_home_normalize_testimonials($decoded['data'], $decoded),
                ];
            }
            return ['ok' => false, 'data' => null];
        };

        $canCache = is_dir($cacheDir) && is_writable($cacheDir);
        if (!$canCache) {
            $fetched = $fetchOnce();
            return $fetched['ok'] ? $fetched['data'] : null;
        }

        $cached = famo_home_read_cache_file($cacheFile);
        if ($cached !== null) {
            $fetchedAt = (int) ($cached['fetched_at'] ?? 0);
            $retryAfter = (int) ($cached['retry_after'] ?? 0);
            if ($fetchedAt > 0 && ($now - $fetchedAt) < FAMO_HOME_CACHE_TTL_SECONDS) {
                return $extractStale($cached, $now);
            }
            if ($retryAfter > 0 && $now < $retryAfter) {
                return $extractStale($cached, $now);
            }
        }

        $lockFp = @fopen($lockFile, 'c');
        if ($lockFp === false) {
            $fetched = $fetchOnce();
            return $fetched['ok'] ? $fetched['data'] : null;
        }

        try {
            if (!@flock($lockFp, LOCK_EX | LOCK_NB)) {
                return $extractStale($cached, $now);
            }

            $freshCheck = famo_home_read_cache_file($cacheFile);
            if ($freshCheck !== null) {
                $fetchedAt = (int) ($freshCheck['fetched_at'] ?? 0);
                $retryAfter = (int) ($freshCheck['retry_after'] ?? 0);
                if (
                    ($fetchedAt > 0 && ($now - $fetchedAt) < FAMO_HOME_CACHE_TTL_SECONDS)
                    || ($retryAfter > 0 && $now < $retryAfter)
                ) {
                    return $extractStale($freshCheck, $now);
                }
                $cached = $freshCheck;
            }

            $fetched = $fetchOnce();
            if ($fetched['ok']) {
                $payload = [
                    'fetched_at'  => $now,
                    'retry_after' => 0,
                    'data'        => $fetched['data'],
                ];
                famo_home_write_cache_atomic($cacheDir, $cacheFile, $payload);
                return $fetched['data'];
            }

            $staleData = $extractStale($cached, $now);
            $payload = [
                'fetched_at'  => $staleData !== null ? (int) ($cached['fetched_at'] ?? 0) : 0,
                'retry_after' => $now + FAMO_HOME_CACHE_RETRY_AFTER_SECOND,
                'data'        => $staleData,
            ];
            famo_home_write_cache_atomic($cacheDir, $cacheFile, $payload);
            return $staleData;
        } finally {
            @flock($lockFp, LOCK_UN);
            @fclose($lockFp);
        }
    } catch (\Throwable) {
        return null;
    }
}
