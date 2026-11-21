<?php
require_once __DIR__ . '/../config.php';
$base = '../';
$baseEsc = htmlspecialchars($base, ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>نظرات شاگردان و اولیا | آموزشگاه فامو بابل</title>
    <meta name="description"
        content="نظرات تأییدشده دانش‌آموزان، فارغ‌التحصیلان و اولیا درباره کلاس‌ها، مشاوره و آزمون‌های آموزشگاه فامو بابل و فرم ثبت نظر جدید.">
    <?php include __DIR__ . '/../layouts/head-common.php'; ?>
</head>

<body class="font-family text-gray-800 bg-[#f9f7f3]">

<!-- Header -->
<?php include __DIR__ . '/../layouts/header.php'; ?>

<main>
    <!-- Hero Banner -->
    <section class="pt-28 sm:pt-32 pb-12 gradient-bg text-white text-center">
        <div class="container mx-auto max-w-6xl px-4">
            <h1 class="text-3xl sm:text-4xl font-bold mb-4">نظرات شاگردان و اولیا</h1>
            <p class="text-white/85 max-w-2xl mx-auto leading-relaxed">
                بازخورد واقعی دانش‌آموزان، فارغ‌التحصیلان و خانواده‌ها از همراهی با تیم آموزشی و مشاوره فامو
            </p>
        </div>
    </section>

    <!-- Summary & Approved Testimonials List -->
    <section id="testimonials-list-section" class="py-12 sm:py-16 px-4">
        <div class="container mx-auto max-w-6xl">
            <div id="testimonialsSummary" class="famo-testi-summary" aria-live="polite">
                <div class="famo-testi-summary-grid" aria-hidden="true">
                    <div class="famo-testi-stat">
                        <div class="skeleton-shimmer famo-testi-skel-stat"></div>
                        <div class="famo-testi-stat-label">نظر تأییدشده</div>
                    </div>
                    <div class="famo-testi-stat">
                        <div class="skeleton-shimmer famo-testi-skel-stat"></div>
                        <div class="famo-testi-stat-label">میانگین امتیاز رضایت</div>
                    </div>
                    <div class="famo-testi-stat-cta">
                        <a href="#submit-testimonial" class="famo-state-btn">ثبت نظر جدید</a>
                    </div>
                </div>
            </div>

            <div class="famo-testi-list-header">
                <h2 class="text-2xl font-bold text-[#445D84] border-r-4 border-[#445D84] pr-3">نظرات ثبت‌شده</h2>
                <div id="testimonialsRoleFilters" class="famo-filter-chips" aria-label="فیلتر بر اساس نقش نویسنده"></div>
            </div>

            <div id="testimonialsList" class="famo-testi-grid" aria-busy="true">
                <!-- Skeleton loaders until API responds -->
                <div class="famo-testi-card famo-testi-skel" aria-hidden="true">
                    <div class="famo-testi-card-head">
                        <div class="skeleton-shimmer famo-testi-skel-avatar"></div>
                        <div class="famo-adm-skel-lines">
                            <div class="skeleton-shimmer famo-adm-skel-line famo-adm-skel-line--name"></div>
                            <div class="skeleton-shimmer famo-adm-skel-line famo-adm-skel-line--result"></div>
                        </div>
                    </div>
                    <div class="skeleton-shimmer famo-testi-skel-body"></div>
                    <div class="skeleton-shimmer famo-testi-skel-body famo-testi-skel-body--short"></div>
                </div>
                <div class="famo-testi-card famo-testi-skel" aria-hidden="true">
                    <div class="famo-testi-card-head">
                        <div class="skeleton-shimmer famo-testi-skel-avatar"></div>
                        <div class="famo-adm-skel-lines">
                            <div class="skeleton-shimmer famo-adm-skel-line famo-adm-skel-line--name"></div>
                            <div class="skeleton-shimmer famo-adm-skel-line famo-adm-skel-line--result"></div>
                        </div>
                    </div>
                    <div class="skeleton-shimmer famo-testi-skel-body"></div>
                    <div class="skeleton-shimmer famo-testi-skel-body famo-testi-skel-body--short"></div>
                </div>
                <div class="famo-testi-card famo-testi-skel" aria-hidden="true">
                    <div class="famo-testi-card-head">
                        <div class="skeleton-shimmer famo-testi-skel-avatar"></div>
                        <div class="famo-adm-skel-lines">
                            <div class="skeleton-shimmer famo-adm-skel-line famo-adm-skel-line--name"></div>
                            <div class="skeleton-shimmer famo-adm-skel-line famo-adm-skel-line--result"></div>
                        </div>
                    </div>
                    <div class="skeleton-shimmer famo-testi-skel-body"></div>
                    <div class="skeleton-shimmer famo-testi-skel-body famo-testi-skel-body--short"></div>
                </div>
            </div>

            <div id="testimonialsPagination" class="famo-pagination-wrap"></div>
        </div>
    </section>

    <!-- Submit Testimonial Form Section -->
    <section id="submit-testimonial" class="py-12 sm:py-16 px-4 bg-white">
        <div class="container mx-auto max-w-3xl">
            <div class="famo-form-card">
                <div class="famo-form-header">
                    <h2 class="text-2xl font-bold text-[#445D84]">ثبت نظر جدید</h2>
                    <p class="famo-form-subtitle">
                        تجربه خود را از کلاس‌ها، آزمون‌ها یا مشاوره فامو بنویسید. نظرات ارسال‌شده پس از بررسی توسط تیم آموزشگاه در سایت نمایش داده می‌شوند.
                    </p>
                </div>

                <form id="testimonialForm" class="famo-form" novalidate>
                    <!-- Anti-spam honeypot field: visually hidden via off-screen CSS, must stay empty -->
                    <div class="famo-hp-field" aria-hidden="true">
                        <label for="testimonial-website">وب‌سایت</label>
                        <input type="text" id="testimonial-website" name="website" value="" tabindex="-1" autocomplete="off" aria-hidden="true">
                    </div>

                    <div class="famo-form-grid">
                        <div class="famo-field">
                            <label for="testimonial-author_name" class="famo-label">
                                نام و نام خانوادگی <span class="famo-required" aria-hidden="true">*</span>
                            </label>
                            <input
                                type="text"
                                id="testimonial-author_name"
                                name="author_name"
                                class="famo-input"
                                required
                                minlength="2"
                                maxlength="120"
                                autocomplete="name"
                                placeholder="مثلاً: سارا محمدی"
                                aria-describedby="err-author_name">
                            <p id="err-author_name" class="famo-field-error" role="alert" hidden></p>
                        </div>

                        <div class="famo-field">
                            <label for="testimonial-author_role" class="famo-label">
                                نقش شما
                            </label>
                            <select
                                id="testimonial-author_role"
                                name="author_role"
                                class="famo-input famo-select"
                                aria-describedby="err-author_role">
                                <option value="student" selected>دانش‌آموز</option>
                                <option value="parent">ولی دانش‌آموز</option>
                                <option value="graduate">فارغ‌التحصیل</option>
                                <option value="other">سایر</option>
                            </select>
                            <p id="err-author_role" class="famo-field-error" role="alert" hidden></p>
                        </div>
                    </div>

                    <div class="famo-field">
                        <label for="testimonial-author_meta" class="famo-label">
                            توضیح کوتاه <span class="famo-optional">(اختیاری)</span>
                        </label>
                        <input
                            type="text"
                            id="testimonial-author_meta"
                            name="author_meta"
                            class="famo-input"
                            maxlength="150"
                            placeholder="مثلاً: ولی دوازدهم تجربی یا دانش‌آموز یازدهم ریاضی"
                            aria-describedby="err-author_meta">
                        <p id="err-author_meta" class="famo-field-error" role="alert" hidden></p>
                    </div>

                    <fieldset class="famo-field famo-rating-fieldset" aria-describedby="err-rating">
                        <legend class="famo-label">
                            امتیاز شما <span class="famo-required" aria-hidden="true">*</span>
                        </legend>
                        <div class="famo-rating-row">
                            <div id="testimonialRatingPicker" class="famo-rating-picker">
                                <label class="famo-rating-option" data-rating="1">
                                    <input type="radio" name="rating" value="1" class="famo-sr-radio" aria-label="۱ ستاره">
                                    <span class="famo-rating-star-slot"></span>
                                </label>
                                <label class="famo-rating-option" data-rating="2">
                                    <input type="radio" name="rating" value="2" class="famo-sr-radio" aria-label="۲ ستاره">
                                    <span class="famo-rating-star-slot"></span>
                                </label>
                                <label class="famo-rating-option" data-rating="3">
                                    <input type="radio" name="rating" value="3" class="famo-sr-radio" aria-label="۳ ستاره">
                                    <span class="famo-rating-star-slot"></span>
                                </label>
                                <label class="famo-rating-option" data-rating="4">
                                    <input type="radio" name="rating" value="4" class="famo-sr-radio" aria-label="۴ ستاره">
                                    <span class="famo-rating-star-slot"></span>
                                </label>
                                <label class="famo-rating-option" data-rating="5">
                                    <input type="radio" name="rating" value="5" class="famo-sr-radio" aria-label="۵ ستاره" checked>
                                    <span class="famo-rating-star-slot"></span>
                                </label>
                            </div>
                            <span id="testimonialRatingText" class="famo-rating-hint">۵ از ۵ ستاره</span>
                        </div>
                        <p id="err-rating" class="famo-field-error" role="alert" hidden></p>
                    </fieldset>

                    <div class="famo-field">
                        <div class="famo-label-row">
                            <label for="testimonial-content" class="famo-label">
                                متن نظر شما <span class="famo-required" aria-hidden="true">*</span>
                            </label>
                            <span id="testimonialContentCounter" class="famo-char-counter">۰ / ۱۵۰۰ کاراکتر</span>
                        </div>
                        <textarea
                            id="testimonial-content"
                            name="content"
                            rows="5"
                            class="famo-input famo-textarea"
                            required
                            minlength="10"
                            maxlength="1500"
                            placeholder="تجربه خود را از کیفیت آموزش، برنامه‌ریزی یا پشتیبانی فامو بنویسید (حداقل ۱۰ کاراکتر)..."
                            aria-describedby="err-content"></textarea>
                        <p id="err-content" class="famo-field-error" role="alert" hidden></p>
                    </div>

                    <div id="testimonialFormStatus" class="famo-form-status" role="status" aria-live="polite" hidden></div>

                    <div class="famo-form-actions">
                        <button type="submit" id="testimonialSubmitBtn" class="famo-submit-btn">
                            ثبت نظر
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>
</main>

<!-- Footer -->
<?php include __DIR__ . '/../layouts/footer.php'; ?>

<script type="module" src="<?= $baseEsc ?>assets/js/testimonials.js"></script>
</body>
</html>
