<?php
/**
 * Website design page — section registry and helpers.
 */

function weblazem_get_webdesign_sections_config() {
    return array(
        'hero'       => 'هیرو — طراحی وب‌سایت',
        'portfolio'  => 'نمونه‌کارها (Success Stories)',
        'customers'  => 'مشتریان و شمارنده',
        'splits'     => 'بخش‌های دو ستونه (شرکتی / فروشگاه)',
        'process'    => 'فرآیند کار و CSAT',
        'advantages' => 'مزایا و ویژگی‌ها',
        'faq'        => 'FAQ و تماس',
    );
}

function weblazem_is_webdesign_section_enabled($section) {
    $sections = weblazem_get_webdesign_sections_config();

    if (!isset($sections[$section])) {
        return true;
    }

    return get_option('weblazem_webdesign_section_' . $section . '_enabled', '1') === '1';
}

function weblazem_webdesign_option($key, $default = '') {
    return get_option('weblazem_webdesign_' . $key, $default);
}

function weblazem_ensure_webdesign_section_defaults() {
    foreach (weblazem_get_webdesign_sections_config() as $key => $label) {
        $option_key = 'weblazem_webdesign_section_' . $key . '_enabled';
        if (get_option($option_key) === false) {
            update_option($option_key, '1');
        }
    }
}
add_action('init', 'weblazem_ensure_webdesign_section_defaults', 13);

function weblazem_webdesign_showcase_colors() {
    return array(
        '#0d9488',
        '#c4a35a',
        '#1e3a5f',
        '#c45c26',
        '#1d4ed8',
        '#b91c1c',
        '#6d28d9',
        '#8b5cf6',
    );
}

function weblazem_render_webdesign_calligraphy($image_key, $text_key, $class = '') {
    $image = weblazem_webdesign_option($image_key, '');
    $text  = weblazem_webdesign_option($text_key, '');

    if (!empty($image)) {
        echo '<img src="' . esc_url($image) . '" alt="" class="webdesign-calligraphy-img ' . esc_attr($class) . '" />';
        return;
    }

    if (!empty($text)) {
        echo '<p class="webdesign-calligraphy-text ' . esc_attr($class) . '">' . wp_kses_post($text) . '</p>';
    }
}

function weblazem_get_default_webdesign_portfolio_tabs() {
    return array(
        array('key' => 'all', 'title' => 'همه نمونه‌کارها', 'category' => ''),
        array('key' => 'sherkati', 'title' => 'سایت شرکتی', 'category' => 'site-sherkati'),
        array('key' => 'foroushgahee', 'title' => 'فروشگاه اینترنتی', 'category' => 'foroushgahee-interneti'),
    );
}

function weblazem_get_webdesign_portfolio_tabs() {
    $tabs = get_option('weblazem_webdesign_portfolio_tabs');

    if (!is_array($tabs) || empty($tabs)) {
        return weblazem_get_default_webdesign_portfolio_tabs();
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

    return !empty($normalized) ? $normalized : weblazem_get_default_webdesign_portfolio_tabs();
}

function weblazem_get_webdesign_portfolio_items() {
    $count = max(4, (int) weblazem_webdesign_option('portfolio_count', '12'));
    $query = new WP_Query(array(
        'post_type'      => 'portfolio',
        'posts_per_page' => $count,
        'post_status'    => 'publish',
        'orderby'        => 'date',
        'order'          => 'DESC',
    ));

    $items = array();
    $colors = weblazem_webdesign_showcase_colors();
    $i = 0;

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $terms = wp_get_post_terms(get_the_ID(), 'portfolio_category', array('fields' => 'slugs'));
            $logo  = get_post_meta(get_the_ID(), '_weblazem_portfolio_client', true);

            $items[] = array(
                'title'    => get_post_meta(get_the_ID(), '_weblazem_portfolio_subtitle', true) ?: get_the_title(),
                'image'    => get_the_post_thumbnail_url(get_the_ID(), 'large'),
                'logo'     => '',
                'logo_text'=> $logo ?: get_the_title(),
                'link'     => get_permalink(),
                'tag'      => '',
                'color'    => $colors[$i % count($colors)],
                'category' => is_array($terms) ? implode(' ', $terms) : '',
            );
            $i++;
        }
        wp_reset_postdata();
    }

    $manual = get_option('weblazem_webdesign_portfolio_items', array());
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

function weblazem_save_webdesign_section_toggles() {
    foreach (weblazem_get_webdesign_sections_config() as $key => $label) {
        $option_key = 'weblazem_webdesign_section_' . $key . '_enabled';
        $value      = (isset($_POST[$option_key]) && $_POST[$option_key] === '1') ? '1' : '0';
        update_option($option_key, $value);
    }
}
