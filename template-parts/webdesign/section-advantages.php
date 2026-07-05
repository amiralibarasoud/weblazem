<?php
/**
 * Website design — advantages / features grid.
 */

$title    = weblazem_webdesign_option('advantages_title', 'مسیر موفقیت، با یک انتخاب درست آغاز می‌شود!');
$subtitle = weblazem_webdesign_option('advantages_subtitle', '');
$items    = get_option('weblazem_webdesign_advantages_items', array());

if (!is_array($items)) {
    $items = array();
}

$items = array_values(array_filter($items, function ($item) {
    return !empty($item['title']);
}));

if (empty($title) && empty($items)) {
    return;
}

$icon_map = array(
    'cube'     => 'fa-cube',
    'document' => 'fa-file-lines',
    'heart'    => 'fa-heart',
    'headset'  => 'fa-headset',
    'layers'   => 'fa-layer-group',
    'rocket'   => 'fa-rocket',
    'chart'    => 'fa-chart-line',
    'nodes'    => 'fa-diagram-project',
);
?>

<section class="webdesign-advantages" dir="rtl">
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
</section>
