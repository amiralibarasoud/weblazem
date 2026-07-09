<?php
/**
 * Contact page — full layout.
 */

$title    = weblazem_contact_option('page_title', 'ارتباط با ما');
$address  = weblazem_contact_option('address');
$phone    = weblazem_contact_option('phone');
$email    = weblazem_contact_option('email');
$illus    = weblazem_contact_option('illustration', '');
if (!$illus) {
    $illus = get_template_directory_uri() . '/assets/images/contact/envelope.svg';
}

$socials = array(
    array('url' => weblazem_contact_option('social_twitter'), 'icon' => 'fa-brands fa-x-twitter', 'label' => 'X'),
    array('url' => weblazem_contact_option('social_instagram'), 'icon' => 'fa-brands fa-instagram', 'label' => 'Instagram'),
    array('url' => weblazem_contact_option('social_linkedin'), 'icon' => 'fa-brands fa-linkedin-in', 'label' => 'LinkedIn'),
    array('url' => weblazem_contact_option('social_telegram'), 'icon' => 'fa-brands fa-telegram', 'label' => 'Telegram'),
);
?>

<section class="contact-page">
    <div class="contact-page__topbar" aria-hidden="true"></div>

    <div class="container contact-page__hero">
        <div class="contact-page__blob" aria-hidden="true"></div>

        <div class="contact-page__hero-grid">
            <div class="contact-page__intro">
                <h1 class="contact-page__title"><?php echo esc_html($title); ?></h1>
                <?php if ($address) : ?>
                    <p class="contact-page__address"><?php echo esc_html($address); ?></p>
                <?php endif; ?>

                <div class="contact-page__social">
                    <?php foreach ($socials as $social) :
                        if (empty($social['url'])) {
                            continue;
                        }
                        ?>
                        <a href="<?php echo esc_url($social['url']); ?>" class="contact-page__social-link" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr($social['label']); ?>">
                            <i class="<?php echo esc_attr($social['icon']); ?>" aria-hidden="true"></i>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="contact-page__info">
                <div class="contact-page__illus-wrap">
                    <img src="<?php echo esc_url($illus); ?>" alt="" class="contact-page__illus" />
                    <div class="contact-page__quick">
                        <?php if ($phone) : ?>
                            <a href="tel:<?php echo esc_attr(preg_replace('/\D+/', '', $phone)); ?>" class="contact-page__quick-item">
                                <i class="fas fa-phone" aria-hidden="true"></i>
                                <span><?php echo esc_html($phone); ?></span>
                            </a>
                        <?php endif; ?>
                        <?php if ($email) : ?>
                            <a href="mailto:<?php echo esc_attr($email); ?>" class="contact-page__quick-item">
                                <i class="fas fa-envelope" aria-hidden="true"></i>
                                <span><?php echo esc_html($email); ?></span>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container contact-page__form-wrap">
        <form class="contact-page__form" id="weblazem-contact-form" novalidate>
            <div class="contact-page__form-row contact-page__form-row--2">
                <div class="contact-page__field">
                    <label for="contact-first-name"><?php echo esc_html(weblazem_contact_option('label_first_name', 'نام')); ?><span class="required">*</span></label>
                    <input type="text" id="contact-first-name" name="first_name" required autocomplete="given-name" />
                </div>
                <div class="contact-page__field">
                    <label for="contact-last-name"><?php echo esc_html(weblazem_contact_option('label_last_name', 'نام خانوادگی')); ?></label>
                    <input type="text" id="contact-last-name" name="last_name" autocomplete="family-name" />
                </div>
            </div>

            <div class="contact-page__form-row contact-page__form-row--2">
                <div class="contact-page__field">
                    <label for="contact-email"><?php echo esc_html(weblazem_contact_option('label_email', 'ایمیل')); ?><span class="required">*</span></label>
                    <input type="email" id="contact-email" name="email" required autocomplete="email" dir="ltr" />
                </div>
                <div class="contact-page__field">
                    <label for="contact-phone"><?php echo esc_html(weblazem_contact_option('label_phone', 'شماره‌ی تماس')); ?><span class="required">*</span></label>
                    <input type="tel" id="contact-phone" name="phone" required autocomplete="tel" dir="ltr" placeholder="09121234567" />
                </div>
            </div>

            <div class="contact-page__field contact-page__field--full">
                <label for="contact-message"><?php echo esc_html(weblazem_contact_option('label_message', 'ثبت پیام')); ?><span class="required">*</span></label>
                <textarea id="contact-message" name="message" rows="6" required></textarea>
            </div>

            <div class="contact-page__form-actions">
                <p class="contact-page__form-status" id="contact-form-status" role="status" aria-live="polite"></p>
                <button type="submit" class="contact-page__submit" id="contact-form-submit">
                    <?php echo esc_html(weblazem_contact_option('submit_text', 'ثبت پیام')); ?>
                </button>
            </div>
        </form>
    </div>
</section>
