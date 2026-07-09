<?php
/**
 * Pricing page — service tariff cards (support / SEO / content).
 */

$title = weblazem_pricing_option('service_tariffs_title', 'تعرفه ها');
$intro = weblazem_pricing_option('service_tariffs_intro');
$cards = weblazem_get_pricing_service_tariffs();

if (empty($cards)) {
    return;
}
?>

<section class="pricing-page-service-tariffs" dir="rtl">
    <div class="pricing-page-service-tariffs__blob" aria-hidden="true"></div>

    <div class="container">
        <div class="pricing-page-service-tariffs__header">
            <?php if ($title) : ?>
                <h2 class="pricing-page-service-tariffs__title"><?php echo esc_html($title); ?></h2>
            <?php endif; ?>

            <?php if ($intro) : ?>
                <p class="pricing-page-service-tariffs__intro"><?php echo esc_html($intro); ?></p>
            <?php endif; ?>
        </div>

        <div class="pricing-page-service-tariffs__grid">
            <?php foreach ($cards as $card) :
                if (empty($card['title'])) {
                    continue;
                }
                $btn_text  = !empty($card['button_text']) ? $card['button_text'] : 'نمایش بیشتر';
                $use_modal = !empty($card['button_modal']) && $card['button_modal'] === '1';
                $btn_url   = !empty($card['button_url']) ? $card['button_url'] : '#';
                $image     = !empty($card['image']) ? $card['image'] : weblazem_pricing_uri('tariff-card.svg');
                ?>
                <article class="pricing-page-service-tariffs__card">
                    <div class="pricing-page-service-tariffs__media">
                        <div class="pricing-page-service-tariffs__wave" aria-hidden="true"></div>
                        <img src="<?php echo esc_url($image); ?>" alt="" loading="lazy" />
                    </div>

                    <div class="pricing-page-service-tariffs__body">
                        <h3 class="pricing-page-service-tariffs__card-title"><?php echo esc_html($card['title']); ?></h3>

                        <?php if (!empty($card['description'])) : ?>
                            <p class="pricing-page-service-tariffs__card-text"><?php echo esc_html($card['description']); ?></p>
                        <?php endif; ?>

                        <?php if ($use_modal) : ?>
                            <button type="button" class="pricing-page-service-tariffs__btn weblazem-consult-trigger">
                                <?php echo esc_html($btn_text); ?>
                            </button>
                        <?php else : ?>
                            <a href="<?php echo esc_url($btn_url); ?>" class="pricing-page-service-tariffs__btn">
                                <?php echo esc_html($btn_text); ?>
                            </a>
                        <?php endif; ?>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
