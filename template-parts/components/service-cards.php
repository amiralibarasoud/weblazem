<?php
/**
 * Shared service cards row (SEO / devproject / about us).
 *
 * @param array $args {
 *     @type string $prefix  Option prefix (webdesign, seo, devproject, …)
 *     @type array  $cards   Optional pre-loaded cards array
 * }
 */

$cards = $args['cards'] ?? null;

if ($cards === null) {
    $prefix = $args['prefix'] ?? 'webdesign';
    $cards  = weblazem_service_get_option($prefix, 'service_cards', array());
}

if (!is_array($cards)) {
    $cards = array();
}

$cards = array_values(array_filter($cards, function ($card) {
    return !empty($card['title']);
}));

if (empty($cards)) {
    return;
}
?>

<div class="webdesign-faq__cards">
    <?php foreach ($cards as $card) :
        $shape_image = !empty($card['shape_image']) ? $card['shape_image'] : ($card['icon'] ?? '');
        ?>
        <a href="<?php echo esc_url(!empty($card['url']) ? $card['url'] : '#'); ?>"
           class="webdesign-service-card">
            <div class="webdesign-service-card__shape" aria-hidden="true">
                <?php if (!empty($shape_image)) : ?>
                    <img src="<?php echo esc_url($shape_image); ?>" alt="" />
                <?php else : ?>
                    <span class="webdesign-service-card__shape-default"></span>
                <?php endif; ?>
            </div>

            <div class="webdesign-service-card__content">
                <h3 class="webdesign-service-card__title"><?php echo esc_html($card['title']); ?></h3>

                <?php if (!empty($card['en_title'])) : ?>
                    <span class="webdesign-service-card__en"><?php echo esc_html($card['en_title']); ?></span>
                <?php endif; ?>

                <?php if (!empty($card['description'])) : ?>
                    <p class="webdesign-service-card__desc"><?php echo esc_html($card['description']); ?></p>
                <?php endif; ?>
            </div>

            <span class="webdesign-service-card__arrow" aria-hidden="true">
                <i class="fas fa-arrow-left"></i>
            </span>
        </a>
    <?php endforeach; ?>
</div>
