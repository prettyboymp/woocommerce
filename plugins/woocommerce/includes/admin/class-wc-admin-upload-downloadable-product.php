<?php
/**
 * Add hooks related to uploading downloadable products.
 *
 * @package     WooCommerce\Admin
 * @version     9.9.0
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use WP_Error;

if ( class_exists( 'WC_Admin_Upload_Downloadable_Product', false ) ) {
	return new WC_Admin_Upload_Downloadable_Product();
}

/**
 * WC_Admin_Upload_Downloadable_Product Class.
 */
class WC_Admin_Upload_Downloadable_Product {
	/**
	 * Add hooks.
	 */
	public function __construct() {
		add_filter( 'upload_dir', array( $this, 'upload_dir' ) );
		add_filter( 'wp_unique_filename', array( $this, 'update_filename' ), 10, 3 );
		add_action( 'media_upload_downloadable_product', array( $this, 'media_upload_downloadable_product' ) );
		add_filter( 'attachment_fields_to_edit', array( $this, 'disable_editing_for_woocommerce_uploads' ), 10, 2 );
		add_action( 'wp_ajax_imgedit-preview', array( $this, 'block_woocommerce_uploads_ajax_preview' ), 5 ); // Early priority to block before WP handles it.
		add_action( 'wp_ajax_image-editor', array( $this, 'block_woocommerce_uploads_ajax_preview' ), 5 ); // Also block the image-editor action
		add_filter( 'load_image_to_edit_path', array( $this, 'block_woocommerce_uploads_image_edit_path' ), 10, 2 );
		add_filter( 'wp_image_editor_before_change', array( $this, 'block_woocommerce_uploads_image_edit' ), 10, 2 );
		add_action( 'ajax_query_attachments_args', array( $this, 'flag_woocommerce_uploads_attachments' ) );
		add_filter( 'media_send_to_editor', array( $this, 'prevent_woocommerce_uploads_insertion' ), 10, 3 );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}
	
	/**
	 * Enqueue admin scripts to highlight secure files in the media library.
	 *
	 * @since 9.9.0
	 */
	public function enqueue_scripts() {
		global $pagenow;
		
		if ( in_array( $pagenow, array( 'upload.php', 'post.php', 'post-new.php' ), true ) ) {
			// Add inline script to disable editing UI for secure files
			wp_add_inline_script( 'media-editor', $this->get_secure_files_js() );
		}
	}
	
	/**
	 * Get JavaScript to highlight secure files in the media library.
	 * Unlike before, we don't disable editing for admins, but we do show a visual indicator.
	 *
	 * @since 9.9.0
	 * @return string JavaScript code.
	 */
	private function get_secure_files_js() {
		return "
		(function($) {
			// Add visual indicator for secure WooCommerce files
			$(document).on('click', '.attachment[data-woocommerce-secure-file=true]', function(e) {
				// Don't prevent default - allow admins to edit, but highlight that it's a secure file
				if (!$(this).hasClass('woocommerce-secure-file-highlighted')) {
					// We only want to show the notice once per session
					$(this).addClass('woocommerce-secure-file-highlighted');
					
					// Add a visual indicator
					if ($('.woocommerce-secure-file-notice').length === 0) {
						$('<div class=\"woocommerce-secure-file-notice\" style=\"color: #72aee6; padding: 5px; margin-top: 10px;\">' + 
						  '<span style=\"font-weight: bold;\">" . esc_js( __( 'Note:', 'woocommerce' ) ) . "</span> " . 
						  esc_js( __( 'This is a secure WooCommerce downloadable file. Changes will be saved to the secure location.', 'woocommerce' ) ) . "</div>')
						  .insertAfter('.attachment-details');
					}
				}
			});
			
			// Add CSS for secure files
			$('head').append('<style>' +
				'.attachment[data-woocommerce-secure-file=true] { border: 1px solid #72aee6; }' +
				'.attachment.selected[data-woocommerce-secure-file=true] { box-shadow: 0 0 0 3px #72aee6; }' +
			'</style>');
		})(jQuery);";
	}
	/**
	 * Change upload dir for downloadable files.
	 *
	 * @param array $pathdata Array of paths.
	 * @return array
	 */
	public function upload_dir( $pathdata ) {
		// phpcs:disable WordPress.Security.NonceVerification.Missing
		if ( isset( $_POST['type'] ) && 'downloadable_product' === $_POST['type'] ) {

			if ( empty( $pathdata['subdir'] ) ) {
				$pathdata['path']   = $pathdata['path'] . '/woocommerce_uploads';
				$pathdata['url']    = $pathdata['url'] . '/woocommerce_uploads';
				$pathdata['subdir'] = '/woocommerce_uploads';
			} else {
				$new_subdir = '/woocommerce_uploads' . $pathdata['subdir'];

				$pathdata['path']   = str_replace( $pathdata['subdir'], $new_subdir, $pathdata['path'] );
				$pathdata['url']    = str_replace( $pathdata['subdir'], $new_subdir, $pathdata['url'] );
				$pathdata['subdir'] = str_replace( $pathdata['subdir'], $new_subdir, $pathdata['subdir'] );
			}
		}
		return $pathdata;
		// phpcs:enable WordPress.Security.NonceVerification.Missing
	}

	/**
	 * Change filename for WooCommerce uploads and prepend unique chars for security.
	 *
	 * @param string $full_filename Original filename.
	 * @param string $ext           Extension of file.
	 * @param string $dir           Directory path.
	 *
	 * @return string New filename with unique hash.
	 * @since 4.0
	 */
	public function update_filename( $full_filename, $ext, $dir ) {
		// phpcs:disable WordPress.Security.NonceVerification.Missing
		if ( ! isset( $_POST['type'] ) || ! 'downloadable_product' === $_POST['type'] ) {
			return $full_filename;
		}

		if ( ! strpos( $dir, 'woocommerce_uploads' ) ) {
			return $full_filename;
		}

		if ( 'no' === get_option( 'woocommerce_downloads_add_hash_to_filename' ) ) {
			return $full_filename;
		}

		return $this->unique_filename( $full_filename, $ext );
		// phpcs:enable WordPress.Security.NonceVerification.Missing
	}

	/**
	 * Change filename to append random text.
	 *
	 * @param string $full_filename Original filename with extension.
	 * @param string $ext           Extension.
	 *
	 * @return string Modified filename.
	 */
	public function unique_filename( $full_filename, $ext ) {
		$ideal_random_char_length = 6;   // Not going with a larger length because then downloaded filename will not be pretty.
		$max_filename_length      = 255; // Max file name length for most file systems.
		$length_to_prepend        = min( $ideal_random_char_length, $max_filename_length - strlen( $full_filename ) - 1 );

		if ( 1 > $length_to_prepend ) {
			return $full_filename;
		}

		$suffix   = strtolower( wp_generate_password( $length_to_prepend, false, false ) );
		$filename = $full_filename;

		if ( strlen( $ext ) > 0 ) {
			$filename = substr( $filename, 0, strlen( $filename ) - strlen( $ext ) );
		}

		$full_filename = str_replace(
			$filename,
			"$filename-$suffix",
			$full_filename
		);

		return $full_filename;
	}

	/**
	 * Run a filter when uploading a downloadable product.
	 */
	public function media_upload_downloadable_product() {
		// phpcs:ignore WooCommerce.Commenting.CommentHooks.MissingHookComment
		do_action( 'media_upload_file' );
	}

	/**
	 * Add notice for images in woocommerce_uploads directory.
	 * For admins, we don't disable editing, but we do add a notice.
	 *
	 * @since 9.9.0
	 *
	 * @param array   $form_fields Array of form fields for attachment.
	 * @param WP_Post $post        Attachment post object.
	 * @return array Modified form fields.
	 */
	public function disable_editing_for_woocommerce_uploads( $form_fields, $post ) {
		// Check if this is a woocommerce_uploads image.
		$file_path = get_attached_file( $post->ID );

		if ( $file_path && false !== strpos( $file_path, 'woocommerce_uploads' ) ) {
			if ( ! current_user_can( 'manage_woocommerce' ) ) {
				// For non-admins, disable editing completely
				$form_fields = array(
					'woocommerce_secure_file' => array(
						'label' => '',
						'input' => 'html',
						'html'  => '<p class="description">' . esc_html__( 'This is a secure WooCommerce downloadable file and cannot be edited.', 'woocommerce' ) . '</p>',
					),
				);

				// Add the URL field back for copy/paste functionality.
				if ( isset( $post->guid ) && ! empty( $post->guid ) ) {
					$form_fields['url'] = array(
						'label' => __( 'File URL', 'woocommerce' ),
						'input' => 'html',
						'html'  => '<input type="text" class="text urlfield" readonly="readonly" name="attachments[' . $post->ID . '][url]" value="' . esc_attr( $post->guid ) . '" />',
						'helps' => __( 'Location of the uploaded file.', 'woocommerce' ),
					);
				}
			} else {
				// For admins, just add a notice at the top
				$notice = '<div class="woocommerce-secure-file-notice" style="background-color: #f0f6fc; color: #2271b1; padding: 8px; margin-bottom: 10px; border-left: 4px solid #2271b1;">' .
					'<strong>' . esc_html__( 'Secure File Notice:', 'woocommerce' ) . '</strong> ' .
					esc_html__( 'This is a secure WooCommerce downloadable file located in a protected directory.', 'woocommerce' ) .
					'</div>';
				
				// Add our notice to the top of existing fields
				$form_fields = array_merge(
					array(
						'woocommerce_secure_file_notice' => array(
							'label' => '',
							'input' => 'html',
							'html'  => $notice,
						),
					),
					$form_fields
				);
			}
		}

		return $form_fields;
	}

	/**
	 * Handles AJAX image edit preview for woocommerce_uploads files.
	 * Rather than blocking edits for admins, ensures the preview is securely served.
	 *
	 * @since 9.9.0
	 */
	public function block_woocommerce_uploads_ajax_preview() {
		// We're not blocking this anymore for admins, instead we'll ensure it's secure
		// by checking capabilities before allowing access
		
		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		if ( ! isset( $_REQUEST['postid'] ) ) {
			return;
		}

		$attachment_id = intval( $_REQUEST['postid'] );
		$file_path     = get_attached_file( $attachment_id );

		// Only intercept requests for woocommerce_uploads files
		if ( $file_path && false !== strpos( $file_path, 'woocommerce_uploads' ) ) {
			// Check if user has permission to edit this attachment
			if ( ! current_user_can( 'manage_woocommerce' ) || ! current_user_can( 'edit_post', $attachment_id ) ) {
				wp_send_json_error(
					array(
						'error' => __( 'You do not have permission to edit secure WooCommerce downloads.', 'woocommerce' ),
					),
					403
				);
				exit;
			}
			
			// For admins with permission, allow the preview to continue
			// We'll let WordPress handle the rest of the process
		}
		// phpcs:enable WordPress.Security.NonceVerification.Recommended
	}

	/**
	 * Handles loading secure WooCommerce uploads files for editing.
	 * Only blocks access for users without proper permissions.
	 * 
	 * @since 9.9.0
	 *
	 * @param string $filepath      Path to the image to load.
	 * @param int    $attachment_id Attachment ID.
	 * @return string|null Path to the image, or null to prevent editing.
	 */
	public function block_woocommerce_uploads_image_edit_path( $filepath, $attachment_id ) {
		if ( $filepath && false !== strpos( $filepath, 'woocommerce_uploads' ) ) {
			// Only allow WooCommerce admins to edit
			if ( ! current_user_can( 'manage_woocommerce' ) || ! current_user_can( 'edit_post', $attachment_id ) ) {
				return null; // Return null to prevent editing for non-admins
			}
			
			// For admins, make sure the file is accessible
			if ( ! is_readable( $filepath ) ) {
				// If file isn't directly readable (e.g., due to server permissions),
				// we could potentially copy it to a temp location or handle differently
				// But for now, we'll just return the original path and let WordPress handle any errors
			}
		}

		return $filepath;
	}

	/**
	 * Handles image editor changes for secure WooCommerce uploads files.
	 * Only blocks for users without proper permissions.
	 *
	 * @since 9.9.0
	 *
	 * @param WP_Image_Editor $editor   The image editor instance.
	 * @param array           $changes  Array of change operations.
	 * @return WP_Image_Editor|WP_Error
	 */
	public function block_woocommerce_uploads_image_edit( $editor, $changes ) {
		$file = $editor->get_file();

		if ( $file && false !== strpos( $file, 'woocommerce_uploads' ) ) {
			// Need to determine the attachment ID from the file path
			global $wpdb;
			$attachment_id = $wpdb->get_var(
				$wpdb->prepare(
					"SELECT post_id FROM $wpdb->postmeta WHERE meta_key = '_wp_attached_file' AND meta_value LIKE %s",
					'%' . $wpdb->esc_like( basename( $file ) ) . '%'
				)
			);
			
			// If we can't find the attachment or user doesn't have permission, block editing
			if ( ! $attachment_id || ! current_user_can( 'manage_woocommerce' ) || ! current_user_can( 'edit_post', $attachment_id ) ) {
				return new WP_Error( 'security_error', __( 'You do not have permission to edit secure WooCommerce downloads.', 'woocommerce' ) );
			}
		}

		return $editor;
	}

	/**
	 * Flag woocommerce_uploads attachments in media library queries.
	 * This allows us to apply special handling in the UI.
	 *
	 * @since 9.9.0
	 *
	 * @param array $args WP_Query arguments.
	 * @return array
	 */
	public function flag_woocommerce_uploads_attachments( $args ) {
		add_filter( 'wp_prepare_attachment_for_js', array( $this, 'add_woocommerce_uploads_flag' ), 10, 2 );
		return $args;
	}

	/**
	 * Add a flag to woocommerce_uploads attachments in JS data.
	 * For admins, doesn't prevent editing but adds visual indicators.
	 *
	 * @since 9.9.0
	 *
	 * @param array   $response   Attachment data for JS.
	 * @param WP_Post $attachment Attachment object.
	 * @return array
	 */
	public function add_woocommerce_uploads_flag( $response, $attachment ) {
		$file_path = get_attached_file( $attachment->ID );

		if ( $file_path && false !== strpos( $file_path, 'woocommerce_uploads' ) ) {
			$response['woocommerce_secure_file'] = true;
			
			// Add classes to make it easier to target with CSS/JS
			if ( isset( $response['classes'] ) ) {
				$response['classes'] .= ' woocommerce-secure-file';
			} else {
				$response['classes'] = 'woocommerce-secure-file';
			}
			
			// Add data attribute for JS targeting
			if ( isset( $response['data'] ) && is_array( $response['data'] ) ) {
				$response['data']['woocommerce-secure-file'] = 'true';
			} else {
				$response['data'] = array(
					'woocommerce-secure-file' => 'true',
				);
			}
			
			// For non-admins, remove edit capabilities
			if ( ! current_user_can( 'manage_woocommerce' ) || ! current_user_can( 'edit_post', $attachment->ID ) ) {
				// Remove edit buttons from actions
				if ( isset( $response['buttons'] ) && isset( $response['buttons']['edit'] ) ) {
					unset( $response['buttons']['edit'] );
				}
				
				// Add a notice to the description about the secure file
				if ( isset( $response['description'] ) ) {
					$response['description'] .= '<p class="woocommerce-secure-file-notice" style="color: #d63638;">' . 
						esc_html__( 'This is a secure WooCommerce downloadable file and cannot be edited by your user role.', 'woocommerce' ) . 
						'</p>';
				}
			} else {
				// For admins, add a visible but non-blocking notice
				if ( isset( $response['description'] ) ) {
					$response['description'] .= '<p class="woocommerce-secure-file-notice" style="color: #2271b1;">' . 
						esc_html__( 'This is a secure WooCommerce downloadable file in a protected directory.', 'woocommerce' ) . 
						'</p>';
				}
			}
		}

		return $response;
	}

	/**
	 * Handle woocommerce_uploads files being inserted into the editor.
	 * Only prevents insertion for users without proper permissions.
	 *
	 * @since 9.9.0
	 *
	 * @param string $html       HTML markup for the media item.
	 * @param int    $id         Attachment ID.
	 * @param array  $attachment Attachment metadata.
	 * @return string
	 */
	public function prevent_woocommerce_uploads_insertion( $html, $id, $attachment ) {
		$file_path = get_attached_file( $id );
		
		if ( $file_path && false !== strpos( $file_path, 'woocommerce_uploads' ) ) {
			// Only allow WooCommerce admins to insert secure files
			if ( ! current_user_can( 'manage_woocommerce' ) || ! current_user_can( 'edit_post', $id ) ) {
				return '';
			}
			
			// For admins, we'll add a data attribute for custom styling
			$html = str_replace('<img ', '<img data-woocommerce-secure-file="true" ', $html);
		}
		
		return $html;
	}
}