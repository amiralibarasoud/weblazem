<?php
/**
 * Plan comparator — interactive filterable website plan comparison.
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('weblazem_growth_ensure_page')) {
    require_once get_template_directory() . '/inc/growth-tools-shared.php';
}

define('WEBLAZEM_PLAN_COMPARATOR_SLUG', 'moghayese-plan');
define('WEBLAZEM_PLAN_COMPARATOR_TEMPLATE', 'plan-comparator-template.php');
define('WEBLAZEM_PLAN_COMPARATOR_OPTION', 'weblazem_plan_comparator_page_id');

function weblazem_plan_comparator_category_labels() {
    return array(
        'corporate' => 'شرکتی',
        'shop'      => 'فروشگاهی',
        'landing'   => 'لندینگ',
        'custom'    => 'سفارشی',
    );
}

function weblazem_plan_comparator_default_plans() {
    $start_url = function_exists('weblazem_get_start_project_page_url')
        ? weblazem_get_start_project_page_url()
        : home_url('/shoro-proje/');
    $consult_url = function_exists('weblazem_get_scheduling_page_url')
        ? weblazem_get_scheduling_page_url()
        : home_url('/rezerve-moshavere/');

    return array(
        array(
            'id'          => 'starter-corporate',
            'title'       => 'پلن شرکتی پایه',
            'price'       => 18000000,
            'badge'       => 'شروع سریع',
            'features'    => array(
                'طراحی اختصاصی تا ۵ صفحه',
                'فرم تماس و نقشه',
                'بهینه‌سازی موبایل',
                'آموزش مدیریت محتوا',
            ),
            'category'    => 'corporate',
            'has_support' => '1',
            'has_seo'     => '0',
            'max_pages'   => 5,
            'recommended' => '0',
            'cta_text'    => 'شروع پروژه',
            'cta_url'     => $start_url,
        ),
        array(
            'id'          => 'business-corporate',
            'title'       => 'پلن شرکتی حرفه‌ای',
            'price'       => 32000000,
            'badge'       => 'پیشنهادی',
            'features'    => array(
                'تا ۱۲ صفحه + بلاگ',
                'سئوی فنی اولیه',
                '۳ ماه پشتیبانی',
                'سرعت و امنیت پایه',
                'اتصال آنالیتیکس',
            ),
            'category'    => 'corporate',
            'has_support' => '1',
            'has_seo'     => '1',
            'max_pages'   => 12,
            'recommended' => '1',
            'cta_text'    => 'شروع پروژه',
            'cta_url'     => $start_url,
        ),
        array(
            'id'          => 'shop-standard',
            'title'       => 'فروشگاه استاندارد',
            'price'       => 45000000,
            'badge'       => 'ووکامرس',
            'features'    => array(
                'فروشگاه ووکامرس کامل',
                'درگاه پرداخت',
                'تا ۲۰۰ محصول اولیه',
                'سئوی فروشگاهی',
                '۶ ماه پشتیبانی',
            ),
            'category'    => 'shop',
            'has_support' => '1',
            'has_seo'     => '1',
            'max_pages'   => 20,
            'recommended' => '0',
            'cta_text'    => 'مشاوره فروشگاه',
            'cta_url'     => $consult_url,
        ),
        array(
            'id'          => 'landing-campaign',
            'title'       => 'لندینگ کمپین',
            'price'       => 12000000,
            'badge'       => 'تبدیل‌محور',
            'features'    => array(
                'یک صفحه تمرکز‌شده',
                'فرم لید و CTA قوی',
                'آماده تبلیغات',
                'تحویل سریع',
            ),
            'category'    => 'landing',
            'has_support' => '0',
            'has_seo'     => '1',
            'max_pages'   => 1,
            'recommended' => '0',
            'cta_text'    => 'شروع پروژه',
            'cta_url'     => $start_url,
        ),
    );
}

function weblazem_plan_comparator_defaults() {
    return array(
        'title'           => 'مقایسه پلن‌های طراحی سایت',
        'subtitle'        => 'بر اساس بودجه، دسته و امکانات فیلتر کنید و بهترین گزینه را انتخاب کنید.',
        'empty_text'      => 'پلنی با این فیلترها پیدا نشد. فیلترها را تغییر دهید.',
        'budget_label'    => 'حداکثر بودجه',
        'support_label'   => 'فقط با پشتیبانی',
        'seo_label'       => 'فقط با سئو',
        'all_categories'  => 'همه دسته‌ها',
        'compare_title'   => 'جدول مقایسه',
        'price_suffix'    => 'تومان',
        'plans'           => weblazem_plan_comparator_default_plans(),
    );
}

function weblazem_plan_comparator_sanitize_plan($plan) {
    $cats = array_keys(weblazem_plan_comparator_category_labels());
    $id   = sanitize_key($plan['id'] ?? '');
    if ($id === '') {
        $id = 'plan-' . substr(md5(wp_json_encode($plan) . wp_rand()), 0, 8);
    }

    $features_raw = $plan['features'] ?? array();
    if (is_string($features_raw)) {
        $features_raw = preg_split('/\r\n|\r|\n/', $features_raw);
    }
    $features = array();
    if (is_array($features_raw)) {
        foreach ($features_raw as $f) {
            $f = sanitize_text_field($f);
            if ($f !== '') {
                $features[] = $f;
            }
        }
    }

    $category = sanitize_key($plan['category'] ?? 'corporate');
    if (!in_array($category, $cats, true)) {
        $category = 'corporate';
    }

    return array(
        'id'          => $id,
        'title'       => sanitize_text_field($plan['title'] ?? ''),
        'price'       => max(0, (int) ($plan['price'] ?? 0)),
        'badge'       => sanitize_text_field($plan['badge'] ?? ''),
        'features'    => $features,
        'category'    => $category,
        'has_support' => (!empty($plan['has_support']) && (string) $plan['has_support'] === '1') ? '1' : '0',
        'has_seo'     => (!empty($plan['has_seo']) && (string) $plan['has_seo'] === '1') ? '1' : '0',
        'max_pages'   => max(0, (int) ($plan['max_pages'] ?? 0)),
        'recommended' => (!empty($plan['recommended']) && (string) $plan['recommended'] === '1') ? '1' : '0',
        'cta_text'    => sanitize_text_field($plan['cta_text'] ?? 'شروع پروژه'),
        'cta_url'     => esc_url_raw($plan['cta_url'] ?? ''),
    );
}

function weblazem_plan_comparator_sanitize_settings($input) {
    $defaults = weblazem_plan_comparator_defaults();
    $out      = $defaults;

    if (!is_array($input)) {
        return $out;
    }

    $out['title']          = sanitize_text_field($input['title'] ?? $defaults['title']);
    $out['subtitle']       = sanitize_textarea_field($input['subtitle'] ?? $defaults['subtitle']);
    $out['empty_text']     = sanitize_textarea_field($input['empty_text'] ?? $defaults['empty_text']);
    $out['budget_label']   = sanitize_text_field($input['budget_label'] ?? $defaults['budget_label']);
    $out['support_label']  = sanitize_text_field($input['support_label'] ?? $defaults['support_label']);
    $out['seo_label']      = sanitize_text_field($input['seo_label'] ?? $defaults['seo_label']);
    $out['all_categories'] = sanitize_text_field($input['all_categories'] ?? $defaults['all_categories']);
    $out['compare_title']  = sanitize_text_field($input['compare_title'] ?? $defaults['compare_title']);
    $out['price_suffix']   = sanitize_text_field($input['price_suffix'] ?? $defaults['price_suffix']);

    $plans = array();
    if (!empty($input['plans']) && is_array($input['plans'])) {
        foreach ($input['plans'] as $plan) {
            if (!is_array($plan)) {
                continue;
            }
            $clean = weblazem_plan_comparator_sanitize_plan($plan);
            if ($clean['title'] === '') {
                continue;
            }
            $plans[] = $clean;
        }
    }

    $out['plans'] = !empty($plans) ? $plans : $defaults['plans'];
    return $out;
}

function weblazem_get_plan_comparator_settings() {
    $defaults = weblazem_plan_comparator_defaults();
    $saved    = get_option('weblazem_plan_comparator_settings', array());
    if (!is_array($saved)) {
        $saved = array();
    }
    $settings = wp_parse_args($saved, $defaults);
    if (empty($settings['plans']) || !is_array($settings['plans'])) {
        $settings['plans'] = $defaults['plans'];
    } else {
        $clean = array();
        foreach ($settings['plans'] as $plan) {
            if (is_array($plan)) {
                $clean[] = weblazem_plan_comparator_sanitize_plan($plan);
            }
        }
        $settings['plans'] = !empty($clean) ? $clean : $defaults['plans'];
    }
    return $settings;
}

function weblazem_ensure_plan_comparator_defaults() {
    if (get_option('weblazem_plan_comparator_settings') === false) {
        update_option('weblazem_plan_comparator_settings', weblazem_plan_comparator_defaults());
    }
}
add_action('init', 'weblazem_ensure_plan_comparator_defaults', 12);

function weblazem_get_plan_comparator_page_id() {
    return weblazem_growth_get_page_id(WEBLAZEM_PLAN_COMPARATOR_OPTION, WEBLAZEM_PLAN_COMPARATOR_SLUG);
}

function weblazem_get_plan_comparator_page_url() {
    return weblazem_growth_get_page_url(WEBLAZEM_PLAN_COMPARATOR_OPTION, WEBLAZEM_PLAN_COMPARATOR_SLUG);
}

function weblazem_is_plan_comparator_page() {
    return weblazem_growth_is_page(
        WEBLAZEM_PLAN_COMPARATOR_TEMPLATE,
        WEBLAZEM_PLAN_COMPARATOR_OPTION,
        WEBLAZEM_PLAN_COMPARATOR_SLUG
    );
}

function weblazem_ensure_plan_comparator_page() {
    weblazem_growth_ensure_page(
        array(
            'slug'     => WEBLAZEM_PLAN_COMPARATOR_SLUG,
            'template' => WEBLAZEM_PLAN_COMPARATOR_TEMPLATE,
            'title'    => 'مقایسه پلن‌ها',
            'option'   => WEBLAZEM_PLAN_COMPARATOR_OPTION,
        )
    );
}
add_action('init', 'weblazem_ensure_plan_comparator_page', 39);

function weblazem_register_plan_comparator_settings() {
    register_setting(
        'weblazem_plan_comparator_group',
        'weblazem_plan_comparator_settings',
        array(
            'type'              => 'array',
            'sanitize_callback' => 'weblazem_plan_comparator_sanitize_settings',
            'default'           => weblazem_plan_comparator_defaults(),
        )
    );
}
add_action('admin_init', 'weblazem_register_plan_comparator_settings');

function weblazem_plan_comparator_admin_menu() {
    add_submenu_page(
        'weblazem-theme-options',
        'مقایسه پلن‌ها',
        'مقایسه پلن‌ها',
        'manage_options',
        'weblazem-plan-comparator-options',
        'weblazem_plan_comparator_options_display'
    );
}
add_action('admin_menu', 'weblazem_plan_comparator_admin_menu', 42);

function weblazem_plan_comparator_options_display() {
    if (!current_user_can('manage_options')) {
        return;
    }

    $s        = weblazem_get_plan_comparator_settings();
    $cats     = weblazem_plan_comparator_category_labels();
    $page_url = weblazem_get_plan_comparator_page_url();
    $plans    = $s['plans'];
    while (count($plans) < 4) {
        $plans[] = array(
            'id'          => '',
            'title'       => '',
            'price'       => 0,
            'badge'       => '',
            'features'    => array(),
            'category'    => 'corporate',
            'has_support' => '0',
            'has_seo'     => '0',
            'max_pages'   => 0,
            'recommended' => '0',
            'cta_text'    => 'شروع پروژه',
            'cta_url'     => '',
        );
    }
    ?>
    <div class="wrap" dir="rtl">
        <h1>تنظیمات مقایسه پلن‌ها</h1>
        <?php if ($page_url) : ?>
            <p>صفحه: <a href="<?php echo esc_url($page_url); ?>" target="_blank" rel="noopener"><?php echo esc_html($page_url); ?></a></p>
        <?php endif; ?>

        <form method="post" action="options.php">
            <?php settings_fields('weblazem_plan_comparator_group'); ?>
            <table class="form-table">
                <tr><th>عنوان</th><td><input type="text" class="large-text" name="weblazem_plan_comparator_settings[title]" value="<?php echo esc_attr($s['title']); ?>" /></td></tr>
                <tr><th>زیرعنوان</th><td><textarea class="large-text" rows="2" name="weblazem_plan_comparator_settings[subtitle]"><?php echo esc_textarea($s['subtitle']); ?></textarea></td></tr>
                <tr><th>متن خالی بودن فیلتر</th><td><textarea class="large-text" rows="2" name="weblazem_plan_comparator_settings[empty_text]"><?php echo esc_textarea($s['empty_text']); ?></textarea></td></tr>
                <tr><th>برچسب بودجه</th><td><input type="text" class="regular-text" name="weblazem_plan_comparator_settings[budget_label]" value="<?php echo esc_attr($s['budget_label']); ?>" /></td></tr>
                <tr><th>برچسب پشتیبانی / سئو</th>
                    <td>
                        <input type="text" class="regular-text" name="weblazem_plan_comparator_settings[support_label]" value="<?php echo esc_attr($s['support_label']); ?>" />
                        <input type="text" class="regular-text" name="weblazem_plan_comparator_settings[seo_label]" value="<?php echo esc_attr($s['seo_label']); ?>" />
                    </td>
                </tr>
                <tr><th>عنوان جدول</th><td><input type="text" class="large-text" name="weblazem_plan_comparator_settings[compare_title]" value="<?php echo esc_attr($s['compare_title']); ?>" /></td></tr>
            </table>

            <h2>پلن‌ها</h2>
            <?php foreach ($plans as $i => $plan) : ?>
                <div style="border:1px solid #ccd0d4;padding:16px;margin:0 0 16px;background:#fff;">
                    <h3>پلن <?php echo (int) ($i + 1); ?></h3>
                    <input type="hidden" name="weblazem_plan_comparator_settings[plans][<?php echo (int) $i; ?>][id]" value="<?php echo esc_attr($plan['id']); ?>" />
                    <input type="hidden" name="weblazem_plan_comparator_settings[plans][<?php echo (int) $i; ?>][has_support]" value="0" />
                    <input type="hidden" name="weblazem_plan_comparator_settings[plans][<?php echo (int) $i; ?>][has_seo]" value="0" />
                    <input type="hidden" name="weblazem_plan_comparator_settings[plans][<?php echo (int) $i; ?>][recommended]" value="0" />
                    <p>
                        <label>عنوان<br />
                            <input type="text" class="regular-text" name="weblazem_plan_comparator_settings[plans][<?php echo (int) $i; ?>][title]" value="<?php echo esc_attr($plan['title']); ?>" />
                        </label>
                    </p>
                    <p>
                        <label>قیمت (تومان)<br />
                            <input type="number" dir="ltr" name="weblazem_plan_comparator_settings[plans][<?php echo (int) $i; ?>][price]" value="<?php echo esc_attr((string) (int) $plan['price']); ?>" min="0" step="1000" />
                        </label>
                        &nbsp;
                        <label>بج<br />
                            <input type="text" name="weblazem_plan_comparator_settings[plans][<?php echo (int) $i; ?>][badge]" value="<?php echo esc_attr($plan['badge']); ?>" />
                        </label>
                    </p>
                    <p>
                        <label>دسته<br />
                            <select name="weblazem_plan_comparator_settings[plans][<?php echo (int) $i; ?>][category]">
                                <?php foreach ($cats as $ck => $cl) : ?>
                                    <option value="<?php echo esc_attr($ck); ?>" <?php selected($plan['category'], $ck); ?>><?php echo esc_html($cl); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </label>
                        &nbsp;
                        <label>حداکثر صفحات
                            <input type="number" dir="ltr" name="weblazem_plan_comparator_settings[plans][<?php echo (int) $i; ?>][max_pages]" value="<?php echo esc_attr((string) (int) $plan['max_pages']); ?>" min="0" style="width:80px;" />
                        </label>
                    </p>
                    <p>
                        <label><input type="checkbox" name="weblazem_plan_comparator_settings[plans][<?php echo (int) $i; ?>][has_support]" value="1" <?php checked($plan['has_support'], '1'); ?> /> پشتیبانی</label>
                        &nbsp;
                        <label><input type="checkbox" name="weblazem_plan_comparator_settings[plans][<?php echo (int) $i; ?>][has_seo]" value="1" <?php checked($plan['has_seo'], '1'); ?> /> سئو</label>
                        &nbsp;
                        <label><input type="checkbox" name="weblazem_plan_comparator_settings[plans][<?php echo (int) $i; ?>][recommended]" value="1" <?php checked($plan['recommended'], '1'); ?> /> پیشنهادی</label>
                    </p>
                    <p>
                        <label>ویژگی‌ها (هر خط یک مورد)<br />
                            <textarea class="large-text" rows="4" name="weblazem_plan_comparator_settings[plans][<?php echo (int) $i; ?>][features]"><?php echo esc_textarea(implode("\n", (array) $plan['features'])); ?></textarea>
                        </label>
                    </p>
                    <p>
                        <label>متن CTA
                            <input type="text" name="weblazem_plan_comparator_settings[plans][<?php echo (int) $i; ?>][cta_text]" value="<?php echo esc_attr($plan['cta_text']); ?>" />
                        </label>
                        <label>لینک CTA
                            <input type="url" class="regular-text" dir="ltr" name="weblazem_plan_comparator_settings[plans][<?php echo (int) $i; ?>][cta_url]" value="<?php echo esc_attr($plan['cta_url']); ?>" />
                        </label>
                    </p>
                </div>
            <?php endforeach; ?>

            <?php submit_button('ذخیره تنظیمات'); ?>
        </form>
    </div>
    <?php
}

function weblazem_plan_comparator_plans_for_js($plans) {
    $cats = weblazem_plan_comparator_category_labels();
    $out  = array();
    foreach ($plans as $plan) {
        $out[] = array(
            'id'            => $plan['id'],
            'title'         => $plan['title'],
            'price'         => (int) $plan['price'],
            'priceFmt'      => weblazem_growth_format_toman((int) $plan['price']),
            'badge'         => $plan['badge'],
            'features'      => array_values((array) $plan['features']),
            'category'      => $plan['category'],
            'categoryLabel' => isset($cats[$plan['category']]) ? $cats[$plan['category']] : $plan['category'],
            'hasSupport'    => $plan['has_support'] === '1',
            'hasSeo'        => $plan['has_seo'] === '1',
            'maxPages'      => (int) $plan['max_pages'],
            'recommended'   => $plan['recommended'] === '1',
            'ctaText'       => $plan['cta_text'],
            'ctaUrl'        => $plan['cta_url'],
        );
    }
    return $out;
}

function weblazem_enqueue_plan_comparator_assets() {
    if (!weblazem_is_plan_comparator_page()) {
        return;
    }

    $s   = weblazem_get_plan_comparator_settings();
    $ver = '1.0.0';

    wp_enqueue_style(
        'weblazem-plan-comparator',
        get_template_directory_uri() . '/assets/css/plan-comparator.css',
        array(),
        $ver
    );
    wp_enqueue_script(
        'weblazem-plan-comparator',
        get_template_directory_uri() . '/assets/js/plan-comparator.js',
        array(),
        $ver,
        true
    );

    $prices = array_map(
        function ($p) {
            return (int) $p['price'];
        },
        $s['plans']
    );
    $max_price = !empty($prices) ? max($prices) : 50000000;
    $min_price = !empty($prices) ? min($prices) : 0;

    wp_localize_script(
        'weblazem-plan-comparator',
        'weblazemPlanComparator',
        array(
            'plans'      => weblazem_plan_comparator_plans_for_js($s['plans']),
            'categories' => weblazem_plan_comparator_category_labels(),
            'emptyText'  => $s['empty_text'],
            'budgetMin'  => $min_price,
            'budgetMax'  => $max_price,
            'i18n'       => array(
                'yes'      => 'دارد',
                'no'       => 'ندارد',
                'pages'    => 'صفحه',
                'all'      => $s['all_categories'],
                'support'  => $s['support_label'],
                'seo'      => $s['seo_label'],
                'budget'   => $s['budget_label'],
            ),
        )
    );
}
add_action('wp_enqueue_scripts', 'weblazem_enqueue_plan_comparator_assets', 30);
