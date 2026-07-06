<?php
/**
 * Custom development — customers / trust section.
 */

$logos         = get_option('weblazem_contentsupport_customers_logos', array());
$counter       = weblazem_contentsupport_option('customers_counter', '+120');
$counter_label = weblazem_contentsupport_option('customers_counter_label', 'SOFTWARE PROJECTS');
$bottom_icon   = weblazem_contentsupport_option('customers_bottom_icon', '');

if (!is_array($logos)) {
    $logos = array();
}

$logos = array_values(array_filter($logos, function ($logo) {
    return !empty($logo['logo']);
}));
?>

<section class="webdesign-customers" dir="rtl">
    <div class="webdesign-customers__glow" aria-hidden="true"></div>

    <div class="container">
        <?php if (!empty($logos)) : ?>
            <div class="webdesign-customers__grid">
                <?php foreach ($logos as $logo) : ?>
                    <div class="webdesign-customers__cell">
                        <?php if (!empty($logo['url'])) : ?>
                            <a href="<?php echo esc_url($logo['url']); ?>" target="_blank" rel="noopener noreferrer">
                        <?php endif; ?>
                            <img src="<?php echo esc_url($logo['logo']); ?>"
                                 alt="<?php echo esc_attr($logo['name'] ?? ''); ?>"
                                 loading="lazy" />
                        <?php if (!empty($logo['url'])) : ?>
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="webdesign-customers__counter-row">
            <div class="webdesign-customers__calligraphy">
                <?php weblazem_render_service_calligraphy('contentsupport', 'customers_calligraphy_image', 'customers_calligraphy_text'); ?>
            </div>

            <div class="webdesign-customers__counter">
                <?php if (!empty($counter)) : ?>
                    <span class="webdesign-customers__counter-number"><?php echo esc_html($counter); ?></span>
                <?php endif; ?>
                <?php if (!empty($counter_label)) : ?>
                    <span class="webdesign-customers__counter-label"><?php echo esc_html($counter_label); ?></span>
                <?php endif; ?>
            </div>
        </div>

        <?php if (!empty($bottom_icon)) : ?>
            <div class="webdesign-customers__bottom-icon">
                <img src="<?php echo esc_url($bottom_icon); ?>" alt="" />
            </div>
        <?php endif; ?>
    </div>
</section>
