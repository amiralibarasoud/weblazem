<?php
/**
 * Homepage section visibility toggles.
 */

function weblazem_get_home_sections_config() {
    return array(
        'hero'         => 'بخش هیرو (بنر اصلی)',
        'services'     => 'بخش خدمات',
        'portfolio'    => 'بخش نمونه‌کارها',
        'outsourcing'  => 'برای داشتن وبسایتی منحصر به فرد آماده‌ای؟',
        'about'        => 'معرفی ما و مجموعه‌ی وب‌لازم',
        'team'         => 'تیم لید',
        'customers'    => 'مشتریان',
        'testimonials' => 'نظرات مشتریان',
        'consultation' => 'بخش مشاوره',
        'faq'          => 'سوالات متداول',
        'ticketing'    => 'ثبت تیکت و پیگیری تسک',
        'growth_tools' => 'ابزارهای رشد (قیمت، کیس‌استادی، رزرو، وضعیت، شروع پروژه)',
    );
}

function weblazem_is_home_section_enabled($section) {
    $sections = weblazem_get_home_sections_config();

    if (!isset($sections[$section])) {
        return true;
    }

    return get_option('weblazem_home_section_' . $section . '_enabled', '1') === '1';
}

function weblazem_ensure_home_section_defaults() {
    foreach (weblazem_get_home_sections_config() as $key => $label) {
        $option_key = 'weblazem_home_section_' . $key . '_enabled';
        if (get_option($option_key) === false) {
            update_option($option_key, '1');
        }
    }

    if (get_option('weblazem_outsourcing_button_modal') === false) {
        update_option('weblazem_outsourcing_button_modal', '1');
    }

    if (get_option('weblazem_about_button_modal') === false) {
        update_option('weblazem_about_button_modal', '1');
    }
}
add_action('init', 'weblazem_ensure_home_section_defaults', 12);

function weblazem_save_home_section_toggles() {
    foreach (weblazem_get_home_sections_config() as $key => $label) {
        $option_key = 'weblazem_home_section_' . $key . '_enabled';
        $value      = (isset($_POST[$option_key]) && $_POST[$option_key] === '1') ? '1' : '0';
        update_option($option_key, $value);
    }

    update_option(
        'weblazem_outsourcing_button_modal',
        (isset($_POST['weblazem_outsourcing_button_modal']) && $_POST['weblazem_outsourcing_button_modal'] === '1') ? '1' : '0'
    );

    update_option(
        'weblazem_about_button_modal',
        (isset($_POST['weblazem_about_button_modal']) && $_POST['weblazem_about_button_modal'] === '1') ? '1' : '0'
    );
}

function weblazem_render_home_sections_tab() {
    $sections = weblazem_get_home_sections_config();
    ?>
    <div class="weblazem-tab-content active" id="sections-tab">
        <div class="weblazem-admin-card">
            <div class="weblazem-admin-card-icon"><i class="fas fa-toggle-on"></i></div>
            <h3>فعال‌سازی سکشن‌های صفحه اصلی</h3>
            <p class="description">هر سکشن را می‌توانید بدون حذف محتوا از نمایش کاربر پنهان کنید.</p>

            <table class="form-table weblazem-section-toggles">
                <?php foreach ($sections as $key => $label) :
                    $option_key = 'weblazem_home_section_' . $key . '_enabled';
                    ?>
                    <tr valign="top">
                        <th scope="row"><?php echo esc_html($label); ?></th>
                        <td>
                            <input type="hidden" name="<?php echo esc_attr($option_key); ?>" value="0" />
                            <label>
                                <input type="checkbox" name="<?php echo esc_attr($option_key); ?>" value="1" <?php checked(get_option($option_key, '1'), '1'); ?> />
                                نمایش در صفحه اصلی
                            </label>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>
    <?php
}
