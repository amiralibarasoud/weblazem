<?php
/**
 * SEO page layout.
 */

$map = array(
    'hero'       => array('part' => 'hero'),
    'clients'    => array('part' => 'clients'),
    'splits'     => array('component' => 'splits', 'args' => array('prefix' => 'seo', 'image_dir' => 'seo')),
    'process'    => array('component' => 'process', 'args' => array('prefix' => 'seo', 'wave' => true)),
    'advantages' => array('component' => 'advantages', 'args' => array('prefix' => 'seo', 'wave_top' => true)),
    'tariffs'    => array('part' => 'tariffs'),
    'faq'        => array('component' => 'faq', 'args' => array('prefix' => 'seo', 'id_prefix' => 'seo')),
);

foreach ($map as $key => $config) {
    if (!weblazem_is_seo_section_enabled($key)) {
        continue;
    }
    if (!empty($config['component'])) {
        get_template_part('template-parts/components/service', $config['component'], $config['args']);
    } else {
        get_template_part('template-parts/seo/section', $config['part']);
    }
}
