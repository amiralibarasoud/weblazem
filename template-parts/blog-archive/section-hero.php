<?php
/**
 * Blog archive — hero section.
 */

$calligraphy_image = weblazem_blogarchive_option('hero_calligraphy_image', '');
$calligraphy_text  = weblazem_blogarchive_option('hero_calligraphy_text', '<span class="highlight">جهش</span>');
$en_subtitle       = weblazem_blogarchive_option('hero_en_subtitle', '');
$intro             = weblazem_blogarchive_option('hero_intro', '');
$banner_enabled    = weblazem_blogarchive_option('hero_banner_enabled', '1') === '1';
$search_enabled    = weblazem_blogarchive_option('hero_search_enabled', '1') === '1';
$search_placeholder = weblazem_blogarchive_option('hero_banner_text', 'جستجو در مقالات...');
$archive_url       = weblazem_get_blogarchive_page_url();
?>

<section class="blog-archive-hero" dir="rtl">
    <?php if ($banner_enabled) : ?>
        <div class="blog-archive-hero__banner">
            <div class="container blog-archive-hero__banner-inner">
                <?php if ($search_enabled) : ?>
                    <form class="blog-archive-hero__search" action="<?php echo esc_url(home_url('/')); ?>" method="get" role="search">
                        <label class="screen-reader-text" for="blog-archive-search">جستجو</label>
                        <input type="search"
                               id="blog-archive-search"
                               name="s"
                               value="<?php echo esc_attr(get_search_query()); ?>"
                               placeholder="<?php echo esc_attr($search_placeholder); ?>" />
                        <button type="submit" aria-label="جستجو">
                            <i class="fas fa-search" aria-hidden="true"></i>
                        </button>
                    </form>
                <?php endif; ?>
            </div>

            <svg class="blog-archive-hero__banner-wave" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 80" preserveAspectRatio="none" aria-hidden="true">
                <path fill="#ffffff" d="M0,40 C360,80 720,0 1080,40 C1260,60 1380,50 1440,45 L1440,80 L0,80 Z"/>
            </svg>
        </div>
    <?php endif; ?>

    <div class="container blog-archive-hero__content">
        <div class="blog-archive-hero__calligraphy">
            <?php if (!empty($calligraphy_image)) : ?>
                <img src="<?php echo esc_url($calligraphy_image); ?>" alt="" class="blog-archive-hero__calligraphy-img" />
            <?php elseif (!empty($calligraphy_text)) : ?>
                <p class="blog-archive-hero__calligraphy-text"><?php echo wp_kses_post($calligraphy_text); ?></p>
            <?php endif; ?>
        </div>

        <?php if (!empty($en_subtitle)) : ?>
            <p class="blog-archive-hero__en"><?php echo esc_html($en_subtitle); ?></p>
        <?php endif; ?>

        <?php if (!empty($intro)) : ?>
            <p class="blog-archive-hero__intro"><?php echo esc_html($intro); ?></p>
        <?php endif; ?>
    </div>
</section>
