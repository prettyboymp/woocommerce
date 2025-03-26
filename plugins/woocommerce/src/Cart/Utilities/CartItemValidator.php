<?php
declare( strict_types=1 );

namespace Automattic\WooCommerce\Cart\Utilities;

use WC_Product;
use Automattic\WooCommerce\StoreApi\Exceptions\RouteException;

/**
 * Validator for cart items.
 *
 * @since 8.0.0
 */
class CartItemValidator {
	/**
	 * Validates a product before adding to cart.
	 *
	 * @param WC_Product $product Product object.
	 * @param int        $quantity Quantity being added.
	 * @throws RouteException If product cannot be added to cart.
	 */
	public function validate_product( WC_Product $product, int $quantity ): void {
		if ( ! $product->is_purchasable() ) {
			throw new RouteException(
				'woocommerce_rest_product_not_purchasable',
				sprintf(
					/* translators: %s: product name */
					esc_html__( 'Sorry, &quot;%s&quot; cannot be purchased.', 'woocommerce' ),
					esc_html( $product->get_name() )
				),
				400
			);
		}

		if ( ! $product->is_in_stock() ) {
			throw new RouteException(
				'woocommerce_rest_product_out_of_stock',
				sprintf(
					/* translators: %s: product name */
					esc_html__( 'You cannot add &quot;%s&quot; to the cart because the product is out of stock.', 'woocommerce' ),
					esc_html( $product->get_name() )
				),
				400
			);
		}

		if ( $product->is_sold_individually() && $quantity > 1 ) {
			throw new RouteException(
				'woocommerce_rest_product_sold_individually',
				sprintf(
					/* translators: %s: product name */
					esc_html__( 'You cannot add more than one &quot;%s&quot; to your cart.', 'woocommerce' ),
					esc_html( $product->get_name() )
				),
				400
			);
		}

		if ( $product->managing_stock() && ! $product->backorders_allowed() ) {
			$remaining_stock = $product->get_stock_quantity();

			if ( $remaining_stock < $quantity ) {
				throw new RouteException(
					'woocommerce_rest_product_partially_out_of_stock',
					sprintf(
						/* translators: 1: product name 2: quantity in stock */
						esc_html__( 'You cannot add that amount of &quot;%1$s&quot; to the cart because there is not enough stock (%2$s remaining).', 'woocommerce' ),
						esc_html( $product->get_name() ),
						esc_html( CartUtils::format_stock_quantity_for_display( $remaining_stock, $product ) )
					),
					400
				);
			}
		}

		/**
		 * Fire action to validate add to cart. Functions hooking into this should throw an Exception to prevent
		 * add to cart from occurring.
		 *
		 * @since 8.0.0
		 * @param WC_Product $product Product object being added to the cart.
		 * @param int        $quantity Quantity being added.
		 */
		do_action( 'woocommerce_validate_cart_item', $product, $quantity );
	}
}
