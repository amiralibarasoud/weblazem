<?php
/**
 * Pricing page — hero.
 */

$icon  = weblazem_pricing_option('hero_icon');
$title = weblazem_pricing_option('hero_title', 'خدمات و تعرفه ها');
$text  = weblazem_pricing_option('hero_text');
?>

<section class="pricing-page-hero" dir="rtl">
    <div class="pricing-page-hero__blob pricing-page-hero__blob--tl" aria-hidden="true"></div>
    <div class="pricing-page-hero__blob pricing-page-hero__blob--br" aria-hidden="true"></div>

    <div class="container">
        <div class="pricing-page-hero__inner">
            <?php if (!empty($icon)) : ?>
                <div class="pricing-page-hero__icon">
                    <img src="<?php echo esc_url($icon); ?>" alt="" width="120" height="96" loading="lazy" />
                </div>
            <?php endif; ?>

            <div class="pricing-page-hero__content">
                <?php if ($title) : ?>
                    <h1 class="pricing-page-hero__title"><?php echo esc_html($title); ?></h1>
                <?php endif; ?>

                <?php if ($text) : ?>
                    <p class="pricing-page-hero__text"><?php echo esc_html($text); ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
