<?php
/**
 * Pricing / tariffs page — admin settings.
 */

require_once get_template_directory() . '/inc/pricing-defaults.php';

function weblazem_pricing_options_menu() {
    add_submenu_page(
        'weblazem-theme-options',
        'تنظیمات خدمات و تعرفه‌ها',
        '  خدمات و تعرفه‌ها',
        'manage_options',
        'weblazem-pricing-options',
        'weblazem_pricing_options_display'
    );
}
add_action('admin_menu', 'weblazem_pricing_options_menu', 23);

function weblazem_register_pricing_settings() {
    $defaults = weblazem_pricing_defaults();

    foreach (array_keys($defaults) as $key) {
        $args = array();
        if ($key === 'consult_btn_modal') {
            $args['sanitize_callback'] = 'weblazem_sanitize_pricing_checkbox';
        }
        register_setting('weblazem_pricing_group', 'weblazem_pricing_' . $key, $args);
    }

    register_setting('weblazem_pricing_group', 'weblazem_pricing_categories', array('sanitize_callback' => 'weblazem_sanitize_pricing_categories'));
    register_setting('weblazem_pricing_group', 'weblazem_pricing_service_tariffs', array('sanitize_callback' => 'weblazem_sanitize_pricing_service_tariffs'));
    register_setting('weblazem_pricing_group', 'weblazem_pricing_webdesign_plans', array('sanitize_callback' => 'weblazem_sanitize_pricing_webdesign_plans'));

    foreach (weblazem_get_pricing_sections_config() as $key => $label) {
        register_setting('weblazem_pricing_group', 'weblazem_pricing_section_' . $key . '_enabled');
    }
}
add_action('admin_init', 'weblazem_register_pricing_settings');

function weblazem_sanitize_pricing_checkbox($value) {
    return $value === '1' ? '1' : '0';
}

function weblazem_sanitize_pricing_categories($input) {
    if (!is_array($input)) {
        return weblazem_get_default_pricing_categories();
    }
    $out = array();
    foreach ($input as $row) {
        if (empty($row['title'])) {
            continue;
        }
        $out[] = array(
            'title' => sanitize_text_field($row['title']),
            'url'   => esc_url_raw($row['url'] ?? ''),
        );
    }
    return !empty($out) ? $out : weblazem_get_default_pricing_categories();
}

function weblazem_sanitize_pricing_service_tariffs($input) {
    if (!is_array($input)) {
        return weblazem_get_default_pricing_service_tariffs();
    }
    $out = array();
    foreach ($input as $row) {
        if (empty($row['title'])) {
            continue;
        }
        $out[] = array(
            'title'        => sanitize_text_field($row['title']),
            'description'  => sanitize_textarea_field($row['description'] ?? ''),
            'image'        => esc_url_raw($row['image'] ?? ''),
            'button_text'  => sanitize_text_field($row['button_text'] ?? 'نمایش بیشتر'),
            'button_url'   => esc_url_raw($row['button_url'] ?? ''),
            'button_modal' => (!empty($row['button_modal']) && $row['button_modal'] === '1') ? '1' : '0',
        );
    }
    return !empty($out) ? $out : weblazem_get_default_pricing_service_tariffs();
}

function weblazem_sanitize_pricing_webdesign_plans($input) {
    if (!is_array($input)) {
        return weblazem_get_default_pricing_webdesign_plans();
    }

    $out = array();
    foreach ($input as $row) {
        $features = array();
        if (!empty($row['features']) && is_array($row['features'])) {
            foreach ($row['features'] as $feature) {
                $feature = sanitize_text_field($feature);
                if ($feature !== '') {
                    $features[] = $feature;
                }
            }
        }

        if (empty($row['title']) && empty($row['price'])) {
            continue;
        }

        $out[] = array(
            'title'        => sanitize_text_field($row['title'] ?? ''),
            'price'        => sanitize_text_field($row['price'] ?? ''),
            'features'     => $features,
            'button_text'  => sanitize_text_field($row['button_text'] ?? 'مشاوره رایگان'),
            'button_modal' => (!empty($row['button_modal']) && $row['button_modal'] === '1') ? '1' : '0',
            'button_url'   => esc_url_raw($row['button_url'] ?? ''),
        );
    }

    return !empty($out) ? $out : weblazem_get_default_pricing_webdesign_plans();
}

function weblazem_pricing_handle_section_checkboxes() {
    if (!isset($_POST['option_page']) || $_POST['option_page'] !== 'weblazem_pricing_group') {
        return;
    }
    foreach (weblazem_get_pricing_sections_config() as $key => $label) {
        $option_key = 'weblazem_pricing_section_' . $key . '_enabled';
        if (!isset($_POST[$option_key])) {
            update_option($option_key, '0');
        }
    }
}
add_action('admin_init', 'weblazem_pricing_handle_section_checkboxes', 20);

function weblazem_pricing_admin_scripts($hook) {
    if (strpos($hook, 'weblazem-pricing-options') === false) {
        return;
    }
    wp_enqueue_media();
}
add_action('admin_enqueue_scripts', 'weblazem_pricing_admin_scripts');

function weblazem_pricing_opt($key) {
    $defaults = weblazem_pricing_defaults();
    return get_option('weblazem_pricing_' . $key, $defaults[$key] ?? '');
}

function weblazem_pricing_options_display() {
    $page_url        = weblazem_get_pricing_page_url();
    $categories      = weblazem_get_pricing_categories();
    $service_tariffs = weblazem_get_pricing_service_tariffs();
    $webdesign_plans = weblazem_get_pricing_webdesign_plans();
    ?>
    <div class="wrap" dir="rtl">
        <div class="weblazem-admin-header">
            <div class="weblazem-admin-header-content">
                <h1>تنظیمات صفحه خدمات و تعرفه‌ها</h1>
                <p>
                    محتوای صفحه داخلی «خدمات و تعرفه‌ها» را مدیریت کنید.
                    <a href="<?php echo esc_url($page_url); ?>" target="_blank" rel="noopener">مشاهده صفحه</a>
                </p>
            </div>
        </div>

        <div class="weblazem-admin-content" style="margin-top:24px;">
            <div class="weblazem-tabs" style="margin-bottom:20px;">
                <?php
                $admin_tabs = array(
                    'layout'           => 'چیدمان سکشن‌ها',
                    'hero'             => 'هیرو',
                    'categories'       => 'دسته‌بندی خدمات',
                    'service_tariffs'  => 'تعرفه خدمات',
                    'webdesign_plans'  => 'تعرفه طراحی سایت',
                    'consult'          => 'مشاوره',
                );
                $first = true;
                foreach ($admin_tabs as $id => $label) :
                    ?>
                    <button type="button" class="weblazem-tab<?php echo $first ? ' active' : ''; ?>" data-tab="<?php echo esc_attr($id); ?>"><?php echo esc_html($label); ?></button>
                    <?php $first = false; endforeach; ?>
            </div>

            <form method="post" action="options.php" id="pricing-options-form">
                <?php settings_fields('weblazem_pricing_group'); ?>

                <div class="weblazem-tab-content active" data-tab-content="layout">
                    <div class="weblazem-admin-card">
                        <h3>فعال‌سازی سکشن‌ها</h3>
                        <table class="form-table">
                            <?php foreach (weblazem_get_pricing_sections_config() as $key => $label) :
                                $option_key = 'weblazem_pricing_section_' . $key . '_enabled';
                                ?>
                                <tr>
                                    <th><?php echo esc_html($label); ?></th>
                                    <td>
                                        <input type="hidden" name="<?php echo esc_attr($option_key); ?>" value="0" />
                                        <label><input type="checkbox" name="<?php echo esc_attr($option_key); ?>" value="1" <?php checked(get_option($option_key, '1'), '1'); ?> /> نمایش در صفحه</label>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </table>
                    </div>
                </div>

                <div class="weblazem-tab-content" data-tab-content="hero">
                    <div class="weblazem-admin-card">
                        <h3>بخش هیرو</h3>
                        <?php weblazem_pricing_admin_image('hero_icon', 'آیکون'); ?>
                        <?php weblazem_pricing_admin_field('hero_title', 'عنوان'); ?>
                        <?php weblazem_pricing_admin_textarea('hero_text', 'متن توضیحی'); ?>
                    </div>
                </div>

                <div class="weblazem-tab-content" data-tab-content="categories">
                    <div class="weblazem-admin-card">
                        <h3>دسته‌بندی خدمات (۴ کارت)</h3>
                        <div id="pricing-categories-container">
                            <?php foreach ($categories as $i => $item) : weblazem_pricing_admin_category_row($i, $item); endforeach; ?>
                        </div>
                        <button type="button" class="button" id="add-pricing-category">افزودن کارت</button>
                    </div>
                </div>

                <div class="weblazem-tab-content" data-tab-content="service_tariffs">
                    <div class="weblazem-admin-card">
                        <h3>تعرفه خدمات (پشتیبانی / سئو / محتوا)</h3>
                        <?php weblazem_pricing_admin_field('service_tariffs_title', 'عنوان بخش'); ?>
                        <?php weblazem_pricing_admin_textarea('service_tariffs_intro', 'متن مقدمه'); ?>
                        <div id="pricing-service-tariffs-container">
                            <?php foreach ($service_tariffs as $i => $card) : weblazem_pricing_admin_service_tariff_row($i, $card); endforeach; ?>
                        </div>
                        <button type="button" class="button" id="add-pricing-service-tariff">افزودن کارت</button>
                    </div>
                </div>

                <div class="weblazem-tab-content" data-tab-content="webdesign_plans">
                    <div class="weblazem-admin-card">
                        <h3>تعرفه طراحی سایت (پلن‌های قیمت)</h3>
                        <p class="description">کارت‌های قیمت مشابه صفحه سئو — با امکان تعیین مبلغ و درخواست مشاوره از طریق مودال.</p>
                        <?php weblazem_pricing_admin_field('webdesign_plans_title', 'عنوان بخش'); ?>
                        <?php weblazem_pricing_admin_field('webdesign_plans_price_label', 'برچسب قیمت'); ?>
                        <div id="pricing-webdesign-plans-container">
                            <?php foreach ($webdesign_plans as $i => $plan) : weblazem_pricing_admin_webdesign_plan_row($i, $plan); endforeach; ?>
                        </div>
                        <button type="button" class="button" id="add-pricing-webdesign-plan">افزودن پلن</button>
                    </div>
                </div>

                <div class="weblazem-tab-content" data-tab-content="consult">
                    <div class="weblazem-admin-card">
                        <h3>بخش مشاوره پایانی</h3>
                        <?php weblazem_pricing_admin_field('consult_title', 'عنوان'); ?>
                        <?php weblazem_pricing_admin_textarea('consult_text', 'متن'); ?>
                        <?php weblazem_pricing_admin_field('consult_btn_text', 'متن دکمه'); ?>
                        <p>
                            <label><strong>لینک دکمه</strong> (در صورت غیرفعال بودن مودال)<br>
                            <input type="text" name="weblazem_pricing_consult_btn_url" class="large-text" value="<?php echo esc_attr(weblazem_pricing_opt('consult_btn_url')); ?>" /></label>
                        </p>
                        <p>
                            <input type="hidden" name="weblazem_pricing_consult_btn_modal" value="0" />
                            <label><input type="checkbox" name="weblazem_pricing_consult_btn_modal" value="1" <?php checked(weblazem_pricing_opt('consult_btn_modal'), '1'); ?> /> باز کردن مودال مشاوره</label>
                        </p>
                    </div>
                </div>

                <?php submit_button('ذخیره تنظیمات'); ?>
            </form>
        </div>
    </div>

    <?php weblazem_pricing_admin_footer_scripts($categories, $service_tariffs, $webdesign_plans); ?>
    <?php
}

function weblazem_pricing_admin_field($key, $label) {
    $val = weblazem_pricing_opt($key);
    echo '<p><label><strong>' . esc_html($label) . '</strong><br>';
    echo '<input type="text" name="weblazem_pricing_' . esc_attr($key) . '" class="large-text" value="' . esc_attr($val) . '" /></label></p>';
}

function weblazem_pricing_admin_textarea($key, $label) {
    $val = weblazem_pricing_opt($key);
    echo '<p><label><strong>' . esc_html($label) . '</strong><br>';
    echo '<textarea name="weblazem_pricing_' . esc_attr($key) . '" class="large-text" rows="3">' . esc_textarea($val) . '</textarea></label></p>';
}

function weblazem_pricing_admin_image($key, $label) {
    $val = weblazem_pricing_opt($key);
    $id  = 'pricing_img_' . $key;
    echo '<p><strong>' . esc_html($label) . '</strong><br>';
    echo '<input type="hidden" id="' . esc_attr($id) . '" name="weblazem_pricing_' . esc_attr($key) . '" value="' . esc_attr($val) . '" />';
    echo '<div class="pricing-img-preview" data-for="' . esc_attr($id) . '" style="margin:8px 0;">';
    if ($val) {
        echo '<img src="' . esc_url($val) . '" style="max-width:200px;border-radius:8px;" alt="" />';
    }
    echo '</div>';
    echo '<button type="button" class="button pricing-upload-img" data-target="' . esc_attr($id) . '">انتخاب تصویر</button> ';
    echo '<button type="button" class="button pricing-remove-img" data-target="' . esc_attr($id) . '">حذف</button></p>';
}

function weblazem_pricing_admin_category_row($i, $item) {
    ?>
    <div class="pricing-repeater-block" style="display:flex;gap:8px;margin-bottom:8px;align-items:center;">
        <input type="text" name="weblazem_pricing_categories[<?php echo (int) $i; ?>][title]" class="large-text" value="<?php echo esc_attr($item['title'] ?? ''); ?>" placeholder="عنوان" />
        <input type="text" name="weblazem_pricing_categories[<?php echo (int) $i; ?>][url]" class="large-text" value="<?php echo esc_attr($item['url'] ?? ''); ?>" placeholder="لینک" dir="ltr" />
        <button type="button" class="button pricing-remove-block">حذف</button>
    </div>
    <?php
}

function weblazem_pricing_admin_service_tariff_row($i, $card) {
    $img_id = 'pricing_tariff_img_' . $i;
    ?>
    <div class="pricing-repeater-block" style="background:#f8f5fc;padding:16px;border-radius:12px;margin-bottom:16px;border:1px solid #e8dff0;">
        <button type="button" class="button pricing-remove-block" style="float:left;">حذف</button>
        <p><input type="text" name="weblazem_pricing_service_tariffs[<?php echo (int) $i; ?>][title]" class="large-text" value="<?php echo esc_attr($card['title'] ?? ''); ?>" placeholder="عنوان" /></p>
        <p><textarea name="weblazem_pricing_service_tariffs[<?php echo (int) $i; ?>][description]" class="large-text" rows="2" placeholder="توضیح"><?php echo esc_textarea($card['description'] ?? ''); ?></textarea></p>
        <p>
            <input type="hidden" id="<?php echo esc_attr($img_id); ?>" name="weblazem_pricing_service_tariffs[<?php echo (int) $i; ?>][image]" value="<?php echo esc_attr($card['image'] ?? ''); ?>" />
            <div class="pricing-img-preview" data-for="<?php echo esc_attr($img_id); ?>" style="margin:8px 0;">
                <?php if (!empty($card['image'])) : ?>
                    <img src="<?php echo esc_url($card['image']); ?>" style="max-width:200px;border-radius:8px;" alt="" />
                <?php endif; ?>
            </div>
            <button type="button" class="button pricing-upload-img" data-target="<?php echo esc_attr($img_id); ?>">انتخاب تصویر</button>
        </p>
        <p>
            <input type="text" name="weblazem_pricing_service_tariffs[<?php echo (int) $i; ?>][button_text]" value="<?php echo esc_attr($card['button_text'] ?? 'نمایش بیشتر'); ?>" placeholder="متن دکمه" />
            <input type="text" name="weblazem_pricing_service_tariffs[<?php echo (int) $i; ?>][button_url]" class="large-text" value="<?php echo esc_attr($card['button_url'] ?? ''); ?>" placeholder="لینک" />
            <label><input type="checkbox" name="weblazem_pricing_service_tariffs[<?php echo (int) $i; ?>][button_modal]" value="1" <?php checked($card['button_modal'] ?? '', '1'); ?> /> مودال مشاوره</label>
        </p>
    </div>
    <?php
}

function weblazem_pricing_admin_webdesign_plan_row($i, $plan) {
    $features = isset($plan['features']) && is_array($plan['features']) ? $plan['features'] : array('');
    ?>
    <div class="pricing-repeater-block pricing-webdesign-plan-row" style="background:#f8f5fc;padding:16px;border-radius:12px;margin-bottom:16px;border:1px solid #e8dff0;">
        <button type="button" class="button pricing-remove-block" style="float:left;">حذف پلن</button>
        <p><input type="text" name="weblazem_pricing_webdesign_plans[<?php echo (int) $i; ?>][title]" class="large-text" value="<?php echo esc_attr($plan['title'] ?? ''); ?>" placeholder="عنوان پلن" /></p>
        <p><input type="text" name="weblazem_pricing_webdesign_plans[<?php echo (int) $i; ?>][price]" class="large-text" value="<?php echo esc_attr($plan['price'] ?? ''); ?>" placeholder="قیمت" /></p>
        <p><strong>ویژگی‌ها</strong></p>
        <div class="pricing-plan-features" data-plan-index="<?php echo (int) $i; ?>">
            <?php foreach ($features as $fi => $feature) : ?>
                <p class="pricing-plan-feature-line">
                    <input type="text" name="weblazem_pricing_webdesign_plans[<?php echo (int) $i; ?>][features][<?php echo (int) $fi; ?>]" class="large-text" value="<?php echo esc_attr($feature); ?>" placeholder="متن ویژگی" />
                    <button type="button" class="button pricing-remove-feature">حذف</button>
                </p>
            <?php endforeach; ?>
        </div>
        <button type="button" class="button pricing-add-plan-feature" data-plan-index="<?php echo (int) $i; ?>">افزودن ویژگی</button>
        <p style="margin-top:12px;">
            <input type="text" name="weblazem_pricing_webdesign_plans[<?php echo (int) $i; ?>][button_text]" value="<?php echo esc_attr($plan['button_text'] ?? 'مشاوره رایگان'); ?>" placeholder="متن دکمه" />
            <input type="text" name="weblazem_pricing_webdesign_plans[<?php echo (int) $i; ?>][button_url]" class="large-text" value="<?php echo esc_attr($plan['button_url'] ?? ''); ?>" placeholder="لینک" />
            <label><input type="checkbox" name="weblazem_pricing_webdesign_plans[<?php echo (int) $i; ?>][button_modal]" value="1" <?php checked($plan['button_modal'] ?? '1', '1'); ?> /> مودال مشاوره</label>
        </p>
    </div>
    <?php
}

function weblazem_pricing_admin_footer_scripts($categories, $service_tariffs, $webdesign_plans) {
    ?>
    <script>
    jQuery(function($) {
        $('.weblazem-tab').on('click', function() {
            var tab = $(this).data('tab');
            $('.weblazem-tab').removeClass('active');
            $(this).addClass('active');
            $('.weblazem-tab-content').removeClass('active');
            $('[data-tab-content="' + tab + '"]').addClass('active');
        });

        var mediaFrame;
        $(document).on('click', '.pricing-upload-img', function(e) {
            e.preventDefault();
            var target = $(this).data('target');
            if (mediaFrame) { mediaFrame.open(); mediaFrame.targetId = target; return; }
            mediaFrame = wp.media({ title: 'انتخاب تصویر', button: { text: 'استفاده' }, multiple: false });
            mediaFrame.on('select', function() {
                var url = mediaFrame.state().get('selection').first().toJSON().url;
                var id = mediaFrame.targetId;
                $('#' + id).val(url);
                $('[data-for="' + id + '"]').html('<img src="' + url + '" style="max-width:200px;border-radius:8px;" alt="" />');
            });
            mediaFrame.targetId = target;
            mediaFrame.open();
        });

        $(document).on('click', '.pricing-remove-img', function(e) {
            e.preventDefault();
            var target = $(this).data('target');
            $('#' + target).val('');
            $('[data-for="' + target + '"]').empty();
        });

        $(document).on('click', '.pricing-remove-block', function() {
            $(this).closest('.pricing-repeater-block').remove();
        });

        var catIdx = <?php echo count($categories); ?>;
        $('#add-pricing-category').on('click', function() {
            $('#pricing-categories-container').append(
                '<div class="pricing-repeater-block" style="display:flex;gap:8px;margin-bottom:8px;align-items:center;">' +
                '<input type="text" name="weblazem_pricing_categories[' + catIdx + '][title]" class="large-text" placeholder="عنوان" />' +
                '<input type="text" name="weblazem_pricing_categories[' + catIdx + '][url]" class="large-text" placeholder="لینک" dir="ltr" />' +
                '<button type="button" class="button pricing-remove-block">حذف</button></div>'
            );
            catIdx++;
        });

        var tariffIdx = <?php echo count($service_tariffs); ?>;
        $('#add-pricing-service-tariff').on('click', function() {
            var imgId = 'pricing_tariff_img_' + tariffIdx;
            $('#pricing-service-tariffs-container').append(
                '<div class="pricing-repeater-block" style="background:#f8f5fc;padding:16px;border-radius:12px;margin-bottom:16px;border:1px solid #e8dff0;">' +
                '<button type="button" class="button pricing-remove-block" style="float:left;">حذف</button>' +
                '<p><input type="text" name="weblazem_pricing_service_tariffs[' + tariffIdx + '][title]" class="large-text" placeholder="عنوان" /></p>' +
                '<p><textarea name="weblazem_pricing_service_tariffs[' + tariffIdx + '][description]" class="large-text" rows="2" placeholder="توضیح"></textarea></p>' +
                '<p><input type="hidden" id="' + imgId + '" name="weblazem_pricing_service_tariffs[' + tariffIdx + '][image]" value="" />' +
                '<button type="button" class="button pricing-upload-img" data-target="' + imgId + '">انتخاب تصویر</button></p>' +
                '<p><input type="text" name="weblazem_pricing_service_tariffs[' + tariffIdx + '][button_text]" value="نمایش بیشتر" placeholder="متن دکمه" />' +
                '<input type="text" name="weblazem_pricing_service_tariffs[' + tariffIdx + '][button_url]" class="large-text" placeholder="لینک" />' +
                '<label><input type="checkbox" name="weblazem_pricing_service_tariffs[' + tariffIdx + '][button_modal]" value="1" /> مودال مشاوره</label></p></div>'
            );
            tariffIdx++;
        });

        var planIdx = <?php echo count($webdesign_plans); ?>;
        $('#add-pricing-webdesign-plan').on('click', function() {
            $('#pricing-webdesign-plans-container').append(
                '<div class="pricing-repeater-block" style="background:#f8f5fc;padding:16px;border-radius:12px;margin-bottom:16px;border:1px solid #e8dff0;">' +
                '<button type="button" class="button pricing-remove-block" style="float:left;">حذف پلن</button>' +
                '<p><input type="text" name="weblazem_pricing_webdesign_plans[' + planIdx + '][title]" class="large-text" placeholder="عنوان پلن" /></p>' +
                '<p><input type="text" name="weblazem_pricing_webdesign_plans[' + planIdx + '][price]" class="large-text" placeholder="قیمت" /></p>' +
                '<p><strong>ویژگی‌ها</strong></p>' +
                '<div class="pricing-plan-features"><p class="pricing-plan-feature-line">' +
                '<input type="text" name="weblazem_pricing_webdesign_plans[' + planIdx + '][features][0]" class="large-text" placeholder="متن ویژگی" />' +
                '<button type="button" class="button pricing-remove-feature">حذف</button></p></div>' +
                '<button type="button" class="button pricing-add-plan-feature" data-plan-index="' + planIdx + '">افزودن ویژگی</button>' +
                '<p style="margin-top:12px;"><input type="text" name="weblazem_pricing_webdesign_plans[' + planIdx + '][button_text]" value="مشاوره رایگان" />' +
                '<input type="text" name="weblazem_pricing_webdesign_plans[' + planIdx + '][button_url]" class="large-text" placeholder="لینک" />' +
                '<label><input type="checkbox" name="weblazem_pricing_webdesign_plans[' + planIdx + '][button_modal]" value="1" checked /> مودال مشاوره</label></p></div>'
            );
            planIdx++;
        });

        $(document).on('click', '.pricing-add-plan-feature', function() {
            var planIndex = $(this).data('plan-index');
            var $wrap = $(this).siblings('.pricing-plan-features');
            var featureIdx = $wrap.find('.pricing-plan-feature-line').length;
            $wrap.append(
                '<p class="pricing-plan-feature-line"><input type="text" name="weblazem_pricing_webdesign_plans[' + planIndex + '][features][' + featureIdx + ']" class="large-text" placeholder="متن ویژگی" />' +
                '<button type="button" class="button pricing-remove-feature">حذف</button></p>'
            );
        });

        $(document).on('click', '.pricing-remove-feature', function() {
            $(this).closest('.pricing-plan-feature-line').remove();
        });
    });
    </script>
    <?php
}
