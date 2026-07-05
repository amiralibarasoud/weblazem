<?php
/**
 * Website design — portfolio showcase (Success Stories).
 */

$subtitle    = weblazem_webdesign_option('portfolio_subtitle', 'نمونه‌کارهای طراحی وب‌سایت و وب‌سیما');
$description = weblazem_webdesign_option('portfolio_description', '');
$en_label    = weblazem_webdesign_option('portfolio_en_label', 'Success Stories');
$tabs        = weblazem_get_webdesign_portfolio_tabs();
$items       = weblazem_get_webdesign_portfolio_items();

if (empty($items)) {
    return;
}
?>

<section class="webdesign-portfolio" dir="rtl" id="webdesign-portfolio">
    <div class="webdesign-portfolio__bg" aria-hidden="true"></div>

    <div class="container">
        <header class="webdesign-portfolio__header">
            <div class="webdesign-portfolio__calligraphy">
                <?php weblazem_render_webdesign_calligraphy('portfolio_calligraphy_image', 'portfolio_calligraphy_text'); ?>
            </div>

            <?php if (!empty($subtitle)) : ?>
                <h2 class="webdesign-portfolio__subtitle"><?php echo esc_html($subtitle); ?></h2>
            <?php endif; ?>

            <?php if (!empty($description)) : ?>
                <p class="webdesign-portfolio__description"><?php echo esc_html($description); ?></p>
            <?php endif; ?>
        </header>

        <div class="webdesign-portfolio__toolbar">
            <?php if (!empty($tabs)) : ?>
                <div class="webdesign-portfolio__tabs" role="tablist">
                    <?php foreach ($tabs as $index => $tab) : ?>
                        <button type="button"
                                class="webdesign-portfolio__tab<?php echo $index === 0 ? ' is-active' : ''; ?>"
                                role="tab"
                                data-webdesign-tab="<?php echo esc_attr($tab['key']); ?>"
                                data-category="<?php echo esc_attr($tab['category']); ?>"
                                aria-selected="<?php echo $index === 0 ? 'true' : 'false'; ?>">
                            <?php echo esc_html($tab['title']); ?>
                        </button>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($en_label)) : ?>
                <span class="webdesign-portfolio__en-label"><?php echo esc_html($en_label); ?></span>
            <?php endif; ?>
        </div>

        <div class="weblazem-carousel webdesign-portfolio__carousel" data-weblazem-carousel data-autoplay="4500">
            <button type="button" class="webdesign-portfolio__nav webdesign-portfolio__nav--prev" data-carousel-prev aria-label="قبلی">
                <i class="fas fa-chevron-right" aria-hidden="true"></i>
            </button>

            <div class="weblazem-carousel__viewport" data-carousel-viewport>
                <div class="weblazem-carousel__track webdesign-portfolio__track" data-carousel-track>
                    <?php foreach ($items as $item) : ?>
                        <article class="weblazem-carousel__slide webdesign-showcase-card"
                                 data-categories="<?php echo esc_attr($item['category']); ?>"
                                 style="--card-accent: <?php echo esc_attr($item['color']); ?>">
                            <a href="<?php echo esc_url($item['link']); ?>" class="webdesign-showcase-card__link">
                                <div class="webdesign-showcase-card__media">
                                    <?php if (!empty($item['image'])) : ?>
                                        <img src="<?php echo esc_url($item['image']); ?>"
                                             alt="<?php echo esc_attr($item['title']); ?>"
                                             loading="lazy" />
                                    <?php else : ?>
                                        <div class="webdesign-showcase-card__placeholder">
                                            <i class="fas fa-image" aria-hidden="true"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <div class="webdesign-showcase-card__body">
                                    <?php if (!empty($item['logo'])) : ?>
                                        <img src="<?php echo esc_url($item['logo']); ?>"
                                             alt=""
                                             class="webdesign-showcase-card__brand" />
                                    <?php endif; ?>

                                    <h3 class="webdesign-showcase-card__title">
                                        <?php echo esc_html($item['logo_text'] ?: $item['title']); ?>
                                    </h3>

                                    <span class="webdesign-showcase-card__cta">
                                        مشاهده پروژه
                                        <i class="fas fa-arrow-left" aria-hidden="true"></i>
                                    </span>
                                </div>
                            </a>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>

            <button type="button" class="webdesign-portfolio__nav webdesign-portfolio__nav--next" data-carousel-next aria-label="بعدی">
                <i class="fas fa-chevron-left" aria-hidden="true"></i>
            </button>
        </div>
    </div>
</section>
