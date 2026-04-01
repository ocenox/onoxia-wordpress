=== ONOXIA – AI Chatbot ===
Contributors: ocenox
Tags: chatbot, ai, live-chat, woocommerce, knowledge-base
Requires at least: 5.8
Tested up to: 6.9
Requires PHP: 7.4
Stable tag: 1.2.2
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

AI-powered chat widget with RAG knowledge base, live chat handover, GDPR compliance and auto-sync for pages, products and sitemaps.

== Description ==

ONOXIA is an AI chatbot widget that integrates seamlessly into your WordPress website. The bot answers visitor questions based on your knowledge base (RAG) — automatically synchronised from your pages, posts and WooCommerce products.

**Features:**

* AI chatbot with RAG knowledge base (Retrieval Augmented Generation)
* Auto-sync: pages and posts are automatically added to the bot knowledge base
* WooCommerce integration: product catalogue sync and product context on product pages
* llms.txt and sitemap import for instant website knowledge
* Live chat with handover to human support agents
* GDPR by design: no cookies, IP hashing, EU-based AI provider
* Voice input (speech-to-text)
* Shadow DOM: guaranteed no CSS conflicts with your theme
* Context-sensitive RAG with tags and URL filters
* Multilingual (15+ languages)

**What makes ONOXIA special:**

* Pages sync automatically — no manual copy-pasting into the knowledge base
* Bot knows your WooCommerce products with prices and categories
* Sitemap navigation: bot can direct visitors to the right page
* Crisis detection with country-specific emergency numbers
* Starting at 15 EUR/month (Starter plan)

= Third-Party Service =

This plugin connects to the **ONOXIA** service ([onoxia.nz](https://onoxia.nz)) operated by OCENOX LTD, New Zealand. The following data is transmitted:

* **Widget script** — A JavaScript file (`widget.js`) is loaded from ONOXIA servers (or a CDN URL provided by the API) to render the chat widget on your site.
* **API calls** — When you configure the plugin with an API token, it communicates with the ONOXIA API (`https://onoxia.nz/api/v1/bot`) to validate your token, retrieve your site ID and widget URL, and optionally sync page/post content, llms.txt and sitemap data.
* **Widget CDN** — The widget script URL may point to a CDN domain (e.g. `cdn.onoxia.nz`) instead of the main `onoxia.nz` domain. The URL is provided by the ONOXIA API and cached locally for 24 hours. The CDN domain is always operated by OCENOX LTD.
* **Visitor context (opt-in)** — When explicitly enabled in Settings → ONOXIA → Visitor Context, the display name and email of logged-in users are passed to the widget via a `data-context` attribute. For WooCommerce sites, product and cart information is included when the "Product context" option is enabled. Both options are **disabled by default**.

No data is transmitted until you enter an API token and save settings. The widget script is only loaded on the frontend when a valid site UUID exists. Visitor context data is never sent unless the administrator explicitly enables it.

* [ONOXIA Terms of Service](https://onoxia.nz/terms)
* [ONOXIA Privacy Policy](https://onoxia.nz/privacy)

== Installation ==

1. Install and activate the plugin.
2. Go to **Settings → ONOXIA** and enter your API token.
3. Create a token at [onoxia.nz/app/api-tokens](https://onoxia.nz/app/api-tokens).

The chat widget appears automatically on your website once connected.

== Frequently Asked Questions ==

= Do I need an ONOXIA account? =

Yes. Create a free account at [onoxia.nz](https://onoxia.nz) and set up your first chatbot.

= How much does ONOXIA cost? =

Starting at 15 EUR/month (Starter plan). 14-day free trial included.

= Is ONOXIA GDPR compliant? =

Yes. No cookies, IP hashing, EU-based AI provider (Mistral AI), configurable consent dialog.

= Does it work with WooCommerce? =

Yes. The plugin detects WooCommerce automatically and offers product sync and product context features.

= What external requests does this plugin make? =

The plugin loads a JavaScript widget from ONOXIA servers and makes API calls to sync content. See the "Third-Party Service" section in the description for full details.

== Privacy Policy ==

ONOXIA is designed with privacy in mind. Here is what data the plugin handles:

**Data transmitted to the ONOXIA service:**

* Page and post content (title, text, URL) — only when sync is enabled by the administrator
* llms.txt and sitemap URLs — only when the respective import options are enabled
* Logged-in user display name and email — only when "Visitor Context" is explicitly enabled (disabled by default)
* WooCommerce product details (name, price, category, SKU) and cart summary — only when "Product context" is enabled (disabled by default)

**Data NOT collected:**

* No cookies are set by the plugin or widget
* No visitor IP addresses are stored (IP hashing is applied server-side)
* No analytics or tracking data is collected

All data is processed by OCENOX LTD, New Zealand, using EU-based AI infrastructure (Mistral AI). For full details, see the [ONOXIA Privacy Policy](https://onoxia.nz/privacy).

== Screenshots ==

1. Settings page with token input and sync options
2. Chat widget on a sample page
3. WooCommerce integration with product context

== Changelog ==

= 1.2.2 =
* Visitor context (user name/email) is now opt-in (disabled by default) for GDPR compliance
* WooCommerce cart context is now opt-in (requires "Product context" setting)
* New "Visitor Context" settings section with clear data disclosure
* Added Privacy Policy section to readme
* Documented CDN widget URL in Third-Party Service section

= 1.2.1 =
* Dashboard with connection status, sync overview and cron info
* Manual sync button with progress bar and per-step log
* Auto-fetch site UUID on token save (no extra "Test connection" needed)
* Strip Laravel Sanctum "id|" prefix from pasted tokens
* Cron job info box with copy-to-clipboard command

= 1.2.0 =
* CDN support: widget URL from API response with 24h cache
* API timeout reduced to 5 seconds
* Automatic site info refresh (UUID, widget URL, site name)
* Fallback to default widget URL if API is unreachable

= 1.0.0 =
* Initial release
* Widget injection with API token authentication
* Auto-sync for pages, posts and WooCommerce products
* llms.txt and sitemap import
* Context tags and page restrictions
* WooCommerce product context (name, price, category, cart)

== Upgrade Notice ==

= 1.2.2 =
Privacy improvements: visitor context is now opt-in. Added Privacy Policy section.

= 1.2.1 =
Admin dashboard with sync panel, cron info, and improved token handling.

= 1.2.0 =
CDN support and improved caching. Widget URL is now fetched from the API and cached for 24 hours.

= 1.0.0 =
Initial release.
