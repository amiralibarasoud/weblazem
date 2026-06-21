<?php
/**
 * Custom post types for Weblazem theme.
 */

function weblazem_register_portfolio_post_type() {
    $labels = array(
        'name'                  => 'نمونه کارها',
        'singular_name'         => 'نمونه کار',
        'menu_name'             => 'نمونه کارها',
        'name_admin_bar'        => 'نمونه کار',
        'add_new'               => 'افزودن نمونه کار',
        'add_new_item'          => 'افزودن نمونه کار جدید',
        'new_item'              => 'نمونه کار جدید',
        'edit_item'             => 'ویرایش نمونه کار',
        'view_item'             => 'مشاهده نمونه کار',
        'all_items'             => 'همه نمونه کارها',
        'search_items'          => 'جستجوی نمونه کار',
        'not_found'             => 'نمونه کاری یافت نشد.',
        'not_found_in_trash'    => 'نمونه کاری در زباله‌دان یافت نشد.',
        'featured_image'        => 'تصویر شاخص پروژه',
        'set_featured_image'    => 'انتخاب تصویر شاخص',
        'remove_featured_image' => 'حذف تصویر شاخص',
        'use_featured_image'    => 'استفاده به عنوان تصویر شاخص',
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'show_in_rest'       => true,
        'query_var'          => true,
        'rewrite'            => array('slug' => 'namune-kar', 'with_front' => false),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => 21,
        'menu_icon'          => 'dashicons-portfolio',
        'supports'           => array('title', 'editor', 'thumbnail', 'excerpt'),
    );

    register_post_type('portfolio', $args);
}
add_action('init', 'weblazem_register_portfolio_post_type');

function weblazem_portfolio_flush_rewrites() {
    weblazem_register_portfolio_post_type();
    flush_rewrite_rules();
}
add_action('after_switch_theme', 'weblazem_portfolio_flush_rewrites');
