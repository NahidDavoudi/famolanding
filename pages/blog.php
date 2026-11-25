<?php
declare(strict_types=1);
require_once __DIR__ . '/../config.php';
$base = '/';
$baseEsc = htmlspecialchars($base, ENT_QUOTES, 'UTF-8');
$pageTitle = 'وبلاگ و مقالات آموزشی | آموزشگاه فامو بابل';
$metaDescription = 'مقالات آموزشی، روش مطالعه، برنامه‌ریزی تحصیلی، مدیریت استرس آزمون و راهنمای آمادگی کنکور و تیزهوشان در آموزشگاه فامو بابل.';
// Category query strings are filter views of the same hub, so canonical stays on /blog/.
$canonicalPath = 'blog/';
$webPageType = 'Blog';
$breadcrumbs = [
    ['name' => 'خانه', 'url' => famo_canonical_url('')],
    ['name' => 'وبلاگ و مقالات', 'url' => famo_canonical_url('blog/')],
];
$page_libs = [
    'gsap'           => false,
    'scroll_trigger' => false,
    'scroll_to'      => false,
    'swiper'         => false,
    'animation'      => false,
];

$reqCategory = isset($_GET['category']) && is_string($_GET['category']) ? trim($_GET['category']) : '';
$reqPage = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;

$ssrPosts = [];
if (famo_env('HOME_SSR', '') !== '0' && $reqCategory === '' && $reqPage === 1) {
    $res = famo_internal_api_get('/public/blog/posts?page=1', 2);
    if (is_array($res['json']['data']['posts'] ?? null)) {
        $ssrPosts = array_values(array_filter($res['json']['data']['posts'], 'is_array'));
    }
}
$hasPostsSsr = count($ssrPosts) > 0;
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include __DIR__ . '/../layouts/head-common.php'; ?>
</head>
<body class="font-family text-gray-800" data-page="blog-index">

<!-- Header -->
<?php include __DIR__ . '/../layouts/header.php'; ?>

<main id="main-content">
<!-- Blog Index -->
<section class="pt-28 sm:pt-32 pb-12 gradient-bg text-white text-center">
    <div class="container mx-auto px-4">
        <nav aria-label="مسیر صفحه" class="text-xs sm:text-sm text-white/80 mb-4 flex justify-center flex-wrap items-center gap-2">
            <a href="/" class="hover:text-white underline-offset-4 hover:underline">خانه</a>
            <span aria-hidden="true">/</span>
            <span class="text-white font-semibold">وبلاگ و مقالات</span>
        </nav>
        <h1 class="text-3xl sm:text-4xl font-bold mb-4 text-white">مقالات و راهنمای کنکور آموزشگاه فامو</h1>
        <p class="text-white/85 max-w-2xl mx-auto text-base sm:text-lg leading-relaxed">
            مقالات تخصصی روش مطالعه، برنامه‌ریزی کنکور، مدیریت استرس آزمون و راهنمای موفقیت در تیزهوشان
        </p>
    </div>
</section>

<!-- Category Badges -->
<section class="py-8 px-4" aria-label="دسته‌بندی مقالات">
    <div class="container mx-auto max-w-6xl">
        <div id="categoryFilters" class="flex flex-wrap justify-center gap-2">
            <a href="/blog/" class="shrink-0 whitespace-nowrap px-4 py-2 rounded-full text-sm font-bold border transition bg-[#445D84] text-white border-[#445D84]">همه</a>
            <a href="/blog/?category=کنکور" class="shrink-0 whitespace-nowrap px-4 py-2 rounded-full text-sm font-bold border transition bg-white text-gray-600 border-[#E2D9C6] hover:bg-[#E2D9C6]">کنکور</a>
            <a href="/blog/?category=تیزهوشان" class="shrink-0 whitespace-nowrap px-4 py-2 rounded-full text-sm font-bold border transition bg-white text-gray-600 border-[#E2D9C6] hover:bg-[#E2D9C6]">تیزهوشان</a>
            <a href="/blog/?category=مشاوره+تحصیلی" class="shrink-0 whitespace-nowrap px-4 py-2 rounded-full text-sm font-bold border transition bg-white text-gray-600 border-[#E2D9C6] hover:bg-[#E2D9C6]">مشاوره تحصیلی</a>
            <a href="/blog/?category=روش+مطالعه" class="shrink-0 whitespace-nowrap px-4 py-2 rounded-full text-sm font-bold border transition bg-white text-gray-600 border-[#E2D9C6] hover:bg-[#E2D9C6]">روش مطالعه</a>
        </div>
    </div>
</section>

<!-- Detailed Posts List -->
<section class="py-8 px-4" aria-labelledby="blog-posts-heading">
    <h2 id="blog-posts-heading" class="sr-only">فهرست مقالات آموزشی</h2>
    <div class="container mx-auto max-w-6xl space-y-8" id="postsContainer"<?= $hasPostsSsr ? ' data-ssr="1"' : ' data-has-fallback="1"' ?>>
<?php if ($hasPostsSsr): ?>
<?php foreach ($ssrPosts as $post):
    $postSlug = trim((string) ($post['slug'] ?? ''));
    $postTitle = trim((string) ($post['title'] ?? ''));
    $postExcerpt = trim((string) ($post['excerpt'] ?? ''));
    $postHref = $postSlug !== '' ? '/blog/' . rawurlencode($postSlug) . '/' : '/blog/';
?>
        <article class="bg-white rounded-2xl overflow-hidden shadow-lg border border-[#E2D9C6] hover-lift transition-all duration-300 hover:shadow-2xl group">
            <a href="<?= htmlspecialchars($postHref, ENT_QUOTES, 'UTF-8') ?>" class="block">
                <div class="p-6">
                    <h3 class="text-lg font-bold text-[#445D84] mb-2 leading-snug"><?= htmlspecialchars($postTitle, ENT_QUOTES, 'UTF-8') ?></h3>
                    <?php if ($postExcerpt !== ''): ?>
                    <p class="text-sm text-gray-600 leading-relaxed line-clamp-3"><?= htmlspecialchars($postExcerpt, ENT_QUOTES, 'UTF-8') ?></p>
                    <?php endif; ?>
                    <span class="inline-flex items-center gap-1 mt-4 text-[#445D84] font-bold text-sm">
                        ادامه مطلب
                        <i class="icon icon--sm" data-lucide="chevron-left" aria-hidden="true"></i>
                    </span>
                </div>
            </a>
        </article>
<?php endforeach; ?>
<?php else: ?>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <article class="bg-white rounded-2xl shadow-md border border-[#E2D9C6] p-6">
                <h3 class="text-lg font-bold text-[#445D84] mb-2">راهنمای برنامه‌ریزی کنکور و امتحان نهایی</h3>
                <p class="text-sm text-gray-600 leading-relaxed mb-4">چطور بین امتحانات نهایی متوسطه دوم و تست‌زنی کنکور تعادل بسازیم و با کارنامه هفتگی روند پیشرفت را کنترل کنیم.</p>
                <a href="/courses/konkur-babol/" class="inline-flex items-center gap-1 text-[#445D84] font-bold text-sm hover:underline">
                    مشاهده راهنمای کنکور بابل
                    <i class="icon icon--sm" data-lucide="chevron-left" aria-hidden="true"></i>
                </a>
            </article>
            <article class="bg-white rounded-2xl shadow-md border border-[#E2D9C6] p-6">
                <h3 class="text-lg font-bold text-[#445D84] mb-2">مسیر آمادگی آزمون تیزهوشان ششم و نهم</h3>
                <p class="text-sm text-gray-600 leading-relaxed mb-4">معرفی بخش‌های استعداد تحلیلی، سرعت و دقت و نحوه برنامه‌ریزی اصولی برای مدارس شهید بهشتی و فرزانگان بابل.</p>
                <a href="/courses/tizhooshan-babol/" class="inline-flex items-center gap-1 text-[#445D84] font-bold text-sm hover:underline">
                    مشاهده راهنمای تیزهوشان بابل
                    <i class="icon icon--sm" data-lucide="chevron-left" aria-hidden="true"></i>
                </a>
            </article>
            <article class="bg-white rounded-2xl shadow-md border border-[#E2D9C6] p-6">
                <h3 class="text-lg font-bold text-[#445D84] mb-2">روش مطالعه مفهومی ریاضی و دروس تخصصی</h3>
                <p class="text-sm text-gray-600 leading-relaxed mb-4">تکنیک‌های تثبیت پیش‌نیازها، رفع اشکال هدفمند و مرور منظم مباحث کلیدی در ریاضی، فیزیک، شیمی و زیست‌شناسی.</p>
                <a href="/courses/math-babol/" class="inline-flex items-center gap-1 text-[#445D84] font-bold text-sm hover:underline">
                    مشاهده برنامه ریاضی بابل
                    <i class="icon icon--sm" data-lucide="chevron-left" aria-hidden="true"></i>
                </a>
            </article>
            <article class="bg-white rounded-2xl shadow-md border border-[#E2D9C6] p-6">
                <h3 class="text-lg font-bold text-[#445D84] mb-2">اصول مشاوره تحصیلی و تحلیل آزمون</h3>
                <p class="text-sm text-gray-600 leading-relaxed mb-4">چگونه برنامه مطالعه شخصی‌سازی‌شده بنویسیم و از داده‌های آزمون‌های هفتگی برای رشد واقعی درصدها استفاده کنیم.</p>
                <a href="/counseling-babol/" class="inline-flex items-center gap-1 text-[#445D84] font-bold text-sm hover:underline">
                    آشنایی با مشاوره تحصیلی بابل
                    <i class="icon icon--sm" data-lucide="chevron-left" aria-hidden="true"></i>
                </a>
            </article>
        </div>
<?php endif; ?>
    </div>
    <nav id="pagination" aria-label="صفحه‌بندی مقالات" class="container mx-auto max-w-6xl mt-10"></nav>
</section>
</main>

<!-- Footer -->
<?php include __DIR__ . '/../layouts/footer.php'; ?>

<script type="module" src="<?= htmlspecialchars(famo_local_asset('assets/js/blog.js', $base), ENT_QUOTES, 'UTF-8') ?>"></script>
</body>
</html>
