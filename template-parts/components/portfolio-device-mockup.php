<?php
/**
 * Monitor + phone device mockup for portfolio screenshots.
 *
 * @var string $desktop
 * @var string $mobile
 * @var string $alt
 * @var string $variant card|hero|showcase
 * @var bool   $mobile_is_fallback
 */

if (!isset($desktop, $mobile, $alt, $variant, $mobile_is_fallback)) {
    return;
}

$classes = array(
    'weblazem-device-mockup',
    'weblazem-device-mockup--' . $variant,
);

if ($desktop === '') {
    $classes[] = 'is-empty';
}

$phone_screen_class = 'weblazem-device-mockup__phone-screen';
if ($mobile_is_fallback) {
    $phone_screen_class .= ' is-fallback-crop';
}
?>

<div class="<?php echo esc_attr(implode(' ', $classes)); ?>" aria-hidden="<?php echo $desktop === '' ? 'true' : 'false'; ?>">
    <div class="weblazem-device-mockup__stage">
        <div class="weblazem-device-mockup__monitor">
            <div class="weblazem-device-mockup__monitor-body">
                <div class="weblazem-device-mockup__monitor-bezel">
                    <span class="weblazem-device-mockup__monitor-camera" aria-hidden="true"></span>
                    <div class="weblazem-device-mockup__monitor-screen">
                        <?php if ($desktop !== '') : ?>
                            <img
                                src="<?php echo esc_url($desktop); ?>"
                                alt="<?php echo esc_attr($alt); ?>"
                                loading="lazy"
                                decoding="async"
                            />
                        <?php else : ?>
                            <div class="weblazem-device-mockup__placeholder">
                                <i class="fas fa-desktop" aria-hidden="true"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="weblazem-device-mockup__monitor-chin" aria-hidden="true">
                    <span class="weblazem-device-mockup__monitor-logo-dot"></span>
                </div>
            </div>
            <div class="weblazem-device-mockup__monitor-stand" aria-hidden="true">
                <span class="weblazem-device-mockup__monitor-neck"></span>
                <span class="weblazem-device-mockup__monitor-base"></span>
            </div>
        </div>

        <div class="weblazem-device-mockup__phone">
            <div class="weblazem-device-mockup__phone-frame">
                <span class="weblazem-device-mockup__phone-island" aria-hidden="true"></span>
                <div class="<?php echo esc_attr($phone_screen_class); ?>">
                    <?php if ($mobile !== '') : ?>
                        <img
                            src="<?php echo esc_url($mobile); ?>"
                            alt="<?php echo esc_attr($alt !== '' ? $alt . ' — موبایل' : ''); ?>"
                            loading="lazy"
                            decoding="async"
                        />
                    <?php else : ?>
                            <div class="weblazem-device-mockup__placeholder weblazem-device-mockup__placeholder--phone">
                                <i class="fas fa-mobile-alt" aria-hidden="true"></i>
                            </div>
                    <?php endif; ?>
                </div>
                <span class="weblazem-device-mockup__phone-btn weblazem-device-mockup__phone-btn--silent" aria-hidden="true"></span>
                <span class="weblazem-device-mockup__phone-btn weblazem-device-mockup__phone-btn--vol-up" aria-hidden="true"></span>
                <span class="weblazem-device-mockup__phone-btn weblazem-device-mockup__phone-btn--vol-down" aria-hidden="true"></span>
                <span class="weblazem-device-mockup__phone-btn weblazem-device-mockup__phone-btn--power" aria-hidden="true"></span>
            </div>
        </div>
    </div>
</div>
