<?php
declare(strict_types=1);
require_once __DIR__ . '/../config.php';
$base = '../';
$baseEsc = htmlspecialchars($base, ENT_QUOTES, 'UTF-8');
$siteUrl = famo_public_site_url();
$servicesPageUrl = famo_canonical_url('pages/services.php');

$pageTitle = 'خدمات آموزشی فامو | کلاس خصوصی و رفع اشکال در بابل';
$metaDescription = 'خدمات آموزشی فامو شامل کلاس خصوصی، آزمون اختصاصی، رفع اشکال، مشاوره تحصیلی و بررسی عملکرد دانش‌آموزان در بابل.';
$canonicalPath = 'pages/services.php';
$webPageType = 'WebPage';
$ogImage = $siteUrl . '/assets/images/services/private_classes.webp';
$ogImageAlt = 'خدمات آموزشی و کلاس‌های خصوصی آموزشگاه فامو بابل';
$breadcrumbs = [
    ['name' => 'خانه', 'url' => famo_canonical_url('')],
    ['name' => 'خدمات آموزشی', 'url' => $servicesPageUrl],
];

$orgRef = [
    '@type' => 'EducationalOrganization',
    '@id'   => $siteUrl . '/#organization',
    'name'  => 'آموزشگاه فامو',
    'url'   => $siteUrl . '/',
];

$extraSchemas = [
    [
        '@type'           => 'ItemList',
        '@id'             => $servicesPageUrl . '#services',
        'name'            => 'خدمات آموزشی آموزشگاه فامو در بابل',
        'itemListElement' => [
            [
                '@type'    => 'ListItem',
                'position' => 1,
                'item'     => [
                    '@type'       => 'Service',
                    'name'        => 'کلاس‌های خصوصی',
                    'description' => 'برگزاری کلاس‌های آموزشی انفرادی با تمرکز کامل بر سطح علمی، نیازهای واقعی و هدف هر دانش‌آموز در بابل.',
                    'provider'    => $orgRef,
                    'areaServed'  => ['@type' => 'City', 'name' => 'بابل'],
                ],
            ],
            [
                '@type'    => 'ListItem',
                'position' => 2,
                'item'     => [
                    '@type'       => 'Service',
                    'name'        => 'آزمون‌های شخصی‌سازی‌شده',
                    'description' => 'طراحی آزمون متناسب با وضعیت هر دانش‌آموز برای سنجش میزان تسلط و ثبت نتایج در سامانه اختصاصی فامو.',
                    'provider'    => $orgRef,
                    'areaServed'  => ['@type' => 'City', 'name' => 'بابل'],
                ],
            ],
            [
                '@type'    => 'ListItem',
                'position' => 3,
                'item'     => [
                    '@type'       => 'Service',
                    'name'        => 'رفع اشکال انفرادی',
                    'description' => 'جلسات اختصاصی رفع اشکال برای بررسی دقیق مباحث دشوار و برطرف کردن نقاط ابهام دانش‌آموز.',
                    'provider'    => $orgRef,
                    'areaServed'  => ['@type' => 'City', 'name' => 'بابل'],
                ],
            ],
            [
                '@type'    => 'ListItem',
                'position' => 4,
                'item'     => [
                    '@type'       => 'Service',
                    'name'        => 'مشاوره و برنامه‌ریزی تحصیلی',
                    'description' => 'برنامه‌ریزی مطالعه و تمرین متناسب با سطح علمی، شرایط فردی و هدف آموزشی هر دانش‌آموز.',
                    'provider'    => $orgRef,
                    'areaServed'  => ['@type' => 'City', 'name' => 'بابل'],
                ],
            ],
            [
                '@type'    => 'ListItem',
                'position' => 5,
                'item'     => [
                    '@type'       => 'Service',
                    'name'        => 'پایش و تحلیل عملکرد',
                    'description' => 'ثبت سوابق آموزشی و بررسی روند تغییرات عملکرد دروس در طول زمان در سامانه اختصاصی فامو.',
                    'provider'    => $orgRef,
                    'areaServed'  => ['@type' => 'City', 'name' => 'بابل'],
                ],
            ],
            [
                '@type'    => 'ListItem',
                'position' => 6,
                'item'     => [
                    '@type'       => 'Service',
                    'name'        => 'کارنامه هفتگی با اطلاع‌رسانی پیامکی',
                    'description' => 'ارائه گزارش منظم هفتگی شامل نتایج آزمون‌ها، روند دروس و نظرات اساتید همراه با اطلاع‌رسانی پیامکی به خانواده.',
                    'provider'    => $orgRef,
                    'areaServed'  => ['@type' => 'City', 'name' => 'بابل'],
                ],
            ],
        ],
    ],
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
<body class="font-family text-gray-800 bg-[#f9f7f3]" data-static-nav="1">

<?php include __DIR__ . '/../layouts/header.php'; ?>

<main id="main-content">
    <!-- Hero -->
    <section class="famo-page-hero gradient-bg text-white text-center">
        <div class="container mx-auto max-w-4xl">
            <p class="famo-eyebrow">آموزشگاه فامو | بابل</p>
            <h1 class="text-3xl sm:text-4xl font-bold mb-5 text-white leading-snug">
                خدمات آموزشی و کلاس‌های خصوصی فامو در بابل
            </h1>
            <p class="text-white/90 text-base sm:text-lg leading-relaxed max-w-3xl mx-auto mb-4">
                فامو با ارائه مجموعه‌ای از خدمات آموزشی و پیگیری تحصیلی، تلاش می‌کند نیازهای هر دانش‌آموز را به‌شکل اختصاصی پوشش دهد.
            </p>
            <p class="text-white/80 text-sm sm:text-base leading-relaxed max-w-2xl mx-auto">
                از کلاس خصوصی و آزمون تا رفع اشکال، برنامه‌ریزی و بررسی عملکرد، خدمات آموزشی فامو با توجه به شرایط فردی دانش‌آموز ارائه می‌شوند.
            </p>
        </div>
    </section>

    <!-- Quick Jump Navigation -->
    <section class="famo-anchor-bar" aria-label="فهرست خدمات آموزشی">
        <div class="container mx-auto max-w-6xl">
            <div class="famo-anchor-pills">
                <a href="#private-classes" class="famo-anchor-pill">کلاس‌های خصوصی</a>
                <a href="#personalized-exams" class="famo-anchor-pill">آزمون‌های شخصی‌سازی‌شده</a>
                <a href="#tutoring" class="famo-anchor-pill">رفع اشکال انفرادی</a>
                <a href="#counseling" class="famo-anchor-pill">مشاوره و برنامه‌ریزی</a>
                <a href="#performance-tracking" class="famo-anchor-pill">پایش و تحلیل عملکرد</a>
                <a href="#weekly-report" class="famo-anchor-pill">کارنامه هفتگی</a>
            </div>
        </div>
    </section>

    <!-- Services Detail Blocks -->
    <section class="py-14 sm:py-16 px-4">
        <div class="container mx-auto max-w-6xl famo-detail-stack">
            <!-- Service 1: Private Classes -->
            <article id="private-classes" class="famo-detail-block famo-detail-block--media">
                <div>
                    <span class="famo-eyebrow famo-eyebrow--light">خدمت اول</span>
                    <h2 class="text-2xl sm:text-3xl font-bold text-[#445D84] mb-3">کلاس‌های خصوصی</h2>
                    <h3 class="text-lg font-bold text-[#736158] mb-4">کلاس خصوصی، با تمرکز کامل بر نیاز دانش‌آموز</h3>
                    <p class="text-gray-600 leading-relaxed mb-3">
                        در کلاس‌های خصوصی فامو، زمان آموزش به یک دانش‌آموز اختصاص دارد. این شیوه امکان تمرکز بیشتر بر مباحث مورد نیاز، بررسی دقیق‌تر اشکالات و هماهنگی روند تدریس با سطح یادگیری فرد را فراهم می‌کند.
                    </p>
                    <p class="text-gray-600 leading-relaxed">
                        محتوای کلاس با توجه به هدف آموزشی و وضعیت دانش‌آموز تنظیم می‌شود تا آموزش بر نیازهای واقعی او متمرکز باشد.
                    </p>
                </div>
                <div class="famo-detail-img-wrap">
                    <picture>
                        <source srcset="<?= htmlspecialchars(famo_local_asset('assets/images/services/private_classes.avif', $base), ENT_QUOTES, 'UTF-8') ?>" type="image/avif">
                        <img src="<?= htmlspecialchars(famo_local_asset('assets/images/services/private_classes.webp', $base), ENT_QUOTES, 'UTF-8') ?>"
                             alt="کلاس‌های خصوصی انفرادی آموزشگاه فامو در بابل"
                             width="800" height="800" loading="eager" decoding="async">
                    </picture>
                </div>
            </article>

            <!-- Service 2: Personalized Exams -->
            <article id="personalized-exams" class="famo-detail-block famo-detail-block--media">
                <div>
                    <span class="famo-eyebrow famo-eyebrow--light">خدمت دوم</span>
                    <h2 class="text-2xl sm:text-3xl font-bold text-[#445D84] mb-3">آزمون‌های شخصی‌سازی‌شده</h2>
                    <h3 class="text-lg font-bold text-[#736158] mb-4">آزمون برای شناخت بهتر وضعیت آموزشی</h3>
                    <p class="text-gray-600 leading-relaxed mb-3">
                        آزمون‌ها در فامو تنها برای ثبت یک نمره نیستند؛ آن‌ها ابزاری برای بررسی میزان تسلط دانش‌آموز و شناسایی مباحث نیازمند تقویت هستند.
                    </p>
                    <p class="text-gray-600 leading-relaxed">
                        محتوای آزمون‌ها با توجه به شرایط و نیازهای آموزشی دانش‌آموز تنظیم می‌شود و نتایج آن‌ها در سامانه ثبت می‌شوند تا امکان بررسی سوابق عملکرد فراهم باشد.
                    </p>
                </div>
                <div class="famo-detail-img-wrap">
                    <picture>
                        <source srcset="<?= htmlspecialchars(famo_local_asset('assets/images/services/weekly_exams.avif', $base), ENT_QUOTES, 'UTF-8') ?>" type="image/avif">
                        <img src="<?= htmlspecialchars(famo_local_asset('assets/images/services/weekly_exams.webp', $base), ENT_QUOTES, 'UTF-8') ?>"
                             alt="آزمون‌های شخصی‌سازی‌شده و تحلیل عملکرد در فامو"
                             width="800" height="800" loading="lazy" decoding="async">
                    </picture>
                </div>
            </article>

            <!-- Service 3: Individual Tutoring -->
            <article id="tutoring" class="famo-detail-block famo-detail-block--media">
                <div>
                    <span class="famo-eyebrow famo-eyebrow--light">خدمت سوم</span>
                    <h2 class="text-2xl sm:text-3xl font-bold text-[#445D84] mb-3">رفع اشکال انفرادی</h2>
                    <h3 class="text-lg font-bold text-[#736158] mb-4">تمرکز بر مباحثی که واقعاً به آموزش بیشتری نیاز دارند</h3>
                    <p class="text-gray-600 leading-relaxed mb-3">
                        گاهی مشکل دانش‌آموز به یک درس یا چند مبحث مشخص محدود است. در چنین شرایطی، تکرار کامل مطالب لزوماً بهترین راهکار نیست.
                    </p>
                    <p class="text-gray-600 leading-relaxed">
                        جلسات رفع اشکال فامو به‌صورت انفرادی برگزار می‌شوند تا مباحث دشوار، نقاط ابهام و اشکالات دانش‌آموز با تمرکز بیشتری بررسی شوند.
                    </p>
                </div>
                <div class="famo-detail-img-wrap">
                    <picture>
                        <source srcset="<?= htmlspecialchars(famo_local_asset('assets/images/services/24support.avif', $base), ENT_QUOTES, 'UTF-8') ?>" type="image/avif">
                        <img src="<?= htmlspecialchars(famo_local_asset('assets/images/services/24support.webp', $base), ENT_QUOTES, 'UTF-8') ?>"
                             alt="رفع اشکال انفرادی و همراهی آموزشی در فامو"
                             width="800" height="800" loading="lazy" decoding="async">
                    </picture>
                </div>
            </article>

            <!-- Service 4: Counseling & Study Planning -->
            <article id="counseling" class="famo-detail-block famo-detail-block--media">
                <div>
                    <span class="famo-eyebrow famo-eyebrow--light">خدمت چهارم</span>
                    <h2 class="text-2xl sm:text-3xl font-bold text-[#445D84] mb-3">مشاوره و برنامه‌ریزی تحصیلی</h2>
                    <h3 class="text-lg font-bold text-[#736158] mb-4">برنامه‌ریزی متناسب با شرایط واقعی دانش‌آموز</h3>
                    <p class="text-gray-600 leading-relaxed mb-3">
                        برنامه‌ریزی تحصیلی زمانی مؤثرتر است که با سطح علمی، اهداف و شرایط دانش‌آموز هماهنگ باشد.
                    </p>
                    <p class="text-gray-600 leading-relaxed">
                        در فامو، مشاوره و برنامه‌ریزی بر اساس وضعیت فردی دانش‌آموز انجام می‌شود تا مسیر مطالعه، تمرین و آموزش هدفمندتر دنبال شود.
                    </p>
                </div>
                <div class="famo-detail-img-wrap">
                    <picture>
                        <source srcset="<?= htmlspecialchars(famo_local_asset('assets/images/services/weekly_plans.avif', $base), ENT_QUOTES, 'UTF-8') ?>" type="image/avif">
                        <img src="<?= htmlspecialchars(famo_local_asset('assets/images/services/weekly_plans.webp', $base), ENT_QUOTES, 'UTF-8') ?>"
                             alt="مشاوره و برنامه‌ریزی تحصیلی شخصی‌سازی‌شده فامو"
                             width="800" height="800" loading="lazy" decoding="async">
                    </picture>
                </div>
            </article>

            <!-- Service 5 & 6: Performance Tracking & Weekly Report -->
            <div class="famo-two-col">
                <article id="performance-tracking" class="famo-detail-block">
                    <div class="flex items-center gap-3 mb-4">
                        <span class="famo-advantage-icon" aria-hidden="true">
                            <i class="icon icon--lg" data-lucide="chart-bar" aria-hidden="true"></i>
                        </span>
                        <div>
                            <span class="text-xs font-bold text-[#736158]">خدمت پنجم</span>
                            <h2 class="text-xl sm:text-2xl font-bold text-[#445D84]">پایش و تحلیل عملکرد</h2>
                        </div>
                    </div>
                    <h3 class="text-base sm:text-lg font-bold text-[#736158] mb-3">عملکرد تحصیلی باید در طول زمان بررسی شود</h3>
                    <p class="text-gray-600 leading-relaxed mb-3">
                        یک نتیجه آزمون به‌تنهایی تصویر کاملی از وضعیت دانش‌آموز ارائه نمی‌کند. بررسی تغییرات عملکرد در آزمون‌های مختلف و روند دروس، اطلاعات دقیق‌تری درباره وضعیت یادگیری فراهم می‌کند.
                    </p>
                    <p class="text-gray-600 leading-relaxed">
                        فامو با ثبت نتایج و سوابق آموزشی در سامانه اختصاصی، امکان بررسی روند عملکرد را فراهم می‌کند.
                    </p>
                </article>

                <article id="weekly-report" class="famo-detail-block">
                    <div class="flex items-center gap-3 mb-4">
                        <span class="famo-advantage-icon" aria-hidden="true">
                            <i class="icon icon--lg" data-lucide="file-text" aria-hidden="true"></i>
                        </span>
                        <div>
                            <span class="text-xs font-bold text-[#736158]">خدمت ششم</span>
                            <h2 class="text-xl sm:text-2xl font-bold text-[#445D84]">کارنامه هفتگی</h2>
                        </div>
                    </div>
                    <h3 class="text-base sm:text-lg font-bold text-[#736158] mb-3">گزارش منظم از وضعیت آموزشی دانش‌آموز</h3>
                    <p class="text-gray-600 leading-relaxed mb-3">
                        کارنامه هفتگی فامو، گزارشی از عملکرد تحصیلی دانش‌آموز را در اختیار خانواده قرار می‌دهد. این گزارش می‌تواند شامل نتایج آزمون‌ها، روند دروس و نظرات اساتید باشد تا خانواده‌ها تصویر روشن‌تری از وضعیت آموزشی فرزندشان داشته باشند.
                    </p>
                    <p class="text-gray-600 leading-relaxed">
                        اطلاع‌رسانی آماده شدن کارنامه نیز از طریق پیامک انجام می‌شود.
                    </p>
                </article>
            </div>

            <!-- Cross-link to Courses & Consultation CTA -->
            <div class="gradient-bg rounded-3xl p-8 sm:p-10 text-center text-white shadow-xl mt-4">
                <h2 class="text-2xl sm:text-3xl font-bold mb-3">خدمات آموزشی باید با نیاز دانش‌آموز هماهنگ باشند</h2>
                <p class="text-white/90 max-w-2xl mx-auto leading-relaxed mb-7">
                    برای بررسی وضعیت تحصیلی و انتخاب خدمات مناسب، با آموزشگاه فامو در بابل در ارتباط باشید یا برنامه‌های مقاطع مختلف را ببینید.
                </p>
                <div class="flex flex-wrap items-center justify-center gap-3 sm:gap-4">
                    <a href="<?= $baseEsc ?>pages/contact.php#consultation"
                        class="inline-flex items-center gap-2 bg-[#E2D9C6] text-[#445D84] font-bold px-8 py-3 rounded-full hover:bg-white transition duration-300 shadow-lg">
                        <i class="icon icon--sm" data-lucide="phone" aria-hidden="true"></i>
                        درخواست مشاوره
                    </a>
                    <a href="<?= $baseEsc ?>pages/course.php"
                        class="inline-flex items-center gap-2 border-2 border-[#E2D9C6] text-[#E2D9C6] font-bold px-7 py-3 rounded-full hover:bg-[#E2D9C6] hover:text-[#445D84] transition duration-300">
                        <i class="icon icon--sm" data-lucide="book-open" aria-hidden="true"></i>
                        مشاهده برنامه‌ها و دوره‌ها
                    </a>
                </div>
            </div>
        </div>
    </section>
</main>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
</body>
</html>
