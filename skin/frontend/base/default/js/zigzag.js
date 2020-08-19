(function ($) {
    $(document).on('change', '#checkout-step-shipping_method [name="shipping_method"], #opc-co-shipping-method-form [name="shipping_method"]', function (event) {
        $('.zigzag-table').remove();
        var elem = event.target;
        var regex = RegExp('^zigzag[a-z]+_zigzag[a-z]+$');

        if (regex.test($(this).val())) {
            var country  = $('#shipping\\:country_id');
            var street1  = $('#shipping\\:street1');
            var street2  = $('#shipping\\:street2');

            if (country.length === 0 || country.val() === "") {
                country  = $('#billing\\:country_id');
            }
            if (street1.length === 0 || street1.val() === "") {
                street1  = $('#billing\\:street1');
            }
            if (street2.length === 0 || street2.val() === "") {
                street2  = $('#billing\\:street2');
            }

            if (street1.length === 0 || street1.val() === "") {
                return;
            }

            var address = street1.val() + (typeof street2 !== 'undefined' && street2.length > 0 &&  street2.val() !== "" ? ' ' + street2.val() : '');
            var country_id = country.find(':selected:first').val();

            $.ajax({
                url: '/zigzag/availability',
                type: 'post',
                data: {'target': address, 'country_id': country_id},
                dataType: 'html',
                beforeSend: function () {
                    if (typeof window.checkout.setLoadWaiting === 'function') {
                        window.checkout.setLoadWaiting('shipping-method', true);
                    } else if (typeof window.IWD.OPC.Checkout.hideLoader === 'function') {
                        window.IWD.OPC.Checkout.showLoader();
                    }
                },
                success: function (response) {
                    if (response) {
                        $(elem).parent().append(response);
                    }
                },
                complete: function () {
                    if (typeof window.checkout.setLoadWaiting === 'function') {
                        window.checkout.setLoadWaiting(false);
                    } else if (typeof window.IWD.OPC.Checkout.hideLoader === 'function') {
                        window.IWD.OPC.Checkout.hideLoader();
                    }
                }
            })
        }
    })
})(jQuery);
