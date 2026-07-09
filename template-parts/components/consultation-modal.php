<?php
/**
 * Global consultation request modal.
 */

if (weblazem_get_consult_option('weblazem_consult_section_enabled', '1') !== '1') {
    return;
}

$modal_title    = weblazem_get_consult_option('weblazem_consult_modal_title');
$modal_subtitle = weblazem_get_consult_option('weblazem_consult_modal_subtitle');
$label_full_name = weblazem_get_consult_option('weblazem_consult_label_full_name', 'نام و نام خانوادگی');
$label_mobile    = weblazem_get_consult_option('weblazem_consult_label_mobile');
$label_subject   = weblazem_get_consult_option('weblazem_consult_label_subject', 'موضوع');
$subject_choices = weblazem_get_consult_subject_choices();
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
                <label for="weblazem-consult-full-name"><?php echo esc_html($label_full_name); ?></label>
                <input type="text" id="weblazem-consult-full-name" name="full_name" required autocomplete="name" />
            </div>

            <div class="weblazem-consult-modal__field">
                <label for="weblazem-consult-mobile"><?php echo esc_html($label_mobile); ?></label>
                <input type="tel" id="weblazem-consult-mobile" name="mobile" required inputmode="numeric" autocomplete="tel" placeholder="09121234567" dir="ltr" />
            </div>

            <div class="weblazem-consult-modal__field">
                <label for="weblazem-consult-subject"><?php echo esc_html($label_subject); ?></label>
                <select id="weblazem-consult-subject" name="subject" required>
                    <option value="">انتخاب کنید</option>
                    <?php foreach ($subject_choices as $value => $label) : ?>
                        <option value="<?php echo esc_attr($value); ?>"><?php echo esc_html($label); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <p class="weblazem-consult-modal__feedback" id="weblazem-consult-feedback" role="status" aria-live="polite"></p>

            <button type="submit" class="weblazem-consult-modal__submit" id="weblazem-consult-submit">
                <span class="weblazem-consult-modal__submit-text"><?php echo esc_html($submit_text); ?></span>
                <span class="weblazem-consult-modal__submit-loading" hidden>در حال ارسال...</span>
            </button>
        </form>
    </div>
</div>
