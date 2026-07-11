(function () {
    'use strict';

    var root = document.getElementById('weblazem-price-estimator');
    var wizard = document.getElementById('pe-wizard');
    if (!root || !wizard || typeof weblazemPriceEstimator === 'undefined') {
        return;
    }

    if (!weblazemPriceEstimator.enabled) {
        return;
    }

    var state = {
        step: 1,
        site_type: 'corporate',
        pages: '1-5',
        addons: [],
        urgency: 'normal',
        result: null
    };

    var totalSteps = 5;

    function qs(sel, ctx) {
        return (ctx || root).querySelector(sel);
    }

    function qsa(sel, ctx) {
        return Array.prototype.slice.call((ctx || root).querySelectorAll(sel));
    }

    function setFeedback(el, message, type) {
        if (!el) return;
        el.hidden = !message;
        el.textContent = message || '';
        el.classList.remove('is-error', 'is-success');
        if (type) {
            el.classList.add(type === 'success' ? 'is-success' : 'is-error');
        }
    }

    function collectSelections() {
        var site = qs('input[name="pe_site_type"]:checked');
        var pages = qs('input[name="pe_pages"]:checked');
        var urgency = qs('input[name="pe_urgency"]:checked');
        state.site_type = site ? site.value : 'corporate';
        state.pages = pages ? pages.value : '1-5';
        state.urgency = urgency ? urgency.value : 'normal';
        state.addons = qsa('input[name="pe_addons[]"]:checked').map(function (el) {
            return el.value;
        });
    }

    function goToStep(step) {
        state.step = Math.max(1, Math.min(totalSteps, step));
        wizard.setAttribute('data-step', String(state.step));

        qsa('[data-step-panel]').forEach(function (panel) {
            var n = parseInt(panel.getAttribute('data-step-panel'), 10);
            var active = n === state.step;
            panel.hidden = !active;
            panel.classList.toggle('is-active', active);
        });

        qsa('[data-step-indicator]').forEach(function (item) {
            var n = parseInt(item.getAttribute('data-step-indicator'), 10);
            item.classList.toggle('is-active', n === state.step);
            item.classList.toggle('is-done', n < state.step);
        });

        var progress = qs('.pe-progress');
        if (progress) {
            progress.setAttribute('aria-valuenow', String(state.step));
        }
    }

    function post(action, data) {
        var body = new FormData();
        body.append('action', action);
        body.append('nonce', weblazemPriceEstimator.nonce);

        Object.keys(data || {}).forEach(function (key) {
            var val = data[key];
            if (Array.isArray(val)) {
                val.forEach(function (v) {
                    body.append(key + '[]', v);
                });
            } else {
                body.append(key, val);
            }
        });

        return fetch(weblazemPriceEstimator.ajaxUrl, {
            method: 'POST',
            credentials: 'same-origin',
            body: body
        }).then(function (response) {
            return response.json().then(function (json) {
                return { ok: response.ok, json: json };
            });
        });
    }

    function renderResult(data) {
        state.result = data;
        qs('#pe-min').textContent = data.min_fmt;
        qs('#pe-max').textContent = data.max_fmt;
        qs('#pe-estimate').textContent = data.estimate_fmt;

        var summary = qs('#pe-summary');
        summary.innerHTML = '';
        var rows = [
            ['نوع سایت', data.site_type_label],
            ['تعداد صفحات', data.pages_label],
            ['زمان‌بندی', data.urgency_label],
            ['افزونه‌ها', (data.addon_labels && data.addon_labels.length) ? data.addon_labels.join('، ') : 'بدون افزونه']
        ];
        rows.forEach(function (row) {
            var li = document.createElement('li');
            li.innerHTML = '<span>' + row[0] + '</span><strong>' + row[1] + '</strong>';
            summary.appendChild(li);
        });
    }

    function runCalc() {
        collectSelections();
        goToStep(5);

        var loading = qs('#pe-result-loading');
        var body = qs('#pe-result-body');
        var err = qs('#pe-calc-error');

        loading.hidden = false;
        body.hidden = true;
        setFeedback(err, '', null);
        err.hidden = true;

        return post('weblazem_price_estimate_calc', {
            site_type: state.site_type,
            pages: state.pages,
            urgency: state.urgency,
            addons: state.addons
        }).then(function (res) {
            loading.hidden = true;
            if (!res.json || !res.json.success) {
                var msg = (res.json && res.json.data && res.json.data.message) || weblazemPriceEstimator.errorText;
                err.hidden = false;
                setFeedback(err, msg, 'error');
                return;
            }
            renderResult(res.json.data);
            body.hidden = false;
        }).catch(function () {
            loading.hidden = true;
            err.hidden = false;
            setFeedback(err, weblazemPriceEstimator.errorText, 'error');
        });
    }

    function restart() {
        state.result = null;
        var site = qs('input[name="pe_site_type"][value="corporate"]');
        var pages = qs('input[name="pe_pages"][value="1-5"]');
        var urgency = qs('input[name="pe_urgency"][value="normal"]');
        if (site) site.checked = true;
        if (pages) pages.checked = true;
        if (urgency) urgency.checked = true;
        qsa('input[name="pe_addons[]"]').forEach(function (el) {
            el.checked = false;
        });

        var leadForm = qs('#pe-lead-form');
        var leadSuccess = qs('#pe-lead-success');
        if (leadForm) {
            leadForm.hidden = false;
            leadForm.reset();
        }
        if (leadSuccess) leadSuccess.hidden = true;
        setFeedback(qs('#pe-lead-feedback'), '', null);

        goToStep(1);
    }

    wizard.addEventListener('click', function (e) {
        var next = e.target.closest('[data-pe-next]');
        var prev = e.target.closest('[data-pe-prev]');
        var calc = e.target.closest('[data-pe-calc]');
        var restartBtn = e.target.closest('[data-pe-restart]');

        if (next) {
            collectSelections();
            goToStep(state.step + 1);
        }
        if (prev) {
            goToStep(state.step - 1);
        }
        if (calc) {
            runCalc();
        }
        if (restartBtn) {
            restart();
        }
    });

    // Card selection visual state
    qsa('.pe-card input').forEach(function (input) {
        input.addEventListener('change', function () {
            var group = input.closest('.pe-cards');
            if (!group) return;
            if (input.type === 'radio') {
                qsa('.pe-card', group).forEach(function (card) {
                    card.classList.toggle('is-selected', card.querySelector('input') === input && input.checked);
                });
            } else {
                input.closest('.pe-card').classList.toggle('is-selected', input.checked);
            }
        });
        if (input.checked) {
            input.dispatchEvent(new Event('change'));
        }
    });

    var leadForm = qs('#pe-lead-form');
    if (leadForm && weblazemPriceEstimator.saveLead) {
        leadForm.addEventListener('submit', function (e) {
            e.preventDefault();
            collectSelections();

            var name = qs('#pe-name').value.trim();
            var mobile = qs('#pe-mobile').value.trim();
            var feedback = qs('#pe-lead-feedback');
            var btn = leadForm.querySelector('button[type="submit"]');

            if (name.length < 2) {
                setFeedback(feedback, 'لطفاً نام خود را وارد کنید.', 'error');
                return;
            }

            var digits = mobile.replace(/\D/g, '');
            if (digits.indexOf('98') === 0 && digits.length >= 12) {
                digits = '0' + digits.slice(2);
            }
            if (/^9\d{9}$/.test(digits)) {
                digits = '0' + digits;
            }
            if (!/^09\d{9}$/.test(digits)) {
                setFeedback(feedback, 'شماره موبایل معتبر نیست.', 'error');
                return;
            }
            mobile = digits;

            btn.disabled = true;
            setFeedback(feedback, 'در حال ارسال...', null);

            post('weblazem_price_estimate_lead', {
                name: name,
                mobile: mobile,
                site_type: state.site_type,
                pages: state.pages,
                urgency: state.urgency,
                addons: state.addons
            }).then(function (res) {
                btn.disabled = false;
                if (!res.json || !res.json.success) {
                    var msg = (res.json && res.json.data && res.json.data.message) || weblazemPriceEstimator.errorText;
                    setFeedback(feedback, msg, 'error');
                    return;
                }
                leadForm.hidden = true;
                var success = qs('#pe-lead-success');
                var successMsg = qs('#pe-lead-success-msg');
                if (successMsg) {
                    successMsg.textContent = (res.json.data && res.json.data.message) || weblazemPriceEstimator.successText;
                }
                if (success) success.hidden = false;
                setFeedback(feedback, '', null);
            }).catch(function () {
                btn.disabled = false;
                setFeedback(feedback, weblazemPriceEstimator.errorText, 'error');
            });
        });
    }

    goToStep(1);
})();
