<?php
/**
 * Homepage FAQ section.
 */

$faq_title    = get_option('weblazem_faq_title', '');
$faq_subtitle = get_option('weblazem_faq_subtitle', '');
$faq_items    = get_option('weblazem_faq_items', array());

if (!is_array($faq_items)) {
    $faq_items = array();
}

$faq_items = array_values(array_filter($faq_items, function ($item) {
    return !empty($item['question']);
}));

if (empty($faq_title) && empty($faq_items)) {
    return;
}
?>

<section class="weblazem-faq-section" dir="rtl">
    <div class="container">
        <div class="faq-section-header">
            <?php if (!empty($faq_title)) : ?>
                <h2 class="faq-section-title"><?php echo esc_html($faq_title); ?></h2>
            <?php endif; ?>

            <?php if (!empty($faq_subtitle)) : ?>
                <p class="faq-section-subtitle"><?php echo esc_html($faq_subtitle); ?></p>
            <?php endif; ?>
        </div>

        <?php if (!empty($faq_items)) : ?>
            <div class="faq-grid" data-faq-accordion>
                <?php foreach ($faq_items as $index => $item) : ?>
                    <?php
                    $panel_id  = 'faq-panel-' . (int) $index;
                    $trigger_id = 'faq-trigger-' . (int) $index;
                    ?>
                    <div class="faq-item">
                        <button type="button"
                                class="faq-item__trigger"
                                id="<?php echo esc_attr($trigger_id); ?>"
                                aria-expanded="false"
                                aria-controls="<?php echo esc_attr($panel_id); ?>">
                            <span class="faq-item__question"><?php echo esc_html($item['question']); ?></span>
                            <span class="faq-item__icon" aria-hidden="true">
                                <i class="fas fa-chevron-down"></i>
                            </span>
                        </button>

                        <div class="faq-item__panel"
                             id="<?php echo esc_attr($panel_id); ?>"
                             role="region"
                             aria-labelledby="<?php echo esc_attr($trigger_id); ?>"
                             hidden>
                            <?php if (!empty($item['answer'])) : ?>
                                <div class="faq-item__answer"><?php echo wp_kses_post(wpautop($item['answer'])); ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
