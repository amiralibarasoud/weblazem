<?php
/**
 * Content production & support page layout.
 */

$map = array(
    'hero'       => array('part' => 'hero'),
    'portfolio'  => array('part' => 'portfolio'),
    'customers'  => array('part' => 'customers'),
    'splits'     => array('component' => 'splits', 'args' => array('prefix' => 'contentsupport', 'image_dir' => 'contentsupport')),
    'process'    => array('component' => 'process', 'args' => array('prefix' => 'contentsupport', 'wave' => false)),
    'advantages' => array('component' => 'advantages', 'args' => array('prefix' => 'contentsupport', 'wave_top' => false)),
    'faq'        => array('component' => 'faq', 'args' => array('prefix' => 'contentsupport', 'id_prefix' => 'contentsupport')),
);

foreach ($map as $key => $config) {
    if (!weblazem_is_contentsupport_section_enabled($key)) {
        continue;
    }
    if (!empty($config['component'])) {
        get_template_part('template-parts/components/service', $config['component'], $config['args']);
    } else {
        get_template_part('template-parts/contentsupport/section', $config['part']);
    }
}
