<?php
/**
 * Animated wave separator between About Us sections.
 *
 * @var array $args {
 *     @type string $variant  light-to-dark|dark-to-light|dark-fill|light-fill
 *     @type string $position top|bottom
 * }
 */

$args = wp_parse_args($args ?? array(), array(
    'variant'  => 'light-to-dark',
    'position' => 'bottom',
));

$variant  = $args['variant'];
$position = $args['position'];
$uid      = 'aboutusWave' . substr(md5($variant . $position . wp_rand()), 0, 8);

$classes = array(
    'aboutus-wave',
    'aboutus-wave--' . esc_attr($position),
    'aboutus-wave--' . esc_attr($variant),
);
?>

<div class="<?php echo esc_attr(implode(' ', $classes)); ?>" aria-hidden="true">
    <svg class="aboutus-wave__svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 120" preserveAspectRatio="none">
        <?php if ($variant === 'light-to-dark' || $variant === 'dark-fill') : ?>
            <path class="aboutus-wave__fill" fill="#05052d" d="M0,64 C240,120 480,0 720,64 C960,128 1200,32 1440,72 L1440,120 L0,120 Z"/>
            <path class="aboutus-wave__stroke aboutus-wave__stroke--animated" fill="none" stroke="url(#<?php echo esc_attr($uid); ?>Grad)" stroke-width="3"
                  d="M0,58 C240,108 480,8 720,58 C960,108 1200,18 1440,58"/>
        <?php elseif ($variant === 'dark-to-light' || $variant === 'light-fill') : ?>
            <path class="aboutus-wave__fill" fill="#ffffff" d="M0,56 C240,0 480,120 720,56 C960,0 1200,96 1440,48 L1440,0 L0,0 Z"/>
            <path class="aboutus-wave__stroke aboutus-wave__stroke--animated" fill="none" stroke="url(#<?php echo esc_attr($uid); ?>Grad)" stroke-width="3"
                  d="M0,62 C240,12 480,112 720,62 C960,12 1200,102 1440,52"/>
        <?php endif; ?>
        <defs>
            <linearGradient id="<?php echo esc_attr($uid); ?>Grad" x1="0%" y1="0%" x2="100%" y2="0%">
                <stop offset="0%" stop-color="#e879f9"/>
                <stop offset="50%" stop-color="#a855f7"/>
                <stop offset="100%" stop-color="#38bdf8"/>
            </linearGradient>
        </defs>
    </svg>
</div>
