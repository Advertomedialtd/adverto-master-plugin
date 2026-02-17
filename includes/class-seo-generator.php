<?php
/**
 * SEO Generator functionality
 * Handles AI-powered SEO title and meta description generation
 */

class Adverto_SEO_Generator {
    
    /**
     * Initialize the class
     */
    public function __construct() {
        // Constructor logic if needed
    }

    /**
     * Initialize admin hooks
     */
    public function init_admin_hooks($loader) {
        $loader->add_action('wp_ajax_adverto_generate_seo_content', $this, 'handle_generate_seo_content');
        $loader->add_action('wp_ajax_adverto_save_seo_content', $this, 'handle_save_seo_content');
        $loader->add_action('wp_ajax_adverto_fetch_pages', $this, 'handle_fetch_pages');
    }

    /**
     * Handle AJAX request to fetch pages
     */
    public function handle_fetch_pages() {
        check_ajax_referer('adverto_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(__('Insufficient permissions.', 'adverto-master'));
            return;
        }

        $args = array(
            'post_type' => 'page',
            'posts_per_page' => -1,
            'post_status' => 'publish',
            'orderby' => 'title',
            'order' => 'ASC',
        );

        $pages = get_posts($args);
        $page_list = array();

        foreach ($pages as $page) {
            $page_list[] = array(
                'id' => $page->ID,
                'title' => $page->post_title,
                'permalink' => get_permalink($page->ID)
            );
        }

        wp_send_json_success($page_list);
    }

    /**
     * Handle AJAX request to generate SEO content
     */
    public function handle_generate_seo_content() {
        check_ajax_referer('adverto_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(__('Insufficient permissions.', 'adverto-master'));
            return;
        }

        $page_id = intval($_POST['page_id']);
        $existing_content = sanitize_textarea_field($_POST['existing_content'] ?? '');
        
        if (!$page_id) {
            wp_send_json_error(__('Invalid page ID.', 'adverto-master'));
            return;
        }

        $page = get_post($page_id);
        if (!$page) {
            wp_send_json_error(__('Page not found.', 'adverto-master'));
            return;
        }

        // Get OpenAI API key from settings
        $settings = get_option('adverto_master_settings', array());
        $api_key = isset($settings['openai_api_key']) ? $settings['openai_api_key'] : '';
        
        // Debug logging
        error_log('Adverto SEO Generator: Settings retrieved: ' . print_r($settings, true));
        error_log('Adverto SEO Generator: API key length: ' . strlen($api_key));
        
        if (empty($api_key)) {
            error_log('Adverto SEO Generator: API key is empty');
            wp_send_json_error(__('OpenAI API key is not configured. Please check your settings.', 'adverto-master'));
            return;
        }

        try {
            $seo_content = $this->generate_seo_with_openai($page, $existing_content, $api_key);
            wp_send_json_success($seo_content);
        } catch (Exception $e) {
            wp_send_json_error(sprintf(__('Error generating SEO content: %s', 'adverto-master'), $e->getMessage()));
        }
    }

    /**
     * Generate SEO content using OpenAI API
     */
    private function generate_seo_with_openai($page, $existing_content, $api_key) {
        $page_content = wp_strip_all_tags($page->post_content);
        $page_title = $page->post_title;
        $page_excerpt = $page->post_excerpt;
        
        $prompt = "Analyse this WordPress page and generate SEO-optimised title and meta description:\n\n";
        $prompt .= "Page Title: {$page_title}\n";
        $prompt .= "Page Content: " . substr($page_content, 0, 1500) . "\n";
        if (!empty($page_excerpt)) {
            $prompt .= "Page Excerpt: {$page_excerpt}\n";
        }
        if (!empty($existing_content)) {
            $prompt .= "Current Content: {$existing_content}\n";
        }
        $prompt .= "\nPlease provide:\n";
        $prompt .= "1. An SEO-optimised title (50-60 characters)\n";
        $prompt .= "2. A compelling meta description (150-160 characters)\n";
        $prompt .= "3. 5-8 relevant keywords/phrases\n\n";
        $prompt .= "Format your response as JSON: {\"title\": \"...\", \"description\": \"...\", \"keywords\": [...]}";

        $response = wp_remote_post('https://api.openai.com/v1/chat/completions', array(
            'headers' => array(
                'Authorization' => 'Bearer ' . $api_key,
                'Content-Type' => 'application/json',
            ),
            'body' => json_encode(array(
                'model' => 'gpt-4o',
                'messages' => array(
                    array(
                        'role' => 'user',
                        'content' => $prompt
                    )
                ),
                'max_tokens' => 500,
                'temperature' => 0.7
            )),
            'timeout' => 30
        ));

        // Debug logging
        error_log('Adverto SEO Generator: API response code: ' . wp_remote_retrieve_response_code($response));
        
        if (is_wp_error($response)) {
            error_log('Adverto SEO Generator: WP Error: ' . $response->get_error_message());
            throw new Exception($response->get_error_message());
        }

        $response_code = wp_remote_retrieve_response_code($response);
        if ($response_code !== 200) {
            $error_body = wp_remote_retrieve_body($response);
            error_log('Adverto SEO Generator: API Error ' . $response_code . ': ' . $error_body);
            throw new Exception('API returned error code ' . $response_code . ': ' . $error_body);
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        // Debug logging
        error_log('Adverto SEO Generator: Response body length: ' . strlen($body));
        error_log('Adverto SEO Generator: JSON decode successful: ' . (is_array($data) ? 'yes' : 'no'));

        if (!isset($data['choices'][0]['message']['content'])) {
            error_log('Adverto SEO Generator: Invalid API response structure: ' . print_r($data, true));
            throw new Exception(__('Invalid response from OpenAI API.', 'adverto-master'));
        }

        $content = $data['choices'][0]['message']['content'];
        
        // Try to parse JSON response
        $seo_data = json_decode($content, true);
        if (!$seo_data) {
            // Fallback parsing if JSON is not perfect
            preg_match('/"title":\s*"([^"]+)"/', $content, $title_matches);
            preg_match('/"description":\s*"([^"]+)"/', $content, $desc_matches);
            preg_match('/"keywords":\s*\[([^\]]+)\]/', $content, $keywords_matches);
            
            $seo_data = array(
                'title' => isset($title_matches[1]) ? $title_matches[1] : $page->post_title,
                'description' => isset($desc_matches[1]) ? $desc_matches[1] : '',
                'keywords' => isset($keywords_matches[1]) ? 
                    array_map('trim', explode(',', str_replace('"', '', $keywords_matches[1]))) : 
                    array()
            );
        }

        return $seo_data;
    }

    /**
     * Handle AJAX request to save SEO content
     */
    public function handle_save_seo_content() {
        check_ajax_referer('adverto_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(__('Insufficient permissions.', 'adverto-master'));
            return;
        }

        $page_id = intval($_POST['page_id']);
        $seo_title = sanitize_text_field($_POST['seo_title']);
        $meta_description = sanitize_textarea_field($_POST['meta_description']);
        
        if (!$page_id) {
            wp_send_json_error(__('Invalid page ID.', 'adverto-master'));
            return;
        }

        // Save SEO title as meta field
        if (!empty($seo_title)) {
            update_post_meta($page_id, '_adverto_seo_title', $seo_title);
        }
        
        // Save meta description as meta field
        if (!empty($meta_description)) {
            update_post_meta($page_id, '_adverto_meta_description', $meta_description);
        }

        // If using Yoast SEO, also update their meta fields
        if (defined('WPSEO_VERSION')) {
            if (!empty($seo_title)) {
                update_post_meta($page_id, '_yoast_wpseo_title', $seo_title);
            }
            if (!empty($meta_description)) {
                update_post_meta($page_id, '_yoast_wpseo_metadesc', $meta_description);
            }
        }

        // If using RankMath, update their meta fields
        if (defined('RANK_MATH_VERSION')) {
            if (!empty($seo_title)) {
                update_post_meta($page_id, 'rank_math_title', $seo_title);
            }
            if (!empty($meta_description)) {
                update_post_meta($page_id, 'rank_math_description', $meta_description);
            }
        }

        wp_send_json_success(array(
            'message' => __('SEO content saved successfully!', 'adverto-master'),
            'page_id' => $page_id
        ));
    }
}
