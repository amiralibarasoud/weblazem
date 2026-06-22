<?php
/**
 * Portfolio archive — latest projects section.
 */

if (get_option('weblazem_portfolio_page_latest_enabled', '1') !== '1') {
    return;
}

$section_title = get_option('weblazem_portfolio_page_latest_title', 'آخرین پروژه‌های اجرا شده در وب‌لازم');
$latest_count  = max(1, min(12, (int) get_option('weblazem_portfolio_page_latest_count', 4)));
$card_btn_text = get_option('weblazem_portfolio_page_card_button_text', 'مشاهده پروژه');

$latest_query = new WP_Query(array(
    'post_type'      => 'portfolio',
    'posts_per_page' => $latest_count,
    'post_status'    => 'publish',
    'orderby'        => 'date',
    'order'          => 'DESC',
));

if (!$latest_query->have_posts()) {
    return;
}
?>

<section class="portfolio-page-section portfolio-page-latest" dir="rtl">
    <div class="container">
        <?php if (!empty($section_title)) : ?>
            <h2 class="portfolio-page-section__title"><?php echo esc_html($section_title); ?></h2>
        <?php endif; ?>

        <div class="portfolio-page-cards">
            <?php
            while ($latest_query->have_posts()) :
                $latest_query->the_post();
                weblazem_render_portfolio_card(array(
                    'card_btn_text' => $card_btn_text,
                    'heading_tag'   => 'h3',
                    'variant'       => 'archive',
                ));
            endwhile;
            ?>
        </div>
    </div>
</section>

<?php wp_reset_postdata(); ?>
