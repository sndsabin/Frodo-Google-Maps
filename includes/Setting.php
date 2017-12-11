<?php
/**
 * Created by PhpStorm.
 * User: sndsabin
 * Date: 10/4/17
 * Time: 12:18 PM
 */

namespace FrodoGoogleMaps\includes;


class Setting
{
    public function __construct()
    {
        add_action( 'admin_menu', array( $this, 'add_submenu_setting_page' ) );
        add_action( 'wp_ajax_save_google_map_api_key', array( $this, 'save_api_key' ) );
    }

    /**
     * Renders Setting page
     */
    public function add_submenu_setting_page()
    {

        add_submenu_page(
            'edit.php?post_type=frodo_google_map',
            __('Setting'),
            __('Setting'),
            'manage_options',
            'frodo_google_map_setting',
            array( $this, 'setting_callback' )
        );
    }

    public function setting_callback()
    {
        // Register a new option
        add_option( 'GOOGLE_MAP_API_KEY', '', '', 'yes' );

    ?>

        <div class="wrap">
            <h1>General Settings</h1>
            <table class="form-table">
                <tbody><tr>
                    <th scope="row"><label for="blogname">Google Map API Key</label></th>
                    <td><input name="apiKey" class="regular-text" placeholder="YOUR GOOGLE MAP API KEY" type="text" id="apiKey" value="<?php echo get_option( 'GOOGLE_MAP_API_KEY' ); ?>"></td>
                </tr>
                </tbody>
            </table>
            <p class="submit"><input type="submit" name="submit" id="submit-google-api-key" class="button button-primary" value="Save Changes"></p>
        </div>
    <?php

    }

    /**
     * Handles AJAX Save functionality
     */
    public function save_api_key()
    {
        // Check Nonce for ajax request
        if ( !check_ajax_referer( 'frodo_google_map_save_api', 'security' ) ) {
            return wp_send_json_error( 'Invalid Nonce' );
        }

        // Check for user permission
        if ( !current_user_can( 'manage_options' ) ) {
            return wp_send_json_error( 'You are not allowed to do this' );
        }

        // Sanitize Data Posted
        $apiKey = sanitize_text_field( $_POST['apiKey'] );

        // Save to option GOOGLE_MAP_API_KEY
        update_option( 'GOOGLE_MAP_API_KEY', $apiKey );

        // Send success message
        wp_send_json_success( 'API Key was saved' );

    }
}

$settingPage = new Setting();