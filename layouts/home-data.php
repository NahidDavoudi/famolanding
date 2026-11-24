<?php
declare(strict_types=1);

require_once __DIR__ . '/../config.php';

/**
 * Extensible endpoint map for homepage SSR data.
 * Adding another homepage feed (e.g. 'admissions' => '/public/admissions?featured=1',
 * 'testimonials' => '/public/testimonials?per_page=2') is a single-line entry here.
 */
const FAMO_HOME_DATA_ENDPOINTS = [
    'courses'     => '/public/courses',
    'instructors' => '/public/instructors',
    'supporters'  => '/public/supporters',
];

const FAMO_HOME_CACHE_TTL_SECONDS        = 60;
const FAMO_HOME_CACHE_MAX_STALE_SECONDS  = 3600;
const FAMO_HOME_CACHE_RETRY_AFTER_SECOND = 15;
const FAMO_HOME_CACHE_FILENAME           = 'home-data-v1.json';
const FAMO_HOME_LOCK_FILENAME            = 'home-data-v1.lock';

/**
 * Return an array keyed by every configured endpoint, all set to null.
 *
 * @return array<string, ?array<int, mixed>>
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
 * Normalize an endpoint data value: only non-empty arrays are returned; empty arrays become null.
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
 * @return array<string, ?array<int, mixed>>
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
        $out[$key] = famo_home_normalize_list($dataMap[$key] ?? null);
    }

    return $out;
}

/**
 * Fetch all configured homepage endpoints concurrently using curl_multi.
 * Never forwards visitor headers/IP. Never throws.
 *
 * @return array<string, array{ok: bool, data: ?array<int, mixed>, status: int}>
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
                'data'   => famo_home_normalize_list($decoded['data']),
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
                'data'   => famo_home_normalize_list($decoded['data']),
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

    $tmpFile = $cacheDir . '/home-data-v1.' . getmypid() . '.' . $suffix . '.tmp';
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
 * @param array<string, array{ok: bool, data: ?array<int, mixed>, status: int}> $fetched
 * @param ?array<string, mixed> $previousCache
 * @return array{payload: array<string, mixed>, data: array<string, ?array<int, mixed>>}
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
            $prevList = famo_home_normalize_list($prevData[$key] ?? null);
            if ($prevList !== null && $keyTs > 0 && ($now - $keyTs) <= FAMO_HOME_CACHE_MAX_STALE_SECONDS) {
                $newData[$key] = $prevList;
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
 * Fetch and cache homepage SSR data (courses, instructors, supporters).
 *
 * Returns an associative array with keys: courses, instructors, supporters.
 * Each value is either a non-empty array or null (empty arrays are treated as null).
 * Never throws; degrades gracefully on any API, lock, or filesystem issue.
 *
 * @return array<string, ?array<int, mixed>>
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
