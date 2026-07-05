<?php
/**
 * Website design — FAQ + contact profile + service cards.
 */

$faq_subtitle = weblazem_webdesign_option('faq_subtitle', 'پرسش‌های پرتکرار');
$faq_intro    = weblazem_webdesign_option('faq_intro', '');
$faq_items    = get_option('weblazem_webdesign_faq_items', array());
$profile_img  = weblazem_webdesign_option('faq_profile_image', '');
$phone        = weblazem_webdesign_option('faq_phone', get_option('weblazem_phone', '021 78358'));
$consult_text = weblazem_webdesign_option('faq_consult_btn_text', 'ثبت درخواست مشاوره');
$footer_text  = weblazem_webdesign_option('faq_footer_text', '');
$cards        = get_option('weblazem_webdesign_service_cards', array());

if (!is_array($faq_items)) {
    $faq_items = array();
}
if (!is_array($cards)) {
    $cards = array();
}

$faq_items = array_values(array_filter($faq_items, function ($item) {
    return !empty($item['question']);
}));
$cards = array_values(array_filter($cards, function ($card) {
    return !empty($card['title']);
}));
?>

<section class="webdesign-faq" dir="rtl">
    <div class="container">
        <div class="webdesign-faq__top">
            <div class="webdesign-faq__questions">
                <div class="webdesign-faq__calligraphy">
                    <?php weblazem_render_webdesign_calligraphy('faq_calligraphy_image', 'faq_calligraphy_text'); ?>
                </div>

                <?php if (!empty($faq_subtitle)) : ?>
                    <h2 class="webdesign-faq__subtitle"><?php echo esc_html($faq_subtitle); ?></h2>
                <?php endif; ?>

                <?php if (!empty($faq_intro)) : ?>
                    <p class="webdesign-faq__intro"><?php echo esc_html($faq_intro); ?></p>
                <?php endif; ?>

                <?php if (!empty($faq_items)) : ?>
                    <div class="webdesign-faq__list" data-faq-accordion>
                        <?php foreach ($faq_items as $index => $item) :
                            $panel_id  = 'webdesign-faq-panel-' . (int) $index;
                            $trigger_id = 'webdesign-faq-trigger-' . (int) $index;
                            ?>
                            <div class="faq-item webdesign-faq__item">
                                <button type="button"
                                        class="faq-item__trigger webdesign-faq__trigger"
                                        id="<?php echo esc_attr($trigger_id); ?>"
                                        aria-expanded="false"
                                        aria-controls="<?php echo esc_attr($panel_id); ?>">
                                    <span class="faq-item__question"><?php echo esc_html($item['question']); ?></span>
                                    <span class="faq-item__icon" aria-hidden="true">
                                        <i class="fas fa-chevron-down"></i>
                                    </span>
                                </button>

                                <div class="faq-item__panel"
                                     id="<?php echo esc_attr($panel_id); ?>"
                                     role="region"
                                     aria-labelledby="<?php echo esc_attr($trigger_id); ?>"
                                     hidden>
                                    <?php if (!empty($item['answer'])) : ?>
                                        <div class="faq-item__answer"><?php echo wp_kses_post(wpautop($item['answer'])); ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <aside class="webdesign-faq__profile">
                <div class="webdesign-faq__profile-card">
                    <?php if (!empty($profile_img)) : ?>
                        <div class="webdesign-faq__profile-image">
                            <img src="<?php echo esc_url($profile_img); ?>" alt="" />
                        </div>
                    <?php else : ?>
                        <div class="webdesign-faq__profile-image webdesign-faq__profile-image--placeholder">
                            <i class="fas fa-user-tie" aria-hidden="true"></i>
                        </div>
                    <?php endif; ?>

                    <div class="webdesign-faq__profile-actions">
                        <?php if (!empty($phone)) : ?>
                            <a href="tel:<?php echo esc_attr(preg_replace('/\s+/', '', $phone)); ?>"
                               class="webdesign-faq__phone-btn">
                                <i class="fas fa-phone" aria-hidden="true"></i>
                                <?php echo esc_html($phone); ?>
                            </a>
                        <?php endif; ?>

                        <?php if (!empty($consult_text)) : ?>
                            <button type="button" class="webdesign-faq__consult-btn weblazem-consult-trigger">
                                <?php echo esc_html($consult_text); ?>
                            </button>
                        <?php endif; ?>
                    </div>

                    <?php if (!empty($footer_text)) : ?>
                        <p class="webdesign-faq__profile-footer"><?php echo esc_html($footer_text); ?></p>
                    <?php endif; ?>
                </div>
            </aside>
        </div>

        <?php if (!empty($cards)) : ?>
            <div class="webdesign-faq__cards">
                <?php foreach ($cards as $card) : ?>
                    <a href="<?php echo esc_url(!empty($card['url']) ? $card['url'] : '#'); ?>"
                       class="webdesign-service-card"
                       <?php echo !empty($card['url']) && $card['url'] !== '#' ? '' : 'onclick="return false;"'; ?>>
                        <div class="webdesign-service-card__shape" aria-hidden="true">
                            <?php if (!empty($card['shape_image'])) : ?>
                                <img src="<?php echo esc_url($card['shape_image']); ?>" alt="" />
                            <?php else : ?>
                                <span class="webdesign-service-card__shape-default"></span>
                            <?php endif; ?>
                        </div>

                        <div class="webdesign-service-card__content">
                            <?php if (!empty($card['title'])) : ?>
                                <h3 class="webdesign-service-card__title"><?php echo esc_html($card['title']); ?></h3>
                            <?php endif; ?>

                            <?php if (!empty($card['en_title'])) : ?>
                                <span class="webdesign-service-card__en"><?php echo esc_html($card['en_title']); ?></span>
                            <?php endif; ?>

                            <?php if (!empty($card['description'])) : ?>
                                <p class="webdesign-service-card__desc"><?php echo esc_html($card['description']); ?></p>
                            <?php endif; ?>
                        </div>

                        <span class="webdesign-service-card__arrow" aria-hidden="true">
                            <i class="fas fa-arrow-left"></i>
                        </span>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
