<?php
/**
 * Plugin Name:         DV WooCommerce Gateway
 * Plugin URI:          https://example.com/
 * Description:         Integration with DV.net payment gateway for WooCommerce.
 * Version:             1.0.1
 * Author:              Your Name
 * Author URI:          https://example.com/
 * License:             GPLv2 or later
 * License URI:         https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:         dv-woocommerce
 * Domain Path:         /languages
 */

use DvWoocommerce\DV_Gateway;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Exit early if Composer dependencies are not installed.
if ( ! file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
	add_action( 'admin_notices', function() {
		echo '<div class="error"><p>';
		echo '<strong>DV WooCommerce Gateway:</strong> Composer autoload file not found. Please run `composer install` in the plugin directory.';
		echo '</p></div>';
	});
	return;
}

require_once __DIR__ . '/vendor/autoload.php';

/**
 * Initialize the gateway.
 *
 * Hooked to 'plugins_loaded' with priority 11 to ensure it runs after WooCommerce is loaded.
 */
function dv_woocommerce_init() {
	// Check if WooCommerce is active before proceeding.
	if ( ! class_exists( 'WooCommerce' ) ) {
		return;
	}

	require_once __DIR__ . '/includes/class-dv-gateway.php';

	// Add the gateway to WooCommerce.
	add_filter( 'woocommerce_payment_gateways', function( $gateways ) {
		$gateways[] = DV_Gateway::class;
		return $gateways;
	} );
}
add_action( 'plugins_loaded', 'dv_woocommerce_init', 11 );


/**
 * Add a direct link to the settings page on the plugins screen.
 */
function dv_gateway_action_links( $links ) {
	$settings_link = '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=checkout&section=dv_gateway' ) . '">Settings</a>';
	array_unshift( $links, $settings_link );
	return $links;
}
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'dv_gateway_action_links' );

