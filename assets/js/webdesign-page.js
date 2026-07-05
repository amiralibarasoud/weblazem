/**
 * Website design page — tab filter for portfolio showcase.
 */
function initWebdesignPortfolioTabs() {
    const section = document.querySelector('.webdesign-portfolio');
    if (!section) {
        return;
    }

    const tabs = section.querySelectorAll('[data-webdesign-tab]');
    const cards = section.querySelectorAll('.webdesign-showcase-card');

    if (!tabs.length || !cards.length) {
        return;
    }

    function filterCards(category) {
        cards.forEach((card) => {
            const cats = (card.getAttribute('data-categories') || '').trim();
            const catList = cats ? cats.split(/\s+/) : [];
            const show = !category || category === 'all' || catList.includes(category);
            card.classList.toggle('is-filtered-out', !show);
            card.style.display = show ? '' : 'none';
        });

        window.dispatchEvent(new Event('resize'));
    }

    tabs.forEach((tab) => {
        tab.addEventListener('click', () => {
            tabs.forEach((t) => {
                t.classList.remove('is-active');
                t.setAttribute('aria-selected', 'false');
            });
            tab.classList.add('is-active');
            tab.setAttribute('aria-selected', 'true');
            filterCards(tab.getAttribute('data-category') || '');
        });
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initWebdesignPortfolioTabs);
} else {
    initWebdesignPortfolioTabs();
}
