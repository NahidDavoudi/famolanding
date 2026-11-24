<?php
declare(strict_types=1);
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/layouts/home-data.php';
require_once __DIR__ . '/layouts/home-render.php';
$base = '';
$baseEsc = '';
$siteUrl = famo_public_site_url();
$loginUrlRaw = famo_login_url();
$loginUrlEsc = htmlspecialchars(($loginUrlRaw !== '' ? $loginUrlRaw . '/' : 'https://login.famoacademy.ir/'), ENT_QUOTES, 'UTF-8');

$homeData = famo_home_data();
$ssrCoursesRaw = is_array($homeData['courses'])
    ? array_values(array_filter($homeData['courses'], 'is_array'))
    : [];
$ssrCourses = count($ssrCoursesRaw) > 0 ? array_slice($ssrCoursesRaw, 0, 4) : null;
$hasCoursesSsr = $ssrCourses !== null;

$ssrTeam = (is_array($homeData['instructors']) && is_array($homeData['supporters']))
    ? famo_home_build_team_list($homeData['instructors'], $homeData['supporters'])
    : [];
$hasTeamSsr = is_array($homeData['instructors']) && is_array($homeData['supporters']) && count($ssrTeam) > 0;
$ssrTeamTotal = count($ssrTeam);
$ssrTeamOffMode = $hasTeamSsr && $ssrTeamTotal < 3;

$pageTitle = 'آموزشگاه فامو بابل | آموزش خصوصی و هوشمند';
$metaDescription = 'آموزشگاه فامو در بابل؛ ارائه کلاس‌های خصوصی، آزمون‌های شخصی‌سازی‌شده و پیگیری هوشمند عملکرد تحصیلی دانش‌آموزان.';
$canonicalPath = '';
$ogImage = $siteUrl . '/assets/images/poster_hero.webp';
$ogImageAlt = 'محیط آموزشی آموزشگاه فامو بابل';
$breadcrumbs = [
    ['name' => 'خانه', 'url' => famo_canonical_url('')],
];
$extraSchemas = [
    [
        '@type'      => 'FAQPage',
        '@id'        => famo_canonical_url('') . '#faq',
        'inLanguage' => 'fa-IR',
        'mainEntity' => [
            [
                '@type'          => 'Question',
                'name'           => 'کلاس‌های آموزشگاه فامو در بابل به چه صورت برگزار می‌شوند؟',
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text'  => 'در آموزشگاه فامو در بابل، کلاس‌های آموزشی، آزمون‌ها و جلسات رفع اشکال به‌صورت انفرادی و متناسب با سطح علمی و نیازهای هر دانش‌آموز در محل آموزشگاه واقع در میدان باغ فردوس برگزار می‌شوند.',
                ],
            ],
            [
                '@type'          => 'Question',
                'name'           => 'خانواده‌ها چگونه روند تحصیلی دانش‌آموز را دنبال می‌کنند؟',
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text'  => 'نتایج آزمون‌ها و روند عملکرد دروس در سامانه اختصاصی فامو ثبت می‌شود. همچنین کارنامه هفتگی همراه با نظرات اساتید آماده شده و اطلاع‌رسانی آن از طریق پیامک برای خانواده انجام می‌گیرد.',
                ],
            ],
            [
                '@type'          => 'Question',
                'name'           => 'چگونه می‌توانم برای مشاوره و انتخاب برنامه آموزشی اقدام کنم؟',
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text'  => 'برای بررسی مقطع، شرایط تحصیلی و انتخاب خدمات متناسب با نیاز دانش‌آموز، می‌توانید از طریق صفحه تماس با ما به‌صورت تلفنی یا در واتساپ و تلگرام با واحد مشاوره فامو در ارتباط باشید.',
                ],
            ],
        ],
    ],
];
$page_libs = [
    'scroll_to' => false,
];
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include __DIR__ . '/layouts/head-common.php'; ?>
    <style>
        #courses .coursesSwiper .swiper-slide {
            height: auto;
        }
    </style>
</head>
<body class="font-family text-gray-800">
    <!-- Header -->
    <?php include __DIR__ . '/layouts/header.php'; ?>

    <main id="main-content">
    <!-- ۱) Hero Section -->
    <section id="home" class="hero-section gradient-bg text-white px-4">
        <div class="container mx-auto max-w-6xl hero-inner">
            <!-- تصویر اصلی هیرو: تمام‌عرض، بدون lazy load -->
            <div class="hero-media">
                <img src="assets/images/poster_hero.webp" alt="محیط آموزشی و کلاس‌های خصوصی آموزشگاه فامو در بابل" width="2816" height="1584" decoding="async" fetchpriority="high" class="hero-media-img">
            </div>

            <p class="famo-eyebrow mt-5">آموزشگاه فامو | بابل</p>

            <h1 class="hero-title" style="margin-top: 0;">
                هر دانش‌آموز، یک مسیر متفاوت برای موفقیت
                <span class="hero-title-accent">فامو؛ آموزش شخصی، با نگاهی هوشمند</span>
            </h1>

            <p class="hero-desc max-w-3xl">
                آموزشگاه فامو در بابل، با ارائه کلاس‌های خصوصی، آزمون‌های شخصی‌سازی‌شده و پیگیری هوشمند عملکرد تحصیلی، مسیر آموزشی متناسب با نیاز هر دانش‌آموز را طراحی می‌کند.
            </p>

            <div class="hero-ctas">
                <a href="pages/contact.php#consultation"
                    class="hero-cta famo-btn-cream font-bold px-8 py-3 rounded-full transition duration-300 shadow-lg hover:shadow-xl">
                    <i class="icon ml-2" data-lucide="phone" aria-hidden="true"></i>
                    درخواست مشاوره
                </a>
                <a href="pages/course.php"
                    class="hero-cta famo-btn-outline-cream font-bold px-8 py-3 rounded-full transition duration-300">
                    <i class="icon ml-2" data-lucide="book-open" aria-hidden="true"></i>
                    مشاهده برنامه‌های آموزشی
                </a>
            </div>
        </div>
    </section>

    <!-- ۲) معرفی اساتید و پشتیبانان (کاروسل زنده API بلافاصله بعد از هیرو) -->
    <section id="team" class="famo-home-section py-12 sm:py-20 px-4 bg-[#f9f7f3]">
        <div class="container mx-auto max-w-6xl">
            <div class="text-center mb-10 sm:mb-12">
                <span class="famo-eyebrow famo-eyebrow--light">تیم آموزشی و پشتیبانی</span>
                <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold text-[#445D84] mb-3">
                    آشنایی با اساتید و پشتیبانان فامو
                </h2>
                <p class="text-base sm:text-lg text-gray-600 max-w-2xl mx-auto px-4">
                    در فامو، هر حوزه آموزشی با همراهی استاد تخصصی همان حوزه ارائه می‌شود و پشتیبانان در حوزه‌های فعالیت خود دانش‌آموزان را در مسیر پیشرفت همراهی می‌کنند.
                </p>
            </div>

            <div class="curve-carousel<?= $ssrTeamOffMode ? ' curve-carousel--off-mode' : '' ?>">
                <div class="curve-carousel__wrap">
                    <div class="curve-carousel__stage" tabindex="0"
                         aria-label="کاروسل اساتید و پشتیبانان فامو؛ با کشیدن یا کلیدهای جهت‌دار جابه‌جا کنید">
                        <div class="curve-carousel__edge-blur curve-carousel__edge-blur--left" aria-hidden="true"></div>
                        <div class="curve-carousel__edge-blur curve-carousel__edge-blur--right" aria-hidden="true"></div>
                        <div class="curve-carousel__track<?= $hasTeamSsr ? '' : ' is-skeleton' ?>" id="instructorsContainer" aria-busy="<?= $hasTeamSsr ? 'false' : 'true' ?>"<?= $hasTeamSsr ? ' data-ssr="1" data-ssr-total="' . $ssrTeamTotal . '"' : '' ?>>
<?php if ($hasTeamSsr): ?>
<?= famo_render_team_cards($ssrTeam) ?>
<?php else: ?>
                            <!-- Skeleton Loaders -->
                            <div class="skeleton-instructor-wrapper">
                                <div class="famo-inst-skel" aria-hidden="true">
                                    <div class="skeleton-shimmer skel-inst-media"></div>
                                    <div class="skeleton-shimmer skel-bar skel-bar--inst-name"></div>
                                    <div class="skeleton-shimmer skel-bar skel-bar--inst-title"></div>
                                    <div class="skeleton-shimmer skel-bar skel-bar--inst-bio"></div>
                                    <div class="skeleton-shimmer skel-bar skel-bar--inst-bio-short"></div>
                                    <div class="skel-inst-socials"><span class="skeleton-shimmer"></span><span class="skeleton-shimmer"></span></div>
                                </div>
                            </div>
                            <div class="skeleton-instructor-wrapper">
                                <div class="famo-inst-skel" aria-hidden="true">
                                    <div class="skeleton-shimmer skel-inst-media"></div>
                                    <div class="skeleton-shimmer skel-bar skel-bar--inst-name"></div>
                                    <div class="skeleton-shimmer skel-bar skel-bar--inst-title"></div>
                                    <div class="skeleton-shimmer skel-bar skel-bar--inst-bio"></div>
                                    <div class="skeleton-shimmer skel-bar skel-bar--inst-bio-short"></div>
                                    <div class="skel-inst-socials"><span class="skeleton-shimmer"></span><span class="skeleton-shimmer"></span></div>
                                </div>
                            </div>
                            <div class="skeleton-instructor-wrapper">
                                <div class="famo-inst-skel" aria-hidden="true">
                                    <div class="skeleton-shimmer skel-inst-media"></div>
                                    <div class="skeleton-shimmer skel-bar skel-bar--inst-name"></div>
                                    <div class="skeleton-shimmer skel-bar skel-bar--inst-title"></div>
                                    <div class="skeleton-shimmer skel-bar skel-bar--inst-bio"></div>
                                    <div class="skeleton-shimmer skel-bar skel-bar--inst-bio-short"></div>
                                    <div class="skel-inst-socials"><span class="skeleton-shimmer"></span><span class="skeleton-shimmer"></span></div>
                                </div>
                            </div>
<?php endif; ?>
                        </div>
                    </div>

                    <div class="curve-carousel__controls">
                        <div class="curve-carousel__arrows">
                            <button type="button" class="curve-carousel__arrow" id="instCarouselPrev" aria-label="قبلی"<?= $ssrTeamOffMode ? ' hidden' : '' ?>>
                                <i class="icon" data-lucide="chevron-right" aria-hidden="true"></i>
                            </button>
                            <div class="curve-carousel__dots" id="instCarouselDots"<?= $ssrTeamOffMode ? ' hidden' : '' ?>><?= ($hasTeamSsr && !$ssrTeamOffMode) ? famo_render_team_dots($ssrTeam) : '' ?></div>
                            <button type="button" class="curve-carousel__arrow" id="instCarouselNext" aria-label="بعدی"<?= $ssrTeamOffMode ? ' hidden' : '' ?>>
                                <i class="icon" data-lucide="chevron-left" aria-hidden="true"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap justify-center gap-3 mt-10">
                <a href="pages/team.php"
                    class="inline-flex items-center gap-2 bg-[#445D84] text-white font-bold px-8 py-3 rounded-full hover:bg-[#344868] transition duration-300 shadow-lg hover:shadow-xl">
                    مشاهده تمام اساتید
                    <i class="icon icon--sm mr-1" data-lucide="chevron-left" aria-hidden="true"></i>
                </a>
                <a href="pages/team.php#support"
                    class="inline-flex items-center gap-2 border-2 border-[#445D84] text-[#445D84] font-bold px-7 py-3 rounded-full hover:bg-[#445D84] hover:text-white transition duration-300">
                    آشنایی با پشتیبانان
                </a>
            </div>
        </div>
    </section>

    <!-- ۳) خلاصه رویکرد و خدمات فامو (۳ کارت کوتاه با لینک به صفحه خدمات) -->
    <section id="about-summary" class="famo-home-section py-12 sm:py-20 px-4 bg-white" aria-label="خلاصه خدمات و رویکرد فامو">
        <div class="container mx-auto max-w-6xl">
            <div class="text-center mb-6">
                <span class="famo-eyebrow famo-eyebrow--light">فامو در یک نگاه</span>
            </div>

            <div class="famo-summary-grid">
                <article class="famo-advantage-card hover-lift">
                    <span class="famo-advantage-icon" aria-hidden="true">
                        <i class="icon icon--lg" data-lucide="user-check" aria-hidden="true"></i>
                    </span>
                    <h2 class="famo-advantage-title">آموزش شخصی‌سازی‌شده</h2>
                    <p class="famo-advantage-text">
                        کلاس، آزمون و رفع اشکال انفرادی، متناسب با نیاز هر دانش‌آموز.
                    </p>
                    <a href="pages/services.php" class="famo-inline-link">
                        آشنایی با خدمات آموزشی
                        <i class="icon icon--sm" data-lucide="chevron-left" aria-hidden="true"></i>
                    </a>
                </article>

                <article class="famo-advantage-card hover-lift">
                    <span class="famo-advantage-icon" aria-hidden="true">
                        <i class="icon icon--lg" data-lucide="chart-bar" aria-hidden="true"></i>
                    </span>
                    <h2 class="famo-advantage-title">سامانه و کارنامه هفتگی</h2>
                    <p class="famo-advantage-text">
                        نتایج آزمون‌ها و روند دروس، با اطلاع‌رسانی پیامکی به خانواده.
                    </p>
                    <a href="pages/services.php#smart-platform" class="famo-inline-link">
                        آشنایی با سامانه هوشمند
                        <i class="icon icon--sm" data-lucide="chevron-left" aria-hidden="true"></i>
                    </a>
                </article>

                <article class="famo-advantage-card hover-lift">
                    <span class="famo-advantage-icon" aria-hidden="true">
                        <i class="icon icon--lg" data-lucide="compass" aria-hidden="true"></i>
                    </span>
                    <h2 class="famo-advantage-title">مسیر آموزش در ۴ گام</h2>
                    <p class="famo-advantage-text">
                        از بررسی وضعیت دانش‌آموز تا ثبت و بررسی عملکرد.
                    </p>
                    <a href="pages/services.php#process" class="famo-inline-link">
                        مشاهده مراحل آموزش
                        <i class="icon icon--sm" data-lucide="chevron-left" aria-hidden="true"></i>
                    </a>
                </article>
            </div>
        </div>
    </section>

    <!-- ۴) برنامه‌های آموزشی و دوره‌های فعال (خلاصه با چیپ‌های مقاطع + حداکثر ۴ دوره) -->
    <section id="courses" class="famo-home-section py-12 sm:py-20 px-4 bg-gradient-to-b from-[#f9f7f3] to-white">
        <div class="container mx-auto max-w-6xl">
            <div class="text-center mb-7">
                <span class="famo-eyebrow famo-eyebrow--light">مقاطع و اهداف تحصیلی</span>
                <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold text-[#445D84] mb-3">
                    برنامه‌های آموزشی فامو
                </h2>
                <p class="text-base sm:text-lg text-gray-600 max-w-2xl mx-auto">
                    آموزش انفرادی و آزمون‌های شخصی‌سازی‌شده برای مقاطع و اهداف تحصیلی مختلف در بابل.
                </p>
            </div>

            <!-- ۵ چیپ دسترسی سریع به مقاطع در صفحه دوره‌ها -->
            <div class="famo-course-chips" aria-label="مقاطع و اهداف تحصیلی">
                <a href="pages/course.php#elementary" class="famo-course-chip">ابتدایی</a>
                <a href="pages/course.php#middle-school" class="famo-course-chip">متوسطه اول</a>
                <a href="pages/course.php#high-school" class="famo-course-chip">متوسطه دوم</a>
                <a href="pages/course.php#konkur" class="famo-course-chip">آمادگی کنکور</a>
                <a href="pages/course.php#tizhooshan" class="famo-course-chip">آمادگی تیزهوشان</a>
            </div>

            <div class="swiper coursesSwiper<?= $hasCoursesSsr ? '' : ' coursesSwiper--loading' ?> relative pb-12">
                <div class="swiper-wrapper" id="coursesContainer" aria-busy="<?= $hasCoursesSsr ? 'false' : 'true' ?>"<?= $hasCoursesSsr ? ' data-ssr="1"' : '' ?>>
<?php if ($hasCoursesSsr): ?>
<?php foreach ($ssrCourses as $courseIdx => $course): ?>
<?= famo_render_course_slide($course, $courseIdx) ?>
<?php endforeach; ?>
<?php else: ?>
                    <!-- Skeleton Loaders -->
                    <div class="swiper-slide skeleton-course-wrapper h-auto">
                        <div class="famo-course-card course-card-skeleton" aria-hidden="true">
                            <div class="famo-course-media skeleton-shimmer skel-media"></div>
                            <div class="famo-course-body">
                                <div class="skeleton-shimmer skel-bar skel-bar--title"></div>
                                <div class="skeleton-shimmer skel-bar skel-bar--title-short"></div>
                                <div class="skel-chips"><span class="skeleton-shimmer"></span><span class="skeleton-shimmer"></span></div>
                                <div class="skeleton-shimmer skel-bar skel-bar--desc"></div>
                                <div class="skeleton-shimmer skel-bar skel-bar--desc-short"></div>
                                <div class="famo-course-footer"><span class="skeleton-shimmer skel-cta"></span></div>
                            </div>
                        </div>
                    </div>
                    <div class="swiper-slide skeleton-course-wrapper h-auto">
                        <div class="famo-course-card course-card-skeleton" aria-hidden="true">
                            <div class="famo-course-media skeleton-shimmer skel-media"></div>
                            <div class="famo-course-body">
                                <div class="skeleton-shimmer skel-bar skel-bar--title"></div>
                                <div class="skeleton-shimmer skel-bar skel-bar--title-short"></div>
                                <div class="skel-chips"><span class="skeleton-shimmer"></span><span class="skeleton-shimmer"></span></div>
                                <div class="skeleton-shimmer skel-bar skel-bar--desc"></div>
                                <div class="skeleton-shimmer skel-bar skel-bar--desc-short"></div>
                                <div class="famo-course-footer"><span class="skeleton-shimmer skel-cta"></span></div>
                            </div>
                        </div>
                    </div>
                    <div class="swiper-slide skeleton-course-wrapper h-auto">
                        <div class="famo-course-card course-card-skeleton" aria-hidden="true">
                            <div class="famo-course-media skeleton-shimmer skel-media"></div>
                            <div class="famo-course-body">
                                <div class="skeleton-shimmer skel-bar skel-bar--title"></div>
                                <div class="skeleton-shimmer skel-bar skel-bar--title-short"></div>
                                <div class="skel-chips"><span class="skeleton-shimmer"></span><span class="skeleton-shimmer"></span></div>
                                <div class="skeleton-shimmer skel-bar skel-bar--desc"></div>
                                <div class="skeleton-shimmer skel-bar skel-bar--desc-short"></div>
                                <div class="famo-course-footer"><span class="skeleton-shimmer skel-cta"></span></div>
                            </div>
                        </div>
                    </div>
                    <div class="swiper-slide skeleton-course-wrapper h-auto">
                        <div class="famo-course-card course-card-skeleton" aria-hidden="true">
                            <div class="famo-course-media skeleton-shimmer skel-media"></div>
                            <div class="famo-course-body">
                                <div class="skeleton-shimmer skel-bar skel-bar--title"></div>
                                <div class="skeleton-shimmer skel-bar skel-bar--title-short"></div>
                                <div class="skel-chips"><span class="skeleton-shimmer"></span><span class="skeleton-shimmer"></span></div>
                                <div class="skeleton-shimmer skel-bar skel-bar--desc"></div>
                                <div class="skeleton-shimmer skel-bar skel-bar--desc-short"></div>
                                <div class="famo-course-footer"><span class="skeleton-shimmer skel-cta"></span></div>
                            </div>
                        </div>
                    </div>
<?php endif; ?>
                </div>

                <div class="swiper-pagination"></div>
            </div>

            <div class="text-center mt-8">
                <a href="pages/course.php"
                    class="inline-flex items-center gap-2 bg-[#445D84] text-white font-bold px-8 py-3 rounded-full hover:bg-[#344868] transition duration-300 shadow-lg hover:shadow-xl">
                    مشاهده همه برنامه‌ها و جزئیات دوره‌ها
                    <i class="icon icon--sm mr-1" data-lucide="chevron-left" aria-hidden="true"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- ۵) نتایج واقعی و تجربه خانواده‌ها -->
    <section id="achievements-testimonials" class="famo-home-section py-12 sm:py-20 px-4 bg-white">
        <div class="container mx-auto max-w-6xl">
            <div class="text-center mb-10">
                <span class="famo-eyebrow famo-eyebrow--light">نتایج و بازخوردهای واقعی</span>
                <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold text-[#445D84] mb-3">
                    نتایج قبولی‌ها و تجربه خانواده‌ها
                </h2>
                <p class="text-base sm:text-lg text-gray-600 max-w-2xl mx-auto px-4">
                    نتایج مستند سال‌های گذشته و بازخورد واقعی دانش‌آموزان و خانواده‌ها از همراهی با آموزشگاه فامو
                </p>
            </div>

            <div class="famo-highlights-grid">
                <article class="famo-highlight-card hover-lift">
                    <div>
                        <div class="famo-highlight-head">
                            <span class="famo-highlight-icon" aria-hidden="true">
                                <i class="icon" data-lucide="award" aria-hidden="true"></i>
                            </span>
                            <h3 class="famo-highlight-title">قبولی‌های سال‌های قبل</h3>
                        </div>
                        <p class="famo-highlight-desc">
                            فهرست نتایج و قبولی‌های مستند دانش‌آموزان فامو در کنکور سراسری، تیزهوشان، نمونه دولتی و المپیادهای علمی به تفکیک سال شمسی.
                        </p>
                        <div id="homeAdmissionsExcerpt" class="famo-excerpt-list" aria-live="polite"></div>
                    </div>
                    <div>
                        <a href="pages/admissions.php"
                            class="inline-flex items-center gap-2 bg-[#445D84] text-white font-bold px-6 py-2.5 rounded-full hover:bg-[#344868] transition duration-300 shadow-md">
                            مشاهده قبولی‌ها
                            <i class="icon icon--sm mr-1" data-lucide="chevron-left" aria-hidden="true"></i>
                        </a>
                    </div>
                </article>

                <article class="famo-highlight-card hover-lift">
                    <div>
                        <div class="famo-highlight-head">
                            <span class="famo-highlight-icon" aria-hidden="true">
                                <i class="icon" data-lucide="message-circle" aria-hidden="true"></i>
                            </span>
                            <h3 class="famo-highlight-title">تجربه خانواده‌ها و دانش‌آموزان</h3>
                        </div>
                        <p class="famo-highlight-desc">
                            بازخورد واقعی و تأییدشده دانش‌آموزان، فارغ‌التحصیلان و اولیا درباره تجربه همکاری با فامو را بخوانید یا دیدگاه خود را ثبت کنید.
                        </p>
                        <div id="homeTestimonialsExcerpt" class="famo-excerpt-list" aria-live="polite"></div>
                    </div>
                    <div>
                        <a href="pages/testimonials.php"
                            class="inline-flex items-center gap-2 bg-[#445D84] text-white font-bold px-6 py-2.5 rounded-full hover:bg-[#344868] transition duration-300 shadow-md">
                            مشاهده و ثبت نظر
                            <i class="icon icon--sm mr-1" data-lucide="chevron-left" aria-hidden="true"></i>
                        </a>
                    </div>
                </article>
            </div>
        </div>
    </section>

    <!-- ۶) پرسش‌های متداول منتخب (حداکثر ۳ سؤال طبق سند) -->
    <section id="faq-contact" class="famo-home-section py-12 sm:py-20 px-4 bg-[#f9f7f3]">
        <div class="container mx-auto max-w-4xl">
            <div class="text-center mb-10">
                <span class="famo-eyebrow famo-eyebrow--light">پرسش‌های پرتکرار</span>
                <h2 class="text-2xl sm:text-3xl font-bold text-[#445D84] mb-3">
                    پرسش‌های متداول خانواده‌ها
                </h2>
                <p class="text-gray-600 text-base">
                    پاسخ کوتاه به چند سؤال رایج درباره شیوه آموزش و پیگیری تحصیلی در فامو
                </p>
            </div>

            <div class="space-y-4">
                <!-- FAQ 1 -->
                <div class="faq-item bg-white rounded-xl shadow-md border border-[#E2D9C6] overflow-hidden">
                    <div class="faq-question cursor-pointer p-5 flex justify-between items-center gap-4" tabindex="0" role="button" aria-expanded="false">
                        <h3 class="text-base sm:text-lg font-bold text-[#445D84] m-0">کلاس‌های آموزشگاه فامو در بابل به چه صورت برگزار می‌شوند؟</h3>
                        <span class="faq-icon transform transition-transform duration-300 shrink-0">
                            <i class="icon icon--sm" data-lucide="chevron-down" aria-hidden="true"></i>
                        </span>
                    </div>
                    <div class="faq-answer px-5">
                        <p class="pb-5 text-gray-600 leading-relaxed m-0">
                            در آموزشگاه فامو در بابل، کلاس‌های آموزشی، آزمون‌ها و جلسات رفع اشکال به‌صورت انفرادی و متناسب با سطح علمی و نیازهای هر دانش‌آموز در محل آموزشگاه واقع در میدان باغ فردوس برگزار می‌شوند.
                        </p>
                    </div>
                </div>

                <!-- FAQ 2 -->
                <div class="faq-item bg-white rounded-xl shadow-md border border-[#E2D9C6] overflow-hidden">
                    <div class="faq-question cursor-pointer p-5 flex justify-between items-center gap-4" tabindex="0" role="button" aria-expanded="false">
                        <h3 class="text-base sm:text-lg font-bold text-[#445D84] m-0">خانواده‌ها چگونه روند تحصیلی دانش‌آموز را دنبال می‌کنند؟</h3>
                        <span class="faq-icon transform transition-transform duration-300 shrink-0">
                            <i class="icon icon--sm" data-lucide="chevron-down" aria-hidden="true"></i>
                        </span>
                    </div>
                    <div class="faq-answer px-5">
                        <p class="pb-5 text-gray-600 leading-relaxed m-0">
                            نتایج آزمون‌ها و روند عملکرد دروس در سامانه اختصاصی فامو ثبت می‌شود. همچنین کارنامه هفتگی همراه با نظرات اساتید آماده شده و اطلاع‌رسانی آن از طریق پیامک برای خانواده انجام می‌گیرد.
                        </p>
                    </div>
                </div>

                <!-- FAQ 3 -->
                <div class="faq-item bg-white rounded-xl shadow-md border border-[#E2D9C6] overflow-hidden">
                    <div class="faq-question cursor-pointer p-5 flex justify-between items-center gap-4" tabindex="0" role="button" aria-expanded="false">
                        <h3 class="text-base sm:text-lg font-bold text-[#445D84] m-0">چگونه می‌توانم برای مشاوره و انتخاب برنامه آموزشی اقدام کنم؟</h3>
                        <span class="faq-icon transform transition-transform duration-300 shrink-0">
                            <i class="icon icon--sm" data-lucide="chevron-down" aria-hidden="true"></i>
                        </span>
                    </div>
                    <div class="faq-answer px-5">
                        <p class="pb-5 text-gray-600 leading-relaxed m-0">
                            برای بررسی مقطع، شرایط تحصیلی و انتخاب خدمات متناسب با نیاز دانش‌آموز، می‌توانید از طریق صفحه تماس با ما به‌صورت تلفنی یا در واتساپ و تلگرام با واحد مشاوره فامو در ارتباط باشید.
                        </p>
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap items-center justify-center gap-4 mt-8 text-sm font-bold">
                <a href="pages/contact.php" class="text-[#445D84] hover:underline inline-flex items-center gap-1">
                    سؤال دیگری دارید؟ تماس با فامو
                    <i class="icon icon--sm" data-lucide="chevron-left" aria-hidden="true"></i>
                </a>
                <span class="text-gray-400" aria-hidden="true">|</span>
                <a href="pages/blog.php" class="text-[#445D84] hover:underline inline-flex items-center gap-1">
                    مطالعه مقالات و راهنمای آموزشی
                    <i class="icon icon--sm" data-lucide="chevron-left" aria-hidden="true"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- ۷) دعوت پایانی -->
    <section id="cta-final" class="famo-home-section py-12 sm:py-16 px-4 bg-white">
        <div class="container mx-auto max-w-5xl">
            <div class="gradient-bg rounded-3xl p-8 sm:p-12 text-center text-white shadow-2xl">
                <h2 class="text-2xl sm:text-3xl font-bold mb-4 leading-snug">
                    مسیر آموزشی مناسب فرزندتان را با یک گفت‌وگو شروع کنید
                </h2>
                <p class="famo-on-dark-soft mb-7 max-w-2xl mx-auto leading-relaxed">
                    برای بررسی نیازهای آموزشی دانش‌آموز و آشنایی با برنامه‌ها و خدمات آموزشگاه فامو در بابل، با ما در ارتباط باشید.
                </p>
                <div class="flex flex-wrap items-center justify-center gap-3 sm:gap-4">
                    <a href="pages/contact.php#consultation"
                        class="inline-flex items-center gap-2 famo-btn-cream font-bold px-8 py-3 rounded-full transition duration-300 shadow-lg">
                        <i class="icon icon--sm" data-lucide="phone" aria-hidden="true"></i>
                        درخواست مشاوره
                    </a>
                    <a href="pages/contact.php"
                        class="inline-flex items-center gap-2 famo-btn-outline-cream font-bold px-8 py-3 rounded-full transition duration-300">
                        <i class="icon icon--sm" data-lucide="map-pin" aria-hidden="true"></i>
                        تماس با فامو
                    </a>
                </div>
            </div>
        </div>
    </section>
    </main>

    <!-- Footer -->
    <?php include __DIR__ . '/layouts/footer.php'; ?>

    <!-- Scripts -->
    <script type="module" src="<?= htmlspecialchars(famo_local_asset('assets/js/main.js', $base), ENT_QUOTES, 'UTF-8') ?>"></script>
</body>
</html>
