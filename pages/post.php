<?php
declare(strict_types=1);
require_once __DIR__ . '/../config.php';
$base = '/';
$baseEsc = htmlspecialchars($base, ENT_QUOTES, 'UTF-8');
$siteUrl = famo_public_site_url();

$slug = isset($_GET['slug']) && is_string($_GET['slug']) ? trim($_GET['slug']) : '';
$pageTitle = 'مقالات وبلاگ | آموزشگاه فامو بابل';
$metaDescription = 'مطالب آموزشی، مشاوره‌ای و روش مطالعه کنکور و تیزهوشان در وبلاگ آموزشگاه فامو بابل.';
$canonicalPath = $slug !== '' ? 'blog/' . rawurlencode($slug) . '/' : 'blog/';
$ogType = 'article';
$webPageType = 'WebPage';
$breadcrumbs = [
    ['name' => 'خانه', 'url' => famo_canonical_url('')],
    ['name' => 'وبلاگ و مقالات', 'url' => famo_canonical_url('blog/')],
];
$extraSchemas = [];
$ssrPost = null;
$isNotFound = false;

if ($slug === '') {
    http_response_code(404);
    $robots = 'noindex, follow';
    $isNotFound = true;
} else {
    $postApiRes = famo_internal_api_get('/public/blog/posts/' . rawurlencode($slug), 2);
    if ($postApiRes['status'] === 404) {
        http_response_code(404);
        $robots = 'noindex, follow';
        $pageTitle = 'مقاله یافت نشد | وبلاگ آموزشگاه فامو';
        $isNotFound = true;
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
            <div id="postContainer"<?= is_array($ssrPost) ? ' data-ssr="1"' : ($isNotFound ? ' data-not-found="1"' : ' data-has-fallback="1"') ?>>
<?php if (is_array($ssrPost)):
    $pTitle = trim((string) ($ssrPost['title'] ?? ''));
    $pCategory = trim((string) ($ssrPost['category'] ?? 'مقالات'));
    $pCover = trim((string) ($ssrPost['cover_image'] ?? ''));
    $pContent = (string) ($ssrPost['content'] ?? '');
    $pCanonical = famo_canonical_url($canonicalPath);
?>
                <nav aria-label="مسیر صفحه" class="text-sm text-gray-500 mb-6 flex flex-wrap items-center gap-2">
                    <a href="/" class="hover:text-[#445D84] transition flex items-center gap-1"><i class="icon icon--sm" data-lucide="house" aria-hidden="true"></i> خانه</a>
                    <span class="text-[#E2D9C6]">/</span>
                    <a href="/blog/" class="hover:text-[#445D84] transition">وبلاگ</a>
                    <span class="text-[#E2D9C6]">/</span>
                    <a href="/blog/?category=<?= rawurlencode($pCategory) ?>" class="hover:text-[#445D84] transition"><?= htmlspecialchars($pCategory, ENT_QUOTES, 'UTF-8') ?></a>
                    <span class="text-[#E2D9C6]">/</span>
                    <span class="text-[#8B786D]"><?= htmlspecialchars($pTitle, ENT_QUOTES, 'UTF-8') ?></span>
                </nav>
                <article class="bg-white rounded-3xl shadow-xl border border-[#E2D9C6] overflow-hidden">
                    <?php if ($pCover !== '' && (str_starts_with($pCover, 'https://') || str_starts_with($pCover, 'http://') || str_starts_with($pCover, '/'))): ?>
                    <div class="relative">
                        <img src="<?= htmlspecialchars($pCover, ENT_QUOTES, 'UTF-8') ?>" alt="تصویر شاخص: <?= htmlspecialchars($pTitle, ENT_QUOTES, 'UTF-8') ?>" decoding="async" fetchpriority="high" class="w-full h-56 sm:h-80 object-cover">
                    </div>
                    <?php endif; ?>
                    <div class="p-6 sm:p-10">
                        <h1 class="text-2xl sm:text-3xl font-bold text-[#445D84] mb-6 leading-relaxed"><?= htmlspecialchars($pTitle, ENT_QUOTES, 'UTF-8') ?></h1>
                        <div class="blog-content prose prose-lg max-w-none text-gray-700 leading-loose text-justify" id="postContent"><?= $pContent !== '' ? $pContent : '<p>محتوا به‌زودی تکمیل می‌شود.</p>' ?></div>
                        <div class="mt-10 pt-6 border-t border-[#E2D9C6] flex items-center justify-between flex-wrap gap-3">
                            <a href="/blog/" class="inline-flex items-center gap-2 bg-[#445D84] text-white px-5 py-2.5 rounded-full text-sm font-bold hover:bg-[#344868] transition">
                                <i class="icon icon--sm" data-lucide="arrow-right" aria-hidden="true"></i> همه مطالب
                            </a>
                            <a href="https://t.me/share/url?url=<?= rawurlencode($pCanonical) ?>&amp;text=<?= rawurlencode($pTitle) ?>" target="_blank" rel="noopener noreferrer" class="bg-[#E2D9C6] text-[#445D84] w-9 h-9 rounded-full flex items-center justify-center hover:scale-110 transition" aria-label="اشتراک‌گذاری در تلگرام">
                                <i class="icon icon--sm" data-lucide="send" aria-hidden="true"></i>
                            </a>
                        </div>
                    </div>
                </article>
<?php elseif ($isNotFound): ?>
                <div class="bg-white rounded-3xl shadow-xl border border-[#E2D9C6] p-8 sm:p-12 text-center">
                    <p class="text-5xl mb-4 font-bold text-[#E2D9C6]">۴۰۴</p>
                    <h1 class="text-2xl font-bold text-[#445D84] mb-3">مقاله مورد نظر یافت نشد</h1>
                    <p class="text-gray-600 mb-6">ممکن است این مقاله منتشر نشده یا نشانی آن تغییر کرده باشد.</p>
                    <a href="/blog/" class="inline-flex items-center gap-2 bg-[#445D84] text-white px-6 py-3 rounded-full font-bold hover:bg-[#344868] transition">
                        <i class="icon icon--sm" data-lucide="arrow-right" aria-hidden="true"></i> بازگشت به وبلاگ
                    </a>
                </div>
<?php else: ?>
                <div class="bg-white rounded-3xl shadow-xl border border-[#E2D9C6] p-8 sm:p-10 text-center">
                    <h1 class="text-2xl font-bold text-[#445D84] mb-3">مقاله آموزشی آموزشگاه فامو</h1>
                    <p class="text-gray-600 mb-6 leading-relaxed">در حال دریافت متن کامل مقاله هستیم. همچنین می‌توانید سایر مطالب و راهنماهای آموزشی فامو را در صفحه وبلاگ مطالعه کنید.</p>
                    <a href="/blog/" class="inline-flex items-center gap-2 bg-[#445D84] text-white px-6 py-3 rounded-full font-bold hover:bg-[#344868] transition">
                        مشاهده همه مقالات وبلاگ
                    </a>
                </div>
<?php endif; ?>
            </div>
        </div>
    </section>
    </main>

<!-- Footer -->
<?php include __DIR__ . '/../layouts/footer.php'; ?>

    <script type="module" src="<?= htmlspecialchars(famo_local_asset('assets/js/blog.js', $base), ENT_QUOTES, 'UTF-8') ?>"></script>
</body>
</html>
