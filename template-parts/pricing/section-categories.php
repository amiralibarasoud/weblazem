<?php
/**
 * Pricing page — service categories grid.
 */

$categories = weblazem_get_pricing_categories();

if (empty($categories)) {
    return;
}
?>

<section class="pricing-page-categories" dir="rtl">
    <div class="container">
        <div class="pricing-page-categories__grid">
            <?php foreach ($categories as $item) :
                if (empty($item['title'])) {
                    continue;
                }
                $url = !empty($item['url']) ? $item['url'] : '#';
                ?>
                <a href="<?php echo esc_url($url); ?>" class="pricing-page-categories__card">
                    <span class="pricing-page-categories__bar" aria-hidden="true"></span>
                    <span class="pricing-page-categories__title"><?php echo esc_html($item['title']); ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
