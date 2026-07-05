<?php
/**
 * Website design page layout.
 */

$sections = array(
    'hero'       => 'hero',
    'portfolio'  => 'portfolio',
    'customers'  => 'customers',
    'splits'     => 'splits',
    'process'    => 'process',
    'advantages' => 'advantages',
    'faq'        => 'faq',
);

foreach ($sections as $key => $part) {
    if (!weblazem_is_webdesign_section_enabled($key)) {
        continue;
    }
    get_template_part('template-parts/webdesign/section', $part);
}
