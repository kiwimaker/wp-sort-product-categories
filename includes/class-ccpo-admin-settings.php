<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and hooks for enqueueing
 * the admin-specific stylesheet and JavaScript, adding the admin menu,
 * and handling AJAX requests for the sorting interface.
 *
 * @package    CCPO
 * @subpackage CCPO/admin
 * @author     Nexir Marketing <info@nexir.es>
 */
class CCPO_Admin_Settings {

	/**
	 * The ID of this plugin (text domain).
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param    string    $plugin_name       The name of this plugin.
	 * @param    string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 * @param string $hook_suffix The current admin page.
	 */
	public function enqueue_styles( $hook_suffix ) {

		// Get the current screen object
		$screen = get_current_screen(); // Restore screen check

		// Define our specific admin page hook
		$plugin_admin_page_hook = 'woocommerce_page_custom-category-order'; // Restore screen check

		// Only load styles on our plugin's admin page -- RESTORED CONDITION
		if ( isset($screen, $screen->id) && $screen->id === $plugin_admin_page_hook ) {
			// Enqueue jQuery UI styles (dependency)
			wp_enqueue_style('jquery-ui-sortable');

			// Enqueue our custom admin styles
			wp_enqueue_style(
				'ccpo-admin-styles', // Use a more specific handle
				CCPO_PLUGIN_URL . '/includes/assets/css/admin-style.css', // Use the constant directly again
				array(), // Remove dependency again as a test
				$this->version, // Version: '1.0.0'
				'all' // Media
			);
		}
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 * @param string $hook_suffix The current admin page.
	 */
	public function enqueue_scripts( $hook_suffix ) {
		// Get the current screen object
		$screen = get_current_screen(); // Restore screen check

		// Define our specific admin page hook
		$plugin_admin_page_hook = 'woocommerce_page_custom-category-order'; // Restore screen check

		// Only load scripts on our plugin's admin page -- RESTORED CONDITION
		if ( isset($screen, $screen->id) && $screen->id === $plugin_admin_page_hook ) {
			// Enqueue jQuery UI Sortable script
			wp_enqueue_script('jquery-ui-sortable');

			// Enqueue our custom admin script
			wp_enqueue_script(
				$this->plugin_name . '-admin',
				CCPO_PLUGIN_URL . '/includes/assets/js/admin-sort.js',
				array( 'jquery', 'jquery-ui-sortable' ), // Dependencies
				$this->version,
				true // Load in footer
			);

			// Localize script to pass PHP variables (like AJAX URL and nonce) to JavaScript
			wp_localize_script(
				$this->plugin_name . '-admin',
				'ccpo_admin_params', // Object name to access in JS (e.g., ccpo_admin_params.ajax_url)
				array(
					'ajax_url' => admin_url( 'admin-ajax.php' ),
					'nonce'    => wp_create_nonce( 'ccpo_admin_ajax_nonce' ), // Nonce for loading products
					'save_nonce' => wp_create_nonce( 'ccpo_save_order_nonce' ), // Nonce specifically for saving
                    'i18n'     => array( // Internationalization strings for JS
                        'loading' => esc_html__( 'Loading products...', 'ccpo' ),
                        'select_category' => esc_html__( 'Select a category to see its products.', 'ccpo' ),
                        'no_products' => esc_html__( 'No products found in this category.', 'ccpo' ),
                        'error_loading' => esc_html__( 'Error loading products:', 'ccpo' ),
                        'ajax_error' => esc_html__( 'AJAX Error:', 'ccpo' ),
                        'select_category_alert' => esc_html__( 'Please select a category first.', 'ccpo' ),
                        'error_saving' => esc_html__( 'Error saving order:', 'ccpo' ),
                        'order_saved' => esc_html__( 'Product order saved successfully.', 'ccpo' ),
                        'dismiss_notice' => esc_html__( 'Dismiss this notice.', 'ccpo' ),
                        'drag_instructions' => esc_html__( 'Drag and drop the products to change their order for this category.', 'ccpo' ),
                    )
				)
			);
		}
	}

	/**
	 * Add the submenu page under the WooCommerce menu.
	 *
	 * @since    1.0.0
	 */
	public function add_plugin_admin_menu() {
		add_submenu_page(
			'woocommerce', // Parent slug (WooCommerce menu)
			__( 'Custom Category Product Order', 'ccpo' ), // Page title (appears in browser tab)
			__( 'Category Product Order', 'ccpo' ), // Menu title (appears in sidebar)
			'manage_woocommerce', // Capability required to access
			'custom-category-order', // Menu slug (unique identifier for the page)
			array( $this, 'display_plugin_admin_page' ) // Callback function to render the page content
		);
	}

	/**
	 * Render the admin page content.
	 *
	 * @since    1.0.0
	 */
	public function display_plugin_admin_page() {
		// Check user capabilities
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'ccpo' ) );
		}

		?>
		<div class="wrap ccpo-admin-wrap">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

			<form id="ccpo-form" method="post" action="">
				<?php
				// Nonce field for security, specifically for the saving action if needed (handled by AJAX nonce now)
				// wp_nonce_field( 'ccpo_save_order_action', 'ccpo_save_order_nonce' );
				?>

				<table class="form-table ccpo-form-table">
					<tbody>
						<tr>
							<th scope="row">
								<label for="ccpo_category_select"><?php esc_html_e( 'Select Product Category', 'ccpo' ); ?></label>
							</th>
							<td>
								<?php
								// Get product categories
								$categories = get_terms( array(
									'taxonomy'   => 'product_cat',
									'hide_empty' => false, // Show categories even if they have no products
									'orderby'    => 'name',
									'order'      => 'ASC',
								) );

								if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) :
									?>
									<select id="ccpo_category_select" name="ccpo_category_select" class="ccpo-category-select">
										<option value=""><?php esc_html_e( '-- Select a Category --', 'ccpo' ); ?></option>
										<?php foreach ( $categories as $category ) : ?>
											<option value="<?php echo esc_attr( $category->term_id ); ?>">
												<?php echo esc_html( $category->name ); ?> (<?php echo esc_html( $category->count ); ?> <?php esc_html_e('products', 'ccpo'); ?>)
											</option>
										<?php endforeach; ?>
									</select>
									<p class="description"><?php esc_html_e('Choose a category to manage the product order within it.', 'ccpo'); ?></p>
								<?php else : ?>
									<p><?php esc_html_e( 'No product categories found. Please add some categories first.', 'ccpo' ); ?></p>
								<?php endif; ?>
							</td>
						</tr>
					</tbody>
				</table>

				<div id="ccpo-product-list-container" class="ccpo-product-list-container">
					<p class="description initial-message"><?php esc_html_e('Select a category above to load and sort its products.', 'ccpo'); ?></p>
					<div id="ccpo-sortable-products-wrapper"> 
						<!-- Products list (ul) will be loaded here via AJAX -->
					</div>
				</div>

				 <p class="submit ccpo-submit-area" style="display: none;"> <!-- Initially hidden, shown via JS -->
					 <button type="button" id="ccpo-save-order" class="button button-primary ccpo-save-button">
						 <?php esc_html_e( 'Save Order', 'ccpo' ); ?>
					 </button>
					 <span class="spinner ccpo-spinner"></span>
				 </p>

			</form>
			<div id="ccpo-admin-feedback" class="ccpo-admin-feedback"></div> <!-- Area for success/error messages -->
		</div><!-- .wrap -->
		<?php
	}

	/**
	 * AJAX handler to load products for a selected category.
	 * Outputs JSON containing the HTML for the sortable list.
	 *
	 * @since 1.0.0
	 */
	public function ajax_load_category_products() {
		// Verify nonce
		check_ajax_referer( 'ccpo_admin_ajax_nonce', 'nonce' );

		// Verify user capabilities
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			wp_send_json_error( array( 'message' => __( 'Permission denied.', 'ccpo' ) ), 403 );
		}

		// Get and sanitize category ID from POST request
		$category_id = isset( $_POST['category_id'] ) ? intval( $_POST['category_id'] ) : 0;

		if ( ! $category_id ) {
			wp_send_json_error( array( 'message' => __( 'Invalid category selected.', 'ccpo' ) ), 400 );
		}

		// --- Query Logic --- 

		// 1. Get the currently saved custom order for this category (if it exists)
		$custom_order_ids = get_term_meta( $category_id, '_custom_product_order', true );
		if ( ! is_array( $custom_order_ids ) ) {
			$custom_order_ids = array();
		}
		$custom_order_ids = array_map('intval', $custom_order_ids); // Ensure integer IDs
		$custom_order_ids = array_filter($custom_order_ids); // Remove zeros

		// 2. Get *all* product IDs currently assigned *directly* to this category
		$args_all_products = array(
			'post_type'      => 'product',
			'posts_per_page' => -1, // Get all of them
			'post_status'    => 'publish', // Only published products
			'tax_query'      => array(
				array(
					'taxonomy' => 'product_cat',
					'field'    => 'term_id',
					'terms'    => $category_id,
					'include_children' => false // IMPORTANT: Only products *directly* in this category
				),
			),
			'fields' => 'ids', // We only need the IDs
            'orderby' => 'menu_order title', // Default sort for getting all products initially
            'order' => 'ASC'
		);
		$all_product_ids_in_category = get_posts( $args_all_products );
		
		// 3. Determine the final ordered list of IDs
        $final_ordered_ids = array();
        if ( ! empty( $custom_order_ids ) ) {
            // Start with the custom order
            $final_ordered_ids = $custom_order_ids;

            // Find products in the category that are NOT in the saved custom order (newly added)
            $new_products = array_diff( $all_product_ids_in_category, $custom_order_ids );
            
            // Add new products to the end of the list
            $final_ordered_ids = array_merge( $final_ordered_ids, $new_products );

            // Filter out any IDs from the saved order that are no longer in the category
            $final_ordered_ids = array_intersect( $final_ordered_ids, $all_product_ids_in_category );

        } else {
            // No custom order saved yet, use the default order
            $final_ordered_ids = $all_product_ids_in_category;
        }

		// --- HTML Generation --- 
		ob_start(); // Start output buffering

		if ( ! empty( $final_ordered_ids ) ) {
			// Output the sortable list
			?>
            <p class="description sort-instructions"><?php esc_html_e( 'Drag and drop the products to change their order for this category.', 'ccpo' ); ?></p>
			<ul id="ccpo-sortable-products" class="ccpo-sortable-list connectedSortable">
				<?php
				foreach ( $final_ordered_ids as $product_id ) {
					$product = wc_get_product( $product_id ); // Get product object
					if ( $product ) {
					    $post_status = get_post_status($product_id);
                        $publish_date = get_the_date( 'd/m/Y', $product_id ); // Format date as dd/mm/yyyy
                        // $stock_status = $product->get_stock_status(); // Removed stock
                        // $stock_status_text = wc_get_stock_html( $product ); // Removed stock
                        $view_url = get_permalink( $product_id );
                        $edit_url = get_edit_post_link( $product_id );
                        
						?>
						<li class="ui-state-default ccpo-product-item status-<?php echo esc_attr($post_status); ?>" data-id="<?php echo esc_attr( $product_id ); ?>">
							<span class="dashicons dashicons-menu handle ccpo-handle"></span>
                            <div class="ccpo-product-image-container">
                                <?php echo $product->get_image('thumbnail', array('class' => 'ccpo-product-image')); ?>
                            </div>
                            <div class="ccpo-product-details">
							    <span class="ccpo-product-title"><?php echo esc_html( $product->get_title() ); ?></span>
							    <span class="ccpo-product-id">(ID: <?php echo esc_html( $product_id ); ?>)</span>
                                <div class="ccpo-product-meta">
                                    <span class="ccpo-meta-item ccpo-status">Status: <strong class="status-<?php echo esc_attr($post_status); ?>"><?php echo esc_html(ucfirst($post_status)); ?></strong></span>
                                    <span class="ccpo-meta-item ccpo-date">Published: <?php echo esc_html($publish_date); ?></span>
                                    <?php // <span class="ccpo-meta-item ccpo-stock">Stock: ...</span> // Removed stock display ?>
                                </div>
                            </div>
                            <div class="ccpo-product-actions">
                                <div class="ccpo-product-price">
                                    <?php echo $product->get_price_html(); ?>
                                </div>
                                <div class="ccpo-action-links">
                                    <?php if ($view_url): ?>
                                        <a href="<?php echo esc_url($view_url); ?>" target="_blank" class="button button-small ccpo-view-link">View</a>
                                    <?php endif; ?>
                                     <?php if ($edit_url): ?>
                                        <a href="<?php echo esc_url($edit_url); ?>" target="_blank" class="button button-small ccpo-edit-link">Edit</a>
                                    <?php endif; ?>
                                </div>
                            </div>
						</li>
						<?php
					}
				}
				?>
			</ul>
			<?php
		} else {
			// No products found in this category
			?>
			<p class="description no-products-message"><?php esc_html_e( 'No products found in this category.', 'ccpo' ); ?></p>
			<?php
		}

		$html = ob_get_clean(); // Get buffered output

		// Send JSON response back to JavaScript
		wp_send_json_success( array(
            'html' => $html,
            'count' => count($final_ordered_ids) // Send product count for JS logic
        ) );
	}

	/**
	 * AJAX handler to save the custom product order for a category.
	 * Expects category_id and an array of product_ids in POST.
	 *
	 * @since 1.0.0
	 */
	public function ajax_save_category_order() {
		// Verify nonce
		check_ajax_referer( 'ccpo_save_order_nonce', 'security' ); // Match nonce key used in wp_localize_script

		// Verify user capabilities
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			wp_send_json_error( array( 'message' => __( 'Permission denied.', 'ccpo' ) ), 403 );
		}

		// Get and sanitize category ID
		$category_id = isset( $_POST['category_id'] ) ? intval( $_POST['category_id'] ) : 0;

		// Get product IDs array - expecting it from the sortable list
		$product_ids = isset( $_POST['product_ids'] ) && is_array( $_POST['product_ids'] ) ? $_POST['product_ids'] : array();

		if ( ! $category_id ) {
			wp_send_json_error( array( 'message' => __( 'Invalid category ID provided.', 'ccpo' ) ), 400 );
		}

		// Sanitize the received product IDs to ensure they are integers
		$sanitized_product_ids = array_map( 'intval', $product_ids );
		$sanitized_product_ids = array_filter( $sanitized_product_ids ); // Remove any zeros or invalid entries

		// Save the sanitized order array as term meta
		// Use update_term_meta - it handles both adding and updating the meta value.
		$result = update_term_meta( $category_id, '_custom_product_order', $sanitized_product_ids );

		if ( $result === false ) {
			// update_term_meta returns false on failure
			wp_send_json_error( array( 'message' => __( 'Database error: Failed to save the product order. Please try again.', 'ccpo' ) ), 500 );
		} else {
			// update_term_meta returns true if the value was updated, or meta_id if added.
            // Either way, it was successful.
			wp_send_json_success( array( 'message' => __( 'Product order saved successfully.', 'ccpo' ) ) );
		}
	}

} // End class 