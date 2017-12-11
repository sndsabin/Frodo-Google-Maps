<?php
/**
 * Created by PhpStorm.
 * User: sndsabin
 * Date: 10/2/17
 * Time: 10:22 AM
 */

namespace FrodoGoogleMaps\includes;


class LocationMarkerTermMeta
{
    public function __construct()
    {
        add_action( 'init', array( $this, 'register_location_marker_taxonomy' ) );
        add_action( 'location-marker_add_form_fields', array( $this, 'add_location_marker_metadata' ) ); # hook : {$taxonomy}_add_form_fields}
        add_action( 'location-marker_edit_form_fields', array( $this, 'edit_location_marker_metadata' ) ); # hook : {$taxonomy}_edit_form_fields}
        add_action( 'create_location-marker', array( $this, 'save_location_marker_metadata' ) ); # hook : create_{$taxonomy}
        add_action( 'edit_location-marker', array( $this, 'save_location_marker_metadata' ) ); # hook : edit_{$taxonomy}


        add_filter( 'manage_edit-location-marker_columns', array( $this, 'remove_description_slug_from_sidebar' )); # hook : manage_edit-{$taxonomy}_columns
    }

    /**
     * Register Custom Taxonomy
     */
    public function register_location_marker_taxonomy()
    {
        $singular = esc_html__( 'Location Marker' );
        $plural   = esc_html__( 'Location Markers' );

        $labels = array(
            'name'                  => _x( $plural, 'text-domain' ),
            'singular_name'         => _x( $singular, 'text-domain' ),
            'search_items'          => _x( 'Search ' . $plural, 'text-domain' ),
            'all_items'             => _x( 'All ' . $plural, 'text-domain' ),
            'parent_item'           => _x( 'Parent ' . $singular, 'text-domain' ),
            'parent_item_colon'     => _x( 'Parent ' . $singular, 'text-domain' ),
            'edit_item'             => _x( 'Edit ' . $singular, 'text-domain' ),
            'update_item'           => _x( 'Update ' . $singular, 'text-domain' ),
            'add_new_item'          => _x( 'Add New ' . $singular, 'text-domain' ),
            'new_item_name'         => _x( 'New ' . $singular . 'Name', 'text-domain' ),
            'menu_name'             => _x( $plural, 'text-domain' ),
        );

        $args = array(
                'labels'             => $labels,
                'show_ui'            => true,
                'show_in_quick_edit' => false,
                'meta_box_cb'        => false,
            );

        register_taxonomy( 'location-marker', 'frodo_google_map', $args );
    }

    /**
     * Removes description and slug attributes from sidebar metabox
     * @param $columns
     * @return mixed
     */
    public function remove_description_slug_from_sidebar( $columns ) {
        if ( isset( $columns['description'] ) ) {
            unset( $columns['description'] );
        }

        if ( isset( $columns['slug'] ) ) {
            unset( $columns['slug'] );
        }


        return $columns;
    }

    /**
     * Renders Custom fields
     */
    public function add_location_marker_metadata()
    {
        wp_nonce_field( FRODO_GOOGLE_MAPS_PATH, 'frodo_google_maps_location_term_meta_nonce' );

        ?>
        <div class="clearfix">
            <div class="frodo-google-map-container">
                <div class="form-field">
                    <label for="location" class="row-title"><?php _e('Location', 'frodo-google-maps'); ?></label>
                    <div class="meta-td location" id="map-container">
                        <input name="location" id="location" type="text"
                               placeholder="Enter a location" required>
                    </div>
                </div>

                <div id="map" style="height: 100%;"></div>
                <div id="infowindow-content">
                    <img src="" width="16" height="16" id="place-icon">
                    <span id="place-name"  class="title"></span><br>
                    <span id="place-address"></span>
                </div>
            </div>
            <div class="description-container">
                <label for="marker_description" class="row-title"><?php _e('Description', 'frodo-google-maps'); ?></label>

                <?php
                    $content = '';
                    $editor_id = 'marker_description';
                    $settings =   array(
                        'media_buttons' => true,
                        'textarea_rows' => get_option('default_post_edit_rows', 10),
                        'tinymce' => false, // disable visual ( issue with visual )
                        'quicktags' => true

                    );
                    wp_editor( $content, $editor_id, $settings );
                ?>
            </div>

            <div class="form-field">
                <label for="marker_animation" class="row-title"><?php _e('Marker Animation', 'frodo-google-maps'); ?></label>

                <select name="marker_animation" id="marker-animation">
                    <option value=""><?php _e('None', 'frodo-google-maps')?></option>';
                    <option value="DROP"><?php _e('Drop', 'frodo-google-maps')?></option>';
                    <option value="BOUNCE"><?php _e('Bounce', 'frodo-google-maps')?></option>';
                </select>

            </div>

            <div class="form-field">
                <label for="is_info_window_open" class="row-title"><?php _e('Infowindow open by default', 'frodo-google-maps'); ?></label>

                <select name="is_info_window_open" id="is-info-window-open">
                    <option value="Yes"><?php _e('Yes', 'frodo-google-maps')?></option>';
                    <option value="No" selected><?php _e('No', 'frodo-google-maps')?></option>';
                </select>

            </div>

            <input id="latitude" name="latitude" type="hidden"/>
            <input id="longitude" name="longitude" type="hidden"/>
        </div>

        <div id="marker-container"></div>

        <?php

    }

    /**
     * Handles Save functionality of the form posted
     * @param $term_id
     */
    public function save_location_marker_metadata( $term_id ) {

        /**
         *  Check if nonce is set
         */

        if ( !isset( $_POST['frodo_google_maps_location_term_meta_nonce'] ) ) {
            return;
        }

        /**
         * Verify nonce
         */

        if ( !wp_verify_nonce( $_POST['frodo_google_maps_location_term_meta_nonce'], FRODO_GOOGLE_MAPS_PATH )  )  {
            return;
        }


        /**
         * Update Database
         */
        $this->update_meta( $term_id );
    }

    /**
     * Updates term meta of the specified term
     * @param $term_id
     */
    protected function update_meta ( $term_id ) {

        // Sanitize Post Data
        list( $location, $marker_description, $marker_animation, $is_info_window_open, $latitude, $longitude ) = $this->sanitizePostData();

        // Update Database
        if ( isset( $_POST['location'] ) ) {
            update_term_meta( $term_id, 'location', $location );
        }

        if ( isset( $_POST['marker_description'] ) ) {
            update_term_meta( $term_id, 'marker_description', $marker_description );
        }

        if ( isset( $_POST['marker_animation'] ) ) {
            update_term_meta( $term_id, 'marker_animation', $marker_animation );
        }

        if ( isset( $_POST['is_info_window_open'] ) ) {
            update_term_meta( $term_id, 'is_info_window_open', $is_info_window_open );
        }

        if ( isset( $_POST['latitude'] ) ) {
            update_term_meta( $term_id, 'latitude', $latitude );
        }

        if ( isset( $_POST['longitude'] ) ) {
            update_term_meta( $term_id, 'longitude', $longitude );
        }

    }

    /**
     * Sanitizes Posted Data via form
     * @return array
     */
    protected function sanitizePostData()
    {
        $location = sanitize_text_field( $_POST['location'] );
        $marker_description =  stripslashes( $_POST['marker_description'] );
        $marker_animation =  sanitize_text_field( $_POST['marker_animation'] );
        $is_info_window_open = sanitize_text_field( $_POST['is_info_window_open'] );
        $latitude = filter_var( $_POST['latitude'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION );
        $longitude = filter_var( $_POST['longitude'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION );

        return array($location, $marker_description, $marker_animation, $is_info_window_open, $latitude, $longitude);
    }

    /**
     * Renders Edit form
     * @param $term
     */
    public function edit_location_marker_metadata( $term ) {
        wp_nonce_field( FRODO_GOOGLE_MAPS_PATH, 'frodo_google_maps_location_term_meta_nonce' );

        $meta_data = array_map(function ($a) { return $a[0]; }, get_term_meta( $term->term_id ));

        /**
         *  Enqueue
         *  JS Script
         */
        wp_enqueue_script('frodo-google-map-js', FRODO_GOOGLE_MAPS_URL.'js/admin/google-map.js');
        wp_enqueue_script('frodo-google-maps-js', 'https://maps.googleapis.com/maps/api/js?key='. FRODO_GOOGLE_MAP_API_KEY .'&libraries=places&callback=initMap', array(), null, true);

        // Make user saved marker data available for use in javascript
        wp_localize_script( 'frodo-google-map-js', 'FRODO_GOOGLE_MAP_USER_SAVED_MARKER', array(
                'data' => json_encode($meta_data)
        ) );

        ?>
            <tr class="form-field">
                <th scope="row">
                    <label for="location" class="row-title"><?php _e('Location', 'frodo-google-maps'); ?></label>
                </th>

                <td class="location" id="map-container">
                    <input name="location" id="location" type="text"
                           placeholder="Enter a location" value="<?php echo isset( $meta_data['location'] ) ? $meta_data['location']: '' ?>" required>
                </td>
            </tr>
            <tr class="form-field">
                <td></td>
                <td id="map" style="height: 300px"></td>
                <td id="infowindow-content">
                        <img src="" width="16" height="16" id="place-icon">
                        <span id="place-name"  class="title"></span><br>
                        <span id="place-address"></span>
                </td>


            </tr>

            <tr>
                <th scope="row">
                    <label for="marker_description" class="row-title"><?php _e('Description', 'frodo-google-maps'); ?></label>
                </th>

                <td>
                    <?php
                    $content = isset( $meta_data['marker_description'] ) ? $meta_data['marker_description']: '' ;
                    $editor_id = 'marker_description';
                    $settings =   array(
                        'media_buttons' => true,
                        'textarea_rows' => get_option('default_post_edit_rows', 10),
                        'tinymce' => false, // disable visual ( issue with visual )
                        'quicktags' => true

                    );
                    wp_editor( $content, $editor_id, $settings );
                    ?>
                </td>

            </tr>
            <tr class="form-field">
                <th scope="row">
                    <label for="is_info_window_open" class="row-title"><?php _e('Marker Animation', 'frodo-google-maps'); ?></label>
                </th>

                <td class="select-option" id="marker-animation">
                    <select name="marker_animation"">
                        <option value=""><?php _e('None', 'frodo-google-maps')?></option>';
                        <option value="DROP" <?php if ( !empty( $meta_data['marker_animation'] ) )selected( $meta_data['marker_animation'], 'DROP' ); ?>><?php _e('Drop', 'frodo-google-maps')?></option>';
                        <option value="BOUNCE" <?php if ( !empty( $meta_data['marker_animation'] ) )selected( $meta_data['marker_animation'], 'BOUNCE' ); ?>><?php _e('Bounce', 'frodo-google-maps')?></option>';
                    </select>
                </td>
            </tr>
            <tr class="form-field">
                <th scope="row">
                    <label for="is_info_window_open" class="row-title"><?php _e('Infowindow open by default', 'frodo-google-maps'); ?></label>
                </th>

                <td class="select-option" id="is-info-window-open">
                    <select name="is_info_window_open">
                        <option value="Yes" <?php if ( !empty( $meta_data['is_info_window_open'] ) )selected( $meta_data['is_info_window_open'], 'Yes' ); ?>><?php _e('Yes', 'frodo-google-maps')?></option>';
                        <option value="No" <?php if ( !empty( $meta_data['is_info_window_open'] ) )selected( $meta_data['is_info_window_open'], 'No' ); ?>><?php _e('No', 'frodo-google-maps')?></option>';
                    </select>
                </td>
            </tr>



        <input id="latitude" name="latitude" type="hidden" value="<?php echo isset( $meta_data['latitude'] ) ? $meta_data['latitude']: '' ?>"/>
        <input id="longitude" name="longitude" type="hidden" value="<?php echo isset( $meta_data['longitude'] ) ? $meta_data['longitude']: '' ?>"/>


        <div id="marker-container"></div>
        <?php


    }
}

$locationMarkerTermMeta = new LocationMarkerTermMeta();