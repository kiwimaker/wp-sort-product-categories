<?php

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and hooks for modifying the main query
 * on product category archives to apply the custom sort order.
 *
 * @package    CCPO
 * @subpackage CCPO/public
 * @author     Nexir Marketing <info@nexir.es>
 */
class CCPO_Frontend_Sort {

	/**
	 * The ID of this plugin.
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
	 * @param    string    $plugin_name       The name of the plugin.
	 * @param    string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Modify the main WordPress query for product category archives.
	 *
	 * This function hooks into 'pre_get_posts' and checks if the current query
	 * is for a product category archive page on the frontend. If it is,
	 * it retrieves the custom product order stored in term meta and applies
	 * it to the query using 'post__in' and 'orderby' => 'post__in'.
	 *
	 * @since    1.0.0
	 * @param    WP_Query $query The WP_Query instance (passed by reference).
	 */
	public function custom_category_product_order( $query ) {

		// Check conditions: Not in admin, is the main query, and is a product category archive page.
		if ( ! is_admin() && $query->is_main_query() && $query->is_tax( 'product_cat' ) ) {

			// Get the queried term object (the current product category)
			$category = $query->get_queried_object();

			// Ensure we have a valid WP_Term object
			if ( $category instanceof WP_Term ) {
				$category_id = $category->term_id;

				// Get the custom order array from term meta
				$custom_order = get_term_meta( $category_id, '_custom_product_order', true );

				// Check if a custom order exists, is an array, and is not empty
				if ( ! empty( $custom_order ) && is_array( $custom_order ) ) {

					// Ensure all IDs in the array are integers
					$ordered_ids = array_map( 'intval', $custom_order );
					$ordered_ids = array_filter( $ordered_ids ); // Remove potential zeros or invalid entries

					// If we still have a valid list of ordered IDs after sanitization
					if ( ! empty( $ordered_ids ) ) {
						// Get all products in this category that are not in the custom order
						$args = array(
							'post_type'      => 'product',
							'posts_per_page' => -1,
							'post_status'    => 'publish',
							'fields'         => 'ids',
							'tax_query'      => array(
								array(
									'taxonomy'         => 'product_cat',
									'field'            => 'term_id',
									'terms'            => $category_id,
									'include_children' => false,
								),
							),
							'post__not_in'   => $ordered_ids,
							'orderby'        => 'menu_order title',
							'order'          => 'ASC',
						);
						$remaining_products = get_posts( $args );

						// Merge the custom ordered products with remaining products
						$all_products = array_merge( $ordered_ids, $remaining_products );

						// Set the orderby parameter to 'post__in'
						$query->set( 'orderby', 'post__in' );

						// Pass the array of ordered post IDs to 'post__in'
						$query->set( 'post__in', $all_products );

						// It might be necessary to explicitly remove other ordering parameters
						// depending on the theme or other plugins.
						// $query->set( 'order', '' ); // Remove ASC/DESC as order is defined by post__in
                        
                        // Important: Also set posts_per_page to -1 IF you want ALL products 
                        // in the custom order to show, overriding the default posts per page setting.
                        // If you want pagination based on the custom order, leave posts_per_page as default.
                        // Example: $query->set( 'posts_per_page', -1 );
					}
				} // End check for custom order existence
			} // End check for valid WP_Term object
		} // End check for query conditions
	} // End custom_category_product_order method

} // End class 