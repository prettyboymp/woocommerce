<?php // phpcs:ignore Suin.Classes.PSR4
/**
 * StockNotificationsTableDataStore class file.
 */

declare( strict_types = 1 );

namespace Automattic\WooCommerce\Internal\DataStores\StockNotifications;

use Automattic\WooCommerce\Internal\Utilities\DatabaseUtil;
defined( 'ABSPATH' ) || exit;

/**
 * This class is the standard data store to be used when the custom orders table is in use.
 *
 * Hint: Implement \WC_Object_Data_Store_Interface
 */
class StockNotificationsTableDataStore {

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
}
