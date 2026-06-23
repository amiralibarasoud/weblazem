<?php
/**
 * Global centered floating action bar — phone + consultation modal.
 */

if (!function_exists('weblazem_should_show_consult_floating_btn') || !weblazem_should_show_consult_floating_btn()) {
    return;
}

$phone    = weblazem_get_consult_option('weblazem_consult_phone');
$btn_text = weblazem_get_consult_option('weblazem_consult_btn_text', 'ثبت درخواست مشاوره');

if (empty($phone) && empty($btn_text)) {
    return;
}
?>

<div class="weblazem-consult-float" dir="rtl" aria-label="اقدام سریع">
    <div class="weblazem-consult-float__bar">
        <?php if (!empty($btn_text)) : ?>
            <button type="button" class="weblazem-consult-float__cta weblazem-consult-trigger">
                <i class="fas fa-pen-to-square" aria-hidden="true"></i>
                <span><?php echo esc_html($btn_text); ?></span>
            </button>
        <?php endif; ?>

        <?php if (!empty($phone)) : ?>
            <a href="tel:<?php echo esc_attr(preg_replace('/\s+/', '', $phone)); ?>" class="weblazem-consult-float__phone" dir="ltr">
                <i class="fas fa-phone-volume" aria-hidden="true"></i>
                <span><?php echo esc_html($phone); ?></span>
            </a>
        <?php endif; ?>
    </div>
</div>
