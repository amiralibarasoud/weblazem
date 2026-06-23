<?php
/**
 * Single portfolio — sticky bottom action bar.
 */

$phone    = weblazem_get_portfolio_single_option('weblazem_portfolio_single_sticky_phone');
$btn_text = weblazem_get_portfolio_single_option('weblazem_portfolio_single_sticky_btn_text');
$btn_url  = weblazem_get_portfolio_single_option('weblazem_portfolio_single_sticky_btn_url', '#');

if (empty($phone) && empty($btn_text)) {
    return;
}
?>

<div class="portfolio-single-sticky" dir="rtl" aria-label="اقدام سریع">
    <div class="portfolio-single-sticky__bar">
        <?php if (!empty($phone)) : ?>
            <a href="tel:<?php echo esc_attr(preg_replace('/\s+/', '', $phone)); ?>" class="portfolio-single-sticky__phone">
                <i class="fas fa-phone" aria-hidden="true"></i>
                <span><?php echo esc_html($phone); ?></span>
            </a>
        <?php endif; ?>

        <?php if (!empty($btn_text)) : ?>
            <a href="<?php echo esc_url($btn_url); ?>" class="portfolio-single-sticky__btn">
                <i class="fas fa-pen-to-square" aria-hidden="true"></i>
                <span><?php echo esc_html($btn_text); ?></span>
            </a>
        <?php endif; ?>
    </div>
</div>
