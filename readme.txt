=== Steam Stats Checker ===
Contributors: develabr
Tags: steam, api, steamid, profile, gaming
Requires at least: 4.7
Tested up to: 6.4
Stable tag: 1.4.2
Requires PHP: 7.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Easily integrate Steam profile information on your WordPress site. Search and display Steam user data using various input formats.

== Description ==

The Steam Stats Checker allows you to embed Steam profile information on your WordPress site. Users can search for Steam profiles using various inputs like SteamID, Steam profile URLs, or custom profile names. The plugin displays detailed information including avatar, level, various Steam IDs, account creation date, and more.

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
* Secure API handling and error management

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

= What happens if I don't enter a Steam API key? =

The plugin will display a notification to administrators to configure the API key. For regular visitors, the plugin will show a message that the feature is temporarily unavailable.

== Screenshots ==

1. Steam profile search form
2. Profile information display
3. Settings page

== Changelog === 1.4.2 - 25.03.25 =* Fixed issue with player level display where multi-digit levels would overflow the circle* Added responsive sizing for level indicator to properly handle 2+, 3+, 4+ and 5+ digit numbers* Implemented enhanced player statistics via new [enhanced_steam_api] shortcode* Added extended profile information showing games count, playtime, friends count, and more* Added recently played games section with playtime statistics* Added wishlist and inventory status displays* Improved layout and styling for both basic and enhanced profile views* Fixed various localization issues with translation strings
= 1.4.1 - 21.03.25 =
* Renamed plugin to "Steam Stats Checker" for better description of functionality
* Improved internationalization by moving text domain loading to the 'init' hook
* Enhanced compatibility with translation plugins like Loco Translate
* Fixed translation issues with admin notices and complex strings
* Added proper escaping for translated texts throughout the plugin
* Improved code security with ABSPATH check to prevent direct file access
* Optimized translation string handling in JavaScript

= 1.3 - 10.03.25 =
* Added full translation support with __() and _e() functions
* Removed hardcoded Russian strings from JavaScript and HTML
* Implemented proper text domain and translation infrastructure
* Added Russian translation (ru_RU)
* Created template directory structure for better organization
* Improved error handling with internationalized messages

= 1.2 - 09.03.25 =
* Removed hardcoded API key for improved security
* Replaced all file_get_contents() calls with WordPress HTTP API (wp_remote_get())
* Switched from HTTP to HTTPS for all API requests
* Added proper error handling throughout the plugin
* Improved API key validation in settings
* Added user-friendly messages when API key is not configured
* Enhanced security for XML parsing
* Better validation of API responses

= 1.1 - 09.03.25 =
* Added settings page for API key and cache duration management
* Implemented caching system using WordPress transients
* Improved error handling for API requests

= 1.0 =
* Initial release

== Upgrade Notice ==

= 1.2 =
Security update: This version removes hardcoded API keys, improves error handling, and enhances security for API requests. Please update immediately and ensure your API key is properly configured in Settings → Steam API.

= 1.1 =
This update adds a settings page for the Steam API key and implements caching for better performance.
