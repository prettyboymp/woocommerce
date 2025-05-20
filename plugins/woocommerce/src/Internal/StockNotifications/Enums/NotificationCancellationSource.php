<?php

declare(strict_types=1);

namespace Automattic\WooCommerce\Internal\StockNotifications\Enums;

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
		return [
			self::ADMIN,
			self::USER,
		];
	}
}
