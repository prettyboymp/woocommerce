<?php

declare( strict_types = 1 );

namespace Automattic\WooCommerce\Internal\StockNotifications;

use Exception;
use WC_Product;

/**
 * The controller for the stock events.
 */
class StockSyncController {

	/**
	 * The queue using product IDs as keys.
	 *
	 * @var array<int, bool>
	 */
	private array $queue = array();

	/**
	 * Initialize the controller.
	 *
	 * @internal
	 */
	final public function init(): void {

		// Event handlers.
		add_action( 'woocommerce_product_set_stock_status', array( $this, 'handle_product_stock_status_change' ), 100, 3 );
		add_action( 'woocommerce_variation_set_stock_status', array( $this, 'handle_product_stock_status_change' ), 100, 3 );

		// Process the queue on shutdown.
		add_action( 'shutdown', array( $this, 'process_queue' ) );
	}

	/**
	 * Handle product stock status changes.
	 *
	 * @param int             $product_id   The product ID.
	 * @param string          $stock_status The new stock status.
	 * @param WC_Product|null $product      The product object (optional).
	 * @return void
	 */
	public function handle_product_stock_status_change( $product_id, $stock_status, $product = null ) {
		if ( ! in_array( (string) $stock_status, array( 'instock', 'onbackorder' ), true ) ) {
			return;
		}

		try {
			if ( ! NotificationQuery::has_active_notifications( $product_id ) ) {
				return;
			}

			// Get product if not provided
			if ( null === $product ) {
				$product = wc_get_product( $product_id );
			}

			// Validate product exists and is supported
			if ( ! $this->validate_product( $product ) ) {
				return;
			}

			// Add to queue.
			$this->queue[ $product_id ] = true;

		} catch ( \Throwable $e ) {
			wc_get_logger()->error(
				sprintf( 'StockSyncController: Failed to process product %d: %s', $product_id, $e->getMessage() ),
				array( 'source' => 'wc-stock-notifications' )
			);
		}
	}

	/**
	 * Process the product IDs in the queue.
	 *
	 * Called on shutdown to schedule Action Scheduler jobs
	 * for each product ID in the queue.
	 *
	 * @return void
	 */
	public function process_queue(): void {
		if ( empty( $this->queue ) || ! is_array( $this->queue ) ) {
			return;
		}

		$product_ids = array_keys( $this->queue );
		foreach ( $product_ids as $product_id ) {
			do_action( 'woocommerce_stock_notifications_product_sync', $product_id );
		}

		$this->queue = array();
	}


	/**
	 * Validate product to be synced.
	 *
	 * @param WC_Product|null $product The product object.
	 * @return bool True if product is valid for sync, false otherwise.
	 */
	public function validate_product( ?WC_Product $product ): bool {
		if ( null === $product || ! is_a( $product, 'WC_Product' ) ) {
			return false;
		}

		// @todo: globalize the supported types.
		$valid = $product->is_type( $this->get_supported_product_types() );

		/**
		 * Filter: woocommerce_stock_notifications_product_sync_validate
		 *
		 * Allow plugins to modify product validation logic.
		 *
		 * @param bool       $valid   Whether the product is valid for sync.
		 * @param WC_Product $product The product object.
		 */
		return (bool) apply_filters( 'woocommerce_stock_notifications_product_sync_validate', $valid, $product );
	}

	/**
	 * Get the supported product types.
	 *
	 * @return array<string> Array of supported product type slugs.
	 */
	protected function get_supported_product_types(): array {
		return array( 'simple', 'variable', 'variation' );
	}
}