<?php
require_once __DIR__ . '/../config.php';
$base = $base ?? '';

$defaultLibs = [
    'gsap'           => true,
    'scroll_trigger' => true,
    'scroll_to'      => true,
    'swiper'         => true,
    'lucide'         => true,
    'animation'      => true,
];
$libs = array_merge($defaultLibs, is_array($page_libs ?? null) ? $page_libs : []);

// Unified SEO metadata defaults (overridden per page before including head-common.php)
$siteUrl = famo_public_site_url();
$seoTitle = isset($pageTitle) && is_string($pageTitle) && trim($pageTitle) !== ''
    ? trim($pageTitle)
    : 'آموزشگاه فامو بابل | آموزش خصوصی و هوشمند';
$seoDescription = isset($metaDescription) && is_string($metaDescription) && trim($metaDescription) !== ''
    ? trim($metaDescription)
    : 'آموزشگاه فامو در بابل؛ ارائه کلاس‌های خصوصی، آزمون‌های شخصی‌سازی‌شده و پیگیری هوشمند عملکرد تحصیلی دانش‌آموزان.';
$seoCanonical = isset($canonicalUrl) && is_string($canonicalUrl) && trim($canonicalUrl) !== ''
    ? trim($canonicalUrl)
    : famo_canonical_url(isset($canonicalPath) && is_string($canonicalPath) ? $canonicalPath : '');
$seoRobots = isset($robots) && is_string($robots) && trim($robots) !== ''
    ? trim($robots)
    : 'index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1';
$seoOgType = isset($ogType) && is_string($ogType) && trim($ogType) !== ''
    ? trim($ogType)
    : 'website';
$seoOgImage = isset($ogImage) && is_string($ogImage) && trim($ogImage) !== ''
    ? trim($ogImage)
    : $siteUrl . '/assets/images/poster_hero.webp';
$seoOgImageAlt = isset($ogImageAlt) && is_string($ogImageAlt) && trim($ogImageAlt) !== ''
    ? trim($ogImageAlt)
    : $seoTitle;
$seoPageType = isset($webPageType) && is_string($webPageType) && trim($webPageType) !== ''
    ? trim($webPageType)
    : 'WebPage';

$logoAssetUrl = $siteUrl . '/assets/images/logo.png';

// Build connected Schema.org @graph (Organization + WebSite + WebPage + BreadcrumbList + page-specific entities)
$orgId = $siteUrl . '/#organization';
$websiteId = $siteUrl . '/#website';
$webpageId = $seoCanonical . '#webpage';
$breadcrumbId = $seoCanonical . '#breadcrumb';

$organizationSchema = [
    '@type'         => ['EducationalOrganization', 'Organization'],
    '@id'           => $orgId,
    'name'          => 'آموزشگاه فامو',
    'alternateName' => 'Famo Academy',
    'description'   => 'آموزشگاه فامو در بابل؛ ارائه کلاس‌های خصوصی، آزمون‌های شخصی‌سازی‌شده، رفع اشکال انفرادی و پیگیری هوشمند عملکرد تحصیلی دانش‌آموزان از ابتدایی تا کنکور و تیزهوشان.',
    'url'           => $siteUrl . '/',
    'logo'          => [
        '@type' => 'ImageObject',
        'url'   => $logoAssetUrl,
    ],
    'image'         => $seoOgImage,
    'email'         => 'info@famoacademy.ir',
    'telephone'     => '+98-901-440-2300',
    'contactPoint'  => [
        [
            '@type'             => 'ContactPoint',
            'telephone'         => '+98-901-440-2300',
            'contactType'       => 'customer service',
            'areaServed'        => 'IR',
            'availableLanguage' => 'fa',
        ],
        [
            '@type'             => 'ContactPoint',
            'telephone'         => '+98-999-904-1429',
            'contactType'       => 'customer service',
            'areaServed'        => 'IR',
            'availableLanguage' => 'fa',
        ],
        [
            '@type'             => 'ContactPoint',
            'telephone'         => '+98-990-681-7387',
            'contactType'       => 'customer service',
            'areaServed'        => 'IR',
            'availableLanguage' => 'fa',
        ],
    ],
    'address'       => [
        '@type'           => 'PostalAddress',
        'addressLocality' => 'بابل',
        'addressRegion'   => 'مازندران',
        'addressCountry'  => 'IR',
        'streetAddress'   => 'میدان باغ فردوس، جنب بیمارستان بابل کلینیک، مدرسه بهارستان',
    ],
    'founder'       => [
        '@type' => 'Person',
        'name'  => 'دکتر محمد موسی زاده موسوی',
    ],
    'areaServed'    => [
        '@type' => 'City',
        'name'  => 'بابل',
    ],
    'sameAs'        => [
        'https://t.me/famoacademy',
    ],
];

if (isset($organizationExtra) && is_array($organizationExtra)) {
    $organizationSchema = array_merge($organizationSchema, $organizationExtra);
}

$websiteSchema = [
    '@type'      => 'WebSite',
    '@id'        => $websiteId,
    'url'        => $siteUrl . '/',
    'name'       => 'آموزشگاه فامو',
    'inLanguage' => 'fa-IR',
    'publisher'  => ['@id' => $orgId],
];

$webPageSchema = [
    '@type'       => $seoPageType,
    '@id'         => $webpageId,
    'url'         => $seoCanonical,
    'name'        => $seoTitle,
    'description' => $seoDescription,
    'inLanguage'  => 'fa-IR',
    'isPartOf'    => ['@id' => $websiteId],
    'about'       => ['@id' => $orgId],
];

$graph = [$organizationSchema, $websiteSchema];

$breadcrumbItems = [];
if (isset($breadcrumbs) && is_array($breadcrumbs) && count($breadcrumbs) > 0) {
    $pos = 1;
    foreach ($breadcrumbs as $crumb) {
        if (!is_array($crumb) || empty($crumb['name'])) {
            continue;
        }
        $item = [
            '@type'    => 'ListItem',
            'position' => $pos++,
            'name'     => (string) $crumb['name'],
        ];
        if (!empty($crumb['url'])) {
            $item['item'] = (string) $crumb['url'];
        }
        $breadcrumbItems[] = $item;
    }
}

if (count($breadcrumbItems) > 0) {
    $webPageSchema['breadcrumb'] = ['@id' => $breadcrumbId];
    $graph[] = $webPageSchema;
    $graph[] = [
        '@type'           => 'BreadcrumbList',
        '@id'             => $breadcrumbId,
        'itemListElement' => $breadcrumbItems,
    ];
} else {
    $graph[] = $webPageSchema;
}

if (isset($extraSchemas) && is_array($extraSchemas)) {
    foreach ($extraSchemas as $schemaEntity) {
        if (is_array($schemaEntity) && !empty($schemaEntity)) {
            $graph[] = $schemaEntity;
        }
    }
}

$jsonLdGraph = json_encode(
    [
        '@context' => 'https://schema.org',
        '@graph'   => $graph,
    ],
    JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
);
if ($jsonLdGraph === false) {
    $jsonLdGraph = '{}';
}

$fontRegularWoff2 = htmlspecialchars(famo_local_asset('assets/fonts/Vazirmatn-Regular.woff2', $base), ENT_QUOTES, 'UTF-8');
$fontRegularTtf   = htmlspecialchars(famo_local_asset('assets/fonts/Vazirmatn-Regular.ttf', $base), ENT_QUOTES, 'UTF-8');
$fontMediumWoff2  = htmlspecialchars(famo_local_asset('assets/fonts/Vazirmatn-Medium.woff2', $base), ENT_QUOTES, 'UTF-8');
$fontMediumTtf    = htmlspecialchars(famo_local_asset('assets/fonts/Vazirmatn-Medium.ttf', $base), ENT_QUOTES, 'UTF-8');
$fontBoldWoff2    = htmlspecialchars(famo_local_asset('assets/fonts/Vazirmatn-Bold.woff2', $base), ENT_QUOTES, 'UTF-8');
$fontBoldTtf      = htmlspecialchars(famo_local_asset('assets/fonts/Vazirmatn-Bold.ttf', $base), ENT_QUOTES, 'UTF-8');
?>
<!-- Shared head: included by every page inside its own <head>. Requires $base. Optional SEO vars & $page_libs opt-out array. -->
<title><?= htmlspecialchars($seoTitle, ENT_QUOTES, 'UTF-8') ?></title>
<meta name="description" content="<?= htmlspecialchars($seoDescription, ENT_QUOTES, 'UTF-8') ?>">
<meta name="robots" content="<?= htmlspecialchars($seoRobots, ENT_QUOTES, 'UTF-8') ?>">
<meta name="color-scheme" content="light">
<meta name="theme-color" content="#445D84">
<link rel="canonical" href="<?= htmlspecialchars($seoCanonical, ENT_QUOTES, 'UTF-8') ?>">

<!-- Open Graph / Social Sharing -->
<meta property="og:locale" content="fa_IR">
<meta property="og:site_name" content="آموزشگاه فامو">
<meta property="og:type" content="<?= htmlspecialchars($seoOgType, ENT_QUOTES, 'UTF-8') ?>">
<meta property="og:title" content="<?= htmlspecialchars($seoTitle, ENT_QUOTES, 'UTF-8') ?>">
<meta property="og:description" content="<?= htmlspecialchars($seoDescription, ENT_QUOTES, 'UTF-8') ?>">
<meta property="og:url" content="<?= htmlspecialchars($seoCanonical, ENT_QUOTES, 'UTF-8') ?>">
<meta property="og:image" content="<?= htmlspecialchars($seoOgImage, ENT_QUOTES, 'UTF-8') ?>">
<meta property="og:image:alt" content="<?= htmlspecialchars($seoOgImageAlt, ENT_QUOTES, 'UTF-8') ?>">

<!-- Twitter / X Card -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?= htmlspecialchars($seoTitle, ENT_QUOTES, 'UTF-8') ?>">
<meta name="twitter:description" content="<?= htmlspecialchars($seoDescription, ENT_QUOTES, 'UTF-8') ?>">
<meta name="twitter:image" content="<?= htmlspecialchars($seoOgImage, ENT_QUOTES, 'UTF-8') ?>">
<meta name="twitter:image:alt" content="<?= htmlspecialchars($seoOgImageAlt, ENT_QUOTES, 'UTF-8') ?>">

<!-- Font Preload (Regular & Bold WOFF2 subset) -->
<link rel="preload" as="font" type="font/woff2" href="<?= $fontRegularWoff2 ?>" crossorigin>
<link rel="preload" as="font" type="font/woff2" href="<?= $fontBoldWoff2 ?>" crossorigin>

<!-- Inline Font Declarations (early discovery before stylesheets, WOFF2 subset with TTF fallback) -->
<style>
@font-face {
    font-family: 'Vazirmatn';
    src: url('<?= $fontRegularWoff2 ?>') format('woff2'),
         url('<?= $fontRegularTtf ?>') format('truetype');
    font-weight: 400;
    font-style: normal;
    font-display: swap;
}
@font-face {
    font-family: 'Vazirmatn';
    src: url('<?= $fontMediumWoff2 ?>') format('woff2'),
         url('<?= $fontMediumTtf ?>') format('truetype');
    font-weight: 500;
    font-style: normal;
    font-display: swap;
}
@font-face {
    font-family: 'Vazirmatn';
    src: url('<?= $fontBoldWoff2 ?>') format('woff2'),
         url('<?= $fontBoldTtf ?>') format('truetype');
    font-weight: 700;
    font-style: normal;
    font-display: swap;
}
@font-face {
    font-family: 'Vazirmatn Fallback';
    src: local('Tahoma'), local('Segoe UI'), local('Arial');
    size-adjust: 102%;
    ascent-override: 98%;
    descent-override: 32%;
    line-gap-override: 0%;
}
</style>

<!-- Stylesheets: tokens -> Tailwind output -> icons -> conditional Swiper -> local style -->
<link rel="stylesheet" href="<?= htmlspecialchars(famo_local_asset('assets/css/tokens.css', $base), ENT_QUOTES, 'UTF-8') ?>">
<link rel="stylesheet" href="<?= htmlspecialchars(famo_local_asset('assets/css/output.css', $base), ENT_QUOTES, 'UTF-8') ?>">
<link rel="stylesheet" href="<?= htmlspecialchars(famo_local_asset('assets/css/icons.css', $base), ENT_QUOTES, 'UTF-8') ?>">
<?php if (!empty($libs['swiper'])): ?>
<link rel="stylesheet" href="<?= htmlspecialchars(famo_local_asset('assets/css/libs/swiper-bundle.min.css', $base), ENT_QUOTES, 'UTF-8') ?>">
<?php endif; ?>
<link rel="stylesheet" href="<?= htmlspecialchars(famo_local_asset('assets/css/style.css', $base), ENT_QUOTES, 'UTF-8') ?>">

<!-- Runtime config must remain inline before deferred scripts -->
<?= famo_config_script() ?>

<!-- Structured Data (@graph: Organization, WebSite, WebPage, BreadcrumbList, page entities) -->
<script type="application/ld+json"><?= $jsonLdGraph ?></script>

<!-- Deferred Scripts (order preserved; gated by $page_libs) -->
<?php if (!empty($libs['gsap'])): ?>
<script defer src="<?= htmlspecialchars(famo_local_asset('assets/js/libs/gsap.min.js', $base), ENT_QUOTES, 'UTF-8') ?>"></script>
<?php if (!empty($libs['scroll_trigger'])): ?>
<script defer src="<?= htmlspecialchars(famo_local_asset('assets/js/libs/ScrollTrigger.min.js', $base), ENT_QUOTES, 'UTF-8') ?>"></script>
<?php endif; ?>
<?php if (!empty($libs['scroll_to'])): ?>
<script defer src="<?= htmlspecialchars(famo_local_asset('assets/js/libs/ScrollToPlugin.min.js', $base), ENT_QUOTES, 'UTF-8') ?>"></script>
<?php endif; ?>
<?php endif; ?>
<?php if (!empty($libs['swiper'])): ?>
<script defer src="<?= htmlspecialchars(famo_local_asset('assets/js/libs/swiper-bundle.min.js', $base), ENT_QUOTES, 'UTF-8') ?>"></script>
<?php endif; ?>
<?php if (!empty($libs['lucide'])): ?>
<script defer src="<?= htmlspecialchars(famo_local_asset('assets/js/libs/lucide.min.js', $base), ENT_QUOTES, 'UTF-8') ?>"></script>
<script defer src="<?= htmlspecialchars(famo_local_asset('assets/js/lucide-adapter.js', $base), ENT_QUOTES, 'UTF-8') ?>"></script>
<?php endif; ?>
<?php if (!empty($libs['animation'])): ?>
<script defer src="<?= htmlspecialchars(famo_local_asset('assets/js/animation.js', $base), ENT_QUOTES, 'UTF-8') ?>"></script>
<?php endif; ?>
<script defer src="<?= htmlspecialchars(famo_local_asset('assets/js/nav.js', $base), ENT_QUOTES, 'UTF-8') ?>"></script>

<!-- Favicon Multi-size -->
<link rel="icon" type="image/png" sizes="16x16" href="<?= htmlspecialchars(famo_local_asset('assets/images/favicon-16.png', $base), ENT_QUOTES, 'UTF-8') ?>">
<link rel="icon" type="image/png" sizes="32x32" href="<?= htmlspecialchars(famo_local_asset('assets/images/favicon-32.png', $base), ENT_QUOTES, 'UTF-8') ?>">
<link rel="icon" type="image/png" sizes="96x96" href="<?= htmlspecialchars(famo_local_asset('assets/images/favicon-96.png', $base), ENT_QUOTES, 'UTF-8') ?>">

<script async src="https://www.googletagmanager.com/gtm.js?id=G-4NHPK2145Z"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
</script>