<?php
/**
 * Duplicate Wizard functionality
 * Handles page duplication with find and replace
 */

class Adverto_Duplicate_Wizard {
    
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
        $loader->add_action('wp_ajax_adverto_duplicate_pages', $this, 'handle_duplicate_pages');
        $loader->add_action('wp_ajax_adverto_duplicate_page_with_replace', $this, 'handle_duplicate_page_with_replace');
        $loader->add_action('wp_ajax_adverto_scan_duplicates', $this, 'handle_scan_duplicates');
        $loader->add_action('wp_ajax_adverto_find_replace_content', $this, 'handle_find_replace_content');
        $loader->add_action('wp_ajax_adverto_get_page_content', $this, 'handle_get_page_content');
    }

    /**
     * Handle AJAX request to duplicate a page with find/replace for location-based SEO
     */
    public function handle_duplicate_page_with_replace() {
        check_ajax_referer('adverto_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(__('Insufficient permissions.', 'adverto-master'));
            return;
        }

        $page_id = intval($_POST['page_id'] ?? 0);
        $find_word = sanitize_text_field($_POST['find_word'] ?? '');
        $replace_word = sanitize_text_field($_POST['replace_word'] ?? '');
        $copy_yoast_seo = isset($_POST['copy_yoast_seo']) && $_POST['copy_yoast_seo'] === 'true';
        $copy_featured_image = isset($_POST['copy_featured_image']) && $_POST['copy_featured_image'] === 'true';
        $copy_custom_fields = isset($_POST['copy_custom_fields']) && $_POST['copy_custom_fields'] === 'true';

        if (!$page_id || !$find_word || !$replace_word) {
            wp_send_json_error(__('Missing required parameters.', 'adverto-master'));
            return;
        }

        try {
            $result = $this->duplicate_page_with_find_replace($page_id, $find_word, $replace_word, [
                'copy_yoast_seo' => $copy_yoast_seo,
                'copy_featured_image' => $copy_featured_image,
                'copy_custom_fields' => $copy_custom_fields
            ]);
            
            wp_send_json_success($result);
        } catch (Exception $e) {
            wp_send_json_error(sprintf(__('Error duplicating page: %s', 'adverto-master'), $e->getMessage()));
        }
    }

    /**
     * Handle AJAX request to scan for duplicate content
     */
    public function handle_scan_duplicates() {
        check_ajax_referer('adverto_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(__('Insufficient permissions.', 'adverto-master'));
            return;
        }

        $post_type = sanitize_text_field($_POST['post_type'] ?? 'page');
        $similarity_threshold = intval($_POST['similarity_threshold'] ?? 80);
        
        try {
            $duplicates = $this->find_duplicate_content($post_type, $similarity_threshold);
            wp_send_json_success($duplicates);
        } catch (Exception $e) {
            wp_send_json_error(sprintf(__('Error scanning for duplicates: %s', 'adverto-master'), $e->getMessage()));
        }
    }

    /**
     * Handle AJAX request to get page content
     */
    public function handle_get_page_content() {
        check_ajax_referer('adverto_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(__('Insufficient permissions.', 'adverto-master'));
            return;
        }

        $page_id = intval($_POST['page_id']);
        
        if (!$page_id) {
            wp_send_json_error(__('Invalid page ID.', 'adverto-master'));
            return;
        }

        $page = get_post($page_id);
        if (!$page) {
            wp_send_json_error(__('Page not found.', 'adverto-master'));
            return;
        }

        wp_send_json_success(array(
            'id' => $page->ID,
            'title' => $page->post_title,
            'content' => $page->post_content,
            'excerpt' => $page->post_excerpt,
            'permalink' => get_permalink($page->ID),
            'edit_link' => get_edit_post_link($page->ID),
            'word_count' => str_word_count(wp_strip_all_tags($page->post_content))
        ));
    }

    /**
     * Handle AJAX request to perform find and replace
     */
    public function handle_find_replace_content() {
        check_ajax_referer('adverto_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(__('Insufficient permissions.', 'adverto-master'));
            return;
        }

        $page_ids = array_map('intval', $_POST['page_ids'] ?? []);
        $find_text = $_POST['find_text'] ?? '';
        $replace_text = $_POST['replace_text'] ?? '';
        $target_fields = $_POST['target_fields'] ?? ['content'];
        $case_sensitive = isset($_POST['case_sensitive']) && $_POST['case_sensitive'] === 'true';
        
        if (empty($page_ids) || empty($find_text)) {
            wp_send_json_error(__('Missing required parameters.', 'adverto-master'));
            return;
        }

        try {
            $results = $this->perform_find_replace($page_ids, $find_text, $replace_text, $target_fields, $case_sensitive);
            wp_send_json_success($results);
        } catch (Exception $e) {
            wp_send_json_error(sprintf(__('Error performing find and replace: %s', 'adverto-master'), $e->getMessage()));
        }
    }

    /**
     * Handle AJAX request to duplicate pages
     */
    public function handle_duplicate_pages() {
        check_ajax_referer('adverto_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(__('Insufficient permissions.', 'adverto-master'));
            return;
        }

        $source_page_id = intval($_POST['source_page_id']);
        $new_title = sanitize_text_field($_POST['new_title']);
        $new_slug = sanitize_title($_POST['new_slug']);
        $copy_meta = isset($_POST['copy_meta']) && $_POST['copy_meta'] === 'true';
        
        if (!$source_page_id || !$new_title) {
            wp_send_json_error(__('Missing required parameters.', 'adverto-master'));
            return;
        }

        try {
            $new_page_id = $this->duplicate_page($source_page_id, $new_title, $new_slug, $copy_meta);
            wp_send_json_success(array(
                'new_page_id' => $new_page_id,
                'new_page_url' => get_permalink($new_page_id),
                'edit_url' => get_edit_post_link($new_page_id),
                'message' => __('Page duplicated successfully!', 'adverto-master')
            ));
        } catch (Exception $e) {
            wp_send_json_error(sprintf(__('Error duplicating page: %s', 'adverto-master'), $e->getMessage()));
        }
    }

    /**
     * Find duplicate content across posts/pages
     */
    private function find_duplicate_content($post_type = 'page', $similarity_threshold = 80) {
        $args = array(
            'post_type' => $post_type,
            'posts_per_page' => -1,
            'post_status' => 'publish',
            'orderby' => 'title',
            'order' => 'ASC',
        );

        $posts = get_posts($args);
        $duplicates = array();
        $processed = array();

        foreach ($posts as $i => $post1) {
            if (in_array($post1->ID, $processed)) {
                continue;
            }

            $content1 = wp_strip_all_tags($post1->post_content);
            $title1 = $post1->post_title;
            $group = array();

            for ($j = $i + 1; $j < count($posts); $j++) {
                $post2 = $posts[$j];
                if (in_array($post2->ID, $processed)) {
                    continue;
                }

                $content2 = wp_strip_all_tags($post2->post_content);
                $title2 = $post2->post_title;

                // Check content similarity
                $content_similarity = $this->calculate_similarity($content1, $content2);
                $title_similarity = $this->calculate_similarity($title1, $title2);

                if ($content_similarity >= $similarity_threshold || $title_similarity >= 90) {
                    if (empty($group)) {
                        $group[] = array(
                            'id' => $post1->ID,
                            'title' => $post1->post_title,
                            'permalink' => get_permalink($post1->ID),
                            'word_count' => str_word_count($content1),
                            'similarity' => 100
                        );
                    }

                    $group[] = array(
                        'id' => $post2->ID,
                        'title' => $post2->post_title,
                        'permalink' => get_permalink($post2->ID),
                        'word_count' => str_word_count($content2),
                        'similarity' => max($content_similarity, $title_similarity)
                    );

                    $processed[] = $post2->ID;
                }
            }

            if (!empty($group)) {
                $duplicates[] = $group;
                $processed[] = $post1->ID;
            }
        }

        return $duplicates;
    }

    /**
     * Calculate text similarity percentage
     */
    private function calculate_similarity($text1, $text2) {
        if (empty($text1) || empty($text2)) {
            return 0;
        }

        // Remove extra whitespace and convert to lowercase for comparison
        $text1 = strtolower(preg_replace('/\s+/', ' ', trim($text1)));
        $text2 = strtolower(preg_replace('/\s+/', ' ', trim($text2)));

        // Use Levenshtein distance for similarity calculation
        $len1 = strlen($text1);
        $len2 = strlen($text2);
        $maxLen = max($len1, $len2);

        if ($maxLen == 0) {
            return 100;
        }

        $distance = levenshtein($text1, $text2);
        $similarity = (1 - ($distance / $maxLen)) * 100;

        return max(0, $similarity);
    }

    /**
     * Perform find and replace operation
     */
    private function perform_find_replace($page_ids, $find_text, $replace_text, $target_fields, $case_sensitive) {
        $results = array();
        $search_flags = $case_sensitive ? '' : 'i';

        foreach ($page_ids as $page_id) {
            $page = get_post($page_id);
            if (!$page) {
                continue;
            }

            $changes = array();
            $page_data = array();

            foreach ($target_fields as $field) {
                switch ($field) {
                    case 'title':
                        $old_value = $page->post_title;
                        $new_value = $case_sensitive ? 
                            str_replace($find_text, $replace_text, $old_value) :
                            str_ireplace($find_text, $replace_text, $old_value);
                        
                        if ($old_value !== $new_value) {
                            $page_data['post_title'] = $new_value;
                            $changes['title'] = array(
                                'old' => $old_value,
                                'new' => $new_value,
                                'occurrences' => substr_count($case_sensitive ? $old_value : strtolower($old_value), $case_sensitive ? $find_text : strtolower($find_text))
                            );
                        }
                        break;

                    case 'content':
                        $old_value = $page->post_content;
                        $new_value = $case_sensitive ? 
                            str_replace($find_text, $replace_text, $old_value) :
                            str_ireplace($find_text, $replace_text, $old_value);
                        
                        if ($old_value !== $new_value) {
                            $page_data['post_content'] = $new_value;
                            $changes['content'] = array(
                                'old' => substr($old_value, 0, 200) . '...',
                                'new' => substr($new_value, 0, 200) . '...',
                                'occurrences' => substr_count($case_sensitive ? $old_value : strtolower($old_value), $case_sensitive ? $find_text : strtolower($find_text))
                            );
                        }
                        break;

                    case 'excerpt':
                        $old_value = $page->post_excerpt;
                        $new_value = $case_sensitive ? 
                            str_replace($find_text, $replace_text, $old_value) :
                            str_ireplace($find_text, $replace_text, $old_value);
                        
                        if ($old_value !== $new_value) {
                            $page_data['post_excerpt'] = $new_value;
                            $changes['excerpt'] = array(
                                'old' => $old_value,
                                'new' => $new_value,
                                'occurrences' => substr_count($case_sensitive ? $old_value : strtolower($old_value), $case_sensitive ? $find_text : strtolower($find_text))
                            );
                        }
                        break;
                }
            }

            if (!empty($page_data)) {
                $page_data['ID'] = $page_id;
                wp_update_post($page_data);

                $results[] = array(
                    'page_id' => $page_id,
                    'page_title' => $page->post_title,
                    'permalink' => get_permalink($page_id),
                    'changes' => $changes
                );
            }
        }

        return $results;
    }

    /**
     * Duplicate a page
     */
    private function duplicate_page($source_page_id, $new_title, $new_slug = '', $copy_meta = false) {
        $source_page = get_post($source_page_id);
        
        if (!$source_page) {
            throw new Exception(__('Source page not found.', 'adverto-master'));
        }

        // Prepare new page data
        $new_page_data = array(
            'post_title' => $new_title,
            'post_content' => $source_page->post_content,
            'post_excerpt' => $source_page->post_excerpt,
            'post_status' => 'draft', // Start as draft for safety
            'post_type' => $source_page->post_type,
            'post_author' => get_current_user_id(),
            'post_parent' => $source_page->post_parent,
            'menu_order' => $source_page->menu_order,
        );

        if (!empty($new_slug)) {
            $new_page_data['post_name'] = $new_slug;
        }

        // Create the new page
        $new_page_id = wp_insert_post($new_page_data);

        if (is_wp_error($new_page_id)) {
            throw new Exception($new_page_id->get_error_message());
        }

        // Copy meta data if requested
        if ($copy_meta) {
            $meta_data = get_post_meta($source_page_id);
            foreach ($meta_data as $key => $values) {
                // Skip some WordPress internal meta
                if (strpos($key, '_edit_') === 0 || strpos($key, '_wp_') === 0) {
                    continue;
                }
                
                foreach ($values as $value) {
                    add_post_meta($new_page_id, $key, maybe_unserialize($value));
                }
            }
        }

        return $new_page_id;
    }

    /**
     * Duplicate page with find and replace for location-based SEO
     */
    private function duplicate_page_with_find_replace($page_id, $find_word, $replace_word, $options = []) {
        $source_page = get_post($page_id);
        if (!$source_page) {
            throw new Exception(__('Source page not found.', 'adverto-master'));
        }

        // Apply find/replace to the title
        $new_title = str_replace($find_word, $replace_word, $source_page->post_title);
        $new_slug = sanitize_title($new_title);

        // Apply find/replace to the content
        $new_content = str_replace($find_word, $replace_word, $source_page->post_content);
        $new_excerpt = str_replace($find_word, $replace_word, $source_page->post_excerpt);

        // Create the new page
        $new_page_data = [
            'post_title'   => $new_title,
            'post_name'    => $new_slug,
            'post_content' => $new_content,
            'post_excerpt' => $new_excerpt,
            'post_status'  => 'draft', // Always create as draft for review
            'post_type'    => $source_page->post_type,
            'post_author'  => get_current_user_id(),
            'post_parent'  => $source_page->post_parent,
            'menu_order'   => $source_page->menu_order,
        ];

        $new_page_id = wp_insert_post($new_page_data);

        if (is_wp_error($new_page_id)) {
            throw new Exception(__('Failed to create new page.', 'adverto-master'));
        }

        // Copy featured image if option is enabled
        if (!empty($options['copy_featured_image'])) {
            $featured_image_id = get_post_thumbnail_id($page_id);
            if ($featured_image_id) {
                set_post_thumbnail($new_page_id, $featured_image_id);
            }
        }

        // Copy custom fields if option is enabled
        if (!empty($options['copy_custom_fields'])) {
            $meta_data = get_post_meta($page_id);
            foreach ($meta_data as $key => $values) {
                // Skip WordPress internal meta and some plugin meta
                if (strpos($key, '_edit_') === 0 || strpos($key, '_wp_') === 0) {
                    continue;
                }
                
                foreach ($values as $value) {
                    $unserialied_value = maybe_unserialize($value);
                    
                    // Apply find/replace to text values
                    if (is_string($unserialied_value)) {
                        $unserialied_value = str_replace($find_word, $replace_word, $unserialied_value);
                    }
                    
                    add_post_meta($new_page_id, $key, $unserialied_value);
                }
            }
        }

        // Copy Yoast SEO meta if option is enabled and Yoast is active
        if (!empty($options['copy_yoast_seo']) && class_exists('WPSEO_Options')) {
            $yoast_fields = [
                '_yoast_wpseo_title',
                '_yoast_wpseo_metadesc', 
                '_yoast_wpseo_focuskw',
                '_yoast_wpseo_meta-robots-noindex',
                '_yoast_wpseo_meta-robots-nofollow',
                '_yoast_wpseo_meta-robots-adv',
                '_yoast_wpseo_canonical',
                '_yoast_wpseo_redirect'
            ];

            foreach ($yoast_fields as $field) {
                $value = get_post_meta($page_id, $field, true);
                if (!empty($value)) {
                    // Apply find/replace to Yoast text fields
                    if (in_array($field, ['_yoast_wpseo_title', '_yoast_wpseo_metadesc', '_yoast_wpseo_focuskw']) && is_string($value)) {
                        $value = str_replace($find_word, $replace_word, $value);
                    }
                    
                    update_post_meta($new_page_id, $field, $value);
                }
            }
        }

        // Return result data
        return [
            'id' => $new_page_id,
            'title' => $new_title,
            'url' => get_permalink($new_page_id),
            'edit_url' => get_edit_post_link($new_page_id)
        ];
    }
}
