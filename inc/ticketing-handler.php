<?php
/**
 * Ticketing sandbox auth + AJAX endpoints (secure).
 */

function weblazem_ticket_client_ip() {
    return isset($_SERVER['REMOTE_ADDR']) ? sanitize_text_field(wp_unslash($_SERVER['REMOTE_ADDR'])) : '0.0.0.0';
}

function weblazem_ticket_rate_limit($action, $limit = 8, $window = 600) {
    $key   = 'weblazem_ticket_rl_' . md5($action . '|' . weblazem_ticket_client_ip());
    $count = (int) get_transient($key);

    if ($count >= $limit) {
        return new WP_Error('rate_limit', 'تعداد درخواست‌ها زیاد است. کمی بعد دوباره تلاش کنید.');
    }

    set_transient($key, $count + 1, $window);
    return true;
}

function weblazem_ticket_start_session() {
    if (headers_sent()) {
        return;
    }

    if (session_status() === PHP_SESSION_NONE) {
        session_set_cookie_params(
            array(
                'lifetime' => 0,
                'path'     => defined('COOKIEPATH') ? COOKIEPATH : '/',
                'domain'   => defined('COOKIE_DOMAIN') ? COOKIE_DOMAIN : '',
                'secure'   => is_ssl(),
                'httponly' => true,
                'samesite' => 'Lax',
            )
        );
        session_start();
    }
}
add_action('init', 'weblazem_ticket_start_session', 1);

function weblazem_ticket_get_session_user() {
    weblazem_ticket_start_session();

    if (empty($_SESSION['weblazem_ticket_user']) || empty($_SESSION['weblazem_ticket_auth'])) {
        return '';
    }

    // Session expiry: 8 hours
    $login_at = isset($_SESSION['weblazem_ticket_login_at']) ? (int) $_SESSION['weblazem_ticket_login_at'] : 0;
    if ($login_at && (time() - $login_at) > 8 * HOUR_IN_SECONDS) {
        weblazem_ticket_clear_session_user();
        return '';
    }

    return weblazem_ticket_normalize_mobile($_SESSION['weblazem_ticket_user']);
}

function weblazem_ticket_set_session_user($mobile) {
    weblazem_ticket_start_session();

    if (session_status() === PHP_SESSION_ACTIVE) {
        session_regenerate_id(true);
    }

    $_SESSION['weblazem_ticket_user']     = weblazem_ticket_normalize_mobile($mobile);
    $_SESSION['weblazem_ticket_auth']     = wp_hash($_SESSION['weblazem_ticket_user'] . '|' . weblazem_get_ticket_access_code());
    $_SESSION['weblazem_ticket_login_at'] = time();
}

function weblazem_ticket_clear_session_user() {
    weblazem_ticket_start_session();
    unset($_SESSION['weblazem_ticket_user'], $_SESSION['weblazem_ticket_auth'], $_SESSION['weblazem_ticket_login_at']);
}

function weblazem_ticket_require_login() {
    $user = weblazem_ticket_get_session_user();
    if ($user === '') {
        wp_send_json_error(array('message' => 'برای ثبت و پیگیری تیکت ابتدا وارد شوید.'), 401);
    }

    return $user;
}

function weblazem_ajax_ticket_login() {
    check_ajax_referer('weblazem_ticketing', 'nonce');

    $rate = weblazem_ticket_rate_limit('login', 10, 15 * MINUTE_IN_SECONDS);
    if (is_wp_error($rate)) {
        wp_send_json_error(array('message' => $rate->get_error_message()), 429);
    }

    $mobile = isset($_POST['mobile']) ? sanitize_text_field(wp_unslash($_POST['mobile'])) : '';
    // Backward compat if username sent as mobile
    if ($mobile === '' && isset($_POST['username'])) {
        $mobile = sanitize_text_field(wp_unslash($_POST['username']));
    }
    $code = isset($_POST['access_code']) ? sanitize_text_field(wp_unslash($_POST['access_code'])) : '';

    if ($mobile === '' || $code === '') {
        wp_send_json_error(array('message' => 'شماره موبایل و کد ورود الزامی است.'), 400);
    }

    if (!weblazem_ticket_is_valid_mobile($mobile)) {
        wp_send_json_error(array('message' => 'شماره موبایل معتبر نیست. مثال: 09121234567'), 400);
    }

    $expected = weblazem_get_ticket_access_code();
    if (!hash_equals((string) $expected, (string) $code)) {
        wp_send_json_error(array('message' => 'کد ورود نامعتبر است.'), 403);
    }

    $mobile = weblazem_ticket_normalize_mobile($mobile);
    weblazem_ticket_set_session_user($mobile);

    wp_send_json_success(
        array(
            'message'  => 'ورود موفق بود.',
            'username' => $mobile,
            'mobile'   => $mobile,
            'tickets'  => array_map('weblazem_format_ticket_for_api', weblazem_get_tickets_by_username($mobile)),
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

    $mobile = weblazem_ticket_get_session_user();
    if ($mobile === '') {
        wp_send_json_success(array('loggedIn' => false));
    }

    wp_send_json_success(
        array(
            'loggedIn' => true,
            'username' => $mobile,
            'mobile'   => $mobile,
            'tickets'  => array_map('weblazem_format_ticket_for_api', weblazem_get_tickets_by_username($mobile)),
        )
    );
}
add_action('wp_ajax_weblazem_ticket_session', 'weblazem_ajax_ticket_session');
add_action('wp_ajax_nopriv_weblazem_ticket_session', 'weblazem_ajax_ticket_session');

function weblazem_ajax_ticket_create() {
    check_ajax_referer('weblazem_ticketing', 'nonce');

    $mobile = weblazem_ticket_require_login();

    $rate = weblazem_ticket_rate_limit('create_' . $mobile, 12, HOUR_IN_SECONDS);
    if (is_wp_error($rate)) {
        wp_send_json_error(array('message' => $rate->get_error_message()), 429);
    }

    $attachment = null;
    if (!empty($_FILES['attachment']) && !empty($_FILES['attachment']['name'])) {
        $attachment = weblazem_ticket_handle_upload($_FILES['attachment']);
        if (is_wp_error($attachment)) {
            wp_send_json_error(array('message' => $attachment->get_error_message()), 400);
        }
    }

    $ticket_id = weblazem_create_support_ticket(
        array(
            'mobile'       => $mobile,
            'username'     => $mobile,
            'title'        => isset($_POST['title']) ? wp_unslash($_POST['title']) : '',
            'subject'      => isset($_POST['subject']) ? wp_unslash($_POST['subject']) : '',
            'message'      => isset($_POST['message']) ? wp_unslash($_POST['message']) : '',
            'email'        => isset($_POST['email']) ? wp_unslash($_POST['email']) : '',
            'priority'     => isset($_POST['priority']) ? wp_unslash($_POST['priority']) : 'normal',
            'project_name' => isset($_POST['project_name']) ? wp_unslash($_POST['project_name']) : '',
        ),
        $attachment
    );

    if (is_wp_error($ticket_id)) {
        wp_send_json_error(array('message' => $ticket_id->get_error_message()), 400);
    }

    $ticket = weblazem_format_ticket_for_api(get_post($ticket_id));
    $success = get_option('weblazem_ticket_success_message', 'تیکت شما با موفقیت ثبت شد. کارشناسان وب‌لازم به‌زودی پاسخ می‌دهند.');

    wp_send_json_success(
        array(
            'message' => $success,
            'ticket'  => $ticket,
            'tickets' => array_map('weblazem_format_ticket_for_api', weblazem_get_tickets_by_username($mobile)),
        )
    );
}
add_action('wp_ajax_weblazem_ticket_create', 'weblazem_ajax_ticket_create');
add_action('wp_ajax_nopriv_weblazem_ticket_create', 'weblazem_ajax_ticket_create');

function weblazem_ajax_ticket_get() {
    check_ajax_referer('weblazem_ticketing', 'nonce');

    $mobile    = weblazem_ticket_require_login();
    $ticket_id = isset($_POST['ticket_id']) ? absint($_POST['ticket_id']) : 0;

    $post = get_post($ticket_id);
    if (!$post || $post->post_type !== 'support_ticket') {
        wp_send_json_error(array('message' => 'تیکت یافت نشد.'), 404);
    }

    if (!weblazem_ticket_user_owns($ticket_id, $mobile) && !current_user_can('manage_options')) {
        wp_send_json_error(array('message' => 'دسترسی مجاز نیست.'), 403);
    }

    wp_send_json_success(array('ticket' => weblazem_format_ticket_for_api($post)));
}
add_action('wp_ajax_weblazem_ticket_get', 'weblazem_ajax_ticket_get');
add_action('wp_ajax_nopriv_weblazem_ticket_get', 'weblazem_ajax_ticket_get');

function weblazem_ajax_ticket_reply() {
    check_ajax_referer('weblazem_ticketing', 'nonce');

    $mobile    = weblazem_ticket_require_login();
    $ticket_id = isset($_POST['ticket_id']) ? absint($_POST['ticket_id']) : 0;
    $message   = isset($_POST['message']) ? wp_unslash($_POST['message']) : '';

    $rate = weblazem_ticket_rate_limit('reply_' . $mobile, 30, HOUR_IN_SECONDS);
    if (is_wp_error($rate)) {
        wp_send_json_error(array('message' => $rate->get_error_message()), 429);
    }

    $post = get_post($ticket_id);
    if (!$post || $post->post_type !== 'support_ticket') {
        wp_send_json_error(array('message' => 'تیکت یافت نشد.'), 404);
    }

    if (!weblazem_ticket_user_owns($ticket_id, $mobile)) {
        wp_send_json_error(array('message' => 'دسترسی مجاز نیست.'), 403);
    }

    $status = get_post_meta($ticket_id, '_ticket_status', true);
    if ($status === 'closed') {
        wp_send_json_error(array('message' => 'این تیکت بسته شده و امکان پاسخ وجود ندارد.'), 400);
    }

    $attachments = array();
    if (!empty($_FILES['attachment']) && !empty($_FILES['attachment']['name'])) {
        $attachment = weblazem_ticket_handle_upload($_FILES['attachment']);
        if (is_wp_error($attachment)) {
            wp_send_json_error(array('message' => $attachment->get_error_message()), 400);
        }
        $attachments[] = $attachment;
        wp_update_post(
            array(
                'ID'          => (int) $attachment['id'],
                'post_parent' => $ticket_id,
            )
        );

        $all = weblazem_get_ticket_attachments($ticket_id);
        $all[] = $attachment;
        update_post_meta($ticket_id, '_ticket_attachments', $all);
    }

    $result = weblazem_add_ticket_reply($ticket_id, $message, 'user', $mobile, $attachments);
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
    $is_ticket_page = function_exists('weblazem_is_ticket_page') && weblazem_is_ticket_page();
    $is_home_cta    = (is_page_template('home-template.php') || is_front_page())
        && (!function_exists('weblazem_is_home_section_enabled') || weblazem_is_home_section_enabled('ticketing'));

    if (!$is_ticket_page && !$is_home_cta) {
        return;
    }

    wp_enqueue_style(
        'weblazem-ticketing',
        get_template_directory_uri() . '/assets/css/ticketing.css',
        array(),
        '1.1.0'
    );

    if (!$is_ticket_page) {
        return;
    }

    wp_enqueue_script(
        'weblazem-ticketing',
        get_template_directory_uri() . '/assets/js/ticketing.js',
        array(),
        '1.1.0',
        true
    );

    wp_localize_script(
        'weblazem-ticketing',
        'weblazemTicket',
        array(
            'ajaxUrl'        => admin_url('admin-ajax.php'),
            'nonce'          => wp_create_nonce('weblazem_ticketing'),
            'subjects'       => weblazem_ticket_subjects(),
            'priorities'     => weblazem_ticket_priorities(),
            'statuses'       => weblazem_ticket_statuses(),
            'maxUploadMb'    => 3,
            'successMessage' => get_option('weblazem_ticket_success_message', 'تیکت شما با موفقیت ثبت شد.'),
            'errorMessage'   => 'خطایی رخ داد. لطفاً دوباره تلاش کنید.',
            'loginRequired'  => 'برای ثبت تیکت ابتدا با شماره موبایل وارد شوید.',
        )
    );
}
add_action('wp_enqueue_scripts', 'weblazem_enqueue_ticketing_assets', 30);
