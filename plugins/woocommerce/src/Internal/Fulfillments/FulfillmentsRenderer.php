<?php
/**
 * WooCommerce Fulfillments Admin.
 */

declare( strict_types=1 );

namespace Automattic\WooCommerce\Internal\Fulfillments;

use Automattic\WooCommerce\Internal\Admin\WCAdminAssets;
use Automattic\WooCommerce\Internal\DataStores\Fulfillments\FulfillmentsDataStore;
use WC_Order;

/**
 * FulfillmentsAdmin class.
 */
class FulfillmentsRenderer {

	/**
	 * Fulfillments cache.
	 *
	 * @var array
	 */
	private array $fulfillments_cache = array();

	/**
	 * CLass constructor.
	 */
	public function __construct() {
		add_filter( 'manage_woocommerce_page_wc-orders_columns', array( $this, 'add_fulfillment_status_column' ) );
		add_action( 'manage_woocommerce_page_wc-orders_custom_column', array( $this, 'render_fulfillment_status_column' ), 10, 2 );
		add_action( 'admin_footer', array( $this, 'render_fulfillment_drawer_slot' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'load_components' ) );
	}

	/**
	 * Add the fulfillment status column to the orders page.
	 *
	 * @param array $columns The columns in the orders page.
	 * @return array The modified columns.
	 */
	public function add_fulfillment_status_column( $columns ) {
		$new_columns = array();
		foreach ( $columns as $column_name => $column_info ) {
			$new_columns[ $column_name ] = $column_info;
			if ( 'order_status' === $column_name ) {
				$new_columns[ $column_name ]       = 'Order Status';
				$new_columns['fulfillment_status'] = __( 'Fulfillment Status', 'woocommerce' );
				$new_columns['shipment_tracking']  = __( 'Shipment Tracking', 'woocommerce' );
				$new_columns['shipment_provider']  = __( 'Shipment Provider', 'woocommerce' );
			}
		}
		return $new_columns;
	}


	/**
	 * Render the fulfillment status column.
	 *
	 * @param string   $column_name The name of the column.
	 * @param WC_Order $order The order object.
	 */
	public function render_fulfillment_status_column( string $column_name, WC_Order $order ) {
		$fulfillments = $this->fulfillments_cache[ $order->get_id() ] ?? null;
		if ( null === $fulfillments ) {
			$manager                                      = wc_get_container()->get( FulfillmentsDataStore::class );
			$fulfillments                                 = $manager->read_fulfillments( WC_Order::class, '' . $order->get_id() );
			$this->fulfillments_cache[ $order->get_id() ] = $fulfillments;
		}
		switch ( $column_name ) {
			case 'fulfillment_status':
				$this->render_fulfillment_status( $order, $fulfillments );
				break;
			case 'shipment_tracking':
				$this->render_shipment_tracking_column( $order, $fulfillments );
				break;
			case 'shipment_provider':
				$this->render_shipment_provider_column( $order, $fulfillments );
				break;
		}
	}

	/**
	 * Render the fulfillment status column.
	 *
	 * @param WC_Order      $order The order object.
	 * @param Fulfillment[] $fulfillments The fulfillments.
	 */
	private function render_fulfillment_status( WC_Order $order, array $fulfillments ) {
		$order_fulfillment_status = $this->get_fulfillment_status( $fulfillments );
		switch ( $order_fulfillment_status ) {
			case 'no_fulfillments':
			case 'unfulfilled':
				echo '<mark class="fulfillment-status status-failed"><span>' . esc_html__( 'Unfulfilled', 'woocommerce' ) . '</span></mark>';
				break;
			case 'partially_fulfilled':
				echo '<mark class="fulfillment-status status-completed"><span>' . esc_html__( 'Partially fulfilled', 'woocommerce' ) . '</span></mark>';
				break;
			case 'fulfilled':
				echo '<mark class="fulfillment-status status-processing"><span>' . esc_html__( 'Fulfilled', 'woocommerce' ) . '</span></mark>';
				break;
			default:
				echo '<mark class="fulfillment-status status-completed"><span>' . esc_html__( 'Unknown', 'woocommerce' ) . '</span></mark>';
		}
		echo "<a href='#' class='fulfillments-trigger' data-order-id='" . esc_attr( $order->get_id() ) . "' title='" . esc_attr__( 'View Fulfillments', 'woocommerce' ) . "'>
			<svg width='16' height='16' viewBox='0 0 12 14' fill='none' xmlns='http://www.w3.org/2000/svg'>
				<path d='M11.8333 2.83301L9.33329 0.333008L2.24996 7.41634L1.41663 10.7497L4.74996 9.91634L11.8333 2.83301ZM5.99996 12.4163H0.166626V13.6663H5.99996V12.4163Z' fill='#3858E9'/>
			</svg>
		</a>";
	}

	/**
	 * Render the shipment tracking column.
	 *
	 * @param WC_Order $order The order object.
	 * @param array    $fulfillments The fulfillments.
	 */
	private function render_shipment_tracking_column( WC_Order $order, array $fulfillments ) {
		$providers = array();
		foreach ( $fulfillments as $fulfillment ) {
			$providers[] = $fulfillment->get_meta( '_shipping_provider' ) ?? null;
		}

		$providers = array_filter(
			$providers,
			function ( $provider ) {
				return ! empty( $provider );
			}
		);

		if ( count( $providers ) > 1 ) {
			echo '<span>' . esc_html__( 'Multiple providers', 'woocommerce' ) . '</span>';
		} elseif ( 1 === count( $providers ) ) {
			echo '<span>' . esc_html( array_shift( $providers ) ) . '</span>';
		} else {
			echo '<span>--</span>';
		}
	}

	/**
	 * Render the shipment provider column.
	 *
	 * @param WC_Order $order The order object.
	 * @param array    $fulfillments The fulfillments.
	 */
	private function render_shipment_provider_column( WC_Order $order, array $fulfillments ) {
		$tracking = array();
		foreach ( $fulfillments as $fulfillment ) {
			$tracking[] = $fulfillment->get_meta( '_tracking_number' ) ?? null;
		}

		$tracking = array_filter(
			$tracking,
			function ( $provider ) {
				return ! empty( $provider );
			}
		);

		if ( count( $tracking ) > 1 ) {
			echo '<span>' . esc_html__( 'Multiple trackings', 'woocommerce' ) . '</span>';
		} elseif ( 1 === count( $tracking ) ) {
			echo '<span>' . esc_html( array_shift( $tracking ) ) . '</span>';
		} else {
			echo '<span>--</span>';
		}
	}

	/**
	 * Get the fulfillment status of the entity. This acts like a computed property.
	 *
	 * @param array $fulfillments The fulfillments.
	 *
	 * @return string The fulfillment status.
	 */
	private function get_fulfillment_status( array $fulfillments ): string {
		$has_fulfillments = ! empty( $fulfillments );
		$all_fulfilled    = true;
		$some_fulfilled   = false;

		if ( $has_fulfillments ) {
			foreach ( $fulfillments as $fulfillment ) {
				if ( ! $fulfillment->get_is_fulfilled() ) {
					$all_fulfilled = false;
				} else {
					$some_fulfilled = true;
				}
			}

			if ( $all_fulfilled ) {
				return 'fulfilled';
			} elseif ( $some_fulfilled ) {
				return 'partially_fulfilled';
			} else {
				return 'unfulfilled';
			}
		} else {
			return 'no_fulfillments';
		}
	}

	/**
	 * Render the fulfillment drawer.
	 */
	public function render_fulfillment_drawer_slot() {
		if ( get_current_screen()->id !== 'woocommerce_page_wc-orders' ) {
			return;
		}
		?>
		<div id="wc_order_fulfillments_panel_container"></div>
		<?php
	}

	/**
	 * Loads the payment method promotions scripts and styles.
	 */
	public static function load_components() {
		if ( get_current_screen()->id !== 'woocommerce_page_wc-orders' ) {
			return;
		}
		WCAdminAssets::register_style( 'fulfillments', 'style', array( 'wp-components' ) );
		WCAdminAssets::register_script( 'wp-admin-scripts', 'fulfillments', true );
	}
}
