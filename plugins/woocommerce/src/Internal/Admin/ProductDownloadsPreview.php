<?php
/**
 * ProductDownloads Preview class file.
 *
 * @package WooCommerce\Internal\FileHandlers
 */

declare(strict_types=1);

namespace Automattic\WooCommerce\Internal\Admin;

use Automattic\WooCommerce\Internal\RegisterHooksInterface;

defined( 'ABSPATH' ) || exit;

/**
 * Class for handling secure admin previews of downloadable product files
 * that would otherwise be inaccessible due to server security configurations.
 *
 * @since 9.9.0
 */
class ProductDownloadsPreview implements RegisterHooksInterface {

	/**
	 * Register hooks.
	 *
	 * @since 9.9.0
	 */
	public function register() {
		// Register AJAX actions for admin file serving
		add_action( 'wp_ajax_wc_product_download_preview', array( $this, 'ajax_product_download_preview' ) );
	}

	/**
	 * AJAX handler for product download preview
	 *
	 * @since 9.9.0
	 */
	public function ajax_product_download_preview() {
		// Verify permissions
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			wp_die( esc_html__( 'Unauthorized access.', 'woocommerce' ), 403 );
		}

		// Verify parameters
		$product_id = isset( $_GET['product_id'] ) ? (int) $_GET['product_id'] : 0;
		$attachment_id = isset( $_GET['attachment_id'] ) ? (int) $_GET['attachment_id'] : 0;
		$size = isset( $_GET['size'] ) ? sanitize_text_field( wp_unslash( $_GET['size'] ) ) : 'large';

		if ( ! $product_id || ! $attachment_id ) {
			wp_die( esc_html__( 'Missing required parameters.', 'woocommerce' ), 400 );
		}

		$this->serve_preview_file( $product_id, $attachment_id, $size );
	}

	/**
	 * Serve the preview file
	 *
	 * @since 9.9.0
	 * @param int    $product_id    Product ID.
	 * @param int    $attachment_id Attachment ID.
	 * @param string $size          Image size to display.
	 */
	private function serve_preview_file( int $product_id, int $attachment_id, string $size ) {
		$file_path = get_attached_file( $attachment_id );

		if ( ! $file_path || ! is_readable( $file_path ) ) {
			wp_die( esc_html__( 'File not found', 'woocommerce' ), 404 );
		}

		$mime_type = get_post_mime_type( $attachment_id );
		$allowed_mime_types = array(
			'image/jpeg',
			'image/jpg',
			'image/png',
			'image/gif',
			'image/webp',
		);

		if ( ! in_array( $mime_type, $allowed_mime_types, true ) ) {
			wp_die( esc_html__( 'Invalid file type', 'woocommerce' ), 403 );
		}

		// Handle resized images
		$size = 'full' === $size ? 'large' : $size;
		$resized = image_get_intermediate_size( $attachment_id, $size );

		if ( $resized && isset( $resized['path'] ) ) {
			$uploads_dir = wp_upload_dir();
			$resized_file_path = $uploads_dir['basedir'] . '/' . $resized['path'];

			if ( is_readable( $resized_file_path ) ) {
				$file_path = $resized_file_path;
			}
		}

		// Clean all output buffers
		while ( ob_get_level() ) {
			@ob_end_clean(); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
		}

		// Send headers
		nocache_headers();
		header( 'Content-Type: ' . $mime_type );
		header( 'Content-Length: ' . filesize( $file_path ) );
		header( 'Content-Disposition: inline; filename="' . basename( $file_path ) . '"' );

		// Output file and exit
		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_readfile
		readfile( $file_path );
		exit;
	}

	/**
	 * Get secure URL for admin image
	 *
	 * @since 9.9.0
	 * @param int    $product_id    Product ID.
	 * @param int    $attachment_id Attachment ID.
	 * @param string $size          Image size.
	 * @return string Secure admin image URL.
	 */
	public function get_admin_image_src_url( int $product_id, int $attachment_id, string $size ): string {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			return '';
		}

		$url = admin_url( 'admin-ajax.php' );
		$url = add_query_arg(
			array(
				'action' => 'wc_product_download_preview',
				'product_id' => $product_id,
				'attachment_id' => $attachment_id,
				'size' => $size,
			),
			$url
		);

		return $url;
	}
}
