<?php
/**
 * LLMs.txt Generator Class
 *
 * This class handles the generation of LLMs.txt files for websites,
 * providing structured content for Large Language Models.
 *
 * @since      1.0.0
 * @package    Adverto_Master
 * @subpackage Adverto_Master/includes
 */

class Adverto_LLM_Generator {

    /**
     * The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     */
    public function __construct($plugin_name, $version) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Initialize public hooks
     */
    public function init_public_hooks($loader) {
        // Add rewrite rule for /llms.txt endpoint
        add_action('init', array($this, 'add_llm_txt_rewrite_rule'));
        add_action('template_redirect', array($this, 'serve_llm_txt'));
        add_filter('query_vars', array($this, 'add_query_vars'));
    }

    /**
     * Add rewrite rule for LLMs.txt endpoint
     */
    public function add_llm_txt_rewrite_rule() {
        add_rewrite_rule('^llms\.txt$', 'index.php?llm_txt=1', 'top');
    }

    /**
     * Add custom query vars
     */
    public function add_query_vars($vars) {
        $vars[] = 'llm_txt';
        return $vars;
    }

    /**
     * Serve LLMs.txt file
     */
    public function serve_llm_txt() {
        if (get_query_var('llm_txt')) {
            $llm_content = get_option('adverto_llm_txt_content', '');
            
            if (empty($llm_content)) {
                $llm_content = $this->generate_default_llm_txt();
            }
            
            header('Content-Type: text/plain; charset=utf-8');
            header('Cache-Control: public, max-age=3600');
            echo $llm_content;
            exit;
        }
    }

    /**
     * Scan WordPress content for LLMs.txt generation with comprehensive site crawling
     */
    public function scan_wordpress_content($options = array()) {
        $defaults = array(
            'include_pages' => true,
            'include_posts' => true,
            'include_products' => true,
            'include_custom_post_types' => false,
            'max_posts' => -1,
            'exclude_ids' => array(),
            'processing_type' => 'basic',
            'discover_all_pages' => true,  // New option for comprehensive discovery
            'max_crawl_depth' => 2,        // How deep to crawl
            'max_pages_to_crawl' => 100,   // Limit for safety
            'include_external_links' => false // Stay within the same domain
        );
        
        $options = wp_parse_args($options, $defaults);
        
        // Comprehensive page discovery
        if ($options['discover_all_pages']) {
            error_log('LLM Generator: Using comprehensive site discovery');
            $all_urls = $this->discover_all_site_pages($options);
            return $this->crawl_discovered_urls($all_urls, $options);
        } else {
            // Fallback to WordPress-only content
            error_log('LLM Generator: Using WordPress-only content discovery');
            return $this->get_wordpress_content_only($options);
        }
    }

    /**
     * Discover all pages on the site using multiple techniques
     */
    private function discover_all_site_pages($options) {
        error_log('LLM Generator: Starting comprehensive site discovery');

        $discovered_urls = array();
        $base_url = home_url();
        $site_domain = parse_url($base_url, PHP_URL_HOST);

        // Method 1: WordPress sitemap discovery
        error_log('LLM Generator: Checking sitemaps...');
        $sitemap_urls = $this->discover_from_sitemap($base_url);
        $discovered_urls = array_merge($discovered_urls, $sitemap_urls);
        error_log('LLM Generator: Found ' . count($sitemap_urls) . ' URLs from sitemaps');

        // Method 2: Crawl navigation menus and internal links
        error_log('LLM Generator: Crawling navigation and homepage...');
        $navigation_urls = $this->discover_from_navigation($base_url, $site_domain);
        $discovered_urls = array_merge($discovered_urls, $navigation_urls);
        error_log('LLM Generator: Found ' . count($navigation_urls) . ' URLs from navigation');

        // Method 3: WordPress database pages
        error_log('LLM Generator: Getting WordPress pages...');
        $wp_urls = $this->get_wordpress_page_urls($options);
        $discovered_urls = array_merge($discovered_urls, $wp_urls);
        error_log('LLM Generator: Found ' . count($wp_urls) . ' URLs from WordPress');

        // Method 4: Enhanced recursive link discovery (always run, not just when < 50)
        error_log('LLM Generator: Performing enhanced recursive link discovery...');
        $recursive_urls = $this->discover_recursive_links_enhanced($discovered_urls, $site_domain, $options);
        $discovered_urls = array_merge($discovered_urls, $recursive_urls);
        error_log('LLM Generator: Found ' . count($recursive_urls) . ' additional URLs from enhanced recursive crawling');

        // Method 5: Discover from additional sources
        error_log('LLM Generator: Checking additional sources...');
        $additional_urls = $this->discover_from_additional_sources($base_url, $site_domain);
        $discovered_urls = array_merge($discovered_urls, $additional_urls);
        error_log('LLM Generator: Found ' . count($additional_urls) . ' URLs from additional sources');

        // Clean and deduplicate URLs
        $discovered_urls = $this->clean_and_filter_urls($discovered_urls, $site_domain, $options);

        // Limit for safety and performance
        if (count($discovered_urls) > $options['max_pages_to_crawl']) {
            $discovered_urls = array_slice($discovered_urls, 0, $options['max_pages_to_crawl']);
            error_log('LLM Generator: Limited to ' . $options['max_pages_to_crawl'] . ' URLs for performance');
        }

        error_log('LLM Generator: Final discovered URLs count: ' . count($discovered_urls));
        return array_unique($discovered_urls);
    }

    /**
     * Crawl all discovered URLs
     */
    private function crawl_discovered_urls($urls, $options) {
        $content_data = array();
        $total_urls = count($urls);
        $processed = 0;
        
        error_log('LLM Generator: Starting to crawl ' . $total_urls . ' discovered URLs');
        
        // Process in batches to prevent timeouts
        $batch_size = $this->get_batch_size($options['processing_type']);
        $batches = array_chunk($urls, $batch_size);
        
        foreach ($batches as $batch_index => $batch) {
            error_log("LLM Generator: Processing URL batch " . ($batch_index + 1) . " of " . count($batches));
            
            foreach ($batch as $url) {
                // Check if we need to skip due to time constraints
                if ($this->should_skip_due_to_time($processed, $total_urls, $options['processing_type'])) {
                    error_log("LLM Generator: Skipping remaining URLs due to time constraints");
                    break 2; // Break out of both loops
                }
                
                // Crawl the live page content
                error_log("LLM Generator: Crawling: " . $url);
                $live_content = $this->crawl_page_content($url, $options['processing_type']);
                
                if (!empty($live_content['content']) && strlen($live_content['content']) > 100) {
                    // Extract title from content or URL
                    $title = $this->extract_page_title($live_content, $url);
                    
                    $content_data[] = array(
                        'id' => 'crawled_' . md5($url),
                        'title' => $title,
                        'content' => $live_content['content'],
                        'url' => $url,
                        'type' => $this->determine_page_type($url, $live_content),
                        'date' => current_time('mysql'),
                        'excerpt' => $live_content['excerpt'] ?? '',
                        'source' => 'site_crawl',
                        'ai_summary' => $live_content['ai_summary'] ?? null,
                        'word_count' => str_word_count($live_content['content']),
                        'crawl_metadata' => $live_content['metadata'] ?? array()
                    );
                }
                
                $processed++;
                
                // Brief pause between requests to be respectful
                usleep(250000); // 0.25 second pause
            }
            
            // Clear memory between batches
            if (function_exists('gc_collect_cycles')) {
                gc_collect_cycles();
            }
        }
        
        error_log("LLM Generator: Successfully crawled " . count($content_data) . " pages from comprehensive discovery");
        return $content_data;
    }

    /**
     * Get appropriate batch size based on processing complexity
     */
    private function get_batch_size($processing_type) {
        switch ($processing_type) {
            case 'basic':
                return 25; // Increased from 20 for better performance
            case 'ai_summaries':
                return 15; // Increased from 10 for better coverage
            case 'key_points':
                return 8;  // Increased from 5 for better coverage
            case 'structured':
                return 5;  // Increased from 3 for better coverage
            default:
                return 20;
        }
    }

    /**
     * Check if we should skip remaining content due to time constraints
     */
    private function should_skip_due_to_time($processed, $total, $processing_type) {
        $start_time = $_SERVER['REQUEST_TIME_FLOAT'] ?? microtime(true);
        $elapsed = microtime(true) - $start_time;

        // Increased time limits for better coverage
        $time_limits = array(
            'basic' => 180,      // 3 minutes (increased from 1.5)
            'ai_summaries' => 420,  // 7 minutes (increased from 4)
            'key_points' => 480,    // 8 minutes (increased from 5)
            'structured' => 600     // 10 minutes (increased from 7)
        );

        $time_limit = $time_limits[$processing_type] ?? 300;

        // Only skip if we're more than halfway through and time is running out
        if ($elapsed > $time_limit && $processed > ($total * 0.3)) {
            error_log("LLM Generator: Time limit reached ({$elapsed}s), skipping remaining content");
            return true;
        }

        return false;
    }

    /**
     * Enhanced page crawling with intelligent chunking and AI processing
     */
    private function crawl_page_content($url, $processing_type = 'basic') {
        // Make HTTP request to get the live page
        $response = wp_remote_get($url, array(
            'timeout' => 45,
            'headers' => array(
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36'
            )
        ));
        
        if (is_wp_error($response)) {
            error_log('LLM Generator: Failed to crawl ' . $url . ' - ' . $response->get_error_message());
            return array('content' => '', 'excerpt' => '', 'chunks' => array());
        }
        
        $html = wp_remote_retrieve_body($response);
        
        if (empty($html)) {
            error_log('LLM Generator: Empty response from ' . $url);
            return array('content' => '', 'excerpt' => '', 'chunks' => array());
        }
        
        // Extract and clean content using enhanced methods
        $extracted = $this->extract_meaningful_content($html);
        
        if (empty($extracted['content'])) {
            return array('content' => '', 'excerpt' => '', 'chunks' => array());
        }
        
        // For advanced processing types, use intelligent chunking
        if (in_array($processing_type, ['ai_summaries', 'key_points', 'structured'])) {
            return $this->process_content_with_intelligent_chunking($extracted['content'], $url, $processing_type);
        }
        
        // Basic processing - return simple extraction
        return array(
            'content' => $extracted['content'],
            'excerpt' => $extracted['excerpt'],
            'word_count' => $extracted['word_count'] ?? str_word_count($extracted['content']),
            'char_count' => $extracted['char_count'] ?? strlen($extracted['content'])
        );
    }

    /**
     * Process content with intelligent chunking and AI enhancement
     */
    private function process_content_with_intelligent_chunking($content, $url, $processing_type) {
        // Use intelligent chunking
        $chunks = $this->intelligent_chunk_text($content, 2500);
        
        if (empty($chunks)) {
            return array('content' => $content, 'excerpt' => substr($content, 0, 300), 'chunks' => array());
        }
        
        $processed_chunks = array();
        $enhanced_content_parts = array();
        
        foreach ($chunks as $chunk_number => $chunk) {
            // Generate metadata for this chunk
            $metadata = $this->generate_chunk_metadata($chunk, $url, $chunk_number, count($chunks));
            
            // Get AI-powered title and summary
            $ai_extracted = $this->extract_ai_title_and_summary($chunk, $url, $chunk_number);
            
            $processed_chunk = array(
                'chunk_number' => $chunk_number,
                'title' => $ai_extracted['title'],
                'summary' => $ai_extracted['summary'],
                'content' => $chunk,
                'metadata' => $metadata,
                'word_count' => str_word_count($chunk),
                'char_count' => strlen($chunk)
            );
            
            $processed_chunks[] = $processed_chunk;
            
            // Build enhanced content with AI summaries
            switch ($processing_type) {
                case 'ai_summaries':
                    $enhanced_content_parts[] = $ai_extracted['summary'];
                    break;
                case 'key_points':
                    $enhanced_content_parts[] = "• " . $ai_extracted['title'] . ": " . $ai_extracted['summary'];
                    break;
                case 'structured':
                    $enhanced_content_parts[] = "## " . $ai_extracted['title'] . "\n" . $ai_extracted['summary'];
                    break;
            }
            
            // Brief pause to respect API rate limits
            if ($processing_type !== 'basic') {
                usleep(500000); // 0.5 second pause
            }
        }
        
        // Combine enhanced content
        $enhanced_content = implode("\n\n", $enhanced_content_parts);
        $smart_excerpt = $this->create_smart_excerpt($enhanced_content, 400);
        
        return array(
            'content' => $enhanced_content,
            'excerpt' => $smart_excerpt,
            'original_content' => $content,
            'chunks' => $processed_chunks,
            'chunk_count' => count($processed_chunks),
            'total_words' => array_sum(array_column($processed_chunks, 'word_count')),
            'processing_type' => $processing_type,
            'ai_enhanced' => true
        );
    }

    /**
     * Enhanced intelligent text chunking inspired by crawl4AI
     * Respects code blocks, paragraphs, and sentences
     */
    private function intelligent_chunk_text($text, $chunk_size = 3000) {
        $chunks = array();
        $start = 0;
        $text_length = strlen($text);

        while ($start < $text_length) {
            $end = $start + $chunk_size;

            // If we're at the end of the text, take what's left
            if ($end >= $text_length) {
                $chunk = trim(substr($text, $start));
                if (!empty($chunk)) {
                    $chunks[] = $chunk;
                }
                break;
            }

            $current_chunk = substr($text, $start, $chunk_size);

            // Try to find a code block boundary first (```)
            $code_block_pos = strrpos($current_chunk, '```');
            if ($code_block_pos !== false && $code_block_pos > ($chunk_size * 0.3)) {
                $end = $start + $code_block_pos;
            }
            // Try to break at a paragraph (double newline)
            elseif (strpos($current_chunk, "\n\n") !== false) {
                $last_break = strrpos($current_chunk, "\n\n");
                if ($last_break > ($chunk_size * 0.3)) {
                    $end = $start + $last_break;
                }
            }
            // Try to break at a sentence
            elseif (strpos($current_chunk, '. ') !== false) {
                $last_period = strrpos($current_chunk, '. ');
                if ($last_period > ($chunk_size * 0.3)) {
                    $end = $start + $last_period + 1;
                }
            }
            // Try to break at any whitespace
            elseif (strpos($current_chunk, ' ') !== false) {
                $last_space = strrpos($current_chunk, ' ');
                if ($last_space > ($chunk_size * 0.5)) {
                    $end = $start + $last_space;
                }
            }

            $chunk = trim(substr($text, $start, $end - $start));
            if (!empty($chunk)) {
                $chunks[] = $chunk;
            }

            $start = max($start + 1, $end);
        }

        return $chunks;
    }

    /**
     * Enhanced AI-powered title and summary extraction
     * Uses structured JSON prompts like crawl4AI
     */
    private function extract_ai_title_and_summary($chunk, $url, $chunk_number) {
        // Skip AI processing if chunk is too short
        if (strlen($chunk) < 50) {
            return array(
                'title' => $this->generate_fallback_title($chunk, $url),
                'summary' => $this->generate_fallback_summary($chunk)
            );
        }

        $system_prompt = "You are an AI that extracts titles and summaries from website content chunks.
        Return a JSON object with 'title' and 'summary' keys.
        For the title: If this seems like the start of a document, extract its title. If it's a middle chunk, derive a descriptive title based on the main topic.
        For the summary: Create a concise 1-2 sentence summary of the main points in this chunk.
        Keep both title and summary concise but informative. Use British English spelling.";

        $user_prompt = "URL: {$url}\nChunk #{$chunk_number}\n\nContent:\n" . substr($chunk, 0, 1200) . "...";

        $prompt = "System: {$system_prompt}\n\nUser: {$user_prompt}\n\nRespond with valid JSON only:";

        $response = $this->call_openai_api($prompt, 'gpt-4o-mini');

        if ($response) {
            // Try to parse JSON response
            $decoded = json_decode($response, true);
            if (json_last_error() === JSON_ERROR_NONE && isset($decoded['title']) && isset($decoded['summary'])) {
                // Validate the response isn't empty
                if (!empty(trim($decoded['title'])) && !empty(trim($decoded['summary']))) {
                    return $decoded;
                }
            }
        }

        // Enhanced fallback if JSON parsing fails or response is empty
        error_log("LLM Generator: AI extraction failed for chunk {$chunk_number}, using enhanced fallback");
        return array(
            'title' => $this->generate_enhanced_fallback_title($chunk, $url, $chunk_number),
            'summary' => $this->generate_enhanced_fallback_summary($chunk)
        );
    }

    /**
     * Generate enhanced metadata for chunks (inspired by crawl4AI tracking)
     */
    private function generate_chunk_metadata($chunk, $url, $chunk_number, $total_chunks) {
        $metadata = array(
            'source_url' => $url,
            'chunk_position' => $chunk_number,
            'total_chunks' => $total_chunks,
            'word_count' => str_word_count($chunk),
            'character_count' => strlen($chunk),
            'has_code_blocks' => (strpos($chunk, '```') !== false || strpos($chunk, '<code>') !== false),
            'has_lists' => (strpos($chunk, '- ') !== false || strpos($chunk, '* ') !== false || preg_match('/\d+\.\s/', $chunk)),
            'has_headings' => (strpos($chunk, '#') !== false || preg_match('/<h[1-6]/', $chunk)),
            'paragraph_count' => substr_count($chunk, "\n\n"),
            'processing_timestamp' => current_time('mysql'),
            'content_hash' => md5($chunk), // For duplicate detection
            'url_path' => parse_url($url, PHP_URL_PATH),
            'domain' => parse_url($url, PHP_URL_HOST),
            'processing_version' => '2.1',
            'generator' => 'Adverto Media LLM Generator'
        );
        
        // Estimate content complexity for processing decisions
        $complexity_score = 0;
        if ($metadata['has_code_blocks']) $complexity_score += 3;
        if ($metadata['has_lists']) $complexity_score += 2;
        if ($metadata['has_headings']) $complexity_score += 1;
        if ($metadata['word_count'] > 1000) $complexity_score += 2;
        if ($metadata['paragraph_count'] > 5) $complexity_score += 1;
        
        $metadata['complexity_score'] = $complexity_score;
        $metadata['content_type'] = $this->classify_content_type($chunk);
        
        return $metadata;
    }

    /**
     * Classify content type for better processing
     */
    private function classify_content_type($content) {
        $code_density = (substr_count($content, '```') + substr_count($content, '<code>')) / max(1, strlen($content) / 100);
        $list_density = (substr_count($content, '- ') + substr_count($content, '* ')) / max(1, str_word_count($content) / 10);
        
        if ($code_density > 0.5) return 'technical';
        if ($list_density > 0.8) return 'list_heavy';
        if (strpos($content, 'function') !== false && strpos($content, 'return') !== false) return 'code';
        if (preg_match('/\b(tutorial|guide|how to|step by step)\b/i', $content)) return 'tutorial';
        if (preg_match('/\b(API|endpoint|parameter|response)\b/i', $content)) return 'documentation';
        
        return 'general';
    }

    /**
     * Create smart excerpt that preserves important information
     */
    private function create_smart_excerpt($content, $max_length = 400) {
        if (strlen($content) <= $max_length) {
            return $content;
        }
        
        // Try to find natural break points
        $sentences = preg_split('/(?<=[.!?])\s+/', $content);
        $excerpt = '';
        
        foreach ($sentences as $sentence) {
            if (strlen($excerpt . $sentence) <= $max_length - 3) {
                $excerpt .= $sentence . ' ';
            } else {
                break;
            }
        }
        
        // If we couldn't get enough content, fall back to word boundary
        if (strlen(trim($excerpt)) < $max_length / 2) {
            $excerpt = substr($content, 0, $max_length - 3);
            $last_space = strrpos($excerpt, ' ');
            if ($last_space !== false) {
                $excerpt = substr($excerpt, 0, $last_space);
            }
        }
        
        return trim($excerpt) . '...';
    }

    /**
     * Discover URLs from WordPress sitemaps
     */
    private function discover_from_sitemap($base_url) {
        $sitemap_urls = array();
        
        // Try common sitemap locations
        $sitemap_locations = array(
            '/sitemap.xml',
            '/sitemap_index.xml', 
            '/wp-sitemap.xml',
            '/sitemap-index.xml',
            '/sitemaps.xml'
        );
        
        foreach ($sitemap_locations as $sitemap_path) {
            $sitemap_url = rtrim($base_url, '/') . $sitemap_path;
            $urls = $this->parse_sitemap($sitemap_url);
            if (!empty($urls)) {
                $sitemap_urls = array_merge($sitemap_urls, $urls);
                error_log('LLM Generator: Found ' . count($urls) . ' URLs in ' . $sitemap_url);
                break; // Use first working sitemap
            }
        }
        
        return $sitemap_urls;
    }

    /**
     * Parse XML sitemap and extract URLs
     */
    private function parse_sitemap($sitemap_url) {
        $response = wp_remote_get($sitemap_url, array(
            'timeout' => 30,
            'user-agent' => 'WordPress/LLM-Generator'
        ));
        
        if (is_wp_error($response)) {
            return array();
        }
        
        $xml_content = wp_remote_retrieve_body($response);
        if (empty($xml_content)) {
            return array();
        }
        
        // Parse XML and extract URLs
        $urls = array();
        
        // Handle sitemap index files
        if (strpos($xml_content, '<sitemapindex') !== false) {
            preg_match_all('/<loc>(.*?)<\/loc>/i', $xml_content, $matches);
            foreach ($matches[1] as $nested_sitemap) {
                $nested_urls = $this->parse_sitemap($nested_sitemap);
                $urls = array_merge($urls, $nested_urls);
                if (count($urls) > 200) break; // Limit to prevent overload
            }
        } else {
            // Handle regular sitemap
            preg_match_all('/<loc>(.*?)<\/loc>/i', $xml_content, $matches);
            $urls = $matches[1];
        }
        
        return array_filter($urls);
    }

    /**
     * Discover URLs from navigation menus and homepage links
     */
    private function discover_from_navigation($base_url, $site_domain) {
        $discovered_urls = array();
        
        // Crawl homepage for links
        $homepage_links = $this->extract_internal_links($base_url, $site_domain);
        $discovered_urls = array_merge($discovered_urls, $homepage_links);
        
        // Get WordPress menu items
        $menu_urls = $this->get_wordpress_menu_urls();
        $discovered_urls = array_merge($discovered_urls, $menu_urls);
        
        return $discovered_urls;
    }

    /**
     * Extract internal links from a page
     */
    private function extract_internal_links($url, $site_domain) {
        $response = wp_remote_get($url, array(
            'timeout' => 30,
            'headers' => array(
                'User-Agent' => 'Mozilla/5.0 (compatible; WordPress LLM Generator)'
            )
        ));
        
        if (is_wp_error($response)) {
            return array();
        }
        
        $html = wp_remote_retrieve_body($response);
        $links = array();
        
        // Extract all href attributes
        preg_match_all('/<a[^>]+href=["\']([^"\']+)["\'][^>]*>/i', $html, $matches);
        
        foreach ($matches[1] as $link) {
            // Skip fragments, javascript, and mailtos
            if (strpos($link, '#') === 0 || strpos($link, 'javascript:') === 0 || strpos($link, 'mailto:') === 0) {
                continue;
            }
            
            // Convert relative URLs to absolute
            if (strpos($link, 'http') !== 0) {
                if (strpos($link, '/') === 0) {
                    $link = rtrim(home_url(), '/') . $link;
                } else {
                    $link = rtrim(dirname($url), '/') . '/' . $link;
                }
            }
            
            // Only include internal links
            $link_domain = parse_url($link, PHP_URL_HOST);
            if ($link_domain === $site_domain) {
                // Remove fragments and query params for cleaner URLs
                $link = strtok($link, '#');
                $link = strtok($link, '?');
                $links[] = $link;
            }
        }
        
        return array_unique($links);
    }

    /**
     * Get WordPress menu URLs
     */
    private function get_wordpress_menu_urls() {
        $menu_urls = array();
        
        $menus = wp_get_nav_menus();
        foreach ($menus as $menu) {
            $menu_items = wp_get_nav_menu_items($menu->term_id);
            if (!empty($menu_items)) {
                foreach ($menu_items as $item) {
                    if (!empty($item->url) && strpos($item->url, home_url()) === 0) {
                        $menu_urls[] = $item->url;
                    }
                }
            }
        }
        
        return $menu_urls;
    }

    /**
     * Get WordPress page URLs from database
     */
    private function get_wordpress_page_urls($options) {
        $post_types = array();
        if ($options['include_pages']) $post_types[] = 'page';
        if ($options['include_posts']) $post_types[] = 'post';
        if ($options['include_products'] && post_type_exists('product')) $post_types[] = 'product';
        
        if ($options['include_custom_post_types']) {
            $custom_post_types = get_post_types(array('public' => true, '_builtin' => false), 'names');
            $post_types = array_merge($post_types, $custom_post_types);
        }
        
        $args = array(
            'post_type' => $post_types,
            'post_status' => 'publish',
            'numberposts' => $options['max_posts'],
            'fields' => 'ids'
        );
        
        $post_ids = get_posts($args);
        $urls = array();
        
        foreach ($post_ids as $post_id) {
            $urls[] = get_permalink($post_id);
        }
        
        return $urls;
    }

    /**
     * Enhanced recursive link discovery with better coverage
     */
    private function discover_recursive_links_enhanced($initial_urls, $site_domain, $options) {
        $all_urls = $initial_urls;
        $max_depth = min($options['max_crawl_depth'] ?? 3, 4); // Cap at 4 for safety
        $max_urls_per_level = 50; // Limit URLs discovered per level

        for ($depth = 1; $depth < $max_depth; $depth++) {
            error_log("LLM Generator: Starting recursive discovery at depth {$depth}");

            $new_urls_this_level = array();
            $sample_urls = array_slice($all_urls, 0, min(20, count($all_urls))); // Sample from discovered URLs

            foreach ($sample_urls as $url) {
                $found_links = $this->extract_internal_links($url, $site_domain);

                // Filter out already discovered URLs
                $new_links = array_diff($found_links, $all_urls);

                // Limit per URL to prevent explosion
                if (count($new_links) > 10) {
                    $new_links = array_slice($new_links, 0, 10);
                }

                $new_urls_this_level = array_merge($new_urls_this_level, $new_links);

                // Safety check - don't discover too many at once
                if (count($new_urls_this_level) >= $max_urls_per_level) {
                    break;
                }
            }

            // Remove duplicates within this level
            $new_urls_this_level = array_unique($new_urls_this_level);

            error_log("LLM Generator: Found " . count($new_urls_this_level) . " new URLs at depth {$depth}");

            if (empty($new_urls_this_level)) {
                break; // No new URLs found, stop crawling
            }

            $all_urls = array_merge($all_urls, $new_urls_this_level);

            // Safety limit total URLs
            if (count($all_urls) >= ($options['max_pages_to_crawl'] ?? 100)) {
                break;
            }
        }

        // Return only the newly discovered URLs
        return array_diff($all_urls, $initial_urls);
    }

    /**
     * Discover URLs from additional sources
     */
    private function discover_from_additional_sources($base_url, $site_domain) {
        $additional_urls = array();

        // Method 1: Check for common page patterns
        $common_patterns = array(
            '/about',
            '/about-us',
            '/contact',
            '/contact-us',
            '/services',
            '/products',
            '/blog',
            '/news',
            '/portfolio',
            '/gallery',
            '/team',
            '/careers',
            '/faq',
            '/privacy',
            '/terms',
            '/sitemap'
        );

        foreach ($common_patterns as $pattern) {
            $test_url = rtrim($base_url, '/') . $pattern;
            $additional_urls[] = $test_url;
        }

        // Method 2: Check for category/tag archives if this is a blog
        if (function_exists('get_categories')) {
            $categories = get_categories(array('number' => 10));
            foreach ($categories as $category) {
                $additional_urls[] = get_category_link($category->term_id);
            }
        }

        if (function_exists('get_tags')) {
            $tags = get_tags(array('number' => 10));
            foreach ($tags as $tag) {
                $additional_urls[] = get_tag_link($tag->term_id);
            }
        }

        // Method 3: Check for author archives
        $authors = get_users(array('number' => 5, 'who' => 'authors'));
        foreach ($authors as $author) {
            $additional_urls[] = get_author_posts_url($author->ID);
        }

        // Method 4: Check for date archives
        $additional_urls[] = get_month_link(date('Y'), date('m'));

        // Method 5: Check for search page
        $additional_urls[] = home_url('/?s=test');

        // Filter and validate URLs
        $valid_urls = array();
        foreach ($additional_urls as $url) {
            if ($this->is_valid_internal_url($url, $site_domain)) {
                $valid_urls[] = $url;
            }
        }

        return array_unique($valid_urls);
    }

    /**
     * Validate if URL is a valid internal URL
     */
    private function is_valid_internal_url($url, $site_domain) {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return false;
        }

        $url_domain = parse_url($url, PHP_URL_HOST);
        if ($url_domain !== $site_domain) {
            return false;
        }

        // Skip common unwanted patterns
        $path = parse_url($url, PHP_URL_PATH);
        $skip_patterns = array(
            '/wp-admin',
            '/wp-content',
            '/wp-includes',
            '/feed',
            '/rss',
            '/xmlrpc',
            '/.well-known',
            '/wp-json'
        );

        foreach ($skip_patterns as $pattern) {
            if (strpos($path, $pattern) !== false) {
                return false;
            }
        }

        return true;
    }

    /**
     * Clean and filter URLs for processing
     */
    private function clean_and_filter_urls($urls, $site_domain, $options) {
        $cleaned_urls = array();
        
        foreach ($urls as $url) {
            // Skip if not a valid URL
            if (!filter_var($url, FILTER_VALIDATE_URL)) {
                continue;
            }
            
            // Check domain
            $url_domain = parse_url($url, PHP_URL_HOST);
            if ($url_domain !== $site_domain) {
                continue;
            }
            
            // Remove query parameters and fragments
            $url = strtok($url, '?');
            $url = strtok($url, '#');
            
            // Skip common unwanted paths
            $path = parse_url($url, PHP_URL_PATH);
            $skip_patterns = array(
                '/wp-admin',
                '/wp-content',
                '/wp-includes',
                '/feed',
                '/rss',
                '/xmlrpc',
                '/.well-known',
                '/wp-json'
            );
            
            $should_skip = false;
            foreach ($skip_patterns as $pattern) {
                if (strpos($path, $pattern) !== false) {
                    $should_skip = true;
                    break;
                }
            }
            
            if (!$should_skip) {
                $cleaned_urls[] = $url;
            }
        }
        
        return array_unique($cleaned_urls);
    }

    /**
     * Extract page title from content or derive from URL
     */
    private function extract_page_title($content, $url) {
        // Try to get title from content if it's AI-processed
        if (!empty($content['chunks'])) {
            foreach ($content['chunks'] as $chunk) {
                if (!empty($chunk['title']) && strlen($chunk['title']) > 3) {
                    return $chunk['title'];
                }
            }
        }
        
        // Try to extract from HTML title tag if available
        if (is_string($content['content'])) {
            if (preg_match('/<title[^>]*>(.*?)<\/title>/i', $content['content'], $matches)) {
                $title = trim(strip_tags($matches[1]));
                if (strlen($title) > 3) {
                    return $title;
                }
            }
        }
        
        // Fallback to URL-based title
        $path = parse_url($url, PHP_URL_PATH);
        $segments = explode('/', trim($path, '/'));
        $last_segment = end($segments);
        
        if (empty($last_segment)) {
            return 'Homepage';
        }
        
        // Clean up URL segment to make readable title
        $title = str_replace(array('-', '_'), ' ', $last_segment);
        $title = ucwords($title);
        
        return $title ?: 'Untitled Page';
    }

    /**
     * Determine page type based on URL and content
     */
    private function determine_page_type($url, $content) {
        $path = parse_url($url, PHP_URL_PATH);
        
        // Check URL patterns
        if (strpos($path, '/product') !== false) return 'product';
        if (strpos($path, '/service') !== false) return 'service'; 
        if (strpos($path, '/blog') !== false || strpos($path, '/news') !== false) return 'blog';
        if (strpos($path, '/about') !== false) return 'about';
        if (strpos($path, '/contact') !== false) return 'contact';
        if ($path === '/' || empty($path)) return 'homepage';
        
        // Check content metadata if available
        if (!empty($content['metadata']['content_type'])) {
            return $content['metadata']['content_type'];
        }
        
        return 'page';
    }

    /**
     * WordPress-only content fallback method
     */
    private function get_wordpress_content_only($options) {
        $post_types = array();
        if ($options['include_pages']) $post_types[] = 'page';
        if ($options['include_posts']) $post_types[] = 'post';
        if ($options['include_products'] && post_type_exists('product')) $post_types[] = 'product';
        
        if ($options['include_custom_post_types']) {
            $custom_post_types = get_post_types(array('public' => true, '_builtin' => false), 'names');
            $post_types = array_merge($post_types, $custom_post_types);
        }
        
        $args = array(
            'post_type' => $post_types,
            'post_status' => 'publish', 
            'numberposts' => $options['max_posts'],
            'orderby' => 'menu_order',
            'order' => 'ASC'
        );
        
        $posts = get_posts($args);
        $content_data = array();
        
        foreach ($posts as $post) {
            $url = get_permalink($post->ID);
            $live_content = $this->crawl_page_content($url, $options['processing_type']);
            
            if (!empty($live_content['content'])) {
                $content_data[] = array(
                    'id' => $post->ID,
                    'title' => $post->post_title,
                    'content' => $live_content['content'],
                    'url' => $url,
                    'type' => $post->post_type,
                    'date' => $post->post_date,
                    'excerpt' => $live_content['excerpt'],
                    'source' => 'wordpress_only'
                );
            }
        }
        
        return $content_data;
    }

    /**
     * Enhanced content extraction with intelligent chunking
     */
    private function extract_meaningful_content($html) {
        // Remove script and style tags
        $html = preg_replace('/<script[^>]*>.*?<\/script>/si', '', $html);
        $html = preg_replace('/<style[^>]*>.*?<\/style>/si', '', $html);
        $html = preg_replace('/<noscript[^>]*>.*?<\/noscript>/si', '', $html);
        
        // Remove comments
        $html = preg_replace('/<!--.*?-->/s', '', $html);
        
        // Enhanced content patterns with priority order
        $content_patterns = array(
            // WordPress specific (high priority)
            '/<article[^>]*>(.*?)<\/article>/si',
            '/<div[^>]*class="[^"]*entry-content[^"]*"[^>]*>(.*?)<\/div>/si',
            '/<div[^>]*class="[^"]*post-content[^"]*"[^>]*>(.*?)<\/div>/si',
            '/<div[^>]*class="[^"]*content-area[^"]*"[^>]*>(.*?)<\/div>/si',
            
            // HTML5 semantic elements (medium priority)
            '/<main[^>]*>(.*?)<\/main>/si',
            '/<section[^>]*class="[^"]*content[^"]*"[^>]*>(.*?)<\/section>/si',
            
            // Generic content containers (lower priority)
            '/<div[^>]*class="[^"]*content[^"]*"[^>]*>(.*?)<\/div>/si',
            '/<div[^>]*id="[^"]*content[^"]*"[^>]*>(.*?)<\/div>/si',
            '/<div[^>]*class="[^"]*container[^"]*"[^>]*>(.*?)<\/div>/si',
            
            // Fallback to body content
            '/<body[^>]*>(.*?)<\/body>/si'
        );
        
        $extracted_content = '';
        foreach ($content_patterns as $pattern) {
            if (preg_match($pattern, $html, $matches)) {
                $extracted_content = $matches[1];
                break;
            }
        }
        
        if (empty($extracted_content)) {
            $extracted_content = $html;
        }
        
        // Clean the extracted content with enhanced filtering
        return $this->clean_extracted_content_enhanced($extracted_content);
    }

    /**
     * Enhanced content cleaning inspired by crawl4AI techniques
     */
    private function clean_extracted_content_enhanced($content) {
        // Enhanced removal patterns
        $remove_patterns = array(
            // Navigation and UI elements
            '/<nav[^>]*>.*?<\/nav>/si',
            '/<footer[^>]*>.*?<\/footer>/si',
            '/<header[^>]*>.*?<\/header>/si',
            '/<aside[^>]*>.*?<\/aside>/si',
            
            // WordPress specific
            '/<div[^>]*class="[^"]*nav[^"]*"[^>]*>.*?<\/div>/si',
            '/<div[^>]*class="[^"]*menu[^"]*"[^>]*>.*?<\/div>/si',
            '/<div[^>]*class="[^"]*sidebar[^"]*"[^>]*>.*?<\/div>/si',
            '/<div[^>]*class="[^"]*footer[^"]*"[^>]*>.*?<\/div>/si',
            '/<div[^>]*class="[^"]*breadcrumb[^"]*"[^>]*>.*?<\/div>/si',
            '/<div[^>]*class="[^"]*widget[^"]*"[^>]*>.*?<\/div>/si',
            
            // Forms and interactive elements
            '/<form[^>]*>.*?<\/form>/si',
            '/<button[^>]*>.*?<\/button>/si',
            '/<input[^>]*\/?>/si',
            '/<select[^>]*>.*?<\/select>/si',
            '/<textarea[^>]*>.*?<\/textarea>/si',
            
            // Social and sharing widgets
            '/<div[^>]*class="[^"]*share[^"]*"[^>]*>.*?<\/div>/si',
            '/<div[^>]*class="[^"]*social[^"]*"[^>]*>.*?<\/div>/si',
            '/<div[^>]*class="[^"]*comment[^"]*"[^>]*>.*?<\/div>/si',
            
            // Advertisement containers
            '/<div[^>]*class="[^"]*ad[^"]*"[^>]*>.*?<\/div>/si',
            '/<div[^>]*class="[^"]*advertisement[^"]*"[^>]*>.*?<\/div>/si',
            
            // Skip links and screen reader elements
            '/<a[^>]*class="[^"]*skip[^"]*"[^>]*>.*?<\/a>/si',
            '/<div[^>]*class="[^"]*screen-reader[^"]*"[^>]*>.*?<\/div>/si',
        );
        
        foreach ($remove_patterns as $pattern) {
            $content = preg_replace($pattern, '', $content);
        }
        
        // Preserve code blocks before HTML removal
        $code_blocks = array();
        $content = preg_replace_callback('/<pre[^>]*>(.*?)<\/pre>/si', function($matches) use (&$code_blocks) {
            $placeholder = '___CODE_BLOCK_' . count($code_blocks) . '___';
            $code_blocks[$placeholder] = strip_tags($matches[1]);
            return $placeholder;
        }, $content);
        
        $content = preg_replace_callback('/<code[^>]*>(.*?)<\/code>/si', function($matches) use (&$code_blocks) {
            $placeholder = '___INLINE_CODE_' . count($code_blocks) . '___';
            $code_blocks[$placeholder] = '`' . strip_tags($matches[1]) . '`';
            return $placeholder;
        }, $content);
        
        // Convert lists to readable format before stripping HTML
        $content = preg_replace('/<\/li>/i', "</li>\n", $content);
        $content = preg_replace('/<li[^>]*>/i', "• ", $content);
        
        // Convert headings to markdown-style
        $content = preg_replace('/<\/h[1-6]>/i', "</h>\n\n", $content);
        $content = preg_replace('/<h1[^>]*>/i', "# ", $content);
        $content = preg_replace('/<h2[^>]*>/i', "## ", $content);
        $content = preg_replace('/<h3[^>]*>/i', "### ", $content);
        $content = preg_replace('/<h[4-6][^>]*>/i', "#### ", $content);
        
        // Convert paragraphs to preserve structure
        $content = preg_replace('/<\/p>/i', "</p>\n\n", $content);
        $content = preg_replace('/<p[^>]*>/i', "", $content);
        $content = preg_replace('/<br[^>]*>/i', "\n", $content);
        
        // Strip remaining HTML tags
        $content = strip_tags($content);
        
        // Restore code blocks
        foreach ($code_blocks as $placeholder => $code) {
            $content = str_replace($placeholder, $code, $content);
        }
        
        // Clean up whitespace intelligently
        $content = preg_replace('/\n\s*\n\s*\n/', "\n\n", $content); // Remove excessive line breaks
        $content = preg_replace('/[ \t]+/', ' ', $content); // Normalize spaces
        $content = trim($content);
        
        // Remove empty lines and very short lines
        $lines = explode("\n", $content);
        $cleaned_lines = array();
        
        foreach ($lines as $line) {
            $line = trim($line);
            if (!empty($line) && strlen($line) > 3) { // Skip very short lines
                // Skip lines that are likely navigation or boilerplate
                if (!preg_match('/^(home|about|contact|menu|search|login|register|\d+|\W+)$/i', $line)) {
                    $cleaned_lines[] = $line;
                }
            }
        }
        
        $text_content = implode("\n", $cleaned_lines);
        
        // Create intelligent excerpt
        $excerpt = $this->create_smart_excerpt($text_content, 300);
        
        return array(
            'content' => $text_content,
            'excerpt' => $excerpt,
            'word_count' => str_word_count($text_content),
            'char_count' => strlen($text_content)
        );
    }

    /**
     * Generate fallback title when AI extraction fails
     */
    private function generate_fallback_title($chunk, $url) {
        // Try to extract from first heading or sentence
        if (preg_match('/^#+\s*(.+?)(?:\n|$)/', $chunk, $matches)) {
            return trim($matches[1]);
        }
        
        // Extract from first sentence
        if (preg_match('/^([^.!?]+[.!?])/', $chunk, $matches)) {
            $title = trim($matches[1]);
            if (strlen($title) < 80) {
                return $title;
            }
        }
        
        // Use URL path as fallback
        $path = parse_url($url, PHP_URL_PATH);
        $segments = explode('/', trim($path, '/'));
        $last_segment = end($segments);
        
        return ucwords(str_replace(array('-', '_'), ' ', $last_segment)) ?: 'Content Section';
    }

    /**
     * Generate enhanced fallback title with better heuristics
     */
    private function generate_enhanced_fallback_title($chunk, $url, $chunk_number) {
        // Try to extract from headings first
        if (preg_match('/#{1,6}\s+(.+)/', $chunk, $matches)) {
            $title = trim($matches[1]);
            if (strlen($title) > 5 && strlen($title) < 100) {
                return $title;
            }
        }

        // Try to extract from first sentence
        $sentences = preg_split('/[.!?]+/', $chunk, -1, PREG_SPLIT_NO_EMPTY);
        if (!empty($sentences)) {
            $first_sentence = trim($sentences[0]);
            if (strlen($first_sentence) > 10 && strlen($first_sentence) < 80) {
                return ucfirst($first_sentence);
            }
        }

        // Try to extract meaningful words from the chunk
        $words = str_word_count($chunk, 1);
        $meaningful_words = array();

        foreach ($words as $word) {
            $word = trim($word);
            if (strlen($word) > 3 && !in_array(strtolower($word), $this->get_common_words())) {
                $meaningful_words[] = ucfirst($word);
                if (count($meaningful_words) >= 4) break;
            }
        }

        if (!empty($meaningful_words)) {
            return implode(' ', $meaningful_words);
        }

        // Final fallback using URL
        return $this->generate_fallback_title($chunk, $url);
    }

    /**
     * Generate enhanced fallback summary
     */
    private function generate_enhanced_fallback_summary($chunk) {
        // Extract first 2-3 meaningful sentences
        $sentences = preg_split('/[.!?]+/', $chunk, -1, PREG_SPLIT_NO_EMPTY);
        $meaningful_sentences = array();

        foreach ($sentences as $sentence) {
            $sentence = trim($sentence);
            if (strlen($sentence) > 10 && strlen($sentence) < 200) {
                $meaningful_sentences[] = $sentence;
                if (count($meaningful_sentences) >= 2) break;
            }
        }

        if (!empty($meaningful_sentences)) {
            return implode('. ', $meaningful_sentences) . '.';
        }

        // Fallback to original method
        return $this->generate_fallback_summary($chunk);
    }

    /**
     * Get common words to filter out
     */
    private function get_common_words() {
        return array(
            'the', 'and', 'for', 'are', 'but', 'not', 'you', 'all', 'can', 'had', 'her', 'was', 'one', 'our', 'out', 'day', 'get', 'has', 'him', 'his', 'how', 'its', 'may', 'new', 'now', 'old', 'see', 'two', 'way', 'who', 'boy', 'did', 'has', 'let', 'put', 'say', 'she', 'too', 'use'
        );
    }

    /**
     * Generate AI summary of crawled content
     */
    private function generate_ai_content_summary($content, $processing_type) {
        $content_sample = substr($content, 0, 1500); // Limit content for API call
        
        $prompt = "Analyze this webpage content and create a professional summary in British English:\n\n";
        $prompt .= $content_sample . "\n\n";
        
        switch ($processing_type) {
            case 'ai_summaries':
                $prompt .= "Create a 1-2 sentence professional summary describing what this page offers.";
                break;
            case 'key_points':
                $prompt .= "Extract 3-5 key points about what this page covers or offers.";
                break;
            case 'structured':
                $prompt .= "Provide a structured summary including: main purpose, key services/products, and target audience.";
                break;
        }
        
        $prompt .= " Use British spelling. Be concise and professional.";
        
        return $this->call_openai_api($prompt);
    }

    /**
     * Extract main content from post, excluding sidebars and navigation
     */
    /**
     * Get intelligent excerpt for post (now handled by web crawling)
     * This method is deprecated - excerpts are now generated during content crawling
     */
    private function get_smart_excerpt($post, $length = 160) {
        // This is now handled by the crawl_page_content method
        // which creates excerpts from live content rather than database content
        return get_the_excerpt($post->ID) ?: 'Content available via live crawling.';
    }

    /**
     * Get categories for post
     */
    private function get_post_categories($post) {
        if ($post->post_type === 'product' && function_exists('wc_get_product_terms')) {
            $terms = wc_get_product_terms($post->ID, 'product_cat');
            return wp_list_pluck($terms, 'name');
        }
        
        $categories = get_the_terms($post->ID, 'category');
        return $categories ? wp_list_pluck($categories, 'name') : array();
    }

    /**
     * Get tags for post
     */
    private function get_post_tags($post) {
        if ($post->post_type === 'product' && function_exists('wc_get_product_terms')) {
            $terms = wc_get_product_terms($post->ID, 'product_tag');
            return wp_list_pluck($terms, 'name');
        }
        
        $tags = get_the_terms($post->ID, 'post_tag');
        return $tags ? wp_list_pluck($tags, 'name') : array();
    }

    /**
     * Get relevant meta data for post
     */
    private function get_post_meta_data($post) {
        $meta = array();
        
        // SEO meta description
        $seo_desc = get_post_meta($post->ID, '_yoast_wpseo_metadesc', true);
        if (empty($seo_desc)) {
            $seo_desc = get_post_meta($post->ID, '_aioseop_description', true);
        }
        if (!empty($seo_desc)) {
            $meta['seo_description'] = $seo_desc;
        }
        
        // Custom fields that might be relevant
        $custom_fields = get_post_meta($post->ID);
        $relevant_fields = array('price', 'location', 'phone', 'email', 'address');
        
        foreach ($relevant_fields as $field) {
            if (isset($custom_fields[$field]) && !empty($custom_fields[$field][0])) {
                $meta[$field] = $custom_fields[$field][0];
            }
        }
        
        return $meta;
    }

    /**
     * Process content with AI to generate summaries
     */
    public function process_with_ai($content_data, $processing_type = 'summary') {
        $api_key = get_option('adverto_master_settings')['openai_api_key'] ?? '';
        
        if (empty($api_key)) {
            return new WP_Error('no_api_key', __('OpenAI API key not configured', 'adverto-master'));
        }
        
        $processed_data = array();
        
        foreach ($content_data as $item) {
            $processed_item = $item;
            
            switch ($processing_type) {
                case 'summary':
                    $processed_item['ai_summary'] = $this->generate_ai_summary($item, $api_key);
                    break;
                    
                case 'key_points':
                    $processed_item['ai_key_points'] = $this->generate_ai_key_points($item, $api_key);
                    break;
                    
                case 'structured':
                    $processed_item['ai_structured'] = $this->generate_ai_structured_data($item, $api_key);
                    break;
            }
            
            $processed_data[] = $processed_item;
            
            // Rate limiting - small delay between API calls
            usleep(100000); // 0.1 second delay
        }
        
        return $processed_data;
    }

    /**
     * Generate LLMs.txt file with given options
     */
    public function generate_llm_file($options = array()) {
        try {
            // Set execution time limit based on processing type
            $this->set_appropriate_time_limit($options['processing_type'] ?? 'basic');
            
            error_log('LLM Generator: Starting generation with options: ' . print_r($options, true));
            
            // Step 1: Scan content (25% progress)
            $this->update_progress('Scanning content...', 25);
            $content_data = $this->scan_wordpress_content($options);
            error_log('LLM Generator: Scanned content count: ' . count($content_data));
            
            if (empty($content_data)) {
                error_log('LLM Generator: No content found');
                return new WP_Error('no_content', __('No content found to process', 'adverto-master'));
            }

            // Step 2: AI Processing (50% progress) 
            $this->update_progress('Processing with AI...', 50);
            $llm_content = $this->generate_basic_llm_content($content_data, $options);
            error_log('LLM Generator: Generated content length: ' . strlen($llm_content));
            
            // Step 3: File Generation (75% progress)
            $this->update_progress('Generating file...', 75);
            
            // Get file path and ensure directory exists
            $upload_dir = wp_upload_dir();
            $file_path = $upload_dir['basedir'] . '/llms.txt';
            
            error_log('LLM Generator: Attempting to write to: ' . $file_path);
            
            // Ensure directory exists and is writable
            if (!file_exists($upload_dir['basedir'])) {
                wp_mkdir_p($upload_dir['basedir']);
            }
            
            // Step 4: Saving & Publishing (100% progress)
            $this->update_progress('Saving & publishing...', 100);
            
            // Write file
            $result = file_put_contents($file_path, $llm_content);
            
            if ($result === false) {
                error_log('LLM Generator: File write failed for: ' . $file_path);
                return new WP_Error('file_write_error', __('Could not write LLMs.txt file. Check file permissions.', 'adverto-master'));
            }

            error_log('LLM Generator: File written successfully. Size: ' . $result . ' bytes');
            
            // Update generation timestamp
            update_option('adverto_llm_generated', time());
            
            return $file_path;
            
        } catch (Exception $e) {
            error_log('LLM Generator Exception: ' . $e->getMessage());
            error_log('LLM Generator Stack trace: ' . $e->getTraceAsString());
            return new WP_Error('generation_error', 'Generation failed: ' . $e->getMessage());
        } catch (Error $e) {
            error_log('LLM Generator Fatal Error: ' . $e->getMessage());
            error_log('LLM Generator Fatal Stack trace: ' . $e->getTraceAsString());
            return new WP_Error('fatal_error', 'Fatal error occurred: ' . $e->getMessage());
        }
    }

    /**
     * Set appropriate time limit based on processing complexity
     */
    private function set_appropriate_time_limit($processing_type) {
        // Remove existing time limit
        if (function_exists('set_time_limit')) {
            switch ($processing_type) {
                case 'basic':
                    set_time_limit(120); // 2 minutes
                    break;
                case 'ai_summaries':
                    set_time_limit(300); // 5 minutes
                    break;
                case 'key_points':
                    set_time_limit(360); // 6 minutes  
                    break;
                case 'structured':
                    set_time_limit(480); // 8 minutes
                    break;
                default:
                    set_time_limit(180); // 3 minutes default
            }
        }
        
        // Also increase memory limit for complex processing
        if (in_array($processing_type, ['key_points', 'structured'])) {
            ini_set('memory_limit', '512M');
        } else {
            ini_set('memory_limit', '256M');
        }
    }

    /**
     * Update progress for frontend tracking
     */
    private function update_progress($status, $percent) {
        // Store progress in transient for potential AJAX polling
        set_transient('adverto_llm_progress', array(
            'status' => $status,
            'percent' => $percent,
            'time' => current_time('mysql')
        ), 300); // 5 minutes expiry
        
        error_log("LLM Generator Progress: $percent% - $status");
    }

    /**
     * Generate basic LLM content without AI processing
     */
    private function generate_basic_llm_content($content_data, $options) {
        $site_name = get_bloginfo('name');
        $site_description = get_bloginfo('description');
        $site_url = home_url();
        $admin_email = get_option('admin_email');
        $processing_type = $options['processing_type'] ?? 'basic';
        
        // Clean content data from shortcodes and theme elements
        $content_data = $this->clean_content_data($content_data);
        
        // Start with Adverto Media branding header
        $content = "# {$site_name} | Powered by Adverto Media\n\n";
        
        // Enhanced description with context
        if (!empty($site_description)) {
            $content .= "{$site_description}\n\n";
        } else {
            $content .= "Welcome to {$site_name} - your trusted partner for quality services and solutions.\n\n";
        }
        
        // Add Adverto Media introduction
        $content .= "*This LLMs.txt file is generated by the award-winning team at [Adverto Media](https://advertomedia.co.uk/about-us/), a leading digital marketing agency based in Gloucester, England. We specialise in WordPress development, SEO optimisation, and intelligent content generation solutions.*\n\n";
        
        $content .= "---\n\n";
        
        // About Us section
        $content .= "## About Us\n";
        $content .= "[Learn More About {$site_name}]({$site_url}/about)\n\n";
        
        $about_text = $this->generate_intelligent_about_section($content_data, $processing_type);
        $content .= $about_text . "\n\n";
        
        $content .= "---\n\n";
        
        // Services/Products section based on content
        $services = $this->extract_services_from_content($content_data, $processing_type);
        if (!empty($services)) {
            $content .= "## Our Services\n";
            $content .= "[View All Services]({$site_url}/services)\n\n";
            
            foreach ($services as $service) {
                $content .= "- [{$service['title']}]({$service['url']}) – {$service['description']}\n";
            }
            $content .= "\n---\n\n";
        }
        
        // Key pages section with AI processing
        $key_pages = $this->extract_key_pages($content_data, $processing_type);
        if (!empty($key_pages)) {
            $content .= "## Key Pages & Information\n";
            $content .= "[Explore Our Website]({$site_url})\n\n";
            
            foreach ($key_pages as $page) {
                $content .= "- [{$page['title']}]({$page['url']}) – {$page['summary']}\n";
            }
            $content .= "\n---\n\n";
        }
        
        // Recent content/blog posts with AI processing
        $recent_posts = $this->extract_recent_posts($content_data, $processing_type, 5);
        if (!empty($recent_posts)) {
            $content .= "## Latest Content & Updates\n";
            $content .= "[View All Articles]({$site_url}/blog)\n\n";
            
            foreach ($recent_posts as $post) {
                $content .= "- [{$post['title']}]({$post['url']}) – {$post['summary']}\n";
            }
            $content .= "\n---\n\n";
        }
        
        // Contact section
        $content .= "## Contact Methods\n";
        $content .= "- [Contact Us]({$site_url}/contact) – Get in touch with our team to discuss your needs and receive personalised recommendations\n";
        
        if (!empty($admin_email)) {
            $content .= "- **Email:** {$admin_email}\n";
        }
        
        $content .= "- [Visit Our Website]({$site_url}) – Explore our full range of services and solutions\n\n";
        
        // Check for contact page content
        $contact_info = $this->extract_contact_details($content_data);
        if (!empty($contact_info)) {
            if (!empty($contact_info['phone'])) {
                $content .= "**Phone:** {$contact_info['phone']}\n";
            }
            if (!empty($contact_info['address'])) {
                $content .= "**Address:** {$contact_info['address']}\n";
            }
        }
        
        $content .= "\n---\n\n";
        
        // Trust signals with AI enhancement
        $trust_signals = $this->extract_trust_signals($content_data, $processing_type);
        if (!empty($trust_signals)) {
            $content .= "## Why Choose {$site_name}\n";
            foreach ($trust_signals as $signal) {
                $content .= "- {$signal}\n";
            }
            $content .= "\n---\n\n";
        }
        
        // Add Adverto Media footer branding
        $content .= "## About Adverto Media\n\n";
        $content .= "This LLMs.txt file was intelligently generated using advanced AI technology by **[Adverto Media](https://advertomedia.co.uk/about-us/)**, an award-winning digital marketing agency based in Gloucester, England.\n\n";
        
        $content .= "### Our Expertise\n";
        $content .= "- **WordPress Development** – Custom WordPress solutions and plugin development\n";
        $content .= "- **SEO Optimisation** – Search engine optimisation and AI-driven content strategies\n";
        $content .= "- **Digital Marketing** – Comprehensive digital marketing services across the UK\n";
        $content .= "- **AI Solutions** – Cutting-edge artificial intelligence integration for businesses\n";
        $content .= "- **Content Generation** – Intelligent, automated content creation systems\n\n";
        
        $content .= "**Contact Adverto Media:**\n";
        $content .= "- Website: [https://advertomedia.co.uk](https://advertomedia.co.uk)\n";
        $content .= "- About Us: [Learn More About Adverto Media](https://advertomedia.co.uk/about-us/)\n";
        $content .= "- Location: Gloucester, England\n\n";
        
        $content .= "---\n\n";
        
        // Final site footer
        $content .= "{$site_name} is your trusted partner for quality services and solutions.\n";
        $content .= "Visit us at {$site_url} to discover how we can help you achieve your goals.\n\n";
        
        $processing_label = $this->get_processing_type_label($processing_type);
        $content .= "*Generated on " . date('j F Y') . " using {$processing_label} | Powered by [Adverto Media](https://advertomedia.co.uk) - Award-Winning Digital Marketing Agency*\n";
        
        return $content;
    }

    /**
     * Generate LLMs.txt file content
     */
    public function generate_llm_txt($processed_data, $options = array()) {
        $site_name = get_bloginfo('name');
        $site_url = home_url();
        $site_description = get_bloginfo('description');
        $current_date = date('Y-m-d H:i:s');
        
        $llm_content = "# {$site_name}\n";
        $llm_content .= "## Site: {$site_url}\n";
        $llm_content .= "## Generated: {$current_date}\n";
        if (!empty($site_description)) {
            $llm_content .= "## Description: {$site_description}\n";
        }
        $llm_content .= "\n";
        
        // Add business information
        $llm_content .= $this->generate_business_info_section();
        
        // Add navigation structure
        $llm_content .= $this->generate_navigation_section();
        
        // Add content sections
        $llm_content .= $this->generate_content_sections($processed_data, $options);
        
        // Add footer information
        $llm_content .= $this->generate_footer_section();
        
        return $llm_content;
    }

    /**
     * Generate business information section
     */
    private function generate_business_info_section() {
        $content = "# Business Information\n";
        
        // Try to extract business info from various sources
        $business_info = array();
        
        // From site title and tagline
        $business_info['name'] = get_bloginfo('name');
        $tagline = get_bloginfo('description');
        if (!empty($tagline)) {
            $business_info['tagline'] = $tagline;
        }
        
        // From admin email
        $admin_email = get_option('admin_email');
        if (!empty($admin_email)) {
            $business_info['email'] = $admin_email;
        }
        
        // From WordPress address
        $wp_url = get_option('siteurl');
        $home_url = get_option('home');
        if ($home_url !== $wp_url) {
            $business_info['website'] = $home_url;
        }
        
        foreach ($business_info as $key => $value) {
            $content .= "- " . ucfirst($key) . ": {$value}\n";
        }
        
        $content .= "\n";
        
        return $content;
    }

    /**
     * Generate navigation structure section
     */
    private function generate_navigation_section() {
        $content = "# Site Navigation\n";
        
        // Get primary menu
        $locations = get_nav_menu_locations();
        if (isset($locations['primary'])) {
            $menu = wp_get_nav_menu_object($locations['primary']);
            if ($menu) {
                $menu_items = wp_get_nav_menu_items($menu->term_id);
                if ($menu_items) {
                    foreach ($menu_items as $item) {
                        if ($item->menu_item_parent == 0) { // Top level items only
                            $content .= "- {$item->title}: {$item->url}\n";
                        }
                    }
                }
            }
        }
        
        $content .= "\n";
        
        return $content;
    }

    /**
     * Generate content sections
     */
    private function generate_content_sections($processed_data, $options) {
        $content = "";
        
        // Group content by type
        $grouped_content = array();
        foreach ($processed_data as $item) {
            $grouped_content[$item['type']][] = $item;
        }
        
        // Generate sections for each content type
        foreach ($grouped_content as $post_type => $items) {
            $section_title = $this->get_section_title($post_type);
            $content .= "# {$section_title}\n\n";
            
            foreach ($items as $item) {
                $content .= "## {$item['title']}\n";
                $content .= "URL: {$item['url']}\n";
                
                if (isset($item['ai_summary']) && !empty($item['ai_summary'])) {
                    $content .= "Summary: {$item['ai_summary']}\n";
                } elseif (!empty($item['excerpt'])) {
                    $content .= "Summary: {$item['excerpt']}\n";
                }
                
                if (!empty($item['categories'])) {
                    $content .= "Categories: " . implode(', ', $item['categories']) . "\n";
                }
                
                if (isset($item['ai_key_points']) && !empty($item['ai_key_points'])) {
                    $content .= "Key Points: {$item['ai_key_points']}\n";
                }
                
                if (!empty($item['meta'])) {
                    foreach ($item['meta'] as $meta_key => $meta_value) {
                        $content .= ucfirst(str_replace('_', ' ', $meta_key)) . ": {$meta_value}\n";
                    }
                }
                
                $content .= "\n";
            }
        }
        
        return $content;
    }

    /**
     * Get section title for post type
     */
    private function get_section_title($post_type) {
        $titles = array(
            'page' => 'Website Pages',
            'post' => 'Blog Posts',
            'product' => 'Products & Services',
            'event' => 'Events',
            'testimonial' => 'Testimonials',
            'portfolio' => 'Portfolio Items'
        );
        
        if (isset($titles[$post_type])) {
            return $titles[$post_type];
        }
        
        $post_type_object = get_post_type_object($post_type);
        return $post_type_object ? $post_type_object->labels->name : ucfirst($post_type);
    }

    /**
     * Generate footer section
     */
    private function generate_footer_section() {
        $content = "# Additional Information\n";
        $content .= "- WordPress Version: " . get_bloginfo('version') . "\n";
        $content .= "- Theme: " . wp_get_theme()->get('Name') . "\n";
        $content .= "- Generated by: Adverto Master Plugin\n";
        $content .= "- Last Updated: " . date('Y-m-d H:i:s') . "\n\n";
        
        $content .= "---\n";
        $content .= "This LLMs.txt file was automatically generated to provide structured information about this website's content for Large Language Models and AI applications.\n";
        
        return $content;
    }

    /**
     * Generate default LLMs.txt if none exists
     */
    private function generate_default_llm_txt() {
        $content_data = $this->scan_wordpress_content(array(
            'include_pages' => true,
            'include_posts' => true,
            'include_products' => false,
            'max_posts' => 20
        ));
        
        return $this->generate_llm_txt($content_data);
    }

    /**
     * Save LLMs.txt content to database
     */
    public function save_llm_txt($content) {
        update_option('adverto_llm_txt_content', $content);
        update_option('adverto_llm_txt_generated', time());
        
        // Flush rewrite rules to ensure endpoint works
        flush_rewrite_rules();
        
        return true;
    }

    /**
     * Get the file path for LLMs.txt
     */
    private function get_file_path() {
        $upload_dir = wp_upload_dir();
        return $upload_dir['basedir'] . '/llms.txt';
    }

    /**
     * Get statistics about the current LLMs.txt
     */
    public function get_llm_stats() {
        $file_path = $this->get_file_path();
        $exists = file_exists($file_path);
        $content = $exists ? file_get_contents($file_path) : '';
        $generated_time = get_option('adverto_llm_generated', 0);
        
        $stats = array(
            'exists' => $exists,
            'size' => $exists ? filesize($file_path) : 0,
            'word_count' => $exists ? str_word_count(strip_tags($content)) : 0,
            'generated' => $generated_time,
            'age_days' => $generated_time ? floor((time() - $generated_time) / DAY_IN_SECONDS) : 0,
            'endpoint_url' => home_url('llms.txt'),
            'file_path' => $file_path
        );
        
        // Count sections and pages
        if (!empty($content)) {
            $stats['sections'] = substr_count($content, '# ');
            $stats['pages'] = substr_count($content, '## ') - $stats['sections'];
        } else {
            $stats['sections'] = 0;
            $stats['pages'] = 0;
        }
        
        return $stats;
    }

    /**
     * Initialize admin-specific hooks
     */
    public function init_admin_hooks($loader) {
        $loader->add_action('wp_ajax_adverto_generate_llm_txt', $this, 'ajax_generate_llm_txt');
        $loader->add_action('wp_ajax_adverto_download_llm_txt', $this, 'ajax_download_llm_txt');
        $loader->add_action('wp_ajax_adverto_get_llm_stats', $this, 'ajax_get_llm_stats');
        $loader->add_action('wp_ajax_adverto_test_llm_ajax', $this, 'ajax_test_handler');
        
        // Hook to serve public LLMs.txt file
        $loader->add_action('template_redirect', $this, 'handle_public_llm_request');
    }

    /**
     * Test AJAX handler
     */
    public function ajax_test_handler() {
        error_log('LLM Generator: Test AJAX handler called');
        
        // Check if nonce is valid
        if (!wp_verify_nonce($_POST['nonce'] ?? '', 'adverto_generate_llm')) {
            error_log('LLM Generator: Test AJAX nonce failed');
            wp_send_json_error('Nonce verification failed');
            return;
        }
        
        error_log('LLM Generator: Test AJAX successful');
        wp_send_json_success(array(
            'message' => 'AJAX is working correctly!',
            'time' => current_time('mysql'),
            'user' => wp_get_current_user()->user_login,
            'plugin_version' => $this->version,
            'wp_version' => get_bloginfo('version')
        ));
    }

    /**
     * Handle AJAX request to generate LLMs.txt file
     */
    public function ajax_generate_llm_txt() {
        // Add debugging
        error_log('LLM Generator AJAX called');
        error_log('POST data: ' . print_r($_POST, true));
        
        // Verify nonce
        if (!wp_verify_nonce($_POST['nonce'] ?? '', 'adverto_generate_llm')) {
            error_log('Nonce verification failed');
            wp_send_json_error(__('Security check failed', 'adverto-master'));
            return;
        }

        // Check permissions
        if (!current_user_can('manage_options')) {
            error_log('Permission check failed');
            wp_send_json_error(__('Insufficient permissions', 'adverto-master'));
            return;
        }

        $options = array(
            'include_pages' => !empty($_POST['include_pages']),
            'include_posts' => !empty($_POST['include_posts']),
            'include_products' => !empty($_POST['include_products']),
            'include_custom_post_types' => !empty($_POST['include_custom_post_types']),
            'discover_all_pages' => !empty($_POST['discover_all_pages']),
            'max_pages_to_crawl' => intval($_POST['max_pages_to_crawl'] ?? 100),
            'max_crawl_depth' => intval($_POST['max_crawl_depth'] ?? 2),
            'processing_type' => sanitize_text_field($_POST['processing_type'] ?? 'summary'),
            'max_posts' => intval($_POST['max_posts'] ?? -1)
        );

        error_log('Options: ' . print_r($options, true));

        try {
            $result = $this->generate_llm_file($options);

            if (is_wp_error($result)) {
                error_log('Generate error: ' . $result->get_error_message());
                wp_send_json_error($result->get_error_message());
            } else {
                error_log('Generation successful');
                wp_send_json_success(array(
                    'message' => __('LLMs.txt file generated successfully!', 'adverto-master'),
                    'stats' => $this->get_llm_stats()
                ));
            }
        } catch (Exception $e) {
            error_log('LLM Generator Exception: ' . $e->getMessage());
            wp_send_json_error(__('An unexpected error occurred. Please try again.', 'adverto-master'));
        }
    }

    /**
     * Handle AJAX request to download LLMs.txt file
     */
    public function ajax_download_llm_txt() {
        $file_path = $this->get_file_path();
        
        if (!file_exists($file_path)) {
            wp_send_json_error(__('LLMs.txt file not found', 'adverto-master'));
            return;
        }

        header('Content-Type: text/plain');
        header('Content-Disposition: attachment; filename="llms.txt"');
        header('Content-Length: ' . filesize($file_path));
        
        readfile($file_path);
        exit;
    }

    /**
     * Handle AJAX request to get current statistics
     */
    public function ajax_get_llm_stats() {
        wp_send_json_success($this->get_llm_stats());
    }

    /**
     * Handle public requests for LLMs.txt file
     */
    public function handle_public_llm_request() {
        global $wp;
        
        // Check if this is a request for /llms.txt
        $request = $wp->request ?? '';
        
        if ($request === 'llms.txt' || $request === 'llms.txt/') {
            $file_path = $this->get_file_path();
            
            if (file_exists($file_path)) {
                // Set appropriate headers
                header('Content-Type: text/plain; charset=utf-8');
                header('Cache-Control: public, max-age=3600'); // Cache for 1 hour
                header('Content-Length: ' . filesize($file_path));
                
                // Output the file content
                readfile($file_path);
                exit;
            } else {
                // File doesn't exist, send 404
                status_header(404);
                header('Content-Type: text/plain; charset=utf-8');
                echo 'LLMs.txt file not found. Please generate it first from the WordPress admin area.';
                exit;
            }
        }
    }

    /**
     * Extract services from content analysis with AI enhancement
     */
    private function extract_services_from_content($content_data, $processing_type = 'basic', $limit = 10) {
        $services = [];
        
        // Look for service-related pages
        foreach ($content_data as $item) {
            if (empty($item['title']) || empty($item['url'])) continue;
            
            $title = $item['title'];
            $is_service = false;
            
            // Check if this looks like a service page
            $service_keywords = [
                'service', 'services', 'solution', 'solutions', 'consulting', 'consultancy',
                'design', 'development', 'marketing', 'seo', 'ppc', 'web', 'digital',
                'management', 'strategy', 'training', 'support', 'maintenance',
                'installation', 'repair', 'build', 'create', 'custom'
            ];
            
            foreach ($service_keywords as $keyword) {
                if (stripos($title, $keyword) !== false) {
                    $is_service = true;
                    break;
                }
            }
            
            // Also check URL for service indicators
            if (!$is_service && (
                stripos($item['url'], '/service') !== false ||
                stripos($item['url'], '/solution') !== false ||
                stripos($item['url'], '/what-we-do') !== false
            )) {
                $is_service = true;
            }
            
            if ($is_service) {
                // Use AI for enhanced description if available
                if (in_array($processing_type, ['ai_summaries', 'key_points', 'structured']) && !empty($item['content'])) {
                    $ai_description = $this->generate_ai_service_description($item);
                    $description = $ai_description ?: $this->generate_fallback_description($item);
                } else {
                    $description = $this->generate_fallback_description($item);
                }
                
                $services[] = [
                    'title' => $title,
                    'url' => $item['url'],
                    'description' => $description
                ];
                
                if (count($services) >= $limit) break;
            }
        }
        
        return $services;
    }

    /**
     * Generate AI-powered service description
     */
    private function generate_ai_service_description($item) {
        $content = isset($item['content']) ? substr(strip_tags($item['content']), 0, 800) : '';
        $excerpt = isset($item['excerpt']) ? strip_tags($item['excerpt']) : '';
        
        if (empty($content) && empty($excerpt)) {
            return false;
        }
        
        $prompt = "Based on this service page content, create a concise, professional description in British English (1 sentence, max 150 characters):

Title: {$item['title']}
Content: {$content} {$excerpt}

Create a description that explains what this service offers. Use British spelling. No quotes or formatting.";

        return $this->call_openai_api($prompt);
    }

    /**
     * Generate fallback description for services
     */
    private function generate_fallback_description($item) {
        $description = isset($item['excerpt']) && !empty($item['excerpt']) 
            ? strip_tags($item['excerpt'])
            : "Professional " . strtolower($item['title']) . " services tailored to your needs";
        
        return substr($description, 0, 150) . (strlen($description) > 150 ? '...' : '');
    }

    /**
     * Extract key pages from content with AI enhancement
     */
    private function extract_key_pages($content_data, $processing_type = 'basic', $limit = 8) {
        $key_pages = [];
        $important_pages = [
            'about', 'contact', 'home', 'services', 'products', 'portfolio', 
            'testimonials', 'case studies', 'privacy', 'terms'
        ];
        
        foreach ($content_data as $item) {
            if (empty($item['title']) || empty($item['url'])) continue;
            
            $title = strtolower($item['title']);
            $url = strtolower($item['url']);
            
            $is_important = false;
            foreach ($important_pages as $important) {
                if (stripos($title, $important) !== false || stripos($url, $important) !== false) {
                    $is_important = true;
                    break;
                }
            }
            
            if ($is_important) {
                // Use AI for enhanced summary if available
                if (in_array($processing_type, ['key_points', 'structured']) && !empty($item['content'])) {
                    $ai_summary = $this->generate_ai_page_summary($item);
                    $summary = $ai_summary ?: $this->generate_fallback_page_summary($item);
                } else {
                    $summary = $this->generate_fallback_page_summary($item);
                }
                
                $key_pages[] = [
                    'title' => $item['title'],
                    'url' => $item['url'],
                    'summary' => $summary
                ];
                
                if (count($key_pages) >= $limit) break;
            }
        }
        
        return $key_pages;
    }

    /**
     * Generate AI-powered page summary
     */
    private function generate_ai_page_summary($item) {
        $content = isset($item['content']) ? substr(strip_tags($item['content']), 0, 600) : '';
        $excerpt = isset($item['excerpt']) ? strip_tags($item['excerpt']) : '';
        
        if (empty($content) && empty($excerpt)) {
            return false;
        }
        
        $prompt = "Summarise this webpage in British English (1 sentence, max 100 characters):

Title: {$item['title']}
Content: {$content} {$excerpt}

Create a brief description of what visitors will find on this page. Use British spelling. No quotes.";

        return $this->call_openai_api($prompt);
    }

    /**
     * Generate fallback page summary
     */
    private function generate_fallback_page_summary($item) {
        $summary = isset($item['excerpt']) && !empty($item['excerpt'])
            ? strip_tags($item['excerpt'])
            : "Important information about our " . strtolower($item['title']);
        
        return substr($summary, 0, 100) . (strlen($summary) > 100 ? '...' : '');
    }

    /**
     * Extract recent posts from content with AI enhancement
     */
    private function extract_recent_posts($content_data, $processing_type = 'basic', $limit = 5) {
        $recent_posts = [];
        
        foreach ($content_data as $item) {
            if (empty($item['title']) || empty($item['url'])) continue;
            
            // Prefer posts over pages
            if (isset($item['type']) && $item['type'] === 'post') {
                // Use AI for enhanced summary if available
                if (in_array($processing_type, ['ai_summaries', 'key_points', 'structured']) && !empty($item['content'])) {
                    $ai_summary = $this->generate_ai_post_summary($item);
                    $summary = $ai_summary ?: $this->generate_fallback_post_summary($item);
                } else {
                    $summary = $this->generate_fallback_post_summary($item);
                }
                
                $recent_posts[] = [
                    'title' => $item['title'],
                    'url' => $item['url'],
                    'summary' => $summary
                ];
                
                if (count($recent_posts) >= $limit) break;
            }
        }
        
        return $recent_posts;
    }

    /**
     * Generate AI-powered post summary
     */
    private function generate_ai_post_summary($item) {
        $content = isset($item['content']) ? substr(strip_tags($item['content']), 0, 800) : '';
        $excerpt = isset($item['excerpt']) ? strip_tags($item['excerpt']) : '';
        
        if (empty($content) && empty($excerpt)) {
            return false;
        }
        
        $prompt = "Create a compelling summary for this blog post in British English (1 sentence, max 120 characters):

Title: {$item['title']}
Content: {$content} {$excerpt}

Summarise the key insights or value this article provides. Use British spelling. No quotes.";

        return $this->call_openai_api($prompt);
    }

    /**
     * Generate fallback post summary
     */
    private function generate_fallback_post_summary($item) {
        $summary = isset($item['excerpt']) && !empty($item['excerpt'])
            ? strip_tags($item['excerpt'])
            : "Latest insights and updates from our team";
        
        return substr($summary, 0, 120) . (strlen($summary) > 120 ? '...' : '');
    }

    /**
     * Extract trust signals with AI enhancement
     */
    private function extract_trust_signals($content_data, $processing_type = 'basic') {
        $trust_signals = [];
        
        // Use AI for structured analysis
        if ($processing_type === 'structured') {
            $ai_trust_signals = $this->generate_ai_trust_signals($content_data);
            if (!empty($ai_trust_signals)) {
                return $ai_trust_signals;
            }
        }
        
        // Fallback to basic extraction
        $signal_keywords = [
            'award', 'certified', 'accredited', 'years experience', 'established',
            'trusted', 'professional', 'expert', 'specialist', 'qualified',
            'testimonial', 'review', 'client', 'customer', 'satisfaction'
        ];
        
        foreach ($content_data as $item) {
            $content = strtolower($item['title'] . ' ' . ($item['excerpt'] ?? ''));
            
            foreach ($signal_keywords as $keyword) {
                if (stripos($content, $keyword) !== false) {
                    $trust_signals[] = "Experienced and " . ucfirst($keyword) . " professionals";
                    break;
                }
            }
            
            if (count($trust_signals) >= 3) break;
        }
        
        // Add default trust signals if none found
        if (empty($trust_signals)) {
            $trust_signals = [
                "Professional and reliable service",
                "Committed to customer satisfaction",
                "Quality assured solutions"
            ];
        }
        
        return array_unique($trust_signals);
    }

    /**
     * Generate AI-powered trust signals
     */
    private function generate_ai_trust_signals($content_data) {
        $content_summary = $this->prepare_content_for_ai($content_data, 1000);
        
        $prompt = "Based on this website content, identify 3-4 key trust signals or competitive advantages in British English:

Content: {$content_summary}

List what makes this business trustworthy or competitive. Each point should be 1 short phrase (max 50 characters). Format as bullet points without bullets, one per line. Use British spelling.";

        $response = $this->call_openai_api($prompt);
        
        if ($response) {
            $signals = array_filter(array_map('trim', explode("\n", $response)));
            return array_slice($signals, 0, 4); // Limit to 4 signals
        }
        
        return [];
    }

    /**
     * Prepare content for AI analysis
     */
    private function prepare_content_for_ai($content_data, $max_chars = 1500) {
        $combined_content = '';
        
        foreach ($content_data as $item) {
            $text = $item['title'] . '. ';
            if (!empty($item['excerpt'])) {
                $text .= strip_tags($item['excerpt']) . ' ';
            } elseif (!empty($item['content'])) {
                $text .= substr(strip_tags($item['content']), 0, 200) . ' ';
            }
            
            $combined_content .= $text;
            
            if (strlen($combined_content) > $max_chars) {
                break;
            }
        }
        
        return substr($combined_content, 0, $max_chars);
    }

    /**
     * Extract contact details from content
     */
    private function extract_contact_details($content_data) {
        $contact_info = [];
        
        foreach ($content_data as $item) {
            if (stripos($item['title'], 'contact') !== false && !empty($item['content'])) {
                $content = strip_tags($item['content']);
                
                // Extract phone numbers
                if (preg_match('/(\+?\d{1,4}[\s\-\(\)]?\d{3,4}[\s\-\(\)]?\d{3,4}[\s\-]?\d{3,4})/', $content, $matches)) {
                    $contact_info['phone'] = trim($matches[0]);
                }
                
                // Extract addresses (basic pattern)
                if (preg_match('/(\d+\s+[A-Za-z\s,]+(?:Street|St|Road|Rd|Lane|Avenue|Ave|Drive|Close|Way)[^,]*(?:,\s*[A-Za-z\s]+)*(?:,\s*[A-Z]{2,3}\s*\d[A-Z\d]{2})?)/i', $content, $matches)) {
                    $contact_info['address'] = trim($matches[0]);
                }
                
                break;
            }
        }
        
        return $contact_info;
    }

    /**
     * Detect business type from content
     */
    private function detect_business_type($content_data) {
        $business_keywords = [
            'agency' => ['agency', 'agencies', 'marketing agency', 'design agency'],
            'consultancy' => ['consultancy', 'consultant', 'consulting', 'advisory'],
            'company' => ['company', 'business', 'enterprise', 'firm'],
            'studio' => ['studio', 'creative studio', 'design studio'],
            'services' => ['services', 'solutions provider', 'service provider']
        ];
        
        $all_content = '';
        foreach ($content_data as $item) {
            $all_content .= ' ' . $item['title'] . ' ' . ($item['excerpt'] ?? '');
        }
        $all_content = strtolower($all_content);
        
        foreach ($business_keywords as $type => $keywords) {
            foreach ($keywords as $keyword) {
                if (stripos($all_content, $keyword) !== false) {
                    return "a professional " . $type;
                }
            }
        }
        
        return null;
    }

    /**
     * Extract location information
     */
    private function extract_location_info($content_data) {
        $location_patterns = [
            '/\b(London|Manchester|Birmingham|Liverpool|Bristol|Edinburgh|Glasgow|Cardiff|Belfast|Southampton|Brighton|Oxford|Cambridge|Bath|York)\b/i',
            '/\b([A-Z][a-z]+(?:\s+[A-Z][a-z]+)*),\s*(?:UK|United Kingdom|England|Scotland|Wales|Northern Ireland)\b/i'
        ];
        
        foreach ($content_data as $item) {
            $text = $item['title'] . ' ' . ($item['excerpt'] ?? '') . ' ' . ($item['content'] ?? '');
            
            foreach ($location_patterns as $pattern) {
                if (preg_match($pattern, $text, $matches)) {
                    return trim($matches[1]);
                }
            }
        }
        
        return null;
    }

    /**
     * Clean content data from shortcodes and theme elements
     */
    private function clean_content_data($content_data) {
        $cleaned_data = [];
        
        foreach ($content_data as $item) {
            $cleaned_item = $item;
            
            // Clean content field
            if (isset($item['content'])) {
                $cleaned_content = $item['content'];
                
                // Remove shortcodes
                $cleaned_content = strip_shortcodes($cleaned_content);
                
                // Remove HTML tags but keep formatting
                $cleaned_content = strip_tags($cleaned_content, '<p><br><strong><em><ul><ol><li>');
                
                // Remove common theme elements and widgets
                $cleaned_content = preg_replace('/\[.*?\]/', '', $cleaned_content); // Any remaining shortcodes
                $cleaned_content = preg_replace('/<script[^>]*>.*?<\/script>/is', '', $cleaned_content); // Scripts
                $cleaned_content = preg_replace('/<style[^>]*>.*?<\/style>/is', '', $cleaned_content); // Styles
                $cleaned_content = preg_replace('/\s+/', ' ', $cleaned_content); // Multiple spaces
                
                $cleaned_item['content'] = trim($cleaned_content);
            }
            
            // Clean excerpt
            if (isset($item['excerpt'])) {
                $cleaned_excerpt = strip_shortcodes($item['excerpt']);
                $cleaned_excerpt = strip_tags($cleaned_excerpt);
                $cleaned_item['excerpt'] = trim($cleaned_excerpt);
            }
            
            $cleaned_data[] = $cleaned_item;
        }
        
        return $cleaned_data;
    }

    /**
     * Get OpenAI API key from settings
     */
    private function get_openai_api_key() {
        // Get API key from plugin settings
        $settings = get_option('adverto_master_settings', array());
        $api_key = isset($settings['openai_api_key']) ? $settings['openai_api_key'] : '';
        
        // Fallback to individual option
        if (empty($api_key)) {
            $api_key = get_option('adverto_openai_api_key', '');
        }
        
        return $api_key;
    }

    /**
     * Make OpenAI API request
     */
    private function call_openai_api($prompt, $model = 'gpt-4o-mini', $max_retries = 3) {
        $api_key = $this->get_openai_api_key();
        
        if (empty($api_key)) {
            error_log('LLM Generator: No OpenAI API key found');
            return false;
        }
        
        $data = [
            'model' => $model,
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'You are a professional content analyst specialising in creating high-quality, British English website summaries and descriptions. Always respond in British English spelling and terminology.'
                ],
                [
                    'role' => 'user',
                    'content' => $prompt
                ]
            ],
            'max_tokens' => 200,
            'temperature' => 0.7
        ];
        
        $retry_count = 0;
        
        while ($retry_count < $max_retries) {
            $response = wp_remote_post('https://api.openai.com/v1/chat/completions', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $api_key,
                    'Content-Type' => 'application/json'
                ],
                'body' => json_encode($data),
                'timeout' => 45 // Increased timeout for complex requests
            ]);
            
            if (is_wp_error($response)) {
                error_log('LLM Generator: OpenAI API error (attempt ' . ($retry_count + 1) . ') - ' . $response->get_error_message());
                
                $retry_count++;
                if ($retry_count < $max_retries) {
                    // Progressive backoff: 2s, 4s, 8s
                    sleep(pow(2, $retry_count));
                    continue;
                }
                
                return false;
            }
            
            $body = json_decode(wp_remote_retrieve_body($response), true);
            $http_code = wp_remote_retrieve_response_code($response);
            
            // Handle rate limiting (HTTP 429)
            if ($http_code === 429) {
                error_log('LLM Generator: Rate limited by OpenAI (attempt ' . ($retry_count + 1) . ')');
                $retry_count++;
                if ($retry_count < $max_retries) {
                    sleep(pow(2, $retry_count + 1)); // Longer wait for rate limits
                    continue;
                }
                return false;
            }
            
            // Handle other HTTP errors
            if ($http_code >= 400) {
                error_log('LLM Generator: OpenAI API HTTP error ' . $http_code . ' - ' . json_encode($body));
                $retry_count++;
                if ($retry_count < $max_retries) {
                    sleep(2);
                    continue;
                }
                return false;
            }
            
            // Success case
            if (isset($body['choices'][0]['message']['content'])) {
                return trim($body['choices'][0]['message']['content']);
            }
            
            // Invalid response structure
            error_log('LLM Generator: Invalid OpenAI API response - ' . json_encode($body));
            return false;
        }
        
        return false;
    }

    /**
     * Get processing type label for display
     */
    private function get_processing_type_label($processing_type) {
        $labels = [
            'basic' => 'Basic Generation',
            'ai_summaries' => 'AI Summaries',
            'key_points' => 'Key Points Extraction',
            'structured' => 'Structured Analysis'
        ];
        
        return $labels[$processing_type] ?? 'Basic Generation';
    }

    /**
     * Generate AI-enhanced about section
     */
    private function generate_intelligent_about_section($content_data, $processing_type = 'basic') {
        $site_name = get_bloginfo('name');
        $site_description = get_bloginfo('description');
        
        // Start with basic description
        $about = "We are {$site_name}";
        
        // Analyse content to determine business type
        $business_type = $this->detect_business_type($content_data);
        if ($business_type) {
            $about .= ", {$business_type}";
        }
        
        // Add location if found
        $location = $this->extract_location_info($content_data);
        if ($location) {
            $about .= " based in {$location}";
        }
        
        $about .= ". ";
        
        // Use AI for advanced processing types
        if (in_array($processing_type, ['ai_summaries', 'key_points', 'structured'])) {
            $enhanced_about = $this->generate_ai_about_section($content_data, $site_name, $site_description);
            if ($enhanced_about) {
                return $enhanced_about;
            }
        }
        
        // Fallback to basic generation
        if (!empty($site_description)) {
            $about .= $site_description;
        } else {
            $services = $this->extract_services_from_content($content_data, 'basic', 3);
            if (!empty($services)) {
                $service_names = array_map(function($service) {
                    return strtolower($service['title']);
                }, $services);
                $about .= "We specialise in " . implode(', ', array_slice($service_names, 0, -1)) . 
                         (count($service_names) > 1 ? ', and ' . end($service_names) : '') . 
                         " to help our clients achieve their goals.";
            }
        }
        
        return $about;
    }

    /**
     * Generate AI-powered about section
     */
    private function generate_ai_about_section($content_data, $site_name, $site_description) {
        // Prepare content for AI analysis
        $content_summary = $this->prepare_content_for_ai($content_data, 1500);
        
        $prompt = "Based on the following website content, create a professional 'About Us' description for {$site_name} in British English. 
        
Website Description: {$site_description}
Content Summary: {$content_summary}

Please create a compelling, professional description that:
1. Explains what the business does
2. Highlights key services or expertise
3. Uses British English spelling and terminology
4. Is 2-3 sentences long
5. Sounds professional and trustworthy

Response should be just the description, no quotes or extra formatting.";

        return $this->call_openai_api($prompt);
    }
}
