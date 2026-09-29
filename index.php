<?php $base=''; $page_title='آموزشگاه فامو | مشاوره کنکور و تیزهوشان بابل'; $meta_description='آموزشگاه فامو بابل - مشاوره تخصصی کنکور و تیزهوشان'; ?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <meta name="description" content="<?php echo $meta_description; ?>">
    <?php include 'layouts/head-common.php'; ?>
</head>
<body class="font-family text-gray-800">
    <!-- Header -->
   <?php include 'layouts/header.php' ?>
    <!-- Hero Section -->
    <section id="home" class="pt-32 sm:pt-32 pb-12 sm:pb-20 px-4 gradient-bg text-white">
        <div class="container mx-auto max-w-6xl">
            <div class="flex flex-col lg:flex-row items-center justify-between gap-8">
                <div class="lg:w-1/2 text-center lg:text-right">
                    <h1 class="text-3xl sm:text-4xl md:text-5xl font-bold mb-4 sm:mb-6 leading-tight">
                        مشاوره تخصصی، آزمون‌های جامع و آموزش مفهومی
                        <span class="text-[#E2D9C6] block mt-2">کنکور و تیزهوشان</span>
                    </h1>
                    <p class="text-base sm:text-lg mb-6 sm:mb-8 text-white/90 leading-relaxed">
                        آموزشگاه فامو با تیمی از اساتید مجرب و مشاوران حرفه‌ای، همراه دانش‌آموزان در مسیر موفقیت تحصیلی
                        است.
                    </p>
                    <div class="flex flex-row justify-center items-center gap-3 sm:gap-5">
                        <a href="<?= htmlspecialchars(famo_login_url() . '/', ENT_QUOTES, 'UTF-8') ?>"
                            class="bg-gradient-to-r from-[#E2D9C6] to-[#d4c9b2] text-[#445D84] px-8 py-3 rounded-full font-bold hover:shadow-xl transition-all duration-300 hover:scale-105 shadow-lg inline-flex items-center min-h-[44px]">
                            <i class="icon ml-2" data-lucide="school" aria-hidden="true"></i>
                            رزرو نوبت
                        </a>
                        <a href="#courses"
                            class="bg-transparent border-2 border-[#E2D9C6] text-[#E2D9C6] px-4 py-3 rounded-full font-bold hover:bg-[#E2D9C6] hover:text-[#445D84] transition-all duration-300 inline-flex items-center min-h-[44px]">
                            <i class="icon ml-2" data-lucide="book-open" aria-hidden="true"></i>
                            مشاهده دوره‌ها
                        </a>
                    </div>
                </div>
                <!-- Illustration & Stats -->
                <div class="lg:w-1/2 w-full relative z-10 mt-0 lg:mt-0">
                    <div class="relative w-full max-w-lg mx-auto">
                        <!-- Stats Card -->
                        <div
                            class="glass-effect rounded-2xl p-6 border border-white/20 lg:absolute lg:-bottom-16 lg:-right-6 lg:max-w-sm w-full mt-10 lg:mt-0 shadow-2xl backdrop-blur-md z-20">
                            <h3
                                class="text-lg font-bold mb-4 text-center text-[#E2D9C6] flex items-center justify-center">
                                <i class="icon icon--md ml-2" data-lucide="chart-column" aria-hidden="true"></i>
                                آمار فامو در یک نگاه
                            </h3>
                            <div class="grid grid-cols-3 gap-3 text-center divide-x divide-x-reverse divide-white/10">
                                                            <!-- SEO fallback values (search engines read these immediately) -->
                                                            <div class="px-1">
                                                                <div class="text-2xl font-bold mb-1 text-white" id="years-counter">7</div>
                                                                <div class="text-white/70 text-xs">سال تجربه</div>
                                                            </div>
                                                            <div class="px-1">
                                                                <div class="text-2xl font-bold mb-1 text-white" id="students-counter">500+</div>
                                                                <div class="text-white/70 text-xs">قبولی کنکور</div>
                                                            </div>
                                                            <div class="px-1">
                                                                <div class="text-2xl font-bold mb-1 text-white" id="teachers-counter">15</div>
                                                                <div class="text-white/70 text-xs">استاد برتر</div>
                                                            </div>
                                                        </div>
                            </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!-- Courses Section -->
    <section id="courses" class="py-20 px-4 bg-gradient-to-b from-[#f9f7f3] to-white">
        <div class="container mx-auto max-w-6xl">
            <div class="text-center mb-12 sm:mb-16">
                <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold text-[#445D84] mb-3 sm:mb-4">دوره‌های تخصصی فامو
                </h2>
                <p class="text-base sm:text-lg text-gray-600 max-w-2xl mx-auto px-4"> دوره‌های جامع آمادگی کنکور و
                    تیزهوشان با اساتید مجرب و روش‌های نوین آموزشی</p>
            </div>

            <!-- Course Cards - Swiper Carousel -->
            <div class="swiper coursesSwiper relative pb-12">
                <div class="swiper-wrapper" id="coursesContainer">
                    <!-- Skeleton Loaders -->
                    <div class="swiper-slide skeleton-course-wrapper">
                        <div
                            class="skeleton-course-card md:w-full h-48 sm:h-40 md:h-auto rounded-2xl flex items-center justify-center relative overflow-hidden min-h-[200px]">
                            <div
                                class="skeleton skeleton-pulse absolute inset-0 bg-gradient-to-r from-gray-300 to-gray-200">
                            </div>
                            <div class="relative z-10 text-center p-4">
                                <div class="skeleton skeleton-pulse w-16 h-16 rounded-full bg-white/30 mx-auto mb-4">
                                </div>
                                <div class="skeleton skeleton-pulse h-6 w-32 bg-white/30 rounded mx-auto"></div>
                            </div>
                        </div>
                    </div>
                    <div class="swiper-slide skeleton-course-wrapper">
                        <div
                            class="skeleton-course-card md:w-full h-48 sm:h-40 md:h-auto rounded-2xl flex items-center justify-center relative overflow-hidden min-h-[200px]">
                            <div
                                class="skeleton skeleton-pulse absolute inset-0 bg-gradient-to-r from-gray-300 to-gray-200">
                            </div>
                            <div class="relative z-10 text-center p-4">
                                <div class="skeleton skeleton-pulse w-16 h-16 rounded-full bg-white/30 mx-auto mb-4">
                                </div>
                                <div class="skeleton skeleton-pulse h-6 w-32 bg-white/30 rounded mx-auto"></div>
                            </div>
                        </div>
                    </div>
                    <div class="swiper-slide skeleton-course-wrapper">
                        <div
                            class="skeleton-course-card md:w-full h-48 sm:h-40 md:h-auto rounded-2xl flex items-center justify-center relative overflow-hidden min-h-[200px]">
                            <div
                                class="skeleton skeleton-pulse absolute inset-0 bg-gradient-to-r from-gray-300 to-gray-200">
                            </div>
                            <div class="relative z-10 text-center p-4">
                                <div class="skeleton skeleton-pulse w-16 h-16 rounded-full bg-white/30 mx-auto mb-4">
                                </div>
                                <div class="skeleton skeleton-pulse h-6 w-32 bg-white/30 rounded mx-auto"></div>
                            </div>
                        </div>
                    </div>
                    <div class="swiper-slide skeleton-course-wrapper">
                        <div
                            class="skeleton-course-card md:w-full h-48 sm:h-40 md:h-auto rounded-2xl flex items-center justify-center relative overflow-hidden min-h-[200px]">
                            <div
                                class="skeleton skeleton-pulse absolute inset-0 bg-gradient-to-r from-gray-300 to-gray-200">
                            </div>
                            <div class="relative z-10 text-center p-4">
                                <div class="skeleton skeleton-pulse w-16 h-16 rounded-full bg-white/30 mx-auto mb-4">
                                </div>
                                <div class="skeleton skeleton-pulse h-6 w-32 bg-white/30 rounded mx-auto"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Swiper Navigation -->

                <div class="swiper-pagination"></div>
            </div>

            <div class="text-center mt-10">
                <a href="pages/course.php"
                    class="inline-flex items-center bg-[#445D84] text-white px-6 py-3 rounded-full font-bold hover:bg-[#344868] transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#445D84] focus-visible:ring-offset-2">
                    مشاهده همه دوره‌ها و جزئیات کامل
                    <i class="icon icon--sm mr-2" data-lucide="chevron-left" aria-hidden="true"></i>
                </a>
            </div>
        </div>
    </section>


    <!-- Team Section -->
    <section id="team" class="py-20 px-4 bg-[#f9f7f3]">
        <div class="container mx-auto max-w-6xl">
            <div class="text-center mb-12 sm:mb-16">
                <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold text-[#445D84] mb-3 sm:mb-4">کادر حرفه ای آموزشگاه فامو
                </h2>
                <p class="text-base sm:text-lg text-gray-600 max-w-2xl mx-auto px-4">جمعی از اساتید و دانشجویان برتر و متخصص شهرستان بابل</p>
            </div>

            <!-- Course Cards - Swiper Carousel -->
            <div class="swiper instructorsSwiper relative pb-12">
                <div class="swiper-wrapper" id="instructorsContainer">
                    <!-- Skeleton Loaders -->
                    <div class="swiper-slide skeleton-instructor-wrapper">
                        <div
                            class="skeleton-instructor-card md:w-full h-48 sm:h-40 md:h-auto rounded-2xl flex items-center justify-center relative overflow-hidden min-h-[200px]">
                            <div
                                class="skeleton skeleton-pulse absolute inset-0 bg-gradient-to-r from-gray-300 to-gray-200">
                            </div>
                            <div class="relative z-10 text-center p-4">
                                <div class="skeleton skeleton-pulse w-16 h-16 rounded-full bg-white/30 mx-auto mb-4">
                                </div>
                                <div class="skeleton skeleton-pulse h-6 w-32 bg-white/30 rounded mx-auto"></div>
                            </div>
                        </div>
                    </div>
                    <div class="swiper-slide skeleton-instructor-wrapper">
                        <div
                            class="skeleton-instructor-card md:w-full h-48 sm:h-40 md:h-auto rounded-2xl flex items-center justify-center relative overflow-hidden min-h-[200px]">
                            <div
                                class="skeleton skeleton-pulse absolute inset-0 bg-gradient-to-r from-gray-300 to-gray-200">
                            </div>
                            <div class="relative z-10 text-center p-4">
                                <div class="skeleton skeleton-pulse w-16 h-16 rounded-full bg-white/30 mx-auto mb-4">
                                </div>
                                <div class="skeleton skeleton-pulse h-6 w-32 bg-white/30 rounded mx-auto"></div>
                            </div>
                        </div>
                    </div>
                    <div class="swiper-slide skeleton-instructor-wrapper">
                        <div
                            class="skeleton-instructor-card md:w-full h-48 sm:h-40 md:h-auto rounded-2xl flex items-center justify-center relative overflow-hidden min-h-[200px]">
                            <div
                                class="skeleton skeleton-pulse absolute inset-0 bg-gradient-to-r from-gray-300 to-gray-200">
                            </div>
                            <div class="relative z-10 text-center p-4">
                                <div class="skeleton skeleton-pulse w-16 h-16 rounded-full bg-white/30 mx-auto mb-4">
                                </div>
                                <div class="skeleton skeleton-pulse h-6 w-32 bg-white/30 rounded mx-auto"></div>
                            </div>
                        </div>
                    </div>
                    <div class="swiper-slide skeleton-instructor-wrapper">
                        <div
                            class="skeleton-instructor-card md:w-full h-48 sm:h-40 md:h-auto rounded-2xl flex items-center justify-center relative overflow-hidden min-h-[200px]">
                            <div
                                class="skeleton skeleton-pulse absolute inset-0 bg-gradient-to-r from-gray-300 to-gray-200">
                            </div>
                            <div class="relative z-10 text-center p-4">
                                <div class="skeleton skeleton-pulse w-16 h-16 rounded-full bg-white/30 mx-auto mb-4">
                                </div>
                                <div class="skeleton skeleton-pulse h-6 w-32 bg-white/30 rounded mx-auto"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Swiper Navigation -->

                <div class="swiper-pagination"></div>
            </div>

            <div class="text-center mt-10">
                <a href="pages/team.php"
                    class="inline-flex items-center bg-[#445D84] text-white px-6 py-3 rounded-full font-bold hover:bg-[#344868] transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#445D84] focus-visible:ring-offset-2">
                    مشاهده کادر آموزشگاه فامو
                    <i class="icon icon--sm mr-2" data-lucide="chevron-left" aria-hidden="true"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="py-20 px-4 bg-white">
        <div class="container mx-auto max-w-5xl">
            <div class="text-center mb-10">
                <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold text-[#445D84] mb-3 sm:mb-4">درباره آموزشگاه فامو
                </h2>
            </div>
            <div class="text-gray-700 leading-loose text-base sm:text-lg space-y-5 text-justify">
                <p>
                    فامو یک موسسه آموزشی کنکور پیشرو در بابل است که با بهره‌گیری از تجربه نخبگان علمی، از جمله دانشجویان
                    برتر
                    رشته‌های پزشکی و مهندسی و اساتید مدال‌آور المپیاد، به هدایت دانش‌آموزان می‌پردازد. این مجموعه با
                    تکیه بر هفت سال سابقه درخشان در مسیر قبولی دانشگاه‌های معتبر، خدماتی جامع شامل برنامه‌ریزی
                    شخصی‌سازی‌شده و رفع اشکال دقیق تمامی دروس را برای مقاطع راهنمایی و دبیرستان فراهم می‌آورد.
                </p>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section id="services" class="py-20 px-4 gradient-bg-light">
        <div class="container mx-auto max-w-6xl">
            <div class="text-center mb-12 sm:mb-16">
                <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold text-[#445D84] mb-3 sm:mb-4">خدمات ویژه آموزشگاه
                    فامو</h2>
                <p class="text-base sm:text-lg text-gray-600 max-w-2xl mx-auto px-4">امکانات و خدمات منحصر به فرد ما
                    برای موفقیت صددرصدی دانش‌آموزان</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 sm:gap-8">
                <!-- Service 1 -->
                <div
                    class="bg-white rounded-2xl p-6 sm:p-8 shadow-xl border border-[#E2D9C6] hover-lift transition-all duration-300">
                    <div
                        class="w-20 h-20 bg-gradient-to-br from-[#E2D9C6] to-[#d4c9b2] rounded-full flex items-center justify-center mb-6 mx-auto">
                        <i class="icon icon--xl text-[#445D84]" data-lucide="headset" aria-hidden="true"></i>
                    </div>
                    <h3 class="text-xl font-bold text-[#445D84] mb-4 text-center">پشتیبانی ۲۴ ساعته</h3>
                    <p class="text-gray-600 text-center leading-relaxed">پاسخگویی به سوالات درسی دانش‌آموزان در تمامی
                        ساعات شبانه‌روز توسط تیم پشتیبانی متخصص.</p>
                </div>

                <!-- Service 2 -->
                <div class="bg-white rounded-2xl p-8 shadow-xl border border-[#E2D9C6] hover-lift transition-all duration-500 animate-card-slide"
                    style="animation-delay: 0.2s">
                    <div
                        class="w-20 h-20 bg-gradient-to-br from-[#E2D9C6] to-[#d4c9b2] rounded-full flex items-center justify-center mb-6 mx-auto">
                        <i class="icon icon--xl text-[#445D84]" data-lucide="chart-column" aria-hidden="true"></i>
                    </div>
                    <h3 class="text-xl font-bold text-[#445D84] mb-4 text-center">آزمون‌های هفتگی</h3>
                    <p class="text-gray-600 text-center leading-relaxed">برگزاری آزمون‌های استاندارد با تحلیل پیشرفت
                        فردی هر دانش‌آموز و ارائه گزارش کامل.</p>
                </div>

                <!-- Service 3 -->
                <div class="bg-white rounded-2xl p-8 shadow-xl border border-[#E2D9C6] hover-lift transition-all duration-500 animate-card-slide"
                    style="animation-delay: 0.3s">
                    <div
                        class="w-20 h-20 bg-gradient-to-br from-[#E2D9C6] to-[#d4c9b2] rounded-full flex items-center justify-center mb-6 mx-auto">
                        <i class="icon icon--xl text-[#445D84]" data-lucide="users" aria-hidden="true"></i>
                    </div>
                    <h3 class="text-xl font-bold text-[#445D84] mb-4 text-center">کلاس‌های خصوصی</h3>
                    <p class="text-gray-600 text-center leading-relaxed">برگزاری جلسات خصوصی برای رفع اشکال تک‌تک دروس
                        با هماهنگی قبلی و اساتید مجرب.</p>
                </div>

                <!-- Service 4 -->
                <div class="bg-white rounded-2xl p-8 shadow-xl border border-[#E2D9C6] hover-lift transition-all duration-500 animate-card-slide"
                    style="animation-delay: 0.4s">
                    <div
                        class="w-20 h-20 bg-gradient-to-br from-[#E2D9C6] to-[#d4c9b2] rounded-full flex items-center justify-center mb-6 mx-auto">
                        <i class="icon icon--xl text-[#445D84]" data-lucide="book-open" aria-hidden="true"></i>
                    </div>
                    <h3 class="text-xl font-bold text-[#445D84] mb-4 text-center">مشاوره تخصصی</h3>
                    <p class="text-gray-600 text-center leading-relaxed">برنامه‌ریزی تحصیلی شخصی‌سازی شده توسط مشاوران
                        خبره و رتبه‌های برتر کنکور.</p>
                </div>
            </div>

            <div
                class="mt-16 bg-gradient-to-r from-[#445D84] to-[#5a779e] rounded-2xl p-8 text-center text-white shadow-xl">
                <h3 class="text-2xl font-bold mb-4">همین امروز شروع کنید!</h3>
                <p class="mb-6 max-w-2xl mx-auto">برای دریافت مشاوره رایگان و آگاهی از شرایط ویژه ثبت‌نام، با ما در تماس
                    باشید.</p>
                <a href="#faq-contact"
                    class="bg-white text-[#445D84] px-8 py-3 rounded-full font-bold hover:shadow-lg transition-all duration-300 hover:scale-105 inline-flex items-center">
                    <i class="icon ml-2" data-lucide="phone" aria-hidden="true"></i>
                    تماس با ما
                </a>
            </div>
        </div>
    </section>

    <!-- FAQ & Contact Section -->
    <section id="faq-contact" class="py-20 px-4 bg-white">
        <div class="container mx-auto max-w-6xl">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                <!-- FAQ Section -->
                <div>
                    <h2 class="text-2xl sm:text-3xl font-bold text-[#445D84] mb-6 sm:mb-8 flex items-center">
                        <i class="icon icon--lg ml-3 text-[#8B786D]" data-lucide="circle-help" aria-hidden="true"></i>
                        سوالات متداول
                    </h2>

                    <div class="space-y-4">
                        <!-- FAQ Item 1 -->
                        <div
                            class="faq-item bg-gradient-to-r from-white to-[#f9f7f3] rounded-xl shadow-md border border-[#E2D9C6] overflow-hidden hover-lift">
                            <div class="faq-question cursor-pointer p-5 flex justify-between items-center" tabindex="0" role="button" aria-expanded="false">
                                <h3 class="text-lg font-bold text-[#445D84]">آیا دوره‌ها به صورت حضوری برگزار می‌شوند؟
                                </h3>
                                <span class="faq-icon text-[#8B786D] transform transition-transform duration-300">
                                    <i class="icon icon--sm" data-lucide="chevron-down" aria-hidden="true"></i>
                                </span>
                            </div>
                            <div class="faq-answer px-5">
                                <p class="pb-5 text-gray-600 leading-relaxed">بله، تمامی دوره‌های آموزشگاه فامو در حال
                                    حاضر به صورت حضوری در محل آموزشگاه واقع در بابل، میدان باغ فردوس برگزار می‌شوند.
                                    کلاس‌ها در محیطی استاندارد و مجهز به امکانات آموزشی روز برگزار می‌گردد.</p>
                            </div>
                        </div>

                        <!-- FAQ Item 2 -->
                        <div
                            class="faq-item bg-gradient-to-r from-white to-[#f9f7f3] rounded-xl shadow-md border border-[#E2D9C6] overflow-hidden hover-lift">
                            <div class="faq-question cursor-pointer p-5 flex justify-between items-center" tabindex="0" role="button" aria-expanded="false">
                                <h3 class="text-lg font-bold text-[#445D84]">هزینه دوره‌ها چقدر است و آیا امکان پرداخت
                                    اقساطی وجود دارد؟</h3>
                                <span class="faq-icon text-[#8B786D] transform transition-transform duration-300">
                                    <i class="icon icon--sm" data-lucide="chevron-down" aria-hidden="true"></i>
                                </span>
                            </div>
                            <div class="faq-answer px-5">
                                <p class="pb-5 text-gray-600 leading-relaxed">برای اطلاع از شرایط پرداخت اقساطی و
                                    تخفیف‌های ویژه، لطفا با شماره تماس آموزشگاه در ارتباط باشید. امکان پرداخت قسطی نیز
                                    برای دانش‌آموزان فراهم است.</p>
                            </div>
                        </div>

                        <!-- FAQ Item 3 -->
                        <div
                            class="faq-item bg-gradient-to-r from-white to-[#f9f7f3] rounded-xl shadow-md border border-[#E2D9C6] overflow-hidden hover-lift">
                            <div class="faq-question cursor-pointer p-5 flex justify-between items-center" tabindex="0" role="button" aria-expanded="false">
                                <h3 class="text-lg font-bold text-[#445D84]">پشتیبانی درسی به چه صورت است؟</h3>
                                <span class="faq-icon text-[#8B786D] transform transition-transform duration-300">
                                    <i class="icon icon--sm" data-lucide="chevron-down" aria-hidden="true"></i>
                                </span>
                            </div>
                            <div class="faq-answer px-5">
                                <p class="pb-5 text-gray-600 leading-relaxed">پشتیبانان ما که از رتبه‌های برتر کنکور
                                    سال‌های گذشته هستند، به صورت ۲۴ ساعته از طریق پیام‌رسان‌ها آماده پاسخگویی به سوالات
                                    درسی و مشاوره‌ای شما هستند. همچنین جلسات رفع اشکال هفتگی نیز برگزار می‌شود.</p>
                            </div>
                        </div>

                        <!-- FAQ Item 4 -->
                        <div
                            class="faq-item bg-gradient-to-r from-white to-[#f9f7f3] rounded-xl shadow-md border border-[#E2D9C6] overflow-hidden hover-lift">
                            <div class="faq-question cursor-pointer p-5 flex justify-between items-center" tabindex="0" role="button" aria-expanded="false">
                                <h3 class="text-lg font-bold text-[#445D84]">چگونه می‌توانم برای مشاوره رایگان ثبت‌نام
                                    کنم؟</h3>
                                <span class="faq-icon text-[#8B786D] transform transition-transform duration-300">
                                    <i class="icon icon--sm" data-lucide="chevron-down" aria-hidden="true"></i>
                                </span>
                            </div>
                            <div class="faq-answer px-5">
                                <p class="pb-5 text-gray-600 leading-relaxed">شما می‌توانید با مراجعه به صفحه ثبت‌نام و
                                    پر کردن فرم مربوطه، درخواست خود را برای جلسه مشاوره رایگان ثبت کنید. همکاران ما در
                                    اسرع وقت با شما تماس خواهند گرفت تا زمان مناسبی را با شما هماهنگ کنند.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact Section -->
                <div>
                    <h2 class="text-2xl sm:text-3xl font-bold text-[#445D84] mb-6 sm:mb-8 flex items-center">
                        <i class="icon icon--lg ml-3 text-[#8B786D]" data-lucide="id-card" aria-hidden="true"></i>
                        با ما در ارتباط باشید
                    </h2>

                    <div
                        class="bg-gradient-to-br from-white to-[#f9f7f3] rounded-2xl shadow-xl border border-[#E2D9C6] p-2 hover-lift">
                        <div class="space-y-1">
                            <!-- Address -->
                            <div
                                class="flex items-start space-x-4 space-x-reverse hover:bg-white/50 p-3 rounded-lg transition duration-300 gap-4">
                                <div
                                    class="bg-gradient-to-br from-[#E2D9C6] to-[#d4c9b2] text-[#445D84] w-12 h-12 rounded-full flex items-center justify-center flex-shrink-0">
                                    <i class="icon" data-lucide="map-pin" aria-hidden="true"></i>
                                </div>
                                <div>
                                    <h4 class="font-bold text-lg text-[#445D84] mb-1">آدرس آموزشگاه</h4>
                                    <p class="text-gray-600">بابل، میدان باغ فردوس</p>
                                </div>
                            </div>

                            <!-- Phone -->
                            <div
                                class="flex items-start space-x-4 space-x-reverse hover:bg-white/50 p-3 rounded-lg transition duration-300 gap-4">
                                <div
                                    class="bg-gradient-to-br from-[#E2D9C6] to-[#d4c9b2] text-[#445D84] w-12 h-12 rounded-full flex items-center justify-center flex-shrink-0">
                                    <i class="icon" data-lucide="phone" aria-hidden="true"></i>
                                </div>
                                <div>
                                    <h4 class="font-bold text-lg text-[#445D84] mb-1">شماره تماس</h4>
                                    <p class="text-gray-600">0901-440-2300</p>
                                    <p class="text-gray-600">0999-904-1429</p>
                                    <p class="text-gray-600">0990-681-7387</p>
                                </div>
                            </div>

                            <!-- Email -->
                            <div
                                class="flex items-start space-x-4 space-x-reverse hover:bg-white/50 p-3 rounded-lg transition duration-300 gap-4">
                                <div
                                    class="bg-gradient-to-br from-[#E2D9C6] to-[#d4c9b2] text-[#445D84] w-12 h-12 rounded-full flex items-center justify-center flex-shrink-0">
                                    <i class="icon" data-lucide="mail" aria-hidden="true"></i>
                                </div>
                                <div>
                                    <h4 class="font-bold text-lg text-[#445D84] mb-1">ایمیل</h4>
                                    <p class="text-gray-600">info@famoacademy.ir</p>
                                </div>
                            </div>

                            <!-- Telegram -->
                            <div
                                class="flex items-start space-x-4 space-x-reverse hover:bg-white/50 p-3 rounded-lg transition duration-300 gap-4">
                                <div
                                    class="bg-gradient-to-br from-[#E2D9C6] to-[#d4c9b2] text-[#445D84] w-12 h-12 rounded-full flex items-center justify-center flex-shrink-0">
                                    <i class="icon" data-lucide="send" aria-hidden="true"></i>
                                </div>
                                <div>
                                    <h4 class="font-bold text-lg text-[#445D84] mb-1">تلگرام</h4>
                                    <p class="text-black">famoacademy@</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <?php include 'layouts/footer.php'?>

    <!-- Scripts -->
    <script type="module" src="assets/js/main.js"></script>
</body>

</html>
