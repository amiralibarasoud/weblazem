<?php
/**
 * Price estimator — defaults, calc engine, CPT, page setup, options, AJAX, enqueue.
 */

define('WEBLAZEM_PRICE_ESTIMATOR_SLUG', 'mohasebe-gheymat');
define('WEBLAZEM_PRICE_ESTIMATOR_TEMPLATE', 'price-estimator-template.php');
define('WEBLAZEM_PRICE_ESTIMATOR_OPTION', 'weblazem_price_estimator_page_id');

function weblazem_price_estimator_defaults() {
    return array(
        'enabled'  => '1',
        'title'    => 'محاسبه‌گر قیمت طراحی سایت',
        'subtitle' => 'با چند کلیک، بازه تقریبی هزینه پروژه خود را ببینید',
        'intro'    => 'نوع سایت، تعداد صفحات، امکانات جانبی و زمان‌بندی را انتخاب کنید تا برآورد شفاف و واقعی دریافت کنید.',
        'site_types' => array(
            'corporate' => array(
                'label' => 'سایت شرکتی',
                'base'  => 25000000,
                'desc'  => 'معرفی کسب‌وکار، خدمات و تماس',
            ),
            'shop' => array(
                'label' => 'فروشگاه اینترنتی',
                'base'  => 45000000,
                'desc'  => 'فروش محصول، سبد خرید و درگاه پرداخت',
            ),
            'landing' => array(
                'label' => 'لندینگ پیج',
                'base'  => 18000000,
                'desc'  => 'صفحه فرود تبلیغاتی و تبدیل محور',
            ),
            'custom' => array(
                'label' => 'پروژه سفارشی',
                'base'  => 60000000,
                'desc'  => 'سامانه اختصاصی یا نیازمندی خاص',
            ),
        ),
        'page_tiers' => array(
            '1-5'   => array('label' => '۱ تا ۵ صفحه', 'multiplier' => 1),
            '6-10'  => array('label' => '۶ تا ۱۰ صفحه', 'multiplier' => 1.25),
            '11-20' => array('label' => '۱۱ تا ۲۰ صفحه', 'multiplier' => 1.55),
            '20+'   => array('label' => 'بیش از ۲۰ صفحه', 'multiplier' => 1.9),
        ),
        'addons' => array(
            'seo' => array(
                'label' => 'سئو پایه',
                'price' => 8000000,
                'desc'  => 'بهینه‌سازی اولیه برای موتورهای جستجو',
            ),
            'content' => array(
                'label' => 'تولید محتوا',
                'price' => 12000000,
                'desc'  => 'نگارش و آماده‌سازی محتوای صفحات',
            ),
            'support' => array(
                'label' => 'پشتیبانی ماهانه',
                'price' => 5000000,
                'desc'  => 'پشتیبانی فنی و به‌روزرسانی‌ها',
            ),
        ),
        'urgency' => array(
            'normal' => array('label' => 'زمان عادی', 'multiplier' => 1, 'desc' => 'زمان‌بندی استاندارد پروژه'),
            'rush'   => array('label' => 'سریع', 'multiplier' => 1.2, 'desc' => 'تحویل زودتر از حالت عادی'),
            'asap'   => array('label' => 'فوری', 'multiplier' => 1.4, 'desc' => 'اولویت بالا و تحویل خیلی سریع'),
        ),
        'range_spread'         => 12,
        'success_text'         => 'برآورد شما ثبت شد. کارشناسان وب‌لازم به‌زودی با شما تماس می‌گیرند.',
        'result_cta_text'      => 'دریافت مشاوره رایگان درباره این برآورد',
        'consult_cta_text'     => 'مشاوره رایگان',
        'consult_cta_url'      => '',
        'start_project_cta_text' => 'شروع پروژه',
        'start_project_cta_url'  => '',
        'lead_form_title'      => 'این برآورد را ذخیره کنید',
        'lead_form_subtitle'   => 'نام و موبایل خود را وارد کنید تا جزئیات را برای شما نگه داریم.',
        'save_lead'            => '1',
        'disabled_message'     => 'محاسبه‌گر قیمت موقتاً غیرفعال است.',
    );
}

function weblazem_get_price_estimator_settings() {
    $defaults = weblazem_price_estimator_defaults();
    $saved    = get_option('weblazem_price_estimator_settings', array());

    if (!is_array($saved)) {
        $saved = array();
    }

    $settings = wp_parse_args($saved, $defaults);

    foreach (array('site_types', 'page_tiers', 'addons', 'urgency') as $key) {
        if (empty($settings[$key]) || !is_array($settings[$key])) {
            $settings[$key] = $defaults[$key];
        } else {
            $settings[$key] = wp_parse_args($settings[$key], $defaults[$key]);
            foreach ($defaults[$key] as $item_key => $item_default) {
                if (isset($settings[$key][$item_key]) && is_array($item_default)) {
                    $settings[$key][$item_key] = wp_parse_args($settings[$key][$item_key], $item_default);
                }
            }
        }
    }

    $settings['range_spread'] = max(0, min(50, (int) $settings['range_spread']));
    $settings['enabled']      = ($settings['enabled'] === '1') ? '1' : '0';
    $settings['save_lead']    = ($settings['save_lead'] === '1') ? '1' : '0';

    return $settings;
}

function weblazem_ensure_price_estimator_defaults() {
    if (get_option('weblazem_price_estimator_settings') === false) {
        update_option('weblazem_price_estimator_settings', weblazem_price_estimator_defaults());
    }
}
add_action('init', 'weblazem_ensure_price_estimator_defaults', 12);

function weblazem_get_price_estimator_page_id() {
    return weblazem_growth_get_page_id(WEBLAZEM_PRICE_ESTIMATOR_OPTION, WEBLAZEM_PRICE_ESTIMATOR_SLUG);
}

function weblazem_get_price_estimator_page_url() {
    return weblazem_growth_get_page_url(WEBLAZEM_PRICE_ESTIMATOR_OPTION, WEBLAZEM_PRICE_ESTIMATOR_SLUG);
}

function weblazem_is_price_estimator_page() {
    return weblazem_growth_is_page(
        WEBLAZEM_PRICE_ESTIMATOR_TEMPLATE,
        WEBLAZEM_PRICE_ESTIMATOR_OPTION,
        WEBLAZEM_PRICE_ESTIMATOR_SLUG
    );
}

function weblazem_ensure_price_estimator_page() {
    weblazem_growth_ensure_page(
        array(
            'slug'     => WEBLAZEM_PRICE_ESTIMATOR_SLUG,
            'template' => WEBLAZEM_PRICE_ESTIMATOR_TEMPLATE,
            'title'    => 'محاسبه‌گر قیمت',
            'option'   => WEBLAZEM_PRICE_ESTIMATOR_OPTION,
        )
    );
}
add_action('init', 'weblazem_ensure_price_estimator_page', 38);

function weblazem_register_price_estimate_lead_cpt() {
    $labels = array(
        'name'               => 'لیدهای برآورد قیمت',
        'singular_name'      => 'لید برآورد',
        'menu_name'          => 'لیدهای برآورد قیمت',
        'all_items'          => 'همه لیدها',
        'edit_item'          => 'ویرایش لید',
        'view_item'          => 'مشاهده لید',
        'search_items'       => 'جستجوی لید',
        'not_found'          => 'لیدی یافت نشد.',
        'not_found_in_trash' => 'لیدی در زباله‌دان یافت نشد.',
    );

    register_post_type(
        'price_estimate_lead',
        array(
            'labels'             => $labels,
            'public'             => false,
            'publicly_queryable' => false,
            'show_ui'            => true,
            'show_in_menu'       => false,
            'show_in_rest'       => false,
            'capability_type'    => 'post',
            'map_meta_cap'       => true,
            'hierarchical'       => false,
            'supports'           => array('title'),
            'has_archive'        => false,
        )
    );
}
add_action('init', 'weblazem_register_price_estimate_lead_cpt');

function weblazem_price_estimator_sanitize_settings($input) {
    $defaults = weblazem_price_estimator_defaults();
    $out      = $defaults;

    if (!is_array($input)) {
        return $out;
    }

    $out['enabled']  = (!empty($input['enabled']) && $input['enabled'] === '1') ? '1' : '0';
    $out['save_lead'] = (!empty($input['save_lead']) && $input['save_lead'] === '1') ? '1' : '0';
    $out['title']    = sanitize_text_field($input['title'] ?? $defaults['title']);
    $out['subtitle'] = sanitize_textarea_field($input['subtitle'] ?? $defaults['subtitle']);
    $out['intro']    = sanitize_textarea_field($input['intro'] ?? $defaults['intro']);
    $out['range_spread'] = max(0, min(50, absint($input['range_spread'] ?? $defaults['range_spread'])));
    $out['success_text'] = sanitize_textarea_field($input['success_text'] ?? $defaults['success_text']);
    $out['result_cta_text'] = sanitize_text_field($input['result_cta_text'] ?? $defaults['result_cta_text']);
    $out['consult_cta_text'] = sanitize_text_field($input['consult_cta_text'] ?? $defaults['consult_cta_text']);
    $out['consult_cta_url'] = esc_url_raw($input['consult_cta_url'] ?? '');
    $out['start_project_cta_text'] = sanitize_text_field($input['start_project_cta_text'] ?? $defaults['start_project_cta_text']);
    $out['start_project_cta_url'] = esc_url_raw($input['start_project_cta_url'] ?? '');
    $out['lead_form_title'] = sanitize_text_field($input['lead_form_title'] ?? $defaults['lead_form_title']);
    $out['lead_form_subtitle'] = sanitize_textarea_field($input['lead_form_subtitle'] ?? $defaults['lead_form_subtitle']);
    $out['disabled_message'] = sanitize_textarea_field($input['disabled_message'] ?? $defaults['disabled_message']);

    foreach ($defaults['site_types'] as $key => $item) {
        $row = isset($input['site_types'][$key]) && is_array($input['site_types'][$key]) ? $input['site_types'][$key] : array();
        $out['site_types'][$key] = array(
            'label' => sanitize_text_field($row['label'] ?? $item['label']),
            'base'  => max(0, absint($row['base'] ?? $item['base'])),
            'desc'  => sanitize_text_field($row['desc'] ?? $item['desc']),
        );
    }

    foreach ($defaults['page_tiers'] as $key => $item) {
        $row = isset($input['page_tiers'][$key]) && is_array($input['page_tiers'][$key]) ? $input['page_tiers'][$key] : array();
        $out['page_tiers'][$key] = array(
            'label'      => sanitize_text_field($row['label'] ?? $item['label']),
            'multiplier' => max(0.1, (float) ($row['multiplier'] ?? $item['multiplier'])),
        );
    }

    foreach ($defaults['addons'] as $key => $item) {
        $row = isset($input['addons'][$key]) && is_array($input['addons'][$key]) ? $input['addons'][$key] : array();
        $out['addons'][$key] = array(
            'label' => sanitize_text_field($row['label'] ?? $item['label']),
            'price' => max(0, absint($row['price'] ?? $item['price'])),
            'desc'  => sanitize_text_field($row['desc'] ?? $item['desc']),
        );
    }

    foreach ($defaults['urgency'] as $key => $item) {
        $row = isset($input['urgency'][$key]) && is_array($input['urgency'][$key]) ? $input['urgency'][$key] : array();
        $out['urgency'][$key] = array(
            'label'      => sanitize_text_field($row['label'] ?? $item['label']),
            'multiplier' => max(0.1, (float) ($row['multiplier'] ?? $item['multiplier'])),
            'desc'       => sanitize_text_field($row['desc'] ?? $item['desc']),
        );
    }

    return $out;
}

function weblazem_register_price_estimator_settings() {
    register_setting(
        'weblazem_price_estimator_group',
        'weblazem_price_estimator_settings',
        array(
            'type'              => 'array',
            'sanitize_callback' => 'weblazem_price_estimator_sanitize_settings',
            'default'           => weblazem_price_estimator_defaults(),
        )
    );
}
add_action('admin_init', 'weblazem_register_price_estimator_settings');

function weblazem_price_estimator_admin_menu() {
    add_submenu_page(
        'weblazem-theme-options',
        'محاسبه‌گر قیمت',
        'محاسبه‌گر قیمت',
        'manage_options',
        'weblazem-price-estimator-options',
        'weblazem_price_estimator_options_display'
    );

    add_submenu_page(
        'weblazem-theme-options',
        'لیدهای برآورد قیمت',
        'لیدهای برآورد قیمت',
        'manage_options',
        'edit.php?post_type=price_estimate_lead'
    );
}
add_action('admin_menu', 'weblazem_price_estimator_admin_menu', 28);

function weblazem_price_estimator_options_display() {
    if (!current_user_can('manage_options')) {
        return;
    }

    $s        = weblazem_get_price_estimator_settings();
    $page_url = weblazem_get_price_estimator_page_url();
    ?>
    <div class="wrap" dir="rtl">
        <h1>تنظیمات محاسبه‌گر قیمت</h1>
        <?php if ($page_url) : ?>
            <p>صفحه: <a href="<?php echo esc_url($page_url); ?>" target="_blank" rel="noopener"><?php echo esc_html($page_url); ?></a></p>
        <?php endif; ?>

        <form method="post" action="options.php">
            <?php settings_fields('weblazem_price_estimator_group'); ?>
            <input type="hidden" name="weblazem_price_estimator_settings[enabled]" value="0" />
            <input type="hidden" name="weblazem_price_estimator_settings[save_lead]" value="0" />

            <table class="form-table">
                <tr>
                    <th>فعال‌سازی</th>
                    <td>
                        <label>
                            <input type="checkbox" name="weblazem_price_estimator_settings[enabled]" value="1" <?php checked($s['enabled'], '1'); ?> />
                            نمایش و استفاده از محاسبه‌گر
                        </label>
                    </td>
                </tr>
                <tr>
                    <th>عنوان</th>
                    <td><input type="text" class="large-text" name="weblazem_price_estimator_settings[title]" value="<?php echo esc_attr($s['title']); ?>" /></td>
                </tr>
                <tr>
                    <th>زیرعنوان</th>
                    <td><textarea class="large-text" rows="2" name="weblazem_price_estimator_settings[subtitle]"><?php echo esc_textarea($s['subtitle']); ?></textarea></td>
                </tr>
                <tr>
                    <th>متن معرفی</th>
                    <td><textarea class="large-text" rows="3" name="weblazem_price_estimator_settings[intro]"><?php echo esc_textarea($s['intro']); ?></textarea></td>
                </tr>
                <tr>
                    <th>درصد بازه (±)</th>
                    <td>
                        <input type="number" min="0" max="50" name="weblazem_price_estimator_settings[range_spread]" value="<?php echo esc_attr($s['range_spread']); ?>" />
                        <p class="description">برای نمایش حداقل و حداکثر حول برآورد مرکزی.</p>
                    </td>
                </tr>
                <tr>
                    <th>ذخیره لید</th>
                    <td>
                        <label>
                            <input type="checkbox" name="weblazem_price_estimator_settings[save_lead]" value="1" <?php checked($s['save_lead'], '1'); ?> />
                            ذخیره نام و موبایل پس از برآورد
                        </label>
                    </td>
                </tr>
                <tr>
                    <th>پیام موفقیت</th>
                    <td><textarea class="large-text" rows="2" name="weblazem_price_estimator_settings[success_text]"><?php echo esc_textarea($s['success_text']); ?></textarea></td>
                </tr>
                <tr>
                    <th>عنوان فرم لید</th>
                    <td><input type="text" class="large-text" name="weblazem_price_estimator_settings[lead_form_title]" value="<?php echo esc_attr($s['lead_form_title']); ?>" /></td>
                </tr>
                <tr>
                    <th>توضیح فرم لید</th>
                    <td><textarea class="large-text" rows="2" name="weblazem_price_estimator_settings[lead_form_subtitle]"><?php echo esc_textarea($s['lead_form_subtitle']); ?></textarea></td>
                </tr>
                <tr>
                    <th>متن CTA نتیجه</th>
                    <td><input type="text" class="large-text" name="weblazem_price_estimator_settings[result_cta_text]" value="<?php echo esc_attr($s['result_cta_text']); ?>" /></td>
                </tr>
                <tr>
                    <th>دکمه مشاوره</th>
                    <td>
                        <input type="text" class="regular-text" name="weblazem_price_estimator_settings[consult_cta_text]" value="<?php echo esc_attr($s['consult_cta_text']); ?>" placeholder="متن دکمه" />
                        <input type="url" class="regular-text" dir="ltr" name="weblazem_price_estimator_settings[consult_cta_url]" value="<?php echo esc_attr($s['consult_cta_url']); ?>" placeholder="URL (اختیاری)" />
                    </td>
                </tr>
                <tr>
                    <th>دکمه شروع پروژه</th>
                    <td>
                        <input type="text" class="regular-text" name="weblazem_price_estimator_settings[start_project_cta_text]" value="<?php echo esc_attr($s['start_project_cta_text']); ?>" placeholder="متن دکمه" />
                        <input type="url" class="regular-text" dir="ltr" name="weblazem_price_estimator_settings[start_project_cta_url]" value="<?php echo esc_attr($s['start_project_cta_url']); ?>" placeholder="URL (اختیاری)" />
                    </td>
                </tr>
                <tr>
                    <th>پیام غیرفعال</th>
                    <td><textarea class="large-text" rows="2" name="weblazem_price_estimator_settings[disabled_message]"><?php echo esc_textarea($s['disabled_message']); ?></textarea></td>
                </tr>
            </table>

            <h2>انواع سایت (پایه به تومان)</h2>
            <table class="widefat striped">
                <thead>
                    <tr><th>کلید</th><th>عنوان</th><th>قیمت پایه</th><th>توضیح</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($s['site_types'] as $key => $item) : ?>
                        <tr>
                            <td><code><?php echo esc_html($key); ?></code></td>
                            <td><input type="text" class="regular-text" name="weblazem_price_estimator_settings[site_types][<?php echo esc_attr($key); ?>][label]" value="<?php echo esc_attr($item['label']); ?>" /></td>
                            <td><input type="number" min="0" step="100000" name="weblazem_price_estimator_settings[site_types][<?php echo esc_attr($key); ?>][base]" value="<?php echo esc_attr($item['base']); ?>" /></td>
                            <td><input type="text" class="large-text" name="weblazem_price_estimator_settings[site_types][<?php echo esc_attr($key); ?>][desc]" value="<?php echo esc_attr($item['desc']); ?>" /></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <h2>تعداد صفحات</h2>
            <table class="widefat striped">
                <thead>
                    <tr><th>کلید</th><th>عنوان</th><th>ضریب</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($s['page_tiers'] as $key => $item) : ?>
                        <tr>
                            <td><code><?php echo esc_html($key); ?></code></td>
                            <td><input type="text" class="regular-text" name="weblazem_price_estimator_settings[page_tiers][<?php echo esc_attr($key); ?>][label]" value="<?php echo esc_attr($item['label']); ?>" /></td>
                            <td><input type="number" min="0.1" step="0.05" name="weblazem_price_estimator_settings[page_tiers][<?php echo esc_attr($key); ?>][multiplier]" value="<?php echo esc_attr($item['multiplier']); ?>" /></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <h2>افزونه‌ها</h2>
            <table class="widefat striped">
                <thead>
                    <tr><th>کلید</th><th>عنوان</th><th>قیمت</th><th>توضیح</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($s['addons'] as $key => $item) : ?>
                        <tr>
                            <td><code><?php echo esc_html($key); ?></code></td>
                            <td><input type="text" class="regular-text" name="weblazem_price_estimator_settings[addons][<?php echo esc_attr($key); ?>][label]" value="<?php echo esc_attr($item['label']); ?>" /></td>
                            <td><input type="number" min="0" step="100000" name="weblazem_price_estimator_settings[addons][<?php echo esc_attr($key); ?>][price]" value="<?php echo esc_attr($item['price']); ?>" /></td>
                            <td><input type="text" class="large-text" name="weblazem_price_estimator_settings[addons][<?php echo esc_attr($key); ?>][desc]" value="<?php echo esc_attr($item['desc']); ?>" /></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <h2>زمان‌بندی / فوریت</h2>
            <table class="widefat striped">
                <thead>
                    <tr><th>کلید</th><th>عنوان</th><th>ضریب</th><th>توضیح</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($s['urgency'] as $key => $item) : ?>
                        <tr>
                            <td><code><?php echo esc_html($key); ?></code></td>
                            <td><input type="text" class="regular-text" name="weblazem_price_estimator_settings[urgency][<?php echo esc_attr($key); ?>][label]" value="<?php echo esc_attr($item['label']); ?>" /></td>
                            <td><input type="number" min="0.1" step="0.05" name="weblazem_price_estimator_settings[urgency][<?php echo esc_attr($key); ?>][multiplier]" value="<?php echo esc_attr($item['multiplier']); ?>" /></td>
                            <td><input type="text" class="large-text" name="weblazem_price_estimator_settings[urgency][<?php echo esc_attr($key); ?>][desc]" value="<?php echo esc_attr($item['desc']); ?>" /></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <?php submit_button('ذخیره تنظیمات'); ?>
        </form>
    </div>
    <?php
}

/**
 * Calculate estimate from selections.
 *
 * @param array $input site_type, pages, addons[], urgency
 * @return array|WP_Error
 */
function weblazem_price_estimate_calculate($input) {
    $settings = weblazem_get_price_estimator_settings();

    if ($settings['enabled'] !== '1') {
        return new WP_Error('disabled', $settings['disabled_message']);
    }

    $site_type = sanitize_key($input['site_type'] ?? '');
    $pages     = sanitize_text_field($input['pages'] ?? '');
    $urgency   = sanitize_key($input['urgency'] ?? '');
    $addons    = isset($input['addons']) && is_array($input['addons']) ? $input['addons'] : array();

    if (!isset($settings['site_types'][$site_type])) {
        return new WP_Error('invalid_site', 'نوع سایت معتبر نیست.');
    }
    if (!isset($settings['page_tiers'][$pages])) {
        return new WP_Error('invalid_pages', 'بازه تعداد صفحات معتبر نیست.');
    }
    if (!isset($settings['urgency'][$urgency])) {
        return new WP_Error('invalid_urgency', 'زمان‌بندی معتبر نیست.');
    }

    $base      = (int) $settings['site_types'][$site_type]['base'];
    $pages_mult = (float) $settings['page_tiers'][$pages]['multiplier'];
    $urg_mult  = (float) $settings['urgency'][$urgency]['multiplier'];

    $addon_sum    = 0;
    $addon_labels = array();
    $clean_addons = array();

    foreach ($addons as $addon_key) {
        $addon_key = sanitize_key($addon_key);
        if (!isset($settings['addons'][$addon_key])) {
            continue;
        }
        $clean_addons[] = $addon_key;
        $addon_sum     += (int) $settings['addons'][$addon_key]['price'];
        $addon_labels[] = $settings['addons'][$addon_key]['label'];
    }

    $estimate = (int) round(($base * $pages_mult * $urg_mult) + $addon_sum);
    $spread   = (float) $settings['range_spread'] / 100;
    $min      = (int) round($estimate * (1 - $spread));
    $max      = (int) round($estimate * (1 + $spread));

    return array(
        'site_type'       => $site_type,
        'site_type_label' => $settings['site_types'][$site_type]['label'],
        'pages'           => $pages,
        'pages_label'     => $settings['page_tiers'][$pages]['label'],
        'urgency'         => $urgency,
        'urgency_label'   => $settings['urgency'][$urgency]['label'],
        'addons'          => $clean_addons,
        'addon_labels'    => $addon_labels,
        'base'            => $base,
        'pages_mult'      => $pages_mult,
        'urgency_mult'    => $urg_mult,
        'addons_total'    => $addon_sum,
        'estimate'        => $estimate,
        'min'             => $min,
        'max'             => $max,
        'range_spread'    => (int) $settings['range_spread'],
        'estimate_fmt'    => weblazem_growth_format_toman($estimate),
        'min_fmt'         => weblazem_growth_format_toman($min),
        'max_fmt'         => weblazem_growth_format_toman($max),
    );
}

function weblazem_ajax_price_estimate_calc() {
    check_ajax_referer('weblazem_price_estimator', 'nonce');

    $rl = weblazem_growth_rate_limit('price_calc', 30, 600);
    if (is_wp_error($rl)) {
        wp_send_json_error(array('message' => $rl->get_error_message()), 429);
    }

    $addons = isset($_POST['addons']) ? (array) wp_unslash($_POST['addons']) : array();

    $result = weblazem_price_estimate_calculate(
        array(
            'site_type' => isset($_POST['site_type']) ? wp_unslash($_POST['site_type']) : '',
            'pages'     => isset($_POST['pages']) ? wp_unslash($_POST['pages']) : '',
            'urgency'   => isset($_POST['urgency']) ? wp_unslash($_POST['urgency']) : '',
            'addons'    => $addons,
        )
    );

    if (is_wp_error($result)) {
        wp_send_json_error(array('message' => $result->get_error_message()), 400);
    }

    wp_send_json_success($result);
}
add_action('wp_ajax_weblazem_price_estimate_calc', 'weblazem_ajax_price_estimate_calc');
add_action('wp_ajax_nopriv_weblazem_price_estimate_calc', 'weblazem_ajax_price_estimate_calc');

function weblazem_ajax_price_estimate_lead() {
    check_ajax_referer('weblazem_price_estimator', 'nonce');

    $settings = weblazem_get_price_estimator_settings();
    if ($settings['save_lead'] !== '1') {
        wp_send_json_error(array('message' => 'ذخیره لید غیرفعال است.'), 403);
    }

    $rl = weblazem_growth_rate_limit('price_lead', 8, 600);
    if (is_wp_error($rl)) {
        wp_send_json_error(array('message' => $rl->get_error_message()), 429);
    }

    $name   = isset($_POST['name']) ? sanitize_text_field(wp_unslash($_POST['name'])) : '';
    $mobile = isset($_POST['mobile']) ? sanitize_text_field(wp_unslash($_POST['mobile'])) : '';

    if ($name === '' || mb_strlen($name) < 2) {
        wp_send_json_error(array('message' => 'لطفاً نام خود را وارد کنید.'), 400);
    }

    if (!function_exists('weblazem_is_valid_iran_mobile') || !weblazem_is_valid_iran_mobile($mobile)) {
        wp_send_json_error(array('message' => 'شماره موبایل معتبر نیست.'), 400);
    }

    $mobile = weblazem_normalize_iran_mobile($mobile);

    $addons = isset($_POST['addons']) ? (array) wp_unslash($_POST['addons']) : array();
    $calc   = weblazem_price_estimate_calculate(
        array(
            'site_type' => isset($_POST['site_type']) ? wp_unslash($_POST['site_type']) : '',
            'pages'     => isset($_POST['pages']) ? wp_unslash($_POST['pages']) : '',
            'urgency'   => isset($_POST['urgency']) ? wp_unslash($_POST['urgency']) : '',
            'addons'    => $addons,
        )
    );

    if (is_wp_error($calc)) {
        wp_send_json_error(array('message' => $calc->get_error_message()), 400);
    }

    $post_id = wp_insert_post(
        array(
            'post_type'   => 'price_estimate_lead',
            'post_status' => 'publish',
            'post_title'  => $name . ' — ' . $mobile,
        ),
        true
    );

    if (is_wp_error($post_id) || !$post_id) {
        wp_send_json_error(array('message' => 'خطا در ذخیره اطلاعات. دوباره تلاش کنید.'), 500);
    }

    update_post_meta($post_id, '_pe_name', $name);
    update_post_meta($post_id, '_pe_mobile', $mobile);
    update_post_meta($post_id, '_pe_site_type', $calc['site_type']);
    update_post_meta($post_id, '_pe_pages', $calc['pages']);
    update_post_meta($post_id, '_pe_urgency', $calc['urgency']);
    update_post_meta($post_id, '_pe_addons', $calc['addons']);
    update_post_meta($post_id, '_pe_estimate', $calc['estimate']);
    update_post_meta($post_id, '_pe_min', $calc['min']);
    update_post_meta($post_id, '_pe_max', $calc['max']);
    update_post_meta($post_id, '_pe_payload', $calc);
    update_post_meta($post_id, '_pe_ip', weblazem_growth_client_ip());
    update_post_meta($post_id, '_pe_created_at', current_time('mysql'));

    wp_send_json_success(
        array(
            'message'  => $settings['success_text'],
            'lead_id'  => $post_id,
            'estimate' => $calc,
        )
    );
}
add_action('wp_ajax_weblazem_price_estimate_lead', 'weblazem_ajax_price_estimate_lead');
add_action('wp_ajax_nopriv_weblazem_price_estimate_lead', 'weblazem_ajax_price_estimate_lead');

function weblazem_price_estimate_lead_meta_box() {
    add_meta_box(
        'weblazem_price_estimate_lead_details',
        'جزئیات برآورد',
        'weblazem_price_estimate_lead_meta_render',
        'price_estimate_lead',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'weblazem_price_estimate_lead_meta_box');

function weblazem_price_estimate_lead_meta_render($post) {
    $payload = get_post_meta($post->ID, '_pe_payload', true);
    $name    = get_post_meta($post->ID, '_pe_name', true);
    $mobile  = get_post_meta($post->ID, '_pe_mobile', true);
    $created = get_post_meta($post->ID, '_pe_created_at', true);

    echo '<table class="form-table"><tbody>';
    echo '<tr><th>نام</th><td>' . esc_html($name) . '</td></tr>';
    echo '<tr><th>موبایل</th><td dir="ltr">' . esc_html($mobile) . '</td></tr>';
    echo '<tr><th>زمان</th><td>' . esc_html($created) . '</td></tr>';

    if (is_array($payload)) {
        echo '<tr><th>نوع سایت</th><td>' . esc_html($payload['site_type_label'] ?? '') . '</td></tr>';
        echo '<tr><th>صفحات</th><td>' . esc_html($payload['pages_label'] ?? '') . '</td></tr>';
        echo '<tr><th>فوریت</th><td>' . esc_html($payload['urgency_label'] ?? '') . '</td></tr>';
        echo '<tr><th>افزونه‌ها</th><td>' . esc_html(implode('، ', $payload['addon_labels'] ?? array())) . '</td></tr>';
        echo '<tr><th>برآورد</th><td>' . esc_html($payload['estimate_fmt'] ?? '') . '</td></tr>';
        echo '<tr><th>بازه</th><td>' . esc_html(($payload['min_fmt'] ?? '') . ' — ' . ($payload['max_fmt'] ?? '')) . '</td></tr>';
    }

    echo '</tbody></table>';
}

function weblazem_enqueue_price_estimator_assets() {
    if (!weblazem_is_price_estimator_page()) {
        return;
    }

    $settings = weblazem_get_price_estimator_settings();

    wp_enqueue_style(
        'weblazem-price-estimator',
        get_template_directory_uri() . '/assets/css/price-estimator.css',
        array(),
        '1.0.0'
    );

    wp_enqueue_script(
        'weblazem-price-estimator',
        get_template_directory_uri() . '/assets/js/price-estimator.js',
        array(),
        '1.0.0',
        true
    );

    wp_localize_script(
        'weblazem-price-estimator',
        'weblazemPriceEstimator',
        array(
            'ajaxUrl'      => admin_url('admin-ajax.php'),
            'nonce'        => wp_create_nonce('weblazem_price_estimator'),
            'enabled'      => $settings['enabled'] === '1',
            'saveLead'     => $settings['save_lead'] === '1',
            'successText'  => $settings['success_text'],
            'errorText'    => 'خطایی رخ داد. لطفاً دوباره تلاش کنید.',
            'siteTypes'    => $settings['site_types'],
            'pageTiers'    => $settings['page_tiers'],
            'addons'       => $settings['addons'],
            'urgency'      => $settings['urgency'],
            'rangeSpread'  => (int) $settings['range_spread'],
            'consultUrl'   => $settings['consult_cta_url'] !== '' ? $settings['consult_cta_url'] : (function_exists('weblazem_get_scheduling_page_url') ? weblazem_get_scheduling_page_url() : ''),
            'startUrl'     => $settings['start_project_cta_url'] !== '' ? $settings['start_project_cta_url'] : (function_exists('weblazem_get_start_project_page_url') ? weblazem_get_start_project_page_url() : ''),
        )
    );
}
add_action('wp_enqueue_scripts', 'weblazem_enqueue_price_estimator_assets', 30);
