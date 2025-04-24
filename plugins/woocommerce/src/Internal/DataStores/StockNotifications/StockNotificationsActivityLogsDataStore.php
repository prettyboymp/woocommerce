<?php
/**
 * StockNotificationsActivityLogsDataStore class file.
 */

declare( strict_types = 1 );

namespace Automattic\WooCommerce\Internal\DataStores\StockNotifications;

defined( 'ABSPATH' ) || exit;

/**
 * The Stock Notifications Activity Logs Data Store.
 */
class StockNotificationsActivityLogsDataStore {

	/**
	 * Returns the name of the table used for storage.
	 *
	 * @return string
	 */
	public function get_table_name(): string {
		global $wpdb;
		return $wpdb->prefix . 'wc_stock_notifications_logs';
	}

	/**
	 * Create an activity log.
	 *
	 * @param array $args Additional arguments.
	 * @return int|false The log ID or false if the log was not created.
	 */
	public function create( $args ) {
		global $wpdb;

		$table = $this->get_table_name();
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

		$table  = $this->get_table_name();
		$select = '*';
		if ( 'count' === $args['return'] ) {
			$select = 'COUNT(*)';
		}

		// WHERE clauses.
		$where        = array();
		$where_values = array();

		if ( $args['notification_id'] ) {
			$where[]        = 'notification_id = %d';
			$where_values[] = absint( $args['notification_id'] );
		}

		if ( $args['action'] ) {
			$where[]        = 'action = %s';
			$where_values[] = esc_sql( $args['action'] );
		}

		if ( $args['user_id'] ) {
			$where[]        = 'user_id = %d';
			$where_values[] = absint( $args['user_id'] );
		}

		if ( $args['user_email'] ) {
			$where[]        = 'user_email = %s';
			$where_values[] = esc_sql( $args['user_email'] );
		}

		// Assemble the query.
		$where  = implode( ' AND ', $where );
		$where  = $where ? ' WHERE ' . $where : '';
		$limit  = $args['limit'] > 0 ? ' LIMIT ' . absint( $args['limit'] ) : '';
		$offset = $args['offset'] > 0 ? ' OFFSET ' . absint( $args['offset'] ) : '';
		$sql    = "SELECT $select FROM $table $where $limit $offset";

		// Prepare the query.
		$prepared_sql = empty( $where_values ) ? $sql : $wpdb->prepare( $sql, $where_values ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

		// Execute the query.
		if ( 'count' === $args['return'] ) {
			return (int) $wpdb->get_var( $prepared_sql ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		}

		$results = $wpdb->get_results( $prepared_sql, ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		if ( empty( $results ) ) {
			return array();
		}

		return $results;
	}
}
