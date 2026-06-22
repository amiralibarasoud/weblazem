<?php
/**
 * Auto-create portfolio WordPress page and helpers.
 */

define('WEBLAZEM_PORTFOLIO_PAGE_SLUG', 'namune-kar');
define('WEBLAZEM_PORTFOLIO_PAGE_TEMPLATE', 'portfolio-template.php');

function weblazem_get_portfolio_page_id() {
    $page_id = (int) get_option('weblazem_portfolio_page_id', 0);

    if ($page_id && get_post_status($page_id) === 'publish') {
        return $page_id;
    }

    $page = get_page_by_path(WEBLAZEM_PORTFOLIO_PAGE_SLUG);

    if ($page && $page->post_status === 'publish') {
        update_option('weblazem_portfolio_page_id', (int) $page->ID);
        return (int) $page->ID;
    }

    return 0;
}

function weblazem_is_portfolio_listing_page() {
    if (is_page_template(WEBLAZEM_PORTFOLIO_PAGE_TEMPLATE)) {
        return true;
    }

    $page_id = weblazem_get_portfolio_page_id();

    return $page_id && is_page($page_id);
}

function weblazem_ensure_portfolio_page() {
    if (weblazem_get_portfolio_page_id()) {
        return;
    }

    $page_id = wp_insert_post(array(
        'post_title'   => 'نمونه کارها',
        'post_name'    => WEBLAZEM_PORTFOLIO_PAGE_SLUG,
        'post_status'  => 'publish',
        'post_type'    => 'page',
        'post_content' => '',
    ), true);

    if (is_wp_error($page_id) || !$page_id) {
        return;
    }

    update_post_meta($page_id, '_wp_page_template', WEBLAZEM_PORTFOLIO_PAGE_TEMPLATE);
    update_option('weblazem_portfolio_page_id', (int) $page_id);

    flush_rewrite_rules(false);
}
add_action('init', 'weblazem_ensure_portfolio_page', 35);

function weblazem_get_portfolio_page_url() {
    $page_id = weblazem_get_portfolio_page_id();

    if ($page_id) {
        $url = get_permalink($page_id);

        if ($url) {
            return $url;
        }
    }

    return home_url('/' . WEBLAZEM_PORTFOLIO_PAGE_SLUG . '/');
}

function weblazem_portfolio_pagination($query) {
    if (!$query instanceof WP_Query || $query->max_num_pages <= 1) {
        return;
    }

    $paged = max(1, (int) get_query_var('paged'), (int) get_query_var('page'));

    $links = paginate_links(array(
        'total'     => (int) $query->max_num_pages,
        'current'   => $paged,
        'mid_size'  => 2,
        'prev_text' => '<i class="fas fa-chevron-right" aria-hidden="true"></i>',
        'next_text' => '<i class="fas fa-chevron-left" aria-hidden="true"></i>',
        'type'      => 'plain',
    ));

    if (!$links) {
        return;
    }

    echo '<nav class="portfolio-page-pagination" aria-label="صفحه‌بندی نمونه کارها">';
    echo '<div class="nav-links">' . $links . '</div>';
    echo '</nav>';
}
