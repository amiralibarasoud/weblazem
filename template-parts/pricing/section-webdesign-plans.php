<?php
/**
 * Pricing page — webdesign pricing plans (SEO-style cards).
 */

$title       = weblazem_pricing_option('webdesign_plans_title', 'تعرفه طراحی سایت');
$price_label = weblazem_pricing_option('webdesign_plans_price_label', 'قیمت');
$plans       = weblazem_get_pricing_webdesign_plans();

if (empty($plans)) {
    return;
}
?>

<section class="pricing-page-plans" dir="rtl">
    <div class="container">
        <?php if ($title) : ?>
            <h2 class="pricing-page-plans__title"><?php echo esc_html($title); ?></h2>
        <?php endif; ?>

        <div class="pricing-page-plans__grid">
            <?php foreach ($plans as $plan) :
                if (empty($plan['title']) && empty($plan['price'])) {
                    continue;
                }
                $features = isset($plan['features']) && is_array($plan['features']) ? $plan['features'] : array();
                $btn_text = !empty($plan['button_text']) ? $plan['button_text'] : 'مشاوره رایگان';
                $use_modal = !empty($plan['button_modal']) && $plan['button_modal'] === '1';
                $btn_url   = !empty($plan['button_url']) ? $plan['button_url'] : '#';
                ?>
                <article class="pricing-page-plans__card">
                    <div class="pricing-page-plans__blob" aria-hidden="true"></div>

                    <div class="pricing-page-plans__card-body">
                        <?php if (!empty($plan['title'])) : ?>
                            <h3 class="pricing-page-plans__card-title"><?php echo esc_html($plan['title']); ?></h3>
                        <?php endif; ?>

                        <?php if (!empty($features)) : ?>
                            <ul class="pricing-page-plans__features">
                                <?php foreach ($features as $feature) :
                                    if ($feature === '') {
                                        continue;
                                    }
                                    ?>
                                    <li><?php echo esc_html($feature); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>

                        <?php if (!empty($plan['price'])) : ?>
                            <div class="pricing-page-plans__price">
                                <span class="pricing-page-plans__price-label"><?php echo esc_html($price_label); ?>:</span>
                                <span class="pricing-page-plans__price-value"><?php echo esc_html($plan['price']); ?></span>
                            </div>
                        <?php endif; ?>

                        <?php if ($use_modal) : ?>
                            <button type="button" class="pricing-page-plans__btn weblazem-consult-trigger">
                                <?php echo esc_html($btn_text); ?>
                            </button>
                        <?php else : ?>
                            <a href="<?php echo esc_url($btn_url); ?>" class="pricing-page-plans__btn">
                                <?php echo esc_html($btn_text); ?>
                            </a>
                        <?php endif; ?>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
