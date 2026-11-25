<?php
declare(strict_types=1);
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../layouts/home-data.php';
require_once __DIR__ . '/../layouts/home-render.php';

$base = '/';
$baseEsc = htmlspecialchars($base, ENT_QUOTES, 'UTF-8');
$siteUrl = famo_public_site_url();
$coursePageUrl = famo_canonical_url('courses/');
$orgRef = [
    '@type' => 'EducationalOrganization',
    '@id'   => $siteUrl . '/#organization',
    'name'  => 'آموزشگاه فامو',
    'url'   => $siteUrl . '/',
];

$pageTitle = 'دوره‌های آموزشی و تقویتی بابل | ابتدایی، متوسطه، کنکور و تیزهوشان | فامو';
$metaDescription = 'فهرست کامل دوره‌های آموزشی، کلاس‌های خصوصی ریاضی، فیزیک، شیمی، زیست، رفع اشکال، متوسطه، کنکور و تیزهوشان در آموزشگاه فامو بابل.';
$canonicalPath = 'courses/';
$webPageType = 'CollectionPage';
$ogImage = $siteUrl . '/assets/images/courses_banner.webp';
$ogImageAlt = 'دوره‌های آموزشی و کلاس‌های خصوصی آموزشگاه فامو در بابل';
$breadcrumbs = [
    ['name' => 'خانه', 'url' => famo_canonical_url('')],
    ['name' => 'دوره‌های آموزشی', 'url' => $coursePageUrl],
];

// Server-side courses data (reuses cached famo_home_data with fail-soft fallback)
$homeData = famo_home_data();
$rawCourses = is_array($homeData['courses'] ?? null)
    ? array_values(array_filter($homeData['courses'], 'is_array'))
    : [];
$hasCoursesSsr = count($rawCourses) > 0;

$subjectLandings = [
    [
        'name'  => 'آموزشگاه و کلاس خصوصی ریاضی بابل',
        'desc'  => 'تدریس مفهومی ریاضی، حسابان، هندسه و آمار از پایه چهارم تا دوازدهم و کنکور با آزمون مبحثی.',
        'level' => 'ابتدایی تا کنکور',
        'url'   => '/courses/math-babol/',
        'icon'  => 'chart-column',
    ],
    [
        'name'  => 'کلاس خصوصی فیزیک بابل',
        'desc'  => 'آموزش شهودی و حل مسائل تشریحی امتحان نهایی و تست‌های کنکور فیزیک دهم تا دوازدهم.',
        'level' => 'متوسطه دوم و کنکور',
        'url'   => '/courses/physics-babol/',
        'icon'  => 'star',
    ],
    [
        'name'  => 'کلاس خصوصی شیمی بابل',
        'desc'  => 'تسلط هم‌زمان بر مسائل استوکیومتری، شیمی آلی و متن کتاب درسی ویژه نهایی و کنکور.',
        'level' => 'متوسطه دوم و کنکور',
        'url'   => '/courses/chemistry-babol/',
        'icon'  => 'file-chart-column',
    ],
    [
        'name'  => 'کلاس خصوصی زیست بابل',
        'desc'  => 'تحلیل خط‌به‌خط کتاب درسی، شکل‌ها و تست‌های ترکیبی زیست‌شناسی رشته تجربی.',
        'level' => 'دهم تا دوازدهم تجربی',
        'url'   => '/courses/biology-babol/',
        'icon'  => 'book-open',
    ],
    [
        'name'  => 'کلاس رفع اشکال بابل',
        'desc'  => 'جلسات انفرادی رفع اشکال ریاضی و دروس تخصصی پیش از امتحانات مدرسه، نهایی و آزمون‌ها.',
        'level' => 'تمام مقاطع تحصیلی',
        'url'   => '/courses/rafa-eshkal-babol/',
        'icon'  => 'circle-help',
    ],
    [
        'name'  => 'مشاوره تحصیلی و کنکور بابل',
        'desc'  => 'برنامه‌ریزی فردی، انتخاب منابع، تحلیل آزمون و پایش هفتگی وضعیت تحصیلی دانش‌آموز.',
        'level' => 'متوسطه، تیزهوشان و کنکور',
        'url'   => '/counseling-babol/',
        'icon'  => 'compass',
    ],
];

$gradePrograms = [
    [
        'badge'  => 'مقطع ابتدایی',
        'title'  => 'کلاس تقویتی ابتدایی بابل (چهارم، پنجم و ششم)',
        'desc'   => 'تقویت مفاهیم پایه ریاضی و علوم، تمرین حل مسئله و رفع اشکال انفرادی با ریتم متناسب با یادگیری دانش‌آموز.',
        'points' => ['تقویت مفاهیم پایه درسی', 'رفع اشکال مباحث', 'افزایش تسلط و دقت', 'آماده‌سازی تدریجی مسیر تیزهوشان'],
        'url'    => '/courses/elementary-babol/',
        'cta'    => 'صفحه دوره ابتدایی',
    ],
    [
        'badge'  => 'متوسطه اول',
        'title'  => 'کلاس تقویتی متوسطه اول بابل (هفتم، هشتم و نهم)',
        'desc'   => 'تثبیت پایه‌های ریاضی و علوم، آموزش مفهومی و شناسایی نقاط ضعف پیش از هدایت تحصیلی و ورود به دبیرستان.',
        'points' => ['تقویت پایه‌های درسی', 'آموزش مفهومی ریاضی و علوم', 'رفع اشکال تخصصی', 'آمادگی آزمون‌های ورودی مدارس'],
        'url'    => '/courses/middle-school-babol/',
        'cta'    => 'صفحه دوره متوسطه اول',
    ],
    [
        'badge'  => 'متوسطه دوم',
        'title'  => 'کلاس تقویتی متوسطه دوم بابل (دهم، یازدهم و دوازدهم)',
        'desc'   => 'آموزش دروس تخصصی و عمومی با تمرکز هم‌زمان بر نمرات امتحانات نهایی (سوابق تحصیلی) و زیربنای کنکور.',
        'points' => ['آمادگی امتحان نهایی', 'آموزش دروس تخصصی و عمومی', 'رفع اشکال موضوعی', 'پایش هفتگی روند نمرات'],
        'url'    => '/courses/high-school-babol/',
        'cta'    => 'صفحه دوره متوسطه دوم',
    ],
    [
        'badge'  => 'کنکور سراسری',
        'title'  => 'آموزشگاه کنکور بابل (تجربی، ریاضی و انسانی)',
        'desc'   => 'کلاس خصوصی دروس کنکور، آزمون‌های شخصی‌سازی‌شده، تحلیل درصدها و گزارش هفتگی وضعیت آموزشی داوطلبان.',
        'points' => ['کلاس خصوصی دروس کنکور', 'آزمون شخصی‌سازی‌شده', 'تحلیل نتایج و درصدها', 'گزارش هفتگی پیامکی به اولیا'],
        'url'    => '/courses/konkur-babol/',
        'cta'    => 'صفحه آمادگی کنکور',
    ],
    [
        'badge'  => 'مدارس استعدادهای درخشان',
        'title'  => 'آموزشگاه تیزهوشان بابل (شهید بهشتی و فرزانگان)',
        'desc'   => 'تقویت استدلال تحلیلی، سرعت و دقت در مسائل هوش و تسلط بر دروس استعداد تحصیلی ویژه ششم و نهم.',
        'points' => ['هوش تحلیلی و ریاضی-منطقی', 'تمرین سرعت و دقت', 'استعداد تحصیلی نهم به دهم', 'آزمون‌های شبیه‌ساز زمان‌دار'],
        'url'    => '/courses/tizhooshan-babol/',
        'cta'    => 'صفحه آمادگی تیزهوشان',
    ],
];

$courseListItems = [];
$pos = 1;
foreach ($gradePrograms as $gp) {
    $courseListItems[] = [
        '@type'    => 'ListItem',
        'position' => $pos++,
        'item'     => [
            '@type'       => 'Course',
            'name'        => $gp['title'],
            'description' => $gp['desc'],
            'url'         => famo_canonical_url(ltrim($gp['url'], '/')),
            'inLanguage'  => 'fa-IR',
            'provider'    => $orgRef,
        ],
    ];
}
foreach ($subjectLandings as $sl) {
    $courseListItems[] = [
        '@type'    => 'ListItem',
        'position' => $pos++,
        'item'     => [
            '@type'            => 'Course',
            'name'             => $sl['name'],
            'description'      => $sl['desc'],
            'url'              => famo_canonical_url(ltrim($sl['url'], '/')),
            'inLanguage'       => 'fa-IR',
            'educationalLevel' => $sl['level'],
            'provider'         => $orgRef,
        ],
    ];
}

$courseSchemaJson = json_encode(
    [
        '@context'        => 'https://schema.org',
        '@type'           => 'ItemList',
        '@id'             => $coursePageUrl . '#courses',
        'name'            => 'دوره‌های آموزشی و تقویتی آموزشگاه فامو بابل',
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
    <script type="application/ld+json" id="courseSchema"><?= $courseSchemaJson ?></script>

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
            width: 100%;
            background: #ffffff;
            border: 1px solid #E2D9C6;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 25px -12px rgba(68, 93, 132, 0.35);
            transition: transform .2s ease, box-shadow .2s ease;
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
        #courses .course-card:hover .course-image { transform: scale(1.05); }
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
        #courses .course-desc {
            margin: 10px 0 0;
            font-size: 13px;
            color: #4b5563;
            line-height: 1.9;
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
            min-height: 44px;
            background: #445D84;
            color: #fff;
            font-size: 13px;
            font-weight: 700;
            padding: 10px 16px;
            border-radius: 10px;
            text-decoration: none;
            white-space: nowrap;
            transition: background-color .18s ease, transform .18s ease;
        }
        #courses .course-cta:hover {
            background: #344868;
            transform: translateY(-1px);
        }
    </style>
</head>

<body class="font-family text-gray-800 bg-[#f9f7f3]">

<?php include __DIR__ . '/../layouts/header.php'; ?>

<main id="main-content">
<!-- Page Title Banner -->
<section class="famo-page-hero gradient-bg text-white text-center">
    <div class="container mx-auto px-4 max-w-5xl">
        <p class="famo-eyebrow">آموزشگاه فامو | بابل</p>
        <h1 class="text-3xl sm:text-4xl font-bold mb-6 text-white">دوره‌های آموزشی و تقویتی فامو در بابل</h1>

        <div class="relative w-full h-56 sm:h-72 md:h-96 rounded-2xl overflow-hidden shadow-2xl border border-white/20 mb-8">
            <img src="<?= $baseEsc ?>assets/images/courses_banner.webp" alt="دوره‌های آموزشی و کلاس‌های خصوصی آموزشگاه فامو در بابل"
                width="2752" height="1536" decoding="async" fetchpriority="high" class="w-full h-full object-cover">
        </div>

        <p class="famo-on-dark-soft max-w-3xl mx-auto text-base sm:text-lg leading-relaxed mb-3">
            فامو برنامه‌های آموزشی خود را برای دانش‌آموزان مقاطع مختلف در بابل ارائه می‌کند. هر مقطع و درس تخصصی دارای صفحه و مسیر اختصاصی متناسب با سطح علمی و هدف هر دانش‌آموز است.
        </p>
        <p class="famo-on-dark-soft max-w-2xl mx-auto text-sm sm:text-base leading-relaxed">
            از کلاس تقویتی ابتدایی و متوسطه تا تدریس خصوصی ریاضی، فیزیک، شیمی، زیست، رفع اشکال، تیزهوشان و کنکور، مسیر آموزش به‌صورت انفرادی و هوشمند طراحی می‌شود.
        </p>
    </div>
</section>

<!-- Direct Links to Dedicated Clean-URL Pages -->
<section class="famo-anchor-bar" aria-label="صفحات اختصاصی مقاطع و دروس">
    <div class="container mx-auto max-w-6xl">
        <div class="famo-anchor-pills">
            <a href="/courses/elementary-babol/" class="famo-anchor-pill">ابتدایی</a>
            <a href="/courses/middle-school-babol/" class="famo-anchor-pill">متوسطه اول</a>
            <a href="/courses/high-school-babol/" class="famo-anchor-pill">متوسطه دوم</a>
            <a href="/courses/konkur-babol/" class="famo-anchor-pill">کنکور</a>
            <a href="/courses/tizhooshan-babol/" class="famo-anchor-pill">تیزهوشان</a>
            <a href="/courses/math-babol/" class="famo-anchor-pill">ریاضی</a>
            <a href="/courses/physics-babol/" class="famo-anchor-pill">فیزیک</a>
            <a href="/courses/chemistry-babol/" class="famo-anchor-pill">شیمی</a>
            <a href="/courses/biology-babol/" class="famo-anchor-pill">زیست</a>
            <a href="/courses/rafa-eshkal-babol/" class="famo-anchor-pill">رفع اشکال</a>
        </div>
    </div>
</section>

<!-- Subject & Tutoring Landing Hub -->
<section class="py-12 sm:py-16 px-4 bg-white border-b border-[#E2D9C6]">
    <div class="container mx-auto max-w-6xl">
        <div class="text-center mb-10">
            <span class="famo-eyebrow famo-eyebrow--light">دروس تخصصی و رفع اشکال</span>
            <h2 class="text-2xl sm:text-3xl font-bold text-[#445D84] mb-3">کلاس‌های خصوصی بر اساس درس</h2>
            <p class="text-gray-600 max-w-2xl mx-auto">
                برای مشاهده سرفصل‌ها، روش تدریس و سؤالات متداول هر درس، وارد صفحه اختصاصی آن شوید.
            </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($subjectLandings as $sl): ?>
            <article class="famo-advantage-card hover-lift">
                <span class="famo-advantage-icon" aria-hidden="true">
                    <i class="icon icon--lg" data-lucide="<?= htmlspecialchars($sl['icon'], ENT_QUOTES, 'UTF-8') ?>" aria-hidden="true"></i>
                </span>
                <h3 class="famo-advantage-title"><?= htmlspecialchars($sl['name'], ENT_QUOTES, 'UTF-8') ?></h3>
                <p class="text-xs font-bold text-[#736158] mb-2"><?= htmlspecialchars($sl['level'], ENT_QUOTES, 'UTF-8') ?></p>
                <p class="famo-advantage-text"><?= htmlspecialchars($sl['desc'], ENT_QUOTES, 'UTF-8') ?></p>
                <a href="<?= htmlspecialchars($sl['url'], ENT_QUOTES, 'UTF-8') ?>" class="famo-inline-link">
                    مشاهده صفحه اختصاصی
                    <i class="icon icon--sm" data-lucide="chevron-left" aria-hidden="true"></i>
                </a>
            </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- 5 Grade & Goal Programs (Each linking to its own clean URL) -->
<section class="py-14 px-4">
    <div class="container mx-auto max-w-6xl famo-detail-stack">
        <?php foreach ($gradePrograms as $gp): ?>
        <article class="famo-detail-block">
            <span class="famo-eyebrow famo-eyebrow--light"><?= htmlspecialchars($gp['badge'], ENT_QUOTES, 'UTF-8') ?></span>
            <h2 class="text-2xl sm:text-3xl font-bold text-[#445D84] mb-3"><?= htmlspecialchars($gp['title'], ENT_QUOTES, 'UTF-8') ?></h2>
            <p class="text-gray-600 leading-relaxed mb-4"><?= htmlspecialchars($gp['desc'], ENT_QUOTES, 'UTF-8') ?></p>
            <ul class="famo-bullet-grid">
                <?php foreach ($gp['points'] as $pt): ?>
                <li><span class="famo-feature-check" aria-hidden="true"><i class="icon icon--sm" data-lucide="check" aria-hidden="true"></i></span><?= htmlspecialchars($pt, ENT_QUOTES, 'UTF-8') ?></li>
                <?php endforeach; ?>
            </ul>
            <div class="flex flex-wrap items-center gap-3">
                <a href="<?= htmlspecialchars($gp['url'], ENT_QUOTES, 'UTF-8') ?>" class="inline-flex items-center gap-2 bg-[#445D84] text-white font-bold px-6 py-2.5 rounded-full hover:bg-[#344868] transition text-sm">
                    <?= htmlspecialchars($gp['cta'], ENT_QUOTES, 'UTF-8') ?>
                    <i class="icon icon--sm" data-lucide="chevron-left" aria-hidden="true"></i>
                </a>
                <a href="/contact/#consultation" class="inline-flex items-center gap-2 border-2 border-[#445D84] text-[#445D84] font-bold px-5 py-2 rounded-full hover:bg-[#445D84] hover:text-white transition text-sm">
                    درخواست مشاوره
                </a>
            </div>
        </article>
        <?php endforeach; ?>
    </div>
</section>

<!-- Active Courses Grid (SSR + Fail-soft fallback so crawlers never see an error or empty skeleton) -->
<section id="courses" class="py-14 px-4 bg-white border-t border-[#E2D9C6]">
    <div class="container mx-auto max-w-6xl">
        <div class="text-center mb-10">
            <span class="famo-eyebrow famo-eyebrow--light">فهرست به‌روز سامانه</span>
            <h2 class="text-2xl sm:text-3xl font-bold text-[#445D84] mb-3">دوره‌های فعال آموزشگاه فامو</h2>
            <p class="text-gray-600 max-w-2xl mx-auto">
                جزئیات دوره‌های ثبت‌شده در سامانه آموزشگاه فامو بابل
            </p>
        </div>

        <div class="courses-grid" id="coursesContainer"<?= $hasCoursesSsr ? ' data-ssr="1"' : ' data-has-fallback="1"' ?>>
<?php if ($hasCoursesSsr): ?>
<?php foreach ($rawCourses as $courseItem): ?>
<?= famo_render_course_grid_card($courseItem) ?>
<?php endforeach; ?>
<?php else: ?>
            <?php foreach (array_slice($subjectLandings, 0, 3) as $fb): ?>
            <article class="course-card">
                <div class="course-image-wrapper" style="background: linear-gradient(135deg, #445D84, #6D8B9E);">
                    <div class="course-image-fallback">
                        <i class="icon icon--3xl text-white/90" data-lucide="<?= htmlspecialchars($fb['icon'], ENT_QUOTES, 'UTF-8') ?>" aria-hidden="true"></i>
                    </div>
                    <span class="course-badge">برنامه فعال بابل</span>
                </div>
                <div class="course-body">
                    <h3 class="course-name"><?= htmlspecialchars($fb['name'], ENT_QUOTES, 'UTF-8') ?></h3>
                    <div class="course-meta"><span><?= htmlspecialchars($fb['level'], ENT_QUOTES, 'UTF-8') ?></span><span>خصوصی و انفرادی</span></div>
                    <p class="course-desc"><?= htmlspecialchars($fb['desc'], ENT_QUOTES, 'UTF-8') ?></p>
                    <div class="course-footer">
                        <a href="/contact/#consultation" class="course-cta">درخواست مشاوره</a>
                        <a href="<?= htmlspecialchars($fb['url'], ENT_QUOTES, 'UTF-8') ?>" class="text-sm font-bold text-[#445D84] hover:underline inline-flex items-center gap-1">
                            جزئیات بیشتر
                            <i class="icon icon--sm" data-lucide="chevron-left" aria-hidden="true"></i>
                        </a>
                    </div>
                </div>
            </article>
            <?php endforeach; ?>
<?php endif; ?>
        </div>

        <!-- Bottom Guidance CTA -->
        <div class="gradient-bg rounded-3xl p-8 sm:p-10 text-center text-white shadow-xl mt-14">
            <h2 class="text-2xl sm:text-3xl font-bold mb-3">نمی‌دانید کدام برنامه برای دانش‌آموز مناسب است؟</h2>
            <p class="famo-on-dark-soft max-w-2xl mx-auto leading-relaxed mb-7">
                انتخاب برنامه آموزشی باید با توجه به مقطع، سطح علمی، هدف و نیازهای دانش‌آموز انجام شود. برای بررسی شرایط تحصیلی و انتخاب خدمات مناسب با ما در ارتباط باشید.
            </p>
            <div class="flex flex-wrap items-center justify-center gap-3 sm:gap-4">
                <a href="/contact/#consultation"
                    class="inline-flex items-center gap-2 famo-btn-cream font-bold px-8 py-3 rounded-full transition duration-300 shadow-lg">
                    <i class="icon icon--sm" data-lucide="phone" aria-hidden="true"></i>
                    درخواست مشاوره
                </a>
                <a href="/services/"
                    class="inline-flex items-center gap-2 famo-btn-outline-cream font-bold px-7 py-3 rounded-full transition duration-300">
                    آشنایی با خدمات آموزشی
                </a>
            </div>
        </div>
    </div>
</section>
</main>

<?php include __DIR__ . '/../layouts/footer.php'; ?>

<script type="module" src="<?= htmlspecialchars(famo_local_asset('assets/js/courses.js', $base), ENT_QUOTES, 'UTF-8') ?>"></script>
</body>
</html>
