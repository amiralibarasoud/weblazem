<?php
/**
 * Price estimator wizard portal.
 */

$settings = weblazem_get_price_estimator_settings();
$enabled  = $settings['enabled'] === '1';
?>

<section class="pe-section" id="weblazem-price-estimator" dir="rtl">
    <div class="pe-bg" aria-hidden="true"></div>
    <div class="container">
        <header class="pe-header">
            <?php if (!empty($settings['title'])) : ?>
                <h1 class="pe-header__title"><?php echo esc_html($settings['title']); ?></h1>
            <?php endif; ?>
            <?php if (!empty($settings['subtitle'])) : ?>
                <p class="pe-header__subtitle"><?php echo esc_html($settings['subtitle']); ?></p>
            <?php endif; ?>
            <?php if (!empty($settings['intro'])) : ?>
                <p class="pe-header__intro"><?php echo esc_html($settings['intro']); ?></p>
            <?php endif; ?>
        </header>

        <?php if (!$enabled) : ?>
            <div class="pe-disabled">
                <p><?php echo esc_html($settings['disabled_message']); ?></p>
            </div>
        <?php else : ?>
            <div class="pe-wizard" id="pe-wizard" data-step="1">
                <div class="pe-progress" role="progressbar" aria-valuemin="1" aria-valuemax="5" aria-valuenow="1">
                    <?php
                    $steps = array(
                        1 => 'نوع سایت',
                        2 => 'صفحات',
                        3 => 'افزونه‌ها',
                        4 => 'زمان‌بندی',
                        5 => 'نتیجه',
                    );
                    foreach ($steps as $num => $label) :
                        ?>
                        <div class="pe-progress__item<?php echo $num === 1 ? ' is-active' : ''; ?>" data-step-indicator="<?php echo esc_attr($num); ?>">
                            <span class="pe-progress__dot"><?php echo esc_html($num); ?></span>
                            <span class="pe-progress__label"><?php echo esc_html($label); ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Step 1: Site type -->
                <div class="pe-step is-active" data-step-panel="1">
                    <h2 class="pe-step__title">چه نوع سایتی می‌خواهید؟</h2>
                    <div class="pe-cards pe-cards--site" role="radiogroup" aria-label="نوع سایت">
                        <?php foreach ($settings['site_types'] as $key => $item) : ?>
                            <label class="pe-card">
                                <input type="radio" name="pe_site_type" value="<?php echo esc_attr($key); ?>" <?php checked($key, 'corporate'); ?> />
                                <span class="pe-card__body">
                                    <span class="pe-card__title"><?php echo esc_html($item['label']); ?></span>
                                    <?php if (!empty($item['desc'])) : ?>
                                        <span class="pe-card__desc"><?php echo esc_html($item['desc']); ?></span>
                                    <?php endif; ?>
                                    <span class="pe-card__meta">از <?php echo esc_html(weblazem_growth_format_toman($item['base'])); ?></span>
                                </span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                    <div class="pe-nav">
                        <button type="button" class="pe-btn pe-btn--primary" data-pe-next>ادامه</button>
                    </div>
                </div>

                <!-- Step 2: Pages -->
                <div class="pe-step" data-step-panel="2" hidden>
                    <h2 class="pe-step__title">تقریباً چند صفحه نیاز دارید؟</h2>
                    <div class="pe-cards pe-cards--tiers" role="radiogroup" aria-label="تعداد صفحات">
                        <?php foreach ($settings['page_tiers'] as $key => $item) : ?>
                            <label class="pe-card pe-card--compact">
                                <input type="radio" name="pe_pages" value="<?php echo esc_attr($key); ?>" <?php checked($key, '1-5'); ?> />
                                <span class="pe-card__body">
                                    <span class="pe-card__title"><?php echo esc_html($item['label']); ?></span>
                                    <span class="pe-card__meta">ضریب ×<?php echo esc_html(number_format_i18n($item['multiplier'], 2)); ?></span>
                                </span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                    <div class="pe-nav">
                        <button type="button" class="pe-btn pe-btn--ghost" data-pe-prev>بازگشت</button>
                        <button type="button" class="pe-btn pe-btn--primary" data-pe-next>ادامه</button>
                    </div>
                </div>

                <!-- Step 3: Addons -->
                <div class="pe-step" data-step-panel="3" hidden>
                    <h2 class="pe-step__title">کدام خدمات جانبی را می‌خواهید؟</h2>
                    <p class="pe-step__hint">می‌توانید چند مورد را انتخاب کنید یا هیچ‌کدام.</p>
                    <div class="pe-cards pe-cards--addons">
                        <?php foreach ($settings['addons'] as $key => $item) : ?>
                            <label class="pe-card pe-card--check">
                                <input type="checkbox" name="pe_addons[]" value="<?php echo esc_attr($key); ?>" />
                                <span class="pe-card__body">
                                    <span class="pe-card__title"><?php echo esc_html($item['label']); ?></span>
                                    <?php if (!empty($item['desc'])) : ?>
                                        <span class="pe-card__desc"><?php echo esc_html($item['desc']); ?></span>
                                    <?php endif; ?>
                                    <span class="pe-card__meta">+ <?php echo esc_html(weblazem_growth_format_toman($item['price'])); ?></span>
                                </span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                    <div class="pe-nav">
                        <button type="button" class="pe-btn pe-btn--ghost" data-pe-prev>بازگشت</button>
                        <button type="button" class="pe-btn pe-btn--primary" data-pe-next>ادامه</button>
                    </div>
                </div>

                <!-- Step 4: Urgency -->
                <div class="pe-step" data-step-panel="4" hidden>
                    <h2 class="pe-step__title">زمان‌بندی تحویل چقدر مهم است؟</h2>
                    <div class="pe-cards pe-cards--urgency" role="radiogroup" aria-label="فوریت">
                        <?php foreach ($settings['urgency'] as $key => $item) : ?>
                            <label class="pe-card">
                                <input type="radio" name="pe_urgency" value="<?php echo esc_attr($key); ?>" <?php checked($key, 'normal'); ?> />
                                <span class="pe-card__body">
                                    <span class="pe-card__title"><?php echo esc_html($item['label']); ?></span>
                                    <?php if (!empty($item['desc'])) : ?>
                                        <span class="pe-card__desc"><?php echo esc_html($item['desc']); ?></span>
                                    <?php endif; ?>
                                    <span class="pe-card__meta">ضریب ×<?php echo esc_html(number_format_i18n($item['multiplier'], 2)); ?></span>
                                </span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                    <div class="pe-nav">
                        <button type="button" class="pe-btn pe-btn--ghost" data-pe-prev>بازگشت</button>
                        <button type="button" class="pe-btn pe-btn--primary" data-pe-calc>مشاهده برآورد</button>
                    </div>
                </div>

                <!-- Step 5: Result -->
                <div class="pe-step" data-step-panel="5" hidden>
                    <div class="pe-result" id="pe-result">
                        <div class="pe-result__loading" id="pe-result-loading" hidden>
                            <div class="pe-spinner" aria-hidden="true"></div>
                            <p>در حال محاسبه...</p>
                        </div>

                        <div class="pe-result__body" id="pe-result-body" hidden>
                            <h2 class="pe-step__title">برآورد تقریبی هزینه پروژه</h2>
                            <div class="pe-result__range">
                                <span class="pe-result__min" id="pe-min"></span>
                                <span class="pe-result__sep">تا</span>
                                <span class="pe-result__max" id="pe-max"></span>
                            </div>
                            <p class="pe-result__center">میانگین حدودی: <strong id="pe-estimate"></strong></p>
                            <ul class="pe-result__summary" id="pe-summary"></ul>
                            <p class="pe-result__note"><?php echo esc_html($settings['result_cta_text']); ?></p>

                            <div class="pe-result__ctas">
                                <?php if (!empty($settings['consult_cta_text'])) : ?>
                                    <?php if (!empty($settings['consult_cta_url'])) : ?>
                                        <a class="pe-btn pe-btn--primary" href="<?php echo esc_url($settings['consult_cta_url']); ?>">
                                            <?php echo esc_html($settings['consult_cta_text']); ?>
                                        </a>
                                    <?php else : ?>
                                        <button type="button" class="pe-btn pe-btn--primary weblazem-consult-trigger">
                                            <?php echo esc_html($settings['consult_cta_text']); ?>
                                        </button>
                                    <?php endif; ?>
                                <?php endif; ?>
                                <?php if (!empty($settings['start_project_cta_text']) && !empty($settings['start_project_cta_url'])) : ?>
                                    <a class="pe-btn pe-btn--accent" href="<?php echo esc_url($settings['start_project_cta_url']); ?>">
                                        <?php echo esc_html($settings['start_project_cta_text']); ?>
                                    </a>
                                <?php endif; ?>
                            </div>

                            <?php if ($settings['save_lead'] === '1') : ?>
                                <div class="pe-lead" id="pe-lead">
                                    <h3 class="pe-lead__title"><?php echo esc_html($settings['lead_form_title']); ?></h3>
                                    <p class="pe-lead__subtitle"><?php echo esc_html($settings['lead_form_subtitle']); ?></p>
                                    <form id="pe-lead-form" class="pe-lead__form" novalidate>
                                        <div class="pe-field">
                                            <label for="pe-name">نام و نام خانوادگی</label>
                                            <input type="text" id="pe-name" name="name" required maxlength="80" autocomplete="name" placeholder="مثال: علی محمدی" />
                                        </div>
                                        <div class="pe-field">
                                            <label for="pe-mobile">شماره موبایل</label>
                                            <input type="tel" id="pe-mobile" name="mobile" required inputmode="numeric" dir="ltr" autocomplete="tel" placeholder="09121234567" maxlength="13" />
                                        </div>
                                        <p class="pe-feedback" id="pe-lead-feedback" role="status" aria-live="polite"></p>
                                        <button type="submit" class="pe-btn pe-btn--accent pe-btn--block">ثبت برآورد و تماس با من</button>
                                    </form>
                                    <div class="pe-lead__success" id="pe-lead-success" hidden>
                                        <div class="pe-lead__success-icon" aria-hidden="true">✓</div>
                                        <p id="pe-lead-success-msg"></p>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>

                        <p class="pe-feedback pe-feedback--error" id="pe-calc-error" hidden></p>
                    </div>
                    <div class="pe-nav">
                        <button type="button" class="pe-btn pe-btn--ghost" data-pe-prev>بازگشت</button>
                        <button type="button" class="pe-btn pe-btn--ghost" data-pe-restart>شروع مجدد</button>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>
