<?php
/**
 * Project status portal markup.
 */

$opts = function_exists('weblazem_get_project_status_options') ? weblazem_get_project_status_options() : array();
$title = !empty($opts['title']) ? $opts['title'] : 'وضعیت پروژه';
$subtitle = !empty($opts['subtitle']) ? $opts['subtitle'] : '';
$login_intro = !empty($opts['login_intro']) ? $opts['login_intro'] : 'با شماره موبایل و کد ورود وارد شوید.';
?>

<section class="wl-ps" id="weblazem-project-status-portal" dir="rtl">
    <div class="wl-ps__bg" aria-hidden="true"></div>
    <div class="container">
        <header class="wl-ps__header">
            <p class="wl-ps__brand">وب‌لازم</p>
            <h1 class="wl-ps__title"><?php echo esc_html($title); ?></h1>
            <?php if ($subtitle !== '') : ?>
                <p class="wl-ps__subtitle"><?php echo esc_html($subtitle); ?></p>
            <?php endif; ?>
        </header>

        <div class="wl-ps__panel" id="weblazem-ps-panel">
            <div class="wl-ps__view" data-view="login" id="wl-ps-login-view">
                <div class="wl-ps__login">
                    <div class="wl-ps__login-icon" aria-hidden="true"><i class="fas fa-tasks"></i></div>
                    <h2>ورود به پورتال پروژه</h2>
                    <p class="wl-ps__hint"><?php echo esc_html($login_intro); ?></p>
                    <form id="wl-ps-login-form" class="wl-ps__form" autocomplete="off" novalidate>
                        <div class="wl-ps__field">
                            <label for="wl-ps-mobile">شماره موبایل</label>
                            <input type="tel" id="wl-ps-mobile" name="mobile" required inputmode="numeric" dir="ltr" maxlength="13" placeholder="09121234567" autocomplete="tel" />
                        </div>
                        <div class="wl-ps__field">
                            <label for="wl-ps-code">کد ورود</label>
                            <input type="password" id="wl-ps-code" name="access_code" required inputmode="numeric" dir="ltr" maxlength="32" placeholder="•••••" autocomplete="current-password" />
                        </div>
                        <p class="wl-ps__feedback" id="wl-ps-login-feedback" role="status" aria-live="polite"></p>
                        <button type="submit" class="wl-ps__btn wl-ps__btn--primary">ورود</button>
                    </form>
                </div>
            </div>

            <div class="wl-ps__view" data-view="list" id="wl-ps-list-view" hidden>
                <div class="wl-ps__bar">
                    <div>
                        <span class="wl-ps__bar-label">موبایل:</span>
                        <strong id="wl-ps-current-user" dir="ltr"></strong>
                    </div>
                    <button type="button" class="wl-ps__btn wl-ps__btn--ghost" id="wl-ps-logout">خروج</button>
                </div>
                <div class="wl-ps__list" id="wl-ps-list"></div>
                <p class="wl-ps__empty" id="wl-ps-empty" hidden>پروژه‌ای برای این شماره ثبت نشده است.</p>
            </div>

            <div class="wl-ps__view" data-view="detail" id="wl-ps-detail-view" hidden>
                <button type="button" class="wl-ps__back" id="wl-ps-back">&rarr; بازگشت به لیست</button>
                <div id="wl-ps-detail"></div>
            </div>
        </div>
    </div>
</section>
