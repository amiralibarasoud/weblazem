<?php
/**
 * Blog archive — page setup, pagination, demo posts.
 */

define('WEBLAZEM_BLOGARCHIVE_PAGE_SLUG', 'blog-websima');
define('WEBLAZEM_BLOGARCHIVE_PAGE_TEMPLATE', 'blog-archive-template.php');

function weblazem_get_blogarchive_page_id() {
    $page_id = (int) get_option('weblazem_blogarchive_page_id', 0);
    if ($page_id && get_post_status($page_id) === 'publish') {
        return $page_id;
    }
    $page = get_page_by_path(WEBLAZEM_BLOGARCHIVE_PAGE_SLUG);
    if ($page && $page->post_status === 'publish') {
        update_option('weblazem_blogarchive_page_id', (int) $page->ID);
        return (int) $page->ID;
    }
    return 0;
}

function weblazem_is_blogarchive_page() {
    if (is_page_template(WEBLAZEM_BLOGARCHIVE_PAGE_TEMPLATE)) {
        return true;
    }
    $page_id = weblazem_get_blogarchive_page_id();
    return $page_id && is_page($page_id);
}

function weblazem_get_blogarchive_page_url() {
    $page_id = weblazem_get_blogarchive_page_id();
    if ($page_id) {
        $url = get_permalink($page_id);
        if ($url) {
            return $url;
        }
    }
    return home_url('/' . WEBLAZEM_BLOGARCHIVE_PAGE_SLUG . '/');
}

function weblazem_get_blogarchive_paged() {
    $paged = (int) get_query_var('blog_page');
    if ($paged < 1 && isset($_GET['blog_page'])) {
        $paged = (int) $_GET['blog_page'];
    }
    if ($paged < 1) {
        $paged = max(1, (int) get_query_var('paged'), (int) get_query_var('page'));
    }
    return max(1, $paged);
}

function weblazem_blogarchive_query_vars($vars) {
    $vars[] = 'blog_page';
    return $vars;
}
add_filter('query_vars', 'weblazem_blogarchive_query_vars');

function weblazem_ensure_blogarchive_page() {
    if (weblazem_get_blogarchive_page_id()) {
        return;
    }
    $page_id = wp_insert_post(array(
        'post_title'   => 'مجله وب‌لازم',
        'post_name'    => WEBLAZEM_BLOGARCHIVE_PAGE_SLUG,
        'post_status'  => 'publish',
        'post_type'    => 'page',
        'post_content' => '',
    ), true);
    if (is_wp_error($page_id) || !$page_id) {
        return;
    }
    update_post_meta($page_id, '_wp_page_template', WEBLAZEM_BLOGARCHIVE_PAGE_TEMPLATE);
    update_option('weblazem_blogarchive_page_id', (int) $page_id);
    flush_rewrite_rules(false);
}
add_action('init', 'weblazem_ensure_blogarchive_page', 36);

function weblazem_blogarchive_posts_query_args() {
    $per_page = max(3, min(24, (int) weblazem_blogarchive_option('posts_per_page', 6)));
    return array(
        'post_type'      => 'post',
        'post_status'    => 'publish',
        'posts_per_page' => $per_page,
        'paged'          => weblazem_get_blogarchive_paged(),
        'orderby'        => 'date',
        'order'          => 'DESC',
    );
}

function weblazem_blogarchive_pagination($query) {
    if (!$query instanceof WP_Query || $query->max_num_pages <= 1) {
        return;
    }

    $paged    = weblazem_get_blogarchive_paged();
    $base_url = trailingslashit(weblazem_get_blogarchive_page_url());
    $last_label = weblazem_blogarchive_option('pagination_last_label', 'صفحه آخر');

    $links = paginate_links(array(
        'base'      => esc_url($base_url) . '%_%',
        'format'    => '?blog_page=%#%',
        'current'   => $paged,
        'total'     => (int) $query->max_num_pages,
        'mid_size'  => 1,
        'end_size'  => 1,
        'prev_next' => false,
        'type'      => 'plain',
    ));

    if (!$links) {
        return;
    }

    echo '<nav class="blog-archive-pagination" aria-label="صفحه‌بندی مقالات">';
    echo '<div class="blog-archive-pagination__inner">';
    echo '<div class="blog-archive-pagination__numbers">' . $links . '</div>';

    if ($paged < (int) $query->max_num_pages) {
        $last_url = add_query_arg('blog_page', (int) $query->max_num_pages, $base_url);
        echo '<a href="' . esc_url($last_url) . '" class="blog-archive-pagination__last">' . esc_html($last_label) . '</a>';
    }

    echo '</div></nav>';
}

function weblazem_get_blogarchive_demo_posts() {
    return array(
        array(
            'title'   => 'تفاوت وردپرس و برنامه‌نویسی اختصاصی؛ کدام برای کسب‌وکار شما مناسب‌تر است؟',
            'excerpt' => 'انتخاب بین وردپرس و توسعه اختصاصی یکی از مهم‌ترین تصمیم‌های فنی هر کسب‌وکار آنلاین است. در این مقاله معیارهای انتخاب را بررسی می‌کنیم.',
            'date'    => '2025-05-04',
            'image'   => 'blog-1.svg',
        ),
        array(
            'title'   => 'سئو یا تبلیغات گوگل؟ مقایسه عملی از تجربه پروژه‌های وب‌لازم',
            'excerpt' => 'هر دو کانال می‌توانند فروش را افزایش دهند، اما زمان، هزینه و پایداری نتایج متفاوت است. این راهنما به شما کمک می‌کند انتخاب درستی داشته باشید.',
            'date'    => '2025-05-06',
            'image'   => 'blog-2.svg',
        ),
        array(
            'title'   => 'برون‌سپاری سئو؛ چه زمانی به تیم متخصص نیاز دارید؟',
            'excerpt' => 'برون‌سپاری سئو وقتی ارزشمند است که به دنبال رشد سریع‌تر، تخصص عمیق‌تر و گزارش‌دهی شفاف هستید. در این مطلب نشانه‌های تصمیم‌گیری را مرور می‌کنیم.',
            'date'    => '2025-05-08',
            'image'   => 'blog-3.svg',
        ),
        array(
            'title'   => 'پرستاشاپ یا ووکامرس؟ تجربه وب‌لازم از راه‌اندازی ده‌ها فروشگاه اینترنتی',
            'excerpt' => 'مقایسه دو پلتفرم محبوب فروشگاه‌ساز از نظر هزینه، انعطاف، امنیت و مقیاس‌پذیری برای کسب‌وکارهای ایرانی.',
            'date'    => '2026-04-07',
            'image'   => 'blog-4.svg',
        ),
        array(
            'title'   => 'راهنمای کامل سئو خارجی (آپدیت 2026)؛ استراتژی‌های جدیدی که جواب می‌دهند!',
            'excerpt' => 'لینک‌سازی، برندسازی و سیگنال‌های اعتماد در سال 2026 چگونه تغییر کرده‌اند؟ این راهنما به‌روزترین رویکردها را معرفی می‌کند.',
            'date'    => '2026-04-09',
            'image'   => 'blog-5.svg',
        ),
        array(
            'title'   => '4 ترند سئو در سال 2026؛ پیش‌بینی وب‌لازم از SEO در سال جدید',
            'excerpt' => 'هوش مصنوعی، جستجوی صوتی و تجربه کاربری همچنان محور رقابت هستند. این چهار ترند را از همین امروز در استراتژی خود بگنجانید.',
            'date'    => '2026-04-11',
            'image'   => 'blog-6.svg',
        ),
        array(
            'title'   => 'چک‌لیست فنی قبل از راه‌اندازی سایت شرکتی',
            'excerpt' => 'سرعت، امنیت، ساختار URL و اسکیما مارکاپ از مواردی هستند که قبل از لانچ باید بررسی شوند تا بعداً هزینه اضافه نپردازید.',
            'date'    => '2026-03-15',
            'image'   => 'blog-7.svg',
        ),
        array(
            'title'   => 'چگونه KPIهای دیجیتال مارکتینگ را برای تیم خود تعریف کنیم؟',
            'excerpt' => 'بدون شاخص‌های درست، بهینه‌سازی تبلیغات و سئو غیرممکن است. در این مقاله چارچوب ساده‌ای برای تعریف KPI ارائه می‌دهیم.',
            'date'    => '2026-03-20',
            'image'   => 'blog-8.svg',
        ),
        array(
            'title'   => 'UX فروشگاه اینترنتی؛ 7 اصل که نرخ تبدیل را بالا می‌برد',
            'excerpt' => 'از صفحه محصول تا درگاه پرداخت، جزئیات کوچک تجربه خرید می‌توانند تفاوت بزرگی در فروش ایجاد کنند.',
            'date'    => '2026-03-25',
            'image'   => 'blog-9.svg',
        ),
    );
}

function weblazem_seed_blogarchive_demo_posts() {
    if (get_option('weblazem_blogarchive_demo_seeded') === '1') {
        return;
    }

    $existing = wp_count_posts('post');
    if ($existing && (int) $existing->publish > 0) {
        update_option('weblazem_blogarchive_demo_seeded', '1');
        return;
    }

    require_once ABSPATH . 'wp-admin/includes/file.php';

    $image_base = get_template_directory_uri() . '/assets/images/blog/';

    foreach (weblazem_get_blogarchive_demo_posts() as $item) {
        $post_id = wp_insert_post(array(
            'post_title'   => $item['title'],
            'post_excerpt' => $item['excerpt'],
            'post_content' => '<p>' . esc_html($item['excerpt']) . '</p>',
            'post_status'  => 'publish',
            'post_type'    => 'post',
            'post_date'    => $item['date'] . ' 10:00:00',
        ), true);

        if (is_wp_error($post_id) || !$post_id) {
            continue;
        }

        update_post_meta($post_id, '_weblazem_demo_thumb', $image_base . $item['image']);
    }

    update_option('weblazem_blogarchive_demo_seeded', '1');
}
add_action('init', 'weblazem_seed_blogarchive_demo_posts', 40);
