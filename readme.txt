=== Custom Category Product Order for WooCommerce ===
Contributors: Nexir Marketing
Tags: woocommerce, products, sort, category, order, reorder, product order, category sort
Requires at least: 5.2
Tested up to: 6.5
Stable tag: 1.0.0
Requires PHP: 7.2
WC requires at least: 3.0
WC tested up to: 8.8
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Allows store managers to define a custom product sort order for each individual WooCommerce product category using a simple drag-and-drop interface.

== Description ==

By default, WooCommerce sorts products using a standard menu order or alphabetically. This plugin extends that functionality by allowing you to set a specific, persistent order for products within each category.

Simply navigate to **WooCommerce > Category Product Order** in your WordPress admin area. Select a product category from the dropdown menu, and you'll see a list of products currently assigned to that category. Drag and drop the products into your desired order, and click "Save Order".

This custom order will then be reflected on the frontend category archive pages, overriding the default WooCommerce sorting for that specific category.

**Features:**

*   Simple drag-and-drop interface using jQuery UI Sortable.
*   Saves custom order per product category using term metadata.
*   Automatically applies the custom order on frontend category pages.
*   Handles newly added products (appends them to the end of the custom order until manually sorted).
*   Checks if WooCommerce is active before running.
*   Uses AJAX for smooth loading and saving without page reloads.

== Installation ==

1.  Download the plugin ZIP file.
2.  Log in to your WordPress admin area and navigate to **Plugins > Add New**.
3.  Click **Upload Plugin** and choose the downloaded ZIP file.
4.  Click **Install Now** and then **Activate Plugin**.

Alternatively:

1.  Unzip the downloaded file.
2.  Upload the `wp-sort-product-categories` folder to the `/wp-content/plugins/` directory on your server.
3.  Navigate to the **Plugins** screen in your WordPress admin area.
4.  Find "Custom Category Product Order for WooCommerce" and click **Activate**.

Once activated, you can access the settings page via **WooCommerce > Category Product Order**.

== Frequently Asked Questions ==

= Does this work with product variations? =

This plugin sorts the main variable product, not the individual variations. The display order of variations within a product is controlled by the product data settings.

= What happens if I add a new product to a category? =

New products added to a category after you've saved a custom order will initially appear at the end of the list on the frontend. You'll need to go back to the Category Product Order page, select that category, and drag the new product into its desired position, then save again.

= Does this affect the default shop page order? =

No, this plugin only affects the order of products on individual product category archive pages where a custom order has been saved.

= Can I sort products that are in multiple categories? =

Yes, but the order is specific *to the category being viewed*. A product can have different positions in the custom order of different categories it belongs to.

== Changelog ==

= 1.0.0 (YYYY-MM-DD) =
* Initial release.
* Added admin interface for drag-and-drop sorting.
* Implemented frontend query modification to apply custom order.
* Included AJAX loading and saving.
* Basic styling for the admin interface.

== Upgrade Notice ==

= 1.0.0 =
Initial release. 