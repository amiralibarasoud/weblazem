<?php
/**
 * Start project brief — CPT, options, page, AJAX, admin list, enqueue.
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('weblazem_growth_ensure_page')) {
    require_once get_template_directory() . '/inc/growth-tools-shared.php';
}

define('WEBLAZEM_START_PROJECT_SLUG', 'shoro-proje');
define('WEBLAZEM_START_PROJECT_TEMPLATE', 'start-project-template.php');
define('WEBLAZEM_START_PROJECT_OPTION', 'weblazem_start_project_page_id');

function weblazem_start_project_default_types() {
    return array(
        'corporate'   => 'سایت شرکتی / معرفی',
        'ecommerce'   => 'فروشگاه اینترنتی',
        'landing'     => 'لندینگ پیج',
        'redesign'    => 'بازطراحی سایت فعلی',
        'webapp'      => 'وب‌اپلیکیشن سفارشی',
        'other'       => 'سایر',
    );
}

function weblazem_start_project_default_budgets() {
    return array(
        'under_20'  => 'کمتر از ۲۰ میلیون تومان',
        '20_40'     => '۲۰ تا ۴۰ میلیون تومان',
        '40_80'     => '۴۰ تا ۸۰ میلیون تومان',
        '80_150'    => '۸۰ تا ۱۵۰ میلیون تومان',
        'over_150'  => 'بیش از ۱۵۰ میلیون تومان',
        'undecided' => 'هنوز مشخص نیست',
    );
}

function weblazem_start_project_defaults() {
    return array(
        'title'            => 'شروع پروژه طراحی سایت',
        'subtitle'         => 'در چند مرحله نیازتان را بگویید تا تیم وب‌لازم بریف اولیه را آماده کند.',
        'success_message'  => 'بریف شما ثبت شد. کارشناسان وب‌لازم به‌زودی با شما تماس می‌گیرند.',
        'step_labels'      => array(
            'هدف و نوع پروژه',
            'رقبا و نمونه‌ها',
            'محتوا و صفحات',
            'بودجه و زمان',
            'اطلاعات تماس',
        ),
        'budget_ranges'    => weblazem_start_project_default_budgets(),
        'project_types'    => weblazem_start_project_default_types(),
    );
}

function weblazem_get_start_project_options() {
    $defaults = weblazem_start_project_defaults();
    $saved    = get_option('weblazem_start_project_options', array());
    if (!is_array($saved)) {
        $saved = array();
    }

    $opts = array_merge($defaults, $saved);

    if (empty($opts['budget_ranges']) || !is_array($opts['budget_ranges'])) {
        $opts['budget_ranges'] = $defaults['budget_ranges'];
    }
    if (empty($opts['project_types']) || !is_array($opts['project_types'])) {
        $opts['project_types'] = $defaults['project_types'];
    }
    if (empty($opts['step_labels']) || !is_array($opts['step_labels'])) {
        $opts['step_labels'] = $defaults['step_labels'];
    }

    return $opts;
}

function weblazem_ensure_start_project_defaults() {
    if (get_option('weblazem_start_project_options', false) === false) {
        update_option('weblazem_start_project_options', weblazem_start_project_defaults());
    }
}
add_action('init', 'weblazem_ensure_start_project_defaults', 12);

function weblazem_get_start_project_page_id() {
    return weblazem_growth_get_page_id(WEBLAZEM_START_PROJECT_OPTION, WEBLAZEM_START_PROJECT_SLUG);
}

function weblazem_get_start_project_page_url() {
    return weblazem_growth_get_page_url(WEBLAZEM_START_PROJECT_OPTION, WEBLAZEM_START_PROJECT_SLUG);
}

function weblazem_is_start_project_page() {
    return weblazem_growth_is_page(WEBLAZEM_START_PROJECT_TEMPLATE, WEBLAZEM_START_PROJECT_OPTION, WEBLAZEM_START_PROJECT_SLUG);
}

function weblazem_ensure_start_project_page() {
    weblazem_growth_ensure_page(
        array(
            'slug'     => WEBLAZEM_START_PROJECT_SLUG,
            'template' => WEBLAZEM_START_PROJECT_TEMPLATE,
            'title'    => 'شروع پروژه',
            'option'   => WEBLAZEM_START_PROJECT_OPTION,
        )
    );
}
add_action('init', 'weblazem_ensure_start_project_page', 38);

function weblazem_register_project_brief_cpt() {
    register_post_type(
        'project_brief',
        array(
            'labels' => array(
                'name'          => 'بریف‌های پروژه',
                'singular_name' => 'بریف پروژه',
                'menu_name'     => 'شروع پروژه',
                'add_new_item'  => 'افزودن بریف',
                'edit_item'     => 'ویرایش بریف',
                'search_items'  => 'جستجوی بریف',
                'not_found'     => 'بریفی یافت نشد.',
            ),
            'public'             => false,
            'publicly_queryable' => false,
            'show_ui'            => true,
            'show_in_menu'       => false,
            'show_in_rest'       => false,
            'capability_type'    => 'post',
            'map_meta_cap'       => true,
            'supports'           => array('title'),
            'has_archive'        => false,
        )
    );
}
add_action('init', 'weblazem_register_project_brief_cpt');

function weblazem_start_project_normalize_mobile($phone) {
    if (function_exists('weblazem_normalize_iran_mobile')) {
        return weblazem_normalize_iran_mobile($phone);
    }
    if (function_exists('weblazem_ticket_normalize_mobile')) {
        return weblazem_ticket_normalize_mobile($phone);
    }
    $digits = preg_replace('/\D+/', '', (string) $phone);
    if (strpos($digits, '98') === 0 && strlen($digits) >= 12) {
        $digits = '0' . substr($digits, 2);
    }
    if (preg_match('/^9\d{9}$/', $digits)) {
        $digits = '0' . $digits;
    }
    return $digits;
}

function weblazem_start_project_is_valid_mobile($phone) {
    if (function_exists('weblazem_is_valid_iran_mobile')) {
        return weblazem_is_valid_iran_mobile($phone);
    }
    return (bool) preg_match('/^09\d{9}$/', weblazem_start_project_normalize_mobile($phone));
}

function weblazem_start_project_parse_budget_textarea($raw) {
    $lines = preg_split('/\r\n|\r|\n/', (string) $raw);
    $out   = array();
    foreach ($lines as $i => $line) {
        $line = trim($line);
        if ($line === '') {
            continue;
        }
        if (strpos($line, '|') !== false) {
            list($key, $label) = array_map('trim', explode('|', $line, 2));
            $key = sanitize_key($key);
            if ($key === '') {
                $key = 'budget_' . ($i + 1);
            }
            $out[$key] = sanitize_text_field($label);
        } else {
            $out['budget_' . ($i + 1)] = sanitize_text_field($line);
        }
    }
    return $out;
}

function weblazem_start_project_budget_to_textarea($ranges) {
    if (!is_array($ranges)) {
        return '';
    }
    $lines = array();
    foreach ($ranges as $key => $label) {
        $lines[] = $key . '|' . $label;
    }
    return implode("\n", $lines);
}

function weblazem_start_project_admin_menu() {
    add_submenu_page(
        'weblazem-theme-options',
        'شروع پروژه',
        'شروع پروژه',
        'manage_options',
        'weblazem-start-project-options',
        'weblazem_start_project_admin_page'
    );
}
add_action('admin_menu', 'weblazem_start_project_admin_menu', 38);

function weblazem_start_project_admin_page() {
    if (!current_user_can('manage_options')) {
        return;
    }

    $tab  = isset($_GET['tab']) ? sanitize_key($_GET['tab']) : 'settings';
    $base = admin_url('admin.php?page=weblazem-start-project-options');

    if ($tab === 'settings' && isset($_POST['weblazem_sp_nonce']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['weblazem_sp_nonce'])), 'weblazem_sp_save')) {
        $step_raw = isset($_POST['step_labels']) ? sanitize_textarea_field(wp_unslash($_POST['step_labels'])) : '';
        $steps    = array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $step_raw))));
        if (count($steps) < 5) {
            $steps = weblazem_start_project_defaults()['step_labels'];
        }

        $budget_raw = isset($_POST['budget_ranges']) ? wp_unslash($_POST['budget_ranges']) : '';
        $budgets    = weblazem_start_project_parse_budget_textarea($budget_raw);
        if (empty($budgets)) {
            $budgets = weblazem_start_project_default_budgets();
        }

        $opts = array(
            'title'           => isset($_POST['title']) ? sanitize_text_field(wp_unslash($_POST['title'])) : '',
            'subtitle'        => isset($_POST['subtitle']) ? sanitize_textarea_field(wp_unslash($_POST['subtitle'])) : '',
            'success_message' => isset($_POST['success_message']) ? sanitize_textarea_field(wp_unslash($_POST['success_message'])) : '',
            'step_labels'     => $steps,
            'budget_ranges'   => $budgets,
            'project_types'   => weblazem_start_project_default_types(),
        );
        update_option('weblazem_start_project_options', $opts);
        echo '<div class="notice notice-success is-dismissible"><p>تنظیمات ذخیره شد.</p></div>';
    }

    $opts     = weblazem_get_start_project_options();
    $page_url = weblazem_get_start_project_page_url();
    ?>
    <div class="wrap" dir="rtl">
        <h1>شروع پروژه</h1>
        <h2 class="nav-tab-wrapper">
            <a class="nav-tab <?php echo $tab === 'settings' ? 'nav-tab-active' : ''; ?>" href="<?php echo esc_url($base . '&tab=settings'); ?>">تنظیمات</a>
            <a class="nav-tab <?php echo $tab === 'briefs' ? 'nav-tab-active' : ''; ?>" href="<?php echo esc_url($base . '&tab=briefs'); ?>">بریف‌ها</a>
        </h2>

        <?php if ($tab === 'briefs') : ?>
            <?php weblazem_start_project_admin_briefs_table(); ?>
        <?php else : ?>
            <?php if ($page_url) : ?>
                <p>صفحه فرم: <a href="<?php echo esc_url($page_url); ?>" target="_blank" rel="noopener"><?php echo esc_html($page_url); ?></a></p>
            <?php endif; ?>
            <form method="post">
                <?php wp_nonce_field('weblazem_sp_save', 'weblazem_sp_nonce'); ?>
                <table class="form-table">
                    <tr>
                        <th>عنوان</th>
                        <td><input type="text" name="title" class="large-text" value="<?php echo esc_attr($opts['title']); ?>" /></td>
                    </tr>
                    <tr>
                        <th>توضیح</th>
                        <td><textarea name="subtitle" class="large-text" rows="2"><?php echo esc_textarea($opts['subtitle']); ?></textarea></td>
                    </tr>
                    <tr>
                        <th>برچسب مراحل</th>
                        <td>
                            <textarea name="step_labels" class="large-text" rows="5"><?php echo esc_textarea(implode("\n", $opts['step_labels'])); ?></textarea>
                            <p class="description">هر خط یک مرحله (حداقل ۵ خط)</p>
                        </td>
                    </tr>
                    <tr>
                        <th>بازه‌های بودجه</th>
                        <td>
                            <textarea name="budget_ranges" class="large-text" rows="6" dir="ltr"><?php echo esc_textarea(weblazem_start_project_budget_to_textarea($opts['budget_ranges'])); ?></textarea>
                            <p class="description">هر خط: key|برچسب فارسی</p>
                        </td>
                    </tr>
                    <tr>
                        <th>پیام موفقیت</th>
                        <td><textarea name="success_message" class="large-text" rows="2"><?php echo esc_textarea($opts['success_message']); ?></textarea></td>
                    </tr>
                </table>
                <?php submit_button('ذخیره تنظیمات'); ?>
            </form>
        <?php endif; ?>
    </div>
    <?php
}

function weblazem_start_project_admin_briefs_table() {
    $opts  = weblazem_get_start_project_options();
    $types = $opts['project_types'];
    $buds  = $opts['budget_ranges'];

    $query = new WP_Query(
        array(
            'post_type'      => 'project_brief',
            'post_status'    => 'publish',
            'posts_per_page' => 100,
            'orderby'        => 'date',
            'order'          => 'DESC',
        )
    );
    ?>
    <table class="widefat striped" style="margin-top:16px;">
        <thead>
            <tr>
                <th>نام</th>
                <th>موبایل</th>
                <th>ایمیل</th>
                <th>نوع</th>
                <th>بودجه</th>
                <th>مهلت</th>
                <th>وضعیت</th>
                <th>تاریخ</th>
                <th>اقدام</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!$query->have_posts()) : ?>
                <tr><td colspan="9">بریفی ثبت نشده است.</td></tr>
            <?php else : ?>
                <?php while ($query->have_posts()) : $query->the_post(); ?>
                    <?php
                    $id     = get_the_ID();
                    $type   = get_post_meta($id, '_brief_project_type', true);
                    $bud    = get_post_meta($id, '_brief_budget', true);
                    $status = get_post_meta($id, '_brief_status', true) ?: 'new';
                    $status_labels = array(
                        'new'       => 'جدید',
                        'converted' => 'تبدیل‌شده به پروژه',
                        'closed'    => 'بسته',
                    );
                    ?>
                    <tr>
                        <td><?php echo esc_html(get_post_meta($id, '_brief_name', true)); ?></td>
                        <td dir="ltr"><?php echo esc_html(get_post_meta($id, '_brief_mobile', true)); ?></td>
                        <td dir="ltr"><?php echo esc_html(get_post_meta($id, '_brief_email', true)); ?></td>
                        <td><?php echo esc_html(isset($types[$type]) ? $types[$type] : $type); ?></td>
                        <td><?php echo esc_html(isset($buds[$bud]) ? $buds[$bud] : $bud); ?></td>
                        <td><?php echo esc_html(get_post_meta($id, '_brief_deadline', true)); ?></td>
                        <td><?php echo esc_html(isset($status_labels[$status]) ? $status_labels[$status] : $status); ?></td>
                        <td><?php echo esc_html(get_the_date('Y-m-d H:i')); ?></td>
                        <td style="white-space:nowrap;">
                            <a class="button button-small" href="<?php echo esc_url(get_edit_post_link($id)); ?>">مشاهده</a>
                            <?php
                            if (function_exists('weblazem_project_convert_button_html')) {
                                echo weblazem_project_convert_button_html('project_brief', $id);
                            }
                            ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php endif; ?>
        </tbody>
    </table>
    <?php
    wp_reset_postdata();
}

function weblazem_ajax_start_project_submit() {
    check_ajax_referer('weblazem_start_project', 'nonce');

    $rate = weblazem_growth_rate_limit('start_project_submit', 6, 20 * MINUTE_IN_SECONDS);
    if (is_wp_error($rate)) {
        wp_send_json_error(array('message' => $rate->get_error_message()), 429);
    }

    $opts  = weblazem_get_start_project_options();
    $types = $opts['project_types'];
    $buds  = $opts['budget_ranges'];

    $project_type = isset($_POST['project_type']) ? sanitize_key(wp_unslash($_POST['project_type'])) : '';
    $goal         = isset($_POST['goal']) ? sanitize_textarea_field(wp_unslash($_POST['goal'])) : '';
    $competitors  = isset($_POST['competitors']) ? sanitize_textarea_field(wp_unslash($_POST['competitors'])) : '';
    $content_ready = isset($_POST['content_ready']) ? sanitize_key(wp_unslash($_POST['content_ready'])) : '';
    $pages_needed = isset($_POST['pages_needed']) ? sanitize_textarea_field(wp_unslash($_POST['pages_needed'])) : '';
    $budget       = isset($_POST['budget']) ? sanitize_key(wp_unslash($_POST['budget'])) : '';
    $deadline     = isset($_POST['deadline']) ? sanitize_text_field(wp_unslash($_POST['deadline'])) : '';
    $name         = isset($_POST['name']) ? sanitize_text_field(wp_unslash($_POST['name'])) : '';
    $mobile       = isset($_POST['mobile']) ? sanitize_text_field(wp_unslash($_POST['mobile'])) : '';
    $email        = isset($_POST['email']) ? sanitize_email(wp_unslash($_POST['email'])) : '';

    if ($name === '' || $mobile === '' || $project_type === '' || $goal === '') {
        wp_send_json_error(array('message' => 'نام، موبایل، نوع پروژه و هدف الزامی است.'), 400);
    }

    if (!isset($types[$project_type])) {
        wp_send_json_error(array('message' => 'نوع پروژه نامعتبر است.'), 400);
    }

    if ($budget !== '' && !isset($buds[$budget])) {
        wp_send_json_error(array('message' => 'بازه بودجه نامعتبر است.'), 400);
    }

    if (!weblazem_start_project_is_valid_mobile($mobile)) {
        wp_send_json_error(array('message' => 'شماره موبایل معتبر نیست. مثال: 09121234567'), 400);
    }

    if ($email !== '' && !is_email($email)) {
        wp_send_json_error(array('message' => 'ایمیل معتبر نیست.'), 400);
    }

    $allowed_content = array('ready', 'partial', 'none');
    if ($content_ready !== '' && !in_array($content_ready, $allowed_content, true)) {
        $content_ready = '';
    }

    $mobile = weblazem_start_project_normalize_mobile($mobile);

    $post_id = wp_insert_post(
        array(
            'post_type'   => 'project_brief',
            'post_status' => 'publish',
            'post_title'  => $name . ' — ' . (isset($types[$project_type]) ? $types[$project_type] : $project_type),
        ),
        true
    );

    if (is_wp_error($post_id) || !$post_id) {
        wp_send_json_error(array('message' => 'خطا در ثبت بریف. دوباره تلاش کنید.'), 500);
    }

    update_post_meta($post_id, '_brief_project_type', $project_type);
    update_post_meta($post_id, '_brief_goal', $goal);
    update_post_meta($post_id, '_brief_competitors', $competitors);
    update_post_meta($post_id, '_brief_content_ready', $content_ready);
    update_post_meta($post_id, '_brief_pages_needed', $pages_needed);
    update_post_meta($post_id, '_brief_budget', $budget);
    update_post_meta($post_id, '_brief_deadline', $deadline);
    update_post_meta($post_id, '_brief_name', $name);
    update_post_meta($post_id, '_brief_mobile', $mobile);
    update_post_meta($post_id, '_brief_email', $email);
    update_post_meta($post_id, '_brief_status', 'new');

    wp_send_json_success(
        array(
            'message' => $opts['success_message'],
            'briefId' => $post_id,
        )
    );
}
add_action('wp_ajax_weblazem_start_project_submit', 'weblazem_ajax_start_project_submit');
add_action('wp_ajax_nopriv_weblazem_start_project_submit', 'weblazem_ajax_start_project_submit');

function weblazem_enqueue_start_project_assets() {
    if (!weblazem_is_start_project_page()) {
        return;
    }

    $opts = weblazem_get_start_project_options();

    wp_enqueue_style(
        'weblazem-start-project',
        get_template_directory_uri() . '/assets/css/start-project.css',
        array(),
        '1.0.0'
    );

    wp_enqueue_script(
        'weblazem-start-project',
        get_template_directory_uri() . '/assets/js/start-project.js',
        array(),
        '1.0.0',
        true
    );

    wp_localize_script(
        'weblazem-start-project',
        'weblazemStartProject',
        array(
            'ajaxUrl'        => admin_url('admin-ajax.php'),
            'nonce'          => wp_create_nonce('weblazem_start_project'),
            'stepLabels'     => array_values($opts['step_labels']),
            'projectTypes'   => $opts['project_types'],
            'budgetRanges'   => $opts['budget_ranges'],
            'successMessage' => $opts['success_message'],
            'errorMessage'   => 'خطایی رخ داد. لطفاً دوباره تلاش کنید.',
        )
    );
}
add_action('wp_enqueue_scripts', 'weblazem_enqueue_start_project_assets', 30);
