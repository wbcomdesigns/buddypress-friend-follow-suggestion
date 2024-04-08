<div class="bffs_horizontal_layout horizontal_swiper">
<div id="members-list" class="bffs-wrapper item-list members-list swiper-wrapper" aria-live="polite" aria-relevant="all" aria-atomic="true">
			<?php
			while ( bp_members() ) :
				bp_the_member();
				?>
				<div class="bffs-slide swiper-slide">
				<div <?php bp_member_class( array( 'item-entry' ) ); ?> data-bp-item-id="<?php bp_member_user_id(); ?>" data-bp-item-component="members">
					<div class="list-wrap">
						<div class="item-avatar">
							<?php
								bp_member_avatar(
									apply_filters(
										'bp_nouveau_avatar_args',
										array(
											'type'   => 'full',
											'width'  => bp_core_avatar_full_width(),
											'height' => bp_core_avatar_full_height(),
										)
									)
								);
							?>
						</div>
						<div class="item">
							<div class="item-title fn"><a href="<?php bp_member_permalink(); ?>"><?php bp_member_name(); ?></a></div>
							<div class="item-meta">
										<?php if ( 'follow' === $settings['suggest'] && bp_is_active( 'follow' ) ) : ?>
											<?php bp_follow_add_follow_button( 'leader_id=' . $members_template->member->id ); ?>
										<?php elseif ( 'follow' === $settings['suggest'] ) : ?>
											<?php
											$button_args = wp_parse_args( $button_args, get_class_vars( 'BP_Button' ) );

											if ( function_exists( 'bp_add_follow_button' ) ) {
												bp_add_follow_button( bp_get_member_user_id(), bp_loggedin_user_id(), $button_args );
											}
											?>
										<?php elseif ( bp_is_active( 'friends' ) ) : ?>
											<?php
											echo wp_kses_post( bp_get_add_friend_button( bp_get_member_user_id() ) );
											?>
										<?php endif; ?>
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
