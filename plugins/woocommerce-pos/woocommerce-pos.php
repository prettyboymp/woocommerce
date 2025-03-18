<?php
/**
 * Plugin Name: WooCommerce POS
 * Plugin URI: https://woocommerce.com/products/woocommerce-pos/
 * Description: Point of Sale extension for WooCommerce.
 * Version: 1.0.0
 * Author: WooCommerce
 * Author URI: https://woocommerce.com/
 * Text Domain: woocommerce-pos
 * Domain Path: /languages
 * Requires at least: 6.0
 * Requires PHP: 7.4
 * WC requires at least: 8.0
 * WC tested up to: 8.5
 *
 * @package WooCommerce\POS
 */

defined( 'ABSPATH' ) || exit;

// Define WC_POS_PLUGIN_FILE.
if ( ! defined( 'WC_POS_PLUGIN_FILE' ) ) {
	define( 'WC_POS_PLUGIN_FILE', __FILE__ );
}

// Define WC_POS_PLUGIN_DIR.
if ( ! defined( 'WC_POS_PLUGIN_DIR' ) ) {
	define( 'WC_POS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
}

// Define WC_POS_PLUGIN_URL.
if ( ! defined( 'WC_POS_PLUGIN_URL' ) ) {
	define( 'WC_POS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}

/**
 * Main WooCommerce POS Class.
 */
class WC_POS {

	/**
	 * Plugin instance.
	 *
	 * @var WC_POS
	 */
	protected static $instance = null;

	/**
	 * Main WooCommerce POS Instance.
	 *
	 * Ensures only one instance of WooCommerce POS is loaded or can be loaded.
	 *
	 * @static
	 * @return WC_POS - Main instance.
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->init_hooks();
		$this->includes();
	}

	/**
	 * Hook into actions and filters.
	 */
	private function init_hooks() {
		add_action( 'plugins_loaded', array( $this, 'on_plugins_loaded' ), 20 );
	}

	/**
	 * Include required files.
	 */
	private function includes() {
		// We'll load the email class only in the register_emails method
		// No need to include it here
	}

	/**
	 * Code to run on plugins loaded.
	 */
	public function on_plugins_loaded() {
		// Check if WooCommerce is active
		if ( ! class_exists( 'WooCommerce' ) ) {
			add_action( 'admin_notices', array( $this, 'woocommerce_missing_notice' ) );
			return;
		}

		// Load plugin text domain.
		load_plugin_textdomain( 'woocommerce-pos', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
		
		// Include POS email classes.
		add_filter( 'woocommerce_email_classes', array( $this, 'register_emails' ) );
		
		// Add template locator for POS email templates.
		add_filter( 'woocommerce_locate_template', array( $this, 'locate_template' ), 10, 3 );
	}

	/**
	 * Register POS emails with WooCommerce.
	 *
	 * @param array $email_classes Email classes.
	 * @return array
	 */
	public function register_emails( $email_classes ) {
		error_log('WooCommerce POS: Starting email registration process...');
		
		// Check if our template directory exists
		if (!file_exists(WC_POS_PLUGIN_DIR . 'templates/emails/')) {
			error_log('WooCommerce POS: Email templates directory not found at ' . WC_POS_PLUGIN_DIR . 'templates/emails/');
			return $email_classes;
		}
		
		// Get all email class files from our plugin
		$email_files = glob(WC_POS_PLUGIN_DIR . 'includes/emails/class-wc-email-*.php');
		error_log('WooCommerce POS: Found ' . count($email_files) . ' email class files.');
		
		if (empty($email_files)) {
			error_log('WooCommerce POS: No email class files found in ' . WC_POS_PLUGIN_DIR . 'includes/emails/');
			return $email_classes;
		}
		
		// Include and instantiate each email class
		foreach ($email_files as $file) {
			error_log('WooCommerce POS: Processing file - ' . basename($file));
			
			// Skip the base abstract class
			if (strpos($file, 'class-wc-email-pos-base.php') !== false) {
				error_log('WooCommerce POS: Skipping base abstract class file - ' . basename($file));
				continue;
			}
			
			// Extract class name from filename (e.g., class-wc-email-customer-pos-completed-order.php => WC_Email_Customer_POS_Completed_Order)
			$basename = basename($file, '.php');
			$class_name = str_replace('-', '_', $basename);
			$class_name = str_replace('class_', '', $class_name);
			$class_name = str_replace('_', ' ', $class_name);
			$class_name = ucwords($class_name);
			$class_name = str_replace(' ', '_', $class_name);
			
			// Include the file if it hasn't been included yet
			if (!class_exists($class_name, false)) {
				require_once $file;
			}
			
			// Check if class exists after including and is not abstract
			if (class_exists($class_name, false)) {
				$reflection = new ReflectionClass($class_name);
				
				// Skip abstract classes
				if ($reflection->isAbstract()) {
					error_log("WooCommerce POS: Skipping abstract class {$class_name}");
					continue;
				}
				
				// Validate that the template files exist for this email class
				$email_instance = new $class_name();
				
				if (isset($email_instance->template_html)) {
					$html_template = WC_POS_PLUGIN_DIR . 'templates/' . $email_instance->template_html;
					if (!file_exists($html_template)) {
						error_log("WooCommerce POS: HTML email template not found for {$class_name} at {$html_template}");
					}
				}
				
				if (isset($email_instance->template_plain)) {
					$plain_template = WC_POS_PLUGIN_DIR . 'templates/' . $email_instance->template_plain;
					if (!file_exists($plain_template)) {
						error_log("WooCommerce POS: Plain email template not found for {$class_name} at {$plain_template}");
					}
				}
				
				// Register the email class
				$class_name = get_class( $email_instance );
				$email_classes[$class_name] = $email_instance;
				error_log("WooCommerce POS: Registered email class {$class_name}");
			} else {
				error_log("WooCommerce POS: Failed to load email class from {$file}");
			}
		}
		
		error_log('WooCommerce POS: Email registration process completed.');
		return $email_classes;
	}

	/**
	 * Locate template files from this plugin.
	 *
	 * @param string $template      Template.
	 * @param string $template_name Template name.
	 * @param string $template_path Template path.
	 * @return string
	 */
	public function locate_template( $template, $template_name, $template_path ) {
		// Debug logging
		error_log("WC POS locate_template: Looking for {$template_name}");
		error_log("WC POS locate_template: Original template path: {$template}");
		
		// Instead of an explicit list, check for POS-specific templates by their prefix/naming convention
		// This is more scalable for adding future templates
		$pos_template_patterns = array(
			'emails/customer-pos-',     // For HTML email templates
			'emails/plain/customer-pos-', // For plain text email templates
			'pos-',                     // For general POS templates
		);
		
		// Check if the template matches any of our POS template patterns
		$is_pos_template = false;
		foreach ($pos_template_patterns as $pattern) {
			if (strpos($template_name, $pattern) !== false) {
				$is_pos_template = true;
				break;
			}
		}
		
		if ($is_pos_template) {
			// Check theme first (theme folder or theme/woocommerce/)
			$theme_template = locate_template(
				array(
					trailingslashit( $template_path ) . $template_name,
					$template_name,
				)
			);
			
			if ($theme_template) {
				error_log("WC POS locate_template: Using theme template at {$theme_template}");
				return $theme_template;
			}
			
			// Try to get the template from our plugin
			$plugin_template = WC_POS_PLUGIN_DIR . 'templates/' . $template_name;
			
			if (file_exists($plugin_template)) {
				error_log("WC POS locate_template: Found template at {$plugin_template}");
				return $plugin_template;
			}
			
			error_log("WC POS locate_template: Template NOT found at {$plugin_template}");
			
			// If we reach here, the template is not found in theme or plugin
			// Let's check if it's a POS email template, and if so, try to use the corresponding WooCommerce template
			if (strpos($template_name, 'emails/customer-pos-') !== false) {
				// Try to fallback to regular WooCommerce customer-completed-order.php
				$fallback_name = str_replace('customer-pos-', 'customer-', $template_name);
				$fallback_template = WC()->plugin_path() . '/templates/' . $fallback_name;
				
				if (file_exists($fallback_template)) {
					error_log("WC POS locate_template: Using fallback WooCommerce template at {$fallback_template}");
					return $fallback_template;
				}
			} elseif (strpos($template_name, 'emails/plain/customer-pos-') !== false) {
				// Try to fallback to regular WooCommerce plain customer-completed-order.php
				$fallback_name = str_replace('customer-pos-', 'customer-', $template_name);
				$fallback_template = WC()->plugin_path() . '/templates/' . $fallback_name;
				
				if (file_exists($fallback_template)) {
					error_log("WC POS locate_template: Using fallback WooCommerce plain template at {$fallback_template}");
					return $fallback_template;
				}
			}
		}
		
		return $template;
	}

	/**
	 * WooCommerce missing notice.
	 */
	public function woocommerce_missing_notice() {
		echo '<div class="error"><p>' . sprintf( 
			/* translators: %s: WooCommerce URL */
			esc_html__( 'WooCommerce POS requires WooCommerce to be installed and active. You can download %s here.', 'woocommerce-pos' ), 
			'<a href="https://woocommerce.com/" target="_blank">WooCommerce</a>' 
		) . '</p></div>';
	}
}

/**
 * Main instance of WooCommerce POS.
 *
 * Returns the main instance of WC_POS to prevent the need to use globals.
 *
 * @return WC_POS
 */
function WC_POS() {
	return WC_POS::instance();
}

// Global for backwards compatibility.
$GLOBALS['wc_pos'] = WC_POS(); 
