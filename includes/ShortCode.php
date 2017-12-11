<?php
/**
 * Created by PhpStorm.
 * User: sndsabin
 * Date: 9/14/17
 * Time: 2:48 PM
 */

namespace FrodoGoogleMaps\includes;

class ShortCode
{
    protected $markers_data = array();
    protected $post_data;

    public function __construct() {
        add_shortcode( 'frodo_google_map', array( $this, 'plot_map' ) );
    }

    public function plot_map( $atts, $content =  null ) {
        if ( !isset( $atts['plot_id'] ) ) {
            return '<div class="alert alert-warning" role="alert">
                        <strong>Warning!</strong> You must provide a plot_id for this shortcode to work.
                    </div>';
        }

        $this->post_data = array_map( function ( $a ) { return $a[0]; }, get_post_meta( $atts['plot_id'] ) );

        // check if frodo-google-map post exists
        if ( count(  get_post_meta( $atts['plot_id']) ) > 0 ) {
            foreach ( get_post_meta( $atts['plot_id'], 'marker_id' ) as $key => $value ) {
                $marker_data = array_map( function ( $a ) { return $a[0]; }, get_term_meta( $value ) );

                // Check if marker data exist
                if ( count($marker_data) > 0) {
                    array_push( $this->markers_data, $marker_data );
                }
            }

        } else {
            return '<div class="alert alert-warning" role="alert">
                        <strong>404 Error. Oops! Map Not Found</strong> The Map for specified id do not exists.
                    </div>';
        }



        /**
         * CSS Files
         */
        wp_enqueue_style('frodo-frontend-css', FRODO_GOOGLE_MAPS_URL.'css/main.css');
        /**
         * JS Files
         */
        wp_enqueue_script('frodo-load-map-with-data-js', FRODO_GOOGLE_MAPS_URL.'js/frodo-load-map-with-data.js', array(), null, false );
        wp_enqueue_script('frodo-google-maps-js', 'https://maps.googleapis.com/maps/api/js?key='. FRODO_GOOGLE_MAP_API_KEY . '&callback=initMap', array(), null, true);

        // make data available for use in javascript
        wp_localize_script('frodo-load-map-with-data-js', 'FRODO_GOOGLE_MAP_MARKERS_DATA', array(
            'data' => json_encode( $this->markers_data )
        ));
        wp_localize_script('frodo-load-map-with-data-js', 'FRODO_GOOGLE_MAP_POST_DATA', array(
            'data' => json_encode( $this->post_data )
        ));
        return '<div id="map"></div>';

    }



}

$short_code = new ShortCode();