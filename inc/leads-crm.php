<?php
/**
 * Unified Leads CRM — dashboard for all inbound leads.
 */

if (!defined('ABSPATH')) {
    exit;
}

function weblazem_crm_statuses() {
    return array(
        'new'       => 'جدید',
        'following' => 'در حال پیگیری',
        'won'       => 'برنده / تبدیل‌شده',
        'archived'  => 'بایگانی',
    );
}

function weblazem_crm_sources() {
    return array(
        'consultation_request' => array(
            'label'  => 'مشاوره',
            'color'  => '#7c3aed',
            'name'   => array('_consult_full_name'),
            'mobile' => array('_consult_mobile'),
            'detail' => '_consult_subject_label',
        ),
        'price_estimate_lead' => array(
            'label'  => 'برآورد قیمت',
            'color'  => '#1d4ed8',
            'name'   => array('_pe_name'),
            'mobile' => array('_pe_mobile'),
            'detail' => '_pe_estimate',
        ),
        'project_brief' => array(
            'label'  => 'بریف پروژه',
            'color'  => '#be123c',
            'name'   => array('_brief_name'),
            'mobile' => array('_brief_mobile'),
            'detail' => '_brief_project_type',
        ),
        'consult_booking' => array(
            'label'  => 'رزرو مشاوره',
            'color'  => '#0f766e',
            'name'   => array('_booking_name'),
            'mobile' => array('_booking_mobile'),
            'detail' => '_booking_date',
        ),
        'resource_lead' => array(
            'label'  => 'دانلود منبع',
            'color'  => '#0e7490',
            'name'   => array('_rh_name'),
            'mobile' => array('_rh_mobile'),
            'detail' => '_rh_resource_title',
        ),
        'referral_lead' => array(
            'label'  => 'معرفی',
            'color'  => '#c2410c',
            'name'   => array('_ref_lead_name'),
            'mobile' => array('_ref_lead_mobile'),
            'detail' => '_ref_lead_service',
        ),
        'contact_request' => array(
            'label'  => 'تماس با ما',
            'color'  => '#475569',
            'name'   => array('_contact_first_name', '_contact_last_name'),
            'mobile' => array('_contact_phone'),
            'detail' => '_contact_message',
        ),
        'client_proposal' => array(
            'label'  => 'پیشنهاد قیمت',
            'color'  => '#4F1E60',
            'name'   => array('_proposal_client_name'),
            'mobile' => array('_proposal_mobile'),
            'detail' => '_proposal_title',
        ),
    );
}

function weblazem_crm_get_status($post_id) {
    $status = get_post_meta($post_id, '_weblazem_crm_status', true);
    $allowed = weblazem_crm_statuses();
    if ($status === '' || !isset($allowed[$status])) {
        return 'new';
    }
    return $status;
}

function weblazem_crm_set_status($post_id, $status) {
    $allowed = weblazem_crm_statuses();
    if (!isset($allowed[$status])) {
        return false;
    }
    update_post_meta($post_id, '_weblazem_crm_status', $status);
    update_post_meta($post_id, '_weblazem_crm_status_at', current_time('mysql'));
    return true;
}

function weblazem_crm_ensure_status_on_insert($post_id, $post, $update) {
    if ($update || wp_is_post_revision($post_id)) {
        return;
    }
    $sources = weblazem_crm_sources();
    if (!isset($sources[$post->post_type])) {
        return;
    }
    if (get_post_meta($post_id, '_weblazem_crm_status', true) === '') {
        update_post_meta($post_id, '_weblazem_crm_status', 'new');
        update_post_meta($post_id, '_weblazem_crm_status_at', current_time('mysql'));
    }
}
add_action('wp_insert_post', 'weblazem_crm_ensure_status_on_insert', 30, 3);

function weblazem_crm_read_meta_first($post_id, $keys) {
    if (!is_array($keys)) {
        $keys = array($keys);
    }
    $parts = array();
    foreach ($keys as $key) {
        $val = trim((string) get_post_meta($post_id, $key, true));
        if ($val !== '') {
            $parts[] = $val;
        }
    }
    return implode(' ', $parts);
}

function weblazem_crm_format_detail($post_type, $post_id, $detail_key) {
    $raw = get_post_meta($post_id, $detail_key, true);

    if ($post_type === 'price_estimate_lead') {
        $min = (int) get_post_meta($post_id, '_pe_min', true);
        $max = (int) get_post_meta($post_id, '_pe_max', true);
        if ($min && $max && function_exists('weblazem_growth_format_toman')) {
            return weblazem_growth_format_toman($min) . ' تا ' . weblazem_growth_format_toman($max);
        }
        if ($raw && function_exists('weblazem_growth_format_toman')) {
            return 'حدود ' . weblazem_growth_format_toman((int) $raw);
        }
    }

    if ($post_type === 'consult_booking') {
        $date = get_post_meta($post_id, '_booking_date', true);
        $time = get_post_meta($post_id, '_booking_time', true);
        return trim($date . ' ' . $time);
    }

    if ($post_type === 'project_brief') {
        $types = function_exists('weblazem_start_project_default_types') ? weblazem_start_project_default_types() : array();
        if (isset($types[$raw])) {
            return $types[$raw];
        }
    }

    if ($post_type === 'contact_request' && is_string($raw) && $raw !== '') {
        return wp_trim_words(wp_strip_all_tags($raw), 12, '…');
    }

    if ($post_type === 'client_proposal') {
        $total = (int) get_post_meta($post_id, '_proposal_total', true);
        $status = get_post_meta($post_id, '_proposal_status', true);
        $label  = $raw;
        if ($total && function_exists('weblazem_growth_format_toman')) {
            $label .= ($label ? ' — ' : '') . weblazem_growth_format_toman($total);
        }
        if ($status !== '') {
            $label .= ' [' . $status . ']';
        }
        return $label;
    }

    return is_string($raw) ? $raw : '';
}

function weblazem_crm_normalize_lead($post_id) {
    $post = get_post($post_id);
    if (!$post) {
        return null;
    }

    $sources = weblazem_crm_sources();
    if (!isset($sources[$post->post_type])) {
        return null;
    }

    $cfg = $sources[$post->post_type];
    $ref = get_post_meta($post_id, '_weblazem_ref_code', true);
    if ($ref === '' && $post->post_type === 'referral_lead') {
        $ref = get_post_meta($post_id, '_ref_lead_code', true);
    }

    return array(
        'id'         => (int) $post_id,
        'type'       => $post->post_type,
        'type_label' => $cfg['label'],
        'color'      => $cfg['color'],
        'title'      => get_the_title($post_id),
        'name'       => weblazem_crm_read_meta_first($post_id, $cfg['name']),
        'mobile'     => weblazem_crm_read_meta_first($post_id, $cfg['mobile']),
        'detail'     => weblazem_crm_format_detail($post->post_type, $post_id, $cfg['detail']),
        'status'     => weblazem_crm_get_status($post_id),
        'note'       => (string) get_post_meta($post_id, '_weblazem_crm_note', true),
        'ref_code'   => (string) $ref,
        'date'       => get_the_date('Y/m/d H:i', $post_id),
        'date_gmt'   => get_post_time('U', true, $post_id),
        'edit_link'  => get_edit_post_link($post_id, 'raw'),
    );
}

function weblazem_crm_query_leads($args = array()) {
    $defaults = array(
        'type'     => '',
        'status'   => '',
        'search'   => '',
        'per_page' => 40,
        'paged'    => 1,
    );
    $args = wp_parse_args($args, $defaults);

    $sources = weblazem_crm_sources();
    $types   = array_keys($sources);
    if ($args['type'] !== '' && isset($sources[$args['type']])) {
        $types = array($args['type']);
    }

    $query_args = array(
        'post_type'      => $types,
        'post_status'    => 'publish',
        'posts_per_page' => max(1, min(100, (int) $args['per_page'])),
        'paged'          => max(1, (int) $args['paged']),
        'orderby'        => 'date',
        'order'          => 'DESC',
    );

    // When searching, pull a larger set and filter by name/mobile meta in PHP.
    $search = trim((string) $args['search']);
    if ($search !== '') {
        $query_args['posts_per_page'] = 200;
        $query_args['paged']          = 1;
    }

    $meta_query = array();

    if ($args['status'] !== '' && isset(weblazem_crm_statuses()[$args['status']])) {
        if ($args['status'] === 'new') {
            $meta_query[] = array(
                'relation' => 'OR',
                array(
                    'key'   => '_weblazem_crm_status',
                    'value' => 'new',
                ),
                array(
                    'key'     => '_weblazem_crm_status',
                    'compare' => 'NOT EXISTS',
                ),
            );
        } else {
            $meta_query[] = array(
                'key'   => '_weblazem_crm_status',
                'value' => $args['status'],
            );
        }
    }

    if (!empty($meta_query)) {
        if (count($meta_query) === 1) {
            $query_args['meta_query'] = $meta_query;
        } else {
            $query_args['meta_query'] = array_merge(array('relation' => 'AND'), $meta_query);
        }
    }

    $query = new WP_Query($query_args);
    $leads = array();

    foreach ($query->posts as $post) {
        $lead = weblazem_crm_normalize_lead($post->ID);
        if (!$lead) {
            continue;
        }

        if ($search !== '') {
            $needle = mb_strtolower($search);
            $hay    = mb_strtolower(
                $lead['name'] . ' ' . $lead['mobile'] . ' ' . $lead['detail'] . ' ' . $lead['title'] . ' ' . $lead['ref_code']
            );
            if (mb_strpos($hay, $needle) === false) {
                continue;
            }
        }

        $leads[] = $lead;
    }

    $total = $search !== '' ? count($leads) : (int) $query->found_posts;
    if ($search !== '') {
        $per_page = max(1, min(100, (int) $args['per_page']));
        $page     = max(1, (int) $args['paged']);
        $offset   = ($page - 1) * $per_page;
        $leads    = array_slice($leads, $offset, $per_page);
        $max_pages = (int) ceil($total / $per_page);
    } else {
        $max_pages = (int) $query->max_num_pages;
        $page      = (int) $args['paged'];
    }

    return array(
        'leads'     => $leads,
        'total'     => $total,
        'max_pages' => $max_pages,
        'paged'     => $page,
    );
}

function weblazem_crm_get_stats() {
    $sources  = weblazem_crm_sources();
    $statuses = weblazem_crm_statuses();
    $stats    = array(
        'total'   => 0,
        'by_status' => array(),
        'by_type'   => array(),
    );

    foreach ($statuses as $key => $label) {
        $stats['by_status'][$key] = 0;
    }
    foreach ($sources as $type => $cfg) {
        $stats['by_type'][$type] = 0;
    }

    $query = new WP_Query(
        array(
            'post_type'      => array_keys($sources),
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'fields'         => 'ids',
            'no_found_rows'  => true,
        )
    );

    foreach ($query->posts as $id) {
        $post_type = get_post_type($id);
        $status    = weblazem_crm_get_status($id);
        $stats['total']++;
        if (isset($stats['by_status'][$status])) {
            $stats['by_status'][$status]++;
        }
        if (isset($stats['by_type'][$post_type])) {
            $stats['by_type'][$post_type]++;
        }
    }

    return $stats;
}

function weblazem_crm_admin_menu() {
    add_submenu_page(
        'weblazem-theme-options',
        'CRM لیدها',
        'CRM لیدها',
        'manage_options',
        'weblazem-leads-crm',
        'weblazem_crm_admin_page'
    );
}
add_action('admin_menu', 'weblazem_crm_admin_menu', 17);

function weblazem_crm_admin_assets($hook) {
    if (strpos($hook, 'weblazem-leads-crm') === false) {
        return;
    }

    wp_enqueue_style(
        'weblazem-leads-crm-admin',
        get_template_directory_uri() . '/assets/css/leads-crm-admin.css',
        array(),
        '1.0.0'
    );

    wp_enqueue_script(
        'weblazem-leads-crm-admin',
        get_template_directory_uri() . '/assets/js/leads-crm-admin.js',
        array(),
        '1.0.0',
        true
    );

    wp_localize_script(
        'weblazem-leads-crm-admin',
        'weblazemLeadsCrm',
        array(
            'ajaxUrl'  => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('weblazem_leads_crm'),
            'statuses' => weblazem_crm_statuses(),
        )
    );
}
add_action('admin_enqueue_scripts', 'weblazem_crm_admin_assets');

function weblazem_crm_admin_page() {
    if (!current_user_can('manage_options')) {
        return;
    }

    $type     = isset($_GET['crm_type']) ? sanitize_key(wp_unslash($_GET['crm_type'])) : '';
    $status   = isset($_GET['crm_status']) ? sanitize_key(wp_unslash($_GET['crm_status'])) : '';
    $search   = isset($_GET['crm_search']) ? sanitize_text_field(wp_unslash($_GET['crm_search'])) : '';
    $paged    = isset($_GET['paged']) ? max(1, (int) $_GET['paged']) : 1;
    $result   = weblazem_crm_query_leads(
        array(
            'type'   => $type,
            'status' => $status,
            'search' => $search,
            'paged'  => $paged,
        )
    );
    $stats    = weblazem_crm_get_stats();
    $sources  = weblazem_crm_sources();
    $statuses = weblazem_crm_statuses();
    $base_url = admin_url('admin.php?page=weblazem-leads-crm');
    ?>
    <div class="wrap wl-crm" dir="rtl">
        <div class="wl-crm__header">
            <div>
                <h1>CRM لیدها</h1>
                <p>همه درخواست‌های ورودی در یک داشبورد — با وضعیت پیگیری فروش</p>
            </div>
        </div>

        <div class="wl-crm__stats">
            <div class="wl-crm__stat">
                <span class="wl-crm__stat-label">کل لیدها</span>
                <strong class="wl-crm__stat-value"><?php echo esc_html(number_format_i18n($stats['total'])); ?></strong>
            </div>
            <?php foreach ($statuses as $key => $label) : ?>
                <a class="wl-crm__stat wl-crm__stat--<?php echo esc_attr($key); ?>" href="<?php echo esc_url(add_query_arg('crm_status', $key, $base_url)); ?>">
                    <span class="wl-crm__stat-label"><?php echo esc_html($label); ?></span>
                    <strong class="wl-crm__stat-value"><?php echo esc_html(number_format_i18n($stats['by_status'][$key])); ?></strong>
                </a>
            <?php endforeach; ?>
        </div>

        <form class="wl-crm__filters" method="get" action="<?php echo esc_url(admin_url('admin.php')); ?>">
            <input type="hidden" name="page" value="weblazem-leads-crm" />
            <select name="crm_type">
                <option value="">همه منابع</option>
                <?php foreach ($sources as $key => $cfg) : ?>
                    <option value="<?php echo esc_attr($key); ?>" <?php selected($type, $key); ?>>
                        <?php echo esc_html($cfg['label'] . ' (' . number_format_i18n($stats['by_type'][$key]) . ')'); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <select name="crm_status">
                <option value="">همه وضعیت‌ها</option>
                <?php foreach ($statuses as $key => $label) : ?>
                    <option value="<?php echo esc_attr($key); ?>" <?php selected($status, $key); ?>><?php echo esc_html($label); ?></option>
                <?php endforeach; ?>
            </select>
            <input type="search" name="crm_search" value="<?php echo esc_attr($search); ?>" placeholder="جستجوی نام یا موبایل…" />
            <button type="submit" class="button button-primary">اعمال فیلتر</button>
            <a class="button" href="<?php echo esc_url($base_url); ?>">پاک کردن</a>
        </form>

        <div class="wl-crm__table-wrap">
            <table class="wl-crm__table">
                <thead>
                    <tr>
                        <th>منبع</th>
                        <th>نام</th>
                        <th>موبایل</th>
                        <th>جزئیات</th>
                        <th>وضعیت</th>
                        <th>یادداشت</th>
                        <th>تاریخ</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($result['leads'])) : ?>
                        <tr>
                            <td colspan="8" class="wl-crm__empty">لیدی با این فیلتر پیدا نشد.</td>
                        </tr>
                    <?php else : ?>
                        <?php foreach ($result['leads'] as $lead) : ?>
                            <tr data-lead-id="<?php echo esc_attr($lead['id']); ?>">
                                <td>
                                    <span class="wl-crm__badge" style="--crm-color:<?php echo esc_attr($lead['color']); ?>">
                                        <?php echo esc_html($lead['type_label']); ?>
                                    </span>
                                    <?php if ($lead['ref_code'] !== '') : ?>
                                        <div class="wl-crm__ref">رفرال: <?php echo esc_html($lead['ref_code']); ?></div>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo esc_html($lead['name'] !== '' ? $lead['name'] : $lead['title']); ?></td>
                                <td dir="ltr">
                                    <?php if ($lead['mobile'] !== '') : ?>
                                        <a href="tel:<?php echo esc_attr(preg_replace('/\s+/', '', $lead['mobile'])); ?>"><?php echo esc_html($lead['mobile']); ?></a>
                                    <?php else : ?>
                                        —
                                    <?php endif; ?>
                                </td>
                                <td class="wl-crm__detail"><?php echo esc_html($lead['detail'] !== '' ? $lead['detail'] : '—'); ?></td>
                                <td>
                                    <select class="wl-crm__status" data-crm-status>
                                        <?php foreach ($statuses as $key => $label) : ?>
                                            <option value="<?php echo esc_attr($key); ?>" <?php selected($lead['status'], $key); ?>><?php echo esc_html($label); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                                <td>
                                    <textarea class="wl-crm__note" data-crm-note rows="2" placeholder="یادداشت پیگیری…"><?php echo esc_textarea($lead['note']); ?></textarea>
                                    <button type="button" class="button button-small wl-crm__save-note" data-crm-save-note>ذخیره یادداشت</button>
                                </td>
                                <td><?php echo esc_html($lead['date']); ?></td>
                                <td>
                                    <?php if (!empty($lead['edit_link'])) : ?>
                                        <a class="button button-small" href="<?php echo esc_url($lead['edit_link']); ?>" target="_blank" rel="noopener">جزئیات</a>
                                    <?php endif; ?>
                                    <span class="wl-crm__flash" data-crm-flash hidden></span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if ($result['max_pages'] > 1) : ?>
            <div class="wl-crm__pager">
                <?php
                echo wp_kses_post(
                    paginate_links(
                        array(
                            'base'      => add_query_arg('paged', '%#%', $base_url),
                            'format'    => '',
                            'current'   => $result['paged'],
                            'total'     => $result['max_pages'],
                            'prev_text' => 'قبلی',
                            'next_text' => 'بعدی',
                            'add_args'  => array_filter(
                                array(
                                    'crm_type'   => $type,
                                    'crm_status' => $status,
                                    'crm_search' => $search,
                                )
                            ),
                        )
                    )
                );
                ?>
            </div>
        <?php endif; ?>
    </div>
    <?php
}

function weblazem_ajax_crm_update_status() {
    check_ajax_referer('weblazem_leads_crm', 'nonce');
    if (!current_user_can('manage_options')) {
        wp_send_json_error(array('message' => 'دسترسی ندارید.'), 403);
    }

    $post_id = isset($_POST['post_id']) ? (int) $_POST['post_id'] : 0;
    $status  = isset($_POST['status']) ? sanitize_key(wp_unslash($_POST['status'])) : '';

    if (!$post_id || !get_post($post_id)) {
        wp_send_json_error(array('message' => 'لید نامعتبر است.'), 400);
    }

    $sources = weblazem_crm_sources();
    if (!isset($sources[get_post_type($post_id)])) {
        wp_send_json_error(array('message' => 'این نوع پست در CRM نیست.'), 400);
    }

    if (!weblazem_crm_set_status($post_id, $status)) {
        wp_send_json_error(array('message' => 'وضعیت نامعتبر است.'), 400);
    }

    wp_send_json_success(
        array(
            'message' => 'وضعیت به‌روز شد.',
            'status'  => $status,
            'label'   => weblazem_crm_statuses()[$status],
        )
    );
}
add_action('wp_ajax_weblazem_crm_update_status', 'weblazem_ajax_crm_update_status');

function weblazem_ajax_crm_update_note() {
    check_ajax_referer('weblazem_leads_crm', 'nonce');
    if (!current_user_can('manage_options')) {
        wp_send_json_error(array('message' => 'دسترسی ندارید.'), 403);
    }

    $post_id = isset($_POST['post_id']) ? (int) $_POST['post_id'] : 0;
    $note    = isset($_POST['note']) ? sanitize_textarea_field(wp_unslash($_POST['note'])) : '';

    if (!$post_id || !get_post($post_id)) {
        wp_send_json_error(array('message' => 'لید نامعتبر است.'), 400);
    }

    $sources = weblazem_crm_sources();
    if (!isset($sources[get_post_type($post_id)])) {
        wp_send_json_error(array('message' => 'این نوع پست در CRM نیست.'), 400);
    }

    update_post_meta($post_id, '_weblazem_crm_note', $note);
    update_post_meta($post_id, '_weblazem_crm_note_at', current_time('mysql'));

    wp_send_json_success(array('message' => 'یادداشت ذخیره شد.'));
}
add_action('wp_ajax_weblazem_crm_update_note', 'weblazem_ajax_crm_update_note');
