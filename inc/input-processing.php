<?php
/**
 * Steam API Plugin - Functions for processing various Steam ID formats
 * 
 * Version: 1.3
 */
function getSteamID64($user) {
    // Get API key from WordPress options
    $apikey = steam_api_get_api_key();
    
    // Check if API key is available
    if (!$apikey) {
        return false;
    }

    if (isset($user) && !empty($user)) {
        if (isset($user)) {
            $okay = $user;
        }

        $steamid1 = '/^STEAM_0:([0|1]):([\d]+)$/'; //STEAM_0:1:38052486
        $steamid2 = '/^([\d]+)$/'; //76561198036370701
        $steamid3 = '/^[^-_\d]{1}[-a-zA-Z_\d]+$/'; //Heavenanvil
        $steamid4 = '~^(http[s]?://)?(www\.)?steamcommunity.com/profiles/([^-_]{1}[\d(/)?]+)$~'; //steamcommunity.com/profiles/76561198036370701
        $steamid5 = '~^(http[s]?://)?(www\.)?steamcommunity.com/id/([^-_]{1}[-a-zA-Z_\d(/)?]+)$~'; //steamcommunity.com/id/heavenanvil

        // Обращение к api.steampowered.com

        if (preg_match($steamid1, $okay, $matches)) // If input is like "STEAM_0:1:38052486"
        {
            $valid1     = $matches[1];
            $valid2     = $matches[2];
            $realokay   = ($valid2 * 2) + 76561197960265728 + $valid1; // Formula to calculate steamID64 from STEAM_0:X:XXXXXXXX
            
            // Use WordPress HTTP API instead of file_get_contents
            $response = wp_remote_get("https://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key=$apikey&steamids=$realokay");
            
            if (!is_wp_error($response) && wp_remote_retrieve_response_code($response) === 200) {
                $urljson = wp_remote_retrieve_body($response);
                $data_obj = json_decode($urljson);
                if (isset($data_obj->response->players[0])) {
                    $data = (array) $data_obj->response->players[0];
                    $profileurl = $data['profileurl']; // Find profileurl (customurl)
                }
            } else {
                return false;
            }
        }
        
        if (preg_match($steamid2, $okay)) {
            // Use WordPress HTTP API instead of file_get_contents
            $response = wp_remote_get("https://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key=$apikey&steamids=$okay");
            
            if (!is_wp_error($response) && wp_remote_retrieve_response_code($response) === 200) {
                $urljson = wp_remote_retrieve_body($response);
                $data_obj = json_decode($urljson);
                if (isset($data_obj->response->players[0])) {
                    $data = (array) $data_obj->response->players[0];
                    $profileurl = $data['profileurl']; // Find profileurl (customurl)
                }
            } else {
                return false;
            }
        }
        
        if (preg_match($steamid4, $profileurl, $matchespro)) // If profileurl is like "steamcommunity.com/profiles/76561198036370701", extract "76561198036370701"
        {
            if (substr($matchespro[3], -1) == '/') // If there's a trailing "/"
            {
                $myurl = substr($matchespro[3], 0, -1); // Remove it
            } else {
                $myurl = $matchespro[3];
            }

            // Use WordPress HTTP API to fetch and parse XML instead of simplexml_load_file
            $xml_url = "https://steamcommunity.com/profiles/$myurl/?xml=1";
            $response = wp_remote_get($xml_url);
            
            if (!is_wp_error($response) && wp_remote_retrieve_response_code($response) === 200) {
                $xml_content = wp_remote_retrieve_body($response);
                $url = simplexml_load_string($xml_content);
                $link = "https://steamcommunity.com/profiles/$myurl";
            } else {
                return false;
            }
        }

        if (preg_match($steamid5, $profileurl, $matchesid)) // If profileurl is like "steamcommunity.com/id/heavenanvil", extract "heavenanvil"
        {
            if (substr($matchesid[3], -1) == '/') // If there's a trailing "/"
            {
                $myurl = substr($matchesid[3], 0, -1); // Remove it
            } else {
                $myurl = $matchesid[3];
            }
            
            // Use WordPress HTTP API to fetch and parse XML
            $xml_url = "https://steamcommunity.com/id/$myurl/?xml=1";
            $response = wp_remote_get($xml_url);
            
            if (!is_wp_error($response) && wp_remote_retrieve_response_code($response) === 200) {
                $xml_content = wp_remote_retrieve_body($response);
                $url = simplexml_load_string($xml_content);
                $link = "https://steamcommunity.com/id/$myurl";
            } else {
                return false;
            }
        }
        
        if (preg_match($steamid3, $okay)) // If input is like "Heavenanvil"
        {
            // Use WordPress HTTP API to fetch and parse XML
            $xml_url = "https://steamcommunity.com/id/$okay/?xml=1";
            $response = wp_remote_get($xml_url);
            
            if (!is_wp_error($response) && wp_remote_retrieve_response_code($response) === 200) {
                $xml_content = wp_remote_retrieve_body($response);
                $url = simplexml_load_string($xml_content);
                $link = "https://steamcommunity.com/id/$okay";
            } else {
                return false;
            }
        }
        
        if (preg_match($steamid4, $okay)) {
            if (preg_match($steamid4, $okay, $matchespro)) // If input is like "steamcommunity.com/profiles/76561198036370701", extract "76561198036370701"
            {
                if (substr($matchespro[3], -1) == '/') // If there's a trailing "/"
                {
                    $myurl = substr($matchespro[3], 0, -1); // Remove it
                } else {
                    $myurl = $matchespro[3];
                }

                // Use WordPress HTTP API instead of file_get_contents
                $response = wp_remote_get("https://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key=$apikey&steamids=$myurl");
                
                if (!is_wp_error($response) && wp_remote_retrieve_response_code($response) === 200) {
                    $urljson = wp_remote_retrieve_body($response);
                    $data_obj = json_decode($urljson);
                    if (isset($data_obj->response->players[0])) {
                        $data = (array) $data_obj->response->players[0];
                        $profileurl = $data['profileurl']; // Check if there's a customurl
                    }
                } else {
                    return false;
                }
                
                if (preg_match($steamid4, $profileurl, $matchesprox)) // If profileurl is like "steamcommunity.com/profiles/76561198036370701", extract "76561198036370701"
                {
                    if (substr($matchesprox[3], -1) == '/') // If there's a trailing "/"
                    {
                        $myurlx = substr($matchesprox[3], 0, -1); // Remove it
                    } else {
                        $myurlx = $matchesprox[3];
                    }

                    // Use WordPress HTTP API to fetch and parse XML
                    $xml_url = "https://steamcommunity.com/profiles/$myurlx/?xml=1";
                    $response = wp_remote_get($xml_url);
                    
                    if (!is_wp_error($response) && wp_remote_retrieve_response_code($response) === 200) {
                        $xml_content = wp_remote_retrieve_body($response);
                        $url = simplexml_load_string($xml_content);
                        $link = "https://steamcommunity.com/profiles/$myurlx";
                    } else {
                        return false;
                    }
                }

                if (preg_match($steamid5, $profileurl, $matchesprox)) // If profileurl is like "steamcommunity.com/profiles/76561198036370701", extract "76561198036370701"
                {
                    if (substr($matchesprox[3], -1) == '/') // If there's a trailing "/"
                    {
                        $myurlx = substr($matchesprox[3], 0, -1); // Remove it
                    } else {
                        $myurlx = $matchesprox[3];
                    }
                    
                    // Use WordPress HTTP API to fetch and parse XML
                    $xml_url = "https://steamcommunity.com/id/$myurlx/?xml=1";
                    $response = wp_remote_get($xml_url);
                    
                    if (!is_wp_error($response) && wp_remote_retrieve_response_code($response) === 200) {
                        $xml_content = wp_remote_retrieve_body($response);
                        $url = simplexml_load_string($xml_content);
                        $link = "https://steamcommunity.com/id/$myurlx";
                    } else {
                        return false;
                    }
                }
            }
        }

        if (preg_match($steamid5, $okay, $matchesid)) // If profileurl is like "steamcommunity.com/id/heavenanvil", extract "heavenanvil"
        {
            if (substr($matchesid[3], -1) == '/') // If there's a trailing "/"
            {
                $myurl = substr($matchesid[3], 0, -1); // Remove it
            } else {
                $myurl = $matchesid[3];
            }

            // Use WordPress HTTP API to fetch and parse XML
            $xml_url = "https://steamcommunity.com/id/$myurl/?xml=1";
            $response = wp_remote_get($xml_url);
            
            if (!is_wp_error($response) && wp_remote_retrieve_response_code($response) === 200) {
                $xml_content = wp_remote_retrieve_body($response);
                $url = simplexml_load_string($xml_content);
                $link = "https://steamcommunity.com/id/$myurl";
            } else {
                return false;
            }
        }

        // Check if $url is set and has the steamID64 property
        if (isset($url) && isset($url->steamID64)) {
            $sid64 = $url->steamID64;
            
            if (($sid64 - 76561197960265728 - 1) - (($sid64 - 76561197960265728 - 1) / 2) - floor(($sid64 - 76561197960265728 - 1) / 2) == 0) {
                $ass = 1;
            } else {
                $ass = 0;
            }

            $sid         = $sid64 - 76561197960265728;
            $myfriend    = null;
            
            // Try to load friends XML using WordPress HTTP API
            $friends_url = $link . "/friends/?xml=1";
            $friends_response = wp_remote_get($friends_url);
            if (!is_wp_error($friends_response) && wp_remote_retrieve_response_code($friends_response) === 200) {
                $friends_xml = wp_remote_retrieve_body($friends_response);
                $myfriend = @simplexml_load_string($friends_xml);
            }
            
            $linktolvl   = $url->steamID64;
            
            // Get Steam level using WordPress HTTP API
            $level_url = "https://api.steampowered.com/IPlayerService/GetSteamLevel/v1?key=$apikey&steamid=$linktolvl";
            $level_response = wp_remote_get($level_url);
            
            if (!is_wp_error($level_response) && wp_remote_retrieve_response_code($level_response) === 200) {
                $steam_level = wp_remote_retrieve_body($level_response);
                $datalevel_obj = json_decode($steam_level);
                $datalevel = isset($datalevel_obj->response->player_level) ? $datalevel_obj->response->player_level : null;
            }
            
            // Get avatar using WordPress HTTP API
            $id = $url->steamID64;
            $avatar_url = "https://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key=$apikey&steamids=$id";
            $avatar_response = wp_remote_get($avatar_url);
            
            if (!is_wp_error($avatar_response) && wp_remote_retrieve_response_code($avatar_response) === 200) {
                $urljson = wp_remote_retrieve_body($avatar_response);
                $data_obj = json_decode($urljson);
                if (isset($data_obj->response->players[0])) {
                    $data = (array) $data_obj->response->players[0];
                    $request_img = isset($data['avatar']) ? $data['avatar'] : '';
                    $request_loc = isset($data['loccountrycode']) ? mb_strtolower($data['loccountrycode']) : '';
                }
            }
            
            return $url->steamID64;
        }
    }

    return false;
}

function getSteamID32($steamID64)  {
    if (strlen($steamID64) === 17)
    {
        $account_id = substr($steamID64, 3) - 61197960265728;
    }
    else
    {
        $account_id = '765'.($steamID64 + 61197960265728);
    }

    return $account_id;
}