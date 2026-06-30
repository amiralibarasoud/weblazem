(function ($) {
    'use strict';

    function updatePreview($field) {
        var custom = $.trim($field.find('.weblazem-menu-fa-icon-custom').val());
        var selected = $.trim($field.find('.weblazem-menu-fa-icon-select').val());
        var iconClass = custom || selected;
        var $preview = $field.find('.weblazem-menu-fa-icon-preview');

        $preview.empty();

        if (iconClass) {
            $preview.append($('<i>', { class: iconClass }));
        }
    }

    function syncSelectToCustom($field) {
        var selected = $field.find('.weblazem-menu-fa-icon-select').val();
        if (selected) {
            $field.find('.weblazem-menu-fa-icon-custom').val(selected);
        }
    }

    $(document).on('change', '.weblazem-menu-fa-icon-select', function () {
        var $field = $(this).closest('.field-weblazem-fa-icon');
        syncSelectToCustom($field);
        updatePreview($field);
    });

    $(document).on('input', '.weblazem-menu-fa-icon-custom', function () {
        var $field = $(this).closest('.field-weblazem-fa-icon');
        $field.find('.weblazem-menu-fa-icon-select').val('');
        updatePreview($field);
    });

    $(document).on('menu-item-added menu-item-updated', function (event, $menuItem) {
        $menuItem.find('.field-weblazem-fa-icon').each(function () {
            updatePreview($(this));
        });
    });
})(jQuery);
