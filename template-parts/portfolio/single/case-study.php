<?php
/**
 * Single portfolio — case study section.
 */

if (!function_exists('weblazem_should_show_case_on_single') || !weblazem_should_show_case_on_single()) {
    return;
}

$settings = weblazem_get_case_study_settings();
$case     = weblazem_get_portfolio_case_data();
$title    = get_the_title();

$has_content = !empty($case['before']) || !empty($case['after'])
    || !empty($case['challenge']) || !empty($case['solution'])
    || !empty($case['result']) || !empty($case['metrics']);

if (!$has_content) {
    return;
}
?>

<section class="cs-single" dir="rtl" id="weblazem-case-study">
    <div class="container">
        <?php if (!empty($settings['section_title'])) : ?>
            <h2 class="cs-single__title"><?php echo esc_html($settings['section_title']); ?></h2>
        <?php endif; ?>

        <?php if (!empty($case['before']) || !empty($case['after'])) : ?>
            <div class="cs-single__compare">
                <figure class="cs-single__figure">
                    <?php if (!empty($case['before'])) : ?>
                        <img src="<?php echo esc_url($case['before']); ?>" alt="<?php echo esc_attr($settings['before_label'] . ' — ' . $title); ?>" loading="lazy" />
                    <?php else : ?>
                        <div class="cs-card__placeholder"><?php echo esc_html($settings['before_label']); ?></div>
                    <?php endif; ?>
                    <figcaption class="cs-single__caption"><?php echo esc_html($settings['before_label']); ?></figcaption>
                </figure>
                <figure class="cs-single__figure">
                    <?php if (!empty($case['after'])) : ?>
                        <img src="<?php echo esc_url($case['after']); ?>" alt="<?php echo esc_attr($settings['after_label'] . ' — ' . $title); ?>" loading="lazy" />
                    <?php else : ?>
                        <div class="cs-card__placeholder"><?php echo esc_html($settings['after_label']); ?></div>
                    <?php endif; ?>
                    <figcaption class="cs-single__caption cs-single__caption--after"><?php echo esc_html($settings['after_label']); ?></figcaption>
                </figure>
            </div>
        <?php endif; ?>

        <?php if (!empty($case['metrics'])) : ?>
            <ul class="cs-single__metrics">
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

        <div class="cs-single__blocks">
            <?php if (!empty($case['challenge'])) : ?>
                <div class="cs-single__block">
                    <h3><?php echo esc_html($settings['challenge_title']); ?></h3>
                    <div class="cs-single__text"><?php echo wp_kses_post(wpautop($case['challenge'])); ?></div>
                </div>
            <?php endif; ?>
            <?php if (!empty($case['solution'])) : ?>
                <div class="cs-single__block">
                    <h3><?php echo esc_html($settings['solution_title']); ?></h3>
                    <div class="cs-single__text"><?php echo wp_kses_post(wpautop($case['solution'])); ?></div>
                </div>
            <?php endif; ?>
            <?php if (!empty($case['result'])) : ?>
                <div class="cs-single__block cs-single__block--result">
                    <h3><?php echo esc_html($settings['result_title']); ?></h3>
                    <div class="cs-single__text"><?php echo wp_kses_post(wpautop($case['result'])); ?></div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
