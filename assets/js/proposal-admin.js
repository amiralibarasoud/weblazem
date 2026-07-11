(function ($) {
    'use strict';

    function formatToman(amount) {
        amount = parseInt(amount, 10) || 0;
        try {
            return amount.toLocaleString('fa-IR') + ' تومان';
        } catch (e) {
            return amount.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',') + ' تومان';
        }
    }

    function recalcTotals() {
        var subtotal = 0;
        $('#weblazem-proposal-items .weblazem-proposal-item__price').each(function () {
            subtotal += parseInt($(this).val(), 10) || 0;
        });
        var discount = parseInt($('#discount').val(), 10) || 0;
        if (discount > subtotal) {
            discount = subtotal;
        }
        var total = Math.max(0, subtotal - discount);
        $('#weblazem-proposal-subtotal-label').text(formatToman(subtotal));
        $('#weblazem-proposal-total-label').text(formatToman(total));
    }

    function bindItemEvents($root) {
        $root.on('input', '.weblazem-proposal-item__price', recalcTotals);
        $root.on('click', '.weblazem-proposal-item__remove', function () {
            var $items = $('#weblazem-proposal-items .weblazem-proposal-item');
            if ($items.length <= 1) {
                $(this).closest('.weblazem-proposal-item').find('input, textarea').val('');
                $(this).closest('.weblazem-proposal-item').find('.weblazem-proposal-item__price').val(0);
            } else {
                $(this).closest('.weblazem-proposal-item').remove();
            }
            recalcTotals();
        });
    }

    $(function () {
        var $items = $('#weblazem-proposal-items');
        if (!$items.length) {
            return;
        }

        bindItemEvents($items);
        $('#discount').on('input', recalcTotals);

        $('#weblazem-proposal-add-item').on('click', function () {
            var tpl = document.getElementById('weblazem-proposal-item-tpl');
            if (!tpl) {
                return;
            }
            $items.append($(tpl.innerHTML));
            recalcTotals();
        });

        $('#brief_id').on('change', function () {
            var $opt = $(this).find('option:selected');
            var name = $opt.data('name') || '';
            var mobile = $opt.data('mobile') || '';
            if (name) {
                $('#client_name').val(name);
            }
            if (mobile) {
                $('#client_mobile').val(mobile);
            }
            if (!$('#proposal_title').val() && name) {
                $('#proposal_title').val('پیشنهاد پروژه — ' + name);
            }
        });

        recalcTotals();
    });
})(jQuery);
