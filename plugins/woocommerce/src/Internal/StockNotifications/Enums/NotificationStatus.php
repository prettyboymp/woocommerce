<?php

declare( strict_types = 1 );

namespace Automattic\WooCommerce\Internal\StockNotifications\Enums;

/**
 * Enum class for all the notification statuses.
 */
final class NotificationStatus {
	/**
	 * The notification has been created but not yet confirmed.
	 *
	 * @var string
	 */
	const PENDING = 'pending';

	/**
	 * The notification has been created and confirmed.
	 *
	 * @var string
	 */
	const ACTIVE = 'active';

	/**
	 * The notification has been sent.
	 *
	 * @var string
	 */
	const SENT = 'sent';

	/**
	 * The notification has been cancelled.
	 *
	 * @var string
	 */
	const CANCELLED = 'cancelled';

	/**
	 * Get all available notification statuses.
	 *
	 * @return array<string> Notification statuses.
	 */
	public static function get_valid_statuses(): array {
		return array(
			self::PENDING,
			self::ACTIVE,
			self::SENT,
			self::CANCELLED,
		);
	}
}
