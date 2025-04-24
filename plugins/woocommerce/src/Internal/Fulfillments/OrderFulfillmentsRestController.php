<?php declare( strict_types=1 );

namespace Automattic\WooCommerce\Internal\Fulfillments;

use Automattic\WooCommerce\Internal\DataStores\Fulfillments\FulfillmentsDataStore;
use Automattic\WooCommerce\Internal\RestApiControllerBase;
use WP_Http;
use WP_REST_Request;
use WP_REST_Response;

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
		return 'order_fulfillments';
	}

	/**
	 * {@inheritDoc}
	 */
	protected function get_base_schema(): array {
		$schema                = parent::get_base_schema();
		$schema['title']       = __( 'Fulfillment', 'woocommerce' );
		$schema['description'] = __( 'Fulfillments for an order.', 'woocommerce' );
		$schema['properties']  = array(
			'order_id'       => array(
				'description' => __( 'Unique identifier for the order.', 'woocommerce' ),
				'type'        => 'integer',
				'context'     => array( 'view', 'edit' ),
				'readonly'    => true,
			),
			'fulfillment_id' => array(
				'description' => __( 'Unique identifier for the fulfillment.', 'woocommerce' ),
				'type'        => 'integer',
				'context'     => array( 'view', 'edit' ),
				'readonly'    => true,
			),
		);

		return $schema;
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
		// Admins who can manage WooCommerce can view all fulfillments.
		if ( is_admin() && current_user_can( 'manage_woocommerce' ) ) { // phpcs:ignore WordPress.WP.Capabilities.Unknown
			return true;
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
		return $this->get_authentication_error_by_method( $request->get_method() );
	}

	/**
	 * Get the fulfillments for the order.
	 *
	 * @param WP_REST_Request $request The request object.
	 *
	 * @return array|WP_Error The fulfillments for the order, or an error if the request fails.
	 */
	public function get_fulfillments( WP_REST_Request $request ) {
		$order_id     = (int) $request->get_param( 'order_id' );
		$fulfillments = array();

		// Fetch fulfillments for the order.
		$datastore    = wc_get_container()->get( FulfillmentsDataStore::class );
		$fulfillments = $datastore->get_fulfillments( $order_id );

		return $fulfillments;
	}

	/**
	 * Create a new fulfillment with the given data for the order.
	 *
	 * @param WP_REST_Request $request The request object.
	 *
	 * @return WP_REST_Response|WP_Error The created fulfillment, or an error if the request fails.
	 */
	public function create_fulfillment( WP_REST_Request $request ) {
		$order_id = (int) $request->get_param( 'order_id' );

		// Create a new fulfillment.
		$datastore   = wc_get_container()->get( FulfillmentsDataStore::class );
		$fulfillment = $datastore->create_fulfillment( $order_id, $request->get_json_params() );

		if ( is_wp_error( $fulfillment ) ) {
			return new WP_REST_Response(
				'woocommerce_rest_fulfillment_create_failed',
				__( 'Failed to create fulfillment.', 'woocommerce' ),
				array( 'status' => WP_Http::INTERNAL_SERVER_ERROR )
			);
		}

		return new WP_REST_Response(
			array(
				'fulfillment_id' => $fulfillment->get_id(),
			),
			WP_Http::CREATED
		);
	}

	/**
	 * Get a specific fulfillment for the order.
	 *
	 * @param WP_REST_Request $request The request object.
	 *
	 * @return WP_REST_Response|WP_Error The fulfillment for the order, or an error if the request fails.
	 */
	public function get_fulfillment( WP_REST_Request $request ) {
		$order_id       = (int) $request->get_param( 'order_id' );
		$fulfillment_id = (int) $request->get_param( 'fulfillment_id' );

		// Fetch the fulfillment for the order.
		$datastore = wc_get_container()->get( FulfillmentsDataStore::class );

		return new WP_REST_Response(
			array(
				'fulfillment_id' => 0,
			),
			WP_Http::OK
		);
	}

	/**
	 * Update a specific fulfillment for the order.
	 *
	 * @param WP_REST_Request $request The request object.
	 *
	 * @return WP_REST_Response|WP_Error The updated fulfillment, or an error if the request fails.
	 */
	public function update_fulfillment( WP_REST_Request $request ) {
		$order_id       = (int) $request->get_param( 'order_id' );
		$fulfillment_id = (int) $request->get_param( 'fulfillment_id' );

		// Update the fulfillment for the order.
		$datastore = wc_get_container()->get( FulfillmentsDataStore::class );

		return new WP_REST_Response(
			array(
				'fulfillment_id' => 0,
			),
			WP_Http::OK
		);
	}

	/**
	 * Delete a specific fulfillment for the order.
	 *
	 * @param WP_REST_Request $request The request object.
	 *
	 * @return WP_REST_Response|WP_Error The deleted fulfillment, or an error if the request fails.
	 */
	public function delete_fulfillment( WP_REST_Request $request ) {
		$order_id       = (int) $request->get_param( 'order_id' );
		$fulfillment_id = (int) $request->get_param( 'fulfillment_id' );

		// Delete the fulfillment for the order.
		$datastore = wc_get_container()->get( FulfillmentsDataStore::class );

		return new WP_REST_Response(
			array(
				'fulfillment_id' => 0,
			),
			WP_Http::NO_CONTENT
		);
	}

	/**
	 * Get the metadata for a specific fulfillment.
	 *
	 * @param WP_REST_Request $request The request object.
	 *
	 * @return WP_REST_Response|WP_Error The metadata for the fulfillment, or an error if the request fails.
	 */
	public function get_fulfillment_meta( WP_REST_Request $request ) {
		$order_id       = (int) $request->get_param( 'order_id' );
		$fulfillment_id = (int) $request->get_param( 'fulfillment_id' );

		// Fetch the metadata for the fulfillment.
		$datastore = wc_get_container()->get( FulfillmentsDataStore::class );

		return new WP_REST_Response(
			array(
				'meta' => array(),
			),
			WP_Http::OK
		);
	}

	/**
	 * Update the metadata for a specific fulfillment.
	 *
	 * @param WP_REST_Request $request The request object.
	 *
	 * @return WP_REST_Response|WP_Error The updated metadata for the fulfillment, or an error if the request fails.
	 */
	public function update_fulfillment_meta( WP_REST_Request $request ) {
		$order_id       = (int) $request->get_param( 'order_id' );
		$fulfillment_id = (int) $request->get_param( 'fulfillment_id' );

		// Update the metadata for the fulfillment.
		$datastore = wc_get_container()->get( FulfillmentsDataStore::class );

		return new WP_REST_Response(
			array(
				'meta' => array(),
			),
			WP_Http::OK
		);
	}

	/**
	 * Delete the metadata for a specific fulfillment.
	 *
	 * @param WP_REST_Request $request The request object.
	 *
	 * @return WP_REST_Response|WP_Error The deleted metadata for the fulfillment, or an error if the request fails.
	 */
	public function delete_fulfillment_meta( WP_REST_Request $request ) {
		$order_id       = (int) $request->get_param( 'order_id' );
		$fulfillment_id = (int) $request->get_param( 'fulfillment_id' );

		// Delete the metadata for the fulfillment.
		$datastore = wc_get_container()->get( FulfillmentsDataStore::class );

		return new WP_REST_Response(
			array(
				'meta' => array(),
			),
			WP_Http::NO_CONTENT
		);
	}

	/**
	 * Get the tracking number details for a given tracking number, if possible.
	 *
	 * @param WP_REST_Request $request The request object.
	 *
	 * @return WP_REST_Response|WP_Error The tracking number details, or an error if the request fails.
	 */
	public function get_tracking_number_details( WP_REST_Request $request ) {
		$tracking_number = $request->get_param( 'tracking_number' );

		return new WP_REST_Response(
			array(
				'tracking_number_details' => array(),
			),
			WP_Http::OK
		);
	}

	/**
	 * Get the arguments for the get fulfillments endpoint.
	 *
	 * @return array
	 */
	private function get_args_for_get_fulfillments(): array {
		return array();
	}

	/**
	 * Get the schema for the get fulfillments endpoint.
	 *
	 * @return array
	 */
	private function get_schema_for_get_fulfillments(): array {
		return array();
	}

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
