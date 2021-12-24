<?php
/**
 * Library post meta boxes handler
 *
 * @package "All In One Meta" library
 * @since   1.0.0
 * @version 1.0.0
 */

// Prevent direct script access.
if ( !defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

if( ! class_exists( 'AIOM_Post_Metabox' ) ) {

	if( ! class_exists( 'AIOM_Object_Metabox' ) ) {
		require_once 'class-aiom-object.php';
	}

	/**
	 * Class AIOM_Post
	 * @since 1.0.0
	 * @version 1.0.0
	 */
	final class AIOM_Post_Metabox extends AIOM_Object_Metabox {
		
		/**
		 * Hold post type: post, page or any other registered post type
		 * @var array
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		private $post_type = array();
		
		/**
		 * Holds meta box context. Post edit screen contexts include 'normal', 'side', and 'advanced'
		 * @var string
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		private $context = 'normal';
		
		/**
		 * Holds meta box priority. high, low, default
		 * @var string
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		private $priority = 'default';
		
		/**
		 * Parse and validate metabox configuration
		 *
		 * @param array $args
		 * @return bool
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		protected function validate_args( &$args ) {
			$args = wp_parse_args( $args, array(
				'id'        => '',
				'title'     => '',
				'post_type' => array(),
				'context'   => 'normal',
				'priority'  => $this->priority,
			) );
			$args[ 'post_type' ] = (array)$args[ 'post_type' ];
			if( ! in_array( $args[ 'priority' ], array( 'high', 'low', 'default' ) ) ) {
				$args[ 'priority' ] = $this->priority;
			}
			if( ! $args[ 'id' ] && $args[ 'title' ] ) {
				$args[ 'id' ] = 'aiom-post-' . sanitize_title_with_dashes( $args[ 'title' ] );
			}

			return ( $args[ 'id' ] && $args[ 'title' ] && ! empty( $args[ 'post_type' ] ) );
		}
		
		/**
		 * Setup metabox arguments
		 *
		 * @param array $args
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		protected function setup_args( $args ) {
			$this->id        = $args[ 'id' ];
			$this->title     = $args[ 'title' ];
			$this->post_type = $args[ 'post_type' ];
			$this->context   = $args[ 'context' ];
			$this->priority  = $args[ 'priority' ];
		}
		
		/**
		 * Get fields
		 * @return array
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		protected function get_fields(){
			return apply_filters( 'aiom/post/fields', parent::get_fields(), get_post() );
		}
		
		/**
		 * Setup generic actions
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		protected function hooks() {
			parent::hooks();

			add_action( 'add_meta_boxes', array( $this, 'register' ), 1 );
			add_action( 'dbx_post_sidebar', array( $this, 'maybe_render_hash_field' ), 99999999, 1 );
			add_action( 'aiom_save_post', array( $this, 'save' ), 10, 3 );
			foreach( $this->post_type as $post_type ) {
				add_filter( 'manage_' . $post_type . '_posts_columns', array( $this, 'manage_list_table_columns' ) );
				add_action( 'manage_' . $post_type . '_posts_custom_column', array( $this, 'render_list_table_column_content' ), 10, 2 );
			}
		}

		/**
		 * Check whether current screen the required one
		 * @return bool
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		protected function is_appropriate_screen() {
			return (
				( $screen = get_current_screen() )
				&& in_array( $screen->id, $this->post_type )
			);
		}
		
		/**
		 * Register meta boxes
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		public function register() {
			foreach ( $this->post_type as $post_type ) {
				add_meta_box(
					$this->id,
					$this->title,
					array( $this, 'render' ),
					$post_type,
					$this->context,
					$this->priority
				);
			}
		}
		
		/**
		 * Render meta box
		 *
		 * @param WP_Post $object Current post instance
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		public function render( $object ) {

			$meta_key = AIOM_Config::get_post_meta_key();
			$data = (array)aiom_get_post_meta( $object->ID, $meta_key );

			// in case of absentee standalone fields in single meta data - use they real meta value
			foreach( $this->get_fields() as $tab_id => $tab_data ) {
				$standalone_fields = wp_list_filter( $tab_data[ 'fields' ], array( 'standalone' => true ) );
				foreach( $standalone_fields as $name => $field_data ) {
					if( ! isset( $data[ $name ] ) ) {
						$data[ $name ] = get_post_meta( $object->ID, $name, true );
					}
				}
			}

			$this->maybe_include_renderer();
			$renderer = new AIOM_Renderer( $this->get_fields(), $data, $meta_key, $this->context, $object );
			$renderer->render();
		}

		/**
		 * Render active tab hidden hash field
		 * @param WP_Post $post Current post object
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		public function maybe_render_hash_field( $post ) {
			if( in_array( $post->post_type, $this->post_type ) ) {
				AIOM_Renderer::render_hash_field();
			}
		}

		/**
		 * Add data to data saver
		 *
		 * @param int   $post_id Post ID
		 * @param array $data    Data to save: key->value pairs
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		private function add_saver_data( $post_id, $data ) {

			$this->maybe_include_renderer();
			$post_type = get_post_type( $post_id );

			$meta_values = array();
			$standalone_values = array();
			foreach ( $this->get_fields() as $tab_id => $tab ) {
				foreach ( $tab[ 'fields' ] as $field_id => $field ) {
					if( $handler = AIOM_Renderer::get_field_handler( $field[ 'type' ] ) ) {
						$field = call_user_func( array( $handler, 'parse_field_args' ), $field );

						$key = ( isset( $field[ 'name' ] ) && $field[ 'name' ] ) ? $field[ 'name' ] : $field_id;
						$value = isset( $data[ $key ] ) ? $data[ $key ] : $field[ 'default' ];
						if( isset( $field[ 'sanitize_callback' ] ) && is_callable( $field[ 'sanitize_callback' ] ) ) {
							$value = call_user_func( $field[ 'sanitize_callback' ] , $value );
						}

						/** Provide possibility to overwrite meta value for standalone field */
						$value = apply_filters( 'aiom/' . $post_type . '/save_value?field=' . $key, $value, $post_id );

						$meta_values[ $key ] = $value;
						if( $field[ 'standalone' ] ) {
							$standalone_values[ $key ] = $value;
						}
					}
				}
			}
			
			/**
			 * Last chance to edit fields values
			 */
			$meta_values = apply_filters( 'aiom/' . $post_type . '/save_values', $meta_values, $post_id );
			
			$saver = AIOM_Data_Saver::get_instance();
			$saver->add_standalone_data( $standalone_values );
			$saver->add_data( $meta_values );
		}
		
		/**
		 * Save post meta data
		 *
		 * @param int     $post_id Current post ID
		 * @param WP_Post $post    Current Post
		 *
		 * @return bool|int
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		public function save( $post_id, $post ) {
			
			if( ! wp_doing_ajax() ) {
				
				/** Add nonce for security and authentication. */
				$nonce_name = isset( $_POST[ 'aiom_nonce' ] ) ? $_POST[ 'aiom_nonce' ] : '';
				$nonce_action = 'aiom_nonce_action';
				
				/** Check if nonce is valid. */
				if ( ! wp_verify_nonce( $nonce_name, $nonce_action ) ) {
					return false;
				}
				
			}
			
			/** Check if user has permissions to save data. */
			if ( ! current_user_can( 'edit_post', $post_id ) ) {
				return false;
			}
			
			/** Check if not an auto save. */
			if ( wp_is_post_autosave( $post_id ) ) {
				return false;
			}
			
			/** Check if not a revision. */
			if ( wp_is_post_revision( $post_id ) ) {
				return false;
			}
			
			/** make sure we're working with needed post type. */
			if ( ! in_array( $post->post_type, $this->post_type ) ) {
				return false;
			}
			
			/** it's save to process */
			$meta_key = AIOM_Config::get_post_meta_key();
			$meta_data = isset( $_POST[ $meta_key ] ) ? $_POST[ $meta_key ] : array();
			
			/** Add data to saver: it must take care */
			$this->add_saver_data( $post_id, $meta_data );

		}

		/**
		 * Render {post_type} custom column content
		 * @param string $column_name Column name
		 * @param int    $object_id   Current post ID
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		public function render_list_table_column_content( $column_name, $object_id ) {
			$fields = $this->get_fields_with_columns();
			if( ! empty( $fields ) && array_key_exists( $column_name, $fields ) ) {
				$field = $fields[ $column_name ];
				$value = aiom_get_post_meta( $object_id, $column_name );
				$callback = $field[ 'table_col' ][ 'callback' ];
				echo call_user_func( $callback, $value, $column_name, $object_id, $field );
			}
		}
		
	}
	
}