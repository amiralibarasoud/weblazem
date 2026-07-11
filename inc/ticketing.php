<?php
/**
 * Support ticketing — post type, helpers, defaults, secure uploads.
 */

function weblazem_ticket_subjects() {
    return array(
        'web_design'     => 'طراحی سایت',
        'redesign'       => 'بازطراحی سایت',
        'seo'            => 'سئو و بهینه‌سازی',
        'content'        => 'محتوا و تولید محتوا',
        'support'        => 'پشتیبانی فنی',
        'hosting'        => 'هاست و دامنه',
        'ecommerce'      => 'فروشگاه اینترنتی',
        'change_request' => 'درخواست تغییرات پروژه',
        'bug'            => 'گزارش باگ / مشکل',
        'consultation'   => 'مشاوره پروژه',
        'other'          => 'سایر موضوعات',
    );
}

function weblazem_ticket_statuses() {
    return array(
        'open'        => 'باز',
        'in_progress' => 'در حال بررسی',
        'answered'    => 'پاسخ داده شده',
        'waiting'     => 'در انتظار پاسخ کاربر',
        'closed'      => 'بسته شده',
    );
}

function weblazem_ticket_priorities() {
    return array(
        'low'    => 'کم',
        'normal' => 'عادی',
        'high'   => 'بالا',
        'urgent' => 'فوری',
    );
}

function weblazem_ticket_allowed_mimes() {
    return array(
        'jpg|jpeg|jpe' => 'image/jpeg',
        'png'          => 'image/png',
        'gif'          => 'image/gif',
        'webp'         => 'image/webp',
        'pdf'          => 'application/pdf',
        'zip'          => 'application/zip',
        'doc'          => 'application/msword',
        'docx'         => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    );
}

function weblazem_ticket_max_upload_bytes() {
    return 3 * 1024 * 1024; // 3MB
}

function weblazem_get_ticket_access_code() {
    return (string) get_option('weblazem_ticket_access_code', '12345');
}

function weblazem_ensure_ticket_defaults() {
    $defaults = array(
        'weblazem_ticket_access_code'      => '12345',
        'weblazem_ticket_section_title'    => 'حساب کاربری مشتری',
        'weblazem_ticket_section_subtitle' => 'تیکت‌ها، بریف‌ها، پیشنهادهای قیمت و وضعیت پروژه را از یک پنل پیگیری کنید.',
        'weblazem_ticket_section_btn_text' => 'ورود به حساب کاربری',
        'weblazem_ticket_page_title'       => 'حساب کاربری مشتری',
        'weblazem_ticket_page_subtitle'    => 'با شماره موبایل وارد شوید؛ تیکت‌ها، بریف‌ها، پیشنهادها و وضعیت پروژه‌ها را مدیریت کنید.',
        'weblazem_ticket_success_message'  => 'تیکت شما با موفقیت ثبت شد. کارشناسان وب‌لازم به‌زودی پاسخ می‌دهند.',
    );

    foreach ($defaults as $key => $value) {
        if (get_option($key) === false) {
            update_option($key, $value);
        }
    }

    // Rename existing ticket page titles to client account (slug stays sabt-ticket).
    $rename_map = array(
        'weblazem_ticket_page_title'       => array('ثبت تیکت و پیگیری', 'حساب کاربری مشتری'),
        'weblazem_ticket_section_title'    => array('ثبت تیکت و پیگیری تسک', 'حساب کاربری مشتری'),
        'weblazem_ticket_section_btn_text' => array('ورود به پنل تیکت', 'ورود به حساب کاربری'),
    );
    foreach ($rename_map as $option_key => $pair) {
        $current = get_option($option_key, '');
        if ($current === $pair[0]) {
            update_option($option_key, $pair[1]);
        }
    }

    $old_sub = get_option('weblazem_ticket_page_subtitle', '');
    if ($old_sub === 'با شماره موبایل وارد شوید، تیکت ثبت کنید و پاسخ تیم را به‌صورت گفتگو دریافت کنید.') {
        update_option('weblazem_ticket_page_subtitle', $defaults['weblazem_ticket_page_subtitle']);
    }
}
add_action('init', 'weblazem_ensure_ticket_defaults', 12);

function weblazem_register_support_ticket_post_type() {
    $labels = array(
        'name'               => 'تیکت‌های پشتیبانی',
        'singular_name'      => 'تیکت',
        'menu_name'          => 'سیستم تیکت',
        'all_items'          => 'همه تیکت‌ها',
        'add_new'            => 'افزودن تیکت',
        'add_new_item'       => 'افزودن تیکت جدید',
        'edit_item'          => 'ویرایش تیکت',
        'view_item'          => 'مشاهده تیکت',
        'search_items'       => 'جستجوی تیکت',
        'not_found'          => 'تیکتی یافت نشد.',
        'not_found_in_trash' => 'تیکتی در زباله‌دان یافت نشد.',
    );

    register_post_type(
        'support_ticket',
        array(
            'labels'             => $labels,
            'public'             => false,
            'publicly_queryable' => false,
            'show_ui'            => true,
            'show_in_menu'       => false,
            'show_in_rest'       => false,
            'capability_type'    => 'post',
            'map_meta_cap'       => true,
            'hierarchical'       => false,
            'supports'           => array('title'),
            'has_archive'        => false,
        )
    );
}
add_action('init', 'weblazem_register_support_ticket_post_type');

function weblazem_ticket_status_label($status) {
    $statuses = weblazem_ticket_statuses();
    return isset($statuses[$status]) ? $statuses[$status] : $status;
}

function weblazem_ticket_subject_label($subject) {
    $subjects = weblazem_ticket_subjects();
    return isset($subjects[$subject]) ? $subjects[$subject] : $subject;
}

function weblazem_ticket_priority_label($priority) {
    $priorities = weblazem_ticket_priorities();
    return isset($priorities[$priority]) ? $priorities[$priority] : $priority;
}

function weblazem_ticket_normalize_mobile($phone) {
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

function weblazem_ticket_is_valid_mobile($phone) {
    if (function_exists('weblazem_is_valid_iran_mobile')) {
        return weblazem_is_valid_iran_mobile($phone);
    }

    return (bool) preg_match('/^09\d{9}$/', weblazem_ticket_normalize_mobile($phone));
}

function weblazem_get_ticket_replies($ticket_id) {
    $replies = get_post_meta($ticket_id, '_ticket_replies', true);
    return is_array($replies) ? $replies : array();
}

function weblazem_get_ticket_attachments($ticket_id) {
    $files = get_post_meta($ticket_id, '_ticket_attachments', true);
    return is_array($files) ? $files : array();
}

function weblazem_add_ticket_reply($ticket_id, $message, $author_type = 'user', $author_name = '', $attachments = array()) {
    $message = trim(wp_kses_post($message));
    if ($message === '' && empty($attachments)) {
        return new WP_Error('empty', 'متن پیام خالی است.');
    }

    $replies   = weblazem_get_ticket_replies($ticket_id);
    $replies[] = array(
        'id'          => uniqid('msg_', true),
        'author_type' => in_array($author_type, array('user', 'admin'), true) ? $author_type : 'user',
        'author_name' => sanitize_text_field($author_name),
        'message'     => $message,
        'attachments' => is_array($attachments) ? $attachments : array(),
        'created_at'  => current_time('mysql'),
    );

    update_post_meta($ticket_id, '_ticket_replies', $replies);
    update_post_meta($ticket_id, '_ticket_updated_at', current_time('mysql'));

    return $replies;
}

/**
 * Secure ticket file upload (max 3MB, whitelist mime/extension).
 *
 * @param array $file $_FILES item
 * @return array|WP_Error attachment meta
 */
function weblazem_ticket_handle_upload($file) {
    if (empty($file) || empty($file['name']) || empty($file['tmp_name'])) {
        return new WP_Error('no_file', 'فایلی ارسال نشده است.');
    }

    if (!empty($file['error']) && (int) $file['error'] !== UPLOAD_ERR_OK) {
        return new WP_Error('upload_error', 'خطا در آپلود فایل.');
    }

    if (!is_uploaded_file($file['tmp_name'])) {
        return new WP_Error('invalid_upload', 'فایل آپلود نامعتبر است.');
    }

    $size = isset($file['size']) ? (int) $file['size'] : 0;
    if ($size <= 0 || $size > weblazem_ticket_max_upload_bytes()) {
        return new WP_Error('file_size', 'حجم فایل نباید بیشتر از ۳ مگابایت باشد.');
    }

    $filename  = sanitize_file_name(wp_basename($file['name']));
    $filetype  = wp_check_filetype_and_ext($file['tmp_name'], $filename, weblazem_ticket_allowed_mimes());
    $ext       = isset($filetype['ext']) ? $filetype['ext'] : '';
    $type      = isset($filetype['type']) ? $filetype['type'] : '';

    if (!$ext || !$type) {
        return new WP_Error('file_type', 'فرمت فایل مجاز نیست. فقط تصویر، PDF، ZIP و Word مجاز است.');
    }

    // Extra MIME sniff when available.
    if (function_exists('finfo_open')) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        if ($finfo) {
            $real_mime = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);
            $allowed_mimes = array_values(weblazem_ticket_allowed_mimes());
            if ($real_mime && !in_array($real_mime, $allowed_mimes, true) && $real_mime !== 'application/x-zip-compressed') {
                // Allow common zip aliases and docx zip containers already checked by WP.
                if (!in_array($real_mime, array('application/octet-stream', 'application/x-empty'), true)) {
                    // Still accept if WP validated ext+type; reject obvious PHP/html.
                    if (preg_match('/^(text\/html|application\/x-httpd-php|text\/x-php)/i', $real_mime)) {
                        return new WP_Error('file_type', 'فرمت فایل مجاز نیست.');
                    }
                }
            }
        }
    }

    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/media.php';
    require_once ABSPATH . 'wp-admin/includes/image.php';

    $overrides = array(
        'test_form'                => false,
        'mimes'                    => weblazem_ticket_allowed_mimes(),
        'unique_filename_callback' => 'weblazem_ticket_unique_filename',
    );

    $upload = wp_handle_upload($file, $overrides);
    if (isset($upload['error'])) {
        return new WP_Error('upload_failed', $upload['error']);
    }

    $attachment = array(
        'post_mime_type' => $upload['type'],
        'post_title'     => preg_replace('/\.[^.]+$/', '', $filename),
        'post_content'   => '',
        'post_status'    => 'inherit',
    );

    $attach_id = wp_insert_attachment($attachment, $upload['file']);
    if (is_wp_error($attach_id) || !$attach_id) {
        return new WP_Error('attach_failed', 'ذخیره فایل ناموفق بود.');
    }

    $meta = wp_generate_attachment_metadata($attach_id, $upload['file']);
    if (!empty($meta)) {
        wp_update_attachment_metadata($attach_id, $meta);
    }

    // Mark as private ticket attachment (not listed publicly).
    update_post_meta($attach_id, '_weblazem_ticket_file', '1');

    return array(
        'id'       => (int) $attach_id,
        'url'      => esc_url_raw($upload['url']),
        'name'     => $filename,
        'type'     => $upload['type'],
        'size'     => $size,
        'uploaded' => current_time('mysql'),
    );
}

function weblazem_ticket_unique_filename($dir, $name, $ext) {
    unset($dir, $ext);
    $safe = sanitize_file_name($name);
    return 'ticket-' . wp_generate_password(12, false, false) . '-' . $safe;
}

function weblazem_create_support_ticket($data, $attachment = null) {
    $mobile  = weblazem_ticket_normalize_mobile($data['mobile'] ?? ($data['username'] ?? ''));
    $subject = sanitize_text_field($data['subject'] ?? '');
    $title   = sanitize_text_field($data['title'] ?? '');
    $message = trim(wp_kses_post($data['message'] ?? ''));
    $email   = sanitize_email($data['email'] ?? '');
    $priority = sanitize_text_field($data['priority'] ?? 'normal');
    $project  = sanitize_text_field($data['project_name'] ?? '');

    if (!weblazem_ticket_is_valid_mobile($mobile)) {
        return new WP_Error('mobile', 'شماره موبایل معتبر نیست.');
    }

    if ($title === '' || $message === '') {
        return new WP_Error('required', 'عنوان و متن تیکت الزامی است.');
    }

    if (mb_strlen($title) > 160) {
        return new WP_Error('title_long', 'عنوان تیکت خیلی طولانی است.');
    }

    if (mb_strlen(wp_strip_all_tags($message)) > 5000) {
        return new WP_Error('message_long', 'متن تیکت خیلی طولانی است.');
    }

    $subjects = weblazem_ticket_subjects();
    if (!isset($subjects[$subject])) {
        $subject = 'other';
    }

    $priorities = weblazem_ticket_priorities();
    if (!isset($priorities[$priority])) {
        $priority = 'normal';
    }

    $post_id = wp_insert_post(
        array(
            'post_type'   => 'support_ticket',
            'post_status' => 'publish',
            'post_title'  => $title,
        ),
        true
    );

    if (is_wp_error($post_id)) {
        return $post_id;
    }

    $code = 'WL-' . strtoupper(wp_generate_password(6, false, false));
    $attachments = array();

    if (is_array($attachment) && !empty($attachment['id'])) {
        $attachments[] = $attachment;
        wp_update_post(
            array(
                'ID'          => (int) $attachment['id'],
                'post_parent' => $post_id,
            )
        );
    }

    update_post_meta($post_id, '_ticket_code', $code);
    update_post_meta($post_id, '_ticket_username', $mobile);
    update_post_meta($post_id, '_ticket_mobile', $mobile);
    update_post_meta($post_id, '_ticket_email', $email);
    update_post_meta($post_id, '_ticket_subject', $subject);
    update_post_meta($post_id, '_ticket_subject_label', weblazem_ticket_subject_label($subject));
    update_post_meta($post_id, '_ticket_priority', $priority);
    update_post_meta($post_id, '_ticket_status', 'open');
    update_post_meta($post_id, '_ticket_project_name', $project);
    update_post_meta($post_id, '_ticket_attachments', $attachments);
    update_post_meta($post_id, '_ticket_updated_at', current_time('mysql'));
    update_post_meta($post_id, '_ticket_ip', isset($_SERVER['REMOTE_ADDR']) ? sanitize_text_field(wp_unslash($_SERVER['REMOTE_ADDR'])) : '');

    weblazem_add_ticket_reply($post_id, $message, 'user', $mobile, $attachments);

    return $post_id;
}

function weblazem_get_tickets_by_username($username) {
    $username = weblazem_ticket_normalize_mobile($username);
    if ($username === '') {
        return array();
    }

    $query = new WP_Query(
        array(
            'post_type'      => 'support_ticket',
            'post_status'    => 'publish',
            'posts_per_page' => 50,
            'meta_key'       => '_ticket_updated_at',
            'orderby'        => 'meta_value',
            'order'          => 'DESC',
            'meta_query'     => array(
                array(
                    'key'   => '_ticket_username',
                    'value' => $username,
                ),
            ),
        )
    );

    return $query->posts;
}

function weblazem_format_ticket_for_api($post) {
    $id = $post->ID;

    return array(
        'id'            => $id,
        'code'          => get_post_meta($id, '_ticket_code', true),
        'title'         => get_the_title($id),
        'username'      => get_post_meta($id, '_ticket_username', true),
        'mobile'        => get_post_meta($id, '_ticket_mobile', true),
        'email'         => get_post_meta($id, '_ticket_email', true),
        'subject'       => get_post_meta($id, '_ticket_subject', true),
        'subjectLabel'  => get_post_meta($id, '_ticket_subject_label', true),
        'priority'      => get_post_meta($id, '_ticket_priority', true),
        'priorityLabel' => weblazem_ticket_priority_label(get_post_meta($id, '_ticket_priority', true)),
        'status'        => get_post_meta($id, '_ticket_status', true),
        'statusLabel'   => weblazem_ticket_status_label(get_post_meta($id, '_ticket_status', true)),
        'projectName'   => get_post_meta($id, '_ticket_project_name', true),
        'createdAt'     => get_the_date('Y-m-d H:i', $id),
        'updatedAt'     => get_post_meta($id, '_ticket_updated_at', true),
        'attachments'   => weblazem_get_ticket_attachments($id),
        'replies'       => weblazem_get_ticket_replies($id),
    );
}

function weblazem_ticket_user_owns($ticket_id, $username) {
    $stored = weblazem_ticket_normalize_mobile(get_post_meta($ticket_id, '_ticket_username', true));
    $user   = weblazem_ticket_normalize_mobile($username);

    return $stored !== '' && hash_equals($stored, $user);
}
