(function () {
    'use strict';

    var form = document.getElementById('weblazem-contact-form');
    if (!form || typeof weblazemContact === 'undefined') {
        return;
    }

    var statusEl = document.getElementById('contact-form-status');
    var submitBtn = document.getElementById('contact-form-submit');

    function setStatus(message, type) {
        if (!statusEl) {
            return;
        }
        statusEl.textContent = message || '';
        statusEl.classList.remove('is-success', 'is-error');
        if (type) {
            statusEl.classList.add('is-' + type);
        }
    }

    form.addEventListener('submit', function (event) {
        event.preventDefault();
        setStatus('');

        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        var formData = new FormData(form);
        formData.append('action', 'weblazem_submit_contact');
        formData.append('nonce', weblazemContact.nonce);

        submitBtn.disabled = true;
        var originalText = submitBtn.textContent;
        submitBtn.textContent = 'در حال ارسال...';

        fetch(weblazemContact.ajaxUrl, {
            method: 'POST',
            body: formData,
            credentials: 'same-origin',
        })
            .then(function (response) {
                return response.json().then(function (data) {
                    return { ok: response.ok, data: data };
                });
            })
            .then(function (result) {
                if (result.data && result.data.success) {
                    setStatus(result.data.data.message || weblazemContact.successMessage, 'success');
                    form.reset();
                    return;
                }

                var message = (result.data && result.data.data && result.data.data.message)
                    ? result.data.data.message
                    : weblazemContact.errorMessage;

                if (result.data && result.data.data && result.data.data.saved) {
                    setStatus(message, 'error');
                    form.reset();
                    return;
                }

                setStatus(message, 'error');
            })
            .catch(function () {
                setStatus(weblazemContact.errorMessage, 'error');
            })
            .finally(function () {
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
            });
    });
})();
