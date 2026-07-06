<?php
/**
 * Blog post single template — Figma layout with configurable sections.
 */

get_header();
?>

<main class="weblazem-blog-single-page" dir="rtl">
    <?php if (have_posts()) : while (have_posts()) : the_post(); ?>

        <?php if (weblazem_is_blog_single_section_enabled('banner')) {
            get_template_part('template-parts/blog-single/section', 'banner');
        } ?>

        <?php if (weblazem_is_blog_single_section_enabled('hero_image')) {
            get_template_part('template-parts/blog-single/section', 'hero-image');
        } ?>

        <?php get_template_part('template-parts/blog-single/section', 'main'); ?>

        <?php if (weblazem_is_blog_single_section_enabled('related')) {
            get_template_part('template-parts/blog-single/section', 'related');
        } ?>

        <?php if (weblazem_is_blog_single_section_enabled('comments')) {
            get_template_part('template-parts/blog-single/section', 'comments');
        } ?>

    <?php endwhile; endif; ?>
</main>

<?php get_footer(); ?>
