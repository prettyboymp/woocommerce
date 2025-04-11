<?php // phpcs:ignore Suin.Classes.PSR4

/**
 * StockNotification class file.
 */

declare( strict_types = 1);

namespace Automattic\WooCommerce\Internal\StockNotifications;

defined( 'ABSPATH' ) || exit;

/**
 * Notification data class.
 */
class Notification extends \WC_Data {

	/**
	 * This is the name of this object type.
	 *
	 * @var string
	 */
	protected $object_type = 'stock_notification';

	/**
	 * Product. Runtime property.
	 *
	 * @var \WC_Product
	 */
	private $product;

	/**
	 * Default data.
	 *
	 * @var array
	 */
	protected $data = array(
		'product_id'      => 0,
		'user_id'         => 0,
		'user_email'      => '',
		'status'          => 'pending',
		'date_created'    => null,
		'date_modified'   => null,
		'date_subscribed' => null,
		'date_notified'   => null,
		'is_queued'       => 0,
	);

	/**
	 * Constructor.
	 *
	 * @param int|object|array $read ID to load from the DB (optional) or already queried data.
	 */
	public function __construct( $read = 0 ) {
		parent::__construct( $read );
		if ( is_numeric( $read ) && $read > 0 ) {
			$this->set_id( $read );
		} elseif ( $read instanceof self ) {
			$this->set_id( $read->get_id() );
		} elseif ( ! empty( $read->ID ) ) {
			$this->set_id( absint( $read->ID ) );
		} else {
			$this->set_object_read( true );
		}

		$this->data_store = \WC_Data_Store::load( 'stock_notification' );
		if ( $this->get_id() > 0 ) {
			$this->data_store->read( $this );
		}
	}

	/**
	 * Get the product ID.
	 *
	 * @return int
	 */
	public function get_product_id( $context = 'view' ) {
		return $this->get_prop( 'product_id', $context );
	}

	/**
	 * Get the user ID.
	 *
	 * @return int
	 */
	public function get_user_id( $context = 'view' ) {
		return $this->get_prop( 'user_id', $context );
	}

	/**
	 * Get the user email.
	 *
	 * @return string
	 */
	public function get_user_email( $context = 'view' ) {
		return $this->get_prop( 'user_email', $context );
	}

	/**
	 * Get the status.
	 *
	 * @return string
	 */
	public function get_status( $context = 'view' ) {
		return $this->get_prop( 'status', $context );
	}

	/**
	 * Get the date created.
	 *
	 * @return \WC_DateTime|null Datetime object if the date is set or null if there is no date.
	 */
	public function get_date_created( $context = 'view' ) {
		return $this->get_prop( 'date_created', $context );
	}

	/**
	 * Get the date modified.
	 *
	 * @return \WC_DateTime|null Datetime object if the date is set or null if there is no date.
	 */
	public function get_date_modified( $context = 'view' ) {
		return $this->get_prop( 'date_modified', $context );
	}

	/**
	 * Get the date subscribed.
	 *
	 * @return \WC_DateTime|null Datetime object if the date is set or null if there is no date.
	 */
	public function get_date_subscribed( $context = 'view' ) {
		return $this->get_prop( 'date_subscribed', $context );
	}

	/**
	 * Get the date notified.
	 *
	 * @return \WC_DateTime|null Datetime object if the date is set or null if there is no date.
	 */
	public function get_date_notified( $context = 'view' ) {
		return $this->get_prop( 'date_notified', $context );
	}

	/**
	 * Get the product.
	 *
	 * @return \WC_Product|false
	 */
	public function get_product() {
		if ( ! empty( $this->product ) ) {
			return $this->product;
		}

		$product = wc_get_product( $this->get_prop( 'product_id' ) );
		if ( ! $product ) {
			return false;
		}

		$this->product = $product;
		return $product;
	}

	/*
	|--------------------------------------------------------------------------
	| Setters
	|--------------------------------------------------------------------------
	*/

	/**
	 * Set the product ID.
	 *
	 * @param int $product_id
	 */
	public function set_product_id( $product_id ) {
		if ( is_a( $this->product, 'WC_Product' ) && $product_id !== $this->product->get_id() ) {
			$this->product = null;
		}
		$this->set_prop( 'product_id', $product_id );
	}

	/**
	 * Set the user ID.
	 *
	 * @param int $user_id
	 */
	public function set_user_id( $user_id ) {
		$this->set_prop( 'user_id', $user_id );
	}

	/**
	 * Set the user email.
	 *
	 * @param string $user_email
	 */
	public function set_user_email( $user_email ) {
		$this->set_prop( 'user_email', $user_email );
	}

	/**
	 * Set the status.
	 *
	 * @param string $status
	 */
	public function set_status( $status ) {
		$this->set_prop( 'status', $status );
	}

	/**
	 * Set the date created.
	 *
	 * @param string $date_created
	 */
	public function set_date_created( $date_created ) {
		$this->set_date_prop( 'date_created', $date_created );
	}

	/**
	 * Set the date modified.
	 *
	 * @param string $date_modified
	 */
	public function set_date_modified( $date_modified ) {
		$this->set_date_prop( 'date_modified', $date_modified );
	}

	/**
	 * Set the date subscribed.
	 *
	 * @param string $date_subscribed
	 */
	public function set_date_subscribed( $date_subscribed ) {
		$this->set_date_prop( 'date_subscribed', $date_subscribed );
	}

	/**
	 * Set the date notified.
	 *
	 * @param string $date_notified
	 */
	public function set_date_notified( $date_notified ) {
		$this->set_date_prop( 'date_notified', $date_notified );
	}

	/**
	 * Set the is queued.
	 *
	 * @param bool $is_queued
	 */
	public function set_is_queued( $is_queued ) {
		$this->set_prop( 'is_queued', $is_queued ? 1 : 0 );
	}

	/*
	|--------------------------------------------------------------------------
	| Conditionals
	|--------------------------------------------------------------------------
	*/

	/**
	 * Is queued.
	 *
	 * @return bool
	 */
	public function is_queued() {
		return 1 === $this->get_prop( 'is_queued' );
	}

	/*
	|--------------------------------------------------------------------------
	| Other Methods
	|--------------------------------------------------------------------------
	*/

	/**
	 * Validate the data.
	 *
	 * @throws \WC_Data_Exception
	 */
	protected function validate_props() {
		if ( empty( $this->get_prop( 'product_id' ) ) ) {
			$this->error( 'stock_notification_product_id_required', __( 'Product ID is required', 'woocommerce' ) );
		}

		if ( empty( $this->get_prop( 'user_id' ) ) && empty( $this->get_prop( 'user_email' ) ) ) {
			$this->error( 'stock_notification_user_id_or_user_email_required', __( 'User ID or User Email is required', 'woocommerce' ) );
		}
	}

	/**
	 * Save the notification.
	 *
	 * @return int|\WP_Error
	 */
	public function save() {
		if ( ! $this->data_store ) {
			return $this->get_id();
		}

		try {
			$this->validate_props();
		} catch ( \WC_Data_Exception $e ) {
			return new \WP_Error( 'stock_notification_validation_error', $e->getMessage() );
		}

		if ( $this->get_id() ) {
			$this->data_store->update( $this );
		} else {
			$this->data_store->create( $this );
		}

		return $this->get_id();
	}

	/**
	 * Add an event.
	 *
	 * @param array $args
	 * @return int|false The log ID or false if the log was not created.
	 */
	public function add_event( $args ) {

		$args = wp_parse_args( $args, array(
			'action'     => '',
			'user_id'    => 0,
			'user_email' => '',
			'ip_address' => '',
			'note'       => '',
		) );

		return $this->data_store->create_event( $this, $args );
	}
}