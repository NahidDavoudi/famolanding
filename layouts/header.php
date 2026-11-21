<?php
$base = $base ?? '';
$baseEsc = htmlspecialchars($base, ENT_QUOTES, 'UTF-8');
$loginUrlEsc = htmlspecialchars(famo_login_url() . '/', ENT_QUOTES, 'UTF-8');
?>
<!-- Header fragment: parent page owns the document shell and includes layouts/head-common.php. Requires $base. -->
<header class="fixed top-0 left-0 right-0 z-50 px-2 sm:px-4 py-2 sm:py-4">
    <div
        class="bg-[#445D84] header-alpha backdrop-blur-sm rounded-xl sm:rounded-2xl shadow-xl border border-white/20 px-3 sm:px-6 py-3 sm:py-4 max-w-7xl mx-auto">
        <div class="container mx-auto flex justify-between items-center">
            <!-- Logo -->
            <div class="flex items-center gap-4">
                <div class="bg-[#445D84] w-12 h-12 rounded-full flex items-center justify-center shrink-0">
                    <a href="<?= $baseEsc ?>index.php">
                        <img src="<?= htmlspecialchars(famo_asset('images/logo.webp', $base . '../shared/images/logo.webp'), ENT_QUOTES, 'UTF-8') ?>" alt="لوگوی آموزشگاه فامو" width="48" height="48" decoding="async" class="w-12 h-12 object-contain">
                    </a>
                </div>
                <div>
                    <h1 class="text-white text-xl font-bold">گروه آموزشی <span class="text-[#E2D9C6]">فامو</span></h1>
                    <p class="text-white/80 text-sm">مشاوره تخصصی کنکور و تیزهوشان</p>
                </div>
            </div>

            <!-- Desktop Navigation -->
            <nav class="hidden lg:block">
                <ul class="famo-desktop-nav">
                    <li>
                        <a href="<?= $baseEsc ?>#home"
                            class="relative text-white hover:text-[#E2D9C6] transition-all duration-300 font-medium text-sm py-2 px-2 xl:px-3 rounded-lg hover:bg-white/10 group flex items-center">
                            <i class="icon icon--sm ml-1 opacity-70 group-hover:opacity-100" data-lucide="house" aria-hidden="true"></i>
                            خانه
                        </a>
                    </li>
                    <li>
                        <a href="<?= $baseEsc ?>pages/course.php"
                            class="relative text-white hover:text-[#E2D9C6] transition-all duration-300 font-medium text-sm py-2 px-2 xl:px-3 rounded-lg hover:bg-white/10 group flex items-center">
                            <i class="icon icon--sm ml-1 opacity-70 group-hover:opacity-100" data-lucide="book-open" aria-hidden="true"></i>
                            دوره‌ها
                        </a>
                    </li>
                    <li>
                        <a href="<?= $baseEsc ?>pages/team.php"
                            class="relative text-white hover:text-[#E2D9C6] transition-all duration-300 font-medium text-sm py-2 px-2 xl:px-3 rounded-lg hover:bg-white/10 group flex items-center">
                            <i class="icon icon--sm ml-1 opacity-70 group-hover:opacity-100" data-lucide="school" aria-hidden="true"></i>
                            اساتید
                        </a>
                    </li>
                    <li>
                        <a href="<?= $baseEsc ?>pages/admissions.php"
                            class="relative text-white hover:text-[#E2D9C6] transition-all duration-300 font-medium text-sm py-2 px-2 xl:px-3 rounded-lg hover:bg-white/10 group flex items-center">
                            <i class="icon icon--sm ml-1 opacity-70 group-hover:opacity-100" data-lucide="award" aria-hidden="true"></i>
                            قبولی‌ها
                        </a>
                    </li>
                    <li>
                        <a href="<?= $baseEsc ?>pages/testimonials.php"
                            class="relative text-white hover:text-[#E2D9C6] transition-all duration-300 font-medium text-sm py-2 px-2 xl:px-3 rounded-lg hover:bg-white/10 group flex items-center">
                            <i class="icon icon--sm ml-1 opacity-70 group-hover:opacity-100" data-lucide="message-circle" aria-hidden="true"></i>
                            نظرات
                        </a>
                    </li>
                    <li>
                        <a href="<?= $baseEsc ?>pages/blog.php"
                            class="relative text-white hover:text-[#E2D9C6] transition-all duration-300 font-medium text-sm py-2 px-2 xl:px-3 rounded-lg hover:bg-white/10 group flex items-center">
                            <i class="icon icon--sm ml-1 opacity-70 group-hover:opacity-100" data-lucide="clipboard-list" aria-hidden="true"></i>
                            وبلاگ
                        </a>
                    </li>
                    <li>
                        <a href="<?= $baseEsc ?>#faq-contact"
                            class="relative text-white hover:text-[#E2D9C6] transition-all duration-300 font-medium text-sm py-2 px-2 xl:px-3 rounded-lg hover:bg-white/10 group flex items-center">
                            <i class="icon icon--sm ml-1 opacity-70 group-hover:opacity-100" data-lucide="circle-help" aria-hidden="true"></i>
                            تماس
                        </a>
                    </li>
                    <li>
                        <a href="<?= $loginUrlEsc ?>"
                            class="bg-gradient-to-r from-[#E2D9C6] to-[#d4c9b2] text-[#445D84] px-4 xl:px-5 py-2.5 rounded-full font-bold hover:shadow-lg hover:shadow-[#E2D9C6]/30 transition-all duration-300 text-sm hover:scale-105 flex items-center gap-2">
                            <i class="icon icon--sm" data-lucide="user-plus" aria-hidden="true"></i>
                            ثبت نام
                        </a>
                    </li>
                </ul>
            </nav>

            <!-- Mobile Menu Button -->
            <button id="mobileMenuBtn"
                class="lg:hidden text-white text-2xl hover:text-[#E2D9C6] hover:cursor-pointer transition duration-300"
                aria-label="باز کردن منوی موبایل" aria-expanded="false">
                <i class="icon icon--lg" data-lucide="menu" aria-hidden="true"></i>
            </button>
        </div>

        <!-- Mobile Navigation -->
        <div id="mobileMenu" class="hidden lg:hidden mt-4 pt-4 border-t border-white/20 animate-fade-in-up">
            <ul class="flex flex-col space-y-4">
                <li><a href="<?= $baseEsc ?>#home"
                        class="block text-white hover:text-[#E2D9C6] transition duration-300 font-medium py-3 hover:bg-white/10 px-3 rounded-lg">خانه</a></li>
                <li><a href="<?= $baseEsc ?>#about"
                        class="block text-white hover:text-[#E2D9C6] transition duration-300 font-medium py-3 hover:bg-white/10 px-3 rounded-lg">درباره
                    ما</a></li>
                <li><a href="<?= $baseEsc ?>pages/course.php"
                        class="block text-white hover:text-[#E2D9C6] transition duration-300 font-medium py-3 hover:bg-white/10 px-3 rounded-lg">دوره‌ها</a></li>
                <li><a href="<?= $baseEsc ?>pages/team.php"
                        class="block text-white hover:text-[#E2D9C6] transition duration-300 font-medium py-3 hover:bg-white/10 px-3 rounded-lg">اساتید</a></li>
                <li><a href="<?= $baseEsc ?>pages/admissions.php"
                        class="block text-white hover:text-[#E2D9C6] transition duration-300 font-medium py-3 hover:bg-white/10 px-3 rounded-lg">قبولی‌های سال‌های قبل</a></li>
                <li><a href="<?= $baseEsc ?>pages/testimonials.php"
                        class="block text-white hover:text-[#E2D9C6] transition duration-300 font-medium py-3 hover:bg-white/10 px-3 rounded-lg">نظرات شاگردان</a></li>
                <li><a href="<?= $baseEsc ?>pages/blog.php"
                        class="block text-white hover:text-[#E2D9C6] transition duration-300 font-medium py-3 hover:bg-white/10 px-3 rounded-lg">وبلاگ</a></li>
                <li><a href="<?= $baseEsc ?>#services"
                        class="block text-white hover:text-[#E2D9C6] transition duration-300 font-medium py-3 hover:bg-white/10 px-3 rounded-lg">خدمات
                        ویژه</a></li>
                <li><a href="<?= $baseEsc ?>#faq-contact"
                        class="block text-white hover:text-[#E2D9C6] transition duration-300 font-medium py-3 hover:bg-white/10 px-3 rounded-lg">سوالات
                        و تماس</a></li>
                <li><a href="<?= $loginUrlEsc ?>"
                        class="block bg-gradient-to-r from-[#E2D9C6] to-[#d4c9b2] text-[#445D84] text-center py-3 rounded-full font-bold hover:shadow-lg transition duration-300">ثبت
                    نام</a></li>
            </ul>
        </div>
    </div>
</header>
