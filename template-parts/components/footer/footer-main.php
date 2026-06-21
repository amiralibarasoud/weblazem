
<?php

$logo = get_option('weblazem_footer_logo');

$desc = get_option('weblazem_footer_description');

$col1_title = get_option('weblazem_footer_col1_title');
$col1_items = explode("\n", get_option('weblazem_footer_col1_items'));

$col2_title = get_option('weblazem_footer_col2_title');
$col2_items = explode("\n", get_option('weblazem_footer_col2_items'));

$col3_title = get_option('weblazem_footer_col3_title');
$col3_items = explode("\n", get_option('weblazem_footer_col3_items'));

$instagram = get_option('weblazem_footer_instagram');
$linkedin  = get_option('weblazem_footer_linkedin');
$telegram  = get_option('weblazem_footer_telegram');

$copyright = get_option('weblazem_footer_copyright');

?>

<footer class="weblazem-footer">

    <div class="footer-top-blur"></div>

    <div class="container">

        <div class="footer-grid">

            <!-- برند -->
            <div class="footer-brand">

                <?php if($logo): ?>

                    <img
                        src="<?php echo esc_url($logo); ?>"
                        alt="Footer Logo"
                    >

                <?php endif; ?>

                <?php if($desc): ?>

                    <p>
                        <?php echo esc_html($desc); ?>
                    </p>

                <?php endif; ?>

                <div class="footer-socials">

                    <?php if($instagram): ?>
                        <a href="<?php echo esc_url($instagram); ?>">
                            <i class="fab fa-instagram"></i>
                        </a>
                    <?php endif; ?>

                    <?php if($linkedin): ?>
                        <a href="<?php echo esc_url($linkedin); ?>">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                    <?php endif; ?>

                    <?php if($telegram): ?>
                        <a href="<?php echo esc_url($telegram); ?>">
                            <i class="fab fa-telegram-plane"></i>
                        </a>
                    <?php endif; ?>

                </div>

            </div>

            <!-- ستون اول -->
            <div class="footer-links">

                <h3>
                    <?php echo esc_html($col1_title); ?>
                </h3>

                <?php foreach($col1_items as $item): ?>

                    <?php if(trim($item)): ?>

                        <a href="#">
                            <?php echo esc_html($item); ?>
                        </a>

                    <?php endif; ?>

                <?php endforeach; ?>

            </div>

            <!-- ستون دوم -->
            <div class="footer-links">

                <h3>
                    <?php echo esc_html($col2_title); ?>
                </h3>

                <?php foreach($col2_items as $item): ?>

                    <?php if(trim($item)): ?>

                        <a href="#">
                            <?php echo esc_html($item); ?>
                        </a>

                    <?php endif; ?>

                <?php endforeach; ?>

            </div>

            <!-- ستون سوم -->
            <div class="footer-links">

                <h3>
                    <?php echo esc_html($col3_title); ?>
                </h3>

                <?php foreach($col3_items as $item): ?>

                    <?php if(trim($item)): ?>

                        <a href="#">
                            <?php echo esc_html($item); ?>
                        </a>

                    <?php endif; ?>

                <?php endforeach; ?>

            </div>

        </div>

        <?php if($copyright): ?>

            <div class="footer-bottom">

                <?php echo esc_html($copyright); ?>

            </div>

        <?php endif; ?>

    </div>

</footer>
