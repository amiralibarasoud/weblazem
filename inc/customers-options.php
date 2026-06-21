<?php
/**
 * Customers section — options, defaults, admin, save.
 */

function weblazem_get_default_customer_logos() {
    $img_base = get_template_directory_uri() . '/assets/images/customers/';

    return array(
        array('name' => 'مشتری ۱', 'logo' => $img_base . 'logo-1.svg', 'url' => ''),
        array('name' => 'مشتری ۲', 'logo' => $img_base . 'logo-2.svg', 'url' => ''),
        array('name' => 'مشتری ۳', 'logo' => $img_base . 'logo-3.svg', 'url' => ''),
        array('name' => 'مشتری ۴', 'logo' => $img_base . 'logo-4.svg', 'url' => ''),
        array('name' => 'مشتری ۵', 'logo' => $img_base . 'logo-5.svg', 'url' => ''),
        array('name' => 'مشتری ۶', 'logo' => $img_base . 'logo-6.svg', 'url' => ''),
        array('name' => 'مشتری ۷', 'logo' => $img_base . 'logo-7.svg', 'url' => ''),
        array('name' => 'مشتری ۸', 'logo' => $img_base . 'logo-8.svg', 'url' => ''),
    );
}

function weblazem_ensure_customers_defaults() {
    if (get_option('weblazem_customers_title') === false) {
        update_option('weblazem_customers_title', 'مشتریان ما');
    }
    if (get_option('weblazem_customers_logos') === false) {
        update_option('weblazem_customers_logos', weblazem_get_default_customer_logos());
    }
}
add_action('init', 'weblazem_ensure_customers_defaults', 15);

function weblazem_sanitize_customer_logos($input) {
    if (empty($input) || !is_array($input)) {
        return array();
    }

    $sanitized = array();

    foreach ($input as $logo) {
        if (empty($logo['logo'])) {
            continue;
        }

        $sanitized[] = array(
            'name' => isset($logo['name']) ? sanitize_text_field($logo['name']) : '',
            'logo' => esc_url_raw($logo['logo']),
            'url'  => isset($logo['url']) ? esc_url_raw($logo['url']) : '',
        );
    }

    return $sanitized;
}

function weblazem_save_customers_homepage_options() {
    if (isset($_POST['weblazem_customers_title'])) {
        update_option('weblazem_customers_title', sanitize_text_field(wp_unslash($_POST['weblazem_customers_title'])));
    }

    if (isset($_POST['weblazem_customers_logos']) && is_array($_POST['weblazem_customers_logos'])) {
        $logos = array();

        foreach ($_POST['weblazem_customers_logos'] as $index => $logo) {
            $image = isset($logo['logo']) ? esc_url_raw($logo['logo']) : '';
            $file_key = 'weblazem_customer_logo_file_' . (int) $index;

            if (!empty($_FILES[$file_key]['name'])) {
                $image = weblazem_handle_option_image_upload($file_key, $image);
            }

            if (empty($image)) {
                continue;
            }

            $logos[] = array(
                'name' => isset($logo['name']) ? sanitize_text_field($logo['name']) : '',
                'logo' => $image,
                'url'  => isset($logo['url']) ? esc_url_raw($logo['url']) : '',
            );
        }

        update_option('weblazem_customers_logos', $logos);
    }
}

function weblazem_render_customers_homepage_tab() {
    $customers_title = get_option('weblazem_customers_title', 'مشتریان ما');
    $customers_logos = get_option('weblazem_customers_logos', weblazem_get_default_customer_logos());

    if (!is_array($customers_logos)) {
        $customers_logos = weblazem_get_default_customer_logos();
    }
    ?>
    <div class="weblazem-tab-content" id="customers-tab">
        <div class="weblazem-admin-card">
            <div class="weblazem-admin-card-icon"><i class="fas fa-handshake"></i></div>
            <h3>مشتریان — صفحه اصلی</h3>

            <table class="form-table">
                <tr valign="top">
                    <th scope="row">عنوان بخش</th>
                    <td>
                        <input type="text" name="weblazem_customers_title" class="large-text" value="<?php echo esc_attr($customers_title); ?>" />
                    </td>
                </tr>
            </table>

            <h4 style="margin-top:24px;">لوگوی مشتریان</h4>
            <p class="description">لوگوها در کاروسل افقی نمایش داده می‌شوند. پیشنهاد: تصویر مربعی با پس‌زمینه شفاف.</p>

            <div id="customers-logos-container">
                <?php foreach ($customers_logos as $index => $logo) : ?>
                    <div class="weblazem-customer-logo-admin" style="background:#f8f5fc;padding:16px;border-radius:12px;margin-bottom:16px;border:1px solid #e8dff0;">
                        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">
                            <h4 style="margin:0;">لوگو <?php echo (int) $index + 1; ?></h4>
                            <button type="button" class="button customer-logo-remove">حذف</button>
                        </div>
                        <table class="form-table">
                            <tr>
                                <th>نام / alt</th>
                                <td>
                                    <input type="text" name="weblazem_customers_logos[<?php echo esc_attr($index); ?>][name]" class="regular-text" value="<?php echo esc_attr($logo['name']); ?>" />
                                </td>
                            </tr>
                            <tr>
                                <th>لینک (اختیاری)</th>
                                <td>
                                    <input type="url" name="weblazem_customers_logos[<?php echo esc_attr($index); ?>][url]" class="large-text" value="<?php echo esc_url($logo['url']); ?>" placeholder="https://" />
                                </td>
                            </tr>
                            <tr>
                                <th>تصویر لوگو</th>
                                <td>
                                    <?php if (!empty($logo['logo'])) : ?>
                                        <img src="<?php echo esc_url($logo['logo']); ?>" alt="" style="width:72px;height:72px;border-radius:50%;object-fit:contain;background:#4F1E60;padding:10px;margin-bottom:10px;display:block;" />
                                    <?php endif; ?>
                                    <input type="file" name="weblazem_customer_logo_file_<?php echo esc_attr($index); ?>" accept="image/*" />
                                    <input type="hidden" name="weblazem_customers_logos[<?php echo esc_attr($index); ?>][logo]" value="<?php echo esc_attr($logo['logo']); ?>" />
                                </td>
                            </tr>
                        </table>
                    </div>
                <?php endforeach; ?>
            </div>

            <button type="button" class="button button-primary" id="add-customer-logo">افزودن لوگو</button>
        </div>
    </div>

    <script type="text/template" id="customer-logo-template">
        <div class="weblazem-customer-logo-admin" style="background:#f8f5fc;padding:16px;border-radius:12px;margin-bottom:16px;border:1px solid #e8dff0;">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">
                <h4 style="margin:0;">لوگو جدید</h4>
                <button type="button" class="button customer-logo-remove">حذف</button>
            </div>
            <table class="form-table">
                <tr>
                    <th>نام / alt</th>
                    <td>
                        <input type="text" name="weblazem_customers_logos[{{index}}][name]" class="regular-text" value="" />
                    </td>
                </tr>
                <tr>
                    <th>لینک (اختیاری)</th>
                    <td>
                        <input type="url" name="weblazem_customers_logos[{{index}}][url]" class="large-text" value="" placeholder="https://" />
                    </td>
                </tr>
                <tr>
                    <th>تصویر لوگو</th>
                    <td>
                        <input type="file" name="weblazem_customer_logo_file_{{index}}" accept="image/*" />
                        <input type="hidden" name="weblazem_customers_logos[{{index}}][logo]" value="" />
                        <p class="description">برای نمایش در سایت، آپلود تصویر الزامی است.</p>
                    </td>
                </tr>
            </table>
        </div>
    </script>
    <script>
    jQuery(function($) {
        var logoIndex = <?php echo count($customers_logos); ?>;

        $('#add-customer-logo').on('click', function() {
            var tpl = $('#customer-logo-template').html().replace(/\{\{index\}\}/g, logoIndex);
            $('#customers-logos-container').append(tpl);
            logoIndex++;
        });

        $(document).on('click', '.customer-logo-remove', function() {
            $(this).closest('.weblazem-customer-logo-admin').remove();
        });
    });
    </script>
    <?php
}
