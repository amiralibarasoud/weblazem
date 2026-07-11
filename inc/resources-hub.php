<?php
/**
 * Resources Hub — gated downloadable guides with mobile lead capture.
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('weblazem_growth_ensure_page')) {
    require_once get_template_directory() . '/inc/growth-tools-shared.php';
}

define('WEBLAZEM_RESOURCES_HUB_SLUG', 'markaz-manabe');
define('WEBLAZEM_RESOURCES_HUB_TEMPLATE', 'resources-hub-template.php');
define('WEBLAZEM_RESOURCES_HUB_OPTION', 'weblazem_resources_hub_page_id');
define('WEBLAZEM_RESOURCES_HUB_SETTINGS', 'weblazem_resources_hub_settings');

function weblazem_resources_hub_default_resources() {
    return array(
        array(
            'id'              => 'seo-checklist',
            'title'           => 'چک‌لیست سئوی سایت',
            'description'     => 'گام‌به‌گام برای بهینه‌سازی صفحات، سرعت و محتوای سایت قبل از انتشار.',
            'category'        => 'سئو',
            'file_url'        => '',
            'icon'            => 'fa-list-check',
            'downloads_count' => 0,
        ),
        array(
            'id'              => 'brief-guide',
            'title'           => 'راهنمای نوشتن بریف پروژه',
            'description'     => 'قالب آماده برای توضیح اهداف، مخاطب، صفحات و بودجه به تیم طراحی.',
            'category'        => 'شروع پروژه',
            'file_url'        => '',
            'icon'            => 'fa-file-lines',
            'downloads_count' => 0,
        ),
        array(
            'id'              => 'content-calendar',
            'title'           => 'تقویم محتوایی ۳۰ روزه',
            'description'     => 'ایده‌های محتوا برای وبلاگ و شبکه‌های اجتماعی در یک ماه اول.',
            'category'        => 'محتوا',
            'file_url'        => '',
            'icon'            => 'fa-calendar-days',
            'downloads_count' => 0,
        ),
    );
}

function weblazem_resources_hub_defaults() {
    return array(
        'title'           => 'مرکز منابع وب‌لازم',
        'subtitle'        => 'چک‌لیست‌ها و راهنماهای کاربردی را با ثبت شماره موبایل دانلود کنید.',
        'success_message' => 'لینک دانلود آماده است. فایل به‌زودی باز می‌شود.',
        'modal_title'     => 'دانلود رایگان',
        'modal_text'      => 'نام و شماره موبایل خود را وارد کنید تا لینک دانلود برایتان فعال شود.',
        'no_file_message' => 'فایل این منبع هنوز بارگذاری نشده است. به‌زودی در دسترس قرار می‌گیرد.',
        'resources'       => weblazem_resources_hub_default_resources(),
    );
}

function weblazem_resources_hub_sanitize_resource($item) {
    $item = is_array($item) ? $item : array();

    $id = isset($item['id']) ? sanitize_key($item['id']) : '';
    if ($id === '') {
        $id = 'res_' . substr(md5(wp_json_encode($item) . microtime()), 0, 10);
    }

    return array(
        'id'              => $id,
        'title'           => sanitize_text_field($item['title'] ?? ''),
        'description'     => sanitize_textarea_field($item['description'] ?? ''),
        'category'        => sanitize_text_field($item['category'] ?? ''),
        'file_url'        => esc_url_raw($item['file_url'] ?? ''),
        'icon'            => sanitize_text_field($item['icon'] ?? 'fa-file'),
        'downloads_count' => max(0, (int) ($item['downloads_count'] ?? 0)),
    );
}

function weblazem_resources_hub_sanitize_settings($input) {
    $defaults = weblazem_resources_hub_defaults();
    $out      = $defaults;

    if (!is_array($input)) {
        return $out;
    }

    $out['title']           = sanitize_text_field($input['title'] ?? $defaults['title']);
    $out['subtitle']        = sanitize_textarea_field($input['subtitle'] ?? $defaults['subtitle']);
    $out['success_message'] = sanitize_textarea_field($input['success_message'] ?? $defaults['success_message']);
    $out['modal_title']     = sanitize_text_field($input['modal_title'] ?? $defaults['modal_title']);
    $out['modal_text']      = sanitize_textarea_field($input['modal_text'] ?? $defaults['modal_text']);
    $out['no_file_message'] = sanitize_textarea_field($input['no_file_message'] ?? $defaults['no_file_message']);

    $resources = array();
    if (!empty($input['resources']) && is_array($input['resources'])) {
        foreach ($input['resources'] as $row) {
            $clean = weblazem_resources_hub_sanitize_resource($row);
            if ($clean['title'] === '') {
                continue;
            }
            $resources[] = $clean;
        }
    }

    $out['resources'] = !empty($resources) ? $resources : $defaults['resources'];

    return $out;
}

function weblazem_get_resources_hub_settings() {
    $defaults = weblazem_resources_hub_defaults();
    $saved    = get_option(WEBLAZEM_RESOURCES_HUB_SETTINGS, array());

    if (!is_array($saved)) {
        $saved = array();
    }

    $settings = wp_parse_args($saved, $defaults);

    if (empty($settings['resources']) || !is_array($settings['resources'])) {
        $settings['resources'] = $defaults['resources'];
    } else {
        $settings['resources'] = array_map('weblazem_resources_hub_sanitize_resource', $settings['resources']);
    }

    return $settings;
}

function weblazem_ensure_resources_hub_defaults() {
    if (get_option(WEBLAZEM_RESOURCES_HUB_SETTINGS, false) === false) {
        update_option(WEBLAZEM_RESOURCES_HUB_SETTINGS, weblazem_resources_hub_defaults());
    }
}
add_action('init', 'weblazem_ensure_resources_hub_defaults', 12);

function weblazem_get_resources_hub_page_id() {
    return weblazem_growth_get_page_id(WEBLAZEM_RESOURCES_HUB_OPTION, WEBLAZEM_RESOURCES_HUB_SLUG);
}

function weblazem_get_resources_hub_page_url() {
    return weblazem_growth_get_page_url(WEBLAZEM_RESOURCES_HUB_OPTION, WEBLAZEM_RESOURCES_HUB_SLUG);
}

function weblazem_is_resources_hub_page() {
    return weblazem_growth_is_page(
        WEBLAZEM_RESOURCES_HUB_TEMPLATE,
        WEBLAZEM_RESOURCES_HUB_OPTION,
        WEBLAZEM_RESOURCES_HUB_SLUG
    );
}

function weblazem_ensure_resources_hub_page() {
    weblazem_growth_ensure_page(
        array(
            'slug'     => WEBLAZEM_RESOURCES_HUB_SLUG,
            'template' => WEBLAZEM_RESOURCES_HUB_TEMPLATE,
            'title'    => 'مرکز منابع',
            'option'   => WEBLAZEM_RESOURCES_HUB_OPTION,
        )
    );
}
add_action('init', 'weblazem_ensure_resources_hub_page', 39);

function weblazem_resources_hub_find($resource_id) {
    $settings = weblazem_get_resources_hub_settings();
    foreach ($settings['resources'] as $resource) {
        if (($resource['id'] ?? '') === $resource_id) {
            return $resource;
        }
    }
    return null;
}

function weblazem_resources_hub_increment_downloads($resource_id) {
    $settings = weblazem_get_resources_hub_settings();
    $found    = false;

    foreach ($settings['resources'] as $i => $resource) {
        if (($resource['id'] ?? '') === $resource_id) {
            $settings['resources'][$i]['downloads_count'] = (int) ($resource['downloads_count'] ?? 0) + 1;
            $found = true;
            break;
        }
    }

    if ($found) {
        update_option(WEBLAZEM_RESOURCES_HUB_SETTINGS, $settings);
    }
}

function weblazem_resources_hub_normalize_mobile($phone) {
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

function weblazem_resources_hub_is_valid_mobile($phone) {
    if (function_exists('weblazem_is_valid_iran_mobile')) {
        return weblazem_is_valid_iran_mobile($phone);
    }
    return (bool) preg_match('/^09\d{9}$/', weblazem_resources_hub_normalize_mobile($phone));
}

function weblazem_register_resource_lead_cpt() {
    register_post_type(
        'resource_lead',
        array(
            'labels' => array(
                'name'          => 'لیدهای منابع',
                'singular_name' => 'لید منبع',
                'menu_name'     => 'لیدهای منابع',
                'edit_item'     => 'مشاهده لید',
                'search_items'  => 'جستجوی لید',
                'not_found'     => 'لیدی یافت نشد.',
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
add_action('init', 'weblazem_register_resource_lead_cpt');

function weblazem_resources_hub_admin_menu() {
    add_submenu_page(
        'weblazem-theme-options',
        'مرکز منابع',
        'مرکز منابع',
        'manage_options',
        'weblazem-resources-hub-options',
        'weblazem_resources_hub_options_display'
    );
}
add_action('admin_menu', 'weblazem_resources_hub_admin_menu', 40);

function weblazem_resources_hub_handle_admin_save() {
    if (!isset($_POST['weblazem_rh_nonce']) ||
        !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['weblazem_rh_nonce'])), 'weblazem_rh_save')) {
        return;
    }

    if (!current_user_can('manage_options')) {
        return;
    }

    $resources = array();
    if (!empty($_POST['resources']) && is_array($_POST['resources'])) {
        foreach ($_POST['resources'] as $row) {
            if (!is_array($row)) {
                continue;
            }
            $clean = weblazem_resources_hub_sanitize_resource(
                array(
                    'id'              => $row['id'] ?? '',
                    'title'           => isset($row['title']) ? wp_unslash($row['title']) : '',
                    'description'     => isset($row['description']) ? wp_unslash($row['description']) : '',
                    'category'        => isset($row['category']) ? wp_unslash($row['category']) : '',
                    'file_url'        => isset($row['file_url']) ? wp_unslash($row['file_url']) : '',
                    'icon'            => isset($row['icon']) ? wp_unslash($row['icon']) : 'fa-file',
                    'downloads_count' => isset($row['downloads_count']) ? (int) $row['downloads_count'] : 0,
                )
            );
            if ($clean['title'] !== '') {
                $resources[] = $clean;
            }
        }
    }

    $payload = array(
        'title'           => isset($_POST['title']) ? wp_unslash($_POST['title']) : '',
        'subtitle'        => isset($_POST['subtitle']) ? wp_unslash($_POST['subtitle']) : '',
        'success_message' => isset($_POST['success_message']) ? wp_unslash($_POST['success_message']) : '',
        'modal_title'     => isset($_POST['modal_title']) ? wp_unslash($_POST['modal_title']) : '',
        'modal_text'      => isset($_POST['modal_text']) ? wp_unslash($_POST['modal_text']) : '',
        'no_file_message' => isset($_POST['no_file_message']) ? wp_unslash($_POST['no_file_message']) : '',
        'resources'       => $resources,
    );

    update_option(WEBLAZEM_RESOURCES_HUB_SETTINGS, weblazem_resources_hub_sanitize_settings($payload));

    add_settings_error('weblazem_rh', 'saved', 'تنظیمات مرکز منابع ذخیره شد.', 'updated');
}

function weblazem_resources_hub_options_display() {
    if (!current_user_can('manage_options')) {
        return;
    }

    weblazem_resources_hub_handle_admin_save();

    $s         = weblazem_get_resources_hub_settings();
    $page_url  = weblazem_get_resources_hub_page_url();
    $leads_url = admin_url('edit.php?post_type=resource_lead');
    $resources = !empty($s['resources']) ? $s['resources'] : array(weblazem_resources_hub_sanitize_resource(array('title' => '')));

    settings_errors('weblazem_rh');
    ?>
    <div class="wrap" dir="rtl">
        <h1>مرکز منابع</h1>
        <?php if ($page_url) : ?>
            <p>
                صفحه عمومی:
                <a href="<?php echo esc_url($page_url); ?>" target="_blank" rel="noopener"><?php echo esc_html($page_url); ?></a>
                |
                <a href="<?php echo esc_url($leads_url); ?>">لیست لیدهای دانلود</a>
            </p>
        <?php endif; ?>

        <form method="post" id="weblazem-rh-admin-form">
            <?php wp_nonce_field('weblazem_rh_save', 'weblazem_rh_nonce'); ?>

            <h2>متن‌های صفحه</h2>
            <table class="form-table">
                <tr>
                    <th>عنوان</th>
                    <td><input type="text" class="large-text" name="title" value="<?php echo esc_attr($s['title']); ?>" /></td>
                </tr>
                <tr>
                    <th>زیرعنوان</th>
                    <td><textarea class="large-text" rows="2" name="subtitle"><?php echo esc_textarea($s['subtitle']); ?></textarea></td>
                </tr>
                <tr>
                    <th>عنوان مودال</th>
                    <td><input type="text" class="large-text" name="modal_title" value="<?php echo esc_attr($s['modal_title']); ?>" /></td>
                </tr>
                <tr>
                    <th>متن مودال</th>
                    <td><textarea class="large-text" rows="2" name="modal_text"><?php echo esc_textarea($s['modal_text']); ?></textarea></td>
                </tr>
                <tr>
                    <th>پیام موفقیت</th>
                    <td><textarea class="large-text" rows="2" name="success_message"><?php echo esc_textarea($s['success_message']); ?></textarea></td>
                </tr>
                <tr>
                    <th>پیام بدون فایل</th>
                    <td><textarea class="large-text" rows="2" name="no_file_message"><?php echo esc_textarea($s['no_file_message']); ?></textarea></td>
                </tr>
            </table>

            <h2>منابع قابل دانلود</h2>
            <p class="description">هر ردیف یک چک‌لیست یا راهنما است. لینک فایل می‌تواند خالی بماند.</p>

            <div id="weblazem-rh-resources">
                <?php foreach ($resources as $index => $resource) : ?>
                    <div class="weblazem-rh-resource-row" style="border:1px solid #ccd0d4;padding:16px;margin:0 0 14px;background:#fff;border-radius:8px;">
                        <input type="hidden" name="resources[<?php echo (int) $index; ?>][id]" value="<?php echo esc_attr($resource['id']); ?>" />
                        <input type="hidden" name="resources[<?php echo (int) $index; ?>][downloads_count]" value="<?php echo (int) $resource['downloads_count']; ?>" />
                        <p>
                            <label>عنوان<br />
                                <input type="text" class="large-text" name="resources[<?php echo (int) $index; ?>][title]" value="<?php echo esc_attr($resource['title']); ?>" />
                            </label>
                        </p>
                        <p>
                            <label>توضیح<br />
                                <textarea class="large-text" rows="2" name="resources[<?php echo (int) $index; ?>][description]"><?php echo esc_textarea($resource['description']); ?></textarea>
                            </label>
                        </p>
                        <p style="display:flex;gap:12px;flex-wrap:wrap;">
                            <label style="flex:1;min-width:160px;">دسته‌بندی<br />
                                <input type="text" class="regular-text" name="resources[<?php echo (int) $index; ?>][category]" value="<?php echo esc_attr($resource['category']); ?>" />
                            </label>
                            <label style="flex:1;min-width:160px;">آیکون Font Awesome<br />
                                <input type="text" class="regular-text" dir="ltr" name="resources[<?php echo (int) $index; ?>][icon]" value="<?php echo esc_attr($resource['icon']); ?>" placeholder="fa-file-pdf" />
                            </label>
                            <label style="flex:1;min-width:80px;">دانلودها<br />
                                <input type="number" class="small-text" readonly value="<?php echo (int) $resource['downloads_count']; ?>" />
                            </label>
                        </p>
                        <p>
                            <label>لینک فایل (PDF/DOCX)<br />
                                <input type="url" class="large-text" dir="ltr" name="resources[<?php echo (int) $index; ?>][file_url]" value="<?php echo esc_attr($resource['file_url']); ?>" />
                            </label>
                        </p>
                        <p>
                            <button type="button" class="button weblazem-rh-remove-row">حذف این منبع</button>
                        </p>
                    </div>
                <?php endforeach; ?>
            </div>

            <p>
                <button type="button" class="button" id="weblazem-rh-add-row">افزودن منبع</button>
            </p>

            <?php submit_button('ذخیره تنظیمات'); ?>
        </form>
    </div>

    <script>
    (function () {
        var wrap = document.getElementById('weblazem-rh-resources');
        var addBtn = document.getElementById('weblazem-rh-add-row');
        if (!wrap || !addBtn) return;

        function reindex() {
            Array.prototype.forEach.call(wrap.querySelectorAll('.weblazem-rh-resource-row'), function (row, index) {
                row.querySelectorAll('input, textarea').forEach(function (el) {
                    if (!el.name) return;
                    el.name = el.name.replace(/resources\[\d+]/, 'resources[' + index + ']');
                });
            });
        }

        addBtn.addEventListener('click', function () {
            var index = wrap.querySelectorAll('.weblazem-rh-resource-row').length;
            var id = 'res_' + Date.now().toString(36);
            var html = '' +
                '<div class="weblazem-rh-resource-row" style="border:1px solid #ccd0d4;padding:16px;margin:0 0 14px;background:#fff;border-radius:8px;">' +
                '<input type="hidden" name="resources[' + index + '][id]" value="' + id + '" />' +
                '<input type="hidden" name="resources[' + index + '][downloads_count]" value="0" />' +
                '<p><label>عنوان<br /><input type="text" class="large-text" name="resources[' + index + '][title]" value="" /></label></p>' +
                '<p><label>توضیح<br /><textarea class="large-text" rows="2" name="resources[' + index + '][description]"></textarea></label></p>' +
                '<p style="display:flex;gap:12px;flex-wrap:wrap;">' +
                '<label style="flex:1;min-width:160px;">دسته‌بندی<br /><input type="text" class="regular-text" name="resources[' + index + '][category]" value="" /></label>' +
                '<label style="flex:1;min-width:160px;">آیکون Font Awesome<br /><input type="text" class="regular-text" dir="ltr" name="resources[' + index + '][icon]" value="fa-file" /></label>' +
                '<label style="flex:1;min-width:80px;">دانلودها<br /><input type="number" class="small-text" readonly value="0" /></label>' +
                '</p>' +
                '<p><label>لینک فایل (PDF/DOCX)<br /><input type="url" class="large-text" dir="ltr" name="resources[' + index + '][file_url]" value="" /></label></p>' +
                '<p><button type="button" class="button weblazem-rh-remove-row">حذف این منبع</button></p>' +
                '</div>';
            wrap.insertAdjacentHTML('beforeend', html);
        });

        wrap.addEventListener('click', function (e) {
            if (e.target && e.target.classList.contains('weblazem-rh-remove-row')) {
                var rows = wrap.querySelectorAll('.weblazem-rh-resource-row');
                if (rows.length <= 1) {
                    alert('حداقل یک ردیف باید باقی بماند.');
                    return;
                }
                e.target.closest('.weblazem-rh-resource-row').remove();
                reindex();
            }
        });
    })();
    </script>
    <?php
}

function weblazem_ajax_resource_download() {
    check_ajax_referer('weblazem_resources_hub', 'nonce');

    $rate = weblazem_growth_rate_limit('resource_download', 10, 15 * MINUTE_IN_SECONDS);
    if (is_wp_error($rate)) {
        wp_send_json_error(array('message' => $rate->get_error_message()), 429);
    }

    $settings    = weblazem_get_resources_hub_settings();
    $resource_id = isset($_POST['resource_id']) ? sanitize_key(wp_unslash($_POST['resource_id'])) : '';
    $name        = isset($_POST['name']) ? sanitize_text_field(wp_unslash($_POST['name'])) : '';
    $mobile      = isset($_POST['mobile']) ? sanitize_text_field(wp_unslash($_POST['mobile'])) : '';

    if ($resource_id === '' || $name === '' || $mobile === '') {
        wp_send_json_error(array('message' => 'نام، موبایل و انتخاب منبع الزامی است.'), 400);
    }

    if (!weblazem_resources_hub_is_valid_mobile($mobile)) {
        wp_send_json_error(array('message' => 'شماره موبایل معتبر نیست. مثال: 09121234567'), 400);
    }

    $resource = weblazem_resources_hub_find($resource_id);
    if (!$resource) {
        wp_send_json_error(array('message' => 'منبع مورد نظر یافت نشد.'), 404);
    }

    if (empty($resource['file_url'])) {
        wp_send_json_error(array('message' => $settings['no_file_message']), 400);
    }

    $mobile = weblazem_resources_hub_normalize_mobile($mobile);

    $post_id = wp_insert_post(
        array(
            'post_type'   => 'resource_lead',
            'post_status' => 'publish',
            'post_title'  => $name . ' — ' . $resource['title'],
        ),
        true
    );

    if (is_wp_error($post_id) || !$post_id) {
        wp_send_json_error(array('message' => 'خطا در ثبت درخواست. دوباره تلاش کنید.'), 500);
    }

    update_post_meta($post_id, '_rh_name', $name);
    update_post_meta($post_id, '_rh_mobile', $mobile);
    update_post_meta($post_id, '_rh_resource_id', $resource_id);
    update_post_meta($post_id, '_rh_resource_title', $resource['title']);
    update_post_meta($post_id, '_rh_ip', weblazem_growth_client_ip());

    weblazem_resources_hub_increment_downloads($resource_id);

    wp_send_json_success(
        array(
            'message'  => $settings['success_message'],
            'file_url' => $resource['file_url'],
            'title'    => $resource['title'],
        )
    );
}
add_action('wp_ajax_weblazem_resource_download', 'weblazem_ajax_resource_download');
add_action('wp_ajax_nopriv_weblazem_resource_download', 'weblazem_ajax_resource_download');

function weblazem_enqueue_resources_hub_assets() {
    if (!weblazem_is_resources_hub_page()) {
        return;
    }

    $settings = weblazem_get_resources_hub_settings();

    wp_enqueue_style(
        'weblazem-resources-hub',
        get_template_directory_uri() . '/assets/css/resources-hub.css',
        array(),
        '1.0.0'
    );

    wp_enqueue_script(
        'weblazem-resources-hub',
        get_template_directory_uri() . '/assets/js/resources-hub.js',
        array(),
        '1.0.0',
        true
    );

    wp_localize_script(
        'weblazem-resources-hub',
        'weblazemResourcesHub',
        array(
            'ajaxUrl'         => admin_url('admin-ajax.php'),
            'nonce'           => wp_create_nonce('weblazem_resources_hub'),
            'successMessage'  => $settings['success_message'],
            'noFileMessage'   => $settings['no_file_message'],
            'genericError'    => 'خطایی رخ داد. دوباره تلاش کنید.',
        )
    );
}
add_action('wp_enqueue_scripts', 'weblazem_enqueue_resources_hub_assets', 30);
