<?php
/**
 * Blog post card for archive grid.
 */

$post_id   = get_the_ID();
$permalink = get_permalink();
$title     = get_the_title();
$excerpt   = has_excerpt() ? get_the_excerpt() : wp_trim_words(wp_strip_all_tags(get_the_content()), 28, '…');
$thumb     = get_the_post_thumbnail_url($post_id, 'large');
if (!$thumb) {
    $thumb = get_post_meta($post_id, '_weblazem_demo_thumb', true);
}
$day       = get_the_date('d');
$month     = strtoupper(get_the_date('M'));
$year      = get_the_date('Y');
$default_gradients = array(
    'linear-gradient(135deg, #1d4ed8 0%, #3b82f6 100%)',
    'linear-gradient(135deg, #7c3aed 0%, #a78bfa 100%)',
    'linear-gradient(135deg, #0d9488 0%, #2dd4bf 100%)',
    'linear-gradient(135deg, #db2777 0%, #f472b6 100%)',
    'linear-gradient(135deg, #1e3a5f 0%, #2563eb 100%)',
    'linear-gradient(135deg, #4f1c61 0%, #7c3aed 100%)',
);
$gradient = $default_gradients[$post_id % count($default_gradients)];
?>

<article class="blog-card">
    <a href="<?php echo esc_url($permalink); ?>" class="blog-card__link">
        <div class="blog-card__media">
            <?php if ($thumb) : ?>
                <img src="<?php echo esc_url($thumb); ?>" alt="<?php echo esc_attr($title); ?>" loading="lazy" />
            <?php else : ?>
                <div class="blog-card__placeholder" style="background: <?php echo esc_attr($gradient); ?>;"></div>
            <?php endif; ?>

            <div class="blog-card__date" aria-label="<?php echo esc_attr(get_the_date()); ?>">
                <span class="blog-card__date-day"><?php echo esc_html($day); ?></span>
                <span class="blog-card__date-month"><?php echo esc_html($month); ?></span>
                <span class="blog-card__date-year"><?php echo esc_html($year); ?></span>
            </div>

            <span class="blog-card__icon" aria-hidden="true">
                <i class="fas fa-book-open"></i>
            </span>
        </div>

        <div class="blog-card__body">
            <h2 class="blog-card__title"><?php echo esc_html($title); ?></h2>
            <?php if (!empty($excerpt)) : ?>
                <p class="blog-card__excerpt"><?php echo esc_html($excerpt); ?></p>
            <?php endif; ?>
        </div>
    </a>
</article>
