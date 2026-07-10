<?php
/**
 * About Us — hero section.
 */

$en_title   = weblazem_aboutus_option('hero_en_title', 'About Us');
$title      = weblazem_aboutus_option('hero_title', 'درباره وب‌سیما');
$text       = weblazem_aboutus_option('hero_text', '');
$image      = weblazem_aboutus_option('hero_image', '');
$default_img = weblazem_aboutus_uri('hero-kiosk.svg');
$cards      = weblazem_get_aboutus_contact_cards();
?>

<section class="aboutus-hero" dir="rtl">
    <div class="container">
        <div class="aboutus-hero__grid">
            <div class="aboutus-hero__content">
                <?php if (!empty($en_title)) : ?>
                    <span class="aboutus-hero__en-title"><?php echo esc_html($en_title); ?></span>
                <?php endif; ?>

                <div class="aboutus-hero__calligraphy">
                    <?php weblazem_render_service_calligraphy('aboutus', 'hero_calligraphy_image', 'hero_calligraphy_text'); ?>
                </div>

                <?php if (!empty($title)) : ?>
                    <h1 class="aboutus-hero__title"><?php echo esc_html($title); ?></h1>
                <?php endif; ?>

                <?php if (!empty($text)) : ?>
                    <div class="aboutus-hero__text"><?php echo wp_kses_post(wpautop($text)); ?></div>
                <?php endif; ?>

                <?php if (!empty($cards)) : ?>
                    <div class="aboutus-hero__contacts">
                        <?php foreach ($cards as $card) :
                            if (empty($card['phone'])) {
                                continue;
                            }
                            ?>
                            <a href="tel:<?php echo esc_attr(preg_replace('/\s+/', '', $card['phone'])); ?>"
                               class="aboutus-contact-card">
                                <span class="aboutus-contact-card__icon" aria-hidden="true">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M6.6 10.8a15.1 15.1 0 006.6 6.6l2.2-2.2c.3-.3.7-.4 1.1-.3 1.2.4 2.5.6 3.8.6.6 0 1 .4 1 1V20c0 .6-.4 1-1 1C10.6 21 3 13.4 3 4c0-.6.4-1 1-1h3.5c.6 0 1 .4 1 1 0 1.3.2 2.6.6 3.8.1.4 0 .8-.3 1.1l-2.2 2.2z" fill="currentColor"/>
                                    </svg>
                                </span>
                                <span class="aboutus-contact-card__body">
                                    <strong class="aboutus-contact-card__phone"><?php echo esc_html($card['phone']); ?></strong>
                                    <?php if (!empty($card['label'])) : ?>
                                        <span class="aboutus-contact-card__label"><?php echo esc_html($card['label']); ?></span>
                                    <?php endif; ?>
                                </span>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="aboutus-hero__visual">
                <div class="aboutus-hero__visual-ring" aria-hidden="true"></div>
                <img src="<?php echo esc_url(!empty($image) ? $image : $default_img); ?>"
                     alt=""
                     class="aboutus-hero__image"
                     loading="eager" />
            </div>
        </div>
    </div>

    <?php get_template_part('template-parts/aboutus/part', 'wave', array('variant' => 'light-to-dark', 'position' => 'bottom')); ?>
</section>
