<?php
/**
 * Archive template for portfolio post type.
 */

get_header();

$card_btn_text = get_option('weblazem_portfolio_card_button_text', 'مشاهده‌ی پروژه');
$archive_url   = weblazem_get_portfolio_archive_url();
?>

<main class="weblazem-portfolio-archive" dir="rtl">
    <div class="container">
        <div class="portfolio-archive-header">
            <h1 class="archive-title"><?php post_type_archive_title(); ?></h1>
            <p class="archive-description">همه نمونه کارهای طراحی و توسعه وب‌لازم</p>
        </div>

        <?php if (have_posts()) : ?>
            <div class="portfolio-cards">
                <?php
                while (have_posts()) :
                    the_post();
                    weblazem_render_portfolio_card(array(
                        'card_btn_text' => $card_btn_text,
                        'heading_tag'   => 'h2',
                    ));
                endwhile;
                ?>
            </div>

            <div class="portfolio-archive-pagination">
                <?php the_posts_pagination(array(
                    'mid_size'  => 2,
                    'prev_text' => '&rarr; قبلی',
                    'next_text' => 'بعدی &larr;',
                )); ?>
            </div>
        <?php else : ?>
            <p class="portfolio-section-empty">هنوز نمونه کاری ثبت نشده است.</p>
            <a href="<?php echo esc_url(home_url('/')); ?>" class="portfolio-more-button" style="margin-top: 24px;">بازگشت به صفحه اصلی</a>
        <?php endif; ?>
    </div>
</main>

<?php get_footer(); ?>
