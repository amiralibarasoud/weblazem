<?php
/**
 * Dynamic service landing pages — context and per-page storage.
 */

define('WEBLAZEM_SERVICE_LANDING_TEMPLATE', 'service-landing-template.php');
define('WEBLAZEM_SERVICE_LANDING_META', '_weblazem_service_landing');

function weblazem_service_landing_get_repeater_keys() {
    return array(
        'splits',
        'process_steps',
        'advantages_items',
        'faq_items',
        'service_cards',
        'portfolio_tabs',
        'customers_logos',
        'portfolio_items',
    );
}

function weblazem_service_landing_get_default_repeaters() {
    return array(
        'splits'            => weblazem_get_default_webdesign_splits(),
        'process_steps'     => weblazem_get_default_webdesign_process_steps(),
        'advantages_items'    => weblazem_get_default_webdesign_advantages(),
        'faq_items'           => weblazem_get_default_webdesign_faq_items(),
        'service_cards'       => weblazem_get_default_webdesign_service_cards(),
        'portfolio_tabs'      => weblazem_get_default_webdesign_portfolio_tabs(),
        'customers_logos'     => function_exists('weblazem_get_default_webdesign_customer_logos')
            ? weblazem_get_default_webdesign_customer_logos()
            : array(),
        'portfolio_items'     => array(),
    );
}

function weblazem_service_landing_get_default_sections() {
    $sections = array();
    foreach (weblazem_get_webdesign_sections_config() as $key => $label) {
        $sections[$key] = '1';
    }
    return $sections;
}

function weblazem_service_landing_get_storage($post_id) {
    $stored = get_post_meta((int) $post_id, WEBLAZEM_SERVICE_LANDING_META, true);
    if (!is_array($stored)) {
        $stored = array();
    }

    $defaults = array(
        'layout'    => 'webdesign',
        'sections'  => weblazem_service_landing_get_default_sections(),
        'fields'    => weblazem_webdesign_defaults(),
        'repeaters' => weblazem_service_landing_get_default_repeaters(),
    );

    $merged = wp_parse_args($stored, $defaults);

    if (!is_array($merged['fields'])) {
        $merged['fields'] = $defaults['fields'];
    } else {
        $merged['fields'] = wp_parse_args($merged['fields'], $defaults['fields']);
    }

    if (!is_array($merged['sections'])) {
        $merged['sections'] = $defaults['sections'];
    }

    if (!is_array($merged['repeaters'])) {
        $merged['repeaters'] = $defaults['repeaters'];
    } else {
        foreach ($defaults['repeaters'] as $key => $value) {
            if (!isset($merged['repeaters'][$key]) || !is_array($merged['repeaters'][$key])) {
                $merged['repeaters'][$key] = $value;
            }
        }
    }

    return $merged;
}

function weblazem_service_landing_save_storage($post_id, $data) {
    return update_post_meta((int) $post_id, WEBLAZEM_SERVICE_LANDING_META, $data);
}

function weblazem_service_landing_set_context($post_id) {
    $GLOBALS['weblazem_service_landing_context_id'] = (int) $post_id;
}

function weblazem_service_landing_get_context_id() {
    return isset($GLOBALS['weblazem_service_landing_context_id'])
        ? (int) $GLOBALS['weblazem_service_landing_context_id']
        : 0;
}

function weblazem_service_landing_clear_context() {
    unset($GLOBALS['weblazem_service_landing_context_id']);
}

function weblazem_is_service_landing_page($post_id = 0) {
    if (!$post_id) {
        $post_id = get_queried_object_id();
    }
    if (!$post_id) {
        return false;
    }
    return get_page_template_slug($post_id) === WEBLAZEM_SERVICE_LANDING_TEMPLATE;
}

function weblazem_get_service_landing_pages() {
    return get_posts(array(
        'post_type'      => 'page',
        'post_status'    => array('publish', 'draft', 'pending', 'private'),
        'posts_per_page' => -1,
        'meta_key'       => '_wp_page_template',
        'meta_value'     => WEBLAZEM_SERVICE_LANDING_TEMPLATE,
        'orderby'        => 'title',
        'order'          => 'ASC',
    ));
}

function weblazem_service_landing_copy_from_webdesign() {
    $data = array(
        'layout'    => 'webdesign',
        'sections'  => array(),
        'fields'    => array(),
        'repeaters' => array(),
    );

    foreach (weblazem_get_webdesign_sections_config() as $key => $label) {
        $data['sections'][$key] = get_option('weblazem_webdesign_section_' . $key . '_enabled', '1');
    }

    foreach (weblazem_webdesign_defaults() as $key => $value) {
        $data['fields'][$key] = get_option('weblazem_webdesign_' . $key, $value);
    }

    foreach (weblazem_service_landing_get_repeater_keys() as $key) {
        $data['repeaters'][$key] = get_option('weblazem_webdesign_' . $key, array());
    }

    return $data;
}

function weblazem_create_service_landing_page($title, $slug, $copy_from_webdesign = true) {
    $existing = get_page_by_path($slug);
    if ($existing) {
        return (int) $existing->ID;
    }

    $post_id = wp_insert_post(array(
        'post_title'  => $title,
        'post_name'   => sanitize_title($slug),
        'post_status' => 'publish',
        'post_type'   => 'page',
        'post_content'=> '',
    ), true);

    if (is_wp_error($post_id) || !$post_id) {
        return $post_id;
    }

    update_post_meta($post_id, '_wp_page_template', WEBLAZEM_SERVICE_LANDING_TEMPLATE);

    $storage = $copy_from_webdesign
        ? weblazem_service_landing_copy_from_webdesign()
        : weblazem_service_landing_get_storage($post_id);

    if ($copy_from_webdesign) {
        $storage['fields']['hero_title'] = $title;
    }

    weblazem_service_landing_save_storage($post_id, $storage);

    return (int) $post_id;
}

function weblazem_seed_service_landing_demo_pages() {
    if (get_option('weblazem_service_landing_seeded') === '1') {
        return;
    }

    $pages = array(
        array(
            'title' => 'طراحی سایت فروشگاهی',
            'slug'  => 'tarahi-site-forooshgahi',
        ),
        array(
            'title' => 'طراحی سایت شرکتی',
            'slug'  => 'tarahi-site-sherkati',
        ),
    );

    foreach ($pages as $page) {
        $post_id = weblazem_create_service_landing_page($page['title'], $page['slug'], true);
        if (!is_wp_error($post_id) && $post_id) {
            $storage = weblazem_service_landing_get_storage($post_id);
            $storage['fields']['hero_title'] = $page['title'];
            if ($page['slug'] === 'tarahi-site-forooshgahi') {
                $storage['fields']['hero_text'] = 'فروشگاه اینترنتی حرفه‌ای، مسیر تبدیل بازدیدکننده به خریدار است. ما فروشگاهی می‌سازیم که سریع، امن و قابل توسعه باشد.';
            } else {
                $storage['fields']['hero_text'] = 'سایت شرکتی، ویترین رسمی برند شما در فضای دیجیتال است. طراحی حرفه‌ای برای اعتمادسازی و جذب مشتریان سازمانی.';
            }
            weblazem_service_landing_save_storage($post_id, $storage);
        }
    }

    update_option('weblazem_service_landing_seeded', '1');
    flush_rewrite_rules(false);
}
add_action('init', 'weblazem_seed_service_landing_demo_pages', 45);
