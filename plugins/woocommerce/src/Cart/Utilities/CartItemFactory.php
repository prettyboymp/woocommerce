<?php
declare( strict_types=1 );

namespace Automattic\WooCommerce\Cart\Utilities;

use Exception;
use WC_Product;
use Automattic\WooCommerce\StoreApi\Exceptions\RouteException;
use Automattic\WooCommerce\StoreApi\Utilities\QuantityLimits;

/**
 * Factory for creating cart items.
 *
 * @since 8.0.0
 */
class CartItemFactory {
	/**
	 * Creates a cart item from product data.
	 *
	 * @since 8.0.0
	 * @param int   $product_id     Product ID.
	 * @param int   $quantity       Quantity.
	 * @param int   $variation_id   Variation ID.
	 * @param array $variation      Variation data.
	 * @param array $cart_item_data Extra cart item data.
	 * @return array Cart item data array including cart_item_id.
	 * @throws RouteException If product cannot be added to cart.
	 */
	public function create_cart_item( $product_id, $quantity = 1, $variation_id = 0, $variation = array(), $cart_item_data = array() ) {
		$product = wc_get_product( $variation_id ? $variation_id : $product_id );

		/**
		 * Filter the quantity before adding to cart.
		 *
		 * @since 2.0.0
		 * @param int $quantity Quantity being added.
		 * @param int $product_id Product ID.
		 */
		$quantity = apply_filters( 'woocommerce_add_to_cart_quantity', $quantity, $product_id );

		// Create cart item data array for quantity validation.
		$cart_item = array(
			'product_id'   => $product_id,
			'variation_id' => $variation_id,
			'variation'    => $variation,
			'data'         => $product,
			'quantity'     => $quantity,
		);

		// Normalize and validate quantity.
		$quantity_limits     = new QuantityLimits();
		$quantity            = $quantity_limits->normalize_cart_item_quantity( $quantity, $cart_item );
		$quantity_validation = $quantity_limits->validate_cart_item_quantity( $quantity, $cart_item );

		if ( is_wp_error( $quantity_validation ) ) {
			throw new RouteException(
				esc_attr( $quantity_validation->get_error_code() ),
				wp_kses_post( $quantity_validation->get_error_message() ),
				400
			);
		}

		// Validate product and quantity.
		$validator = new CartItemValidator();
		$validator->validate_product( $product, $quantity );

		// Generate unique ID for the cart item.
		$cart_item_id = CartUtils::generate_cart_item_id(
			$product_id,
			$variation_id,
			$variation,
			$cart_item_data
		);

		// Generate cart item data.
		$cart_item = array_merge(
			$cart_item_data,
			array(
				'key'          => $cart_item_id,
				'product_id'   => $product_id,
				'variation_id' => $variation_id,
				'variation'    => $variation,
				'quantity'     => $quantity,
				'data'         => $product,
				'data_hash'    => CartUtils::get_cart_item_data_hash( $product ),
			)
		);

		/**
		 * Filter the cart item before it's added to the cart.
		 *
		 * @since 8.0.0
		 * @param array $cart_item Cart item data.
		 * @param string $cart_item_id Generated cart item ID.
		 */
		return apply_filters( 'woocommerce_add_cart_item', $cart_item, $cart_item_id );
	}
}
