<?php
/**
 * Alt Text Generator AI view
 * Beautiful Material Design interface for generating AI-powered alt texts
 */

$settings = get_option('adverto_master_settings', array());
$api_key = isset($settings['openai_api_key']) ? $settings['openai_api_key'] : '';
?>

<div class="adverto-container">
    <!-- Header -->
    <div class="adverto-header">
        <h1>
            <span class="material-icons">image</span>
            <?php _e('Alt Text Generator AI', 'adverto-master'); ?>
        </h1>
        <div class="adverto-breadcrumb">
            <a href="<?php echo admin_url('admin.php?page=adverto-master'); ?>"><?php _e('Dashboard', 'adverto-master'); ?></a>
            <span> / </span>
            <span><?php _e('Alt Text Generator AI', 'adverto-master'); ?></span>
        </div>
    </div>

    <!-- Content -->
    <div class="adverto-content">
        
        <?php if (empty($api_key)): ?>
        <!-- API Key Required Alert -->
        <div class="adverto-alert warning">
            <i class="material-icons">key</i>
            <span>
                <?php _e('OpenAI API key is required to use this tool. ', 'adverto-master'); ?>
                <a href="<?php echo admin_url('admin.php?page=adverto-master-settings'); ?>" class="adverto-btn-link">
                    <?php _e('Configure it now', 'adverto-master'); ?>
                </a>
            </span>
        </div>
        <?php endif; ?>

        <!-- Tool Card -->
        <div class="adverto-card">
            <div class="adverto-card-header">
                <h3 class="adverto-card-title">
                    <span class="material-icons">auto_fix_high</span>
                    <?php _e('Generate Alt Texts', 'adverto-master'); ?>
                </h3>
                <div class="adverto-card-subtitle">
                    <?php _e('Select images and let AI generate descriptive alt texts for better SEO and accessibility', 'adverto-master'); ?>
                </div>
            </div>
            
            <div class="adverto-card-content">
                <!-- Action Buttons -->
                <div class="adverto-actions">
                    <button id="adverto-select-images" class="adverto-btn adverto-btn-primary" data-tooltip="Select images from media library">
                        <span class="material-icons">photo_library</span>
                        <?php _e('Select Images', 'adverto-master'); ?>
                    </button>
                    
                    <button id="adverto-generate-alt" class="adverto-btn adverto-btn-secondary" disabled data-tooltip="Generate AI alt texts for selected images">
                        <span class="material-icons">psychology</span>
                        <?php _e('Generate Alt Texts', 'adverto-master'); ?>
                    </button>
                </div>

                <!-- Selected Images Preview -->
                <div id="adverto-selected-images" class="adverto-section" style="display: none;">
                    <h4><?php _e('Selected Images', 'adverto-master'); ?> <span id="adverto-image-count" class="adverto-chip"></span></h4>
                    <div id="adverto-image-preview" class="adverto-image-grid"></div>
                </div>

                <!-- Loading State -->
                <div id="adverto-loading" class="adverto-loading" style="display: none;">
                    <div class="adverto-spinner"></div>
                    <div class="adverto-loading-text adverto-loading-pulse">
                        <?php _e('AI is analysing images and generating alt texts...', 'adverto-master'); ?>
                    </div>
                </div>

                <!-- Progress Bar -->
                <div id="adverto-progress-container" class="adverto-progress" style="display: none;">
                    <div id="adverto-progress-bar" class="adverto-progress-bar" style="width: 0%;"></div>
                </div>

                <!-- Results Table -->
                <div id="adverto-results" class="adverto-section" style="display: none;">
                    <h4><?php _e('Generated Alt Texts', 'adverto-master'); ?></h4>
                    <div class="adverto-table-container">
                        <table class="adverto-table">
                            <thead>
                                <tr>
                                    <th style="width: 120px;"><?php _e('Image', 'adverto-master'); ?></th>
                                    <th style="width: 200px;"><?php _e('Current Alt Text', 'adverto-master'); ?></th>
                                    <th><?php _e('Generated Alt Text', 'adverto-master'); ?></th>
                                    <th style="width: 100px;"><?php _e('Actions', 'adverto-master'); ?></th>
                                </tr>
                            </thead>
                            <tbody id="adverto-results-body">
                                <!-- Results will be populated here -->
                            </tbody>
                        </table>
                    </div>

                    <div class="adverto-card-actions" style="margin-top: 20px;">
                        <button id="adverto-save-all" class="adverto-btn adverto-btn-success" style="display: none;">
                            <span class="material-icons">save</span>
                            <?php _e('Save All Alt Texts', 'adverto-master'); ?>
                        </button>
                        
                        <button id="adverto-select-all" class="adverto-btn adverto-btn-secondary">
                            <span class="material-icons">select_all</span>
                            <?php _e('Select All', 'adverto-master'); ?>
                        </button>
                    </div>
                </div>

                <!-- Error Display -->
                <div id="adverto-error" class="adverto-alert error" style="display: none;">
                    <i class="material-icons">error</i>
                    <span id="adverto-error-message"></span>
                </div>
            </div>
        </div>

        <!-- Statistics Card -->
        <div class="adverto-card">
            <div class="adverto-card-header">
                <h3 class="adverto-card-title">
                    <span class="material-icons">analytics</span>
                    <?php _e('Statistics', 'adverto-master'); ?>
                </h3>
            </div>
            <div class="adverto-card-content">
                <div class="adverto-grid adverto-grid-3">
                    <div class="adverto-stat-mini">
                        <div class="adverto-stat-number">
                            <?php echo number_format(get_option('adverto_alt_texts_generated', 0)); ?>
                        </div>
                        <div class="adverto-stat-label"><?php _e('Total Generated', 'adverto-master'); ?></div>
                    </div>
                    
                    <div class="adverto-stat-mini">
                        <div class="adverto-stat-number">
                            <?php echo number_format(get_option('adverto_alt_texts_this_month', 0)); ?>
                        </div>
                        <div class="adverto-stat-label"><?php _e('This Month', 'adverto-master'); ?></div>
                    </div>
                    
                    <div class="adverto-stat-mini">
                        <div class="adverto-stat-number">85%</div>
                        <div class="adverto-stat-label"><?php _e('Accuracy Rate', 'adverto-master'); ?></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tips Card -->
        <div class="adverto-card">
            <div class="adverto-card-header">
                <h3 class="adverto-card-title">
                    <span class="material-icons">tips_and_updates</span>
                    <?php _e('Pro Tips', 'adverto-master'); ?>
                </h3>
            </div>
            <div class="adverto-card-content">
                <div class="adverto-tips-grid">
                    <div class="adverto-tip">
                        <div class="adverto-tip-icon">
                            <span class="material-icons">lightbulb</span>
                        </div>
                        <div class="adverto-tip-content">
                            <h4><?php _e('Keep it descriptive', 'adverto-master'); ?></h4>
                            <p><?php _e('Good alt texts describe what\'s in the image and its context, helping both accessibility and SEO.', 'adverto-master'); ?></p>
                        </div>
                    </div>
                    
                    <div class="adverto-tip">
                        <div class="adverto-tip-icon">
                            <span class="material-icons">psychology</span>
                        </div>
                        <div class="adverto-tip-content">
                            <h4><?php _e('Review AI suggestions', 'adverto-master'); ?></h4>
                            <p><?php _e('AI does great work, but always review and customise the suggestions to match your brand voice.', 'adverto-master'); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Alt Text Generator specific styles */
.adverto-actions {
    display: flex;
    gap: 16px;
    margin-bottom: 24px;
    flex-wrap: wrap;
}

.adverto-image-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
    gap: 16px;
    margin-top: 16px;
    padding: 20px;
    background: rgba(66, 133, 244, 0.02);
    border-radius: 8px;
    border: 2px dashed rgba(66, 133, 244, 0.2);
}

.adverto-image-item {
    position: relative;
    border-radius: 8px;
    overflow: hidden;
    transition: var(--transition);
    background: white;
    box-shadow: var(--shadow-1);
}

.adverto-image-item:hover {
    transform: scale(1.05);
    box-shadow: var(--shadow-2);
}

.adverto-image-item img {
    width: 100%;
    height: 120px;
    object-fit: cover;
    display: block;
}

.adverto-image-item .adverto-image-remove {
    position: absolute;
    top: 4px;
    right: 4px;
    width: 24px;
    height: 24px;
    border-radius: 50%;
    background: rgba(244, 67, 54, 0.9);
    color: white;
    border: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: var(--transition);
}

.adverto-image-item:hover .adverto-image-remove {
    opacity: 1;
}

.adverto-table-container {
    overflow-x: auto;
    border-radius: 8px;
    box-shadow: var(--shadow-1);
}

.adverto-result-row {
    transition: var(--transition);
}

.adverto-result-row.selected {
    background: rgba(66, 133, 244, 0.05);
}

.adverto-result-image {
    width: 80px;
    height: 80px;
    object-fit: cover;
    border-radius: 8px;
    box-shadow: var(--shadow-1);
}

.adverto-alt-textarea {
    width: 100%;
    min-height: 80px;
    padding: 12px;
    border: 2px solid var(--divider-color);
    border-radius: 8px;
    font-family: inherit;
    font-size: 14px;
    line-height: 1.4;
    resize: vertical;
    transition: var(--transition);
}

.adverto-alt-textarea:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(66, 133, 244, 0.1);
}

.adverto-result-checkbox {
    width: 18px;
    height: 18px;
    margin-right: 8px;
}

.adverto-stat-mini {
    text-align: center;
    padding: 20px;
    background: rgba(66, 133, 244, 0.02);
    border-radius: 8px;
}

.adverto-stat-mini .adverto-stat-number {
    font-size: 24px;
    font-weight: 300;
    color: var(--text-primary);
    margin-bottom: 4px;
}

.adverto-stat-mini .adverto-stat-label {
    font-size: 12px;
    color: var(--text-secondary);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.adverto-tips-grid {
    display: grid;
    gap: 20px;
}

.adverto-tip {
    display: flex;
    align-items: flex-start;
    gap: 16px;
    padding: 20px;
    background: rgba(76, 175, 80, 0.02);
    border-radius: 8px;
    border-left: 4px solid var(--success-color);
}

.adverto-tip-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: var(--success-color);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.adverto-tip-content h4 {
    margin: 0 0 8px 0;
    font-size: 16px;
    font-weight: 500;
    color: var(--text-primary);
}

.adverto-tip-content p {
    margin: 0;
    font-size: 14px;
    color: var(--text-secondary);
    line-height: 1.5;
}

.adverto-empty-state {
    text-align: center;
    padding: 60px 20px;
    color: var(--text-secondary);
}

.adverto-empty-state .material-icons {
    font-size: 72px;
    opacity: 0.3;
    margin-bottom: 16px;
    color: var(--primary-color);
}

@media (max-width: 768px) {
    .adverto-actions {
        flex-direction: column;
    }
    
    .adverto-image-grid {
        grid-template-columns: repeat(auto-fill, minmax(80px, 1fr));
    }
    
    .adverto-tips-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
jQuery(document).ready(function($) {
    let selectedImages = [];
    let generatedResults = [];

    // Select Images Button
    $('#adverto-select-images').on('click', function(e) {
        e.preventDefault();
        
        const frame = wp.media({
            title: '<?php _e('Select Images for Alt Text Generation', 'adverto-master'); ?>',
            button: { text: '<?php _e('Use Selected Images', 'adverto-master'); ?>' },
            multiple: true,
            library: { type: 'image' }
        });

        frame.on('select', function() {
            const attachments = frame.state().get('selection').toJSON();
            selectedImages = attachments;
            
            displaySelectedImages();
            $('#adverto-generate-alt').prop('disabled', false);
        });

        frame.open();
    });

    // Display selected images
    function displaySelectedImages() {
        if (selectedImages.length === 0) return;
        
        let previewHtml = '';
        selectedImages.forEach((image, index) => {
            previewHtml += `
                <div class="adverto-image-item" data-index="${index}">
                    <img src="${image.url}" alt="${image.alt || 'Selected image'}">
                    <button type="button" class="adverto-image-remove" onclick="removeImage(${index})">
                        <span class="material-icons" style="font-size: 16px;">close</span>
                    </button>
                </div>
            `;
        });
        
        $('#adverto-image-preview').html(previewHtml);
        $('#adverto-image-count').text(selectedImages.length + ' ' + '<?php _e('selected', 'adverto-master'); ?>');
        $('#adverto-selected-images').show().addClass('adverto-fade-in');
    }

    // Remove image from selection
    window.removeImage = function(index) {
        selectedImages.splice(index, 1);
        displaySelectedImages();
        
        if (selectedImages.length === 0) {
            $('#adverto-selected-images').hide();
            $('#adverto-generate-alt').prop('disabled', true);
        }
    };

    // Generate Alt Texts
    $('#adverto-generate-alt').on('click', function() {
        if (selectedImages.length === 0) {
            AdvertoAdmin.showAlert('<?php _e('Please select some images first.', 'adverto-master'); ?>', 'warning');
            return;
        }

        const imageIds = selectedImages.map(img => img.id);
        
        // Show loading
        $('#adverto-loading').show().addClass('adverto-fade-in');
        $('#adverto-progress-container').show();
        $('#adverto-error').hide();
        $('#adverto-results').hide();

        // Simulate progress
        let progress = 0;
        const progressInterval = setInterval(() => {
            progress += Math.random() * 15;
            if (progress > 90) progress = 90;
            $('#adverto-progress-bar').css('width', progress + '%');
        }, 500);

        // AJAX request
        $.ajax({
            url: adverto_ajax.ajax_url,
            method: 'POST',
            data: {
                action: 'adverto_generate_alt_texts',
                nonce: adverto_ajax.nonce,
                image_ids: imageIds
            },
            success: function(response) {
                clearInterval(progressInterval);
                $('#adverto-progress-bar').css('width', '100%');
                
                setTimeout(() => {
                    $('#adverto-loading').hide();
                    $('#adverto-progress-container').hide();
                    
                    if (response.success) {
                        generatedResults = response.data;
                        displayResults();
                        AdvertoAdmin.showAlert('<?php _e('Alt texts generated successfully!', 'adverto-master'); ?>', 'success');
                    } else {
                        showError(response.data);
                    }
                }, 500);
            },
            error: function(xhr, status, error) {
                clearInterval(progressInterval);
                $('#adverto-loading').hide();
                $('#adverto-progress-container').hide();
                showError('<?php _e('Network error. Please try again.', 'adverto-master'); ?>');
            }
        });
    });

    // Display results
    function displayResults() {
        let resultsHtml = '';
        
        generatedResults.forEach((result, index) => {
            resultsHtml += `
                <tr class="adverto-result-row" data-id="${result.id}">
                    <td>
                        <img src="${result.url}" alt="" class="adverto-result-image">
                    </td>
                    <td>
                        <div class="adverto-current-alt">
                            ${result.current_alt || '<em><?php _e('No alt text', 'adverto-master'); ?></em>'}
                        </div>
                    </td>
                    <td>
                        <textarea class="adverto-alt-textarea" data-id="${result.id}">${result.generated_alt}</textarea>
                    </td>
                    <td>
                        <label class="adverto-checkbox-label">
                            <input type="checkbox" class="adverto-result-checkbox" data-id="${result.id}" checked>
                            <span><?php _e('Save', 'adverto-master'); ?></span>
                        </label>
                    </td>
                </tr>
            `;
        });
        
        $('#adverto-results-body').html(resultsHtml);
        $('#adverto-results').show().addClass('adverto-fade-in');
        $('#adverto-save-all').show();
        
        // Bind checkbox events
        $('.adverto-result-checkbox').on('change', function() {
            const row = $(this).closest('tr');
            if (this.checked) {
                row.addClass('selected');
            } else {
                row.removeClass('selected');
            }
        });
    }

    // Select All checkbox
    $('#adverto-select-all').on('click', function() {
        const checkboxes = $('.adverto-result-checkbox');
        const allChecked = checkboxes.filter(':checked').length === checkboxes.length;
        
        checkboxes.prop('checked', !allChecked).trigger('change');
        $(this).find('.material-icons').text(allChecked ? 'select_all' : 'deselect');
    });

    // Save All Alt Texts
    $('#adverto-save-all').on('click', function() {
        const altTexts = [];
        
        $('.adverto-result-checkbox:checked').each(function() {
            const id = $(this).data('id');
            const altText = $(`.adverto-alt-textarea[data-id="${id}"]`).val();
            altTexts.push({ id: id, alt_text: altText });
        });
        
        if (altTexts.length === 0) {
            AdvertoAdmin.showAlert('<?php _e('Please select at least one alt text to save.', 'adverto-master'); ?>', 'warning');
            return;
        }
        
        const $button = $(this);
        const originalText = $button.html();
        $button.html('<div class="adverto-spinner" style="width: 20px; height: 20px; margin-right: 8px;"></div><?php _e('Saving...', 'adverto-master'); ?>').prop('disabled', true);
        
        $.ajax({
            url: adverto_ajax.ajax_url,
            method: 'POST',
            data: {
                action: 'adverto_save_alt_texts',
                nonce: adverto_ajax.nonce,
                alt_texts: altTexts
            },
            success: function(response) {
                $button.html(originalText).prop('disabled', false);
                
                if (response.success) {
                    AdvertoAdmin.showAlert(`<?php _e('Successfully saved', 'adverto-master'); ?> ${altTexts.length} <?php _e('alt texts!', 'adverto-master'); ?>`, 'success');
                } else {
                    showError(response.data);
                }
            },
            error: function() {
                $button.html(originalText).prop('disabled', false);
                showError('<?php _e('Network error. Please try again.', 'adverto-master'); ?>');
            }
        });
    });

    // Show error
    function showError(message) {
        $('#adverto-error-message').text(message);
        $('#adverto-error').show().addClass('adverto-fade-in');
    }
});
</script>
