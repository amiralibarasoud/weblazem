<?php
/**
 * SEO page — section registry.
 */

function weblazem_get_seo_sections_config() {
    return array(
        'hero'       => 'هیرو — سئو و بازاریابی دیجیتال',
        'clients'    => 'مشتریان و اعتماد',
        'splits'     => 'بخش‌های دو ستونه (استراتژی / مشاوره / ماهانه)',
        'process'    => 'فرآیند کار و CSAT',
        'advantages' => 'مزایا و ویژگی‌ها',
        'tariffs'    => 'تعرفه‌ها (پلن‌های سئو)',
        'faq'        => 'FAQ و تماس',
    );
}

function weblazem_is_seo_section_enabled($section) {
    $sections = weblazem_get_seo_sections_config();
    if (!isset($sections[$section])) {
        return true;
    }
    return get_option('weblazem_seo_section_' . $section . '_enabled', '1') === '1';
}

function weblazem_seo_option($key, $default = '') {
    return weblazem_service_option('seo', $key, $default);
}

function weblazem_ensure_seo_section_defaults() {
    foreach (weblazem_get_seo_sections_config() as $key => $label) {
        $option_key = 'weblazem_seo_section_' . $key . '_enabled';
        if (get_option($option_key) === false) {
            update_option($option_key, '1');
        }
    }
}
add_action('init', 'weblazem_ensure_seo_section_defaults', 13);
