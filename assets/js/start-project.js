(function () {
    'use strict';

    var panel = document.getElementById('weblazem-sp-panel');
    var form = document.getElementById('wl-sp-form');
    if (!panel || !form || typeof weblazemStartProject === 'undefined') {
        return;
    }

    var totalSteps = 5;
    var current = 1;

    var prevBtn = document.getElementById('wl-sp-prev');
    var nextBtn = document.getElementById('wl-sp-next');
    var submitBtn = document.getElementById('wl-sp-submit');
    var navEl = document.getElementById('wl-sp-nav');
    var againBtn = document.getElementById('wl-sp-again');

    function setFeedback(message, type) {
        var el = document.getElementById('wl-sp-feedback');
        if (!el) return;
        el.textContent = message || '';
        el.classList.remove('is-error', 'is-success');
        if (type) {
            el.classList.add(type === 'success' ? 'is-success' : 'is-error');
        }
    }

    function showStep(step) {
        current = step;
        form.querySelectorAll('.wl-sp__step').forEach(function (el) {
            var s = el.getAttribute('data-step');
            if (s === 'success') {
                el.hidden = step !== 'success';
            } else {
                el.hidden = parseInt(s, 10) !== step;
            }
        });

        panel.querySelectorAll('[data-sp-step-indicator]').forEach(function (el) {
            var n = parseInt(el.getAttribute('data-sp-step-indicator'), 10);
            if (step === 'success') {
                el.classList.add('is-done');
                el.classList.remove('is-active');
                return;
            }
            el.classList.toggle('is-active', n === step);
            el.classList.toggle('is-done', n < step);
        });

        if (navEl) {
            navEl.hidden = step === 'success';
        }
        if (prevBtn) prevBtn.hidden = step === 1 || step === 'success';
        if (nextBtn) nextBtn.hidden = step === totalSteps || step === 'success';
        if (submitBtn) submitBtn.hidden = step !== totalSteps;

        if (step === totalSteps) {
            buildSummary();
        }
    }

    function validateStep(step) {
        if (step === 1) {
            var type = document.getElementById('wl-sp-type').value;
            var goal = document.getElementById('wl-sp-goal').value.trim();
            if (!type || !goal) {
                setFeedback('نوع پروژه و هدف را کامل کنید.', 'error');
                return false;
            }
        }
        if (step === 4) {
            var budget = document.getElementById('wl-sp-budget').value;
            if (!budget) {
                setFeedback('بازه بودجه را انتخاب کنید.', 'error');
                return false;
            }
        }
        if (step === 5) {
            var name = document.getElementById('wl-sp-name').value.trim();
            var mobile = document.getElementById('wl-sp-mobile').value.trim();
            if (!name || !mobile) {
                setFeedback('نام و موبایل الزامی است.', 'error');
                return false;
            }
        }
        setFeedback('', '');
        return true;
    }

    function labelOf(map, key) {
        if (!map || !key) return key || '—';
        return map[key] || key;
    }

    function contentReadyLabel(val) {
        var map = {
            ready: 'محتوا آماده است',
            partial: 'بخشی آماده است',
            none: 'نیاز به کمک در محتوا'
        };
        return map[val] || val || '—';
    }

    function buildSummary() {
        var el = document.getElementById('wl-sp-summary');
        if (!el) return;
        var type = document.getElementById('wl-sp-type').value;
        var budget = document.getElementById('wl-sp-budget').value;
        var content = (form.querySelector('input[name="content_ready"]:checked') || {}).value || '';
        var deadline = document.getElementById('wl-sp-deadline').value.trim();

        el.innerHTML =
            '<strong>خلاصه بریف</strong><br>' +
            'نوع: ' + labelOf(weblazemStartProject.projectTypes, type) + '<br>' +
            'بودجه: ' + labelOf(weblazemStartProject.budgetRanges, budget) + '<br>' +
            'محتوا: ' + contentReadyLabel(content) +
            (deadline ? '<br>مهلت: ' + deadline : '');
    }

    function post(data) {
        var body = new FormData();
        body.append('action', 'weblazem_start_project_submit');
        body.append('nonce', weblazemStartProject.nonce);
        Object.keys(data).forEach(function (key) {
            body.append(key, data[key]);
        });

        return fetch(weblazemStartProject.ajaxUrl, {
            method: 'POST',
            credentials: 'same-origin',
            body: body
        }).then(function (response) {
            return response.json().then(function (json) {
                return { ok: response.ok, json: json };
            });
        });
    }

    if (nextBtn) {
        nextBtn.addEventListener('click', function () {
            if (!validateStep(current)) return;
            showStep(Math.min(totalSteps, current + 1));
        });
    }

    if (prevBtn) {
        prevBtn.addEventListener('click', function () {
            setFeedback('', '');
            showStep(Math.max(1, current - 1));
        });
    }

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        if (!validateStep(5)) return;

        var contentReady = (form.querySelector('input[name="content_ready"]:checked') || {}).value || '';
        submitBtn.disabled = true;
        setFeedback('در حال ارسال…', '');

        post({
            project_type: document.getElementById('wl-sp-type').value,
            goal: document.getElementById('wl-sp-goal').value.trim(),
            competitors: document.getElementById('wl-sp-competitors').value.trim(),
            content_ready: contentReady,
            pages_needed: document.getElementById('wl-sp-pages').value.trim(),
            budget: document.getElementById('wl-sp-budget').value,
            deadline: document.getElementById('wl-sp-deadline').value.trim(),
            name: document.getElementById('wl-sp-name').value.trim(),
            mobile: document.getElementById('wl-sp-mobile').value.trim(),
            email: document.getElementById('wl-sp-email').value.trim()
        }).then(function (res) {
            submitBtn.disabled = false;
            if (!res.json || !res.json.success) {
                var msg = (res.json && res.json.data && res.json.data.message) || weblazemStartProject.errorMessage;
                setFeedback(msg, 'error');
                return;
            }
            document.getElementById('wl-sp-success-message').textContent =
                (res.json.data && res.json.data.message) || weblazemStartProject.successMessage;
            setFeedback('', '');
            showStep('success');
        }).catch(function () {
            submitBtn.disabled = false;
            setFeedback(weblazemStartProject.errorMessage, 'error');
        });
    });

    if (againBtn) {
        againBtn.addEventListener('click', function () {
            form.reset();
            var partial = form.querySelector('input[name="content_ready"][value="partial"]');
            if (partial) partial.checked = true;
            showStep(1);
        });
    }

    showStep(1);
})();
