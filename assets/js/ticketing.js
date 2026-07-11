(function () {
    'use strict';

    var panel = document.getElementById('weblazem-ticket-panel');
    if (!panel || typeof weblazemTicket === 'undefined') {
        return;
    }

    var state = {
        username: '',
        tickets: [],
        briefs: [],
        proposals: [],
        projects: [],
        counts: {},
        currentTicketId: null,
        currentProposalId: null,
        activeTab: 'overview'
    };

    var loginView = document.getElementById('weblazem-ticket-login-view');
    var dashView = document.getElementById('weblazem-ticket-dash-view');
    var createView = document.getElementById('weblazem-ticket-create-view');
    var detailView = document.getElementById('weblazem-ticket-detail-view');
    var proposalView = document.getElementById('weblazem-proposal-detail-view');
    var projectView = document.getElementById('weblazem-project-detail-view');
    var listEl = document.getElementById('weblazem-ticket-list');
    var emptyEl = document.getElementById('weblazem-ticket-empty');
    var userEl = document.getElementById('weblazem-ticket-current-user');
    var chatEl = document.getElementById('weblazem-ticket-chat');
    var successModal = document.getElementById('weblazem-ticket-success');

    function showView(name) {
        [loginView, dashView, createView, detailView, proposalView, projectView].forEach(function (view) {
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

    function proposalStatusClass(status) {
        return 'weblazem-ticket-status weblazem-proposal-status--' + (status || 'sent');
    }

    function escapeHtml(str) {
        var div = document.createElement('div');
        div.textContent = str || '';
        return div.innerHTML;
    }

    function applyDashboard(data) {
        state.username = data.mobile || data.username || state.username;
        state.tickets = data.tickets || [];
        state.briefs = data.briefs || [];
        state.proposals = data.proposals || [];
        state.projects = data.projects || [];
        state.counts = data.counts || {
            tickets: state.tickets.length,
            briefs: state.briefs.length,
            proposals: state.proposals.length,
            projects: state.projects.length
        };
        if (userEl) userEl.textContent = state.username;
        renderAll();
    }

    function loadDashboard() {
        return post('weblazem_client_dashboard', {}).then(function (result) {
            if (!result.ok || !result.json.success) {
                return false;
            }
            applyDashboard(result.json.data);
            return true;
        });
    }

    function setTab(tab) {
        state.activeTab = tab || 'overview';
        panel.querySelectorAll('.weblazem-account-tab').forEach(function (btn) {
            btn.classList.toggle('is-active', btn.getAttribute('data-account-tab') === state.activeTab);
        });
        panel.querySelectorAll('.weblazem-account-pane').forEach(function (pane) {
            var match = pane.getAttribute('data-pane') === state.activeTab;
            pane.hidden = !match;
            pane.classList.toggle('is-active', match);
        });
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
            listEl.appendChild(buildTicketCard(ticket));
        });
    }

    function buildTicketCard(ticket) {
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
        return btn;
    }

    function renderBriefs() {
        var list = document.getElementById('weblazem-account-briefs-list');
        var empty = document.getElementById('weblazem-account-briefs-empty');
        if (!list) return;
        list.innerHTML = '';

        if (!state.briefs.length) {
            if (empty) empty.hidden = false;
            return;
        }
        if (empty) empty.hidden = true;

        state.briefs.forEach(function (brief) {
            var card = document.createElement('div');
            card.className = 'weblazem-account-card';
            card.innerHTML =
                '<div class="weblazem-account-card__body">' +
                    '<div class="weblazem-account-card__row">' +
                        '<p class="weblazem-account-card__title"></p>' +
                        '<span class="weblazem-ticket-status"></span>' +
                    '</div>' +
                    '<p class="weblazem-account-card__meta"></p>' +
                    '<p class="weblazem-account-card__desc"></p>' +
                    '<div class="weblazem-account-card__actions"></div>' +
                '</div>';
            card.querySelector('.weblazem-account-card__title').textContent = brief.projectLabel || brief.title || 'بریف پروژه';
            card.querySelector('.weblazem-ticket-status').textContent = brief.statusLabel || brief.status || 'در انتظار بررسی';
            card.querySelector('.weblazem-account-card__meta').textContent =
                (brief.budgetLabel || '') + (brief.deadline ? ' · مهلت: ' + brief.deadline : '') + (brief.createdAt ? ' · ' + brief.createdAt : '');
            card.querySelector('.weblazem-account-card__desc').textContent = brief.goal || '';
            if (brief.projectId) {
                var go = document.createElement('button');
                go.type = 'button';
                go.className = 'weblazem-account-link';
                go.textContent = 'مشاهده پروژه';
                go.addEventListener('click', function () {
                    var project = state.projects.find(function (p) { return String(p.id) === String(brief.projectId); });
                    if (project) {
                        openProject(project);
                    } else {
                        setTab('projects');
                    }
                });
                card.querySelector('.weblazem-account-card__actions').appendChild(go);
            }
            list.appendChild(card);
        });
    }

    function renderProposals() {
        var list = document.getElementById('weblazem-account-proposals-list');
        var empty = document.getElementById('weblazem-account-proposals-empty');
        if (!list) return;
        list.innerHTML = '';

        if (!state.proposals.length) {
            if (empty) empty.hidden = false;
            return;
        }
        if (empty) empty.hidden = true;

        state.proposals.forEach(function (proposal) {
            list.appendChild(buildProposalCard(proposal));
        });
    }

    function buildProposalCard(proposal) {
        var btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'weblazem-ticket-card weblazem-proposal-card';
        btn.innerHTML =
            '<div>' +
                '<p class="weblazem-ticket-card__title"></p>' +
                '<div class="weblazem-ticket-card__meta"></div>' +
                '<div class="weblazem-proposal-card__total"></div>' +
            '</div>' +
            '<span class="' + proposalStatusClass(proposal.status) + '"></span>';

        btn.querySelector('.weblazem-ticket-card__title').textContent = proposal.title;
        btn.querySelector('.weblazem-ticket-card__meta').textContent =
            (proposal.code || '') + (proposal.deliveryDays ? ' · تحویل ' + proposal.deliveryDays + ' روزه' : '') + (proposal.sentAt ? ' · ' + proposal.sentAt : '');
        btn.querySelector('.weblazem-proposal-card__total').textContent = proposal.totalLabel || '';
        btn.querySelector('span').textContent = proposal.statusLabel || proposal.status;
        btn.addEventListener('click', function () {
            openProposal(proposal.id);
        });
        return btn;
    }

    function renderProjects() {
        var list = document.getElementById('weblazem-account-projects-list');
        var empty = document.getElementById('weblazem-account-projects-empty');
        if (!list) return;
        list.innerHTML = '';

        if (!state.projects.length) {
            if (empty) empty.hidden = false;
            return;
        }
        if (empty) empty.hidden = true;

        state.projects.forEach(function (project) {
            var btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'weblazem-ticket-card weblazem-project-card';
            var progress = Math.max(0, Math.min(100, parseInt(project.progress, 10) || 0));
            btn.innerHTML =
                '<div>' +
                    '<p class="weblazem-ticket-card__title"></p>' +
                    '<div class="weblazem-ticket-card__meta"></div>' +
                    '<div class="weblazem-project-progress"><span style="width:' + progress + '%"></span></div>' +
                    '<p class="weblazem-account-card__desc"></p>' +
                '</div>' +
                '<span class="weblazem-ticket-status"></span>';
            btn.querySelector('.weblazem-ticket-card__title').textContent = project.title;
            btn.querySelector('.weblazem-ticket-card__meta').textContent =
                (project.code || '') + (project.updatedAt ? ' · ' + project.updatedAt : '');
            btn.querySelector('.weblazem-account-card__desc').textContent =
                (project.statusLabel || 'در حال انجام') + ' · پیشرفت ' + progress + '٪';
            btn.querySelector('.weblazem-ticket-status').textContent = project.stageLabel || project.stage || '';
            btn.addEventListener('click', function () {
                openProject(project);
            });
            list.appendChild(btn);
        });
    }

    function openProject(project) {
        state.currentProjectId = project.id;
        document.getElementById('weblazem-project-detail-code').textContent = project.code || '';
        document.getElementById('weblazem-project-detail-title').textContent = project.title || '';
        document.getElementById('weblazem-project-detail-meta').textContent =
            (project.statusLabel || 'در حال انجام') +
            (project.updatedAt ? ' · آخرین بروزرسانی: ' + project.updatedAt : '');

        var statusEl = document.getElementById('weblazem-project-detail-status');
        statusEl.textContent = project.stageLabel || project.stage || '';
        statusEl.className = 'weblazem-ticket-status';

        var progress = Math.max(0, Math.min(100, parseInt(project.progress, 10) || 0));
        document.getElementById('weblazem-project-detail-progress-text').textContent = progress + '٪';
        document.getElementById('weblazem-project-detail-progress-bar').style.width = progress + '%';

        var stagesEl = document.getElementById('weblazem-project-detail-stages');
        stagesEl.innerHTML = '';
        (project.stages || []).forEach(function (stage) {
            var li = document.createElement('li');
            li.className = 'weblazem-project-stages__item' + (stage.done ? ' is-done' : '');
            li.innerHTML =
                '<div class="weblazem-project-stages__dot" aria-hidden="true"></div>' +
                '<div class="weblazem-project-stages__body">' +
                    '<strong></strong>' +
                    '<span class="weblazem-project-stages__date"></span>' +
                    '<p class="weblazem-project-stages__note"></p>' +
                '</div>';
            li.querySelector('strong').textContent = stage.label || stage.key || '';
            li.querySelector('.weblazem-project-stages__date').textContent = stage.date || (stage.done ? 'انجام شده' : 'در انتظار');
            var note = li.querySelector('.weblazem-project-stages__note');
            if (stage.note) {
                note.textContent = stage.note;
            } else {
                note.hidden = true;
            }
            stagesEl.appendChild(li);
        });

        var filesWrap = document.getElementById('weblazem-project-detail-files-wrap');
        var filesEl = document.getElementById('weblazem-project-detail-files');
        filesEl.innerHTML = '';
        if (project.files && project.files.length) {
            filesWrap.hidden = false;
            project.files.forEach(function (file) {
                var a = document.createElement('a');
                a.className = 'weblazem-ticket-attach';
                a.href = file.url || '#';
                a.target = '_blank';
                a.rel = 'noopener noreferrer';
                a.innerHTML = '<i class="fas fa-paperclip" aria-hidden="true"></i> ' + escapeHtml(file.title || 'فایل');
                filesEl.appendChild(a);
            });
        } else {
            filesWrap.hidden = true;
        }

        showView('project');
    }

    function renderOverview() {
        var stats = document.getElementById('weblazem-account-stats');
        if (stats) {
            var c = state.counts || {};
            stats.innerHTML =
                '<button type="button" class="weblazem-account-stat" data-goto-tab="tickets">' +
                    '<strong>' + (c.tickets || 0) + '</strong><span>تیکت</span></button>' +
                '<button type="button" class="weblazem-account-stat" data-goto-tab="briefs">' +
                    '<strong>' + (c.briefs || 0) + '</strong><span>بریف</span></button>' +
                '<button type="button" class="weblazem-account-stat" data-goto-tab="proposals">' +
                    '<strong>' + (c.proposals || 0) + '</strong><span>پیشنهاد</span></button>' +
                '<button type="button" class="weblazem-account-stat" data-goto-tab="projects">' +
                    '<strong>' + (c.projects || 0) + '</strong><span>پروژه</span></button>';
        }

        var ovTickets = document.getElementById('weblazem-account-overview-tickets');
        var ovProposals = document.getElementById('weblazem-account-overview-proposals');
        var ovProjects = document.getElementById('weblazem-account-overview-projects');

        if (ovProjects) {
            ovProjects.innerHTML = '';
            var activeProjects = state.projects.filter(function (p) {
                return (p.status || 'active') !== 'done';
            });
            if (!activeProjects.length && !state.projects.length) {
                ovProjects.innerHTML = '<p class="weblazem-account-muted">هنوز پروژه‌ای شروع نشده است.</p>';
            } else {
                (activeProjects.length ? activeProjects : state.projects).slice(0, 3).forEach(function (project) {
                    var btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'weblazem-ticket-card weblazem-project-card weblazem-project-card--compact';
                    var progress = Math.max(0, Math.min(100, parseInt(project.progress, 10) || 0));
                    btn.innerHTML =
                        '<div>' +
                            '<p class="weblazem-ticket-card__title"></p>' +
                            '<div class="weblazem-ticket-card__meta"></div>' +
                            '<div class="weblazem-project-progress"><span style="width:' + progress + '%"></span></div>' +
                        '</div>' +
                        '<span class="weblazem-ticket-status"></span>';
                    btn.querySelector('.weblazem-ticket-card__title').textContent = project.title;
                    btn.querySelector('.weblazem-ticket-card__meta').textContent =
                        (project.code || '') + ' · پیشرفت ' + progress + '٪';
                    btn.querySelector('.weblazem-ticket-status').textContent = project.stageLabel || project.statusLabel || '';
                    btn.addEventListener('click', function () {
                        openProject(project);
                    });
                    ovProjects.appendChild(btn);
                });
            }
        }

        if (ovTickets) {
            ovTickets.innerHTML = '';
            if (!state.tickets.length) {
                ovTickets.innerHTML = '<p class="weblazem-account-muted">تیکتی نیست.</p>';
            } else {
                state.tickets.slice(0, 3).forEach(function (ticket) {
                    ovTickets.appendChild(buildTicketCard(ticket));
                });
            }
        }

        if (ovProposals) {
            ovProposals.innerHTML = '';
            if (!state.proposals.length) {
                ovProposals.innerHTML = '<p class="weblazem-account-muted">پیشنهادی نیست.</p>';
            } else {
                state.proposals.slice(0, 3).forEach(function (proposal) {
                    ovProposals.appendChild(buildProposalCard(proposal));
                });
            }
        }
    }

    function renderAll() {
        renderOverview();
        renderTickets();
        renderBriefs();
        renderProposals();
        renderProjects();
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

    function openProposal(proposalId) {
        post('weblazem_proposal_get', { proposal_id: proposalId }).then(function (result) {
            if (!result.ok || !result.json.success) {
                return;
            }

            var proposal = result.json.data.proposal;
            state.currentProposalId = proposal.id;
            renderProposalDetail(proposal);
            showView('proposal');

            // Refresh list status (viewed)
            state.proposals = state.proposals.map(function (p) {
                return p.id === proposal.id ? proposal : p;
            });
            renderProposals();
            renderOverview();
        });
    }

    function renderProposalDetail(proposal) {
        document.getElementById('weblazem-proposal-detail-code').textContent = proposal.code || '';
        document.getElementById('weblazem-proposal-detail-title').textContent = proposal.title || '';
        document.getElementById('weblazem-proposal-detail-meta').textContent =
            (proposal.deliveryDays ? 'تحویل در ' + proposal.deliveryDays + ' روز کاری' : '') +
            (proposal.sentAt ? ' · ارسال: ' + proposal.sentAt : '');

        var statusEl = document.getElementById('weblazem-proposal-detail-status');
        statusEl.className = proposalStatusClass(proposal.status);
        statusEl.textContent = proposal.statusLabel || proposal.status;

        var introEl = document.getElementById('weblazem-proposal-detail-intro');
        introEl.textContent = proposal.intro || '';
        introEl.hidden = !proposal.intro;

        var itemsEl = document.getElementById('weblazem-proposal-detail-items');
        itemsEl.innerHTML = (proposal.items || []).map(function (item) {
            return '<div class="weblazem-proposal-line">' +
                '<div><strong>' + escapeHtml(item.title || '') + '</strong>' +
                (item.description ? '<p>' + escapeHtml(item.description) + '</p>' : '') +
                '</div><span dir="ltr">' + escapeHtml(formatMaybeLabel(item.price)) + '</span></div>';
        }).join('');

        var totalsEl = document.getElementById('weblazem-proposal-detail-totals');
        totalsEl.innerHTML =
            '<div><span>جمع جزء</span><strong>' + escapeHtml(proposal.subtotalLabel || '') + '</strong></div>' +
            (proposal.discount ? '<div><span>تخفیف</span><strong>' + escapeHtml(proposal.discountLabel || '') + '</strong></div>' : '') +
            '<div class="is-total"><span>مبلغ نهایی</span><strong>' + escapeHtml(proposal.totalLabel || '') + '</strong></div>';

        var termsEl = document.getElementById('weblazem-proposal-detail-terms');
        termsEl.textContent = proposal.terms || '';

        var actions = document.getElementById('weblazem-proposal-detail-actions');
        var changesBox = document.getElementById('weblazem-proposal-changes-box');
        var canRespond = !!proposal.canRespond;
        if (actions) actions.hidden = !canRespond;
        if (changesBox) changesBox.hidden = true;
        setFeedback(document.getElementById('weblazem-proposal-action-feedback'), '', null);
        setFeedback(document.getElementById('weblazem-proposal-result-feedback'), '', null);

        if (proposal.clientNote && proposal.status === 'changes_requested') {
            setFeedback(
                document.getElementById('weblazem-proposal-result-feedback'),
                'درخواست تغییر شما: ' + proposal.clientNote,
                'success'
            );
        }
        if (proposal.status === 'accepted') {
            setFeedback(
                document.getElementById('weblazem-proposal-result-feedback'),
                'این پیشنهاد پذیرفته شده است.',
                'success'
            );
        }
    }

    function formatMaybeLabel(price) {
        var n = parseInt(price, 10) || 0;
        try {
            return n.toLocaleString('fa-IR') + ' تومان';
        } catch (e) {
            return n + ' تومان';
        }
    }

    function enterDashboard(username, tickets) {
        state.username = username;
        state.tickets = tickets || [];
        if (userEl) userEl.textContent = username;
        showView('dash');
        setTab('overview');
        loadDashboard().catch(function () {
            renderAll();
        });
    }

    function goBackToAccount(tab) {
        showView('dash');
        setTab(tab || state.activeTab || 'overview');
        renderAll();
    }

    /* Events */

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
            state.briefs = [];
            state.proposals = [];
            state.projects = [];
            state.currentTicketId = null;
            state.currentProposalId = null;
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
            var tab = btn.getAttribute('data-back-to') || 'tickets';
            goBackToAccount(tab);
        });
    });

    panel.addEventListener('click', function (event) {
        var tabBtn = event.target.closest('[data-account-tab]');
        if (tabBtn) {
            setTab(tabBtn.getAttribute('data-account-tab'));
            return;
        }
        var goto = event.target.closest('[data-goto-tab]');
        if (goto) {
            setTab(goto.getAttribute('data-goto-tab'));
        }
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
            loadDashboard();

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

    var acceptBtn = document.getElementById('weblazem-proposal-accept-btn');
    if (acceptBtn) {
        acceptBtn.addEventListener('click', function () {
            if (!state.currentProposalId) return;
            if (!window.confirm('آیا از پذیرش این پیشنهاد مطمئن هستید؟')) return;

            var feedback = document.getElementById('weblazem-proposal-result-feedback');
            setFeedback(feedback, 'در حال ثبت...', null);
            acceptBtn.disabled = true;

            post('weblazem_proposal_accept', { proposal_id: state.currentProposalId }).then(function (result) {
                acceptBtn.disabled = false;
                if (!result.ok || !result.json.success) {
                    var msg = (result.json.data && result.json.data.message) || weblazemTicket.errorMessage;
                    setFeedback(feedback, msg, 'error');
                    return;
                }
                renderProposalDetail(result.json.data.proposal);
                setFeedback(feedback, result.json.data.message, 'success');
                loadDashboard();
            }).catch(function () {
                acceptBtn.disabled = false;
                setFeedback(feedback, weblazemTicket.errorMessage, 'error');
            });
        });
    }

    var changesBtn = document.getElementById('weblazem-proposal-changes-btn');
    var changesBox = document.getElementById('weblazem-proposal-changes-box');
    var changesCancel = document.getElementById('weblazem-proposal-changes-cancel');
    var changesSubmit = document.getElementById('weblazem-proposal-changes-submit');

    if (changesBtn && changesBox) {
        changesBtn.addEventListener('click', function () {
            changesBox.hidden = false;
            var note = document.getElementById('weblazem-proposal-changes-note');
            if (note) note.focus();
        });
    }
    if (changesCancel && changesBox) {
        changesCancel.addEventListener('click', function () {
            changesBox.hidden = true;
            setFeedback(document.getElementById('weblazem-proposal-action-feedback'), '', null);
        });
    }
    if (changesSubmit) {
        changesSubmit.addEventListener('click', function () {
            if (!state.currentProposalId) return;
            var noteEl = document.getElementById('weblazem-proposal-changes-note');
            var note = noteEl ? noteEl.value.trim() : '';
            var feedback = document.getElementById('weblazem-proposal-action-feedback');

            if (!note) {
                setFeedback(feedback, 'لطفاً توضیحات درخواست تغییر را بنویسید.', 'error');
                return;
            }

            setFeedback(feedback, 'در حال ارسال...', null);
            changesSubmit.disabled = true;

            post('weblazem_proposal_request_changes', {
                proposal_id: state.currentProposalId,
                note: note
            }).then(function (result) {
                changesSubmit.disabled = false;
                if (!result.ok || !result.json.success) {
                    var msg = (result.json.data && result.json.data.message) || weblazemTicket.errorMessage;
                    setFeedback(feedback, msg, 'error');
                    return;
                }
                if (noteEl) noteEl.value = '';
                if (changesBox) changesBox.hidden = true;
                renderProposalDetail(result.json.data.proposal);
                setFeedback(document.getElementById('weblazem-proposal-result-feedback'), result.json.data.message, 'success');
                loadDashboard();
            }).catch(function () {
                changesSubmit.disabled = false;
                setFeedback(feedback, weblazemTicket.errorMessage, 'error');
            });
        });
    }

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
