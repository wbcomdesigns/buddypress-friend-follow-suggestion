<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://wbcomdesigns.com/
 * @since             1.0.0
 * @package           Buddypress_Friend_Follow_Suggestion
 *
 * @wordpress-plugin
 * Plugin Name:       BuddyPress Friend & Follow Suggestion
 * Plugin URI:        https://wbcomdesigns.com/downloads/buddypress-friend-follow-suggestion
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            WBComDesigns
 * Author URI:        https://wbcomdesigns.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       buddypress-friend-follow-suggestion
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
define( 'BUDDYPRESS_FRIEND_FOLLOW_SUGGESTION_VERSION', '1.0.0' );


if ( ! defined( 'BFFS_PLUGIN_VERSION' ) ) {
	define( 'BFFS_PLUGIN_VERSION', '1.0.0' );
}
if ( ! defined( 'BFFS_PLUGIN_FILE' ) ) {
	define( 'BFFS_PLUGIN_FILE', __FILE__ );
}
define( 'BFFS_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'BFFS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-buddypress-friend-follow-suggestion-activator.php
 */
function activate_buddypress_friend_follow_suggestion() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-buddypress-friend-follow-suggestion-activator.php';
	Buddypress_Friend_Follow_Suggestion_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-buddypress-friend-follow-suggestion-deactivator.php
 */
function deactivate_buddypress_friend_follow_suggestion() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-buddypress-friend-follow-suggestion-deactivator.php';
	Buddypress_Friend_Follow_Suggestion_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_buddypress_friend_follow_suggestion' );
register_deactivation_hook( __FILE__, 'deactivate_buddypress_friend_follow_suggestion' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-buddypress-friend-follow-suggestion.php';


require plugin_dir_path(__FILE__) . 'edd-license/edd-plugin-license.php';
/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_buddypress_friend_follow_suggestion() {

	$plugin = new Buddypress_Friend_Follow_Suggestion();
	$plugin->run();

}
run_buddypress_friend_follow_suggestion();
