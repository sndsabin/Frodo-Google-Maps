<?php
/**
 * Created by PhpStorm.
 * User: sndsabin
 * Date: 9/11/17
 * Time: 10:03 AM
 */
namespace FrodoGoogleMaps\includes;


class GoogleMapPostType
{
    protected $labels;
    protected $args;
    protected $slug = 'frodo_google_map';

    public function __construct() {
        add_action('init',  array($this, 'register_post_type'));
    }

    /*
     * Registers Custom Post Type for google_map
     * @uses register_post_type
     */
    public function register_post_type() {
        $singular = esc_html__('Google Map');
        $plural = esc_html__('Google Maps');

        $this->labels = array(
            'name' 					=> $plural,
            'singular_name' 		=> $singular,
            'add_new' 				=> esc_html__( 'Add New', 'frodo-google-maps' ),
            'add_new_item' 			=> sprintf( esc_html__( 'Add New %s', 'frodo-google-maps' ), $singular ),
            'edit'		        	=> esc_html__( 'Edit', 'frodo-google-maps' ),
            'edit_item'	        	=> sprintf( esc_html__( 'Edit %s', 'frodo-google-maps' ), $singular ),
            'new_item'	        	=> sprintf( esc_html__( 'New %s', 'frodo-google-maps' ), $singular ),
            'view' 					=> sprintf( esc_html__( 'View %s', 'frodo-google-maps' ), $singular ),
            'view_item' 			=> sprintf( esc_html__( 'View %s', 'frodo-google-maps' ), $singular ),
            'search_term'   		=> sprintf( esc_html__( 'Search %s', 'frodo-google-maps' ), $plural ),
            'parent' 				=> sprintf( esc_html__( 'Parent %s', 'frodo-google-maps' ), $singular ),
            'not_found' 			=> sprintf( esc_html__( 'No %s found', 'frodo-google-maps' ), $plural ),
            'not_found_in_trash' 	=> sprintf( esc_html__( 'No %s in Trash', 'frodo-google-maps' ), $plural )
        );

        // For Side bar Section
        $this->args = array(
            'labels'                => $this->labels,
            'public'                => true,
            'publicly_queryable'    => false,
            'exclude_from_search'   => false,
            'show_in_nav_menus'     => true,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'show_in_admin_bar'     => true,
            'menu_position'         => 8,
            'menu_icon'             => 'dashicons-location-alt',
            'can_export'            => true,
            'delete_with_user'      => false,
            'hierarchical'          => false,
            'has_archive'           => true,
            'query_var'             => true,
            'capability_type'       => 'post',
            'map_meta_cap'          => true,
            'rewrite'               => array(
                'slug' => $this->slug,
                'with_front' => true,
                'pages' => true,
                'feeds' => false,
            ),
            // Allows which default input fields type show up
            'supports' => array(
                'title'
            )


        );


        register_post_type($this->slug, $this->args);

    }
}

$frodoGoogleMap = new GoogleMapPostType();