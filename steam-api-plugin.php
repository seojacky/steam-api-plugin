<?php
/*
Plugin Name: Steam Stats Checker
Description: Plugin adds opportunity to check Steam ID info on your web-pages.
Version: 1.4.1
Author: develabr, seo_jacky
Author URI: https://t.me/big_jacky
Plugin URI: https://github.com/seojacky/steam-api-plugin
GitHub Plugin URI: https://github.com/seojacky/steam-api-plugin
Text Domain: steam-api-plugin
Domain Path: /languages
*/

// Prohibition of direct access to the file
if (!defined('ABSPATH')) {
    exit;
}

// Load plugin text domain
function steam_api_load_textdomain() {
    load_plugin_textdomain(
        'steam-api-plugin',
        false,
        dirname(plugin_basename(__FILE__)) . '/languages/'
    );
}
add_action('init', 'steam_api_load_textdomain');

require_once(plugin_dir_path(__FILE__) . 'ajax/ajax-handler.php');
// Connecting the file with the user input processing function
require_once(plugin_dir_path(__FILE__) . 'inc/input-processing.php');
require_once(plugin_dir_path(__FILE__) . 'settings-page.php');

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

// Admin notice if API key is not configured - Option 2: Using esc_html functions
function steam_api_admin_notice() {
    $settings = get_option('steam_api_settings');
    if (empty($settings['api_key']) && current_user_can('manage_options')) {
        ?>
        <div class="notice notice-error is-dismissible">
            <p>
                <?php esc_html_e('Steam API Plugin requires a Steam API key to function.', 'steam-api-plugin'); ?>
                <a href="<?php echo esc_url(admin_url('options-general.php?page=steam_api_settings')); ?>">
                    <?php esc_html_e('Configure it now', 'steam-api-plugin'); ?>
                </a>
            </p>
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
        'offline' => __('🔴 Offline', 'steam-api-plugin'),
        'online' => __('🟢 Online', 'steam-api-plugin'),
        'busy' => __('Busy', 'steam-api-plugin'),
        'away' => __('Away', 'steam-api-plugin'),
        'snooze' => __('Snooze', 'steam-api-plugin'),
        'lookingToTrade' => __('Looking to trade', 'steam-api-plugin'),
        'lookingToPlay' => __('Looking to play', 'steam-api-plugin'),
        'unknown' => __('Unknown', 'steam-api-plugin'),
        'private' => __('Private', 'steam-api-plugin'),
        'public' => __('Public', 'steam-api-plugin'),
        'level' => __('Level', 'steam-api-plugin'),
        'copyButton' => __('Copy', 'steam-api-plugin'),
        'copyButtonDone' => __('Done', 'steam-api-plugin'),
        'steamID2' => __('SteamID2', 'steam-api-plugin'),
        'steamID3' => __('SteamID3', 'steam-api-plugin'),
        'steamID64' => __('SteamID64', 'steam-api-plugin'),
        'realName' => __('Real Name', 'steam-api-plugin'),
        'hidden' => __('Hidden', 'steam-api-plugin'),
        'profileURL' => __('Profile URL', 'steam-api-plugin'),
        'accountCreated' => __('Account created', 'steam-api-plugin'),
        'visibility' => __('Visibility', 'steam-api-plugin'),
        'status' => __('Status', 'steam-api-plugin'),
        'location' => __('Location', 'steam-api-plugin'),
        'avatar' => __('Avatar', 'steam-api-plugin'),
        'errorFetchingData' => __('Error fetching player data.', 'steam-api-plugin'),
        'playerNotFound' => __('Player data not found.', 'steam-api-plugin'),
        'find' => __('Find', 'steam-api-plugin'),
        'enterDetails' => __('Enter SteamID / SteamCommunityID / Profile Name / Profile URL', 'steam-api-plugin'),
        'findSteamId' => __('Find and get your Steam ID, Steam ID 64, customURL and community ID', 'steam-api-plugin'),
        'examples' => __('For example:
Heavenanvil
76561198036370701
STEAM_0:1:38052486
steamcommunity.com/id/heavenanvil
steamcommunity.com/profiles/76561198036370701', 'steam-api-plugin'),
        'lastLogin' => __('Last Login', 'steam-api-plugin'),
        'vacBanStatus' => __('VAC Ban Status', 'steam-api-plugin'),
        'tradeBanStatus' => __('Trade Ban Status', 'steam-api-plugin'),
        'yes' => __('Yes', 'steam-api-plugin'),
        'no' => __('No', 'steam-api-plugin'),
        'bans' => __('bans', 'steam-api-plugin'),
        'daysSinceLastBan' => __('days since last ban', 'steam-api-plugin'),
        'permanentBan' => __('Permanent Ban', 'steam-api-plugin')
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
                   sprintf(__('Steam API key is not configured. %sConfigure it now%s.', 'steam-api-plugin'), 
                   '<a href="' . admin_url('options-general.php?page=steam_api_settings') . '">', '</a>') . 
                   '</div></div>';
        } else {
            return '<div class="steam-api-wrapper"><div class="steam-api-error">' . 
                   __('This feature is currently unavailable. Please contact the site administrator.', 'steam-api-plugin') . 
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
            wp_send_json(array('error' => __('Steam API key is not configured. Please contact the site administrator.', 'steam-api-plugin')));
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
            wp_send_json(array('error' => __('Could not find Steam profile. Please check your input and try again.', 'steam-api-plugin')));
            wp_die();
        }
    }

    wp_die();
}
