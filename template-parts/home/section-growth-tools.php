<?php
/**
 * Homepage — growth tools cards (5 feature pages).
 */

$section_title = get_option('weblazem_growth_tools_title', 'ابزارهای هوشمند وب‌لازم');
$section_text  = get_option('weblazem_growth_tools_subtitle', 'از برآورد قیمت تا پیگیری پروژه — همه در یک مسیر شفاف');
$tools         = function_exists('weblazem_growth_tools_list') ? weblazem_growth_tools_list() : array();

if (empty($tools)) {
    return;
}
?>

<section class="weblazem-growth-tools" dir="rtl">
    <div class="container">
        <header class="weblazem-growth-tools__header">
            <?php if ($section_title !== '') : ?>
                <h2 class="weblazem-growth-tools__title"><?php echo esc_html($section_title); ?></h2>
            <?php endif; ?>
            <?php if ($section_text !== '') : ?>
                <p class="weblazem-growth-tools__subtitle"><?php echo esc_html($section_text); ?></p>
            <?php endif; ?>
        </header>

        <div class="weblazem-growth-tools__grid">
            <?php foreach ($tools as $tool) : ?>
                <a
                    href="<?php echo esc_url($tool['url']); ?>"
                    class="weblazem-growth-tools__card"
                    style="--tool-accent: <?php echo esc_attr($tool['color']); ?>"
                >
                    <span class="weblazem-growth-tools__icon" aria-hidden="true">
                        <i class="fas <?php echo esc_attr($tool['icon']); ?>"></i>
                    </span>
                    <h3 class="weblazem-growth-tools__card-title"><?php echo esc_html($tool['title']); ?></h3>
                    <p class="weblazem-growth-tools__card-desc"><?php echo esc_html($tool['desc']); ?></p>
                    <span class="weblazem-growth-tools__card-cta">
                        ورود
                        <i class="fas fa-arrow-left" aria-hidden="true"></i>
                    </span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
