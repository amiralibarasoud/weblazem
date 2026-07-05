<?php
/**
 * Website design — hero section.
 */

$en_title   = weblazem_webdesign_option('hero_en_title', 'Website Design');
$title      = weblazem_webdesign_option('hero_title', 'طراحی وب‌سایت');
$text       = weblazem_webdesign_option('hero_text', '');
$image      = weblazem_webdesign_option('hero_image', '');
$stat1_num  = weblazem_webdesign_option('hero_stat1_number', '+700');
$stat1_ttl  = weblazem_webdesign_option('hero_stat1_title', 'پروژه موفق');
$stat1_desc = weblazem_webdesign_option('hero_stat1_desc', 'با همراهی معتبرترین برندهای کشور');
$stat2_num  = weblazem_webdesign_option('hero_stat2_number', '+80');
$stat2_ttl  = weblazem_webdesign_option('hero_stat2_title', 'ماژول اختصاصی');
$stat2_desc = weblazem_webdesign_option('hero_stat2_desc', 'هماهنگ پایدار و امن');
$default_img = get_template_directory_uri() . '/assets/images/webdesign/hero-monitor.svg';
?>

<section class="webdesign-hero" dir="rtl">
    <div class="container">
        <div class="webdesign-hero__grid">
            <div class="webdesign-hero__visual">
                <img src="<?php echo esc_url(!empty($image) ? $image : $default_img); ?>"
                     alt=""
                     class="webdesign-hero__image" />
            </div>

            <div class="webdesign-hero__content">
                <?php if (!empty($en_title)) : ?>
                    <span class="webdesign-hero__en-title"><?php echo esc_html($en_title); ?></span>
                <?php endif; ?>

                <div class="webdesign-hero__calligraphy">
                    <?php weblazem_render_webdesign_calligraphy('hero_calligraphy_image', 'hero_calligraphy_text', 'webdesign-hero__calligraphy-el'); ?>
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
        </div>
    </div>
</section>
