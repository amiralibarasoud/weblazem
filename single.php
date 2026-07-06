<?php
/**
 * Blog post single template.
 */

get_header();

$post_id = get_the_ID();
$thumb   = get_the_post_thumbnail_url($post_id, 'large');
if (!$thumb) {
    $thumb = get_post_meta($post_id, '_weblazem_demo_thumb', true);
}
?>

<main class="weblazem-blog-archive-page blog-single" dir="rtl">
    <div class="container">
        <a href="<?php echo esc_url(weblazem_get_blogarchive_page_url()); ?>" class="blog-single__back">
            <i class="fas fa-arrow-right" aria-hidden="true"></i>
            بازگشت به مجله
        </a>

        <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
            <article>
                <h1 class="blog-single__title"><?php the_title(); ?></h1>
                <p class="blog-single__meta"><?php echo esc_html(get_the_date()); ?></p>

                <?php if ($thumb) : ?>
                    <div class="blog-single__thumb">
                        <img src="<?php echo esc_url($thumb); ?>" alt="<?php the_title_attribute(); ?>" />
                    </div>
                <?php endif; ?>

                <div class="blog-single__content">
                    <?php the_content(); ?>
                </div>
            </article>
        <?php endwhile; endif; ?>
    </div>
</main>

<?php get_footer(); ?>
