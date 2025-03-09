<?php

$apikey = '2EF6E52E83E288756136106E81C4B41E';

function getSteamID64($user) {
	global $apikey;

	if (isset($user) && !empty($user)) {
			if (isset($user)) {
				$okay = $user;
			}

	  	$steamid1 = '/^STEAM_0:([0|1]):([\d]+)$/'; //STEAM_0:1:38052486
	  	$steamid2 = '/^([\d]+)$/'; //76561198036370701
	  	$steamid3 = '/^[^-_\d]{1}[-a-zA-Z_\d]+$/'; //Heavenanvil
	  	$steamid4 = '~^(http[s]?://)?(www\.)?steamcommunity.com/profiles/([^-_]{1}[\d(/)?]+)$~'; //steamcommunity.com/profiles/76561198036370701
	  	$steamid5 = '~^(http[s]?://)?(www\.)?steamcommunity.com/id/([^-_]{1}[-a-zA-Z_\d(/)?]+)$~'; //steamcommunity.com/id/heavenanvil

	  	//Обращение к api.steampowered.com

	  	if (preg_match($steamid1, $okay, $matches)) //Если данные из Input вида "STEAM_0:1:38052486"
			{
				$valid1     = $matches[1];
				$valid2     = $matches[2];
		    $realokay   = ($valid2 * 2) + 76561197960265728 + $valid1; //Формула расчета steamID64 из STEAM_0:X:XXXXXXXX
				$urljson    = file_get_contents("http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key=$apikey&steamids=$realokay");
				$data       = (array) json_decode($urljson)->response->players[0];
		    $profileurl = $data['profileurl']; //Находим profileurl (customurl)
			}
			if (preg_match($steamid2, $okay)) {
				$urljson    = file_get_contents("http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key=$apikey&steamids=$okay");
				$data       = (array) json_decode($urljson)->response->players[0];
		    $profileurl = $data['profileurl']; //Находим profileurl (customurl)
			}
	  	if (preg_match($steamid4, $profileurl, $matchespro)) //Если profileurl вида "steamcommunity.com/profiles/76561198036370701", находим "76561198036370701" из ссылки
			{
	    	if (substr($matchespro[3], -1) == '/') //Если на конце знак "/"
					{
	      		$myurl = substr($matchespro[3], 0, -1); //Убираем его
				} else {
					$myurl = $matchespro[3];
				}

				$slf  = "http://steamcommunity.com/profiles/$myurl/?xml=1";
				$url  = simplexml_load_file($slf);
				$link = "http://steamcommunity.com/profiles/$myurl";
			}

	  	if (preg_match($steamid5, $profileurl, $matchesid)) //Если profileurl вида "steamcommunity.com/id/heavenanvil", находим "heavenanvil" из ссылки
			{
	    	if (substr($matchesid[3], -1) == '/') //Если на конце знак "/"
					{
	      		$myurl = substr($matchesid[3], 0, -1); //Убираем его
				} else {
					$myurl = $matchesid[3];
				}
				$slf  = "http://steamcommunity.com/id/$myurl/?xml=1";
				$url  = simplexml_load_file($slf);
				$link = "http://steamcommunity.com/id/$myurl";
			}
	  	if (preg_match($steamid3, $okay)) //Если Input вида "Heavenanvil"
			{
				$slf  = "http://steamcommunity.com/id/$okay/?xml=1";
				$url  = simplexml_load_file($slf);
				$link = "http://steamcommunity.com/id/$okay";
			}
			if (preg_match($steamid4, $okay)) {
	    	if (preg_match($steamid4, $okay, $matchespro)) //Если Input вида "steamcommunity.com/profiles/76561198036370701", находим "76561198036370701" из ссылки
				{
	      	if (substr($matchespro[3], -1) == '/') //Если на конце знак "/"
					{
	        	$myurl = substr($matchespro[3], 0, -1); //Убираем его
					} else {
						$myurl = $matchespro[3];
					}

					$urljson    = file_get_contents("http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key=$apikey&steamids=$myurl");
						$data       = (array) json_decode($urljson)->response->players[0];
		      	$profileurl = $data['profileurl']; //Проверяем, есть ли customurl
	      		if (preg_match($steamid4, $profileurl, $matchesprox)) //Если profileurl вида "steamcommunity.com/profiles/76561198036370701", находим "76561198036370701" из ссылки
						{
	        		if (substr($matchesprox[3], -1) == '/') //Если на конце знак "/"
							{
	          			$myurlx = substr($matchesprox[3], 0, -1); //Убираем его
							} else {
								$myurlx = $matchesprox[3];
							}

							$slf  = "http://steamcommunity.com/profiles/$myurlx/?xml=1";
							$url  = simplexml_load_file($slf);
							$link = "http://steamcommunity.com/profiles/$myurlx";
						}

	      		if (preg_match($steamid5, $profileurl, $matchesprox)) //Если profileurl вида "steamcommunity.com/profiles/76561198036370701", находим "76561198036370701" из ссылки
						{
	        		if (substr($matchesprox[3], -1) == '/') //Если на конце знак "/"
							{
	          			$myurlx = substr($matchesprox[3], 0, -1); //Убираем его
							} else {
								$myurlx = $matchesprox[3];
							}
							$slf  = "http://steamcommunity.com/id/$myurlx/?xml=1";
							$url  = simplexml_load_file($slf);
							$link = "http://steamcommunity.com/id/$myurlx";
					}
				}
			}

	  	if (preg_match($steamid5, $okay, $matchesid)) //Если profileurl вида "steamcommunity.com/id/heavenanvil", находим "heavenanvil" из ссылки
			{
	    	if (substr($matchesid[3], -1) == '/') //Если на конце знак "/"
					{
	      		$myurl = substr($matchesid[3], 0, -1); //Убираем его
				} else {
					$myurl = $matchesid[3];
				}

				$slf  = "http://steamcommunity.com/id/$myurl/?xml=1";
				$url  = simplexml_load_file($slf);
				$link = "http://steamcommunity.com/id/$myurl";
			}

				$sid64 = $url->steamID64;
			if (($sid64 - 76561197960265728 - 1) - (($sid64 - 76561197960265728 - 1) / 2) - floor(($sid64 - 76561197960265728 - 1) / 2) == 0) {
				$ass = 1;
			} else {
				$ass = 0;
			}

		$sid         = $sid64 - 76561197960265728;
		$myfriend    = @simplexml_load_file($link . "/friends/?xml=1");
		$linktolvl   = $url->steamID64;
		$steam_level = @file_get_contents("http://api.steampowered.com/IPlayerService/GetSteamLevel/v1?key=$apikey&steamid=$linktolvl");
		$datalevel   = json_decode($steam_level)->response->player_level;
		$need        = $_GET['need'];
		//Получаем аватарку
		$id          = $url->steamID64;
		$myurl       = 'http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key=' . $apikey . '&steamids=' . $id;
		$urljson     = file_get_contents($myurl);
		$data        = (array) json_decode($urljson)->response->players[0];
		$request_img = $data['avatar'];
		// Получаем код страны для флага
		$data        = (array) json_decode($urljson)->response->players[0];
		$request_loc = $flag = mb_strtolower($data['loccountrycode']);
		//Пишем в БД
		//$result0     = mysqli_query($db, "INSERT INTO `steaminfodb` (request_id,request_cont,datetime,request_img,request_lvl,request_loc) VALUES('$request_id','$userok','$superdate','$request_img','$datalevel','$request_loc')");

	}

	return $url->steamID64;
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
