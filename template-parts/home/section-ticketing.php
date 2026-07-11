<?php
/**
 * Homepage ticketing section — login + portal.
 */

if (function_exists('weblazem_is_home_section_enabled') && !weblazem_is_home_section_enabled('ticketing')) {
    return;
}

$title    = get_option('weblazem_ticket_section_title', 'ثبت تیکت و پیگیری تسک');
$subtitle = get_option('weblazem_ticket_section_subtitle', 'پروژه طراحی سایت خود را پیگیری کنید، تیکت ثبت کنید و پاسخ تیم وب‌لازم را دریافت نمایید.');
$subjects = weblazem_ticket_subjects();
$priorities = weblazem_ticket_priorities();
?>

<section class="weblazem-ticket-section" id="weblazem-ticket-section" dir="rtl">
    <div class="container">
        <div class="weblazem-ticket-section__header">
            <?php if (!empty($title)) : ?>
                <h2 class="weblazem-ticket-section__title"><?php echo esc_html($title); ?></h2>
            <?php endif; ?>
            <?php if (!empty($subtitle)) : ?>
                <p class="weblazem-ticket-section__subtitle"><?php echo esc_html($subtitle); ?></p>
            <?php endif; ?>
        </div>

        <div class="weblazem-ticket-panel" id="weblazem-ticket-panel">
            <!-- Login -->
            <div class="weblazem-ticket-view" id="weblazem-ticket-login-view" data-view="login">
                <div class="weblazem-ticket-login">
                    <div class="weblazem-ticket-login__icon"><i class="fas fa-ticket" aria-hidden="true"></i></div>
                    <h3>ورود به پنل تیکت</h3>
                    <p class="weblazem-ticket-login__hint">با نام کاربری دلخواه و کد ورود سندباکس وارد شوید.</p>
                    <form id="weblazem-ticket-login-form" class="weblazem-ticket-form" novalidate>
                        <div class="weblazem-ticket-field">
                            <label for="weblazem-ticket-username">نام کاربری</label>
                            <input type="text" id="weblazem-ticket-username" name="username" required autocomplete="username" placeholder="مثال: ali-project" />
                        </div>
                        <div class="weblazem-ticket-field">
                            <label for="weblazem-ticket-code">کد ورود</label>
                            <input type="text" id="weblazem-ticket-code" name="access_code" required inputmode="numeric" dir="ltr" placeholder="12345" />
                        </div>
                        <p class="weblazem-ticket-feedback" id="weblazem-ticket-login-feedback" role="status"></p>
                        <button type="submit" class="weblazem-ticket-btn weblazem-ticket-btn--primary">ورود به سیستم تیکت</button>
                    </form>
                </div>
            </div>

            <!-- Dashboard -->
            <div class="weblazem-ticket-view" id="weblazem-ticket-dash-view" data-view="dash" hidden>
                <div class="weblazem-ticket-dash-bar">
                    <div>
                        <span class="weblazem-ticket-dash-bar__label">کاربر:</span>
                        <strong id="weblazem-ticket-current-user"></strong>
                    </div>
                    <div class="weblazem-ticket-dash-bar__actions">
                        <button type="button" class="weblazem-ticket-btn weblazem-ticket-btn--ghost" id="weblazem-ticket-new-btn">
                            <i class="fas fa-plus" aria-hidden="true"></i> تیکت جدید
                        </button>
                        <button type="button" class="weblazem-ticket-btn weblazem-ticket-btn--ghost" id="weblazem-ticket-logout-btn">خروج</button>
                    </div>
                </div>

                <div class="weblazem-ticket-list" id="weblazem-ticket-list"></div>
                <p class="weblazem-ticket-empty" id="weblazem-ticket-empty" hidden>هنوز تیکتی ثبت نکرده‌اید. اولین تیکت خود را بسازید.</p>
            </div>

            <!-- Create -->
            <div class="weblazem-ticket-view" id="weblazem-ticket-create-view" data-view="create" hidden>
                <button type="button" class="weblazem-ticket-back" data-ticket-back>&rarr; بازگشت</button>
                <h3>ثبت تیکت جدید</h3>
                <form id="weblazem-ticket-create-form" class="weblazem-ticket-form" novalidate>
                    <div class="weblazem-ticket-grid">
                        <div class="weblazem-ticket-field">
                            <label for="weblazem-ticket-title">عنوان تیکت</label>
                            <input type="text" id="weblazem-ticket-title" name="title" required placeholder="مثال: درخواست تغییر صفحه اصلی" />
                        </div>
                        <div class="weblazem-ticket-field">
                            <label for="weblazem-ticket-subject">موضوع</label>
                            <select id="weblazem-ticket-subject" name="subject" required>
                                <?php foreach ($subjects as $key => $label) : ?>
                                    <option value="<?php echo esc_attr($key); ?>"><?php echo esc_html($label); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="weblazem-ticket-field">
                            <label for="weblazem-ticket-priority">اولویت</label>
                            <select id="weblazem-ticket-priority" name="priority">
                                <?php foreach ($priorities as $key => $label) : ?>
                                    <option value="<?php echo esc_attr($key); ?>" <?php selected($key, 'normal'); ?>><?php echo esc_html($label); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="weblazem-ticket-field">
                            <label for="weblazem-ticket-project">نام پروژه / سایت</label>
                            <input type="text" id="weblazem-ticket-project" name="project_name" placeholder="مثال: فروشگاه فلان" />
                        </div>
                        <div class="weblazem-ticket-field">
                            <label for="weblazem-ticket-mobile">موبایل</label>
                            <input type="tel" id="weblazem-ticket-mobile" name="mobile" dir="ltr" placeholder="0912..." />
                        </div>
                        <div class="weblazem-ticket-field">
                            <label for="weblazem-ticket-email">ایمیل (اختیاری)</label>
                            <input type="email" id="weblazem-ticket-email" name="email" dir="ltr" />
                        </div>
                    </div>
                    <div class="weblazem-ticket-field">
                        <label for="weblazem-ticket-message">شرح درخواست</label>
                        <textarea id="weblazem-ticket-message" name="message" rows="5" required placeholder="جزئیات درخواست، صفحه مورد نظر، تغییرات طراحی و هر نکته مرتبط را بنویسید..."></textarea>
                    </div>
                    <p class="weblazem-ticket-feedback" id="weblazem-ticket-create-feedback" role="status"></p>
                    <button type="submit" class="weblazem-ticket-btn weblazem-ticket-btn--primary">ثبت تیکت</button>
                </form>
            </div>

            <!-- Detail / Chat -->
            <div class="weblazem-ticket-view" id="weblazem-ticket-detail-view" data-view="detail" hidden>
                <button type="button" class="weblazem-ticket-back" data-ticket-back>&rarr; بازگشت به لیست</button>
                <div class="weblazem-ticket-detail-head">
                    <div>
                        <h3 id="weblazem-ticket-detail-title"></h3>
                        <div class="weblazem-ticket-detail-meta" id="weblazem-ticket-detail-meta"></div>
                    </div>
                    <span class="weblazem-ticket-status" id="weblazem-ticket-detail-status"></span>
                </div>
                <div class="weblazem-ticket-chat" id="weblazem-ticket-chat"></div>
                <form id="weblazem-ticket-reply-form" class="weblazem-ticket-reply-form" novalidate>
                    <textarea name="message" rows="3" required placeholder="پاسخ یا پیگیری خود را بنویسید..."></textarea>
                    <div class="weblazem-ticket-reply-form__actions">
                        <p class="weblazem-ticket-feedback" id="weblazem-ticket-reply-feedback" role="status"></p>
                        <button type="submit" class="weblazem-ticket-btn weblazem-ticket-btn--primary">ارسال پیام</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
