<?php
declare(strict_types=1);
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../layouts/seo-pages.php';

$rawSlug = isset($_GET['slug']) && is_string($_GET['slug']) ? trim($_GET['slug']) : '';
$pageData = famo_get_seo_page($rawSlug);

if ($pageData === null) {
    require __DIR__ . '/../404.php';
    exit;
}

$base = '/';
$baseEsc = htmlspecialchars($base, ENT_QUOTES, 'UTF-8');
$siteUrl = famo_public_site_url();
$slug = (string) $pageData['slug'];
$canonicalPath = $slug . '/';
$pageCanonicalUrl = famo_canonical_url($canonicalPath);

$pageTitle = (string) ($pageData['pageTitle'] ?? 'آموزشگاه فامو بابل');
$metaDescription = (string) ($pageData['metaDescription'] ?? '');
$webPageType = 'WebPage';
$heroImage = (string) ($pageData['heroImage'] ?? '/assets/images/courses_banner.webp');
$ogImage = str_starts_with($heroImage, 'http') ? $heroImage : ($siteUrl . $heroImage);
$ogImageAlt = (string) ($pageData['heroImageAlt'] ?? $pageTitle);

$breadcrumbs = [];
if (is_array($pageData['breadcrumbs'] ?? null)) {
    foreach ($pageData['breadcrumbs'] as $crumb) {
        if (!is_array($crumb) || empty($crumb['name'])) {
            continue;
        }
        $crumbPath = (string) ($crumb['path'] ?? '');
        $breadcrumbs[] = [
            'name' => (string) $crumb['name'],
            'url'  => famo_canonical_url($crumbPath),
        ];
    }
}

$orgRef = [
    '@type' => 'EducationalOrganization',
    '@id'   => $siteUrl . '/#organization',
    'name'  => 'آموزشگاه فامو',
    'url'   => $siteUrl . '/',
];

$schemaType = (string) ($pageData['schemaType'] ?? 'Course');
$extraSchemas = [];

if ($schemaType === 'Course') {
    $courseEntity = [
        '@type'             => 'Course',
        '@id'               => $pageCanonicalUrl . '#course',
        'name'              => (string) ($pageData['h1'] ?? $pageTitle),
        'description'       => $metaDescription,
        'url'               => $pageCanonicalUrl,
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
                    'streetAddress'   => 'میدان باغ فردوس، جنب بیمارستان بابل کلینیک، مدرسه بهارستان',
                ],
            ],
        ],
    ];
    if (!empty($pageData['educationalLevel'])) {
        $courseEntity['educationalLevel'] = (string) $pageData['educationalLevel'];
    }
    $extraSchemas[] = $courseEntity;
} elseif ($schemaType === 'Service') {
    $extraSchemas[] = [
        '@type'       => 'Service',
        '@id'         => $pageCanonicalUrl . '#service',
        'name'        => (string) ($pageData['h1'] ?? $pageTitle),
        'description' => $metaDescription,
        'url'         => $pageCanonicalUrl,
        'provider'    => $orgRef,
        'areaServed'  => [
            ['@type' => 'City', 'name' => 'بابل'],
            ['@type' => 'AdministrativeArea', 'name' => 'مازندران'],
        ],
    ];
} else {
    $extraSchemas[] = [
        '@type'       => 'LocalBusiness',
        '@id'         => $pageCanonicalUrl . '#local',
        'name'        => 'آموزشگاه فامو بابل',
        'description' => $metaDescription,
        'url'         => $pageCanonicalUrl,
        'telephone'   => '+98-901-440-2300',
        'address'     => [
            '@type'           => 'PostalAddress',
            'addressLocality' => 'بابل',
            'addressRegion'   => 'مازندران',
            'addressCountry'  => 'IR',
            'streetAddress'   => 'میدان باغ فردوس، جنب بیمارستان بابل کلینیک، مدرسه بهارستان',
        ],
        'areaServed'  => [
            ['@type' => 'City', 'name' => 'بابل'],
            ['@type' => 'City', 'name' => 'امیرکلا'],
            ['@type' => 'City', 'name' => 'بابلسر'],
            ['@type' => 'City', 'name' => 'آمل'],
            ['@type' => 'City', 'name' => 'قائمشهر'],
        ],
    ];
}

$faqs = is_array($pageData['faqs'] ?? null) ? $pageData['faqs'] : [];
if (count($faqs) > 0) {
    $faqMainEntity = [];
    foreach ($faqs as $faq) {
        if (!is_array($faq) || empty($faq['q']) || empty($faq['a'])) {
            continue;
        }
        $faqMainEntity[] = [
            '@type'          => 'Question',
            'name'           => (string) $faq['q'],
            'acceptedAnswer' => [
                '@type' => 'Answer',
                'text'  => (string) $faq['a'],
            ],
        ];
    }
    if (count($faqMainEntity) > 0) {
        $extraSchemas[] = [
            '@type'      => 'FAQPage',
            '@id'        => $pageCanonicalUrl . '#faq',
            'inLanguage' => 'fa-IR',
            'mainEntity' => $faqMainEntity,
        ];
    }
}

$page_libs = [
    'gsap'           => false,
    'scroll_trigger' => false,
    'scroll_to'      => false,
    'swiper'         => false,
    'animation'      => false,
];

$audience = is_array($pageData['audience'] ?? null) ? $pageData['audience'] : [];
$highlights = is_array($pageData['highlights'] ?? null) ? $pageData['highlights'] : [];
$syllabus = is_array($pageData['syllabus'] ?? null) ? $pageData['syllabus'] : [];
$related = is_array($pageData['related'] ?? null) ? $pageData['related'] : [];
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
        <div class="container mx-auto px-4 max-w-4xl">
            <?php if (count($breadcrumbs) > 1): ?>
            <nav aria-label="مسیر صفحه" class="flex flex-wrap items-center justify-center gap-2 text-xs sm:text-sm famo-on-dark-soft mb-4">
                <?php foreach ($breadcrumbs as $idx => $crumb): ?>
                    <?php if ($idx > 0): ?><span aria-hidden="true">/</span><?php endif; ?>
                    <?php if ($idx < count($breadcrumbs) - 1): ?>
                        <?php $crumbHref = '/' . ltrim((string) ($pageData['breadcrumbs'][$idx]['path'] ?? ''), '/'); ?>
                        <a href="<?= htmlspecialchars($crumbHref, ENT_QUOTES, 'UTF-8') ?>" class="hover:text-white transition underline-offset-4 hover:underline">
                            <?= htmlspecialchars($crumb['name'], ENT_QUOTES, 'UTF-8') ?>
                        </a>
                    <?php else: ?>
                        <span class="famo-on-dark-cream font-bold" aria-current="page"><?= htmlspecialchars($crumb['name'], ENT_QUOTES, 'UTF-8') ?></span>
                    <?php endif; ?>
                <?php endforeach; ?>
            </nav>
            <?php endif; ?>

            <p class="famo-eyebrow"><?= htmlspecialchars((string) ($pageData['eyebrow'] ?? 'آموزشگاه فامو | بابل'), ENT_QUOTES, 'UTF-8') ?></p>
            <h1 class="text-2xl sm:text-4xl font-bold mb-5 text-white leading-snug">
                <?= htmlspecialchars((string) ($pageData['h1'] ?? $pageTitle), ENT_QUOTES, 'UTF-8') ?>
            </h1>
            <p class="famo-on-dark-soft text-base sm:text-lg leading-relaxed max-w-3xl mx-auto mb-7">
                <?= htmlspecialchars((string) ($pageData['lead'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
            </p>

            <div class="flex flex-wrap items-center justify-center gap-3 sm:gap-4">
                <a href="/contact/#consultation"
                    class="inline-flex items-center gap-2 famo-btn-cream font-bold px-7 py-3 rounded-full transition duration-300 shadow-lg">
                    <i class="icon icon--sm" data-lucide="phone" aria-hidden="true"></i>
                    درخواست مشاوره و تعیین سطح
                </a>
                <a href="/courses/"
                    class="inline-flex items-center gap-2 famo-btn-outline-cream font-bold px-6 py-3 rounded-full transition duration-300">
                    <i class="icon icon--sm" data-lucide="book-open" aria-hidden="true"></i>
                    همه دوره‌ها و مقاطع
                </a>
            </div>
        </div>
    </section>

    <!-- Highlights (3 Cards) -->
    <?php if (count($highlights) > 0): ?>
    <section class="py-12 sm:py-16 px-4 bg-white border-b border-[#E2D9C6]">
        <div class="container mx-auto max-w-6xl">
            <div class="text-center mb-10">
                <span class="famo-eyebrow famo-eyebrow--light">ویژگی‌های برنامه در فامو</span>
                <h2 class="text-2xl sm:text-3xl font-bold text-[#445D84]">شیوه اجرا و پیگیری آموزشی</h2>
            </div>
            <div class="famo-summary-grid">
                <?php foreach ($highlights as $item): ?>
                <article class="famo-advantage-card hover-lift">
                    <span class="famo-advantage-icon" aria-hidden="true">
                        <i class="icon icon--lg" data-lucide="check" aria-hidden="true"></i>
                    </span>
                    <h3 class="famo-advantage-title"><?= htmlspecialchars((string) ($item['title'] ?? ''), ENT_QUOTES, 'UTF-8') ?></h3>
                    <p class="famo-advantage-text"><?= htmlspecialchars((string) ($item['text'] ?? ''), ENT_QUOTES, 'UTF-8') ?></p>
                </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Audience & Syllabus -->
    <section class="py-12 sm:py-16 px-4">
        <div class="container mx-auto max-w-6xl famo-two-col">
            <article class="famo-detail-block">
                <span class="famo-eyebrow famo-eyebrow--light">مخاطبان این برنامه</span>
                <h2 class="text-xl sm:text-2xl font-bold text-[#445D84] mb-4">این برنامه برای چه دانش‌آموزانی مناسب است؟</h2>
                <ul class="space-y-3 text-gray-700 leading-relaxed list-none p-0 m-0">
                    <?php foreach ($audience as $line): ?>
                    <li class="flex items-start gap-2.5">
                        <span class="famo-feature-check mt-1 shrink-0" aria-hidden="true">
                            <i class="icon icon--sm" data-lucide="check" aria-hidden="true"></i>
                        </span>
                        <span><?= htmlspecialchars((string) $line, ENT_QUOTES, 'UTF-8') ?></span>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </article>

            <article class="famo-detail-block">
                <span class="famo-eyebrow famo-eyebrow--light">سرفصل‌ها و محورهای کلیدی</span>
                <h2 class="text-xl sm:text-2xl font-bold text-[#445D84] mb-4">آنچه در این مسیر پوشش داده می‌شود</h2>
                <ul class="space-y-3 text-gray-700 leading-relaxed list-none p-0 m-0">
                    <?php foreach ($syllabus as $topic): ?>
                    <li class="flex items-start gap-2.5">
                        <span class="famo-feature-check mt-1 shrink-0" aria-hidden="true">
                            <i class="icon icon--sm" data-lucide="book-open" aria-hidden="true"></i>
                        </span>
                        <span><?= htmlspecialchars((string) $topic, ENT_QUOTES, 'UTF-8') ?></span>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </article>
        </div>
    </section>

    <!-- FAQs -->
    <?php if (count($faqs) > 0): ?>
    <section class="py-12 sm:py-16 px-4 bg-white border-t border-[#E2D9C6]">
        <div class="container mx-auto max-w-4xl">
            <div class="text-center mb-8">
                <span class="famo-eyebrow famo-eyebrow--light">پرسش‌های پرتکرار</span>
                <h2 class="text-2xl sm:text-3xl font-bold text-[#445D84]">سؤالات متداول خانواده‌ها و دانش‌آموزان</h2>
            </div>

            <div class="space-y-4">
                <?php foreach ($faqs as $faq): ?>
                <div class="faq-item bg-[#f9f7f3] rounded-xl shadow-sm border border-[#E2D9C6] overflow-hidden">
                    <div class="faq-question cursor-pointer p-5 flex justify-between items-center gap-4" tabindex="0" role="button" aria-expanded="false">
                        <h3 class="text-base sm:text-lg font-bold text-[#445D84] m-0">
                            <?= htmlspecialchars((string) ($faq['q'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
                        </h3>
                        <span class="faq-icon transform transition-transform duration-300 shrink-0">
                            <i class="icon icon--sm" data-lucide="chevron-down" aria-hidden="true"></i>
                        </span>
                    </div>
                    <div class="faq-answer px-5">
                        <p class="pb-5 text-gray-700 leading-relaxed m-0">
                            <?= htmlspecialchars((string) ($faq['a'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
                        </p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Related Internal Links + Final CTA -->
    <section class="py-12 sm:py-16 px-4">
        <div class="container mx-auto max-w-6xl space-y-10">
            <?php if (count($related) > 0): ?>
            <div class="famo-detail-block">
                <h2 class="text-xl font-bold text-[#445D84] mb-4">مسیرها و صفحات مرتبط در آموزشگاه فامو</h2>
                <div class="flex flex-wrap gap-3">
                    <?php foreach ($related as $rel): ?>
                    <a href="/<?= htmlspecialchars(ltrim((string) ($rel['path'] ?? ''), '/'), ENT_QUOTES, 'UTF-8') ?>"
                        class="famo-anchor-pill">
                        <?= htmlspecialchars((string) ($rel['label'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <div class="gradient-bg rounded-3xl p-8 sm:p-10 text-center text-white shadow-xl">
                <h2 class="text-2xl sm:text-3xl font-bold mb-3">آماده شروع برنامه شخصی‌سازی‌شده هستید؟</h2>
                <p class="famo-on-dark-soft max-w-2xl mx-auto leading-relaxed mb-7">
                    برای بررسی پایه تحصیلی، انتخاب استاد مناسب و هماهنگی جلسه مشاوره حضوری در میدان باغ فردوس بابل با واحد مشاوره فامو در تماس باشید.
                </p>
                <div class="flex flex-wrap items-center justify-center gap-3 sm:gap-4">
                    <a href="/contact/#consultation"
                        class="inline-flex items-center gap-2 famo-btn-cream font-bold px-8 py-3 rounded-full transition duration-300 shadow-lg">
                        <i class="icon icon--sm" data-lucide="phone" aria-hidden="true"></i>
                        درخواست مشاوره
                    </a>
                    <a href="tel:+989014402300" dir="ltr"
                        class="inline-flex items-center gap-2 famo-btn-outline-cream font-bold px-7 py-3 rounded-full transition duration-300">
                        0901-440-2300
                    </a>
                </div>
            </div>
        </div>
    </section>
</main>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
</body>
</html>
