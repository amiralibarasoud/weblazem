<?php
/**
 * About Us — services cards section.
 */

$cards = weblazem_get_aboutus_service_cards();
$logo  = weblazem_aboutus_option('services_logo', weblazem_aboutus_uri('logo-websima.svg'));
?>

<section class="aboutus-services" dir="rtl">
    <?php get_template_part('template-parts/aboutus/part', 'wave', array('variant' => 'light-fill', 'position' => 'top')); ?>

    <div class="aboutus-services__inner">
        <div class="container">
            <?php if (!empty($cards)) : ?>
                <div class="aboutus-services__grid">
                    <?php foreach ($cards as $card) :
                        if (empty($card['title'])) {
                            continue;
                        }
                        $url = !empty($card['url']) ? $card['url'] : '#';
                        $icon = !empty($card['icon']) ? $card['icon'] : weblazem_aboutus_uri('service-seo.svg');
                        ?>
                        <a href="<?php echo esc_url($url); ?>" class="aboutus-service-card">
                            <div class="aboutus-service-card__icon-wrap">
                                <img src="<?php echo esc_url($icon); ?>" alt="" class="aboutus-service-card__icon" loading="lazy" />
                            </div>
                            <div class="aboutus-service-card__body">
                                <h3 class="aboutus-service-card__title"><?php echo esc_html($card['title']); ?></h3>
                                <?php if (!empty($card['en_title'])) : ?>
                                    <span class="aboutus-service-card__en"><?php echo esc_html($card['en_title']); ?></span>
                                <?php endif; ?>
                                <?php if (!empty($card['description'])) : ?>
                                    <p class="aboutus-service-card__desc"><?php echo esc_html($card['description']); ?></p>
                                <?php endif; ?>
                            </div>
                            <span class="aboutus-service-card__arrow" aria-hidden="true">→</span>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="aboutus-services__footer-wave">
        <?php get_template_part('template-parts/aboutus/part', 'wave', array('variant' => 'light-to-dark', 'position' => 'bottom')); ?>
        <?php if (!empty($logo)) : ?>
            <div class="aboutus-services__logo">
                <img src="<?php echo esc_url($logo); ?>" alt="websima" loading="lazy" />
            </div>
        <?php endif; ?>
    </div>
</section>
