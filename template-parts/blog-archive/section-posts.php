<?php
/**
 * Blog archive — posts grid + pagination.
 */

$query = new WP_Query(weblazem_blogarchive_posts_query_args());
$empty_message = weblazem_blogarchive_option('posts_empty_message', 'به‌زودی مقالات جدید منتشر می‌شود.');
?>

<section class="blog-archive-posts" dir="rtl">
    <div class="container">
        <?php if ($query->have_posts()) : ?>
            <div class="blog-archive-grid">
                <?php
                while ($query->have_posts()) :
                    $query->the_post();
                    get_template_part('template-parts/components/blog', 'card');
                endwhile;
                ?>
            </div>

            <?php weblazem_blogarchive_pagination($query); ?>
        <?php else : ?>
            <p class="blog-archive-empty"><?php echo esc_html($empty_message); ?></p>
        <?php endif; ?>
    </div>
</section>

<?php wp_reset_postdata(); ?>
