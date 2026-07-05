<?php
/**
 * Custom development page — defaults.
 */

function weblazem_devproject_defaults() {
    $hero_text = 'توسعه نرم‌افزار اختصاصی راهی مطمئن برای حل چالش‌های واقعی کسب‌وکار شماست. در وب‌لازم، با تحلیل دقیق فرآیندها و نیازهای سازمانی، راهکارهای نرم‌افزاری مقیاس‌پذیر، امن و قابل توسعه طراحی و پیاده‌سازی می‌کنیم.';

    return array(
        'hero_en_title'              => 'Custom Software Development',
        'hero_calligraphy_text'      => '<span class="accent">راهکارهای</span> <span class="highlight">نرم‌افزاری</span> <span class="accent">اختصاصی</span>',
        'hero_title'                 => 'برنامه‌نویسی و پروژه اختصاصی',
        'hero_text'                  => $hero_text,
        'hero_image'                 => '',
        'hero_stat1_number'          => '+120',
        'hero_stat1_title'           => 'پروژه نرم‌افزاری',
        'hero_stat1_desc'            => 'از استارتاپ تا سازمان‌های بزرگ',
        'hero_stat2_number'          => '+50',
        'hero_stat2_title'           => 'ماژول و API',
        'hero_stat2_desc'            => 'یکپارچه، پایدار و مستند',
        'portfolio_calligraphy_text' => 'نمونه‌کارهای <span class="highlight">برنامه‌نویسی</span> اختصاصی',
        'portfolio_subtitle'         => 'پروژه‌های نرم‌افزاری اجراشده توسط تیم وب‌لازم',
        'portfolio_description'      => 'هر پروژه، تجربه‌ای از حل مسئله، نوآوری و تحویل به‌موقع است. نمونه‌کارهای ما گواهی بر تخصص در توسعه سیستم‌های اختصاصی است.',
        'portfolio_en_label'         => 'Success Stories',
        'portfolio_count'            => '12',
        'customers_calligraphy_text' => 'اعتمادی که به آن <span class="highlight">افتخار</span> می‌کنیم',
        'customers_counter'          => '+120',
        'customers_counter_label'    => 'SOFTWARE PROJECTS',
        'process_calligraphy_text'   => 'یک سفر <span class="highlight">حرفه‌ای</span> در مسیر موفقیت',
        'process_subtitle'           => 'فرآیند شفاف توسعه نرم‌افزار؛ از ایده تا تحویل',
        'process_description'        => 'پروژه‌های نرم‌افزاری با برنامه‌ریزی دقیق، مستندسازی کامل و همکاری نزدیک با تیم شما پیش می‌روند تا در هر مرحله خروجی قابل اندازه‌گیری داشته باشید.',
        'process_start_note'         => 'از اینجا شروع کنیم',
        'process_journey_caption'    => 'مسیر تحلیل، توسعه، تست و تحویل پروژه شما',
        'process_csat_number'        => '98%',
        'process_csat_label'         => 'شاخص رضایت مشتریان',
        'process_csat_sub'           => 'CSAT — Customer Satisfaction Score',
        'process_btn1_text'          => 'معرفی تیم',
        'process_btn1_url'           => '#',
        'process_btn2_text'          => 'درباره ما',
        'process_btn2_url'           => '#',
        'advantages_title'           => 'توسعه اختصاصی، انتخابی برای رشد پایدار!',
        'advantages_subtitle'        => 'نرم‌افزار اختصاصی باید با فرآیندهای واقعی کسب‌وکار شما هم‌راستا باشد، نه برعکس.',
        'faq_calligraphy_text'       => 'مشتاق شنیدن ایده‌های شما هستیم',
        'faq_subtitle'               => 'پرسش‌های متداول',
        'faq_intro'                  => 'پاسخ سوالات رایج درباره برنامه‌نویسی و پروژه‌های اختصاصی را در این بخش بیابید تا با اطمینان بیشتری تصمیم بگیرید.',
        'faq_phone'                  => '021 78358',
        'faq_consult_btn_text'       => 'ثبت درخواست مشاوره',
        'faq_footer_text'            => 'ما ترکیبی از مهندسی نرم‌افزار و درک عمیق کسب‌وکار را برای ساخت راهکارهای اثربخش به کار می‌گیریم',
    );
}

function weblazem_get_default_devproject_splits() {
    return array(
        array(
            'title'        => 'توسعه اپلیکیشن و API',
            'text'         => 'اپلیکیشن‌های وب و موبایل و APIهای RESTful پایه ارتباط سیستم‌های شما با دنیای بیرون هستند. با معماری تمیز و مستندسازی کامل، توسعه‌پذیری آینده را تضمین می‌کنیم.',
            'button_text'  => 'مشاهده بیشتر',
            'button_url'   => '#',
            'button_modal' => '0',
            'image'        => '',
            'caption'      => 'اتصال هوشمند سیستم‌ها و سرویس‌ها',
            'layout'       => 'right',
        ),
        array(
            'title'        => 'سیستم‌های اختصاصی و اتوماسیون',
            'text'         => 'نرم‌افزار اختصاصی دقیقاً مطابق فرآیندهای سازمان شما ساخته می‌شود. اتوماسیون کارهای تکراری، کاهش خطای انسانی و افزایش بهره‌وری از مزایای اصلی آن است.',
            'button_text'  => 'مشاهده بیشتر',
            'button_url'   => '#',
            'button_modal' => '0',
            'image'        => '',
            'caption'      => 'دیجیتالی‌سازی فرآیندهای کسب‌وکار',
            'layout'       => 'left',
        ),
        array(
            'title'        => 'پشتیبانی و توسعه مستمر',
            'text'         => 'پس از تحویل پروژه، همراهی تیم فنی ادامه می‌یابد. به‌روزرسانی‌ها، رفع باگ، افزودن قابلیت جدید و مانیتورینگ عملکرد بخشی از خدمات پشتیبانی ما است.',
            'button_text'  => 'مشاهده بیشتر',
            'button_url'   => '#',
            'button_modal' => '0',
            'image'        => '',
            'caption'      => 'رشد مداوم محصول نرم‌افزاری شما',
            'layout'       => 'right',
        ),
    );
}

function weblazem_get_default_devproject_process_steps() {
    return array(
        array('title' => 'جلسه مشاوره و کشف نیاز'),
        array('title' => 'تحلیل فنی و طراحی معماری'),
        array('title' => 'تدوین پروپوزال و برآورد'),
        array('title' => 'تایید قرارداد و برنامه‌ریزی اسپرینت'),
        array('title' => 'توسعه، تست و تحویل'),
    );
}

function weblazem_get_default_devproject_advantages() {
    return array(
        array('icon' => 'cube', 'title' => 'معماری نرم‌افزار مقیاس‌پذیر', 'text' => 'طراحی ساختار ماژولار که رشد آینده محصول را بدون بازنویسی کامل ممکن می‌کند.'),
        array('icon' => 'document', 'title' => 'مستندسازی کامل پروژه', 'text' => 'مستندات فنی، API و راهنمای کاربری برای انتقال دانش و نگهداری آسان‌تر.'),
        array('icon' => 'headset', 'title' => 'پشتیبانی فنی اختصاصی', 'text' => 'تیم پشتیبانی پاسخ‌گو در تمام مراحل توسعه و پس از تحویل پروژه.'),
        array('icon' => 'layers', 'title' => 'ماژول‌های قابل توسعه', 'text' => 'افزودن قابلیت‌های جدید بدون اختلال در عملکرد بخش‌های موجود.'),
        array('icon' => 'rocket', 'title' => 'تکنولوژی‌های روز', 'text' => 'استفاده از فریم‌ورک‌ها و ابزارهای مدرن برای سرعت، امنیت و کیفیت کد.'),
        array('icon' => 'chart', 'title' => 'تحلیل عملکرد و بهینه‌سازی', 'text' => 'پایش مداوم سرعت، پایداری و مصرف منابع برای بهبود مستمر.'),
        array('icon' => 'nodes', 'title' => 'یکپارچه‌سازی با سیستم‌های موجود', 'text' => 'اتصال نرم‌افزار جدید به CRM، ERP، درگاه پرداخت و سایر سرویس‌ها.'),
        array('icon' => 'grid', 'title' => 'تست و تضمین کیفیت', 'text' => 'فرآیند QA ساخت‌یافته شامل تست واحد، یکپارچه و پذیرش کاربر.'),
    );
}

function weblazem_get_default_devproject_faq_items() {
    return array(
        array('question' => 'تفاوت نرم‌افزار اختصاصی با نرم‌افزار آماده چیست؟', 'answer' => 'نرم‌افزار اختصاصی دقیقاً مطابق فرآیندها و نیازهای شما طراحی می‌شود، در حالی که نرم‌افزار آماده اغلب نیاز به انطباق فرآیند شما با محصول دارد.'),
        array('question' => 'مدت زمان توسعه یک پروژه اختصاصی چقدر است؟', 'answer' => 'بسته به پیچیدگی، بین ۶ هفته تا چند ماه. پس از جلسه کشف نیاز، زمان‌بندی و فازبندی دقیق ارائه می‌شود.'),
        array('question' => 'از چه تکنولوژی‌هایی استفاده می‌کنید؟', 'answer' => 'بسته به نیاز پروژه از PHP، WordPress، JavaScript، React، Node.js و پایگاه‌داده‌های رایج استفاده می‌کنیم.'),
        array('question' => 'آیا کد منبع پروژه در اختیار ما قرار می‌گیرد؟', 'answer' => 'بله. طبق قرارداد، مالکیت کد منبع و مستندات فنی به کارفرما منتقل می‌شود.'),
        array('question' => 'پشتیبانی پس از تحویل چگونه است؟', 'answer' => 'پکیج‌های پشتیبانی ماهانه شامل رفع باگ، به‌روزرسانی امنیتی و توسعه تدریجی قابلیت‌ها ارائه می‌شود.'),
        array('question' => 'آیا امکان یکپارچه‌سازی با سیستم‌های فعلی ما وجود دارد؟', 'answer' => 'بله. طراحی API و اتصال به CRM، ERP، درگاه پرداخت و سایر سرویس‌ها بخشی از تخصص ماست.'),
    );
}

function weblazem_get_default_devproject_service_cards() {
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

function weblazem_get_default_devproject_customer_logos() {
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

function weblazem_ensure_devproject_defaults() {
    foreach (weblazem_devproject_defaults() as $key => $value) {
        $option_key = 'weblazem_devproject_' . $key;
        if (get_option($option_key) === false) {
            update_option($option_key, $value);
        }
    }

    $arrays = array(
        'weblazem_devproject_splits'            => 'weblazem_get_default_devproject_splits',
        'weblazem_devproject_process_steps'     => 'weblazem_get_default_devproject_process_steps',
        'weblazem_devproject_advantages_items'  => 'weblazem_get_default_devproject_advantages',
        'weblazem_devproject_faq_items'         => 'weblazem_get_default_devproject_faq_items',
        'weblazem_devproject_service_cards'     => 'weblazem_get_default_devproject_service_cards',
        'weblazem_devproject_portfolio_tabs'    => 'weblazem_get_default_devproject_portfolio_tabs',
        'weblazem_devproject_customers_logos'   => 'weblazem_get_default_devproject_customer_logos',
        'weblazem_devproject_portfolio_items'   => 'weblazem_get_empty_array',
    );

    foreach ($arrays as $option_key => $callback) {
        if (get_option($option_key) === false) {
            $value = is_callable($callback) ? call_user_func($callback) : array();
            update_option($option_key, $value);
        }
    }
}
add_action('init', 'weblazem_ensure_devproject_defaults', 14);
