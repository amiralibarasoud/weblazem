<?php
/**
 * Single portfolio — hero section.
 */

$post_id      = get_the_ID();
$client_logo  = get_post_meta($post_id, '_weblazem_portfolio_client_logo', true);
$devices      = function_exists('weblazem_get_portfolio_device_images')
    ? weblazem_get_portfolio_device_images($post_id)
    : array(
        'desktop'            => weblazem_get_portfolio_single_hero_image($post_id),
        'mobile'             => get_post_meta($post_id, '_weblazem_portfolio_mobile_image', true),
        'mobile_is_fallback' => false,
    );
$title        = weblazem_get_portfolio_single_display_title($post_id);
$intro        = weblazem_get_portfolio_single_intro($post_id);
$phone        = weblazem_get_portfolio_single_option('weblazem_portfolio_single_sticky_phone');
$btn_text     = weblazem_get_portfolio_single_option('weblazem_portfolio_single_sticky_btn_text');
$btn_url      = weblazem_get_portfolio_single_option('weblazem_portfolio_single_sticky_btn_url', '#');
$hero_image   = $devices['desktop'];
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
                    <?php
                    if (function_exists('weblazem_render_portfolio_device_mockup')) {
                        weblazem_render_portfolio_device_mockup(array(
                            'desktop'            => $devices['desktop'],
                            'mobile'             => $devices['mobile'],
                            'alt'                => $title,
                            'variant'            => 'hero',
                            'mobile_is_fallback' => !empty($devices['mobile_is_fallback']),
                        ));
                    }
                    ?>
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
