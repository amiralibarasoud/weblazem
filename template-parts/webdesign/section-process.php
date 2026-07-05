<?php
/**
 * Website design — process timeline + CSAT.
 */

$subtitle    = weblazem_webdesign_option('process_subtitle', 'طراحی یک وب‌سایت حرفه‌ای به یک برنامه دقیق نیاز دارد');
$description = weblazem_webdesign_option('process_description', '');
$start_note  = weblazem_webdesign_option('process_start_note', 'از اینجا شروع کنیم');
$steps       = get_option('weblazem_webdesign_process_steps', array());
$csat_num    = weblazem_webdesign_option('process_csat_number', '98%');
$csat_label  = weblazem_webdesign_option('process_csat_label', 'شاخص رضایت مشتریان');
$csat_sub    = weblazem_webdesign_option('process_csat_sub', 'CSAT — Customer Satisfaction Score');
$btn1_text   = weblazem_webdesign_option('process_btn1_text', 'معرفی تیم');
$btn1_url    = weblazem_webdesign_option('process_btn1_url', '#');
$btn2_text   = weblazem_webdesign_option('process_btn2_text', 'درباره ما');
$btn2_url    = weblazem_webdesign_option('process_btn2_url', '#');

if (!is_array($steps)) {
    $steps = array();
}

$steps = array_values(array_filter($steps, function ($step) {
    return !empty($step['title']);
}));

$step_count = count($steps);
?>

<section class="webdesign-process" dir="rtl">
    <div class="container">
        <header class="webdesign-process__header">
            <div class="webdesign-process__calligraphy">
                <?php weblazem_render_webdesign_calligraphy('process_calligraphy_image', 'process_calligraphy_text'); ?>
            </div>

            <?php if (!empty($subtitle)) : ?>
                <h2 class="webdesign-process__subtitle"><?php echo esc_html($subtitle); ?></h2>
            <?php endif; ?>

            <?php if (!empty($description)) : ?>
                <p class="webdesign-process__description"><?php echo esc_html($description); ?></p>
            <?php endif; ?>
        </header>

        <?php if (!empty($steps)) : ?>
            <div class="webdesign-process__timeline-wrap">
                <?php if (!empty($start_note)) : ?>
                    <p class="webdesign-process__start-note">
                        <i class="fas fa-hand-point-left" aria-hidden="true"></i>
                        <?php echo esc_html($start_note); ?>
                    </p>
                <?php endif; ?>

                <div class="webdesign-process__timeline" role="list">
                    <?php foreach ($steps as $index => $step) : ?>
                        <div class="webdesign-process__timeline-item" role="listitem">
                            <div class="webdesign-process__timeline-node">
                                <span class="webdesign-process__timeline-num"><?php echo (int) $index + 1; ?></span>
                            </div>
                            <?php if ($index < $step_count - 1) : ?>
                                <div class="webdesign-process__timeline-line" aria-hidden="true"></div>
                            <?php endif; ?>
                            <p class="webdesign-process__timeline-label"><?php echo esc_html($step['title']); ?></p>
                        </div>
                    <?php endforeach; ?>

                    <div class="webdesign-process__timeline-end" aria-hidden="true">
                        <span class="webdesign-process__timeline-rocket"><i class="fas fa-rocket"></i></span>
                    </div>
                </div>
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
