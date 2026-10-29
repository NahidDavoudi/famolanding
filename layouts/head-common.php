<!-- Shared head: included by every page inside its own <head>. Requires $base. -->
<?php require_once __DIR__ . '/../config.php'; ?>
<meta name="color-scheme" content="light">
<?= famo_config_script() ?>
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-4NHPK2145Z"></script>
<script>
    window.dataLayer = window.dataLayer || [];
    function gtag() { dataLayer.push(arguments); }
    gtag('js', new Date());
    gtag('config', 'G-4NHPK2145Z');
</script>

<!-- Structured Data -->
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "EducationalOrganization",
    "name": "آموزشگاه فامو",
    "alternateName": "Famo Academy",
    "description": "آموزشگاه فامو نهاد آموزشی پیشرو در بابل با هفت سال سابقه در مشاوره تخصصی، آموزش مفهومی و آمادگی کنکور و تیزهوشان برای مقاطع راهنمایی و دبیرستان.",
    "url": <?= json_encode(famo_public_url(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?>,
    "logo": "<?php echo famo_asset('images/logo.png', $base . '../shared/images/logo.png'); ?>",
    "image": "<?php echo famo_asset('images/logo.png', $base . '../shared/images/logo.png'); ?>",
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

<script src="<?php echo famo_asset('js/libs/gsap.min.js', $base . '../shared/js/libs/gsap.min.js'); ?>"></script>
<script src="<?php echo famo_asset('js/libs/ScrollTrigger.min.js', $base . '../shared/js/libs/ScrollTrigger.min.js'); ?>"></script>
<script src="<?php echo famo_asset('js/libs/ScrollToPlugin.min.js', $base . '../shared/js/libs/ScrollToPlugin.min.js'); ?>"></script>
<script src="<?php echo famo_asset('js/libs/swiper-bundle.min.js', $base . '../shared/js/libs/swiper-bundle.min.js'); ?>"></script>
<script src="<?php echo famo_asset('js/libs/lucide.min.js', $base . '../shared/js/libs/lucide.min.js'); ?>"></script>
<script src="<?php echo famo_asset('js/lucide-adapter.js', $base . '../shared/js/lucide-adapter.js'); ?>"></script>
<script src="<?php echo $base; ?>assets/js/animation.js"></script>

<!-- Font Vazir -->
<link rel="stylesheet" href="<?php echo famo_asset('css/output.css', $base . '../shared/css/output.css'); ?>">
<link rel="stylesheet" href="<?php echo $base; ?>assets/css/icons.css">
<link rel="stylesheet" href="<?php echo famo_asset('css/libs/swiper-bundle.min.css', $base . '../shared/css/libs/swiper-bundle.min.css'); ?>">
<link rel="stylesheet" href="<?php echo $base; ?>assets/css/style.css">
<link rel="stylesheet" href="<?php echo famo_asset('css/fonts.css', $base . '../shared/css/fonts.css'); ?>">

<!-- Favicon Multi-size -->
<link rel="icon" type="image/png" sizes="16x16" href="<?php echo famo_asset('images/favicon-16.png', $base . '../shared/images/favicon-16.png'); ?>">
<link rel="icon" type="image/png" sizes="32x32" href="<?php echo famo_asset('images/favicon-32.png', $base . '../shared/images/favicon-32.png'); ?>">
<link rel="icon" type="image/png" sizes="96x96" href="<?php echo famo_asset('images/favicon-96.png', $base . '../shared/images/favicon-96.png'); ?>">