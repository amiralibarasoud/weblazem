<?php
/**
 * Client account portal — overview, tickets, briefs, proposals, projects.
 */

$page_title    = get_option('weblazem_ticket_page_title', 'حساب کاربری مشتری');
$page_subtitle = get_option('weblazem_ticket_page_subtitle', 'با شماره موبایل وارد شوید؛ تیکت‌ها، بریف‌ها، پیشنهادها و وضعیت پروژه‌ها را مدیریت کنید.');
$subjects      = weblazem_ticket_subjects();
$priorities    = weblazem_ticket_priorities();
?>

<section class="weblazem-ticket-section weblazem-ticket-section--page weblazem-account" id="weblazem-ticket-portal" dir="rtl">
    <div class="container">
        <div class="weblazem-ticket-section__header">
            <?php if (!empty($page_title)) : ?>
                <h1 class="weblazem-ticket-section__title"><?php echo esc_html($page_title); ?></h1>
            <?php endif; ?>
            <?php if (!empty($page_subtitle)) : ?>
                <p class="weblazem-ticket-section__subtitle"><?php echo esc_html($page_subtitle); ?></p>
            <?php endif; ?>
        </div>

        <div class="weblazem-ticket-panel weblazem-account-panel" id="weblazem-ticket-panel">
            <!-- Login -->
            <div class="weblazem-ticket-view" id="weblazem-ticket-login-view" data-view="login">
                <div class="weblazem-ticket-login">
                    <div class="weblazem-ticket-login__icon"><i class="fas fa-user-circle" aria-hidden="true"></i></div>
                    <h2>ورود به حساب کاربری</h2>
                    <p class="weblazem-ticket-login__hint">با شماره موبایل و کد ورود وارد شوید تا تیکت‌ها، بریف‌ها، پیشنهادهای قیمت و وضعیت پروژه را ببینید.</p>
                    <form id="weblazem-ticket-login-form" class="weblazem-ticket-form" autocomplete="off" novalidate>
                        <div class="weblazem-ticket-field">
                            <label for="weblazem-ticket-mobile-login">شماره موبایل</label>
                            <input type="tel" id="weblazem-ticket-mobile-login" name="mobile" required inputmode="numeric" dir="ltr" autocomplete="tel" placeholder="09121234567" maxlength="13" />
                        </div>
                        <div class="weblazem-ticket-field">
                            <label for="weblazem-ticket-code">کد ورود</label>
                            <input type="password" id="weblazem-ticket-code" name="access_code" required inputmode="numeric" dir="ltr" autocomplete="current-password" placeholder="•••••" maxlength="32" />
                        </div>
                        <p class="weblazem-ticket-feedback" id="weblazem-ticket-login-feedback" role="status" aria-live="polite"></p>
                        <button type="submit" class="weblazem-ticket-btn weblazem-ticket-btn--primary">ورود به حساب کاربری</button>
                    </form>
                </div>
            </div>

            <!-- Account shell -->
            <div class="weblazem-ticket-view" id="weblazem-ticket-dash-view" data-view="dash" hidden>
                <div class="weblazem-ticket-dash-bar weblazem-account-bar">
                    <div>
                        <span class="weblazem-ticket-dash-bar__label">موبایل:</span>
                        <strong id="weblazem-ticket-current-user" dir="ltr"></strong>
                    </div>
                    <div class="weblazem-ticket-dash-bar__actions">
                        <button type="button" class="weblazem-ticket-btn weblazem-ticket-btn--ghost" id="weblazem-ticket-new-btn">
                            <i class="fas fa-plus" aria-hidden="true"></i> تیکت جدید
                        </button>
                        <button type="button" class="weblazem-ticket-btn weblazem-ticket-btn--ghost" id="weblazem-ticket-logout-btn">خروج</button>
                    </div>
                </div>

                <nav class="weblazem-account-tabs" id="weblazem-account-tabs" aria-label="بخش‌های حساب کاربری">
                    <button type="button" class="weblazem-account-tab is-active" data-account-tab="overview">خلاصه</button>
                    <button type="button" class="weblazem-account-tab" data-account-tab="tickets">تیکت‌ها</button>
                    <button type="button" class="weblazem-account-tab" data-account-tab="briefs">بریف‌ها</button>
                    <button type="button" class="weblazem-account-tab" data-account-tab="proposals">پیشنهادها</button>
                    <button type="button" class="weblazem-account-tab" data-account-tab="projects">پروژه‌ها</button>
                </nav>

                <!-- Overview -->
                <div class="weblazem-account-pane is-active" id="weblazem-account-pane-overview" data-pane="overview">
                    <div class="weblazem-account-stats" id="weblazem-account-stats"></div>
                    <div class="weblazem-account-overview-grid">
                        <div class="weblazem-account-block">
                            <div class="weblazem-account-block__head">
                                <h3>آخرین تیکت‌ها</h3>
                                <button type="button" class="weblazem-account-link" data-goto-tab="tickets">همه</button>
                            </div>
                            <div id="weblazem-account-overview-tickets"></div>
                        </div>
                        <div class="weblazem-account-block">
                            <div class="weblazem-account-block__head">
                                <h3>آخرین پیشنهادها</h3>
                                <button type="button" class="weblazem-account-link" data-goto-tab="proposals">همه</button>
                            </div>
                            <div id="weblazem-account-overview-proposals"></div>
                        </div>
                    </div>
                </div>

                <!-- Tickets list -->
                <div class="weblazem-account-pane" id="weblazem-account-pane-tickets" data-pane="tickets" hidden>
                    <div class="weblazem-ticket-list" id="weblazem-ticket-list"></div>
                    <p class="weblazem-ticket-empty" id="weblazem-ticket-empty" hidden>هنوز تیکتی ثبت نکرده‌اید. اولین تیکت خود را بسازید.</p>
                </div>

                <!-- Briefs -->
                <div class="weblazem-account-pane" id="weblazem-account-pane-briefs" data-pane="briefs" hidden>
                    <div class="weblazem-account-list" id="weblazem-account-briefs-list"></div>
                    <p class="weblazem-ticket-empty" id="weblazem-account-briefs-empty" hidden>بریفی ثبت نشده است. از صفحه «شروع پروژه» بریف بفرستید.</p>
                </div>

                <!-- Proposals list -->
                <div class="weblazem-account-pane" id="weblazem-account-pane-proposals" data-pane="proposals" hidden>
                    <div class="weblazem-account-list" id="weblazem-account-proposals-list"></div>
                    <p class="weblazem-ticket-empty" id="weblazem-account-proposals-empty" hidden>هنوز پیشنهادی برای شما ارسال نشده است.</p>
                </div>

                <!-- Projects -->
                <div class="weblazem-account-pane" id="weblazem-account-pane-projects" data-pane="projects" hidden>
                    <div class="weblazem-account-list" id="weblazem-account-projects-list"></div>
                    <p class="weblazem-ticket-empty" id="weblazem-account-projects-empty" hidden>پروژه‌ای برای این شماره ثبت نشده است.</p>
                </div>
            </div>

            <!-- Create ticket -->
            <div class="weblazem-ticket-view" id="weblazem-ticket-create-view" data-view="create" hidden>
                <button type="button" class="weblazem-ticket-back" data-ticket-back data-back-to="tickets">&rarr; بازگشت</button>
                <h2>ثبت تیکت جدید</h2>
                <form id="weblazem-ticket-create-form" class="weblazem-ticket-form" enctype="multipart/form-data" novalidate>
                    <div class="weblazem-ticket-grid">
                        <div class="weblazem-ticket-field">
                            <label for="weblazem-ticket-title">عنوان تیکت</label>
                            <input type="text" id="weblazem-ticket-title" name="title" required maxlength="160" placeholder="مثال: درخواست تغییر صفحه اصلی" />
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
                            <input type="text" id="weblazem-ticket-project" name="project_name" maxlength="120" placeholder="مثال: فروشگاه فلان" />
                        </div>
                        <div class="weblazem-ticket-field">
                            <label for="weblazem-ticket-email">ایمیل (اختیاری)</label>
                            <input type="email" id="weblazem-ticket-email" name="email" dir="ltr" maxlength="120" />
                        </div>
                        <div class="weblazem-ticket-field">
                            <label for="weblazem-ticket-attachment">پیوست فایل (حداکثر ۳ مگابایت)</label>
                            <input type="file" id="weblazem-ticket-attachment" name="attachment" accept=".jpg,.jpeg,.png,.gif,.webp,.pdf,.zip,.doc,.docx,image/*,application/pdf" />
                            <p class="weblazem-ticket-field__hint">فرمت‌های مجاز: تصویر، PDF، ZIP، Word</p>
                        </div>
                    </div>
                    <div class="weblazem-ticket-field">
                        <label for="weblazem-ticket-message">شرح درخواست</label>
                        <textarea id="weblazem-ticket-message" name="message" rows="5" required maxlength="5000" placeholder="جزئیات درخواست، صفحه مورد نظر، تغییرات طراحی و هر نکته مرتبط را بنویسید..."></textarea>
                    </div>
                    <p class="weblazem-ticket-feedback" id="weblazem-ticket-create-feedback" role="status" aria-live="polite"></p>
                    <button type="submit" class="weblazem-ticket-btn weblazem-ticket-btn--primary" id="weblazem-ticket-create-submit">ثبت تیکت</button>
                </form>
            </div>

            <!-- Ticket detail -->
            <div class="weblazem-ticket-view" id="weblazem-ticket-detail-view" data-view="detail" hidden>
                <button type="button" class="weblazem-ticket-back" data-ticket-back data-back-to="tickets">&rarr; بازگشت به تیکت‌ها</button>
                <div class="weblazem-ticket-detail-head">
                    <div>
                        <h2 id="weblazem-ticket-detail-title"></h2>
                        <div class="weblazem-ticket-detail-meta" id="weblazem-ticket-detail-meta"></div>
                    </div>
                    <span class="weblazem-ticket-status" id="weblazem-ticket-detail-status"></span>
                </div>
                <div class="weblazem-ticket-chat" id="weblazem-ticket-chat"></div>
                <form id="weblazem-ticket-reply-form" class="weblazem-ticket-reply-form" enctype="multipart/form-data" novalidate>
                    <textarea name="message" rows="3" required maxlength="5000" placeholder="پاسخ یا پیگیری خود را بنویسید..."></textarea>
                    <div class="weblazem-ticket-field">
                        <label for="weblazem-ticket-reply-attachment">پیوست (اختیاری، حداکثر ۳ مگابایت)</label>
                        <input type="file" id="weblazem-ticket-reply-attachment" name="attachment" accept=".jpg,.jpeg,.png,.gif,.webp,.pdf,.zip,.doc,.docx,image/*,application/pdf" />
                    </div>
                    <div class="weblazem-ticket-reply-form__actions">
                        <p class="weblazem-ticket-feedback" id="weblazem-ticket-reply-feedback" role="status" aria-live="polite"></p>
                        <button type="submit" class="weblazem-ticket-btn weblazem-ticket-btn--primary">ارسال پیام</button>
                    </div>
                </form>
            </div>

            <!-- Proposal detail -->
            <div class="weblazem-ticket-view" id="weblazem-proposal-detail-view" data-view="proposal" hidden>
                <button type="button" class="weblazem-ticket-back" data-ticket-back data-back-to="proposals">&rarr; بازگشت به پیشنهادها</button>
                <div class="weblazem-proposal-detail" id="weblazem-proposal-detail">
                    <div class="weblazem-proposal-detail__head">
                        <div>
                            <p class="weblazem-proposal-detail__code" id="weblazem-proposal-detail-code" dir="ltr"></p>
                            <h2 id="weblazem-proposal-detail-title"></h2>
                            <p class="weblazem-proposal-detail__meta" id="weblazem-proposal-detail-meta"></p>
                        </div>
                        <span class="weblazem-ticket-status" id="weblazem-proposal-detail-status"></span>
                    </div>
                    <div class="weblazem-proposal-detail__intro" id="weblazem-proposal-detail-intro"></div>
                    <div class="weblazem-proposal-detail__items" id="weblazem-proposal-detail-items"></div>
                    <div class="weblazem-proposal-detail__totals" id="weblazem-proposal-detail-totals"></div>
                    <div class="weblazem-proposal-detail__terms">
                        <h3>شرایط و ضوابط</h3>
                        <div id="weblazem-proposal-detail-terms"></div>
                    </div>
                    <div class="weblazem-proposal-detail__actions" id="weblazem-proposal-detail-actions" hidden>
                        <button type="button" class="weblazem-ticket-btn weblazem-ticket-btn--accent" id="weblazem-proposal-accept-btn">پذیرش پیشنهاد</button>
                        <button type="button" class="weblazem-ticket-btn weblazem-ticket-btn--ghost" id="weblazem-proposal-changes-btn">درخواست تغییر</button>
                    </div>
                    <div class="weblazem-proposal-changes" id="weblazem-proposal-changes-box" hidden>
                        <label for="weblazem-proposal-changes-note">توضیح تغییرات مورد نظر</label>
                        <textarea id="weblazem-proposal-changes-note" rows="4" maxlength="2000" placeholder="مثلاً: کاهش هزینه بخش فروشگاه یا افزودن پنل کاربری..."></textarea>
                        <p class="weblazem-ticket-feedback" id="weblazem-proposal-action-feedback" role="status" aria-live="polite"></p>
                        <div class="weblazem-proposal-changes__actions">
                            <button type="button" class="weblazem-ticket-btn weblazem-ticket-btn--primary" id="weblazem-proposal-changes-submit">ثبت درخواست تغییر</button>
                            <button type="button" class="weblazem-ticket-btn weblazem-ticket-btn--ghost" id="weblazem-proposal-changes-cancel">انصراف</button>
                        </div>
                    </div>
                    <p class="weblazem-ticket-feedback" id="weblazem-proposal-result-feedback" role="status" aria-live="polite"></p>
                </div>
            </div>
        </div>
    </div>

    <div class="weblazem-ticket-success" id="weblazem-ticket-success" hidden aria-hidden="true">
        <div class="weblazem-ticket-success__dialog" role="dialog" aria-modal="true" aria-labelledby="weblazem-ticket-success-title">
            <div class="weblazem-ticket-success__icon"><i class="fas fa-check-circle" aria-hidden="true"></i></div>
            <h3 id="weblazem-ticket-success-title">ثبت موفق</h3>
            <p id="weblazem-ticket-success-message"></p>
            <p class="weblazem-ticket-success__code" id="weblazem-ticket-success-code"></p>
            <button type="button" class="weblazem-ticket-btn weblazem-ticket-btn--primary" id="weblazem-ticket-success-close">متوجه شدم</button>
        </div>
    </div>
</section>
