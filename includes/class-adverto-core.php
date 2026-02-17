<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 */

class Adverto_Core {

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     */
    protected $plugin_name;

    /**
     * The current version of the plugin.
     */
    protected $version;

    /**
     * Define the core functionality of the plugin.
     */
    public function __construct() {
        if (defined('ADVERTO_MASTER_VERSION')) {
            $this->version = ADVERTO_MASTER_VERSION;
        } else {
            $this->version = '1.0.0';
        }
        $this->plugin_name = 'adverto-master';

        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();
    }

    /**
     * Load the required dependencies for this plugin.
     */
    private function load_dependencies() {
        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once ADVERTO_MASTER_INCLUDES_DIR . 'class-adverto-loader.php';

        /**
         * The class responsible for defining internationalization functionality
         * of the plugin.
         */
        require_once ADVERTO_MASTER_INCLUDES_DIR . 'class-adverto-i18n.php';

        /**
         * The class responsible for defining all actions that occur in the admin area.
         */
        require_once ADVERTO_MASTER_ADMIN_DIR . 'class-adverto-admin.php';

        /**
         * The class responsible for defining all actions that occur in the public-facing
         * side of the site.
         */
        require_once ADVERTO_MASTER_INCLUDES_DIR . 'class-adverto-public.php';

        /**
         * Load all tool classes
         */
        require_once ADVERTO_MASTER_INCLUDES_DIR . 'class-alt-text-generator.php';
        require_once ADVERTO_MASTER_INCLUDES_DIR . 'class-seo-generator.php';
        require_once ADVERTO_MASTER_INCLUDES_DIR . 'class-side-tab.php';
        require_once ADVERTO_MASTER_INCLUDES_DIR . 'class-duplicate-wizard.php';
        require_once ADVERTO_MASTER_INCLUDES_DIR . 'class-llm-generator.php';

        $this->loader = new Adverto_Loader();
    }

    /**
     * Define the locale for this plugin for internationalization.
     */
    private function set_locale() {
        $plugin_i18n = new Adverto_i18n();
        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     */
    private function define_admin_hooks() {
        $plugin_admin = new Adverto_Admin($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
        $this->loader->add_action('admin_menu', $plugin_admin, 'add_plugin_admin_menu');
        $this->loader->add_action('admin_init', $plugin_admin, 'register_settings');
        
        // Add AJAX handler for API testing
        $this->loader->add_action('wp_ajax_adverto_test_api', $plugin_admin, 'test_openai_api');

        // Initialize all tool classes
        $alt_text_generator = new Adverto_Alt_Text_Generator();
        $seo_generator = new Adverto_SEO_Generator();
        $side_tab = new Adverto_Side_Tab();
        $duplicate_wizard = new Adverto_Duplicate_Wizard();
        $llm_generator = new Adverto_LLM_Generator($this->get_plugin_name(), $this->get_version());

        // Hook their admin actions
        $alt_text_generator->init_admin_hooks($this->loader);
        $seo_generator->init_admin_hooks($this->loader);
        $side_tab->init_admin_hooks($this->loader);
        $duplicate_wizard->init_admin_hooks($this->loader);
        $llm_generator->init_admin_hooks($this->loader);
    }

    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     */
    private function define_public_hooks() {
        $plugin_public = new Adverto_Public($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');

        // Initialize public-facing functionality for tools that need it
        $side_tab = new Adverto_Side_Tab();
        $side_tab->init_public_hooks($this->loader);
    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     */
    public function run() {
        $this->loader->run();
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     */
    public function get_plugin_name() {
        return $this->plugin_name;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     */
    public function get_loader() {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     */
    public function get_version() {
        return $this->version;
    }
}
