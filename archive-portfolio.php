<?php
/**
 * Archive fallback — redirect to the portfolio page when available.
 */

$page_id = function_exists('weblazem_get_portfolio_page_id') ? weblazem_get_portfolio_page_id() : 0;

if ($page_id) {
    wp_safe_redirect(get_permalink($page_id));
    exit;
}

get_header();
?>

<main class="weblazem-portfolio-page" dir="rtl">
    <?php get_template_part('template-parts/portfolio/layout', 'main'); ?>
</main>

<?php get_footer(); ?>
