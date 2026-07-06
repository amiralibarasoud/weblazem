<?php
/**
 * Blog archive page — defaults.
 */

function weblazem_blogarchive_defaults() {
    return array(
        'hero_calligraphy_image'  => '',
        'hero_calligraphy_text'   => '<span class="highlight">جهش</span>',
        'hero_en_subtitle'        => 'Your Guide in the Digital World Powered by WEBLAZEM',
        'hero_intro'              => 'در مجله وب‌لازم، تجربیات تیم ما از دنیای دیجیتال، سئو، طراحی وب و توسعه نرم‌افزار را با شما به اشتراک می‌گذاریم. هر مقاله، راهنمایی عملی برای رشد کسب‌وکار آنلاین شماست.',
        'hero_banner_text'        => 'جستجو در مقالات...',
        'hero_banner_enabled'     => '1',
        'hero_search_enabled'     => '1',
        'posts_per_page'          => '6',
        'posts_empty_message'     => 'به‌زودی مقالات جدید منتشر می‌شود.',
        'pagination_last_label'   => 'صفحه آخر',
    );
}

function weblazem_ensure_blogarchive_defaults() {
    foreach (weblazem_blogarchive_defaults() as $key => $value) {
        $option_key = 'weblazem_blogarchive_' . $key;
        if (get_option($option_key) === false) {
            update_option($option_key, $value);
        }
    }
}
add_action('init', 'weblazem_ensure_blogarchive_defaults', 14);
