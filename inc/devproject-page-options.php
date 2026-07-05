<?php
/**
 * Custom development page — admin settings.
 */

require_once get_template_directory() . '/inc/devproject-defaults.php';

function weblazem_devproject_options_menu() {
    add_submenu_page(
        'weblazem-theme-options',
        'تنظیمات برنامه نویسی و پروژه اختصاصی',
        '  برنامه نویسی و پروژه',
        'manage_options',
        'weblazem-devproject-options',
        'weblazem_devproject_options_display'
    );
}
add_action('admin_menu', 'weblazem_devproject_options_menu', 22);

function weblazem_register_devproject_settings() {
    $defaults = weblazem_devproject_defaults();

    foreach (array_keys($defaults) as $key) {
        register_setting('weblazem_devproject_group', 'weblazem_devproject_' . $key);
    }

    register_setting('weblazem_devproject_group', 'weblazem_devproject_splits', array('sanitize_callback' => 'weblazem_sanitize_devproject_splits'));
    register_setting('weblazem_devproject_group', 'weblazem_devproject_process_steps', array('sanitize_callback' => 'weblazem_sanitize_devproject_steps'));
    register_setting('weblazem_devproject_group', 'weblazem_devproject_advantages_items', array('sanitize_callback' => 'weblazem_sanitize_devproject_advantages'));
    register_setting('weblazem_devproject_group', 'weblazem_devproject_faq_items', array('sanitize_callback' => 'weblazem_sanitize_devproject_faq'));
    register_setting('weblazem_devproject_group', 'weblazem_devproject_service_cards', array('sanitize_callback' => 'weblazem_sanitize_devproject_service_cards'));
    register_setting('weblazem_devproject_group', 'weblazem_devproject_portfolio_tabs', array('sanitize_callback' => 'weblazem_sanitize_devproject_portfolio_tabs'));
    register_setting('weblazem_devproject_group', 'weblazem_devproject_customers_logos', array('sanitize_callback' => 'weblazem_sanitize_devproject_logos'));
    register_setting('weblazem_devproject_group', 'weblazem_devproject_portfolio_items', array('sanitize_callback' => 'weblazem_sanitize_devproject_portfolio_items'));

    foreach (weblazem_get_devproject_sections_config() as $key => $label) {
        register_setting('weblazem_devproject_group', 'weblazem_devproject_section_' . $key . '_enabled');
    }
}
add_action('admin_init', 'weblazem_register_devproject_settings');

function weblazem_sanitize_devproject_splits($input) {
    if (!is_array($input)) {
        return weblazem_get_default_devproject_splits();
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
    return !empty($out) ? $out : weblazem_get_default_devproject_splits();
}

function weblazem_sanitize_devproject_steps($input) {
    if (!is_array($input)) {
        return weblazem_get_default_devproject_process_steps();
    }
    $out = array();
    foreach ($input as $row) {
        if (empty($row['title'])) {
            continue;
        }
        $out[] = array('title' => sanitize_text_field($row['title']));
    }
    return !empty($out) ? $out : weblazem_get_default_devproject_process_steps();
}

function weblazem_sanitize_devproject_advantages($input) {
    if (!is_array($input)) {
        return weblazem_get_default_devproject_advantages();
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
    return !empty($out) ? $out : weblazem_get_default_devproject_advantages();
}

function weblazem_sanitize_devproject_faq($input) {
    if (!is_array($input)) {
        return weblazem_get_default_devproject_faq_items();
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

function weblazem_sanitize_devproject_service_cards($input) {
    if (!is_array($input)) {
        return weblazem_get_default_devproject_service_cards();
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

function weblazem_sanitize_devproject_portfolio_tabs($input) {
    if (!is_array($input)) {
        return weblazem_get_default_devproject_portfolio_tabs();
    }
    $out = array();
    foreach ($input as $tab) {
        if (empty($tab['title'])) {
            continue;
        }
        $out[] = array(
            'key'      => !empty($tab['key']) ? sanitize_key($tab['key']) : sanitize_title($tab['title']),
            'title'    => sanitize_text_field($tab['title']),
            'category' => !empty($tab['category']) ? sanitize_title($tab['category']) : '',
        );
    }
    return !empty($out) ? $out : weblazem_get_default_devproject_portfolio_tabs();
}

function weblazem_sanitize_devproject_logos($input) {
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

function weblazem_sanitize_devproject_portfolio_items($input) {
    if (!is_array($input)) {
        return array();
    }
    $out = array();
    foreach ($input as $row) {
        if (empty($row['title']) && empty($row['image'])) {
            continue;
        }
        $out[] = array(
            'title'     => sanitize_text_field($row['title'] ?? ''),
            'image'     => esc_url_raw($row['image'] ?? ''),
            'logo'      => esc_url_raw($row['logo'] ?? ''),
            'logo_text' => sanitize_text_field($row['logo_text'] ?? ''),
            'link'      => esc_url_raw($row['link'] ?? '#'),
            'tag'       => sanitize_text_field($row['tag'] ?? ''),
            'color'     => sanitize_hex_color($row['color'] ?? '') ?: '#1d4ed8',
            'category'  => sanitize_text_field($row['category'] ?? ''),
        );
    }
    return $out;
}

function weblazem_devproject_handle_section_checkboxes() {
    if (!isset($_POST['option_page']) || $_POST['option_page'] !== 'weblazem_devproject_group') {
        return;
    }
    foreach (weblazem_get_devproject_sections_config() as $key => $label) {
        $option_key = 'weblazem_devproject_section_' . $key . '_enabled';
        if (!isset($_POST[$option_key])) {
            update_option($option_key, '0');
        }
    }
}
add_action('admin_init', 'weblazem_devproject_handle_section_checkboxes', 20);

function weblazem_devproject_admin_scripts($hook) {
    if (strpos($hook, 'weblazem-devproject-options') === false) {
        return;
    }
    wp_enqueue_media();
}
add_action('admin_enqueue_scripts', 'weblazem_devproject_admin_scripts');

function weblazem_devproject_opt($key) {
    $defaults = weblazem_devproject_defaults();
    return get_option('weblazem_devproject_' . $key, $defaults[$key] ?? '');
}

function weblazem_devproject_options_display() {
    $page_url   = weblazem_get_devproject_page_url();
    $tabs       = weblazem_get_devproject_portfolio_tabs();
    $categories = function_exists('weblazem_get_portfolio_category_choices') ? weblazem_get_portfolio_category_choices() : array('' => 'همه پروژه‌ها');
    $splits     = get_option('weblazem_devproject_splits', weblazem_get_default_devproject_splits());
    $steps      = get_option('weblazem_devproject_process_steps', weblazem_get_default_devproject_process_steps());
    $advantages = get_option('weblazem_devproject_advantages_items', weblazem_get_default_devproject_advantages());
    $faq_items  = get_option('weblazem_devproject_faq_items', weblazem_get_default_devproject_faq_items());
    $cards      = get_option('weblazem_devproject_service_cards', weblazem_get_default_devproject_service_cards());
    $logos      = get_option('weblazem_devproject_customers_logos', weblazem_get_default_devproject_customer_logos());
    $manual_items = get_option('weblazem_devproject_portfolio_items', array());
    $icon_choices = array(
        'cube' => 'مکعب', 'document' => 'سند', 'heart' => 'قلب', 'headset' => 'پشتیبانی',
        'layers' => 'لایه‌ها', 'rocket' => 'راکت', 'chart' => 'نمودار', 'nodes' => 'شبکه',
    );
    ?>
    <div class="wrap" dir="rtl">
        <div class="weblazem-admin-header">
            <div class="weblazem-admin-header-content">
                <h1>تنظیمات صفحه برنامه نویسی و پروژه اختصاصی</h1>
                <p>
                    محتوای صفحه داخلی «برنامه نویسی و پروژه اختصاصی» را مدیریت کنید.
                    <a href="<?php echo esc_url($page_url); ?>" target="_blank" rel="noopener">مشاهده صفحه</a>
                </p>
            </div>
        </div>

        <div class="weblazem-admin-content" style="margin-top:24px;">
            <div class="weblazem-tabs" style="margin-bottom:20px;">
                <?php
                $admin_tabs = array(
                    'layout' => 'چیدمان سکشن‌ها', 'hero' => 'هیرو', 'portfolio' => 'نمونه‌کارها',
                    'customers' => 'مشتریان', 'splits' => 'بخش‌های دو ستونه', 'process' => 'فرآیند',
                    'advantages' => 'مزایا', 'faq' => 'FAQ و تماس',
                );
                $first = true;
                foreach ($admin_tabs as $id => $label) :
                    ?>
                    <button type="button" class="weblazem-tab<?php echo $first ? ' active' : ''; ?>" data-tab="<?php echo esc_attr($id); ?>"><?php echo esc_html($label); ?></button>
                    <?php $first = false; endforeach; ?>
            </div>

            <form method="post" action="options.php" id="devproject-options-form">
                <?php settings_fields('weblazem_devproject_group'); ?>

                <div class="weblazem-tab-content active" data-tab-content="layout">
                    <div class="weblazem-admin-card">
                        <h3>فعال‌سازی سکشن‌ها</h3>
                        <table class="form-table">
                            <?php foreach (weblazem_get_devproject_sections_config() as $key => $label) :
                                $option_key = 'weblazem_devproject_section_' . $key . '_enabled';
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
                        <?php weblazem_devproject_admin_field('hero_en_title', 'عنوان انگلیسی'); ?>
                        <?php weblazem_devproject_admin_image('hero_calligraphy_image', 'تصویر خوشنویسی'); ?>
                        <?php weblazem_devproject_admin_textarea('hero_calligraphy_text', 'متن خوشنویسی (HTML)', 'در صورت نبود تصویر'); ?>
                        <?php weblazem_devproject_admin_field('hero_title', 'عنوان فارسی'); ?>
                        <?php weblazem_devproject_admin_textarea('hero_text', 'متن توضیحی'); ?>
                        <?php weblazem_devproject_admin_image('hero_image', 'تصویر مانیتور / ایلاستریشن'); ?>
                        <h4>شمارنده‌ها</h4>
                        <?php weblazem_devproject_admin_field('hero_stat1_number', 'شمارنده ۱ — عدد'); ?>
                        <?php weblazem_devproject_admin_field('hero_stat1_title', 'شمارنده ۱ — عنوان'); ?>
                        <?php weblazem_devproject_admin_field('hero_stat1_desc', 'شمارنده ۱ — توضیح'); ?>
                        <?php weblazem_devproject_admin_field('hero_stat2_number', 'شمارنده ۲ — عدد'); ?>
                        <?php weblazem_devproject_admin_field('hero_stat2_title', 'شمارنده ۲ — عنوان'); ?>
                        <?php weblazem_devproject_admin_field('hero_stat2_desc', 'شمارنده ۲ — توضیح'); ?>
                    </div>
                </div>

                <div class="weblazem-tab-content" data-tab-content="portfolio">
                    <div class="weblazem-admin-card">
                        <h3>نمونه‌کارها (Success Stories)</h3>
                        <?php weblazem_devproject_admin_image('portfolio_calligraphy_image', 'تصویر خوشنویسی'); ?>
                        <?php weblazem_devproject_admin_textarea('portfolio_calligraphy_text', 'متن خوشنویسی'); ?>
                        <?php weblazem_devproject_admin_field('portfolio_subtitle', 'زیرعنوان'); ?>
                        <?php weblazem_devproject_admin_textarea('portfolio_description', 'توضیح'); ?>
                        <?php weblazem_devproject_admin_field('portfolio_en_label', 'برچسب انگلیسی'); ?>
                        <?php weblazem_devproject_admin_field('portfolio_count', 'تعداد نمونه‌کار از CPT'); ?>
                        <p class="description">نمونه‌کارها از پست‌تایپ portfolio خوانده می‌شوند. کارت‌های دستی زیر به آن‌ها اضافه می‌شوند.</p>
                        <h4>تب‌های فیلتر</h4>
                        <div id="devproject-tabs-container">
                            <?php foreach ($tabs as $i => $tab) : weblazem_devproject_admin_portfolio_tab($i, $tab, $categories); endforeach; ?>
                        </div>
                        <button type="button" class="button" id="add-devproject-tab">افزودن تب</button>
                        <h4 style="margin-top:24px;">کارت‌های دستی (اختیاری)</h4>
                        <div id="devproject-portfolio-items">
                            <?php if (!empty($manual_items)) : foreach ($manual_items as $i => $item) : weblazem_devproject_admin_portfolio_item($i, $item); endforeach; endif; ?>
                        </div>
                        <button type="button" class="button" id="add-devproject-portfolio-item">افزودن کارت</button>
                    </div>
                </div>

                <div class="weblazem-tab-content" data-tab-content="customers">
                    <div class="weblazem-admin-card">
                        <h3>مشتریان</h3>
                        <?php weblazem_devproject_admin_image('customers_calligraphy_image', 'تصویر خوشنویسی'); ?>
                        <?php weblazem_devproject_admin_field('customers_calligraphy_text', 'متن خوشنویسی'); ?>
                        <?php weblazem_devproject_admin_field('customers_counter', 'شمارنده'); ?>
                        <?php weblazem_devproject_admin_field('customers_counter_label', 'برچسب انگلیسی شمارنده'); ?>
                        <?php weblazem_devproject_admin_image('customers_bottom_icon', 'آیکون پایین سکشن'); ?>
                        <h4>لوگوها</h4>
                        <div id="devproject-logos-container">
                            <?php foreach ($logos as $i => $logo) : weblazem_devproject_admin_logo_row($i, $logo); endforeach; ?>
                        </div>
                        <button type="button" class="button" id="add-devproject-logo">افزودن لوگو</button>
                    </div>
                </div>

                <div class="weblazem-tab-content" data-tab-content="splits">
                    <div class="weblazem-admin-card">
                        <h3>بخش‌های دو ستونه</h3>
                        <div id="devproject-splits-container">
                            <?php foreach ($splits as $i => $split) : weblazem_devproject_admin_split_row($i, $split); endforeach; ?>
                        </div>
                        <button type="button" class="button" id="add-devproject-split">افزودن بخش</button>
                    </div>
                </div>

                <div class="weblazem-tab-content" data-tab-content="process">
                    <div class="weblazem-admin-card">
                        <h3>فرآیند کار</h3>
                        <?php weblazem_devproject_admin_image('process_calligraphy_image', 'تصویر خوشنویسی'); ?>
                        <?php weblazem_devproject_admin_textarea('process_calligraphy_text', 'متن خوشنویسی'); ?>
                        <?php weblazem_devproject_admin_field('process_subtitle', 'زیرعنوان'); ?>
                        <?php weblazem_devproject_admin_textarea('process_description', 'توضیح'); ?>
                        <?php weblazem_devproject_admin_field('process_start_note', 'یادداشت شروع'); ?>
                        <?php weblazem_devproject_admin_field('process_journey_caption', 'متن پایین کارت‌ها'); ?>
                        <h4>مراحل</h4>
                        <div id="devproject-steps-container">
                            <?php foreach ($steps as $i => $step) : ?>
                                <p><input type="text" name="weblazem_devproject_process_steps[<?php echo $i; ?>][title]" class="large-text" value="<?php echo esc_attr($step['title']); ?>" placeholder="عنوان مرحله" />
                                <button type="button" class="button devproject-step-remove">حذف</button></p>
                            <?php endforeach; ?>
                        </div>
                        <button type="button" class="button" id="add-devproject-step">افزودن مرحله</button>
                        <h4>CSAT و دکمه‌ها</h4>
                        <?php weblazem_devproject_admin_field('process_csat_number', 'درصد CSAT'); ?>
                        <?php weblazem_devproject_admin_field('process_csat_sub', 'زیرنویس CSAT'); ?>
                        <?php weblazem_devproject_admin_field('process_csat_label', 'برچسب CSAT'); ?>
                        <?php weblazem_devproject_admin_field('process_btn1_text', 'دکمه چپ — متن'); ?>
                        <?php weblazem_devproject_admin_field('process_btn1_url', 'دکمه چپ — لینک'); ?>
                        <?php weblazem_devproject_admin_field('process_btn2_text', 'دکمه راست — متن'); ?>
                        <?php weblazem_devproject_admin_field('process_btn2_url', 'دکمه راست — لینک'); ?>
                    </div>
                </div>

                <div class="weblazem-tab-content" data-tab-content="advantages">
                    <div class="weblazem-admin-card">
                        <h3>مزایا</h3>
                        <?php weblazem_devproject_admin_field('advantages_title', 'عنوان'); ?>
                        <?php weblazem_devproject_admin_textarea('advantages_subtitle', 'زیرعنوان'); ?>
                        <div id="devproject-advantages-container">
                            <?php foreach ($advantages as $i => $item) : weblazem_devproject_admin_advantage_row($i, $item, $icon_choices); endforeach; ?>
                        </div>
                        <button type="button" class="button" id="add-devproject-advantage">افزودن مزیت</button>
                    </div>
                </div>

                <div class="weblazem-tab-content" data-tab-content="faq">
                    <div class="weblazem-admin-card">
                        <h3>FAQ و تماس</h3>
                        <?php weblazem_devproject_admin_image('faq_calligraphy_image', 'تصویر خوشنویسی'); ?>
                        <?php weblazem_devproject_admin_textarea('faq_calligraphy_text', 'متن خوشنویسی'); ?>
                        <?php weblazem_devproject_admin_field('faq_subtitle', 'عنوان FAQ'); ?>
                        <?php weblazem_devproject_admin_textarea('faq_intro', 'متن مقدمه'); ?>
                        <?php weblazem_devproject_admin_image('faq_profile_image', 'تصویر پروفایل'); ?>
                        <?php weblazem_devproject_admin_field('faq_phone', 'شماره تماس'); ?>
                        <?php weblazem_devproject_admin_field('faq_consult_btn_text', 'متن دکمه مشاوره'); ?>
                        <?php weblazem_devproject_admin_textarea('faq_footer_text', 'متن پایین کارت پروفایل'); ?>
                        <h4>سوالات</h4>
                        <div id="devproject-faq-container">
                            <?php foreach ($faq_items as $i => $item) : weblazem_devproject_admin_faq_row($i, $item); endforeach; ?>
                        </div>
                        <button type="button" class="button" id="add-devproject-faq">افزودن سوال</button>
                        <h4 style="margin-top:24px;">کارت‌های خدمات پایین صفحه</h4>
                        <div id="devproject-service-cards">
                            <?php foreach ($cards as $i => $card) : weblazem_devproject_admin_service_card($i, $card); endforeach; ?>
                        </div>
                        <button type="button" class="button" id="add-devproject-service-card">افزودن کارت</button>
                    </div>
                </div>

                <?php submit_button('ذخیره تنظیمات'); ?>
            </form>
        </div>
    </div>

    <?php weblazem_devproject_admin_scripts_inline($categories, $icon_choices); ?>
    <?php
}

function weblazem_devproject_admin_field($key, $label) {
    $val = weblazem_devproject_opt($key);
    echo '<p><label><strong>' . esc_html($label) . '</strong><br>';
    echo '<input type="text" name="weblazem_devproject_' . esc_attr($key) . '" class="large-text" value="' . esc_attr($val) . '" /></label></p>';
}

function weblazem_devproject_admin_textarea($key, $label, $desc = '') {
    $val = weblazem_devproject_opt($key);
    echo '<p><label><strong>' . esc_html($label) . '</strong>';
    if ($desc) {
        echo ' <span class="description">' . esc_html($desc) . '</span>';
    }
    echo '<br><textarea name="weblazem_devproject_' . esc_attr($key) . '" class="large-text" rows="3">' . esc_textarea($val) . '</textarea></label></p>';
}

function weblazem_devproject_admin_image($key, $label) {
    $val = weblazem_devproject_opt($key);
    $id  = 'devproject_img_' . $key;
    echo '<p><strong>' . esc_html($label) . '</strong><br>';
    echo '<input type="hidden" id="' . esc_attr($id) . '" name="weblazem_devproject_' . esc_attr($key) . '" value="' . esc_attr($val) . '" />';
    echo '<div class="devproject-img-preview" data-for="' . esc_attr($id) . '" style="margin:8px 0;">';
    if ($val) {
        echo '<img src="' . esc_url($val) . '" style="max-width:200px;border-radius:8px;" alt="" />';
    }
    echo '</div>';
    echo '<button type="button" class="button devproject-upload-img" data-target="' . esc_attr($id) . '">انتخاب تصویر</button> ';
    echo '<button type="button" class="button devproject-remove-img" data-target="' . esc_attr($id) . '">حذف</button></p>';
}

function weblazem_devproject_admin_split_row($i, $split) {
    ?>
    <div class="devproject-repeater-block" style="background:#f8f5fc;padding:16px;border-radius:12px;margin-bottom:16px;border:1px solid #e8dff0;">
        <button type="button" class="button devproject-remove-block" style="float:left;">حذف</button>
        <p><input type="text" name="weblazem_devproject_splits[<?php echo $i; ?>][title]" class="large-text" value="<?php echo esc_attr($split['title'] ?? ''); ?>" placeholder="عنوان" /></p>
        <p><textarea name="weblazem_devproject_splits[<?php echo $i; ?>][text]" class="large-text" rows="3" placeholder="متن"><?php echo esc_textarea($split['text'] ?? ''); ?></textarea></p>
        <p>
            <input type="text" name="weblazem_devproject_splits[<?php echo $i; ?>][button_text]" value="<?php echo esc_attr($split['button_text'] ?? ''); ?>" placeholder="متن دکمه" />
            <input type="text" name="weblazem_devproject_splits[<?php echo $i; ?>][button_url]" class="large-text" value="<?php echo esc_attr($split['button_url'] ?? ''); ?>" placeholder="لینک دکمه" />
            <label><input type="checkbox" name="weblazem_devproject_splits[<?php echo $i; ?>][button_modal]" value="1" <?php checked($split['button_modal'] ?? '', '1'); ?> /> باز کردن مودال مشاوره</label>
        </p>
        <p><input type="text" name="weblazem_devproject_splits[<?php echo $i; ?>][image]" class="large-text devproject-split-image" value="<?php echo esc_attr($split['image'] ?? ''); ?>" placeholder="URL تصویر" />
        <button type="button" class="button devproject-upload-split-img">انتخاب تصویر</button></p>
        <p><input type="text" name="weblazem_devproject_splits[<?php echo $i; ?>][caption]" class="large-text" value="<?php echo esc_attr($split['caption'] ?? ''); ?>" placeholder="کپشن دست‌نویس" /></p>
        <p>چیدمان تصویر:
            <select name="weblazem_devproject_splits[<?php echo $i; ?>][layout]">
                <option value="right" <?php selected($split['layout'] ?? '', 'right'); ?>>متن چپ — تصویر راست</option>
                <option value="left" <?php selected($split['layout'] ?? '', 'left'); ?>>تصویر چپ — متن راست</option>
            </select>
        </p>
    </div>
    <?php
}

function weblazem_devproject_admin_advantage_row($i, $item, $icons) {
    ?>
    <div class="devproject-repeater-block" style="background:#f8f5fc;padding:16px;border-radius:12px;margin-bottom:16px;">
        <button type="button" class="button devproject-remove-block" style="float:left;">حذف</button>
        <p><select name="weblazem_devproject_advantages_items[<?php echo $i; ?>][icon]">
            <?php foreach ($icons as $k => $label) : ?>
                <option value="<?php echo esc_attr($k); ?>" <?php selected($item['icon'] ?? '', $k); ?>><?php echo esc_html($label); ?></option>
            <?php endforeach; ?>
        </select></p>
        <p><input type="text" name="weblazem_devproject_advantages_items[<?php echo $i; ?>][icon_image]" class="large-text" value="<?php echo esc_attr($item['icon_image'] ?? ''); ?>" placeholder="URL آیکون سفارشی (اختیاری)" /></p>
        <p><input type="text" name="weblazem_devproject_advantages_items[<?php echo $i; ?>][title]" class="large-text" value="<?php echo esc_attr($item['title'] ?? ''); ?>" placeholder="عنوان" /></p>
        <p><textarea name="weblazem_devproject_advantages_items[<?php echo $i; ?>][text]" class="large-text" rows="2" placeholder="توضیح"><?php echo esc_textarea($item['text'] ?? ''); ?></textarea></p>
    </div>
    <?php
}

function weblazem_devproject_admin_faq_row($i, $item) {
    ?>
    <div class="devproject-repeater-block" style="background:#f8f5fc;padding:16px;border-radius:12px;margin-bottom:16px;">
        <button type="button" class="button devproject-remove-block" style="float:left;">حذف</button>
        <p><input type="text" name="weblazem_devproject_faq_items[<?php echo $i; ?>][question]" class="large-text" value="<?php echo esc_attr($item['question'] ?? ''); ?>" placeholder="سوال" /></p>
        <p><textarea name="weblazem_devproject_faq_items[<?php echo $i; ?>][answer]" class="large-text" rows="2" placeholder="پاسخ"><?php echo esc_textarea($item['answer'] ?? ''); ?></textarea></p>
    </div>
    <?php
}

function weblazem_devproject_admin_service_card($i, $card) {
    ?>
    <div class="devproject-repeater-block" style="background:#f8f5fc;padding:16px;border-radius:12px;margin-bottom:16px;">
        <button type="button" class="button devproject-remove-block" style="float:left;">حذف</button>
        <p><input type="text" name="weblazem_devproject_service_cards[<?php echo $i; ?>][title]" class="large-text" value="<?php echo esc_attr($card['title'] ?? ''); ?>" placeholder="عنوان فارسی" /></p>
        <p><input type="text" name="weblazem_devproject_service_cards[<?php echo $i; ?>][en_title]" class="large-text" value="<?php echo esc_attr($card['en_title'] ?? ''); ?>" placeholder="عنوان انگلیسی" /></p>
        <p><input type="text" name="weblazem_devproject_service_cards[<?php echo $i; ?>][description]" class="large-text" value="<?php echo esc_attr($card['description'] ?? ''); ?>" placeholder="توضیح" /></p>
        <p><input type="text" name="weblazem_devproject_service_cards[<?php echo $i; ?>][url]" class="large-text" value="<?php echo esc_attr($card['url'] ?? ''); ?>" placeholder="لینک" /></p>
        <p><input type="text" name="weblazem_devproject_service_cards[<?php echo $i; ?>][shape_image]" class="large-text" value="<?php echo esc_attr($card['shape_image'] ?? ''); ?>" placeholder="URL شکل ۳D" /></p>
    </div>
    <?php
}

function weblazem_devproject_admin_logo_row($i, $logo) {
    ?>
    <div class="devproject-repeater-block" style="display:flex;gap:8px;margin-bottom:8px;align-items:center;">
        <input type="text" name="weblazem_devproject_customers_logos[<?php echo $i; ?>][name]" value="<?php echo esc_attr($logo['name'] ?? ''); ?>" placeholder="نام" />
        <input type="text" name="weblazem_devproject_customers_logos[<?php echo $i; ?>][logo]" class="large-text" value="<?php echo esc_attr($logo['logo'] ?? ''); ?>" placeholder="URL لوگو" />
        <input type="text" name="weblazem_devproject_customers_logos[<?php echo $i; ?>][url]" value="<?php echo esc_attr($logo['url'] ?? ''); ?>" placeholder="لینک" />
        <button type="button" class="button devproject-remove-block">حذف</button>
    </div>
    <?php
}

function weblazem_devproject_admin_portfolio_tab($i, $tab, $categories) {
    ?>
    <div class="devproject-repeater-block" style="background:#f8f5fc;padding:12px;border-radius:8px;margin-bottom:8px;">
        <button type="button" class="button devproject-remove-block" style="float:left;">حذف</button>
        <input type="text" name="weblazem_devproject_portfolio_tabs[<?php echo $i; ?>][title]" value="<?php echo esc_attr($tab['title']); ?>" placeholder="عنوان تب" />
        <input type="text" name="weblazem_devproject_portfolio_tabs[<?php echo $i; ?>][key]" value="<?php echo esc_attr($tab['key']); ?>" placeholder="شناسه" dir="ltr" />
        <select name="weblazem_devproject_portfolio_tabs[<?php echo $i; ?>][category]">
            <?php foreach ($categories as $slug => $label) : ?>
                <option value="<?php echo esc_attr($slug); ?>" <?php selected($tab['category'], $slug); ?>><?php echo esc_html($label); ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <?php
}

function weblazem_devproject_admin_portfolio_item($i, $item) {
    ?>
    <div class="devproject-repeater-block" style="background:#f0f4ff;padding:12px;border-radius:8px;margin-bottom:8px;">
        <button type="button" class="button devproject-remove-block" style="float:left;">حذف</button>
        <p><input type="text" name="weblazem_devproject_portfolio_items[<?php echo $i; ?>][title]" class="large-text" value="<?php echo esc_attr($item['title'] ?? ''); ?>" placeholder="عنوان" /></p>
        <p><input type="text" name="weblazem_devproject_portfolio_items[<?php echo $i; ?>][image]" class="large-text" value="<?php echo esc_attr($item['image'] ?? ''); ?>" placeholder="تصویر" /></p>
        <p><input type="text" name="weblazem_devproject_portfolio_items[<?php echo $i; ?>][logo]" value="<?php echo esc_attr($item['logo'] ?? ''); ?>" placeholder="لوگو" />
        <input type="text" name="weblazem_devproject_portfolio_items[<?php echo $i; ?>][logo_text]" value="<?php echo esc_attr($item['logo_text'] ?? ''); ?>" placeholder="متن لوگو" /></p>
        <p><input type="text" name="weblazem_devproject_portfolio_items[<?php echo $i; ?>][link]" value="<?php echo esc_attr($item['link'] ?? ''); ?>" placeholder="لینک" />
        <input type="text" name="weblazem_devproject_portfolio_items[<?php echo $i; ?>][tag]" value="<?php echo esc_attr($item['tag'] ?? ''); ?>" placeholder="برچسب SPECIAL" />
        <input type="color" name="weblazem_devproject_portfolio_items[<?php echo $i; ?>][color]" value="<?php echo esc_attr($item['color'] ?? '#1d4ed8'); ?>" />
        <input type="text" name="weblazem_devproject_portfolio_items[<?php echo $i; ?>][category]" value="<?php echo esc_attr($item['category'] ?? ''); ?>" placeholder="دسته (slug)" dir="ltr" /></p>
    </div>
    <?php
}

function weblazem_devproject_admin_scripts_inline($categories, $icon_choices) {
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

        $(document).on('click', '.devproject-upload-img', function(e) {
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

        $(document).on('click', '.devproject-remove-img', function(e) {
            e.preventDefault();
            var target = $(this).data('target');
            $('#' + target).val('');
            $('[data-for="' + target + '"]').empty();
        });

        $(document).on('click', '.devproject-remove-block', function() {
            $(this).closest('.devproject-repeater-block').remove();
        });

        var tabIdx = <?php echo count(weblazem_get_devproject_portfolio_tabs()); ?>;
        var catOpts = <?php echo wp_json_encode($categories); ?>;
        function catHtml(sel) {
            var h = '';
            Object.keys(catOpts).forEach(function(k) {
                h += '<option value="' + k + '"' + (k === sel ? ' selected' : '') + '>' + catOpts[k] + '</option>';
            });
            return h;
        }

        $('#add-devproject-tab').on('click', function() {
            $('#devproject-tabs-container').append(
                '<div class="devproject-repeater-block" style="background:#f8f5fc;padding:12px;border-radius:8px;margin-bottom:8px;">' +
                '<button type="button" class="button devproject-remove-block" style="float:left;">حذف</button>' +
                '<input type="text" name="weblazem_devproject_portfolio_tabs[' + tabIdx + '][title]" placeholder="عنوان تب" />' +
                '<input type="text" name="weblazem_devproject_portfolio_tabs[' + tabIdx + '][key]" placeholder="شناسه" dir="ltr" />' +
                '<select name="weblazem_devproject_portfolio_tabs[' + tabIdx + '][category]">' + catHtml('') + '</select></div>'
            );
            tabIdx++;
        });

        var itemIdx = <?php echo count(get_option('weblazem_devproject_portfolio_items', array())); ?>;
        $('#add-devproject-portfolio-item').on('click', function() {
            $('#devproject-portfolio-items').append(
                '<div class="devproject-repeater-block" style="background:#f0f4ff;padding:12px;border-radius:8px;margin-bottom:8px;">' +
                '<button type="button" class="button devproject-remove-block" style="float:left;">حذف</button>' +
                '<p><input type="text" name="weblazem_devproject_portfolio_items[' + itemIdx + '][title]" class="large-text" placeholder="عنوان" /></p>' +
                '<p><input type="text" name="weblazem_devproject_portfolio_items[' + itemIdx + '][image]" class="large-text" placeholder="تصویر" /></p>' +
                '<p><input type="text" name="weblazem_devproject_portfolio_items[' + itemIdx + '][logo_text]" placeholder="متن لوگو" />' +
                '<input type="text" name="weblazem_devproject_portfolio_items[' + itemIdx + '][link]" placeholder="لینک" />' +
                '<input type="color" name="weblazem_devproject_portfolio_items[' + itemIdx + '][color]" value="#1d4ed8" /></p></div>'
            );
            itemIdx++;
        });

        var logoIdx = <?php echo count(get_option('weblazem_devproject_customers_logos', array())); ?>;
        $('#add-devproject-logo').on('click', function() {
            $('#devproject-logos-container').append(
                '<div class="devproject-repeater-block" style="display:flex;gap:8px;margin-bottom:8px;">' +
                '<input type="text" name="weblazem_devproject_customers_logos[' + logoIdx + '][name]" placeholder="نام" />' +
                '<input type="text" name="weblazem_devproject_customers_logos[' + logoIdx + '][logo]" class="large-text" placeholder="URL لوگو" />' +
                '<input type="text" name="weblazem_devproject_customers_logos[' + logoIdx + '][url]" placeholder="لینک" />' +
                '<button type="button" class="button devproject-remove-block">حذف</button></div>'
            );
            logoIdx++;
        });

        var splitIdx = <?php echo count(get_option('weblazem_devproject_splits', array())); ?>;
        $('#add-devproject-split').on('click', function() {
            $('#devproject-splits-container').append(
                '<div class="devproject-repeater-block" style="background:#f8f5fc;padding:16px;border-radius:12px;margin-bottom:16px;">' +
                '<button type="button" class="button devproject-remove-block" style="float:left;">حذف</button>' +
                '<p><input type="text" name="weblazem_devproject_splits[' + splitIdx + '][title]" class="large-text" placeholder="عنوان" /></p>' +
                '<p><textarea name="weblazem_devproject_splits[' + splitIdx + '][text]" class="large-text" rows="3" placeholder="متن"></textarea></p>' +
                '<p><input type="text" name="weblazem_devproject_splits[' + splitIdx + '][button_text]" placeholder="متن دکمه" />' +
                '<input type="text" name="weblazem_devproject_splits[' + splitIdx + '][button_url]" placeholder="لینک" /></p>' +
                '<p><select name="weblazem_devproject_splits[' + splitIdx + '][layout]"><option value="right">متن چپ</option><option value="left">تصویر چپ</option></select></p></div>'
            );
            splitIdx++;
        });

        var stepIdx = <?php echo count(get_option('weblazem_devproject_process_steps', array())); ?>;
        $('#add-devproject-step').on('click', function() {
            $('#devproject-steps-container').append(
                '<p><input type="text" name="weblazem_devproject_process_steps[' + stepIdx + '][title]" class="large-text" placeholder="عنوان مرحله" />' +
                '<button type="button" class="button devproject-step-remove">حذف</button></p>'
            );
            stepIdx++;
        });
        $(document).on('click', '.devproject-step-remove', function() { $(this).parent().remove(); });

        var advIdx = <?php echo count(get_option('weblazem_devproject_advantages_items', array())); ?>;
        $('#add-devproject-advantage').on('click', function() {
            $('#devproject-advantages-container').append(
                '<div class="devproject-repeater-block" style="background:#f8f5fc;padding:16px;border-radius:12px;margin-bottom:16px;">' +
                '<button type="button" class="button devproject-remove-block" style="float:left;">حذف</button>' +
                '<p><input type="text" name="weblazem_devproject_advantages_items[' + advIdx + '][title]" class="large-text" placeholder="عنوان" /></p>' +
                '<p><textarea name="weblazem_devproject_advantages_items[' + advIdx + '][text]" class="large-text" rows="2" placeholder="توضیح"></textarea></p></div>'
            );
            advIdx++;
        });

        var faqIdx = <?php echo count(get_option('weblazem_devproject_faq_items', array())); ?>;
        $('#add-devproject-faq').on('click', function() {
            $('#devproject-faq-container').append(
                '<div class="devproject-repeater-block" style="background:#f8f5fc;padding:16px;border-radius:12px;margin-bottom:16px;">' +
                '<button type="button" class="button devproject-remove-block" style="float:left;">حذف</button>' +
                '<p><input type="text" name="weblazem_devproject_faq_items[' + faqIdx + '][question]" class="large-text" placeholder="سوال" /></p>' +
                '<p><textarea name="weblazem_devproject_faq_items[' + faqIdx + '][answer]" class="large-text" rows="2" placeholder="پاسخ"></textarea></p></div>'
            );
            faqIdx++;
        });

        var cardIdx = <?php echo count(get_option('weblazem_devproject_service_cards', array())); ?>;
        $('#add-devproject-service-card').on('click', function() {
            $('#devproject-service-cards').append(
                '<div class="devproject-repeater-block" style="background:#f8f5fc;padding:16px;border-radius:12px;margin-bottom:16px;">' +
                '<button type="button" class="button devproject-remove-block" style="float:left;">حذف</button>' +
                '<p><input type="text" name="weblazem_devproject_service_cards[' + cardIdx + '][title]" class="large-text" placeholder="عنوان" /></p>' +
                '<p><input type="text" name="weblazem_devproject_service_cards[' + cardIdx + '][en_title]" placeholder="EN" />' +
                '<input type="text" name="weblazem_devproject_service_cards[' + cardIdx + '][url]" placeholder="لینک" /></p></div>'
            );
            cardIdx++;
        });
    });
    </script>
    <?php
}
