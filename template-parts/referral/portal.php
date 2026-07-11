<?php
/**
 * Referral club portal — join + referred lead forms.
 */

$s         = weblazem_get_referral_settings();
$services  = weblazem_referral_service_choices();
$cookie    = weblazem_referral_get_cookie_code();
$ref_param = isset($_GET['ref']) ? weblazem_referral_normalize_code(wp_unslash($_GET['ref'])) : '';
$active_ref = $ref_param !== '' ? $ref_param : $cookie;
$terms_lines = array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', (string) $s['terms_text'])));
?>

<section class="rf-page" dir="rtl" id="weblazem-referral" data-rf-root>
    <div class="rf-page__bg" aria-hidden="true"></div>
    <div class="container">
        <header class="rf-page__header">
            <p class="rf-page__brand">وب‌لازم</p>
            <?php if (!empty($s['title'])) : ?>
                <h1 class="rf-page__title"><?php echo esc_html($s['title']); ?></h1>
            <?php endif; ?>
            <?php if (!empty($s['subtitle'])) : ?>
                <p class="rf-page__subtitle"><?php echo esc_html($s['subtitle']); ?></p>
            <?php endif; ?>
        </header>

        <div class="rf-rewards">
            <div class="rf-rewards__card">
                <span class="rf-rewards__label">پاداش معرف</span>
                <strong><?php echo esc_html($s['reward_text']); ?></strong>
            </div>
            <div class="rf-rewards__card rf-rewards__card--friend">
                <span class="rf-rewards__label">پاداش دوست</span>
                <strong><?php echo esc_html($s['reward_for_friend']); ?></strong>
            </div>
        </div>

        <div class="rf-grid">
            <article class="rf-panel" data-rf-join-panel>
                <h2 class="rf-panel__title"><?php echo esc_html($s['join_title']); ?></h2>
                <p class="rf-panel__text"><?php echo esc_html($s['join_subtitle']); ?></p>

                <form class="rf-form" id="rf-join-form" novalidate>
                    <div class="rf-form__field">
                        <label for="rf-join-name">نام و نام خانوادگی</label>
                        <input type="text" id="rf-join-name" name="name" required autocomplete="name" />
                    </div>
                    <div class="rf-form__field">
                        <label for="rf-join-mobile">موبایل</label>
                        <input type="tel" id="rf-join-mobile" name="mobile" required dir="ltr" inputmode="numeric" placeholder="09121234567" autocomplete="tel" />
                    </div>
                    <button type="submit" class="rf-btn rf-btn--primary" data-rf-join-submit>دریافت کد معرفی</button>
                    <p class="rf-form__feedback" data-rf-join-feedback role="status" aria-live="polite"></p>
                </form>

                <div class="rf-share" data-rf-share hidden>
                    <p class="rf-share__label"><?php echo esc_html($s['share_label']); ?></p>
                    <div class="rf-share__row">
                        <input type="text" readonly dir="ltr" data-rf-share-url value="" />
                        <button type="button" class="rf-btn rf-btn--yellow" data-rf-copy><?php echo esc_html($s['copy_text']); ?></button>
                    </div>
                    <p class="rf-share__code">کد شما: <strong dir="ltr" data-rf-code></strong></p>
                    <p class="rf-share__reward" data-rf-reward></p>
                </div>
            </article>

            <article class="rf-panel rf-panel--lead" data-rf-lead-panel>
                <h2 class="rf-panel__title"><?php echo esc_html($s['lead_title']); ?></h2>
                <p class="rf-panel__text"><?php echo esc_html($s['lead_subtitle']); ?></p>

                <?php if ($active_ref !== '') : ?>
                    <p class="rf-active-ref">کد فعال: <strong dir="ltr"><?php echo esc_html($active_ref); ?></strong></p>
                <?php endif; ?>

                <form class="rf-form" id="rf-lead-form" novalidate>
                    <div class="rf-form__field">
                        <label for="rf-lead-name">نام و نام خانوادگی</label>
                        <input type="text" id="rf-lead-name" name="name" required autocomplete="name" />
                    </div>
                    <div class="rf-form__field">
                        <label for="rf-lead-mobile">موبایل</label>
                        <input type="tel" id="rf-lead-mobile" name="mobile" required dir="ltr" inputmode="numeric" placeholder="09121234567" autocomplete="tel" />
                    </div>
                    <div class="rf-form__field">
                        <label for="rf-lead-service">خدمت مورد علاقه</label>
                        <select id="rf-lead-service" name="service" required>
                            <option value="">انتخاب کنید…</option>
                            <?php foreach ($services as $key => $label) : ?>
                                <option value="<?php echo esc_attr($key); ?>"><?php echo esc_html($label); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="rf-form__field">
                        <label for="rf-lead-code">کد معرفی</label>
                        <input type="text" id="rf-lead-code" name="ref_code" dir="ltr" value="<?php echo esc_attr($active_ref); ?>" placeholder="WL-XXXX" />
                    </div>
                    <button type="submit" class="rf-btn rf-btn--yellow" data-rf-lead-submit>ثبت درخواست</button>
                    <p class="rf-form__feedback" data-rf-lead-feedback role="status" aria-live="polite"></p>
                </form>
            </article>
        </div>

        <?php if (!empty($terms_lines)) : ?>
            <aside class="rf-terms">
                <h3>شرایط باشگاه</h3>
                <ul>
                    <?php foreach ($terms_lines as $line) : ?>
                        <li><?php echo esc_html($line); ?></li>
                    <?php endforeach; ?>
                </ul>
            </aside>
        <?php endif; ?>
    </div>
</section>
