# Custom Category Product Order for WooCommerce

[![WordPress Plugin Version](https://img.shields.io/badge/version-1.0.1-blue.svg)](https://github.com/kiwimaker/wp-sort-product-categories)
[![WordPress Compatibility](https://img.shields.io/badge/wordpress-5.2%2B-blue.svg)](https://wordpress.org/)
[![WooCommerce Compatibility](https://img.shields.io/badge/woocommerce-3.0%2B-purple.svg)](https://woocommerce.com/)
[![PHP Version](https://img.shields.io/badge/php-7.2%2B-blue.svg)](https://php.net/)
[![License](https://img.shields.io/badge/license-GPL%20v2%2B-green.svg)](https://www.gnu.org/licenses/gpl-2.0.html)

A WordPress plugin that allows store managers to define custom product sort orders for each individual WooCommerce product category using an intuitive drag-and-drop interface.

## 🚀 Features

- **Drag & Drop Interface**: Simple jQuery UI Sortable interface for easy product reordering
- **Category-Specific Ordering**: Set different product orders for each category
- **Persistent Storage**: Custom orders are saved using WordPress term metadata
- **Frontend Integration**: Automatically applies custom order on category archive pages
- **Smart Product Handling**: New products are automatically appended to existing custom orders
- **WooCommerce Integration**: Seamlessly integrates with WooCommerce without conflicts
- **AJAX-Powered**: Smooth loading and saving without page reloads
- **HPOS Compatible**: Fully compatible with WooCommerce High-Performance Order Storage

## 📋 Requirements

- **WordPress**: 5.2 or higher
- **WooCommerce**: 3.0 or higher (tested up to 8.8)
- **PHP**: 7.2 or higher
- **Tested up to**: WordPress 6.5

## 🔧 Installation

### Method 1: WordPress Admin (Recommended)

1. Download the plugin ZIP file
2. Log in to your WordPress admin area
3. Navigate to **Plugins > Add New**
4. Click **Upload Plugin** and select the downloaded ZIP file
5. Click **Install Now** and then **Activate Plugin**

### Method 2: Manual Installation

1. Unzip the downloaded file
2. Upload the `wp-sort-product-categories` folder to `/wp-content/plugins/`
3. Navigate to **Plugins** in your WordPress admin
4. Find "Custom Category Product Order for WooCommerce" and click **Activate**

### Post-Installation

Once activated, access the plugin via **WooCommerce > Category Product Order** in your WordPress admin.

## 🎯 Usage

1. Navigate to **WooCommerce > Category Product Order** in your admin area
2. Select a product category from the dropdown menu
3. View the list of products currently assigned to that category
4. Drag and drop products into your desired order
5. Click **Save Order** to apply changes
6. The custom order will be reflected on frontend category pages

## ❓ Frequently Asked Questions

### Does this work with product variations?
This plugin sorts the main variable product, not individual variations. Variation display order is controlled by the product data settings.

### What happens when I add a new product to a category?
New products will initially appear at the end of the list on the frontend. You'll need to return to the Category Product Order page and manually position the new product, then save again.

### Does this affect the default shop page order?
No, this plugin only affects individual product category archive pages where a custom order has been saved.

### Can I sort products that are in multiple categories?
Yes! The order is specific to each category being viewed. A product can have different positions in different categories it belongs to.

## 🛠️ Technical Details

### Plugin Structure
```
wp-sort-product-categories/
├── wp-sort-product-categories.php  # Main plugin file
├── includes/                       # Core plugin classes
├── README.md                      # This file
└── readme.txt                     # WordPress.org readme
```

### Hooks & Filters
The plugin uses WordPress and WooCommerce standard hooks to integrate seamlessly with your store.

### Data Storage
Custom product orders are stored as term metadata, ensuring data persistence and compatibility with WordPress standards.

## 📝 Changelog

### Version 1.0.1
- 📝 Updated README to modern Markdown format
- ✨ Added badges for better project visibility
- 🔧 Improved documentation structure and readability
- 🔗 Updated repository links

### Version 1.0.0
- ✨ Initial release
- ➕ Added admin interface for drag-and-drop sorting
- ➕ Implemented frontend query modification to apply custom order
- ➕ Included AJAX loading and saving functionality
- ➕ Added basic styling for the admin interface
- ➕ WooCommerce HPOS compatibility

## 🤝 Contributing

We welcome contributions! Please feel free to submit issues and pull requests.

## 📄 License

This plugin is licensed under the [GPL v2 or later](https://www.gnu.org/licenses/gpl-2.0.html).

## 👥 Credits

**Developed by**: [Nexir Marketing](https://nexir.es/)  
**Plugin URI**: [https://nexir.es/](https://nexir.es/)

---

**Tags**: woocommerce, products, sort, category, order, reorder, product order, category sort 