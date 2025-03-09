<?php
/**
 * AJAX handler for Steam API requests
 *
 * @package SteamAPIPlugin
 */

/**
 * Fetches player statistics from Steam API
 *
 * @param string $steam_id The Steam ID to fetch stats for
 * @return array Player statistics or error message
 */
function fetch_steam_player_stats($steam_id) {
    // Get API key from WordPress options
    $api_key = steam_api_get_api_key();
    
    if (empty($api_key)) {
        return array('error' => 'Steam API key is not configured. Please set it in the plugin settings.');
    }

    // Array to store player statistics
    $player_stats = array();

    try {
        // Get player profile information
        $profile_url = "https://api.steampowered.com/ISteamUser/GetPlayerSummaries/v2/?key={$api_key}&steamids={$steam_id}";
        $profile_response = wp_remote_get($profile_url);
        
        if (is_wp_error($profile_response)) {
            return array('error' => 'Error accessing Steam API: ' . $profile_response->get_error_message());
        }
        
        $response_code = wp_remote_retrieve_response_code($profile_response);
        if ($response_code !== 200) {
            return array('error' => 'Steam API returned error code: ' . $response_code);
        }
        
        $profile_body = wp_remote_retrieve_body($profile_response);
        $profile_data = json_decode($profile_body, true);
        
        if (!isset($profile_data['response']['players'][0])) {
            return array('error' => 'Player not found or profile is private');
        }
        
        // Extract player information
        $player_info = $profile_data['response']['players'][0];
        $player_stats['nickname'] = isset($player_info['personaname']) ? $player_info['personaname'] : '';
        $player_stats['steamid'] = isset($player_info['steamid']) ? $player_info['steamid'] : '';
        $player_stats['avatar'] = isset($player_info['avatarfull']) ? $player_info['avatarfull'] : '';
        $player_stats['realname'] = isset($player_info['realname']) ? $player_info['realname'] : '';
        $player_stats['profileurl'] = isset($player_info['profileurl']) ? $player_info['profileurl'] : '';
        $player_stats['timecreated'] = isset($player_info['timecreated']) ? $player_info['timecreated'] : '';
        $player_stats['communityvisibilitystate'] = isset($player_info['communityvisibilitystate']) ? $player_info['communityvisibilitystate'] : '';
        $player_stats['loccountrycode'] = isset($player_info['loccountrycode']) ? $player_info['loccountrycode'] : '';
        $player_stats['locstatecode'] = isset($player_info['locstatecode']) ? $player_info['locstatecode'] : '';
        $player_stats['loccityid'] = isset($player_info['loccityid']) ? $player_info['loccityid'] : '';
        $player_stats['personastate'] = isset($player_info['personastate']) ? $player_info['personastate'] : '';
        
        // Get player's level
        $steam_level_url = "https://api.steampowered.com/IPlayerService/GetSteamLevel/v1?key={$api_key}&steamid={$steam_id}";
        $level_response = wp_remote_get($steam_level_url);
        
        if (!is_wp_error($level_response) && wp_remote_retrieve_response_code($level_response) === 200) {
            $level_body = wp_remote_retrieve_body($level_response);
            $level_data = json_decode($level_body, true);
            
            if (isset($level_data['response']['player_level'])) {
                $player_stats['playerlevel'] = $level_data['response']['player_level'];
            } else {
                $player_stats['playerlevel'] = "";
            }
        } else {
            $player_stats['playerlevel'] = "";
        }
        
        return $player_stats;
        
    } catch (Exception $e) {
        return array('error' => 'Error processing request: ' . $e->getMessage());
    }
}
