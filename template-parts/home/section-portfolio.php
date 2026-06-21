<?php
/**
 * Homepage portfolio section — latest 4 portfolio items.
 */

$section_title = get_option('weblazem_portfolio_title', 'جدیدترین نمونه‌کارهای وب‌لازم');
$more_text     = get_option('weblazem_portfolio_more_text', 'نمایش بیشتر');
$card_btn_text = get_option('weblazem_portfolio_card_button_text', 'مشاهده‌ی پروژه');
$archive_url   = weblazem_get_portfolio_archive_url();

$portfolio_query = new WP_Query(array(
    'post_type'      => 'portfolio',
    'posts_per_page' => 4,
    'post_status'    => 'publish',
    'orderby'        => 'date',
    'order'          => 'DESC',
));
?>

<section class="weblazem-portfolio-section" dir="rtl">
    <div class="container">
        <div class="portfolio-section-header">
            <?php if (!empty($section_title)) : ?>
                <h2 class="portfolio-section-title"><?php echo esc_html($section_title); ?></h2>
            <?php endif; ?>

            <a href="<?php echo esc_url($archive_url); ?>" class="portfolio-more-button">
                <?php echo esc_html($more_text); ?>
                <i class="fas fa-arrow-left" aria-hidden="true"></i>
            </a>
        </div>

        <?php if ($portfolio_query->have_posts()) : ?>
            <div class="portfolio-cards">
                <?php
                while ($portfolio_query->have_posts()) :
                    $portfolio_query->the_post();
                    weblazem_render_portfolio_card(array(
                        'card_btn_text' => $card_btn_text,
                        'heading_tag'   => 'h3',
                    ));
                endwhile;
                ?>
            </div>
        <?php else : ?>
            <p class="portfolio-section-empty">به‌زودی نمونه کارهای جدید اضافه می‌شود.</p>
        <?php endif; ?>
    </div>
</section>

<?php wp_reset_postdata(); ?>
