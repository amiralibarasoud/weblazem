<?php
/**
 * Internal pages settings hub — parent submenu under theme options.
 */

function weblazem_internal_pages_menu() {
    add_submenu_page(
        'weblazem-theme-options',
        'تنظیمات صفحات داخلی',
        'تنظیمات صفحات داخلی',
        'manage_options',
        'weblazem-internal-pages',
        'weblazem_internal_pages_hub_display'
    );
}
add_action('admin_menu', 'weblazem_internal_pages_menu', 20);

function weblazem_internal_pages_hub_display() {
    ?>
    <div class="wrap" dir="rtl">
        <div class="weblazem-admin-header">
            <div class="weblazem-admin-header-content">
                <h1>تنظیمات صفحات داخلی</h1>
                <p>از این بخش می‌توانید محتوای صفحات داخلی سایت را مدیریت کنید.</p>
            </div>
        </div>

        <div class="weblazem-admin-content" style="margin-top:24px;">
            <div class="weblazem-admin-card">
                <div class="weblazem-admin-card-icon"><i class="fas fa-images"></i></div>
                <h3>صفحات قابل تنظیم</h3>
                <ul style="list-style:disc;padding-right:24px;line-height:2.2;">
                    <li>
                        <a href="<?php echo esc_url(admin_url('admin.php?page=weblazem-portfolio-page-options')); ?>">
                            <strong>تنظیمات نمونه کار</strong>
                        </a>
                        — هیرو، آخرین پروژه‌ها، تعرفه‌ها و بخش تمام پروژه‌ها
                    </li>
                    <li>
                        <a href="<?php echo esc_url(admin_url('admin.php?page=weblazem-portfolio-single-options')); ?>">
                            <strong>تنظیمات نمونه کار تکی</strong>
                        </a>
                        — بخش‌های ثابت: نمونه‌کارهای بیشتر، مشاوره و CTA پایانی
                    </li>
                    <li>
                        <a href="<?php echo esc_url(admin_url('edit.php?post_type=page')); ?>">
                            <strong>برگه نمونه کارها</strong>
                        </a>
                        — در بخش برگه‌ها با عنوان «نمونه کارها» ساخته شده است
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <?php
}
