<?php
/**
 * SEO page — pricing plans (تعرفه‌ها).
 */

$title       = weblazem_seo_option('tariffs_title', 'پلن های سئو با خدمات وب‌لازم');
$price_label = weblazem_seo_option('tariffs_price_label', 'قیمت');
$plans       = weblazem_get_seo_pricing_plans();

if (empty($plans)) {
    return;
}
?>

<section class="seo-page-tariffs" dir="rtl">
    <div class="container">
        <?php if ($title) : ?>
            <h2 class="seo-page-tariffs__title"><?php echo esc_html($title); ?></h2>
        <?php endif; ?>

        <div class="seo-page-tariffs__grid">
            <?php foreach ($plans as $plan) :
                if (empty($plan['title']) && empty($plan['price'])) {
                    continue;
                }
                $features = isset($plan['features']) && is_array($plan['features']) ? $plan['features'] : array();
                $btn_text = !empty($plan['button_text']) ? $plan['button_text'] : 'مشاوره رایگان';
                $use_modal = !empty($plan['button_modal']) && $plan['button_modal'] === '1';
                $btn_url   = !empty($plan['button_url']) ? $plan['button_url'] : '#';
                ?>
                <article class="seo-page-tariffs__card">
                    <div class="seo-page-tariffs__blob" aria-hidden="true"></div>

                    <div class="seo-page-tariffs__card-body">
                        <?php if (!empty($plan['title'])) : ?>
                            <h3 class="seo-page-tariffs__card-title"><?php echo esc_html($plan['title']); ?></h3>
                        <?php endif; ?>

                        <?php if (!empty($features)) : ?>
                            <ul class="seo-page-tariffs__features">
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
                            <div class="seo-page-tariffs__price">
                                <span class="seo-page-tariffs__price-label"><?php echo esc_html($price_label); ?>:</span>
                                <span class="seo-page-tariffs__price-value"><?php echo esc_html($plan['price']); ?></span>
                            </div>
                        <?php endif; ?>

                        <?php if ($use_modal) : ?>
                            <button type="button" class="seo-page-tariffs__btn weblazem-consult-trigger">
                                <?php echo esc_html($btn_text); ?>
                            </button>
                        <?php else : ?>
                            <a href="<?php echo esc_url($btn_url); ?>" class="seo-page-tariffs__btn">
                                <?php echo esc_html($btn_text); ?>
                            </a>
                        <?php endif; ?>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
