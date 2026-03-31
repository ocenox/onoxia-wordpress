<?php
/**
 * ONOXIA Frontend — Widget Injection
 *
 * @package Onoxia
 * @copyright 2026 OCENOX LTD
 */

defined('ABSPATH') || exit;

class Onoxia_Frontend {

    private $site_uuid;
    private $widget_url;

    public function __construct() {
        $this->site_uuid = get_option('onoxia_site_uuid', '');

        if (!empty($this->site_uuid)) {
            add_action('wp_footer', [$this, 'inject_widget']);
        }
    }

    /**
     * Get cached site info (UUID + widget_url) with 24h transient cache.
     * Refreshes from API on cache miss, falls back to stored values on error.
     */
    private function get_widget_url() {
        if ($this->widget_url !== null) {
            return $this->widget_url;
        }

        // Try transient cache first
        $cached = get_transient('onoxia_widget_url');
        if ($cached !== false) {
            $this->widget_url = $cached;
            return $this->widget_url;
        }

        // Cache miss — refresh from API
        $token = get_option('onoxia_api_token', '');
        if (!empty($token)) {
            $api  = new Onoxia_Api($token);
            $site = $api->get_site();

            if (!is_wp_error($site)) {
                $url = $site['widget_url'] ?? '';
                if (!empty($url)) {
                    set_transient('onoxia_widget_url', $url, DAY_IN_SECONDS);
                    $this->widget_url = $url;

                    // Also refresh UUID/name if changed
                    if (!empty($site['id'])) {
                        update_option('onoxia_site_uuid', $site['id']);
                        $this->site_uuid = $site['id'];
                    }
                    if (!empty($site['name'])) {
                        update_option('onoxia_site_name', $site['name']);
                    }

                    return $this->widget_url;
                }
            }
        }

        // Fallback to default
        $this->widget_url = ONOXIA_WIDGET_URL;
        return $this->widget_url;
    }

    /**
     * Inject the ONOXIA widget script tag into the footer.
     */
    public function inject_widget() {
        // Page restriction check
        if (!$this->is_allowed_page()) {
            return;
        }

        $attrs = 'data-site="' . esc_attr($this->site_uuid) . '"';

        // Context tags
        $tags = get_option('onoxia_context_tags', '');
        if (!empty($tags)) {
            $attrs .= ' data-tags="' . esc_attr($tags) . '"';
        }

        // Build visitor context
        $context = $this->build_context();
        if (!empty($context)) {
            $attrs .= " data-context='" . esc_attr(wp_json_encode($context)) . "'";
        }

        echo '<script src="' . esc_url($this->get_widget_url()) . '" ' . $attrs . '></script>' . "\n";
    }

    /**
     * Build visitor context based on current page and user.
     */
    private function build_context() {
        $context = [];

        // Logged-in user context
        if (is_user_logged_in()) {
            $user = wp_get_current_user();
            $context['name']  = $user->display_name;
            $context['email'] = $user->user_email;
        }

        // WooCommerce product context
        if (get_option('onoxia_product_context') && function_exists('is_product') && is_product()) {
            global $product;
            if ($product) {
                $context['product_name']     = $product->get_name();
                $context['product_price']    = $product->get_price();
                $context['product_category'] = implode(', ', wp_get_post_terms($product->get_id(), 'product_cat', ['fields' => 'names']));
                $context['product_sku']      = $product->get_sku();
            }
        }

        // WooCommerce cart context
        if (function_exists('WC') && WC()->cart) {
            $cart = WC()->cart;
            if ($cart->get_cart_contents_count() > 0) {
                $context['cart_value'] = $cart->get_cart_total();
                $context['cart_items'] = $cart->get_cart_contents_count();
            }
        }

        return $context;
    }

    /**
     * Check if widget should appear on the current page.
     */
    private function is_allowed_page() {
        $restriction = get_option('onoxia_page_restriction', 'all');

        if ($restriction === 'all' || empty($restriction)) {
            return true;
        }

        // Exclude patterns (comma-separated URL patterns)
        if (strpos($restriction, '/') !== false) {
            $patterns = array_map('trim', explode(',', $restriction));
            $path     = wp_parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?: '/';

            foreach ($patterns as $pattern) {
                if (empty($pattern)) continue;

                $negate = strpos($pattern, '!') === 0;
                $p      = ltrim($pattern, '!');

                if (str_ends_with($p, '*')) {
                    $match = str_starts_with($path, rtrim($p, '*'));
                } else {
                    $match = rtrim($path, '/') === rtrim($p, '/');
                }

                if ($negate && $match) return false;
                if (!$negate && $match) return true;
            }

            return false;
        }

        return true;
    }
}
