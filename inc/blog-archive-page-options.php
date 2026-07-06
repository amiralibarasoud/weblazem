<?php
/**
 * Blog archive page — admin settings.
 */

require_once get_template_directory() . '/inc/blog-archive-defaults.php';

function weblazem_blogarchive_options_menu() {
    add_submenu_page(
        'weblazem-theme-options',
        'تنظیمات آرشیو بلاگ',
        '  آرشیو بلاگ',
        'manage_options',
        'weblazem-blogarchive-options',
        'weblazem_blogarchive_options_display'
    );
}
add_action('admin_menu', 'weblazem_blogarchive_options_menu', 23);

function weblazem_register_blogarchive_settings() {
    foreach (array_keys(weblazem_blogarchive_defaults()) as $key) {
        register_setting('weblazem_blogarchive_group', 'weblazem_blogarchive_' . $key);
    }
    foreach (weblazem_get_blogarchive_sections_config() as $key => $label) {
        register_setting('weblazem_blogarchive_group', 'weblazem_blogarchive_section_' . $key . '_enabled');
    }
}
add_action('admin_init', 'weblazem_register_blogarchive_settings');

function weblazem_blogarchive_handle_section_checkboxes() {
    if (!isset($_POST['option_page']) || $_POST['option_page'] !== 'weblazem_blogarchive_group') {
        return;
    }
    foreach (weblazem_get_blogarchive_sections_config() as $key => $label) {
        $option_key = 'weblazem_blogarchive_section_' . $key . '_enabled';
        if (!isset($_POST[$option_key])) {
            update_option($option_key, '0');
        }
    }
}
add_action('admin_init', 'weblazem_blogarchive_handle_section_checkboxes', 20);

function weblazem_blogarchive_admin_scripts($hook) {
    if (strpos($hook, 'weblazem-blogarchive-options') === false) {
        return;
    }
    wp_enqueue_media();
}
add_action('admin_enqueue_scripts', 'weblazem_blogarchive_admin_scripts');

function weblazem_blogarchive_opt($key) {
    $defaults = weblazem_blogarchive_defaults();
    return get_option('weblazem_blogarchive_' . $key, $defaults[$key] ?? '');
}

function weblazem_blogarchive_options_display() {
    $page_url = weblazem_get_blogarchive_page_url();
    ?>
    <div class="wrap" dir="rtl">
        <div class="weblazem-admin-header">
            <div class="weblazem-admin-header-content">
                <h1>تنظیمات آرشیو بلاگ</h1>
                <p>
                    محتوای صفحه «مجله وب‌لازم» را مدیریت کنید.
                    <a href="<?php echo esc_url($page_url); ?>" target="_blank" rel="noopener">مشاهده صفحه</a>
                </p>
            </div>
        </div>

        <div class="weblazem-admin-content" style="margin-top:24px;">
            <form method="post" action="options.php">
                <?php settings_fields('weblazem_blogarchive_group'); ?>

                <div class="weblazem-admin-card" style="margin-bottom:20px;">
                    <h3>فعال‌سازی سکشن‌ها</h3>
                    <table class="form-table">
                        <?php foreach (weblazem_get_blogarchive_sections_config() as $key => $label) :
                            $option_key = 'weblazem_blogarchive_section_' . $key . '_enabled';
                            ?>
                            <tr>
                                <th><?php echo esc_html($label); ?></th>
                                <td>
                                    <input type="hidden" name="<?php echo esc_attr($option_key); ?>" value="0" />
                                    <label>
                                        <input type="checkbox" name="<?php echo esc_attr($option_key); ?>" value="1" <?php checked(get_option($option_key, '1'), '1'); ?> />
                                        نمایش در صفحه
                                    </label>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                </div>

                <div class="weblazem-admin-card" style="margin-bottom:20px;">
                    <h3>بخش هیرو</h3>
                    <?php weblazem_blogarchive_admin_image('hero_calligraphy_image', 'تصویر خوشنویسی (جهش)'); ?>
                    <?php weblazem_blogarchive_admin_textarea('hero_calligraphy_text', 'متن خوشنویسی (HTML)', 'در صورت نبود تصویر'); ?>
                    <?php weblazem_blogarchive_admin_field('hero_en_subtitle', 'زیرعنوان انگلیسی'); ?>
                    <?php weblazem_blogarchive_admin_textarea('hero_intro', 'متن معرفی'); ?>
                    <?php weblazem_blogarchive_admin_checkbox('hero_banner_enabled', 'نمایش بنر بالایی'); ?>
                    <?php weblazem_blogarchive_admin_checkbox('hero_search_enabled', 'نمایش جستجو در بنر'); ?>
                    <?php weblazem_blogarchive_admin_field('hero_banner_text', 'متن placeholder جستجو'); ?>
                </div>

                <div class="weblazem-admin-card">
                    <h3>لیست مقالات</h3>
                    <?php weblazem_blogarchive_admin_field('posts_per_page', 'تعداد مقاله در هر صفحه'); ?>
                    <?php weblazem_blogarchive_admin_field('posts_empty_message', 'پیام خالی بودن لیست'); ?>
                    <?php weblazem_blogarchive_admin_field('pagination_last_label', 'متن دکمه صفحه آخر'); ?>
                    <p class="description">مقالات از بخش «نوشته‌ها» وردپرس خوانده می‌شوند.</p>
                </div>

                <?php submit_button('ذخیره تنظیمات'); ?>
            </form>
        </div>
    </div>
    <?php weblazem_blogarchive_admin_scripts_inline(); ?>
    <?php
}

function weblazem_blogarchive_admin_field($key, $label) {
    $val = weblazem_blogarchive_opt($key);
    echo '<p><label><strong>' . esc_html($label) . '</strong><br>';
    echo '<input type="text" name="weblazem_blogarchive_' . esc_attr($key) . '" class="large-text" value="' . esc_attr($val) . '" /></label></p>';
}

function weblazem_blogarchive_admin_textarea($key, $label, $desc = '') {
    $val = weblazem_blogarchive_opt($key);
    echo '<p><label><strong>' . esc_html($label) . '</strong>';
    if ($desc) {
        echo ' <span class="description">' . esc_html($desc) . '</span>';
    }
    echo '<br><textarea name="weblazem_blogarchive_' . esc_attr($key) . '" class="large-text" rows="3">' . esc_textarea($val) . '</textarea></label></p>';
}

function weblazem_blogarchive_admin_checkbox($key, $label) {
    $val = weblazem_blogarchive_opt($key);
    echo '<p><label><input type="hidden" name="weblazem_blogarchive_' . esc_attr($key) . '" value="0" />';
    echo '<input type="checkbox" name="weblazem_blogarchive_' . esc_attr($key) . '" value="1" ' . checked($val, '1', false) . ' /> ';
    echo esc_html($label) . '</label></p>';
}

function weblazem_blogarchive_admin_image($key, $label) {
    $val = weblazem_blogarchive_opt($key);
    $id  = 'blogarchive_img_' . $key;
    echo '<p><strong>' . esc_html($label) . '</strong><br>';
    echo '<input type="hidden" id="' . esc_attr($id) . '" name="weblazem_blogarchive_' . esc_attr($key) . '" value="' . esc_attr($val) . '" />';
    echo '<div class="blogarchive-img-preview" data-for="' . esc_attr($id) . '" style="margin:8px 0;">';
    if ($val) {
        echo '<img src="' . esc_url($val) . '" style="max-width:200px;border-radius:8px;" alt="" />';
    }
    echo '</div>';
    echo '<button type="button" class="button blogarchive-upload-img" data-target="' . esc_attr($id) . '">انتخاب تصویر</button> ';
    echo '<button type="button" class="button blogarchive-remove-img" data-target="' . esc_attr($id) . '">حذف</button></p>';
}

function weblazem_blogarchive_admin_scripts_inline() {
    ?>
    <script>
    jQuery(function($) {
        $(document).on('click', '.blogarchive-upload-img', function(e) {
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
        $(document).on('click', '.blogarchive-remove-img', function(e) {
            e.preventDefault();
            var target = $(this).data('target');
            $('#' + target).val('');
            $('[data-for="' + target + '"]').empty();
        });
    });
    </script>
    <?php
}
