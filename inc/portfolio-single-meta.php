<?php
/**
 * Single portfolio page — per-project fields and flexible sections.
 */

function weblazem_portfolio_single_meta_boxes() {
    add_meta_box(
        'weblazem_portfolio_single_hero',
        'صفحه نمونه کار — هیرو و معرفی',
        'weblazem_portfolio_single_hero_meta_render',
        'portfolio',
        'normal',
        'high'
    );

    add_meta_box(
        'weblazem_portfolio_single_sections',
        'صفحه نمونه کار — سکشن‌های دو ستونه',
        'weblazem_portfolio_single_sections_meta_render',
        'portfolio',
        'normal',
        'default'
    );
}
add_action('add_meta_boxes', 'weblazem_portfolio_single_meta_boxes');

function weblazem_portfolio_single_admin_scripts($hook) {
    global $post_type;

    if (($hook === 'post.php' || $hook === 'post-new.php') && $post_type === 'portfolio') {
        wp_enqueue_media();
        wp_enqueue_script(
            'weblazem-portfolio-single-admin',
            get_template_directory_uri() . '/assets/js/portfolio-single-admin.js',
            array('jquery'),
            null,
            true
        );
    }
}
add_action('admin_enqueue_scripts', 'weblazem_portfolio_single_admin_scripts');

function weblazem_portfolio_single_hero_meta_render($post) {
    wp_nonce_field('weblazem_portfolio_single_meta_save', 'weblazem_portfolio_single_meta_nonce');

    $client_logo   = get_post_meta($post->ID, '_weblazem_portfolio_client_logo', true);
    $hero_image    = get_post_meta($post->ID, '_weblazem_portfolio_hero_image', true);
    $intro_text    = get_post_meta($post->ID, '_weblazem_portfolio_intro_text', true);
    $mobile_image  = get_post_meta($post->ID, '_weblazem_portfolio_mobile_image', true);
    $display_title = get_post_meta($post->ID, '_weblazem_portfolio_display_title', true);
    ?>
    <table class="form-table">
        <tr>
            <th scope="row"><label for="weblazem_portfolio_display_title">عنوان نمایشی صفحه</label></th>
            <td>
                <input type="text" id="weblazem_portfolio_display_title" name="weblazem_portfolio_display_title"
                       class="large-text" value="<?php echo esc_attr($display_title); ?>"
                       placeholder="<?php echo esc_attr(get_the_title($post)); ?>" />
                <p class="description">در صورت خالی بودن از عنوان پست استفاده می‌شود.</p>
            </td>
        </tr>
        <tr>
            <th scope="row">لوگوی شرکت / کارفرما</th>
            <td>
                <?php weblazem_portfolio_single_image_field('weblazem_portfolio_client_logo', $client_logo, 'انتخاب لوگو'); ?>
            </td>
        </tr>
        <tr>
            <th scope="row">متن معرفی پروژه</th>
            <td>
                <textarea id="weblazem_portfolio_intro_text" name="weblazem_portfolio_intro_text"
                          class="large-text" rows="4"><?php echo esc_textarea($intro_text); ?></textarea>
                <p class="description">در صورت خالی بودن از چکیده (Excerpt) استفاده می‌شود.</p>
            </td>
        </tr>
        <tr>
            <th scope="row">تصویر اصلی (دسکتاپ)</th>
            <td>
                <?php weblazem_portfolio_single_image_field('weblazem_portfolio_hero_image', $hero_image, 'انتخاب تصویر دسکتاپ'); ?>
                <p class="description">اسکرین‌شات صفحه اصلی سایت. در صورت خالی بودن از تصویر شاخص استفاده می‌شود.</p>
            </td>
        </tr>
        <tr>
            <th scope="row">تصویر نمایش موبایل (هیرو)</th>
            <td>
                <?php weblazem_portfolio_single_image_field('weblazem_portfolio_mobile_image', $mobile_image, 'انتخاب تصویر موبایل'); ?>
                <p class="description">اسکرین‌شات نسخه موبایل پروژه. در کارت‌های نمونه‌کار کنار مانیتور و در هیرو صفحه جزئیات نمایش داده می‌شود. اگر خالی باشد، همان تصویر دسکتاپ با برش موبایلی استفاده می‌شود.</p>
            </td>
        </tr>
    </table>
    <?php
}

function weblazem_portfolio_single_sections_meta_render($post) {
    $sections = weblazem_get_portfolio_single_sections($post->ID);
    ?>
    <p class="description" style="margin-bottom:16px;">
        سکشن‌های دو ستونه با عنوان، متن و تصویر. می‌توانید تعداد نامحدود اضافه یا حذف کنید.
        برای نمایش موبایل، نوع نمایش را «موبایل» انتخاب کنید.
    </p>

    <div id="portfolio-single-sections-container">
        <?php foreach ($sections as $index => $section) : ?>
            <?php weblazem_portfolio_single_section_admin_row($index, $section); ?>
        <?php endforeach; ?>
    </div>

    <p>
        <button type="button" class="button button-primary" id="add-portfolio-single-section">افزودن سکشن</button>
    </p>

    <script type="text/template" id="portfolio-single-section-template">
        <?php ob_start(); weblazem_portfolio_single_section_admin_row('{{index}}', weblazem_get_default_portfolio_single_section()); echo ob_get_clean(); ?>
    </script>
    <?php
}

function weblazem_portfolio_single_section_admin_row($index, $section) {
    $section = wp_parse_args($section, weblazem_get_default_portfolio_single_section());
    ?>
    <div class="weblazem-portfolio-section-admin" style="background:#f8f5fc;padding:16px;border-radius:12px;margin-bottom:16px;border:1px solid #e8dff0;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">
            <strong>سکشن <?php echo is_numeric($index) ? ((int) $index + 1) : 'جدید'; ?></strong>
            <button type="button" class="button portfolio-single-section-remove">حذف سکشن</button>
        </div>
        <table class="form-table">
            <tr>
                <th scope="row">عنوان</th>
                <td>
                    <input type="text" class="large-text"
                           name="weblazem_portfolio_sections[<?php echo esc_attr($index); ?>][title]"
                           value="<?php echo esc_attr($section['title']); ?>" />
                </td>
            </tr>
            <tr>
                <th scope="row">متن</th>
                <td>
                    <textarea class="large-text" rows="4"
                              name="weblazem_portfolio_sections[<?php echo esc_attr($index); ?>][text]"><?php echo esc_textarea($section['text']); ?></textarea>
                </td>
            </tr>
            <tr>
                <th scope="row">تصویر</th>
                <td>
                    <?php weblazem_portfolio_single_image_field(
                        'weblazem_portfolio_sections[' . esc_attr($index) . '][image]',
                        $section['image'],
                        'انتخاب تصویر سکشن'
                    ); ?>
                </td>
            </tr>
            <tr>
                <th scope="row">چیدمان تصویر</th>
                <td>
                    <select name="weblazem_portfolio_sections[<?php echo esc_attr($index); ?>][layout]">
                        <option value="image-start" <?php selected($section['layout'], 'image-start'); ?>>تصویر راست — متن چپ</option>
                        <option value="image-end" <?php selected($section['layout'], 'image-end'); ?>>متن راست — تصویر چپ</option>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row">نوع نمایش</th>
                <td>
                    <select name="weblazem_portfolio_sections[<?php echo esc_attr($index); ?>][display]">
                        <option value="desktop" <?php selected($section['display'], 'desktop'); ?>>دسکتاپ / تبلت</option>
                        <option value="mobile" <?php selected($section['display'], 'mobile'); ?>>موبایل (قاب گوشی)</option>
                    </select>
                </td>
            </tr>
        </table>
    </div>
    <?php
}

function weblazem_portfolio_single_image_field($name, $value, $button_label) {
    $field_id = 'img_' . md5($name . wp_rand());
    ?>
    <input type="hidden" class="weblazem-portfolio-image-input" id="<?php echo esc_attr($field_id); ?>"
           name="<?php echo esc_attr($name); ?>" value="<?php echo esc_url($value); ?>" />
    <div class="weblazem-portfolio-image-preview" style="margin-bottom:10px;">
        <?php if (!empty($value)) : ?>
            <img src="<?php echo esc_url($value); ?>" style="max-width:220px;border-radius:10px;" alt="" />
        <?php endif; ?>
    </div>
    <button type="button" class="button weblazem-portfolio-upload-image" data-target="#<?php echo esc_attr($field_id); ?>">
        <?php echo esc_html($button_label); ?>
    </button>
    <button type="button" class="button weblazem-portfolio-remove-image" data-target="#<?php echo esc_attr($field_id); ?>">حذف</button>
    <?php
}

function weblazem_get_default_portfolio_single_section() {
    return array(
        'title'   => '',
        'text'    => '',
        'image'   => '',
        'layout'  => 'image-start',
        'display' => 'desktop',
    );
}

function weblazem_sanitize_portfolio_single_sections($sections) {
    if (!is_array($sections)) {
        return array();
    }

    $sanitized = array();

    foreach ($sections as $section) {
        if (empty($section['title']) && empty($section['text']) && empty($section['image'])) {
            continue;
        }

        $layout  = isset($section['layout']) ? $section['layout'] : 'image-start';
        $display = isset($section['display']) ? $section['display'] : 'desktop';

        $sanitized[] = array(
            'title'   => isset($section['title']) ? sanitize_text_field($section['title']) : '',
            'text'    => isset($section['text']) ? wp_kses_post($section['text']) : '',
            'image'   => isset($section['image']) ? esc_url_raw($section['image']) : '',
            'layout'  => in_array($layout, array('image-start', 'image-end'), true) ? $layout : 'image-start',
            'display' => in_array($display, array('desktop', 'mobile'), true) ? $display : 'desktop',
        );
    }

    return $sanitized;
}

function weblazem_get_portfolio_single_sections($post_id = null) {
    $post_id  = $post_id ? $post_id : get_the_ID();
    $sections = get_post_meta($post_id, '_weblazem_portfolio_sections', true);

    return is_array($sections) ? $sections : array();
}

function weblazem_get_portfolio_single_display_title($post_id = null) {
    $post_id = $post_id ? $post_id : get_the_ID();
    $title   = get_post_meta($post_id, '_weblazem_portfolio_display_title', true);

    if (!empty($title)) {
        return $title;
    }

    return get_the_title($post_id);
}

function weblazem_get_portfolio_single_intro($post_id = null) {
    $post_id = $post_id ? $post_id : get_the_ID();
    $intro   = get_post_meta($post_id, '_weblazem_portfolio_intro_text', true);

    if (!empty($intro)) {
        return $intro;
    }

    $excerpt = get_post_field('post_excerpt', $post_id);

    if (!empty($excerpt)) {
        return $excerpt;
    }

    return '';
}

function weblazem_get_portfolio_single_hero_image($post_id = null) {
    $post_id = $post_id ? $post_id : get_the_ID();
    $image   = get_post_meta($post_id, '_weblazem_portfolio_hero_image', true);

    if (!empty($image)) {
        return $image;
    }

    if (has_post_thumbnail($post_id)) {
        return get_the_post_thumbnail_url($post_id, 'full');
    }

    return '';
}

function weblazem_portfolio_single_meta_save($post_id) {
    if (!isset($_POST['weblazem_portfolio_single_meta_nonce']) ||
        !wp_verify_nonce($_POST['weblazem_portfolio_single_meta_nonce'], 'weblazem_portfolio_single_meta_save')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id) || get_post_type($post_id) !== 'portfolio') {
        return;
    }

    $text_fields = array(
        '_weblazem_portfolio_display_title' => 'weblazem_portfolio_display_title',
        '_weblazem_portfolio_intro_text'    => 'weblazem_portfolio_intro_text',
    );

    foreach ($text_fields as $meta_key => $post_key) {
        if (isset($_POST[$post_key])) {
            update_post_meta($post_id, $meta_key, sanitize_text_field(wp_unslash($_POST[$post_key])));
        }
    }

    $url_fields = array(
        '_weblazem_portfolio_client_logo'  => 'weblazem_portfolio_client_logo',
        '_weblazem_portfolio_hero_image'   => 'weblazem_portfolio_hero_image',
        '_weblazem_portfolio_mobile_image' => 'weblazem_portfolio_mobile_image',
    );

    foreach ($url_fields as $meta_key => $post_key) {
        if (isset($_POST[$post_key])) {
            update_post_meta($post_id, $meta_key, esc_url_raw(wp_unslash($_POST[$post_key])));
        }
    }

    if (isset($_POST['weblazem_portfolio_sections']) && is_array($_POST['weblazem_portfolio_sections'])) {
        update_post_meta(
            $post_id,
            '_weblazem_portfolio_sections',
            weblazem_sanitize_portfolio_single_sections(wp_unslash($_POST['weblazem_portfolio_sections']))
        );
    } else {
        update_post_meta($post_id, '_weblazem_portfolio_sections', array());
    }
}
add_action('save_post_portfolio', 'weblazem_portfolio_single_meta_save');
