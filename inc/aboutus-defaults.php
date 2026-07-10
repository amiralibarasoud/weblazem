<?php
/**
 * About Us page — defaults.
 */

function weblazem_aboutus_uri($path) {
    return get_template_directory_uri() . '/assets/images/aboutus/' . ltrim($path, '/');
}

function weblazem_aboutus_page_link($slug, $fallback = '#') {
    $page = get_page_by_path($slug);
    if ($page && $page->post_status === 'publish') {
        $url = get_permalink($page);
        if ($url) {
            return $url;
        }
    }
    return $fallback;
}

function weblazem_aboutus_defaults() {
    $lorem = 'لورم ایپسوم متن ساختگی با تولید سادگی نامفهوم از صنعت چاپ و با استفاده از طراحان گرافیک است. چاپگرها و متون بلکه روزنامه و مجله در ستون و سطرآنچنان که لازم است و برای شرایط فعلی تکنولوژی مورد نیاز و کاربردهای متنوع با هدف بهبود ابزارهای کاربردی می‌باشد.';

    return array(
        'hero_en_title'           => 'About Us',
        'hero_calligraphy_text'   => 'پیشگام در تحول دیجیتال',
        'hero_calligraphy_image'  => '',
        'hero_title'              => 'درباره وب‌سیما',
        'hero_text'               => $lorem,
        'hero_image'              => weblazem_aboutus_uri('hero-kiosk.svg'),
        'journey_calligraphy_text' => 'سفر ما در گذر زمان',
        'journey_calligraphy_image' => '',
        'journey_subtitle'        => 'با هم ساختیم، با هم پیش می‌رویم',
        'journey_intro'           => $lorem,
        'ceo_calligraphy_text'    => 'ما همه چیز را به نفع شما متحول می‌کنیم!',
        'ceo_calligraphy_image'   => '',
        'ceo_accent_text'         => 'به ما محول کنید',
        'ceo_name_calligraphy'    => 'امیر حسین اسماعیلی',
        'ceo_name_en'             => 'AMIR HOSSEIN ESMAEILI',
        'ceo_title_en'            => 'CEO at websima business studio',
        'ceo_text'                => $lorem,
        'ceo_image'               => weblazem_aboutus_uri('ceo-portrait.svg'),
        'team_calligraphy_text'   => 'با هم برای تحقق رویاهای دیجیتال شما',
        'team_calligraphy_image'  => '',
        'team_text'               => 'ما در وب‌سیما یک تیم همدل و متحد هستیم که هدفمان خلق بهترین‌ها برای شماست. فراتر از یک همکاری ساده، خلق ارزشی پایدار و پویا در وب‌سیما به دور از هرگونه تعصب در کنار هم بهترین راهکارها را برای رشد و موفقیت دیجیتال شما خلق می‌کنیم.',
        'team_btn_text'           => 'معرفی تیم',
        'team_btn_url'            => '',
        'team_btn_modal'          => '0',
        'services_logo'           => weblazem_aboutus_uri('logo-websima.svg'),
    );
}

function weblazem_get_default_aboutus_contact_cards() {
    return array(
        array(
            'phone' => '021 740 38 000',
            'label' => 'تماس مستقیم',
        ),
        array(
            'phone' => '021 78358',
            'label' => 'تماس مستقیم',
        ),
    );
}

function weblazem_get_aboutus_contact_cards() {
    $items = get_option('weblazem_aboutus_contact_cards');
    if (!is_array($items) || empty($items)) {
        return weblazem_get_default_aboutus_contact_cards();
    }
    return $items;
}

function weblazem_get_default_aboutus_journey_items() {
    $img = weblazem_aboutus_uri('journey-card.svg');
    $lorem = 'لورم ایپسوم متن ساختگی با تولید سادگی نامفهوم از صنعت چاپ و با استفاده از طراحان گرافیک است.';

    return array(
        array(
            'year'        => '1403',
            'title'       => 'سایت جدید رونمایی شد!',
            'description' => 'پس از ماه‌ها توسعه و طراحی، نسخه جدید وب‌سایت وب‌سیما با ظاهری مدرن و امکانات به‌روز در دسترس شما قرار گرفت.',
            'image'       => $img,
        ),
        array(
            'year'        => '1403',
            'title'       => 'استودیو کسب‌وکار وب‌سیما؛ رسماً یک شرکت خلاق',
            'description' => 'استودیو کسب‌وکار وب‌سیما به‌طور رسمی به‌عنوان یک شرکت خلاق ثبت شد و مسیر جدیدی در ارائه خدمات دیجیتال آغاز کرد.',
            'image'       => $img,
        ),
        array(
            'year'        => '1403',
            'title'       => '۱۳ سال همراهی با شما',
            'description' => 'سیزده سال از آغاز فعالیت وب‌سیما می‌گذرد؛ سال‌هایی پر از تجربه، یادگیری و همراهی با مشتریان عزیز.',
            'image'       => $img,
        ),
        array(
            'year'        => '1403',
            'title'       => 'دورهمی یلدایی وب‌سیما در بلندترین شب سال',
            'description' => 'تیم وب‌سیما در جمعی صمیمی شب یلدا را با هم سپری کرد و لحظاتی به‌یادماندنی خلق شد.',
            'image'       => $img,
        ),
        array(
            'year'        => '1403',
            'title'       => 'کندو ۲۹؛ آخرین روز بهار',
            'description' => 'حضور در رویداد کندو ۲۹ در مرکز همایش‌های بین‌المللی و به‌اشتراک‌گذاری تجربیات تیم با جامعه دیجیتال.',
            'image'       => $img,
        ),
    );
}

function weblazem_get_aboutus_journey_items() {
    $items = get_option('weblazem_aboutus_journey_items');
    if (!is_array($items) || empty($items)) {
        return weblazem_get_default_aboutus_journey_items();
    }
    return $items;
}

function weblazem_get_default_aboutus_team_members() {
    $img = weblazem_aboutus_uri('team-member.svg');
    $members = array();
    $sizes = array('lg', 'sm', 'sm', 'md', 'sm', 'sm', 'md', 'sm');

    foreach ($sizes as $i => $size) {
        $members[] = array(
            'image' => $img,
            'size'  => $size,
            'alt'   => 'عضو تیم ' . ($i + 1),
        );
    }

    return $members;
}

function weblazem_get_aboutus_team_members() {
    $items = get_option('weblazem_aboutus_team_members');
    if (!is_array($items) || empty($items)) {
        return weblazem_get_default_aboutus_team_members();
    }
    return $items;
}

function weblazem_get_default_aboutus_service_cards() {
    $seo_url = function_exists('weblazem_get_seo_page_url') ? weblazem_get_seo_page_url() : '#';
    $web_url = weblazem_aboutus_page_link('tarahi-site-khedmati', '#');

    return array(
        array(
            'title'       => 'سئو و بازاریابی دیجیتال',
            'en_title'    => 'SEO AND DIGITAL MARKETING',
            'description' => 'توسعه و افزایش تعامل',
            'icon'        => weblazem_aboutus_uri('service-seo.svg'),
            'url'         => $seo_url,
        ),
        array(
            'title'       => 'طراحی وب سایت',
            'en_title'    => 'WEB DESIGN',
            'description' => 'اولین قدم برای موفقیت آنلاین',
            'icon'        => weblazem_aboutus_uri('service-webdesign.svg'),
            'url'         => $web_url,
        ),
    );
}

function weblazem_get_aboutus_service_cards() {
    $items = get_option('weblazem_aboutus_service_cards');
    if (!is_array($items) || empty($items)) {
        return weblazem_get_default_aboutus_service_cards();
    }
    return $items;
}
