<?php
/**
 * Custom development — hero section.
 */

$en_title    = weblazem_devproject_option('hero_en_title', 'Custom Software Development');
$title       = weblazem_devproject_option('hero_title', 'برنامه‌نویسی و پروژه اختصاصی');
$text        = weblazem_devproject_option('hero_text', '');
$image       = weblazem_devproject_option('hero_image', '');
$stat1_num   = weblazem_devproject_option('hero_stat1_number', '+120');
$stat1_ttl   = weblazem_devproject_option('hero_stat1_title', 'پروژه نرم‌افزاری');
$stat1_desc  = weblazem_devproject_option('hero_stat1_desc', '');
$stat2_num   = weblazem_devproject_option('hero_stat2_number', '+50');
$stat2_ttl   = weblazem_devproject_option('hero_stat2_title', 'ماژول و API');
$stat2_desc  = weblazem_devproject_option('hero_stat2_desc', '');
$default_img = get_template_directory_uri() . '/assets/images/devproject/hero-code.svg';
?>

<section class="webdesign-hero" dir="rtl">
    <div class="container">
        <div class="webdesign-hero__grid">
            <div class="webdesign-hero__content" dir="rtl">
                <?php if (!empty($en_title)) : ?>
                    <span class="webdesign-hero__en-title"><?php echo esc_html($en_title); ?></span>
                <?php endif; ?>

                <div class="webdesign-hero__calligraphy">
                    <?php weblazem_render_service_calligraphy('devproject', 'hero_calligraphy_image', 'hero_calligraphy_text', 'webdesign-hero__calligraphy-el'); ?>
                </div>

                <?php if (!empty($title)) : ?>
                    <h1 class="webdesign-hero__title"><?php echo esc_html($title); ?></h1>
                <?php endif; ?>

                <?php if (!empty($text)) : ?>
                    <div class="webdesign-hero__text"><?php echo wp_kses_post(wpautop($text)); ?></div>
                <?php endif; ?>

                <div class="webdesign-hero__stats">
                    <div class="webdesign-hero__stat">
                        <span class="webdesign-hero__stat-number"><?php echo esc_html($stat1_num); ?></span>
                        <strong class="webdesign-hero__stat-title"><?php echo esc_html($stat1_ttl); ?></strong>
                        <?php if (!empty($stat1_desc)) : ?>
                            <span class="webdesign-hero__stat-desc"><?php echo esc_html($stat1_desc); ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="webdesign-hero__stat">
                        <span class="webdesign-hero__stat-number"><?php echo esc_html($stat2_num); ?></span>
                        <strong class="webdesign-hero__stat-title"><?php echo esc_html($stat2_ttl); ?></strong>
                        <?php if (!empty($stat2_desc)) : ?>
                            <span class="webdesign-hero__stat-desc"><?php echo esc_html($stat2_desc); ?></span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="webdesign-hero__visual">
                <img src="<?php echo esc_url(!empty($image) ? $image : $default_img); ?>"
                     alt=""
                     class="webdesign-hero__image" />
            </div>
        </div>
    </div>
</section>
