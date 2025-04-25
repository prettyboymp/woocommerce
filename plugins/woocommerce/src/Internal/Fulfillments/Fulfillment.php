<?php
/**
 * WooCommerce order fulfillments.
 *
 * The WooCommerce order fulfillments class gets contains fulfillment related properties and methods.
 *
 * @package WooCommerce\Classes
 * @version 9.9.0
 */

declare( strict_types=1 );

namespace Automattic\WooCommerce\Internal\Fulfillments;

use Automattic\WooCommerce\Internal\DataStores\Fulfillments\FulfillmentsDataStore;

defined( 'ABSPATH' ) || exit;


/**
 * WC Order Fulfillment Class
 *
 * @since 9.9.0
 */
class Fulfillment extends \WC_Data {
	/**
	 * Fulfillment constructor. Loads fulfillment data.
	 *
	 * @param array|string|Fulfillment $data Fulfillment data.
	 */
	public function __construct( $data = '' ) {
		parent::__construct( $data );

		if ( $data instanceof Fulfillment ) {
			$this->set_id( absint( $data->get_id() ) );
		} elseif ( is_numeric( $data ) ) {
			$this->set_id( absint( $data ) );
		} elseif ( is_array( $data ) && isset( $data['id'] ) ) {
			$this->set_id( absint( $data['id'] ) );
		} elseif ( is_string( $data ) && ! empty( $data ) ) {
			$this->set_id( absint( $data ) );
		} elseif ( is_object( $data ) && isset( $data->id ) ) {
			$this->set_id( absint( $data->id ) );
		} else {
			$this->set_object_read( true );
		}

		// Load the items array.
		$this->data_store = wc_get_container()->get( FulfillmentsDataStore::class );
		if ( $this->get_id() > 0 ) {
			$this->data_store->read( $this );
		}
	}

	/**
	 * Get the fulfillment ID.
	 *
	 * @return int Fulfillment ID.
	 */
	public function get_id(): int {
		return $this->data['fulfillment_id'] ?? 0;
	}

	/**
	 * Set the fulfillment ID.
	 *
	 * @param int $id Fulfillment ID.
	 */
	public function set_id( $id ): void {
		$this->data['fulfillment_id'] = is_numeric( $id ) ? absint( $id ) : 0;
		parent::set_id( $this->data['fulfillment_id'] );
	}

	/**
	 * Get the entity type.
	 *
	 * @return string|null Entity type.
	 */
	public function get_entity_type(): ?string {
		return $this->data['entity_type'];
	}

	/**
	 * Set the entity type.
	 *
	 * @param class-string|null $entity_type Entity type.
	 */
	public function set_entity_type( ?string $entity_type ): void {
		$this->data['entity_type'] = $entity_type;
	}

	/**
	 * Get the entity ID.
	 *
	 * @return string|null Entity ID.
	 */
	public function get_entity_id(): ?string {
		return $this->data['entity_id'];
	}

	/**
	 * Set the entity ID.
	 *
	 * @param class-string|null $entity_id Entity ID.
	 */
	public function set_entity_id( ?string $entity_id ): void {
		$this->data['entity_id'] = $entity_id;
	}

	/**
	 * Set fulfillment status.
	 *
	 * @param string|null $status Fulfillment status.
	 *
	 * @return void
	 */
	public function set_status( ?string $status ): void {
		$this->data['status'] = $status;
	}

	/**
	 * Get the fulfillment status.
	 *
	 * @return string|null Fulfillment status.
	 */
	public function get_status(): ?string {
		return $this->data['status'];
	}

	/**
	 * Set if the fulfillment is fulfilled.
	 *
	 * @param bool $is_fulfilled Whether the fulfillment is fulfilled.
	 *
	 *  @return void
	 */
	public function set_is_fulfilled( bool $is_fulfilled ): void {
		$this->data['is_fulfilled'] = $is_fulfilled;
	}

	/**
	 * Get if the fulfillment is fulfilled.
	 *
	 * @return bool Whether the fulfillment is fulfilled.
	 */
	public function get_is_fulfilled(): bool {
		return $this->data['is_fulfilled'] ?? false;
	}

	/**
	 * Get the date updated.
	 *
	 * @return string|null Date updated.
	 */
	public function get_date_updated(): ?string {
		return $this->data['date_updated'];
	}

	/**
	 * Set the date updated.
	 *
	 * @param string|null $date_updated Date updated.
	 */
	public function set_date_updated( ?string $date_updated ) {
		$this->data['date_updated'] = $date_updated;
	}
	/**
	 * Get the date deleted.
	 *
	 * @return string|null Date deleted.
	 */
	public function get_date_deleted(): ?string {
		return $this->data['date_deleted'];
	}
	/**
	 * Set the date deleted.
	 *
	 * @param string|null $date_deleted Date deleted.
	 * @return void
	 */
	public function set_date_deleted( ?string $date_deleted ): void {
		$this->data['date_deleted'] = $date_deleted;
	}

	/**
	 * Get the fulfillment items.
	 *
	 * @return array Fulfillment items.
	 */
	public function get_items(): array {
		// Get the meta data for this fulfillment.
		$meta_data = $this->get_meta_data();

		// Get the meta object with the "_items" meta key.
		$items = array_values(
			array_filter(
				$meta_data,
				function ( $meta ) {
					return '_items' === $meta->key;
				}
			)
		);

		// If we have a matching meta key, decode the JSON and return it.
		if ( 0 < count( $items ) ) {
			return json_decode( $items[0]->value, true );
		}

		// If we don't have a matching meta key, return an empty array.
		return array();
	}

	/**
	 * Set the fulfillment items.
	 *
	 * @param array $items Fulfillment items.
	 */
	public function set_items( array $items ): void {
		$this->update_meta_data( '_items', wp_json_encode( $items ) );
	}

	/**
	 * Resets the object to its default state.
	 *
	 * @return void
	 */
	public function reset(): void {
		$this->data = array();
		$this->set_id( 0 );
		$this->init_meta_data();
		$this->set_object_read( false );
		$this->apply_changes();
	}
}
