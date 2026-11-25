<?php
$base = $base ?? '/';
$baseEsc = htmlspecialchars($base, ENT_QUOTES, 'UTF-8');
$homeHref = '/';
$loginUrlRaw = famo_login_url();
$loginUrlEsc = htmlspecialchars(($loginUrlRaw !== '' ? $loginUrlRaw . '/' : 'https://login.famoacademy.ir/'), ENT_QUOTES, 'UTF-8');
?>
<!-- Footer -->
<footer class="gradient-bg text-white pt-12 pb-6 px-4">
    <div class="container mx-auto max-w-6xl">
        <div class="famo-footer-grid">
            <!-- Brand & Description -->
            <div>
                <div class="flex items-center gap-3.5 mb-5">
                    <div class="bg-[#445D84] w-12 h-12 rounded-full flex items-center justify-center shrink-0">
                        <picture>
                            <source srcset="<?= htmlspecialchars(famo_local_asset('assets/images/logo.webp', $base), ENT_QUOTES, 'UTF-8') ?>" type="image/webp">
                            <img src="<?= htmlspecialchars(famo_local_asset('assets/images/logo.png', $base), ENT_QUOTES, 'UTF-8') ?>" alt="لوگوی آموزشگاه فامو" width="40" height="40" loading="lazy" decoding="async" class="w-10 h-10 object-contain">
                        </picture>
                    </div>
                    <div>
                        <p class="text-xl font-bold leading-snug">آموزشگاه <span class="famo-on-dark-cream">فامو</span></p>
                        <p class="famo-on-dark-soft text-xs mt-0.5">آموزش شخصی، با نگاهی هوشمند</p>
                    </div>
                </div>
                <p class="famo-on-dark-soft text-sm leading-relaxed mb-6">
                    آموزش خصوصی، آزمون‌های شخصی‌سازی‌شده و پیگیری هوشمند عملکرد تحصیلی در بابل؛ برای ساختن مسیری متناسب با نیاز هر دانش‌آموز.
                </p>
                <div class="flex gap-3">
                    <a href="https://t.me/famoacademy" target="_blank" rel="noopener noreferrer"
                        class="famo-social-btn w-10 h-10 rounded-full flex items-center justify-center transition duration-300 shrink-0"
                        aria-label="فامو در تلگرام">
                        <i class="icon" data-lucide="send" aria-hidden="true"></i>
                    </a>
                    <a href="https://wa.me/989014402300" target="_blank" rel="noopener noreferrer"
                        class="famo-social-btn w-10 h-10 rounded-full flex items-center justify-center transition duration-300 shrink-0"
                        aria-label="فامو در واتساپ">
                        <i class="icon" data-lucide="message-circle" aria-hidden="true"></i>
                    </a>
                    <a href="mailto:info@famoacademy.ir"
                        class="famo-social-btn w-10 h-10 rounded-full flex items-center justify-center transition duration-300 shrink-0"
                        aria-label="ایمیل آموزشگاه فامو">
                        <i class="icon" data-lucide="mail" aria-hidden="true"></i>
                    </a>
                </div>
            </div>

            <!-- Quick Links -->
            <nav aria-label="دسترسی سریع">
                <h2 class="text-lg font-bold mb-5 famo-on-dark-cream">دسترسی سریع</h2>
                <ul class="space-y-2 text-sm">
                    <li><a href="/"
                            class="famo-footer-link transition duration-300 hover:pr-2 flex items-center">
                        <i class="icon icon--sm ml-2" data-lucide="chevron-left" aria-hidden="true"></i>
                        خانه
                    </a></li>
                    <li><a href="/courses/"
                            class="famo-footer-link transition duration-300 hover:pr-2 flex items-center">
                        <i class="icon icon--sm ml-2" data-lucide="chevron-left" aria-hidden="true"></i>
                        دوره‌ها و مقاطع آموزشی
                    </a></li>
                    <li><a href="/courses/math-babol/"
                            class="famo-footer-link transition duration-300 hover:pr-2 flex items-center">
                        <i class="icon icon--sm ml-2" data-lucide="chevron-left" aria-hidden="true"></i>
                        کلاس خصوصی ریاضی بابل
                    </a></li>
                    <li><a href="/courses/konkur-babol/"
                            class="famo-footer-link transition duration-300 hover:pr-2 flex items-center">
                        <i class="icon icon--sm ml-2" data-lucide="chevron-left" aria-hidden="true"></i>
                        آموزشگاه کنکور بابل
                    </a></li>
                    <li><a href="/courses/tizhooshan-babol/"
                            class="famo-footer-link transition duration-300 hover:pr-2 flex items-center">
                        <i class="icon icon--sm ml-2" data-lucide="chevron-left" aria-hidden="true"></i>
                        آموزشگاه تیزهوشان بابل
                    </a></li>
                    <li><a href="/counseling-babol/"
                            class="famo-footer-link transition duration-300 hover:pr-2 flex items-center">
                        <i class="icon icon--sm ml-2" data-lucide="chevron-left" aria-hidden="true"></i>
                        مشاوره تحصیلی بابل
                    </a></li>
                    <li><a href="/services/"
                            class="famo-footer-link transition duration-300 hover:pr-2 flex items-center">
                        <i class="icon icon--sm ml-2" data-lucide="chevron-left" aria-hidden="true"></i>
                        خدمات آموزشی و سامانه هوشمند
                    </a></li>
                    <li><a href="/team/"
                            class="famo-footer-link transition duration-300 hover:pr-2 flex items-center">
                        <i class="icon icon--sm ml-2" data-lucide="chevron-left" aria-hidden="true"></i>
                        اساتید و پشتیبانان
                    </a></li>
                    <li><a href="/results/"
                            class="famo-footer-link transition duration-300 hover:pr-2 flex items-center">
                        <i class="icon icon--sm ml-2" data-lucide="chevron-left" aria-hidden="true"></i>
                        قبولی‌ها و نظرات
                    </a></li>
                    <li><a href="/blog/"
                            class="famo-footer-link transition duration-300 hover:pr-2 flex items-center">
                        <i class="icon icon--sm ml-2" data-lucide="chevron-left" aria-hidden="true"></i>
                        وبلاگ و مقالات آموزشی
                    </a></li>
                    <li><a href="/contact/"
                            class="famo-footer-link transition duration-300 hover:pr-2 flex items-center">
                        <i class="icon icon--sm ml-2" data-lucide="chevron-left" aria-hidden="true"></i>
                        تماس با ما
                    </a></li>
                </ul>
            </nav>

            <!-- Contact Info (NAP Consistent) -->
            <div>
                <h2 class="text-lg font-bold mb-5 famo-on-dark-cream">ارتباط با فامو</h2>
                <address class="not-italic space-y-3 text-sm famo-on-dark-soft">
                    <div class="flex items-start gap-2.5">
                        <i class="icon icon--sm mt-1 famo-on-dark-cream shrink-0" data-lucide="map-pin" aria-hidden="true"></i>
                        <span>بابل، میدان باغ فردوس</span>
                    </div>
                    <div class="flex items-start gap-2.5">
                        <i class="icon icon--sm mt-1 famo-on-dark-cream shrink-0" data-lucide="phone" aria-hidden="true"></i>
                        <div class="flex flex-col gap-1">
                            <a href="tel:+989014402300" dir="ltr" class="famo-footer-link transition text-right">0901-440-2300</a>
                            <a href="tel:+989999041429" dir="ltr" class="famo-footer-link transition text-right">0999-904-1429</a>
                            <a href="tel:+989906817387" dir="ltr" class="famo-footer-link transition text-right">0990-681-7387</a>
                        </div>
                    </div>
                    <div class="flex items-center gap-2.5">
                        <i class="icon icon--sm famo-on-dark-cream shrink-0" data-lucide="mail" aria-hidden="true"></i>
                        <a href="mailto:info@famoacademy.ir" class="famo-footer-link transition">info@famoacademy.ir</a>
                    </div>
                    <div class="flex items-center gap-2.5">
                        <i class="icon icon--sm famo-on-dark-cream shrink-0" data-lucide="send" aria-hidden="true"></i>
                        <a href="https://t.me/famoacademy" target="_blank" rel="noopener noreferrer" dir="ltr" class="famo-footer-link transition">@famoacademy</a>
                    </div>
                </address>
                <div class="mt-5 pt-4 border-t border-white/15">
                    <p class="text-xs famo-on-dark-cream font-bold mb-2">مناطق تحت پوشش در مازندران</p>
                    <div class="flex flex-wrap gap-x-3 gap-y-1.5 text-xs">
                        <a href="/locations/amirkola/" class="famo-footer-link hover:underline">دانش‌آموزان امیرکلا</a>
                        <span class="text-white/30" aria-hidden="true">•</span>
                        <a href="/locations/babolsar-mazandaran/" class="famo-footer-link hover:underline">بابلسر، آمل و قائمشهر</a>
                    </div>
                </div>
            </div>

            <!-- Student Portal & Map -->
            <div>
                <h2 class="text-lg font-bold mb-5 famo-on-dark-cream">دسترسی دانش‌آموزان</h2>
                <div class="space-y-3 mb-5">
                    <a href="<?= $loginUrlEsc ?>"
                        class="w-full inline-flex items-center justify-center gap-2 famo-btn-cream text-sm font-bold px-4 py-2.5 rounded-xl transition duration-300">
                        <i class="icon icon--sm" data-lucide="user" aria-hidden="true"></i>
                        ورود به پنل دانش‌آموزی
                    </a>
                    <a href="/contact/#consultation"
                        class="famo-footer-consult-btn">
                        <i class="icon icon--sm" data-lucide="phone" aria-hidden="true"></i>
                        درخواست مشاوره
                    </a>
                </div>
                <div class="famo-footer-map-card">
                    <p class="text-xs famo-on-dark-soft mb-2">مراجعه حضوری: بابل، میدان باغ فردوس</p>
                    <a href="https://maps.google.com/?q=بابل، میدان باغ فردوس" target="_blank" rel="noopener noreferrer"
                        class="inline-flex items-center gap-1.5 famo-on-dark-cream hover:text-white text-xs font-bold transition">
                        <i class="icon icon--sm" data-lucide="external-link" aria-hidden="true"></i>
                        مشاهده موقعیت در نقشه
                    </a>
                </div>
            </div>
        </div>

        <!-- Copyright -->
        <div class="border-t border-white/20 pt-6 text-center famo-on-dark-soft">
            <div class="flex flex-col md:flex-row justify-between items-center gap-2 text-sm">
                <p>© <span id="copyright-year">۱۴۰۴</span> تمامی حقوق برای آموزشگاه فامو محفوظ است.</p>
                <p class="famo-on-dark-cream">مدیریت: دکتر محمد موسی زاده موسوی</p>
            </div>
            <p class="mt-4 text-xs sm:text-sm">
                طراحی و توسعه توسط
                <a href="https://mhdavoudi.ir" target="_blank" rel="noopener noreferrer"
                    class="inline-flex items-center gap-1.5 famo-on-dark-cream underline hover:text-white transition duration-300 font-medium" id="nahid">
                    محمد حسین داودی
                </a>
            </p>
        </div>
    </div>
</footer>
