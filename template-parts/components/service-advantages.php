<?php
/**
 * Shared advantages grid section.
 *
 * @param array $args prefix, wave_top (bool)
 */

$prefix    = $args['prefix'] ?? 'webdesign';
$wave_top  = !empty($args['wave_top']);

$title    = weblazem_service_option($prefix, 'advantages_title', '');
$subtitle = weblazem_service_option($prefix, 'advantages_subtitle', '');
$items    = weblazem_service_get_option($prefix, 'advantages_items', array());

if (!is_array($items)) {
    $items = array();
}

$items = array_values(array_filter($items, function ($item) {
    return !empty($item['title']);
}));

if (empty($title) && empty($items)) {
    return;
}

$icon_map = weblazem_get_service_advantage_icons();
?>

<section class="webdesign-advantages<?php echo $wave_top ? ' webdesign-advantages--wave' : ''; ?>" dir="rtl">
    <?php if ($wave_top) : ?>
        <div class="seo-page__wave-decor seo-page__wave-decor--top" aria-hidden="true"></div>
    <?php endif; ?>

    <div class="container">
        <header class="webdesign-advantages__header">
            <?php if (!empty($title)) : ?>
                <h2 class="webdesign-advantages__title"><?php echo esc_html($title); ?></h2>
            <?php endif; ?>

            <?php if (!empty($subtitle)) : ?>
                <p class="webdesign-advantages__subtitle"><?php echo esc_html($subtitle); ?></p>
            <?php endif; ?>
        </header>

        <?php if (!empty($items)) : ?>
            <div class="webdesign-advantages__grid">
                <?php foreach ($items as $item) :
                    $icon_key = $item['icon'] ?? 'cube';
                    $fa_icon  = $icon_map[$icon_key] ?? 'fa-cube';
                    ?>
                    <article class="webdesign-advantage-card">
                        <div class="webdesign-advantage-card__icon" aria-hidden="true">
                            <?php if (!empty($item['icon_image'])) : ?>
                                <img src="<?php echo esc_url($item['icon_image']); ?>" alt="" />
                            <?php else : ?>
                                <i class="fas <?php echo esc_attr($fa_icon); ?>"></i>
                            <?php endif; ?>
                        </div>

                        <?php if (!empty($item['title'])) : ?>
                            <h3 class="webdesign-advantage-card__title"><?php echo esc_html($item['title']); ?></h3>
                        <?php endif; ?>

                        <?php if (!empty($item['text'])) : ?>
                            <p class="webdesign-advantage-card__text"><?php echo esc_html($item['text']); ?></p>
                        <?php endif; ?>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <?php if ($wave_top) : ?>
        <div class="seo-page__wave-decor seo-page__wave-decor--bottom" aria-hidden="true"></div>
    <?php endif; ?>
</section>
