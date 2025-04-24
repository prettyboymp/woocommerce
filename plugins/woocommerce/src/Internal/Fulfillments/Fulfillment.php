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
	public function get_entity_type(): string {
		return $this->data['entity_type'];
	}
	/**
	 * Set the entity type.
	 *
	 * @param class-string $entity_type Entity type.
	 */
	public function set_entity_type( string $entity_type ): void {
		$this->data['entity_type'] = $entity_type;
	}
	/**
	 * Get the entity ID.
	 *
	 * @return string|null Entity ID.
	 */
	public function get_entity_id(): string {
		return $this->data['entity_id'];
	}
	/**
	 * Set the entity ID.
	 *
	 * @param class-string|null $entity_id Entity ID.
	 */
	public function set_entity_id( string $entity_id ): void {
		$this->data['entity_id'] = $entity_id;
	}
	/**
	 * Get the date updated.
	 *
	 * @return \DateTime|null Date updated.
	 */
	public function get_date_updated(): \DateTime {
		return $this->data['date_updated'];
	}

	/**
	 * Set the date updated.
	 *
	 * @param \DateTime $date_updated Date updated.
	 */
	public function set_date_updated( \DateTime $date_updated ) {
		$this->data['date_updated'] = $date_updated;
	}
	/**
	 * Get the date deleted.
	 *
	 * @return \DateTime|null Date deleted.
	 */
	public function get_date_deleted(): ?\DateTime {
		return $this->data['date_deleted'] ?? null;
	}
	/**
	 * Set the date deleted.
	 *
	 * @param \DateTime|null $date_deleted Date deleted.
	 * @return void
	 */
	public function set_date_deleted( ?\DateTime $date_deleted ): void {
		$this->data['date_deleted'] = $date_deleted;
	}

	/**
	 * Get the fulfillment items.
	 *
	 * @return array Fulfillment items.
	 */
	public function get_items(): array {
		$meta_data = $this->get_meta_data();
		if ( $meta_data['_items'] ?? null ) {
			return json_decode( $meta_data['_items'], true );
		}
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
}
