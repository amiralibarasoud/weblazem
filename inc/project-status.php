<?php
/**
 * Project status portal — CPT, options, page, AJAX, admin CRUD, enqueue.
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('weblazem_growth_ensure_page')) {
    require_once get_template_directory() . '/inc/growth-tools-shared.php';
}

define('WEBLAZEM_PROJECT_STATUS_SLUG', 'vaziat-proje');
define('WEBLAZEM_PROJECT_STATUS_TEMPLATE', 'project-status-template.php');
define('WEBLAZEM_PROJECT_STATUS_OPTION', 'weblazem_project_status_page_id');

function weblazem_project_status_default_stages() {
    return array(
        array('key' => 'briefing', 'label' => 'بریف و نیازسنجی', 'done' => false, 'date' => '', 'note' => ''),
        array('key' => 'design', 'label' => 'طراحی UI/UX', 'done' => false, 'date' => '', 'note' => ''),
        array('key' => 'development', 'label' => 'توسعه و پیاده‌سازی', 'done' => false, 'date' => '', 'note' => ''),
        array('key' => 'review', 'label' => 'بازبینی و تست', 'done' => false, 'date' => '', 'note' => ''),
        array('key' => 'delivery', 'label' => 'تحویل', 'done' => false, 'date' => '', 'note' => ''),
        array('key' => 'done', 'label' => 'اتمام پروژه', 'done' => false, 'date' => '', 'note' => ''),
    );
}

function weblazem_project_status_stage_labels() {
    return array(
        'briefing'     => 'بریف و نیازسنجی',
        'design'       => 'طراحی UI/UX',
        'development'  => 'توسعه و پیاده‌سازی',
        'review'       => 'بازبینی و تست',
        'delivery'     => 'تحویل',
        'done'         => 'اتمام پروژه',
    );
}

function weblazem_project_status_defaults() {
    return array(
        'title'       => 'وضعیت پروژه',
        'subtitle'    => 'پیشرفت پروژه طراحی سایت خود را آنلاین دنبال کنید.',
        'login_intro' => 'با شماره موبایل و کد ورود (همان کد سیستم تیکت) وارد شوید تا پروژه‌هایتان را ببینید.',
    );
}

function weblazem_get_project_status_options() {
    $defaults = weblazem_project_status_defaults();
    $saved    = get_option('weblazem_project_status_options', array());
    if (!is_array($saved)) {
        $saved = array();
    }
    return array_merge($defaults, $saved);
}

function weblazem_ensure_project_status_defaults() {
    if (get_option('weblazem_project_status_options', false) === false) {
        update_option('weblazem_project_status_options', weblazem_project_status_defaults());
    }
}
add_action('init', 'weblazem_ensure_project_status_defaults', 12);

function weblazem_get_project_status_page_id() {
    return weblazem_growth_get_page_id(WEBLAZEM_PROJECT_STATUS_OPTION, WEBLAZEM_PROJECT_STATUS_SLUG);
}

function weblazem_get_project_status_page_url() {
    return weblazem_growth_get_page_url(WEBLAZEM_PROJECT_STATUS_OPTION, WEBLAZEM_PROJECT_STATUS_SLUG);
}

function weblazem_is_project_status_page() {
    return weblazem_growth_is_page(WEBLAZEM_PROJECT_STATUS_TEMPLATE, WEBLAZEM_PROJECT_STATUS_OPTION, WEBLAZEM_PROJECT_STATUS_SLUG);
}

function weblazem_ensure_project_status_page() {
    weblazem_growth_ensure_page(
        array(
            'slug'     => WEBLAZEM_PROJECT_STATUS_SLUG,
            'template' => WEBLAZEM_PROJECT_STATUS_TEMPLATE,
            'title'    => 'وضعیت پروژه',
            'option'   => WEBLAZEM_PROJECT_STATUS_OPTION,
        )
    );
}
add_action('init', 'weblazem_ensure_project_status_page', 38);

function weblazem_register_client_project_cpt() {
    register_post_type(
        'client_project',
        array(
            'labels' => array(
                'name'          => 'پروژه‌های مشتری',
                'singular_name' => 'پروژه مشتری',
                'menu_name'     => 'وضعیت پروژه',
                'add_new'       => 'افزودن پروژه',
                'add_new_item'  => 'افزودن پروژه جدید',
                'edit_item'     => 'ویرایش پروژه',
                'search_items'  => 'جستجوی پروژه',
                'not_found'     => 'پروژه‌ای یافت نشد.',
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
add_action('init', 'weblazem_register_client_project_cpt');

function weblazem_project_status_normalize_mobile($phone) {
    if (function_exists('weblazem_ticket_normalize_mobile')) {
        return weblazem_ticket_normalize_mobile($phone);
    }
    if (function_exists('weblazem_normalize_iran_mobile')) {
        return weblazem_normalize_iran_mobile($phone);
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

function weblazem_project_status_is_valid_mobile($phone) {
    if (function_exists('weblazem_is_valid_iran_mobile')) {
        return weblazem_is_valid_iran_mobile($phone);
    }
    if (function_exists('weblazem_ticket_is_valid_mobile')) {
        return weblazem_ticket_is_valid_mobile($phone);
    }
    return (bool) preg_match('/^09\d{9}$/', weblazem_project_status_normalize_mobile($phone));
}

function weblazem_project_status_generate_code() {
    do {
        $code = 'PRJ-' . strtoupper(wp_generate_password(6, false, false));
        $exists = get_posts(
            array(
                'post_type'      => 'client_project',
                'post_status'    => 'any',
                'posts_per_page' => 1,
                'fields'         => 'ids',
                'meta_key'       => '_project_code',
                'meta_value'     => $code,
            )
        );
    } while (!empty($exists));

    return $code;
}

function weblazem_project_status_sanitize_stages($stages) {
    $defaults = weblazem_project_status_default_stages();
    if (!is_array($stages) || empty($stages)) {
        return $defaults;
    }

    $clean = array();
    foreach ($stages as $stage) {
        if (!is_array($stage)) {
            continue;
        }
        $key = isset($stage['key']) ? sanitize_key($stage['key']) : '';
        if ($key === '') {
            continue;
        }
        $clean[] = array(
            'key'   => $key,
            'label' => isset($stage['label']) ? sanitize_text_field($stage['label']) : $key,
            'done'  => !empty($stage['done']),
            'date'  => isset($stage['date']) ? sanitize_text_field($stage['date']) : '',
            'note'  => isset($stage['note']) ? sanitize_textarea_field($stage['note']) : '',
        );
    }

    return !empty($clean) ? $clean : $defaults;
}

function weblazem_project_status_sanitize_files($files) {
    if (!is_array($files)) {
        return array();
    }
    $clean = array();
    foreach ($files as $file) {
        if (!is_array($file)) {
            continue;
        }
        $title = isset($file['title']) ? sanitize_text_field($file['title']) : '';
        $url   = isset($file['url']) ? esc_url_raw($file['url']) : '';
        if ($title === '' && $url === '') {
            continue;
        }
        $clean[] = array(
            'title' => $title !== '' ? $title : 'فایل',
            'url'   => $url,
        );
    }
    return $clean;
}

function weblazem_format_client_project_for_api($post) {
    if (!$post || $post->post_type !== 'client_project') {
        return null;
    }

    $id       = (int) $post->ID;
    $stage    = get_post_meta($id, '_project_stage', true) ?: 'briefing';
    $labels   = weblazem_project_status_stage_labels();
    $stages   = get_post_meta($id, '_project_stages', true);
    $stages   = weblazem_project_status_sanitize_stages($stages);
    $files    = weblazem_project_status_sanitize_files(get_post_meta($id, '_project_files', true));
    $progress = max(0, min(100, (int) get_post_meta($id, '_project_progress', true)));

    return array(
        'id'          => $id,
        'title'       => get_the_title($id),
        'code'        => (string) get_post_meta($id, '_project_code', true),
        'clientName'  => (string) get_post_meta($id, '_project_client_name', true),
        'clientMobile'=> (string) get_post_meta($id, '_project_client_mobile', true),
        'progress'    => $progress,
        'stage'       => $stage,
        'stageLabel'  => isset($labels[$stage]) ? $labels[$stage] : $stage,
        'stages'      => $stages,
        'files'       => $files,
        'updatedAt'   => get_the_modified_date('Y-m-d H:i', $id),
    );
}

function weblazem_get_client_projects_by_mobile($mobile) {
    $mobile = weblazem_project_status_normalize_mobile($mobile);
    if ($mobile === '') {
        return array();
    }

    $query = new WP_Query(
        array(
            'post_type'      => 'client_project',
            'post_status'    => 'publish',
            'posts_per_page' => 50,
            'orderby'        => 'modified',
            'order'          => 'DESC',
            'meta_query'     => array(
                array(
                    'key'   => '_project_client_mobile',
                    'value' => $mobile,
                ),
            ),
        )
    );

    $items = array();
    foreach ($query->posts as $post) {
        $formatted = weblazem_format_client_project_for_api($post);
        if ($formatted) {
            $items[] = $formatted;
        }
    }
    wp_reset_postdata();

    return $items;
}

function weblazem_project_status_admin_menu() {
    add_submenu_page(
        'weblazem-theme-options',
        'وضعیت پروژه',
        'وضعیت پروژه',
        'manage_options',
        'weblazem-project-status-options',
        'weblazem_project_status_admin_page'
    );
}
add_action('admin_menu', 'weblazem_project_status_admin_menu', 37);

function weblazem_project_status_admin_page() {
    if (!current_user_can('manage_options')) {
        return;
    }

    $tab = isset($_GET['tab']) ? sanitize_key($_GET['tab']) : 'settings';
    $base = admin_url('admin.php?page=weblazem-project-status-options');

    if ($tab === 'settings' && isset($_POST['weblazem_ps_nonce']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['weblazem_ps_nonce'])), 'weblazem_ps_save')) {
        $opts = array(
            'title'       => isset($_POST['title']) ? sanitize_text_field(wp_unslash($_POST['title'])) : '',
            'subtitle'    => isset($_POST['subtitle']) ? sanitize_textarea_field(wp_unslash($_POST['subtitle'])) : '',
            'login_intro' => isset($_POST['login_intro']) ? sanitize_textarea_field(wp_unslash($_POST['login_intro'])) : '',
        );
        update_option('weblazem_project_status_options', $opts);
        echo '<div class="notice notice-success is-dismissible"><p>تنظیمات ذخیره شد.</p></div>';
    }

    if ($tab === 'projects' && isset($_POST['weblazem_ps_project_nonce']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['weblazem_ps_project_nonce'])), 'weblazem_ps_project_save')) {
        $result = weblazem_project_status_admin_save_project($_POST);
        if (is_wp_error($result)) {
            echo '<div class="notice notice-error"><p>' . esc_html($result->get_error_message()) . '</p></div>';
        } else {
            echo '<div class="notice notice-success is-dismissible"><p>پروژه ذخیره شد.</p></div>';
        }
    }

    $opts = weblazem_get_project_status_options();
    $page_url = weblazem_get_project_status_page_url();
    ?>
    <div class="wrap" dir="rtl">
        <h1>وضعیت پروژه</h1>
        <h2 class="nav-tab-wrapper">
            <a class="nav-tab <?php echo $tab === 'settings' ? 'nav-tab-active' : ''; ?>" href="<?php echo esc_url($base . '&tab=settings'); ?>">تنظیمات</a>
            <a class="nav-tab <?php echo $tab === 'projects' ? 'nav-tab-active' : ''; ?>" href="<?php echo esc_url($base . '&tab=projects'); ?>">پروژه‌ها</a>
        </h2>

        <?php if ($tab === 'projects') : ?>
            <?php weblazem_project_status_admin_projects_ui(); ?>
        <?php else : ?>
            <?php if ($page_url) : ?>
                <p>صفحه پورتال: <a href="<?php echo esc_url($page_url); ?>" target="_blank" rel="noopener"><?php echo esc_html($page_url); ?></a></p>
            <?php endif; ?>
            <p class="description">ورود کاربران با همان کد سندباکس تیکت (<code><?php echo esc_html(function_exists('weblazem_get_ticket_access_code') ? weblazem_get_ticket_access_code() : '12345'); ?></code>) انجام می‌شود.</p>
            <form method="post">
                <?php wp_nonce_field('weblazem_ps_save', 'weblazem_ps_nonce'); ?>
                <table class="form-table">
                    <tr>
                        <th>عنوان صفحه</th>
                        <td><input type="text" name="title" class="large-text" value="<?php echo esc_attr($opts['title']); ?>" /></td>
                    </tr>
                    <tr>
                        <th>توضیح</th>
                        <td><textarea name="subtitle" class="large-text" rows="2"><?php echo esc_textarea($opts['subtitle']); ?></textarea></td>
                    </tr>
                    <tr>
                        <th>متن ورود</th>
                        <td><textarea name="login_intro" class="large-text" rows="3"><?php echo esc_textarea($opts['login_intro']); ?></textarea></td>
                    </tr>
                </table>
                <?php submit_button('ذخیره تنظیمات'); ?>
            </form>
        <?php endif; ?>
    </div>
    <?php
}

function weblazem_project_status_admin_save_project($data) {
    $project_id = isset($data['project_id']) ? absint($data['project_id']) : 0;
    $title      = isset($data['project_title']) ? sanitize_text_field(wp_unslash($data['project_title'])) : '';
    $mobile     = isset($data['client_mobile']) ? weblazem_project_status_normalize_mobile(sanitize_text_field(wp_unslash($data['client_mobile']))) : '';
    $name       = isset($data['client_name']) ? sanitize_text_field(wp_unslash($data['client_name'])) : '';
    $progress   = isset($data['progress']) ? max(0, min(100, absint($data['progress']))) : 0;
    $stage      = isset($data['stage']) ? sanitize_key($data['stage']) : 'briefing';
    $labels     = weblazem_project_status_stage_labels();

    if ($title === '') {
        return new WP_Error('title', 'عنوان پروژه الزامی است.');
    }
    if (!weblazem_project_status_is_valid_mobile($mobile)) {
        return new WP_Error('mobile', 'موبایل مشتری معتبر نیست.');
    }
    if (!isset($labels[$stage])) {
        $stage = 'briefing';
    }

    $stages = weblazem_project_status_default_stages();
    if (isset($data['stage_done']) && is_array($data['stage_done'])) {
        $done_keys = array_map('sanitize_key', wp_unslash($data['stage_done']));
        foreach ($stages as &$s) {
            $s['done'] = in_array($s['key'], $done_keys, true);
            if (isset($data['stage_date'][$s['key']])) {
                $s['date'] = sanitize_text_field(wp_unslash($data['stage_date'][$s['key']]));
            }
            if (isset($data['stage_note'][$s['key']])) {
                $s['note'] = sanitize_textarea_field(wp_unslash($data['stage_note'][$s['key']]));
            }
        }
        unset($s);
    }

    $files = array();
    if (!empty($data['file_title']) && is_array($data['file_title'])) {
        $titles = wp_unslash($data['file_title']);
        $urls   = isset($data['file_url']) && is_array($data['file_url']) ? wp_unslash($data['file_url']) : array();
        foreach ($titles as $i => $ft) {
            $files[] = array(
                'title' => sanitize_text_field($ft),
                'url'   => isset($urls[$i]) ? esc_url_raw($urls[$i]) : '',
            );
        }
    }
    $files = weblazem_project_status_sanitize_files($files);

    if ($project_id) {
        $post = get_post($project_id);
        if (!$post || $post->post_type !== 'client_project') {
            return new WP_Error('missing', 'پروژه یافت نشد.');
        }
        wp_update_post(
            array(
                'ID'         => $project_id,
                'post_title' => $title,
            )
        );
        $code = get_post_meta($project_id, '_project_code', true);
        if ($code === '') {
            $code = weblazem_project_status_generate_code();
        }
    } else {
        $project_id = wp_insert_post(
            array(
                'post_type'   => 'client_project',
                'post_status' => 'publish',
                'post_title'  => $title,
            ),
            true
        );
        if (is_wp_error($project_id)) {
            return $project_id;
        }
        $code = weblazem_project_status_generate_code();
    }

    update_post_meta($project_id, '_project_client_mobile', $mobile);
    update_post_meta($project_id, '_project_client_name', $name);
    update_post_meta($project_id, '_project_code', $code);
    update_post_meta($project_id, '_project_progress', $progress);
    update_post_meta($project_id, '_project_stage', $stage);
    update_post_meta($project_id, '_project_stages', $stages);
    update_post_meta($project_id, '_project_files', $files);

    return $project_id;
}

function weblazem_project_status_admin_projects_ui() {
    $edit_id = isset($_GET['edit']) ? absint($_GET['edit']) : 0;
    $edit    = $edit_id ? get_post($edit_id) : null;
    if ($edit && $edit->post_type !== 'client_project') {
        $edit = null;
        $edit_id = 0;
    }

    $labels  = weblazem_project_status_stage_labels();
    $stages  = $edit ? weblazem_project_status_sanitize_stages(get_post_meta($edit_id, '_project_stages', true)) : weblazem_project_status_default_stages();
    $files   = $edit ? weblazem_project_status_sanitize_files(get_post_meta($edit_id, '_project_files', true)) : array();
    if (count($files) < 2) {
        $files = array_pad($files, 2, array('title' => '', 'url' => ''));
    }

    $title   = $edit ? $edit->post_title : '';
    $mobile  = $edit ? get_post_meta($edit_id, '_project_client_mobile', true) : '';
    $name    = $edit ? get_post_meta($edit_id, '_project_client_name', true) : '';
    $progress = $edit ? (int) get_post_meta($edit_id, '_project_progress', true) : 0;
    $stage   = $edit ? get_post_meta($edit_id, '_project_stage', true) : 'briefing';
    $code    = $edit ? get_post_meta($edit_id, '_project_code', true) : '';
    ?>
    <h2><?php echo $edit ? 'ویرایش پروژه' : 'افزودن پروژه جدید'; ?></h2>
    <form method="post" style="margin-bottom:28px;background:#fff;padding:16px;border:1px solid #ccd0d4;">
        <?php wp_nonce_field('weblazem_ps_project_save', 'weblazem_ps_project_nonce'); ?>
        <input type="hidden" name="project_id" value="<?php echo esc_attr($edit_id); ?>" />
        <table class="form-table">
            <tr>
                <th>عنوان پروژه</th>
                <td><input type="text" name="project_title" class="regular-text" required value="<?php echo esc_attr($title); ?>" /></td>
            </tr>
            <tr>
                <th>نام مشتری</th>
                <td><input type="text" name="client_name" class="regular-text" value="<?php echo esc_attr($name); ?>" /></td>
            </tr>
            <tr>
                <th>موبایل مشتری</th>
                <td><input type="text" name="client_mobile" class="regular-text" dir="ltr" required value="<?php echo esc_attr($mobile); ?>" placeholder="09121234567" /></td>
            </tr>
            <?php if ($code) : ?>
                <tr>
                    <th>کد پروژه</th>
                    <td><code dir="ltr"><?php echo esc_html($code); ?></code></td>
                </tr>
            <?php endif; ?>
            <tr>
                <th>پیشرفت (%)</th>
                <td><input type="number" name="progress" min="0" max="100" value="<?php echo esc_attr($progress); ?>" /></td>
            </tr>
            <tr>
                <th>مرحله فعلی</th>
                <td>
                    <select name="stage">
                        <?php foreach ($labels as $key => $label) : ?>
                            <option value="<?php echo esc_attr($key); ?>" <?php selected($stage, $key); ?>><?php echo esc_html($label); ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th>مراحل</th>
                <td>
                    <?php foreach ($stages as $s) : ?>
                        <div style="margin-bottom:10px;padding:8px;border:1px solid #eee;">
                            <label>
                                <input type="checkbox" name="stage_done[]" value="<?php echo esc_attr($s['key']); ?>" <?php checked(!empty($s['done'])); ?> />
                                <strong><?php echo esc_html($s['label']); ?></strong>
                            </label>
                            <input type="text" name="stage_date[<?php echo esc_attr($s['key']); ?>]" value="<?php echo esc_attr($s['date']); ?>" placeholder="تاریخ" dir="ltr" style="width:120px;margin:0 8px;" />
                            <input type="text" name="stage_note[<?php echo esc_attr($s['key']); ?>]" value="<?php echo esc_attr($s['note']); ?>" placeholder="یادداشت" class="regular-text" />
                        </div>
                    <?php endforeach; ?>
                </td>
            </tr>
            <tr>
                <th>فایل‌ها</th>
                <td>
                    <?php foreach ($files as $i => $file) : ?>
                        <p>
                            <input type="text" name="file_title[]" value="<?php echo esc_attr($file['title']); ?>" placeholder="عنوان فایل" />
                            <input type="url" name="file_url[]" value="<?php echo esc_attr($file['url']); ?>" placeholder="https://..." dir="ltr" class="regular-text" />
                        </p>
                    <?php endforeach; ?>
                </td>
            </tr>
        </table>
        <?php submit_button($edit ? 'به‌روزرسانی پروژه' : 'ایجاد پروژه'); ?>
        <?php if ($edit) : ?>
            <a href="<?php echo esc_url(admin_url('admin.php?page=weblazem-project-status-options&tab=projects')); ?>">انصراف / پروژه جدید</a>
        <?php endif; ?>
    </form>

    <h2>لیست پروژه‌ها</h2>
    <?php
    $query = new WP_Query(
        array(
            'post_type'      => 'client_project',
            'post_status'    => 'publish',
            'posts_per_page' => 100,
            'orderby'        => 'modified',
            'order'          => 'DESC',
        )
    );
    ?>
    <table class="widefat striped">
        <thead>
            <tr>
                <th>عنوان</th>
                <th>کد</th>
                <th>مشتری</th>
                <th>موبایل</th>
                <th>پیشرفت</th>
                <th>مرحله</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php if (!$query->have_posts()) : ?>
                <tr><td colspan="7">پروژه‌ای ثبت نشده است.</td></tr>
            <?php else : ?>
                <?php while ($query->have_posts()) : $query->the_post(); ?>
                    <?php
                    $id = get_the_ID();
                    $st = get_post_meta($id, '_project_stage', true);
                    ?>
                    <tr>
                        <td><?php the_title(); ?></td>
                        <td dir="ltr"><?php echo esc_html(get_post_meta($id, '_project_code', true)); ?></td>
                        <td><?php echo esc_html(get_post_meta($id, '_project_client_name', true)); ?></td>
                        <td dir="ltr"><?php echo esc_html(get_post_meta($id, '_project_client_mobile', true)); ?></td>
                        <td><?php echo esc_html((int) get_post_meta($id, '_project_progress', true)); ?>%</td>
                        <td><?php echo esc_html(isset($labels[$st]) ? $labels[$st] : $st); ?></td>
                        <td><a href="<?php echo esc_url(admin_url('admin.php?page=weblazem-project-status-options&tab=projects&edit=' . $id)); ?>">ویرایش</a></td>
                    </tr>
                <?php endwhile; ?>
            <?php endif; ?>
        </tbody>
    </table>
    <?php
    wp_reset_postdata();
}

function weblazem_ajax_project_status_session() {
    check_ajax_referer('weblazem_project_status', 'nonce');

    if (!function_exists('weblazem_ticket_get_session_user')) {
        wp_send_json_success(array('loggedIn' => false));
    }

    $mobile = weblazem_ticket_get_session_user();
    if ($mobile === '') {
        wp_send_json_success(array('loggedIn' => false));
    }

    wp_send_json_success(
        array(
            'loggedIn' => true,
            'mobile'   => $mobile,
            'projects' => weblazem_get_client_projects_by_mobile($mobile),
        )
    );
}
add_action('wp_ajax_weblazem_project_status_session', 'weblazem_ajax_project_status_session');
add_action('wp_ajax_nopriv_weblazem_project_status_session', 'weblazem_ajax_project_status_session');

function weblazem_ajax_project_status_login() {
    check_ajax_referer('weblazem_project_status', 'nonce');

    $rate = weblazem_growth_rate_limit('project_status_login', 10, 15 * MINUTE_IN_SECONDS);
    if (is_wp_error($rate)) {
        wp_send_json_error(array('message' => $rate->get_error_message()), 429);
    }

    $mobile = isset($_POST['mobile']) ? sanitize_text_field(wp_unslash($_POST['mobile'])) : '';
    $code   = isset($_POST['access_code']) ? sanitize_text_field(wp_unslash($_POST['access_code'])) : '';

    if ($mobile === '' || $code === '') {
        wp_send_json_error(array('message' => 'شماره موبایل و کد ورود الزامی است.'), 400);
    }

    if (!weblazem_project_status_is_valid_mobile($mobile)) {
        wp_send_json_error(array('message' => 'شماره موبایل معتبر نیست. مثال: 09121234567'), 400);
    }

    $expected = function_exists('weblazem_get_ticket_access_code') ? weblazem_get_ticket_access_code() : '12345';
    if (!hash_equals((string) $expected, (string) $code)) {
        wp_send_json_error(array('message' => 'کد ورود نامعتبر است.'), 403);
    }

    $mobile = weblazem_project_status_normalize_mobile($mobile);

    if (function_exists('weblazem_ticket_set_session_user')) {
        weblazem_ticket_set_session_user($mobile);
    }

    wp_send_json_success(
        array(
            'message'  => 'ورود موفق بود.',
            'mobile'   => $mobile,
            'projects' => weblazem_get_client_projects_by_mobile($mobile),
        )
    );
}
add_action('wp_ajax_weblazem_project_status_login', 'weblazem_ajax_project_status_login');
add_action('wp_ajax_nopriv_weblazem_project_status_login', 'weblazem_ajax_project_status_login');

function weblazem_ajax_project_status_logout() {
    check_ajax_referer('weblazem_project_status', 'nonce');
    if (function_exists('weblazem_ticket_clear_session_user')) {
        weblazem_ticket_clear_session_user();
    }
    wp_send_json_success(array('message' => 'خارج شدید.'));
}
add_action('wp_ajax_weblazem_project_status_logout', 'weblazem_ajax_project_status_logout');
add_action('wp_ajax_nopriv_weblazem_project_status_logout', 'weblazem_ajax_project_status_logout');

function weblazem_ajax_project_status_list() {
    check_ajax_referer('weblazem_project_status', 'nonce');

    if (!function_exists('weblazem_ticket_get_session_user')) {
        wp_send_json_error(array('message' => 'برای مشاهده ابتدا وارد شوید.'), 401);
    }

    $mobile = weblazem_ticket_get_session_user();
    if ($mobile === '') {
        wp_send_json_error(array('message' => 'برای مشاهده ابتدا وارد شوید.'), 401);
    }

    wp_send_json_success(
        array(
            'mobile'   => $mobile,
            'projects' => weblazem_get_client_projects_by_mobile($mobile),
        )
    );
}
add_action('wp_ajax_weblazem_project_status_list', 'weblazem_ajax_project_status_list');
add_action('wp_ajax_nopriv_weblazem_project_status_list', 'weblazem_ajax_project_status_list');

function weblazem_ajax_project_status_get() {
    check_ajax_referer('weblazem_project_status', 'nonce');

    if (!function_exists('weblazem_ticket_get_session_user')) {
        wp_send_json_error(array('message' => 'برای مشاهده ابتدا وارد شوید.'), 401);
    }

    $mobile = weblazem_ticket_get_session_user();
    if ($mobile === '') {
        wp_send_json_error(array('message' => 'برای مشاهده ابتدا وارد شوید.'), 401);
    }

    $project_id = isset($_POST['project_id']) ? absint($_POST['project_id']) : 0;
    $post = get_post($project_id);
    if (!$post || $post->post_type !== 'client_project') {
        wp_send_json_error(array('message' => 'پروژه یافت نشد.'), 404);
    }

    $owner = weblazem_project_status_normalize_mobile(get_post_meta($project_id, '_project_client_mobile', true));
    if ($owner !== $mobile && !current_user_can('manage_options')) {
        wp_send_json_error(array('message' => 'دسترسی مجاز نیست.'), 403);
    }

    wp_send_json_success(array('project' => weblazem_format_client_project_for_api($post)));
}
add_action('wp_ajax_weblazem_project_status_get', 'weblazem_ajax_project_status_get');
add_action('wp_ajax_nopriv_weblazem_project_status_get', 'weblazem_ajax_project_status_get');

function weblazem_enqueue_project_status_assets() {
    if (!weblazem_is_project_status_page()) {
        return;
    }

    $opts = weblazem_get_project_status_options();

    wp_enqueue_style(
        'weblazem-project-status',
        get_template_directory_uri() . '/assets/css/project-status.css',
        array(),
        '1.0.0'
    );

    wp_enqueue_script(
        'weblazem-project-status',
        get_template_directory_uri() . '/assets/js/project-status.js',
        array(),
        '1.0.0',
        true
    );

    wp_localize_script(
        'weblazem-project-status',
        'weblazemProjectStatus',
        array(
            'ajaxUrl'      => admin_url('admin-ajax.php'),
            'nonce'        => wp_create_nonce('weblazem_project_status'),
            'errorMessage' => 'خطایی رخ داد. لطفاً دوباره تلاش کنید.',
            'emptyMessage' => 'پروژه‌ای برای این شماره موبایل ثبت نشده است.',
            'loginIntro'   => $opts['login_intro'],
        )
    );
}
add_action('wp_enqueue_scripts', 'weblazem_enqueue_project_status_assets', 30);
