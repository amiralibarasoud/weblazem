<?php
/**
 * Plan comparator interactive layout.
 */

$s        = weblazem_get_plan_comparator_settings();
$cats     = weblazem_plan_comparator_category_labels();
$plans    = $s['plans'];
$prices   = array_map(
    function ($p) {
        return (int) $p['price'];
    },
    $plans
);
$max_price = !empty($prices) ? max($prices) : 50000000;
$min_price = !empty($prices) ? min($prices) : 0;
?>

<section class="pc-page" dir="rtl" id="weblazem-plan-comparator" data-pc-root>
    <div class="pc-page__bg" aria-hidden="true"></div>
    <div class="container">
        <header class="pc-page__header">
            <p class="pc-page__brand">وب‌لازم</p>
            <?php if (!empty($s['title'])) : ?>
                <h1 class="pc-page__title"><?php echo esc_html($s['title']); ?></h1>
            <?php endif; ?>
            <?php if (!empty($s['subtitle'])) : ?>
                <p class="pc-page__subtitle"><?php echo esc_html($s['subtitle']); ?></p>
            <?php endif; ?>
        </header>

        <div class="pc-filters" data-pc-filters>
            <div class="pc-filters__cats" role="group" aria-label="دسته پلن">
                <button type="button" class="pc-chip is-active" data-pc-cat="all">
                    <?php echo esc_html($s['all_categories']); ?>
                </button>
                <?php foreach ($cats as $slug => $label) : ?>
                    <button type="button" class="pc-chip" data-pc-cat="<?php echo esc_attr($slug); ?>">
                        <?php echo esc_html($label); ?>
                    </button>
                <?php endforeach; ?>
            </div>

            <div class="pc-filters__row">
                <label class="pc-budget">
                    <span><?php echo esc_html($s['budget_label']); ?></span>
                    <input
                        type="range"
                        data-pc-budget
                        min="<?php echo esc_attr((string) $min_price); ?>"
                        max="<?php echo esc_attr((string) $max_price); ?>"
                        step="1000000"
                        value="<?php echo esc_attr((string) $max_price); ?>"
                    />
                    <strong data-pc-budget-label><?php echo esc_html(weblazem_growth_format_toman($max_price)); ?></strong>
                </label>

                <label class="pc-toggle">
                    <input type="checkbox" data-pc-support />
                    <span><?php echo esc_html($s['support_label']); ?></span>
                </label>

                <label class="pc-toggle">
                    <input type="checkbox" data-pc-seo />
                    <span><?php echo esc_html($s['seo_label']); ?></span>
                </label>
            </div>
        </div>

        <div class="pc-cards" data-pc-cards>
            <?php foreach ($plans as $plan) :
                $features = isset($plan['features']) && is_array($plan['features']) ? $plan['features'] : array();
                $cat_label = isset($cats[$plan['category']]) ? $cats[$plan['category']] : $plan['category'];
                ?>
                <article
                    class="pc-card<?php echo $plan['recommended'] === '1' ? ' is-recommended' : ''; ?>"
                    data-pc-card
                    data-pc-id="<?php echo esc_attr($plan['id']); ?>"
                    data-pc-category="<?php echo esc_attr($plan['category']); ?>"
                    data-pc-price="<?php echo esc_attr((string) (int) $plan['price']); ?>"
                    data-pc-support="<?php echo esc_attr($plan['has_support']); ?>"
                    data-pc-seo="<?php echo esc_attr($plan['has_seo']); ?>"
                >
                    <?php if ($plan['badge'] !== '') : ?>
                        <span class="pc-card__badge"><?php echo esc_html($plan['badge']); ?></span>
                    <?php endif; ?>
                    <h2 class="pc-card__title"><?php echo esc_html($plan['title']); ?></h2>
                    <p class="pc-card__cat"><?php echo esc_html($cat_label); ?></p>
                    <p class="pc-card__price"><?php echo esc_html(weblazem_growth_format_toman((int) $plan['price'])); ?></p>
                    <ul class="pc-card__features">
                        <?php foreach ($features as $feature) : ?>
                            <li><?php echo esc_html($feature); ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <ul class="pc-card__meta">
                        <li>پشتیبانی: <?php echo $plan['has_support'] === '1' ? 'دارد' : 'ندارد'; ?></li>
                        <li>سئو: <?php echo $plan['has_seo'] === '1' ? 'دارد' : 'ندارد'; ?></li>
                        <li>تا <?php echo esc_html((string) (int) $plan['max_pages']); ?> صفحه</li>
                    </ul>
                    <?php if (!empty($plan['cta_url'])) : ?>
                        <a class="pc-card__cta" href="<?php echo esc_url($plan['cta_url']); ?>">
                            <?php echo esc_html($plan['cta_text'] !== '' ? $plan['cta_text'] : 'شروع پروژه'); ?>
                        </a>
                    <?php endif; ?>
                </article>
            <?php endforeach; ?>
        </div>

        <p class="pc-empty" data-pc-empty hidden><?php echo esc_html($s['empty_text']); ?></p>

        <div class="pc-table-wrap">
            <h2 class="pc-table__title"><?php echo esc_html($s['compare_title']); ?></h2>
            <div class="pc-table-scroll">
                <table class="pc-table" data-pc-table>
                    <thead>
                        <tr>
                            <th>ویژگی</th>
                            <?php foreach ($plans as $plan) : ?>
                                <th data-pc-col="<?php echo esc_attr($plan['id']); ?>"><?php echo esc_html($plan['title']); ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th>قیمت</th>
                            <?php foreach ($plans as $plan) : ?>
                                <td data-pc-col="<?php echo esc_attr($plan['id']); ?>"><?php echo esc_html(weblazem_growth_format_toman((int) $plan['price'])); ?></td>
                            <?php endforeach; ?>
                        </tr>
                        <tr>
                            <th>دسته</th>
                            <?php foreach ($plans as $plan) : ?>
                                <td data-pc-col="<?php echo esc_attr($plan['id']); ?>"><?php echo esc_html(isset($cats[$plan['category']]) ? $cats[$plan['category']] : $plan['category']); ?></td>
                            <?php endforeach; ?>
                        </tr>
                        <tr>
                            <th>پشتیبانی</th>
                            <?php foreach ($plans as $plan) : ?>
                                <td data-pc-col="<?php echo esc_attr($plan['id']); ?>"><?php echo $plan['has_support'] === '1' ? 'دارد' : 'ندارد'; ?></td>
                            <?php endforeach; ?>
                        </tr>
                        <tr>
                            <th>سئو</th>
                            <?php foreach ($plans as $plan) : ?>
                                <td data-pc-col="<?php echo esc_attr($plan['id']); ?>"><?php echo $plan['has_seo'] === '1' ? 'دارد' : 'ندارد'; ?></td>
                            <?php endforeach; ?>
                        </tr>
                        <tr>
                            <th>حداکثر صفحات</th>
                            <?php foreach ($plans as $plan) : ?>
                                <td data-pc-col="<?php echo esc_attr($plan['id']); ?>"><?php echo esc_html((string) (int) $plan['max_pages']); ?></td>
                            <?php endforeach; ?>
                        </tr>
                        <tr>
                            <th>اقدام</th>
                            <?php foreach ($plans as $plan) : ?>
                                <td data-pc-col="<?php echo esc_attr($plan['id']); ?>">
                                    <?php if (!empty($plan['cta_url'])) : ?>
                                        <a class="pc-table__cta" href="<?php echo esc_url($plan['cta_url']); ?>">
                                            <?php echo esc_html($plan['cta_text'] !== '' ? $plan['cta_text'] : 'شروع'); ?>
                                        </a>
                                    <?php endif; ?>
                                </td>
                            <?php endforeach; ?>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
