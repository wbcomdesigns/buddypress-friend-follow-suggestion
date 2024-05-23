<?php
/**
 * BuddyPress Friend Follow Suggestion Widget.
 *
 * @package Buddypress_Friend_Follow_Suggestion
 * @since 1.0.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * BuddyPress Friend Follow Suggestion Widget.
 *
 * @since 1.0.0
 */
class BP_Friend_Follow_Suggestion_Swiper_Widget extends WP_Widget {

	/**
	 * Constructor method.
	 *
	 * @since 1.5.0
	 */
	public function __construct() {

		// Setup widget name & description.
		$name        = _x( '(Swiper) BuddyPress Friend & Follow Suggestion', 'widget name', 'buddypress-friend-follow-suggestion' );
		$description = __( 'A dynamic suggestion list of matched members to your profile.', 'buddypress-friend-follow-suggestion' );

		// Call WP_Widget constructor.
		parent::__construct(
			false,
			$name,
			array(
				'description' => $description,
				'classname'   => 'widget_bp_friend_follow_suggestion_widget buddypress widget',
			)
		);
	}


	/**
	 * Display the Suggeation widget.
	 *
	 * @since 1.0.0
	 *
	 * @see WP_Widget::widget() for description of parameters.
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Widget settings, as saved by the user.
	 */
	public function widget( $args, $instance ) {
		do_action( 'bp_enqueue_community_scripts' );
		global $members_template;
		global $follower_id;
		global $leader_id;
		global $button_args;
		// Get widget settings.
		$settings = $this->parse_settings( $instance );
		$settings['layout'] = (isset($settings['layout']) && $settings['layout'] != '') ? $settings['layout']: 'layout_one';

		/**
		 * Filters the title of the Suggestions widget.
		 *
		 * @since 1.0.0
		 *
		 * @param string $title    The widget title.
		 * @param array  $settings The settings for the particular instance of the widget.
		 * @param string $id_base  Root ID for all widgets of this type.
		 */
		$title = apply_filters( 'widget_title', $settings['title'], $settings, $this->id_base );
		// Output before widget HTMl, title (and maybe content before & after it).
		echo wp_kses_post( $args['before_widget'] ) . wp_kses_post( $args['before_title'] ) . esc_html( $title ) . wp_kses_post( $args['after_title'] );

		$max_members         = (int) $settings['max_members'];
		$percentage_criteria = (int) $settings['percentage_criteria'];
		$suggest             = $settings['suggest'];
		$matched_members     = bp_suggestions_get_matched_users( bp_loggedin_user_id(), $max_members, $percentage_criteria, $settings['suggest'] );
		$bb_follow_buttons   = false;
		if ( function_exists( 'bp_admin_setting_callback_enable_activity_follow' ) ) {
			$bb_follow_buttons = bp_is_activity_follow_active();
		}
		// Setup args for querying members.
		$members_args = array(
			'include'         => $matched_members,
			'exclude'         => array( bp_loggedin_user_id() ),
			'per_page'        => $max_members,
			'max'             => $max_members,
			'populate_extras' => true,
			'search_terms'    => false,
		);
		
		
		if ( bp_has_members( $members_args ) && ! empty( $matched_members ) ) :    
		
			require BFFS_PLUGIN_PATH . 'templates/horizontal-slider-layout.php';
				
		else : ?>

			<div class="widget-error">
				<?php esc_html_e( 'No suggestion found.', 'buddypress-friend-follow-suggestion' ); ?>
			</div>
		<?php
		endif;
		echo wp_kses_post( $args['after_widget'] );

	}

		/**
		 * Update the Suggestions widget options.
		 *
		 * @since 1.0.0
		 *
		 * @param array $new_instance The new instance options.
		 * @param array $old_instance The old instance options.
		 * @return array $instance The parsed options to be saved.
		 */
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		$instance['title']               = strip_tags( $new_instance['title'] );
		$instance['suggest']             = strip_tags( $new_instance['suggest'] );
		$instance['max_members']         = intval( $new_instance['max_members'] );
		$instance['percentage_criteria'] = intval( $new_instance['percentage_criteria'] );
		$instance['layout']              = ( ! empty( $new_instance['layout'] ) ) ? $new_instance['layout'] : '';

		return $instance;
	}

				/**
				 * Output the Suggestion widget options form.
				 *
				 * @since 1.0.0
				 *
				 * @param array $instance Widget instance settings.
				 * @return void
				 */
	public function form( $instance ) {
		// Get widget settings.
		$settings            = $this->parse_settings( $instance );
		$title               = strip_tags( $settings['title'] );
		$max_members         = intval( $settings['max_members'] );
		$percentage_criteria = intval( $settings['percentage_criteria'] );
		$suggest             = isset( $settings['suggest'] ) ? strip_tags( $settings['suggest'] ) : 'friends';
		$bb_follow_button    = false;
		if ( function_exists( 'bp_admin_setting_callback_enable_activity_follow' ) ) {
			$bb_follow_button = bp_is_activity_follow_active();
		}

		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>">
					<?php esc_html_e( 'Title:', 'buddypress-friend-follow-suggestion' ); ?>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" style="width: 100%" />
			</label>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'max_members' ) ); ?>">
					<?php esc_html_e( 'Max members to show:', 'buddypress-friend-follow-suggestion' ); ?>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'max_members' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'max_members' ) ); ?>" type="number" min="1" max="100" value="<?php echo esc_attr( $max_members ); ?>" style="width: 30%" />
			</label>
		</p>
		<p>
		<label for="<?php echo esc_attr( $this->get_field_id( 'percentage_criteria' ) ); ?>">
				<?php esc_html_e( 'Percentage Criteria:', 'buddypress-friend-follow-suggestion' ); ?>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'percentage_criteria' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'percentage_criteria' ) ); ?>" type="number" min="1" max="100" value="<?php echo esc_attr( $percentage_criteria ); ?>" style="width: 30%" />
			%
		</label>
		</p>			
		<p>
			<label><?php esc_attr_e( 'Layout:', 'buddypress-friend-follow-suggestion' ); ?></label>
			<select name="<?php echo esc_attr( $this->get_field_name( 'layout' ) ); ?>">
				<option value="layout_one"<?php echo isset( $instance['layout'] ) ? selected( $instance['layout'], 'layout_one' ) : ''; ?>><?php esc_html_e( 'Swiper Layout One', 'buddypress-friend-follow-suggestion' ); ?></option>
				<option value="layout_two"<?php echo isset( $instance['layout'] ) ? selected( $instance['layout'], 'layout_two' ) : ''; ?>><?php esc_html_e( 'Swiper Layout Two', 'buddypress-friend-follow-suggestion' ); ?></option>
			</select>				
		</p>
		<p>
			<label>
				<input type="radio" value="friends" name="<?php echo esc_attr( $this->get_field_name( 'suggest' ) ); ?>" <?php checked( $suggest, 'friends' ); ?> id="<?php echo esc_attr( $this->get_field_id( 'suggest' ) ); ?>" />
				<?php esc_attr_e( 'Friends Suggestion', 'buddypress-friend-follow-suggestion' ); ?>
			</label>
		</p>
				<?php if ( bp_is_active( 'follow' ) || true === $bb_follow_button ) : ?>
		<p>
			<label>
				<input type="radio" value="follow" name="<?php echo esc_attr( $this->get_field_name( 'suggest' ) ); ?>" <?php checked( $suggest, 'follow' ); ?> id="<?php echo esc_attr( $this->get_field_id( 'suggest' ) ); ?>" />
					<?php esc_attr_e( 'Follow Suggestion', 'buddypress-friend-follow-suggestion' ); ?>
			</label>
		</p>
		<?php endif; 
	}

	/**
	 * Merge the widget settings into defaults array.
	 *
	 * @since 1.0.0
	 *
	 * @param array $instance Widget instance settings.
	 * @return array
	 */
	public function parse_settings( $instance = array() ) {
		return bp_parse_args(
			$instance,
			array(
				'title'               => __( 'Suggestions', 'buddypress-friend-follow-suggestion' ),
				'max_members'         => 5,
				'percentage_criteria' => 10,
				'suggest'             => 'friends',
				'layout'              => 'list_layout',
			),
			'suggestion_widget_settings'
		);
	}

}

				/**
				 * Bp_suggestion_register_widgets
				 *
				 * @since 1.0.0
				 */
function bp_suggestion_swiper_register_widgets() {
	add_action(
		'widgets_init',
		function() {
			return register_widget( 'BP_Friend_Follow_Suggestion_Swiper_Widget' );
		}
	);
}
$bffs_general_setting = get_site_option( 'bffs_general_setting' );
if ( isset( $bffs_general_setting['enable_profile_match'] ) && 1 == $bffs_general_setting['enable_profile_match'] ) {
	add_action( 'bp_register_widgets', 'bp_suggestion_swiper_register_widgets' );
}
