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
                <p class="description">در صفحه اصلی زیر تصویر نمایش داده می‌شود. اگر خالی باشد، از عنوان پست استفاده می‌شود.</p>
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
 * Render a portfolio card with explicit template variables.
 *
 * @param array $args {
 *     @type string $card_title
 *     @type string $project_link
 *     @type string $card_btn_text
 *     @type bool   $external
 *     @type string $heading_tag h2|h3|h4
 * }
 */
function weblazem_render_portfolio_card($args = array()) {
    $post_id = get_the_ID();

    $defaults = array(
        'card_title'    => $post_id ? weblazem_get_portfolio_card_title($post_id) : '',
        'project_link'  => $post_id ? weblazem_get_portfolio_project_link($post_id) : '#',
        'card_btn_text' => get_option('weblazem_portfolio_card_button_text', 'مشاهده‌ی پروژه'),
        'external'      => $post_id ? (bool) get_post_meta($post_id, '_weblazem_portfolio_project_url', true) : false,
        'heading_tag'   => 'h3',
    );

    $args = wp_parse_args($args, $defaults);

    $card_title    = (string) $args['card_title'];
    $project_link  = (string) $args['project_link'];
    $card_btn_text = (string) $args['card_btn_text'];
    $external      = (bool) $args['external'];
    $heading_tag   = (string) $args['heading_tag'];

    $template = get_template_directory() . '/template-parts/components/portfolio-card.php';

    if (!file_exists($template)) {
        return;
    }

    include $template;
}
