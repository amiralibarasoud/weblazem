(function () {
    'use strict';

    if (typeof weblazemLeadsCrm === 'undefined') {
        return;
    }

    function flash(row, message, isError) {
        var el = row.querySelector('[data-crm-flash]');
        if (!el) {
            return;
        }
        el.hidden = false;
        el.textContent = message;
        el.classList.toggle('is-error', !!isError);
        window.setTimeout(function () {
            el.hidden = true;
        }, 2200);
    }

    function post(action, data) {
        var body = new FormData();
        body.append('action', action);
        body.append('nonce', weblazemLeadsCrm.nonce);
        Object.keys(data).forEach(function (key) {
            body.append(key, data[key]);
        });
        return fetch(weblazemLeadsCrm.ajaxUrl, {
            method: 'POST',
            credentials: 'same-origin',
            body: body,
        }).then(function (res) {
            return res.json();
        });
    }

    document.addEventListener('change', function (event) {
        var select = event.target.closest('[data-crm-status]');
        if (!select) {
            return;
        }
        var row = select.closest('tr[data-lead-id]');
        if (!row) {
            return;
        }
        select.disabled = true;
        post('weblazem_crm_update_status', {
            post_id: row.getAttribute('data-lead-id'),
            status: select.value,
        })
            .then(function (json) {
                if (!json || !json.success) {
                    throw new Error((json && json.data && json.data.message) || 'خطا');
                }
                flash(row, json.data.message || 'ذخیره شد');
            })
            .catch(function (err) {
                flash(row, err.message || 'خطا در ذخیره وضعیت', true);
            })
            .finally(function () {
                select.disabled = false;
            });
    });

    document.addEventListener('click', function (event) {
        var btn = event.target.closest('[data-crm-save-note]');
        if (!btn) {
            return;
        }
        var row = btn.closest('tr[data-lead-id]');
        if (!row) {
            return;
        }
        var note = row.querySelector('[data-crm-note]');
        btn.disabled = true;
        post('weblazem_crm_update_note', {
            post_id: row.getAttribute('data-lead-id'),
            note: note ? note.value : '',
        })
            .then(function (json) {
                if (!json || !json.success) {
                    throw new Error((json && json.data && json.data.message) || 'خطا');
                }
                flash(row, json.data.message || 'یادداشت ذخیره شد');
            })
            .catch(function (err) {
                flash(row, err.message || 'خطا در ذخیره یادداشت', true);
            })
            .finally(function () {
                btn.disabled = false;
            });
    });
})();
