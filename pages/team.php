<?php
require_once __DIR__ . '/../config.php';
$base = '../';
$baseEsc = htmlspecialchars($base, ENT_QUOTES, 'UTF-8');
$pageTitle = 'اساتید و مشاوران کنکور و تیزهوشان بابل | آموزشگاه فامو';
$metaDescription = 'معرفی اساتید برجسته کنکور، مدال‌آوران المپیاد، مشاوران تخصصی و تیم پشتیبانی ۲۴ ساعته آموزشگاه فامو بابل برای همراهی تا قبولی.';
$canonicalPath = 'pages/team.php';
$webPageType = 'AboutPage';
$ogImage = famo_public_site_url() . '/assets/images/team.webp';
$ogImageAlt = 'کادر آموزشی، اساتید و مشاوران آموزشگاه فامو بابل';
$breadcrumbs = [
    ['name' => 'خانه', 'url' => famo_canonical_url('')],
    ['name' => 'اساتید و مشاوران', 'url' => famo_canonical_url('pages/team.php')],
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

<body class="font-family text-gray-800 bg-[#f9f7f3]">

<!-- Header -->
<?php include __DIR__ . '/../layouts/header.php'; ?>

<main id="main-content">

<section class="pt-28 sm:pt-32 pb-12 gradient-bg text-white text-center">
    <div class="container mx-auto px-4">
        <h1 class="text-3xl sm:text-4xl font-bold mb-4">کادر آموزشی و مشاوران فامو بابل</h1>
        <div class="relative w-full h-56 sm:h-72 md:h-96 rounded-2xl overflow-hidden shadow-2xl border border-white/20 mb-8">
            <img src="<?= $baseEsc ?>assets/images/team.webp" alt="کادر آموزشی، اساتید و مشاوران آموزشگاه فامو بابل"
                width="1200" height="670" decoding="async" fetchpriority="high" class="w-full h-full object-cover">
        </div>
        <p class="text-white/80 max-w-2xl mx-auto">همراهی اساتید مجرب و پشتیبانان رتبه برتر کنکور در تمامی مراحل
            تحصیلی</p>
    </div>
</section>

<!-- Instructors Section -->
<section class="py-16 px-4">
    <div class="container mx-auto max-w-6xl">
        <h2 class="text-2xl font-bold text-[#445D84] mb-8 border-r-4 border-[#445D84] pr-3">اساتید تخصصی کنکور و
            تیزهوشان</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8" id="instructorsContainer" aria-busy="true">
            <!-- Skeleton loaders shown until the API responds -->
            <div class="famo-inst-skel" aria-hidden="true">
                <div class="skeleton-shimmer skel-inst-media"></div>
                <div class="skeleton-shimmer skel-bar skel-bar--inst-name"></div>
                <div class="skeleton-shimmer skel-bar skel-bar--inst-title"></div>
                <div class="skeleton-shimmer skel-bar skel-bar--inst-bio"></div>
                <div class="skeleton-shimmer skel-bar skel-bar--inst-bio-short"></div>
                <div class="skel-inst-socials"><span class="skeleton-shimmer"></span><span class="skeleton-shimmer"></span></div>
            </div>
            <div class="famo-inst-skel" aria-hidden="true">
                <div class="skeleton-shimmer skel-inst-media"></div>
                <div class="skeleton-shimmer skel-bar skel-bar--inst-name"></div>
                <div class="skeleton-shimmer skel-bar skel-bar--inst-title"></div>
                <div class="skeleton-shimmer skel-bar skel-bar--inst-bio"></div>
                <div class="skeleton-shimmer skel-bar skel-bar--inst-bio-short"></div>
                <div class="skel-inst-socials"><span class="skeleton-shimmer"></span><span class="skeleton-shimmer"></span></div>
            </div>
            <div class="famo-inst-skel" aria-hidden="true">
                <div class="skeleton-shimmer skel-inst-media"></div>
                <div class="skeleton-shimmer skel-bar skel-bar--inst-name"></div>
                <div class="skeleton-shimmer skel-bar skel-bar--inst-title"></div>
                <div class="skeleton-shimmer skel-bar skel-bar--inst-bio"></div>
                <div class="skeleton-shimmer skel-bar skel-bar--inst-bio-short"></div>
                <div class="skel-inst-socials"><span class="skeleton-shimmer"></span><span class="skeleton-shimmer"></span></div>
            </div>
        </div>
    </div>
</section>

<!-- Support Team Section -->
<section id="support" class="py-16 px-4 bg-white">
    <div class="container mx-auto max-w-6xl">
        <h2 class="text-2xl font-bold text-[#445D84] mb-8 border-r-4 border-[#445D84] pr-3">تیم پشتیبانی ۲۴/۷</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8" id="supportersContainer" aria-busy="true">
            <!-- Skeleton loaders shown until the API responds -->
            <div class="famo-inst-skel" aria-hidden="true">
                <div class="skeleton-shimmer skel-inst-media"></div>
                <div class="skeleton-shimmer skel-bar skel-bar--inst-name"></div>
                <div class="skeleton-shimmer skel-bar skel-bar--inst-title"></div>
                <div class="skeleton-shimmer skel-bar skel-bar--inst-bio"></div>
                <div class="skeleton-shimmer skel-bar skel-bar--inst-bio-short"></div>
                <div class="skel-inst-socials"><span class="skeleton-shimmer"></span><span class="skeleton-shimmer"></span></div>
            </div>
            <div class="famo-inst-skel" aria-hidden="true">
                <div class="skeleton-shimmer skel-inst-media"></div>
                <div class="skeleton-shimmer skel-bar skel-bar--inst-name"></div>
                <div class="skeleton-shimmer skel-bar skel-bar--inst-title"></div>
                <div class="skeleton-shimmer skel-bar skel-bar--inst-bio"></div>
                <div class="skeleton-shimmer skel-bar skel-bar--inst-bio-short"></div>
                <div class="skel-inst-socials"><span class="skeleton-shimmer"></span><span class="skeleton-shimmer"></span></div>
            </div>
            <div class="famo-inst-skel" aria-hidden="true">
                <div class="skeleton-shimmer skel-inst-media"></div>
                <div class="skeleton-shimmer skel-bar skel-bar--inst-name"></div>
                <div class="skeleton-shimmer skel-bar skel-bar--inst-title"></div>
                <div class="skeleton-shimmer skel-bar skel-bar--inst-bio"></div>
                <div class="skeleton-shimmer skel-bar skel-bar--inst-bio-short"></div>
                <div class="skel-inst-socials"><span class="skeleton-shimmer"></span><span class="skeleton-shimmer"></span></div>
            </div>
        </div>
    </div>
</section>
</main>
<!-- footer -->
<?php include __DIR__ . '/../layouts/footer.php'; ?>

<script type="module" src="<?= htmlspecialchars(famo_local_asset('assets/js/team.js', $base), ENT_QUOTES, 'UTF-8') ?>"></script>
</body>
</html>
