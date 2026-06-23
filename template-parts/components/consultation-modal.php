<?php
/**
 * Global consultation request modal.
 */

if (weblazem_get_consult_option('weblazem_consult_section_enabled', '1') !== '1') {
    return;
}

$modal_title    = weblazem_get_consult_option('weblazem_consult_modal_title');
$modal_subtitle = weblazem_get_consult_option('weblazem_consult_modal_subtitle');
$label_first    = weblazem_get_consult_option('weblazem_consult_label_first_name');
$label_last     = weblazem_get_consult_option('weblazem_consult_label_last_name');
$label_mobile   = weblazem_get_consult_option('weblazem_consult_label_mobile');
$submit_text    = weblazem_get_consult_option('weblazem_consult_submit_text');
?>

<div class="weblazem-consult-modal" id="weblazem-consult-modal" aria-hidden="true" hidden>
    <div class="weblazem-consult-modal__overlay" data-consult-close></div>

    <div class="weblazem-consult-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="weblazem-consult-modal-title" dir="rtl">
        <button type="button" class="weblazem-consult-modal__close" data-consult-close aria-label="بستن">
            <i class="fas fa-times" aria-hidden="true"></i>
        </button>

        <div class="weblazem-consult-modal__header">
            <?php if (!empty($modal_title)) : ?>
                <h2 class="weblazem-consult-modal__title" id="weblazem-consult-modal-title"><?php echo esc_html($modal_title); ?></h2>
            <?php endif; ?>

            <?php if (!empty($modal_subtitle)) : ?>
                <p class="weblazem-consult-modal__subtitle"><?php echo esc_html($modal_subtitle); ?></p>
            <?php endif; ?>
        </div>

        <form class="weblazem-consult-modal__form" id="weblazem-consult-form" novalidate>
            <div class="weblazem-consult-modal__field">
                <label for="weblazem-consult-first-name"><?php echo esc_html($label_first); ?></label>
                <input type="text" id="weblazem-consult-first-name" name="first_name" required autocomplete="given-name" />
            </div>

            <div class="weblazem-consult-modal__field">
                <label for="weblazem-consult-last-name"><?php echo esc_html($label_last); ?></label>
                <input type="text" id="weblazem-consult-last-name" name="last_name" required autocomplete="family-name" />
            </div>

            <div class="weblazem-consult-modal__field">
                <label for="weblazem-consult-mobile"><?php echo esc_html($label_mobile); ?></label>
                <input type="tel" id="weblazem-consult-mobile" name="mobile" required inputmode="numeric" autocomplete="tel" placeholder="09121234567" dir="ltr" />
            </div>

            <p class="weblazem-consult-modal__feedback" id="weblazem-consult-feedback" role="status" aria-live="polite"></p>

            <button type="submit" class="weblazem-consult-modal__submit" id="weblazem-consult-submit">
                <span class="weblazem-consult-modal__submit-text"><?php echo esc_html($submit_text); ?></span>
                <span class="weblazem-consult-modal__submit-loading" hidden>در حال ارسال...</span>
            </button>
        </form>
    </div>
</div>
