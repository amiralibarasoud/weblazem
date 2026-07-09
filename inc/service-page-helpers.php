<?php
/**
 * Shared helpers for internal service pages (webdesign, seo, …).
 */

function weblazem_service_get_option($prefix, $key, $default = '') {
    $post_id = function_exists('weblazem_service_landing_get_context_id')
        ? weblazem_service_landing_get_context_id()
        : 0;

    if (
        $post_id
        && function_exists('weblazem_service_landing_get_storage')
        && function_exists('weblazem_service_landing_get_repeater_keys')
        && ($prefix === 'webdesign' || $prefix === 'service_landing')
    ) {
        $storage = weblazem_service_landing_get_storage($post_id);

        if (in_array($key, weblazem_service_landing_get_repeater_keys(), true)) {
            $repeaters = $storage['repeaters'][$key] ?? array();
            return !empty($repeaters) ? $repeaters : $default;
        }

        if (isset($storage['fields'][$key])) {
            return $storage['fields'][$key];
        }

        $defaults = weblazem_webdesign_defaults();
        return $defaults[$key] ?? $default;
    }

    return get_option('weblazem_' . $prefix . '_' . $key, $default);
}

function weblazem_service_option($prefix, $key, $default = '') {
    return weblazem_service_get_option($prefix, $key, $default);
}

function weblazem_render_service_calligraphy($prefix, $image_key, $text_key, $class = '') {
    $image = weblazem_service_option($prefix, $image_key, '');
    $text  = weblazem_service_option($prefix, $text_key, '');

    if (!empty($image)) {
        echo '<img src="' . esc_url($image) . '" alt="" class="webdesign-calligraphy-img ' . esc_attr($class) . '" />';
        return;
    }

    if (!empty($text)) {
        echo '<p class="webdesign-calligraphy-text ' . esc_attr($class) . '">' . wp_kses_post($text) . '</p>';
    }
}

function weblazem_get_service_advantage_icons() {
    return array(
        'clipboard' => 'fa-clipboard-list',
        'chart'     => 'fa-chart-line',
        'star'      => 'fa-star',
        'target'    => 'fa-bullseye',
        'shark'     => 'fa-water',
        'coffee'    => 'fa-mug-hot',
        'grid'      => 'fa-grip',
        'graph'     => 'fa-chart-area',
        'cube'      => 'fa-cube',
        'document'  => 'fa-file-lines',
        'heart'     => 'fa-heart',
        'headset'   => 'fa-headset',
        'layers'    => 'fa-layer-group',
        'rocket'    => 'fa-rocket',
        'nodes'     => 'fa-diagram-project',
    );
}
