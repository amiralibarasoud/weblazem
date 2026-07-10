<?php
/**
 * About Us — team section.
 */

$text       = weblazem_aboutus_option('team_text', '');
$btn_text   = weblazem_aboutus_option('team_btn_text', 'معرفی تیم');
$btn_url    = weblazem_aboutus_option('team_btn_url', '');
$btn_modal  = weblazem_aboutus_option('team_btn_modal', '0') === '1';
$members    = weblazem_get_aboutus_team_members();
?>

<section class="aboutus-team" dir="rtl">
    <?php get_template_part('template-parts/aboutus/part', 'wave', array('variant' => 'dark-fill', 'position' => 'top')); ?>

    <div class="aboutus-team__inner">
        <div class="container">
            <div class="aboutus-team__grid">
                <div class="aboutus-team__content">
                    <div class="aboutus-team__calligraphy">
                        <?php weblazem_render_service_calligraphy('aboutus', 'team_calligraphy_image', 'team_calligraphy_text'); ?>
                    </div>

                    <?php if (!empty($text)) : ?>
                        <div class="aboutus-team__text"><?php echo wp_kses_post(wpautop($text)); ?></div>
                    <?php endif; ?>

                    <?php if (!empty($btn_text)) : ?>
                        <?php if ($btn_modal) : ?>
                            <button type="button" class="aboutus-team__btn weblazem-consult-trigger">
                                <?php echo esc_html($btn_text); ?>
                            </button>
                        <?php else : ?>
                            <a href="<?php echo esc_url(!empty($btn_url) ? $btn_url : '#'); ?>"
                               class="aboutus-team__btn">
                                <?php echo esc_html($btn_text); ?>
                            </a>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>

                <?php if (!empty($members)) : ?>
                    <div class="aboutus-team__collage" aria-hidden="true">
                        <?php foreach ($members as $member) :
                            if (empty($member['image'])) {
                                continue;
                            }
                            $size = !empty($member['size']) ? $member['size'] : 'sm';
                            ?>
                            <div class="aboutus-team__member aboutus-team__member--<?php echo esc_attr($size); ?>">
                                <div class="aboutus-team__member-frame">
                                    <img src="<?php echo esc_url($member['image']); ?>"
                                         alt="<?php echo esc_attr($member['alt'] ?? ''); ?>"
                                         loading="lazy" />
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php get_template_part('template-parts/aboutus/part', 'wave', array('variant' => 'dark-to-light', 'position' => 'bottom')); ?>
</section>
