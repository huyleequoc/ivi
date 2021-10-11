<?php
/**
 * Shortcode for Download Button
 *
 * @package BoomBox_Theme_Extensions
 *
 */

// Prevent direct script access
if ( ! defined( 'ABSPATH' ) ) {
	die ( 'No direct script access allowed' );
}

class Boombox_Download_Button_Shortcode {

	private $_query = false;
	private $_url = false;
	private $_status = false;

	/**
	 * Holds unique instance
	 *
	 * @var Boombox_Download_Button_Shortcode
	 * @since   1.5.6
	 * @version 1.5.6
	 */
	private static $_instance;

	/**
	 * Get unique instance
	 *
	 * @return Boombox_Download_Button_Shortcode
	 * @since   1.5.6
	 * @version 1.5.6
	 */
	public static function get_instance() {
		if ( null === self::$_instance ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Boombox_Download_Button_Shortcode constructor.
	 *
	 * @since   1.5.6
	 * @version 1.5.6
	 */
	private function __construct() {
		$this->setup_hooks();
	}

	/**
	 * Prevent clone
	 *
	 * @since   1.5.6
	 * @version 1.5.6
	 */
	public function __clone() {
	}

	/**
	 * Setup hooks
	 *
	 * @since   1.5.6
	 * @version 1.5.6
	 */
	private function setup_hooks() {
		add_shortcode( 'boombox_download_button', array( $this, 'run' ) );
		add_filter( 'boombox/customizer/register/sections', array( $this, 'register_customizer_section' ), 10, 1 );
		add_filter( 'boombox/customizer/register/fields', array( $this, 'register_customizer_fields' ), 10, 2 );
		add_filter( 'boombox/customizer_default_values', array( $this, 'edit_customizer_default_values' ), 10, 1 );
		if ( boombox_get_theme_option( 'extras_download_page_id' ) ) {
			add_action( 'wp', array( $this, 'may_be_download_page' ) );
		}
	}

	/**
	 * Get "Extras->Download" section id
	 *
	 * @return string
	 *
	 * @since   1.5.6
	 * @version 1.5.6
	 */
	private function get_customizer_section_id() {
		return 'boombox_extras_download';
	}

	/**
	 * Register "Extras->Download" section
	 *
	 * @param array $sections Current sections
	 *
	 * @return array
	 *
	 * @since   1.5.6
	 * @version 1.5.6
	 */
	public function register_customizer_section( $sections ) {
		$sections[] = array(
			'id'   => $this->get_customizer_section_id(),
			'args' => array(
				'title'      => __( 'Download', 'boombox-theme-extensions' ),
				'panel'      => 'boombox_extras',
				'priority'   => 120,
				'capability' => 'edit_theme_options',
			),
		);

		return $sections;
	}

	/**
	 * Register fields for "Extras->Download" section
	 *
	 * @param array $fields   Current fields configuration
	 * @param array $defaults Array containing default values
	 *
	 * @return array
	 *
	 * @since   1.5.6
	 * @version 1.5.6
	 */
	public function register_customizer_fields( $fields, $defaults ) {

		$section         = $this->get_customizer_section_id();
		$choices_helper  = Boombox_Choices_Helper::get_instance();
		$published_pages = $choices_helper->get_published_pages();

		$custom_fields = array(
			/***** Download Page ID */
			array(
				'settings' => 'extras_download_page_id',
				'label'    => __( 'Download Page', 'boombox-theme-extensions' ),
				'section'  => $section,
				'type'     => 'select',
				'priority' => 20,
				'default'  => $defaults['extras_download_page_id'],
				'multiple' => 1,
				'choices'  => $published_pages,
			),
			/***** Delay before download */
			array(
				'settings' => 'extras_download_delay',
				'label'    => __( 'Delay before download (in seconds)', 'boombox-theme-extensions' ),
				'section'  => $section,
				'type'     => 'number',
				'priority' => 30,
				'default'  => $defaults['extras_download_delay'],
				'choices'  => array(
					'min'  => 0,
					'step' => 1,
				),
			),
			/***** Download Content Position */
			array(
				'settings' => 'extras_download_content_position',
				'label'    => __( 'Download Content Position', 'boombox-theme-extensions' ),
				'section'  => $section,
				'type'     => 'radio',
				'priority' => 40,
				'default'  => $defaults['extras_download_content_position'],
				'multiple' => 1,
				'choices'  => array(
					'before' => __( 'Before Page Content', 'boombox-theme-extensions' ),
					'after'  => __( 'After Page Content', 'boombox-theme-extensions' ),
				),
			),
			/***** Counter */
			array(
				'settings' => 'extras_download_render_counter',
				'label'    => __( 'Counter', 'boombox-theme-extensions' ),
				'section'  => $section,
				'type'     => 'switch',
				'priority' => 50,
				'default'  => $defaults['extras_download_render_counter'],
				'choices'  => array(
					'on'  => esc_attr__( 'On', 'boombox-theme-extensions' ),
					'off' => esc_attr__( 'Off', 'boombox-theme-extensions' ),
				),
			),
			/***** Download Count Style */
			array(
				'settings'        => 'extras_download_download_count_style',
				'label'           => __( 'Download Count Style', 'boombox' ),
				'section'         => $section,
				'type'            => 'select',
				'priority'        => 50,
				'default'         => $defaults['extras_download_download_count_style'],
				'multiple'        => 1,
				'choices'         => array(
					'rounded' => __( 'Rounded', 'boombox' ),
					'full'    => __( 'Full', 'boombox' ),
				),
				'active_callback' => array(
					array(
						'setting'  => 'extras_download_render_counter',
						'value'    => 1,
						'operator' => '==',
					),
				),
			),
			/***** Fake Download Count */
			array(
				'settings'        => 'extras_download_fake_count',
				'label'           => __( 'Fake Download Count', 'boombox-theme-extensions' ),
				'description'     => __( 'Leave 0 to show real count', 'boombox-theme-extensions' ),
				'section'         => $section,
				'type'            => 'number',
				'priority'        => 60,
				'default'         => $defaults['extras_download_fake_count'],
				'choices'         => array(
					'min'  => 0,
					'step' => 1,
				),
				'active_callback' => array(
					array(
						'setting'  => 'extras_download_render_counter',
						'value'    => 1,
						'operator' => '==',
					),
				),
			),
			/***** Other fields need to go here */
		);

		/***** Let others to add fields to this section */
		$custom_fields = apply_filters( 'boombox/customizer/fields/boombox_extras', $custom_fields, $section, $defaults );

		return array_merge( $fields, $custom_fields );
	}

	/**
	 * Setup default values for customizer extra fields
	 *
	 * @param array $values Current values
	 *
	 * @return array
	 */
	public function edit_customizer_default_values( $values ) {

		$values['extras_download_page_id']              = 0;
		$values['extras_download_delay']                = 10;
		$values['extras_download_content_position']     = 'after';
		$values['extras_download_render_counter']       = 1;
		$values['extras_download_download_count_style'] = 'rounded';
		$values['extras_download_fake_count']           = 0;

		return $values;
	}

	/**
	 * Hash URL
	 *
	 * @param string $url  The URL to hash
	 * @param array  $data Additional data
	 *
	 * @return string
	 * @since   1.5.6
	 * @version 1.5.6
	 */
	private function hash_url( $url, $data = array() ) {
		$data = array_merge( array( 'url' => $url ), $data );

		return base64_encode( json_encode( $data ) );
	}

	/**
	 * Unhash URL
	 *
	 * @param string $url The URL to unhash
	 *
	 * @return array|mixed|null|object
	 * @since   1.5.6
	 * @version 1.5.6
	 */
	private function unhash_url( $url ) {
		return json_decode( base64_decode( $url ), true );
	}

	/**
	 * Callback to handle "boombox_download_button" shortcode
	 *
	 * @param string|array $atts    The shortcode attributes
	 * @param string       $content The shortcode content
	 *
	 * @return string
	 */
	public function run( $atts, $content = '' ) {
		$html = '';

		$a = shortcode_atts( array(
			'file_url'         => '',
			'external_url'     => '',
			'fake'             => 1,
			'counter'          => 'inherit',
			'type'             => '',        // primary|secondary|success|info|warning|danger
			'size'             => '',        // large|small
			'target'           => 'self',    // blank|self
			'background_color' => '',
			'text_color'       => '',
			'class'            => ''
		), $atts, 'boombox_download_button' );

		$url = get_permalink( absint( boombox_get_theme_option( 'extras_download_page_id' ) ) );
		if ( ! $url ) {
			return $html;
		}

		$type             = esc_html( $a['type'] );
		$size             = esc_html( $a['size'] );
		$target           = esc_attr( $a['target'] );
		$background_color = esc_html( $a['background_color'] );
		$text_color       = esc_html( $a['text_color'] );
		$class            = esc_attr( $a['class'] );
		$file_url         = esc_attr( $a['file_url'] );
		$external_url     = esc_attr( $a['external_url'] );
		$counter          = (bool) ( ( 'inherit' === $a['counter'] ) ? boombox_get_theme_option( 'extras_download_render_counter' ) : filter_var( $a['counter'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE ) );
		$use_fake         = (bool) absint( $a['fake'] );

		$working_url = false;
		if ( $file_url ) {
			$working_url = $file_url;
			$hash        = $this->hash_url( $file_url, array(
				'action' => 'prepare',
				'type'   => 'download'
			) );
			$url         = add_query_arg( array( 'q' => $hash ), $url );
		} elseif ( $external_url ) {
			$working_url = $external_url;
			$hash        = $this->hash_url( $external_url, array(
				'action' => 'prepare',
				'type'   => 'redirect'
			) );
			$url         = add_query_arg( array( 'q' => $hash ), $url );
		} else {
			return $html;
		}

		// region CSS classes
		$classes = array( 'bb-btn-download', 'bb-btn', 'bb-btn-icon', 'btn-sm', 'icon-left', 'bb-bg-primary' );

		if ( ! empty( $type ) && in_array( $type, array(
				'primary',
				'secondary',
				'success',
				'info',
				'warning',
				'danger'
			) ) ) {
			$classes[] = 'bb-btn-' . $type;
		}

		if ( ! empty( $size ) && in_array( $size, array( 'large', 'small' ) ) ) {
			$classes[] = 'large' == $size ? 'bb-btn-lg' : 'bb-btn-sm';
		}

		if ( ! empty( $class ) ) {
			if ( is_string( $class ) ) {
				$class = explode( ' ', $class );
			}
			if ( is_array( $class ) ) {
				$classes = array_merge( $classes, $class );
			}
		}
		$classes = implode( ' ', $classes );
		// endregion

		// region Styles
		$styles = array();
		if ( ! empty( $background_color ) ) {
			$styles[] = 'background-color: ' . $background_color;
			$styles[] = 'border-color: ' . $background_color;
		}

		if ( ! empty( $text_color ) ) {
			$styles[] = 'color: ' . $text_color;
		}
		// endregion

		// region Attributes
		$attributes = array(
			'href="' . $url . '"'
		);
		if ( ! empty( $target ) && 'blank' == $target ) {
			$attributes[] = 'target="_' . $target . '" rel="noopener"';
		}

		if ( ! empty( $styles ) ) {
			$attributes[] = 'style="' . implode( '; ', $styles ) . '"';
		}
		$attributes = implode( '', $attributes );
		// endregion

		if ( $counter ) {
			$download_count    = $this->get_url_download_count( $working_url, false );
			$download_treshold = absint( apply_filters( 'bbte/download_treshold', 0 ) );
			if ( $download_count >= $download_treshold ) {
				if ( $use_fake ) {
					$download_count = $this->get_url_download_count( $working_url, true );
				}
				if ( 'rounded' == boombox_get_theme_option( 'extras_download_download_count_style' ) ) {
					$download_count = boombox_numerical_word( $download_count );
				}
				$content .= sprintf( '<span class="counter">(%s)</span>', $download_count );
			}
		}

		$html = sprintf( '<a class="%s" %s><span class="bb-icon bb-icon-download"></span><span class="text">%s</span></a>',
			esc_attr( $classes ),
			$attributes,
			do_shortcode( stripcslashes( $content ) )
		);

		return $html;

	}

	/**
	 * May be set up download page stuff
	 *
	 * @since   1.5.6
	 * @version 1.5.6
	 */
	public function may_be_download_page() {
		if ( boombox_get_theme_option( 'extras_download_page_id' ) != get_the_ID() ) {
			return;
		}

		if ( isset( $_GET['q'] ) && $_GET['q'] ) {
			$this->_query = wp_parse_args( $this->unhash_url( $_GET['q'] ), array(
				'url'    => '',
				'type'   => '',
				'action' => ''
			) );

			if ( ! $this->_query['url'] || ! in_array( $this->_query['action'], array(
					'prepare',
					'process'
				) ) || ! in_array( $this->_query['type'], array( 'download', 'redirect' ) ) ) {
				return;
			}

			$current_url = get_permalink( boombox_get_theme_option( 'extras_download_page_id' ) );
			if ( 'prepare' == $this->_query['action'] ) {
				if ( 'download' == $this->_query['type'] ) {
					$attachment_id  = attachment_url_to_postid( $this->_query['url'] );
					$attachment_dir = get_attached_file( $attachment_id );

					$this->_url = add_query_arg( array(
						'q' => $this->hash_url( $this->_query['url'], array(
							'action' => 'process',
							'type'   => $this->_query['type'],
							'dir'    => $attachment_dir,
							'nonce'  => wp_create_nonce( 'process_download_file' )
						) )
					), $current_url );
				}
				if ( 'redirect' == $this->_query['type'] ) {
					$this->_url = add_query_arg( array(
						'q' => $this->hash_url( $this->_query['url'], array(
							'action' => 'process',
							'type'   => $this->_query['type'],
							'nonce'  => wp_create_nonce( 'process_redirect' )
						) )
					), $current_url );
				}

				$content_location_action_priority = ( boombox_get_theme_option( 'extras_download_content_position' ) == 'before' ) ? 0 : 999;
				add_filter( 'the_content', array(
					$this,
					'append_page_download_stuff'
				), $content_location_action_priority, 1 );
				add_filter( 'boombox/render_page_content', '__return_true' );
				add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_countdown' ) );
			}

			if ( 'process' == $this->_query['action'] ) {

				$this->update_download_count( $this->_query['url'] );

				if ( 'download' == $this->_query['type'] ) {
					if ( wp_verify_nonce( $this->_query['nonce'], 'process_download_file' ) && file_exists( $this->_query['dir'] ) ) {
						$this->_status = true;

						header( 'Content-Description: File Transfer' );
						header( 'Content-Type: application/octet-stream' );
						header( 'Content-Disposition: attachment; filename="' . basename( $this->_query['dir'] ) . '"' );
						header( 'Expires: 0' );
						header( 'Cache-Control: must-revalidate' );
						header( 'Pragma: public' );
						header( 'Content-Length: ' . filesize( $this->_query['dir'] ) );
						flush(); // Flush system output buffer
						readfile( $this->_query['dir'] );
						die;
					}
				} elseif ( 'redirect' == $this->_query['type'] ) {
					$this->_status = true;

					if ( wp_verify_nonce( $this->_query['nonce'], 'process_redirect' ) ) {
						wp_redirect( $this->_query['url'] );
						die;
					}
				}

			}
		}
	}

	/**
	 * Enqueue countdown scripts
	 *
	 * @since   1.5.6
	 * @version 1.5.6
	 */
	public function enqueue_countdown() {
		$delay = absint( boombox_get_theme_option( 'extras_download_delay' ) );
		wp_add_inline_script( 'boombox-shortcodes', "
				var bb_download_delay = " . ( $delay ) . ";
				var bb_download_interval = setInterval( function(){ 
					if( bb_download_delay > 0 ) { 
						jQuery( '#bb-timing-seconds' ).html( --bb_download_delay )
					} else {
						clearInterval( bb_download_interval );
						jQuery( '#bb-manual-download' ).removeClass( 'hidden' );
						window.location = '" . $this->_url . "';
					}
				}, 1000 );
			"
		);
	}

	/**
	 * Edit download page content and append download stuff
	 *
	 * @param string $content Current content
	 *
	 * @return string
	 * @since    1.5.6
	 * @vversion 1.5.6
	 */
	public function append_page_download_stuff( $content ) {

		if ( ( current_filter() == 'the_content' ) && ( get_the_ID() == boombox_get_theme_option( 'extras_download_page_id' ) ) ) {
			$delay = absint( boombox_get_theme_option( 'extras_download_delay' ) );

			if ( 'prepare' == $this->_query['action'] ) {
				$text_template = __( 'Thanks! Your download is starting', 'boombox-theme-extensions' );
				if ( $delay > 0 ) {
					$text_template .= sprintf(
						'<span class="timing-seconds-row">%s <span class="timing-seconds" id="bb-timing-seconds">%d</span> %s</span>',
						__( 'in', 'boombox-theme-extensions' ),
						$delay,
						_n( 'second', 'seconds', $delay, 'boombox-theme-extensions' )
					);
				}
				$content .= sprintf( '<p class="bb-timing-block timing-info">%s</p>', $text_template );

				$content .= sprintf( '<p class="bb-timing-block download-info%s" id="bb-manual-download">%s %s</p>',
					( $delay > 0 ) ? ' hidden' : '',
					__( 'If your download doesn\'t start automatically please', 'boombox-theme-extensions' ),
					sprintf( '<a href="%s" class="file-link" id="bb-download-file">%s</a>', $this->_url, __( 'click here', 'boombox-theme-extensions' ) )
				);

			} elseif ( 'prepare' == $this->_query['process'] ) {
				if ( ! $this->_status ) {
					$content .= __( 'Invalid request', 'boombox-theme-extensions' );
				}
			}
		}

		return $content;
	}

	/**
	 * Get URL hash for counter
	 *
	 * @param string $url The URL
	 *
	 * @return string
	 * @since   1.5.6
	 * @version 1.5.6
	 */
	private function get_counter_url_hash( $url ) {
		return md5( wp_unslash( $url ) );
	}

	/**
	 * Get URL download count
	 *
	 * @param string $url      The URL
	 * @param bool   $use_fake Whether to apply fake count
	 *
	 * @return int
	 * @since   1.5.6
	 * @version 1.5.6
	 */
	private function get_url_download_count( $url, $use_fake = true ) {
		$counter  = get_option( 'boombox_download_counter', array() );
		$url_hash = $this->get_counter_url_hash( $url );
		$count    = isset( $counter[ $url_hash ] ) ? $counter[ $url_hash ] : 0;

		$fake_count = 0;
		if ( $use_fake ) {
			$fake_count = absint( boombox_get_theme_option( 'extras_download_fake_count' ) );
			if ( $fake_count > 0 ) {
				$fake_count += strlen( wp_unslash( $url ) );
			}
		}

		return $count + $fake_count;
	}

	/**
	 * Update URL download count
	 *
	 * @param string $url The URL
	 *
	 * @since   1.5.6
	 * @version 1.5.6
	 */
	private function update_download_count( $url ) {
		$hash             = $this->get_counter_url_hash( $url );
		$score            = max( absint( apply_filters( 'bbte/single_download_score', 1 ) ), 1 );
		$counter          = get_option( 'boombox_download_counter', array() );
		$count            = $this->get_url_download_count( $url, false );
		$count            += $score;
		$counter[ $hash ] = $count;
		update_option( 'boombox_download_counter', $counter );
	}
}

Boombox_Download_Button_Shortcode::get_instance();