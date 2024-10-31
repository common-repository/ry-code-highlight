<?php
/*
Plugin Name: RY Code Highlight
Plugin URI: https://richer.tw/ry-code-highlight
Description: Highlighted code.
Version: 1.0.2
Author: Richer Yang
Author URI: https://richer.tw/
Text Domain: ry-code-highlight
Domain Path: /languages
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

function_exists('plugin_dir_url') OR exit('No direct script access allowed');

define('RY_CHL_VERSION', '1.0.2');

define('RY_CHL_PLUGIN_URL', plugin_dir_url(__FILE__));
define('RY_CHL_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('RY_CHL_PLUGIN_BASENAME', plugin_basename(__FILE__));

require_once(RY_CHL_PLUGIN_DIR . 'class.main.php');

register_activation_hook(__FILE__, array('RY_CHL', 'plugin_activation'));
register_deactivation_hook(__FILE__, array('RY_CHL', 'plugin_deactivation'));

add_action('init', array('RY_CHL', 'init'));
