<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title><?php bloginfo('name'); ?></title>

    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>

<div id="preloader" class="weblazem-preloader" aria-hidden="true">
    <div class="weblazem-preloader__spinner" role="status" aria-label="در حال بارگذاری"></div>
</div>

<?php get_template_part('template-parts/components/site', 'header'); ?>

<script>
    window.addEventListener('load', function() {
        var preloader = document.getElementById('preloader');
        if (preloader) {
            preloader.classList.add('is-hidden');
            window.setTimeout(function() {
                preloader.style.display = 'none';
            }, 500);
        }
    });
</script>
