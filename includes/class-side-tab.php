<?php
/**
 * Side Tab functionality
 * Handles customisable side navigation tab
 */

class Adverto_Side_Tab {
    
    /**
     * Initialize the class
     */
    public function __construct() {
        // Constructor logic if needed
    }

    /**
     * Initialize admin hooks
     */
    public function init_admin_hooks($loader) {
        $loader->add_action('wp_ajax_adverto_save_side_tab_settings', $this, 'handle_save_settings');
        $loader->add_action('wp_ajax_adverto_add_side_tab_item', $this, 'handle_add_item');
        $loader->add_action('wp_ajax_adverto_update_side_tab_item', $this, 'handle_update_item');
        $loader->add_action('wp_ajax_adverto_delete_side_tab_item', $this, 'handle_delete_item');
        $loader->add_action('wp_ajax_adverto_reorder_side_tab_items', $this, 'handle_reorder_items');
        $loader->add_action('wp_ajax_adverto_get_side_tab_stats', $this, 'handle_get_stats');
        $loader->add_action('wp_ajax_nopriv_adverto_track_side_tab_click', $this, 'handle_track_click');
        $loader->add_action('wp_ajax_adverto_track_side_tab_click', $this, 'handle_track_click');
    }

    /**
     * Handle saving side tab settings
     */
    public function handle_save_settings() {
        check_ajax_referer('adverto_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(__('Insufficient permissions.', 'adverto-master'));
            return;
        }

        $settings = array(
            'enabled' => !empty($_POST['enabled']) && $_POST['enabled'] !== 'false' ? 1 : 0,
            'position' => sanitize_text_field($_POST['position'] ?? 'right'),
            'background_color' => sanitize_hex_color($_POST['background_color'] ?? '#4285f4'),
            'text_color' => sanitize_hex_color($_POST['text_color'] ?? '#ffffff'),
            'hover_color' => sanitize_hex_color($_POST['hover_color'] ?? '#3367d6'),
        );

        update_option('adverto_side_tab_settings', $settings);
        wp_send_json_success(array('message' => __('Settings saved successfully!', 'adverto-master')));
    }

    /**
     * Handle adding new side tab item
     */
    public function handle_add_item() {
        check_ajax_referer('adverto_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(__('Insufficient permissions.', 'adverto-master'));
            return;
        }

        $text = sanitize_text_field($_POST['text']);
        $link = esc_url_raw($_POST['link']);
        $target = sanitize_text_field($_POST['target'] ?? '_self');
        $icon = esc_url_raw($_POST['icon'] ?? '');

        if (empty($text) || empty($link)) {
            wp_send_json_error(__('Text and link are required.', 'adverto-master'));
            return;
        }

        $items = get_option('adverto_side_tab_items', array());
        $new_item = array(
            'id' => uniqid(),
            'text' => $text,
            'link' => $link,
            'target' => $target,
            'icon' => $icon,
            'order' => count($items)
        );

        $items[] = $new_item;
        update_option('adverto_side_tab_items', $items);

        wp_send_json_success(array(
            'message' => __('Item added successfully!', 'adverto-master'),
            'item' => $new_item
        ));
    }

    /**
     * Handle updating side tab item
     */
    public function handle_update_item() {
        check_ajax_referer('adverto_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(__('Insufficient permissions.', 'adverto-master'));
            return;
        }

        $item_id = sanitize_text_field($_POST['item_id']);
        $text = sanitize_text_field($_POST['text']);
        $link = esc_url_raw($_POST['link']);
        $target = sanitize_text_field($_POST['target'] ?? '_self');
        $icon = esc_url_raw($_POST['icon'] ?? '');

        if (empty($item_id) || empty($text) || empty($link)) {
            wp_send_json_error(__('All required fields must be filled.', 'adverto-master'));
            return;
        }

        $items = get_option('adverto_side_tab_items', array());
        foreach ($items as &$item) {
            if ($item['id'] === $item_id) {
                $item['text'] = $text;
                $item['link'] = $link;
                $item['target'] = $target;
                $item['icon'] = $icon;
                break;
            }
        }

        update_option('adverto_side_tab_items', $items);
        wp_send_json_success(array('message' => __('Item updated successfully!', 'adverto-master')));
    }

    /**
     * Handle deleting side tab item
     */
    public function handle_delete_item() {
        check_ajax_referer('adverto_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(__('Insufficient permissions.', 'adverto-master'));
            return;
        }

        $item_id = sanitize_text_field($_POST['item_id']);
        
        if (empty($item_id)) {
            wp_send_json_error(__('Invalid item ID.', 'adverto-master'));
            return;
        }

        $items = get_option('adverto_side_tab_items', array());
        $items = array_filter($items, function($item) use ($item_id) {
            return $item['id'] !== $item_id;
        });

        update_option('adverto_side_tab_items', array_values($items));
        wp_send_json_success(array('message' => __('Item deleted successfully!', 'adverto-master')));
    }

    /**
     * Handle reordering side tab items
     */
    public function handle_reorder_items() {
        check_ajax_referer('adverto_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(__('Insufficient permissions.', 'adverto-master'));
            return;
        }

        $item_ids = $_POST['item_ids'] ?? array();
        
        if (empty($item_ids) || !is_array($item_ids)) {
            wp_send_json_error(__('Invalid item order.', 'adverto-master'));
            return;
        }

        $items = get_option('adverto_side_tab_items', array());
        $reordered_items = array();

        foreach ($item_ids as $order => $item_id) {
            foreach ($items as $item) {
                if ($item['id'] === $item_id) {
                    $item['order'] = $order;
                    $reordered_items[] = $item;
                    break;
                }
            }
        }

        update_option('adverto_side_tab_items', $reordered_items);
        wp_send_json_success(array('message' => __('Items reordered successfully!', 'adverto-master')));
    }

    /**
     * Handle getting side tab statistics
     */
    public function handle_get_stats() {
        check_ajax_referer('adverto_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(__('Insufficient permissions.', 'adverto-master'));
            return;
        }

        $stats = get_option('adverto_side_tab_stats', array(
            'total_clicks' => 0,
            'unique_visitors' => 0,
            'last_click' => null
        ));

        wp_send_json_success($stats);
    }

    /**
     * Handle tracking clicks
     */
    public function handle_track_click() {
        check_ajax_referer('adverto_public_nonce', 'nonce');

        $stats = get_option('adverto_side_tab_stats', array(
            'total_clicks' => 0,
            'unique_visitors' => 0,
            'last_click' => null
        ));

        $stats['total_clicks']++;
        $stats['last_click'] = current_time('mysql');

        // Track unique visitors using session
        if (!isset($_SESSION)) {
            session_start();
        }
        
        if (!isset($_SESSION['adverto_side_tab_visited'])) {
            $_SESSION['adverto_side_tab_visited'] = true;
            $stats['unique_visitors']++;
        }

        update_option('adverto_side_tab_stats', $stats);
        wp_send_json_success();
    }

    /**
     * Initialize public hooks
     */
    public function init_public_hooks($loader) {
        $settings = get_option('adverto_side_tab_settings', array());
        
        if (!empty($settings['enabled'])) {
            $loader->add_action('wp_footer', $this, 'render_side_tab');
        }
    }

    /**
     * Render side tab on frontend
     */
    public function render_side_tab() {
        $settings = get_option('adverto_side_tab_settings', array(
            'background_color' => '#4285f4',
            'text_color' => '#ffffff',
            'hover_color' => '#3367d6',
            'enabled' => 1,
            'position' => 'right'
        ));

        // Check if side tab is enabled - return early if not
        if (empty($settings['enabled'])) {
            return;
        }

        $items = get_option('adverto_side_tab_items', array());
        
        if (empty($items)) {
            return;
        }

        $style = sprintf(
            'background-color: %s; color: %s;',
            esc_attr($settings['background_color']),
            esc_attr($settings['text_color'])
        );

        $position = esc_attr($settings['position']);
        ?>
        <div id="adverto-side-tab" class="adverto-side-tab collapsed <?php echo $position; ?>" style="<?php echo $style; ?>">
            <button id="adverto-side-tab-toggle" class="adverto-side-tab-toggle">
                <span class="adverto-toggle-icon">›</span>
            </button>
            <div class="adverto-side-tab-items">
                <?php foreach ($items as $item) : ?>
                    <a href="<?php echo esc_url($item['link']); ?>" 
                       target="<?php echo esc_attr($item['target']); ?>"
                       class="adverto-side-tab-item">
                        <?php if (!empty($item['icon'])): ?>
                            <img src="<?php echo esc_url($item['icon']); ?>" 
                                 alt="<?php echo esc_attr($item['text']); ?>" />
                        <?php endif; ?>
                        <span><?php echo esc_html($item['text']); ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

        <style>
        #adverto-side-tab {
            position: fixed;
            top: 50%;
            transform: translateY(-50%);
            z-index: 9999;
            display: flex;
            transition: transform 0.3s ease;
            width: fit-content;
            font-family: 'Roboto', sans-serif;
        }
        
        #adverto-side-tab.right {
            right: 0;
        }
        
        #adverto-side-tab.left {
            left: 0;
            flex-direction: row-reverse;
        }
        
        #adverto-side-tab.right.collapsed {
            transform: translateY(-50%) translateX(calc(100% - 40px));
        }
        
        #adverto-side-tab.left.collapsed {
            transform: translateY(-50%) translateX(calc(-100% + 40px));
        }
        
        .adverto-side-tab-toggle {
            width: 40px;
            height: 40px;
            border: none;
            background: inherit;
            color: inherit;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0;
            margin: 0;
            min-width: 40px;
            border-radius: 0;
        }
        
        .adverto-toggle-icon {
            font-size: 24px;
            transition: transform 0.3s ease;
            display: block;
            line-height: 1;
        }
        
        #adverto-side-tab.right .adverto-toggle-icon {
            transform: rotate(0deg);
        }
        
        #adverto-side-tab.right.collapsed .adverto-toggle-icon {
            transform: rotate(180deg);
        }
        
        #adverto-side-tab.left .adverto-toggle-icon {
            transform: rotate(180deg);
        }
        
        #adverto-side-tab.left.collapsed .adverto-toggle-icon {
            transform: rotate(0deg);
        }
        
        .adverto-side-tab-items {
            display: flex;
            flex-direction: column;
            background: inherit;
        }
        
        .adverto-side-tab-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            padding: 10px 15px;
            text-decoration: none;
            color: inherit;
            transition: background-color 0.3s ease;
            position: relative;
            background: inherit;
        }
        
        .adverto-side-tab-item:not(:last-child) {
            border-bottom: 2px solid rgba(255, 255, 255, 0.4);
            margin-bottom: 2px;
        }
        
        .adverto-side-tab-item:hover {
            background-color: <?php echo esc_attr($settings['hover_color']); ?>;
        }
        
        .adverto-side-tab-item img {
            width: 24px;
            height: 24px;
            margin-bottom: 5px;
            object-fit: contain;
        }
        
        .adverto-side-tab-item span {
            font-size: 14px;
            font-weight: 500;
            white-space: normal;
            line-height: 1.2;
        }
        </style>

        <script>
        jQuery(document).ready(function($) {
            // Toggle side tab
            $('#adverto-side-tab-toggle').on('click', function() {
                $('#adverto-side-tab').toggleClass('collapsed');
            });

            // Close side tab when clicking outside
            $(document).on('click', function(e) {
                if (!$(e.target).closest('#adverto-side-tab').length) {
                    $('#adverto-side-tab').addClass('collapsed');
                }
            });

            // Track clicks
            $('.adverto-side-tab-item').on('click', function() {
                $.ajax({
                    url: '<?php echo admin_url('admin-ajax.php'); ?>',
                    method: 'POST',
                    data: {
                        action: 'adverto_track_side_tab_click',
                        nonce: '<?php echo wp_create_nonce('adverto_public_nonce'); ?>'
                    }
                });
            });
        });
        </script>
        <?php
    }
}
