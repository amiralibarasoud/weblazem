<?php
/**
 * Auto-create content production & support page.
 */

define('WEBLAZEM_CONTENTSUPPORT_PAGE_SLUG', 'tolid-mohtava-poshtibani');
define('WEBLAZEM_CONTENTSUPPORT_PAGE_TEMPLATE', 'contentsupport-template.php');

function weblazem_get_contentsupport_page_id() {
    $page_id = (int) get_option('weblazem_contentsupport_page_id', 0);
    if ($page_id && get_post_status($page_id) === 'publish') {
        return $page_id;
    }
    $page = get_page_by_path(WEBLAZEM_CONTENTSUPPORT_PAGE_SLUG);
    if ($page && $page->post_status === 'publish') {
        update_option('weblazem_contentsupport_page_id', (int) $page->ID);
        return (int) $page->ID;
    }
    return 0;
}

function weblazem_is_contentsupport_page() {
    if (is_page_template(WEBLAZEM_CONTENTSUPPORT_PAGE_TEMPLATE)) {
        return true;
    }
    $page_id = weblazem_get_contentsupport_page_id();
    return $page_id && is_page($page_id);
}

function weblazem_get_contentsupport_page_url() {
    $page_id = weblazem_get_contentsupport_page_id();
    if ($page_id) {
        $url = get_permalink($page_id);
        if ($url) {
            return $url;
        }
    }
    return home_url('/' . WEBLAZEM_CONTENTSUPPORT_PAGE_SLUG . '/');
}

function weblazem_ensure_contentsupport_page() {
    if (weblazem_get_contentsupport_page_id()) {
        return;
    }
    $page_id = wp_insert_post(array(
        'post_title'   => 'تولید محتوا و پشتیبانی',
        'post_name'    => WEBLAZEM_CONTENTSUPPORT_PAGE_SLUG,
        'post_status'  => 'publish',
        'post_type'    => 'page',
        'post_content' => '',
    ), true);
    if (is_wp_error($page_id) || !$page_id) {
        return;
    }
    update_post_meta($page_id, '_wp_page_template', WEBLAZEM_CONTENTSUPPORT_PAGE_TEMPLATE);
    update_option('weblazem_contentsupport_page_id', (int) $page_id);
    weblazem_maybe_link_services_card_to_contentsupport();
    flush_rewrite_rules(false);
}
add_action('init', 'weblazem_ensure_contentsupport_page', 36);

function weblazem_maybe_link_services_card_to_contentsupport() {
    $cards = get_option('weblazem_services_cards', array());
    if (!is_array($cards) || empty($cards)) {
        return;
    }
    $url = weblazem_get_contentsupport_page_url();
    $changed = false;
    foreach ($cards as $index => $card) {
        if (empty($card['title'])) {
            continue;
        }
        $title = $card['title'];
        if (strpos($title, 'محتوا') !== false || strpos($title, 'پشتیبانی') !== false) {
            if (empty($card['button_url']) || $card['button_url'] === '#') {
                $cards[$index]['button_url'] = $url;
                $changed = true;
            }
        }
    }
    if ($changed) {
        update_option('weblazem_services_cards', $cards);
    }
}
