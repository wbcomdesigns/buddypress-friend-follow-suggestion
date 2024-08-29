<?php


if ( ! function_exists( 'bp_suggestions_get_matched_users' ) ) {
	/**
	 * Get matched members
	 *
	 * @param  int    $user_id             User whose match to find.
	 * @param  int    $max_members         Maximum number of matched users.
	 * @param  int    $percentage_criteria Percentage criteria to get the matched users.
	 * @param  string $suggest             Type of suggestion ('friends', 'follow', etc.).
	 *
	 * @return array Matched members' IDs.
	 */
	function bp_suggestions_get_matched_users( $user_id, $max_members, $percentage_criteria, $suggest = '' ) {
		$matched_members = array();
		if ( empty( $user_id ) ) {
			return $matched_members; // Return early if no user ID is provided.
		}

		global $wpdb;

		$exclude_user = array();

		// Caching exclusion list to reduce repeated DB hits.
		$cache_key = "exclude_user_{$user_id}_{$suggest}";
		$exclude_user = wp_cache_get( $cache_key );
		
		/*
		 * Exclude rejected members from the exclude users
		 */
		$rejected_members = get_transient( 'rejected_members_' . $user_id );		
		if( is_array($exclude_user) && is_array($rejected_members) ) {			
			$exclude_user = array_merge( $exclude_user, $rejected_members);
		}
		if( !is_array($exclude_user) && is_array($rejected_members)) {
			$exclude_user = $rejected_members;
		}
		
		
		if ( false === $exclude_user ) {
			$exclude_user = array();
			$is_confirmed = 0;

			// Exclude friends or followers based on suggestion type.
			if ( 'friends' === $suggest ) {
				$sql = $wpdb->prepare(
					"SELECT `friend_user_id` FROM {$wpdb->prefix}bp_friends WHERE initiator_user_id = %d AND `is_confirmed` = %d",
					$user_id, $is_confirmed
				);
				$exclude_user = $wpdb->get_col( $sql );
			} elseif ( 'follow' === $suggest ) {
				$sql = $wpdb->prepare(
					"SELECT `leader_id` FROM {$wpdb->prefix}bp_follow WHERE `follower_id` = %d",
					$user_id
				);
				$exclude_user = $wpdb->get_col( $sql );
			}

			// Merge with swiped users to exclude.
			$swiped_users = get_user_meta( $user_id, 'swiped', true );
			if ( ! empty( $swiped_users ) ) {
				$exclude_user = array_merge( $exclude_user, (array) $swiped_users );
			}

			// Cache the exclusion list for 5 minutes.
			wp_cache_set( $cache_key, $exclude_user, '', 300 );
		}

		$bffs_general_setting = get_option( 'bffs_general_setting' );
		$matche_obj = new Buddypress_Friend_Follow_Suggestion_Public( 'buddypress-friend-follow-suggestion', BFFS_PLUGIN_VERSION );

		$max_members = ! empty( $max_members ) ? $max_members : apply_filters( 'bp_suggestion_max_members', 5 );
		$percentage_criteria = ! empty( $percentage_criteria ) ? $percentage_criteria : apply_filters( 'bp_suggestion_critaria', 10 );

		// Fetch users excluding the specified ones.
		$exclude_user[] = $user_id;
		$users = get_users(
			array(
				'exclude' => $exclude_user,
				'number'  => $max_members,
				'fields'  => array( 'ID' ), // Fetch only necessary fields to reduce memory usage.
			)
		);

		foreach ( $users as $user ) {
			if ( $user_id !== $user->ID ) {
				$matche_score = $matche_obj->buddypress_friend_follow_compatibility_score( $user_id, $user->ID );
				if ( $percentage_criteria <= $matche_score ) {
					// Check relationship status based on suggestion type.
					if ( 'friends' === $suggest && function_exists( 'bp_is_friend' ) ) {
						$is_friend = bp_is_friend( $user->ID );
						if ( 'not_friends' === $is_friend ) {
							$matched_members[] = $user->ID;
						}
					} elseif ( 'follow' === $suggest && function_exists( 'bp_is_following' ) ) {
						$is_following = bp_is_following(
							array(
								'leader_id'   => $user->ID,
								'follower_id' => $user_id,
							)
						);
						if ( 0 === $is_following ) {
							$matched_members[] = $user->ID;
						}
					} else {
						$matched_members[] = $user->ID;
					}
				}
			}
		}

		return apply_filters( 'bffs_remove_specific_role_from_suggestion_widget', $matched_members );
	}
}



if ( ! function_exists( 'bp_suggestions_get_compose_message_url' ) ) {

    /**
     * Get the URL to compose a new BuddyPress message.
     *
     * @return string|false The compose message URL or false if messaging is inactive.
     */
    function bp_suggestions_get_compose_message_url() {
        // Check if the messaging component is active.
        if ( ! bp_is_active( 'messages' ) ) {
            return false; // Return false if messaging is not active.
        }

        // Construct the compose message URL.
        $message_url = bp_loggedin_user_domain() . bp_get_messages_slug() . '/compose/?r=';

        // Allow customization of the message URL.
        return apply_filters( 'bp_suggestions_compose_message_url', $message_url );
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
		// Check if BuddyPress is active and the function is available
		if ( ! function_exists( 'bp_get_user_last_activity' ) ) {
			return false;
		}

		// Validate the user ID
		if ( empty( $user_id ) || ! is_numeric( $user_id ) ) {
			return false;
		}

		// Attempt to get the last activity time (cached if possible)
		$last_activity = wp_cache_get( "bp_last_activity_{$user_id}" );
		if ( false === $last_activity ) {
			$last_activity = bp_get_user_last_activity( $user_id );
			wp_cache_set( "bp_last_activity_{$user_id}", $last_activity, '', 300 ); // Cache for 5 minutes
		}

		// If no activity found, the user is considered offline
		if ( empty( $last_activity ) ) {
			return false;
		}

		// Convert the last activity to a timestamp
		$last_activity_timestamp = strtotime( $last_activity );
		if ( false === $last_activity_timestamp ) {
			return false;
		}

		// Calculate the threshold for online status (5 minutes)
		$activity_threshold = time() - ( 5 * MINUTE_IN_SECONDS );

		// Compare the last activity timestamp with the threshold
		return $last_activity_timestamp >= $activity_threshold;
	}
}


/*
 * BuddyPress user status
 *
 * @param $user_id
 *
 */
if ( ! function_exists( 'bp_suggestions_user_status' ) ) {

    /**
     * Display BuddyPress user status.
     *
     * @param int $user_id The ID of the user whose status we want to display.
     * @return string The HTML output for the user's status.
     */
    function bp_suggestions_user_status( $user_id ) {
        // Validate user ID and check if the user is online.
        if ( empty( $user_id ) || ! bp_suggestions_is_user_online( $user_id ) ) {
            return ''; // Return an empty string if the user ID is invalid or the user is offline.
        }

        // Generate the status HTML (this can be customized).
        $status_html = '<span class="member-status online"></span>';

        // Allow developers to modify the status HTML via a filter.
        return apply_filters( 'bp_suggestions_user_status_html', $status_html, $user_id );
    }
}
