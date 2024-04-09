<div class="bffs_swiper_layout_one bffs_swiper swiper">
	<div id="members-list" class="bffs-wrapper item-list members-list swiper-wrapper" aria-live="polite" aria-relevant="all" aria-atomic="true">
			<?php
			while ( bp_members() ) :
				bp_the_member();
				?>
				<div class="bffs-swipe-slides swiper-slide">

					<div <?php bp_member_class( array( 'item-entry' ) ); ?> data-bp-item-id="<?php bp_member_user_id(); ?>" data-bp-item-component="members">
						<div class="list-wrap">
							<div class="bffs_user_layout_one">
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
								<div class="bffs_swipe_layout_bottom">
									<div class="bffs_user_buttons">
										<div class="swipe-cross-button">
											<a class="bffs_remove_user" href="" data-total_mem ="<?php echo esc_attr( count( $matched_members ) );?>" data-max_mem ="<?php echo esc_attr( $max_members ); ?>" data-mem_id="<?php bp_member_user_id(); ?>">
												<svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
													<mask id="mask0_19_529" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0" width="22" height="22">
													<rect width="22" height="22" fill="#D9D9D9"/>
													</mask>
													<g mask="url(#mask0_19_529)">
													<path d="M5.86667 17.4167L4.58333 16.1333L9.71667 11L4.58333 5.86668L5.86667 4.58334L11 9.71668L16.1333 4.58334L17.4167 5.86668L12.2833 11L17.4167 16.1333L16.1333 17.4167L11 12.2833L5.86667 17.4167Z" fill="#1C1B1F"/>
													</g>
												</svg>
											</a>
										</div>
										<?php if ( 'follow' === $settings['suggest'] && bp_is_active( 'follow' ) ) : ?>
											<button class="bffs-follow-button friendship-button" data-total_mem ="<?php echo esc_attr( count( $matched_members ) );?>" data-max_mem ="<?php echo esc_attr( $max_members ); ?>" data-mem_id = "<?php echo esc_attr( $members_template->member->id ); ?>" id="friend-<?php echo esc_attr( $members_template->member->id ); ?>" rel="add" title="Follow" data-bp-btn-action="not_friends">
												<?php esc_html_e( 'Follow', 'buddypress-friend-follow-suggestion' ); ?>
											</button>
											<?php elseif ( 'follow' === $settings['suggest'] ) : ?>
												<?php

												$button_args = wp_parse_args( $button_args, get_class_vars( 'BP_Button' ) );

												if ( function_exists( 'bp_add_follow_button' ) ) {
													bp_add_follow_button( bp_get_member_user_id(), bp_loggedin_user_id(), $button_args );
												}
												?>
											<?php elseif ( bp_is_active( 'friends' ) ) : ?>
													<button class="bffs-friendship-button friendship-button" data-total_mem ="<?php echo esc_attr( count( $matched_members ) );?>" data-max_mem ="<?php echo esc_attr( $max_members ); ?>" data-mem_id = "<?php echo esc_attr( $members_template->member->id );?>" id="friend-<?php echo esc_attr( $members_template->member->id );?>" rel="add" title="Add Friend" data-bp-btn-action="not_friends">
														<?php echo esc_html( 'Add Friend' );?>
													</button>
											<?php endif; ?>
									</div>
									<div class="item-title fn"><a href="<?php bp_member_permalink(); ?>"><?php bp_member_name(); ?></a></div>
									<div class="bffs_user_online"><span class="user_status"></span><?php esc_html_e( 'Online', 'buddypress-friend-follow-suggestion' ); ?></div>
									<ul class="bffs_user_layout_button_list">
										<li class="bffs_actvity_icon">
											<a href="<?php bp_member_permalink(); ?>">
												<svg width="22" height="16" viewBox="0 0 22 16" fill="none" xmlns="http://www.w3.org/2000/svg">
												<path d="M3.72087 15.2791C2.78754 14.3347 2.05213 13.2409 1.51463 11.9979C0.977116 10.7548 0.708359 9.42221 0.708359 7.99999C0.708359 6.56667 0.977116 5.22848 1.51463 3.98543C2.05213 2.74237 2.78754 1.65418 3.72087 0.720856L4.87917 1.87916C4.08472 2.6736 3.4618 3.59443 3.01042 4.64166C2.55903 5.68888 2.33333 6.80832 2.33333 7.99999C2.33333 9.20971 2.55903 10.3382 3.01042 11.3854C3.4618 12.4326 4.08472 13.3444 4.87917 14.1208L3.72087 15.2791ZM6.78128 12.2187C6.24518 11.6715 5.82122 11.0382 5.50942 10.3187C5.1976 9.59929 5.04169 8.82637 5.04169 7.99999C5.04169 7.1625 5.1976 6.38404 5.50942 5.66459C5.82122 4.94515 6.24518 4.31738 6.78128 3.78127L7.93958 4.93957C7.54236 5.33679 7.2309 5.79721 7.00521 6.32082C6.77951 6.84443 6.66667 7.40416 6.66667 7.99999C6.66667 8.59582 6.77951 9.15554 7.00521 9.67916C7.2309 10.2028 7.54236 10.6632 7.93958 11.0604L6.78128 12.2187ZM11 9.62496C10.557 9.62496 10.1754 9.46489 9.85524 9.14475C9.5351 8.82462 9.37503 8.44304 9.37503 7.99999C9.37503 7.55694 9.5351 7.17536 9.85524 6.85523C10.1754 6.53509 10.557 6.37502 11 6.37502C11.443 6.37502 11.8246 6.53509 12.1448 6.85523C12.4649 7.17536 12.625 7.55694 12.625 7.99999C12.625 8.44304 12.4649 8.82462 12.1448 9.14475C11.8246 9.46489 11.443 9.62496 11 9.62496ZM15.2187 12.2187L14.0604 11.0604C14.4576 10.6632 14.7691 10.2028 14.9948 9.67916C15.2205 9.15554 15.3333 8.59582 15.3333 7.99999C15.3333 7.40416 15.2205 6.84443 14.9948 6.32082C14.7691 5.79721 14.4576 5.33679 14.0604 4.93957L15.2187 3.78127C15.7548 4.31738 16.1788 4.94515 16.4906 5.66459C16.8024 6.38404 16.9583 7.1625 16.9583 7.99999C16.9583 8.82637 16.8024 9.59929 16.4906 10.3187C16.1788 11.0382 15.7548 11.6715 15.2187 12.2187ZM18.2791 15.2791L17.1208 14.1208C17.9153 13.3264 18.5382 12.4055 18.9896 11.3583C19.441 10.3111 19.6667 9.19166 19.6667 7.99999C19.6667 6.79722 19.441 5.67222 18.9896 4.625C18.5382 3.57778 17.9153 2.6625 17.1208 1.87916L18.2791 0.720856C19.2125 1.65418 19.9479 2.74237 20.4854 3.98543C21.0229 5.22848 21.2916 6.56667 21.2916 7.99999C21.2916 9.42221 21.0229 10.7548 20.4854 11.9979C19.9479 13.2409 19.2125 14.3347 18.2791 15.2791Z" fill="#FFFFFF"/>
												</svg>
												<?php esc_html_e( 'Discover', 'buddypress-friend-follow-suggestion' ); ?>
											</a>
										</li>
										<?php
										if( ( function_exists( 'buddypress' ) && isset( buddypress()->buddyboss  ) ) ){
											$messgae_url = bp_loggedin_user_domain() . bp_get_messages_slug() . '/compose/?r=' . bp_core_get_username( bp_get_member_user_id() );
										} else {
											$messgae_url = bp_loggedin_user_domain() . bp_get_messages_slug() . '/compose/?r=' . bp_members_get_user_slug( bp_get_member_user_id() );
										}
										?>
										<li class="bffs_msg_icon">
											<a href="<?php echo esc_url( $messgae_url ); ?>">
												<svg width="20" height="22" viewBox="0 0 20 22" fill="none" xmlns="http://www.w3.org/2000/svg">
													<path d="M4.25002 13.75H11.75V12.25H4.25002V13.75ZM4.25002 10.75H15.75V9.25004H4.25002V10.75ZM4.25002 7.74999H15.2211V7.68846C14.7903 7.56153 14.4025 7.37724 14.0577 7.13559C13.7128 6.89392 13.4115 6.59874 13.1538 6.25004H4.25002V7.74999ZM0.500023 21.0384V4.30774C0.500023 3.8026 0.675023 3.37504 1.02502 3.02504C1.37502 2.67504 1.80259 2.50004 2.30772 2.50004H12.35C12.2962 2.75004 12.2676 2.99843 12.2644 3.24521C12.2612 3.49201 12.2769 3.74361 12.3115 4.00001H2.30772C2.23079 4.00001 2.16026 4.03206 2.09615 4.09616C2.03205 4.16028 2 4.2308 2 4.30774V17.3846L3.40002 16H17.6923C17.7692 16 17.8397 15.968 17.9039 15.9039C17.968 15.8397 18 15.7692 18 15.6923V8.12119C18.2872 8.05709 18.5542 7.97504 18.801 7.87504C19.0477 7.77504 19.2807 7.64363 19.5 7.48081V15.6923C19.5 16.1974 19.325 16.625 18.975 16.975C18.625 17.325 18.1974 17.5 17.6923 17.5H4.03847L0.500023 21.0384ZM2 4.30774V16.6924V4.00001V4.30774ZM17 6.23079C16.2372 6.23079 15.5882 5.96316 15.0529 5.42791C14.5177 4.89266 14.25 4.24363 14.25 3.48081C14.25 2.71799 14.5177 2.06896 15.0529 1.53371C15.5882 0.998461 16.2372 0.730835 17 0.730835C17.7628 0.730835 18.4118 0.998461 18.9471 1.53371C19.4823 2.06896 19.75 2.71799 19.75 3.48081C19.75 4.24363 19.4823 4.89266 18.9471 5.42791C18.4118 5.96316 17.7628 6.23079 17 6.23079Z" fill="#A4B3D0"/>
												</svg>
											</a>
										</li>										
										<li class="bffs_user_icon">
											<?php 
											if( function_exists( 'buddypress' ) && isset( buddypress()->buddyboss  ) ) {
												$link = home_url( '/members/' . bp_core_get_username( bp_get_member_user_id() ) . '/profile/' ); 
											} else {
												$link = home_url( '/members/' . bp_members_get_user_slug( bp_get_member_user_id() ) . '/profile/' ); 
											}
											?>
											<a href="<?php echo $link; ?>">
												<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
													<path d="M4.0231 15.2923C4.8731 14.6616 5.79906 14.1635 6.80097 13.7981C7.80289 13.4327 8.86923 13.25 10 13.25C11.1308 13.25 12.1971 13.4327 13.199 13.7981C14.2009 14.1635 15.1269 14.6616 15.9769 15.2923C16.5987 14.609 17.0913 13.818 17.4548 12.9193C17.8183 12.0205 18 11.0474 18 10C18 7.78334 17.2208 5.89584 15.6625 4.33751C14.1042 2.77917 12.2167 2.00001 10 2.00001C7.78333 2.00001 5.89583 2.77917 4.3375 4.33751C2.77916 5.89584 2 7.78334 2 10C2 11.0474 2.18173 12.0205 2.5452 12.9193C2.90866 13.818 3.4013 14.609 4.0231 15.2923ZM10.0003 10.75C9.08728 10.75 8.31731 10.4366 7.6904 9.80991C7.06348 9.18317 6.75002 8.41331 6.75002 7.50031C6.75002 6.58729 7.06338 5.81732 7.6901 5.19041C8.31683 4.56349 9.0867 4.25003 9.9997 4.25003C10.9127 4.25003 11.6827 4.56339 12.3096 5.19011C12.9365 5.81684 13.25 6.58671 13.25 7.49971C13.25 8.41272 12.9366 9.18269 12.3099 9.80961C11.6832 10.4365 10.9133 10.75 10.0003 10.75ZM10 19.5C8.68076 19.5 7.44327 19.2519 6.28752 18.7557C5.13176 18.2596 4.12631 17.5839 3.27117 16.7288C2.41606 15.8737 1.74042 14.8682 1.24427 13.7125C0.748106 12.5567 0.500023 11.3192 0.500023 10C0.500023 8.68077 0.748106 7.44328 1.24427 6.28753C1.74042 5.13176 2.41606 4.12631 3.27117 3.27118C4.12631 2.41606 5.13176 1.74043 6.28752 1.24428C7.44327 0.748114 8.68076 0.500031 10 0.500031C11.3192 0.500031 12.5567 0.748114 13.7125 1.24428C14.8682 1.74043 15.8737 2.41606 16.7288 3.27118C17.5839 4.12631 18.2596 5.13176 18.7557 6.28753C19.2519 7.44328 19.5 8.68077 19.5 10C19.5 11.3192 19.2519 12.5567 18.7557 13.7125C18.2596 14.8682 17.5839 15.8737 16.7288 16.7288C15.8737 17.5839 14.8682 18.2596 13.7125 18.7557C12.5567 19.2519 11.3192 19.5 10 19.5ZM10 18C10.9026 18 11.7728 17.8548 12.6106 17.5644C13.4484 17.2741 14.1923 16.868 14.8423 16.3462C14.1923 15.8436 13.458 15.4519 12.6394 15.1712C11.8208 14.8904 10.941 14.75 10 14.75C9.05896 14.75 8.17755 14.8888 7.35575 15.1663C6.53395 15.4439 5.80126 15.8372 5.15767 16.3462C5.80767 16.868 6.55159 17.2741 7.38942 17.5644C8.22724 17.8548 9.09743 18 10 18ZM10 9.25003C10.4974 9.25003 10.9135 9.08272 11.2481 8.74811C11.5827 8.41347 11.75 7.99744 11.75 7.50001C11.75 7.00257 11.5827 6.58654 11.2481 6.25191C10.9135 5.91729 10.4974 5.74998 10 5.74998C9.50256 5.74998 9.08653 5.91729 8.7519 6.25191C8.41728 6.58654 8.24997 7.00257 8.24997 7.50001C8.24997 7.99744 8.41728 8.41347 8.7519 8.74811C9.08653 9.08272 9.50256 9.25003 10 9.25003Z" fill="#A4B3D0"/>
												</svg>
											</a>
										</li>
									</ul>
								</div>
							</div>
						</div>
					</div>
				</div>
			<?php endwhile; ?>
		</div>
		<!-- If we need navigation buttons -->
 <div class="swiper-button-next"></div>
<div class="swiper-button-prev"></div>
<div class="swiper-pagination"></div>
</div>
