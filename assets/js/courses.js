const { default: API, onReady } = await import(`${window.APP_CONFIG.assetUrl}/js/api.js`);

const DASHBOARD_URL = (window.APP_CONFIG && window.APP_CONFIG.dashboardUrl) || '';

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
function formatPrice(value) {
    if (value === null || value === undefined || value === '') return '';
    const persianDigits = '۰۱۲۳۴۵۶۷۸۹';
    let str = String(value);
    // تبدیل ارقام فارسی/عربی به انگلیسی
    str = str
        .replace(/[۰-۹]/g, d => persianDigits.indexOf(d))
        .replace(/[٠-٩]/g, d => '٠١٢٣٤٥٦٧٨٩'.indexOf(d));
    const digits = str.replace(/\D/g, '');
    if (!digits) return String(value); // مثل «تماس بگیرید» را دست نمی‌زنیم
    // جداکردن سه‌رقم سه‌رقم
    const grouped = digits.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    // تبدیل به ارقام فارسی + تومان
    return grouped.replace(/\d/g, d => persianDigits[+d]) + ' تومان';
}
function renderFeature(text) {
    return `<div class="flex items-center gap-2">
        ${svgIcon('icon-check', 'icon--sm text-[#445D84]')}
        <span>${text}</span>
    </div>`;
}

function renderCourseCard(course) {
    const featuresHtml = (course.features || []).map(renderFeature).join('');
    const featuresBlock = featuresHtml
        ? `<div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm font-medium text-gray-700 pt-2">${featuresHtml}</div>`
        : '';
    const priceBlock = course.price
    ? `<div class="text-2xl font-bold text-[#445D84]">${formatPrice(course.price)}</div>`
    : '';
    const badgeBlock = course.badge_label
        ? `<span class="bg-[#445D84]/10 text-[#445D84] text-xs font-bold px-3 py-1 rounded-full inline-block">${course.badge_label}</span>`
        : '';
    const bodyText = course.full_description || course.description || '';

    return `<article id="course-${course.id}"
        class="bg-white rounded-2xl shadow-xl border border-[#E2D9C6] overflow-hidden p-6 sm:p-8 flex flex-col md:flex-row gap-8 items-center">
        <div class="md:w-2/3 space-y-4">
            ${badgeBlock}
            <h2 class="text-2xl font-bold text-[#445D84]">${course.name}</h2>
            <p class="text-gray-600 leading-relaxed text-justify">${bodyText}</p>
            ${featuresBlock}
        </div>
        <div class="md:w-1/3 bg-[#f9f7f3] p-6 rounded-xl border border-[#E2D9C6] text-center w-full space-y-4">
            ${course.target_grades ? `<div class="text-[#445D84] font-bold text-lg">${course.target_grades}</div>` : ''}
            ${course.format ? `<p class="text-xs text-gray-500">${course.format}</p>` : ''}
            ${priceBlock}
            <a href="${DASHBOARD_URL}/" class="block bg-[#445D84] text-white py-3 rounded-xl font-bold hover:bg-[#344868] transition">مشاوره و ثبت‌نام</a>
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
            container.innerHTML = '<p class="text-center text-gray-500 py-8">در حال حاضر دوره‌ای ثبت نشده است</p>';
            return;
        }
        container.innerHTML = courses.map(renderCourseCard).join('');
        if (window.refreshLucideIcons) window.refreshLucideIcons(container);
        updateCourseSchema(courses);
    } catch (error) {
        console.error('Error loading courses:', error);
        container.innerHTML = '<p class="text-center text-gray-500 py-8">خطا در بارگذاری دوره‌ها</p>';
    }
}

onReady(() => {
    initMobileMenu();
    loadCourses();
});
