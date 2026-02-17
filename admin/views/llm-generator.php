<?php
/**
 * LLMs.txt Generator Admin View
 * Beautiful, intelligent interface with British spellings
 */

// Security check
if (!defined('ABSPATH')) {
    exit;
}

// Get LLMs Generator instance
$llm_generator = new Adverto_LLM_Generator('adverto-master', '1.0.0');

// Handle form submissions
if (isset($_POST['generate_llm_txt']) && wp_verify_nonce($_POST['_wpnonce'], 'adverto_generate_llm')) {
    $generation_options = array(
        'include_pages' => isset($_POST['include_pages']),
        'include_posts' => isset($_POST['include_posts']),
        'include_products' => isset($_POST['include_products']),
        'include_custom_post_types' => isset($_POST['include_custom_post_types']),
        'processing_type' => sanitize_text_field($_POST['processing_type'] ?? 'basic'),
        'max_posts' => intval($_POST['max_posts'] ?? -1)
    );
    
    // Try to generate the file
    $llm_generator = new Adverto_LLM_Generator('adverto-master', '1.0.0');
    $result = $llm_generator->generate_llm_file($generation_options);
    
    if (is_wp_error($result)) {
        $error_message = $result->get_error_message();
        echo '<div class="notice notice-error"><p>Error: ' . esc_html($error_message) . '</p></div>';
    } else {
        echo '<div class="notice notice-success"><p>LLMs.txt file generated successfully!</p></div>';
    }
}

// Handle test AJAX form submission (non-AJAX fallback)
if (isset($_POST['test_ajax_fallback'])) {
    echo '<div class="notice notice-info"><p>✅ Form submission works! WordPress processing is functional.</p></div>';
}

// Get current stats
$stats = $llm_generator->get_llm_stats();

// Get content counts
$pages_count = wp_count_posts('page')->publish ?? 0;
$posts_count = wp_count_posts('post')->publish ?? 0;
$products_count = post_type_exists('product') ? wp_count_posts('product')->publish : 0;

// Get custom post types
$custom_post_types = get_post_types(array('public' => true, '_builtin' => false), 'objects');
?>

<div class="adverto-container">
    <div class="adverto-header">
        <h1>
            <span class="material-icons">auto_awesome</span>
            <?php _e('LLMs.txt Generator', 'adverto-master'); ?>
            <span class="powered-by-adverto">Powered by Adverto Media</span>
        </h1>
        <div class="adverto-breadcrumb">
            <a href="<?php echo admin_url('admin.php?page=adverto-master'); ?>"><?php _e('Dashboard', 'adverto-master'); ?></a>
            <span> / </span>
            <span><?php _e('LLMs.txt Generator', 'adverto-master'); ?></span>
        </div>
    </div>

    <div class="adverto-content">
        <!-- Status Overview -->
        <div class="adverto-card llm-overview">
            <div class="adverto-card-header">
                <h2>
                    <span class="material-icons">analytics</span>
                    <?php _e('LLMs.txt Status Overview', 'adverto-master'); ?>
                </h2>
                <p><?php _e('Current status of your site\'s LLMs.txt file for AI and language model integration', 'adverto-master'); ?></p>
            </div>
            
            <div class="adverto-card-content">
                <div class="llm-status-grid">
                    <div class="status-item">
                        <div class="status-icon <?php echo $stats['exists'] ? 'success' : 'warning'; ?>">
                            <span class="material-icons"><?php echo $stats['exists'] ? 'check_circle' : 'pending'; ?></span>
                        </div>
                        <div class="status-content">
                            <h3><?php _e('File Status', 'adverto-master'); ?></h3>
                            <p><?php echo $stats['exists'] ? __('LLMs.txt Generated', 'adverto-master') : __('No LLMs.txt File', 'adverto-master'); ?></p>
                        </div>
                    </div>
                    
                    <?php if ($stats['exists']): ?>
                    <div class="status-item">
                        <div class="status-icon info">
                            <span class="material-icons">description</span>
                        </div>
                        <div class="status-content">
                            <h3><?php _e('Content Size', 'adverto-master'); ?></h3>
                            <p><?php echo number_format($stats['word_count']); ?> <?php _e('words', 'adverto-master'); ?></p>
                            <small><?php echo size_format($stats['size'], 1); ?></small>
                        </div>
                    </div>
                    
                    <div class="status-item">
                        <div class="status-icon info">
                            <span class="material-icons">folder</span>
                        </div>
                        <div class="status-content">
                            <h3><?php _e('Sections', 'adverto-master'); ?></h3>
                            <p><?php echo $stats['sections']; ?> <?php _e('sections', 'adverto-master'); ?></p>
                            <small><?php echo $stats['pages']; ?> <?php _e('pages included', 'adverto-master'); ?></small>
                        </div>
                    </div>
                    
                    <div class="status-item">
                        <div class="status-icon <?php echo $stats['age_days'] > 30 ? 'warning' : 'success'; ?>">
                            <span class="material-icons">schedule</span>
                        </div>
                        <div class="status-content">
                            <h3><?php _e('Last Updated', 'adverto-master'); ?></h3>
                            <p><?php echo $stats['age_days']; ?> <?php _e('days ago', 'adverto-master'); ?></p>
                            <small><?php echo date('j M Y, g:i a', $stats['generated']); ?></small>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                
                <?php if ($stats['exists']): ?>
                <div class="llm-actions">
                    <a href="<?php echo $stats['endpoint_url']; ?>" target="_blank" class="adverto-btn adverto-btn-outline">
                        <span class="material-icons">open_in_new</span>
                        <span class="btn-text"><?php _e('View LLMs.txt', 'adverto-master'); ?></span>
                    </a>
                    <button type="button" id="download-llm-txt" class="adverto-btn adverto-btn-secondary">
                        <span class="material-icons">download</span>
                        <span class="btn-text"><?php _e('Download File', 'adverto-master'); ?></span>
                    </button>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Content Analysis -->
        <div class="adverto-card">
            <div class="adverto-card-header">
                <h2>
                    <span class="material-icons">insights</span>
                    <?php _e('Content Analysis', 'adverto-master'); ?>
                </h2>
                <p><?php _e('Overview of your website content available for LLMs.txt generation', 'adverto-master'); ?></p>
            </div>
            
            <div class="adverto-card-content">
                <div class="content-analysis-grid">
                    <div class="content-type-card">
                        <div class="content-icon">
                            <span class="material-icons">description</span>
                        </div>
                        <div class="content-info">
                            <h4><?php _e('Pages', 'adverto-master'); ?></h4>
                            <p class="content-count"><?php echo number_format($pages_count); ?></p>
                            <small><?php _e('Published pages', 'adverto-master'); ?></small>
                        </div>
                        <div class="content-toggle">
                            <label class="adverto-switch">
                                <input type="checkbox" id="include_pages" checked>
                                <span class="adverto-switch-slider"></span>
                            </label>
                        </div>
                    </div>
                    
                    <div class="content-type-card">
                        <div class="content-icon">
                            <span class="material-icons">article</span>
                        </div>
                        <div class="content-info">
                            <h4><?php _e('Blog Posts', 'adverto-master'); ?></h4>
                            <p class="content-count"><?php echo number_format($posts_count); ?></p>
                            <small><?php _e('Published posts', 'adverto-master'); ?></small>
                        </div>
                        <div class="content-toggle">
                            <label class="adverto-switch">
                                <input type="checkbox" id="include_posts" checked>
                                <span class="adverto-switch-slider"></span>
                            </label>
                        </div>
                    </div>
                    
                    <?php if ($products_count > 0): ?>
                    <div class="content-type-card">
                        <div class="content-icon">
                            <span class="material-icons">shopping_bag</span>
                        </div>
                        <div class="content-info">
                            <h4><?php _e('Products', 'adverto-master'); ?></h4>
                            <p class="content-count"><?php echo number_format($products_count); ?></p>
                            <small><?php _e('WooCommerce products', 'adverto-master'); ?></small>
                        </div>
                        <div class="content-toggle">
                            <label class="adverto-switch">
                                <input type="checkbox" id="include_products">
                                <span class="adverto-switch-slider"></span>
                            </label>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($custom_post_types)): ?>
                    <div class="content-type-card">
                        <div class="content-icon">
                            <span class="material-icons">extension</span>
                        </div>
                        <div class="content-info">
                            <h4><?php _e('Custom Content', 'adverto-master'); ?></h4>
                            <p class="content-count"><?php echo count($custom_post_types); ?></p>
                            <small><?php _e('Custom post types', 'adverto-master'); ?></small>
                        </div>
                        <div class="content-toggle">
                            <label class="adverto-switch">
                                <input type="checkbox" id="include_custom_post_types">
                                <span class="adverto-switch-slider"></span>
                            </label>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <!-- New Comprehensive Site Discovery Option -->
                    <div class="content-type-card featured">
                        <div class="content-icon">
                            <span class="material-icons">travel_explore</span>
                        </div>
                        <div class="content-info">
                            <h4><?php _e('Comprehensive Site Discovery', 'adverto-master'); ?></h4>
                            <p class="content-count"><?php _e('ALL', 'adverto-master'); ?></p>
                            <small><?php _e('Discovers ALL pages via sitemaps, navigation & crawling', 'adverto-master'); ?></small>
                        </div>
                        <div class="content-toggle">
                            <label class="adverto-switch">
                                <input type="checkbox" id="discover_all_pages" checked>
                                <span class="adverto-switch-slider"></span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Advanced Discovery Options -->
        <div class="adverto-card" id="advanced-discovery-options" style="display: block;">
            <div class="adverto-card-header">
                <h2>
                    <span class="material-icons">settings</span>
                    <?php _e('Advanced Discovery Options', 'adverto-master'); ?>
                </h2>
                <p><?php _e('Fine-tune the comprehensive site discovery process', 'adverto-master'); ?></p>
            </div>
            
            <div class="adverto-card-content">
                <div class="discovery-options-grid">
                    <div class="option-group">
                        <label for="max_pages_to_crawl"><?php _e('Maximum Pages to Crawl', 'adverto-master'); ?></label>
                        <select id="max_pages_to_crawl" name="max_pages_to_crawl">
                            <option value="50"><?php _e('50 pages (Fast)', 'adverto-master'); ?></option>
                            <option value="100" selected><?php _e('100 pages (Recommended)', 'adverto-master'); ?></option>
                            <option value="200"><?php _e('200 pages (Comprehensive)', 'adverto-master'); ?></option>
                            <option value="500"><?php _e('500 pages (Maximum)', 'adverto-master'); ?></option>
                        </select>
                        <small class="help-text"><?php _e('Limits the total number of pages to prevent timeouts', 'adverto-master'); ?></small>
                    </div>
                    
                    <div class="option-group">
                        <label for="max_crawl_depth"><?php _e('Crawling Depth', 'adverto-master'); ?></label>
                        <select id="max_crawl_depth" name="max_crawl_depth">
                            <option value="1"><?php _e('Level 1 (Fast)', 'adverto-master'); ?></option>
                            <option value="2" selected><?php _e('Level 2 (Recommended)', 'adverto-master'); ?></option>
                            <option value="3"><?php _e('Level 3 (Deep)', 'adverto-master'); ?></option>
                        </select>
                        <small class="help-text"><?php _e('How many levels deep to follow links', 'adverto-master'); ?></small>
                    </div>
                </div>
                
                <div class="discovery-methods">
                    <h4><?php _e('Discovery Methods Used:', 'adverto-master'); ?></h4>
                    <ul class="discovery-list">
                        <li><span class="material-icons">map</span> <?php _e('XML Sitemaps (sitemap.xml)', 'adverto-master'); ?></li>
                        <li><span class="material-icons">menu</span> <?php _e('Navigation Menus', 'adverto-master'); ?></li>
                        <li><span class="material-icons">link</span> <?php _e('Internal Link Following', 'adverto-master'); ?></li>
                        <li><span class="material-icons">storage</span> <?php _e('WordPress Database', 'adverto-master'); ?></li>
                        <li><span class="material-icons">search</span> <?php _e('Recursive Link Discovery', 'adverto-master'); ?></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Generation Settings -->
        <div class="adverto-card">
            <div class="adverto-card-header">
                <h2>
                    <span class="material-icons">tune</span>
                    <?php _e('Generation Settings', 'adverto-master'); ?>
                </h2>
                <p><?php _e('Configure how your LLMs.txt file is generated with AI-powered content analysis', 'adverto-master'); ?></p>
            </div>
            
            <div class="adverto-card-content">
                <form method="post" id="llm-generator-form" action="<?php echo admin_url('admin.php?page=adverto-llm-generator'); ?>">
                    <?php wp_nonce_field('adverto_generate_llm', '_wpnonce'); ?>
                    
                    <div class="generation-options">
                        <div class="option-group">
                            <h4><?php _e('AI Processing Level', 'adverto-master'); ?></h4>
                            <p class="option-description"><?php _e('Choose how content should be processed by artificial intelligence', 'adverto-master'); ?></p>
                            
                            <div class="processing-options">
                                <label class="processing-option">
                                    <input type="radio" name="processing_type" value="basic" checked>
                                    <div class="option-content">
                                        <div class="option-icon">
                                            <span class="material-icons">flash_on</span>
                                        </div>
                                        <div class="option-info">
                                            <h5><?php _e('Basic Generation', 'adverto-master'); ?></h5>
                                            <p><?php _e('Fast generation using existing content and excerpts', 'adverto-master'); ?></p>
                                            <small class="option-time"><?php _e('~30 seconds', 'adverto-master'); ?></small>
                                        </div>
                                    </div>
                                </label>
                                
                                <label class="processing-option">
                                    <input type="radio" name="processing_type" value="summary">
                                    <div class="option-content">
                                        <div class="option-icon">
                                            <span class="material-icons">auto_awesome</span>
                                        </div>
                                        <div class="option-info">
                                            <h5><?php _e('AI Summaries', 'adverto-master'); ?></h5>
                                            <p><?php _e('Generate intelligent summaries for each page using AI', 'adverto-master'); ?></p>
                                            <small class="option-time"><?php _e('~2-5 minutes', 'adverto-master'); ?></small>
                                        </div>
                                    </div>
                                </label>
                                
                                <label class="processing-option">
                                    <input type="radio" name="processing_type" value="key_points">
                                    <div class="option-content">
                                        <div class="option-icon">
                                            <span class="material-icons">list_alt</span>
                                        </div>
                                        <div class="option-info">
                                            <h5><?php _e('Key Points Extraction', 'adverto-master'); ?></h5>
                                            <p><?php _e('AI-powered extraction of key points and highlights', 'adverto-master'); ?></p>
                                            <small class="option-time"><?php _e('~3-7 minutes', 'adverto-master'); ?></small>
                                        </div>
                                    </div>
                                </label>
                                
                                <label class="processing-option">
                                    <input type="radio" name="processing_type" value="structured">
                                    <div class="option-content">
                                        <div class="option-icon">
                                            <span class="material-icons">account_tree</span>
                                        </div>
                                        <div class="option-info">
                                            <h5><?php _e('Structured Analysis', 'adverto-master'); ?></h5>
                                            <p><?php _e('Comprehensive AI analysis with structured data extraction', 'adverto-master'); ?></p>
                                            <small class="option-time"><?php _e('~5-10 minutes', 'adverto-master'); ?></small>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>
                        
                        <div class="option-group">
                            <h4><?php _e('Content Limits', 'adverto-master'); ?></h4>
                            <p class="option-description"><?php _e('Set limits to control processing time and API costs', 'adverto-master'); ?></p>
                            
                            <div class="adverto-form-row">
                                <div class="adverto-form-group">
                                    <label for="max_posts"><?php _e('Maximum Items to Process', 'adverto-master'); ?></label>
                                    <select name="max_posts" id="max_posts" class="adverto-select">
                                        <option value="-1"><?php _e('All Content (Recommended)', 'adverto-master'); ?></option>
                                        <option value="50"><?php _e('50 Items (Quick)', 'adverto-master'); ?></option>
                                        <option value="100"><?php _e('100 Items (Balanced)', 'adverto-master'); ?></option>
                                        <option value="200"><?php _e('200 Items (Comprehensive)', 'adverto-master'); ?></option>
                                    </select>
                                    <small class="adverto-field-help"><?php _e('Limit the number of items processed to control generation time', 'adverto-master'); ?></small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="generation-actions">
                        <div class="action-left">
                            <div class="cost-estimate">
                                <span class="material-icons">info</span>
                                <span id="cost-estimate"><?php _e('Select options to see cost estimate', 'adverto-master'); ?></span>
                            </div>
                        </div>
                        
                        <div class="action-right">
                            <button type="button" id="test-modal-btn" class="adverto-btn adverto-btn-outline" style="margin-right: 10px;">
                                <span class="material-icons">visibility</span>
                                <span class="btn-text">Test Progress Modal</span>
                            </button>
                            <button type="submit" name="generate_llm_txt" id="generate-btn" class="adverto-btn adverto-btn-primary adverto-btn-large">
                                <span class="material-icons">smart_toy</span>
                                <span class="btn-text"><?php _e('Generate LLMs.txt File', 'adverto-master'); ?></span>
                            </button>
                        </div>
                    </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Advanced Settings -->
        <div class="adverto-card">
            <div class="adverto-card-header">
                <h2>
                    <span class="material-icons">settings</span>
                    <?php _e('Advanced Settings', 'adverto-master'); ?>
                </h2>
                <p><?php _e('Configure automatic updates and advanced options for your LLMs.txt file', 'adverto-master'); ?></p>
            </div>
            
            <div class="adverto-card-content">
                <div class="advanced-settings-grid">
                    <div class="setting-group">
                        <h4><?php _e('Automatic Updates', 'adverto-master'); ?></h4>
                        <p><?php _e('Schedule automatic regeneration of your LLMs.txt file', 'adverto-master'); ?></p>
                        
                        <label class="adverto-switch">
                            <input type="checkbox" id="auto_update_enabled">
                            <span class="adverto-switch-slider"></span>
                        </label>
                        <span class="switch-label"><?php _e('Enable automatic updates', 'adverto-master'); ?></span>
                        
                        <div class="schedule-options" style="display: none;">
                            <select id="update_frequency" class="adverto-select">
                                <option value="weekly"><?php _e('Weekly', 'adverto-master'); ?></option>
                                <option value="monthly"><?php _e('Monthly', 'adverto-master'); ?></option>
                                <option value="quarterly"><?php _e('Quarterly', 'adverto-master'); ?></option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="setting-group">
                        <h4><?php _e('Public Access', 'adverto-master'); ?></h4>
                        <p><?php _e('Make your LLMs.txt file publicly accessible at /llms.txt', 'adverto-master'); ?></p>
                        
                        <label class="adverto-switch">
                            <input type="checkbox" id="public_access" checked>
                            <span class="adverto-switch-slider"></span>
                        </label>
                        <span class="switch-label"><?php _e('Enable public access', 'adverto-master'); ?></span>
                        
                        <div class="public-url">
                            <code><?php echo home_url('llms.txt'); ?></code>
                            <button type="button" class="copy-url-btn" title="<?php _e('Copy URL', 'adverto-master'); ?>">
                                <span class="material-icons">content_copy</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Information Panel -->
        <div class="adverto-card llm-info">
            <div class="adverto-card-header">
                <h2>
                    <span class="material-icons">help</span>
                    <?php _e('About LLMs.txt Files', 'adverto-master'); ?>
                </h2>
            </div>
            
            <div class="adverto-card-content">
                <div class="info-sections">
                    <div class="info-section">
                        <h4><?php _e('What is LLMs.txt?', 'adverto-master'); ?></h4>
                        <p><?php _e('LLMs.txt is a standardised file format that provides Large Language Models and AI systems with structured, easily digestible information about your website\'s content. It\'s similar to robots.txt but designed for AI consumption rather than web crawlers.', 'adverto-master'); ?></p>
                    </div>
                    
                    <div class="info-section">
                        <h4><?php _e('Benefits for Your Website', 'adverto-master'); ?></h4>
                        <ul>
                            <li><?php _e('Improved AI interactions and chatbot responses', 'adverto-master'); ?></li>
                            <li><?php _e('Better search engine understanding of your content', 'adverto-master'); ?></li>
                            <li><?php _e('Enhanced accessibility for AI-powered tools', 'adverto-master'); ?></li>
                            <li><?php _e('Future-proofing for AI-driven web technologies', 'adverto-master'); ?></li>
                        </ul>
                    </div>
                    
                    <div class="info-section">
                        <h4><?php _e('Privacy & Security', 'adverto-master'); ?></h4>
                        <p><?php _e('Only published content is included in your LLMs.txt file. Private pages, drafts, and password-protected content are automatically excluded. You maintain full control over what information is shared.', 'adverto-master'); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Processing Modal -->
<div id="processing-modal" class="adverto-modal" style="display: none;">
    <div class="adverto-modal-backdrop"></div>
    <div class="adverto-modal-container">
        <div class="adverto-modal-content">
            <div class="processing-header">
                <div class="processing-icon">
                    <span class="material-icons spinning">smart_toy</span>
                </div>
                <h3><?php _e('Generating LLMs.txt File', 'adverto-master'); ?></h3>
                <p><?php _e('Please wait while we process your content with AI...', 'adverto-master'); ?></p>
            </div>
            
            <div class="processing-progress">
                <div class="progress-bar">
                    <div class="progress-fill" id="progress-fill"></div>
                </div>
                <div class="progress-text">
                    <span id="progress-status"><?php _e('Initialising...', 'adverto-master'); ?></span>
                    <span id="progress-percent">0%</span>
                </div>
            </div>
            
            <div class="processing-steps">
                <div class="step" id="step-scan">
                    <span class="material-icons">search</span>
                    <span><?php _e('Scanning content', 'adverto-master'); ?></span>
                    <span class="step-status"></span>
                </div>
                <div class="step" id="step-ai">
                    <span class="material-icons">psychology</span>
                    <span><?php _e('AI processing', 'adverto-master'); ?></span>
                    <span class="step-status"></span>
                </div>
                <div class="step" id="step-generate">
                    <span class="material-icons">description</span>
                    <span><?php _e('Generating file', 'adverto-master'); ?></span>
                    <span class="step-status"></span>
                </div>
                <div class="step" id="step-save">
                    <span class="material-icons">save</span>
                    <span><?php _e('Saving & publishing', 'adverto-master'); ?></span>
                    <span class="step-status"></span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
(function($) {
    'use strict';
    
    $(document).ready(function() {
        console.log('LLMs Generator JavaScript loaded');
        
        // Update cost estimate when options change
        function updateCostEstimate() {
            const processingType = $('input[name="processing_type"]:checked').val();
            const maxPosts = $('#max_posts').val();
            const includePages = $('#include_pages').is(':checked');
            const includePosts = $('#include_posts').is(':checked');
            const includeProducts = $('#include_products').is(':checked');
            
            let estimatedItems = 0;
            if (includePages) estimatedItems += <?php echo $pages_count; ?>;
            if (includePosts) estimatedItems += <?php echo $posts_count; ?>;
            if (includeProducts) estimatedItems += <?php echo $products_count; ?>;
            
            if (maxPosts !== '-1') {
                estimatedItems = Math.min(estimatedItems, parseInt(maxPosts));
            }
            
            let costText = '';
            let timeText = '';
            
            switch (processingType) {
                case 'basic':
                    costText = '<?php _e('Free', 'adverto-master'); ?>';
                    timeText = '~30 <?php _e('seconds', 'adverto-master'); ?>';
                    break;
                case 'summary':
                    costText = '$' + (estimatedItems * 0.001).toFixed(3);
                    timeText = '~' + Math.ceil(estimatedItems / 20) + ' <?php _e('minutes', 'adverto-master'); ?>';
                    break;
                case 'key_points':
                    costText = '$' + (estimatedItems * 0.002).toFixed(3);
                    timeText = '~' + Math.ceil(estimatedItems / 15) + ' <?php _e('minutes', 'adverto-master'); ?>';
                    break;
                case 'structured':
                    costText = '$' + (estimatedItems * 0.004).toFixed(3);
                    timeText = '~' + Math.ceil(estimatedItems / 10) + ' <?php _e('minutes', 'adverto-master'); ?>';
                    break;
            }
            
            $('#cost-estimate').text('<?php _e('Estimated cost:', 'adverto-master'); ?> ' + costText + ' • ' + timeText + ' • ' + estimatedItems + ' <?php _e('items', 'adverto-master'); ?>');
        }
        
        // Bind events
        $('input[name="processing_type"], #max_posts, .content-toggle input').on('change', updateCostEstimate);
        
        // Initial cost estimate
        updateCostEstimate();
        
        // Test AJAX button
        $('#test-ajax-btn').on('click', function() {
            console.log('Test AJAX button clicked');
            
            var testData = {
                action: 'adverto_test_llm_ajax',
                nonce: '<?php echo wp_create_nonce('adverto_generate_llm'); ?>'
            };
            
            console.log('Test AJAX URL:', '<?php echo admin_url('admin-ajax.php'); ?>');
            console.log('Test data:', testData);
            
            $.ajax({
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                type: 'POST',
                data: testData,
                dataType: 'json',
                timeout: 10000,
                success: function(response) {
                    console.log('Test AJAX Success:', response);
                    alert('✅ AJAX Test SUCCESS!\n\nServer Response: ' + JSON.stringify(response, null, 2));
                },
                error: function(xhr, status, error) {
                    console.log('Test AJAX Error:', xhr, status, error);
                    console.log('Response Text:', xhr.responseText);
                    
                    var errorDetails = 'Status: ' + status + '\nError: ' + error;
                    if (xhr.responseText) {
                        errorDetails += '\nResponse: ' + xhr.responseText.substring(0, 500);
                    }
                    
                    alert('❌ AJAX Test FAILED!\n\n' + errorDetails);
                }
            });
        });
        
        // Auto-update toggle
        $('#auto_update_enabled').on('change', function() {
            $('.schedule-options').toggle($(this).is(':checked'));
        });
        
        // Copy URL functionality
        $('.copy-url-btn').on('click', function() {
            const url = $(this).siblings('code').text();
            navigator.clipboard.writeText(url).then(function() {
                // Show success feedback
                const btn = $('.copy-url-btn');
                const originalIcon = btn.find('.material-icons').text();
                btn.find('.material-icons').text('check');
                setTimeout(() => {
                    btn.find('.material-icons').text(originalIcon);
                }, 2000);
            });
        });
        
        // Download LLMs.txt
        $('#download-llm-txt').on('click', function() {
            console.log('Download button clicked');
            
            // Create a form and submit it to trigger download
            var form = $('<form>', {
                method: 'POST',
                action: '<?php echo admin_url('admin-ajax.php'); ?>',
                target: '_blank'
            });
            
            form.append($('<input>', {
                type: 'hidden',
                name: 'action',
                value: 'adverto_download_llm_txt'
            }));
            
            form.append($('<input>', {
                type: 'hidden',
                name: 'nonce',
                value: $('input[name="_wpnonce"]').val()
            }));
            
            $('body').append(form);
            form.submit();
            form.remove();
        });
        
        // View LLMs.txt
        $('#view-llm-txt').on('click', function() {
            console.log('View button clicked');
            
            // Open the public URL in a new tab
            window.open('<?php echo home_url('/llms.txt'); ?>', '_blank');
        });
        
        // Update cost estimation based on selected options
        function updateCostEstimate() {
            var processing_type = $('input[name="processing_type"]:checked').val() || 'basic';
            var max_posts = $('#max_posts').val() || 'all';
            var include_posts = $('#include_posts').is(':checked');
            var include_pages = $('#include_pages').is(':checked');
            var include_products = $('#include_products').is(':checked');
            
            var estimate = '';
            var cost = 0;
            var time = '';
            
            // Calculate based on processing type
            switch(processing_type) {
                case 'basic':
                    cost = 0;
                    time = '~30 seconds';
                    estimate = 'Free - Basic generation using existing content';
                    break;
                case 'ai_summaries':
                    cost = 0.05;
                    time = '~2-5 minutes';
                    estimate = 'Approximately £0.05 - AI-powered summaries';
                    break;
                case 'key_points':
                    cost = 0.08;
                    time = '~3-7 minutes';
                    estimate = 'Approximately £0.08 - Key points extraction';
                    break;
                case 'structured':
                    cost = 0.12;
                    time = '~5-10 minutes';
                    estimate = 'Approximately £0.12 - Full AI analysis';
                    break;
            }
            
            // Add content factor
            var content_types = 0;
            if (include_posts) content_types++;
            if (include_pages) content_types++;
            if (include_products) content_types++;
            
            if (processing_type !== 'basic' && content_types > 1) {
                cost *= content_types * 0.7; // Slight multiplier for multiple content types
            }
            
            // Update limits factor
            if (max_posts !== '-1' && max_posts < 50) {
                cost *= 0.5; // Reduce cost for limited content
                time = time.replace(/\d+-\d+/, Math.ceil(parseInt(time.match(/\d+/)[0]) * 0.5) + '-' + Math.ceil(parseInt(time.match(/\d+-(\d+)/)[1]) * 0.5));
            }
            
            $('#cost-estimate').html('<strong>Estimated Cost:</strong> ' + estimate + ' | <strong>Time:</strong> ' + time);
        }
        
        // Update cost estimate when options change
        $('input[name="processing_type"], #max_posts, #include_posts, #include_pages, #include_products').on('change', updateCostEstimate);
        
        // Initial cost estimate
        updateCostEstimate();
        
        // Test modal button
        $('#test-modal-btn').on('click', function(e) {
            e.preventDefault();
            console.log('Testing modal display');
            
            // Show modal immediately with multiple methods to ensure visibility
            var $modal = $('#processing-modal');
            $modal.show().css({
                'display': 'flex',
                'visibility': 'visible',
                'opacity': '1'
            }).addClass('active');
            
            $('body').css('overflow', 'hidden');
            
            // Start test progress
            startProgressTracking('structured');
            
            // Auto-hide after 10 seconds
            setTimeout(() => {
                hideModal();
            }, 10000);
        });
        
        $('#llm-generator-form').on('submit', function(e) {
            e.preventDefault();
            
            console.log('Form submitted - showing modal');
            
            // Show modal immediately with multiple methods to ensure visibility
            var $modal = $('#processing-modal');
            $modal.show().css({
                'display': 'flex',
                'visibility': 'visible',
                'opacity': '1'
            }).addClass('active');
            
            // Force body scroll lock
            $('body').css('overflow', 'hidden');
            
            // Double-check modal is visible
            setTimeout(() => {
                if (!$modal.is(':visible')) {
                    console.log('Modal not visible, forcing display');
                    $modal.css('display', 'flex !important');
                }
            }, 100);
            
            console.log('Modal should be visible now');
            
            // Get form data
            var formData = {
                action: 'adverto_generate_llm_txt',
                nonce: $('input[name="_wpnonce"]').val(),
                include_pages: $('#include_pages').is(':checked') ? 1 : 0,
                include_posts: $('#include_posts').is(':checked') ? 1 : 0,
                include_products: $('#include_products').is(':checked') ? 1 : 0,
                include_custom_post_types: $('#include_custom_post_types').is(':checked') ? 1 : 0,
                discover_all_pages: $('#discover_all_pages').is(':checked') ? 1 : 0,
                max_pages_to_crawl: $('#max_pages_to_crawl').val() || 100,
                max_crawl_depth: $('#max_crawl_depth').val() || 2,
                processing_type: $('input[name="processing_type"]:checked').val() || 'basic',
                max_posts: $('#max_posts').val() || -1
            };
            
            console.log('Sending data:', formData);
            
            // Start realistic progress simulation
            startProgressTracking(formData.processing_type);
            
            // Set timeout based on processing type
            var timeout = getTimeoutForProcessing(formData.processing_type);
            
            // Make AJAX request with proper timeout
            $.ajax({
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                type: 'POST',
                data: formData,
                dataType: 'json',
                timeout: timeout,
                success: function(response) {
                    console.log('AJAX Success:', response);
                    completeProgress(true);
                    setTimeout(() => {
                        hideModal();
                        if (response.success) {
                            showSuccessMessage('<?php _e('LLMs.txt file generated successfully!', 'adverto-master'); ?>');
                            location.reload();
                        } else {
                            showErrorMessage('<?php _e('Error:', 'adverto-master'); ?> ' + (response.data || 'Unknown error'));
                        }
                    }, 1500);
                },
                error: function(xhr, status, error) {
                    console.log('AJAX Error:', xhr, status, error);
                    console.log('Response Text:', xhr.responseText);
                    completeProgress(false);
                    
                    setTimeout(() => {
                        hideModal();
                        
                        var errorMsg;
                        if (status === 'timeout') {
                            errorMsg = '<?php _e('Processing timeout - try reducing content or using Basic processing', 'adverto-master'); ?>';
                        } else {
                            errorMsg = '<?php _e('Processing failed:', 'adverto-master'); ?> ' + status;
                            if (xhr.responseText) {
                                errorMsg += ' - ' + xhr.responseText.substring(0, 200);
                            }
                        }
                        showErrorMessage(errorMsg);
                    }, 1500);
                }
            });
        });
        
        function getTimeoutForProcessing(processingType) {
            switch (processingType) {
                case 'basic':
                    return 60000; // 1 minute
                case 'ai_summaries':
                    return 180000; // 3 minutes
                case 'key_points':
                    return 240000; // 4 minutes
                case 'structured':
                    return 360000; // 6 minutes
                default:
                    return 120000; // 2 minutes default
            }
        }
        
        function startProgressTracking(processingType) {
            // Reset progress
            $('#progress-fill').css('width', '0%');
            $('#progress-percent').text('0%');
            $('.step').removeClass('active complete').find('.step-status').empty();
            
            // Start first step
            $('#step-scan').addClass('active');
            $('#progress-status').text('<?php _e('Initialising content scan...', 'adverto-master'); ?>');
            
            // Determine timing based on processing type
            let totalSteps = 4;
            let totalTime = getTimeoutForProcessing(processingType) * 0.8; // Use 80% of timeout for progress
            let stepDuration = totalTime / totalSteps;
            
            window.progressInterval = setInterval(() => {
                updateProgressStep(processingType);
            }, stepDuration / 20); // 20 updates per step for smooth animation
        }
        
        let currentProgress = 0;
        let currentStepIndex = 0;
        const progressSteps = ['scan', 'ai', 'generate', 'save'];
        
        function updateProgressStep(processingType) {
            currentProgress += 1;
            
            // Update progress bar
            let displayProgress = Math.min(currentProgress, 95); // Never show 100% until complete
            $('#progress-fill').css('width', displayProgress + '%');
            $('#progress-percent').text(Math.floor(displayProgress) + '%');
            
            // Update steps
            let stepProgress = currentProgress / 25; // Each step is 25%
            let newStepIndex = Math.floor(stepProgress);
            
            if (newStepIndex > currentStepIndex && newStepIndex < progressSteps.length) {
                // Complete current step
                if (currentStepIndex < progressSteps.length) {
                    $('#step-' + progressSteps[currentStepIndex])
                        .removeClass('active')
                        .addClass('complete')
                        .find('.step-status')
                        .html('<span class="material-icons">check_circle</span>');
                }
                
                // Start new step
                currentStepIndex = newStepIndex;
                if (currentStepIndex < progressSteps.length) {
                    $('#step-' + progressSteps[currentStepIndex]).addClass('active');
                    
                    const statusTexts = {
                        'scan': '<?php _e('Crawling live pages...', 'adverto-master'); ?>',
                        'ai': getAIProcessingText(processingType),
                        'generate': '<?php _e('Generating LLMs.txt structure...', 'adverto-master'); ?>',
                        'save': '<?php _e('Finalising & publishing...', 'adverto-master'); ?>'
                    };
                    
                    $('#progress-status').text(statusTexts[progressSteps[currentStepIndex]]);
                }
            }
            
            // Stop at 95% and wait for actual completion
            if (currentProgress >= 95) {
                clearInterval(window.progressInterval);
                $('#progress-status').text('<?php _e('Completing final processing...', 'adverto-master'); ?>');
            }
        }
        
        function getAIProcessingText(processingType) {
            switch (processingType) {
                case 'ai_summaries':
                    return '<?php _e('Generating AI content summaries...', 'adverto-master'); ?>';
                case 'key_points':
                    return '<?php _e('Extracting key points with AI...', 'adverto-master'); ?>';
                case 'structured':
                    return '<?php _e('Creating structured AI analysis...', 'adverto-master'); ?>';
                default:
                    return '<?php _e('Processing content...', 'adverto-master'); ?>';
            }
        }
        
        function completeProgress(success) {
            if (window.progressInterval) {
                clearInterval(window.progressInterval);
            }
            
            // Complete all steps
            $('.step').removeClass('active').addClass('complete')
                .find('.step-status')
                .html('<span class="material-icons">' + (success ? 'check_circle' : 'error') + '</span>');
            
            // Complete progress bar
            $('#progress-fill').css('width', '100%');
            $('#progress-percent').text('100%');
            $('#progress-status').text(success ? 
                '<?php _e('Generation complete!', 'adverto-master'); ?>' : 
                '<?php _e('Processing failed', 'adverto-master'); ?>');
        }
        
        function showSuccessMessage(message) {
            // You can customize this to show a nice success notification
            alert(message);
        }
        
        function showErrorMessage(message) {
            // You can customize this to show a nice error notification  
            alert(message);
        }
        
        function hideModal() {
            console.log('Hiding modal');
            $('#processing-modal').removeClass('active').css('display', 'none');
            $('body').css('overflow', 'auto');
        }
        
        // Close modal when clicking backdrop
        $(document).on('click', '.adverto-modal-backdrop', function() {
            // Only allow closing after completion
            if ($('#progress-percent').text() === '100%') {
                hideModal();
            }
        });
    });
    
})(jQuery);
</script>

<!-- Adverto Media Footer -->
<div class="adverto-footer">
    <div class="adverto-footer-content">
        <div class="footer-branding">
            <span class="material-icons">emoji_objects</span>
            <div class="branding-text">
                <strong>Powered by Adverto Media</strong>
                <p>Award-winning digital marketing agency based in Gloucester, England</p>
                <p><a href="https://advertomedia.co.uk/about-us/" target="_blank">Learn more about our services</a></p>
            </div>
        </div>
        <div class="footer-tech">
            <p>Intelligent LLMs.txt generation • AI-powered content analysis • Professional web crawling</p>
        </div>
    </div>
</div>
