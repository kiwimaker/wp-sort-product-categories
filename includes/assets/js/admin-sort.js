/**
 * Admin-specific JavaScript for the Custom Category Product Order plugin.
 *
 * Handles AJAX requests for loading products, initializing the jQuery UI Sortable
 * interface, and saving the new product order back to the server.
 *
 * Uses the parameters passed via wp_localize_script (object name: ccpo_admin_params).
 */

jQuery(document).ready(function($) {

    // --- Cache DOM elements --- 
    const categorySelect = $('#ccpo_category_select');
    const productListContainer = $('#ccpo-product-list-container'); // Main container
    const productListWrapper = $('#ccpo-sortable-products-wrapper'); // Inner div where the <ul> will be placed
    const saveButton = $('#ccpo-save-order');
    const submitParagraph = $('.ccpo-submit-area');
    const spinner = submitParagraph.find('.ccpo-spinner');
    const initialMessage = productListContainer.find('.initial-message');
    const feedbackArea = $('#ccpo-admin-feedback');
    const sortableListSelector = '#ccpo-sortable-products'; // Selector for the <ul>

    // --- Function to display feedback messages --- 
    function showFeedback(message, type = 'success') {
        feedbackArea.html(
            `<div id="setting-error-settings_updated" class="notice notice-${type} settings-error is-dismissible">` +
            `<p><strong>${message}</strong></p>` +
            `<button type="button" class="notice-dismiss"><span class="screen-reader-text">${ccpo_admin_params.i18n.dismiss_notice}</span></button>` +
            `</div>`
        ).show();

        // Auto-dismiss after a few seconds
        setTimeout(function() {
            feedbackArea.find('.notice').fadeOut('slow', function() { $(this).remove(); });
        }, 5000);

        // Make sure the dismiss button works
        feedbackArea.off('click', '.notice-dismiss').on('click', '.notice-dismiss', function(e) {
            e.preventDefault();
            $(this).closest('.notice').fadeOut('slow', function() { $(this).remove(); });
        });
    }

    // --- Function to initialize jQuery UI Sortable --- 
    function initializeSortable() {
        const sortableList = $(sortableListSelector);
        if (sortableList.length > 0) {
            // Destroy existing instance first to avoid conflicts if re-initializing
            if (sortableList.hasClass('ui-sortable')) {
                try {
                    sortableList.sortable('destroy');
                } catch (e) {
                    console.error("Error destroying sortable:", e);
                }
            }
            // Initialize sortable
            sortableList.sortable({
                placeholder: "ui-state-highlight ccpo-placeholder", // Class for placeholder visuals
                handle: ".ccpo-handle", // Use the specific handle class
                axis: "y", // Only allow vertical dragging
                cursor: "move",
                opacity: 0.7, // Make item semi-transparent while dragging
                update: function(event, ui) {
                    // Maybe add a subtle visual cue that the order changed, e.g., a dirty flag
                }
            }).disableSelection(); // Prevent text selection while dragging
        } else {
            console.warn("Sortable list element not found for initialization.");
        }
    }

    // --- Event Handler: Category Selection Change --- 
    categorySelect.on('change', function() {
        const categoryId = $(this).val();

        // Clear previous feedback
        feedbackArea.empty(); 
        // Clear previous product list and show loading indicator
        productListWrapper.html(''); 
        initialMessage.hide(); // Hide the initial prompt
        productListContainer.append('<div class="ccpo-loading"><span class="spinner is-active"></span> ' + ccpo_admin_params.i18n.loading + '</div>');
        submitParagraph.hide(); // Hide save button while loading

        if (!categoryId) {
            productListContainer.find('.ccpo-loading').remove(); // Remove loading indicator
            initialMessage.show(); // Show the initial prompt again
            return; // Stop if no category is selected
        }

        // --- AJAX Request to Load Products --- 
        $.ajax({
            url: ccpo_admin_params.ajax_url,
            type: 'POST',
            data: {
                action: 'ccpo_load_category_products', // Defined in PHP (add_action)
                nonce: ccpo_admin_params.nonce,     // Nonce for security
                category_id: categoryId
            },
            dataType: 'json' // Expect a JSON response
        })
        .done(function(response) {
            if (response.success) {
                productListWrapper.html(response.data.html); // Inject the HTML list
                if (response.data.count > 0) {
                    initializeSortable(); // Make the new list sortable
                    submitParagraph.show(); // Show save button
                } else {
                    submitParagraph.hide(); // Keep save button hidden if no products
                }
            } else {
                // Display error message from server
                const errorMessage = response.data && response.data.message ? response.data.message : 'Unknown error';
                showFeedback(ccpo_admin_params.i18n.error_loading + ' ' + errorMessage, 'error');
                productListWrapper.html('<p class="error-message">' + ccpo_admin_params.i18n.error_loading + ' ' + errorMessage + '</p>');
                submitParagraph.hide();
            }
        })
        .fail(function(jqXHR, textStatus, errorThrown) {
            // Handle AJAX communication errors
            console.error("AJAX Error:", textStatus, errorThrown, jqXHR.responseText);
            const errorText = `${ccpo_admin_params.i18n.ajax_error} ${textStatus} - ${errorThrown}`; 
            showFeedback(errorText, 'error');
            productListWrapper.html('<p class="error-message">' + errorText + '</p>');
            submitParagraph.hide();
        })
        .always(function() {
            // Remove loading indicator regardless of success or failure
            productListContainer.find('.ccpo-loading').remove(); 
        });
    });

    // --- Event Handler: Save Button Click --- 
    saveButton.on('click', function(e) {
        e.preventDefault(); // Prevent default button behavior

        const categoryId = categorySelect.val();
        if (!categoryId) {
            alert(ccpo_admin_params.i18n.select_category_alert);
            return;
        }

        // Show spinner and disable button
        spinner.addClass('is-active');
        saveButton.prop('disabled', true);
        feedbackArea.empty(); // Clear previous feedback

        // Get the ordered list of product IDs from the data attributes of the list items
        const productIds = $(sortableListSelector + ' li').map(function() {
            return $(this).data('id');
        }).get(); // .get() converts jQuery map object to a standard array

        // --- AJAX Request to Save Order --- 
        $.ajax({
            url: ccpo_admin_params.ajax_url,
            type: 'POST',
            data: {
                action: 'ccpo_save_category_order', // Defined in PHP (add_action)
                security: ccpo_admin_params.save_nonce, // Use the specific save nonce
                category_id: categoryId,
                product_ids: productIds // Send the array of IDs
            },
            dataType: 'json'
        })
        .done(function(response) {
            if (response.success) {
                // Show success message
                const successMessage = response.data && response.data.message ? response.data.message : ccpo_admin_params.i18n.order_saved;
                showFeedback(successMessage, 'success');
            } else {
                // Show error message from server
                const errorMessage = response.data && response.data.message ? response.data.message : 'Unknown error';
                showFeedback(ccpo_admin_params.i18n.error_saving + ' ' + errorMessage, 'error');
            }
        })
        .fail(function(jqXHR, textStatus, errorThrown) {
             // Handle AJAX communication errors
            console.error("AJAX Error:", textStatus, errorThrown, jqXHR.responseText);
            const errorText = `${ccpo_admin_params.i18n.ajax_error} ${textStatus} - ${errorThrown}`;
            showFeedback(errorText, 'error');
        })
        .always(function() {
            // Hide spinner and re-enable button
            spinner.removeClass('is-active');
            saveButton.prop('disabled', false);
        });
    });

}); 