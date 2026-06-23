<?php
/**
 * Global compact floating consultation trigger (all pages except portfolio single).
 */

if (!function_exists('weblazem_should_show_consult_floating_btn') || !weblazem_should_show_consult_floating_btn()) {
    return;
}

$btn_text = weblazem_get_consult_option('weblazem_consult_float_text', 'مشاوره رایگان');
?>

<button
    type="button"
    class="weblazem-consult-float weblazem-consult-trigger"
    aria-label="<?php echo esc_attr($btn_text); ?>"
    dir="rtl"
>
    <i class="fas fa-headset weblazem-consult-float__icon" aria-hidden="true"></i>
    <span class="weblazem-consult-float__text"><?php echo esc_html($btn_text); ?></span>
</button>
