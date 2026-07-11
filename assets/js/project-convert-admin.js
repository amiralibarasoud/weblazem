(function () {
    'use strict';

    if (typeof weblazemProjectConvert === 'undefined') {
        return;
    }

    document.addEventListener('click', function (event) {
        var btn = event.target.closest('.weblazem-convert-project-btn');
        if (!btn) {
            return;
        }

        event.preventDefault();
        if (btn.disabled) {
            return;
        }

        var sourceType = btn.getAttribute('data-source-type');
        var sourceId = btn.getAttribute('data-source-id');
        if (!sourceType || !sourceId) {
            return;
        }

        if (!window.confirm('این درخواست به پروژه فعال مشتری تبدیل شود؟')) {
            return;
        }

        var original = btn.textContent;
        btn.disabled = true;
        btn.textContent = 'در حال ایجاد…';

        var body = new FormData();
        body.append('action', 'weblazem_admin_convert_to_project');
        body.append('nonce', weblazemProjectConvert.nonce);
        body.append('source_type', sourceType);
        body.append('source_id', sourceId);

        fetch(weblazemProjectConvert.ajaxUrl, {
            method: 'POST',
            credentials: 'same-origin',
            body: body
        })
            .then(function (res) {
                return res.json();
            })
            .then(function (json) {
                if (!json || !json.success) {
                    throw new Error((json && json.data && json.data.message) || 'خطا در ایجاد پروژه');
                }
                var code = (json.data && json.data.code) || '';
                var url = (json.data && json.data.projectUrl) || '';
                var link = document.createElement('a');
                link.className = 'button button-small';
                link.href = url || '#';
                link.textContent = 'پروژه ' + (code || 'ایجاد شد');
                btn.replaceWith(link);
                window.alert(json.data.message || 'پروژه ساخته شد.');
            })
            .catch(function (err) {
                window.alert(err.message || 'خطا');
                btn.disabled = false;
                btn.textContent = original;
            });
    });
})();
