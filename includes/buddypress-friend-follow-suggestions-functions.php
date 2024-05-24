<?php


if ( ! function_exists( 'bp_suggestions_get_matched_users' ) ) {
	/**
	 * Get matched members
	 *
	 * @param  int $user_id             User whose match find.
	 * @param  int $max_members         Maximum number of matched users.
	 * @param  int $percentage_criteria percentage Critatia on behalf get the metched users.
	 *
	 * @return  array
	 */
	function bp_suggestions_get_matched_users( $user_id, $max_members, $percentage_criteria, $suggest = '' ) {
		$matched_members     = array();
		if ( ! empty( $user_id ) ) {
			global $wpdb;

			$exclude_user = array();
			$is_confirmed = 0;
			if ( 'friends' === $suggest ) {
				$sql          = $wpdb->prepare( "Select `friend_user_id` from {$wpdb->prefix}bp_friends where initiator_user_id = %d AND `is_confirmed` = %d", $user_id, $is_confirmed );
				$exclude_user = $wpdb->get_col( $sql );
			} elseif ( 'follow' === $suggest ) {
				$sql          = "select leader_id from {$wpdb->prefix}bp_follow where follower_id = {$user_id}";
				$exclude_user = $wpdb->get_col( $sql );
			}

			if ( ! empty( get_user_meta( $user_id, 'swiped', true ) ) ) {
				$exclude_user = array_merge( $exclude_user, get_user_meta( $user_id, 'swiped', true ) );
			}

			$bffs_general_setting = get_option( 'bffs_general_setting' );
			$matche_obj           = new Buddypress_Friend_Follow_Suggestion_Public( 'buddypress-friend-follow-suggestion', BFFS_PLUGIN_VERSION );
			$max_members          = ! empty( $max_members ) ? $max_members : apply_filters( 'bp_suggestion_max_members', 5 );
			$users                = get_users(
				array(
					'exclude' => $exclude_user,
					'number'  => $max_members,
				)
			);

			$match_data          = ! empty( $bffs_general_setting['bffs_match_data'] ) ? $bffs_general_setting['bffs_match_data'] : '';
			$percentage_criteria = ! empty( $percentage_criteria ) ? $percentage_criteria : apply_filters( 'bp_suggestion_critaria', 10 );
			
			foreach ( $users as $key => $user ) {
				if ( $user_id !== $user->ID ) {
					$matche_score = $matche_obj->buddypress_friend_follow_compatibility_score( $user_id, $user->ID );
					if ( $percentage_criteria <= $matche_score ) {
						if ( 'friends' === $suggest && function_exists( 'bp_is_friend' ) ) {

							$is_friend = bp_is_friend( $user->ID );
							if ( 'not_friends' === $is_friend ) {
								$matched_members[] = $user->ID;
							}
						} elseif ( 'follow' === $suggest ) {
							if ( function_exists( 'bp_add_follow_button' ) ) {
								$is_following = bp_is_following(
									array(
										'leader_id'   => $user->ID,
										'follower_id' => $user_id,
									)
								);
								if ( 0 == $is_following ) {
									$matched_members[] = $user->ID;
								}
							}

							if ( function_exists( 'bp_follow_add_follow_button' ) ) {
								$is_following = bp_follow_is_following(
									array(
										'leader_id'   => $user->ID,
										'follower_id' => $user_id,
									)
								);
								if ( 0 == $is_following ) {
									$matched_members[] = $user->ID;
								}
							}
						} else {
							$matched_members[] = $user->ID;
						}
					}
				}
			}			
		}
		return apply_filters( 'bffs_remove_specific_role_from_suggestion_widget', $matched_members );
	}
}


if ( ! function_exists( 'bp_suggestions_get_compose_message_url' ) ) {
	function bp_suggestions_get_compose_message_url() {
		if ( ( function_exists( 'buddypress' ) && isset( buddypress()->buddyboss ) ) ) {
			$messgae_url = bp_loggedin_user_domain() . bp_get_messages_slug() . '/compose/?r=';
		} else {
			$messgae_url = bp_loggedin_user_domain() . bp_get_messages_slug() . '/compose/?r=';
		}

		return apply_filters( 'bp_suggestions_compose_message_url', $messgae_url );
	}
}




/*
 * Is the current user online
 *
 * @param $user_id
 *
 * @return bool
 */
if ( ! function_exists( 'bp_suggestions_is_user_online' ) ) {

	function bp_suggestions_is_user_online( $user_id ) {
		if ( ! function_exists( 'bp_get_user_last_activity' ) ) {
			return;
		}

		$last_activity = strtotime( bp_get_user_last_activity( $user_id ) );

		if ( empty( $last_activity ) ) {
			return false;
		}

		// the activity timeframe is 5 minutes
		$activity_timeframe = 5 * MINUTE_IN_SECONDS;

		return time() - $last_activity <= $activity_timeframe;
	}
}

/*
 * BuddyPress user status
 *
 * @param $user_id
 *
 */
if ( ! function_exists( 'buddyx_user_status' ) ) {

	function bp_suggestions_user_status( $user_id ) {
		if ( bp_suggestions_is_user_online( $user_id ) ) {
			echo '<span class="member-status online"></span>';
		}
	}
}