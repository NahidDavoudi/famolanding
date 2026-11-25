// Shared lightweight navigation & static FAQ script loaded across all public pages.
// Handles active nav highlighting, desktop dropdowns ([data-famo-dropdown]), static mobile menu, and static FAQ accordions.

(function () {
    function svgIcon(name, cls) {
        var suffix = cls ? ' ' + cls : '';
        var icon = window.famoLucideName ? window.famoLucideName(name) : name.replace(/^icon-/, '');
        return '<i class="icon' + suffix + '" data-lucide="' + icon + '" aria-hidden="true"></i>';
    }

    function normalizePath(pathname) {
        var clean = (pathname || '/').replace(/\/index\.php$/i, '/');
        if (clean !== '/' && clean.charAt(clean.length - 1) !== '/') {
            clean += '/';
        }
        return clean;
    }

    function initDropdowns() {
        var dropdowns = document.querySelectorAll('[data-famo-dropdown]');
        dropdowns.forEach(function (wrap) {
            if (wrap.dataset.famoDropdownBound) return;
            wrap.dataset.famoDropdownBound = '1';

            var trigger = wrap.querySelector('[data-famo-dropdown-trigger]');
            var panel = wrap.querySelector('[data-famo-dropdown-menu]');
            if (!trigger || !panel) return;

            function setOpen(open) {
                panel.hidden = !open;
                trigger.setAttribute('aria-expanded', open ? 'true' : 'false');
                wrap.classList.toggle('is-open', open);
            }

            trigger.addEventListener('click', function (event) {
                event.stopPropagation();
                var willOpen = panel.hidden;
                document.querySelectorAll('[data-famo-dropdown].is-open').forEach(function (other) {
                    if (other !== wrap) {
                        var otherPanel = other.querySelector('[data-famo-dropdown-menu]');
                        var otherTrigger = other.querySelector('[data-famo-dropdown-trigger]');
                        if (otherPanel) otherPanel.hidden = true;
                        if (otherTrigger) otherTrigger.setAttribute('aria-expanded', 'false');
                        other.classList.remove('is-open');
                    }
                });
                setOpen(willOpen);
            });

            document.addEventListener('click', function (event) {
                if (!wrap.contains(event.target) && !panel.hidden) {
                    setOpen(false);
                }
            });

            wrap.addEventListener('keydown', function (event) {
                if (event.key === 'Escape' && !panel.hidden) {
                    setOpen(false);
                    trigger.focus();
                }
            });
        });
    }

    function initStaticMobileMenu() {
        if (!document.body || document.body.dataset.staticNav !== '1') return;

        var mobileMenuBtn = document.getElementById('mobileMenuBtn');
        var mobileMenu = document.getElementById('mobileMenu');

        if (mobileMenuBtn && mobileMenu && !mobileMenuBtn.dataset.famoNavBound) {
            mobileMenuBtn.dataset.famoNavBound = '1';

            function closeMobile() {
                if (!mobileMenu.classList.contains('hidden')) {
                    mobileMenu.classList.add('hidden');
                    mobileMenuBtn.innerHTML = svgIcon('icon-menu', 'icon--lg');
                    mobileMenuBtn.setAttribute('aria-expanded', 'false');
                    if (window.refreshLucideIcons) window.refreshLucideIcons(mobileMenuBtn);
                }
            }

            mobileMenuBtn.addEventListener('click', function (event) {
                event.stopPropagation();
                var isHidden = mobileMenu.classList.contains('hidden');
                mobileMenu.classList.toggle('hidden');
                mobileMenuBtn.innerHTML = isHidden
                    ? svgIcon('icon-x', 'icon--lg')
                    : svgIcon('icon-menu', 'icon--lg');
                mobileMenuBtn.setAttribute('aria-expanded', isHidden ? 'true' : 'false');
                if (window.refreshLucideIcons) window.refreshLucideIcons(mobileMenuBtn);
            });

            document.addEventListener('click', function (event) {
                if (!mobileMenu.contains(event.target) && !mobileMenuBtn.contains(event.target)) {
                    closeMobile();
                }
            });

            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape') closeMobile();
            });
        }

        var copyrightYear = document.getElementById('copyright-year');
        if (copyrightYear) {
            copyrightYear.textContent = new Date().toLocaleDateString('fa-IR', { year: 'numeric' });
        }
    }

    function initActiveNav() {
        var currentPath = normalizePath(window.location.pathname || '/');
        var navLinks = document.querySelectorAll('header nav a[href]');
        var exactMatches = new Set();

        navLinks.forEach(function (link) {
            var href = link.getAttribute('href') || '';
            if (!href || href.indexOf('#') !== -1 || /^https?:\/\//i.test(href)) return;
            try {
                var targetPath = normalizePath(new URL(href, window.location.href).pathname);
                if (targetPath === currentPath) {
                    exactMatches.add(link);
                }
            } catch (_) {
                // Ignore malformed relative URLs
            }
        });

        navLinks.forEach(function (link) {
            var href = link.getAttribute('href') || '';
            if (!href || href.indexOf('#') !== -1 || /^https?:\/\//i.test(href)) return;
            try {
                var targetPath = normalizePath(new URL(href, window.location.href).pathname);
                var isExact = exactMatches.has(link);
                var isParentSection = false;
                if (!isExact && exactMatches.size === 0) {
                    if (targetPath === '/courses/' && currentPath.indexOf('/courses/') === 0) {
                        isParentSection = true;
                    } else if (targetPath === '/blog/' && currentPath.indexOf('/blog/') === 0) {
                        isParentSection = true;
                    } else if (targetPath === '/contact/' && currentPath.indexOf('/locations/') === 0) {
                        isParentSection = true;
                    }
                }

                if (isExact || isParentSection) {
                    link.setAttribute('aria-current', 'page');
                    link.classList.add('is-active');
                    var parentDropdown = link.closest('[data-famo-dropdown]');
                    if (parentDropdown) {
                        parentDropdown.classList.add('is-active-group');
                    }
                }
            } catch (_) {
                // Ignore malformed relative URLs
            }
        });
    }

    function initStaticFaqs() {
        // Pages loading animation.js already wire FAQAnimations; avoid double-binding.
        if (document.querySelector('script[src*="animation.js"]')) return;

        var faqItems = document.querySelectorAll('.faq-item');
        if (!faqItems.length) return;

        function closeItem(item) {
            var q = item.querySelector('.faq-question');
            var a = item.querySelector('.faq-answer');
            var ic = item.querySelector('.faq-icon');
            item.classList.remove('active');
            if (q) q.setAttribute('aria-expanded', 'false');
            if (a) a.style.maxHeight = '0px';
            if (ic) ic.style.transform = 'rotate(0deg)';
        }

        function openItem(item) {
            var q = item.querySelector('.faq-question');
            var a = item.querySelector('.faq-answer');
            var ic = item.querySelector('.faq-icon');
            item.classList.add('active');
            if (q) q.setAttribute('aria-expanded', 'true');
            if (a) a.style.maxHeight = 'none';
            if (ic) ic.style.transform = 'rotate(180deg)';
        }

        faqItems.forEach(function (item) {
            if (item.dataset.faqBound === '1') return;
            item.dataset.faqBound = '1';

            var question = item.querySelector('.faq-question');
            var answer = item.querySelector('.faq-answer');
            if (!question || !answer) return;

            answer.style.maxHeight = '0px';
            question.setAttribute('aria-expanded', 'false');

            function toggle() {
                var wasActive = item.classList.contains('active');
                document.querySelectorAll('.faq-item.active').forEach(function (other) {
                    if (other !== item) closeItem(other);
                });
                if (wasActive) {
                    closeItem(item);
                } else {
                    openItem(item);
                }
            }

            question.addEventListener('click', toggle);
            question.addEventListener('keydown', function (event) {
                if (event.key === 'Enter' || event.key === ' ') {
                    event.preventDefault();
                    toggle();
                }
            });
        });
    }

    function init() {
        initActiveNav();
        initDropdowns();
        initStaticMobileMenu();
        initStaticFaqs();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
