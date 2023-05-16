<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       tangopeter.co.nz
 * @since      1.0.0
 *
 * @package    Fwg_Custom_Functions
 * @subpackage Fwg_Custom_Functions/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Fwg_Custom_Functions
 * @subpackage Fwg_Custom_Functions/includes
 * @author     Peter Williamson <peter@t1.co.nz>
 */
class Fwg_Custom_Functions_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'fwg-custom-functions',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
