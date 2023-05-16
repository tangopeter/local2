<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       tangopeter.co.nz
 * @since      1.0.0
 *
 * @package    Fwg_Custom_Functions
 * @subpackage Fwg_Custom_Functions/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Fwg_Custom_Functions
 * @subpackage Fwg_Custom_Functions/includes
 * @author     Peter Williamson <peter@t1.co.nz>
 */
class Fwg_Custom_Functions {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Fwg_Custom_Functions_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'PLUGIN_NAME_VERSION' ) ) {
			$this->version = PLUGIN_NAME_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'fwg-custom-functions';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Fwg_Custom_Functions_Loader. Orchestrates the hooks of the plugin.
	 * - Fwg_Custom_Functions_i18n. Defines internationalization functionality.
	 * - Fwg_Custom_Functions_Admin. Defines all hooks for the admin area.
	 * - Fwg_Custom_Functions_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-fwg-custom-functions-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-fwg-custom-functions-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-fwg-custom-functions-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-fwg-custom-functions-public.php';

		$this->loader = new Fwg_Custom_Functions_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Fwg_Custom_Functions_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Fwg_Custom_Functions_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Fwg_Custom_Functions_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		// Get Rid of “Howdy”
		add_filter('gettext', 'change_howdy', 10, 3);
		function change_howdy($translated, $text, $domain) {
		    if (!is_admin() || 'default' != $domain)
		        return $translated;
		    if (false !== strpos($translated, 'Howdy'))
		        return str_replace('Howdy', 'Welcome', $translated);
		    return $translated;
		}
		remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
		remove_action( 'wp_print_styles', 'print_emoji_styles' );

		// Dashboard Widgets
		function remove_dashboard_meta() {
		        remove_meta_box( 'dashboard_incoming_links', 'dashboard', 'normal' );
		        remove_meta_box( 'dashboard_plugins', 'dashboard', 'normal' );
		        remove_meta_box( 'dashboard_primary', 'dashboard', 'side' );
		        remove_meta_box( 'dashboard_secondary', 'dashboard', 'normal' );
		        remove_meta_box( 'dashboard_quick_press', 'dashboard', 'side' );
		        remove_meta_box( 'dashboard_recent_drafts', 'dashboard', 'side' );
		        // remove_meta_box( 'dashboard_recent_comments', 'dashboard', 'normal' );
		        // remove_meta_box( 'dashboard_right_now', 'dashboard', 'normal' );
		        remove_meta_box( 'dashboard_activity', 'dashboard', 'normal');//since 3.8
		}
		add_action( 'admin_init', 'remove_dashboard_meta' );

		/* Add an order column to the pages listing */
		add_filter('manage_pages_columns', 'my_columns');
		function my_columns($columns)
		    {$crunchify_columns = array(); $title = 'title'; 
		        foreach($columns as $key => $value)
		        { $crunchify_columns[$key] = $value; 
		            if ($key==$title)
		                {$crunchify_columns['order'] = 'Order'; }
		            } 
		            return $crunchify_columns;
		}

		add_action('manage_pages_custom_column',  'my_show_columns');
		function my_show_columns($name) {
		    global $post;

		    switch ($name) {
		        case 'order':
		            $views = $post->menu_order;
		            echo $views;
		            break;
		    }
		}
		// Reorder the Admin Menu items
		function custom_menu_order($menu_ord) {
		    if (!$menu_ord) return true;
		     
		    return array(
		        'index.php', // Dashboard
		        'video-user-manuals/plugin.php',
		        'mwp-support',
		        'separator', // separator
		        'products',        
		        'woocommerce',
		        'separator1', // separator
		        'edit.php?post_type=page', // Pages
		        'edit.php', // Posts
		        'upload.php', // Media
		        'tve_dash_section',
		        'separator2', // separator
		        'ninja-forms',
		        'separator3', // separator
		        'themes.php', // Appearance
		        'options-general.php', // Settings
		        'plugins.php', // Plugins
		        'separator4', // separator
		        'users.php', // Users
		        'tools.php', // Tools
		        'link-manager.php', // Links
		        'edit-comments.php', // Comments
		        'separator-last', // Last separator
		    );
		}
		add_filter('custom_menu_order', 'custom_menu_order'); // Activate custom_menu_order
		add_filter('menu_order', 'custom_menu_order');

		add_filter('get_user_option_admin_color','change_admin_color');
		function change_admin_color() {
		    return 'ectoplasm';
		}
		
		add_filter('admin_footer_text', 'change_text_footer_admin'); //change admin footer text
		function change_text_footer_admin () {
		echo "Website by the lovemarketers";
		}
}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Fwg_Custom_Functions_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Fwg_Custom_Functions_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
