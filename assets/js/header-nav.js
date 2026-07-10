(function () {
    'use strict';

    var header = document.getElementById('weblazem-site-header');
    var toggle = document.getElementById('weblazem-header-toggle');
    var drawer = document.getElementById('weblazem-header-drawer');
    var overlay = document.getElementById('weblazem-header-overlay');

    if (!header || !toggle || !drawer || !overlay) {
        return;
    }

    function openMenu() {
        header.classList.add('is-menu-open');
        drawer.hidden = false;
        drawer.classList.add('is-open');
        drawer.setAttribute('aria-hidden', 'false');
        overlay.hidden = false;
        overlay.classList.add('is-visible');
        toggle.setAttribute('aria-expanded', 'true');
        document.body.classList.add('weblazem-header-menu-open');
    }

    function closeMenu() {
        header.classList.remove('is-menu-open');
        drawer.classList.remove('is-open');
        drawer.setAttribute('aria-hidden', 'true');
        overlay.classList.remove('is-visible');
        toggle.setAttribute('aria-expanded', 'false');
        document.body.classList.remove('weblazem-header-menu-open');

        window.setTimeout(function () {
            if (!header.classList.contains('is-menu-open')) {
                drawer.hidden = true;
                overlay.hidden = true;
            }
        }, 280);
    }

    function isOpen() {
        return header.classList.contains('is-menu-open');
    }

    toggle.addEventListener('click', function () {
        if (isOpen()) {
            closeMenu();
        } else {
            openMenu();
        }
    });

    overlay.addEventListener('click', closeMenu);

    drawer.querySelectorAll('a').forEach(function (link) {
        link.addEventListener('click', closeMenu);
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && isOpen()) {
            closeMenu();
        }
    });

    window.addEventListener('resize', function () {
        if (window.innerWidth > 1024 && isOpen()) {
            closeMenu();
        }
    });
})();
