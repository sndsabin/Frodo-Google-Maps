<?php
/**
 * Created by PhpStorm.
 * User: sndsabin
 * Date: 9/11/17
 * Time: 11:00 AM
 */

namespace FrodoGoogleMaps\includes;

class EnqueueAssets
{
    protected $pagenow;
    protected $typenow;

    public function __construct() {
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
    }

    public function enqueue_assets() {
        global $pagenow, $typenow;

        $this->typenow = $typenow;
        $this->pagenow = $pagenow;

        if ( $this->pagenow == 'post-new.php' &&
            $this->typenow == 'frodo_google_map' ) {
            wp_enqueue_script( 'frodo-toggle-change-default-js', FRODO_GOOGLE_MAPS_URL.'js/admin/toggle-change-default.js', array( 'jquery' ), null, true );
        }

        if (
            (
                $this->pagenow == 'post.php' ||
                $this->pagenow == 'post-new.php' ||
                $this->typenow == 'edit.php'
            ) &&
            $this->typenow == 'frodo_google_map'
        ) {
            /**
             * CSS Files
             */
            wp_enqueue_style( 'frodo-bootstrap-css', FRODO_GOOGLE_MAPS_URL.'css/admin/bootstrap.min.css' );
            wp_enqueue_style( 'frodo-bootstrap-toggle-css', FRODO_GOOGLE_MAPS_URL.'css/admin/bootstrap-toggle.min.css' );

            /**
             * JS Files
             */
            wp_enqueue_script( 'frodo-clipboard-js', FRODO_GOOGLE_MAPS_URL.'js/admin/clipboard.min.js', array(), null, true );
            wp_enqueue_script( 'frodo-clipboard-click-action-js', FRODO_GOOGLE_MAPS_URL.'js/admin/clipboard-click-action.js', array(), null, true );
            wp_enqueue_script( 'frodo-bootstrap-toggle-js', FRODO_GOOGLE_MAPS_URL.'js/admin/bootstrap-toggle.min.js', array(), null, true );

        }

        // Load Scripts only on custom taxonomy
        if (
            ($this->pagenow == 'edit-tags.php' || $this->pagenow == 'term.php') &&
            $this->typenow == 'frodo_google_map'
        ) {
            /**
             *  CSS Files
             */
            wp_enqueue_style( 'frodo-google-map-css', FRODO_GOOGLE_MAPS_URL.'css/admin/marker-term-meta.css' );

            /**
             * JS Files
             */

            // Exclude in edit page
            if ( $this->pagenow != 'term.php' ) {
                wp_enqueue_script( 'frodo-google-map-js', FRODO_GOOGLE_MAPS_URL.'js/admin/google-map.js' );
                wp_enqueue_script( 'frodo-google-maps-js', 'https://maps.googleapis.com/maps/api/js?key='. FRODO_GOOGLE_MAP_API_KEY .'&libraries=places&callback=initMap', array(), null, true );
            }

            wp_enqueue_script( 'frodo-modify-term-meta-js', FRODO_GOOGLE_MAPS_URL.'js/admin/term-meta.js', array(), null, false );
        }

        wp_enqueue_script( 'frodo-setting-js', FRODO_GOOGLE_MAPS_URL.'js/admin/setting.js', array(), null, true );

        // make nonce available for use in javascript
        wp_localize_script( 'frodo-setting-js', 'FRODO_GOOGLE_MAP', array(
            'security' => wp_create_nonce('frodo_google_map_save_api'),
            'success' => __('API Key has been saved'),
            'error' => __('There was error saving the API Key, or you do not have proper permission')
        ) );
    }
}

$scripts = new EnqueueAssets();