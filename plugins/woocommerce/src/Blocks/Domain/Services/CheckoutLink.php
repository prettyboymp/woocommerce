<?php
/**
 * Functionality that takes a static URL, constructs a cart, and redirects to the checkout with a cart session.
 */

declare(strict_types=1);

namespace Automattic\WooCommerce\Blocks\Domain\Services;

use Automattic\WooCommerce\Blocks\Utils\CartCheckoutUtils;
use Automattic\WooCommerce\StoreApi\Utilities\CartTokenUtils;
use Automattic\WooCommerce\StoreApi\Utilities\CartController;

defined( 'ABSPATH' ) || exit;

/**
 * Checkout Link class.
 */
class CheckoutLink {
	/**
	 * Initialize the checkout link service.
	 */
	public function init() {
		add_action( 'init', array( $this, 'add_checkout_link_endpoint' ) );
		add_filter( 'query_vars', array( $this, 'add_query_vars' ), 0 );
		add_action( 'template_redirect', array( $this, 'handle_checkout_link_endpoint' ) );
	}

	/**
	 * Add the checkout link endpoint.
	 */
	public function add_checkout_link_endpoint() {
		add_rewrite_rule( '^checkout-link$', 'index.php?checkout-link=true', 'top' );
	}

	/**
	 * Add the checkout link query var.
	 *
	 * @param array $vars The query vars.
	 * @return array The query vars.
	 */
	public function add_query_vars( $vars ) {
		$vars[] = 'checkout-link';
		return $vars;
	}

	/**
	 * Handle the checkout link endpoint.
	 *
	 * @return void
	 */
	public function handle_checkout_link_endpoint() {
		if ( ! get_query_var( 'checkout-link' ) ) {
			return;
		}
		wp_safe_redirect( $this->get_checkout_link() );
		exit;
	}

	/**
	 * Process the query params and return the checkout link to redirect to complete with session token.
	 *
	 * @return string The checkout link.
	 */
	protected function get_checkout_link() {
		$controller = new CartController();
		$controller->empty_cart();

		// Populate cart with products.
		$products = wp_parse_id_list( wp_unslash( $_GET['products'] ?? '' ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		foreach ( $products as $product_id ) {
			try {
				$controller->add_to_cart(
					[
						'id'       => $product_id,
						'quantity' => 1,
					]
				);
			} catch ( \Exception $e ) {
				wc_add_notice( $e->getMessage(), 'error' );
			}
		}

		// Apply coupon if provided.
		$coupon = wc_format_coupon_code( wp_unslash( $_GET['coupon'] ?? '' ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

		if ( wc_coupons_enabled() && ! empty( $coupon ) ) {
			try {
				$controller->apply_coupon( $coupon );
			} catch ( \Exception $e ) {
				wc_add_notice( $e->getMessage(), 'error' );
			}
		}

		// Nothing was added to the cart. We need to redirect to the cart page with an error notice. Since guests may not
		// have a session, add the notice in the query string.
		if ( wc()->cart->is_empty() ) {
			return add_query_arg( 'wc_error', __( 'The provided checkout link was out of date or invalid. No products were added to the cart.', 'woocommerce' ), wc_get_cart_url() );
		}

		$redirect_url = wc_get_checkout_url();

		// If the user is logged in, the session is tied to the user ID. Do not use a cart token.
		if ( ! is_user_logged_in() ) {
			$session_token = CartTokenUtils::get_cart_token( wc()->session->get_customer_id() );
			$redirect_url  = add_query_arg( 'session', $session_token, $redirect_url );
		}

		return $redirect_url;
	}
}
