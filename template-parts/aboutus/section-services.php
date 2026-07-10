<?php
/**
 * About Us — service cards (same markup as SEO / devproject).
 */

$cards = weblazem_get_aboutus_service_cards();

if (empty($cards)) {
    return;
}
?>

<section class="webdesign-faq aboutus-service-cards" dir="rtl">
    <?php get_template_part('template-parts/aboutus/part', 'wave', array('variant' => 'light-fill', 'position' => 'top')); ?>

    <div class="container">
        <?php get_template_part('template-parts/components/service', 'cards', array('cards' => $cards)); ?>
    </div>
</section>
