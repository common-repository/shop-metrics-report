<?php
/**
 * Plugin Name: Shop Metrics Report for WP
 * Plugin URI: https://shopmetrics.report/shop-metrics-wordpress-plugin#utm_source=WordPress_Plugin_Overview&utm_medium=link&utm_campaign=plugin_link
 * Description: The Shop Metrics Report plugin for WP helps you with integrating your webshop(s) to our API. Generate the best charts and analytics from your order data. This plugin is compatible with WooCommerce (More plugins will be added soon!).
 * Version: 1.0.1
 * Author: Van Wilderen ICT
 * Author URI: https://shopmetrics.report/#utm_source=WordPress_Plugin_Overview&utm_medium=link&utm_campaign=plugin_link
 * License: GPL2
 */

if ( ! defined( 'SMR_FILE' ) ) {
	define( 'SMR_FILE', __FILE__ );
}

if ( ! class_exists( 'Shop_Metrics_for_WP' ) ) {

	define( "SMR_PATH", plugin_dir_path( __FILE__ ) );

	add_action( 'plugins_loaded', 'smr_load_files' );
}

function smr_load_files() {
	require_once( SMR_PATH . 'lib/class-init.php' );
	$smr = new Shop_Metrics_Report_Init();

	add_action( 'init', array( $smr, 'load_smr_plugins' ) );

	if ( is_admin() ) {
		require_once SMR_PATH . 'lib/class-admin.php';
	}
}

/**
 * Load translation files
 */
function sm_for_wp_load_translation_files() {
	load_plugin_textdomain( 'shopmetrics-for-wp', false, dirname( plugin_basename( __FILE__ ) ) . '/translations/' );
}

//add action to load my plugin files
add_action( 'plugins_loaded', 'sm_for_wp_load_translation_files' );