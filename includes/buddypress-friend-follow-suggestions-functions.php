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
		if ( ! empty( $user_id ) ) {
			global $wpdb;
			
			$exclude_user = array();
			if ( $suggest == 'friends' ) {
				$sql = "select friend_user_id from {$wpdb->prefix}bp_friends where initiator_user_id = {$user_id}";
				$exclude_user = $wpdb->get_col($sql);
			} else if ($suggest == 'follow' ) {
				$sql = "select leader_id from {$wpdb->prefix}bp_follow where follower_id = {$user_id}";
				$exclude_user = $wpdb->get_col($sql);
			}
			
			$bffs_general_setting = get_option( 'bffs_general_setting' );
			$matche_obj           = new Buddypress_Friend_Follow_Suggestion_Public( 'buddypress-friend-follow-suggestion', BUDDYPRESS_FRIEND_FOLLOW_SUGGESTION_VERSION );
			$max_members          = ! empty( $max_members ) ? $max_members : apply_filters( 'bp_suggestion_max_members', 5 );
			
			$users                = get_users( array( 'exclude'=> $exclude_user) );
			
			$match_data           = ! empty( $bffs_general_setting['bffs_match_data'] ) ? $bffs_general_setting['bffs_match_data'] : '';
			$percentage_criteria  = ! empty( $percentage_criteria ) ? $percentage_criteria : apply_filters( 'bp_suggestion_critaria', 10 );
			$matched_members      = array();			
			foreach ( $users as $key => $user ) {
				if ( $user_id !== $user->ID ) {
					$matche_score = $matche_obj->buddypress_friend_follow_compatibility_score( $user_id, $user->ID );
					if ( $percentage_criteria <= $matche_score ) {
						if ( $suggest == 'friends' && function_exists( 'bp_is_friend' ) ) {
						
							$is_friend = bp_is_friend( $user->ID );
							if ( $is_friend == 'not_friends' ) {
								$matched_members[] = $user->ID;
							}
						}else if ( $suggest == 'follow') {
							if ( function_exists( 'bp_add_follow_button' ) ) {
								$is_following = bp_is_following(
														array(
															'leader_id'   => $user->ID,
															'follower_id' => $user_id,
														)
													);
								if ( $is_following == 0 ) {
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
								if ( $is_following == 0 ) {
									$matched_members[] = $user->ID;
								} 
							}
						} else {
							$matched_members[] = $user->ID;
						}
					}
				}
			}
			
			return $matched_members;
		}
	}
}
