<?php

declare(strict_types=1);

namespace Automattic\WooCommerce\Blocks\BlockTypes;

use Automattic\WooCommerce\Blocks\Assets\BlocksInteractivityConfig;
use Automattic\WooCommerce\Blocks\Assets\Api as AssetApi;
use WP_Block;

/**
 * AbstractInteractivityBlock class. Use this for blocks using the interactivity API.
 */
abstract class AbstractInteractivityAPIBlock {

	/**
	 * Block namespace.
	 *
	 * @var string
	 */
	protected $namespace = 'woocommerce';

	/**
	 * Block name within this namespace.
	 *
	 * @var string
	 */
	protected $block_name = '';

	/**
	 * Asset API instance.
	 *
	 * @var AssetApi
	 */
	protected $asset_api;

	/**
	 * Constructor.
	 *
	 * @param AssetApi $asset_api Instance of the asset API.
	 */
	public function __construct( $asset_api ) {
		$this->asset_api = $asset_api;

		if ( empty( $this->block_name ) ) {
			wc_doing_it_wrong( __METHOD__, 'Block name is required.', '9.9.0' );
		} else {
			$this->register_block_type();
		}

		add_action( 'enqueue_block_editor_assets', [ $this, 'register_editor_script' ] );
	}

	/**
	 * Get the fully qualified block name. e.g. woocommerce/cart
	 *
	 * @return string The block namespace
	 */
	protected function get_full_block_name() {
		return $this->namespace . '/' . $this->block_name;
	}

	/**
	 * The default render_callback for all blocks. This will ensure assets are enqueued just in time, then render
	 * the block (if applicable).
	 *
	 * @param array|WP_Block $attributes Block attributes, or an instance of a WP_Block. Defaults to an empty array.
	 * @param string         $content    Block content. Default empty string.
	 * @param WP_Block|null  $block      Block instance.
	 * @return string Rendered block type output.
	 */
	public function render_callback( $attributes = [], $content = '', $block = null ) {
		$render_callback_attributes = $this->parse_render_callback_attributes( $attributes );
		$result                     = $this->render( $render_callback_attributes, $content, $block );

		if ( ! empty( $result ) ) {
			// Only initialize settings when we know an interactivity block is being rendered.
			BlocksInteractivityConfig::init();

			wp_enqueue_script_module( $this->get_full_block_name() );
			wp_enqueue_style( $this->get_frontend_style_handle() );
		}

		return $result;
	}

	/**
	 * Get the frontend style handle for this block type.
	 * The script modules build generates front-end stylesheets
	 * as <block-name>-style by default.
	 *
	 * @return string|null
	 */
	protected function get_frontend_style_handle() {
		return $this->get_full_block_name() . '-style';
	}

	/**
	 * Get the editor style handle for this block type.
	 * The script modules build generates editor stylesheets
	 * as <block-name>-editor-style by default.
	 *
	 * @return string|null
	 */
	protected function get_editor_style_handle() {
		return $this->get_full_block_name() . '-editor-style';
	}

	/**
	 * Registers the block type with WordPress.
	 *
	 * @throws \Exception When block metadata path is not set.
	 */
	protected function register_block_type() {
		$frontend_style_handle = $this->get_frontend_style_handle();
		$editor_style_handle   = $this->get_editor_style_handle();

		$this->asset_api->register_style( $frontend_style_handle, $this->asset_api->get_block_asset_build_path( $frontend_style_handle, 'css' ), [], 'all', true );
		$this->asset_api->register_style( $editor_style_handle, $this->asset_api->get_block_asset_build_path( $editor_style_handle, 'css' ), [], 'all', true );

		$block_settings = [
			'render_callback' => [ $this, 'render_callback' ],
			'editor_style'    => $editor_style_handle,
			'editor_script'   => $this->get_block_type_editor_script( 'handle' ),
			'style'           => $frontend_style_handle,
		];

		$metadata_path = $this->asset_api->get_block_metadata_path( $this->block_name );

		if ( ! empty( $metadata_path ) ) {
			register_block_type_from_metadata(
				$metadata_path,
				$block_settings
			);
		} else {
			throw new \Exception( 'Block metadata path is required for Interactivity API blocks.' );
		}
	}

	/**
	 * Register the editor script for this block type.
	 */
	public function register_editor_script() {
		if ( null !== $this->get_editor_script() ) {
			$data     = $this->asset_api->get_script_data( $this->get_editor_script( 'path' ) );
			$has_i18n = in_array( 'wp-i18n', $data['dependencies'], true );

			$this->asset_api->register_script(
				$this->get_editor_script( 'handle' ),
				$this->get_editor_script( 'path' ),
				array_merge(
					$this->get_editor_script( 'dependencies' ),
				),
				$has_i18n
			);
		}
	}

	/**
	 * Get the editor script data for this block.
	 *
	 * @param string $key Data to get, or default to everything.
	 * @return array|string
	 */
	protected function get_editor_script( $key = null ) {
		$script = [
			'handle'       => 'wc-' . $this->block_name . '-block',
			'path'         => $this->asset_api->get_block_asset_build_path( $this->block_name ),
			'dependencies' => [ 'wc-blocks' ],
		];
		return $key ? $script[ $key ] : $script;
	}


	/**
	 * Parses block attributes from the render_callback.
	 *
	 * @param array|WP_Block $attributes Block attributes, or an instance of a WP_Block. Defaults to an empty array.
	 * @return array
	 */
	protected function parse_render_callback_attributes( $attributes ) {
		return is_a( $attributes, 'WP_Block' ) ? $attributes->attributes : $attributes;
	}

	/**
	 * Render the block. Extended by children.
	 *
	 * @param array    $attributes Block attributes.
	 * @param string   $content    Block content.
	 * @param WP_Block $block      Block instance.
	 * @return string Rendered block type output.
	 */
	protected function render( $attributes, $content, $block ) {
		return $content;
	}
}
