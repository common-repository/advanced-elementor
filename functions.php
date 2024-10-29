<?php

/**
 * The plugin bootstrap file
 *
 * @since             1.0.0
 * @package           AWElementor
 *
 * @wordpress-plugin
 * Plugin Name:       Advanced Widgets for Elementor
 * Plugin URI:        https://wordpress.org/plugins/awelementor/
 * Description:       Advanced Widgets for Elementor Page Builder.
 * Version:           1.0.7
 * Author:            Mae Company
 * Author URI:        https://mae.company/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       awelementor
 * Domain Path:       /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Current plugin version.
 */
define( 'AWELEMENTOR_VERSION', '1.0.7' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-awelementor-activator.php
 */
function activate_awelementor() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-awelementor-activator.php';
	AWElementor_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-awelementor-deactivator.php
 */
function deactivate_awelementor() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-awelementor-deactivator.php';
	AWElementor_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_awelementor' );
register_deactivation_hook( __FILE__, 'deactivate_awelementor' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-awelementor.php';

/**
 * Begins execution of the plugin.
 *
 * @since    1.0.0
 */
function run_awelementor() {
	$plugin = new AWElementor();
	$plugin->run();
}
run_awelementor();
