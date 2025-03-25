<?php
/**
 * Steam API Plugin - Function to fetch player stats from Steam API
 * 
 * Version: 1.5
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
            
            // Add ban info in more structured way for UI display
            $player_stats['vacBanned'] = $player_stats['vac_banned'];
            $player_stats['vacBanCount'] = $player_stats['vac_ban_count'];
            $player_stats['daysSinceLastBan'] = $player_stats['days_since_last_ban'];
            $player_stats['gameBanCount'] = $player_stats['game_ban_count'];
            $player_stats['economyBan'] = $player_stats['economy_ban'];
            $player_stats['communityBanned'] = $player_stats['community_banned'];
        } else {
            // Set default values if player data not found in response
            $player_stats['vac_banned'] = false;
            $player_stats['vac_ban_count'] = 0;
            $player_stats['days_since_last_ban'] = 0;
            $player_stats['community_banned'] = false;
            $player_stats['economy_ban'] = 'none';
            $player_stats['game_ban_count'] = 0;
            
            $player_stats['vacBanned'] = false;
            $player_stats['vacBanCount'] = 0;
            $player_stats['daysSinceLastBan'] = 0;
            $player_stats['communityBanned'] = false;
            $player_stats['economyBan'] = 'none';
            $player_stats['gameBanCount'] = 0;
        }
    } else {
        $error_message = is_wp_error($bans_response) ? $bans_response->get_error_message() : __('Unknown error when contacting Steam API', 'steam-api-plugin');
        return array('error' => __('Error while requesting Steam API:', 'steam-api-plugin') . ' ' . $error_message);
    }
    
    // Only fetch additional data if profile is not private
    if (isset($player_stats['communityvisibilitystate']) && $player_stats['communityvisibilitystate'] == 3) {
        // Fetch recently played games
        $recent_games_url = "https://api.steampowered.com/IPlayerService/GetRecentlyPlayedGames/v1/?key={$api_key}&steamid={$steamId}&count=5";
        $recent_games_response = wp_remote_get($recent_games_url);
        
        if (!is_wp_error($recent_games_response) && wp_remote_retrieve_response_code($recent_games_response) === 200) {
            $recent_games_data = json_decode(wp_remote_retrieve_body($recent_games_response), true);
            if (isset($recent_games_data['response']['games'])) {
                $player_stats['recentGames'] = $recent_games_data['response']['games'];
                $player_stats['recentGamesCount'] = isset($recent_games_data['response']['total_count']) ? 
                    $recent_games_data['response']['total_count'] : count($recent_games_data['response']['games']);
            } else {
                $player_stats['recentGames'] = array();
                $player_stats['recentGamesCount'] = 0;
            }
        }
        
        


// Fetch owned games count and total playtime
$owned_games_url = "https://api.steampowered.com/IPlayerService/GetOwnedGames/v1/?key={$api_key}&steamid={$steamId}&include_appinfo=1&include_played_free_games=1";
$owned_games_response = wp_remote_get($owned_games_url);

if (!is_wp_error($owned_games_response) && wp_remote_retrieve_response_code($owned_games_response) === 200) {
    $owned_games_data = json_decode(wp_remote_retrieve_body($owned_games_response), true);
    if (isset($owned_games_data['response']['games'])) {
        $player_stats['gamesCount'] = count($owned_games_data['response']['games']);
        
        // Calculate total playtime across all games
        $total_playtime = 0;
        foreach ($owned_games_data['response']['games'] as $game) {
            $total_playtime += isset($game['playtime_forever']) ? $game['playtime_forever'] : 0;
        }
        $player_stats['totalPlaytime'] = $total_playtime; // In minutes
        
        // Подготавливаем топ-5 игр с наибольшим временем игры
        if (isset($owned_games_data['response']['games']) && count($owned_games_data['response']['games']) > 0) {
            // Копируем массив игр, чтобы не менять оригинальные данные
            $all_games = $owned_games_data['response']['games'];
            
            // Сортируем игры по времени игры (от большего к меньшему)
            usort($all_games, function($a, $b) {
                return $b['playtime_forever'] - $a['playtime_forever'];
            });
            
            // Берем только первые 5 игр
            $top_games = array_slice($all_games, 0, 5);
            
            // Проверяем, что игры действительно имеют какое-то время игры
            $filtered_top_games = array_filter($top_games, function($game) {
                return isset($game['playtime_forever']) && $game['playtime_forever'] > 0;
            });
            
            // Если есть игры с игровым временем, сохраняем их
            if (!empty($filtered_top_games)) {
                $player_stats['topGames'] = array_values($filtered_top_games);
                $player_stats['topGamesCount'] = count($filtered_top_games);
            } else {
                $player_stats['topGames'] = array();
                $player_stats['topGamesCount'] = 0;
            }
        } else {
            $player_stats['topGames'] = array();
            $player_stats['topGamesCount'] = 0;
        }
    } else {
        $player_stats['gamesCount'] = 0;
        $player_stats['totalPlaytime'] = 0;
        $player_stats['topGames'] = array();
        $player_stats['topGamesCount'] = 0;
    }
} else {
    $error_message = is_wp_error($owned_games_response) ? $owned_games_response->get_error_message() : "HTTP " . wp_remote_retrieve_response_code($owned_games_response);
    $player_stats['gamesCount'] = 0;
    $player_stats['totalPlaytime'] = 0;
    $player_stats['topGames'] = array();
    $player_stats['topGamesCount'] = 0;
}


        
        // Fetch friend list
        $friends_url = "https://api.steampowered.com/ISteamUser/GetFriendList/v1/?key={$api_key}&steamid={$steamId}&relationship=friend";
        $friends_response = wp_remote_get($friends_url);
        
        if (!is_wp_error($friends_response) && wp_remote_retrieve_response_code($friends_response) === 200) {
            $friends_data = json_decode(wp_remote_retrieve_body($friends_response), true);
            if (isset($friends_data['friendslist']['friends'])) {
                $player_stats['friendsCount'] = count($friends_data['friendslist']['friends']);
                // Get the first 5 friends for display
                $player_stats['friends'] = array_slice($friends_data['friendslist']['friends'], 0, 5);
            } else {
                $player_stats['friendsCount'] = 0;
                $player_stats['friends'] = array();
            }
        } else {
            // If we get a 401 error, it means the friends list is private
            $player_stats['friendsCount'] = 'private';
            $player_stats['friends'] = array();
        }
        
        // Fetch wishlist (using custom endpoint)
        $wishlist_url = "https://store.steampowered.com/wishlist/profiles/{$steamId}/wishlistdata/";
        $wishlist_response = wp_remote_get($wishlist_url);
        
        if (!is_wp_error($wishlist_response) && wp_remote_retrieve_response_code($wishlist_response) === 200) {
            $wishlist_data = json_decode(wp_remote_retrieve_body($wishlist_response), true);
            if ($wishlist_data && is_array($wishlist_data)) {
                $player_stats['wishlistCount'] = count($wishlist_data);
                
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
                $player_stats['wishlist'] = $wishlist_items;
            } else {
                $player_stats['wishlistCount'] = 0;
                $player_stats['wishlist'] = array();
            }
        }
        
// Get achievements based on games with most achievements
// First, get owned games
if (!isset($owned_games_data) || !isset($owned_games_data['response']['games'])) {
    // Fetch owned games if not already fetched
    $owned_games_url = "https://api.steampowered.com/IPlayerService/GetOwnedGames/v1/?key={$api_key}&steamid={$steamId}&include_appinfo=1&include_played_free_games=1";
    $owned_games_response = wp_remote_get($owned_games_url);
    
    if (!is_wp_error($owned_games_response) && wp_remote_retrieve_response_code($owned_games_response) === 200) {
        $owned_games_data = json_decode(wp_remote_retrieve_body($owned_games_response), true);
    }
}

if (isset($owned_games_data['response']['games']) && count($owned_games_data['response']['games']) > 0) {
    // First sort games by playtime
    usort($owned_games_data['response']['games'], function($a, $b) {
        return $b['playtime_forever'] - $a['playtime_forever'];
    });
    
    // Create array to store games with their achievements
    $games_with_achievements = [];
    
    // Check top-10 games for achievements
    foreach(array_slice($owned_games_data['response']['games'], 0, 10) as $game) {
        $appid = $game['appid'];
        $achievements_url = "https://api.steampowered.com/ISteamUserStats/GetPlayerAchievements/v1/?key={$api_key}&steamid={$steamId}&appid={$appid}";
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
                    // Save game with achievement info
                    $games_with_achievements[] = [
                        'name' => $game['name'],
                        'appid' => $appid,
                        'total_achievements' => $total_achievements,
                        'completed_achievements' => $completed_achievements,
                        'percentage' => round(($completed_achievements / $total_achievements) * 100, 2)
                    ];
                }
            }
        }
    }
    
    // If we found games with achievements, sort them by achievement count
    if (!empty($games_with_achievements)) {
        usort($games_with_achievements, function($a, $b) {
            return $b['total_achievements'] - $a['total_achievements'];
        });
        
        // Get game with most achievements
        $best_game = $games_with_achievements[0];
        $player_stats['achievementPercentage'] = $best_game['percentage'];
        $player_stats['achievementGameName'] = $best_game['name'];
        $player_stats['achievementTotalCount'] = $best_game['total_achievements'];
        $player_stats['achievementCompletedCount'] = $best_game['completed_achievements'];
    } else {
        $player_stats['achievementPercentage'] = 'N/A';
        $player_stats['achievementGameName'] = '';
        $player_stats['achievementTotalCount'] = 0;
        $player_stats['achievementCompletedCount'] = 0;
    }
} else {
    $player_stats['achievementPercentage'] = 'N/A';
    $player_stats['achievementGameName'] = '';
    $player_stats['achievementTotalCount'] = 0;
    $player_stats['achievementCompletedCount'] = 0;
}
        
        // Try to get inventory status
        $inventory_url = "https://steamcommunity.com/inventory/{$steamId}/730/2?count=1"; // CS:GO inventory as example
        $inventory_response = wp_remote_get($inventory_url);
        
        if (!is_wp_error($inventory_response)) {
            $response_code = wp_remote_retrieve_response_code($inventory_response);
            if ($response_code === 200) {
                $inventory_data = json_decode(wp_remote_retrieve_body($inventory_response), true);
                $player_stats['inventoryAccessible'] = isset($inventory_data['assets']) ? true : false;
            } else {
                $player_stats['inventoryAccessible'] = false;
            }
        } else {
            $player_stats['inventoryAccessible'] = false;
        }
    } else {
        // Profile is private, set default values
        $player_stats['recentGames'] = array();
        $player_stats['recentGamesCount'] = 0;
        $player_stats['gamesCount'] = 'private';
        $player_stats['totalPlaytime'] = 'private';
        $player_stats['friendsCount'] = 'private';
        $player_stats['friends'] = array();
        $player_stats['wishlistCount'] = 'private';
        $player_stats['wishlist'] = array();
        $player_stats['achievementPercentage'] = 'private';
        $player_stats['inventoryAccessible'] = false;
    }
	
	return $player_stats;
}