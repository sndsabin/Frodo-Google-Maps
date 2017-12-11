<?php
/*
Plugin Name: Frodo Google Maps
Plugin URI: http://www.sabinb.com.np
Description: A Simple Plugin that helps to CRUD Google Maps
Version:     2.1.1
Author: sndsabin
Author URI: http://www.sabinb.com.np
License:     GPL2

Frodo Google Maps is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.

Frodo Google Maps is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Frodo Google Maps. If not, see https://www.gnu.org/licenses/old-licenses/gpl-2.0.en.html.
*/

namespace FrodoGoogleMaps;

// Global Constants
define('FRODO_GOOGLE_MAPS_VERSION', '1.0.0');
define('FRODO_GOOGLE_MAPS_URL', plugin_dir_url(__FILE__));
define('FRODO_GOOGLE_MAPS_PATH', dirname(__FILE__) . '/');
define('FRODO_GOOGLE_MAP_API_KEY', get_option( 'GOOGLE_MAP_API_KEY' ) );

// Exit if directly accessed
if (!defined('ABSPATH')) {
    exit();
}

/**
 * Include the other items
 */
require_once(FRODO_GOOGLE_MAPS_PATH . 'includes/GoogleMapPostType.php');
require_once(FRODO_GOOGLE_MAPS_PATH . 'includes/MetaBox.php');
require_once(FRODO_GOOGLE_MAPS_PATH . 'includes/EnqueueAssets.php');
require_once(FRODO_GOOGLE_MAPS_PATH . 'includes/ShortCode.php');
require_once(FRODO_GOOGLE_MAPS_PATH . 'includes/LocationMarkerTermMeta.php');
require_once(FRODO_GOOGLE_MAPS_PATH . 'includes/Setting.php');



