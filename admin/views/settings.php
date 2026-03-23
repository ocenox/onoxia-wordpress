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
            <h2><?php esc_html_e('Verbindung', 'onoxia'); ?></h2>
            <table class="form-table">
                <tr>
                    <th><label for="onoxia_api_token"><?php esc_html_e('API-Token', 'onoxia'); ?></label></th>
                    <td>
                        <input type="password" name="onoxia_api_token" id="onoxia_api_token"
                               value="<?php echo esc_attr($token); ?>"
                               class="regular-text" autocomplete="off">
                        <button type="button" id="onoxia-validate" class="button">
                            <?php esc_html_e('Verbindung testen', 'onoxia'); ?>
                        </button>
                        <p class="description">
                            <?php printf(
                                esc_html__('Erstellen Sie einen Token unter %s.', 'onoxia'),
                                '<a href="https://onoxia.nz/app/api-tokens" target="_blank">Administration &rarr; API-Tokens</a>'
                            ); ?>
                        </p>
                        <div id="onoxia-status" style="margin-top:10px;"></div>
                    </td>
                </tr>
            </table>

            <?php if (!empty($site_name)) : ?>
                <div class="onoxia-status-box onoxia-status-ok">
                    <strong><?php esc_html_e('Verbunden:', 'onoxia'); ?></strong>
                    <?php echo esc_html($site_name); ?>
                    <code style="margin-left:8px;font-size:11px;"><?php echo esc_html($site_uuid); ?></code>
                </div>
            <?php endif; ?>
        </div>

        <!-- Auto-Sync -->
        <div class="onoxia-card">
            <h2><?php esc_html_e('Auto-Wissensimport', 'onoxia'); ?></h2>
            <p class="description"><?php esc_html_e('Der Bot lernt automatisch den Inhalt Ihrer Website.', 'onoxia'); ?></p>
            <table class="form-table">
                <tr>
                    <th><?php esc_html_e('Seiten syncen', 'onoxia'); ?></th>
                    <td>
                        <label>
                            <input type="checkbox" name="onoxia_sync_pages" value="1" <?php checked(get_option('onoxia_sync_pages')); ?>>
                            <?php esc_html_e('Seiten und Beiträge automatisch als RAG-Quellen syncen (bei Speichern/Löschen)', 'onoxia'); ?>
                        </label>
                    </td>
                </tr>
                <tr>
                    <th><?php esc_html_e('llms.txt importieren', 'onoxia'); ?></th>
                    <td>
                        <label>
                            <input type="checkbox" name="onoxia_sync_llms" value="1" <?php checked(get_option('onoxia_sync_llms')); ?>>
                            <?php esc_html_e('llms.txt der Website täglich importieren', 'onoxia'); ?>
                        </label>
                    </td>
                </tr>
                <tr>
                    <th><?php esc_html_e('Sitemap-Navigation', 'onoxia'); ?></th>
                    <td>
                        <label>
                            <input type="checkbox" name="onoxia_sync_sitemap" value="1" <?php checked(get_option('onoxia_sync_sitemap')); ?>>
                            <?php esc_html_e('Sitemap-Struktur täglich importieren (Bot kann auf Seiten verweisen)', 'onoxia'); ?>
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
                    <th><?php esc_html_e('Produkte syncen', 'onoxia'); ?></th>
                    <td>
                        <label>
                            <input type="checkbox" name="onoxia_sync_products" value="1" <?php checked(get_option('onoxia_sync_products')); ?>>
                            <?php esc_html_e('Produkte als RAG-Quellen syncen (Bot kann Preisfragen beantworten)', 'onoxia'); ?>
                        </label>
                    </td>
                </tr>
                <tr>
                    <th><?php esc_html_e('Produkt-Kontext', 'onoxia'); ?></th>
                    <td>
                        <label>
                            <input type="checkbox" name="onoxia_product_context" value="1" <?php checked(get_option('onoxia_product_context')); ?>>
                            <?php esc_html_e('Auf Produktseiten automatisch Produktname, Preis und Kategorie als Kontext senden', 'onoxia'); ?>
                        </label>
                    </td>
                </tr>
            </table>
        </div>
        <?php endif; ?>

        <!-- Widget Settings -->
        <div class="onoxia-card">
            <h2><?php esc_html_e('Widget-Einstellungen', 'onoxia'); ?></h2>
            <table class="form-table">
                <tr>
                    <th><label for="onoxia_context_tags"><?php esc_html_e('Kontext-Tags', 'onoxia'); ?></label></th>
                    <td>
                        <input type="text" name="onoxia_context_tags" id="onoxia_context_tags"
                               value="<?php echo esc_attr(get_option('onoxia_context_tags', '')); ?>"
                               class="regular-text" placeholder="tag1, tag2, tag3">
                        <p class="description"><?php esc_html_e('Komma-separierte Tags für kontextsensitives RAG (optional).', 'onoxia'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th><label for="onoxia_page_restriction"><?php esc_html_e('Seiteneinschränkung', 'onoxia'); ?></label></th>
                    <td>
                        <input type="text" name="onoxia_page_restriction" id="onoxia_page_restriction"
                               value="<?php echo esc_attr(get_option('onoxia_page_restriction', 'all')); ?>"
                               class="regular-text" placeholder="all">
                        <p class="description"><?php esc_html_e('"all" = überall. Oder URL-Patterns: /shop/*, /kontakt, !/admin*', 'onoxia'); ?></p>
                    </td>
                </tr>
            </table>
        </div>

        <?php submit_button(__('Einstellungen speichern', 'onoxia')); ?>
    </form>
</div>

<script>
document.getElementById('onoxia-validate')?.addEventListener('click', function() {
    const token = document.getElementById('onoxia_api_token').value;
    const status = document.getElementById('onoxia-status');

    if (!token) {
        status.innerHTML = '<div class="notice notice-error inline"><p>Bitte Token eingeben.</p></div>';
        return;
    }

    status.innerHTML = '<p>Verbindung wird getestet...</p>';

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
            status.innerHTML = '<div class="notice notice-success inline"><p><strong>Verbunden:</strong> ' +
                data.data.name + ' (' + data.data.persona + ')</p></div>';
        } else {
            status.innerHTML = '<div class="notice notice-error inline"><p><strong>Fehler:</strong> ' +
                (data.data || 'Unbekannter Fehler') + '</p></div>';
        }
    })
    .catch(() => {
        status.innerHTML = '<div class="notice notice-error inline"><p>Verbindungsfehler.</p></div>';
    });
});
</script>
