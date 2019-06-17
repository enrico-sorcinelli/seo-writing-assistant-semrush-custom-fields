<?php
/**
 * SEMrush SEO Writing Assistant Custom Fields plugin for WordPress
 *
 * @package swa-semrush-custom-fields
 *
 * Plugin Name: SEO Writing Assistant SEMrush Custom Fields
 * Plugin URI:  https://github.com/enrico-sorcinelli/seo-writing-assistant-semrush-custom-fields
 * Description: A WordPress plugin that allows SEMrush SEO Writing Assistant working with custom fields
 * Author:      Enrico Sorcinelli
 * Author URI:  https://github.com/enrico-sorcinelli/seo-writing-assistant-semrush-custom-fields/graphs/contributors
 * Text Domain: swa-semrush-custom-fields
 * Domain Path: /languages/
 * Version:     1.1.0
 * License:     GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

// Check running WordPress instance.
if ( ! defined( 'ABSPATH' ) ) {
	header( 'HTTP/1.1 404 Not Found' );
	exit();
}

if ( ! class_exists( 'SEO_Writing_Assistant_SEMrush_Custom_Fields' ) ) {

	// Plugins constants.
	define( 'SWA_SEMRUSH_CUSTOM_FIELDS_PLUGIN_VERSION', '1.1.0' );
	define( 'SWA_SEMRUSH_CUSTOM_FIELDS_PLUGIN_BASEDIR', dirname( __FILE__ ) );
	define( 'SWA_SEMRUSH_CUSTOM_FIELDS_PLUGIN_BASEURL', plugin_dir_url( __FILE__ ) );

	// Enable debug prints on error_log (only when WP_DEBUG is true).
	if ( ! defined( 'SWA_SEMRUSH_CUSTOM_FIELDS_PLUGIN_DEBUG' ) ) {
		define( 'SWA_SEMRUSH_CUSTOM_FIELDS_PLUGIN_DEBUG', false );
	}

	require_once( SWA_SEMRUSH_CUSTOM_FIELDS_PLUGIN_BASEDIR . '/php/class-seo-writing-assistant-semrush-custom-fields.php' );

	/**
	 * Init the plugin.
	 */
	function swa_semrush_custom_fields_plugin_init() {

		// Instantiate our plugin class and add it to the set of globals.
		$GLOBALS['swa_semrush_custom_fields_plugin'] = SEO_Writing_Assistant_SEMrush_Custom_Fields::get_instance( array( 'debug' => SWA_SEMRUSH_CUSTOM_FIELDS_PLUGIN_DEBUG && WP_DEBUG ) );
	}

	// Activate the plugin once all plugin have been loaded.
	add_action( 'plugins_loaded', 'swa_semrush_custom_fields_plugin_init' );

	// Activation hook.
	register_activation_hook( __FILE__, array( 'SEO_Writing_Assistant_SEMrush_Custom_Fields', 'plugin_activation' ) );
}
