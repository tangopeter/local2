<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              tangopeter.co.nz
 * @since             1.0.0
 * @package           Fwg_Custom_Functions
 *
 * @wordpress-plugin
 * Plugin Name:       Fast Web Guru - Functions
 * Plugin URI:        tangopeter.co.nz
 * Description:       This plugin contains Fast Web guru's custom functions.
 * Version:           1.0.1
 * Author:            Peter Williamson - peter@t1.co.nz
 * Author URI:        tangopeter.co.nz
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       fwg-custom-functions
 * Domain Path:       /languages
 */

/*
    11/3/2019 v1.0.1
    * Add fix to css for admin pages headings colour (was white on white!)

    11/2/2019 v1.0 
    * created installable version from WordPress Plugin Boilerplate 
    * force admin users to use Ectoplasm admin theme
    * added  * Debug Pending Updates * Displays hidden plugin and theme updates on update-core screen.

*/

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
  die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'PLUGIN_NAME_VERSION', '1.0.1' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-fwg-custom-functions-activator.php
 */
function activate_fwg_custom_functions() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-fwg-custom-functions-activator.php';
	Fwg_Custom_Functions_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-fwg-custom-functions-deactivator.php
 */
function deactivate_fwg_custom_functions() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-fwg-custom-functions-deactivator.php';
	Fwg_Custom_Functions_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_fwg_custom_functions' );
register_deactivation_hook( __FILE__, 'deactivate_fwg_custom_functions' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-fwg-custom-functions.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_fwg_custom_functions() {

	$plugin = new Fwg_Custom_Functions();
	$plugin->run();

}
run_fwg_custom_functions();
