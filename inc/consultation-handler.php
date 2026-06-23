<?php
/**
 * Consultation form AJAX handler + sms.ir integration.
 */

function weblazem_normalize_iran_mobile($phone) {
    $digits = preg_replace('/\D+/', '', (string) $phone);

    if (strpos($digits, '98') === 0 && strlen($digits) >= 12) {
        $digits = '0' . substr($digits, 2);
    }

    if (preg_match('/^9\d{9}$/', $digits)) {
        $digits = '0' . $digits;
    }

    return $digits;
}

function weblazem_sms_ir_api_mobile($phone) {
    $digits = preg_replace('/\D+/', '', weblazem_normalize_iran_mobile($phone));

    if (strpos($digits, '0') === 0) {
        $digits = substr($digits, 1);
    }

    return $digits;
}

function weblazem_is_valid_iran_mobile($phone) {
    $normalized = weblazem_normalize_iran_mobile($phone);

    return (bool) preg_match('/^09\d{9}$/', $normalized);
}

function weblazem_build_consult_sms_parameters($form_data) {
    $parameters = array();
    $rows       = weblazem_get_consult_sms_parameters();

    foreach ($rows as $row) {
        if (empty($row['name'])) {
            continue;
        }

        $value = '';

        switch ($row['source']) {
            case 'first_name':
                $value = $form_data['first_name'];
                break;
            case 'last_name':
                $value = $form_data['last_name'];
                break;
            case 'full_name':
                $value = trim($form_data['first_name'] . ' ' . $form_data['last_name']);
                break;
            case 'mobile':
                $value = $form_data['mobile'];
                break;
            case 'page_url':
                $value = $form_data['page_url'];
                break;
            case 'static':
            default:
                $value = $row['static'] ?? '';
                break;
        }

        $parameters[] = array(
            'name'  => $row['name'],
            'value' => (string) $value,
        );
    }

    return $parameters;
}

function weblazem_send_sms_ir_verify($mobile, $template_id, $parameters, $api_key) {
    if (empty($api_key) || empty($template_id) || empty($mobile)) {
        return new WP_Error('sms_config', 'تنظیمات پیامک ناقص است.');
    }

    $body = array(
        'mobile'     => weblazem_sms_ir_api_mobile($mobile),
        'templateId' => (int) $template_id,
        'parameters' => array_values($parameters),
    );

    $response = wp_remote_post(
        'https://api.sms.ir/v1/send/verify/',
        array(
            'headers' => array(
                'Content-Type' => 'application/json',
                'Accept'       => 'text/plain',
                'x-api-key'    => $api_key,
            ),
            'body'    => wp_json_encode($body),
            'timeout' => 30,
        )
    );

    if (is_wp_error($response)) {
        return $response;
    }

    $code = wp_remote_retrieve_response_code($response);
    $raw  = wp_remote_retrieve_body($response);
    $json = json_decode($raw, true);

    if ($code >= 200 && $code < 300) {
        if (is_array($json) && isset($json['status']) && (int) $json['status'] !== 1) {
            $message = isset($json['message']) ? $json['message'] : 'خطای نامشخص sms.ir';
            return new WP_Error('sms_api', $message, $json);
        }

        return array(
            'success'  => true,
            'response' => $raw,
            'data'     => $json,
        );
    }

    $message = is_array($json) && !empty($json['message']) ? $json['message'] : $raw;

    return new WP_Error('sms_http', $message, array('code' => $code, 'body' => $raw));
}

function weblazem_ajax_submit_consultation() {
    check_ajax_referer('weblazem_consultation', 'nonce');

    $first_name = isset($_POST['first_name']) ? sanitize_text_field(wp_unslash($_POST['first_name'])) : '';
    $last_name  = isset($_POST['last_name']) ? sanitize_text_field(wp_unslash($_POST['last_name'])) : '';
    $mobile     = isset($_POST['mobile']) ? sanitize_text_field(wp_unslash($_POST['mobile'])) : '';
    $page_url   = isset($_POST['page_url']) ? esc_url_raw(wp_unslash($_POST['page_url'])) : '';

    if (empty($first_name) || empty($last_name) || empty($mobile)) {
        wp_send_json_error(
            array('message' => 'لطفاً تمام فیلدها را پر کنید.'),
            400
        );
    }

    if (!weblazem_is_valid_iran_mobile($mobile)) {
        wp_send_json_error(
            array('message' => 'شماره موبایل معتبر نیست.'),
            400
        );
    }

    $mobile = weblazem_normalize_iran_mobile($mobile);

    $form_data = array(
        'first_name' => $first_name,
        'last_name'  => $last_name,
        'mobile'     => $mobile,
        'page_url'   => $page_url,
        'ip'         => isset($_SERVER['REMOTE_ADDR']) ? sanitize_text_field(wp_unslash($_SERVER['REMOTE_ADDR'])) : '',
    );

    $api_key     = weblazem_get_consult_option('weblazem_consult_sms_api_key');
    $template_id = weblazem_get_consult_option('weblazem_consult_sms_template_id');
    $admin_phone = weblazem_get_consult_option('weblazem_consult_sms_admin_mobile');

    $parameters = weblazem_build_consult_sms_parameters($form_data);
    $sms_result = weblazem_send_sms_ir_verify($admin_phone, $template_id, $parameters, $api_key);

    $sms_status   = 'sent';
    $sms_response = '';

    if (is_wp_error($sms_result)) {
        $sms_status   = 'failed: ' . $sms_result->get_error_message();
        $sms_response = wp_json_encode($sms_result->get_error_data());
    } else {
        $sms_response = isset($sms_result['response']) ? $sms_result['response'] : '';
    }

    $form_data['sms_status']   = $sms_status;
    $form_data['sms_response'] = $sms_response;

    $saved = weblazem_save_consultation_request($form_data);

    if (is_wp_error($saved)) {
        wp_send_json_error(
            array('message' => weblazem_get_consult_option('weblazem_consult_error_message')),
            500
        );
    }

    if (is_wp_error($sms_result)) {
        wp_send_json_error(
            array(
                'message' => 'درخواست ثبت شد اما ارسال پیامک ناموفق بود: ' . $sms_result->get_error_message(),
                'saved'   => true,
            ),
            502
        );
    }

    wp_send_json_success(
        array(
            'message' => weblazem_get_consult_option('weblazem_consult_success_message'),
        )
    );
}
add_action('wp_ajax_weblazem_submit_consultation', 'weblazem_ajax_submit_consultation');
add_action('wp_ajax_nopriv_weblazem_submit_consultation', 'weblazem_ajax_submit_consultation');

function weblazem_enqueue_consultation_assets() {
    if (weblazem_get_consult_option('weblazem_consult_section_enabled', '1') !== '1') {
        return;
    }

    wp_enqueue_style(
        'weblazem-consultation',
        get_template_directory_uri() . '/assets/css/consultation.css',
        array(),
        '1.0.2'
    );

    wp_enqueue_script(
        'weblazem-consultation-modal',
        get_template_directory_uri() . '/assets/js/consultation-modal.js',
        array(),
        '1.0.2',
        true
    );

    wp_localize_script(
        'weblazem-consultation-modal',
        'weblazemConsult',
        array(
            'ajaxUrl'        => admin_url('admin-ajax.php'),
            'nonce'          => wp_create_nonce('weblazem_consultation'),
            'successMessage' => weblazem_get_consult_option('weblazem_consult_success_message'),
            'errorMessage'   => weblazem_get_consult_option('weblazem_consult_error_message'),
        )
    );
}
add_action('wp_enqueue_scripts', 'weblazem_enqueue_consultation_assets', 25);
