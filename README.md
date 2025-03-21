# Steam Stats Checker

A WordPress plugin that allows users to check and display Steam profile information using the Steam Web API.

## Features

- Search for Steam profiles using various input formats:
  - SteamID (STEAM_0:1:38052486)
  - SteamID64 (76561198036370701)
  - Custom URL name (heavenanvil)
  - Profile URL (steamcommunity.com/id/heavenanvil)
  - Profile URL with ID (steamcommunity.com/profiles/76561198036370701)
- Displays detailed user information:
  - Profile avatar
  - Steam level
  - SteamID in multiple formats (SteamID2, SteamID3, SteamID64)
  - Profile creation date
  - Profile visibility settings
  - Online status
  - Country/location (with flag emoji)
- One-click copy functionality for Steam IDs
- Responsive design for mobile, tablet, and desktop
- Caching system to reduce API calls and improve performance
- Secure handling of API keys

## Installation

1. Upload the `steam-api-plugin` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Navigate to Settings → Steam API to configure your Steam API key
4. Add the `[steam_api]` shortcode to any page or post where you want the Steam profile lookup form to appear

## Configuration

### Setting Up Your Steam API Key

1. Visit [Steam Web API Key Registration](https://steamcommunity.com/dev/apikey) to obtain your API key
2. In your WordPress admin panel, go to Settings → Steam API
3. Enter your Steam API key in the designated field
4. Configure caching duration (default is 3600 seconds / 1 hour)
5. Save your settings

## Usage

Simply add the shortcode `[steam_api]` to any page or post to display the Steam profile lookup form.

Users can enter any of the following formats to search for a Steam profile:
- SteamID: `STEAM_0:1:38052486`
- SteamID64: `76561198036370701`
- Custom URL name: `heavenanvil`
- Full profile URL: `https://steamcommunity.com/id/heavenanvil`
- Full profile URL with ID: `https://steamcommunity.com/profiles/76561198036370701`

## Security Considerations

- The plugin securely stores your Steam API key in the WordPress options table
- All user inputs are sanitized before processing
- AJAX requests include nonce verification to prevent CSRF attacks
- API responses are cached to reduce external requests
- Error handling prevents exposure of sensitive information

## Frequently Asked Questions

### How do I get a Steam API key?
Visit [Steam Web API Key Registration](https://steamcommunity.com/dev/apikey) and follow the instructions to register a new API key.

### Can I change the appearance of the plugin?
Yes, you can customize the appearance by modifying the CSS in the `css/steam-api-public.css` file or by adding custom CSS to your theme.

### Why am I seeing "This feature is currently unavailable"?
This message appears when the Steam API key is not configured. If you're an administrator, follow the link to set up your API key.

### How can I reduce API usage?
The plugin includes a caching system that stores API responses. You can adjust the cache duration in the plugin settings (Settings → Steam API).

## Changelog

### 1.1.0
- Added secure API key management system
- Implemented caching for API responses
- Replaced insecure XML loading with proper WordPress HTTP API
- Improved error handling and user feedback
- Added modern clipboard API support
- Enhanced responsive design
- Fixed security vulnerabilities
- Added comprehensive documentation

### 1.0.0
- Initial release

## Credits

Developed by develabr

## License

This plugin is licensed under the GPL v2 or later.

```
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.
```
