<?php
/**
 * Single portfolio — bottom CTA + category promo cards (fixed).
 */

if (weblazem_get_portfolio_single_option('weblazem_portfolio_single_cta_enabled', '1') !== '1') {
    return;
}

$subtitle  = weblazem_get_portfolio_single_option('weblazem_portfolio_single_cta_subtitle');
$title     = weblazem_get_portfolio_single_option('weblazem_portfolio_single_cta_title');
$highlight = weblazem_get_portfolio_single_option('weblazem_portfolio_single_cta_highlight');
$phone     = weblazem_get_portfolio_single_option('weblazem_portfolio_single_cta_phone');
$btn_text  = weblazem_get_portfolio_single_option('weblazem_portfolio_single_cta_btn_text');
$btn_url   = weblazem_get_portfolio_single_option('weblazem_portfolio_single_cta_btn_url', '#');
$cards     = weblazem_get_portfolio_single_promo_cards();
?>

<section class="portfolio-single-bottom" dir="rtl">
    <div class="container">
        <?php if (!empty($cards)) : ?>
            <div class="portfolio-single-bottom__cards">
                <?php foreach ($cards as $card) :
                    $card_url  = weblazem_get_promo_card_url($card);
                    $bg_image  = weblazem_get_promo_card_background_image($card);
                    $fg_image  = !empty($card['image']) ? $card['image'] : $bg_image;
                    ?>
                    <a href="<?php echo esc_url($card_url); ?>" class="portfolio-single-bottom__card">
                        <?php if (!empty($bg_image)) : ?>
                            <span class="portfolio-single-bottom__card-bg" style="background-image: url('<?php echo esc_url($bg_image); ?>');" aria-hidden="true"></span>
                        <?php endif; ?>
                        <span class="portfolio-single-bottom__card-overlay" aria-hidden="true"></span>
                        <span class="portfolio-single-bottom__card-glow" aria-hidden="true"></span>

                        <div class="portfolio-single-bottom__card-content">
                            <?php if (!empty($card['title'])) : ?>
                                <h3 class="portfolio-single-bottom__card-title"><?php echo esc_html($card['title']); ?></h3>
                            <?php endif; ?>

                            <?php if (!empty($card['subtitle'])) : ?>
                                <span class="portfolio-single-bottom__card-subtitle" dir="ltr"><?php echo esc_html($card['subtitle']); ?></span>
                            <?php endif; ?>

                            <?php if (!empty($card['text'])) : ?>
                                <p class="portfolio-single-bottom__card-text"><?php echo esc_html($card['text']); ?></p>
                            <?php endif; ?>

                            <span class="portfolio-single-bottom__card-arrow" aria-hidden="true">
                                <i class="fas fa-arrow-left"></i>
                            </span>
                        </div>

                        <?php if (!empty($fg_image)) : ?>
                            <div class="portfolio-single-bottom__card-device">
                                <div class="portfolio-single-bottom__card-device-frame">
                                    <img src="<?php echo esc_url($fg_image); ?>" alt="<?php echo esc_attr($card['title']); ?>" loading="lazy" />
                                </div>
                            </div>
                        <?php endif; ?>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="portfolio-single-bottom__cta">
            <?php if (!empty($subtitle)) : ?>
                <p class="portfolio-single-bottom__cta-subtitle"><?php echo esc_html($subtitle); ?></p>
            <?php endif; ?>

            <?php if (!empty($title)) : ?>
                <h2 class="portfolio-single-bottom__cta-title">
                    <?php echo weblazem_highlight_cta_title($title, $highlight); ?>
                </h2>
            <?php endif; ?>

            <div class="portfolio-single-bottom__cta-actions">
                <?php if (!empty($phone)) : ?>
                    <a href="tel:<?php echo esc_attr(preg_replace('/\s+/', '', $phone)); ?>" class="portfolio-single-bottom__cta-phone">
                        <i class="fas fa-phone" aria-hidden="true"></i>
                        <?php echo esc_html($phone); ?>
                    </a>
                <?php endif; ?>

                <?php if (!empty($btn_text)) : ?>
                    <a href="<?php echo esc_url($btn_url); ?>" class="portfolio-single-bottom__cta-btn">
                        <?php echo esc_html($btn_text); ?>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
