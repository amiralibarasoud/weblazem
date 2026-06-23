jQuery(function ($) {
    var frame;

    function bindImageButtons($context) {
        $context.find('.weblazem-portfolio-upload-image').off('click').on('click', function (e) {
            e.preventDefault();
            var target = $($(this).data('target'));

            if (frame) {
                frame.open();
            } else {
                frame = wp.media({
                    title: 'انتخاب تصویر',
                    button: { text: 'استفاده از این تصویر' },
                    multiple: false
                });
            }

            frame.off('select').on('select', function () {
                var attachment = frame.state().get('selection').first().toJSON();
                target.val(attachment.url);
                target.closest('td, .weblazem-portfolio-section-admin').find('.weblazem-portfolio-image-preview')
                    .html('<img src="' + attachment.url + '" style="max-width:220px;border-radius:10px;" alt="" />');
            });

            frame.open();
        });

        $context.find('.weblazem-portfolio-remove-image').off('click').on('click', function (e) {
            e.preventDefault();
            var target = $($(this).data('target'));
            target.val('');
            target.closest('td, .weblazem-portfolio-section-admin').find('.weblazem-portfolio-image-preview').empty();
        });
    }

    bindImageButtons($(document));

    var sectionIndex = $('#portfolio-single-sections-container .weblazem-portfolio-section-admin').length;

    $('#add-portfolio-single-section').on('click', function () {
        var tpl = $('#portfolio-single-section-template').html().replace(/\{\{index\}\}/g, sectionIndex);
        var $block = $(tpl);
        $('#portfolio-single-sections-container').append($block);
        bindImageButtons($block);
        sectionIndex++;
    });

    $(document).on('click', '.portfolio-single-section-remove', function () {
        $(this).closest('.weblazem-portfolio-section-admin').remove();
    });
});
