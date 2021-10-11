<?php
/**
 * Plugin for including social links on theme
 *
 * @package BoomBox_Theme_Extensions
 *
 */

// Prevent direct script access
if ( ! defined( 'ABSPATH' ) ) {
	die ( 'No direct script access allowed' );
}

if ( ! class_exists( 'Boombox_Social' ) ):

	class Boombox_Social {

		/**
		 * Init
		 */
		public static function init() {
			if ( ! defined( 'BBTE_SOCIAL_PLUGIN_DIR' ) ) {
				define( 'BBTE_SOCIAL_PLUGIN_DIR', BBTE_PLUGIN_PATH . '/boombox-social' );
			}
			if ( ! defined( 'BBTE_SOCIAL_PLUGIN_URL' ) ) {
				define( 'BBTE_SOCIAL_PLUGIN_URL', BBTE_PLUGIN_URL . '/boombox-social' );
			}

			add_action( 'admin_menu', array( __CLASS__, 'user_menu' ) );
			add_action( 'admin_init', array( __CLASS__, 'admin_init' ) );
			add_action( 'admin_notices', array( __CLASS__, 'admin_notices' ) );
			add_action( 'widgets_init', array( __CLASS__, 'register_social_widget' ) );
		}

		/**
		 * Register Social Widget
		 */
		public static function register_social_widget() {
			require_once( BBTE_SOCIAL_PLUGIN_DIR . '/widget.php' );
			register_widget( 'Boombox_Social_Widget' );
		}

		/**
		 * Register Social Settings
		 */
		public static function admin_init() {
			register_setting( 'boombox_social_items_settings', 'boombox_social_items', array(
				__CLASS__,
				'settings_validate'
			) );
		}

		/**
		 *  Show Settings Errors
		 */
		public static function admin_notices() {
			settings_errors();
		}

		/**
		 * Validate Social Settings
		 *
		 * @param $args
		 *
		 * @return mixed
		 */
		public static function settings_validate( $args ) {
			$default_items = self::social_default_items();
			foreach ( $args as $key => $arg ) {
				if ( 'email' == $key && '' != $arg['link'] && ! is_email( $arg['link'] ) ) {
					add_settings_error( 'boombox_social_items_settings', 'invalid-email', esc_attr__( 'You have entered an invalid e-mail address.', 'boombox-theme-extensions' ) );
				}
				if ( '' == $arg['title'] ) {
					$args[ $key ]['title'] = $default_items[ $key ]['title'];
				}
			}

			return $args;
		}

		/**
		 * Create Admin Menu
		 */
		public static function user_menu() {
			$menu_page = add_menu_page( 'Boombox Socials', 'Boombox Socials', 'administrator', 'boombox_socials_settings', array(
				__CLASS__,
				'social_settings'
			) );
			add_action( 'admin_print_styles-' . $menu_page, array( __CLASS__, 'social_enqueue_styles' ) );
			add_action( 'admin_print_scripts-' . $menu_page, array( __CLASS__, 'social_enqueue_scripts' ) );

		}

		/**
		 * Enqueue Styles
		 */
		public static function social_enqueue_styles() {
			if ( wp_style_is( 'boombox-icomoon-style', 'enqueued' ) || wp_style_is( 'icomoon', 'enqueued' ) ) {
				return;
			} else {
				wp_enqueue_style( 'boombox-icomoon-style', BBTE_SOCIAL_PLUGIN_URL . '/css/icomoon/style.css' );
			}

			wp_enqueue_style( 'boombox-admin-css', BBTE_SOCIAL_PLUGIN_URL . '/css/admin.css' );
		}

		/**
		 * Enqueue Scripts
		 */
		public static function social_enqueue_scripts() {
			if ( ! wp_style_is( 'jquery-ui-sortable', 'enqueued' ) ) {
				wp_enqueue_script( 'jquery-ui-sortable' );
			}
			wp_enqueue_script( 'boombox-admin-js', BBTE_SOCIAL_PLUGIN_URL . '/js/admin.js', array(), '', true );
		}

		/**
		 * Social Settings
		 */
		public static function social_settings() {
			$items_default = self::social_default_items();
			$socials       = get_option( 'boombox_social_items', $items_default );
			$socials       = array_merge( $socials, array_diff_key( $items_default, $socials ) );
			?>
			<div class="wrap">
				<div id="icon-users" class="icon32"><br /></div>
				<h2><?php esc_html_e( 'Boombox Social', 'boombox-theme-extensions' ); ?></h2>

				<p><?php esc_html_e( 'For reordering social items, please, drag and drop icons', 'boombox-theme-extensions' ); ?></p>

				<form class="boombox-social-items" method="post" action="options.php">
					<?php settings_fields( 'boombox_social_items_settings' );

					if ( $socials ) : ?>
						<ul class="boombox-social">
							<?php
							$i                = 1;
							foreach ( $socials as $social => $social_data ):
								$element_id = 'social-' . $social;
								$element_type = ( 'email' == $social ) ? 'email' : 'text';
								?>
								<li class="ui-state-default">
									<input type="hidden"
										   name="boombox_social_items[<?php esc_attr_e( $social ); ?>][icon]"
										   value="<?php esc_attr_e( $social_data['icon'] ); ?>" />
									<a class="icon icon-<?php esc_attr_e( $social_data['icon'] ); ?>" href="#"
									   title="<?php esc_attr_e( $social_data['title'] ) ?>"><span><?php echo esc_html( $social ); ?></span></a>

									<div class="boombox-social-title">
										<input type="text"
											   name="boombox_social_items[<?php esc_attr_e( $social ); ?>][title]"
											   value="<?php echo esc_html( $social_data['title'] ); ?>"
											   placeholder="<?php esc_attr_e( 'Title', 'boombox-theme-extensions' ); ?>" />
									</div>
									<div class="boombox-social-url">
										<input type="<?php echo esc_attr( $element_type ); ?>"
											   id="<?php echo esc_html( $element_id ); ?>"
											   name="boombox_social_items[<?php esc_attr_e( $social ); ?>][link]"
											   value="<?php echo esc_html( $social_data['link'] ); ?>"
											   placeholder="<?php echo $items_default[ $social ]['placeholder']; ?>"
											   tabindex="<?php echo $i; ?>" />
										<?php if ( $items_default[ $social ]['description'] ) { ?>
											<p class="description"><?php echo esc_html( $items_default[ $social ]['description'] ); ?></p>
										<?php } ?>
									</div>
								</li>
								<?php $i ++; endforeach; ?>
						</ul>
					<?php endif; ?>

					<?php submit_button(); ?>

				</form>

			</div>
			<?php
		}

		/**
		 * Default values
		 *
		 * @return array
		 */
		public static function social_default_items() {
			static $items;
			if ( ! $items ) {
				$placeholder_link  = esc_attr__( 'Link', 'boombox-theme-extensions' );
				$placeholder_email = esc_attr__( 'Email Address', 'boombox-theme-extensions' );
				$items             = array(
					'facebook'       => array(
						'icon'        => 'facebook',
						'title'       => esc_html__( 'Facebook', 'boombox-theme-extensions' ),
						'link'        => '',
						'description' => '',
						'placeholder' => $placeholder_link
					),
					'googleplus'     => array(
						'icon'        => 'google-plus',
						'title'       => esc_html__( 'Google+', 'boombox-theme-extensions' ),
						'link'        => '',
						'description' => '',
						'placeholder' => $placeholder_link
					),
					'instagram'      => array(
						'icon'        => 'instagram',
						'title'       => esc_html__( 'Instagram', 'boombox-theme-extensions' ),
						'link'        => '',
						'description' => '',
						'placeholder' => $placeholder_link
					),
					'linkedin'       => array(
						'icon'        => 'linkedin',
						'title'       => esc_html__( 'Linkedin', 'boombox-theme-extensions' ),
						'link'        => '',
						'description' => '',
						'placeholder' => $placeholder_link
					),
					'pinterest'      => array(
						'icon'        => 'pinterest',
						'title'       => esc_html__( 'Pinterest', 'boombox-theme-extensions' ),
						'link'        => '',
						'description' => '',
						'placeholder' => $placeholder_link
					),
					'tumblr'         => array(
						'icon'        => 'tumblr',
						'title'       => esc_html__( 'Tumblr', 'boombox-theme-extensions' ),
						'link'        => '',
						'description' => '',
						'placeholder' => $placeholder_link
					),
					'twitter'        => array(
						'icon'        => 'twitter',
						'title'       => esc_html__( 'Twitter', 'boombox-theme-extensions' ),
						'link'        => '',
						'description' => '',
						'placeholder' => $placeholder_link
					),
					'vimeo'          => array(
						'icon'        => 'vimeo',
						'title'       => esc_html__( 'Vimeo', 'boombox-theme-extensions' ),
						'link'        => '',
						'description' => '',
						'placeholder' => $placeholder_link
					),
					'youtube'        => array(
						'icon'        => 'youtube',
						'title'       => esc_html__( 'Youtube', 'boombox-theme-extensions' ),
						'link'        => '',
						'description' => '',
						'placeholder' => $placeholder_link
					),
					'email'          => array(
						'icon'        => 'envelope',
						'title'       => esc_html__( 'Email', 'boombox-theme-extensions' ),
						'link'        => '',
						'description' => '',
						'placeholder' => $placeholder_email
					),
					'behance'        => array(
						'icon'        => 'behance',
						'title'       => esc_html__( 'Behance', 'boombox-theme-extensions' ),
						'link'        => '',
						'description' => '',
						'placeholder' => $placeholder_link
					),
					'codepen'        => array(
						'icon'        => 'codepen',
						'title'       => esc_html__( 'CodePen', 'boombox-theme-extensions' ),
						'link'        => '',
						'description' => '',
						'placeholder' => $placeholder_link
					),
					'delicious'      => array(
						'icon'        => 'delicious',
						'title'       => esc_html__( 'Delicious', 'boombox-theme-extensions' ),
						'link'        => '',
						'description' => '',
						'placeholder' => $placeholder_link
					),
					'deviantart'     => array(
						'icon'        => 'deviantart',
						'title'       => esc_html__( 'DeviantArt', 'boombox-theme-extensions' ),
						'link'        => '',
						'description' => '',
						'placeholder' => $placeholder_link
					),
					'digg'           => array(
						'icon'        => 'digg',
						'title'       => esc_html__( 'Digg', 'boombox-theme-extensions' ),
						'link'        => '',
						'description' => '',
						'placeholder' => $placeholder_link
					),
					'dribbble'       => array(
						'icon'        => 'dribbble',
						'title'       => esc_html__( 'Dribbble', 'boombox-theme-extensions' ),
						'link'        => '',
						'description' => '',
						'placeholder' => $placeholder_link
					),
					'flickr'         => array(
						'icon'        => 'flickr',
						'title'       => esc_html__( 'Flickr', 'boombox-theme-extensions' ),
						'link'        => '',
						'description' => '',
						'placeholder' => $placeholder_link
					),
					'foursquare'     => array(
						'icon'        => 'foursquare',
						'title'       => esc_html__( 'Foursquare', 'boombox-theme-extensions' ),
						'link'        => '',
						'description' => '',
						'placeholder' => $placeholder_link
					),
					'github'         => array(
						'icon'        => 'github',
						'title'       => esc_html__( 'GitHub', 'boombox-theme-extensions' ),
						'link'        => '',
						'description' => '',
						'placeholder' => $placeholder_link
					),
					'jsfiddle'       => array(
						'icon'        => 'jsfiddle',
						'title'       => esc_html__( 'JSFiddle', 'boombox-theme-extensions' ),
						'link'        => '',
						'description' => '',
						'placeholder' => $placeholder_link
					),
					'lastfm'         => array(
						'icon'        => 'lastfm',
						'title'       => esc_html__( 'Last.fm', 'boombox-theme-extensions' ),
						'link'        => '',
						'description' => '',
						'placeholder' => $placeholder_link
					),
					'reddit'         => array(
						'icon'        => 'reddit',
						'title'       => esc_html__( 'Reddit', 'boombox-theme-extensions' ),
						'link'        => '',
						'description' => '',
						'placeholder' => $placeholder_link
					),
					'slideshare'     => array(
						'icon'        => 'slideshare',
						'title'       => esc_html__( 'SlideShare', 'boombox-theme-extensions' ),
						'link'        => '',
						'description' => '',
						'placeholder' => $placeholder_link
					),
					'stack-overflow' => array(
						'icon'        => 'stack-overflow',
						'title'       => esc_html__( 'Stack Overflow', 'boombox-theme-extensions' ),
						'link'        => '',
						'description' => '',
						'placeholder' => $placeholder_link
					),
					'stumbleupon'    => array(
						'icon'        => 'stumbleupon',
						'title'       => esc_html__( 'StumbleUpon', 'boombox-theme-extensions' ),
						'link'        => '',
						'description' => '',
						'placeholder' => $placeholder_link
					),
					'vine'           => array(
						'icon'        => 'vine',
						'title'       => esc_html__( 'Vine', 'boombox-theme-extensions' ),
						'link'        => '',
						'description' => '',
						'placeholder' => $placeholder_link
					),
					'xing'           => array(
						'icon'        => 'xing',
						'title'       => esc_html__( 'XING', 'boombox-theme-extensions' ),
						'link'        => '',
						'description' => '',
						'placeholder' => $placeholder_link
					),
					'yelp'           => array(
						'icon'        => 'yelp',
						'title'       => esc_html__( 'Yelp', 'boombox-theme-extensions' ),
						'link'        => '',
						'description' => '',
						'placeholder' => $placeholder_link
					),
					'vk'             => array(
						'icon'        => 'vk',
						'title'       => esc_html__( 'Vkontakte', 'boombox-theme-extensions' ),
						'link'        => '',
						'description' => '',
						'placeholder' => $placeholder_link
					),
					'ok'             => array(
						'icon'        => 'odnoklassniki',
						'title'       => esc_html__( 'Odnoclassniki', 'boombox-theme-extensions' ),
						'link'        => '',
						'description' => '',
						'placeholder' => $placeholder_link
					),
					'twitch'         => array(
						'icon'        => 'twitch',
						'title'       => esc_html__( 'Twitch', 'boombox-theme-extensions' ),
						'link'        => '',
						'description' => '',
						'placeholder' => $placeholder_link
					),
					'websitelink'    => array(
						'icon'        => 'globe',
						'title'       => esc_html__( 'Website Link', 'boombox-theme-extensions' ),
						'link'        => '',
						'description' => '',
						'placeholder' => $placeholder_link
					),
					'snapchat'       => array(
						'icon'        => 'snapchat-ghost',
						'title'       => esc_html__( 'Snapchat', 'boombox-theme-extensions' ),
						'link'        => '',
						'description' => '',
						'placeholder' => $placeholder_link
					),
					'telegram'       => array(
						'icon'        => 'telegram',
						'title'       => esc_html__( 'Telegram', 'boombox-theme-extensions' ),
						'link'        => '',
						'description' => __( 'http://telegram.me/TELEGRAM_USERNAME', 'boombox-theme-extensions' ),
						'placeholder' => $placeholder_link
					),
					'rss'            => array(
						'icon'        => 'rss',
						'title'       => esc_html__( 'RSS', 'boombox-theme-extensions' ),
						'link'        => '',
						'description' => sprintf( 'RSS Url: %s', get_bloginfo( 'rss2_url' ) ),
						'placeholder' => $placeholder_link
					),
					'soundcloud'     => array(
						'icon'        => 'soundcloud',
						'title'       => esc_html__( 'SoundCould', 'boombox-theme-extensions' ),
						'link'        => '',
						'description' => '',
						'placeholder' => $placeholder_link
					),
					'mixcloud'       => array(
						'icon'        => 'mixcloud',
						'title'       => esc_html__( 'Mixcloud', 'boombox-theme-extensions' ),
						'link'        => '',
						'description' => '',
						'placeholder' => $placeholder_link
					),
					'quora'          => array(
						'icon'        => 'quora',
						'title'       => esc_html__( 'Quora', 'boombox-theme-extensions' ),
						'link'        => '',
						'description' => '',
						'placeholder' => $placeholder_link
					),
					'whatsapp'       => array(
						'icon'        => 'whatsapp',
						'title'       => esc_html__( 'WhatsApp', 'boombox-theme-extensions' ),
						'link'        => '',
						'description' => 'https://api.whatsapp.com/send?phone=PHONE_NUMBER',
						'placeholder' => $placeholder_link
					),
					'discord'        => array(
						'icon'        => 'discord',
						'title'       => esc_html__( 'Discord', 'boombox-theme-extensions' ),
						'link'        => '',
						'description' => "https://discordapp.com/users/USER_ID \n https://discordapp.com/channels/SERVER_ID/[CHANEL_ID]/[MESSAGE_ID]",
						'placeholder' => $placeholder_link
					),
					'tiktok'        => array(
						'icon'        => 'tiktok',
						'title'       => esc_html__( 'Tiktok', 'boombox-theme-extensions' ),
						'link'        => '',
						'description' => "https://www.tiktok.com/@account?lang=en",
						'placeholder' => $placeholder_link
					)
				);

				$items = apply_filters( 'bbte/social-icons', $items );
			}

			return $items;
		}
	}
endif;