<?php
/**
 * About Us page — section registry.
 */

require_once get_template_directory() . '/inc/aboutus-defaults.php';

function weblazem_get_aboutus_sections_config() {
    return array(
        'hero'     => 'هیرو — معرفی درباره ما',
        'journey'  => 'سفر ما در گذر زمان (تایم‌لاین)',
        'ceo'      => 'مدیرعامل',
        'team'     => 'تیم و همکاری',
        'services' => 'کارت‌های خدمات',
        'consult'  => 'مشاوره و درخواست پروژه',
    );
}

function weblazem_is_aboutus_section_enabled($section) {
    $sections = weblazem_get_aboutus_sections_config();
    if (!isset($sections[$section])) {
        return true;
    }
    return get_option('weblazem_aboutus_section_' . $section . '_enabled', '1') === '1';
}

function weblazem_aboutus_option($key, $default = '') {
    return weblazem_service_option('aboutus', $key, $default);
}

function weblazem_ensure_aboutus_section_defaults() {
    foreach (weblazem_get_aboutus_sections_config() as $key => $label) {
        $option_key = 'weblazem_aboutus_section_' . $key . '_enabled';
        if (get_option($option_key) === false) {
            update_option($option_key, '1');
        }
    }
}
add_action('init', 'weblazem_ensure_aboutus_section_defaults', 13);
