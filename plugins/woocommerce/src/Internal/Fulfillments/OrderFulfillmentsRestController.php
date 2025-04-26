<?php
/**
 * FulfillmentsAPISchema class file.
 */

declare( strict_types=1 );

namespace Automattic\WooCommerce\Internal\Fulfillments;

use Automattic\WooCommerce\Internal\RestApiControllerBase;
use Automattic\WooCommerce\Internal\DataStores\Fulfillments\FulfillmentsDataStore;
use WC_Order;
use WP_Http;
use WP_REST_Request;
use WP_REST_Response;

/**
 * OrderFulfillmentsRestController class file.
 *
 * ! Note: This REST controller is only created for WC_Order entities.
 * ! If you are using another entity type for your fulfillments, you should create a new controller.
 *
 * @package Automattic\WooCommerce\Internal\Fulfillments
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
		return 'order_fulfillments';
	}

	/**
	 * Register the routes for fulfillments.
	 */
	public function register_routes() {
		// Register the route for getting and setting order fulfillments.
		register_rest_route(
			$this->route_namespace,
			$this->rest_base,
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
			$this->route_namespace,
			$this->rest_base . '/(?P<fulfillment_id>[\d]+)',
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
			$this->route_namespace,
			$this->rest_base . '/(?P<fulfillment_id>[\d]+)/metadata',
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
			$this->route_namespace,
			'/fulfillments/lookup',
			array(
				array(
					'methods'             => \WP_REST_Server::READABLE,
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
	 * @return bool|\WP_Error True if the current user has the capability, otherwise an "Unauthorized" error or False if no error is available for the request method.
	 */
	protected function check_permission_for_fulfillments( WP_REST_Request $request ) {
		// Check if the user is logged in as admin, and has the required capability.
		// Admins who can manage WooCommerce can view all fulfillments.
		if ( current_user_can( 'manage_woocommerce' ) ) { // phpcs:ignore WordPress.WP.Capabilities.Unknown
			return true;
		}

		// Fetch the order.
		// We allow this because we need to render the order fulfillments on the customer's order details and order tracking pages.
		// But they will be only able to view them, not edit.
		$order = null;
		if ( $request->has_param( 'order_id' ) ) {
			$order_id = (int) $request->get_param( 'order_id' );
			$order    = wc_get_order( $order_id );

			if ( ! $order ) {
				return new \WP_Error(
					'woocommerce_rest_order_invalid_id',
					__( 'Invalid order ID.', 'woocommerce' ),
					array( 'status' => WP_Http::NOT_FOUND )
				);
			}

			// Check if the order exists, and if the current user is the owner of the order, and the request is a read request.
			if ( get_current_user_id() === $order->get_customer_id() && \WP_REST_Server::READABLE === $request->get_method() ) {
				return true;
			}
		}

		// Return an error related to the request method.
		$error_information = $this->get_authentication_error_by_method( $request->get_method() );

		if ( is_null( $error_information ) ) {
			return false;
		}

		return new \WP_Error(
			$error_information['code'],
			$error_information['message'],
			array( 'status' => rest_authorization_required_code() )
		);
	}

	/**
	 * Get the fulfillments for the order.
	 *
	 * @param WP_REST_Request $request The request object.
	 *
	 * @return array The fulfillments for the order, or an error if the request fails.
	 */
	public function get_fulfillments( WP_REST_Request $request ): WP_REST_Response {
		$order_id     = esc_attr( $request->get_param( 'order_id' ) );
		$fulfillments = array();

		// Fetch fulfillments for the order.
		try {
			$datastore    = wc_get_container()->get( FulfillmentsDataStore::class );
			$fulfillments = $datastore->read_fulfillments( WC_Order::class, $order_id );
		} catch ( \Exception $e ) {
			return new WP_REST_Response(
				$e->getMessage(),
				WP_Http::BAD_REQUEST
			);
		}

		// Return the fulfillments.
		return new WP_REST_Response(
			array(
				'fulfillments' => array_map(
					function ( $fulfillment ) {
						return $fulfillment->get_raw_data(); },
					$fulfillments
				),
			),
			WP_Http::OK
		);
	}

	/**
	 * Create a new fulfillment with the given data for the order.
	 *
	 * @param WP_REST_Request $request The request object.
	 *
	 * @return WP_REST_Response The created fulfillment, or an error if the request fails.
	 */
	public function create_fulfillment( WP_REST_Request $request ) {
		$order_id = esc_attr( $request->get_param( 'order_id' ) );

		// Create a new fulfillment.
		try {
			$fulfillment = new Fulfillment();
			$fulfillment->set_props( $request->get_json_params() );
			$fulfillment->set_meta_data( $request->get_json_params()['meta_data'] );
			$fulfillment->set_entity_type( WC_Order::class );
			$fulfillment->set_entity_id( $order_id );
			$fulfillment->save();
		} catch ( \Exception $e ) {
			return new WP_REST_Response(
				$e->getMessage(),
				WP_Http::UNAUTHORIZED
			);
		}

		return new WP_REST_Response( array( 'fulfillment' => $fulfillment ), WP_Http::CREATED );
	}

	/**
	 * Get a specific fulfillment for the order.
	 *
	 * @param WP_REST_Request $request The request object.
	 *
	 * @return WP_REST_Response The fulfillment for the order, or an error if the request fails.
	 */
	public function get_fulfillment( WP_REST_Request $request ): WP_REST_Response {
		$order_id       = (int) $request->get_param( 'order_id' );
		$fulfillment_id = (int) $request->get_param( 'fulfillment_id' );

		// Fetch the fulfillment for the order.
		try {
			$fulfillment = new Fulfillment( $fulfillment_id );
			if ( $fulfillment->get_entity_type() !== WC_Order::class && $fulfillment->get_entity_id() !== $order_id ) {
				return new WP_REST_Response(
					__( 'Fulfillment does not belong to this order.', 'woocommerce' ),
					WP_Http::UNAUTHORIZED
				);
			}
		} catch ( \Exception $e ) {
			return new WP_REST_Response(
				$e->getMessage(),
				WP_Http::BAD_REQUEST
			);
		}

		return new WP_REST_Response(
			array( 'fulfillment' => $fulfillment ),
			WP_Http::OK
		);
	}

	/**
	 * Update a specific fulfillment for the order.
	 *
	 * @param WP_REST_Request $request The request object.
	 *
	 * @return WP_REST_Response The updated fulfillment, or an error if the request fails.
	 */
	public function update_fulfillment( WP_REST_Request $request ): WP_REST_Response {
		$order_id       = esc_attr( $request->get_param( 'order_id' ) );
		$fulfillment_id = esc_attr( $request->get_param( 'fulfillment_id' ) );

		// Update the fulfillment for the order.
		try {
			$fulfillment = new Fulfillment( $fulfillment_id );
			if ( $fulfillment->get_entity_type() !== WC_Order::class && $fulfillment->get_entity_id() !== $order_id ) {
				return new WP_REST_Response(
					__( 'Fulfillment does not belong to this order.', 'woocommerce' ),
					WP_Http::UNAUTHORIZED
				);
			}
			$fulfillment->set_props( $request->get_json_params() );
			$fulfillment->set_meta_data( $request->get_json_params()['meta_data'] );
			$fulfillment->set_entity_type( WC_Order::class );
			$fulfillment->set_entity_id( $order_id );
			$fulfillment->save();
		} catch ( \Exception $e ) {
			return new WP_REST_Response(
				$e->getMessage(),
				WP_Http::UNAUTHORIZED
			);
		}

		return new WP_REST_Response(
			array( 'fulfillment' => $fulfillment ),
			WP_Http::OK
		);
	}

	/**
	 * Delete a specific fulfillment for the order.
	 *
	 * @param WP_REST_Request $request The request object.
	 *
	 * @return WP_REST_Response The deleted fulfillment, or an error if the request fails.
	 */
	public function delete_fulfillment( WP_REST_Request $request ) {
		$order_id       = esc_attr( $request->get_param( 'order_id' ) );
		$fulfillment_id = esc_attr( $request->get_param( 'fulfillment_id' ) );

		// Delete the fulfillment for the order.
		try {
			$fulfillment = new Fulfillment( $fulfillment_id );
			if ( $fulfillment->get_entity_type() !== WC_Order::class && $fulfillment->get_entity_id() !== $order_id ) {
				return new WP_REST_Response(
					__( 'Fulfillment does not belong to this order.', 'woocommerce' ),
					WP_Http::UNAUTHORIZED
				);
			}
			$fulfillment->delete();
		} catch ( \Exception $e ) {
			return new WP_REST_Response(
				$e->getMessage(),
				WP_Http::BAD_REQUEST
			);
		}

		return new WP_REST_Response(
			array(
				'message' => __( 'Fulfillment deleted successfully.', 'woocommerce' ),
			),
			WP_Http::OK
		);
	}

	/**
	 * Get the metadata for a specific fulfillment.
	 *
	 * @param WP_REST_Request $request The request object.
	 *
	 * @return WP_REST_Response The metadata for the fulfillment, or an error if the request fails.
	 */
	public function get_fulfillment_meta( WP_REST_Request $request ): WP_REST_Response {
		$order_id       = (int) $request->get_param( 'order_id' );
		$fulfillment_id = (int) $request->get_param( 'fulfillment_id' );

		// Fetch the metadata for the fulfillment.
		try {
			$fulfillment = new Fulfillment( $fulfillment_id );
			if ( $fulfillment->get_entity_type() !== WC_Order::class && $fulfillment->get_entity_id() !== $order_id ) {
				return new WP_REST_Response(
					__( 'Fulfillment does not belong to this order.', 'woocommerce' ),
					WP_Http::UNAUTHORIZED
				);
			}
		} catch ( \Exception $e ) {
			return new WP_REST_Response(
				$e->getMessage(),
				WP_Http::BAD_REQUEST
			);
		}

		return new WP_REST_Response(
			array(
				'meta_data' => $fulfillment->get_meta_data(),
			),
			WP_Http::OK
		);
	}

	/**
	 * Update the metadata for a specific fulfillment.
	 *
	 * @param WP_REST_Request $request The request object.
	 *
	 * @return WP_REST_Response The updated metadata for the fulfillment, or an error if the request fails.
	 */
	public function update_fulfillment_meta( WP_REST_Request $request ): WP_REST_Response {
		$order_id       = (int) $request->get_param( 'order_id' );
		$fulfillment_id = (int) $request->get_param( 'fulfillment_id' );

		// Update the metadata for the fulfillment.
		try {
			$fulfillment = new Fulfillment( $fulfillment_id );
			if ( $fulfillment->get_entity_type() !== WC_Order::class && $fulfillment->get_entity_id() !== $order_id ) {
				return new WP_REST_Response(
					__( 'Fulfillment does not belong to this order.', 'woocommerce' ),
					WP_Http::UNAUTHORIZED
				);
			}
			foreach ( $request->get_json_params() as $key => $value ) {
				$fulfillment->update_meta_data( $key, $value );
			}
			$fulfillment->save();
		} catch ( \Exception $e ) {
			return new WP_REST_Response(
				$e->getMessage(),
				WP_Http::UNAUTHORIZED
			);
		}

		return new WP_REST_Response(
			array(
				'meta_data' => $fulfillment->get_meta_data(),
			),
			WP_Http::OK
		);
	}

	/**
	 * Delete the metadata for a specific fulfillment.
	 *
	 * @param WP_REST_Request $request The request object.
	 *
	 * @return WP_REST_Response The deleted metadata for the fulfillment, or an error if the request fails.
	 */
	public function delete_fulfillment_meta( WP_REST_Request $request ) {
		$order_id       = (int) $request->get_param( 'order_id' );
		$fulfillment_id = (int) $request->get_param( 'fulfillment_id' );

		// Delete the metadata for the fulfillment.
		try {
			$fulfillment = new Fulfillment( $fulfillment_id );
			if ( $fulfillment->get_entity_type() !== WC_Order::class && $fulfillment->get_entity_id() !== $order_id ) {
				return new WP_REST_Response(
					__( 'Fulfillment does not belong to this order.', 'woocommerce' ),
					WP_Http::UNAUTHORIZED
				);
			}
			$fulfillment->delete_meta_data( $request->get_param( 'meta_key' ) );
			$fulfillment->save();
		} catch ( \Exception $e ) {
			return new WP_REST_Response(
				$e->getMessage(),
				WP_Http::BAD_REQUEST
			);
		}

		return new WP_REST_Response(
			array(
				'meta_data' => $fulfillment->get_meta_data(),
			),
			WP_Http::NO_CONTENT
		);
	}

	/**
	 * Get the tracking number details for a given tracking number, if possible.
	 *
	 * @param WP_REST_Request $request The request object.
	 *
	 * @return WP_REST_Response The tracking number details, or an error if the request fails.
	 */
	public function get_tracking_number_details( WP_REST_Request $request ) {
		$tracking_number = $request->get_param( 'tracking_number' );

		// Prepare a stubbed response for the tracking number details for now.
		return new WP_REST_Response(
			array(
				'tracking_number_details' => array(
					'tracking_number'   => $tracking_number,
					'shipping_provider' => 'UPS',
					'tracking_url'      => "https://www.ups.com/track?tracknum={$tracking_number}",
				),
			),
			WP_Http::OK
		);
	}


	/**
	 * Get the arguments for the get order fulfillments endpoint.
	 *
	 * @return array
	 */
	private function get_args_for_get_fulfillments(): array {
		return array(
			'order_id' => array(
				'description' => __( 'Unique identifier for the order.', 'woocommerce' ),
				'type'        => 'integer',
				'required'    => true,
				'context'     => array( 'view', 'edit' ),
			),
		);
	}

	/**
	 * Get the schema for the get order fulfillments endpoint.
	 *
	 * @return array
	 */
	private function get_schema_for_get_fulfillments(): array {
		$schema               = $this->get_base_schema();
		$schema['properties'] = array(
			'fulfillment' => array(
				'description' => __( 'The fulfillment object.', 'woocommerce' ),
				'type'        => 'array',
				'required'    => true,
				'schema'      => $this->get_read_schema_for_fulfillment(),
			),
		);
		return $schema;
	}

	/**
	 * Get the arguments for the create fulfillment endpoint.
	 *
	 * @return array
	 */
	private function get_args_for_create_fulfillment(): array {
		return $this->get_write_args_for_fulfillment( true );
	}

	/**
	 * Get the schema for the create fulfillment endpoint.
	 *
	 * @return array
	 */
	private function get_schema_for_create_fulfillment(): array {
		$schema               = $this->get_base_schema();
		$schema['properties'] = array(
			'fulfillment' => array(
				'description' => __( 'The created fulfillment object.', 'woocommerce' ),
				'type'        => 'object',
				'required'    => true,
				'schema'      => $this->get_read_schema_for_fulfillment(),
			),
		);
		return $schema;
	}

	/**
	 * Get the arguments for the get fulfillment endpoint.
	 *
	 * @return array
	 */
	private function get_args_for_get_fulfillment(): array {
		return array(
			'order_id'       => array(
				'description' => __( 'Unique identifier for the order.', 'woocommerce' ),
				'type'        => 'integer',
				'context'     => array( 'view', 'edit' ),
				'required'    => true,
			),
			'fulfillment_id' => array(
				'description' => __( 'Unique identifier for the fulfillment.', 'woocommerce' ),
				'type'        => 'integer',
				'context'     => array( 'view', 'edit' ),
				'required'    => true,
			),
		);
	}

	/**
	 * Get the schema for the get fulfillment endpoint.
	 *
	 * @return array
	 */
	private function get_schema_for_get_fulfillment(): array {
		$schema               = $this->get_base_schema();
		$schema['title']      = __( 'The returned fulfillment response.', 'woocommerce' );
		$schema['properties'] = array(
			'fulfillment' => array(
				'description' => __( 'The fulfillment object.', 'woocommerce' ),
				'type'        => 'object',
				'required'    => true,
				'schema'      => $this->get_read_schema_for_fulfillment(),
			),
		);

		return $schema;
	}

	/**
	 * Get the arguments for the update fulfillment endpoint.
	 *
	 * @return array
	 */
	private function get_args_for_update_fulfillment(): array {
		return $this->get_write_args_for_fulfillment( false );
	}

	/**
	 * Get the schema for the update fulfillment endpoint.
	 *
	 * @return array
	 */
	private function get_schema_for_update_fulfillment(): array {
		$schema               = $this->get_base_schema();
		$schema['title']      = __( 'The updated fulfillment response.', 'woocommerce' );
		$schema['properties'] = array(
			'fulfillment' => array(
				'description' => __( 'The fulfillment object.', 'woocommerce' ),
				'type'        => 'object',
				'required'    => true,
				'schema'      => $this->get_read_schema_for_fulfillment(),
			),
		);

		return $schema;
	}

	/**
	 * Get the arguments for the delete fulfillment endpoint.
	 *
	 * @return array
	 */
	private function get_args_for_delete_fulfillment(): array {
		return array(
			'order_id'       => array(
				'description' => __( 'Unique identifier for the order.', 'woocommerce' ),
				'type'        => 'integer',
				'required'    => true,
				'context'     => array( 'view', 'edit' ),
			),
			'fulfillment_id' => array(
				'description' => __( 'Unique identifier for the fulfillment.', 'woocommerce' ),
				'type'        => 'integer',
				'required'    => true,
				'context'     => array( 'view', 'edit' ),
			),
		);
	}

	/**
	 * Get the schema for the delete fulfillment endpoint.
	 *
	 * @return array
	 */
	private function get_schema_for_delete_fulfillment(): array {
		$schema               = $this->get_base_schema();
		$schema['title']      = __( 'The deleted fulfillment response.', 'woocommerce' );
		$schema['properties'] = array(
			'message' => array(
				'description' => __( 'The response message.', 'woocommerce' ),
				'type'        => 'string',
				'required'    => true,
			),
		);

		return $schema;
	}

	/**
	 * Get the arguments for the get fulfillment meta endpoint.
	 *
	 * @return array
	 */
	private function get_args_for_get_fulfillment_meta(): array {
		return array(
			'order_id'       => array(
				'description' => __( 'Unique identifier for the order.', 'woocommerce' ),
				'type'        => 'integer',
				'required'    => true,
				'context'     => array( 'view', 'edit' ),
			),
			'fulfillment_id' => array(
				'description' => __( 'Unique identifier for the fulfillment.', 'woocommerce' ),
				'type'        => 'integer',
				'required'    => true,
				'context'     => array( 'view', 'edit' ),
			),
		);
	}

	/**
	 * Get the schema for the get fulfillment meta endpoint.
	 *
	 * @return array
	 */
	private function get_schema_for_get_fulfillment_meta(): array {
		$schema               = $this->get_base_schema();
		$schema['title']      = __( 'The returned fulfillment meta data response.', 'woocommerce' );
		$schema['properties'] = array(
			'meta_data' => array(
				'description' => __( 'The meta data array.', 'woocommerce' ),
				'type'        => 'array',
				'required'    => true,
				'items'       => array(
					'description' => __( 'The meta data object.', 'woocommerce' ),
					'type'        => 'object',
					'schema'      => $this->get_schema_for_meta_data(),
				),
			),
		);

		return $schema;
	}

	/**
	 * Get the arguments for the update fulfillment meta endpoint.
	 *
	 * @return array
	 */
	private function get_args_for_update_fulfillment_meta(): array {
		return array(
			'order_id'       => array(
				'description' => __( 'Unique identifier for the order.', 'woocommerce' ),
				'type'        => 'integer',
				'required'    => true,
				'context'     => array( 'view', 'edit' ),
			),
			'fulfillment_id' => array(
				'description' => __( 'Unique identifier for the fulfillment.', 'woocommerce' ),
				'type'        => 'integer',
				'required'    => true,
				'context'     => array( 'view', 'edit' ),
			),
			'meta_data'      => array(
				'description' => __( 'The meta data array.', 'woocommerce' ),
				'type'        => 'array',
				'required'    => true,
				'items'       => array(
					'description' => __( 'The meta data object.', 'woocommerce' ),
					'type'        => 'object',
					'schema'      => $this->get_schema_for_meta_data(),
				),
			),
		);
	}

	/**
	 * Get the schema for the update fulfillment meta endpoint.
	 *
	 * @return array
	 */
	private function get_schema_for_update_fulfillment_meta(): array {
		$schema               = $this->get_base_schema();
		$schema['title']      = __( 'The updated fulfillment meta data response.', 'woocommerce' );
		$schema['properties'] = array(
			'meta_data' => array(
				'description' => __( 'The meta data array.', 'woocommerce' ),
				'type'        => 'array',
				'required'    => true,
				'items'       => array(
					'description' => __( 'The meta data object.', 'woocommerce' ),
					'type'        => 'object',
					'schema'      => $this->get_schema_for_meta_data(),
				),
			),
		);

		return $schema;
	}

	/**
	 * Get the arguments for the delete fulfillment meta endpoint.
	 *
	 * @return array
	 */
	private function get_args_for_delete_fulfillment_meta(): array {
		return array(
			'order_id'       => array(
				'description' => __( 'Unique identifier for the order.', 'woocommerce' ),
				'type'        => 'integer',
				'required'    => true,
				'context'     => array( 'view', 'edit' ),
			),
			'fulfillment_id' => array(
				'description' => __( 'Unique identifier for the fulfillment.', 'woocommerce' ),
				'type'        => 'integer',
				'required'    => true,
				'context'     => array( 'view', 'edit' ),
			),
			'meta_key'       => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
				'description' => __( 'The meta key to delete.', 'woocommerce' ),
				'type'        => 'string',
				'required'    => true,
			),
		);
	}

	/**
	 * Get the schema for the delete fulfillment meta endpoint.
	 *
	 * @return array
	 */
	private function get_schema_for_delete_fulfillment_meta(): array {
		$schema               = $this->get_base_schema();
		$schema['title']      = __( 'The final fulfillment meta data response.', 'woocommerce' );
		$schema['properties'] = array(
			'meta_data' => array(
				'description' => __( 'The meta data array.', 'woocommerce' ),
				'type'        => 'array',
				'required'    => true,
				'items'       => array(
					'description' => __( 'The meta data object.', 'woocommerce' ),
					'type'        => 'object',
					'schema'      => $this->get_schema_for_meta_data(),
				),
			),
		);

		return $schema;
	}

	/**
	 * Get the arguments for the get tracking number details endpoint.
	 *
	 * @return array
	 */
	private function get_args_for_get_tracking_number_details(): array {
		return array(
			'tracking_number' => array(
				'description' => __( 'The tracking number to look up.', 'woocommerce' ),
				'type'        => 'string',
				'required'    => true,
				'context'     => array( 'view', 'edit' ),
			),
		);
	}

	/**
	 * Get the schema for the get tracking number details endpoint.
	 *
	 * @return array
	 */
	private function get_schema_for_get_tracking_number_details(): array {
		return array(
			'tracking_number_details' => array(
				'description' => __( 'The tracking number details.', 'woocommerce' ),
				'type'        => 'object',
				'required'    => true,
				'properties'  => array(
					'tracking_number'   => array(
						'description' => __( 'The tracking number.', 'woocommerce' ),
						'type'        => 'string',
						'required'    => true,
					),
					'shipping_provider' => array(
						'description' => __( 'The shipping provider.', 'woocommerce' ),
						'type'        => 'string',
						'required'    => true,
					),
					'tracking_url'      => array(
						'description' => __( 'The tracking URL.', 'woocommerce' ),
						'type'        => 'string',
						'required'    => true,
					),
				),
			),
		);
	}

	/**
	 * Get the base schema for the fulfillment with a read context.
	 *
	 * @return array
	 */
	private function get_read_schema_for_fulfillment() {
		return array(
			'fulfillment_id' => array(
				'description' => __( 'Unique identifier for the fulfillment.', 'woocommerce' ),
				'type'        => 'integer',
				'context'     => array( 'view', 'edit' ),
				'readonly'    => true,
			),
			'entity_type'    => array(
				'description' => __( 'The type of entity for which the fulfillment is created.', 'woocommerce' ),
				'type'        => 'string',
				'required'    => true,
				'context'     => array( 'view', 'edit' ),
			),
			'entity_id'      => array(
				'description' => __( 'Unique identifier for the entity.', 'woocommerce' ),
				'type'        => 'string',
				'required'    => true,
				'context'     => array( 'view', 'edit' ),
			),
			'status'         => array(
				'description' => __( 'The status of the fulfillment.', 'woocommerce' ),
				'type'        => 'string',
				'default'     => 'unfulfilled',
				'required'    => true,
				'context'     => array( 'view', 'edit' ),
			),
			'is_fulfilled'   => array(
				'description' => __( 'Whether the fulfillment is fulfilled.', 'woocommerce' ),
				'type'        => 'boolean',
				'default'     => false,
				'required'    => true,
				'context'     => array( 'view', 'edit' ),
			),
			'date_updated'   => array(
				'description' => __( 'The date the fulfillment was created.', 'woocommerce' ),
				'type'        => 'string',
				'context'     => array( 'view', 'edit' ),
				'readonly'    => true,
				'required'    => true,
			),
			'date_deleted'   => array(
				'description' => __( 'The date the fulfillment was deleted.', 'woocommerce' ),
				'type'        => 'string',
				'default'     => null,
				'context'     => array( 'view', 'edit' ),
				'readonly'    => true,
				'required'    => true,
			),
			'meta_data'      => array(
				'description' => __( 'Meta data for the fulfillment.', 'woocommerce' ),
				'type'        => 'array',
				'required'    => true,
				'schema'      => $this->get_schema_for_meta_data(),
			),
		);
	}

	/**
	 * Get the base args for the fulfillment with a write context.
	 *
	 * @param bool $is_create Whether the args list is for a create request.
	 *
	 * @return array
	 */
	private function get_write_args_for_fulfillment( bool $is_create = false ) {
		return array_merge(
			! $is_create ? array(
				'fulfillment_id' => array(
					'description' => __( 'Unique identifier for the fulfillment.', 'woocommerce' ),
					'type'        => 'integer',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
			) : array(),
			array(
				'entity_type'  => array(
					'description' => __( 'The type of entity for which the fulfillment is created.', 'woocommerce' ),
					'type'        => 'string',
					'required'    => true,
					'context'     => array( 'view', 'edit' ),
				),
				'entity_id'    => array(
					'description' => __( 'Unique identifier for the entity.', 'woocommerce' ),
					'type'        => 'string',
					'required'    => true,
					'context'     => array( 'view', 'edit' ),
				),
				'status'       => array(
					'description' => __( 'The status of the fulfillment.', 'woocommerce' ),
					'type'        => 'string',
					'default'     => 'unfulfilled',
					'required'    => false,
					'context'     => array( 'view', 'edit' ),
				),
				'is_fulfilled' => array(
					'description' => __( 'Whether the fulfillment is fulfilled.', 'woocommerce' ),
					'type'        => 'boolean',
					'default'     => false,
					'required'    => false,
					'context'     => array( 'view', 'edit' ),
				),
				'meta_data'    => array(
					'description' => __( 'Meta data for the fulfillment.', 'woocommerce' ),
					'type'        => 'array',
					'required'    => true,
					'schema'      => $this->get_schema_for_meta_data(),
				),
			)
		);
	}

	/**
	 * Get the schema for the meta data.
	 *
	 * @return array
	 */
	private function get_schema_for_meta_data(): array {
		return array(
			'type'       => 'object',
			'properties' => array(
				'key'   => array(
					'description' => __( 'The key of the meta data.', 'woocommerce' ),
					'type'        => 'string',
					'required'    => true,
					'context'     => array( 'view', 'edit' ),
				),
				'value' => array(
					'description' => __( 'The value of the meta data.', 'woocommerce' ),
					'type'        => 'string',
					'required'    => true,
					'context'     => array( 'view', 'edit' ),
				),
			),
			'required'   => true,
			'context'    => array( 'view', 'edit' ),
			'readonly'   => true,
		);
	}
}
