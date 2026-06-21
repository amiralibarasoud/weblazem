<?php
/**
 * FAQ section — options, defaults, admin, save.
 */

function weblazem_get_default_faq_items() {
    $sample_question = 'چطور می‌توانم ثبت سفارش کنم؟';
    $sample_answer   = 'لورم ایپسوم متن ساختگی با تولید سادگی نامفهوم از صنعت چاپ و با استفاده از طراحان گرافیک است. چاپگرها و متون بلکه روزنامه و مجله در ستون و سطرآنچنان که لازم است.';

    $items = array();

    for ($i = 1; $i <= 12; $i++) {
        $items[] = array(
            'question' => $sample_question,
            'answer'   => $sample_answer,
        );
    }

    return $items;
}

function weblazem_ensure_faq_defaults() {
    if (get_option('weblazem_faq_title') === false) {
        update_option('weblazem_faq_title', 'سوالات متداول');
    }
    if (get_option('weblazem_faq_subtitle') === false) {
        update_option(
            'weblazem_faq_subtitle',
            'در صورتی که پاسخ سوال خود را در قسمت سوالات متداول پیدا نکردید، می‌توانید با پشتیبانی تماس حاصل فرمایید وب‌لازم در هر شرایطی آماده‌ی پاسخگویی به شماست'
        );
    }
    if (get_option('weblazem_faq_items') === false) {
        update_option('weblazem_faq_items', weblazem_get_default_faq_items());
    }
}
add_action('init', 'weblazem_ensure_faq_defaults', 15);

function weblazem_sanitize_faq_items($input) {
    if (empty($input) || !is_array($input)) {
        return array();
    }

    $sanitized = array();

    foreach ($input as $item) {
        if (empty($item['question'])) {
            continue;
        }

        $sanitized[] = array(
            'question' => sanitize_text_field($item['question']),
            'answer'   => isset($item['answer']) ? wp_kses_post($item['answer']) : '',
        );
    }

    return $sanitized;
}

function weblazem_save_faq_homepage_options() {
    if (isset($_POST['weblazem_faq_title'])) {
        update_option('weblazem_faq_title', sanitize_text_field(wp_unslash($_POST['weblazem_faq_title'])));
    }

    if (isset($_POST['weblazem_faq_subtitle'])) {
        update_option('weblazem_faq_subtitle', wp_kses_post(wp_unslash($_POST['weblazem_faq_subtitle'])));
    }

    if (isset($_POST['weblazem_faq_items']) && is_array($_POST['weblazem_faq_items'])) {
        $items = array();

        foreach ($_POST['weblazem_faq_items'] as $item) {
            if (empty($item['question'])) {
                continue;
            }

            $items[] = array(
                'question' => sanitize_text_field($item['question']),
                'answer'   => isset($item['answer']) ? wp_kses_post($item['answer']) : '',
            );
        }

        update_option('weblazem_faq_items', $items);
    }
}

function weblazem_render_faq_homepage_tab() {
    $faq_title    = get_option('weblazem_faq_title', 'سوالات متداول');
    $faq_subtitle = get_option('weblazem_faq_subtitle', '');
    $faq_items    = get_option('weblazem_faq_items', weblazem_get_default_faq_items());

    if (!is_array($faq_items)) {
        $faq_items = weblazem_get_default_faq_items();
    }
    ?>
    <div class="weblazem-tab-content" id="faq-tab">
        <div class="weblazem-admin-card">
            <div class="weblazem-admin-card-icon"><i class="fas fa-question-circle"></i></div>
            <h3>سوالات متداول — صفحه اصلی</h3>

            <table class="form-table">
                <tr valign="top">
                    <th scope="row">عنوان بخش</th>
                    <td>
                        <input type="text" name="weblazem_faq_title" class="large-text" value="<?php echo esc_attr($faq_title); ?>" />
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">متن توضیحی</th>
                    <td>
                        <textarea name="weblazem_faq_subtitle" class="large-text" rows="3"><?php echo esc_textarea($faq_subtitle); ?></textarea>
                    </td>
                </tr>
            </table>

            <h4 style="margin-top:24px;">سوالات</h4>
            <p class="description">هر سوال شامل عنوان و پاسخ است. در صفحه اصلی به‌صورت آکاردئون نمایش داده می‌شوند.</p>

            <div id="faq-items-container">
                <?php foreach ($faq_items as $index => $item) : ?>
                    <div class="weblazem-faq-item-admin" style="background:#f8f5fc;padding:16px;border-radius:12px;margin-bottom:16px;border:1px solid #e8dff0;">
                        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">
                            <h4 style="margin:0;">سوال <?php echo (int) $index + 1; ?></h4>
                            <button type="button" class="button faq-item-remove">حذف</button>
                        </div>
                        <table class="form-table">
                            <tr>
                                <th>سوال</th>
                                <td>
                                    <input type="text" name="weblazem_faq_items[<?php echo esc_attr($index); ?>][question]" class="large-text" value="<?php echo esc_attr($item['question']); ?>" />
                                </td>
                            </tr>
                            <tr>
                                <th>پاسخ</th>
                                <td>
                                    <textarea name="weblazem_faq_items[<?php echo esc_attr($index); ?>][answer]" class="large-text" rows="3"><?php echo esc_textarea($item['answer']); ?></textarea>
                                </td>
                            </tr>
                        </table>
                    </div>
                <?php endforeach; ?>
            </div>

            <button type="button" class="button button-primary" id="add-faq-item">افزودن سوال</button>
        </div>
    </div>

    <script type="text/template" id="faq-item-template">
        <div class="weblazem-faq-item-admin" style="background:#f8f5fc;padding:16px;border-radius:12px;margin-bottom:16px;border:1px solid #e8dff0;">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">
                <h4 style="margin:0;">سوال جدید</h4>
                <button type="button" class="button faq-item-remove">حذف</button>
            </div>
            <table class="form-table">
                <tr>
                    <th>سوال</th>
                    <td><input type="text" name="weblazem_faq_items[{{index}}][question]" class="large-text" value="" /></td>
                </tr>
                <tr>
                    <th>پاسخ</th>
                    <td><textarea name="weblazem_faq_items[{{index}}][answer]" class="large-text" rows="3"></textarea></td>
                </tr>
            </table>
        </div>
    </script>
    <script>
    jQuery(function($) {
        var faqIndex = <?php echo count($faq_items); ?>;

        $('#add-faq-item').on('click', function() {
            var tpl = $('#faq-item-template').html().replace(/\{\{index\}\}/g, faqIndex);
            $('#faq-items-container').append(tpl);
            faqIndex++;
        });

        $(document).on('click', '.faq-item-remove', function() {
            $(this).closest('.weblazem-faq-item-admin').remove();
        });
    });
    </script>
    <?php
}
