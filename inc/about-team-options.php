<?php
/**
 * About Us + Team sections — options, defaults, admin, save.
 */

function weblazem_get_default_about_options() {
    return array(
        'title'       => 'معرفی ما و مجموعه‌ی وب‌لازم',
        'text'        => 'لورم ایپسوم متن ساختگی با تولید سادگی نامفهوم از صنعت چاپ و با استفاده از طراحان گرافیک است. چاپگرها و متون بلکه روزنامه و مجله در ستون و سطرآنچنان که لازم است و برای شرایط فعلی تکنولوژی مورد نیاز و کاربردهای متنوع با هدف بهبود ابزارهای کاربردی می‌باشد.',
        'image'       => get_template_directory_uri() . '/assets/images/about-demo.jpg',
        'button_text' => 'ارتباط با ما',
        'button_url'  => '#',
    );
}

function weblazem_get_default_team_members() {
    $img_base = get_template_directory_uri() . '/assets/images/';

    return array(
        array(
            'name'   => 'اسم و فامیل',
            'role'   => 'کارشناس ارشد سئو و تولید محتوا',
            'bio'    => 'لورم ایپسوم متن ساختگی با تولید سادگی نامفهوم از صنعت چاپ و با استفاده از طراحان گرافیک است. چاپگرها و متون بلکه روزنامه و مجله در ستون و سطرآنچنان که لازم است و برای شرایط فعلی تکنولوژی مورد نیاز و کاربردهای متنوع با هدف بهبود ابزارهای کاربردی می‌باشد.',
            'image'  => $img_base . 'team-demo-1.jpg',
            'layout' => 'text-image',
        ),
        array(
            'name'   => 'اسم و فامیل',
            'role'   => 'برنامه‌نویس و کارشناس ارشد طراحی وب‌سایت',
            'bio'    => 'لورم ایپسوم متن ساختگی با تولید سادگی نامفهوم از صنعت چاپ و با استفاده از طراحان گرافیک است. چاپگرها و متون بلکه روزنامه و مجله در ستون و سطرآنچنان که لازم است و برای شرایط فعلی تکنولوژی مورد نیاز و کاربردهای متنوع با هدف بهبود ابزارهای کاربردی می‌باشد.',
            'image'  => $img_base . 'team-demo-2.jpg',
            'layout' => 'image-text',
        ),
    );
}

function weblazem_ensure_about_team_defaults() {
    $about_defaults = weblazem_get_default_about_options();

    if (get_option('weblazem_about_title') === false) {
        update_option('weblazem_about_title', $about_defaults['title']);
    }
    if (get_option('weblazem_about_text') === false) {
        update_option('weblazem_about_text', $about_defaults['text']);
    }
    if (get_option('weblazem_about_image') === false) {
        update_option('weblazem_about_image', $about_defaults['image']);
    }
    if (get_option('weblazem_about_button_text') === false) {
        update_option('weblazem_about_button_text', $about_defaults['button_text']);
    }
    if (get_option('weblazem_about_button_url') === false) {
        update_option('weblazem_about_button_url', $about_defaults['button_url']);
    }

    if (get_option('weblazem_team_title') === false) {
        update_option('weblazem_team_title', 'تیم لید خدمات تخصصی وب‌لازم');
    }
    if (get_option('weblazem_team_members') === false) {
        update_option('weblazem_team_members', weblazem_get_default_team_members());
    }
}
add_action('init', 'weblazem_ensure_about_team_defaults', 15);

function weblazem_sanitize_team_members($input) {
    if (empty($input) || !is_array($input)) {
        return array();
    }

    $sanitized = array();

    foreach ($input as $member) {
        if (empty($member['name'])) {
            continue;
        }

        $layout = isset($member['layout']) ? $member['layout'] : 'text-image';
        if (!in_array($layout, array('text-image', 'image-text'), true)) {
            $layout = 'text-image';
        }

        $sanitized[] = array(
            'name'   => sanitize_text_field($member['name']),
            'role'   => isset($member['role']) ? sanitize_text_field($member['role']) : '',
            'bio'    => isset($member['bio']) ? wp_kses_post($member['bio']) : '',
            'image'  => isset($member['image']) ? esc_url_raw($member['image']) : '',
            'layout' => $layout,
        );
    }

    return $sanitized;
}

function weblazem_handle_option_image_upload($file_field, $current_value = '') {
    if (empty($_FILES[$file_field]['name'])) {
        return $current_value;
    }

    $upload = wp_handle_upload($_FILES[$file_field], array('test_form' => false));

    if (isset($upload['error'])) {
        return $current_value;
    }

    return isset($upload['url']) ? $upload['url'] : $current_value;
}

function weblazem_save_about_team_homepage_options() {
    if (isset($_POST['weblazem_about_title'])) {
        update_option('weblazem_about_title', sanitize_text_field(wp_unslash($_POST['weblazem_about_title'])));
    }

    if (isset($_POST['weblazem_about_text'])) {
        update_option('weblazem_about_text', wp_kses_post(wp_unslash($_POST['weblazem_about_text'])));
    }

    if (isset($_POST['weblazem_about_button_text'])) {
        update_option('weblazem_about_button_text', sanitize_text_field(wp_unslash($_POST['weblazem_about_button_text'])));
    }

    if (isset($_POST['weblazem_about_button_url'])) {
        update_option('weblazem_about_button_url', esc_url_raw(wp_unslash($_POST['weblazem_about_button_url'])));
    }

    $about_image = isset($_POST['weblazem_about_image']) ? esc_url_raw(wp_unslash($_POST['weblazem_about_image'])) : get_option('weblazem_about_image', '');
    $about_image = weblazem_handle_option_image_upload('weblazem_about_image_file', $about_image);
    update_option('weblazem_about_image', $about_image);

    if (isset($_POST['weblazem_team_title'])) {
        update_option('weblazem_team_title', sanitize_text_field(wp_unslash($_POST['weblazem_team_title'])));
    }

    if (isset($_POST['weblazem_team_members']) && is_array($_POST['weblazem_team_members'])) {
        $members = array();

        foreach ($_POST['weblazem_team_members'] as $index => $member) {
            if (empty($member['name'])) {
                continue;
            }

            $layout = isset($member['layout']) ? sanitize_text_field($member['layout']) : 'text-image';
            if (!in_array($layout, array('text-image', 'image-text'), true)) {
                $layout = 'text-image';
            }

            $image = isset($member['image']) ? esc_url_raw($member['image']) : '';
            $file_key = 'weblazem_team_member_image_file_' . (int) $index;

            if (!empty($_FILES[$file_key]['name'])) {
                $image = weblazem_handle_option_image_upload($file_key, $image);
            }

            $members[] = array(
                'name'   => sanitize_text_field($member['name']),
                'role'   => isset($member['role']) ? sanitize_text_field($member['role']) : '',
                'bio'    => isset($member['bio']) ? wp_kses_post($member['bio']) : '',
                'image'  => $image,
                'layout' => $layout,
            );
        }

        update_option('weblazem_team_members', $members);
    }
}

function weblazem_render_about_homepage_tab() {
    $about_title       = get_option('weblazem_about_title', weblazem_get_default_about_options()['title']);
    $about_text        = get_option('weblazem_about_text', weblazem_get_default_about_options()['text']);
    $about_image       = get_option('weblazem_about_image', weblazem_get_default_about_options()['image']);
    $about_button_text = get_option('weblazem_about_button_text', weblazem_get_default_about_options()['button_text']);
    $about_button_url  = get_option('weblazem_about_button_url', weblazem_get_default_about_options()['button_url']);
    ?>
    <div class="weblazem-tab-content" id="about-tab">
        <div class="weblazem-admin-card">
            <div class="weblazem-admin-card-icon"><i class="fas fa-info-circle"></i></div>
            <h3>ویرایش درباره ما — صفحه اصلی</h3>

            <table class="form-table">
                <tr valign="top">
                    <th scope="row">عنوان بخش</th>
                    <td>
                        <input type="text" name="weblazem_about_title" class="large-text" value="<?php echo esc_attr($about_title); ?>" />
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">متن معرفی</th>
                    <td>
                        <textarea name="weblazem_about_text" class="large-text" rows="6"><?php echo esc_textarea($about_text); ?></textarea>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">تصویر بخش</th>
                    <td>
                        <?php if (!empty($about_image)) : ?>
                            <div style="margin-bottom:12px;">
                                <img src="<?php echo esc_url($about_image); ?>" alt="" style="max-width:280px;border-radius:12px;" />
                            </div>
                        <?php endif; ?>
                        <input type="file" name="weblazem_about_image_file" accept="image/*" />
                        <input type="hidden" name="weblazem_about_image" value="<?php echo esc_attr($about_image); ?>" />
                        <p class="description">پیشنهاد: تصویر افقی با نسبت ۴:۳</p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">متن دکمه</th>
                    <td>
                        <input type="text" name="weblazem_about_button_text" class="regular-text" value="<?php echo esc_attr($about_button_text); ?>" />
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">لینک دکمه</th>
                    <td>
                        <input type="url" name="weblazem_about_button_url" class="large-text" value="<?php echo esc_url($about_button_url); ?>" />
                    </td>
                </tr>
            </table>
        </div>
    </div>
    <?php
}

function weblazem_render_team_homepage_tab() {
    $team_title = get_option('weblazem_team_title', 'تیم لید خدمات تخصصی وب‌لازم');
    $team_members = get_option('weblazem_team_members', weblazem_get_default_team_members());

    if (!is_array($team_members)) {
        $team_members = weblazem_get_default_team_members();
    }
    ?>
    <div class="weblazem-tab-content" id="team-tab">
        <div class="weblazem-admin-card">
            <div class="weblazem-admin-card-icon"><i class="fas fa-users"></i></div>
            <h3>تیم لید — صفحه اصلی</h3>

            <table class="form-table">
                <tr valign="top">
                    <th scope="row">عنوان بخش</th>
                    <td>
                        <input type="text" name="weblazem_team_title" class="large-text" value="<?php echo esc_attr($team_title); ?>" />
                    </td>
                </tr>
            </table>

            <h4 style="margin-top:24px;">اعضای تیم</h4>
            <?php foreach ($team_members as $index => $member) : ?>
                <div class="weblazem-team-member-admin" style="background:#f8f5fc;padding:16px;border-radius:12px;margin-bottom:16px;border:1px solid #e8dff0;">
                    <h4>عضو <?php echo (int) $index + 1; ?></h4>
                    <table class="form-table">
                        <tr>
                            <th>نام و نام خانوادگی</th>
                            <td><input type="text" name="weblazem_team_members[<?php echo esc_attr($index); ?>][name]" class="regular-text" value="<?php echo esc_attr($member['name']); ?>" /></td>
                        </tr>
                        <tr>
                            <th>سمت</th>
                            <td><input type="text" name="weblazem_team_members[<?php echo esc_attr($index); ?>][role]" class="regular-text" value="<?php echo esc_attr($member['role']); ?>" /></td>
                        </tr>
                        <tr>
                            <th>توضیحات</th>
                            <td><textarea name="weblazem_team_members[<?php echo esc_attr($index); ?>][bio]" class="large-text" rows="3"><?php echo esc_textarea($member['bio']); ?></textarea></td>
                        </tr>
                        <tr>
                            <th>چیدمان</th>
                            <td>
                                <select name="weblazem_team_members[<?php echo esc_attr($index); ?>][layout]">
                                    <option value="text-image" <?php selected($member['layout'], 'text-image'); ?>>متن سپس تصویر</option>
                                    <option value="image-text" <?php selected($member['layout'], 'image-text'); ?>>تصویر سپس متن</option>
                                </select>
                                <p class="description">در صفحه اصلی چیدمان آینه‌ای به‌صورت خودکار از موقعیت عضو (اول/دوم) تعیین می‌شود.</p>
                            </td>
                        </tr>
                        <tr>
                            <th>تصویر</th>
                            <td>
                                <?php if (!empty($member['image'])) : ?>
                                    <img src="<?php echo esc_url($member['image']); ?>" alt="" style="width:90px;height:90px;border-radius:50%;object-fit:cover;margin-bottom:10px;display:block;" />
                                <?php endif; ?>
                                <input type="file" name="weblazem_team_member_image_file_<?php echo esc_attr($index); ?>" accept="image/*" />
                                <input type="hidden" name="weblazem_team_members[<?php echo esc_attr($index); ?>][image]" value="<?php echo esc_attr($member['image']); ?>" />
                            </td>
                        </tr>
                    </table>
                </div>
            <?php endforeach; ?>
            <p class="description">برای نمایش در صفحه اصلی حداقل یک عضو با نام پر شده کافی است.</p>
        </div>
    </div>
    <?php
}
