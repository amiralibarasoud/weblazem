<?php
/**
 * Post archive fallback — redirect to blog archive page when available.
 */

$page_id = function_exists('weblazem_get_blogarchive_page_id') ? weblazem_get_blogarchive_page_id() : 0;

if ($page_id && is_home()) {
    wp_safe_redirect(get_permalink($page_id));
    exit;
}

get_header();
?>

<main class="weblazem-blog-archive-page" dir="rtl">
    <?php get_template_part('template-parts/blog-archive/layout', 'main'); ?>
</main>

<?php get_footer(); ?>
