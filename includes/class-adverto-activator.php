<?php
/**
 * Fired during plugin activation
 *
 * This class defines all code necessary to run during the plugin's activation.
 */

class Adverto_Activator {

    /**
     * Short Description. (use period)
     *
     * Long Description.
     */
    public static function activate() {
        // Set default options for all tools
        self::set_default_options();
        
        // Create necessary database tables if needed
        self::create_tables();
        
        // Set activation flag for welcome screen
        update_option('adverto_master_show_welcome', true);
    }

    /**
     * Set default options for all tools
     */
    private static function set_default_options() {
        // Global settings
        if (!get_option('adverto_master_settings')) {
            $default_settings = array(
                'openai_api_key' => '',
                'theme_color' => '#4285f4', // Google Blue
                'enable_animations' => true,
                'enable_tooltips' => true,
            );
            add_option('adverto_master_settings', $default_settings);
        }

        // Side Tab default settings
        if (!get_option('adverto_side_tab_items')) {
            $default_items = array(
                array(
                    'icon' => ADVERTO_MASTER_ASSETS_URL . 'images/phone.svg',
                    'text' => 'Contact Us',
                    'link' => 'tel:123456789',
                    'target' => '_self'
                ),
                array(
                    'icon' => ADVERTO_MASTER_ASSETS_URL . 'images/quote.svg',
                    'text' => 'Get a Quote',
                    'link' => '/get-a-quote',
                    'target' => '_self'
                ),
                array(
                    'icon' => ADVERTO_MASTER_ASSETS_URL . 'images/contact.svg',
                    'text' => 'Contact Form',
                    'link' => '/contact-form',
                    'target' => '_self'
                )
            );
            add_option('adverto_side_tab_items', $default_items);
        }

        if (!get_option('adverto_side_tab_settings')) {
            $default_settings = array(
                'background_color' => '#4285f4',
                'text_color' => '#ffffff',
                'hover_color' => '#3367d6',
                'enabled' => 1,
                'position' => 'right'
            );
            add_option('adverto_side_tab_settings', $default_settings);
        }

        // Alt Text Generator settings
        if (!get_option('adverto_alt_text_prompt')) {
            add_option('adverto_alt_text_prompt', 'Generate a descriptive alt text for this image for SEO purposes. Be concise but descriptive.');
        }

        // SEO Generator settings
        if (!get_option('adverto_seo_prompt')) {
            add_option('adverto_seo_prompt', 'Generate an SEO-optimised title (up to 60 characters) and a meta description (up to 160 characters) for this page based on its content. Ensure both a title and a description are provided.');
        }
    }

    /**
     * Create necessary database tables
     */
    private static function create_tables() {
        global $wpdb;

        // Create usage statistics table
        $table_name = $wpdb->prefix . 'adverto_usage_stats';

        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            tool_name varchar(50) NOT NULL,
            action_type varchar(50) NOT NULL,
            user_id bigint(20) NOT NULL,
            timestamp datetime DEFAULT CURRENT_TIMESTAMP,
            metadata longtext,
            PRIMARY KEY (id),
            KEY user_id (user_id),
            KEY tool_name (tool_name),
            KEY timestamp (timestamp)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
}
