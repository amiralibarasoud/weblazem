<?php
/**
 * Contact page — admin menu links.
 */

function weblazem_contact_menu() {
    add_submenu_page(
        'weblazem-theme-options',
        'تنظیمات تماس با ما',
        '  تماس با ما',
        'manage_options',
        'weblazem-contact-options',
        'weblazem_contact_options_display'
    );

    add_submenu_page(
        'weblazem-theme-options',
        'پیام‌های تماس',
        'پیام‌های تماس',
        'manage_options',
        'edit.php?post_type=contact_request'
    );
}
add_action('admin_menu', 'weblazem_contact_menu', 25);
