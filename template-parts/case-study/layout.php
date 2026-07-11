<?php
/**
 * Success stories listing — Challenge → Approach → Results.
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
            <ol class="cs-listing__flow" aria-label="ساختار داستان موفقیت">
                <li><span>۱</span> <?php echo esc_html($settings['challenge_title']); ?></li>
                <li><span>۲</span> <?php echo esc_html($settings['solution_title']); ?></li>
                <li><span>۳</span> <?php echo esc_html($settings['result_title']); ?></li>
            </ol>
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
                    $case  = weblazem_get_portfolio_case_data(get_the_ID());
                    $title = get_the_title();
                    $link  = get_permalink();
                    $thumb = has_post_thumbnail() ? get_the_post_thumbnail_url(get_the_ID(), 'large') : '';
                    ?>
                    <article <?php post_class('cs-card cs-card--story'); ?>>
                        <div class="cs-card__body">
                            <p class="cs-card__eyebrow">داستان موفقیت</p>
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
                            <?php endif; ?>

                            <div class="cs-card__story-steps">
                                <?php if (!empty($case['challenge'])) : ?>
                                    <div class="cs-card__step">
                                        <strong><?php echo esc_html($settings['challenge_title']); ?></strong>
                                        <p><?php echo esc_html(wp_trim_words(wp_strip_all_tags($case['challenge']), 18)); ?></p>
                                    </div>
                                <?php endif; ?>
                                <?php if (!empty($case['solution'])) : ?>
                                    <div class="cs-card__step">
                                        <strong><?php echo esc_html($settings['solution_title']); ?></strong>
                                        <p><?php echo esc_html(wp_trim_words(wp_strip_all_tags($case['solution']), 18)); ?></p>
                                    </div>
                                <?php endif; ?>
                                <?php if (!empty($case['result'])) : ?>
                                    <div class="cs-card__step cs-card__step--result">
                                        <strong><?php echo esc_html($settings['result_title']); ?></strong>
                                        <p><?php echo esc_html(wp_trim_words(wp_strip_all_tags($case['result']), 18)); ?></p>
                                    </div>
                                <?php elseif (empty($case['metrics']) && empty($case['challenge'])) : ?>
                                    <p class="cs-card__excerpt"><?php echo esc_html(wp_trim_words(get_the_excerpt(), 22)); ?></p>
                                <?php endif; ?>
                            </div>

                            <?php if (!empty($case['before']) || !empty($case['after']) || $thumb) : ?>
                                <div class="cs-card__visuals" aria-label="<?php echo esc_attr($settings['visuals_title']); ?>">
                                    <?php if (!empty($case['before'])) : ?>
                                        <figure>
                                            <img src="<?php echo esc_url($case['before']); ?>" alt="<?php echo esc_attr($settings['before_label'] . ' — ' . $title); ?>" loading="lazy" />
                                            <figcaption><?php echo esc_html($settings['before_label']); ?></figcaption>
                                        </figure>
                                    <?php endif; ?>
                                    <?php
                                    $after_src = !empty($case['after']) ? $case['after'] : $thumb;
                                    if ($after_src) :
                                        ?>
                                        <figure>
                                            <img src="<?php echo esc_url($after_src); ?>" alt="<?php echo esc_attr($settings['after_label'] . ' — ' . $title); ?>" loading="lazy" />
                                            <figcaption><?php echo esc_html($settings['after_label']); ?></figcaption>
                                        </figure>
                                    <?php endif; ?>
                                </div>
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
