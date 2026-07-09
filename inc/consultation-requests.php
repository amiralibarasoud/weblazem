<?php
/**
 * Consultation request log (custom post type).
 */

function weblazem_register_consultation_request_post_type() {
    $labels = array(
        'name'               => 'درخواست‌های مشاوره',
        'singular_name'      => 'درخواست مشاوره',
        'menu_name'          => 'درخواست‌های مشاوره',
        'all_items'          => 'همه درخواست‌ها',
        'view_item'          => 'مشاهده درخواست',
        'search_items'       => 'جستجوی درخواست',
        'not_found'          => 'درخواستی یافت نشد.',
        'not_found_in_trash' => 'درخواستی در زباله‌دان یافت نشد.',
    );

    register_post_type(
        'consultation_request',
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
add_action('init', 'weblazem_register_consultation_request_post_type');

function weblazem_consultation_request_columns($columns) {
    $new = array();

    foreach ($columns as $key => $label) {
        if ($key === 'title') {
            $new['title'] = 'نام';
            continue;
        }
        if ($key === 'date') {
            $new['consult_mobile']  = 'موبایل';
            $new['consult_subject'] = 'موضوع';
            $new['consult_sms']     = 'وضعیت پیامک';
            $new['consult_page']    = 'صفحه';
            $new['date']            = $label;
            continue;
        }
        $new[$key] = $label;
    }

    return $new;
}
add_filter('manage_consultation_request_posts_columns', 'weblazem_consultation_request_columns');

function weblazem_consultation_request_column_content($column, $post_id) {
    switch ($column) {
        case 'consult_mobile':
            echo esc_html(get_post_meta($post_id, '_consult_mobile', true));
            break;
        case 'consult_subject':
            $subject_label = get_post_meta($post_id, '_consult_subject_label', true);
            if ($subject_label === '') {
                $subject_label = weblazem_get_consult_subject_label(get_post_meta($post_id, '_consult_subject', true));
            }
            echo esc_html($subject_label);
            break;
        case 'consult_sms':
            $status = get_post_meta($post_id, '_consult_sms_status', true);
            echo $status === 'sent' ? '<span style="color:#15803d;">ارسال شد</span>' : '<span style="color:#b45309;">' . esc_html($status ?: 'نامشخص') . '</span>';
            break;
        case 'consult_page':
            $url = get_post_meta($post_id, '_consult_page_url', true);
            if ($url) {
                echo '<a href="' . esc_url($url) . '" target="_blank" rel="noopener">' . esc_html(wp_parse_url($url, PHP_URL_PATH) ?: $url) . '</a>';
            }
            break;
    }
}
add_action('manage_consultation_request_posts_custom_column', 'weblazem_consultation_request_column_content', 10, 2);

function weblazem_consultation_request_meta_box() {
    add_meta_box(
        'weblazem_consult_request_details',
        'جزئیات درخواست',
        'weblazem_consultation_request_meta_box_render',
        'consultation_request',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'weblazem_consultation_request_meta_box');

function weblazem_consultation_request_meta_box_render($post) {
    $stored_full_name = get_post_meta($post->ID, '_consult_full_name', true);
    if ($stored_full_name === '') {
        $stored_full_name = trim(
            get_post_meta($post->ID, '_consult_first_name', true) . ' ' . get_post_meta($post->ID, '_consult_last_name', true)
        );
    }

    $fields = array(
        'نام و نام خانوادگی' => $stored_full_name,
        'موبایل'        => get_post_meta($post->ID, '_consult_mobile', true),
        'موضوع'         => get_post_meta($post->ID, '_consult_subject_label', true) ?: weblazem_get_consult_subject_label(get_post_meta($post->ID, '_consult_subject', true)),
        'صفحه'          => get_post_meta($post->ID, '_consult_page_url', true),
        'IP'            => get_post_meta($post->ID, '_consult_ip', true),
        'وضعیت پیامک'  => get_post_meta($post->ID, '_consult_sms_status', true),
        'پاسخ sms.ir'   => get_post_meta($post->ID, '_consult_sms_response', true),
    );

    echo '<table class="form-table"><tbody>';
    foreach ($fields as $label => $value) {
        echo '<tr><th>' . esc_html($label) . '</th><td>';
        if ($label === 'صفحه' && $value) {
            echo '<a href="' . esc_url($value) . '" target="_blank" rel="noopener">' . esc_html($value) . '</a>';
        } else {
            echo esc_html((string) $value);
        }
        echo '</td></tr>';
    }
    echo '</tbody></table>';
}

function weblazem_save_consultation_request($data) {
    $full_name = !empty($data['full_name'])
        ? trim($data['full_name'])
        : trim($data['first_name'] . ' ' . $data['last_name']);

    $post_id = wp_insert_post(
        array(
            'post_type'   => 'consultation_request',
            'post_status' => 'publish',
            'post_title'  => $full_name,
        ),
        true
    );

    if (is_wp_error($post_id)) {
        return $post_id;
    }

    update_post_meta($post_id, '_consult_full_name', $full_name);
    update_post_meta($post_id, '_consult_first_name', $data['first_name']);
    update_post_meta($post_id, '_consult_last_name', $data['last_name']);
    update_post_meta($post_id, '_consult_mobile', $data['mobile']);
    update_post_meta($post_id, '_consult_subject', $data['subject'] ?? '');
    update_post_meta($post_id, '_consult_subject_label', $data['subject_label'] ?? '');
    update_post_meta($post_id, '_consult_page_url', $data['page_url']);
    update_post_meta($post_id, '_consult_ip', $data['ip']);
    update_post_meta($post_id, '_consult_sms_status', $data['sms_status']);
    update_post_meta($post_id, '_consult_sms_response', $data['sms_response']);

    return $post_id;
}
