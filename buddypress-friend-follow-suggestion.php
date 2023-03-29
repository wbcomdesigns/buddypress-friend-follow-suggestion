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
 * Version:           1.4.3
 * Author:            Wbcom Designs
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
define( 'BUDDYPRESS_FRIEND_FOLLOW_SUGGESTION_VERSION', '1.4.3' );


if ( ! defined( 'BFFS_PLUGIN_VERSION' ) ) {
	define( 'BFFS_PLUGIN_VERSION', '1.4.3' );
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


require plugin_dir_path( __FILE__ ) . 'edd-license/edd-plugin-license.php';
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

/**
 * Function used return profile field dropdown
 *
 * @since    1.0.0
 * @param field_value $field_value field_value.
 * @param variable    $j Define parameter.
 */
function bffs_profile_fields_dropdown( $field_value = '', $j = 0 ) {

	$groups = array();

	if ( bp_is_active( 'xprofile' ) ) {

		global $group, $field;

		$fields = array();
		$args   = array(
			'hide_empty_fields' => false,
			'member_type'       => bp_get_member_types(),
		);
		if ( bp_has_profile( $args ) ) {
			while ( bp_profile_groups() ) {
				bp_the_profile_group();
				$group_name = str_replace( '&amp;', '&', stripslashes( $group->name ) );

				while ( bp_profile_fields() ) {
					bp_the_profile_field();
					$f = new stdClass();

					$f->group       = $group_name;
					$f->id          = $field->id;
					$f->code        = $field->id;
					$f->name        = str_replace( '&amp;', '&', stripslashes( $field->name ) );
					$f->description = str_replace( '&amp;', '&', stripslashes( $field->description ) );
					$f->type        = $field->type;
					$f->options     = bffs_profile_fields_xprofile_options( $field );
					$fields[]       = $f;
				}
			}
		}

		foreach ( $fields as $f ) {
			$groups[ $f->group ][] = array(
				'id'   => $f->code,
				'name' => $f->name,
			);
			$fields[ $f->code ]    = $f;
		}

		list ($groups, ) = array( $groups, $fields );
		// Unset($groups['Base']);.
	}

	ob_start();
	?>
	<select class="bffs_profile_field_name" name="bffs_general_setting[bffs_match_data][<?php echo esc_attr( $j ); ?>][field_id]">
		<option value=""><?php esc_html_e( 'Select a field', 'buddypress-friend-follow-suggestion' ); ?></option>
		<?php
		foreach ( $groups as $group => $fields ) {
			$group = esc_attr( $group );
			echo '<optgroup label="' . esc_attr( $group ) . '">\n';
			foreach ( $fields as $field ) {
				$selected = $field['id'] == $field_value ? " selected='selected'" : '';
				echo "<option value='$field[id]'$selected data-field-name='" . esc_attr( $field['name'] ) . "'>" . esc_attr( $field['name'] ) . "</option>\n"; //phpcs:ignore
			}
			echo "</optgroup>\n";
		}
		?>
	</select>

	<?php
	return ob_get_clean();
}

/**
 * Bffs_profile_fields_xprofile_options
 *
 * @param field $field field.
 */
function bffs_profile_fields_xprofile_options( $field ) {

	$options = array();

	if ( false === $field->type_obj->supports_options ) {
		return $options;
	}

	$rows = $field->get_children();
	if ( is_array( $rows ) ) {
		foreach ( $rows as $row ) {
			if ( 'gender' === $field->type ) {
				if ( 1 == $row->option_order ) {
					$options[ 'his_' . stripslashes( trim( $row->name ) ) ] = stripslashes( trim( $row->name ) );
				} elseif ( 2 == $row->option_order ) {
					$options[ 'her_' . stripslashes( trim( $row->name ) ) ] = stripslashes( trim( $row->name ) );
				} else {
					$options[ 'their_' . stripslashes( trim( $row->name ) ) ] = stripslashes( trim( $row->name ) );
				}
			} else {
				$options[ stripslashes( trim( $row->name ) ) ] = stripslashes( trim( $row->name ) );
			}
		}
	}

	return $options;
}

add_action( 'activated_plugin', 'bffs_activation_redirect_settings' );

/**
 * Redirect to plugin settings page after activated
 *
 * @param plugin $plugin plugin.
 */
function bffs_activation_redirect_settings( $plugin ) {
	if ( ! isset( $_GET['plugin'] ) ) {
		return;
	}
	if ( plugin_basename( __FILE__ ) === $plugin && class_exists( 'Buddypress' ) ) {
		if ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] == 'activate' && isset( $_REQUEST['plugin'] ) && $_REQUEST['plugin'] == $plugin ) {
			wp_redirect( admin_url( 'admin.php?page=bffs-settings' ) );
			exit;
		}
	}
}



/**
 *  Check if buddypress activate.
 */
function bffs_requires_buddypress() {

	if ( ! class_exists( 'BuddyPress' ) ) {
		deactivate_plugins( plugin_basename( __FILE__ ) );
		add_action( 'admin_notices', 'bffs_required_plugin_admin_notice' );
		unset( $_GET['activate'] );
	} elseif ( ! bp_is_active( 'xprofile' ) ) {
		deactivate_plugins( plugin_basename( __FILE__ ) );
		add_action( 'admin_notices', 'bffs_required_component_admin_notice' );
		unset( $_GET['activate'] );
	}
}

add_action( 'admin_init', 'bffs_requires_buddypress' );


/**
 * Throw an Alert to tell the Admin why it didn't activate.
 *
 * @author wbcomdesigns
 * @since  2.3.0
 */
function bffs_required_plugin_admin_notice() {

	$bpmb_plugin = esc_html__( ' BuddyPress Friend & Follow Suggestion', 'buddypress-friend-follow-suggestion' );
	$bp_plugin   = esc_html__( 'BuddyPress', 'buddypress-friend-follow-suggestion' );
	echo '<div class="error"><p>';
	echo sprintf( esc_html__( '%1$s is ineffective now as it requires %2$s to be installed and active.', 'buddypress-friend-follow-suggestion' ), '<strong>' . esc_html( $bpmb_plugin ) . '</strong>', '<strong>' . esc_html( $bp_plugin ) . '</strong>' );
	echo '</p></div>';
	if ( isset( $_GET['activate'] ) ) {
		unset( $_GET['activate'] );
	}
}


function bffs_required_component_admin_notice() {
	$bpmb_plugin = esc_html__( ' BuddyPress Friend & Follow Suggestion', 'buddypress-friend-follow-suggestion' );
	$component   = esc_html__( 'Extended Profiles', 'buddypress-friend-follow-suggestion' );
	echo '<div class="error"><p>';
	echo sprintf( esc_html__( '%1$s is ineffective now as it requires %2$s to be active.', 'buddypress-friend-follow-suggestion' ), '<strong>' . esc_html( $bpmb_plugin ) . '</strong>', '<strong>' . esc_html( $component ) . '</strong>' );
	echo '</p></div>';
	if ( isset( $_GET['activate'] ) ) {
		unset( $_GET['activate'] );
	}
}
