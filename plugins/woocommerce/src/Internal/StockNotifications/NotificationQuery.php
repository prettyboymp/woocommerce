<?php
declare( strict_types=1 );

namespace Automattic\WooCommerce\Internal\StockNotifications;

class NotificationQuery {

	public static function get_notifications( array $args ): array {
		return \WC_Data_Store::load( 'stock_notification' )->query( $args );
	}

	public static function product_has_active_notifications( int $product_id ): bool {
		return \WC_Data_Store::load( 'stock_notification' )->product_has_active_notifications( $product_id );
	}
}