<?php
/*
Plugin Name: Steam API Plugin
Description: Plugin adds opportunity to check Steam ID info on your web-pages.
Version: 1.3
Author: develabr, seo_jacky
Author URI: https://t.me/big_jacky
Plugin URI: https://github.com/seojacky/steam-api-plugin
GitHub Plugin URI: https://github.com/seojacky/steam-api-plugin
Text Domain: steam-api-plugin
Domain Path: /languages
*/

// Define text domain constant
define('STEAM_API_TEXT_DOMAIN', 'steam-api-plugin');

// Load plugin text domain
function steam_api_load_textdomain() {
    load_plugin_textdomain(
        STEAM_API_TEXT_DOMAIN,
        false,
        dirname(plugin_basename(__FILE__)) . '/languages/'
    );
}
add_action('plugins_loaded', 'steam_api_load_textdomain');

require_once(plugin_dir_path(__FILE__) . 'ajax/ajax-handler.php');
// Connecting the file with the user input processing function
require_once(plugin_dir_path(__FILE__) . 'inc/input-processing.php');

// Plugin activation hook to set default settings
register_activation_hook(__FILE__, 'steam_api_activate');
function steam_api_activate() {
    // Add default options if they don't exist
    if (!get_option('steam_api_settings')) {
        add_option('steam_api_settings', array(
            'api_key' => '', // No default API key - must be configured by admin
            'cache_duration' => 3600, // Default cache duration in seconds (1 hour)
        ));
    }
}

// Add plugin settings page
add_action('admin_menu', 'steam_api_add_admin_menu');
function steam_api_add_admin_menu() {
    add_options_page(
        __('Steam API Settings', STEAM_API_TEXT_DOMAIN),
        __('Steam API', STEAM_API_TEXT_DOMAIN),
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
        
        // Validate API key format
        if (!empty($sanitized_input['api_key'])) {
            if (strlen($sanitized_input['api_key']) < 32) {
                add_settings_error(
                    'steam_api_settings',
                    'invalid_api_key',
                    __('The Steam API key appears to be invalid. Please check it and try again.', STEAM_API_TEXT_DOMAIN),
                    'error'
                );
            }
        }
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
                        <label for="steam_api_settings[api_key]"><?php _e('Steam API Key', STEAM_API_TEXT_DOMAIN); ?></label>
                    </th>
                    <td>
                        <input type="text" 
                               id="steam_api_settings[api_key]" 
                               name="steam_api_settings[api_key]" 
                               value="<?php echo esc_attr($settings['api_key']); ?>" 
                               class="regular-text" />
                        <p class="description">
                            <?php _e('Enter your Steam API key. You can get one from', STEAM_API_TEXT_DOMAIN); ?> 
                            <a href="https://steamcommunity.com/dev/apikey" target="_blank">
                                <?php _e('Steam Web API Key Registration', STEAM_API_TEXT_DOMAIN); ?>
                            </a>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="steam_api_settings[cache_duration]"><?php _e('Cache Duration (seconds)', STEAM_API_TEXT_DOMAIN); ?></label>
                    </th>
                    <td>
                        <input type="number" 
                               id="steam_api_settings[cache_duration]" 
                               name="steam_api_settings[cache_duration]" 
                               value="<?php echo esc_attr($settings['cache_duration']); ?>" 
                               class="regular-text" />
                        <p class="description">
                            <?php _e('Duration to cache API results in seconds. Default is 3600 (1 hour).', STEAM_API_TEXT_DOMAIN); ?>
                        </p>
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

// Admin notice if API key is not configured
function steam_api_admin_notice() {
    $settings = get_option('steam_api_settings');
    if (empty($settings['api_key']) && current_user_can('manage_options')) {
        ?>
        <div class="notice notice-error is-dismissible">
            <p><?php printf(
                __('Steam API Plugin requires a Steam API key to function. Please %sconfigure it now%s.', STEAM_API_TEXT_DOMAIN),
                '<a href="options-general.php?page=steam_api_settings">',
                '</a>'
            ); ?></p>
        </div>
        <?php
    }
}
add_action('admin_notices', 'steam_api_admin_notice');

// Helper function to get API key (used in ajax-handler.php and input-processing.php)
function steam_api_get_api_key() {
    $settings = get_option('steam_api_settings');
    return isset($settings['api_key']) && !empty($settings['api_key']) 
        ? $settings['api_key'] 
        : false; // Return false instead of a hardcoded key
}

// Helper function to get cache duration
function steam_api_get_cache_duration() {
    $settings = get_option('steam_api_settings');
    return isset($settings['cache_duration']) ? intval($settings['cache_duration']) : 3600;
}

function steam_api_enqueue_styles() {
    if (is_singular() && has_shortcode(get_post()->post_content, 'steam_api')) {
        wp_enqueue_style('steam-api-styles', plugin_dir_url(__FILE__) . 'css/steam-api-public.css');
    }
}
add_action('wp_enqueue_scripts', 'steam_api_enqueue_styles');

function steam_api_enqueue_scripts() {
    if (is_singular() && has_shortcode(get_post()->post_content, 'steam_api')) {
        wp_enqueue_script('steam-api-scripts', plugin_dir_url(__FILE__) . 'js/steam-api-public.js', array('jquery'), '1.0', true);
    }

    add_filter("script_loader_tag", "add_module_to_my_script", 10, 3);
    function add_module_to_my_script($tag, $handle, $src) {
        if ("steam-api-scripts" === $handle) {
            $tag = '<script type="module" src="' . esc_url($src) . '"></script>';
        }

        return $tag;
    }

    $localized_data = array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'i18n' => array(
            'offline' => __('🔴 Offline', STEAM_API_TEXT_DOMAIN),
            'online' => __('🟢 Online', STEAM_API_TEXT_DOMAIN),
            'busy' => __('Busy', STEAM_API_TEXT_DOMAIN),
            'away' => __('Away', STEAM_API_TEXT_DOMAIN),
            'snooze' => __('Snooze', STEAM_API_TEXT_DOMAIN),
            'lookingToTrade' => __('Looking to trade', STEAM_API_TEXT_DOMAIN),
            'lookingToPlay' => __('Looking to play', STEAM_API_TEXT_DOMAIN),
            'unknown' => __('Unknown', STEAM_API_TEXT_DOMAIN),
            'private' => __('Private', STEAM_API_TEXT_DOMAIN),
            'public' => __('Public', STEAM_API_TEXT_DOMAIN),
            'level' => __('Level', STEAM_API_TEXT_DOMAIN),
            'copyButton' => __('Copy', STEAM_API_TEXT_DOMAIN),
            'copyButtonDone' => __('Done', STEAM_API_TEXT_DOMAIN),
            'steamID2' => __('SteamID2', STEAM_API_TEXT_DOMAIN),
            'steamID3' => __('SteamID3', STEAM_API_TEXT_DOMAIN),
            'steamID64' => __('SteamID64', STEAM_API_TEXT_DOMAIN),
            'realName' => __('Real Name', STEAM_API_TEXT_DOMAIN),
            'hidden' => __('Hidden', STEAM_API_TEXT_DOMAIN),
            'profileURL' => __('Profile URL', STEAM_API_TEXT_DOMAIN),
            'accountCreated' => __('Account created', STEAM_API_TEXT_DOMAIN),
            'visibility' => __('Visibility', STEAM_API_TEXT_DOMAIN),
            'status' => __('Status', STEAM_API_TEXT_DOMAIN),
            'location' => __('Location', STEAM_API_TEXT_DOMAIN),
            'avatar' => __('Avatar', STEAM_API_TEXT_DOMAIN),
            'errorFetchingData' => __('Error fetching player data.', STEAM_API_TEXT_DOMAIN),
            'playerNotFound' => __('Player data not found.', STEAM_API_TEXT_DOMAIN),
            'find' => __('Find', STEAM_API_TEXT_DOMAIN),
            'enterDetails' => __('Enter SteamID / SteamCommunityID / Profile Name / Profile URL', STEAM_API_TEXT_DOMAIN),
            'findSteamId' => __('Find and get your Steam ID, Steam ID 64, customURL and community ID', STEAM_API_TEXT_DOMAIN),
            'examples' => __('For example:
Heavenanvil
76561198036370701
STEAM_0:1:38052486
steamcommunity.com/id/heavenanvil
steamcommunity.com/profiles/76561198036370701', STEAM_API_TEXT_DOMAIN)
        )
    );
    wp_localize_script('steam-api-scripts', 'steamApiData', $localized_data);
}
add_action('wp_enqueue_scripts', 'steam_api_enqueue_scripts');

// Create templates directory if it doesn't exist
function steam_api_create_templates_directory() {
    $templates_dir = plugin_dir_path(__FILE__) . 'templates';
    if (!file_exists($templates_dir)) {
        mkdir($templates_dir, 0755, true);
    }
}
register_activation_hook(__FILE__, 'steam_api_create_templates_directory');

// Create languages directory if it doesn't exist
function steam_api_create_languages_directory() {
    $languages_dir = plugin_dir_path(__FILE__) . 'languages';
    if (!file_exists($languages_dir)) {
        mkdir($languages_dir, 0755, true);
    }
}
register_activation_hook(__FILE__, 'steam_api_create_languages_directory');

function steam_api_shortcode() {
    // Check if API key is configured
    $api_key = steam_api_get_api_key();
    
    if (!$api_key) {
        if (current_user_can('manage_options')) {
            return '<div class="steam-api-wrapper"><div class="steam-api-error">' . 
                   sprintf(__('Steam API key is not configured. %sConfigure it now%s.', STEAM_API_TEXT_DOMAIN), 
                   '<a href="' . admin_url('options-general.php?page=steam_api_settings') . '">', '</a>') . 
                   '</div></div>';
        } else {
            return '<div class="steam-api-wrapper"><div class="steam-api-error">' . 
                   __('This feature is currently unavailable. Please contact the site administrator.', STEAM_API_TEXT_DOMAIN) . 
                   '</div></div>';
        }
    }
    
    ob_start();
    include(plugin_dir_path(__FILE__) . 'templates/form-template.php');
    return ob_get_clean();
}
add_shortcode('steam_api', 'steam_api_shortcode');

add_action('wp_ajax_get_player_stats', 'get_player_stats_callback');
add_action('wp_ajax_nopriv_get_player_stats', 'get_player_stats_callback');

function get_player_stats_callback() {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Check if API key is configured
        $api_key = steam_api_get_api_key();
        if (!$api_key) {
            wp_send_json(array('error' => __('Steam API key is not configured. Please contact the site administrator.', STEAM_API_TEXT_DOMAIN)));
            wp_die();
        }
        
        $inputValue = sanitize_text_field($_POST['steamId']); // Get player ID

        // Get Steam ID from the user input.
        $steamId = getSteamID64($inputValue);

        if (!empty($steamId)) {
            // Check cache first
            $cache_key = 'steam_api_' . md5($steamId);
            $cached_data = get_transient($cache_key);
            
            if ($cached_data !== false) {
                // Return cached data
                wp_send_json($cached_data);
            } else {
                // Query Steam API for player stats.
                $player_stats = fetch_steam_player_stats($steamId);
                
                // Cache the result
                set_transient($cache_key, $player_stats, steam_api_get_cache_duration());
                
                // Send data back to the client.
                wp_send_json($player_stats);
            }
        } else {
            wp_send_json(array('error' => __('Could not find Steam profile. Please check your input and try again.', STEAM_API_TEXT_DOMAIN)));
            wp_die();
        }
    }

    wp_die();
}