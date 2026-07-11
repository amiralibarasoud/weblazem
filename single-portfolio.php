<?php
/**
 * Single portfolio project template.
 */

get_header();
?>

<main class="weblazem-portfolio-single" dir="rtl">
    <?php while (have_posts()) : the_post(); ?>
        <?php get_template_part('template-parts/portfolio/single/hero'); ?>
        <?php get_template_part('template-parts/portfolio/single/sections'); ?>
        <?php get_template_part('template-parts/portfolio/single/case-study'); ?>
        <?php get_template_part('template-parts/portfolio/single/more-projects'); ?>
        <?php get_template_part('template-parts/portfolio/single/consultation'); ?>
        <?php get_template_part('template-parts/portfolio/single/bottom-cta'); ?>
    <?php endwhile; ?>
</main>

<?php get_footer(); ?>
