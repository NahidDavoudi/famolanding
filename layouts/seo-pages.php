<?php
declare(strict_types=1);

/**
 * Single-source-of-truth registry for clean-URL SEO landing pages.
 * Loads data from layouts/seo-pages.json (also consumed by the local Node preview/test harness).
 *
 * @return array<string, array<string, mixed>>
 */
function famo_get_seo_pages(): array
{
    static $cache = null;
    if (is_array($cache)) {
        return $cache;
    }

    $jsonPath = __DIR__ . '/seo-pages.json';
    if (!is_file($jsonPath) || !is_readable($jsonPath)) {
        $cache = [];
        return $cache;
    }

    $raw = file_get_contents($jsonPath);
    if (!is_string($raw) || $raw === '') {
        $cache = [];
        return $cache;
    }

    $decoded = json_decode($raw, true);
    $cache = is_array($decoded) ? $decoded : [];
    return $cache;
}

/**
 * Look up one landing page definition by normalized slug (e.g. 'courses/math-babol').
 *
 * @return array<string, mixed>|null
 */
function famo_get_seo_page(string $slug): ?array
{
    $normalized = trim($slug, "/ \t\n\r\0\x0B");
    if ($normalized === '') {
        return null;
    }
    $pages = famo_get_seo_pages();
    return isset($pages[$normalized]) && is_array($pages[$normalized]) ? $pages[$normalized] : null;
}
