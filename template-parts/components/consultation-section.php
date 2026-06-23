<?php
/**
 * Global consultation section (homepage + portfolio single).
 *
 * @param string $context home|portfolio_single
 */

$context = $args['context'] ?? 'home';

if (!weblazem_should_show_consult_section($context)) {
    return;
}

$badge    = weblazem_get_consult_option('weblazem_consult_badge');
$title    = weblazem_get_consult_option('weblazem_consult_title');
$text     = weblazem_get_consult_option('weblazem_consult_text');
$image    = weblazem_get_consult_option('weblazem_consult_image');
$phone    = weblazem_get_consult_option('weblazem_consult_phone');
$btn_text = weblazem_get_consult_option('weblazem_consult_btn_text');
?>

<section class="weblazem-consult-section" dir="rtl">
    <div class="weblazem-consult-section__bg-shape weblazem-consult-section__bg-shape--one" aria-hidden="true"></div>
    <div class="weblazem-consult-section__bg-shape weblazem-consult-section__bg-shape--two" aria-hidden="true"></div>

    <div class="container">
        <div class="weblazem-consult-section__panel">
            <div class="weblazem-consult-section__content">
                <?php if (!empty($badge)) : ?>
                    <span class="weblazem-consult-section__badge"><?php echo esc_html($badge); ?></span>
                <?php endif; ?>

                <?php if (!empty($title)) : ?>
                    <h2 class="weblazem-consult-section__title"><?php echo esc_html($title); ?></h2>
                <?php endif; ?>

                <?php if (!empty($text)) : ?>
                    <p class="weblazem-consult-section__text"><?php echo esc_html($text); ?></p>
                <?php endif; ?>

                <div class="weblazem-consult-section__actions">
                    <?php if (!empty($phone)) : ?>
                        <a href="tel:<?php echo esc_attr(preg_replace('/\s+/', '', $phone)); ?>" class="weblazem-consult-section__phone">
                            <i class="fas fa-phone" aria-hidden="true"></i>
                            <span><?php echo esc_html($phone); ?></span>
                        </a>
                    <?php endif; ?>

                    <?php if (!empty($btn_text)) : ?>
                        <button type="button" class="weblazem-consult-section__btn weblazem-consult-trigger">
                            <?php echo esc_html($btn_text); ?>
                            <i class="fas fa-arrow-left" aria-hidden="true"></i>
                        </button>
                    <?php endif; ?>
                </div>
            </div>

            <div class="weblazem-consult-section__media">
                <div class="weblazem-consult-section__media-frame">
                    <div class="weblazem-consult-section__media-wave" aria-hidden="true"></div>
                    <?php if (!empty($image)) : ?>
                        <img src="<?php echo esc_url($image); ?>" alt="" class="weblazem-consult-section__media-image" loading="lazy" />
                    <?php else : ?>
                        <div class="weblazem-consult-section__media-placeholder">
                            <i class="fas fa-user-tie" aria-hidden="true"></i>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <svg class="weblazem-consult-section__wave" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 80" preserveAspectRatio="none" aria-hidden="true">
        <path fill="#f2e6f7" d="M0,40 C300,80 600,0 900,32 C1140,56 1320,24 1440,40 L1440,80 L0,80 Z"/>
    </svg>
</section>
