<?php
/**
 * Created by PhpStorm.
 * User: sndsabin
 * Date: 9/11/17
 * Time: 10:03 AM
 */

namespace FrodoGoogleMaps\includes;


class MetaBox
{

    public function __construct()
    {
        add_action('add_meta_boxes', array($this, 'add_custom_metabox'));
        add_action('save_post', array($this, 'save_metabox_data'));
    }

    /**
     * Renders Metabox
     */
    public function add_custom_metabox()
    {
        add_meta_box(
            'frodo_google_map',
            __('Map'),
            array($this, 'render_meta_fields'),
            'frodo_google_map',
            'normal',
            'core'
        );
    }

    /**
     * Renders Meta Fields
     */
    public function render_meta_fields()
    {

        wp_nonce_field( FRODO_GOOGLE_MAPS_PATH, 'frodo_google_maps_nonce' );

        $meta_data = get_post_meta( get_the_ID() );

        // Retrieve all saved markers
        $saved_markers = get_terms( [
            'taxonomy' => 'location-marker',
            'hide_empty' => false,
        ] );

        // Display if GOOGLE_MAP_API_KEY is not SET
        if ( empty( get_option( 'GOOGLE_MAP_API_KEY' ) ) ) : ?>
            <div id="message" class="error">
                <p><?php echo _e('Google Map API Not Set', 'frodo-google-maps'); ?>. Add <a
                            href="<?php echo get_admin_url(); ?>/edit.php?post_type=frodo_google_map&page=frodo_google_map_setting">API
                        KEY</a></p>
            </div>
        <?php endif; ?>

        <div class="form-group">
            <label for="shortcode" class="row-title">Shortcode</label>
            <div class="form-inline">
                <input id="shortcode" class="form-control" type="text"
                       value="<?php echo '[frodo_google_map plot_id=' . get_the_ID() . ']' ?>" readonly>
                <span class="input-group-button">
                        <button id="copy-to-clipboard" class="btn" type="button" data-clipboard-demo=""
                                data-clipboard-target="#shortcode" style="">
                            <img class="clippy" src="<?php echo FRODO_GOOGLE_MAPS_URL ?>img/clippy.svg" width="13"
                                 alt="Copy to clipboard">
                        </button>
                        copy this into your post or page to display the map
                    </span>

            </div>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="width" class="row-title"><?php _e( 'Width', 'frodo-google-maps' ); ?></label>
                <div class="form-inline">
                    <input name="width" id="width" type="number"
                           placeholder="100" class="form-control"
                           value="<?php echo ( !empty( $meta_data['width'][0] ) ) ? $meta_data['width'][0] : '' ?>">

                    <label for="width_metric" class="row-title">&nbsp;</label>
                    <select name="width_metric" id="width-metric">
                        <option value="%" <?php echo ( isset( $meta_data['width_metric'][0] ) && $meta_data['width_metric'][0] == '%' ) ? 'selected' : '' ?>>
                            %
                        </option>
                        <option value="px" <?php echo ( isset( $meta_data['width_metric'][0] ) && $meta_data['width_metric'][0] == 'px' ) ? 'selected' : '' ?>>
                            px
                        </option>
                    </select>
                </div>

            </div>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="height" class="row-title"><?php _e( 'Height', 'frodo-google-maps' ); ?></label>
                <div class="form-inline">
                    <input name="height" id="height" type="number"
                           placeholder="500" class="form-control"
                           value="<?php echo ( !empty( $meta_data['height'][0] ) ) ? $meta_data['height'][0] : '' ?>">

                    <label for="height_metric" class="row-title">&nbsp;</label>
                    <select name="height_metric" id="width-metric">
                        <option value="px" selected>px</option>
                    </select>
                </div>

            </div>

        </div>
        <div class="row">
            <div class="col-md-5">
                <label for="zoom-level" class="row-title"><?php _e( 'Zoom Level', 'frodo-google-maps' ); ?></label>
                <input name="zoom_level" id="zoom-level" type="number"
                       placeholder="7" class="form-control"
                       value="<?php echo (!empty( $meta_data['zoom_level'][0] ) ) ? $meta_data['zoom_level'][0] : '' ?>">
            </div>
            <div class="col-md-3">
                <label for="draggable" class="row-title"><?php _e( 'Draggable', 'frodo-google-maps' ); ?></label>
                <br>
                <input name="draggable" class="form-control" id="draggable-toggle"
                       type="checkbox" <?php echo ( $meta_data['draggable'][0]  ) ? 'checked' : '' ?>
                       data-toggle="toggle" data-size="small">
            </div>
            <div class="col-md-3">
                <label for="scroll" class="row-title"><?php _e( 'Scroll', 'frodo-google-maps' ); ?></label>
                <br>
                <input name="scroll" class="form-control" id="scroll-toggle"
                       type="checkbox" <?php echo ( $meta_data['scroll'][0] ) ? 'checked' : '' ?>
                       data-toggle="toggle" data-size="small">
            </div>
        </div>

        <div class="form-group">
            <label for="map-type" class="row-title"><?php _e('Map Type', 'frodo-google-maps'); ?></label>
            <select class="form-control" name="map_type">

                <option value="roadmap" <?php echo (isset($meta_data['map_type'][0]) && $meta_data['map_type'][0] == 'roadmap') ? 'selected' : '' ?>><?php _e('Roadmap', 'frodo-google-maps'); ?></option>
                <option value="satellite" <?php echo (isset($meta_data['map_type'][0]) && $meta_data['map_type'][0] == 'satellite') ? 'selected' : '' ?>><?php _e('Satellite', 'frodo-google-maps'); ?></option>
                <option value="hybrid" <?php echo (isset($meta_data['map_type'][0]) && $meta_data['map_type'][0] == 'hybrid') ? 'selected' : '' ?>><?php _e('Hybrid', 'frodo-google-maps'); ?></option>
                <option value="terrain" <?php echo (isset($meta_data['map_type'][0]) && $meta_data['map_type'][0] == 'terrain') ? 'selected' : '' ?>><?php _e('Terrain', 'frodo-google-maps'); ?></option>
            </select>

        </div>


        <div class="form-check">
            <label for="map-type" class="row-title"><?php _e('Markers', 'frodo-google-maps'); ?></label>
            <br>
            <?php

            if ( count($saved_markers) == 0 ) { ?>
                <p>
                    No Markers. Please <a
                            href="<?php echo get_admin_url(); ?>edit-tags.php?taxonomy=location-marker&post_type=frodo_google_map">Add
                        Marker</a>.
                </p>

                <?php
            }

            foreach ( $saved_markers as $key => $value ):
                if ( isset( $meta_data['marker_id'] ) ) {
                    if ( in_array( $value->term_id, $meta_data['marker_id'] ) ) {
                        $checked = 'checked';
                    } else {
                        $checked = '';
                    }
                }


                ?>
                <label class="custom-control custom-checkbox">
                    <input name="markers[]" type="checkbox" class="custom-control-input"
                           value="<?php echo ( !empty( $value->term_id ) ) ? $value->term_id : ''; ?>" <?php echo ( isset( $checked ) ) ? $checked : ''; ?>>
                    <span class="custom-control-indicator"></span>
                    <span class="custom-control-description"><?php echo $value->name; ?></span>
                </label>
            <?php endforeach; ?>

        </div>


        <?php

    }

    /**
     * Handles Save/update Functionality
     * @param $post_id
     */
    public function save_metabox_data( $post_id )
    {

        // Check save status
        $is_autosave = wp_is_post_autosave( $post_id );
        $is_revision = wp_is_post_revision( $post_id );
        $is_valid_none = (
            isset( $_POST['frodo_google_maps_nonce'] ) &&
            wp_verify_nonce( $_POST['frodo_google_maps_nonce'], FRODO_GOOGLE_MAPS_PATH ) )
            ? 'true'
            : 'false';

        // Exits depending upon save status
        if ( $is_autosave || $is_revision || !$is_valid_none ) {
            return;
        }

        /**
         * Updating database
         */
        $this->update_meta( $post_id );

    }


    /**
     * Updates the post meta of specified post_id
     * @param $post_id
     */
    protected function update_meta( $post_id )
    {
        /**
         * Updates Post Meta
         */
        if ( isset( $_POST['width'] ) ) {
            update_post_meta($post_id, 'width', $this->sanitizePostData( $_POST['width'] ) );
        }

        if ( isset( $_POST['width_metric'] ) ) {
            update_post_meta($post_id, 'width_metric', $this->sanitizePostData( $_POST['width_metric'] ) );
        }

        if ( isset( $_POST['height'] ) ) {
            update_post_meta($post_id, 'height', $this->sanitizePostData( $_POST['height'] ) );
        }

        if ( isset($_POST['height_metric'] ) ) {
            update_post_meta($post_id, 'height_metric', $this->sanitizePostData( $_POST['height_metric'] ) );
        }

        if ( isset( $_POST['zoom_level'] ) ) {
            update_post_meta( $post_id, 'zoom_level', $this->sanitizePostData( $_POST['zoom_level'] ) );
        }

        if ( isset( $_POST['draggable'] ) ) {
            update_post_meta( $post_id, 'draggable', 1 );
        } else {
            update_post_meta( $post_id, 'draggable', 0 );
        }

        if ( isset( $_POST['scroll'] ) ) {
            update_post_meta( $post_id, 'scroll', 1 );
        } else {
            update_post_meta( $post_id, 'scroll', 0 );
        }

        if ( isset( $_POST['map_type'] ) ) {
            update_post_meta( $post_id, 'map_type', $this->sanitizePostData( $_POST['map_type'] ) );
        }

        // Save Selected Marker to database
        $this->add_new_markers_meta( $post_id );

    }

    /**
     * Add New Post Meta attributed named marker_id
     * for all selected markers
     * @param $postId
     */
    protected function add_new_markers_meta( $postId )
    {
        if ( isset( $_POST['_wp_http_referer'] ) && strpos( $_POST['_wp_http_referer'], '&') !== false ) {
            // Check if the request is from edit page
            if ( explode('=', explode('&', $_POST['_wp_http_referer'] )[1] )[1] !== 'edit' ) {
                if ( isset( $_POST['markers'] ) ) {
                    foreach ( $_POST['markers'] as $key => $value ) {
                        add_post_meta($postId, 'marker_id', $value);
                    }
                }
            } else {
                delete_post_meta( $postId, 'marker_id' );

                // Add Updated post_meta

                if ( isset( $_POST['markers'] ) ) {
                    foreach ( $_POST['markers'] as $key => $value ) {
                        add_post_meta( $postId, 'marker_id', $value );
                    }
                }
            }
        }
        

    }

    /**
     * Sanitizes Data Posted via Form
     * @return array
     */
    protected function sanitizePostData($data)
    {
        if (!isset($data)) {
            return false;
        }

        return sanitize_text_field($data);
    }


}

$metabox = new MetaBox();