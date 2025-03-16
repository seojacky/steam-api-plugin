<?php 

// Add plugin settings page
add_action('admin_menu', 'steam_api_add_admin_menu');
function steam_api_add_admin_menu() {
    add_options_page(
        __('Steam API Settings', STEAM_API_TEXT_DOMAIN),
        __('Steam API', STEAM_API_TEXT_DOMAIN),
        'manage_options',
        'steam_api_settings',
        'steam_api_settings_page'
    );
}

// Register settings
add_action('admin_init', 'steam_api_register_settings');
function steam_api_register_settings() {
    register_setting('steam_api_settings_group', 'steam_api_settings', 'steam_api_sanitize_settings');
}

// Sanitize settings
function steam_api_sanitize_settings($input) {
    $sanitized_input = array();
    
    if (isset($input['api_key'])) {
        $sanitized_input['api_key'] = sanitize_text_field($input['api_key']);
        
        // Validate API key format
        if (!empty($sanitized_input['api_key'])) {
            if (strlen($sanitized_input['api_key']) < 32) {
                add_settings_error(
                    'steam_api_settings',
                    'invalid_api_key',
                    __('The Steam API key appears to be invalid. Please check it and try again.', STEAM_API_TEXT_DOMAIN),
                    'error'
                );
            }
        }
    }
    
    if (isset($input['cache_duration'])) {
        $sanitized_input['cache_duration'] = absint($input['cache_duration']);
    }
    
    return $sanitized_input;
}

// Settings page
function steam_api_settings_page() {
    $settings = get_option('steam_api_settings');
    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        <form method="post" action="options.php">
            <?php settings_fields('steam_api_settings_group'); ?>
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="steam_api_settings[api_key]"><?php _e('Steam API Key', STEAM_API_TEXT_DOMAIN); ?></label>
                    </th>
                    <td>
                        <input type="text" 
                               id="steam_api_settings[api_key]" 
                               name="steam_api_settings[api_key]" 
                               value="<?php echo esc_attr($settings['api_key']); ?>" 
                               class="regular-text" />
                        <p class="description">
                            <?php _e('Enter your Steam API key. You can get one from', STEAM_API_TEXT_DOMAIN); ?> 
                            <a href="https://steamcommunity.com/dev/apikey" target="_blank">
                                <?php _e('Steam Web API Key Registration', STEAM_API_TEXT_DOMAIN); ?>
                            </a>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="steam_api_settings[cache_duration]"><?php _e('Cache Duration (seconds)', STEAM_API_TEXT_DOMAIN); ?></label>
                    </th>
                    <td>
                        <input type="number" 
                               id="steam_api_settings[cache_duration]" 
                               name="steam_api_settings[cache_duration]" 
                               value="<?php echo esc_attr($settings['cache_duration']); ?>" 
                               class="regular-text" />
                        <p class="description">
                            <?php _e('Duration to cache API results in seconds. Default is 3600 (1 hour).', STEAM_API_TEXT_DOMAIN); ?>
                        </p>
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}