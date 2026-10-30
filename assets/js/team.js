const { default: API, onReady } = await import(`${window.APP_CONFIG.assetUrl}/js/api.js`);

// Escape dynamic text before injecting into HTML: & < > " '
function esc(value) {
    return String(value ?? '').replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
}

// Only allow https:// or /-rooted URLs (same-origin relative paths) into markup.
function safeUrl(value) {
    const s = String(value ?? '').trim();
    return (/^https?:\/\//i.test(s) || s.startsWith('/')) ? s : '';
}

const UPLOADS_BASE = (window.APP_CONFIG && window.APP_CONFIG.uploadsUrl) || 'https://api.famoacademy.ir/uploads';

function resolveImageUrl(value) {
    if (!value) return '';
    const src = String(value).trim();
    if (!src) return '';
    if (/^(https?:)?\/\//i.test(src) || src.startsWith('data:')) return src;
    if (src.startsWith('/')) return src;
    if (src.startsWith('uploads/') || src.startsWith('../') || src.startsWith('assets/')) return src;
    return `${UPLOADS_BASE.replace(/\/$/, '')}/${src}`;
}

function personImageUrl(person) {
    const raw = person.image_url || person.avatar_url || person.image || '';
    const resolved = resolveImageUrl(raw);
    return /^(javascript|vbscript):/i.test(resolved) ? '' : resolved;
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


function svgIcon(name, cls = '') {
    const c = cls ? ` ${cls}` : '';
    const icon = window.famoLucideName ? window.famoLucideName(name) : name.replace(/^icon-/, '');
    return `<i class="icon${c}" data-lucide="${icon}" aria-hidden="true"></i>`;
}

// Maps a social_links.platform value to a sprite icon.
// Add more entries here as you add icons to the sprite (e.g. icon-brand-linkedin).
const SOCIAL_ICON_MAP = {
    telegram: 'icon-telegram',
    instagram: 'icon-brand-instagram',
    whatsapp: 'icon-brand-whatsapp',
};

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

// کارت یکسان اعضای کادر (استاد و پشتیبان) — هم‌ساختار با صفحه اصلی
function normalizeInstructor(instructor) {
    return {
        role: 'instructor',
        roleLabel: 'استاد',
        name: instructor.name || '',
        title: instructor.title || 'استاد تخصصی',
        description: instructor.full_bio || instructor.description || '',
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
        description: supporter.full_bio || supporter.description || supporter.bio ||
            'پیگیری برنامه مطالعه، رفع اشکال درسی و همراهی مستمر دانش‌آموزان تا رسیدن به هدف.',
        image_url: supporter.image_url || supporter.avatar_url || supporter.image || '',
        initial_letter: supporter.initial_letter || '',
        social_links: supporter.social_links || [],
    };
}

function personCardHtml(person) {
    const role = person.role === 'supporter' ? 'supporter' : 'instructor';
    const initial = person.initial_letter || (person.name ? person.name.trim().charAt(0) : '');
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
                    : `<div class="inst-avatar inst-avatar-ph" aria-hidden="true">${esc(initial)}</div>`}
                <span class="person-role-badge">${esc(person.roleLabel || '')}</span>
            </div>
            <h3 class="inst-name">${esc(person.name)}</h3>
            <p class="inst-title">${esc(person.title)}</p>
            ${person.description ? `<p class="inst-bio">${esc(person.description)}</p>` : ''}
            ${footerBlock}
        </article>
    `;
}

function renderInstructorCard(instructor) {
    return personCardHtml(normalizeInstructor(instructor));
}

function renderSupporterCard(supporter) {
    return personCardHtml(normalizeSupporter(supporter));
}

async function loadInstructors() {
    const container = document.getElementById('instructorsContainer');
    if (!container) return;
    container.setAttribute('aria-busy', 'true');

    try {
        const result = await API.get('/public/instructors');
        container.setAttribute('aria-busy', 'false');

        if (!result.data || result.data.length === 0) {
            container.innerHTML = '<p class="col-span-full text-center text-gray-500 py-8">استادی ثبت نشده است</p>';
            return;
        }

        container.innerHTML = result.data.map(renderInstructorCard).join('');
        if (window.refreshLucideIcons) window.refreshLucideIcons(container);
    } catch (error) {
        console.error('Error loading instructors:', error);
        container.setAttribute('aria-busy', 'false');
        container.innerHTML = '<p class="col-span-full text-center text-gray-500 py-8">خطا در بارگذاری اساتید</p>';
    }
}

async function loadSupporters() {
    const container = document.getElementById('supportersContainer');
    if (!container) return;
    container.setAttribute('aria-busy', 'true');

    try {
        const result = await API.get('/public/supporters');
        container.setAttribute('aria-busy', 'false');

        if (!result.data || result.data.length === 0) {
            container.innerHTML = '<p class="col-span-full text-center text-gray-500 py-8">پشتیبانی ثبت نشده است</p>';
            return;
        }

        container.innerHTML = result.data.map(renderSupporterCard).join('');
        if (window.refreshLucideIcons) window.refreshLucideIcons(container);
    } catch (error) {
        console.error('Error loading supporters:', error);
        container.setAttribute('aria-busy', 'false');
        container.innerHTML = '<p class="col-span-full text-center text-gray-500 py-8">خطا در بارگذاری پشتیبانان</p>';
    }
}

onReady(() => {
    loadInstructors();
    loadSupporters();
});

// Set current Persian year in footer
const copyrightYear = document.getElementById('copyright-year');
if (copyrightYear) {
    const persianYear = new Date().toLocaleDateString('fa-IR', { year: 'numeric' });
    copyrightYear.textContent = persianYear.replace(/[۰-۹]/g, d => '۰۱۲۳۴۵۶۷۸۹'.indexOf(d));
}