/**
 * Shared pure utilities and chrome helpers for Famo public pages
 * (admissions.php & testimonials.php).
 * Strictly DOM-safe: never uses innerHTML for user or API data.
 */

const PERSIAN_DIGITS = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];

export const EXAM_TYPE_LABELS = Object.freeze({
    konkur: 'کنکور سراسری',
    tizhooshan: 'تیزهوشان',
    nemoone: 'نمونه دولتی',
    olympiad: 'المپیاد علمی',
    other: 'سایر آزمون‌ها',
});

export const AUTHOR_ROLE_LABELS = Object.freeze({
    student: 'دانش‌آموز',
    parent: 'ولی دانش‌آموز',
    graduate: 'فارغ‌التحصیل',
    other: 'سایر',
});

export const ALLOWED_AUTHOR_ROLES = Object.freeze(['student', 'parent', 'graduate', 'other']);

export function toPersianDigits(value) {
    return String(value ?? '').replace(/[0-9]/g, (d) => PERSIAN_DIGITS[Number(d)]);
}

export function formatPersianRating(value) {
    const num = Number(value);
    if (!Number.isFinite(num) || num <= 0) return toPersianDigits('0');
    const fixed = Number.isInteger(num) ? String(num) : num.toFixed(1).replace(/\.0$/, '');
    return toPersianDigits(fixed.replace('.', '٫'));
}

export function normalizeApiBase(url) {
    const trimmed = String(url ?? '').trim().replace(/\/+$/, '');
    if (!trimmed) return 'https://api.famoacademy.ir/api/v1';
    return /\/api\/v1$/i.test(trimmed) ? trimmed : `${trimmed}/api/v1`;
}

export function getApiBase() {
    const configured =
        typeof window !== 'undefined' && window.APP_CONFIG && window.APP_CONFIG.apiUrl
            ? window.APP_CONFIG.apiUrl
            : '';
    return normalizeApiBase(configured);
}

export function getUploadsBase() {
    const configured =
        typeof window !== 'undefined' && window.APP_CONFIG && window.APP_CONFIG.uploadsUrl
            ? window.APP_CONFIG.uploadsUrl
            : 'https://api.famoacademy.ir/uploads';
    return String(configured).replace(/\/+$/, '');
}

export function resolveSafeImageUrl(raw) {
    const src = String(raw ?? '').trim();
    if (!src) return '';
    if (/^(javascript|vbscript|data):/i.test(src)) return '';
    if (/^https?:\/\//i.test(src)) return src;
    if (src.startsWith('/')) return src;
    if (src.startsWith('uploads/') || src.startsWith('../') || src.startsWith('assets/')) return src;
    return `${getUploadsBase()}/${src.replace(/^\/+/, '')}`;
}

export function getInitialLetter(name) {
    const trimmed = String(name ?? '').trim();
    if (!trimmed) return 'ف';
    return Array.from(trimmed)[0] || 'ف';
}

export function formatExamType(examType) {
    const key = String(examType ?? '').trim();
    return EXAM_TYPE_LABELS[key] || EXAM_TYPE_LABELS.other;
}

export function formatAuthorRole(role) {
    const key = String(role ?? '').trim();
    return AUTHOR_ROLE_LABELS[key] || AUTHOR_ROLE_LABELS.other;
}

/**
 * Validate testimonial form input according to OpenAPI PublicTestimonialSubmitRequest:
 * - author_name: string, minLength 2, maxLength 120, required
 * - author_role: enum [student, parent, graduate, other], default student
 * - author_meta: string | null, maxLength 150
 * - rating: integer 1..5, required
 * - content: string, minLength 10, maxLength 1500, required
 */
export function validateTestimonialInput(raw) {
    const authorName = String(raw?.author_name ?? '').trim();
    const authorRoleRaw = String(raw?.author_role ?? 'student').trim();
    const authorRole = ALLOWED_AUTHOR_ROLES.includes(authorRoleRaw) ? authorRoleRaw : 'student';
    const authorMeta = String(raw?.author_meta ?? '').trim();
    const ratingNum = Number.parseInt(String(raw?.rating ?? ''), 10);
    const content = String(raw?.content ?? '').trim();

    const errors = {};

    if (!authorName) {
        errors.author_name = 'لطفاً نام خود را وارد کنید.';
    } else if (authorName.length < 2) {
        errors.author_name = `نام باید حداقل ${toPersianDigits(2)} کاراکتر باشد.`;
    } else if (authorName.length > 120) {
        errors.author_name = `نام نمی‌تواند بیشتر از ${toPersianDigits(120)} کاراکتر باشد.`;
    }

    if (!ALLOWED_AUTHOR_ROLES.includes(authorRoleRaw)) {
        errors.author_role = 'لطفاً یکی از نقش‌های معتبر را انتخاب کنید.';
    }

    if (authorMeta.length > 150) {
        errors.author_meta = `توضیح کوتاه نمی‌تواند بیشتر از ${toPersianDigits(150)} کاراکتر باشد.`;
    }

    if (!Number.isInteger(ratingNum) || ratingNum < 1 || ratingNum > 5) {
        errors.rating = `لطفاً امتیازی بین ${toPersianDigits(1)} تا ${toPersianDigits(5)} انتخاب کنید.`;
    }

    if (!content) {
        errors.content = 'لطفاً متن نظر خود را بنویسید.';
    } else if (content.length < 10) {
        errors.content = `متن نظر باید حداقل ${toPersianDigits(10)} کاراکتر باشد.`;
    } else if (content.length > 1500) {
        errors.content = `متن نظر نمی‌تواند بیشتر از ${toPersianDigits(1500)} کاراکتر باشد.`;
    }

    return {
        valid: Object.keys(errors).length === 0,
        errors,
        normalized: {
            author_name: authorName,
            author_role: authorRole,
            author_meta: authorMeta ? authorMeta : null,
            rating: ratingNum,
            content,
        },
    };
}

const KNOWN_TESTIMONIAL_FIELDS = ['author_name', 'author_role', 'author_meta', 'rating', 'content'];

function inferFieldFromMessage(message) {
    const msg = String(message ?? '');
    if (!msg) return null;
    if (/author_name|نام/i.test(msg)) return 'author_name';
    if (/author_role|نقش/i.test(msg)) return 'author_role';
    if (/author_meta|سمت|توضیح|پایه|رشته/i.test(msg)) return 'author_meta';
    if (/rating|امتیاز|ستاره/i.test(msg)) return 'rating';
    if (/content|متن|نظر/i.test(msg)) return 'content';
    return null;
}

/**
 * Extracts 422 field-level and general errors from ErrorResponse payloads.
 * Supports both structured maps (error.fields / error.errors / errors)
 * and flat OpenAPI ErrorResponse ({ error: { code, message } }).
 */
export function extractValidationErrors(payload) {
    const fieldErrors = {};
    let generalMessage = '';

    const rawMap =
        (payload?.error && (payload.error.fields || payload.error.errors || payload.error.details)) ||
        payload?.errors ||
        null;

    if (rawMap && typeof rawMap === 'object' && !Array.isArray(rawMap)) {
        for (const [key, val] of Object.entries(rawMap)) {
            if (!KNOWN_TESTIMONIAL_FIELDS.includes(key)) continue;
            if (Array.isArray(val) && val.length > 0) {
                fieldErrors[key] = String(val[0]);
            } else if (typeof val === 'string' && val.trim()) {
                fieldErrors[key] = val.trim();
            }
        }
    }

    const topMsg = String(payload?.error?.message || payload?.message || '').trim();
    if (Object.keys(fieldErrors).length === 0 && topMsg) {
        const inferred = inferFieldFromMessage(topMsg);
        if (inferred) {
            fieldErrors[inferred] = topMsg;
        } else {
            generalMessage = topMsg;
        }
    } else if (topMsg && Object.keys(fieldErrors).length > 0) {
        generalMessage = topMsg;
    }

    if (Object.keys(fieldErrors).length === 0 && !generalMessage) {
        generalMessage = 'لطفاً اطلاعات واردشده را بررسی کرده و دوباره تلاش کنید.';
    }

    return { fieldErrors, generalMessage };
}

/**
 * Formats 429 rate-limit message using Retry-After header when available.
 */
export function formatRateLimitMessage(retryAfterHeader) {
    const base = 'تعداد درخواست‌ها زیاد است، کمی بعد دوباره تلاش کنید.';
    const raw = String(retryAfterHeader ?? '').trim();
    if (!raw) return base;

    const seconds = Number.parseInt(raw, 10);
    if (!Number.isFinite(seconds) || seconds <= 0) {
        return base;
    }

    if (seconds >= 60) {
        const minutes = Math.ceil(seconds / 60);
        return `تعداد درخواست‌ها زیاد است، لطفاً حدود ${toPersianDigits(minutes)} دقیقه دیگر دوباره تلاش کنید.`;
    }
    return `تعداد درخواست‌ها زیاد است، لطفاً ${toPersianDigits(seconds)} ثانیه دیگر دوباره تلاش کنید.`;
}

/**
 * Creates an SVG star icon using DOM namespace API (zero innerHTML).
 */
export function createStarSvg(filled = true, size = 18) {
    const ns = 'http://www.w3.org/2000/svg';
    const svg = document.createElementNS(ns, 'svg');
    svg.setAttribute('viewBox', '0 0 24 24');
    svg.setAttribute('width', String(size));
    svg.setAttribute('height', String(size));
    svg.setAttribute('aria-hidden', 'true');
    svg.setAttribute('class', filled ? 'famo-star famo-star--filled' : 'famo-star famo-star--empty');

    const path = document.createElementNS(ns, 'path');
    path.setAttribute(
        'd',
        'M12 2.5l2.93 5.94 6.56.95-4.75 4.63 1.12 6.53L12 17.46 6.14 20.55l1.12-6.53L2.51 9.39l6.56-.95L12 2.5z'
    );
    svg.appendChild(path);
    return svg;
}

/**
 * Wires shared header mobile menu toggle and footer copyright year.
 */
export function initPageChrome() {
    const mobileMenuBtn = document.getElementById('mobileMenuBtn');
    const mobileMenu = document.getElementById('mobileMenu');

    const setMenuIcon = (isOpen) => {
        if (!mobileMenuBtn) return;
        const icon = document.createElement('i');
        icon.className = 'icon icon--lg';
        icon.setAttribute('data-lucide', isOpen ? 'x' : 'menu');
        icon.setAttribute('aria-hidden', 'true');
        mobileMenuBtn.replaceChildren(icon);
        mobileMenuBtn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        if (typeof window.refreshLucideIcons === 'function') {
            window.refreshLucideIcons(mobileMenuBtn);
        }
    };

    if (mobileMenuBtn && mobileMenu) {
        mobileMenuBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            const willOpen = mobileMenu.classList.contains('hidden');
            mobileMenu.classList.toggle('hidden');
            setMenuIcon(willOpen);
        });

        document.addEventListener('click', (e) => {
            if (!mobileMenu.contains(e.target) && !mobileMenuBtn.contains(e.target)) {
                if (!mobileMenu.classList.contains('hidden')) {
                    mobileMenu.classList.add('hidden');
                    setMenuIcon(false);
                }
            }
        });
    }

    document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
        anchor.addEventListener('click', (e) => {
            const targetId = anchor.getAttribute('href');
            if (!targetId || targetId === '#') return;
            const targetEl = document.querySelector(targetId);
            if (!targetEl) return;
            e.preventDefault();
            if (mobileMenu && !mobileMenu.classList.contains('hidden')) {
                mobileMenu.classList.add('hidden');
                setMenuIcon(false);
            }
            window.scrollTo({
                top: targetEl.offsetTop - 96,
                behavior: 'smooth',
            });
        });
    });

    const copyrightYear = document.getElementById('copyright-year');
    if (copyrightYear) {
        try {
            const persianYear = new Date().toLocaleDateString('fa-IR', { year: 'numeric' });
            copyrightYear.textContent = toPersianDigits(persianYear);
        } catch {
            // Keep server default
        }
    }
}

export function onDomReady(fn) {
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', fn, { once: true });
    } else {
        fn();
    }
}
