<?php
/*
Plugin Name: Advanced Widgets for Elementor
Plugin URI: 
Description: Advanced Widgets for Elementor is a collection of advanced widgets for Elementor Page Builder.
Author: Mae Company
Version: 1.0
Author URI: https://mae.company
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class AWElementor {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Plugin_Name_Loader    $loader    Maintains and registers all hooks for the plugin.
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
		$this->version = AWELEMENTOR_VERSION;
		$this->plugin_name = 'advanced-elementor'; // TODO: change

		$this->load_dependencies();
		$this->set_locale();
		$this->define_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Plugin_Name_Loader. Orchestrates the hooks of the plugin.
	 * - Plugin_Name_i18n. Defines internationalization functionality.
	 * - Plugin_Name_Admin. Defines all hooks for the admin area.
	 * - Plugin_Name_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-awelementor-loader.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-awelementor-i18n.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-awelementor-elementor.php';

		//require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-awelementor-admin.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-awelementor-public.php';

		$this->loader = new AWElementor_Loader();
	}

	/**
	 * Run the plugin.
	 */
	public function run() {
		$this->loader->load();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Plugin_Name_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {
		$plugin_i18n = new AWElementor_i18n();
		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
	}

	/**
	 * Register all of the hooks of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_hooks() {
		$elementor = new AWElementor_Elementor();

		$this->loader->add_action( 'elementor/elements/categories_registered', $elementor, 'register_categories' );
		$this->loader->add_action( 'elementor/controls/controls_registered', $elementor, 'register_controls' );
		$this->loader->add_action( 'elementor/widgets/widgets_registered', $elementor, 'register_widgets' );

//		$this->define_admin_hooks();
		$this->define_public_hooks();
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {
		$plugin_public = new AWElementor_Public( $this->get_plugin_name(), $this->get_version() );

//		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
//		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		$this->loader->add_action( 'get_header', $plugin_public, 'get_header' );
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
	 * @return    Plugin_Name_Loader    Orchestrates the hooks of the plugin.
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