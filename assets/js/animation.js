/**
 * Advanced Animations for Famo Academy Website
 * با GSAP و CSS Custom Properties
 */

// ==================== GLOBAL ANIMATION SETTINGS ====================
const AnimationConfig = {
    // Durations
    durations: {
        fast: 0.3,
        medium: 0.5,
        slow: 0.8,
        verySlow: 1.2
    },

    // Easing functions
    easings: {
        smooth: "power2.out",
        bouncy: "elastic.out(1, 0.8)",
        back: "back.out(1.7)",
        smoothInOut: "power2.inOut"
    },

    // Stagger delays
    staggers: {
        small: 0.05,
        medium: 0.1,
        large: 0.15
    }
};

// ==================== PAGE TRANSITIONS ====================
class PageTransitions {
    static init() {
        // Add transition class to all internal links
        document.querySelectorAll('a[href^="/"], a[href^="#"]').forEach(link => {
            if (link.href && !link.href.includes('#')) {
                link.addEventListener('click', (e) => {
                    if (!link.target && link.href !== window.location.href) {
                        e.preventDefault();
                        this.fadeOut().then(() => {
                            window.location.href = link.href;
                        });
                    }
                });
            }
        });
    }

    static fadeOut() {
        return new Promise((resolve) => {
            const overlay = document.createElement('div');
            overlay.className = 'page-transition-overlay';
            document.body.appendChild(overlay);

            gsap.to(overlay, {
                opacity: 1,
                duration: AnimationConfig.durations.medium,
                ease: AnimationConfig.easings.smooth,
                onComplete: resolve
            });
        });
    }

    static fadeIn() {
        const overlay = document.querySelector('.page-transition-overlay');
        if (overlay) {
            gsap.to(overlay, {
                opacity: 0,
                duration: AnimationConfig.durations.medium,
                ease: AnimationConfig.easings.smooth,
                onComplete: () => overlay.remove()
            });
        }
    }
}

// ==================== SCROLL ANIMATIONS ====================
class ScrollAnimations {
    static init() {
        // Initialize ScrollTrigger
        if (typeof gsap !== 'undefined' && typeof ScrollTrigger !== 'undefined' && typeof ScrollToPlugin !== 'undefined') {
            gsap.registerPlugin(ScrollTrigger, ScrollToPlugin);
            this.setupScrollAnimations();
        }

        // Smooth scroll only on pages without main.js (main.js owns it on the home page
        // because it also closes the mobile menu after clicking an anchor).
        if (!document.querySelector('script[src*="main.js"]')) {
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', (e) => {
                    const targetId = anchor.getAttribute('href');
                    if (targetId === '#') return;

                    const target = document.querySelector(targetId);
                    if (target) {
                        e.preventDefault();
                        this.smoothScrollTo(target);
                    }
                });
            });
        }
    }

    static smoothScrollTo(element) {
        if (typeof ScrollToPlugin !== 'undefined') {
            gsap.to(window, {
                duration: AnimationConfig.durations.slow,
                scrollTo: {
                    y: element,
                    offsetY: 80
                },
                ease: AnimationConfig.easings.smoothInOut
            });
        } else {
            // Fallback: native smooth scroll
            element.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }

    static setupScrollAnimations() {
        // Animate sections on scroll
        gsap.utils.toArray('.animate-on-scroll').forEach(section => {
            gsap.from(section, {
                opacity: 0,
                y: 50,
                duration: AnimationConfig.durations.slow,
                ease: AnimationConfig.easings.smooth,
                scrollTrigger: {
                    trigger: section,
                    start: "top 80%",
                    end: "bottom 20%",
                    toggleActions: "play none none reverse"
                }
            });
        });

        // Parallax effect for hero sections
        gsap.utils.toArray('.parallax-bg').forEach(bg => {
            gsap.to(bg, {
                yPercent: 20,
                ease: "none",
                scrollTrigger: {
                    trigger: bg.parentElement,
                    start: "top bottom",
                    end: "bottom top",
                    scrub: true
                }
            });
        });

        // Stagger animations for lists
        gsap.utils.toArray('.stagger-animate').forEach(container => {
            const items = container.querySelectorAll('.animate-item');
            if (items.length > 0) {
                gsap.from(items, {
                    opacity: 0,
                    y: 30,
                    stagger: AnimationConfig.staggers.medium,
                    duration: AnimationConfig.durations.medium,
                    ease: AnimationConfig.easings.smooth,
                    scrollTrigger: {
                        trigger: container,
                        start: "top 75%"
                    }
                });
            }
        });
    }
}

// ==================== COUNTER ANIMATIONS ====================
class CounterAnimations {
    static init() {
        // Initialize counters when they come into view
        const counters = document.querySelectorAll('[data-counter]');

        counters.forEach(counter => {
            ScrollTrigger.create({
                trigger: counter,
                start: "top 80%",
                onEnter: () => this.animateCounter(counter)
            });
        });
    }

    static animateCounter(element) {
        const finalValue = parseInt(element.getAttribute('data-counter')) ||
            parseInt(element.textContent.replace(/,/g, '')) || 0;
        const prefix = element.getAttribute('data-prefix') || '';
        const suffix = element.getAttribute('data-suffix') || '';
        const duration = parseFloat(element.getAttribute('data-duration')) || 2;

        const obj = { value: 0 };
        gsap.to(obj, {
            value: finalValue,
            duration: duration,
            ease: AnimationConfig.easings.smooth,
            onUpdate: () => {
                const value = Math.floor(obj.value);
                const formattedValue = value.toLocaleString('fa-IR');
                element.textContent = `${prefix}${formattedValue}${suffix}`;
            },
            onComplete: () => {
                element.classList.add('counter-completed');
            }
        });
    }
}

// ==================== FAQ ANIMATIONS ====================
class FAQAnimations {
    static init() {
        const faqItems = document.querySelectorAll('.faq-item');

        if (faqItems.length === 0) return;

        // Add initial animations
        gsap.from(faqItems, {
            opacity: 0,
            y: 20,
            stagger: AnimationConfig.staggers.small,
            duration: AnimationConfig.durations.medium,
            ease: AnimationConfig.easings.smooth,
            scrollTrigger: {
                trigger: '#faq-contact',
                start: "top 75%"
            }
        });

        // Setup click handlers
        faqItems.forEach(item => {
            const question = item.querySelector('.faq-question');
            const answer = item.querySelector('.faq-answer');
            const icon = item.querySelector('.faq-icon');

            if (question && answer) {
                // Reset initial state
                gsap.set(answer, { maxHeight: 0 });

                question.addEventListener('click', () => {
                    this.toggleFAQ(item, answer, icon);
                });
            }
        });
    }

    static toggleFAQ(item, answer, icon) {
        const isActive = item.classList.contains('active');

        // First close all other items
        document.querySelectorAll('.faq-item.active').forEach(otherItem => {
            if (otherItem !== item) {
                this.closeFAQ(otherItem);
            }
        });

        // Toggle current item
        if (isActive) {
            this.closeFAQ(item);
        } else {
            this.openFAQ(item, answer, icon);
        }
    }

    static openFAQ(item, answer, icon) {
        item.classList.add('active');

        // Animate answer
        gsap.to(answer, {
            maxHeight: answer.scrollHeight + "px",
            duration: AnimationConfig.durations.medium,
            ease: AnimationConfig.easings.smooth,
            onComplete: () => {
                // Allow natural height after animation
                answer.style.maxHeight = 'none';
            }
        });

        // Animate icon
        if (icon) {
            gsap.to(icon, {
                rotation: 180,
                duration: AnimationConfig.durations.fast,
                ease: AnimationConfig.easings.smooth
            });
        }

        // Add background color animation
        gsap.to(item, {
            backgroundColor: "rgba(68, 93, 132, 0.05)",
            duration: AnimationConfig.durations.fast,
            ease: AnimationConfig.easings.smooth
        });
    }

    static closeFAQ(item) {
        const answer = item.querySelector('.faq-answer');
        const icon = item.querySelector('.faq-icon');

        item.classList.remove('active');

        // Store current height before animating
        const currentHeight = answer.scrollHeight;
        answer.style.maxHeight = currentHeight + "px";

        // Animate answer
        gsap.to(answer, {
            maxHeight: 0,
            duration: AnimationConfig.durations.fast,
            ease: AnimationConfig.easings.smooth
        });

        // Animate icon
        if (icon) {
            gsap.to(icon, {
                rotation: 0,
                duration: AnimationConfig.durations.fast,
                ease: AnimationConfig.easings.smooth
            });
        }

        // Reset background color
        gsap.to(item, {
            backgroundColor: "transparent",
            duration: AnimationConfig.durations.fast,
            ease: AnimationConfig.easings.smooth
        });
    }
}

// ==================== CARD HOVER EFFECTS ====================
class CardAnimations {
    static init() {
        const cards = document.querySelectorAll('.hover-card');

        cards.forEach(card => {
            // Initial state
            gsap.set(card, {
                transformPerspective: 1000
            });

            // Mouse enter animation
            card.addEventListener('mouseenter', (e) => {
                this.animateCardEnter(card, e);
            });

            // Mouse leave animation
            card.addEventListener('mouseleave', (e) => {
                this.animateCardLeave(card, e);
            });

            // Mouse move parallax
            card.addEventListener('mousemove', (e) => {
                this.parallaxCard(card, e);
            });
        });
    }

    static animateCardEnter(card, e) {
        // Stop any ongoing animations
        gsap.killTweensOf(card);

        // Scale up with bounce
        gsap.to(card, {
            scale: 1.05,
            duration: AnimationConfig.durations.medium,
            ease: AnimationConfig.easings.back,
            yoyo: true,
            yoyoEase: true
        });

        // Elevation effect
        gsap.to(card, {
            y: -10,
            duration: AnimationConfig.durations.medium,
            ease: AnimationConfig.easings.smooth,
            boxShadow: "0 20px 40px rgba(0,0,0,0.1)"
        });

        // Content fade in
        const content = card.querySelector('.card-content');
        if (content) {
            gsap.to(content, {
                y: -5,
                opacity: 1,
                duration: AnimationConfig.durations.fast,
                ease: AnimationConfig.easings.smooth
            });
        }
    }

    static animateCardLeave(card, e) {
        // Stop any ongoing animations
        gsap.killTweensOf(card);

        // Scale back
        gsap.to(card, {
            scale: 1,
            duration: AnimationConfig.durations.medium,
            ease: AnimationConfig.easings.smooth
        });

        // Reset elevation
        gsap.to(card, {
            y: 0,
            duration: AnimationConfig.durations.medium,
            ease: AnimationConfig.easings.smooth,
            boxShadow: "0 4px 6px rgba(0,0,0,0.05)"
        });

        // Reset rotation
        gsap.to(card, {
            rotationX: 0,
            rotationY: 0,
            duration: AnimationConfig.durations.medium,
            ease: AnimationConfig.easings.smooth
        });
    }

    static parallaxCard(card, e) {
        const rect = card.getBoundingClientRect();
        const x = e.clientX - rect.left;
        const y = e.clientY - rect.top;

        const centerX = rect.width / 2;
        const centerY = rect.height / 2;

        const rotateX = (y - centerY) / 20;
        const rotateY = (centerX - x) / 20;

        gsap.to(card, {
            rotationX: rotateX,
            rotationY: rotateY,
            duration: 0.5,
            ease: AnimationConfig.easings.smooth
        });
    }
}

// ==================== BUTTON ANIMATIONS ====================
class ButtonAnimations {
    static init() {
        const buttons = document.querySelectorAll('.animated-button');

        buttons.forEach(button => {
            // Ripple effect
            button.addEventListener('click', (e) => {
                this.createRipple(button, e);
            });

            // Hover effect
            button.addEventListener('mouseenter', () => {
                this.animateButtonEnter(button);
            });

            button.addEventListener('mouseleave', () => {
                this.animateButtonLeave(button);
            });
        });
    }

    static createRipple(button, e) {
        const ripple = document.createElement('span');
        const rect = button.getBoundingClientRect();
        const size = Math.max(rect.width, rect.height);
        const x = e.clientX - rect.left - size / 2;
        const y = e.clientY - rect.top - size / 2;

        ripple.style.cssText = `
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.7);
            transform: scale(0);
            animation: ripple-animation 0.6s linear;
            width: ${size}px;
            height: ${size}px;
            top: ${y}px;
            left: ${x}px;
            pointer-events: none;
        `;

        button.appendChild(ripple);

        // Remove ripple after animation
        setTimeout(() => {
            ripple.remove();
        }, 600);
    }

    static animateButtonEnter(button) {
        gsap.to(button, {
            scale: 1.05,
            duration: AnimationConfig.durations.fast,
            ease: AnimationConfig.easings.smooth
        });

        // Shine effect
        const shine = document.createElement('div');
        shine.className = 'button-shine';
        button.appendChild(shine);

        gsap.to(shine, {
            x: '100%',
            duration: 0.6,
            ease: "power2.out",
            onComplete: () => shine.remove()
        });
    }

    static animateButtonLeave(button) {
        gsap.to(button, {
            scale: 1,
            duration: AnimationConfig.durations.fast,
            ease: AnimationConfig.easings.smooth
        });
    }
}

// ==================== MENU ANIMATIONS ====================
class MenuAnimations {
    static init() {
        const menuToggle = document.querySelector('.menu-toggle');
        const navMenu = document.querySelector('.nav-menu');

        if (menuToggle && navMenu) {
            menuToggle.addEventListener('click', () => {
                this.toggleMenu(navMenu, menuToggle);
            });
        }
    }

    static toggleMenu(navMenu, menuToggle) {
        const isOpen = navMenu.classList.contains('open');

        if (isOpen) {
            this.closeMenu(navMenu, menuToggle);
        } else {
            this.openMenu(navMenu, menuToggle);
        }
    }

    static openMenu(navMenu, menuToggle) {
        navMenu.classList.add('open');
        menuToggle.classList.add('open');

        // Animate menu items
        const items = navMenu.querySelectorAll('.nav-item');
        gsap.from(items, {
            opacity: 0,
            x: -20,
            stagger: AnimationConfig.staggers.small,
            duration: AnimationConfig.durations.fast,
            ease: AnimationConfig.easings.smooth
        });

        // Animate menu background
        gsap.to(navMenu, {
            opacity: 1,
            y: 0,
            duration: AnimationConfig.durations.medium,
            ease: AnimationConfig.easings.smooth
        });
    }

    static closeMenu(navMenu, menuToggle) {
        navMenu.classList.remove('open');
        menuToggle.classList.remove('open');

        gsap.to(navMenu, {
            opacity: 0,
            y: -20,
            duration: AnimationConfig.durations.fast,
            ease: AnimationConfig.easings.smooth
        });
    }
}

// ==================== LOADING ANIMATIONS ====================
class LoadingAnimations {
    static init() {
        // Initial page load animation
        window.addEventListener('load', () => {
            this.hideLoader();
            this.animatePageContent();
        });
    }

    static hideLoader() {
        const loader = document.querySelector('.page-loader');
        if (loader) {
            gsap.to(loader, {
                opacity: 0,
                duration: AnimationConfig.durations.medium,
                ease: AnimationConfig.easings.smooth,
                onComplete: () => {
                    loader.style.display = 'none';
                }
            });
        }
    }

    static animatePageContent() {
        // Animate hero section
        const heroElements = document.querySelectorAll('.hero-animate');
        if (heroElements.length > 0) {
            gsap.from(heroElements, {
                opacity: 0,
                y: 30,
                stagger: AnimationConfig.staggers.medium,
                duration: AnimationConfig.durations.slow,
                ease: AnimationConfig.easings.smooth,
                delay: 0.3
            });
        }
    }
}

// ==================== INITIALIZE ALL ANIMATIONS ====================
class AnimationManager {
    static init() {
        // Check if GSAP is loaded
        if (typeof gsap === 'undefined') {
            console.warn('GSAP not loaded. Animations disabled.');
            return;
        }

        // Initialize all animation modules
        this.initModules();

        // Add CSS for animations
        this.addAnimationStyles();

        // Handle page transitions
        PageTransitions.init();

        // Initial animations on page load
        setTimeout(() => {
            LoadingAnimations.init();
        }, 100);
    }

    static initModules() {
        ScrollAnimations.init();
        CounterAnimations.init();
        FAQAnimations.init();
        CardAnimations.init();
        ButtonAnimations.init();
        MenuAnimations.init();
    }

    static addAnimationStyles() {
        const style = document.createElement('style');
        style.textContent = `
            /* Page transition overlay */
            .page-transition-overlay {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: #445D84;
                z-index: 9999;
                opacity: 0;
                pointer-events: none;
            }
            
            /* Ripple animation */
            @keyframes ripple-animation {
                to {
                    transform: scale(4);
                    opacity: 0;
                }
            }
            
            /* Button shine effect */
            .button-shine {
                position: absolute;
                top: 0;
                left: -100%;
                width: 100%;
                height: 100%;
                background: linear-gradient(
                    90deg,
                    transparent,
                    rgba(255, 255, 255, 0.3),
                    transparent
                );
                pointer-events: none;
            }
            
            /* Animation classes */
            .animate-on-scroll {
                opacity: 0;
                transform: translateY(20px);
                transition: opacity 0.6s ease, transform 0.6s ease;
            }
            
            .animate-on-scroll.animated {
                opacity: 1;
                transform: translateY(0);
            }
            
            /* Card hover effects */
            .hover-card {
                transition: transform 0.3s ease, box-shadow 0.3s ease;
                will-change: transform;
            }
            
            .hover-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 10px 20px rgba(0,0,0,0.1);
            }
            
            /* Smooth transitions */
            .smooth-transition {
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }
            
            /* Loading animation */
            .page-loader {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: white;
                display: flex;
                align-items: center;
                justify-content: center;
                z-index: 9999;
            }
            
            .loader-spinner {
                width: 50px;
                height: 50px;
                border: 3px solid #f3f3f3;
                border-top: 3px solid #445D84;
                border-radius: 50%;
                animation: spin 1s linear infinite;
            }
            
            @keyframes spin {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }
            
            /* Counter completed state */
            .counter-completed {
                color: #445D84;
                font-weight: bold;
            }
        `;
        document.head.appendChild(style);
    }
}

// ==================== EXPORT AND INITIALIZE ====================
// Initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        AnimationManager.init();
    });
} else {
    AnimationManager.init();
}

// Export for manual control
window.FamoAnimations = {
    AnimationManager,
    PageTransitions,
    ScrollAnimations,
    CounterAnimations,
    FAQAnimations,
    CardAnimations,
    ButtonAnimations,
    MenuAnimations,
    LoadingAnimations
};