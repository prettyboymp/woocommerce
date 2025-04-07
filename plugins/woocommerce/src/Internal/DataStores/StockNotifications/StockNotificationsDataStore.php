<?php // phpcs:ignore Suin.Classes.PSR4
/**
 * StockNotificationsDataStore class file.
 */

declare( strict_types = 1 );

namespace Automattic\WooCommerce\Internal\DataStores\StockNotifications;

use Automattic\WooCommerce\Internal\Utilities\DatabaseUtil;
defined( 'ABSPATH' ) || exit;

/**
 * The Stock Notifications Data Store.
 */
class StockNotificationsDataStore implements \WC_Object_Data_Store_Interface {

	/**
	 * The database util object to use.
	 *
	 * @var DatabaseUtil
	 */
	protected $database_util;

	/**
	 * Initialize.
	 *
	 * @internal
	 * @param DatabaseUtil $database_util The database util instance to use.
	 *
	 * @return void
	 */
	final public function init( DatabaseUtil $database_util ) {
		$this->database_util = $database_util;
	}

	/**
	 * Get the stock notifications table name.
	 *
	 * @return string
	 */
	public static function get_table_name() {
		global $wpdb;
		return $wpdb->prefix . 'woocommerce_stock_notifications';
	}

	/**
	 * Get the stock notifications meta table name.
	 *
	 * @return string
	 */
	public static function get_meta_table_name() {
		global $wpdb;
		return $wpdb->prefix . 'woocommerce_stock_notificationmeta';
	}

	/**
	 * Get the stock notifications logs table name.
	 *
	 * @return string
	 */
	public static function get_logs_table_name() {
		global $wpdb;
		return $wpdb->prefix . 'woocommerce_stock_notifications_logs';
	}

	/**
	 * Get the database schema.
	 *
	 * @return string
	 */
	public function get_database_schema() {
		global $wpdb;

		$collate = $wpdb->has_cap( 'collation' ) ? $wpdb->get_charset_collate() : '';

		$notifications_table_name = $this->get_table_name();
		$meta_table_name          = $this->get_meta_table_name();
		$logs_table_name          = $this->get_logs_table_name();
		$max_index_length         = $this->database_util->get_max_index_length();

		$sql = "
CREATE TABLE $notifications_table_name (
	id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
	product_id bigint(20) unsigned NOT NULL,
	user_id bigint(20) unsigned NOT NULL,
	user_email varchar(100) NOT NULL,
	status varchar(20) NOT NULL DEFAULT 'pending',
	date_created timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
	date_subscribed timestamp NULL,
	date_last_notified timestamp NULL,
	is_queued tinyint(1) NOT NULL DEFAULT 0,
	PRIMARY KEY  (id),
	KEY product_status_queue (product_id, status, is_queued),
	KEY user_id (user_id),
	KEY user_email (user_email)
) $collate;
CREATE TABLE $meta_table_name (
	meta_id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
	notification_id bigint(20) unsigned NOT NULL,
	meta_key varchar(255) NULL,
	meta_value longtext NULL,
	PRIMARY KEY  (meta_id),
	KEY notification_id (notification_id),
	KEY meta_key (meta_key($max_index_length))
) $collate;
CREATE TABLE $logs_table_name (
	id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
	notification_id bigint(20) unsigned NOT NULL,
	action varchar(200) NOT NULL,
	user_id bigint(20) unsigned NOT NULL,
	user_email varchar(100) NOT NULL,
	ip_address VARCHAR(45) NULL,
	date_logged timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
	note text NOT NULL,
	PRIMARY KEY  (id),
	KEY notification_id (notification_id),
	KEY action (action),
	KEY user_id (user_id)
) $collate;
		";

		return $sql;
	}

	/**
	 * Get the internal meta keys.
	 *
	 * @return array
	 */
	public function get_internal_meta_keys() {
		return array();
	}

	/**
	 * Filter the raw meta data.
	 *
	 * @param \WC_Data $notification The data object to filter.
	 * @param array $raw_meta_data The raw meta data to filter.
	 * @return array
	 */
	public function filter_raw_meta_data( &$notification, $raw_meta_data ) {
		return $raw_meta_data;
	}

	/**
	 * Create a new stock notification.
	 *
	 * @param \WC_Data $notification The data object to create.
	 * @return void
	 */
	public function create( &$notification ) {
		global $wpdb;

		$notification->set_defaults();

		$data           = $notification->get_data();
		$data_to_insert = array(
			'product_id'         => $data['product_id'],
			'user_id'            => $data['user_id'],
			'user_email'         => $data['user_email'],
			'status'             => $data['status'],
			'date_created'       => $data['date_created'],
			'date_subscribed'    => $data['date_subscribed'],
			'date_last_notified' => $data['date_last_notified'],
		);

		$wpdb->insert( $this->get_table_name(), $data_to_insert );
	}

	/**
	 * Read a stock notification.
	 *
	 * @param \WC_Data $notification The data object to read.
	 * @return void
	 */
	public function read( &$notification ) {
		global $wpdb;

		$data = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM %i WHERE id = %d", $this->get_table_name(), $notification->get_id() ) );

		if ( ! $data ) {
			throw new \Exception( 'Stock notification not found' );
		}

		$notification->set_props( array(
			'id'                 => $data->id,
			'product_id'         => $data->product_id,
			'user_id'            => $data->user_id,
			'user_email'         => $data->user_email,
			'status'             => $data->status,
			'date_created'       => $data->date_created,
			'date_subscribed'    => $data->date_subscribed,
			'date_last_notified' => $data->date_last_notified,
			'is_queued'          => $data->is_queued,
		));

		$notification->set_object_read( true );
	}

	/**
	 * Update a stock notification.
	 *
	 * @param \WC_Data $notification The data object to update.
	 * @return void
	 */
	public function update( &$notification ) {
		global $wpdb;

		$data           = $notification->get_data();
		$data_to_update = array(
			'product_id'         => $data['product_id'],
			'user_id'            => $data['user_id'],
			'user_email'         => $data['user_email'],
			'status'             => $data['status'],
			'date_subscribed'    => $data['date_subscribed'],
			'date_last_notified' => $data['date_last_notified'],
			'is_queued'          => $data['is_queued'],
		);

		$wpdb->update( $this->get_table_name(), $data_to_update, array( 'id' => $notification->get_id() ) );
	}

	/**
	 * Delete a stock notification.
	 *
	 * @param \WC_Data $notification The data object to delete.
	 * @return void
	 */
	public function delete( &$notification, $args = array() ) {
		global $wpdb;

		$wpdb->delete( $this->get_table_name(), array( 'id' => $notification->get_id() ) );

		// Delete the meta.
		$wpdb->delete( $this->get_meta_table_name(), array( 'notification_id' => $notification->get_id() ) );

		// Delete the logs.
		$wpdb->delete( $this->get_logs_table_name(), array( 'notification_id' => $notification->get_id() ) );
	}

	/**
	 * Add meta.
	 *
	 * @param \WC_Data $notification The data object to add.
	 * @param \stdClass $meta The meta object to add.
	 * @return int|false meta ID
	 */
	public function add_meta( &$notification, $meta ) {
		global $wpdb;

		$object_id = $notification->get_id();
		if ( ! $object_id ) {
			return false;
		}

		$meta_key   = wp_unslash( wp_slash( $meta->key ) );
		$meta_value = maybe_serialize( is_string( $meta->value ) ? wp_unslash( wp_slash( $meta->value ) ) : $meta->value );

		$result = $wpdb->insert( $this->get_meta_table_name(), array(
			'notification_id' => $object_id,
			'meta_key'        => $meta_key,
			'meta_value'      => $meta_value,
		) );

		return $result ? (int) $wpdb->insert_id : false;
	}

	/**
	 * Read meta.
	 *
	 * @param \WC_Data $notification The data object to read.
	 * @return array
	 */
	public function read_meta( &$notification ) {
		global $wpdb;

		$meta_rows = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM %i WHERE notification_id = %d", $this->get_meta_table_name(), $notification->get_id() ) );

		// Format output.
		$raw_meta_data = array();
		foreach ( $meta_rows as $meta ) {
			$raw_meta_data[] = (object) array(
				'meta_id'    => $meta->meta_id,
				'meta_key'   => $meta->meta_key,
				'meta_value' => $meta->meta_value,
			);
		}

		return $this->filter_raw_meta_data( $notification, $raw_meta_data );
	}

	/**
	 * Update meta.
	 *
	 * @param \WC_Data $notification The data object to update.
	 * @param \stdClass $meta The meta object to update.
	 * @return bool
	 */
	public function update_meta( &$notification, $meta ) {
		global $wpdb;

		if ( ! isset( $meta->id ) || empty( $meta->key ) || ! $notification->get_id() ) {
			return false;
		}

		$data = array(
			'meta_key'   => $meta->key,
			'meta_value' => maybe_serialize( $meta->value ),
		);

		$meta_id = absint( $meta->id );

		$result = $wpdb->update(
			$this->get_meta_table_name(),
			$data,
			array(
				'meta_id' => $meta_id,
			),
			'%s',
			'%d'
		);

		return 1 === $result;
	}

	/**
	 * Delete meta.
	 *
	 * @param \WC_Data $notification The data object to delete.
	 * @param \stdClass $meta The meta object to delete.
	 * @return bool
	 */
	public function delete_meta( &$notification, $meta ) {
		global $wpdb;

		if ( ! isset( $meta->id ) ) {
			return false;
		}

		$meta_id = absint( $meta->id );

		return (bool) $wpdb->delete(
			$this->get_meta_table_name(),
			array(
				'meta_id' => $meta_id,
			),
			'%d'
		);
	}

	/**
	 * Create a log.
	 *
	 * @param \WC_Data $notification The data object to create the log for.
	 * @param array $args {
	 *     @type string  $action      The action to create the log for.
	 *     @type int     $user_id     The user ID to create the log for.
	 *     @type string  $user_email  The user email to create the log for.
	 *     @type string  $ip_address  The IP address to create the log for.
	 *     @type string  $note        The note to create the log for.
	 * }s
	 * @return int|false The log ID or false if the log was not created.
	 */
	public function create_log( &$notification, $args ) {
		global $wpdb;

		// TODO: Sanity check the args.

		$data = array(
			'notification_id' => $notification->get_id(),
			'action'          => $args['action'],
			'user_id'         => $args['user_id'],
			'user_email'      => $args['user_email'],
			'ip_address'      => $args['ip_address'],
			'date_logged'     => current_time( 'mysql' ),
			'note'            => $args['note'],
		);

		$result = $wpdb->insert( $this->get_logs_table_name(), $data );

		return $result ? (int) $wpdb->insert_id : false;
	}
}
