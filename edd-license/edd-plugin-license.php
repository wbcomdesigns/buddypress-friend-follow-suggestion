<?php
// this is the URL our updater / license checker pings. This should be the URL of the site with EDD installed
define( 'EDD_BFFS_STORE_URL', 'https://wbcomdesigns.com/' ); // you should use your own CONSTANT name, and be sure to replace it throughout this file

// the name of your product. This should match the download name in EDD exactly
// you should use your own CONSTANT name, and be sure to replace it throughout this file
define( 'EDD_BFFS_ITEM_NAME', 'BuddyPress Friend & Follow Suggestion' );

// the name of the settings page for the license input to be displayed
define( 'EDD_BFFS_PLUGIN_LICENSE_PAGE', 'wbcom-license-page' );

if ( ! class_exists( 'EDD_BFFS_Plugin_Updater' ) ) {
	// load our custom updater.
	include dirname( __FILE__ ) . '/EDD_BFFS_Plugin_Updater.php';
}

function edd_BFFS_plugin_updater() {
	// retrieve our license key from the DB.
	$license_key = trim( get_option( 'edd_wbcom_BFFS_license_key' ) );

	// setup the updater
	$edd_updater = new EDD_BFFS_Plugin_Updater(
		EDD_BFFS_STORE_URL,
		BFFS_PLUGIN_FILE,
		array(
			'version'   => BFFS_PLUGIN_VERSION,             // current version number.
			'license'   => $license_key,        // license key (used get_option above to retrieve from DB).
			'item_name' => EDD_BFFS_ITEM_NAME,  // name of this plugin.
			'author'    => 'wbcomdesigns',  // author of this plugin.
			'url'       => home_url(),
		)
	);
}
add_action( 'admin_init', 'edd_BFFS_plugin_updater', 0 );

function edd_wbcom_BFFS_register_option() {
	// creates our settings in the options table
	register_setting( 'edd_wbcom_BFFS_license', 'edd_wbcom_BFFS_license_key', 'edd_BFFS_sanitize_license' );
}
add_action( 'admin_init', 'edd_wbcom_BFFS_register_option' );

function edd_BFFS_sanitize_license( $new ) {
	$old = get_option( 'edd_wbcom_BFFS_license_key' );
	if ( $old && $old != $new ) {
		delete_option( 'edd_wbcom_BFFS_license_status' ); // new license has been entered, so must reactivate
	}
	return $new;
}



/************************************
 * this illustrates how to activate
 * a license key
 *************************************/

function edd_wbcom_BFFS_activate_license() {
	// listen for our activate button to be clicked
	if ( isset( $_POST['edd_BFFS_license_activate'] ) ) {
		// run a quick security check
		if ( ! check_admin_referer( 'edd_wbcom_BFFS_nonce', 'edd_wbcom_BFFS_nonce' ) ) {
			return; // get out if we didn't click the Activate button
		}

		// retrieve the license from the database
		$license = isset( $_POST['edd_wbcom_BFFS_license_key'] ) ? sanitize_text_field( wp_unslash( $_POST['edd_wbcom_BFFS_license_key'] ) ) : '';

		// data to send in our API request
		$api_params = array(
			'edd_action' => 'activate_license',
			'license'    => $license,
			'item_name'  => urlencode( EDD_BFFS_ITEM_NAME ), // the name of our product in EDD
			'url'        => home_url(),
		);

		// Call the custom API.
		$response = wp_remote_post(
			EDD_BFFS_STORE_URL,
			array(
				'timeout'   => 15,
				'sslverify' => false,
				'body'      => $api_params,
			)
		);

		// make sure the response came back okay
		if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
			if ( is_wp_error( $response ) ) {
				$message = $response->get_error_message();
			} else {
				$message = __( 'An error occurred, please try again.', 'buddypress-friend-follow-suggestion' );
			}
		} else {
			$license_data = json_decode( wp_remote_retrieve_body( $response ) );

			if ( false === $license_data->success ) {
				switch ( $license_data->error ) {
					case 'expired':
						$message = sprintf(
							__( 'Your license key expired on %s.', 'buddypress-friend-follow-suggestion' ),
							date_i18n( get_option( 'date_format' ), strtotime( $license_data->expires, current_time( 'timestamp' ) ) )
						);
						break;

					case 'revoked':
						$message = __( 'Your license key has been disabled.', 'buddypress-friend-follow-suggestion' );
						break;

					case 'missing':
						$message = __( 'Invalid license.', 'buddypress-friend-follow-suggestion' );
						break;

					case 'invalid':
					case 'site_inactive':
						$message = __( 'Your license is not active for this URL.', 'buddypress-friend-follow-suggestion' );
						break;

					case 'item_name_mismatch':
						$message = sprintf( __( 'This appears to be an invalid license key for %s.', 'buddypress-friend-follow-suggestion' ), EDD_BFFS_ITEM_NAME );
						break;

					case 'no_activations_left':
						$message = __( 'Your license key has reached its activation limit.', 'buddypress-friend-follow-suggestion' );
						break;

					default:
						$message = __( 'An error occurred, please try again.', 'buddypress-friend-follow-suggestion' );
						break;
				}
			}else {
				set_transient("edd_wbcom_BFFS_license_key_data", $license_data, 12 * HOUR_IN_SECONDS);
			}
		}

		// Check if anything passed on a message constituting a failure
		if ( ! empty( $message ) ) {
			$base_url = admin_url( 'admin.php?page=' . EDD_BFFS_PLUGIN_LICENSE_PAGE );
			$redirect = add_query_arg(
				array(
					'bpgp_activation' => 'false',
					'message'         => urlencode( $message ),
				),
				$base_url
			);
			$license  = trim( $license );
			update_option( 'edd_wbcom_BFFS_license_key', $license );
			update_option( 'edd_wbcom_BFFS_license_status', $license_data->license );
			wp_redirect( $redirect );
			exit();
		}

		// $license_data->license will be either "valid" or "invalid"
		$license = trim( $license );
		update_option( 'edd_wbcom_BFFS_license_key', $license );
		update_option( 'edd_wbcom_BFFS_license_status', $license_data->license );
		wp_redirect( admin_url( 'admin.php?page=' . EDD_BFFS_PLUGIN_LICENSE_PAGE ) );
		exit();
	}
}
add_action( 'admin_init', 'edd_wbcom_BFFS_activate_license' );


/***********************************************
 * Illustrates how to deactivate a license key.
 * This will decrease the site count
 ***********************************************/

function edd_wbcom_BFFS_deactivate_license() {
	// listen for our activate button to be clicked
	if ( isset( $_POST['edd_BFFS_license_deactivate'] ) ) {
		// run a quick security check
		if ( ! check_admin_referer( 'edd_wbcom_BFFS_nonce', 'edd_wbcom_BFFS_nonce' ) ) {
			return; // get out if we didn't click the Activate button
		}

		// retrieve the license from the database
		$license = trim( get_option( 'edd_wbcom_BFFS_license_key' ) );

		// data to send in our API request
		$api_params = array(
			'edd_action' => 'deactivate_license',
			'license'    => $license,
			'item_name'  => urlencode( EDD_BFFS_ITEM_NAME ), // the name of our product in EDD
			'url'        => home_url(),
		);

		// Call the custom API.
		$response = wp_remote_post(
			EDD_BFFS_STORE_URL,
			array(
				'timeout'   => 15,
				'sslverify' => false,
				'body'      => $api_params,
			)
		);

		// make sure the response came back okay
		if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
			if ( is_wp_error( $response ) ) {
				$message = $response->get_error_message();
			} else {
				$message = __( 'An error occurred, please try again.', 'buddypress-friend-follow-suggestion' );
			}

			$base_url = admin_url( 'admin.php?page=' . EDD_BFFS_PLUGIN_LICENSE_PAGE );
			$redirect = add_query_arg(
				array(
					'bpgp_activation' => 'false',
					'message'         => urlencode( $message ),
				),
				$base_url
			);

			wp_redirect( $redirect );
			exit();
		}

		// decode the license data
		$license_data = json_decode( wp_remote_retrieve_body( $response ) );
		delete_transient("edd_wbcom_BFFS_license_key_data");
		
		
		// $license_data->license will be either "deactivated" or "failed"
		if ( $license_data->license == 'deactivated' ) {
			delete_option( 'edd_wbcom_BFFS_license_status' );
		}

		wp_redirect( admin_url( 'admin.php?page=' . EDD_BFFS_PLUGIN_LICENSE_PAGE ) );
		exit();
	}
}
add_action( 'admin_init', 'edd_wbcom_BFFS_deactivate_license' );


/************************************
 * this illustrates how to check if
 * a license key is still valid
 * the updater does this for you,
 * so this is only needed if you
 * want to do something custom
 *************************************/
add_action( 'admin_init', 'edd_wbcom_BFFS_check_license' );
function edd_wbcom_BFFS_check_license() {
	global $wp_version, $pagenow;

	
	if ( $pagenow === 'plugins.php' || $pagenow === 'index.php' || ( isset($_GET['page']) && $_GET['page'] === 'wbcom-license-page') ) {
		
		$license_data = get_transient("edd_wbcom_BFFS_license_key_data");	
		$license = trim( get_option( 'edd_wbcom_BFFS_license_key' ) );
		
		if( empty($license_data) && $license != '' ) {

	

			$api_params = array(
				'edd_action' => 'check_license',
				'license'    => $license,
				'item_name'  => urlencode( EDD_BFFS_ITEM_NAME ),
				'url'        => home_url(),
			);

			// Call the custom API.
			$response = wp_remote_post(
				EDD_BFFS_STORE_URL,
				array(
					'timeout'   => 15,
					'sslverify' => false,
					'body'      => $api_params,
				)
			);

			if ( is_wp_error( $response ) ) {
				return false;
			}

			$license_data = json_decode( wp_remote_retrieve_body( $response ) );

			if(!empty($license_data)) {
				set_transient("edd_wbcom_bprm_license_key_data", $license_data, 12 * HOUR_IN_SECONDS);
			}
		}
	}
}

/**
 * This is a means of catching errors from the activation method above and displaying it to the customer
 */
function edd_wbcom_BFFS_admin_notices() {
	$license_activation = filter_input( INPUT_GET, 'bpgp_activation' ) ? filter_input( INPUT_GET, 'bpgp_activation' ) : '';
	$error_message      = filter_input( INPUT_GET, 'message' ) ? filter_input( INPUT_GET, 'message' ) : '';
	$license_data 		= get_transient("edd_wbcom_BFFS_license_key_data");
	$license 			= trim( get_option( 'edd_wbcom_BFFS_license_key' ) );
	
	if ( isset( $license_activation ) && ! empty( $error_message ) || ( !empty($license_data) && $license_data->license == 'expired' )) {
		if ( $license_activation === '' ) {
			$license_activation = $license_data->license;
		}
		switch ( $license_activation ) {
			case 'expired':
				?>
				<div class="notice notice-error is-dismissible">
				<?php 
				echo $message = sprintf(
							/* translators: %1$s: Expire Time*/
							__( 'Your BuddyPress Friend & Follow Suggestion plugin license key expired on %s.', 'buddypress-friend-follow-suggestion' ),
							date_i18n( get_option( 'date_format' ), strtotime( $license_data->expires, current_time( 'timestamp' ) ) )
						);
				?>
				</div>
				<?php
						break;
			break;
			case 'false':
				$message = urldecode( $error_message );
				?>
				<div class="error">
					<p><?php echo esc_html( $message ); ?></p>
				</div>
				<?php

				break;

			case 'true':
			default:
				// Developers can put a custom success message here for when activation is successful if they way.
				break;
		}
	}
	
	if ( $license === '' ) {
		?>
		<div class="notice notice-error is-dismissible">
			<p>
			<?php 
			echo esc_html__( 'Please activate your BuddyPress Friend & Follow Suggestion plugin license key.', 'buddypress-friend-follow-suggestion' );
			?>
			</p>			
		</div>
		<?php
	}	
}
add_action( 'admin_notices', 'edd_wbcom_BFFS_admin_notices' );


add_action( 'wbcom_add_plugin_license_code', 'wbcom_BFFS_render_license_section' );
function wbcom_BFFS_render_license_section() {

	$license = get_option( 'edd_wbcom_BFFS_license_key', true );
	$status  = get_option( 'edd_wbcom_BFFS_license_status' );

	$plugin_data = get_plugin_data( BFFS_PLUGIN_PATH . '/buddypress-friend-follow-suggestion.php', $markup = true, $translate = true );

	$license_data 		= get_transient("edd_wbcom_BFFS_license_key_data");
	
	if ( false !== $status && 'valid' === $status  && !empty($license_data) && $license_data->license == 'valid') {
		$status_class = 'active';
		$status_text  = 'Active';
	} else if ( !empty($license_data) && $license_data->license != '' ) {
		$status_class = 'expired';
		$status_text  = $license_data->license;
	}else {
		$status_class = 'inactive';
		$status_text  = 'Inactive';
	}
	?>
	<table class="form-table wb-license-form-table mobile-license-headings">
		<thead>
			<tr>
				<th class="wb-product-th"><?php esc_html_e( 'Product', 'buddypress-friend-follow-suggestion' ); ?></th>
				<th class="wb-version-th"><?php esc_html_e( 'Version', 'buddypress-friend-follow-suggestion' ); ?></th>
				<th class="wb-key-th"><?php esc_html_e( 'Key', 'buddypress-friend-follow-suggestion' ); ?></th>
				<th class="wb-status-th"><?php esc_html_e( 'Status', 'buddypress-friend-follow-suggestion' ); ?></th>
				<th class="wb-action-th"><?php esc_html_e( 'Action', 'buddypress-friend-follow-suggestion' ); ?></th>
			</tr>
		</thead>
	</table>
	<form method="post" action="options.php">
		<?php settings_fields( 'edd_wbcom_BFFS_license' ); ?>
		<table class="form-table wb-license-form-table">
			<tr>
				<td class="wb-plugin-name"><?php esc_attr_e( $plugin_data['Name'], 'buddypress-friend-follow-suggestion' ); ?></td>
				<td class="wb-plugin-version"><?php esc_attr_e( $plugin_data['Version'], 'buddypress-friend-follow-suggestion' ); ?></td>
				<td class="wb-plugin-license-key"><input id="edd_wbcom_BFFS_license_key" name="edd_wbcom_BFFS_license_key" type="text" class="regular-text" value="<?php esc_attr_e( $license, 'buddypress-friend-follow-suggestion' ); ?>" /></td>
				<td class="wb-license-status <?php echo esc_attr( $status_class ); ?>"><?php echo esc_html( $status_text ); ?></td>
				<td class="wb-license-action">
					<?php
					if ( $status !== false && $status == 'valid' ) {
						wp_nonce_field( 'edd_wbcom_BFFS_nonce', 'edd_wbcom_BFFS_nonce' );
						?>
						 <input type="submit" class="button-secondary" name="edd_BFFS_license_deactivate" value="<?php esc_attr_e( 'Deactivate License', 'buddypress-friend-follow-suggestion' ); ?>"/>
						<?php
					} else {
						wp_nonce_field( 'edd_wbcom_BFFS_nonce', 'edd_wbcom_BFFS_nonce' );
						?>
						 <input type="submit" class="button-secondary" name="edd_BFFS_license_activate" value="<?php esc_attr_e( 'Activate License', 'buddypress-friend-follow-suggestion' ); ?>"/>
					<?php } ?>
				</td>
			</tr>
		</table>
	</form>
	<?php
}

function edd_wbcom_BFFS_activate_license_button() {
	// listen for our activate button to be clicked
	if ( isset( $_POST['edd_BFFS_license_activate'] ) ) {
		// run a quick security check
		if ( ! check_admin_referer( 'edd_wbcom_BFFS_nonce', 'edd_wbcom_BFFS_nonce' ) ) {
			return; // get out if we didn't click the Activate button
		}

		// retrieve the license from the database
		$license = ! empty( $_POST['edd_wbcom_BFFS_license_key'] ) ? trim( sanitize_text_field( wp_unslash( $_POST['edd_wbcom_BFFS_license_key'] ) ) ) : '';

		// data to send in our API request
		$api_params = array(
			'edd_action' => 'activate_license',
			'license'    => $license,
			'item_name'  => urlencode( EDD_BFFS_ITEM_NAME ), // the name of our product in EDD
			'url'        => home_url(),
		);

		// Call the custom API.
		$response = wp_remote_post(
			EDD_BFFS_STORE_URL,
			array(
				'timeout'   => 15,
				'sslverify' => false,
				'body'      => $api_params,
			)
		);

		// make sure the response came back okay
		if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
			if ( is_wp_error( $response ) ) {
				$message = $response->get_error_message();
			} else {
				$message = __( 'An error occurred, please try again.', 'buddypress-friend-follow-suggestion' );
			}
		} else {
			$license_data = json_decode( wp_remote_retrieve_body( $response ) );

			if ( false === $license_data->success ) {
				switch ( $license_data->error ) {
					case 'expired':
						$message = sprintf(
							__( 'Your license key expired on %s.', 'buddypress-friend-follow-suggestion' ),
							date_i18n( get_option( 'date_format' ), strtotime( $license_data->expires, current_time( 'timestamp' ) ) )
						);
						break;

					case 'revoked':
						$message = __( 'Your license key has been disabled.', 'buddypress-friend-follow-suggestion' );
						break;

					case 'missing':
						$message = __( 'Invalid license.', 'buddypress-friend-follow-suggestion' );
						break;

					case 'invalid':
					case 'site_inactive':
						$message = __( 'Your license is not active for this URL.', 'buddypress-friend-follow-suggestion' );
						break;

					case 'item_name_mismatch':
						$message = sprintf( __( 'This appears to be an invalid license key for %s.', 'buddypress-friend-follow-suggestion' ), EDD_BFFS_ITEM_NAME );
						break;

					case 'no_activations_left':
						$message = __( 'Your license key has reached its activation limit.', 'buddypress-friend-follow-suggestion' );
						break;

					default:
						$message = __( 'An error occurred, please try again.', 'buddypress-friend-follow-suggestion' );
						break;
				}
			}
		}

		// Check if anything passed on a message constituting a failure
		if ( ! empty( $message ) ) {
			$base_url = admin_url( 'admin.php?page=' . EDD_BFFS_PLUGIN_LICENSE_PAGE );
			$redirect = add_query_arg(
				array(
					'bpgp_activation' => 'false',
					'message'         => urlencode( $message ),
				),
				$base_url
			);
			$license  = trim( $license );
			update_option( 'edd_wbcom_BFFS_license_key', $license );
			update_option( 'edd_wbcom_BFFS_license_status', $license_data->license );
			wp_redirect( $redirect );
			exit();
		}

		// $license_data->license will be either "valid" or "invalid"
		$license = trim( $license );
		update_option( 'edd_wbcom_BFFS_license_key', $license );
		update_option( 'edd_wbcom_BFFS_license_status', $license_data->license );
		wp_redirect( admin_url( 'admin.php?page=' . EDD_BFFS_PLUGIN_LICENSE_PAGE ) );
		exit();
	}
}
add_action( 'admin_init', 'edd_wbcom_BFFS_activate_license_button' );
