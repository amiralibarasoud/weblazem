<?php
/**
 * Pricing page — section registry.
 */

function weblazem_get_pricing_sections_config() {
    return array(
        'hero'             => 'هیرو — خدمات و تعرفه‌ها',
        'categories'       => 'دسته‌بندی خدمات (۴ کارت)',
        'service_tariffs'  => 'تعرفه خدمات (پشتیبانی / سئو / محتوا)',
        'webdesign_plans'  => 'تعرفه طراحی سایت (پلن‌های قیمت)',
        'consult'          => 'بخش مشاوره پایانی',
    );
}

function weblazem_is_pricing_section_enabled($section) {
    $sections = weblazem_get_pricing_sections_config();
    if (!isset($sections[$section])) {
        return true;
    }
    return get_option('weblazem_pricing_section_' . $section . '_enabled', '1') === '1';
}

function weblazem_pricing_option($key, $default = '') {
    return weblazem_service_option('pricing', $key, $default);
}

function weblazem_ensure_pricing_section_defaults() {
    foreach (weblazem_get_pricing_sections_config() as $key => $label) {
        $option_key = 'weblazem_pricing_section_' . $key . '_enabled';
        if (get_option($option_key) === false) {
            update_option($option_key, '1');
        }
    }
}
add_action('init', 'weblazem_ensure_pricing_section_defaults', 13);
