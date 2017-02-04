<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * Dashboard. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://example.com
 * @since             1.0.0
 * @package           Content_Otter
 *
 * @wordpress-plugin
 * Plugin Name:       Content Otter
 * Plugin URI:        http://graemefulton.com/
 * Description:       Import and format content from external web pages
 * Version:           1.0.0
 * Author:            Graeme Fulton
 * Author URI:        http://graemefulton.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       plugin-name
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
*  only load in admin dashboard
 */
require plugin_dir_path(__FILE__) . 'admin/class-content-otter-admin.php';
//load the plugin when activated - only load admin side of plugin
add_action( 'plugins_loaded', array( 'Content_Otter_Admin', 'get_instance' ) );
