<?php
/**
 * Portfolio archive — all projects with pagination.
 */

$section_title = get_option('weblazem_portfolio_page_all_title', 'تمام پروژه‌ها');
$card_btn_text = get_option('weblazem_portfolio_page_card_button_text', 'مشاهده پروژه');
$per_page      = max(4, (int) get_option('weblazem_portfolio_page_all_per_page', 8));
$paged         = max(1, (int) get_query_var('paged'), (int) get_query_var('page'));

$portfolio_all_query = new WP_Query(array(
    'post_type'      => 'portfolio',
    'posts_per_page' => $per_page,
    'paged'          => $paged,
    'post_status'    => 'publish',
    'orderby'        => 'date',
    'order'          => 'DESC',
));
?>

<section class="portfolio-page-section portfolio-page-all" dir="rtl">
    <div class="container">
        <?php if (!empty($section_title)) : ?>
            <h2 class="portfolio-page-section__title"><?php echo esc_html($section_title); ?></h2>
        <?php endif; ?>

        <?php if ($portfolio_all_query->have_posts()) : ?>
            <div class="portfolio-page-cards">
                <?php
                while ($portfolio_all_query->have_posts()) :
                    $portfolio_all_query->the_post();
                    weblazem_render_portfolio_card(array(
                        'card_btn_text' => $card_btn_text,
                        'heading_tag'   => 'h3',
                        'variant'       => 'archive',
                    ));
                endwhile;
                ?>
            </div>

            <?php weblazem_portfolio_pagination($portfolio_all_query); ?>
        <?php else : ?>
            <p class="portfolio-page-empty">هنوز نمونه کاری ثبت نشده است.</p>
        <?php endif; ?>
    </div>
</section>

<?php wp_reset_postdata(); ?>
