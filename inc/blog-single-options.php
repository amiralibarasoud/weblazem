<?php
/**
 * Blog single — admin settings.
 */

require_once get_template_directory() . '/inc/blog-single-defaults.php';

function weblazem_blog_single_options_menu() {
    add_submenu_page(
        'weblazem-theme-options',
        'تنظیمات مقاله بلاگ',
        '  مقاله بلاگ',
        'manage_options',
        'weblazem-blog-single-options',
        'weblazem_blog_single_options_display'
    );
}
add_action('admin_menu', 'weblazem_blog_single_options_menu', 24);

function weblazem_register_blog_single_settings() {
    foreach (array_keys(weblazem_blog_single_defaults()) as $key) {
        register_setting('weblazem_blog_single_group', 'weblazem_blog_single_' . $key);
    }
    foreach (weblazem_get_blog_single_sections_config() as $key => $label) {
        register_setting('weblazem_blog_single_group', 'weblazem_blog_single_section_' . $key . '_enabled');
    }
}
add_action('admin_init', 'weblazem_register_blog_single_settings');

function weblazem_blog_single_handle_section_checkboxes() {
    if (!isset($_POST['option_page']) || $_POST['option_page'] !== 'weblazem_blog_single_group') {
        return;
    }
    foreach (weblazem_get_blog_single_sections_config() as $key => $label) {
        $option_key = 'weblazem_blog_single_section_' . $key . '_enabled';
        if (!isset($_POST[$option_key])) {
            update_option($option_key, '0');
        }
    }
}
add_action('admin_init', 'weblazem_blog_single_handle_section_checkboxes', 20);

function weblazem_blog_single_admin_scripts($hook) {
    if (strpos($hook, 'weblazem-blog-single-options') === false) {
        return;
    }
    wp_enqueue_media();
}
add_action('admin_enqueue_scripts', 'weblazem_blog_single_admin_scripts');

function weblazem_blog_single_opt($key) {
    $defaults = weblazem_blog_single_defaults();
    return get_option('weblazem_blog_single_' . $key, $defaults[$key] ?? '');
}

function weblazem_blog_single_options_display() {
    ?>
    <div class="wrap" dir="rtl">
        <div class="weblazem-admin-header">
            <div class="weblazem-admin-header-content">
                <h1>تنظیمات صفحه تک‌مقاله بلاگ</h1>
                <p>نمایش سکشن‌ها و محتوای سایدبار مقالات را مدیریت کنید.</p>
            </div>
        </div>

        <div class="weblazem-admin-content" style="margin-top:24px;">
            <form method="post" action="options.php">
                <?php settings_fields('weblazem_blog_single_group'); ?>

                <div class="weblazem-admin-card" style="margin-bottom:20px;">
                    <h3>فعال‌سازی سکشن‌ها</h3>
                    <table class="form-table">
                        <?php foreach (weblazem_get_blog_single_sections_config() as $key => $label) :
                            $option_key = 'weblazem_blog_single_section_' . $key . '_enabled';
                            ?>
                            <tr>
                                <th><?php echo esc_html($label); ?></th>
                                <td>
                                    <input type="hidden" name="<?php echo esc_attr($option_key); ?>" value="0" />
                                    <label>
                                        <input type="checkbox" name="<?php echo esc_attr($option_key); ?>" value="1" <?php checked(get_option($option_key, '1'), '1'); ?> />
                                        نمایش در صفحه مقاله
                                    </label>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                </div>

                <div class="weblazem-admin-card" style="margin-bottom:20px;">
                    <h3>بنر بالای صفحه</h3>
                    <?php weblazem_blog_single_admin_field('banner_line1', 'خط اول'); ?>
                    <?php weblazem_blog_single_admin_field('banner_line2', 'خط دوم'); ?>
                    <?php weblazem_blog_single_admin_image('banner_image', 'تصویر/ایلاستریشن بنر'); ?>
                </div>

                <div class="weblazem-admin-card" style="margin-bottom:20px;">
                    <h3>سایدبار</h3>
                    <?php weblazem_blog_single_admin_checkbox('sidebar_categories_enabled', 'نمایش لیست موضوعات (دسته‌بندی‌ها)'); ?>
                    <?php weblazem_blog_single_admin_field('sidebar_categories_title', 'عنوان بخش موضوعات'); ?>
                    <?php weblazem_blog_single_admin_field('sidebar_categories_count', 'حداکثر تعداد دسته (۰ = همه)'); ?>
                    <?php weblazem_blog_single_admin_checkbox('sidebar_latest_enabled', 'نمایش آخرین مقالات'); ?>
                    <?php weblazem_blog_single_admin_field('sidebar_latest_title', 'عنوان آخرین مقالات'); ?>
                    <?php weblazem_blog_single_admin_field('sidebar_latest_count', 'تعداد آخرین مقالات'); ?>
                </div>

                <div class="weblazem-admin-card" style="margin-bottom:20px;">
                    <h3>مقالات مرتبط</h3>
                    <?php weblazem_blog_single_admin_field('related_count', 'تعداد مقالات مرتبط (ستون‌ها)'); ?>
                    <p class="description">مقالات هم‌دسته با مقاله فعلی نمایش داده می‌شوند.</p>
                </div>

                <div class="weblazem-admin-card">
                    <h3>بخش نظرات</h3>
                    <?php weblazem_blog_single_admin_field('comments_title', 'عنوان بخش'); ?>
                    <?php weblazem_blog_single_admin_field('comments_submit_text', 'متن دکمه ارسال'); ?>
                    <?php weblazem_blog_single_admin_image('comments_image', 'تصویر تزئینی (پاکت نامه)'); ?>
                </div>

                <?php submit_button('ذخیره تنظیمات'); ?>
            </form>
        </div>
    </div>
    <?php weblazem_blog_single_admin_scripts_inline(); ?>
    <?php
}

function weblazem_blog_single_admin_field($key, $label) {
    $val = weblazem_blog_single_opt($key);
    echo '<p><label><strong>' . esc_html($label) . '</strong><br>';
    echo '<input type="text" name="weblazem_blog_single_' . esc_attr($key) . '" class="large-text" value="' . esc_attr($val) . '" /></label></p>';
}

function weblazem_blog_single_admin_checkbox($key, $label) {
    $val = weblazem_blog_single_opt($key);
    echo '<p><label><input type="hidden" name="weblazem_blog_single_' . esc_attr($key) . '" value="0" />';
    echo '<input type="checkbox" name="weblazem_blog_single_' . esc_attr($key) . '" value="1" ' . checked($val, '1', false) . ' /> ';
    echo esc_html($label) . '</label></p>';
}

function weblazem_blog_single_admin_image($key, $label) {
    $val = weblazem_blog_single_opt($key);
    $id  = 'blog_single_img_' . $key;
    echo '<p><strong>' . esc_html($label) . '</strong><br>';
    echo '<input type="hidden" id="' . esc_attr($id) . '" name="weblazem_blog_single_' . esc_attr($key) . '" value="' . esc_attr($val) . '" />';
    echo '<div class="blog-single-img-preview" data-for="' . esc_attr($id) . '" style="margin:8px 0;">';
    if ($val) {
        echo '<img src="' . esc_url($val) . '" style="max-width:200px;border-radius:8px;" alt="" />';
    }
    echo '</div>';
    echo '<button type="button" class="button blog-single-upload-img" data-target="' . esc_attr($id) . '">انتخاب تصویر</button> ';
    echo '<button type="button" class="button blog-single-remove-img" data-target="' . esc_attr($id) . '">حذف</button></p>';
}

function weblazem_blog_single_admin_scripts_inline() {
    ?>
    <script>
    jQuery(function($) {
        $(document).on('click', '.blog-single-upload-img', function(e) {
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
        $(document).on('click', '.blog-single-remove-img', function(e) {
            e.preventDefault();
            var target = $(this).data('target');
            $('#' + target).val('');
            $('[data-for="' + target + '"]').empty();
        });
    });
    </script>
    <?php
}
