<?php
ob_start();
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.fiverr.com/junaidzx90
 * @since             1.0.0
 * @package           Locker_Plugin
 *
 * @wordpress-plugin
 * Plugin Name:       Locker plugin
 * Plugin URI:        https://www.fiverr.com/
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Md Junayed
 * Author URI:        https://www.fiverr.com/junaidzx90
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       locker-plugin
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
define( 'LOCKER_PLUGIN_VERSION', '1.0.0' );
date_default_timezone_set(get_option('timezone_string')?get_option('timezone_string'):'UTC');
/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-locker-plugin-activator.php
 */
function activate_locker_plugin() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-locker-plugin-activator.php';
	Locker_Plugin_Activator::activate();
}

function is_locked($post_id){
	$locker = get_post_meta($post_id, 'is_post_locked', true);
	if($locker && is_array($locker) && count($locker) > 0 && in_array($locker['end_date'], $locker) && !empty($locker['end_date'])){
		return true;
	}else{
		return false;
	}
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-locker-plugin-deactivator.php
 */
function deactivate_locker_plugin() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-locker-plugin-deactivator.php';
	Locker_Plugin_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_locker_plugin' );
register_deactivation_hook( __FILE__, 'deactivate_locker_plugin' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-locker-plugin.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_locker_plugin() {

	$plugin = new Locker_Plugin();
	$plugin->run();

}
run_locker_plugin();
