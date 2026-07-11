(function () {
    'use strict';

    var panel = document.getElementById('weblazem-scheduling-panel');
    if (!panel || typeof weblazemScheduling === 'undefined') {
        return;
    }

    var state = {
        date: '',
        dateLabel: '',
        time: ''
    };

    var datesView = document.getElementById('wl-sched-dates-view');
    var timesView = document.getElementById('wl-sched-times-view');
    var formView = document.getElementById('wl-sched-form-view');
    var successView = document.getElementById('wl-sched-success-view');
    var datesEl = document.getElementById('wl-sched-dates');
    var timesEl = document.getElementById('wl-sched-times');
    var form = document.getElementById('wl-sched-form');

    function showView(name) {
        [datesView, timesView, formView, successView].forEach(function (view) {
            if (!view) return;
            view.hidden = view.getAttribute('data-view') !== name;
        });

        var stepMap = { dates: 1, times: 2, form: 3, success: 3 };
        var current = stepMap[name] || 1;
        panel.querySelectorAll('[data-step-indicator]').forEach(function (el) {
            var n = parseInt(el.getAttribute('data-step-indicator'), 10);
            el.classList.toggle('is-active', n === current);
            el.classList.toggle('is-done', n < current);
        });
    }

    function setFeedback(id, message, type) {
        var el = document.getElementById(id);
        if (!el) return;
        el.textContent = message || '';
        el.classList.remove('is-error', 'is-success');
        if (type) {
            el.classList.add(type === 'success' ? 'is-success' : 'is-error');
        }
    }

    function post(action, data) {
        var body = new FormData();
        body.append('action', action);
        body.append('nonce', weblazemScheduling.nonce);
        Object.keys(data || {}).forEach(function (key) {
            body.append(key, data[key]);
        });

        return fetch(weblazemScheduling.ajaxUrl, {
            method: 'POST',
            credentials: 'same-origin',
            body: body
        }).then(function (response) {
            return response.json().then(function (json) {
                return { ok: response.ok, json: json };
            });
        });
    }

    function renderDates() {
        if (!datesEl) return;
        datesEl.innerHTML = '';
        var dates = weblazemScheduling.dates || [];

        if (!dates.length) {
            setFeedback('wl-sched-dates-feedback', 'در بازه فعلی روز خالی برای رزرو وجود ندارد.', 'error');
            return;
        }

        dates.forEach(function (item) {
            var btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'wl-sched__date-btn';
            btn.setAttribute('role', 'option');
            btn.innerHTML =
                '<span class="dow"></span>' +
                '<span class="ymd"></span>' +
                '<span class="cnt"></span>';
            btn.querySelector('.dow').textContent = item.weekday || '';
            btn.querySelector('.ymd').textContent = item.date || '';
            btn.querySelector('.cnt').textContent = (item.slotCount || 0) + ' نوبت آزاد';
            btn.addEventListener('click', function () {
                state.date = item.date;
                state.dateLabel = item.label || item.date;
                state.time = '';
                loadSlots(item.date);
            });
            datesEl.appendChild(btn);
        });
    }

    function loadSlots(date) {
        setFeedback('wl-sched-times-feedback', 'در حال بارگذاری…', '');
        timesEl.innerHTML = '';
        document.getElementById('wl-sched-selected-date').textContent = state.dateLabel;
        showView('times');

        post('weblazem_scheduling_slots', { date: date }).then(function (res) {
            var data = res.json && res.json.data ? res.json.data : {};
            var slots = data.slots || [];

            if (!res.json || !res.json.success) {
                var msg = (res.json && res.json.data && res.json.data.message) || weblazemScheduling.errorMessage;
                setFeedback('wl-sched-times-feedback', msg, 'error');
                return;
            }

            if (!slots.length) {
                setFeedback('wl-sched-times-feedback', 'برای این روز نوبت خالی نیست.', 'error');
                return;
            }

            setFeedback('wl-sched-times-feedback', '', '');
            slots.forEach(function (time) {
                var chip = document.createElement('button');
                chip.type = 'button';
                chip.className = 'wl-sched__time-chip';
                chip.textContent = time;
                chip.addEventListener('click', function () {
                    state.time = time;
                    document.getElementById('wl-sched-summary').textContent =
                        'رزرو برای ' + state.dateLabel + ' ساعت ' + time;
                    showView('form');
                });
                timesEl.appendChild(chip);
            });
        }).catch(function () {
            setFeedback('wl-sched-times-feedback', weblazemScheduling.errorMessage, 'error');
        });
    }

    panel.querySelectorAll('[data-sched-back]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            showView(btn.getAttribute('data-sched-back'));
        });
    });

    if (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            var name = document.getElementById('wl-sched-name').value.trim();
            var mobile = document.getElementById('wl-sched-mobile').value.trim();
            var note = document.getElementById('wl-sched-note').value.trim();
            var submitBtn = document.getElementById('wl-sched-submit');

            if (!name || !mobile || !state.date || !state.time) {
                setFeedback('wl-sched-form-feedback', 'لطفاً همه فیلدهای ضروری را پر کنید.', 'error');
                return;
            }

            submitBtn.disabled = true;
            setFeedback('wl-sched-form-feedback', 'در حال ثبت…', '');

            post('weblazem_scheduling_book', {
                name: name,
                mobile: mobile,
                date: state.date,
                time: state.time,
                note: note
            }).then(function (res) {
                submitBtn.disabled = false;
                if (!res.json || !res.json.success) {
                    var msg = (res.json && res.json.data && res.json.data.message) || weblazemScheduling.errorMessage;
                    setFeedback('wl-sched-form-feedback', msg, 'error');
                    return;
                }

                var data = res.json.data || {};
                document.getElementById('wl-sched-success-message').textContent =
                    data.message || weblazemScheduling.successMessage;
                document.getElementById('wl-sched-success-meta').textContent =
                    (data.date || state.date) + ' · ' + (data.time || state.time);
                showView('success');
                form.reset();
            }).catch(function () {
                submitBtn.disabled = false;
                setFeedback('wl-sched-form-feedback', weblazemScheduling.errorMessage, 'error');
            });
        });
    }

    var againBtn = document.getElementById('wl-sched-again');
    if (againBtn) {
        againBtn.addEventListener('click', function () {
            state.date = '';
            state.time = '';
            state.dateLabel = '';
            showView('dates');
        });
    }

    renderDates();
    showView('dates');
})();
