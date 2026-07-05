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
            <?php if (!empty($en_label)) : ?>
                <span class="webdesign-portfolio__en-label"><?php echo esc_html($en_label); ?></span>
            <?php endif; ?>

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
        </div>

        <div class="weblazem-carousel webdesign-portfolio__carousel" data-weblazem-carousel data-autoplay="5000">
            <div class="weblazem-carousel__viewport" data-carousel-viewport>
                <div class="weblazem-carousel__track webdesign-portfolio__track" data-carousel-track>
                    <?php foreach ($items as $item) : ?>
                        <article class="weblazem-carousel__slide webdesign-showcase-card"
                                 data-categories="<?php echo esc_attr($item['category']); ?>"
                                 style="--card-bg: <?php echo esc_attr($item['color']); ?>">
                            <span class="webdesign-showcase-card__star" aria-hidden="true">
                                <i class="fas fa-star"></i>
                            </span>

                            <?php if (!empty($item['tag'])) : ?>
                                <span class="webdesign-showcase-card__tag"><?php echo esc_html($item['tag']); ?></span>
                            <?php endif; ?>

                            <a href="<?php echo esc_url($item['link']); ?>" class="webdesign-showcase-card__link">
                                <div class="webdesign-showcase-card__preview">
                                    <?php if (!empty($item['image'])) : ?>
                                        <img src="<?php echo esc_url($item['image']); ?>"
                                             alt="<?php echo esc_attr($item['title']); ?>"
                                             loading="lazy" />
                                    <?php else : ?>
                                        <div class="webdesign-showcase-card__placeholder">
                                            <i class="fas fa-laptop" aria-hidden="true"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <div class="webdesign-showcase-card__footer">
                                    <?php if (!empty($item['logo'])) : ?>
                                        <img src="<?php echo esc_url($item['logo']); ?>"
                                             alt="<?php echo esc_attr($item['logo_text']); ?>"
                                             class="webdesign-showcase-card__logo" />
                                    <?php elseif (!empty($item['logo_text'])) : ?>
                                        <span class="webdesign-showcase-card__logo-text"><?php echo esc_html($item['logo_text']); ?></span>
                                    <?php endif; ?>

                                    <span class="webdesign-showcase-card__arrow" aria-hidden="true">
                                        <i class="fas fa-arrow-left"></i>
                                    </span>
                                </div>
                            </a>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="webdesign-portfolio__indicator" aria-hidden="true">
            <span></span><span></span>
        </div>
    </div>
</section>
