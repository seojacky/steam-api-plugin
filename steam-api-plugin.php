<?php
/*
Plugin Name: Steam API Plugin
Description: Plugin adds opportunity to check Steam ID info on your web-pages.
Version: 1.0
Author: develabr
*/

require_once(plugin_dir_path(__FILE__) . 'ajax/ajax-handler.php');
// Підключення файлу з функцією обробки вводу користувача
require_once(plugin_dir_path(__FILE__) . 'inc/input-processing.php');

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
		function add_module_to_my_script($tag, $handle, $src)
		{
    if ("steam-api-scripts" === $handle) {
        $tag = '<script type="module" src="' . esc_url($src) . '"></script>';
    }

    return $tag;
}

	$localized_data = array(
			'ajax_url' => admin_url('admin-ajax.php'),
	);
	wp_localize_script('steam-api-scripts', 'ajax_object', $localized_data);
}
add_action('wp_enqueue_scripts', 'steam_api_enqueue_scripts');


function steam_api_shortcode() {
	ob_start();

return
    '<div class="steam-api-wrapper">
			<div class="steam-api-info">
				<form class="form" id="steam-form" autocomplete="off">
						<div class="input-group">
								<input type="text" id="steamInput" class="form-input" title="Например:
Heavenanvil
76561198036370701
STEAM_0:1:38052486
steamcommunity.com/id/heavenanvil
steamcommunity.com/profiles/76561198036370701" placeholder="Введите SteamID / SteamCommunityID / Имя профиля / URL профиля" style="border-top-left-radius: .25rem;border-bottom-left-radius: .25rem;">
								<button type="button" id="get-stats-button">Найти</button>
						</div>
						<p class="form-description">Найдите и получите свой Steam ID, Steam ID 64, customURL и идентификатор сообщества</p>
				</form>

			</div>
			<div id="user-info" class="user-info-block"></div>
			<div id="results"></div>
		</div>'
		?>
	<?php
	return ob_get_clean();
}
add_shortcode('steam_api', 'steam_api_shortcode');

add_action('wp_ajax_get_player_stats', 'get_player_stats_callback');
add_action('wp_ajax_nopriv_get_player_stats', 'get_player_stats_callback');

function get_player_stats_callback() {
	if ($_SERVER["REQUEST_METHOD"] == "POST") {
			$inputValue = sanitize_text_field($_POST['steamId']); // Отримуємо ID гравця

			// Отримуємо Steam ID з введеного тексту користувача.
			$steamId = getSteamID64($inputValue, $api_key);

			if (!empty($steamId)) {
					// Запит до Steam API та отримання статистики гравця.
					$player_stats = fetch_steam_player_stats($steamId);

					// Отправка даних у форматі JSON назад на клієнтську сторінку.
					wp_send_json($player_stats);
			}
	}

	wp_die();
}
