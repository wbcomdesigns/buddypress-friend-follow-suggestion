<div class="bffs_horizontal_layout horizontal_swiper">
	<div id="members-list" class="bffs-wrapper item-list members-list swiper-wrapper" aria-live="polite" aria-relevant="all" aria-atomic="true">
		<?php
		// Pre-calculate or cache any repeated values or checks outside the loop
		$is_follow_active 	= bp_is_active( 'follow' );
		$is_friends_active 	= bp_is_active( 'friends' );
		$suggest_is_follow 	= 'follow' === $settings['suggest'];
		while ( bp_members() ) :
			bp_the_member();
			$member_id 			= bp_get_member_user_id();
			$member_permalink 	= bp_get_member_permalink();
			$member_name 		= bp_get_member_name();
			?>
			<div class="bffs-slide swiper-slide">
				<div <?php bp_member_class( array( 'item-entry' ) ); ?> data-bp-item-id="<?php echo esc_attr($member_id); ?>" data-bp-item-component="members">
					<div class="list-wrap">
						<div class="item-avatar">
							<?php
							// Avatar args can be stored in a variable for clarity and reuse
							$avatar_args = apply_filters(
								'bp_nouveau_avatar_args',
								array(
									'type'   => 'full',
									'width'  => bp_core_avatar_full_width(),
									'height' => bp_core_avatar_full_height(),
								)
							);
							bp_member_avatar( $avatar_args );
							?>
						</div>
						<div class="item">
							<div class="item-title fn">
								<a href="<?php echo esc_url($member_permalink); ?>"><?php echo esc_html( $member_name ); ?></a>
							</div>
							<div class="item-meta">
								<?php 
									if ( $suggest_is_follow && $is_follow_active ) { 										
										bp_follow_add_follow_button( 'leader_id=' . $member_id ); 
									
									} elseif ( $suggest_is_follow ) { 
										$button_args = wp_parse_args( $button_args, get_class_vars( 'BP_Button' ) );
										if ( function_exists( 'bp_add_follow_button' ) ) {
											bp_add_follow_button( $member_id, bp_loggedin_user_id(), $button_args );
										}
									} elseif ( $is_friends_active ) { 
										echo wp_kses_post( bp_get_add_friend_button( $member_id ) );
									
									} ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		<?php endwhile; ?>
	</div>
	<div class="swiper-button-next"></div>
	<div class="swiper-button-prev"></div>
	<div class="swiper-pagination"></div>
</div>
