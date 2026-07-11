<?php
/**
 * Ticketing sandbox auth + AJAX endpoints.
 */

function weblazem_ticket_start_session() {
    if (headers_sent()) {
        return;
    }
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}
add_action('init', 'weblazem_ticket_start_session', 1);

function weblazem_ticket_get_session_user() {
    weblazem_ticket_start_session();
    if (empty($_SESSION['weblazem_ticket_user'])) {
        return '';
    }
    return sanitize_text_field($_SESSION['weblazem_ticket_user']);
}

function weblazem_ticket_set_session_user($username) {
    weblazem_ticket_start_session();
    $_SESSION['weblazem_ticket_user'] = sanitize_text_field($username);
}

function weblazem_ticket_clear_session_user() {
    weblazem_ticket_start_session();
    unset($_SESSION['weblazem_ticket_user']);
}

function weblazem_ajax_ticket_login() {
    check_ajax_referer('weblazem_ticketing', 'nonce');

    $username = isset($_POST['username']) ? sanitize_text_field(wp_unslash($_POST['username'])) : '';
    $code     = isset($_POST['access_code']) ? sanitize_text_field(wp_unslash($_POST['access_code'])) : '';

    if ($username === '' || $code === '') {
        wp_send_json_error(array('message' => 'نام کاربری و کد ورود الزامی است.'), 400);
    }

    if ($code !== weblazem_get_ticket_access_code()) {
        wp_send_json_error(array('message' => 'کد ورود نامعتبر است.'), 403);
    }

    weblazem_ticket_set_session_user($username);

    wp_send_json_success(
        array(
            'message'  => 'ورود موفق بود.',
            'username' => $username,
            'tickets'  => array_map('weblazem_format_ticket_for_api', weblazem_get_tickets_by_username($username)),
        )
    );
}
add_action('wp_ajax_weblazem_ticket_login', 'weblazem_ajax_ticket_login');
add_action('wp_ajax_nopriv_weblazem_ticket_login', 'weblazem_ajax_ticket_login');

function weblazem_ajax_ticket_logout() {
    check_ajax_referer('weblazem_ticketing', 'nonce');
    weblazem_ticket_clear_session_user();
    wp_send_json_success(array('message' => 'خارج شدید.'));
}
add_action('wp_ajax_weblazem_ticket_logout', 'weblazem_ajax_ticket_logout');
add_action('wp_ajax_nopriv_weblazem_ticket_logout', 'weblazem_ajax_ticket_logout');

function weblazem_ajax_ticket_session() {
    check_ajax_referer('weblazem_ticketing', 'nonce');

    $username = weblazem_ticket_get_session_user();
    if ($username === '') {
        wp_send_json_success(array('loggedIn' => false));
    }

    wp_send_json_success(
        array(
            'loggedIn' => true,
            'username' => $username,
            'tickets'  => array_map('weblazem_format_ticket_for_api', weblazem_get_tickets_by_username($username)),
        )
    );
}
add_action('wp_ajax_weblazem_ticket_session', 'weblazem_ajax_ticket_session');
add_action('wp_ajax_nopriv_weblazem_ticket_session', 'weblazem_ajax_ticket_session');

function weblazem_ajax_ticket_create() {
    check_ajax_referer('weblazem_ticketing', 'nonce');

    $username = weblazem_ticket_get_session_user();
    if ($username === '') {
        wp_send_json_error(array('message' => 'لطفاً ابتدا وارد شوید.'), 401);
    }

    $ticket_id = weblazem_create_support_ticket(
        array(
            'username'     => $username,
            'title'        => isset($_POST['title']) ? wp_unslash($_POST['title']) : '',
            'subject'      => isset($_POST['subject']) ? wp_unslash($_POST['subject']) : '',
            'message'      => isset($_POST['message']) ? wp_unslash($_POST['message']) : '',
            'mobile'       => isset($_POST['mobile']) ? wp_unslash($_POST['mobile']) : '',
            'email'        => isset($_POST['email']) ? wp_unslash($_POST['email']) : '',
            'priority'     => isset($_POST['priority']) ? wp_unslash($_POST['priority']) : 'normal',
            'project_name' => isset($_POST['project_name']) ? wp_unslash($_POST['project_name']) : '',
        )
    );

    if (is_wp_error($ticket_id)) {
        wp_send_json_error(array('message' => $ticket_id->get_error_message()), 400);
    }

    wp_send_json_success(
        array(
            'message' => 'تیکت با موفقیت ثبت شد.',
            'ticket'  => weblazem_format_ticket_for_api(get_post($ticket_id)),
            'tickets' => array_map('weblazem_format_ticket_for_api', weblazem_get_tickets_by_username($username)),
        )
    );
}
add_action('wp_ajax_weblazem_ticket_create', 'weblazem_ajax_ticket_create');
add_action('wp_ajax_nopriv_weblazem_ticket_create', 'weblazem_ajax_ticket_create');

function weblazem_ajax_ticket_get() {
    check_ajax_referer('weblazem_ticketing', 'nonce');

    $username  = weblazem_ticket_get_session_user();
    $ticket_id = isset($_POST['ticket_id']) ? absint($_POST['ticket_id']) : 0;

    if ($username === '') {
        wp_send_json_error(array('message' => 'لطفاً ابتدا وارد شوید.'), 401);
    }

    $post = get_post($ticket_id);
    if (!$post || $post->post_type !== 'support_ticket') {
        wp_send_json_error(array('message' => 'تیکت یافت نشد.'), 404);
    }

    if (!weblazem_ticket_user_owns($ticket_id, $username) && !current_user_can('manage_options')) {
        wp_send_json_error(array('message' => 'دسترسی مجاز نیست.'), 403);
    }

    wp_send_json_success(array('ticket' => weblazem_format_ticket_for_api($post)));
}
add_action('wp_ajax_weblazem_ticket_get', 'weblazem_ajax_ticket_get');
add_action('wp_ajax_nopriv_weblazem_ticket_get', 'weblazem_ajax_ticket_get');

function weblazem_ajax_ticket_reply() {
    check_ajax_referer('weblazem_ticketing', 'nonce');

    $username  = weblazem_ticket_get_session_user();
    $ticket_id = isset($_POST['ticket_id']) ? absint($_POST['ticket_id']) : 0;
    $message   = isset($_POST['message']) ? wp_unslash($_POST['message']) : '';

    if ($username === '') {
        wp_send_json_error(array('message' => 'لطفاً ابتدا وارد شوید.'), 401);
    }

    $post = get_post($ticket_id);
    if (!$post || $post->post_type !== 'support_ticket') {
        wp_send_json_error(array('message' => 'تیکت یافت نشد.'), 404);
    }

    if (!weblazem_ticket_user_owns($ticket_id, $username)) {
        wp_send_json_error(array('message' => 'دسترسی مجاز نیست.'), 403);
    }

    $status = get_post_meta($ticket_id, '_ticket_status', true);
    if ($status === 'closed') {
        wp_send_json_error(array('message' => 'این تیکت بسته شده و امکان پاسخ وجود ندارد.'), 400);
    }

    $result = weblazem_add_ticket_reply($ticket_id, $message, 'user', $username);
    if (is_wp_error($result)) {
        wp_send_json_error(array('message' => $result->get_error_message()), 400);
    }

    update_post_meta($ticket_id, '_ticket_status', 'waiting');

    wp_send_json_success(
        array(
            'message' => 'پاسخ شما ثبت شد.',
            'ticket'  => weblazem_format_ticket_for_api(get_post($ticket_id)),
        )
    );
}
add_action('wp_ajax_weblazem_ticket_reply', 'weblazem_ajax_ticket_reply');
add_action('wp_ajax_nopriv_weblazem_ticket_reply', 'weblazem_ajax_ticket_reply');

function weblazem_ajax_ticket_admin_reply() {
    check_ajax_referer('weblazem_ticketing_admin', 'nonce');

    if (!current_user_can('manage_options')) {
        wp_send_json_error(array('message' => 'دسترسی مجاز نیست.'), 403);
    }

    $ticket_id = isset($_POST['ticket_id']) ? absint($_POST['ticket_id']) : 0;
    $message   = isset($_POST['message']) ? wp_unslash($_POST['message']) : '';
    $status    = isset($_POST['status']) ? sanitize_text_field(wp_unslash($_POST['status'])) : '';

    $post = get_post($ticket_id);
    if (!$post || $post->post_type !== 'support_ticket') {
        wp_send_json_error(array('message' => 'تیکت یافت نشد.'), 404);
    }

    if (trim($message) !== '') {
        $user   = wp_get_current_user();
        $result = weblazem_add_ticket_reply($ticket_id, $message, 'admin', $user->display_name ?: 'پشتیبانی وب‌لازم');
        if (is_wp_error($result)) {
            wp_send_json_error(array('message' => $result->get_error_message()), 400);
        }
        if ($status === '') {
            $status = 'answered';
        }
    }

    $statuses = weblazem_ticket_statuses();
    if ($status !== '' && isset($statuses[$status])) {
        update_post_meta($ticket_id, '_ticket_status', $status);
        update_post_meta($ticket_id, '_ticket_updated_at', current_time('mysql'));
    }

    wp_send_json_success(
        array(
            'message' => 'پاسخ ادمین ثبت شد.',
            'ticket'  => weblazem_format_ticket_for_api(get_post($ticket_id)),
        )
    );
}
add_action('wp_ajax_weblazem_ticket_admin_reply', 'weblazem_ajax_ticket_admin_reply');

function weblazem_enqueue_ticketing_assets() {
    if (!is_page_template('home-template.php') && !is_front_page()) {
        return;
    }

    if (function_exists('weblazem_is_home_section_enabled') && !weblazem_is_home_section_enabled('ticketing')) {
        return;
    }

    wp_enqueue_style(
        'weblazem-ticketing',
        get_template_directory_uri() . '/assets/css/ticketing.css',
        array(),
        '1.0.0'
    );

    wp_enqueue_script(
        'weblazem-ticketing',
        get_template_directory_uri() . '/assets/js/ticketing.js',
        array(),
        '1.0.0',
        true
    );

    wp_localize_script(
        'weblazem-ticketing',
        'weblazemTicket',
        array(
            'ajaxUrl'     => admin_url('admin-ajax.php'),
            'nonce'       => wp_create_nonce('weblazem_ticketing'),
            'subjects'    => weblazem_ticket_subjects(),
            'priorities'  => weblazem_ticket_priorities(),
            'statuses'    => weblazem_ticket_statuses(),
            'accessHint'  => 'کد ورود سندباکس: ' . weblazem_get_ticket_access_code(),
            'errorMessage'=> 'خطایی رخ داد. لطفاً دوباره تلاش کنید.',
        )
    );
}
add_action('wp_enqueue_scripts', 'weblazem_enqueue_ticketing_assets', 30);
