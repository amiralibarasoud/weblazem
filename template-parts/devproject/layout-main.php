<?php
/**
 * Custom development page layout.
 */

$map = array(
    'hero'       => array('part' => 'hero'),
    'portfolio'  => array('part' => 'portfolio'),
    'customers'  => array('part' => 'customers'),
    'splits'     => array('component' => 'splits', 'args' => array('prefix' => 'devproject', 'image_dir' => 'devproject')),
    'process'    => array('component' => 'process', 'args' => array('prefix' => 'devproject', 'wave' => false)),
    'advantages' => array('component' => 'advantages', 'args' => array('prefix' => 'devproject', 'wave_top' => false)),
    'faq'        => array('component' => 'faq', 'args' => array('prefix' => 'devproject', 'id_prefix' => 'devproject')),
);

foreach ($map as $key => $config) {
    if (!weblazem_is_devproject_section_enabled($key)) {
        continue;
    }
    if (!empty($config['component'])) {
        get_template_part('template-parts/components/service', $config['component'], $config['args']);
    } else {
        get_template_part('template-parts/devproject/section', $config['part']);
    }
}
