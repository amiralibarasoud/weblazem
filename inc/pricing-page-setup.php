<?php
/**
 * Auto-create pricing / tariffs page.
 */

define('WEBLAZEM_PRICING_PAGE_SLUG', 'khadamat-va-tarafeha');
define('WEBLAZEM_PRICING_PAGE_TEMPLATE', 'pricing-template.php');

function weblazem_get_pricing_page_id() {
    $page_id = (int) get_option('weblazem_pricing_page_id', 0);
    if ($page_id && get_post_status($page_id) === 'publish') {
        return $page_id;
    }
    $page = get_page_by_path(WEBLAZEM_PRICING_PAGE_SLUG);
    if ($page && $page->post_status === 'publish') {
        update_option('weblazem_pricing_page_id', (int) $page->ID);
        return (int) $page->ID;
    }
    return 0;
}

function weblazem_is_pricing_page() {
    if (is_page_template(WEBLAZEM_PRICING_PAGE_TEMPLATE)) {
        return true;
    }
    $page_id = weblazem_get_pricing_page_id();
    return $page_id && is_page($page_id);
}

function weblazem_get_pricing_page_url() {
    $page_id = weblazem_get_pricing_page_id();
    if ($page_id) {
        $url = get_permalink($page_id);
        if ($url) {
            return $url;
        }
    }
    return home_url('/' . WEBLAZEM_PRICING_PAGE_SLUG . '/');
}

function weblazem_ensure_pricing_page() {
    if (weblazem_get_pricing_page_id()) {
        return;
    }
    $page_id = wp_insert_post(array(
        'post_title'   => 'خدمات و تعرفه‌ها',
        'post_name'    => WEBLAZEM_PRICING_PAGE_SLUG,
        'post_status'  => 'publish',
        'post_type'    => 'page',
        'post_content' => '',
    ), true);
    if (is_wp_error($page_id) || !$page_id) {
        return;
    }
    update_post_meta($page_id, '_wp_page_template', WEBLAZEM_PRICING_PAGE_TEMPLATE);
    update_option('weblazem_pricing_page_id', (int) $page_id);
    flush_rewrite_rules(false);
}
add_action('init', 'weblazem_ensure_pricing_page', 36);
