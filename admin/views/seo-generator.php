<?php
/**
 * SEO Generator AI view - Bulk generate SEO titles and meta descriptions for multiple pages
 */
?>

<div class="adverto-container">
    <div class="adverto-header">
        <h1>
            <span class="material-icons">search</span>
            <?php _e('SEO Generator AI', 'adverto-master'); ?>
        </h1>
        <div class="adverto-breadcrumb">
            <a href="<?php echo admin_url('admin.php?page=adverto-master'); ?>"><?php _e('Dashboard', 'adverto-master'); ?></a>
            <span> / </span>
            <span><?php _e('SEO Generator AI', 'adverto-master'); ?></span>
        </div>
    </div>

    <div class="adverto-content">
        <!-- Page Selection Section -->
        <div class="adverto-card">
            <div class="adverto-card-header">
                <h2>
                    <span class="material-icons">checklist</span>
                    <?php _e('Select Pages for SEO Generation', 'adverto-master'); ?>
                </h2>
                <p><?php _e('Choose multiple pages to generate AI-powered SEO titles and meta descriptions in bulk', 'adverto-master'); ?></p>
            </div>
            
            <div class="adverto-card-content">
                <div class="adverto-form-group">
                    <div class="adverto-bulk-actions">
                        <button type="button" id="select-all-btn" class="adverto-btn adverto-btn-secondary">
                            <span class="material-icons">select_all</span>
                            <?php _e('Select All', 'adverto-master'); ?>
                        </button>
                        <button type="button" id="clear-selection-btn" class="adverto-btn adverto-btn-secondary">
                            <span class="material-icons">clear</span>
                            <?php _e('Clear Selection', 'adverto-master'); ?>
                        </button>
                        <span class="selection-count">
                            <strong id="selected-count">0</strong> <?php _e('pages selected', 'adverto-master'); ?>
                        </span>
                    </div>
                </div>
                
                <div class="adverto-form-group">
                    <label for="page-filter"><?php _e('Filter Pages', 'adverto-master'); ?></label>
                    <input type="text" id="page-filter" class="adverto-input" placeholder="<?php _e('Type to search pages...', 'adverto-master'); ?>">
                </div>
                
                <div class="adverto-page-list" id="page-list">
                    <div class="adverto-loading" id="pages-loading">
                        <div class="adverto-spinner"></div>
                        <p><?php _e('Loading pages...', 'adverto-master'); ?></p>
                    </div>
                </div>
                
                <div class="adverto-form-actions">
                    <button type="button" id="generate-bulk-seo-btn" class="adverto-btn adverto-btn-primary" disabled>
                        <span class="material-icons">auto_awesome</span>
                        <?php _e('Generate SEO Content for Selected Pages', 'adverto-master'); ?>
                    </button>
                </div>
            </div>
        </div>

        <!-- Generation Progress Section -->
        <div class="adverto-card" id="generation-progress-section" style="display: none;">
            <div class="adverto-card-header">
                <h2>
                    <span class="material-icons">hourglass_empty</span>
                    <?php _e('Generating SEO Content...', 'adverto-master'); ?>
                </h2>
                <p><?php _e('Please wait while AI generates SEO content for your selected pages', 'adverto-master'); ?></p>
            </div>
            
            <div class="adverto-card-content">
                <div class="generation-progress">
                    <div class="progress-bar">
                        <div class="progress-fill" id="progress-fill"></div>
                    </div>
                    <div class="progress-text">
                        <span id="current-progress">0</span> / <span id="total-progress">0</span> pages processed
                    </div>
                </div>
                
                <div id="current-processing" class="current-processing">
                    <span class="material-icons">autorenew</span>
                    <span id="current-page-title"><?php _e('Processing...', 'adverto-master'); ?></span>
                </div>
            </div>
        </div>

        <!-- Results Review Section -->
        <div class="adverto-card" id="results-review-section" style="display: none;">
            <div class="adverto-card-header">
                <h2>
                    <span class="material-icons">rate_review</span>
                    <?php _e('Review Generated Content', 'adverto-master'); ?>
                </h2>
                <p><?php _e('Review, edit, and approve the generated SEO content before saving', 'adverto-master'); ?></p>
            </div>
            
            <div class="adverto-card-content">
                <div class="review-actions">
                    <button type="button" id="approve-all-btn" class="adverto-btn adverto-btn-success">
                        <span class="material-icons">done_all</span>
                        <?php _e('Approve All & Save', 'adverto-master'); ?>
                    </button>
                    <button type="button" id="regenerate-selected-btn" class="adverto-btn adverto-btn-secondary">
                        <span class="material-icons">refresh</span>
                        <?php _e('Regenerate Selected', 'adverto-master'); ?>
                    </button>
                </div>
                
                <div id="results-list" class="results-list">
                    <!-- Generated results will be populated here -->
                </div>
            </div>
        </div>

        <!-- Final Results Section -->
        <div class="adverto-card" id="final-results-section" style="display: none;">
            <div class="adverto-card-header">
                <h2>
                    <span class="material-icons">task_alt</span>
                    <?php _e('SEO Content Saved Successfully!', 'adverto-master'); ?>
                </h2>
            </div>
            
            <div class="adverto-card-content">
                <div class="adverto-success-message">
                    <span class="material-icons">check_circle</span>
                    <div>
                        <h4><?php _e('Bulk SEO Generation Complete!', 'adverto-master'); ?></h4>
                        <p id="final-summary"></p>
                        <div class="result-actions">
                            <button type="button" id="generate-more-btn" class="adverto-btn adverto-btn-primary">
                                <span class="material-icons">add</span>
                                <?php _e('Generate More Pages', 'adverto-master'); ?>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    let allPages = [];
    let selectedPages = [];
    let generationResults = [];
    let currentProcessing = 0;
    
    // Load pages on initialization
    loadPages();
    
    // Page filter functionality
    $('#page-filter').on('input', function() {
        const filterText = $(this).val().toLowerCase();
        filterPages(filterText);
    });
    
    // Bulk actions
    $('#select-all-btn').on('click', selectAllPages);
    $('#clear-selection-btn').on('click', clearSelection);
    
    // Generation actions
    $('#generate-bulk-seo-btn').on('click', startBulkGeneration);
    $('#approve-all-btn').on('click', saveAllResults);
    $('#regenerate-selected-btn').on('click', regenerateSelected);
    $('#generate-more-btn').on('click', resetToStart);
    
    function loadPages() {
        $.post(ajaxurl, {
            action: 'adverto_fetch_pages',
            nonce: '<?php echo wp_create_nonce('adverto_nonce'); ?>'
        }, function(response) {
            if (response.success) {
                allPages = response.data;
                displayPages(allPages);
            } else {
                showNotification(response.data, 'error');
            }
        }).always(function() {
            $('#pages-loading').hide();
        });
    }
    
    function displayPages(pages) {
        const pageList = $('#page-list');
        pageList.empty();
        
        if (pages.length === 0) {
            pageList.append('<div class="adverto-empty-state">No pages found</div>');
            return;
        }
        
        pages.forEach(function(page) {
            const pageItem = $(`
                <label class="adverto-page-item">
                    <input type="checkbox" value="${page.id}" class="page-checkbox">
                    <span class="adverto-checkbox-mark"></span>
                    <div class="page-info">
                        <h4>${page.title}</h4>
                        <a href="${page.permalink}" target="_blank" class="page-url">${page.permalink}</a>
                    </div>
                </label>
            `);
            
            pageItem.find('.page-checkbox').on('change', updateSelection);
            pageList.append(pageItem);
        });
    }
    
    function filterPages(filterText) {
        const filtered = filterText ? 
            allPages.filter(page => 
                page.title.toLowerCase().includes(filterText) || 
                page.permalink.toLowerCase().includes(filterText)
            ) : allPages;
        
        displayPages(filtered);
        
        // Restore selections
        selectedPages.forEach(function(pageId) {
            $(`.page-checkbox[value="${pageId}"]`).prop('checked', true);
        });
    }
    
    function selectAllPages() {
        $('.page-checkbox').prop('checked', true);
        updateSelection();
    }
    
    function clearSelection() {
        $('.page-checkbox').prop('checked', false);
        updateSelection();
    }
    
    function updateSelection() {
        selectedPages = $('.page-checkbox:checked').map(function() {
            return parseInt($(this).val());
        }).get();
        
        $('#selected-count').text(selectedPages.length);
        $('#generate-bulk-seo-btn').prop('disabled', selectedPages.length === 0);
    }
    
    function startBulkGeneration() {
        if (selectedPages.length === 0) {
            showNotification('Please select at least one page.', 'error');
            return;
        }
        
        // Show progress section
        $('#generation-progress-section').slideDown();
        $('#total-progress').text(selectedPages.length);
        currentProcessing = 0;
        generationResults = [];
        
        // Start processing pages one by one
        processNextPage();
    }
    
    function processNextPage() {
        if (currentProcessing >= selectedPages.length) {
            // All pages processed, show results
            showResults();
            return;
        }
        
        const pageId = selectedPages[currentProcessing];
        const page = allPages.find(p => p.id === pageId);
        
        // Update progress
        $('#current-progress').text(currentProcessing + 1);
        $('#current-page-title').text(`Processing: ${page.title}`);
        
        const progressPercent = ((currentProcessing + 1) / selectedPages.length) * 100;
        $('#progress-fill').css('width', progressPercent + '%');
        
        // Generate SEO content for this page
        $.post(ajaxurl, {
            action: 'adverto_generate_seo_content',
            nonce: '<?php echo wp_create_nonce('adverto_nonce'); ?>',
            page_id: pageId,
            existing_content: ''
        }, function(response) {
            if (response.success) {
                generationResults.push({
                    page: page,
                    content: response.data,
                    selected: true
                });
            } else {
                generationResults.push({
                    page: page,
                    error: response.data,
                    selected: false
                });
            }
            
            currentProcessing++;
            
            // Process next page after short delay
            setTimeout(processNextPage, 500);
        }).fail(function() {
            generationResults.push({
                page: page,
                error: 'Network error occurred',
                selected: false
            });
            
            currentProcessing++;
            setTimeout(processNextPage, 500);
        });
    }
    
    function showResults() {
        $('#generation-progress-section').hide();
        $('#results-review-section').slideDown();
        
        const resultsList = $('#results-list');
        resultsList.empty();
        
        generationResults.forEach(function(result, index) {
            const hasError = result.error;
            const resultItem = $(`
                <div class="result-item ${hasError ? 'error' : ''}" data-index="${index}">
                    <div class="result-header">
                        <label class="adverto-checkbox">
                            <input type="checkbox" class="result-checkbox" ${result.selected ? 'checked' : ''} ${hasError ? 'disabled' : ''}>
                            <span class="adverto-checkbox-mark"></span>
                            <h4>${result.page.title}</h4>
                        </label>
                        <div class="result-actions">
                            ${!hasError ? `<button type="button" class="adverto-btn adverto-btn-small regenerate-single-btn" data-index="${index}">
                                <span class="material-icons">refresh</span>
                            </button>` : ''}
                        </div>
                    </div>
                    
                    ${hasError ? `
                        <div class="error-message">
                            <span class="material-icons">error</span>
                            <span>${result.error}</span>
                        </div>
                    ` : `
                        <div class="result-content">
                            <div class="content-field">
                                <label>SEO Title (<span class="char-count">${result.content.title ? result.content.title.length : 0}</span> chars)</label>
                                <input type="text" class="generated-title" value="${result.content.title || ''}" data-index="${index}">
                            </div>
                            
                            <div class="content-field">
                                <label>Meta Description (<span class="char-count">${result.content.description ? result.content.description.length : 0}</span> chars)</label>
                                <textarea class="generated-description" rows="2" data-index="${index}">${result.content.description || ''}</textarea>
                            </div>
                            
                            <div class="search-preview">
                                <div class="search-title">${result.content.title || result.page.title}</div>
                                <div class="search-url">${result.page.permalink}</div>
                                <div class="search-description">${result.content.description || 'No description'}</div>
                            </div>
                        </div>
                    `}
                </div>
            `);
            
            // Add event listeners
            resultItem.find('.result-checkbox').on('change', function() {
                const index = parseInt($(this).closest('.result-item').data('index'));
                generationResults[index].selected = $(this).is(':checked');
            });
            
            resultItem.find('.regenerate-single-btn').on('click', function() {
                const index = parseInt($(this).data('index'));
                regenerateSinglePage(index);
            });
            
            resultItem.find('.generated-title, .generated-description').on('input', function() {
                const index = parseInt($(this).data('index'));
                const field = $(this).hasClass('generated-title') ? 'title' : 'description';
                generationResults[index].content[field] = $(this).val();
                
                // Update character count
                $(this).siblings('label').find('.char-count').text($(this).val().length);
                
                // Update preview
                const previewItem = $(this).closest('.result-item');
                if (field === 'title') {
                    previewItem.find('.search-title').text($(this).val());
                } else {
                    previewItem.find('.search-description').text($(this).val());
                }
            });
            
            resultsList.append(resultItem);
        });
    }
    
    function regenerateSinglePage(index) {
        const result = generationResults[index];
        const $item = $(`.result-item[data-index="${index}"]`);
        
        $item.addClass('regenerating');
        
        $.post(ajaxurl, {
            action: 'adverto_generate_seo_content',
            nonce: '<?php echo wp_create_nonce('adverto_nonce'); ?>',
            page_id: result.page.id,
            existing_content: ''
        }, function(response) {
            if (response.success) {
                generationResults[index].content = response.data;
                generationResults[index].error = null;
                generationResults[index].selected = true;
                
                // Update the display
                showResults();
                showNotification('Content regenerated successfully!', 'success');
            } else {
                showNotification(response.data, 'error');
            }
        }).always(function() {
            $item.removeClass('regenerating');
        });
    }
    
    function regenerateSelected() {
        const selectedResults = generationResults
            .map((result, index) => ({result, index}))
            .filter(item => item.result.selected && !item.result.error);
        
        if (selectedResults.length === 0) {
            showNotification('Please select pages to regenerate.', 'error');
            return;
        }
        
        selectedResults.forEach(item => {
            regenerateSinglePage(item.index);
        });
    }
    
    function saveAllResults() {
        const selectedResults = generationResults.filter(result => result.selected && !result.error);
        
        if (selectedResults.length === 0) {
            showNotification('Please select pages to save.', 'error');
            return;
        }
        
        const $btn = $('#approve-all-btn');
        const originalHtml = $btn.html();
        $btn.prop('disabled', true).html('<span class="material-icons spinning">save</span> Saving...');
        
        let saved = 0;
        let errors = 0;
        
        selectedResults.forEach(function(result) {
            $.post(ajaxurl, {
                action: 'adverto_save_seo_content',
                nonce: '<?php echo wp_create_nonce('adverto_nonce'); ?>',
                page_id: result.page.id,
                seo_title: result.content.title,
                meta_description: result.content.description
            }, function(response) {
                if (response.success) {
                    saved++;
                } else {
                    errors++;
                }
                
                // Check if all requests completed
                if (saved + errors === selectedResults.length) {
                    showFinalResults(saved, errors);
                    $btn.prop('disabled', false).html(originalHtml);
                }
            });
        });
    }
    
    function showFinalResults(saved, errors) {
        $('#results-review-section').hide();
        $('#final-results-section').slideDown();
        
        let message = `Successfully saved SEO content for ${saved} page${saved !== 1 ? 's' : ''}`;
        if (errors > 0) {
            message += ` with ${errors} error${errors !== 1 ? 's' : ''}`;
        }
        message += '.';
        
        $('#final-summary').text(message);
    }
    
    function resetToStart() {
        $('#final-results-section, #results-review-section, #generation-progress-section').hide();
        clearSelection();
        $('html, body').animate({scrollTop: 0}, 500);
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
.adverto-bulk-actions {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 16px;
    padding: 16px;
    background: var(--hover-color);
    border-radius: 8px;
}

.selection-count {
    margin-left: auto;
    font-size: 14px;
    color: var(--text-secondary);
}

.adverto-page-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 16px;
    border: 1px solid var(--border-color);
    border-radius: 8px;
    margin-bottom: 12px;
    cursor: pointer;
    transition: all 0.2s ease;
}

.adverto-page-item:hover {
    border-color: var(--primary-color);
    box-shadow: 0 2px 8px rgba(66, 133, 244, 0.1);
}

.page-info {
    flex: 1;
}

.page-info h4 {
    margin: 0 0 4px 0;
    font-size: 16px;
    color: var(--text-primary);
}

.page-url {
    color: var(--primary-color);
    text-decoration: none;
    font-size: 14px;
}

.generation-progress {
    text-align: center;
    margin-bottom: 24px;
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

.review-actions {
    display: flex;
    gap: 12px;
    margin-bottom: 24px;
    padding-bottom: 24px;
    border-bottom: 1px solid var(--border-color);
}

.results-list {
    max-height: 600px;
    overflow-y: auto;
}

.result-item {
    border: 1px solid var(--border-color);
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 16px;
    background: var(--card-background);
}

.result-item.error {
    border-color: var(--error-color);
    background: var(--error-light);
}

.result-item.regenerating {
    opacity: 0.6;
    pointer-events: none;
}

.result-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 16px;
}

.result-header h4 {
    margin: 0;
    font-size: 16px;
    color: var(--text-primary);
}

.result-content {
    display: grid;
    gap: 16px;
}

.content-field label {
    display: block;
    margin-bottom: 4px;
    font-weight: 500;
    color: var(--text-primary);
}

.content-field input,
.content-field textarea {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid var(--border-color);
    border-radius: 6px;
    font-family: inherit;
}

.char-count {
    color: var(--primary-color);
    font-weight: 600;
}

.search-preview {
    background: #f8f9fa;
    border: 1px solid #e1e5e9;
    border-radius: 8px;
    padding: 16px;
    font-family: arial, sans-serif;
    margin-top: 12px;
}

.search-title {
    color: #1a0dab;
    font-size: 16px;
    font-weight: normal;
    margin-bottom: 4px;
}

.search-url {
    color: #006621;
    font-size: 13px;
    margin-bottom: 4px;
}

.search-description {
    color: #545454;
    font-size: 13px;
    line-height: 1.4;
}

.error-message {
    display: flex;
    align-items: center;
    gap: 8px;
    color: var(--error-color);
    background: var(--error-light);
    padding: 12px;
    border-radius: 6px;
}

.spinning {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

.adverto-success-message {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 16px;
    background: var(--success-light);
    border: 1px solid var(--success-color);
    border-radius: 8px;
}

.adverto-success-message .material-icons {
    color: var(--success-color);
    font-size: 24px;
}

.adverto-success-message h4 {
    margin: 0 0 4px 0;
    color: var(--success-color);
}

.result-actions {
    display: flex;
    gap: 12px;
    margin-top: 16px;
}
</style>
