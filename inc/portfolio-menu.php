<?php
/**
 * Add portfolio archive link to Appearance → Menus.
 */

function weblazem_portfolio_nav_menu_meta_box() {
    add_meta_box(
        'weblazem-portfolio-archive-link',
        'صفحه نمونه کارها',
        'weblazem_portfolio_nav_menu_meta_box_render',
        'nav-menus',
        'side',
        'default'
    );
}
add_action('admin_head-nav-menus.php', 'weblazem_portfolio_nav_menu_meta_box');

function weblazem_portfolio_nav_menu_meta_box_render() {
    global $_nav_menu_placeholder;

    $_nav_menu_placeholder = isset($_nav_menu_placeholder) ? (int) $_nav_menu_placeholder - 1 : -1;

    $url   = weblazem_get_portfolio_page_url();
    $title = 'نمونه کارها';
    ?>
    <div id="weblazem-portfolio-archive-div" class="posttypediv">
        <div id="tabs-panel-weblazem-portfolio" class="tabs-panel tabs-panel-active">
            <ul id="weblazem-portfolio-archive-checklist" class="categorychecklist form-no-clear">
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
                       name="add-weblazem-portfolio-archive"
                       id="submit-weblazem-portfolio-archive">
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
