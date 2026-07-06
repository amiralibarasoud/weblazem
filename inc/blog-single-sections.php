<?php
/**
 * Blog single post — section registry and helpers.
 */

function weblazem_get_blog_single_sections_config() {
    return array(
        'banner'     => 'بنر معرفی بالای صفحه',
        'hero_image' => 'تصویر شاخص بزرگ',
        'sidebar'    => 'سایدبار (موضوعات و آخرین مقالات)',
        'related'    => 'مقالات مرتبط',
        'comments'   => 'بخش ثبت دیدگاه',
    );
}

function weblazem_is_blog_single_section_enabled($section) {
    $sections = weblazem_get_blog_single_sections_config();
    if (!isset($sections[$section])) {
        return true;
    }
    return get_option('weblazem_blog_single_section_' . $section . '_enabled', '1') === '1';
}

function weblazem_blog_single_option($key, $default = '') {
    return get_option('weblazem_blog_single_' . $key, $default);
}

function weblazem_ensure_blog_single_section_defaults() {
    foreach (weblazem_get_blog_single_sections_config() as $key => $label) {
        $option_key = 'weblazem_blog_single_section_' . $key . '_enabled';
        if (get_option($option_key) === false) {
            update_option($option_key, '1');
        }
    }
}
add_action('init', 'weblazem_ensure_blog_single_section_defaults', 13);

function weblazem_get_blog_single_sidebar_categories($show_count = true) {
    return get_categories(array(
        'orderby'    => 'name',
        'order'      => 'ASC',
        'hide_empty' => false,
    ));
}

function weblazem_get_blog_single_latest_posts($count = 6, $exclude_id = 0) {
    return new WP_Query(array(
        'post_type'      => 'post',
        'post_status'    => 'publish',
        'posts_per_page' => max(1, min(12, (int) $count)),
        'post__not_in'   => $exclude_id ? array((int) $exclude_id) : array(),
        'orderby'        => 'modified',
        'order'          => 'DESC',
    ));
}

function weblazem_get_blog_single_related_posts($post_id, $count = 4) {
    $categories = wp_get_post_categories($post_id);
    if (empty($categories)) {
        return new WP_Query(array(
            'post_type'      => 'post',
            'post_status'    => 'publish',
            'posts_per_page' => max(1, (int) $count),
            'post__not_in'   => array((int) $post_id),
            'orderby'        => 'date',
            'order'          => 'DESC',
        ));
    }

    return new WP_Query(array(
        'post_type'      => 'post',
        'post_status'    => 'publish',
        'posts_per_page' => max(1, (int) $count),
        'post__not_in'   => array((int) $post_id),
        'category__in'   => $categories,
        'orderby'        => 'date',
        'order'          => 'DESC',
    ));
}

function weblazem_get_blog_single_post_thumb($post_id) {
    $thumb = get_the_post_thumbnail_url($post_id, 'medium');
    if (!$thumb) {
        $thumb = get_post_meta($post_id, '_weblazem_demo_thumb', true);
    }
    return $thumb ?: '';
}

function weblazem_ensure_blog_sidebar_categories() {
    if (get_option('weblazem_blog_sidebar_categories_seeded') === '1') {
        return;
    }

    $categories = array(
        array('name' => 'طراحی سایت فروشگاهی', 'slug' => 'tarahi-site-forooshgahi'),
        array('name' => 'آخرین اخبار سئو', 'slug' => 'akharin-akhbar-seo'),
        array('name' => 'سایت شرکتی', 'slug' => 'site-sherkati'),
        array('name' => 'مقالات', 'slug' => 'maghalat'),
        array('name' => 'طراحی سایت خدماتی', 'slug' => 'tarahi-site-khedmati'),
        array('name' => 'طراحی سایت', 'slug' => 'tarahi-site'),
    );

    $term_ids = array();
    foreach ($categories as $cat) {
        $existing = get_term_by('slug', $cat['slug'], 'category');
        if ($existing) {
            $term_ids[] = (int) $existing->term_id;
            continue;
        }
        $created = wp_insert_term($cat['name'], 'category', array('slug' => $cat['slug']));
        if (!is_wp_error($created)) {
            $term_ids[] = (int) $created['term_id'];
        }
    }

    $posts = get_posts(array(
        'post_type'      => 'post',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'fields'         => 'ids',
    ));

    if ($posts && $term_ids) {
        foreach ($posts as $index => $post_id) {
            wp_set_post_categories((int) $post_id, array($term_ids[$index % count($term_ids)]));
        }
    }

    update_option('weblazem_blog_sidebar_categories_seeded', '1');
}
add_action('init', 'weblazem_ensure_blog_sidebar_categories', 42);
