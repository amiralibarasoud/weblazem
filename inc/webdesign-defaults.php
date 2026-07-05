<?php
/**
 * Website design page — defaults and option bootstrap.
 */

function weblazem_webdesign_defaults() {
    $lorem = 'وب‌سایت فقط چند صفحه نیست؛ پایه‌ی اعتبار برند و ارتباط آنلاین شماست. طراحی حرفه‌ای، سرمایه‌گذاری بلندمدت برای آینده کسب‌وکار شماست.';

    return array(
        'hero_en_title'              => 'Website Design',
        'hero_calligraphy_text'      => '<span class="accent">اولین قدم</span> برای <span class="highlight">موفقیت</span> <span class="accent">آنلاین</span>',
        'hero_title'                 => 'طراحی وب‌سایت',
        'hero_text'                  => $lorem . "\n\n" . 'در وب‌سیما، طراحی وب‌سایت فراتر از زیبایی بصری است؛ ما با تحلیل دقیق نیازهای کسب‌وکار شما، زیرساختی مقیاس‌پذیر و امن می‌سازیم که پایه رشد دیجیتال شما باشد.',
        'hero_image'                 => '',
        'hero_stat1_number'          => '+700',
        'hero_stat1_title'           => 'پروژه موفق',
        'hero_stat1_desc'            => 'با همراهی معتبرترین برندهای کشور',
        'hero_stat2_number'          => '+80',
        'hero_stat2_title'           => 'ماژول اختصاصی',
        'hero_stat2_desc'            => 'هماهنگ پایدار و امن',
        'portfolio_calligraphy_text' => 'نمونه‌کارهای <span class="highlight">طراحی وب‌سایت</span>',
        'portfolio_subtitle'         => 'نمونه‌کارهای طراحی وب‌سایت و وب‌سیما',
        'portfolio_description'      => 'برای ما نتیجه مهم است! و همه نتایج برای ما افتخارآفرینند، همه تجربه‌ها، همه موفقیت‌ها',
        'portfolio_en_label'         => 'Success Stories',
        'portfolio_count'            => '12',
        'customers_calligraphy_text' => 'اعتماد شما شایسته تقدیر است',
        'customers_counter'          => '+700',
        'customers_counter_label'    => 'HAPPY CUSTOMERS',
        'process_calligraphy_text'   => 'یک سفر <span class="highlight">حرفه‌ای</span> در مسیر موفقیت',
        'process_subtitle'           => 'طراحی یک وب‌سایت حرفه‌ای به یک برنامه دقیق نیاز دارد',
        'process_description'        => 'فرآیند طراحی وب‌سایت در وب‌سیما فراتر از ظاهر است؛ از جلسه مشاوره تا تحویل نهایی، هر مرحله با دقت و شفافیت پیش می‌رود.',
        'process_start_note'         => 'از اینجا شروع کنیم',
        'process_journey_caption'    => 'مسیر طراحی، اجرا و تحویل پروژه شما',
        'process_csat_number'        => '98%',
        'process_csat_label'         => 'شاخص رضایت مشتریان',
        'process_csat_sub'           => 'CSAT — Customer Satisfaction Score',
        'process_btn1_text'          => 'معرفی تیم',
        'process_btn1_url'           => '#',
        'process_btn2_text'          => 'درباره ما',
        'process_btn2_url'           => '#',
        'advantages_title'           => 'مسیر موفقیت، با یک انتخاب درست آغاز می‌شود!',
        'advantages_subtitle'        => 'وب‌سایت ابزار قدرتمندی برای رشد کسب‌وکار است و به تیمی نیاز دارد که فراتر از طراحی فکر کند.',
        'faq_calligraphy_text'       => 'مشتاق شنیدن صدای شما هستیم',
        'faq_subtitle'               => 'پرسش‌های پرتکرار',
        'faq_intro'                  => 'پاسخ سوالات رایج درباره طراحی وب‌سایت را در این بخش بیابید. در صورت نیاز با ما تماس بگیرید.',
        'faq_phone'                  => '021 78358',
        'faq_consult_btn_text'       => 'ثبت درخواست مشاوره',
        'faq_footer_text'            => 'ما ترکیب مطلوبی از خلاقیت و تکنولوژی را برای توسعه کسب‌وکارهای دیجیتال خلق کرده‌ایم',
    );
}

function weblazem_get_default_webdesign_splits() {
    return array(
        array(
            'title'       => 'طراحی سایت شرکتی؛ ساخت تصویر معتبر از برند شما',
            'text'        => 'وب‌سایت شرکتی، نماینده رسمی کسب‌وکار شما در فضای دیجیتال است. طراحی حرفه‌ای اعتماد مشتریان و سرمایه‌گذاران را جلب می‌کند.',
            'button_text' => 'مطالعه بیشتر',
            'button_url'  => '#',
            'button_modal'=> '0',
            'image'       => '',
            'caption'     => 'اعتبار برند شما در فضای دیجیتال',
            'layout'      => 'right',
        ),
        array(
            'title'       => 'طراحی فروشگاه اینترنتی؛ تبدیل بازدیدکننده به خریدار واقعی',
            'text'        => 'مسیر خرید ساده و سریع، کلید افزایش نرخ تبدیل است. فروشگاه اینترنتی حرفه‌ای، تجربه‌ای روان برای مشتریان شما می‌سازد.',
            'button_text' => 'مطالعه بیشتر',
            'button_url'  => '#',
            'button_modal'=> '0',
            'image'       => '',
            'caption'     => 'افزایش فروش با یک تجربه خرید حرفه‌ای',
            'layout'      => 'left',
        ),
    );
}

function weblazem_get_default_webdesign_process_steps() {
    return array(
        array('title' => 'جلسه مشاوره کسب‌وکار'),
        array('title' => 'نیازسنجی و ارائه راهکار'),
        array('title' => 'تدوین پروپوزال'),
        array('title' => 'تایید پروپوزال و عقد قرارداد'),
        array('title' => 'ارجاع به تیم اجرایی'),
    );
}

function weblazem_get_default_webdesign_advantages() {
    $lorem = 'لورم ایپسوم متن ساختگی با تولید سادگی نامفهوم از صنعت چاپ و با استفاده از طراحان گرافیک است.';
    return array(
        array('icon' => 'cube', 'title' => 'کدنویسی اختصاصی و وردپرس حرفه‌ای؛ زیرساختی برای رشد آینده', 'text' => $lorem),
        array('icon' => 'document', 'title' => 'پروپوزال اختصاصی؛ شروع پروژه با مسیر شفاف', 'text' => $lorem),
        array('icon' => 'heart', 'title' => 'اکانت منیجر اختصاصی؛ هماهنگی کامل بین شما و تیم فنی', 'text' => $lorem),
        array('icon' => 'headset', 'title' => 'پشتیبان اختصاصی؛ پاسخ‌گویی سریع و دقیق در تمام مراحل', 'text' => $lorem),
        array('icon' => 'layers', 'title' => 'تجربه کاربری روان؛ دلیل بازگشت دوباره کاربران', 'text' => $lorem),
        array('icon' => 'rocket', 'title' => 'استفاده از تکنولوژی‌های روز؛ امنیت، سرعت و توسعه‌پذیری', 'text' => $lorem),
        array('icon' => 'chart', 'title' => 'پشتیبانی و توسعه مستمر؛ همکاری بلندمدت با وب‌سیما', 'text' => $lorem),
        array('icon' => 'nodes', 'title' => 'شناخت عمیق کسب‌وکار شما؛ پایه طراحی اختصاصی', 'text' => $lorem),
    );
}

function weblazem_get_default_webdesign_faq_items() {
    $q = 'مدت زمان طراحی وب‌سایت چقدر است؟';
    $a = 'بسته به پیچیدگی پروژه، بین ۴ تا ۱۲ هفته زمان نیاز است. پس از جلسه مشاوره، زمان‌بندی دقیق ارائه می‌شود.';
    $items = array();
    for ($i = 0; $i < 6; $i++) {
        $items[] = array('question' => $q, 'answer' => $a);
    }
    return $items;
}

function weblazem_get_default_webdesign_service_cards() {
    return array(
        array(
            'title'       => 'سئو و بازاریابی دیجیتال',
            'en_title'    => 'SEO AND DIGITAL MARKETING',
            'description' => 'توسعه و افزایش تعامل',
            'url'         => '#',
            'shape_image' => '',
        ),
        array(
            'title'       => 'استراتژی دیجیتال',
            'en_title'    => 'DIGITAL STRATEGY',
            'description' => 'نقشه راهی برای موفقیت آنلاین',
            'url'         => '#',
            'shape_image' => '',
        ),
    );
}

function weblazem_ensure_webdesign_defaults() {
    foreach (weblazem_webdesign_defaults() as $key => $value) {
        $option_key = 'weblazem_webdesign_' . $key;
        if (get_option($option_key) === false) {
            update_option($option_key, $value);
        }
    }

    $arrays = array(
        'weblazem_webdesign_splits'            => 'weblazem_get_default_webdesign_splits',
        'weblazem_webdesign_process_steps'     => 'weblazem_get_default_webdesign_process_steps',
        'weblazem_webdesign_advantages_items'  => 'weblazem_get_default_webdesign_advantages',
        'weblazem_webdesign_faq_items'         => 'weblazem_get_default_webdesign_faq_items',
        'weblazem_webdesign_service_cards'     => 'weblazem_get_default_webdesign_service_cards',
        'weblazem_webdesign_portfolio_tabs'    => 'weblazem_get_default_webdesign_portfolio_tabs',
        'weblazem_webdesign_customers_logos'   => 'weblazem_get_default_webdesign_customer_logos',
        'weblazem_webdesign_portfolio_items'   => 'weblazem_get_empty_array',
    );

    foreach ($arrays as $option_key => $callback) {
        if (get_option($option_key) === false) {
            $value = is_callable($callback) ? call_user_func($callback) : array();
            update_option($option_key, $value);
        }
    }
}
add_action('init', 'weblazem_ensure_webdesign_defaults', 14);

function weblazem_get_empty_array() {
    return array();
}

function weblazem_get_default_webdesign_customer_logos() {
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
