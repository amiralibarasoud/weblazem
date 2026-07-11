<?php
/**
 * Case studies listing layout.
 */

$settings = weblazem_get_case_study_settings();
$query    = weblazem_query_case_study_portfolios();
?>

<section class="cs-listing" dir="rtl">
    <div class="cs-listing__bg" aria-hidden="true"></div>
    <div class="container">
        <header class="cs-listing__header">
            <?php if (!empty($settings['title'])) : ?>
                <h1 class="cs-listing__title"><?php echo esc_html($settings['title']); ?></h1>
            <?php endif; ?>
            <?php if (!empty($settings['subtitle'])) : ?>
                <p class="cs-listing__subtitle"><?php echo esc_html($settings['subtitle']); ?></p>
            <?php endif; ?>
        </header>

        <?php if (!$query->have_posts()) : ?>
            <div class="cs-empty">
                <p><?php echo esc_html($settings['empty_text']); ?></p>
            </div>
        <?php else : ?>
            <div class="cs-grid">
                <?php
                while ($query->have_posts()) :
                    $query->the_post();
                    $case    = weblazem_get_portfolio_case_data(get_the_ID());
                    $title   = get_the_title();
                    $link    = get_permalink();
                    $before  = $case['before'];
                    $after   = $case['after'];
                    $thumb   = has_post_thumbnail() ? get_the_post_thumbnail_url(get_the_ID(), 'large') : '';
                    if (empty($after) && $thumb) {
                        $after = $thumb;
                    }
                    ?>
                    <article <?php post_class('cs-card'); ?>>
                        <div class="cs-card__compare" data-cs-compare>
                            <div class="cs-card__pane cs-card__pane--before">
                                <?php if (!empty($before)) : ?>
                                    <img src="<?php echo esc_url($before); ?>" alt="<?php echo esc_attr($settings['before_label'] . ' — ' . $title); ?>" loading="lazy" />
                                <?php else : ?>
                                    <div class="cs-card__placeholder"><?php echo esc_html($settings['before_label']); ?></div>
                                <?php endif; ?>
                                <span class="cs-card__badge"><?php echo esc_html($settings['before_label']); ?></span>
                            </div>
                            <div class="cs-card__pane cs-card__pane--after">
                                <?php if (!empty($after)) : ?>
                                    <img src="<?php echo esc_url($after); ?>" alt="<?php echo esc_attr($settings['after_label'] . ' — ' . $title); ?>" loading="lazy" />
                                <?php else : ?>
                                    <div class="cs-card__placeholder"><?php echo esc_html($settings['after_label']); ?></div>
                                <?php endif; ?>
                                <span class="cs-card__badge cs-card__badge--after"><?php echo esc_html($settings['after_label']); ?></span>
                            </div>
                        </div>

                        <div class="cs-card__body">
                            <h2 class="cs-card__title">
                                <a href="<?php echo esc_url($link); ?>"><?php echo esc_html($title); ?></a>
                            </h2>

                            <?php if (!empty($case['metrics'])) : ?>
                                <ul class="cs-card__metrics">
                                    <?php foreach ($case['metrics'] as $metric) : ?>
                                        <li>
                                            <?php if ($metric['value'] !== '') : ?>
                                                <strong><?php echo esc_html($metric['value']); ?></strong>
                                            <?php endif; ?>
                                            <?php if ($metric['label'] !== '') : ?>
                                                <span><?php echo esc_html($metric['label']); ?></span>
                                            <?php endif; ?>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php elseif (!empty($case['challenge'])) : ?>
                                <p class="cs-card__excerpt"><?php echo esc_html(wp_trim_words(wp_strip_all_tags($case['challenge']), 22)); ?></p>
                            <?php endif; ?>

                            <a class="cs-card__btn" href="<?php echo esc_url($link); ?>">
                                <?php echo esc_html($settings['card_button_text']); ?>
                            </a>
                        </div>
                    </article>
                <?php endwhile; ?>
                <?php wp_reset_postdata(); ?>
            </div>
        <?php endif; ?>
    </div>
</section>
