<?php
/**
 * Portfolio card markup (homepage + archive).
 *
 * Variables are set by weblazem_render_portfolio_card() before include.
 *
 * @var string $card_title
 * @var string $project_link
 * @var string $card_btn_text
 * @var bool   $external
 * @var string $heading_tag
 */

if (!isset($card_title, $project_link, $card_btn_text, $external, $heading_tag)) {
    return;
}

$allowed_headings = array('h2', 'h3', 'h4');
if (!in_array($heading_tag, $allowed_headings, true)) {
    $heading_tag = 'h3';
}

$devices = function_exists('weblazem_get_portfolio_device_images')
    ? weblazem_get_portfolio_device_images(get_the_ID())
    : array('desktop' => '', 'mobile' => '', 'mobile_is_fallback' => false);
?>

<article <?php post_class('portfolio-card'); ?>>
    <div class="portfolio-card-media">
        <?php
        if (function_exists('weblazem_render_portfolio_device_mockup')) {
            weblazem_render_portfolio_device_mockup(array(
                'desktop'            => $devices['desktop'],
                'mobile'             => $devices['mobile'],
                'alt'                => $card_title,
                'variant'            => 'card',
                'mobile_is_fallback' => !empty($devices['mobile_is_fallback']),
            ));
        }
        ?>

        <svg class="portfolio-card-wave" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 72" preserveAspectRatio="none" aria-hidden="true">
            <path fill="#4F1E60" d="M0,72 L0,14 C38,46 92,10 150,28 C205,44 252,34 320,48 L320,72 Z"/>
        </svg>
    </div>

    <div class="portfolio-card-body">
        <<?php echo $heading_tag; ?> class="portfolio-card-title"><?php echo esc_html($card_title); ?></<?php echo $heading_tag; ?>>
        <a href="<?php echo esc_url($project_link); ?>"
           class="portfolio-card-button"
           <?php echo $external ? 'target="_blank" rel="noopener noreferrer"' : ''; ?>>
            <?php echo esc_html($card_btn_text); ?>
        </a>
    </div>
</article>
