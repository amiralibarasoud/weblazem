<?php
/**
 * Template Name: صفحه خدمات داینامیک
 * Description: صفحه داخلی با قالب طراحی سایت و داده اختصاصی
 */

get_header();

if (have_posts()) :
    while (have_posts()) :
        the_post();
        weblazem_service_landing_set_context(get_the_ID());
        ?>
        <main class="weblazem-webdesign-page weblazem-service-landing-page" dir="rtl">
            <?php get_template_part('template-parts/webdesign/layout', 'main'); ?>
        </main>
        <?php
        weblazem_service_landing_clear_context();
    endwhile;
endif;

get_footer();
