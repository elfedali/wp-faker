<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://codewithabdessamad.ma
 * @since             1.0.0
 * @package           Wp_Faker
 *
 * @wordpress-plugin
 * Plugin Name:       Wordpress Faker
 * Plugin URI:        https://codewithabdessamad.ma
 * Description:       faker data for dev porpse
 * Version:           1.0.0
 * Author:            Code with abdessamad
 * Author URI:        https://codewithabdessamad.ma/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wp-faker
 * Domain Path:       /languages
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
define( 'WP_FAKER_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wp-faker-activator.php
 */
function activate_wp_faker() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wp-faker-activator.php';
	Wp_Faker_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wp-faker-deactivator.php
 */
function deactivate_wp_faker() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wp-faker-deactivator.php';
	Wp_Faker_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_wp_faker' );
register_deactivation_hook( __FILE__, 'deactivate_wp_faker' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-wp-faker.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_wp_faker() {

	$plugin = new Wp_Faker();
	$plugin->run();

}
run_wp_faker();
