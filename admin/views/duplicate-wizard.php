<?php
/**
 * Duplicate SEO Wizard view - Advanced page duplication and find/replace tool
 */
?>

<div class="adverto-container">
    <div class="adverto-header">
        <h1>
            <span class="material-icons">content_copy</span>
            <?php _e('Duplicate SEO Wizard', 'adverto-master'); ?>
        </h1>
        <div class="adverto-breadcrumb">
            <a href="<?php echo admin_url('admin.php?page=adverto-master'); ?>"><?php _e('Dashboard', 'adverto-master'); ?></a>
            <span> / </span>
            <span><?php _e('Duplicate SEO Wizard', 'adverto-master'); ?></span>
        </div>
    </div>

    <div class="adverto-content">
        <!-- Tool Selection Tabs -->
        <div class="adverto-tabs" id="wizard-tabs">
            <button class="adverto-tab-btn active" data-tab="duplicate"><?php _e('Page Duplicator', 'adverto-master'); ?></button>
            <button class="adverto-tab-btn" data-tab="multi-location"><?php _e('Multi-Page Location Duplicator', 'adverto-master'); ?></button>
            <button class="adverto-tab-btn" data-tab="scanner"><?php _e('Duplicate Scanner', 'adverto-master'); ?></button>
            <button class="adverto-tab-btn" data-tab="find-replace"><?php _e('Find & Replace', 'adverto-master'); ?></button>
        </div>

        <!-- Page Duplicator Tab -->
        <div class="adverto-tab-content" id="duplicate-tab">
            <div class="adverto-card">
                <div class="adverto-card-header">
                    <h2>
                        <span class="material-icons">copy_all</span>
                        <?php _e('Location-Based Page Duplicator', 'adverto-master'); ?>
                    </h2>
                    <p><?php _e('Duplicate pages for location-based SEO with automatic find & replace (perfect for "iPhone Cheltenham" → "iPhone Gloucester" type duplications)', 'adverto-master'); ?></p>
                </div>
                
                <div class="adverto-card-content">
                    <div class="duplicate-form">
                        <div class="adverto-form-group">
                            <label for="page-to-duplicate"><?php _e('Page to Duplicate', 'adverto-master'); ?></label>
                            <select id="page-to-duplicate" class="adverto-select">
                                <option value=""><?php _e('Choose page to duplicate...', 'adverto-master'); ?></option>
                            </select>
                        </div>
                        
                        <div class="adverto-form-group">
                            <label for="find-word"><?php _e('Find', 'adverto-master'); ?></label>
                            <input type="text" id="find-word" class="adverto-input" placeholder="<?php _e('e.g., Cheltenham', 'adverto-master'); ?>">
                            <small class="adverto-field-help"><?php _e('This word will be replaced in page title, content, and Yoast SEO fields', 'adverto-master'); ?></small>
                        </div>
                        
                        <div class="adverto-form-group">
                            <label for="duplicate-count"><?php _e('Number of Duplicates', 'adverto-master'); ?></label>
                            <input type="number" id="duplicate-count" class="adverto-input" value="4" min="1" max="10">
                            <small class="adverto-field-help"><?php _e('How many new pages to create', 'adverto-master'); ?></small>
                        </div>
                        
                        <div class="replace-fields">
                            <div class="adverto-form-group">
                                <label for="replace-1"><?php _e('Replace 1', 'adverto-master'); ?></label>
                                <input type="text" id="replace-1" class="adverto-input replace-field" placeholder="<?php _e('e.g., Gloucester', 'adverto-master'); ?>">
                            </div>
                            
                            <div class="adverto-form-group">
                                <label for="replace-2"><?php _e('Replace 2', 'adverto-master'); ?></label>
                                <input type="text" id="replace-2" class="adverto-input replace-field" placeholder="<?php _e('e.g., Bristol', 'adverto-master'); ?>">
                            </div>
                            
                            <div class="adverto-form-group">
                                <label for="replace-3"><?php _e('Replace 3', 'adverto-master'); ?></label>
                                <input type="text" id="replace-3" class="adverto-input replace-field" placeholder="<?php _e('e.g., Bath', 'adverto-master'); ?>">
                            </div>
                            
                            <div class="adverto-form-group">
                                <label for="replace-4"><?php _e('Replace 4', 'adverto-master'); ?></label>
                                <input type="text" id="replace-4" class="adverto-input replace-field" placeholder="<?php _e('e.g., Oxford', 'adverto-master'); ?>">
                            </div>
                        </div>
                        
                        <div class="adverto-checkbox-group">
                            <label class="adverto-checkbox">
                                <input type="checkbox" id="copy-yoast-seo" checked>
                                <span class="adverto-checkbox-mark"></span>
                                <?php _e('Copy Yoast SEO Title, Meta Description & Focus Keyword', 'adverto-master'); ?>
                            </label>
                            
                            <label class="adverto-checkbox">
                                <input type="checkbox" id="copy-featured-image" checked>
                                <span class="adverto-checkbox-mark"></span>
                                <?php _e('Copy Featured Image', 'adverto-master'); ?>
                            </label>
                            
                            <label class="adverto-checkbox">
                                <input type="checkbox" id="copy-custom-fields">
                                <span class="adverto-checkbox-mark"></span>
                                <?php _e('Copy All Custom Fields', 'adverto-master'); ?>
                            </label>
                        </div>
                        
                        <div class="adverto-alert info">
                            <span class="material-icons">info</span>
                            <strong><?php _e('Draft Mode:', 'adverto-master'); ?></strong> <?php _e('All duplicated pages will be created as drafts, allowing you to review and edit them before publishing.', 'adverto-master'); ?>
                        </div>
                        
                        <div class="adverto-form-actions">
                            <button type="button" id="duplicate-and-replace-btn" class="adverto-btn adverto-btn-primary" disabled>
                                <span class="material-icons">content_copy</span>
                                <?php _e('Create Draft Pages', 'adverto-master'); ?>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Progress Section -->
            <div class="adverto-card" id="duplicate-progress-section" style="display: none;">
                <div class="adverto-card-header">
                    <h2>
                        <span class="material-icons">hourglass_empty</span>
                        <?php _e('Creating Duplicate Pages...', 'adverto-master'); ?>
                    </h2>
                </div>
                
                <div class="adverto-card-content">
                    <div class="generation-progress">
                        <div class="progress-bar">
                            <div class="progress-fill" id="duplicate-progress-fill"></div>
                        </div>
                        <div class="progress-text">
                            <span id="duplicate-current-progress">0</span> / <span id="duplicate-total-progress">0</span> pages created
                        </div>
                    </div>
                    
                    <div id="duplicate-current-processing" class="current-processing">
                        <span class="material-icons">autorenew</span>
                        <span id="duplicate-current-page-title"><?php _e('Processing...', 'adverto-master'); ?></span>
                    </div>
                </div>
            </div>

            <!-- Results Section -->
            <div class="adverto-card" id="duplicate-results-section" style="display: none;">
                <div class="adverto-card-header">
                    <h2>
                        <span class="material-icons">task_alt</span>
                        <?php _e('Draft Pages Created Successfully!', 'adverto-master'); ?>
                    </h2>
                    <p><?php _e('Your duplicated pages have been created as drafts for you to review before publishing.', 'adverto-master'); ?></p>
                </div>
                
                <div class="adverto-card-content">
                    <div class="adverto-success-message">
                        <span class="material-icons">check_circle</span>
                        <div>
                            <h4><?php _e('Draft Pages Created Successfully!', 'adverto-master'); ?></h4>
                            <p id="duplicate-final-summary"></p>
                            <div class="adverto-alert info">
                                <span class="material-icons">info</span>
                                <?php _e('All duplicated pages are created as <strong>drafts</strong> so you can review and edit them before publishing. This ensures quality control and prevents accidental publishing of unfinished content.', 'adverto-master'); ?>
                            </div>
                            <div class="results-list" id="duplicate-results-list">
                                <!-- Results will be populated here -->
                            </div>
                            <div class="result-actions">
                                <button type="button" id="view-drafts-btn" class="adverto-btn adverto-btn-primary">
                                    <span class="material-icons">drafts</span>
                                    <?php _e('View All Draft Pages', 'adverto-master'); ?>
                                </button>
                                <button type="button" id="create-more-duplicates-btn" class="adverto-btn adverto-btn-secondary">
                                    <span class="material-icons">add</span>
                                    <?php _e('Create More Duplicates', 'adverto-master'); ?>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Multi-Page Location Duplicator Tab -->
        <div class="adverto-tab-content" id="multi-location-tab" style="display: none;">
            <div class="adverto-card">
                <div class="adverto-card-header">
                    <h2>
                        <span class="material-icons">location_on</span>
                        <?php _e('Multi-Page Location Duplicator', 'adverto-master'); ?>
                    </h2>
                    <p><?php _e('Select multiple pages and duplicate them all for a new location. Perfect for creating location-specific versions of service pages (e.g., iPhone Bristol, Android Birmingham → iPhone York, Android York).', 'adverto-master'); ?></p>
                </div>
                
                <div class="adverto-card-content">
                    <div class="multi-location-form">
                        <!-- Page Selection Section -->
                        <div class="adverto-form-section">
                            <h3><?php _e('1. Select Pages to Duplicate', 'adverto-master'); ?></h3>
                            <div class="adverto-form-group">
                                <div class="page-selection-controls">
                                    <button type="button" id="select-all-pages-btn" class="adverto-btn adverto-btn-secondary adverto-btn-small">
                                        <span class="material-icons">select_all</span>
                                        <?php _e('Select All', 'adverto-master'); ?>
                                    </button>
                                    <button type="button" id="clear-all-pages-btn" class="adverto-btn adverto-btn-secondary adverto-btn-small">
                                        <span class="material-icons">clear</span>
                                        <?php _e('Clear All', 'adverto-master'); ?>
                                    </button>
                                    <input type="text" id="page-search-filter" class="adverto-input" style="width: 300px;" placeholder="<?php _e('Search pages...', 'adverto-master'); ?>">
                                </div>
                                <div class="selected-pages-count">
                                    <strong id="selected-pages-count">0</strong> <?php _e('pages selected', 'adverto-master'); ?>
                                </div>
                            </div>
                            
                            <div class="pages-list" id="multi-pages-list">
                                <div class="adverto-loading" id="multi-pages-loading">
                                    <div class="adverto-spinner"></div>
                                    <p><?php _e('Loading pages...', 'adverto-master'); ?></p>
                                </div>
                            </div>
                        </div>

                        <!-- Location and Find/Replace Section -->
                        <div class="adverto-form-section">
                            <h3><?php _e('2. Configure Location Replacement', 'adverto-master'); ?></h3>
                            
                            <div class="adverto-form-group">
                                <label for="new-location"><?php _e('New Location Name', 'adverto-master'); ?></label>
                                <input type="text" id="new-location" class="adverto-input" placeholder="<?php _e('e.g., York', 'adverto-master'); ?>">
                                <small class="adverto-field-help"><?php _e('This will replace the old location names in all selected pages', 'adverto-master'); ?></small>
                            </div>
                        </div>

                        <!-- Selected Pages Configuration -->
                        <div class="adverto-form-section" id="selected-pages-config" style="display: none;">
                            <h3><?php _e('3. Configure Each Page', 'adverto-master'); ?></h3>
                            <p><?php _e('For each selected page, specify which word should be replaced with your new location:', 'adverto-master'); ?></p>
                            <div id="page-config-list">
                                <!-- Will be populated dynamically -->
                            </div>
                        </div>

                        <!-- Options -->
                        <div class="adverto-form-section">
                            <h3><?php _e('4. Duplication Options', 'adverto-master'); ?></h3>
                            <div class="adverto-checkbox-group">
                                <label class="adverto-checkbox">
                                    <input type="checkbox" id="multi-copy-yoast-seo" checked>
                                    <span class="adverto-checkbox-mark"></span>
                                    <?php _e('Copy Yoast SEO Title, Meta Description & Focus Keyword', 'adverto-master'); ?>
                                </label>
                                
                                <label class="adverto-checkbox">
                                    <input type="checkbox" id="multi-copy-featured-image" checked>
                                    <span class="adverto-checkbox-mark"></span>
                                    <?php _e('Copy Featured Images', 'adverto-master'); ?>
                                </label>
                                
                                <label class="adverto-checkbox">
                                    <input type="checkbox" id="multi-copy-custom-fields">
                                    <span class="adverto-checkbox-mark"></span>
                                    <?php _e('Copy All Custom Fields', 'adverto-master'); ?>
                                </label>
                            </div>
                        </div>
                        
                        <div class="adverto-alert info">
                            <span class="material-icons">info</span>
                            <strong><?php _e('Draft Mode:', 'adverto-master'); ?></strong> <?php _e('All duplicated pages will be created as drafts for review before publishing.', 'adverto-master'); ?>
                        </div>
                        
                        <div class="adverto-form-actions">
                            <button type="button" id="create-location-pages-btn" class="adverto-btn adverto-btn-primary" disabled>
                                <span class="material-icons">add_location</span>
                                <?php _e('Create Location Pages', 'adverto-master'); ?>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Progress Section -->
            <div class="adverto-card" id="multi-location-progress-section" style="display: none;">
                <div class="adverto-card-header">
                    <h2>
                        <span class="material-icons">hourglass_empty</span>
                        <?php _e('Creating Location Pages...', 'adverto-master'); ?>
                    </h2>
                </div>
                
                <div class="adverto-card-content">
                    <div class="generation-progress">
                        <div class="progress-bar">
                            <div class="progress-fill" id="multi-location-progress-fill"></div>
                        </div>
                        <div class="progress-text">
                            <span id="multi-location-current-progress">0</span> / <span id="multi-location-total-progress">0</span> <?php _e('pages processed', 'adverto-master'); ?>
                        </div>
                    </div>
                    
                    <div id="multi-location-current-processing" class="current-processing">
                        <span class="material-icons">autorenew</span>
                        <span id="multi-location-current-page-title"><?php _e('Processing...', 'adverto-master'); ?></span>
                    </div>
                </div>
            </div>

            <!-- Results Section -->
            <div class="adverto-card" id="multi-location-results-section" style="display: none;">
                <div class="adverto-card-header">
                    <h2>
                        <span class="material-icons">task_alt</span>
                        <?php _e('Location Pages Created Successfully!', 'adverto-master'); ?>
                    </h2>
                    <p><?php _e('Your location-specific pages have been created as drafts for review.', 'adverto-master'); ?></p>
                </div>
                
                <div class="adverto-card-content">
                    <div class="adverto-success-message">
                        <span class="material-icons">check_circle</span>
                        <div>
                            <h4><?php _e('Location Pages Created Successfully!', 'adverto-master'); ?></h4>
                            <p id="multi-location-final-summary"></p>
                            <div class="adverto-alert info">
                                <span class="material-icons">info</span>
                                <?php _e('All location pages are created as <strong>drafts</strong>. Review and edit them before publishing.', 'adverto-master'); ?>
                            </div>
                            <div class="results-list" id="multi-location-results-list">
                                <!-- Results will be populated here -->
                            </div>
                            <div class="result-actions">
                                <button type="button" id="view-location-drafts-btn" class="adverto-btn adverto-btn-primary">
                                    <span class="material-icons">drafts</span>
                                    <?php _e('View All Draft Pages', 'adverto-master'); ?>
                                </button>
                                <button type="button" id="create-more-location-pages-btn" class="adverto-btn adverto-btn-secondary">
                                    <span class="material-icons">add</span>
                                    <?php _e('Create More Location Pages', 'adverto-master'); ?>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Duplicate Scanner Tab -->
        <div class="adverto-tab-content" id="scanner-tab" style="display: none;">
            <div class="adverto-card">
                <div class="adverto-card-header">
                    <h2>
                        <span class="material-icons">find_in_page</span>
                        <?php _e('Duplicate Content Scanner', 'adverto-master'); ?>
                    </h2>
                    <p><?php _e('Scan your website for duplicate or similar content across pages', 'adverto-master'); ?></p>
                </div>
                
                <div class="adverto-card-content">
                    <div class="adverto-form-grid">
                        <div class="adverto-form-group">
                            <label for="scan-post-type"><?php _e('Content Type', 'adverto-master'); ?></label>
                            <select id="scan-post-type" class="adverto-select">
                                <option value="page"><?php _e('Pages', 'adverto-master'); ?></option>
                                <option value="post"><?php _e('Posts', 'adverto-master'); ?></option>
                            </select>
                        </div>
                        
                        <div class="adverto-form-group">
                            <label for="similarity-threshold"><?php _e('Similarity Threshold', 'adverto-master'); ?></label>
                            <div class="adverto-range-input">
                                <input type="range" id="similarity-threshold" min="50" max="95" value="80" class="adverto-range">
                                <span class="adverto-range-value">80%</span>
                            </div>
                            <small class="adverto-field-help"><?php _e('Higher values find more exact duplicates', 'adverto-master'); ?></small>
                        </div>
                        
                        <div class="adverto-form-group">
                            <button type="button" id="scan-duplicates-btn" class="adverto-btn adverto-btn-primary">
                                <span class="material-icons">scanner</span>
                                <?php _e('Scan for Duplicates', 'adverto-master'); ?>
                            </button>
                        </div>
                    </div>
                    
                    <div id="scan-results" style="display: none;">
                        <div class="adverto-divider"></div>
                        <h3>
                            <span class="material-icons">assessment</span>
                            <?php _e('Duplicate Content Found', 'adverto-master'); ?>
                        </h3>
                        <div id="duplicate-groups"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Find & Replace Tab -->
        <div class="adverto-tab-content" id="find-replace-tab" style="display: none;">
            <div class="adverto-card">
                <div class="adverto-card-header">
                    <h2>
                        <span class="material-icons">find_replace</span>
                        <?php _e('Bulk Find & Replace', 'adverto-master'); ?>
                    </h2>
                    <p><?php _e('Find and replace text across multiple pages at once', 'adverto-master'); ?></p>
                </div>
                
                <div class="adverto-card-content">
                    <div class="adverto-form-grid">
                        <div class="adverto-form-group">
                            <label for="find-text"><?php _e('Find Text', 'adverto-master'); ?></label>
                            <input type="text" id="find-text" class="adverto-input" placeholder="<?php _e('Enter text to find...', 'adverto-master'); ?>">
                        </div>
                        
                        <div class="adverto-form-group">
                            <label for="replace-text"><?php _e('Replace With', 'adverto-master'); ?></label>
                            <input type="text" id="replace-text" class="adverto-input" placeholder="<?php _e('Enter replacement text...', 'adverto-master'); ?>">
                        </div>
                    </div>
                    
                    <div class="adverto-form-group">
                        <label><?php _e('Target Fields', 'adverto-master'); ?></label>
                        <div class="adverto-checkbox-group">
                            <label class="adverto-checkbox">
                                <input type="checkbox" name="target-fields" value="title">
                                <span class="adverto-checkbox-mark"></span>
                                <?php _e('Page Titles', 'adverto-master'); ?>
                            </label>
                            <label class="adverto-checkbox">
                                <input type="checkbox" name="target-fields" value="content" checked>
                                <span class="adverto-checkbox-mark"></span>
                                <?php _e('Page Content', 'adverto-master'); ?>
                            </label>
                            <label class="adverto-checkbox">
                                <input type="checkbox" name="target-fields" value="excerpt">
                                <span class="adverto-checkbox-mark"></span>
                                <?php _e('Page Excerpts', 'adverto-master'); ?>
                            </label>
                        </div>
                    </div>
                    
                    <div class="adverto-checkbox-group">
                        <label class="adverto-checkbox">
                            <input type="checkbox" id="case-sensitive">
                            <span class="adverto-checkbox-mark"></span>
                            <?php _e('Case sensitive search', 'adverto-master'); ?>
                        </label>
                    </div>
                    
                    <div class="adverto-form-group">
                        <label for="selected-pages"><?php _e('Select Pages', 'adverto-master'); ?></label>
                        <div class="adverto-page-selector" id="page-selector">
                            <div class="adverto-loading" id="pages-loading-replace">
                                <div class="adverto-spinner"></div>
                                <p><?php _e('Loading pages...', 'adverto-master'); ?></p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="adverto-form-actions">
                        <button type="button" id="preview-replace-btn" class="adverto-btn adverto-btn-secondary" disabled>
                            <span class="material-icons">preview</span>
                            <?php _e('Preview Changes', 'adverto-master'); ?>
                        </button>
                        <button type="button" id="apply-replace-btn" class="adverto-btn adverto-btn-primary" disabled>
                            <span class="material-icons">find_replace</span>
                            <?php _e('Apply Changes', 'adverto-master'); ?>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Results Section -->
        <div class="adverto-card" id="results-section" style="display: none;">
            <div class="adverto-card-header">
                <h2>
                    <span class="material-icons">task_alt</span>
                    <?php _e('Operation Results', 'adverto-master'); ?>
                </h2>
            </div>
            
            <div class="adverto-card-content">
                <div id="results-content"></div>
            </div>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    let allPages = [];
    let selectedPages = [];
    
    // Tab functionality
    $('.adverto-tab-btn').on('click', function() {
        const tabId = $(this).data('tab');
        
        $('.adverto-tab-btn').removeClass('active');
        $(this).addClass('active');
        
        $('.adverto-tab-content').hide();
        $('#' + tabId + '-tab').show();
        
        // Load pages when switching to find-replace tab
        if (tabId === 'find-replace' && allPages.length === 0) {
            loadPagesForReplace();
        }
    });
    
    // Range slider for similarity threshold
    $('#similarity-threshold').on('input', function() {
        $('.adverto-range-value').text($(this).val() + '%');
    });
    
    // Load pages for duplication
    loadPagesForDuplication();
    
    // Form validation for duplication
    $('#page-to-duplicate, #find-word, #duplicate-count, .replace-field').on('change keyup', function() {
        validateDuplicationForm();
    });
    
    // Number of duplicates change handler
    $('#duplicate-count').on('change', function() {
        const count = parseInt($(this).val()) || 0;
        updateReplaceFields(count);
        validateDuplicationForm();
    });
    
    // Initialize replace fields
    updateReplaceFields(4);
    
    // Duplicate and replace handler
    $('#duplicate-and-replace-btn').on('click', function() {
        startDuplicationProcess();
    });
    
    // Create more duplicates handler
    $('#create-more-duplicates-btn').on('click', function() {
        resetDuplicationForm();
    });
    
    // View drafts handler
    $('#view-drafts-btn').on('click', function() {
        window.open('<?php echo admin_url('edit.php?post_status=draft&post_type=page'); ?>', '_blank');
    });

    // Multi-Location Duplicator functionality
    let multiLocationAllPages = [];
    let multiLocationSelectedPages = [];
    let pageConfigs = {};

    // Load pages when switching to multi-location tab
    $('.adverto-tab-btn[data-tab="multi-location"]').on('click', function() {
        if (multiLocationAllPages.length === 0) {
            loadPagesForMultiLocation();
        }
    });

    // Page selection controls for multi-location
    $('#select-all-pages-btn').on('click', function() {
        $('.multi-page-checkbox').prop('checked', true);
        updateMultiLocationSelection();
    });

    $('#clear-all-pages-btn').on('click', function() {
        $('.multi-page-checkbox').prop('checked', false);
        updateMultiLocationSelection();
    });

    // Page search filter
    $('#page-search-filter').on('input', function() {
        const filterText = $(this).val().toLowerCase();
        $('.multi-page-item').each(function() {
            const pageTitle = $(this).find('.page-title').text().toLowerCase();
            const pageUrl = $(this).find('.page-url').text().toLowerCase();
            
            if (pageTitle.includes(filterText) || pageUrl.includes(filterText)) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });

    // New location input handler
    $('#new-location').on('input', function() {
        validateMultiLocationForm();
    });

    // Create location pages handler
    $('#create-location-pages-btn').on('click', function() {
        startMultiLocationDuplication();
    });

    // Results handlers for multi-location
    $('#view-location-drafts-btn').on('click', function() {
        window.open('<?php echo admin_url('edit.php?post_status=draft&post_type=page'); ?>', '_blank');
    });

    $('#create-more-location-pages-btn').on('click', function() {
        resetMultiLocationForm();
    });

    function loadPagesForMultiLocation() {
        $('#multi-pages-loading').show();
        
        $.post(ajaxurl, {
            action: 'adverto_fetch_pages',
            nonce: '<?php echo wp_create_nonce('adverto_nonce'); ?>'
        }, function(response) {
            if (response.success) {
                multiLocationAllPages = response.data;
                displayMultiLocationPages(multiLocationAllPages);
            } else {
                showNotification(response.data || 'Failed to load pages', 'error');
            }
        }).always(function() {
            $('#multi-pages-loading').hide();
        });
    }

    function displayMultiLocationPages(pages) {
        const pagesList = $('#multi-pages-list');
        pagesList.empty();
        
        if (pages.length === 0) {
            pagesList.append('<div class="adverto-empty-state">No pages found</div>');
            return;
        }

        pages.forEach(function(page) {
            const pageItem = $(`
                <label class="multi-page-item">
                    <input type="checkbox" value="${page.id}" class="multi-page-checkbox">
                    <span class="adverto-checkbox-mark"></span>
                    <div class="page-info">
                        <h4 class="page-title">${page.title}</h4>
                        <a href="${page.permalink}" target="_blank" class="page-url">${page.permalink}</a>
                    </div>
                </label>
            `);
            
            pageItem.find('.multi-page-checkbox').on('change', updateMultiLocationSelection);
            pagesList.append(pageItem);
        });
    }

    function updateMultiLocationSelection() {
        multiLocationSelectedPages = $('.multi-page-checkbox:checked').map(function() {
            return parseInt($(this).val());
        }).get();
        
        $('#selected-pages-count').text(multiLocationSelectedPages.length);
        
        // Show/hide page configuration section
        if (multiLocationSelectedPages.length > 0) {
            $('#selected-pages-config').show();
            generatePageConfigs();
        } else {
            $('#selected-pages-config').hide();
        }
        
        validateMultiLocationForm();
    }

    function generatePageConfigs() {
        const configList = $('#page-config-list');
        configList.empty();
        
        multiLocationSelectedPages.forEach(function(pageId) {
            const page = multiLocationAllPages.find(p => p.id === pageId);
            if (!page) return;
            
            const configItem = $(`
                <div class="page-config-item" data-page-id="${pageId}">
                    <div class="page-config-header">
                        <h4>${page.title}</h4>
                        <small>${page.permalink}</small>
                    </div>
                    <div class="page-config-fields">
                        <div class="adverto-form-group">
                            <label for="find-word-${pageId}"><?php _e('Find Word in This Page', 'adverto-master'); ?></label>
                            <input type="text" id="find-word-${pageId}" class="adverto-input page-find-input" 
                                   placeholder="<?php _e('e.g., Bristol, Birmingham, Oxford', 'adverto-master'); ?>" 
                                   data-page-id="${pageId}">
                            <small class="adverto-field-help"><?php _e('This word will be replaced with your new location in this page', 'adverto-master'); ?></small>
                        </div>
                    </div>
                </div>
            `);
            
            configItem.find('.page-find-input').on('input', function() {
                const pageId = $(this).data('page-id');
                pageConfigs[pageId] = $(this).val();
                validateMultiLocationForm();
            });
            
            configList.append(configItem);
        });
    }

    function validateMultiLocationForm() {
        const hasSelectedPages = multiLocationSelectedPages.length > 0;
        const hasNewLocation = $('#new-location').val().trim().length > 0;
        const allPagesConfigured = multiLocationSelectedPages.every(pageId => {
            return pageConfigs[pageId] && pageConfigs[pageId].trim().length > 0;
        });
        
        const isValid = hasSelectedPages && hasNewLocation && allPagesConfigured;
        $('#create-location-pages-btn').prop('disabled', !isValid);
    }

    function startMultiLocationDuplication() {
        const newLocation = $('#new-location').val().trim();
        const copyYoast = $('#multi-copy-yoast-seo').is(':checked');
        const copyFeaturedImage = $('#multi-copy-featured-image').is(':checked');
        const copyCustomFields = $('#multi-copy-custom-fields').is(':checked');
        
        // Show progress section
        $('#multi-location-progress-section').slideDown();
        $('#multi-location-total-progress').text(multiLocationSelectedPages.length);
        
        let results = [];
        let currentIndex = 0;
        
        function processNextPage() {
            if (currentIndex >= multiLocationSelectedPages.length) {
                showMultiLocationResults(results);
                return;
            }
            
            const pageId = multiLocationSelectedPages[currentIndex];
            const page = multiLocationAllPages.find(p => p.id === pageId);
            const findWord = pageConfigs[pageId];
            
            // Update progress
            $('#multi-location-current-progress').text(currentIndex + 1);
            $('#multi-location-current-page-title').text(`Processing: ${page.title}`);
            
            const progressPercent = ((currentIndex + 1) / multiLocationSelectedPages.length) * 100;
            $('#multi-location-progress-fill').css('width', progressPercent + '%');
            
            // Duplicate this page
            $.post(ajaxurl, {
                action: 'adverto_duplicate_page_with_replace',
                nonce: '<?php echo wp_create_nonce('adverto_nonce'); ?>',
                page_id: pageId,
                find_word: findWord,
                replace_word: newLocation,
                copy_yoast_seo: copyYoast,
                copy_featured_image: copyFeaturedImage,
                copy_custom_fields: copyCustomFields
            }, function(response) {
                if (response.success) {
                    results.push({
                        success: true,
                        page_id: response.data.new_page_id,
                        original_page_title: page.title,
                        new_page_title: response.data.new_page_title,
                        new_page_url: response.data.new_page_url,
                        original_word: findWord,
                        new_word: newLocation
                    });
                } else {
                    results.push({
                        success: false,
                        original_page_title: page.title,
                        original_word: findWord,
                        new_word: newLocation,
                        error: response.data
                    });
                }
                
                currentIndex++;
                setTimeout(processNextPage, 500);
            }).fail(function() {
                results.push({
                    success: false,
                    original_page_title: page.title,
                    original_word: findWord,
                    new_word: newLocation,
                    error: 'Network error occurred'
                });
                
                currentIndex++;
                setTimeout(processNextPage, 500);
            });
        }
        
        processNextPage();
    }

    function showMultiLocationResults(results) {
        $('#multi-location-progress-section').hide();
        $('#multi-location-results-section').slideDown();
        
        const successful = results.filter(r => r.success);
        const failed = results.filter(r => !r.success);
        
        let summary = `Successfully created ${successful.length} location page${successful.length !== 1 ? 's' : ''}`;
        if (failed.length > 0) {
            summary += ` with ${failed.length} error${failed.length !== 1 ? 's' : ''}`;
        }
        summary += '. Review and publish when ready.';
        
        $('#multi-location-final-summary').text(summary);
        
        // Show results list
        const resultsList = $('#multi-location-results-list');
        resultsList.empty();
        
        results.forEach(function(result) {
            let resultHtml;
            
            if (result.success) {
                resultHtml = `
                    <div class="result-item success">
                        <span class="material-icons">check_circle</span>
                        <div class="result-info">
                            <h4>${result.new_page_title}</h4>
                            <p>From "${result.original_page_title}" - replaced "${result.original_word}" with "${result.new_word}"</p>
                            <div class="result-status">
                                <span class="status-badge draft">
                                    <span class="material-icons">edit_note</span>
                                    <?php _e('Draft', 'adverto-master'); ?>
                                </span>
                            </div>
                            <div class="result-actions-inline">
                                <a href="<?php echo admin_url('post.php?action=edit&post='); ?>${result.page_id}" target="_blank" class="adverto-btn adverto-btn-small">
                                    <span class="material-icons">edit</span>
                                    <?php _e('Edit & Review', 'adverto-master'); ?>
                                </a>
                                <a href="${result.new_page_url}" target="_blank" class="adverto-btn adverto-btn-small adverto-btn-secondary">
                                    <span class="material-icons">preview</span>
                                    <?php _e('Preview', 'adverto-master'); ?>
                                </a>
                            </div>
                        </div>
                    </div>
                `;
            } else {
                resultHtml = `
                    <div class="result-item error">
                        <span class="material-icons">error</span>
                        <div class="result-info">
                            <h4>Failed: ${result.original_page_title}</h4>
                            <p>Attempted to replace "${result.original_word}" with "${result.new_word}"</p>
                            <p class="error-message">${result.error}</p>
                        </div>
                    </div>
                `;
            }
            
            resultsList.append(resultHtml);
        });
    }

    function resetMultiLocationForm() {
        $('#multi-location-results-section, #multi-location-progress-section').hide();
        $('#selected-pages-config').hide();
        $('.multi-page-checkbox').prop('checked', false);
        $('#new-location').val('');
        $('#page-search-filter').val('');
        pageConfigs = {};
        multiLocationSelectedPages = [];
        $('#selected-pages-count').text('0');
        validateMultiLocationForm();
        $('html, body').animate({scrollTop: 0}, 500);
    }
    
    function loadPagesForDuplication() {
        $.post(ajaxurl, {
            action: 'adverto_fetch_pages',
            nonce: '<?php echo wp_create_nonce('adverto_nonce'); ?>'
        }, function(response) {
            if (response.success) {
                const select = $('#page-to-duplicate');
                select.empty().append('<option value="">Choose page to duplicate...</option>');
                
                response.data.forEach(function(page) {
                    select.append(`<option value="${page.id}">${page.title}</option>`);
                });
            } else {
                showNotification(response.data, 'error');
            }
        });
    }
    
    function updateReplaceFields(count) {
        const maxFields = 4;
        
        for (let i = 1; i <= maxFields; i++) {
            const field = $(`#replace-${i}`).closest('.adverto-form-group');
            
            if (i <= count) {
                field.show();
            } else {
                field.hide();
                $(`#replace-${i}`).val('');
            }
        }
    }
    
    function validateDuplicationForm() {
        const pageSelected = $('#page-to-duplicate').val() !== '';
        const findWord = $('#find-word').val().trim() !== '';
        const count = parseInt($('#duplicate-count').val()) || 0;
        
        // Check if we have enough replacement words
        let replacementsFilled = 0;
        for (let i = 1; i <= count && i <= 4; i++) {
            if ($(`#replace-${i}`).val().trim() !== '') {
                replacementsFilled++;
            }
        }
        
        const isValid = pageSelected && findWord && count > 0 && replacementsFilled === count;
        $('#duplicate-and-replace-btn').prop('disabled', !isValid);
    }
    
    function startDuplicationProcess() {
        const pageId = $('#page-to-duplicate').val();
        const findWord = $('#find-word').val().trim();
        const count = parseInt($('#duplicate-count').val());
        const copyYoastSeo = $('#copy-yoast-seo').is(':checked');
        const copyFeaturedImage = $('#copy-featured-image').is(':checked');
        const copyCustomFields = $('#copy-custom-fields').is(':checked');
        
        // Collect replacement words
        const replacements = [];
        for (let i = 1; i <= count && i <= 4; i++) {
            const replaceWord = $(`#replace-${i}`).val().trim();
            if (replaceWord) {
                replacements.push(replaceWord);
            }
        }
        
        if (replacements.length !== count) {
            showNotification('Please fill in all replacement words.', 'error');
            return;
        }
        
        // Show progress section
        $('#duplicate-progress-section').slideDown();
        $('#duplicate-total-progress').text(count);
        
        // Hide other sections
        $('#duplicate-results-section').hide();
        
        // Start duplication process
        let currentIndex = 0;
        let results = [];
        
        function duplicateNext() {
            if (currentIndex >= replacements.length) {
                // All done, show results
                showDuplicationResults(results);
                return;
            }
            
            const replaceWord = replacements[currentIndex];
            const pageTitle = $(`#page-to-duplicate option:selected`).text();
            
            // Update progress
            $('#duplicate-current-progress').text(currentIndex + 1);
            $('#duplicate-current-page-title').text(`Creating: ${pageTitle.replace(findWord, replaceWord)}`);
            
            const progressPercent = ((currentIndex + 1) / count) * 100;
            $('#duplicate-progress-fill').css('width', progressPercent + '%');
            
            // Make duplication request
            $.post(ajaxurl, {
                action: 'adverto_duplicate_page_with_replace',
                nonce: '<?php echo wp_create_nonce('adverto_nonce'); ?>',
                page_id: pageId,
                find_word: findWord,
                replace_word: replaceWord,
                copy_yoast_seo: copyYoastSeo,
                copy_featured_image: copyFeaturedImage,
                copy_custom_fields: copyCustomFields
            }, function(response) {
                if (response.success) {
                    results.push({
                        success: true,
                        original_word: findWord,
                        new_word: replaceWord,
                        new_page_title: response.data.title,
                        new_page_url: response.data.url,
                        page_id: response.data.id
                    });
                } else {
                    results.push({
                        success: false,
                        original_word: findWord,
                        new_word: replaceWord,
                        error: response.data
                    });
                }
                
                currentIndex++;
                
                // Continue with next duplication after short delay
                setTimeout(duplicateNext, 500);
                
            }).fail(function() {
                results.push({
                    success: false,
                    original_word: findWord,
                    new_word: replaceWord,
                    error: 'Network error occurred'
                });
                
                currentIndex++;
                setTimeout(duplicateNext, 500);
            });
        }
        
        // Start the process
        duplicateNext();
    }
    
    function showDuplicationResults(results) {
        $('#duplicate-progress-section').hide();
        $('#duplicate-results-section').slideDown();
        
        const successful = results.filter(r => r.success);
        const failed = results.filter(r => !r.success);
        
        let summary = `Successfully created ${successful.length} draft page${successful.length !== 1 ? 's' : ''}`;
        if (failed.length > 0) {
            summary += ` with ${failed.length} error${failed.length !== 1 ? 's' : ''}`;
        }
        summary += '. Review and publish when ready.';
        
        $('#duplicate-final-summary').text(summary);
        
        // Show results list
        const resultsList = $('#duplicate-results-list');
        resultsList.empty();
        
        results.forEach(function(result) {
            let resultHtml;
            
            if (result.success) {
                resultHtml = `
                    <div class="result-item success">
                        <span class="material-icons">check_circle</span>
                        <div class="result-info">
                            <h4>${result.new_page_title}</h4>
                            <p>Replaced "${result.original_word}" with "${result.new_word}"</p>
                            <div class="result-status">
                                <span class="status-badge draft">
                                    <span class="material-icons">edit_note</span>
                                    <?php _e('Draft', 'adverto-master'); ?>
                                </span>
                            </div>
                            <div class="result-actions-inline">
                                <a href="<?php echo admin_url('post.php?action=edit&post='); ?>${result.page_id}" target="_blank" class="adverto-btn adverto-btn-small">
                                    <span class="material-icons">edit</span>
                                    <?php _e('Edit & Review', 'adverto-master'); ?>
                                </a>
                                <a href="${result.new_page_url}" target="_blank" class="adverto-btn adverto-btn-small adverto-btn-secondary">
                                    <span class="material-icons">preview</span>
                                    <?php _e('Preview', 'adverto-master'); ?>
                                </a>
                            </div>
                        </div>
                    </div>
                `;
            } else {
                resultHtml = `
                    <div class="result-item error">
                        <span class="material-icons">error</span>
                        <div class="result-info">
                            <h4>Failed: "${result.original_word}" → "${result.new_word}"</h4>
                            <p class="error-message">${result.error}</p>
                        </div>
                    </div>
                `;
            }
            
            resultsList.append(resultHtml);
        });
    }
    
    function resetDuplicationForm() {
        $('#duplicate-results-section, #duplicate-progress-section').hide();
        $('#page-to-duplicate').val('');
        $('#find-word').val('');
        $('#duplicate-count').val('4');
        $('.replace-field').val('');
        updateReplaceFields(4);
        validateDuplicationForm();
        $('html, body').animate({scrollTop: 0}, 500);
    }
    
    // Scan for duplicates
    $('#scan-duplicates-btn').on('click', function() {
        scanForDuplicates();
    });
    
    // Find & Replace functionality
    $('#find-text, input[name="target-fields"]').on('input change', function() {
        updateReplaceButtons();
    });
    
    $('#preview-replace-btn').on('click', function() {
        previewReplace();
    });
    
    $('#apply-replace-btn').on('click', function() {
        applyReplace();
    });
    
    function loadSourcePages() {
        $.post(ajaxurl, {
            action: 'adverto_fetch_pages',
            nonce: '<?php echo wp_create_nonce('adverto_nonce'); ?>'
        }, function(response) {
            if (response.success) {
                const select = $('#source-page');
                select.empty().append('<option value="">Select a page to duplicate...</option>');
                
                response.data.forEach(function(page) {
                    select.append(`<option value="${page.id}">${page.title}</option>`);
                });
                
                allPages = response.data;
            }
        });
    }
    
    function loadPagesForReplace() {
        if (allPages.length === 0) {
            $.post(ajaxurl, {
                action: 'adverto_fetch_pages',
                nonce: '<?php echo wp_create_nonce('adverto_nonce'); ?>'
            }, function(response) {
                if (response.success) {
                    allPages = response.data;
                    displayPagesForSelection();
                }
            }).always(function() {
                $('#pages-loading-replace').hide();
            });
        } else {
            displayPagesForSelection();
            $('#pages-loading-replace').hide();
        }
    }
    
    function displayPagesForSelection() {
        const container = $('#page-selector');
        container.empty();
        
        allPages.forEach(function(page) {
            const pageItem = $(`
                <label class="adverto-page-checkbox">
                    <input type="checkbox" value="${page.id}">
                    <span class="adverto-checkbox-mark"></span>
                    <span class="page-title">${page.title}</span>
                </label>
            `);
            
            pageItem.find('input').on('change', function() {
                updateSelectedPages();
            });
            
            container.append(pageItem);
        });
    }
    
    function updateSelectedPages() {
        selectedPages = [];
        $('#page-selector input:checked').each(function() {
            selectedPages.push(parseInt($(this).val()));
        });
        updateReplaceButtons();
    }
    
    function updateReplaceButtons() {
        const hasText = $('#find-text').val().trim() !== '';
        const hasPages = selectedPages.length > 0;
        const hasTargets = $('input[name="target-fields"]:checked').length > 0;
        
        const canPreview = hasText && hasPages && hasTargets;
        $('#preview-replace-btn, #apply-replace-btn').prop('disabled', !canPreview);
    }
    
    function duplicatePage() {
        const sourcePageId = $('#source-page').val();
        const newTitle = $('#new-page-title').val().trim();
        const newSlug = $('#new-page-slug').val().trim();
        const copyMeta = $('#copy-meta').is(':checked');
        
        if (!sourcePageId || !newTitle) {
            showNotification('Please select a source page and enter a new title.', 'error');
            return;
        }
        
        const $btn = $('#duplicate-page-btn');
        const originalText = $btn.text();
        
        $btn.prop('disabled', true).html('<span class="material-icons spinning">content_copy</span> Duplicating...');
        
        $.post(ajaxurl, {
            action: 'adverto_duplicate_pages',
            nonce: '<?php echo wp_create_nonce('adverto_nonce'); ?>',
            source_page_id: sourcePageId,
            new_title: newTitle,
            new_slug: newSlug,
            copy_meta: copyMeta
        }, function(response) {
            if (response.success) {
                const data = response.data;
                showResults(`
                    <div class="adverto-success-message">
                        <span class="material-icons">check_circle</span>
                        <div>
                            <h4>Page Duplicated Successfully!</h4>
                            <p>${data.message}</p>
                            <div class="result-actions">
                                <a href="${data.new_page_url}" target="_blank" class="adverto-btn adverto-btn-secondary">
                                    <span class="material-icons">open_in_new</span>
                                    View Page
                                </a>
                                <a href="${data.edit_url}" target="_blank" class="adverto-btn adverto-btn-primary">
                                    <span class="material-icons">edit</span>
                                    Edit Page
                                </a>
                            </div>
                        </div>
                    </div>
                `);
                
                // Reset form
                $('#new-page-title, #new-page-slug').val('');
                $('#source-page').val('');
                $btn.prop('disabled', true);
                
            } else {
                showNotification(response.data, 'error');
            }
        }).always(function() {
            $btn.prop('disabled', false).html('<span class="material-icons">content_copy</span>' + originalText.replace('Duplicating...', 'Duplicate Page'));
        });
    }
    
    function scanForDuplicates() {
        const postType = $('#scan-post-type').val();
        const threshold = $('#similarity-threshold').val();
        
        const $btn = $('#scan-duplicates-btn');
        const originalText = $btn.text();
        
        $btn.prop('disabled', true).html('<span class="material-icons spinning">scanner</span> Scanning...');
        
        $.post(ajaxurl, {
            action: 'adverto_scan_duplicates',
            nonce: '<?php echo wp_create_nonce('adverto_nonce'); ?>',
            post_type: postType,
            similarity_threshold: threshold
        }, function(response) {
            if (response.success) {
                displayDuplicateResults(response.data);
            } else {
                showNotification(response.data, 'error');
            }
        }).always(function() {
            $btn.prop('disabled', false).html('<span class="material-icons">scanner</span>' + originalText.replace('Scanning...', 'Scan for Duplicates'));
        });
    }
    
    function displayDuplicateResults(duplicates) {
        const container = $('#duplicate-groups');
        container.empty();
        
        if (duplicates.length === 0) {
            container.html('<div class="adverto-empty-state">No duplicate content found!</div>');
            $('#scan-results').show();
            return;
        }
        
        duplicates.forEach(function(group, index) {
            const groupHtml = $(`
                <div class="duplicate-group">
                    <h4>Duplicate Group ${index + 1}</h4>
                    <div class="duplicate-items"></div>
                </div>
            `);
            
            const itemsContainer = groupHtml.find('.duplicate-items');
            
            group.forEach(function(item, itemIndex) {
                const isOriginal = itemIndex === 0;
                const itemHtml = $(`
                    <div class="duplicate-item ${isOriginal ? 'original' : ''}">
                        <div class="item-info">
                            <h5>${item.title} ${isOriginal ? '(Original)' : ''}</h5>
                            <p>Similarity: ${item.similarity.toFixed(1)}% | Words: ${item.word_count}</p>
                            <a href="${item.permalink}" target="_blank">View Page</a>
                        </div>
                        <div class="similarity-bar">
                            <div class="similarity-fill" style="width: ${item.similarity}%"></div>
                        </div>
                    </div>
                `);
                
                itemsContainer.append(itemHtml);
            });
            
            container.append(groupHtml);
        });
        
        $('#scan-results').slideDown();
    }
    
    function previewReplace() {
        // Implementation for preview functionality
        showNotification('Preview functionality - showing potential changes without applying them', 'info');
    }
    
    function applyReplace() {
        const findText = $('#find-text').val();
        const replaceText = $('#replace-text').val();
        const targetFields = [];
        const caseSensitive = $('#case-sensitive').is(':checked');
        
        $('input[name="target-fields"]:checked').each(function() {
            targetFields.push($(this).val());
        });
        
        if (!findText || selectedPages.length === 0 || targetFields.length === 0) {
            showNotification('Please fill in all required fields.', 'error');
            return;
        }
        
        const $btn = $('#apply-replace-btn');
        const originalText = $btn.text();
        
        $btn.prop('disabled', true).html('<span class="material-icons spinning">find_replace</span> Processing...');
        
        $.post(ajaxurl, {
            action: 'adverto_find_replace_content',
            nonce: '<?php echo wp_create_nonce('adverto_nonce'); ?>',
            page_ids: selectedPages,
            find_text: findText,
            replace_text: replaceText,
            target_fields: targetFields,
            case_sensitive: caseSensitive
        }, function(response) {
            if (response.success) {
                displayReplaceResults(response.data);
            } else {
                showNotification(response.data, 'error');
            }
        }).always(function() {
            $btn.prop('disabled', false).html('<span class="material-icons">find_replace</span>' + originalText.replace('Processing...', 'Apply Changes'));
        });
    }
    
    function displayReplaceResults(results) {
        let html = `
            <div class="adverto-success-message">
                <span class="material-icons">check_circle</span>
                <div>
                    <h4>Find & Replace Completed!</h4>
                    <p>Successfully processed ${results.length} pages with changes.</p>
                </div>
            </div>
        `;
        
        if (results.length > 0) {
            html += '<div class="replace-results">';
            results.forEach(function(result) {
                html += `
                    <div class="result-item">
                        <h5>${result.page_title}</h5>
                        <a href="${result.permalink}" target="_blank">View Page</a>
                        <div class="changes">
                `;
                
                Object.keys(result.changes).forEach(function(field) {
                    const change = result.changes[field];
                    html += `
                        <div class="change-item">
                            <strong>${field}:</strong> ${change.occurrences} replacement(s) made
                        </div>
                    `;
                });
                
                html += '</div></div>';
            });
            html += '</div>';
        }
        
        showResults(html);
    }
    
    function showResults(html) {
        $('#results-content').html(html);
        $('#results-section').slideDown();
        
        $('html, body').animate({
            scrollTop: $('#results-section').offset().top - 50
        }, 500);
    }
    
    function showNotification(message, type = 'info') {
        if (type === 'error') {
            alert('Error: ' + message);
        } else {
            alert(message);
        }
    }
});
</script>

<style>
.adverto-tabs {
    display: flex;
    border-bottom: 2px solid var(--border-color);
    margin-bottom: 24px;
}

.adverto-tab-btn {
    padding: 12px 24px;
    background: none;
    border: none;
    cursor: pointer;
    font-size: 14px;
    font-weight: 500;
    color: var(--text-secondary);
    border-bottom: 2px solid transparent;
    transition: all 0.2s ease;
}

.adverto-tab-btn:hover {
    color: var(--primary-color);
}

.adverto-tab-btn.active {
    color: var(--primary-color);
    border-bottom-color: var(--primary-color);
}

.adverto-range-input {
    display: flex;
    align-items: center;
    gap: 12px;
}

.adverto-range {
    flex: 1;
    height: 6px;
    border-radius: 3px;
    background: var(--border-color);
    outline: none;
    appearance: none;
}

.adverto-range::-webkit-slider-thumb {
    appearance: none;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    background: var(--primary-color);
    cursor: pointer;
}

.adverto-range-value {
    font-weight: 600;
    color: var(--primary-color);
    min-width: 40px;
}

.adverto-page-selector {
    max-height: 300px;
    overflow-y: auto;
    border: 1px solid var(--border-color);
    border-radius: 8px;
    padding: 12px;
}

.adverto-page-checkbox {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px;
    cursor: pointer;
    border-radius: 4px;
    transition: background 0.2s ease;
}

.adverto-page-checkbox:hover {
    background: var(--hover-color);
}

.duplicate-group {
    margin-bottom: 24px;
    border: 1px solid var(--border-color);
    border-radius: 8px;
    padding: 16px;
}

.duplicate-group h4 {
    margin: 0 0 12px 0;
    color: var(--text-primary);
}

.duplicate-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px;
    margin-bottom: 8px;
    border-radius: 8px;
    background: var(--card-background);
}

.duplicate-item.original {
    background: var(--success-light);
    border: 1px solid var(--success-color);
}

.item-info h5 {
    margin: 0 0 4px 0;
    color: var(--text-primary);
}

.item-info p {
    margin: 0 0 4px 0;
    font-size: 14px;
    color: var(--text-secondary);
}

.item-info a {
    color: var(--primary-color);
    text-decoration: none;
    font-size: 14px;
}

.similarity-bar {
    width: 100px;
    height: 8px;
    background: var(--border-color);
    border-radius: 4px;
    overflow: hidden;
}

.similarity-fill {
    height: 100%;
    background: var(--primary-color);
    transition: width 0.3s ease;
}

.replace-results {
    margin-top: 20px;
}

.result-item {
    padding: 16px;
    border: 1px solid var(--border-color);
    border-radius: 8px;
    margin-bottom: 12px;
}

.result-item h5 {
    margin: 0 0 8px 0;
    color: var(--text-primary);
}

.changes {
    margin-top: 12px;
}

.change-item {
    padding: 4px 0;
    font-size: 14px;
    color: var(--text-secondary);
}

.result-actions {
    display: flex;
    gap: 12px;
    margin-top: 16px;
}

/* Duplication Form Styles */
.duplicate-form .replace-fields {
    display: grid;
    gap: 16px;
    margin: 24px 0;
}

.progress-bar {
    width: 100%;
    height: 8px;
    background: var(--border-color);
    border-radius: 4px;
    overflow: hidden;
    margin-bottom: 12px;
}

.progress-fill {
    height: 100%;
    background: var(--primary-color);
    transition: width 0.3s ease;
}

.progress-text {
    font-size: 16px;
    font-weight: 500;
    color: var(--text-primary);
    text-align: center;
    margin-bottom: 16px;
}

.current-processing {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
    padding: 16px;
    background: var(--hover-color);
    border-radius: 8px;
    font-weight: 500;
}

.current-processing .material-icons {
    animation: spin 1s linear infinite;
}

.results-list {
    max-height: 400px;
    overflow-y: auto;
    margin: 16px 0;
}

.result-item {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 16px;
    border: 1px solid var(--border-color);
    border-radius: 8px;
    margin-bottom: 12px;
    background: var(--card-background);
}

.result-item.success {
    border-left: 4px solid var(--success-color);
}

.result-item.error {
    border-left: 4px solid var(--error-color);
}

.result-item .material-icons {
    font-size: 20px;
}

.result-item.success .material-icons {
    color: var(--success-color);
}

.result-item.error .material-icons {
    color: var(--error-color);
}

.result-info {
    flex: 1;
}

.result-info h4 {
    margin: 0 0 8px 0;
    font-size: 16px;
    color: var(--text-primary);
}

.result-info p {
    margin: 0 0 8px 0;
    font-size: 14px;
    color: var(--text-secondary);
}

.error-message {
    color: var(--error-color);
}

.view-page-link {
    color: var(--primary-color);
    text-decoration: none;
    font-size: 14px;
}

.view-page-link:hover {
    text-decoration: underline;
}

.spinning {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
</style>
