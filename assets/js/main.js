const { default: API, onReady } = await import(`${window.APP_CONFIG.assetUrl}/js/api.js`);

const UPLOADS_BASE = (window.APP_CONFIG && window.APP_CONFIG.uploadsUrl) || 'https://api.famoacademy.ir/uploads';

// Escape dynamic text before injecting into HTML: & < > " '
function esc(value) {
    return String(value ?? '').replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
}

// Only allow https:// or /-rooted URLs (same-origin relative paths) into markup.
function safeUrl(value) {
    const s = String(value ?? '').trim();
    return (/^https?:\/\//i.test(s) || s.startsWith('/')) ? s : '';
}

// Only accept hex colors for gradients; otherwise fall back to the brand color.
function safeHex(value, fallback) {
    const s = String(value ?? '').trim();
    return /^#([0-9a-f]{3}|[0-9a-f]{6})$/i.test(s) ? s : fallback;
}

// Course image resolver: full URL, root-relative path, or bare filename in uploads.
// ponytail: کپی resolveImageUrl از courses.js؛ وقتی آپدیت shared ماژول شد یکپارچه شود
function resolveImageUrl(value) {
    if (!value) return '';
    const src = String(value).trim();
    if (!src) return '';
    if (/^(https?:)?\/\//i.test(src) || src.startsWith('data:')) return src;
    if (src.startsWith('/')) return src;
    if (src.startsWith('uploads/') || src.startsWith('../') || src.startsWith('assets/')) return src;
    return `${UPLOADS_BASE.replace(/\/$/, '')}/${src}`;
}

// Helper: create a Lucide icon element.
function svgIcon(name, cls = '') {
    const c = cls ? ` ${cls}` : '';
    const icon = window.famoLucideName ? window.famoLucideName(name) : name.replace(/^icon-/, '');
    return `<i class="icon${c}" data-lucide="${icon}" aria-hidden="true"></i>`;
}

// Format price: Persian digits + thousands separator (unit is added in markup)
// e.g. 2,500,000 -> "۲,۵۰۰,۰۰۰"
function formatPrice(value) {
    if (value === null || value === undefined || value === '') return '';
    const persianDigits = '۰۱۲۳۴۵۶۷۸۹';
    let str = String(value).trim();
    // Convert Persian/Arabic digits to English
    str = str
        .replace(/[۰-۹]/g, d => persianDigits.indexOf(d))
        .replace(/[٠-٩]/g, d => '٠١٢٣٤٥٦٧٨٩'.indexOf(d));
    const digits = str.replace(/\D/g, '');
    if (!digits) return String(value); // keep non-numeric values (e.g. "تماس بگیرید") as is
    // Thousands separator (groups of three)
    const grouped = digits.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    // Convert back to Persian digits
    return grouped.replace(/\d/g, d => persianDigits[+d]);
}

// Is the course price a real number? (so we know whether to show the "تومان" unit)
function isNumericPrice(value) {
    if (value === null || value === undefined || value === '') return false;
    return /\d/.test(String(value).replace(/[۰-۹]/g, '0').replace(/[٠-٩]/g, '0'));
}

// FontAwesome → SVG sprite mapping for dynamic course icons
const FA_ICON_MAP = {
    'fa-home': 'icon-home', 'fa-book': 'icon-book', 'fa-book-open': 'icon-book',
    'fa-school': 'icon-school', 'fa-graduation-cap': 'icon-school', 'fa-user-graduate': 'icon-school',
    'fa-chalkboard-teacher': 'icon-school', 'fa-chart-line': 'icon-chart-bar', 'fa-chart-bar': 'icon-chart-bar',
    'fa-user': 'icon-user', 'fa-users': 'icon-users', 'fa-user-friends': 'icon-users',
    'fa-user-plus': 'icon-user-plus', 'fa-calendar': 'icon-calendar', 'fa-calendar-alt': 'icon-calendar',
    'fa-star': 'icon-star', 'fa-headset': 'icon-headset', 'fa-phone': 'icon-phone',
    'fa-phone-alt': 'icon-phone', 'fa-envelope': 'icon-mail', 'fa-search': 'icon-search',
    'fa-edit': 'icon-edit', 'fa-pen': 'icon-edit', 'fa-pencil-alt': 'icon-edit',
    'fa-trash': 'icon-trash', 'fa-trash-alt': 'icon-trash', 'fa-download': 'icon-download',
    'fa-upload': 'icon-upload', 'fa-file': 'icon-file', 'fa-file-alt': 'icon-file',
    'fa-eye': 'icon-eye', 'fa-check': 'icon-check', 'fa-times': 'icon-x',
    'fa-plus': 'icon-plus', 'fa-cog': 'icon-settings', 'fa-cogs': 'icon-settings',
    'fa-calculator': 'icon-chart-bar', 'fa-flask': 'icon-report', 'fa-atom': 'icon-star',
    'fa-brain': 'icon-school', 'fa-clipboard': 'icon-clipboard', 'fa-clock': 'icon-clock',
    'fa-map-marker-alt': 'icon-map-pin', 'fa-filter': 'icon-filter', 'fa-bars': 'icon-menu',
    'fa-question-circle': 'icon-help-circle', 'fa-microscope': 'icon-search',
    'fa-square-root-alt': 'icon-chart-bar', 'fa-language': 'icon-book',
    'fa-paint-brush': 'icon-edit', 'fa-laptop-code': 'icon-settings',
    'fa-dna': 'icon-report', 'fa-globe': 'icon-external-link',
};

// Convert FontAwesome class string to SVG sprite icon name
function faToSvg(faClass) {
    if (!faClass) return 'icon-book';
    const parts = faClass.split(/\s+/);
    for (const part of parts) {
        if (FA_ICON_MAP[part]) return FA_ICON_MAP[part];
    }
    return 'icon-book'; // fallback
}

// Mobile Menu Toggle - Global variables for menu elements
let mobileMenuBtn = null;
let mobileMenu = null;

function initMobileMenu() {
    mobileMenuBtn = document.getElementById('mobileMenuBtn');
    mobileMenu = document.getElementById('mobileMenu');

    if (mobileMenuBtn && mobileMenu) {
        mobileMenuBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            const isHidden = mobileMenu.classList.contains('hidden');
            mobileMenu.classList.toggle('hidden');
            mobileMenuBtn.innerHTML = isHidden
                ? svgIcon('icon-x', 'icon--lg')
                : svgIcon('icon-menu', 'icon--lg');
            mobileMenuBtn.setAttribute('aria-expanded', isHidden ? 'true' : 'false');
        });

        // Close menu when clicking outside
        document.addEventListener('click', (e) => {
            if (!mobileMenu.contains(e.target) && !mobileMenuBtn.contains(e.target)) {
                if (!mobileMenu.classList.contains('hidden')) {
                    mobileMenu.classList.add('hidden');
                    mobileMenuBtn.innerHTML = svgIcon('icon-menu', 'icon--lg');
                    mobileMenuBtn.setAttribute('aria-expanded', 'false');
                }
            }
        });
    }
}

// Initialize mobile menu when DOM is ready
onReady(initMobileMenu);

// Smooth Scrolling for Anchor Links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();

        const targetId = this.getAttribute('href');
        if (targetId === '#') return;

        const targetElement = document.querySelector(targetId);
        if (targetElement) {
            // Close mobile menu if open
            if (mobileMenu) {
                mobileMenu.classList.add('hidden');
                if (mobileMenuBtn) mobileMenuBtn.innerHTML = svgIcon('icon-menu', 'icon--lg');
            }

            window.scrollTo({
                top: targetElement.offsetTop - 80,
                behavior: 'smooth'
            });
        }
    });
});

// Skeleton Loader Functions
// (کلاس‌های show/hide هیچ‌وقت در CSS تعریف نشده بودند و این توابع عملاً بی‌اثر بودند؛
// حالا از hidden واقعی + aria-busy استفاده می‌شود. در مسیرهای فعلی، innerHTML بلافاصله
// جای اسکلتون‌ها را می‌گیرد؛ hidden فقط برای مسیرهایی است که جایگزینی انجام نمی‌شود.)
function showSkeletons(containerId, skeletonClass) {
    const container = document.getElementById(containerId);
    if (!container) return;

    container.setAttribute('aria-busy', 'true');
    const skeletons = container.querySelectorAll(`.${skeletonClass}`);
    skeletons.forEach(skeleton => {
        skeleton.hidden = false;
    });
}

function hideSkeletons(containerId, skeletonClass) {
    const container = document.getElementById(containerId);
    if (!container) return;

    container.setAttribute('aria-busy', 'false');
    const skeletons = container.querySelectorAll(`.${skeletonClass}`);
    skeletons.forEach(skeleton => {
        skeleton.hidden = true;
    });
}

// Swiper instance for the courses carousel (created once after data renders)
let coursesSwiper = null;

// ScrollTrigger موقعیت‌ها را یک‌بار موقع init اندازه می‌گیرد. بعد از لود API/عکس/فونت ارتفاع صفحه
// عوض می‌شود و باید refresh شود، وگرنه انیمیشن‌ها دیر (یا خیلی دیرتر از دید کاربر) فعال می‌شوند.
let scrollRefreshTimer = null;
function scheduleScrollRefresh() {
    if (typeof ScrollTrigger === 'undefined') return;
    clearTimeout(scrollRefreshTimer);
    scrollRefreshTimer = setTimeout(() => ScrollTrigger.refresh(), 120);
}

function renderCourseFeatureChip(text) {
    return `<span>${esc(text)}</span>`;
}

// Build one course card slide for the home carousel.
// Index: 0-based position; first 3 slides skip lazy loading (visible on load).
function buildCourseSlide(course, index) {
    const gradient = `linear-gradient(135deg, ${safeHex(course.gradient_color_from, '#445D84')}, ${safeHex(course.gradient_color_to, '#6D8B9E')})`;
    const imageUrl = safeUrl(resolveImageUrl(course.background_image_url));
    const badgeBlock = course.badge_label
        ? `<span class="famo-course-badge">${esc(course.badge_label)}</span>`
        : '';
    const metaItems = [
        course.target_grades ? `<span>${esc(course.target_grades)}</span>` : '',
        course.format ? `<span>${esc(course.format)}</span>` : '',
    ].filter(Boolean).join('');
    const metaBlock = metaItems
        ? `<div class="famo-course-meta">${metaItems}</div>`
        : '';
    const description = course.description || '';
    const shouldLazyLoad = index >= 3;
    const imgAttrs = `width="320" height="200" decoding="async"${shouldLazyLoad ? ' loading="lazy"' : ''}`;

    return `
        <div class="swiper-slide">
            <a class="famo-course-card" href="pages/course.php#course-${esc(course.id)}"
               aria-label="${esc(course.name)} - مشاهده جزئیات دوره">
                <div class="famo-course-media" style="background-image: ${gradient};">
                    ${imageUrl
                        ? `<img src="${esc(imageUrl)}" alt="${esc(course.name)}" ${imgAttrs} class="famo-course-img">`
                        : `<div class="course-image-fallback">
                                ${svgIcon(faToSvg(course.icon), 'icon--3xl text-white/90')}
                           </div>`}
                    ${badgeBlock}
                </div>
                <div class="famo-course-body">
                    <h3 class="famo-course-name">${esc(course.name)}</h3>
                    ${metaBlock}
                    ${description ? `<p class="famo-course-desc">${esc(description)}</p>` : ''}
                    <div class="famo-course-footer">
                        <span class="famo-course-cta">جزئیات دوره
                            <i class="icon icon--sm" data-lucide="chevron-left" aria-hidden="true"></i>
                        </span>
                    </div>
                </div>
            </a>
        </div>
    `;
}

// Load courses from API
async function loadCoursesFromAPI() {
    showSkeletons('coursesContainer', 'skeleton-course-wrapper');

    try {
        const result = await API.get('/public/courses');
        const courses = result.data || [];

        const container = document.getElementById('coursesContainer');
        if (!container) return;

        container.setAttribute('aria-busy', 'false');
        const loadingRoot = container.closest('.coursesSwiper');
        if (loadingRoot) loadingRoot.classList.remove('coursesSwiper--loading');

        if (courses.length === 0) {
            const swiperRoot = container.closest('.coursesSwiper');
            if (swiperRoot) swiperRoot.classList.add('coursesSwiper--disabled');
            container.innerHTML = '<div class="famo-empty-message">در حال حاضر دوره‌ای ثبت نشده است</div>';
            return;
        }

        container.innerHTML = courses.map((course, i) => buildCourseSlide(course, i)).join('');
        if (window.refreshLucideIcons) window.refreshLucideIcons(container);

        // حالت ۰/۱/۲ دوره: بدون سوایپر (گرید ساده)؛ در غیر این صورت سوایپر ساخته می‌شود
        const swiperRoot = container.closest('.coursesSwiper');
        if (courses.length > 2 && window.Swiper) {
            if (swiperRoot) swiperRoot.classList.remove('coursesSwiper--disabled');
            if (!coursesSwiper) {
                coursesSwiper = new Swiper('.coursesSwiper', {
                    slidesPerView: 1.15,
                    spaceBetween: 16,
                    grabCursor: true,
                    loop: courses.length > 3,
                    pagination: {
                        el: '.coursesSwiper .swiper-pagination',
                        clickable: true,
                    },
                    navigation: {
                        nextEl: '.coursesSwiper .swiper-button-next',
                        prevEl: '.coursesSwiper .swiper-button-prev',
                    },
                    breakpoints: {
                        640: { slidesPerView: 2 },
                        1024: { slidesPerView: 3 },
                        1280: { slidesPerView: 4 },
                    },
                });
            } else {
                coursesSwiper.update();
            }
        } else if (swiperRoot) {
            swiperRoot.classList.add('coursesSwiper--disabled');
        }
    } catch (error) {
        console.error('Error loading courses:', error);
        const container = document.getElementById('coursesContainer');
        if (container) {
            container.setAttribute('aria-busy', 'false');
            const swiperRoot = container.closest('.coursesSwiper');
            if (swiperRoot) swiperRoot.classList.remove('coursesSwiper--loading');
            container.innerHTML = '<div class="famo-empty-message">خطا در بارگذاری دوره‌ها</div>';
        }
    }
}

// Maps a social_links.platform value to a sprite icon (برای لینک‌های اجتماعی اساتید)
const SOCIAL_ICON_MAP = {
    telegram: 'icon-telegram',
    instagram: 'icon-brand-instagram',
    whatsapp: 'icon-brand-whatsapp',
};

// Design مشترک لینک‌های اجتماعی اساتید
function renderSocialLinks(links) {
    if (!links || links.length === 0) return '';
    const items = links.map(link => {
        const url = safeUrl(link.url);
        if (!url) return '';
        const icon = SOCIAL_ICON_MAP[link.platform] || 'icon-external-link';
        return `
            <a href="${esc(url)}" target="_blank" rel="noopener"
                class="inst-social" aria-label="${esc(link.platform)}">
                ${svgIcon(icon, 'icon--sm')}
            </a>
        `;
    }).filter(Boolean).join('');
    return items ? `<div class="inst-socials">${items}</div>` : '';
}

// کارت یکسان اعضای کادر (استاد و پشتیبان) در صفحه اصلی
function personImageUrl(person) {
    const raw = person.image_url || person.avatar_url || person.image || '';
    const resolved = resolveImageUrl(raw);
    return /^(javascript|vbscript):/i.test(resolved) ? '' : resolved;
}

function normalizeInstructor(instructor) {
    return {
        role: 'instructor',
        roleLabel: 'استاد',
        name: instructor.name || '',
        title: instructor.title || 'استاد تخصصی',
        description: instructor.description || instructor.full_bio || '',
        image_url: instructor.image_url || instructor.avatar_url || '',
        initial_letter: instructor.initial_letter || '',
        social_links: instructor.social_links || [],
        tag: '',
    };
}

function normalizeSupporter(supporter) {
    const fieldLabel = supporter.field
        ? `رشته ${supporter.field}`
        : (supporter.grade ? `پایه ${supporter.grade}` : 'تحصیلی');

    return {
        role: 'supporter',
        roleLabel: 'پشتیبان',
        name: supporter.name || '',
        title: supporter.title || `پشتیبان تخصصی ${fieldLabel}`,
        description: supporter.description || supporter.full_bio || supporter.bio ||
            'پیگیری برنامه مطالعه، رفع اشکال درسی و همراهی مستمر دانش‌آموزان تا رسیدن به هدف.',
        image_url: supporter.image_url || supporter.avatar_url || supporter.image || '',
        initial_letter: supporter.initial_letter || '',
        social_links: supporter.social_links || [],
    };
}

function personCardHtml(person) {
    const role = person.role === 'supporter' ? 'supporter' : 'instructor';
    const initialLetter = person.initial_letter || (person.name ? person.name.trim().charAt(0) : '');
    const avatarUrl = personImageUrl(person);
    const socialsBlock = renderSocialLinks(person.social_links);
    const footerBlock = socialsBlock || (person.tag
        ? `<div class="inst-socials person-card__footer"><span class="person-tag">${esc(person.tag)}</span></div>`
        : '');

    return `
        <article class="inst-card person-card person-card--${role}" data-person-role="${role}">
            <div class="person-media">
                ${avatarUrl
                    ? `<img class="inst-avatar" src="${esc(avatarUrl)}" alt="${esc(person.name)}" width="640" height="450" loading="lazy" decoding="async">`
                    : `<div class="inst-avatar inst-avatar-ph" aria-hidden="true">${esc(initialLetter)}</div>`}
                <span class="person-role-badge">${esc(person.roleLabel || '')}</span>
            </div>
            <h3 class="inst-name">${esc(person.name)}</h3>
            <p class="inst-title">${esc(person.title)}</p>
            ${person.description ? `<p class="inst-bio">${esc(person.description)}</p>` : ''}
            ${footerBlock}
        </article>
    `;
}

function instructorCardHtml(instructor) {
    return personCardHtml(normalizeInstructor(instructor));
}

// ==================== کاروسل سه‌بعدی کادر آموزشی — پورت inspiration/CurveCarousel.js (پالت فامو، بدون autoplay) ====================
// فیزیک فنری damped-harmonic + اسنپ همیشه به عدد صحیح + اینرسی با cap ±2
// + blur/روشنایی/اشباع بر اساس فاصله از مرکز (عمق میدان).
const CURVE_CFG = {
    perspective: 1200, mobilePerspective: 900,
    depthStep: 150, mobileDepthStep: 110,
    rotateYStep: 18, maxRotateY: 42,
    spacingRatio: 0.72, mobileSpacingRatio: 0.66,
    sizeDecrease: 0.12, blurStep: 4.5, maxBlur: 14,
    cardWidth: 320, mobileCardWidth: 262,
    cardRadius: 20,
    arrowSize: 42, arrowInset: 24, arrowGap: 14,
    stiffness: 170, damping: 24, mass: 1,
};

let teamCarousel = null;

class TeamCurveCarousel {
    constructor(root, opts = {}) {
        this.stage = root.querySelector('.curve-carousel__stage') || root;
        this.track = root.querySelector('.curve-carousel__track');
        this.onIndex = opts.onIndex || (() => {});
        this.total = opts.total || 0;
        if (!this.track || !this.total) return;

        this.cardEls = Array.from(this.track.children);
        this.renderCount = this.cardEls.length;

        this.reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        this.currentPos = 0;   // شاخه پیوسته فعلی
        this.targetPos = 0;    // شاخه هدف (همیشه عدد صحیح = اسنپ)
        this.velocity = 0;     // کارت بر ثانیه
        this.rafId = null;
        this.lastTime = 0;

        this.isDragging = false;
        this.pointerId = null;
        this.startX = 0;
        this.startY = 0;
        this.lastPointerX = 0;
        this.lastPointerTime = 0;
        this.pointerVelocityX = 0;
        this.dragStartPos = 0;
        this.suppressClick = false;

        this.resolvedCfg = this.resolveSizing();
        this.applySizing();
        this.startPos(); // شروع از کپی وسط بدون قفل‌شدن جهت

        this.bindEvents();
        window.addEventListener('resize', this.onResize);
        this.renderFrame();
    }

    throwStart() {
        // شروع: اولین کارتِ وسطِ ستون کپی در مرکز
        const mid = Math.floor(this.renderCount / 2);
        this.currentPos = mid;
        this.targetPos = mid;
        this.renderFrame();
    }

    startPos() { this.throwStart(); }

    resolveSizing() {
        const isMobile = window.innerWidth < 768;
        const c = CURVE_CFG;
        return {
            perspective: isMobile ? c.mobilePerspective : c.perspective,
            depthStep: isMobile ? c.mobileDepthStep : c.depthStep,
            spacingRatio: isMobile ? c.mobileSpacingRatio : c.spacingRatio,
            cardWidth: isMobile ? c.mobileCardWidth : c.cardWidth,
            cardRadius: c.cardRadius,
            rotateYStep: c.rotateYStep, maxRotateY: c.maxRotateY, sizeDecrease: c.sizeDecrease,
            blurStep: c.blurStep, maxBlur: c.maxBlur,
            arrowSize: c.arrowSize, arrowInset: c.arrowInset, arrowGap: c.arrowGap,
        };
    }

    applySizing() {
        const c = this.resolvedCfg;
        const root = this.stage.closest('.curve-carousel') || this.stage.parentElement;
        this.stage.style.setProperty('--curve-pers', `${c.perspective}px`);
        this.stage.style.setProperty('--curve-card-w', `${c.cardWidth}px`);
        this.track.style.setProperty('--curve-card-w', `${c.cardWidth}px`);
        // ارتفاع واقعی کارت‌ها را بعد از یک فورسِ layout اندازه می‌گیریم (کارت‌های محتوادی، ارتفاع آزاد)
        this.track.style.height = 'auto';
        let cardH = 0;
        for (const el of this.cardEls) {
            el.style.position = 'static';
            el.style.transform = 'none';
            el.style.opacity = '1';
            cardH = Math.max(cardH, el.offsetHeight);
        }
        this.stage.style.height = `${cardH + 48}px`;
        for (const el of this.cardEls) {
            el.style.position = '';
            el.style.transform = '';
            el.style.opacity = '';
        }
        void root;
    }

    wrapDiff(diff, count) {
        if (count <= 0) return 0;
        let d = ((diff % count) + count) % count;
        if (d > count / 2) d -= count;
        return d;
    }

    renderFrame() {
        const c = this.resolvedCfg;
        const stepX = c.cardWidth * c.spacingRatio;
        const count = this.renderCount;

        for (let j = 0; j < count; j += 1) {
            const card = this.cardEls[j];
            const diff = this.wrapDiff(j - this.currentPos, count);
            const absDiff = Math.abs(diff);

            if (absDiff > 3.3) {
                card.style.opacity = '0';
                card.style.pointerEvents = 'none';
                card.style.visibility = 'hidden';
                card.setAttribute('aria-hidden', 'true');
                continue;
            }

            card.style.visibility = 'visible';

            // فشرده‌سازی افقی غیرخطی برای کارت‌های عمیق (حس پشته ۳بعدی)
            const compression = 1 - Math.min(absDiff, 3) * 0.055;
            const translateX = diff * stepX * compression;
            // عمق نفسِ Z (بدون قوس عمودی)
            const translateZ = -Math.pow(absDiff, 0.92) * c.depthStep;
            // چرخش inward به مرکز
            const rawRotateY = -diff * c.rotateYStep;
            const rotateY = Math.max(-c.maxRotateY, Math.min(c.maxRotateY, rawRotateY));
            // سلسله‌مراتب نرمِ scale
            const scale = Math.max(0.62, 1 - absDiff * c.sizeDecrease);
            // blur عمق میدان + brightness/saturate
            const blurPx = absDiff < 0.04 ? 0 : Math.min(c.maxBlur, Math.pow(absDiff, 1.15) * c.blurStep);
            const brightness = Math.max(0.45, 1.03 - absDiff * 0.19);
            const saturate = Math.max(0.75, 1.05 - absDiff * 0.08);
            // کاهش نرمِ شفافیت
            const opacity = absDiff <= 1.15 ? 1 : Math.max(0, 1 - (absDiff - 1.15) * 0.55);
            const isCenter = absDiff < 0.45;

            card.style.transform = `translate3d(calc(-50% + ${translateX.toFixed(2)}px), -50%, ${translateZ.toFixed(2)}px) rotateY(${rotateY.toFixed(2)}deg) scale(${scale.toFixed(4)})`;
            card.style.zIndex = String(Math.round(100 - absDiff * 20));
            card.style.opacity = opacity.toFixed(3);
            card.style.pointerEvents = absDiff < 2.2 ? 'auto' : 'none';
            card.classList.toggle('is-center', isCenter);
            card.setAttribute('aria-hidden', isCenter ? 'false' : 'true');

            const inner = card.firstElementChild;
            if (inner) {
                inner.style.filter = blurPx > 0.05
                    ? `blur(${blurPx.toFixed(2)}px) brightness(${brightness.toFixed(2)}) saturate(${saturate.toFixed(2)})`
                    : ` brightness(${brightness.toFixed(2)}) saturate(${saturate.toFixed(2)})`;
            }
        }

        const idx = ((Math.round(this.currentPos) % this.total) + this.total) % this.total;
        if (idx !== this.activeIdx) {
            this.activeIdx = idx;
            this.onIndex(idx);
        }
    }

    stepPhysics(now) {
        if (!this.lastTime) this.lastTime = now;
        const dt = Math.min(0.032, Math.max(0.001, (now - this.lastTime) / 1000));
        this.lastTime = now;

        if (this.isDragging) {
            // دنبال‌کردن حسی و تنگِ انگشت کاربر
            const followFactor = 1 - Math.exp(-26 * dt);
            const prevPos = this.currentPos;
            this.currentPos += (this.targetPos - this.currentPos) * followFactor;
            this.velocity = (this.currentPos - prevPos) / dt;
            this.renderFrame();
            this.rafId = requestAnimationFrame(t => this.stepPhysics(t));
            return;
        }

        if (this.reduced) {
            this.currentPos = this.targetPos;
            this.velocity = 0;
            this.renderFrame();
            this.rafId = null;
            this.lastTime = 0;
            return;
        }

        // فنر منطبق بر مرجع (Framer Motion style)
        const displacement = this.currentPos - this.targetPos;
        const springForce = -CURVE_CFG.stiffness * displacement;
        const dampingForce = -CURVE_CFG.damping * this.velocity;
        const acceleration = (springForce + dampingForce) / CURVE_CFG.mass;

        this.velocity += acceleration * dt;
        this.currentPos += this.velocity * dt;
        this.renderFrame();

        if (Math.abs(this.targetPos - this.currentPos) < 0.0015 && Math.abs(this.velocity) < 0.008) {
            this.currentPos = this.targetPos;
            this.velocity = 0;
            this.renderFrame();
            this.rafId = null;
            this.lastTime = 0;
        } else {
            this.rafId = requestAnimationFrame(t => this.stepPhysics(t));
        }
    }

    startLoop() {
        if (this.rafId == null) {
            this.lastTime = performance.now();
            this.rafId = requestAnimationFrame(t => this.stepPhysics(t));
        }
    }

    goTo(newTarget) {
        if (this.total <= 1) return;
        this.targetPos = newTarget;
        this.startLoop();
    }

    move(direction) {
        this.goTo(Math.round(this.targetPos) + direction);
    }

    next() { this.move(1); }
    prev() { this.move(-1); }

    bindEvents() {
        const s = this.stage;
        s.addEventListener('pointerdown', this.onPointerDown);
        s.addEventListener('pointermove', this.onPointerMove);
        s.addEventListener('pointerup', this.endDrag);
        s.addEventListener('pointercancel', this.endDrag);
        s.addEventListener('click', this.onStageClick, true);
        s.addEventListener('dragstart', this.onDragStart);
        s.addEventListener('wheel', this.onWheel, { passive: false });
        s.addEventListener('keydown', this.onKeydown);
    }

    onPointerDown = (e) => {
        if (this.total <= 1) return;
        if (e.pointerType === 'mouse' && e.button !== 0) return;
        if (e.target.closest('button')) return;

        this.isDragging = true;
        this.pointerId = e.pointerId;
        this.startX = e.clientX;
        this.startY = e.clientY;
        this.lastPointerX = e.clientX;
        this.lastPointerTime = performance.now();
        this.pointerVelocityX = 0;
        this.dragStartPos = this.currentPos;
        this.targetPos = this.currentPos;
        this.suppressClick = false;

        this.stage.classList.add('is-dragging');
        this.startLoop();
    };

    onPointerMove = (e) => {
        if (!this.isDragging || e.pointerId !== this.pointerId) return;

        const now = performance.now();
        const dx = e.clientX - this.startX;
        const dy = e.clientY - this.startY;
        const draggedDistance = Math.hypot(dx, dy);

        if (draggedDistance > 7) {
            this.suppressClick = true;
            if (!this.stage.hasPointerCapture || !this.stage.hasPointerCapture(e.pointerId)) {
                this.stage.setPointerCapture && this.stage.setPointerCapture(e.pointerId);
            }
        }

        const dtMs = Math.max(1, now - this.lastPointerTime);
        const instantVx = (e.clientX - this.lastPointerX) / dtMs; // px per ms
        this.pointerVelocityX = this.pointerVelocityX * 0.65 + instantVx * 0.35;
        this.lastPointerX = e.clientX;
        this.lastPointerTime = now;

        const dragSpan = Math.max(160, this.resolvedCfg.cardWidth * this.resolvedCfg.spacingRatio);
        this.targetPos = this.dragStartPos - dx / dragSpan;
        this.startLoop();
    };

    endDrag = (e) => {
        if (!this.isDragging) return;
        if (e && this.pointerId != null && e.pointerId != null && e.pointerId !== this.pointerId) return;

        this.isDragging = false;
        this.pointerId = null;
        this.stage.classList.remove('is-dragging');

        const dragSpan = Math.max(160, this.resolvedCfg.cardWidth * this.resolvedCfg.spacingRatio);
        // تبدیل سرعت pointer (px/ms) به سرعت شاخه‌ای (کارت بر ثانیه)
        const releaseCardVelocity = (-this.pointerVelocityX * 1000) / dragSpan;
        this.velocity = releaseCardVelocity;

        // برآورد مکانیزم فرود پرتاب
        const projected = this.targetPos + releaseCardVelocity * 0.16;
        const maxJump = Math.round(this.dragStartPos) + (releaseCardVelocity > 0 ? 2 : -2);
        let snapped = Math.round(projected);

        // اگر فلیک سریع حتی با فاصله کم بود، یک کارت جلو برو
        const totalDx = this.lastPointerX - this.startX;
        if (snapped === Math.round(this.dragStartPos) && Math.abs(this.pointerVelocityX) > 0.35 && Math.abs(totalDx) > 18) {
            snapped = Math.round(this.dragStartPos) + (totalDx < 0 ? 1 : -1);
        }

        if (Math.abs(snapped - Math.round(this.dragStartPos)) > 2) {
            snapped = maxJump;
        }

        this.targetPos = snapped;
        this.startLoop();
    };

    // کلیک هنگام/بعد از درگ خفه می‌شود؛ کلیک روی کارت کناری → مرکز شدن آن
    onStageClick = (e) => {
        if (this.suppressClick) {
            e.preventDefault();
            e.stopPropagation();
            this.suppressClick = false;
            return;
        }
        const cardEl = e.target.closest('.curve-carousel__card');
        if (!cardEl) return;
        const vIdx = Number(cardEl.dataset.virtualIdx);
        if (Number.isNaN(vIdx)) return;
        const diff = this.wrapDiff(vIdx - this.currentPos, this.renderCount);
        if (Math.abs(diff) >= 0.45) {
            e.preventDefault();
            e.stopPropagation();
            this.goTo(Math.round(this.currentPos + diff));
        }
    };

    onDragStart = (e) => e.preventDefault();

    onWheel = (e) => {
        if (this.total <= 1) return;
        const isHorizontal = Math.abs(e.deltaX) > Math.abs(e.deltaY) || e.shiftKey;
        if (!isHorizontal) return;
        const delta = e.deltaX || e.deltaY;
        if (Math.abs(delta) < 8) return;
        e.preventDefault();
        const now = performance.now();
        if (this.wheelCooldown && now - this.wheelCooldown < 260) return;
        this.wheelCooldown = now;
        this.move(delta > 0 ? 1 : -1);
    };

    onKeydown = (e) => {
        if (e.key === 'ArrowLeft') { e.preventDefault(); this.next(); }
        if (e.key === 'ArrowRight') { e.preventDefault(); this.prev(); }
    };

    onResize = () => {
        this.resolvedCfg = this.resolveSizing();
        this.applySizing();
        this.renderFrame();
    };

    destroy() {
        if (this.rafId != null) cancelAnimationFrame(this.rafId);
        this.rafId = null;
        const s = this.stage;
        s.removeEventListener('pointerdown', this.onPointerDown);
        s.removeEventListener('pointermove', this.onPointerMove);
        s.removeEventListener('pointerup', this.endDrag);
        s.removeEventListener('pointercancel', this.endDrag);
        s.removeEventListener('click', this.onStageClick, true);
        s.removeEventListener('dragstart', this.onDragStart);
        s.removeEventListener('wheel', this.onWheel);
        s.removeEventListener('keydown', this.onKeydown);
        window.removeEventListener('resize', this.onResize);
    }
}

// Load instructors + supporters into one shared team carousel.
async function loadTeamFromAPI() {
    showSkeletons('instructorsContainer', 'skeleton-instructor-wrapper');

    const container = document.getElementById('instructorsContainer');
    if (!container) return;

    const root = container.closest('.curve-carousel');
    const dotsEl = document.getElementById('instCarouselDots');
    const prevBtn = document.getElementById('instCarouselPrev');
    const nextBtn = document.getElementById('instCarouselNext');

    try {
        const [instructorsResult, supportersResult] = await Promise.allSettled([
            API.get('/public/instructors'),
            API.get('/public/supporters'),
        ]);

        if (instructorsResult.status === 'rejected') {
            console.error('Error loading instructors:', instructorsResult.reason);
        }
        if (supportersResult.status === 'rejected') {
            console.error('Error loading supporters:', supportersResult.reason);
        }
        if (instructorsResult.status === 'rejected' && supportersResult.status === 'rejected') {
            throw instructorsResult.reason;
        }

        const instructors = instructorsResult.status === 'fulfilled' && Array.isArray(instructorsResult.value.data)
            ? instructorsResult.value.data
            : [];
        const supporters = supportersResult.status === 'fulfilled' && Array.isArray(supportersResult.value.data)
            ? supportersResult.value.data
            : [];
        const list = [
            ...instructors.map(normalizeInstructor),
            ...supporters.map(normalizeSupporter),
        ];

        hideSkeletons('instructorsContainer', 'skeleton-instructor-wrapper');
        container.classList.remove('is-skeleton');

        if (list.length === 0) {
            if (root) root.classList.add('curve-carousel--off-mode');
            container.innerHTML = '<div class="famo-empty-message">عضوی از کادر فامو یافت نشد</div>';
            dotsEl && (dotsEl.hidden = true);
            prevBtn && (prevBtn.hidden = true);
            nextBtn && (nextBtn.hidden = true);
            return;
        }

        // حلقه بی‌درز مثل مرجع: کپی‌های لازم برای پوشش wrap (حداقل ~۷ کارت)
        const total = list.length;
        const repeatMultiplier = total < 5 ? Math.ceil(7 / total) : 1;
        const renderCount = Math.max(total * repeatMultiplier, 7);

        const cards = [];
        for (let j = 0; j < renderCount; j += 1) {
            const idx = j % total;
            cards.push(
                `<div class="curve-carousel__card" data-virtual-idx="${j}" data-item-idx="${idx}" data-person-role="${esc(list[idx].role)}">${personCardHtml(list[idx])}</div>`
            );
        }
        container.innerHTML = cards.join('');
        if (window.refreshLucideIcons) window.refreshLucideIcons(container);

        const useCarousel = total >= 3 && root;
        if (!useCarousel) {
            if (root) root.classList.add('curve-carousel--off-mode');
            container.innerHTML = list.map(person => `<div class="curve-carousel__card" data-person-role="${esc(person.role)}">${personCardHtml(person)}</div>`).join('');
            if (window.refreshLucideIcons) window.refreshLucideIcons(container);
            dotsEl && (dotsEl.hidden = true);
            prevBtn && (prevBtn.hidden = true);
            nextBtn && (nextBtn.hidden = true);
            return;
        }

        root.classList.remove('curve-carousel--off-mode');
        dotsEl && (dotsEl.hidden = false);
        prevBtn && (prevBtn.hidden = false);
        nextBtn && (nextBtn.hidden = false);

        if (teamCarousel) { teamCarousel.destroy(); teamCarousel = null; }

        // نقاط: طبق مرجع حداکثر ۱۲ تا
        if (dotsEl) {
            dotsEl.innerHTML = total > 1 && total <= 12
                ? list.map((person, i) => `<button type="button" class="curve-carousel__dot" data-curve-dot="${i}" aria-label="اسلاید ${i + 1}: ${esc(person.roleLabel)} ${esc(person.name)}"></button>`).join('')
                : '';
        }

        teamCarousel = new TeamCurveCarousel(root, {
            total,
            onIndex: (idx) => {
                if (!dotsEl) return;
                dotsEl.querySelectorAll('[data-curve-dot]').forEach(d => {
                    d.classList.toggle('is-active', Number(d.dataset.curveDot) === idx);
                });
            },
        });

        prevBtn.disabled = false;
        nextBtn.disabled = false;
        prevBtn && prevBtn.addEventListener('click', () => teamCarousel.prev());
        nextBtn && nextBtn.addEventListener('click', () => teamCarousel.next());
        dotsEl && dotsEl.addEventListener('click', (e) => {
            const dot = e.target.closest('[data-curve-dot]');
            if (dot && teamCarousel) {
                const targetIdx = Number(dot.dataset.curveDot);
                const currentMod = teamCarousel.activeIdx;
                const delta = teamCarousel.wrapDiff(targetIdx - currentMod, teamCarousel.total);
                teamCarousel.goTo(Math.round(teamCarousel.targetPos) + delta);
            }
        });
    } catch (error) {
        console.error('Error loading team members:', error);
        hideSkeletons('instructorsContainer', 'skeleton-instructor-wrapper');
        container.classList.remove('is-skeleton');
        if (root) root.classList.add('curve-carousel--off-mode');
        container.innerHTML = '<div class="famo-empty-message">خطا در بارگذاری کادر فامو</div>';
    }
}
// Load all data on page load
onReady(() => {
    Promise.allSettled([loadCoursesFromAPI(), loadTeamFromAPI()]).then(scheduleScrollRefresh);
});

// Stats Counter Animation with GSAP
const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
function animateCounter(elementId, finalValue, suffix = '') {
    const element = document.getElementById(elementId);
    if (!element) return;
    if (prefersReducedMotion || typeof gsap === 'undefined') {
        element.textContent = finalValue + suffix;
        return;
    }

    const obj = { value: 0 };
    gsap.to(obj, {
        value: finalValue,
        duration: 2,
        ease: "power2.out",
        onUpdate: () => {
            element.textContent = Math.floor(obj.value) + suffix;
        }
    });
}

// مقادیر آمار (تأییدشده): باید دقیقاً با عددهای HTML هیرو یکی باشند (200 قبولی، 10 استاد).
const STATS = { students: 200, teachers: 10 };

// Intersection Observer for stats animation with GSAP
const statsSection = document.querySelector('#home');
if (statsSection) {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                // Animate stats card with GSAP (if loaded; counters must run regardless)
                if (!prefersReducedMotion && typeof gsap !== 'undefined') {
                    gsap.from('#home .hero-stats', {
                        opacity: 0,
                        y: 30,
                        duration: 0.8,
                        ease: "power2.out"
                    });
                }

                setTimeout(() => {
                    animateCounter('years-counter', 7);
                    if (STATS.students !== null) animateCounter('students-counter', STATS.students);
                    if (STATS.teachers !== null) animateCounter('teachers-counter', STATS.teachers);
                }, 500);

                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.5 });

    observer.observe(statsSection);
}

// Set current Persian year in footer
const copyrightYear = document.getElementById('copyright-year');
if (copyrightYear) {
    const persianYear = new Date().toLocaleDateString('fa-IR', { year: 'numeric' });
    copyrightYear.textContent = persianYear.replace(/[۰-۹]/g, d => '۰۱۲۳۴۵۶۷۸۹'.indexOf(d));
}

// Add hover effects to service cards with GSAP
const serviceCards = document.querySelectorAll('#services .service-card');
serviceCards.forEach(card => {
    const icon = card.querySelector('.service-illustration');
    if (icon) {
        card.addEventListener('mouseenter', function () {
            gsap.to(icon, {
                scale: 1.1,
                duration: 0.3,
                ease: "power2.out"
            });
        });

        card.addEventListener('mouseleave', function () {
            gsap.to(icon, {
                scale: 1,
                duration: 0.3,
                ease: "power2.out"
            });
        });
    }
});

// ScrollTrigger reveal animations
// - به‌جای محو کردن کل سکشن (که روی موبایل خیلی بلند است) فقط کانتینر داخلش انیمیت می‌شود
// - once: true و clearProps: بعد از اجرا هیچ transform/opacity باقی نمی‌ماند
// - ScrollToPlugin لازم نیست؛ اسکرول نرم لینک‌ها در همین فایل با window.scrollTo انجام می‌شود
function initScrollAnimations() {
    if (prefersReducedMotion) return;
    if (typeof gsap === 'undefined' || typeof ScrollTrigger === 'undefined') return;

    gsap.registerPlugin(ScrollTrigger);
    ScrollTrigger.config({ ignoreMobileResize: true });

    // #home حذف شده: هیرو بالای صفحه است و محو کردنش به LCP لطمه می‌زند
    document.querySelectorAll('section[id]:not(#home)').forEach((section) => {
        const target = section.querySelector(':scope > .container') || section;
        gsap.from(target, {
            opacity: 0,
            y: 28,
            duration: 0.7,
            ease: 'power2.out',
            clearProps: 'opacity,transform',
            scrollTrigger: {
                trigger: section,
                start: 'top 90%',
                once: true,
            },
        });
    });

    // هر تغییر ارتفاع صفحه (لود API، عکس‌های lazy، فونت) → refresh با debounce
    if ('ResizeObserver' in window) {
        new ResizeObserver(scheduleScrollRefresh).observe(document.body);
    }
    window.addEventListener('load', scheduleScrollRefresh);
    if (document.fonts && document.fonts.ready) document.fonts.ready.then(scheduleScrollRefresh);
}

onReady(initScrollAnimations);