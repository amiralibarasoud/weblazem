<?php
/**
 * Shared process journey section (gradient cards + CSAT).
 *
 * @param array $args prefix (webdesign|seo), wave (bool)
 */

$prefix = $args['prefix'] ?? 'webdesign';
$show_wave = !empty($args['wave']);

$subtitle    = weblazem_service_option($prefix, 'process_subtitle', '');
$description = weblazem_service_option($prefix, 'process_description', '');
$start_note  = weblazem_service_option($prefix, 'process_start_note', 'از اینجا شروع کنیم');
$caption     = weblazem_service_option($prefix, 'process_journey_caption', 'مسیر طراحی، اجرا و تحویل پروژه شما');
$steps       = weblazem_service_get_option($prefix, 'process_steps', array());
$csat_num    = weblazem_service_option($prefix, 'process_csat_number', '98%');
$csat_label  = weblazem_service_option($prefix, 'process_csat_label', 'شاخص رضایت مشتریان');
$csat_sub    = weblazem_service_option($prefix, 'process_csat_sub', 'CSAT — Customer Satisfaction Score');
$btn1_text   = weblazem_service_option($prefix, 'process_btn1_text', 'معرفی تیم');
$btn1_url    = weblazem_service_option($prefix, 'process_btn1_url', '#');
$btn2_text   = weblazem_service_option($prefix, 'process_btn2_text', 'درباره ما');
$btn2_url    = weblazem_service_option($prefix, 'process_btn2_url', '#');

if (!is_array($steps)) {
    $steps = array();
}

$steps = array_values(array_filter($steps, function ($step) {
    return !empty($step['title']);
}));

$step_count = count($steps);
?>

<section class="webdesign-process<?php echo $show_wave ? ' webdesign-process--wave-top' : ''; ?>" dir="rtl">
    <?php if ($show_wave) : ?>
        <svg class="webdesign-process__wave" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 80" preserveAspectRatio="none" aria-hidden="true">
            <path fill="#05052d" d="M0,40 C360,80 720,0 1080,40 C1260,60 1380,50 1440,45 L1440,80 L0,80 Z"/>
            <path fill="none" stroke="url(#seoWaveGrad)" stroke-width="2" d="M0,42 C360,78 720,2 1080,42 C1260,58 1380,48 1440,44"/>
            <defs>
                <linearGradient id="seoWaveGrad" x1="0%" y1="0%" x2="100%" y2="0%">
                    <stop offset="0%" stop-color="#e879f9"/>
                    <stop offset="100%" stop-color="#fb923c"/>
                </linearGradient>
            </defs>
        </svg>
    <?php endif; ?>

    <div class="container">
        <header class="webdesign-process__header">
            <div class="webdesign-process__calligraphy">
                <?php weblazem_render_service_calligraphy($prefix, 'process_calligraphy_image', 'process_calligraphy_text'); ?>
            </div>

            <?php if (!empty($subtitle)) : ?>
                <h2 class="webdesign-process__subtitle"><?php echo esc_html($subtitle); ?></h2>
            <?php endif; ?>

            <?php if (!empty($description)) : ?>
                <p class="webdesign-process__description"><?php echo esc_html($description); ?></p>
            <?php endif; ?>
        </header>

        <?php if (!empty($steps)) : ?>
            <div class="webdesign-process__journey">
                <?php if (!empty($start_note)) : ?>
                    <p class="webdesign-process__start-note">
                        <i class="fas fa-route" aria-hidden="true"></i>
                        <?php echo esc_html($start_note); ?>
                    </p>
                <?php endif; ?>

                <div class="webdesign-process__cards" role="list">
                    <?php foreach ($steps as $index => $step) : ?>
                        <article class="webdesign-process__card" role="listitem" style="--step-i: <?php echo (int) $index; ?>">
                            <span class="webdesign-process__card-bg-num" aria-hidden="true"><?php echo str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT); ?></span>

                            <div class="webdesign-process__card-top">
                                <span class="webdesign-process__card-badge">
                                    <span class="webdesign-process__card-plus" aria-hidden="true">+</span>
                                    <span class="webdesign-process__card-num"><?php echo (int) $index + 1; ?></span>
                                </span>
                            </div>

                            <h3 class="webdesign-process__card-title"><?php echo esc_html($step['title']); ?></h3>

                            <?php if ($index < $step_count - 1) : ?>
                                <span class="webdesign-process__card-arrow" aria-hidden="true">
                                    <i class="fas fa-chevron-left"></i>
                                </span>
                            <?php endif; ?>
                        </article>
                    <?php endforeach; ?>
                </div>

                <?php if (!empty($caption)) : ?>
                    <p class="webdesign-process__journey-caption">
                        <i class="fas fa-rocket" aria-hidden="true"></i>
                        <?php echo esc_html($caption); ?>
                    </p>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <div class="webdesign-process__footer">
            <?php if (!empty($btn1_text)) : ?>
                <a href="<?php echo esc_url($btn1_url); ?>" class="webdesign-process__footer-btn">
                    <?php echo esc_html($btn1_text); ?>
                </a>
            <?php endif; ?>

            <div class="webdesign-process__csat">
                <div class="webdesign-process__csat-stars" aria-hidden="true">
                    <?php for ($s = 0; $s < 5; $s++) : ?>
                        <i class="fas fa-star"></i>
                    <?php endfor; ?>
                </div>
                <span class="webdesign-process__csat-number"><?php echo esc_html($csat_num); ?></span>
                <span class="webdesign-process__csat-sub"><?php echo esc_html($csat_sub); ?></span>
                <?php if (!empty($csat_label)) : ?>
                    <span class="webdesign-process__csat-label"><?php echo esc_html($csat_label); ?></span>
                <?php endif; ?>
            </div>

            <?php if (!empty($btn2_text)) : ?>
                <a href="<?php echo esc_url($btn2_url); ?>" class="webdesign-process__footer-btn">
                    <?php echo esc_html($btn2_text); ?>
                </a>
            <?php endif; ?>
        </div>
    </div>
</section>
