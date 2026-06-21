<?php
/**
 * Homepage Customers section.
 */

$customers_title = get_option('weblazem_customers_title', 'مشتریان ما');
$customers_logos = get_option('weblazem_customers_logos', array());

if (!is_array($customers_logos)) {
    $customers_logos = array();
}

$customers_logos = array_values(array_filter($customers_logos, function ($logo) {
    return !empty($logo['logo']);
}));

if (empty($customers_title) && empty($customers_logos)) {
    return;
}

$wave_bg = get_template_directory_uri() . '/assets/images/customers/wave-bg.svg';
?>

<section class="weblazem-customers-section" dir="rtl">
    <div class="customers-wave-bg" style="background-image: url('<?php echo esc_url($wave_bg); ?>');" aria-hidden="true"></div>

    <div class="container">
        <?php if (!empty($customers_title)) : ?>
            <h2 class="customers-section-title"><?php echo esc_html($customers_title); ?></h2>
        <?php endif; ?>

        <?php if (!empty($customers_logos)) : ?>
            <div class="weblazem-carousel customers-carousel" data-weblazem-carousel data-autoplay="3500">
                <button type="button"
                        class="customers-carousel-btn"
                        data-carousel-prev
                        aria-label="لوگوی قبلی">
                    <i class="fas fa-chevron-right" aria-hidden="true"></i>
                </button>

                <div class="weblazem-carousel__viewport" data-carousel-viewport>
                    <div class="weblazem-carousel__track" data-carousel-track>
                        <?php foreach ($customers_logos as $logo) : ?>
                            <div class="weblazem-carousel__slide customer-logo-slide">
                                <?php if (!empty($logo['url'])) : ?>
                                    <a href="<?php echo esc_url($logo['url']); ?>"
                                       class="customer-logo-link"
                                       target="_blank"
                                       rel="noopener noreferrer"
                                       aria-label="<?php echo esc_attr($logo['name']); ?>">
                                <?php else : ?>
                                    <div class="customer-logo-link">
                                <?php endif; ?>
                                    <span class="customer-logo-circle">
                                        <img src="<?php echo esc_url($logo['logo']); ?>"
                                             alt="<?php echo esc_attr($logo['name']); ?>"
                                             class="customer-logo-image"
                                             loading="lazy"
                                             draggable="false" />
                                    </span>
                                <?php if (!empty($logo['url'])) : ?>
                                    </a>
                                <?php else : ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <button type="button"
                        class="customers-carousel-btn"
                        data-carousel-next
                        aria-label="لوگوی بعدی">
                    <i class="fas fa-chevron-left" aria-hidden="true"></i>
                </button>
            </div>
        <?php endif; ?>
    </div>
</section>
