<?php
/**
 * CSAT portal — survey form (with token) or public results.
 */

$settings = weblazem_get_csat_settings();
$token    = isset($_GET['token']) ? sanitize_text_field(wp_unslash($_GET['token'])) : '';
$invite_id = $token ? weblazem_csat_find_invite_by_token($token) : 0;
$mode      = 'public';

if ($token) {
    if (!$invite_id) {
        $mode = 'invalid';
    } elseif (get_post_meta($invite_id, '_csat_status', true) === 'completed') {
        $mode = 'completed';
    } else {
        $mode = 'form';
    }
}

$categories = weblazem_csat_category_keys();
?>

<section class="csat-section" dir="rtl" id="weblazem-csat" data-csat-mode="<?php echo esc_attr($mode); ?>">
    <div class="csat-bg" aria-hidden="true"></div>
    <div class="container">

        <?php if ($mode === 'invalid') : ?>
            <div class="csat-state csat-state--error">
                <div class="csat-state__icon" aria-hidden="true"><i class="fas fa-link-slash"></i></div>
                <h1><?php echo esc_html($settings['title']); ?></h1>
                <p><?php echo esc_html($settings['invalid_message']); ?></p>
                <a class="csat-state__link" href="<?php echo esc_url(weblazem_get_csat_page_url()); ?>">مشاهده نتایج عمومی</a>
            </div>

        <?php elseif ($mode === 'completed') : ?>
            <div class="csat-state csat-state--done">
                <div class="csat-state__icon" aria-hidden="true"><i class="fas fa-circle-check"></i></div>
                <h1><?php echo esc_html($settings['thank_you_title']); ?></h1>
                <p><?php echo esc_html($settings['already_message']); ?></p>
                <a class="csat-state__link" href="<?php echo esc_url(weblazem_get_csat_page_url()); ?>">مشاهده نتایج عمومی</a>
            </div>

        <?php elseif ($mode === 'form') :
            $client  = get_post_meta($invite_id, '_csat_client_name', true);
            $project = get_post_meta($invite_id, '_csat_project', true);
            ?>
            <header class="csat-header">
                <h1 class="csat-header__title"><?php echo esc_html($settings['form_title']); ?></h1>
                <p class="csat-header__subtitle"><?php echo esc_html($settings['form_subtitle']); ?></p>
            </header>

            <div class="csat-form-wrap" id="csat-form-wrap">
                <div class="csat-project-badge">
                    <span class="csat-project-badge__label">پروژه</span>
                    <strong><?php echo esc_html($project); ?></strong>
                    <?php if ($client) : ?>
                        <span class="csat-project-badge__client"><?php echo esc_html($client); ?></span>
                    <?php endif; ?>
                </div>

                <form class="csat-form" id="csat-form" novalidate>
                    <input type="hidden" name="token" id="csat-token" value="<?php echo esc_attr($token); ?>" />

                    <fieldset class="csat-stars-block">
                        <legend>امتیاز کلی رضایت</legend>
                        <div class="csat-stars" data-csat-stars="overall" role="radiogroup" aria-label="امتیاز کلی از ۱ تا ۵">
                            <?php for ($i = 1; $i <= 5; $i++) : ?>
                                <button type="button" class="csat-star" data-value="<?php echo (int) $i; ?>" aria-label="<?php echo (int) $i; ?> از ۵">
                                    <i class="far fa-star" aria-hidden="true"></i>
                                </button>
                            <?php endfor; ?>
                        </div>
                        <input type="hidden" name="score_overall" id="csat-score-overall" value="" required />
                        <p class="csat-stars__hint" data-stars-hint>یک ستاره انتخاب کنید</p>
                    </fieldset>

                    <div class="csat-categories">
                        <p class="csat-categories__title">امتیاز اختیاری به بخش‌ها</p>
                        <?php foreach ($categories as $key => $label) : ?>
                            <div class="csat-cat-row">
                                <span class="csat-cat-row__label"><?php echo esc_html($label); ?></span>
                                <div class="csat-stars csat-stars--sm" data-csat-stars="<?php echo esc_attr($key); ?>" role="radiogroup" aria-label="<?php echo esc_attr($label); ?>">
                                    <?php for ($i = 1; $i <= 5; $i++) : ?>
                                        <button type="button" class="csat-star" data-value="<?php echo (int) $i; ?>" aria-label="<?php echo (int) $i; ?>">
                                            <i class="far fa-star" aria-hidden="true"></i>
                                        </button>
                                    <?php endfor; ?>
                                </div>
                                <input type="hidden" name="score_<?php echo esc_attr($key); ?>" id="csat-score-<?php echo esc_attr($key); ?>" value="" />
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <label class="csat-field">
                        <span>نظر شما (اختیاری)</span>
                        <textarea name="comment" id="csat-comment" rows="4" placeholder="چه چیزی خوب بود؟ چه چیزی بهتر می‌شد؟"></textarea>
                    </label>

                    <label class="csat-check">
                        <input type="checkbox" name="allow_publish" id="csat-allow-publish" value="1" />
                        <span>اجازه می‌دهم نظرم (بدون شماره تماس) در صفحه عمومی نمایش داده شود.</span>
                    </label>

                    <p class="csat-feedback" id="csat-feedback" role="status" aria-live="polite"></p>

                    <button type="submit" class="csat-submit" id="csat-submit">ثبت نظرسنجی</button>
                </form>

                <div class="csat-success" id="csat-success" hidden>
                    <div class="csat-success__icon" aria-hidden="true"><i class="fas fa-heart"></i></div>
                    <h2 id="csat-success-title"><?php echo esc_html($settings['thank_you_title']); ?></h2>
                    <p id="csat-success-msg"><?php echo esc_html($settings['success_message']); ?></p>
                    <a class="csat-state__link" href="<?php echo esc_url(weblazem_get_csat_page_url()); ?>">مشاهده نتایج عمومی</a>
                </div>
            </div>

        <?php else :
            $stats = weblazem_get_csat_stats();
            get_template_part(
                'template-parts/csat/public-stats',
                null,
                array(
                    'settings' => $settings,
                    'stats'    => $stats,
                    'full'     => true,
                )
            );
        endif; ?>

    </div>
</section>
