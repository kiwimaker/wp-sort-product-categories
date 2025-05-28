<?php
/**
 * Plugin Name:       Custom Category Product Order for WooCommerce
 * Plugin URI:        https://nexir.es/
 * Description:       Allows sorting products differently for each WooCommerce product category.
 * Version:           1.0.1
 * Author:            Nexir Marketing
 * Author URI:        https://nexir.es/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       ccpo
 * Domain Path:       /languages
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * WC requires at least: 3.0
 * WC tested up to: 8.0 
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Check if WooCommerce is active before proceeding.
 * If not, display an admin notice and prevent the rest of the plugin from loading.
 */
function ccpo_check_woocommerce_active() {
    if ( ! class_exists( 'WooCommerce' ) ) {
        // Display an admin notice if WooCommerce is not active
        add_action( 'admin_notices', 'ccpo_woocommerce_inactive_notice' );
        // We return true here to indicate the check failed, so the caller knows to stop.
        return true; 
    }
    // WooCommerce is active, return false (check passed)
    return false; 
}

/**
 * Admin notice displayed when WooCommerce is not active.
 */
function ccpo_woocommerce_inactive_notice() {
    ?>
    <div class="notice notice-error is-dismissible">
        <p><?php esc_html_e( 'Custom Category Product Order requires WooCommerce to be activated.', 'ccpo' ); ?></p>
    </div>
    <?php
}

// Run the check. If it returns true (WooCommerce is inactive), stop loading the plugin.
if ( ccpo_check_woocommerce_active() ) {
    return; 
}

/**
 * Declare compatibility with WooCommerce High-Performance Order Storage (HPOS)
 */
add_action( 'before_woocommerce_init', function() {
	if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
		\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
	}
} );

/**
 * Define Constants
 */
define( 'CCPO_VERSION', '1.0.1' );
// Use untrailingslashit() for better compatibility with path joining.
define( 'CCPO_PLUGIN_DIR', untrailingslashit( plugin_dir_path( __FILE__ ) ) );
define( 'CCPO_PLUGIN_URL', untrailingslashit( plugin_dir_url( __FILE__ ) ) );
define( 'CCPO_PLUGIN_BASENAME', plugin_basename( __FILE__ ) ); // Useful for hooks like plugin_action_links

/**
 * Load the main plugin class responsible for setting up actions and filters.
 */
require_once CCPO_PLUGIN_DIR . '/includes/class-ccpo-main.php';

/**
 * Begins execution of the plugin.
 *
 * Instantiates the main plugin class and calls its run method
 * to register all hooks.
 *
 * @since    1.0.0
 */
function run_ccpo_plugin() {
	$plugin = new CCPO_Main();
	$plugin->run();
}

// Hook the plugin execution to the 'plugins_loaded' action hook.
// This ensures that all plugins, including WooCommerce, are loaded before our plugin runs.
add_action( 'plugins_loaded', 'run_ccpo_plugin' ); 