<?php
/**
 * Fired during plugin activation
 *
 * @link       https://wbcomdesigns.com/
 * @since      1.0.0
 *
 * @package    Buddypress_Friend_Follow_Suggestion
 * @subpackage Buddypress_Friend_Follow_Suggestion/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Buddypress_Friend_Follow_Suggestion
 * @subpackage Buddypress_Friend_Follow_Suggestion/includes
 * @author     WBComDesigns <admin@wbcomdesigns.com>
 */
class Buddypress_Friend_Follow_Suggestion_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		$bffs_general_setting = get_option( 'bffs_general_setting' );
		if ( false === $bffs_general_setting ) {
			$bffs_general_setting = array(
				'enable_profile_match' => 1,
				'profile_st_percentage' => 20,
			);
			update_option( 'bffs_general_setting', $bffs_general_setting );
		}
	}

}
