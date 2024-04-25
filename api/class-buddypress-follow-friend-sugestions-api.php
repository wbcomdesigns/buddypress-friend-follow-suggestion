<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://wbcomdesigns.com/
 * @since      1.5.0
 *
 * @package    Buddypress_Friend_Follow_Suggestion
 * @subpackage Buddypress_Friend_Follow_Suggestion/api
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Buddypress_Friend_Follow_Suggestion
 * @subpackage Buddypress_Friend_Follow_Suggestion/api
 * @author     WBComDesigns <admin@wbcomdesigns.com>
 */
class BP_Follow_Friend_Suggetion_API {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.5.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.5.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Instance of a user meta fields object.
	 *
	 * @since 1.5.0
	 * @var WP_REST_User_Meta_Fields
	 */
	protected $meta;

	/**
	 * The namespace of this controller's route.
	 *
	 * @since 1.5.0
	 * @var string
	 */
	protected $namespace;

	/**
	 * The base of this controller's route.
	 *
	 * @since 1.5.0
	 * @var string
	 */
	protected $rest_base;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since 1.5.0
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;

		$this->namespace = 'bpffs/v1';
		$this->rest_base = 'swipe';

		$this->meta = new WP_REST_User_Meta_Fields();
	}

		/**
	 * Registers the routes for users.
	 *
	 * @since 4.7.0
	 *
	 * @see register_rest_route()
	 */
	public function register_routes() {
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/(?P<id>[\d]+)',
			array(
				'args' => array(
					'id' => array(
						'description' => __( 'Unique identifier for the user.' ),
						'type'        => 'integer',
					),
				),
				array(
					'methods'             => WP_REST_Server::EDITABLE,
					'callback'            => array( $this, 'swipe_item' ),
					'permission_callback' => array( $this, 'swipe_item_permissions_check' ),
				),
			)
		);
	}


	/**
	 * Checks if a given request has access to read a user.
	 *
	 * @since 1.5.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return true|WP_Error True if the request has read access for the item, otherwise WP_Error object.
	 */
	public function swipe_item_permissions_check( $request ) {

		$user = $this->get_user( $request['id'] );

		if ( is_wp_error( $user ) ) {
			return $user;
		}

		$nonce = isset( $_REQUEST['security'] ) && ! empty( $_REQUEST['security'] ) ? wp_unslash( sanitize_text_field( $_REQUEST['security'] ) ) : '';

		if ( ! wp_verify_nonce( $nonce, 'bffs-swipe-' . get_current_user_id() ) ) {
			return new WP_Error(
				'rest_user_cannot_view',
				__( 'Sorry, you are not allowed to list users.' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		return true;
	}

	/**
	 * Retrieves a single user.
	 *
	 * @since 1.5.0
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function swipe_item( $request ) {
		$user = $this->get_user( $request['id'] );

		if ( is_wp_error( $user ) ) {
			return $user;
		}

		$swiped = get_user_meta( get_current_user_id(), 'swiped', true );

		if ( ! empty( $swiped ) ) {
			if ( ! in_array( $user->ID, $swiped ) ) {
				$swiped[] = $user->ID;
			}
		} else {
			$swiped = array( $user->ID );
		}

		$isSwiped = update_user_meta( get_current_user_id(), 'swiped', $swiped );

		return  rest_ensure_response( $isSwiped );

	}


	/**
	 * Get the user, if the ID is valid.
	 *
	 * @since 1.5.0
	 *
	 * @param int $id Supplied ID.
	 * @return WP_User|WP_Error True if ID is valid, WP_Error otherwise.
	 */
	protected function get_user( $id ) {
		$error = new WP_Error(
			'rest_user_invalid_id',
			__( 'Invalid user ID.' ),
			array( 'status' => 404 )
		);

		if ( (int) $id <= 0 ) {
			return $error;
		}

		$user = get_userdata( (int) $id );
		if ( empty( $user ) || ! $user->exists() ) {
			return $error;
		}

		if ( is_multisite() && ! is_user_member_of_blog( $user->ID ) ) {
			return $error;
		}

		return $user;
	}


}



