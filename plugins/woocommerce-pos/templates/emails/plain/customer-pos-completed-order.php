<?php
/**
 * Customer POS completed order email (plain text)
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/plain/customer-pos-completed-order.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\POS\Templates\Emails\Plain
 * @version 1.0.0
 */

// phpcs:disable Universal.WhiteSpace.PrecisionAlignment.Found, Generic.WhiteSpace.DisallowSpaceIndent.SpacesUsed -- Plain text output needs specific spacing without tabs

use Automattic\WooCommerce\Enums\OrderStatus;

defined( 'ABSPATH' ) || exit;

echo "=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n";
echo esc_html( wp_strip_all_tags( $email_heading ) );
echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

/* translators: %s: Customer first name */
echo sprintf( esc_html__( 'Hi %s,', 'woocommerce-pos' ), esc_html( $order->get_billing_first_name() ) ) . "\n\n";

echo sprintf( esc_html__( 'Thank you for your in-store purchase from %s.', 'woocommerce-pos' ), wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES ) ) . "\n\n";

/**
 * Hook for the woocommerce_email_order_details.
 *
 * @hooked WC_Emails::order_details() Shows the order details table.
 * @hooked WC_Structured_Data::generate_order_data() Generates structured data.
 * @hooked WC_Structured_Data::output_structured_data() Outputs structured data.
 * @since 2.5.0
 */
do_action( 'woocommerce_email_order_details', $order, $sent_to_admin, $plain_text, $email );

echo "\n----------------------------------------\n\n";

/**
 * Hook for the woocommerce_email_order_meta.
 *
 * @hooked WC_Emails::order_meta() Shows order meta data.
 */
do_action( 'woocommerce_email_order_meta', $order, $sent_to_admin, $plain_text, $email );

/**
 * Hook for woocommerce_email_customer_details
 *
 * @hooked WC_Emails::customer_details() Shows customer details
 * @hooked WC_Emails::email_address() Shows email address
 */
do_action( 'woocommerce_email_customer_details', $order, $sent_to_admin, $plain_text, $email );

echo "\n\n----------------------------------------\n\n";

/**
 * Show user-defined additional content - this is set in each email's settings.
 */
if ( $additional_content ) {
	echo esc_html( wp_strip_all_tags( wptexturize( $additional_content ) ) );
	echo "\n\n----------------------------------------\n\n";
}

/**
 * Show refund and returns policy if set
 */
if ( isset( $refund_returns_policy ) && ! empty( $refund_returns_policy ) ) {
	echo esc_html__( 'REFUND & RETURNS POLICY', 'woocommerce-pos' ) . "\n\n";
	echo esc_html( wp_strip_all_tags( wptexturize( $refund_returns_policy ) ) );
	echo "\n\n----------------------------------------\n\n";
}

echo wp_kses_post( apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) ) );

// phpcs:enable Universal.WhiteSpace.PrecisionAlignment.Found, Generic.WhiteSpace.DisallowSpaceIndent.SpacesUsed
