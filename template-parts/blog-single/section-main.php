<?php
/**
 * Blog single — article body + optional sidebar.
 */

$post_id   = get_the_ID();
$thumb     = weblazem_get_blog_single_post_thumb($post_id);
if (!$thumb) {
    $thumb = get_the_post_thumbnail_url($post_id, 'large');
}
$sidebar_on = weblazem_is_blog_single_section_enabled('sidebar')
    && (
        weblazem_blog_single_option('sidebar_categories_enabled', '1') === '1'
        || weblazem_blog_single_option('sidebar_latest_enabled', '1') === '1'
    );
?>

<section class="blog-single-main" dir="rtl">
    <div class="container">
        <div class="blog-single-main__grid<?php echo $sidebar_on ? '' : ' blog-single-main__grid--full'; ?>">
            <article class="blog-single-article">
                <h1 class="blog-single-article__title"><?php the_title(); ?></h1>

                <?php if (has_excerpt()) : ?>
                    <p class="blog-single-article__intro"><?php echo esc_html(get_the_excerpt()); ?></p>
                <?php endif; ?>

                <?php if ($thumb) : ?>
                    <div class="blog-single-article__featured">
                        <img src="<?php echo esc_url($thumb); ?>" alt="<?php the_title_attribute(); ?>" />
                    </div>
                <?php endif; ?>

                <div class="blog-single-article__content entry-content">
                    <?php the_content(); ?>
                </div>
            </article>

            <?php if ($sidebar_on) {
                get_template_part('template-parts/blog-single/partial', 'sidebar');
            } ?>
        </div>
    </div>
</section>
