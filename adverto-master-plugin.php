<?php
/**
 * Plugin Name: Adverto Master Plugin
 * Plugin URI: https://advertomedia.co.uk
 * Description: A comprehensive AI-powered marketing toolkit with alt text generation, SEO optimisation, side tabs, and page duplication tools. Beautiful Google Material Design inspired interface.
 * Version: 1.0.0
 * Author: Noah (Adverto Team)
 * Author URI: https://advertomedia.co.uk
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: adverto-master
 * Domain Path: /languages
 * Requires at least: 5.0
 * Tested up to: 6.3
 * Requires PHP: 7.4
 * Network: false
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * Plugin version
 */
define('ADVERTO_MASTER_VERSION', '1.0.0');

/**
 * Plugin paths and URLs
 */
define('ADVERTO_MASTER_PLUGIN_FILE', __FILE__);
define('ADVERTO_MASTER_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('ADVERTO_MASTER_PLUGIN_URL', plugin_dir_url(__FILE__));
define('ADVERTO_MASTER_ADMIN_DIR', ADVERTO_MASTER_PLUGIN_DIR . 'admin/');
define('ADVERTO_MASTER_ADMIN_URL', ADVERTO_MASTER_PLUGIN_URL . 'admin/');
define('ADVERTO_MASTER_INCLUDES_DIR', ADVERTO_MASTER_PLUGIN_DIR . 'includes/');
define('ADVERTO_MASTER_ASSETS_URL', ADVERTO_MASTER_PLUGIN_URL . 'assets/');

/**
 * Include required files
 */
require_once ADVERTO_MASTER_INCLUDES_DIR . 'class-adverto-core.php';
require_once ADVERTO_MASTER_INCLUDES_DIR . 'class-alt-text-generator.php';
require_once ADVERTO_MASTER_INCLUDES_DIR . 'class-seo-generator.php';
require_once ADVERTO_MASTER_INCLUDES_DIR . 'class-side-tab.php';
require_once ADVERTO_MASTER_INCLUDES_DIR . 'class-duplicate-wizard.php';

/**
 * The code that runs during plugin activation.
 */
function activate_adverto_master() {
    require_once ADVERTO_MASTER_INCLUDES_DIR . 'class-adverto-activator.php';
    Adverto_Activator::activate();
}
register_activation_hook(__FILE__, 'activate_adverto_master');

/**
 * The code that runs during plugin deactivation.
 */
function deactivate_adverto_master() {
    require_once ADVERTO_MASTER_INCLUDES_DIR . 'class-adverto-deactivator.php';
    Adverto_Deactivator::deactivate();
}
register_deactivation_hook(__FILE__, 'deactivate_adverto_master');

/**
 * Initialize the plugin.
 */
function run_adverto_master() {
    $plugin = new Adverto_Core();
    $plugin->run();
}

// Start the plugin
add_action('init', 'run_adverto_master');

/**
 * Add plugin action links
 */
function adverto_master_action_links($links) {
    $custom_links = array(
        '<a href="' . admin_url('admin.php?page=adverto-master') . '">' . __('Dashboard', 'adverto-master') . '</a>',
        '<a href="' . admin_url('admin.php?page=adverto-master-settings') . '">' . __('Settings', 'adverto-master') . '</a>',
    );
    return array_merge($custom_links, $links);
}
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'adverto_master_action_links');

/**
 * Add plugin meta links
 */
function adverto_master_meta_links($links, $file) {
    if ($file === plugin_basename(__FILE__)) {
        $meta_links = array(
            '<a href="https://adverto.com/support" target="_blank">' . __('Support', 'adverto-master') . '</a>',
            '<a href="https://adverto.com/docs" target="_blank">' . __('Documentation', 'adverto-master') . '</a>',
            '<a href="https://adverto.com" target="_blank">' . __('Visit Website', 'adverto-master') . '</a>',
        );
        return array_merge($links, $meta_links);
    }
    return $links;
}
add_filter('plugin_row_meta', 'adverto_master_meta_links', 10, 2);
