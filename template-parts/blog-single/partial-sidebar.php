<?php
/**
 * Blog single — sidebar widgets.
 */

$show_categories = weblazem_blog_single_option('sidebar_categories_enabled', '1') === '1';
$show_latest     = weblazem_blog_single_option('sidebar_latest_enabled', '1') === '1';
$cat_title       = weblazem_blog_single_option('sidebar_categories_title', 'موضوعات');
$latest_title    = weblazem_blog_single_option('sidebar_latest_title', 'آخرین مقالات');
$latest_count    = (int) weblazem_blog_single_option('sidebar_latest_count', '6');
$cat_limit       = (int) weblazem_blog_single_option('sidebar_categories_count', '0');
$post_id         = get_the_ID();

if (!$show_categories && !$show_latest) {
    return;
}
?>

<aside class="blog-single-sidebar" dir="rtl">
    <?php if ($show_categories) :
        $categories = weblazem_get_blog_single_sidebar_categories();
        if ($cat_limit > 0) {
            $categories = array_slice($categories, 0, $cat_limit);
        }
        if (!empty($categories)) : ?>
            <div class="blog-single-sidebar__widget">
                <h2 class="blog-single-sidebar__title"><?php echo esc_html($cat_title); ?></h2>
                <div class="blog-single-sidebar__box">
                    <ul class="blog-single-sidebar__cats">
                        <?php foreach ($categories as $cat) : ?>
                            <li>
                                <a href="<?php echo esc_url(get_category_link($cat->term_id)); ?>">
                                    <?php echo esc_html($cat->name); ?>
                                    <span class="blog-single-sidebar__count">(<?php echo esc_html(number_format_i18n($cat->count)); ?>)</span>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        <?php endif;
    endif; ?>

    <?php if ($show_latest) :
        $latest_query = weblazem_get_blog_single_latest_posts($latest_count, $post_id);
        if ($latest_query->have_posts()) : ?>
            <div class="blog-single-sidebar__widget">
                <h2 class="blog-single-sidebar__title"><?php echo esc_html($latest_title); ?></h2>
                <ul class="blog-single-sidebar__latest">
                    <?php while ($latest_query->have_posts()) : $latest_query->the_post();
                        $item_thumb = weblazem_get_blog_single_post_thumb(get_the_ID());
                        ?>
                        <li class="blog-single-sidebar__latest-item">
                            <?php if ($item_thumb) : ?>
                                <a href="<?php the_permalink(); ?>" class="blog-single-sidebar__latest-thumb">
                                    <img src="<?php echo esc_url($item_thumb); ?>" alt="<?php the_title_attribute(); ?>" />
                                </a>
                            <?php endif; ?>
                            <div class="blog-single-sidebar__latest-body">
                                <a href="<?php the_permalink(); ?>" class="blog-single-sidebar__latest-title"><?php the_title(); ?></a>
                                <p class="blog-single-sidebar__latest-excerpt"><?php echo esc_html(wp_trim_words(get_the_excerpt(), 14)); ?></p>
                                <p class="blog-single-sidebar__latest-date">
                                    <?php
                                    printf(
                                        'آخرین به‌روزرسانی: %s',
                                        esc_html(get_the_modified_date('j F Y'))
                                    );
                                    ?>
                                </p>
                            </div>
                        </li>
                    <?php endwhile; wp_reset_postdata(); ?>
                </ul>
            </div>
        <?php endif;
    endif; ?>
</aside>
