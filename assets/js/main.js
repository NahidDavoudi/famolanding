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
function showSkeletons(containerId, skeletonClass) {
    const container = document.getElementById(containerId);
    if (!container) return;

    const skeletons = container.querySelectorAll(`.${skeletonClass}`);
    skeletons.forEach(skeleton => {
        skeleton.classList.add('show');
    });
}

function hideSkeletons(containerId, skeletonClass) {
    const container = document.getElementById(containerId);
    if (!container) return;

    const skeletons = container.querySelectorAll(`.${skeletonClass}`);
    skeletons.forEach(skeleton => {
        skeleton.classList.add('hide');
        skeleton.classList.remove('show');
    });
}

// Swiper instance for the courses carousel (created once after data renders)
let coursesSwiper = null;

function renderCourseFeatureChip(text) {
    return `<span>${esc(text)}</span>`;
}

// Build one course card slide for the home carousel.
function buildCourseSlide(course) {
    const hasPrice = course.price !== null && course.price !== undefined && String(course.price).trim() !== '';
    const priceText = hasPrice ? formatPrice(course.price) : '';
    const priceUnit = hasPrice && isNumericPrice(course.price) ? 'تومان' : '';
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

    return `
        <div class="swiper-slide">
            <a class="famo-course-card" href="pages/course.php#course-${esc(course.id)}"
               aria-label="${esc(course.name)} - مشاهده جزئیات دوره">
                <div class="famo-course-media" style="background-image: ${gradient};">
                    ${imageUrl
                        ? `<img src="${esc(imageUrl)}" alt="${esc(course.name)}" width="1200" height="750" loading="lazy" class="famo-course-img">`
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
                        <div class="famo-course-price">
                            <span class="famo-course-price-label">هزینه دوره</span>
                            ${hasPrice
                                ? `<span class="famo-course-price-value">${esc(priceText)}${priceUnit ? `<span class="famo-course-price-unit">${esc(priceUnit)}</span>` : ''}</span>`
                                : `<span class="famo-course-price-value">تماس بگیرید</span>`}
                        </div>
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

        if (courses.length === 0) {
            const swiperRoot = container.closest('.coursesSwiper');
            if (swiperRoot) swiperRoot.classList.add('coursesSwiper--disabled');
            container.innerHTML = '<div class="famo-empty-message">در حال حاضر دوره‌ای ثبت نشده است</div>';
            return;
        }

        container.innerHTML = courses.map(buildCourseSlide).join('');
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

// markup مشترک کارت استاد (صفحه اصلی + pages/team.php): آواتار بزرگ، بدون ظاهر کلیک‌پذیر
function instructorCardHtml(instructor) {
    const initialLetter = instructor.initial_letter || (instructor.name ? instructor.name.charAt(0) : '');
    const bio = instructor.description || '';
    const avatarUrl = safeUrl(instructor.image_url);
    const socialsBlock = renderSocialLinks(instructor.social_links);

    return `
        <article class="inst-card">
            ${avatarUrl
                ? `<img class="inst-avatar" src="${esc(avatarUrl)}" alt="${esc(instructor.name)}" width="112" height="112" loading="lazy">`
                : `<div class="inst-avatar inst-avatar-ph" aria-hidden="true">${esc(initialLetter)}</div>`}
            <h3 class="inst-name">${esc(instructor.name)}</h3>
            <p class="inst-title">${esc(instructor.title)}</p>
            ${bio ? `<p class="inst-bio">${esc(bio)}</p>` : ''}
            ${socialsBlock}
        </article>
    `;
}

// ==================== درگ-کاروسل ۳بعدی اساتید (بدون کتابخانه جدید؛ GSAP موجود) ====================
// حس مشابه DragableCarousel فِرِیمر: حلقه مادام، کارت میانی برجسته، کارت‌های کناری
// rotateY + scale + opacity، درگ با اینرسی و اسنپ فنری به کارت میانی.
const DRAG_COPIES = 3;
let instCarousel = null;

class InstructorDragCarousel {
    constructor(track, opts = {}) {
        this.track = track;
        this.viewport = track.parentElement;
        this.onIndex = opts.onIndex || (() => {});
        this.slides = Array.from(track.children);
        this.n = this.slides.length / DRAG_COPIES;
        if (!this.slides.length || !this.n) return;

        // اندازه‌ها (RTL-agnostic: فاصله واقعی اسلایدها از خود DOM گرفته می‌شود)
        const r0 = this.slides[0].getBoundingClientRect();
        let step = r0.width;
        if (this.slides.length > 1) {
            const r1 = this.slides[1].getBoundingClientRect();
            step = Math.abs(r1.left - r0.left) || r0.width;
        }
        this.step = step;
        this.vc = this.viewport.getBoundingClientRect();
        this.halfCx = this.vc.left + this.vc.width / 2;
        this.x = 0;
        this.tx = 0;
        this.tween = null;
        this.dragging = false;
        this.pointerId = null;
        this.px = 0;
        this.history = [];
        this.reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        this.activeIdx = -1;

        this.bindEvents();
        this.measure();
        // شروع: اولین کارتِ کپیِ میانی دقیقاً در مرکز (بدون فرض جهت RTL)
        this.x = this.tx = this.halfCx - this.centerOf(this.slides[this.n]);
        this.rafId = requestAnimationFrame(() => this.loop());
        window.addEventListener('resize', this.measure);
    }

    measure = () => {
        if (!this.slides.length) return;
        const r0 = this.slides[0].getBoundingClientRect();
        let step = r0.width;
        if (this.slides.length > 1) {
            const r1 = this.slides[1].getBoundingClientRect();
            step = Math.abs(r1.left - r0.left) || r0.width;
        }
        this.step = step;
        this.vc = this.viewport.getBoundingClientRect();
        this.halfCx = this.vc.left + this.vc.width / 2;
        this.cycleW = step * this.n;
    };

    // هزینه کم: هر اسلاید rect خودش را می‌گیرد (فقط transform قبلی اعمال شده است)
    centerOf(el) {
        const r = el.getBoundingClientRect();
        return r.left + r.width / 2;
    }

    // اسلایدی که الان به مرکز نزدیک‌تر است + شاخه منطقی آن
    nearestSlot(shift = 0) {
        const targetCx = this.halfCx - shift;
        let best = 0, bestD = Infinity;
        for (let j = 0; j < this.slides.length; j++) {
            const d = Math.abs(this.centerOf(this.slides[j]) + shift - targetCx);
            if (d < bestD) { bestD = d; best = j; }
        }
        return best;
    }

    index() {
        return this.nearestSlot() % this.n;
    }

    // اسنپ به شاخه منطقی idx (کپی‌ای که نزدیک مرکز است)
    go(idx, instant = false) {
        idx = ((idx % this.n) + this.n) % this.n;
        const cur = this.x;
        // بهترین کپی بین هر هر: j = idx، idx+n یا idx+2n — همان که به مرکز نزدیک‌تر
        const candidates = [idx, idx + this.n, idx + 2 * this.n].map(j => ({
            j,
            delta: this.centerOf(this.slides[j]) - this.halfCx, // فاصله فعلی از مرکز
        }));
        candidates.sort((a, b) => Math.abs(a.delta) - Math.abs(b.delta));
        const targetX = cur - candidates[0].delta;
        if (this.tween) this.tween.kill();
        this.tween = null;
        if (instant || this.reduced || typeof gsap === 'undefined') {
            this.x = this.tx = targetX;
        } else {
            this.tween = gsap.to(this, { tx: targetX, duration: 0.9, ease: 'power3.out' });
        }
    }

    next(instant) { this.go(this.index() + 1, instant); }
    prev(instant) { this.go(this.index() - 1, instant); }

    destroy() {
        cancelAnimationFrame(this.rafId);
        window.removeEventListener('resize', this.measure);
        if (this.tween) this.tween.kill();
    }

    bindEvents() {
        const v = this.viewport;
        v.addEventListener('pointerdown', (e) => this.onDown(e));
        v.addEventListener('pointermove', (e) => this.onMove(e));
        v.addEventListener('pointerup', (e) => this.onUp(e));
        v.addEventListener('pointercancel', (e) => this.onUp(e));
        v.addEventListener('dragstart', (e) => e.preventDefault());
        // جایگزین غیردو-klic برای درگ: کیبورد
        v.addEventListener('keydown', (e) => {
            if (e.key === 'ArrowLeft') { e.preventDefault(); this.next(); }
            if (e.key === 'ArrowRight') { e.preventDefault(); this.prev(); }
        });
    }

    onDown(e) {
        if (this.pointerId !== null) return;
        this.pointerId = e.pointerId;
        this.dragging = true;
        this.viewport.classList.add('is-dragging');
        if (this.tween) { this.tween.kill(); this.tween = null; }
        this.px = e.clientX;
        this.history = [this.x];
        this.viewport.setPointerCapture && this.viewport.setPointerCapture(e.pointerId);
    }

    onMove(e) {
        if (!this.dragging || e.pointerId !== this.pointerId) return;
        e.preventDefault(); // اسکرول عمودی اسکرول‌بار دست‌نخورده (touch-action: pan-y)
        const dx = e.clientX - this.px;
        this.px = e.clientX;
        this.tx += dx;
        const now = performance.now();
        this.history.push([now, this.tx]);
        while (this.history.length > 2 && now - this.history[0][0] > 100) this.history.shift();
    }

    onUp(e) {
        if (e.pointerId === undefined || e.pointerId === this.pointerId) this.release();
    }

    release() {
        if (!this.dragging) return;
        this.dragging = false;
        this.pointerId = null;
        this.viewport.classList.remove('is-dragging');
        // اینرسی (px/ms) از تاریخچه درگ + پرتاب
        let v = 0;
        if (this.history.length >= 2) {
            const [t0, x0] = this.history[0];
            const [t1, x1] = this.history[this.history.length - 1];
            if (t1 - t0 > 0) v = (x1 - x0) / (t1 - t0);
        }
        const projected = this.tx + v * 280;
        // کارتی که در موقعیتِ برآوردشده به مرکز نزدیک‌تر است
        let best = 0, bestD = Infinity;
        for (let j = 0; j < this.slides.length; j++) {
            const baseCx = this.centerOf(this.slides[j]) - this.x; // موقعیت بدون ترنسفورم رد
            const d = Math.abs(baseCx + projected - this.halfCx);
            if (d < bestD) { bestD = d; best = j; }
        }
        const tX = this.x + this.halfCx - this.centerOf(this.slides[best]);
        if (this.reduced || typeof gsap === 'undefined') {
            this.x = this.tx = tX;
        } else {
            this.tween = gsap.to(this, { tx: tX, duration: 0.85, ease: 'power3.out' });
        }
    }

    apply() {
        this.track.style.transform = `translate3d(${this.x}px,0,0)`;
        const n = this.n;
        for (let j = 0; j < this.slides.length; j++) {
            const el = this.slides[j];
            let t = (this.centerOf(el) - this.halfCx) / this.step;
            // wrap: t را به بازه ±(n/2+1) نگه می‌داریم تا کپی‌ها درست شبیه‌سازی شوند
            while (t > n / 2 + 1) t -= n;
            while (t < -(n / 2 + 1)) t += n;
            const at = Math.min(1, Math.abs(t));
            // کلید حس سه بعدی: کارت میانی درشت و جلو، کناری‌ها چرخیده به سمت مرکز و عقب
            const scale = 1 - at * 0.3;
            const z = -at * 150;
            const rot = -t * 24;
            const op = 1 - at * 0.4;
            el.style.transform = `translate3d(0,0,${z}px) rotateY(${rot}deg) scale(${scale})`;
            el.style.opacity = op;
            el.style.zIndex = String(1000 - Math.round(at * 200));
        }
        const idx = this.index();
        if (idx !== this.activeIdx) {
            this.activeIdx = idx;
            this.onIndex(idx);
        }
    }

    loop = () => {
        // wrap: اگر بسیار دور شد، دقیقاً یک دوره (کپی کامل) برگرد — محتوا یکسان، بی‌درز
        if (Math.abs(this.x) > this.cycleW * 2) {
            const shiftPx = Math.round(this.x / this.cycleW) * this.cycleW;
            this.x -= shiftPx;
            this.tx -= shiftPx;
        }
        // نرمی: دنبال‌کردن lerp به سمت tx (کشِ نرم در درگ + سقفِ اسنپ)
        this.x += (this.tx - this.x) * (this.dragging ? 0.4 : 0.15);
        if (Math.abs(this.tx - this.x) < 0.05) this.x = this.tx;
        this.apply();
        this.rafId = requestAnimationFrame(() => this.loop());
    };
}

// Load instructors from API
async function loadInstructorsFromAPI() {
    showSkeletons('instructorsContainer', 'skeleton-instructor-wrapper');

    try {
        const result = await API.get('/public/instructors');
        const list = result.data || [];

        const container = document.getElementById('instructorsContainer');
        if (!container) return;

        hideSkeletons('instructorsContainer', 'skeleton-instructor-wrapper');

        if (list.length === 0) {
            container.innerHTML = '<div class="famo-empty-message">استادی یافت نشد</div>';
            return;
        }

        const viewport = container.closest('.drag-carousel-viewport');
        const dotsEl = document.getElementById('instCarouselDots');
        const prevBtn = document.getElementById('instCarouselPrev');
        const nextBtn = document.getElementById('instCarouselNext');

        // تعداد کمتر از ۳: گرید ساده (بدون کاروسل)
        if (list.length < 3 || !viewport) {
            viewport?.classList.add('drag-carousel--off');
            container.innerHTML = list.map(instructor => `<div class="drag-carousel-slide drag-carousel-slide--grid">${instructorCardHtml(instructor)}</div>`).join('');
            if (window.refreshLucideIcons) window.refreshLucideIcons(container);
            if (dotsEl || prevBtn || nextBtn) {
                dotsEl && (dotsEl.hidden = true);
                prevBtn && (prevBtn.hidden = true);
                nextBtn && (nextBtn.hidden = true);
            }
            return;
        }

        viewport?.classList.remove('drag-carousel--off');
        if (dotsEl || prevBtn || nextBtn) {
            dotsEl && (dotsEl.hidden = false);
            prevBtn && (prevBtn.hidden = false);
            nextBtn && (nextBtn.hidden = false);
        }

        // ۳ کپی از لیست برای حلقه بی‌درز
        const copies = [];
        for (let c = 0; c < DRAG_COPIES; c++) copies.push(...list);
        container.innerHTML = copies
            .map(i => `<div class="drag-carousel-slide">${instructorCardHtml(i)}</div>`)
            .join('');
        if (window.refreshLucideIcons) window.refreshLucideIcons(container);

        // نقاط دات‌ها بر اساس تعداد واقعی اساتید
        if (dotsEl) {
            dotsEl.innerHTML = list.map((inst, i) =>
                `<button type="button" class="drag-carousel-dot" data-inst-index="${i}" aria-label="حالت ${i + 1}: ${esc(inst.name)}"></button>`
            ).join('');
        }

        if (instCarousel) { instCarousel.destroy(); instCarousel = null; }
        instCarousel = new InstructorDragCarousel(container, {
            onIndex: (idx) => {
                if (dotsEl) {
                    dotsEl.querySelectorAll('.drag-carousel-dot').forEach(d => {
                        d.classList.toggle('is-active', Number(d.dataset.instIndex) === idx);
                    });
                }
            },
        });
        instCarousel.reversed = true; // RTL: چرخش جهت‌کلید حرکت با کلید به سمت وفيزیکی糸
        prevBtn?.addEventListener('click', () => instCarousel.prev());
        nextBtn?.addEventListener('click', () => instCarousel.next());
        dotsEl?.addEventListener('click', (e) => {
            const dot = e.target.closest('.drag-carousel-dot');
            if (dot) instCarousel.go(Number(dot.dataset.instIndex));
        });
    } catch (error) {
        console.error('Error loading instructors:', error);
        const container = document.getElementById('instructorsContainer');
        if (container) {
            hideSkeletons('instructorsContainer', 'skeleton-instructor-wrapper');
            container.innerHTML = '<div class="famo-empty-message">خطا در بارگذاری اساتید</div>';
        }
    }
}

// Load all data on page load
onReady(() => {
    loadCoursesFromAPI();
    loadInstructorsFromAPI();
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

// مقادیر آمار: JS منبع اصلی است؛ هر دو عدد باید دقیقاً یکی باشند.
// pending: عدد نهایی قبولی/استاد منتظر تأیید کارفرما (HTML فعلی: 500+ قبولی، 15 استاد؛ JS قدیمی: 200 و 8).
// تا زمان تأیید، انیمیشن شمارنده اجرا نمی‌شود تا HTML هرگز به ۰ یا مقدار نادرست نرود.
const STATS = { students: null, teachers: null };

// Intersection Observer for stats animation with GSAP
const statsSection = document.querySelector('#home');
if (statsSection) {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                // Animate stats with GSAP
                gsap.from('#home .glass-effect', {
                    opacity: 0,
                    y: 30,
                    duration: 0.8,
                    ease: "power2.out"
                });

                setTimeout(() => {
                    animateCounter('years-counter', 7);
                    if (STATS.students !== null) animateCounter('students-counter', STATS.students, '+');
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
const serviceCards = document.querySelectorAll('#services > div > div > div');
serviceCards.forEach(card => {
    const icon = card.querySelector('div.w-20');
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

// ScrollTrigger animations for sections - Initialize when GSAP is ready
function initScrollAnimations() {
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
    if (typeof gsap === 'undefined' || typeof ScrollTrigger === 'undefined') {
        return;
    }

    if (gsap.registerPlugin) {
        gsap.registerPlugin(ScrollTrigger, ScrollToPlugin);
    }

    // Animate sections on scroll
    const sections = document.querySelectorAll('section[id]');
    sections.forEach((section) => {
        gsap.from(section, {
            opacity: 0,
            y: 50,
            duration: 0.8,
            ease: "power2.out",
            scrollTrigger: {
                trigger: section,
                start: "top 80%",
                end: "bottom 20%",
                toggleActions: "play none none none"
            }
        });
    });

    // Animate instructor cards on scroll (after they're loaded)

    setTimeout(() => {
        const instructorCardsAnimate = document.querySelectorAll('.inst-card');
        instructorCardsAnimate.forEach((card, index) => {
            gsap.from(card, {
                opacity: 0,
                scale: 0.8,
                rotationY: -15,
                duration: 0.6,
                delay: index * 0.1,
                ease: "power2.out",
                scrollTrigger: {
                    trigger: card,
                    start: "top 85%",
                    toggleActions: "play none none none"
                }
            });
        });

        // Animate swiper navigation buttons
        const swiperButtons = document.querySelectorAll('.swiper-button-next, .swiper-button-prev');
        swiperButtons.forEach(btn => {
            const swiper = btn.closest('.swiper');
            if (swiper) {
                gsap.from(btn, {
                    opacity: 0,
                    scale: 0,
                    duration: 0.5,
                    ease: "back.out(1.7)",
                    scrollTrigger: {
                        trigger: swiper,
                        start: "top 80%",
                        toggleActions: "play none none none"
                    }
                });
            }
        });
    }, 1000); // Wait for data to load
}

// Initialize scroll animations when GSAP is ready
onReady(() => {
    if (typeof gsap !== 'undefined' && typeof ScrollTrigger !== 'undefined') {
        initScrollAnimations();
    } else {
        const checkGSAP = setInterval(() => {
            if (typeof gsap !== 'undefined' && typeof ScrollTrigger !== 'undefined') {
                clearInterval(checkGSAP);
                initScrollAnimations();
            }
        }, 100);
        setTimeout(() => clearInterval(checkGSAP), 5000);
    }
});

