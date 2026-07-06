<?php
/**
 * Blog archive page — section registry.
 */

function weblazem_get_blogarchive_sections_config() {
    return array(
        'hero'  => 'هیرو — آرشیو بلاگ',
        'posts' => 'لیست مقالات',
    );
}

function weblazem_is_blogarchive_section_enabled($section) {
    $sections = weblazem_get_blogarchive_sections_config();
    if (!isset($sections[$section])) {
        return true;
    }
    return get_option('weblazem_blogarchive_section_' . $section . '_enabled', '1') === '1';
}

function weblazem_blogarchive_option($key, $default = '') {
    return get_option('weblazem_blogarchive_' . $key, $default);
}

function weblazem_ensure_blogarchive_section_defaults() {
    foreach (weblazem_get_blogarchive_sections_config() as $key => $label) {
        $option_key = 'weblazem_blogarchive_section_' . $key . '_enabled';
        if (get_option($option_key) === false) {
            update_option($option_key, '1');
        }
    }
}
add_action('init', 'weblazem_ensure_blogarchive_section_defaults', 13);
