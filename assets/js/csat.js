(function () {
    'use strict';

    var root = document.getElementById('weblazem-csat');
    if (!root || root.getAttribute('data-csat-mode') !== 'form' || typeof weblazemCsat === 'undefined') {
        return;
    }

    var form = document.getElementById('csat-form');
    var feedback = document.getElementById('csat-feedback');
    var submitBtn = document.getElementById('csat-submit');
    var successBox = document.getElementById('csat-success');
    var formWrap = document.getElementById('csat-form-wrap');

    function setFeedback(message, type) {
        if (!feedback) return;
        feedback.textContent = message || '';
        feedback.classList.remove('is-error', 'is-success');
        if (type === 'error') feedback.classList.add('is-error');
        if (type === 'success') feedback.classList.add('is-success');
    }

    function paintStars(group, value, preview) {
        var buttons = group.querySelectorAll('.csat-star');
        var active = preview || value || 0;
        buttons.forEach(function (btn) {
            var v = parseInt(btn.getAttribute('data-value'), 10);
            var on = v <= active;
            btn.classList.toggle('is-active', value > 0 && v <= value);
            btn.classList.toggle('is-preview', preview > 0 && v <= preview && !(value > 0 && v <= value));
            var icon = btn.querySelector('i');
            if (icon) {
                icon.className = on ? 'fas fa-star' : 'far fa-star';
            }
        });
    }

    root.querySelectorAll('[data-csat-stars]').forEach(function (group) {
        var key = group.getAttribute('data-csat-stars');
        var input = document.getElementById('csat-score-' + key);
        var value = 0;

        group.addEventListener('mouseover', function (e) {
            var btn = e.target.closest('.csat-star');
            if (!btn) return;
            paintStars(group, value, parseInt(btn.getAttribute('data-value'), 10));
        });

        group.addEventListener('mouseleave', function () {
            paintStars(group, value, 0);
        });

        group.addEventListener('click', function (e) {
            var btn = e.target.closest('.csat-star');
            if (!btn) return;
            value = parseInt(btn.getAttribute('data-value'), 10);
            if (input) input.value = String(value);
            paintStars(group, value, 0);

            if (key === 'overall') {
                var hint = group.parentNode.querySelector('[data-stars-hint]');
                if (hint) {
                    hint.textContent = value + ' از ۵ انتخاب شد';
                }
            }
        });
    });

    if (!form) return;

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        setFeedback('', '');

        var overall = document.getElementById('csat-score-overall');
        if (!overall || !overall.value) {
            setFeedback('لطفاً امتیاز کلی را انتخاب کنید.', 'error');
            return;
        }

        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.textContent = 'در حال ارسال...';
        }

        var body = new FormData(form);
        body.append('action', 'weblazem_csat_submit');
        body.append('nonce', weblazemCsat.nonce);
        if (!body.get('allow_publish')) {
            body.set('allow_publish', '0');
        } else {
            body.set('allow_publish', '1');
        }

        fetch(weblazemCsat.ajaxUrl, {
            method: 'POST',
            credentials: 'same-origin',
            body: body
        })
            .then(function (response) {
                return response.json().then(function (json) {
                    return { ok: response.ok, json: json };
                });
            })
            .then(function (result) {
                var json = result.json || {};
                if (!result.ok || !json.success) {
                    var msg = (json.data && json.data.message) || weblazemCsat.genericError;
                    setFeedback(msg, 'error');
                    return;
                }

                var data = json.data || {};
                if (form) form.hidden = true;
                if (successBox) {
                    var titleEl = document.getElementById('csat-success-title');
                    var msgEl = document.getElementById('csat-success-msg');
                    if (titleEl && data.title) titleEl.textContent = data.title;
                    if (msgEl && data.message) msgEl.textContent = data.message;
                    successBox.hidden = false;
                }
                if (formWrap) {
                    formWrap.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            })
            .catch(function () {
                setFeedback(weblazemCsat.genericError, 'error');
            })
            .finally(function () {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'ثبت نظرسنجی';
                }
            });
    });
})();
