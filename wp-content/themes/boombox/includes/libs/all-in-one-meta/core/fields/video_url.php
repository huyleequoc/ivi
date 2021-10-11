<?php
/**
 * Video URL field for metaboxes
 *
 * @package "All In One Meta" library
 * @since   1.0.0
 * @version 1.0.0
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

if ( ! class_exists( 'AIOM_Base_Field' ) ) {
	require_once( 'base-field.php' );
}

if ( ! class_exists( 'AIOM_Video_URL_Field' ) && class_exists( 'AIOM_Base_Field' ) ) {

	/**
	 * Class AIOM_Video_URL_Field
	 * @since 1.0.0
	 * @version 1.0.0
	 */
	final class AIOM_Video_URL_Field extends AIOM_Base_Field {

		/**
		 * Parse field arguments
		 *
		 * @param array $args Field arguments
		 *
		 * @return array
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		public static function parse_field_args( $args ) {
			$args = wp_parse_args( $args, array(
				'id'                 => '',
				'name'               => '',
				'label'              => '',
				'description'        => '',
				'default'            => '',
				'order'              => 10,
				'sub_order'          => 10,
				'standalone'         => false,
				'class'              => '',
				'attributes'         => '',
				'wrapper_class'      => '',
				'wrapper_attributes' => '',
				'table_col'          => null,
				'sanitize_callback'  => array( __CLASS__, 'sanitize' ),
				'render_callback'    => null,
				'active_callback'    => null,
			) );

			return $args;
		}
		
		/**
		 * Get field HTML classes
		 * @return string
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		public function get_class() {
			$classes = 'regular-text';
			if( $passed_classes = parent::get_class() ) {
				$classes .= ' ' . $passed_classes;
			}
			
			return $classes;
		}
		
		/**
		 * Get field wrapper classes
		 * @return string
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		public function get_wrapper_class() {
			$classes = 'aiom-form-row aiom-form-row-text';
			if( $passed_classes = parent::get_wrapper_class() ) {
				$classes .= ' ' . $passed_classes;
			}
			
			return $classes;
		}
		
		/**
		 * Render field
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		public function render() {
			$label = $this->get_label(); ?>
			<div class="<?php echo esc_attr( $this->get_wrapper_class() ); ?>" <?php echo $this->get_wrapper_attributes(); ?>>
				<div class="label-col<?php echo $label ? '' : ' label-col-empty'; ?>">
					<label for="<?php echo esc_attr( $this->get_id() ); ?>"><?php echo esc_html( $label ); ?></label>
				</div>
				<div class="control-col">
					<input type="text"
					       id="<?php echo esc_attr( $this->get_id() ); ?>"
					       name="<?php echo esc_attr( $this->get_name() ); ?>"
					       class="<?php echo esc_attr( $this->get_class() ); ?>" <?php echo $this->get_attributes(); ?>
						   value="<?php echo esc_attr( $this->get_value() ); ?>"/>
					<?php if ( $description = $this->get_description() ) { ?>
						<p class="description"><?php echo $description; ?></p>
					<?php } ?>
				</div>
				<?php echo $this->get_active_callback(); ?>
			</div>
			<?php
		}

		/**
		 * Get regular expression
		 *
		 * @param string $type Expression type
		 *
		 * @return string
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		private static function get_regex( $type ) {

			switch ( $type ) {
				case 'youtube':
					$regex = "/^(?:http(?:s)?:\/\/)?(?:www\.)?(?:m\.)?(?:youtu\.be\/|youtube\.com\/(?:(?:watch)?\?(?:.*&)?v(?:i)?=|(?:embed|v|vi|user)\/))([^\?&\"'>]+)/";
					break;
				case 'vimeo':
					$regex = "/(https?:\/\/)?(www\.)?(player\.)?vimeo\.com\/([a-z]*\/)*([0-9]{6,11})[?]?.*/";
					break;
				case 'dailymotion':
					$regex = "/^.+dailymotion.com\/(?:video|swf\/video|embed\/video|hub|swf)\/([^&?]+)/";
					break;
				case 'vine':
					$regex = "/^http(?:s?):\/\/(?:www\.)?vine\.co\/v\/([a-zA-Z0-9]*)?/";
					break;
				case 'ok':
					$regex = "/^http(?:s?):\/\/(?:www\.)?(ok|odnoklassniki)\.ru\/video\/([a-zA-Z0-9]*)?/";
					break;
				case 'facebook':
					$regex = "/^(https?:\/\/www\.facebook\.com\/(?:video\.php\?v=\d+|.*?\/videos\/\d+)\/?)$/";
					break;
				case 'vidme':
					$regex = "/^http(?:s?):\/\/(?:www\.)?vid\.me\/([a-zA-Z0-9]*)?/";
					break;
				case 'vk':
					$regex = "/^(http(?:s?):)?\/\/(?:www\.)?vk\.com\/video_ext\.php\?(.*)/";
					break;
				case 'twitch':
					$regex = "/^http(?:s?):\/\/(?:www\.)?(?:go\.)?twitch\.tv(\/videos)?\/([a-zA-Z0-9]*)?/";
					break;
				case 'coub':
					$regex = "/^(http(?:s?):)?\/\/(?:www\.)?coub\.com\/(view|embed)\/([a-zA-Z0-9]*)?/";
					break;
				case 'twitter':
					$regex = "/http(?:s)?:\/\/(?:www\.)?twitter\.com\/([a-zA-Z0-9_]+)\/status(?:es)?\/([a-zA-Z0-9_]+)/";
					break;
				case 'instagram':
					$regex = "/(https?:\/\/)?([\w\.]*)instagram\.com\/p\/([a-zA-Z0-9_-]*)\/{0,1}/";
					break;
				default:
					$regex = '';
			}

			return $regex;
		}

		/**
		 * Sanitize value
		 * @param string $value Current value
		 *
		 * @return string
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		public static function sanitize( $value = '' ) {

			if ( $value ) {

				if ( strpos( $value, '<iframe' ) !== false ) {
					preg_match( '/src="([^"]+)"/', $value, $src_matches );
					$value = $src_matches[ 1 ];
				}
				$video_url = trim( $value );

				while ( true ) {

					/***** "Youtube" */
					preg_match( static::get_regex( 'youtube' ), $video_url, $youtube_matches );
					if ( isset( $youtube_matches[ 1 ] ) && $youtube_matches[ 1 ] ) {
						$value = $video_url;
						break;
					}

					/***** "Vimeo" */
					preg_match( static::get_regex( 'vimeo' ), $video_url, $vimeo_matches );
					if ( isset( $vimeo_matches[ 5 ] ) && $vimeo_matches[ 5 ] ) {
						$value = $video_url;
						break;
					}

					/***** "Dailymotion" */
					preg_match( static::get_regex( 'dailymotion' ), $video_url, $dailymotion_matches );
					if ( isset( $dailymotion_matches[ 1 ] ) && $dailymotion_matches[ 1 ] ) {
						$value = $video_url;
						break;
					}

					/***** "Vine" */
					preg_match( static::get_regex( 'vine' ), $video_url, $vine_matches );
					if ( isset( $vine_matches[ 1 ] ) && $vine_matches[ 1 ] ) {
						$value = $video_url;
						break;
					}

					/***** "Ok" */
					preg_match( static::get_regex( 'ok' ), $video_url, $ok_matches );
					if ( isset( $ok_matches[ 2 ] ) && $ok_matches[ 2 ] ) {
						$value = $video_url;
						break;
					}

					/***** "Facebook" */
					preg_match( static::get_regex( 'facebook' ), $video_url, $fb_matches );
					if ( isset( $fb_matches[ 1 ] ) && $fb_matches[ 1 ] ) {
						$value = $video_url;
						break;
					}

					/***** "Vidme" */
					preg_match( static::get_regex( 'vidme' ), $video_url, $vidme_matches );
					if ( isset( $vidme_matches[ 1 ] ) && $vidme_matches[ 1 ] ) {
						$value = $video_url;
						break;
					}

					/***** "VK" */
					preg_match( static::get_regex( 'vk' ), $video_url, $vk_matches );
					if ( isset( $vk_matches[ 2 ] ) && $vk_matches[ 2 ] ) {
						parse_str( $vk_matches[ 2 ], $vk_matches );
						if ( isset( $vk_matches[ 'id' ], $vk_matches[ 'oid' ], $vk_matches[ 'hash' ] ) ) {
							$value = $video_url;
						}
						break;
					}

					/***** "Twitch" */
					preg_match( static::get_regex( 'twitch' ), $video_url, $twitch_matches );
					if ( isset( $twitch_matches[ 2 ] ) && $twitch_matches[ 2 ] ) {
						$value = $video_url;
						break;
					}

					/***** "Coub" */
					preg_match( static::get_regex( 'coub' ), $video_url, $coub_matches );
					if ( isset( $coub_matches[ 3 ] ) && $coub_matches[ 3 ] ) {
						$value = $video_url;
						break;
					}

					/***** "Twitter" */
					preg_match( static::get_regex( 'twitter' ), $video_url, $twitter_matches );
					if ( isset( $twitter_matches[ 1 ] ) && isset( $twitter_matches[ 2 ] ) && $twitter_matches[ 1 ] && $twitter_matches[ 2 ] ) {
						$value = $video_url;
						break;
					}

					/***** "Instagram" */
					preg_match( static::get_regex( 'instagram' ), $video_url, $instagram_matches );
					if ( isset( $instagram_matches[ 3 ] ) && $instagram_matches[ 3 ] ) {
						$value = $video_url;
						break;
					}

					/***** "HTML video" */
					$video_type = wp_check_filetype( $video_url );
					if ( isset( $video_type[ 'type' ] ) && $video_type[ 'type' ] && preg_match( "~^(?:f|ht)tps?://~i", $video_url ) ) {
						$value = $video_url;
						break;
					}

					$value = '';
					break;
				}

			}

			return $value;

		}

	}

}