<?php
declare(strict_types=1);
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../layouts/home-render.php';

$base = '/';
$baseEsc = htmlspecialchars($base, ENT_QUOTES, 'UTF-8');
$siteUrl = famo_public_site_url();
$resultsUrl = famo_canonical_url('results/');

$pageTitle = 'قبولی‌های کنکور و تیزهوشان و نظرات خانواده‌ها | آموزشگاه فامو بابل';
$metaDescription = 'نتایج مستند قبولی‌های کنکور سراسری، تیزهوشان، نمونه دولتی و المپیادهای علمی به همراه نظرات تأییدشده دانش‌آموزان و اولیا درباره آموزشگاه فامو بابل.';
$canonicalPath = 'results/';
$webPageType = 'CollectionPage';
$breadcrumbs = [
    ['name' => 'خانه', 'url' => famo_canonical_url('')],
    ['name' => 'نتایج، قبولی‌ها و نظرات', 'url' => $resultsUrl],
];

// Fail-soft SSR hydration for Admissions & Testimonials so crawlers never see an empty/error page
$admApiRes = famo_internal_api_get('/public/admissions?group_by=year', 2);
$admData = is_array($admApiRes['json']['data'] ?? null) ? $admApiRes['json']['data'] : [];
$ssrAdmGroups = is_array($admData['groups'] ?? null) ? array_values(array_filter($admData['groups'], 'is_array')) : [];
$hasAdmSsr = count($ssrAdmGroups) > 0;

$testiApiRes = famo_internal_api_get('/public/testimonials?page=1&per_page=9', 2);
$testiData = is_array($testiApiRes['json']['data'] ?? null) ? $testiApiRes['json']['data'] : [];
$testiSummary = is_array($testiData['summary'] ?? ($testiApiRes['json']['summary'] ?? null))
    ? ($testiData['summary'] ?? $testiApiRes['json']['summary'])
    : null;
$ssrTestiItems = is_array($testiData['items'] ?? null) ? array_values(array_filter($testiData['items'], 'is_array')) : [];
$hasTestiSsr = count($ssrTestiItems) > 0;

if ($testiSummary !== null) {
    $totalApproved = (int) ($testiSummary['total_approved'] ?? 0);
    $avgRating = (float) ($testiSummary['average_rating'] ?? 0);
    if ($totalApproved > 0 && $avgRating > 0) {
        $organizationExtra = [
            'aggregateRating' => [
                '@type'       => 'AggregateRating',
                'ratingValue' => round($avgRating, 2),
                'reviewCount' => $totalApproved,
                'bestRating'  => 5,
                'worstRating' => 1,
            ],
        ];
    }
}

$examTypeLabels = [
    'konkur'     => 'کنکور سراسری',
    'tizhooshan' => 'تیزهوشان',
    'nemoone'    => 'نمونه دولتی',
    'olympiad'   => 'المپیاد علمی',
    'other'      => 'سایر',
];

$roleLabels = [
    'student'  => 'دانش‌آموز',
    'parent'   => 'ولی دانش‌آموز',
    'graduate' => 'فارغ‌التحصیل',
    'other'    => 'همراه فامو',
];

$toFaDigits = static function (string $s): string {
    return strtr($s, ['0' => '۰', '1' => '۱', '2' => '۲', '3' => '۳', '4' => '۴', '5' => '۵', '6' => '۶', '7' => '۷', '8' => '۸', '9' => '۹']);
};

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
<body class="font-family text-gray-800 bg-[#f9f7f3]">

<?php include __DIR__ . '/../layouts/header.php'; ?>

<main id="main-content">
    <!-- Hero Banner -->
    <section class="famo-page-hero gradient-bg text-white text-center">
        <div class="container mx-auto max-w-5xl px-4">
            <p class="famo-eyebrow">نتایج مستند و بازخوردهای واقعی | بابل</p>
            <h1 class="text-3xl sm:text-4xl font-bold mb-4 text-white">قبولی‌های کنکور و تیزهوشان و نظرات خانواده‌ها</h1>
            <p class="famo-on-dark-soft max-w-3xl mx-auto leading-relaxed text-base sm:text-lg mb-6">
                کارنامه واقعی هر مجموعه آموزشی در نتایج مستند دانش‌آموزان و تجربه خانواده‌ها دیده می‌شود. در این صفحه فهرست قبولی‌های ثبت‌شده در سامانه فامو و بازخورد تأییدشده دانش‌آموزان و اولیا را مشاهده می‌کنید.
            </p>
            <div class="flex flex-wrap justify-center gap-3">
                <a href="#admissions" class="famo-btn-cream font-bold px-6 py-2.5 rounded-full text-sm transition">
                    مشاهده قبولی‌ها
                </a>
                <a href="#testimonials" class="famo-btn-outline-cream font-bold px-6 py-2.5 rounded-full text-sm transition">
                    نظرات شاگردان و اولیا
                </a>
                <a href="#submit-testimonial" class="famo-btn-outline-cream font-bold px-6 py-2.5 rounded-full text-sm transition">
                    ثبت نظر جدید
                </a>
            </div>
        </div>
    </section>

    <!-- Section 1: Admissions -->
    <section id="admissions" class="py-12 sm:py-16 px-4">
        <div class="container mx-auto max-w-6xl">
            <div class="mb-8">
                <span class="famo-eyebrow famo-eyebrow--light">مستندات قبولی‌ها</span>
                <h2 class="text-2xl sm:text-3xl font-bold text-[#445D84] border-r-4 border-[#445D84] pr-3 mb-2">
                    قبولی‌های کنکور، تیزهوشان و مدارس برتر
                </h2>
                <p class="text-gray-600">
                    نتایج دانش‌آموزان آموزشگاه فامو بابل به تفکیک سال شمسی و نوع آزمون (کنکور سراسری، تیزهوشان، نمونه دولتی و المپیاد).
                </p>
            </div>

            <div class="famo-filters-bar" aria-label="فیلترهای نمایش قبولی‌ها">
                <div class="famo-filter-row">
                    <span class="famo-filter-label">انتخاب سال:</span>
                    <div id="admissionsYearTabs" class="famo-filter-tabs" role="tablist" aria-label="فیلتر سال شمسی"></div>
                </div>
                <div class="famo-filter-row">
                    <span class="famo-filter-label">نوع آزمون:</span>
                    <div id="admissionsExamFilters" class="famo-filter-chips" aria-label="فیلتر نوع آزمون"></div>
                </div>
            </div>

            <div id="admissionsContainer" class="famo-adm-groups" aria-busy="false"<?= $hasAdmSsr ? ' data-ssr="1"' : ' data-has-fallback="1"' ?>>
<?php if ($hasAdmSsr): ?>
                <?php foreach ($ssrAdmGroups as $group): ?>
                    <?php
                    $items = is_array($group['items'] ?? null) ? $group['items'] : [];
                    if (count($items) === 0) {
                        continue;
                    }
                    $yearLabel = $toFaDigits(famo_str($group['jalali_year'] ?? ''));
                    $cntLabel = $toFaDigits((string) count($items));
                    ?>
                    <section class="famo-adm-group">
                        <div class="famo-adm-group-head">
                            <h3 class="famo-adm-group-title">قبولی‌های سال <?= famo_esc($yearLabel) ?></h3>
                            <span class="famo-adm-group-count"><?= famo_esc($cntLabel) ?> قبولی</span>
                        </div>
                        <div class="famo-adm-grid">
                            <?php foreach ($items as $item): ?>
                                <?php
                                if (!is_array($item)) {
                                    continue;
                                }
                                $sName = famo_str($item['student_name'] ?? '');
                                $sRes = $toFaDigits(famo_str($item['result_title'] ?? ''));
                                $examKey = famo_str($item['exam_type'] ?? 'other');
                                $examLbl = $examTypeLabels[$examKey] ?? 'سایر';
                                $rankRaw = trim(famo_str($item['rank_value'] ?? ''));
                                $fieldRaw = trim(famo_str($item['field'] ?? ''));
                                $quoteRaw = trim(famo_str($item['quote'] ?? ''));
                                $initial = famo_first_char($sName);
                                ?>
                                <article class="famo-adm-card hover-lift">
                                    <div class="famo-adm-card-head">
                                        <div class="famo-adm-avatar-wrap">
                                            <div class="famo-adm-avatar-ph" aria-hidden="true"><?= famo_esc($initial) ?></div>
                                        </div>
                                        <div class="famo-adm-identity">
                                            <h4 class="famo-adm-name"><?= famo_esc($sName) ?></h4>
                                            <p class="famo-adm-result"><?= famo_esc($sRes) ?></p>
                                        </div>
                                    </div>
                                    <div class="famo-adm-meta">
                                        <span class="famo-adm-chip famo-adm-chip--exam"><?= famo_esc($examLbl) ?></span>
                                        <?php if ($rankRaw !== ''): ?>
                                            <span class="famo-adm-chip famo-adm-chip--rank"><?= famo_esc($toFaDigits(str_starts_with($rankRaw, 'رتبه') ? $rankRaw : 'رتبه ' . $rankRaw)) ?></span>
                                        <?php endif; ?>
                                        <?php if ($fieldRaw !== ''): ?>
                                            <span class="famo-adm-chip famo-adm-chip--field"><?= famo_esc($fieldRaw) ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <?php if ($quoteRaw !== ''): ?>
                                        <blockquote class="famo-adm-quote">«<?= famo_esc($quoteRaw) ?>»</blockquote>
                                    <?php endif; ?>
                                </article>
                            <?php endforeach; ?>
                        </div>
                    </section>
                <?php endforeach; ?>
<?php else: ?>
                <!-- Fail-soft crawlable summary shown when API is unreachable -->
                <div class="famo-detail-block">
                    <h3 class="text-xl font-bold text-[#445D84] mb-3">حوزه‌های ارزیابی و قبولی‌های دانش‌آموزان فامو در بابل</h3>
                    <p class="text-gray-600 leading-relaxed mb-5">
                        فهرست تفصیلی قبولی‌های دانش‌آموزان آموزشگاه فامو در رشته‌های پزشکی، دندانپزشکی، داروسازی، مهندسی دانشگاه‌های دولتی، مدارس استعدادهای درخشان (شهید بهشتی و فرزانگان بابل) و مدارس نمونه دولتی به تفکیک سال در سامانه فامو ثبت و به‌روزرسانی می‌شود.
                    </p>
                    <div class="flex flex-wrap gap-3">
                        <a href="/courses/konkur-babol/" class="famo-anchor-pill">برنامه آمادگی کنکور بابل</a>
                        <a href="/courses/tizhooshan-babol/" class="famo-anchor-pill">برنامه آمادگی تیزهوشان بابل</a>
                        <a href="/contact/#consultation" class="famo-anchor-pill">درخواست مشاوره حضوری</a>
                    </div>
                </div>
<?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Section 2: Testimonials -->
    <section id="testimonials" class="py-12 sm:py-16 px-4 bg-white border-t border-[#E2D9C6]">
        <div class="container mx-auto max-w-6xl" id="testimonials-list-section">
            <div class="mb-8">
                <span class="famo-eyebrow famo-eyebrow--light">دیدگاه اولیا و دانش‌آموزان</span>
                <h2 class="text-2xl sm:text-3xl font-bold text-[#445D84] border-r-4 border-[#445D84] pr-3 mb-2">
                    نظرات تأییدشده شاگردان و خانواده‌ها
                </h2>
                <p class="text-gray-600">
                    تجربه واقعی دانش‌آموزان، فارغ‌التحصیلان و اولیا درباره کیفیت کلاس‌های خصوصی، آزمون‌های شخصی‌سازی‌شده و مشاوره فامو.
                </p>
            </div>

            <div id="testimonialsSummary" class="famo-testi-summary" aria-live="polite">
                <div class="famo-testi-summary-grid">
                    <div class="famo-testi-stat">
                        <div class="famo-testi-stat-value"><?= $testiSummary ? famo_esc($toFaDigits((string) ((int) ($testiSummary['total_approved'] ?? 0)))) : 'ثبت‌شده' ?></div>
                        <div class="famo-testi-stat-label">بازخورد تأییدشده در سامانه</div>
                    </div>
                    <div class="famo-testi-stat">
                        <div class="famo-testi-stat-value"><?= ($testiSummary && (float) ($testiSummary['average_rating'] ?? 0) > 0) ? famo_esc($toFaDigits(number_format((float) $testiSummary['average_rating'], 1))) : 'شفاف' ?></div>
                        <div class="famo-testi-stat-label">میانگین امتیاز رضایت خانواده‌ها</div>
                    </div>
                    <div class="famo-testi-stat-cta">
                        <a href="#submit-testimonial" class="famo-state-btn">ثبت نظر جدید</a>
                    </div>
                </div>
            </div>

            <div class="famo-testi-list-header">
                <h3 class="text-xl font-bold text-[#445D84] border-r-4 border-[#445D84] pr-3">بازخوردهای ثبت‌شده</h3>
                <div id="testimonialsRoleFilters" class="famo-filter-chips" aria-label="فیلتر بر اساس نقش نویسنده"></div>
            </div>

            <div id="testimonialsList" class="famo-testi-grid" aria-busy="false"<?= $hasTestiSsr ? ' data-ssr="1"' : ' data-has-fallback="1"' ?>>
<?php if ($hasTestiSsr): ?>
                <?php foreach ($ssrTestiItems as $item): ?>
                    <?php
                    $aName = famo_str($item['author_name'] ?? '');
                    $aRole = $roleLabels[famo_str($item['author_role'] ?? '')] ?? 'همراه فامو';
                    $aMeta = trim(famo_str($item['author_meta'] ?? ''));
                    $aBody = famo_str($item['content'] ?? '');
                    ?>
                    <article class="famo-testi-card hover-lift">
                        <div class="famo-testi-card-head">
                            <div class="famo-testi-author">
                                <div class="famo-testi-avatar" aria-hidden="true"><?= famo_esc(famo_first_char($aName)) ?></div>
                                <div class="famo-testi-author-info">
                                    <div class="famo-testi-name-row">
                                        <h4 class="famo-testi-name"><?= famo_esc($aName) ?></h4>
                                        <span class="famo-testi-role-badge"><?= famo_esc($aRole) ?></span>
                                    </div>
                                    <?php if ($aMeta !== ''): ?>
                                        <p class="famo-testi-meta"><?= famo_esc($toFaDigits($aMeta)) ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <p class="famo-testi-content"><?= famo_esc($aBody) ?></p>
                    </article>
                <?php endforeach; ?>
<?php else: ?>
                <div class="col-span-full famo-detail-block">
                    <p class="text-gray-700 leading-relaxed m-0">
                        تمامی دیدگاه‌های اولیا، دانش‌آموزان و فارغ‌التحصیلان پس از بررسی اصالت توسط تیم آموزشگاه فامو در این بخش منتشر می‌شوند. می‌توانید تجربه خود را از طریق فرم زیر ثبت کنید.
                    </p>
                </div>
<?php endif; ?>
            </div>

            <div id="testimonialsPagination" class="famo-pagination-wrap"></div>
        </div>
    </section>

    <!-- Section 3: Submit Testimonial Form -->
    <section id="submit-testimonial" class="py-12 sm:py-16 px-4 bg-[#f9f7f3] border-t border-[#E2D9C6]">
        <div class="container mx-auto max-w-3xl">
            <div class="famo-form-card">
                <div class="famo-form-header">
                    <h2 class="text-2xl font-bold text-[#445D84]">ثبت نظر جدید</h2>
                    <p class="famo-form-subtitle">
                        تجربه خود را از کلاس‌ها، آزمون‌ها یا مشاوره فامو بنویسید. نظرات ارسال‌شده پس از بررسی توسط تیم آموزشگاه در سایت نمایش داده می‌شوند.
                    </p>
                </div>

                <form id="testimonialForm" class="famo-form" novalidate>
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
                            <label for="testimonial-author_role" class="famo-label">نقش شما</label>
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

<?php include __DIR__ . '/../layouts/footer.php'; ?>

<script type="module" src="<?= htmlspecialchars(famo_local_asset('assets/js/admissions.js', $base), ENT_QUOTES, 'UTF-8') ?>"></script>
<script type="module" src="<?= htmlspecialchars(famo_local_asset('assets/js/testimonials.js', $base), ENT_QUOTES, 'UTF-8') ?>"></script>
</body>
</html>
