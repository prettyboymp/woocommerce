<?php // phpcs:ignore Suin.Classes.PSR4
/**
 * StockNotificationsActivityLogsDataStore class file.
 */

declare( strict_types = 1 );

namespace Automattic\WooCommerce\Internal\DataStores\StockNotifications;

use Automattic\WooCommerce\Internal\DataStores\StockNotifications\StockNotificationsDataStore;

defined( 'ABSPATH' ) || exit;

/**
 * The Stock Notifications Activity Logs Data Store.
 */
class StockNotificationsActivityLogsDataStore {

	/**
	 * Create an activity log.
	 *
	 * @param array $args Additional arguments.
	 * @return int|false The log ID or false if the log was not created.
	 */
	public function create( $args ) {
		global $wpdb;

		$table = StockNotificationsDataStore::get_logs_table_name();
		$data  = array(
			'notification_id' => absint( $args['notification_id'] ),
			'action'          => sanitize_text_field( $args['action'] ),
			'user_id'         => absint( $args['user_id'] ),
			'user_email'      => sanitize_email( $args['user_email'] ),
			'ip_address'      => sanitize_text_field( $args['ip_address'] ),
			'date_logged_gmt' => current_time( 'mysql' ),
			'note'            => sanitize_text_field( $args['note'] ),
		);

		$result = $wpdb->insert(
			$table,
			$data,
			array( '%d', '%s', '%d', '%s', '%s', '%s', '%s' )
		);

		return $result ? (int) $wpdb->insert_id : false;
	}

	/**
	 * Read a log.
	 *
	 * @param int $log_id The log ID.
	 * @return array|false The log data or false if not found.
	 */
	public function read( $log_id ) {
		global $wpdb;

		$log = $wpdb->get_row(
			$wpdb->prepare(
				'SELECT * FROM %i WHERE id = %d',
				StockNotificationsDataStore::get_logs_table_name(),
				$log_id
			),
			ARRAY_A
		);

		return $log ?: false;
	}

	/**
	 * Delete a log.
	 *
	 * @param int $log_id The log ID.
	 * @return bool True if deleted, false otherwise.
	 */
	public function delete( $log_id ) {
		global $wpdb;

		return (bool) $wpdb->delete(
			StockNotificationsDataStore::get_logs_table_name(),
			array( 'id' => $log_id ),
			array( '%d' )
		);
	}

	/**
	 * Query activity logs.
	 *
	 * @param array $args The arguments.
	 * @return array|int An array of logs or the number of logs.
	 */
	public function query( $args ) {
		global $wpdb;

		$args = wp_parse_args(
			$args,
			array(
				'notification_id' => 0,
				'action'          => '',
				'user_id'         => 0,
				'user_email'      => '',
				'limit'           => -1,
				'offset'          => 0,
				'return'          => 'objects',
			)
		);

		$table  = StockNotificationsDataStore::get_logs_table_name();
		$select = '*';
		if ( 'count' === $args['return'] ) {
			$select = 'COUNT(*)';
		}

		// WHERE clauses.
		$where        = array();
		$where_values = array();

		if ( $args['notification_id'] ) {
			$where[]        = "notification_id = %d";
			$where_values[] = absint( $args['notification_id'] );
		}

		if ( $args['action'] ) {
			$where[]        = "action = %s";
			$where_values[] = esc_sql( $args['action'] );
		}

		if ( $args['user_id'] ) {
			$where[]        = "user_id = %d";
			$where_values[] = absint( $args['user_id'] );
		}

		if ( $args['user_email'] ) {
			$where[]        = "user_email = %s";
			$where_values[] = esc_sql( $args['user_email'] );
		}

		// Assemble the query.
		$where  = implode( ' AND ', $where );
		$where  = $where ? ' WHERE ' . $where : '';
		$limit  = $args['limit'] > 0 ? ' LIMIT ' . absint( $args['limit'] ) : '';
		$offset = $args['offset'] > 0 ? ' OFFSET ' . absint( $args['offset'] ) : '';
		$sql    = "SELECT $select FROM $table $where $limit $offset";

		// Prepare the query.
		$prepared_sql = empty( $where_values ) ? $sql : $wpdb->prepare( $sql, $where_values );

		// Execute the query.
		if ( 'count' === $args['return'] ) {
			return (int) $wpdb->get_var( $prepared_sql );
		}

		$results = $wpdb->get_results( $prepared_sql, ARRAY_A );
		if ( empty( $results ) ) {
			return array();
		}

		return $results;
	}
}
