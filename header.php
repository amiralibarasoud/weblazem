<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title><?php bloginfo('name'); ?></title>

    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>

<!--preloadin-->

<div id="preloader" class="fixed inset-0 z-50 flex items-center justify-center bg-white">
    <div class="w-16 h-16 border-t-4 border-blue-600 border-opacity-50 rounded-full animate-spin"></div>
</div>

<!--header-->

<header class="sticky top-0 z-50 w-full pt-[20px]" style="background-color: #4f1c61;">
    <div class="container px-4 mx-auto">
        <div class="flex items-center justify-between h-24" dir="rtl">
            <!-- لوگو - سمت راست -->
            <div class="flex items-center">
                <?php 
                // Check for custom logo from theme options first
                $custom_logo = get_option('weblazem_logo');
                
                if (!empty($custom_logo)) : ?>
                    <a href="<?php echo esc_url(home_url('/')); ?>" class="block">
                        <img src="<?php echo esc_url($custom_logo); ?>" alt="<?php bloginfo('name'); ?>" class="w-auto h-12 max-w-[250px] max-h-[280px]" style="max-width:280px; max-height:120px;">
                    </a>
                <?php elseif (has_custom_logo()) : ?>
                    <?php the_custom_logo(); ?>
                <?php else : ?>
                    <a href="<?php echo esc_url(home_url('/')); ?>" class="text-3xl font-bold text-blue-600 transition-all hover:text-blue-700">
                        وب لازم
                    </a>
                <?php endif; ?>
            </div>

            <!-- منو - وسط -->
            <nav class="flex items-center justify-center">
                <?php
                wp_nav_menu([
                    'theme_location' => 'main_menu',
                    'container' => false,
                    'menu_class' => 'flex items-center text-white space-x-[30px] space-x-reverse font-medium',
                    'add_li_class' => 'px-5',
                    'link_class' => 'px-[20px] py-3 block hover:text-yellow-400 transition-colors relative after:absolute after:bottom-0 after:right-0 after:w-0 after:h-0.5 after:bg-blue-500 hover:after:w-full after:transition-all',
                ]);
                ?>
            </nav>

            <!-- شماره تماس - سمت چپ -->
            <div class="flex items-center">
                <div class="flex items-center px-5 py-3 text-white transition-all rounded-full shadow-sm bg-transparent gap-0">
                    <i class="ml-3 text-white fa-solid fa-phone animate-pulse hover:text-[#F2DF33]"></i>
                    <?php 
                    $phone_number = get_option('weblazem_phone_number');
                    $phone_display = !empty($phone_number) ? esc_html($phone_number) : '09129512697';
                    $phone_link = 'tel:' . preg_replace('/[^0-9+]/', '', $phone_display);
                    ?>
                    <a href="<?php echo esc_attr($phone_link); ?>" class="text-sm text-white font-medium hover:underline hover:text-[#F2DF33]">
                        مشاوره رایگان: <?php echo $phone_display; ?>
                    </a>
                </div>
            </div>
        </div>
    </div>
</header>

<script>
    // Remove preloader when page loads
    window.addEventListener('load', function() {
        const preloader = document.getElementById('preloader');
        preloader.style.display = 'none';
    });
</script>
