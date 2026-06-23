<?php
/**
 * Portfolio categories taxonomy.
 */

function weblazem_register_portfolio_taxonomy() {
    $labels = array(
        'name'              => 'دسته‌بندی نمونه کار',
        'singular_name'     => 'دسته نمونه کار',
        'search_items'      => 'جستجوی دسته',
        'all_items'         => 'همه دسته‌ها',
        'parent_item'       => 'دسته والد',
        'parent_item_colon' => 'دسته والد:',
        'edit_item'         => 'ویرایش دسته',
        'update_item'       => 'به‌روزرسانی دسته',
        'add_new_item'      => 'افزودن دسته جدید',
        'new_item_name'     => 'نام دسته جدید',
        'menu_name'         => 'دسته‌بندی‌ها',
    );

    register_taxonomy(
        'portfolio_category',
        'portfolio',
        array(
            'labels'            => $labels,
            'hierarchical'      => true,
            'show_ui'           => true,
            'show_admin_column' => true,
            'show_in_rest'      => true,
            'query_var'         => true,
            'rewrite'           => array(
                'slug'         => 'daste-namune-kar',
                'with_front'   => false,
            ),
        )
    );
}
add_action('init', 'weblazem_register_portfolio_taxonomy', 11);

function weblazem_get_default_portfolio_categories() {
    return array(
        'foroushgahee-interneti' => 'فروشگاه اینترنتی',
        'site-sherkati'          => 'سایت شرکتی',
    );
}

function weblazem_ensure_portfolio_categories() {
    if (!taxonomy_exists('portfolio_category')) {
        return;
    }

    foreach (weblazem_get_default_portfolio_categories() as $slug => $name) {
        if (!term_exists($slug, 'portfolio_category')) {
            wp_insert_term($name, 'portfolio_category', array('slug' => $slug));
        }
    }
}
add_action('init', 'weblazem_ensure_portfolio_categories', 16);

function weblazem_assign_demo_portfolio_categories() {
    if (!taxonomy_exists('portfolio_category')) {
        return;
    }

    $shop_slugs    = array('demo-tarahi-site-foroushgahee-1', 'demo-tarahi-site-foroushgahee-2', 'demo-tarahi-site-foroushgahee-3', 'demo-tarahi-site-foroushgahee-4');
    $company_slugs = array('demo-tarahi-site-foroushgahee-5', 'demo-tarahi-site-foroushgahee-6', 'demo-tarahi-site-foroushgahee-7', 'demo-tarahi-site-foroushgahee-8');

    foreach ($shop_slugs as $slug) {
        $post = get_page_by_path($slug, OBJECT, 'portfolio');
        if ($post) {
            wp_set_object_terms((int) $post->ID, 'foroushgahee-interneti', 'portfolio_category', false);
        }
    }

    foreach ($company_slugs as $slug) {
        $post = get_page_by_path($slug, OBJECT, 'portfolio');
        if ($post) {
            wp_set_object_terms((int) $post->ID, 'site-sherkati', 'portfolio_category', false);
        }
    }
}
add_action('init', 'weblazem_assign_demo_portfolio_categories', 26);

function weblazem_get_portfolio_category_choices() {
    $choices = array('' => '— همه پروژه‌ها (بدون فیلتر) —');
    $terms   = get_terms(array(
        'taxonomy'   => 'portfolio_category',
        'hide_empty' => false,
    ));

    if (!is_wp_error($terms) && !empty($terms)) {
        foreach ($terms as $term) {
            $choices[$term->slug] = $term->name;
        }
    }

    return $choices;
}
