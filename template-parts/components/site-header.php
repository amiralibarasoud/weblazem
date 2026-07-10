<?php
/**
 * Site header — responsive layout + mobile drawer navigation.
 */
?>
<header class="weblazem-site-header" id="weblazem-site-header">
    <div class="weblazem-header__container">
        <div class="weblazem-header__bar" dir="rtl">
            <div class="weblazem-header__brand">
                <?php
                $custom_logo = get_option('weblazem_logo');

                if (!empty($custom_logo)) :
                    ?>
                    <a href="<?php echo esc_url(home_url('/')); ?>" class="weblazem-header__logo-link">
                        <img src="<?php echo esc_url($custom_logo); ?>" alt="<?php bloginfo('name'); ?>" class="weblazem-header__logo" />
                    </a>
                <?php elseif (has_custom_logo()) : ?>
                    <?php the_custom_logo(); ?>
                <?php else : ?>
                    <a href="<?php echo esc_url(home_url('/')); ?>" class="weblazem-header__logo-text">
                        وب لازم
                    </a>
                <?php endif; ?>
            </div>

            <nav class="weblazem-header-nav weblazem-header-nav--desktop" aria-label="منوی اصلی">
                <?php
                wp_nav_menu(array(
                    'theme_location' => 'main_menu',
                    'container'      => false,
                    'menu_class'     => 'weblazem-header-menu',
                    'fallback_cb'    => false,
                ));
                ?>
            </nav>

            <div class="weblazem-header__actions">
                <?php
                $header_consult_enabled = get_option('weblazem_header_consult_enabled', '1') === '1';
                $consult_system_enabled = function_exists('weblazem_get_consult_option')
                    && weblazem_get_consult_option('weblazem_consult_section_enabled', '1') === '1';
                $header_btn_text        = '';

                if ($header_consult_enabled && $consult_system_enabled) :
                    $header_btn_text = get_option('weblazem_header_consult_btn_text', '');
                    if (empty($header_btn_text) && function_exists('weblazem_get_consult_option')) {
                        $header_btn_text = weblazem_get_consult_option('weblazem_consult_btn_text', 'ثبت درخواست مشاوره');
                    }
                    if (empty($header_btn_text)) {
                        $header_btn_text = 'ثبت درخواست مشاوره';
                    }
                    ?>
                    <button type="button" class="weblazem-header-consult-btn weblazem-consult-trigger" aria-label="<?php echo esc_attr($header_btn_text); ?>">
                        <i class="fas fa-pen-to-square" aria-hidden="true"></i>
                        <span class="weblazem-header-consult-btn__label"><?php echo esc_html($header_btn_text); ?></span>
                    </button>
                <?php endif; ?>

                <button
                    type="button"
                    class="weblazem-header__toggle"
                    id="weblazem-header-toggle"
                    aria-expanded="false"
                    aria-controls="weblazem-header-drawer"
                    aria-label="باز و بسته کردن منو"
                >
                    <span class="weblazem-header__toggle-bar" aria-hidden="true"></span>
                    <span class="weblazem-header__toggle-bar" aria-hidden="true"></span>
                    <span class="weblazem-header__toggle-bar" aria-hidden="true"></span>
                </button>
            </div>
        </div>
    </div>

    <div class="weblazem-header__overlay" id="weblazem-header-overlay" hidden></div>

    <div class="weblazem-header__drawer" id="weblazem-header-drawer" aria-hidden="true" hidden>
        <nav class="weblazem-header-nav weblazem-header-nav--mobile" aria-label="منوی موبایل">
            <?php
            wp_nav_menu(array(
                'theme_location' => 'main_menu',
                'container'      => false,
                'menu_class'     => 'weblazem-header-menu weblazem-header-menu--mobile',
                'fallback_cb'    => false,
            ));
            ?>
        </nav>

        <?php if ($header_consult_enabled && $consult_system_enabled && !empty($header_btn_text)) : ?>
            <div class="weblazem-header__drawer-cta">
                <button type="button" class="weblazem-header__drawer-consult weblazem-consult-trigger">
                    <i class="fas fa-pen-to-square" aria-hidden="true"></i>
                    <?php echo esc_html($header_btn_text); ?>
                </button>
            </div>
        <?php endif; ?>
    </div>
</header>
