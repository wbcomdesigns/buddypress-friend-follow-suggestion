<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://wbcomdesigns.com/
 * @since      1.0.0
 *
 * @package    Buddypress_Friend_Follow_Suggestion
 * @subpackage Buddypress_Friend_Follow_Suggestion/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Buddypress_Friend_Follow_Suggestion
 * @subpackage Buddypress_Friend_Follow_Suggestion/admin
 * @author     WBComDesigns <admin@wbcomdesigns.com>
 */
class Buddypress_Friend_Follow_Suggestion_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Buddypress_Friend_Follow_Suggestion_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Buddypress_Friend_Follow_Suggestion_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/buddypress-friend-follow-suggestion-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Buddypress_Friend_Follow_Suggestion_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Buddypress_Friend_Follow_Suggestion_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/buddypress-friend-follow-suggestion-admin.js', array( 'jquery' ), $this->version, false );

	}
	
	public function bffs_add_submenu_page_admin_settings() {
		
		if ( empty( $GLOBALS['admin_page_hooks']['wbcomplugins'] ) ) {
			add_menu_page( esc_html__( 'WB Plugins', 'buddypress-friend-follow-suggestion' ), esc_html__( 'WB Plugins', 'buddypress-friend-follow-suggestion' ), 'manage_options', 'wbcomplugins', array( $this, 'bffs_admin_options_page' ), 'dashicons-lightbulb', 59 );
			add_submenu_page( 'wbcomplugins', esc_html__( 'General', 'buddypress-friend-follow-suggestion' ), esc_html__( 'General', 'buddypress-friend-follow-suggestion' ), 'manage_options', 'wbcomplugins' );
		}
		add_submenu_page( 'wbcomplugins', esc_html__( 'Admin Settings For Buddypress Friend & Follow Suggestion', 'buddypress-friend-follow-suggestion' ), esc_html__( 'BP Friend & Follow Suggestion', 'buddypress-friend-follow-suggestion' ), 'manage_options', 'bffss-settings', array( $this, 'bffs_admin_options_page' ) );
	}
	
	public function bffs_plugin_settings() {
		$this->plugin_settings_tabs['bffs-general'] = esc_html__( 'General', 'buddypress-friend-follow-suggestion' );
		register_setting( 'bffs_admin_general_options', 'bffs_admin_general_options' );
		add_settings_section( 'bffs-general', ' ', array( $this, 'bffs_admin_general_content' ), 'bffs-general' );
	}
	
	public function bffs_admin_general_content() {
			require_once BFFS_PLUGIN_PATH . 'admin/inc/buddypress-friend-follow-suggestion-general-settings.php';
		}
	
	public function bffs_admin_register_settings() {
		if(isset($_POST['bffs_settings'])){				
			update_site_option('bffs_settings',$_POST['bffs_settings']);
			wp_redirect($_POST['_wp_http_referer']);
			exit();
		}
	}
	
	
	/**
	 * Actions performed to create a submenu page content.
	 *
	 * @since    1.0.0
	 * @access public
	 */
	public function bffs_admin_options_page() {

		global $allowedposttags;
		$tab = filter_input( INPUT_GET, 'tab' ) ? filter_input( INPUT_GET, 'tab' ) : 'bffs-general';
		?>
		<div class="wrap">
			<div class="bffs-header">
				<?php echo do_shortcode( '[wbcom_admin_setting_header]' ); ?>
				<h1 class="wbcom-plugin-heading">
					<?php esc_html_e( 'BuddyPress Friend & Follow Suggestion Settings', 'buddypress-friend-follow-suggestion' ); ?>
				</h1>
			</div>
			<div class="wbcom-admin-settings-page">
				<?php
				settings_errors();
				$this->bffs_plugin_settings_tabs();
				settings_fields( $tab );
				do_settings_sections( $tab );
				?>
			</div>
		</div>
		<?php

	}
	
	/**
	 * Actions performed to create tabs on the sub menu page.
	 *
	 * @since    1.0.0
	 * @access public
	 */
	public function bffs_plugin_settings_tabs() {

		$current_tab = filter_input( INPUT_GET, 'tab' ) ? filter_input( INPUT_GET, 'tab' ) : 'bpgp-general';
		// xprofile setup tab.
		echo '<div class="wbcom-tabs-section"><h2 class="nav-tab-wrapper">';
		foreach ( $this->plugin_settings_tabs as $tab_key => $tab_caption ) {
			$active = $current_tab === $tab_key ? 'nav-tab-active' : '';
			echo '<a class="nav-tab ' . esc_attr( $active ) . '" id="' . esc_attr( $tab_key ) . '-tab" href="?page=bpgp-settings' . '&tab=' . esc_attr( $tab_key ) . '">' . esc_attr( $tab_caption ) . '</a>';
		}
		echo '</h2></div>';
	}

}
