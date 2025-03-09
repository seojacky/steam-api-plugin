<?php
/**
 * Input processing functions for Steam API
 *
 * @package SteamAPIPlugin
 */

/**
 * Resolves various formats of Steam identifiers to SteamID64
 *
 * @param string $user Input from user (can be SteamID, SteamID64, vanity URL, etc.)
 * @return string|false SteamID64 if successful, false otherwise
 */
function getSteamID64($user) {
    if (empty($user)) {
        return false;
    }
    
    // Get API key from WordPress options
    $api_key = steam_api_get_api_key();
    
    if (empty($api_key)) {
        return false;
    }
    
    // Sanitize input
    $input = trim(sanitize_text_field($user));
    
    // Regular expressions for different Steam ID formats
    $patterns = array(
        'steamid1' => '/^STEAM_0:([0|1]):([\d]+)$/', // STEAM_0:1:38052486
        'steamid2' => '/^([\d]{17})$/', // 76561198036370701
        'vanity'   => '/^[^-_\d]{1}[-a-zA-Z_\d]+$/', // Heavenanvil
        'profile'  => '~^(?:https?://)?(?:www\.)?steamcommunity\.com/profiles/([^-_]{1}[\d]+)/?$~', // steamcommunity.com/profiles/76561198036370701
        'id'       => '~^(?:https?://)?(?:www\.)?steamcommunity\.com/id/([^-_]{1}[-a-zA-Z_\d]+)/?$~' // steamcommunity.com/id/heavenanvil
    );
    
    try {
        // Case 1: Input is a SteamID (STEAM_0:1:38052486)
        if (preg_match($patterns['steamid1'], $input, $matches)) {
            $y = (int)$matches[1];
            $z = (int)$matches[2];
            $steamid64 = bcadd(bcadd(bcmul($z, '2'), '76561197960265728'), $y);
            return $steamid64;
        }
        
        // Case 2: Input is already a SteamID64 (76561198036370701)
        if (preg_match($patterns['steamid2'], $input)) {
            return $input;
        }
        
        // Case 3: Input is a profile URL (steamcommunity.com/profiles/76561198036370701)
        if (preg_match($patterns['profile'], $input, $matches)) {
            return $matches[1];
        }
        
        // Case 4: Input is a vanity URL (steamcommunity.com/id/heavenanvil)
        if (preg_match($patterns['id'], $input, $matches)) {
            $vanity_name = $matches[1];
            return steam_api_resolve_vanity_url($vanity_name, $api_key);
        }
        
        // Case 5: Input is a vanity name (Heavenanvil)
        if (preg_match($patterns['vanity'], $input)) {
            return steam_api_resolve_vanity_url($input, $api_key);
        }
        
        return false;
        
    } catch (Exception $e) {
        error_log('Steam API Plugin Error: ' . $e->getMessage());
        return false;
    }
}

/**
 * Resolves a vanity URL to a SteamID64
 *
 * @param string $vanity_name Vanity URL name
 * @param string $api_key Steam API key
 * @return string|false SteamID64 if successful, false otherwise
 */
function steam_api_resolve_vanity_url($vanity_name, $api_key) {
    $resolve_url = "https://api.steampowered.com/ISteamUser/ResolveVanityURL/v1/?key={$api_key}&vanityurl={$vanity_name}";
    
    $response = wp_remote_get($resolve_url);
    
    if (is_wp_error($response)) {
        return false;
    }
    
    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);
    
    if (isset($data['response']['success']) && $data['response']['success'] == 1) {
        return $data['response']['steamid'];
    }
    
    return false;
}

/**
 * Converts SteamID64 to SteamID32
 *
 * @param string $steam_id64 SteamID64 format ID
 * @return string SteamID32 format
 */
function getSteamID32($steam_id64) {
    if (strlen($steam_id64) === 17) {
        // Use BC Math for large integer calculations
        $account_id = bcsub($steam_id64, '76561197960265728');
    } else {
        // This is an error condition, but return a formatted result for compatibility
        $account_id = $steam_id64;
    }
    
    return $account_id;
}
