<?php
/**
 * Plugin base class.
 *
 * @package swa-semrush-custom-fields
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
class SEO_Writing_Assistant_SEMrush_Custom_Fields {

	/**
	 * Instance settings.
	 *
	 * @var array
	 */
	private $settings = array();

	/**
	 * Plugin settings.
	 *
	 * @var array
	 */
	private $plugin_settings;

	/**
	 * Prefix used for options and postmeta fields, DOM IDs and DB tables.
	 *
	 * @var string
	 */
	private static $prefix = 'swa_semrush_custom_fields_';

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

		// Load plugin text domain.
		load_plugin_textdomain( 'swa-semrush-custom-fields', false, dirname( plugin_basename( __FILE__ ) ) . '/../languages/' );

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

		// Get plugin settings.
		$this->plugin_settings = $this->get_plugin_default_settings();

		// Add JS to post pages.
		if ( ! self::is_ajax_request() ) {
			add_action( 'admin_print_scripts-post.php', array( $this, 'load_javascript' ), 10, 0 );
			add_action( 'admin_print_scripts-post-new.php', array( $this, 'load_javascript' ), 10, 0 );
		}

		// Check for ACF.
		if ( function_exists( 'get_field' ) ) {
			add_action( 'acf/render_field_settings/type=text', array( $this, 'acf_render_field_settings' ) );
			add_action( 'acf/render_field_settings/type=textarea', array( $this, 'acf_render_field_settings' ) );
			add_action( 'acf/render_field_settings/type=select', array( $this, 'acf_render_field_settings' ) );
			add_action( 'acf/render_field_settings/type=radio', array( $this, 'acf_render_field_settings' ) );
			add_action( 'acf/render_field_settings/type=checkbox', array( $this, 'acf_render_field_settings' ) );
			add_filter( 'acf/prepare_field', array( $this, 'acf_prepare_field' ) );
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

		// Add JavaScript.
		wp_enqueue_script(
			self::$prefix . 'js',
			SWA_SEMRUSH_CUSTOM_FIELDS_PLUGIN_BASEURL . '/assets/js/swa-semrush-custom-fields.js',
			array(),
			SWA_SEMRUSH_CUSTOM_FIELDS_PLUGIN_VERSION,
			false
		);

		// Localization.
		wp_localize_script(
			self::$prefix . 'js',
			self::$prefix . 'i18n',
			array(
				'enable'   => $this->plugin_settings['enable'],
				'interval' => $this->plugin_settings['interval'],
			)
		);
	}

	/**
	 * Add custom ACF settings.
	 *
	 * @param array $field Field data.
	 */
	function acf_render_field_settings( $field ) {

		acf_render_field_setting(
			$field,
			array(
				'label'        => __( 'SEMrush', 'swa-semrush-custom-fields' ),
				'instructions' => __( 'Add field value to the text used by SEMrush Writing Assistant analysis', 'swa-semrush-custom-fields' ),
				'name'         => 'swa_scf',
				'type'         => 'true_false',
				'ui'           => 1,
			)
		);
	}

	/**
	 * Add class to ACF fields.
	 *
	 * @param array $field Field data.
	 *
	 * @return mixed
	 */
	function acf_prepare_field( $field ) {
		if ( ! empty( $field['swa_scf'] ) ) {
			$field['class'] .= ' swa-scf';
		}
		return $field;
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
	 * Get plugin options settings.
	 *
	 * @return array
	 */
	private function get_plugin_default_settings() {

		$settings = array(
			'enable'   => ( defined( 'SWA_SEMRUSH_CUSTOM_FIELDS_PLUGIN_AUTOENABLE' ) && SWA_SEMRUSH_CUSTOM_FIELDS_PLUGIN_AUTOENABLE === true ) ? true : false,
			'interval' => defined( 'SWA_SEMRUSH_CUSTOM_FIELDS_PLUGIN_INTERVAL' ) ? absint( SWA_SEMRUSH_CUSTOM_FIELDS_PLUGIN_INTERVAL ) : 5,
		);

		/**
		 * Filter plugin settings.
		 *
		 * @param array $settings Plugin settings.
		 *
		 * @return array
		 */
		$settings = apply_filters( self::$prefix . 'settings', $settings );

		return $settings;
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

			// Load translations.
			load_plugin_textdomain( 'swa-semrush-custom-fields', false, dirname( plugin_basename( __FILE__ ) ) . '/../languages/' );

			// Throw an error in the WordPress admin console.
			die(
				sprintf(
					/* translators: 1 current plugin name, 2 plugin dependency name */'<p style="color: #444; font-size: 13px; line-height: 1.4em;font-family: -apple-system, BlinkMacSystemFont, \'Segoe UI\', Roboto, Oxygen-Sans, Ubuntu, Cantarell, \'Helvetica Neue\', sans-serif;">' . __( 'The %1$s plugin requires %2$s plugin to be active.', 'swa-semrush-custom-fields' ) . '</p>',
					'<i>SEO Writing Assistant SEMrush  Custom Fields</i>',
					'<a target="_blank" href="' . esc_url( 'https://wordpress.org/plugins/semrush-seo-writing-assistant/' ) . '">SEMrush SEO Writing Assistant</a>'
				)
			); // WPCS: XSS ok.
		}
	}
}
