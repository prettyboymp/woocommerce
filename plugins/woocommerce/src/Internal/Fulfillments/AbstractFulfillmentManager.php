<?php
/**
 * AbstractFulfillmentManager class file.
 */

declare( strict_types=1 );

namespace Automattic\WooCommerce\Internal\Fulfillments;

use Automattic\WooCommerce\Internal\DataStores\Fulfillments\FulfillmentsDataStore;

/**
 * Abstract fulfillment manager.
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
	}


	/**
	 * Get the fulfillment status of the entity. This acts like a computed property.
	 */
	public function get_fulfillment_status(): string {
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
				return 'fulfilled';
			} elseif ( $some_fulfilled ) {
				return 'partially_fulfilled';
			} else {
				return 'unfulfilled';
			}
		} else {
			return 'no_fulfillments';
		}
	}
}
