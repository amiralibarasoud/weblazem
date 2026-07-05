<?php
/**
 * SEO page — clients / trust section.
 */

$subtitle    = weblazem_seo_option('clients_subtitle', '');
$description = weblazem_seo_option('clients_description', '');
$logos       = get_option('weblazem_seo_clients_logos', array());

if (!is_array($logos)) {
    $logos = array();
}

$logos = array_values(array_filter($logos, function ($logo) {
    return !empty($logo['logo']);
}));
?>

<section class="seo-clients" dir="rtl">
    <div class="seo-page__wave-decor seo-page__wave-decor--corner-tl" aria-hidden="true"></div>
    <div class="seo-page__wave-decor seo-page__wave-decor--corner-tr" aria-hidden="true"></div>
    <div class="seo-page__wave-decor seo-page__wave-decor--corner-bl" aria-hidden="true"></div>
    <div class="seo-page__wave-decor seo-page__wave-decor--corner-br" aria-hidden="true"></div>

    <div class="container">
        <header class="seo-clients__header">
            <div class="seo-clients__calligraphy">
                <?php weblazem_render_service_calligraphy('seo', 'clients_calligraphy_image', 'clients_calligraphy_text'); ?>
            </div>

            <?php if (!empty($subtitle)) : ?>
                <h2 class="seo-clients__subtitle"><?php echo esc_html($subtitle); ?></h2>
            <?php endif; ?>

            <?php if (!empty($description)) : ?>
                <p class="seo-clients__description"><?php echo esc_html($description); ?></p>
            <?php endif; ?>
        </header>

        <?php if (!empty($logos)) : ?>
            <div class="seo-clients__grid">
                <?php foreach ($logos as $logo) : ?>
                    <div class="seo-clients__cell">
                        <?php if (!empty($logo['url'])) : ?>
                            <a href="<?php echo esc_url($logo['url']); ?>" target="_blank" rel="noopener noreferrer">
                        <?php endif; ?>
                            <img src="<?php echo esc_url($logo['logo']); ?>"
                                 alt="<?php echo esc_attr($logo['name'] ?? ''); ?>"
                                 loading="lazy" />
                        <?php if (!empty($logo['url'])) : ?>
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
