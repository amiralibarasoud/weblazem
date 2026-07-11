<?php
/**
 * Proposal Builder — CPT, admin CRUD, client AJAX, options.
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('weblazem_growth_format_toman')) {
    require_once get_template_directory() . '/inc/growth-tools-shared.php';
}

/**
 * Proposal statuses.
 */
function weblazem_proposal_statuses() {
    return array(
        'draft'             => 'پیش‌نویس',
        'sent'              => 'ارسال‌شده',
        'viewed'            => 'مشاهده‌شده',
        'accepted'          => 'پذیرفته‌شده',
        'rejected'          => 'رد شده',
        'changes_requested' => 'درخواست تغییر',
    );
}

function weblazem_proposal_status_label($status) {
    $statuses = weblazem_proposal_statuses();
    return isset($statuses[$status]) ? $statuses[$status] : $status;
}

function weblazem_proposal_default_terms() {
    return "۱. این پیشنهاد تا ۷ روز پس از ارسال معتبر است.\n"
        . "۲. ۵۰٪ مبلغ به‌عنوان پیش‌پرداخت قبل از شروع کار دریافت می‌شود و مابقی پس از تحویل نهایی.\n"
        . "۳. تغییرات خارج از محدودهٔ توافق‌شده مشمول هزینهٔ اضافه خواهد بود.\n"
        . "۴. زمان تحویل از تاریخ تایید پیشنهاد و دریافت پیش‌پرداخت محاسبه می‌شود.\n"
        . "۵. محتوای متنی و تصاویر نهایی بر عهدهٔ کارفرماست مگر خلاف آن توافق شود.";
}

function weblazem_proposal_defaults() {
    return array(
        'default_terms'    => weblazem_proposal_default_terms(),
        'default_delivery' => 21,
    );
}

function weblazem_get_proposal_options() {
    $defaults = weblazem_proposal_defaults();
    $saved    = get_option('weblazem_proposal_options', array());
    if (!is_array($saved)) {
        $saved = array();
    }
    return array_merge($defaults, $saved);
}

function weblazem_ensure_proposal_defaults() {
    if (get_option('weblazem_proposal_options', false) === false) {
        update_option('weblazem_proposal_options', weblazem_proposal_defaults());
    }
}
add_action('init', 'weblazem_ensure_proposal_defaults', 12);

function weblazem_proposal_normalize_mobile($phone) {
    if (function_exists('weblazem_ticket_normalize_mobile')) {
        return weblazem_ticket_normalize_mobile($phone);
    }
    $digits = preg_replace('/\D+/', '', (string) $phone);
    if (strpos($digits, '98') === 0 && strlen($digits) >= 12) {
        $digits = '0' . substr($digits, 2);
    }
    if (preg_match('/^9\d{9}$/', $digits)) {
        $digits = '0' . $digits;
    }
    return $digits;
}

function weblazem_proposal_is_valid_mobile($phone) {
    if (function_exists('weblazem_ticket_is_valid_mobile')) {
        return weblazem_ticket_is_valid_mobile($phone);
    }
    return (bool) preg_match('/^09\d{9}$/', weblazem_proposal_normalize_mobile($phone));
}

function weblazem_register_client_proposal_cpt() {
    register_post_type(
        'client_proposal',
        array(
            'labels' => array(
                'name'          => 'پیشنهادهای قیمت',
                'singular_name' => 'پیشنهاد',
                'menu_name'     => 'پیشنهادها',
                'add_new'       => 'پیشنهاد جدید',
                'add_new_item'  => 'افزودن پیشنهاد جدید',
                'edit_item'     => 'ویرایش پیشنهاد',
                'search_items'  => 'جستجوی پیشنهاد',
                'not_found'     => 'پیشنهادی یافت نشد.',
            ),
            'public'             => false,
            'publicly_queryable' => false,
            'show_ui'            => true,
            'show_in_menu'       => false,
            'show_in_rest'       => false,
            'capability_type'    => 'post',
            'map_meta_cap'       => true,
            'supports'           => array('title'),
            'has_archive'        => false,
        )
    );
}
add_action('init', 'weblazem_register_client_proposal_cpt');

function weblazem_proposal_generate_code() {
    do {
        $code   = 'PROP-' . strtoupper(wp_generate_password(4, false, false));
        $exists = get_posts(
            array(
                'post_type'      => 'client_proposal',
                'post_status'    => 'any',
                'posts_per_page' => 1,
                'fields'         => 'ids',
                'meta_key'       => '_proposal_code',
                'meta_value'     => $code,
            )
        );
    } while (!empty($exists));

    return $code;
}

function weblazem_proposal_sanitize_items($items) {
    if (!is_array($items)) {
        return array();
    }

    $clean = array();
    foreach ($items as $item) {
        if (!is_array($item)) {
            continue;
        }
        $title = isset($item['title']) ? sanitize_text_field($item['title']) : '';
        $desc  = isset($item['description']) ? sanitize_textarea_field($item['description']) : '';
        $price = isset($item['price']) ? (int) $item['price'] : 0;
        if ($title === '' && $desc === '' && $price === 0) {
            continue;
        }
        $clean[] = array(
            'title'       => $title,
            'description' => $desc,
            'price'       => max(0, $price),
        );
    }

    return $clean;
}

function weblazem_proposal_calc_totals($items, $discount = 0) {
    $subtotal = 0;
    foreach ($items as $item) {
        $subtotal += isset($item['price']) ? (int) $item['price'] : 0;
    }
    $discount = max(0, (int) $discount);
    if ($discount > $subtotal) {
        $discount = $subtotal;
    }
    return array(
        'subtotal' => $subtotal,
        'discount' => $discount,
        'total'    => max(0, $subtotal - $discount),
    );
}

function weblazem_format_proposal_for_api($post, $include_private = false) {
    if (!$post || $post->post_type !== 'client_proposal') {
        return null;
    }

    $id     = (int) $post->ID;
    $status = get_post_meta($id, '_proposal_status', true) ?: 'draft';
    $items  = weblazem_proposal_sanitize_items(get_post_meta($id, '_proposal_items', true));
    $subtotal = (int) get_post_meta($id, '_proposal_subtotal', true);
    $discount = (int) get_post_meta($id, '_proposal_discount', true);
    $total    = (int) get_post_meta($id, '_proposal_total', true);

    if (!$subtotal && !empty($items)) {
        $calc     = weblazem_proposal_calc_totals($items, $discount);
        $subtotal = $calc['subtotal'];
        $discount = $calc['discount'];
        $total    = $calc['total'];
    }

    $data = array(
        'id'            => $id,
        'code'          => (string) get_post_meta($id, '_proposal_code', true),
        'title'         => (string) (get_post_meta($id, '_proposal_title', true) ?: get_the_title($id)),
        'intro'         => (string) get_post_meta($id, '_proposal_intro', true),
        'clientName'    => (string) get_post_meta($id, '_proposal_client_name', true),
        'mobile'        => (string) get_post_meta($id, '_proposal_mobile', true),
        'briefId'       => (int) get_post_meta($id, '_proposal_brief_id', true),
        'items'         => $items,
        'subtotal'      => $subtotal,
        'discount'      => $discount,
        'total'         => $total,
        'subtotalLabel' => weblazem_growth_format_toman($subtotal),
        'discountLabel' => weblazem_growth_format_toman($discount),
        'totalLabel'    => weblazem_growth_format_toman($total),
        'deliveryDays'  => (int) get_post_meta($id, '_proposal_delivery_days', true),
        'terms'         => (string) get_post_meta($id, '_proposal_terms', true),
        'status'        => $status,
        'statusLabel'   => weblazem_proposal_status_label($status),
        'clientNote'    => (string) get_post_meta($id, '_proposal_client_note', true),
        'sentAt'        => (string) get_post_meta($id, '_proposal_sent_at', true),
        'respondedAt'   => (string) get_post_meta($id, '_proposal_responded_at', true),
        'updatedAt'     => get_the_modified_date('Y-m-d H:i', $id),
        'createdAt'     => get_the_date('Y-m-d H:i', $id),
    );

    if ($include_private) {
        $data['canRespond'] = in_array($status, array('sent', 'viewed', 'changes_requested'), true);
    }

    return $data;
}

function weblazem_get_proposals_by_mobile($mobile, $exclude_draft = true) {
    $mobile = weblazem_proposal_normalize_mobile($mobile);
    if ($mobile === '') {
        return array();
    }

    $query = new WP_Query(
        array(
            'post_type'      => 'client_proposal',
            'post_status'    => 'publish',
            'posts_per_page' => 50,
            'orderby'        => 'date',
            'order'          => 'DESC',
            'meta_query'     => array(
                array(
                    'key'   => '_proposal_mobile',
                    'value' => $mobile,
                ),
            ),
        )
    );

    $items = array();
    foreach ($query->posts as $post) {
        $status = get_post_meta($post->ID, '_proposal_status', true) ?: 'draft';
        if ($exclude_draft && $status === 'draft') {
            continue;
        }
        $formatted = weblazem_format_proposal_for_api($post, true);
        if ($formatted) {
            $items[] = $formatted;
        }
    }
    wp_reset_postdata();

    return $items;
}

function weblazem_format_brief_for_client_api($post) {
    if (!$post || $post->post_type !== 'project_brief') {
        return null;
    }

    $id    = (int) $post->ID;
    $type  = (string) get_post_meta($id, '_brief_project_type', true);
    $types = function_exists('weblazem_start_project_default_types') ? weblazem_start_project_default_types() : array();
    $bud   = (string) get_post_meta($id, '_brief_budget', true);
    $buds  = function_exists('weblazem_start_project_default_budgets') ? weblazem_start_project_default_budgets() : array();

    return array(
        'id'           => $id,
        'title'        => get_the_title($id),
        'name'         => (string) get_post_meta($id, '_brief_name', true),
        'mobile'       => (string) get_post_meta($id, '_brief_mobile', true),
        'email'        => (string) get_post_meta($id, '_brief_email', true),
        'projectType'  => $type,
        'projectLabel' => isset($types[$type]) ? $types[$type] : $type,
        'budget'       => $bud,
        'budgetLabel'  => isset($buds[$bud]) ? $buds[$bud] : $bud,
        'deadline'     => (string) get_post_meta($id, '_brief_deadline', true),
        'goal'         => (string) get_post_meta($id, '_brief_goal', true),
        'status'       => (string) (get_post_meta($id, '_brief_status', true) ?: 'new'),
        'crmStatus'    => function_exists('weblazem_crm_get_status') ? weblazem_crm_get_status($id) : '',
        'createdAt'    => get_the_date('Y-m-d H:i', $id),
    );
}

function weblazem_get_briefs_by_mobile($mobile) {
    $mobile = weblazem_proposal_normalize_mobile($mobile);
    if ($mobile === '' || !post_type_exists('project_brief')) {
        return array();
    }

    $query = new WP_Query(
        array(
            'post_type'      => 'project_brief',
            'post_status'    => 'publish',
            'posts_per_page' => 50,
            'orderby'        => 'date',
            'order'          => 'DESC',
            'meta_query'     => array(
                array(
                    'key'   => '_brief_mobile',
                    'value' => $mobile,
                ),
            ),
        )
    );

    $items = array();
    foreach ($query->posts as $post) {
        $formatted = weblazem_format_brief_for_client_api($post);
        if ($formatted) {
            $items[] = $formatted;
        }
    }
    wp_reset_postdata();

    return $items;
}

function weblazem_proposal_get_brief_choices() {
    if (!post_type_exists('project_brief')) {
        return array();
    }

    $query = new WP_Query(
        array(
            'post_type'      => 'project_brief',
            'post_status'    => 'publish',
            'posts_per_page' => 100,
            'orderby'        => 'date',
            'order'          => 'DESC',
        )
    );

    $choices = array();
    foreach ($query->posts as $post) {
        $id     = (int) $post->ID;
        $name   = get_post_meta($id, '_brief_name', true);
        $mobile = get_post_meta($id, '_brief_mobile', true);
        $type   = get_post_meta($id, '_brief_project_type', true);
        $types  = function_exists('weblazem_start_project_default_types') ? weblazem_start_project_default_types() : array();
        $label  = isset($types[$type]) ? $types[$type] : $type;

        $choices[] = array(
            'id'     => $id,
            'label'  => trim(($name ?: get_the_title($id)) . ' — ' . $mobile . ($label ? ' (' . $label . ')' : '')),
            'name'   => (string) $name,
            'mobile' => (string) $mobile,
            'type'   => (string) $type,
            'goal'   => (string) get_post_meta($id, '_brief_goal', true),
        );
    }
    wp_reset_postdata();

    return $choices;
}

/**
 * Create client_project from accepted proposal when helpers exist.
 */
function weblazem_proposal_maybe_create_project($proposal_id) {
    if (!post_type_exists('client_project')) {
        return 0;
    }

    $existing = (int) get_post_meta($proposal_id, '_proposal_project_id', true);
    if ($existing && get_post($existing)) {
        return $existing;
    }

    $title  = get_post_meta($proposal_id, '_proposal_title', true) ?: get_the_title($proposal_id);
    $mobile = weblazem_proposal_normalize_mobile(get_post_meta($proposal_id, '_proposal_mobile', true));
    $name   = get_post_meta($proposal_id, '_proposal_client_name', true);
    $code   = function_exists('weblazem_project_status_generate_code')
        ? weblazem_project_status_generate_code()
        : ('PRJ-' . strtoupper(wp_generate_password(6, false, false)));

    $stages = function_exists('weblazem_project_status_default_stages')
        ? weblazem_project_status_default_stages()
        : array();

    if (!empty($stages) && isset($stages[0])) {
        $stages[0]['done'] = true;
        $stages[0]['date'] = current_time('Y-m-d');
        $stages[0]['note'] = 'ایجاد شده از پیشنهاد پذیرفته‌شده ' . get_post_meta($proposal_id, '_proposal_code', true);
    }

    $project_id = wp_insert_post(
        array(
            'post_type'   => 'client_project',
            'post_status' => 'publish',
            'post_title'  => $title,
        ),
        true
    );

    if (is_wp_error($project_id) || !$project_id) {
        return 0;
    }

    update_post_meta($project_id, '_project_code', $code);
    update_post_meta($project_id, '_project_client_name', $name);
    update_post_meta($project_id, '_project_client_mobile', $mobile);
    update_post_meta($project_id, '_project_stage', 'briefing');
    update_post_meta($project_id, '_project_progress', 10);
    update_post_meta($project_id, '_project_stages', $stages);
    update_post_meta($project_id, '_project_files', array());
    update_post_meta($project_id, '_project_proposal_id', $proposal_id);
    update_post_meta($proposal_id, '_proposal_project_id', $project_id);

    return (int) $project_id;
}

function weblazem_proposal_user_owns($proposal_id, $mobile) {
    $owner = weblazem_proposal_normalize_mobile(get_post_meta($proposal_id, '_proposal_mobile', true));
    return $owner !== '' && hash_equals($owner, weblazem_proposal_normalize_mobile($mobile));
}

function weblazem_proposal_mark_viewed($proposal_id) {
    $status = get_post_meta($proposal_id, '_proposal_status', true);
    if ($status === 'sent') {
        update_post_meta($proposal_id, '_proposal_status', 'viewed');
        update_post_meta($proposal_id, '_proposal_viewed_at', current_time('mysql'));
    }
}

/* --------------------------------------------------------------------------
 * Admin menu + assets
 * -------------------------------------------------------------------------- */

function weblazem_proposal_admin_menu() {
    add_submenu_page(
        'weblazem-theme-options',
        'پیشنهادهای قیمت',
        'پیشنهادهای قیمت',
        'manage_options',
        'weblazem-proposals',
        'weblazem_proposal_admin_page'
    );
}
add_action('admin_menu', 'weblazem_proposal_admin_menu', 36);

function weblazem_proposal_admin_assets($hook) {
    if (strpos($hook, 'weblazem-proposals') === false) {
        return;
    }

    wp_enqueue_style(
        'weblazem-proposal-admin',
        get_template_directory_uri() . '/assets/css/proposal-admin.css',
        array(),
        '1.0.0'
    );

    wp_enqueue_script(
        'weblazem-proposal-admin',
        get_template_directory_uri() . '/assets/js/proposal-admin.js',
        array('jquery'),
        '1.0.0',
        true
    );

    $opts = weblazem_get_proposal_options();

    wp_localize_script(
        'weblazem-proposal-admin',
        'weblazemProposalAdmin',
        array(
            'ajaxUrl'         => admin_url('admin-ajax.php'),
            'nonce'           => wp_create_nonce('weblazem_proposal_admin'),
            'briefs'          => weblazem_proposal_get_brief_choices(),
            'defaultTerms'    => $opts['default_terms'],
            'defaultDelivery' => (int) $opts['default_delivery'],
            'portalUrl'       => function_exists('weblazem_get_ticket_page_url') ? weblazem_get_ticket_page_url() : home_url('/sabt-ticket/'),
        )
    );
}
add_action('admin_enqueue_scripts', 'weblazem_proposal_admin_assets');

function weblazem_proposal_admin_save($data, $send = false) {
    $proposal_id = isset($data['proposal_id']) ? absint($data['proposal_id']) : 0;
    $title       = isset($data['proposal_title']) ? sanitize_text_field(wp_unslash($data['proposal_title'])) : '';
    $intro       = isset($data['proposal_intro']) ? sanitize_textarea_field(wp_unslash($data['proposal_intro'])) : '';
    $client_name = isset($data['client_name']) ? sanitize_text_field(wp_unslash($data['client_name'])) : '';
    $mobile      = isset($data['client_mobile']) ? weblazem_proposal_normalize_mobile(sanitize_text_field(wp_unslash($data['client_mobile']))) : '';
    $brief_id    = isset($data['brief_id']) ? absint($data['brief_id']) : 0;
    $discount    = isset($data['discount']) ? absint($data['discount']) : 0;
    $delivery    = isset($data['delivery_days']) ? absint($data['delivery_days']) : 0;
    $terms       = isset($data['terms']) ? sanitize_textarea_field(wp_unslash($data['terms'])) : '';

    $raw_items = array();
    if (isset($data['items']) && is_array($data['items'])) {
        $raw_items = $data['items'];
    } elseif (isset($data['item_title']) && is_array($data['item_title'])) {
        $titles = $data['item_title'];
        $descs  = isset($data['item_description']) && is_array($data['item_description']) ? $data['item_description'] : array();
        $prices = isset($data['item_price']) && is_array($data['item_price']) ? $data['item_price'] : array();
        foreach ($titles as $i => $t) {
            $raw_items[] = array(
                'title'       => wp_unslash($t),
                'description' => isset($descs[$i]) ? wp_unslash($descs[$i]) : '',
                'price'       => isset($prices[$i]) ? $prices[$i] : 0,
            );
        }
    }

    $items = weblazem_proposal_sanitize_items($raw_items);
    $calc  = weblazem_proposal_calc_totals($items, $discount);

    if ($title === '') {
        return new WP_Error('title', 'عنوان پیشنهاد الزامی است.');
    }
    if (!weblazem_proposal_is_valid_mobile($mobile)) {
        return new WP_Error('mobile', 'شماره موبایل مشتری معتبر نیست.');
    }
    if (empty($items)) {
        return new WP_Error('items', 'حداقل یک ردیف خدمات اضافه کنید.');
    }

    if ($brief_id && get_post_type($brief_id) !== 'project_brief') {
        $brief_id = 0;
    }

    if ($proposal_id) {
        $post = get_post($proposal_id);
        if (!$post || $post->post_type !== 'client_proposal') {
            return new WP_Error('not_found', 'پیشنهاد یافت نشد.');
        }
        wp_update_post(
            array(
                'ID'         => $proposal_id,
                'post_title' => $title,
            )
        );
    } else {
        $proposal_id = wp_insert_post(
            array(
                'post_type'   => 'client_proposal',
                'post_status' => 'publish',
                'post_title'  => $title,
            ),
            true
        );
        if (is_wp_error($proposal_id) || !$proposal_id) {
            return new WP_Error('insert', 'ثبت پیشنهاد ناموفق بود.');
        }
        update_post_meta($proposal_id, '_proposal_code', weblazem_proposal_generate_code());
        update_post_meta($proposal_id, '_proposal_status', 'draft');
    }

    update_post_meta($proposal_id, '_proposal_title', $title);
    update_post_meta($proposal_id, '_proposal_intro', $intro);
    update_post_meta($proposal_id, '_proposal_client_name', $client_name);
    update_post_meta($proposal_id, '_proposal_mobile', $mobile);
    update_post_meta($proposal_id, '_proposal_brief_id', $brief_id);
    update_post_meta($proposal_id, '_proposal_items', $items);
    update_post_meta($proposal_id, '_proposal_subtotal', $calc['subtotal']);
    update_post_meta($proposal_id, '_proposal_discount', $calc['discount']);
    update_post_meta($proposal_id, '_proposal_total', $calc['total']);
    update_post_meta($proposal_id, '_proposal_delivery_days', $delivery);
    update_post_meta($proposal_id, '_proposal_terms', $terms !== '' ? $terms : weblazem_proposal_default_terms());

    $current = get_post_meta($proposal_id, '_proposal_status', true) ?: 'draft';

    if ($send) {
        update_post_meta($proposal_id, '_proposal_status', 'sent');
        update_post_meta($proposal_id, '_proposal_sent_at', current_time('mysql'));
    } elseif ($current === '') {
        update_post_meta($proposal_id, '_proposal_status', 'draft');
    }

    return (int) $proposal_id;
}

function weblazem_proposal_admin_page() {
    if (!current_user_can('manage_options')) {
        return;
    }

    $action = isset($_GET['action']) ? sanitize_key($_GET['action']) : 'list';
    $edit_id = isset($_GET['id']) ? absint($_GET['id']) : 0;
    $status_filter = isset($_GET['status']) ? sanitize_key($_GET['status']) : '';
    $base = admin_url('admin.php?page=weblazem-proposals');
    $notice = '';

    if (isset($_POST['weblazem_proposal_nonce']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['weblazem_proposal_nonce'])), 'weblazem_proposal_save')) {
        $send   = !empty($_POST['send_to_client']);
        $result = weblazem_proposal_admin_save($_POST, $send);
        if (is_wp_error($result)) {
            $notice = '<div class="notice notice-error"><p>' . esc_html($result->get_error_message()) . '</p></div>';
            $action = 'edit';
            $edit_id = isset($_POST['proposal_id']) ? absint($_POST['proposal_id']) : 0;
        } else {
            $msg = $send ? 'پیشنهاد ذخیره و برای مشتری ارسال شد.' : 'پیشنهاد به‌عنوان پیش‌نویس ذخیره شد.';
            echo '<div class="notice notice-success is-dismissible"><p>' . esc_html($msg) . '</p></div>';
            $action = 'list';
        }
    }

    if (isset($_POST['weblazem_proposal_opts_nonce']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['weblazem_proposal_opts_nonce'])), 'weblazem_proposal_opts')) {
        $opts = array(
            'default_terms'    => isset($_POST['default_terms']) ? sanitize_textarea_field(wp_unslash($_POST['default_terms'])) : weblazem_proposal_default_terms(),
            'default_delivery' => isset($_POST['default_delivery']) ? absint($_POST['default_delivery']) : 21,
        );
        update_option('weblazem_proposal_options', $opts);
        echo '<div class="notice notice-success is-dismissible"><p>تنظیمات ذخیره شد.</p></div>';
        $action = 'settings';
    }

    if (isset($_GET['delete']) && isset($_GET['_wpnonce']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['_wpnonce'])), 'weblazem_proposal_delete_' . absint($_GET['delete']))) {
        $del = absint($_GET['delete']);
        if ($del && get_post_type($del) === 'client_proposal') {
            wp_trash_post($del);
            echo '<div class="notice notice-success is-dismissible"><p>پیشنهاد حذف شد.</p></div>';
        }
    }

    echo $notice; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    ?>
    <div class="wrap weblazem-proposal-admin" dir="rtl">
        <h1>پیشنهادهای قیمت</h1>
        <h2 class="nav-tab-wrapper">
            <a class="nav-tab <?php echo $action === 'list' ? 'nav-tab-active' : ''; ?>" href="<?php echo esc_url($base); ?>">لیست پیشنهادها</a>
            <a class="nav-tab <?php echo ($action === 'edit' || $action === 'new') ? 'nav-tab-active' : ''; ?>" href="<?php echo esc_url($base . '&action=new'); ?>">ایجاد / ویرایش</a>
            <a class="nav-tab <?php echo $action === 'settings' ? 'nav-tab-active' : ''; ?>" href="<?php echo esc_url($base . '&action=settings'); ?>">تنظیمات</a>
        </h2>

        <?php
        if ($action === 'settings') {
            weblazem_proposal_admin_settings_ui();
        } elseif ($action === 'edit' || $action === 'new') {
            weblazem_proposal_admin_form_ui($edit_id);
        } else {
            weblazem_proposal_admin_list_ui($status_filter);
        }
        ?>
    </div>
    <?php
}

function weblazem_proposal_admin_settings_ui() {
    $opts = weblazem_get_proposal_options();
    $portal = function_exists('weblazem_get_ticket_page_url') ? weblazem_get_ticket_page_url() : home_url('/sabt-ticket/');
    ?>
    <p class="description">مشتریان پیشنهاد را در <a href="<?php echo esc_url($portal); ?>" target="_blank" rel="noopener">حساب کاربری</a> (تب پیشنهادها) مشاهده می‌کنند.</p>
    <form method="post">
        <?php wp_nonce_field('weblazem_proposal_opts', 'weblazem_proposal_opts_nonce'); ?>
        <table class="form-table">
            <tr>
                <th>شرایط پیش‌فرض</th>
                <td><textarea name="default_terms" class="large-text" rows="8"><?php echo esc_textarea($opts['default_terms']); ?></textarea></td>
            </tr>
            <tr>
                <th>زمان تحویل پیش‌فرض (روز)</th>
                <td><input type="number" name="default_delivery" min="1" value="<?php echo esc_attr((int) $opts['default_delivery']); ?>" /></td>
            </tr>
        </table>
        <?php submit_button('ذخیره تنظیمات'); ?>
    </form>
    <?php
}

function weblazem_proposal_admin_list_ui($status_filter = '') {
    $base = admin_url('admin.php?page=weblazem-proposals');
    $statuses = weblazem_proposal_statuses();

    $args = array(
        'post_type'      => 'client_proposal',
        'post_status'    => 'publish',
        'posts_per_page' => 100,
        'orderby'        => 'date',
        'order'          => 'DESC',
    );
    if ($status_filter !== '' && isset($statuses[$status_filter])) {
        $args['meta_query'] = array(
            array(
                'key'   => '_proposal_status',
                'value' => $status_filter,
            ),
        );
    }

    $query = new WP_Query($args);
    ?>
    <div class="weblazem-proposal-admin__filters">
        <a class="button <?php echo $status_filter === '' ? 'button-primary' : ''; ?>" href="<?php echo esc_url($base); ?>">همه</a>
        <?php foreach ($statuses as $key => $label) : ?>
            <a class="button <?php echo $status_filter === $key ? 'button-primary' : ''; ?>" href="<?php echo esc_url($base . '&status=' . $key); ?>"><?php echo esc_html($label); ?></a>
        <?php endforeach; ?>
        <a class="button button-secondary" href="<?php echo esc_url($base . '&action=new'); ?>" style="margin-right:auto;">+ پیشنهاد جدید</a>
    </div>

    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th>کد</th>
                <th>عنوان</th>
                <th>مشتری</th>
                <th>موبایل</th>
                <th>مبلغ</th>
                <th>وضعیت</th>
                <th>تاریخ</th>
                <th>عملیات</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!$query->have_posts()) : ?>
                <tr><td colspan="8">پیشنهادی ثبت نشده است.</td></tr>
            <?php else : ?>
                <?php foreach ($query->posts as $post) :
                    $id = (int) $post->ID;
                    $st = get_post_meta($id, '_proposal_status', true) ?: 'draft';
                    $total = (int) get_post_meta($id, '_proposal_total', true);
                    ?>
                    <tr>
                        <td dir="ltr"><?php echo esc_html(get_post_meta($id, '_proposal_code', true)); ?></td>
                        <td><?php echo esc_html(get_post_meta($id, '_proposal_title', true) ?: get_the_title($id)); ?></td>
                        <td><?php echo esc_html(get_post_meta($id, '_proposal_client_name', true)); ?></td>
                        <td dir="ltr"><?php echo esc_html(get_post_meta($id, '_proposal_mobile', true)); ?></td>
                        <td><?php echo esc_html(weblazem_growth_format_toman($total)); ?></td>
                        <td><span class="weblazem-proposal-badge weblazem-proposal-badge--<?php echo esc_attr($st); ?>"><?php echo esc_html(weblazem_proposal_status_label($st)); ?></span></td>
                        <td><?php echo esc_html(get_the_date('Y-m-d H:i', $id)); ?></td>
                        <td>
                            <a href="<?php echo esc_url($base . '&action=edit&id=' . $id); ?>">ویرایش</a>
                            |
                            <a href="<?php echo esc_url(wp_nonce_url($base . '&delete=' . $id, 'weblazem_proposal_delete_' . $id)); ?>" onclick="return confirm('حذف شود؟');">حذف</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
    <?php
    wp_reset_postdata();
}

function weblazem_proposal_admin_form_ui($edit_id = 0) {
    $opts = weblazem_get_proposal_options();
    $portal = function_exists('weblazem_get_ticket_page_url') ? weblazem_get_ticket_page_url() : home_url('/sabt-ticket/');

    $title = '';
    $intro = '';
    $name = '';
    $mobile = '';
    $brief_id = 0;
    $items = array(array('title' => '', 'description' => '', 'price' => 0));
    $discount = 0;
    $delivery = (int) $opts['default_delivery'];
    $terms = $opts['default_terms'];
    $status = 'draft';
    $code = '';

    if ($edit_id) {
        $post = get_post($edit_id);
        if ($post && $post->post_type === 'client_proposal') {
            $title    = get_post_meta($edit_id, '_proposal_title', true) ?: get_the_title($edit_id);
            $intro    = get_post_meta($edit_id, '_proposal_intro', true);
            $name     = get_post_meta($edit_id, '_proposal_client_name', true);
            $mobile   = get_post_meta($edit_id, '_proposal_mobile', true);
            $brief_id = (int) get_post_meta($edit_id, '_proposal_brief_id', true);
            $saved    = weblazem_proposal_sanitize_items(get_post_meta($edit_id, '_proposal_items', true));
            if (!empty($saved)) {
                $items = $saved;
            }
            $discount = (int) get_post_meta($edit_id, '_proposal_discount', true);
            $delivery = (int) get_post_meta($edit_id, '_proposal_delivery_days', true) ?: $delivery;
            $terms    = get_post_meta($edit_id, '_proposal_terms', true) ?: $terms;
            $status   = get_post_meta($edit_id, '_proposal_status', true) ?: 'draft';
            $code     = get_post_meta($edit_id, '_proposal_code', true);
        } else {
            $edit_id = 0;
        }
    }

    $briefs = weblazem_proposal_get_brief_choices();
    $calc   = weblazem_proposal_calc_totals($items, $discount);
    ?>
    <div class="weblazem-proposal-admin__hint">
        <p>پس از ارسال، مشتری پیشنهاد را در حساب کاربری (<a href="<?php echo esc_url($portal); ?>" target="_blank" rel="noopener"><?php echo esc_html($portal); ?></a>) تب «پیشنهادها» می‌بیند.</p>
        <?php if ($code) : ?>
            <p>کد پیشنهاد: <strong dir="ltr"><?php echo esc_html($code); ?></strong> — وضعیت: <span class="weblazem-proposal-badge weblazem-proposal-badge--<?php echo esc_attr($status); ?>"><?php echo esc_html(weblazem_proposal_status_label($status)); ?></span></p>
        <?php endif; ?>
    </div>

    <form method="post" class="weblazem-proposal-form" id="weblazem-proposal-form">
        <?php wp_nonce_field('weblazem_proposal_save', 'weblazem_proposal_nonce'); ?>
        <input type="hidden" name="proposal_id" value="<?php echo esc_attr($edit_id); ?>" />

        <div class="weblazem-proposal-form__grid">
            <div class="weblazem-proposal-form__main">
                <table class="form-table">
                    <tr>
                        <th><label for="brief_id">بریف پروژه</label></th>
                        <td>
                            <select name="brief_id" id="brief_id" class="regular-text">
                                <option value="0">— بدون بریف / دستی —</option>
                                <?php foreach ($briefs as $brief) : ?>
                                    <option
                                        value="<?php echo esc_attr($brief['id']); ?>"
                                        data-name="<?php echo esc_attr($brief['name']); ?>"
                                        data-mobile="<?php echo esc_attr($brief['mobile']); ?>"
                                        <?php selected($brief_id, $brief['id']); ?>
                                    ><?php echo esc_html($brief['label']); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <p class="description">با انتخاب بریف، نام و موبایل پر می‌شود.</p>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="client_name">نام مشتری</label></th>
                        <td><input type="text" name="client_name" id="client_name" class="regular-text" value="<?php echo esc_attr($name); ?>" /></td>
                    </tr>
                    <tr>
                        <th><label for="client_mobile">موبایل مشتری</label></th>
                        <td><input type="text" name="client_mobile" id="client_mobile" class="regular-text" dir="ltr" value="<?php echo esc_attr($mobile); ?>" required placeholder="09121234567" /></td>
                    </tr>
                    <tr>
                        <th><label for="proposal_title">عنوان پیشنهاد</label></th>
                        <td><input type="text" name="proposal_title" id="proposal_title" class="large-text" value="<?php echo esc_attr($title); ?>" required /></td>
                    </tr>
                    <tr>
                        <th><label for="proposal_intro">مقدمه / توضیح</label></th>
                        <td><textarea name="proposal_intro" id="proposal_intro" class="large-text" rows="4"><?php echo esc_textarea($intro); ?></textarea></td>
                    </tr>
                </table>

                <h3>ردیف‌های خدمات</h3>
                <div id="weblazem-proposal-items" class="weblazem-proposal-items">
                    <?php foreach ($items as $i => $item) : ?>
                        <div class="weblazem-proposal-item">
                            <div class="weblazem-proposal-item__row">
                                <input type="text" name="item_title[]" placeholder="عنوان خدمت" value="<?php echo esc_attr($item['title']); ?>" />
                                <input type="number" name="item_price[]" class="weblazem-proposal-item__price" placeholder="قیمت (تومان)" min="0" dir="ltr" value="<?php echo esc_attr((int) $item['price']); ?>" />
                                <button type="button" class="button weblazem-proposal-item__remove" title="حذف">&times;</button>
                            </div>
                            <textarea name="item_description[]" rows="2" placeholder="توضیح مختصر"><?php echo esc_textarea($item['description']); ?></textarea>
                        </div>
                    <?php endforeach; ?>
                </div>
                <p><button type="button" class="button" id="weblazem-proposal-add-item">+ افزودن ردیف</button></p>
            </div>

            <div class="weblazem-proposal-form__side">
                <div class="weblazem-proposal-totals">
                    <h3>جمع‌بندی</h3>
                    <p>جمع جزء: <strong id="weblazem-proposal-subtotal-label"><?php echo esc_html(weblazem_growth_format_toman($calc['subtotal'])); ?></strong></p>
                    <p>
                        <label for="discount">تخفیف (تومان)</label>
                        <input type="number" name="discount" id="discount" min="0" dir="ltr" value="<?php echo esc_attr($discount); ?>" />
                    </p>
                    <p class="weblazem-proposal-totals__total">مبلغ نهایی: <strong id="weblazem-proposal-total-label"><?php echo esc_html(weblazem_growth_format_toman($calc['total'])); ?></strong></p>
                    <p>
                        <label for="delivery_days">زمان تحویل (روز)</label>
                        <input type="number" name="delivery_days" id="delivery_days" min="1" value="<?php echo esc_attr($delivery); ?>" />
                    </p>
                    <p>
                        <label for="terms">شرایط و ضوابط</label>
                        <textarea name="terms" id="terms" rows="8"><?php echo esc_textarea($terms); ?></textarea>
                    </p>
                    <div class="weblazem-proposal-form__actions">
                        <button type="submit" name="save_draft" value="1" class="button button-secondary button-large">ذخیره پیش‌نویس</button>
                        <button type="submit" name="send_to_client" value="1" class="button button-primary button-large">ارسال به مشتری</button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <template id="weblazem-proposal-item-tpl">
        <div class="weblazem-proposal-item">
            <div class="weblazem-proposal-item__row">
                <input type="text" name="item_title[]" placeholder="عنوان خدمت" value="" />
                <input type="number" name="item_price[]" class="weblazem-proposal-item__price" placeholder="قیمت (تومان)" min="0" dir="ltr" value="0" />
                <button type="button" class="button weblazem-proposal-item__remove" title="حذف">&times;</button>
            </div>
            <textarea name="item_description[]" rows="2" placeholder="توضیح مختصر"></textarea>
        </div>
    </template>
    <?php
}

/* --------------------------------------------------------------------------
 * Client AJAX (session-based)
 * -------------------------------------------------------------------------- */

function weblazem_ajax_proposal_get() {
    check_ajax_referer('weblazem_ticketing', 'nonce');

    if (!function_exists('weblazem_ticket_require_login')) {
        wp_send_json_error(array('message' => 'سیستم ورود در دسترس نیست.'), 500);
    }

    $mobile      = weblazem_ticket_require_login();
    $proposal_id = isset($_POST['proposal_id']) ? absint($_POST['proposal_id']) : 0;
    $post        = get_post($proposal_id);

    if (!$post || $post->post_type !== 'client_proposal') {
        wp_send_json_error(array('message' => 'پیشنهاد یافت نشد.'), 404);
    }

    if (!weblazem_proposal_user_owns($proposal_id, $mobile) && !current_user_can('manage_options')) {
        wp_send_json_error(array('message' => 'دسترسی مجاز نیست.'), 403);
    }

    $status = get_post_meta($proposal_id, '_proposal_status', true);
    if ($status === 'draft' && !current_user_can('manage_options')) {
        wp_send_json_error(array('message' => 'پیشنهاد هنوز ارسال نشده است.'), 403);
    }

    weblazem_proposal_mark_viewed($proposal_id);

    wp_send_json_success(
        array(
            'proposal' => weblazem_format_proposal_for_api(get_post($proposal_id), true),
        )
    );
}
add_action('wp_ajax_weblazem_proposal_get', 'weblazem_ajax_proposal_get');
add_action('wp_ajax_nopriv_weblazem_proposal_get', 'weblazem_ajax_proposal_get');

function weblazem_ajax_proposal_accept() {
    check_ajax_referer('weblazem_ticketing', 'nonce');

    $mobile      = weblazem_ticket_require_login();
    $proposal_id = isset($_POST['proposal_id']) ? absint($_POST['proposal_id']) : 0;
    $post        = get_post($proposal_id);

    if (!$post || $post->post_type !== 'client_proposal') {
        wp_send_json_error(array('message' => 'پیشنهاد یافت نشد.'), 404);
    }
    if (!weblazem_proposal_user_owns($proposal_id, $mobile)) {
        wp_send_json_error(array('message' => 'دسترسی مجاز نیست.'), 403);
    }

    $status = get_post_meta($proposal_id, '_proposal_status', true);
    if (!in_array($status, array('sent', 'viewed', 'changes_requested'), true)) {
        wp_send_json_error(array('message' => 'امکان پذیرش این پیشنهاد وجود ندارد.'), 400);
    }

    update_post_meta($proposal_id, '_proposal_status', 'accepted');
    update_post_meta($proposal_id, '_proposal_responded_at', current_time('mysql'));

    $brief_id = (int) get_post_meta($proposal_id, '_proposal_brief_id', true);
    if ($brief_id && function_exists('weblazem_crm_set_status')) {
        weblazem_crm_set_status($brief_id, 'won');
    }

    $project_id = weblazem_proposal_maybe_create_project($proposal_id);

    wp_send_json_success(
        array(
            'message'  => 'پیشنهاد با موفقیت پذیرفته شد. به‌زودی برای شروع پروژه با شما هماهنگ می‌کنیم.',
            'proposal' => weblazem_format_proposal_for_api(get_post($proposal_id), true),
            'projectId'=> $project_id,
        )
    );
}
add_action('wp_ajax_weblazem_proposal_accept', 'weblazem_ajax_proposal_accept');
add_action('wp_ajax_nopriv_weblazem_proposal_accept', 'weblazem_ajax_proposal_accept');

function weblazem_ajax_proposal_request_changes() {
    check_ajax_referer('weblazem_ticketing', 'nonce');

    $mobile      = weblazem_ticket_require_login();
    $proposal_id = isset($_POST['proposal_id']) ? absint($_POST['proposal_id']) : 0;
    $note        = isset($_POST['note']) ? sanitize_textarea_field(wp_unslash($_POST['note'])) : '';
    $post        = get_post($proposal_id);

    if (!$post || $post->post_type !== 'client_proposal') {
        wp_send_json_error(array('message' => 'پیشنهاد یافت نشد.'), 404);
    }
    if (!weblazem_proposal_user_owns($proposal_id, $mobile)) {
        wp_send_json_error(array('message' => 'دسترسی مجاز نیست.'), 403);
    }
    if (trim($note) === '') {
        wp_send_json_error(array('message' => 'لطفاً توضیحات درخواست تغییر را بنویسید.'), 400);
    }

    $status = get_post_meta($proposal_id, '_proposal_status', true);
    if (!in_array($status, array('sent', 'viewed', 'changes_requested'), true)) {
        wp_send_json_error(array('message' => 'امکان درخواست تغییر برای این پیشنهاد وجود ندارد.'), 400);
    }

    update_post_meta($proposal_id, '_proposal_status', 'changes_requested');
    update_post_meta($proposal_id, '_proposal_client_note', $note);
    update_post_meta($proposal_id, '_proposal_responded_at', current_time('mysql'));

    wp_send_json_success(
        array(
            'message'  => 'درخواست تغییر ثبت شد. تیم وب‌لازم بررسی و پیشنهاد به‌روز ارسال می‌کند.',
            'proposal' => weblazem_format_proposal_for_api(get_post($proposal_id), true),
        )
    );
}
add_action('wp_ajax_weblazem_proposal_request_changes', 'weblazem_ajax_proposal_request_changes');
add_action('wp_ajax_nopriv_weblazem_proposal_request_changes', 'weblazem_ajax_proposal_request_changes');
