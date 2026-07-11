<?php
/**
 * Live demo gallery + device viewer layout.
 */

$settings = weblazem_get_live_demo_settings();
$query    = weblazem_query_live_demo_portfolios();
$has_posts = $query->have_posts();

$cat_map = array();
if ($has_posts) {
    while ($query->have_posts()) {
        $query->the_post();
        $terms = get_the_terms(get_the_ID(), 'portfolio_category');
        if ($terms && !is_wp_error($terms)) {
            foreach ($terms as $term) {
                $cat_map[$term->slug] = $term->name;
            }
        }
    }
    $query->rewind_posts();
}
?>

<section class="ld-page" dir="rtl" id="weblazem-live-demo" data-ld-root>
    <div class="ld-page__bg" aria-hidden="true"></div>
    <div class="container">
        <header class="ld-page__header">
            <?php if (!empty($settings['title'])) : ?>
                <h1 class="ld-page__title"><?php echo esc_html($settings['title']); ?></h1>
            <?php endif; ?>
            <?php if (!empty($settings['subtitle'])) : ?>
                <p class="ld-page__subtitle"><?php echo esc_html($settings['subtitle']); ?></p>
            <?php endif; ?>
        </header>

        <?php if (!$has_posts) : ?>
            <div class="ld-empty">
                <p><?php echo esc_html($settings['empty_text']); ?></p>
            </div>
        <?php else : ?>
            <?php if (!empty($cat_map)) : ?>
                <div class="ld-filters" role="tablist" aria-label="فیلتر دسته‌بندی">
                    <button type="button" class="ld-filters__chip is-active" data-ld-filter="all">
                        <?php echo esc_html($settings['filter_all_label']); ?>
                    </button>
                    <?php foreach ($cat_map as $slug => $name) : ?>
                        <button type="button" class="ld-filters__chip" data-ld-filter="<?php echo esc_attr($slug); ?>">
                            <?php echo esc_html($name); ?>
                        </button>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <div class="ld-layout">
                <aside class="ld-list" aria-label="لیست دموها">
                    <?php
                    while ($query->have_posts()) :
                        $query->the_post();
                        $id    = get_the_ID();
                        $demo  = weblazem_get_portfolio_live_demo_data($id);
                        $thumb = has_post_thumbnail($id) ? get_the_post_thumbnail_url($id, 'medium') : '';
                        $terms = get_the_terms($id, 'portfolio_category');
                        $slugs = array();
                        if ($terms && !is_wp_error($terms)) {
                            foreach ($terms as $term) {
                                $slugs[] = $term->slug;
                            }
                        }
                        ?>
                        <button
                            type="button"
                            class="ld-list__item"
                            data-ld-item
                            data-ld-id="<?php echo esc_attr((string) $id); ?>"
                            data-ld-cats="<?php echo esc_attr(implode(',', $slugs)); ?>"
                        >
                            <span class="ld-list__thumb">
                                <?php if ($thumb) : ?>
                                    <img src="<?php echo esc_url($thumb); ?>" alt="" loading="lazy" />
                                <?php else : ?>
                                    <span class="ld-list__thumb-ph" aria-hidden="true"></span>
                                <?php endif; ?>
                            </span>
                            <span class="ld-list__meta">
                                <span class="ld-list__title"><?php echo esc_html(get_the_title()); ?></span>
                                <?php if ($demo['note'] !== '') : ?>
                                    <span class="ld-list__note"><?php echo esc_html($demo['note']); ?></span>
                                <?php endif; ?>
                            </span>
                        </button>
                    <?php endwhile; ?>
                    <?php wp_reset_postdata(); ?>
                </aside>

                <div class="ld-viewer" data-ld-viewer>
                    <div class="ld-viewer__toolbar">
                        <div class="ld-viewer__modes" role="group" aria-label="نوع نمایش">
                            <button type="button" class="ld-viewer__mode is-active" data-ld-mode="live" hidden>سایت زنده</button>
                            <button type="button" class="ld-viewer__mode" data-ld-mode="video" hidden>ویدیو</button>
                        </div>
                        <div class="ld-viewer__devices" role="group" aria-label="اندازه دستگاه">
                            <button type="button" class="ld-viewer__device is-active" data-ld-device="desktop" title="دسکتاپ">
                                <span class="ld-ico ld-ico--desktop" aria-hidden="true"></span>
                                <span>دسکتاپ</span>
                            </button>
                            <button type="button" class="ld-viewer__device" data-ld-device="tablet" title="تبلت">
                                <span class="ld-ico ld-ico--tablet" aria-hidden="true"></span>
                                <span>تبلت</span>
                            </button>
                            <button type="button" class="ld-viewer__device" data-ld-device="mobile" title="موبایل">
                                <span class="ld-ico ld-ico--mobile" aria-hidden="true"></span>
                                <span>موبایل</span>
                            </button>
                        </div>
                        <a class="ld-viewer__open" data-ld-open href="#" target="_blank" rel="noopener noreferrer" hidden>
                            <?php echo esc_html($settings['open_site_text']); ?>
                        </a>
                    </div>

                    <p class="ld-viewer__placeholder" data-ld-placeholder>یک پروژه را از لیست انتخاب کنید</p>

                    <div class="ld-frame-wrap" data-ld-frame-wrap hidden>
                        <div class="ld-frame ld-frame--desktop is-loading" data-ld-frame>
                            <div class="ld-frame__chrome" aria-hidden="true">
                                <span></span><span></span><span></span>
                            </div>
                            <div class="ld-frame__screen">
                                <div class="ld-frame__loader" data-ld-loader>
                                    <span class="ld-spinner" aria-hidden="true"></span>
                                    <span>در حال بارگذاری…</span>
                                </div>
                                <iframe data-ld-iframe title="پیش‌نمایش زنده" loading="lazy" referrerpolicy="no-referrer" sandbox="allow-scripts allow-same-origin allow-forms allow-popups"></iframe>
                                <div class="ld-video-slot" data-ld-video-slot hidden></div>
                            </div>
                        </div>
                    </div>

                    <p class="ld-viewer__note" data-ld-note hidden><?php echo esc_html($settings['iframe_note']); ?></p>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>
