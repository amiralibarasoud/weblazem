<?php
/**
 * Testimonials section — options, defaults, admin, save.
 */

function weblazem_get_default_testimonials() {
    $img_base = get_template_directory_uri() . '/assets/images/testimonials/';

    return array(
        array(
            'name'   => 'تارا',
            'text'   => 'شروع خیلی لذت‌بخشی بود و از این بابت خیلی خوشحالم که با وب‌لازم همکاری کردم. تیم حرفه‌ای و پاسخگو بودند.',
            'rating' => 5,
            'avatar' => $img_base . 'avatar-1.svg',
        ),
        array(
            'name'   => 'مسعود',
            'text'   => 'کیفیت طراحی و پشتیبانی عالی بود. پروژه‌ام دقیقاً مطابق انتظار تحویل شد و نتیجه فوق‌العاده شد.',
            'rating' => 5,
            'avatar' => $img_base . 'avatar-2.svg',
        ),
        array(
            'name'   => 'احسان',
            'text'   => 'همکاری با وب‌لازم تجربه‌ای متفاوت بود. از مشاوره اولیه تا تحویل نهایی، همه چیز منظم و شفاف پیش رفت.',
            'rating' => 5,
            'avatar' => $img_base . 'avatar-3.svg',
        ),
        array(
            'name'   => 'سارا',
            'text'   => 'سرعت عمل تیم و توجه به جزئیات واقعاً قابل تحسین است. حتماً برای پروژه‌های بعدی هم با آن‌ها کار می‌کنم.',
            'rating' => 5,
            'avatar' => $img_base . 'avatar-4.svg',
        ),
    );
}

function weblazem_ensure_testimonials_defaults() {
    if (get_option('weblazem_testimonials_title') === false) {
        update_option('weblazem_testimonials_title', 'تجربه‌ی مشتریان از همکاری با وب‌لازم');
    }
    if (get_option('weblazem_testimonials_rating_label') === false) {
        update_option('weblazem_testimonials_rating_label', 'رتبه‌بندی ۱۴۰ کاربر');
    }
    if (get_option('weblazem_testimonials_rating_score') === false) {
        update_option('weblazem_testimonials_rating_score', '۴.۶');
    }
    if (get_option('weblazem_testimonials_rating_value') === false) {
        update_option('weblazem_testimonials_rating_value', 5);
    }
    if (get_option('weblazem_testimonials_items') === false) {
        update_option('weblazem_testimonials_items', weblazem_get_default_testimonials());
    }
}
add_action('init', 'weblazem_ensure_testimonials_defaults', 15);

function weblazem_sanitize_testimonials_items($input) {
    if (empty($input) || !is_array($input)) {
        return array();
    }

    $sanitized = array();

    foreach ($input as $item) {
        if (empty($item['name']) && empty($item['text'])) {
            continue;
        }

        $rating = isset($item['rating']) ? (int) $item['rating'] : 5;
        $rating = max(1, min(5, $rating));

        $sanitized[] = array(
            'name'   => isset($item['name']) ? sanitize_text_field($item['name']) : '',
            'text'   => isset($item['text']) ? wp_kses_post($item['text']) : '',
            'rating' => $rating,
            'avatar' => isset($item['avatar']) ? esc_url_raw($item['avatar']) : '',
        );
    }

    return $sanitized;
}

function weblazem_save_testimonials_homepage_options() {
    if (isset($_POST['weblazem_testimonials_title'])) {
        update_option('weblazem_testimonials_title', sanitize_text_field(wp_unslash($_POST['weblazem_testimonials_title'])));
    }

    if (isset($_POST['weblazem_testimonials_rating_label'])) {
        update_option('weblazem_testimonials_rating_label', sanitize_text_field(wp_unslash($_POST['weblazem_testimonials_rating_label'])));
    }

    if (isset($_POST['weblazem_testimonials_rating_score'])) {
        update_option('weblazem_testimonials_rating_score', sanitize_text_field(wp_unslash($_POST['weblazem_testimonials_rating_score'])));
    }

    if (isset($_POST['weblazem_testimonials_rating_value'])) {
        $rating_value = (int) $_POST['weblazem_testimonials_rating_value'];
        update_option('weblazem_testimonials_rating_value', max(1, min(5, $rating_value)));
    }

    if (isset($_POST['weblazem_testimonials_items']) && is_array($_POST['weblazem_testimonials_items'])) {
        $items = array();

        foreach ($_POST['weblazem_testimonials_items'] as $index => $item) {
            if (empty($item['name']) && empty($item['text'])) {
                continue;
            }

            $avatar = isset($item['avatar']) ? esc_url_raw($item['avatar']) : '';
            $file_key = 'weblazem_testimonial_avatar_file_' . (int) $index;

            if (!empty($_FILES[$file_key]['name'])) {
                $avatar = weblazem_handle_option_image_upload($file_key, $avatar);
            }

            $rating = isset($item['rating']) ? (int) $item['rating'] : 5;

            $items[] = array(
                'name'   => isset($item['name']) ? sanitize_text_field($item['name']) : '',
                'text'   => isset($item['text']) ? wp_kses_post($item['text']) : '',
                'rating' => max(1, min(5, $rating)),
                'avatar' => $avatar,
            );
        }

        update_option('weblazem_testimonials_items', $items);
    }
}

function weblazem_render_star_rating($rating, $extra_class = '') {
    $rating = max(0, min(5, (int) $rating));
    $classes = trim('weblazem-star-rating ' . $extra_class);
    ?>
    <span class="<?php echo esc_attr($classes); ?>" aria-label="<?php echo esc_attr(sprintf('امتیاز %d از 5', $rating)); ?>">
        <?php for ($i = 1; $i <= 5; $i++) : ?>
            <i class="<?php echo $i <= $rating ? 'fas' : 'far'; ?> fa-star" aria-hidden="true"></i>
        <?php endfor; ?>
    </span>
    <?php
}

function weblazem_render_testimonials_homepage_tab() {
    $title        = get_option('weblazem_testimonials_title', 'تجربه‌ی مشتریان از همکاری با وب‌لازم');
    $rating_label = get_option('weblazem_testimonials_rating_label', 'رتبه‌بندی ۱۴۰ کاربر');
    $rating_score = get_option('weblazem_testimonials_rating_score', '۴.۶');
    $rating_value = (int) get_option('weblazem_testimonials_rating_value', 5);
    $items        = get_option('weblazem_testimonials_items', weblazem_get_default_testimonials());

    if (!is_array($items)) {
        $items = weblazem_get_default_testimonials();
    }
    ?>
    <div class="weblazem-tab-content" id="testimonials-tab">
        <div class="weblazem-admin-card">
            <div class="weblazem-admin-card-icon"><i class="fas fa-comment-dots"></i></div>
            <h3>نظرات مشتریان — صفحه اصلی</h3>

            <table class="form-table">
                <tr valign="top">
                    <th scope="row">عنوان بخش</th>
                    <td>
                        <input type="text" name="weblazem_testimonials_title" class="large-text" value="<?php echo esc_attr($title); ?>" />
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">متن رتبه‌بندی</th>
                    <td>
                        <input type="text" name="weblazem_testimonials_rating_label" class="regular-text" value="<?php echo esc_attr($rating_label); ?>" />
                        <p class="description">مثال: رتبه‌بندی ۱۴۰ کاربر</p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">امتیاز کلی</th>
                    <td>
                        <input type="text" name="weblazem_testimonials_rating_score" class="small-text" value="<?php echo esc_attr($rating_score); ?>" />
                        <p class="description">مثال: ۴.۶</p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">ستاره‌های امتیاز کلی</th>
                    <td>
                        <select name="weblazem_testimonials_rating_value">
                            <?php for ($i = 5; $i >= 1; $i--) : ?>
                                <option value="<?php echo esc_attr($i); ?>" <?php selected($rating_value, $i); ?>><?php echo esc_html($i); ?> ستاره</option>
                            <?php endfor; ?>
                        </select>
                    </td>
                </tr>
            </table>

            <h4 style="margin-top:24px;">نظرات</h4>
            <div id="testimonials-items-container">
                <?php foreach ($items as $index => $item) : ?>
                    <div class="weblazem-testimonial-admin" style="background:#f8f5fc;padding:16px;border-radius:12px;margin-bottom:16px;border:1px solid #e8dff0;">
                        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">
                            <h4 style="margin:0;">نظر <?php echo (int) $index + 1; ?></h4>
                            <button type="button" class="button testimonial-item-remove">حذف</button>
                        </div>
                        <table class="form-table">
                            <tr>
                                <th>نام</th>
                                <td>
                                    <input type="text" name="weblazem_testimonials_items[<?php echo esc_attr($index); ?>][name]" class="regular-text" value="<?php echo esc_attr($item['name']); ?>" />
                                </td>
                            </tr>
                            <tr>
                                <th>متن نظر</th>
                                <td>
                                    <textarea name="weblazem_testimonials_items[<?php echo esc_attr($index); ?>][text]" class="large-text" rows="3"><?php echo esc_textarea($item['text']); ?></textarea>
                                </td>
                            </tr>
                            <tr>
                                <th>امتیاز</th>
                                <td>
                                    <select name="weblazem_testimonials_items[<?php echo esc_attr($index); ?>][rating]">
                                        <?php for ($r = 5; $r >= 1; $r--) : ?>
                                            <option value="<?php echo esc_attr($r); ?>" <?php selected((int) $item['rating'], $r); ?>><?php echo esc_html($r); ?> ستاره</option>
                                        <?php endfor; ?>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <th>تصویر پروفایل</th>
                                <td>
                                    <?php if (!empty($item['avatar'])) : ?>
                                        <img src="<?php echo esc_url($item['avatar']); ?>" alt="" style="width:56px;height:56px;border-radius:50%;object-fit:cover;margin-bottom:10px;display:block;" />
                                    <?php endif; ?>
                                    <input type="file" name="weblazem_testimonial_avatar_file_<?php echo esc_attr($index); ?>" accept="image/*" />
                                    <input type="hidden" name="weblazem_testimonials_items[<?php echo esc_attr($index); ?>][avatar]" value="<?php echo esc_attr($item['avatar']); ?>" />
                                </td>
                            </tr>
                        </table>
                    </div>
                <?php endforeach; ?>
            </div>

            <button type="button" class="button button-primary" id="add-testimonial-item">افزودن نظر</button>
        </div>
    </div>

    <script type="text/template" id="testimonial-item-template">
        <div class="weblazem-testimonial-admin" style="background:#f8f5fc;padding:16px;border-radius:12px;margin-bottom:16px;border:1px solid #e8dff0;">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">
                <h4 style="margin:0;">نظر جدید</h4>
                <button type="button" class="button testimonial-item-remove">حذف</button>
            </div>
            <table class="form-table">
                <tr>
                    <th>نام</th>
                    <td><input type="text" name="weblazem_testimonials_items[{{index}}][name]" class="regular-text" value="" /></td>
                </tr>
                <tr>
                    <th>متن نظر</th>
                    <td><textarea name="weblazem_testimonials_items[{{index}}][text]" class="large-text" rows="3"></textarea></td>
                </tr>
                <tr>
                    <th>امتیاز</th>
                    <td>
                        <select name="weblazem_testimonials_items[{{index}}][rating]">
                            <option value="5" selected>5 ستاره</option>
                            <option value="4">4 ستاره</option>
                            <option value="3">3 ستاره</option>
                            <option value="2">2 ستاره</option>
                            <option value="1">1 ستاره</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th>تصویر پروفایل</th>
                    <td>
                        <input type="file" name="weblazem_testimonial_avatar_file_{{index}}" accept="image/*" />
                        <input type="hidden" name="weblazem_testimonials_items[{{index}}][avatar]" value="" />
                    </td>
                </tr>
            </table>
        </div>
    </script>
    <script>
    jQuery(function($) {
        var testimonialIndex = <?php echo count($items); ?>;

        $('#add-testimonial-item').on('click', function() {
            var tpl = $('#testimonial-item-template').html().replace(/\{\{index\}\}/g, testimonialIndex);
            $('#testimonials-items-container').append(tpl);
            testimonialIndex++;
        });

        $(document).on('click', '.testimonial-item-remove', function() {
            $(this).closest('.weblazem-testimonial-admin').remove();
        });
    });
    </script>
    <?php
}
