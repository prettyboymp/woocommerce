<?php

declare(strict_types=1);

namespace Automattic\WooCommerce\Internal\StockNotifications\Enums;

/**
 * Notification cancellation source enum.
 */
class NotificationCancellationSource {

	/**
	 * Admin cancellation source.
	 *
	 * @var string
	 */
	const ADMIN = 'admin';

	/**
	 * User cancellation source.
	 *
	 * @var string
	 */
	const USER = 'user';

	/**
	 * Get valid cancellation sources.
	 *
	 * @return string[]
	 */
	public static function get_valid_cancellation_sources(): array {
		return array(
			self::ADMIN,
			self::USER,
		);
	}
}
