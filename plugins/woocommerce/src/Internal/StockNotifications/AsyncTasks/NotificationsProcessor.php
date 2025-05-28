<?php

declare( strict_types = 1 );

namespace Automattic\WooCommerce\Internal\StockNotifications\AsyncTasks;

use Automattic\WooCommerce\Internal\StockNotifications\Enums\NotificationStatus;
use Automattic\WooCommerce\Internal\StockNotifications\Emails\EmailManager;
use Automattic\WooCommerce\Internal\StockNotifications\NotificationQuery;
use Automattic\WooCommerce\Internal\StockNotifications\Factory;
use Automattic\WooCommerce\Enums\ProductStatus;
use Automattic\WooCommerce\Enums\ProductType;
use Automattic\WooCommerce\Enums\ProductStockStatus;
use Automattic\WooCommerce\Internal\StockNotifications\Notification;
use WC_Product;
use Exception;

/**
 * The async processor for sending stock notifications in bulk.
 */
class NotificationsProcessor {

	/*
	|--------------------------------------------------------------------------
	| Constants.
	|--------------------------------------------------------------------------
	*/

	/**
	 * The batch size for processing notifications.
	 */
	protected const BATCH_SIZE = 50;

	/**
	 * The delay for the first batch.
	 */
	protected const FIRST_BATCH_DELAY = MINUTE_IN_SECONDS;

	/**
	 * The spam threshold for processing notifications.
	 */
	protected const SPAM_THRESHOLD = 0;

	/**
	 * The job hook for sending stock notifications.
	 */
	protected const AS_JOB_SEND_STOCK_NOTIFICATIONS = 'wc_send_stock_notifications_batch';

	/**
	 * AS job group.
	 */
	protected const AS_JOB_GROUP = 'wc-stock-notifications';

	/**
	 * State option prefix.
	 */
	protected const STATE_OPTION_PREFIX = 'wc_stock_notifications_cycle_state_';

	/**
	 * The cycle state manager.
	 *
	 * @var CycleStateManager
	 */
	private CycleStateManager $state;

	/**
	 * Initialize the controller.
	 *
	 * @internal
	 */
	final public function init(): void {
		add_action( 'woocommerce_stock_notifications_product_sync', array( $this, 'schedule' ) );
		add_action( self::AS_JOB_SEND_STOCK_NOTIFICATIONS, array( $this, 'process_batch' ) );

		$this->state = new CycleStateManager(); // @todo: DI.
	}

	/*
	|--------------------------------------------------------------------------
	| Initial Schedule (via StockSyncController).
	|--------------------------------------------------------------------------
	*/

	/**
	 * Schedule a notification job for a specific product.
	 *
	 * @param int   $product_id  The product ID.
	 * @param array $product_ids All product IDs being processed (for filter context).
	 * @return bool True if job was scheduled, false otherwise.
	 */
	public function schedule( $product_id ) {
		$args = array( 'product_id' => $product_id );

		try {
			// @todo: Or if running.
			if ( WC()->queue()->get_next( self::AS_JOB_SEND_STOCK_NOTIFICATIONS, array( 'args' => $args ), self::AS_JOB_GROUP) ) {
				return false;
			}

			/**
			 * Filter: woocommerce_stock_notifications_first_batch_delay
			 *
			 * @since 0.0.0
			 *
			 * Schedule the first batch with a delay to prevent overwhelming the system.
			 *
			 * @param int   $delay       Delay time in seconds before first batch.
			 * @param int   $product_id  Product ID being scheduled.
			 */
			$delay = (int) apply_filters( 'woocommerce_stock_notifications_first_batch_delay', self::FIRST_BATCH_DELAY, $product_id );
			$delay = max( 0, $delay );

			WC()->queue()->schedule_single(
				time() + $delay,
				self::AS_JOB_SEND_STOCK_NOTIFICATIONS,
				array( 'args' => $args ),
				self::AS_JOB_GROUP
			);

			wc_get_logger()->info(
				sprintf( 'Scheduled stock notification for product %d', $product_id ),
				array( 'source' => 'wc-stock-notifications' )
			);

			return true;
		} catch ( Exception $e ) {
			wc_get_logger()->error(
				sprintf( 'Failed to schedule stock notification for product %d: %s', $product_id, $e->getMessage() ),
				array( 'source' => 'wc-stock-notifications' )
			);

			return false;
		}
	}

	/**
	 * Schedule the next batch for a product.
	 *
	 * @param int $product_id The product ID.
	 * @return bool
	 */
	private function schedule_next_batch( int $product_id ): bool {
		$scheduled = WC()->queue()->add(
			self::AS_JOB_SEND_STOCK_NOTIFICATIONS,
			array( 'args' => array( 'product_id' => $product_id ) ),
			self::AS_JOB_GROUP
		);

		return ! empty( $scheduled );
	}

	/*
	|--------------------------------------------------------------------------
	| Batch processing.
	|--------------------------------------------------------------------------
	*/

	/**
	 * Get the batch size for processing notifications.
	 *
	 * @return int
	 */
	private function get_batch_size(): int {
		/**
		 * Filter: woocommerce_stock_notifications_batch_size
		 *
		 * @since 0.0.0
		 *
		 * Allow customization of batch size for processing notifications.
		 *
		 * @param int $batch_size Default batch size.
		 * @return int
		 */
		return (int) apply_filters( 'woocommerce_stock_notifications_batch_size', self::BATCH_SIZE );
	}

	/**
	 * Parse the product ID from the arguments.
	 *
	 * @param array $args The arguments for the batch.
	 * @return int
	 * @throws \Exception If the product is not found.
	 */
	private function parse_args( $args ): int {
		if ( ! is_array( $args ) || ! isset( $args['product_id'] ) || ! is_numeric( $args['product_id'] ) ) {
			throw new \Exception( 'Invalid arguments.' );
		}

		$product_id = (int) $args['product_id'];
		if ( $product_id <= 0 ) {
			throw new \Exception( 'Product ID is required.' );
		}

		return $product_id;
	}

	/**
	 * Parse the product.
	 *
	 * @param int $product_id The product ID.
	 * @return \WC_Product
	 * @throws \Exception If the product is not valid for notifications.
	 */
	private function parse_product( int $product_id ): WC_Product {

		$product = wc_get_product( $product_id );
		if ( ! $product instanceof WC_Product ) {
			throw new \Exception( sprintf( 'Product %d not found.', $product_id ) );
		}

		$valid_types      = array( ProductType::SIMPLE, ProductType::VARIABLE, ProductType::VARIATION ); // @todo: make this reusable.
		$has_valid_type   = in_array( $product->get_type(), $valid_types, true );

		if ( ! $has_valid_type ) {
			throw new \Exception( sprintf( 'Product %d is not a valid type for notifications.', $product->get_id() ) );
		}

		$valid_statuses   = array( ProductStockStatus::IN_STOCK, ProductStockStatus::ON_BACKORDER ); // @todo: make this reusable.
		$has_valid_status = in_array( $product->get_stock_status(), $valid_statuses, true );

		if ( ! $has_valid_status ) {
			throw new \Exception( sprintf( 'Product %d is not valid for notifications (i.e. not in stock).', $product->get_id() ) );
		}

		return $product;
	}

	/**
	 * Parse the cycle state for a product.
	 *
	 * @param int $product_id The product ID.
	 * @return array
	 * @throws \Exception If the cycle state is invalid.
	 */
	private function parse_state( int $product_id ): array {
		$this->state->set_product_id( $product_id );
		if ( ! $this->state->exists() ) {
			return $this->state->initialize();
		}

		if ( ! $this->state->validate() ) {
			throw new \Exception( 'Invalid cycle state.' );
		}

		// @todo
		// if ( $product->is_type( ProductType::VARIABLE ) ) {
		// 	$this->handle_variable_product( $product ); // @todo: Maybe incorporate this int the state initialization.
		// }

		return $this->state->get();
	}

	/**
	 * Process a batch of notifications.
	 *
	 * @param array $args The arguments for the batch.
	 * @return void
	 */
	public function process_batch( $args ) {

		// Sanity checks.
		try {
			$product_id  = $this->parse_args( $args );
			$product     = $this->parse_product( $product_id );
			$cycle_state = $this->parse_state( $product_id );
		} catch ( \Throwable $e ) {
			$product_id = $args['product_id'] ?? 'unknown';
			wc_get_logger()->error(
				sprintf( 'Background process for product %s terminated. Reason: %s', $product_id, $e->getMessage() ),
				array(
					'source' => 'wc-stock-notifications',
					'product_id' => $product_id,
					'exception' => get_class( $e )
				)
			);

			// Clean up state if it was initialized.
			if ( isset( $product ) && $product instanceof \WC_Product ) {
				$this->state->set_product_id( $product->get_id() );
				if ( $this->state->exists() ) {
					$this->state->delete();
				}
			}

			return;
		}

		// Get notifications.
		$notifications = NotificationQuery::get_notifications(
			array(
				'status'             => NotificationStatus::ACTIVE,
				'product_id'         => $product->get_id(),
				'last_attempt_limit' => (int) $cycle_state['cycle_start_time'],
				'return'             => 'ids',
				'limit'              => $this->get_batch_size(),
				'orderby'            => 'id',
				'order'              => 'ASC',
			)
		);

		if ( empty( $notifications ) ) {
			$this->state->complete();
			return;
		}

		// Send notifications.
		$container     = wc_get_container();
		$email_manager = $container->get( EmailManager::class ); // @todo: DI.

		foreach ( $notifications as $notification_id ) {
			$notification = Factory::get_notification( $notification_id );
			if ( ! $notification instanceof Notification ) {
				wc_get_logger()->error(
					sprintf( 'Failed to get notification ID: %d', $notification_id ),
					array( 'source' => 'wc-stock-notifications' )
				);
				continue;
			}

			$notification->set_date_last_attempt( time() );
			$cycle_state['total_count']++;

			if ( $this->should_skip_notification( $notification ) ) {
				$cycle_state['skipped_count']++;
				$notification->save();
				continue;
			}

			$is_sent = true;
			try {
				$res = $email_manager->send_stock_notification_email( $notification );
				if ( ! $res ) {
					$is_sent = false;
				}
			} catch ( \Throwable $e ) {
				$is_sent = false;
			}

			if ( ! $is_sent ) {
				wc_get_logger()->error(
					sprintf( 'Failed to send stock notification ID: %d', $notification->get_id() ),
					array( 'source' => 'wc-stock-notifications' )
				);
				$cycle_state['failed_count']++;
				$notification->set_status( NotificationStatus::CANCELLED );
				$notification->set_cancellation_source( 'system' ); // @todo: make this reusable.
			} else {
				$notification->set_date_notified( time() );
				$notification->set_status( NotificationStatus::SENT );
				$cycle_state['sent_count']++;
			}

			// Always save the notification to reflect last attempt time.
			$notification->save();

			// ==== TEST ====
			error_log( print_r( $cycle_state, true ) );
			if ( $cycle_state['total_count'] > 10 ) {
				$this->state->complete();
				return;
			}
			// ==== TEST ====
		}

		if ( count( $notifications ) === $this->get_batch_size() ) {
			$this->state->save( $cycle_state );
			$this->schedule_next_batch( $product_id );
			return;
		}

		$this->state->complete();
	}

	// /**
	//  * Process a batch of notifications.
	//  *
	//  * @param array $args The arguments for the batch.
	//  * @return void
	//  */
	// public function process_batch( $args ) {

	// 	if ( empty( $args['product_id'] ) || ! is_numeric( $args['product_id'] ) ) {
	// 		wc_get_logger()->error(
	// 			'NotificationsAsyncProcessor: Invalid product ID provided',
	// 			array( 'source' => 'wc-stock-notifications' )
	// 		);
	// 		return;
	// 	}

	// 	$product_id = (int) $args['product_id'];
	// 	$product    = wc_get_product( $product_id );
	// 	if ( ! $product ) {
	// 		wc_get_logger()->error(
	// 			sprintf( 'NotificationsAsyncProcessor: Product %d not found', $product_id ),
	// 			array( 'source' => 'wc-stock-notifications' )
	// 		);
	// 		return;
	// 	}

	// 	if ( ! $this->is_product_valid( $product ) ) {
	// 		wc_get_logger()->debug(
	// 			sprintf( 'NotificationsAsyncProcessor: Product %d is not valid for notifications', $product_id ),
	// 			array( 'source' => 'wc-stock-notifications' )
	// 		);
	// 		return;
	// 	}

	// 	if ( $product->is_type( ProductType::VARIABLE ) ) {
	// 		$this->handle_variable_product( $product ); // @todo: Maybe incorporate this int the state initialization.
	// 	}

	// 	// Get or initialize cycle state.
	// 	$this->state->set_product_id( $product_id );
	// 	if ( ! $this->state->exists() ) {
	// 		$cycle_state = $this->state->initialize();
	// 	} else {
	// 		$cycle_state = $this->state->get();
	// 	}

	// 	if ( ! $this->state->validate() ) {
	// 		wc_get_logger()->error(
	// 			sprintf( 'NotificationsAsyncProcessor: Invalid cycle state for product %d', $product_id ),
	// 			array( 'source' => 'wc-stock-notifications' )
	// 		);
	// 		$this->state->delete();
	// 		return;
	// 	}

	// 	$notifications = NotificationQuery::get_notifications(
	// 		array(
	// 			'product_id'         => $product_id,
	// 			'status'             => NotificationStatus::ACTIVE,
	// 			'last_attempt_limit' => (int) $cycle_state['cycle_start_time'],
	// 			'limit'              => $this->get_batch_size(),
	// 			'return'             => 'objects',
	// 		)
	// 	);

	// 	if ( empty( $notifications ) ) {
	// 		$this->state->complete();
	// 		return;
	// 	}

	// 	$container     = wc_get_container();
	// 	$email_manager = $container->get( EmailManager::class ); // @todo: DI.

	// 	foreach ( $notifications as $notification ) {
	// 		$notification->set_date_last_attempt( time() );
	// 		$cycle_state['total_count']++;

	// 		if ( $this->is_spam_throttled( $notification ) ) {
	// 			$cycle_state['spare_count']++;
	// 			$notification->save();
	// 			continue;
	// 		}

	// 		/**
	// 		 * Filter: woocommerce_stock_notification_pre_send
	// 		 *
	// 		 * @since 0.0.0
	// 		 *
	// 		 * Prevent or manage sending a specific notification.
	// 		 *
	// 		 * @param bool|null $pre_send       Whether to skip sending (null = don't skip).
	// 		 * @param int       $notification_id The notification ID.
	// 		 * @return bool|null
	// 		 */
	// 		$pre_send = apply_filters( 'woocommerce_stock_notification_pre_send', null, $notification->get_id() );
	// 		if ( ! is_null( $pre_send ) ) {
	// 			$cycle_state['skipped_count']++;
	// 			$notification->save();
	// 			continue;
	// 		}

	// 		$is_sent = true;
	// 		try {
	// 			$res = $email_manager->send_stock_notification_email( $notification );
	// 			if ( ! $res ) {
	// 				$is_sent = false;
	// 			}
	// 		} catch ( Exception $e ) {
	// 			$is_sent = false;
	// 		}

	// 		if ( ! $is_sent ) {
	// 			wc_get_logger()->error(
	// 				sprintf( 'NotificationsAsyncProcessor: Failed to send stock notification for product %d: %s', $product_id, $e->getMessage() ),
	// 				array( 'source' => 'wc-stock-notifications' )
	// 			);
	// 		} else {
	// 			$notification->set_date_notified( time() );
	// 			$notification->set_status( NotificationStatus::SENT );
	// 			$cycle_state['sent_count']++;
	// 		}

	// 		// Always save the notification to reflect last attempt time.
	// 		$notification->save();

	// 		// ==== TEST ====
	// 		error_log( print_r( $cycle_state, true ) );
	// 		if ( $cycle_state['total_count'] > 10 ) {
	// 			$this->state->complete();
	// 			return;
	// 		}
	// 		// ==== TEST ====
	// 	}

	// 	if ( count( $notifications ) === $this->get_batch_size() ) {
	// 		$this->state->save( $cycle_state );
	// 		$this->schedule_next_batch( $product_id );
	// 	} else {
	// 		$this->state->complete();
	// 	}
	// }

	/**
	 * Check if a notification should be skipped.
	 *
	 * @param Notification $notification The notification object.
	 * @return bool
	 */
	private function should_skip_notification( Notification $notification ): bool {

		$is_throttled         = $this->is_notification_throttled( $notification );
		$is_product_published = $notification->get_product()->get_status() === ProductStatus::PUBLISHED;
		$should_skip          = $is_throttled || $is_product_published;

		// Bypass for privileged users.
		if ( $should_skip ) {
			$user_id = $notification->get_user_id();
			if ( $user_id ) {
				$user = get_user_by( 'id', $user_id );
				if ( $user && ( user_can( $user, 'manage_woocommerce' ) || user_can( $user, 'administrator' ) ) ) {
					$should_skip = false;
				}
			}
		}

		/**
		 * Filter: woocommerce_stock_notification_pre_send
		 *
		 * @since 0.0.0
		 *
		 * Prevent or manage sending a specific notification.
		 *
		 * @param bool $should_skip Whether to skip sending.
		 * @param int  $notification_id The notification ID.
		 * @return bool
		 */
		return (bool) apply_filters( 'woocommerce_stock_notification_should_skip_sending', $should_skip, $notification->get_id() );
	}

	/**
	 * Check if notification is throttled.
	 *
	 * @param Notification $notification The notification object.
	 * @return void
	 * @throws \Exception If the notification is throttled.
	 */
	private function is_notification_throttled( $notification ): void {

		/**
		 * Filter: woocommerce_stock_notifications_throttle_threshold
		 *
		 * @since 0.0.0
		 *
		 * @param int $threshold Throttle time in seconds should pass from the last notification delivery time.
		 */
		$threshold = (int) apply_filters( 'woocommerce_stock_notifications_throttle_threshold', self::SPAM_THRESHOLD );

		if ( $threshold <= 0 ) {
			return;
		}

		$last_notified = $notification->get_date_notified();
		$is_throttled = $last_notified instanceof \WC_DateTime && $last_notified->getTimestamp() > ( time() - $threshold );

		if ( $is_throttled ) {
			throw new \Exception( sprintf( 'Notification %d is throttled.', $notification->get_id() ) );
		}
	}

	/*
	|--------------------------------------------------------------------------
	| Product status helpers.
	|--------------------------------------------------------------------------
	*/

	/**
	 * Manages variable products.
	 *
	 * Variations that manage stock based on the parent are automatically included in the cycle.
	 *
	 * @param \WC_Product $product The variable product.
	 * @return void
	 */
	private function handle_variable_product( \WC_Product $product ): void {
		$variation_ids = $product->get_children();

		foreach ( $variation_ids as $variation_id ) {
			$variation = wc_get_product( $variation_id );

			if ( ! $variation ) {
				continue;
			}

			// Check if variation manages stock at parent level and is valid
			if ( ! $variation->managing_stock() && $this->is_product_valid( $variation ) ) {
				// Schedule a separate process for this variation
				// $this->continue_cycle( $variation_id );
			}
		}
	}
}