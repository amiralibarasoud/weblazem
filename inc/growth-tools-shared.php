<?php
/**
 * Shared helpers for growth tools feature pages.
 */

function weblazem_growth_client_ip() {
    return isset($_SERVER['REMOTE_ADDR']) ? sanitize_text_field(wp_unslash($_SERVER['REMOTE_ADDR'])) : '0.0.0.0';
}

function weblazem_growth_rate_limit($bucket, $limit = 12, $window = 600) {
    $key   = 'weblazem_growth_rl_' . md5($bucket . '|' . weblazem_growth_client_ip());
    $count = (int) get_transient($key);

    if ($count >= $limit) {
        return new WP_Error('rate_limit', 'تعداد درخواست‌ها زیاد است. کمی بعد دوباره تلاش کنید.');
    }

    set_transient($key, $count + 1, $window);
    return true;
}

function weblazem_growth_ensure_page($args) {
    $slug     = $args['slug'];
    $template = $args['template'];
    $title    = $args['title'];
    $option   = $args['option'];

    $page_id = (int) get_option($option, 0);
    if ($page_id && get_post_status($page_id) === 'publish') {
        return $page_id;
    }

    $page = get_page_by_path($slug);
    if ($page && $page->post_status === 'publish') {
        update_post_meta((int) $page->ID, '_wp_page_template', $template);
        update_option($option, (int) $page->ID);
        return (int) $page->ID;
    }

    $new_id = wp_insert_post(
        array(
            'post_title'   => $title,
            'post_name'    => $slug,
            'post_status'  => 'publish',
            'post_type'    => 'page',
            'post_content' => '',
        ),
        true
    );

    if (is_wp_error($new_id) || !$new_id) {
        return 0;
    }

    update_post_meta((int) $new_id, '_wp_page_template', $template);
    update_option($option, (int) $new_id);
    flush_rewrite_rules(false);

    return (int) $new_id;
}

function weblazem_growth_get_page_id($option, $slug) {
    $page_id = (int) get_option($option, 0);
    if ($page_id && get_post_status($page_id) === 'publish') {
        return $page_id;
    }

    $page = get_page_by_path($slug);
    if ($page && $page->post_status === 'publish') {
        update_option($option, (int) $page->ID);
        return (int) $page->ID;
    }

    return 0;
}

function weblazem_growth_get_page_url($option, $slug) {
    $page_id = weblazem_growth_get_page_id($option, $slug);
    if ($page_id) {
        $url = get_permalink($page_id);
        if ($url) {
            return $url;
        }
    }

    return home_url('/' . $slug . '/');
}

function weblazem_growth_is_page($template, $option, $slug) {
    if (is_page_template($template)) {
        return true;
    }

    $page_id = weblazem_growth_get_page_id($option, $slug);
    return $page_id && is_page($page_id);
}

function weblazem_growth_format_toman($amount) {
    $amount = (int) $amount;
    return number_format_i18n($amount) . ' تومان';
}

function weblazem_growth_tools_list() {
    return array(
        'price' => array(
            'title' => 'محاسبه‌گر قیمت',
            'desc'  => 'برآورد هزینه طراحی سایت بر اساس نیاز شما',
            'icon'  => 'fa-calculator',
            'url'   => function_exists('weblazem_get_price_estimator_page_url') ? weblazem_get_price_estimator_page_url() : home_url('/mohasebe-gheymat/'),
            'color' => '#1d4ed8',
        ),
        'demo' => array(
            'title' => 'دموی زنده',
            'desc'  => 'مشاهده نمونه‌کارها داخل فریم دسکتاپ و موبایل',
            'icon'  => 'fa-desktop',
            'url'   => function_exists('weblazem_get_live_demo_page_url') ? weblazem_get_live_demo_page_url() : home_url('/demo-zende/'),
            'color' => '#4338ca',
        ),
        'case' => array(
            'title' => 'داستان موفقیت',
            'desc'  => 'چالش، رویکرد و نتایج عددی پروژه‌ها',
            'icon'  => 'fa-chart-line',
            'url'   => function_exists('weblazem_get_case_study_page_url') ? weblazem_get_case_study_page_url() : home_url('/keis-astadi/'),
            'color' => '#7c3aed',
        ),
        'plans' => array(
            'title' => 'مقایسه پلن‌ها',
            'desc'  => 'فیلتر و مقایسه تعاملی تعرفه‌های طراحی سایت',
            'icon'  => 'fa-table-columns',
            'url'   => function_exists('weblazem_get_plan_comparator_page_url') ? weblazem_get_plan_comparator_page_url() : home_url('/moghayese-plan/'),
            'color' => '#0369a1',
        ),
        'referral' => array(
            'title' => 'باشگاه معرفی',
            'desc'  => 'معرفی دوستان و دریافت اعتبار و تخفیف',
            'icon'  => 'fa-handshake',
            'url'   => function_exists('weblazem_get_referral_page_url') ? weblazem_get_referral_page_url() : home_url('/bashgah-moarefi/'),
            'color' => '#c2410c',
        ),
        'resources' => array(
            'title' => 'مرکز منابع',
            'desc'  => 'چک‌لیست‌ها و راهنماهای قابل دانلود',
            'icon'  => 'fa-book-open',
            'url'   => function_exists('weblazem_get_resources_hub_page_url') ? weblazem_get_resources_hub_page_url() : home_url('/markaz-manabe/'),
            'color' => '#0e7490',
        ),
        'csat' => array(
            'title' => 'نظرسنجی رضایت',
            'desc'  => 'امتیاز واقعی مشتریان پس از تحویل پروژه',
            'icon'  => 'fa-star-half-stroke',
            'url'   => function_exists('weblazem_get_csat_page_url') ? weblazem_get_csat_page_url() : home_url('/nazar-sanji/'),
            'color' => '#a16207',
        ),
        'schedule' => array(
            'title' => 'رزرو مشاوره',
            'desc'  => 'انتخاب روز و ساعت جلسه مشاوره',
            'icon'  => 'fa-calendar-check',
            'url'   => function_exists('weblazem_get_scheduling_page_url') ? weblazem_get_scheduling_page_url() : home_url('/rezerve-moshavere/'),
            'color' => '#0f766e',
        ),
        'status' => array(
            'title' => 'وضعیت پروژه',
            'desc'  => 'پیگیری مراحل پروژه برای مشتریان',
            'icon'  => 'fa-tasks',
            'url'   => function_exists('weblazem_get_project_status_page_url') ? weblazem_get_project_status_page_url() : home_url('/vaziat-proje/'),
            'color' => '#b45309',
        ),
        'start' => array(
            'title' => 'شروع پروژه',
            'desc'  => 'ارسال بریف چندمرحله‌ای پروژه جدید',
            'icon'  => 'fa-rocket',
            'url'   => function_exists('weblazem_get_start_project_page_url') ? weblazem_get_start_project_page_url() : home_url('/shoro-proje/'),
            'color' => '#be123c',
        ),
    );
}

// Feature modules (loaded when this shared file is required).
$weblazem_growth_feature_files = array(
    'price-estimator.php',
    'case-study.php',
    'scheduling.php',
    'project-status.php',
    'start-project.php',
    'live-demo.php',
    'referral.php',
    'plan-comparator.php',
    'resources-hub.php',
    'csat.php',
    'leads-crm.php',
    'proposal.php',
);
foreach ($weblazem_growth_feature_files as $weblazem_growth_feature_file) {
    $weblazem_growth_feature_path = get_template_directory() . '/inc/' . $weblazem_growth_feature_file;
    if (file_exists($weblazem_growth_feature_path)) {
        require_once $weblazem_growth_feature_path;
    }
}
unset($weblazem_growth_feature_files, $weblazem_growth_feature_file, $weblazem_growth_feature_path);
