<?php
/**
 * Steam API Plugin - Enhanced Function to fetch extended player stats from Steam API
 * 
 * Version: 1.4
 */
function fetch_enhanced_steam_player_stats($steamId) {
    // Get API key from WordPress options
    $api_key = steam_api_get_api_key();
    
    // Check if API key is available
    if (!$api_key) {
        return array('error' => __('Steam API key is not configured.', 'steam-api-plugin'));
    }

    // Get basic player stats first
    $player_stats = fetch_steam_player_stats($steamId);
    
    // If there was an error getting basic stats, return the error
    if (isset($player_stats['error'])) {
        return $player_stats;
    }
    
    // Array to store extended player statistics
    $extended_stats = array();
    
    // Fetch VAC ban status
    $bans_url = "https://api.steampowered.com/ISteamUser/GetPlayerBans/v1/?key={$api_key}&steamids={$steamId}";
    $bans_response = wp_remote_get($bans_url);
    
    if (!is_wp_error($bans_response) && wp_remote_retrieve_response_code($bans_response) === 200) {
        $bans_data = json_decode(wp_remote_retrieve_body($bans_response), true);
        if (isset($bans_data['players'][0])) {
            $ban_info = $bans_data['players'][0];
            $extended_stats['vacBanned'] = $ban_info['VACBanned'] ? true : false;
            $extended_stats['vacBanCount'] = isset($ban_info['NumberOfVACBans']) ? $ban_info['NumberOfVACBans'] : 0;
            $extended_stats['daysSinceLastBan'] = isset($ban_info['DaysSinceLastBan']) ? $ban_info['DaysSinceLastBan'] : 0;
            $extended_stats['gameBanCount'] = isset($ban_info['NumberOfGameBans']) ? $ban_info['NumberOfGameBans'] : 0;
            $extended_stats['economyBan'] = isset($ban_info['EconomyBan']) ? $ban_info['EconomyBan'] : 'none';
            $extended_stats['communityBanned'] = isset($ban_info['CommunityBanned']) ? $ban_info['CommunityBanned'] : false;
        }
    }
    
    // Only fetch additional data if profile is not private
    if (isset($player_stats['communityvisibilitystate']) && $player_stats['communityvisibilitystate'] == 3) {
        // Fetch recently played games
        $recent_games_url = "https://api.steampowered.com/IPlayerService/GetRecentlyPlayedGames/v1/?key={$api_key}&steamid={$steamId}&count=5";
        $recent_games_response = wp_remote_get($recent_games_url);
        
        if (!is_wp_error($recent_games_response) && wp_remote_retrieve_response_code($recent_games_response) === 200) {
            $recent_games_data = json_decode(wp_remote_retrieve_body($recent_games_response), true);
            if (isset($recent_games_data['response']['games'])) {
                $extended_stats['recentGames'] = $recent_games_data['response']['games'];
                $extended_stats['recentGamesCount'] = isset($recent_games_data['response']['total_count']) ? 
                    $recent_games_data['response']['total_count'] : count($recent_games_data['response']['games']);
            } else {
                $extended_stats['recentGames'] = array();
                $extended_stats['recentGamesCount'] = 0;
            }
        }
        
        // Fetch owned games count and total playtime
        $owned_games_url = "https://api.steampowered.com/IPlayerService/GetOwnedGames/v1/?key={$api_key}&steamid={$steamId}&include_appinfo=0&include_played_free_games=1";
        $owned_games_response = wp_remote_get($owned_games_url);
        
        if (!is_wp_error($owned_games_response) && wp_remote_retrieve_response_code($owned_games_response) === 200) {
            $owned_games_data = json_decode(wp_remote_retrieve_body($owned_games_response), true);
            if (isset($owned_games_data['response']['games'])) {
                $extended_stats['gamesCount'] = count($owned_games_data['response']['games']);
                
                // Calculate total playtime across all games
                $total_playtime = 0;
                foreach ($owned_games_data['response']['games'] as $game) {
                    $total_playtime += isset($game['playtime_forever']) ? $game['playtime_forever'] : 0;
                }
                $extended_stats['totalPlaytime'] = $total_playtime; // In minutes
            } else {
                $extended_stats['gamesCount'] = 0;
                $extended_stats['totalPlaytime'] = 0;
            }
        }
        
        // Fetch friend list
        $friends_url = "https://api.steampowered.com/ISteamUser/GetFriendList/v1/?key={$api_key}&steamid={$steamId}&relationship=friend";
        $friends_response = wp_remote_get($friends_url);
        
        if (!is_wp_error($friends_response) && wp_remote_retrieve_response_code($friends_response) === 200) {
            $friends_data = json_decode(wp_remote_retrieve_body($friends_response), true);
            if (isset($friends_data['friendslist']['friends'])) {
                $extended_stats['friendsCount'] = count($friends_data['friendslist']['friends']);
                // Get the first 5 friends for display
                $extended_stats['friends'] = array_slice($friends_data['friendslist']['friends'], 0, 5);
            } else {
                $extended_stats['friendsCount'] = 0;
                $extended_stats['friends'] = array();
            }
        } else {
            // If we get a 401 error, it means the friends list is private
            $extended_stats['friendsCount'] = 'private';
            $extended_stats['friends'] = array();
        }
        
        // Fetch wishlist (using custom endpoint)
        $wishlist_url = "https://store.steampowered.com/wishlist/profiles/{$steamId}/wishlistdata/";
        $wishlist_response = wp_remote_get($wishlist_url);
        
        if (!is_wp_error($wishlist_response) && wp_remote_retrieve_response_code($wishlist_response) === 200) {
            $wishlist_data = json_decode(wp_remote_retrieve_body($wishlist_response), true);
            if ($wishlist_data && is_array($wishlist_data)) {
                $extended_stats['wishlistCount'] = count($wishlist_data);
                
                // Get the first 5 wishlist items for display
                $wishlist_items = array();
                $count = 0;
                foreach ($wishlist_data as $id => $item) {
                    if ($count >= 5) break;
                    if (isset($item['name'])) {
                        $wishlist_items[] = array(
                            'appid' => $id,
                            'name' => $item['name']
                        );
                        $count++;
                    }
                }
                $extended_stats['wishlist'] = $wishlist_items;
            } else {
                $extended_stats['wishlistCount'] = 0;
                $extended_stats['wishlist'] = array();
            }
        }
        
        // Fetch global achievements percentage
        $achievements_url = "https://api.steampowered.com/IPlayerService/GetAchievements/v1/?key={$api_key}&steamid={$steamId}&appid=218620"; // Using PAYDAY 2 as a sample game
        $achievements_response = wp_remote_get($achievements_url);
        
        if (!is_wp_error($achievements_response) && wp_remote_retrieve_response_code($achievements_response) === 200) {
            $achievements_data = json_decode(wp_remote_retrieve_body($achievements_response), true);
            if (isset($achievements_data['playerstats']['achievements'])) {
                $total_achievements = count($achievements_data['playerstats']['achievements']);
                $completed_achievements = 0;
                
                foreach ($achievements_data['playerstats']['achievements'] as $achievement) {
                    if (isset($achievement['achieved']) && $achievement['achieved'] == 1) {
                        $completed_achievements++;
                    }
                }
                
                if ($total_achievements > 0) {
                    $extended_stats['achievementPercentage'] = round(($completed_achievements / $total_achievements) * 100, 2);
                } else {
                    $extended_stats['achievementPercentage'] = 0;
                }
            } else {
                $extended_stats['achievementPercentage'] = 'N/A';
            }
        }
        
        // Try to get inventory status
        $inventory_url = "https://steamcommunity.com/inventory/{$steamId}/730/2?count=1"; // CS:GO inventory as example
        $inventory_response = wp_remote_get($inventory_url);
        
        if (!is_wp_error($inventory_response)) {
            $response_code = wp_remote_retrieve_response_code($inventory_response);
            if ($response_code === 200) {
                $inventory_data = json_decode(wp_remote_retrieve_body($inventory_response), true);
                $extended_stats['inventoryAccessible'] = isset($inventory_data['assets']) ? true : false;
            } else {
                $extended_stats['inventoryAccessible'] = false;
            }
        } else {
            $extended_stats['inventoryAccessible'] = false;
        }
    } else {
        // Profile is private, set default values
        $extended_stats['recentGames'] = array();
        $extended_stats['recentGamesCount'] = 0;
        $extended_stats['gamesCount'] = 'private';
        $extended_stats['totalPlaytime'] = 'private';
        $extended_stats['friendsCount'] = 'private';
        $extended_stats['friends'] = array();
        $extended_stats['wishlistCount'] = 'private';
        $extended_stats['wishlist'] = array();
        $extended_stats['achievementPercentage'] = 'private';
        $extended_stats['inventoryAccessible'] = false;
    }
    
    // Merge the basic and extended stats
    return array_merge($player_stats, $extended_stats);
}

// Register this new AJAX action
add_action('wp_ajax_get_enhanced_player_stats', 'get_enhanced_player_stats_callback');
add_action('wp_ajax_nopriv_get_enhanced_player_stats', 'get_enhanced_player_stats_callback');

function get_enhanced_player_stats_callback() {
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
            $cache_key = 'steam_api_enhanced_' . md5($steamId);
            $cached_data = get_transient($cache_key);
            
            if ($cached_data !== false) {
                // Return cached data
                wp_send_json($cached_data);
            } else {
                // Query Steam API for extended player stats.
                $player_stats = fetch_enhanced_steam_player_stats($steamId);
                
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