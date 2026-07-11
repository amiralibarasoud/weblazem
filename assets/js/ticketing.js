(function () {
    'use strict';

    var panel = document.getElementById('weblazem-ticket-panel');
    if (!panel || typeof weblazemTicket === 'undefined') {
        return;
    }

    var state = {
        username: '',
        tickets: [],
        currentTicketId: null
    };

    var loginView = document.getElementById('weblazem-ticket-login-view');
    var dashView = document.getElementById('weblazem-ticket-dash-view');
    var createView = document.getElementById('weblazem-ticket-create-view');
    var detailView = document.getElementById('weblazem-ticket-detail-view');
    var listEl = document.getElementById('weblazem-ticket-list');
    var emptyEl = document.getElementById('weblazem-ticket-empty');
    var userEl = document.getElementById('weblazem-ticket-current-user');
    var chatEl = document.getElementById('weblazem-ticket-chat');
    var successModal = document.getElementById('weblazem-ticket-success');

    function showView(name) {
        [loginView, dashView, createView, detailView].forEach(function (view) {
            if (!view) return;
            view.hidden = view.getAttribute('data-view') !== name;
        });
    }

    function setFeedback(el, message, type) {
        if (!el) return;
        el.textContent = message || '';
        el.classList.remove('is-error', 'is-success');
        if (type) {
            el.classList.add(type === 'success' ? 'is-success' : 'is-error');
        }
    }

    function showSuccess(message, code) {
        if (!successModal) return;
        document.getElementById('weblazem-ticket-success-message').textContent = message || weblazemTicket.successMessage;
        document.getElementById('weblazem-ticket-success-code').textContent = code ? ('کد پیگیری: ' + code) : '';
        successModal.hidden = false;
        successModal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('weblazem-ticket-success-open');
    }

    function hideSuccess() {
        if (!successModal) return;
        successModal.hidden = true;
        successModal.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('weblazem-ticket-success-open');
    }

    function validateFile(input) {
        if (!input || !input.files || !input.files[0]) {
            return true;
        }
        var file = input.files[0];
        var max = (weblazemTicket.maxUploadMb || 3) * 1024 * 1024;
        if (file.size > max) {
            return 'حجم فایل نباید بیشتر از ۳ مگابایت باشد.';
        }
        return true;
    }

    function post(action, data, isMultipart) {
        var body;
        if (isMultipart) {
            body = data;
            if (!(body instanceof FormData)) {
                body = new FormData();
            }
            body.append('action', action);
            body.append('nonce', weblazemTicket.nonce);
        } else {
            body = new FormData();
            body.append('action', action);
            body.append('nonce', weblazemTicket.nonce);
            Object.keys(data || {}).forEach(function (key) {
                body.append(key, data[key]);
            });
        }

        return fetch(weblazemTicket.ajaxUrl, {
            method: 'POST',
            credentials: 'same-origin',
            body: body
        }).then(function (response) {
            return response.json().then(function (json) {
                return { ok: response.ok, json: json };
            });
        });
    }

    function statusClass(status) {
        return 'weblazem-ticket-status weblazem-ticket-status--' + (status || 'open');
    }

    function escapeHtml(str) {
        var div = document.createElement('div');
        div.textContent = str || '';
        return div.innerHTML;
    }

    function renderAttachments(attachments) {
        if (!attachments || !attachments.length) {
            return '';
        }
        return attachments.map(function (file) {
            var name = escapeHtml(file.name || 'پیوست');
            var url = escapeHtml(file.url || '#');
            return '<a class="weblazem-ticket-attach" href="' + url + '" target="_blank" rel="noopener noreferrer"><i class="fas fa-paperclip"></i> ' + name + '</a>';
        }).join('');
    }

    function renderTickets() {
        if (!listEl) return;
        listEl.innerHTML = '';

        if (!state.tickets.length) {
            if (emptyEl) emptyEl.hidden = false;
            return;
        }

        if (emptyEl) emptyEl.hidden = true;

        state.tickets.forEach(function (ticket) {
            var btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'weblazem-ticket-card';
            btn.innerHTML =
                '<div>' +
                    '<p class="weblazem-ticket-card__title"></p>' +
                    '<div class="weblazem-ticket-card__meta"></div>' +
                '</div>' +
                '<span class="' + statusClass(ticket.status) + '"></span>';

            btn.querySelector('.weblazem-ticket-card__title').textContent = ticket.title;
            btn.querySelector('.weblazem-ticket-card__meta').textContent =
                (ticket.code || '') + ' · ' + (ticket.subjectLabel || '') + ' · ' + (ticket.updatedAt || '');
            btn.querySelector('span').textContent = ticket.statusLabel || ticket.status;
            btn.addEventListener('click', function () {
                openTicket(ticket.id);
            });
            listEl.appendChild(btn);
        });
    }

    function renderChat(ticket) {
        if (!chatEl) return;
        chatEl.innerHTML = '';

        (ticket.replies || []).forEach(function (reply) {
            var bubble = document.createElement('div');
            var isAdmin = reply.author_type === 'admin';
            bubble.className = 'weblazem-ticket-chat__bubble ' + (isAdmin ? 'is-admin' : 'is-user');
            bubble.innerHTML =
                '<div class="weblazem-ticket-chat__meta"><strong></strong><span></span></div>' +
                '<div class="weblazem-ticket-chat__text"></div>' +
                '<div class="weblazem-ticket-chat__files"></div>';

            bubble.querySelector('strong').textContent = reply.author_name || (isAdmin ? 'پشتیبانی' : 'شما');
            bubble.querySelector('span').textContent = reply.created_at || '';
            bubble.querySelector('.weblazem-ticket-chat__text').textContent = reply.message || '';
            bubble.querySelector('.weblazem-ticket-chat__files').innerHTML = renderAttachments(reply.attachments || []);
            chatEl.appendChild(bubble);
        });

        chatEl.scrollTop = chatEl.scrollHeight;
    }

    function openTicket(ticketId) {
        post('weblazem_ticket_get', { ticket_id: ticketId }).then(function (result) {
            if (!result.ok || !result.json.success) {
                return;
            }

            var ticket = result.json.data.ticket;
            state.currentTicketId = ticket.id;

            document.getElementById('weblazem-ticket-detail-title').textContent = ticket.title;
            document.getElementById('weblazem-ticket-detail-meta').textContent =
                (ticket.code || '') + ' · ' + (ticket.subjectLabel || '') + ' · اولویت: ' + (ticket.priorityLabel || '');

            var statusEl = document.getElementById('weblazem-ticket-detail-status');
            statusEl.className = statusClass(ticket.status);
            statusEl.textContent = ticket.statusLabel || ticket.status;

            renderChat(ticket);
            showView('detail');

            var replyForm = document.getElementById('weblazem-ticket-reply-form');
            if (replyForm) {
                replyForm.hidden = ticket.status === 'closed';
            }
        });
    }

    function enterDashboard(username, tickets) {
        state.username = username;
        state.tickets = tickets || [];
        if (userEl) userEl.textContent = username;
        renderTickets();
        showView('dash');
    }

    document.getElementById('weblazem-ticket-login-form').addEventListener('submit', function (event) {
        event.preventDefault();
        var feedback = document.getElementById('weblazem-ticket-login-feedback');
        var form = event.currentTarget;
        var mobile = form.mobile.value.trim();
        var code = form.access_code.value.trim();

        setFeedback(feedback, 'در حال ورود...', null);

        post('weblazem_ticket_login', {
            mobile: mobile,
            access_code: code
        }).then(function (result) {
            if (!result.ok || !result.json.success) {
                var msg = (result.json.data && result.json.data.message) || weblazemTicket.errorMessage;
                setFeedback(feedback, msg, 'error');
                return;
            }
            setFeedback(feedback, '', null);
            form.reset();
            enterDashboard(result.json.data.mobile || result.json.data.username, result.json.data.tickets);
        }).catch(function () {
            setFeedback(feedback, weblazemTicket.errorMessage, 'error');
        });
    });

    document.getElementById('weblazem-ticket-logout-btn').addEventListener('click', function () {
        post('weblazem_ticket_logout', {}).then(function () {
            state.username = '';
            state.tickets = [];
            state.currentTicketId = null;
            showView('login');
        });
    });

    document.getElementById('weblazem-ticket-new-btn').addEventListener('click', function () {
        if (!state.username) {
            showView('login');
            setFeedback(document.getElementById('weblazem-ticket-login-feedback'), weblazemTicket.loginRequired, 'error');
            return;
        }
        document.getElementById('weblazem-ticket-create-form').reset();
        setFeedback(document.getElementById('weblazem-ticket-create-feedback'), '', null);
        showView('create');
    });

    panel.querySelectorAll('[data-ticket-back]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            renderTickets();
            showView('dash');
        });
    });

    document.getElementById('weblazem-ticket-create-form').addEventListener('submit', function (event) {
        event.preventDefault();

        if (!state.username) {
            showView('login');
            setFeedback(document.getElementById('weblazem-ticket-login-feedback'), weblazemTicket.loginRequired, 'error');
            return;
        }

        var form = event.currentTarget;
        var feedback = document.getElementById('weblazem-ticket-create-feedback');
        var fileCheck = validateFile(form.attachment);
        if (fileCheck !== true) {
            setFeedback(feedback, fileCheck, 'error');
            return;
        }

        var submitBtn = document.getElementById('weblazem-ticket-create-submit');
        if (submitBtn) submitBtn.disabled = true;
        setFeedback(feedback, 'در حال ثبت...', null);

        var body = new FormData(form);

        post('weblazem_ticket_create', body, true).then(function (result) {
            if (submitBtn) submitBtn.disabled = false;

            if (!result.ok || !result.json.success) {
                var msg = (result.json.data && result.json.data.message) || weblazemTicket.errorMessage;
                setFeedback(feedback, msg, 'error');
                return;
            }

            state.tickets = result.json.data.tickets || [];
            form.reset();
            setFeedback(feedback, '', null);
            showSuccess(result.json.data.message, result.json.data.ticket && result.json.data.ticket.code);

            window.setTimeout(function () {
                hideSuccess();
                openTicket(result.json.data.ticket.id);
            }, 2200);
        }).catch(function () {
            if (submitBtn) submitBtn.disabled = false;
            setFeedback(feedback, weblazemTicket.errorMessage, 'error');
        });
    });

    document.getElementById('weblazem-ticket-reply-form').addEventListener('submit', function (event) {
        event.preventDefault();
        var form = event.currentTarget;
        var feedback = document.getElementById('weblazem-ticket-reply-feedback');

        if (!state.currentTicketId || !state.username) {
            return;
        }

        var fileCheck = validateFile(form.attachment);
        if (fileCheck !== true) {
            setFeedback(feedback, fileCheck, 'error');
            return;
        }

        setFeedback(feedback, 'در حال ارسال...', null);

        var body = new FormData(form);
        body.append('ticket_id', state.currentTicketId);

        post('weblazem_ticket_reply', body, true).then(function (result) {
            if (!result.ok || !result.json.success) {
                var msg = (result.json.data && result.json.data.message) || weblazemTicket.errorMessage;
                setFeedback(feedback, msg, 'error');
                return;
            }

            form.reset();
            setFeedback(feedback, '', null);
            renderChat(result.json.data.ticket);

            var statusEl = document.getElementById('weblazem-ticket-detail-status');
            statusEl.className = statusClass(result.json.data.ticket.status);
            statusEl.textContent = result.json.data.ticket.statusLabel;

            var updated = result.json.data.ticket;
            state.tickets = state.tickets.map(function (t) {
                return t.id === updated.id ? updated : t;
            });
        }).catch(function () {
            setFeedback(feedback, weblazemTicket.errorMessage, 'error');
        });
    });

    var successClose = document.getElementById('weblazem-ticket-success-close');
    if (successClose) {
        successClose.addEventListener('click', hideSuccess);
    }
    if (successModal) {
        successModal.addEventListener('click', function (event) {
            if (event.target === successModal) {
                hideSuccess();
            }
        });
    }

    post('weblazem_ticket_session', {}).then(function (result) {
        if (result.ok && result.json.success && result.json.data.loggedIn) {
            enterDashboard(result.json.data.mobile || result.json.data.username, result.json.data.tickets);
        } else {
            showView('login');
        }
    }).catch(function () {
        showView('login');
    });
})();
