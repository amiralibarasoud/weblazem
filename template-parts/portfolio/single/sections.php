<?php
/**
 * Single portfolio — flexible two-column sections.
 */

$sections = weblazem_get_portfolio_single_sections();

if (empty($sections)) {
    return;
}
?>

<section class="portfolio-single-sections" dir="rtl">
    <div class="container">
        <?php foreach ($sections as $section) :
            if (empty($section['image']) && empty($section['title']) && empty($section['text'])) {
                continue;
            }

            $layout_class  = ($section['layout'] === 'image-end') ? 'is-image-end' : 'is-image-start';
            $display_class = ($section['display'] === 'mobile') ? 'is-mobile-display' : 'is-desktop-display';
            ?>
            <div class="portfolio-single-section <?php echo esc_attr($layout_class . ' ' . $display_class); ?>">
                <div class="portfolio-single-section__media">
                    <div class="portfolio-single-section__media-inner">
                        <?php if (!empty($section['image'])) : ?>
                            <img src="<?php echo esc_url($section['image']); ?>"
                                 alt="<?php echo esc_attr($section['title']); ?>" />
                        <?php endif; ?>
                    </div>
                </div>

                <div class="portfolio-single-section__content">
                    <?php if (!empty($section['title'])) : ?>
                        <h2 class="portfolio-single-section__title"><?php echo esc_html($section['title']); ?></h2>
                    <?php endif; ?>

                    <?php if (!empty($section['text'])) : ?>
                        <div class="portfolio-single-section__text"><?php echo wp_kses_post(wpautop($section['text'])); ?></div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>
