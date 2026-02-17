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
        
        <?php if (get_option('adverto_master_show_welcome')): ?>
        <!-- Welcome Alert -->
        <div class="adverto-alert info" data-auto-hide="10000">
            <i class="material-icons">celebration</i>
            <span><?php _e('Welcome to Adverto Master! Your comprehensive AI marketing toolkit is ready to use.', 'adverto-master'); ?></span>
        </div>
        <?php 
        delete_option('adverto_master_show_welcome');
        endif; 
        ?>

        <?php if (empty($api_key)): ?>
        <!-- API Key Warning -->
        <div class="adverto-alert warning">
            <i class="material-icons">key</i>
            <span>
                <?php _e('OpenAI API key not configured. ', 'adverto-master'); ?>
                <a href="<?php echo admin_url('admin.php?page=adverto-master-settings'); ?>" class="adverto-btn-link">
                    <?php _e('Configure it now', 'adverto-master'); ?>
                </a>
            </span>
        </div>
        <?php endif; ?>

        <!-- Statistics Grid -->
        <div class="adverto-grid adverto-grid-4">
            <div class="adverto-stat-card primary">
                <div class="adverto-stat-number" data-count="<?php echo $stats['alt_texts_generated']; ?>">
                    <?php echo number_format($stats['alt_texts_generated']); ?>
                </div>
                <div class="adverto-stat-label"><?php _e('Alt Texts Generated', 'adverto-master'); ?></div>
                <i class="material-icons adverto-stat-icon">image</i>
            </div>
            
            <div class="adverto-stat-card success">
                <div class="adverto-stat-number" data-count="<?php echo $stats['seo_contents_generated']; ?>">
                    <?php echo number_format($stats['seo_contents_generated']); ?>
                </div>
                <div class="adverto-stat-label"><?php _e('SEO Contents Generated', 'adverto-master'); ?></div>
                <i class="material-icons adverto-stat-icon">search</i>
            </div>
            
            <div class="adverto-stat-card warning">
                <div class="adverto-stat-number" data-count="<?php echo $stats['pages_duplicated']; ?>">
                    <?php echo number_format($stats['pages_duplicated']); ?>
                </div>
                <div class="adverto-stat-label"><?php _e('Pages Duplicated', 'adverto-master'); ?></div>
                <i class="material-icons adverto-stat-icon">content_copy</i>
            </div>
            
            <div class="adverto-stat-card error">
                <div class="adverto-stat-number" data-count="<?php echo $stats['side_tab_clicks']; ?>">
                    <?php echo number_format($stats['side_tab_clicks']); ?>
                </div>
                <div class="adverto-stat-label"><?php _e('Side Tab Interactions', 'adverto-master'); ?></div>
                <i class="material-icons adverto-stat-icon">tab</i>
            </div>
        </div>

        <!-- Tools Grid -->
        <div class="adverto-grid adverto-grid-3">
            
            <!-- Alt Text Generator AI -->
            <div class="adverto-card">
                <div class="adverto-card-header">
                    <h3 class="adverto-card-title">
                        <span class="material-icons">image</span>
                        <?php _e('Alt Text Generator AI', 'adverto-master'); ?>
                    </h3>
                    <div class="adverto-card-subtitle">
                        <?php _e('Generate AI-powered alt texts for your images', 'adverto-master'); ?>
                    </div>
                </div>
                <div class="adverto-card-content">
                    <p><?php _e('Automatically generate descriptive, SEO-friendly alt texts for multiple images using advanced AI technology.', 'adverto-master'); ?></p>
                    <div class="adverto-progress" style="margin: 16px 0;">
                        <div class="adverto-progress-bar" data-progress="85"></div>
                    </div>
                    <small class="adverto-text-secondary"><?php _e('85% accuracy rate', 'adverto-master'); ?></small>
                </div>
                <div class="adverto-card-actions">
                    <a href="<?php echo admin_url('admin.php?page=adverto-alt-text-generator'); ?>" class="adverto-btn adverto-btn-primary">
                        <span class="material-icons">launch</span>
                        <?php _e('Open Tool', 'adverto-master'); ?>
                    </a>
                </div>
            </div>

            <!-- SEO Generator AI -->
            <div class="adverto-card">
                <div class="adverto-card-header">
                    <h3 class="adverto-card-title">
                        <span class="material-icons">search</span>
                        <?php _e('SEO Generator AI', 'adverto-master'); ?>
                    </h3>
                    <div class="adverto-card-subtitle">
                        <?php _e('Generate SEO-optimised titles and meta descriptions', 'adverto-master'); ?>
                    </div>
                </div>
                <div class="adverto-card-content">
                    <p><?php _e('Create compelling SEO titles and meta descriptions for your pages with AI assistance.', 'adverto-master'); ?></p>
                    <div class="adverto-progress" style="margin: 16px 0;">
                        <div class="adverto-progress-bar" data-progress="92"></div>
                    </div>
                    <small class="adverto-text-secondary"><?php _e('92% SEO improvement rate', 'adverto-master'); ?></small>
                </div>
                <div class="adverto-card-actions">
                    <a href="<?php echo admin_url('admin.php?page=adverto-seo-generator'); ?>" class="adverto-btn adverto-btn-primary">
                        <span class="material-icons">launch</span>
                        <?php _e('Open Tool', 'adverto-master'); ?>
                    </a>
                </div>
            </div>

            <!-- Side Tab Manager -->
            <div class="adverto-card">
                <div class="adverto-card-header">
                    <h3 class="adverto-card-title">
                        <span class="material-icons">tab</span>
                        <?php _e('Side Tab Manager', 'adverto-master'); ?>
                    </h3>
                    <div class="adverto-card-subtitle">
                        <?php _e('Customise your website\'s side navigation tab', 'adverto-master'); ?>
                    </div>
                </div>
                <div class="adverto-card-content">
                    <p><?php _e('Add a beautiful, customisable side tab to your website for quick access to contact forms, phone numbers, and more.', 'adverto-master'); ?></p>
                    <div class="adverto-progress" style="margin: 16px 0;">
                        <div class="adverto-progress-bar" data-progress="78"></div>
                    </div>
                    <small class="adverto-text-secondary"><?php _e('78% conversion increase', 'adverto-master'); ?></small>
                </div>
                <div class="adverto-card-actions">
                    <a href="<?php echo admin_url('admin.php?page=adverto-side-tab'); ?>" class="adverto-btn adverto-btn-primary">
                        <span class="material-icons">launch</span>
                        <?php _e('Open Tool', 'adverto-master'); ?>
                    </a>
                </div>
            </div>

            <!-- LLMs.txt Generator AI -->
            <div class="adverto-card">
                <div class="adverto-card-header">
                    <h3 class="adverto-card-title">
                        <span class="material-icons">smart_toy</span>
                        <?php _e('LLMs.txt Generator AI', 'adverto-master'); ?>
                    </h3>
                    <div class="adverto-card-subtitle">
                        <?php _e('Generate AI-readable site summaries', 'adverto-master'); ?>
                    </div>
                </div>
                <div class="adverto-card-content">
                    <p><?php _e('Create LLMs.txt files that help AI systems understand your website content, improving chatbot interactions and AI-powered search results.', 'adverto-master'); ?></p>
                    <div class="adverto-progress" style="margin: 16px 0;">
                        <div class="adverto-progress-bar" data-progress="96"></div>
                    </div>
                    <small class="adverto-text-secondary"><?php _e('96% AI comprehension', 'adverto-master'); ?></small>
                </div>
                <div class="adverto-card-actions">
                    <a href="<?php echo admin_url('admin.php?page=adverto-llm-generator'); ?>" class="adverto-btn adverto-btn-primary">
                        <span class="material-icons">launch</span>
                        <?php _e('Open Tool', 'adverto-master'); ?>
                    </a>
                </div>
            </div>

            <!-- Duplicate SEO Wizard -->
            <div class="adverto-card">
                <div class="adverto-card-header">
                    <h3 class="adverto-card-title">
                        <span class="material-icons">content_copy</span>
                        <?php _e('Duplicate SEO Wizard', 'adverto-master'); ?>
                    </h3>
                    <div class="adverto-card-subtitle">
                        <?php _e('Duplicate pages with find and replace functionality', 'adverto-master'); ?>
                    </div>
                </div>
                <div class="adverto-card-content">
                    <p><?php _e('Quickly duplicate pages and replace specific content, perfect for creating location-based or product variant pages.', 'adverto-master'); ?></p>
                    <div class="adverto-progress" style="margin: 16px 0;">
                        <div class="adverto-progress-bar" data-progress="95"></div>
                    </div>
                    <small class="adverto-text-secondary"><?php _e('95% time saved', 'adverto-master'); ?></small>
                </div>
                <div class="adverto-card-actions">
                    <a href="<?php echo admin_url('admin.php?page=adverto-duplicate-wizard'); ?>" class="adverto-btn adverto-btn-primary">
                        <span class="material-icons">launch</span>
                        <?php _e('Open Tool', 'adverto-master'); ?>
                    </a>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="adverto-card">
            <div class="adverto-card-header">
                <h3 class="adverto-card-title">
                    <span class="material-icons">bolt</span>
                    <?php _e('Quick Actions', 'adverto-master'); ?>
                </h3>
            </div>
            <div class="adverto-card-content">
                <div class="adverto-grid adverto-grid-4">
                    <a href="<?php echo admin_url('admin.php?page=adverto-master-settings'); ?>" class="adverto-btn adverto-btn-secondary">
                        <span class="material-icons">settings</span>
                        <?php _e('Settings', 'adverto-master'); ?>
                    </a>
                    
                    <a href="<?php echo admin_url('admin.php?page=adverto-alt-text-generator'); ?>" class="adverto-btn adverto-btn-secondary">
                        <span class="material-icons">auto_fix_high</span>
                        <?php _e('Generate Alt Texts', 'adverto-master'); ?>
                    </a>
                    
                    <a href="<?php echo admin_url('admin.php?page=adverto-seo-generator'); ?>" class="adverto-btn adverto-btn-secondary">
                        <span class="material-icons">trending_up</span>
                        <?php _e('Optimise SEO', 'adverto-master'); ?>
                    </a>
                    
                    <a href="https://adverto.com/support" target="_blank" class="adverto-btn adverto-btn-secondary">
                        <span class="material-icons">help</span>
                        <?php _e('Get Support', 'adverto-master'); ?>
                    </a>
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
                // Get recent activity from database
                global $wpdb;
                $table_name = $wpdb->prefix . 'adverto_usage_stats';
                $recent_activities = $wpdb->get_results(
                    "SELECT * FROM $table_name ORDER BY timestamp DESC LIMIT 5"
                );
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
                            <div class="adverto-activity-title">
                                <?php echo esc_html(ucfirst(str_replace('-', ' ', $activity->tool_name)) . ' - ' . ucfirst($activity->action_type)); ?>
                            </div>
                            <div class="adverto-activity-time">
                                <?php echo human_time_diff(strtotime($activity->timestamp), current_time('timestamp')) . ' ago'; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <div class="adverto-empty-state">
                    <span class="material-icons">inbox</span>
                    <p><?php _e('No recent activity. Start using the tools to see activity here!', 'adverto-master'); ?></p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Floating Action Button -->
<button class="adverto-fab" data-tooltip="Quick Settings">
    <span class="material-icons">settings</span>
</button>

<style>
/* Dashboard specific styles */
.adverto-activity-list {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.adverto-activity-item {
    display: flex;
    align-items: center;
    padding: 12px;
    background: rgba(66, 133, 244, 0.02);
    border-radius: 8px;
    transition: var(--transition);
}

.adverto-activity-item:hover {
    background: rgba(66, 133, 244, 0.05);
}

.adverto-activity-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: var(--primary-color);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 16px;
}

.adverto-activity-content {
    flex: 1;
}

.adverto-activity-title {
    font-weight: 500;
    color: var(--text-primary);
    margin-bottom: 4px;
}

.adverto-activity-time {
    font-size: 12px;
    color: var(--text-secondary);
}

.adverto-empty-state {
    text-align: center;
    padding: 40px 20px;
    color: var(--text-secondary);
}

.adverto-empty-state .material-icons {
    font-size: 64px;
    opacity: 0.3;
    margin-bottom: 16px;
}

.adverto-text-secondary {
    color: var(--text-secondary);
}

.adverto-btn-link {
    color: var(--primary-color);
    text-decoration: none;
    font-weight: 500;
}

.adverto-btn-link:hover {
    color: var(--primary-dark);
    text-decoration: underline;
}
</style>

<script>
jQuery(document).ready(function($) {
    // Add click handler for FAB
    $('.adverto-fab').on('click', function() {
        window.location.href = '<?php echo admin_url('admin.php?page=adverto-master-settings'); ?>';
    });
});
</script>
