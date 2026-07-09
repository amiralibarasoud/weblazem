<?php
/**
 * SEO & digital marketing page — admin settings.
 */

require_once get_template_directory() . '/inc/seo-defaults.php';

function weblazem_seo_options_menu() {
    add_submenu_page(
        'weblazem-theme-options',
        'تنظیمات سئو و بازاریابی دیجیتال',
        '  سئو و بازاریابی',
        'manage_options',
        'weblazem-seo-options',
        'weblazem_seo_options_display'
    );
}
add_action('admin_menu', 'weblazem_seo_options_menu', 22);

function weblazem_register_seo_settings() {
    $defaults = weblazem_seo_defaults();

    foreach (array_keys($defaults) as $key) {
        register_setting('weblazem_seo_group', 'weblazem_seo_' . $key);
    }

    register_setting('weblazem_seo_group', 'weblazem_seo_splits', array('sanitize_callback' => 'weblazem_sanitize_seo_splits'));
    register_setting('weblazem_seo_group', 'weblazem_seo_process_steps', array('sanitize_callback' => 'weblazem_sanitize_seo_steps'));
    register_setting('weblazem_seo_group', 'weblazem_seo_advantages_items', array('sanitize_callback' => 'weblazem_sanitize_seo_advantages'));
    register_setting('weblazem_seo_group', 'weblazem_seo_faq_items', array('sanitize_callback' => 'weblazem_sanitize_seo_faq'));
    register_setting('weblazem_seo_group', 'weblazem_seo_service_cards', array('sanitize_callback' => 'weblazem_sanitize_seo_service_cards'));
    register_setting('weblazem_seo_group', 'weblazem_seo_clients_logos', array('sanitize_callback' => 'weblazem_sanitize_seo_logos'));
    register_setting('weblazem_seo_group', 'weblazem_seo_pricing_plans', array('sanitize_callback' => 'weblazem_sanitize_seo_pricing_plans'));

    foreach (weblazem_get_seo_sections_config() as $key => $label) {
        register_setting('weblazem_seo_group', 'weblazem_seo_section_' . $key . '_enabled');
    }
}
add_action('admin_init', 'weblazem_register_seo_settings');

function weblazem_sanitize_seo_splits($input) {
    if (!is_array($input)) {
        return weblazem_get_default_seo_splits();
    }
    $out = array();
    foreach ($input as $row) {
        if (empty($row['title']) && empty($row['text'])) {
            continue;
        }
        $out[] = array(
            'title'        => sanitize_text_field($row['title'] ?? ''),
            'text'         => wp_kses_post($row['text'] ?? ''),
            'button_text'  => sanitize_text_field($row['button_text'] ?? ''),
            'button_url'   => esc_url_raw($row['button_url'] ?? ''),
            'button_modal' => (!empty($row['button_modal']) && $row['button_modal'] === '1') ? '1' : '0',
            'image'        => esc_url_raw($row['image'] ?? ''),
            'caption'      => sanitize_text_field($row['caption'] ?? ''),
            'layout'       => ($row['layout'] ?? '') === 'left' ? 'left' : 'right',
        );
    }
    return !empty($out) ? $out : weblazem_get_default_seo_splits();
}

function weblazem_sanitize_seo_steps($input) {
    if (!is_array($input)) {
        return weblazem_get_default_seo_process_steps();
    }
    $out = array();
    foreach ($input as $row) {
        if (empty($row['title'])) {
            continue;
        }
        $out[] = array('title' => sanitize_text_field($row['title']));
    }
    return !empty($out) ? $out : weblazem_get_default_seo_process_steps();
}

function weblazem_sanitize_seo_advantages($input) {
    if (!is_array($input)) {
        return weblazem_get_default_seo_advantages();
    }
    $out = array();
    foreach ($input as $row) {
        if (empty($row['title'])) {
            continue;
        }
        $out[] = array(
            'icon'       => sanitize_key($row['icon'] ?? 'cube'),
            'icon_image' => esc_url_raw($row['icon_image'] ?? ''),
            'title'      => sanitize_text_field($row['title']),
            'text'       => sanitize_textarea_field($row['text'] ?? ''),
        );
    }
    return !empty($out) ? $out : weblazem_get_default_seo_advantages();
}

function weblazem_sanitize_seo_faq($input) {
    if (!is_array($input)) {
        return weblazem_get_default_seo_faq_items();
    }
    $out = array();
    foreach ($input as $row) {
        if (empty($row['question'])) {
            continue;
        }
        $out[] = array(
            'question' => sanitize_text_field($row['question']),
            'answer'   => wp_kses_post($row['answer'] ?? ''),
        );
    }
    return $out;
}

function weblazem_sanitize_seo_service_cards($input) {
    if (!is_array($input)) {
        return weblazem_get_default_seo_service_cards();
    }
    $out = array();
    foreach ($input as $row) {
        if (empty($row['title'])) {
            continue;
        }
        $out[] = array(
            'title'       => sanitize_text_field($row['title']),
            'en_title'    => sanitize_text_field($row['en_title'] ?? ''),
            'description' => sanitize_text_field($row['description'] ?? ''),
            'url'         => esc_url_raw($row['url'] ?? ''),
            'shape_image' => esc_url_raw($row['shape_image'] ?? ''),
        );
    }
    return $out;
}

function weblazem_sanitize_seo_logos($input) {
    if (!is_array($input)) {
        return array();
    }
    $out = array();
    foreach ($input as $row) {
        if (empty($row['logo'])) {
            continue;
        }
        $out[] = array(
            'name' => sanitize_text_field($row['name'] ?? ''),
            'logo' => esc_url_raw($row['logo']),
            'url'  => esc_url_raw($row['url'] ?? ''),
        );
    }
    return $out;
}

function weblazem_sanitize_seo_pricing_plans($input) {
    if (!is_array($input)) {
        return weblazem_get_default_seo_pricing_plans();
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

    return !empty($out) ? $out : weblazem_get_default_seo_pricing_plans();
}

function weblazem_seo_handle_section_checkboxes() {
    if (!isset($_POST['option_page']) || $_POST['option_page'] !== 'weblazem_seo_group') {
        return;
    }
    foreach (weblazem_get_seo_sections_config() as $key => $label) {
        $option_key = 'weblazem_seo_section_' . $key . '_enabled';
        if (!isset($_POST[$option_key])) {
            update_option($option_key, '0');
        }
    }
}
add_action('admin_init', 'weblazem_seo_handle_section_checkboxes', 20);

function weblazem_seo_admin_scripts($hook) {
    if (strpos($hook, 'weblazem-seo-options') === false) {
        return;
    }
    wp_enqueue_media();
}
add_action('admin_enqueue_scripts', 'weblazem_seo_admin_scripts');

function weblazem_seo_opt($key) {
    $defaults = weblazem_seo_defaults();
    return get_option('weblazem_seo_' . $key, $defaults[$key] ?? '');
}

function weblazem_seo_options_display() {
    $page_url   = weblazem_get_seo_page_url();
    $splits     = get_option('weblazem_seo_splits', weblazem_get_default_seo_splits());
    $steps      = get_option('weblazem_seo_process_steps', weblazem_get_default_seo_process_steps());
    $advantages = get_option('weblazem_seo_advantages_items', weblazem_get_default_seo_advantages());
    $faq_items  = get_option('weblazem_seo_faq_items', weblazem_get_default_seo_faq_items());
    $cards      = get_option('weblazem_seo_service_cards', weblazem_get_default_seo_service_cards());
    $logos      = get_option('weblazem_seo_clients_logos', weblazem_get_default_seo_client_logos());
    $plans      = weblazem_get_seo_pricing_plans();
    $icon_choices = array(
        'clipboard' => 'استراتژی', 'chart' => 'نمودار', 'star' => 'ستاره', 'target' => 'هدف',
        'shark' => 'رقبا', 'coffee' => 'محتوا', 'grid' => 'UX', 'graph' => 'گزارش',
        'cube' => 'مکعب', 'rocket' => 'راکت', 'headset' => 'پشتیبانی',
    );
    ?>
    <div class="wrap" dir="rtl">
        <div class="weblazem-admin-header">
            <div class="weblazem-admin-header-content">
                <h1>تنظیمات صفحه سئو و بازاریابی دیجیتال</h1>
                <p>
                    محتوای صفحه داخلی «سئو و بازاریابی دیجیتال» را مدیریت کنید.
                    <a href="<?php echo esc_url($page_url); ?>" target="_blank" rel="noopener">مشاهده صفحه</a>
                </p>
            </div>
        </div>

        <div class="weblazem-admin-content" style="margin-top:24px;">
            <div class="weblazem-tabs" style="margin-bottom:20px;">
                <?php
                $admin_tabs = array(
                    'layout' => 'چیدمان سکشن‌ها', 'hero' => 'هیرو', 'clients' => 'مشتریان',
                    'splits' => 'بخش‌های دو ستونه', 'process' => 'فرآیند',
                    'advantages' => 'مزایا', 'tariffs' => 'تعرفه‌ها', 'faq' => 'FAQ و تماس',
                );
                $first = true;
                foreach ($admin_tabs as $id => $label) :
                    ?>
                    <button type="button" class="weblazem-tab<?php echo $first ? ' active' : ''; ?>" data-tab="<?php echo esc_attr($id); ?>"><?php echo esc_html($label); ?></button>
                    <?php $first = false; endforeach; ?>
            </div>

            <form method="post" action="options.php" id="seo-options-form">
                <?php settings_fields('weblazem_seo_group'); ?>

                <div class="weblazem-tab-content active" data-tab-content="layout">
                    <div class="weblazem-admin-card">
                        <h3>فعال‌سازی سکشن‌ها</h3>
                        <table class="form-table">
                            <?php foreach (weblazem_get_seo_sections_config() as $key => $label) :
                                $option_key = 'weblazem_seo_section_' . $key . '_enabled';
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
                        <?php weblazem_seo_admin_field('hero_en_title', 'عنوان انگلیسی'); ?>
                        <?php weblazem_seo_admin_image('hero_calligraphy_image', 'تصویر خوشنویسی'); ?>
                        <?php weblazem_seo_admin_textarea('hero_calligraphy_text', 'متن خوشنویسی (HTML)', 'در صورت نبود تصویر'); ?>
                        <?php weblazem_seo_admin_field('hero_title', 'عنوان فارسی'); ?>
                        <?php weblazem_seo_admin_textarea('hero_text', 'متن توضیحی'); ?>
                        <?php weblazem_seo_admin_image('hero_image', 'تصویر ایلاستریشن'); ?>
                    </div>
                </div>

                <div class="weblazem-tab-content" data-tab-content="clients">
                    <div class="weblazem-admin-card">
                        <h3>مشتریان و اعتماد</h3>
                        <?php weblazem_seo_admin_image('clients_calligraphy_image', 'تصویر خوشنویسی'); ?>
                        <?php weblazem_seo_admin_textarea('clients_calligraphy_text', 'متن خوشنویسی (HTML)'); ?>
                        <?php weblazem_seo_admin_field('clients_subtitle', 'زیرعنوان'); ?>
                        <?php weblazem_seo_admin_textarea('clients_description', 'توضیح'); ?>
                        <h4>لوگوها</h4>
                        <div id="seo-logos-container">
                            <?php foreach ($logos as $i => $logo) : weblazem_seo_admin_logo_row($i, $logo); endforeach; ?>
                        </div>
                        <button type="button" class="button" id="add-seo-logo">افزودن لوگو</button>
                    </div>
                </div>

                <div class="weblazem-tab-content" data-tab-content="splits">
                    <div class="weblazem-admin-card">
                        <h3>بخش‌های دو ستونه</h3>
                        <div id="seo-splits-container">
                            <?php foreach ($splits as $i => $split) : weblazem_seo_admin_split_row($i, $split); endforeach; ?>
                        </div>
                        <button type="button" class="button" id="add-seo-split">افزودن بخش</button>
                    </div>
                </div>

                <div class="weblazem-tab-content" data-tab-content="process">
                    <div class="weblazem-admin-card">
                        <h3>فرآیند کار</h3>
                        <?php weblazem_seo_admin_image('process_calligraphy_image', 'تصویر خوشنویسی'); ?>
                        <?php weblazem_seo_admin_textarea('process_calligraphy_text', 'متن خوشنویسی'); ?>
                        <?php weblazem_seo_admin_field('process_subtitle', 'زیرعنوان'); ?>
                        <?php weblazem_seo_admin_textarea('process_description', 'توضیح'); ?>
                        <?php weblazem_seo_admin_field('process_start_note', 'یادداشت شروع'); ?>
                        <?php weblazem_seo_admin_field('process_journey_caption', 'متن پایین کارت‌ها'); ?>
                        <h4>مراحل</h4>
                        <div id="seo-steps-container">
                            <?php foreach ($steps as $i => $step) : ?>
                                <p><input type="text" name="weblazem_seo_process_steps[<?php echo $i; ?>][title]" class="large-text" value="<?php echo esc_attr($step['title']); ?>" placeholder="عنوان مرحله" />
                                <button type="button" class="button seo-step-remove">حذف</button></p>
                            <?php endforeach; ?>
                        </div>
                        <button type="button" class="button" id="add-seo-step">افزودن مرحله</button>
                        <h4>CSAT و دکمه‌ها</h4>
                        <?php weblazem_seo_admin_field('process_csat_number', 'درصد CSAT'); ?>
                        <?php weblazem_seo_admin_field('process_csat_sub', 'زیرنویس CSAT'); ?>
                        <?php weblazem_seo_admin_field('process_csat_label', 'برچسب CSAT'); ?>
                        <?php weblazem_seo_admin_field('process_btn1_text', 'دکمه چپ — متن'); ?>
                        <?php weblazem_seo_admin_field('process_btn1_url', 'دکمه چپ — لینک'); ?>
                        <?php weblazem_seo_admin_field('process_btn2_text', 'دکمه راست — متن'); ?>
                        <?php weblazem_seo_admin_field('process_btn2_url', 'دکمه راست — لینک'); ?>
                    </div>
                </div>

                <div class="weblazem-tab-content" data-tab-content="advantages">
                    <div class="weblazem-admin-card">
                        <h3>مزایا</h3>
                        <?php weblazem_seo_admin_field('advantages_title', 'عنوان'); ?>
                        <?php weblazem_seo_admin_textarea('advantages_subtitle', 'زیرعنوان'); ?>
                        <div id="seo-advantages-container">
                            <?php foreach ($advantages as $i => $item) : weblazem_seo_admin_advantage_row($i, $item, $icon_choices); endforeach; ?>
                        </div>
                        <button type="button" class="button" id="add-seo-advantage">افزودن مزیت</button>
                    </div>
                </div>

                <div class="weblazem-tab-content" data-tab-content="tariffs">
                    <div class="weblazem-admin-card">
                        <h3>تعرفه‌ها (پلن‌های سئو)</h3>
                        <p class="description">کارت‌های قیمت‌گذاری در یک ردیف نمایش داده می‌شوند. دکمه «مشاوره رایگان» می‌تواند مودال درخواست مشاوره را باز کند.</p>
                        <?php weblazem_seo_admin_field('tariffs_title', 'عنوان بخش'); ?>
                        <?php weblazem_seo_admin_field('tariffs_price_label', 'برچسب قیمت'); ?>
                        <h4>پلن‌ها</h4>
                        <div id="seo-pricing-plans-container">
                            <?php foreach ($plans as $i => $plan) : weblazem_seo_admin_pricing_plan_row($i, $plan); endforeach; ?>
                        </div>
                        <button type="button" class="button" id="add-seo-pricing-plan">افزودن پلن</button>
                    </div>
                </div>

                <div class="weblazem-tab-content" data-tab-content="faq">
                    <div class="weblazem-admin-card">
                        <h3>FAQ و تماس</h3>
                        <?php weblazem_seo_admin_image('faq_calligraphy_image', 'تصویر خوشنویسی'); ?>
                        <?php weblazem_seo_admin_textarea('faq_calligraphy_text', 'متن خوشنویسی'); ?>
                        <?php weblazem_seo_admin_field('faq_subtitle', 'عنوان FAQ'); ?>
                        <?php weblazem_seo_admin_textarea('faq_intro', 'متن مقدمه'); ?>
                        <?php weblazem_seo_admin_image('faq_profile_image', 'تصویر پروفایل'); ?>
                        <?php weblazem_seo_admin_field('faq_phone', 'شماره تماس'); ?>
                        <?php weblazem_seo_admin_field('faq_consult_btn_text', 'متن دکمه مشاوره'); ?>
                        <?php weblazem_seo_admin_textarea('faq_footer_text', 'متن پایین کارت پروفایل'); ?>
                        <h4>سوالات</h4>
                        <div id="seo-faq-container">
                            <?php foreach ($faq_items as $i => $item) : weblazem_seo_admin_faq_row($i, $item); endforeach; ?>
                        </div>
                        <button type="button" class="button" id="add-seo-faq">افزودن سوال</button>
                        <h4 style="margin-top:24px;">کارت‌های خدمات پایین صفحه</h4>
                        <div id="seo-service-cards">
                            <?php foreach ($cards as $i => $card) : weblazem_seo_admin_service_card($i, $card); endforeach; ?>
                        </div>
                        <button type="button" class="button" id="add-seo-service-card">افزودن کارت</button>
                    </div>
                </div>

                <?php submit_button('ذخیره تنظیمات'); ?>
            </form>
        </div>
    </div>

    <?php weblazem_seo_admin_scripts_inline($icon_choices); ?>
    <?php
}

function weblazem_seo_admin_field($key, $label) {
    $val = weblazem_seo_opt($key);
    echo '<p><label><strong>' . esc_html($label) . '</strong><br>';
    echo '<input type="text" name="weblazem_seo_' . esc_attr($key) . '" class="large-text" value="' . esc_attr($val) . '" /></label></p>';
}

function weblazem_seo_admin_textarea($key, $label, $desc = '') {
    $val = weblazem_seo_opt($key);
    echo '<p><label><strong>' . esc_html($label) . '</strong>';
    if ($desc) {
        echo ' <span class="description">' . esc_html($desc) . '</span>';
    }
    echo '<br><textarea name="weblazem_seo_' . esc_attr($key) . '" class="large-text" rows="3">' . esc_textarea($val) . '</textarea></label></p>';
}

function weblazem_seo_admin_image($key, $label) {
    $val = weblazem_seo_opt($key);
    $id  = 'seo_img_' . $key;
    echo '<p><strong>' . esc_html($label) . '</strong><br>';
    echo '<input type="hidden" id="' . esc_attr($id) . '" name="weblazem_seo_' . esc_attr($key) . '" value="' . esc_attr($val) . '" />';
    echo '<div class="seo-img-preview" data-for="' . esc_attr($id) . '" style="margin:8px 0;">';
    if ($val) {
        echo '<img src="' . esc_url($val) . '" style="max-width:200px;border-radius:8px;" alt="" />';
    }
    echo '</div>';
    echo '<button type="button" class="button seo-upload-img" data-target="' . esc_attr($id) . '">انتخاب تصویر</button> ';
    echo '<button type="button" class="button seo-remove-img" data-target="' . esc_attr($id) . '">حذف</button></p>';
}

function weblazem_seo_admin_split_row($i, $split) {
    ?>
    <div class="seo-repeater-block" style="background:#f8f5fc;padding:16px;border-radius:12px;margin-bottom:16px;border:1px solid #e8dff0;">
        <button type="button" class="button seo-remove-block" style="float:left;">حذف</button>
        <p><input type="text" name="weblazem_seo_splits[<?php echo $i; ?>][title]" class="large-text" value="<?php echo esc_attr($split['title'] ?? ''); ?>" placeholder="عنوان" /></p>
        <p><textarea name="weblazem_seo_splits[<?php echo $i; ?>][text]" class="large-text" rows="3" placeholder="متن"><?php echo esc_textarea($split['text'] ?? ''); ?></textarea></p>
        <p>
            <input type="text" name="weblazem_seo_splits[<?php echo $i; ?>][button_text]" value="<?php echo esc_attr($split['button_text'] ?? ''); ?>" placeholder="متن دکمه" />
            <input type="text" name="weblazem_seo_splits[<?php echo $i; ?>][button_url]" class="large-text" value="<?php echo esc_attr($split['button_url'] ?? ''); ?>" placeholder="لینک دکمه" />
            <label><input type="checkbox" name="weblazem_seo_splits[<?php echo $i; ?>][button_modal]" value="1" <?php checked($split['button_modal'] ?? '', '1'); ?> /> باز کردن مودال مشاوره</label>
        </p>
        <p><input type="text" name="weblazem_seo_splits[<?php echo $i; ?>][image]" class="large-text seo-split-image" value="<?php echo esc_attr($split['image'] ?? ''); ?>" placeholder="URL تصویر" />
        <button type="button" class="button seo-upload-split-img">انتخاب تصویر</button></p>
        <p><input type="text" name="weblazem_seo_splits[<?php echo $i; ?>][caption]" class="large-text" value="<?php echo esc_attr($split['caption'] ?? ''); ?>" placeholder="کپشن دست‌نویس" /></p>
        <p>چیدمان تصویر:
            <select name="weblazem_seo_splits[<?php echo $i; ?>][layout]">
                <option value="right" <?php selected($split['layout'] ?? '', 'right'); ?>>متن چپ — تصویر راست</option>
                <option value="left" <?php selected($split['layout'] ?? '', 'left'); ?>>تصویر چپ — متن راست</option>
            </select>
        </p>
    </div>
    <?php
}

function weblazem_seo_admin_advantage_row($i, $item, $icons) {
    ?>
    <div class="seo-repeater-block" style="background:#f8f5fc;padding:16px;border-radius:12px;margin-bottom:16px;">
        <button type="button" class="button seo-remove-block" style="float:left;">حذف</button>
        <p><select name="weblazem_seo_advantages_items[<?php echo $i; ?>][icon]">
            <?php foreach ($icons as $k => $label) : ?>
                <option value="<?php echo esc_attr($k); ?>" <?php selected($item['icon'] ?? '', $k); ?>><?php echo esc_html($label); ?></option>
            <?php endforeach; ?>
        </select></p>
        <p><input type="text" name="weblazem_seo_advantages_items[<?php echo $i; ?>][icon_image]" class="large-text" value="<?php echo esc_attr($item['icon_image'] ?? ''); ?>" placeholder="URL آیکون سفارشی (اختیاری)" /></p>
        <p><input type="text" name="weblazem_seo_advantages_items[<?php echo $i; ?>][title]" class="large-text" value="<?php echo esc_attr($item['title'] ?? ''); ?>" placeholder="عنوان" /></p>
        <p><textarea name="weblazem_seo_advantages_items[<?php echo $i; ?>][text]" class="large-text" rows="2" placeholder="توضیح"><?php echo esc_textarea($item['text'] ?? ''); ?></textarea></p>
    </div>
    <?php
}

function weblazem_seo_admin_pricing_plan_row($i, $plan) {
    $features = isset($plan['features']) && is_array($plan['features']) ? $plan['features'] : array();
    if (empty($features)) {
        $features = array('');
    }
    ?>
    <div class="seo-repeater-block seo-pricing-plan-row" style="background:#f8f5fc;padding:16px;border-radius:12px;margin-bottom:16px;border:1px solid #e8dff0;">
        <button type="button" class="button seo-remove-block" style="float:left;">حذف پلن</button>
        <p><input type="text" name="weblazem_seo_pricing_plans[<?php echo (int) $i; ?>][title]" class="large-text" value="<?php echo esc_attr($plan['title'] ?? ''); ?>" placeholder="عنوان پلن (مثلاً پلن طلایی (ماهانه))" /></p>
        <p><input type="text" name="weblazem_seo_pricing_plans[<?php echo (int) $i; ?>][price]" class="large-text" value="<?php echo esc_attr($plan['price'] ?? ''); ?>" placeholder="قیمت (مثلاً ۲.۰۰۰.۰۰۰ تومان)" /></p>
        <p><strong>ویژگی‌ها</strong></p>
        <div class="seo-plan-features" data-plan-index="<?php echo (int) $i; ?>">
            <?php foreach ($features as $fi => $feature) : ?>
                <p class="seo-plan-feature-line">
                    <input type="text" name="weblazem_seo_pricing_plans[<?php echo (int) $i; ?>][features][<?php echo (int) $fi; ?>]" class="large-text" value="<?php echo esc_attr($feature); ?>" placeholder="متن ویژگی" />
                    <button type="button" class="button seo-remove-feature">حذف</button>
                </p>
            <?php endforeach; ?>
        </div>
        <button type="button" class="button seo-add-plan-feature" data-plan-index="<?php echo (int) $i; ?>">افزودن ویژگی</button>
        <p style="margin-top:12px;">
            <input type="text" name="weblazem_seo_pricing_plans[<?php echo (int) $i; ?>][button_text]" value="<?php echo esc_attr($plan['button_text'] ?? 'مشاوره رایگان'); ?>" placeholder="متن دکمه" />
            <input type="text" name="weblazem_seo_pricing_plans[<?php echo (int) $i; ?>][button_url]" class="large-text" value="<?php echo esc_attr($plan['button_url'] ?? ''); ?>" placeholder="لینک دکمه (در صورت غیرفعال بودن مودال)" />
            <label><input type="checkbox" name="weblazem_seo_pricing_plans[<?php echo (int) $i; ?>][button_modal]" value="1" <?php checked($plan['button_modal'] ?? '1', '1'); ?> /> باز کردن مودال مشاوره</label>
        </p>
    </div>
    <?php
}

function weblazem_seo_admin_faq_row($i, $item) {
    ?>
    <div class="seo-repeater-block" style="background:#f8f5fc;padding:16px;border-radius:12px;margin-bottom:16px;">
        <button type="button" class="button seo-remove-block" style="float:left;">حذف</button>
        <p><input type="text" name="weblazem_seo_faq_items[<?php echo $i; ?>][question]" class="large-text" value="<?php echo esc_attr($item['question'] ?? ''); ?>" placeholder="سوال" /></p>
        <p><textarea name="weblazem_seo_faq_items[<?php echo $i; ?>][answer]" class="large-text" rows="2" placeholder="پاسخ"><?php echo esc_textarea($item['answer'] ?? ''); ?></textarea></p>
    </div>
    <?php
}

function weblazem_seo_admin_service_card($i, $card) {
    ?>
    <div class="seo-repeater-block" style="background:#f8f5fc;padding:16px;border-radius:12px;margin-bottom:16px;">
        <button type="button" class="button seo-remove-block" style="float:left;">حذف</button>
        <p><input type="text" name="weblazem_seo_service_cards[<?php echo $i; ?>][title]" class="large-text" value="<?php echo esc_attr($card['title'] ?? ''); ?>" placeholder="عنوان فارسی" /></p>
        <p><input type="text" name="weblazem_seo_service_cards[<?php echo $i; ?>][en_title]" class="large-text" value="<?php echo esc_attr($card['en_title'] ?? ''); ?>" placeholder="عنوان انگلیسی" /></p>
        <p><input type="text" name="weblazem_seo_service_cards[<?php echo $i; ?>][description]" class="large-text" value="<?php echo esc_attr($card['description'] ?? ''); ?>" placeholder="توضیح" /></p>
        <p><input type="text" name="weblazem_seo_service_cards[<?php echo $i; ?>][url]" class="large-text" value="<?php echo esc_attr($card['url'] ?? ''); ?>" placeholder="لینک" /></p>
        <p><input type="text" name="weblazem_seo_service_cards[<?php echo $i; ?>][shape_image]" class="large-text" value="<?php echo esc_attr($card['shape_image'] ?? ''); ?>" placeholder="URL شکل ۳D" /></p>
    </div>
    <?php
}

function weblazem_seo_admin_logo_row($i, $logo) {
    ?>
    <div class="seo-repeater-block" style="display:flex;gap:8px;margin-bottom:8px;align-items:center;">
        <input type="text" name="weblazem_seo_clients_logos[<?php echo $i; ?>][name]" value="<?php echo esc_attr($logo['name'] ?? ''); ?>" placeholder="نام" />
        <input type="text" name="weblazem_seo_clients_logos[<?php echo $i; ?>][logo]" class="large-text" value="<?php echo esc_attr($logo['logo'] ?? ''); ?>" placeholder="URL لوگو" />
        <input type="text" name="weblazem_seo_clients_logos[<?php echo $i; ?>][url]" value="<?php echo esc_attr($logo['url'] ?? ''); ?>" placeholder="لینک" />
        <button type="button" class="button seo-remove-block">حذف</button>
    </div>
    <?php
}

function weblazem_seo_admin_scripts_inline($icon_choices) {
    ?>
    <script>
    jQuery(function($) {
        var frame;
        $('.weblazem-tab').on('click', function() {
            var tab = $(this).data('tab');
            $('.weblazem-tab').removeClass('active');
            $(this).addClass('active');
            $('.weblazem-tab-content').removeClass('active');
            $('[data-tab-content="' + tab + '"]').addClass('active');
        });

        function openMedia(targetId) {
            if (frame) { frame.open(); return; }
            frame = wp.media({ title: 'انتخاب تصویر', button: { text: 'استفاده' }, multiple: false });
            frame.on('select', function() {
                var url = frame.state().get('selection').first().toJSON().url;
                $('#' + targetId).val(url);
                $('[data-for="' + targetId + '"]').html('<img src="' + url + '" style="max-width:200px;border-radius:8px;" alt="" />');
            });
            frame.open();
        }

        $(document).on('click', '.seo-upload-img', function(e) {
            e.preventDefault();
            var target = $(this).data('target');
            frame = wp.media({ title: 'انتخاب تصویر', button: { text: 'استفاده' }, multiple: false });
            frame.on('select', function() {
                var url = frame.state().get('selection').first().toJSON().url;
                $('#' + target).val(url);
                $('[data-for="' + target + '"]').html('<img src="' + url + '" style="max-width:200px;border-radius:8px;" alt="" />');
            });
            frame.open();
        });

        $(document).on('click', '.seo-remove-img', function(e) {
            e.preventDefault();
            var target = $(this).data('target');
            $('#' + target).val('');
            $('[data-for="' + target + '"]').empty();
        });

        $(document).on('click', '.seo-remove-block', function() {
            $(this).closest('.seo-repeater-block').remove();
        });

        var logoIdx = <?php echo count(get_option('weblazem_seo_clients_logos', array())); ?>;
        $('#add-seo-logo').on('click', function() {
            $('#seo-logos-container').append(
                '<div class="seo-repeater-block" style="display:flex;gap:8px;margin-bottom:8px;">' +
                '<input type="text" name="weblazem_seo_clients_logos[' + logoIdx + '][name]" placeholder="نام" />' +
                '<input type="text" name="weblazem_seo_clients_logos[' + logoIdx + '][logo]" class="large-text" placeholder="URL لوگو" />' +
                '<input type="text" name="weblazem_seo_clients_logos[' + logoIdx + '][url]" placeholder="لینک" />' +
                '<button type="button" class="button seo-remove-block">حذف</button></div>'
            );
            logoIdx++;
        });

        var splitIdx = <?php echo count(get_option('weblazem_seo_splits', array())); ?>;
        $('#add-seo-split').on('click', function() {
            $('#seo-splits-container').append(
                '<div class="seo-repeater-block" style="background:#f8f5fc;padding:16px;border-radius:12px;margin-bottom:16px;">' +
                '<button type="button" class="button seo-remove-block" style="float:left;">حذف</button>' +
                '<p><input type="text" name="weblazem_seo_splits[' + splitIdx + '][title]" class="large-text" placeholder="عنوان" /></p>' +
                '<p><textarea name="weblazem_seo_splits[' + splitIdx + '][text]" class="large-text" rows="3" placeholder="متن"></textarea></p>' +
                '<p><input type="text" name="weblazem_seo_splits[' + splitIdx + '][button_text]" placeholder="متن دکمه" />' +
                '<input type="text" name="weblazem_seo_splits[' + splitIdx + '][button_url]" placeholder="لینک" /></p>' +
                '<p><select name="weblazem_seo_splits[' + splitIdx + '][layout]"><option value="right">متن چپ</option><option value="left">تصویر چپ</option></select></p></div>'
            );
            splitIdx++;
        });

        var stepIdx = <?php echo count(get_option('weblazem_seo_process_steps', array())); ?>;
        $('#add-seo-step').on('click', function() {
            $('#seo-steps-container').append(
                '<p><input type="text" name="weblazem_seo_process_steps[' + stepIdx + '][title]" class="large-text" placeholder="عنوان مرحله" />' +
                '<button type="button" class="button seo-step-remove">حذف</button></p>'
            );
            stepIdx++;
        });
        $(document).on('click', '.seo-step-remove', function() { $(this).parent().remove(); });

        var advIdx = <?php echo count(get_option('weblazem_seo_advantages_items', array())); ?>;
        $('#add-seo-advantage').on('click', function() {
            $('#seo-advantages-container').append(
                '<div class="seo-repeater-block" style="background:#f8f5fc;padding:16px;border-radius:12px;margin-bottom:16px;">' +
                '<button type="button" class="button seo-remove-block" style="float:left;">حذف</button>' +
                '<p><input type="text" name="weblazem_seo_advantages_items[' + advIdx + '][title]" class="large-text" placeholder="عنوان" /></p>' +
                '<p><textarea name="weblazem_seo_advantages_items[' + advIdx + '][text]" class="large-text" rows="2" placeholder="توضیح"></textarea></p></div>'
            );
            advIdx++;
        });

        var planIdx = <?php echo count($plans); ?>;
        $('#add-seo-pricing-plan').on('click', function() {
            $('#seo-pricing-plans-container').append(
                '<div class="seo-repeater-block seo-pricing-plan-row" style="background:#f8f5fc;padding:16px;border-radius:12px;margin-bottom:16px;border:1px solid #e8dff0;">' +
                '<button type="button" class="button seo-remove-block" style="float:left;">حذف پلن</button>' +
                '<p><input type="text" name="weblazem_seo_pricing_plans[' + planIdx + '][title]" class="large-text" placeholder="عنوان پلن" /></p>' +
                '<p><input type="text" name="weblazem_seo_pricing_plans[' + planIdx + '][price]" class="large-text" placeholder="قیمت" /></p>' +
                '<p><strong>ویژگی‌ها</strong></p>' +
                '<div class="seo-plan-features" data-plan-index="' + planIdx + '">' +
                '<p class="seo-plan-feature-line"><input type="text" name="weblazem_seo_pricing_plans[' + planIdx + '][features][0]" class="large-text" placeholder="متن ویژگی" />' +
                '<button type="button" class="button seo-remove-feature">حذف</button></p></div>' +
                '<button type="button" class="button seo-add-plan-feature" data-plan-index="' + planIdx + '">افزودن ویژگی</button>' +
                '<p style="margin-top:12px;"><input type="text" name="weblazem_seo_pricing_plans[' + planIdx + '][button_text]" value="مشاوره رایگان" placeholder="متن دکمه" />' +
                '<input type="text" name="weblazem_seo_pricing_plans[' + planIdx + '][button_url]" class="large-text" placeholder="لینک دکمه" />' +
                '<label><input type="checkbox" name="weblazem_seo_pricing_plans[' + planIdx + '][button_modal]" value="1" checked /> باز کردن مودال مشاوره</label></p></div>'
            );
            planIdx++;
        });

        $(document).on('click', '.seo-add-plan-feature', function() {
            var planIndex = $(this).data('plan-index');
            var $wrap = $(this).siblings('.seo-plan-features');
            var featureIdx = $wrap.find('.seo-plan-feature-line').length;
            $wrap.append(
                '<p class="seo-plan-feature-line"><input type="text" name="weblazem_seo_pricing_plans[' + planIndex + '][features][' + featureIdx + ']" class="large-text" placeholder="متن ویژگی" />' +
                '<button type="button" class="button seo-remove-feature">حذف</button></p>'
            );
        });

        $(document).on('click', '.seo-remove-feature', function() {
            $(this).closest('.seo-plan-feature-line').remove();
        });

        var faqIdx = <?php echo count(get_option('weblazem_seo_faq_items', array())); ?>;
        $('#add-seo-faq').on('click', function() {
            $('#seo-faq-container').append(
                '<div class="seo-repeater-block" style="background:#f8f5fc;padding:16px;border-radius:12px;margin-bottom:16px;">' +
                '<button type="button" class="button seo-remove-block" style="float:left;">حذف</button>' +
                '<p><input type="text" name="weblazem_seo_faq_items[' + faqIdx + '][question]" class="large-text" placeholder="سوال" /></p>' +
                '<p><textarea name="weblazem_seo_faq_items[' + faqIdx + '][answer]" class="large-text" rows="2" placeholder="پاسخ"></textarea></p></div>'
            );
            faqIdx++;
        });

        var cardIdx = <?php echo count(get_option('weblazem_seo_service_cards', array())); ?>;
        $('#add-seo-service-card').on('click', function() {
            $('#seo-service-cards').append(
                '<div class="seo-repeater-block" style="background:#f8f5fc;padding:16px;border-radius:12px;margin-bottom:16px;">' +
                '<button type="button" class="button seo-remove-block" style="float:left;">حذف</button>' +
                '<p><input type="text" name="weblazem_seo_service_cards[' + cardIdx + '][title]" class="large-text" placeholder="عنوان" /></p>' +
                '<p><input type="text" name="weblazem_seo_service_cards[' + cardIdx + '][en_title]" placeholder="EN" />' +
                '<input type="text" name="weblazem_seo_service_cards[' + cardIdx + '][url]" placeholder="لینک" /></p></div>'
            );
            cardIdx++;
        });
    });
    </script>
    <?php
}

