<?php
/**
 * Portfolio archive — all projects with category tabs and pagination.
 */

$section_title = get_option('weblazem_portfolio_page_all_title', 'تمام پروژه‌ها');
$card_btn_text = get_option('weblazem_portfolio_page_card_button_text', 'مشاهده پروژه');
$tabs          = weblazem_get_portfolio_page_tabs();
$active_tab    = weblazem_get_active_portfolio_tab();
$active_key    = weblazem_get_active_portfolio_tab_key();

$portfolio_all_query = new WP_Query(weblazem_build_portfolio_list_query_args($active_tab));
?>

<section id="portfolio-all-projects" class="portfolio-page-section portfolio-page-all" dir="rtl">
    <div class="container">
        <?php if (!empty($section_title)) : ?>
            <h2 class="portfolio-page-section__title"><?php echo esc_html($section_title); ?></h2>
        <?php endif; ?>

        <?php if (!empty($tabs)) : ?>
            <nav class="portfolio-page-tabs" aria-label="فیلتر نمونه کارها">
                <?php foreach ($tabs as $tab) :
                    $is_active = ($tab['key'] === $active_key);
                    $tab_url   = weblazem_get_portfolio_tab_url($tab['key']);
                    ?>
                    <a href="<?php echo esc_url($tab_url); ?>"
                       class="portfolio-page-tabs__btn<?php echo $is_active ? ' is-active' : ''; ?>"
                       <?php echo $is_active ? 'aria-current="page"' : ''; ?>>
                        <?php echo esc_html($tab['title']); ?>
                    </a>
                <?php endforeach; ?>
            </nav>
        <?php endif; ?>

        <?php if ($portfolio_all_query->have_posts()) : ?>
            <div class="portfolio-page-cards">
                <?php
                while ($portfolio_all_query->have_posts()) :
                    $portfolio_all_query->the_post();
                    weblazem_render_portfolio_card(array(
                        'card_btn_text' => $card_btn_text,
                        'heading_tag'   => 'h3',
                        'variant'       => 'archive',
                    ));
                endwhile;
                ?>
            </div>

            <?php weblazem_portfolio_pagination($portfolio_all_query, $active_key); ?>
        <?php else : ?>
            <p class="portfolio-page-empty">نمونه کاری در این دسته یافت نشد.</p>
        <?php endif; ?>
    </div>
</section>

<?php wp_reset_postdata(); ?>
