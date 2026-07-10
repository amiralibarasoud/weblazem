<?php
/**
 * About Us — journey / timeline section.
 */

$subtitle = weblazem_aboutus_option('journey_subtitle', 'با هم ساختیم، با هم پیش می‌رویم');
$intro    = weblazem_aboutus_option('journey_intro', '');
$items    = weblazem_get_aboutus_journey_items();
?>

<section class="aboutus-journey" dir="rtl">
    <?php get_template_part('template-parts/aboutus/part', 'wave', array('variant' => 'dark-fill', 'position' => 'top')); ?>

    <div class="aboutus-journey__inner">
        <div class="container">
            <header class="aboutus-journey__header">
                <div class="aboutus-journey__calligraphy">
                    <?php weblazem_render_service_calligraphy('aboutus', 'journey_calligraphy_image', 'journey_calligraphy_text'); ?>
                </div>

                <?php if (!empty($subtitle)) : ?>
                    <h2 class="aboutus-journey__subtitle"><?php echo esc_html($subtitle); ?></h2>
                <?php endif; ?>

                <?php if (!empty($intro)) : ?>
                    <div class="aboutus-journey__intro"><?php echo wp_kses_post(wpautop($intro)); ?></div>
                <?php endif; ?>
            </header>

            <?php if (!empty($items)) : ?>
                <div class="aboutus-journey__track" role="list">
                    <?php foreach ($items as $item) :
                        if (empty($item['title'])) {
                            continue;
                        }
                        $img = !empty($item['image']) ? $item['image'] : weblazem_aboutus_uri('journey-card.svg');
                        ?>
                        <article class="aboutus-journey-card" role="listitem">
                            <div class="aboutus-journey-card__media">
                                <img src="<?php echo esc_url($img); ?>"
                                     alt=""
                                     class="aboutus-journey-card__image"
                                     loading="lazy" />
                                <?php if (!empty($item['year'])) : ?>
                                    <span class="aboutus-journey-card__year"><?php echo esc_html($item['year']); ?></span>
                                <?php endif; ?>
                            </div>
                            <h3 class="aboutus-journey-card__title"><?php echo esc_html($item['title']); ?></h3>
                            <?php if (!empty($item['description'])) : ?>
                                <p class="aboutus-journey-card__text"><?php echo esc_html($item['description']); ?></p>
                            <?php endif; ?>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php get_template_part('template-parts/aboutus/part', 'wave', array('variant' => 'dark-to-light', 'position' => 'bottom')); ?>
</section>
