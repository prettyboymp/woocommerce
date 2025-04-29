<?php
/**
 * OrderFulfillmentManager class file.
 */

declare( strict_types=1 );

namespace Automattic\WooCommerce\Internal\Fulfillments;

use WC_Order;

/**
 * Order fulfillment manager.
 *
 * @since 9.0.0
 */
class OrderFulfillmentManager extends AbstractFulfillmentManager {
	/**
	 * Order object.
	 *
	 * @var \WC_Order
	 */
	private \WC_Order $order;

	/**
	 * Constructor.
	 *
	 * @param \WC_Order $order Order object.
	 */
	public function __construct( \WC_Order $order ) {
		$this->order = $order;
	}

	/**
	 * Fulfillable ID, which is the identifier of the object with fulfillments. For example, `WC_Order::get_id()` is used for order fulfillments.
	 *
	 * @var string
	 */
	public function get_fulfillable_id(): string {
		return (string) $this->order->get_id();
	}

	/**
	 * Fulfillable type, which is the identifier of the object with fulfillments. For example, `WC_Order::class` is used for order fulfillments.
	 *
	 * @var string
	 */
	public function get_fulfillable_type(): string {
		return WC_Order::class;
	}


	/**
	 * Returns the fulfillment status of the order.
	 *
	 * @return string
	 */
	public function get_fulfillment_status(): string {
		$meta = $this->order->get_meta( '_fulfillment_status' );
		return $meta ? $meta : 'no_fulfillments';
	}

	/**
	 * Sets the fulfillment status of the order.
	 *
	 * @param string $status The status to set.
	 */
	public function set_fulfillment_status( string $status ): void {
		$this->order->update_meta_data( '_fulfillment_status', $status );
		$this->order->save_meta_data();
	}
}
