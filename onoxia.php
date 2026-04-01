<?php
/**
 * Plugin Name: ONOXIA – AI Chatbot
 * Plugin URI:  https://onoxia.nz
 * Description: AI-powered chat widget with RAG knowledge base, live chat handover and GDPR compliance. Auto-sync for pages, llms.txt and sitemap.
 * Version:     1.2.2
 * Requires at least: 5.8
 * Requires PHP: 7.4
 * Author:      OCENOX LTD
 * Author URI:  https://ocenox.com
 * License:     GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: onoxia
 * Domain Path: /languages
 *
 * @package Onoxia
 * @copyright 2026 OCENOX LTD, New Zealand
 */

defined('ABSPATH') || exit;

define('ONOXIA_VERSION', '1.2.2');
define('ONOXIA_PLUGIN_FILE', __FILE__);
define('ONOXIA_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('ONOXIA_PLUGIN_URL', plugin_dir_url(__FILE__));
define('ONOXIA_API_BASE', 'https://onoxia.nz/api/v1/bot');
define('ONOXIA_WIDGET_URL', 'https://onoxia.nz/widget.js');

// Autoload
require_once ONOXIA_PLUGIN_DIR . 'includes/class-onoxia-api.php';
require_once ONOXIA_PLUGIN_DIR . 'includes/class-onoxia-admin.php';
require_once ONOXIA_PLUGIN_DIR . 'includes/class-onoxia-frontend.php';
require_once ONOXIA_PLUGIN_DIR . 'includes/class-onoxia-sync.php';

/**
 * Initialize the plugin.
 *
 * @since 1.0.0
 * @copyright OCENOX LTD
 */
function onoxia_init() {
    if (is_admin()) {
        new Onoxia_Admin();
    }

    new Onoxia_Frontend();

    if (get_option('onoxia_sync_pages') || get_option('onoxia_sync_products')) {
        new Onoxia_Sync();
    }
}
add_action('plugins_loaded', 'onoxia_init');

/**
 * Activation hook — schedule cron jobs.
 */
function onoxia_activate() {
    if (!wp_next_scheduled('onoxia_daily_sync')) {
        wp_schedule_event(time(), 'daily', 'onoxia_daily_sync');
    }
}
register_activation_hook(__FILE__, 'onoxia_activate');

/**
 * Deactivation hook — clear cron jobs.
 */
function onoxia_deactivate() {
    wp_clear_scheduled_hook('onoxia_daily_sync');
}
register_deactivation_hook(__FILE__, 'onoxia_deactivate');
