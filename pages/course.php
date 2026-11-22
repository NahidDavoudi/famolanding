<?php
declare(strict_types=1);
require_once __DIR__ . '/../config.php';
$base = '../';
$baseEsc = htmlspecialchars($base, ENT_QUOTES, 'UTF-8');
$siteUrl = famo_public_site_url();
$coursePageUrl = famo_canonical_url('pages/course.php');
$orgRef = [
    '@type' => 'EducationalOrganization',
    '@id'   => $siteUrl . '/#organization',
    'name'  => 'آموزشگاه فامو',
    'url'   => $siteUrl . '/',
];

$pageTitle = 'دوره‌های آموزشی فامو در بابل | ابتدایی تا کنکور';
$metaDescription = 'آشنایی با برنامه‌های آموزشی فامو برای ابتدایی، متوسطه، کنکور و تیزهوشان؛ همراه با کلاس‌های خصوصی و مسیر آموزشی شخصی‌سازی‌شده.';
$canonicalPath = 'pages/course.php';
$webPageType = 'CollectionPage';
$ogImage = $siteUrl . '/assets/images/courses_banner.webp';
$ogImageAlt = 'دوره‌های آموزشی آموزشگاه فامو در بابل';
$breadcrumbs = [
    ['name' => 'خانه', 'url' => famo_canonical_url('')],
    ['name' => 'دوره‌های آموزشی', 'url' => $coursePageUrl],
];

// Server-side Course JSON-LD hydration (fail-soft with 5 core programs when API is unreachable)
$courseApiRes = famo_internal_api_get('/public/courses', 2);
$rawCourses = is_array($courseApiRes['json']['data'] ?? null) ? $courseApiRes['json']['data'] : [];
$courseListItems = [];

if (count($rawCourses) > 0) {
    $pos = 1;
    foreach ($rawCourses as $c) {
        if (!is_array($c) || empty($c['name'])) {
            continue;
        }
        $cId = isset($c['id']) ? (string) $c['id'] : (string) $pos;
        $cDesc = trim((string) ($c['full_description'] ?? $c['description'] ?? 'دوره آموزشی آموزشگاه فامو بابل'));
        $courseEntity = [
            '@type'             => 'Course',
            'name'              => (string) $c['name'],
            'description'       => $cDesc,
            'url'               => $coursePageUrl . '#course-' . rawurlencode($cId),
            'inLanguage'        => 'fa-IR',
            'provider'          => $orgRef,
            'hasCourseInstance' => [
                '@type'      => 'CourseInstance',
                'courseMode' => 'onsite',
                'location'   => [
                    '@type'   => 'Place',
                    'name'    => 'آموزشگاه فامو بابل',
                    'address' => [
                        '@type'           => 'PostalAddress',
                        'addressLocality' => 'بابل',
                        'addressRegion'   => 'مازندران',
                        'addressCountry'  => 'IR',
                    ],
                ],
            ],
        ];
        if (!empty($c['target_grades'])) {
            $courseEntity['educationalLevel'] = (string) $c['target_grades'];
        }
        $courseListItems[] = [
            '@type'    => 'ListItem',
            'position' => $pos++,
            'item'     => $courseEntity,
        ];
    }
} else {
    $fallbackCourses = [
        [
            'name'        => 'آموزش و تقویت دروس ابتدایی در بابل',
            'description' => 'کلاس‌های خصوصی ابتدایی فامو بر اساس سطح یادگیری دانش‌آموز برای تقویت مفاهیم پایه، رفع اشکال و تثبیت آموخته‌ها.',
            'level'       => 'دوره ابتدایی',
            'anchor'      => '#elementary',
        ],
        [
            'name'        => 'کلاس خصوصی متوسطه اول در بابل',
            'description' => 'تقویت پایه‌های درسی، آموزش مفهومی، رفع اشکال تخصصی و افزایش مهارت حل مسئله برای دانش‌آموزان متوسطه اول.',
            'level'       => 'دوره اول متوسطه',
            'anchor'      => '#middle-school',
        ],
        [
            'name'        => 'کلاس خصوصی متوسطه دوم در بابل',
            'description' => 'آموزش دروس تخصصی و عمومی، رفع اشکال موضوعی و آمادگی امتحانات مدرسه و مسیر کنکور متناسب با شرایط هر دانش‌آموز.',
            'level'       => 'دوره دوم متوسطه',
            'anchor'      => '#high-school',
        ],
        [
            'name'        => 'کلاس خصوصی و آمادگی کنکور در بابل',
            'description' => 'کلاس خصوصی دروس کنکور، آزمون‌های شخصی‌سازی‌شده، تحلیل نتایج و گزارش هفتگی وضعیت آموزشی داوطلبان.',
            'level'       => 'آمادگی کنکور',
            'anchor'      => '#konkur',
        ],
        [
            'name'        => 'آمادگی آزمون تیزهوشان در بابل',
            'description' => 'تقویت مهارت استدلال، تحلیل، حل مسائل هوش و تمرین متناسب با سطح دانش‌آموز برای آمادگی آزمون مدارس استعدادهای درخشان.',
            'level'       => 'آمادگی تیزهوشان',
            'anchor'      => '#tizhooshan',
        ],
    ];
    foreach ($fallbackCourses as $idx => $fc) {
        $courseListItems[] = [
            '@type'    => 'ListItem',
            'position' => $idx + 1,
            'item'     => [
                '@type'            => 'Course',
                'name'             => $fc['name'],
                'description'      => $fc['description'],
                'url'              => $coursePageUrl . $fc['anchor'],
                'inLanguage'       => 'fa-IR',
                'educationalLevel' => $fc['level'],
                'provider'         => $orgRef,
            ],
        ];
    }
}

$courseSchemaJson = json_encode(
    [
        '@context'        => 'https://schema.org',
        '@type'           => 'ItemList',
        '@id'             => $coursePageUrl . '#courses',
        'name'            => 'دوره‌های آموزشی فامو بابل',
        'itemListElement' => $courseListItems,
    ],
    JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
);
if ($courseSchemaJson === false) {
    $courseSchemaJson = '{}';
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
    <!-- Course ItemList Schema Markup (SSR hydrated + kept in sync by courses.js) -->
    <script type="application/ld+json" id="courseSchema"><?= $courseSchemaJson ?></script>

    <!-- استایل کارت‌های دوره: تصویر در بالا، نام و مشخصات زیر آن، چیدمان گرید -->
    <style>
        #courses .courses-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 20px;
            align-items: stretch;
        }

        @media (min-width: 640px) {
            #courses .courses-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 24px;
            }
        }

        @media (min-width: 1024px) {
            #courses .courses-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr));
                gap: 28px;
            }
        }

        #courses .course-card {
            display: flex;
            flex-direction: column;
            height: 100%;
            min-width: 0;
            background: #ffffff;
            border: 1px solid #E2D9C6;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 25px -12px rgba(68, 93, 132, 0.35);
            transition: transform .3s ease, box-shadow .3s ease;
            scroll-margin-top: 100px;
        }

        #courses .course-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 35px -16px rgba(68, 93, 132, 0.5);
        }

        #courses .course-image-wrapper {
            position: relative;
            width: 100%;
            aspect-ratio: 16 / 10;
            flex: 0 0 auto;
            overflow: hidden;
        }

        #courses .course-image-wrapper img,
        #courses .course-image {
            display: block;
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
            transition: transform .5s ease;
        }

        #courses .course-card:hover .course-image {
            transform: scale(1.05);
        }

        #courses .course-image-fallback {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        #courses .course-badge {
            position: absolute;
            top: 12px;
            inset-inline-start: 12px;
            z-index: 2;
            background: rgba(255, 255, 255, 0.95);
            color: #445D84;
            font-size: 11px;
            font-weight: 700;
            line-height: 1.9;
            padding: 2px 12px;
            border-radius: 9999px;
            box-shadow: 0 4px 10px rgba(68, 93, 132, 0.15);
        }

        #courses .course-body {
            display: flex;
            flex-direction: column;
            flex: 1 1 auto;
            min-width: 0;
            padding: 16px 18px 18px;
        }

        #courses .course-name {
            margin: 0;
            font-size: 16px;
            font-weight: 700;
            color: #445D84;
            line-height: 1.8;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        #courses .course-meta {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 6px 10px;
            margin-top: 8px;
            font-size: 12px;
            color: #736158;
        }

        #courses .course-meta span {
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        #courses .course-desc {
            margin: 10px 0 0;
            font-size: 13px;
            color: #6b7280;
            line-height: 1.9;
            display: -webkit-box;
            -webkit-line-clamp: 4;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        #courses .course-features {
            margin: 12px 0 0;
            padding: 0;
            list-style: none;
            display: grid;
            grid-template-columns: 1fr;
            gap: 6px;
            font-size: 12.5px;
            color: #4b5563;
        }

        #courses .course-features li {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        #courses .course-footer {
            margin-top: auto;
            padding-top: 14px;
            border-top: 1px solid #E2D9C6;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            flex-wrap: wrap;
        }

        #courses .course-cta {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            background: #445D84;
            color: #fff;
            font-size: 13px;
            font-weight: 700;
            padding: 8px 14px;
            border-radius: 10px;
            text-decoration: none;
            white-space: nowrap;
            transition: background-color .18s ease, transform .18s ease;
        }

        #courses .course-cta:hover {
            background: #344868;
            transform: translateY(-1px);
        }

        #courses .course-cta:focus-visible {
            outline: 2px solid #445D84;
            outline-offset: 2px;
        }

        #courses .course-card-skeleton {
            background: var(--skeleton-bg, #f5f5f5);
            border: 1px solid var(--skeleton-border, #e0e0e0);
            border-radius: 16px;
            box-shadow: 0 10px 25px -12px rgba(0, 0, 0, 0.08);
            padding: 16px 18px 18px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            overflow: hidden;
        }

        #courses .course-card-skeleton .skeleton-img {
            aspect-ratio: 16 / 10;
            margin: -16px -18px 6px;
            border-radius: 0;
        }

        #courses .course-card-skeleton .skeleton-line-lg { height: 15px; width: 75%; }
        #courses .course-card-skeleton .skeleton-line { height: 11px; width: 100%; }
        #courses .course-card-skeleton .skeleton-line-sm { height: 11px; width: 60%; }
        #courses .course-card-skeleton .skeleton-price { height: 16px; width: 45%; margin-top: 6px; align-self: flex-end; }
    </style>
</head>

<body class="font-family text-gray-800 bg-[#f9f7f3]">

<!-- Header -->
<?php include __DIR__ . '/../layouts/header.php'; ?>

<main id="main-content">
<!-- Page Title Banner -->
<section class="famo-page-hero gradient-bg text-white text-center">
    <div class="container mx-auto px-4 max-w-5xl">
        <p class="famo-eyebrow">آموزشگاه فامو | بابل</p>
        <h1 class="text-3xl sm:text-4xl font-bold mb-6 text-white">دوره‌های آموزشی فامو در بابل</h1>

        <div class="relative w-full h-56 sm:h-72 md:h-96 rounded-2xl overflow-hidden shadow-2xl border border-white/20 mb-8">
            <img src="<?= $baseEsc ?>assets/images/courses_banner.webp" alt="دوره‌های آموزشی و کلاس‌های خصوصی آموزشگاه فامو در بابل"
                width="2752" height="1536" decoding="async" fetchpriority="high" class="w-full h-full object-cover">
        </div>

        <p class="text-white/90 max-w-3xl mx-auto text-base sm:text-lg leading-relaxed mb-3">
            فامو برنامه‌های آموزشی خود را برای دانش‌آموزان مقاطع مختلف ارائه می‌کند. در این مجموعه، کلاس‌های خصوصی بر اساس سطح علمی، نیازهای آموزشی و هدف هر دانش‌آموز برگزار می‌شوند.
        </p>
        <p class="text-white/80 max-w-2xl mx-auto text-sm sm:text-base leading-relaxed">
            از تقویت مفاهیم درسی در دوره ابتدایی تا آمادگی کنکور و آزمون تیزهوشان، مسیر آموزش متناسب با شرایط دانش‌آموز طراحی می‌شود.
        </p>
    </div>
</section>

<!-- Quick Section Navigation -->
<section class="famo-anchor-bar" aria-label="فهرست مقاطع و برنامه‌های آموزشی">
    <div class="container mx-auto max-w-6xl">
        <div class="famo-anchor-pills">
            <a href="#elementary" class="famo-anchor-pill">آموزش ابتدایی</a>
            <a href="#middle-school" class="famo-anchor-pill">متوسطه اول</a>
            <a href="#high-school" class="famo-anchor-pill">متوسطه دوم</a>
            <a href="#konkur" class="famo-anchor-pill">آمادگی کنکور</a>
            <a href="#tizhooshan" class="famo-anchor-pill">آمادگی تیزهوشان</a>
            <a href="#courses" class="famo-anchor-pill">دوره‌های فعال فامو</a>
        </div>
    </div>
</section>

<!-- 5 Core Grade & Goal Programs (Strategy Doc v1.0 Section 3.2) -->
<section class="py-14 px-4">
    <div class="container mx-auto max-w-6xl famo-detail-stack">
        <!-- ۱. آموزش ابتدایی -->
        <article id="elementary" class="famo-detail-block">
            <span class="famo-eyebrow famo-eyebrow--light">مقطع ابتدایی</span>
            <h2 class="text-2xl sm:text-3xl font-bold text-[#445D84] mb-3">آموزش و تقویت دروس ابتدایی در بابل</h2>
            <p class="text-gray-600 leading-relaxed mb-3">
                سال‌های ابتدایی تحصیل نقش مهمی در شکل‌گیری پایه علمی دانش‌آموز دارند. یادگیری درست مفاهیم در این دوره می‌تواند زمینه مناسبی برای ادامه تحصیل فراهم کند.
            </p>
            <p class="text-gray-600 leading-relaxed">
                در کلاس‌های خصوصی ابتدایی فامو، آموزش بر اساس سطح یادگیری دانش‌آموز پیش می‌رود و مباحثی که نیاز به توضیح یا تمرین بیشتری دارند، با تمرکز بررسی می‌شوند.
            </p>
            <h3 class="text-base font-bold text-[#445D84] mt-5 mb-2">محورهای آموزشی</h3>
            <ul class="famo-bullet-grid">
                <li><span class="famo-feature-check" aria-hidden="true"><i class="icon icon--sm" data-lucide="check" aria-hidden="true"></i></span>تقویت مفاهیم پایه درسی</li>
                <li><span class="famo-feature-check" aria-hidden="true"><i class="icon icon--sm" data-lucide="check" aria-hidden="true"></i></span>رفع اشکال مباحث</li>
                <li><span class="famo-feature-check" aria-hidden="true"><i class="icon icon--sm" data-lucide="check" aria-hidden="true"></i></span>افزایش تسلط بر مطالب</li>
                <li><span class="famo-feature-check" aria-hidden="true"><i class="icon icon--sm" data-lucide="check" aria-hidden="true"></i></span>تمرین و تثبیت آموخته‌ها</li>
            </ul>
            <a href="<?= $baseEsc ?>pages/contact.php#consultation" class="inline-flex items-center gap-2 bg-[#445D84] text-white font-bold px-6 py-2.5 rounded-full hover:bg-[#344868] transition text-sm">
                درخواست مشاوره آموزش ابتدایی
                <i class="icon icon--sm" data-lucide="chevron-left" aria-hidden="true"></i>
            </a>
        </article>

        <!-- ۲. آموزش متوسطه اول -->
        <article id="middle-school" class="famo-detail-block">
            <span class="famo-eyebrow famo-eyebrow--light">متوسطه اول</span>
            <h2 class="text-2xl sm:text-3xl font-bold text-[#445D84] mb-3">کلاس خصوصی متوسطه اول در بابل</h2>
            <p class="text-gray-600 leading-relaxed mb-3">
                دوره متوسطه اول مرحله‌ای مهم در تثبیت پایه‌های علمی و تقویت مهارت‌های یادگیری است.
            </p>
            <p class="text-gray-600 leading-relaxed">
                در این مقطع، کلاس‌های خصوصی فامو با توجه به وضعیت دانش‌آموز برگزار می‌شوند تا مباحث درسی به‌صورت دقیق‌تر آموزش داده شوند و نقاط ضعف پیش از ورود به متوسطه دوم شناسایی شوند.
            </p>
            <h3 class="text-base font-bold text-[#445D84] mt-5 mb-2">محورهای آموزشی</h3>
            <ul class="famo-bullet-grid">
                <li><span class="famo-feature-check" aria-hidden="true"><i class="icon icon--sm" data-lucide="check" aria-hidden="true"></i></span>تقویت پایه‌های درسی</li>
                <li><span class="famo-feature-check" aria-hidden="true"><i class="icon icon--sm" data-lucide="check" aria-hidden="true"></i></span>آموزش مفهومی</li>
                <li><span class="famo-feature-check" aria-hidden="true"><i class="icon icon--sm" data-lucide="check" aria-hidden="true"></i></span>رفع اشکال تخصصی</li>
                <li><span class="famo-feature-check" aria-hidden="true"><i class="icon icon--sm" data-lucide="check" aria-hidden="true"></i></span>تقویت مهارت حل مسئله</li>
                <li><span class="famo-feature-check" aria-hidden="true"><i class="icon icon--sm" data-lucide="check" aria-hidden="true"></i></span>آمادگی برای آزمون‌های ورودی مدارس</li>
            </ul>
            <a href="<?= $baseEsc ?>pages/contact.php#consultation" class="inline-flex items-center gap-2 bg-[#445D84] text-white font-bold px-6 py-2.5 rounded-full hover:bg-[#344868] transition text-sm">
                درخواست مشاوره متوسطه اول
                <i class="icon icon--sm" data-lucide="chevron-left" aria-hidden="true"></i>
            </a>
        </article>

        <!-- ۳. آموزش متوسطه دوم -->
        <article id="high-school" class="famo-detail-block">
            <span class="famo-eyebrow famo-eyebrow--light">متوسطه دوم</span>
            <h2 class="text-2xl sm:text-3xl font-bold text-[#445D84] mb-3">کلاس خصوصی متوسطه دوم در بابل</h2>
            <p class="text-gray-600 leading-relaxed mb-3">
                با ورود به متوسطه دوم، نیازهای آموزشی دانش‌آموزان تخصصی‌تر می‌شوند. رشته تحصیلی، سطح علمی و هدف هر دانش‌آموز، محتوای آموزشی و نحوه تدریس را تحت تأثیر قرار می‌دهد.
            </p>
            <p class="text-gray-600 leading-relaxed">
                در فامو، کلاس‌های خصوصی این مقطع متناسب با شرایط دانش‌آموز برگزار می‌شوند تا آموزش بر مباحث مورد نیاز و اهداف تحصیلی او متمرکز باشد.
            </p>
            <h3 class="text-base font-bold text-[#445D84] mt-5 mb-2">محورهای آموزشی</h3>
            <ul class="famo-bullet-grid">
                <li><span class="famo-feature-check" aria-hidden="true"><i class="icon icon--sm" data-lucide="check" aria-hidden="true"></i></span>آموزش دروس تخصصی و عمومی</li>
                <li><span class="famo-feature-check" aria-hidden="true"><i class="icon icon--sm" data-lucide="check" aria-hidden="true"></i></span>تقویت مباحث پایه</li>
                <li><span class="famo-feature-check" aria-hidden="true"><i class="icon icon--sm" data-lucide="check" aria-hidden="true"></i></span>رفع اشکال موضوعی</li>
                <li><span class="famo-feature-check" aria-hidden="true"><i class="icon icon--sm" data-lucide="check" aria-hidden="true"></i></span>آمادگی امتحانات مدرسه</li>
                <li><span class="famo-feature-check" aria-hidden="true"><i class="icon icon--sm" data-lucide="check" aria-hidden="true"></i></span>آماده‌سازی برای مسیر کنکور</li>
            </ul>
            <a href="<?= $baseEsc ?>pages/contact.php#consultation" class="inline-flex items-center gap-2 bg-[#445D84] text-white font-bold px-6 py-2.5 rounded-full hover:bg-[#344868] transition text-sm">
                درخواست مشاوره متوسطه دوم
                <i class="icon icon--sm" data-lucide="chevron-left" aria-hidden="true"></i>
            </a>
        </article>

        <!-- ۴. آمادگی کنکور -->
        <article id="konkur" class="famo-detail-block">
            <span class="famo-eyebrow famo-eyebrow--light">کنکور سراسری</span>
            <h2 class="text-2xl sm:text-3xl font-bold text-[#445D84] mb-3">کلاس خصوصی و آمادگی کنکور در بابل</h2>
            <p class="text-gray-600 leading-relaxed mb-3">
                هر داوطلب کنکور با شرایط علمی، نقاط ضعف و هدف متفاوتی وارد این مسیر می‌شود. به همین دلیل، برنامه آموزشی یکسان نمی‌تواند پاسخ‌گوی نیاز تمام داوطلبان باشد.
            </p>
            <p class="text-gray-600 leading-relaxed">
                فامو با تمرکز بر کلاس‌های خصوصی، آزمون‌های شخصی‌سازی‌شده و بررسی نتایج، مسیر آمادگی هر دانش‌آموز را متناسب با وضعیت او پیش می‌برد.
            </p>
            <h3 class="text-base font-bold text-[#445D84] mt-5 mb-2">محورهای آموزشی</h3>
            <ul class="famo-bullet-grid">
                <li><span class="famo-feature-check" aria-hidden="true"><i class="icon icon--sm" data-lucide="check" aria-hidden="true"></i></span>کلاس خصوصی دروس کنکور</li>
                <li><span class="famo-feature-check" aria-hidden="true"><i class="icon icon--sm" data-lucide="check" aria-hidden="true"></i></span>آزمون‌های متناسب با شرایط داوطلب</li>
                <li><span class="famo-feature-check" aria-hidden="true"><i class="icon icon--sm" data-lucide="check" aria-hidden="true"></i></span>تحلیل نتایج آزمون</li>
                <li><span class="famo-feature-check" aria-hidden="true"><i class="icon icon--sm" data-lucide="check" aria-hidden="true"></i></span>شناسایی مباحث نیازمند تقویت</li>
                <li><span class="famo-feature-check" aria-hidden="true"><i class="icon icon--sm" data-lucide="check" aria-hidden="true"></i></span>بررسی روند عملکرد دروس</li>
                <li><span class="famo-feature-check" aria-hidden="true"><i class="icon icon--sm" data-lucide="check" aria-hidden="true"></i></span>گزارش هفتگی وضعیت آموزشی</li>
            </ul>
            <a href="<?= $baseEsc ?>pages/contact.php#consultation" class="inline-flex items-center gap-2 bg-[#445D84] text-white font-bold px-6 py-2.5 rounded-full hover:bg-[#344868] transition text-sm">
                درخواست مشاوره کنکور
                <i class="icon icon--sm" data-lucide="chevron-left" aria-hidden="true"></i>
            </a>
        </article>

        <!-- ۵. آمادگی تیزهوشان -->
        <article id="tizhooshan" class="famo-detail-block">
            <span class="famo-eyebrow famo-eyebrow--light">مدارس استعدادهای درخشان</span>
            <h2 class="text-2xl sm:text-3xl font-bold text-[#445D84] mb-3">آمادگی آزمون تیزهوشان در بابل</h2>
            <p class="text-gray-600 leading-relaxed mb-3">
                آزمون‌های ورودی مدارس استعدادهای درخشان، علاوه بر تسلط درسی، به مهارت‌هایی مانند تحلیل، استدلال، تشخیص الگو و حل مسئله نیاز دارند.
            </p>
            <p class="text-gray-600 leading-relaxed">
                برنامه آمادگی تیزهوشان در فامو با توجه به سطح و نیازهای دانش‌آموز تنظیم می‌شود تا تمرین و آموزش بر مهارت‌های مورد نیاز او متمرکز باشد.
            </p>
            <h3 class="text-base font-bold text-[#445D84] mt-5 mb-2">محورهای آموزشی</h3>
            <ul class="famo-bullet-grid">
                <li><span class="famo-feature-check" aria-hidden="true"><i class="icon icon--sm" data-lucide="check" aria-hidden="true"></i></span>تقویت مهارت استدلال</li>
                <li><span class="famo-feature-check" aria-hidden="true"><i class="icon icon--sm" data-lucide="check" aria-hidden="true"></i></span>توسعه توانایی تحلیل</li>
                <li><span class="famo-feature-check" aria-hidden="true"><i class="icon icon--sm" data-lucide="check" aria-hidden="true"></i></span>حل مسائل و تمرین‌های هوش</li>
                <li><span class="famo-feature-check" aria-hidden="true"><i class="icon icon--sm" data-lucide="check" aria-hidden="true"></i></span>شناسایی نقاط ضعف</li>
                <li><span class="famo-feature-check" aria-hidden="true"><i class="icon icon--sm" data-lucide="check" aria-hidden="true"></i></span>ارزیابی و تمرین متناسب با سطح دانش‌آموز</li>
            </ul>
            <a href="<?= $baseEsc ?>pages/contact.php#consultation" class="inline-flex items-center gap-2 bg-[#445D84] text-white font-bold px-6 py-2.5 rounded-full hover:bg-[#344868] transition text-sm">
                درخواست مشاوره تیزهوشان
                <i class="icon icon--sm" data-lucide="chevron-left" aria-hidden="true"></i>
            </a>
        </article>
    </div>
</section>

<!-- Detailed Active Courses Grid (Preserved Live API Integration) -->
<section id="courses" class="py-14 px-4 bg-white border-t border-[#E2D9C6]">
    <div class="container mx-auto max-w-6xl">
        <div class="text-center mb-10">
            <span class="famo-eyebrow famo-eyebrow--light">فهرست به‌روز سامانه</span>
            <h2 class="text-2xl sm:text-3xl font-bold text-[#445D84] mb-3">دوره‌های فعال آموزشگاه فامو</h2>
            <p class="text-gray-600 max-w-2xl mx-auto">
                جزئیات دوره‌های ثبت‌شده در سامانه آموزشگاه فامو بابل
            </p>
        </div>

        <div class="courses-grid" id="coursesContainer">
            <!-- Skeleton loaders shown until the API responds -->
            <div class="course-card-skeleton">
                <div class="skeleton skeleton-pulse skeleton-img"></div>
                <div class="skeleton skeleton-pulse skeleton-line-lg"></div>
                <div class="skeleton skeleton-pulse skeleton-line"></div>
                <div class="skeleton skeleton-pulse skeleton-line-sm"></div>
                <div class="skeleton skeleton-pulse skeleton-price"></div>
            </div>
            <div class="course-card-skeleton">
                <div class="skeleton skeleton-pulse skeleton-img"></div>
                <div class="skeleton skeleton-pulse skeleton-line-lg"></div>
                <div class="skeleton skeleton-pulse skeleton-line"></div>
                <div class="skeleton skeleton-pulse skeleton-line-sm"></div>
                <div class="skeleton skeleton-pulse skeleton-price"></div>
            </div>
            <div class="course-card-skeleton">
                <div class="skeleton skeleton-pulse skeleton-img"></div>
                <div class="skeleton skeleton-pulse skeleton-line-lg"></div>
                <div class="skeleton skeleton-pulse skeleton-line"></div>
                <div class="skeleton skeleton-pulse skeleton-line-sm"></div>
                <div class="skeleton skeleton-pulse skeleton-price"></div>
            </div>
        </div>

        <!-- Bottom Guidance CTA -->
        <div class="gradient-bg rounded-3xl p-8 sm:p-10 text-center text-white shadow-xl mt-14">
            <h2 class="text-2xl sm:text-3xl font-bold mb-3">نمی‌دانید کدام برنامه برای دانش‌آموز مناسب است؟</h2>
            <p class="text-white/90 max-w-2xl mx-auto leading-relaxed mb-7">
                انتخاب برنامه آموزشی باید با توجه به مقطع، سطح علمی، هدف و نیازهای دانش‌آموز انجام شود. برای بررسی شرایط تحصیلی و انتخاب خدمات مناسب با ما در ارتباط باشید.
            </p>
            <div class="flex flex-wrap items-center justify-center gap-3 sm:gap-4">
                <a href="<?= $baseEsc ?>pages/contact.php#consultation"
                    class="inline-flex items-center gap-2 bg-[#E2D9C6] text-[#445D84] font-bold px-8 py-3 rounded-full hover:bg-white transition duration-300 shadow-lg">
                    <i class="icon icon--sm" data-lucide="phone" aria-hidden="true"></i>
                    درخواست مشاوره
                </a>
                <a href="<?= $baseEsc ?>pages/services.php"
                    class="inline-flex items-center gap-2 border-2 border-[#E2D9C6] text-[#E2D9C6] font-bold px-7 py-3 rounded-full hover:bg-[#E2D9C6] hover:text-[#445D84] transition duration-300">
                    آشنایی با خدمات آموزشی
                </a>
            </div>
        </div>
    </div>
</section>
</main>

<!-- Footer -->
<?php include __DIR__ . '/../layouts/footer.php'; ?>

<script type="module" src="<?= htmlspecialchars(famo_local_asset('assets/js/courses.js', $base), ENT_QUOTES, 'UTF-8') ?>"></script>
</body>
</html>
