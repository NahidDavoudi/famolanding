const { default: API, onReady } = await import(`${window.APP_CONFIG.assetUrl}/js/api.js`);

// Escape dynamic text before injecting into HTML: & < > " '
function esc(value) {
    return String(value ?? '').replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
}

const DASHBOARD_URL = (window.APP_CONFIG && window.APP_CONFIG.dashboardUrl) || '';
const UPLOADS_BASE = (window.APP_CONFIG && window.APP_CONFIG.uploadsUrl) || 'https://api.famoacademy.ir/uploads';

let mobileMenuBtn = null;
let mobileMenu = null;

function initMobileMenu() {
    mobileMenuBtn = document.getElementById('mobileMenuBtn');
    mobileMenu = document.getElementById('mobileMenu');
    if (!mobileMenuBtn || !mobileMenu) return;

    mobileMenuBtn.addEventListener('click', (event) => {
        event.stopPropagation();
        const isHidden = mobileMenu.classList.contains('hidden');
        mobileMenu.classList.toggle('hidden');
        mobileMenuBtn.innerHTML = isHidden
            ? svgIcon('icon-x', 'icon--lg')
            : svgIcon('icon-menu', 'icon--lg');
        mobileMenuBtn.setAttribute('aria-expanded', isHidden ? 'true' : 'false');
    });

    document.addEventListener('click', (event) => {
        if (!mobileMenu.contains(event.target) && !mobileMenuBtn.contains(event.target)) {
            mobileMenu.classList.add('hidden');
            mobileMenuBtn.innerHTML = svgIcon('icon-menu', 'icon--lg');
            mobileMenuBtn.setAttribute('aria-expanded', 'false');
        }
    });
}

function svgIcon(name, cls = '') {
    const suffix = cls ? ` ${cls}` : '';
    const icon = window.famoLucideName ? window.famoLucideName(name) : name.replace(/^icon-/, '');
    return `<i class="icon${suffix}" data-lucide="${icon}" aria-hidden="true"></i>`;
}

// جداکردن سه‌رقم سه‌رقم با ارقام فارسی: 2500000 → ۲,۵۰۰,۰۰۰
function formatPrice(value) {
    if (value === null || value === undefined || value === '') return '';
    const persianDigits = '۰۱۲۳۴۵۶۷۸۹';
    let str = String(value).trim();
    // تبدیل ارقام فارسی/عربی به انگلیسی
    str = str
        .replace(/[۰-۹]/g, d => persianDigits.indexOf(d))
        .replace(/[٠-٩]/g, d => '٠١٢٣٤٥٦٧٨٩'.indexOf(d));
    const digits = str.replace(/\D/g, '');
    if (!digits) return String(value); // مثل «تماس بگیرید» را دست نمی‌زنیم
    // جداکردن سه‌رقم سه‌رقم
    const grouped = digits.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    // تبدیل به ارقام فارسی
    return grouped.replace(/\d/g, d => persianDigits[+d]);
}

// آیا قیمت عددی است؟ (برای نمایش واحد «تومان»)
function isNumericPrice(value) {
    if (value === null || value === undefined || value === '') return false;
    return /\d/.test(String(value).replace(/[۰-۹]/g, '0').replace(/[٠-٩]/g, '0'));
}

// آدرس تصویر دوره: آدرس کامل، مسیر نسبی یا فقط نام فایل در پوشه uploads
function resolveImageUrl(value) {
    if (!value) return '';
    const src = String(value).trim();
    if (!src) return '';
    if (/^(https?:)?\/\//i.test(src) || src.startsWith('data:')) return src;
    if (src.startsWith('/')) return src;
    if (src.startsWith('uploads/') || src.startsWith('../') || src.startsWith('assets/')) return src;
    return `${UPLOADS_BASE.replace(/\/$/, '')}/${src}`;
}

// FontAwesome → نام آیکون داخلی (برای حالت بدون تصویر)
const FA_ICON_MAP = {
    'fa-home': 'home', 'fa-book': 'book', 'fa-book-open': 'book',
    'fa-school': 'school', 'fa-graduation-cap': 'school', 'fa-user-graduate': 'school',
    'fa-chalkboard-teacher': 'school', 'fa-chart-line': 'chart-bar', 'fa-chart-bar': 'chart-bar',
    'fa-user': 'user', 'fa-users': 'users', 'fa-user-friends': 'users',
    'fa-calendar': 'calendar', 'fa-calendar-alt': 'calendar',
    'fa-star': 'star', 'fa-headset': 'headset', 'fa-phone': 'phone',
    'fa-envelope': 'mail', 'fa-search': 'search', 'fa-check': 'check',
    'fa-clock': 'clock', 'fa-calculator': 'chart-bar', 'fa-flask': 'report',
    'fa-atom': 'star', 'fa-brain': 'school', 'fa-clipboard': 'clipboard',
    'fa-globe': 'external-link', 'fa-laptop-code': 'settings', 'fa-paint-brush': 'edit',
};

function faToSvgName(faClass) {
    if (!faClass) return 'book';
    for (const part of String(faClass).split(/\s+/)) {
        if (FA_ICON_MAP[part]) return FA_ICON_MAP[part];
    }
    return 'book';
}

function renderFeature(text) {
    return `<li>${svgIcon('icon-check', 'icon--sm text-[#445D84]')}<span>${esc(text)}</span></li>`;
}

// گرادیان فقط با hex معتبر؛ در غیر این صورت رنگ ثابت برند
function safeHex(value, fallback) {
    const s = String(value ?? '').trim();
    return /^#([0-9a-f]{3}|[0-9a-f]{6})$/i.test(s) ? s : fallback;
}

function renderCourseCard(course) {
    const hasPrice = course.price !== null && course.price !== undefined && String(course.price).trim() !== '';
    const priceText = hasPrice ? formatPrice(course.price) : '';
    const priceUnit = hasPrice && isNumericPrice(course.price) ? 'تومان' : '';
    const imageUrl = resolveImageUrl(course.background_image_url || course.image_url || course.image);
    const gradientFrom = safeHex(course.gradient_color_from, '#445D84');
    const gradientTo = safeHex(course.gradient_color_to, '#6D8B9E');
    const bodyText = course.full_description || course.description || '';
    const featuresHtml = (course.features || []).map(renderFeature).join('');
    const featuresBlock = featuresHtml
        ? `<ul class="course-features">${featuresHtml}</ul>`
        : '';
    const badgeBlock = course.badge_label
        ? `<span class="course-badge">${esc(course.badge_label)}</span>`
        : '';
    const metaItems = [
        course.target_grades ? `<span>${esc(course.target_grades)}</span>` : '',
        course.format ? `<span>${esc(course.format)}</span>` : '',
    ].filter(Boolean).join('');
    const metaBlock = metaItems ? `<div class="course-meta">${metaItems}</div>` : '';

    return `<article id="course-${course.id}" class="course-card">
        <!-- تصویر دوره (بدون overlay) -->
        <div class="course-image-wrapper" style="background: linear-gradient(135deg, ${gradientFrom}, ${gradientTo});">
            ${imageUrl
                ? `<img src="${esc(imageUrl)}" alt="${esc(course.name)}" loading="lazy" class="course-image">`
                : `<div class="course-image-fallback">${svgIcon(`icon-${faToSvgName(course.icon)}`, 'icon--3xl text-white/90')}</div>`}
            ${badgeBlock}
        </div>

        <!-- نام، توضیحات و قیمت زیر تصویر -->
        <div class="course-body">
            <h2 class="course-name">${esc(course.name)}</h2>
            ${metaBlock}
            ${bodyText ? `<p class="course-desc">${esc(bodyText)}</p>` : ''}
            ${featuresBlock}

            <div class="course-footer">
                <div class="course-price-box">
                    <span class="course-price-label">هزینه دوره</span>
                    ${hasPrice
                        ? `<span class="course-price-value">${esc(priceText)}${priceUnit ? `<span class="course-price-unit">${esc(priceUnit)}</span>` : ''}</span>`
                        : `<span class="course-price-value">تماس بگیرید</span>`}
                </div>
                <a href="${DASHBOARD_URL}/" class="course-cta">مشاوره و ثبت‌نام</a>
            </div>
        </div>
    </article>`;
}

function updateCourseSchema(courses) {
    const schemaTag = document.getElementById('courseSchema');
    if (!schemaTag) return;
    try {
        const schema = JSON.parse(schemaTag.textContent);
        schema.itemListElement = courses.map((course, index) => ({
            '@type': 'ListItem', position: index + 1, name: course.name
        }));
        schemaTag.textContent = JSON.stringify(schema);
    } catch (error) {
        console.error('Error updating course schema:', error);
    }
}

async function loadCourses() {
    const container = document.getElementById('coursesContainer');
    if (!container) return;
    try {
        const result = await API.get('/public/courses');
        const courses = result.data || [];
        if (courses.length === 0) {
            container.innerHTML = '<div class="famo-empty-message">در حال حاضر دوره‌ای ثبت نشده است</div>';
            return;
        }
        container.innerHTML = courses.map(renderCourseCard).join('');
        if (window.refreshLucideIcons) window.refreshLucideIcons(container);
        updateCourseSchema(courses);
    } catch (error) {
        console.error('Error loading courses:', error);
        container.innerHTML = '<div class="famo-empty-message">خطا در بارگذاری دوره‌ها</div>';
    }
}

onReady(() => {
    initMobileMenu();
    loadCourses();
});

// Set current Persian year in footer
const copyrightYear = document.getElementById('copyright-year');
if (copyrightYear) {
    const persianYear = new Date().toLocaleDateString('fa-IR', { year: 'numeric' });
    copyrightYear.textContent = persianYear.replace(/[۰-۹]/g, d => '۰۱۲۳۴۵۶۷۸۹'.indexOf(d));
}
