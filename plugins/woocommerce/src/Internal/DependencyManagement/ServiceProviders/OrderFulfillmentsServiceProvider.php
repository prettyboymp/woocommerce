<?php
/**
 * OrderFulfillmentsRestControllerServiceProvider class file.
 */

declare( strict_types=1 );

namespace Automattic\WooCommerce\Internal\DependencyManagement\ServiceProviders;

use Automattic\WooCommerce\Internal\DependencyManagement\ServiceProviders\AbstractInterfaceServiceProvider;
use Automattic\WooCommerce\Internal\Fulfillments\OrderFulfillmentsRestController;

/**
 * Service provider for the order fulfillments controller classes in the Automattic\WooCommerce\Internal\Fulfillments namespace.
 *
 * @since 9.0.0
 */
class OrderFulfillmentsServiceProvider extends AbstractInterfaceServiceProvider {

	/**
	 * The classes/interfaces that are serviced by this service provider.
	 *
	 * @var array
	 */
	protected $provides = array(
		OrderFulfillmentsRestController::class,
	);

	/**
	 * Register the classes.
	 */
	public function register() {
		$this->share_with_implements_tags( OrderFulfillmentsRestController::class );
	}
}
