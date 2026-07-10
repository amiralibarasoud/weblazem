<?php
/**
 * About Us page — main layout.
 */

$sections = array(
    'hero'     => 'section-hero',
    'journey'  => 'section-journey',
    'ceo'      => 'section-ceo',
    'team'     => 'section-team',
    'services' => 'section-services',
);

foreach ($sections as $key => $template) {
    if (!weblazem_is_aboutus_section_enabled($key)) {
        continue;
    }
    get_template_part('template-parts/aboutus/' . $template);
}
