<?php
/**
 * Portfolio archive page — admin settings under internal pages.
 */

function weblazem_portfolio_page_defaults() {
    return array(
        'weblazem_portfolio_page_hero_title'       => 'جدیدترین نمونه‌کارهای وب‌لازم',
        'weblazem_portfolio_page_hero_description' => 'لورم ایپسوم متن ساختگی با تولید سادگی نامفهوم از صنعت چاپ و با استفاده از طراحان گرافیک است. چاپگرها و متون بلکه روزنامه و مجله در ستون و سطرآنچنان که لازم است.',
        'weblazem_portfolio_page_hero_image'        => '',
        'weblazem_portfolio_page_latest_enabled'    => '1',
        'weblazem_portfolio_page_latest_title'      => 'آخرین پروژه‌های اجرا شده در وب‌لازم',
        'weblazem_portfolio_page_latest_count'      => '4',
        'weblazem_portfolio_page_card_button_text'  => 'مشاهده پروژه',
        'weblazem_portfolio_page_tariffs_enabled'   => '1',
        'weblazem_portfolio_page_tariffs_title'     => 'تعرفه‌ها',
        'weblazem_portfolio_page_tariffs_description' => 'لورم ایپسوم متن ساختگی با تولید سادگی نامفهوم از صنعت چاپ و با استفاده از طراحان گرافیک است.',
        'weblazem_portfolio_page_tariffs_cta_text'  => 'اگه هنوز نمیدونی چه سایتی نیاز داری، برای کسب و کارت چه چیزی مناسب تره حتما از ما مشاوره بگیر',
        'weblazem_portfolio_page_tariffs_btn_text'  => 'مشاوره رایگان',
        'weblazem_portfolio_page_tariffs_btn_url'   => '#',
        'weblazem_portfolio_page_all_title'         => 'تمام پروژه‌ها',
        'weblazem_portfolio_page_all_per_page'      => '8',
    );
}

function weblazem_ensure_portfolio_page_defaults() {
    foreach (weblazem_portfolio_page_defaults() as $key => $value) {
        if (get_option($key) === false) {
            update_option($key, $value);
        }
    }
}
add_action('init', 'weblazem_ensure_portfolio_page_defaults', 14);

function weblazem_portfolio_page_options_menu() {
    add_submenu_page(
        'weblazem-theme-options',
        'تنظیمات نمونه کار',
        '  نمونه کار',
        'manage_options',
        'weblazem-portfolio-page-options',
        'weblazem_portfolio_page_options_display'
    );
}
add_action('admin_menu', 'weblazem_portfolio_page_options_menu', 21);

function weblazem_register_portfolio_page_settings() {
    $defaults = weblazem_portfolio_page_defaults();

    foreach (array_keys($defaults) as $key) {
        register_setting('weblazem_portfolio_page_group', $key);
    }
}
add_action('admin_init', 'weblazem_register_portfolio_page_settings');

function weblazem_portfolio_page_admin_scripts($hook) {
    if (strpos($hook, 'weblazem-portfolio-page-options') === false) {
        return;
    }

    wp_enqueue_media();
}
add_action('admin_enqueue_scripts', 'weblazem_portfolio_page_admin_scripts');

function weblazem_portfolio_page_options_display() {
    $defaults = weblazem_portfolio_page_defaults();
    $opts     = array();

    foreach ($defaults as $key => $default) {
        $opts[$key] = get_option($key, $default);
    }

    $archive_url = weblazem_get_portfolio_page_url();
    ?>
    <div class="wrap" dir="rtl">
        <div class="weblazem-admin-header">
            <div class="weblazem-admin-header-content">
                <h1>تنظیمات نمونه کار</h1>
                <p>
                    محتوای صفحه آرشیو نمونه کارها را مدیریت کنید.
                    <a href="<?php echo esc_url($archive_url); ?>" target="_blank" rel="noopener">مشاهده صفحه</a>
                    |
                    <a href="<?php echo esc_url(admin_url('edit.php?post_type=portfolio')); ?>">مدیریت نمونه کارها</a>
                </p>
            </div>
        </div>

        <div class="weblazem-admin-content" style="margin-top:24px;">
            <form method="post" action="options.php">
                <?php settings_fields('weblazem_portfolio_page_group'); ?>

                <div class="weblazem-admin-card">
                    <div class="weblazem-admin-card-icon"><i class="fas fa-star"></i></div>
                    <h3>بخش هیرو</h3>
                    <table class="form-table">
                        <tr>
                            <th scope="row">عنوان اصلی</th>
                            <td>
                                <input type="text" name="weblazem_portfolio_page_hero_title" class="large-text"
                                       value="<?php echo esc_attr($opts['weblazem_portfolio_page_hero_title']); ?>" />
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">متن توضیحی</th>
                            <td>
                                <textarea name="weblazem_portfolio_page_hero_description" class="large-text" rows="4"><?php echo esc_textarea($opts['weblazem_portfolio_page_hero_description']); ?></textarea>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">تصویر/ایلاستریشن</th>
                            <td>
                                <input type="hidden" id="weblazem_portfolio_page_hero_image"
                                       name="weblazem_portfolio_page_hero_image"
                                       value="<?php echo esc_attr($opts['weblazem_portfolio_page_hero_image']); ?>" />
                                <div id="portfolio-hero-image-preview" style="margin-bottom:12px;">
                                    <?php if (!empty($opts['weblazem_portfolio_page_hero_image'])) : ?>
                                        <img src="<?php echo esc_url($opts['weblazem_portfolio_page_hero_image']); ?>"
                                             style="max-width:280px;border-radius:12px;" alt="" />
                                    <?php endif; ?>
                                </div>
                                <button type="button" class="button button-primary" id="upload_portfolio_hero_image">انتخاب تصویر</button>
                                <button type="button" class="button" id="remove_portfolio_hero_image">حذف تصویر</button>
                                <p class="description">در صورت خالی بودن، ایلاستریشن پیش‌فرض نمایش داده می‌شود.</p>
                            </td>
                        </tr>
                    </table>
                </div>

                <div class="weblazem-admin-card">
                    <div class="weblazem-admin-card-icon"><i class="fas fa-th-large"></i></div>
                    <h3>بخش آخرین پروژه‌ها</h3>
                    <table class="form-table">
                        <tr>
                            <th scope="row">نمایش بخش</th>
                            <td>
                                <label>
                                    <input type="checkbox" name="weblazem_portfolio_page_latest_enabled" value="1"
                                        <?php checked($opts['weblazem_portfolio_page_latest_enabled'], '1'); ?> />
                                    فعال
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">عنوان بخش</th>
                            <td>
                                <input type="text" name="weblazem_portfolio_page_latest_title" class="large-text"
                                       value="<?php echo esc_attr($opts['weblazem_portfolio_page_latest_title']); ?>" />
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">تعداد نمایش</th>
                            <td>
                                <input type="number" name="weblazem_portfolio_page_latest_count" min="1" max="12"
                                       value="<?php echo esc_attr($opts['weblazem_portfolio_page_latest_count']); ?>" />
                                <p class="description">جدیدترین نمونه کارها به‌صورت خودکار نمایش داده می‌شوند.</p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">متن دکمه کارت</th>
                            <td>
                                <input type="text" name="weblazem_portfolio_page_card_button_text" class="regular-text"
                                       value="<?php echo esc_attr($opts['weblazem_portfolio_page_card_button_text']); ?>" />
                            </td>
                        </tr>
                    </table>
                </div>

                <div class="weblazem-admin-card">
                    <div class="weblazem-admin-card-icon"><i class="fas fa-handshake"></i></div>
                    <h3>بخش تعرفه‌ها / مشاوره</h3>
                    <table class="form-table">
                        <tr>
                            <th scope="row">نمایش بخش</th>
                            <td>
                                <label>
                                    <input type="checkbox" name="weblazem_portfolio_page_tariffs_enabled" value="1"
                                        <?php checked($opts['weblazem_portfolio_page_tariffs_enabled'], '1'); ?> />
                                    فعال
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">عنوان</th>
                            <td>
                                <input type="text" name="weblazem_portfolio_page_tariffs_title" class="large-text"
                                       value="<?php echo esc_attr($opts['weblazem_portfolio_page_tariffs_title']); ?>" />
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">توضیح کوتاه</th>
                            <td>
                                <textarea name="weblazem_portfolio_page_tariffs_description" class="large-text" rows="3"><?php echo esc_textarea($opts['weblazem_portfolio_page_tariffs_description']); ?></textarea>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">متن باکس مشاوره</th>
                            <td>
                                <textarea name="weblazem_portfolio_page_tariffs_cta_text" class="large-text" rows="3"><?php echo esc_textarea($opts['weblazem_portfolio_page_tariffs_cta_text']); ?></textarea>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">متن دکمه</th>
                            <td>
                                <input type="text" name="weblazem_portfolio_page_tariffs_btn_text" class="regular-text"
                                       value="<?php echo esc_attr($opts['weblazem_portfolio_page_tariffs_btn_text']); ?>" />
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">لینک دکمه</th>
                            <td>
                                <input type="url" name="weblazem_portfolio_page_tariffs_btn_url" class="large-text"
                                       value="<?php echo esc_attr($opts['weblazem_portfolio_page_tariffs_btn_url']); ?>" />
                            </td>
                        </tr>
                    </table>
                </div>

                <div class="weblazem-admin-card">
                    <div class="weblazem-admin-card-icon"><i class="fas fa-folder-open"></i></div>
                    <h3>بخش تمام پروژه‌ها</h3>
                    <table class="form-table">
                        <tr>
                            <th scope="row">عنوان بخش</th>
                            <td>
                                <input type="text" name="weblazem_portfolio_page_all_title" class="large-text"
                                       value="<?php echo esc_attr($opts['weblazem_portfolio_page_all_title']); ?>" />
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">تعداد در هر صفحه</th>
                            <td>
                                <input type="number" name="weblazem_portfolio_page_all_per_page" min="4" max="24" step="4"
                                       value="<?php echo esc_attr($opts['weblazem_portfolio_page_all_per_page']); ?>" />
                                <p class="description">برای صفحه‌بندی؛ پیشنهاد: ۸ (دو ردیف ۴ تایی)</p>
                            </td>
                        </tr>
                    </table>
                </div>

                <?php submit_button('ذخیره تنظیمات'); ?>
            </form>
        </div>
    </div>

    <script>
    jQuery(function($) {
        var frame;

        $('#upload_portfolio_hero_image').on('click', function(e) {
            e.preventDefault();

            if (frame) {
                frame.open();
                return;
            }

            frame = wp.media({
                title: 'انتخاب تصویر هیرو',
                button: { text: 'استفاده از این تصویر' },
                multiple: false
            });

            frame.on('select', function() {
                var attachment = frame.state().get('selection').first().toJSON();
                $('#weblazem_portfolio_page_hero_image').val(attachment.url);
                $('#portfolio-hero-image-preview').html(
                    '<img src="' + attachment.url + '" style="max-width:280px;border-radius:12px;" alt="" />'
                );
            });

            frame.open();
        });

        $('#remove_portfolio_hero_image').on('click', function(e) {
            e.preventDefault();
            $('#weblazem_portfolio_page_hero_image').val('');
            $('#portfolio-hero-image-preview').empty();
        });
    });
    </script>
    <?php
}

/**
 * Checkbox options are absent from POST when unchecked — normalize on save.
 */
function weblazem_sanitize_portfolio_page_checkboxes($value) {
    return $value === '1' ? '1' : '0';
}

function weblazem_portfolio_page_checkbox_defaults() {
    add_filter('pre_update_option_weblazem_portfolio_page_latest_enabled', 'weblazem_sanitize_portfolio_page_checkboxes');
    add_filter('pre_update_option_weblazem_portfolio_page_tariffs_enabled', 'weblazem_sanitize_portfolio_page_checkboxes');
}
add_action('admin_init', 'weblazem_portfolio_page_checkbox_defaults');

function weblazem_portfolio_page_handle_unchecked_checkboxes() {
    if (!isset($_POST['option_page']) || $_POST['option_page'] !== 'weblazem_portfolio_page_group') {
        return;
    }

    if (!isset($_POST['weblazem_portfolio_page_latest_enabled'])) {
        update_option('weblazem_portfolio_page_latest_enabled', '0');
    }

    if (!isset($_POST['weblazem_portfolio_page_tariffs_enabled'])) {
        update_option('weblazem_portfolio_page_tariffs_enabled', '0');
    }
}
add_action('admin_init', 'weblazem_portfolio_page_handle_unchecked_checkboxes', 20);
