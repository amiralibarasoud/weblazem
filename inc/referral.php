<?php
/**
 * Referral club — members, leads, cookie attribution, AJAX, admin.
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('weblazem_growth_ensure_page')) {
    require_once get_template_directory() . '/inc/growth-tools-shared.php';
}

define('WEBLAZEM_REFERRAL_SLUG', 'bashgah-moarefi');
define('WEBLAZEM_REFERRAL_TEMPLATE', 'referral-template.php');
define('WEBLAZEM_REFERRAL_OPTION', 'weblazem_referral_page_id');
define('WEBLAZEM_REFERRAL_COOKIE', 'weblazem_ref');

function weblazem_referral_defaults() {
    return array(
        'title'             => 'باشگاه معرفی وب‌لازم',
        'subtitle'          => 'دوستانتان را معرفی کنید؛ هم شما و هم آن‌ها پاداش می‌گیرید.',
        'join_title'        => 'عضویت در باشگاه',
        'join_subtitle'     => 'نام و موبایل را وارد کنید تا کد اختصاصی معرفی‌تان ساخته شود.',
        'lead_title'        => 'من از معرفی اومدم',
        'lead_subtitle'     => 'اگر با لینک معرفی آمده‌اید، همین‌جا درخواست‌تان را ثبت کنید.',
        'reward_text'       => '۵٪ تخفیف پشتیبانی سالانه برای معرف',
        'reward_for_friend' => '۱۰٪ تخفیف شروع پروژه برای دوست معرفی‌شده',
        'terms_text'        => "پاداش پس از تایید پروژه توسط وب‌لازم اعمال می‌شود.\nهر شخص فقط یک‌بار می‌تواند از کد معرفی استفاده کند.\nسوءاستفاده از باشگاه موجب لغو پاداش می‌شود.",
        'success_join'      => 'عضویت شما ثبت شد. لینک معرفی را با دوستان‌تان به اشتراک بگذارید.',
        'success_lead'      => 'درخواست شما ثبت شد. به‌زودی با شما تماس می‌گیریم.',
        'share_label'       => 'لینک معرفی شما',
        'copy_text'         => 'کپی لینک',
        'copied_text'       => 'کپی شد!',
    );
}

function weblazem_get_referral_settings() {
    $defaults = weblazem_referral_defaults();
    $saved    = get_option('weblazem_referral_settings', array());
    if (!is_array($saved)) {
        $saved = array();
    }
    return wp_parse_args($saved, $defaults);
}

function weblazem_ensure_referral_defaults() {
    if (get_option('weblazem_referral_settings') === false) {
        update_option('weblazem_referral_settings', weblazem_referral_defaults());
    }
}
add_action('init', 'weblazem_ensure_referral_defaults', 12);

function weblazem_get_referral_page_id() {
    return weblazem_growth_get_page_id(WEBLAZEM_REFERRAL_OPTION, WEBLAZEM_REFERRAL_SLUG);
}

function weblazem_get_referral_page_url() {
    return weblazem_growth_get_page_url(WEBLAZEM_REFERRAL_OPTION, WEBLAZEM_REFERRAL_SLUG);
}

function weblazem_is_referral_page() {
    return weblazem_growth_is_page(
        WEBLAZEM_REFERRAL_TEMPLATE,
        WEBLAZEM_REFERRAL_OPTION,
        WEBLAZEM_REFERRAL_SLUG
    );
}

function weblazem_ensure_referral_page() {
    weblazem_growth_ensure_page(
        array(
            'slug'     => WEBLAZEM_REFERRAL_SLUG,
            'template' => WEBLAZEM_REFERRAL_TEMPLATE,
            'title'    => 'باشگاه معرفی',
            'option'   => WEBLAZEM_REFERRAL_OPTION,
        )
    );
}
add_action('init', 'weblazem_ensure_referral_page', 39);

function weblazem_referral_normalize_mobile($phone) {
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

function weblazem_referral_is_valid_mobile($phone) {
    if (function_exists('weblazem_is_valid_iran_mobile')) {
        return weblazem_is_valid_iran_mobile($phone);
    }
    return (bool) preg_match('/^09\d{9}$/', weblazem_referral_normalize_mobile($phone));
}

function weblazem_register_referral_cpts() {
    register_post_type(
        'referral_member',
        array(
            'labels' => array(
                'name'          => 'اعضای معرفی',
                'singular_name' => 'عضو معرفی',
                'menu_name'     => 'اعضای باشگاه معرفی',
                'search_items'  => 'جستجوی عضو',
                'not_found'     => 'عضوی یافت نشد.',
            ),
            'public'             => false,
            'publicly_queryable' => false,
            'show_ui'            => true,
            'show_in_menu'       => false,
            'capability_type'    => 'post',
            'map_meta_cap'       => true,
            'supports'           => array('title'),
            'has_archive'        => false,
        )
    );

    register_post_type(
        'referral_lead',
        array(
            'labels' => array(
                'name'          => 'لیدهای معرفی',
                'singular_name' => 'لید معرفی',
                'menu_name'     => 'لیدهای باشگاه معرفی',
                'search_items'  => 'جستجوی لید',
                'not_found'     => 'لیدی یافت نشد.',
            ),
            'public'             => false,
            'publicly_queryable' => false,
            'show_ui'            => true,
            'show_in_menu'       => false,
            'capability_type'    => 'post',
            'map_meta_cap'       => true,
            'supports'           => array('title'),
            'has_archive'        => false,
        )
    );
}
add_action('init', 'weblazem_register_referral_cpts');

function weblazem_referral_generate_code() {
    $alphabet = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
    for ($attempt = 0; $attempt < 40; $attempt++) {
        $code = 'WL-';
        for ($i = 0; $i < 4; $i++) {
            $code .= $alphabet[wp_rand(0, strlen($alphabet) - 1)];
        }
        if (!weblazem_referral_find_member_by_code($code)) {
            return $code;
        }
    }
    return 'WL-' . strtoupper(substr(md5(uniqid((string) wp_rand(), true)), 0, 4));
}

function weblazem_referral_normalize_code($code) {
    $code = strtoupper(trim((string) $code));
    $code = preg_replace('/[^A-Z0-9\-]/', '', $code);
    return $code;
}

function weblazem_referral_find_member_by_code($code) {
    $code = weblazem_referral_normalize_code($code);
    if ($code === '') {
        return 0;
    }
    $q = new WP_Query(
        array(
            'post_type'      => 'referral_member',
            'post_status'    => 'publish',
            'posts_per_page' => 1,
            'fields'         => 'ids',
            'meta_query'     => array(
                array(
                    'key'   => '_ref_code',
                    'value' => $code,
                ),
            ),
        )
    );
    if (empty($q->posts)) {
        return 0;
    }
    return (int) $q->posts[0];
}

function weblazem_referral_find_member_by_mobile($mobile) {
    $mobile = weblazem_referral_normalize_mobile($mobile);
    if ($mobile === '') {
        return 0;
    }
    $q = new WP_Query(
        array(
            'post_type'      => 'referral_member',
            'post_status'    => 'publish',
            'posts_per_page' => 1,
            'fields'         => 'ids',
            'meta_query'     => array(
                array(
                    'key'   => '_ref_mobile',
                    'value' => $mobile,
                ),
            ),
        )
    );
    if (empty($q->posts)) {
        return 0;
    }
    return (int) $q->posts[0];
}

function weblazem_referral_share_url($code) {
    $code = weblazem_referral_normalize_code($code);
    $base = weblazem_get_referral_page_url();
    return add_query_arg('ref', rawurlencode($code), $base);
}

function weblazem_referral_get_cookie_code() {
    if (empty($_COOKIE[WEBLAZEM_REFERRAL_COOKIE])) {
        return '';
    }
    return weblazem_referral_normalize_code(wp_unslash($_COOKIE[WEBLAZEM_REFERRAL_COOKIE]));
}

function weblazem_referral_set_cookie($code) {
    $code = weblazem_referral_normalize_code($code);
    if ($code === '' || !weblazem_referral_find_member_by_code($code)) {
        return false;
    }
    $expire = time() + (30 * DAY_IN_SECONDS);
    if (!headers_sent()) {
        setcookie(
            WEBLAZEM_REFERRAL_COOKIE,
            $code,
            array(
                'expires'  => $expire,
                'path'     => COOKIEPATH ? COOKIEPATH : '/',
                'domain'   => COOKIE_DOMAIN,
                'secure'   => is_ssl(),
                'httponly' => false,
                'samesite' => 'Lax',
            )
        );
    }
    $_COOKIE[WEBLAZEM_REFERRAL_COOKIE] = $code;
    return true;
}

function weblazem_referral_capture_ref_param() {
    if (empty($_GET['ref'])) {
        return;
    }
    $code = weblazem_referral_normalize_code(wp_unslash($_GET['ref']));
    if ($code !== '') {
        weblazem_referral_set_cookie($code);
    }
}
add_action('template_redirect', 'weblazem_referral_capture_ref_param', 5);

function weblazem_referral_attach_code_to_post($post_id, $post, $update) {
    if ($update || wp_is_post_revision($post_id) || wp_is_post_autosave($post_id)) {
        return;
    }
    $types = array('project_brief', 'price_estimate_lead', 'consultation_request');
    if (!in_array($post->post_type, $types, true)) {
        return;
    }
    $code = weblazem_referral_get_cookie_code();
    if ($code === '' || !weblazem_referral_find_member_by_code($code)) {
        return;
    }
    update_post_meta($post_id, '_weblazem_ref_code', $code);
}
add_action('wp_insert_post', 'weblazem_referral_attach_code_to_post', 20, 3);

function weblazem_referral_service_choices() {
    return array(
        'webdesign'  => 'طراحی سایت',
        'ecommerce'  => 'فروشگاه اینترنتی',
        'seo'        => 'سئو',
        'support'    => 'پشتیبانی و محتوا',
        'redesign'   => 'بازطراحی',
        'consult'    => 'مشاوره',
        'other'      => 'سایر',
    );
}

function weblazem_referral_sanitize_settings($input) {
    $defaults = weblazem_referral_defaults();
    $out      = $defaults;
    if (!is_array($input)) {
        return $out;
    }
    foreach (array_keys($defaults) as $key) {
        if (!isset($input[$key])) {
            continue;
        }
        if (in_array($key, array('subtitle', 'join_subtitle', 'lead_subtitle', 'terms_text', 'success_join', 'success_lead'), true)) {
            $out[$key] = sanitize_textarea_field($input[$key]);
        } else {
            $out[$key] = sanitize_text_field($input[$key]);
        }
    }
    return $out;
}

function weblazem_register_referral_settings() {
    register_setting(
        'weblazem_referral_group',
        'weblazem_referral_settings',
        array(
            'type'              => 'array',
            'sanitize_callback' => 'weblazem_referral_sanitize_settings',
            'default'           => weblazem_referral_defaults(),
        )
    );
}
add_action('admin_init', 'weblazem_register_referral_settings');

function weblazem_referral_admin_menu() {
    add_submenu_page(
        'weblazem-theme-options',
        'باشگاه معرفی',
        'باشگاه معرفی',
        'manage_options',
        'weblazem-referral-options',
        'weblazem_referral_options_display'
    );
}
add_action('admin_menu', 'weblazem_referral_admin_menu', 41);

function weblazem_referral_options_display() {
    if (!current_user_can('manage_options')) {
        return;
    }

    $tab  = isset($_GET['tab']) ? sanitize_key($_GET['tab']) : 'settings';
    $base = admin_url('admin.php?page=weblazem-referral-options');
    $s    = weblazem_get_referral_settings();
    $url  = weblazem_get_referral_page_url();
    ?>
    <div class="wrap" dir="rtl">
        <h1>باشگاه معرفی</h1>
        <h2 class="nav-tab-wrapper">
            <a class="nav-tab <?php echo $tab === 'settings' ? 'nav-tab-active' : ''; ?>" href="<?php echo esc_url($base . '&tab=settings'); ?>">تنظیمات</a>
            <a class="nav-tab <?php echo $tab === 'members' ? 'nav-tab-active' : ''; ?>" href="<?php echo esc_url($base . '&tab=members'); ?>">اعضا</a>
            <a class="nav-tab <?php echo $tab === 'leads' ? 'nav-tab-active' : ''; ?>" href="<?php echo esc_url($base . '&tab=leads'); ?>">لیدها</a>
        </h2>

        <?php if ($tab === 'members') : ?>
            <?php weblazem_referral_admin_members_table(); ?>
        <?php elseif ($tab === 'leads') : ?>
            <?php weblazem_referral_admin_leads_table(); ?>
        <?php else : ?>
            <?php if ($url) : ?>
                <p>صفحه باشگاه: <a href="<?php echo esc_url($url); ?>" target="_blank" rel="noopener"><?php echo esc_html($url); ?></a></p>
            <?php endif; ?>
            <form method="post" action="options.php">
                <?php settings_fields('weblazem_referral_group'); ?>
                <table class="form-table">
                    <tr><th>عنوان</th><td><input type="text" class="large-text" name="weblazem_referral_settings[title]" value="<?php echo esc_attr($s['title']); ?>" /></td></tr>
                    <tr><th>زیرعنوان</th><td><textarea class="large-text" rows="2" name="weblazem_referral_settings[subtitle]"><?php echo esc_textarea($s['subtitle']); ?></textarea></td></tr>
                    <tr><th>عنوان فرم عضویت</th><td><input type="text" class="large-text" name="weblazem_referral_settings[join_title]" value="<?php echo esc_attr($s['join_title']); ?>" /></td></tr>
                    <tr><th>توضیح فرم عضویت</th><td><textarea class="large-text" rows="2" name="weblazem_referral_settings[join_subtitle]"><?php echo esc_textarea($s['join_subtitle']); ?></textarea></td></tr>
                    <tr><th>عنوان فرم لید</th><td><input type="text" class="large-text" name="weblazem_referral_settings[lead_title]" value="<?php echo esc_attr($s['lead_title']); ?>" /></td></tr>
                    <tr><th>توضیح فرم لید</th><td><textarea class="large-text" rows="2" name="weblazem_referral_settings[lead_subtitle]"><?php echo esc_textarea($s['lead_subtitle']); ?></textarea></td></tr>
                    <tr><th>پاداش معرف</th><td><input type="text" class="large-text" name="weblazem_referral_settings[reward_text]" value="<?php echo esc_attr($s['reward_text']); ?>" /></td></tr>
                    <tr><th>پاداش دوست</th><td><input type="text" class="large-text" name="weblazem_referral_settings[reward_for_friend]" value="<?php echo esc_attr($s['reward_for_friend']); ?>" /></td></tr>
                    <tr><th>قوانین</th><td><textarea class="large-text" rows="5" name="weblazem_referral_settings[terms_text]"><?php echo esc_textarea($s['terms_text']); ?></textarea></td></tr>
                    <tr><th>پیام موفقیت عضویت</th><td><textarea class="large-text" rows="2" name="weblazem_referral_settings[success_join]"><?php echo esc_textarea($s['success_join']); ?></textarea></td></tr>
                    <tr><th>پیام موفقیت لید</th><td><textarea class="large-text" rows="2" name="weblazem_referral_settings[success_lead]"><?php echo esc_textarea($s['success_lead']); ?></textarea></td></tr>
                </table>
                <?php submit_button('ذخیره تنظیمات'); ?>
            </form>
        <?php endif; ?>
    </div>
    <?php
}

function weblazem_referral_admin_members_table() {
    $q = new WP_Query(
        array(
            'post_type'      => 'referral_member',
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
                <th>کد</th>
                <th>پاداش</th>
                <th>تاریخ</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php if (!$q->have_posts()) : ?>
                <tr><td colspan="6">عضوی ثبت نشده است.</td></tr>
            <?php else : ?>
                <?php while ($q->have_posts()) : $q->the_post(); ?>
                    <?php $id = get_the_ID(); ?>
                    <tr>
                        <td><?php echo esc_html(get_post_meta($id, '_ref_name', true)); ?></td>
                        <td dir="ltr"><?php echo esc_html(get_post_meta($id, '_ref_mobile', true)); ?></td>
                        <td dir="ltr"><code><?php echo esc_html(get_post_meta($id, '_ref_code', true)); ?></code></td>
                        <td><?php echo esc_html(get_post_meta($id, '_ref_reward', true)); ?></td>
                        <td><?php echo esc_html(get_the_date('Y-m-d H:i')); ?></td>
                        <td><a href="<?php echo esc_url(get_edit_post_link($id)); ?>">مشاهده</a></td>
                    </tr>
                <?php endwhile; ?>
            <?php endif; ?>
        </tbody>
    </table>
    <?php
    wp_reset_postdata();
}

function weblazem_referral_admin_leads_table() {
    $services = weblazem_referral_service_choices();
    $q = new WP_Query(
        array(
            'post_type'      => 'referral_lead',
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
                <th>خدمت</th>
                <th>کد معرف</th>
                <th>تاریخ</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php if (!$q->have_posts()) : ?>
                <tr><td colspan="6">لیدی ثبت نشده است.</td></tr>
            <?php else : ?>
                <?php while ($q->have_posts()) : $q->the_post(); ?>
                    <?php
                    $id  = get_the_ID();
                    $svc = get_post_meta($id, '_ref_lead_service', true);
                    ?>
                    <tr>
                        <td><?php echo esc_html(get_post_meta($id, '_ref_lead_name', true)); ?></td>
                        <td dir="ltr"><?php echo esc_html(get_post_meta($id, '_ref_lead_mobile', true)); ?></td>
                        <td><?php echo esc_html(isset($services[$svc]) ? $services[$svc] : $svc); ?></td>
                        <td dir="ltr"><code><?php echo esc_html(get_post_meta($id, '_ref_lead_code', true)); ?></code></td>
                        <td><?php echo esc_html(get_the_date('Y-m-d H:i')); ?></td>
                        <td><a href="<?php echo esc_url(get_edit_post_link($id)); ?>">مشاهده</a></td>
                    </tr>
                <?php endwhile; ?>
            <?php endif; ?>
        </tbody>
    </table>
    <?php
    wp_reset_postdata();
}

function weblazem_ajax_referral_join() {
    check_ajax_referer('weblazem_referral', 'nonce');

    $rl = weblazem_growth_rate_limit('referral_join', 8, 600);
    if (is_wp_error($rl)) {
        wp_send_json_error(array('message' => $rl->get_error_message()), 429);
    }

    $settings = weblazem_get_referral_settings();
    $name     = isset($_POST['name']) ? sanitize_text_field(wp_unslash($_POST['name'])) : '';
    $mobile   = isset($_POST['mobile']) ? sanitize_text_field(wp_unslash($_POST['mobile'])) : '';

    if ($name === '' || mb_strlen($name) < 2) {
        wp_send_json_error(array('message' => 'لطفاً نام معتبر وارد کنید.'), 400);
    }
    if (!weblazem_referral_is_valid_mobile($mobile)) {
        wp_send_json_error(array('message' => 'شماره موبایل معتبر نیست. مثال: 09121234567'), 400);
    }

    $mobile = weblazem_referral_normalize_mobile($mobile);
    $existing = weblazem_referral_find_member_by_mobile($mobile);
    if ($existing) {
        $code = get_post_meta($existing, '_ref_code', true);
        wp_send_json_success(
            array(
                'message'  => 'قبلاً عضو هستید. لینک معرفی شما آماده است.',
                'code'     => $code,
                'shareUrl' => weblazem_referral_share_url($code),
                'reward'   => get_post_meta($existing, '_ref_reward', true),
                'existing' => true,
            )
        );
    }

    $code = weblazem_referral_generate_code();
    $post_id = wp_insert_post(
        array(
            'post_type'   => 'referral_member',
            'post_status' => 'publish',
            'post_title'  => $name . ' — ' . $code,
        ),
        true
    );

    if (is_wp_error($post_id) || !$post_id) {
        wp_send_json_error(array('message' => 'خطا در ثبت عضویت. دوباره تلاش کنید.'), 500);
    }

    update_post_meta($post_id, '_ref_name', $name);
    update_post_meta($post_id, '_ref_mobile', $mobile);
    update_post_meta($post_id, '_ref_code', $code);
    update_post_meta($post_id, '_ref_reward', $settings['reward_text']);
    update_post_meta($post_id, '_ref_reward_friend', $settings['reward_for_friend']);
    update_post_meta($post_id, '_ref_ip', weblazem_growth_client_ip());
    update_post_meta($post_id, '_ref_created_at', current_time('mysql'));

    wp_send_json_success(
        array(
            'message'  => $settings['success_join'],
            'code'     => $code,
            'shareUrl' => weblazem_referral_share_url($code),
            'reward'   => $settings['reward_text'],
            'existing' => false,
        )
    );
}
add_action('wp_ajax_weblazem_referral_join', 'weblazem_ajax_referral_join');
add_action('wp_ajax_nopriv_weblazem_referral_join', 'weblazem_ajax_referral_join');

function weblazem_ajax_referral_lead() {
    check_ajax_referer('weblazem_referral', 'nonce');

    $rl = weblazem_growth_rate_limit('referral_lead', 8, 600);
    if (is_wp_error($rl)) {
        wp_send_json_error(array('message' => $rl->get_error_message()), 429);
    }

    $settings  = weblazem_get_referral_settings();
    $services  = weblazem_referral_service_choices();
    $name      = isset($_POST['name']) ? sanitize_text_field(wp_unslash($_POST['name'])) : '';
    $mobile    = isset($_POST['mobile']) ? sanitize_text_field(wp_unslash($_POST['mobile'])) : '';
    $service   = isset($_POST['service']) ? sanitize_key(wp_unslash($_POST['service'])) : '';
    $code_post = isset($_POST['ref_code']) ? weblazem_referral_normalize_code(wp_unslash($_POST['ref_code'])) : '';

    if ($name === '' || mb_strlen($name) < 2) {
        wp_send_json_error(array('message' => 'لطفاً نام معتبر وارد کنید.'), 400);
    }
    if (!weblazem_referral_is_valid_mobile($mobile)) {
        wp_send_json_error(array('message' => 'شماره موبایل معتبر نیست.'), 400);
    }
    if ($service === '' || !isset($services[$service])) {
        wp_send_json_error(array('message' => 'خدمت مورد علاقه را انتخاب کنید.'), 400);
    }

    $mobile = weblazem_referral_normalize_mobile($mobile);
    $code   = $code_post !== '' ? $code_post : weblazem_referral_get_cookie_code();

    if ($code === '' || !weblazem_referral_find_member_by_code($code)) {
        wp_send_json_error(array('message' => 'کد معرفی معتبر نیست. از لینک معرفی وارد شوید یا کد را وارد کنید.'), 400);
    }

    $member_id = weblazem_referral_find_member_by_code($code);
    $post_id   = wp_insert_post(
        array(
            'post_type'   => 'referral_lead',
            'post_status' => 'publish',
            'post_title'  => $name . ' — ' . $code,
        ),
        true
    );

    if (is_wp_error($post_id) || !$post_id) {
        wp_send_json_error(array('message' => 'خطا در ثبت درخواست. دوباره تلاش کنید.'), 500);
    }

    update_post_meta($post_id, '_ref_lead_name', $name);
    update_post_meta($post_id, '_ref_lead_mobile', $mobile);
    update_post_meta($post_id, '_ref_lead_service', $service);
    update_post_meta($post_id, '_ref_lead_code', $code);
    update_post_meta($post_id, '_ref_lead_member_id', $member_id);
    update_post_meta($post_id, '_ref_lead_ip', weblazem_growth_client_ip());
    update_post_meta($post_id, '_ref_lead_created_at', current_time('mysql'));

    weblazem_referral_set_cookie($code);

    wp_send_json_success(
        array(
            'message' => $settings['success_lead'],
            'leadId'  => $post_id,
            'code'    => $code,
        )
    );
}
add_action('wp_ajax_weblazem_referral_lead', 'weblazem_ajax_referral_lead');
add_action('wp_ajax_nopriv_weblazem_referral_lead', 'weblazem_ajax_referral_lead');

function weblazem_enqueue_referral_assets() {
    $on_referral = weblazem_is_referral_page();
    $has_ref     = !empty($_GET['ref']);

    if (!$on_referral && !$has_ref) {
        // Still set cookie via JS on any page when ?ref= present — enqueue lightweight script globally only if ?ref=
        return;
    }

    $ver      = '1.0.0';
    $settings = weblazem_get_referral_settings();
    $cookie   = weblazem_referral_get_cookie_code();
    $ref_get  = isset($_GET['ref']) ? weblazem_referral_normalize_code(wp_unslash($_GET['ref'])) : '';

    if ($on_referral) {
        wp_enqueue_style(
            'weblazem-referral',
            get_template_directory_uri() . '/assets/css/referral.css',
            array(),
            $ver
        );
    }

    wp_enqueue_script(
        'weblazem-referral',
        get_template_directory_uri() . '/assets/js/referral.js',
        array(),
        $ver,
        true
    );

    wp_localize_script(
        'weblazem-referral',
        'weblazemReferral',
        array(
            'ajaxUrl'     => admin_url('admin-ajax.php'),
            'nonce'       => wp_create_nonce('weblazem_referral'),
            'cookieName'  => WEBLAZEM_REFERRAL_COOKIE,
            'cookieDays'  => 30,
            'refFromUrl'  => $ref_get,
            'refCookie'   => $cookie,
            'pageUrl'     => weblazem_get_referral_page_url(),
            'isPortal'    => $on_referral,
            'services'    => weblazem_referral_service_choices(),
            'shareLabel'  => $settings['share_label'],
            'copyText'    => $settings['copy_text'],
            'copiedText'  => $settings['copied_text'],
            'errorMessage'=> 'خطایی رخ داد. لطفاً دوباره تلاش کنید.',
        )
    );
}
add_action('wp_enqueue_scripts', 'weblazem_enqueue_referral_assets', 30);
