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
	 * Fulfillment ID.
	 *
	 * @var int
	 */
	protected $fulfillment_id;

	/**
	 * Entity type. Will be referencing an object type on the system. For WC, this is WC_Order.
	 *
	 * @var string
	 */
	protected $entity_type;

	/**
	 * Entity ID. Can be string, int, or UUID according to the entity used with this record.
	 *
	 * @var mixed
	 * @since 9.9.0
	 **/
	protected $entity_id;

	/**
	 * Date when the record was updated.
	 *
	 * @var string
	 * @since 9.9.0
	 */
	protected $date_updated;

	/**
	 * Date when the record was deleted.
	 *
	 * @var string
	 */
	protected $date_deleted = null;

	/**
	 * Fulfillment items.
	 *
	 * @var array
	 */
	protected $items = array();

	/**
	 * Fulfillment constructor. Loads fulfillment data.
	 *
	 * @param mixed $data Fulfillment data.
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
	public function get_id() {
		return $this->fulfillment_id;
	}
	/**
	 * Set the fulfillment ID.
	 *
	 * @param int $id Fulfillment ID.
	 */
	public function set_id( $id ) {
		$this->fulfillment_id = $id;
		parent::set_id( $id );
	}
	/**
	 * Get the entity type.
	 *
	 * @return string Entity type.
	 */
	public function get_entity_type() {
		return $this->entity_type;
	}
	/**
	 * Set the entity type.
	 *
	 * @param string $entity_type Entity type.
	 */
	public function set_entity_type( $entity_type ) {
		$this->entity_type = $entity_type;
	}
	/**
	 * Get the entity ID.
	 *
	 * @return mixed Entity ID.
	 */
	public function get_entity_id() {
		return $this->entity_id;
	}
	/**
	 * Set the entity ID.
	 *
	 * @param mixed $entity_id Entity ID.
	 */
	public function set_entity_id( $entity_id ) {
		$this->entity_id = $entity_id;
	}
	/**
	 * Get the date updated.
	 *
	 * @return string Date updated.
	 */
	public function get_date_updated() {
		return $this->date_updated;
	}

	/**
	 * Set the date updated.
	 *
	 * @param string $date_updated Date updated.
	 */
	public function set_date_updated( $date_updated ) {
		$this->date_updated = $date_updated;
	}
	/**
	 * Get the date deleted.
	 *
	 * @return string Date deleted.
	 */
	public function get_date_deleted() {
		return $this->date_deleted;
	}
	/**
	 * Set the date deleted.
	 *
	 * @param string $date_deleted Date deleted.
	 */
	public function set_date_deleted( $date_deleted ) {
		$this->date_deleted = $date_deleted;
	}

	/**
	 * Get the fulfillment items.
	 *
	 * @return array Fulfillment items.
	 */
	public function get_items() {
		$this->items = json_decode( $this->get_meta( '_items' ), true );
		return $this->items;
	}

	/**
	 * Set the fulfillment items.
	 *
	 * @param array $items Fulfillment items.
	 */
	public function set_items( $items ) {
		$this->items = $items;
		$this->update_meta_data( '_items', wp_json_encode( $items ) );
	}
}
