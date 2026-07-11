(function () {
    'use strict';

    var panel = document.getElementById('weblazem-ps-panel');
    if (!panel || typeof weblazemProjectStatus === 'undefined') {
        return;
    }

    var state = {
        mobile: '',
        projects: [],
        currentId: null
    };

    var loginView = document.getElementById('wl-ps-login-view');
    var listView = document.getElementById('wl-ps-list-view');
    var detailView = document.getElementById('wl-ps-detail-view');
    var listEl = document.getElementById('wl-ps-list');
    var emptyEl = document.getElementById('wl-ps-empty');
    var detailEl = document.getElementById('wl-ps-detail');
    var userEl = document.getElementById('wl-ps-current-user');

    function showView(name) {
        [loginView, listView, detailView].forEach(function (view) {
            if (!view) return;
            view.hidden = view.getAttribute('data-view') !== name;
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

    function escapeHtml(str) {
        var div = document.createElement('div');
        div.textContent = str || '';
        return div.innerHTML;
    }

    function post(action, data) {
        var body = new FormData();
        body.append('action', action);
        body.append('nonce', weblazemProjectStatus.nonce);
        Object.keys(data || {}).forEach(function (key) {
            body.append(key, data[key]);
        });

        return fetch(weblazemProjectStatus.ajaxUrl, {
            method: 'POST',
            credentials: 'same-origin',
            body: body
        }).then(function (response) {
            return response.json().then(function (json) {
                return { ok: response.ok, json: json };
            });
        });
    }

    function enterDashboard(mobile, projects) {
        state.mobile = mobile || '';
        state.projects = projects || [];
        if (userEl) userEl.textContent = state.mobile;
        renderList();
        showView('list');
    }

    function renderList() {
        if (!listEl) return;
        listEl.innerHTML = '';

        if (!state.projects.length) {
            if (emptyEl) {
                emptyEl.hidden = false;
                emptyEl.textContent = weblazemProjectStatus.emptyMessage || emptyEl.textContent;
            }
            return;
        }

        if (emptyEl) emptyEl.hidden = true;

        state.projects.forEach(function (project) {
            var btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'wl-ps__card';
            btn.innerHTML =
                '<p class="wl-ps__card-title"></p>' +
                '<p class="wl-ps__card-meta"></p>' +
                '<div class="wl-ps__card-bar"><span></span></div>';
            btn.querySelector('.wl-ps__card-title').textContent = project.title || '';
            btn.querySelector('.wl-ps__card-meta').textContent =
                (project.code || '') + ' · ' + (project.stageLabel || '') + ' · ' + (project.progress || 0) + '%';
            btn.querySelector('.wl-ps__card-bar > span').style.width = (project.progress || 0) + '%';
            btn.addEventListener('click', function () {
                openProject(project.id);
            });
            listEl.appendChild(btn);
        });
    }

    function renderDetail(project) {
        if (!detailEl || !project) return;

        var stagesHtml = '';
        (project.stages || []).forEach(function (stage) {
            var cls = 'wl-ps__timeline-item';
            if (stage.done) cls += ' is-done';
            if (stage.key === project.stage) cls += ' is-current';
            stagesHtml +=
                '<li class="' + cls + '">' +
                    '<p class="wl-ps__timeline-label">' + escapeHtml(stage.label || stage.key) + '</p>' +
                    '<p class="wl-ps__timeline-meta">' +
                        escapeHtml(stage.date || '') +
                        (stage.note ? ' — ' + escapeHtml(stage.note) : '') +
                    '</p>' +
                '</li>';
        });

        var filesHtml = '';
        if (project.files && project.files.length) {
            filesHtml = '<h3 class="wl-ps__files-title">فایل‌ها و لینک‌ها</h3><div class="wl-ps__files">';
            project.files.forEach(function (file) {
                if (!file.url) return;
                filesHtml +=
                    '<a class="wl-ps__file" href="' + escapeHtml(file.url) + '" target="_blank" rel="noopener noreferrer">' +
                        '<i class="fas fa-file-alt" aria-hidden="true"></i> ' + escapeHtml(file.title || 'فایل') +
                    '</a>';
            });
            filesHtml += '</div>';
        }

        detailEl.innerHTML =
            '<h2 class="wl-ps__detail-title">' + escapeHtml(project.title) + '</h2>' +
            '<p class="wl-ps__detail-code">' + escapeHtml(project.code || '') +
                (project.clientName ? ' · ' + escapeHtml(project.clientName) : '') +
            '</p>' +
            '<div class="wl-ps__progress-wrap">' +
                '<div class="wl-ps__progress-label"><span>پیشرفت پروژه</span><span>' + (project.progress || 0) + '%</span></div>' +
                '<div class="wl-ps__progress"><span style="width:' + (project.progress || 0) + '%"></span></div>' +
            '</div>' +
            '<ul class="wl-ps__timeline">' + stagesHtml + '</ul>' +
            filesHtml;

        showView('detail');
    }

    function openProject(id) {
        state.currentId = id;
        post('weblazem_project_status_get', { project_id: id }).then(function (res) {
            if (!res.json || !res.json.success) {
                return;
            }
            renderDetail(res.json.data.project);
        });
    }

    var loginForm = document.getElementById('wl-ps-login-form');
    if (loginForm) {
        loginForm.addEventListener('submit', function (e) {
            e.preventDefault();
            var mobile = document.getElementById('wl-ps-mobile').value.trim();
            var code = document.getElementById('wl-ps-code').value.trim();
            setFeedback('wl-ps-login-feedback', 'در حال ورود…', '');

            post('weblazem_project_status_login', {
                mobile: mobile,
                access_code: code
            }).then(function (res) {
                if (!res.json || !res.json.success) {
                    var msg = (res.json && res.json.data && res.json.data.message) || weblazemProjectStatus.errorMessage;
                    setFeedback('wl-ps-login-feedback', msg, 'error');
                    return;
                }
                setFeedback('wl-ps-login-feedback', '', '');
                enterDashboard(res.json.data.mobile, res.json.data.projects);
            }).catch(function () {
                setFeedback('wl-ps-login-feedback', weblazemProjectStatus.errorMessage, 'error');
            });
        });
    }

    var logoutBtn = document.getElementById('wl-ps-logout');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', function () {
            post('weblazem_project_status_logout', {}).then(function () {
                state.mobile = '';
                state.projects = [];
                showView('login');
            });
        });
    }

    var backBtn = document.getElementById('wl-ps-back');
    if (backBtn) {
        backBtn.addEventListener('click', function () {
            showView('list');
        });
    }

    post('weblazem_project_status_session', {}).then(function (res) {
        if (res.json && res.json.success && res.json.data && res.json.data.loggedIn) {
            enterDashboard(res.json.data.mobile, res.json.data.projects);
        } else {
            showView('login');
        }
    }).catch(function () {
        showView('login');
    });
})();
