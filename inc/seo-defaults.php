<?php
/**
 * SEO page — defaults.
 */

function weblazem_seo_defaults() {
    $hero_text = 'بازاریابی دیجیتال یکی از ابزارهای قدرتمند برای رسیدن به مخاطبان هدف و افزایش فروش است. در وب‌لازم، با بهره‌گیری از جدیدترین روش‌ها و تکنیک‌های دیجیتال مارکتینگ، به شما کمک می‌کنیم تا برند خود را به مخاطبان مناسب معرفی کرده و روابط معناداری با آنها برقرار کنید.';

    return array(
        'hero_en_title'              => 'SEO & Digital Marketing',
        'hero_calligraphy_text'      => '<span class="highlight">توسعه</span> و <span class="accent">افزایش تعامل</span>',
        'hero_title'                 => 'سئو و بازاریابی دیجیتال',
        'hero_text'                  => $hero_text,
        'hero_image'                 => '',
        'clients_calligraphy_text'   => 'اعتمادی که به آن <span class="highlight">افتخار</span> می‌کنیم',
        'clients_subtitle'           => 'مشتریان وب‌لازم، همراهان موفقیت',
        'clients_description'        => 'با افتخار، وب‌لازم همراه بسیاری از برندهای معتبر و پیشرو در صنعت دیجیتال بوده است. همکاری با این شرکت‌ها، گواهی بر کیفیت خدمات و تخصص ما در ارائه راهکارهای نوآورانه و اثربخش است.',
        'process_calligraphy_text'   => 'یک سفر <span class="highlight">حرفه‌ای</span> در مسیر موفقیت',
        'process_subtitle'           => 'فرآیند اثربخش بازاریابی دیجیتال ما؛ دستیابی به نتایج در هر گام',
        'process_description'        => 'در وب‌لازم، پروژه‌های بازاریابی دیجیتال با رویکردی هدفمند و مرحله‌به‌مرحله پیش می‌روند تا در هر گام، نتیجه ملموس و قابل اندازه‌گیری داشته باشید.',
        'process_start_note'         => 'از اینجا شروع کنیم',
        'process_journey_caption'    => 'مسیر رشد آنلاین، افزایش ترافیک و تبدیل بازدید به فروش',
        'process_csat_number'        => '98%',
        'process_csat_label'         => 'شاخص رضایت مشتریان',
        'process_csat_sub'           => 'CSAT — Customer Satisfaction Score',
        'process_btn1_text'          => 'معرفی تیم',
        'process_btn1_url'           => '#',
        'process_btn2_text'          => 'درباره ما',
        'process_btn2_url'           => '#',
        'advantages_title'           => 'مسیر موفقیت، با یک انتخاب درست آغاز می‌شود!',
        'advantages_subtitle'        => 'استراتژی درست سئو و بازاریابی دیجیتال، پایه رشد پایدار کسب‌وکار شماست.',
        'tariffs_title'              => 'پلن های سئو با خدمات وب‌لازم',
        'tariffs_price_label'        => 'قیمت',
        'faq_subtitle'               => 'پرسش‌های متداول',
        'faq_intro'                  => 'پاسخ سوالات رایج درباره سئو و بازاریابی دیجیتال را در این بخش بیابید تا با اطمینان بیشتری تصمیم بگیرید.',
        'faq_phone'                  => '021 78358',
        'faq_consult_btn_text'       => 'ثبت درخواست مشاوره',
        'faq_footer_text'            => 'ما ترکیبی از خلاقیت و تکنولوژی را برای توسعه کسب‌وکارهای دیجیتال خلق کرده‌ایم',
    );
}

function weblazem_get_default_seo_pricing_plans() {
    $feature = 'لورم ایپسوم متن ساختگی';
    $features = array_fill(0, 5, $feature);
    $plans = array();

    for ($i = 0; $i < 4; $i++) {
        $plans[] = array(
            'title'        => 'پلن طلایی (ماهانه)',
            'price'        => '۲.۰۰۰.۰۰۰ تومان',
            'features'     => $features,
            'button_text'  => 'مشاوره رایگان',
            'button_modal' => '1',
            'button_url'   => '',
        );
    }

    return $plans;
}

function weblazem_get_seo_pricing_plans() {
    $plans = get_option('weblazem_seo_pricing_plans');
    if (!is_array($plans) || empty($plans)) {
        return weblazem_get_default_seo_pricing_plans();
    }
    return $plans;
}

function weblazem_get_default_seo_splits() {
    return array(
        array(
            'title'        => 'استراتژی سئو',
            'text'         => 'استراتژی سئو موفق، پایه حضور قوی در نتایج جستجو است. با تحلیل بازار و رقبا، مسیر بهبود رتبه و جذب ترافیک هدفمند را ترسیم می‌کنیم.',
            'button_text'  => 'استراتژی سئو',
            'button_url'   => '#',
            'button_modal' => '0',
            'image'        => '',
            'caption'      => 'طراحی مسیر رشد آنلاین',
            'layout'       => 'right',
        ),
        array(
            'title'        => 'مشاوره سئو',
            'text'         => 'اگر به دنبال راهنمایی تخصصی برای بهبود سئو و افزایش رتبه سایت خود هستید، تیم مشاوره ما بهترین انتخاب است. الگوریتم‌ها و موتورهای جستجو را تحلیل کرده و راهکار عملی ارائه می‌دهیم.',
            'button_text'  => 'مشاهده بیشتر',
            'button_url'   => '#',
            'button_modal' => '0',
            'image'        => '',
            'caption'      => 'راهنمایی تخصصی برای موفقیت آنلاین',
            'layout'       => 'left',
        ),
        array(
            'title'        => 'سئو ماهانه',
            'text'         => 'سئو ماهانه، سرمایه‌گذاری مستمر برای رشد پایدار است. با به‌روزرسانی منظم محتوا، لینک‌سازی و بهینه‌سازی فنی، رتبه و ترافیک سایت شما را ارتقا می‌دهیم.',
            'button_text'  => 'مشاهده بیشتر',
            'button_url'   => '#',
            'button_modal' => '0',
            'image'        => '',
            'caption'      => 'ارتقای دائمی رتبه و ترافیک سایت',
            'layout'       => 'right',
        ),
    );
}

function weblazem_get_default_seo_process_steps() {
    return array(
        array('title' => 'جلسه مشاوره کسب‌وکار'),
        array('title' => 'نیازسنجی و ارائه راهکار'),
        array('title' => 'تدوین پروپوزال'),
        array('title' => 'تایید پروپوزال و عقد قرارداد'),
        array('title' => 'ارجاع به تیم اجرایی'),
    );
}

function weblazem_get_default_seo_advantages() {
    $lorem = 'لورم ایپسوم متن ساختگی با تولید سادگی نامفهوم از صنعت چاپ و با استفاده از طراحان گرافیک است.';
    return array(
        array('icon' => 'clipboard', 'title' => 'تصمیم‌گیری استراتژیک با استفاده از داده‌ها', 'text' => $lorem),
        array('icon' => 'chart', 'title' => 'تحلیل منظم و بهبود مداوم', 'text' => $lorem),
        array('icon' => 'star', 'title' => 'کلمات کلیدی طلایی', 'text' => $lorem),
        array('icon' => 'target', 'title' => 'استراتژی هدفمند و شخصی‌سازی شده', 'text' => $lorem),
        array('icon' => 'shark', 'title' => 'تحلیل دقیق رقبا و شناسایی فرصت‌ها', 'text' => $lorem),
        array('icon' => 'coffee', 'title' => 'محتوای جذاب', 'text' => $lorem),
        array('icon' => 'grid', 'title' => 'بهینه‌سازی تجربه کاربری (UX)', 'text' => $lorem),
        array('icon' => 'graph', 'title' => 'گزارش‌های هوشمند و شفاف', 'text' => $lorem),
    );
}

function weblazem_get_default_seo_faq_items() {
    $items = array(
        array('question' => 'سئو چیست و چرا اهمیت دارد؟', 'answer' => 'سئو به مجموعه اقداماتی گفته می‌شود که رتبه سایت شما را در نتایج موتورهای جستجو بهبود می‌دهد و ترافیک هدفمند جذب می‌کند.'),
        array('question' => 'چقدر طول می‌کشد تا نتایج سئو را ببینیم؟', 'answer' => 'بسته به رقابت کلمات کلیدی و وضعیت فعلی سایت، معمولاً بین ۳ تا ۶ ماه اول نتایج اولیه و بین ۶ تا ۱۲ ماه رشد پایدار دیده می‌شود.'),
        array('question' => 'چرا باید از خدمات بازاریابی دیجیتال استفاده کنم؟', 'answer' => 'بازاریابی دیجیتال به شما امکان می‌دهد مخاطبان دقیق را هدف بگیرید، هزینه تبلیغات را بهینه کنید و نتایج را اندازه‌گیری کنید.'),
        array('question' => 'بازاریابی دیجیتال چطور می‌تواند به رشد کسب‌وکار من کمک کند؟', 'answer' => 'با ترکیب سئو، تبلیغات، شبکه‌های اجتماعی و ایمیل مارکتینگ، آگاهی از برند و فروش شما افزایش می‌یابد.'),
        array('question' => 'آیا بازاریابی دیجیتال فقط برای کسب‌وکارهای بزرگ مناسب است؟', 'answer' => 'خیر. کسب‌وکارهای کوچک و متوسط نیز با استراتژی درست می‌توانند از بازاریابی دیجیتال بهره ببرند.'),
        array('question' => 'چگونه می‌توانم موفقیت در بازاریابی دیجیتال را اندازه‌گیری کنم؟', 'answer' => 'با شاخص‌هایی مانند ترافیک ارگانیک، نرخ تبدیل، هزینه جذب مشتری و بازگشت سرمایه تبلیغات (ROAS).'),
    );
    return $items;
}

function weblazem_get_default_seo_service_cards() {
    $webdesign_url = function_exists('weblazem_get_webdesign_page_url') ? weblazem_get_webdesign_page_url() : '#';
    return array(
        array(
            'title'       => 'استراتژی دیجیتال',
            'en_title'    => 'DIGITAL STRATEGY',
            'description' => 'نقشه راهی برای موفقیت آنلاین',
            'url'         => '#',
            'shape_image' => '',
        ),
        array(
            'title'       => 'طراحی وب‌سایت',
            'en_title'    => 'WEB DESIGN',
            'description' => 'اولین قدم برای موفقیت آنلاین',
            'url'         => $webdesign_url,
            'shape_image' => '',
        ),
    );
}

function weblazem_get_default_seo_client_logos() {
    $logos = array();
    for ($i = 1; $i <= 18; $i++) {
        $logos[] = array(
            'name' => 'مشتری ' . $i,
            'logo' => get_template_directory_uri() . '/assets/images/customers/logo-' . (($i - 1) % 8 + 1) . '.svg',
            'url'  => '',
        );
    }
    return $logos;
}

function weblazem_ensure_seo_defaults() {
    foreach (weblazem_seo_defaults() as $key => $value) {
        $option_key = 'weblazem_seo_' . $key;
        if (get_option($option_key) === false) {
            update_option($option_key, $value);
        }
    }

    $arrays = array(
        'weblazem_seo_splits'           => 'weblazem_get_default_seo_splits',
        'weblazem_seo_process_steps'    => 'weblazem_get_default_seo_process_steps',
        'weblazem_seo_advantages_items' => 'weblazem_get_default_seo_advantages',
        'weblazem_seo_pricing_plans'    => 'weblazem_get_default_seo_pricing_plans',
        'weblazem_seo_faq_items'        => 'weblazem_get_default_seo_faq_items',
        'weblazem_seo_service_cards'    => 'weblazem_get_default_seo_service_cards',
        'weblazem_seo_clients_logos'    => 'weblazem_get_default_seo_client_logos',
    );

    foreach ($arrays as $option_key => $callback) {
        if (get_option($option_key) === false) {
            update_option($option_key, call_user_func($callback));
        }
    }
}
add_action('init', 'weblazem_ensure_seo_defaults', 14);
