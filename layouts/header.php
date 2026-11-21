<?php
$base = $base ?? '';
$baseEsc = htmlspecialchars($base, ENT_QUOTES, 'UTF-8');
$loginUrlEsc = htmlspecialchars(famo_login_url() . '/', ENT_QUOTES, 'UTF-8');
?>
<!-- Header fragment: parent page owns the document shell and includes layouts/head-common.php. Requires $base. -->
<header class="fixed top-0 left-0 right-0 z-50 px-2 sm:px-4 py-2 sm:py-4">
    <div
        class="header-alpha px-3 sm:px-6 py-3 sm:py-4 max-w-7xl mx-auto">
        <div class="container mx-auto flex justify-between items-center">
            <!-- Logo -->
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-full flex items-center justify-center shrink-0">
                    <a href="<?= $base === '' ? './' : $baseEsc ?>" aria-label="صفحه اصلی آموزشگاه فامو">
                        <img src="<?= htmlspecialchars(famo_asset('images/logo.webp', $base . '../shared/images/logo.webp'), ENT_QUOTES, 'UTF-8') ?>" alt="لوگوی آموزشگاه فامو" width="48" height="48" decoding="async" class="w-12 h-12 object-contain">
                    </a>
                </div>
                <div>
                    <a href="<?= $base === '' ? './' : $baseEsc ?>" class="block text-white text-xl font-bold leading-tight">گروه آموزشی <span class="text-[#E2D9C6]">فامو</span></a>
                    <p class="text-white/80 text-sm">مشاوره تخصصی کنکور و تیزهوشان</p>
                </div>
            </div>

            <!-- Desktop Navigation -->
            <nav class="hidden lg:block" aria-label="منوی اصلی">
                <ul class="famo-desktop-nav">
                    <li>
                        <a href="<?= $baseEsc ?>#home"
                            class="relative text-white font-medium text-sm py-2 px-2 xl:px-3 rounded-lg hover:text-[#E2D9C6] transition duration-300 group flex items-center">
                            <i class="icon icon--sm ml-1 opacity-75 group-hover:opacity-100" data-lucide="house" aria-hidden="true"></i>
                            خانه
                        </a>
                    </li>
                    <li>
                        <a href="<?= $baseEsc ?>pages/course.php"
                            class="relative text-white font-medium text-sm py-2 px-2 xl:px-3 rounded-lg hover:text-[#E2D9C6] transition duration-300 group flex items-center">
                            <i class="icon icon--sm ml-1 opacity-75 group-hover:opacity-100" data-lucide="book-open" aria-hidden="true"></i>
                            دوره‌ها
                        </a>
                    </li>
                    <li>
                        <a href="<?= $baseEsc ?>pages/team.php"
                            class="relative text-white font-medium text-sm py-2 px-2 xl:px-3 rounded-lg hover:text-[#E2D9C6] transition duration-300 group flex items-center">
                            <i class="icon icon--sm ml-1 opacity-75 group-hover:opacity-100" data-lucide="school" aria-hidden="true"></i>
                            اساتید
                        </a>
                    </li>
                    <li>
                        <a href="<?= $baseEsc ?>pages/admissions.php"
                            class="relative text-white font-medium text-sm py-2 px-2 xl:px-3 rounded-lg hover:text-[#E2D9C6] transition duration-300 group flex items-center">
                            <i class="icon icon--sm ml-1 opacity-75 group-hover:opacity-100" data-lucide="award" aria-hidden="true"></i>
                            قبولی‌ها
                        </a>
                    </li>
                    <li>
                        <a href="<?= $baseEsc ?>pages/testimonials.php"
                            class="relative text-white font-medium text-sm py-2 px-2 xl:px-3 rounded-lg hover:text-[#E2D9C6] transition duration-300 group flex items-center">
                            <i class="icon icon--sm ml-1 opacity-75 group-hover:opacity-100" data-lucide="message-circle" aria-hidden="true"></i>
                            نظرات
                        </a>
                    </li>
                    <li>
                        <a href="<?= $baseEsc ?>pages/blog.php"
                            class="relative text-white font-medium text-sm py-2 px-2 xl:px-3 rounded-lg hover:text-[#E2D9C6] transition duration-300 group flex items-center">
                            <i class="icon icon--sm ml-1 opacity-75 group-hover:opacity-100" data-lucide="clipboard-list" aria-hidden="true"></i>
                            وبلاگ
                        </a>
                    </li>
                    <li>
                        <a href="<?= $baseEsc ?>#faq-contact"
                            class="relative text-white font-medium text-sm py-2 px-2 xl:px-3 rounded-lg hover:text-[#E2D9C6] transition duration-300 group flex items-center">
                            <i class="icon icon--sm ml-1 opacity-75 group-hover:opacity-100" data-lucide="circle-help" aria-hidden="true"></i>
                            تماس
                        </a>
                    </li>
                    <li>
                        <a href="<?= $loginUrlEsc ?>"
                            class="bg-[#E2D9C6] text-[#445D84] text-sm font-bold px-4 xl:px-5 py-2 rounded-full hover:bg-white transition duration-300 shadow-md hover:shadow-lg flex items-center">
                            <i class="icon icon--sm ml-1" data-lucide="user-plus" aria-hidden="true"></i>
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
        <nav id="mobileMenu" aria-label="منوی موبایل" class="hidden lg:hidden mt-4 pt-4 border-t border-white/20 animate-fade-in-up">
            <ul class="flex flex-col space-y-2">
                <li><a href="<?= $baseEsc ?>#home"
                        class="block text-white font-medium py-3 px-2 rounded hover:bg-white/10 transition duration-300">خانه</a></li>
                <li><a href="<?= $baseEsc ?>#about"
                        class="block text-white font-medium py-3 px-2 rounded hover:bg-white/10 transition duration-300">درباره
                    ما</a></li>
                <li><a href="<?= $baseEsc ?>pages/course.php"
                        class="block text-white font-medium py-3 px-2 rounded hover:bg-white/10 transition duration-300">دوره‌ها</a></li>
                <li><a href="<?= $baseEsc ?>pages/team.php"
                        class="block text-white font-medium py-3 px-2 rounded hover:bg-white/10 transition duration-300">اساتید</a></li>
                <li><a href="<?= $baseEsc ?>pages/admissions.php"
                        class="block text-white font-medium py-3 px-2 rounded hover:bg-white/10 transition duration-300">قبولی‌های سال‌های قبل</a></li>
                <li><a href="<?= $baseEsc ?>pages/testimonials.php"
                        class="block text-white font-medium py-3 px-2 rounded hover:bg-white/10 transition duration-300">نظرات شاگردان</a></li>
                <li><a href="<?= $baseEsc ?>pages/blog.php"
                        class="block text-white font-medium py-3 px-2 rounded hover:bg-white/10 transition duration-300">وبلاگ</a></li>
                <li><a href="<?= $baseEsc ?>#services"
                        class="block text-white font-medium py-3 px-2 rounded hover:bg-white/10 transition duration-300">خدمات
                        ویژه</a></li>
                <li><a href="<?= $baseEsc ?>#faq-contact"
                        class="block text-white font-medium py-3 px-2 rounded hover:bg-white/10 transition duration-300">سوالات
                        و تماس</a></li>
                <li class="pt-2"><a href="<?= $loginUrlEsc ?>"
                        class="block bg-[#E2D9C6] text-[#445D84] font-bold text-center py-3 rounded-lg hover:bg-white transition duration-300">ثبت
                    نام</a></li>
            </ul>
        </nav>
    </div>
</header>
