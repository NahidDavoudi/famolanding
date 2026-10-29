const { default: API, onReady } = await import(`${window.APP_CONFIG.assetUrl}/js/api.js`);

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

// Initialize Swiper instances (will be updated after data loads)
let instructorsSwiper = null;
let supportSwiper = null;

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

// Load courses from API
async function loadCoursesFromAPI() {
    showSkeletons('coursesContainer', 'skeleton-course-wrapper');

    try {
        const result = await API.get('/public/courses');

        const container = document.getElementById('coursesContainer');
        if (!container) return;

        hideSkeletons('coursesContainer', 'skeleton-course-wrapper');

        if (!result.data || result.data.length === 0) {
            container.innerHTML = '<p class="text-center text-gray-500 py-8 col-span-full">دوره‌ای یافت نشد</p>';
            return;
        }

        container.innerHTML = result.data.map(course => {
            const hasPrice = course.price !== null && course.price !== undefined && String(course.price).trim() !== '';
            const priceText = hasPrice ? formatPrice(course.price) : '';
            const priceUnit = hasPrice && isNumericPrice(course.price) ? 'تومان' : '';
            const cardGradient = `linear-gradient(135deg, ${course.gradient_color_from || '#445D84'}, ${course.gradient_color_to || '#6D8B9E'})`;
            const imageUrl = course.background_image_url
                ? `https://api.famoacademy.ir/uploads/${course.background_image_url}`
                : '';
            const badgeBlock = course.badge_label
                ? `<span class="course-badge">${course.badge_label}</span>`
                : '';
            const shortDescription = course.description || '';

            return `
            <a class="course-card group" href="pages/course.php#course-${course.id}"
               aria-label="${course.name} - مشاهده جزئیات دوره">
                <!-- تصویر دوره (بدون overlay) -->
                <div class="course-image-wrapper" style="background: ${cardGradient};">
                    ${imageUrl
                        ? `<img src="${imageUrl}" alt="${course.name}" loading="lazy" class="course-image">`
                        : `<div class="course-image-fallback">
                                ${svgIcon(faToSvg(course.icon), 'icon--3xl text-white/90')}
                           </div>`}
                    ${badgeBlock}
                </div>

                <!-- نام دوره -->
                <div class="course-body">
                    <h3 class="course-name">${course.name}</h3>

                    <!-- توضیح کوتاه دوره -->
                    ${shortDescription ? `<p class="course-desc">${shortDescription}</p>` : ''}

                    <!-- قیمت دوره (کاملاً زیر تصویر و نام، بدون کراپ شدن) -->
                    <div class="course-footer">
                        <span class="course-price-label">هزینه دوره</span>
                        ${hasPrice
                            ? `<span class="course-price-value">${priceText}${priceUnit ? `<span class="course-price-unit">${priceUnit}</span>` : ''}</span>`
                            : `<span class="course-price-value course-price-value--text">تماس بگیرید</span>`}
                    </div>
                </div>
            </a>
        `;
        }).join('');
        if (window.refreshLucideIcons) window.refreshLucideIcons(container);
    } catch (error) {
        console.error('Error loading courses:', error);
        const container = document.getElementById('coursesContainer');
        if (container) {
            hideSkeletons('coursesContainer', 'skeleton-course-wrapper');
            container.innerHTML = '<p class="text-center text-gray-500 py-8 col-span-full">خطا در بارگذاری دوره‌ها</p>';
        }
    }
}

// Load instructors from API
async function loadInstructorsFromAPI() {
    showSkeletons('instructorsContainer', 'skeleton-instructor-wrapper');

    try {
        const result = await API.get('/public/instructors');

        const container = document.getElementById('instructorsContainer');
        if (!container) return;

        hideSkeletons('instructorsContainer', 'skeleton-instructor-wrapper');

        if (!result.data || result.data.length === 0) {
            container.innerHTML = '<div class="swiper-slide"><p class="text-center text-gray-500 py-8">استادی یافت نشد</p></div>';
            return;
        }

        // Gradient colors for instructors (cycling)
        const gradients = [
            ['#445D84', '#5a779e'],
            ['#F2E8C9', '#B3A8A1'],
            ['#6D8B9E', '#8aa5b8'],
            ['#445D84', '#6D8B9E']
        ];

        container.innerHTML = result.data.map((instructor, index) => {
            const gradient = gradients[index % gradients.length];
            const initialLetter = instructor.initial_letter || instructor.name.charAt(0);
            const description = instructor.description || '';

            return `
        <div class="swiper-slide">
            <div class="instructor-card bg-gradient-to-b from-white to-[#f9f7f3] rounded-2xl overflow-hidden shadow-xl border border-[#E2D9C6] h-full hover-lift transition-all duration-300 cursor-pointer" 
                 data-name="${instructor.name}" 
                 data-title="${instructor.title}" 
                 data-details="${description}">
                <div class="h-40 flex items-center justify-center relative overflow-hidden" 
                     style="background: linear-gradient(135deg, ${gradient[0]}, ${gradient[1]});">
                    <div class="absolute inset-0 animate-shimmer"></div>
                    <div class="w-24 h-24 rounded-full bg-white border-4 border-[#E2D9C6] overflow-hidden shadow-lg relative z-10">
                        ${instructor.image_url ? `
                            <img src="${instructor.image_url}" alt="${instructor.name}" class="w-full h-full object-cover">
                        ` : `
                            <div class="w-full h-full bg-gradient-to-br from-[#E2D9C6] to-[#d4c9b2] flex items-center justify-center text-3xl font-bold" 
                                 style="color: ${gradient[0]};">
                                ${initialLetter}
                            </div>
                        `}
                    </div>
                </div>
                <div class="p-4 text-center">
                    <h3 class="text-xl font-bold text-[#445D84] mb-2">${instructor.name}</h3>
                    <p class="text-[#8B786D] font-medium mb-3">${instructor.title}</p>
                </div>
            </div>
        </div>
    `;
        }).join('');

        // Initialize or update Swiper
        if (!instructorsSwiper) {
            instructorsSwiper = new Swiper('.instructorsSwiper', {
                slidesPerView: 1,
                spaceBetween: 20,
                loop: true,
                grabCursor: true,
                pagination: {
                    el: '.instructorsSwiper .swiper-pagination',
                    clickable: true,
                    dynamicBullets: true,

                },
                navigation: {
                    nextEl: '.instructorsSwiper .swiper-button-next',
                    prevEl: '.instructorsSwiper .swiper-button-prev',
                },
                breakpoints: {
                    640: {
                        slidesPerView: 2,
                    },
                    1024: {
                        slidesPerView: 3,
                    },
                },
            });
        } else {
            instructorsSwiper.update();
        }

        // Re-attach modal event listeners
        attachInstructorModalListeners();
    } catch (error) {
        console.error('Error loading instructors:', error);
        const container = document.getElementById('instructorsContainer');
        if (container) {
            hideSkeletons('instructorsContainer', 'skeleton-instructor-wrapper');
            container.innerHTML = '<div class="swiper-slide"><p class="text-center text-gray-500 py-8">خطا در بارگذاری اساتید</p></div>';
        }
    }
}

// Load supporters from API
async function loadSupportersFromAPI() {
    showSkeletons('supportersContainer', 'skeleton-supporter-wrapper');

    try {
        const result = await API.get('/public/supporters');

        const container = document.getElementById('supportersContainer');
        if (!container) return;

        hideSkeletons('supportersContainer', 'skeleton-supporter-wrapper');

        if (!result.data || result.data.length === 0) {
            container.innerHTML = '<div class="swiper-slide"><p class="text-center text-gray-500 py-8">پشتیبانی یافت نشد</p></div>';
            return;
        }

        // Gradient colors for supporters (cycling)
        const gradients = [
            ['#445D84', '#5a779e'],
            ['#8B786D', '#a3958a'],
            ['#6D8B9E', '#8aa5b8'],
            ['#445D84', '#6D8B9E']
        ];

        container.innerHTML = result.data.map((supporter, index) => {
            const gradient = gradients[index % gradients.length];
            const initialLetter = supporter.name.charAt(0);

            return `
                <div class="swiper-slide">
                    <div class="bg-white rounded-2xl overflow-hidden shadow-xl border border-[#E2D9C6] h-full hover-lift transition-all duration-500">
                        <div class="h-40 flex items-center justify-center relative overflow-hidden" 
                             style="background: linear-gradient(135deg, ${gradient[0]}, ${gradient[1]});">
                            <div class="absolute inset-0 animate-shimmer"></div>
                            <div class="w-24 h-24 rounded-full bg-white border-4 border-[#E2D9C6] overflow-hidden shadow-lg relative z-10">
                                <div class="w-full h-full bg-gradient-to-br from-[#E2D9C6] to-[#d4c9b2] flex items-center justify-center text-3xl font-bold" 
                                     style="color: ${gradient[0]};">
                                    ${initialLetter}
                                </div>
                            </div>
                        </div>
                        <div class="p-6 text-center">
                            <h3 class="text-lg font-bold text-[#445D84] mb-2">${supporter.name}</h3>
                            <p class="text-[#8B786D] font-medium mb-3 text-sm">پشتیبان ${supporter.field ? `رشته ${supporter.field}` : `پایه ${supporter.grade}`}</p>
                            <div class="flex justify-center space-x-2 space-x-reverse">
                                <span class="bg-[#E2D9C6] text-[#445D84] px-3 py-1 rounded-full text-xs font-bold">پشتیبانی ۲۴/۷</span>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }).join('');

        // Initialize or update Swiper
        if (!supportSwiper) {
            supportSwiper = new Swiper('.supportSwiper', {
                slidesPerView: 1,
                spaceBetween: 20,
                loop: true,
                grabCursor: true,
                pagination: {
                    el: '.supportSwiper .swiper-pagination',
                    clickable: true,
                    dynamicBullets: true,
                },
                navigation: {
                    nextEl: '.supportSwiper .swiper-button-next',
                    prevEl: '.supportSwiper .swiper-button-prev',
                },
                breakpoints: {
                    640: {
                        slidesPerView: 2,
                    },
                    1024: {
                        slidesPerView: 3,
                    },
                },
            });
        } else {
            supportSwiper.update();
        }
    } catch (error) {
        console.error('Error loading supporters:', error);
        const container = document.getElementById('supportersContainer');
        if (container) {
            hideSkeletons('supportersContainer', 'skeleton-supporter-wrapper');
            container.innerHTML = '<div class="swiper-slide"><p class="text-center text-gray-500 py-8">خطا در بارگذاری پشتیبانان</p></div>';
        }
    }
}

// Load all data on page load
onReady(() => {
    loadCoursesFromAPI();
    loadInstructorsFromAPI();
    loadSupportersFromAPI();
    // Re-initialize FAQ after content loads
    setTimeout(() => {
    }, 500);
});

// Stats Counter Animation with GSAP
const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
function animateCounter(elementId, finalValue) {
    const element = document.getElementById(elementId);
    if (!element) return;
    if (prefersReducedMotion || typeof gsap === 'undefined') {
        element.textContent = finalValue;
        return;
    }

    const obj = { value: 0 };
    gsap.to(obj, {
        value: finalValue,
        duration: 2,
        ease: "power2.out",
        onUpdate: () => {
            element.textContent = Math.floor(obj.value);
        }
    });
}

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
                    animateCounter('students-counter', 200);
                    animateCounter('teachers-counter', 8);
                }, 500);

                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.5 });

    observer.observe(statsSection);
}

// FAQ Accordion with GSAP
const FAQ = {
    init() {
        const items = document.querySelectorAll('.faq-item');
        if (items.length === 0) return;

        items.forEach(item => {
            const newItem = item.cloneNode(true);
            item.parentNode.replaceChild(newItem, item);

            const question = newItem.querySelector('.faq-question');
            const answer = newItem.querySelector('.faq-answer');

            if (question && answer) {
                if (State.isGSAPReady) {
                    gsap.set(answer, { maxHeight: 0 });
                }

                question.addEventListener('click', () => {
                    this.toggle(newItem, answer);
                });
                question.addEventListener('keydown', (e) => {
                    if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        this.toggle(newItem, answer);
                    }
                });
            }
        });
    },

    toggle(item, answer) {
        const isActive = item.classList.contains('active');
        const icon = item.querySelector('.faq-icon');

        // Close all other items first
        document.querySelectorAll('.faq-item.active').forEach(otherItem => {
            if (otherItem !== item) {
                this.close(otherItem);
            }
        });

        // Toggle current item
        isActive ? this.close(item) : this.open(item, answer, icon);
    },

    open(item, answer, icon) {
        item.classList.add('active');
        const q = item.querySelector('.faq-question');
        if (q) q.setAttribute('aria-expanded', 'true');

        if (State.isGSAPReady) {
            gsap.to(answer, {
                maxHeight: answer.scrollHeight + 'px',
                duration: CONFIG.animations.durations.medium,
                ease: CONFIG.animations.easings.smooth,
                onComplete: () => { answer.style.maxHeight = 'none'; }
            });

            if (icon) {
                gsap.to(icon, {
                    rotation: 180,
                    duration: CONFIG.animations.durations.fast,
                    ease: CONFIG.animations.easings.smooth
                });
            }

            gsap.to(item, {
                backgroundColor: 'rgba(68, 93, 132, 0.05)',
                duration: CONFIG.animations.durations.fast
            });
        } else {
            answer.style.maxHeight = answer.scrollHeight + 'px';
            if (icon) icon.style.transform = 'rotate(180deg)';
        }
    },

    close(item) {
        const answer = item.querySelector('.faq-answer');
        const icon = item.querySelector('.faq-icon');

        item.classList.remove('active');
        const q = item.querySelector('.faq-question');
        if (q) q.setAttribute('aria-expanded', 'false');

        if (State.isGSAPReady) {
            const currentHeight = answer.scrollHeight;
            answer.style.maxHeight = currentHeight + 'px';

            gsap.to(answer, {
                maxHeight: 0,
                duration: CONFIG.animations.durations.fast,
                ease: CONFIG.animations.easings.smoothIn
            });

            if (icon) {
                gsap.to(icon, {
                    rotation: 0,
                    duration: CONFIG.animations.durations.fast
                });
            }

            gsap.to(item, {
                backgroundColor: 'transparent',
                duration: CONFIG.animations.durations.fast
            });
        } else {
            answer.style.maxHeight = '0';
            if (icon) icon.style.transform = 'rotate(0deg)';
        }
    }
};
// Initialize FAQ when DOM is ready
onReady(() => {
    setTimeout(initFAQ, 100);
});

// Function to attach instructor modal listeners
function attachInstructorModalListeners() {
    const instructorModal = document.getElementById('instructorModal');
    const closeModalBtn = document.getElementById('closeModal');
    const modalContent = document.getElementById('modalContent');
    const instructorCards = document.querySelectorAll('.instructor-card');

    if (!instructorModal || !modalContent) return;

    instructorCards.forEach(card => {
        // Remove existing listeners to avoid duplicates
        const newCard = card.cloneNode(true);
        card.parentNode.replaceChild(newCard, card);

        newCard.addEventListener('click', () => {
            const name = newCard.getAttribute('data-name');
            const title = newCard.getAttribute('data-title');
            const details = newCard.getAttribute('data-details');

            // Extract first letter for avatar
            const firstLetter = name.charAt(0);

            modalContent.innerHTML = `
                <div class="flex flex-col items-center text-center">
                    <div class="w-40 h-40 rounded-full bg-gradient-to-br from-[#E2D9C6] to-[#d4c9b2] flex items-center justify-center text-6xl text-[#445D84] font-bold mb-6 shadow-lg">
                        ${firstLetter}
                    </div>
                    <h3 class="text-2xl font-bold text-[#445D84] mb-2">${name}</h3>
                    <p class="text-lg text-[#8B786D] font-medium mb-6">${title}</p>
                    <div class="bg-gradient-to-r from-[#f9f7f3] to-white p-6 rounded-xl border border-[#E2D9C6]">
                        <p class="text-gray-600 leading-relaxed text-justify">${details || 'اطلاعات بیشتری در دسترس نیست.'}</p>
                    </div>
                    <div class="mt-8 flex space-x-4 space-x-reverse">
                        <button id="closeModal2" class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg font-medium hover:bg-gray-300 transition duration-300">
                            بستن
                        </button>
                    </div>
                </div>
            `;

            if (instructorModal) {
                instructorModal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';

                // Add event listener to close button inside modal
                const closeModal2 = document.getElementById('closeModal2');
                if (closeModal2) {
                    closeModal2.addEventListener('click', () => {
                        instructorModal.classList.add('hidden');
                        document.body.style.overflow = 'auto';
                    });
                }
            }
        });
    });

    // Close modal handlers
    if (closeModalBtn && instructorModal) {
        closeModalBtn.addEventListener('click', () => {
            instructorModal.classList.add('hidden');
            document.body.style.overflow = 'auto';
        });
    }

    if (instructorModal) {
        instructorModal.addEventListener('click', (e) => {
            if (e.target === instructorModal) {
                instructorModal.classList.add('hidden');
                document.body.style.overflow = 'auto';
            }
        });
    }

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && instructorModal && !instructorModal.classList.contains('hidden')) {
            instructorModal.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }
    });
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
        const instructorCardsAnimate = document.querySelectorAll('.instructor-card');
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

        // Animate support team cards
        const supportCards = document.querySelectorAll('.supportSwiper .swiper-slide');
        supportCards.forEach((card, index) => {
            gsap.from(card, {
                opacity: 0,
                y: 30,
                duration: 0.5,
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

