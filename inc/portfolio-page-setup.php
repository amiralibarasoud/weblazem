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

function weblazem_get_portfolio_list_paged() {
    $paged = (int) get_query_var('portfolio_page');

    if ($paged < 1 && isset($_GET['portfolio_page'])) {
        $paged = (int) $_GET['portfolio_page'];
    }

    if ($paged < 1) {
        $paged = max(1, (int) get_query_var('paged'), (int) get_query_var('page'));
    }

    return max(1, $paged);
}

function weblazem_portfolio_query_vars($vars) {
    $vars[] = 'portfolio_page';
    return $vars;
}
add_filter('query_vars', 'weblazem_portfolio_query_vars');

function weblazem_portfolio_pagination($query) {
    if (!$query instanceof WP_Query || $query->max_num_pages <= 1) {
        return;
    }

    $paged    = weblazem_get_portfolio_list_paged();
    $base_url = trailingslashit(weblazem_get_portfolio_page_url());

    $links = paginate_links(array(
        'base'      => esc_url($base_url) . '%_%',
        'format'    => '?portfolio_page=%#%',
        'current'   => $paged,
        'total'     => (int) $query->max_num_pages,
        'mid_size'  => 2,
        'end_size'  => 1,
        'prev_text' => '<i class="fas fa-chevron-right" aria-hidden="true"></i><span class="screen-reader-text">صفحه قبل</span>',
        'next_text' => '<i class="fas fa-chevron-left" aria-hidden="true"></i><span class="screen-reader-text">صفحه بعد</span>',
        'type'      => 'plain',
        'add_args'  => false,
    ));

    if (!$links) {
        return;
    }

    $links = preg_replace(
        '/(href=[\'"])([^\'"]+)([\'"])/',
        '$1$2#portfolio-all-projects$3',
        $links
    );

    echo '<nav class="portfolio-page-pagination" aria-label="صفحه‌بندی نمونه کارها">';
    echo '<div class="nav-links">' . $links . '</div>';
    echo '</nav>';
}
