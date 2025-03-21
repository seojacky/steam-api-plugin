<?php
/**
 * Steam API Plugin - Function to fetch player stats from Steam API
 * 
 * Version: 1.3
 */
function fetch_steam_player_stats($steamId) {
    // Get API key from WordPress options
    $api_key = steam_api_get_api_key();
    
    // Check if API key is available
    if (!$api_key) {
        return array('error' => __('Steam API key is not configured.', 'steam-api-plugin'));
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
            
            // Add last login time information
            $player_stats['last_logoff'] = isset($player_info['lastlogoff']) ? $player_info['lastlogoff'] : null;
        } else {
            return array('error' => __('Player profile not found.', 'steam-api-plugin'));
        }
    } else {
        $error_message = is_wp_error($profile_response) ? $profile_response->get_error_message() : __('Unknown error when contacting Steam API', 'steam-api-plugin');
        return array('error' => __('Error while requesting Steam API:', 'steam-api-plugin') . ' ' . $error_message);
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
        $error_message = is_wp_error($level_response) ? $level_response->get_error_message() : __('Unknown error when contacting Steam API', 'steam-api-plugin');
        return array('error' => __('Error while requesting Steam API:', 'steam-api-plugin') . ' ' . $error_message);
    }
    
    // Get player ban information
    $bans_url = "https://api.steampowered.com/ISteamUser/GetPlayerBans/v1/?key={$api_key}&steamids={$steamId}";
    $bans_response = wp_remote_get($bans_url);
    
    if (!is_wp_error($bans_response) && wp_remote_retrieve_response_code($bans_response) === 200) {
        $bans_data = json_decode(wp_remote_retrieve_body($bans_response), true);
        if (isset($bans_data['players'][0])) {
            $bans_info = $bans_data['players'][0];
            $player_stats['vac_banned'] = isset($bans_info['VACBanned']) ? $bans_info['VACBanned'] : false;
            $player_stats['vac_ban_count'] = isset($bans_info['NumberOfVACBans']) ? $bans_info['NumberOfVACBans'] : 0;
            $player_stats['days_since_last_ban'] = isset($bans_info['DaysSinceLastBan']) ? $bans_info['DaysSinceLastBan'] : 0;
            $player_stats['community_banned'] = isset($bans_info['CommunityBanned']) ? $bans_info['CommunityBanned'] : false;
            $player_stats['economy_ban'] = isset($bans_info['EconomyBan']) ? $bans_info['EconomyBan'] : 'none';
            $player_stats['game_ban_count'] = isset($bans_info['NumberOfGameBans']) ? $bans_info['NumberOfGameBans'] : 0;
        } else {
            // Set default values if player data not found in response
            $player_stats['vac_banned'] = false;
            $player_stats['vac_ban_count'] = 0;
            $player_stats['days_since_last_ban'] = 0;
            $player_stats['community_banned'] = false;
            $player_stats['economy_ban'] = 'none';
            $player_stats['game_ban_count'] = 0;
        }
    } else {
        $error_message = is_wp_error($bans_response) ? $bans_response->get_error_message() : __('Unknown error when contacting Steam API', 'steam-api-plugin');
        return array('error' => __('Error while requesting Steam API:', 'steam-api-plugin') . ' ' . $error_message);
    }

    return $player_stats;
}