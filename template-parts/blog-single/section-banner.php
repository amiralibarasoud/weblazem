<?php
/**
 * Blog single — intro banner with illustration.
 */

$line1 = weblazem_blog_single_option('banner_line1', 'به‌روزترین اخبار از طراحی سایت، سئو و دیجیتال مارکتینگ');
$line2 = weblazem_blog_single_option('banner_line2', 'را در بلاگ وب‌لازم جستجو کنید');
$image = weblazem_blog_single_option('banner_image', '');
if (!$image) {
    $image = get_template_directory_uri() . '/assets/images/blog-single/banner-browser.svg';
}
?>

<section class="blog-single-banner" dir="rtl">
    <div class="container blog-single-banner__inner">
        <div class="blog-single-banner__text">
            <p class="blog-single-banner__line1"><?php echo esc_html($line1); ?></p>
            <p class="blog-single-banner__line2"><?php echo esc_html($line2); ?></p>
        </div>
        <div class="blog-single-banner__illus" aria-hidden="true">
            <img src="<?php echo esc_url($image); ?>" alt="" />
        </div>
    </div>
</section>
