(function () {
    'use strict';

    if (typeof weblazemReferral === 'undefined') {
        return;
    }

    var cfg = weblazemReferral;

    function setCookie(name, value, days) {
        var maxAge = Math.max(1, parseInt(days, 10) || 30) * 24 * 60 * 60;
        var parts = [
            encodeURIComponent(name) + '=' + encodeURIComponent(value),
            'path=/',
            'max-age=' + maxAge,
            'SameSite=Lax'
        ];
        if (window.location.protocol === 'https:') {
            parts.push('Secure');
        }
        document.cookie = parts.join('; ');
    }

    function getCookie(name) {
        var match = document.cookie.match(new RegExp('(?:^|; )' + name.replace(/([.$?*|{}()[\]\\/+^])/g, '\\$1') + '=([^;]*)'));
        return match ? decodeURIComponent(match[1]) : '';
    }

    if (cfg.refFromUrl) {
        setCookie(cfg.cookieName, cfg.refFromUrl, cfg.cookieDays);
    }

    if (!cfg.isPortal) {
        return;
    }

    var joinForm = document.getElementById('rf-join-form');
    var leadForm = document.getElementById('rf-lead-form');
    var shareBox = document.querySelector('[data-rf-share]');
    var shareInput = document.querySelector('[data-rf-share-url]');
    var codeEl = document.querySelector('[data-rf-code]');
    var rewardEl = document.querySelector('[data-rf-reward]');
    var copyBtn = document.querySelector('[data-rf-copy]');

    function feedback(el, message, type) {
        if (!el) return;
        el.textContent = message || '';
        el.classList.remove('is-error', 'is-success');
        if (type) {
            el.classList.add(type === 'success' ? 'is-success' : 'is-error');
        }
    }

    function showShare(data) {
        if (!shareBox) return;
        shareBox.hidden = false;
        if (shareInput) shareInput.value = data.shareUrl || '';
        if (codeEl) codeEl.textContent = data.code || '';
        if (rewardEl) rewardEl.textContent = data.reward ? ('پاداش شما: ' + data.reward) : '';
    }

    if (joinForm) {
        joinForm.addEventListener('submit', function (e) {
            e.preventDefault();
            var fb = document.querySelector('[data-rf-join-feedback]');
            var btn = document.querySelector('[data-rf-join-submit]');
            var name = (document.getElementById('rf-join-name') || {}).value || '';
            var mobile = (document.getElementById('rf-join-mobile') || {}).value || '';
            name = name.trim();
            mobile = mobile.trim();

            if (!name || !mobile) {
                feedback(fb, 'نام و موبایل الزامی است.', 'error');
                return;
            }

            if (btn) btn.disabled = true;
            feedback(fb, 'در حال ثبت…', '');

            var body = new FormData();
            body.append('action', 'weblazem_referral_join');
            body.append('nonce', cfg.nonce);
            body.append('name', name);
            body.append('mobile', mobile);

            fetch(cfg.ajaxUrl, { method: 'POST', body: body, credentials: 'same-origin' })
                .then(function (res) { return res.json(); })
                .then(function (json) {
                    if (!json || !json.success) {
                        var msg = (json && json.data && json.data.message) ? json.data.message : cfg.errorMessage;
                        feedback(fb, msg, 'error');
                        return;
                    }
                    feedback(fb, json.data.message || '', 'success');
                    showShare(json.data);
                })
                .catch(function () {
                    feedback(fb, cfg.errorMessage, 'error');
                })
                .finally(function () {
                    if (btn) btn.disabled = false;
                });
        });
    }

    if (leadForm) {
        leadForm.addEventListener('submit', function (e) {
            e.preventDefault();
            var fb = document.querySelector('[data-rf-lead-feedback]');
            var btn = document.querySelector('[data-rf-lead-submit]');
            var name = ((document.getElementById('rf-lead-name') || {}).value || '').trim();
            var mobile = ((document.getElementById('rf-lead-mobile') || {}).value || '').trim();
            var service = ((document.getElementById('rf-lead-service') || {}).value || '').trim();
            var refCode = ((document.getElementById('rf-lead-code') || {}).value || '').trim();

            if (!refCode) {
                refCode = getCookie(cfg.cookieName) || cfg.refCookie || '';
            }

            if (!name || !mobile || !service) {
                feedback(fb, 'نام، موبایل و خدمت الزامی است.', 'error');
                return;
            }
            if (!refCode) {
                feedback(fb, 'کد معرفی را وارد کنید یا از لینک معرفی وارد شوید.', 'error');
                return;
            }

            if (btn) btn.disabled = true;
            feedback(fb, 'در حال ثبت…', '');

            var body = new FormData();
            body.append('action', 'weblazem_referral_lead');
            body.append('nonce', cfg.nonce);
            body.append('name', name);
            body.append('mobile', mobile);
            body.append('service', service);
            body.append('ref_code', refCode);

            fetch(cfg.ajaxUrl, { method: 'POST', body: body, credentials: 'same-origin' })
                .then(function (res) { return res.json(); })
                .then(function (json) {
                    if (!json || !json.success) {
                        var msg = (json && json.data && json.data.message) ? json.data.message : cfg.errorMessage;
                        feedback(fb, msg, 'error');
                        return;
                    }
                    feedback(fb, json.data.message || '', 'success');
                    if (json.data.code) {
                        setCookie(cfg.cookieName, json.data.code, cfg.cookieDays);
                    }
                    leadForm.reset();
                    var codeInput = document.getElementById('rf-lead-code');
                    if (codeInput && json.data.code) {
                        codeInput.value = json.data.code;
                    }
                })
                .catch(function () {
                    feedback(fb, cfg.errorMessage, 'error');
                })
                .finally(function () {
                    if (btn) btn.disabled = false;
                });
        });
    }

    if (copyBtn && shareInput) {
        copyBtn.addEventListener('click', function () {
            var val = shareInput.value;
            if (!val) return;
            var done = function () {
                var original = copyBtn.textContent;
                copyBtn.textContent = cfg.copiedText || 'کپی شد!';
                setTimeout(function () {
                    copyBtn.textContent = original;
                }, 1600);
            };
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(val).then(done).catch(function () {
                    shareInput.select();
                    document.execCommand('copy');
                    done();
                });
            } else {
                shareInput.select();
                document.execCommand('copy');
                done();
            }
        });
    }
})();
