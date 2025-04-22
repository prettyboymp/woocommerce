<?php
/**
 * Class FulfillmentsDataStore file.
 *
 * @package WooCommerce\DataStores
 */

declare( strict_types=1 );

namespace Automattic\WooCommerce\Internal\DataStores\Fulfillments;

use Automattic\WooCommerce\Internal\Fulfillments\Fulfillment;
use WC_Meta_Data;
use WP_Error;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC Order Item Product Data Store
 *
 * @version  9.9.0
 */
class FulfillmentsDataStore extends \WC_Data_Store_WP implements \WC_Object_Data_Store_Interface {

	/**
	 * Error object.
	 *
	 * @var WP_Error|null $error The error object.
	 */
	protected $error = null;

	/**
	 * Method to create a new fulfillment in the database.
	 *
	 * @param Fulfillment $data The fulfillment object to create.
	 *
	 * @return void
	 */
	public function create( &$data ) {
		$this->clear_error();

		// Validate the fulfillment data.
		if ( ! $data->get_entity_type() ) {
			$this->set_error( new WP_Error( 'invalid_entity_type', __( 'Invalid entity type.', 'woocommerce' ) ) );
			return;
		}
		if ( ! $data->get_entity_id() ) {
			$this->set_error( new WP_Error( 'invalid_entity_id', __( 'Invalid entity ID.', 'woocommerce' ) ) );
			return;
		}
		if ( ! is_array( $data->get_items() ) ) {
			$this->set_error( new WP_Error( 'invalid_items', __( 'Items must be an array.', 'woocommerce' ) ) );
			return;
		}
		if ( empty( $data->get_items() ) ) {
			$this->set_error( new WP_Error( 'missing_items', __( 'The fulfillment should contain at least one item.', 'woocommerce' ) ) );
			return;
		}
		foreach ( $data->get_items() as $item ) {
			if ( ! isset( $item['item_id'] ) || ! isset( $item['qty'] ) ) {
				$this->set_error( new WP_Error( 'invalid_item', __( 'Invalid item.', 'woocommerce' ) ) );
				return;
			}
		}

		// Set fulfillment properties.
		$data->set_date_updated( current_time( 'mysql' ) );
		$data->set_date_deleted( null );

		// Save the fulfillment to the database.
		global $wpdb;
		$data_id = $wpdb->insert(
			$wpdb->prefix . 'wc_order_fulfillments',
			array(
				'entity_type'  => $data->get_entity_type(),
				'entity_id'    => $data->get_entity_id(),
				'date_updated' => $data->get_date_updated(),
				'date_deleted' => $data->get_date_deleted(),
			),
			array(
				'%s',
				'%d',
				'%s',
				'%s',
			)
		);

		// Check for errors.
		if ( false === $data_id ) {
			$this->set_error( is_wp_error( $wpdb->error ) ? $wpdb->error : new WP_Error( 'fulfillment_insert_failed', __( 'Failed to insert fulfillment.', 'woocommerce' ) ) );
			return;
		}

		// Set the ID of the fulfillment object.
		$data_id = $wpdb->insert_id;

		$data->set_id( $data_id );

		// Save the metadata for the fulfillment to the database.
		$data->save_meta_data();

		// Apply changes let's the object know that the current object reflects the database and no "changes" exist between the two.
		$data->apply_changes();
		$data->set_object_read( true );
	}

	/**
	 * Method to read a fulfillment from the database.
	 *
	 * @param Fulfillment $data The fulfillment object to read.
	 *
	 * @return void
	 */
	public function read( &$data ) {
		$this->clear_error();

		// Read the fulfillment from the database.
		global $wpdb;

		$data_id          = $data->get_id();
		$fulfillment_data = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT * FROM {$wpdb->prefix}wc_order_fulfillments WHERE fulfillment_id = %d",
				$data_id
			),
			ARRAY_A
		);

		if ( empty( $fulfillment_data ) ) {
			$this->set_error( new WP_Error( 'fulfillment_not_found', __( 'Fulfillment not found.', 'woocommerce' ) ) );
			return;
		}

		$data->set_props(
			array(
				'fulfillment_id' => $fulfillment_data['fulfillment_id'],
				'entity_type'    => $fulfillment_data['entity_type'],
				'entity_id'      => $fulfillment_data['entity_id'],
				'date_updated'   => $fulfillment_data['date_updated'],
				'date_deleted'   => $fulfillment_data['date_deleted'],
			)
		);

		$data->read_meta_data( true );

		$data->set_object_read( true );
	}

	/**
	 * Method to update an existing fulfillment in the database.
	 *
	 * @param Fulfillment $data The fulfillment object to update.
	 *
	 * @return void
	 */
	public function update( &$data ) {
		$this->clear_error();

		// Update the fulfillment in the database.
		global $wpdb;

		$data_id = $data->get_id();
		$wpdb->update(
			$wpdb->prefix . 'wc_order_fulfillments',
			array(
				'entity_type'  => $data->get_entity_type(),
				'entity_id'    => $data->get_entity_id(),
				'date_updated' => $data->get_date_updated(),
				'date_deleted' => $data->get_date_deleted(),
			),
			array( 'fulfillment_id' => $data_id ),
			array(
				'%s',
				'%d',
				'%s',
				'%s',
			),
			array( '%d' )
		);

		// Check for errors.
		if ( $wpdb->last_error ) {
			$this->set_error( is_wp_error( $wpdb->error ) ? $wpdb->error : new WP_Error( 'fulfillment_update_failed', __( 'Failed to update fulfillment.', 'woocommerce' ) ) );
			return;
		}

		// Update the metadata for the fulfillment.
		$data->save_meta_data();
		$data->apply_changes();

		$data->set_object_read( true );
	}

	/**
	 * Method to delete a fulfillment from the database.
	 *
	 * @param Fulfillment $data The fulfillment object to delete.
	 * @param array       $args Optional arguments to pass to the delete method.
	 *
	 * @return void
	 */
	public function delete( &$data, $args = array() ) {
		$this->clear_error();

		// Soft Delete the fulfillment from the database.
		global $wpdb;

		$data_id       = $data->get_id();
		$deletion_time = current_time( 'mysql' );
		$wpdb->update(
			$wpdb->prefix . 'wc_order_fulfillments',
			array(
				'date_deleted' => $deletion_time,
			),
			array( 'fulfillment_id' => $data_id ),
			array(
				'%s',
			),
			array( '%d' )
		);

		// Check for errors.
		if ( $wpdb->last_error ) {
			$this->set_error( is_wp_error( $wpdb->error ) ? $wpdb->error : new WP_Error( 'fulfillment_delete_failed', __( 'Failed to delete fulfillment.', 'woocommerce' ) ) );
			return;
		}

		// Delete the metadata for the fulfillment.
		$wpdb->delete(
			$wpdb->prefix . 'wc_order_fulfillment_meta',
			array( 'fulfillment_id' => $data_id ),
			array( '%d' )
		);

		// Check for errors.
		if ( $wpdb->last_error ) {
			$this->set_error( is_wp_error( $wpdb->error ) ? $wpdb->error : new WP_Error( 'fulfillment_meta_clear_failed', __( 'Failed to clear fulfillment meta.', 'woocommerce' ) ) );
			return;
		}

		$data->init_meta_data( array() );
		$data->set_date_deleted( $deletion_time );
		$data->apply_changes();

		$data->set_object_read( true );
	}

	/**
	 * Method to read the metadata for a fulfillment.
	 *
	 * @param Fulfillment $data The fulfillment object to read.
	 * @return array
	 */
	public function read_meta( &$data ) {
		$this->clear_error();

		if ( ! $data->get_id() ) {
			$this->set_error( new WP_Error( 'invalid_fulfillment', __( 'Invalid fulfillment.', 'woocommerce' ) ) );
			return array();
		}

		// Read the metadata for the fulfillment.
		global $wpdb;

		$data_id   = $data->get_id();
		$meta_data = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT * FROM {$wpdb->prefix}wc_order_fulfillment_meta WHERE fulfillment_id = %d",
				$data_id
			),
			OBJECT
		);

		return $meta_data;
	}

	/**
	 * Method to delete the metadata for a fulfillment.
	 *
	 * @param Fulfillment  $data The fulfillment object to delete.
	 * @param WC_Meta_Data $meta Meta object (containing at least ->id).
	 *
	 * @return void
	 */
	public function delete_meta( &$data, $meta ) {
		$this->clear_error();

		// Check if the fulfillment and meta are saved.
		$data_id = $data->get_id();
		$meta_id = $meta->id;
		if ( ! is_numeric( $data_id ) || $data_id <= 0 || ! is_numeric( $meta_id ) || $meta_id <= 0 ) {
			$this->set_error( new WP_Error( 'invalid_fulfillment_meta', __( 'Invalid fulfillment or meta.', 'woocommerce' ) ) );
			return;
		}

		// Delete the metadata for the fulfillment.
		global $wpdb;

		$wpdb->delete(
			$wpdb->prefix . 'wc_order_fulfillment_meta',
			array(
				'fulfillment_id' => $data_id,
				'meta_id'        => $meta_id,
			),
			array(
				'%d',
				'%d',
			)
		);
	}

	/**
	 * Method to add metadata for a fulfillment.
	 *
	 * @param Fulfillment  $data The fulfillment object to save.
	 * @param WC_Meta_Data $meta Meta object (containing at least ->id).
	 * @return int|WP_Error meta ID or WP_Error on failure.
	 */
	public function add_meta( &$data, $meta ) {
		$this->clear_error();
		// Add the metadata for the fulfillment.
		global $wpdb;

		// Data ID can't be something wrong as this function is called after the meta is read.
		// See WC_Data::save_meta_data().
		$data_id = $data->get_id();

		$wpdb->insert(
			$wpdb->prefix . 'wc_order_fulfillment_meta',
			array(
				'fulfillment_id' => $data_id,
				'meta_key'       => $meta->key, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
				'meta_value'     => $meta->value, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
			),
			array(
				'%d',
				'%s',
				'%s',
			)
		);

		// Note: There is no error check on WC_Data::save_meta_data(), and it expects us to return an ID in all cases.
		// If there's an error, we should return null to indicate we didn't save it.
		if ( $wpdb->last_error ) {
			$this->set_error( new WP_Error( 'fulfillment_meta_insert_failed', __( 'Failed to insert fulfillment meta.', 'woocommerce' ) ) );
			return null;
		}

		return $wpdb->insert_id;
	}

	/**
	 * Method to save the metadata for a fulfillment.
	 *
	 * @param Fulfillment  $data The fulfillment object to save.
	 * @param WC_Meta_Data $meta Meta object (containing at least ->id).
	 *
	 * @return void
	 */
	public function update_meta( &$data, $meta ) {
		$this->clear_error();

		// Update the metadata for the fulfillment.
		global $wpdb;

		$data_id      = $data->get_id();
		$rows_updated = $wpdb->update(
			$wpdb->prefix . 'wc_order_fulfillment_meta',
			array(
				'meta_value' => $meta->value, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
			),
			array(
				'fulfillment_id' => $data_id,
				'meta_id'        => $meta->id,
				'meta_key'       => $meta->key, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			),
			array(
				'%s',
			),
			array(
				'%d',
				'%d',
				'%s',
			)
		);

		// Check for errors.
		if ( $wpdb->last_error ) {
			$this->set_error( new WP_Error( 'fulfillment_meta_update_failed', __( 'Failed to update fulfillment meta.', 'woocommerce' ) ) );
			return;
		}

		return $rows_updated;
	}

	/**
	 * Get the error.
	 */
	public function get_error() {
		return $this->error;
	}

	/**
	 * Clear the error.
	 */
	protected function clear_error() {
		$this->error = null;
	}

	/**
	 * Set the error.
	 *
	 * @param WP_Error $error The error to set.
	 */
	protected function set_error( $error ) {
		$this->error = $error;
	}
}
