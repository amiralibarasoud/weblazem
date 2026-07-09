<?php
/**
 * Contact page — admin settings.
 */

require_once get_template_directory() . '/inc/contact-defaults.php';

function weblazem_register_contact_settings() {
    foreach (array_keys(weblazem_contact_defaults()) as $key) {
        register_setting('weblazem_contact_group', 'weblazem_contact_' . $key);
    }
    register_setting(
        'weblazem_contact_group',
        'weblazem_contact_sms_parameters',
        array('sanitize_callback' => 'weblazem_sanitize_contact_sms_parameters')
    );
}
add_action('admin_init', 'weblazem_register_contact_settings');

function weblazem_contact_handle_checkboxes() {
    if (!isset($_POST['option_page']) || $_POST['option_page'] !== 'weblazem_contact_group') {
        return;
    }
    if (!isset($_POST['weblazem_contact_sms_use_consult_creds'])) {
        update_option('weblazem_contact_sms_use_consult_creds', '0');
    }
}
add_action('admin_init', 'weblazem_contact_handle_checkboxes', 20);

function weblazem_sanitize_contact_sms_parameters($input) {
    if (!is_array($input)) {
        return weblazem_get_default_contact_sms_parameters();
    }
    $allowed_sources = array('first_name', 'last_name', 'full_name', 'email', 'phone', 'mobile', 'message', 'static');
    $sanitized = array();
    foreach ($input as $row) {
        if (!is_array($row) || empty($row['name'])) {
            continue;
        }
        $source = in_array($row['source'] ?? '', $allowed_sources, true) ? $row['source'] : 'static';
        $sanitized[] = array(
            'name'   => sanitize_text_field($row['name']),
            'source' => $source,
            'static' => sanitize_text_field($row['static'] ?? ''),
        );
    }
    return !empty($sanitized) ? $sanitized : weblazem_get_default_contact_sms_parameters();
}

function weblazem_contact_admin_scripts($hook) {
    if (strpos($hook, 'weblazem-contact-options') === false) {
        return;
    }
    wp_enqueue_media();
}
add_action('admin_enqueue_scripts', 'weblazem_contact_admin_scripts');

function weblazem_contact_opt($key) {
    return weblazem_contact_option($key);
}

function weblazem_contact_options_display() {
    $page_url   = weblazem_get_contact_page_url();
    $sms_params = weblazem_get_contact_sms_parameters();
    ?>
    <div class="wrap" dir="rtl">
        <div class="weblazem-admin-header">
            <div class="weblazem-admin-header-content">
                <h1>تنظیمات صفحه تماس با ما</h1>
                <p>
                    محتوای صفحه، فرم تماس و اطلاع‌رسانی پیامکی را مدیریت کنید.
                    <a href="<?php echo esc_url($page_url); ?>" target="_blank" rel="noopener">مشاهده صفحه</a>
                    &nbsp;|&nbsp;
                    <a href="<?php echo esc_url(admin_url('edit.php?post_type=contact_request')); ?>">مشاهده پیام‌های دریافتی</a>
                </p>
            </div>
        </div>

        <div class="weblazem-admin-content" style="margin-top:24px;">
            <form method="post" action="options.php">
                <?php settings_fields('weblazem_contact_group'); ?>

                <div class="weblazem-admin-card" style="margin-bottom:20px;">
                    <h3>اطلاعات صفحه</h3>
                    <?php weblazem_contact_admin_field('page_title', 'عنوان صفحه'); ?>
                    <?php weblazem_contact_admin_textarea('address', 'آدرس'); ?>
                    <?php weblazem_contact_admin_field('phone', 'شماره تماس'); ?>
                    <?php weblazem_contact_admin_field('email', 'ایمیل'); ?>
                    <?php weblazem_contact_admin_image('illustration', 'تصویر پاکت نامه'); ?>
                </div>

                <div class="weblazem-admin-card" style="margin-bottom:20px;">
                    <h3>شبکه‌های اجتماعی</h3>
                    <?php weblazem_contact_admin_field('social_twitter', 'لینک X (توییتر)'); ?>
                    <?php weblazem_contact_admin_field('social_instagram', 'لینک اینستاگرام'); ?>
                    <?php weblazem_contact_admin_field('social_linkedin', 'لینک لینکدین'); ?>
                    <?php weblazem_contact_admin_field('social_telegram', 'لینک تلگرام'); ?>
                </div>

                <div class="weblazem-admin-card" style="margin-bottom:20px;">
                    <h3>برچسب‌های فرم</h3>
                    <?php weblazem_contact_admin_field('label_first_name', 'برچسب نام'); ?>
                    <?php weblazem_contact_admin_field('label_last_name', 'برچسب نام خانوادگی'); ?>
                    <?php weblazem_contact_admin_field('label_email', 'برچسب ایمیل'); ?>
                    <?php weblazem_contact_admin_field('label_phone', 'برچسب شماره تماس'); ?>
                    <?php weblazem_contact_admin_field('label_message', 'برچسب پیام'); ?>
                    <?php weblazem_contact_admin_field('submit_text', 'متن دکمه ارسال'); ?>
                    <?php weblazem_contact_admin_field('success_message', 'پیام موفقیت'); ?>
                    <?php weblazem_contact_admin_field('error_message', 'پیام خطا'); ?>
                </div>

                <div class="weblazem-admin-card">
                    <div class="weblazem-admin-card-icon"><i class="fas fa-sms"></i></div>
                    <h3>اطلاع‌رسانی پیامکی (sms.ir)</h3>
                    <p class="description">با پر شدن فرم، پیامک به شماره مدیر ارسال می‌شود. می‌توانید از کلید API مشاوره استفاده کنید.</p>
                    <table class="form-table">
                        <tr>
                            <th>استفاده از کلید API مشاوره</th>
                            <td>
                                <input type="hidden" name="weblazem_contact_sms_use_consult_creds" value="0" />
                                <label>
                                    <input type="checkbox" name="weblazem_contact_sms_use_consult_creds" value="1" <?php checked(weblazem_contact_opt('sms_use_consult_creds'), '1'); ?> />
                                    اگر کلید API اینجا خالی باشد، از تنظیمات مودال مشاوره استفاده شود
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <th>کلید API</th>
                            <td><input type="text" name="weblazem_contact_sms_api_key" class="large-text" dir="ltr" value="<?php echo esc_attr(weblazem_contact_opt('sms_api_key')); ?>" autocomplete="off" /></td>
                        </tr>
                        <tr>
                            <th>شناسه قالب پیامک</th>
                            <td><input type="text" name="weblazem_contact_sms_template_id" class="regular-text" dir="ltr" value="<?php echo esc_attr(weblazem_contact_opt('sms_template_id')); ?>" /></td>
                        </tr>
                        <tr>
                            <th>موبایل مدیر</th>
                            <td>
                                <input type="text" name="weblazem_contact_sms_admin_mobile" class="regular-text" dir="ltr" value="<?php echo esc_attr(weblazem_contact_opt('sms_admin_mobile')); ?>" placeholder="09121234567" />
                            </td>
                        </tr>
                    </table>

                    <h4 style="margin-top:20px;">پارامترهای قالب پیامک</h4>
                    <div id="weblazem-contact-sms-params">
                        <?php foreach ($sms_params as $index => $param) : ?>
                            <div class="weblazem-sms-param-row" style="background:#f8f5fc;padding:12px 16px;border-radius:10px;margin-bottom:12px;border:1px solid #e8dff0;">
                                <p><strong>نام پارامتر</strong><br>
                                <input type="text" name="weblazem_contact_sms_parameters[<?php echo (int) $index; ?>][name]" class="regular-text" dir="ltr" value="<?php echo esc_attr($param['name']); ?>" /></p>
                                <p><strong>منبع داده</strong><br>
                                <select name="weblazem_contact_sms_parameters[<?php echo (int) $index; ?>][source]">
                                    <?php
                                    $sources = array(
                                        'first_name' => 'نام',
                                        'last_name'  => 'نام خانوادگی',
                                        'full_name'  => 'نام کامل',
                                        'email'      => 'ایمیل',
                                        'phone'      => 'موبایل',
                                        'message'    => 'پیام (۵۰ کاراکتر اول)',
                                        'static'     => 'مقدار ثابت',
                                    );
                                    foreach ($sources as $val => $lbl) :
                                        ?>
                                        <option value="<?php echo esc_attr($val); ?>" <?php selected($param['source'] ?? '', $val); ?>><?php echo esc_html($lbl); ?></option>
                                    <?php endforeach; ?>
                                </select></p>
                                <p><strong>مقدار ثابت</strong><br>
                                <input type="text" name="weblazem_contact_sms_parameters[<?php echo (int) $index; ?>][static]" class="large-text" value="<?php echo esc_attr($param['static'] ?? ''); ?>" /></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <button type="button" class="button" id="weblazem-add-contact-sms-param">افزودن پارامتر</button>
                </div>

                <?php submit_button('ذخیره تنظیمات'); ?>
            </form>
        </div>
    </div>
    <script>
    jQuery(function($) {
        var paramIndex = <?php echo count($sms_params); ?>;
        $('#weblazem-add-contact-sms-param').on('click', function() {
            var html = '<div class="weblazem-sms-param-row" style="background:#f8f5fc;padding:12px 16px;border-radius:10px;margin-bottom:12px;border:1px solid #e8dff0;">' +
                '<p><strong>نام پارامتر</strong><br><input type="text" name="weblazem_contact_sms_parameters[' + paramIndex + '][name]" class="regular-text" dir="ltr" value="" /></p>' +
                '<p><strong>منبع داده</strong><br><select name="weblazem_contact_sms_parameters[' + paramIndex + '][source]">' +
                '<option value="first_name">نام</option><option value="last_name">نام خانوادگی</option><option value="full_name">نام کامل</option>' +
                '<option value="email">ایمیل</option><option value="phone">موبایل</option><option value="message">پیام</option><option value="static">مقدار ثابت</option>' +
                '</select></p>' +
                '<p><strong>مقدار ثابت</strong><br><input type="text" name="weblazem_contact_sms_parameters[' + paramIndex + '][static]" class="large-text" value="" /></p></div>';
            $('#weblazem-contact-sms-params').append(html);
            paramIndex++;
        });
        $(document).on('click', '.contact-upload-img', function(e) {
            e.preventDefault();
            var target = $(this).data('target');
            var frame = wp.media({ title: 'انتخاب تصویر', button: { text: 'استفاده' }, multiple: false });
            frame.on('select', function() {
                var url = frame.state().get('selection').first().toJSON().url;
                $('#' + target).val(url);
                $('[data-for="' + target + '"]').html('<img src="' + url + '" style="max-width:200px;border-radius:8px;" alt="" />');
            });
            frame.open();
        });
        $(document).on('click', '.contact-remove-img', function(e) {
            e.preventDefault();
            var target = $(this).data('target');
            $('#' + target).val('');
            $('[data-for="' + target + '"]').empty();
        });
    });
    </script>
    <?php
}

function weblazem_contact_admin_field($key, $label) {
    echo '<p><label><strong>' . esc_html($label) . '</strong><br>';
    echo '<input type="text" name="weblazem_contact_' . esc_attr($key) . '" class="large-text" value="' . esc_attr(weblazem_contact_opt($key)) . '" /></label></p>';
}

function weblazem_contact_admin_textarea($key, $label) {
    echo '<p><label><strong>' . esc_html($label) . '</strong><br>';
    echo '<textarea name="weblazem_contact_' . esc_attr($key) . '" class="large-text" rows="3">' . esc_textarea(weblazem_contact_opt($key)) . '</textarea></label></p>';
}

function weblazem_contact_admin_image($key, $label) {
    $val = weblazem_contact_opt($key);
    $id  = 'contact_img_' . $key;
    echo '<p><strong>' . esc_html($label) . '</strong><br>';
    echo '<input type="hidden" id="' . esc_attr($id) . '" name="weblazem_contact_' . esc_attr($key) . '" value="' . esc_attr($val) . '" />';
    echo '<div class="contact-img-preview" data-for="' . esc_attr($id) . '" style="margin:8px 0;">';
    if ($val) {
        echo '<img src="' . esc_url($val) . '" style="max-width:200px;border-radius:8px;" alt="" />';
    }
    echo '</div>';
    echo '<button type="button" class="button contact-upload-img" data-target="' . esc_attr($id) . '">انتخاب تصویر</button> ';
    echo '<button type="button" class="button contact-remove-img" data-target="' . esc_attr($id) . '">حذف</button></p>';
}
