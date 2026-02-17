<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 */

class Adverto_Admin {

    /**
     * The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     */
    public function __construct($plugin_name, $version) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Register the stylesheets for the admin area.
     * 
     * @param string $hook The current admin page.
     */
    public function enqueue_styles($hook) {
        // Only load on plugin pages
        if (strpos($hook, 'adverto') === false) {
            return;
        }

        wp_enqueue_style('adverto-material-icons', 'https://fonts.googleapis.com/icon?family=Material+Icons', array(), $this->version, 'all');
        wp_enqueue_style('adverto-google-fonts', 'https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap', array(), $this->version, 'all');
        wp_enqueue_style($this->plugin_name, ADVERTO_MASTER_ADMIN_URL . 'css/adverto-admin.css', array(), $this->version, 'all');
        
        // Add color picker for settings
        wp_enqueue_style('wp-color-picker');
    }

    /**
     * Register the JavaScript for the admin area.
     * 
     * @param string $hook The current admin page.
     */
    public function enqueue_scripts($hook) {
        // Only load on plugin pages
        if (strpos($hook, 'adverto') === false) {
            return;
        }

        wp_enqueue_script('wp-color-picker');
        wp_enqueue_media();
        wp_enqueue_script($this->plugin_name, ADVERTO_MASTER_ADMIN_URL . 'js/adverto-admin.js', array('jquery', 'wp-color-picker'), $this->version, false);
        
        // Localize script for AJAX
        wp_localize_script($this->plugin_name, 'adverto_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('adverto_nonce'),
            'plugin_url' => ADVERTO_MASTER_PLUGIN_URL,
            'assets_url' => ADVERTO_MASTER_ASSETS_URL,
        ));
    }

    /**
     * Add the plugin admin menu
     */
    public function add_plugin_admin_menu() {
        // Main menu page - Use custom Adverto Media logo
        $icon_url = ADVERTO_MASTER_ASSETS_URL . 'images/advertomedia2024.png';
        
        add_menu_page(
            __('Adverto Master', 'adverto-master'),
            __('Adverto Master', 'adverto-master'),
            'manage_options',
            'adverto-master',
            array($this, 'display_plugin_dashboard'),
            $icon_url,
            30
        );

        // Dashboard (same as main menu)
        add_submenu_page(
            'adverto-master',
            __('Dashboard', 'adverto-master'),
            __('Dashboard', 'adverto-master'),
            'manage_options',
            'adverto-master',
            array($this, 'display_plugin_dashboard')
        );

        // Alt Text Generator AI
        add_submenu_page(
            'adverto-master',
            __('Alt Text Generator AI', 'adverto-master'),
            __('Alt Text Generator AI', 'adverto-master'),
            'manage_options',
            'adverto-alt-text-generator',
            array($this, 'display_alt_text_generator')
        );

        // SEO Generator AI
        add_submenu_page(
            'adverto-master',
            __('SEO Generator AI', 'adverto-master'),
            __('SEO Generator AI', 'adverto-master'),
            'manage_options',
            'adverto-seo-generator',
            array($this, 'display_seo_generator')
        );

        // Side Tab Manager
        add_submenu_page(
            'adverto-master',
            __('Side Tab Manager', 'adverto-master'),
            __('Side Tab Manager', 'adverto-master'),
            'manage_options',
            'adverto-side-tab',
            array($this, 'display_side_tab_manager')
        );

        // LLMs.txt Generator AI
        add_submenu_page(
            'adverto-master',
            __('LLMs.txt Generator AI', 'adverto-master'),
            __('LLMs.txt Generator AI', 'adverto-master'),
            'manage_options',
            'adverto-llm-generator',
            array($this, 'display_llm_generator')
        );

        // Duplicate SEO Wizard
        add_submenu_page(
            'adverto-master',
            __('Duplicate SEO Wizard', 'adverto-master'),
            __('Duplicate SEO Wizard', 'adverto-master'),
            'manage_options',
            'adverto-duplicate-wizard',
            array($this, 'display_duplicate_wizard')
        );

        // Global Settings
        add_submenu_page(
            'adverto-master',
            __('Settings', 'adverto-master'),
            __('Settings', 'adverto-master'),
            'manage_options',
            'adverto-master-settings',
            array($this, 'display_plugin_settings')
        );
    }

    /**
     * Display the plugin dashboard page
     */
    public function display_plugin_dashboard() {
        include_once ADVERTO_MASTER_ADMIN_DIR . 'views/dashboard.php';
    }

    /**
     * Display Alt Text Generator page
     */
    public function display_alt_text_generator() {
        include_once ADVERTO_MASTER_ADMIN_DIR . 'views/alt-text-generator.php';
    }

    /**
     * Display SEO Generator page
     */
    public function display_seo_generator() {
        include_once ADVERTO_MASTER_ADMIN_DIR . 'views/seo-generator.php';
    }

    /**
     * Display Side Tab Manager page
     */
    public function display_side_tab_manager() {
        include_once ADVERTO_MASTER_ADMIN_DIR . 'views/side-tab-manager-simple.php';
    }

    /**
     * Display LLMs.txt Generator page
     */
    public function display_llm_generator() {
        include_once ADVERTO_MASTER_ADMIN_DIR . 'views/llm-generator.php';
    }

    /**
     * Display Duplicate Wizard page
     */
    public function display_duplicate_wizard() {
        include_once ADVERTO_MASTER_ADMIN_DIR . 'views/duplicate-wizard.php';
    }

    /**
     * Display the plugin settings page
     */
    public function display_plugin_settings() {
        include_once ADVERTO_MASTER_ADMIN_DIR . 'views/settings.php';
    }

    /**
     * Register settings
     */
    public function register_settings() {
        register_setting('adverto_master_settings_group', 'adverto_master_settings');
        register_setting('adverto_alt_text_settings_group', 'adverto_alt_text_prompt');
        register_setting('adverto_seo_settings_group', 'adverto_seo_prompt');
        
        // Register side tab settings with sanitization
        register_setting(
            'adverto_side_tab_settings_group',
            'adverto_side_tab_settings',
            array(
                'sanitize_callback' => array($this, 'sanitize_side_tab_settings'),
                'default' => array(
                    'enabled' => 1,
                    'position' => 'right',
                    'background_color' => '#4285f4',
                    'text_color' => '#ffffff',
                    'hover_color' => '#3367d6'
                )
            )
        );
        
        // Register side tab items setting
        register_setting(
            'adverto_side_tab_items_group',
            'adverto_side_tab_items',
            array(
                'sanitize_callback' => array($this, 'sanitize_side_tab_items'),
                'default' => array()
            )
        );
    }

    /**
     * Sanitize side tab settings
     */
    public function sanitize_side_tab_settings($settings) {
        if (!is_array($settings)) {
            return array();
        }

        $sanitized = array();
        $sanitized['enabled'] = isset($settings['enabled']) ? 1 : 0;
        $sanitized['position'] = in_array($settings['position'] ?? 'right', array('left', 'right')) ? $settings['position'] : 'right';
        $sanitized['background_color'] = sanitize_hex_color($settings['background_color'] ?? '#4285f4');
        $sanitized['text_color'] = sanitize_hex_color($settings['text_color'] ?? '#ffffff');
        $sanitized['hover_color'] = sanitize_hex_color($settings['hover_color'] ?? '#3367d6');

        return $sanitized;
    }

    /**
     * Sanitize side tab items
     */
    public function sanitize_side_tab_items($items) {
        if (!is_array($items)) {
            return array();
        }

        $sanitized = array();
        foreach ($items as $item) {
            if (!is_array($item)) continue;
            
            $sanitized[] = array(
                'icon' => esc_url_raw($item['icon'] ?? ''),
                'text' => sanitize_text_field($item['text'] ?? ''),
                'link' => esc_url_raw($item['link'] ?? ''),
                'target' => in_array($item['target'] ?? '_self', array('_self', '_blank')) ? $item['target'] : '_self'
            );
        }

        return $sanitized;
    }

    /**
     * Test OpenAI API connection
     */
    public function test_openai_api() {
        // Log the attempt
        error_log('Adverto: API test started');
        
        check_ajax_referer('adverto_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            error_log('Adverto: Insufficient permissions for API test');
            wp_send_json_error(__('Insufficient permissions.', 'adverto-master'));
            return;
        }

        $api_key = sanitize_text_field($_POST['api_key'] ?? '');
        error_log('Adverto: API key length: ' . strlen($api_key));
        
        if (empty($api_key)) {
            error_log('Adverto: Empty API key provided');
            wp_send_json_error(__('API key is required.', 'adverto-master'));
            return;
        }

        // Test the API key by making a simple request
        error_log('Adverto: Making API request to OpenAI');
        $response = wp_remote_get('https://api.openai.com/v1/models', array(
            'timeout' => 30,
            'headers' => array(
                'Authorization' => 'Bearer ' . $api_key,
                'Content-Type' => 'application/json',
            ),
        ));

        if (is_wp_error($response)) {
            $error_message = $response->get_error_message();
            error_log('Adverto: WP_Error occurred: ' . $error_message);
            wp_send_json_error(sprintf(__('Network error: %s', 'adverto-master'), $error_message));
            return;
        }

        $response_code = wp_remote_retrieve_response_code($response);
        $response_body = wp_remote_retrieve_body($response);
        error_log('Adverto: API response code: ' . $response_code);
        error_log('Adverto: API response body length: ' . strlen($response_body));
        
        if ($response_code === 200) {
            error_log('Adverto: API test successful');
            wp_send_json_success(__('API connection successful!', 'adverto-master'));
        } else if ($response_code === 401) {
            error_log('Adverto: Invalid API key (401)');
            wp_send_json_error(__('Invalid API key. Please check your OpenAI API key.', 'adverto-master'));
        } else if ($response_code === 429) {
            error_log('Adverto: Rate limit exceeded (429)');
            wp_send_json_error(__('Rate limit exceeded. Please try again later.', 'adverto-master'));
        } else {
            $error_data = json_decode($response_body, true);
            $error_message = isset($error_data['error']['message']) ? $error_data['error']['message'] : 'Unknown error';
            error_log('Adverto: API error (' . $response_code . '): ' . $error_message);
            wp_send_json_error(sprintf(__('API error (%d): %s', 'adverto-master'), $response_code, $error_message));
        }
    }
}
