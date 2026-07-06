<?php
/**
 * Content production & support — defaults.
 */

function weblazem_contentsupport_defaults() {
    $hero_text = 'محتوای باکیفیت و پشتیبانی حرفه‌ای، دو ستون اصلی موفقیت وب‌سایت شماست. در وب‌لازم، تولید مقالات سفارشی، محتوای سئو محور و پشتیبانی فنی مستمر را با تعرفه‌های شفاف و قابل پیش‌بینی ارائه می‌دهیم تا سایت شما همیشه به‌روز، امن و پربازدید بماند.';

    return array(
        'hero_en_title'              => 'Content Production & Support',
        'hero_calligraphy_text'      => '<span class="accent">محتوای</span> <span class="highlight">اثربخش</span> <span class="accent">و پشتیبانی</span>',
        'hero_title'                 => 'تولید محتوا و پشتیبانی',
        'hero_text'                  => $hero_text,
        'hero_image'                 => '',
        'hero_stat1_number'          => '+2000',
        'hero_stat1_title'           => 'مقاله تولیدشده',
        'hero_stat1_desc'            => 'محتوای سئو محور و تخصصی',
        'hero_stat2_number'          => '+350',
        'hero_stat2_title'           => 'سایت پشتیبانی‌شده',
        'hero_stat2_desc'            => 'پاسخ‌گویی سریع و مستمر',
        'portfolio_calligraphy_text' => 'نمونه‌کارهای <span class="highlight">محتوا</span> و پشتیبانی',
        'portfolio_subtitle'         => 'پروژه‌هایی که با محتوا و پشتیبانی رشد کردند',
        'portfolio_description'      => 'از تولید مقالات سفارشی تا نگهداری فنی سایت، این نمونه‌کارها نشان می‌دهند چگونه تیم وب‌لازم به رشد برندها کمک کرده است.',
        'portfolio_en_label'         => 'Success Stories',
        'portfolio_count'            => '12',
        'customers_calligraphy_text' => 'اعتمادی که به آن <span class="highlight">افتخار</span> می‌کنیم',
        'customers_counter'          => '+350',
        'customers_counter_label'    => 'SUPPORTED WEBSITES',
        'process_calligraphy_text'   => 'یک سفر <span class="highlight">حرفه‌ای</span> در مسیر موفقیت',
        'process_subtitle'           => 'فرآیند شفاف تولید محتوا و پشتیبانی؛ از نیازسنجی تا تحویل',
        'process_description'        => 'از تعیین استراتژی محتوا و تعرفه مقالات سفارشی تا اجرای منظم پشتیبانی فنی، هر مرحله با برنامه مشخص و گزارش‌دهی شفاف پیش می‌رود.',
        'process_start_note'         => 'از اینجا شروع کنیم',
        'process_journey_caption'    => 'مسیر تولید محتوا، انتشار و پشتیبانی مستمر سایت شما',
        'process_csat_number'        => '98%',
        'process_csat_label'         => 'شاخص رضایت مشتریان',
        'process_csat_sub'           => 'CSAT — Customer Satisfaction Score',
        'process_btn1_text'          => 'معرفی تیم',
        'process_btn1_url'           => '#',
        'process_btn2_text'          => 'درباره ما',
        'process_btn2_url'           => '#',
        'advantages_title'           => 'محتوای درست + پشتیبانی مطمئن = رشد پایدار!',
        'advantages_subtitle'        => 'تعرفه‌های شفاف تولید محتوا و پشتیبانی وب‌سایت، بدون هزینه پنهان.',
        'faq_calligraphy_text'       => 'مشتاق شنیدن نیازهای محتوایی شما هستیم',
        'faq_subtitle'               => 'پرسش‌های متداول',
        'faq_intro'                  => 'پاسخ سوالات رایج درباره تولید محتوا، تعرفه مقالات سفارشی و پشتیبانی وب‌سایت را در این بخش بیابید.',
        'faq_phone'                  => '021 78358',
        'faq_consult_btn_text'       => 'ثبت درخواست مشاوره',
        'faq_footer_text'            => 'ما ترکیبی از خلاقیت محتوایی و پشتیبانی فنی را برای رشد پایدار وب‌سایت شما ارائه می‌دهیم',
    );
}

function weblazem_get_default_contentsupport_splits() {
    return array(
        array(
            'title'        => 'تولید محتوا و مقالات سفارشی',
            'text'         => 'مقالات سئو محور، محتوای صفحات خدمات، وبلاگ و کپشن شبکه‌های اجتماعی با لحن برند شما تولید می‌شود. تعرفه‌ها بر اساس تعداد کلمات، سطح تخصصی و تحقیق کلمه کلیدی تعیین می‌شود.',
            'button_text'  => 'درخواست مشاوره و تعرفه',
            'button_url'   => '#',
            'button_modal' => '1',
            'image'        => '',
            'caption'      => 'محتوایی که مخاطب را جذب و به مشتری تبدیل می‌کند',
            'layout'       => 'right',
        ),
        array(
            'title'        => 'پشتیبانی وب‌سایت',
            'text'         => 'به‌روزرسانی‌ها، رفع باگ، پشتیبان‌گیری، مانیتورینگ امنیت و پاسخ‌گویی سریع به درخواست‌های فنی. بسته‌های ماهانه پشتیبانی متناسب با اندازه و نیاز سایت شما طراحی شده‌اند.',
            'button_text'  => 'مشاهده بسته‌های پشتیبانی',
            'button_url'   => '#',
            'button_modal' => '1',
            'image'        => '',
            'caption'      => 'آرامش خاطر با پشتیبانی حرفه‌ای',
            'layout'       => 'left',
        ),
        array(
            'title'        => 'تعرفه تولید محتوا',
            'text'         => 'بسته‌های مقاله سفارشی از ۸۰۰ تا ۲۵۰۰ کلمه، با گزینه تحقیق سئو، ویراستاری و انتشار در سایت. برای دریافت تعرفه دقیق متناسب با حوزه فعالیتتان، درخواست مشاوره ثبت کنید.',
            'button_text'  => 'دریافت تعرفه',
            'button_url'   => '#',
            'button_modal' => '1',
            'image'        => '',
            'caption'      => 'شفاف، منعطف و متناسب با بودجه شما',
            'layout'       => 'right',
        ),
    );
}

function weblazem_get_default_contentsupport_process_steps() {
    return array(
        array('title' => 'جلسه مشاوره و تعیین نیاز محتوایی'),
        array('title' => 'ارائه تعرفه و بسته پیشنهادی'),
        array('title' => 'تایید قرارداد و تقویم انتشار'),
        array('title' => 'تولید، ویراستاری و تایید محتوا'),
        array('title' => 'انتشار و پشتیبانی مستمر'),
    );
}

function weblazem_get_default_contentsupport_advantages() {
    return array(
        array('icon' => 'coffee', 'title' => 'مقالات سفارشی سئو محور', 'text' => 'محتوایی که هم برای مخاطب ارزش دارد و هم در نتایج جستجو دیده می‌شود.'),
        array('icon' => 'document', 'title' => 'تعرفه شفاف تولید محتوا', 'text' => 'قیمت‌گذاری روشن بر اساس تعداد کلمات، سطح تخصص و خدمات جانبی.'),
        array('icon' => 'headset', 'title' => 'پشتیبانی فنی ۷/۲۴', 'text' => 'تیم پشتیبانی برای رفع مشکلات فوری و نگهداری روزانه سایت در دسترس است.'),
        array('icon' => 'chart', 'title' => 'گزارش عملکرد محتوا', 'text' => 'ارائه گزارش دوره‌ای از بازدید، تعامل و رتبه کلمات کلیدی.'),
        array('icon' => 'target', 'title' => 'هم‌راستا با استراتژی برند', 'text' => 'لحن، سبک و پیام محتوا دقیقاً مطابق هویت برند شما تنظیم می‌شود.'),
        array('icon' => 'layers', 'title' => 'بسته‌های ترکیبی محتوا + پشتیبانی', 'text' => 'امکان ترکیب تولید محتوا و پشتیبانی ماهانه در یک قرارداد یکپارچه.'),
        array('icon' => 'rocket', 'title' => 'انتشار سریع و منظم', 'text' => 'تقویم محتوایی منظم برای حفظ تعامل مخاطب و سیگنال مثبت به گوگل.'),
        array('icon' => 'heart', 'title' => 'ویراستاری و کنترل کیفیت', 'text' => 'هر محتوا پیش از انتشار از نظر نگارشی، فنی و سئو بررسی می‌شود.'),
    );
}

function weblazem_get_default_contentsupport_faq_items() {
    return array(
        array('question' => 'تعرفه تولید مقاله سفارشی چگونه محاسبه می‌شود؟', 'answer' => 'بر اساس تعداد کلمات، سطح تخصصی موضوع، نیاز به تحقیق سئو و تعداد تصاویر. پس از مشاوره، پیش‌فاکتور شفاف ارائه می‌شود.'),
        array('question' => 'آیا محتوا برای سئو بهینه می‌شود؟', 'answer' => 'بله. تمام مقالات با رعایت ساختار سئو، کلمات کلیدی هدف و لینک‌سازی داخلی تولید می‌شوند.'),
        array('question' => 'پشتیبانی وب‌سایت شامل چه مواردی است؟', 'answer' => 'به‌روزرسانی وردپرس و افزونه‌ها، پشتیبان‌گیری، رفع باگ، مانیتورینگ امنیت و پاسخ به درخواست‌های فنی.'),
        array('question' => 'چقدر طول می‌کشد تا یک مقاله تحویل داده شود؟', 'answer' => 'معمولاً بین ۳ تا ۷ روز کاری بسته به حجم و پیچیدگی موضوع. برای پروژه‌های فوری، بسته اکسپرس نیز داریم.'),
        array('question' => 'آیا می‌توانم فقط پشتیبانی بگیرم بدون تولید محتوا؟', 'answer' => 'بله. بسته‌های پشتیبانی مستقل از خدمات محتوا قابل ارائه هستند.'),
        array('question' => 'چگونه درخواست مشاوره ثبت کنم؟', 'answer' => 'از دکمه «درخواست مشاوره» در سایت استفاده کنید تا تیم ما در کوتاه‌ترین زمان با شما تماس بگیرد.'),
    );
}

function weblazem_get_default_contentsupport_service_cards() {
    $webdesign_url = function_exists('weblazem_get_webdesign_page_url') ? weblazem_get_webdesign_page_url() : '#';
    $seo_url       = function_exists('weblazem_get_seo_page_url') ? weblazem_get_seo_page_url() : '#';

    return array(
        array(
            'title'       => 'طراحی وب‌سایت',
            'en_title'    => 'WEB DESIGN',
            'description' => 'اولین قدم برای موفقیت آنلاین',
            'url'         => $webdesign_url,
            'shape_image' => '',
        ),
        array(
            'title'       => 'سئو و بازاریابی دیجیتال',
            'en_title'    => 'SEO & DIGITAL MARKETING',
            'description' => 'توسعه و افزایش تعامل',
            'url'         => $seo_url,
            'shape_image' => '',
        ),
    );
}

function weblazem_get_default_contentsupport_customer_logos() {
    $logos = array();
    for ($i = 1; $i <= 10; $i++) {
        $logos[] = array(
            'name' => 'مشتری ' . $i,
            'logo' => get_template_directory_uri() . '/assets/images/customers/logo-' . (($i - 1) % 8 + 1) . '.svg',
            'url'  => '',
        );
    }
    return $logos;
}

function weblazem_ensure_contentsupport_defaults() {
    foreach (weblazem_contentsupport_defaults() as $key => $value) {
        $option_key = 'weblazem_contentsupport_' . $key;
        if (get_option($option_key) === false) {
            update_option($option_key, $value);
        }
    }

    $arrays = array(
        'weblazem_contentsupport_splits'            => 'weblazem_get_default_contentsupport_splits',
        'weblazem_contentsupport_process_steps'     => 'weblazem_get_default_contentsupport_process_steps',
        'weblazem_contentsupport_advantages_items'  => 'weblazem_get_default_contentsupport_advantages',
        'weblazem_contentsupport_faq_items'         => 'weblazem_get_default_contentsupport_faq_items',
        'weblazem_contentsupport_service_cards'     => 'weblazem_get_default_contentsupport_service_cards',
        'weblazem_contentsupport_portfolio_tabs'    => 'weblazem_get_default_contentsupport_portfolio_tabs',
        'weblazem_contentsupport_customers_logos'   => 'weblazem_get_default_contentsupport_customer_logos',
        'weblazem_contentsupport_portfolio_items'   => 'weblazem_get_empty_array',
    );

    foreach ($arrays as $option_key => $callback) {
        if (get_option($option_key) === false) {
            $value = is_callable($callback) ? call_user_func($callback) : array();
            update_option($option_key, $value);
        }
    }
}
add_action('init', 'weblazem_ensure_contentsupport_defaults', 14);
