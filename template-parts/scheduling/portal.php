<?php
/**
 * Scheduling portal markup.
 */

$opts = function_exists('weblazem_get_scheduling_options') ? weblazem_get_scheduling_options() : array();
$title = !empty($opts['title']) ? $opts['title'] : 'رزرو مشاوره رایگان';
$subtitle = !empty($opts['subtitle']) ? $opts['subtitle'] : '';
$enabled = !isset($opts['enabled']) || !empty($opts['enabled']);
?>

<section class="wl-sched" id="weblazem-scheduling-portal" dir="rtl">
    <div class="wl-sched__bg" aria-hidden="true"></div>
    <div class="container">
        <header class="wl-sched__header">
            <p class="wl-sched__brand">وب‌لازم</p>
            <h1 class="wl-sched__title"><?php echo esc_html($title); ?></h1>
            <?php if ($subtitle !== '') : ?>
                <p class="wl-sched__subtitle"><?php echo esc_html($subtitle); ?></p>
            <?php endif; ?>
        </header>

        <?php if (!$enabled) : ?>
            <div class="wl-sched__panel">
                <p class="wl-sched__disabled">رزرو مشاوره فعلاً در دسترس نیست.</p>
            </div>
        <?php else : ?>
            <div class="wl-sched__panel" id="weblazem-scheduling-panel">
                <div class="wl-sched__steps" aria-hidden="true">
                    <span class="wl-sched__step is-active" data-step-indicator="1">۱. روز</span>
                    <span class="wl-sched__step" data-step-indicator="2">۲. ساعت</span>
                    <span class="wl-sched__step" data-step-indicator="3">۳. اطلاعات</span>
                </div>

                <div class="wl-sched__view" data-view="dates" id="wl-sched-dates-view">
                    <h2 class="wl-sched__view-title">روز مورد نظر را انتخاب کنید</h2>
                    <div class="wl-sched__dates" id="wl-sched-dates" role="listbox" aria-label="روزهای قابل رزرو"></div>
                    <p class="wl-sched__feedback" id="wl-sched-dates-feedback" role="status" aria-live="polite"></p>
                </div>

                <div class="wl-sched__view" data-view="times" id="wl-sched-times-view" hidden>
                    <button type="button" class="wl-sched__back" data-sched-back="dates">&rarr; تغییر روز</button>
                    <h2 class="wl-sched__view-title">ساعت جلسه را انتخاب کنید</h2>
                    <p class="wl-sched__selected-date" id="wl-sched-selected-date"></p>
                    <div class="wl-sched__times" id="wl-sched-times" role="listbox" aria-label="ساعات خالی"></div>
                    <p class="wl-sched__feedback" id="wl-sched-times-feedback" role="status" aria-live="polite"></p>
                </div>

                <div class="wl-sched__view" data-view="form" id="wl-sched-form-view" hidden>
                    <button type="button" class="wl-sched__back" data-sched-back="times">&rarr; تغییر ساعت</button>
                    <h2 class="wl-sched__view-title">اطلاعات تماس</h2>
                    <p class="wl-sched__summary" id="wl-sched-summary"></p>
                    <form id="wl-sched-form" class="wl-sched__form" novalidate autocomplete="on">
                        <div class="wl-sched__field">
                            <label for="wl-sched-name">نام و نام خانوادگی</label>
                            <input type="text" id="wl-sched-name" name="name" required maxlength="120" placeholder="مثال: علی محمدی" />
                        </div>
                        <div class="wl-sched__field">
                            <label for="wl-sched-mobile">شماره موبایل</label>
                            <input type="tel" id="wl-sched-mobile" name="mobile" required inputmode="numeric" dir="ltr" maxlength="13" placeholder="09121234567" autocomplete="tel" />
                        </div>
                        <div class="wl-sched__field">
                            <label for="wl-sched-note">توضیح کوتاه (اختیاری)</label>
                            <textarea id="wl-sched-note" name="note" rows="3" maxlength="500" placeholder="موضوع مشاوره یا سوالاتتان…"></textarea>
                        </div>
                        <p class="wl-sched__feedback" id="wl-sched-form-feedback" role="status" aria-live="polite"></p>
                        <button type="submit" class="wl-sched__btn wl-sched__btn--primary" id="wl-sched-submit">ثبت رزرو</button>
                    </form>
                </div>

                <div class="wl-sched__view" data-view="success" id="wl-sched-success-view" hidden>
                    <div class="wl-sched__success">
                        <div class="wl-sched__success-icon" aria-hidden="true"><i class="fas fa-check"></i></div>
                        <h2>رزرو ثبت شد</h2>
                        <p id="wl-sched-success-message"></p>
                        <p class="wl-sched__success-meta" id="wl-sched-success-meta"></p>
                        <button type="button" class="wl-sched__btn wl-sched__btn--ghost" id="wl-sched-again">رزرو جدید</button>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>
