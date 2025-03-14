<?php
/**
 * WC_BIS_Admin_Notifications_Page class
 *
 * @package  WooCommerce Back In Stock Notifications
 * @since    1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_BIS_Admin_Notifications_Page Class.
 *
 * @version 1.6.5
 */
class WC_BIS_Admin_Notifications_Page {

	/**
	 * Page home URL.
	 *
	 * @const PAGE_URL
	 */
	const PAGE_URL = 'admin.php?page=bis_notifications';

	public static function init() {
		// Add JS template.
		add_action( 'admin_footer', array( __CLASS__, 'add_js_template' ) );
	}

	/**
	 * Render page.
	 */
	public static function output() {

		if ( isset( $_GET['bis_notice'] ) ) {
			$updated_notice_args = array(
				'id'                 => 'message',
				'type'               => 'success',
				'additional_classes' => array( 'updated' ),
				'dismissible'        => false,
			);
			switch ( $_GET['bis_notice'] ) {
				case 'deleted':
					wp_admin_notice( __( 'Notification deleted.', 'woocommerce' ), $updated_notice_args );
					break;
				case 'created':
					wp_admin_notice( __( 'Notification created.', 'woocommerce' ), $updated_notice_args );
					break;
				case 'updated':
					wp_admin_notice( __( 'Notification updated.', 'woocommerce' ), $updated_notice_args );
					break;
				case 'sent':
					$recipient = isset( $_GET['recipient'] ) ? $_GET['recipient'] : '';
					wp_admin_notice( sprintf( __( 'Notification sent to "%s".', 'woocommerce' ), $recipient ), $updated_notice_args );
					break;
				case 'verification_email_sent':
					$recipient = isset( $_GET['recipient'] ) ? $_GET['recipient'] : '';
					wp_admin_notice( sprintf( __( 'Verification e-mail sent to "%s".', 'woocommerce' ), $recipient ), $updated_notice_args );
					break;
				case 'error':
					$message = isset( $_GET['error'] ) ? $_GET['error'] : __( 'An error occurred.', 'woocommerce' );
					$updated_notice_args['type'] = 'error';
					wp_admin_notice( $message, $updated_notice_args );
					break;
				case 'not_found':
					$updated_notice_args['type'] = 'error';
					wp_admin_notice( __( 'Notification not found.', 'woocommerce' ), $updated_notice_args );
					break;
			}
		}

		$search = isset( $_REQUEST['s'] ) ? sanitize_text_field( $_REQUEST['s'] ) : '';
		$table  = new WC_BIS_Notifications_List_Table();
		$table->prepare_items();

		include __DIR__ . '/views/html-admin-notifications.php';
	}

	/**
	 * Save.
	 */
	public static function process() {

		if ( empty( $_POST ) ) {
			return false;
		}

		check_admin_referer( 'woocommerce-bis-edit', 'bis_edit_security' );

		$notification_id = isset( $_GET['notification'] ) ? absint( $_GET['notification'] ) : 0;
		if ( $notification_id ) {
			$notification = WC_BIS()->db->notifications->get( $notification_id );
		}

		if ( isset( $notification ) && $notification->get_id() ) {

			// Construct edit url.
			$edit_url = add_query_arg(
				array(
					'section'      => 'edit',
					'notification' => $notification->get_id(),
				),
				self::PAGE_URL
			);

			if ( isset( $_POST['save'] ) ) {

				// Posted data.
				$args          = $_POST;
				$should_update = false;
				$update_args   = array();

				// Attributes.
				if ( isset( $args['status'] ) ) {

					$update_args['is_active'] = 'on' === sanitize_text_field( $args['status'] ) ? 'on' : 'off';

					// If changed log an activity.
					if ( 'on' === $update_args['is_active'] && ! $notification->is_active() ) {
						// Log activate.
						$should_update = true;
						self::handle_reactivation( $notification, $update_args );
					} elseif ( 'off' === $update_args['is_active'] && $notification->is_active() ) {
						// Log deactivate.
						$should_update = true;
						self::handle_deactivation( $notification, $update_args );
					}
				}

				try {
					if ( $should_update && WC_BIS()->db->notifications->update( $notification, $update_args ) ) {
						$edit_url = add_query_arg(
							array(
								'bis_notice' => 'updated',
							),
							$edit_url
						);
					}
				} catch ( Exception $e ) {
					$edit_url = add_query_arg(
						array(
							'bis_notice' => 'error',
							'error' => $e->getMessage(),
						),
						$edit_url
					);
				}
			}

			// Process action.
			if ( ! empty( $_POST['wc_bis_action'] ) ) {

				$action        = wc_clean( $_POST['wc_bis_action'] );
				$should_update = false;
				$update_args   = array();

				switch ( $action ) {

					case 'send_notification':
						$product = $notification->get_product();

						if ( $notification->is_active() && $product->is_in_stock() ) {
							do_action( 'woocommerce_bis_force_send_notification_to_customer', $notification );
							/* translators: user email */
							$edit_url = add_query_arg(
								array(
									'bis_notice' => 'sent',
									'recipient' => $notification->get_user_email(),
								),
								$edit_url
							);
						} else {
							$edit_url = add_query_arg(
								array(
									'bis_notice' => 'error',
									'error' => __( 'Failed to send notification. Please make sure that (a) the notification is active, and (b) the listed product is available.', 'woocommerce' ),
								),
								$edit_url
							);
						}
						break;

					case 'enable_notification':
						if ( ! $notification->is_active() ) {
							$update_args['is_active'] = 'on';
							self::handle_reactivation( $notification, $update_args );
							$should_update = true;
						}

						break;

					case 'disable_notification':
						if ( $notification->is_active() ) {
							$update_args['is_active'] = 'off';
							self::handle_deactivation( $notification, $update_args );
							$should_update = true;
						}

						break;
					case 'send_verification_email':
						$product = $notification->get_product();

						if ( ! $notification->is_verified() ) {
							do_action( 'woocommerce_bis_verify_notification_to_customer', $notification );
							/* translators: user email */
							$edit_url = add_query_arg(
								array(
									'bis_notice' => 'verification_email_sent',
									'recipient' => $notification->get_user_email(),
								),
								$edit_url
							);
						}
						break;
				}

				try {
					if ( $should_update && WC_BIS()->db->notifications->update( $notification, $update_args ) ) {
						$edit_url = add_query_arg(
							array(
								'bis_notice' => 'updated',
							),
							$edit_url
						);
					}
				} catch ( Exception $e ) {
					$edit_url = add_query_arg(
						array(
							'bis_notice' => 'error',
							'error' => $e->getMessage(),
						),
						$edit_url
					);
				}
			}

			wp_safe_redirect( admin_url( $edit_url ) );
			exit;
		}

		// Process new notification.
		if ( isset( $_POST['create_save'] ) ) {

			// Posted data.
			$args       = $_POST;
			$query_args = array();

			// Escape attributes.

			if ( isset( $args['user_id'] ) && ! empty( $args['user_id'] ) ) {
				$query_args['user_id'] = absint( $args['user_id'] );
				$user                  = get_user_by( 'id', $query_args['user_id'] );
				if ( $user && is_a( $user, 'WP_User' ) ) {
					$query_args['user_email'] = $user->user_email;
				}
			} elseif ( isset( $args['user_email'] ) && ! empty( $args['user_email'] ) ) {
				$query_args['user_email'] = sanitize_text_field( $args['user_email'] );
				// Is there a user with this email?
				$user = get_user_by( 'email', $query_args['user_email'] );
				if ( $user && is_a( $user, 'WP_User' ) ) {
					$query_args['user_id'] = $user->ID;
				}
			}

			if ( isset( $args['status'] ) && 'off' === $args['status'] ) {
				$query_args['is_active'] = 'off';
			} else {
				$query_args['is_active'] = 'on';
			}

			if ( isset( $args['product_id'] ) && ! empty( $args['product_id'] ) ) {

				$query_args['product_id'] = absint( $args['product_id'] );

				if ( 'on' === $query_args['is_active'] ) {

					// Mark waiting time now if product is currently outofstock.
					$product = wc_get_product( $query_args['product_id'] );
					if ( is_a( $product, 'WC_Product' ) && ! $product->is_in_stock() ) {
						$query_args['subscribe_date'] = time();
					}
				}
			}

			// Check if notification with user + product exists.
			$exists_args           = array();
			$exists_args['return'] = 'objects';
			if ( isset( $query_args['product_id'] ) ) {
				$exists_args['product_id'] = $query_args['product_id'];
			}

			if ( isset( $query_args['user_id'] ) ) {
				$exists_args['user_id'] = $query_args['user_id'];
			}

			if ( isset( $query_args['user_email'] ) ) {
				$exists_args['user_email'] = $query_args['user_email'];
			}

			if ( ! empty( $exists_args['product_id'] ) && ( ! empty( $exists_args['user_id'] ) || ! empty( $exists_args['user_email'] ) ) ) {

				$notification_exists = WC_BIS()->db->notifications->query( $exists_args );
				if ( ! empty( $notification_exists ) ) {
					$object = current( $notification_exists );
					if ( is_a( $object, 'WC_BIS_Notification_Data' ) ) {
						/* translators: %s duplicate notification edit url */
						wp_admin_notice( 
							sprintf( 
								__( 'A <a href="%s">notification</a> for the same product and customer already exists in your database.', 'woocommerce' ), 
								admin_url( 'admin.php?page=bis_notifications&section=edit&notification=' . $object->get_id() ) 
							), 
							array(
								'id'                 => 'message',
								'additional_classes' => array( 'error' ),
								'dismissible'        => false,
							)
						);
					}

					return;
				}
			}

			try {
				$id = WC_BIS()->db->notifications->add( $query_args );
				if ( $id ) {

					$notification = wc_bis_get_notification( $id );
					if ( $notification ) {
						$notification->add_event( 'created', wp_get_current_user() );

						// Redirect.
						$edit_url = add_query_arg(
							array(
								'section'      => 'edit',
								'notification' => $id,
								'bis_notice' => 'created',
							),
							self::PAGE_URL
						);
						wp_safe_redirect( admin_url( $edit_url ) );
						exit;
					}
				}
			} catch ( Exception $e ) {
				wp_admin_notice( $e->getMessage(), array(
					'id'                 => 'message',
					'additional_classes' => array( 'error' ),
					'dismissible'        => false,
				) );

			}
		}
	}

	/**
	 * Delete notification.
	 */
	public static function delete() {

		check_admin_referer( 'delete_notification' );

		$notification_id = isset( $_GET['notification'] ) ? absint( $_GET['notification'] ) : 0;
		if ( $notification_id ) {
			$notification = wc_bis_get_notification( $notification_id );
		}

		if ( isset( $notification ) && $notification ) {
			$notification->delete();
			
			wp_safe_redirect( add_query_arg( 'bis_notice', 'deleted', admin_url( self::PAGE_URL ) ) );
			exit;
		} else {
			
			wp_safe_redirect( add_query_arg( 'bis_notice', 'not_found', admin_url( self::PAGE_URL ) ) );
			exit;
		}
	}

	/**
	 * Render createe page.
	 */
	public static function create_output() {
		$args = array();
		if ( ! empty( $_POST ) ) {
			check_admin_referer( 'woocommerce-bis-edit', 'bis_edit_security' );
			$args = $_POST;
		}
		include __DIR__ . '/views/html-admin-notification-create.php';
	}

	/**
	 * Render edit page.
	 */
	public static function edit_output() {

		$notification_id = isset( $_GET['notification'] ) ? absint( $_GET['notification'] ) : 0;
		if ( $notification_id ) {
			$notification = wc_bis_get_notification( $notification_id );
		}

		if ( ! isset( $notification ) || ! is_a( $notification, 'WC_BIS_Notification_Data' ) ) {
			$redirect_url = add_query_arg( 'bis_notice', 'not_found', admin_url( self::PAGE_URL ) );
			wp_safe_redirect( $redirect_url );
			exit;
		}

		$activity_table = new WC_BIS_Activity_List_Table( $notification->get_id() );
		$activity_table->prepare_items();

		include __DIR__ . '/views/html-admin-notification-edit.php';
	}

	/**
	 * Handle notification activation.
	 *
	 * @param  WC_BIS_Notification_Data $notification
	 * @param  array                    $update_args
	 * @return void
	 */
	public static function handle_reactivation( $notification, &$update_args ) {

		try {

			if ( 0 === $notification->get_subscribe_date() || $notification->is_delivered() ) {

				$product = $notification->get_product();
				if ( ! $product->is_in_stock() ) {
					$update_args['subscribe_date'] = time();
				}
			}

			$notification = wc_bis_get_notification( $notification );
			$notification->add_event( 'reactivated', wp_get_current_user() );

			/**
			 * Filter: `woocommerce_bis_notification_reactivation_args`.
			 *
			 * @param  array
			 * @param  WC_BIS_Notification_Data
			 */
			$updated_args = apply_filters( 'woocommerce_bis_notification_reactivation_args', $update_args, $notification );

		} catch ( Exception $e ) {
			wp_admin_notice( $e->getMessage(), array(
				'id'                 => 'message',
				'additional_classes' => array( 'error' ),
				'dismissible'        => false,
			) );
		}
	}

	/**
	 * Handle notification deactivation.
	 *
	 * @param  WC_BIS_Notification_Data $notification
	 * @param  array                    $update_args
	 * @return void
	 */
	public static function handle_deactivation( $notification, &$update_args ) {

		try {

			$update_args['is_queued'] = 'off';

			$notification = wc_bis_get_notification( $notification );
			if ( $notification->is_queued() ) {
				$notification->add_event( 'aborted', wp_get_current_user() );
			}
			$notification->add_event( 'deactivated', wp_get_current_user() );

			/**
			 * Filter: `woocommerce_bis_notification_deactivation_args`.
			 *
			 * @param  array
			 * @param  WC_BIS_Notification_Data
			 */
			$updated_args = apply_filters( 'woocommerce_bis_notification_deactivation_args', $update_args, $notification );

		} catch ( Exception $e ) {
			wp_admin_notice( $e->getMessage(), array(
				'id'                 => 'message',
				'additional_classes' => array( 'error' ),
				'dismissible'        => false,
			) );
		}
	}

	/**
	 * JS template of modal for exporting notifications.
	 *
	 * @return void
	 */
	public static function add_js_template() {

		if ( wp_script_is( 'wc-bis-writepanel' ) ) {
			?>
			<script type="text/template" id="tmpl-wc-bis-export-notifications">
				<div class="wc-backbone-modal">
					<div class="wc-backbone-modal-content wc-backbone-modal-content-export-notifications">
						<section class="wc-backbone-modal-main" role="main">
							<header class="wc-backbone-modal-header">
								<h1>{{{ data.action }}}</h1>
								<button class="modal-close modal-close-link dashicons dashicons-no-alt">
									<span class="screen-reader-text">Close modal panel</span>
								</button>
							</header>
							<article>
								<form action="" method="post">
								</form>
							</article>
						</section>
					</div>
				</div>
				<div class="wc-backbone-modal-backdrop modal-close"></div>
			</script>
			<?php
		}
	}
}

WC_BIS_Admin_Notifications_Page::init();
