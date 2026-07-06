<?php

function weblazem_theme_setup() {
    // پشتیبانی از عنوان صفحه (title tag)
    add_theme_support('title-tag');

    // پشتیبانی از تصویر شاخص
    add_theme_support('post-thumbnails');

    // پشتیبانی از HTML5
    add_theme_support('html5', ['search-form', 'comment-form', 'comment-list', 'gallery', 'caption']);

    // ثبت منوها
    register_nav_menus([
        'main_menu' => 'منوی اصلی',
    ]);
}
add_action('after_setup_theme', 'weblazem_theme_setup');

// Include theme options
require get_template_directory() . '/inc/theme-options.php';
require get_template_directory() . '/inc/post-types.php';
require get_template_directory() . '/inc/portfolio-taxonomy.php';
require get_template_directory() . '/inc/portfolio-meta.php';
require get_template_directory() . '/inc/portfolio-theme-options.php';
require get_template_directory() . '/inc/portfolio-demo-data.php';
require get_template_directory() . '/inc/about-team-options.php';
require get_template_directory() . '/inc/customers-options.php';
require get_template_directory() . '/inc/testimonials-options.php';
require get_template_directory() . '/inc/faq-options.php';
require_once get_template_directory() . '/inc/footer-options.php';
require_once get_template_directory() . '/inc/internal-pages-options.php';
require_once get_template_directory() . '/inc/service-page-helpers.php';
require_once get_template_directory() . '/inc/portfolio-page-options.php';
require_once get_template_directory() . '/inc/portfolio-page-setup.php';
require_once get_template_directory() . '/inc/webdesign-sections.php';
require_once get_template_directory() . '/inc/webdesign-page-setup.php';
require_once get_template_directory() . '/inc/webdesign-page-options.php';
require_once get_template_directory() . '/inc/webdesign-menu.php';
require_once get_template_directory() . '/inc/seo-sections.php';
require_once get_template_directory() . '/inc/seo-page-setup.php';
require_once get_template_directory() . '/inc/seo-page-options.php';
require_once get_template_directory() . '/inc/seo-menu.php';
require_once get_template_directory() . '/inc/devproject-sections.php';
require_once get_template_directory() . '/inc/devproject-page-setup.php';
require_once get_template_directory() . '/inc/devproject-page-options.php';
require_once get_template_directory() . '/inc/devproject-menu.php';
require_once get_template_directory() . '/inc/contentsupport-sections.php';
require_once get_template_directory() . '/inc/contentsupport-defaults.php';
require_once get_template_directory() . '/inc/contentsupport-page-setup.php';
require_once get_template_directory() . '/inc/contentsupport-page-options.php';
require_once get_template_directory() . '/inc/contentsupport-menu.php';
require_once get_template_directory() . '/inc/blog-archive-sections.php';
require_once get_template_directory() . '/inc/blog-archive-defaults.php';
require_once get_template_directory() . '/inc/blog-archive-page-setup.php';
require_once get_template_directory() . '/inc/blog-archive-page-options.php';
require_once get_template_directory() . '/inc/blog-archive-menu.php';
require_once get_template_directory() . '/inc/blog-single-sections.php';
require_once get_template_directory() . '/inc/blog-single-defaults.php';
require_once get_template_directory() . '/inc/blog-single-options.php';
require_once get_template_directory() . '/inc/blog-sample-tarahi-post.php';
require_once get_template_directory() . '/inc/portfolio-menu.php';
require_once get_template_directory() . '/inc/portfolio-single-meta.php';
require_once get_template_directory() . '/inc/portfolio-single-options.php';
require_once get_template_directory() . '/inc/consultation-options.php';
require_once get_template_directory() . '/inc/consultation-requests.php';
require_once get_template_directory() . '/inc/consultation-handler.php';
require_once get_template_directory() . '/inc/nav-menu-icons.php';
require_once get_template_directory() . '/inc/home-sections.php';

function weblazem_enqueue_assets() {
    // فونت‌ها
    wp_enqueue_style(
        'weblazem-fonts',
        get_template_directory_uri() . '/assets/css/fonts.css',
        [],
        null
    );

    // استایل اصلی قالب (اگر لازم داشتی)
    wp_enqueue_style(
        'weblazem-style',
        get_stylesheet_uri(),
        [],
        null
    );

    // استایل صفحه اصلی و بخش نمونه کارها
    $is_portfolio_listing = is_page_template('portfolio-template.php')
        || (function_exists('weblazem_is_portfolio_listing_page') && weblazem_is_portfolio_listing_page())
        || is_post_type_archive('portfolio');

    $is_webdesign_page = is_page_template('webdesign-template.php')
        || (function_exists('weblazem_is_webdesign_page') && weblazem_is_webdesign_page());

    $is_seo_page = is_page_template('seo-template.php')
        || (function_exists('weblazem_is_seo_page') && weblazem_is_seo_page());

    $is_devproject_page = is_page_template('devproject-template.php')
        || (function_exists('weblazem_is_devproject_page') && weblazem_is_devproject_page());

    $is_contentsupport_page = is_page_template('contentsupport-template.php')
        || (function_exists('weblazem_is_contentsupport_page') && weblazem_is_contentsupport_page());

    $is_blogarchive_page = is_page_template('blog-archive-template.php')
        || (function_exists('weblazem_is_blogarchive_page') && weblazem_is_blogarchive_page());

    if (is_page_template('home-template.php') || $is_portfolio_listing || $is_webdesign_page || $is_seo_page || $is_devproject_page || $is_contentsupport_page || $is_blogarchive_page || is_singular('portfolio') || is_singular('post')) {
        wp_enqueue_style(
            'weblazem-home-style',
            get_template_directory_uri() . '/assets/css/home.css',
            [],
            null
        );

        if ($is_portfolio_listing) {
            wp_enqueue_style(
                'weblazem-portfolio-page-style',
                get_template_directory_uri() . '/assets/css/portfolio-page.css',
                array('weblazem-home-style'),
                null
            );
        }

        if (is_singular('portfolio')) {
            wp_enqueue_style(
                'weblazem-portfolio-single-style',
                get_template_directory_uri() . '/assets/css/portfolio-single.css',
                array('weblazem-home-style'),
                null
            );
        }

        if ($is_webdesign_page) {
            wp_enqueue_style(
                'weblazem-webdesign-page-style',
                get_template_directory_uri() . '/assets/css/webdesign-page.css',
                array('weblazem-home-style'),
                null
            );

            wp_enqueue_script(
                'weblazem-home-carousel',
                get_template_directory_uri() . '/assets/js/home-carousel.js',
                array(),
                null,
                true
            );

            wp_enqueue_script(
                'weblazem-home-faq',
                get_template_directory_uri() . '/assets/js/home-faq.js',
                array(),
                null,
                true
            );

            wp_enqueue_script(
                'weblazem-webdesign-page',
                get_template_directory_uri() . '/assets/js/webdesign-page.js',
                array(),
                null,
                true
            );
        }

        if ($is_seo_page) {
            wp_enqueue_style(
                'weblazem-webdesign-page-style',
                get_template_directory_uri() . '/assets/css/webdesign-page.css',
                array('weblazem-home-style'),
                null
            );

            wp_enqueue_style(
                'weblazem-seo-page-style',
                get_template_directory_uri() . '/assets/css/seo-page.css',
                array('weblazem-webdesign-page-style'),
                null
            );

            wp_enqueue_script(
                'weblazem-home-faq',
                get_template_directory_uri() . '/assets/js/home-faq.js',
                array(),
                null,
                true
            );
        }

        if ($is_devproject_page) {
            wp_enqueue_style(
                'weblazem-webdesign-page-style',
                get_template_directory_uri() . '/assets/css/webdesign-page.css',
                array('weblazem-home-style'),
                null
            );

            wp_enqueue_style(
                'weblazem-devproject-page-style',
                get_template_directory_uri() . '/assets/css/devproject-page.css',
                array('weblazem-webdesign-page-style'),
                null
            );

            wp_enqueue_script(
                'weblazem-home-carousel',
                get_template_directory_uri() . '/assets/js/home-carousel.js',
                array(),
                null,
                true
            );

            wp_enqueue_script(
                'weblazem-home-faq',
                get_template_directory_uri() . '/assets/js/home-faq.js',
                array(),
                null,
                true
            );

            wp_enqueue_script(
                'weblazem-webdesign-page',
                get_template_directory_uri() . '/assets/js/webdesign-page.js',
                array(),
                null,
                true
            );
        }

        if ($is_contentsupport_page) {
            wp_enqueue_style(
                'weblazem-webdesign-page-style',
                get_template_directory_uri() . '/assets/css/webdesign-page.css',
                array('weblazem-home-style'),
                null
            );

            wp_enqueue_style(
                'weblazem-contentsupport-page-style',
                get_template_directory_uri() . '/assets/css/contentsupport-page.css',
                array('weblazem-webdesign-page-style'),
                null
            );

            wp_enqueue_script(
                'weblazem-home-carousel',
                get_template_directory_uri() . '/assets/js/home-carousel.js',
                array(),
                null,
                true
            );

            wp_enqueue_script(
                'weblazem-home-faq',
                get_template_directory_uri() . '/assets/js/home-faq.js',
                array(),
                null,
                true
            );

            wp_enqueue_script(
                'weblazem-webdesign-page',
                get_template_directory_uri() . '/assets/js/webdesign-page.js',
                array(),
                null,
                true
            );
        }

        if ($is_blogarchive_page || is_singular('post')) {
            wp_enqueue_style(
                'weblazem-blog-archive-page-style',
                get_template_directory_uri() . '/assets/css/blog-archive-page.css',
                array('weblazem-home-style'),
                null
            );
        }

        if (is_singular('post')) {
            wp_enqueue_style(
                'weblazem-blog-single-page-style',
                get_template_directory_uri() . '/assets/css/blog-single-page.css',
                array('weblazem-blog-archive-page-style'),
                null
            );
        }
        
        wp_enqueue_style( 
            'weblazem-footer-style',
             get_template_directory_uri() . '/assets/css/footer.css', [],
              null );

        if (is_page_template('home-template.php')) {
            wp_enqueue_script(
                'weblazem-home-carousel',
                get_template_directory_uri() . '/assets/js/home-carousel.js',
                [],
                null,
                true
            );

            wp_enqueue_script(
                'weblazem-home-faq',
                get_template_directory_uri() . '/assets/js/home-faq.js',
                [],
                null,
                true
            );
        }

        if (is_singular('portfolio')) {
            wp_enqueue_script(
                'weblazem-home-carousel',
                get_template_directory_uri() . '/assets/js/home-carousel.js',
                [],
                null,
                true
            );
        }
    }

    wp_enqueue_style(
        'weblazem-header-style',
        get_template_directory_uri() . '/assets/css/header.css',
        array('weblazem-fonts'),
        '1.0.2'
    );

    wp_enqueue_style(
        'fontawesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css', [],
        null);

    wp_enqueue_script(
        'weblazem-main-js',
        get_template_directory_uri() . '/assets/js/main.js',
        [],
        null,
        true
    );
}
add_action('wp_enqueue_scripts', 'weblazem_enqueue_assets');


function weblazem_enqueue_styles() {
    wp_enqueue_style('tailwind-style', get_template_directory_uri() . '/dist/style.css', [], null);
}
add_action('wp_enqueue_scripts', 'weblazem_enqueue_styles');

// اضافه کردن کلاس به آیتم‌های منو
function add_additional_class_on_li($classes, $item, $args) {
    if(isset($args->add_li_class)) {
        $classes[] = $args->add_li_class;
    }
    return $classes;
}
add_filter('nav_menu_css_class', 'add_additional_class_on_li', 10, 3);

// اضافه کردن کلاس به لینک‌های منو
function add_link_class($atts, $item, $args) {
    if (isset($args->link_class)) {
        $atts['class'] = $args->link_class;
    }
    return $atts;
}
add_filter('nav_menu_link_attributes', 'add_link_class', 10, 3);

// اضافه کردن استایل داینامیک برای رنگ اصلی قالب
function weblazem_custom_theme_colors() {
    // دریافت رنگ اصلی از تنظیمات
    $primary_color = get_option('weblazem_primary_color', '#4f46e5');
    $primary_darker = weblazem_adjust_brightness($primary_color, -20); // رنگ تیره‌تر
    
    // تنظیم استایل‌های سفارشی بر اساس رنگ اصلی
    echo '<style>
        :root {
            --weblazem-primary: ' . esc_attr($primary_color) . ';
            --weblazem-primary-hover: ' . esc_attr($primary_darker) . ';
        }
        
        /* لوگو */
        .text-blue-600 {
            color: var(--weblazem-primary) !important;
        }
        .hover\:text-blue-700:hover {
            color: var(--weblazem-primary-hover) !important;
        }
        
        /* نوار منو */
        .after\:bg-blue-500::after {
            background-color: var(--weblazem-primary) !important;
        }
        
        /* شماره تلفن */
        .text-blue-800,
        .text-blue-600 {
            color: var(--weblazem-primary) !important;
        }
        .bg-blue-50 {
            background-color: rgba(' . weblazem_hex2rgb($primary_color) . ', 0.1) !important;
        }
        .hover\:bg-blue-100:hover {
            background-color: rgba(' . weblazem_hex2rgb($primary_color) . ', 0.2) !important;
        }
        
        /* فوتر */
        .bg-blue-600,
        .hover\:bg-blue-700:hover {
            background-color: var(--weblazem-primary) !important;
        }
        .text-blue-500 {
            color: var(--weblazem-primary) !important;
        }
    </style>';
}
add_action('wp_head', 'weblazem_custom_theme_colors');

// تابع کمکی برای تغییر روشنایی رنگ
function weblazem_adjust_brightness($hex, $steps) {
    // حذف # از ابتدای کد رنگ
    $hex = ltrim($hex, '#');
    
    // تبدیل به RGB
    $r = hexdec(substr($hex, 0, 2));
    $g = hexdec(substr($hex, 2, 2));
    $b = hexdec(substr($hex, 4, 2));
    
    // تغییر روشنایی
    $r = max(0, min(255, $r + $steps));
    $g = max(0, min(255, $g + $steps));
    $b = max(0, min(255, $b + $steps));
    
    // تبدیل به هگز
    return '#' . sprintf('%02x%02x%02x', $r, $g, $b);
}

// تابع کمکی برای تبدیل کد رنگ هگز به RGB
function weblazem_hex2rgb($hex) {
    // حذف # از ابتدای کد رنگ
    $hex = ltrim($hex, '#');
    
    // تبدیل به RGB
    $r = hexdec(substr($hex, 0, 2));
    $g = hexdec(substr($hex, 2, 2));
    $b = hexdec(substr($hex, 4, 2));
    
    return $r . ',' . $g . ',' . $b;
}

