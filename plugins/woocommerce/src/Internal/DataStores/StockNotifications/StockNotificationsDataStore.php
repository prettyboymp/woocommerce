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
	 * Handles custom metadata in the wc_stock_notificationmeta table.
	 *
	 * @var StockNotificationsMetaDataStore
	 */
	protected $data_store_meta;

	/**
	 * Initialize.
	 *
	 * @internal
	 *
	 * @param StockNotificationsMetaDataStore $data_store_meta The data store meta instance to use.
	 * @param DatabaseUtil $database_util                      The database util instance to use.
	 *
	 * @return void
	 */
	final public function init( StockNotificationsMetaDataStore $data_store_meta, DatabaseUtil $database_util ) {
		$this->data_store_meta = $data_store_meta;
		$this->database_util   = $database_util;
	}

	/**
	 * Get the stock notifications table name.
	 *
	 * @return string
	 */
	public static function get_table_name(): string {
		global $wpdb;
		return $wpdb->prefix . 'wc_stock_notifications';
	}

	/**
	 * Get the stock notifications meta table name.
	 *
	 * @return string
	 */
	public static function get_meta_table_name(): string {
		global $wpdb;
		return $wpdb->prefix . 'wc_stock_notificationmeta';
	}

	/**
	 * Get the stock notifications logs table name.
	 *
	 * @return string
	 */
	public static function get_logs_table_name(): string {
		global $wpdb;
		return $wpdb->prefix . 'wc_stock_notifications_logs';
	}

	/**
	 * Get the database schema.
	 *
	 * @return string
	 */
	public function get_database_schema(): string {
		global $wpdb;

		$collate = $wpdb->has_cap( 'collation' ) ? $wpdb->get_charset_collate() : '';

		$table_name       = $this->get_table_name();
		$meta_table_name  = $this->get_meta_table_name();
		$logs_table_name  = $this->get_logs_table_name();
		$max_index_length = $this->database_util->get_max_index_length();

		$sql = "
CREATE TABLE $table_name (
	id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
	product_id bigint(20) unsigned NOT NULL,
	user_id bigint(20) unsigned NOT NULL,
	user_email varchar(100) NOT NULL,
	status varchar(20) NOT NULL DEFAULT 'pending',
	date_created_gmt datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
	date_modified_gmt datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
	date_subscribed_gmt datetime  NOT NULL DEFAULT '0000-00-00 00:00:00',
	date_notified_gmt datetime  NOT NULL DEFAULT '0000-00-00 00:00:00',
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
	date_logged_gmt datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
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
	public function get_internal_meta_keys(): array {
		return array();
	}

	/**
	 * Filter the raw meta data.
	 *
	 * @param \WC_Data $notification The data object to filter.
	 * @param array $raw_meta_data   The raw meta data to filter.
	 * @return array
	 */
	public function filter_raw_meta_data( &$notification, array $raw_meta_data ): array {
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
			'product_id'          => $data['product_id'],
			'user_id'             => $data['user_id'],
			'user_email'          => $data['user_email'],
			'status'              => $data['status'],
			'date_created_gmt'    => $data['date_created_gmt'],
			'date_modified_gmt'   => $data['date_modified_gmt'],
			'date_subscribed_gmt' => $data['date_subscribed_gmt'],
			'date_notified_gmt'   => $data['date_notified_gmt'],
			'is_queued'           => $data['is_queued'],
		);

		$wpdb->insert( $this->get_table_name(), $data_to_insert );
	}

	/**
	 * Read a stock notification.
	 *
	 * @param \WC_Data $notification The data object to read.
	 *
	 * @throws \Exception If the stock notification is not found.
	 *
	 * @return void
	 */
	public function read( &$notification ) {
		global $wpdb;

		$data = $wpdb->get_row( $wpdb->prepare( 'SELECT * FROM %i WHERE id = %d', $this->get_table_name(), $notification->get_id() ) );

		if ( ! $data ) {
			throw new \Exception( 'Stock notification not found' );
		}

		$notification->set_props(
			array(
				'id'                  => $data->id,
				'product_id'          => $data->product_id,
				'user_id'             => $data->user_id,
				'user_email'          => $data->user_email,
				'status'              => $data->status,
				'date_created_gmt'    => $data->date_created_gmt,
				'date_modified_gmt'   => $data->date_modified_gmt,
				'date_subscribed_gmt' => $data->date_subscribed_gmt,
				'date_notified_gmt'   => $data->date_notified_gmt,
				'is_queued'           => $data->is_queued,
			)
		);

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
			'product_id'          => $data['product_id'],
			'user_id'             => $data['user_id'],
			'user_email'          => $data['user_email'],
			'status'              => $data['status'],
			'date_modified_gmt'   => current_time( 'mysql' ),
			'date_subscribed_gmt' => $data['date_subscribed_gmt'],
			'date_notified_gmt'   => $data['date_notified_gmt'],
			'is_queued'           => $data['is_queued'],
		);

		$wpdb->update( $this->get_table_name(), $data_to_update, array( 'id' => $notification->get_id() ) );
	}

	/**
	 * Delete a stock notification.
	 *
	 * @param \WC_Data $notification The data object to delete.
	 * @param array $args            Additional arguments.
	 * @return void
	 */
	public function delete( &$notification, $args = array() ) {
		global $wpdb;

		$wpdb->delete( $this->get_table_name(), array( 'id' => $notification->get_id() ) );

		$wpdb->delete( $this->get_meta_table_name(), array( 'notification_id' => $notification->get_id() ) );

		$wpdb->delete( $this->get_logs_table_name(), array( 'notification_id' => $notification->get_id() ) );
	}

	/**
	 * Add meta.
	 *
	 * @param \WC_Data $notification The data object to add.
	 * @param \stdClass $meta        The meta object to add (containing ->key and ->value).
	 * @return int|false meta ID
	 */
	public function add_meta( &$notification, $meta ) {
		$add_meta        = $this->data_store_meta->add_meta( $notification, $meta );
		$meta->id        = $add_meta;
		$changes_applied = $this->after_meta_change( $object, $meta );

		return $add_meta && $changes_applied ? $add_meta : false;
	}

	/**
	 * Read meta.
	 *
	 * @param \WC_Data $notification The data object to read.
	 * @return array
	 */
	public function read_meta( &$notification ): array {
		$raw_meta_data = $this->data_store_meta->read_meta( $notification );
		return $this->filter_raw_meta_data( $notification, $raw_meta_data );
	}

	/**
	 * Update meta.
	 *
	 * @param \WC_Data $notification The data object to update.
	 * @param \stdClass $meta        The meta object to update.
	 * @return bool
	 */
	public function update_meta( &$notification, $meta ): bool {
		$update_meta     = $this->data_store_meta->update_meta( $notification, $meta );
		$changes_applied = $this->after_meta_change( $notification, $meta );

		return $update_meta && $changes_applied;
	}

	/**
	 * Delete meta.
	 *
	 * @param \WC_Data $notification The data object to delete.
	 * @param \stdClass $meta        The meta object to delete.
	 * @return bool
	 */
	public function delete_meta( &$notification, $meta ): bool {
		$delete_meta     = $this->data_store_meta->delete_meta( $notification, $meta );
		$changes_applied = $this->after_meta_change( $notification, $meta );

		return $delete_meta && $changes_applied;
	}

	/**
	 * Perform after meta change operations.
	 *
	 * @param \WC_Data $notification The notification object.
	 * @param \WC_Meta_Data $meta    Metadata object.
	 *
	 * @return bool True if changes were applied, false otherwise.
	 */
	private function after_meta_change( &$notification, $meta ): bool {
		method_exists( $meta, 'apply_changes' ) && $meta->apply_changes();

		// Prevent this happening multiple time in same request.
		if ( $this->should_save_after_meta_change( $notification, $meta ) ) {
			// $notification->set_date_modified_gmt( current_time( 'mysql' ) );
			// $notification->save();
			return true;
		}

		return false;
	}

	/**
	 * Check if the notification should be saved after meta change.
	 *
	 * @param \WC_Data $notification The notification object.
	 * @param \WC_Meta_Data $meta    Metadata object.
	 *
	 * @return bool
	 */
	private function should_save_after_meta_change( &$notification, $meta ): bool {
		$current_time      = current_time( 'mysql' );
		$current_date_time = new \WC_DateTime( $current_time, new \DateTimeZone( 'GMT' ) );

		$should_save =
			$notification->get_id() > 0
			// && $notification->get_date_modified_gmt() < $current_date_time
			&& empty( $notification->get_changes() )
			&& ( ! is_object( $meta ) );

		/**
		 * Allows code to skip a full notification save() when metadata is changed.
		 *
		 * @since <x.x.x>
		 *
		 * @param bool $should_save Whether to trigger a full save after metadata is changed.
		 */
		return apply_filters( 'woocommerce_stock_notifications_datastore_should_save_after_meta_change', $should_save );
	}

	/**
	 * Create a log.
	 *
	 * @param \WC_Data $notification The data object to create the log for.
	 * @param array $args            Additional arguments.
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
			'date_logged_gmt'     => current_time( 'mysql' ),
			'note'            => $args['note'],
		);

		$result = $wpdb->insert( $this->get_logs_table_name(), $data );

		return $result ? (int) $wpdb->insert_id : false;
	}
}
