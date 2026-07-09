<?php
/**
 * Pricing page — defaults.
 */

function weblazem_pricing_uri($path) {
    return get_template_directory_uri() . '/assets/images/pricing/' . ltrim($path, '/');
}

function weblazem_pricing_defaults() {
    $lorem = 'لورم ایپسوم متن ساختگی با تولید سادگی نامفهوم از صنعت چاپ و با استفاده از طراحان گرافیک است. چاپگرها و متون بلکه روزنامه و مجله در ستون و سطرآنچنان که لازم است.';

    return array(
        'hero_icon'                    => weblazem_pricing_uri('hero-services.svg'),
        'hero_title'                   => 'خدمات و تعرفه ها',
        'hero_text'                    => $lorem,
        'service_tariffs_title'        => 'تعرفه ها',
        'service_tariffs_intro'        => $lorem,
        'webdesign_plans_title'        => 'تعرفه طراحی سایت',
        'webdesign_plans_price_label'  => 'قیمت',
        'consult_title'                => 'خدمات مشاوره',
        'consult_text'                 => $lorem . ' ' . $lorem,
        'consult_btn_text'             => 'مشاوره رایگان',
        'consult_btn_url'              => '',
        'consult_btn_modal'            => '1',
    );
}

function weblazem_pricing_page_link($slug, $fallback = '#') {
    $page = get_page_by_path($slug);
    if ($page && $page->post_status === 'publish') {
        $url = get_permalink($page);
        if ($url) {
            return $url;
        }
    }
    return $fallback;
}

function weblazem_get_default_pricing_categories() {
    return array(
        array(
            'title' => 'طراحی سایت خدماتی',
            'url'   => weblazem_pricing_page_link('tarahi-site-khedmati'),
        ),
        array(
            'title' => 'طراحی سایت فروشگاهی',
            'url'   => weblazem_pricing_page_link('tarahi-site-forooshgahi'),
        ),
        array(
            'title' => 'طراحی سایت شخصی',
            'url'   => weblazem_pricing_page_link('tarahi-site-shakhsi'),
        ),
        array(
            'title' => 'طراحی سایت شرکتی',
            'url'   => weblazem_pricing_page_link('tarahi-site-sherkati'),
        ),
    );
}

function weblazem_get_pricing_categories() {
    $items = get_option('weblazem_pricing_categories');
    if (!is_array($items) || empty($items)) {
        return weblazem_get_default_pricing_categories();
    }
    return $items;
}

function weblazem_get_default_pricing_service_tariffs() {
    $lorem = 'لورم ایپسوم متن ساختگی با تولید سادگی نامفهوم از صنعت چاپ و با استفاده از طراحان گرافیک است.';
    $image = weblazem_pricing_uri('tariff-card.svg');

    $seo_url = function_exists('weblazem_get_seo_page_url') ? weblazem_get_seo_page_url() : '#';
    $content_url = function_exists('weblazem_get_contentsupport_page_url') ? weblazem_get_contentsupport_page_url() : '#';

    return array(
        array(
            'title'        => 'تعرفه ی پشتیبانی',
            'description'  => $lorem,
            'image'        => $image,
            'button_text'  => 'نمایش بیشتر',
            'button_url'   => $content_url,
            'button_modal' => '0',
        ),
        array(
            'title'        => 'تعرفه ی سئو',
            'description'  => $lorem,
            'image'        => $image,
            'button_text'  => 'نمایش بیشتر',
            'button_url'   => $seo_url,
            'button_modal' => '0',
        ),
        array(
            'title'        => 'تعرفه ی تولید محتوا',
            'description'  => $lorem,
            'image'        => $image,
            'button_text'  => 'نمایش بیشتر',
            'button_url'   => $content_url,
            'button_modal' => '0',
        ),
    );
}

function weblazem_get_pricing_service_tariffs() {
    $items = get_option('weblazem_pricing_service_tariffs');
    if (!is_array($items) || empty($items)) {
        return weblazem_get_default_pricing_service_tariffs();
    }
    return $items;
}

function weblazem_get_default_pricing_webdesign_plans() {
    $feature  = 'لورم ایپسوم متن ساختگی';
    $features = array_fill(0, 5, $feature);
    $plans    = array();

    for ($i = 0; $i < 4; $i++) {
        $plans[] = array(
            'title'        => 'پلن طلایی (ماهانه)',
            'price'        => '۲.۰۰۰.۰۰۰ تومان',
            'features'     => $features,
            'button_text'  => 'مشاوره رایگان',
            'button_modal' => '1',
            'button_url'   => '',
        );
    }

    return $plans;
}

function weblazem_get_pricing_webdesign_plans() {
    $plans = get_option('weblazem_pricing_webdesign_plans');
    if (!is_array($plans) || empty($plans)) {
        return weblazem_get_default_pricing_webdesign_plans();
    }
    return $plans;
}

function weblazem_ensure_pricing_defaults() {
    foreach (weblazem_pricing_defaults() as $key => $value) {
        $option_key = 'weblazem_pricing_' . $key;
        if (get_option($option_key) === false) {
            update_option($option_key, $value);
        }
    }

    $arrays = array(
        'weblazem_pricing_categories'       => 'weblazem_get_default_pricing_categories',
        'weblazem_pricing_service_tariffs'  => 'weblazem_get_default_pricing_service_tariffs',
        'weblazem_pricing_webdesign_plans'  => 'weblazem_get_default_pricing_webdesign_plans',
    );

    foreach ($arrays as $option_key => $callback) {
        if (get_option($option_key) === false) {
            update_option($option_key, call_user_func($callback));
        }
    }
}
add_action('init', 'weblazem_ensure_pricing_defaults', 14);
