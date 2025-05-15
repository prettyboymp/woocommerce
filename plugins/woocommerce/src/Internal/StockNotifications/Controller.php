<?php

declare( strict_types = 1 );

namespace Automattic\WooCommerce\Internal\StockNotifications;

use Automattic\WooCommerce\Internal\DataStores\StockNotifications\StockNotificationsDataStore;
use Automattic\WooCommerce\Internal\StockNotifications\Templates;
use Automattic\WooCommerce\Internal\StockNotifications\Emails;

/**
 * The controller for the stock notifications.
 */
class Controller {

	/**
	 * Initialize the controller.
	 *
	 * @internal
	 */
	final public function init() {
		$this->init_feature();
		add_filter( 'woocommerce_data_stores', array( $this, 'register_data_stores' ) );
	}

	/**
	 * Initialize the controller.
	 *
	 * @internal
	 */
	private function init_feature() {
		$container = wc_get_container();
		$container->get( Templates::class );
		$container->get( Emails::class );
	}

	 /**
	 * Register the data stores.
	 *
	 * @param array $data_stores Data stores.
	 * @return array
	 */
	public function register_data_stores( $data_stores ) {
		$data_stores['stock_notification'] = wc_get_container()->get( StockNotificationsDataStore::class );
		return $data_stores;
	}
}
