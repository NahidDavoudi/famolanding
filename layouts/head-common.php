<?php
require_once __DIR__ . '/../config.php';
$base = $base ?? '';
?>
<!-- Shared head: included by every page inside its own <head>. Requires $base. -->
<meta name="color-scheme" content="light">
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

<script src="<?= htmlspecialchars(famo_asset('js/libs/gsap.min.js', $base . '../shared/js/libs/gsap.min.js'), ENT_QUOTES, 'UTF-8') ?>"></script>
<script src="<?= htmlspecialchars(famo_asset('js/libs/ScrollTrigger.min.js', $base . '../shared/js/libs/ScrollTrigger.min.js'), ENT_QUOTES, 'UTF-8') ?>"></script>
<script src="<?= htmlspecialchars(famo_asset('js/libs/ScrollToPlugin.min.js', $base . '../shared/js/libs/ScrollToPlugin.min.js'), ENT_QUOTES, 'UTF-8') ?>"></script>
<script src="<?= htmlspecialchars(famo_asset('js/libs/swiper-bundle.min.js', $base . '../shared/js/libs/swiper-bundle.min.js'), ENT_QUOTES, 'UTF-8') ?>"></script>
<script src="<?= htmlspecialchars(famo_asset('js/libs/lucide.min.js', $base . '../shared/js/libs/lucide.min.js'), ENT_QUOTES, 'UTF-8') ?>"></script>
<script src="<?= htmlspecialchars(famo_asset('js/lucide-adapter.js', $base . '../shared/js/lucide-adapter.js'), ENT_QUOTES, 'UTF-8') ?>"></script>
<script src="<?= htmlspecialchars($base, ENT_QUOTES, 'UTF-8') ?>assets/js/animation.js"></script>

<!-- Font Vazir -->
<link rel="stylesheet" href="<?= htmlspecialchars(famo_asset('css/tokens.css', $base . '../shared/css/tokens.css'), ENT_QUOTES, 'UTF-8') ?>">
<link rel="stylesheet" href="<?= htmlspecialchars(famo_asset('css/output.css', $base . '../shared/css/output.css'), ENT_QUOTES, 'UTF-8') ?>">
<link rel="stylesheet" href="<?= htmlspecialchars($base, ENT_QUOTES, 'UTF-8') ?>assets/css/icons.css">
<link rel="stylesheet" href="<?= htmlspecialchars(famo_asset('css/libs/swiper-bundle.min.css', $base . '../shared/css/libs/swiper-bundle.min.css'), ENT_QUOTES, 'UTF-8') ?>">
<link rel="stylesheet" href="<?= htmlspecialchars($base, ENT_QUOTES, 'UTF-8') ?>assets/css/style.css">
<link rel="stylesheet" href="<?= htmlspecialchars(famo_asset('css/fonts.css', $base . '../shared/css/fonts.css'), ENT_QUOTES, 'UTF-8') ?>">

<!-- Favicon Multi-size -->
<link rel="icon" type="image/png" sizes="16x16" href="<?= htmlspecialchars(famo_asset('images/favicon-16.png', $base . '../shared/images/favicon-16.png'), ENT_QUOTES, 'UTF-8') ?>">
<link rel="icon" type="image/png" sizes="32x32" href="<?= htmlspecialchars(famo_asset('images/favicon-32.png', $base . '../shared/images/favicon-32.png'), ENT_QUOTES, 'UTF-8') ?>">
<link rel="icon" type="image/png" sizes="96x96" href="<?= htmlspecialchars(famo_asset('images/favicon-96.png', $base . '../shared/images/favicon-96.png'), ENT_QUOTES, 'UTF-8') ?>">
