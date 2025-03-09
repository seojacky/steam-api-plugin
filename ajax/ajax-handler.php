<?php
function fetch_steam_player_stats($steamId) {
	// Замените YOUR_API_KEY на ваш ключ Steam API
	$api_key = '2EF6E52E83E288756136106E81C4B41E';

	// Массив для хранения статистики игрока
	$player_stats = array();

	// Получение информации о профиле игрока
	$profile_url = "https://api.steampowered.com/ISteamUser/GetPlayerSummaries/v2/?key={$api_key}&steamids={$steamId}";
	$profile_response = file_get_contents($profile_url);

	if ($profile_response !== false) {
			$profile_data = json_decode($profile_response, true);
			if (isset($profile_data['response']['players'][0])) {
					$player_info = $profile_data['response']['players'][0];
					$player_stats['nickname'] = $player_info['personaname'];
					$player_stats['steamid'] = $player_info['steamid'];
					$player_stats['avatar'] = $player_info['avatarfull'];
					$player_stats['realname'] = $player_info['realname'];
					$player_stats['profileurl'] = $player_info['profileurl'];
					// $player_stats['profilestate'] = $player_info['profilestate'];
					$player_stats['timecreated'] = $player_info['timecreated'];
					$player_stats['communityvisibilitystate'] = $player_info['communityvisibilitystate'];
					$player_stats['loccountrycode'] = $player_info['loccountrycode'];
					$player_stats['locstatecode'] = $player_info['locstatecode'];
					$player_stats['loccityid'] = $player_info['loccityid'];
					$player_stats['personastate'] = $player_info['personastate'];
			}
	} else {
			return array('error' => 'Ошибка при выполнении запроса к Steam API: ' . error_get_last()['message']);
	}

	// Get player's level

	$steam_level_url = "http://api.steampowered.com/IPlayerService/GetSteamLevel/v1?key={$api_key}&steamid={$steamId}";
	$level_responce = file_get_contents($steam_level_url);
	if ($level_responce !== false) {
    $datalevel = json_decode($level_responce, true);
    if (isset($datalevel['response']['player_level'])) {
        $player_stats['playerlevel'] = $datalevel['response']['player_level'];
    } else {
				$player_stats['playerlevel'] = "";
        // return array('error' => 'Помилка: значення player_level не знайдено в отриманих даних');
    }
} else {
		return array('error' => 'Ошибка при выполнении запроса к Steam API: ' . error_get_last()['message']);
}

	return $player_stats;
}
