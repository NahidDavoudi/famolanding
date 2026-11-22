// Shared lightweight navigation script loaded across all public pages.
// Always handles desktop dropdowns ([data-famo-dropdown]); handles mobile menu & footer year on static pages (data-static-nav="1").

(function () {
    function svgIcon(name, cls) {
        var suffix = cls ? ' ' + cls : '';
        var icon = window.famoLucideName ? window.famoLucideName(name) : name.replace(/^icon-/, '');
        return '<i class="icon' + suffix + '" data-lucide="' + icon + '" aria-hidden="true"></i>';
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
        var currentPath = (window.location.pathname || '/').replace(/\/index\.php$/i, '/');
        var navLinks = document.querySelectorAll('header nav a[href]');
        navLinks.forEach(function (link) {
            var href = link.getAttribute('href') || '';
            if (!href || href.indexOf('#') !== -1 || /^https?:\/\//i.test(href)) return;
            try {
                var target = new URL(href, window.location.href);
                var targetPath = target.pathname.replace(/\/index\.php$/i, '/');
                if (targetPath === currentPath) {
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

    function init() {
        initActiveNav();
        initDropdowns();
        initStaticMobileMenu();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
