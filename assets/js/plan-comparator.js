(function () {
    'use strict';

    var root = document.querySelector('[data-pc-root]');
    if (!root || typeof weblazemPlanComparator === 'undefined') {
        return;
    }

    var cfg = weblazemPlanComparator;
    var catBtns = root.querySelectorAll('[data-pc-cat]');
    var budgetInput = root.querySelector('[data-pc-budget]');
    var budgetLabel = root.querySelector('[data-pc-budget-label]');
    var supportToggle = root.querySelector('[data-pc-support]');
    var seoToggle = root.querySelector('[data-pc-seo]');
    var cards = root.querySelectorAll('[data-pc-card]');
    var emptyEl = root.querySelector('[data-pc-empty]');
    var table = root.querySelector('[data-pc-table]');

    var state = {
        category: 'all',
        budget: budgetInput ? parseInt(budgetInput.value, 10) : (cfg.budgetMax || 999999999),
        support: false,
        seo: false
    };

    function formatToman(amount) {
        var n = parseInt(amount, 10) || 0;
        try {
            return n.toLocaleString('fa-IR') + ' تومان';
        } catch (e) {
            return n.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',') + ' تومان';
        }
    }

    function applyFilters() {
        var visible = 0;

        cards.forEach(function (card) {
            var cat = card.getAttribute('data-pc-category') || '';
            var price = parseInt(card.getAttribute('data-pc-price'), 10) || 0;
            var hasSupport = card.getAttribute('data-pc-support') === '1';
            var hasSeo = card.getAttribute('data-pc-seo') === '1';
            var id = card.getAttribute('data-pc-id');

            var ok = true;
            if (state.category !== 'all' && cat !== state.category) {
                ok = false;
            }
            if (price > state.budget) {
                ok = false;
            }
            if (state.support && !hasSupport) {
                ok = false;
            }
            if (state.seo && !hasSeo) {
                ok = false;
            }

            card.hidden = !ok;
            if (ok) visible += 1;

            if (table && id) {
                table.querySelectorAll('[data-pc-col="' + id + '"]').forEach(function (cell) {
                    cell.hidden = !ok;
                });
            }
        });

        if (emptyEl) {
            emptyEl.hidden = visible > 0;
        }
    }

    catBtns.forEach(function (btn) {
        btn.addEventListener('click', function () {
            state.category = btn.getAttribute('data-pc-cat') || 'all';
            catBtns.forEach(function (b) {
                b.classList.toggle('is-active', b === btn);
            });
            applyFilters();
        });
    });

    if (budgetInput) {
        budgetInput.addEventListener('input', function () {
            state.budget = parseInt(budgetInput.value, 10) || 0;
            if (budgetLabel) {
                budgetLabel.textContent = formatToman(state.budget);
            }
            applyFilters();
        });
    }

    if (supportToggle) {
        supportToggle.addEventListener('change', function () {
            state.support = !!supportToggle.checked;
            applyFilters();
        });
    }

    if (seoToggle) {
        seoToggle.addEventListener('change', function () {
            state.seo = !!seoToggle.checked;
            applyFilters();
        });
    }

    applyFilters();
})();
