<?php
/**
 * Resources Hub front layout.
 */

$settings  = weblazem_get_resources_hub_settings();
$resources = isset($settings['resources']) && is_array($settings['resources']) ? $settings['resources'] : array();
?>

<section class="rh-section" dir="rtl" id="weblazem-resources-hub">
    <div class="rh-bg" aria-hidden="true"></div>
    <div class="container">
        <header class="rh-header">
            <?php if (!empty($settings['title'])) : ?>
                <h1 class="rh-header__title"><?php echo esc_html($settings['title']); ?></h1>
            <?php endif; ?>
            <?php if (!empty($settings['subtitle'])) : ?>
                <p class="rh-header__subtitle"><?php echo esc_html($settings['subtitle']); ?></p>
            <?php endif; ?>
        </header>

        <?php if (empty($resources)) : ?>
            <div class="rh-empty">
                <p>هنوز منبعی منتشر نشده است.</p>
            </div>
        <?php else : ?>
            <div class="rh-grid">
                <?php foreach ($resources as $resource) :
                    $icon = !empty($resource['icon']) ? $resource['icon'] : 'fa-file';
                    $has_file = !empty($resource['file_url']);
                    ?>
                    <article class="rh-card" data-resource-id="<?php echo esc_attr($resource['id']); ?>">
                        <div class="rh-card__icon" aria-hidden="true">
                            <i class="fas <?php echo esc_attr($icon); ?>"></i>
                        </div>
                        <div class="rh-card__body">
                            <?php if (!empty($resource['category'])) : ?>
                                <span class="rh-card__cat"><?php echo esc_html($resource['category']); ?></span>
                            <?php endif; ?>
                            <h2 class="rh-card__title"><?php echo esc_html($resource['title']); ?></h2>
                            <?php if (!empty($resource['description'])) : ?>
                                <p class="rh-card__desc"><?php echo esc_html($resource['description']); ?></p>
                            <?php endif; ?>
                            <div class="rh-card__meta">
                                <span>
                                    <i class="fas fa-download" aria-hidden="true"></i>
                                    <?php echo esc_html(number_format_i18n((int) ($resource['downloads_count'] ?? 0))); ?> دانلود
                                </span>
                                <?php if (!$has_file) : ?>
                                    <span class="rh-card__soon">به‌زودی</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <button
                            type="button"
                            class="rh-card__btn"
                            data-rh-download
                            data-resource-id="<?php echo esc_attr($resource['id']); ?>"
                            data-resource-title="<?php echo esc_attr($resource['title']); ?>"
                            data-has-file="<?php echo $has_file ? '1' : '0'; ?>"
                        >
                            دانلود رایگان
                        </button>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="rh-modal" id="rh-modal" hidden>
        <div class="rh-modal__backdrop" data-rh-close></div>
        <div class="rh-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="rh-modal-title">
            <button type="button" class="rh-modal__close" data-rh-close aria-label="بستن">
                <i class="fas fa-times" aria-hidden="true"></i>
            </button>
            <h2 class="rh-modal__title" id="rh-modal-title"><?php echo esc_html($settings['modal_title']); ?></h2>
            <p class="rh-modal__text"><?php echo esc_html($settings['modal_text']); ?></p>
            <p class="rh-modal__resource" id="rh-modal-resource"></p>

            <form class="rh-form" id="rh-form" novalidate>
                <input type="hidden" name="resource_id" id="rh-resource-id" value="" />
                <label class="rh-form__field">
                    <span>نام و نام خانوادگی</span>
                    <input type="text" name="name" id="rh-name" required autocomplete="name" placeholder="مثلاً علی رضایی" />
                </label>
                <label class="rh-form__field">
                    <span>شماره موبایل</span>
                    <input type="tel" name="mobile" id="rh-mobile" required dir="ltr" inputmode="tel" autocomplete="tel" placeholder="09121234567" />
                </label>
                <p class="rh-form__feedback" id="rh-feedback" role="status" aria-live="polite"></p>
                <button type="submit" class="rh-form__submit" id="rh-submit">
                    دریافت لینک دانلود
                </button>
            </form>
        </div>
    </div>
</section>
