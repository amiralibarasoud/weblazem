<?php
/**
 * Live portfolio demos — page, options, portfolio meta, enqueue.
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('weblazem_growth_ensure_page')) {
    require_once get_template_directory() . '/inc/growth-tools-shared.php';
}

define('WEBLAZEM_LIVE_DEMO_SLUG', 'demo-zende');
define('WEBLAZEM_LIVE_DEMO_TEMPLATE', 'live-demo-template.php');
define('WEBLAZEM_LIVE_DEMO_OPTION', 'weblazem_live_demo_page_id');

function weblazem_live_demo_defaults() {
    return array(
        'title'            => 'دموی زنده نمونه‌کارها',
        'subtitle'         => 'پروژه‌های واقعی را در قاب دسکتاپ، تبلت یا موبایل ببینید — یا ویدیوی دمو را پخش کنید.',
        'iframe_note'      => 'برخی سایت‌ها iframe را مسدود می‌کنند — در این حالت دکمه باز کردن در تب جدید',
        'empty_text'       => 'هنوز دموی زنده‌ای فعال نشده است.',
        'show_video_first' => '0',
        'open_site_text'   => 'باز کردن سایت',
        'filter_all_label' => 'همه',
    );
}

function weblazem_get_live_demo_settings() {
    $defaults = weblazem_live_demo_defaults();
    $saved    = get_option('weblazem_live_demo_settings', array());
    if (!is_array($saved)) {
        $saved = array();
    }
    $settings                     = wp_parse_args($saved, $defaults);
    $settings['show_video_first'] = ($settings['show_video_first'] === '1') ? '1' : '0';
    return $settings;
}

function weblazem_ensure_live_demo_defaults() {
    if (get_option('weblazem_live_demo_settings') === false) {
        update_option('weblazem_live_demo_settings', weblazem_live_demo_defaults());
    }
}
add_action('init', 'weblazem_ensure_live_demo_defaults', 12);

function weblazem_get_live_demo_page_id() {
    return weblazem_growth_get_page_id(WEBLAZEM_LIVE_DEMO_OPTION, WEBLAZEM_LIVE_DEMO_SLUG);
}

function weblazem_get_live_demo_page_url() {
    return weblazem_growth_get_page_url(WEBLAZEM_LIVE_DEMO_OPTION, WEBLAZEM_LIVE_DEMO_SLUG);
}

function weblazem_is_live_demo_page() {
    return weblazem_growth_is_page(
        WEBLAZEM_LIVE_DEMO_TEMPLATE,
        WEBLAZEM_LIVE_DEMO_OPTION,
        WEBLAZEM_LIVE_DEMO_SLUG
    );
}

function weblazem_ensure_live_demo_page() {
    weblazem_growth_ensure_page(
        array(
            'slug'     => WEBLAZEM_LIVE_DEMO_SLUG,
            'template' => WEBLAZEM_LIVE_DEMO_TEMPLATE,
            'title'    => 'دموی زنده',
            'option'   => WEBLAZEM_LIVE_DEMO_OPTION,
        )
    );
}
add_action('init', 'weblazem_ensure_live_demo_page', 39);

function weblazem_live_demo_sanitize_settings($input) {
    $defaults = weblazem_live_demo_defaults();
    $out      = $defaults;
    if (!is_array($input)) {
        return $out;
    }
    $out['title']            = sanitize_text_field($input['title'] ?? $defaults['title']);
    $out['subtitle']         = sanitize_textarea_field($input['subtitle'] ?? $defaults['subtitle']);
    $out['iframe_note']      = sanitize_textarea_field($input['iframe_note'] ?? $defaults['iframe_note']);
    $out['empty_text']       = sanitize_textarea_field($input['empty_text'] ?? $defaults['empty_text']);
    $out['show_video_first'] = (!empty($input['show_video_first']) && $input['show_video_first'] === '1') ? '1' : '0';
    $out['open_site_text']   = sanitize_text_field($input['open_site_text'] ?? $defaults['open_site_text']);
    $out['filter_all_label'] = sanitize_text_field($input['filter_all_label'] ?? $defaults['filter_all_label']);
    return $out;
}

function weblazem_register_live_demo_settings() {
    register_setting(
        'weblazem_live_demo_group',
        'weblazem_live_demo_settings',
        array(
            'type'              => 'array',
            'sanitize_callback' => 'weblazem_live_demo_sanitize_settings',
            'default'           => weblazem_live_demo_defaults(),
        )
    );
}
add_action('admin_init', 'weblazem_register_live_demo_settings');

function weblazem_live_demo_admin_menu() {
    add_submenu_page(
        'weblazem-theme-options',
        'دموی زنده',
        'دموی زنده',
        'manage_options',
        'weblazem-live-demo-options',
        'weblazem_live_demo_options_display'
    );
}
add_action('admin_menu', 'weblazem_live_demo_admin_menu', 40);

function weblazem_live_demo_options_display() {
    if (!current_user_can('manage_options')) {
        return;
    }
    $s        = weblazem_get_live_demo_settings();
    $page_url = weblazem_get_live_demo_page_url();
    ?>
    <div class="wrap" dir="rtl">
        <h1>تنظیمات دموی زنده</h1>
        <?php if ($page_url) : ?>
            <p>صفحه: <a href="<?php echo esc_url($page_url); ?>" target="_blank" rel="noopener"><?php echo esc_html($page_url); ?></a></p>
        <?php endif; ?>
        <form method="post" action="options.php">
            <?php settings_fields('weblazem_live_demo_group'); ?>
            <input type="hidden" name="weblazem_live_demo_settings[show_video_first]" value="0" />
            <table class="form-table">
                <tr>
                    <th>عنوان صفحه</th>
                    <td><input type="text" class="large-text" name="weblazem_live_demo_settings[title]" value="<?php echo esc_attr($s['title']); ?>" /></td>
                </tr>
                <tr>
                    <th>زیرعنوان</th>
                    <td><textarea class="large-text" rows="3" name="weblazem_live_demo_settings[subtitle]"><?php echo esc_textarea($s['subtitle']); ?></textarea></td>
                </tr>
                <tr>
                    <th>یادداشت iframe</th>
                    <td><textarea class="large-text" rows="2" name="weblazem_live_demo_settings[iframe_note]"><?php echo esc_textarea($s['iframe_note']); ?></textarea></td>
                </tr>
                <tr>
                    <th>متن خالی بودن</th>
                    <td><textarea class="large-text" rows="2" name="weblazem_live_demo_settings[empty_text]"><?php echo esc_textarea($s['empty_text']); ?></textarea></td>
                </tr>
                <tr>
                    <th>متن دکمه باز کردن سایت</th>
                    <td><input type="text" class="regular-text" name="weblazem_live_demo_settings[open_site_text]" value="<?php echo esc_attr($s['open_site_text']); ?>" /></td>
                </tr>
                <tr>
                    <th>برچسب فیلتر همه</th>
                    <td><input type="text" class="regular-text" name="weblazem_live_demo_settings[filter_all_label]" value="<?php echo esc_attr($s['filter_all_label']); ?>" /></td>
                </tr>
                <tr>
                    <th>اولویت ویدیو</th>
                    <td>
                        <label>
                            <input type="checkbox" name="weblazem_live_demo_settings[show_video_first]" value="1" <?php checked($s['show_video_first'], '1'); ?> />
                            اگر ویدیو تنظیم شده باشد، ابتدا ویدیو نمایش داده شود (به‌جای iframe)
                        </label>
                    </td>
                </tr>
            </table>
            <?php submit_button('ذخیره تنظیمات'); ?>
        </form>
    </div>
    <?php
}

function weblazem_live_demo_meta_boxes() {
    add_meta_box(
        'weblazem_live_demo_meta',
        'دموی زنده',
        'weblazem_live_demo_meta_render',
        'portfolio',
        'normal',
        'default'
    );
}
add_action('add_meta_boxes', 'weblazem_live_demo_meta_boxes');

function weblazem_live_demo_meta_render($post) {
    wp_nonce_field('weblazem_live_demo_meta_save', 'weblazem_live_demo_meta_nonce');
    $enabled = get_post_meta($post->ID, '_weblazem_live_demo_enabled', true);
    $url     = get_post_meta($post->ID, '_weblazem_live_demo_url', true);
    $video   = get_post_meta($post->ID, '_weblazem_live_demo_video', true);
    $note    = get_post_meta($post->ID, '_weblazem_live_demo_note', true);
    ?>
    <table class="form-table">
        <tr>
            <th scope="row">فعال‌سازی دموی زنده</th>
            <td>
                <label>
                    <input type="checkbox" name="weblazem_live_demo_enabled" value="1" <?php checked($enabled, '1'); ?> />
                    این نمونه کار را در صفحه دموی زنده نمایش بده
                </label>
            </td>
        </tr>
        <tr>
            <th scope="row">آدرس سایت زنده</th>
            <td>
                <input type="url" class="large-text" dir="ltr" name="weblazem_live_demo_url" value="<?php echo esc_attr($url); ?>" placeholder="https://example.com" />
                <p class="description">برای نمایش در iframe</p>
            </td>
        </tr>
        <tr>
            <th scope="row">ویدیو دمو (اختیاری)</th>
            <td>
                <input type="url" class="large-text" dir="ltr" name="weblazem_live_demo_video" value="<?php echo esc_attr($video); ?>" placeholder="https://...mp4 یا YouTube" />
                <p class="description">فایل mp4 یا لینک یوتیوب</p>
            </td>
        </tr>
        <tr>
            <th scope="row">یادداشت کوتاه</th>
            <td><input type="text" class="large-text" name="weblazem_live_demo_note" value="<?php echo esc_attr($note); ?>" maxlength="200" /></td>
        </tr>
    </table>
    <?php
}

function weblazem_live_demo_meta_save($post_id) {
    if (!isset($_POST['weblazem_live_demo_meta_nonce']) ||
        !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['weblazem_live_demo_meta_nonce'])), 'weblazem_live_demo_meta_save')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (!current_user_can('edit_post', $post_id) || get_post_type($post_id) !== 'portfolio') {
        return;
    }

    $enabled = (!empty($_POST['weblazem_live_demo_enabled']) && $_POST['weblazem_live_demo_enabled'] === '1') ? '1' : '0';
    update_post_meta($post_id, '_weblazem_live_demo_enabled', $enabled);
    update_post_meta(
        $post_id,
        '_weblazem_live_demo_url',
        isset($_POST['weblazem_live_demo_url']) ? esc_url_raw(wp_unslash($_POST['weblazem_live_demo_url'])) : ''
    );
    update_post_meta(
        $post_id,
        '_weblazem_live_demo_video',
        isset($_POST['weblazem_live_demo_video']) ? esc_url_raw(wp_unslash($_POST['weblazem_live_demo_video'])) : ''
    );
    update_post_meta(
        $post_id,
        '_weblazem_live_demo_note',
        isset($_POST['weblazem_live_demo_note']) ? sanitize_text_field(wp_unslash($_POST['weblazem_live_demo_note'])) : ''
    );
}
add_action('save_post_portfolio', 'weblazem_live_demo_meta_save');

function weblazem_is_portfolio_live_demo_enabled($post_id = null) {
    $post_id = $post_id ? $post_id : get_the_ID();
    return get_post_meta($post_id, '_weblazem_live_demo_enabled', true) === '1';
}

function weblazem_get_portfolio_live_demo_data($post_id = null) {
    $post_id = $post_id ? $post_id : get_the_ID();
    return array(
        'enabled' => weblazem_is_portfolio_live_demo_enabled($post_id),
        'url'     => get_post_meta($post_id, '_weblazem_live_demo_url', true),
        'video'   => get_post_meta($post_id, '_weblazem_live_demo_video', true),
        'note'    => get_post_meta($post_id, '_weblazem_live_demo_note', true),
    );
}

function weblazem_live_demo_youtube_embed($url) {
    $url = trim((string) $url);
    if ($url === '') {
        return '';
    }
    if (preg_match('#(?:youtube\.com/watch\?v=|youtu\.be/|youtube\.com/embed/)([A-Za-z0-9_-]{6,})#', $url, $m)) {
        return 'https://www.youtube.com/embed/' . rawurlencode($m[1]) . '?rel=0';
    }
    return '';
}

function weblazem_live_demo_is_mp4($url) {
    $path = wp_parse_url($url, PHP_URL_PATH);
    return is_string($path) && (bool) preg_match('/\.mp4$/i', $path);
}

function weblazem_query_live_demo_portfolios($args = array()) {
    $defaults = array(
        'post_type'      => 'portfolio',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'orderby'        => 'date',
        'order'          => 'DESC',
        'meta_query'     => array(
            array(
                'key'   => '_weblazem_live_demo_enabled',
                'value' => '1',
            ),
        ),
    );
    return new WP_Query(wp_parse_args($args, $defaults));
}

function weblazem_get_live_demo_items_for_js() {
    $query = weblazem_query_live_demo_portfolios();
    $items = array();
    if (!$query->have_posts()) {
        return $items;
    }
    while ($query->have_posts()) {
        $query->the_post();
        $id   = get_the_ID();
        $demo = weblazem_get_portfolio_live_demo_data($id);
        $cats = array();
        $terms = get_the_terms($id, 'portfolio_category');
        if ($terms && !is_wp_error($terms)) {
            foreach ($terms as $term) {
                $cats[] = array(
                    'slug' => $term->slug,
                    'name' => $term->name,
                );
            }
        }
        $thumb = has_post_thumbnail($id) ? get_the_post_thumbnail_url($id, 'medium_large') : '';
        $items[] = array(
            'id'      => $id,
            'title'   => get_the_title(),
            'url'     => $demo['url'],
            'video'   => $demo['video'],
            'note'    => $demo['note'],
            'thumb'   => $thumb,
            'cats'    => $cats,
            'catSlugs'=> wp_list_pluck($cats, 'slug'),
            'ytEmbed' => weblazem_live_demo_youtube_embed($demo['video']),
            'isMp4'   => weblazem_live_demo_is_mp4($demo['video']),
        );
    }
    wp_reset_postdata();
    return $items;
}

function weblazem_enqueue_live_demo_assets() {
    if (!weblazem_is_live_demo_page()) {
        return;
    }
    $settings = weblazem_get_live_demo_settings();
    $ver      = '1.0.0';

    wp_enqueue_style(
        'weblazem-live-demo',
        get_template_directory_uri() . '/assets/css/live-demo.css',
        array(),
        $ver
    );
    wp_enqueue_script(
        'weblazem-live-demo',
        get_template_directory_uri() . '/assets/js/live-demo.js',
        array(),
        $ver,
        true
    );
    wp_localize_script(
        'weblazem-live-demo',
        'weblazemLiveDemo',
        array(
            'items'          => weblazem_get_live_demo_items_for_js(),
            'showVideoFirst' => $settings['show_video_first'] === '1',
            'iframeNote'     => $settings['iframe_note'],
            'openSiteText'   => $settings['open_site_text'],
            'emptyText'      => $settings['empty_text'],
            'i18n'           => array(
                'desktop'  => 'دسکتاپ',
                'tablet'   => 'تبلت',
                'mobile'   => 'موبایل',
                'loading'  => 'در حال بارگذاری…',
                'video'    => 'ویدیو',
                'live'     => 'سایت زنده',
                'select'   => 'یک پروژه را انتخاب کنید',
                'blocked'  => $settings['iframe_note'],
            ),
        )
    );
}
add_action('wp_enqueue_scripts', 'weblazem_enqueue_live_demo_assets', 30);
