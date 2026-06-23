<?php
/**
 * Single portfolio — consultation section (fixed).
 */

if (weblazem_get_portfolio_single_option('weblazem_portfolio_single_consult_enabled', '1') !== '1') {
    return;
}

$title    = weblazem_get_portfolio_single_option('weblazem_portfolio_single_consult_title');
$text     = weblazem_get_portfolio_single_option('weblazem_portfolio_single_consult_text');
$image    = weblazem_get_portfolio_single_option('weblazem_portfolio_single_consult_image');
$phone    = weblazem_get_portfolio_single_option('weblazem_portfolio_single_consult_phone');
$btn_text = weblazem_get_portfolio_single_option('weblazem_portfolio_single_consult_btn_text');
$btn_url  = weblazem_get_portfolio_single_option('weblazem_portfolio_single_consult_btn_url', '#');
?>

<section class="portfolio-single-consult" dir="rtl">
    <div class="container">
        <div class="portfolio-single-consult__grid">
            <div class="portfolio-single-consult__content">
                <?php if (!empty($title)) : ?>
                    <h2 class="portfolio-single-consult__title"><?php echo esc_html($title); ?></h2>
                <?php endif; ?>

                <?php if (!empty($text)) : ?>
                    <p class="portfolio-single-consult__text"><?php echo esc_html($text); ?></p>
                <?php endif; ?>

                <?php if (!empty($phone)) : ?>
                    <a href="tel:<?php echo esc_attr(preg_replace('/\s+/', '', $phone)); ?>" class="portfolio-single-consult__phone">
                        <?php echo esc_html($phone); ?>
                    </a>
                <?php endif; ?>

                <?php if (!empty($btn_text)) : ?>
                    <a href="<?php echo esc_url($btn_url); ?>" class="portfolio-single-consult__btn">
                        <?php echo esc_html($btn_text); ?>
                        <i class="fas fa-arrow-left" aria-hidden="true"></i>
                    </a>
                <?php endif; ?>
            </div>

            <?php if (!empty($image)) : ?>
                <div class="portfolio-single-consult__media">
                    <div class="portfolio-single-consult__media-frame">
                        <img src="<?php echo esc_url($image); ?>" alt="" />
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <svg class="portfolio-single-consult__wave" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 80" preserveAspectRatio="none" aria-hidden="true">
        <path fill="#f2e6f7" d="M0,40 C300,80 600,0 900,32 C1140,56 1320,24 1440,40 L1440,80 L0,80 Z"/>
    </svg>
</section>
