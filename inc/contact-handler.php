<?php
/**
 * Contact form — AJAX handler + sms.ir integration.
 */

function weblazem_get_contact_sms_credential($key) {
    $contact_val = weblazem_contact_option($key, '');
    if ($contact_val !== '') {
        return $contact_val;
    }
    if (weblazem_contact_option('sms_use_consult_creds', '1') === '1' && function_exists('weblazem_get_consult_option')) {
        $map = array(
            'sms_api_key'      => 'weblazem_consult_sms_api_key',
            'sms_admin_mobile' => 'weblazem_consult_sms_admin_mobile',
        );
        if (isset($map[$key])) {
            return weblazem_get_consult_option($map[$key]);
        }
    }
    return '';
}

function weblazem_build_contact_sms_parameters($form_data) {
    $parameters = array();
    $rows       = weblazem_get_contact_sms_parameters();

    foreach ($rows as $row) {
        if (empty($row['name'])) {
            continue;
        }

        $value = '';
        switch ($row['source'] ?? 'static') {
            case 'first_name':
                $value = $form_data['first_name'];
                break;
            case 'last_name':
                $value = $form_data['last_name'];
                break;
            case 'full_name':
                $value = trim($form_data['first_name'] . ' ' . $form_data['last_name']);
                break;
            case 'email':
                $value = $form_data['email'];
                break;
            case 'mobile':
            case 'phone':
                $value = $form_data['phone'];
                break;
            case 'message':
                $value = mb_substr($form_data['message'], 0, 50);
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

function weblazem_ajax_submit_contact() {
    check_ajax_referer('weblazem_contact_form', 'nonce');

    $first_name = isset($_POST['first_name']) ? sanitize_text_field(wp_unslash($_POST['first_name'])) : '';
    $last_name  = isset($_POST['last_name']) ? sanitize_text_field(wp_unslash($_POST['last_name'])) : '';
    $email      = isset($_POST['email']) ? sanitize_email(wp_unslash($_POST['email'])) : '';
    $phone      = isset($_POST['phone']) ? sanitize_text_field(wp_unslash($_POST['phone'])) : '';
    $message    = isset($_POST['message']) ? sanitize_textarea_field(wp_unslash($_POST['message'])) : '';

    if ($first_name === '' || $email === '' || $phone === '' || $message === '') {
        wp_send_json_error(array('message' => 'لطفاً فیلدهای الزامی را پر کنید.'), 400);
    }

    if (!is_email($email)) {
        wp_send_json_error(array('message' => 'ایمیل معتبر نیست.'), 400);
    }

    if (!weblazem_is_valid_iran_mobile($phone)) {
        wp_send_json_error(array('message' => 'شماره موبایل معتبر نیست.'), 400);
    }

    $phone = weblazem_normalize_iran_mobile($phone);

    $form_data = array(
        'first_name' => $first_name,
        'last_name'  => $last_name,
        'email'      => $email,
        'phone'      => $phone,
        'message'    => $message,
        'ip'         => isset($_SERVER['REMOTE_ADDR']) ? sanitize_text_field(wp_unslash($_SERVER['REMOTE_ADDR'])) : '',
    );

    $api_key     = weblazem_get_contact_sms_credential('sms_api_key');
    $template_id = weblazem_contact_option('sms_template_id', '');
    $admin_phone = weblazem_get_contact_sms_credential('sms_admin_mobile');

    $parameters = weblazem_build_contact_sms_parameters($form_data);
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

    $saved = weblazem_save_contact_request($form_data);

    if (is_wp_error($saved)) {
        wp_send_json_error(array('message' => weblazem_contact_option('error_message')), 500);
    }

    if (is_wp_error($sms_result)) {
        wp_send_json_error(array(
            'message' => 'پیام ثبت شد اما ارسال پیامک ناموفق بود: ' . $sms_result->get_error_message(),
            'saved'   => true,
        ), 502);
    }

    wp_send_json_success(array(
        'message' => weblazem_contact_option('success_message'),
    ));
}
add_action('wp_ajax_weblazem_submit_contact', 'weblazem_ajax_submit_contact');
add_action('wp_ajax_nopriv_weblazem_submit_contact', 'weblazem_ajax_submit_contact');

function weblazem_enqueue_contact_assets() {
    if (!weblazem_is_contact_page()) {
        return;
    }

    wp_enqueue_style(
        'weblazem-contact-page',
        get_template_directory_uri() . '/assets/css/contact-page.css',
        array('weblazem-home-style'),
        '1.0.0'
    );

    wp_enqueue_script(
        'weblazem-contact-form',
        get_template_directory_uri() . '/assets/js/contact-form.js',
        array(),
        '1.0.0',
        true
    );

    wp_localize_script('weblazem-contact-form', 'weblazemContact', array(
        'ajaxUrl'        => admin_url('admin-ajax.php'),
        'nonce'          => wp_create_nonce('weblazem_contact_form'),
        'successMessage' => weblazem_contact_option('success_message'),
        'errorMessage'   => weblazem_contact_option('error_message'),
    ));
}
add_action('wp_enqueue_scripts', 'weblazem_enqueue_contact_assets', 26);
