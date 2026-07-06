<?php
/**
 * Blog archive layout.
 */

$sections = array(
    'hero'  => 'hero',
    'posts' => 'posts',
);

foreach ($sections as $key => $part) {
    if (!weblazem_is_blogarchive_section_enabled($key)) {
        continue;
    }
    get_template_part('template-parts/blog-archive/section', $part);
}
