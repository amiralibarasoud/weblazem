<?php
/**
 * Convert inbound requests (brief, proposal, leads…) into client projects.
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Convertible source types and how to read them.
 */
function weblazem_project_convert_sources() {
    return array(
        'project_brief' => array(
            'label' => 'بریف پروژه',
            'name'  => array('_brief_name'),
            'mobile'=> array('_brief_mobile'),
            'title' => 'brief',
        ),
        'client_proposal' => array(
            'label' => 'پیشنهاد قیمت',
            'name'  => array('_proposal_client_name'),
            'mobile'=> array('_proposal_mobile'),
            'title' => 'proposal',
        ),
        'consultation_request' => array(
            'label' => 'مشاوره',
            'name'  => array('_consult_full_name'),
            'mobile'=> array('_consult_mobile'),
            'title' => 'consult',
        ),
        'price_estimate_lead' => array(
            'label' => 'برآورد قیمت',
            'name'  => array('_pe_name'),
            'mobile'=> array('_pe_mobile'),
            'title' => 'price',
        ),
        'consult_booking' => array(
            'label' => 'رزرو مشاوره',
            'name'  => array('_booking_name'),
            'mobile'=> array('_booking_mobile'),
            'title' => 'booking',
        ),
        'referral_lead' => array(
            'label' => 'معرفی',
            'name'  => array('_ref_lead_name'),
            'mobile'=> array('_ref_lead_mobile'),
            'title' => 'referral',
        ),
        'contact_request' => array(
            'label' => 'تماس با ما',
            'name'  => array('_contact_first_name', '_contact_last_name'),
            'mobile'=> array('_contact_phone'),
            'title' => 'contact',
        ),
    );
}

function weblazem_project_convert_read_meta($post_id, $keys) {
    $parts = array();
    foreach ((array) $keys as $key) {
        $val = trim((string) get_post_meta($post_id, $key, true));
        if ($val !== '') {
            $parts[] = $val;
        }
    }
    return implode(' ', $parts);
}

function weblazem_project_convert_normalize_mobile($phone) {
    if (function_exists('weblazem_project_status_normalize_mobile')) {
        return weblazem_project_status_normalize_mobile($phone);
    }
    if (function_exists('weblazem_normalize_iran_mobile')) {
        return weblazem_normalize_iran_mobile($phone);
    }
    $phone = preg_replace('/\D+/', '', (string) $phone);
    if (strpos($phone, '98') === 0 && strlen($phone) === 12) {
        $phone = '0' . substr($phone, 2);
    }
    return $phone;
}

function weblazem_project_find_existing_for_source($source_type, $source_id) {
    $source_id = (int) $source_id;

    if ($source_type === 'project_brief') {
        $id = (int) get_post_meta($source_id, '_brief_project_id', true);
        if ($id && get_post($id)) {
            return $id;
        }
    }

    if ($source_type === 'client_proposal') {
        $id = (int) get_post_meta($source_id, '_proposal_project_id', true);
        if ($id && get_post($id)) {
            return $id;
        }
    }

    $crm_project = (int) get_post_meta($source_id, '_weblazem_crm_project_id', true);
    if ($crm_project && get_post($crm_project)) {
        return $crm_project;
    }

    $query = new WP_Query(
        array(
            'post_type'      => 'client_project',
            'post_status'    => 'publish',
            'posts_per_page' => 1,
            'fields'         => 'ids',
            'meta_query'     => array(
                'relation' => 'AND',
                array(
                    'key'   => '_project_source_type',
                    'value' => $source_type,
                ),
                array(
                    'key'   => '_project_source_id',
                    'value' => $source_id,
                ),
            ),
        )
    );

    $id = !empty($query->posts[0]) ? (int) $query->posts[0] : 0;
    wp_reset_postdata();
    return $id;
}

/**
 * Build title/name/mobile/note from a source post.
 *
 * @return array|WP_Error
 */
function weblazem_project_convert_extract_source($source_type, $source_id) {
    $sources = weblazem_project_convert_sources();
    if (!isset($sources[$source_type])) {
        return new WP_Error('source', 'این نوع درخواست قابل تبدیل به پروژه نیست.');
    }

    $post = get_post($source_id);
    if (!$post || $post->post_type !== $source_type) {
        return new WP_Error('missing', 'درخواست یافت نشد.');
    }

    $cfg    = $sources[$source_type];
    $name   = weblazem_project_convert_read_meta($source_id, $cfg['name']);
    $mobile = weblazem_project_convert_normalize_mobile(weblazem_project_convert_read_meta($source_id, $cfg['mobile']));
    $title  = get_the_title($source_id);
    $note   = '';

    switch ($source_type) {
        case 'project_brief':
            $types = function_exists('weblazem_get_start_project_options')
                ? weblazem_get_start_project_options()['project_types']
                : array();
            $type_key = get_post_meta($source_id, '_brief_project_type', true);
            $type_lbl = isset($types[$type_key]) ? $types[$type_key] : $type_key;
            $goal     = get_post_meta($source_id, '_brief_goal', true);
            $title    = trim(($name ? $name . ' — ' : '') . ($type_lbl ?: 'پروژه وب'));
            $note     = $goal;
            break;

        case 'client_proposal':
            $title = get_post_meta($source_id, '_proposal_title', true) ?: $title;
            $note  = get_post_meta($source_id, '_proposal_intro', true);
            break;

        case 'consultation_request':
            $subject = get_post_meta($source_id, '_consult_subject_label', true);
            $title   = 'پروژه از مشاوره' . ($subject ? ' — ' . $subject : '');
            break;

        case 'price_estimate_lead':
            $title = 'پروژه از برآورد قیمت' . ($name ? ' — ' . $name : '');
            break;

        case 'consult_booking':
            $date  = get_post_meta($source_id, '_booking_date', true);
            $title = 'پروژه از رزرو مشاوره' . ($date ? ' — ' . $date : '');
            $note  = get_post_meta($source_id, '_booking_note', true);
            break;

        case 'referral_lead':
            $svc   = get_post_meta($source_id, '_ref_lead_service', true);
            $title = 'پروژه از معرفی' . ($svc ? ' — ' . $svc : '');
            break;

        case 'contact_request':
            $title = 'پروژه از تماس' . ($name ? ' — ' . $name : '');
            $note  = get_post_meta($source_id, '_contact_message', true);
            break;
    }

    if ($mobile === '' || !preg_match('/^09\d{9}$/', $mobile)) {
        if (function_exists('weblazem_project_status_is_valid_mobile') && !weblazem_project_status_is_valid_mobile($mobile)) {
            return new WP_Error('mobile', 'موبایل معتبر روی این درخواست ثبت نشده است.');
        }
        if (!preg_match('/^09\d{9}$/', $mobile)) {
            return new WP_Error('mobile', 'موبایل معتبر روی این درخواست ثبت نشده است.');
        }
    }

    if ($title === '') {
        $title = 'پروژه مشتری ' . $mobile;
    }

    return array(
        'title'       => $title,
        'client_name' => $name,
        'mobile'      => $mobile,
        'note'        => is_string($note) ? wp_trim_words(wp_strip_all_tags($note), 40, '…') : '',
        'source_type' => $source_type,
        'source_id'   => (int) $source_id,
        'source_label'=> $cfg['label'],
    );
}

/**
 * Create a client_project from structured args.
 *
 * @param array $args title, client_name, mobile, note, source_type, source_id, proposal_id, brief_id
 * @return int|WP_Error project id
 */
function weblazem_create_client_project($args = array()) {
    if (!post_type_exists('client_project')) {
        return new WP_Error('cpt', 'سیستم پروژه فعال نیست.');
    }

    $title   = isset($args['title']) ? sanitize_text_field($args['title']) : '';
    $name    = isset($args['client_name']) ? sanitize_text_field($args['client_name']) : '';
    $mobile  = weblazem_project_convert_normalize_mobile(isset($args['mobile']) ? $args['mobile'] : '');
    $note    = isset($args['note']) ? sanitize_textarea_field($args['note']) : '';
    $source_type = isset($args['source_type']) ? sanitize_key($args['source_type']) : '';
    $source_id   = isset($args['source_id']) ? absint($args['source_id']) : 0;
    $proposal_id = isset($args['proposal_id']) ? absint($args['proposal_id']) : 0;
    $brief_id    = isset($args['brief_id']) ? absint($args['brief_id']) : 0;

    if ($title === '') {
        return new WP_Error('title', 'عنوان پروژه الزامی است.');
    }
    if ($mobile === '' || (function_exists('weblazem_project_status_is_valid_mobile') && !weblazem_project_status_is_valid_mobile($mobile))) {
        if (!preg_match('/^09\d{9}$/', $mobile)) {
            return new WP_Error('mobile', 'موبایل مشتری معتبر نیست.');
        }
    }

    if ($source_type && $source_id) {
        $existing = weblazem_project_find_existing_for_source($source_type, $source_id);
        if ($existing) {
            return $existing;
        }
    }

    if ($brief_id && !$source_id) {
        $existing = weblazem_project_find_existing_for_source('project_brief', $brief_id);
        if ($existing) {
            return $existing;
        }
    }

    $stages = function_exists('weblazem_project_status_default_stages')
        ? weblazem_project_status_default_stages()
        : array();

    if (!empty($stages[0])) {
        $stages[0]['done'] = true;
        $stages[0]['date'] = current_time('Y-m-d');
        $stages[0]['note'] = $note !== ''
            ? $note
            : ('ایجاد شده از ' . ($args['source_label'] ?? 'درخواست مشتری'));
    }

    $code = function_exists('weblazem_project_status_generate_code')
        ? weblazem_project_status_generate_code()
        : ('PRJ-' . strtoupper(wp_generate_password(6, false, false)));

    $project_id = wp_insert_post(
        array(
            'post_type'   => 'client_project',
            'post_status' => 'publish',
            'post_title'  => $title,
        ),
        true
    );

    if (is_wp_error($project_id) || !$project_id) {
        return is_wp_error($project_id) ? $project_id : new WP_Error('create', 'ایجاد پروژه ناموفق بود.');
    }

    update_post_meta($project_id, '_project_code', $code);
    update_post_meta($project_id, '_project_client_name', $name);
    update_post_meta($project_id, '_project_client_mobile', $mobile);
    update_post_meta($project_id, '_project_stage', 'briefing');
    update_post_meta($project_id, '_project_progress', 10);
    update_post_meta($project_id, '_project_stages', $stages);
    update_post_meta($project_id, '_project_files', array());
    update_post_meta($project_id, '_project_status', 'active');
    update_post_meta($project_id, '_project_created_at', current_time('mysql'));

    if ($source_type) {
        update_post_meta($project_id, '_project_source_type', $source_type);
    }
    if ($source_id) {
        update_post_meta($project_id, '_project_source_id', $source_id);
    }

    if ($source_type === 'project_brief' || $brief_id) {
        $bid = $brief_id ?: $source_id;
        update_post_meta($project_id, '_project_brief_id', $bid);
        update_post_meta($bid, '_brief_project_id', $project_id);
        update_post_meta($bid, '_brief_status', 'converted');
        if (function_exists('weblazem_crm_set_status')) {
            weblazem_crm_set_status($bid, 'following');
        }
    }

    if ($source_type === 'client_proposal' || $proposal_id) {
        $pid = $proposal_id ?: $source_id;
        update_post_meta($project_id, '_project_proposal_id', $pid);
        update_post_meta($pid, '_proposal_project_id', $project_id);
        $linked_brief = (int) get_post_meta($pid, '_proposal_brief_id', true);
        if ($linked_brief) {
            update_post_meta($project_id, '_project_brief_id', $linked_brief);
            update_post_meta($linked_brief, '_brief_project_id', $project_id);
            update_post_meta($linked_brief, '_brief_status', 'converted');
            if (function_exists('weblazem_crm_set_status')) {
                weblazem_crm_set_status($linked_brief, 'following');
            }
        }
    }

    if ($source_id && function_exists('weblazem_crm_set_status') && $source_type !== 'project_brief') {
        weblazem_crm_set_status($source_id, 'following');
        update_post_meta($source_id, '_weblazem_crm_project_id', $project_id);
    }

    return (int) $project_id;
}

/**
 * Convert any supported request into a client project.
 *
 * @return int|WP_Error
 */
function weblazem_convert_source_to_project($source_type, $source_id) {
    $existing = weblazem_project_find_existing_for_source($source_type, $source_id);
    if ($existing) {
        return $existing;
    }

    $extracted = weblazem_project_convert_extract_source($source_type, $source_id);
    if (is_wp_error($extracted)) {
        return $extracted;
    }

    $args = array(
        'title'        => $extracted['title'],
        'client_name'  => $extracted['client_name'],
        'mobile'       => $extracted['mobile'],
        'note'         => $extracted['note'],
        'source_type'  => $extracted['source_type'],
        'source_id'    => $extracted['source_id'],
        'source_label' => $extracted['source_label'],
    );

    if ($source_type === 'project_brief') {
        $args['brief_id'] = (int) $source_id;
    }
    if ($source_type === 'client_proposal') {
        $args['proposal_id'] = (int) $source_id;
        $brief_id = (int) get_post_meta($source_id, '_proposal_brief_id', true);
        if ($brief_id) {
            $args['brief_id'] = $brief_id;
        }
    }

    return weblazem_create_client_project($args);
}

function weblazem_ajax_admin_convert_to_project() {
    check_ajax_referer('weblazem_project_convert', 'nonce');

    if (!current_user_can('manage_options')) {
        wp_send_json_error(array('message' => 'دسترسی ندارید.'), 403);
    }

    $source_type = isset($_POST['source_type']) ? sanitize_key(wp_unslash($_POST['source_type'])) : '';
    $source_id   = isset($_POST['source_id']) ? absint($_POST['source_id']) : 0;

    $result = weblazem_convert_source_to_project($source_type, $source_id);
    if (is_wp_error($result)) {
        wp_send_json_error(array('message' => $result->get_error_message()), 400);
    }

    $edit = admin_url('admin.php?page=weblazem-project-status-options&tab=projects&edit=' . (int) $result);

    wp_send_json_success(
        array(
            'message'    => 'پروژه ساخته شد و در حساب کاربری مشتری نمایش داده می‌شود.',
            'projectId'  => (int) $result,
            'projectUrl' => $edit,
            'code'       => get_post_meta($result, '_project_code', true),
        )
    );
}
add_action('wp_ajax_weblazem_admin_convert_to_project', 'weblazem_ajax_admin_convert_to_project');

function weblazem_project_convert_admin_assets($hook) {
    $screen = function_exists('get_current_screen') ? get_current_screen() : null;
    $allowed_pages = array(
        'weblazem-start-project-options',
        'weblazem-leads-crm',
        'weblazem-proposals',
        'weblazem-project-status-options',
    );

    $ok = false;
    foreach ($allowed_pages as $slug) {
        if (strpos((string) $hook, $slug) !== false) {
            $ok = true;
            break;
        }
    }

    $convertible = array_keys(weblazem_project_convert_sources());
    if (!$ok && $screen && in_array($screen->post_type, $convertible, true)) {
        $ok = true;
    }

    if (!$ok) {
        return;
    }

    wp_enqueue_script(
        'weblazem-project-convert-admin',
        get_template_directory_uri() . '/assets/js/project-convert-admin.js',
        array(),
        '1.0.1',
        true
    );

    wp_localize_script(
        'weblazem-project-convert-admin',
        'weblazemProjectConvert',
        array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce'   => wp_create_nonce('weblazem_project_convert'),
        )
    );
}
add_action('admin_enqueue_scripts', 'weblazem_project_convert_admin_assets');

/**
 * Render convert button HTML for admin tables.
 */
function weblazem_project_convert_button_html($source_type, $source_id) {
    $existing = weblazem_project_find_existing_for_source($source_type, $source_id);
    if ($existing) {
        $url = admin_url('admin.php?page=weblazem-project-status-options&tab=projects&edit=' . $existing);
        $code = get_post_meta($existing, '_project_code', true);
        return '<a class="button button-small" href="' . esc_url($url) . '">پروژه ' . esc_html($code ?: ('#' . $existing)) . '</a>';
    }

    return '<button type="button" class="button button-primary button-small weblazem-convert-project-btn" data-source-type="' . esc_attr($source_type) . '" data-source-id="' . esc_attr($source_id) . '">تبدیل به پروژه</button>';
}

function weblazem_project_convert_add_meta_box() {
    foreach (array_keys(weblazem_project_convert_sources()) as $post_type) {
        if (!post_type_exists($post_type)) {
            continue;
        }
        add_meta_box(
            'weblazem_convert_to_project',
            'تبدیل به پروژه مشتری',
            'weblazem_project_convert_meta_box_render',
            $post_type,
            'side',
            'high'
        );
    }
}
add_action('add_meta_boxes', 'weblazem_project_convert_add_meta_box');

function weblazem_project_convert_meta_box_render($post) {
    echo '<p style="margin:0 0 10px;">با تبدیل، پروژه در تب «پروژه‌ها»ی حساب کاربری مشتری نمایش داده می‌شود.</p>';
    echo weblazem_project_convert_button_html($post->post_type, $post->ID);
}

function weblazem_project_convert_row_actions($actions, $post) {
    $sources = weblazem_project_convert_sources();
    if (!isset($sources[$post->post_type]) || !current_user_can('manage_options')) {
        return $actions;
    }

    $existing = weblazem_project_find_existing_for_source($post->post_type, $post->ID);
    if ($existing) {
        $url = admin_url('admin.php?page=weblazem-project-status-options&tab=projects&edit=' . $existing);
        $actions['weblazem_project'] = '<a href="' . esc_url($url) . '">مشاهده پروژه</a>';
        return $actions;
    }

    $actions['weblazem_convert'] = '<a href="#" class="weblazem-convert-project-btn" data-source-type="' . esc_attr($post->post_type) . '" data-source-id="' . esc_attr($post->ID) . '">تبدیل به پروژه</a>';
    return $actions;
}
add_filter('post_row_actions', 'weblazem_project_convert_row_actions', 20, 2);
