<?php
/**
 * Ticketing admin UI — list + chat-style ticket management.
 */

function weblazem_ticketing_admin_menu() {
    add_submenu_page(
        'weblazem-theme-options',
        'سیستم تیکت',
        'سیستم تیکت',
        'manage_options',
        'weblazem-ticketing',
        'weblazem_ticketing_admin_page'
    );

    add_submenu_page(
        'weblazem-theme-options',
        'تنظیمات تیکت',
        'تنظیمات تیکت',
        'manage_options',
        'weblazem-ticketing-settings',
        'weblazem_ticketing_settings_page'
    );
}
add_action('admin_menu', 'weblazem_ticketing_admin_menu', 25);

function weblazem_ticketing_admin_assets($hook) {
    if (strpos($hook, 'weblazem-ticketing') === false) {
        return;
    }

    wp_enqueue_style(
        'weblazem-ticketing-admin',
        get_template_directory_uri() . '/assets/css/ticketing-admin.css',
        array(),
        '1.0.0'
    );

    wp_enqueue_script(
        'weblazem-ticketing-admin',
        get_template_directory_uri() . '/assets/js/ticketing-admin.js',
        array('jquery'),
        '1.0.0',
        true
    );

    wp_localize_script(
        'weblazem-ticketing-admin',
        'weblazemTicketAdmin',
        array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce'   => wp_create_nonce('weblazem_ticketing_admin'),
        )
    );
}
add_action('admin_enqueue_scripts', 'weblazem_ticketing_admin_assets');

function weblazem_ticketing_settings_page() {
    if (isset($_POST['weblazem_ticket_settings_nonce']) && wp_verify_nonce($_POST['weblazem_ticket_settings_nonce'], 'weblazem_ticket_settings')) {
        update_option('weblazem_ticket_access_code', sanitize_text_field(wp_unslash($_POST['weblazem_ticket_access_code'] ?? '12345')));
        update_option('weblazem_ticket_section_title', sanitize_text_field(wp_unslash($_POST['weblazem_ticket_section_title'] ?? '')));
        update_option('weblazem_ticket_section_subtitle', sanitize_textarea_field(wp_unslash($_POST['weblazem_ticket_section_subtitle'] ?? '')));
        echo '<div class="notice notice-success is-dismissible"><p>تنظیمات ذخیره شد.</p></div>';
    }

    $code     = weblazem_get_ticket_access_code();
    $title    = get_option('weblazem_ticket_section_title', 'ثبت تیکت و پیگیری تسک');
    $subtitle = get_option('weblazem_ticket_section_subtitle', '');
    ?>
    <div class="wrap" dir="rtl">
        <h1>تنظیمات سیستم تیکت</h1>
        <form method="post">
            <?php wp_nonce_field('weblazem_ticket_settings', 'weblazem_ticket_settings_nonce'); ?>
            <table class="form-table">
                <tr>
                    <th>کد ورود سندباکس</th>
                    <td>
                        <input type="text" name="weblazem_ticket_access_code" class="regular-text" dir="ltr" value="<?php echo esc_attr($code); ?>" />
                        <p class="description">کاربران با این کد وارد بخش تیکت می‌شوند. پیش‌فرض: 12345</p>
                    </td>
                </tr>
                <tr>
                    <th>عنوان سکشن صفحه اصلی</th>
                    <td><input type="text" name="weblazem_ticket_section_title" class="large-text" value="<?php echo esc_attr($title); ?>" /></td>
                </tr>
                <tr>
                    <th>توضیح سکشن</th>
                    <td><textarea name="weblazem_ticket_section_subtitle" class="large-text" rows="3"><?php echo esc_textarea($subtitle); ?></textarea></td>
                </tr>
            </table>
            <?php submit_button('ذخیره تنظیمات'); ?>
        </form>
    </div>
    <?php
}

function weblazem_ticketing_admin_page() {
    $ticket_id = isset($_GET['ticket_id']) ? absint($_GET['ticket_id']) : 0;

    if ($ticket_id) {
        weblazem_ticketing_admin_single($ticket_id);
        return;
    }

    $status_filter = isset($_GET['status']) ? sanitize_text_field(wp_unslash($_GET['status'])) : '';
    $search        = isset($_GET['s']) ? sanitize_text_field(wp_unslash($_GET['s'])) : '';

    $args = array(
        'post_type'      => 'support_ticket',
        'post_status'    => 'publish',
        'posts_per_page' => 50,
        'meta_key'       => '_ticket_updated_at',
        'orderby'        => 'meta_value',
        'order'          => 'DESC',
    );

    $meta_query = array();
    if ($status_filter !== '') {
        $meta_query[] = array(
            'key'   => '_ticket_status',
            'value' => $status_filter,
        );
    }
    if (!empty($meta_query)) {
        $args['meta_query'] = $meta_query;
    }
    if ($search !== '') {
        $args['s'] = $search;
    }

    $query = new WP_Query($args);
    ?>
    <div class="wrap weblazem-ticket-admin" dir="rtl">
        <h1>سیستم تیکت پشتیبانی</h1>
        <p>مدیریت تیکت‌های کاربران، پاسخ‌ها و وضعیت پیگیری پروژه‌ها.</p>

        <form method="get" class="weblazem-ticket-admin__filters">
            <input type="hidden" name="page" value="weblazem-ticketing" />
            <select name="status">
                <option value="">همه وضعیت‌ها</option>
                <?php foreach (weblazem_ticket_statuses() as $key => $label) : ?>
                    <option value="<?php echo esc_attr($key); ?>" <?php selected($status_filter, $key); ?>><?php echo esc_html($label); ?></option>
                <?php endforeach; ?>
            </select>
            <input type="search" name="s" value="<?php echo esc_attr($search); ?>" placeholder="جستجو در عنوان..." />
            <button type="submit" class="button">فیلتر</button>
            <a class="button" href="<?php echo esc_url(admin_url('admin.php?page=weblazem-ticketing-settings')); ?>">تنظیمات</a>
        </form>

        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th>کد</th>
                    <th>عنوان</th>
                    <th>کاربر</th>
                    <th>موضوع</th>
                    <th>اولویت</th>
                    <th>وضعیت</th>
                    <th>آخرین بروزرسانی</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php if ($query->have_posts()) : ?>
                    <?php while ($query->have_posts()) : $query->the_post();
                        $id = get_the_ID();
                        $status = get_post_meta($id, '_ticket_status', true);
                        ?>
                        <tr>
                            <td><code><?php echo esc_html(get_post_meta($id, '_ticket_code', true)); ?></code></td>
                            <td><strong><?php echo esc_html(get_the_title()); ?></strong></td>
                            <td><?php echo esc_html(get_post_meta($id, '_ticket_username', true)); ?></td>
                            <td><?php echo esc_html(get_post_meta($id, '_ticket_subject_label', true)); ?></td>
                            <td><?php echo esc_html(weblazem_ticket_priority_label(get_post_meta($id, '_ticket_priority', true))); ?></td>
                            <td><span class="weblazem-ticket-status weblazem-ticket-status--<?php echo esc_attr($status); ?>"><?php echo esc_html(weblazem_ticket_status_label($status)); ?></span></td>
                            <td><?php echo esc_html(get_post_meta($id, '_ticket_updated_at', true)); ?></td>
                            <td>
                                <a class="button button-primary" href="<?php echo esc_url(admin_url('admin.php?page=weblazem-ticketing&ticket_id=' . $id)); ?>">مشاهده / پاسخ</a>
                            </td>
                        </tr>
                    <?php endwhile; wp_reset_postdata(); ?>
                <?php else : ?>
                    <tr><td colspan="8">تیکتی یافت نشد.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php
}

function weblazem_ticketing_admin_single($ticket_id) {
    $post = get_post($ticket_id);
    if (!$post || $post->post_type !== 'support_ticket') {
        echo '<div class="wrap"><p>تیکت یافت نشد.</p></div>';
        return;
    }

    $ticket  = weblazem_format_ticket_for_api($post);
    $replies = $ticket['replies'];
    ?>
    <div class="wrap weblazem-ticket-admin weblazem-ticket-admin--single" dir="rtl">
        <p><a href="<?php echo esc_url(admin_url('admin.php?page=weblazem-ticketing')); ?>">&larr; بازگشت به لیست تیکت‌ها</a></p>

        <div class="weblazem-ticket-admin__layout">
            <div class="weblazem-ticket-admin__chat-panel">
                <div class="weblazem-ticket-admin__chat-head">
                    <h1><?php echo esc_html($ticket['title']); ?></h1>
                    <span class="weblazem-ticket-status weblazem-ticket-status--<?php echo esc_attr($ticket['status']); ?>"><?php echo esc_html($ticket['statusLabel']); ?></span>
                </div>

                <div class="weblazem-ticket-chat" id="weblazem-admin-ticket-chat" data-ticket-id="<?php echo esc_attr($ticket_id); ?>">
                    <?php foreach ($replies as $reply) :
                        $is_admin = ($reply['author_type'] ?? '') === 'admin';
                        ?>
                        <div class="weblazem-ticket-chat__bubble <?php echo $is_admin ? 'is-admin' : 'is-user'; ?>">
                            <div class="weblazem-ticket-chat__meta">
                                <strong><?php echo esc_html($reply['author_name'] ?: ($is_admin ? 'پشتیبانی' : 'کاربر')); ?></strong>
                                <span><?php echo esc_html($reply['created_at'] ?? ''); ?></span>
                            </div>
                            <div class="weblazem-ticket-chat__text"><?php echo wp_kses_post(nl2br($reply['message'] ?? '')); ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <form class="weblazem-ticket-admin__reply-form" id="weblazem-admin-ticket-reply-form">
                    <textarea name="message" rows="4" placeholder="پاسخ خود را بنویسید..." required></textarea>
                    <div class="weblazem-ticket-admin__reply-actions">
                        <select name="status">
                            <?php foreach (weblazem_ticket_statuses() as $key => $label) : ?>
                                <option value="<?php echo esc_attr($key); ?>" <?php selected($ticket['status'], $key); ?>><?php echo esc_html($label); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit" class="button button-primary">ارسال پاسخ</button>
                    </div>
                    <p class="weblazem-ticket-admin__feedback" id="weblazem-admin-ticket-feedback"></p>
                </form>
            </div>

            <aside class="weblazem-ticket-admin__info">
                <h2>اطلاعات تیکت</h2>
                <table class="form-table">
                    <tr><th>کد تیکت</th><td><code><?php echo esc_html($ticket['code']); ?></code></td></tr>
                    <tr><th>نام کاربری</th><td><?php echo esc_html($ticket['username']); ?></td></tr>
                    <tr><th>موبایل</th><td><?php echo esc_html($ticket['mobile'] ?: '—'); ?></td></tr>
                    <tr><th>ایمیل</th><td><?php echo esc_html($ticket['email'] ?: '—'); ?></td></tr>
                    <tr><th>موضوع</th><td><?php echo esc_html($ticket['subjectLabel']); ?></td></tr>
                    <tr><th>اولویت</th><td><?php echo esc_html($ticket['priorityLabel']); ?></td></tr>
                    <tr><th>نام پروژه</th><td><?php echo esc_html($ticket['projectName'] ?: '—'); ?></td></tr>
                    <tr><th>تاریخ ایجاد</th><td><?php echo esc_html($ticket['createdAt']); ?></td></tr>
                    <tr><th>آخرین بروزرسانی</th><td><?php echo esc_html($ticket['updatedAt']); ?></td></tr>
                </table>
            </aside>
        </div>
    </div>
    <?php
}
