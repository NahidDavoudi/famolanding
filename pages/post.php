<?php
require_once __DIR__ . '/../config.php';
$base = '../';
$baseEsc = htmlspecialchars($base, ENT_QUOTES, 'UTF-8');
$siteUrl = famo_public_site_url();

$slug = isset($_GET['slug']) && is_string($_GET['slug']) ? trim($_GET['slug']) : '';
$pageTitle = 'مقالات وبلاگ | آموزشگاه فامو بابل';
$metaDescription = 'مطالب آموزشی، مشاوره‌ای و روش مطالعه کنکور و تیزهوشان در وبلاگ آموزشگاه فامو بابل.';
$canonicalPath = $slug !== '' ? 'pages/post.php?slug=' . rawurlencode($slug) : 'pages/blog.php';
$ogType = 'article';
$webPageType = 'WebPage';
$breadcrumbs = [
    ['name' => 'خانه', 'url' => famo_canonical_url('')],
    ['name' => 'وبلاگ و مقالات', 'url' => famo_canonical_url('pages/blog.php')],
];
$extraSchemas = [];

if ($slug === '') {
    http_response_code(404);
    $robots = 'noindex, follow';
} else {
    $postApiRes = famo_internal_api_get('/public/blog/posts/' . rawurlencode($slug), 2);
    if ($postApiRes['status'] === 404) {
        http_response_code(404);
        $robots = 'noindex, follow';
        $pageTitle = 'مقاله یافت نشد | وبلاگ آموزشگاه فامو';
    } elseif (is_array($postApiRes['json']['data']['post'] ?? null)) {
        $ssrPost = $postApiRes['json']['data']['post'];
        $postTitle = trim((string) ($ssrPost['title'] ?? ''));
        if ($postTitle !== '') {
            $pageTitle = $postTitle . ' | وبلاگ آموزشگاه فامو';
            $breadcrumbs[] = ['name' => $postTitle, 'url' => famo_canonical_url($canonicalPath)];
        }
        $postDesc = trim((string) ($ssrPost['meta_description'] ?? $ssrPost['excerpt'] ?? ''));
        if ($postDesc !== '') {
            $metaDescription = $postDesc;
        }
        $postCover = trim((string) ($ssrPost['cover_image'] ?? ''));
        if ($postCover !== '') {
            $ogImage = $postCover;
            $ogImageAlt = $postTitle !== '' ? $postTitle : $pageTitle;
        }
        $postCanonical = famo_canonical_url($canonicalPath);
        $blogPosting = [
            '@type'            => 'BlogPosting',
            '@id'              => $postCanonical . '#article',
            'headline'         => $postTitle !== '' ? $postTitle : $pageTitle,
            'description'      => $metaDescription,
            'image'            => $postCover !== '' ? $postCover : ($siteUrl . '/assets/images/poster_hero.webp'),
            'inLanguage'       => 'fa-IR',
            'author'           => ['@id' => $siteUrl . '/#organization'],
            'publisher'        => ['@id' => $siteUrl . '/#organization'],
            'mainEntityOfPage' => ['@id' => $postCanonical . '#webpage'],
        ];
        if (!empty($ssrPost['published_at'])) {
            $blogPosting['datePublished'] = (string) $ssrPost['published_at'];
        }
        if (!empty($ssrPost['updated_at'])) {
            $blogPosting['dateModified'] = (string) $ssrPost['updated_at'];
        } elseif (!empty($ssrPost['published_at'])) {
            $blogPosting['dateModified'] = (string) $ssrPost['published_at'];
        }
        $extraSchemas[] = $blogPosting;
    }
}

$page_libs = [
    'gsap'           => false,
    'scroll_trigger' => false,
    'scroll_to'      => false,
    'swiper'         => false,
    'animation'      => false,
];
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include __DIR__ . '/../layouts/head-common.php'; ?>
</head>

<body class="font-family text-gray-800 bg-[#f9f7f3] min-h-screen flex flex-col" data-page="blog-post">

    <!-- Header -->
<?php include __DIR__ . '/../layouts/header.php'; ?>

    <main id="main-content" class="flex-1">
    <!-- Article Container -->
    <section class="pt-28 sm:pt-32 pb-16 px-4">
        <div class="container mx-auto max-w-4xl">
            <!-- Skeleton -->
            <div id="postContainer">
                <div class="blog-skeleton animate-pulse">
                    <div class="bg-white rounded-3xl shadow-xl border border-[#E2D9C6] overflow-hidden">
                        <div class="h-56 sm:h-80 bg-gray-200"></div>
                        <div class="p-6 sm:p-10 space-y-4">
                            <div class="h-4 w-48 bg-gray-200 rounded"></div>
                            <div class="h-8 w-3/4 bg-gray-200 rounded"></div>
                            <div class="h-4 w-full bg-gray-200 rounded"></div>
                            <div class="h-4 w-full bg-gray-200 rounded"></div>
                            <div class="h-4 w-5/6 bg-gray-200 rounded"></div>
                            <div class="h-4 w-2/3 bg-gray-200 rounded"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    </main>

<!-- Footer -->
<?php include __DIR__ . '/../layouts/footer.php'; ?>

    <script type="module" src="<?= htmlspecialchars(famo_local_asset('assets/js/blog.js', $base), ENT_QUOTES, 'UTF-8') ?>"></script>
</body>
</html>
