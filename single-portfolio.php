<?php
/**
 * Single portfolio project template.
 */

get_header();
?>

<main class="weblazem-portfolio-single" dir="rtl">
    <div class="container">
        <?php while (have_posts()) : the_post(); ?>
            <article <?php post_class(); ?>>
                <h1 class="entry-title"><?php the_title(); ?></h1>

                <?php
                $client = get_post_meta(get_the_ID(), '_weblazem_portfolio_client', true);
                $external_url = get_post_meta(get_the_ID(), '_weblazem_portfolio_project_url', true);
                ?>

                <?php if ($client) : ?>
                    <div class="entry-meta">کارفرما: <?php echo esc_html($client); ?></div>
                <?php endif; ?>

                <?php if (has_post_thumbnail()) : ?>
                    <div class="entry-thumbnail">
                        <?php the_post_thumbnail('large'); ?>
                    </div>
                <?php endif; ?>

                <div class="entry-content">
                    <?php the_content(); ?>
                </div>

                <?php
                $project_link = !empty($external_url) ? $external_url : get_permalink();
                $btn_text = get_option('weblazem_portfolio_card_button_text', 'مشاهده‌ی پروژه');
                ?>
                <a href="<?php echo esc_url($project_link); ?>"
                   class="portfolio-project-link"
                   <?php echo !empty($external_url) ? 'target="_blank" rel="noopener noreferrer"' : ''; ?>>
                    <?php echo esc_html($btn_text); ?>
                    <i class="fas fa-arrow-left" aria-hidden="true"></i>
                </a>
            </article>
        <?php endwhile; ?>
    </div>
</main>

<?php get_footer(); ?>
