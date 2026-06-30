<?php
/**
 * Font Awesome icons for navigation menu items.
 */

function weblazem_sanitize_fa_icon_class($icon) {
    $icon = trim(sanitize_text_field((string) $icon));

    if ($icon === '') {
        return '';
    }

    $icon = preg_replace('/\s+/', ' ', $icon);

    if (preg_match('/^fa-[a-z0-9-]+$/i', $icon)) {
        $icon = 'fas ' . $icon;
    }

    if (!preg_match('/^(fab|far|fas|fal|fat|fad|fa-solid|fa-regular|fa-brands)\s+fa-[a-z0-9-]+(\s+fa-[a-z0-9-]+)*$/i', $icon)) {
        return '';
    }

    return $icon;
}

function weblazem_get_menu_fa_icon_choices() {
    return array(
        'fas fa-house'           => 'خانه',
        'fas fa-file-lines'      => 'صفحه',
        'fas fa-briefcase'       => 'نمونه کار',
        'fas fa-images'          => 'گالری',
        'fas fa-envelope'        => 'ایمیل',
        'fas fa-phone'           => 'تلفن',
        'fas fa-user'            => 'کاربر',
        'fas fa-users'           => 'تیم',
        'fas fa-star'            => 'ستاره',
        'fas fa-circle-info'     => 'اطلاعات',
        'fas fa-question-circle' => 'سؤال',
        'fas fa-comments'        => 'نظرات',
        'fas fa-tags'            => 'برچسب',
        'fas fa-cart-shopping'   => 'فروشگاه',
        'fas fa-blog'            => 'وبلاگ',
        'fas fa-newspaper'       => 'اخبار',
        'fas fa-gear'            => 'تنظیمات',
        'fas fa-chart-line'      => 'آمار',
        'fas fa-magnifying-glass'=> 'جستجو',
        'fas fa-link'            => 'لینک',
        'fas fa-arrow-left'      => 'فلش',
        'fas fa-handshake'       => 'همکاری',
        'fas fa-lightbulb'       => 'ایده',
        'fas fa-rocket'          => 'رشد',
        'fas fa-globe'           => 'وب',
    );
}

function weblazem_nav_menu_item_title_with_icon($title, $item, $args, $depth) {
    if (empty($args->theme_location) || $args->theme_location !== 'main_menu' || $depth > 0) {
        return $title;
    }

    $icon = get_post_meta($item->ID, '_weblazem_menu_fa_icon', true);
    $icon_class = weblazem_sanitize_fa_icon_class($icon);

    if ($icon_class === '') {
        return $title;
    }

    return '<i class="' . esc_attr($icon_class) . ' weblazem-header-menu__icon" aria-hidden="true"></i>'
        . '<span class="weblazem-header-menu__label">' . esc_html($title) . '</span>';
}
add_filter('nav_menu_item_title', 'weblazem_nav_menu_item_title_with_icon', 10, 4);

function weblazem_nav_menu_icon_custom_field($item_id, $item, $depth, $args) {
    $icon = get_post_meta($item_id, '_weblazem_menu_fa_icon', true);
    $choices = weblazem_get_menu_fa_icon_choices();
    ?>
    <p class="field-weblazem-fa-icon description description-wide">
        <label for="edit-menu-item-fa-icon-<?php echo (int) $item_id; ?>">
            <?php esc_html_e('آیکون Font Awesome', 'weblazem'); ?>
        </label>
        <span class="weblazem-menu-fa-icon-row">
            <select
                id="edit-menu-item-fa-icon-<?php echo (int) $item_id; ?>"
                class="widefat weblazem-menu-fa-icon-select"
                name="menu-item-fa-icon[<?php echo (int) $item_id; ?>]"
            >
                <option value=""><?php esc_html_e('— بدون آیکون —', 'weblazem'); ?></option>
                <?php foreach ($choices as $class => $label) : ?>
                    <option value="<?php echo esc_attr($class); ?>" <?php selected($icon, $class); ?>>
                        <?php echo esc_html($label . ' (' . $class . ')'); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <input
                type="text"
                class="widefat weblazem-menu-fa-icon-custom"
                name="menu-item-fa-icon-custom[<?php echo (int) $item_id; ?>]"
                value="<?php echo esc_attr($icon); ?>"
                placeholder="مثال: fas fa-house"
                dir="ltr"
            />
            <span class="weblazem-menu-fa-icon-preview" aria-hidden="true">
                <?php if ($icon) : ?>
                    <i class="<?php echo esc_attr(weblazem_sanitize_fa_icon_class($icon)); ?>"></i>
                <?php endif; ?>
            </span>
        </span>
        <span class="description">از لیست انتخاب کنید یا کلاس Font Awesome را دستی وارد کنید.</span>
    </p>
    <?php
}
add_action('wp_nav_menu_item_custom_fields', 'weblazem_nav_menu_icon_custom_field', 10, 4);

function weblazem_save_nav_menu_icon_field($menu_id, $menu_item_db_id) {
    unset($menu_id);

    if (isset($_POST['menu-item-fa-icon-custom'][$menu_item_db_id])) {
        $custom = sanitize_text_field(wp_unslash($_POST['menu-item-fa-icon-custom'][$menu_item_db_id]));
        if ($custom !== '') {
            update_post_meta($menu_item_db_id, '_weblazem_menu_fa_icon', weblazem_sanitize_fa_icon_class($custom));
            return;
        }
    }

    if (isset($_POST['menu-item-fa-icon'][$menu_item_db_id])) {
        $icon = sanitize_text_field(wp_unslash($_POST['menu-item-fa-icon'][$menu_item_db_id]));
        if ($icon === '') {
            delete_post_meta($menu_item_db_id, '_weblazem_menu_fa_icon');
        } else {
            update_post_meta($menu_item_db_id, '_weblazem_menu_fa_icon', weblazem_sanitize_fa_icon_class($icon));
        }
    }
}
add_action('wp_update_nav_menu_item', 'weblazem_save_nav_menu_icon_field', 10, 2);

function weblazem_nav_menu_icons_admin_assets($hook) {
    if ($hook !== 'nav-menus.php') {
        return;
    }

    wp_enqueue_style(
        'fontawesome-admin',
        'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css',
        array(),
        '6.5.1'
    );

    wp_enqueue_style(
        'weblazem-nav-menu-icons-admin',
        get_template_directory_uri() . '/assets/css/admin-menu-icons.css',
        array(),
        '1.0.0'
    );

    wp_enqueue_script(
        'weblazem-nav-menu-icons-admin',
        get_template_directory_uri() . '/assets/js/admin-menu-icons.js',
        array('jquery'),
        '1.0.0',
        true
    );
}
add_action('admin_enqueue_scripts', 'weblazem_nav_menu_icons_admin_assets');
