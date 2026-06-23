<?php
/**
 * Single portfolio — hero section.
 */

$post_id      = get_the_ID();
$client_logo  = get_post_meta($post_id, '_weblazem_portfolio_client_logo', true);
$hero_image   = weblazem_get_portfolio_single_hero_image($post_id);
$mobile_image = get_post_meta($post_id, '_weblazem_portfolio_mobile_image', true);
$title        = weblazem_get_portfolio_single_display_title($post_id);
$intro        = weblazem_get_portfolio_single_intro($post_id);
$phone        = weblazem_get_portfolio_single_option('weblazem_portfolio_single_sticky_phone');
$btn_text     = weblazem_get_portfolio_single_option('weblazem_portfolio_single_sticky_btn_text');
$btn_url      = weblazem_get_portfolio_single_option('weblazem_portfolio_single_sticky_btn_url', '#');
?>

<section class="portfolio-single-hero" dir="rtl">
    <div class="container portfolio-single-hero__intro">
        <?php if (!empty($client_logo)) : ?>
            <div class="portfolio-single-hero__logo">
                <img src="<?php echo esc_url($client_logo); ?>" alt="<?php echo esc_attr($title); ?>" />
            </div>
        <?php endif; ?>

        <h1 class="portfolio-single-hero__title"><?php echo esc_html($title); ?></h1>

        <?php if (!empty($intro)) : ?>
            <p class="portfolio-single-hero__text"><?php echo esc_html($intro); ?></p>
        <?php endif; ?>
    </div>

    <?php if (!empty($hero_image)) : ?>
        <div class="container">
            <div class="portfolio-single-hero__showcase">
                <span class="portfolio-single-hero__deco" aria-hidden="true">ART OF DESIGN</span>

                <div class="portfolio-single-hero__devices">
                    <div class="portfolio-single-hero__desktop">
                        <img src="<?php echo esc_url($hero_image); ?>" alt="<?php echo esc_attr($title); ?>" />
                    </div>

                    <?php if (!empty($mobile_image)) : ?>
                        <div class="portfolio-single-hero__mobile">
                            <div class="portfolio-single-hero__mobile-frame">
                                <img src="<?php echo esc_url($mobile_image); ?>" alt="<?php echo esc_attr($title); ?> — موبایل" />
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="portfolio-single-hero__actions">
                    <?php if (!empty($phone)) : ?>
                        <a href="tel:<?php echo esc_attr(preg_replace('/\s+/', '', $phone)); ?>" class="portfolio-single-hero__action portfolio-single-hero__action--phone">
                            <i class="fas fa-phone" aria-hidden="true"></i>
                            <span><?php echo esc_html($phone); ?></span>
                        </a>
                    <?php endif; ?>

                    <?php if (!empty($btn_text)) : ?>
                        <a href="<?php echo esc_url($btn_url); ?>" class="portfolio-single-hero__action portfolio-single-hero__action--cta">
                            <i class="fas fa-pen-to-square" aria-hidden="true"></i>
                            <span><?php echo esc_html($btn_text); ?></span>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
</section>
