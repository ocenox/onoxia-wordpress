<?php
/**
 * ONOXIA Settings Page Template
 *
 * @package Onoxia
 * @copyright 2026 OCENOX LTD
 */

defined('ABSPATH') || exit;

$token     = get_option('onoxia_api_token', '');
$site_name = get_option('onoxia_site_name', '');
$site_uuid = get_option('onoxia_site_uuid', '');
$has_woo   = class_exists('WooCommerce');
?>

<div class="wrap onoxia-settings">
    <h1>
        <img src="<?php echo esc_url(ONOXIA_PLUGIN_URL . 'admin/css/onoxia-icon.svg'); ?>" alt="" style="height:28px;vertical-align:middle;margin-right:8px;">
        ONOXIA
    </h1>

    <form method="post" action="options.php">
        <?php settings_fields('onoxia_settings'); ?>

        <!-- Connection -->
        <div class="onoxia-card">
            <h2><?php esc_html_e('Connection', 'onoxia'); ?></h2>
            <table class="form-table">
                <tr>
                    <th><label for="onoxia_api_token"><?php esc_html_e('API Token', 'onoxia'); ?></label></th>
                    <td>
                        <input type="password" name="onoxia_api_token" id="onoxia_api_token"
                               value="<?php echo esc_attr($token); ?>"
                               class="regular-text" autocomplete="off">
                        <button type="button" id="onoxia-validate" class="button">
                            <?php esc_html_e('Test connection', 'onoxia'); ?>
                        </button>
                        <p class="description">
                            <?php printf(
                                /* translators: %s: link to the API tokens page */
                                esc_html__('Create a token at %s.', 'onoxia'),
                                '<a href="https://onoxia.nz/app/api-tokens" target="_blank">Administration &rarr; API Tokens</a>'
                            ); ?>
                        </p>
                        <div id="onoxia-status" style="margin-top:10px;"></div>
                    </td>
                </tr>
            </table>

            <?php if (!empty($site_name)) : ?>
                <div class="onoxia-status-box onoxia-status-ok">
                    <strong><?php esc_html_e('Connected:', 'onoxia'); ?></strong>
                    <?php echo esc_html($site_name); ?>
                    <code style="margin-left:8px;font-size:11px;"><?php echo esc_html($site_uuid); ?></code>
                </div>
            <?php endif; ?>
        </div>

        <!-- Auto-Sync -->
        <div class="onoxia-card">
            <h2><?php esc_html_e('Auto Knowledge Import', 'onoxia'); ?></h2>
            <p class="description"><?php esc_html_e('The bot automatically learns the content of your website.', 'onoxia'); ?></p>
            <table class="form-table">
                <tr>
                    <th><?php esc_html_e('Sync pages', 'onoxia'); ?></th>
                    <td>
                        <label>
                            <input type="checkbox" name="onoxia_sync_pages" value="1" <?php checked(get_option('onoxia_sync_pages')); ?>>
                            <?php esc_html_e('Automatically sync pages and posts as RAG sources (on save/delete)', 'onoxia'); ?>
                        </label>
                    </td>
                </tr>
                <tr>
                    <th><?php esc_html_e('Import llms.txt', 'onoxia'); ?></th>
                    <td>
                        <label>
                            <input type="checkbox" name="onoxia_sync_llms" value="1" <?php checked(get_option('onoxia_sync_llms')); ?>>
                            <?php esc_html_e('Import llms.txt from website daily', 'onoxia'); ?>
                        </label>
                    </td>
                </tr>
                <tr>
                    <th><?php esc_html_e('Sitemap navigation', 'onoxia'); ?></th>
                    <td>
                        <label>
                            <input type="checkbox" name="onoxia_sync_sitemap" value="1" <?php checked(get_option('onoxia_sync_sitemap')); ?>>
                            <?php esc_html_e('Import sitemap structure daily (bot can link to pages)', 'onoxia'); ?>
                        </label>
                    </td>
                </tr>
            </table>
        </div>

        <?php if ($has_woo) : ?>
        <!-- WooCommerce -->
        <div class="onoxia-card">
            <h2>WooCommerce</h2>
            <table class="form-table">
                <tr>
                    <th><?php esc_html_e('Sync products', 'onoxia'); ?></th>
                    <td>
                        <label>
                            <input type="checkbox" name="onoxia_sync_products" value="1" <?php checked(get_option('onoxia_sync_products')); ?>>
                            <?php esc_html_e('Sync products as RAG sources (bot can answer pricing questions)', 'onoxia'); ?>
                        </label>
                    </td>
                </tr>
                <tr>
                    <th><?php esc_html_e('Product context', 'onoxia'); ?></th>
                    <td>
                        <label>
                            <input type="checkbox" name="onoxia_product_context" value="1" <?php checked(get_option('onoxia_product_context')); ?>>
                            <?php esc_html_e('Automatically send product name, price and category as context on product pages', 'onoxia'); ?>
                        </label>
                    </td>
                </tr>
            </table>
        </div>
        <?php endif; ?>

        <!-- Widget Settings -->
        <div class="onoxia-card">
            <h2><?php esc_html_e('Widget Settings', 'onoxia'); ?></h2>
            <table class="form-table">
                <tr>
                    <th><label for="onoxia_context_tags"><?php esc_html_e('Context tags', 'onoxia'); ?></label></th>
                    <td>
                        <input type="text" name="onoxia_context_tags" id="onoxia_context_tags"
                               value="<?php echo esc_attr(get_option('onoxia_context_tags', '')); ?>"
                               class="regular-text" placeholder="tag1, tag2, tag3">
                        <p class="description"><?php esc_html_e('Comma-separated tags for context-sensitive RAG (optional).', 'onoxia'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th><label for="onoxia_page_restriction"><?php esc_html_e('Page restriction', 'onoxia'); ?></label></th>
                    <td>
                        <input type="text" name="onoxia_page_restriction" id="onoxia_page_restriction"
                               value="<?php echo esc_attr(get_option('onoxia_page_restriction', 'all')); ?>"
                               class="regular-text" placeholder="all">
                        <p class="description"><?php esc_html_e('"all" = everywhere. Or URL patterns: /shop/*, /contact, !/admin*', 'onoxia'); ?></p>
                    </td>
                </tr>
            </table>
        </div>

        <?php submit_button(esc_html__('Save settings', 'onoxia')); ?>
    </form>
</div>

<script>
document.getElementById('onoxia-validate')?.addEventListener('click', function() {
    const token = document.getElementById('onoxia_api_token').value;
    const status = document.getElementById('onoxia-status');

    if (!token) {
        status.innerHTML = '<div class="notice notice-error inline"><p>' + <?php echo wp_json_encode(esc_html__('Please enter a token.', 'onoxia')); ?> + '</p></div>';
        return;
    }

    status.innerHTML = '<p>' + <?php echo wp_json_encode(esc_html__('Testing connection...', 'onoxia')); ?> + '</p>';

    fetch(ajaxurl, {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: new URLSearchParams({
            action: 'onoxia_validate_token',
            nonce: '<?php echo esc_js(wp_create_nonce('onoxia_nonce')); ?>',
            token: token
        })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            status.innerHTML = '<div class="notice notice-success inline"><p><strong>' + <?php echo wp_json_encode(esc_html__('Connected:', 'onoxia')); ?> + '</strong> ' +
                data.data.name + ' (' + data.data.persona + ')</p></div>';
        } else {
            status.innerHTML = '<div class="notice notice-error inline"><p><strong>' + <?php echo wp_json_encode(esc_html__('Error:', 'onoxia')); ?> + '</strong> ' +
                (data.data || <?php echo wp_json_encode(esc_html__('Unknown error', 'onoxia')); ?>) + '</p></div>';
        }
    })
    .catch(() => {
        status.innerHTML = '<div class="notice notice-error inline"><p>' + <?php echo wp_json_encode(esc_html__('Connection error.', 'onoxia')); ?> + '</p></div>';
    });
});
</script>
