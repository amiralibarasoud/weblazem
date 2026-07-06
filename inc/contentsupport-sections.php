<?php
/**
 * Content production & support page — section registry and portfolio helpers.
 */

function weblazem_get_contentsupport_sections_config() {
    return array(
        'hero'       => 'هیرو — تولید محتوا و پشتیبانی',
        'portfolio'  => 'نمونه‌کارها (Success Stories)',
        'customers'  => 'مشتریان و شمارنده',
        'splits'     => 'بخش‌های دو ستونه',
        'process'    => 'فرآیند کار و CSAT',
        'advantages' => 'مزایا و ویژگی‌ها',
        'faq'        => 'FAQ و تماس',
    );
}

function weblazem_is_contentsupport_section_enabled($section) {
    $sections = weblazem_get_contentsupport_sections_config();
    if (!isset($sections[$section])) {
        return true;
    }
    return get_option('weblazem_contentsupport_section_' . $section . '_enabled', '1') === '1';
}

function weblazem_contentsupport_option($key, $default = '') {
    return weblazem_service_option('contentsupport', $key, $default);
}

function weblazem_ensure_contentsupport_section_defaults() {
    foreach (weblazem_get_contentsupport_sections_config() as $key => $label) {
        $option_key = 'weblazem_contentsupport_section_' . $key . '_enabled';
        if (get_option($option_key) === false) {
            update_option($option_key, '1');
        }
    }
}
add_action('init', 'weblazem_ensure_contentsupport_section_defaults', 13);

function weblazem_contentsupport_showcase_colors() {
    return array('#7c3aed', '#db2777', '#2563eb', '#0d9488', '#4f1c61', '#ea580c', '#1d4ed8', '#0891b2');
}

function weblazem_get_default_contentsupport_portfolio_tabs() {
    return array(
        array('key' => 'all', 'title' => 'همه پروژه‌ها', 'category' => ''),
        array('key' => 'content', 'title' => 'تولید محتوا', 'category' => 'content'),
        array('key' => 'support', 'title' => 'پشتیبانی', 'category' => 'support'),
    );
}

function weblazem_get_contentsupport_portfolio_tabs() {
    $tabs = get_option('weblazem_contentsupport_portfolio_tabs');
    if (!is_array($tabs) || empty($tabs)) {
        return weblazem_get_default_contentsupport_portfolio_tabs();
    }
    $normalized = array();
    foreach ($tabs as $tab) {
        if (empty($tab['title'])) {
            continue;
        }
        $normalized[] = array(
            'key'      => !empty($tab['key']) ? sanitize_key($tab['key']) : sanitize_title($tab['title']),
            'title'    => sanitize_text_field($tab['title']),
            'category' => !empty($tab['category']) ? sanitize_title($tab['category']) : '',
        );
    }
    return !empty($normalized) ? $normalized : weblazem_get_default_contentsupport_portfolio_tabs();
}

function weblazem_get_contentsupport_portfolio_items() {
    $count = max(4, (int) weblazem_contentsupport_option('portfolio_count', '12'));
    $query = new WP_Query(array(
        'post_type'      => 'portfolio',
        'posts_per_page' => $count,
        'post_status'    => 'publish',
        'orderby'        => 'date',
        'order'          => 'DESC',
    ));

    $items = array();
    $colors = weblazem_contentsupport_showcase_colors();
    $i = 0;

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $terms = wp_get_post_terms(get_the_ID(), 'portfolio_category', array('fields' => 'slugs'));
            $logo  = get_post_meta(get_the_ID(), '_weblazem_portfolio_client', true);

            $items[] = array(
                'title'     => get_post_meta(get_the_ID(), '_weblazem_portfolio_subtitle', true) ?: get_the_title(),
                'image'     => get_the_post_thumbnail_url(get_the_ID(), 'large'),
                'logo'      => '',
                'logo_text' => $logo ?: get_the_title(),
                'link'      => get_permalink(),
                'tag'       => '',
                'color'     => $colors[$i % count($colors)],
                'category'  => is_array($terms) ? implode(' ', $terms) : '',
            );
            $i++;
        }
        wp_reset_postdata();
    }

    $manual = get_option('weblazem_contentsupport_portfolio_items', array());
    if (is_array($manual) && !empty($manual)) {
        foreach ($manual as $item) {
            if (empty($item['title']) && empty($item['image'])) {
                continue;
            }
            $items[] = array(
                'title'     => $item['title'] ?? '',
                'image'     => $item['image'] ?? '',
                'logo'      => $item['logo'] ?? '',
                'logo_text' => $item['logo_text'] ?? ($item['title'] ?? ''),
                'link'      => $item['link'] ?? '#',
                'tag'       => $item['tag'] ?? '',
                'color'     => !empty($item['color']) ? $item['color'] : $colors[$i % count($colors)],
                'category'  => $item['category'] ?? '',
            );
            $i++;
        }
    }

    return $items;
}
