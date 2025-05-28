<?php

declare( strict_types = 1 );

namespace Automattic\WooCommerce\Internal\StockNotifications\AsyncTasks;

/**
 * Manages notification processing cycle state.
 *
 * @todo: DI for logger, emailmanager.
 */
class CycleStateManager {

	/**
	 * Option prefix for cycle state keys.
	 */
	private const OPTION_PREFIX = 'wc_stock_notifications_cycle_state_';

	/**
	 * The product ID.
	 *
	 * @var int
	 */
	private $product_id;

	/**
	 * Set the product ID.
	 *
	 * @param int $product_id The product ID.
	 */
	public function set_product_id( int $product_id ): void {
		$this->product_id = $product_id;
	}

	/**
	 * Initialize cycle state for a product.
	 *
	 * @return array
	 */
	public function initialize(): array {
		if ( ! $this->product_id ) {
			\wc_doing_it_wrong( __METHOD__, 'Product ID is not set', '0.0.0' );
		}

		$state = array(
			'product_id'       => $this->product_id,
			'sent_count'       => 0,
			'failed_count'     => 0,
			'skipped_count'    => 0,
			'total_count'      => 0,
			'cycle_start_time' => time(),
		);

		$this->save( $product_id, $state );
		return $state;
	}

	/**
	 * Get cycle state for a product.
	 *
	 * @return array|null Cycle state array or null if not found.
	 */
	public function get()  {
		if ( ! $this->product_id ) {
			\wc_doing_it_wrong( __METHOD__, 'Product ID is not set', '0.0.0' );
			return null;
		}

		$cycle_key = $this->get_key( $this->product_id );
		$state = get_option( $cycle_key, array() );

		if ( empty( $state ) ) {
			return null;
		}

		if ( ! $this->validate( $state ) ) {
			$this->delete( $product_id );
			return null;
		}

		return $state;
	}

	/**
	 * Save cycle state for a product.
	 *
	 * @param array $state      The cycle state.
	 * @return bool
	 */
	public function save( array $state ): bool {
		if ( ! $this->product_id ) {
			\wc_doing_it_wrong( __METHOD__, 'Product ID is not set', '0.0.0' );
			return false;
		}

		$cycle_key = $this->get_key( $this->product_id );
		return update_option( $cycle_key, $state, false );
	}

	/**
	 * Complete and clean up cycle state.
	 *
	 * @return array|null The final cycle state before cleanup, or null if not found.
	 */
	public function complete() {
		$state = $this->get();
		if ( null === $state ) {
			return null;
		}

		$stats = $this->get_statistics();

		// Log completion.
		wc_get_logger()->info(
			sprintf(
				'NotificationsAsyncProcessor: Completed cycle for product %d. Sent: %d, Failed: %d, Skipped: %d, Total: %d, Duration: %d seconds',
				$stats['product_id'],
				$stats['sent_count'],
				$stats['failed_count'],
				$stats['skipped_count'],
				$stats['total_count'],
				$stats['duration']
			),
			array( 'source' => 'wc-stock-notifications' )
		);

		$this->delete();
		return $state;
	}

	/**
	 * Delete cycle state for a product.
	 *
	 * @return bool
	 */
	public function delete(): bool {
		$cycle_key = $this->get_key();
		if ( ! $cycle_key ) {
			return false;
		}

		return delete_option( $cycle_key );
	}

	/**
	 * Check if cycle exists for a product.
	 *
	 * @return bool
	 */
	public function exists(): bool {
		return null !== $this->get();
	}

	/**
	 * Validate the cycle state structure.
	 *
	 * @param array $cycle_state The cycle state.
	 * @return bool True if the cycle state is valid, false otherwise.
	 */
	public function validate(): bool {
		$cycle_state = $this->get();
		if ( null === $cycle_state ) {
			return false;
		}

		$required_keys = array(
			'product_id',
			'sent_count',
			'failed_count',
			'skipped_count',
			'total_count',
			'cycle_start_time'
		);

		foreach ( $required_keys as $key ) {
			if ( ! array_key_exists( $key, $cycle_state ) ) {
				wc_get_logger()->error(
					sprintf( 'CycleStateManager: Missing required key "%s" in cycle state', $key ),
					array( 'source' => 'wc-stock-notifications' )
				);
				return false;
			}
		}

		if ( ! is_numeric( $cycle_state['product_id'] ) ||
			 ! is_numeric( $cycle_state['cycle_start_time'] ) ||
			 $cycle_state['cycle_start_time'] <= 0 ) {
			wc_get_logger()->error(
				'CycleStateManager: Invalid cycle state data types or values',
				array( 'source' => 'wc-stock-notifications' )
			);
			return false;
		}

		return true;
	}

	/**
	 * Get cycle state statistics.
	 *
	 * @return array Statistics array with counters.
	 */
	public function get_statistics(): array {
		$state = $this->get();
		if ( null === $state ) {
			return array();
		}

		return array(
			'sent_count'       => $state['sent_count'],
			'failed_count'     => $state['failed_count'],
			'skipped_count'    => $state['skipped_count'],
			'total_count'      => $state['total_count'],
			'cycle_start_time' => $state['cycle_start_time'],
			'duration'         => time() - $state['cycle_start_time'],
		);
	}

	/**
	 * Get the cycle option key for a product.
	 *
	 * @return string
	 */
	private function get_key(): string {
		if ( ! $this->product_id ) {
			\wc_doing_it_wrong( __METHOD__, 'Product ID is not set', '0.0.0' );
			return '';
		}

		return self::OPTION_PREFIX . $this->product_id;
	}
}