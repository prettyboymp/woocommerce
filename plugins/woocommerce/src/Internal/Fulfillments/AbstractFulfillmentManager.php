<?php
/**
 * Fulfillable trait.
 */

declare( strict_types=1 );

namespace Automattic\WooCommerce\Internal\Fulfillments;

use Automattic\WooCommerce\Internal\DataStores\Fulfillments\FulfillmentsDataStore;

/**
 * This trait is used to mark classes that can be fulfilled.
 *
 * @since 9.0.0
 */
abstract class AbstractFulfillmentManager {
	/**
	 * Fulfillable ID, which is the identifier of the object with fulfillments. For example, `WC_Order::get_id()` is used for order fulfillments.
	 *
	 * @var string
	 */
	abstract public function get_fulfillable_id(): string;

	/**
	 * Fulfillable type, which is the identifier of the object with fulfillments. For example, `WC_Order::class` is used for order fulfillments.
	 *
	 * @var string
	 */
	abstract public function get_fulfillable_type(): string;

	/**
	 * Fulfillable status, which is the status of the object with fulfillments. For example, `WC_Order::get_status()` is used for order fulfillments.
	 *
	 * @param string $status Fulfillable status.
	 *
	 * @var string
	 */
	abstract public function set_fulfillment_status( string $status ): void;

	/**
	 * Fulfillable status, which is the status of the object with fulfillments. For example, `WC_Order::get_status()` is used for order fulfillments.
	 *
	 * @var string
	 */
	abstract public function get_fulfillment_status(): string;

	/**
	 * Get fulfillments of an entity.
	 *
	 * @return Array<Fulfillment>
	 */
	public function get_fulfillments(): array {
		return wc_get_container()
			->get( FulfillmentsDataStore::class )
			->read_fulfillments( $this->get_fulfillable_type(), $this->get_fulfillable_id() );
	}

	/**
	 * Create a new fulfillment on the entity.
	 *
	 * @param Fulfillment $fulfillment Fulfillment object.
	 *
	 * @return void
	 */
	public function add_fulfillment( Fulfillment $fulfillment ): void {
		wc_get_container()
			->get( FulfillmentsDataStore::class )
			->create( $fulfillment );
		$this->update_order_fulfillment_status();
	}

	/**
	 * Update an existing fulfillment on the entity.
	 *
	 * @param Fulfillment $fulfillment Fulfillment object.
	 *
	 * @return void
	 */
	public function update_fulfillment( Fulfillment $fulfillment ): void {
		wc_get_container()
			->get( FulfillmentsDataStore::class )
			->update( $fulfillment );
		$this->update_order_fulfillment_status();
	}

	/**
	 * Delete a fulfillment from the entity.
	 *
	 * @param Fulfillment $fulfillment Fulfillment object.
	 *
	 * @return void
	 */
	public function delete_fulfillment( Fulfillment $fulfillment ): void {
		wc_get_container()
			->get( FulfillmentsDataStore::class )
			->delete( $fulfillment );
		$this->update_order_fulfillment_status();
	}

	/**
	 * Mark a fulfillment as fulfilled.
	 *
	 * @param Fulfillment $fulfillment Fulfillment object.
	 * @param string      $status     Fulfillment status.
	 *
	 * @return void
	 */
	public function fulfill( Fulfillment $fulfillment, string $status = 'fulfilled' ): void {
		$fulfillment->set_status( $status );
		$fulfillment->set_is_fulfilled( true );
		$fulfillment->save();
		$this->update_order_fulfillment_status();
	}

	/**
	 * Update the fulfillment status of the entity based on the fulfillments.
	 *
	 * This method checks if all fulfillments are fulfilled, if some fulfillments are fulfilled, or if no fulfillments are present.
	 * It sets the fulfillment status of the entity accordingly.
	 */
	private function update_order_fulfillment_status(): void {
		$fulfillments     = $this->get_fulfillments();
		$has_fulfillments = ! empty( $fulfillments );
		$all_fulfilled    = true;
		$some_fulfilled   = false;

		if ( $has_fulfillments ) {
			foreach ( $fulfillments as $fulfillment ) {
				if ( ! $fulfillment->get_is_fulfilled() ) {
					$all_fulfilled = false;
				} else {
					$some_fulfilled = true;
				}
			}

			if ( $all_fulfilled ) {
				$this->set_fulfillment_status( 'fulfilled' );
			} elseif ( $some_fulfilled ) {
				$this->set_fulfillment_status( 'partially_fulfilled' );
			} else {
				$this->set_fulfillment_status( 'unfulfilled' );
			}
		} else {
			$this->set_fulfillment_status( 'no_fulfillments' );
		}
	}
}
