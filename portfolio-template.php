<?php
/**
 * Template Name: صفحه نمونه کارها
 * Description: لیست نمونه کارها با طرح اختصاصی
 */

get_header();
?>

<main class="weblazem-portfolio-page" dir="rtl">
    <?php get_template_part('template-parts/portfolio/layout', 'main'); ?>
</main>

<script>
document.addEventListener('DOMContentLoaded', function () {
    if (window.location.hash === '#portfolio-all-projects' || /portfolio_page=\d+/.test(window.location.search)) {
        var section = document.getElementById('portfolio-all-projects');
        if (section) {
            section.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }
});
</script>

<?php get_footer(); ?>
