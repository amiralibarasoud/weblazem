<?php
/**
 * Public CSAT stats block — usable on CSAT page and homepage.
 *
 * @var array $args Optional: settings, stats, full (bool)
 */

$args     = isset($args) && is_array($args) ? $args : array();
$settings = isset($args['settings']) ? $args['settings'] : (function_exists('weblazem_get_csat_settings') ? weblazem_get_csat_settings() : array());
$stats    = isset($args['stats']) ? $args['stats'] : (function_exists('weblazem_get_csat_stats') ? weblazem_get_csat_stats() : array());
$full     = !empty($args['full']);

if (empty($stats) || !is_array($stats)) {
    return;
}

$avg   = isset($stats['avg']) ? (float) $stats['avg'] : 0;
$count = isset($stats['count']) ? (int) $stats['count'] : 0;
$pct   = $avg > 0 ? min(100, round(($avg / 5) * 100)) : 0;
$dist  = isset($stats['distribution']) && is_array($stats['distribution']) ? $stats['distribution'] : array();
$cats  = isset($stats['categories']) && is_array($stats['categories']) ? $stats['categories'] : array();
$quotes = array();

if (!empty($stats['featured']) && is_array($stats['featured'])) {
    $quotes = $stats['featured'];
} elseif (!empty($stats['published']) && is_array($stats['published'])) {
    foreach ($stats['published'] as $item) {
        if (!empty($item['comment'])) {
            $quotes[] = $item;
        }
        if (count($quotes) >= 6) {
            break;
        }
    }
}

$compact = !$full;
$page_url = function_exists('weblazem_get_csat_page_url') ? weblazem_get_csat_page_url() : '';
?>

<div class="csat-public <?php echo $compact ? 'csat-public--compact' : ''; ?>" dir="rtl">
    <?php if ($full) : ?>
        <header class="csat-header">
            <h1 class="csat-header__title"><?php echo esc_html($settings['public_title'] ?? 'رضایت مشتریان'); ?></h1>
            <p class="csat-header__subtitle"><?php echo esc_html($settings['public_subtitle'] ?? ''); ?></p>
        </header>
    <?php else : ?>
        <div class="csat-public__intro">
            <h3 class="csat-public__heading">رضایت مشتریان (CSAT)</h3>
            <?php if ($page_url) : ?>
                <a class="csat-public__more" href="<?php echo esc_url($page_url); ?>">جزئیات بیشتر</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <?php if ($count < 1) : ?>
        <div class="csat-empty">
            <p>هنوز پاسخی ثبت نشده است. پس از تکمیل پروژه‌ها، میانگین رضایت اینجا نمایش داده می‌شود.</p>
        </div>
    <?php else : ?>
        <div class="csat-stats">
            <div class="csat-ring" style="--csat-pct: <?php echo (float) $pct; ?>;">
                <div class="csat-ring__inner">
                    <strong class="csat-ring__score"><?php echo esc_html(number_format_i18n($avg, 1)); ?></strong>
                    <span class="csat-ring__of">از ۵</span>
                </div>
            </div>

            <div class="csat-stats__meta">
                <p class="csat-stats__count">
                    بر اساس <strong><?php echo esc_html(number_format_i18n($count)); ?></strong> نظرسنجی پس از تحویل پروژه
                </p>

                <?php if ($full && !empty($dist)) : ?>
                    <ul class="csat-dist">
                        <?php for ($i = 5; $i >= 1; $i--) :
                            $n = isset($dist[$i]) ? (int) $dist[$i] : 0;
                            $bar = $count > 0 ? round(($n / $count) * 100) : 0;
                            ?>
                            <li>
                                <span class="csat-dist__label"><?php echo (int) $i; ?>★</span>
                                <span class="csat-dist__bar"><i style="width:<?php echo (int) $bar; ?>%"></i></span>
                                <span class="csat-dist__n"><?php echo esc_html(number_format_i18n($n)); ?></span>
                            </li>
                        <?php endfor; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>

        <?php if ($full && !empty($cats)) : ?>
            <div class="csat-cat-rings">
                <?php foreach ($cats as $key => $cat) :
                    if (empty($cat['count'])) {
                        continue;
                    }
                    $cavg = (float) $cat['avg'];
                    $cpct = min(100, round(($cavg / 5) * 100));
                    ?>
                    <div class="csat-cat-ring" style="--csat-pct: <?php echo (float) $cpct; ?>;">
                        <div class="csat-cat-ring__inner">
                            <strong><?php echo esc_html(number_format_i18n($cavg, 1)); ?></strong>
                        </div>
                        <span><?php echo esc_html($cat['label']); ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($quotes)) : ?>
            <div class="csat-quotes">
                <?php if ($full) : ?>
                    <h2 class="csat-quotes__title">نظرات منتشرشده</h2>
                <?php endif; ?>
                <div class="csat-quotes__grid">
                    <?php foreach ($quotes as $quote) : ?>
                        <blockquote class="csat-quote">
                            <div class="csat-quote__stars" aria-label="<?php echo esc_attr(($quote['score'] ?? 0) . ' از ۵'); ?>">
                                <?php
                                $sc = (int) ($quote['score'] ?? 0);
                                for ($i = 1; $i <= 5; $i++) {
                                    echo $i <= $sc ? '<i class="fas fa-star" aria-hidden="true"></i>' : '<i class="far fa-star" aria-hidden="true"></i>';
                                }
                                ?>
                            </div>
                            <?php if (!empty($quote['comment'])) : ?>
                                <p><?php echo esc_html($quote['comment']); ?></p>
                            <?php endif; ?>
                            <footer>
                                <?php if (!empty($quote['client'])) : ?>
                                    <strong><?php echo esc_html($quote['client']); ?></strong>
                                <?php endif; ?>
                                <?php if (!empty($quote['project'])) : ?>
                                    <span><?php echo esc_html($quote['project']); ?></span>
                                <?php endif; ?>
                            </footer>
                        </blockquote>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>
