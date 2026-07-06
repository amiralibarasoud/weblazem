<?php
/**
 * Blog single — large featured image below banner.
 */

$post_id = get_the_ID();
$thumb   = weblazem_get_blog_single_post_thumb($post_id);
if (!$thumb) {
    $thumb = get_the_post_thumbnail_url($post_id, 'large');
}
if (!$thumb) {
    return;
}
?>

<section class="blog-single-hero-img" dir="rtl">
    <div class="container">
        <div class="blog-single-hero-img__frame">
            <img src="<?php echo esc_url($thumb); ?>" alt="<?php the_title_attribute(); ?>" />
        </div>
    </div>
</section>
