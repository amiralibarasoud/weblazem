<?php
/**
 * Blog single — styled comments section.
 */

if (!comments_open() && !get_comments_number()) {
    return;
}

$title       = weblazem_blog_single_option('comments_title', 'ثبت دیدگاه');
$submit_text = weblazem_blog_single_option('comments_submit_text', 'ثبت دیدگاه');
$image       = weblazem_blog_single_option('comments_image', '');
if (!$image) {
    $image = get_template_directory_uri() . '/assets/images/blog-single/comment-envelope.svg';
}
?>

<section class="blog-single-comments" dir="rtl" id="comments">
    <div class="container blog-single-comments__inner">
        <div class="blog-single-comments__illus" aria-hidden="true">
            <img src="<?php echo esc_url($image); ?>" alt="" />
        </div>

        <div class="blog-single-comments__form-wrap">
            <h2 class="blog-single-comments__title"><?php echo esc_html($title); ?></h2>

            <?php if (get_comments_number()) : ?>
                <ol class="blog-single-comments__list">
                    <?php
                    wp_list_comments(array(
                        'style'       => 'ol',
                        'short_ping'  => true,
                        'avatar_size' => 48,
                    ));
                    ?>
                </ol>
                <?php the_comments_navigation(); ?>
            <?php endif; ?>

            <?php
            comment_form(array(
                'title_reply'          => '',
                'title_reply_to'       => 'پاسخ به %s',
                'cancel_reply_link'    => 'لغو پاسخ',
                'label_submit'         => $submit_text,
                'class_form'           => 'blog-single-comments__form',
                'class_submit'         => 'blog-single-comments__submit',
                'comment_notes_before' => '',
                'comment_notes_after'  => '',
                'fields'               => array(
                    'author' => '<p class="comment-form-author"><label for="author">نام<span class="required">*</span></label><input id="author" name="author" type="text" value="' . esc_attr(wp_get_current_commenter()['comment_author']) . '" size="30" required /></p>',
                    'email'  => '<p class="comment-form-email"><label for="email">ایمیل<span class="required">*</span></label><input id="email" name="email" type="email" value="' . esc_attr(wp_get_current_commenter()['comment_author_email']) . '" size="30" required /></p>',
                ),
                'comment_field'        => '<p class="comment-form-comment"><label for="comment">ثبت پیام<span class="required">*</span></label><textarea id="comment" name="comment" cols="45" rows="6" required></textarea></p>',
                'submit_field'         => '<p class="form-submit">%1$s %2$s</p>',
            ));
            ?>
        </div>
    </div>
</section>
