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
        <div class="adverto-grid adverto-grid-4" style="margin-bottom: 32px;">
            <div class="adverto-stat-card primary" style="background: linear-gradient(135deg, #4285f4, #3367d6); color: white;">
                <div class="adverto-stat-icon" style="background: rgba(255,255,255,0.2); width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 12px;">
                    <i class="material-icons" style="font-size: 24px; color: white;">image</i>
                </div>
                <div class="adverto-stat-number" style="font-size: 32px; font-weight: 700; margin-bottom: 4px;" data-count="<?php echo $stats['alt_texts_generated']; ?>">
                    <?php echo number_format($stats['alt_texts_generated']); ?>
                </div>
                <div class="adverto-stat-label" style="opacity: 0.9; font-size: 13px; font-weight: 500; text-transform: uppercase; letter-spacing: 0.5px;"><?php _e('Alt Texts Generated', 'adverto-master'); ?></div>
            </div>
            
            <div class="adverto-stat-card success" style="background: linear-gradient(135deg, #0f9d58, #0b8043); color: white;">
                <div class="adverto-stat-icon" style="background: rgba(255,255,255,0.2); width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 12px;">
                    <i class="material-icons" style="font-size: 24px; color: white;">search</i>
                </div>
                <div class="adverto-stat-number" style="font-size: 32px; font-weight: 700; margin-bottom: 4px;" data-count="<?php echo $stats['seo_contents_generated']; ?>">
                    <?php echo number_format($stats['seo_contents_generated']); ?>
                </div>
                <div class="adverto-stat-label" style="opacity: 0.9; font-size: 13px; font-weight: 500; text-transform: uppercase; letter-spacing: 0.5px;"><?php _e('SEO Optimizations', 'adverto-master'); ?></div>
            </div>
            
            <div class="adverto-stat-card warning" style="background: linear-gradient(135deg, #f4b400, #f09300); color: white;">
                <div class="adverto-stat-icon" style="background: rgba(255,255,255,0.2); width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 12px;">
                    <i class="material-icons" style="font-size: 24px; color: white;">content_copy</i>
                </div>
                <div class="adverto-stat-number" style="font-size: 32px; font-weight: 700; margin-bottom: 4px;" data-count="<?php echo $stats['pages_duplicated']; ?>">
                    <?php echo number_format($stats['pages_duplicated']); ?>
                </div>
                <div class="adverto-stat-label" style="opacity: 0.9; font-size: 13px; font-weight: 500; text-transform: uppercase; letter-spacing: 0.5px;"><?php _e('Pages Duplicated', 'adverto-master'); ?></div>
            </div>
            
            <div class="adverto-stat-card error" style="background: linear-gradient(135deg, #db4437, #c53929); color: white;">
                <div class="adverto-stat-icon" style="background: rgba(255,255,255,0.2); width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 12px;">
                    <i class="material-icons" style="font-size: 24px; color: white;">touch_app</i>
                </div>
                <div class="adverto-stat-number" style="font-size: 32px; font-weight: 700; margin-bottom: 4px;" data-count="<?php echo $stats['side_tab_clicks']; ?>">
                    <?php echo number_format($stats['side_tab_clicks']); ?>
                </div>
                <div class="adverto-stat-label" style="opacity: 0.9; font-size: 13px; font-weight: 500; text-transform: uppercase; letter-spacing: 0.5px;"><?php _e('Tab Interactions', 'adverto-master'); ?></div>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px;">
            <!-- Main Column -->
            <div class="adverto-main-col">
                
                <!-- Quick Actions -->
                <div class="adverto-card" style="margin-bottom: 24px;">
                    <div class="adverto-card-header" style="border-bottom: 1px solid #eee;">
                        <h3 class="adverto-card-title">
                            <span class="material-icons" style="color: #4285f4; margin-right: 8px;">bolt</span>
                            <?php _e('Quick Actions', 'adverto-master'); ?>
                        </h3>
                    </div>
                    <div class="adverto-card-content" style="padding: 20px;">
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
                            <a href="<?php echo admin_url('admin.php?page=adverto-alt-text-generator'); ?>" class="adverto-action-btn" style="display: flex; align-items: center; padding: 16px; background: #f8f9fa; border-radius: 8px; text-decoration: none; color: #333; transition: all 0.2s; border: 1px solid #e1e4e8;">
                                <span class="material-icons" style="color: #4285f4; margin-right: 12px;">image</span>
                                <div>
                                    <div style="font-weight: 600; font-size: 14px;">Generate Alt Text</div>
                                    <div style="font-size: 12px; color: #666;">Optimize image SEO</div>
                                </div>
                            </a>
                            
                            <a href="<?php echo admin_url('admin.php?page=adverto-seo-generator'); ?>" class="adverto-action-btn" style="display: flex; align-items: center; padding: 16px; background: #f8f9fa; border-radius: 8px; text-decoration: none; color: #333; transition: all 0.2s; border: 1px solid #e1e4e8;">
                                <span class="material-icons" style="color: #0f9d58; margin-right: 12px;">search</span>
                                <div>
                                    <div style="font-weight: 600; font-size: 14px;">SEO Generator</div>
                                    <div style="font-size: 12px; color: #666;">Optimize meta tags</div>
                                </div>
                            </a>

                            <a href="<?php echo admin_url('admin.php?page=adverto-duplicate-wizard'); ?>" class="adverto-action-btn" style="display: flex; align-items: center; padding: 16px; background: #f8f9fa; border-radius: 8px; text-decoration: none; color: #333; transition: all 0.2s; border: 1px solid #e1e4e8;">
                                <span class="material-icons" style="color: #f4b400; margin-right: 12px;">content_copy</span>
                                <div>
                                    <div style="font-weight: 600; font-size: 14px;">Duplicate Page</div>
                                    <div style="font-size: 12px; color: #666;">Clone & replace</div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Tools Overview -->
                <h3 style="font-size: 18px; margin: 0 0 16px 0; color: #444;"><?php _e('Available Tools', 'adverto-master'); ?></h3>
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 24px;">
                    <!-- Side Tab Manager -->
                    <div class="adverto-card tool-card" style="height: 100%; display: flex; flex-direction: column;">
                        <div class="adverto-card-content" style="flex: 1; padding: 24px;">
                            <div style="display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 16px;">
                                <div style="width: 48px; height: 48px; background: #e8f0fe; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                                    <span class="material-icons" style="color: #4285f4; font-size: 24px;">tab</span>
                                </div>
                                <span class="adverto-status-badge" style="background: #e6f4ea; color: #137333; padding: 4px 8px; border-radius: 12px; font-size: 11px; font-weight: 600;">ACTIVE</span>
                            </div>
                            <h3 style="margin: 0 0 8px 0; font-size: 18px;">Side Tab Manager</h3>
                            <p style="color: #666; margin: 0; font-size: 14px; line-height: 1.5;">Customize your floating side navigation widget. Manage items, icons, and appearance.</p>
                        </div>
                        <div class="adverto-card-actions" style="padding: 16px 24px; border-top: 1px solid #eee; background: #f8f9fa;">
                            <a href="<?php echo admin_url('admin.php?page=adverto-side-tab'); ?>" style="text-decoration: none; color: #4285f4; font-weight: 600; font-size: 14px; display: flex; align-items: center;">
                                Manage Tab
                                <span class="material-icons" style="font-size: 16px; margin-left: 4px;">arrow_forward</span>
                            </a>
                        </div>
                    </div>

                    <!-- LLMs.txt Generator -->
                    <div class="adverto-card tool-card" style="height: 100%; display: flex; flex-direction: column;">
                        <div class="adverto-card-content" style="flex: 1; padding: 24px;">
                            <div style="display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 16px;">
                                <div style="width: 48px; height: 48px; background: #fce8e6; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                                    <span class="material-icons" style="color: #ea4335; font-size: 24px;">smart_toy</span>
                                </div>
                                <?php if($stats['seo_contents_generated'] > 0): ?>
                                <span class="adverto-status-badge" style="background: #e6f4ea; color: #137333; padding: 4px 8px; border-radius: 12px; font-size: 11px; font-weight: 600;">ACTIVE</span>
                                <?php endif; ?>
                            </div>
                            <h3 style="margin: 0 0 8px 0; font-size: 18px;">LLMs.txt Generator</h3>
                            <p style="color: #666; margin: 0; font-size: 14px; line-height: 1.5;">Generate AI-readable site summaries to improve how AI agents understand your content.</p>
                        </div>
                        <div class="adverto-card-actions" style="padding: 16px 24px; border-top: 1px solid #eee; background: #f8f9fa;">
                            <a href="<?php echo admin_url('admin.php?page=adverto-llm-generator'); ?>" style="text-decoration: none; color: #4285f4; font-weight: 600; font-size: 14px; display: flex; align-items: center;">
                                Configure
                                <span class="material-icons" style="font-size: 16px; margin-left: 4px;">arrow_forward</span>
                            </a>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Sidebar Column -->
            <div class="adverto-sidebar-col">
                
                <!-- System Status -->
                <div class="adverto-card">
                    <div class="adverto-card-header" style="border-bottom: 1px solid #eee; padding: 16px 20px;">
                        <h4 style="margin: 0; font-size: 16px; display: flex; align-items: center;">
                            <span class="material-icons" style="font-size: 20px; color: #666; margin-right: 8px;">dns</span>
                            System Status
                        </h4>
                    </div>
                    <div class="adverto-card-content" style="padding: 0;">
                        <?php foreach($system_status as $label => $value): ?>
                        <div style="display: flex; justify-content: space-between; padding: 12px 20px; border-bottom: 1px solid #f5f5f5;">
                            <span style="color: #666; font-size: 13px;"><?php echo esc_html($label); ?></span>
                            <span style="font-weight: 500; font-size: 13px;"><?php echo esc_html($value); ?></span>
                        </div>
                        <?php endforeach; ?>
                        <div style="display: flex; justify-content: space-between; padding: 12px 20px;">
                            <span style="color: #666; font-size: 13px;">API Key Status</span>
                            <?php if(!empty($api_key)): ?>
                                <span style="color: #137333; font-weight: 600; font-size: 13px;">Configured</span>
                            <?php else: ?>
                                <a href="<?php echo admin_url('admin.php?page=adverto-master-settings'); ?>" style="color: #d93025; font-weight: 600; font-size: 13px; text-decoration: none;">Missing (Fix)</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="adverto-card">
                    <div class="adverto-card-header" style="border-bottom: 1px solid #eee; padding: 16px 20px;">
                        <h4 style="margin: 0; font-size: 16px; display: flex; align-items: center;">
                            <span class="material-icons" style="font-size: 20px; color: #666; margin-right: 8px;">history</span>
                            Recent Activity
                        </h4>
                    </div>
                    <div class="adverto-card-content" style="padding: 0;">
                        <?php
                        global $wpdb;
                        $table_name = $wpdb->prefix . 'adverto_usage_stats';
                        // Check if table exists first to avoid errors
                        if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name) {
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
                                <div class="adverto-activity-item" style="padding: 12px 20px; border-bottom: 1px solid #f5f5f5; display: flex; align-items: center;">
                                    <div class="adverto-activity-icon" style="width: 32px; height: 32px; border-radius: 50%; background: #e8f0fe; display: flex; align-items: center; justify-content: center; margin-right: 12px;">
                                        <span class="material-icons" style="font-size: 16px; color: #4285f4;">
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
                                    <div class="adverto-activity-content" style="flex: 1; min-width: 0;">
                                        <div class="adverto-activity-title" style="font-size: 13px; font-weight: 500; truncate: ellipsis; white-space: nowrap; overflow: hidden;">
                                            <?php echo esc_html(ucfirst(str_replace('-', ' ', $activity->tool_name))); ?>
                                        </div>
                                        <div class="adverto-activity-time" style="font-size: 11px; color: #888;">
                                            <?php echo human_time_diff(strtotime($activity->timestamp), current_time('timestamp')) . ' ago'; ?>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="adverto-empty-state" style="padding: 24px; text-align: center;">
                                <span class="material-icons" style="font-size: 32px; color: #ddd; margin-bottom: 8px;">inbox</span>
                                <p style="font-size: 13px; color: #999; margin: 0;"><?php _e('No recent activity.', 'adverto-master'); ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Support -->
                <div class="adverto-card" style="background: #333; color: white;">
                    <div class="adverto-card-content" style="padding: 24px; text-align: center;">
                        <span class="material-icons" style="font-size: 40px; margin-bottom: 16px; opacity: 0.8;">headset_mic</span>
                        <h3 style="margin: 0 0 8px 0; color: white;">Need Help?</h3>
                        <p style="color: #aaa; font-size: 13px; margin-bottom: 20px;">Check our documentation or contact support for assistance.</p>
                        <a href="https://adverto.com/support" target="_blank" class="adverto-btn" style="background: white; color: #333; border: none; width: 100%; justify-content: center;">
                            Get Support
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<style>
/* Dashboard specific hover effects */
.adverto-action-btn:hover {
    background: white !important;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    border-color: #d1d5db !important;
    transform: translateY(-2px);
}
.adverto-stat-card {
    border-radius: 16px;
    padding: 24px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    transition: transform 0.3s ease;
}
.adverto-stat-card:hover {
    transform: translateY(-5px);
}
.tool-card {
    transition: all 0.3s ease;
    border: 1px solid transparent;
}
.tool-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.08);
    border-color: #e0e0e0;
}
</style>

<!-- Floating Action Button -->
<button class="adverto-fab" data-tooltip="Quick Settings">
    <span class="material-icons">settings</span>
</button>

<script>
jQuery(document).ready(function($) {
    // Add click handler for FAB
    $('.adverto-fab').on('click', function() {
        window.location.href = '<?php echo admin_url('admin.php?page=adverto-master-settings'); ?>';
    });
});
</script>
