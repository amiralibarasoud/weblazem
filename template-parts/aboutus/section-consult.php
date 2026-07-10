<?php
/**
 * About Us — consultation / project request CTA.
 */

$title     = weblazem_aboutus_option('consult_title', 'مشاوره و درخواست اجرای پروژه');
$text      = weblazem_aboutus_option('consult_text', '');
$btn_text  = weblazem_aboutus_option('consult_btn_text', 'ثبت درخواست مشاوره');
$btn_url   = weblazem_aboutus_option('consult_btn_url', '');
$use_modal = weblazem_aboutus_option('consult_btn_modal', '1') === '1';
?>

<section class="pricing-page-consult aboutus-consult" dir="rtl">
    <div class="pricing-page-consult__bg" aria-hidden="true"></div>

    <div class="container">
        <div class="pricing-page-consult__card">
            <div class="pricing-page-consult__content">
                <?php if ($title) : ?>
                    <h2 class="pricing-page-consult__title"><?php echo esc_html($title); ?></h2>
                <?php endif; ?>

                <?php if ($text) : ?>
                    <p class="pricing-page-consult__text"><?php echo esc_html($text); ?></p>
                <?php endif; ?>
            </div>

            <?php if ($btn_text) : ?>
                <?php if ($use_modal) : ?>
                    <button type="button" class="pricing-page-consult__btn weblazem-consult-trigger">
                        <?php echo esc_html($btn_text); ?>
                        <i class="fas fa-arrow-left" aria-hidden="true"></i>
                    </button>
                <?php else : ?>
                    <a href="<?php echo esc_url($btn_url ?: '#'); ?>" class="pricing-page-consult__btn">
                        <?php echo esc_html($btn_text); ?>
                        <i class="fas fa-arrow-left" aria-hidden="true"></i>
                    </a>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</section>
