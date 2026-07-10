<?php
/**
 * About Us — CEO section.
 */

$accent     = weblazem_aboutus_option('ceo_accent_text', 'به ما محول کنید');
$name_cal   = weblazem_aboutus_option('ceo_name_calligraphy', 'امیر حسین اسماعیلی');
$name_en    = weblazem_aboutus_option('ceo_name_en', 'AMIR HOSSEIN ESMAEILI');
$title_en   = weblazem_aboutus_option('ceo_title_en', 'CEO at websima business studio');
$text       = weblazem_aboutus_option('ceo_text', '');
$image      = weblazem_aboutus_option('ceo_image', '');
$default_img = weblazem_aboutus_uri('ceo-portrait.svg');
?>

<section class="aboutus-ceo" dir="rtl">
    <?php get_template_part('template-parts/aboutus/part', 'wave', array('variant' => 'light-fill', 'position' => 'top')); ?>

    <div class="aboutus-ceo__inner">
        <div class="container">
            <div class="aboutus-ceo__grid">
                <div class="aboutus-ceo__portrait">
                    <div class="aboutus-ceo__portrait-bg" aria-hidden="true"></div>
                    <img src="<?php echo esc_url(!empty($image) ? $image : $default_img); ?>"
                         alt="<?php echo esc_attr($name_cal); ?>"
                         class="aboutus-ceo__image"
                         loading="lazy" />
                </div>

                <div class="aboutus-ceo__content">
                    <div class="aboutus-ceo__calligraphy-wrap">
                        <?php if (!empty($accent)) : ?>
                            <span class="aboutus-ceo__accent"><?php echo esc_html($accent); ?></span>
                        <?php endif; ?>
                        <?php if (!empty($name_cal)) : ?>
                            <span class="aboutus-ceo__name-cal"><?php echo esc_html($name_cal); ?></span>
                        <?php endif; ?>
                        <div class="aboutus-ceo__calligraphy">
                            <?php weblazem_render_service_calligraphy('aboutus', 'ceo_calligraphy_image', 'ceo_calligraphy_text'); ?>
                        </div>
                    </div>

                    <?php if (!empty($name_en)) : ?>
                        <h2 class="aboutus-ceo__name-en"><?php echo esc_html($name_en); ?></h2>
                    <?php endif; ?>

                    <?php if (!empty($title_en)) : ?>
                        <p class="aboutus-ceo__title-en"><?php echo esc_html($title_en); ?></p>
                    <?php endif; ?>

                    <?php if (!empty($text)) : ?>
                        <div class="aboutus-ceo__text"><?php echo wp_kses_post(wpautop($text)); ?></div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <?php get_template_part('template-parts/aboutus/part', 'wave', array('variant' => 'light-to-dark', 'position' => 'bottom')); ?>
</section>
