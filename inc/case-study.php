<?php
/**
 * Case studies — portfolio meta, listing page, options, enqueue.
 */

define('WEBLAZEM_CASE_STUDY_SLUG', 'keis-astadi');
define('WEBLAZEM_CASE_STUDY_TEMPLATE', 'case-study-template.php');
define('WEBLAZEM_CASE_STUDY_OPTION', 'weblazem_case_study_page_id');

function weblazem_case_study_defaults() {
    return array(
        'title'            => 'کیس‌استادی پروژه‌ها',
        'subtitle'         => 'قبل و بعد واقعی پروژه‌های وب‌لازم با چالش، راه‌حل و نتایج قابل اندازه‌گیری',
        'empty_text'       => 'هنوز کیس‌استادی‌ای منتشر نشده است.',
        'card_button_text' => 'مشاهده جزئیات',
        'show_on_single'   => '1',
        'section_title'    => 'کیس‌استادی این پروژه',
        'before_label'     => 'قبل',
        'after_label'      => 'بعد',
        'challenge_title'  => 'چالش',
        'solution_title'   => 'راه‌حل',
        'result_title'     => 'نتیجه',
    );
}

function weblazem_get_case_study_settings() {
    $defaults = weblazem_case_study_defaults();
    $saved    = get_option('weblazem_case_study_settings', array());

    if (!is_array($saved)) {
        $saved = array();
    }

    $settings = wp_parse_args($saved, $defaults);
    $settings['show_on_single'] = ($settings['show_on_single'] === '1') ? '1' : '0';

    return $settings;
}

function weblazem_ensure_case_study_defaults() {
    if (get_option('weblazem_case_study_settings') === false) {
        update_option('weblazem_case_study_settings', weblazem_case_study_defaults());
    }
}
add_action('init', 'weblazem_ensure_case_study_defaults', 12);

function weblazem_get_case_study_page_id() {
    return weblazem_growth_get_page_id(WEBLAZEM_CASE_STUDY_OPTION, WEBLAZEM_CASE_STUDY_SLUG);
}

function weblazem_get_case_study_page_url() {
    return weblazem_growth_get_page_url(WEBLAZEM_CASE_STUDY_OPTION, WEBLAZEM_CASE_STUDY_SLUG);
}

function weblazem_is_case_study_page() {
    return weblazem_growth_is_page(
        WEBLAZEM_CASE_STUDY_TEMPLATE,
        WEBLAZEM_CASE_STUDY_OPTION,
        WEBLAZEM_CASE_STUDY_SLUG
    );
}

function weblazem_ensure_case_study_page() {
    weblazem_growth_ensure_page(
        array(
            'slug'     => WEBLAZEM_CASE_STUDY_SLUG,
            'template' => WEBLAZEM_CASE_STUDY_TEMPLATE,
            'title'    => 'کیس‌استادی',
            'option'   => WEBLAZEM_CASE_STUDY_OPTION,
        )
    );
}
add_action('init', 'weblazem_ensure_case_study_page', 38);

function weblazem_case_study_sanitize_settings($input) {
    $defaults = weblazem_case_study_defaults();
    $out      = $defaults;

    if (!is_array($input)) {
        return $out;
    }

    $out['title']            = sanitize_text_field($input['title'] ?? $defaults['title']);
    $out['subtitle']         = sanitize_textarea_field($input['subtitle'] ?? $defaults['subtitle']);
    $out['empty_text']       = sanitize_textarea_field($input['empty_text'] ?? $defaults['empty_text']);
    $out['card_button_text'] = sanitize_text_field($input['card_button_text'] ?? $defaults['card_button_text']);
    $out['show_on_single']   = (!empty($input['show_on_single']) && $input['show_on_single'] === '1') ? '1' : '0';
    $out['section_title']    = sanitize_text_field($input['section_title'] ?? $defaults['section_title']);
    $out['before_label']     = sanitize_text_field($input['before_label'] ?? $defaults['before_label']);
    $out['after_label']      = sanitize_text_field($input['after_label'] ?? $defaults['after_label']);
    $out['challenge_title']  = sanitize_text_field($input['challenge_title'] ?? $defaults['challenge_title']);
    $out['solution_title']   = sanitize_text_field($input['solution_title'] ?? $defaults['solution_title']);
    $out['result_title']     = sanitize_text_field($input['result_title'] ?? $defaults['result_title']);

    return $out;
}

function weblazem_register_case_study_settings() {
    register_setting(
        'weblazem_case_study_group',
        'weblazem_case_study_settings',
        array(
            'type'              => 'array',
            'sanitize_callback' => 'weblazem_case_study_sanitize_settings',
            'default'           => weblazem_case_study_defaults(),
        )
    );
}
add_action('admin_init', 'weblazem_register_case_study_settings');

function weblazem_case_study_admin_menu() {
    add_submenu_page(
        'weblazem-theme-options',
        'کیس‌استادی',
        'کیس‌استادی',
        'manage_options',
        'weblazem-case-study-options',
        'weblazem_case_study_options_display'
    );
}
add_action('admin_menu', 'weblazem_case_study_admin_menu', 29);

function weblazem_case_study_options_display() {
    if (!current_user_can('manage_options')) {
        return;
    }

    $s        = weblazem_get_case_study_settings();
    $page_url = weblazem_get_case_study_page_url();
    ?>
    <div class="wrap" dir="rtl">
        <h1>تنظیمات کیس‌استادی</h1>
        <?php if ($page_url) : ?>
            <p>صفحه لیست: <a href="<?php echo esc_url($page_url); ?>" target="_blank" rel="noopener"><?php echo esc_html($page_url); ?></a></p>
        <?php endif; ?>

        <form method="post" action="options.php">
            <?php settings_fields('weblazem_case_study_group'); ?>
            <input type="hidden" name="weblazem_case_study_settings[show_on_single]" value="0" />

            <table class="form-table">
                <tr>
                    <th>عنوان صفحه</th>
                    <td><input type="text" class="large-text" name="weblazem_case_study_settings[title]" value="<?php echo esc_attr($s['title']); ?>" /></td>
                </tr>
                <tr>
                    <th>زیرعنوان</th>
                    <td><textarea class="large-text" rows="3" name="weblazem_case_study_settings[subtitle]"><?php echo esc_textarea($s['subtitle']); ?></textarea></td>
                </tr>
                <tr>
                    <th>متن خالی بودن</th>
                    <td><textarea class="large-text" rows="2" name="weblazem_case_study_settings[empty_text]"><?php echo esc_textarea($s['empty_text']); ?></textarea></td>
                </tr>
                <tr>
                    <th>متن دکمه کارت</th>
                    <td><input type="text" class="regular-text" name="weblazem_case_study_settings[card_button_text]" value="<?php echo esc_attr($s['card_button_text']); ?>" /></td>
                </tr>
                <tr>
                    <th>نمایش در صفحه نمونه کار</th>
                    <td>
                        <label>
                            <input type="checkbox" name="weblazem_case_study_settings[show_on_single]" value="1" <?php checked($s['show_on_single'], '1'); ?> />
                            نمایش سکشن کیس‌استادی در صفحه جزئیات نمونه کار (در صورت فعال بودن برای همان پروژه)
                        </label>
                    </td>
                </tr>
                <tr>
                    <th>عنوان سکشن تک‌صفحه</th>
                    <td><input type="text" class="large-text" name="weblazem_case_study_settings[section_title]" value="<?php echo esc_attr($s['section_title']); ?>" /></td>
                </tr>
                <tr>
                    <th>برچسب قبل / بعد</th>
                    <td>
                        <input type="text" class="regular-text" name="weblazem_case_study_settings[before_label]" value="<?php echo esc_attr($s['before_label']); ?>" />
                        <input type="text" class="regular-text" name="weblazem_case_study_settings[after_label]" value="<?php echo esc_attr($s['after_label']); ?>" />
                    </td>
                </tr>
                <tr>
                    <th>عناوین چالش / راه‌حل / نتیجه</th>
                    <td>
                        <input type="text" class="regular-text" name="weblazem_case_study_settings[challenge_title]" value="<?php echo esc_attr($s['challenge_title']); ?>" placeholder="چالش" />
                        <input type="text" class="regular-text" name="weblazem_case_study_settings[solution_title]" value="<?php echo esc_attr($s['solution_title']); ?>" placeholder="راه‌حل" />
                        <input type="text" class="regular-text" name="weblazem_case_study_settings[result_title]" value="<?php echo esc_attr($s['result_title']); ?>" placeholder="نتیجه" />
                    </td>
                </tr>
            </table>

            <?php submit_button('ذخیره تنظیمات'); ?>
        </form>
    </div>
    <?php
}

function weblazem_case_study_meta_boxes() {
    add_meta_box(
        'weblazem_case_study_meta',
        'کیس‌استادی',
        'weblazem_case_study_meta_render',
        'portfolio',
        'normal',
        'default'
    );
}
add_action('add_meta_boxes', 'weblazem_case_study_meta_boxes');

function weblazem_case_study_admin_scripts($hook) {
    global $post_type;

    if (($hook === 'post.php' || $hook === 'post-new.php') && $post_type === 'portfolio') {
        wp_enqueue_media();

        // Reuse portfolio image uploader if already registered; otherwise add a minimal binder.
        if (!wp_script_is('weblazem-portfolio-single-admin', 'enqueued')) {
            wp_enqueue_script(
                'weblazem-portfolio-single-admin',
                get_template_directory_uri() . '/assets/js/portfolio-single-admin.js',
                array('jquery'),
                null,
                true
            );
        }
    }
}
add_action('admin_enqueue_scripts', 'weblazem_case_study_admin_scripts', 20);

function weblazem_case_study_meta_render($post) {
    wp_nonce_field('weblazem_case_study_meta_save', 'weblazem_case_study_meta_nonce');

    $enabled   = get_post_meta($post->ID, '_weblazem_case_enabled', true);
    $before    = get_post_meta($post->ID, '_weblazem_case_before_image', true);
    $after     = get_post_meta($post->ID, '_weblazem_case_after_image', true);
    $challenge = get_post_meta($post->ID, '_weblazem_case_challenge', true);
    $solution  = get_post_meta($post->ID, '_weblazem_case_solution', true);
    $result    = get_post_meta($post->ID, '_weblazem_case_result', true);
    ?>
    <table class="form-table">
        <tr>
            <th scope="row">فعال‌سازی کیس‌استادی</th>
            <td>
                <label>
                    <input type="checkbox" name="weblazem_case_enabled" value="1" <?php checked($enabled, '1'); ?> />
                    این نمونه کار را در صفحه کیس‌استادی نمایش بده
                </label>
            </td>
        </tr>
        <tr>
            <th scope="row">تصویر قبل</th>
            <td>
                <?php
                if (function_exists('weblazem_portfolio_single_image_field')) {
                    weblazem_portfolio_single_image_field('weblazem_case_before_image', $before, 'انتخاب تصویر قبل');
                } else {
                    echo '<input type="url" class="large-text" dir="ltr" name="weblazem_case_before_image" value="' . esc_attr($before) . '" />';
                }
                ?>
            </td>
        </tr>
        <tr>
            <th scope="row">تصویر بعد</th>
            <td>
                <?php
                if (function_exists('weblazem_portfolio_single_image_field')) {
                    weblazem_portfolio_single_image_field('weblazem_case_after_image', $after, 'انتخاب تصویر بعد');
                } else {
                    echo '<input type="url" class="large-text" dir="ltr" name="weblazem_case_after_image" value="' . esc_attr($after) . '" />';
                }
                ?>
            </td>
        </tr>
        <tr>
            <th scope="row">چالش</th>
            <td><textarea class="large-text" rows="4" name="weblazem_case_challenge"><?php echo esc_textarea($challenge); ?></textarea></td>
        </tr>
        <tr>
            <th scope="row">راه‌حل</th>
            <td><textarea class="large-text" rows="4" name="weblazem_case_solution"><?php echo esc_textarea($solution); ?></textarea></td>
        </tr>
        <tr>
            <th scope="row">نتیجه</th>
            <td><textarea class="large-text" rows="4" name="weblazem_case_result"><?php echo esc_textarea($result); ?></textarea></td>
        </tr>
        <?php for ($i = 1; $i <= 3; $i++) :
            $label = get_post_meta($post->ID, '_weblazem_case_metric_' . $i . '_label', true);
            $value = get_post_meta($post->ID, '_weblazem_case_metric_' . $i . '_value', true);
            ?>
            <tr>
                <th scope="row">متریک <?php echo (int) $i; ?></th>
                <td>
                    <input type="text" class="regular-text" name="weblazem_case_metric_<?php echo (int) $i; ?>_label" value="<?php echo esc_attr($label); ?>" placeholder="برچسب (مثلاً افزایش ترافیک)" />
                    <input type="text" class="regular-text" name="weblazem_case_metric_<?php echo (int) $i; ?>_value" value="<?php echo esc_attr($value); ?>" placeholder="مقدار (مثلاً +۱۴۰٪)" />
                </td>
            </tr>
        <?php endfor; ?>
    </table>
    <?php
}

function weblazem_case_study_meta_save($post_id) {
    if (!isset($_POST['weblazem_case_study_meta_nonce']) ||
        !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['weblazem_case_study_meta_nonce'])), 'weblazem_case_study_meta_save')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id) || get_post_type($post_id) !== 'portfolio') {
        return;
    }

    $enabled = (!empty($_POST['weblazem_case_enabled']) && $_POST['weblazem_case_enabled'] === '1') ? '1' : '0';
    update_post_meta($post_id, '_weblazem_case_enabled', $enabled);

    $before = isset($_POST['weblazem_case_before_image']) ? esc_url_raw(wp_unslash($_POST['weblazem_case_before_image'])) : '';
    $after  = isset($_POST['weblazem_case_after_image']) ? esc_url_raw(wp_unslash($_POST['weblazem_case_after_image'])) : '';
    update_post_meta($post_id, '_weblazem_case_before_image', $before);
    update_post_meta($post_id, '_weblazem_case_after_image', $after);

    $text_fields = array(
        '_weblazem_case_challenge' => 'weblazem_case_challenge',
        '_weblazem_case_solution'  => 'weblazem_case_solution',
        '_weblazem_case_result'    => 'weblazem_case_result',
    );

    foreach ($text_fields as $meta_key => $post_key) {
        $value = isset($_POST[$post_key]) ? wp_kses_post(wp_unslash($_POST[$post_key])) : '';
        update_post_meta($post_id, $meta_key, $value);
    }

    for ($i = 1; $i <= 3; $i++) {
        $label_key = 'weblazem_case_metric_' . $i . '_label';
        $value_key = 'weblazem_case_metric_' . $i . '_value';
        update_post_meta(
            $post_id,
            '_weblazem_case_metric_' . $i . '_label',
            isset($_POST[$label_key]) ? sanitize_text_field(wp_unslash($_POST[$label_key])) : ''
        );
        update_post_meta(
            $post_id,
            '_weblazem_case_metric_' . $i . '_value',
            isset($_POST[$value_key]) ? sanitize_text_field(wp_unslash($_POST[$value_key])) : ''
        );
    }
}
add_action('save_post_portfolio', 'weblazem_case_study_meta_save');

function weblazem_is_portfolio_case_enabled($post_id = null) {
    $post_id = $post_id ? $post_id : get_the_ID();
    return get_post_meta($post_id, '_weblazem_case_enabled', true) === '1';
}

function weblazem_get_portfolio_case_data($post_id = null) {
    $post_id = $post_id ? $post_id : get_the_ID();

    $metrics = array();
    for ($i = 1; $i <= 3; $i++) {
        $label = get_post_meta($post_id, '_weblazem_case_metric_' . $i . '_label', true);
        $value = get_post_meta($post_id, '_weblazem_case_metric_' . $i . '_value', true);
        if ($label !== '' || $value !== '') {
            $metrics[] = array(
                'label' => $label,
                'value' => $value,
            );
        }
    }

    return array(
        'enabled'   => weblazem_is_portfolio_case_enabled($post_id),
        'before'    => get_post_meta($post_id, '_weblazem_case_before_image', true),
        'after'     => get_post_meta($post_id, '_weblazem_case_after_image', true),
        'challenge' => get_post_meta($post_id, '_weblazem_case_challenge', true),
        'solution'  => get_post_meta($post_id, '_weblazem_case_solution', true),
        'result'    => get_post_meta($post_id, '_weblazem_case_result', true),
        'metrics'   => $metrics,
    );
}

function weblazem_query_case_study_portfolios($args = array()) {
    $defaults = array(
        'post_type'      => 'portfolio',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'orderby'        => 'date',
        'order'          => 'DESC',
        'meta_query'     => array(
            array(
                'key'   => '_weblazem_case_enabled',
                'value' => '1',
            ),
        ),
    );

    return new WP_Query(wp_parse_args($args, $defaults));
}

function weblazem_should_show_case_on_single($post_id = null) {
    $settings = weblazem_get_case_study_settings();
    if ($settings['show_on_single'] !== '1') {
        return false;
    }

    return weblazem_is_portfolio_case_enabled($post_id);
}

function weblazem_enqueue_case_study_assets() {
    $on_listing = weblazem_is_case_study_page();
    $on_single  = is_singular('portfolio') && weblazem_should_show_case_on_single(get_queried_object_id());

    if (!$on_listing && !$on_single) {
        return;
    }

    wp_enqueue_style(
        'weblazem-case-study',
        get_template_directory_uri() . '/assets/css/case-study.css',
        array(),
        '1.0.0'
    );
}
add_action('wp_enqueue_scripts', 'weblazem_enqueue_case_study_assets', 30);
