<?php
// templates/enhanced-form-template.php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
?>
<div class="steam-api-wrapper">
    <div class="steam-api-info">
        <form class="form" id="steam-form" autocomplete="off">
            <div class="input-group">
                <input type="text" id="steamInput" class="form-input" 
                    title="<?php echo esc_attr__('For example:
Heavenanvil
76561198036370701
STEAM_0:1:38052486
steamcommunity.com/id/heavenanvil
steamcommunity.com/profiles/76561198036370701', 'steam-api-plugin'); ?>" 
                    placeholder="<?php echo esc_attr__('Enter SteamID / SteamCommunityID / Profile Name / Profile URL', 'steam-api-plugin'); ?>" 
                    style="border-top-left-radius: .25rem;border-bottom-left-radius: .25rem;">
                <button type="button" id="get-stats-button"><?php echo esc_html__('Find', 'steam-api-plugin'); ?></button>
            </div>
            <p class="form-description"><?php echo esc_html__('Find and get your Steam ID, Steam ID 64, customURL and community ID, along with extended profile information', 'steam-api-plugin'); ?></p>
        </form>
    </div>
    
    <!-- Loading indicator -->
    <div id="loading-indicator" class="loading-indicator hidden">
        <div class="loader"></div>
    </div>
    
    <div id="user-info" class="user-info-block"></div>
    <div id="results"></div>
</div>