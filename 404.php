<?php
declare(strict_types=1);
require_once __DIR__ . '/config.php';
http_response_code(404);

$base = '/';
$baseEsc = htmlspecialchars($base, ENT_QUOTES, 'UTF-8');
$pageTitle = 'صفحه مورد نظر یافت نشد (۴۰۴) | آموزشگاه فامو بابل';
$metaDescription = 'صفحه‌ای که به دنبال آن هستید در سایت آموزشگاه فامو یافت نشد. می‌توانید به صفحه اصلی، دوره‌ها یا خدمات آموزشی مراجعه کنید.';
$canonicalPath = '';
$robots = 'noindex, follow';
$webPageType = 'WebPage';
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
    <?php include __DIR__ . '/layouts/head-common.php'; ?>
</head>
<body class="font-family text-gray-800 bg-[#f9f7f3]" data-static-nav="1">

<?php include __DIR__ . '/layouts/header.php'; ?>

<main id="main-content">
    <section class="pt-32 sm:pt-36 pb-20 px-4 min-h-[70vh] flex items-center">
        <div class="container mx-auto max-w-3xl text-center">
            <div class="bg-white rounded-3xl border border-[#E2D9C6] shadow-xl p-8 sm:p-12">
                <p class="inline-flex items-center justify-center px-4 py-1.5 rounded-full bg-[#EFE9DD] text-[#445D84] font-bold text-sm mb-5">
                    خطای ۴۰۴
                </p>
                <h1 class="text-3xl sm:text-4xl font-bold text-[#445D84] mb-4">
                    صفحه مورد نظر یافت نشد
                </h1>
                <p class="text-gray-600 text-base sm:text-lg leading-relaxed max-w-xl mx-auto mb-8">
                    نشانی واردشده ممکن است تغییر کرده باشد یا دیگر در دسترس نباشد. می‌توانید از مسیرهای زیر به صفحات اصلی آموزشگاه فامو دسترسی پیدا کنید.
                </p>

                <div class="flex flex-wrap items-center justify-center gap-3 sm:gap-4">
                    <a href="/"
                        class="inline-flex items-center gap-2 bg-[#445D84] text-white font-bold px-7 py-3 rounded-full hover:bg-[#344868] transition duration-300 shadow-md">
                        <i class="icon icon--sm" data-lucide="house" aria-hidden="true"></i>
                        بازگشت به صفحه اصلی
                    </a>
                    <a href="/courses/"
                        class="inline-flex items-center gap-2 border-2 border-[#445D84] text-[#445D84] font-bold px-7 py-3 rounded-full hover:bg-[#445D84] hover:text-white transition duration-300">
                        <i class="icon icon--sm" data-lucide="book-open" aria-hidden="true"></i>
                        مشاهده دوره‌ها
                    </a>
                    <a href="/services/"
                        class="inline-flex items-center gap-2 bg-[#EFE9DD] text-[#445D84] font-bold px-6 py-3 rounded-full hover:bg-[#E2D9C6] transition duration-300">
                        خدمات آموزشی
                    </a>
                    <a href="/contact/"
                        class="inline-flex items-center gap-2 bg-[#EFE9DD] text-[#445D84] font-bold px-6 py-3 rounded-full hover:bg-[#E2D9C6] transition duration-300">
                        تماس با ما
                    </a>
                </div>
            </div>
        </div>
    </section>
</main>

<?php include __DIR__ . '/layouts/footer.php'; ?>
</body>
</html>
