<?php
/**
 * Fired during plugin deactivation
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 */

class Adverto_Deactivator {

    /**
     * Short Description. (use period)
     *
     * Long Description.
     */
    public static function deactivate() {
        // Clean up scheduled events
        wp_clear_scheduled_hook('adverto_master_daily_cleanup');
        
        // Remove temporary data
        delete_transient('adverto_master_cache');
        
        // Note: We don't delete user data or settings on deactivation
        // only on uninstall if the user chooses to remove all data
    }
}
