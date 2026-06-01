<?php
/**
 * Dashboard view for Adverto Master Plugin
 * Beautiful Material Design inspired interface
 */

// Get usage statistics
$stats = array(
    'alt_texts_generated' => get_option('adverto_alt_texts_generated', 0),
    'seo_contents_generated' => get_option('adverto_seo_contents_generated', 0),
    'pages_duplicated' => get_option('adverto_pages_duplicated', 0),
    'side_tab_clicks' => get_option('adverto_side_tab_clicks', 0),
);

$settings = get_option('adverto_master_settings', array());
$api_key = isset($settings['openai_api_key']) ? $settings['openai_api_key'] : '';

// Check System Status
$php_version = phpversion();
$wp_version = get_bloginfo('version');
$memory_limit = ini_get('memory_limit');
$system_status = array(
    'Server Software' => $_SERVER['SERVER_SOFTWARE'],
    'PHP Version' => $php_version,
    'WordPress Version' => $wp_version,
    'Memory Limit' => $memory_limit
);
?>

<div class="adverto-container">
    <!-- Header -->
    <div class="adverto-header">
        <h1>
            <span class="material-icons">dashboard</span>
            <?php _e('Adverto Master Dashboard', 'adverto-master'); ?>
        </h1>
        <div class="adverto-breadcrumb">
            <span><?php _e('Welcome to your AI-powered marketing toolkit', 'adverto-master'); ?></span>
        </div>
    </div>

    <!-- Content -->
    <div class="adverto-content">
        
        <?php if (empty($api_key)): ?>
        <!-- API Key Warning -->
        <div class="adverto-alert warning" style="margin-bottom: 24px;">
            <i class="material-icons">key</i>
            <div style="flex: 1;">
                <strong><?php _e('OpenAI API Key Missing', 'adverto-master'); ?></strong>
                <p style="margin: 4px 0 0; opacity: 0.8;"><?php _e('Most AI features will not work without an API key.', 'adverto-master'); ?></p>
            </div>
            <a href="<?php echo admin_url('admin.php?page=adverto-master-settings'); ?>" class="adverto-btn adverto-btn-primary">
                <?php _e('Configure Now', 'adverto-master'); ?>
            </a>
        </div>
        <?php endif; ?>

        <!-- Statistics Grid -->
        <div class="adverto-grid adverto-grid-4" style="margin-bottom: 24px;">
            <div class="adverto-stat-card">
                <div class="adverto-stat-icon">
                    <i class="material-icons">image</i>
                </div>
                <div class="adverto-stat-number"><?php echo number_format($stats['alt_texts_generated']); ?></div>
                <div class="adverto-stat-label"><?php _e('Alt Texts Generated', 'adverto-master'); ?></div>
            </div>

            <div class="adverto-stat-card">
                <div class="adverto-stat-icon">
                    <i class="material-icons">search</i>
                </div>
                <div class="adverto-stat-number"><?php echo number_format($stats['seo_contents_generated']); ?></div>
                <div class="adverto-stat-label"><?php _e('SEO Optimisations', 'adverto-master'); ?></div>
            </div>

            <div class="adverto-stat-card">
                <div class="adverto-stat-icon">
                    <i class="material-icons">content_copy</i>
                </div>
                <div class="adverto-stat-number"><?php echo number_format($stats['pages_duplicated']); ?></div>
                <div class="adverto-stat-label"><?php _e('Pages Duplicated', 'adverto-master'); ?></div>
            </div>

            <div class="adverto-stat-card">
                <div class="adverto-stat-icon">
                    <i class="material-icons">touch_app</i>
                </div>
                <div class="adverto-stat-number"><?php echo number_format($stats['side_tab_clicks']); ?></div>
                <div class="adverto-stat-label"><?php _e('Tab Interactions', 'adverto-master'); ?></div>
            </div>
        </div>

        <div class="adverto-dashboard-two-col">
            <!-- Main Column -->
            <div class="adverto-main-col">
                
                <!-- Quick Actions -->
                <div class="adverto-card" style="margin-bottom: 20px;">
                    <div class="adverto-card-header">
                        <h3 class="adverto-card-title">
                            <span class="material-icons">bolt</span>
                            <?php _e('Quick Actions', 'adverto-master'); ?>
                        </h3>
                    </div>
                    <div class="adverto-card-content">
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 12px;">
                            <a href="<?php echo admin_url('admin.php?page=adverto-alt-text-generator'); ?>" class="adverto-action-btn">
                                <span class="material-icons">image</span>
                                <div>
                                    <div style="font-weight: 600; font-size: 13px;"><?php _e('Generate Alt Text', 'adverto-master'); ?></div>
                                    <div style="font-size: 12px; color: var(--text-secondary);"><?php _e('Optimise image SEO', 'adverto-master'); ?></div>
                                </div>
                            </a>

                            <a href="<?php echo admin_url('admin.php?page=adverto-seo-generator'); ?>" class="adverto-action-btn">
                                <span class="material-icons">search</span>
                                <div>
                                    <div style="font-weight: 600; font-size: 13px;"><?php _e('SEO Generator', 'adverto-master'); ?></div>
                                    <div style="font-size: 12px; color: var(--text-secondary);"><?php _e('Optimise meta tags', 'adverto-master'); ?></div>
                                </div>
                            </a>

                            <a href="<?php echo admin_url('admin.php?page=adverto-duplicate-wizard'); ?>" class="adverto-action-btn">
                                <span class="material-icons">content_copy</span>
                                <div>
                                    <div style="font-weight: 600; font-size: 13px;"><?php _e('Duplicate Page', 'adverto-master'); ?></div>
                                    <div style="font-size: 12px; color: var(--text-secondary);"><?php _e('Clone &amp; replace', 'adverto-master'); ?></div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Tools Overview -->
                <h3 style="font-size: 15px; font-weight: 600; margin: 0 0 14px; color: var(--text-primary);"><?php _e('Available Tools', 'adverto-master'); ?></h3>
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 16px;">
                    <!-- Side Tab Manager -->
                    <div class="adverto-card tool-card" style="display: flex; flex-direction: column;">
                        <div class="adverto-card-content" style="flex: 1;">
                            <div style="display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 12px;">
                                <div class="adverto-stat-icon">
                                    <span class="material-icons">tab</span>
                                </div>
                                <span class="adverto-status-badge">Active</span>
                            </div>
                            <h3 style="margin: 0 0 6px; font-size: 15px; font-weight: 600;"><?php _e('Side Tab Manager', 'adverto-master'); ?></h3>
                            <p style="color: var(--text-secondary); margin: 0; font-size: 13px; line-height: 1.5;"><?php _e('Manage items, icons and appearance of your side navigation tab.', 'adverto-master'); ?></p>
                        </div>
                        <div class="adverto-card-actions">
                            <a href="<?php echo admin_url('admin.php?page=adverto-side-tab'); ?>" class="adverto-btn adverto-btn-outline adverto-btn-small">
                                <?php _e('Manage Tab', 'adverto-master'); ?>
                                <span class="material-icons">arrow_forward</span>
                            </a>
                        </div>
                    </div>

                    <!-- LLMs.txt Generator -->
                    <div class="adverto-card tool-card" style="display: flex; flex-direction: column;">
                        <div class="adverto-card-content" style="flex: 1;">
                            <div style="display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 12px;">
                                <div class="adverto-stat-icon">
                                    <span class="material-icons">smart_toy</span>
                                </div>
                                <?php if ($stats['seo_contents_generated'] > 0): ?>
                                    <span class="adverto-status-badge">Active</span>
                                <?php endif; ?>
                            </div>
                            <h3 style="margin: 0 0 6px; font-size: 15px; font-weight: 600;"><?php _e('LLMs.txt Generator', 'adverto-master'); ?></h3>
                            <p style="color: var(--text-secondary); margin: 0; font-size: 13px; line-height: 1.5;"><?php _e('Generate AI-readable site summaries to improve how AI agents understand your content.', 'adverto-master'); ?></p>
                        </div>
                        <div class="adverto-card-actions">
                            <a href="<?php echo admin_url('admin.php?page=adverto-llm-generator'); ?>" class="adverto-btn adverto-btn-outline adverto-btn-small">
                                <?php _e('Configure', 'adverto-master'); ?>
                                <span class="material-icons">arrow_forward</span>
                            </a>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Sidebar Column -->
            <div class="adverto-sidebar-col" style="display: flex; flex-direction: column; gap: 16px;">

                <!-- System Status -->
                <div class="adverto-card">
                    <div class="adverto-card-header">
                        <h3 class="adverto-card-title">
                            <span class="material-icons">dns</span>
                            <?php _e('System Status', 'adverto-master'); ?>
                        </h3>
                    </div>
                    <div class="adverto-card-content">
                        <div class="adverto-system-info">
                            <?php foreach ($system_status as $label => $value): ?>
                            <div class="adverto-info-item">
                                <span class="adverto-info-label"><?php echo esc_html($label); ?></span>
                                <span class="adverto-info-value"><?php echo esc_html($value); ?></span>
                            </div>
                            <?php endforeach; ?>
                            <div class="adverto-info-item">
                                <span class="adverto-info-label"><?php _e('API Key', 'adverto-master'); ?></span>
                                <?php if (!empty($api_key)): ?>
                                    <span style="font-size: 13px; font-weight: 500; color: var(--success);"><?php _e('Configured', 'adverto-master'); ?></span>
                                <?php else: ?>
                                    <a href="<?php echo admin_url('admin.php?page=adverto-master-settings'); ?>" style="font-size: 13px; font-weight: 500; color: var(--error); text-decoration: none;"><?php _e('Missing — fix', 'adverto-master'); ?></a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Usage & Cost -->
                <div class="adverto-card">
                    <div class="adverto-card-header">
                        <h3 class="adverto-card-title">
                            <span class="material-icons">receipt_long</span>
                            <?php _e('Usage & Cost', 'adverto-master'); ?>
                        </h3>
                    </div>
                    <div class="adverto-card-content">
                        <?php
                        $total_ai_calls = $stats['alt_texts_generated'] + $stats['seo_contents_generated'];
                        $est_cost = number_format($total_ai_calls * 0.002, 3);
                        ?>
                        <div class="usage-cost-grid">
                            <div class="usage-cost-item">
                                <div class="uc-value"><?php echo number_format($total_ai_calls); ?></div>
                                <div class="uc-label"><?php _e('AI calls', 'adverto-master'); ?></div>
                            </div>
                            <div class="usage-cost-item">
                                <div class="uc-value">~$<?php echo $est_cost; ?></div>
                                <div class="uc-label"><?php _e('Est. cost', 'adverto-master'); ?></div>
                            </div>
                        </div>
                        <div style="margin-top: 14px; padding-top: 14px; border-top: 1px solid var(--border);">
                            <button type="button" id="adverto-reset-usage" class="adverto-btn adverto-btn-secondary adverto-btn-small" style="width: 100%; justify-content: center;">
                                <span class="material-icons">restart_alt</span>
                                <?php _e('Reset Counters', 'adverto-master'); ?>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="adverto-card">
                    <div class="adverto-card-header">
                        <h3 class="adverto-card-title">
                            <span class="material-icons">history</span>
                            <?php _e('Recent Activity', 'adverto-master'); ?>
                        </h3>
                    </div>
                    <div class="adverto-card-content">
                        <?php
                        global $wpdb;
                        $table_name = $wpdb->prefix . 'adverto_usage_stats';
                        if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name) {
                            $recent_activities = $wpdb->get_results(
                                "SELECT * FROM $table_name ORDER BY timestamp DESC LIMIT 5"
                            );
                        } else {
                            $recent_activities = array();
                        }
                        ?>
                        <?php if (!empty($recent_activities)): ?>
                            <div class="adverto-activity-list">
                                <?php foreach ($recent_activities as $activity): ?>
                                <div class="adverto-activity-item">
                                    <div class="adverto-activity-icon">
                                        <span class="material-icons">
                                            <?php
                                            switch ($activity->tool_name) {
                                                case 'alt-text': echo 'image'; break;
                                                case 'seo': echo 'search'; break;
                                                case 'side-tab': echo 'tab'; break;
                                                case 'duplicate': echo 'content_copy'; break;
                                                default: echo 'circle';
                                            }
                                            ?>
                                        </span>
                                    </div>
                                    <div class="adverto-activity-content">
                                        <div class="adverto-activity-title"><?php echo esc_html(ucfirst(str_replace('-', ' ', $activity->tool_name))); ?></div>
                                        <div class="adverto-activity-time"><?php echo human_time_diff(strtotime($activity->timestamp), current_time('timestamp')) . ' ' . __('ago', 'adverto-master'); ?></div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="adverto-empty-state" style="padding: 20px;">
                                <div class="empty-state-icon">
                                    <span class="material-icons">inbox</span>
                                </div>
                                <div class="empty-state-content">
                                    <p><?php _e('No recent activity.', 'adverto-master'); ?></p>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    // Reset usage counters
    $('#adverto-reset-usage').on('click', function() {
        if (!confirm('<?php _e('Reset all usage counters to zero?', 'adverto-master'); ?>')) return;
        var $btn = $(this);
        $btn.prop('disabled', true);
        $.post(ajaxurl, {
            action: 'adverto_reset_usage_counters',
            nonce: '<?php echo wp_create_nonce('adverto_nonce'); ?>'
        }, function(response) {
            if (response.success) {
                location.reload();
            } else {
                $btn.prop('disabled', false);
            }
        }).fail(function() {
            $btn.prop('disabled', false);
        });
    });
});
</script>
