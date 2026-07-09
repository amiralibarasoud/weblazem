<?php
/**
 * Dynamic service landing pages — admin UI and save handler.
 */

function weblazem_service_landing_admin_menu() {
    add_submenu_page(
        'weblazem-theme-options',
        'صفحات خدمات داینامیک',
        'صفحات خدمات داینامیک',
        'manage_options',
        'weblazem-service-landing',
        'weblazem_service_landing_list_display'
    );

    add_submenu_page(
        null,
        'ویرایش صفحه خدمات',
        'ویرایش صفحه خدمات',
        'manage_options',
        'weblazem-service-landing-edit',
        'weblazem_service_landing_edit_display'
    );
}
add_action('admin_menu', 'weblazem_service_landing_admin_menu', 21);

function weblazem_service_landing_admin_scripts($hook) {
    if (strpos($hook, 'weblazem-service-landing') === false) {
        return;
    }
    wp_enqueue_media();
}
add_action('admin_enqueue_scripts', 'weblazem_service_landing_admin_scripts');

function weblazem_service_landing_list_display() {
    if (isset($_POST['weblazem_create_service_landing']) && check_admin_referer('weblazem_create_service_landing')) {
        $title = isset($_POST['new_page_title']) ? sanitize_text_field(wp_unslash($_POST['new_page_title'])) : '';
        $slug  = isset($_POST['new_page_slug']) ? sanitize_title(wp_unslash($_POST['new_page_slug'])) : '';
        $copy  = !empty($_POST['copy_from_webdesign']);

        if ($title && $slug) {
            $post_id = weblazem_create_service_landing_page($title, $slug, $copy);
            if (!is_wp_error($post_id) && $post_id) {
                wp_safe_redirect(admin_url('admin.php?page=weblazem-service-landing-edit&post_id=' . $post_id . '&created=1'));
                exit;
            }
        }
    }

    $pages = weblazem_get_service_landing_pages();
    ?>
    <div class="wrap" dir="rtl">
        <div class="weblazem-admin-header">
            <div class="weblazem-admin-header-content">
                <h1>صفحات خدمات داینامیک</h1>
                <p>هر صفحه از قالب طراحی سایت استفاده می‌کند و داده‌های مستقل خود را دارد.</p>
            </div>
        </div>

        <div class="weblazem-admin-content" style="margin-top:24px;display:grid;grid-template-columns:1fr 360px;gap:24px;">
            <div class="weblazem-admin-card">
                <h3>صفحات موجود</h3>
                <?php if (empty($pages)) : ?>
                    <p>هنوز صفحه‌ای ساخته نشده است.</p>
                <?php else : ?>
                    <table class="widefat striped">
                        <thead>
                            <tr>
                                <th>عنوان</th>
                                <th>آدرس</th>
                                <th>عملیات</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pages as $page) : ?>
                                <tr>
                                    <td><strong><?php echo esc_html($page->post_title); ?></strong></td>
                                    <td><a href="<?php echo esc_url(get_permalink($page)); ?>" target="_blank" rel="noopener"><?php echo esc_html(wp_parse_url(get_permalink($page), PHP_URL_PATH)); ?></a></td>
                                    <td>
                                        <a class="button button-primary" href="<?php echo esc_url(admin_url('admin.php?page=weblazem-service-landing-edit&post_id=' . $page->ID)); ?>">ویرایش محتوا</a>
                                        <a class="button" href="<?php echo esc_url(get_edit_post_link($page->ID, 'raw')); ?>">تنظیمات برگه</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>

            <div class="weblazem-admin-card">
                <h3>افزودن صفحه جدید</h3>
                <form method="post">
                    <?php wp_nonce_field('weblazem_create_service_landing'); ?>
                    <p><label><strong>عنوان صفحه</strong><br>
                        <input type="text" name="new_page_title" class="large-text" placeholder="مثلاً طراحی سایت فروشگاهی" required /></label></p>
                    <p><label><strong>نامک (slug)</strong><br>
                        <input type="text" name="new_page_slug" class="large-text" dir="ltr" placeholder="tarahi-site-forooshgahi" required /></label></p>
                    <p><label><input type="checkbox" name="copy_from_webdesign" value="1" checked /> کپی محتوا از صفحه طراحی سایت فعلی</label></p>
                    <p class="description">قالب: طراحی سایت — تمام سکشن‌ها قابل چیدمان و ویرایش هستند.</p>
                    <?php submit_button('ایجاد صفحه', 'primary', 'weblazem_create_service_landing'); ?>
                </form>
            </div>
        </div>
    </div>
    <?php
}

function weblazem_service_landing_edit_display() {
    $post_id = isset($_GET['post_id']) ? (int) $_GET['post_id'] : 0;
    $post    = $post_id ? get_post($post_id) : null;

    if (!$post || $post->post_type !== 'page' || !weblazem_is_service_landing_page($post_id)) {
        echo '<div class="wrap"><p>صفحه معتبر نیست.</p></div>';
        return;
    }

    $GLOBALS['weblazem_service_landing_admin_post_id'] = $post_id;

    if (!empty($_GET['created'])) {
        echo '<div class="notice notice-success is-dismissible"><p>صفحه با موفقیت ایجاد شد.</p></div>';
    }
  if (!empty($_GET['updated'])) {
        echo '<div class="notice notice-success is-dismissible"><p>تغییرات ذخیره شد.</p></div>';
    }

    $page_url = get_permalink($post_id);
    $storage  = weblazem_service_landing_get_storage($post_id);
    $tabs     = $storage['repeaters']['portfolio_tabs'];
    $categories = function_exists('weblazem_get_portfolio_category_choices') ? weblazem_get_portfolio_category_choices() : array('' => 'همه پروژه‌ها');
    $splits     = $storage['repeaters']['splits'];
    $steps      = $storage['repeaters']['process_steps'];
    $advantages = $storage['repeaters']['advantages_items'];
    $faq_items  = $storage['repeaters']['faq_items'];
    $cards      = $storage['repeaters']['service_cards'];
    $logos      = $storage['repeaters']['customers_logos'];
    $manual_items = $storage['repeaters']['portfolio_items'];
    $icon_choices = array(
        'cube' => 'مکعب', 'document' => 'سند', 'heart' => 'قلب', 'headset' => 'پشتیبانی',
        'layers' => 'لایه‌ها', 'rocket' => 'راکت', 'chart' => 'نمودار', 'nodes' => 'شبکه',
    );
    ?>
    <div class="wrap" dir="rtl">
        <div class="weblazem-admin-header">
            <div class="weblazem-admin-header-content">
                <h1>ویرایش: <?php echo esc_html($post->post_title); ?></h1>
                <p>
                    <a href="<?php echo esc_url($page_url); ?>" target="_blank" rel="noopener">مشاهده صفحه</a>
                    &nbsp;|&nbsp;
                    <a href="<?php echo esc_url(admin_url('admin.php?page=weblazem-service-landing')); ?>">بازگشت به لیست</a>
                </p>
            </div>
        </div>

        <div class="weblazem-admin-content" style="margin-top:24px;">
            <div class="weblazem-tabs" style="margin-bottom:20px;">
                <?php
                $admin_tabs = array(
                    'layout' => 'چیدمان سکشن‌ها', 'hero' => 'هیرو', 'portfolio' => 'نمونه‌کارها',
                    'customers' => 'مشتریان', 'splits' => 'بخش‌های دو ستونه', 'process' => 'فرآیند',
                    'advantages' => 'مزایا', 'faq' => 'FAQ و تماس',
                );
                $first = true;
                foreach ($admin_tabs as $id => $label) :
                    ?>
                    <button type="button" class="weblazem-tab<?php echo $first ? ' active' : ''; ?>" data-tab="<?php echo esc_attr($id); ?>"><?php echo esc_html($label); ?></button>
                    <?php $first = false; endforeach; ?>
            </div>

            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" id="webdesign-options-form">
                <input type="hidden" name="action" value="weblazem_save_service_landing" />
                <input type="hidden" name="post_id" value="<?php echo (int) $post_id; ?>" />
                <?php wp_nonce_field('weblazem_save_service_landing_' . $post_id); ?>

                <div class="weblazem-tab-content active" data-tab-content="layout">
                    <div class="weblazem-admin-card">
                        <h3>فعال‌سازی سکشن‌ها</h3>
                        <table class="form-table">
                            <?php foreach (weblazem_get_webdesign_sections_config() as $key => $label) :
                                $option_key = 'weblazem_webdesign_section_' . $key . '_enabled';
                                ?>
                                <tr>
                                    <th><?php echo esc_html($label); ?></th>
                                    <td>
                                        <input type="hidden" name="<?php echo esc_attr($option_key); ?>" value="0" />
                                        <label><input type="checkbox" name="<?php echo esc_attr($option_key); ?>" value="1" <?php checked($storage['sections'][$key] ?? '1', '1'); ?> /> نمایش در صفحه</label>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </table>
                    </div>
                </div>

                <?php
                // Reuse webdesign admin field helpers with overridden opt reader.
                weblazem_service_landing_render_edit_tabs(
                    $splits,
                    $steps,
                    $advantages,
                    $faq_items,
                    $cards,
                    $logos,
                    $tabs,
                    $manual_items,
                    $categories,
                    $icon_choices
                );
                ?>

                <?php submit_button('ذخیره تنظیمات صفحه'); ?>
            </form>
        </div>
    </div>
    <?php weblazem_webdesign_admin_scripts_inline($categories, $icon_choices); ?>
    <?php
}

function weblazem_service_landing_admin_opt($key) {
    $post_id = isset($GLOBALS['weblazem_service_landing_admin_post_id'])
        ? (int) $GLOBALS['weblazem_service_landing_admin_post_id']
        : 0;
    if (!$post_id) {
        return weblazem_webdesign_opt($key);
    }
    $storage = weblazem_service_landing_get_storage($post_id);
    $defaults = weblazem_webdesign_defaults();
    return $storage['fields'][$key] ?? ($defaults[$key] ?? '');
}

function weblazem_service_landing_render_edit_tabs($splits, $steps, $advantages, $faq_items, $cards, $logos, $tabs, $manual_items, $categories, $icon_choices) {
    include get_template_directory() . '/inc/service-landing-edit-tabs.php';
}

function weblazem_save_service_landing_admin() {
    $post_id = isset($_POST['post_id']) ? (int) $_POST['post_id'] : 0;
    if (!$post_id || !check_admin_referer('weblazem_save_service_landing_' . $post_id)) {
        wp_die('درخواست نامعتبر است.');
    }

    if (!current_user_can('manage_options') || !weblazem_is_service_landing_page($post_id)) {
        wp_die('دسترسی مجاز نیست.');
    }

    $storage = weblazem_service_landing_get_storage($post_id);
    $defaults = weblazem_webdesign_defaults();

    foreach (array_keys($defaults) as $key) {
        $field = 'weblazem_webdesign_' . $key;
        if (!isset($_POST[$field])) {
            continue;
        }
        $raw = wp_unslash($_POST[$field]);
        if (strpos($key, 'text') !== false || strpos($key, 'intro') !== false || strpos($key, 'description') !== false) {
            $storage['fields'][$key] = wp_kses_post($raw);
        } else {
            $storage['fields'][$key] = sanitize_text_field($raw);
        }
    }

    foreach (weblazem_get_webdesign_sections_config() as $key => $label) {
        $field = 'weblazem_webdesign_section_' . $key . '_enabled';
        $storage['sections'][$key] = (isset($_POST[$field]) && $_POST[$field] === '1') ? '1' : '0';
    }

    $repeater_map = array(
        'splits'            => 'weblazem_sanitize_webdesign_splits',
        'process_steps'     => 'weblazem_sanitize_webdesign_steps',
        'advantages_items'  => 'weblazem_sanitize_webdesign_advantages',
        'faq_items'         => 'weblazem_sanitize_webdesign_faq',
        'service_cards'     => 'weblazem_sanitize_webdesign_service_cards',
        'portfolio_tabs'    => 'weblazem_sanitize_webdesign_portfolio_tabs',
        'customers_logos'   => 'weblazem_sanitize_webdesign_logos',
        'portfolio_items'   => 'weblazem_sanitize_webdesign_portfolio_items',
    );

    foreach ($repeater_map as $key => $sanitizer) {
        $field = 'weblazem_webdesign_' . $key;
        if (isset($_POST[$field]) && function_exists($sanitizer)) {
            $storage['repeaters'][$key] = $sanitizer(wp_unslash($_POST[$field]));
        }
    }

    weblazem_service_landing_save_storage($post_id, $storage);

    wp_safe_redirect(admin_url('admin.php?page=weblazem-service-landing-edit&post_id=' . $post_id . '&updated=1'));
    exit;
}
add_action('admin_post_weblazem_save_service_landing', 'weblazem_save_service_landing_admin');

// Admin field helpers bound to per-page storage.
function weblazem_service_landing_admin_field($key, $label) {
    $val = weblazem_service_landing_admin_opt($key);
    echo '<p><label><strong>' . esc_html($label) . '</strong><br>';
    echo '<input type="text" name="weblazem_webdesign_' . esc_attr($key) . '" class="large-text" value="' . esc_attr($val) . '" /></label></p>';
}

function weblazem_service_landing_admin_textarea($key, $label, $desc = '') {
    $val = weblazem_service_landing_admin_opt($key);
    echo '<p><label><strong>' . esc_html($label) . '</strong>';
    if ($desc) {
        echo ' <span class="description">' . esc_html($desc) . '</span>';
    }
    echo '<br><textarea name="weblazem_webdesign_' . esc_attr($key) . '" class="large-text" rows="3">' . esc_textarea($val) . '</textarea></label></p>';
}

function weblazem_service_landing_admin_image($key, $label) {
    $val = weblazem_service_landing_admin_opt($key);
    $id  = 'webdesign_img_' . $key;
    echo '<p><strong>' . esc_html($label) . '</strong><br>';
    echo '<input type="hidden" id="' . esc_attr($id) . '" name="weblazem_webdesign_' . esc_attr($key) . '" value="' . esc_attr($val) . '" />';
    echo '<div class="webdesign-img-preview" data-for="' . esc_attr($id) . '" style="margin:8px 0;">';
    if ($val) {
        echo '<img src="' . esc_url($val) . '" style="max-width:200px;border-radius:8px;" alt="" />';
    }
    echo '</div>';
    echo '<button type="button" class="button webdesign-upload-img" data-target="' . esc_attr($id) . '">انتخاب تصویر</button> ';
    echo '<button type="button" class="button webdesign-remove-img" data-target="' . esc_attr($id) . '">حذف</button></p>';
}
