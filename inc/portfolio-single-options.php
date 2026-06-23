<?php
/**
 * Global settings for single portfolio pages (fixed sections).
 */

function weblazem_portfolio_single_options_defaults() {
    return array(
        'weblazem_portfolio_single_sticky_phone'       => '021-12345678',
        'weblazem_portfolio_single_sticky_btn_text'    => 'ثبت درخواست مشاوره',
        'weblazem_portfolio_single_sticky_btn_url'     => '#',
        'weblazem_portfolio_single_more_enabled'       => '1',
        'weblazem_portfolio_single_more_title'         => 'نمونه‌کارهای بیشتر',
        'weblazem_portfolio_single_more_text'          => 'پروژه‌های دیگری که توسط تیم وب‌لازم طراحی و اجرا شده‌اند را ببینید و از نزدیک با کیفیت کار ما آشنا شوید.',
        'weblazem_portfolio_single_more_btn_text'      => 'همه نمونه‌کارها',
        'weblazem_portfolio_single_more_btn_url'       => '',
        'weblazem_portfolio_single_more_count'         => '8',
        'weblazem_portfolio_single_consult_enabled'    => '1',
        'weblazem_portfolio_single_consult_title'      => 'در صدر بازار خود قرار بگیرید',
        'weblazem_portfolio_single_consult_text'       => 'با استراتژی درست، طراحی حرفه‌ای وب‌سایت و بازاریابی دیجیتال می‌توانید در بازار رقابتی امروز جایگاه برتر کسب‌وکار خود را تثبیت کنید.',
        'weblazem_portfolio_single_consult_image'      => '',
        'weblazem_portfolio_single_consult_phone'      => '021-12345678',
        'weblazem_portfolio_single_consult_btn_text'   => 'ثبت درخواست مشاوره',
        'weblazem_portfolio_single_consult_btn_url'    => '#',
        'weblazem_portfolio_single_cta_enabled'        => '1',
        'weblazem_portfolio_single_cta_subtitle'       => 'به ما محول کنید',
        'weblazem_portfolio_single_cta_title'          => 'پروژه بعدی شما می‌تواند اینجا باشد',
        'weblazem_portfolio_single_cta_highlight'      => 'بعدی',
        'weblazem_portfolio_single_cta_phone'          => '021-12345678',
        'weblazem_portfolio_single_cta_btn_text'       => 'درخواست مشاوره',
        'weblazem_portfolio_single_cta_btn_url'        => '#',
    );
}

function weblazem_get_default_portfolio_single_promo_cards() {
    return array(
        array(
            'title'    => 'طراحی فروشگاه اینترنتی',
            'subtitle' => 'E-Commerce Design',
            'text'     => 'فروشگاه آنلاین حرفه‌ای با تجربه کاربری مدرن',
            'image'    => '',
            'url'      => '',
            'category' => 'foroushgahee-interneti',
        ),
        array(
            'title'    => 'طراحی سایت شرکتی',
            'subtitle' => 'Corporate Website',
            'text'     => 'وب‌سایت شرکتی با هویت بصری قوی و ساختار حرفه‌ای',
            'image'    => '',
            'url'      => '',
            'category' => 'site-sherkati',
        ),
    );
}

function weblazem_ensure_portfolio_single_options_defaults() {
    foreach (weblazem_portfolio_single_options_defaults() as $key => $value) {
        if (get_option($key) === false) {
            update_option($key, $value);
        }
    }

    if (get_option('weblazem_portfolio_single_promo_cards') === false) {
        update_option('weblazem_portfolio_single_promo_cards', weblazem_get_default_portfolio_single_promo_cards());
    }
}
add_action('init', 'weblazem_ensure_portfolio_single_options_defaults', 14);

function weblazem_portfolio_single_options_menu() {
    add_submenu_page(
        'weblazem-theme-options',
        'تنظیمات نمونه کار تکی',
        '  نمونه کار تکی',
        'manage_options',
        'weblazem-portfolio-single-options',
        'weblazem_portfolio_single_options_display'
    );
}
add_action('admin_menu', 'weblazem_portfolio_single_options_menu', 22);

function weblazem_register_portfolio_single_settings() {
    foreach (array_keys(weblazem_portfolio_single_options_defaults()) as $key) {
        register_setting('weblazem_portfolio_single_group', $key);
    }

    register_setting(
        'weblazem_portfolio_single_group',
        'weblazem_portfolio_single_promo_cards',
        array('sanitize_callback' => 'weblazem_sanitize_portfolio_single_promo_cards')
    );
}
add_action('admin_init', 'weblazem_register_portfolio_single_settings');

function weblazem_sanitize_portfolio_single_promo_cards($input) {
    if (!is_array($input)) {
        return weblazem_get_default_portfolio_single_promo_cards();
    }

    $sanitized = array();

    foreach ($input as $card) {
        if (empty($card['title'])) {
            continue;
        }

        $sanitized[] = array(
            'title'    => sanitize_text_field($card['title']),
            'subtitle' => sanitize_text_field($card['subtitle'] ?? ''),
            'text'     => sanitize_text_field($card['text'] ?? ''),
            'image'    => esc_url_raw($card['image'] ?? ''),
            'url'      => esc_url_raw($card['url'] ?? ''),
            'category' => sanitize_title($card['category'] ?? ''),
        );
    }

    return !empty($sanitized) ? $sanitized : weblazem_get_default_portfolio_single_promo_cards();
}

function weblazem_portfolio_single_options_admin_scripts($hook) {
    if (strpos($hook, 'weblazem-portfolio-single-options') === false) {
        return;
    }

    wp_enqueue_media();
}
add_action('admin_enqueue_scripts', 'weblazem_portfolio_single_options_admin_scripts');

function weblazem_get_portfolio_single_option($key, $default = '') {
    $defaults = weblazem_portfolio_single_options_defaults();
    $fallback = isset($defaults[$key]) ? $defaults[$key] : $default;

    return get_option($key, $fallback);
}

function weblazem_get_portfolio_single_promo_cards() {
    $cards = get_option('weblazem_portfolio_single_promo_cards');

    if (!is_array($cards) || empty($cards)) {
        return weblazem_get_default_portfolio_single_promo_cards();
    }

    return $cards;
}

function weblazem_portfolio_single_options_display() {
    $defaults = weblazem_portfolio_single_options_defaults();
    $opts     = array();

    foreach ($defaults as $key => $default) {
        $opts[$key] = get_option($key, $default);
    }

    $promo_cards = weblazem_get_portfolio_single_promo_cards();
    $categories  = weblazem_get_portfolio_category_choices();
    $more_url    = !empty($opts['weblazem_portfolio_single_more_btn_url'])
        ? $opts['weblazem_portfolio_single_more_btn_url']
        : weblazem_get_portfolio_page_url();
    ?>
    <div class="wrap" dir="rtl">
        <div class="weblazem-admin-header">
            <div class="weblazem-admin-header-content">
                <h1>تنظیمات نمونه کار تکی</h1>
                <p>بخش‌های ثابت که در تمام صفحات جزئیات نمونه کار نمایش داده می‌شوند.</p>
            </div>
        </div>

        <div class="weblazem-admin-content" style="margin-top:24px;">
            <form method="post" action="options.php">
                <?php settings_fields('weblazem_portfolio_single_group'); ?>

                <div class="weblazem-admin-card">
                    <div class="weblazem-admin-card-icon"><i class="fas fa-phone"></i></div>
                    <h3>نوار شناور پایین صفحه</h3>
                    <table class="form-table">
                        <tr>
                            <th>شماره تماس</th>
                            <td><input type="text" name="weblazem_portfolio_single_sticky_phone" class="regular-text" value="<?php echo esc_attr($opts['weblazem_portfolio_single_sticky_phone']); ?>" /></td>
                        </tr>
                        <tr>
                            <th>متن دکمه</th>
                            <td><input type="text" name="weblazem_portfolio_single_sticky_btn_text" class="regular-text" value="<?php echo esc_attr($opts['weblazem_portfolio_single_sticky_btn_text']); ?>" /></td>
                        </tr>
                        <tr>
                            <th>لینک دکمه</th>
                            <td><input type="url" name="weblazem_portfolio_single_sticky_btn_url" class="large-text" value="<?php echo esc_attr($opts['weblazem_portfolio_single_sticky_btn_url']); ?>" /></td>
                        </tr>
                    </table>
                </div>

                <div class="weblazem-admin-card">
                    <div class="weblazem-admin-card-icon"><i class="fas fa-images"></i></div>
                    <h3>بخش نمونه‌کارهای بیشتر</h3>
                    <table class="form-table">
                        <tr>
                            <th>فعال</th>
                            <td><label><input type="checkbox" name="weblazem_portfolio_single_more_enabled" value="1" <?php checked($opts['weblazem_portfolio_single_more_enabled'], '1'); ?> /> نمایش بخش</label></td>
                        </tr>
                        <tr>
                            <th>عنوان</th>
                            <td><input type="text" name="weblazem_portfolio_single_more_title" class="large-text" value="<?php echo esc_attr($opts['weblazem_portfolio_single_more_title']); ?>" /></td>
                        </tr>
                        <tr>
                            <th>توضیحات</th>
                            <td><textarea name="weblazem_portfolio_single_more_text" class="large-text" rows="3"><?php echo esc_textarea($opts['weblazem_portfolio_single_more_text']); ?></textarea></td>
                        </tr>
                        <tr>
                            <th>متن دکمه</th>
                            <td><input type="text" name="weblazem_portfolio_single_more_btn_text" class="regular-text" value="<?php echo esc_attr($opts['weblazem_portfolio_single_more_btn_text']); ?>" /></td>
                        </tr>
                        <tr>
                            <th>لینک دکمه</th>
                            <td>
                                <input type="url" name="weblazem_portfolio_single_more_btn_url" class="large-text" value="<?php echo esc_attr($opts['weblazem_portfolio_single_more_btn_url']); ?>" placeholder="<?php echo esc_attr(weblazem_get_portfolio_page_url()); ?>" />
                                <p class="description">در صورت خالی بودن به صفحه نمونه کارها لینک می‌شود.</p>
                            </td>
                        </tr>
                        <tr>
                            <th>تعداد نمایش</th>
                            <td><input type="number" name="weblazem_portfolio_single_more_count" min="4" max="16" value="<?php echo esc_attr($opts['weblazem_portfolio_single_more_count']); ?>" /></td>
                        </tr>
                    </table>
                </div>

                <div class="weblazem-admin-card">
                    <div class="weblazem-admin-card-icon"><i class="fas fa-handshake"></i></div>
                    <h3>بخش درخواست مشاوره و راهنمایی</h3>
                    <p class="description">تنظیمات این بخش به <strong>تنظیمات قالب → مودال درخواست مشاوره</strong> منتقل شده است.</p>
                    <table class="form-table">
                        <tr>
                            <th>فعال</th>
                            <td><label><input type="checkbox" name="weblazem_portfolio_single_consult_enabled" value="1" <?php checked($opts['weblazem_portfolio_single_consult_enabled'], '1'); ?> disabled /> از تنظیمات سراسری مدیریت می‌شود</label></td>
                        </tr>
                    </table>
                    <p><a href="<?php echo esc_url(admin_url('admin.php?page=weblazem-consultation-options')); ?>" class="button button-primary">رفتن به تنظیمات مودال مشاوره</a></p>
                </div>

                <div class="weblazem-admin-card">
                    <div class="weblazem-admin-card-icon"><i class="fas fa-bullhorn"></i></div>
                    <h3>بخش دعوت به اقدام پایانی + کارت‌های دسته‌بندی</h3>
                    <table class="form-table">
                        <tr>
                            <th>فعال</th>
                            <td><label><input type="checkbox" name="weblazem_portfolio_single_cta_enabled" value="1" <?php checked($opts['weblazem_portfolio_single_cta_enabled'], '1'); ?> /> نمایش بخش</label></td>
                        </tr>
                        <tr>
                            <th>زیرعنوان</th>
                            <td><input type="text" name="weblazem_portfolio_single_cta_subtitle" class="large-text" value="<?php echo esc_attr($opts['weblazem_portfolio_single_cta_subtitle']); ?>" /></td>
                        </tr>
                        <tr>
                            <th>عنوان اصلی</th>
                            <td><input type="text" name="weblazem_portfolio_single_cta_title" class="large-text" value="<?php echo esc_attr($opts['weblazem_portfolio_single_cta_title']); ?>" /></td>
                        </tr>
                        <tr>
                            <th>کلمه برجسته</th>
                            <td><input type="text" name="weblazem_portfolio_single_cta_highlight" class="regular-text" value="<?php echo esc_attr($opts['weblazem_portfolio_single_cta_highlight']); ?>" /></td>
                        </tr>
                        <tr>
                            <th>شماره تماس</th>
                            <td><input type="text" name="weblazem_portfolio_single_cta_phone" class="regular-text" value="<?php echo esc_attr($opts['weblazem_portfolio_single_cta_phone']); ?>" /></td>
                        </tr>
                        <tr>
                            <th>متن دکمه</th>
                            <td><input type="text" name="weblazem_portfolio_single_cta_btn_text" class="regular-text" value="<?php echo esc_attr($opts['weblazem_portfolio_single_cta_btn_text']); ?>" /></td>
                        </tr>
                        <tr>
                            <th>لینک دکمه</th>
                            <td><input type="url" name="weblazem_portfolio_single_cta_btn_url" class="large-text" value="<?php echo esc_attr($opts['weblazem_portfolio_single_cta_btn_url']); ?>" /></td>
                        </tr>
                    </table>

                    <h4 style="margin-top:24px;">کارت‌های معرفی دسته‌ها</h4>
                    <p class="description">تصویر هر کارت به‌صورت محو در پس‌زمینه و واضح در قاب سمت چپ نمایش داده می‌شود. اگر تصویر خالی باشد، از اولین نمونه کار همان دسته استفاده می‌شود.</p>
                    <div id="portfolio-promo-cards-container">
                        <?php foreach ($promo_cards as $index => $card) : ?>
                            <div class="weblazem-promo-card-admin" style="background:#f8f5fc;padding:16px;border-radius:12px;margin-bottom:16px;border:1px solid #e8dff0;">
                                <table class="form-table">
                                    <tr>
                                        <th>عنوان</th>
                                        <td><input type="text" name="weblazem_portfolio_single_promo_cards[<?php echo (int) $index; ?>][title]" class="large-text" value="<?php echo esc_attr($card['title']); ?>" /></td>
                                    </tr>
                                    <tr>
                                        <th>زیرعنوان انگلیسی</th>
                                        <td><input type="text" name="weblazem_portfolio_single_promo_cards[<?php echo (int) $index; ?>][subtitle]" class="regular-text" value="<?php echo esc_attr($card['subtitle']); ?>" dir="ltr" /></td>
                                    </tr>
                                    <tr>
                                        <th>توضیح کوتاه</th>
                                        <td><input type="text" name="weblazem_portfolio_single_promo_cards[<?php echo (int) $index; ?>][text]" class="large-text" value="<?php echo esc_attr($card['text']); ?>" /></td>
                                    </tr>
                                    <tr>
                                        <th>تصویر</th>
                                        <td>
                                            <input type="hidden" class="promo-card-image-input" name="weblazem_portfolio_single_promo_cards[<?php echo (int) $index; ?>][image]" value="<?php echo esc_attr($card['image']); ?>" />
                                            <div class="promo-card-image-preview" style="margin-bottom:8px;">
                                                <?php if (!empty($card['image'])) : ?><img src="<?php echo esc_url($card['image']); ?>" style="max-width:180px;border-radius:10px;" alt="" /><?php endif; ?>
                                            </div>
                                            <button type="button" class="button upload-promo-card-image">انتخاب تصویر</button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>لینک</th>
                                        <td><input type="url" name="weblazem_portfolio_single_promo_cards[<?php echo (int) $index; ?>][url]" class="large-text" value="<?php echo esc_attr($card['url']); ?>" /></td>
                                    </tr>
                                    <tr>
                                        <th>دسته (فیلتر)</th>
                                        <td>
                                            <select name="weblazem_portfolio_single_promo_cards[<?php echo (int) $index; ?>][category]">
                                                <?php foreach ($categories as $slug => $label) : if ($slug === '') continue; ?>
                                                    <option value="<?php echo esc_attr($slug); ?>" <?php selected($card['category'], $slug); ?>><?php echo esc_html($label); ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <?php submit_button('ذخیره تنظیمات'); ?>
            </form>
        </div>
    </div>

    <script>
    jQuery(function($) {
        var frame;
        $(document).on('click', '.upload-promo-card-image', function(e) {
            e.preventDefault();
            var $wrap = $(this).closest('td');
            if (!frame) {
                frame = wp.media({ title: 'انتخاب تصویر', button: { text: 'استفاده' }, multiple: false });
            }
            frame.off('select').on('select', function() {
                var url = frame.state().get('selection').first().toJSON().url;
                $wrap.find('.promo-card-image-input').val(url);
                $wrap.find('.promo-card-image-preview').html('<img src="' + url + '" style="max-width:180px;border-radius:10px;" alt="" />');
            });
            frame.open();
        });
    });
    </script>
    <?php
}

function weblazem_portfolio_single_handle_checkboxes() {
    if (!isset($_POST['option_page']) || $_POST['option_page'] !== 'weblazem_portfolio_single_group') {
        return;
    }

    $checkboxes = array(
        'weblazem_portfolio_single_more_enabled',
        'weblazem_portfolio_single_consult_enabled',
        'weblazem_portfolio_single_cta_enabled',
    );

    foreach ($checkboxes as $checkbox) {
        if (!isset($_POST[$checkbox])) {
            update_option($checkbox, '0');
        }
    }
}
add_action('admin_init', 'weblazem_portfolio_single_handle_checkboxes', 20);

function weblazem_get_more_portfolio_items($exclude_id = 0, $count = 8) {
    return new WP_Query(array(
        'post_type'      => 'portfolio',
        'posts_per_page' => max(1, (int) $count),
        'post_status'    => 'publish',
        'orderby'        => 'date',
        'order'          => 'DESC',
        'post__not_in'   => $exclude_id ? array((int) $exclude_id) : array(),
    ));
}

function weblazem_get_promo_card_background_image($card) {
    if (!empty($card['image'])) {
        return $card['image'];
    }

    if (empty($card['category'])) {
        return '';
    }

    $query = new WP_Query(array(
        'post_type'      => 'portfolio',
        'posts_per_page' => 1,
        'post_status'    => 'publish',
        'orderby'        => 'date',
        'order'          => 'DESC',
        'tax_query'      => array(
            array(
                'taxonomy' => 'portfolio_category',
                'field'    => 'slug',
                'terms'    => sanitize_title($card['category']),
            ),
        ),
    ));

    if (!$query->have_posts()) {
        return '';
    }

    $query->the_post();
    $image = weblazem_get_portfolio_single_hero_image(get_the_ID());
    wp_reset_postdata();

    return $image;
}

function weblazem_get_promo_card_url($card) {
    if (!empty($card['url'])) {
        return $card['url'];
    }

    if (!empty($card['category'])) {
        return add_query_arg('portfolio_tab', sanitize_title($card['category']), weblazem_get_portfolio_page_url());
    }

    return weblazem_get_portfolio_page_url();
}

function weblazem_highlight_cta_title($title, $highlight) {
    if (empty($highlight) || strpos($title, $highlight) === false) {
        return esc_html($title);
    }

    $parts = explode($highlight, $title, 2);

    return esc_html($parts[0]) . '<span class="portfolio-single-cta__highlight">' . esc_html($highlight) . '</span>' . esc_html($parts[1]);
}
