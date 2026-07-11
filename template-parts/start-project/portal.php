<?php
/**
 * Start project multi-step form markup.
 */

$opts = function_exists('weblazem_get_start_project_options') ? weblazem_get_start_project_options() : array();
$title = !empty($opts['title']) ? $opts['title'] : 'شروع پروژه';
$subtitle = !empty($opts['subtitle']) ? $opts['subtitle'] : '';
$steps = !empty($opts['step_labels']) ? $opts['step_labels'] : array();
$types = !empty($opts['project_types']) ? $opts['project_types'] : array();
$budgets = !empty($opts['budget_ranges']) ? $opts['budget_ranges'] : array();
?>

<section class="wl-sp" id="weblazem-start-project-portal" dir="rtl">
    <div class="wl-sp__bg" aria-hidden="true"></div>
    <div class="container">
        <header class="wl-sp__header">
            <p class="wl-sp__brand">وب‌لازم</p>
            <h1 class="wl-sp__title"><?php echo esc_html($title); ?></h1>
            <?php if ($subtitle !== '') : ?>
                <p class="wl-sp__subtitle"><?php echo esc_html($subtitle); ?></p>
            <?php endif; ?>
        </header>

        <div class="wl-sp__panel" id="weblazem-sp-panel">
            <div class="wl-sp__progress" aria-hidden="true">
                <?php foreach ($steps as $i => $label) : ?>
                    <span class="wl-sp__progress-step <?php echo $i === 0 ? 'is-active' : ''; ?>" data-sp-step-indicator="<?php echo esc_attr($i + 1); ?>">
                        <span class="num"><?php echo esc_html($i + 1); ?></span>
                        <span class="lbl"><?php echo esc_html($label); ?></span>
                    </span>
                <?php endforeach; ?>
            </div>

            <form id="wl-sp-form" class="wl-sp__form" novalidate>
                <div class="wl-sp__step" data-step="1">
                    <h2 class="wl-sp__step-title"><?php echo esc_html(isset($steps[0]) ? $steps[0] : 'هدف و نوع پروژه'); ?></h2>
                    <div class="wl-sp__field">
                        <label for="wl-sp-type">نوع پروژه</label>
                        <select id="wl-sp-type" name="project_type" required>
                            <option value="">انتخاب کنید…</option>
                            <?php foreach ($types as $key => $label) : ?>
                                <option value="<?php echo esc_attr($key); ?>"><?php echo esc_html($label); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="wl-sp__field">
                        <label for="wl-sp-goal">هدف اصلی پروژه</label>
                        <textarea id="wl-sp-goal" name="goal" rows="4" required maxlength="1000" placeholder="مثلاً افزایش فروش آنلاین، معرفی برند، جذب لید…"></textarea>
                    </div>
                </div>

                <div class="wl-sp__step" data-step="2" hidden>
                    <h2 class="wl-sp__step-title"><?php echo esc_html(isset($steps[1]) ? $steps[1] : 'رقبا و نمونه‌ها'); ?></h2>
                    <div class="wl-sp__field">
                        <label for="wl-sp-competitors">سایت‌های رقیب یا نمونه‌های مورد علاقه</label>
                        <textarea id="wl-sp-competitors" name="competitors" rows="5" maxlength="2000" placeholder="آدرس سایت‌ها و نکته‌هایی که می‌پسندید یا نمی‌پسندید…"></textarea>
                    </div>
                </div>

                <div class="wl-sp__step" data-step="3" hidden>
                    <h2 class="wl-sp__step-title"><?php echo esc_html(isset($steps[2]) ? $steps[2] : 'محتوا و صفحات'); ?></h2>
                    <div class="wl-sp__field">
                        <label>آمادگی محتوا</label>
                        <div class="wl-sp__radios">
                            <label><input type="radio" name="content_ready" value="ready" /> محتوا آماده است</label>
                            <label><input type="radio" name="content_ready" value="partial" checked /> بخشی آماده است</label>
                            <label><input type="radio" name="content_ready" value="none" /> نیاز به کمک در محتوا</label>
                        </div>
                    </div>
                    <div class="wl-sp__field">
                        <label for="wl-sp-pages">صفحات / بخش‌های مورد نیاز</label>
                        <textarea id="wl-sp-pages" name="pages_needed" rows="4" maxlength="1500" placeholder="مثلاً صفحه اصلی، درباره ما، خدمات، بلاگ، تماس…"></textarea>
                    </div>
                </div>

                <div class="wl-sp__step" data-step="4" hidden>
                    <h2 class="wl-sp__step-title"><?php echo esc_html(isset($steps[3]) ? $steps[3] : 'بودجه و زمان'); ?></h2>
                    <div class="wl-sp__field">
                        <label for="wl-sp-budget">بازه بودجه</label>
                        <select id="wl-sp-budget" name="budget" required>
                            <option value="">انتخاب کنید…</option>
                            <?php foreach ($budgets as $key => $label) : ?>
                                <option value="<?php echo esc_attr($key); ?>"><?php echo esc_html($label); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="wl-sp__field">
                        <label for="wl-sp-deadline">مهلت تقریبی</label>
                        <input type="text" id="wl-sp-deadline" name="deadline" maxlength="120" placeholder="مثلاً ۱ ماه / قبل از نوروز / فوری نیست" />
                    </div>
                </div>

                <div class="wl-sp__step" data-step="5" hidden>
                    <h2 class="wl-sp__step-title"><?php echo esc_html(isset($steps[4]) ? $steps[4] : 'اطلاعات تماس'); ?></h2>
                    <div class="wl-sp__summary" id="wl-sp-summary" aria-live="polite"></div>
                    <div class="wl-sp__grid">
                        <div class="wl-sp__field">
                            <label for="wl-sp-name">نام و نام خانوادگی</label>
                            <input type="text" id="wl-sp-name" name="name" required maxlength="120" />
                        </div>
                        <div class="wl-sp__field">
                            <label for="wl-sp-mobile">موبایل</label>
                            <input type="tel" id="wl-sp-mobile" name="mobile" required inputmode="numeric" dir="ltr" maxlength="13" placeholder="09121234567" />
                        </div>
                    </div>
                    <div class="wl-sp__field">
                        <label for="wl-sp-email">ایمیل (اختیاری)</label>
                        <input type="email" id="wl-sp-email" name="email" dir="ltr" maxlength="160" placeholder="you@example.com" />
                    </div>
                </div>

                <div class="wl-sp__step" data-step="success" hidden>
                    <div class="wl-sp__success">
                        <div class="wl-sp__success-icon" aria-hidden="true"><i class="fas fa-rocket"></i></div>
                        <h2>بریف ثبت شد</h2>
                        <p id="wl-sp-success-message"></p>
                        <button type="button" class="wl-sp__btn wl-sp__btn--ghost" id="wl-sp-again">ارسال بریف جدید</button>
                    </div>
                </div>

                <p class="wl-sp__feedback" id="wl-sp-feedback" role="status" aria-live="polite"></p>

                <div class="wl-sp__nav" id="wl-sp-nav">
                    <button type="button" class="wl-sp__btn wl-sp__btn--ghost" id="wl-sp-prev" hidden>قبلی</button>
                    <button type="button" class="wl-sp__btn wl-sp__btn--primary" id="wl-sp-next">ادامه</button>
                    <button type="submit" class="wl-sp__btn wl-sp__btn--primary" id="wl-sp-submit" hidden>ارسال بریف</button>
                </div>
            </form>
        </div>
    </div>
</section>
