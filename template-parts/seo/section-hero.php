<?php
/**
 * SEO page — hero section.
 */

$en_title    = weblazem_seo_option('hero_en_title', 'SEO & Digital Marketing');
$title       = weblazem_seo_option('hero_title', 'سئو و بازاریابی دیجیتال');
$text        = weblazem_seo_option('hero_text', '');
$image       = weblazem_seo_option('hero_image', '');
$default_img = get_template_directory_uri() . '/assets/images/seo/hero-disc.svg';
?>

<section class="seo-hero" dir="rtl">
    <div class="container">
        <div class="seo-hero__grid">
            <div class="seo-hero__visual">
                <img src="<?php echo esc_url(!empty($image) ? $image : $default_img); ?>"
                     alt=""
                     class="seo-hero__image" />
            </div>

            <div class="seo-hero__content" dir="rtl">
                <?php if (!empty($en_title)) : ?>
                    <span class="seo-hero__en-title"><?php echo esc_html($en_title); ?></span>
                <?php endif; ?>

                <div class="seo-hero__calligraphy">
                    <?php weblazem_render_service_calligraphy('seo', 'hero_calligraphy_image', 'hero_calligraphy_text'); ?>
                </div>

                <?php if (!empty($title)) : ?>
                    <h1 class="seo-hero__title"><?php echo esc_html($title); ?></h1>
                <?php endif; ?>

                <?php if (!empty($text)) : ?>
                    <div class="seo-hero__text"><?php echo wp_kses_post(wpautop($text)); ?></div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <svg class="seo-hero__wave" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 60" preserveAspectRatio="none" aria-hidden="true">
        <path fill="none" stroke="url(#heroWaveGrad)" stroke-width="2" d="M0,30 C360,55 720,5 1080,30 C1260,42 1380,35 1440,32"/>
        <defs>
            <linearGradient id="heroWaveGrad" x1="0%" y1="0%" x2="100%" y2="0%">
                <stop offset="0%" stop-color="#e879f9"/>
                <stop offset="100%" stop-color="#fb923c"/>
            </linearGradient>
        </defs>
    </svg>
</section>
