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

        $hex = $settings['background_color'];
        list($r, $g, $b) = sscanf($hex, "#%02x%02x%02x");
        $bg_rgba = sprintf('rgba(%d, %d, %d, 0.85)', $r, $g, $b); // 0.85 opacity for glass effect

        $style = sprintf(
            '--adverto-tab-bg: %s; --adverto-tab-text: %s; --adverto-tab-hover: %s;',
            esc_attr($bg_rgba),
            esc_attr($settings['text_color']),
            esc_attr($settings['hover_color'])
        );

        $position = esc_attr($settings['position']);
        ?>
        <div id="adverto-side-tab-wrapper" class="<?php echo $position; ?>" style="<?php echo $style; ?>">
            <div id="adverto-side-tab" class="adverto-side-tab collapsed">
                <button id="adverto-side-tab-toggle" class="adverto-side-tab-toggle" aria-label="Toggle Side Tab">
                    <span class="adverto-toggle-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                </button>
                <div class="adverto-side-tab-content">
                    <?php foreach ($items as $item) : ?>
                        <a href="<?php echo esc_url($item['link']); ?>" 
                           target="<?php echo esc_attr($item['target']); ?>"
                           class="adverto-side-tab-item">
                            <div class="adverto-item-icon">
                                <?php if (!empty($item['icon'])): ?>
                                    <img src="<?php echo esc_url($item['icon']); ?>" alt="<?php echo esc_attr($item['text']); ?>" />
                                <?php else: ?>
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" class="default-icon">
                                        <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                <?php endif; ?>
                            </div>
                            <span class="adverto-item-text"><?php echo esc_html($item['text']); ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <style>
        #adverto-side-tab-wrapper {
            position: fixed;
            top: 50%;
            transform: translateY(-50%);
            z-index: 99999;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
        }

        #adverto-side-tab-wrapper.right { right: 0; }
        #adverto-side-tab-wrapper.left { left: 0; }

        #adverto-side-tab {
            position: relative;
            background-color: var(--adverto-tab-bg);
            color: var(--adverto-tab-text);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px 0 0 12px;
            transition: transform 0.4s cubic-bezier(0.2, 0.8, 0.2, 1);
            transform: translateX(0);
        }

        #adverto-side-tab-wrapper.left #adverto-side-tab {
            border-radius: 0 12px 12px 0;
        }

        #adverto-side-tab.collapsed {
            transform: translateX(100%);
        }
        
        #adverto-side-tab-wrapper.left #adverto-side-tab.collapsed {
            transform: translateX(-100%);
        }

        .adverto-side-tab-toggle {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 32px;
            height: 64px;
            background-color: var(--adverto-tab-bg);
            color: var(--adverto-tab-text);
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0;
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            box-shadow: -4px 0 12px rgba(0,0,0,0.1);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-right: none;
            border-radius: 8px 0 0 8px;
            transition: transform 0.3s ease, background-color 0.3s;
            outline: none;
        }

        #adverto-side-tab-wrapper.right .adverto-side-tab-toggle {
            left: -32px;
        }

        #adverto-side-tab-wrapper.left .adverto-side-tab-toggle {
            right: -32px;
            border-left: none;
            border-right: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 0 8px 8px 0;
            box-shadow: 4px 0 12px rgba(0,0,0,0.1);
        }

        .adverto-side-tab-toggle:hover {
            filter: brightness(1.1);
        }

        .adverto-toggle-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.4s ease;
        }
        
        /* Icon rotation logic */
        #adverto-side-tab-wrapper.right #adverto-side-tab.collapsed .adverto-toggle-icon {
            transform: rotate(180deg);
        }
        
        #adverto-side-tab-wrapper.left #adverto-side-tab:not(.collapsed) .adverto-toggle-icon {
            transform: rotate(180deg);
        }

        .adverto-side-tab-content {
            display: flex;
            flex-direction: column;
            padding: 8px 0;
            min-width: 80px;
        }

        .adverto-side-tab-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 12px 16px;
            text-decoration: none;
            color: inherit !important;
            transition: all 0.2s ease;
            position: relative;
            gap: 6px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        .adverto-side-tab-item:last-child {
            border-bottom: none;
        }

        .adverto-side-tab-item:hover {
            background-color: rgba(255, 255, 255, 0.15);
            text-decoration: none;
        }

        .adverto-item-icon {
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .adverto-item-icon img, .adverto-item-icon svg {
            width: 100%;
            height: 100%;
            object-fit: contain;
            transition: transform 0.2s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .adverto-side-tab-item:hover .adverto-item-icon img,
        .adverto-side-tab-item:hover .adverto-item-icon svg {
            transform: scale(1.2);
        }

        .adverto-item-text {
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            opacity: 0.9;
            text-align: center;
            line-height: 1.2;
        }
        </style>

        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const wrapper = document.getElementById('adverto-side-tab-wrapper');
            const tab = document.getElementById('adverto-side-tab');
            const toggle = document.getElementById('adverto-side-tab-toggle');

            if (toggle && tab) {
                toggle.addEventListener('click', function(e) {
                    e.stopPropagation();
                    tab.classList.toggle('collapsed');
                });

                document.addEventListener('click', function(e) {
                    if (wrapper && !wrapper.contains(e.target) && !tab.classList.contains('collapsed')) {
                        tab.classList.add('collapsed');
                    }
                });
            }
            
            // Track clicks
            const links = document.querySelectorAll('.adverto-side-tab-item');
            links.forEach(link => {
                link.addEventListener('click', function() {
                    if (window.jQuery) {
                        window.jQuery.post('<?php echo admin_url('admin-ajax.php'); ?>', {
                            action: 'adverto_track_side_tab_click',
                            nonce: '<?php echo wp_create_nonce('adverto_public_nonce'); ?>'
                        });
                    }
                });
            });
        });
        </script>
        <?php
    }
}
