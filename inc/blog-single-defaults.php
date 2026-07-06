<?php
/**
 * Blog single post — defaults.
 */

function weblazem_blog_single_defaults() {
    return array(
        'banner_line1'              => 'به‌روزترین اخبار از طراحی سایت، سئو و دیجیتال مارکتینگ',
        'banner_line2'              => 'را در بلاگ وب‌لازم جستجو کنید',
        'banner_image'              => '',
        'sidebar_categories_title'  => 'موضوعات',
        'sidebar_categories_enabled'=> '1',
        'sidebar_categories_count'  => '0',
        'sidebar_latest_title'      => 'آخرین مقالات',
        'sidebar_latest_enabled'    => '1',
        'sidebar_latest_count'      => '6',
        'related_count'             => '4',
        'comments_title'            => 'ثبت دیدگاه',
        'comments_image'            => '',
        'comments_submit_text'      => 'ثبت دیدگاه',
    );
}

function weblazem_ensure_blog_single_defaults() {
    foreach (weblazem_blog_single_defaults() as $key => $value) {
        $option_key = 'weblazem_blog_single_' . $key;
        if (get_option($option_key) === false) {
            update_option($option_key, $value);
        }
    }
}
add_action('init', 'weblazem_ensure_blog_single_defaults', 14);
