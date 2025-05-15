<?php

declare( strict_types = 1 );

namespace Automattic\WooCommerce\Internal\StockNotifications;

use Automattic\WooCommerce\Internal\StockNotifications\Notification;
use Automattic\WooCommerce\Internal\StockNotifications\Emails\StockNotificationEmail;
use Automattic\WooCommerce\Internal\StockNotifications\Emails\StockNotificationEmailConfirm;
use Automattic\WooCommerce\Internal\StockNotifications\Emails\StockNotificationEmailVerify;

/**
 * Emails manager.
 */
class Emails {

	/**
	 * List of all core email IDs.
	 *
	 * @var array
	 */
	public static $email_ids = array(
		'stock_notification_receive',
		'stock_notification_confirm',
		'stock_notification_verify',
	);

	final public function init() {

		// Setup email hooks & handlers.
		add_filter( 'woocommerce_email_classes', array( $this, 'email_classes' ) );

		// Setup styles.
		add_filter( 'woocommerce_email_styles', array( $this, 'add_stylesheets' ), 10, 2 );

		// Preview.
		add_filter( 'woocommerce_prepare_email_for_preview', array( $this, 'prepare_email_for_preview' ) );
		add_filter( 'woocommerce_email_preview_email_content_setting_ids', array( $this, 'add_intro_content_to_preview_settings' ), 10, 2 );

		// Restore customer's context into the background queue.
		add_action( 'woocommerce_email_stock_notification_product_before_title', array( $this, 'maybe_restore_customer_data' ), 9 );
	}

	/**
	 * Registers custom emails classes.
	 *
	 * @param  array $emails Array of email classes.
	 * @return array
	 */
	public function email_classes( $emails ) {
		$emails[ 'WC_Email_Stock_Notification_Receive' ] = new StockNotificationEmail();
		$emails[ 'WC_Email_Stock_Notification_Confirm' ] = new StockNotificationEmailConfirm();
		$emails[ 'WC_Email_Stock_Notification_Verify' ] = new StockNotificationEmailVerify();

		return $emails;
	}

	/**
	 * Restore customer data from notification's metadata, if applicable.
	 *
	 * @param  Notification $notification
	 * @return void
	 */
	public function maybe_restore_customer_data( $notification ) {

		// No need if stores displaying price excluding tax.
		if ( 'incl' !== get_option( 'woocommerce_tax_display_shop' ) ) {
			return;
		}

		// Check if for some reason (e.g., 3PD), a WC_Customer is already assigned into the BG process's context.
		if ( ! empty( WC()->customer ) ) {
			return;
		}

		// Get the recorded customer data, if any.
		$location = $notification->get_meta( '_customer_location_data' );
		if ( empty( $location ) || ! is_array( $location ) || 4 !== count( $location ) ) {
			return;
		}

		// Restore the tax location.
		add_filter(
			'woocommerce_get_tax_location',
			function () use ( $location ) {
				return $location;
			}
		);
	}

	/**
	 * Prints CSS in the emails.
	 *
	 * @param  string   $css
	 * @param  WC_Email $email (Optional)
	 * @return void
	 */
	public function add_stylesheets( $css, $email = null ) {

		/**
		 * `woocommerce_bis_emails_to_style` filter.
		 *
		 * @since  0.0.0
		 *
		 * @return array
		 */
		if ( ( is_null( $email ) || ! in_array( $email->id, (array) apply_filters( 'woocommerce_bis_emails_to_style', self::$email_ids ), true ) ) ) {
			return $css;
		}

		// General text.
		$text = get_option( 'woocommerce_email_text_color' );

		// Primary color.
		$base      = get_option( 'woocommerce_email_base_color' );
		$base_text = (string) apply_filters( 'woocommerce_bis_email_base_text_color', wc_light_or_dark( $base, '#202020', '#ffffff' ), $email );

		ob_start();
		?>
		#header_wrapper h1 {
			line-height: 1em !important;
		}
		#notification__container {
			color: <?php echo esc_attr( $text ); ?> !important;
			padding: 20px 20px;
			text-align: center;
			font-family: "Helvetica Neue", Helvetica, Roboto, Arial, sans-serif;
			width: 100%;
		}
		#notification__into_content {
			margin-bottom: 48px;
			color: <?php echo esc_attr( $text ); ?> !important;
		}
		#notification__product__image {
			text-align: center;
			margin-bottom: 20px;
			width: 100%;
		}
		#notification__product__image img {
			margin-right: 0;
			width: 220px;
		}
		#notification__product__title {
			font-size: 16px;
			font-weight: bold;
			line-height: 130%;
			margin-bottom: 5px;
			color: <?php echo esc_attr( $text ); ?> !important;
		}
		#notification__product__attributes table {
			width: 100%;
			padding: 0;
			margin: 0;
			color: <?php echo esc_attr( $text ); ?> !important;
		}
		#notification__product__attributes th,
		#notification__product__attributes td {
			color: <?php echo esc_attr( $text ); ?> !important;
			padding: 4px !important;
			text-align: center;
		}
		#notification__product__price {
			margin-bottom: 20px;
			color: <?php echo esc_attr( $text ); ?> !important;
		}
		#notification__action_button {
			text-decoration: none;
			display: inline-block;
			background: <?php echo esc_attr( $base ); ?>;
			color: <?php echo esc_attr( $base_text ); ?> !important;
			border: 10px solid <?php echo esc_attr( $base ); ?>;
		}
		#notification__verification_expiration {
			font-size: 0.8em;
			margin-top: 20px;
			color: <?php echo esc_attr( $text ); ?>;
		}
		#notification__footer {
			text-align: center;
			margin-top: 20px;
			color: <?php echo esc_attr( $text ); ?>;
		}
		#notification__unsubscribe_link {
			color: <?php echo esc_attr( $text ); ?>;
		}
		#notification__product__price .screen-reader-text {
			display: none;
		}
		<?php
		$css .= ob_get_clean();

		return $css;
	}

	/**
	 * Register intro_content email fields to be watched by WooCommerce's live email preview.
	 *
	 * @param array  $setting_ids The email content setting IDs.
	 * @param string $email_id The email ID.
	 * @return array
	 */
	public function add_intro_content_to_preview_settings( $setting_ids, $email_id ) {

		if ( in_array( $email_id, self::$email_ids, true ) ) {
			$setting_ids[] = "woocommerce_{$email_id}_intro_content";
		}

		return $setting_ids;
	}

	/**
	 * Prepares the email for preview.
	 *
	 * @param \WC_Email $email The email object being previewed.
	 * @return \WC_Email
	 */
	public function prepare_email_for_preview( $email ) {
		if ( ! in_array( $email->id, self::$email_ids, true ) ) {
			return $email;
		}

		$notification = $this->get_dummy_notification( $email );
		$email->prepare_email( $notification );

		return $email;
	}

	/**
	 * Get a dummy product.
	 *
	 * @return WC_Product
	 */
	private function get_dummy_product(): \WC_Product {
		$product = new \WC_Product();
		$product->set_name( 'Dummy Product' );
		$product->set_price( 25 );
		return $product;
	}

	/**
	 * Get a dummy notification object.
	 *
	 * @return Notification
	 */
	private function get_dummy_notification(): Notification {
		$product      = $this->get_dummy_product();
		$notification = new Notification();
		$notification->product = $product;
		return $notification;
	}
}
