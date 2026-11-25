<?php
$base = $base ?? '/';
$baseEsc = htmlspecialchars($base, ENT_QUOTES, 'UTF-8');
$homeHref = '/';
$loginUrlRaw = famo_login_url();
$loginUrlEsc = htmlspecialchars(($loginUrlRaw !== '' ? $loginUrlRaw . '/' : 'https://auth.famoacademy.ir/'), ENT_QUOTES, 'UTF-8');
?>
<!-- Header fragment: parent page owns the document shell and includes layouts/head-common.php. Requires $base. -->
<a href="#main-content" class="famo-skip-link">رفتن به محتوای اصلی</a>
<header class="fixed top-0 left-0 right-0 z-50 px-2 sm:px-4 py-2 sm:py-4">
    <div class="header-alpha px-3 sm:px-5 py-3 sm:py-3.5 max-w-7xl mx-auto">
        <div class="flex justify-between items-center gap-3">
            <!-- Logo -->
            <div class="flex items-center gap-3 shrink-0">
                <div class="w-11 h-11 rounded-full flex items-center justify-center shrink-0">
                    <a href="<?= $homeHref ?>" aria-label="صفحه اصلی آموزشگاه فامو" class="inline-flex items-center justify-center w-11 h-11">
                        <img src="<?= htmlspecialchars(famo_local_asset('assets/images/logo.webp', $base), ENT_QUOTES, 'UTF-8') ?>" alt="لوگوی آموزشگاه فامو" width="44" height="44" decoding="async" class="w-11 h-11 object-contain">
                    </a>
                </div>
                <div>
                    <a href="<?= $homeHref ?>" class="block text-white text-lg sm:text-xl font-bold leading-tight">آموزشگاه <span class="famo-on-dark-cream">فامو</span></a>
                    <p class="famo-on-dark-soft text-xs sm:text-sm leading-snug">آموزش شخصی، با نگاهی هوشمند</p>
                </div>
            </div>

            <!-- Desktop Navigation -->
            <nav class="famo-header-desktop" aria-label="منوی اصلی">
                <ul class="famo-desktop-nav">
                    <li>
                        <a href="/"
                            class="famo-nav-link font-medium text-sm py-2 px-2.5 rounded-lg transition duration-300 flex items-center">
                            خانه
                        </a>
                    </li>
                    <li>
                        <a href="/courses/"
                            class="famo-nav-link font-medium text-sm py-2 px-2.5 rounded-lg transition duration-300 flex items-center">
                            دوره‌ها
                        </a>
                    </li>
                    <li>
                        <a href="/services/"
                            class="famo-nav-link font-medium text-sm py-2 px-2.5 rounded-lg transition duration-300 flex items-center">
                            خدمات
                        </a>
                    </li>
                    <li>
                        <a href="/team/"
                            class="famo-nav-link font-medium text-sm py-2 px-2.5 rounded-lg transition duration-300 flex items-center">
                            اساتید
                        </a>
                    </li>
                    <li>
                        <a href="/services/smart-platform/"
                            class="famo-nav-link font-medium text-sm py-2 px-2.5 rounded-lg transition duration-300 flex items-center">
                            سامانه هوشمند
                        </a>
                    </li>
                    <li>
                        <a href="/blog/"
                            class="famo-nav-link font-medium text-sm py-2 px-2.5 rounded-lg transition duration-300 flex items-center">
                            مقالات
                        </a>
                    </li>
                    <li class="famo-nav-dropdown" data-famo-dropdown>
                        <button type="button"
                            class="famo-nav-dropdown__trigger famo-nav-link font-medium text-sm py-2 px-2.5 rounded-lg transition duration-300 flex items-center gap-1"
                            data-famo-dropdown-trigger
                            aria-expanded="false"
                            aria-haspopup="true">
                            نتایج و مشاوره
                            <i class="icon icon--sm" data-lucide="chevron-down" aria-hidden="true"></i>
                        </button>
                        <ul class="famo-nav-dropdown__menu" data-famo-dropdown-menu hidden>
                            <li>
                                <a href="/results/" class="famo-nav-dropdown__link">
                                    <i class="icon icon--sm" data-lucide="award" aria-hidden="true"></i>
                                    قبولی‌های سال‌های قبل
                                </a>
                            </li>
                            <li>
                                <a href="/results/#testimonials" class="famo-nav-dropdown__link">
                                    <i class="icon icon--sm" data-lucide="message-circle" aria-hidden="true"></i>
                                    نظرات شاگردان و اولیا
                                </a>
                            </li>
                            <li>
                                <a href="/counseling-babol/" class="famo-nav-dropdown__link">
                                    <i class="icon icon--sm" data-lucide="compass" aria-hidden="true"></i>
                                    مشاوره تحصیلی بابل
                                </a>
                            </li>
                            <li>
                                <a href="/team/#support" class="famo-nav-dropdown__link">
                                    <i class="icon icon--sm" data-lucide="users" aria-hidden="true"></i>
                                    پشتیبانان آموزشی
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <a href="/contact/"
                            class="famo-nav-link font-medium text-sm py-2 px-2.5 rounded-lg transition duration-300 flex items-center">
                            تماس با ما
                        </a>
                    </li>
                </ul>

                <!-- Independent Primary & Portal CTAs -->
                <div class="famo-header-actions">
                    <a href="/contact/#consultation"
                        class="famo-btn-cream text-sm font-bold px-4 py-2 rounded-full transition duration-300 shadow-md flex items-center gap-1.5 whitespace-nowrap">
                        <i class="icon icon--sm" data-lucide="phone" aria-hidden="true"></i>
                        درخواست مشاوره
                    </a>
                    <a href="<?= $loginUrlEsc ?>"
                        class="famo-portal-btn text-white text-sm font-bold px-3.5 py-2 rounded-full transition duration-300 flex items-center gap-1.5 whitespace-nowrap">
                        <i class="icon icon--sm" data-lucide="user" aria-hidden="true"></i>
                        ورود به پنل
                    </a>
                </div>
            </nav>

            <!-- Mobile Menu Button -->
            <button id="mobileMenuBtn"
                type="button"
                class="famo-header-mobile-btn famo-nav-link text-2xl hover:cursor-pointer transition duration-300 p-1.5"
                aria-label="باز کردن منوی موبایل" aria-expanded="false">
                <i class="icon icon--lg" data-lucide="menu" aria-hidden="true"></i>
            </button>
        </div>

        <!-- Mobile Navigation -->
        <nav id="mobileMenu" aria-label="منوی موبایل" class="hidden famo-header-mobile-menu mt-4 pt-4 border-t border-white/20 animate-fade-in-up">
            <ul class="flex flex-col space-y-1.5">
                <li><a href="/"
                        class="block text-white font-medium py-2.5 px-3 rounded-lg hover:bg-white/10 transition duration-300">خانه</a></li>
                <li><a href="/courses/"
                        class="block text-white font-medium py-2.5 px-3 rounded-lg hover:bg-white/10 transition duration-300">دوره‌ها</a></li>
                <li><a href="/services/"
                        class="block text-white font-medium py-2.5 px-3 rounded-lg hover:bg-white/10 transition duration-300">خدمات آموزشی</a></li>
                <li><a href="/counseling-babol/"
                        class="block text-white font-medium py-2.5 px-3 rounded-lg hover:bg-white/10 transition duration-300">مشاوره تحصیلی بابل</a></li>
                <li><a href="/team/"
                        class="block text-white font-medium py-2.5 px-3 rounded-lg hover:bg-white/10 transition duration-300">اساتید</a></li>
                <li><a href="/team/#support"
                        class="block text-white font-medium py-2.5 px-3 rounded-lg hover:bg-white/10 transition duration-300">پشتیبانان آموزشی</a></li>
                <li><a href="/services/smart-platform/"
                        class="block text-white font-medium py-2.5 px-3 rounded-lg hover:bg-white/10 transition duration-300">سامانه هوشمند</a></li>
                <li><a href="/blog/"
                        class="block text-white font-medium py-2.5 px-3 rounded-lg hover:bg-white/10 transition duration-300">مقالات</a></li>
                <li><a href="/results/"
                        class="block text-white font-medium py-2.5 px-3 rounded-lg hover:bg-white/10 transition duration-300">قبولی‌ها و نتایج</a></li>
                <li><a href="/results/#testimonials"
                        class="block text-white font-medium py-2.5 px-3 rounded-lg hover:bg-white/10 transition duration-300">نظرات شاگردان و اولیا</a></li>
                <li><a href="/contact/"
                        class="block text-white font-medium py-2.5 px-3 rounded-lg hover:bg-white/10 transition duration-300">تماس با ما</a></li>
                <li class="pt-3 grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                    <a href="/contact/#consultation"
                        class="block famo-btn-cream font-bold text-center py-3 px-4 rounded-xl transition duration-300">
                        درخواست مشاوره
                    </a>
                    <a href="<?= $loginUrlEsc ?>"
                        class="block famo-portal-btn text-white font-bold text-center py-3 px-4 rounded-xl transition duration-300">
                        ورود به پنل دانش‌آموزی
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</header>
