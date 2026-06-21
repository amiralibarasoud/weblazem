<?php
/**
 * Homepage portfolio section — admin tab markup.
 */

function weblazem_render_portfolio_homepage_tab() {
    $portfolio_title = get_option('weblazem_portfolio_title', 'جدیدترین نمونه‌کارهای وب‌لازم');
    $portfolio_more_text = get_option('weblazem_portfolio_more_text', 'نمایش بیشتر');
    $portfolio_card_button_text = get_option('weblazem_portfolio_card_button_text', 'مشاهده‌ی پروژه');
    ?>
    <div class="weblazem-tab-content" id="portfolio-tab">
        <div class="weblazem-admin-card">
            <div class="weblazem-admin-card-icon"><i class="fas fa-images"></i></div>
            <h3>بخش نمونه کارها (صفحه اصلی)</h3>
            <p class="description" style="margin-bottom: 20px;">
                نمونه کارها از منوی «نمونه کارها» در پیشخوان وردپرس مدیریت می‌شوند. ۴ مورد جدیدتر به‌صورت خودکار در صفحه اصلی نمایش داده می‌شود.
                <a href="<?php echo esc_url(admin_url('edit.php?post_type=portfolio')); ?>">مدیریت نمونه کارها</a>
                |
                <a href="<?php echo esc_url(admin_url('post-new.php?post_type=portfolio')); ?>">افزودن نمونه کار جدید</a>
            </p>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">عنوان بخش</th>
                    <td>
                        <input type="text" name="weblazem_portfolio_title" class="regular-text" value="<?php echo esc_attr($portfolio_title); ?>" />
                        <p class="description">عنوانی که در بالای بخش نمونه کارها نمایش داده می‌شود</p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">متن دکمه «نمایش بیشتر»</th>
                    <td>
                        <input type="text" name="weblazem_portfolio_more_text" class="regular-text" value="<?php echo esc_attr($portfolio_more_text); ?>" />
                        <p class="description">
                            لینک این دکمه به آرشیو نمونه کارها می‌رود:
                            <a href="<?php echo esc_url(weblazem_get_portfolio_archive_url()); ?>" target="_blank" rel="noopener"><?php echo esc_html(weblazem_get_portfolio_archive_url()); ?></a>
                        </p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">متن دکمه هر کارت</th>
                    <td>
                        <input type="text" name="weblazem_portfolio_card_button_text" class="regular-text" value="<?php echo esc_attr($portfolio_card_button_text); ?>" />
                        <p class="description">متن دکمه «مشاهده پروژه» روی هر کارت</p>
                    </td>
                </tr>
            </table>
        </div>
    </div>
    <?php
}

function weblazem_save_portfolio_homepage_options() {
    if (isset($_POST['weblazem_portfolio_title'])) {
        update_option('weblazem_portfolio_title', sanitize_text_field($_POST['weblazem_portfolio_title']));
    }

    if (isset($_POST['weblazem_portfolio_more_text'])) {
        update_option('weblazem_portfolio_more_text', sanitize_text_field($_POST['weblazem_portfolio_more_text']));
    }

    if (isset($_POST['weblazem_portfolio_card_button_text'])) {
        update_option('weblazem_portfolio_card_button_text', sanitize_text_field($_POST['weblazem_portfolio_card_button_text']));
    }
}
