<?php
declare(strict_types=1);
require_once __DIR__ . '/config.php';
$base = '';
$baseEsc = '';
$siteUrl = famo_public_site_url();
$loginUrlRaw = famo_login_url();
$loginUrlEsc = htmlspecialchars(($loginUrlRaw !== '' ? $loginUrlRaw . '/' : 'https://login.famoacademy.ir/'), ENT_QUOTES, 'UTF-8');

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
                    class="hero-cta bg-[#E2D9C6] text-[#445D84] font-bold px-8 py-3 rounded-full hover:bg-white transition duration-300 shadow-lg hover:shadow-xl">
                    <i class="icon ml-2" data-lucide="phone" aria-hidden="true"></i>
                    درخواست مشاوره
                </a>
                <a href="pages/course.php"
                    class="hero-cta border-2 border-[#E2D9C6] text-[#E2D9C6] font-bold px-8 py-3 rounded-full hover:bg-[#E2D9C6] hover:text-[#445D84] transition duration-300">
                    <i class="icon ml-2" data-lucide="book-open" aria-hidden="true"></i>
                    مشاهده برنامه‌های آموزشی
                </a>
            </div>
        </div>
    </section>

    <!-- ۲) دو مزیت رقابتی اصلی فامو -->
    <section id="advantages" class="py-16 sm:py-20 px-4 bg-white">
        <div class="container mx-auto max-w-6xl">
            <div class="text-center mb-12">
                <span class="famo-eyebrow famo-eyebrow--light">رویکرد آموزشی فامو</span>
                <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold text-[#445D84] mb-3">
                    فامو چه تفاوتی با یک آموزشگاه معمولی دارد؟
                </h2>
                <p class="text-base sm:text-lg text-gray-600 max-w-2xl mx-auto">
                    هیچ دو دانش‌آموزی دقیقاً شرایط یکسانی ندارند؛ بنابراین مسیر آموزشی و پیگیری عملکرد آن‌ها نیز نباید یکسان باشد.
                </p>
            </div>

            <div class="famo-two-col">
                <article class="famo-advantage-card hover-lift">
                    <span class="famo-advantage-icon" aria-hidden="true">
                        <i class="icon icon--lg" data-lucide="user-check" aria-hidden="true"></i>
                    </span>
                    <h3 class="famo-advantage-title">آموزش کاملاً شخصی‌سازی‌شده</h3>
                    <p class="famo-advantage-text">
                        در فامو، هر دانش‌آموز مسیر آموزشی متناسب با شرایط خودش را دنبال می‌کند. کلاس‌ها، آزمون‌ها و جلسات رفع اشکال به‌صورت انفرادی برگزار می‌شوند تا زمان آموزش بر نیازها و نقاط ضعف واقعی هر دانش‌آموز متمرکز باشد.
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
                    <h3 class="famo-advantage-title">آموزش هوشمند و مبتنی بر داده</h3>
                    <p class="famo-advantage-text">
                        اطلاعات آموزشی دانش‌آموزان در سامانه اختصاصی فامو ثبت می‌شود. تاریخچه آزمون‌ها، روند عملکرد دروس و کارنامه‌های هفتگی به خانواده‌ها کمک می‌کنند وضعیت تحصیلی را دقیق‌تر و شفاف‌تر دنبال کنند.
                    </p>
                    <a href="#smart-platform" class="famo-inline-link">
                        آشنایی با سامانه هوشمند
                        <i class="icon icon--sm" data-lucide="chevron-left" aria-hidden="true"></i>
                    </a>
                </article>
            </div>
        </div>
    </section>

    <!-- ۳) فرایند آموزش در فامو -->
    <section id="process" class="py-16 sm:py-20 px-4 bg-[#f9f7f3]">
        <div class="container mx-auto max-w-6xl">
            <div class="text-center mb-12">
                <span class="famo-eyebrow famo-eyebrow--light">شیوه فعالیت فامو</span>
                <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold text-[#445D84] mb-3">
                    از شناخت نیاز تا بررسی پیشرفت
                </h2>
                <p class="text-base sm:text-lg text-gray-600 max-w-2xl mx-auto">
                    مسیر یادگیری در چهار گام منظم و قابل پیگیری طراحی و اجرا می‌شود.
                </p>
            </div>

            <div class="famo-steps-grid">
                <article class="famo-step-card">
                    <span class="famo-step-num" aria-hidden="true">۱</span>
                    <h3 class="famo-step-title">بررسی وضعیت دانش‌آموز</h3>
                    <p class="famo-step-desc">
                        در آغاز مسیر، شرایط تحصیلی، سطح علمی و نیازهای آموزشی دانش‌آموز بررسی می‌شوند تا خدمات مناسب انتخاب شوند.
                    </p>
                </article>

                <article class="famo-step-card">
                    <span class="famo-step-num" aria-hidden="true">۲</span>
                    <h3 class="famo-step-title">طراحی مسیر آموزشی</h3>
                    <p class="famo-step-desc">
                        با توجه به شرایط دانش‌آموز، برنامه کلاس‌ها، آزمون‌ها و فعالیت‌های آموزشی متناسب با نیاز او تنظیم می‌شوند.
                    </p>
                </article>

                <article class="famo-step-card">
                    <span class="famo-step-num" aria-hidden="true">۳</span>
                    <h3 class="famo-step-title">آموزش و ارزیابی انفرادی</h3>
                    <p class="famo-step-desc">
                        کلاس‌ها، آزمون‌ها و جلسات رفع اشکال به‌صورت اختصاصی برگزار می‌شوند تا تمرکز آموزش بر مباحث مورد نیاز دانش‌آموز باشد.
                    </p>
                </article>

                <article class="famo-step-card">
                    <span class="famo-step-num" aria-hidden="true">۴</span>
                    <h3 class="famo-step-title">ثبت و بررسی عملکرد</h3>
                    <p class="famo-step-desc">
                        نتایج آزمون‌ها و روند دروس در سامانه ثبت می‌شوند و گزارش‌های هفتگی، همراه با نظرات اساتید، در اختیار دانش‌آموز و خانواده قرار می‌گیرند.
                    </p>
                </article>
            </div>
        </div>
    </section>

    <!-- ۴) معرفی سامانه هوشمند فامو -->
    <section id="smart-platform" class="py-16 sm:py-20 px-4 bg-white">
        <div class="container mx-auto max-w-6xl">
            <div class="famo-platform-grid">
                <div>
                    <span class="famo-eyebrow famo-eyebrow--light">سامانه هوشمند فامو</span>
                    <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold text-[#445D84] mb-4 leading-snug">
                        وضعیت تحصیلی، شفاف و قابل پیگیری
                    </h2>
                    <p class="text-gray-600 leading-relaxed mb-3">
                        در فامو، اطلاعات آموزشی دانش‌آموز فقط در برگه‌های کاغذی یا گزارش‌های مقطعی باقی نمی‌مانند. سامانه اختصاصی فامو امکان مشاهده سوابق آزمون‌ها، بررسی روند عملکرد دروس و دسترسی به کارنامه‌های هفتگی را فراهم می‌کند.
                    </p>
                    <p class="text-gray-600 leading-relaxed">
                        خانواده‌ها می‌توانند با اطلاعات دقیق‌تری وضعیت آموزشی فرزندشان را دنبال کنند و از گزارش‌های هفتگی او مطلع شوند.
                    </p>

                    <ul class="famo-feature-list">
                        <li>
                            <span class="famo-feature-check" aria-hidden="true"><i class="icon icon--sm" data-lucide="check" aria-hidden="true"></i></span>
                            تاریخچه نتایج آزمون‌ها
                        </li>
                        <li>
                            <span class="famo-feature-check" aria-hidden="true"><i class="icon icon--sm" data-lucide="check" aria-hidden="true"></i></span>
                            نمودار روند عملکرد دروس
                        </li>
                        <li>
                            <span class="famo-feature-check" aria-hidden="true"><i class="icon icon--sm" data-lucide="check" aria-hidden="true"></i></span>
                            کارنامه هفتگی اختصاصی
                        </li>
                        <li>
                            <span class="famo-feature-check" aria-hidden="true"><i class="icon icon--sm" data-lucide="check" aria-hidden="true"></i></span>
                            ثبت نظرات اساتید و پشتیبان
                        </li>
                        <li>
                            <span class="famo-feature-check" aria-hidden="true"><i class="icon icon--sm" data-lucide="check" aria-hidden="true"></i></span>
                            اطلاع‌رسانی پیامکی کارنامه
                        </li>
                        <li>
                            <span class="famo-feature-check" aria-hidden="true"><i class="icon icon--sm" data-lucide="check" aria-hidden="true"></i></span>
                            سیستم نوبت‌دهی جلسات
                        </li>
                    </ul>

                    <div class="flex flex-wrap gap-3">
                        <a href="<?= $loginUrlEsc ?>"
                            class="inline-flex items-center gap-2 bg-[#445D84] text-white font-bold px-6 py-3 rounded-full hover:bg-[#344868] transition duration-300 shadow-md">
                            <i class="icon icon--sm" data-lucide="user" aria-hidden="true"></i>
                            ورود به پنل دانش‌آموزی
                        </a>
                        <a href="pages/services.php#weekly-report"
                            class="inline-flex items-center gap-2 border-2 border-[#445D84] text-[#445D84] font-bold px-6 py-3 rounded-full hover:bg-[#445D84] hover:text-white transition duration-300">
                            جزئیات کارنامه و پایش
                        </a>
                    </div>
                </div>

                <!-- پیش‌نمایش شماتیک ساختار کارنامه هفتگی فامو (بدون اطلاعات واقعی دانش‌آموزان) -->
                <div>
                    <div class="famo-platform-mock" aria-label="نمای شماتیک کارنامه هفتگی سامانه فامو">
                        <div class="famo-mock-head">
                            <div>
                                <p class="text-sm font-bold text-[#445D84] m-0">کارنامه هفتگی سامانه فامو</p>
                                <p class="text-xs text-gray-500 m-0 mt-0.5">نمای ساختاری گزارش عملکرد دروس و نظر استاد</p>
                            </div>
                            <span class="famo-mock-badge">اطلاع‌رسانی پیامکی</span>
                        </div>

                        <div class="famo-mock-bars">
                            <div class="famo-mock-row">
                                <div class="famo-mock-row-label">
                                    <span>ریاضی — مبحث تابع و معادلات</span>
                                    <span class="text-[#445D84]">ثبت درصد و تعداد صحیح/غلط</span>
                                </div>
                                <div class="famo-mock-track"><div class="famo-mock-fill" style="width: 78%;"></div></div>
                            </div>
                            <div class="famo-mock-row">
                                <div class="famo-mock-row-label">
                                    <span>فیزیک — تمرین‌های مفهومی</span>
                                    <span class="text-[#445D84]">مقایسه با آزمون‌های قبل</span>
                                </div>
                                <div class="famo-mock-track"><div class="famo-mock-fill" style="width: 68%;"></div></div>
                            </div>
                            <div class="famo-mock-row">
                                <div class="famo-mock-row-label">
                                    <span>دروس پایه — ارزیابی هفتگی</span>
                                    <span class="text-[#445D84]">تشخیص نقاط نیازمند رفع اشکال</span>
                                </div>
                                <div class="famo-mock-track"><div class="famo-mock-fill" style="width: 84%;"></div></div>
                            </div>
                        </div>

                        <div class="famo-mock-note">
                            <strong class="text-[#445D84] block mb-1">بخش تحلیل دبیر و پشتیبان:</strong>
                            در کنار درصد هر درس، نظر اختصاصی استاد و اولویت مباحث هفته آینده برای دانش‌آموز و خانواده ثبت می‌شود.
                        </div>
                        <p class="famo-mock-caption">نمونه نمایشی ساختار سامانه؛ اطلاعات واقعی دانش‌آموزان صرفاً در پنل شخصی نمایش داده می‌شود.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ۵) برنامه‌های آموزشی و دوره‌های فعال -->
    <section id="courses" class="py-16 sm:py-20 px-4 bg-gradient-to-b from-[#f9f7f3] to-white">
        <div class="container mx-auto max-w-6xl">
            <div class="text-center mb-12">
                <span class="famo-eyebrow famo-eyebrow--light">مقاطع و اهداف تحصیلی</span>
                <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold text-[#445D84] mb-3">
                    برنامه‌های آموزشی فامو
                </h2>
                <p class="text-base sm:text-lg text-gray-600 max-w-2xl mx-auto">
                    فامو خدمات آموزشی خود را برای مقاطع و اهداف تحصیلی مختلف در بابل ارائه می‌کند. محتوای هر برنامه با توجه به نیازها و شرایط دانش‌آموز تنظیم می‌شود.
                </p>
            </div>

            <!-- ۵ کارت مقاطع مطابق سند نسخه ۱.۰ -->
            <div class="famo-programs-grid mb-14">
                <article class="famo-program-card">
                    <span class="famo-program-icon" aria-hidden="true"><i class="icon" data-lucide="book-open" aria-hidden="true"></i></span>
                    <h3 class="text-lg font-bold text-[#445D84] m-0">آموزش ابتدایی</h3>
                    <p class="text-sm text-gray-600 leading-relaxed m-0">
                        تقویت مفاهیم پایه، درک عمیق‌تر مباحث درسی و شناسایی زمینه‌های نیازمند تقویت در سال‌های ابتدایی تحصیل.
                    </p>
                    <a href="pages/course.php#elementary" class="famo-inline-link">
                        مشاهده جزئیات
                        <i class="icon icon--sm" data-lucide="chevron-left" aria-hidden="true"></i>
                    </a>
                </article>

                <article class="famo-program-card">
                    <span class="famo-program-icon" aria-hidden="true"><i class="icon" data-lucide="layers" aria-hidden="true"></i></span>
                    <h3 class="text-lg font-bold text-[#445D84] m-0">آموزش متوسطه اول</h3>
                    <p class="text-sm text-gray-600 leading-relaxed m-0">
                        تقویت پایه‌های درسی، افزایش مهارت حل مسئله و آماده‌سازی برای ورود به مقاطع بالاتر.
                    </p>
                    <a href="pages/course.php#middle-school" class="famo-inline-link">
                        مشاهده جزئیات
                        <i class="icon icon--sm" data-lucide="chevron-left" aria-hidden="true"></i>
                    </a>
                </article>

                <article class="famo-program-card">
                    <span class="famo-program-icon" aria-hidden="true"><i class="icon" data-lucide="graduation-cap" aria-hidden="true"></i></span>
                    <h3 class="text-lg font-bold text-[#445D84] m-0">آموزش متوسطه دوم</h3>
                    <p class="text-sm text-gray-600 leading-relaxed m-0">
                        آموزش دروس تخصصی و عمومی، رفع اشکال مباحث و همراهی دانش‌آموزان در مسیر تحصیلی.
                    </p>
                    <a href="pages/course.php#high-school" class="famo-inline-link">
                        مشاهده جزئیات
                        <i class="icon icon--sm" data-lucide="chevron-left" aria-hidden="true"></i>
                    </a>
                </article>

                <article class="famo-program-card">
                    <span class="famo-program-icon" aria-hidden="true"><i class="icon" data-lucide="target" aria-hidden="true"></i></span>
                    <h3 class="text-lg font-bold text-[#445D84] m-0">آمادگی کنکور</h3>
                    <p class="text-sm text-gray-600 leading-relaxed m-0">
                        آموزش خصوصی دروس، آزمون‌های شخصی‌سازی‌شده و بررسی روند عملکرد برای داوطلبان کنکور.
                    </p>
                    <a href="pages/course.php#konkur" class="famo-inline-link">
                        مشاهده جزئیات
                        <i class="icon icon--sm" data-lucide="chevron-left" aria-hidden="true"></i>
                    </a>
                </article>

                <article class="famo-program-card">
                    <span class="famo-program-icon" aria-hidden="true"><i class="icon" data-lucide="sparkles" aria-hidden="true"></i></span>
                    <h3 class="text-lg font-bold text-[#445D84] m-0">آمادگی تیزهوشان</h3>
                    <p class="text-sm text-gray-600 leading-relaxed m-0">
                        تقویت مهارت‌های تحلیل، استدلال و حل مسئله برای دانش‌آموزان متقاضی ورود به مدارس استعدادهای درخشان.
                    </p>
                    <a href="pages/course.php#tizhooshan" class="famo-inline-link">
                        مشاهده جزئیات
                        <i class="icon icon--sm" data-lucide="chevron-left" aria-hidden="true"></i>
                    </a>
                </article>

                <article class="famo-program-card bg-gradient-to-br from-[#445D84] to-[#55719b] text-white border-transparent">
                    <span class="w-11 h-11 rounded-xl bg-white/15 text-[#E2D9C6] inline-flex items-center justify-center" aria-hidden="true">
                        <i class="icon" data-lucide="compass" aria-hidden="true"></i>
                    </span>
                    <h3 class="text-lg font-bold text-white m-0">نیاز به راهنمایی در انتخاب برنامه دارید؟</h3>
                    <p class="text-sm text-white/85 leading-relaxed m-0">
                        انتخاب برنامه آموزشی بر اساس مقطع، سطح علمی و هدف دانش‌آموز انجام می‌شود.
                    </p>
                    <a href="pages/contact.php#consultation" class="inline-flex items-center gap-1.5 mt-auto pt-2 text-[#E2D9C6] hover:text-white font-bold text-sm">
                        درخواست مشاوره
                        <i class="icon icon--sm" data-lucide="chevron-left" aria-hidden="true"></i>
                    </a>
                </article>
            </div>

            <!-- دوره‌های ثبت‌شده در سامانه (بارگذاری زنده از API) -->
            <div class="text-center mb-8">
                <h3 class="text-xl sm:text-2xl font-bold text-[#445D84] mb-2">دوره‌های فعال آموزشگاه فامو</h3>
                <p class="text-sm sm:text-base text-gray-600">فهرست به‌روز دوره‌های در حال برگزاری در آموزشگاه فامو بابل</p>
            </div>

            <div class="swiper coursesSwiper coursesSwiper--loading relative pb-12">
                <div class="swiper-wrapper" id="coursesContainer" aria-busy="true">
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

    <!-- ۶) خدمات آموزشی فامو (با تصاویر بهینه‌شده AVIF + WebP) -->
    <section id="services" class="py-16 sm:py-20 px-4 gradient-bg-light">
        <div class="container mx-auto max-w-6xl">
            <div class="text-center mb-12">
                <span class="famo-eyebrow famo-eyebrow--light">شیوه اجرای آموزش انفرادی</span>
                <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold text-[#445D84] mb-3">
                    خدمات آموزشی آموزشگاه فامو
                </h2>
                <p class="text-base sm:text-lg text-gray-600 max-w-2xl mx-auto">
                    مجموعه‌ای از خدمات آموزشی و پیگیری تحصیلی متناسب با شرایط و نیاز هر دانش‌آموز در بابل
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 sm:gap-8">
                <!-- Service 1 -->
                <article class="service-card hover-lift">
                    <div class="service-media">
                        <picture>
                            <source srcset="<?= htmlspecialchars(famo_local_asset('assets/images/services/private_classes.avif', $base), ENT_QUOTES, 'UTF-8') ?>" type="image/avif">
                            <img src="<?= htmlspecialchars(famo_local_asset('assets/images/services/private_classes.webp', $base), ENT_QUOTES, 'UTF-8') ?>"
                                alt="کلاس‌های خصوصی و انفرادی آموزشگاه فامو"
                                width="800" height="800" loading="lazy" decoding="async"
                                class="service-illustration">
                        </picture>
                    </div>
                    <div class="service-body">
                        <h3 class="service-title">کلاس‌های خصوصی</h3>
                        <p class="service-desc">برگزاری کلاس‌های انفرادی با تمرکز کامل بر سطح یادگیری و مباحث مورد نیاز هر دانش‌آموز.</p>
                    </div>
                </article>

                <!-- Service 2 -->
                <article class="service-card hover-lift">
                    <div class="service-media">
                        <picture>
                            <source srcset="<?= htmlspecialchars(famo_local_asset('assets/images/services/weekly_exams.avif', $base), ENT_QUOTES, 'UTF-8') ?>" type="image/avif">
                            <img src="<?= htmlspecialchars(famo_local_asset('assets/images/services/weekly_exams.webp', $base), ENT_QUOTES, 'UTF-8') ?>"
                                alt="آزمون‌های شخصی‌سازی‌شده و تحلیل عملکرد فامو"
                                width="800" height="800" loading="lazy" decoding="async"
                                class="service-illustration">
                        </picture>
                    </div>
                    <div class="service-body">
                        <h3 class="service-title">آزمون‌های شخصی‌سازی‌شده</h3>
                        <p class="service-desc">ارزیابی تسلط درسی و ثبت نتایج آزمون‌ها در سامانه برای بررسی روند پیشرفت و کارنامه هفتگی.</p>
                    </div>
                </article>

                <!-- Service 3 -->
                <article class="service-card hover-lift">
                    <div class="service-media">
                        <picture>
                            <source srcset="<?= htmlspecialchars(famo_local_asset('assets/images/services/24support.avif', $base), ENT_QUOTES, 'UTF-8') ?>" type="image/avif">
                            <img src="<?= htmlspecialchars(famo_local_asset('assets/images/services/24support.webp', $base), ENT_QUOTES, 'UTF-8') ?>"
                                alt="رفع اشکال انفرادی و همراهی آموزشی فامو"
                                width="800" height="800" loading="lazy" decoding="async"
                                class="service-illustration">
                        </picture>
                    </div>
                    <div class="service-body">
                        <h3 class="service-title">رفع اشکال انفرادی</h3>
                        <p class="service-desc">تمرکز مستقیم روی مباحث دشوار و نقاط ابهام دانش‌آموز در جلسات اختصاصی رفع اشکال.</p>
                    </div>
                </article>

                <!-- Service 4 -->
                <article class="service-card hover-lift">
                    <div class="service-media">
                        <picture>
                            <source srcset="<?= htmlspecialchars(famo_local_asset('assets/images/services/weekly_plans.avif', $base), ENT_QUOTES, 'UTF-8') ?>" type="image/avif">
                            <img src="<?= htmlspecialchars(famo_local_asset('assets/images/services/weekly_plans.webp', $base), ENT_QUOTES, 'UTF-8') ?>"
                                alt="مشاوره و برنامه‌ریزی تحصیلی متناسب با شرایط فردی"
                                width="800" height="800" loading="lazy" decoding="async"
                                class="service-illustration">
                        </picture>
                    </div>
                    <div class="service-body">
                        <h3 class="service-title">مشاوره و برنامه‌ریزی</h3>
                        <p class="service-desc">تنظیم برنامه مطالعه و تمرین هماهنگ با سطح علمی، شرایط واقعی و هدف آموزشی دانش‌آموز.</p>
                    </div>
                </article>
            </div>

            <div class="text-center mt-10">
                <a href="pages/services.php"
                    class="inline-flex items-center gap-2 bg-[#445D84] text-white font-bold px-8 py-3 rounded-full hover:bg-[#344868] transition duration-300 shadow-lg">
                    آشنایی کامل با تمام خدمات فامو
                    <i class="icon icon--sm mr-1" data-lucide="chevron-left" aria-hidden="true"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- ۷) معرفی اساتید و پشتیبانان (کاروسل زنده API) -->
    <section id="team" class="py-16 sm:py-20 px-4 bg-[#f9f7f3]">
        <div class="container mx-auto max-w-6xl">
            <div class="text-center mb-12">
                <span class="famo-eyebrow famo-eyebrow--light">تیم آموزشی و پشتیبانی</span>
                <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold text-[#445D84] mb-3">
                    آشنایی با اساتید و پشتیبانان فامو
                </h2>
                <p class="text-base sm:text-lg text-gray-600 max-w-2xl mx-auto px-4">
                    در فامو، هر حوزه آموزشی با همراهی استاد تخصصی همان حوزه ارائه می‌شود و پشتیبانان در حوزه‌های فعالیت خود دانش‌آموزان را در مسیر پیشرفت همراهی می‌کنند.
                </p>
            </div>

            <div class="curve-carousel">
                <div class="curve-carousel__wrap">
                    <div class="curve-carousel__stage" tabindex="0"
                         aria-label="کاروسل اساتید و پشتیبانان فامو؛ با کشیدن یا کلیدهای جهت‌دار جابه‌جا کنید">
                        <div class="curve-carousel__edge-blur curve-carousel__edge-blur--left" aria-hidden="true"></div>
                        <div class="curve-carousel__edge-blur curve-carousel__edge-blur--right" aria-hidden="true"></div>
                        <div class="curve-carousel__track is-skeleton" id="instructorsContainer" aria-busy="true">
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
                        </div>
                    </div>

                    <div class="curve-carousel__controls">
                        <div class="curve-carousel__arrows">
                            <button type="button" class="curve-carousel__arrow" id="instCarouselPrev" aria-label="قبلی">
                                <i class="icon" data-lucide="chevron-right" aria-hidden="true"></i>
                            </button>
                            <div class="curve-carousel__dots" id="instCarouselDots"></div>
                            <button type="button" class="curve-carousel__arrow" id="instCarouselNext" aria-label="بعدی">
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

    <!-- ۸) نتایج واقعی و تجربه خانواده‌ها -->
    <section id="achievements-testimonials" class="py-16 sm:py-20 px-4 bg-white">
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

    <!-- ۹) پرسش‌های متداول منتخب (حداکثر ۳ سؤال طبق سند) -->
    <section id="faq-contact" class="py-16 sm:py-20 px-4 bg-[#f9f7f3]">
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
                        <span class="faq-icon text-[#8B786D] transform transition-transform duration-300 shrink-0">
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
                        <span class="faq-icon text-[#8B786D] transform transition-transform duration-300 shrink-0">
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
                        <span class="faq-icon text-[#8B786D] transform transition-transform duration-300 shrink-0">
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
                <span class="text-gray-300" aria-hidden="true">|</span>
                <a href="pages/blog.php" class="text-[#445D84] hover:underline inline-flex items-center gap-1">
                    مطالعه مقالات و راهنمای آموزشی
                    <i class="icon icon--sm" data-lucide="chevron-left" aria-hidden="true"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- ۱۰) دعوت پایانی -->
    <section id="cta-final" class="py-16 px-4 bg-white">
        <div class="container mx-auto max-w-5xl">
            <div class="gradient-bg rounded-3xl p-8 sm:p-12 text-center text-white shadow-2xl">
                <h2 class="text-2xl sm:text-3xl font-bold mb-4 leading-snug">
                    مسیر آموزشی مناسب فرزندتان را با یک گفت‌وگو شروع کنید
                </h2>
                <p class="text-white/90 mb-7 max-w-2xl mx-auto leading-relaxed">
                    برای بررسی نیازهای آموزشی دانش‌آموز و آشنایی با برنامه‌ها و خدمات آموزشگاه فامو در بابل، با ما در ارتباط باشید.
                </p>
                <div class="flex flex-wrap items-center justify-center gap-3 sm:gap-4">
                    <a href="pages/contact.php#consultation"
                        class="inline-flex items-center gap-2 bg-[#E2D9C6] text-[#445D84] font-bold px-8 py-3 rounded-full hover:bg-white transition duration-300 shadow-lg">
                        <i class="icon icon--sm" data-lucide="phone" aria-hidden="true"></i>
                        درخواست مشاوره
                    </a>
                    <a href="pages/contact.php"
                        class="inline-flex items-center gap-2 border-2 border-[#E2D9C6] text-[#E2D9C6] font-bold px-8 py-3 rounded-full hover:bg-[#E2D9C6] hover:text-[#445D84] transition duration-300">
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
