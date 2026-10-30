<?php $base=''; $page_title='آموزشگاه فامو | مشاوره کنکور و تیزهوشان بابل'; $meta_description='آموزشگاه فامو بابل - مشاوره تخصصی کنکور و تیزهوشان'; ?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <meta name="description" content="<?php echo $meta_description; ?>">
    <?php include 'layouts/head-common.php'; ?>
    <!-- استایل کارت‌های دوره (طرح مشترک با pages/course.php): فاصله‌ها و رنگ‌ها از توکن‌های برند -->
    <style>
        #courses .coursesSwiper .swiper-slide {
            height: auto;
        }
    </style>
</head>
<body class="font-family text-gray-800">
    <!-- Header -->
   <?php include 'layouts/header.php' ?>
    <!-- Hero Section -->
    <section id="home" class="hero-section gradient-bg text-white px-4">
        <div class="container mx-auto max-w-6xl hero-inner">
            <!-- ۱) تصویر هیرو: تمام‌عرض، بدون overlay -->
            <div class="hero-media">
                <img src="assets/images/poster_hero.webp" alt="محیط آموزشی آموزشگاه فامو" width="2817" height="1585" decoding="async" fetchpriority="high" class="hero-media-img">
            </div>

            <!-- ۲) تیتر -->
            <h1 class="hero-title">
                مشاوره تخصصی، آزمون‌های جامع و آموزش مفهومی
                <span class="hero-title-accent">کنکور و تیزهوشان</span>
            </h1>

            <!-- ۳) توضیح کوتاه -->
            <p class="hero-desc">اساتید مجرب و مشاوران حرفه‌ای، همراه دانش‌آموزان تا قبولی.</p>

            <!-- ۴) کارت آمار: خارج از تصویر، روی پس‌زمینه روشن -->
            <div class="hero-stats">
                <div class="grid grid-cols-3 text-center hero-stats-grid">
                    <div class="hero-stat">
                        <div class="hero-stat-number text-2xl sm:text-3xl font-bold"><span id="years-counter">7</span><span class="hero-stat-plus">+</span></div>
                        <div class="hero-stat-label text-sm">سال تجربه</div>
                    </div>
                    <div class="hero-stat">
                        <div class="hero-stat-number text-2xl sm:text-3xl font-bold"><span id="students-counter">200</span><span class="hero-stat-plus">+</span></div>
                        <div class="hero-stat-label text-sm">قبولی کنکور</div>
                    </div>
                    <div class="hero-stat">
                        <div class="hero-stat-number text-2xl sm:text-3xl font-bold"><span id="teachers-counter">10</span><span class="hero-stat-plus">+</span></div>
                        <div class="hero-stat-label text-sm">استاد برتر</div>
                    </div>
                </div>
            </div>

            <!-- ۵) دکمه‌ها: هم‌اندازه -->
            <div class="hero-ctas">
                <a href="<?= htmlspecialchars(famo_login_url() . '/', ENT_QUOTES, 'UTF-8') ?>"
                    class="hero-cta bg-gradient-to-r from-[#E2D9C6] to-[#d4c9b2] text-[#445D84] px-6 py-3 rounded-full font-bold hover:shadow-xl transition-all duration-300 hover:scale-105 shadow-lg min-h-[44px]">
                    <i class="icon ml-2" data-lucide="school" aria-hidden="true"></i>
                    رزرو نوبت
                </a>
                <a href="#courses"
                    class="hero-cta bg-transparent border-2 border-[#E2D9C6] text-[#E2D9C6] px-6 py-3 rounded-full font-bold hover:bg-[#E2D9C6] hover:text-[#445D84] transition-all duration-300 min-h-[44px]">
                    <i class="icon ml-2" data-lucide="book-open" aria-hidden="true"></i>
                    مشاهده دوره‌ها
                </a>
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
            <div class="swiper coursesSwiper coursesSwiper--loading relative pb-12">
                <div class="swiper-wrapper" id="coursesContainer" aria-busy="true">
                    <!-- Skeleton Loaders -->
                    <div class="swiper-slide skeleton-course-wrapper h-auto">
                        <div class="famo-course-card course-card-skeleton" aria-hidden="true">
                            <div class="famo-course-media skeleton-shimmer skel-media"></div>
                            <div class="famo-course-body">
                                <div class="skeleton-shimmer skel-bar skel-bar--title"></div>
                                <div class="skeleton-shimmer skel-bar skel-bar--title-short"></div>
                                <div class="skel-chips"><span class="skeleton-shimmer"></span><span class="skeleton-shimmer"></span></div>
                                <div class="skeleton-shimmer skel-bar skel-bar--desc"></div>
                                <div class="skeleton-shimmer skel-bar skel-bar--desc-short"></div>
                                <div class="famo-course-footer">
                                    <span class="skeleton-shimmer skel-cta"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="swiper-slide skeleton-course-wrapper h-auto">
                        <div class="famo-course-card course-card-skeleton" aria-hidden="true">
                            <div class="famo-course-media skeleton-shimmer skel-media"></div>
                            <div class="famo-course-body">
                                <div class="skeleton-shimmer skel-bar skel-bar--title"></div>
                                <div class="skeleton-shimmer skel-bar skel-bar--title-short"></div>
                                <div class="skel-chips"><span class="skeleton-shimmer"></span><span class="skeleton-shimmer"></span></div>
                                <div class="skeleton-shimmer skel-bar skel-bar--desc"></div>
                                <div class="skeleton-shimmer skel-bar skel-bar--desc-short"></div>
                                <div class="famo-course-footer">
                                    <span class="skeleton-shimmer skel-cta"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="swiper-slide skeleton-course-wrapper h-auto">
                        <div class="famo-course-card course-card-skeleton" aria-hidden="true">
                            <div class="famo-course-media skeleton-shimmer skel-media"></div>
                            <div class="famo-course-body">
                                <div class="skeleton-shimmer skel-bar skel-bar--title"></div>
                                <div class="skeleton-shimmer skel-bar skel-bar--title-short"></div>
                                <div class="skel-chips"><span class="skeleton-shimmer"></span><span class="skeleton-shimmer"></span></div>
                                <div class="skeleton-shimmer skel-bar skel-bar--desc"></div>
                                <div class="skeleton-shimmer skel-bar skel-bar--desc-short"></div>
                                <div class="famo-course-footer">
                                    <span class="skeleton-shimmer skel-cta"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="swiper-slide skeleton-course-wrapper h-auto">
                        <div class="famo-course-card course-card-skeleton" aria-hidden="true">
                            <div class="famo-course-media skeleton-shimmer skel-media"></div>
                            <div class="famo-course-body">
                                <div class="skeleton-shimmer skel-bar skel-bar--title"></div>
                                <div class="skeleton-shimmer skel-bar skel-bar--title-short"></div>
                                <div class="skel-chips"><span class="skeleton-shimmer"></span><span class="skeleton-shimmer"></span></div>
                                <div class="skeleton-shimmer skel-bar skel-bar--desc"></div>
                                <div class="skeleton-shimmer skel-bar skel-bar--desc-short"></div>
                                <div class="famo-course-footer">
                                    <span class="skeleton-shimmer skel-cta"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Swiper Navigation (RTL، aria-label فارسی) -->
                <!-- <button type="button" class="swiper-button-prev" aria-label="دوره قبلی"></button>
                <button type="button" class="swiper-button-next" aria-label="دوره بعدی"></button> -->

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
                <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold text-[#445D84] mb-3 sm:mb-4">کادر حرفه‌ای آموزشگاه فامو
                </h2>
                <p class="text-base sm:text-lg text-gray-600 max-w-2xl mx-auto px-4">اساتید، مشاوران و پشتیبانان برتر بابل؛ همراه دانش‌آموزان از آموزش تا رفع اشکال</p>
            </div>

            <div class="curve-carousel">
                <div class="curve-carousel__wrap">
                    <div class="curve-carousel__stage" tabindex="0"
                         aria-label="کاروسل کادر آموزشی فامو؛ با کشیدن یا کلیدهای جهت‌دار جابه‌جا کنید">
                        <div class="curve-carousel__edge-blur curve-carousel__edge-blur--left" aria-hidden="true"></div>
                        <div class="curve-carousel__edge-blur curve-carousel__edge-blur--right" aria-hidden="true"></div>
                        <div class="curve-carousel__track is-skeleton" id="instructorsContainer" aria-busy="true">
                            <!-- Skeleton Loaders -->
                            <div class="skeleton-instructor-wrapper">
                                <div class="famo-inst-skel" aria-hidden="true">
                                    <div class="skeleton-shimmer skel-inst-media"></div>
                                    <div class="skeleton-shimmer skel-bar skel-bar--inst-name"></div>
                                    <div class="skeleton-shimmer skel-bar skel-bar--inst-title"></div>
                                    <div class="skeleton-shimmer skel-bar skel-bar--inst-bio"></div>
                                    <div class="skeleton-shimmer skel-bar skel-bar--inst-bio-short"></div>
                                    <div class="skel-inst-socials"><span class="skeleton-shimmer"></span><span class="skeleton-shimmer"></span></div>
                                </div>
                            </div>
                            <div class="skeleton-instructor-wrapper">
                                <div class="famo-inst-skel" aria-hidden="true">
                                    <div class="skeleton-shimmer skel-inst-media"></div>
                                    <div class="skeleton-shimmer skel-bar skel-bar--inst-name"></div>
                                    <div class="skeleton-shimmer skel-bar skel-bar--inst-title"></div>
                                    <div class="skeleton-shimmer skel-bar skel-bar--inst-bio"></div>
                                    <div class="skeleton-shimmer skel-bar skel-bar--inst-bio-short"></div>
                                    <div class="skel-inst-socials"><span class="skeleton-shimmer"></span><span class="skeleton-shimmer"></span></div>
                                </div>
                            </div>
                            <div class="skeleton-instructor-wrapper">
                                <div class="famo-inst-skel" aria-hidden="true">
                                    <div class="skeleton-shimmer skel-inst-media"></div>
                                    <div class="skeleton-shimmer skel-bar skel-bar--inst-name"></div>
                                    <div class="skeleton-shimmer skel-bar skel-bar--inst-title"></div>
                                    <div class="skeleton-shimmer skel-bar skel-bar--inst-bio"></div>
                                    <div class="skeleton-shimmer skel-bar skel-bar--inst-bio-short"></div>
                                    <div class="skel-inst-socials"><span class="skeleton-shimmer"></span><span class="skeleton-shimmer"></span></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- کنترل‌ها (dotها + فلش‌ها = جایگزین درگ برای دسترس‌پذیری) -->
                    <div class="curve-carousel__controls">
                        <div class="curve-carousel__arrows">
                            <button type="button" class="curve-carousel__arrow" id="instCarouselPrev" aria-label="قبلی">
                                <i class="icon" data-lucide="chevron-right" aria-hidden="true"></i>
                            </button>
                            <div class="curve-carousel__dots" id="instCarouselDots"></div>
                            <button type="button" class="curve-carousel__arrow" id="instCarouselNext" aria-label="بعدی">
                                <i class="icon" data-lucide="chevron-left" aria-hidden="true"></i>
                            </button>
                        </div>
                    </div>
                </div>
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
<section id="about" class="relative py-20 px-4 bg-white overflow-hidden">
    <!-- تصویر پس‌زمینه -->
    <div class="absolute inset-0 z-0">
        <!-- <img src="assets/images/about_us.webp"
             alt="آموزشگاه فامو"
             width="1920" height="1080"
             loading="lazy"
             decoding="async"
             class="w-full h-full object-cover opacity-100"> -->
        <!-- لایه نیمه‌شفاف برای خوانایی متن -->
        <div class="absolute inset-0 bg-white/85"></div>
    </div>

    <div class="container mx-auto max-w-6xl relative z-10">
        <div class="text-center mb-10">
            <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold text-[#445D84] mb-3 sm:mb-4">
                درباره آموزشگاه فامو
            </h2>
            <div class="w-24 h-1 bg-gradient-to-r from-[#E2D9C6] to-[#445D84] mx-auto rounded-full"></div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 items-center">
            <!-- متن -->
            <div class="text-gray-700 leading-loose text-base sm:text-lg space-y-5 text-justify">
                <p>
                    <span class="font-bold text-[#445D84]">فامو</span> یک موسسه آموزشی کنکور پیشرو در بابل است که با
                    بهره‌گیری از تجربه نخبگان علمی، از جمله دانشجویان برتر رشته‌های پزشکی و مهندسی و اساتید مدال‌آور
                    المپیاد، به هدایت دانش‌آموزان می‌پردازد.
                </p>

                <p>
                    این مجموعه با تکیه بر <strong class="text-[#445D84]">هفت سال سابقه درخشان</strong> در مسیر قبولی
                    دانشگاه‌های معتبر، خدماتی جامع شامل برنامه‌ریزی شخصی‌سازی‌شده و رفع اشکال دقیق تمامی دروس را برای
                    مقاطع راهنمایی و دبیرستان فراهم می‌آورد.
                </p>

                <!-- <p>
                    هدف ما ایجاد مسیری روشن و مطمئن برای دانش‌آموزان در جهت رسیدن به موفقیت تحصیلی و ورود به
                    دانشگاه‌های برتر کشور است.
                </p> -->

                
            </div>

            <!-- تصویر -->
            <div class="relative">
                <div class="rounded-3xl overflow-hidden shadow-2xl border-4 border-white">
                    <img src="assets/images/about_us-small.webp"
                         alt="محیط آموزشگاه فامو"
                         width="800" height="600"
                         loading="lazy"
                         decoding="async"
                         class="w-full h-auto object-cover">
                </div>
            </div>
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
                <article class="service-card hover-lift">
                    <div class="service-media">
                        <img src="assets/images/services/24support.webp"
                            alt="ایلوستریشن پشتیبانی ۲۴ ساعته آموزشگاه فامو"
                            width="800" height="500" loading="lazy" decoding="async"
                            class="service-illustration">
                    </div>
                    <div class="service-body">
                        <h3 class="service-title">پشتیبانی ۲۴ ساعته</h3>
                        <p class="service-desc">پاسخگویی به سوالات درسی دانش‌آموزان در تمامی ساعات شبانه‌روز توسط تیم پشتیبانی متخصص.</p>
                    </div>
                </article>

                <!-- Service 2 -->
                <article class="service-card hover-lift animate-card-slide" style="animation-delay: 0.2s">
                    <div class="service-media">
                        <img src="assets/images/services/weekly_exams.webp"
                            alt="ایلوستریشن آزمون‌های هفتگی و تحلیل پیشرفت"
                            width="800" height="500" loading="lazy" decoding="async"
                            class="service-illustration">
                    </div>
                    <div class="service-body">
                        <h3 class="service-title">آزمون‌های هفتگی</h3>
                        <p class="service-desc">برگزاری آزمون‌های استاندارد با تحلیل پیشرفت فردی هر دانش‌آموز و ارائه گزارش کامل.</p>
                    </div>
                </article>

                <!-- Service 3 -->
                <article class="service-card hover-lift animate-card-slide" style="animation-delay: 0.3s">
                    <div class="service-media">
                        <img src="assets/images/services/private_classes.webp"
                            alt="ایلوستریشن کلاس‌های خصوصی و رفع اشکال"
                            width="800" height="500" loading="lazy" decoding="async"
                            class="service-illustration">
                    </div>
                    <div class="service-body">
                        <h3 class="service-title">کلاس‌های خصوصی</h3>
                        <p class="service-desc">برگزاری جلسات خصوصی برای رفع اشکال تک‌تک دروس با هماهنگی قبلی و اساتید مجرب.</p>
                    </div>
                </article>

                <!-- Service 4 -->
                <article class="service-card hover-lift animate-card-slide" style="animation-delay: 0.4s">
                    <div class="service-media">
                        <img src="assets/images/services/weekly_plans.webp"
                            alt="ایلوستریشن مشاوره تخصصی و برنامه‌ریزی تحصیلی"
                            width="800" height="500" loading="lazy" decoding="async"
                            class="service-illustration">
                    </div>
                    <div class="service-body">
                        <h3 class="service-title">مشاوره تخصصی</h3>
                        <p class="service-desc">برنامه‌ریزی تحصیلی شخصی‌سازی شده توسط مشاوران خبره و رتبه‌های برتر کنکور.</p>
                    </div>
                </article>
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
