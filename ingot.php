<?php
/**
Plugin Name: Ingot
Version: 1.1.0
Plugin URI:  http://IngotHQ.com
Description: Conversion optimization made easy for WordPress
Author:       Ingot LLC
Author URI:  http://IngotHQ.com
Text Domain: ingot
Domain Path: /languages
 */

define( 'INGOT_VER', '1.1.0' );
define( 'INGOT_URL', plugin_dir_url( __FILE__ ) );
define( 'INGOT_DIR', dirname( __FILE__ ) );
define( 'INGOT_UI_PARTIALS_DIR', dirname( __FILE__ ) . '/classes/ui/admin/partials/' );
define( 'INGOT_ROOT', basename( dirname( __FILE__ ) ) );


/**
 * Actions to boot up plugin
 */
add_action( 'plugins_loaded', 'ingot_maybe_load', 0 );
add_action( 'ingot_loaded',  'ingot_edd_sl_init', 1 );
/**
 * Load plugin if possible
 *
 * @since 0.0.0
 */
function ingot_maybe_load() {
	$fail = false;
	if ( ! version_compare( PHP_VERSION, '5.5.0', '>=' ) ) {
		$fail = true;
		if ( is_admin() ) {
			include_once( dirname( __FILE__ ) . '/vendor/calderawp/dismissible-notice/src/functions.php' );
			$message = esc_html__( sprintf( 'Ingot requires PHP version 5.5.0 or later. Current version is %s.', PHP_VERSION ), 'ingot' );
                        
                        if(function_exists('caldera_warnings_dismissible_notice') )
                            echo caldera_warnings_dismissible_notice( $message, true, 'activate_plugins' );
		}

	}
	global $wp_version;
	if ( ! version_compare( $wp_version, '4.4', '>=' ) ) {
		$fail = true;
		if ( is_admin() ) {
			include_once( dirname( __FILE__ ) . '/vendor/calderawp/dismissible-notice/src/functions.php' );
			$message = esc_html__( sprintf( 'Ingot requires WordPress version 4.4 or later. Current version is %s.', $wp_version ), 'ingot' );
			echo caldera_warnings_dismissible_notice( $message, true, 'activate_plugins' );
		}

	}
	if( false == $fail ){
		include_once( dirname(__FILE__ ) . '/ingot_bootstrap.php' );
		add_action( 'plugins_loaded', array( 'ingot_bootstrap', 'maybe_load' ) );
	}

}
/**
 * EDD Licensing
 *
 * @since 0.3.0
 */
function ingot_edd_sl_init(){
	define( 'INGOT_SL_STORE_URL', 'http://ingothq.com' );
	define( 'INGOT_SL_ITEM_NAME', 'Ingot Plugin: The Automatic A/B Tester' );
	add_action( 'admin_init', 'ingot_sl_plugin_updater', 0 );
	add_action( 'admin_init', 'ingot_sl_register_option' );
	if ( is_admin() && ! class_exists( 'EDD_SL_Plugin_Updater' ) ) {
		include( dirname( __FILE__ ) . '/includes/EDD_SL_Plugin_Updater.php' );
	}
}


/**
 * Load translations
 *
 * @since 1.1.0
 */
add_action( 'plugins_loaded', 'ingot_load_textdomain' );

/**
 * Load plugin textdomain.
 *
 * @since 1.1.0
 */
function ingot_load_textdomain() {
	load_plugin_textdomain( 'ingot', false, plugin_basename( dirname( __FILE__ ) ) . '/languages' );
}

