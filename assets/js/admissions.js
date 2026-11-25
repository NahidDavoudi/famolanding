import {
    EXAM_TYPE_LABELS,
    formatExamType,
    getApiBase,
    getInitialLetter,
    initPageChrome,
    onDomReady,
    resolveSafeImageUrl,
    toPersianDigits,
} from './public-utils.js';

const EXAM_FILTER_KEYS = ['all', 'konkur', 'tizhooshan', 'nemoone', 'olympiad', 'other'];

const state = {
    selectedYear: null,
    selectedExamType: 'all',
    availableYears: [],
    loading: false,
};

function createAvatarPlaceholder(name) {
    const ph = document.createElement('div');
    ph.className = 'famo-adm-avatar-ph';
    ph.setAttribute('aria-hidden', 'true');
    ph.textContent = getInitialLetter(name);
    return ph;
}

function buildAdmissionCard(item) {
    const card = document.createElement('article');
    card.className = 'famo-adm-card hover-lift';
    if (item.is_featured) {
        card.classList.add('famo-adm-card--featured');
    }

    const header = document.createElement('div');
    header.className = 'famo-adm-card-head';

    const mediaWrap = document.createElement('div');
    mediaWrap.className = 'famo-adm-avatar-wrap';

    const safeImg = resolveSafeImageUrl(item.image_url);
    if (safeImg) {
        const img = document.createElement('img');
        img.className = 'famo-adm-avatar';
        img.src = safeImg;
        img.alt = String(item.student_name || 'دانش‌آموز پذیرفته‌شده');
        img.width = 72;
        img.height = 72;
        img.loading = 'lazy';
        img.decoding = 'async';
        img.addEventListener('error', () => {
            mediaWrap.replaceChildren(createAvatarPlaceholder(item.student_name));
        });
        mediaWrap.appendChild(img);
    } else {
        mediaWrap.appendChild(createAvatarPlaceholder(item.student_name));
    }

    const identity = document.createElement('div');
    identity.className = 'famo-adm-identity';

    const nameEl = document.createElement('h3');
    nameEl.className = 'famo-adm-name';
    nameEl.textContent = String(item.student_name || '');

    const resultEl = document.createElement('p');
    resultEl.className = 'famo-adm-result';
    resultEl.textContent = toPersianDigits(String(item.result_title || ''));

    identity.appendChild(nameEl);
    identity.appendChild(resultEl);

    header.appendChild(mediaWrap);
    header.appendChild(identity);
    card.appendChild(header);

    const metaRow = document.createElement('div');
    metaRow.className = 'famo-adm-meta';

    const examBadge = document.createElement('span');
    examBadge.className = 'famo-adm-chip famo-adm-chip--exam';
    examBadge.textContent = formatExamType(item.exam_type);
    metaRow.appendChild(examBadge);

    if (item.rank_value !== null && item.rank_value !== undefined && String(item.rank_value).trim() !== '') {
        const rankBadge = document.createElement('span');
        rankBadge.className = 'famo-adm-chip famo-adm-chip--rank';
        const rankRaw = String(item.rank_value).trim();
        const rankText = /^رتبه\b/.test(rankRaw) ? rankRaw : `رتبه ${rankRaw}`;
        rankBadge.textContent = toPersianDigits(rankText);
        metaRow.appendChild(rankBadge);
    }

    if (item.field !== null && item.field !== undefined && String(item.field).trim() !== '') {
        const fieldBadge = document.createElement('span');
        fieldBadge.className = 'famo-adm-chip famo-adm-chip--field';
        fieldBadge.textContent = String(item.field).trim();
        metaRow.appendChild(fieldBadge);
    }

    card.appendChild(metaRow);

    if (item.quote !== null && item.quote !== undefined && String(item.quote).trim() !== '') {
        const quote = document.createElement('blockquote');
        quote.className = 'famo-adm-quote';
        quote.textContent = `«${String(item.quote).trim()}»`;
        card.appendChild(quote);
    }

    return card;
}

function buildSkeletonMarkup() {
    const frag = document.createDocumentFragment();
    const grid = document.createElement('div');
    grid.className = 'famo-adm-grid';
    grid.setAttribute('aria-hidden', 'true');

    for (let i = 0; i < 6; i += 1) {
        const skel = document.createElement('div');
        skel.className = 'famo-adm-card famo-adm-skel';

        const top = document.createElement('div');
        top.className = 'famo-adm-card-head';

        const circle = document.createElement('div');
        circle.className = 'skeleton-shimmer famo-adm-skel-avatar';

        const lines = document.createElement('div');
        lines.className = 'famo-adm-skel-lines';
        const l1 = document.createElement('div');
        l1.className = 'skeleton-shimmer famo-adm-skel-line famo-adm-skel-line--name';
        const l2 = document.createElement('div');
        l2.className = 'skeleton-shimmer famo-adm-skel-line famo-adm-skel-line--result';
        lines.append(l1, l2);

        top.append(circle, lines);

        const chips = document.createElement('div');
        chips.className = 'famo-adm-meta';
        for (let c = 0; c < 3; c += 1) {
            const chip = document.createElement('span');
            chip.className = 'skeleton-shimmer famo-adm-skel-chip';
            chips.appendChild(chip);
        }

        skel.append(top, chips);
        grid.appendChild(skel);
    }

    frag.appendChild(grid);
    return frag;
}

function renderYearTabs() {
    const tabsEl = document.getElementById('admissionsYearTabs');
    if (!tabsEl) return;

    const frag = document.createDocumentFragment();

    const makeTab = (label, yearValue, isActive) => {
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = isActive ? 'famo-filter-tab is-active' : 'famo-filter-tab';
        btn.setAttribute('role', 'tab');
        btn.setAttribute('aria-selected', isActive ? 'true' : 'false');
        btn.textContent = label;
        btn.addEventListener('click', () => {
            if (state.selectedYear === yearValue) return;
            state.selectedYear = yearValue;
            renderYearTabs();
            loadAdmissions();
        });
        return btn;
    };

    frag.appendChild(makeTab('همه سال‌ها', null, state.selectedYear === null));

    const sortedYears = [...state.availableYears].sort((a, b) => Number(b) - Number(a));
    for (const yr of sortedYears) {
        frag.appendChild(makeTab(`سال ${toPersianDigits(yr)}`, yr, state.selectedYear === yr));
    }

    tabsEl.replaceChildren(frag);
}

function renderExamTypeChips() {
    const chipsEl = document.getElementById('admissionsExamFilters');
    if (!chipsEl) return;

    const frag = document.createDocumentFragment();

    for (const key of EXAM_FILTER_KEYS) {
        const isActive = state.selectedExamType === key;
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = isActive ? 'famo-filter-chip is-active' : 'famo-filter-chip';
        btn.setAttribute('aria-pressed', isActive ? 'true' : 'false');
        btn.textContent = key === 'all' ? 'همه آزمون‌ها' : EXAM_TYPE_LABELS[key];
        btn.addEventListener('click', () => {
            if (state.selectedExamType === key) return;
            state.selectedExamType = key;
            renderExamTypeChips();
            loadAdmissions();
        });
        frag.appendChild(btn);
    }

    chipsEl.replaceChildren(frag);
}

function renderEmptyState(container, isFiltered) {
    const box = document.createElement('div');
    box.className = 'famo-empty-message famo-state-box';

    const title = document.createElement('p');
    title.className = 'famo-state-title';
    title.textContent = isFiltered
        ? 'قبولی‌ای با فیلتر انتخاب‌شده یافت نشد.'
        : 'به‌زودی...';

    const desc = document.createElement('p');
    desc.className = 'famo-state-desc';
    desc.textContent = isFiltered
        ? 'می‌توانید فیلتر سال یا نوع آزمون را تغییر دهید تا سایر قبولی‌ها نمایش داده شوند.'
        : 'فهرست قبولی‌های آموزشگاه فامو در حال تکمیل است و به‌زودی در این بخش منتشر می‌شود.';

    box.append(title, desc);

    if (isFiltered) {
        const resetBtn = document.createElement('button');
        resetBtn.type = 'button';
        resetBtn.className = 'famo-state-btn';
        resetBtn.textContent = 'نمایش همه قبولی‌ها';
        resetBtn.addEventListener('click', () => {
            state.selectedYear = null;
            state.selectedExamType = 'all';
            renderYearTabs();
            renderExamTypeChips();
            loadAdmissions();
        });
        box.appendChild(resetBtn);
    }

    container.replaceChildren(box);
}

function renderErrorState(container, message) {
    const box = document.createElement('div');
    box.className = 'famo-empty-message famo-state-box famo-state-box--error';
    box.setAttribute('role', 'alert');

    const title = document.createElement('p');
    title.className = 'famo-state-title';
    title.textContent = 'خطا در دریافت اطلاعات قبولی‌ها';

    const desc = document.createElement('p');
    desc.className = 'famo-state-desc';
    desc.textContent = message || 'امکان برقراری ارتباط با سرور وجود ندارد. لطفاً دوباره تلاش کنید.';

    const retryBtn = document.createElement('button');
    retryBtn.type = 'button';
    retryBtn.className = 'famo-state-btn';
    retryBtn.textContent = 'تلاش مجدد';
    retryBtn.addEventListener('click', () => {
        loadAdmissions();
    });

    box.append(title, desc, retryBtn);
    container.replaceChildren(box);
}

function normalizeGroups(data) {
    if (Array.isArray(data?.groups) && data.groups.length > 0) {
        return [...data.groups]
            .filter((g) => g && Array.isArray(g.items) && g.items.length > 0)
            .sort((a, b) => Number(b.jalali_year) - Number(a.jalali_year));
    }

    if (Array.isArray(data?.items) && data.items.length > 0) {
        const byYear = new Map();
        for (const item of data.items) {
            const yr = Number(item.jalali_year) || 0;
            if (!byYear.has(yr)) byYear.set(yr, []);
            byYear.get(yr).push(item);
        }
        return Array.from(byYear.entries())
            .sort((a, b) => b[0] - a[0])
            .map(([jalali_year, items]) => ({
                jalali_year,
                count: items.length,
                items,
            }));
    }

    return [];
}

function renderGroups(container, groups) {
    const frag = document.createDocumentFragment();

    for (const group of groups) {
        const section = document.createElement('section');
        section.className = 'famo-adm-group';

        const headingRow = document.createElement('div');
        headingRow.className = 'famo-adm-group-head';

        const h2 = document.createElement('h2');
        h2.className = 'famo-adm-group-title';
        h2.textContent = `قبولی‌های سال ${toPersianDigits(group.jalali_year)}`;

        const count = Number.isFinite(Number(group.count)) ? Number(group.count) : group.items.length;
        const countBadge = document.createElement('span');
        countBadge.className = 'famo-adm-group-count';
        countBadge.textContent = `${toPersianDigits(count)} قبولی`;

        headingRow.append(h2, countBadge);

        const grid = document.createElement('div');
        grid.className = 'famo-adm-grid';
        for (const item of group.items) {
            grid.appendChild(buildAdmissionCard(item));
        }

        section.append(headingRow, grid);
        frag.appendChild(section);
    }

    container.replaceChildren(frag);
}

async function loadAdmissions(isInitial = false) {
    const container = document.getElementById('admissionsContainer');
    if (!container) return;

    const isFiltered = state.selectedYear !== null || state.selectedExamType !== 'all';
    const preserveFallback = isInitial && !isFiltered && container.dataset.hasFallback === '1';

    state.loading = true;
    container.setAttribute('aria-busy', 'true');
    if (!preserveFallback) {
        container.replaceChildren(buildSkeletonMarkup());
    }

    const params = new URLSearchParams();
    params.set('group_by', 'year');
    if (state.selectedYear !== null) {
        params.set('year', String(state.selectedYear));
    }
    if (state.selectedExamType && state.selectedExamType !== 'all') {
        params.set('exam_type', state.selectedExamType);
    }

    const url = `${getApiBase()}/public/admissions?${params.toString()}`;

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
                const msg = payload?.error?.message || 'دریافت فهرست قبولی‌ها با خطا مواجه شد.';
                renderErrorState(container, msg);
            }
            return;
        }

        const data = payload.data || {};
        if (Array.isArray(data.years) && data.years.length > 0 && state.selectedYear === null) {
            state.availableYears = data.years
                .map((y) => Number.parseInt(String(y), 10))
                .filter((y) => Number.isInteger(y) && y >= 1380 && y <= 1450)
                .sort((a, b) => b - a);
            renderYearTabs();
        } else if (Array.isArray(data.years) && state.availableYears.length === 0) {
            state.availableYears = data.years
                .map((y) => Number.parseInt(String(y), 10))
                .filter((y) => Number.isInteger(y))
                .sort((a, b) => b - a);
            renderYearTabs();
        }

        const groups = normalizeGroups(data);

        if (groups.length === 0) {
            if (!preserveFallback) {
                renderEmptyState(container, isFiltered);
            }
            return;
        }

        renderGroups(container, groups);
    } catch {
        if (!preserveFallback) {
            renderErrorState(container, 'ارتباط با سرور برقرار نشد. لطفاً اتصال اینترنت خود را بررسی کرده و دوباره تلاش کنید.');
        }
    } finally {
        state.loading = false;
        container.setAttribute('aria-busy', 'false');
    }
}

onDomReady(() => {
    initPageChrome();
    const container = document.getElementById('admissionsContainer');
    const yearsAttr = container?.dataset.ssrYears || '';
    if (yearsAttr) {
        state.availableYears = yearsAttr
            .split(',')
            .map((y) => Number.parseInt(y.trim(), 10))
            .filter((y) => Number.isInteger(y) && y >= 1380 && y <= 1450)
            .sort((a, b) => b - a);
    }
    renderYearTabs();
    renderExamTypeChips();
    if (container && container.dataset.ssr === '1') {
        return;
    }
    loadAdmissions(true);
});
