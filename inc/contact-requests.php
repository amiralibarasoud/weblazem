<?php
/**
 * Contact form submissions — custom post type.
 */

function weblazem_register_contact_request_post_type() {
    $labels = array(
        'name'               => 'پیام‌های تماس',
        'singular_name'      => 'پیام تماس',
        'menu_name'          => 'پیام‌های تماس',
        'all_items'          => 'همه پیام‌ها',
        'view_item'          => 'مشاهده پیام',
        'search_items'       => 'جستجوی پیام',
        'not_found'          => 'پیامی یافت نشد.',
        'not_found_in_trash' => 'پیامی در زباله‌دان یافت نشد.',
    );

    register_post_type(
        'contact_request',
        array(
            'labels'              => $labels,
            'public'              => false,
            'publicly_queryable'  => false,
            'show_ui'             => true,
            'show_in_menu'        => false,
            'show_in_rest'        => false,
            'capability_type'     => 'post',
            'map_meta_cap'        => true,
            'hierarchical'        => false,
            'supports'            => array('title'),
            'has_archive'         => false,
        )
    );
}
add_action('init', 'weblazem_register_contact_request_post_type');

function weblazem_contact_request_columns($columns) {
    $new = array();
    foreach ($columns as $key => $label) {
        if ($key === 'title') {
            $new['title'] = 'نام';
            continue;
        }
        if ($key === 'date') {
            $new['contact_email'] = 'ایمیل';
            $new['contact_phone'] = 'موبایل';
            $new['contact_sms']   = 'وضعیت پیامک';
            $new['date']         = $label;
            continue;
        }
        $new[$key] = $label;
    }
    return $new;
}
add_filter('manage_contact_request_posts_columns', 'weblazem_contact_request_columns');

function weblazem_contact_request_column_content($column, $post_id) {
    switch ($column) {
        case 'contact_email':
            echo esc_html(get_post_meta($post_id, '_contact_email', true));
            break;
        case 'contact_phone':
            echo esc_html(get_post_meta($post_id, '_contact_phone', true));
            break;
        case 'contact_sms':
            $status = get_post_meta($post_id, '_contact_sms_status', true);
            if ($status === 'sent') {
                echo '<span style="color:#15803d;">ارسال شد</span>';
            } else {
                echo '<span style="color:#b45309;">' . esc_html($status ?: 'نامشخص') . '</span>';
            }
            break;
    }
}
add_action('manage_contact_request_posts_custom_column', 'weblazem_contact_request_column_content', 10, 2);

function weblazem_contact_request_meta_box() {
    add_meta_box(
        'weblazem_contact_request_details',
        'جزئیات پیام',
        'weblazem_contact_request_meta_box_render',
        'contact_request',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'weblazem_contact_request_meta_box');

function weblazem_contact_request_meta_box_render($post) {
    $fields = array(
        'نام'            => get_post_meta($post->ID, '_contact_first_name', true),
        'نام خانوادگی'   => get_post_meta($post->ID, '_contact_last_name', true),
        'ایمیل'          => get_post_meta($post->ID, '_contact_email', true),
        'موبایل'         => get_post_meta($post->ID, '_contact_phone', true),
        'پیام'           => get_post_meta($post->ID, '_contact_message', true),
        'IP'             => get_post_meta($post->ID, '_contact_ip', true),
        'وضعیت پیامک'   => get_post_meta($post->ID, '_contact_sms_status', true),
        'پاسخ sms.ir'    => get_post_meta($post->ID, '_contact_sms_response', true),
    );

    echo '<table class="form-table"><tbody>';
    foreach ($fields as $label => $value) {
        echo '<tr><th>' . esc_html($label) . '</th><td>';
        if ($label === 'پیام') {
            echo '<div style="white-space:pre-wrap;">' . esc_html((string) $value) . '</div>';
        } else {
            echo esc_html((string) $value);
        }
        echo '</td></tr>';
    }
    echo '</tbody></table>';
}

function weblazem_save_contact_request($data) {
    $title = trim($data['first_name'] . ' ' . $data['last_name']);
    if ($title === '') {
        $title = $data['email'];
    }

    $post_id = wp_insert_post(array(
        'post_type'   => 'contact_request',
        'post_status' => 'publish',
        'post_title'  => $title,
    ), true);

    if (is_wp_error($post_id)) {
        return $post_id;
    }

    update_post_meta($post_id, '_contact_first_name', $data['first_name']);
    update_post_meta($post_id, '_contact_last_name', $data['last_name']);
    update_post_meta($post_id, '_contact_email', $data['email']);
    update_post_meta($post_id, '_contact_phone', $data['phone']);
    update_post_meta($post_id, '_contact_message', $data['message']);
    update_post_meta($post_id, '_contact_ip', $data['ip']);
    update_post_meta($post_id, '_contact_sms_status', $data['sms_status']);
    update_post_meta($post_id, '_contact_sms_response', $data['sms_response']);

    return $post_id;
}
