<?php // phpcs:ignore Suin.Classes.PSR4
/**
 * StockNotificationsDataStore class file.
 */

declare( strict_types = 1 );

namespace Automattic\WooCommerce\Internal\DataStores\StockNotifications;

use Automattic\WooCommerce\Internal\StockNotifications\Notification;
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
	 * @param DatabaseUtil                    $database_util   The database util instance to use.
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
	date_created_gmt datetime NULL,
	date_modified_gmt datetime NULL,
	date_subscribed_gmt datetime NULL,
	date_notified_gmt datetime NULL,
	is_queued tinyint(1) NOT NULL DEFAULT 0,
	PRIMARY KEY  (id),
	KEY product_status_queue (product_id, status, is_queued),
	KEY user_id (user_id),
	KEY user_email (user_email)
) $collate;
CREATE TABLE $meta_table_name (
	id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
	notification_id bigint(20) unsigned NOT NULL,
	meta_key varchar(255) NULL,
	meta_value longtext NULL,
	PRIMARY KEY  (id),
	KEY notification_id (notification_id),
	KEY meta_key (meta_key($max_index_length))
) $collate;
CREATE TABLE $logs_table_name (
	id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
	notification_id bigint(20) unsigned NOT NULL,
	action varchar(200) NOT NULL,
	user_id bigint(20) unsigned NOT NULL,
	user_email varchar(100) NOT NULL,
	ip_address varchar(45) NULL,
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
	 * @param Notification $notification  The data object to filter.
	 * @param array    $raw_meta_data The raw meta data to filter.
	 * @return array
	 */
	public function filter_raw_meta_data( &$notification, $raw_meta_data ): array {
		return $raw_meta_data;
	}

	/**
	 * Create a new stock notification.
	 *
	 * @param Notification $notification The data object to create.
	 * @return int|\WP_Error The notification ID on success. WP_Error on failure.
	 */
	public function create( &$notification ) {
		global $wpdb;

		// Fill in created and modified dates.
		if ( ! $notification->get_date_created( 'edit' ) ) {
			$notification->set_date_created( current_time( 'mysql' ) );
		}
		if ( ! $notification->get_date_modified( 'edit' ) ) {
			$notification->set_date_modified( current_time( 'mysql' ) );
		}

		$insert = $wpdb->insert(
			$this->get_table_name(),
			array(
				'product_id'          => $notification->get_product_id( 'edit' ),
				'user_id'             => $notification->get_user_id( 'edit' ),
				'user_email'          => $notification->get_user_email( 'edit' ),
				'status'              => $notification->get_status( 'edit' ),
				'date_created_gmt'    => gmdate( 'Y-m-d H:i:s', $notification->get_date_created( 'edit' )->getTimestamp() ),
				'date_modified_gmt'   => gmdate( 'Y-m-d H:i:s', $notification->get_date_modified( 'edit' )->getTimestamp() ),
				'date_subscribed_gmt' => $notification->get_date_subscribed( 'edit' ) ? gmdate( 'Y-m-d H:i:s', $notification->get_date_subscribed( 'edit' )->getTimestamp() ) : null,
				'date_notified_gmt'   => $notification->get_date_notified( 'edit' ) ? gmdate( 'Y-m-d H:i:s', $notification->get_date_notified( 'edit' )->getTimestamp() ) : null,
				'is_queued'           => $notification->is_queued( 'edit' ) ? 1 : 0,
			),
			array(
				'%d',
				'%d',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%d',
			)
		);

		if ( false === $insert ) {
			return new \WP_Error( 'db_insert_error', 'Could not insert stock notification into the database.' );
		}

		$notification_id = (int) $wpdb->insert_id;
		$notification->set_id( $notification_id );

		$notification->save_meta_data();
		$notification->apply_changes();

		return $notification->get_id();
	}

	/**
	 * Read a stock notification.
	 *
	 * @param Notification $notification The data object to read.
	 *
	 * @throws \Exception If the stock notification is not found.
	 *
	 * @return void
	 */
	public function read( &$notification ) {
		global $wpdb;

		if ( 0 === $notification->get_id() ) {
			throw new \Exception( 'Invalid notification ID.' );
		}

		$data = $wpdb->get_row(
			$wpdb->prepare(
				'SELECT * FROM %i WHERE id = %d',
				$this->get_table_name(),
				$notification->get_id()
			)
		);

		if ( ! $data ) {
			throw new \Exception( 'Stock notification not found' );
		}

		$notification->set_props(
			array(
				'id'              => $data->id,
				'product_id'      => $data->product_id,
				'user_id'         => $data->user_id,
				'user_email'      => $data->user_email,
				'status'          => $data->status,
				'date_created'    => $data->date_created_gmt,
				'date_modified'   => $data->date_modified_gmt,
				'date_subscribed' => $data->date_subscribed_gmt,
				'date_notified'   => $data->date_notified_gmt,
				'is_queued'       => $data->is_queued,
			)
		);

		$notification->read_meta_data();
		$notification->set_object_read( true );
	}

	/**
	 * Update a stock notification.
	 *
	 * @param Notification $notification The data object to update.
	 * @return int|\WP_Error The number of rows updated or WP_Error on failure.
	 */
	public function update( &$notification ) {
		global $wpdb;

		if ( 0 === $notification->get_id() ) {
			return new \WP_Error( 'invalid_stock_notification', 'Invalid notification ID.' );
		}

		$changes       = $notification->get_changes();
		$date_modified = current_time( 'mysql' );
		$result        = 0;

		if ( in_array( array( 'product_id', 'user_id', 'user_email', 'status', 'is_queued', 'date_subscribed', 'date_notified' ), array_keys( $changes ) ) ) {
			$result = $wpdb->update(
				$this->get_table_name(),
				array(
					'product_id'          => $notification->get_product_id( 'edit' ),
					'user_id'             => $notification->get_user_id( 'edit' ),
					'user_email'          => $notification->get_user_email( 'edit' ),
					'status'              => $notification->get_status( 'edit' ),
					'date_modified_gmt'   => $date_modified,
					'date_subscribed_gmt' => $notification->get_date_subscribed( 'edit' ) ? gmdate( 'Y-m-d H:i:s', $notification->get_date_subscribed( 'edit' )->getTimestamp() ) : null,
					'date_notified_gmt'   => $notification->get_date_notified( 'edit' ) ? gmdate( 'Y-m-d H:i:s', $notification->get_date_notified( 'edit' )->getTimestamp() ) : null,
					'is_queued'           => $notification->is_queued( 'edit' ) ? 1 : 0,
				),
				array( 'id' => $notification->get_id() ),
				array( '%d', '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%d' ),
				array( '%d' )
			);

			if ( false === $result ) {
				return new \WP_Error( 'db_update_error', 'Could not update stock notification in the database.' );
			}

			if ( 0 === $result ) {
				return new \WP_Error( 'db_update_error', 'Invalid notification ID.' );
			}

			$notification->set_date_modified( $date_modified );
		}

		$notification->save_meta_data();
		$notification->apply_changes();

		return $result;
	}

	/**
	 * Delete a stock notification.
	 *
	 * @param Notification $notification The data object to delete.
	 * @param array    $args         Additional arguments.
	 * @return void
	 */
	public function delete( &$notification, $args = array() ) {
		global $wpdb;

		$wpdb->delete( $this->get_table_name(), array( 'id' => $notification->get_id() ), array( '%d' ) );

		$wpdb->delete( $this->get_meta_table_name(), array( 'notification_id' => $notification->get_id() ), array( '%d' ) );

		$wpdb->delete( $this->get_logs_table_name(), array( 'notification_id' => $notification->get_id() ), array( '%d' ) );
	}

	/**
	 * Add meta.
	 *
	 * @param Notification $notification The data object to add.
	 * @param \stdClass $meta         The meta object to add (containing ->key and ->value).
	 * @return int|false The meta ID or false if the meta was not added.
	 */
	public function add_meta( &$notification, $meta ) {
		$add_meta        = $this->data_store_meta->add_meta( $notification, $meta );
		$meta->id        = $add_meta;
		$changes_applied = $this->after_meta_change( $notification, $meta );

		return $add_meta && $changes_applied ? $add_meta : false;
	}

	/**
	 * Read meta.
	 *
	 * @param Notification $notification The data object to read.
	 * @return array
	 */
	public function read_meta( &$notification ): array {
		$raw_meta_data = $this->data_store_meta->read_meta( $notification );
		return $this->filter_raw_meta_data( $notification, $raw_meta_data );
	}

	/**
	 * Update meta.
	 *
	 * @param Notification $notification The data object to update.
	 * @param \stdClass $meta         The meta object to update (containing ->id, ->key and ->value).
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
	 * @param Notification $notification The data object to delete.
	 * @param \stdClass $meta         The meta object to delete (containing at least ->id).
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
	 * @param Notification $notification The notification object.
	 * @param \stdClass    $meta         Metadata object.
	 *
	 * @return bool True if changes were applied, false otherwise.
	 */
	private function after_meta_change( &$notification, $meta ): bool {
		method_exists( $meta, 'apply_changes' ) && $meta->apply_changes();

		// Prevent this happening multiple time in same request.
		if ( $this->should_save_after_meta_change( $notification, $meta ) ) {
			$notification->set_date_modified( current_time( 'mysql' ) );
			$notification->save();
			return true;
		}

		return false;
	}

	/**
	 * Check if the notification should be saved after meta change.
	 *
	 * @param Notification $notification The notification object.
	 * @param \stdClass    $meta         Metadata object.
	 *
	 * @return bool
	 */
	private function should_save_after_meta_change( &$notification, $meta ): bool {
		$current_time      = current_time( 'mysql' );
		$current_date_time = new \WC_DateTime( $current_time, new \DateTimeZone( 'GMT' ) );

		$should_save =
			$notification->get_id() > 0
			&& $notification->get_date_modified() < $current_date_time
			&& empty( $notification->get_changes() )
			&& ( ! is_object( $meta ) );

		// phpcs:disable WooCommerce.Commenting.CommentHooks.MissingSinceVersionComment
		/**
		 * Allows code to skip a full notification save() when metadata is changed.
		 *
		 * @since x.x.x
		 *
		 * @param bool $should_save Whether to trigger a full save after metadata is changed.
		 * @return bool
		 */
		return apply_filters( 'woocommerce_stock_notifications_datastore_should_save_after_meta_change', $should_save );
		// phpcs:enable WooCommerce.Commenting.CommentHooks.MissingSinceVersionComment
	}

	/**
	 * Create a event.
	 *
	 * @param Notification $notification The data object to create the log for.
	 * @param array        $args         Additional arguments.
	 * @return int|false The log ID or false if the log was not created.
	 */
	public function create_event( &$notification, $args ) {
		global $wpdb;

		$args['action']     = sanitize_text_field( $args['action'] );
		$args['user_id']    = absint( $args['user_id'] );
		$args['user_email'] = sanitize_email( $args['user_email'] );
		$args['ip_address'] = sanitize_text_field( $args['ip_address'] );
		$args['note']       = sanitize_text_field( $args['note'] );

		$data = array(
			'notification_id' => $notification->get_id(),
			'action'          => $args['action'],
			'user_id'         => $args['user_id'],
			'user_email'      => $args['user_email'],
			'ip_address'      => $args['ip_address'],
			'date_logged_gmt' => current_time( 'mysql' ),
			'note'            => $args['note'],
		);

		$result = $wpdb->insert( $this->get_logs_table_name(), $data );

		return $result ? (int) $wpdb->insert_id : false;
	}
}
