<?php
/**
 * ONOXIA API Client
 *
 * @package Onoxia
 * @copyright 2026 OCENOX LTD
 */

defined('ABSPATH') || exit;

class Onoxia_Api {

    private $token;

    public function __construct($token = null) {
        $this->token = $token ?: get_option('onoxia_api_token', '');
    }

    /**
     * GET /site — validate token and get bot info.
     */
    public function get_site() {
        return $this->request('GET', '/site');
    }

    /**
     * GET /analytics/tokens — token budget info.
     */
    public function get_analytics() {
        return $this->request('GET', '/analytics/tokens');
    }

    /**
     * GET /rag — list RAG sources.
     */
    public function get_rag() {
        return $this->request('GET', '/rag');
    }

    /**
     * POST /rag/sync — bulk sync RAG sources.
     */
    public function sync_rag($sources, $delete_missing = true) {
        return $this->request('POST', '/rag/sync', [
            'sources'        => $sources,
            'delete_missing' => $delete_missing,
        ]);
    }

    /**
     * POST /ingest/llms-txt — import llms.txt.
     */
    public function ingest_llms($url) {
        return $this->request('POST', '/ingest/llms-txt', ['url' => $url]);
    }

    /**
     * POST /ingest/sitemap — import sitemap navigation.
     */
    public function ingest_sitemap($url) {
        return $this->request('POST', '/ingest/sitemap', ['url' => $url]);
    }

    /**
     * POST /webhooks — register a webhook.
     */
    public function create_webhook($data) {
        return $this->request('POST', '/webhooks', $data);
    }

    /**
     * Make an API request.
     */
    private function request($method, $endpoint, $body = null) {
        if (empty($this->token)) {
            return new WP_Error('no_token', __('No API token configured.', 'onoxia'));
        }

        $url  = ONOXIA_API_BASE . $endpoint;
        $args = [
            'timeout' => 30,
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token,
                'Accept'        => 'application/json',
                'Content-Type'  => 'application/json',
            ],
        ];

        if ($method === 'POST' && $body !== null) {
            $args['body'] = wp_json_encode($body);
            $response = wp_remote_post($url, $args);
        } else {
            $response = wp_remote_get($url, $args);
        }

        if (is_wp_error($response)) {
            return $response;
        }

        $code = wp_remote_retrieve_response_code($response);
        $data = json_decode(wp_remote_retrieve_body($response), true);

        if ($code >= 400) {
            return new WP_Error(
                'api_error',
                $data['error'] ?? "HTTP {$code}",
                ['status' => $code]
            );
        }

        return $data;
    }
}
