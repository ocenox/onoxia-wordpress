<?php
/**
 * ONOXIA Settings Page Template
 *
 * @package Onoxia
 * @copyright 2026 OCENOX LTD
 */

defined('ABSPATH') || exit;

$onoxia_token     = get_option('onoxia_api_token', '');
$onoxia_site_name = get_option('onoxia_site_name', '');
$onoxia_site_uuid = get_option('onoxia_site_uuid', '');
$onoxia_has_woo   = class_exists('WooCommerce');
$onoxia_widget_url = get_transient('onoxia_widget_url') ?: '';

// Token preview (first 8 + last 4 chars)
$onoxia_token_preview = '';
if (!empty($onoxia_token)) {
    $onoxia_token_preview = substr($onoxia_token, 0, 8) . '****' . substr($onoxia_token, -4);
}

// Sync settings
$onoxia_sync_pages   = get_option('onoxia_sync_pages');
$onoxia_sync_llms    = get_option('onoxia_sync_llms');
$onoxia_sync_sitemap = get_option('onoxia_sync_sitemap');
$onoxia_sync_products = get_option('onoxia_sync_products');

// Cron URL
$onoxia_cron_url = home_url('?onoxia_cron=1&secret=' . wp_hash('onoxia_cron_secret'));
?>

<div class="wrap onoxia-settings">
    <h1>
        <img src="<?php echo esc_url(ONOXIA_PLUGIN_URL . 'admin/css/onoxia-icon.svg'); ?>" alt="" style="height:28px;vertical-align:middle;margin-right:8px;">
        ONOXIA
    </h1>

    <!-- Status & Debug -->
    <div class="onoxia-row">
        <div class="onoxia-col">
            <div class="onoxia-card">
                <h2><?php esc_html_e('Connection', 'onoxia'); ?></h2>

                <?php if (!empty($onoxia_site_name)) : ?>
                    <div class="onoxia-status-box onoxia-status-ok">
                        <strong>&#10003; <?php esc_html_e('Connected', 'onoxia'); ?></strong>
                    </div>
                <?php elseif (!empty($onoxia_token)) : ?>
                    <div class="onoxia-status-box onoxia-status-error">
                        <strong>&#10007; <?php esc_html_e('Token set but not connected — click "Test connection"', 'onoxia'); ?></strong>
                    </div>
                <?php else : ?>
                    <div class="onoxia-status-box" style="background:#fffbeb;border:1px solid #fde68a;color:#92400e;">
                        <strong>&#9888; <?php esc_html_e('No API token configured', 'onoxia'); ?></strong>
                    </div>
                <?php endif; ?>

                <table class="widefat striped" style="margin-top:12px;">
                    <tbody>
                        <tr>
                            <th><?php esc_html_e('API Token', 'onoxia'); ?></th>
                            <td>
                                <?php if (!empty($onoxia_token)) : ?>
                                    <span class="onoxia-badge onoxia-badge-ok"><?php esc_html_e('Set', 'onoxia'); ?></span>
                                    <code style="margin-left:6px;"><?php echo esc_html($onoxia_token_preview); ?></code>
                                <?php else : ?>
                                    <span class="onoxia-badge onoxia-badge-off"><?php esc_html_e('Missing', 'onoxia'); ?></span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php if (!empty($onoxia_site_name)) : ?>
                        <tr>
                            <th><?php esc_html_e('Site name', 'onoxia'); ?></th>
                            <td><?php echo esc_html($onoxia_site_name); ?></td>
                        </tr>
                        <tr>
                            <th><?php esc_html_e('Site UUID', 'onoxia'); ?></th>
                            <td><code><?php echo esc_html($onoxia_site_uuid); ?></code></td>
                        </tr>
                        <?php endif; ?>
                        <?php if (!empty($onoxia_widget_url)) : ?>
                        <tr>
                            <th><?php esc_html_e('Widget URL', 'onoxia'); ?></th>
                            <td><code><?php echo esc_html($onoxia_widget_url); ?></code></td>
                        </tr>
                        <?php endif; ?>
                        <tr>
                            <th><?php esc_html_e('Plugin version', 'onoxia'); ?></th>
                            <td><?php echo esc_html(ONOXIA_VERSION); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="onoxia-col">
            <div class="onoxia-card">
                <h2><?php esc_html_e('Configuration', 'onoxia'); ?></h2>
                <table class="widefat striped">
                    <tbody>
                        <tr>
                            <th><?php esc_html_e('Sync pages', 'onoxia'); ?></th>
                            <td>
                                <span class="onoxia-badge <?php echo $onoxia_sync_pages ? 'onoxia-badge-ok' : 'onoxia-badge-off'; ?>">
                                    <?php echo $onoxia_sync_pages ? esc_html__('Yes', 'onoxia') : esc_html__('No', 'onoxia'); ?>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th><?php esc_html_e('Import llms.txt', 'onoxia'); ?></th>
                            <td>
                                <span class="onoxia-badge <?php echo $onoxia_sync_llms ? 'onoxia-badge-ok' : 'onoxia-badge-off'; ?>">
                                    <?php echo $onoxia_sync_llms ? esc_html__('Yes', 'onoxia') : esc_html__('No', 'onoxia'); ?>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th><?php esc_html_e('Sitemap navigation', 'onoxia'); ?></th>
                            <td>
                                <span class="onoxia-badge <?php echo $onoxia_sync_sitemap ? 'onoxia-badge-ok' : 'onoxia-badge-off'; ?>">
                                    <?php echo $onoxia_sync_sitemap ? esc_html__('Yes', 'onoxia') : esc_html__('No', 'onoxia'); ?>
                                </span>
                            </td>
                        </tr>
                        <?php if ($onoxia_has_woo) : ?>
                        <tr>
                            <th><?php esc_html_e('Sync products', 'onoxia'); ?></th>
                            <td>
                                <span class="onoxia-badge <?php echo $onoxia_sync_products ? 'onoxia-badge-ok' : 'onoxia-badge-off'; ?>">
                                    <?php echo $onoxia_sync_products ? esc_html__('Yes', 'onoxia') : esc_html__('No', 'onoxia'); ?>
                                </span>
                            </td>
                        </tr>
                        <?php endif; ?>
                        <tr>
                            <th><?php esc_html_e('Page restriction', 'onoxia'); ?></th>
                            <td><?php echo esc_html(get_option('onoxia_page_restriction', 'all')); ?></td>
                        </tr>
                    </tbody>
                </table>

                <div style="margin-top:12px;display:flex;gap:8px;flex-wrap:wrap;">
                    <a href="https://onoxia.nz/app" target="_blank" rel="noopener" class="button">
                        <?php esc_html_e('ONOXIA Dashboard', 'onoxia'); ?> &#8599;
                    </a>
                    <a href="https://onoxia.nz/docs/api" target="_blank" rel="noopener" class="button">
                        <?php esc_html_e('API Docs', 'onoxia'); ?> &#8599;
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Sync Panel & Cron -->
    <div class="onoxia-row">
        <div class="onoxia-col">
            <div class="onoxia-card">
                <div style="display:flex;justify-content:space-between;align-items:center;">
                    <h2 style="margin:0;"><?php esc_html_e('Sync Knowledge Base', 'onoxia'); ?></h2>
                    <button type="button" id="onoxia-sync-btn" class="button button-primary"
                            <?php echo empty($onoxia_site_uuid) ? 'disabled' : ''; ?>>
                        &#9654; <?php esc_html_e('Sync now', 'onoxia'); ?>
                    </button>
                </div>
                <p class="description" style="margin-top:8px;">
                    <?php esc_html_e('Manually sync all pages, llms.txt and sitemap to the ONOXIA knowledge base.', 'onoxia'); ?>
                </p>

                <div id="onoxia-sync-progress" style="display:none;margin-top:12px;">
                    <div style="background:#e5e7eb;border-radius:4px;height:20px;overflow:hidden;">
                        <div id="onoxia-sync-bar" style="background:#295F7C;height:100%;width:0%;transition:width 0.3s;border-radius:4px;text-align:center;color:#fff;font-size:12px;line-height:20px;">0%</div>
                    </div>
                </div>

                <div id="onoxia-sync-log" style="margin-top:12px;"></div>
            </div>
        </div>

        <div class="onoxia-col">
            <div class="onoxia-card">
                <h2><?php esc_html_e('Cron Job', 'onoxia'); ?></h2>
                <div class="onoxia-status-box" style="background:#eff6ff;border:1px solid #bfdbfe;color:#1e40af;margin-top:0;margin-bottom:12px;">
                    <strong>&#9432;</strong>
                    <?php esc_html_e('WordPress uses WP-Cron (triggered on page visits), which can be unreliable on low-traffic sites. For consistent daily syncs, set up a real server cron job.', 'onoxia'); ?>
                </div>
                <p><?php esc_html_e('Add this to your server crontab (runs daily at 03:00):', 'onoxia'); ?></p>
                <div style="display:flex;gap:4px;">
                    <input type="text" readonly class="regular-text" id="onoxia-cron-cmd" style="font-family:monospace;font-size:12px;flex:1;"
                           value="0 3 * * * curl -s '<?php echo esc_url(home_url('/wp-cron.php?doing_wp_cron')); ?>'">
                    <button type="button" id="onoxia-copy-cron" class="button" title="<?php esc_attr_e('Copy', 'onoxia'); ?>">&#128203;</button>
                </div>
                <p class="description" style="margin-top:8px;">
                    <?php esc_html_e('Optional: Disable WP-Cron and use server cron only. Add to wp-config.php:', 'onoxia'); ?>
                </p>
                <code style="display:block;padding:8px;background:#f8f8f8;border-radius:4px;font-size:12px;">define('DISABLE_WP_CRON', true);</code>
            </div>
        </div>
    </div>

    <!-- Settings Form -->
    <form method="post" action="options.php">
        <?php settings_fields('onoxia_settings'); ?>

        <!-- Connection -->
        <div class="onoxia-card">
            <h2><?php esc_html_e('API Token', 'onoxia'); ?></h2>
            <table class="form-table">
                <tr>
                    <th><label for="onoxia_api_token"><?php esc_html_e('API Token', 'onoxia'); ?></label></th>
                    <td>
                        <input type="password" name="onoxia_api_token" id="onoxia_api_token"
                               value="<?php echo esc_attr($onoxia_token); ?>"
                               class="regular-text" autocomplete="off">
                        <button type="button" id="onoxia-validate" class="button">
                            <?php esc_html_e('Test connection', 'onoxia'); ?>
                        </button>
                        <p class="description">
                            <?php
                            printf(
                                /* translators: %s: link to API token creation page */
                                esc_html__('Create a token at %s.', 'onoxia'),
                                '<a href="https://onoxia.nz/app/api-tokens" target="_blank">Administration &rarr; API Tokens</a>'
                            ); ?>
                        </p>
                        <div id="onoxia-status" style="margin-top:10px;"></div>
                    </td>
                </tr>
            </table>
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

        <?php if ($onoxia_has_woo) : ?>
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

        <!-- Privacy / Context -->
        <div class="onoxia-card">
            <h2><?php esc_html_e('Visitor Context', 'onoxia'); ?></h2>
            <p class="description"><?php esc_html_e('Control what visitor data is passed to the ONOXIA widget. All options are disabled by default for GDPR compliance.', 'onoxia'); ?></p>
            <table class="form-table">
                <tr>
                    <th><?php esc_html_e('Logged-in user context', 'onoxia'); ?></th>
                    <td>
                        <label>
                            <input type="checkbox" name="onoxia_user_context" value="1" <?php checked(get_option('onoxia_user_context')); ?>>
                            <?php esc_html_e('Pass display name and email of logged-in users to the widget', 'onoxia'); ?>
                        </label>
                        <p class="description"><?php esc_html_e('When enabled, logged-in visitors are identified by name in live chat. Their display name and email are transmitted to the ONOXIA service.', 'onoxia'); ?></p>
                    </td>
                </tr>
            </table>
        </div>

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
document.addEventListener('DOMContentLoaded', function() {
    // Test connection
    document.getElementById('onoxia-validate')?.addEventListener('click', function() {
        var token = document.getElementById('onoxia_api_token').value;
        var status = document.getElementById('onoxia-status');

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
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.success) {
                status.innerHTML = '<div class="notice notice-success inline"><p><strong>' + <?php echo wp_json_encode(esc_html__('Connected:', 'onoxia')); ?> + '</strong> ' +
                    data.data.name + ' (' + data.data.persona + ')</p></div>';
            } else {
                status.innerHTML = '<div class="notice notice-error inline"><p><strong>' + <?php echo wp_json_encode(esc_html__('Error:', 'onoxia')); ?> + '</strong> ' +
                    (data.data || <?php echo wp_json_encode(esc_html__('Unknown error', 'onoxia')); ?>) + '</p></div>';
            }
        })
        .catch(function() {
            status.innerHTML = '<div class="notice notice-error inline"><p>' + <?php echo wp_json_encode(esc_html__('Connection error.', 'onoxia')); ?> + '</p></div>';
        });
    });

    // Sync
    var syncBtn = document.getElementById('onoxia-sync-btn');
    var syncBar = document.getElementById('onoxia-sync-bar');
    var syncProgress = document.getElementById('onoxia-sync-progress');
    var syncLog = document.getElementById('onoxia-sync-log');

    var steps = [
        {action: 'onoxia_sync_articles', label: <?php echo wp_json_encode(esc_html__('Articles', 'onoxia')); ?>},
        {action: 'onoxia_sync_llms', label: <?php echo wp_json_encode(esc_html__('llms.txt', 'onoxia')); ?>},
        {action: 'onoxia_sync_sitemap', label: <?php echo wp_json_encode(esc_html__('Sitemap', 'onoxia')); ?>}
    ];

    if (syncBtn) {
        syncBtn.addEventListener('click', function() {
            syncBtn.disabled = true;
            syncLog.innerHTML = '';
            syncProgress.style.display = 'block';
            syncBar.style.width = '0%';
            syncBar.textContent = '0%';
            runStep(0);
        });
    }

    function runStep(i) {
        if (i >= steps.length) {
            syncBar.style.width = '100%';
            syncBar.textContent = '100%';
            syncBar.style.background = '#16a34a';
            syncBtn.disabled = false;
            return;
        }
        var pct = Math.round((i / steps.length) * 100);
        syncBar.style.width = pct + '%';
        syncBar.textContent = pct + '%';

        fetch(ajaxurl, {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: new URLSearchParams({
                action: steps[i].action,
                nonce: '<?php echo esc_js(wp_create_nonce('onoxia_nonce')); ?>'
            })
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            var icon = data.success ? '&#10003;' : '&#10007;';
            var color = data.success ? '#16a34a' : '#dc2626';
            if (data.data && data.data.skipped) { icon = '&#9654;'; color = '#6b7280'; }
            addSyncLog(icon, color, steps[i].label + ': ' + (data.data?.message || data.data || 'OK'));
            runStep(i + 1);
        })
        .catch(function(err) {
            addSyncLog('&#10007;', '#dc2626', steps[i].label + ': ' + err.message);
            runStep(i + 1);
        });
    }

    function addSyncLog(icon, color, text) {
        var div = document.createElement('div');
        div.style.marginBottom = '4px';
        div.innerHTML = '<span style="color:' + color + ';margin-right:6px;">' + icon + '</span>' + text;
        syncLog.appendChild(div);
    }

    // Copy cron
    document.getElementById('onoxia-copy-cron')?.addEventListener('click', function() {
        var input = document.getElementById('onoxia-cron-cmd');
        navigator.clipboard.writeText(input.value).then(function() {
            document.getElementById('onoxia-copy-cron').textContent = '\u2713';
            setTimeout(function() { document.getElementById('onoxia-copy-cron').textContent = '\uD83D\uDCCB'; }, 2000);
        });
    });
});
</script>
