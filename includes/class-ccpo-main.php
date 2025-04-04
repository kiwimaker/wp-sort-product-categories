<?php

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    CCPO
 * @subpackage CCPO/includes
 * @author     Nexir Marketing <info@nexir.es>
 */
class CCPO_Main {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      CCPO_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin (text domain).
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Instance of the admin settings class.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      CCPO_Admin_Settings $admin_settings Handles admin area functionality.
	 */
	protected $admin_settings;

	/**
	 * Instance of the frontend sorting class.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      CCPO_Frontend_Sort $frontend_sort Handles frontend query modifications.
	 */
	protected $frontend_sort;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'CCPO_VERSION' ) ) {
			$this->version = CCPO_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'ccpo';

		$this->load_dependencies();
		// $this->set_locale(); // Uncomment if you add internationalization
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - CCPO_Loader. Orchestrates the hooks of the plugin.
	 * - CCPO_i18n. Defines internationalization functionality. (Optional)
	 * - CCPO_Admin_Settings. Defines all hooks for the admin area.
	 * - CCPO_Frontend_Sort. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {
		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once CCPO_PLUGIN_DIR . '/includes/class-ccpo-loader.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once CCPO_PLUGIN_DIR . '/includes/class-ccpo-admin-settings.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once CCPO_PLUGIN_DIR . '/includes/class-ccpo-frontend-sort.php';

		// Optional: Load internationalization class
		// require_once CCPO_PLUGIN_DIR . '/includes/class-ccpo-i18n.php';

		$this->loader = new CCPO_Loader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the CCPO_i18n class in order to set the domain and to register the load_plugin_textdomain
	 * hook with WordPress. (Optional)
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	/* // Uncomment if using i18n
	private function set_locale() {
		$plugin_i18n = new CCPO_i18n();
		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
	}
	*/

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {
		$this->admin_settings = new CCPO_Admin_Settings( $this->get_plugin_name(), $this->get_version() );

		// Add admin menu page
		$this->loader->add_action( 'admin_menu', $this->admin_settings, 'add_plugin_admin_menu' );

		// Enqueue admin scripts and styles
		$this->loader->add_action( 'admin_enqueue_scripts', $this->admin_settings, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $this->admin_settings, 'enqueue_scripts' );

		// Register AJAX handlers
		$this->loader->add_action( 'wp_ajax_ccpo_load_category_products', $this->admin_settings, 'ajax_load_category_products' );
		$this->loader->add_action( 'wp_ajax_ccpo_save_category_order', $this->admin_settings, 'ajax_save_category_order' );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {
		$this->frontend_sort = new CCPO_Frontend_Sort( $this->get_plugin_name(), $this->get_version() );

		// Hook into pre_get_posts to modify the main query on category archives
		$this->loader->add_action( 'pre_get_posts', $this->frontend_sort, 'custom_category_product_order' );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization constraints.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    CCPO_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

} // End class 