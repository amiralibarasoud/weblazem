<?php
/**
 * Shared splits (Z-pattern) section.
 *
 * @param array $args prefix, image_dir
 */

$prefix    = $args['prefix'] ?? 'webdesign';
$image_dir = $args['image_dir'] ?? 'webdesign';

$splits = weblazem_service_get_option($prefix, 'splits', array());

if (!is_array($splits)) {
    $splits = array();
}

$splits = array_values(array_filter($splits, function ($item) {
    return !empty($item['title']) || !empty($item['text']);
}));

if (empty($splits)) {
    return;
}
?>

<section class="webdesign-splits" dir="rtl">
    <div class="container">
        <?php foreach ($splits as $index => $split) :
            $layout = (!empty($split['layout']) && $split['layout'] === 'left') ? 'is-image-left' : 'is-image-right';
            $default_img = get_template_directory_uri() . '/assets/images/' . $image_dir . '/split-' . (($index % 3) + 1) . '.svg';
            ?>
            <div class="webdesign-split <?php echo esc_attr($layout); ?>">
                <div class="webdesign-split__content">
                    <?php if (!empty($split['title'])) : ?>
                        <h2 class="webdesign-split__title"><?php echo esc_html($split['title']); ?></h2>
                    <?php endif; ?>

                    <?php if (!empty($split['text'])) : ?>
                        <div class="webdesign-split__text"><?php echo wp_kses_post(wpautop($split['text'])); ?></div>
                    <?php endif; ?>

                    <?php if (!empty($split['button_text'])) : ?>
                        <?php
                        $btn_url  = !empty($split['button_url']) ? $split['button_url'] : '#';
                        $is_modal = !empty($split['button_modal']) && $split['button_modal'] === '1';
                        ?>
                        <?php if ($is_modal) : ?>
                            <button type="button" class="webdesign-split__btn weblazem-consult-trigger">
                                <?php echo esc_html($split['button_text']); ?>
                            </button>
                        <?php else : ?>
                            <a href="<?php echo esc_url($btn_url); ?>" class="webdesign-split__btn">
                                <?php echo esc_html($split['button_text']); ?>
                            </a>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>

                <div class="webdesign-split__media">
                    <img src="<?php echo esc_url(!empty($split['image']) ? $split['image'] : $default_img); ?>"
                         alt=""
                         class="webdesign-split__image" />
                    <?php if (!empty($split['caption'])) : ?>
                        <p class="webdesign-split__caption"><?php echo esc_html($split['caption']); ?></p>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>
