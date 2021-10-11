<?php
/**
 * Buddypress plugin functions
 *
 * @package BoomBox_Theme
 * @since   1.0.0
 * @version 2.0.0
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

if ( ! boombox_plugin_management_service()->is_plugin_active( 'buddypress/bp-loader.php' ) ) {
	return;
}

if ( ! class_exists( 'Boombox_Buddypress' ) ) {

	final class Boombox_Buddypress {

		/**
		 * Holds class single instance
		 * @var null
		 */
		private static $_instance = null;

		/**
		 * Get instance
		 * @return Boombox_Buddypress|null
		 */
		public static function get_instance() {

			if ( null == static::$_instance ) {
				static::$_instance = new self();
			}

			return static::$_instance;

		}

		/**
		 * Boombox_Buddypress constructor.
		 */
		private function __construct() {
			$this->define_constants();
			$this->hooks();

			do_action( 'boombox/bp/wakeup', $this );
		}

		/**
		 * A dummy magic method to prevent Boombox_Buddypress from being cloned.
		 *
		 */
		public function __clone() {
			throw new Exception( 'Cloning ' . __CLASS__ . ' is forbidden' );
		}

		/**
		 * Define constants
		 */
		private function define_constants() {
			if ( ! defined( 'BP_AVATAR_FULL_WIDTH' ) ) {
				define( 'BP_AVATAR_FULL_WIDTH', 186 );
			}
			if ( ! defined( 'BP_AVATAR_FULL_HEIGHT' ) ) {
				define( 'BP_AVATAR_FULL_HEIGHT', 186 );
			}

			if ( ! defined( 'BP_AVATAR_THUMB_WIDTH' ) ) {
				define( 'BP_AVATAR_THUMB_WIDTH', 66 );
			}
			if ( ! defined( 'BP_AVATAR_THUMB_HEIGHT' ) ) {
				define( 'BP_AVATAR_THUMB_HEIGHT', 66 );
			}

			if ( ! defined( 'BP_AVATAR_DEFAULT' ) ) {
				define( 'BP_AVATAR_DEFAULT', BOOMBOX_THEME_URL . 'buddypress/images/user.jpg' );
			}
			if ( ! defined( 'BP_AVATAR_DEFAULT_THUMB' ) ) {
				define( 'BP_AVATAR_DEFAULT_THUMB', BOOMBOX_THEME_URL . 'buddypress/images/user-150.jpg' );
			}
		}

		/**
		 * Setup Hooks
		 */
		private function hooks() {

			add_filter( 'boombox_author_avatar_size', array( $this, 'author_avatar_size' ), 10, 1 );
			add_filter( 'bp_core_default_avatar', array( $this, 'edit_groups_default_avatar' ), 10, 3 );
			add_filter( 'bp_core_avatar_default_thumb', array( $this, 'edit_groups_default_avatar' ), 10, 3 );
			add_filter( 'bp_before_members_cover_image_settings_parse_args', array( $this, 'attach_theme_handle' ), 10, 1 );
			add_filter( 'bp_before_groups_cover_image_settings_parse_args', array( $this, 'attach_theme_handle' ), 10, 1 );
			add_filter( 'bp_core_get_js_strings', array( $this, 'core_get_js_strings' ), 10, 1 );
			add_filter( 'bp_get_add_friend_button', array( $this, 'get_add_friend_button' ), 10, 1 );
			add_filter( 'bp_get_send_message_button_args', array( $this, 'get_send_message_button_args' ), 10, 1 );
			add_filter( 'bp_get_send_public_message_button', array( $this, 'get_send_public_message_button' ), 10, 1 );
			add_filter( 'author_link', array( $this, 'author_link' ), 10, 3 );
			add_filter( 'bp_get_new_group_invite_friend_list', array( $this, 'get_new_group_invite_friend_list' ), 10, 3 );
			add_filter( 'bp_nav_menu_objects', array( $this, 'nav_menu_objects' ), 9999, 2 );
			add_filter( 'bp_get_the_profile_field_required_label', array( $this, 'bbp_get_the_profile_field_required_label' ), 10, 2 );
			add_filter( 'bp_members_signup_error_message', array( $this, 'members_signup_error_message' ), 10, 1 );
			add_filter( 'author_extended_data', array( $this, 'bbp_author_extended_data' ), 10, 2 );
			add_filter( 'boombox/author_bio', array( $this, 'edit_author_biography' ), 10, 2 );
			add_filter( 'gfy/author_bio', array( $this, 'edit_author_biography' ), 10, 2 );
			add_filter( 'boombox/color_scheme_styles', array( $this, 'bbp_color_scheme_styles' ), 10, 1 );
			add_filter( 'wpseo_title', array( $this, 'wpseo_fix_title_buddypress' ), 10, 1 );
			add_filter( 'boombox/customizer/fields/extras_authentication', array( $this, 'add_settings_to_customizer_extras_authentication' ), 10, 3 );
			add_filter( 'boombox/customizer_default_values', array( $this, 'customizer_default_values' ), 10, 1 );
			add_filter( 'boombox/auth/registration_callback', array( $this, 'edit_registration_callback' ), 10, 1 );
			add_filter( 'boombox/auth/login/get_user', array( $this, 'check_user_on_auth_login' ), 10, 3 );
			add_action( 'wp_ajax_nopriv_bb_bp_resend_activation_email', array( $this, 'resend_activation_email' ) );
			add_action( 'after_setup_theme', array( $this, 'add_theme_support' ), 11 );

			if ( has_action( 'bp_notification_settings', 'bp_activity_screen_notification_settings' ) ) {
				remove_action( 'bp_notification_settings', 'bp_activity_screen_notification_settings', 1 );
				add_action( 'bp_notification_settings', array( $this, 'bp_activity_screen_notification_settings' ), 1 );
			}

			if ( has_action( 'bp_notification_settings', 'friends_screen_notification_settings' ) ) {
				remove_action( 'bp_notification_settings', 'friends_screen_notification_settings' );
				add_action( 'bp_notification_settings', array( $this, 'friends_screen_notification_settings' ) );
			}

			if ( has_action( 'bp_notification_settings', 'messages_screen_notification_settings' ) ) {
				remove_action( 'bp_notification_settings', 'messages_screen_notification_settings', 2 );
				add_action( 'bp_notification_settings', array( $this, 'messages_screen_notification_settings' ), 3 );
			}

			if ( has_action( 'bp_notification_settings', 'groups_screen_notification_settings' ) ) {
				remove_action( 'bp_notification_settings', 'groups_screen_notification_settings' );
				add_action( 'bp_notification_settings', array( $this, 'groups_screen_notification_settings' ), 4 );
			}

			add_action( 'bp_before_member_home_content', array( $this, 'before_member_home_content' ), 10 );
			add_action( 'bp_after_member_home_content', array( $this, 'after_member_home_content' ), 10 );
			add_action( 'bp_before_group_home_content', array( $this, 'before_group_home_content' ), 10 );
			add_action( 'bp_after_group_home_content', array( $this, 'after_group_home_content' ), 10 );
			add_action( 'boombox/before_authentication', array( $this, 'user_notifications' ), 10, 2 );
			add_action( 'bbp_captcha', array( $this, 'before_registration_submit_buttons' ), 10 );
			add_action( 'bp_signup_validate', array( $this, 'signup_validate' ), 10 );
			add_action( 'bp_before_member_header_meta', array( $this, 'show_user_total_posts_and_views_count' ), 20 );
			add_action( 'bp_before_member_header_meta', array( $this, 'member_social_xprofile_data' ), 20 );
			add_action( 'wp_head', array( $this, 'generate_user_meta_description' ), 10, 1 );
			add_action( 'bp_setup_nav', array( $this, 'main_nav_setup_sub_nav' ), 9999 );

			add_filter( 'gfy/widget/featured_author/cover_image_url', array( $this, 'edit_gfy_featured_author_widget_cover_image_url' ), 10, 2 );
			add_filter( 'gfy/widget/featured_author/social_links', array( $this, 'edit_gfy_featured_author_widget_social_links' ), 10, 2 );

		}

		/**
		 * Add theme compat support
		 */
		public function add_theme_support() {
			add_theme_support( 'buddypress-use-legacy' );
		}

		/**
		 * Fix Yoast Seo broken title
		 *
		 * @param string $title Current title
		 *
		 * @return string
		 */
		function wpseo_fix_title_buddypress( $title ) {
			// Check if we are in a buddypress page
			if ( ( is_user_logged_in() && isset( buddypress()->displayed_user->id ) && buddypress()->displayed_user->id ) || buddypress()->current_component ) {
				$bp_title_parts = bp_modify_document_title_parts();

				// let's rebuild the title here
				if ( isset( $bp_title_parts[ 'title' ] ) ) {
					$title = $bp_title_parts[ 'title' ] . ' ' . $title;
				}
			}

			return $title;
		}

		/**
		 * Modify error message layout
		 *
		 * @param string $error_message Current error message
		 *
		 * @return string
		 */
		function members_signup_error_message( $error_message ) {
			return sprintf( '<div class="error bb-txt-msg msg-error">%1$s</div>', strip_tags( $error_message ) );
		}

		/**
		 * Add additional JS variables
		 *
		 * @param array $params Current data
		 *
		 * @return array
		 */
		public function core_get_js_strings( $params ) {
			$boombox_params = array(
				'captcha_file_url' => BOOMBOX_INCLUDES_URL . 'authentication/default/captcha/captcha-security-image.php',
			);
			if ( function_exists( 'boombox_get_auth_loading_message' ) ) {
				$boombox_params[ 'auth_loading_message' ] = boombox_get_auth_loading_message( 'default' );
			}

			return array_merge( $params, $boombox_params );
		}

		/**
		 * Get captcha field name
		 *
		 * @param string $type Captcha type
		 *
		 * @return string|bool
		 */
		private function get_recaptcha_fieldname( $type ) {
			switch ( $type ) {
				case 'image':
					$fieldname = 'bp_register';
					break;
				case 'google':
                case 'google_v3':
					$fieldname = 'g-recaptcha-response';
					break;
				default:
					$fieldname = false;
			}

			return $fieldname;
		}

		/**
		 * Add validation rules
		 */
		public function signup_validate() {

			$boombox_auth_captcha_type = boombox_get_auth_captcha_type();
			$boombox_enable_registration_captcha = boombox_get_theme_option( 'extra_authentication_enable_registration_captcha' );

			if ( boombox_is_auth_allowed() && ! is_user_logged_in() && $boombox_enable_registration_captcha ) {
				$bp = buddypress();

				if ( 'image' === $boombox_auth_captcha_type ) {

					$fieldname = $this->get_recaptcha_fieldname( $boombox_auth_captcha_type );
					if ( ! boombox_validate_image_captcha( $fieldname, 'bp_register' ) ) {
						$bp->signup->errors[ $fieldname ] = __( 'Invalid captcha', 'buddypress' );
					}

				} else if ( in_array( $boombox_auth_captcha_type, array( 'google', 'google_v3' ) ) ) {

					$fieldname = $this->get_recaptcha_fieldname( $boombox_auth_captcha_type );
					$recaptcha_response = boombox_validate_google_captcha( $fieldname );

					if ( ! $recaptcha_response[ 'success' ] ) {
						$bp->signup->errors[ $fieldname ] = __( 'Invalid recaptcha.', 'buddypress' );
					}

				}
			}
		}

		/**
		 * Render captcha
		 */
		public function before_registration_submit_buttons() {

			$boombox_auth_captcha_type = boombox_get_auth_captcha_type();
			$boombox_enable_registration_captcha = boombox_get_theme_option( 'extra_authentication_enable_registration_captcha' );

			if ( boombox_is_auth_allowed() && ! is_user_logged_in() && $boombox_enable_registration_captcha && $boombox_auth_captcha_type ) {

				if ( 'image' === $boombox_auth_captcha_type ) {

					$fieldname = $this->get_recaptcha_fieldname( $boombox_auth_captcha_type );
					do_action( 'bp_' . $fieldname . '_errors' );

					echo sprintf( '<div class="input-field captcha-container loading">
                        <div class="form-captcha">
                            <img src="" alt="Captcha!" class="captcha">
                            <a href="#refresh-captcha" class="bp-auth-refresh-captcha refresh-captcha" data-action="bp_register"></a>
                        </div>
                        <input type="text" name="%1$s" class="required" placeholder="%2$s">
                    </div>',
						$fieldname,
						esc_html__( 'Enter captcha', 'boombox' )
					);

				} elseif ( 'google' === $boombox_auth_captcha_type ) {
					do_action( 'bp_' . $this->get_recaptcha_fieldname( $boombox_auth_captcha_type ) . '_errors' );
					echo sprintf( '<div class="g-recaptcha google-captcha-code" id="bbp-register-captcha" data-sitekey="%1$s"></div>', boombox_get_theme_option( 'extra_authentication_google_recaptcha_site_key' ) );
				} elseif( 'google_v3' === $boombox_auth_captcha_type ) {
					do_action( 'bp_' . $this->get_recaptcha_fieldname( $boombox_auth_captcha_type ) . '_errors' );
					echo sprintf( '<div class="google-captcha-code" id="bbp-register-captcha" data-sitekey="%1$s"></div>', boombox_get_theme_option( 'extra_authentication_google_v3_recaptcha_site_key' ) ); ?>
                    <input type="hidden" id="g-recaptcha-response-buddypress" name="g-recaptcha-response" /><?php
                }
			}
		}

		/**
		 * Modify required label for profile fields
		 *
		 * @param string $translated_string Current string
		 * @param string $field_id          Field unique ID
		 *
		 * @return string
		 */
		public function bbp_get_the_profile_field_required_label( $translated_string, $field_id ) {
			$translated_string = '*';

			return $translated_string;
		}

		/**
		 * Set default avatar size
		 *
		 * @param int $size Current size
		 *
		 * @return int
		 */
		public function author_avatar_size( $size ) {
			return BP_AVATAR_THUMB_WIDTH;
		}

		/**
		 * Hook into cover image to attach style handle for profile image
		 *
		 * @param array $settings Current settings
		 *
		 * @return array
		 */
		public function attach_theme_handle( $settings = array() ) {

			$theme_handle = 'bp-parent-css';
			if ( is_rtl() ) {
				$theme_handle .= '-rtl';
			}
			$settings[ 'theme_handle' ] = $theme_handle;
			$settings[ 'width' ] = 1920;
			$settings[ 'height' ] = 265;

			return (array)apply_filters( 'boombox/buddypress/theme_default_settings', $settings );
		}

		/**
		 * Hook into 'add friend' button args to modify required params
		 *
		 * @param array <string,mixed> $button_args Current arguments
		 *
		 * @return array
		 */
		public function get_add_friend_button( $button_args ) {
			$button_args[ 'link_class' ] = 'btn btn-primary';

			return $button_args;
		}

		/**
		 * Hook into 'private message' button args to modify required params
		 *
		 * @param array <string,mixed> $button_args Current arguments
		 *
		 * @return array
		 */
		public function get_send_message_button_args( $button_args ) {
			$button_args[ 'link_class' ] = 'btn btn-primary';

			return $button_args;
		}

		/**
		 * Hook into 'public message' button args to modify required params
		 *
		 * @param array <string,mixed> $button_args Current arguments
		 *
		 * @return array
		 */
		public function get_send_public_message_button( $button_args ) {
			$button_args[ 'link_class' ] = 'btn btn-primary';

			return $button_args;
		}

		/**
		 * Locate author post link to buddypress profile
		 *
		 * @param string $link     Current link
		 * @param int    $user_id  User ID
		 * @param string $nicename User nicename
		 *
		 * @return string
		 */
		public function author_link( $link, $user_id, $nicename ) {
			$link = bp_core_get_user_domain( $user_id );

			return $link;
		}

		/**
		 * Hook for generate the "x members" count string for a group.
		 *
		 * @param string $value Current value
		 *
		 * @return string
		 */
		public function make_number_rounded( $value ) {
			return sprintf( '<span class="count">%s</span>', $value );
		}

		/**
		 * Render the Group members template
		 */
		public function groups_members_template_part() {
			?>
			<div class="item-list-tabs" id="subnav" role="navigation">
				<ul>
					<?php do_action( 'bp_members_directory_member_sub_types' ); ?>
				</ul>
			</div>

			<div class="bbp-filters">
				<div class="row">
					<div class="col-sm-6">
						<div class="bbp-filter">
							<?php $this->groups_members_filter(); ?>
						</div>
					</div>

					<div class="col-sm-6">
						<div class="bbp-search">
							<div class="groups-members-search" role="search">
								<?php bp_directory_members_search_form(); ?>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div id="members-group-list" class="group_members dir-list">

				<?php bp_get_template_part( 'groups/single/members' ); ?>

			</div>
			<?php
		}

		/**
		 * Render the Group members filters
		 */
		public function groups_members_filter() {
			?>
			<div id="group_members-order-select" class="filter">
				<label for="group_members-order-by"><?php _e( 'Order By:', 'buddypress' ); ?></label>
				<select id="group_members-order-by">
					<option value="last_joined"><?php _e( 'Newest', 'buddypress' ); ?></option>
					<option value="first_joined"><?php _e( 'Oldest', 'buddypress' ); ?></option>

					<?php if ( bp_is_active( 'activity' ) ) : ?>
						<option value="group_activity"><?php _e( 'Group Activity', 'buddypress' ); ?></option>
					<?php endif; ?>

					<option value="alphabetical"><?php _e( 'Alphabetical', 'buddypress' ); ?></option>

					<?php do_action( 'bp_groups_members_order_options' ); ?>

				</select>
			</div>
			<?php
		}

		/**
		 * Edit groups/members default avatar
		 *
		 * @param string $avatar Current avatar
		 * @param        array   <string,mixed> $params   Additional params
		 *
		 * @return string
		 */
		public function edit_groups_default_avatar( $avatar, $params ) {
			if ( isset( $params[ 'object' ] ) && 'group' === $params[ 'object' ] ) {
				$file = 'group';
				if ( isset( $params[ 'type' ] ) && 'thumb' === $params[ 'type' ] ) {
					$file .= '-150';
				}
				$avatar = apply_filters( 'boombox/buddypress/group_default_avatar', BOOMBOX_THEME_URL . "buddypress/images/$file.jpg", $file );
			}

			return $avatar;
		}

		/**
		 * Change invited users list HTML
		 *
		 * @param array $items Array of friends.
		 * @param array $r     Parsed arguments from bp_get_new_group_invite_friend_list()
		 * @param array $args  Unparsed arguments from bp_get_new_group_invite_friend_list()
		 *
		 * @return array
		 */
		public function get_new_group_invite_friend_list( $items, $r, $args ) {
			$friends = friends_get_friends_invite_list( $r[ 'user_id' ], $r[ 'group_id' ] );

			if ( ! empty( $friends ) ) {

				$items = array();

				$invites = groups_get_invites_for_group( $r[ 'user_id' ], $r[ 'group_id' ] );

				for ( $i = 0, $count = count( $friends ); $i < $count; ++$i ) {
					$checked = in_array( (int)$friends[ $i ][ 'id' ], (array)$invites );
					$items[] = '<' . $r[ 'separator' ] . '><label class="bbp-checkbox" for="f-' . esc_attr( $friends[ $i ][ 'id' ] ) . '"><input' . checked( $checked, true, false ) . ' type="checkbox" name="friends[]" id="f-' . esc_attr( $friends[ $i ][ 'id' ] ) . '" value="' . esc_attr( $friends[ $i ][ 'id' ] ) . '" /><span class="bbp-checkbox-check"></span>' . esc_html( $friends[ $i ][ 'full_name' ] ) . '</label></' . $r[ 'separator' ] . '>';
				}

			}

			return $items;
		}

		/**
		 * Open wrapper for member/home templates
		 */
		public function before_member_home_content() {
			?>
			<div class="bbp-wrapper">
			<?php
		}

		/**
		 * Close wrapper for member/home templates
		 */
		public function after_member_home_content() {
			?>
			</div>
			<?php
		}

		/**
		 * Open wrapper for group/home templates
		 */
		public function before_group_home_content() {
			?>
			<div class="bbp-wrapper">
			<?php
		}

		/**
		 * Close wrapper for group/home templates
		 */
		public function after_group_home_content() {
			?>
			</div>
			<?php
		}

		/**
		 * Add additional data to profile extended box
		 *
		 * @param string $extended_html Current html
		 * @param int    $user_id       User ID
		 *
		 * @return string
		 */
		public function bbp_author_extended_data( $extended_html, $user_id ) {
			$profile_socials = $this->get_xprofile_socials( $user_id );

			if ( ! empty( $profile_socials ) ) {
				$website = $socials = $html = '';
				$website_field_name = strtolower( apply_filters( 'bbp_website_field_name', 'website' ) );
				$email_field_name = strtolower( apply_filters( 'bbp_email_field_name', 'email' ) );

				foreach ( $profile_socials as $name => $data ) {
					if ( $data[ 'bb_key' ] == $website_field_name ) {
						$website = $data[ 'field_data' ];
						continue;
					}
					if ( $data[ 'bb_key' ] == $email_field_name ) {
						$data[ 'field_data' ] = sprintf( 'mailto:%s', str_replace( 'mailto:', '', $data[ 'field_data' ] ) );
					}
					$socials .= sprintf( '<li><a class="bb-ui-icon-%1$s" href="%2$s" title="%3$s" target="_blank" rel="nofollow noopener"></a></li>', $data[ 'icon' ], $data[ 'field_data' ], $name );
				}

				if ( $website ) {
					$html .= sprintf( '<a class="website-url" href="%1$s" target="_blank" rel="nofollow noopener">%1$s</a>', $website );
				}

				if ( $socials ) {
					$html .= sprintf( '<div class="%1$s"><ul>%2$s</ul></div>', boombox_is_amp() ? 'bb-social default' : 'social', $socials );
				}

				if ( $html ) {
					$extended_html .= sprintf( '<div class="auth-references">%s</div>', $html );
				}
			}

			return $extended_html;
		}

		/**
		 * Edit profile description
		 *
		 * @param string $bio     Current description
		 * @param int    $user_id User ID
		 *
		 * @return string
		 */
		public function edit_author_biography( $bio, $user_id ) {
			if( bp_is_active( 'xprofile' ) ) {
				$field_name = apply_filters( 'boombox/buddypress/bio_field_name', 'biography' );
				$xprofile_field_id = xprofile_get_field_id_from_name( $field_name );
				if ( $xprofile_field_id ) {
					$xprofile_field_data = xprofile_get_field_data( $xprofile_field_id, $user_id );
					if ( $xprofile_field_data ) {
						return wpautop( $xprofile_field_data, false );
					}
				}
			}

			return $bio;
		}

		/**
		 * Color scheme support
		 *
		 * @param string $css Current stylesheet data
		 *
		 * @return string
		 * @see boombox_global_style_css() for available colors
		 */
		public function bbp_color_scheme_styles( $css ) {
			$css .= '

                /* *** Buddypress Plugin *** */

                /* -link color */
                #buddypress .visibility-toggle-link {
                  color:%21$s
                }

                /* Base Text Color */
                .buddypress.widget .item-title,
                .buddypress.widget .item-options a.selected, 
                .buddypress.widget .item-options a:hover,
                .header .account-box .notifications-list.menu ul li a, #buddypress ul.button-nav li a,
                 #buddypress #object-nav li.current>a, #buddypress #object-nav li.selected>a,
                 #buddypress #object-nav li:hover>a, #buddypress #object-nav li:hover>span,
                 #buddypress .bbp-main-nav li.current>a, #buddypress .bbp-main-nav li.selected>a,
                 #buddypress .bbp-main-nav li:hover>a, #buddypress .bbp-main-nav li:hover>span,
                 #buddypress #subnav li>a,
                 #buddypress table td a,
                 /* Pagination */
                 #buddypress .pagination-links a,
				 #buddypress .pagination-links span,
				 #buddypress .pagination a,
				 #buddypress .pagination span,
				.gfy-bp-component .pagination a,
				.gfy-bp-component .pagination span,
				.gfy-bp-component .pagination span.dots,
				 #buddypress #latest-update {
                    color: %8$s;
                }
                #buddypress #object-nav ul li:before, #buddypress .bbp-main-nav ul li:before {
                	background-color: %8$s;
                }

                /* Heading Text Color */
                #buddypress table th,
                #buddypress .item-header a,
                #buddypress .activity-header a,
                #buddypress .acomment-header a,
                #buddypress #invite-list label,
                #buddypress .standard-form label,
                #buddypress .standard-form legend,
                #buddypress .standard-form span.label,
                #buddypress .messages-notices .thread-from a,
                #buddypress .messages-notices .thread-info a,
                #buddypress #item-header-content .group-name,
                #buddypress #item-header-content .member-name,
                #buddypress .message-metadata a {
                    color: %10$s;
                }

                /* Secondary Text Color */
                #buddypress .pagination .pag-count,
                #buddypress .notification-description a,
                #buddypress #item-header-content .group-activity,
                #buddypress #item-header-content .member-activity,
                #buddypress #register-page .field-visibility-settings-toggle, #buddypress #register-page .wp-social-login-connect-with, #buddypress .field-visibility-settings-close {
                    color: %9$s;
                }

                #buddypress #register-page ::-webkit-input-placeholder, #buddypress #activate-page ::-webkit-input-placeholder {
                    color: %9$s;
                }
                #buddypress #register-page ::-moz-placeholder, #buddypress #activate-page ::-moz-placeholder {
                    color: %9$s;
                }
                #buddypress #register-page :-ms-input-placeholder, #buddypress #activate-page :-ms-input-placeholder {
                    color: %9$s;
                }
                #buddypress #register-page :-moz-placeholder, #buddypress #activate-page :-moz-placeholder {
                    color: %9$s;
                }

                #buddypress table .bbp-checkbox-check {
                	border-color: %9$s;
                }

                /* Global Border Color */
                #buddypress table td,
                #buddypress table th,
                #buddypress .bbp-item-info,
                #buddypress .activity-list li,
                #buddypress .activity-meta a,
                #buddypress .acomment-options a,
                #buddypress .item-list .item-action a,
                #buddypress .bbp-radio-check,
                #buddypress .bbp-checkbox-check,
                #buddypress .standard-form .submit,
                #buddypress #invite-list li,
                #buddypress #invite-list li:first-child,

                #buddypress #blogs-list,
                #buddypress #groups-list,
                #buddypress #member-list,
                #buddypress #friend-list,
                #buddypress #admins-list,
                #buddypress #mods-list,
                #buddypress #members-list,
                #buddypress #request-list,
                #buddypress #group-list,

                #buddypress #blogs-list li,
                #buddypress #groups-list li,
                #buddypress #member-list li,
                #buddypress #friend-list li,
                #buddypress #admins-list li,
                #buddypress #mods-list li,
                #buddypress #members-list li,
                #buddypress #request-list li,
                #buddypress #group-list li,

                .buddypress.widget .item-options,
                #buddypress .vp_post_entry,
                #buddypress .vp_post_entry .col-lg-3 .entry-footer .post-edit-link,

                #buddypress #register-page .standard-form .submit,

                /* Pagination */
                #buddypress .pagination-links a,
				#buddypress .pagination-links span.current,
				#buddypress .pagination a,
				#buddypress .pagination span.current,
				.gfy-bp-component .pagination a,
				.gfy-bp-component .pagination span.current {
                    border-color: %13$s;
                }

                .bp-avatar-nav ul,
                .bp-avatar-nav ul.avatar-nav-items li.current {
                    border-color: %13$s;
                }

                .bp-avatar-nav ul.avatar-nav-items li.current {
                    background-color: %13$s;
                }

                /* -secondary components bg color */
                #buddypress .field-visibility-settings,
                table.bbp-table-responsive tbody tr:nth-child(2n+1),
                #buddypress .acomment-bubble, #buddypress .activity-bubble, #buddypress .item-bubble,
                #buddypress #latest-update,
                #buddypress #group-create-tabs li a, #buddypress #group-create-tabs li span,
                #buddypress #create-group-form #header-cover-image {
                    background-color: %14$s;
                }
                /* Pagination */
                #buddypress .pagination-links a:hover,
				#buddypress .pagination-links span.current,
				#buddypress .pagination a:hover,
				#buddypress .pagination span.current,
				.gfy-bp-component .pagination a:hover,
				.gfy-bp-component .pagination span.current {
					background-color: %14$s!important;
				}

				/* - Secondary components text color */
				#buddypress #group-create-tabs li a, #buddypress #group-create-tabs li span {
					color:%22$s;
				}

                /* Primary Color */
                #buddypress button,
                #buddypress input[type=button],
                #buddypress input[type=reset],
                #buddypress input[type=submit],
                #buddypress ul.button-nav li.current a, #buddypress ul.button-nav li:hover a,
                #buddypress a.bp-title-button,
                #buddypress .comment-reply-link,
                #buddypress .activity-list .load-more a,
                #buddypress .activity-list .load-newest a {
                    background-color: %6$s;
                }
                .header .account-box .notifications-list.menu ul li a:hover {
                    color: %6$s;
                }
                .gfy-tabs .tab-menu-item.active {
                	 border-color: %6$s;
				}

                /* Primary Text */
                #buddypress button,
                #buddypress input[type=button],
                #buddypress input[type=reset],
                #buddypress input[type=submit],
                #buddypress ul.button-nav li.current a, #buddypress ul.button-nav li:hover a,
                #buddypress a.bp-title-button,
                #buddypress .comment-reply-link,
                #buddypress .activity-list .load-more a,
                #buddypress .activity-list .load-newest a,
                #buddypress #register-page input[type=submit], #buddypress #activate-page input[type=submit],
                #buddypress ul.button-nav li.current a, #buddypress ul.button-nav li:hover a {
                    color: %7$s;
                }

                /* -content bg color */
                #buddypress  #register-page .field-visibility-settings {
                  background-color: %5$s;
                }

                /* -border-radius */
                #buddypress  #register-page .field-visibility-settings {
                  -webkit-border-radius: %11$s;
                  -moz-border-radius: %11$s;
                  border-radius: %11$s;
                 }

                /* --border-radius inputs, buttons */
                #buddypress #register-page input[type=submit], #buddypress #activate-page input[type=submit] ,
                #buddypress .bb-form-block input, #buddypress .bb-form-block textarea, #buddypress .bb-form-block select {
                  -webkit-border-radius: %12$s;
                  -moz-border-radius: %12$s;
                  border-radius: %12$s;
                }

				/* *** Gamify Plugin *** */

				/* - Border-radius - */
				.widget_gfy_leaderboard .leaderboard-item,
				.gfy-featured-author-content,.widget_gfy-featured-author .gfy-count-list .gfy-item {
				  -webkit-border-radius: %11$s;
					 -moz-border-radius: %11$s;
						  border-radius: %11$s;
				}

				/* - Secondary components bg color - */
				.widget_gfy_leaderboard .leaderboard-item,
				.gfy-bp-achievements .achievements-wrapper .col,
				.gfy-featured-author-content,
				.gfy-popup-body .gfy-body {
					background-color: %14$s;
				}

				/* - Secondary components text color */
				.widget_gfy_leaderboard .leaderboard-item,
				.widget_gfy-featured-author .gfy-name {
					color:%22$s;
				}
				
                .widget_gfy-featured-author .gfy-cover {
				    background-color: %22$s;
				}

				/* - Secondary text color - */
				.widget_gfy_leaderboard .leaderboard-item .item-number, .widget_gfy_leaderboard .leaderboard-item .item-points,
				.gfy-icon-btn, .gfy-close .gfy-icon,
				.gfy-bp-component .gfy-rank-item .rank-desc,
				.widget_gfy-featured-author .gfy-count-list .gfy-item .gfy-count-name,
				.widget_gfy-featured-author .gfy-description,
	            .widget_gfy-featured-author .gfy-social,
	            .gfy-tabs .tab-menu-item a {
				  color: %9$s;
				}

				/* - Base text color - */
				.widget_gfy_leaderboard .leaderboard-item .item-title,
				.gfy-bp-achievements .achievements-wrapper .rank-level,
				.widget_gfy-featured-author .gfy-count-list .gfy-item .gfy-count,
				.gfy-tabs .tab-menu-item.active a {
				  color: %8$s;
				}

				/* - Heading text color - */
				#buddypress .gfy-bp-leaderboard table a, .gfy-bp-leaderboard table a {
					color: %10$s;
				}

				/* - Content bg color - */
				.gfy-popup-body,.gfy-popup-body .gfy-badge-title,
				 .widget_gfy-featured-author .gfy-count-list .gfy-item {
					background-color: %5$s;
				}

            ';

			return $css;
		}

		/**
		 * Social Media Icons based on the profile user info
		 */
		public function member_social_xprofile_data() {

			$profile_socials = $this->get_xprofile_socials();

			$html = '';
			if ( ! empty( $profile_socials ) ) {
				$email_field_name = strtolower( apply_filters( 'bbp_email_field_name', 'email' ) );
				$html .= '<ul class="bbp-social">';

				foreach ( $profile_socials as $name => $data ) {
					if ( $data[ 'bb_key' ] == $email_field_name ) {
						$data[ 'field_data' ] = sprintf( 'mailto:%s', str_replace( 'mailto:', '', $data[ 'field_data' ] ) );
					}

					$html .= sprintf( '<li><a href="%1$s" title="%2$s" target="_blank" rel="nofollow noopener"><span class="bb-icon bb-ui-icon-%3$s"></span></a></li>', $data[ 'field_data' ], $name, $data[ 'icon' ] );
				}
				$html .= '</ul>';
			}

			echo $html;
		}

		/**
		 * Get buildin social options from "boombox-theme-extensions" plugin
		 *
		 * @return array|bool
		 */
		private function get_builtin_socials() {
			if ( class_exists( 'Boombox_Social' ) ) {
				return Boombox_Social::social_default_items();
			}

			return false;
		}

		/**
		 * Get Extended profile social data
		 * @return array
		 */
		private function get_xprofile_socials( $user_id = false ) {
			if ( ! $user_id ) {
				$user_id = bp_displayed_user_id();
			}

			$return = array();
			if ( $user_id && bp_is_active( 'xprofile' ) ) {
				$profile_data = BP_XProfile_ProfileData::get_all_for_user( $user_id );
				$socials = array_merge(
					(array)$this->get_builtin_socials(),
					array(
						'vkontakte'     => array( 'icon' => 'vk', 'title' => 'Vkontakte', 'default' => '' ),
						'odnoklassniki' => array(
							'icon'    => 'odnoklassniki',
							'title'   => 'Vkontakte',
							'default' => '',
						),
						'stackoverflow' => array(
							'icon'    => 'stack-overflow',
							'title'   => 'Stack Overflow',
							'default' => '',
						),
						'website'       => array( 'icon' => 'chain', 'title' => 'Website', 'default' => '' ),
					),
					apply_filters( 'bbp_additional_socials', array() )
				);

				if ( $socials ) {
					foreach ( $profile_data as $human_key => $data ) {

						$key = strtr( strtolower( $human_key ), array(
							'-' => '',
							'_' => '',
							' ' => '',
							'+' => 'plus',
						) );
						if ( array_key_exists( $key, $socials ) ) {
							$icon = ( isset( $socials[ $key ][ 'icon' ] ) && $socials[ $key ][ 'icon' ] ) ? $socials[ $key ][ 'icon' ] : false;
							if ( $icon ) {
								$return[ $human_key ] = array_merge( $data, array(
									'icon'   => $icon,
									'bb_key' => $key,
								) );
							}
						}
					}
				}
			}

			return $return;

		}

		/**
		 * Notification settings for 'activity' screen
		 */
		public function bp_activity_screen_notification_settings() {
			if ( bp_activity_do_mentions() ) {
				if ( ! $mention = bp_get_user_meta( bp_displayed_user_id(), 'notification_activity_new_mention', true ) ) {
					$mention = 'yes';
				}
			}

			if ( ! $reply = bp_get_user_meta( bp_displayed_user_id(), 'notification_activity_new_reply', true ) ) {
				$reply = 'yes';
			}

			?>

			<table class="notification-settings" id="activity-notification-settings">
				<thead>
				<tr>
					<th class="title"><?php _e( 'Activity', 'buddypress' ) ?></th>
					<th class="yes"><?php _e( 'Yes', 'buddypress' ) ?></th>
					<th class="no"><?php _e( 'No', 'buddypress' ) ?></th>
				</tr>
				</thead>

				<tbody>
				<?php if ( bp_activity_do_mentions() ) : ?>
					<tr id="activity-notification-settings-mentions">
						<td><?php printf( __( 'A member mentions you in an update using "@%s"', 'buddypress' ), bp_core_get_username( bp_displayed_user_id() ) ) ?></td>
						<td class="yes">
							<label for="notification-activity-new-mention-yes" class="bbp-radio">
								<input type="radio" name="notifications[notification_activity_new_mention]"
								       id="notification-activity-new-mention-yes"
								       value="yes" <?php checked( $mention, 'yes', true ) ?>/>
								<span class="bbp-radio-check"></span>
								<span class="bp-screen-reader-text"><?php
									/* translators: accessibility text */
									_e( 'Yes, send email', 'buddypress' );
									?></span>
							</label>
						</td>
						<td class="no">
							<label for="notification-activity-new-mention-no" class="bbp-radio">
								<input type="radio" name="notifications[notification_activity_new_mention]"
								       id="notification-activity-new-mention-no"
								       value="no" <?php checked( $mention, 'no', true ) ?>/>
								<span class="bbp-radio-check"></span>
								<span class="bp-screen-reader-text"><?php
									/* translators: accessibility text */
									_e( 'No, do not send email', 'buddypress' );
									?></span>
							</label>
						</td>
					</tr>
				<?php endif; ?>

				<tr id="activity-notification-settings-replies">
					<td><?php _e( "A member replies to an update or comment you've posted", 'buddypress' ) ?></td>
					<td class="yes">
						<label for="notification-activity-new-reply-yes" class="bbp-radio">
							<input type="radio" name="notifications[notification_activity_new_reply]"
							       id="notification-activity-new-reply-yes"
							       value="yes" <?php checked( $reply, 'yes', true ) ?>/>
							<span class="bbp-radio-check"></span>
							<span class="bp-screen-reader-text"><?php
								/* translators: accessibility text */
								_e( 'Yes, send email', 'buddypress' );
								?></span>
						</label>
					</td>
					<td class="no">
						<label for="notification-activity-new-reply-no" class="bbp-radio">
							<input type="radio" name="notifications[notification_activity_new_reply]"
							       id="notification-activity-new-reply-no"
							       value="no" <?php checked( $reply, 'no', true ) ?>/>
							<span class="bbp-radio-check"></span>
							<span class="bp-screen-reader-text"><?php
								/* translators: accessibility text */
								_e( 'No, do not send email', 'buddypress' );
								?></span>
						</label>
					</td>
				</tr>

				<?php

				/**
				 * Fires inside the closing </tbody> tag for activity screen notification settings.
				 *
				 * @since 1.2.0
				 */
				do_action( 'bp_activity_screen_notification_settings' ) ?>
				</tbody>
			</table>
		<?php }

		/**
		 * Notification settings for 'friends' screen
		 */
		public function friends_screen_notification_settings() {
			if ( ! $send_requests = bp_get_user_meta( bp_displayed_user_id(), 'notification_friends_friendship_request', true ) )
				$send_requests = 'yes';

			if ( ! $accept_requests = bp_get_user_meta( bp_displayed_user_id(), 'notification_friends_friendship_accepted', true ) )
				$accept_requests = 'yes'; ?>

			<table class="notification-settings" id="friends-notification-settings">
				<thead>
				<tr>
					<th class="title"><?php _ex( 'Friends', 'Friend settings on notification settings page', 'buddypress' ) ?></th>
					<th class="yes"><?php _e( 'Yes', 'buddypress' ) ?></th>
					<th class="no"><?php _e( 'No', 'buddypress' ) ?></th>
				</tr>
				</thead>

				<tbody>
				<tr id="friends-notification-settings-request">
					<td><?php _ex( 'A member sends you a friendship request', 'Friend settings on notification settings page', 'buddypress' ) ?></td>
					<td class="yes">
						<label for="notification-friends-friendship-request-yes" class="bbp-radio">
							<input type="radio" name="notifications[notification_friends_friendship_request]"
							       id="notification-friends-friendship-request-yes"
							       value="yes" <?php checked( $send_requests, 'yes', true ) ?>/>
							<span class="bbp-radio-check"></span>
							<span class="bp-screen-reader-text"><?php
								/* translators: accessibility text */
								_e( 'Yes, send email', 'buddypress' );
								?></span>
						</label>
					</td>
					<td class="no">
						<label for="notification-friends-friendship-request-no" class="bbp-radio">
							<input type="radio" name="notifications[notification_friends_friendship_request]"
							       id="notification-friends-friendship-request-no"
							       value="no" <?php checked( $send_requests, 'no', true ) ?>/>
							<span class="bbp-radio-check"></span>
							<span class="bp-screen-reader-text"><?php
								/* translators: accessibility text */
								_e( 'No, do not send email', 'buddypress' );
								?></span>
						</label>
					</td>
				</tr>
				<tr id="friends-notification-settings-accepted">
					<td><?php _ex( 'A member accepts your friendship request', 'Friend settings on notification settings page', 'buddypress' ) ?></td>
					<td class="yes">
						<label for="notification-friends-friendship-accepted-yes" class="bbp-radio">
							<input type="radio" name="notifications[notification_friends_friendship_accepted]"
							       id="notification-friends-friendship-accepted-yes"
							       value="yes" <?php checked( $accept_requests, 'yes', true ) ?>/>
							<span class="bbp-radio-check"></span>
							<span class="bp-screen-reader-text"><?php
								/* translators: accessibility text */
								_e( 'Yes, send email', 'buddypress' );
								?></span>
						</label>
					</td>
					<td class="no">
						<label for="notification-friends-friendship-accepted-no" class="bbp-radio">
							<input type="radio" name="notifications[notification_friends_friendship_accepted]"
							       id="notification-friends-friendship-accepted-no"
							       value="no" <?php checked( $accept_requests, 'no', true ) ?>/>
							<span class="bbp-radio-check"></span>
							<span class="bp-screen-reader-text"><?php
								/* translators: accessibility text */
								_e( 'No, do not send email', 'buddypress' );
								?></span>
						</label>
					</td>
				</tr>

				<?php

				/**
				 * Fires after the last table row on the friends notification screen.
				 *
				 * @since 1.0.0
				 */
				do_action( 'friends_screen_notification_settings' ); ?>

				</tbody>
			</table>

			<?php
		}

		/**
		 * Notification settings for 'messages' screen
		 */
		public function messages_screen_notification_settings() {
			if ( bp_action_variables() ) {
				bp_do_404();

				return;
			}

			if ( ! $new_messages = bp_get_user_meta( bp_displayed_user_id(), 'notification_messages_new_message', true ) ) {
				$new_messages = 'yes';
			} ?>

			<table class="notification-settings" id="messages-notification-settings">
				<thead>
				<tr>
					<th class="title"><?php _e( 'Messages', 'buddypress' ) ?></th>
					<th class="yes"><?php _e( 'Yes', 'buddypress' ) ?></th>
					<th class="no"><?php _e( 'No', 'buddypress' ) ?></th>
				</tr>
				</thead>

				<tbody>
				<tr id="messages-notification-settings-new-message">
					<td><?php _e( 'A member sends you a new message', 'buddypress' ) ?></td>
					<td class="yes">
						<label for="notification-messages-new-messages-yes" class="bbp-radio">
							<input type="radio" name="notifications[notification_messages_new_message]"
							       id="notification-messages-new-messages-yes"
							       value="yes" <?php checked( $new_messages, 'yes', true ) ?>/>
							<span class="bbp-radio-check"></span>
							<span class="bp-screen-reader-text"><?php
								/* translators: accessibility text */
								_e( 'Yes, send email', 'buddypress' );
								?></span>
						</label>
					</td>
					<td class="no">
						<label for="notification-messages-new-messages-no" class="bbp-radio">
							<input type="radio" name="notifications[notification_messages_new_message]"
							       id="notification-messages-new-messages-no"
							       value="no" <?php checked( $new_messages, 'no', true ) ?>/>
							<span class="bbp-radio-check"></span>
							<span class="bp-screen-reader-text"><?php
								/* translators: accessibility text */
								_e( 'No, do not send email', 'buddypress' );
								?></span>
						</label>
					</td>
				</tr>

				<?php

				/**
				 * Fires inside the closing </tbody> tag for messages screen notification settings.
				 *
				 * @since 1.0.0
				 */
				do_action( 'messages_screen_notification_settings' ); ?>
				</tbody>
			</table>

			<?php
		}

		/**
		 * Notification settings for 'groups' screen
		 */
		public function groups_screen_notification_settings() {
			if ( ! $group_invite = bp_get_user_meta( bp_displayed_user_id(), 'notification_groups_invite', true ) )
				$group_invite = 'yes';

			if ( ! $group_update = bp_get_user_meta( bp_displayed_user_id(), 'notification_groups_group_updated', true ) )
				$group_update = 'yes';

			if ( ! $group_promo = bp_get_user_meta( bp_displayed_user_id(), 'notification_groups_admin_promotion', true ) )
				$group_promo = 'yes';

			if ( ! $group_request = bp_get_user_meta( bp_displayed_user_id(), 'notification_groups_membership_request', true ) )
				$group_request = 'yes';

			if ( ! $group_request_completed = bp_get_user_meta( bp_displayed_user_id(), 'notification_membership_request_completed', true ) ) {
				$group_request_completed = 'yes';
			}
			?>

			<table class="notification-settings" id="groups-notification-settings">
				<thead>
				<tr>
					<th class="title"><?php _ex( 'Groups', 'Group settings on notification settings page', 'buddypress' ) ?></th>
					<th class="yes"><?php _e( 'Yes', 'buddypress' ) ?></th>
					<th class="no"><?php _e( 'No', 'buddypress' ) ?></th>
				</tr>
				</thead>

				<tbody>
				<tr id="groups-notification-settings-invitation">
					<td><?php _ex( 'A member invites you to join a group', 'group settings on notification settings page', 'buddypress' ) ?></td>
					<td class="yes">
						<label for="notification-groups-invite-yes" class="bbp-radio">
							<input type="radio" name="notifications[notification_groups_invite]"
							       id="notification-groups-invite-yes"
							       value="yes" <?php checked( $group_invite, 'yes', true ) ?>/>
							<span class="bbp-radio-check"></span>
							<span class="bp-screen-reader-text"><?php
								/* translators: accessibility text */
								_e( 'Yes, send email', 'buddypress' );
								?></span>
						</label>
					</td>
					<td class="no">
						<label for="notification-groups-invite-no" class="bbp-radio">
							<input type="radio" name="notifications[notification_groups_invite]"
							       id="notification-groups-invite-no"
							       value="no" <?php checked( $group_invite, 'no', true ) ?>/>
							<span class="bbp-radio-check"></span>
							<span class="bp-screen-reader-text"><?php
								/* translators: accessibility text */
								_e( 'No, do not send email', 'buddypress' );
								?></span>
						</label>
					</td>
				</tr>
				<tr id="groups-notification-settings-info-updated">
					<td><?php _ex( 'Group information is updated', 'group settings on notification settings page', 'buddypress' ) ?></td>
					<td class="yes">
						<label for="notification-groups-group-updated-yes" class="bbp-radio">
							<input type="radio" name="notifications[notification_groups_group_updated]"
							       id="notification-groups-group-updated-yes"
							       value="yes" <?php checked( $group_update, 'yes', true ) ?>/>
							<span class="bbp-radio-check"></span>
							<span class="bp-screen-reader-text"><?php
								/* translators: accessibility text */
								_e( 'Yes, send email', 'buddypress' );
								?></span>
						</label>
					</td>
					<td class="no">
						<label for="notification-groups-group-updated-no" class="bbp-radio">
							<input type="radio" name="notifications[notification_groups_group_updated]"
							       id="notification-groups-group-updated-no"
							       value="no" <?php checked( $group_update, 'no', true ) ?>/>
							<span class="bbp-radio-check"></span>
							<span class="bp-screen-reader-text"><?php
								/* translators: accessibility text */
								_e( 'No, do not send email', 'buddypress' );
								?></span>
						</label>
					</td>
				</tr>
				<tr id="groups-notification-settings-promoted">
					<td><?php _ex( 'You are promoted to a group administrator or moderator', 'group settings on notification settings page', 'buddypress' ) ?></td>
					<td class="yes">
						<label for="notification-groups-admin-promotion-yes" class="bbp-radio">
							<input type="radio" name="notifications[notification_groups_admin_promotion]"
							       id="notification-groups-admin-promotion-yes"
							       value="yes" <?php checked( $group_promo, 'yes', true ) ?>/>
							<span class="bbp-radio-check"></span>
							<span class="bp-screen-reader-text"><?php
								/* translators: accessibility text */
								_e( 'Yes, send email', 'buddypress' );
								?></span>
						</label>
					</td>
					<td class="no">
						<label for="notification-groups-admin-promotion-no" class="bbp-radio">
							<input type="radio" name="notifications[notification_groups_admin_promotion]"
							       id="notification-groups-admin-promotion-no"
							       value="no" <?php checked( $group_promo, 'no', true ) ?>/>
							<span class="bbp-radio-check"></span>
							<span class="bp-screen-reader-text"><?php
								/* translators: accessibility text */
								_e( 'No, do not send email', 'buddypress' );
								?></span>
						</label>
					</td>
				</tr>
				<tr id="groups-notification-settings-request">
					<td><?php _ex( 'A member requests to join a private group for which you are an admin', 'group settings on notification settings page', 'buddypress' ) ?></td>
					<td class="yes">
						<label for="notification-groups-membership-request-yes" class="bbp-radio">
							<input type="radio" name="notifications[notification_groups_membership_request]"
							       id="notification-groups-membership-request-yes"
							       value="yes" <?php checked( $group_request, 'yes', true ) ?>/>
							<span class="bbp-radio-check"></span>
							<span class="bp-screen-reader-text"><?php
								/* translators: accessibility text */
								_e( 'Yes, send email', 'buddypress' );
								?></span>
						</label>
					</td>
					<td class="no">
						<label for="notification-groups-membership-request-no" class="bbp-radio">
							<input type="radio" name="notifications[notification_groups_membership_request]"
							       id="notification-groups-membership-request-no"
							       value="no" <?php checked( $group_request, 'no', true ) ?>/>
							<span class="bbp-radio-check"></span>
							<span class="bp-screen-reader-text"><?php
								/* translators: accessibility text */
								_e( 'No, do not send email', 'buddypress' );
								?></span>
						</label>
					</td>
				</tr>
				<tr id="groups-notification-settings-request-completed">
					<td><?php _ex( 'Your request to join a group has been approved or denied', 'group settings on notification settings page', 'buddypress' ) ?></td>
					<td class="yes">
						<label for="notification-groups-membership-request-completed-yes" class="bbp-radio">
							<input type="radio" name="notifications[notification_membership_request_completed]"
							       id="notification-groups-membership-request-completed-yes"
							       value="yes" <?php checked( $group_request_completed, 'yes', true ) ?>/>
							<span class="bbp-radio-check"></span>
							<span class="bp-screen-reader-text"><?php
								/* translators: accessibility text */
								_e( 'Yes, send email', 'buddypress' );
								?></span>
						</label>
					</td>
					<td class="no">
						<label for="notification-groups-membership-request-completed-no" class="bbp-radio">
							<input type="radio" name="notifications[notification_membership_request_completed]"
							       id="notification-groups-membership-request-completed-no"
							       value="no" <?php checked( $group_request_completed, 'no', true ) ?>/>
							<span class="bbp-radio-check"></span>
							<span class="bp-screen-reader-text"><?php
								/* translators: accessibility text */
								_e( 'No, do not send email', 'buddypress' );
								?></span>
						</label>
					</td>
				</tr>

				<?php

				/**
				 * Fires at the end of the available group settings fields on Notification Settings page.
				 *
				 * @since 1.0.0
				 */
				do_action( 'groups_screen_notification_settings' ); ?>

				</tbody>
			</table>

			<?php
		}

		/**
		 * Render user notifications box
		 *
		 * @param array $options Template Options
		 * @param array $helpers  Template Helpers
		 */
		public function user_notifications( $options, $helpers ) {
			if ( ! boombox_is_auth_allowed() ) {
				return;
			}
			if ( ! is_user_logged_in() ) {
				return;
			}
			if ( ! bp_is_active( 'notifications' ) ) {
				return;
			}

			$user_id = bp_loggedin_user_id();

			$max_show = 5;
			$count = bp_notifications_get_unread_notification_count( $user_id );
			$notifications = bp_notifications_get_notifications_for_user( $user_id, 'string' );

			$all_notifications_url = esc_url( bp_loggedin_user_domain() . bp_get_notifications_slug() );
			$class = $helpers['header']->get_component_location();
			?>
			<div class="header-item user-notifications bb-icn-count bb-toggle  pos-<?php echo $class; ?>">
				<a class="icn-link bb-header-icon element-toggle only-mobile <?php if ( $count ) echo 'has-count' ?>"
				   href="<?php echo $all_notifications_url; ?>" data-toggle=".user-notifications .menu">
					<i class="bb-icon bb-ui-icon-notification"></i>
					<?php if ( $count ) { ?>
						<span class="count"><?php echo bp_core_number_format( $count ); ?></span>
					<?php } ?>
				</a>

				<?php if ( (bool)$notifications ) { ?>
					<div class="notifications-list menu bb-header-dropdown toggle-content">
						<ul>
							<?php foreach ( $notifications as $index => $notification ) { ?>
								<?php if ( $index >= $max_show ) break; ?>
								<li><?php echo $notification; ?></li>
							<?php } ?>
						</ul>
						<?php if ( $count > $max_show ) { ?>
							<a href="<?php echo $all_notifications_url; ?>"
							   class="notifications-more"><?php esc_html_e( 'View all', 'buddypress' ); ?></a>
						<?php } ?>
					</div>
				<?php } ?>
			</div>
			<?php
		}

		/**
		 * Add menu item to buddypress navigation
		 *
		 * @param array $menu_items Current menu items
		 * @param array $args       Array of arguments for the menu.
		 *
		 * @return array
		 */
		public function nav_menu_objects( $menu_items, $args ) {

			$user_id = bp_loggedin_user_id();

			if ( $user_id ) {
				$menu = new stdClass;
				$menu->class = array( 'menu-parent' );
				$menu->css_id = 'logout';
				$menu->link = wp_logout_url( bp_get_requested_url() );
				$menu->name = esc_html__( 'Logout', 'boddypress' );
				$menu->parent = 0;

				$menu_items[] = $menu;
			}

			return $menu_items;
		}

		/**
		 * Add user meta description to wordpress header
		 */
		public function generate_user_meta_description() {
			if ( function_exists( 'bp_is_user' ) && bp_is_user() ) {
				$meta_description = boombox_get_user_meta_description( bp_displayed_user_id() );
				printf( '<meta name="description" content="%1$s" />', esc_attr( $meta_description ) );
			}
		}

		/**
		 * Edit user account registration callback
		 *
		 * @param callable $callback Current callback
		 *
		 * @return callable
		 */
		public function edit_registration_callback( $callback ) {

			if ( boombox_get_theme_option( 'buddypress_account_activation' ) ) {
				$callback = array( $this, 'register_user' );
			}

			return $callback;
		}

		/**
		 * Callback to overwrite theme default registration functionality
		 *
		 * @param array $account Array containing user registration data
		 *
		 * @return array
		 */
		public function register_user( $account ) {

			$has_error = false;
			$login = ( isset( $account[ 'user_login' ] ) && $account[ 'user_login' ] ) ? $account[ 'user_login' ] : '';
			$email = ( isset( $account[ 'user_email' ] ) && $account[ 'user_email' ] ) ? $account[ 'user_email' ] : '';
			$password = ( isset( $account[ 'user_pass' ] ) && $account[ 'user_pass' ] ) ? $account[ 'user_pass' ] : '';

			$result = bp_core_validate_user_signup( $login, $email );

			if ( ! empty( $result[ 'errors' ]->errors[ 'user_name' ] ) ) {
				$has_error = true;
				$message = $result[ 'errors' ]->errors[ 'user_name' ][ 0 ];
			}

			if ( ! empty( $result[ 'errors' ]->errors[ 'user_email' ] ) ) {
				$has_error = true;
				$message = $result[ 'errors' ]->errors[ 'user_email' ][ 0 ];
			}

			if ( ! $password ) {
				$has_error = true;
				$message = esc_html__( 'Password cannot be empty', 'boombox' );
			}

			if ( ! $has_error ) {
				$usermeta = array(
					'password' => wp_hash_password( $password ),
				);
				if ( is_multisite() ) {
					$usermeta = array_merge( $usermeta, array(
						'add_to_blog' => get_current_blog_id(),
						'new_role'    => get_option( 'default_role', 'contributor' ),
					) );
				}
				$user_id = bp_core_signup_user( $login, $password, $email, $usermeta );

				if ( is_wp_error( $user_id ) ) {
					$has_error = true;
					$message = $user_id->get_error_message();
				} else {
					$message = esc_html__( 'Registration successful', 'boombox' );
				}
			}

			return array(
				'has_error'       => $has_error,
				'response'        => $message,
				'need_activation' => bp_registration_needs_activation(),
			);


		}

		/**
		 * Try to get inactive user account data
		 *
		 * @param int    $user             User ID
		 * @param string $user_login_email User login or email addess
		 * @param string $user_password    User password
		 *
		 * @return int|WP_Error
		 */
		public function check_user_on_auth_login( $user, $user_login_email, $user_password ) {

			$search_key = false;

			if ( $user ) {
				if ( BP_Signup::check_user_status( $user ) != 2 ) {
					$search_key = 'user_login';
					$user_login_email = $user->user_login;
				}
			} else if ( ! is_wp_error( $user ) ) {

				if ( is_email( $user_login_email ) ) {
					$search_key = 'usersearch';
					$user_login_email = sanitize_email( $user_login_email );
				} else {
					$search_key = 'user_login';
					$user_login_email = sanitize_user( $user_login_email );
				}
			}

			if ( $search_key ) {

				$signups = BP_Signup::get( array(
					$search_key => $user_login_email,
				) );

				// No signup or more than one, something is wrong. Let's bail.
				if ( $signups[ 'signups' ] && ( ! empty( $signups[ 'signups' ][ 0 ] ) || $signups[ 'total' ] <= 1 ) ) {

					// Unactivated user account found!
					// Set up the feedback message.
					$signup_id = $signups[ 'signups' ][ 0 ]->signup_id;

					$nonce = wp_create_nonce( sprintf( 'boombox-bp-%d-resend-activation', $signup_id ) );
					$resend_string = sprintf( __( 'If you have not received an email yet, <a href="#" id="bb-bp-btn-resend" data-id="%1$d" data-token="%2$s">click here to resend it</a>.', 'boombox' ), $signup_id, $nonce );

					$user = new WP_Error( 'bp_account_not_activated', __( 'Your account has not been activated. Check your email for the activation link. ', 'boombox' ) . $resend_string );

				}
			}

			return $user;
		}

		/**
		 * Ajax callback to resend activation email
		 */
		public static function resend_activation_email() {
			$signup_id = isset( $_POST[ 'id' ] ) && $_POST[ 'id' ] ? $_POST[ 'id' ] : false;
			$token = isset( $_POST[ 'token' ] ) && $_POST[ 'token' ] ? $_POST[ 'token' ] : '';

			$has_error = false;
			$message = '';
			if ( wp_verify_nonce( $token, sprintf( 'boombox-bp-%d-resend-activation', $signup_id ) ) ) {

				// Try to resend activation email
				$resend = BP_Signup::resend( array( $signup_id ) );
				if ( ! empty( $resend[ 'errors' ] ) ) {
					$has_error = true;
					$message = __( 'Your account has already been activated.', 'boombox' );
				} else {
					$message = __( 'Activation email resent! Please check your inbox or spam folder.', 'boombox' );
				}
			} else {
				$has_error = true;
				$message = esc_html__( 'Invalid request.', 'boombox' );
			}

			if ( $has_error ) {
				wp_send_json_error( array( 'message' => $message ) );
			} else {
				wp_send_json_success( array( 'message' => $message ) );
			}
		}

		/**
		 * render total views and total posts count
		 */

		public function show_user_total_posts_and_views_count() {

			$user_id = bp_displayed_user_id();

			$total_posts = count_user_posts( $user_id, 'post', true );

			$total_data = '';
			$show_user_total_data = apply_filters( 'boombox/buddypress/show_user_total_data', user_can( $user_id, 'edit_posts' ) );
			if( $show_user_total_data ) {
				$total_data = sprintf('
					<div class="row">
                        <span class="col total-label text-right">%s:</span>
                        <span class="col total-count text-left">%s</span>
                    </div>
                    <div class="row">
                        <span class="col total-label text-right">%s:</span>
                        <span class="col total-count text-left">%s</span>
                    </div>',
					/* -1- */
					__( 'Total Reads', 'boombox' ),
					/* -2- */
					number_format( floatval( get_user_meta( $user_id, 'total_posts_view_count', true ) ) ),
					/* -3- */
					__( 'Total Posts', 'boombox' ),
					/* -4- */
					number_format( floatval( $total_posts ) )
				);
			}

			$total_data .= apply_filters( 'boombox/buddypress/member_header_additional_data', '' );

			printf(
				'
                <div class="bbp-user-report">
                    <p class="user-registered text-center">%s %s</p>
                    <div class="user-totals">%s</div>
                </div>',
				/* -1- */
				__( 'Member since', 'boombox' ),
				/* -2- */
				apply_filters( 'boombox/user_registered_date', date_i18n( get_option( 'date_format' ), strtotime( bp_get_displayed_user()->userdata->user_registered ) ), bp_get_displayed_user()->userdata ),
				/* -3- */
				$total_data
			);


		}

		/**
		 * Setup sub navigation in buddypress main navigation
		 */
		public function main_nav_setup_sub_nav() {
			if( ! is_buddypress() ) {
				return;
			}

			$bp = buddypress();

			$parent_items_count = apply_filters( 'boombox/buddypress/main_nav/parent_items_count', 5 );
			$nav = $bp->members->nav->get_primary();
			if ( get_current_user_id() != bp_displayed_user_id() ) {
				$nav = wp_list_filter( $nav, array( 'show_for_displayed_user' => true ) );
			}

			if ( $nav && ( count( $nav ) > $parent_items_count ) ) {

				$nav_item_priorities        = array_keys( $nav );
				$start_nav_item_key         = $nav_item_priorities[ $parent_items_count ];
				$start_nav_item             = $nav[ $start_nav_item_key ];
				$start_nav_item_filter_name = 'bp_get_displayed_user_nav_' . $start_nav_item->css_id;

				add_filter( $start_nav_item_filter_name, array( $this, 'open_bp_main_nav_sub_menu' ), 1, 2 );

				$nav_item_priorities_reversed = array_reverse( $nav_item_priorities );
				$end_nav_item_key             = $nav_item_priorities_reversed[0];
				$end_nav_item                 = $nav[ $end_nav_item_key ];
				$end_nav_item_filter_name     = 'bp_get_displayed_user_nav_' . $end_nav_item->css_id;

				add_filter( $end_nav_item_filter_name, array( $this, 'close_bp_main_nav_sub_menu' ), 9999, 2 );
			}
		}

		/**
		 * Open sub-nav
		 *
		 * @param $html
		 * @param $item
		 *
		 * @return string
		 */
		public function open_bp_main_nav_sub_menu( $html, $item ) {
			$html = sprintf(
				'<li class="nav-has-child">
                            <span class="more-menu">%s</span>
                            <span class="menu-toggle"></span>
                            <div class="nav-sub-menu">
                                <ul class="sub-menu-inner">%s',
				apply_filters( 'boombox/buddypress/main_nav/parent_menu_item_label', __( 'More', 'boombox' ) ),
				$html
			);

			return $html;
		}

		/**
		 * Close sub-nav
		 *
		 * @param $html
		 * @param $item
		 *
		 * @return string
		 */
		public function close_bp_main_nav_sub_menu( $html, $item ) {
			$html .= '</ul></div></li>';

			return $html;
		}

		/**
		 * Add extra fields to theme customizer
		 *
		 * @param array  $fields   Current fields
		 * @param string $section  Section ID
		 * @param array  $defaults Default values
		 *
		 * @return array
		 */
		public function add_settings_to_customizer_extras_authentication( $fields, $section, $defaults ) {

			// Disable BuddyPress account activation
			$fields[] = array(
				'settings'    => 'buddypress_account_activation',
				'label'       => __( 'BuddyPress Account Activation', 'boombox' ),
				'description' => __( 'Applies only on native registration form in popup', 'boombox' ),
				'section'     => $section,
				'type'        => 'switch',
				'priority'    => 25,
				'default'     => $defaults[ 'buddypress_account_activation' ],
				'choices'     => array(
					'on'  => esc_attr__( 'On', 'boombox' ),
					'off' => esc_attr__( 'Off', 'boombox' ),
				),
			);

			return $fields;
		}

		/**
		 * Setup default values for customizer extra fields
		 *
		 * @param array $values Current values
		 *
		 * @return array
		 */
		public function customizer_default_values( $values ) {

			$values[ 'buddypress_account_activation' ] = true;

			return $values;
		}

		/**
		 * Edit "Gamify"'s featured author widget user cover image URL
		 * @param string $url Current URL
		 * @param int $user_id Widget selected user ID
		 *
		 * @return string
		 */
		public function edit_gfy_featured_author_widget_cover_image_url( $url, $user_id ) {

			$cover_url = bp_attachments_get_attachment( 'url', array( 'item_id' => $user_id ) );
			if( $cover_url ) {
				$url = $cover_url;
			}

			return $url;
		}

		/**
		 * Edit "Gamify"'s featured author widget user social links
		 * @param array $socials Current social links
		 * @param int $user_id Widget selected user ID
		 *
		 * @return array
		 */
		public function edit_gfy_featured_author_widget_social_links( $socials, $user_id ) {

			$profile_socials = $this->get_xprofile_socials( $user_id );
			if( ! empty( $profile_socials ) ){
				$socials = $profile_socials;
			}

			return $socials;
		}

	}

	Boombox_Buddypress::get_instance();

}