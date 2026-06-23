(function () {
    'use strict';

    var modal = document.getElementById('weblazem-consult-modal');
    if (!modal || typeof weblazemConsult === 'undefined') {
        return;
    }

    var form = document.getElementById('weblazem-consult-form');
    var feedback = document.getElementById('weblazem-consult-feedback');
    var submitBtn = document.getElementById('weblazem-consult-submit');
    var submitText = submitBtn ? submitBtn.querySelector('.weblazem-consult-modal__submit-text') : null;
    var submitLoading = submitBtn ? submitBtn.querySelector('.weblazem-consult-modal__submit-loading') : null;

    function openModal() {
        modal.hidden = false;
        modal.classList.add('is-open');
        modal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('weblazem-consult-modal-open');

        var firstInput = form ? form.querySelector('input') : null;
        if (firstInput) {
            window.setTimeout(function () {
                firstInput.focus();
            }, 80);
        }
    }

    function closeModal() {
        modal.classList.remove('is-open');
        modal.setAttribute('aria-hidden', 'true');
        modal.hidden = true;
        document.body.classList.remove('weblazem-consult-modal-open');
    }

    function setFeedback(message, type) {
        if (!feedback) {
            return;
        }

        feedback.textContent = message || '';
        feedback.classList.remove('is-error', 'is-success');

        if (type) {
            feedback.classList.add(type === 'success' ? 'is-success' : 'is-error');
        }
    }

    function setLoading(isLoading) {
        if (!submitBtn) {
            return;
        }

        submitBtn.disabled = isLoading;

        if (submitText) {
            submitText.hidden = isLoading;
        }

        if (submitLoading) {
            submitLoading.hidden = !isLoading;
        }
    }

    function handleTriggerClick(event) {
        var trigger = event.target.closest('.weblazem-consult-trigger');

        if (!trigger) {
            return;
        }

        event.preventDefault();
        openModal();
    }

    document.addEventListener('click', handleTriggerClick);

    modal.querySelectorAll('[data-consult-close]').forEach(function (el) {
        el.addEventListener('click', closeModal);
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && modal.classList.contains('is-open')) {
            closeModal();
        }
    });

    if (!form) {
        return;
    }

    form.addEventListener('submit', function (event) {
        event.preventDefault();
        setFeedback('', null);
        setLoading(true);

        var formData = new FormData(form);
        formData.append('action', 'weblazem_submit_consultation');
        formData.append('nonce', weblazemConsult.nonce);
        formData.append('page_url', window.location.href);

        fetch(weblazemConsult.ajaxUrl, {
            method: 'POST',
            credentials: 'same-origin',
            body: formData
        })
            .then(function (response) {
                return response.json().then(function (data) {
                    return { ok: response.ok, data: data };
                });
            })
            .then(function (result) {
                setLoading(false);

                if (result.ok && result.data && result.data.success) {
                    setFeedback(result.data.data.message || weblazemConsult.successMessage, 'success');
                    form.reset();

                    window.setTimeout(function () {
                        closeModal();
                        setFeedback('', null);
                    }, 1800);

                    return;
                }

                var message = (result.data && result.data.data && result.data.data.message)
                    ? result.data.data.message
                    : weblazemConsult.errorMessage;

                setFeedback(message, 'error');
            })
            .catch(function () {
                setLoading(false);
                setFeedback(weblazemConsult.errorMessage, 'error');
            });
    });
})();
