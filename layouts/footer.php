<!-- Footer -->
<?php require_once __DIR__ . '/../config.php'; ?>
<footer class="gradient-bg text-white pt-12 pb-6 px-4">
    <div class="container mx-auto max-w-6xl">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-10 pb-10">
            <!-- Logo & Description -->
            <div>
                <div class="flex items-center gap-4 mb-6">
                    <div class="bg-[#445D84] w-12 h-12 rounded-full flex items-center justify-center shrink-0">
                        <img src="<?php echo famo_asset('images/logo.png', $base . '../shared/images/logo.png'); ?>" alt="لوگوی آموزشگاه فامو" width="40" height="40" loading="lazy" decoding="async" class="w-10 h-10 object-contain">
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold">آموزشگاه <span class="text-[#E2D9C6]">فامو</span></h3>
                        <p class="text-white/80 text-sm mt-1">مشاوره تخصصی کنکور و تیزهوشان</p>
                    </div>
                </div>
                <p class="text-white/85 leading-relaxed mb-6">
                    آموزشگاه فامو با بیش از 7 سال سابقه درخشان در زمینه مشاوره و کنکور و تیزهوشان، همراه
                    دانش‌آموزان در مسیر موفقیت تحصیلی است.
                </p>
                <div class="flex gap-4">
                    <a href="https://t.me/famoacademy"
                        class="bg-white/10 w-10 h-10 rounded-full flex items-center justify-center hover:bg-white/20 transition duration-300 hover:scale-110 shrink-0"
                        aria-label="فامو در تلگرام">
                        <i class="icon" data-lucide="send" aria-hidden="true"></i>
                    </a>
                    <a href="#"
                        class="bg-white/10 w-10 h-10 rounded-full flex items-center justify-center hover:bg-white/20 transition duration-300 hover:scale-110 shrink-0"
                        aria-label="فامو در اینستاگرام">
                        <i class="icon" data-lucide="instagram" aria-hidden="true"></i>
                    </a>
                    <a href="https://wa.me/989014402300"
                        class="bg-white/10 w-10 h-10 rounded-full flex items-center justify-center hover:bg-white/20 transition duration-300 hover:scale-110 shrink-0"
                        aria-label="فامو در واتساپ">
                        <i class="icon" data-lucide="message-circle" aria-hidden="true"></i>
                    </a>
                </div>
            </div>

            <!-- Quick Links -->
            <div>
                <h4 class="text-xl font-bold mb-6 text-[#E2D9C6]">دسترسی سریع</h4>
                <ul class="space-y-3">
                    <li><a href="<?php echo $base; ?>#home"
                            class="text-white/80 hover:text-white transition duration-300 hover:pr-2 flex items-center">
                        <i class="icon icon--sm ml-2" data-lucide="chevron-left" aria-hidden="true"></i>
                        خانه
                    </a></li>
                    <li><a href="<?php echo $base; ?>pages/course.php"
                            class="text-white/80 hover:text-white transition duration-300 hover:pr-2 flex items-center">
                        <i class="icon icon--sm ml-2" data-lucide="chevron-left" aria-hidden="true"></i>
                        دوره‌ها
                    </a></li>
                    <li><a href="<?php echo $base; ?>pages/team.php"
                            class="text-white/80 hover:text-white transition duration-300 hover:pr-2 flex items-center">
                        <i class="icon icon--sm ml-2" data-lucide="chevron-left" aria-hidden="true"></i>
                        اساتید
                    </a></li>
                    <li><a href="<?php echo $base; ?>#services"
                            class="text-white/80 hover:text-white transition duration-300 hover:pr-2 flex items-center">
                        <i class="icon icon--sm ml-2" data-lucide="chevron-left" aria-hidden="true"></i>
                        خدمات ویژه
                    </a></li>
                    <li><a href="<?php echo $base; ?>#faq-contact"
                            class="text-white/80 hover:text-white transition duration-300 hover:pr-2 flex items-center">
                        <i class="icon icon--sm ml-2" data-lucide="chevron-left" aria-hidden="true"></i>
                        سوالات و تماس
                    </a></li>
                </ul>
            </div>

            <!-- Map Section -->
            <div>
                <h4 class="text-xl font-bold mb-6 text-[#E2D9C6]">موقعیت آموزشگاه</h4>
                <div class="bg-white/10 rounded-xl overflow-hidden border border-white/20 p-4">
                    <div class="h-48 bg-gradient-to-r from-[#E2D9C6] to-[#d4c9b2] rounded-lg flex flex-col items-center justify-center text-[#445D84]">
                        <i class="icon icon--3xl mb-3" data-lucide="map-pin" aria-hidden="true"></i>
                        <p class="font-bold text-center">بابل، میدان باغ فردوس</p>
                    </div>
                    <div class="mt-4 text-center">
                        <a href="https://maps.google.com/?q=بابل، میدان باغ فردوس" target="_blank"
                            class="inline-flex items-center bg-white/20 text-white px-4 py-2 rounded-lg hover:bg-white/30 transition duration-300">
                            <i class="icon icon--sm ml-2" data-lucide="external-link" aria-hidden="true"></i>
                            مشاهده در نقشه
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Copyright -->
        <div class="border-t border-white/20 pt-6 text-center text-white/80">
            <div class="flex flex-col md:flex-row justify-between items-center gap-2">
                <p>© <span id="copyright-year">۱۴۰۳</span> تمامی حقوق برای آموزشگاه فامو محفوظ است.</p>
                <p class="text-[#E2D9C6]">مدیریت: دکتر محمد موسی زاده موسوی</p>
            </div>
            <p class="mt-4 text-sm">
                طراحی و توسعه توسط
                <a href="https://mhdavoudi.ir" target="_blank" rel="noopener"
                    class="inline-flex items-center gap-1.5 text-[#E2D9C6] underline hover:text-white transition duration-300 font-medium" id="nahid">
                    محمد حسین داودی
                </a>
            </p>
        </div>
    </div>
</footer>
        
