<?php
/**
 * ONOXIA Admin Settings Page
 *
 * @package Onoxia
 * @copyright 2026 OCENOX LTD
 */

defined('ABSPATH') || exit;

class Onoxia_Admin {

    public function __construct() {
        add_action('admin_menu', [$this, 'add_menu']);
        add_action('admin_init', [$this, 'register_settings']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_styles']);
        add_action('wp_ajax_onoxia_validate_token', [$this, 'ajax_validate_token']);
        add_action('wp_ajax_onoxia_sync_articles', [$this, 'ajax_sync_articles']);
        add_action('wp_ajax_onoxia_sync_llms', [$this, 'ajax_sync_llms']);
        add_action('wp_ajax_onoxia_sync_sitemap', [$this, 'ajax_sync_sitemap']);
        add_action('update_option_onoxia_api_token', [$this, 'on_token_updated'], 10, 2);
    }

    public function add_menu() {
        add_options_page(
            'ONOXIA',
            'ONOXIA',
            'manage_options',
            'onoxia',
            [$this, 'render_settings_page']
        );
    }

    public function register_settings() {
        register_setting('onoxia_settings', 'onoxia_api_token', ['sanitize_callback' => [$this, 'sanitize_token']]);
        register_setting('onoxia_settings', 'onoxia_site_uuid', ['sanitize_callback' => 'sanitize_text_field']);
        register_setting('onoxia_settings', 'onoxia_site_name', ['sanitize_callback' => 'sanitize_text_field']);
        register_setting('onoxia_settings', 'onoxia_context_tags', ['sanitize_callback' => 'sanitize_text_field']);
        register_setting('onoxia_settings', 'onoxia_sync_pages', ['sanitize_callback' => 'absint']);
        register_setting('onoxia_settings', 'onoxia_sync_llms', ['sanitize_callback' => 'absint']);
        register_setting('onoxia_settings', 'onoxia_sync_sitemap', ['sanitize_callback' => 'absint']);
        register_setting('onoxia_settings', 'onoxia_sync_products', ['sanitize_callback' => 'absint']);
        register_setting('onoxia_settings', 'onoxia_product_context', ['sanitize_callback' => 'absint']);
        register_setting('onoxia_settings', 'onoxia_page_restriction', ['sanitize_callback' => 'sanitize_text_field']);
    }

    /**
     * Strip Laravel Sanctum "id|" prefix if user pastes the full token string.
     */
    public function sanitize_token($value) {
        $value = sanitize_text_field($value);
        if (preg_match('/^\d+\|(.+)$/', $value, $m)) {
            $value = $m[1];
        }
        return $value;
    }

    public function enqueue_styles($hook) {
        if ($hook !== 'settings_page_onoxia') {
            return;
        }
        wp_enqueue_style('onoxia-admin', ONOXIA_PLUGIN_URL . 'admin/css/admin.css', [], ONOXIA_VERSION);
    }

    public function render_settings_page() {
        include ONOXIA_PLUGIN_DIR . 'admin/views/settings.php';
    }

    /**
     * Auto-fetch site info when token is saved via settings form.
     */
    public function on_token_updated($old_value, $new_value) {
        $token = sanitize_text_field($new_value);
        if (empty($token)) {
            delete_option('onoxia_site_uuid');
            delete_option('onoxia_site_name');
            delete_transient('onoxia_widget_url');
            return;
        }

        $api  = new Onoxia_Api($token);
        $site = $api->get_site();

        if (!is_wp_error($site)) {
            update_option('onoxia_site_uuid', $site['id'] ?? '');
            update_option('onoxia_site_name', $site['name'] ?? '');
            $widget_url = $site['widget_url'] ?? '';
            if (!empty($widget_url)) {
                set_transient('onoxia_widget_url', $widget_url, DAY_IN_SECONDS);
            }
        }
    }

    public function ajax_validate_token() {
        check_ajax_referer('onoxia_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized');
        }

        $token = $this->sanitize_token($_POST['token'] ?? '');
        $api   = new Onoxia_Api($token);
        $site  = $api->get_site();

        if (is_wp_error($site)) {
            wp_send_json_error($site->get_error_message());
        }

        // Save site info
        update_option('onoxia_site_uuid', $site['id'] ?? '');
        update_option('onoxia_site_name', $site['name'] ?? '');

        // Cache widget_url from API (CDN support) — 24h transient
        $widget_url = $site['widget_url'] ?? '';
        if (!empty($widget_url)) {
            set_transient('onoxia_widget_url', $widget_url, DAY_IN_SECONDS);
        }

        wp_send_json_success([
            'name'    => $site['name'] ?? 'Unknown',
            'id'      => $site['id'] ?? '',
            'persona' => $site['persona'] ?? 'ONOXIA',
        ]);
    }

    public function ajax_sync_articles() {
        check_ajax_referer('onoxia_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized');
        }
        if (!get_option('onoxia_sync_pages')) {
            wp_send_json_success(['skipped' => true, 'message' => __('Disabled', 'onoxia')]);
        }

        $sync = new Onoxia_Sync();
        $result = $sync->full_sync_with_result();
        wp_send_json_success(['message' => $result]);
    }

    public function ajax_sync_llms() {
        check_ajax_referer('onoxia_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized');
        }
        if (!get_option('onoxia_sync_llms')) {
            wp_send_json_success(['skipped' => true, 'message' => __('Disabled', 'onoxia')]);
        }

        $api = new Onoxia_Api();
        $result = $api->ingest_llms(home_url('/llms.txt'));
        if (is_wp_error($result)) {
            wp_send_json_error($result->get_error_message());
        }
        wp_send_json_success(['message' => __('Imported', 'onoxia')]);
    }

    public function ajax_sync_sitemap() {
        check_ajax_referer('onoxia_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized');
        }
        if (!get_option('onoxia_sync_sitemap')) {
            wp_send_json_success(['skipped' => true, 'message' => __('Disabled', 'onoxia')]);
        }

        $api = new Onoxia_Api();
        $result = $api->ingest_sitemap(home_url('/wp-sitemap.xml'));
        if (is_wp_error($result)) {
            wp_send_json_error($result->get_error_message());
        }
        wp_send_json_success(['message' => __('Imported', 'onoxia')]);
    }
}
