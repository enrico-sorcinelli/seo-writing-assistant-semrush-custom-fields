<?php
/**
 * Plugin base class.
 *
 * @package semrush-swa-custom-fields
 * @author Enrico Sorcinelli
 */

// Check running WordPress instance.
if ( ! defined( 'ABSPATH' ) ) {
	header( 'HTTP/1.1 404 Not Found' );
	exit();
}

/**
 * Plugin base class.
 */
class SEMrush_SEO_Writing_Assistant_Custom_Fields {

	/**
	 * Instance settings.
	 *
	 * @var array
	 */
	private $settings = array();

	/**
	 * Prefix used for options and postmeta fields, DOM IDs and DB tables.
	 *
	 * @var string
	 */
	private static $prefix = 'semrush_swa_custom_fields_plugin_';

	/**
	 * Instance of this class.
	 *
	 * @var object
	 */
	public static $instance;

	/**
	 * Plugin class constructor.
	 *
	 * @param array $args {
	 *    Constructor arguments list.
	 *
	 *    @type boolean $debug Default value is `false`.
	 * }
	 *
	 * @return object
	 */
	public function __construct( $args = array() ) {

		$this->settings = wp_parse_args(
			array(
				'debug' => false,
			),
			$args
		);

		// Check and load needed compoments.
		if ( is_admin() ) {
			$this->require_components();
		}
	}

	/**
	 * Get the singleton instance of this class.
	 *
	 * @param array $args Constructor arguments list.
	 *
	 * @return object
	 */
	public static function get_instance( $args = array() ) {
		if ( ! ( self::$instance instanceof self ) ) {
			self::$instance = new self( $args );
		}
		return self::$instance;
	}

	/**
	 * This function will include core files before the theme's functions.php
	 * file has been excecuted.
	 */
	public function require_components() {

		// For plugin checks.
		if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		}

		// Check for SEMrush SEO Writing Assistant plugin.
		if ( ! is_plugin_active( 'semrush-seo-writing-assistant/semrush-seo-writing-assistant.php' ) ) {
			return;
		}

		// Add JS to post pages.
		if ( ! self::is_ajax_request() ) {
			add_action( 'admin_print_scripts-post.php', array( $this, 'load_javascript' ), 10, 0 );
			add_action( 'admin_print_scripts-post-new.php', array( $this, 'load_javascript' ), 10, 0 );
		}
	}

	/**
	 * Load JavaScript files.
	 *
	 * @return void
	 */
	public function load_javascript() {

		global $post_type;

		/**
		 * Filter post types array where to add files.
		 *
		 * @param array $post_types. Default to `array( 'post', 'page', 'product' )`.
		 *
		 * @return array
		 */
		$allowed_post_types = apply_filters( 'semrush_seo_writing_assistant_post_types', array( 'post', 'page', 'product' ) );

		if ( empty( $post_type ) || ! in_array( $post_type, $allowed_post_types, true ) ) {
			return;
		}

		wp_enqueue_script(
			self::$prefix . 'js',
			SEMRUSH_SWA_CUSTOM_FIELDS_PLUGIN_BASEURL . '/assets/js/semrush-swa-custom-fields.js',
			array(),
			SEMRUSH_SWA_CUSTOM_FIELDS_PLUGIN_VERSION,
			false
		);
	}

	/**
	 * Check for AJAX request.
	 *
	 * @return boolean
	 */
	public static function is_ajax_request() {

		if (
			// PHPCS:ignore.
			( ! empty( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && strtolower( $_SERVER['HTTP_X_REQUESTED_WITH'] ) === 'xmlhttprequest' )
			|| ( defined( 'DOING_AJAX' ) && DOING_AJAX )
		) {
			return true;
		}

		return false;
	}

	/**
	 * Plugin activation hook.
	 *
	 * @return void
	 */
	public static function plugin_activation() {
		if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		}
		if ( current_user_can( 'activate_plugins' )
			&& ! is_plugin_active( 'semrush-seo-writing-assistant/semrush-seo-writing-assistant.php' ) ) {

			// Throw an error in the WordPress admin console.
			die(
				sprintf(
					'<p style="color: #444; font-size: 13px; line-height: 1.4em;font-family: -apple-system, BlinkMacSystemFont, \'Segoe UI\', Roboto, Oxygen-Sans, Ubuntu, Cantarell, \'Helvetica Neue\', sans-serif;">' . __( 'The <i>%$1s</i> plugin requires %$2s plugin to be active.', 'semrush-swa-custom-fields' ) . '</p>',
					'SEMrush SEO Writing Assistant Custom Fields',
					'<a target="_blank" href="' . esc_url( 'https://wordpress.org/plugins/semrush-seo-writing-assistant/' ) . '">SEMrush SEO Writing Assistant</a>'
				)
			); // WPCS: XSS ok.
		}
	}
}
