<?php
/**
 * Portfolio archive — hero section.
 */

$hero_title = get_option('weblazem_portfolio_page_hero_title', 'جدیدترین نمونه‌کارهای وب‌لازم');
$hero_desc  = get_option('weblazem_portfolio_page_hero_description', '');
$hero_image = get_option('weblazem_portfolio_page_hero_image', '');
$default_illustration = get_template_directory_uri() . '/assets/images/portfolio-hero-illustration.svg';
?>

<section class="portfolio-page-hero" dir="rtl">
    <div class="portfolio-page-hero__inner">
        <div class="container">
            <div class="portfolio-page-hero__grid">
                <div class="portfolio-page-hero__content">
                    <?php if (!empty($hero_title)) : ?>
                        <h1 class="portfolio-page-hero__title"><?php echo esc_html($hero_title); ?></h1>
                    <?php endif; ?>

                    <?php if (!empty($hero_desc)) : ?>
                        <p class="portfolio-page-hero__description"><?php echo esc_html($hero_desc); ?></p>
                    <?php endif; ?>
                </div>

                <div class="portfolio-page-hero__illustration" aria-hidden="true">
                    <?php if (!empty($hero_image)) : ?>
                        <img src="<?php echo esc_url($hero_image); ?>" alt="" class="portfolio-page-hero__image" />
                    <?php else : ?>
                        <img src="<?php echo esc_url($default_illustration); ?>" alt="" class="portfolio-page-hero__image" />
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <svg class="portfolio-page-hero__wave" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 120" preserveAspectRatio="none" aria-hidden="true">
        <path fill="#ffffff" d="M0,64 C240,120 480,0 720,48 C960,96 1200,24 1440,64 L1440,120 L0,120 Z"/>
    </svg>
</section>
