<?php
/**
 * Homepage Team section.
 */

$team_title   = get_option('weblazem_team_title', '');
$team_members = get_option('weblazem_team_members', array());

if (!is_array($team_members)) {
    $team_members = array();
}

$team_members = array_values(array_filter($team_members, function ($member) {
    return !empty($member['name']);
}));

if (empty($team_title) && empty($team_members)) {
    return;
}
?>

<section class="weblazem-team-section" dir="rtl">
    <div class="container">
        <?php if (!empty($team_title)) : ?>
            <h2 class="team-section-title"><?php echo esc_html($team_title); ?></h2>
        <?php endif; ?>

        <?php if (!empty($team_members)) : ?>
            <div class="team-members">
                <?php foreach ($team_members as $index => $member) : ?>
                    <?php
                    // چیدمان آینه‌ای مطابق فیگما: عضو اول چپ، عضو دوم راست — عکس‌ها به سمت مرکز
                    $layout_class = ($index % 2 === 0) ? 'team-member--col-left' : 'team-member--col-right';
                    ?>
                    <article class="team-member <?php echo esc_attr($layout_class); ?>">
                        <div class="team-member-photo-wrap">
                            <?php if (!empty($member['image'])) : ?>
                                <img src="<?php echo esc_url($member['image']); ?>"
                                     alt="<?php echo esc_attr($member['name']); ?>"
                                     class="team-member-photo" />
                            <?php else : ?>
                                <div class="team-member-photo team-member-photo--placeholder">
                                    <i class="fas fa-user" aria-hidden="true"></i>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="team-member-content">
                            <h3 class="team-member-name"><?php echo esc_html($member['name']); ?></h3>
                            <?php if (!empty($member['role'])) : ?>
                                <div class="team-member-role"><?php echo esc_html($member['role']); ?></div>
                            <?php endif; ?>
                            <?php if (!empty($member['bio'])) : ?>
                                <div class="team-member-bio"><?php echo wp_kses_post(wpautop($member['bio'])); ?></div>
                            <?php endif; ?>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
