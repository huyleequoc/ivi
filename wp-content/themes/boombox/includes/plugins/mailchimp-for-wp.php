<?php
/**
 * "Mailchimp for WP" plugin functions
 *
 * @package BoomBox_Theme
 * @since   2.0.0
 * @version 1.0.0
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

if( ! boombox_plugin_management_service()->is_plugin_active( 'mailchimp-for-wp/mailchimp-for-wp.php' ) ) {
	return;
}

if( ! class_exists( 'Boombox_Mailchimp' ) ) {
	
	final class Boombox_Mailchimp {
		
		/**
		 * Holds class single instance
		 * @var null
		 */
		private static $_instance = null;
		
		/**
		 * Get instance
		 * @return Boombox_Mailchimp|null
		 */
		public static function get_instance() {
			
			if (null == static::$_instance) {
				static::$_instance = new self();
			}
			
			return static::$_instance;
			
		}
		
		/**
		 * Boombox_Mailchimp constructor.
		 */
		private function __construct() {
			$this->hooks();

			do_action( 'boombox/mailchimp/wakeup', $this );
		}
		
		/**
		 * A dummy magic method to prevent Boombox_Mailchimp from being cloned.
		 *
		 */
		public function __clone() {
			throw new Exception('Cloning ' . __CLASS__ . ' is forbidden');
		}
		
		/**
		 * Setup Hooks
		 */
		public function hooks() {
			add_filter( 'boombox/admin/post/meta-boxes/structure', array( $this, 'edit_page_metaboxes_structure' ), 10, 4 );
			add_filter( 'boombox/customizer/fields/archive_main_posts', array( $this, 'add_inject_settings_to_custimizer' ), 10, 3 );
			add_filter( 'boombox/customizer/fields/home_main_posts', array( $this, 'add_inject_settings_to_custimizer' ), 10, 3 );
			add_filter( 'boombox/customizer_default_values', array( $this, 'edit_customizer_default_values' ), 10, 1 );
			add_filter( 'boombox/single_post/sortable_section_choices', array( $this, 'edit_single_post_sortable_section_choices'), 10, 1 );
		}
		
		/**
		 * Edit page metaboxes structure and add additional fields
		 *
		 * @param array  $structure $structure  Current structure
		 * @param string $id Current instance
		 * @param string $post_type Current post type
		 * @param string $context Meta box context
		 * @return array
		 * @since 2.5.0
		 * @version 2.5.0
		 */
		public function edit_page_metaboxes_structure( $structure, $id, $post_type, $context ) {
			if( 'page' == $post_type ) {
				
				$choices_helper = Boombox_Choices_Helper::get_instance();
				
				$structure[ 'tab_listing' ][ 'fields' ] = array_merge( $structure[ 'tab_listing' ][ 'fields' ], array(
					// "Injects" heading
					'boombox_listing_injects_heading' => array(
						'type'            => 'custom',
						'html'            => sprintf( '<h2>%s</h2><hr/>', esc_html__( 'Injects', 'boombox' ) ),
						'order'           => 90,
						'active_callback' => array(
							array(
								'field_id' => 'boombox_listing_type',
								'value'    => 'none',
								'compare'  => '!=',
							),
						),
					),
					// Newsletter
					'boombox_page_newsletter'  => array(
						'type'            => 'select',
						'label'           => esc_html__( 'Newsletter', 'boombox' ),
						'order'           => 90,
						'sub_order'       => 40,
						'choices'         => $choices_helper->get_injects(),
						'default'         => 'none',
						'active_callback' => array(
							array(
								'field_id' => 'boombox_listing_type',
								'value'    => 'none',
								'compare'  => '!=',
							),
						),
					),
					// Inject Newsletter As Post
					'boombox_inject_newsletter_instead_post'  => array(
						'type'            => 'number',
						'label'           => esc_html__( 'Inject Newsletter As Post', 'boombox' ),
						'order'           => 90,
						'sub_order'       => 50,
						'default'         => 1,
						'attributes'      => array(
							'min' => 1,
						),
						'callback'        => array( $this, 'sanitize_page_metaboxes_newsletter_instead_post_value' ),
						'render_callback' => array( $this, 'render_page_metaboxes_newsletter_instead_post_value' ),
						'active_callback' => array(
							'relation' => 'AND',
							array(
								'field_id' => 'boombox_listing_type',
								'value'    => 'none',
								'compare'  => '!=',
							),
							array(
								'field_id' => 'boombox_page_newsletter',
								'value'    => 'none',
								'compare'  => '!=',
							),
						),
					),
				) );
				
			}
			
			return $structure;
		}
		
		/**
		 * Sanitize page metaboxes instead post value on rendering
		 *
		 * @param int $value Current value
		 * @return int
		 * @since 2.5.0
		 * @version 2.5.0
		 */
		public function render_page_metaboxes_newsletter_instead_post_value( $value ) {
			$post = get_post();
			return max( 1, min( $value, boombox_get_post_meta( $post->ID, 'boombox_posts_per_page' ) ) );
			
		}
		
		/**
		 * Sanitize page metaboxes instead post value on saving
		 *
		 * @param mixed $value Current value
		 * @return int
		 * @since 2.5.0
		 * @version 2.5.0
		 */
		public function sanitize_page_metaboxes_newsletter_instead_post_value( $value ) {
			return min( max( 1, absint( $value ) ), absint( $_POST[ AIOM_Config::get_post_meta_key() ]['boombox_posts_per_page'] ) );
		}
		
		/**
		 * Add extra fields to theme customizer
		 *
		 * @param array  $fields   Current fields
		 * @param string $section  Section ID
		 * @param array  $defaults Default values
		 *
		 * @return mixed
		 */
		public function add_inject_settings_to_custimizer ( $fields, $section, $defaults ) {
			
			$priority = false;
			$section_prefix = '';
			if ( $section == boombox_customizer_get_archive_main_posts_section_id() ) {
				$priority = 111;
				$section_prefix = 'archive_main_posts_';
			} else if ( $section == boombox_customizer_get_home_main_posts_section_id() ) {
				$priority = 131;
				$section_prefix = 'home_main_posts_';
			}
			
			if ( $priority ) {
				$fields = array_merge( $fields, array(
					array(
						'settings' => $section_prefix . 'inject_newsletter',
						'label'    => esc_html__( 'Newsletter', 'boombox' ),
						'section'  => $section,
						'type'     => 'select',
						'priority' => $priority,
						'default'  => $defaults[ $section_prefix . 'inject_newsletter' ],
						'multiple' => 1,
						'choices'  => Boombox_Choices_Helper::get_instance()->get_injects(),
					),
					array(
						'settings' => $section_prefix . 'injected_newsletter_position',
						'label'    => esc_html__( 'Inject Newsletter As Post', 'boombox' ),
						'section'  => $section,
						'type'     => 'number',
						'priority' => $priority,
						'default'  => $defaults[ $section_prefix . 'injected_newsletter_position' ],
						'choices'  => array(
							'min'  => 1,
							'step' => 1,
						),
						'active_callback'    => array(
							array(
								'setting'  => $section_prefix . 'inject_newsletter',
								'value'    => 'none',
								'operator' => '!=',
							),
						),
					),
				) );
			}
			
			return $fields;
		}
		
		/**
		 * Setup default values for customizer extra fields
		 *
		 * @param $values
		 *
		 * @return mixed
		 */
		public function edit_customizer_default_values ( $values ) {
			$section_prefixes = array( 'archive_main_posts_', 'home_main_posts_' );
			foreach ( $section_prefixes as $prefix ) {
				$values[ $prefix . 'inject_newsletter' ] = 'none';
				$values[ $prefix . 'injected_newsletter_position' ] = 1;
			}
			
			array_splice( $values['single_post_general_sections'], 4, 0, 'subscribe_form' );
			
			return $values;
		}
		
		/**
		 * Add newsletter form choice to sortable sections choices for single post
		 * @param array $choices Current choices
		 *
		 * @return array
		 */
		public function edit_single_post_sortable_section_choices( $choices ) {
			$choices['subscribe_form'] = esc_html__( 'Subscribe Form', 'boombox' );
			
			return $choices;
		}
		
		/**
		 * Get newsletter Form HTML
		 *
		 * @see     Mashshare Plugin
		 *
		 * @param array $args Parsing arguments
		 *
		 * @return string
		 *
		 * @since   1.0.0
		 * @version 2.0.0
		 */
		public function get_form_html( $args = array() ) {
			
			$args = wp_parse_args( $args, array(
				'tag'   => 'div',
				'class' => '',
			) );
			
			$cache_key = 'mailchimp_form_id';
			$form_id = boombox_cache_get( $cache_key );
			if( $form_id === false ) {
				$form_id = 0;
				$forms = mc4wp_get_forms( array( 'numberposts' => 1 ) );
				if( (bool) $forms ) {
					$form = array_pop( $forms );
					$form_id = $form->ID;
				}
				boombox_cache_set( $cache_key, $form_id );
			}
			
			if( ! $form_id ) {
				return;
			}
			
			$class = 'newsletter-box ';
			if( $args[ 'class' ] ) {
				$class.= $args[ 'class' ];
			}
			$before = '<' . $args[ 'tag' ] . ' class="' . esc_attr( trim( $class ) ) . '">';
			$title = apply_filters( 'boombox_mailchimp_form_title', esc_html__( 'Get The Newsletter', 'boombox' ) );
			$content = do_shortcode( '[mc4wp_form id="' . $form_id . '"]' );
			$after = '</' . $args[ 'tag' ] . '>';
			
			$html = sprintf( '%s<div class="widget widget_mc4wp_form_widget horizontal">
					<h2 class="widget-title">%s</h2>%s
				</div>%s', $before, $title, $content, $after );
			
			return $html;
		}
		
	}
	
	Boombox_Mailchimp::get_instance();
	
}

/**
 * These functions can be rewritten with a child theme
 */
if ( ! function_exists('boombox_mc4wp_form_before_form' ) ) {
	
	/**
	 * Add text before mailchimp form
	 *
	 * @param $html
	 *
	 * @return string
	 */
	function boombox_mc4wp_form_before_form( $html ) {
		$html .= sprintf(
			'<p><b>%1$s</b><br/>%2$s</p>',
			esc_html__("LIKE WHAT YOU'RE READING?", 'boombox'),
			esc_html__('subscribe to our top stories', 'boombox')
		);
		
		return $html;
	}
	
}
add_filter( 'mc4wp_form_before_fields', 'boombox_mc4wp_form_before_form', 10, 2 );

if ( !function_exists('boombox_mc4wp_form_after_form' ) ) {
	
	/**
	 * Add text after mailchimp form
	 *
	 * @param $html
	 *
	 * @return string
	 */
	function boombox_mc4wp_form_after_form($html) {
		$html .= '<small>' . esc_html__('Don\'t worry, we don\'t spam', 'boombox') . '</small>';
		
		return $html;
	}
}
add_filter( 'mc4wp_form_after_fields', 'boombox_mc4wp_form_after_form', 10, 2 );