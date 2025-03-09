<?php
/*
Plugin Name: Steam API Plugin
Description: Plugin adds opportunity to check Steam ID info on your web-pages.
Version: 1.1
Author: develabr, seo_jacky
Author URI: https://t.me/big_jacky
Plugin URI: https://github.com/seojacky/steam-api-plugin
GitHub Plugin URI: https://github.com/seojacky/steam-api-plugin
*/
require_once(plugin_dir_path(__FILE__) . 'ajax/ajax-handler.php');
// Include the file with user input processing function
require_once(plugin_dir_path(__FILE__) . 'inc/input-processing.php');

// Plugin activation hook to set default settings
register_activation_hook(__FILE__, 'steam_api_activate');
function steam_api_activate() {
    // Add default options if they don't exist
    if (!get_option('steam_api_settings')) {
        add_option('steam_api_settings', array(
            'api_key' => '',
            'cache_duration' => 3600, // Default cache duration in seconds (1 hour)
        ));
    }
}

// Add plugin settings page
add_action('admin_menu', 'steam_api_add_admin_menu');
function steam_api_add_admin_menu() {
    add_options_page(
        'Steam API Settings',
        'Steam API',
        'manage_options',
        'steam_api_settings',
        'steam_api_settings_page'
    );
}

// Register settings
add_action('admin_init', 'steam_api_register_settings');
function steam_api_register_settings() {
    register_setting('steam_api_settings_group', 'steam_api_settings', 'steam_api_sanitize_settings');
}

// Sanitize settings
function steam_api_sanitize_settings($input) {
    $sanitized_input = array();
    
    if (isset($input['api_key'])) {
        $sanitized_input['api_key'] = sanitize_text_field($input['api_key']);
    }
    
    if (isset($input['cache_duration'])) {
        $sanitized_input['cache_duration'] = absint($input['cache_duration']);
    }
    
    return $sanitized_input;
}

// Settings page
function steam_api_settings_page() {
    $settings = get_option('steam_api_settings');
    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        <form method="post" action="options.php">
            <?php settings_fields('steam_api_settings_group'); ?>
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="steam_api_settings[api_key]">Steam API Key</label>
                    </th>
                    <td>
                        <input type="text" 
                               id="steam_api_settings[api_key]" 
                               name="steam_api_settings[api_key]" 
                               value="<?php echo esc_attr($settings['api_key']); ?>" 
                               class="regular-text" />
                        <p class="description">
                            Enter your Steam API key. You can get one from 
                            <a href="https://steamcommunity.com/dev/apikey" target="_blank">
                                Steam Web API Key Registration
                            </a>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="steam_api_settings[cache_duration]">Cache Duration (seconds)</label>
                    </th>
                    <td>
                        <input type="number" 
                               id="steam_api_settings[cache_duration]" 
                               name="steam_api_settings[cache_duration]" 
                               value="<?php echo esc_attr($settings['cache_duration']); ?>" 
                               class="regular-text" />
                        <p class="description">
                            Duration to cache API results in seconds. Default is 3600 (1 hour).
                        </p>
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

// Helper function to get API key
function steam_api_get_api_key() {
    $settings = get_option('steam_api_settings');
    return isset($settings['api_key']) ? $settings['api_key'] : '';
}

// Helper function to get cache duration
function steam_api_get_cache_duration() {
    $settings = get_option('steam_api_settings');
    return isset($settings['cache_duration']) ? intval($settings['cache_duration']) : 3600;
}

// Enqueue styles only when shortcode is used
function steam_api_enqueue_styles() {
    if (is_singular() && has_shortcode(get_post()->post_content, 'steam_api')) {
        wp_enqueue_style('steam-api-styles', plugin_dir_url(__FILE__) . 'css/steam-api-public.css', array(), '1.1');
    }
}
add_action('wp_enqueue_scripts', 'steam_api_enqueue_styles');

// Enqueue scripts only when shortcode is used
function steam_api_enqueue_scripts() {
    if (is_singular() && has_shortcode(get_post()->post_content, 'steam_api')) {
        wp_enqueue_script('steam-api-scripts', plugin_dir_url(__FILE__) . 'js/steam-api-public.js', array(), '1.1', true);
        
        // Add module type to script
        add_filter("script_loader_tag", "steam_api_add_module_to_script", 10, 3);
        
        // Localize script with AJAX URL and nonce
        $localized_data = array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('steam_api_nonce'),
        );
        wp_localize_script('steam-api-scripts', 'ajax_object', $localized_data);
    }
}
add_action('wp_enqueue_scripts', 'steam_api_enqueue_scripts');

// Add module type to script tag
function steam_api_add_module_to_script($tag, $handle, $src) {
    if ("steam-api-scripts" === $handle) {
        $tag = '<script type="module" src="' . esc_url($src) . '"></script>';
    }
    return $tag;
}

// Shortcode for displaying the Steam API form
function steam_api_shortcode() {
    ob_start();
    
    // Check if API key is set
    $api_key = steam_api_get_api_key();
    if (empty($api_key)) {
        if (current_user_can('manage_options')) {
            return '<div class="steam-api-error">Please set your Steam API key in the <a href="' . 
                   admin_url('options-general.php?page=steam_api_settings') . 
                   '">Steam API Settings</a>.</div>';
        } else {
            return '<div class="steam-api-error">This feature is currently unavailable. Please contact the site administrator.</div>';
        }
    }
    
    // Return the HTML for the form
    return '<div class="steam-api-wrapper">
        <div class="steam-api-info">
            <form class="form" id="steam-form" autocomplete="off">
                <div class="input-group">
                    <input type="text" id="steamInput" class="form-input" title="Examples:
Heavenanvil
76561198036370701
STEAM_0:1:38052486
steamcommunity.com/id/heavenanvil
steamcommunity.com/profiles/76561198036370701" placeholder="Enter SteamID / SteamCommunityID / Profile Name / Profile URL" style="border-top-left-radius: .25rem;border-bottom-left-radius: .25rem;">
                    <button type="button" id="get-stats-button">Find</button>
                </div>
                <p class="form-description">Find and get your Steam ID, Steam ID 64, customURL and community identifier</p>
            </form>
        </div>
        <div id="user-info" class="user-info-block"></div>
        <div id="results"></div>
    </div>';
}
add_shortcode('steam_api', 'steam_api_shortcode');

// AJAX handler for get_player_stats
add_action('wp_ajax_get_player_stats', 'steam_api_get_player_stats_callback');
add_action('wp_ajax_nopriv_get_player_stats', 'steam_api_get_player_stats_callback');

function steam_api_get_player_stats_callback() {
    // Check nonce for security
    check_ajax_referer('steam_api_nonce', 'nonce');
    
    if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['steamId'])) {
        $input_value = sanitize_text_field($_POST['steamId']);
        
        // Get Steam ID from the user input
        $steam_id = getSteamID64($input_value);
        
        if (!empty($steam_id)) {
            // Check cache first
            $cache_key = 'steam_api_' . md5($steam_id);
            $cached_data = get_transient($cache_key);
            
            if ($cached_data !== false) {
                // Return cached data
                wp_send_json($cached_data);
            } else {
                // Query Steam API and get player stats
                $player_stats = fetch_steam_player_stats($steam_id);
                
                if (!isset($player_stats['error'])) {
                    // Cache the result
                    set_transient($cache_key, $player_stats, steam_api_get_cache_duration());
                }
                
                // Send data back to client
                wp_send_json($player_stats);
            }
        } else {
            wp_send_json(array('error' => 'Invalid Steam ID format or cannot resolve the provided input.'));
        }
    } else {
        wp_send_json(array('error' => 'Invalid request.'));
    }
    
    wp_die();
}
