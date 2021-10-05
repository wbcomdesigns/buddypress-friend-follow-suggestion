<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://wbcomdesigns.com/
 * @since      1.0.0
 *
 * @package    Buddypress_Friend_Follow_Suggestion
 * @subpackage Buddypress_Friend_Follow_Suggestion/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Buddypress_Friend_Follow_Suggestion
 * @subpackage Buddypress_Friend_Follow_Suggestion/public
 * @author     WBComDesigns <admin@wbcomdesigns.com>
 */
class Buddypress_Friend_Follow_Suggestion_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * The profile fields.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $profile_fields    The profile fields.
	 */
	private $profile_fields = null;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string $plugin_name       The name of the plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Buddypress_Friend_Follow_Suggestion_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Buddypress_Friend_Follow_Suggestion_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( '$bpffs-icon', plugin_dir_url( __FILE__ ) . 'css/bpffs-icons.css', array(), $this->version, 'all' );
                
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/buddypress-friend-follow-suggestion-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Buddypress_Friend_Follow_Suggestion_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Buddypress_Friend_Follow_Suggestion_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/buddypress-friend-follow-suggestion-public.js', array( 'jquery' ), $this->version, true );

	}


	/**
	 * Display user compatibility match.
	 *
	 * @since    1.0.0
	 */
	public function buddypress_friend_follow_compatibility_match() {
		global $bp;

		if ( is_user_logged_in() && ! bp_is_my_profile() ) {
			if ( is_multisite() && is_plugin_active_for_network( plugin_basename( __FILE__ ) ) ) {
				$bffs_general_setting = get_site_option( 'bffs_general_setting' );
			} else {
				$bffs_general_setting = get_option( 'bffs_general_setting' );
			}

			if ( isset( $bffs_general_setting['enable_profile_match'] ) ) {
				echo '<div class="bffs-matching-wrap">';
				echo esc_html__( 'Profile Match: ', 'buddypress-friend-follow-suggestion' ) . $this->buddypress_friend_follow_compatibility_score( $bp->loggedin_user->id, bp_displayed_user_id() ) . '%';
				echo '</div>';
			}
		}
	}

	/**
	 * Return  user compatibility match score.
	 *
	 * @since    1.0.0
	 * @param user_id1 $user_id1 user_id1.
	 * @param user_id2 $user_id2 user_id2.
	 */
	public function buddypress_friend_follow_compatibility_score( $user_id1 = false, $user_id2 = false ) {

		if ( is_multisite() && is_plugin_active_for_network( plugin_basename( __FILE__ ) ) ) {
			$bffs_general_setting = get_site_option( 'bffs_general_setting' );
		} else {
			$bffs_general_setting = get_option( 'bffs_general_setting' );
		}
		
		$score = ( isset( $bffs_general_setting['profile_st_percentage'] ) && $bffs_general_setting['profile_st_percentage'] != ''  ) ? $bffs_general_setting['profile_st_percentage'] : 0;

		if ( ! $user_id1 || ! $user_id2 ) {
			return $score;
		}

		$all_fields = $this->get_profile_fields();
		// bffs_match_data.
		if ( ! empty( $bffs_general_setting['bffs_match_data'] ) ) {

			foreach ( $bffs_general_setting['bffs_match_data'] as $bffs_match_data ) {

				$field1 = xprofile_get_field_data( $bffs_match_data['field_id'], $user_id1 );
				$field2 = xprofile_get_field_data( $bffs_match_data['field_id'], $user_id2 );

				// multi type.
				if ( isset( $all_fields[ $bffs_match_data['field_id'] ]['options'] ) ) {

					if ( $field1 && $field2 ) {

						$intersect = array_intersect( (array) $field1, (array) $field2 );

						if ( count( $intersect ) >= 1 ) {
							$score += $bffs_match_data['percentage'] * count( $intersect );
						} elseif ( isset( $bffs_match_data['stop_match'] ) && $bffs_match_data['stop_match'] == 1 ) {
							return $score;
						}
					} elseif ( isset( $bffs_match_data['stop_match'] ) && $bffs_match_data['stop_match'] == 1 ) {
						return $score;
					}
				} else {
					// single type.

					if ( $field1 && $field2 && $field1 == $field2 ) {						
						$score += $bffs_match_data['percentage'];
					} elseif ( isset( $bffs_match_data['stop_match'] ) && $bffs_match_data['stop_match'] == 1 ) {
						return $score;
					}
				}
			}
		}

		if ( $score > 100 ) {
			$score = 100;
		}

		return $score;
	}

	/**
	 * Get user profile fields.
	 *
	 * @since    1.0.0
	 */
	public function get_profile_fields() {
		if ( null !== $this->profile_fields ) {
			return $this->profile_fields;
		}

		$fields = array();
		if ( function_exists( 'bp_is_active' ) && bp_is_active( 'xprofile' ) ) {
			if ( function_exists( 'bp_has_profile' ) ) {
				if ( bp_has_profile( 'hide_empty_fields=0' ) ) {

					while ( bp_profile_groups() ) {
						bp_the_profile_group();
						while ( bp_profile_fields() ) {
							bp_the_profile_field();

							switch ( bp_get_the_profile_field_type() ) {

								case 'multiselectbox':
								case 'checkbox':
									$field_type = 'multi';
									break;

								default:
									$field_type = 'single';
									break;
							}
							$profile_field_id            = bp_get_the_profile_field_id();
							$fields[ $profile_field_id ] = array(
								'id'   => $profile_field_id,
								'name' => bp_get_the_profile_field_name(),
							);

							if ( $field_type == 'multi' ) {
								$fields[ $profile_field_id ]['options'] = 'true';
							}
						}
					}
				}
			}
		}
		$this->profile_fields = $fields;

		return $fields;
	}

}
