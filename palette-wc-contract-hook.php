<?php
/**
 * WooCommerce Auto-Checkout & Customer Pre-fill Snippet
 * Place this in your child-theme's functions.php or in the "Code Snippets" plugin on palette.agency
 */

add_action('template_redirect', 'palette_handle_contract_redirect');
function palette_handle_contract_redirect() {
    // Check if coming from signed contract proposal
    if (isset($_GET['contract_signed']) && $_GET['contract_signed'] === 'true') {
        
        // Product ID or Slug of the "Bartan Website" service in WooCommerce
        // You can change 'bartan-website' to your actual product ID or let it auto-detect
        $product_slug = 'bartan-website';
        $product = get_page_by_path($product_slug, OBJECT, 'product');
        $product_id = $product ? $product->ID : null;

        if ($product_id && function_exists('WC')) {
            // Optional: empty previous cart so there is no duplicate
            WC()->cart->empty_cart();

            // Add product to cart
            WC()->cart->add_to_cart($product_id, 1);

            // Pre-fill Customer billing details into WooCommerce session
            if (!empty($_GET['billing_phone'])) {
                WC()->customer->set_billing_phone(sanitize_text_field($_GET['billing_phone']));
            }
            if (!empty($_GET['billing_first_name'])) {
                WC()->customer->set_billing_first_name(sanitize_text_field($_GET['billing_first_name']));
            }
            if (!empty($_GET['billing_last_name'])) {
                WC()->customer->set_billing_last_name(sanitize_text_field($_GET['billing_last_name']));
            }

            // Save customer session
            WC()->customer->save();

            // Redirect directly to checkout page
            $checkout_url = wc_get_checkout_url();
            
            // Pass payment method preference to checkout
            if (!empty($_GET['payment_mode'])) {
                $checkout_url = add_query_arg('payment_mode', sanitize_text_field($_GET['payment_mode']), $checkout_url);
            }

            wp_safe_redirect($checkout_url);
            exit;
        }
    }
}
