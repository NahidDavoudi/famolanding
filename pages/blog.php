<?php
require_once __DIR__ . '/../config.php';
$base = '../';
$baseEsc = htmlspecialchars($base, ENT_QUOTES, 'UTF-8');
$categoryParam = isset($_GET['category']) && is_string($_GET['category']) ? trim($_GET['category']) : '';
$pageTitle = 'وبلاگ و مقالات آموزشی کنکور و تیزهوشان | آموزشگاه فامو بابل';
$metaDescription = 'مقالات تخصصی روش مطالعه، برنامه‌ریزی کنکور سراسری، مدیریت استرس آزمون و راهنمای موفقیت در تیزهوشان از مشاوران و اساتید آموزشگاه فامو بابل.';
$canonicalPath = $categoryParam !== '' ? 'pages/blog.php?category=' . rawurlencode($categoryParam) : 'pages/blog.php';
$webPageType = 'Blog';
$breadcrumbs = [
    ['name' => 'خانه', 'url' => famo_canonical_url('')],
    ['name' => 'وبلاگ و مقالات', 'url' => famo_canonical_url('pages/blog.php')],
];
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
<body class="font-family text-gray-800" data-page="blog-index">

<!-- Header -->
<?php include __DIR__ . '/../layouts/header.php'; ?>

<main id="main-content">
<!-- Blog Index -->
<section class="pt-28 sm:pt-32 pb-12 gradient-bg text-white text-center">
    <div class="container mx-auto px-4">
        <h1 class="text-3xl sm:text-4xl font-bold mb-4 text-white">مقالات و راهنمای کنکور آموزشگاه فامو</h1>
        <p class="text-white/85 max-w-2xl mx-auto text-base sm:text-lg leading-relaxed">
            مقالات تخصصی روش مطالعه، برنامه‌ریزی کنکور، مدیریت استرس آزمون و راهنمای موفقیت در تیزهوشان
        </p>
    </div>
</section>

<!-- Category Badges -->
<section class="py-8 px-4" aria-label="دسته‌بندی مقالات">
    <div class="container mx-auto max-w-6xl">
        <div id="categoryFilters" class="flex flex-wrap justify-center gap-2"></div>
    </div>
</section>
<!-- Detailed Posts List -->
<section class="py-8 px-4">
    <div class="container mx-auto max-w-6xl space-y-12" id="postsContainer">
        <!-- Skeleton loaders shown until the API responds -->
        <div
            class="course-skeleton blog-skeleton bg-white rounded-2xl shadow-xl border border-[#E2D9C6] p-6 sm:p-8 flex flex-col md:flex-row gap-8 items-center animate-pulse">
            <div class="md:w-2/3 space-y-4 w-full">
                <div class="h-5 w-32 bg-gray-200 rounded-full"></div>
                <div class="h-7 w-2/3 bg-gray-200 rounded"></div>
                <div class="h-4 w-full bg-gray-200 rounded"></div>
                <div class="h-4 w-5/6 bg-gray-200 rounded"></div>
            </div>
            <div class="md:w-1/3 w-full h-40 bg-gray-100 rounded-xl"></div>
        </div>
        <div
            class="course-skeleton blog-skeleton bg-white rounded-2xl shadow-xl border border-[#E2D9C6] p-6 sm:p-8 flex flex-col md:flex-row gap-8 items-center animate-pulse">
            <div class="md:w-2/3 space-y-4 w-full">
                <div class="h-5 w-32 bg-gray-200 rounded-full"></div>
                <div class="h-7 w-2/3 bg-gray-200 rounded"></div>
                <div class="h-4 w-full bg-gray-200 rounded"></div>
                <div class="h-4 w-5/6 bg-gray-200 rounded"></div>
            </div>
            <div class="md:w-1/3 w-full h-40 bg-gray-100 rounded-xl"></div>
        </div>
    </div>
    <nav id="pagination" aria-label="صفحه‌بندی مقالات" class="container mx-auto max-w-6xl mt-10"></nav>
</section>
</main>

<!-- Footer -->
<?php include __DIR__ . '/../layouts/footer.php'; ?>

<script type="module" src="<?= htmlspecialchars(famo_local_asset('assets/js/blog.js', $base), ENT_QUOTES, 'UTF-8') ?>"></script>
</body>
</html>
