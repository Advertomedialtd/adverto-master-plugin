<?php
/**
 * Settings view for Adverto Master Plugin
 * Beautiful Material Design interface for plugin configuration
 */

$settings = get_option('adverto_master_settings', array(
    'openai_api_key' => '',
    'theme_color' => '#4285f4',
    'enable_animations' => true,
    'enable_tooltips' => true,
));

// Handle form submission
if (isset($_POST['submit']) && wp_verify_nonce($_POST['_wpnonce'], 'adverto_settings_nonce')) {
    $new_settings = array(
        'openai_api_key' => sanitize_text_field($_POST['openai_api_key']),
        'theme_color' => sanitize_hex_color($_POST['theme_color']),
        'enable_animations' => isset($_POST['enable_animations']),
        'enable_tooltips' => isset($_POST['enable_tooltips']),
    );
    
    update_option('adverto_master_settings', $new_settings);
    
    // Update individual tool settings
    if (isset($_POST['alt_text_prompt'])) {
        update_option('adverto_alt_text_prompt', sanitize_textarea_field($_POST['alt_text_prompt']));
    }
    
    if (isset($_POST['seo_prompt'])) {
        update_option('adverto_seo_prompt', sanitize_textarea_field($_POST['seo_prompt']));
    }
    
    $settings = $new_settings;
    $success_message = __('Settings saved successfully!', 'adverto-master');
}

$alt_text_prompt = get_option('adverto_alt_text_prompt', 'Generate a descriptive alt text for this image for SEO purposes. Be concise but descriptive.');
$seo_prompt = get_option('adverto_seo_prompt', 'Generate an SEO-optimised title (up to 60 characters) and a meta description (up to 160 characters) for this page based on its content.');
?>

<div class="adverto-container">
    <!-- Header -->
    <div class="adverto-header">
        <h1>
            <span class="material-icons">settings</span>
            <?php _e('Settings', 'adverto-master'); ?>
        </h1>
        <div class="adverto-breadcrumb">
            <a href="<?php echo admin_url('admin.php?page=adverto-master'); ?>"><?php _e('Dashboard', 'adverto-master'); ?></a>
            <span> / </span>
            <span><?php _e('Settings', 'adverto-master'); ?></span>
        </div>
    </div>

    <!-- Content -->
    <div class="adverto-content">
        
        <?php if (isset($success_message)): ?>
        <!-- Success Alert -->
        <div class="adverto-alert success" data-auto-hide="5000">
            <i class="material-icons">check_circle</i>
            <span><?php echo esc_html($success_message); ?></span>
        </div>
        <?php endif; ?>

        <form method="post" action="" class="adverto-form">
            <?php wp_nonce_field('adverto_settings_nonce'); ?>
            
            <!-- API Configuration -->
            <div class="adverto-card">
                <div class="adverto-card-header">
                    <h3 class="adverto-card-title">
                        <span class="material-icons">key</span>
                        <?php _e('API Configuration', 'adverto-master'); ?>
                    </h3>
                    <div class="adverto-card-subtitle">
                        <?php _e('Configure your OpenAI API key for AI-powered features', 'adverto-master'); ?>
                    </div>
                </div>
                <div class="adverto-card-content">
                    <div class="adverto-form-group">
                        <label for="openai_api_key" class="adverto-label">
                            <span class="material-icons" style="font-size: 18px; vertical-align: middle; margin-right: 8px;">vpn_key</span>
                            <?php _e('OpenAI API Key', 'adverto-master'); ?>
                            <span class="required" style="color: var(--error-color);">*</span>
                        </label>
                        <div class="adverto-input-group">
                            <input 
                                type="password" 
                                id="openai_api_key" 
                                name="openai_api_key" 
                                value="<?php echo esc_attr($settings['openai_api_key']); ?>"
                                class="adverto-input"
                                placeholder="sk-..."
                                data-validate="required"
                            >
                            <button type="button" class="adverto-btn adverto-btn-secondary adverto-toggle-password" data-target="openai_api_key">
                                <span class="material-icons">visibility</span>
                            </button>
                        </div>
                        <div class="adverto-help-text">
                            <?php _e('Get your API key from', 'adverto-master'); ?> 
                            <a href="https://platform.openai.com/account/api-keys" target="_blank" class="adverto-link">
                                <?php _e('OpenAI Platform', 'adverto-master'); ?>
                                <span class="material-icons" style="font-size: 14px; vertical-align: middle;">open_in_new</span>
                            </a>
                        </div>
                    </div>

                    <?php if (!empty($settings['openai_api_key'])): ?>
                    <div class="adverto-api-status">
                        <div class="adverto-status-indicator">
                            <span class="adverto-status-dot success"></span>
                            <span class="adverto-status-text"><?php _e('API Key Configured', 'adverto-master'); ?></span>
                        </div>
                        <button type="button" class="adverto-btn adverto-btn-secondary adverto-test-api" data-tooltip="Test API connection">
                            <span class="material-icons">wifi_protected_setup</span>
                            <?php _e('Test Connection', 'adverto-master'); ?>
                        </button>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- AI Prompts Configuration -->
            <div class="adverto-card">
                <div class="adverto-card-header">
                    <h3 class="adverto-card-title">
                        <span class="material-icons">psychology</span>
                        <?php _e('AI Prompts Configuration', 'adverto-master'); ?>
                    </h3>
                    <div class="adverto-card-subtitle">
                        <?php _e('Customise the AI prompts for better results', 'adverto-master'); ?>
                    </div>
                </div>
                <div class="adverto-card-content">
                    <div class="adverto-form-group">
                        <label for="alt_text_prompt" class="adverto-label">
                            <span class="material-icons" style="font-size: 18px; vertical-align: middle; margin-right: 8px;">image</span>
                            <?php _e('Alt Text Generation Prompt', 'adverto-master'); ?>
                        </label>
                        <textarea 
                            id="alt_text_prompt" 
                            name="alt_text_prompt" 
                            rows="4" 
                            class="adverto-textarea"
                            placeholder="<?php _e('Enter your custom prompt for alt text generation...', 'adverto-master'); ?>"
                        ><?php echo esc_textarea($alt_text_prompt); ?></textarea>
                        <div class="adverto-help-text">
                            <?php _e('This prompt will be sent to the AI along with each image to generate alt texts.', 'adverto-master'); ?>
                        </div>
                    </div>

                    <div class="adverto-form-group">
                        <label for="seo_prompt" class="adverto-label">
                            <span class="material-icons" style="font-size: 18px; vertical-align: middle; margin-right: 8px;">search</span>
                            <?php _e('SEO Content Generation Prompt', 'adverto-master'); ?>
                        </label>
                        <textarea 
                            id="seo_prompt" 
                            name="seo_prompt" 
                            rows="4" 
                            class="adverto-textarea"
                            placeholder="<?php _e('Enter your custom prompt for SEO content generation...', 'adverto-master'); ?>"
                        ><?php echo esc_textarea($seo_prompt); ?></textarea>
                        <div class="adverto-help-text">
                            <?php _e('This prompt will be used to generate SEO titles and meta descriptions for your pages.', 'adverto-master'); ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Appearance Settings -->
            <div class="adverto-card">
                <div class="adverto-card-header">
                    <h3 class="adverto-card-title">
                        <span class="material-icons">palette</span>
                        <?php _e('Appearance Settings', 'adverto-master'); ?>
                    </h3>
                    <div class="adverto-card-subtitle">
                        <?php _e('Customise the look and feel of the plugin interface', 'adverto-master'); ?>
                    </div>
                </div>
                <div class="adverto-card-content">
                    <div class="adverto-form-group">
                        <label for="theme_color" class="adverto-label">
                            <span class="material-icons" style="font-size: 18px; vertical-align: middle; margin-right: 8px;">color_lens</span>
                            <?php _e('Theme Colour', 'adverto-master'); ?>
                        </label>
                        <div class="adverto-color-picker-group">
                            <input 
                                type="text" 
                                id="theme_color" 
                                name="theme_color" 
                                value="<?php echo esc_attr($settings['theme_color']); ?>"
                                class="adverto-color-picker"
                            >
                            <div class="adverto-color-presets">
                                <button type="button" class="adverto-color-preset" data-color="#4285f4" style="background: #4285f4;" data-tooltip="Google Blue"></button>
                                <button type="button" class="adverto-color-preset" data-color="#9c27b0" style="background: #9c27b0;" data-tooltip="Material Purple"></button>
                                <button type="button" class="adverto-color-preset" data-color="#f44336" style="background: #f44336;" data-tooltip="Material Red"></button>
                                <button type="button" class="adverto-color-preset" data-color="#4caf50" style="background: #4caf50;" data-tooltip="Material Green"></button>
                                <button type="button" class="adverto-color-preset" data-color="#ff9800" style="background: #ff9800;" data-tooltip="Material Orange"></button>
                            </div>
                        </div>
                    </div>

                    <div class="adverto-form-group">
                        <div class="adverto-switch-group">
                            <label class="adverto-switch">
                                <input type="checkbox" name="enable_animations" <?php checked($settings['enable_animations']); ?>>
                                <span class="adverto-switch-slider"></span>
                            </label>
                            <div class="adverto-switch-label">
                                <strong><?php _e('Enable Animations', 'adverto-master'); ?></strong>
                                <p><?php _e('Enable smooth animations and transitions throughout the interface', 'adverto-master'); ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="adverto-form-group">
                        <div class="adverto-switch-group">
                            <label class="adverto-switch">
                                <input type="checkbox" name="enable_tooltips" <?php checked($settings['enable_tooltips']); ?>>
                                <span class="adverto-switch-slider"></span>
                            </label>
                            <div class="adverto-switch-label">
                                <strong><?php _e('Enable Tooltips', 'adverto-master'); ?></strong>
                                <p><?php _e('Show helpful tooltips throughout the interface', 'adverto-master'); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="adverto-card-actions">
                <button type="submit" name="submit" class="adverto-btn adverto-btn-primary">
                    <span class="material-icons">save</span>
                    <?php _e('Save Settings', 'adverto-master'); ?>
                </button>
                
                <button type="button" class="adverto-btn adverto-btn-secondary adverto-reset-settings" data-confirm="<?php _e('Are you sure you want to reset all settings to defaults?', 'adverto-master'); ?>">
                    <span class="material-icons">restore</span>
                    <?php _e('Reset to Defaults', 'adverto-master'); ?>
                </button>
            </div>
        </form>

        <!-- System Information -->
        <div class="adverto-card">
            <div class="adverto-card-header">
                <h3 class="adverto-card-title">
                    <span class="material-icons">info</span>
                    <?php _e('System Information', 'adverto-master'); ?>
                </h3>
            </div>
            <div class="adverto-card-content">
                <div class="adverto-system-info">
                    <div class="adverto-info-item">
                        <span class="adverto-info-label"><?php _e('Plugin Version:', 'adverto-master'); ?></span>
                        <span class="adverto-info-value"><?php echo ADVERTO_MASTER_VERSION; ?></span>
                    </div>
                    <div class="adverto-info-item">
                        <span class="adverto-info-label"><?php _e('WordPress Version:', 'adverto-master'); ?></span>
                        <span class="adverto-info-value"><?php echo get_bloginfo('version'); ?></span>
                    </div>
                    <div class="adverto-info-item">
                        <span class="adverto-info-label"><?php _e('PHP Version:', 'adverto-master'); ?></span>
                        <span class="adverto-info-value"><?php echo PHP_VERSION; ?></span>
                    </div>
                    <div class="adverto-info-item">
                        <span class="adverto-info-label"><?php _e('Memory Limit:', 'adverto-master'); ?></span>
                        <span class="adverto-info-value"><?php echo ini_get('memory_limit'); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Settings specific styles */
.adverto-input-group {
    display: flex;
    gap: 8px;
    align-items: center;
}

.adverto-input-group .adverto-input {
    flex: 1;
}

.adverto-toggle-password {
    min-width: 48px;
    padding: 12px;
}

.adverto-api-status {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 16px;
    background: rgba(76, 175, 80, 0.05);
    border-radius: 8px;
    border: 1px solid rgba(76, 175, 80, 0.2);
    margin-top: 16px;
}

.adverto-status-indicator {
    display: flex;
    align-items: center;
    gap: 8px;
}

.adverto-status-dot {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: var(--success-color);
}

.adverto-status-text {
    font-weight: 500;
    color: var(--success-color);
}

.adverto-help-text {
    font-size: 13px;
    color: var(--text-secondary);
    margin-top: 8px;
    line-height: 1.4;
}

.adverto-link {
    color: var(--primary-color);
    text-decoration: none;
    font-weight: 500;
}

.adverto-link:hover {
    text-decoration: underline;
}

.adverto-color-picker-group {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.adverto-color-presets {
    display: flex;
    gap: 8px;
}

.adverto-color-preset {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    border: 2px solid rgba(0, 0, 0, 0.1);
    cursor: pointer;
    transition: var(--transition);
    padding: 0;
}

.adverto-color-preset:hover {
    transform: scale(1.1);
    box-shadow: var(--shadow-2);
}

.adverto-switch-group {
    display: flex;
    align-items: flex-start;
    gap: 16px;
}

.adverto-switch-label {
    flex: 1;
}

.adverto-switch-label p {
    margin: 4px 0 0 0;
    font-size: 14px;
    color: var(--text-secondary);
}

.adverto-system-info {
    display: grid;
    gap: 12px;
}

.adverto-info-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 0;
    border-bottom: 1px solid var(--divider-color);
}

.adverto-info-item:last-child {
    border-bottom: none;
}

.adverto-info-label {
    font-weight: 500;
    color: var(--text-secondary);
}

.adverto-info-value {
    font-family: 'Monaco', 'Menlo', monospace;
    font-size: 13px;
    background: rgba(0, 0, 0, 0.05);
    padding: 4px 8px;
    border-radius: 4px;
    color: var(--text-primary);
}

.required {
    font-size: 16px;
    margin-left: 4px;
}

.adverto-alert {
    border: 1px solid;
    border-radius: 8px;
    padding: 12px 16px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 12px;
}

.adverto-alert.success {
    background: #d4edda;
    border-color: #c3e6cb;
    color: #155724;
}

.adverto-alert.error {
    background: #f8d7da;
    border-color: #f5c6cb;
    color: #721c24;
}

.alert-close {
    margin-left: auto;
    background: none;
    border: none;
    cursor: pointer;
    padding: 4px;
    border-radius: 4px;
    display: flex;
    align-items: center;
}

.alert-close:hover {
    background: rgba(0,0,0,0.1);
}

@media (max-width: 768px) {
    .adverto-api-status {
        flex-direction: column;
        gap: 12px;
        align-items: stretch;
    }
    
    .adverto-switch-group {
        flex-direction: column;
        gap: 8px;
    }
    
    .adverto-info-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 4px;
    }
}
</style>

<script>
jQuery(document).ready(function($) {
    // Alert function
    function showAlert(message, type) {
        // Remove existing alerts
        $('.adverto-alert').remove();
        
        // Create new alert
        const alertClass = type === 'success' ? 'adverto-alert success' : 'adverto-alert error';
        const alertHtml = `
            <div class="${alertClass}" style="margin-bottom: 20px; padding: 12px 16px; border-radius: 8px; display: flex; align-items: center; gap: 12px;">
                <i class="material-icons">${type === 'success' ? 'check_circle' : 'error'}</i>
                <span>${message}</span>
                <button type="button" class="alert-close" style="margin-left: auto; background: none; border: none; cursor: pointer;">
                    <i class="material-icons">close</i>
                </button>
            </div>
        `;
        
        // Insert at top of content
        $('.adverto-content').prepend(alertHtml);
        
        // Auto-hide after 5 seconds
        setTimeout(function() {
            $('.adverto-alert').fadeOut();
        }, 5000);
        
        // Close button functionality
        $('.alert-close').on('click', function() {
            $(this).closest('.adverto-alert').fadeOut();
        });
    }
    
    // Toggle password visibility
    $('.adverto-toggle-password').on('click', function() {
        const target = $(this).data('target');
        const input = $('#' + target);
        const icon = $(this).find('.material-icons');
        
        if (input.attr('type') === 'password') {
            input.attr('type', 'text');
            icon.text('visibility_off');
        } else {
            input.attr('type', 'password');
            icon.text('visibility');
        }
    });

    // Color preset selection
    $('.adverto-color-preset').on('click', function() {
        const color = $(this).data('color');
        $('#theme_color').val(color).trigger('change');
    });

    // Test API connection
    $('.adverto-test-api').on('click', function() {
        const $button = $(this);
        const originalText = $button.html();
        
        $button.html('<div class="adverto-spinner" style="width: 16px; height: 16px; margin-right: 8px;"></div><?php _e('Testing...', 'adverto-master'); ?>').prop('disabled', true);
        
        $.ajax({
            url: adverto_ajax.ajax_url,
            method: 'POST',
            data: {
                action: 'adverto_test_api',
                nonce: adverto_ajax.nonce,
                api_key: $('#openai_api_key').val()
            },
            success: function(response) {
                $button.html(originalText).prop('disabled', false);
                
                if (response.success) {
                    showAlert('<?php _e('API connection successful!', 'adverto-master'); ?>', 'success');
                } else {
                    showAlert('<?php _e('API connection failed: ', 'adverto-master'); ?>' + response.data, 'error');
                }
            },
            error: function(xhr, status, error) {
                $button.html(originalText).prop('disabled', false);
                console.log('AJAX Error:', {xhr: xhr, status: status, error: error});
                showAlert('<?php _e('Network error during API test: ', 'adverto-master'); ?>' + error, 'error');
            }
        });
    });

    // Reset settings
    $('.adverto-reset-settings').on('click', function() {
        // Reset form to defaults
        $('#openai_api_key').val('');
        $('#theme_color').val('#4285f4');
        $('#alt_text_prompt').val('Generate a descriptive alt text for this image for SEO purposes. Be concise but descriptive.');
        $('#seo_prompt').val('Generate an SEO-optimised title (up to 60 characters) and a meta description (up to 160 characters) for this page based on its content.');
        $('input[name="enable_animations"]').prop('checked', true);
        $('input[name="enable_tooltips"]').prop('checked', true);
        
        AdvertoAdmin.showAlert('<?php _e('Settings reset to defaults.', 'adverto-master'); ?>', 'info');
    });
});
</script>
