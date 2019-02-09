<?php
/**
 * SEMrush SEO Writing Assistant Custom Fields plugin for WordPress
 *
 * @package semrush-swa-custom-fields
 *
 * Plugin Name: SEMrush SEO Writing Assistant Custom Fields
 * Plugin URI:  https://github.com/enrico-sorcinelli/semrush-seo-writing-assistant-custom-fields
 * Description: A WordPress plugin that allows SEMrush SEO Writing Assistant working with custom fields
 * Author:      Enrico Sorcinelli
 * Author URI:  https://github.com/enrico-sorcinelli/semrush-seo-writing-assistant-custom-fields/graphs/contributors
 * Text Domain: semrush-swa-custom-fields
 * Domain Path: /languages/
 * Version:     1.0.1
 * License:     GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

// Check running WordPress instance.
if ( ! defined( 'ABSPATH' ) ) {
	header( 'HTTP/1.1 404 Not Found' );
	exit();
}

if ( ! class_exists( 'SEMrush_SEO_Writing_Assistant_Custom_Fields' ) ) {

	// Plugins constants.
	define( 'SEMRUSH_SWA_CUSTOM_FIELDS_PLUGIN_VERSION', '1.0.1' );
	define( 'SEMRUSH_SWA_CUSTOM_FIELDS_PLUGIN_BASEDIR', dirname( __FILE__ ) );
	define( 'SEMRUSH_SWA_CUSTOM_FIELDS_PLUGIN_BASEURL', plugin_dir_url( __FILE__ ) );

	// Enable debug prints on error_log (only when WP_DEBUG is true).
	if ( ! defined( 'SEMRUSH_SWA_CUSTOM_FIELDS_PLUGIN_DEBUG' ) ) {
		define( 'SEMRUSH_SWA_CUSTOM_FIELDS_PLUGIN_DEBUG', false );
	}

	require_once( SEMRUSH_SWA_CUSTOM_FIELDS_PLUGIN_BASEDIR . '/php/class-semrush-seo-writing-assistant-custom-fields.php' );

	/**
	 * Init the plugin.
	 *
	 * Define SEMRUSH_SWA_CUSTOM_FIELDS_PLUGIN_DEBUG to false in your <i>wp-config.php</i> to disable.
	 *
	 * @return void
	 */
	function semrush_swa_custom_fields_plugin_init() {

		if ( defined( 'SEMRUSH_SWA_CUSTOM_FIELDS_PLUGIN_AUTOENABLE' ) && SEMRUSH_SWA_CUSTOM_FIELDS_PLUGIN_AUTOENABLE === false ) {
			return;
		}

		// Instantiate our plugin class and add it to the set of globals.
		$GLOBALS['semrush_swa_custom_fields_plugin'] = SEMrush_SEO_Writing_Assistant_Custom_Fields::get_instance( array( 'debug' => SEMRUSH_SWA_CUSTOM_FIELDS_PLUGIN_DEBUG && WP_DEBUG ) );
	}

	// Activate the plugin once all plugin have been loaded.
	add_action( 'plugins_loaded', 'semrush_swa_custom_fields_plugin_init' );

	// Activation hook.
	register_activation_hook( __FILE__, array( 'SEMrush_SEO_Writing_Assistant_Custom_Fields', 'plugin_activation' ) );
}
