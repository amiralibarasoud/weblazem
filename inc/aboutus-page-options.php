<?php
/**
 * About Us page — admin settings.
 */

require_once get_template_directory() . '/inc/aboutus-defaults.php';

function weblazem_register_aboutus_settings() {
    $defaults = weblazem_aboutus_defaults();

    foreach (array_keys($defaults) as $key) {
        $args = array();
        if ($key === 'team_btn_modal' || $key === 'consult_btn_modal') {
            $args['sanitize_callback'] = 'weblazem_sanitize_aboutus_checkbox';
        }
        register_setting('weblazem_aboutus_group', 'weblazem_aboutus_' . $key, $args);
    }

    register_setting('weblazem_aboutus_group', 'weblazem_aboutus_contact_cards', array('sanitize_callback' => 'weblazem_sanitize_aboutus_contact_cards'));
    register_setting('weblazem_aboutus_group', 'weblazem_aboutus_journey_items', array('sanitize_callback' => 'weblazem_sanitize_aboutus_journey_items'));
    register_setting('weblazem_aboutus_group', 'weblazem_aboutus_team_members', array('sanitize_callback' => 'weblazem_sanitize_aboutus_team_members'));
    register_setting('weblazem_aboutus_group', 'weblazem_aboutus_service_cards', array('sanitize_callback' => 'weblazem_sanitize_aboutus_service_cards'));

    foreach (weblazem_get_aboutus_sections_config() as $key => $label) {
        register_setting('weblazem_aboutus_group', 'weblazem_aboutus_section_' . $key . '_enabled');
    }
}
add_action('admin_init', 'weblazem_register_aboutus_settings');

function weblazem_sanitize_aboutus_checkbox($value) {
    return $value === '1' ? '1' : '0';
}

function weblazem_sanitize_aboutus_contact_cards($input) {
    if (!is_array($input)) {
        return weblazem_get_default_aboutus_contact_cards();
    }
    $out = array();
    foreach ($input as $row) {
        if (empty($row['phone'])) {
            continue;
        }
        $out[] = array(
            'phone' => sanitize_text_field($row['phone']),
            'label' => sanitize_text_field($row['label'] ?? 'تماس مستقیم'),
        );
    }
    return !empty($out) ? $out : weblazem_get_default_aboutus_contact_cards();
}

function weblazem_sanitize_aboutus_journey_items($input) {
    if (!is_array($input)) {
        return weblazem_get_default_aboutus_journey_items();
    }
    $out = array();
    foreach ($input as $row) {
        if (empty($row['title'])) {
            continue;
        }
        $out[] = array(
            'year'        => sanitize_text_field($row['year'] ?? ''),
            'title'       => sanitize_text_field($row['title']),
            'description' => sanitize_textarea_field($row['description'] ?? ''),
            'image'       => esc_url_raw($row['image'] ?? ''),
        );
    }
    return !empty($out) ? $out : weblazem_get_default_aboutus_journey_items();
}

function weblazem_sanitize_aboutus_team_members($input) {
    if (!is_array($input)) {
        return weblazem_get_default_aboutus_team_members();
    }
    $out = array();
    $sizes = array('sm', 'md', 'lg');
    foreach ($input as $row) {
        if (empty($row['image'])) {
            continue;
        }
        $size = in_array($row['size'] ?? '', $sizes, true) ? $row['size'] : 'sm';
        $out[] = array(
            'image' => esc_url_raw($row['image']),
            'size'  => $size,
            'alt'   => sanitize_text_field($row['alt'] ?? ''),
        );
    }
    return !empty($out) ? $out : weblazem_get_default_aboutus_team_members();
}

function weblazem_sanitize_aboutus_service_cards($input) {
    if (!is_array($input)) {
        return weblazem_get_default_aboutus_service_cards();
    }
    $out = array();
    foreach ($input as $row) {
        if (empty($row['title'])) {
            continue;
        }
        $out[] = array(
            'title'       => sanitize_text_field($row['title']),
            'en_title'    => sanitize_text_field($row['en_title'] ?? ''),
            'description' => sanitize_text_field($row['description'] ?? ''),
            'shape_image' => esc_url_raw($row['shape_image'] ?? $row['icon'] ?? ''),
            'url'         => esc_url_raw($row['url'] ?? ''),
        );
    }
    return !empty($out) ? $out : weblazem_get_default_aboutus_service_cards();
}

function weblazem_aboutus_handle_section_checkboxes() {
    if (!isset($_POST['option_page']) || $_POST['option_page'] !== 'weblazem_aboutus_group') {
        return;
    }
    foreach (weblazem_get_aboutus_sections_config() as $key => $label) {
        $option_key = 'weblazem_aboutus_section_' . $key . '_enabled';
        if (!isset($_POST[$option_key])) {
            update_option($option_key, '0');
        }
    }
}
add_action('admin_init', 'weblazem_aboutus_handle_section_checkboxes', 20);

function weblazem_aboutus_admin_scripts($hook) {
    if (strpos($hook, 'weblazem-aboutus-options') === false) {
        return;
    }
    wp_enqueue_media();
}
add_action('admin_enqueue_scripts', 'weblazem_aboutus_admin_scripts');

function weblazem_aboutus_opt($key) {
    $defaults = weblazem_aboutus_defaults();
    return get_option('weblazem_aboutus_' . $key, $defaults[$key] ?? '');
}

function weblazem_aboutus_options_display() {
    $page_url       = weblazem_get_aboutus_page_url();
    $contact_cards  = weblazem_get_aboutus_contact_cards();
    $journey_items  = weblazem_get_aboutus_journey_items();
    $team_members   = weblazem_get_aboutus_team_members();
    $service_cards  = weblazem_get_aboutus_service_cards();
    ?>
    <div class="wrap" dir="rtl">
        <div class="weblazem-admin-header">
            <div class="weblazem-admin-header-content">
                <h1>تنظیمات صفحه درباره ما</h1>
                <p>
                    محتوای صفحه داخلی «درباره ما» را مدیریت کنید.
                    <a href="<?php echo esc_url($page_url); ?>" target="_blank" rel="noopener">مشاهده صفحه</a>
                </p>
            </div>
        </div>

        <div class="weblazem-admin-content" style="margin-top:24px;">
            <div class="weblazem-tabs" style="margin-bottom:20px;">
                <?php
                $admin_tabs = array(
                    'layout'   => 'چیدمان سکشن‌ها',
                    'hero'     => 'هیرو',
                    'journey'  => 'تایم‌لاین',
                    'ceo'      => 'مدیرعامل',
                    'team'     => 'تیم',
                    'services' => 'کارت‌های خدمات',
                    'consult'  => 'مشاوره',
                );
                $first = true;
                foreach ($admin_tabs as $id => $label) :
                    ?>
                    <button type="button" class="weblazem-tab<?php echo $first ? ' active' : ''; ?>" data-tab="<?php echo esc_attr($id); ?>"><?php echo esc_html($label); ?></button>
                    <?php $first = false; endforeach; ?>
            </div>

            <form method="post" action="options.php" id="aboutus-options-form">
                <?php settings_fields('weblazem_aboutus_group'); ?>

                <div class="weblazem-tab-content active" data-tab-content="layout">
                    <div class="weblazem-admin-card">
                        <h3>فعال‌سازی سکشن‌ها</h3>
                        <table class="form-table">
                            <?php foreach (weblazem_get_aboutus_sections_config() as $key => $label) :
                                $option_key = 'weblazem_aboutus_section_' . $key . '_enabled';
                                ?>
                                <tr>
                                    <th><?php echo esc_html($label); ?></th>
                                    <td>
                                        <input type="hidden" name="<?php echo esc_attr($option_key); ?>" value="0" />
                                        <label><input type="checkbox" name="<?php echo esc_attr($option_key); ?>" value="1" <?php checked(get_option($option_key, '1'), '1'); ?> /> نمایش در صفحه</label>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </table>
                    </div>
                </div>

                <div class="weblazem-tab-content" data-tab-content="hero">
                    <div class="weblazem-admin-card">
                        <h3>بخش هیرو</h3>
                        <?php weblazem_aboutus_admin_field('hero_en_title', 'عنوان انگلیسی'); ?>
                        <?php weblazem_aboutus_admin_image('hero_calligraphy_image', 'تصویر خوشنویسی (اختیاری)'); ?>
                        <?php weblazem_aboutus_admin_field('hero_calligraphy_text', 'متن خوشنویسی (در صورت نبود تصویر)'); ?>
                        <?php weblazem_aboutus_admin_field('hero_title', 'عنوان'); ?>
                        <?php weblazem_aboutus_admin_textarea('hero_text', 'متن توضیحی'); ?>
                        <?php weblazem_aboutus_admin_image('hero_image', 'تصویر کیوسک / بصری'); ?>
                        <h4>کارت‌های تماس</h4>
                        <div id="aboutus-contact-cards-container">
                            <?php foreach ($contact_cards as $i => $card) : weblazem_aboutus_admin_contact_row($i, $card); endforeach; ?>
                        </div>
                        <button type="button" class="button" id="add-aboutus-contact">افزودن کارت تماس</button>
                    </div>
                </div>

                <div class="weblazem-tab-content" data-tab-content="journey">
                    <div class="weblazem-admin-card">
                        <h3>سفر ما در گذر زمان</h3>
                        <?php weblazem_aboutus_admin_image('journey_calligraphy_image', 'تصویر خوشنویسی'); ?>
                        <?php weblazem_aboutus_admin_field('journey_calligraphy_text', 'متن خوشنویسی'); ?>
                        <?php weblazem_aboutus_admin_field('journey_subtitle', 'زیرعنوان'); ?>
                        <?php weblazem_aboutus_admin_textarea('journey_intro', 'متن مقدمه'); ?>
                        <div id="aboutus-journey-container">
                            <?php foreach ($journey_items as $i => $item) : weblazem_aboutus_admin_journey_row($i, $item); endforeach; ?>
                        </div>
                        <button type="button" class="button" id="add-aboutus-journey">افزودن رویداد</button>
                    </div>
                </div>

                <div class="weblazem-tab-content" data-tab-content="ceo">
                    <div class="weblazem-admin-card">
                        <h3>بخش مدیرعامل</h3>
                        <?php weblazem_aboutus_admin_image('ceo_image', 'تصویر پرتره'); ?>
                        <?php weblazem_aboutus_admin_field('ceo_accent_text', 'متن کوچک (به ما محول کنید)'); ?>
                        <?php weblazem_aboutus_admin_field('ceo_name_calligraphy', 'نام خوشنویسی'); ?>
                        <?php weblazem_aboutus_admin_image('ceo_calligraphy_image', 'تصویر شعار خوشنویسی'); ?>
                        <?php weblazem_aboutus_admin_field('ceo_calligraphy_text', 'متن شعار خوشنویسی'); ?>
                        <?php weblazem_aboutus_admin_field('ceo_name_en', 'نام انگلیسی'); ?>
                        <?php weblazem_aboutus_admin_field('ceo_title_en', 'سمت انگلیسی'); ?>
                        <?php weblazem_aboutus_admin_textarea('ceo_text', 'متن بیوگرافی'); ?>
                    </div>
                </div>

                <div class="weblazem-tab-content" data-tab-content="team">
                    <div class="weblazem-admin-card">
                        <h3>بخش تیم</h3>
                        <?php weblazem_aboutus_admin_image('team_calligraphy_image', 'تصویر خوشنویسی'); ?>
                        <?php weblazem_aboutus_admin_field('team_calligraphy_text', 'متن خوشنویسی'); ?>
                        <?php weblazem_aboutus_admin_textarea('team_text', 'متن'); ?>
                        <?php weblazem_aboutus_admin_field('team_btn_text', 'متن دکمه'); ?>
                        <p>
                            <label><strong>لینک دکمه</strong> (در صورت غیرفعال بودن مودال)<br>
                            <input type="text" name="weblazem_aboutus_team_btn_url" class="large-text" value="<?php echo esc_attr(weblazem_aboutus_opt('team_btn_url')); ?>" /></label>
                        </p>
                        <p>
                            <input type="hidden" name="weblazem_aboutus_team_btn_modal" value="0" />
                            <label><input type="checkbox" name="weblazem_aboutus_team_btn_modal" value="1" <?php checked(weblazem_aboutus_opt('team_btn_modal'), '1'); ?> /> باز کردن مودال مشاوره</label>
                        </p>
                        <h4>اعضای تیم (کلاژ)</h4>
                        <div id="aboutus-team-container">
                            <?php foreach ($team_members as $i => $member) : weblazem_aboutus_admin_team_row($i, $member); endforeach; ?>
                        </div>
                        <button type="button" class="button" id="add-aboutus-team">افزودن عضو</button>
                    </div>
                </div>

                <div class="weblazem-tab-content" data-tab-content="services">
                    <div class="weblazem-admin-card">
                        <h3>کارت‌های خدمات (مشابه صفحات سئو و برنامه‌نویسی)</h3>
                        <div id="aboutus-services-container">
                            <?php foreach ($service_cards as $i => $card) : weblazem_aboutus_admin_service_row($i, $card); endforeach; ?>
                        </div>
                        <button type="button" class="button" id="add-aboutus-service">افزودن کارت</button>
                    </div>
                </div>

                <div class="weblazem-tab-content" data-tab-content="consult">
                    <div class="weblazem-admin-card">
                        <h3>بخش مشاوره و درخواست اجرای پروژه</h3>
                        <?php weblazem_aboutus_admin_field('consult_title', 'عنوان'); ?>
                        <?php weblazem_aboutus_admin_textarea('consult_text', 'متن'); ?>
                        <?php weblazem_aboutus_admin_field('consult_btn_text', 'متن دکمه'); ?>
                        <p>
                            <label><strong>لینک دکمه</strong> (در صورت غیرفعال بودن مودال)<br>
                            <input type="text" name="weblazem_aboutus_consult_btn_url" class="large-text" value="<?php echo esc_attr(weblazem_aboutus_opt('consult_btn_url')); ?>" /></label>
                        </p>
                        <p>
                            <input type="hidden" name="weblazem_aboutus_consult_btn_modal" value="0" />
                            <label><input type="checkbox" name="weblazem_aboutus_consult_btn_modal" value="1" <?php checked(weblazem_aboutus_opt('consult_btn_modal'), '1'); ?> /> باز کردن مودال مشاوره</label>
                        </p>
                    </div>
                </div>

                <?php submit_button('ذخیره تنظیمات'); ?>
            </form>
        </div>
    </div>

    <?php weblazem_aboutus_admin_footer_scripts($contact_cards, $journey_items, $team_members, $service_cards); ?>
    <?php
}

function weblazem_aboutus_admin_field($key, $label) {
    $val = weblazem_aboutus_opt($key);
    echo '<p><label><strong>' . esc_html($label) . '</strong><br>';
    echo '<input type="text" name="weblazem_aboutus_' . esc_attr($key) . '" class="large-text" value="' . esc_attr($val) . '" /></label></p>';
}

function weblazem_aboutus_admin_textarea($key, $label) {
    $val = weblazem_aboutus_opt($key);
    echo '<p><label><strong>' . esc_html($label) . '</strong><br>';
    echo '<textarea name="weblazem_aboutus_' . esc_attr($key) . '" class="large-text" rows="4">' . esc_textarea($val) . '</textarea></label></p>';
}

function weblazem_aboutus_admin_image($key, $label) {
    $val = weblazem_aboutus_opt($key);
    $id  = 'aboutus_img_' . $key;
    echo '<p><strong>' . esc_html($label) . '</strong><br>';
    echo '<input type="hidden" id="' . esc_attr($id) . '" name="weblazem_aboutus_' . esc_attr($key) . '" value="' . esc_attr($val) . '" />';
    echo '<div class="aboutus-img-preview" data-for="' . esc_attr($id) . '" style="margin:8px 0;">';
    if ($val) {
        echo '<img src="' . esc_url($val) . '" style="max-width:200px;border-radius:8px;" alt="" />';
    }
    echo '</div>';
    echo '<button type="button" class="button aboutus-upload-img" data-target="' . esc_attr($id) . '">انتخاب تصویر</button> ';
    echo '<button type="button" class="button aboutus-remove-img" data-target="' . esc_attr($id) . '">حذف</button></p>';
}

function weblazem_aboutus_admin_contact_row($i, $item) {
    ?>
    <div class="aboutus-repeater-block" style="display:flex;gap:8px;margin-bottom:8px;align-items:center;flex-wrap:wrap;">
        <input type="text" name="weblazem_aboutus_contact_cards[<?php echo (int) $i; ?>][phone]" value="<?php echo esc_attr($item['phone'] ?? ''); ?>" placeholder="شماره تماس" class="regular-text" />
        <input type="text" name="weblazem_aboutus_contact_cards[<?php echo (int) $i; ?>][label]" value="<?php echo esc_attr($item['label'] ?? 'تماس مستقیم'); ?>" placeholder="برچسب" class="regular-text" />
        <button type="button" class="button aboutus-remove-row">حذف</button>
    </div>
    <?php
}

function weblazem_aboutus_admin_journey_row($i, $item) {
    $img_id = 'aboutus_journey_img_' . $i;
    $img    = $item['image'] ?? '';
    ?>
    <div class="aboutus-repeater-block weblazem-admin-card" style="margin-bottom:12px;padding:12px;">
        <p><label>سال<br><input type="text" name="weblazem_aboutus_journey_items[<?php echo (int) $i; ?>][year]" value="<?php echo esc_attr($item['year'] ?? ''); ?>" class="small-text" /></label></p>
        <p><label>عنوان<br><input type="text" name="weblazem_aboutus_journey_items[<?php echo (int) $i; ?>][title]" value="<?php echo esc_attr($item['title'] ?? ''); ?>" class="large-text" /></label></p>
        <p><label>توضیح<br><textarea name="weblazem_aboutus_journey_items[<?php echo (int) $i; ?>][description]" class="large-text" rows="2"><?php echo esc_textarea($item['description'] ?? ''); ?></textarea></label></p>
        <input type="hidden" id="<?php echo esc_attr($img_id); ?>" name="weblazem_aboutus_journey_items[<?php echo (int) $i; ?>][image]" value="<?php echo esc_attr($img); ?>" />
        <div class="aboutus-img-preview" data-for="<?php echo esc_attr($img_id); ?>"><?php if ($img) : ?><img src="<?php echo esc_url($img); ?>" style="max-width:120px;border-radius:8px;" alt="" /><?php endif; ?></div>
        <button type="button" class="button aboutus-upload-img" data-target="<?php echo esc_attr($img_id); ?>">تصویر</button>
        <button type="button" class="button aboutus-remove-row">حذف رویداد</button>
    </div>
    <?php
}

function weblazem_aboutus_admin_team_row($i, $item) {
    $img_id = 'aboutus_team_img_' . $i;
    $img    = $item['image'] ?? '';
    $size   = $item['size'] ?? 'sm';
    ?>
    <div class="aboutus-repeater-block weblazem-admin-card" style="margin-bottom:12px;padding:12px;display:flex;gap:12px;align-items:center;flex-wrap:wrap;">
        <input type="hidden" id="<?php echo esc_attr($img_id); ?>" name="weblazem_aboutus_team_members[<?php echo (int) $i; ?>][image]" value="<?php echo esc_attr($img); ?>" />
        <div class="aboutus-img-preview" data-for="<?php echo esc_attr($img_id); ?>"><?php if ($img) : ?><img src="<?php echo esc_url($img); ?>" style="max-width:80px;border-radius:8px;" alt="" /><?php endif; ?></div>
        <button type="button" class="button aboutus-upload-img" data-target="<?php echo esc_attr($img_id); ?>">تصویر</button>
        <select name="weblazem_aboutus_team_members[<?php echo (int) $i; ?>][size]">
            <option value="sm" <?php selected($size, 'sm'); ?>>کوچک</option>
            <option value="md" <?php selected($size, 'md'); ?>>متوسط</option>
            <option value="lg" <?php selected($size, 'lg'); ?>>بزرگ</option>
        </select>
        <input type="text" name="weblazem_aboutus_team_members[<?php echo (int) $i; ?>][alt]" value="<?php echo esc_attr($item['alt'] ?? ''); ?>" placeholder="alt" class="regular-text" />
        <button type="button" class="button aboutus-remove-row">حذف</button>
    </div>
    <?php
}

function weblazem_aboutus_admin_service_row($i, $item) {
    $icon_id = 'aboutus_service_shape_' . $i;
    $shape   = !empty($item['shape_image']) ? $item['shape_image'] : ($item['icon'] ?? '');
    ?>
    <div class="aboutus-repeater-block weblazem-admin-card" style="margin-bottom:12px;padding:12px;">
        <p><label>عنوان فارسی<br><input type="text" name="weblazem_aboutus_service_cards[<?php echo (int) $i; ?>][title]" value="<?php echo esc_attr($item['title'] ?? ''); ?>" class="large-text" /></label></p>
        <p><label>عنوان انگلیسی<br><input type="text" name="weblazem_aboutus_service_cards[<?php echo (int) $i; ?>][en_title]" value="<?php echo esc_attr($item['en_title'] ?? ''); ?>" class="large-text" /></label></p>
        <p><label>توضیح کوتاه<br><input type="text" name="weblazem_aboutus_service_cards[<?php echo (int) $i; ?>][description]" value="<?php echo esc_attr($item['description'] ?? ''); ?>" class="large-text" /></label></p>
        <p><label>لینک<br><input type="text" name="weblazem_aboutus_service_cards[<?php echo (int) $i; ?>][url]" value="<?php echo esc_attr($item['url'] ?? ''); ?>" class="large-text" /></label></p>
        <input type="hidden" id="<?php echo esc_attr($icon_id); ?>" name="weblazem_aboutus_service_cards[<?php echo (int) $i; ?>][shape_image]" value="<?php echo esc_attr($shape); ?>" />
        <div class="aboutus-img-preview" data-for="<?php echo esc_attr($icon_id); ?>"><?php if ($shape) : ?><img src="<?php echo esc_url($shape); ?>" style="max-width:80px;" alt="" /><?php endif; ?></div>
        <button type="button" class="button aboutus-upload-img" data-target="<?php echo esc_attr($icon_id); ?>">آیکون شکل (اختیاری)</button>
        <button type="button" class="button aboutus-remove-row">حذف کارت</button>
    </div>
    <?php
}

function weblazem_aboutus_admin_footer_scripts($contact_cards, $journey_items, $team_members, $service_cards) {
    $contact_idx  = count($contact_cards);
    $journey_idx  = count($journey_items);
    $team_idx     = count($team_members);
    $service_idx  = count($service_cards);
    ?>
    <script>
    (function($) {
        function bindTabs() {
            $('.weblazem-tab').on('click', function() {
                var tab = $(this).data('tab');
                $('.weblazem-tab').removeClass('active');
                $(this).addClass('active');
                $('.weblazem-tab-content').removeClass('active');
                $('[data-tab-content="' + tab + '"]').addClass('active');
            });
        }

        function bindMedia() {
            var frame;
            $(document).on('click', '.aboutus-upload-img', function(e) {
                e.preventDefault();
                var target = $(this).data('target');
                if (frame) { frame.close(); }
                frame = wp.media({ title: 'انتخاب تصویر', button: { text: 'استفاده' }, multiple: false });
                frame.on('select', function() {
                    var url = frame.state().get('selection').first().toJSON().url;
                    $('#' + target).val(url);
                    $('[data-for="' + target + '"]').html('<img src="' + url + '" style="max-width:200px;border-radius:8px;" alt="" />');
                });
                frame.open();
            });
            $(document).on('click', '.aboutus-remove-img', function(e) {
                e.preventDefault();
                var target = $(this).data('target');
                $('#' + target).val('');
                $('[data-for="' + target + '"]').empty();
            });
        }

        $(document).on('click', '.aboutus-remove-row', function() {
            $(this).closest('.aboutus-repeater-block').remove();
        });

        var contactIdx = <?php echo (int) $contact_idx; ?>;
        $('#add-aboutus-contact').on('click', function() {
            var html = '<div class="aboutus-repeater-block" style="display:flex;gap:8px;margin-bottom:8px;align-items:center;flex-wrap:wrap;">' +
                '<input type="text" name="weblazem_aboutus_contact_cards[' + contactIdx + '][phone]" placeholder="شماره تماس" class="regular-text" />' +
                '<input type="text" name="weblazem_aboutus_contact_cards[' + contactIdx + '][label]" value="تماس مستقیم" placeholder="برچسب" class="regular-text" />' +
                '<button type="button" class="button aboutus-remove-row">حذف</button></div>';
            $('#aboutus-contact-cards-container').append(html);
            contactIdx++;
        });

        var journeyIdx = <?php echo (int) $journey_idx; ?>;
        $('#add-aboutus-journey').on('click', function() {
            var id = 'aboutus_journey_img_' + journeyIdx;
            var html = '<div class="aboutus-repeater-block weblazem-admin-card" style="margin-bottom:12px;padding:12px;">' +
                '<p><label>سال<br><input type="text" name="weblazem_aboutus_journey_items[' + journeyIdx + '][year]" class="small-text" /></label></p>' +
                '<p><label>عنوان<br><input type="text" name="weblazem_aboutus_journey_items[' + journeyIdx + '][title]" class="large-text" /></label></p>' +
                '<p><label>توضیح<br><textarea name="weblazem_aboutus_journey_items[' + journeyIdx + '][description]" class="large-text" rows="2"></textarea></label></p>' +
                '<input type="hidden" id="' + id + '" name="weblazem_aboutus_journey_items[' + journeyIdx + '][image]" value="" />' +
                '<div class="aboutus-img-preview" data-for="' + id + '"></div>' +
                '<button type="button" class="button aboutus-upload-img" data-target="' + id + '">تصویر</button> ' +
                '<button type="button" class="button aboutus-remove-row">حذف رویداد</button></div>';
            $('#aboutus-journey-container').append(html);
            journeyIdx++;
        });

        var teamIdx = <?php echo (int) $team_idx; ?>;
        $('#add-aboutus-team').on('click', function() {
            var id = 'aboutus_team_img_' + teamIdx;
            var html = '<div class="aboutus-repeater-block weblazem-admin-card" style="margin-bottom:12px;padding:12px;display:flex;gap:12px;align-items:center;flex-wrap:wrap;">' +
                '<input type="hidden" id="' + id + '" name="weblazem_aboutus_team_members[' + teamIdx + '][image]" value="" />' +
                '<div class="aboutus-img-preview" data-for="' + id + '"></div>' +
                '<button type="button" class="button aboutus-upload-img" data-target="' + id + '">تصویر</button>' +
                '<select name="weblazem_aboutus_team_members[' + teamIdx + '][size]"><option value="sm">کوچک</option><option value="md">متوسط</option><option value="lg">بزرگ</option></select>' +
                '<input type="text" name="weblazem_aboutus_team_members[' + teamIdx + '][alt]" placeholder="alt" class="regular-text" />' +
                '<button type="button" class="button aboutus-remove-row">حذف</button></div>';
            $('#aboutus-team-container').append(html);
            teamIdx++;
        });

        var serviceIdx = <?php echo (int) $service_idx; ?>;
        $('#add-aboutus-service').on('click', function() {
            var id = 'aboutus_service_shape_' + serviceIdx;
            var html = '<div class="aboutus-repeater-block weblazem-admin-card" style="margin-bottom:12px;padding:12px;">' +
                '<p><label>عنوان فارسی<br><input type="text" name="weblazem_aboutus_service_cards[' + serviceIdx + '][title]" class="large-text" /></label></p>' +
                '<p><label>عنوان انگلیسی<br><input type="text" name="weblazem_aboutus_service_cards[' + serviceIdx + '][en_title]" class="large-text" /></label></p>' +
                '<p><label>توضیح کوتاه<br><input type="text" name="weblazem_aboutus_service_cards[' + serviceIdx + '][description]" class="large-text" /></label></p>' +
                '<p><label>لینک<br><input type="text" name="weblazem_aboutus_service_cards[' + serviceIdx + '][url]" class="large-text" /></label></p>' +
                '<input type="hidden" id="' + id + '" name="weblazem_aboutus_service_cards[' + serviceIdx + '][shape_image]" value="" />' +
                '<div class="aboutus-img-preview" data-for="' + id + '"></div>' +
                '<button type="button" class="button aboutus-upload-img" data-target="' + id + '">آیکون شکل (اختیاری)</button> ' +
                '<button type="button" class="button aboutus-remove-row">حذف کارت</button></div>';
            $('#aboutus-services-container').append(html);
            serviceIdx++;
        });

        bindTabs();
        bindMedia();
    })(jQuery);
    </script>
    <?php
}
