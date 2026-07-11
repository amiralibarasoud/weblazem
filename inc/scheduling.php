<?php
/**
 * Consultation scheduling — CPT, options, page, AJAX, enqueue.
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('weblazem_growth_ensure_page')) {
    require_once get_template_directory() . '/inc/growth-tools-shared.php';
}

define('WEBLAZEM_SCHEDULING_SLUG', 'rezerve-moshavere');
define('WEBLAZEM_SCHEDULING_TEMPLATE', 'scheduling-template.php');
define('WEBLAZEM_SCHEDULING_OPTION', 'weblazem_scheduling_page_id');

function weblazem_scheduling_defaults() {
    return array(
        'enabled'               => 1,
        'title'                 => 'رزرو مشاوره رایگان',
        'subtitle'              => 'روز و ساعت مناسب خود را انتخاب کنید تا با تیم وب‌لازم صحبت کنیم.',
        'work_days'             => array(6, 0, 1, 2, 3),
        'slot_start'            => 10,
        'slot_end'              => 18,
        'slot_duration_minutes' => 30,
        'max_days_ahead'        => 21,
        'blocked_dates'         => '',
        'success_message'       => 'رزرو شما ثبت شد. به‌زودی برای هماهنگی نهایی با شما تماس می‌گیریم.',
    );
}

function weblazem_get_scheduling_options() {
    $defaults = weblazem_scheduling_defaults();
    $saved    = get_option('weblazem_scheduling_options', array());
    if (!is_array($saved)) {
        $saved = array();
    }

    $opts = array_merge($defaults, $saved);

    if (!is_array($opts['work_days'])) {
        $opts['work_days'] = $defaults['work_days'];
    }
    $opts['work_days'] = array_values(array_map('intval', $opts['work_days']));
    $opts['slot_start'] = (int) $opts['slot_start'];
    $opts['slot_end'] = (int) $opts['slot_end'];
    $opts['slot_duration_minutes'] = max(15, (int) $opts['slot_duration_minutes']);
    $opts['max_days_ahead'] = max(1, (int) $opts['max_days_ahead']);
    $opts['enabled'] = !empty($opts['enabled']) ? 1 : 0;

    return $opts;
}

function weblazem_ensure_scheduling_defaults() {
    if (get_option('weblazem_scheduling_options', false) === false) {
        update_option('weblazem_scheduling_options', weblazem_scheduling_defaults());
    }
}
add_action('init', 'weblazem_ensure_scheduling_defaults', 12);

function weblazem_get_scheduling_page_id() {
    return weblazem_growth_get_page_id(WEBLAZEM_SCHEDULING_OPTION, WEBLAZEM_SCHEDULING_SLUG);
}

function weblazem_get_scheduling_page_url() {
    return weblazem_growth_get_page_url(WEBLAZEM_SCHEDULING_OPTION, WEBLAZEM_SCHEDULING_SLUG);
}

function weblazem_is_scheduling_page() {
    return weblazem_growth_is_page(WEBLAZEM_SCHEDULING_TEMPLATE, WEBLAZEM_SCHEDULING_OPTION, WEBLAZEM_SCHEDULING_SLUG);
}

function weblazem_ensure_scheduling_page() {
    weblazem_growth_ensure_page(
        array(
            'slug'     => WEBLAZEM_SCHEDULING_SLUG,
            'template' => WEBLAZEM_SCHEDULING_TEMPLATE,
            'title'    => 'رزرو مشاوره',
            'option'   => WEBLAZEM_SCHEDULING_OPTION,
        )
    );
}
add_action('init', 'weblazem_ensure_scheduling_page', 38);

function weblazem_register_consult_booking_cpt() {
    register_post_type(
        'consult_booking',
        array(
            'labels' => array(
                'name'          => 'رزروهای مشاوره',
                'singular_name' => 'رزرو مشاوره',
                'menu_name'     => 'رزرو مشاوره',
                'add_new'       => 'افزودن رزرو',
                'add_new_item'  => 'افزودن رزرو جدید',
                'edit_item'     => 'ویرایش رزرو',
                'search_items'  => 'جستجوی رزرو',
                'not_found'     => 'رزروی یافت نشد.',
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
add_action('init', 'weblazem_register_consult_booking_cpt');

function weblazem_scheduling_weekday_labels() {
    return array(
        6 => 'شنبه',
        0 => 'یکشنبه',
        1 => 'دوشنبه',
        2 => 'سه‌شنبه',
        3 => 'چهارشنبه',
        4 => 'پنج‌شنبه',
        5 => 'جمعه',
    );
}

function weblazem_scheduling_parse_blocked_dates($raw) {
    $lines  = preg_split('/\r\n|\r|\n/', (string) $raw);
    $dates  = array();
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line !== '' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $line)) {
            $dates[] = $line;
        }
    }
    return array_values(array_unique($dates));
}

function weblazem_scheduling_normalize_mobile($phone) {
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

function weblazem_scheduling_is_valid_mobile($phone) {
    if (function_exists('weblazem_is_valid_iran_mobile')) {
        return weblazem_is_valid_iran_mobile($phone);
    }
    return (bool) preg_match('/^09\d{9}$/', weblazem_scheduling_normalize_mobile($phone));
}

function weblazem_scheduling_booked_times_for_date($date) {
    $query = new WP_Query(
        array(
            'post_type'      => 'consult_booking',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'fields'         => 'ids',
            'meta_query'     => array(
                array(
                    'key'   => '_booking_date',
                    'value' => $date,
                ),
                array(
                    'key'     => '_booking_status',
                    'value'   => array('cancelled'),
                    'compare' => 'NOT IN',
                ),
            ),
        )
    );

    $times = array();
    foreach ($query->posts as $post_id) {
        $time = get_post_meta($post_id, '_booking_time', true);
        if ($time) {
            $times[] = $time;
        }
    }
    wp_reset_postdata();

    return $times;
}

function weblazem_scheduling_generate_slots($date) {
    $opts = weblazem_get_scheduling_options();
    $blocked = weblazem_scheduling_parse_blocked_dates($opts['blocked_dates']);

    if (in_array($date, $blocked, true)) {
        return array();
    }

    $ts = strtotime($date . ' 12:00:00');
    if (!$ts) {
        return array();
    }

    $dow = (int) date('w', $ts);
    if (!in_array($dow, $opts['work_days'], true)) {
        return array();
    }

    $today = current_time('Y-m-d');
    if ($date < $today) {
        return array();
    }

    $max = (int) $opts['max_days_ahead'];
    $limit_ts = strtotime('+' . $max . ' days', strtotime($today . ' 12:00:00'));
    if ($ts > $limit_ts) {
        return array();
    }

    $start_h = (int) $opts['slot_start'];
    $end_h   = (int) $opts['slot_end'];
    $dur     = (int) $opts['slot_duration_minutes'];
    $booked  = weblazem_scheduling_booked_times_for_date($date);

    $slots = array();
    $cursor = $start_h * 60;
    $end_m  = $end_h * 60;

    while ($cursor + $dur <= $end_m) {
        $h = floor($cursor / 60);
        $m = $cursor % 60;
        $label = sprintf('%02d:%02d', $h, $m);

        $available = !in_array($label, $booked, true);

        // Skip past slots for today
        if ($date === $today) {
            $now_min = (int) current_time('G') * 60 + (int) current_time('i');
            if ($cursor <= $now_min) {
                $available = false;
            }
        }

        if ($available) {
            $slots[] = $label;
        }

        $cursor += $dur;
    }

    return $slots;
}

function weblazem_scheduling_available_dates() {
    $opts    = weblazem_get_scheduling_options();
    $blocked = weblazem_scheduling_parse_blocked_dates($opts['blocked_dates']);
    $max     = (int) $opts['max_days_ahead'];
    $labels  = weblazem_scheduling_weekday_labels();
    $dates   = array();

    for ($i = 0; $i <= $max; $i++) {
        $ts   = strtotime('+' . $i . ' days', strtotime(current_time('Y-m-d') . ' 12:00:00'));
        $date = date('Y-m-d', $ts);
        $dow  = (int) date('w', $ts);

        if (!in_array($dow, $opts['work_days'], true)) {
            continue;
        }
        if (in_array($date, $blocked, true)) {
            continue;
        }

        $slots = weblazem_scheduling_generate_slots($date);
        if (empty($slots)) {
            continue;
        }

        $dates[] = array(
            'date'      => $date,
            'weekday'   => isset($labels[$dow]) ? $labels[$dow] : '',
            'label'     => (isset($labels[$dow]) ? $labels[$dow] . ' — ' : '') . $date,
            'slotCount' => count($slots),
        );
    }

    return $dates;
}

function weblazem_scheduling_admin_menu() {
    add_submenu_page(
        'weblazem-theme-options',
        'رزرو مشاوره',
        'رزرو مشاوره',
        'manage_options',
        'weblazem-scheduling-options',
        'weblazem_scheduling_admin_page'
    );
}
add_action('admin_menu', 'weblazem_scheduling_admin_menu', 36);

function weblazem_scheduling_admin_page() {
    if (!current_user_can('manage_options')) {
        return;
    }

    $tab = isset($_GET['tab']) ? sanitize_key($_GET['tab']) : 'settings';

    if ($tab === 'settings' && isset($_POST['weblazem_scheduling_nonce']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['weblazem_scheduling_nonce'])), 'weblazem_scheduling_save')) {
        $work_days = isset($_POST['work_days']) && is_array($_POST['work_days'])
            ? array_map('intval', wp_unslash($_POST['work_days']))
            : array();
        $work_days = array_values(array_intersect($work_days, array(0, 1, 2, 3, 4, 5, 6)));

        $opts = array(
            'enabled'               => !empty($_POST['enabled']) ? 1 : 0,
            'title'                 => isset($_POST['title']) ? sanitize_text_field(wp_unslash($_POST['title'])) : '',
            'subtitle'              => isset($_POST['subtitle']) ? sanitize_textarea_field(wp_unslash($_POST['subtitle'])) : '',
            'work_days'             => $work_days,
            'slot_start'            => isset($_POST['slot_start']) ? absint($_POST['slot_start']) : 10,
            'slot_end'              => isset($_POST['slot_end']) ? absint($_POST['slot_end']) : 18,
            'slot_duration_minutes' => isset($_POST['slot_duration_minutes']) ? absint($_POST['slot_duration_minutes']) : 30,
            'max_days_ahead'        => isset($_POST['max_days_ahead']) ? absint($_POST['max_days_ahead']) : 21,
            'blocked_dates'         => isset($_POST['blocked_dates']) ? sanitize_textarea_field(wp_unslash($_POST['blocked_dates'])) : '',
            'success_message'       => isset($_POST['success_message']) ? sanitize_textarea_field(wp_unslash($_POST['success_message'])) : '',
        );
        update_option('weblazem_scheduling_options', $opts);
        echo '<div class="notice notice-success is-dismissible"><p>تنظیمات ذخیره شد.</p></div>';
    }

    $opts     = weblazem_get_scheduling_options();
    $page_url = weblazem_get_scheduling_page_url();
    $labels   = weblazem_scheduling_weekday_labels();
    $base     = admin_url('admin.php?page=weblazem-scheduling-options');
    ?>
    <div class="wrap" dir="rtl">
        <h1>رزرو مشاوره</h1>
        <h2 class="nav-tab-wrapper">
            <a href="<?php echo esc_url($base . '&tab=settings'); ?>" class="nav-tab <?php echo $tab === 'settings' ? 'nav-tab-active' : ''; ?>">تنظیمات</a>
            <a href="<?php echo esc_url($base . '&tab=bookings'); ?>" class="nav-tab <?php echo $tab === 'bookings' ? 'nav-tab-active' : ''; ?>">لیست رزروها</a>
        </h2>

        <?php if ($tab === 'bookings') : ?>
            <?php weblazem_scheduling_admin_bookings_table(); ?>
        <?php else : ?>
            <?php if ($page_url) : ?>
                <p>صفحه رزرو: <a href="<?php echo esc_url($page_url); ?>" target="_blank" rel="noopener"><?php echo esc_html($page_url); ?></a></p>
            <?php endif; ?>
            <form method="post">
                <?php wp_nonce_field('weblazem_scheduling_save', 'weblazem_scheduling_nonce'); ?>
                <table class="form-table">
                    <tr>
                        <th>فعال بودن</th>
                        <td><label><input type="checkbox" name="enabled" value="1" <?php checked($opts['enabled'], 1); ?> /> نمایش و پذیرش رزرو</label></td>
                    </tr>
                    <tr>
                        <th>عنوان</th>
                        <td><input type="text" name="title" class="large-text" value="<?php echo esc_attr($opts['title']); ?>" /></td>
                    </tr>
                    <tr>
                        <th>توضیح</th>
                        <td><textarea name="subtitle" class="large-text" rows="3"><?php echo esc_textarea($opts['subtitle']); ?></textarea></td>
                    </tr>
                    <tr>
                        <th>روزهای کاری</th>
                        <td>
                            <?php foreach ($labels as $num => $label) : ?>
                                <label style="display:inline-block;margin:0 12px 8px 0;">
                                    <input type="checkbox" name="work_days[]" value="<?php echo esc_attr($num); ?>" <?php checked(in_array((int) $num, $opts['work_days'], true)); ?> />
                                    <?php echo esc_html($label); ?>
                                </label>
                            <?php endforeach; ?>
                            <p class="description">پیش‌فرض ایران: شنبه تا چهارشنبه</p>
                        </td>
                    </tr>
                    <tr>
                        <th>ساعت شروع</th>
                        <td><input type="number" name="slot_start" min="0" max="23" value="<?php echo esc_attr($opts['slot_start']); ?>" /></td>
                    </tr>
                    <tr>
                        <th>ساعت پایان</th>
                        <td><input type="number" name="slot_end" min="1" max="24" value="<?php echo esc_attr($opts['slot_end']); ?>" /></td>
                    </tr>
                    <tr>
                        <th>مدت هر اسلات (دقیقه)</th>
                        <td><input type="number" name="slot_duration_minutes" min="15" step="5" value="<?php echo esc_attr($opts['slot_duration_minutes']); ?>" /></td>
                    </tr>
                    <tr>
                        <th>حداکثر روزهای آینده</th>
                        <td><input type="number" name="max_days_ahead" min="1" max="90" value="<?php echo esc_attr($opts['max_days_ahead']); ?>" /></td>
                    </tr>
                    <tr>
                        <th>تاریخ‌های مسدود</th>
                        <td>
                            <textarea name="blocked_dates" class="large-text" rows="5" dir="ltr" placeholder="2026-07-15"><?php echo esc_textarea($opts['blocked_dates']); ?></textarea>
                            <p class="description">هر خط یک تاریخ به فرمت Y-m-d</p>
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

function weblazem_scheduling_admin_bookings_table() {
    $query = new WP_Query(
        array(
            'post_type'      => 'consult_booking',
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
                <th>تاریخ</th>
                <th>ساعت</th>
                <th>وضعیت</th>
                <th>یادداشت</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php if (!$query->have_posts()) : ?>
                <tr><td colspan="7">رزروی ثبت نشده است.</td></tr>
            <?php else : ?>
                <?php while ($query->have_posts()) : $query->the_post(); ?>
                    <?php
                    $id = get_the_ID();
                    ?>
                    <tr>
                        <td><?php echo esc_html(get_post_meta($id, '_booking_name', true)); ?></td>
                        <td dir="ltr"><?php echo esc_html(get_post_meta($id, '_booking_mobile', true)); ?></td>
                        <td dir="ltr"><?php echo esc_html(get_post_meta($id, '_booking_date', true)); ?></td>
                        <td dir="ltr"><?php echo esc_html(get_post_meta($id, '_booking_time', true)); ?></td>
                        <td><?php echo esc_html(get_post_meta($id, '_booking_status', true) ?: 'pending'); ?></td>
                        <td><?php echo esc_html(wp_trim_words(get_post_meta($id, '_booking_note', true), 12)); ?></td>
                        <td><a href="<?php echo esc_url(get_edit_post_link($id)); ?>">ویرایش</a></td>
                    </tr>
                <?php endwhile; ?>
            <?php endif; ?>
        </tbody>
    </table>
    <?php
    wp_reset_postdata();
}

function weblazem_ajax_scheduling_slots() {
    check_ajax_referer('weblazem_scheduling', 'nonce');

    $opts = weblazem_get_scheduling_options();
    if (empty($opts['enabled'])) {
        wp_send_json_error(array('message' => 'رزرو مشاوره فعلاً غیرفعال است.'), 403);
    }

    $date = isset($_POST['date']) ? sanitize_text_field(wp_unslash($_POST['date'])) : '';
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
        wp_send_json_error(array('message' => 'تاریخ نامعتبر است.'), 400);
    }

    $slots = weblazem_scheduling_generate_slots($date);
    wp_send_json_success(
        array(
            'date'  => $date,
            'slots' => $slots,
        )
    );
}
add_action('wp_ajax_weblazem_scheduling_slots', 'weblazem_ajax_scheduling_slots');
add_action('wp_ajax_nopriv_weblazem_scheduling_slots', 'weblazem_ajax_scheduling_slots');

function weblazem_ajax_scheduling_book() {
    check_ajax_referer('weblazem_scheduling', 'nonce');

    $opts = weblazem_get_scheduling_options();
    if (empty($opts['enabled'])) {
        wp_send_json_error(array('message' => 'رزرو مشاوره فعلاً غیرفعال است.'), 403);
    }

    $rate = weblazem_growth_rate_limit('scheduling_book', 8, 15 * MINUTE_IN_SECONDS);
    if (is_wp_error($rate)) {
        wp_send_json_error(array('message' => $rate->get_error_message()), 429);
    }

    $name   = isset($_POST['name']) ? sanitize_text_field(wp_unslash($_POST['name'])) : '';
    $mobile = isset($_POST['mobile']) ? sanitize_text_field(wp_unslash($_POST['mobile'])) : '';
    $date   = isset($_POST['date']) ? sanitize_text_field(wp_unslash($_POST['date'])) : '';
    $time   = isset($_POST['time']) ? sanitize_text_field(wp_unslash($_POST['time'])) : '';
    $note   = isset($_POST['note']) ? sanitize_textarea_field(wp_unslash($_POST['note'])) : '';

    if ($name === '' || $mobile === '' || $date === '' || $time === '') {
        wp_send_json_error(array('message' => 'نام، موبایل، تاریخ و ساعت الزامی است.'), 400);
    }

    if (!weblazem_scheduling_is_valid_mobile($mobile)) {
        wp_send_json_error(array('message' => 'شماره موبایل معتبر نیست. مثال: 09121234567'), 400);
    }

    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date) || !preg_match('/^\d{2}:\d{2}$/', $time)) {
        wp_send_json_error(array('message' => 'تاریخ یا ساعت نامعتبر است.'), 400);
    }

    $available = weblazem_scheduling_generate_slots($date);
    if (!in_array($time, $available, true)) {
        wp_send_json_error(array('message' => 'این زمان دیگر در دسترس نیست. لطفاً زمان دیگری انتخاب کنید.'), 409);
    }

    $mobile = weblazem_scheduling_normalize_mobile($mobile);

    $post_id = wp_insert_post(
        array(
            'post_type'   => 'consult_booking',
            'post_status' => 'publish',
            'post_title'  => $name . ' — ' . $date . ' ' . $time,
        ),
        true
    );

    if (is_wp_error($post_id) || !$post_id) {
        wp_send_json_error(array('message' => 'خطا در ثبت رزرو. دوباره تلاش کنید.'), 500);
    }

    update_post_meta($post_id, '_booking_date', $date);
    update_post_meta($post_id, '_booking_time', $time);
    update_post_meta($post_id, '_booking_mobile', $mobile);
    update_post_meta($post_id, '_booking_name', $name);
    update_post_meta($post_id, '_booking_note', $note);
    update_post_meta($post_id, '_booking_status', 'pending');

    wp_send_json_success(
        array(
            'message'   => $opts['success_message'],
            'bookingId' => $post_id,
            'date'      => $date,
            'time'      => $time,
        )
    );
}
add_action('wp_ajax_weblazem_scheduling_book', 'weblazem_ajax_scheduling_book');
add_action('wp_ajax_nopriv_weblazem_scheduling_book', 'weblazem_ajax_scheduling_book');

function weblazem_enqueue_scheduling_assets() {
    if (!weblazem_is_scheduling_page()) {
        return;
    }

    $opts = weblazem_get_scheduling_options();

    wp_enqueue_style(
        'weblazem-scheduling',
        get_template_directory_uri() . '/assets/css/scheduling.css',
        array(),
        '1.0.0'
    );

    wp_enqueue_script(
        'weblazem-scheduling',
        get_template_directory_uri() . '/assets/js/scheduling.js',
        array(),
        '1.0.0',
        true
    );

    wp_localize_script(
        'weblazem-scheduling',
        'weblazemScheduling',
        array(
            'ajaxUrl'        => admin_url('admin-ajax.php'),
            'nonce'          => wp_create_nonce('weblazem_scheduling'),
            'dates'          => weblazem_scheduling_available_dates(),
            'successMessage' => $opts['success_message'],
            'errorMessage'   => 'خطایی رخ داد. لطفاً دوباره تلاش کنید.',
            'enabled'        => (bool) $opts['enabled'],
        )
    );
}
add_action('wp_enqueue_scripts', 'weblazem_enqueue_scheduling_assets', 30);
