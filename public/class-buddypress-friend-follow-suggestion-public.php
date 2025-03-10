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
	public function __construct( $plugin_name, $version = null ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version ?? ( defined( 'BFFS_PLUGIN_VERSION' ) ? BFFS_PLUGIN_VERSION : '1.0.0' );
	}
	

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		$rtl_css = is_rtl() ? '-rtl' : '';
		if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
			$css_extension = '.css';
		} else {
			$css_extension = '.min.css';
		}

		wp_register_style( 'bp-friend-swiper-slider', BFFS_PLUGIN_URL . 'public/css' . $rtl_css . '/bp-friend-swiper' . $css_extension, array(), $this->version, 'all' );
		wp_register_style( 'bpffs-icon', BFFS_PLUGIN_URL . 'public/css' . $rtl_css . '/bpffs-icons' . $css_extension, array(), $this->version, 'all' );

		$widget_layout = get_option( 'widget_bp_friend_follow_suggestion_widget' );
		foreach ( $widget_layout as $layout_widget ) {
			if ( isset( $layout_widget['layout'] ) && 'horizontal_layout' == $layout_widget['layout'] ) {
				wp_register_style( 'swiper-style', BFFS_PLUGIN_URL . 'public/css/vendor/swiper-bundle.min.css', array(), $this->version, 'all' );
			}
		}
		wp_register_style( 'bp-friend-follow-public-css', BFFS_PLUGIN_URL . 'public/css' . $rtl_css . '/buddypress-friend-follow-suggestion-public' . $css_extension, array(), $this->version, 'all' );
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
		if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
			$js_extension = '.js';
		} else {
			$js_extension = '.min.js';
		}

		wp_register_script( 'bp-friend-suggestion-transfrom', BFFS_PLUGIN_URL . 'public/js/vendor/jquery.transform2d.js', array( 'jquery' ), $this->version, true );
		wp_register_script( 'bp-friend-suggestion-swiper-slider', BFFS_PLUGIN_URL . 'public/js/vendor/jquery.swiper-slider.js', array( 'jquery' ), $this->version, true );

		wp_register_script( 'bp-friend-follow-public-js', BFFS_PLUGIN_URL . 'public/js/buddypress-friend-follow-suggestion-public' . $js_extension, array( 'jquery' ), $this->version, true );

		$widget_layout = get_option( 'widget_bp_friend_follow_suggestion_widget' );
		foreach ( $widget_layout as $layout_widget ) {
			if ( isset( $layout_widget['layout'] ) && 'horizontal_layout' == $layout_widget['layout'] ) {
				wp_register_script( 'bp-friend-follow-slider', BFFS_PLUGIN_URL . 'public/js/vendor/buddypress-friend-follow-suggestion-swiper-slider.min.js', array( 'jquery' ) );
			}
		}

		wp_localize_script(
			'bp-friend-follow-public-js',
			'bffs_ajax_object',
			array(
				'ajaxurl'    => admin_url( 'admin-ajax.php' ),
				'ajax_nonce' => wp_create_nonce( 'bffs-widget-nonce' ),
				'root'       => esc_url_raw( rest_url() ),
				'nonce'      => wp_create_nonce( 'wp_rest' ),
				'userId'     => get_current_user_id(),
				'security'   => wp_create_nonce( 'bffs-swipe-' . get_current_user_id() ),
				'compose'    => bp_suggestions_get_compose_message_url(),
			)
		);
	}


	/**
	 * Display user compatibility match.
	 *
	 * @since    1.0.0
	 */
	public function buddypress_friend_follow_compatibility_match() {
		global $bp;

		if ( is_user_logged_in() && ! bp_is_my_profile() ) {
			if ( is_multisite() && is_plugin_active_for_network( plugin_basename( BFFS_PLUGIN_FILE ) ) ) {
				$bffs_general_setting = get_site_option( 'bffs_general_setting' );
			} elseif ( is_multisite() ) {
				$bffs_general_setting = get_site_option( 'bffs_general_setting' );
			} else {
				$bffs_general_setting = get_option( 'bffs_general_setting' );
			}

			if ( isset( $bffs_general_setting['enable_profile_match'] ) ) {
				echo '<div class="bffs-matching-wrap">';
				echo esc_html__( 'Profile Match: ', 'buddypress-friend-follow-suggestion' ) . esc_html( $this->buddypress_friend_follow_compatibility_score( $bp->loggedin_user->id, bp_displayed_user_id() ) ) . '%';
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

		if ( is_multisite() && is_plugin_active_for_network( plugin_basename( BFFS_PLUGIN_FILE ) ) ) {
			$bffs_general_setting = get_site_option( 'bffs_general_setting' );
		} elseif ( is_multisite() ) {
			$bffs_general_setting = get_site_option( 'bffs_general_setting' );
		} else {
			$bffs_general_setting = get_option( 'bffs_general_setting' );
		}

		$score = ( isset( $bffs_general_setting['profile_st_percentage'] ) && $bffs_general_setting['profile_st_percentage'] != '' ) ? $bffs_general_setting['profile_st_percentage'] : 0;

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
						$score += intval( $bffs_match_data['percentage'] );
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

	/**
	 * Bffs_remove_user_form_widget
	 */
	public function bffs_remove_user_form_widget() {
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'bffs-widget-nonce' ) ) {
			return false;
		}
		$mem_id = isset( $_POST['mem_id'] ) ? sanitize_text_field( wp_unslash( $_POST['mem_id'] ) ) : '';
		if ( $mem_id ) {
			$existing_removed_users = get_user_meta( bp_loggedin_user_id(), 'bffs_remove_user', true );
			if ( ! is_array( $existing_removed_users ) ) {
				$existing_removed_users = array();
			}
			// Add the new member ID to the existing array.
			$existing_removed_users[] = $mem_id;
			update_user_meta( bp_loggedin_user_id(), 'bffs_remove_user', $existing_removed_users );
		}
	}

	/**
	 * Bffs_add_friend_widget
	 */
	public function bffs_add_friend_widget() {
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'bffs-widget-nonce' ) ) {
			return false;
		}

		$friend_id = isset( $_POST['mem_id'] ) ? sanitize_text_field( wp_unslash( $_POST['mem_id'] ) ) : '';
		if ( 'not_friends' === BP_Friends_Friendship::check_is_friend( bp_loggedin_user_id(), $friend_id ) ) {
			if ( ! friends_add_friend( bp_loggedin_user_id(), $friend_id ) ) {
				$response['feedback'] = sprintf(
					'<div class="bp-feedback error">%s</div>',
					esc_html__( 'Friendship could not be requested.', 'buddypress-friend-follow-suggestion' )
				);
				wp_send_json_error( $response );
			} else {
				wp_send_json_success( array( 'contents' => bp_get_add_friend_button( $friend_id ) ) );
			}
		}
	}

	/**
	 * Bffs_follow_button_widget
	 */
	public function bffs_follow_button_widget() {
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'bffs-widget-nonce' ) ) {
			return false;
		}
		$leader_id = isset( $_POST['mem_id'] ) ? sanitize_text_field( wp_unslash( $_POST['mem_id'] ) ) : '';
		if ( bp_follow_start_following(
			array(
				'leader_id'   => $leader_id,
				'follower_id' => bp_loggedin_user_id(),
			)
		) ) {
			// output unfollow button.
			$output = bp_follow_get_add_follow_button(
				array(
					'leader_id'   => $leader_id,
					'follower_id' => bp_loggedin_user_id(),
				)
			);

		} else {
			// output fallback invalid button.
			$args = array(
				'id'        => 'invalid',
				'link_href' => 'javascript:;',
				'component' => 'follow',
			);

			if ( bp_follow_is_following(
				array(
					'leader_id'   => $leader_id,
					'follower_id' => bp_loggedin_user_id(),
				)
			) ) {
				$output = bp_get_button(
					array_merge(
						array(
							'link_text' => __( 'Already following', 'buddypress-friend-follow-suggestion' ),
						),
						$args
					)
				);
			} else {
				$output = bp_get_button(
					array_merge(
						array(
							'link_text' => __( 'Error following user', 'buddypress-friend-follow-suggestion' ),
						),
						$args
					)
				);
			}
		}

		$output = apply_filters(
			'bp_follow_ajax_action_start_response',
			array(
				'button' => $output,
			),
			$leader_id
		);

		wp_send_json_success( $output );
	}


	public function bp_friend_follow_suggestion_register_user_meta() {
		$object_type = 'user';
		$meta_args   = array(
			'type'         => 'array',
			'single'       => true,
			'show_in_rest' => array(
				'schema' => array(
					'type'  => 'array',
					'items' => array(
						'type' => 'integer',
					),
				),
			),
		);
		register_meta( $object_type, 'swiped', $meta_args );
	}

	public function bp_friend_follow_suggestion_member_dislike() {
		check_ajax_referer( 'bffs-swipe-' . get_current_user_id(), 'nonce' );

		$rejected_member_id           = sanitize_text_field( wp_unslash( $_POST['member_id'] ) );
		$rejected_members_transient   = get_transient( 'rejected_members_' . get_current_user_id() );
		$rejected_members             = array();

		if ( false === $rejected_members_transient ) {
			$rejected_members[] = $rejected_member_id;
		} else {
			$rejected_members[] = $rejected_member_id;
		}
		$set = set_transient( 'rejected_members_' . get_current_user_id(), $rejected_members, 24 * HOUR_IN_SECONDS );

		if ( $set ) {
			wp_send_json_success();
		} else {
			wp_send_json_error();
		}
	}


	public function bp_friend_follow_suggestion_remove_dislike_members( $members ) {

		if ( ! is_user_logged_in() ) {
			return $members;
		}

		if ( false === get_transient( 'rejected_members_' . get_current_user_id() ) ) {
			return $members;
		}

		$rejected_members = get_transient( 'rejected_members_' . get_current_user_id() );
		$members          = array_diff( $members, $rejected_members );

		return apply_filters( 'bp_friend_follow_suggestion_remove_dislike_members', $members );
	}
}


