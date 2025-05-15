<?php

declare( strict_types = 1 );

namespace Automattic\WooCommerce\Internal\StockNotifications;

use Automattic\WooCommerce\Internal\StockNotifications\Notification;

/**
 * Emails manager.
 */
class Templates {

	/**
	 * Initialize the class.
	 */
	final public function init() {
		add_action( 'init', array( $this, 'add_email_template_hooks' ) );
	}

	/**
	 * Add template hooks.
	 */
	public function add_email_template_hooks() {
		add_action( 'woocommerce_email_stock_notification_product', array( $this, 'email_product_image' ), 10, 2 );
		add_action( 'woocommerce_email_stock_notification_product', array( $this, 'email_product_title' ), 20, 2 );
		add_action( 'woocommerce_email_stock_notification_product', array( $this, 'email_product_attributes' ), 30, 2 );
		add_action( 'woocommerce_email_stock_notification_product', array( $this, 'email_product_price' ), 40, 2 );
	}

	/**
	 * Email product image.
	 *
	 * @param WC_Product $product The product object.
	 * @param Notification $notification The notification object.
	 */
	public function email_product_image( $product, $notification ) {

		$image     = wp_get_attachment_image_src( $product->get_image_id(), 'woocommerce_thumbnail' );
		$image_src = is_array( $image ) && isset( $image[0] ) ? $image[0] : '';

		ob_start();
		if ( $image_src ) { ?>
				<div id="notification__product__image">
					<img src="<?php echo esc_attr( $image_src ); ?>" alt="<?php echo esc_attr( $product->get_title() ); ?>" width="220"/>
				</div>
			<?php
		}
		$html = ob_get_clean();
		echo wp_kses_post( $html );
	}

	/**
	 * Email product title.
	 *
	 * @param WC_Product $product The product object.
	 * @param Notification $notification The notification object.
	 */
	public function email_product_title( $product, $notification ) {
		ob_start();
		?>
		<div id="notification__product__title"><?php echo esc_html( $product->get_name() ); ?></div>
		<?php
		$html = ob_get_clean();
		echo wp_kses_post( $html );
	}

	/**
	 * Email product attributes.
	 *
	 * @param WC_Product $product The product object.
	 * @param Notification $notification The notification object.
	 */
	public function email_product_attributes( $product, $notification ) {
		ob_start();
		?>
		<div id="notification__product__attributes"><?php echo wp_kses_post( $notification->get_product_formatted_variation_list( false, 'email' ) ); ?></div>
		<?php
		$html = ob_get_clean();
		echo wp_kses_post( $html );
	}

	/**
	 * Email product price.
	 *
	 * @param WC_Product $product The product object.
	 * @param Notification $notification The notification object.
	 */
	public function email_product_price( $product, $notification ) {
		ob_start();
		?>
		<div id="notification__product__price"><?php echo wp_kses_post( $product->get_price_html() ); ?></div>
		<?php
		$html = ob_get_clean();
		echo wp_kses_post( $html );
	}
}
