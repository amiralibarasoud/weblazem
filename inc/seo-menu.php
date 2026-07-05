<?php
/**
 * Add SEO page link to Appearance → Menus.
 */

function weblazem_seo_nav_menu_meta_box() {
    add_meta_box(
        'weblazem-seo-page-link',
        'صفحه سئو و بازاریابی دیجیتال',
        'weblazem_seo_nav_menu_meta_box_render',
        'nav-menus',
        'side',
        'default'
    );
}
add_action('admin_head-nav-menus.php', 'weblazem_seo_nav_menu_meta_box');

function weblazem_seo_nav_menu_meta_box_render() {
    global $_nav_menu_placeholder;

    $_nav_menu_placeholder = isset($_nav_menu_placeholder) ? (int) $_nav_menu_placeholder - 1 : -1;

    $url   = weblazem_get_seo_page_url();
    $title = 'سئو و بازاریابی دیجیتال';
    ?>
    <div id="weblazem-seo-page-div" class="posttypediv">
        <div class="tabs-panel tabs-panel-active">
            <ul class="categorychecklist form-no-clear">
                <li>
                    <label class="menu-item-title">
                        <input type="checkbox" class="menu-item-checkbox" name="menu-item[<?php echo (int) $_nav_menu_placeholder; ?>][menu-item-object-id]" value="-1">
                        <?php echo esc_html($title); ?>
                    </label>
                    <input type="hidden" class="menu-item-type" name="menu-item[<?php echo (int) $_nav_menu_placeholder; ?>][menu-item-type]" value="custom">
                    <input type="hidden" class="menu-item-title" name="menu-item[<?php echo (int) $_nav_menu_placeholder; ?>][menu-item-title]" value="<?php echo esc_attr($title); ?>">
                    <input type="hidden" class="menu-item-url" name="menu-item[<?php echo (int) $_nav_menu_placeholder; ?>][menu-item-url]" value="<?php echo esc_url($url); ?>">
                    <input type="hidden" class="menu-item-classes" name="menu-item[<?php echo (int) $_nav_menu_placeholder; ?>][menu-item-classes]" value="">
                </li>
            </ul>
        </div>

        <p class="button-controls wp-clearfix">
            <span class="add-to-menu">
                <input type="submit" class="button-secondary submit-add-to-menu right"
                       value="<?php esc_attr_e('Add to Menu'); ?>"
                       name="add-weblazem-seo-page"
                       id="submit-weblazem-seo-page">
                <span class="spinner"></span>
            </span>
        </p>

        <p class="description" style="margin-top:10px;">
            آدرس صفحه:
            <code dir="ltr" style="display:inline-block;word-break:break-all;"><?php echo esc_html($url); ?></code>
        </p>
    </div>
    <?php
}
