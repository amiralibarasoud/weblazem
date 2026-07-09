<?php
/**
 * Pricing page layout.
 */

$map = array(
    'hero'            => array('part' => 'hero'),
    'categories'      => array('part' => 'categories'),
    'service_tariffs' => array('part' => 'service-tariffs'),
    'webdesign_plans' => array('part' => 'webdesign-plans'),
    'consult'         => array('part' => 'consult'),
);

foreach ($map as $key => $config) {
    if (!weblazem_is_pricing_section_enabled($key)) {
        continue;
    }
    get_template_part('template-parts/pricing/section', $config['part']);
}
