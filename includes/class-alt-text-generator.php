<?php
/**
 * Alt Text Generator functionality
 * Handles AI-powered alt text generation for images
 */

class Adverto_Alt_Text_Generator {
    
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
        $loader->add_action('wp_ajax_adverto_generate_alt_texts', $this, 'handle_generate_alt_texts');
        $loader->add_action('wp_ajax_adverto_save_alt_texts', $this, 'handle_save_alt_texts');
    }

    /**
     * Handle AJAX request to generate alt texts
     */
    public function handle_generate_alt_texts() {
        check_ajax_referer('adverto_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(__('Insufficient permissions.', 'adverto-master'));
            return;
        }

        $image_ids = isset($_POST['image_ids']) ? array_map('intval', $_POST['image_ids']) : array();
        
        if (empty($image_ids)) {
            wp_send_json_error(__('No images selected.', 'adverto-master'));
            return;
        }

        $settings = get_option('adverto_master_settings', array());
        $api_key = isset($settings['openai_api_key']) ? $settings['openai_api_key'] : '';
        
        if (empty($api_key)) {
            wp_send_json_error(__('OpenAI API key not configured. Please configure it in settings.', 'adverto-master'));
            return;
        }

        $results = array();
        $prompt = get_option('adverto_alt_text_prompt', 'Generate a descriptive alt text for this image for SEO purposes. Be concise but descriptive.');

        foreach ($image_ids as $image_id) {
            $image_url = wp_get_attachment_url($image_id);
            $current_alt = get_post_meta($image_id, '_wp_attachment_image_alt', true);
            
            if (!$image_url) {
                continue;
            }

            $generated_alt = $this->generate_alt_text_from_openai($image_url, $api_key, $prompt);
            
            $results[] = array(
                'id' => $image_id,
                'url' => $image_url,
                'current_alt' => $current_alt,
                'generated_alt' => $generated_alt
            );
        }

        if (empty($results)) {
            wp_send_json_error(__('No valid images found.', 'adverto-master'));
            return;
        }

        // Update statistics
        $current_count = get_option('adverto_alt_texts_generated', 0);
        update_option('adverto_alt_texts_generated', $current_count + count($results));

        // Log usage
        $this->log_usage('generate', count($results));

        wp_send_json_success($results);
    }

    /**
     * Handle AJAX request to save alt texts
     */
    public function handle_save_alt_texts() {
        check_ajax_referer('adverto_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(__('Insufficient permissions.', 'adverto-master'));
            return;
        }

        $alt_texts = isset($_POST['alt_texts']) ? (array)$_POST['alt_texts'] : array();
        
        if (empty($alt_texts)) {
            wp_send_json_error(__('No alt texts to save.', 'adverto-master'));
            return;
        }

        $saved_count = 0;
        
        foreach ($alt_texts as $item) {
            if (!isset($item['id']) || !isset($item['alt_text'])) {
                continue;
            }
            
            $image_id = intval($item['id']);
            $alt_text = sanitize_text_field($item['alt_text']);
            
            if (update_post_meta($image_id, '_wp_attachment_image_alt', $alt_text)) {
                $saved_count++;
            }
        }

        if ($saved_count === 0) {
            wp_send_json_error(__('Failed to save alt texts.', 'adverto-master'));
            return;
        }

        // Log usage
        $this->log_usage('save', $saved_count);

        wp_send_json_success(array(
            'saved_count' => $saved_count,
            'message' => sprintf(__('%d alt texts saved successfully.', 'adverto-master'), $saved_count)
        ));
    }

    /**
     * Generate alt text using OpenAI API
     */
    private function generate_alt_text_from_openai($image_url, $api_key, $prompt) {
        if (empty($api_key)) {
            return __('Error: No API key provided.', 'adverto-master');
        }

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
                        'content' => array(
                            array('type' => 'text', 'text' => $prompt),
                            array('type' => 'image_url', 'image_url' => array('url' => $image_url))
                        )
                    )
                ),
                'max_tokens' => 100,
                'temperature' => 0.7
            )),
            'timeout' => 30
        ));

        if (is_wp_error($response)) {
            error_log('Adverto Alt Text Generator - OpenAI API Error: ' . $response->get_error_message());
            return __('API Error: ', 'adverto-master') . $response->get_error_message();
        }

        $response_code = wp_remote_retrieve_response_code($response);
        $body = json_decode(wp_remote_retrieve_body($response), true);

        if ($response_code !== 200 || isset($body['error'])) {
            $error_message = isset($body['error']['message']) ? $body['error']['message'] : __('Unknown error', 'adverto-master');
            error_log('Adverto Alt Text Generator - OpenAI API Response Error: ' . $error_message);
            return __('API Error: ', 'adverto-master') . $error_message;
        }

        $generated_text = isset($body['choices'][0]['message']['content']) 
            ? trim($body['choices'][0]['message']['content']) 
            : __('Failed to generate alt text', 'adverto-master');

        // Clean up the generated text
        $generated_text = $this->clean_generated_text($generated_text);

        return $generated_text;
    }

    /**
     * Clean and format generated alt text
     */
    private function clean_generated_text($text) {
        // Remove common prefixes that AI might add
        $prefixes_to_remove = array(
            'Alt text: ',
            'Alt-text: ',
            'Image description: ',
            'Description: ',
            'This image shows ',
            'The image shows ',
            'Image: '
        );

        $text = str_ireplace($prefixes_to_remove, '', $text);
        
        // Remove quotes if they wrap the entire text
        $text = trim($text, '"\'');
        
        // Ensure it doesn't start with lowercase unless it's intentional
        $text = ucfirst($text);
        
        // Remove extra whitespace
        $text = preg_replace('/\s+/', ' ', $text);
        
        // Limit length (alt text should be under 125 characters for best SEO)
        if (strlen($text) > 125) {
            $text = substr($text, 0, 122) . '...';
        }

        return trim($text);
    }

    /**
     * Log usage statistics
     */
    private function log_usage($action_type, $count = 1) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'adverto_usage_stats';
        
        $wpdb->insert(
            $table_name,
            array(
                'tool_name' => 'alt-text',
                'action_type' => $action_type,
                'user_id' => get_current_user_id(),
                'metadata' => json_encode(array('count' => $count))
            ),
            array('%s', '%s', '%d', '%s')
        );
    }

    /**
     * Get usage statistics
     */
    public function get_usage_stats() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'adverto_usage_stats';
        
        $stats = $wpdb->get_results(
            "SELECT action_type, COUNT(*) as count, SUM(JSON_EXTRACT(metadata, '$.count')) as total_items
             FROM $table_name 
             WHERE tool_name = 'alt-text' 
             GROUP BY action_type"
        );

        $formatted_stats = array(
            'generate' => 0,
            'save' => 0,
            'total_generated' => 0,
            'total_saved' => 0
        );

        foreach ($stats as $stat) {
            $formatted_stats[$stat->action_type] = $stat->count;
            $formatted_stats['total_' . $stat->action_type . 'd'] = intval($stat->total_items);
        }

        return $formatted_stats;
    }

    /**
     * Bulk process images (for background processing)
     */
    public function bulk_process_images($image_ids, $api_key, $prompt) {
        $results = array();
        $batch_size = 5; // Process in batches to avoid timeout
        
        $batches = array_chunk($image_ids, $batch_size);
        
        foreach ($batches as $batch) {
            foreach ($batch as $image_id) {
                $image_url = wp_get_attachment_url($image_id);
                
                if (!$image_url) {
                    continue;
                }

                $generated_alt = $this->generate_alt_text_from_openai($image_url, $api_key, $prompt);
                
                $results[] = array(
                    'id' => $image_id,
                    'url' => $image_url,
                    'generated_alt' => $generated_alt
                );

                // Small delay to be respectful to the API
                usleep(100000); // 0.1 seconds
            }
        }

        return $results;
    }

    /**
     * Validate image for processing
     */
    private function validate_image($image_id) {
        // Check if image exists
        $image_url = wp_get_attachment_url($image_id);
        if (!$image_url) {
            return false;
        }

        // Check if it's actually an image
        $attachment = get_post($image_id);
        if (!$attachment || $attachment->post_type !== 'attachment') {
            return false;
        }

        $mime_type = get_post_mime_type($image_id);
        $allowed_types = array('image/jpeg', 'image/png', 'image/gif', 'image/webp');
        
        return in_array($mime_type, $allowed_types);
    }

    /**
     * Get recommended settings for alt text generation
     */
    public function get_recommended_settings() {
        return array(
            'max_length' => 125,
            'include_context' => true,
            'focus_on_content' => true,
            'avoid_redundancy' => true,
            'use_keywords_sparingly' => true
        );
    }
}
