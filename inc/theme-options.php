<?php
/**
 * Theme Options Page
 */

// Create Theme Options Page
function weblazem_theme_options_page() {
    // اضافه کردن منوی اصلی تنظیمات قالب
    add_menu_page(
        'تنظیمات قالب وب لازم',      // Page title
        'تنظیمات قالب',               // Menu title
        'manage_options',              // Capability
        'weblazem-theme-options',      // Menu slug
        'weblazem_theme_options_display', // Callback function
        'dashicons-admin-customizer',  // Icon
        30                             // Position (بالاتر در منو)
    );
    
    // اضافه کردن زیرمنوها برای بخش‌های مختلف تنظیمات
    add_submenu_page(
        'weblazem-theme-options',     // Parent slug
        'تنظیمات عمومی',              // Page title
        'تنظیمات عمومی',              // Menu title
        'manage_options',             // Capability
        'weblazem-theme-options',     // Menu slug (همان منوی اصلی)
        'weblazem_theme_options_display' // Callback function
    );
    
    // زیرمنوی شبکه‌های اجتماعی
    add_submenu_page(
        'weblazem-theme-options',     // Parent slug
        'شبکه‌های اجتماعی',           // Page title
        'شبکه‌های اجتماعی',           // Menu title
        'manage_options',             // Capability
        'weblazem-social-options',    // Menu slug
        'weblazem_social_options_display' // Callback function
    );
    
    // زیرمنوی چیدمان صفحه اصلی
    add_submenu_page(
        'weblazem-theme-options',     // Parent slug
        'چیدمان صفحه اصلی',           // Page title
        'چیدمان صفحه اصلی',           // Menu title
        'manage_options',             // Capability
        'weblazem-homepage-options',  // Menu slug
        'weblazem_homepage_options_display' // Callback function
    );
    
    // می‌توانید در آینده زیرمنوهای بیشتری اضافه کنید
    // مثال:
    /*
    add_submenu_page(
        'weblazem-theme-options',
        'تنظیمات بخش تماس',
        'بخش تماس',
        'manage_options',
        'weblazem-contact-options',
        'weblazem_contact_options_display'
    );
    */
}
add_action('admin_menu', 'weblazem_theme_options_page');

function weblazem_sanitize_header_consult_checkbox($value) {
    return ($value === '1') ? '1' : '0';
}

// Register Settings
function weblazem_register_settings() {
    // ثبت تنظیمات در گروه options
    register_setting('weblazem_options_group', 'weblazem_phone_number');
    register_setting('weblazem_options_group', 'weblazem_header_consult_enabled', array('sanitize_callback' => 'weblazem_sanitize_header_consult_checkbox'));
    register_setting('weblazem_options_group', 'weblazem_header_consult_btn_text');
    register_setting('weblazem_options_group', 'weblazem_logo', 'weblazem_handle_logo_upload');
    register_setting('weblazem_options_group', 'weblazem_primary_color');
    register_setting('weblazem_options_group', 'weblazem_background_image', 'weblazem_handle_background_upload');
    
    // تنظیمات شبکه‌های اجتماعی
    register_setting('weblazem_social_options_group', 'weblazem_instagram');
    register_setting('weblazem_social_options_group', 'weblazem_telegram');
    register_setting('weblazem_social_options_group', 'weblazem_whatsapp');
    
    // تنظیمات بخش هیرو
    register_setting('weblazem_homepage_options_group', 'weblazem_hero_title');
    register_setting('weblazem_homepage_options_group', 'weblazem_hero_subtitle');
    register_setting('weblazem_homepage_options_group', 'weblazem_hero_text');
    register_setting('weblazem_homepage_options_group', 'weblazem_hero_button_text');
    register_setting('weblazem_homepage_options_group', 'weblazem_hero_button_url');
    register_setting('weblazem_homepage_options_group', 'weblazem_hero_image', 'weblazem_handle_hero_image_upload');

    // تنظیمات بخش خدمات
    register_setting('weblazem_homepage_options_group', 'weblazem_services_title');
    register_setting('weblazem_homepage_options_group', 'weblazem_services_subtitle');
    
    // تنظیمات کارت‌های خدمات (آرایه‌ای از کارت‌ها)
    register_setting('weblazem_homepage_options_group', 'weblazem_services_cards', 'weblazem_sanitize_services_cards');
    
    // تنظیمات بخش برون‌سپاری
    register_setting('weblazem_homepage_options_group', 'weblazem_outsourcing_title');
    register_setting('weblazem_homepage_options_group', 'weblazem_outsourcing_subtitle');
    register_setting('weblazem_homepage_options_group', 'weblazem_outsourcing_button_text');
    register_setting('weblazem_homepage_options_group', 'weblazem_outsourcing_button_url');
    register_setting('weblazem_homepage_options_group', 'weblazem_outsourcing_background', 'weblazem_handle_outsourcing_background_upload');

    // تنظیمات بخش نمونه کارها
    register_setting('weblazem_homepage_options_group', 'weblazem_portfolio_title');
    register_setting('weblazem_homepage_options_group', 'weblazem_portfolio_more_text');
    register_setting('weblazem_homepage_options_group', 'weblazem_portfolio_card_button_text');

    // تنظیمات بخش درباره ما
    register_setting('weblazem_homepage_options_group', 'weblazem_about_title');
    register_setting('weblazem_homepage_options_group', 'weblazem_about_text');
    register_setting('weblazem_homepage_options_group', 'weblazem_about_image');
    register_setting('weblazem_homepage_options_group', 'weblazem_about_button_text');
    register_setting('weblazem_homepage_options_group', 'weblazem_about_button_url');

    // تنظیمات بخش تیم
    register_setting('weblazem_homepage_options_group', 'weblazem_team_title');
    register_setting('weblazem_homepage_options_group', 'weblazem_team_members', 'weblazem_sanitize_team_members');

    // تنظیمات بخش مشتریان
    register_setting('weblazem_homepage_options_group', 'weblazem_customers_title');
    register_setting('weblazem_homepage_options_group', 'weblazem_customers_logos', 'weblazem_sanitize_customer_logos');

    // تنظیمات بخش نظرات مشتریان
    register_setting('weblazem_homepage_options_group', 'weblazem_testimonials_title');
    register_setting('weblazem_homepage_options_group', 'weblazem_testimonials_rating_label');
    register_setting('weblazem_homepage_options_group', 'weblazem_testimonials_rating_score');
    register_setting('weblazem_homepage_options_group', 'weblazem_testimonials_rating_value');
    register_setting('weblazem_homepage_options_group', 'weblazem_testimonials_items', 'weblazem_sanitize_testimonials_items');

    // تنظیمات بخش سوالات متداول
    register_setting('weblazem_homepage_options_group', 'weblazem_faq_title');
    register_setting('weblazem_homepage_options_group', 'weblazem_faq_subtitle');
    register_setting('weblazem_homepage_options_group', 'weblazem_faq_items', 'weblazem_sanitize_faq_items');
    
    // می‌توانید تنظیمات بیشتری در آینده اضافه کنید
    // register_setting('weblazem_options_group', 'weblazem_email');
    // register_setting('weblazem_options_group', 'weblazem_address');
}
add_action('admin_init', 'weblazem_register_settings');

// اضافه کردن اسکریپت‌های مورد نیاز برای انتخاب‌گر رنگ
function weblazem_theme_options_scripts($hook) {
    // فقط در صفحات تنظیمات قالب اسکریپت‌ها را لود کن
    if (strpos($hook, 'weblazem') !== false) {
        // اضافه کردن انتخاب‌گر رنگ
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('wp-color-picker');
    }
}
add_action('admin_enqueue_scripts', 'weblazem_theme_options_scripts');

// Handle Logo Upload
function weblazem_handle_logo_upload($option) {
    if(!empty($_FILES["weblazem_logo_file"]["tmp_name"])) {
        $urls = wp_handle_upload($_FILES["weblazem_logo_file"], array('test_form' => false));
        if($urls["error"]) {
            return $option;
        }
        return $urls["url"];
    }
    return $option;
}

// Handle Background Image Upload
function weblazem_handle_background_upload($option) {
    if(!empty($_FILES["weblazem_background_file"]["tmp_name"])) {
        $urls = wp_handle_upload($_FILES["weblazem_background_file"], array('test_form' => false));
        if($urls["error"]) {
            return $option;
        }
        return $urls["url"];
    }
    return $option;
}

// Handle Hero Image Upload
function weblazem_handle_hero_image_upload($option) {
    if(!empty($_FILES["weblazem_hero_image_file"]["tmp_name"])) {
        $urls = wp_handle_upload($_FILES["weblazem_hero_image_file"], array('test_form' => false));
        if(isset($urls["error"])) {
            // ثبت خطای آپلود
            error_log('خطا در آپلود تصویر هیرو: ' . $urls["error"]);
            add_settings_error(
                'weblazem_hero_image',
                'hero_image_error',
                'خطا در آپلود تصویر: ' . $urls["error"],
                'error'
            );
            return $option;
        }
        
        // آپلود موفق
        return $urls["url"];
    }
    return $option;
}

// Sanitize service cards data
function weblazem_sanitize_services_cards($input) {
    // اگر مقدار خالی بود، آرایه خالی برگردان
    if (empty($input)) {
        return array();
    }
    
    // اگر آرایه نبود، تبدیل به آرایه کن
    if (!is_array($input)) {
        return array();
    }
    
    $sanitized_cards = array();
    
    // هر کارت را بررسی و تمیز کن
    foreach ($input as $card) {
        // فیلدهای ضروری را بررسی کن
        if (!isset($card['title'])) {
            continue; // این کارت را رد کن
        }
        
        // مقدار پیش‌فرض برای URL اگر خالی بود
        $button_url = '';
        if (isset($card['button_url']) && !empty($card['button_url'])) {
            $button_url = esc_url_raw($card['button_url']);
        }
        
        $sanitized_card = array(
            'title' => sanitize_text_field($card['title']),
            'image' => isset($card['image']) ? esc_url_raw($card['image']) : '',
            'button_text' => isset($card['button_text']) ? sanitize_text_field($card['button_text']) : '',
            'button_url' => $button_url,
        );
        
        $sanitized_cards[] = $sanitized_card;
    }
    
    return $sanitized_cards;
}

// Handle Service Card Image Upload
function weblazem_handle_service_card_image_upload() {
    // فقط به کاربران با سطح دسترسی مناسب اجازه آپلود بده
    if (!current_user_can('manage_options')) {
        wp_send_json_error('شما اجازه این کار را ندارید.');
    }
    
    // بررسی نانس برای امنیت
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'weblazem_homepage_save')) {
        wp_send_json_error('خطای امنیتی رخ داده است.');
    }
    
    // بررسی فایل
    if (empty($_FILES['file']['tmp_name'])) {
        wp_send_json_error('فایلی برای آپلود یافت نشد.');
    }
    
    // آپلود فایل
    $urls = wp_handle_upload($_FILES['file'], array('test_form' => false));
    
    if (isset($urls['error'])) {
        wp_send_json_error('خطا در آپلود فایل: ' . $urls['error']);
    }
    
    // اطلاعات دیباگ
    $debug_info = array(
        'file_name' => $_FILES['file']['name'],
        'file_size' => size_format($_FILES['file']['size']),
        'file_type' => $_FILES['file']['type'],
        'uploaded_url' => $urls['url'],
        'card_index' => isset($_POST['card_index']) ? intval($_POST['card_index']) : 'not set'
    );
    
    // برگرداندن URL تصویر آپلود شده
    wp_send_json_success(array(
        'url' => $urls['url'],
        'debug' => $debug_info
    ));
}
add_action('wp_ajax_weblazem_upload_service_image', 'weblazem_handle_service_card_image_upload');

// تابع کمکی برای تغییر روشنایی رنگ اگر تابع در functions.php وجود نداشته باشد
if (!function_exists('weblazem_adjust_brightness')) {
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
}

// Admin Styles for Theme Options
function weblazem_admin_styles() {
    // فقط در صفحه تنظیمات قالب استایل‌ها را اضافه کن
    $screen = get_current_screen();
    if (strpos($screen->id, 'weblazem') !== false) {
        // دریافت رنگ اصلی از تنظیمات
        $primary_color = get_option('weblazem_primary_color', '#4f46e5');
        $primary_darker = weblazem_adjust_brightness($primary_color, -20); // رنگ تیره‌تر
        $secondary_color = weblazem_adjust_brightness($primary_color, 10); // رنگ روشن‌تر
        
        echo '<style>
            @font-face {
                font-family: "Dana";
                src: url("' . get_template_directory_uri() . '/assets/fonts/dana/dana-regular.woff2") format("woff2"),
                     url("' . get_template_directory_uri() . '/assets/fonts/dana/dana-regular.woff") format("woff");
                font-weight: normal;
                font-style: normal;
                font-display: swap;
            }
            
            @font-face {
                font-family: "Dana";
                src: url("' . get_template_directory_uri() . '/assets/fonts/dana/dana-medium.woff2") format("woff2"),
                     url("' . get_template_directory_uri() . '/assets/fonts/dana/dana-medium.woff") format("woff");
                font-weight: 500;
                font-style: normal;
                font-display: swap;
            }
            
            @font-face {
                font-family: "Dana";
                src: url("' . get_template_directory_uri() . '/assets/fonts/dana/dana-bold.woff2") format("woff2"),
                     url("' . get_template_directory_uri() . '/assets/fonts/dana/dana-bold.woff") format("woff");
                font-weight: 700;
                font-style: normal;
                font-display: swap;
            }
            
            :root {
                --weblazem-primary: ' . esc_attr($primary_color) . ';
                --weblazem-primary-hover: ' . esc_attr($primary_darker) . ';
                --weblazem-secondary: ' . esc_attr($secondary_color) . ';
                --weblazem-light: #f9fafb;
                --weblazem-dark: #111827;
                --weblazem-gray: #9ca3af;
                --weblazem-border: #e5e7eb;
                --weblazem-font: "Dana", "Tahoma", sans-serif;
            }
            
            #wpcontent {
                padding-right: 0;
            }
            
            .wrap {
                margin: 0;
                padding: 0;
                max-width: 100%;
                font-family: var(--weblazem-font);
            }
            
            .weblazem-admin-header {
                background: linear-gradient(135deg, var(--weblazem-primary) 0%, var(--weblazem-secondary) 100%);
                padding: 40px;
                color: white;
                margin-bottom: 30px;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
                position: relative;
                overflow: hidden;
                font-family: var(--weblazem-font);
            }
            
            .weblazem-admin-header:before {
                content: "";
                position: absolute;
                top: -50%;
                left: -50%;
                width: 200%;
                height: 200%;
                background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0) 80%);
                opacity: 0.8;
                animation: rotate 25s linear infinite;
            }
            
            @keyframes rotate {
                0% {
                    transform: rotate(0deg);
                }
                100% {
                    transform: rotate(360deg);
                }
            }
            
            .weblazem-admin-header-content {
                position: relative;
                z-index: 10;
            }
            
            .weblazem-admin-header h1 {
                color: white;
                margin: 0 0 10px;
                font-size: 28px;
                font-weight: 700;
                font-family: var(--weblazem-font);
                animation: fadeInDown 0.6s ease-out;
            }
            
            @keyframes fadeInDown {
                from {
                    opacity: 0;
                    transform: translateY(-20px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
            
            .weblazem-admin-header p {
                font-size: 16px;
                opacity: 0.9;
                margin: 0;
                font-family: var(--weblazem-font);
                animation: fadeInUp 0.6s ease-out;
            }
            
            @keyframes fadeInUp {
                from {
                    opacity: 0;
                    transform: translateY(20px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
            
            .weblazem-admin-content {
                background: #fff;
                padding: 30px;
                border-radius: 8px;
                box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
                max-width: 1000px;
                margin: 0 auto 40px;
                animation: fadeIn 0.8s ease-out;
                position: relative;
                font-family: var(--weblazem-font);
            }
            
            @keyframes fadeIn {
                from {
                    opacity: 0;
                }
                to {
                    opacity: 1;
                }
            }
            
            .form-table {
                margin-top: 20px;
                font-family: var(--weblazem-font);
            }
            
            .form-table th {
                padding: 25px 20px 25px 10px;
                font-weight: 500;
                font-size: 15px;
                color: var(--weblazem-dark);
                width: 200px;
                vertical-align: top;
                font-family: var(--weblazem-font);
            }
            
            .form-table td {
                padding: 20px 10px;
                vertical-align: top;
                font-family: var(--weblazem-font);
            }
            
            .form-table input[type="text"],
            .form-table input[type="url"] {
                border: 1px solid var(--weblazem-border);
                border-radius: 6px;
                padding: 8px 12px;
                width: 100%;
                max-width: 400px;
                box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
                transition: all 0.2s ease;
                font-family: var(--weblazem-font);
            }
            
            .form-table input[type="text"]:focus,
            .form-table input[type="url"]:focus {
                border-color: var(--weblazem-primary);
                box-shadow: 0 0 0 2px rgba(79, 70, 229, 0.2);
                outline: none;
                transform: translateY(-2px);
            }
            
            .form-table .description {
                margin-top: 8px;
                color: var(--weblazem-gray);
                font-size: 13px;
                transition: color 0.2s ease;
                font-family: var(--weblazem-font);
            }
            
            .form-table input:focus + .description {
                color: var(--weblazem-secondary);
            }
            
            .submit .button-primary {
                background: var(--weblazem-primary);
                border-color: var(--weblazem-primary);
                color: white;
                padding: 8px 24px;
                height: auto;
                font-size: 14px;
                border-radius: 6px;
                box-shadow: 0 2px 4px rgba(79, 70, 229, 0.2);
                transition: all 0.3s ease;
                font-family: var(--weblazem-font);
            }
            
            .submit .button-primary:hover {
                background: var(--weblazem-primary-hover);
                border-color: var(--weblazem-primary-hover);
                transform: translateY(-2px);
                box-shadow: 0 4px 8px rgba(79, 70, 229, 0.3);
            }
            
            .submit .button-primary:active {
                transform: translateY(0);
            }
            
            .current-logo,
            .current-background {
                background: var(--weblazem-light);
                border-radius: 6px;
                padding: 15px;
                display: inline-block;
                margin-bottom: 15px;
                border: 1px solid var(--weblazem-border);
                transition: all 0.3s ease;
            }
            
            .current-logo:hover,
            .current-background:hover {
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
                transform: translateY(-5px);
            }
            
            .current-logo img,
            .current-background img {
                max-width: 200px;
                height: auto;
                border-radius: 4px;
            }
            
            .current-background img {
                max-width: 300px;
            }
            
            .file-upload-wrapper {
                position: relative;
                margin-bottom: 20px;
            }
            
            input[type="file"] {
                background: var(--weblazem-light);
                padding: 15px;
                border-radius: 6px;
                border: 2px dashed var(--weblazem-border);
                width: 100%;
                max-width: 400px;
                cursor: pointer;
                transition: all 0.3s ease;
                font-family: var(--weblazem-font);
            }
            
            input[type="file"]:hover {
                border-color: var(--weblazem-secondary);
                background: rgba(99, 102, 241, 0.05);
            }
            
            .color-picker-wrapper {
                display: flex;
                align-items: center;
                gap: 10px;
            }
            
            .color-preview {
                width: 30px;
                height: 30px;
                border-radius: 4px;
                border: 1px solid var(--weblazem-border);
                display: inline-block;
                transition: all 0.3s ease;
            }
            
            /* تب های تنظیمات */
            .weblazem-tabs {
                margin-bottom: 30px;
                border-bottom: 1px solid var(--weblazem-border);
                display: flex;
                gap: 5px;
                font-family: var(--weblazem-font);
            }
            
            .weblazem-tab {
                padding: 12px 20px;
                background: none;
                border: none;
                cursor: pointer;
                font-size: 14px;
                color: var(--weblazem-gray);
                position: relative;
                transition: all 0.3s ease;
                font-family: var(--weblazem-font);
            }
            
            .weblazem-tab.active {
                color: var(--weblazem-primary);
                font-weight: 500;
            }
            
            .weblazem-tab.active:after {
                content: "";
                position: absolute;
                bottom: -1px;
                right: 0;
                left: 0;
                height: 2px;
                background: var(--weblazem-primary);
                animation: expandWidth 0.3s ease-out;
            }
            
            @keyframes expandWidth {
                from {
                    width: 0;
                    left: 50%;
                    right: 50%;
                }
                to {
                    width: 100%;
                    left: 0;
                    right: 0;
                }
            }
            
            .weblazem-tab:hover {
                color: var(--weblazem-dark);
            }
            
            .weblazem-tab-content {
                display: none;
                animation: fadeIn 0.5s ease-out;
                font-family: var(--weblazem-font);
            }
            
            .weblazem-tab-content.active {
                display: block;
            }
            
            /* Tooltip */
            .weblazem-tooltip {
                position: relative;
                display: inline-block;
                margin-right: 8px;
                cursor: help;
                font-family: var(--weblazem-font);
            }
            
            .weblazem-tooltip i {
                color: var(--weblazem-gray);
                font-size: 16px;
            }
            
            .weblazem-tooltip .tooltip-text {
                visibility: hidden;
                width: 200px;
                background-color: var(--weblazem-dark);
                color: #fff;
                text-align: center;
                border-radius: 6px;
                padding: 8px 12px;
                position: absolute;
                z-index: 1;
                bottom: 125%;
                left: 50%;
                transform: translateX(-50%);
                opacity: 0;
                transition: opacity 0.3s;
                font-size: 12px;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                font-family: var(--weblazem-font);
            }
            
            .weblazem-tooltip:hover .tooltip-text {
                visibility: visible;
                opacity: 1;
            }
            
            /* Save indicator */
            @keyframes savedAnimation {
                0% {
                    opacity: 0;
                    transform: translateY(10px);
                }
                50% {
                    opacity: 1;
                    transform: translateY(0);
                }
                90% {
                    opacity: 1;
                }
                100% {
                    opacity: 0;
                }
            }
            
            .saved-indicator {
                position: fixed;
                bottom: 20px;
                left: 20px;
                background: var(--weblazem-primary);
                color: white;
                padding: 10px 20px;
                border-radius: 4px;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                display: none;
                animation: savedAnimation 2s forwards;
                font-family: var(--weblazem-font);
            }
            
            .saved-indicator.show {
                display: block;
            }
            
            @keyframes float {
                0% {
                    transform: translateY(0px);
                }
                50% {
                    transform: translateY(-10px);
                }
                100% {
                    transform: translateY(0px);
                }
            }
            
            .weblazem-admin-card {
                background: white;
                border-radius: 8px;
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
                padding: 25px;
                margin-bottom: 30px;
                transition: all 0.3s ease;
                position: relative;
                overflow: hidden;
                font-family: var(--weblazem-font);
            }
            
            .weblazem-admin-card:hover {
                box-shadow: 0 8px 30px rgba(79, 70, 229, 0.15);
                transform: translateY(-5px);
            }
            
            .weblazem-admin-card h3 {
                font-size: 18px;
                margin-top: 0;
                margin-bottom: 20px;
                color: var(--weblazem-dark);
                position: relative;
                padding-right: 20px;
                font-family: var(--weblazem-font);
                font-weight: 700;
            }
            
            .weblazem-admin-card h3:before {
                content: "";
                position: absolute;
                right: 0;
                top: 50%;
                transform: translateY(-50%);
                width: 4px;
                height: 20px;
                background: var(--weblazem-primary);
                border-radius: 2px;
            }
            
            .weblazem-admin-card-icon {
                position: absolute;
                top: 20px;
                left: 20px;
                font-size: 24px;
                color: var(--weblazem-primary);
                opacity: 0.7;
            }
            
            .weblazem-feature-card {
                border-radius: 8px;
                padding: 20px;
                background: white;
                margin-bottom: 20px;
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
                transition: all 0.3s ease;
                border: 1px solid var(--weblazem-border);
                font-family: var(--weblazem-font);
            }
            
            .weblazem-feature-card:hover {
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
                transform: translateY(-5px);
            }
            
            .weblazem-feature-card-grid {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
                gap: 20px;
                margin-top: 30px;
            }
            
            .weblazem-feature-card-icon {
                width: 50px;
                height: 50px;
                background: linear-gradient(135deg, var(--weblazem-primary) 0%, var(--weblazem-secondary) 100%);
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                margin-bottom: 15px;
            }
            
            .weblazem-feature-card-icon i {
                color: white;
                font-size: 20px;
            }
            
            .weblazem-feature-card-title {
                font-size: 16px;
                font-weight: 500;
                margin-bottom: 10px;
                color: var(--weblazem-dark);
                font-family: var(--weblazem-font);
            }
            
            .weblazem-feature-card-desc {
                font-size: 14px;
                color: var(--weblazem-gray);
                line-height: 1.6;
                font-family: var(--weblazem-font);
            }
            
            /* بهبود استایل صفحه شبکه‌های اجتماعی */
            #weblazem-social-options p {
                font-family: var(--weblazem-font);
            }
            
            /* سایر استایل‌ها */
            
            .color-preview-section {
                margin-top: 25px;
                padding: 20px;
                background: var(--weblazem-light);
                border-radius: 8px;
                border: 1px solid var(--weblazem-border);
            }
            
            .color-preview-section h4 {
                margin-top: 0;
                margin-bottom: 15px;
                font-size: 14px;
                color: var(--weblazem-dark);
                font-weight: 500;
            }
            
            .color-preview-items {
                display: flex;
                flex-wrap: wrap;
                gap: 20px;
            }
            
            .preview-item {
                text-align: center;
                width: 100px;
            }
            
            .preview-logo {
                font-size: 18px;
                font-weight: bold;
                margin-bottom: 10px;
            }
            
            .preview-button {
                padding: 8px 12px;
                border-radius: 4px;
                color: white;
                font-size: 14px;
                margin-bottom: 10px;
                transition: all 0.3s ease;
            }
            
            .preview-button:hover {
                opacity: 0.9;
                transform: translateY(-2px);
            }
            
            .preview-link {
                font-size: 14px;
                text-decoration: underline;
                margin-bottom: 10px;
                transition: all 0.3s ease;
            }
            
            .preview-link:hover {
                opacity: 0.8;
            }
            
            .preview-icon {
                font-size: 22px;
                margin-bottom: 10px;
            }
            
            .preview-title {
                font-size: 12px;
                color: var(--weblazem-gray);
            }
            
            /* خاص انتخاب رنگ */
            .wp-picker-container {
                display: inline-block;
            }
            
            .color-picker-wrapper {
                display: flex;
                align-items: center;
                gap: 10px;
            }
            
            .color-preview {
                width: 30px;
                height: 30px;
                border-radius: 4px;
                border: 1px solid var(--weblazem-border);
                display: inline-block;
                transition: all 0.3s ease;
                cursor: pointer;
            }
        </style>';
    }
}
add_action('admin_head', 'weblazem_admin_styles');

// Display Theme Options Page
function weblazem_theme_options_display() {
    ?>
    <div class="wrap" dir="rtl">
        <div class="weblazem-admin-header">
            <div class="weblazem-admin-header-content">
                <h1>تنظیمات قالب وب لازم</h1>
                <p>در این بخش می‌توانید تنظیمات مختلف قالب را مدیریت کنید.</p>
            </div>
        </div>
        
        <div class="weblazem-admin-content">
            <div class="weblazem-tabs">
                <button type="button" class="weblazem-tab active" data-tab="general">اطلاعات عمومی</button>
                <button type="button" class="weblazem-tab" data-tab="appearance">ظاهر سایت</button>
                <button type="button" class="weblazem-tab" data-tab="features">ویژگی‌ها</button>
            </div>
            
            <form method="post" action="options.php" enctype="multipart/form-data" id="weblazem-options-form">
                <?php settings_fields('weblazem_options_group'); ?>
                <?php do_settings_sections('weblazem_options_group'); ?>
                
                <!-- بخش اطلاعات عمومی -->
                <div class="weblazem-tab-content active" id="general-tab">
                    <div class="weblazem-admin-card">
                        <div class="weblazem-admin-card-icon"><i class="fas fa-heading"></i></div>
                        <h3>تنظیمات هدر</h3>
                        <table class="form-table">
                            <tr valign="top">
                                <th scope="row">دکمه درخواست مشاوره</th>
                                <td>
                                    <input type="hidden" name="weblazem_header_consult_enabled" value="0" />
                                    <label>
                                        <input type="checkbox" name="weblazem_header_consult_enabled" value="1" <?php checked(get_option('weblazem_header_consult_enabled', '1'), '1'); ?> />
                                        نمایش دکمه «درخواست مشاوره» در هدر
                                    </label>
                                    <p class="description">با کلیک روی دکمه، مودال ثبت درخواست مشاوره (همانند دکمه شناور) باز می‌شود. در موبایل و تبلت، منو به‌صورت کشویی نمایش داده می‌شود و دکمه مشاوره در هدر به آیکون تبدیل می‌شود.</p>
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row">متن دکمه هدر</th>
                                <td>
                                    <?php
                                    $header_consult_btn_default = function_exists('weblazem_get_consult_option')
                                        ? weblazem_get_consult_option('weblazem_consult_btn_text', 'ثبت درخواست مشاوره')
                                        : 'ثبت درخواست مشاوره';
                                    ?>
                                    <input type="text" name="weblazem_header_consult_btn_text" class="regular-text" value="<?php echo esc_attr(get_option('weblazem_header_consult_btn_text', '')); ?>" placeholder="<?php echo esc_attr($header_consult_btn_default); ?>" />
                                    <p class="description">در صورت خالی بودن، از متن دکمه مشاوره در <a href="<?php echo esc_url(admin_url('admin.php?page=weblazem-consultation-options')); ?>">تنظیمات مودال مشاوره</a> استفاده می‌شود.</p>
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row">آیکون آیتم‌های منو</th>
                                <td>
                                    <p class="description">برای هر آیتم منو می‌توانید آیکون Font Awesome انتخاب کنید.</p>
                                    <p><a href="<?php echo esc_url(admin_url('nav-menus.php')); ?>" class="button button-secondary">مدیریت منو و آیکون‌ها</a></p>
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row">شماره تلفن</th>
                                <td>
                                    <input type="text" name="weblazem_phone_number" class="regular-text" value="<?php echo esc_attr(get_option('weblazem_phone_number')); ?>" />
                                    <p class="description">شماره تماس عمومی سایت (در صورت نیاز در بخش‌های دیگر استفاده می‌شود)</p>
                                </td>
                            </tr>
                        </table>
                    </div>
                    
                    <div class="weblazem-admin-card">
                        <div class="weblazem-admin-card-icon"><i class="fas fa-image"></i></div>
                        <h3>تصویر و لوگو</h3>
                        <table class="form-table">
                            <tr valign="top">
                                <th scope="row">لوگو</th>
                                <td>
                                    <?php $logo_url = get_option('weblazem_logo'); ?>
                                    <?php if($logo_url) : ?>
                                        <div class="current-logo">
                                            <img src="<?php echo esc_url($logo_url); ?>" alt="لوگو سایت">
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="file-upload-wrapper">
                                        <input type="file" name="weblazem_logo_file" />
                                        <input type="hidden" name="weblazem_logo" value="<?php echo esc_attr($logo_url); ?>" />
                                        <p class="description">لوگوی سایت را آپلود کنید</p>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                <!-- بخش ظاهر سایت -->
                <div class="weblazem-tab-content" id="appearance-tab">
                    <div class="weblazem-admin-card">
                        <div class="weblazem-admin-card-icon"><i class="fas fa-palette"></i></div>
                        <h3>رنگ‌بندی</h3>
                        <table class="form-table">
                            <tr valign="top">
                                <th scope="row">رنگ اصلی سایت</th>
                                <td>
                                    <div class="color-picker-wrapper">
                                        <?php $primary_color = get_option('weblazem_primary_color', '#4f46e5'); ?>
                                        <input type="text" name="weblazem_primary_color" class="regular-text color-picker" value="<?php echo esc_attr($primary_color); ?>" placeholder="#4f46e5" />
                                        <div class="color-preview" style="background-color: <?php echo esc_attr($primary_color); ?>"></div>
                                    </div>
                                    <p class="description">رنگ اصلی استفاده شده در سایت (مقدار هگزادسیمال)</p>
                                    
                                    <!-- پیش‌نمایش رنگ -->
                                    <div class="color-preview-section">
                                        <h4>پیش‌نمایش رنگ انتخاب شده</h4>
                                        <div class="color-preview-items">
                                            <div class="preview-item">
                                                <div class="preview-logo">
                                                    <span style="color: <?php echo esc_attr($primary_color); ?>">وب لازم</span>
                                                </div>
                                                <div class="preview-title">لوگو</div>
                                            </div>
                                            
                                            <div class="preview-item">
                                                <div class="preview-button" style="background-color: <?php echo esc_attr($primary_color); ?>">
                                                    دکمه
                                                </div>
                                                <div class="preview-title">دکمه</div>
                                            </div>
                                            
                                            <div class="preview-item">
                                                <div class="preview-link" style="color: <?php echo esc_attr($primary_color); ?>">
                                                    لینک صفحات
                                                </div>
                                                <div class="preview-title">لینک</div>
                                            </div>
                                            
                                            <div class="preview-item">
                                                <div class="preview-icon">
                                                    <i class="fas fa-phone" style="color: <?php echo esc_attr($primary_color); ?>"></i>
                                                </div>
                                                <div class="preview-title">آیکون</div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>
                    
                    <div class="weblazem-admin-card">
                        <div class="weblazem-admin-card-icon"><i class="fas fa-images"></i></div>
                        <h3>تصاویر سایت</h3>
                        <table class="form-table">
                            <tr valign="top">
                                <th scope="row">تصویر پس‌زمینه</th>
                                <td>
                                    <?php $background_url = get_option('weblazem_background_image'); ?>
                                    <?php if($background_url) : ?>
                                        <div class="current-background">
                                            <img src="<?php echo esc_url($background_url); ?>" alt="تصویر پس‌زمینه">
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="file-upload-wrapper">
                                        <input type="file" name="weblazem_background_file" />
                                        <input type="hidden" name="weblazem_background_image" value="<?php echo esc_attr($background_url); ?>" />
                                        <p class="description">تصویر پس‌زمینه سایت را آپلود کنید</p>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                <!-- بخش ویژگی‌ها -->
                <div class="weblazem-tab-content" id="features-tab">
                    <div class="weblazem-admin-card">
                        <div class="weblazem-admin-card-icon"><i class="fas fa-star"></i></div>
                        <h3>ویژگی‌های قالب</h3>
                        <p>قالب وب لازم دارای ویژگی‌های زیادی است که می‌توانید از آن‌ها استفاده کنید:</p>
                        
                        <div class="weblazem-feature-card-grid">
                            <div class="weblazem-feature-card">
                                <div class="weblazem-feature-card-icon">
                                    <i class="fas fa-mobile-alt"></i>
                                </div>
                                <div class="weblazem-feature-card-title">واکنش‌گرا</div>
                                <div class="weblazem-feature-card-desc">
                                    طراحی کاملاً واکنش‌گرا برای نمایش مناسب در تمام دستگاه‌ها
                                </div>
                            </div>
                            
                            <div class="weblazem-feature-card">
                                <div class="weblazem-feature-card-icon">
                                    <i class="fas fa-tachometer-alt"></i>
                                </div>
                                <div class="weblazem-feature-card-title">سریع و بهینه</div>
                                <div class="weblazem-feature-card-desc">
                                    کد بهینه شده برای بارگذاری سریع صفحات و افزایش رتبه سئو
                                </div>
                            </div>
                            
                            <div class="weblazem-feature-card">
                                <div class="weblazem-feature-card-icon">
                                    <i class="fas fa-edit"></i>
                                </div>
                                <div class="weblazem-feature-card-title">شخصی‌سازی آسان</div>
                                <div class="weblazem-feature-card-desc">
                                    امکان شخصی‌سازی تمام بخش‌های قالب از طریق پنل مدیریت
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <?php submit_button('ذخیره تنظیمات', 'primary large', 'submit', true, array('id' => 'submit-btn', 'class' => 'button button-primary button-large')); ?>
            </form>
        </div>
        
        <div class="saved-indicator" id="saved-indicator">تنظیمات با موفقیت ذخیره شد!</div>
    </div>
    
    <script>
    jQuery(document).ready(function($) {
        // تب‌ها
        $('.weblazem-tab').on('click', function() {
            var tab = $(this).data('tab');
            
            // فعال کردن تب
            $('.weblazem-tab').removeClass('active');
            $(this).addClass('active');
            
            // نمایش محتوای تب
            $('.weblazem-tab-content').removeClass('active');
            $('#' + tab + '-tab').addClass('active');
        });
        
        // اضافه کردن انتخابگر رنگ به فیلد رنگ
        if ($.fn.wpColorPicker) {
            $('.color-picker').wpColorPicker({
                change: function(event, ui) {
                    var color = ui.color.toString();
                    updatePreviewColors(color);
                },
                clear: function() {
                    updatePreviewColors('#4f46e5'); // رنگ پیش‌فرض
                }
            });
        } else {
            // اگر انتخابگر رنگ وجود نداشت، از روش ساده استفاده کن
            $('input[name="weblazem_primary_color"]').on('input', function() {
                var color = $(this).val();
                updatePreviewColors(color);
            });
        }
        
        // تابع به‌روزرسانی پیش‌نمایش رنگ‌ها
        function updatePreviewColors(color) {
            // به‌روزرسانی رنگ پیش‌نمایش
            $('.color-preview').css('background-color', color);
            
            // به‌روزرسانی پیش‌نمایش‌های مختلف
            $('.preview-logo span').css('color', color);
            $('.preview-button').css('background-color', color);
            $('.preview-link').css('color', color);
            $('.preview-icon i').css('color', color);
            
            // سایر المان‌های صفحه مدیریت
            $('.weblazem-admin-card-icon').css('color', color);
            $('.weblazem-tab.active').css('color', color);
        }
        
        // نمایش پیام ذخیره‌سازی
        <?php if(isset($_GET['settings-updated']) && $_GET['settings-updated'] == 'true') : ?>
        $('#saved-indicator').addClass('show');
        setTimeout(function() {
            $('#saved-indicator').removeClass('show');
        }, 3000);
        <?php endif; ?>
        
        // انیمیشن کارت‌ها
        $('.weblazem-feature-card').each(function(index) {
            $(this).css('animation', 'float 3s ease-in-out infinite');
            $(this).css('animation-delay', (index * 0.2) + 's');
        });
    });
    </script>
    <?php
}

// Display Social Options Page
function weblazem_social_options_display() {
    ?>
    <div class="wrap" dir="rtl">
        <div class="weblazem-admin-header">
            <div class="weblazem-admin-header-content">
                <h1>تنظیمات شبکه‌های اجتماعی</h1>
                <p>لینک شبکه‌های اجتماعی خود را وارد کنید تا در سایت نمایش داده شوند.</p>
            </div>
        </div>
        
        <div class="weblazem-admin-content" id="weblazem-social-options">
            <form method="post" action="options.php">
                <?php settings_fields('weblazem_social_options_group'); ?>
                <?php do_settings_sections('weblazem_social_options_group'); ?>
                
                <div class="weblazem-admin-card">
                    <div class="weblazem-admin-card-icon"><i class="fas fa-share-alt"></i></div>
                    <h3>شبکه‌های اجتماعی</h3>
                    <table class="form-table">
                        <tr valign="top">
                            <th scope="row">اینستاگرام</th>
                            <td>
                                <input type="url" name="weblazem_instagram" class="regular-text" value="<?php echo esc_attr(get_option('weblazem_instagram')); ?>" placeholder="https://instagram.com/youraccount" />
                                <p class="description">آدرس کامل صفحه اینستاگرام خود را وارد کنید</p>
                            </td>
                        </tr>
                        
                        <tr valign="top">
                            <th scope="row">تلگرام</th>
                            <td>
                                <input type="url" name="weblazem_telegram" class="regular-text" value="<?php echo esc_attr(get_option('weblazem_telegram')); ?>" placeholder="https://t.me/youraccount" />
                                <p class="description">آدرس کامل کانال یا اکانت تلگرام خود را وارد کنید</p>
                            </td>
                        </tr>
                        
                        <tr valign="top">
                            <th scope="row">واتساپ</th>
                            <td>
                                <input type="url" name="weblazem_whatsapp" class="regular-text" value="<?php echo esc_attr(get_option('weblazem_whatsapp')); ?>" placeholder="https://wa.me/989123456789" />
                                <p class="description">لینک واتساپ خود را وارد کنید (مثال: https://wa.me/989123456789)</p>
                            </td>
                        </tr>
                    </table>
                </div>
                
                <?php submit_button('ذخیره تنظیمات', 'primary', 'submit', true, array('id' => 'submit-btn')); ?>
            </form>
        </div>
        
        <div class="saved-indicator" id="saved-indicator">تنظیمات با موفقیت ذخیره شد!</div>
    </div>
    
    <script>
    jQuery(document).ready(function($) {
        // نمایش پیام ذخیره‌سازی
        <?php if(isset($_GET['settings-updated']) && $_GET['settings-updated'] == 'true') : ?>
        $('#saved-indicator').addClass('show');
        setTimeout(function() {
            $('#saved-indicator').removeClass('show');
        }, 3000);
        <?php endif; ?>
    });
    </script>
    <?php
}

/**
 * Helper function to display social media links with icons
 * 
 * @param string $wrapper_class CSS class for the wrapper element
 * @param string $link_class CSS class for the link elements
 * @return string HTML for social media links
 */
function weblazem_get_social_links($wrapper_class = '', $link_class = '') {
    $social_links = array();
    
    // Get social media links from options
    $instagram = get_option('weblazem_instagram');
    $telegram = get_option('weblazem_telegram');
    $whatsapp = get_option('weblazem_whatsapp');
    
    // Build HTML output
    $output = '<div class="' . esc_attr($wrapper_class) . '">';
    
    if (!empty($instagram)) {
        $output .= '<a href="' . esc_url($instagram) . '" class="' . esc_attr($link_class) . '" target="_blank" rel="noopener noreferrer">';
        $output .= '<i class="fab fa-instagram"></i>';
        $output .= '</a>';
    }
    
    if (!empty($telegram)) {
        $output .= '<a href="' . esc_url($telegram) . '" class="' . esc_attr($link_class) . '" target="_blank" rel="noopener noreferrer">';
        $output .= '<i class="fab fa-telegram"></i>';
        $output .= '</a>';
    }
    
    if (!empty($whatsapp)) {
        $output .= '<a href="' . esc_url($whatsapp) . '" class="' . esc_attr($link_class) . '" target="_blank" rel="noopener noreferrer">';
        $output .= '<i class="fab fa-whatsapp"></i>';
        $output .= '</a>';
    }
    
    $output .= '</div>';
    
    return $output;
}

// A shortcode for displaying social media links
function weblazem_social_links_shortcode($atts) {
    $atts = shortcode_atts(array(
        'wrapper_class' => 'social-links',
        'link_class' => 'social-link'
    ), $atts);
    
    return weblazem_get_social_links($atts['wrapper_class'], $atts['link_class']);
}
add_shortcode('weblazem_social', 'weblazem_social_links_shortcode');

// Display Homepage Options
function weblazem_homepage_options_display() {
    if (function_exists('weblazem_ensure_about_team_defaults')) {
        weblazem_ensure_about_team_defaults();
    }
    if (function_exists('weblazem_ensure_customers_defaults')) {
        weblazem_ensure_customers_defaults();
    }
    if (function_exists('weblazem_ensure_testimonials_defaults')) {
        weblazem_ensure_testimonials_defaults();
    }
    if (function_exists('weblazem_ensure_faq_defaults')) {
        weblazem_ensure_faq_defaults();
    }
    if (function_exists('weblazem_ensure_home_section_defaults')) {
        weblazem_ensure_home_section_defaults();
    }

    // آماده‌سازی جاوااسکریپت برای مدیریت کارت‌های خدمات
    wp_enqueue_media();
    
    // اضافه کردن jQuery و URL آژاکس
    wp_enqueue_script('jquery');
    wp_localize_script('jquery', 'weblazem_ajax', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('weblazem_homepage_save')
    ));
    
    // نمایش پیام موفقیت اگر فرم ارسال شده باشد
    if (isset($_GET['updated']) && $_GET['updated'] == 'true') {
        echo '<div class="notice notice-success is-dismissible"><p>تنظیمات با موفقیت ذخیره شدند.</p></div>';
    }
    
    // دریافت مقادیر ذخیره شده
    $hero_title = get_option('weblazem_hero_title', 'وب لازم برای کسب و کار شما');
    $hero_subtitle = get_option('weblazem_hero_subtitle', 'راهکارهای دیجیتال مارکتینگ');
    $hero_text = get_option('weblazem_hero_text', 'لورم ایپسوم متن ساختگی با تولید سادگی نامفهوم از صنعت چاپ و با استفاده از طراحان گرافیک است. چاپگرها و متون بلکه روزنامه و مجله در ستون و سطرآنچنان که لازم است.');
    $hero_button_text = get_option('weblazem_hero_button_text', 'مشاوره رایگان');
    $hero_button_url = get_option('weblazem_hero_button_url', '#');
    $hero_image = get_option('weblazem_hero_image', '');
    
    $services_title = get_option('weblazem_services_title', 'خدمات ما');
    $services_subtitle = get_option('weblazem_services_subtitle', 'خدمات تخصصی وب لازم برای کسب و کار شما');
    
    $services_cards = get_option('weblazem_services_cards', array());
    
    // برون‌سپاری
    $outsourcing_title = get_option('weblazem_outsourcing_title', 'برون‌سپاری پروژه‌ها');
    $outsourcing_subtitle = get_option('weblazem_outsourcing_subtitle', 'با تیم متخصص ما پروژه‌های خود را به صورت برون‌سپاری انجام دهید');
    $outsourcing_button_text = get_option('weblazem_outsourcing_button_text', 'ثبت درخواست مشاوره');
    $outsourcing_button_url = get_option('weblazem_outsourcing_button_url', '#');
    $outsourcing_button_modal = get_option('weblazem_outsourcing_button_modal', '1');
    $outsourcing_background = get_option('weblazem_outsourcing_background', '');
    $outsourcing_buttons = array(); // فعلاً به صورت آرایه خالی

    $portfolio_title = get_option('weblazem_portfolio_title', 'جدیدترین نمونه‌کارهای وب‌لازم');
    $portfolio_more_text = get_option('weblazem_portfolio_more_text', 'نمایش بیشتر');
    $portfolio_card_button_text = get_option('weblazem_portfolio_card_button_text', 'مشاهده‌ی پروژه');
    
    ?>
    <div class="wrap" dir="rtl">
        <div class="weblazem-admin-header">
            <div class="weblazem-admin-header-content">
                <h1>چیدمان صفحه اصلی</h1>
                <p>در این بخش می‌توانید قسمت‌های مختلف صفحه اصلی را مدیریت کنید.</p>
            </div>
        </div>
        
        <div class="weblazem-admin-content">
            <div class="weblazem-tabs">
                <button type="button" class="weblazem-tab active" data-tab="sections">نمایش سکشن‌ها</button>
                <button type="button" class="weblazem-tab" data-tab="hero">بخش هیرو</button>
                <button type="button" class="weblazem-tab" data-tab="services">بخش خدمات</button>
                <button type="button" class="weblazem-tab" data-tab="portfolio">بخش نمونه کارها</button>
                <button type="button" class="weblazem-tab" data-tab="about">درباره ما</button>
                <button type="button" class="weblazem-tab" data-tab="team">تیم لید</button>
                <button type="button" class="weblazem-tab" data-tab="customers">مشتریان</button>
                <button type="button" class="weblazem-tab" data-tab="testimonials">نظرات مشتریان</button>
                <button type="button" class="weblazem-tab" data-tab="faq">سوالات متداول</button>
                <button type="button" class="weblazem-tab" data-tab="outsourcing">بخش برون‌سپاری</button>
            </div>
            
            <form method="post" action="<?php echo admin_url('admin-post.php'); ?>" enctype="multipart/form-data" id="weblazem-homepage-form">
                <input type="hidden" name="action" value="save_weblazem_homepage_options">
                <?php wp_nonce_field('weblazem_homepage_options', 'weblazem_homepage_nonce'); ?>

                <?php weblazem_render_home_sections_tab(); ?>
                
                <!-- بخش هیرو -->
                <div class="weblazem-tab-content" id="hero-tab">
                    <div class="weblazem-admin-card">
                        <div class="weblazem-admin-card-icon"><i class="fas fa-home"></i></div>
                        <h3>بخش هیرو (بنر اصلی سایت)</h3>
                        
                        <div class="weblazem-section-preview">
                            <div class="hero-preview-container">
                                <div class="hero-preview-text">
                                    <h2 id="hero-title-preview"><?php echo esc_html($hero_title); ?></h2>
                                    <h3 id="hero-subtitle-preview"><?php echo esc_html($hero_subtitle); ?></h3>
                                    <p id="hero-text-preview"><?php echo wp_kses_post($hero_text); ?></p>
                                    <button id="hero-button-preview"><?php echo esc_html($hero_button_text); ?></button>
                                </div>
                                <div class="hero-preview-image">
                                    <?php if (!empty($hero_image)) : ?>
                                        <img src="<?php echo esc_url($hero_image); ?>" alt="تصویر هیرو" id="hero-image-preview">
                                    <?php else : ?>
                                        <div class="no-image" id="hero-image-preview-placeholder">بدون تصویر</div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        
                        <table class="form-table">
                            <tr valign="top">
                                <th scope="row">عنوان اصلی</th>
                                <td>
                                    <input type="text" name="weblazem_hero_title" class="regular-text" value="<?php echo esc_attr($hero_title); ?>" data-preview="hero-title-preview" />
                                    <p class="description">عنوان اصلی که در بخش هیرو نمایش داده می‌شود</p>
                                </td>
                            </tr>
                            
                            <tr valign="top">
                                <th scope="row">زیرعنوان</th>
                                <td>
                                    <input type="text" name="weblazem_hero_subtitle" class="regular-text" value="<?php echo esc_attr($hero_subtitle); ?>" data-preview="hero-subtitle-preview" />
                                    <p class="description">زیرعنوان یا توضیح کوتاه</p>
                                </td>
                            </tr>
                            
                            <tr valign="top">
                                <th scope="row">متن توضیحی</th>
                                <td>
                                    <textarea name="weblazem_hero_text" class="large-text" rows="4" data-preview="hero-text-preview"><?php echo esc_textarea($hero_text); ?></textarea>
                                    <p class="description">متن توضیحی بخش هیرو</p>
                                </td>
                            </tr>
                            
                            <tr valign="top">
                                <th scope="row">متن دکمه</th>
                                <td>
                                    <input type="text" name="weblazem_hero_button_text" class="regular-text" value="<?php echo esc_attr($hero_button_text); ?>" data-preview="hero-button-preview" />
                                    <p class="description">متن نمایش داده شده روی دکمه</p>
                                </td>
                            </tr>
                            
                            <tr valign="top">
                                <th scope="row">لینک دکمه</th>
                                <td>
                                    <input type="url" name="weblazem_hero_button_url" class="regular-text" value="<?php echo esc_url($hero_button_url); ?>" />
                                    <p class="description">آدرس مقصد دکمه</p>
                                </td>
                            </tr>
                            
                            <tr valign="top">
                                <th scope="row">تصویر هیرو</th>
                                <td>
                                    <?php if (!empty($hero_image)) : ?>
                                        <div class="current-hero-image">
                                            <img src="<?php echo esc_url($hero_image); ?>" alt="تصویر هیرو">
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="file-upload-wrapper">
                                        <input type="file" name="weblazem_hero_image_file" id="hero-image-uploader" />
                                        <input type="hidden" name="weblazem_hero_image" value="<?php echo esc_attr($hero_image); ?>" />
                                        <p class="description">تصویر بخش هیرو (پیشنهاد: 600×600 پیکسل)</p>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                <!-- بخش خدمات -->
                <div class="weblazem-tab-content" id="services-tab">
                    <div class="weblazem-admin-card">
                        <div class="weblazem-admin-card-icon"><i class="fas fa-briefcase"></i></div>
                        <h3>بخش خدمات</h3>
                        
                        <div class="services-section-header">
                            <table class="form-table">
                                <tr valign="top">
                                    <th scope="row">عنوان بخش</th>
                                    <td>
                                        <input type="text" name="weblazem_services_title" class="regular-text" value="<?php echo esc_attr($services_title); ?>" />
                                        <p class="description">عنوان اصلی بخش خدمات</p>
                                    </td>
                                </tr>
                                
                                <tr valign="top">
                                    <th scope="row">زیرعنوان بخش</th>
                                    <td>
                                        <input type="text" name="weblazem_services_subtitle" class="regular-text" value="<?php echo esc_attr($services_subtitle); ?>" />
                                        <p class="description">زیرعنوان یا توضیح کوتاه برای بخش خدمات</p>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        
                        <h4>کارت‌های خدمات</h4>
                        <p class="description">کارت‌های خدمات را مدیریت کنید. هر کارت شامل یک عنوان، تصویر و دکمه است.</p>
                        
                        <div id="services-cards-container">
                            <?php foreach ($services_cards as $index => $card) : ?>
                                <div class="service-card-item" data-index="<?php echo esc_attr($index); ?>">
                                    <div class="service-card-header">
                                        <span class="service-card-title"><?php echo esc_html($card['title']); ?></span>
                                        <div class="service-card-actions">
                                            <button type="button" class="button service-card-toggle"><span class="dashicons dashicons-arrow-down-alt2"></span></button>
                                            <button type="button" class="button service-card-remove"><span class="dashicons dashicons-no-alt"></span></button>
                                        </div>
                                    </div>
                                    
                                    <div class="service-card-content">
                                        <table class="form-table">
                                            <tr valign="top">
                                                <th scope="row">عنوان کارت</th>
                                                <td>
                                                    <input type="text" name="weblazem_services_cards[<?php echo esc_attr($index); ?>][title]" class="regular-text service-card-title-input" value="<?php echo esc_attr($card['title']); ?>" />
                                                </td>
                                            </tr>
                                            
                                            <tr valign="top">
                                                <th scope="row">تصویر کارت</th>
                                                <td>
                                                    <div class="service-card-image-preview">
                                                        <?php if (!empty($card['image'])) : ?>
                                                            <img src="<?php echo esc_url($card['image']); ?>" alt="تصویر کارت">
                                                        <?php else : ?>
                                                            <div class="no-image">بدون تصویر</div>
                                                        <?php endif; ?>
                                                    </div>
                                                    
                                                    <button type="button" class="button service-card-upload">انتخاب تصویر</button>
                                                    <button type="button" class="button service-card-remove-image" <?php echo empty($card['image']) ? 'style="display:none;"' : ''; ?>>حذف تصویر</button>
                                                    <input type="hidden" name="weblazem_services_cards[<?php echo esc_attr($index); ?>][image]" class="service-card-image-url" value="<?php echo esc_attr($card['image']); ?>" />
                                                </td>
                                            </tr>
                                            
                                            <tr valign="top">
                                                <th scope="row">متن دکمه</th>
                                                <td>
                                                    <input type="text" name="weblazem_services_cards[<?php echo esc_attr($index); ?>][button_text]" class="regular-text" value="<?php echo esc_attr($card['button_text']); ?>" />
                                                </td>
                                            </tr>
                                            
                                            <tr valign="top">
                                                <th scope="row">لینک دکمه</th>
                                                <td>
                                                    <input type="text" name="weblazem_services_cards[<?php echo esc_attr($index); ?>][button_url]" class="regular-text" value="<?php echo esc_attr($card['button_url']); ?>" />
                                                    <p class="description">آدرس URL دکمه را وارد کنید (می‌تواند خالی باشد)</p>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <div class="service-card-actions">
                            <button type="button" class="button button-primary" id="add-service-card">افزودن کارت جدید</button>
                        </div>
                        
                        <!-- الگو برای کارت جدید (مخفی) -->
                        <div id="service-card-template" style="display:none;">
                            <div class="service-card-item" data-index="{{index}}">
                                <div class="service-card-header">
                                    <span class="service-card-title">کارت جدید</span>
                                    <div class="service-card-actions">
                                        <button type="button" class="button service-card-toggle"><span class="dashicons dashicons-arrow-down-alt2"></span></button>
                                        <button type="button" class="button service-card-remove"><span class="dashicons dashicons-no-alt"></span></button>
                                    </div>
                                </div>
                                
                                <div class="service-card-content">
                                    <table class="form-table">
                                        <tr valign="top">
                                            <th scope="row">عنوان کارت</th>
                                            <td>
                                                <input type="text" name="weblazem_services_cards[{{index}}][title]" class="regular-text service-card-title-input" value="کارت جدید" />
                                            </td>
                                        </tr>
                                        
                                        <tr valign="top">
                                            <th scope="row">تصویر کارت</th>
                                            <td>
                                                <div class="service-card-image-preview">
                                                    <div class="no-image">بدون تصویر</div>
                                                </div>
                                                
                                                <button type="button" class="button service-card-upload">انتخاب تصویر</button>
                                                <button type="button" class="button service-card-remove-image" style="display:none;">حذف تصویر</button>
                                                <input type="hidden" name="weblazem_services_cards[{{index}}][image]" class="service-card-image-url" value="" />
                                            </td>
                                        </tr>
                                        
                                        <tr valign="top">
                                            <th scope="row">متن دکمه</th>
                                            <td>
                                                <input type="text" name="weblazem_services_cards[{{index}}][button_text]" class="regular-text" value="مشاهده خدمات" />
                                            </td>
                                        </tr>
                                        
                                        <tr valign="top">
                                            <th scope="row">لینک دکمه</th>
                                            <td>
                                                <input type="text" name="weblazem_services_cards[{{index}}][button_url]" class="regular-text" value="#" />
                                                <p class="description">آدرس URL دکمه را وارد کنید (می‌تواند خالی باشد)</p>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- بخش برون‌سپاری -->
                <?php weblazem_render_portfolio_homepage_tab(); ?>

                <?php weblazem_render_about_homepage_tab(); ?>

                <?php weblazem_render_team_homepage_tab(); ?>

                <?php weblazem_render_customers_homepage_tab(); ?>

                <?php weblazem_render_testimonials_homepage_tab(); ?>

                <?php weblazem_render_faq_homepage_tab(); ?>

                <div class="weblazem-tab-content" id="outsourcing-tab">
                    <div class="weblazem-admin-card">
                        <div class="weblazem-admin-card-icon"><i class="fas fa-building"></i></div>
                        <h3>بخش برون‌سپاری</h3>
                        
                        <div class="outsourcing-section-header">
                            <table class="form-table">
                                <tr valign="top">
                                    <th scope="row">عنوان بخش</th>
                                    <td>
                                        <input type="text" name="weblazem_outsourcing_title" class="regular-text" value="<?php echo esc_attr($outsourcing_title); ?>" />
                                        <p class="description">عنوان اصلی بخش برون‌سپاری</p>
                                    </td>
                                </tr>
                                
                                <tr valign="top">
                                    <th scope="row">زیرعنوان بخش</th>
                                    <td>
                                        <input type="text" name="weblazem_outsourcing_subtitle" class="regular-text" value="<?php echo esc_attr($outsourcing_subtitle); ?>" />
                                        <p class="description">زیرعنوان یا توضیح کوتاه برای بخش برون‌سپاری</p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">متن دکمه</th>
                                    <td>
                                        <input type="text" name="weblazem_outsourcing_button_text" class="regular-text" value="<?php echo esc_attr($outsourcing_button_text); ?>" />
                                        <p class="description">متن دکمه (پیش‌فرض: ثبت درخواست مشاوره)</p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">رفتار دکمه</th>
                                    <td>
                                        <input type="hidden" name="weblazem_outsourcing_button_modal" value="0" />
                                        <label>
                                            <input type="checkbox" name="weblazem_outsourcing_button_modal" value="1" <?php checked($outsourcing_button_modal, '1'); ?> />
                                            باز کردن مودال ثبت درخواست مشاوره
                                        </label>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">لینک دکمه</th>
                                    <td>
                                        <input type="text" name="weblazem_outsourcing_button_url" class="regular-text" value="<?php echo esc_attr($outsourcing_button_url); ?>" />
                                        <p class="description">فقط وقتی مودال غیرفعال است استفاده می‌شود</p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">تصویر پس‌زمینه</th>
                                    <td>
                                        <?php if (!empty($outsourcing_background)) : ?>
                                            <div class="weblazem-image-preview">
                                                <img src="<?php echo esc_url($outsourcing_background); ?>" alt="تصویر پس‌زمینه" style="max-width: 300px; margin-bottom: 10px;" />
                                            </div>
                                        <?php endif; ?>
                                        <input type="file" name="weblazem_outsourcing_background_file" id="weblazem_outsourcing_background_file" />
                                        <input type="hidden" name="weblazem_outsourcing_background" value="<?php echo esc_attr($outsourcing_background); ?>" />
                                        <p class="description">تصویر پس‌زمینه بخش برون‌سپاری (پیشنهادی: 1920×600 پیکسل)</p>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        
                        <h4>دکمه‌های برون‌سپاری</h4>
                        <p class="description">دکمه‌های برون‌سپاری را مدیریت کنید. هر دکمه شامل یک عنوان، تصویر و دکمه است.</p>
                        
                        <div id="outsourcing-buttons-container">
                            <?php foreach ($outsourcing_buttons as $index => $button) : ?>
                                <div class="outsourcing-button-item" data-index="<?php echo esc_attr($index); ?>">
                                    <div class="outsourcing-button-header">
                                        <span class="outsourcing-button-title"><?php echo esc_html($button['title']); ?></span>
                                        <div class="outsourcing-button-actions">
                                            <button type="button" class="button outsourcing-button-toggle"><span class="dashicons dashicons-arrow-down-alt2"></span></button>
                                            <button type="button" class="button outsourcing-button-remove"><span class="dashicons dashicons-no-alt"></span></button>
                                        </div>
                                    </div>
                                    
                                    <div class="outsourcing-button-content">
                                        <table class="form-table">
                                            <tr valign="top">
                                                <th scope="row">عنوان دکمه</th>
                                                <td>
                                                    <input type="text" name="weblazem_outsourcing_buttons[<?php echo esc_attr($index); ?>][title]" class="regular-text outsourcing-button-title-input" value="<?php echo esc_attr($button['title']); ?>" />
                                                </td>
                                            </tr>
                                            
                                            <tr valign="top">
                                                <th scope="row">تصویر دکمه</th>
                                                <td>
                                                    <div class="outsourcing-button-image-preview">
                                                        <?php if (!empty($button['image'])) : ?>
                                                            <img src="<?php echo esc_url($button['image']); ?>" alt="تصویر دکمه">
                                                        <?php else : ?>
                                                            <div class="no-image">بدون تصویر</div>
                                                        <?php endif; ?>
                                                    </div>
                                                    
                                                    <button type="button" class="button outsourcing-button-upload">انتخاب تصویر</button>
                                                    <button type="button" class="button outsourcing-button-remove-image" <?php echo empty($button['image']) ? 'style="display:none;"' : ''; ?>>حذف تصویر</button>
                                                    <input type="hidden" name="weblazem_outsourcing_buttons[<?php echo esc_attr($index); ?>][image]" class="outsourcing-button-image-url" value="<?php echo esc_attr($button['image']); ?>" />
                                                </td>
                                            </tr>
                                            
                                            <tr valign="top">
                                                <th scope="row">متن دکمه</th>
                                                <td>
                                                    <input type="text" name="weblazem_outsourcing_buttons[<?php echo esc_attr($index); ?>][button_text]" class="regular-text" value="<?php echo esc_attr($button['button_text']); ?>" />
                                                </td>
                                            </tr>
                                            
                                            <tr valign="top">
                                                <th scope="row">لینک دکمه</th>
                                                <td>
                                                    <input type="text" name="weblazem_outsourcing_buttons[<?php echo esc_attr($index); ?>][button_url]" class="regular-text" value="<?php echo esc_attr($button['button_url']); ?>" />
                                                    <p class="description">آدرس URL دکمه را وارد کنید (می‌تواند خالی باشد)</p>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <div class="outsourcing-button-actions">
                            <button type="button" class="button button-primary" id="add-outsourcing-button">افزودن دکمه جدید</button>
                        </div>
                        
                        <!-- الگو برای دکمه جدید (مخفی) -->
                        <div id="outsourcing-button-template" style="display:none;">
                            <div class="outsourcing-button-item" data-index="{{index}}">
                                <div class="outsourcing-button-header">
                                    <span class="outsourcing-button-title">دکمه جدید</span>
                                    <div class="outsourcing-button-actions">
                                        <button type="button" class="button outsourcing-button-toggle"><span class="dashicons dashicons-arrow-down-alt2"></span></button>
                                        <button type="button" class="button outsourcing-button-remove"><span class="dashicons dashicons-no-alt"></span></button>
                                    </div>
                                </div>
                                
                                <div class="outsourcing-button-content">
                                    <table class="form-table">
                                        <tr valign="top">
                                            <th scope="row">عنوان دکمه</th>
                                            <td>
                                                <input type="text" name="weblazem_outsourcing_buttons[{{index}}][title]" class="regular-text outsourcing-button-title-input" value="دکمه جدید" />
                                            </td>
                                        </tr>
                                        
                                        <tr valign="top">
                                            <th scope="row">تصویر دکمه</th>
                                            <td>
                                                <div class="outsourcing-button-image-preview">
                                                    <div class="no-image">بدون تصویر</div>
                                                </div>
                                                
                                                <button type="button" class="button outsourcing-button-upload">انتخاب تصویر</button>
                                                <button type="button" class="button outsourcing-button-remove-image" style="display:none;">حذف تصویر</button>
                                                <input type="hidden" name="weblazem_outsourcing_buttons[{{index}}][image]" class="outsourcing-button-image-url" value="" />
                                            </td>
                                        </tr>
                                        
                                        <tr valign="top">
                                            <th scope="row">متن دکمه</th>
                                            <td>
                                                <input type="text" name="weblazem_outsourcing_buttons[{{index}}][button_text]" class="regular-text" value="مشاهده خدمات" />
                                            </td>
                                        </tr>
                                        
                                        <tr valign="top">
                                            <th scope="row">لینک دکمه</th>
                                            <td>
                                                <input type="text" name="weblazem_outsourcing_buttons[{{index}}][button_url]" class="regular-text" value="#" />
                                                <p class="description">آدرس URL دکمه را وارد کنید (می‌تواند خالی باشد)</p>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <?php submit_button('ذخیره تنظیمات', 'primary large', 'submit', true, array('id' => 'submit-btn', 'class' => 'button button-primary button-large')); ?>
            </form>
        </div>
        
        <div class="saved-indicator" id="saved-indicator">تنظیمات با موفقیت ذخیره شد!</div>
    </div>
    
    <style>
        /* استایل‌های مخصوص بخش چیدمان صفحه اصلی */
        .weblazem-section-preview {
            background: var(--weblazem-light);
            border: 1px solid var(--weblazem-border);
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 30px;
        }
        
        /* وضعیت بارگذاری */
        .loading-image {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 150px;
            background-color: #f0f0f0;
            border-radius: 8px;
            color: #666;
            font-size: 14px;
            position: relative;
        }
        
        .loading-image:before {
            content: '';
            position: absolute;
            width: 30px;
            height: 30px;
            border: 3px solid #ddd;
            border-top-color: var(--weblazem-primary, #4F1E60);
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-right: 10px;
            margin-bottom: 10px;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        /* نشانگر ذخیره‌سازی بهبود یافته */
        .saved-indicator {
            position: fixed;
            bottom: 30px;
            left: 30px;
            background-color: #4CAF50;
            color: white;
            padding: 15px 25px;
            border-radius: 5px;
            font-size: 16px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            transform: translateY(100px);
            opacity: 0;
            transition: all 0.3s ease;
            z-index: 9999;
            display: flex;
            align-items: center;
        }
        
        .saved-indicator:before {
            content: '\f147';
            font-family: dashicons;
            font-size: 20px;
            margin-left: 10px;
        }
        
        .saved-indicator.show {
            transform: translateY(0);
            opacity: 1;
        }
        
        .saved-indicator.error {
            background-color: #F44336;
        }
        
        .saved-indicator.error:before {
            content: '\f335';
        }
        
        /* استایل‌های موجود */
        .hero-preview-container {
            display: flex;
            flex-wrap: wrap;
            gap: 30px;
        }
        
        .hero-preview-text {
            flex: 1;
            min-width: 300px;
        }
        
        .hero-preview-text h2 {
            font-size: 24px;
            margin-top: 0;
            color: var(--weblazem-dark);
        }
        
        .hero-preview-text h3 {
            font-size: 18px;
            color: var(--weblazem-primary);
            margin-top: 5px;
        }
        
        .hero-preview-text p {
            font-size: 14px;
            color: var(--weblazem-gray);
            margin: 15px 0;
        }
        
        .hero-preview-text button {
            background: var(--weblazem-primary);
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-family: var(--weblazem-font);
        }
        
        .hero-preview-image {
            flex: 1;
            min-width: 300px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .hero-preview-image img {
            max-width: 100%;
            height: auto;
            max-height: 300px;
            border-radius: 8px;
        }
        
        .current-hero-image {
            margin-bottom: 15px;
        }
        
        .current-hero-image img {
            max-width: 300px;
            height: auto;
            border-radius: 8px;
            border: 1px solid var(--weblazem-border);
        }
        
        .no-image {
            width: 300px;
            height: 200px;
            background: #f0f0f0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--weblazem-gray);
            border-radius: 8px;
            border: 1px dashed var(--weblazem-border);
        }
        
        /* استایل کارت‌های خدمات */
        #services-cards-container {
            margin: 20px 0;
        }
        
        .service-card-item {
            background: #fff;
            border: 1px solid var(--weblazem-border);
            border-radius: 5px;
            margin-bottom: 15px;
        }
        
        .service-card-header {
            padding: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: var(--weblazem-light);
            border-bottom: 1px solid var(--weblazem-border);
            cursor: pointer;
        }
        
        .service-card-title {
            font-weight: 500;
        }
        
        .service-card-actions {
            display: flex;
            gap: 5px;
        }
        
        .service-card-content {
            padding: 15px;
            display: none;
        }
        
        .service-card-content.active {
            display: block;
        }
        
        .service-card-image-preview {
            margin-bottom: 10px;
        }
        
        .service-card-image-preview img {
            max-width: 200px;
            height: auto;
            border-radius: 4px;
            border: 1px solid var(--weblazem-border);
        }
        
        .service-card-actions button {
            margin-right: 5px;
        }
    </style>
    
    <script>
    jQuery(document).ready(function($) {
        // تب‌ها
        $('.weblazem-tab').on('click', function() {
            var tab = $(this).data('tab');
            
            // فعال کردن تب
            $('.weblazem-tab').removeClass('active');
            $(this).addClass('active');
            
            // نمایش محتوای تب
            $('.weblazem-tab-content').removeClass('active');
            $('#' + tab + '-tab').addClass('active');
        });
        
        // تابع کمکی برای آپلود تصاویر با AJAX
        function uploadServiceImageAjax(file, cardIndex, callback) {
            if (!file) {
                return;
            }
            
            var formData = new FormData();
            formData.append('file', file);
            formData.append('action', 'weblazem_upload_service_image');
            formData.append('nonce', weblazem_ajax.nonce);
            formData.append('card_index', cardIndex);
            
            $.ajax({
                url: weblazem_ajax.ajax_url,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success && callback) {
                        callback(response.data.url);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('خطا در آپلود تصویر:', error);
                    alert('خطا در آپلود تصویر: ' + error);
                }
            });
        }
        
        // پیش‌نمایش زنده برای بخش هیرو
        $('[data-preview]').on('input', function() {
            var previewId = $(this).data('preview');
            var value = $(this).val();
            
            $('#' + previewId).text(value);
        });
        
        // نمایش/مخفی‌سازی محتوای کارت خدمات
        $(document).on('click', '.service-card-header', function(e) {
            if (!$(e.target).is('button, span')) {
                $(this).next('.service-card-content').slideToggle();
                $(this).find('.service-card-toggle span').toggleClass('dashicons-arrow-down-alt2 dashicons-arrow-up-alt2');
            }
        });
        
        // دکمه باز/بسته کردن
        $(document).on('click', '.service-card-toggle', function(e) {
            e.stopPropagation();
            $(this).closest('.service-card-header').next('.service-card-content').slideToggle();
            $(this).find('span').toggleClass('dashicons-arrow-down-alt2 dashicons-arrow-up-alt2');
        });
        
        // حذف کارت خدمات
        $(document).on('click', '.service-card-remove', function(e) {
            e.stopPropagation();
            if (confirm('آیا از حذف این کارت اطمینان دارید؟')) {
                $(this).closest('.service-card-item').remove();
                updateCardIndices();
            }
        });
        
        // افزودن کارت جدید
        $('#add-service-card').on('click', function() {
            var template = $('#service-card-template').html();
            var index = $('.service-card-item').length;
            template = template.replace(/{{index}}/g, index);
            
            $('#services-cards-container').append(template);
            $('.service-card-item[data-index="' + index + '"] .service-card-content').show();
        });
        
        // بروزرسانی ایندکس‌های کارت‌ها
        function updateCardIndices() {
            $('.service-card-item').each(function(index) {
                $(this).attr('data-index', index);
                
                $(this).find('input, select, textarea').each(function() {
                    var name = $(this).attr('name');
                    if (name) {
                        var newName = name.replace(/weblazem_services_cards\[\d+\]/g, 'weblazem_services_cards[' + index + ']');
                        $(this).attr('name', newName);
                    }
                });
            });
            
            console.log('ایندکس‌های کارت‌ها بروزرسانی شدند. تعداد کارت‌ها: ' + $('.service-card-item').length);
            
            // بررسی مقادیر کارت‌ها برای دیباگ
            $('.service-card-item').each(function(index) {
                var title = $(this).find('.service-card-title-input').val();
                var image = $(this).find('.service-card-image-url').val();
                console.log('کارت ' + index + ': عنوان = ' + title + ', تصویر = ' + (image ? 'دارد' : 'ندارد'));
            });
        }
        
        // آپلود تصویر کارت
        $(document).on('click', '.service-card-upload', function(e) {
            e.preventDefault();
            
            var button = $(this);
            var cardItem = button.closest('.service-card-item');
            var cardIndex = cardItem.data('index');
            var imagePreview = cardItem.find('.service-card-image-preview');
            var imageUrl = cardItem.find('.service-card-image-url');
            var removeButton = cardItem.find('.service-card-remove-image');
            
            // نمایش وضعیت در حال بارگذاری
            button.prop('disabled', true).text('در حال آپلود...');
            imagePreview.html('<div class="loading-image">در حال آپلود تصویر...</div>');
            
            var mediaUploader = wp.media({
                title: 'انتخاب تصویر',
                button: {
                    text: 'استفاده از این تصویر'
                },
                multiple: false
            });
            
            mediaUploader.on('select', function() {
                var attachment = mediaUploader.state().get('selection').first().toJSON();
                
                // نمایش تصویر
                imagePreview.html('<img src="' + attachment.url + '" alt="تصویر کارت">');
                
                // به‌روزرسانی URL تصویر
                imageUrl.val(attachment.url);
                
                // نمایش دکمه حذف
                removeButton.show();
                
                // بازگرداندن دکمه به حالت عادی
                button.prop('disabled', false).text('انتخاب تصویر');
                
                console.log('تصویر انتخاب شد:', attachment.url);
            });
            
            mediaUploader.on('close', function() {
                // بازگرداندن دکمه به حالت عادی اگر هیچ تصویری انتخاب نشده
                if (imageUrl.val() === '') {
                    imagePreview.html(imagePreview.data('original-content') || '<div class="no-image">بدون تصویر</div>');
                }
                button.prop('disabled', false).text('انتخاب تصویر');
            });
            
            // ذخیره محتوای فعلی پیش‌نمایش برای بازگشت در صورت لغو
            imagePreview.data('original-content', imagePreview.html());
            
            mediaUploader.open();
        });
        
        // حذف تصویر کارت
        $(document).on('click', '.service-card-remove-image', function(e) {
            e.preventDefault();
            
            var button = $(this);
            var cardItem = button.closest('.service-card-item');
            var imagePreview = cardItem.find('.service-card-image-preview');
            var imageUrl = cardItem.find('.service-card-image-url');
            
            // حذف تصویر و مخفی کردن دکمه حذف
            imagePreview.html('<div class="no-image">بدون تصویر</div>');
            imageUrl.val('');
            button.hide();
        });
        
        // بروزرسانی عنوان کارت در هدر
        $(document).on('input', '.service-card-title-input', function() {
            var title = $(this).val();
            $(this).closest('.service-card-item').find('.service-card-title').text(title);
        });
        
        // نمایش پیام ذخیره‌سازی
        <?php if(isset($_GET['updated']) && $_GET['updated'] == 'true') : ?>
        showSavedIndicator('تنظیمات با موفقیت ذخیره شدند.', 'success');
        <?php endif; ?>
        
        // اصلاح ارسال فرم
        $('#weblazem-homepage-form').on('submit', function(e) {
            // آماده‌سازی ایندکس‌های کارت‌ها قبل از ارسال
            updateCardIndices();
            
            // ارسال فرم به صورت عادی ادامه پیدا می‌کند
            return true;
        });
        
        // تابع نمایش پیام ذخیره‌سازی
        function showSavedIndicator(message, type) {
            var indicator = $('#saved-indicator');
            
            // حذف کلاس‌های قبلی
            indicator.removeClass('success error loading show');
            
            // تنظیم پیام
            indicator.text(message);
            
            // اضافه کردن کلاس نوع
            if (type) {
                indicator.addClass(type);
            }
            
            // نمایش پیام
            setTimeout(function() {
                indicator.addClass('show');
            }, 100);
            
            // مخفی کردن پیام بعد از مدتی (به جز حالت در حال بارگذاری)
            if (type !== 'loading') {
                setTimeout(function() {
                    indicator.removeClass('show');
                }, 3000);
            }
        }
    });
    </script>
    
    <?php
    // بررسی ارسال فرم و ذخیره تنظیمات
    if (isset($_POST['submit']) && isset($_POST['weblazem_homepage_nonce']) && 
        wp_verify_nonce($_POST['weblazem_homepage_nonce'], 'weblazem_homepage_options')) {
        
        // بررسی هیرو
        if (isset($_POST['weblazem_hero_title'])) {
            update_option('weblazem_hero_title', sanitize_text_field($_POST['weblazem_hero_title']));
        }
        
        if (isset($_POST['weblazem_hero_subtitle'])) {
            update_option('weblazem_hero_subtitle', sanitize_text_field($_POST['weblazem_hero_subtitle']));
        }
        
        if (isset($_POST['weblazem_hero_text'])) {
            update_option('weblazem_hero_text', wp_kses_post($_POST['weblazem_hero_text']));
        }
        
        if (isset($_POST['weblazem_hero_button_text'])) {
            update_option('weblazem_hero_button_text', sanitize_text_field($_POST['weblazem_hero_button_text']));
        }
        
        if (isset($_POST['weblazem_hero_button_url'])) {
            update_option('weblazem_hero_button_url', esc_url_raw($_POST['weblazem_hero_button_url']));
        }
        
        // آپلود تصویر هیرو
        if (!empty($_FILES['weblazem_hero_image_file']['name'])) {
            $hero_image = weblazem_handle_hero_image_upload(get_option('weblazem_hero_image'));
            update_option('weblazem_hero_image', $hero_image);
        }

        // بخش خدمات
        if (isset($_POST['weblazem_services_title'])) {
            update_option('weblazem_services_title', sanitize_text_field($_POST['weblazem_services_title']));
        }

        if (isset($_POST['weblazem_services_subtitle'])) {
            update_option('weblazem_services_subtitle', sanitize_text_field($_POST['weblazem_services_subtitle']));
        }

        // کارت‌های خدمات
        if (isset($_POST['weblazem_services_cards']) && is_array($_POST['weblazem_services_cards'])) {
            $services_cards = array();

            // پردازش هر کارت به صورت دستی
            foreach ($_POST['weblazem_services_cards'] as $index => $card) {
                if (empty($card['title'])) {
                    continue; // کارت بدون عنوان را رد کن
                }

                // مدیریت URL دکمه (می‌تواند خالی باشد)
                $button_url = '';
                if (isset($card['button_url']) && !empty($card['button_url'])) {
                    $button_url = esc_url_raw($card['button_url']);
                }

                $services_cards[] = array(
                    'title' => sanitize_text_field($card['title']),
                    'image' => isset($card['image']) ? esc_url_raw($card['image']) : '',
                    'button_text' => isset($card['button_text']) ? sanitize_text_field($card['button_text']) : '',
                    'button_url' => $button_url,
                );
            }

            update_option('weblazem_services_cards', $services_cards);
        } else {
            // آرایه خالی ذخیره کن اگر هیچ کارتی وجود نداشت
            update_option('weblazem_services_cards', array());
        }

        // ثبت زمان آخرین بروزرسانی
        update_option('weblazem_homepage_last_update', current_time('mysql'));

        // ریدایرکت به صفحه ویرایش با پیام موفقیت
        wp_redirect(add_query_arg(
            array(
                'page' => 'weblazem-homepage-options',
                'updated' => 'true'
            ),
            admin_url('admin.php')
        ));
        exit;
    }
}

/**
 * مدیریت درخواست AJAX برای ذخیره تنظیمات چیدمان صفحه اصلی
 */
function weblazem_save_homepage_options_ajax() {
    // بررسی امنیت
    if (!isset($_POST['security']) || !wp_verify_nonce($_POST['security'], 'weblazem_homepage_save')) {
        wp_send_json_error('خطای امنیتی: نانس نامعتبر است.');
        return;
    }
    
    // بررسی سطح دسترسی
    if (!current_user_can('manage_options')) {
        wp_send_json_error('شما دسترسی لازم برای این عملیات را ندارید.');
        return;
    }
    
    $debug_data = array(); // آرایه برای ذخیره اطلاعات دیباگ
    
    // بررسی هیرو
    if (isset($_POST['weblazem_hero_title'])) {
        $hero_title = sanitize_text_field($_POST['weblazem_hero_title']);
        $result = update_option('weblazem_hero_title', $hero_title);
        $debug_data['hero_title'] = array('value' => $hero_title, 'saved' => $result);
    }
    
    if (isset($_POST['weblazem_hero_subtitle'])) {
        $hero_subtitle = sanitize_text_field($_POST['weblazem_hero_subtitle']);
        $result = update_option('weblazem_hero_subtitle', $hero_subtitle);
        $debug_data['hero_subtitle'] = array('value' => $hero_subtitle, 'saved' => $result);
    }
    
    if (isset($_POST['weblazem_hero_text'])) {
        $hero_text = wp_kses_post($_POST['weblazem_hero_text']);
        $result = update_option('weblazem_hero_text', $hero_text);
        $debug_data['hero_text'] = array('value' => substr($hero_text, 0, 50) . '...', 'saved' => $result);
    }
    
    if (isset($_POST['weblazem_hero_button_text'])) {
        $button_text = sanitize_text_field($_POST['weblazem_hero_button_text']);
        $result = update_option('weblazem_hero_button_text', $button_text);
        $debug_data['hero_button_text'] = array('value' => $button_text, 'saved' => $result);
    }
    
    if (isset($_POST['weblazem_hero_button_url'])) {
        $button_url = esc_url_raw($_POST['weblazem_hero_button_url']);
        $result = update_option('weblazem_hero_button_url', $button_url);
        $debug_data['hero_button_url'] = array('value' => $button_url, 'saved' => $result);
    }
    
    // آپلود تصویر هیرو
    if (!empty($_FILES['weblazem_hero_image_file']['name'])) {
        $hero_image = weblazem_handle_hero_image_upload(get_option('weblazem_hero_image'));
        $result = update_option('weblazem_hero_image', $hero_image);
        $debug_data['hero_image'] = array('value' => $hero_image, 'saved' => $result);
    }
    
    // بخش خدمات
    if (isset($_POST['weblazem_services_title'])) {
        $services_title = sanitize_text_field($_POST['weblazem_services_title']);
        $result = update_option('weblazem_services_title', $services_title);
        $debug_data['services_title'] = array('value' => $services_title, 'saved' => $result);
    }
    
    if (isset($_POST['weblazem_services_subtitle'])) {
        $services_subtitle = sanitize_text_field($_POST['weblazem_services_subtitle']);
        $result = update_option('weblazem_services_subtitle', $services_subtitle);
        $debug_data['services_subtitle'] = array('value' => $services_subtitle, 'saved' => $result);
    }
    
    // کارت‌های خدمات
    if (isset($_POST['weblazem_services_cards']) && is_array($_POST['weblazem_services_cards'])) {
        $services_cards = weblazem_sanitize_services_cards($_POST['weblazem_services_cards']);
        $result = update_option('weblazem_services_cards', $services_cards);
        $debug_data['services_cards'] = array('count' => count($services_cards), 'saved' => $result);
    } else {
        // آرایه خالی ذخیره کن اگر هیچ کارتی وجود نداشت
        $result = update_option('weblazem_services_cards', array());
        $debug_data['services_cards'] = array('count' => 0, 'saved' => $result);
    }
    
    // بخش برون‌سپاری
    if (isset($_POST['weblazem_outsourcing_title'])) {
        $outsourcing_title = sanitize_text_field($_POST['weblazem_outsourcing_title']);
        $result = update_option('weblazem_outsourcing_title', $outsourcing_title);
        $debug_data['outsourcing_title'] = array('value' => $outsourcing_title, 'saved' => $result);
    }
    
    if (isset($_POST['weblazem_outsourcing_subtitle'])) {
        $outsourcing_subtitle = sanitize_text_field($_POST['weblazem_outsourcing_subtitle']);
        $result = update_option('weblazem_outsourcing_subtitle', $outsourcing_subtitle);
        $debug_data['outsourcing_subtitle'] = array('value' => $outsourcing_subtitle, 'saved' => $result);
    }
    
    if (isset($_POST['weblazem_outsourcing_button_text'])) {
        $outsourcing_button_text = sanitize_text_field($_POST['weblazem_outsourcing_button_text']);
        $result = update_option('weblazem_outsourcing_button_text', $outsourcing_button_text);
        $debug_data['outsourcing_button_text'] = array('value' => $outsourcing_button_text, 'saved' => $result);
    }
    
    if (isset($_POST['weblazem_outsourcing_button_url'])) {
        $outsourcing_button_url = esc_url_raw($_POST['weblazem_outsourcing_button_url']);
        $result = update_option('weblazem_outsourcing_button_url', $outsourcing_button_url);
        $debug_data['outsourcing_button_url'] = array('value' => $outsourcing_button_url, 'saved' => $result);
    }
    
    // آپلود تصویر پس‌زمینه برون‌سپاری
    if (!empty($_FILES['weblazem_outsourcing_background_file']['name'])) {
        $outsourcing_background = weblazem_handle_outsourcing_background_upload(get_option('weblazem_outsourcing_background'));
        update_option('weblazem_outsourcing_background', $outsourcing_background);
    }

    weblazem_save_portfolio_homepage_options();
    weblazem_save_about_team_homepage_options();
    weblazem_save_customers_homepage_options();
    weblazem_save_testimonials_homepage_options();
    weblazem_save_faq_homepage_options();
    weblazem_save_home_section_toggles();
    
    // ثبت زمان آخرین بروزرسانی
    update_option('weblazem_homepage_last_update', current_time('mysql'));
    
    // اضافه کردن یک نشانگر که نشان دهد تنظیمات با AJAX ذخیره شده‌اند
    update_option('weblazem_homepage_ajax_saved', time());
    
    // پاسخ موفقیت
    wp_send_json_success(array(
        'message' => 'تنظیمات با موفقیت ذخیره شدند.',
        'debug' => $debug_data,
        'timestamp' => current_time('mysql')
    ));
}
add_action('wp_ajax_weblazem_save_homepage_options', 'weblazem_save_homepage_options_ajax');

/**
 * تنظیم مقادیر پیش‌فرض برای چیدمان صفحه اصلی اگر وجود نداشته باشند
 */
function weblazem_set_default_homepage_options() {
    // بخش هیرو
    if (get_option('weblazem_hero_title') === false) {
        update_option('weblazem_hero_title', 'وب لازم برای کسب و کار شما');
    }
    
    if (get_option('weblazem_hero_subtitle') === false) {
        update_option('weblazem_hero_subtitle', 'راهکارهای دیجیتال مارکتینگ');
    }
    
    if (get_option('weblazem_hero_text') === false) {
        update_option('weblazem_hero_text', 'لورم ایپسوم متن ساختگی با تولید سادگی نامفهوم از صنعت چاپ و با استفاده از طراحان گرافیک است. چاپگرها و متون بلکه روزنامه و مجله در ستون و سطرآنچنان که لازم است.');
    }
    
    if (get_option('weblazem_hero_button_text') === false) {
        update_option('weblazem_hero_button_text', 'مشاوره رایگان');
    }
    
    if (get_option('weblazem_hero_button_url') === false) {
        update_option('weblazem_hero_button_url', '#');
    }
    
    // بخش خدمات
    if (get_option('weblazem_services_title') === false) {
        update_option('weblazem_services_title', 'خدمات ما');
    }
    
    if (get_option('weblazem_services_subtitle') === false) {
        update_option('weblazem_services_subtitle', 'خدمات تخصصی وب لازم برای کسب و کار شما');
    }
    
    // بخش برون‌سپاری
    if (get_option('weblazem_outsourcing_title') === false) {
        update_option('weblazem_outsourcing_title', 'برون‌سپاری پروژه‌ها');
    }
    
    if (get_option('weblazem_outsourcing_subtitle') === false) {
        update_option('weblazem_outsourcing_subtitle', 'با تیم متخصص ما پروژه‌های خود را به صورت برون‌سپاری انجام دهید');
    }
    
    if (get_option('weblazem_outsourcing_button_text') === false) {
        update_option('weblazem_outsourcing_button_text', 'مشاوره رایگان');
    }
    
    if (get_option('weblazem_outsourcing_button_url') === false) {
        update_option('weblazem_outsourcing_button_url', '#');
    }
    
    // کارت‌های خدمات
    if (get_option('weblazem_services_cards') === false) {
        $default_cards = array(
            array(
                'title' => 'طراحی سایت',
                'image' => '',
                'button_text' => 'مشاهده خدمات',
                'button_url' => '#',
            ),
            array(
                'title' => 'سئو و بهینه‌سازی',
                'image' => '',
                'button_text' => 'مشاهده خدمات',
                'button_url' => '#',
            ),
            array(
                'title' => 'دیجیتال مارکتینگ',
                'image' => '',
                'button_text' => 'مشاهده خدمات',
                'button_url' => '#',
            ),
        );
        
        update_option('weblazem_services_cards', $default_cards);
    }
}

/**
 * مدیریت درخواست ذخیره تنظیمات چیدمان صفحه اصلی (admin-post)
 */
function handle_save_weblazem_homepage_options() {
    // بررسی امنیت
    if (!isset($_POST['weblazem_homepage_nonce']) || !wp_verify_nonce($_POST['weblazem_homepage_nonce'], 'weblazem_homepage_options')) {
        wp_die('خطای امنیتی: نانس نامعتبر است.');
    }
    
    // بررسی سطح دسترسی
    if (!current_user_can('manage_options')) {
        wp_die('شما دسترسی لازم برای این عملیات را ندارید.');
    }
    
    // بررسی هیرو
    if (isset($_POST['weblazem_hero_title'])) {
        update_option('weblazem_hero_title', sanitize_text_field($_POST['weblazem_hero_title']));
    }
    
    if (isset($_POST['weblazem_hero_subtitle'])) {
        update_option('weblazem_hero_subtitle', sanitize_text_field($_POST['weblazem_hero_subtitle']));
    }
    
    if (isset($_POST['weblazem_hero_text'])) {
        update_option('weblazem_hero_text', wp_kses_post($_POST['weblazem_hero_text']));
    }
    
    if (isset($_POST['weblazem_hero_button_text'])) {
        update_option('weblazem_hero_button_text', sanitize_text_field($_POST['weblazem_hero_button_text']));
    }
    
    if (isset($_POST['weblazem_hero_button_url'])) {
        update_option('weblazem_hero_button_url', esc_url_raw($_POST['weblazem_hero_button_url']));
    }
    
    // آپلود تصویر هیرو
    if (!empty($_FILES['weblazem_hero_image_file']['name'])) {
        $hero_image = weblazem_handle_hero_image_upload(get_option('weblazem_hero_image'));
        update_option('weblazem_hero_image', $hero_image);
    }
    
    // بخش خدمات
    if (isset($_POST['weblazem_services_title'])) {
        update_option('weblazem_services_title', sanitize_text_field($_POST['weblazem_services_title']));
    }
    
    if (isset($_POST['weblazem_services_subtitle'])) {
        update_option('weblazem_services_subtitle', sanitize_text_field($_POST['weblazem_services_subtitle']));
    }
    
    // کارت‌های خدمات
    if (isset($_POST['weblazem_services_cards']) && is_array($_POST['weblazem_services_cards'])) {
        $services_cards = array();
        
        // پردازش هر کارت به صورت دستی
        foreach ($_POST['weblazem_services_cards'] as $index => $card) {
            if (empty($card['title'])) {
                continue; // کارت بدون عنوان را رد کن
            }
            
            // مدیریت URL دکمه (می‌تواند خالی باشد)
            $button_url = '';
            if (isset($card['button_url']) && !empty($card['button_url'])) {
                $button_url = esc_url_raw($card['button_url']);
            }
            
            $services_cards[] = array(
                'title' => sanitize_text_field($card['title']),
                'image' => isset($card['image']) ? esc_url_raw($card['image']) : '',
                'button_text' => isset($card['button_text']) ? sanitize_text_field($card['button_text']) : '',
                'button_url' => $button_url,
            );
        }
        
        update_option('weblazem_services_cards', $services_cards);
    } else {
        // آرایه خالی ذخیره کن اگر هیچ کارتی وجود نداشت
        update_option('weblazem_services_cards', array());
    }
    
    // بخش برون‌سپاری
    if (isset($_POST['weblazem_outsourcing_title'])) {
        update_option('weblazem_outsourcing_title', sanitize_text_field($_POST['weblazem_outsourcing_title']));
    }
    
    if (isset($_POST['weblazem_outsourcing_subtitle'])) {
        update_option('weblazem_outsourcing_subtitle', sanitize_text_field($_POST['weblazem_outsourcing_subtitle']));
    }
    
    if (isset($_POST['weblazem_outsourcing_button_text'])) {
        update_option('weblazem_outsourcing_button_text', sanitize_text_field($_POST['weblazem_outsourcing_button_text']));
    }
    
    if (isset($_POST['weblazem_outsourcing_button_url'])) {
        update_option('weblazem_outsourcing_button_url', esc_url_raw($_POST['weblazem_outsourcing_button_url']));
    }
    
    // آپلود تصویر پس‌زمینه برون‌سپاری
    if (!empty($_FILES['weblazem_outsourcing_background_file']['name'])) {
        $outsourcing_background = weblazem_handle_outsourcing_background_upload(get_option('weblazem_outsourcing_background'));
        update_option('weblazem_outsourcing_background', $outsourcing_background);
    }

    weblazem_save_portfolio_homepage_options();
    weblazem_save_about_team_homepage_options();
    weblazem_save_customers_homepage_options();
    weblazem_save_testimonials_homepage_options();
    weblazem_save_faq_homepage_options();
    weblazem_save_home_section_toggles();
    
    // ثبت زمان آخرین بروزرسانی
    update_option('weblazem_homepage_last_update', current_time('mysql'));
    
    // ریدایرکت به صفحه ویرایش با پیام موفقیت
    wp_redirect(add_query_arg(
        array(
            'page' => 'weblazem-homepage-options',
            'updated' => 'true'
        ),
        admin_url('admin.php')
    ));
    exit;
}
add_action('admin_post_save_weblazem_homepage_options', 'handle_save_weblazem_homepage_options');

/**
 * مدیریت آپلود تصویر پس‌زمینه بخش برون‌سپاری
 */
function weblazem_handle_outsourcing_background_upload($option) {
    if(!empty($_FILES["weblazem_outsourcing_background_file"]["tmp_name"])) {
        $urls = wp_handle_upload($_FILES["weblazem_outsourcing_background_file"], array('test_form' => false));
        if(isset($urls["error"])) {
            // ثبت خطای آپلود
            error_log('خطا در آپلود تصویر پس‌زمینه برون‌سپاری: ' . $urls["error"]);
            add_settings_error(
                'weblazem_outsourcing_background',
                'outsourcing_background_error',
                'خطا در آپلود تصویر: ' . $urls["error"],
                'error'
            );
            return $option;
        }
        
        // آپلود موفق
        return $urls["url"];
    }
    return $option;
}