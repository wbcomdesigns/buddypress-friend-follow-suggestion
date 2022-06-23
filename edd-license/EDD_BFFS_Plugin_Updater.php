<?php
/**
 * EDD plugin updater
 *
 * @package Buddypress_Friend_Follow_Suggestion
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Allows plugins to use their own update API.
 *
 * @author Easy Digital Downloads
 * @version 1.6.15
 */
class EDD_BFFS_Plugin_Updater {

	/**
	 * Api_url
	 *
	 * @var api_url $api_url api_url.
	 */
	private $api_url = '';

	/**
	 * Api_data
	 *
	 * @var api_data $api_data api_data.
	 */
	private $api_data = array();

	/**
	 * Name
	 *
	 * @var name $name name.
	 */
	private $name = '';

	/**
	 * Slug
	 *
	 * @var slug $slug slug.
	 */
	private $slug = '';

	/**
	 * Version
	 *
	 * @var version $version version.
	 */
	private $version = '';

	/**
	 * Wp_override
	 *
	 * @var wp_override $wp_override wp_override.
	 */
	private $wp_override = false;

	/**
	 * Cache_key
	 *
	 * @var cache_key $cache_key cache_key.
	 */
	private $cache_key = '';

	/**
	 * Class constructor.
	 *
	 * @uses plugin_basename()
	 * @uses hook()
	 *
	 * @param string $_api_url     The URL pointing to the custom API endpoint.
	 * @param string $_plugin_file Path to the plugin file.
	 * @param array  $_api_data    Optional data to send with API calls.
	 */
	public function __construct( $_api_url, $_plugin_file, $_api_data = null ) {
		global $edd_plugin_data;

		$this->api_url     = trailingslashit( $_api_url );
		$this->api_data    = $_api_data;
		$this->name        = plugin_basename( $_plugin_file );
		$this->slug        = basename( $_plugin_file, '.php' );
		$this->version     = $_api_data['version'];
		$this->wp_override = isset( $_api_data['wp_override'] ) ? (bool) $_api_data['wp_override'] : false;
		$this->beta        = ! empty( $this->api_data['beta'] ) ? true : false;
		$this->cache_key   = 'edd_sl_' . md5( serialize( $this->slug . $this->api_data['license'] . $this->beta ) );

		$edd_plugin_data[ $this->slug ] = $this->api_data;

		// Set up hooks.
		$this->init();
	}

	/**
	 * Set up WordPress filters to hook into WP's update process.
	 *
	 * @uses add_filter()
	 *
	 * @return void
	 */
	public function init() {
		add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'check_update' ) );
		add_filter( 'plugins_api', array( $this, 'plugins_api_filter' ), 10, 3 );
		remove_action( 'after_plugin_row_' . $this->name, 'wp_plugin_update_row', 10 );
		add_action( 'after_plugin_row_' . $this->name, array( $this, 'show_update_notification' ), 10, 2 );
		add_action( 'admin_init', array( $this, 'show_changelog' ) );
	}

	/**
	 * Check for Updates at the defined API endpoint and modify the update array.
	 *
	 * This function dives into the update API just when WordPress creates its update array,
	 * then adds a custom API call and injects the custom plugin data retrieved from the API.
	 * It is reassembled from parts of the native WordPress plugin update code.
	 * See wp-includes/update.php line 121 for the original wp_update_plugins() function.
	 *
	 * @uses api_request()
	 *
	 * @param array $_transient_data Update array build by WordPress.
	 * @return array Modified update array with custom plugin data.
	 */
	public function check_update( $_transient_data ) {
		global $pagenow;

		if ( ! is_object( $_transient_data ) ) {
			$_transient_data = new stdClass();
		}

		if ( 'plugins.php' === $pagenow && is_multisite() ) {
			return $_transient_data;
		}

		if ( ! empty( $_transient_data->response ) && ! empty( $_transient_data->response[ $this->name ] ) && false === $this->wp_override ) {
			return $_transient_data;
		}

		$version_info = $this->get_cached_version_info();

		if ( false === $version_info ) {
			$version_info = $this->api_request(
				'plugin_latest_version',
				array(
					'slug' => $this->slug,
					'beta' => $this->beta,
				)
			);

			$this->set_version_info_cache( $version_info );
		}

		if ( false !== $version_info && is_object( $version_info ) && isset( $version_info->new_version ) ) {
			if ( version_compare( $this->version, $version_info->new_version, '<' ) ) {
				$_transient_data->response[ $this->name ] = $version_info;
			}

			$_transient_data->last_checked           = current_time( 'timestamp' );
			$_transient_data->checked[ $this->name ] = $this->version;
		}

		return $_transient_data;
	}

	/**
	 * Show update nofication row -- needed for multisite subsites, because WP won't tell you otherwise!
	 *
	 * @param string $file file.
	 * @param array  $plugin plugin.
	 */
	public function show_update_notification( $file, $plugin ) {
		if ( is_network_admin() ) {
			return;
		}

		if ( ! current_user_can( 'update_plugins' ) ) {
			return;
		}

		if ( ! is_multisite() ) {
			return;
		}

		if ( $this->name != $file ) {
			return;
		}

		// Remove our filter on the site transient.
		remove_filter( 'pre_set_site_transient_update_plugins', array( $this, 'check_update' ), 10 );

		$update_cache = get_site_transient( 'update_plugins' );

		$update_cache = is_object( $update_cache ) ? $update_cache : new stdClass();

		if ( empty( $update_cache->response ) || empty( $update_cache->response[ $this->name ] ) ) {
			$version_info = $this->get_cached_version_info();

			if ( false === $version_info ) {
				$version_info = $this->api_request(
					'plugin_latest_version',
					array(
						'slug' => $this->slug,
						'beta' => $this->beta,
					)
				);

				$this->set_version_info_cache( $version_info );
			}

			if ( ! is_object( $version_info ) ) {
				return;
			}

			if ( version_compare( $this->version, $version_info->new_version, '<' ) ) {
				$update_cache->response[ $this->name ] = $version_info;
			}

			$update_cache->last_checked           = current_time( 'timestamp' );
			$update_cache->checked[ $this->name ] = $this->version;

			set_site_transient( 'update_plugins', $update_cache );
		} else {
			$version_info = $update_cache->response[ $this->name ];
		}

		// Restore our filter.
		add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'check_update' ) );

		if ( ! empty( $update_cache->response[ $this->name ] ) && version_compare( $this->version, $version_info->new_version, '<' ) ) {
			// build a plugin list row, with update notification.
			$wp_list_table = _get_list_table( 'WP_Plugins_List_Table' );

			echo '<tr class="plugin-update-tr" id="' . esc_attr( $this->slug ) . '-update" data-slug="' . esc_attr( $this->slug ) . '" data-plugin="' . esc_attr( $this->slug ) . '/' . esc_attr( $file ) . '">';
			echo '<td colspan="3" class="plugin-update colspanchange">';
			echo '<div class="update-message notice inline notice-warning notice-alt">';

			$changelog_link = self_admin_url( 'index.php?edd_sl_action=view_plugin_changelog&plugin=' . $this->name . '&slug=' . $this->slug . '&TB_iframe=true&width=772&height=911' );

			if ( empty( $version_info->download_link ) ) {
				printf(
					esc_html__( 'There is a new version of %1$s available. %2$sView version %3$s details%4$s.', 'buddypress-friend-follow-suggestion' ),
					esc_html( $version_info->name ),
					'<a target="_blank" class="thickbox" href="' . esc_url( $changelog_link ) . '">',
					esc_html( $version_info->new_version ),
					'</a>'
				);
			} else {
				printf(
					esc_html__( 'There is a new version of %1$s available. %2$sView version %3$s details%4$s or %5$supdate now%6$s.', 'buddypress-friend-follow-suggestion' ),
					esc_html( $version_info->name ),
					'<a target="_blank" class="thickbox" href="' . esc_url( $changelog_link ) . '">',
					esc_html( $version_info->new_version ),
					'</a>',
					'<a href="' . esc_url( wp_nonce_url( self_admin_url( 'update.php?action=upgrade-plugin&plugin=' ) . $this->name, 'upgrade-plugin_' . $this->name ) ) . '">',
					'</a>'
				);
			}

			do_action( "in_plugin_update_message-{$file}", $plugin, $version_info );

			echo '</div></td></tr>';
		}
	}

	/**
	 * Updates information on the "View version x.x details" page with custom data.
	 *
	 * @uses api_request()
	 *
	 * @param mixed  $_data data.
	 * @param string $_action action.
	 * @param object $_args args.
	 * @return object $_data data.
	 */
	public function plugins_api_filter( $_data, $_action = '', $_args = null ) {
		if ( 'plugin_information' !== $_action ) {
			return $_data;
		}

		if ( ! isset( $_args->slug ) || ( $_args->slug != $this->slug ) ) {
			return $_data;
		}

		$to_send = array(
			'slug'   => $this->slug,
			'is_ssl' => is_ssl(),
			'fields' => array(
				'banners' => array(),
				'reviews' => false,
			),
		);

		$cache_key = 'edd_api_request_' . md5( serialize( $this->slug . $this->api_data['license'] . $this->beta ) );

		// Get the transient where we store the api request for this plugin for 24 hours.
		$edd_api_request_transient = $this->get_cached_version_info( $cache_key );

		// If we have no transient-saved value, run the API, set a fresh transient with the API value, and return that value too right now.
		if ( empty( $edd_api_request_transient ) ) {
			$api_response = $this->api_request( 'plugin_information', $to_send );

			// Expires in 3 hours.
			$this->set_version_info_cache( $api_response, $cache_key );

			if ( false !== $api_response ) {
				$_data = $api_response;
			}
		} else {
			$_data = $edd_api_request_transient;
		}

		// Convert sections into an associative array, since we're getting an object, but Core expects an array.
		if ( isset( $_data->sections ) && ! is_array( $_data->sections ) ) {
			$new_sections = array();
			foreach ( $_data->sections as $key => $value ) {
				$new_sections[ $key ] = $value;
			}

			$_data->sections = $new_sections;
		}

		// Convert banners into an associative array, since we're getting an object, but Core expects an array.
		if ( isset( $_data->banners ) && ! is_array( $_data->banners ) ) {
			$new_banners = array();
			foreach ( $_data->banners as $key => $value ) {
				$new_banners[ $key ] = $value;
			}

			$_data->banners = $new_banners;
		}

		return $_data;
	}

	/**
	 * Disable SSL verification in order to prevent download update failures
	 *
	 * @param array  $args args.
	 * @param string $url url.
	 * @return object $array
	 */
	public function http_request_args( $args, $url ) {
		$verify_ssl = $this->verify_ssl();
		if ( strpos( $url, 'https://' ) !== false && strpos( $url, 'edd_action=package_download' ) ) {
			$args['sslverify'] = $verify_ssl;
		}
		return $args;
	}

	/**
	 * Calls the API and, if successfull, returns the object delivered by the API.
	 *
	 * @uses get_bloginfo()
	 * @uses wp_remote_post()
	 * @uses is_wp_error()
	 *
	 * @param string $_action The requested action.
	 * @param array  $_data   Parameters for the API action.
	 * @return false|object
	 */
	private function api_request( $_action, $_data ) {
		global $wp_version;

		$data = array_merge( $this->api_data, $_data );

		if ( $data['slug'] != $this->slug ) {
			return;
		}

		if ( $this->api_url == trailingslashit( home_url() ) ) {
			return false; // Don't allow a plugin to ping itself.
		}

		$api_params = array(
			'edd_action' => 'get_version',
			'license'    => ! empty( $data['license'] ) ? $data['license'] : '',
			'item_name'  => isset( $data['item_name'] ) ? $data['item_name'] : false,
			'item_id'    => isset( $data['item_id'] ) ? $data['item_id'] : false,
			'version'    => isset( $data['version'] ) ? $data['version'] : false,
			'slug'       => $data['slug'],
			'author'     => $data['author'],
			'url'        => home_url(),
			'beta'       => ! empty( $data['beta'] ),
		);

		$verify_ssl = $this->verify_ssl();
		$request    = wp_remote_post(
			$this->api_url,
			array(
				'timeout'   => 15,
				'sslverify' => $verify_ssl,
				'body'      => $api_params,
			)
		);

		if ( ! is_wp_error( $request ) ) {
			$request = json_decode( wp_remote_retrieve_body( $request ) );
		}

		if ( $request && isset( $request->sections ) ) {
			$request->sections = maybe_unserialize( $request->sections );
		} else {
			$request = false;
		}

		if ( $request && isset( $request->banners ) ) {
			$request->banners = maybe_unserialize( $request->banners );
		}

		if ( ! empty( $request->sections ) ) {
			foreach ( $request->sections as $key => $section ) {
				$request->$key = (array) $section;
			}
		}

		return $request;
	}

	public function show_changelog() {
		global $edd_plugin_data;

		if ( empty( $_REQUEST['edd_sl_action'] ) || 'view_plugin_changelog' != $_REQUEST['edd_sl_action'] ) {
			return;
		}

		if ( empty( $_REQUEST['plugin'] ) ) {
			return;
		}

		if ( empty( $_REQUEST['slug'] ) ) {
			return;
		}

		if ( ! current_user_can( 'update_plugins' ) ) {
			wp_die( esc_html_e( 'You do not have permission to install plugin updates', 'buddypress-friend-follow-suggestion' ), esc_html_e( 'Error', 'buddypress-friend-follow-suggestion' ), array( 'response' => 403 ) );
		}

		$data         = $edd_plugin_data[ $_REQUEST['slug'] ];
		$beta         = ! empty( $data['beta'] ) ? true : false;
		$cache_key    = md5( 'edd_plugin_' . sanitize_key( $_REQUEST['plugin'] ) . '_' . $beta . '_version_info' );
		$version_info = $this->get_cached_version_info( $cache_key );

		if ( false === $version_info ) {
			$api_params = array(
				'edd_action' => 'get_version',
				'item_name'  => isset( $data['item_name'] ) ? $data['item_name'] : false,
				'item_id'    => isset( $data['item_id'] ) ? $data['item_id'] : false,
				'slug'       => $_REQUEST['slug'],
				'author'     => $data['author'],
				'url'        => home_url(),
				'beta'       => ! empty( $data['beta'] ),
			);

			$verify_ssl = $this->verify_ssl();
			$request    = wp_remote_post(
				$this->api_url,
				array(
					'timeout'   => 15,
					'sslverify' => $verify_ssl,
					'body'      => $api_params,
				)
			);

			if ( ! is_wp_error( $request ) ) {
				$version_info = json_decode( wp_remote_retrieve_body( $request ) );
			}

			if ( ! empty( $version_info ) && isset( $version_info->sections ) ) {
				$version_info->sections = maybe_unserialize( $version_info->sections );
			} else {
				$version_info = false;
			}

			if ( ! empty( $version_info ) ) {
				foreach ( $version_info->sections as $key => $section ) {
					$version_info->$key = (array) $section;
				}
			}

			$this->set_version_info_cache( $version_info, $cache_key );
		}

		if ( ! empty( $version_info ) && isset( $version_info->sections['changelog'] ) ) {
			echo '<div style="background:#fff;padding:10px;">' . $version_info->sections['changelog'] . '</div>';
		}

		exit;
	}

	public function get_cached_version_info( $cache_key = '' ) {
		if ( empty( $cache_key ) ) {
			$cache_key = $this->cache_key;
		}

		$cache = get_option( $cache_key );

		if ( empty( $cache['timeout'] ) || current_time( 'timestamp' ) > $cache['timeout'] ) {
			return false; // Cache is expired
		}

		return json_decode( $cache['value'] );
	}

	public function set_version_info_cache( $value = '', $cache_key = '' ) {
		if ( empty( $cache_key ) ) {
			$cache_key = $this->cache_key;
		}

		$data = array(
			'timeout' => strtotime( '+3 hours', current_time( 'timestamp' ) ),
			'value'   => json_encode( $value ),
		);

		update_option( $cache_key, $data, 'no' );
	}

	/**
	 * Returns if the SSL of the store should be verified.
	 *
	 * @since  1.6.13
	 * @return bool
	 */
	private function verify_ssl() {
		 return (bool) apply_filters( 'edd_BFFS_sl_api_request_verify_ssl', true, $this );
	}
}
