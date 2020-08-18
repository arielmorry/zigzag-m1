(function ($) {
    $(document).on('change', '#checkout-step-shipping_method [name="shipping_method"]', function (event) {
        $('.zigzag-table').remove();
        var elem = event.target;
        var regex = RegExp('^zigzag[a-z]+_zigzag[a-z]+$');

        if (regex.test($(this).val())) {
            var address = $('#shipping\\:street1').val() + ' ' + $('#shipping\\:street2').val();
            var country_id = jQuery('#shipping\\:country_id :selected').val();

            $.ajax({
                url: '/zigzag/availability',
                type: 'post',
                data: {'target': address, 'country_id': country_id},
                dataType: 'html',
                beforeSend: function () {
                    window.checkout.setLoadWaiting('shipping-method', true);
                },
                success: function (response) {
                    if (response) {
                        $(elem).parent().append(response);
                    }
                },
                complete: function () {
                    window.checkout.setLoadWaiting(false);
                }
            })
        }
    })
})(jQuery);
