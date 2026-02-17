<?php
/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 */

class Adverto_Public {

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
     * Register the stylesheets for the public-facing side of the site.
     */
    public function enqueue_styles() {
        // Only enqueue if needed
        $settings = get_option('adverto_side_tab_settings', array());
        if (!empty($settings['enabled'])) {
            wp_enqueue_style(
                $this->plugin_name . '-public', 
                ADVERTO_MASTER_ASSETS_URL . 'css/adverto-public.css', 
                array(), 
                $this->version, 
                'all'
            );
        }
    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     */
    public function enqueue_scripts() {
        // Only enqueue if needed
        $settings = get_option('adverto_side_tab_settings', array());
        if (!empty($settings['enabled'])) {
            wp_enqueue_script(
                $this->plugin_name . '-public', 
                ADVERTO_MASTER_ASSETS_URL . 'js/adverto-public.js', 
                array('jquery'), 
                $this->version, 
                false
            );
            
            // Localize script
            wp_localize_script(
                $this->plugin_name . '-public',
                'adverto_public',
                array(
                    'ajax_url' => admin_url('admin-ajax.php'),
                    'nonce' => wp_create_nonce('adverto_public_nonce')
                )
            );
        }
    }
}
