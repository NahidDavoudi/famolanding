const { default: API, onReady } = await import(`${window.APP_CONFIG.assetUrl}/js/api.js`);

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
        const icon = SOCIAL_ICON_MAP[link.platform] || 'icon-external-link';
        return `
            <a href="${link.url}" target="_blank" rel="noopener"
                class="bg-[#445D84]/10 w-9 h-9 rounded-full flex items-center justify-center hover:bg-[#445D84] hover:text-white text-[#445D84] transition"
                aria-label="${link.platform}">
                ${svgIcon(icon, 'icon--sm')}
            </a>
        `;
    }).join('');
    return `<div class="flex justify-center gap-3 pt-2">${items}</div>`;
}

function renderInstructorCard(instructor) {
    const initial = instructor.initial_letter || instructor.name.charAt(0);
    const avatar = instructor.image_url
        ? `<img src="${instructor.image_url}" alt="${instructor.name}" class="w-24 h-24 rounded-full object-cover mx-auto shadow-md">`
        : `<div class="w-24 h-24 bg-[#445D84] text-white text-3xl font-bold rounded-full flex items-center justify-center mx-auto shadow-md">${initial}</div>`;

    const bio = instructor.full_bio || instructor.description || '';

    return `
        <article class="bg-white rounded-2xl p-6 shadow-lg border border-[#E2D9C6] text-center space-y-4">
            ${avatar}
            <div>
                <h3 class="text-xl font-bold text-[#445D84]">${instructor.name}</h3>
                <p class="text-sm text-[#8B786D] font-medium mt-1">${instructor.title}</p>
            </div>
            <p class="text-gray-600 text-sm leading-relaxed text-justify">${bio}</p>
            ${renderSocialLinks(instructor.social_links)}
        </article>
    `;
}

function renderSupporterCard(supporter) {
    const initial = supporter.name.charAt(0);
    const fieldLabel = supporter.field ? `رشته ${supporter.field}` : `پایه ${supporter.grade}`;

    const avatar = supporter.avatar_url
        ? `<img src="${supporter.avatar_url}" alt="${supporter.name}" class="w-full h-full object-cover">`
        : `<div class="w-full h-full bg-[#E2D9C6] text-[#445D84] text-2xl font-bold flex items-center justify-center">${initial}</div>`;

    return `
        <article class="bg-[#f9f7f3] rounded-2xl p-6 border border-[#E2D9C6] flex items-center gap-4">
            <div class="w-16 h-16 rounded-full overflow-hidden shadow-md shrink-0">
                ${avatar}
            </div>
            <div>
                <h3 class="font-bold text-[#445D84] text-lg">${supporter.name}</h3>
                <p class="text-xs text-gray-600">پشتیبان تخصصی ${fieldLabel}</p>
                <span class="inline-block mt-2 text-xs bg-white px-2 py-1 rounded text-[#445D84] font-bold">رفع
                    اشکال و نظارت مستمر</span>
            </div>
        </article>
    `;
}

async function loadInstructors() {
    const container = document.getElementById('instructorsContainer');
    if (!container) return;

    try {
        const result = await API.get('/public/instructors');

        if (!result.data || result.data.length === 0) {
            container.innerHTML = '<p class="col-span-full text-center text-gray-500 py-8">استادی ثبت نشده است</p>';
            return;
        }

        container.innerHTML = result.data.map(renderInstructorCard).join('');
        if (window.refreshLucideIcons) window.refreshLucideIcons(container);
    } catch (error) {
        console.error('Error loading instructors:', error);
        container.innerHTML = '<p class="col-span-full text-center text-gray-500 py-8">خطا در بارگذاری اساتید</p>';
    }
}

async function loadSupporters() {
    const container = document.getElementById('supportersContainer');
    if (!container) return;

    try {
        const result = await API.get('/public/supporters');

        if (!result.data || result.data.length === 0) {
            container.innerHTML = '<p class="col-span-full text-center text-gray-500 py-8">پشتیبانی ثبت نشده است</p>';
            return;
        }

        container.innerHTML = result.data.map(renderSupporterCard).join('');
        if (window.refreshLucideIcons) window.refreshLucideIcons(container);
    } catch (error) {
        console.error('Error loading supporters:', error);
        container.innerHTML = '<p class="col-span-full text-center text-gray-500 py-8">خطا در بارگذاری پشتیبانان</p>';
    }
}

onReady(() => {
    loadInstructors();
    loadSupporters();
});