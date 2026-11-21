<?php
declare(strict_types=1);
require_once __DIR__ . '/config.php';

header('Content-Type: application/xml; charset=UTF-8');
header('Cache-Control: public, max-age=1800');

$urls = [];
$maxPages = 10;

for ($page = 1; $page <= $maxPages; $page++) {
    $res = famo_internal_api_get('/public/blog/posts?page=' . $page, 2);
    $posts = is_array($res['json']['data']['posts'] ?? null) ? $res['json']['data']['posts'] : [];
    if (count($posts) === 0) {
        break;
    }

    foreach ($posts as $post) {
        if (!is_array($post) || empty($post['slug'])) {
            continue;
        }
        $slug = (string) $post['slug'];
        $loc = famo_canonical_url('pages/post.php?slug=' . rawurlencode($slug));
        $rawDate = (string) ($post['updated_at'] ?? $post['published_at'] ?? '');
        $lastmod = '';
        if (preg_match('/^(\d{4}-\d{2}-\d{2})/', $rawDate, $m) === 1) {
            $lastmod = $m[1];
        }
        $urls[$slug] = [
            'loc'     => $loc,
            'lastmod' => $lastmod,
        ];
    }

    $totalPages = (int) ($res['json']['pagination']['total_pages'] ?? 1);
    if ($page >= $totalPages) {
        break;
    }
}

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
foreach ($urls as $item) {
    echo "    <url>\n";
    echo '        <loc>' . htmlspecialchars($item['loc'], ENT_XML1 | ENT_QUOTES, 'UTF-8') . "</loc>\n";
    if ($item['lastmod'] !== '') {
        echo '        <lastmod>' . htmlspecialchars($item['lastmod'], ENT_XML1 | ENT_QUOTES, 'UTF-8') . "</lastmod>\n";
    }
    echo "        <changefreq>monthly</changefreq>\n";
    echo "        <priority>0.8</priority>\n";
    echo "    </url>\n";
}
echo "</urlset>\n";
