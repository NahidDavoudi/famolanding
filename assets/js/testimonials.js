import {
    AUTHOR_ROLE_LABELS,
    createStarSvg,
    extractValidationErrors,
    formatAuthorRole,
    formatPersianRating,
    formatRateLimitMessage,
    getApiBase,
    getInitialLetter,
    initPageChrome,
    onDomReady,
    toPersianDigits,
    validateTestimonialInput,
} from './public-utils.js';

const PER_PAGE = 9;
const ROLE_FILTERS = ['all', 'student', 'parent', 'graduate', 'other'];
const FORM_FIELDS = ['author_name', 'author_role', 'author_meta', 'rating', 'content'];

const listState = {
    page: 1,
    role: 'all',
};

let isSubmitting = false;

function buildStarsRow(ratingValue) {
    const rating = Number.parseInt(String(ratingValue ?? ''), 10);
    if (!Number.isInteger(rating) || rating < 1 || rating > 5) return null;

    const wrap = document.createElement('div');
    wrap.className = 'famo-stars';
    wrap.setAttribute('aria-label', `امتیاز ${toPersianDigits(rating)} از ${toPersianDigits(5)}`);

    for (let i = 1; i <= 5; i += 1) {
        wrap.appendChild(createStarSvg(i <= rating, 18));
    }

    return wrap;
}

function renderSummary(summary) {
    const container = document.getElementById('testimonialsSummary');
    if (!container) return;

    const totalApproved = Number.parseInt(String(summary?.total_approved ?? 0), 10) || 0;
    const avgRating = Number(summary?.average_rating ?? 0);

    const card = document.createElement('div');
    card.className = 'famo-testi-summary-grid';

    const totalBox = document.createElement('div');
    totalBox.className = 'famo-testi-stat';
    const totalNum = document.createElement('div');
    totalNum.className = 'famo-testi-stat-value';
    totalNum.textContent = toPersianDigits(totalApproved);
    const totalLabel = document.createElement('div');
    totalLabel.className = 'famo-testi-stat-label';
    totalLabel.textContent = 'نظر تأییدشده';
    totalBox.append(totalNum, totalLabel);

    const avgBox = document.createElement('div');
    avgBox.className = 'famo-testi-stat';
    const avgTop = document.createElement('div');
    avgTop.className = 'famo-testi-stat-rating-row';

    const avgNum = document.createElement('span');
    avgNum.className = 'famo-testi-stat-value';
    avgNum.textContent = totalApproved > 0 && Number.isFinite(avgRating) && avgRating > 0
        ? formatPersianRating(avgRating)
        : '—';

    const avgOutOf = document.createElement('span');
    avgOutOf.className = 'famo-testi-stat-outof';
    avgOutOf.textContent = totalApproved > 0 ? `از ${toPersianDigits(5)}` : '';

    avgTop.append(avgNum, avgOutOf);

    const starsWrap = document.createElement('div');
    starsWrap.className = 'famo-stars famo-stars--summary';
    const rounded = totalApproved > 0 && Number.isFinite(avgRating) ? Math.round(avgRating) : 0;
    for (let i = 1; i <= 5; i += 1) {
        starsWrap.appendChild(createStarSvg(i <= rounded, 18));
    }

    const avgLabel = document.createElement('div');
    avgLabel.className = 'famo-testi-stat-label';
    avgLabel.textContent = 'میانگین امتیاز رضایت';

    avgBox.append(avgTop, starsWrap, avgLabel);

    const ctaBox = document.createElement('div');
    ctaBox.className = 'famo-testi-stat-cta';
    const ctaText = document.createElement('p');
    ctaText.className = 'famo-testi-stat-note';
    ctaText.textContent = 'تجربه حضور در کلاس‌ها یا مشاوره فامو را داشته‌اید؟';
    const ctaLink = document.createElement('a');
    ctaLink.href = '#submit-testimonial';
    ctaLink.className = 'famo-state-btn';
    ctaLink.textContent = 'ثبت نظر جدید';
    ctaBox.append(ctaText, ctaLink);

    card.append(totalBox, avgBox, ctaBox);
    container.replaceChildren(card);
}

function buildTestimonialCard(item) {
    const card = document.createElement('article');
    card.className = 'famo-testi-card hover-lift';
    if (item.is_featured) {
        card.classList.add('famo-testi-card--featured');
    }

    const head = document.createElement('div');
    head.className = 'famo-testi-card-head';

    const authorWrap = document.createElement('div');
    authorWrap.className = 'famo-testi-author';

    const avatar = document.createElement('div');
    avatar.className = 'famo-testi-avatar';
    avatar.setAttribute('aria-hidden', 'true');
    avatar.textContent = getInitialLetter(item.author_name);

    const authorInfo = document.createElement('div');
    authorInfo.className = 'famo-testi-author-info';

    const nameRow = document.createElement('div');
    nameRow.className = 'famo-testi-name-row';

    const nameEl = document.createElement('h3');
    nameEl.className = 'famo-testi-name';
    nameEl.textContent = String(item.author_name || '');

    const roleBadge = document.createElement('span');
    roleBadge.className = 'famo-testi-role-badge';
    roleBadge.textContent = formatAuthorRole(item.author_role);

    nameRow.append(nameEl, roleBadge);
    authorInfo.appendChild(nameRow);

    if (item.author_meta !== null && item.author_meta !== undefined && String(item.author_meta).trim() !== '') {
        const metaEl = document.createElement('p');
        metaEl.className = 'famo-testi-meta';
        metaEl.textContent = toPersianDigits(String(item.author_meta).trim());
        authorInfo.appendChild(metaEl);
    }

    authorWrap.append(avatar, authorInfo);
    head.appendChild(authorWrap);

    const starsRow = buildStarsRow(item.rating);
    if (starsRow) {
        head.appendChild(starsRow);
    }

    card.appendChild(head);

    const contentEl = document.createElement('p');
    contentEl.className = 'famo-testi-content';
    contentEl.textContent = String(item.content || '');
    card.appendChild(contentEl);

    const hasCourse = item.course_name !== null && item.course_name !== undefined && String(item.course_name).trim() !== '';
    const hasInstructor = item.instructor_name !== null && item.instructor_name !== undefined && String(item.instructor_name).trim() !== '';

    if (hasCourse || hasInstructor) {
        const footer = document.createElement('div');
        footer.className = 'famo-testi-tags';

        if (hasCourse) {
            const courseTag = document.createElement('span');
            courseTag.className = 'famo-adm-chip famo-adm-chip--exam';
            courseTag.textContent = `دوره: ${String(item.course_name).trim()}`;
            footer.appendChild(courseTag);
        }

        if (hasInstructor) {
            const instTag = document.createElement('span');
            instTag.className = 'famo-adm-chip famo-adm-chip--field';
            instTag.textContent = `استاد: ${String(item.instructor_name).trim()}`;
            footer.appendChild(instTag);
        }

        card.appendChild(footer);
    }

    return card;
}

function buildSkeletons() {
    const frag = document.createDocumentFragment();
    for (let i = 0; i < 6; i += 1) {
        const skel = document.createElement('div');
        skel.className = 'famo-testi-card famo-testi-skel';
        skel.setAttribute('aria-hidden', 'true');

        const head = document.createElement('div');
        head.className = 'famo-testi-card-head';

        const avatar = document.createElement('div');
        avatar.className = 'skeleton-shimmer famo-testi-skel-avatar';

        const lines = document.createElement('div');
        lines.className = 'famo-adm-skel-lines';
        const l1 = document.createElement('div');
        l1.className = 'skeleton-shimmer famo-adm-skel-line famo-adm-skel-line--name';
        const l2 = document.createElement('div');
        l2.className = 'skeleton-shimmer famo-adm-skel-line famo-adm-skel-line--result';
        lines.append(l1, l2);

        head.append(avatar, lines);

        const body1 = document.createElement('div');
        body1.className = 'skeleton-shimmer famo-testi-skel-body';
        const body2 = document.createElement('div');
        body2.className = 'skeleton-shimmer famo-testi-skel-body famo-testi-skel-body--short';

        skel.append(head, body1, body2);
        frag.appendChild(skel);
    }
    return frag;
}

function renderRoleFilters() {
    const el = document.getElementById('testimonialsRoleFilters');
    if (!el) return;

    const frag = document.createDocumentFragment();
    for (const roleKey of ROLE_FILTERS) {
        const isActive = listState.role === roleKey;
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = isActive ? 'famo-filter-chip is-active' : 'famo-filter-chip';
        btn.setAttribute('aria-pressed', isActive ? 'true' : 'false');
        btn.textContent = roleKey === 'all' ? 'همه نظرات' : AUTHOR_ROLE_LABELS[roleKey];
        btn.addEventListener('click', () => {
            if (listState.role === roleKey) return;
            listState.role = roleKey;
            listState.page = 1;
            renderRoleFilters();
            loadTestimonials();
        });
        frag.appendChild(btn);
    }

    el.replaceChildren(frag);
}

function renderPagination(pagination) {
    const el = document.getElementById('testimonialsPagination');
    if (!el) return;

    const page = Number.parseInt(String(pagination?.page ?? 1), 10) || 1;
    const totalPages = Number.parseInt(String(pagination?.total_pages ?? 1), 10) || 1;

    if (totalPages <= 1) {
        el.replaceChildren();
        return;
    }

    const nav = document.createElement('nav');
    nav.className = 'famo-pagination';
    nav.setAttribute('aria-label', 'صفحه‌بندی نظرات');

    const prevBtn = document.createElement('button');
    prevBtn.type = 'button';
    prevBtn.className = 'famo-page-btn';
    prevBtn.textContent = 'صفحه قبل';
    prevBtn.disabled = page <= 1;
    prevBtn.addEventListener('click', () => {
        if (listState.page <= 1) return;
        listState.page -= 1;
        loadTestimonials(true);
    });

    const info = document.createElement('span');
    info.className = 'famo-page-info';
    info.textContent = `صفحه ${toPersianDigits(page)} از ${toPersianDigits(totalPages)}`;

    const nextBtn = document.createElement('button');
    nextBtn.type = 'button';
    nextBtn.className = 'famo-page-btn';
    nextBtn.textContent = 'صفحه بعد';
    nextBtn.disabled = page >= totalPages;
    nextBtn.addEventListener('click', () => {
        if (listState.page >= totalPages) return;
        listState.page += 1;
        loadTestimonials(true);
    });

    nav.append(prevBtn, info, nextBtn);
    el.replaceChildren(nav);
}

function renderListEmpty(container, isFiltered) {
    const box = document.createElement('div');
    box.className = 'famo-empty-message famo-state-box';

    const title = document.createElement('p');
    title.className = 'famo-state-title';
    title.textContent = isFiltered
        ? 'نظری در این دسته‌بندی ثبت نشده است.'
        : 'هنوز نظری در این بخش منتشر نشده است.';

    const desc = document.createElement('p');
    desc.className = 'famo-state-desc';
    desc.textContent = isFiltered
        ? 'می‌توانید فیلتر نقش را روی «همه نظرات» قرار دهید.'
        : 'شما می‌توانید اولین نفری باشید که تجربه خود از آموزشگاه فامو را ثبت می‌کند.';

    box.append(title, desc);

    if (isFiltered) {
        const resetBtn = document.createElement('button');
        resetBtn.type = 'button';
        resetBtn.className = 'famo-state-btn';
        resetBtn.textContent = 'نمایش همه نظرات';
        resetBtn.addEventListener('click', () => {
            listState.role = 'all';
            listState.page = 1;
            renderRoleFilters();
            loadTestimonials();
        });
        box.appendChild(resetBtn);
    }

    container.replaceChildren(box);
}

function renderListError(container, message) {
    const box = document.createElement('div');
    box.className = 'famo-empty-message famo-state-box famo-state-box--error';
    box.setAttribute('role', 'alert');

    const title = document.createElement('p');
    title.className = 'famo-state-title';
    title.textContent = 'خطا در دریافت نظرات';

    const desc = document.createElement('p');
    desc.className = 'famo-state-desc';
    desc.textContent = message || 'امکان برقراری ارتباط با سرور وجود ندارد. لطفاً دوباره تلاش کنید.';

    const retryBtn = document.createElement('button');
    retryBtn.type = 'button';
    retryBtn.className = 'famo-state-btn';
    retryBtn.textContent = 'تلاش مجدد';
    retryBtn.addEventListener('click', () => {
        loadTestimonials();
    });

    box.append(title, desc, retryBtn);
    container.replaceChildren(box);
}

async function loadTestimonials(shouldScroll = false, isInitial = false) {
    const listEl = document.getElementById('testimonialsList');
    const paginationEl = document.getElementById('testimonialsPagination');
    if (!listEl) return;

    const isFiltered = listState.role !== 'all' || listState.page > 1;
    const preserveFallback = isInitial && !isFiltered && listEl.dataset.hasFallback === '1';

    listEl.setAttribute('aria-busy', 'true');
    if (!preserveFallback) {
        listEl.replaceChildren(buildSkeletons());
        if (paginationEl) paginationEl.replaceChildren();
    }

    const params = new URLSearchParams();
    params.set('page', String(listState.page));
    params.set('per_page', String(PER_PAGE));
    if (listState.role !== 'all') {
        params.set('role', listState.role);
    }

    const url = `${getApiBase()}/public/testimonials?${params.toString()}`;

    try {
        const res = await fetch(url, {
            method: 'GET',
            headers: { Accept: 'application/json' },
        });

        let payload = null;
        try {
            payload = await res.json();
        } catch {
            payload = null;
        }

        if (!res.ok || !payload || payload.success === false) {
            if (!preserveFallback) {
                const msg = payload?.error?.message || 'دریافت نظرات با خطا مواجه شد.';
                renderListError(listEl, msg);
            }
            return;
        }

        const data = payload.data || {};
        if (data.summary && typeof data.summary === 'object') {
            renderSummary(data.summary);
        }

        const items = Array.isArray(data.items) ? data.items : [];
        if (items.length === 0) {
            if (!preserveFallback) {
                renderListEmpty(listEl, listState.role !== 'all');
            }
            return;
        }

        const frag = document.createDocumentFragment();
        for (const item of items) {
            frag.appendChild(buildTestimonialCard(item));
        }
        listEl.replaceChildren(frag);
        renderPagination(payload.pagination);

        if (shouldScroll) {
            const section = document.getElementById('testimonials-list-section');
            if (section) {
                window.scrollTo({ top: section.offsetTop - 96, behavior: 'smooth' });
            }
        }
    } catch {
        if (!preserveFallback) {
            renderListError(listEl, 'ارتباط با سرور برقرار نشد. لطفاً اتصال اینترنت خود را بررسی کرده و دوباره تلاش کنید.');
        }
    } finally {
        listEl.setAttribute('aria-busy', 'false');
    }
}

/* ==================== Form Management ==================== */

function setFieldError(fieldName, message) {
    const errEl = document.getElementById(`err-${fieldName}`);
    const inputEl = document.getElementById(`testimonial-${fieldName}`);
    if (errEl) {
        errEl.textContent = message || '';
        errEl.hidden = !message;
    }
    if (inputEl) {
        if (message) {
            inputEl.setAttribute('aria-invalid', 'true');
            inputEl.classList.add('is-invalid');
        } else {
            inputEl.removeAttribute('aria-invalid');
            inputEl.classList.remove('is-invalid');
        }
    }
}

function clearAllFieldErrors() {
    for (const field of FORM_FIELDS) {
        setFieldError(field, '');
    }
}

function setFormStatus(type, message) {
    const statusEl = document.getElementById('testimonialFormStatus');
    if (!statusEl) return;
    if (!message) {
        statusEl.textContent = '';
        statusEl.hidden = true;
        statusEl.className = 'famo-form-status';
        return;
    }
    statusEl.textContent = message;
    statusEl.hidden = false;
    statusEl.className = `famo-form-status famo-form-status--${type}`;
}

function updateStarPickerVisual(selectedRating) {
    const picker = document.getElementById('testimonialRatingPicker');
    const hint = document.getElementById('testimonialRatingText');
    if (!picker) return;

    const labels = picker.querySelectorAll('.famo-rating-option');
    labels.forEach((label) => {
        const val = Number.parseInt(label.dataset.rating || '0', 10);
        const isActive = val <= selectedRating;
        label.classList.toggle('is-filled', isActive);
        const svgWrap = label.querySelector('.famo-rating-star-slot');
        if (svgWrap) {
            svgWrap.replaceChildren(createStarSvg(isActive, 24));
        }
    });

    if (hint) {
        hint.textContent = `${toPersianDigits(selectedRating)} از ${toPersianDigits(5)} ستاره`;
    }
}

function updateContentCounter() {
    const textarea = document.getElementById('testimonial-content');
    const counter = document.getElementById('testimonialContentCounter');
    if (!textarea || !counter) return;
    const len = textarea.value.trim().length;
    counter.textContent = `${toPersianDigits(len)} / ${toPersianDigits(1500)} کاراکتر`;
}

function initTestimonialForm() {
    const form = document.getElementById('testimonialForm');
    if (!form) return;

    const submitBtn = document.getElementById('testimonialSubmitBtn');
    const nameInput = document.getElementById('testimonial-author_name');
    const roleSelect = document.getElementById('testimonial-author_role');
    const metaInput = document.getElementById('testimonial-author_meta');
    const contentInput = document.getElementById('testimonial-content');
    const websiteInput = document.getElementById('testimonial-website');
    const ratingRadios = form.querySelectorAll('input[name="rating"]');

    const getSelectedRating = () => {
        const checked = form.querySelector('input[name="rating"]:checked');
        return checked ? Number.parseInt(checked.value, 10) : 5;
    };

    updateStarPickerVisual(getSelectedRating());
    updateContentCounter();

    ratingRadios.forEach((radio) => {
        radio.addEventListener('change', () => {
            updateStarPickerVisual(getSelectedRating());
            setFieldError('rating', '');
        });
    });

    if (contentInput) {
        contentInput.addEventListener('input', () => {
            updateContentCounter();
            setFieldError('content', '');
        });
    }

    if (nameInput) {
        nameInput.addEventListener('input', () => setFieldError('author_name', ''));
    }
    if (roleSelect) {
        roleSelect.addEventListener('change', () => setFieldError('author_role', ''));
    }
    if (metaInput) {
        metaInput.addEventListener('input', () => setFieldError('author_meta', ''));
    }

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        if (isSubmitting) return;

        clearAllFieldErrors();
        setFormStatus('', '');

        const validation = validateTestimonialInput({
            author_name: nameInput ? nameInput.value : '',
            author_role: roleSelect ? roleSelect.value : 'student',
            author_meta: metaInput ? metaInput.value : '',
            rating: getSelectedRating(),
            content: contentInput ? contentInput.value : '',
        });

        if (!validation.valid) {
            for (const [field, msg] of Object.entries(validation.errors)) {
                setFieldError(field, msg);
            }
            const firstErrField = Object.keys(validation.errors)[0];
            const firstEl = document.getElementById(`testimonial-${firstErrField}`);
            if (firstEl && typeof firstEl.focus === 'function') {
                firstEl.focus();
            }
            return;
        }

        const requestBody = {
            author_name: validation.normalized.author_name,
            author_role: validation.normalized.author_role,
            author_meta: validation.normalized.author_meta,
            rating: validation.normalized.rating,
            content: validation.normalized.content,
            website: websiteInput ? websiteInput.value : '',
        };

        isSubmitting = true;
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.textContent = 'در حال ارسال...';
        }

        try {
            const res = await fetch(`${getApiBase()}/public/testimonials`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                },
                body: JSON.stringify(requestBody),
            });

            let payload = null;
            try {
                payload = await res.json();
            } catch {
                payload = null;
            }

            if ((res.status === 201 || res.status === 200) && (!payload || payload.success !== false)) {
                form.reset();
                const defaultFive = form.querySelector('input[name="rating"][value="5"]');
                if (defaultFive) defaultFive.checked = true;
                updateStarPickerVisual(5);
                updateContentCounter();
                setFormStatus('success', 'نظر شما دریافت شد و پس از بررسی در سایت نمایش داده می‌شود.');
                return;
            }

            if (res.status === 409) {
                setFormStatus('warning', 'این نظر قبلاً ارسال شده است.');
                return;
            }

            if (res.status === 429) {
                const retryAfter = res.headers.get('Retry-After');
                setFormStatus('warning', formatRateLimitMessage(retryAfter));
                return;
            }

            if (res.status === 422) {
                const { fieldErrors, generalMessage } = extractValidationErrors(payload);
                for (const [field, msg] of Object.entries(fieldErrors)) {
                    setFieldError(field, msg);
                }
                if (generalMessage) {
                    setFormStatus('error', generalMessage);
                }
                return;
            }

            setFormStatus('error', 'خطایی در ثبت نظر رخ داد. لطفاً کمی بعد دوباره تلاش کنید.');
        } catch {
            // Network error: show generic message and preserve user input in the form
            setFormStatus('error', 'ارتباط با سرور برقرار نشد. متن نظر شما حفظ شده است؛ لطفاً دوباره تلاش کنید.');
        } finally {
            isSubmitting = false;
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.textContent = 'ثبت نظر';
            }
        }
    });
}

onDomReady(() => {
    initPageChrome();
    renderRoleFilters();
    initTestimonialForm();
    const listEl = document.getElementById('testimonialsList');
    if (listEl && listEl.dataset.ssr === '1') {
        return;
    }
    loadTestimonials(false, true);
});
