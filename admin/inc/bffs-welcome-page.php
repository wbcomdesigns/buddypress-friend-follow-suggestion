<?php
/**
 *
 * This file is used for rendering and saving plugin welcome settings.
 *
 * @package Buddypress_Friend_Follow_Suggestion
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
	// Exit if accessed directly.
}
?>
<div class="wbcom-tab-content">
	<div class="wbcom-welcome-main-wrapper">
		<div class="wbcom-welcome-head">
			<p class="wbcom-welcome-description"><?php esc_html_e( 'BuddyPress Friends and Follow Suggestions plugin improves your BuddyPress community by displaying profile matching percentages in the member header area.', 'buddypress-friend-follow-suggestion' ); ?><br>
			<?php
			$active_plugins = get_option( 'active_plugins' );
			if ( ! in_array( 'buddypress-followers/loader.php', $active_plugins ) ) {
				?>
			<a href="https://github.com/r-a-y/buddypress-followers/archive/refs/heads/master.zip" class="download-link-wb"><?php esc_html_e( 'Install BuddyPress Follow plugin', 'buddypress-friend-follow-suggestion' ); ?></a>
			<?php } ?>
			</p>
		</div><!-- .wbcom-welcome-head -->

		<div class="wbcom-welcome-content">

			<div class="wbcom-welcome-support-info">
				<h3><?php esc_html_e( 'Help &amp; Support Resources', 'buddypress-friend-follow-suggestion' ); ?></h3>
				<p><?php esc_html_e( 'Here are all the resources you may need to get help from us. Documentation is usually the best place to start. Should you require help anytime, our customer care team is available to assist you at the support center.', 'buddypress-friend-follow-suggestion' ); ?></p>

				<div class="wbcom-support-info-wrap">
					<div class="support-info-wrap-inner">
						<div class="wb-section-top">
							<div class="wbcom-support-info-widgets">
								<div class="wbcom-support-inner">
								<h3><span class="dashicons dashicons-book"></span><?php esc_html_e( 'Documentation', 'buddypress-friend-follow-suggestion' ); ?></h3>
								<p><?php esc_html_e( 'We have prepared an extensive guide on BuddyPress Friend & Follow Suggestion to learn all aspects of the plugin. You will find most of your answers here.', 'buddypress-friend-follow-suggestion' ); ?></p>
								<a href="<?php echo esc_url( 'https://wbcomdesigns.com/docs/buddypress-paid-addons/buddypress-friend-follow-suggestion/' ); ?>" class="button button-primary button-welcome-support" target="_blank"><?php esc_html_e( 'Read Documentation', 'buddypress-friend-follow-suggestion' ); ?></a>
								</div>
							</div>
							<div class="wbcom-support-info-widgets">
								<div class="wbcom-support-inner">
									<h3><span class="dashicons dashicons-sos"></span><?php esc_html_e( 'Support Center', 'buddypress-friend-follow-suggestion' ); ?></h3>
									<p><?php esc_html_e( 'We strive to offer the best customer care via our support center. Once your theme is activated, you can ask us for help anytime.', 'buddypress-friend-follow-suggestion' ); ?></p>
									<a href="<?php echo esc_url( 'https://wbcomdesigns.com/support/' ); ?>" class="button button-primary button-welcome-support" target="_blank"><?php esc_html_e( 'Get Support', 'buddypress-friend-follow-suggestion' ); ?></a>
								</div>
							</div>
						</div>
						<div class="wbcom-support-info-widgets feedback-wrapp">
							<div class="wbcom-support-inner">
							<h3><span class="dashicons dashicons-admin-comments"></span><?php esc_html_e( 'Got Feedback?', 'buddypress-friend-follow-suggestion' ); ?></h3>
							<p><?php esc_html_e( 'We want to hear about your experience with the plugin. We would also love to hear any suggestions you may for future updates.', 'buddypress-friend-follow-suggestion' ); ?></p>
							<a href="<?php echo esc_url( 'https://wbcomdesigns.com/submit-review/' ); ?>" class="button button-primary button-welcome-support" target="_blank"><?php esc_html_e( 'Send Feedback', 'buddypress-friend-follow-suggestion' ); ?></a>
						</div>
						</div>
					</div>

					<div class="support-video">
						<iframe width="100%" height="450" src="https://www.youtube.com/embed/cZ8GCiwqI90" title="Friends and Follow Suggestion - BuddyPress Addon to Provide profile matching features to your users" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
					</div>

				</div>

			</div>
		</div>
	</div><!-- .wbcom-welcome-main-wrapper -->
</div><!-- .wbcom-tab-content -->
