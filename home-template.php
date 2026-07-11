<?php
/**
 * Template Name: صفحه اصلی
 *
 * قالب برای نمایش صفحه اصلی با بخش‌های قابل تنظیم
 */

// ثبت تگ‌های کوتاه برای استفاده از مقادیر تنظیمات در محتوای صفحه
function weblazem_homepage_shortcodes($atts, $content = null, $tag = '')
{
    switch ($tag) {
        case 'weblazem_hero_title':
            return get_option('weblazem_hero_title', 'وب لازم برای کسب و کار شما');

        case 'weblazem_hero_subtitle':
            return get_option('weblazem_hero_subtitle', 'راهکارهای دیجیتال مارکتینگ');

        case 'weblazem_hero_text':
            return get_option('weblazem_hero_text', '');

        case 'weblazem_hero_button':
            $button_text = get_option('weblazem_hero_button_text', 'مشاوره رایگان');
            $button_url = get_option('weblazem_hero_button_url', '#');
            $order_button_text = get_option('weblazem_order_button_text', 'ثبت سفارش');
            $order_button_url = get_option('weblazem_order_button_url', '#');
            return '<div class="flex gap-4 items-center">
                <a href="' . esc_url($button_url) . '" class="hero-button">' . esc_html($button_text) . ' <i class="fas fa-arrow-left"></i></a>
                <a href="' . esc_url($order_button_url) . '" class="bg-white/10 backdrop-blur-sm border border-white text-white px-6 py-3 font-medium transition-all duration-300 hover:bg-white/20 hover:-translate-y-1" style="border-radius: 8px;">' . esc_html($order_button_text) . '</a>
            </div>';

        case 'weblazem_services_title':
            return get_option('weblazem_services_title', 'خدمات ما');

        case 'weblazem_services_subtitle':
            return get_option('weblazem_services_subtitle', 'خدمات تخصصی وب لازم برای کسب و کار شما');

        default:
            return '';
    }
}

// ثبت تگ‌های کوتاه
add_shortcode('weblazem_hero_title', 'weblazem_homepage_shortcodes');
add_shortcode('weblazem_hero_subtitle', 'weblazem_homepage_shortcodes');
add_shortcode('weblazem_hero_text', 'weblazem_homepage_shortcodes');
add_shortcode('weblazem_hero_button', 'weblazem_homepage_shortcodes');
add_shortcode('weblazem_services_title', 'weblazem_homepage_shortcodes');
add_shortcode('weblazem_services_subtitle', 'weblazem_homepage_shortcodes');

get_header();

// دیباگ و اطمینان از وجود مقادیر
$hero_subtitle = get_option('weblazem_hero_subtitle');
$hero_title = get_option('weblazem_hero_title');
$hero_text = get_option('weblazem_hero_text');
$button_text = get_option('weblazem_hero_button_text');
$button_url = get_option('weblazem_hero_button_url');
$hero_image = get_option('weblazem_hero_image');
$hero_background = get_option('weblazem_hero_background', 'https://weblazem.com/wp-content/uploads/2025/07/Rectangle-31.png');

$services_title = get_option('weblazem_services_title');
$services_subtitle = get_option('weblazem_services_subtitle');
$services_cards = get_option('weblazem_services_cards', array());

$outsourcing_title = get_option('weblazem_outsourcing_title');
$outsourcing_subtitle = get_option('weblazem_outsourcing_subtitle');
$outsourcing_button_text = get_option('weblazem_outsourcing_button_text');
$outsourcing_button_url = get_option('weblazem_outsourcing_button_url');
$outsourcing_background = get_option('weblazem_outsourcing_background');

// بخش دیباگ (در محیط توسعه فعال کنید)
if (defined('WP_DEBUG') && WP_DEBUG) :
    ?>
    <div style="background: #f5f5f5; padding: 20px; margin: 20px; border: 1px solid #ddd; border-radius: 5px; direction: ltr; text-align: left;">
        <h3>Debug Information</h3>
        <pre><?php
            echo "Hero Subtitle: " . esc_html($hero_subtitle) . "\n";
            echo "Hero Title: " . esc_html($hero_title) . "\n";
            echo "Hero Text: " . esc_html(substr($hero_text, 0, 50)) . "...\n";
            echo "Button Text: " . esc_html($button_text) . "\n";
            echo "Button URL: " . esc_url($button_url) . "\n";
            echo "Hero Image: " . esc_url($hero_image) . "\n";
            echo "Services Title: " . esc_html($services_title) . "\n";
            echo "Services Subtitle: " . esc_html($services_subtitle) . "\n";
            echo "Services Cards Count: " . count($services_cards) . "\n";

            if (!empty($services_cards)) {
                echo "First Card Title: " . esc_html($services_cards[0]['title']) . "\n";
            }
            ?></pre>
    </div>
<?php
endif;
// پایان بخش دیباگ
?>

<?php if (weblazem_is_home_section_enabled('hero')) : ?>
<div class="weblazem-home-hero"<?php if (!empty($hero_background)) : ?> style="background-image: url('<?php echo esc_url($hero_background); ?>'); background-size: cover; background-position: bottom; background-repeat: no-repeat;"<?php endif; ?>>
    <div class="container">
        <div class="hero-content">
            <div class="hero-text text-white">
                <?php if (!empty($hero_subtitle)) : ?>
                    <div class="hero-subtitle"><?php echo esc_html($hero_subtitle); ?></div>
                <?php endif; ?>

                <?php if (!empty($hero_title)) : ?>
                    <h1 class="hero-title"><?php echo esc_html($hero_title); ?></h1>
                <?php endif; ?>

                <?php if (!empty($hero_text)) : ?>
                    <div class="hero-description"><?php echo wp_kses_post($hero_text); ?></div>
                <?php endif; ?>

                <?php if (!empty($button_text) && !empty($button_url)) : ?>
                    <div class="flex gap-4 items-center">
                        <a href="<?php echo esc_url($button_url); ?>"
                           class="hero-button"><?php echo esc_html($button_text); ?> <i
                                    class="fas fa-arrow-left"></i></a>
                        <a href="<?php echo esc_url(get_option('weblazem_order_button_url', '#')); ?>"
                           class="bg-white/10 backdrop-blur-sm border border-white text-white px-6 py-3 font-medium transition-all duration-300 hover:bg-white/20 hover:-translate-y-1"
                           style="border-radius: 8px;"><?php echo esc_html(get_option('weblazem_order_button_text', 'ثبت سفارش')); ?></a>
                    </div>
                <?php endif; ?>
            </div>

            <div class="hero-image">
                <?php if (!empty($hero_image)) : ?>
                    <img src="<?php echo esc_url($hero_image); ?>"
                         alt="<?php echo esc_attr(!empty($hero_title) ? $hero_title : 'وب لازم'); ?>">
                <?php else: ?>
                    <!-- تصویر پیش‌فرض اگر تصویر انتخاب نشده باشد -->
                    <div class="placeholder-image">
                        <i class="fas fa-image"></i>
                        <span>تصویر هیرو</span>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php if (weblazem_is_home_section_enabled('services') && (!empty($services_title) || !empty($services_subtitle) || !empty($services_cards))) : ?>
    <div class="weblazem-services-section">
        <div class="container">
            <div class="section-header">
                <?php if (!empty($services_title)) : ?>
                    <h2 class="section-title"><?php echo esc_html($services_title); ?></h2>
                <?php endif; ?>

                <?php if (!empty($services_subtitle)) : ?>
                    <div class="section-subtitle"><?php echo esc_html($services_subtitle); ?></div>
                <?php endif; ?>
            </div>
            <?php
            // Always show 4 cards, fill with placeholders if needed
            $cards = is_array($services_cards) ? $services_cards : array();
            $count = count($cards);
            for ($i = $count; $i < 4; $i++) {
                $cards[] = array(
                    'image' => '',
                    'title' => 'خدمت نمونه',
                    'desc' => 'توضیحات نمونه برای خدمت',
                    'button_text' => '',
                    'button_url' => ''
                );
            }
            ?>
            <div class="services-cards new-services-cards">
                <?php foreach (array_slice($cards, 0, 4) as $card) : ?>
                    <div class="service-card new-service-card">
                        <div class="service-card-image new-service-card-image">
                            <?php if (!empty($card['image'])) : ?>
                                <img src="<?php echo esc_url($card['image']); ?>"
                                     alt="<?php echo esc_attr($card['title']); ?>">
                            <?php else: ?>
                                <div class="service-card-icon-placeholder">
                                    <i class="fas fa-shield-alt"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        <h3 class="service-card-title new-service-card-title"><?php echo esc_html($card['title']); ?></h3>
                        <div class="service-card-desc new-service-card-desc"><?php echo !empty($card['desc']) ? esc_html($card['desc']) : ''; ?></div>
                        <?php if (!empty($card['button_text']) && !empty($card['button_url'])) : ?>
                            <a href="<?php echo esc_url($card['button_url']); ?>"
                               class="service-card-button new-service-card-button"><?php echo esc_html($card['button_text']); ?></a>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php if (weblazem_is_home_section_enabled('portfolio')) : ?>
    <?php get_template_part('template-parts/home/section', 'portfolio'); ?>
<?php endif; ?>

<?php if (weblazem_is_home_section_enabled('outsourcing') && (!empty($outsourcing_title) || !empty($outsourcing_subtitle))) : ?>
    <div class="weblazem-outsourcing-section mb-10"
        <?php if (!empty($outsourcing_background)) : ?>
            style="background-image: url('<?php echo esc_url($outsourcing_background); ?>');"
        <?php endif; ?>
    >
        <div class="container">
            <div class="outsourcing-content outsourcing-grid">

                <!-- ستون متن -->
                <div class="outsourcing-text">
                    <?php if (!empty($outsourcing_title)) : ?>
                        <h2 class="outsourcing-title">
                            <?php echo esc_html($outsourcing_title); ?>
                        </h2>
                    <?php endif; ?>

                    <?php if (!empty($outsourcing_subtitle)) : ?>
                        <div class="outsourcing-subtitle">
                            <?php echo esc_html($outsourcing_subtitle); ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- ستون دکمه -->
                <?php if (!empty($outsourcing_button_text)) : ?>
                    <div class="outsourcing-cta">
                        <?php if (get_option('weblazem_outsourcing_button_modal', '1') === '1') : ?>
                            <button type="button" class="outsourcing-button weblazem-consult-trigger">
                                <?php echo esc_html($outsourcing_button_text); ?>
                                <span class="outsourcing-btn-icon"><i class="fa fa-arrow-left" aria-hidden="true"></i></span>
                            </button>
                        <?php else : ?>
                            <a href="<?php echo esc_url($outsourcing_button_url); ?>" class="outsourcing-button">
                                <?php echo esc_html($outsourcing_button_text); ?>
                                <span class="outsourcing-btn-icon"><i class="fa fa-arrow-left" aria-hidden="true"></i></span>
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </div>

<?php endif; ?>

<?php if (weblazem_is_home_section_enabled('about')) : ?>
    <?php get_template_part('template-parts/home/section', 'about'); ?>
<?php endif; ?>

<?php if (weblazem_is_home_section_enabled('team')) : ?>
    <?php get_template_part('template-parts/home/section', 'team'); ?>
<?php endif; ?>

<?php if (weblazem_is_home_section_enabled('customers')) : ?>
    <?php get_template_part('template-parts/home/section', 'customers'); ?>
<?php endif; ?>

<?php if (weblazem_is_home_section_enabled('testimonials')) : ?>
    <?php get_template_part('template-parts/home/section', 'testimonials'); ?>
<?php endif; ?>

<?php if (weblazem_is_home_section_enabled('consultation')) : ?>
    <?php get_template_part('template-parts/components/consultation', 'section', array('context' => 'home')); ?>
<?php endif; ?>

<?php if (weblazem_is_home_section_enabled('faq')) : ?>
    <?php get_template_part('template-parts/home/section', 'faq'); ?>
<?php endif; ?>

<?php if (weblazem_is_home_section_enabled('ticketing')) : ?>
    <?php get_template_part('template-parts/home/section', 'ticketing'); ?>
<?php endif; ?>


<?php
// محتوای صفحه
if (have_posts()) :
    while (have_posts()) : the_post();
        if (get_the_content()) : ?>
            <div class="weblazem-page-content">
                <div class="container">
                    <?php
                    // استفاده از تگ‌های کوتاه در محتوای صفحه
                    $content = get_the_content();
                    $content = apply_filters('the_content', $content);
                    $content = str_replace(']]>', ']]&gt;', $content);
                    echo $content;
                    ?>
                </div>
            </div>
        <?php endif;
    endwhile;
endif;

get_footer(); ?> 