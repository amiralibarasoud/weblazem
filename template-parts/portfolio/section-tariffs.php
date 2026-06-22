<?php
/**
 * Portfolio archive — tariffs / consultation CTA section.
 */

if (get_option('weblazem_portfolio_page_tariffs_enabled', '1') !== '1') {
    return;
}

$title       = get_option('weblazem_portfolio_page_tariffs_title', 'تعرفه‌ها');
$description = get_option('weblazem_portfolio_page_tariffs_description', '');
$cta_text    = get_option('weblazem_portfolio_page_tariffs_cta_text', '');
$btn_text    = get_option('weblazem_portfolio_page_tariffs_btn_text', 'مشاوره رایگان');
$btn_url     = get_option('weblazem_portfolio_page_tariffs_btn_url', '#');
?>

<section class="portfolio-page-section portfolio-page-tariffs" dir="rtl">
    <div class="container">
        <div class="portfolio-page-tariffs__header">
            <?php if (!empty($title)) : ?>
                <h2 class="portfolio-page-section__title portfolio-page-section__title--center"><?php echo esc_html($title); ?></h2>
            <?php endif; ?>

            <?php if (!empty($description)) : ?>
                <p class="portfolio-page-tariffs__subtitle"><?php echo esc_html($description); ?></p>
            <?php endif; ?>
        </div>

        <div class="portfolio-page-tariffs__cta-wrap">
            <div class="portfolio-page-tariffs__blob" aria-hidden="true"></div>

            <div class="portfolio-page-tariffs__cta">
                <?php if (!empty($cta_text)) : ?>
                    <p class="portfolio-page-tariffs__cta-text"><?php echo esc_html($cta_text); ?></p>
                <?php endif; ?>

                <?php if (!empty($btn_text)) : ?>
                    <a href="<?php echo esc_url($btn_url); ?>" class="portfolio-page-tariffs__btn">
                        <?php echo esc_html($btn_text); ?>
                        <i class="fas fa-arrow-left" aria-hidden="true"></i>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
