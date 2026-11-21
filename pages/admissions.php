<?php
require_once __DIR__ . '/../config.php';
$base = '../';
$baseEsc = htmlspecialchars($base, ENT_QUOTES, 'UTF-8');
$pageTitle = 'قبولی‌های سال‌های قبل کنکور و تیزهوشان | آموزشگاه فامو بابل';
$metaDescription = 'فهرست قبولی‌ها و رتبه‌های برتر کنکور سراسری، تیزهوشان، نمونه دولتی و المپیادهای علمی آموزشگاه فامو بابل به تفکیک سال شمسی و رشته.';
$canonicalPath = 'pages/admissions.php';
$webPageType = 'CollectionPage';
$breadcrumbs = [
    ['name' => 'خانه', 'url' => famo_canonical_url('')],
    ['name' => 'قبولی‌های سال‌های قبل', 'url' => famo_canonical_url('pages/admissions.php')],
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
    <!-- Hero Banner -->
    <section class="pt-28 sm:pt-32 pb-12 gradient-bg text-white text-center">
        <div class="container mx-auto max-w-6xl px-4">
            <h1 class="text-3xl sm:text-4xl font-bold mb-4">قبولی‌های سال‌های قبل آموزشگاه فامو</h1>
            <p class="text-white/85 max-w-2xl mx-auto leading-relaxed">
                افتخارات دانش‌آموزان فامو در کنکور سراسری، آزمون‌های تیزهوشان، نمونه دولتی و المپیادهای علمی به تفکیک سال
            </p>
        </div>
    </section>

    <!-- Filters & Grouped List -->
    <section class="py-12 sm:py-16 px-4">
        <div class="container mx-auto max-w-6xl">
            <div class="famo-filters-bar" aria-label="فیلترهای نمایش قبولی‌ها">
                <div class="famo-filter-row">
                    <span class="famo-filter-label">انتخاب سال:</span>
                    <div id="admissionsYearTabs" class="famo-filter-tabs" role="tablist" aria-label="فیلتر سال شمسی"></div>
                </div>
                <div class="famo-filter-row">
                    <span class="famo-filter-label">نوع آزمون:</span>
                    <div id="admissionsExamFilters" class="famo-filter-chips" aria-label="فیلتر نوع آزمون"></div>
                </div>
            </div>

            <div id="admissionsContainer" class="famo-adm-groups" aria-busy="true">
                <!-- Skeleton loaders until API responds -->
                <div class="famo-adm-grid" aria-hidden="true">
                    <div class="famo-adm-card famo-adm-skel">
                        <div class="famo-adm-card-head">
                            <div class="skeleton-shimmer famo-adm-skel-avatar"></div>
                            <div class="famo-adm-skel-lines">
                                <div class="skeleton-shimmer famo-adm-skel-line famo-adm-skel-line--name"></div>
                                <div class="skeleton-shimmer famo-adm-skel-line famo-adm-skel-line--result"></div>
                            </div>
                        </div>
                        <div class="famo-adm-meta">
                            <span class="skeleton-shimmer famo-adm-skel-chip"></span>
                            <span class="skeleton-shimmer famo-adm-skel-chip"></span>
                        </div>
                    </div>
                    <div class="famo-adm-card famo-adm-skel">
                        <div class="famo-adm-card-head">
                            <div class="skeleton-shimmer famo-adm-skel-avatar"></div>
                            <div class="famo-adm-skel-lines">
                                <div class="skeleton-shimmer famo-adm-skel-line famo-adm-skel-line--name"></div>
                                <div class="skeleton-shimmer famo-adm-skel-line famo-adm-skel-line--result"></div>
                            </div>
                        </div>
                        <div class="famo-adm-meta">
                            <span class="skeleton-shimmer famo-adm-skel-chip"></span>
                            <span class="skeleton-shimmer famo-adm-skel-chip"></span>
                        </div>
                    </div>
                    <div class="famo-adm-card famo-adm-skel">
                        <div class="famo-adm-card-head">
                            <div class="skeleton-shimmer famo-adm-skel-avatar"></div>
                            <div class="famo-adm-skel-lines">
                                <div class="skeleton-shimmer famo-adm-skel-line famo-adm-skel-line--name"></div>
                                <div class="skeleton-shimmer famo-adm-skel-line famo-adm-skel-line--result"></div>
                            </div>
                        </div>
                        <div class="famo-adm-meta">
                            <span class="skeleton-shimmer famo-adm-skel-chip"></span>
                            <span class="skeleton-shimmer famo-adm-skel-chip"></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cross-link to Testimonials -->
            <div class="famo-cross-cta">
                <div>
                    <h2 class="famo-cross-cta-title">تجربه شاگردان و اولیای فامو را بخوانید</h2>
                    <p class="famo-cross-cta-desc">دیدگاه دانش‌آموزان و خانواده‌ها درباره کیفیت کلاس‌ها، آزمون‌ها و مشاوره آموزشی فامو</p>
                </div>
                <a href="<?= $baseEsc ?>pages/testimonials.php" class="famo-state-btn">
                    مشاهده نظرات شاگردان
                </a>
            </div>
        </div>
    </section>
</main>

<!-- Footer -->
<?php include __DIR__ . '/../layouts/footer.php'; ?>

<script type="module" src="<?= htmlspecialchars(famo_local_asset('assets/js/admissions.js', $base), ENT_QUOTES, 'UTF-8') ?>"></script>
</body>
</html>
