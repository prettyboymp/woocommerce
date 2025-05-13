<?php
/**
 * Address suggestions component for WooCommerce checkout
 *
 * @package WooCommerce\Templates
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="woocommerce-address-suggestions" style="display: none; position: relative;">
    <div class="woocommerce-address-suggestions__wrapper" style="
        position: absolute;
        z-index: 1000;
        background: white;
        border: 1px solid #ddd;
        border-radius: 4px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        width: 100%;
        max-width: 100%;
        top: 0;
        left: 0;
        padding: 8px 0;
    ">
        <ul class="woocommerce-address-suggestions__list" style="
            list-style: none;
            margin: 0;
            padding: 0;
        ">
            <li class="woocommerce-address-suggestions__item" style="
                padding: 8px 16px;
                cursor: pointer;
                transition: background-color 0.2s;
            ">123 Main Street, Apt 4B, New York, NY 10001</li>
            <li class="woocommerce-address-suggestions__item" style="
                padding: 8px 16px;
                cursor: pointer;
                transition: background-color 0.2s;
            ">123 Main Street, Suite 100, New York, NY 10001</li>
            <li class="woocommerce-address-suggestions__item" style="
                padding: 8px 16px;
                cursor: pointer;
                transition: background-color 0.2s;
            ">123 Main Avenue, Brooklyn, NY 11201</li>
        </ul>
    </div>
</div>

<style>
.woocommerce-address-suggestions__item:hover {
    background-color: #f6f6f6;
}

/* Position the suggestions container relative to the address field */
#billing_address_1_field {
    position: relative;
}

.woocommerce-address-suggestions {
    position: absolute;
    width: 100%;
    z-index: 1000;
}
</style>

<script>
jQuery(function($) {
    // Move suggestions div right after the address input
    var $suggestions = $('.woocommerce-address-suggestions');
    var $addressField = $('#billing_address_1_field');
    $suggestions.appendTo($addressField);

    $('#billing_address_1').on('input', function() {
        var value = $(this).val();
        if (value.length >= 3) {
            $suggestions.show();
        } else {
            $suggestions.hide();
        }
    });

    $('.woocommerce-address-suggestions__item').on('click', function() {
        var address = $(this).text();
        $('#billing_address_1').val(address);
        $suggestions.hide();
    });

    // Hide suggestions when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.woocommerce-address-suggestions, #billing_address_1').length) {
            $suggestions.hide();
        }
    });
});
</script>
