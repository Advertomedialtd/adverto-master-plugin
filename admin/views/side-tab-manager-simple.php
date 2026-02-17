<?php
/**
 * Simple Side Tab Manager - Based on working original plugin
 */

// Get current settings and items using the original approach
$settings = get_option('adverto_side_tab_settings', array(
    'enabled' => 1,
    'position' => 'right',
    'background_color' => '#4285f4',
    'text_color' => '#ffffff',
    'hover_color' => '#3367d6'
));

$items = get_option('adverto_side_tab_items', array());
?>

<div class="adverto-container">
    <div class="adverto-header">
        <h1>
            <span class="material-icons">tab</span>
            <?php _e('Side Tab Manager', 'adverto-master'); ?>
        </h1>
        <div class="adverto-breadcrumb">
            <a href="<?php echo admin_url('admin.php?page=adverto-master'); ?>"><?php _e('Dashboard', 'adverto-master'); ?></a>
            <span> / </span>
            <span><?php _e('Side Tab Manager', 'adverto-master'); ?></span>
        </div>
    </div>

    <div class="adverto-content">
        <!-- Configuration Section -->
        <div class="adverto-card">
            <div class="adverto-card-header">
                <h2>
                    <span class="material-icons">settings</span>
                    <?php _e('Side Tab Configuration', 'adverto-master'); ?>
                </h2>
                <p><?php _e('Configure the appearance and behaviour of your side navigation tab', 'adverto-master'); ?></p>
            </div>
            
            <div class="adverto-card-content">
                <form method="post" action="options.php">
                    <?php settings_fields('adverto_side_tab_settings_group'); ?>
                    
                    <div class="adverto-form-grid">
                        <div class="adverto-form-group">
                            <label class="adverto-switch">
                                <input type="checkbox" name="adverto_side_tab_settings[enabled]" value="1" <?php checked($settings['enabled'], 1); ?>>
                                <span class="adverto-switch-slider"></span>
                            </label>
                            <label class="adverto-form-label"><?php _e('Enable Side Tab', 'adverto-master'); ?></label>
                            <small class="adverto-field-help"><?php _e('Show the side tab on your website', 'adverto-master'); ?></small>
                        </div>
                        
                        <div class="adverto-form-group">
                            <label for="side-tab-position"><?php _e('Position', 'adverto-master'); ?></label>
                            <select name="adverto_side_tab_settings[position]" id="side-tab-position" class="adverto-select">
                                <option value="right" <?php selected($settings['position'], 'right'); ?>><?php _e('Right Side', 'adverto-master'); ?></option>
                                <option value="left" <?php selected($settings['position'], 'left'); ?>><?php _e('Left Side', 'adverto-master'); ?></option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="adverto-form-grid">
                        <div class="adverto-form-group">
                            <label for="background-color"><?php _e('Background Colour', 'adverto-master'); ?></label>
                            <input type="text" name="adverto_side_tab_settings[background_color]" id="background-color" value="<?php echo esc_attr($settings['background_color']); ?>" class="color-picker" />
                        </div>
                        
                        <div class="adverto-form-group">
                            <label for="text-color"><?php _e('Text Colour', 'adverto-master'); ?></label>
                            <input type="text" name="adverto_side_tab_settings[text_color]" id="text-color" value="<?php echo esc_attr($settings['text_color']); ?>" class="color-picker" />
                        </div>
                        
                        <div class="adverto-form-group">
                            <label for="hover-color"><?php _e('Hover Colour', 'adverto-master'); ?></label>
                            <input type="text" name="adverto_side_tab_settings[hover_color]" id="hover-color" value="<?php echo esc_attr($settings['hover_color']); ?>" class="color-picker" />
                        </div>
                    </div>
                    
                    <?php submit_button(__('Save Settings', 'adverto-master'), 'primary', 'submit', false, array('class' => 'adverto-btn adverto-btn-primary')); ?>
                </form>
            </div>
        </div>

        <!-- Items Management Section -->
        <div class="adverto-card">
            <div class="adverto-card-header">
                <h2>
                    <span class="material-icons">list</span>
                    <?php _e('Side Tab Items', 'adverto-master'); ?>
                </h2>
                <p><?php _e('Manage the items that appear in your side tab', 'adverto-master'); ?></p>
            </div>
            
            <div class="adverto-card-content">
                <form method="post" action="options.php">
                    <?php settings_fields('adverto_side_tab_items_group'); ?>
                    
                    <div id="side-tab-items">
                        <?php if (empty($items)): ?>
                            <div class="adverto-empty-state" id="empty-state">
                                <div class="empty-state-icon">
                                    <span class="material-icons">tab_unselected</span>
                                </div>
                                <div class="empty-state-content">
                                    <h3><?php _e('No Items Yet', 'adverto-master'); ?></h3>
                                    <p><?php _e('Add your first side tab item to get started. Each item can have an icon, text, and link to any page.', 'adverto-master'); ?></p>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="side-tab-items-list">
                                <?php foreach ($items as $index => $item): ?>
                                    <div class="side-tab-item" data-index="<?php echo $index; ?>">
                                        <div class="item-header">
                                            <div class="item-number"><?php echo $index + 1; ?></div>
                                            <div class="item-preview">
                                                <?php if (!empty($item['icon'])): ?>
                                                    <img src="<?php echo esc_url($item['icon']); ?>" alt="" class="item-icon-preview">
                                                <?php else: ?>
                                                    <div class="item-icon-placeholder">
                                                        <span class="material-icons">image</span>
                                                    </div>
                                                <?php endif; ?>
                                                <div class="item-info">
                                                    <h4 class="item-title"><?php echo esc_html($item['text'] ?: __('Untitled Item', 'adverto-master')); ?></h4>
                                                    <p class="item-url"><?php echo esc_html($item['link'] ?: __('No link set', 'adverto-master')); ?></p>
                                                </div>
                                            </div>
                                            <div class="item-toggle">
                                                <span class="material-icons toggle-icon">expand_more</span>
                                            </div>
                                        </div>
                                        <div class="item-content">
                                            <div class="adverto-form-row">
                                                <div class="adverto-form-group">
                                                    <label class="adverto-form-label">
                                                        <span class="material-icons">image</span>
                                                        <?php _e('Icon URL', 'adverto-master'); ?>
                                                    </label>
                                                    <div class="adverto-input-group">
                                                        <input type="text" 
                                                               name="adverto_side_tab_items[<?php echo $index; ?>][icon]" 
                                                               value="<?php echo esc_attr($item['icon']); ?>" 
                                                               class="adverto-input icon-url" 
                                                               placeholder="<?php _e('Enter icon URL or upload an image', 'adverto-master'); ?>" />
                                                        <button type="button" class="adverto-btn adverto-btn-secondary upload-icon-btn" title="<?php _e('Upload Image', 'adverto-master'); ?>">
                                                            <span class="material-icons">cloud_upload</span>
                                                            <span class="btn-text"><?php _e('Upload', 'adverto-master'); ?></span>
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="adverto-form-group">
                                                    <label class="adverto-form-label">
                                                        <span class="material-icons">text_fields</span>
                                                        <?php _e('Display Text', 'adverto-master'); ?>
                                                    </label>
                                                    <input type="text" 
                                                           name="adverto_side_tab_items[<?php echo $index; ?>][text]" 
                                                           value="<?php echo esc_attr($item['text']); ?>" 
                                                           class="adverto-input item-text-input" 
                                                           placeholder="<?php _e('e.g., Contact Us, Get Quote', 'adverto-master'); ?>" 
                                                           required />
                                                </div>
                                            </div>
                                            <div class="adverto-form-row">
                                                <div class="adverto-form-group">
                                                    <label class="adverto-form-label">
                                                        <span class="material-icons">link</span>
                                                        <?php _e('Link URL', 'adverto-master'); ?>
                                                    </label>
                                                    <input type="url" 
                                                           name="adverto_side_tab_items[<?php echo $index; ?>][link]" 
                                                           value="<?php echo esc_attr($item['link']); ?>" 
                                                           class="adverto-input item-link-input" 
                                                           placeholder="<?php _e('https://example.com/contact', 'adverto-master'); ?>" 
                                                           required />
                                                </div>
                                                <div class="adverto-form-group">
                                                    <label class="adverto-form-label">
                                                        <span class="material-icons">open_in_new</span>
                                                        <?php _e('Link Target', 'adverto-master'); ?>
                                                    </label>
                                                    <select name="adverto_side_tab_items[<?php echo $index; ?>][target]" class="adverto-select">
                                                        <option value="_self" <?php selected($item['target'], '_self'); ?>><?php _e('Same Window', 'adverto-master'); ?></option>
                                                        <option value="_blank" <?php selected($item['target'], '_blank'); ?>><?php _e('New Window/Tab', 'adverto-master'); ?></option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="item-actions">
                                                <button type="button" class="adverto-btn adverto-btn-small adverto-btn-danger remove-item-btn" title="<?php _e('Remove this item', 'adverto-master'); ?>">
                                                    <span class="material-icons">delete</span>
                                                    <span class="btn-text"><?php _e('Remove', 'adverto-master'); ?></span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="adverto-form-actions">
                        <div class="form-actions-left">
                            <button type="button" id="add-new-item" class="adverto-btn adverto-btn-outline">
                                <span class="material-icons">add_circle</span>
                                <span class="btn-text"><?php _e('Add New Item', 'adverto-master'); ?></span>
                            </button>
                        </div>
                        <div class="form-actions-right">
                            <?php submit_button(__('Save All Items', 'adverto-master'), 'primary', 'submit', false, array('class' => 'adverto-btn adverto-btn-primary')); ?>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
(function($) {
    'use strict';
    
    $(document).ready(function() {
        console.log('Side Tab Manager JavaScript loaded');
        console.log('jQuery version:', $.fn.jquery);
        
        // Initialize WordPress color picker
        if ($.fn.wpColorPicker) {
            $('.color-picker').wpColorPicker();
        }
        
        // Initialize WordPress media uploader for icons
        let mediaUploader;
    
    $(document).on('click', '.upload-icon-btn', function(e) {
        e.preventDefault();
        
        const button = $(this);
        const urlField = button.siblings('.icon-url');
        
        // If the media frame already exists, reopen it
        if (mediaUploader) {
            mediaUploader.open();
            return;
        }
        
        // Create the media frame
        mediaUploader = wp.media({
            title: '<?php _e('Select Icon', 'adverto-master'); ?>',
            button: {
                text: '<?php _e('Use this icon', 'adverto-master'); ?>'
            },
            multiple: false,
            library: {
                type: ['image']
            }
        });
        
        // When an image is selected, run a callback
        mediaUploader.on('select', function() {
            const attachment = mediaUploader.state().get('selection').first().toJSON();
            urlField.val(attachment.url);
        });
        
        // Open the modal
        mediaUploader.open();
    });
    
    // Add new item
    $('#add-new-item').on('click', function() {
        let itemsContainer = $('#side-tab-items');
        
        // If empty state exists, check if we need to create the list container
        if ($('.adverto-empty-state').length > 0) {
            $('.adverto-empty-state').remove();
            itemsContainer.html('<div class="side-tab-items-list"></div>');
            itemsContainer = itemsContainer.find('.side-tab-items-list');
        } else {
            // Find or create the items list container
            if (itemsContainer.find('.side-tab-items-list').length === 0) {
                itemsContainer.html('<div class="side-tab-items-list"></div>');
            }
            itemsContainer = itemsContainer.find('.side-tab-items-list');
        }
        
        const index = $('.side-tab-item').length;
        
        const template = `
            <div class="side-tab-item" data-index="${index}">
                <div class="item-header">
                    <div class="item-number">${index + 1}</div>
                    <div class="item-preview">
                        <div class="item-icon-placeholder">
                            <span class="material-icons">image</span>
                        </div>
                        <div class="item-info">
                            <h4 class="item-title"><?php _e('New Item', 'adverto-master'); ?></h4>
                            <p class="item-url"><?php _e('No link set', 'adverto-master'); ?></p>
                        </div>
                    </div>
                    <div class="item-toggle">
                        <span class="material-icons toggle-icon">expand_more</span>
                    </div>
                </div>
                <div class="item-content">
                    <div class="adverto-form-row">
                        <div class="adverto-form-group">
                            <label class="adverto-form-label">
                                <span class="material-icons">image</span>
                                <?php _e('Icon URL', 'adverto-master'); ?>
                            </label>
                            <div class="adverto-input-group">
                                <input type="text" 
                                       name="adverto_side_tab_items[${index}][icon]" 
                                       value="" 
                                       class="adverto-input icon-url" 
                                       placeholder="<?php _e('Enter icon URL or upload an image', 'adverto-master'); ?>" />
                                <button type="button" class="adverto-btn adverto-btn-secondary upload-icon-btn" title="<?php _e('Upload Image', 'adverto-master'); ?>">
                                    <span class="material-icons">cloud_upload</span>
                                    <span class="btn-text"><?php _e('Upload', 'adverto-master'); ?></span>
                                </button>
                            </div>
                        </div>
                        <div class="adverto-form-group">
                            <label class="adverto-form-label">
                                <span class="material-icons">text_fields</span>
                                <?php _e('Display Text', 'adverto-master'); ?>
                            </label>
                            <input type="text" 
                                   name="adverto_side_tab_items[${index}][text]" 
                                   value="" 
                                   class="adverto-input item-text-input" 
                                   placeholder="<?php _e('e.g., Contact Us, Get Quote', 'adverto-master'); ?>" 
                                   required />
                        </div>
                    </div>
                    <div class="adverto-form-row">
                        <div class="adverto-form-group">
                            <label class="adverto-form-label">
                                <span class="material-icons">link</span>
                                <?php _e('Link URL', 'adverto-master'); ?>
                            </label>
                            <input type="url" 
                                   name="adverto_side_tab_items[${index}][link]" 
                                   value="" 
                                   class="adverto-input item-link-input" 
                                   placeholder="<?php _e('https://example.com/contact', 'adverto-master'); ?>" 
                                   required />
                        </div>
                        <div class="adverto-form-group">
                            <label class="adverto-form-label">
                                <span class="material-icons">open_in_new</span>
                                <?php _e('Link Target', 'adverto-master'); ?>
                            </label>
                            <select name="adverto_side_tab_items[${index}][target]" class="adverto-select">
                                <option value="_self"><?php _e('Same Window', 'adverto-master'); ?></option>
                                <option value="_blank"><?php _e('New Window/Tab', 'adverto-master'); ?></option>
                            </select>
                        </div>
                    </div>
                    <div class="item-actions">
                        <button type="button" class="adverto-btn adverto-btn-small adverto-btn-danger remove-item-btn" title="<?php _e('Remove this item', 'adverto-master'); ?>">
                            <span class="material-icons">delete</span>
                            <span class="btn-text"><?php _e('Remove', 'adverto-master'); ?></span>
                        </button>
                    </div>
                </div>
            </div>
        `;
        
        itemsContainer.append(template);
        
        // Focus on the new item's text field and auto-expand it
        setTimeout(() => {
            const newItem = itemsContainer.find('.side-tab-item').last();
            newItem.find('.item-content').show();
            newItem.find('.toggle-icon').addClass('rotated');
            newItem.find('.item-text-input').focus();
        }, 100);
    });
    
    // Toggle item content
    $(document).on('click', '.item-header', function() {
        const item = $(this).closest('.side-tab-item');
        const content = item.find('.item-content');
        const toggleIcon = item.find('.toggle-icon');
        
        content.slideToggle(300);
        toggleIcon.toggleClass('rotated');
    });
    
    // Real-time preview updates
    $(document).on('input', '.item-text-input', function() {
        const item = $(this).closest('.side-tab-item');
        const title = item.find('.item-title');
        const value = $(this).val().trim();
        
        title.text(value || '<?php _e('Untitled Item', 'adverto-master'); ?>');
    });
    
    $(document).on('input', '.item-link-input', function() {
        const item = $(this).closest('.side-tab-item');
        const url = item.find('.item-url');
        const value = $(this).val().trim();
        
        url.text(value || '<?php _e('No link set', 'adverto-master'); ?>');
    });
    
    $(document).on('input', '.icon-url', function() {
        const item = $(this).closest('.side-tab-item');
        const preview = item.find('.item-icon-preview, .item-icon-placeholder');
        const value = $(this).val().trim();
        
        if (value) {
            if (preview.is('.item-icon-placeholder')) {
                preview.replaceWith(`<img src="${value}" alt="" class="item-icon-preview">`);
            } else {
                preview.attr('src', value);
            }
        } else {
            if (preview.is('.item-icon-preview')) {
                preview.replaceWith(`
                    <div class="item-icon-placeholder">
                        <span class="material-icons">image</span>
                    </div>
                `);
            }
        }
    });
    
    // Remove item
    $(document).on('click', '.remove-item-btn', function() {
        if (confirm('<?php _e('Are you sure you want to remove this item?', 'adverto-master'); ?>')) {
            $(this).closest('.side-tab-item').remove();
            
            // Reindex remaining items
            $('.side-tab-item').each(function(index) {
                $(this).attr('data-index', index);
                $(this).find('.item-number').text(index + 1);
                $(this).find('input, select').each(function() {
                    const name = $(this).attr('name');
                    if (name) {
                        $(this).attr('name', name.replace(/\[\d+\]/, '[' + index + ']'));
                    }
                });
            });
            
            // Show empty state if no items
            if ($('.side-tab-item').length === 0) {
                $('#side-tab-items').html(`
                    <div class="adverto-empty-state" id="empty-state">
                        <div class="empty-state-icon">
                            <span class="material-icons">tab_unselected</span>
                        </div>
                        <div class="empty-state-content">
                            <h3><?php _e('No Items Yet', 'adverto-master'); ?></h3>
                            <p><?php _e('Add your first side tab item to get started. Each item can have an icon, text, and link to any page.', 'adverto-master'); ?></p>
                        </div>
                    </div>
                `);
            }
        }
    });
    
    // Debug click handlers
    $(document).on('click', '#add-new-item', function() {
        console.log('Add new item button clicked');
    });
    
    $(document).on('click', '.item-header', function() {
        console.log('Item header clicked');
    });
    
    }); // End document ready
})(jQuery); // End function wrapper
</script>
