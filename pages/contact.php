<?php
declare(strict_types=1);
require_once __DIR__ . '/../config.php';
$base = '/';
$baseEsc = htmlspecialchars($base, ENT_QUOTES, 'UTF-8');
$siteUrl = famo_public_site_url();
$contactPageUrl = famo_canonical_url('contact/');
$loginUrlRaw = famo_login_url();
$loginUrlEsc = htmlspecialchars(($loginUrlRaw !== '' ? $loginUrlRaw . '/' : 'https://login.famoacademy.ir/'), ENT_QUOTES, 'UTF-8');

$pageTitle = 'آدرس و تماس آموزشگاه فامو بابل | میدان باغ فردوس و رزرو مشاوره';
$metaDescription = 'اطلاعات تماس، نشانی و موقعیت آموزشگاه فامو در میدان باغ فردوس بابل؛ راه‌های ارتباطی مستقیم برای رزرو مشاوره و ثبت‌نام دوره‌های آموزشی.';
$canonicalPath = 'contact/';
$webPageType = 'ContactPage';
$breadcrumbs = [
    ['name' => 'خانه', 'url' => famo_canonical_url('')],
    ['name' => 'تماس با ما', 'url' => $contactPageUrl],
];

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
<body class="font-family text-gray-800 bg-[#f9f7f3]" data-static-nav="1">

<?php include __DIR__ . '/../layouts/header.php'; ?>

<main id="main-content">
    <!-- Hero -->
    <section class="famo-page-hero gradient-bg text-white text-center">
        <div class="container mx-auto max-w-4xl">
            <p class="famo-eyebrow">آموزشگاه فامو | بابل</p>
            <h1 class="text-3xl sm:text-4xl font-bold mb-5 text-white leading-snug">
                تماس با آموزشگاه فامو در بابل
            </h1>
            <p class="text-white/90 text-base sm:text-lg leading-relaxed max-w-2xl mx-auto">
                برای دریافت اطلاعات درباره کلاس‌های خصوصی، برنامه‌های آموزشی، خدمات مشاوره یا هماهنگی شروع مسیر تحصیلی دانش‌آموز، از راه‌های ارتباطی زیر با ما در تماس باشید.
            </p>
        </div>
    </section>

    <!-- Verified Contact Info + Map -->
    <section class="py-14 sm:py-16 px-4">
        <div class="container mx-auto max-w-6xl space-y-10">
            <div class="famo-contact-grid">
                <!-- Contact Details Card -->
                <div class="famo-detail-block">
                    <h2 class="text-2xl font-bold text-[#445D84] mb-6 flex items-center gap-2.5">
                        <i class="icon icon--lg text-[#8B786D]" data-lucide="id-card" aria-hidden="true"></i>
                        اطلاعات تماس رسمی
                    </h2>

                    <address class="famo-contact-info-list">
                        <!-- Phone -->
                        <div class="famo-contact-info-item">
                            <div class="famo-contact-info-icon">
                                <i class="icon" data-lucide="phone" aria-hidden="true"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-base text-[#445D84] mb-1.5">شماره تماس</h3>
                                <div class="space-y-1 text-gray-700 font-medium">
                                    <p><a href="tel:+989014402300" dir="ltr" class="hover:text-[#445D84] transition">0901-440-2300</a></p>
                                    <p><a href="tel:+989999041429" dir="ltr" class="hover:text-[#445D84] transition">0999-904-1429</a></p>
                                    <p><a href="tel:+989906817387" dir="ltr" class="hover:text-[#445D84] transition">0990-681-7387</a></p>
                                </div>
                            </div>
                        </div>

                        <!-- Address -->
                        <div class="famo-contact-info-item">
                            <div class="famo-contact-info-icon">
                                <i class="icon" data-lucide="map-pin" aria-hidden="true"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-base text-[#445D84] mb-1">نشانی آموزشگاه</h3>
                                <p class="text-gray-700 leading-relaxed">بابل، میدان باغ فردوس</p>
                            </div>
                        </div>

                        <!-- Email -->
                        <div class="famo-contact-info-item">
                            <div class="famo-contact-info-icon">
                                <i class="icon" data-lucide="mail" aria-hidden="true"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-base text-[#445D84] mb-1">ایمیل رسمی</h3>
                                <p class="text-gray-700"><a href="mailto:info@famoacademy.ir" class="hover:text-[#445D84] transition">info@famoacademy.ir</a></p>
                            </div>
                        </div>

                        <!-- Social / Messaging Channels -->
                        <div class="famo-contact-info-item">
                            <div class="famo-contact-info-icon">
                                <i class="icon" data-lucide="send" aria-hidden="true"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-base text-[#445D84] mb-1.5">شبکه‌های ارتباطی</h3>
                                <div class="flex flex-wrap gap-3 text-sm font-bold">
                                    <a href="https://t.me/famoacademy" target="_blank" rel="noopener noreferrer"
                                        class="inline-flex items-center gap-1.5 text-[#445D84] hover:underline">
                                        <span dir="ltr">@famoacademy</span> (تلگرام)
                                    </a>
                                    <span class="text-gray-300" aria-hidden="true">|</span>
                                    <a href="https://wa.me/989014402300" target="_blank" rel="noopener noreferrer"
                                        class="inline-flex items-center gap-1.5 text-[#445D84] hover:underline">
                                        واتساپ آموزشگاه
                                    </a>
                                </div>
                            </div>
                        </div>
                    </address>
                </div>

                <!-- Location / Map Card -->
                <div class="famo-detail-block flex flex-col justify-between">
                    <div>
                        <h2 class="text-2xl font-bold text-[#445D84] mb-3 flex items-center gap-2.5">
                            <i class="icon icon--lg text-[#8B786D]" data-lucide="map-pin" aria-hidden="true"></i>
                            موقعیت فامو روی نقشه
                        </h2>
                        <p class="text-gray-600 leading-relaxed mb-6">
                            برای مراجعه حضوری، می‌توانید موقعیت آموزشگاه فامو در بابل را از طریق نقشه مشاهده کنید و پیش از مراجعه با شماره‌های آموزشگاه هماهنگ شوید.
                        </p>

                        <div class="famo-map-box">
                            <div class="w-16 h-16 rounded-full bg-[#445D84] text-[#E2D9C6] mx-auto mb-4 flex items-center justify-center shadow-md">
                                <i class="icon icon--xl" data-lucide="map-pin" aria-hidden="true"></i>
                            </div>
                            <p class="text-lg font-bold text-[#445D84] mb-1">آموزشگاه فامو بابل</p>
                            <p class="text-gray-600 text-sm mb-5">بابل، میدان باغ فردوس</p>
                            <a href="https://maps.google.com/?q=بابل، میدان باغ فردوس" target="_blank" rel="noopener noreferrer"
                                class="inline-flex items-center gap-2 bg-[#445D84] text-white font-bold px-6 py-3 rounded-full hover:bg-[#344868] transition duration-300 shadow-md">
                                <i class="icon icon--sm" data-lucide="external-link" aria-hidden="true"></i>
                                مسیریابی در Google Maps
                            </a>
                        </div>
                    </div>

                    <div class="mt-6 pt-5 border-t border-[#EFE9DD] space-y-3 text-sm">
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <span class="text-gray-600">راهنمای دسترسی از شهرهای اطراف:</span>
                            <div class="flex flex-wrap gap-3 font-bold">
                                <a href="/locations/amirkola/" class="text-[#445D84] hover:underline">دانش‌آموزان امیرکلا</a>
                                <span class="text-gray-300" aria-hidden="true">|</span>
                                <a href="/locations/babolsar-mazandaran/" class="text-[#445D84] hover:underline">بابلسر، آمل و قائمشهر</a>
                            </div>
                        </div>
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <span class="text-gray-600">دانش‌آموز فعلی فامو هستید؟</span>
                            <a href="<?= $loginUrlEsc ?>" class="inline-flex items-center gap-1.5 text-[#445D84] font-bold hover:underline">
                                <i class="icon icon--sm" data-lucide="user" aria-hidden="true"></i>
                                ورود مستقیم به پنل دانش‌آموزی
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Direct Consultation Request Section (Honest channels until backend consultation API is ready) -->
            <section id="consultation" class="famo-detail-block">
                <div class="max-w-3xl mb-7">
                    <span class="famo-eyebrow famo-eyebrow--light">درخواست مشاوره و شروع همکاری</span>
                    <h2 class="text-2xl sm:text-3xl font-bold text-[#445D84] mb-3">
                        اولین قدم برای شروع مسیر آموزشی فامو
                    </h2>
                    <p class="text-gray-600 leading-relaxed">
                        برای آشنایی با برنامه‌های آموزشی، بررسی شرایط تحصیلی دانش‌آموز و انتخاب خدمات متناسب (کلاس خصوصی، آزمون، رفع اشکال یا برنامه‌ریزی)، مستقیماً از یکی از راه‌های زیر با واحد مشاوره فامو ارتباط بگیرید:
                    </p>
                </div>

                <div class="famo-channel-grid mb-8">
                    <a href="tel:+989014402300" class="famo-channel-card">
                        <span class="famo-program-icon" aria-hidden="true">
                            <i class="icon" data-lucide="phone" aria-hidden="true"></i>
                        </span>
                        <h3 class="text-lg font-bold text-[#445D84] m-0">تماس تلفنی مستقیم</h3>
                        <p class="text-sm text-gray-600 m-0 leading-relaxed">گفت‌وگوی مستقیم با مشاوران فامو برای بررسی مقطع و نیاز آموزشی دانش‌آموز.</p>
                        <span class="text-sm font-bold text-[#445D84] mt-auto pt-2" dir="ltr">0901-440-2300</span>
                    </a>

                    <a href="https://wa.me/989014402300" target="_blank" rel="noopener noreferrer" class="famo-channel-card">
                        <span class="famo-program-icon" aria-hidden="true">
                            <i class="icon" data-lucide="message-circle" aria-hidden="true"></i>
                        </span>
                        <h3 class="text-lg font-bold text-[#445D84] m-0">هماهنگی در واتساپ</h3>
                        <p class="text-sm text-gray-600 m-0 leading-relaxed">ارسال مقطع، پایه تحصیلی و درس‌های مورد نیاز برای هماهنگی جلسه مشاوره.</p>
                        <span class="text-sm font-bold text-[#445D84] mt-auto pt-2">شروع گفت‌وگو در واتساپ</span>
                    </a>

                    <a href="https://t.me/famoacademy" target="_blank" rel="noopener noreferrer" class="famo-channel-card">
                        <span class="famo-program-icon" aria-hidden="true">
                            <i class="icon" data-lucide="send" aria-hidden="true"></i>
                        </span>
                        <h3 class="text-lg font-bold text-[#445D84] m-0">پیام در تلگرام</h3>
                        <p class="text-sm text-gray-600 m-0 leading-relaxed">دریافت راهنمایی درباره دوره‌ها، کلاس‌های خصوصی و زمان‌بندی جلسات.</p>
                        <span class="text-sm font-bold text-[#445D84] mt-auto pt-2" dir="ltr">@famoacademy</span>
                    </a>
                </div>

                <!-- 5 Steps of Starting Cooperation (Strategy Doc v1.0 Section 3.9) -->
                <div class="bg-[#f9f7f3] rounded-2xl border border-[#EFE9DD] p-6">
                    <h3 class="text-lg font-bold text-[#445D84] mb-4">مراحل شروع همکاری با فامو</h3>
                    <ol class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 text-sm text-gray-700 list-none p-0 m-0">
                        <li class="bg-white rounded-xl border border-[#E2D9C6] p-3.5">
                            <span class="block text-xs font-bold text-[#736158] mb-1">مرحله ۱</span>
                            <span class="font-bold text-[#445D84]">تماس و بیان نیاز آموزشی توسط خانواده</span>
                        </li>
                        <li class="bg-white rounded-xl border border-[#E2D9C6] p-3.5">
                            <span class="block text-xs font-bold text-[#736158] mb-1">مرحله ۲</span>
                            <span class="font-bold text-[#445D84]">بررسی مقطع و وضعیت اولیه دانش‌آموز</span>
                        </li>
                        <li class="bg-white rounded-xl border border-[#E2D9C6] p-3.5">
                            <span class="block text-xs font-bold text-[#736158] mb-1">مرحله ۳</span>
                            <span class="font-bold text-[#445D84]">گفت‌وگو و مشاوره تخصصی</span>
                        </li>
                        <li class="bg-white rounded-xl border border-[#E2D9C6] p-3.5">
                            <span class="block text-xs font-bold text-[#736158] mb-1">مرحله ۴</span>
                            <span class="font-bold text-[#445D84]">انتخاب خدمات متناسب با شرایط فرد</span>
                        </li>
                        <li class="bg-white rounded-xl border border-[#E2D9C6] p-3.5">
                            <span class="block text-xs font-bold text-[#736158] mb-1">مرحله ۵</span>
                            <span class="font-bold text-[#445D84]">هماهنگی زمان جلسات و شروع مسیر</span>
                        </li>
                    </ol>
                </div>
            </section>
        </div>
    </section>
</main>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
</body>
</html>
