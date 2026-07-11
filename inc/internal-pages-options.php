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
                        <a href="<?php echo esc_url(admin_url('admin.php?page=weblazem-contentsupport-options')); ?>">
                            <strong>تنظیمات تولید محتوا و پشتیبانی</strong>
                        </a>
                        — هیرو، نمونه‌کارها، مشتریان، بخش‌های دو ستونه، فرآیند، مزایا و FAQ
                    </li>
                    <li>
                        <a href="<?php echo esc_url(admin_url('admin.php?page=weblazem-blogarchive-options')); ?>">
                            <strong>تنظیمات آرشیو بلاگ</strong>
                        </a>
                        — هیرو، لیست مقالات و صفحه‌بندی
                    </li>
                    <li>
                        <a href="<?php echo esc_url(admin_url('admin.php?page=weblazem-blog-single-options')); ?>">
                            <strong>تنظیمات مقاله بلاگ (تک‌مقاله)</strong>
                        </a>
                        — بنر، سایدبار، مقالات مرتبط و بخش نظرات
                    </li>
                    <li>
                        <a href="<?php echo esc_url(admin_url('admin.php?page=weblazem-contact-options')); ?>">
                            <strong>تنظیمات تماس با ما</strong>
                        </a>
                        — اطلاعات تماس، فرم، پیامک و مشاهده پیام‌های دریافتی
                    </li>
                    <li>
                        <a href="<?php echo esc_url(admin_url('admin.php?page=weblazem-contact-options')); ?>">
                            <strong>تنظیمات تماس با ما</strong>
                        </a>
                        — اطلاعات تماس، فرم، پیامک و پیام‌های دریافتی
                    </li>
                    <li>
                        <a href="<?php echo esc_url(admin_url('admin.php?page=weblazem-devproject-options')); ?>">
                            <strong>تنظیمات برنامه نویسی و پروژه اختصاصی</strong>
                        </a>
                        — هیرو، نمونه‌کارها، مشتریان، بخش‌های دو ستونه، فرآیند، مزایا و FAQ
                    </li>
                    <li>
                        <a href="<?php echo esc_url(admin_url('admin.php?page=weblazem-pricing-options')); ?>">
                            <strong>تنظیمات خدمات و تعرفه‌ها</strong>
                        </a>
                        — هیرو، دسته‌بندی خدمات، تعرفه‌ها، پلن طراحی سایت و مشاوره
                    </li>
                    <li>
                        <a href="<?php echo esc_url(admin_url('admin.php?page=weblazem-aboutus-options')); ?>">
                            <strong>تنظیمات درباره ما</strong>
                        </a>
                        — هیرو، تایم‌لاین، مدیرعامل، تیم، کارت‌های خدمات و مشاوره
                    </li>
                    <li>
                        <a href="<?php echo esc_url(admin_url('admin.php?page=weblazem-seo-options')); ?>">
                            <strong>تنظیمات سئو و بازاریابی دیجیتال</strong>
                        </a>
                        — هیرو، مشتریان، بخش‌های دو ستونه، فرآیند، مزایا و FAQ
                    </li>
                    <li>
                        <a href="<?php echo esc_url(admin_url('admin.php?page=weblazem-webdesign-options')); ?>">
                            <strong>تنظیمات طراحی سایت</strong>
                        </a>
                        — هیرو، نمونه‌کارها، مشتریان، فرآیند، مزایا و FAQ
                    </li>
                    <li>
                        <a href="<?php echo esc_url(admin_url('admin.php?page=weblazem-service-landing')); ?>">
                            <strong>صفحات خدمات داینامیک</strong>
                        </a>
                        — ساخت نامحدود صفحه با قالب طراحی سایت (فروشگاهی، شرکتی و …)
                    </li>
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
                        <a href="<?php echo esc_url(admin_url('admin.php?page=weblazem-price-estimator-options')); ?>">
                            <strong>محاسبه‌گر قیمت</strong>
                        </a>
                        — تنظیمات برآورد هزینه و لیدها
                    </li>
                    <li>
                        <a href="<?php echo esc_url(admin_url('admin.php?page=weblazem-live-demo-options')); ?>">
                            <strong>دموی زنده نمونه‌کارها</strong>
                        </a>
                        — iframe / ویدیو و تنظیمات صفحه دمو
                    </li>
                    <li>
                        <a href="<?php echo esc_url(admin_url('admin.php?page=weblazem-case-study-options')); ?>">
                            <strong>داستان موفقیت</strong>
                        </a>
                        — چالش، رویکرد، نتایج و متریک‌ها
                    </li>
                    <li>
                        <a href="<?php echo esc_url(admin_url('admin.php?page=weblazem-plan-comparator-options')); ?>">
                            <strong>مقایسه پلن‌ها</strong>
                        </a>
                        — پلن‌ها، فیلترها و جدول مقایسه
                    </li>
                    <li>
                        <a href="<?php echo esc_url(admin_url('admin.php?page=weblazem-referral-options')); ?>">
                            <strong>باشگاه معرفی</strong>
                        </a>
                        — کد معرف، پاداش و لیدهای معرفی
                    </li>
                    <li>
                        <a href="<?php echo esc_url(admin_url('admin.php?page=weblazem-resources-hub-options')); ?>">
                            <strong>مرکز منابع</strong>
                        </a>
                        — چک‌لیست‌ها، فایل‌ها و لید دانلود
                    </li>
                    <li>
                        <a href="<?php echo esc_url(admin_url('admin.php?page=weblazem-csat-options')); ?>">
                            <strong>نظرسنجی رضایت (CSAT)</strong>
                        </a>
                        — دعوت‌نامه، توکن، پاسخ‌ها و نمایش عمومی
                    </li>
                    <li>
                        <a href="<?php echo esc_url(admin_url('admin.php?page=weblazem-scheduling-options')); ?>">
                            <strong>رزرو مشاوره</strong>
                        </a>
                        — ساعت‌های کاری، روزها و رزروها
                    </li>
                    <li>
                        <a href="<?php echo esc_url(admin_url('admin.php?page=weblazem-project-status-options')); ?>">
                            <strong>وضعیت پروژه</strong>
                        </a>
                        — پنل مشتری و مدیریت پروژه‌ها
                    </li>
                    <li>
                        <a href="<?php echo esc_url(admin_url('admin.php?page=weblazem-start-project-options')); ?>">
                            <strong>شروع پروژه</strong>
                        </a>
                        — بریف چندمرحله‌ای و درخواست‌های دریافتی
                    </li>
                    <li>
                        <a href="<?php echo esc_url(function_exists('weblazem_get_contentsupport_page_url') ? weblazem_get_contentsupport_page_url() : '#'); ?>" target="_blank" rel="noopener">
                            <strong>برگه تولید محتوا و پشتیبانی</strong>
                        </a>
                        — تولید محتوا، تعرفه مقالات و پشتیبانی وب‌سایت
                    </li>
                    <li>
                        <a href="<?php echo esc_url(function_exists('weblazem_get_blogarchive_page_url') ? weblazem_get_blogarchive_page_url() : '#'); ?>" target="_blank" rel="noopener">
                            <strong>برگه آرشیو بلاگ (مجله وب‌لازم)</strong>
                        </a>
                        — لیست مقالات و مجله
                    </li>
                    <li>
                        <a href="<?php echo esc_url(function_exists('weblazem_get_contact_page_url') ? weblazem_get_contact_page_url() : '#'); ?>" target="_blank" rel="noopener">
                            <strong>برگه تماس با ما</strong>
                        </a>
                        — فرم تماس و اطلاعات ارتباطی
                    </li>
                    <li>
                        <a href="<?php echo esc_url(function_exists('weblazem_get_devproject_page_url') ? weblazem_get_devproject_page_url() : '#'); ?>" target="_blank" rel="noopener">
                            <strong>برگه برنامه نویسی و پروژه اختصاصی</strong>
                        </a>
                        — صفحه داخلی خدمات توسعه نرم‌افزار
                    </li>
                    <li>
                        <a href="<?php echo esc_url(function_exists('weblazem_get_pricing_page_url') ? weblazem_get_pricing_page_url() : '#'); ?>" target="_blank" rel="noopener">
                            <strong>برگه خدمات و تعرفه‌ها</strong>
                        </a>
                        — صفحه داخلی تعرفه‌ها و خدمات
                    </li>
                    <li>
                        <a href="<?php echo esc_url(function_exists('weblazem_get_aboutus_page_url') ? weblazem_get_aboutus_page_url() : '#'); ?>" target="_blank" rel="noopener">
                            <strong>برگه درباره ما</strong>
                        </a>
                        — صفحه داخلی معرفی شرکت، تیم و مسیر رشد
                    </li>
                    <li>
                        <a href="<?php echo esc_url(function_exists('weblazem_get_seo_page_url') ? weblazem_get_seo_page_url() : '#'); ?>" target="_blank" rel="noopener">
                            <strong>برگه سئو و بازاریابی دیجیتال</strong>
                        </a>
                        — صفحه داخلی خدمات سئو و دیجیتال مارکتینگ
                    </li>
                    <li>
                        <a href="<?php echo esc_url(function_exists('weblazem_get_webdesign_page_url') ? weblazem_get_webdesign_page_url() : '#'); ?>" target="_blank" rel="noopener">
                            <strong>برگه طراحی سایت</strong>
                        </a>
                        — صفحه داخلی خدمات طراحی وب‌سایت
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
