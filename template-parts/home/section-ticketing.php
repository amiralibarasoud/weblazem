<?php
/**
 * Homepage ticketing CTA — links to dedicated ticket page.
 */

if (function_exists('weblazem_is_home_section_enabled') && !weblazem_is_home_section_enabled('ticketing')) {
    return;
}

$title    = get_option('weblazem_ticket_section_title', 'ثبت تیکت و پیگیری تسک');
$subtitle = get_option('weblazem_ticket_section_subtitle', 'پروژه طراحی سایت خود را پیگیری کنید، تیکت ثبت کنید و پاسخ تیم وب‌لازم را دریافت نمایید.');
$btn_text = get_option('weblazem_ticket_section_btn_text', 'ورود به پنل تیکت');
$page_url = function_exists('weblazem_get_ticket_page_url') ? weblazem_get_ticket_page_url() : home_url('/sabt-ticket/');
?>

<section class="weblazem-ticket-cta-section" id="weblazem-ticket-section" dir="rtl">
    <div class="container">
        <div class="weblazem-ticket-cta">
            <div class="weblazem-ticket-cta__content">
                <?php if (!empty($title)) : ?>
                    <h2 class="weblazem-ticket-cta__title"><?php echo esc_html($title); ?></h2>
                <?php endif; ?>
                <?php if (!empty($subtitle)) : ?>
                    <p class="weblazem-ticket-cta__subtitle"><?php echo esc_html($subtitle); ?></p>
                <?php endif; ?>
            </div>
            <a href="<?php echo esc_url($page_url); ?>" class="weblazem-ticket-cta__btn">
                <i class="fas fa-ticket" aria-hidden="true"></i>
                <span><?php echo esc_html($btn_text); ?></span>
            </a>
        </div>
    </div>
</section>
