<ul id="members-list" class="item-list members-list" aria-live="polite" aria-relevant="all" aria-atomic="true">
			<?php
			while ( bp_members() ) :
				bp_the_member();
				
				$member_id 			= bp_get_member_user_id();
				$member_permalink 	= bp_get_member_permalink();
				$member_name 		= bp_get_member_name();
				?>
				<li <?php bp_member_class( array( 'item-entry' ) ); ?> data-bp-item-id="<?php echo esc_attr( $member_id ); ?>" data-bp-item-component="members">
					<div class="list-wrap">
						<div class="item-avatar">
							<a href="<?php echo esc_url( $member_permalink ); ?>" class="bp-tooltip" data-bp-tooltip="<?php echo esc_attr( $member_name ); ?>"><?php bp_member_avatar(); ?></a>
						</div>
						<div class="item">
							<div class="item-title fn"><a href="<?php echo esc_url( $member_permalink ); ?>"><?php echo esc_html( $member_name ); ?></a></div>
							<div class="item-meta">
								<ul>
									<li>
										<?php if ( 'follow' === $settings['suggest'] && bp_is_active( 'follow' ) ) : ?>
											<?php bp_follow_add_follow_button( 'leader_id=' . $members_template->member->id ); ?>
										<?php elseif ( 'follow' === $settings['suggest'] ) : ?>
											<?php

											$button_args = wp_parse_args( $button_args, get_class_vars( 'BP_Button' ) );

											if ( function_exists( 'bp_add_follow_button' ) ) {
												bp_add_follow_button( $member_id , bp_loggedin_user_id(), $button_args );
											}
											?>
										<?php elseif ( bp_is_active( 'friends' ) ) : ?>
											<?php
											echo wp_kses_post( bp_get_add_friend_button( $member_id ) );
											?>
										<?php endif; ?>
									</li>
								</ul>
							</div>
						</div>
					</div>
				</li>
			<?php endwhile; ?>
		</ul>