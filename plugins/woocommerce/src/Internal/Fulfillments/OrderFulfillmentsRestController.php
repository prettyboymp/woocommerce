<?php

namespace Automattic\WooCommerce\Internal\Fulfillments;

use Automattic\WooCommerce\Internal\RestApiControllerBase;
use WP_REST_Request;

/**
 * Class with methods for handling order fulfillments.
 */
class OrderFulfillmentsRestController extends RestApiControllerBase {
	/**
	 * Endpoint namespace.
	 *
	 * @var string
	 */
	protected $namespace = 'wc/v3';

	/**
	 * REST API base.
	 *
	 * @var string
	 */
	protected $rest_base = '/orders/(?P<order_id>[\d]+)/fulfillments';

	/**
	 * Get the WooCommerce REST API namespace for the class.
	 *
	 * @return string
	 */
	protected function get_rest_api_namespace(): string {
		return 'orders';
	}

	/**
	 * Register the routes for fulfillments.
	 */
	public function register_routes() {
		// Register the route for getting and setting order fulfillments.
		register_rest_route(
			$this->get_rest_api_namespace(),
			'/' . $this->rest_base,
			array(
				array(
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => fn( $request ) => $this->run( $request, 'get_fulfillments' ),
					'permission_callback' => fn( $request ) => $this->check_permission_for_fulfillments( $request ),
					'args'                => $this->get_args_for_get_fulfillments(),
					'schema'              => $this->get_schema_for_get_fulfillments(),
				),
				array(
					'methods'             => \WP_REST_Server::CREATABLE,
					'callback'            => fn( $request ) => $this->run( $request, 'create_fulfillment' ),
					'permission_callback' => fn( $request ) => $this->check_permission_for_fulfillments( $request ),
					'args'                => $this->get_args_for_create_fulfillment(),
					'schema'              => $this->get_schema_for_create_fulfillment(),
				),
			),
		);

		// Register the route for getting a specific fulfillment.
		register_rest_route(
			$this->get_rest_api_namespace(),
			'/' . $this->rest_base . '/(?P<fulfillment_id>[\d]+)',
			array(
				array(
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => fn( $request ) => $this->run( $request, 'get_fulfillment' ),
					'permission_callback' => fn( $request ) => $this->check_permission_for_fulfillments( $request ),
					'args'                => $this->get_args_for_get_fulfillment(),
					'schema'              => $this->get_schema_for_get_fulfillment(),
				),
				array(
					'methods'             => \WP_REST_Server::EDITABLE,
					'callback'            => fn( $request ) => $this->run( $request, 'update_fulfillment' ),
					'permission_callback' => fn( $request ) => $this->check_permission_for_fulfillments( $request ),
					'args'                => $this->get_args_for_update_fulfillment(),
					'schema'              => $this->get_schema_for_update_fulfillment(),
				),
				array(
					'methods'             => \WP_REST_Server::DELETABLE,
					'callback'            => fn( $request ) => $this->run( $request, 'delete_fulfillment' ),
					'permission_callback' => fn( $request ) => $this->check_permission_for_fulfillments( $request ),
					'args'                => $this->get_args_for_delete_fulfillment(),
					'schema'              => $this->get_schema_for_delete_fulfillment(),
				),
			),
		);

		// Register the route for fulfillment metadata.
		register_rest_route(
			$this->get_rest_api_namespace(),
			'/' . $this->rest_base . '/(?P<fulfillment_id>[\d]+)/metadata',
			array(
				array(
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => fn( $request ) => $this->run( $request, 'get_fulfillment_meta' ),
					'permission_callback' => fn( $request ) => $this->check_permission_for_fulfillments( $request ),
					'args'                => $this->get_args_for_get_fulfillment_meta(),
					'schema'              => $this->get_schema_for_get_fulfillment_meta(),
				),
				array(
					'methods'             => \WP_REST_Server::EDITABLE,
					'callback'            => fn( $request ) => $this->run( $request, 'update_fulfillment_meta' ),
					'permission_callback' => fn( $request ) => $this->check_permission_for_fulfillments( $request ),
					'args'                => $this->get_args_for_update_fulfillment_meta(),
					'schema'              => $this->get_schema_for_update_fulfillment_meta(),
				),
				array(
					'methods'             => \WP_REST_Server::DELETABLE,
					'callback'            => fn( $request ) => $this->run( $request, 'delete_fulfillment_meta' ),
					'permission_callback' => fn( $request ) => $this->check_permission_for_fulfillments( $request ),
					'args'                => $this->get_args_for_delete_fulfillment_meta(),
					'schema'              => $this->get_schema_for_delete_fulfillment_meta(),
				),
			),
		);

		// Register the route for tracking number lookup.
		register_rest_route(
			$this->get_rest_api_namespace(),
			'/fulfillments/lookup',
			array(
				array(
					'methods'             => \WP_REST_Server::CREATABLE,
					'callback'            => fn( $request ) => $this->run( $request, 'get_tracking_number_details' ),
					'permission_callback' => fn( $request ) => $this->check_permission_for_fulfillments( $request ),
					'args'                => $this->get_args_for_get_tracking_number_details(),
					'schema'              => $this->get_schema_for_get_tracking_number_details(),
				),
			),
		);
	}

	/**
	 * Permission check for REST API endpoints, given the request method.
	 * For all fulfillments methods that have an order_id, we need to be sure the user has permission to view the order.
	 * For all other methods, we check if the user is logged in as admin and has the required capability.
	 *
	 * @param WP_REST_Request $request The request for which the permission is checked.
	 * @return bool|WP_Error True if the current user has the capability, otherwise an "Unauthorized" error or False if no error is available for the request method.
	 */
	protected function check_permission_for_fulfillments( WP_REST_Request $request ) {
		// Check if the user is logged in as admin, and has the required capability.
		if ( is_admin() ) {
			if ( current_user_can( 'manage_woocommerce' ) ) {
				return true;
			}
		}

		// Otherwise, check if the user has permission to view the order.
		if ( $request->has_param( 'order_id' ) ) {
			$order_id = (int) $request->get_param( 'order_id' );
			$order    = wc_get_order( $order_id );
			if ( ! $order ) {
				return new \WP_Error( 'woocommerce_rest_order_invalid_id', __( 'Invalid order ID.', 'woocommerce' ), array( 'status' => 404 ) );
			}
		}

		// User doesn't have permission to view the fulfillment.
		return new \WP_Error(
			'woocommerce_rest_cannot_view',
			__( 'Sorry, you cannot view this resource.', 'woocommerce' ),
			array( 'status' => rest_authorization_required_code() )
		);
	}

	/**
	 * Get the arguments for the get fulfillments endpoint.
	 *
	 * @return array
	 */
	private function get_args_for_get_fulfillments(): array {
		return array();}

	/**
	 * Get the schema for the get fulfillments endpoint.
	 *
	 * @return array
	 */
	private function get_schema_for_get_fulfillments(): array {
		return array();}

	/**
	 * Get the arguments for the create fulfillment endpoint.
	 *
	 * @return array
	 */
	private function get_args_for_create_fulfillment(): array {
		return array();}

	/**
	 * Get the schema for the create fulfillment endpoint.
	 *
	 * @return array
	 */
	private function get_schema_for_create_fulfillment(): array {
		return array();}

	/**
	 * Get the arguments for the get fulfillment endpoint.
	 *
	 * @return array
	 */
	private function get_args_for_get_fulfillment(): array {
		return array();}
	/**
	 * Get the schema for the get fulfillment endpoint.
	 *
	 * @return array
	 */
	private function get_schema_for_get_fulfillment(): array {
		return array();}
	/**
	 * Get the arguments for the update fulfillment endpoint.
	 *
	 * @return array
	 */
	private function get_args_for_update_fulfillment(): array {
		return array();}
	/**
	 * Get the schema for the update fulfillment endpoint.
	 *
	 * @return array
	 */
	private function get_schema_for_update_fulfillment(): array {
		return array();}
	/**
	 * Get the arguments for the delete fulfillment endpoint.
	 *
	 * @return array
	 */
	private function get_args_for_delete_fulfillment(): array {
		return array();}
	/**
	 * Get the schema for the delete fulfillment endpoint.
	 *
	 * @return array
	 */
	private function get_schema_for_delete_fulfillment(): array {
		return array();}
	/**
	 * Get the arguments for the get fulfillment meta endpoint.
	 *
	 * @return array
	 */
	private function get_args_for_get_fulfillment_meta(): array {
		return array();}
	/**
	 * Get the schema for the get fulfillment meta endpoint.
	 *
	 * @return array
	 */
	private function get_schema_for_get_fulfillment_meta(): array {
		return array();}
	/**
	 * Get the arguments for the update fulfillment meta endpoint.
	 *
	 * @return array
	 */
	private function get_args_for_update_fulfillment_meta(): array {
		return array();}
	/**
	 * Get the schema for the update fulfillment meta endpoint.
	 *
	 * @return array
	 */
	private function get_schema_for_update_fulfillment_meta(): array {
		return array();}
	/**
	 * Get the arguments for the delete fulfillment meta endpoint.
	 *
	 * @return array
	 */
	private function get_args_for_delete_fulfillment_meta(): array {
		return array();}
	/**
	 * Get the schema for the delete fulfillment meta endpoint.
	 *
	 * @return array
	 */
	private function get_schema_for_delete_fulfillment_meta(): array {
		return array();}
	/**
	 * Get the arguments for the get tracking number details endpoint.
	 *
	 * @return array
	 */
	private function get_args_for_get_tracking_number_details(): array {
		return array();
	}
	/**
	 * Get the schema for the get tracking number details endpoint.
	 *
	 * @return array
	 */
	private function get_schema_for_get_tracking_number_details(): array {
		return array();}
}
