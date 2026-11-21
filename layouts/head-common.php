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

$fontRegularWoff2 = htmlspecialchars(famo_asset('fonts/Vazirmatn-Regular.subset.woff2', $base . '../shared/fonts/Vazirmatn-Regular.subset.woff2'), ENT_QUOTES, 'UTF-8');
$fontRegularTtf   = htmlspecialchars(famo_asset('fonts/Vazirmatn-Regular.ttf', $base . '../shared/fonts/Vazirmatn-Regular.ttf'), ENT_QUOTES, 'UTF-8');
$fontMediumWoff2  = htmlspecialchars(famo_asset('fonts/Vazirmatn-Medium.subset.woff2', $base . '../shared/fonts/Vazirmatn-Medium.subset.woff2'), ENT_QUOTES, 'UTF-8');
$fontMediumTtf    = htmlspecialchars(famo_asset('fonts/Vazirmatn-Medium.ttf', $base . '../shared/fonts/Vazirmatn-Medium.ttf'), ENT_QUOTES, 'UTF-8');
$fontBoldWoff2    = htmlspecialchars(famo_asset('fonts/Vazirmatn-Bold.subset.woff2', $base . '../shared/fonts/Vazirmatn-Bold.subset.woff2'), ENT_QUOTES, 'UTF-8');
$fontBoldTtf      = htmlspecialchars(famo_asset('fonts/Vazirmatn-Bold.ttf', $base . '../shared/fonts/Vazirmatn-Bold.ttf'), ENT_QUOTES, 'UTF-8');
?>
<!-- Shared head: included by every page inside its own <head>. Requires $base. Optional $page_libs opt-out array. -->
<meta name="color-scheme" content="light">

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
<link rel="stylesheet" href="<?= htmlspecialchars(famo_asset('css/tokens.css', $base . '../shared/css/tokens.css'), ENT_QUOTES, 'UTF-8') ?>">
<link rel="stylesheet" href="<?= htmlspecialchars(famo_asset('css/output.css', $base . '../shared/css/output.css'), ENT_QUOTES, 'UTF-8') ?>">
<link rel="stylesheet" href="<?= htmlspecialchars(famo_local_asset('assets/css/icons.css', $base), ENT_QUOTES, 'UTF-8') ?>">
<?php if (!empty($libs['swiper'])): ?>
<link rel="stylesheet" href="<?= htmlspecialchars(famo_asset('css/libs/swiper-bundle.min.css', $base . '../shared/css/libs/swiper-bundle.min.css'), ENT_QUOTES, 'UTF-8') ?>">
<?php endif; ?>
<link rel="stylesheet" href="<?= htmlspecialchars(famo_local_asset('assets/css/style.css', $base), ENT_QUOTES, 'UTF-8') ?>">

<!-- Runtime config must remain inline before deferred scripts -->
<?= famo_config_script() ?>

<!-- Structured Data -->
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "EducationalOrganization",
    "name": "آموزشگاه فامو",
    "alternateName": "Famo Academy",
    "description": "آموزشگاه فامو نهاد آموزشی پیشرو در بابل با هفت سال سابقه در مشاوره تخصصی، آموزش مفهومی و آمادگی کنکور و تیزهوشان برای مقاطع راهنمایی و دبیرستان.",
    "url": <?= json_encode(famo_public_url(), JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>,
    "logo": "<?= htmlspecialchars(famo_asset('images/logo.png', $base . '../shared/images/logo.png'), ENT_QUOTES, 'UTF-8') ?>",
    "image": "<?= htmlspecialchars(famo_asset('images/logo.png', $base . '../shared/images/logo.png'), ENT_QUOTES, 'UTF-8') ?>",
    "email": "info@famoacademy.ir",
    "telephone": "+98-11-32221234",
    "address": {
        "@type": "PostalAddress",
        "addressLocality": "بابل",
        "addressRegion": "مازندران",
        "addressCountry": "IR",
        "streetAddress": "میدان باغ فردوس، جنب بیمارستان بابل کلینیک، مدرسه بهارستان"
    },
    "founder": {
        "@type": "Person",
        "name": "دکتر محمد موسی زاده موسوی"
    },
    "areaServed": {
        "@type": "City",
        "name": "بابل"
    }
}
</script>

<!-- Deferred Scripts (order preserved; gated by $page_libs) -->
<?php if (!empty($libs['gsap'])): ?>
<script defer src="<?= htmlspecialchars(famo_asset('js/libs/gsap.min.js', $base . '../shared/js/libs/gsap.min.js'), ENT_QUOTES, 'UTF-8') ?>"></script>
<?php if (!empty($libs['scroll_trigger'])): ?>
<script defer src="<?= htmlspecialchars(famo_asset('js/libs/ScrollTrigger.min.js', $base . '../shared/js/libs/ScrollTrigger.min.js'), ENT_QUOTES, 'UTF-8') ?>"></script>
<?php endif; ?>
<?php if (!empty($libs['scroll_to'])): ?>
<script defer src="<?= htmlspecialchars(famo_asset('js/libs/ScrollToPlugin.min.js', $base . '../shared/js/libs/ScrollToPlugin.min.js'), ENT_QUOTES, 'UTF-8') ?>"></script>
<?php endif; ?>
<?php endif; ?>
<?php if (!empty($libs['swiper'])): ?>
<script defer src="<?= htmlspecialchars(famo_asset('js/libs/swiper-bundle.min.js', $base . '../shared/js/libs/swiper-bundle.min.js'), ENT_QUOTES, 'UTF-8') ?>"></script>
<?php endif; ?>
<?php if (!empty($libs['lucide'])): ?>
<script defer src="<?= htmlspecialchars(famo_asset('js/libs/lucide.min.js', $base . '../shared/js/libs/lucide.min.js'), ENT_QUOTES, 'UTF-8') ?>"></script>
<script defer src="<?= htmlspecialchars(famo_asset('js/lucide-adapter.js', $base . '../shared/js/lucide-adapter.js'), ENT_QUOTES, 'UTF-8') ?>"></script>
<?php endif; ?>
<?php if (!empty($libs['animation'])): ?>
<script defer src="<?= htmlspecialchars(famo_local_asset('assets/js/animation.js', $base), ENT_QUOTES, 'UTF-8') ?>"></script>
<?php endif; ?>

<!-- Favicon Multi-size -->
<link rel="icon" type="image/png" sizes="16x16" href="<?= htmlspecialchars(famo_asset('images/favicon-16.png', $base . '../shared/images/favicon-16.png'), ENT_QUOTES, 'UTF-8') ?>">
<link rel="icon" type="image/png" sizes="32x32" href="<?= htmlspecialchars(famo_asset('images/favicon-32.png', $base . '../shared/images/favicon-32.png'), ENT_QUOTES, 'UTF-8') ?>">
<link rel="icon" type="image/png" sizes="96x96" href="<?= htmlspecialchars(famo_asset('images/favicon-96.png', $base . '../shared/images/favicon-96.png'), ENT_QUOTES, 'UTF-8') ?>">
