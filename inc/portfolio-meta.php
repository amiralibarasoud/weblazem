<?php
/**
 * Meta boxes for portfolio post type.
 */

function weblazem_portfolio_meta_boxes() {
    add_meta_box(
        'weblazem_portfolio_details',
        'جزئیات نمونه کار',
        'weblazem_portfolio_meta_box_render',
        'portfolio',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'weblazem_portfolio_meta_boxes');

function weblazem_portfolio_meta_box_render($post) {
    wp_nonce_field('weblazem_portfolio_meta_save', 'weblazem_portfolio_meta_nonce');

    $subtitle    = get_post_meta($post->ID, '_weblazem_portfolio_subtitle', true);
    $client      = get_post_meta($post->ID, '_weblazem_portfolio_client', true);
    $project_url = get_post_meta($post->ID, '_weblazem_portfolio_project_url', true);
    ?>
    <table class="form-table">
        <tr>
            <th scope="row"><label for="weblazem_portfolio_subtitle">عنوان نمایشی در کارت</label></th>
            <td>
                <input type="text" id="weblazem_portfolio_subtitle" name="weblazem_portfolio_subtitle"
                       class="large-text" value="<?php echo esc_attr($subtitle); ?>"
                       placeholder="<?php echo esc_attr(get_the_title($post)); ?>" />
                <p class="description">در صفحه نمونه کارها زیر تصویر نمایش داده می‌شود. اگر خالی باشد، از عنوان پست استفاده می‌شود.</p>
            </td>
        </tr>
        <tr>
            <th scope="row">خلاصه کارت</th>
            <td>
                <p class="description">از فیلد «چکیده» در ویرایشگر (Excerpt) برای متن کوتاه زیر عنوان کارت در صفحه نمونه کارها استفاده کنید.</p>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="weblazem_portfolio_client">کارفرما / مشتری</label></th>
            <td>
                <input type="text" id="weblazem_portfolio_client" name="weblazem_portfolio_client"
                       class="regular-text" value="<?php echo esc_attr($client); ?>" />
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="weblazem_portfolio_project_url">لینک پروژه (اختیاری)</label></th>
            <td>
                <input type="url" id="weblazem_portfolio_project_url" name="weblazem_portfolio_project_url"
                       class="large-text" value="<?php echo esc_url($project_url); ?>" placeholder="https://" />
                <p class="description">اگر پر شود، دکمه «مشاهده پروژه» به این آدرس می‌رود؛ در غیر این صورت به صفحه جزئیات نمونه کار.</p>
            </td>
        </tr>
    </table>
    <?php
}

function weblazem_portfolio_meta_save($post_id) {
    if (!isset($_POST['weblazem_portfolio_meta_nonce']) ||
        !wp_verify_nonce($_POST['weblazem_portfolio_meta_nonce'], 'weblazem_portfolio_meta_save')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    if (get_post_type($post_id) !== 'portfolio') {
        return;
    }

    if (isset($_POST['weblazem_portfolio_subtitle'])) {
        update_post_meta($post_id, '_weblazem_portfolio_subtitle', sanitize_text_field($_POST['weblazem_portfolio_subtitle']));
    }

    if (isset($_POST['weblazem_portfolio_client'])) {
        update_post_meta($post_id, '_weblazem_portfolio_client', sanitize_text_field($_POST['weblazem_portfolio_client']));
    }

    if (isset($_POST['weblazem_portfolio_project_url'])) {
        update_post_meta($post_id, '_weblazem_portfolio_project_url', esc_url_raw($_POST['weblazem_portfolio_project_url']));
    }
}
add_action('save_post_portfolio', 'weblazem_portfolio_meta_save');

/**
 * Card title for homepage / archive listings.
 */
function weblazem_get_portfolio_card_title($post_id = null) {
    $post_id = $post_id ? $post_id : get_the_ID();
    $subtitle = get_post_meta($post_id, '_weblazem_portfolio_subtitle', true);

    if (!empty($subtitle)) {
        return $subtitle;
    }

    return get_the_title($post_id);
}

/**
 * Project link for card button.
 */
function weblazem_get_portfolio_project_link($post_id = null) {
    $post_id = $post_id ? $post_id : get_the_ID();
    $external = get_post_meta($post_id, '_weblazem_portfolio_project_url', true);

    if (!empty($external)) {
        return $external;
    }

    return get_permalink($post_id);
}

/**
 * Short description for portfolio cards (excerpt or trimmed content).
 */
function weblazem_get_portfolio_card_description($post_id = null) {
    $post_id = $post_id ? $post_id : get_the_ID();
    $excerpt = get_post_field('post_excerpt', $post_id);

    if (!empty($excerpt)) {
        return wp_trim_words($excerpt, 18, '…');
    }

    $content = get_post_field('post_content', $post_id);

    if (!empty($content)) {
        return wp_trim_words(wp_strip_all_tags($content), 18, '…');
    }

    return '';
}

/**
 * Desktop + mobile screenshots for device mockups.
 *
 * @param int|null $post_id
 * @return array{desktop:string,mobile:string,mobile_is_fallback:bool}
 */
function weblazem_get_portfolio_device_images($post_id = null) {
    $post_id = $post_id ? (int) $post_id : (int) get_the_ID();

    $desktop = '';

    if (function_exists('weblazem_get_portfolio_single_hero_image')) {
        $desktop = (string) weblazem_get_portfolio_single_hero_image($post_id);
    }

    if ($desktop === '' && has_post_thumbnail($post_id)) {
        $desktop = (string) get_the_post_thumbnail_url($post_id, 'large');
    }

    $mobile_meta = (string) get_post_meta($post_id, '_weblazem_portfolio_mobile_image', true);
    $has_mobile  = $mobile_meta !== '';

    return array(
        'desktop'            => $desktop,
        'mobile'             => $has_mobile ? $mobile_meta : $desktop,
        'mobile_is_fallback' => !$has_mobile && $desktop !== '',
    );
}

/**
 * Render monitor + phone device mockup.
 *
 * @param array $args {
 *     @type string $desktop
 *     @type string $mobile
 *     @type string $alt
 *     @type string $variant card|hero|showcase
 *     @type bool   $mobile_is_fallback
 * }
 */
function weblazem_render_portfolio_device_mockup($args = array()) {
    $defaults = array(
        'desktop'            => '',
        'mobile'             => '',
        'alt'                => '',
        'variant'            => 'card',
        'mobile_is_fallback' => false,
    );

    $args = wp_parse_args($args, $defaults);

    if ($args['desktop'] === '' && $args['mobile'] === '') {
        $args['desktop'] = '';
        $args['mobile']  = '';
    }

    if ($args['mobile'] === '' && $args['desktop'] !== '') {
        $args['mobile']             = $args['desktop'];
        $args['mobile_is_fallback'] = true;
    }

    $variant = $args['variant'];
    if (!in_array($variant, array('card', 'hero', 'showcase'), true)) {
        $variant = 'card';
    }
    $args['variant'] = $variant;

    $template = get_template_directory() . '/template-parts/components/portfolio-device-mockup.php';

    if (!file_exists($template)) {
        return;
    }

    $desktop            = (string) $args['desktop'];
    $mobile             = (string) $args['mobile'];
    $alt                = (string) $args['alt'];
    $mobile_is_fallback = (bool) $args['mobile_is_fallback'];

    include $template;
}

/**
 * Render a portfolio card with explicit template variables.
 *
 * @param array $args {
 *     @type string $card_title
 *     @type string $card_description
 *     @type string $project_link
 *     @type string $card_btn_text
 *     @type bool   $external
 *     @type string $heading_tag h2|h3|h4
 *     @type string $variant homepage|archive
 * }
 */
function weblazem_render_portfolio_card($args = array()) {
    $post_id = get_the_ID();

    $defaults = array(
        'card_title'       => $post_id ? weblazem_get_portfolio_card_title($post_id) : '',
        'card_description' => $post_id ? weblazem_get_portfolio_card_description($post_id) : '',
        'project_link'     => $post_id ? weblazem_get_portfolio_project_link($post_id) : '#',
        'card_btn_text'    => get_option('weblazem_portfolio_card_button_text', 'مشاهده‌ی پروژه'),
        'external'         => $post_id ? (bool) get_post_meta($post_id, '_weblazem_portfolio_project_url', true) : false,
        'heading_tag'      => 'h3',
        'variant'          => 'homepage',
    );

    $args = wp_parse_args($args, $defaults);

    $card_title       = (string) $args['card_title'];
    $card_description = (string) $args['card_description'];
    $project_link     = (string) $args['project_link'];
    $card_btn_text    = (string) $args['card_btn_text'];
    $external         = (bool) $args['external'];
    $heading_tag      = (string) $args['heading_tag'];
    $variant          = $args['variant'] === 'archive' ? 'archive' : 'homepage';

    $template_name = $variant === 'archive' ? 'portfolio-card-archive' : 'portfolio-card';
    $template      = get_template_directory() . '/template-parts/components/' . $template_name . '.php';

    if (!file_exists($template)) {
        return;
    }

    include $template;
}
