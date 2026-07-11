(function ($) {
    'use strict';

    var $form = $('#weblazem-admin-ticket-reply-form');
    if (!$form.length || typeof weblazemTicketAdmin === 'undefined') {
        return;
    }

    var ticketId = $('#weblazem-admin-ticket-chat').data('ticket-id');
    var $chat = $('#weblazem-admin-ticket-chat');
    var $feedback = $('#weblazem-admin-ticket-feedback');

    function appendBubble(reply) {
        var isAdmin = reply.author_type === 'admin';
        var $bubble = $('<div>', {
            class: 'weblazem-ticket-chat__bubble ' + (isAdmin ? 'is-admin' : 'is-user')
        });

        var $meta = $('<div>', { class: 'weblazem-ticket-chat__meta' });
        $meta.append($('<strong>').text(reply.author_name || (isAdmin ? 'پشتیبانی' : 'کاربر')));
        $meta.append($('<span>').text(reply.created_at || ''));

        var $text = $('<div>', { class: 'weblazem-ticket-chat__text' }).text(reply.message || '');

        $bubble.append($meta).append($text);
        $chat.append($bubble);
        $chat.scrollTop($chat[0].scrollHeight);
    }

    $form.on('submit', function (event) {
        event.preventDefault();

        var message = $.trim($form.find('[name="message"]').val());
        var status = $form.find('[name="status"]').val();

        $feedback.text('در حال ارسال...');

        $.post(weblazemTicketAdmin.ajaxUrl, {
            action: 'weblazem_ticket_admin_reply',
            nonce: weblazemTicketAdmin.nonce,
            ticket_id: ticketId,
            message: message,
            status: status
        }).done(function (response) {
            if (!response || !response.success) {
                $feedback.text((response && response.data && response.data.message) || 'خطا در ارسال پاسخ');
                return;
            }

            var ticket = response.data.ticket;
            $chat.empty();
            (ticket.replies || []).forEach(appendBubble);
            $form.find('[name="message"]').val('');
            $feedback.text(response.data.message || 'ثبت شد');

            $('.weblazem-ticket-admin__chat-head .weblazem-ticket-status')
                .attr('class', 'weblazem-ticket-status weblazem-ticket-status--' + ticket.status)
                .text(ticket.statusLabel);
        }).fail(function () {
            $feedback.text('خطا در ارتباط با سرور');
        });
    });
})(jQuery);
