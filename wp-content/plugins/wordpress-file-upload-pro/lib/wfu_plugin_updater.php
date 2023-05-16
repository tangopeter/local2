<?php

/**
 * Plugin Updater Class
 *
 * This file contains the class definition of the auto-updater of the
 * Professional version. The auto-updater delivers the updates through the two
 * Iptanus Services endpoints:
 *
 *      1. https://services2.iptanus.com
 *      2. https://iptanusservices.appspot.com
 *
 * @link /lib/wfu_plugin_updater.php
 *
 * @package WordPress File Upload Plugin
 * @subpackage Core Components
 * @since 4.14.0
 */


/**
 * Plugin Auto-Updater Class.
 *
 * This class implements auto-updates of the plugin Professional version. It is
 * noted that this class is independent of the plugin.
 *
 * @since 4.14.0
 */
class wfu_plugin_auto_updater {

	/**
	 * @since 4.14.0
	 * @var string $update_path The service URL that provides information about
	 *      the updates.
	 */
	public $update_path;

	/**
	 * @since 4.14.0
	 * @var string $plugin_slug The full plugin slug in the form:
	 *      plugin_path/plugin_main_file.php
	 */
	public $plugin_slug;

	/**
	 * @since 4.14.0
	 * @var string $slug The slug that serves as the unique ID of the plugin
	 */
	public $slug;

	/**
	 * @since 4.14.0
	 * @var array $request_params {
	 *      Required parameters passed to the service URL depending on the type
	 *      of request.
	 *
	 *      @type array $info Contains required parameters for getting
	 *            information about the plugin.
	 *      @type array $update Contains required parameters for getting
	 *            information about the latest version of the plugin.
	 * }
	 */
	public $request_params;

	/**
	 * @since 4.14.0
	 * @var array $fallback_info An array of default information of the plugin
	 *      that will be shown in case that the service URL cannot respond.
	 */
	public $fallback_info;

	/**
	 * @since 4.14.0
	 * @var string $transient_prefix A prefix to use for storing transients.
	 */
	public $transient_prefix;

	/**
	 * Class Constructor.
	 *
	 * It initializes the class properties and assigns the update hooks.
	 *
	 * @since 4.14.0
	 *
	 * @param string $update_path The service URL that provides information
	 *        about the updates.
	 * @param string $plugin_slug The full plugin slug in the form:
	 *        plugin_path/plugin_main_file.php
	 * @param array $request_params Required parameters passed to the service
	 *        URL, see property $request_params for details.
	 * @param string $transient_prefix A prefix to use for storing transients.
	 * @param array $fallback_info An array of default information of the plugin
	 *        that will be shown in case that the service URL cannot respond.
	 * @param bool $always_show_plugin_details Optional. Defines whether the
	 *        'View Details' link in Plugins page will always be active for the
	 *        plugin.
	 */
	function __construct($update_path, $plugin_slug, $request_params, $fallback_info, $transient_prefix, $always_show_plugin_details = true) {
		$this->update_path = $update_path;
		$this->plugin_slug = $plugin_slug;
		list ($t1, $t2) = explode('/', $plugin_slug);
		$this->slug = $t1;
		$this->request_params = $request_params;
		$this->fallback_info = $fallback_info;
		$this->transient_prefix = $transient_prefix;
		add_filter('pre_set_site_transient_update_plugins', array(&$this, 'check_update'));
		add_filter('plugins_api', array(&$this, 'check_info'), 10, 3);
		if ( $always_show_plugin_details )
			add_filter('all_plugins', array(&$this, 'check_slug'), 10, 1);
	}

	/**
	 * Force the Plugin Slug.
	 *
	 * This function hooks on 'all_plugins' Wordpress filter and enforces the
	 * plugin to obtain a slug. This way 'View Details' link in Plugins page
	 * will always be active for the plugin, even when no new updates are
	 * available.
	 *
	 * @since 4.14.0
	 *
	 * @param array $plugins An array of all installed plugins.
	 *
	 * @return array The modified array of all installed plugins.
	 */
	public function check_slug($plugins) {
		foreach ( $plugins as $path => $plugin ) {
			$parts = explode('/', $path);
			$slug = $parts[0];
			if ( $slug == $this->slug ) $plugins[$path]['slug'] = $slug;
		}
		return $plugins;
	}

	/**
	 * Provide Information About the Plugin.
	 *
	 * This function hooks on 'plugins_api' Wordpress filter and returns
	 * structured information about the plugin, which are shown when 'View
	 * Details' link in Plugins page of the plugin is pressed.
	 *
	 * @since 4.14.0
	 *
	 * @param array $res An array of structured information about the plugin.
	 *        Initially it is false.
	 * @param string $action The purpose this function is called.
	 * @param array $arg An array of arguments about the plugin.
	 *
	 * @return false|array An array with structured information about the plugin
	 *         if this is a call for 'plugin_information' referring to the
	 *         specific plugin, false otherwise.
	 */
	public function check_info($res, $action, $arg) {
		if( $action != 'plugin_information' ) {
			return false;
		}
		if( $this->slug !== $arg->slug ) {
			return false;
		}
		if( false == $remote = get_transient( $this->transient_prefix . '_update_' . $this->slug ) ) {
			$remote = wp_remote_post($this->update_path, array(
				'timeout' => 10,
				'headers' => array(
					'Accept' => 'application/json'
				),
				'body' => $this->request_params['info']
			));
			if ( !is_wp_error( $remote ) && isset( $remote['response']['code'] ) && $remote['response']['code'] == 200 && !empty( $remote['body'] ) )
				set_transient( $this->transient_prefix . '_update_' . $this->slug, $remote, 43200 );
			else {
				$remote = $this->fallback_info;
				$remote = array ( 'body' => json_encode($remote) );
			}
		}
		if ( ! empty( $remote ) ) {
			$remote = json_decode( $remote['body'] );
			if ( $remote ) {
				$res = new stdClass();
				$res->name = ( isset($remote->info->Name) ? $remote->info->Name : '' );
				$res->slug = $this->slug;
				$res->version = ( isset($remote->info->Version) ? $remote->info->Version : '' );
				$res->tested = ( isset($remote->info->Tested) ? $remote->info->Tested : '' );
				$res->homepage = ( isset($remote->info->PluginURI) ? $remote->info->PluginURI : '' );
				$res->requires = ( isset($remote->info->RequiresWP) ? $remote->info->RequiresWP : '' );
				$res->author = ( isset($remote->info->Author) ? $remote->info->Author : '' );
				$res->author_profile = ( isset($remote->info->AuthorURI) ? $remote->info->AuthorURI : '' );
				$res->download_link = ( isset($remote->info->DownloadURI) ? $remote->info->DownloadURI : '' );
				$res->trunk = ( isset($remote->info->DownloadURI) ? $remote->info->DownloadURI : '' );
				$res->requires_php = ( isset($remote->info->RequiresPHP) ? $remote->info->RequiresPHP : '' );
				$res->last_updated = ( isset($remote->info->LastUpdated) ? $remote->info->LastUpdated : '' );
				if ( isset($remote->sections->Description) ) $res->sections['description'] = $remote->sections->Description;
				if ( isset($remote->sections->Installation) ) $res->sections['installation'] = $remote->sections->Installation;
				if ( isset($remote->sections->Changelog) ) $res->sections['changelog'] = $remote->sections->Changelog;
				if ( isset($remote->sections->Frequently_Asked_Questions) ) $res->sections['faq'] = $remote->sections->Frequently_Asked_Questions;
				if( !empty( $remote->screenshots ) ) {
					$res->sections['screenshots'] = $remote->screenshots;
				}
				if( !empty( $remote->banners ) ) {
					$res->banners["high"] = $remote->banners->High;
					$res->banners["low"] = $remote->banners->Low;
				}
				return $res;
			}

		}
		return false;
	}

	/**
	 * Check Plugin for New Update.
	 *
	 * This function hooks on 'pre_set_site_transient_update_plugins' Wordpress
	 * filter and checks whether a new update is available.
	 *
	 * @since 4.14.0
	 *
	 * @param array $transient An array of structured information about the
	 *        plugin's update status.
	 *
	 * @return array The modified $transient structure.
	 */
	public function check_update($transient) {
		if ( empty($transient->checked) || !isset($transient->checked[$this->plugin_slug]) ) {
			return $transient;
		}
		$current_version = $transient->checked[$this->plugin_slug];
		if( false == $remote = get_transient( $this->transient_prefix . '_upgrade_' . $this->slug ) ) {
			$remote = wp_remote_post($this->update_path, array(
				'timeout' => 10,
				'headers' => array(
					'Accept' => 'application/json'
				),
				'body' => $this->request_params['update']
			));
			if ( !is_wp_error( $remote ) && isset( $remote['response']['code'] ) && $remote['response']['code'] == 200 && !empty( $remote['body'] ) )
				set_transient( $this->transient_prefix . '_upgrade_' . $this->slug, $remote, 43200 );

		}
		if ( !is_wp_error( $remote ) && isset( $remote['response']['code'] ) && $remote['response']['code'] == 200 && !empty( $remote['body'] ) ) {
			$remote = json_decode( $remote['body'] );
			if ( $remote && version_compare($current_version, $remote->version, '<') ) {
				$obj = new stdClass();
				$obj->slug = $this->slug;
				$obj->plugin = $this->plugin_slug;
				$obj->new_version = $remote->version;
				$obj->package = $remote->url;
				$transient->response[$this->plugin_slug] = $obj;
			}
		}
		return $transient;
	}
}