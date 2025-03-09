<?php
/**
 * Steam API Plugin - Function to fetch player stats from Steam API
 * 
 * Version: 1.2
 */
function fetch_steam_player_stats($steamId) {
    // Get API key from WordPress options
    $api_key = steam_api_get_api_key();
    
    // Check if API key is available
    if (!$api_key) {
        return array('error' => 'Steam API key is not configured.');
    }

    // Array to store player statistics
    $player_stats = array();

    // Get player profile information using WordPress HTTP API instead of file_get_contents
    $profile_url = "https://api.steampowered.com/ISteamUser/GetPlayerSummaries/v2/?key={$api_key}&steamids={$steamId}";
    $profile_response = wp_remote_get($profile_url);
    
    if (!is_wp_error($profile_response) && wp_remote_retrieve_response_code($profile_response) === 200) {
        $profile_data = json_decode(wp_remote_retrieve_body($profile_response), true);
        if (isset($profile_data['response']['players'][0])) {
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
        }
    } else {
        $error_message = is_wp_error($profile_response) ? $profile_response->get_error_message() : 'Unknown error when contacting Steam API';
        return array('error' => 'Error while requesting Steam API: ' . $error_message);
    }

    // Get player's level using WordPress HTTP API
    $steam_level_url = "https://api.steampowered.com/IPlayerService/GetSteamLevel/v1?key={$api_key}&steamid={$steamId}";
    $level_response = wp_remote_get($steam_level_url);
    
    if (!is_wp_error($level_response) && wp_remote_retrieve_response_code($level_response) === 200) {
        $datalevel = json_decode(wp_remote_retrieve_body($level_response), true);
        if (isset($datalevel['response']['player_level'])) {
            $player_stats['playerlevel'] = $datalevel['response']['player_level'];
        } else {
            $player_stats['playerlevel'] = "";
        }
    } else {
        $error_message = is_wp_error($level_response) ? $level_response->get_error_message() : 'Unknown error when contacting Steam API';
        return array('error' => 'Error while requesting Steam API: ' . $error_message);
    }

    return $player_stats;
}
