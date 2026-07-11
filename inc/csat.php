<?php
/**
 * CSAT Survey — post-delivery client satisfaction invites & public stats.
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('weblazem_growth_ensure_page')) {
    require_once get_template_directory() . '/inc/growth-tools-shared.php';
}

define('WEBLAZEM_CSAT_SLUG', 'nazar-sanji');
define('WEBLAZEM_CSAT_TEMPLATE', 'csat-template.php');
define('WEBLAZEM_CSAT_OPTION', 'weblazem_csat_page_id');
define('WEBLAZEM_CSAT_SETTINGS', 'weblazem_csat_settings');

function weblazem_csat_defaults() {
    return array(
        'title'              => 'نظرسنجی رضایت مشتریان',
        'subtitle'           => 'پس از تحویل پروژه، نظر واقعی مشتریان درباره کیفیت همکاری با وب‌لازم',
        'public_title'       => 'رضایت مشتریان وب‌لازم',
        'public_subtitle'    => 'میانگین امتیاز و نظرات منتشرشده از پروژه‌های تکمیل‌شده',
        'form_title'         => 'نظر شما برای ما مهم است',
        'form_subtitle'      => 'لطفاً تجربه همکاری در این پروژه را امتیاز دهید.',
        'success_message'    => 'از وقتی که گذاشتید سپاسگزاریم. نظر شما ثبت شد.',
        'already_message'    => 'این لینک قبلاً استفاده شده و نظرسنجی تکمیل شده است.',
        'invalid_message'    => 'لینک نظرسنجی نامعتبر یا منقضی است.',
        'thank_you_title'    => 'تشکر از بازخورد شما',
    );
}

function weblazem_csat_sanitize_settings($input) {
    $defaults = weblazem_csat_defaults();
    $out      = $defaults;

    if (!is_array($input)) {
        return $out;
    }

    foreach (array_keys($defaults) as $key) {
        if (!isset($input[$key])) {
            continue;
        }
        if (strpos($key, 'subtitle') !== false || strpos($key, 'message') !== false) {
            $out[$key] = sanitize_textarea_field($input[$key]);
        } else {
            $out[$key] = sanitize_text_field($input[$key]);
        }
    }

    return $out;
}

function weblazem_get_csat_settings() {
    $defaults = weblazem_csat_defaults();
    $saved    = get_option(WEBLAZEM_CSAT_SETTINGS, array());
    if (!is_array($saved)) {
        $saved = array();
    }
    return wp_parse_args($saved, $defaults);
}

function weblazem_ensure_csat_defaults() {
    if (get_option(WEBLAZEM_CSAT_SETTINGS, false) === false) {
        update_option(WEBLAZEM_CSAT_SETTINGS, weblazem_csat_defaults());
    }
}
add_action('init', 'weblazem_ensure_csat_defaults', 12);

function weblazem_get_csat_page_id() {
    return weblazem_growth_get_page_id(WEBLAZEM_CSAT_OPTION, WEBLAZEM_CSAT_SLUG);
}

function weblazem_get_csat_page_url() {
    return weblazem_growth_get_page_url(WEBLAZEM_CSAT_OPTION, WEBLAZEM_CSAT_SLUG);
}

function weblazem_is_csat_page() {
    return weblazem_growth_is_page(WEBLAZEM_CSAT_TEMPLATE, WEBLAZEM_CSAT_OPTION, WEBLAZEM_CSAT_SLUG);
}

function weblazem_ensure_csat_page() {
    weblazem_growth_ensure_page(
        array(
            'slug'     => WEBLAZEM_CSAT_SLUG,
            'template' => WEBLAZEM_CSAT_TEMPLATE,
            'title'    => 'نظرسنجی رضایت',
            'option'   => WEBLAZEM_CSAT_OPTION,
        )
    );
}
add_action('init', 'weblazem_ensure_csat_page', 40);

function weblazem_csat_normalize_mobile($phone) {
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

function weblazem_csat_is_valid_mobile($phone) {
    if (function_exists('weblazem_is_valid_iran_mobile')) {
        return weblazem_is_valid_iran_mobile($phone);
    }
    return (bool) preg_match('/^09\d{9}$/', weblazem_csat_normalize_mobile($phone));
}

function weblazem_csat_generate_token() {
    return bin2hex(random_bytes(16));
}

function weblazem_register_csat_cpts() {
    register_post_type(
        'csat_invite',
        array(
            'labels' => array(
                'name'          => 'دعوت‌نامه‌های CSAT',
                'singular_name' => 'دعوت‌نامه CSAT',
                'menu_name'     => 'دعوت‌نامه‌های رضایت',
                'add_new_item'  => 'دعوت‌نامه جدید',
                'edit_item'     => 'ویرایش دعوت‌نامه',
                'search_items'  => 'جستجوی دعوت‌نامه',
                'not_found'     => 'دعوت‌نامه‌ای یافت نشد.',
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

    register_post_type(
        'csat_response',
        array(
            'labels' => array(
                'name'          => 'پاسخ‌های CSAT',
                'singular_name' => 'پاسخ CSAT',
                'menu_name'     => 'پاسخ‌های رضایت',
                'edit_item'     => 'مشاهده پاسخ',
                'search_items'  => 'جستجوی پاسخ',
                'not_found'     => 'پاسخی یافت نشد.',
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
add_action('init', 'weblazem_register_csat_cpts');

/**
 * Create a CSAT invite for a completed project.
 *
 * @param array $args client_name, mobile, project_title
 * @return int|WP_Error Invite post ID
 */
function weblazem_csat_create_invite($args) {
    $args = wp_parse_args(
        $args,
        array(
            'client_name'   => '',
            'mobile'        => '',
            'project_title' => '',
        )
    );

    $client  = sanitize_text_field($args['client_name']);
    $project = sanitize_text_field($args['project_title']);
    $mobile  = weblazem_csat_normalize_mobile($args['mobile']);

    if ($client === '' || $project === '') {
        return new WP_Error('csat_missing', 'نام مشتری و عنوان پروژه الزامی است.');
    }

    if ($mobile !== '' && !weblazem_csat_is_valid_mobile($mobile)) {
        return new WP_Error('csat_mobile', 'شماره موبایل معتبر نیست.');
    }

    $token = weblazem_csat_generate_token();

    $post_id = wp_insert_post(
        array(
            'post_type'   => 'csat_invite',
            'post_status' => 'publish',
            'post_title'  => $client . ' — ' . $project,
        ),
        true
    );

    if (is_wp_error($post_id) || !$post_id) {
        return is_wp_error($post_id) ? $post_id : new WP_Error('csat_insert', 'خطا در ایجاد دعوت‌نامه.');
    }

    update_post_meta($post_id, '_csat_token', $token);
    update_post_meta($post_id, '_csat_client_name', $client);
    update_post_meta($post_id, '_csat_mobile', $mobile);
    update_post_meta($post_id, '_csat_project', $project);
    update_post_meta($post_id, '_csat_status', 'pending');
    update_post_meta($post_id, '_csat_sent_at', current_time('mysql'));

    return (int) $post_id;
}

function weblazem_csat_invite_url($invite_id) {
    $token = get_post_meta($invite_id, '_csat_token', true);
    if (!$token) {
        return '';
    }
    return add_query_arg('token', rawurlencode($token), weblazem_get_csat_page_url());
}

function weblazem_csat_find_invite_by_token($token) {
    $token = sanitize_text_field($token);
    if ($token === '') {
        return 0;
    }

    $query = new WP_Query(
        array(
            'post_type'      => 'csat_invite',
            'post_status'    => 'publish',
            'posts_per_page' => 1,
            'fields'         => 'ids',
            'meta_query'     => array(
                array(
                    'key'   => '_csat_token',
                    'value' => $token,
                ),
            ),
        )
    );

    $id = !empty($query->posts[0]) ? (int) $query->posts[0] : 0;
    wp_reset_postdata();
    return $id;
}

function weblazem_csat_category_keys() {
    return array(
        'design'        => 'طراحی',
        'communication' => 'ارتباطات',
        'delivery'      => 'تحویل',
        'support'       => 'پشتیبانی',
    );
}

/**
 * Aggregate CSAT stats from published responses.
 *
 * @return array{avg:float,count:int,distribution:array,categories:array,featured:array,published:array}
 */
function weblazem_get_csat_stats() {
    $query = new WP_Query(
        array(
            'post_type'      => 'csat_response',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'fields'         => 'ids',
        )
    );

    $ids            = $query->posts;
    $count          = count($ids);
    $sum            = 0;
    $distribution   = array(1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0);
    $cat_sums       = array('design' => 0, 'communication' => 0, 'delivery' => 0, 'support' => 0);
    $cat_counts     = array('design' => 0, 'communication' => 0, 'delivery' => 0, 'support' => 0);
    $published      = array();
    $featured       = array();

    foreach ($ids as $id) {
        $overall = (int) get_post_meta($id, '_csat_score_overall', true);
        if ($overall < 1 || $overall > 5) {
            continue;
        }

        $sum += $overall;
        $distribution[$overall]++;

        foreach (array_keys($cat_sums) as $key) {
            $score = (int) get_post_meta($id, '_csat_score_' . $key, true);
            if ($score >= 1 && $score <= 5) {
                $cat_sums[$key] += $score;
                $cat_counts[$key]++;
            }
        }

        $allow = get_post_meta($id, '_csat_allow_publish', true) === '1';
        if (!$allow) {
            continue;
        }

        $item = array(
            'id'       => $id,
            'score'    => $overall,
            'comment'  => get_post_meta($id, '_csat_comment', true),
            'client'   => get_post_meta($id, '_csat_client_name', true),
            'project'  => get_post_meta($id, '_csat_project', true),
            'featured' => get_post_meta($id, '_csat_featured', true) === '1',
        );

        $published[] = $item;
        if ($item['featured'] && $item['comment'] !== '') {
            $featured[] = $item;
        }
    }

    wp_reset_postdata();

    $categories = array();
    $labels     = weblazem_csat_category_keys();
    foreach ($labels as $key => $label) {
        $categories[$key] = array(
            'label' => $label,
            'avg'   => $cat_counts[$key] > 0 ? round($cat_sums[$key] / $cat_counts[$key], 1) : 0,
            'count' => $cat_counts[$key],
        );
    }

    return array(
        'avg'          => $count > 0 ? round($sum / $count, 1) : 0,
        'count'        => $count,
        'distribution' => $distribution,
        'categories'   => $categories,
        'published'    => $published,
        'featured'     => $featured,
    );
}

function weblazem_csat_admin_menu() {
    add_submenu_page(
        'weblazem-theme-options',
        'نظرسنجی رضایت',
        'نظرسنجی رضایت',
        'manage_options',
        'weblazem-csat-options',
        'weblazem_csat_options_display'
    );
}
add_action('admin_menu', 'weblazem_csat_admin_menu', 41);

function weblazem_csat_handle_admin_actions() {
    if (!current_user_can('manage_options')) {
        return;
    }

    // Settings save
    if (isset($_POST['weblazem_csat_settings_nonce']) &&
        wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['weblazem_csat_settings_nonce'])), 'weblazem_csat_settings')) {
        $payload = array();
        foreach (array_keys(weblazem_csat_defaults()) as $key) {
            if (isset($_POST[$key])) {
                $payload[$key] = wp_unslash($_POST[$key]);
            }
        }
        update_option(WEBLAZEM_CSAT_SETTINGS, weblazem_csat_sanitize_settings($payload));
        add_settings_error('weblazem_csat', 'settings_saved', 'تنظیمات ذخیره شد.', 'updated');
    }

    // Create invite
    if (isset($_POST['weblazem_csat_invite_nonce']) &&
        wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['weblazem_csat_invite_nonce'])), 'weblazem_csat_invite')) {
        $result = weblazem_csat_create_invite(
            array(
                'client_name'   => isset($_POST['client_name']) ? wp_unslash($_POST['client_name']) : '',
                'mobile'        => isset($_POST['mobile']) ? wp_unslash($_POST['mobile']) : '',
                'project_title' => isset($_POST['project_title']) ? wp_unslash($_POST['project_title']) : '',
            )
        );

        if (is_wp_error($result)) {
            add_settings_error('weblazem_csat', 'invite_err', $result->get_error_message(), 'error');
        } else {
            $url = weblazem_csat_invite_url($result);
            add_settings_error(
                'weblazem_csat',
                'invite_ok',
                'دعوت‌نامه ساخته شد. لینک: ' . esc_html($url),
                'updated'
            );
            set_transient('weblazem_csat_last_invite_url', $url, HOUR_IN_SECONDS);
        }
    }

    // Toggle featured
    if (isset($_GET['csat_feature']) && isset($_GET['_wpnonce'])) {
        $resp_id = (int) $_GET['csat_feature'];
        if (wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['_wpnonce'])), 'csat_feature_' . $resp_id)) {
            $current = get_post_meta($resp_id, '_csat_featured', true) === '1';
            update_post_meta($resp_id, '_csat_featured', $current ? '0' : '1');
            add_settings_error('weblazem_csat', 'featured', 'وضعیت ویژه به‌روز شد.', 'updated');
        }
    }
}

function weblazem_csat_options_display() {
    if (!current_user_can('manage_options')) {
        return;
    }

    weblazem_csat_handle_admin_actions();

    $tab      = isset($_GET['tab']) ? sanitize_key(wp_unslash($_GET['tab'])) : 'settings';
    $allowed  = array('settings', 'invite', 'responses');
    if (!in_array($tab, $allowed, true)) {
        $tab = 'settings';
    }

    $s         = weblazem_get_csat_settings();
    $page_url  = weblazem_get_csat_page_url();
    $base      = admin_url('admin.php?page=weblazem-csat-options');
    $last_url  = get_transient('weblazem_csat_last_invite_url');
    $stats     = weblazem_get_csat_stats();

    settings_errors('weblazem_csat');
    ?>
    <div class="wrap" dir="rtl">
        <h1>نظرسنجی رضایت (CSAT)</h1>
        <?php if ($page_url) : ?>
            <p>
                صفحه عمومی:
                <a href="<?php echo esc_url($page_url); ?>" target="_blank" rel="noopener"><?php echo esc_html($page_url); ?></a>
                —
                میانگین فعلی: <strong><?php echo esc_html($stats['avg']); ?></strong>
                از <?php echo esc_html((string) $stats['count']); ?> پاسخ
            </p>
        <?php endif; ?>

        <h2 class="nav-tab-wrapper">
            <a href="<?php echo esc_url(add_query_arg('tab', 'settings', $base)); ?>" class="nav-tab <?php echo $tab === 'settings' ? 'nav-tab-active' : ''; ?>">تنظیمات</a>
            <a href="<?php echo esc_url(add_query_arg('tab', 'invite', $base)); ?>" class="nav-tab <?php echo $tab === 'invite' ? 'nav-tab-active' : ''; ?>">ایجاد دعوت‌نامه</a>
            <a href="<?php echo esc_url(add_query_arg('tab', 'responses', $base)); ?>" class="nav-tab <?php echo $tab === 'responses' ? 'nav-tab-active' : ''; ?>">پاسخ‌ها</a>
        </h2>

        <?php if ($tab === 'settings') : ?>
            <form method="post">
                <?php wp_nonce_field('weblazem_csat_settings', 'weblazem_csat_settings_nonce'); ?>
                <table class="form-table">
                    <?php
                    $fields = array(
                        'title'           => 'عنوان (با توکن)',
                        'subtitle'        => 'زیرعنوان (با توکن)',
                        'public_title'    => 'عنوان صفحه عمومی',
                        'public_subtitle' => 'زیرعنوان صفحه عمومی',
                        'form_title'      => 'عنوان فرم',
                        'form_subtitle'   => 'زیرعنوان فرم',
                        'success_message' => 'پیام موفقیت',
                        'already_message' => 'پیام توکن استفاده‌شده',
                        'invalid_message' => 'پیام توکن نامعتبر',
                        'thank_you_title' => 'عنوان تشکر',
                    );
                    foreach ($fields as $key => $label) :
                        $is_area = (strpos($key, 'subtitle') !== false || strpos($key, 'message') !== false);
                        ?>
                        <tr>
                            <th><?php echo esc_html($label); ?></th>
                            <td>
                                <?php if ($is_area) : ?>
                                    <textarea class="large-text" rows="2" name="<?php echo esc_attr($key); ?>"><?php echo esc_textarea($s[$key]); ?></textarea>
                                <?php else : ?>
                                    <input type="text" class="large-text" name="<?php echo esc_attr($key); ?>" value="<?php echo esc_attr($s[$key]); ?>" />
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>
                <?php submit_button('ذخیره تنظیمات'); ?>
            </form>
        <?php elseif ($tab === 'invite') : ?>
            <?php if ($last_url) : ?>
                <div class="notice notice-success"><p>آخرین لینک: <code dir="ltr"><?php echo esc_html($last_url); ?></code></p></div>
            <?php endif; ?>
            <form method="post">
                <?php wp_nonce_field('weblazem_csat_invite', 'weblazem_csat_invite_nonce'); ?>
                <table class="form-table">
                    <tr>
                        <th>نام مشتری</th>
                        <td><input type="text" class="regular-text" name="client_name" required /></td>
                    </tr>
                    <tr>
                        <th>موبایل</th>
                        <td><input type="text" class="regular-text" dir="ltr" name="mobile" placeholder="09121234567" /></td>
                    </tr>
                    <tr>
                        <th>عنوان پروژه</th>
                        <td><input type="text" class="large-text" name="project_title" required /></td>
                    </tr>
                </table>
                <?php submit_button('ساخت دعوت‌نامه و لینک'); ?>
            </form>

            <h2>دعوت‌نامه‌های اخیر</h2>
            <?php weblazem_csat_admin_invites_table(); ?>
        <?php else : ?>
            <?php weblazem_csat_admin_responses_table(); ?>
        <?php endif; ?>
    </div>
    <?php
}

function weblazem_csat_admin_invites_table() {
    $query = new WP_Query(
        array(
            'post_type'      => 'csat_invite',
            'post_status'    => 'publish',
            'posts_per_page' => 50,
            'orderby'        => 'date',
            'order'          => 'DESC',
        )
    );
    ?>
    <table class="widefat striped" style="margin-top:12px;">
        <thead>
            <tr>
                <th>مشتری</th>
                <th>پروژه</th>
                <th>موبایل</th>
                <th>وضعیت</th>
                <th>لینک</th>
                <th>تاریخ</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!$query->have_posts()) : ?>
                <tr><td colspan="6">دعوت‌نامه‌ای ثبت نشده است.</td></tr>
            <?php else : ?>
                <?php while ($query->have_posts()) : $query->the_post(); ?>
                    <?php
                    $id     = get_the_ID();
                    $status = get_post_meta($id, '_csat_status', true);
                    $url    = weblazem_csat_invite_url($id);
                    ?>
                    <tr>
                        <td><?php echo esc_html(get_post_meta($id, '_csat_client_name', true)); ?></td>
                        <td><?php echo esc_html(get_post_meta($id, '_csat_project', true)); ?></td>
                        <td dir="ltr"><?php echo esc_html(get_post_meta($id, '_csat_mobile', true)); ?></td>
                        <td><?php echo $status === 'completed' ? 'تکمیل‌شده' : 'در انتظار'; ?></td>
                        <td><input type="text" class="large-text" dir="ltr" readonly value="<?php echo esc_attr($url); ?>" onclick="this.select();" /></td>
                        <td><?php echo esc_html(get_the_date('Y-m-d H:i')); ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php endif; ?>
        </tbody>
    </table>
    <?php
    wp_reset_postdata();
}

function weblazem_csat_admin_responses_table() {
    $query = new WP_Query(
        array(
            'post_type'      => 'csat_response',
            'post_status'    => 'publish',
            'posts_per_page' => 100,
            'orderby'        => 'date',
            'order'          => 'DESC',
        )
    );
    $base = admin_url('admin.php?page=weblazem-csat-options&tab=responses');
    ?>
    <table class="widefat striped" style="margin-top:16px;">
        <thead>
            <tr>
                <th>مشتری</th>
                <th>پروژه</th>
                <th>امتیاز</th>
                <th>نظر</th>
                <th>انتشار</th>
                <th>ویژه</th>
                <th>تاریخ</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!$query->have_posts()) : ?>
                <tr><td colspan="7">پاسخی ثبت نشده است.</td></tr>
            <?php else : ?>
                <?php while ($query->have_posts()) : $query->the_post(); ?>
                    <?php
                    $id       = get_the_ID();
                    $score    = (int) get_post_meta($id, '_csat_score_overall', true);
                    $comment  = get_post_meta($id, '_csat_comment', true);
                    $publish  = get_post_meta($id, '_csat_allow_publish', true) === '1';
                    $featured = get_post_meta($id, '_csat_featured', true) === '1';
                    $toggle   = wp_nonce_url(add_query_arg('csat_feature', $id, $base), 'csat_feature_' . $id);
                    ?>
                    <tr>
                        <td><?php echo esc_html(get_post_meta($id, '_csat_client_name', true)); ?></td>
                        <td><?php echo esc_html(get_post_meta($id, '_csat_project', true)); ?></td>
                        <td><strong><?php echo esc_html((string) $score); ?>/5</strong></td>
                        <td><?php echo esc_html(wp_trim_words($comment, 16)); ?></td>
                        <td><?php echo $publish ? 'بله' : 'خیر'; ?></td>
                        <td>
                            <?php if ($publish) : ?>
                                <a href="<?php echo esc_url($toggle); ?>"><?php echo $featured ? 'حذف از ویژه' : 'علامت ویژه'; ?></a>
                            <?php else : ?>
                                —
                            <?php endif; ?>
                        </td>
                        <td><?php echo esc_html(get_the_date('Y-m-d H:i')); ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php endif; ?>
        </tbody>
    </table>
    <?php
    wp_reset_postdata();
}

function weblazem_ajax_csat_submit() {
    check_ajax_referer('weblazem_csat', 'nonce');

    $rate = weblazem_growth_rate_limit('csat_submit', 8, 20 * MINUTE_IN_SECONDS);
    if (is_wp_error($rate)) {
        wp_send_json_error(array('message' => $rate->get_error_message()), 429);
    }

    $settings = weblazem_get_csat_settings();
    $token    = isset($_POST['token']) ? sanitize_text_field(wp_unslash($_POST['token'])) : '';
    $invite_id = weblazem_csat_find_invite_by_token($token);

    if (!$invite_id) {
        wp_send_json_error(array('message' => $settings['invalid_message']), 400);
    }

    $status = get_post_meta($invite_id, '_csat_status', true);
    if ($status === 'completed') {
        wp_send_json_error(array('message' => $settings['already_message']), 409);
    }

    $overall = isset($_POST['score_overall']) ? (int) $_POST['score_overall'] : 0;
    if ($overall < 1 || $overall > 5) {
        wp_send_json_error(array('message' => 'امتیاز کلی باید بین ۱ تا ۵ باشد.'), 400);
    }

    $categories = weblazem_csat_category_keys();
    $scores     = array();
    foreach (array_keys($categories) as $key) {
        $val = isset($_POST['score_' . $key]) ? (int) $_POST['score_' . $key] : 0;
        if ($val !== 0 && ($val < 1 || $val > 5)) {
            wp_send_json_error(array('message' => 'امتیاز دسته‌ها باید بین ۱ تا ۵ باشد.'), 400);
        }
        $scores[$key] = $val;
    }

    $comment       = isset($_POST['comment']) ? sanitize_textarea_field(wp_unslash($_POST['comment'])) : '';
    $allow_publish = (!empty($_POST['allow_publish']) && $_POST['allow_publish'] === '1') ? '1' : '0';

    $client  = get_post_meta($invite_id, '_csat_client_name', true);
    $project = get_post_meta($invite_id, '_csat_project', true);
    $mobile  = get_post_meta($invite_id, '_csat_mobile', true);

    $response_id = wp_insert_post(
        array(
            'post_type'   => 'csat_response',
            'post_status' => 'publish',
            'post_title'  => $client . ' — ' . $overall . '/5 — ' . $project,
        ),
        true
    );

    if (is_wp_error($response_id) || !$response_id) {
        wp_send_json_error(array('message' => 'خطا در ثبت پاسخ. دوباره تلاش کنید.'), 500);
    }

    update_post_meta($response_id, '_csat_invite_id', $invite_id);
    update_post_meta($response_id, '_csat_token', $token);
    update_post_meta($response_id, '_csat_client_name', $client);
    update_post_meta($response_id, '_csat_mobile', $mobile);
    update_post_meta($response_id, '_csat_project', $project);
    update_post_meta($response_id, '_csat_score_overall', $overall);
    update_post_meta($response_id, '_csat_comment', $comment);
    update_post_meta($response_id, '_csat_allow_publish', $allow_publish);
    update_post_meta($response_id, '_csat_featured', '0');
    update_post_meta($response_id, '_csat_ip', weblazem_growth_client_ip());

    foreach ($scores as $key => $val) {
        if ($val >= 1) {
            update_post_meta($response_id, '_csat_score_' . $key, $val);
        }
    }

    update_post_meta($invite_id, '_csat_status', 'completed');
    update_post_meta($invite_id, '_csat_response_id', $response_id);
    update_post_meta($invite_id, '_csat_completed_at', current_time('mysql'));

    wp_send_json_success(
        array(
            'message' => $settings['success_message'],
            'title'   => $settings['thank_you_title'],
        )
    );
}
add_action('wp_ajax_weblazem_csat_submit', 'weblazem_ajax_csat_submit');
add_action('wp_ajax_nopriv_weblazem_csat_submit', 'weblazem_ajax_csat_submit');

function weblazem_enqueue_csat_assets() {
    $on_csat  = weblazem_is_csat_page();
    $on_home  = is_front_page() || is_home();

    if (!$on_csat && !$on_home) {
        return;
    }

    wp_enqueue_style(
        'weblazem-csat',
        get_template_directory_uri() . '/assets/css/csat.css',
        array(),
        '1.0.0'
    );

    if ($on_csat) {
        wp_enqueue_script(
            'weblazem-csat',
            get_template_directory_uri() . '/assets/js/csat.js',
            array(),
            '1.0.0',
            true
        );

        wp_localize_script(
            'weblazem-csat',
            'weblazemCsat',
            array(
                'ajaxUrl'      => admin_url('admin-ajax.php'),
                'nonce'        => wp_create_nonce('weblazem_csat'),
                'genericError' => 'خطایی رخ داد. دوباره تلاش کنید.',
            )
        );
    }
}
add_action('wp_enqueue_scripts', 'weblazem_enqueue_csat_assets', 30);
