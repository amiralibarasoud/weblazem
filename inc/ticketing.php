<?php
/**
 * Support ticketing — post type, helpers, defaults.
 */

function weblazem_ticket_subjects() {
    return array(
        'web_design'      => 'طراحی سایت',
        'redesign'        => 'بازطراحی سایت',
        'seo'             => 'سئو و بهینه‌سازی',
        'content'         => 'محتوا و تولید محتوا',
        'support'         => 'پشتیبانی فنی',
        'hosting'         => 'هاست و دامنه',
        'ecommerce'       => 'فروشگاه اینترنتی',
        'change_request'  => 'درخواست تغییرات پروژه',
        'bug'             => 'گزارش باگ / مشکل',
        'consultation'    => 'مشاوره پروژه',
        'other'           => 'سایر موضوعات',
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

function weblazem_get_ticket_access_code() {
    return (string) get_option('weblazem_ticket_access_code', '12345');
}

function weblazem_ensure_ticket_defaults() {
    if (get_option('weblazem_ticket_access_code') === false) {
        update_option('weblazem_ticket_access_code', '12345');
    }
    if (get_option('weblazem_ticket_section_title') === false) {
        update_option('weblazem_ticket_section_title', 'ثبت تیکت و پیگیری تسک');
    }
    if (get_option('weblazem_ticket_section_subtitle') === false) {
        update_option('weblazem_ticket_section_subtitle', 'پروژه طراحی سایت خود را پیگیری کنید، تیکت ثبت کنید و پاسخ تیم وب‌لازم را دریافت نمایید.');
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

function weblazem_get_ticket_replies($ticket_id) {
    $replies = get_post_meta($ticket_id, '_ticket_replies', true);
    return is_array($replies) ? $replies : array();
}

function weblazem_add_ticket_reply($ticket_id, $message, $author_type = 'user', $author_name = '') {
    $message = trim(wp_kses_post($message));
    if ($message === '') {
        return new WP_Error('empty', 'متن پیام خالی است.');
    }

    $replies   = weblazem_get_ticket_replies($ticket_id);
    $replies[] = array(
        'id'          => uniqid('msg_', true),
        'author_type' => in_array($author_type, array('user', 'admin'), true) ? $author_type : 'user',
        'author_name' => sanitize_text_field($author_name),
        'message'     => $message,
        'created_at'  => current_time('mysql'),
    );

    update_post_meta($ticket_id, '_ticket_replies', $replies);
    update_post_meta($ticket_id, '_ticket_updated_at', current_time('mysql'));

    return $replies;
}

function weblazem_create_support_ticket($data) {
    $username = sanitize_text_field($data['username'] ?? '');
    $subject  = sanitize_text_field($data['subject'] ?? '');
    $title    = sanitize_text_field($data['title'] ?? '');
    $message  = trim(wp_kses_post($data['message'] ?? ''));
    $mobile   = sanitize_text_field($data['mobile'] ?? '');
    $email    = sanitize_email($data['email'] ?? '');
    $priority = sanitize_text_field($data['priority'] ?? 'normal');
    $project  = sanitize_text_field($data['project_name'] ?? '');

    if ($username === '' || $title === '' || $message === '') {
        return new WP_Error('required', 'نام کاربری، عنوان و متن تیکت الزامی است.');
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

    update_post_meta($post_id, '_ticket_code', $code);
    update_post_meta($post_id, '_ticket_username', $username);
    update_post_meta($post_id, '_ticket_mobile', $mobile);
    update_post_meta($post_id, '_ticket_email', $email);
    update_post_meta($post_id, '_ticket_subject', $subject);
    update_post_meta($post_id, '_ticket_subject_label', weblazem_ticket_subject_label($subject));
    update_post_meta($post_id, '_ticket_priority', $priority);
    update_post_meta($post_id, '_ticket_status', 'open');
    update_post_meta($post_id, '_ticket_project_name', $project);
    update_post_meta($post_id, '_ticket_updated_at', current_time('mysql'));
    update_post_meta($post_id, '_ticket_ip', isset($_SERVER['REMOTE_ADDR']) ? sanitize_text_field(wp_unslash($_SERVER['REMOTE_ADDR'])) : '');

    weblazem_add_ticket_reply($post_id, $message, 'user', $username);

    return $post_id;
}

function weblazem_get_tickets_by_username($username) {
    $username = sanitize_text_field($username);
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
        'id'           => $id,
        'code'         => get_post_meta($id, '_ticket_code', true),
        'title'        => get_the_title($id),
        'username'     => get_post_meta($id, '_ticket_username', true),
        'mobile'       => get_post_meta($id, '_ticket_mobile', true),
        'email'        => get_post_meta($id, '_ticket_email', true),
        'subject'      => get_post_meta($id, '_ticket_subject', true),
        'subjectLabel' => get_post_meta($id, '_ticket_subject_label', true),
        'priority'     => get_post_meta($id, '_ticket_priority', true),
        'priorityLabel'=> weblazem_ticket_priority_label(get_post_meta($id, '_ticket_priority', true)),
        'status'       => get_post_meta($id, '_ticket_status', true),
        'statusLabel'  => weblazem_ticket_status_label(get_post_meta($id, '_ticket_status', true)),
        'projectName'  => get_post_meta($id, '_ticket_project_name', true),
        'createdAt'    => get_the_date('Y-m-d H:i', $id),
        'updatedAt'    => get_post_meta($id, '_ticket_updated_at', true),
        'replies'      => weblazem_get_ticket_replies($id),
    );
}

function weblazem_ticket_user_owns($ticket_id, $username) {
    return strcasecmp((string) get_post_meta($ticket_id, '_ticket_username', true), (string) $username) === 0;
}
