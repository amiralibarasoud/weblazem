<?php
/**
 * About Us page — admin menu link.
 */

function weblazem_aboutus_options_menu() {
    add_submenu_page(
        'weblazem-theme-options',
        'تنظیمات درباره ما',
        '  درباره ما',
        'manage_options',
        'weblazem-aboutus-options',
        'weblazem_aboutus_options_display'
    );
}
add_action('admin_menu', 'weblazem_aboutus_options_menu', 24);
