<?php
/**
 * Homepage Testimonials section.
 */

$title        = get_option('weblazem_testimonials_title', '');
$rating_label = get_option('weblazem_testimonials_rating_label', '');
$rating_score = get_option('weblazem_testimonials_rating_score', '');
$rating_value = (int) get_option('weblazem_testimonials_rating_value', 5);
$items        = get_option('weblazem_testimonials_items', array());

if (!is_array($items)) {
    $items = array();
}

$items = array_values(array_filter($items, function ($item) {
    return !empty($item['name']) || !empty($item['text']);
}));

if (empty($title) && empty($items)) {
    return;
}
?>

<section class="weblazem-testimonials-section" dir="rtl">
    <div class="testimonials-section-inner">
        <div class="container">
            <div class="testimonials-header">
                <?php if (!empty($title)) : ?>
                    <h2 class="testimonials-section-title"><?php echo esc_html($title); ?></h2>
                <?php endif; ?>

                <?php if (!empty($rating_label) || !empty($rating_score)) : ?>
                    <div class="testimonials-summary">
                        <?php if (!empty($rating_label)) : ?>
                            <span class="testimonials-summary-label"><?php echo esc_html($rating_label); ?></span>
                        <?php endif; ?>

                        <?php if (!empty($rating_score)) : ?>
                            <span class="testimonials-summary-score"><?php echo esc_html($rating_score); ?></span>
                        <?php endif; ?>

                        <?php weblazem_render_star_rating($rating_value, 'testimonials-summary-stars'); ?>
                    </div>
                <?php endif; ?>
            </div>

            <?php if (!empty($items)) : ?>
                <div class="weblazem-carousel testimonials-carousel" data-weblazem-carousel data-autoplay="4500">
                    <button type="button"
                            class="testimonials-carousel-btn"
                            data-carousel-prev
                            aria-label="نظر قبلی">
                        <i class="fas fa-chevron-right" aria-hidden="true"></i>
                    </button>

                    <div class="weblazem-carousel__viewport" data-carousel-viewport>
                        <div class="weblazem-carousel__track" data-carousel-track>
                            <?php foreach ($items as $item) : ?>
                                <div class="weblazem-carousel__slide testimonial-slide">
                                    <article class="testimonial-card">
                                        <header class="testimonial-card-header">
                                            <?php if (!empty($item['avatar'])) : ?>
                                                <img src="<?php echo esc_url($item['avatar']); ?>"
                                                     alt="<?php echo esc_attr($item['name']); ?>"
                                                     class="testimonial-avatar"
                                                     loading="lazy"
                                                     draggable="false" />
                                            <?php else : ?>
                                                <span class="testimonial-avatar testimonial-avatar--placeholder">
                                                    <i class="fas fa-user" aria-hidden="true"></i>
                                                </span>
                                            <?php endif; ?>

                                            <?php if (!empty($item['name'])) : ?>
                                                <h3 class="testimonial-name"><?php echo esc_html($item['name']); ?></h3>
                                            <?php endif; ?>
                                        </header>

                                        <?php if (!empty($item['text'])) : ?>
                                            <p class="testimonial-text"><?php echo esc_html($item['text']); ?></p>
                                        <?php endif; ?>

                                        <?php weblazem_render_star_rating(isset($item['rating']) ? (int) $item['rating'] : 5, 'testimonial-card-stars'); ?>
                                    </article>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <button type="button"
                            class="testimonials-carousel-btn"
                            data-carousel-next
                            aria-label="نظر بعدی">
                        <i class="fas fa-chevron-left" aria-hidden="true"></i>
                    </button>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
