<?php
/**
 * Global consultation section + modal + SMS.ir settings.
 */

function weblazem_consultation_options_defaults() {
    return array(
        'weblazem_consult_section_enabled'          => '1',
        'weblazem_consult_section_home'             => '1',
        'weblazem_consult_section_portfolio_single' => '1',
        'weblazem_consult_float_enabled'            => '1',
        'weblazem_consult_float_text'               => 'مشاوره رایگان',
        'weblazem_consult_badge'                  => 'مشاوره تخصصی وب‌لازم',
        'weblazem_consult_title'                    => 'در صدر بازار خود قرار بگیرید',
        'weblazem_consult_text'                     => 'با استراتژی درست، طراحی حرفه‌ای وب‌سایت و بازاریابی دیجیتال می‌توانید در بازار رقابتی امروز جایگاه برتر کسب‌وکار خود را تثبیت کنید.',
        'weblazem_consult_image'                    => '',
        'weblazem_consult_phone'                    => '021-12345678',
        'weblazem_consult_btn_text'                 => 'ثبت درخواست مشاوره',
        'weblazem_consult_modal_title'              => 'درخواست مشاوره رایگان',
        'weblazem_consult_modal_subtitle'           => 'فرم زیر را پر کنید تا کارشناسان ما در اسرع وقت با شما تماس بگیرند.',
        'weblazem_consult_label_full_name'          => 'نام و نام خانوادگی',
        'weblazem_consult_label_mobile'             => 'شماره موبایل',
        'weblazem_consult_label_subject'            => 'موضوع',
        'weblazem_consult_submit_text'              => 'ارسال درخواست',
        'weblazem_consult_success_message'          => 'درخواست شما با موفقیت ثبت شد. به زودی با شما تماس می‌گیریم.',
        'weblazem_consult_error_message'            => 'خطا در ثبت درخواست. لطفاً دوباره تلاش کنید.',
        'weblazem_consult_sms_api_key'              => '',
        'weblazem_consult_sms_template_id'          => '',
        'weblazem_consult_sms_admin_mobile'         => '',
    );
}

function weblazem_get_default_consult_sms_parameters() {
    return array(
        array(
            'name'   => 'NAME',
            'source' => 'full_name',
            'static' => '',
        ),
        array(
            'name'   => 'MOBILE',
            'source' => 'mobile',
            'static' => '',
        ),
        array(
            'name'   => 'SUBJECT',
            'source' => 'subject',
            'static' => '',
        ),
    );
}

function weblazem_get_consult_subject_choices() {
    return array(
        'webdesign' => 'طراحی سایت',
        'seo'       => 'سئو',
        'content'   => 'تولید محتوا',
    );
}

function weblazem_get_consult_subject_label($key) {
    $choices = weblazem_get_consult_subject_choices();

    return isset($choices[$key]) ? $choices[$key] : '';
}

function weblazem_migrate_consultation_from_portfolio_single() {
    if (get_option('weblazem_consult_migrated') === '1') {
        return;
    }

    $map = array(
        'weblazem_portfolio_single_consult_title'    => 'weblazem_consult_title',
        'weblazem_portfolio_single_consult_text'     => 'weblazem_consult_text',
        'weblazem_portfolio_single_consult_image'    => 'weblazem_consult_image',
        'weblazem_portfolio_single_consult_phone'    => 'weblazem_consult_phone',
        'weblazem_portfolio_single_consult_btn_text' => 'weblazem_consult_btn_text',
    );

    foreach ($map as $old_key => $new_key) {
        $old = get_option($old_key);
        if ($old !== false && get_option($new_key) === false) {
            update_option($new_key, $old);
        }
    }

    update_option('weblazem_consult_migrated', '1');
}

function weblazem_ensure_consultation_options_defaults() {
    foreach (weblazem_consultation_options_defaults() as $key => $value) {
        if (get_option($key) === false) {
            update_option($key, $value);
        }
    }

    if (get_option('weblazem_consult_sms_parameters') === false) {
        update_option('weblazem_consult_sms_parameters', weblazem_get_default_consult_sms_parameters());
    }

    if (get_option('weblazem_consult_label_full_name') === false) {
        update_option('weblazem_consult_label_full_name', 'نام و نام خانوادگی');
    }

    if (get_option('weblazem_consult_label_subject') === false) {
        update_option('weblazem_consult_label_subject', 'موضوع');
    }

    weblazem_migrate_consult_sms_parameters_subject();
    weblazem_migrate_consultation_from_portfolio_single();
}

function weblazem_migrate_consult_sms_parameters_subject() {
    if (get_option('weblazem_consult_sms_migrated_subject') === '1') {
        return;
    }

    $params = get_option('weblazem_consult_sms_parameters');
    if (!is_array($params)) {
        $params = weblazem_get_default_consult_sms_parameters();
    }

    $has_subject = false;
    foreach ($params as $param) {
        if (($param['source'] ?? '') === 'subject') {
            $has_subject = true;
            break;
        }
    }

    if (!$has_subject) {
        $params[] = array(
            'name'   => 'SUBJECT',
            'source' => 'subject',
            'static' => '',
        );
        update_option('weblazem_consult_sms_parameters', $params);
    }

    update_option('weblazem_consult_sms_migrated_subject', '1');
}
add_action('init', 'weblazem_ensure_consultation_options_defaults', 13);

function weblazem_consultation_options_menu() {
    add_submenu_page(
        'weblazem-theme-options',
        'مودال درخواست مشاوره',
        'مودال درخواست مشاوره',
        'manage_options',
        'weblazem-consultation-options',
        'weblazem_consultation_options_display'
    );

    add_submenu_page(
        'weblazem-theme-options',
        'درخواست‌های مشاوره',
        'درخواست‌های مشاوره',
        'manage_options',
        'edit.php?post_type=consultation_request'
    );
}
add_action('admin_menu', 'weblazem_consultation_options_menu', 18);

function weblazem_sanitize_consult_sms_parameters($input) {
    if (!is_array($input)) {
        return weblazem_get_default_consult_sms_parameters();
    }

    $allowed_sources = array('first_name', 'last_name', 'full_name', 'mobile', 'subject', 'page_url', 'static');
    $sanitized       = array();

    foreach ($input as $row) {
        if (empty($row['name'])) {
            continue;
        }

        $source = sanitize_key($row['source'] ?? 'static');
        if (!in_array($source, $allowed_sources, true)) {
            $source = 'static';
        }

        $sanitized[] = array(
            'name'   => sanitize_text_field($row['name']),
            'source' => $source,
            'static' => sanitize_text_field($row['static'] ?? ''),
        );
    }

    return !empty($sanitized) ? $sanitized : weblazem_get_default_consult_sms_parameters();
}

function weblazem_sanitize_consult_checkbox($value) {
    return $value === '1' ? '1' : '0';
}

function weblazem_register_consultation_settings() {
    $checkboxes = array(
        'weblazem_consult_section_enabled',
        'weblazem_consult_section_home',
        'weblazem_consult_section_portfolio_single',
        'weblazem_consult_float_enabled',
    );

    foreach (weblazem_consultation_options_defaults() as $key => $default) {
        $args = array();
        if (in_array($key, $checkboxes, true)) {
            $args['sanitize_callback'] = 'weblazem_sanitize_consult_checkbox';
        }
        register_setting('weblazem_consultation_group', $key, $args);
    }

    register_setting(
        'weblazem_consultation_group',
        'weblazem_consult_sms_parameters',
        array('sanitize_callback' => 'weblazem_sanitize_consult_sms_parameters')
    );
}
add_action('admin_init', 'weblazem_register_consultation_settings');

function weblazem_consultation_options_admin_scripts($hook) {
    if (strpos($hook, 'weblazem-consultation-options') === false) {
        return;
    }

    wp_enqueue_media();
}
add_action('admin_enqueue_scripts', 'weblazem_consultation_options_admin_scripts');

function weblazem_get_consult_option($key, $default = '') {
    $defaults = weblazem_consultation_options_defaults();
    $fallback = isset($defaults[$key]) ? $defaults[$key] : $default;

    return get_option($key, $fallback);
}

function weblazem_get_consult_sms_parameters() {
    $params = get_option('weblazem_consult_sms_parameters');

    if (!is_array($params) || empty($params)) {
        return weblazem_get_default_consult_sms_parameters();
    }

    return $params;
}

function weblazem_should_show_consult_section($context = '') {
    if (weblazem_get_consult_option('weblazem_consult_section_enabled', '1') !== '1') {
        return false;
    }

    if ($context === 'home') {
        return weblazem_get_consult_option('weblazem_consult_section_home', '1') === '1';
    }

    if ($context === 'portfolio_single') {
        return weblazem_get_consult_option('weblazem_consult_section_portfolio_single', '1') === '1';
    }

    return true;
}

function weblazem_should_show_consult_floating_btn() {
    if (weblazem_get_consult_option('weblazem_consult_section_enabled', '1') !== '1') {
        return false;
    }

    if (weblazem_get_consult_option('weblazem_consult_float_enabled', '1') !== '1') {
        return false;
    }

    return true;
}

function weblazem_consultation_options_display() {
    $defaults = weblazem_consultation_options_defaults();
    $opts     = array();

    foreach ($defaults as $key => $default) {
        $opts[$key] = get_option($key, $default);
    }

    $sms_params = weblazem_get_consult_sms_parameters();
    ?>
    <div class="wrap" dir="rtl">
        <div class="weblazem-admin-header">
            <div class="weblazem-admin-header-content">
                <h1>مودال درخواست مشاوره</h1>
                <p>تنظیمات بخش مشاوره، مودال فرم و ارسال پیامک sms.ir در سراسر سایت.</p>
            </div>
        </div>

        <div class="weblazem-admin-content" style="margin-top:24px;">
            <form method="post" action="options.php">
                <?php settings_fields('weblazem_consultation_group'); ?>
                <input type="hidden" name="weblazem_consult_section_enabled" value="0" />
                <input type="hidden" name="weblazem_consult_section_home" value="0" />
                <input type="hidden" name="weblazem_consult_section_portfolio_single" value="0" />
                <input type="hidden" name="weblazem_consult_float_enabled" value="0" />

                <div class="weblazem-admin-card">
                    <div class="weblazem-admin-card-icon"><i class="fas fa-eye"></i></div>
                    <h3>نمایش بخش</h3>
                    <table class="form-table">
                        <tr>
                            <th>فعال‌سازی کلی</th>
                            <td><label><input type="checkbox" name="weblazem_consult_section_enabled" value="1" <?php checked($opts['weblazem_consult_section_enabled'], '1'); ?> /> نمایش بخش و مودال مشاوره</label></td>
                        </tr>
                        <tr>
                            <th>صفحه اصلی</th>
                            <td><label><input type="checkbox" name="weblazem_consult_section_home" value="1" <?php checked($opts['weblazem_consult_section_home'], '1'); ?> /> نمایش سکشن مشاوره در صفحه اصلی</label></td>
                        </tr>
                        <tr>
                            <th>نمونه کار تکی</th>
                            <td><label><input type="checkbox" name="weblazem_consult_section_portfolio_single" value="1" <?php checked($opts['weblazem_consult_section_portfolio_single'], '1'); ?> /> نمایش سکشن مشاوره در صفحه جزئیات نمونه کار</label></td>
                        </tr>
                        <tr>
                            <th>دکمه شناور</th>
                            <td>
                                <label><input type="checkbox" name="weblazem_consult_float_enabled" value="1" <?php checked($opts['weblazem_consult_float_enabled'], '1'); ?> /> نمایش دکمه شناور در تمام صفحات</label>
                                <p class="description">نوار شناور وسط‌چین پایین صفحه — شماره تماس و دکمه مودال مشاوره.</p>
                            </td>
                        </tr>
                        <tr>
                            <th>متن دکمه شناور</th>
                            <td><input type="text" name="weblazem_consult_float_text" class="regular-text" value="<?php echo esc_attr($opts['weblazem_consult_float_text']); ?>" /></td>
                        </tr>
                    </table>
                    <p class="description">دکمه‌هایی با کلاس <code>weblazem-consult-trigger</code> در کل سایت مودال را باز می‌کنند.</p>
                </div>

                <div class="weblazem-admin-card">
                    <div class="weblazem-admin-card-icon"><i class="fas fa-handshake"></i></div>
                    <h3>سکشن مشاوره (صفحه اصلی و نمونه کار)</h3>
                    <table class="form-table">
                        <tr>
                            <th>برچسب</th>
                            <td><input type="text" name="weblazem_consult_badge" class="regular-text" value="<?php echo esc_attr($opts['weblazem_consult_badge']); ?>" /></td>
                        </tr>
                        <tr>
                            <th>عنوان</th>
                            <td><input type="text" name="weblazem_consult_title" class="large-text" value="<?php echo esc_attr($opts['weblazem_consult_title']); ?>" /></td>
                        </tr>
                        <tr>
                            <th>متن</th>
                            <td><textarea name="weblazem_consult_text" class="large-text" rows="3"><?php echo esc_textarea($opts['weblazem_consult_text']); ?></textarea></td>
                        </tr>
                        <tr>
                            <th>تصویر</th>
                            <td>
                                <input type="hidden" id="weblazem_consult_image" name="weblazem_consult_image" value="<?php echo esc_attr($opts['weblazem_consult_image']); ?>" />
                                <div id="weblazem-consult-image-preview" style="margin-bottom:10px;">
                                    <?php if (!empty($opts['weblazem_consult_image'])) : ?>
                                        <img src="<?php echo esc_url($opts['weblazem_consult_image']); ?>" style="max-width:180px;border-radius:12px;" alt="" />
                                    <?php endif; ?>
                                </div>
                                <button type="button" class="button button-primary" id="weblazem_upload_consult_image">انتخاب تصویر</button>
                                <button type="button" class="button" id="weblazem_remove_consult_image">حذف</button>
                            </td>
                        </tr>
                        <tr>
                            <th>شماره تماس</th>
                            <td><input type="text" name="weblazem_consult_phone" class="regular-text" value="<?php echo esc_attr($opts['weblazem_consult_phone']); ?>" /></td>
                        </tr>
                        <tr>
                            <th>متن دکمه</th>
                            <td><input type="text" name="weblazem_consult_btn_text" class="regular-text" value="<?php echo esc_attr($opts['weblazem_consult_btn_text']); ?>" /></td>
                        </tr>
                    </table>
                </div>

                <div class="weblazem-admin-card">
                    <div class="weblazem-admin-card-icon"><i class="fas fa-window-restore"></i></div>
                    <h3>مودال فرم</h3>
                    <table class="form-table">
                        <tr>
                            <th>عنوان مودال</th>
                            <td><input type="text" name="weblazem_consult_modal_title" class="large-text" value="<?php echo esc_attr($opts['weblazem_consult_modal_title']); ?>" /></td>
                        </tr>
                        <tr>
                            <th>زیرعنوان</th>
                            <td><textarea name="weblazem_consult_modal_subtitle" class="large-text" rows="2"><?php echo esc_textarea($opts['weblazem_consult_modal_subtitle']); ?></textarea></td>
                        </tr>
                        <tr>
                            <th>برچسب نام و نام خانوادگی</th>
                            <td><input type="text" name="weblazem_consult_label_full_name" class="regular-text" value="<?php echo esc_attr($opts['weblazem_consult_label_full_name']); ?>" /></td>
                        </tr>
                        <tr>
                            <th>برچسب موبایل</th>
                            <td><input type="text" name="weblazem_consult_label_mobile" class="regular-text" value="<?php echo esc_attr($opts['weblazem_consult_label_mobile']); ?>" /></td>
                        </tr>
                        <tr>
                            <th>برچسب موضوع</th>
                            <td>
                                <input type="text" name="weblazem_consult_label_subject" class="regular-text" value="<?php echo esc_attr($opts['weblazem_consult_label_subject']); ?>" />
                                <p class="description">گزینه‌ها: طراحی سایت، سئو، تولید محتوا</p>
                            </td>
                        </tr>
                        <tr>
                            <th>متن دکمه ارسال</th>
                            <td><input type="text" name="weblazem_consult_submit_text" class="regular-text" value="<?php echo esc_attr($opts['weblazem_consult_submit_text']); ?>" /></td>
                        </tr>
                        <tr>
                            <th>پیام موفقیت</th>
                            <td><textarea name="weblazem_consult_success_message" class="large-text" rows="2"><?php echo esc_textarea($opts['weblazem_consult_success_message']); ?></textarea></td>
                        </tr>
                        <tr>
                            <th>پیام خطا</th>
                            <td><textarea name="weblazem_consult_error_message" class="large-text" rows="2"><?php echo esc_textarea($opts['weblazem_consult_error_message']); ?></textarea></td>
                        </tr>
                    </table>
                </div>

                <div class="weblazem-admin-card">
                    <div class="weblazem-admin-card-icon"><i class="fas fa-sms"></i></div>
                    <h3>وب‌سرویس sms.ir (ارسال سریع / Verify)</h3>
                    <p class="description">طبق مستندات sms.ir از endpoint <code>POST /v1/send/verify/</code> با کلید API و شناسه قالب استفاده می‌شود.</p>
                    <table class="form-table">
                        <tr>
                            <th>کلید API (x-api-key)</th>
                            <td><input type="text" name="weblazem_consult_sms_api_key" class="large-text" dir="ltr" value="<?php echo esc_attr($opts['weblazem_consult_sms_api_key']); ?>" autocomplete="off" /></td>
                        </tr>
                        <tr>
                            <th>شناسه قالب (templateId)</th>
                            <td><input type="text" name="weblazem_consult_sms_template_id" class="regular-text" dir="ltr" value="<?php echo esc_attr($opts['weblazem_consult_sms_template_id']); ?>" /></td>
                        </tr>
                        <tr>
                            <th>موبایل مدیر (گیرنده پیامک)</th>
                            <td>
                                <input type="text" name="weblazem_consult_sms_admin_mobile" class="regular-text" dir="ltr" value="<?php echo esc_attr($opts['weblazem_consult_sms_admin_mobile']); ?>" placeholder="09121234567" />
                                <p class="description">پیامک اطلاع‌رسانی درخواست مشاوره به این شماره ارسال می‌شود.</p>
                            </td>
                        </tr>
                    </table>

                    <h4 style="margin-top:20px;">پارامترهای قالب پیامک</h4>
                    <p class="description">نام هر پارامتر باید دقیقاً مطابق قالب تعریف‌شده در پنل sms.ir باشد.</p>
                    <div id="weblazem-consult-sms-params">
                        <?php foreach ($sms_params as $index => $param) : ?>
                            <div class="weblazem-sms-param-row" style="background:#f8f5fc;padding:12px 16px;border-radius:10px;margin-bottom:12px;border:1px solid #e8dff0;">
                                <label>نام پارامتر در قالب</label>
                                <input type="text" name="weblazem_consult_sms_parameters[<?php echo (int) $index; ?>][name]" class="regular-text" dir="ltr" value="<?php echo esc_attr($param['name']); ?>" />
                                <label style="margin-top:8px;display:block;">منبع مقدار</label>
                                <select name="weblazem_consult_sms_parameters[<?php echo (int) $index; ?>][source]">
                                    <option value="full_name" <?php selected($param['source'], 'full_name'); ?>>نام کامل (نام + نام خانوادگی)</option>
                                    <option value="first_name" <?php selected($param['source'], 'first_name'); ?>>نام</option>
                                    <option value="last_name" <?php selected($param['source'], 'last_name'); ?>>نام خانوادگی</option>
                                    <option value="mobile" <?php selected($param['source'], 'mobile'); ?>>شماره موبایل</option>
                                    <option value="subject" <?php selected($param['source'], 'subject'); ?>>موضوع درخواست</option>
                                    <option value="page_url" <?php selected($param['source'], 'page_url'); ?>>آدرس صفحه</option>
                                    <option value="static" <?php selected($param['source'], 'static'); ?>>متن ثابت</option>
                                </select>
                                <label style="margin-top:8px;display:block;">متن ثابت (در صورت انتخاب منبع ثابت)</label>
                                <input type="text" name="weblazem_consult_sms_parameters[<?php echo (int) $index; ?>][static]" class="large-text" value="<?php echo esc_attr($param['static'] ?? ''); ?>" />
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <button type="button" class="button" id="weblazem-add-sms-param">افزودن پارامتر</button>
                </div>

                <?php submit_button('ذخیره تنظیمات'); ?>
            </form>
        </div>
    </div>

    <script>
    jQuery(function($) {
        var frame;
        $('#weblazem_upload_consult_image').on('click', function(e) {
            e.preventDefault();
            if (frame) { frame.open(); return; }
            frame = wp.media({
                title: 'انتخاب تصویر مشاوره',
                button: { text: 'استفاده از این تصویر' },
                multiple: false
            });
            frame.on('select', function() {
                var url = frame.state().get('selection').first().toJSON().url;
                $('#weblazem_consult_image').val(url);
                $('#weblazem-consult-image-preview').html('<img src="' + url + '" style="max-width:180px;border-radius:12px;" alt="" />');
            });
            frame.open();
        });
        $('#weblazem_remove_consult_image').on('click', function(e) {
            e.preventDefault();
            $('#weblazem_consult_image').val('');
            $('#weblazem-consult-image-preview').empty();
        });

        var paramIndex = <?php echo count($sms_params); ?>;
        $('#weblazem-add-sms-param').on('click', function() {
            var html = '<div class="weblazem-sms-param-row" style="background:#f8f5fc;padding:12px 16px;border-radius:10px;margin-bottom:12px;border:1px solid #e8dff0;">' +
                '<label>نام پارامتر در قالب</label>' +
                '<input type="text" name="weblazem_consult_sms_parameters[' + paramIndex + '][name]" class="regular-text" dir="ltr" value="" />' +
                '<label style="margin-top:8px;display:block;">منبع مقدار</label>' +
                '<select name="weblazem_consult_sms_parameters[' + paramIndex + '][source]">' +
                '<option value="full_name">نام کامل (نام + نام خانوادگی)</option>' +
                '<option value="first_name">نام</option>' +
                '<option value="last_name">نام خانوادگی</option>' +
                '<option value="mobile">شماره موبایل</option>' +
                '<option value="subject">موضوع درخواست</option>' +
                '<option value="page_url">آدرس صفحه</option>' +
                '<option value="static">متن ثابت</option>' +
                '</select>' +
                '<label style="margin-top:8px;display:block;">متن ثابت</label>' +
                '<input type="text" name="weblazem_consult_sms_parameters[' + paramIndex + '][static]" class="large-text" value="" />' +
                '</div>';
            $('#weblazem-consult-sms-params').append(html);
            paramIndex++;
        });
    });
    </script>
    <?php
}
