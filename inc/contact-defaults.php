<?php
/**
 * Contact page — default option values.
 */

function weblazem_contact_defaults() {
    return array(
        'page_title'           => 'ارتباط با ما',
        'address'              => 'تهران، خیابان ولیعصر، بالاتر از میدان ونک، برج نگین، طبقه ۱۲، واحد ۱۲۰۳',
        'phone'                => '۰۹۱۲ ۱۵۶ ۶۷ ۶۰',
        'email'                => 'weblazem@gmail.com',
        'illustration'         => '',
        'social_twitter'       => '#',
        'social_instagram'     => '#',
        'social_linkedin'      => '#',
        'social_telegram'      => '#',
        'label_first_name'     => 'نام',
        'label_last_name'      => 'نام خانوادگی',
        'label_email'          => 'ایمیل',
        'label_phone'          => 'شماره‌ی تماس',
        'label_message'        => 'ثبت پیام',
        'submit_text'          => 'ثبت پیام',
        'success_message'      => 'پیام شما با موفقیت ثبت شد. به زودی با شما تماس می‌گیریم.',
        'error_message'        => 'خطا در ثبت پیام. لطفاً دوباره تلاش کنید.',
        'sms_api_key'          => '',
        'sms_template_id'      => '',
        'sms_admin_mobile'     => '',
        'sms_use_consult_creds'=> '1',
    );
}

function weblazem_get_default_contact_sms_parameters() {
    return array(
        array('name' => 'NAME', 'source' => 'full_name', 'static' => ''),
        array('name' => 'MOBILE', 'source' => 'mobile', 'static' => ''),
        array('name' => 'EMAIL', 'source' => 'email', 'static' => ''),
    );
}

function weblazem_ensure_contact_defaults() {
    foreach (weblazem_contact_defaults() as $key => $value) {
        $option_key = 'weblazem_contact_' . $key;
        if (get_option($option_key) === false) {
            update_option($option_key, $value);
        }
    }
    if (get_option('weblazem_contact_sms_parameters') === false) {
        update_option('weblazem_contact_sms_parameters', weblazem_get_default_contact_sms_parameters());
    }
}
add_action('init', 'weblazem_ensure_contact_defaults', 14);

function weblazem_contact_option($key, $default = '') {
    $defaults = weblazem_contact_defaults();
    $fallback = $defaults[$key] ?? $default;
    return get_option('weblazem_contact_' . $key, $fallback);
}

function weblazem_get_contact_sms_parameters() {
    $params = get_option('weblazem_contact_sms_parameters');
    if (!is_array($params) || empty($params)) {
        return weblazem_get_default_contact_sms_parameters();
    }
    return $params;
}
