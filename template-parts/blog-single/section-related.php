<?php
/**
 * Blog single — related posts by category (dark multi-column section).
 */

$post_id = get_the_ID();
$count   = (int) weblazem_blog_single_option('related_count', '4');
$query   = weblazem_get_blog_single_related_posts($post_id, $count);

if (!$query->have_posts()) {
    return;
}
?>

<section class="blog-single-related" dir="rtl">
    <div class="blog-single-related__curve" aria-hidden="true"></div>
    <div class="container blog-single-related__inner">
        <div class="blog-single-related__grid">
            <?php while ($query->have_posts()) : $query->the_post(); ?>
                <article class="blog-single-related__col">
                    <h3 class="blog-single-related__col-title">
                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                    </h3>
                    <p class="blog-single-related__col-text"><?php echo esc_html(wp_trim_words(get_the_excerpt(), 28)); ?></p>
                </article>
            <?php endwhile; wp_reset_postdata(); ?>
        </div>
    </div>
</section>
