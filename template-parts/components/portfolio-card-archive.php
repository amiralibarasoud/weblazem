<?php
/**
 * Portfolio card — archive page variant (Figma design).
 *
 * @var string $card_title
 * @var string $card_description
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

$card_description = isset($card_description) ? $card_description : '';
?>

<article <?php post_class('portfolio-page-card'); ?>>
    <div class="portfolio-page-card__media">
        <div class="portfolio-page-card__image-frame">
            <?php if (has_post_thumbnail()) : ?>
                <?php the_post_thumbnail('large', array(
                    'class' => 'portfolio-page-card__image',
                    'alt'   => esc_attr($card_title),
                )); ?>
            <?php else : ?>
                <div class="portfolio-page-card__image portfolio-page-card__image--placeholder">
                    <i class="fas fa-laptop" aria-hidden="true"></i>
                </div>
            <?php endif; ?>
            <span class="portfolio-page-card__accent" aria-hidden="true"></span>
        </div>
    </div>

    <div class="portfolio-page-card__body">
        <<?php echo $heading_tag; ?> class="portfolio-page-card__title"><?php echo esc_html($card_title); ?></<?php echo $heading_tag; ?>>

        <?php if (!empty($card_description)) : ?>
            <p class="portfolio-page-card__description"><?php echo esc_html($card_description); ?></p>
        <?php endif; ?>

        <a href="<?php echo esc_url($project_link); ?>"
           class="portfolio-page-card__button"
           <?php echo $external ? 'target="_blank" rel="noopener noreferrer"' : ''; ?>>
            <?php echo esc_html($card_btn_text); ?>
        </a>
    </div>
</article>
