<?php
/**
 * Single portfolio — more projects carousel (fixed section).
 */

if (weblazem_get_portfolio_single_option('weblazem_portfolio_single_more_enabled', '1') !== '1') {
    return;
}

$title    = weblazem_get_portfolio_single_option('weblazem_portfolio_single_more_title');
$text     = weblazem_get_portfolio_single_option('weblazem_portfolio_single_more_text');
$btn_text = weblazem_get_portfolio_single_option('weblazem_portfolio_single_more_btn_text');
$btn_url  = weblazem_get_portfolio_single_option('weblazem_portfolio_single_more_btn_url');

if (empty($btn_url)) {
    $btn_url = weblazem_get_portfolio_page_url();
}

$count = (int) weblazem_get_portfolio_single_option('weblazem_portfolio_single_more_count', 8);
$query = weblazem_get_more_portfolio_items(get_the_ID(), $count);

if (!$query->have_posts()) {
    return;
}
?>

<section class="portfolio-single-more" dir="rtl">
    <svg class="portfolio-single-more__wave" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 120" preserveAspectRatio="none" aria-hidden="true">
        <path fill="#ffffff" d="M0,48 C200,96 420,0 720,40 C980,76 1180,20 1440,56 L1440,0 L0,0 Z"/>
        <path fill="#f2e6f7" d="M0,72 C220,110 460,24 720,56 C980,88 1200,36 1440,72 L1440,0 L0,0 Z"/>
        <path fill="#40165c" d="M0,96 C240,120 480,40 720,72 C960,104 1200,52 1440,88 L1440,0 L0,0 Z"/>
    </svg>

    <div class="portfolio-single-more__inner">
        <div class="container">
            <div class="portfolio-single-more__header">
                <?php if (!empty($title)) : ?>
                    <h2 class="portfolio-single-more__title"><?php echo esc_html($title); ?></h2>
                <?php endif; ?>

                <?php if (!empty($text)) : ?>
                    <p class="portfolio-single-more__text"><?php echo esc_html($text); ?></p>
                <?php endif; ?>

                <?php if (!empty($btn_text)) : ?>
                    <a href="<?php echo esc_url($btn_url); ?>" class="portfolio-single-more__all-btn">
                        <?php echo esc_html($btn_text); ?>
                    </a>
                <?php endif; ?>
            </div>

            <div class="weblazem-carousel portfolio-single-more__carousel" data-weblazem-carousel data-autoplay="4500">
                <button type="button" class="portfolio-single-more__nav" data-carousel-prev aria-label="قبلی">
                    <i class="fas fa-chevron-right" aria-hidden="true"></i>
                </button>

                <div class="weblazem-carousel__viewport" data-carousel-viewport>
                    <div class="weblazem-carousel__track" data-carousel-track>
                        <?php
                        while ($query->have_posts()) :
                            $query->the_post();
                            $thumb = get_the_post_thumbnail_url(get_the_ID(), 'medium_large');
                            if (!$thumb) {
                                $thumb = weblazem_get_portfolio_single_hero_image(get_the_ID());
                            }
                            $logo = get_post_meta(get_the_ID(), '_weblazem_portfolio_client_logo', true);
                            ?>
                            <div class="weblazem-carousel__slide portfolio-single-more__slide">
                                <a href="<?php echo esc_url(get_permalink()); ?>" class="portfolio-single-more__card">
                                    <div class="portfolio-single-more__card-screen">
                                        <?php if ($thumb) : ?>
                                            <img src="<?php echo esc_url($thumb); ?>" alt="<?php echo esc_attr(get_the_title()); ?>" loading="lazy" draggable="false" />
                                        <?php else : ?>
                                            <div class="portfolio-single-more__card-placeholder">
                                                <i class="fas fa-laptop" aria-hidden="true"></i>
                                            </div>
                                        <?php endif; ?>
                                        <span class="portfolio-single-more__card-star" aria-hidden="true"><i class="fas fa-star"></i></span>
                                    </div>
                                    <div class="portfolio-single-more__card-footer">
                                        <?php if ($logo) : ?>
                                            <img src="<?php echo esc_url($logo); ?>" alt="" class="portfolio-single-more__card-logo" />
                                        <?php else : ?>
                                            <span class="portfolio-single-more__card-name"><?php echo esc_html(weblazem_get_portfolio_card_title()); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </a>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>

                <button type="button" class="portfolio-single-more__nav" data-carousel-next aria-label="بعدی">
                    <i class="fas fa-chevron-left" aria-hidden="true"></i>
                </button>
            </div>
        </div>
    </div>
</section>

<?php wp_reset_postdata(); ?>
