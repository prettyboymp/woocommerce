<?php // phpcs:ignore Suin.Classes.PSR4
/**
 * StockNotificationsServiceProvider class file.
 */

declare( strict_types = 1 );

namespace Automattic\WooCommerce\Internal\DependencyManagement\ServiceProviders;

use Automattic\WooCommerce\Internal\DependencyManagement\AbstractServiceProvider;
use Automattic\WooCommerce\Internal\DataStores\StockNotifications\StockNotificationsTableDataStore;
use Automattic\WooCommerce\Internal\Utilities\DatabaseUtil;

/**
 * Service provider for Back in Stock Notification classes.
 */
class StockNotificationsServiceProvider extends AbstractServiceProvider {

	/**
	 * The classes/interfaces that are serviced by this service provider.
	 *
	 * @var array
	 */
	protected $provides = array(
		StockNotificationsTableDataStore::class
	);

	/**
	 * Register the classes.
	 */
	public function register() {
		$this->share( StockNotificationsTableDataStore::class )->addArguments( array( DatabaseUtil::class ) );
	}
}
