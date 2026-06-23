<?php
/**
 * Seed demo portfolio items (طراحی سایت فروشگاهی × 8).
 */

function weblazem_get_portfolio_demo_slugs() {
    return array(
        'demo-tarahi-site-foroushgahee-1',
        'demo-tarahi-site-foroushgahee-2',
        'demo-tarahi-site-foroushgahee-3',
        'demo-tarahi-site-foroushgahee-4',
        'demo-tarahi-site-foroushgahee-5',
        'demo-tarahi-site-foroushgahee-6',
        'demo-tarahi-site-foroushgahee-7',
        'demo-tarahi-site-foroushgahee-8',
    );
}

function weblazem_get_portfolio_archive_url() {
    if (function_exists('weblazem_get_portfolio_page_url')) {
        return weblazem_get_portfolio_page_url();
    }

    $url = get_post_type_archive_link('portfolio');

    if (!$url) {
        $url = home_url('/namune-kar/');
    }

    return $url;
}

/**
 * Create a soft placeholder image similar to the mockup (laptop/desk tones).
 *
 * @return int Attachment ID or 0.
 */
function weblazem_create_portfolio_demo_attachment() {
    $existing_id = (int) get_option('weblazem_portfolio_demo_attachment_id', 0);
    if ($existing_id && get_post($existing_id)) {
        return $existing_id;
    }

    foreach (array('portfolio-demo.jpg', 'portfolio-demo.png') as $demo_file) {
        $theme_image = get_template_directory() . '/assets/images/' . $demo_file;
        if (file_exists($theme_image)) {
            return weblazem_import_image_from_path($theme_image, $demo_file);
        }
    }

    if (!function_exists('imagecreatetruecolor')) {
        return 0;
    }

    $width  = 900;
    $height = 560;
    $image  = imagecreatetruecolor($width, $height);

    $bg_top    = imagecolorallocate($image, 243, 234, 253);
    $bg_bottom = imagecolorallocate($image, 232, 223, 240);
    $desk      = imagecolorallocate($image, 196, 181, 168);
    $screen    = imagecolorallocate($image, 45, 35, 58);
    $highlight = imagecolorallocate($image, 120, 95, 145);

    imagefilledrectangle($image, 0, 0, $width, $height, $bg_top);
    imagefilledrectangle($image, 0, (int) ($height * 0.45), $width, $height, $bg_bottom);
    imagefilledrectangle($image, 0, (int) ($height * 0.72), $width, $height, $desk);

    // Laptop base
    imagefilledrectangle($image, 250, 300, 650, 430, $screen);
    imagefilledrectangle($image, 220, 420, 680, 455, $highlight);
    imagefilledrectangle($image, 290, 320, 610, 390, $bg_top);

    $tmp = wp_tempnam('portfolio-demo.jpg');
    if (!$tmp) {
        imagedestroy($image);
        return 0;
    }

    imagejpeg($image, $tmp, 88);
    imagedestroy($image);

    $attachment_id = weblazem_import_image_from_path($tmp, 'portfolio-demo.jpg');

    if (file_exists($tmp)) {
        @unlink($tmp);
    }

    return $attachment_id;
}

/**
 * @return int Attachment ID or 0.
 */
function weblazem_import_image_from_path($file_path, $filename = '') {
    if (!file_exists($file_path)) {
        return 0;
    }

    if (!function_exists('wp_upload_bits')) {
        return 0;
    }

    $filename = $filename ? $filename : basename($file_path);
    $contents = file_get_contents($file_path);

    if ($contents === false) {
        return 0;
    }

    $upload = wp_upload_bits($filename, null, $contents);

    if (!empty($upload['error'])) {
        return 0;
    }

    $filetype = wp_check_filetype($upload['file']);
    $attachment = array(
        'post_mime_type' => $filetype['type'] ? $filetype['type'] : 'image/jpeg',
        'post_title'     => sanitize_file_name(pathinfo($filename, PATHINFO_FILENAME)),
        'post_content'   => '',
        'post_status'    => 'inherit',
    );

    $attachment_id = wp_insert_attachment($attachment, $upload['file']);

    if (is_wp_error($attachment_id) || !$attachment_id) {
        return 0;
    }

    require_once ABSPATH . 'wp-admin/includes/image.php';

    $metadata = wp_generate_attachment_metadata($attachment_id, $upload['file']);
    wp_update_attachment_metadata($attachment_id, $metadata);

    update_option('weblazem_portfolio_demo_attachment_id', (int) $attachment_id);

    return (int) $attachment_id;
}

function weblazem_portfolio_demo_missing() {
    foreach (weblazem_get_portfolio_demo_slugs() as $slug) {
        if (!get_page_by_path($slug, OBJECT, 'portfolio')) {
            return true;
        }
    }

    return false;
}

function weblazem_seed_portfolio_demo() {
    $demo_slugs = weblazem_get_portfolio_demo_slugs();
    $created_any = false;

    foreach ($demo_slugs as $slug) {
        if (get_page_by_path($slug, OBJECT, 'portfolio')) {
            continue;
        }

        $post_id = wp_insert_post(array(
            'post_type'    => 'portfolio',
            'post_status'  => 'publish',
            'post_title'   => 'طراحی سایت فروشگاهی',
            'post_name'    => $slug,
            'post_content' => '<p>نمونه کار دمو — طراحی و پیاده‌سازی فروشگاه آنلاین با تجربه کاربری مدرن، سبد خرید، درگاه پرداخت و پنل مدیریت اختصاصی برای وب‌لازم.</p>',
            'post_excerpt' => 'طراحی و توسعه فروشگاه اینترنتی حرفه‌ای با UI مدرن و بهینه‌سازی برای فروش.',
        ), true);

        if (is_wp_error($post_id) || !$post_id) {
            continue;
        }

        update_post_meta($post_id, '_weblazem_portfolio_subtitle', 'طراحی سایت فروشگاهی');
        update_post_meta($post_id, '_weblazem_portfolio_client', 'وب‌لازم (دمو)');

        if (function_exists('weblazem_assign_demo_portfolio_categories')) {
            $index = array_search($slug, $demo_slugs, true);
            $term  = ($index !== false && $index >= 4) ? 'site-sherkati' : 'foroushgahee-interneti';
            wp_set_object_terms($post_id, $term, 'portfolio_category', false);
        }

        $created_any = true;
    }

    $attachment_id = weblazem_create_portfolio_demo_attachment();

    if ($attachment_id) {
        foreach ($demo_slugs as $slug) {
            $post = get_page_by_path($slug, OBJECT, 'portfolio');
            if ($post && !has_post_thumbnail($post->ID)) {
                set_post_thumbnail($post->ID, $attachment_id);
            }
        }
    }

    if ($created_any) {
        flush_rewrite_rules(false);
    }

    update_option('weblazem_portfolio_demo_seeded', 1);
}

function weblazem_attach_demo_thumbnails() {
    $attachment_id = weblazem_create_portfolio_demo_attachment();

    if (!$attachment_id) {
        return;
    }

    foreach (weblazem_get_portfolio_demo_slugs() as $slug) {
        $post = get_page_by_path($slug, OBJECT, 'portfolio');

        if ($post && !has_post_thumbnail($post->ID)) {
            set_post_thumbnail($post->ID, $attachment_id);
        }
    }
}

function weblazem_maybe_seed_portfolio_demo() {
    if (!post_type_exists('portfolio')) {
        return;
    }

    if (weblazem_portfolio_demo_missing()) {
        weblazem_seed_portfolio_demo();
        return;
    }

    weblazem_attach_demo_thumbnails();
}
add_action('init', 'weblazem_maybe_seed_portfolio_demo', 25);

/**
 * Allow re-seeding demo from admin (Tools) if needed.
 */
function weblazem_reset_portfolio_demo() {
    if (!current_user_can('manage_options')) {
        return;
    }

    if (!isset($_GET['weblazem_reset_portfolio_demo']) || !isset($_GET['_wpnonce'])) {
        return;
    }

    if (!wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['_wpnonce'])), 'weblazem_reset_portfolio_demo')) {
        return;
    }

    foreach (weblazem_get_portfolio_demo_slugs() as $slug) {
        $post = get_page_by_path($slug, OBJECT, 'portfolio');
        if ($post) {
            wp_delete_post($post->ID, true);
        }
    }

    delete_option('weblazem_portfolio_demo_seeded');
    delete_option('weblazem_portfolio_demo_attachment_id');

    weblazem_seed_portfolio_demo();

    wp_safe_redirect(remove_query_arg(array('weblazem_reset_portfolio_demo', '_wpnonce')));
    exit;
}
add_action('admin_init', 'weblazem_reset_portfolio_demo');
