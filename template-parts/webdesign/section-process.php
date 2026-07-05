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
            <div class="webdesign-process__flow">
                <?php if (!empty($start_note)) : ?>
                    <div class="webdesign-process__start-note">
                        <span><?php echo esc_html($start_note); ?></span>
                        <i class="fas fa-arrow-left" aria-hidden="true"></i>
                    </div>
                <?php endif; ?>

                <ol class="webdesign-process__steps">
                    <?php foreach ($steps as $index => $step) : ?>
                        <li class="webdesign-process__step" style="--step-index: <?php echo (int) $index; ?>">
                            <span class="webdesign-process__step-pill">
                                <span class="webdesign-process__step-icon" aria-hidden="true">+</span>
                                <?php echo esc_html($step['title']); ?>
                            </span>
                        </li>
                    <?php endforeach; ?>
                </ol>

                <div class="webdesign-process__rocket" aria-hidden="true">
                    <i class="fas fa-rocket"></i>
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
