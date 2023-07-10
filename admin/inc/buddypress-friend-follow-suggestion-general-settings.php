<?php
/**
 *
 * This file is used for rendering and saving plugin welcome settings.
 *
 * @package Buddypress_Friend_Follow_Suggestion
 */

if ( is_multisite() && is_plugin_active_for_network( plugin_basename( BFFS_PLUGIN_FILE ) ) ) {
	$bffs_general_setting = get_site_option( 'bffs_general_setting' );
} elseif ( is_multisite() ) {
	$bffs_general_setting = get_site_option( 'bffs_general_setting' );
} else {
	$bffs_general_setting = get_option( 'bffs_general_setting' );
}


?>
<div class="wbcom-tab-content">
	<div class="bffs-gen-settings-wrap">
		<div class="bffs-gen-settings-container">
			<div class="wbcom-wrapper-admin">
			<div class="wbcom-admin-title-section">
				<h3 style="margin-bottom:10px;"><?php esc_html_e( 'BuddyPress Profile matching', 'buddypress-friend-follow-suggestion' ); ?></h3>
				<p class="description description-title"><?php esc_html_e( 'Here you can customize Buddypress profiles matching functionality.', 'buddypress-friend-follow-suggestion' ); ?></p>
			</div>			
			<div class="form-table buddypress-friend-follow wbcom-admin-option-wrap">						
			<form method="post" action="options.php">
				<?php
				settings_fields( 'bffs_admin_general_options' );
					do_settings_sections( 'bffs_admin_general_options' );
				?>
				<div class="form-table">
					<div class="wbcom-settings-section-wrap">
						<div class="wbcom-settings-section-options-heading">
							<label for="bprm_enable_profile_match"><?php esc_html_e( 'Profile matching settings', 'buddypress-friend-follow-suggestion' ); ?></label>
							<p class="description"><?php esc_html_e( 'Enable this option if you want display member matching on member profile page. ', 'buddypress-friend-follow-suggestion' ); ?></p>
						</div>
						<div class="wbcom-settings-section-options">
							<input type='checkbox' id="bprm_enable_profile_match" name='bffs_general_setting[enable_profile_match]'  class="regular-text" value='1' 
							<?php
							if ( isset( $bffs_general_setting['enable_profile_match'] ) && 1 == $bffs_general_setting['enable_profile_match'] ) :
								?>
								checked <?php endif; ?> />
						</div>	
					</div>
					<div class="wbcom-settings-section-wrap" id="profile_st_percentage" 
					<?php
					if ( ! isset( $bffs_general_setting['enable_profile_match'] ) || '' === $bffs_general_setting['enable_profile_match'] ) :
						?>
						style="display:none;"<?php endif; ?>>
						<div class="wbcom-settings-section-options-heading">
							<label><?php esc_html_e( 'Starting percentage', 'buddypress-friend-follow-suggestion' ); ?></label>
							<p class="description"><?php esc_html_e( 'Percentage will start from this value.', 'buddypress-friend-follow-suggestion' ); ?></p>
						</div>
						<div class="wbcom-settings-section-options">
							<input type='text' id="bprm_profile_start_percentage" name='bffs_general_setting[profile_st_percentage]'  class="regular-text" value='<?php echo ( isset( $bffs_general_setting['profile_st_percentage'] ) ) ? esc_attr( $bffs_general_setting['profile_st_percentage'] ) : ''; ?>' />							
						</div>
					</div>

				<div id="bffs-profile-match-fields" class="bffs-profile-match-fields wbcom-bp-row-2settings" 
				<?php
				if ( ! isset( $bffs_general_setting['enable_profile_match'] ) || '' === $bffs_general_setting['enable_profile_match'] ) :
					?>
					style="display:none;"<?php endif; ?>>
					<div class="bffs-field-header">
						<span class="bffs-col1">&nbsp;</span>
						<span class="bffs-col2"><strong><?php esc_html_e( 'Profile Field', 'buddypress-friend-follow-suggestion' ); ?></strong></span>
						<span class="bffs-col3"><strong><?php esc_html_e( 'Percentage', 'buddypress-friend-follow-suggestion' ); ?></strong></span>
						<span class="bffs-col4"><strong><?php esc_html_e( 'Stop if no match ', 'buddypress-friend-follow-suggestion' ); ?></strong></span>						
						<span class="bffs-col5">&nbsp;</span>
					</div>

					<div id="bffs-field-content" class="bffs-field-content">
						<?php if ( ! empty( $bffs_general_setting['bffs_match_data'] ) ) : ?>
							<?php
							$j = 0;
							foreach ( $bffs_general_setting['bffs_match_data'] as $bffs_match_data ) :
								$stop_match = ( isset( $bffs_match_data['stop_match'] ) ) ? $bffs_match_data['stop_match'] : 0;
								?>
								<div class="search_field">
									<span class="bffs-col1">&nbsp;&#x21C5;</span>
									<span class="bffs-col2">
										<?php echo bffs_profile_fields_dropdown( $bffs_match_data['field_id'], $j ); //phpcs:ignore?>
									</span>
									<span class="bffs-col3">
										<input type="text" class="bffs-input bffs-match-percentage" placeholder="Percentage" name="bffs_general_setting[bffs_match_data][<?php echo esc_attr( $j ); ?>][percentage]" value="<?php echo esc_attr( $bffs_match_data['percentage'] ); ?>">
									</span>
									<span class="bffs-col4">
										<input class="bffs-check bffs-match-stop-match" type="checkbox" name="bffs_general_setting[bffs_match_data][<?php echo esc_attr( $j ); ?>][stop_match]" value='1' <?php checked( $stop_match, 1 ); ?>>
									</span>
									<span class="bffs-col5"><a href="javascript:void(0)" class="delete_bffs_field"><?php esc_html_e( 'Delete', 'buddypress-friend-follow-suggestion' ); ?></a></span>									
								</div>
								<?php
								$j++;
							endforeach;
							?>
						<?php endif; ?>
					</div>

					<div class="bffs-add-field">
						<a href="javascript:void(0)" id="add-bffs-match-field" class="add-bffs-match-field" ><?php esc_html_e( 'Add Field', 'buddypress-friend-follow-suggestion' ); ?></a>
					</div>

				</div>
				<?php submit_button(); ?>
			</form>
		</div>
		</div>
		</div>
	</div>
</div>
