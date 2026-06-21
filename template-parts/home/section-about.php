<?php
/**
 * Homepage About Us section.
 */

$about_title       = get_option('weblazem_about_title', '');
$about_text        = get_option('weblazem_about_text', '');
$about_image       = get_option('weblazem_about_image', '');
$about_button_text = get_option('weblazem_about_button_text', '');
$about_button_url  = get_option('weblazem_about_button_url', '#');

if (empty($about_title) && empty($about_text) && empty($about_image)) {
    return;
}
?>

<section class="weblazem-about-section" dir="rtl">
    <div class="container">
        <div class="about-grid">
            <div class="about-media">
                <div class="about-blob about-blob--back" aria-hidden="true"></div>
                <div class="about-blob about-blob--front">
                    <?php if (!empty($about_image)) : ?>
                        <img src="<?php echo esc_url($about_image); ?>" alt="<?php echo esc_attr($about_title); ?>" class="about-image" />
                    <?php else : ?>
                        <div class="about-image about-image--placeholder">
                            <i class="fas fa-building" aria-hidden="true"></i>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="about-content">
                <?php if (!empty($about_title)) : ?>
                    <h2 class="about-title"><?php echo esc_html($about_title); ?></h2>
                <?php endif; ?>

                <?php if (!empty($about_text)) : ?>
                    <div class="about-text"><?php echo wp_kses_post(wpautop($about_text)); ?></div>
                <?php endif; ?>

                <?php if (!empty($about_button_text)) : ?>
                    <a href="<?php echo esc_url($about_button_url); ?>" class="about-button">
                        <?php echo esc_html($about_button_text); ?>
                        <i class="fas fa-arrow-left" aria-hidden="true"></i>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
