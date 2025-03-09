=== Steam API Plugin ===
Contributors: develabr
Tags: steam, api, steamid, profile, gaming
Requires at least: 4.7
Tested up to: 6.4
Stable tag: 1.1
Requires PHP: 7.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Easily integrate Steam profile information on your WordPress site. Search and display Steam user data using various input formats.

== Description ==

The Steam API Plugin allows you to embed Steam profile information on your WordPress site. Users can search for Steam profiles using various inputs like SteamID, Steam profile URLs, or custom profile names. The plugin displays detailed information including avatar, level, various Steam IDs, account creation date, and more.

= Features =

* Search for Steam profiles using multiple input formats:
  * SteamID (STEAM_0:1:38052486)
  * SteamID64 (76561198036370701)
  * Custom URL name (heavenanvil)
  * Profile URL with custom URL (steamcommunity.com/id/heavenanvil)
  * Profile URL with ID (steamcommunity.com/profiles/76561198036370701)
* Display detailed user information:
  * Profile avatar
  * Steam level
  * SteamID conversions (SteamID2, SteamID3, SteamID64)
  * Real name (if public)
  * Profile URL
  * Account creation date
  * Profile visibility
  * Online status with emoji indicators
  * Country location with flag
* One-click copy buttons for easy copying of Steam IDs
* Responsive design for all device sizes
* Simple settings page for API key management
* Cache system to reduce API calls and improve performance

= Usage =

Simply add the shortcode `[steam_api]` to any page or post to display the Steam profile search form.

== Installation ==

1. Upload the `steam-api-plugin` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Navigate to Settings → Steam API to configure your Steam API key
4. Add the `[steam_api]` shortcode to any page or post

== Frequently Asked Questions ==

= How do I get a Steam API key? =

You can get your Steam API key from [Steam Web API Key Registration](https://steamcommunity.com/dev/apikey). You'll need to have a Steam account and accept the Steam API Terms of Use.

= Can I customize the appearance of the plugin? =

Yes, you can customize the appearance by adding your own CSS to your theme or by using a CSS plugin.

= Does this plugin store any Steam data in my database? =

The plugin only temporarily caches API responses to improve performance. No permanent data is stored in your database. You can adjust the cache duration in the plugin settings.

= Why am I seeing errors when searching for profiles? =

Make sure your Steam API key is correctly set up in the plugin settings (Settings → Steam API). Also, ensure the domain you registered with Steam matches your WordPress site domain.

== Screenshots ==

1. Steam profile search form
2. Profile information display
3. Settings page

== Changelog ==

= 1.1 - 09.03.25 =
* Added settings page for API key and cache duration management
* Implemented caching system using WordPress transients
* Improved error handling for API requests

= 1.0 =
* Initial release

== Upgrade Notice ==

= 1.1 =
This update adds a settings page for the Steam API key and implements caching for better performance.
