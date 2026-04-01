<?php
/**
 * ONOXIA RAG Sync — auto-sync pages, products, llms.txt, sitemap
 *
 * @package Onoxia
 * @copyright 2026 OCENOX LTD
 */

defined('ABSPATH') || exit;

class Onoxia_Sync {

    private $api;

    public function __construct() {
        $this->api = new Onoxia_Api();

        // Page sync on save/delete
        if (get_option('onoxia_sync_pages')) {
            add_action('save_post', [$this, 'on_save_post'], 20, 2);
            add_action('delete_post', [$this, 'on_delete_post']);
        }

        // WooCommerce product sync
        if (get_option('onoxia_sync_products') && function_exists('WC')) {
            add_action('woocommerce_update_product', [$this, 'on_save_product']);
            add_action('woocommerce_delete_product', [$this, 'on_delete_post']);
        }

        // Daily cron for llms.txt and sitemap
        add_action('onoxia_daily_sync', [$this, 'daily_sync']);
    }

    /**
     * Sync a page/post when saved.
     */
    public function on_save_post($post_id, $post) {
        if (wp_is_post_revision($post_id) || wp_is_post_autosave($post_id)) {
            return;
        }

        if ($post->post_status !== 'publish') {
            return;
        }

        // Only sync pages and posts
        if (!in_array($post->post_type, ['page', 'post'], true)) {
            return;
        }

        $content = wp_strip_all_tags($post->post_content);
        if (empty(trim($content))) {
            return;
        }

        $permalink = get_permalink($post_id);
        $path      = wp_parse_url($permalink, PHP_URL_PATH) ?: '/';
        $categories = wp_get_post_categories($post_id, ['fields' => 'names']);

        $this->api->sync_rag([
            [
                'name'         => $post->post_title,
                'type'         => 'text',
                'answer'       => "# {$post->post_title}\n\n{$content}",
                'url_patterns' => [$path . '*'],
                'context_tags' => !empty($categories) ? $categories : null,
            ],
        ], false); // Don't delete missing — only upsert this one
    }

    /**
     * Sync a WooCommerce product when saved.
     */
    public function on_save_product($product_id) {
        $product = wc_get_product($product_id);
        if (!$product || $product->get_status() !== 'publish') {
            return;
        }

        $permalink  = get_permalink($product_id);
        $path       = wp_parse_url($permalink, PHP_URL_PATH) ?: '/';
        $categories = wp_get_post_terms($product_id, 'product_cat', ['fields' => 'names']);
        $price      = $product->get_price();
        $desc       = $product->get_description() ?: $product->get_short_description();

        $text = "# {$product->get_name()}\n\n";
        $text .= "Preis: {$price}\n";
        if ($product->get_sku()) {
            $text .= "SKU: {$product->get_sku()}\n";
        }
        $text .= "\n{$desc}";

        $this->api->sync_rag([
            [
                'name'         => 'Produkt: ' . $product->get_name(),
                'type'         => 'text',
                'answer'       => $text,
                'url_patterns' => [$path . '*'],
                'context_tags' => !empty($categories) ? $categories : ['produkte'],
            ],
        ], false);
    }

    /**
     * Remove RAG source when a post/product is deleted.
     */
    public function on_delete_post($post_id) {
        $post = get_post($post_id);
        if (!$post) {
            return;
        }

        // Trigger a full sync to clean up deleted posts
        // (next daily sync will handle this via delete_missing)
        update_option('onoxia_needs_full_sync', true);
    }

    /**
     * Daily sync cron job.
     */
    public function daily_sync() {
        // llms.txt import
        if (get_option('onoxia_sync_llms')) {
            $site_url = home_url('/llms.txt');
            $this->api->ingest_llms($site_url);
        }

        // Sitemap import
        if (get_option('onoxia_sync_sitemap')) {
            $sitemap_url = home_url('/wp-sitemap.xml');
            $this->api->ingest_sitemap($sitemap_url);
        }

        // Full sync if needed (after deletion)
        if (get_option('onoxia_needs_full_sync')) {
            $this->full_sync();
            delete_option('onoxia_needs_full_sync');
        }
    }

    /**
     * Full sync with result message (for AJAX).
     */
    public function full_sync_with_result() {
        $posts = get_posts([
            'post_type'   => ['page', 'post'],
            'post_status' => 'publish',
            'numberposts' => 200,
        ]);

        $sources = [];
        foreach ($posts as $post) {
            $content = wp_strip_all_tags($post->post_content);
            if (empty(trim($content))) {
                continue;
            }
            $permalink  = get_permalink($post->ID);
            $path       = wp_parse_url($permalink, PHP_URL_PATH) ?: '/';
            $categories = wp_get_post_categories($post->ID, ['fields' => 'names']);
            $sources[] = [
                'name'         => $post->post_title,
                'type'         => 'text',
                'answer'       => "# {$post->post_title}\n\n{$content}",
                'url_patterns' => [$path . '*'],
                'context_tags' => !empty($categories) ? $categories : null,
            ];
        }

        if (empty($sources)) {
            return __('No published content found', 'onoxia');
        }

        $result = $this->api->sync_rag($sources, true);
        if (is_wp_error($result)) {
            return $result->get_error_message();
        }
        /* translators: %d: number of synced sources */
        return sprintf(__('%d sources synced', 'onoxia'), count($sources));
    }

    /**
     * Full sync — push all published pages and posts.
     */
    private function full_sync() {
        $posts = get_posts([
            'post_type'   => ['page', 'post'],
            'post_status' => 'publish',
            'numberposts' => 200,
        ]);

        $sources = [];
        foreach ($posts as $post) {
            $content = wp_strip_all_tags($post->post_content);
            if (empty(trim($content))) {
                continue;
            }

            $permalink  = get_permalink($post->ID);
            $path       = wp_parse_url($permalink, PHP_URL_PATH) ?: '/';
            $categories = wp_get_post_categories($post->ID, ['fields' => 'names']);

            $sources[] = [
                'name'         => $post->post_title,
                'type'         => 'text',
                'answer'       => "# {$post->post_title}\n\n{$content}",
                'url_patterns' => [$path . '*'],
                'context_tags' => !empty($categories) ? $categories : null,
            ];
        }

        if (!empty($sources)) {
            $this->api->sync_rag($sources, true); // delete_missing = true
        }
    }
}
