

<?php

/*
|--------------------------------------------------------------------------
| Footer Menu
|--------------------------------------------------------------------------
*/

function weblazem_footer_options_page() {

    add_submenu_page(
        'weblazem-theme-options',
        'تنظیمات فوتر',
        'تنظیمات فوتر',
        'manage_options',
        'weblazem-footer-options',
        'weblazem_footer_options_display'
    );

}

add_action('admin_menu', 'weblazem_footer_options_page');


/*
|--------------------------------------------------------------------------
| Register Settings
|--------------------------------------------------------------------------
*/

function weblazem_register_footer_settings() {

    register_setting('weblazem_footer_group', 'weblazem_footer_logo');

    register_setting('weblazem_footer_group', 'weblazem_footer_description');

    register_setting('weblazem_footer_group', 'weblazem_footer_col1_title');
    register_setting('weblazem_footer_group', 'weblazem_footer_col1_items');

    register_setting('weblazem_footer_group', 'weblazem_footer_col2_title');
    register_setting('weblazem_footer_group', 'weblazem_footer_col2_items');

    register_setting('weblazem_footer_group', 'weblazem_footer_col3_title');
    register_setting('weblazem_footer_group', 'weblazem_footer_col3_items');

    register_setting('weblazem_footer_group', 'weblazem_footer_instagram');

    register_setting('weblazem_footer_group', 'weblazem_footer_linkedin');

    register_setting('weblazem_footer_group', 'weblazem_footer_telegram');

    register_setting('weblazem_footer_group', 'weblazem_footer_copyright');

}

add_action('admin_init', 'weblazem_register_footer_settings');


/*
|--------------------------------------------------------------------------
| Load Media Uploader
|--------------------------------------------------------------------------
*/

function weblazem_footer_admin_scripts($hook) {

    if (strpos($hook, 'weblazem-footer-options') !== false) {

        wp_enqueue_media();

    }

}

add_action('admin_enqueue_scripts', 'weblazem_footer_admin_scripts');


/*
|--------------------------------------------------------------------------
| Page HTML
|--------------------------------------------------------------------------
*/

function weblazem_footer_options_display() {

?>

<div class="wrap">

    <h1>تنظیمات فوتر</h1>

    <form method="post" action="options.php">

        <?php settings_fields('weblazem_footer_group'); ?>

        <table class="form-table">

            <tr>

                <th>لوگوی فوتر</th>

                <td>

                    <input
                        type="hidden"
                        id="weblazem_footer_logo"
                        name="weblazem_footer_logo"
                        value="<?php echo esc_attr(get_option('weblazem_footer_logo')); ?>"
                    >

                    <div id="footer-logo-preview">

                        <?php if(get_option('weblazem_footer_logo')): ?>

                            <img
                                src="<?php echo esc_url(get_option('weblazem_footer_logo')); ?>"
                                style="max-width:180px;margin-bottom:15px;border-radius:10px;"
                            >

                        <?php endif; ?>

                    </div>

                    <button
                        type="button"
                        class="button button-primary"
                        id="upload_footer_logo_button"
                    >
                        انتخاب لوگو
                    </button>

                </td>

            </tr>

            <tr>

                <th>توضیحات فوتر</th>

                <td>

                    <textarea
                        name="weblazem_footer_description"
                        rows="5"
                        class="large-text"
                    ><?php echo esc_textarea(get_option('weblazem_footer_description')); ?></textarea>

                </td>

            </tr>

            <tr>

                <th>عنوان ستون اول</th>

                <td>

                    <input
                        type="text"
                        name="weblazem_footer_col1_title"
                        value="<?php echo esc_attr(get_option('weblazem_footer_col1_title')); ?>"
                        class="regular-text"
                    >

                </td>

            </tr>

            <tr>

                <th>آیتم‌های ستون اول</th>

                <td>

                    <textarea
                        name="weblazem_footer_col1_items"
                        rows="6"
                        class="large-text"
                    ><?php echo esc_textarea(get_option('weblazem_footer_col1_items')); ?></textarea>

                </td>

            </tr>

            <tr>

                <th>عنوان ستون دوم</th>

                <td>

                    <input
                        type="text"
                        name="weblazem_footer_col2_title"
                        value="<?php echo esc_attr(get_option('weblazem_footer_col2_title')); ?>"
                        class="regular-text"
                    >

                </td>

            </tr>

            <tr>

                <th>آیتم‌های ستون دوم</th>

                <td>

                    <textarea
                        name="weblazem_footer_col2_items"
                        rows="6"
                        class="large-text"
                    ><?php echo esc_textarea(get_option('weblazem_footer_col2_items')); ?></textarea>

                </td>

            </tr>

            <tr>

                <th>عنوان ستون سوم</th>

                <td>

                    <input
                        type="text"
                        name="weblazem_footer_col3_title"
                        value="<?php echo esc_attr(get_option('weblazem_footer_col3_title')); ?>"
                        class="regular-text"
                    >

                </td>

            </tr>

            <tr>

                <th>آیتم‌های ستون سوم</th>

                <td>

                    <textarea
                        name="weblazem_footer_col3_items"
                        rows="6"
                        class="large-text"
                    ><?php echo esc_textarea(get_option('weblazem_footer_col3_items')); ?></textarea>

                </td>

            </tr>

            <tr>

                <th>اینستاگرام</th>

                <td>

                    <input
                        type="url"
                        name="weblazem_footer_instagram"
                        value="<?php echo esc_attr(get_option('weblazem_footer_instagram')); ?>"
                        class="regular-text"
                    >

                </td>

            </tr>

            <tr>

                <th>لینکدین</th>

                <td>

                    <input
                        type="url"
                        name="weblazem_footer_linkedin"
                        value="<?php echo esc_attr(get_option('weblazem_footer_linkedin')); ?>"
                        class="regular-text"
                    >

                </td>

            </tr>

            <tr>

                <th>تلگرام</th>

                <td>

                    <input
                        type="url"
                        name="weblazem_footer_telegram"
                        value="<?php echo esc_attr(get_option('weblazem_footer_telegram')); ?>"
                        class="regular-text"
                    >

                </td>

            </tr>

            <tr>

                <th>کپی‌رایت</th>

                <td>

                    <input
                        type="text"
                        name="weblazem_footer_copyright"
                        value="<?php echo esc_attr(get_option('weblazem_footer_copyright')); ?>"
                        class="regular-text"
                    >

                </td>

            </tr>

        </table>

        <?php submit_button(); ?>

    </form>

</div>

<script>

jQuery(document).ready(function($){

    let mediaUploader;

    $('#upload_footer_logo_button').click(function(e){

        e.preventDefault();

        if(mediaUploader){

            mediaUploader.open();
            return;

        }

        mediaUploader = wp.media({

            title: 'انتخاب لوگوی فوتر',

            button: {
                text: 'استفاده از لوگو'
            },

            multiple: false

        });

        mediaUploader.on('select', function(){

            let attachment = mediaUploader
                .state()
                .get('selection')
                .first()
                .toJSON();

            $('#weblazem_footer_logo').val(attachment.url);

            $('#footer-logo-preview').html(
                '<img src="'+attachment.url+'" style="max-width:180px;margin-top:15px;border-radius:10px;">'
            );

        });

        mediaUploader.open();

    });

});

</script>

<?php

}

