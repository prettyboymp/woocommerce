<?php
declare(strict_types=1);

namespace Automattic\WooCommerce\Blocks\BlockTypes;

use Automattic\WooCommerce\Blocks\Utils\ProductGalleryUtils;
use WP_Block;

/**
 * ProductGalleryLargeImage class.
 */
class ProductGalleryLargeImage extends AbstractBlock {
	/**
	 * Block name.
	 *
	 * @var string
	 */
	protected $block_name = 'product-gallery-large-image';


	/**
	 * Get the frontend style handle for this block type.
	 *
	 * @return null
	 */
	protected function get_block_type_style() {
		return null;
	}

	/**
	 *  Register the context
	 *
	 * @return string[]
	 */
	protected function get_block_type_uses_context() {
		return [ 'postId', 'hoverZoom', 'fullScreenOnClick' ];
	}

	/**
	 * Initialize this block type.
	 *
	 * - Hook into WP lifecycle.
	 * - Register the block with WordPress.
	 * - Hook into pre_render_block to update the query.
	 */
	protected function initialize() {
		add_filter( 'block_type_metadata_settings', array( $this, 'add_block_type_metadata_settings' ), 10, 2 );
		parent::initialize();
	}

	/**
	 * Enqueue frontend assets for this block, just in time for rendering.
	 *
	 * @param array    $attributes  Any attributes that currently are available from the block.
	 * @param string   $content    The block content.
	 * @param WP_Block $block    The block object.
	 */
	protected function enqueue_assets( array $attributes, $content, $block ) {
		if ( $block->context['hoverZoom'] || $block->context['fullScreenOnClick'] ) {
			parent::enqueue_assets( $attributes, $content, $block );
		}
	}

	/**
	 * Include and render the block.
	 *
	 * @param array    $attributes Block attributes. Default empty array.
	 * @param string   $content    Block content. Default empty string.
	 * @param WP_Block $block      Block instance.
	 * @return string Rendered block type output.
	 */
	protected function render( $attributes, $content, $block ) {
		$post_id = $block->context['postId'];

		if ( ! isset( $post_id ) ) {
			return '';
		}

		global $product;

		$previous_product = $product;
		$product          = wc_get_product( $post_id );
		if ( ! $product instanceof \WC_Product ) {
			$product = $previous_product;

			return '';
		}

		$images_html = $this->get_main_images_html( $block->context, $block, $product );
		wp_enqueue_script_module( $this->get_full_block_name() );

		$processor = new \WP_HTML_Tag_Processor( $content );
		$processor->next_tag();
		$processor->remove_class( 'wp-block-woocommerce-product-gallery-large-image' );
		$content = $processor->get_updated_html();

		ob_start();
		?>
			<div class="wc-block-product-gallery-large-image wp-block-woocommerce-product-gallery-large-image">
				<?php // No need to use wp_kses here because the image HTML is built internally. ?>
				<?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<?php echo $images_html; ?>
				<?php // No need to use wp_kses here because $content is inner blocks which are already escaped. ?>
				<?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<?php echo $content; ?>
			</div>
		<?php
		$html = ob_get_clean();

		return $html;
	}

	/**
	 * Get the main images html code. The first element of the array contains the HTML of the first image that is visible, the second element contains the HTML of the other images that are hidden.
	 *
	 * @param array $context The block context.
	 *
	 * @return array
	 */
	private function get_main_images_html( $context, $block, $product ) {
		$base_classes = 'wc-block-woocommerce-product-gallery-large-image__image';

		$images = ProductGalleryUtils::get_product_gallery_image_data( $product );

		$directives      = $this->get_directives( $context );
		$directives_html = array_reduce(
			array_keys( $directives ),
			function ( $carry, $key ) use ( $directives ) {
				return $carry . ' ' . $key . '="' . esc_attr( $directives[ $key ] ) . '"';
			},
			''
		);

		if ( $context['fullScreenOnClick'] ) {
			$base_classes .= ' wc-block-woocommerce-product-gallery-large-image__image--full-screen-on-click';
		}
		if ( $context['hoverZoom'] ) {
			$base_classes .= ' wc-block-woocommerce-product-gallery-large-image__image--hoverZoom';
		}

		// Get an instance of the Product Image block.
		$product_image_block = $block->inner_blocks[0]->parsed_block;
		$next_prev_arrows    = $block->inner_blocks[1]->parsed_block;

		ob_start();
		?>
			<ul class="wc-block-product-gallery-large-image__container" tabindex="-1">
				<?php foreach ( $images['images'] as $image ) : ?>
					<li class="wc-block-product-gallery-large-image__wrapper" <?php echo $directives_html; ?>>
						<?php
							$block_content = (
								new WP_Block(
									$product_image_block,
									array(
										'imageId' => $image['id'],
										'postId'  => $context['postId'],
									)
								)
							)->render( array( 'dynamic' => true ) );
							echo $block_content;
						?>
						<!-- <img
							class="<?php echo esc_attr( $base_classes ); ?>"
							data-wp-bind--src="context.image.src"
							data-wp-bind--srcset="context.image.srcset"
							data-wp-bind--sizes="context.image.sizes"
							data-wp-bind--data-image-id="context.image.id"
							data-wp-bind--tabindex="context.image.tabIndex"
							data-wp-on--keydown="actions.onSelectedLargeImageKeyDown"
							data-wp-class--wc-block-woocommerce-product-gallery-large-image__image--active-image-slide="context.image.isActive"
							data-wp-on--touchstart="actions.onTouchStart"
							data-wp-on--touchmove="actions.onTouchMove"
							data-wp-on--touchend="actions.onTouchEnd"
							loading="lazy"
							alt=""
						/> -->
					</li>
				<?php endforeach; ?>
			</ul>
			<?php
				$block_content = (
					new WP_Block(
						$next_prev_arrows,
						array(
							'imageId' => $image['id'],
							'postId'  => $context['postId'],
						)
					)
				)->render( array( 'dynamic' => true ) );
				echo $block_content;
			?>
		<?php
		$template = ob_get_clean();

		return $template;
	}

	/**
	 * Get directives for the block.
	 *
	 * @param array $block_context The block context.
	 *
	 * @return array
	 */
	private function get_directives( $block_context ) {
		return array_merge(
			$this->get_zoom_directives( $block_context ),
			$this->get_open_dialog_directives( $block_context )
		);
	}

	/**
	 * Get directives for zoom.
	 *
	 * @param array $block_context The block context.
	 *
	 * @return array
	 */
	private function get_zoom_directives( $block_context ) {
		if ( ! $block_context['hoverZoom'] ) {
			return array();
		}

		return array(
			'data-wp-interactive'    => 'woocommerce/product-gallery',
			'data-wp-on--mousemove'  => 'actions.startZoom',
			'data-wp-on--mouseleave' => 'actions.resetZoom',
		);
	}

	/**
	 * Get directives for opening the dialog.
	 *
	 * @param array $block_context The block context.
	 *
	 * @return array
	 */
	private function get_open_dialog_directives( $block_context ) {
		if ( ! $block_context['fullScreenOnClick'] ) {
			return array();
		}

		return array(
			'data-wp-on--click' => 'actions.openDialog',
		);
	}

	/**
	 * Disable the block type script, this uses script modules.
	 *
	 * @param string|null $key The key.
	 *
	 * @return null
	 */
	protected function get_block_type_script( $key = null ) {
		return null;
	}

		/**
		 * Product Template renders inner blocks manually so we need to skip default
		 * rendering routine for its inner blocks
		 *
		 * @param array $settings Array of determined settings for registering a block type.
		 * @param array $metadata Metadata provided for registering a block type.
		 * @return array
		 */
	public function add_block_type_metadata_settings( $settings, $metadata ) {
		if ( ! empty( $metadata['name'] ) && 'woocommerce/product-gallery-large-image' === $metadata['name'] ) {
			$settings['skip_inner_blocks'] = true;
		}
			return $settings;
	}
}
