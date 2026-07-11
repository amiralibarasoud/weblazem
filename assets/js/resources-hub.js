(function () {
    'use strict';

    var root = document.getElementById('weblazem-resources-hub');
    if (!root || typeof weblazemResourcesHub === 'undefined') {
        return;
    }

    var modal = document.getElementById('rh-modal');
    var form = document.getElementById('rh-form');
    var feedback = document.getElementById('rh-feedback');
    var resourceIdInput = document.getElementById('rh-resource-id');
    var resourceLabel = document.getElementById('rh-modal-resource');
    var submitBtn = document.getElementById('rh-submit');
    var nameInput = document.getElementById('rh-name');
    var mobileInput = document.getElementById('rh-mobile');

    function setFeedback(message, type) {
        if (!feedback) return;
        feedback.textContent = message || '';
        feedback.classList.remove('is-error', 'is-success');
        if (type === 'error') feedback.classList.add('is-error');
        if (type === 'success') feedback.classList.add('is-success');
    }

    function openModal(btn) {
        if (!modal) return;

        var hasFile = btn.getAttribute('data-has-file') === '1';
        if (!hasFile) {
            window.alert(weblazemResourcesHub.noFileMessage || 'فایل هنوز آماده نیست.');
            return;
        }

        resourceIdInput.value = btn.getAttribute('data-resource-id') || '';
        resourceLabel.textContent = btn.getAttribute('data-resource-title') || '';
        setFeedback('', '');
        form.reset();
        resourceIdInput.value = btn.getAttribute('data-resource-id') || '';
        modal.hidden = false;
        document.body.style.overflow = 'hidden';
        if (nameInput) nameInput.focus();
    }

    function closeModal() {
        if (!modal) return;
        modal.hidden = true;
        document.body.style.overflow = '';
        setFeedback('', '');
    }

    function triggerDownload(url) {
        if (!url) return;
        var link = document.createElement('a');
        link.href = url;
        link.target = '_blank';
        link.rel = 'noopener';
        link.download = '';
        document.body.appendChild(link);
        link.click();
        link.remove();
        window.setTimeout(function () {
            window.open(url, '_blank', 'noopener');
        }, 120);
    }

    root.addEventListener('click', function (e) {
        var btn = e.target.closest('[data-rh-download]');
        if (btn) {
            openModal(btn);
            return;
        }
        if (e.target.closest('[data-rh-close]')) {
            closeModal();
        }
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && modal && !modal.hidden) {
            closeModal();
        }
    });

    if (!form) return;

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        setFeedback('', '');

        var name = (nameInput && nameInput.value || '').trim();
        var mobile = (mobileInput && mobileInput.value || '').trim();
        var resourceId = resourceIdInput ? resourceIdInput.value : '';

        if (!name || !mobile || !resourceId) {
            setFeedback('نام و موبایل را کامل وارد کنید.', 'error');
            return;
        }

        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.textContent = 'در حال ارسال...';
        }

        var body = new FormData();
        body.append('action', 'weblazem_resource_download');
        body.append('nonce', weblazemResourcesHub.nonce);
        body.append('resource_id', resourceId);
        body.append('name', name);
        body.append('mobile', mobile);

        fetch(weblazemResourcesHub.ajaxUrl, {
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
                    var msg = (json.data && json.data.message) || weblazemResourcesHub.genericError;
                    setFeedback(msg, 'error');
                    return;
                }

                var data = json.data || {};
                setFeedback(data.message || weblazemResourcesHub.successMessage, 'success');
                if (data.file_url) {
                    triggerDownload(data.file_url);
                }
                window.setTimeout(closeModal, 900);
            })
            .catch(function () {
                setFeedback(weblazemResourcesHub.genericError, 'error');
            })
            .finally(function () {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'دریافت لینک دانلود';
                }
            });
    });
})();
