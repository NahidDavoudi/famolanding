<?php
declare(strict_types=1);
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../layouts/home-data.php';
require_once __DIR__ . '/../layouts/home-render.php';

$base = '/';
$baseEsc = htmlspecialchars($base, ENT_QUOTES, 'UTF-8');
$pageTitle = 'اساتید و پشتیبانان آموزشگاه فامو بابل | کادر دبیران رسمی و رتبه‌برترها';
$metaDescription = 'معرفی اساتید، دبیران رسمی آموزش‌وپرورش، پزشکان و پشتیبانان تخصصی آموزشگاه فامو بابل در دروس ریاضی، فیزیک، شیمی، زیست، تیزهوشان و مشاوره کنکور.';
$canonicalPath = 'team/';
$webPageType = 'AboutPage';
$ogImage = famo_public_site_url() . '/assets/images/team.webp';
$ogImageAlt = 'کادر آموزشی، اساتید و پشتیبانان آموزشگاه فامو بابل';
$breadcrumbs = [
    ['name' => 'خانه', 'url' => famo_canonical_url('')],
    ['name' => 'اساتید و پشتیبانان', 'url' => famo_canonical_url('team/')],
];

$homeData = famo_home_data();
$ssrInstructors = is_array($homeData['instructors'] ?? null)
    ? array_map('famo_normalize_instructor', array_values(array_filter($homeData['instructors'], 'is_array')))
    : [];
$ssrSupporters = is_array($homeData['supporters'] ?? null)
    ? array_map('famo_normalize_supporter', array_values(array_filter($homeData['supporters'], 'is_array')))
    : [];
$hasInstructorsSsr = count($ssrInstructors) > 0;
$hasSupportersSsr = count($ssrSupporters) > 0;

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

<?php include __DIR__ . '/../layouts/header.php'; ?>

<main id="main-content">

<section class="famo-page-hero gradient-bg text-white text-center">
    <div class="container mx-auto px-4 max-w-5xl">
        <p class="famo-eyebrow">کادر آموزشی و پشتیبانی | بابل</p>
        <h1 class="text-3xl sm:text-4xl font-bold mb-5 text-white">اساتید و پشتیبانان آموزشگاه فامو بابل</h1>
        <div class="relative w-full h-56 sm:h-72 md:h-96 rounded-2xl overflow-hidden shadow-2xl border border-white/20 mb-8">
            <img src="<?= $baseEsc ?>assets/images/team.webp" alt="کادر آموزشی، اساتید و پشتیبانان آموزشگاه فامو بابل"
                width="1200" height="670" decoding="async" fetchpriority="high" class="w-full h-full object-cover">
        </div>
        <p class="famo-on-dark-soft max-w-3xl mx-auto text-base sm:text-lg leading-relaxed">
            در آموزشگاه فامو بابل، تدریس دروس تخصصی توسط دبیران باسابقه و مدرسان حوزه ریاضی، فیزیک، شیمی، زیست و هوش تحلیلی انجام می‌شود و تیم پشتیبانان آموزشی روند مطالعه، آزمون‌ها و رفع اشکال هر دانش‌آموز را به‌صورت مستمر پیگیری می‌کنند.
        </p>
    </div>
</section>

<!-- Instructors Section -->
<section class="py-16 px-4">
    <div class="container mx-auto max-w-6xl">
        <h2 class="text-2xl font-bold text-[#445D84] mb-8 border-r-4 border-[#445D84] pr-3">اساتید تخصصی دروس، کنکور و تیزهوشان</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8" id="instructorsContainer" aria-busy="false"<?= $hasInstructorsSsr ? ' data-ssr="1"' : ' data-has-fallback="1"' ?>>
<?php if ($hasInstructorsSsr): ?>
<?php foreach ($ssrInstructors as $inst): ?>
<?= famo_render_person_card($inst) ?>
<?php endforeach; ?>
<?php else: ?>
            <article class="famo-advantage-card">
                <span class="famo-advantage-icon" aria-hidden="true"><i class="icon icon--lg" data-lucide="chart-column" aria-hidden="true"></i></span>
                <h3 class="famo-advantage-title">گروه آموزشی ریاضی و فیزیک</h3>
                <p class="famo-advantage-text">تدریس مفهومی ریاضی، حسابان، هندسه و فیزیک از مقطع ابتدایی تا متوسطه دوم و کنکور سراسری با تمرکز بر حل مسئله.</p>
                <a href="/courses/math-babol/" class="famo-inline-link">کلاس خصوصی ریاضی <i class="icon icon--sm" data-lucide="chevron-left" aria-hidden="true"></i></a>
            </article>
            <article class="famo-advantage-card">
                <span class="famo-advantage-icon" aria-hidden="true"><i class="icon icon--lg" data-lucide="book-open" aria-hidden="true"></i></span>
                <h3 class="famo-advantage-title">گروه آموزشی زیست‌شناسی و شیمی</h3>
                <p class="famo-advantage-text">همراهی مدرسان تخصصی و پزشکان مجموعه در آموزش خط‌به‌خط زیست‌شناسی، مسائل شیمی و تحلیل آزمون‌های رشته تجربی.</p>
                <a href="/courses/biology-babol/" class="famo-inline-link">کلاس خصوصی زیست <i class="icon icon--sm" data-lucide="chevron-left" aria-hidden="true"></i></a>
            </article>
            <article class="famo-advantage-card">
                <span class="famo-advantage-icon" aria-hidden="true"><i class="icon icon--lg" data-lucide="school" aria-hidden="true"></i></span>
                <h3 class="famo-advantage-title">گروه تیزهوشان و پایه</h3>
                <p class="famo-advantage-text">آموزش هوش تحلیلی، سرعت و دقت و تقویت دروس پایه برای آمادگی ورود به مدارس شهید بهشتی و فرزانگان بابل.</p>
                <a href="/courses/tizhooshan-babol/" class="famo-inline-link">آمادگی تیزهوشان <i class="icon icon--sm" data-lucide="chevron-left" aria-hidden="true"></i></a>
            </article>
<?php endif; ?>
        </div>
    </div>
</section>

<!-- Support Team Section -->
<section id="support" class="py-16 px-4 bg-white border-t border-[#E2D9C6]">
    <div class="container mx-auto max-w-6xl">
        <h2 class="text-2xl font-bold text-[#445D84] mb-8 border-r-4 border-[#445D84] pr-3">تیم پشتیبانان و مشاوران آموزشی</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8" id="supportersContainer" aria-busy="false"<?= $hasSupportersSsr ? ' data-ssr="1"' : ' data-has-fallback="1"' ?>>
<?php if ($hasSupportersSsr): ?>
<?php foreach ($ssrSupporters as $sup): ?>
<?= famo_render_person_card($sup) ?>
<?php endforeach; ?>
<?php else: ?>
            <div class="col-span-full famo-detail-block">
                <h3 class="text-xl font-bold text-[#445D84] mb-3">همراهی مستمر پشتیبانان آموزشی در طول هفته</h3>
                <p class="text-gray-600 leading-relaxed mb-5">
                    پشتیبانان تخصصی فامو در رشته‌های تجربی، ریاضی، انسانی و پایه‌های متوسطه وظیفه پیگیری اجرای برنامه مطالعاتی، تحلیل کارنامه آزمون‌های هفتگی، هماهنگی جلسات رفع اشکال و ثبت گزارش منظم برای خانواده را بر عهده دارند.
                </p>
                <div class="flex flex-wrap gap-3">
                    <a href="/counseling-babol/" class="famo-anchor-pill">مشاوره و برنامه‌ریزی تحصیلی</a>
                    <a href="/services/smart-platform/" class="famo-anchor-pill">سامانه هوشمند و کارنامه هفتگی</a>
                    <a href="/contact/#consultation" class="famo-anchor-pill">درخواست مشاوره اولیه</a>
                </div>
            </div>
<?php endif; ?>
        </div>
    </div>
</section>
</main>

<?php include __DIR__ . '/../layouts/footer.php'; ?>

<script type="module" src="<?= htmlspecialchars(famo_local_asset('assets/js/team.js', $base), ENT_QUOTES, 'UTF-8') ?>"></script>
</body>
</html>
