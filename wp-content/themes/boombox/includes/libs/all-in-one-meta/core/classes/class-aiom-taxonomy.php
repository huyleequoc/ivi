<?php
/**
 * Library term meta boxes handler
 *
 * @package "All In One Meta" library
 * @since   1.0.0
 * @version 1.0.0
 */

// Prevent direct script access.
if ( !defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

if( ! class_exists( 'AIOM_Taxonomy_Metabox' ) ) {

	if( ! class_exists( 'AIOM_Object_Metabox' ) ) {
		require_once 'class-aiom-object.php';
	}

	/**
	 * Class AIOM_Taxonomy_Metabox
	 * @since 1.0.0
	 * @version 1.0.0
	 */
	final class AIOM_Taxonomy_Metabox extends AIOM_Object_Metabox {

		/**
		 * Hold taxonomy: category, post_tag or any registered taxonomy
		 * @var array
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		private $taxonomy = array();

		/**
		 * Holds priority for meta boxes form rendering hooks
		 * @var int
		 */
		private $priority = 10;

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
				'taxonomy'  => array(),
				'priority'  => $this->priority
			) );
			$args[ 'taxonomy' ] = (array)$args[ 'taxonomy' ];
			$args[ 'priority' ] = absint( $args[ 'priority' ] );
			if( ! $args[ 'id' ] && $args[ 'title' ] ) {
				$args[ 'id' ] = 'aiom-term-' . sanitize_title_with_dashes( $args[ 'title' ] );
			}

			return ( $args[ 'id' ] && $args[ 'title' ] && ! empty( $args[ 'taxonomy' ] ) );
		}

		/**
		 * Setup metabox arguments
		 *
		 * @param array $args
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		protected function setup_args( $args ) {
			$this->id       = $args[ 'id' ];
			$this->title    = $args[ 'title' ];
			$this->taxonomy = $args[ 'taxonomy' ];
			$this->priority = $args[ 'priority' ];
		}
		
		/**
		 * Get fields
		 * @return array
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		protected function get_fields(){
			$taxnow = $_REQUEST['taxonomy'];
			$tag = null;
			if( isset( $_REQUEST['tag_ID'] ) ) {
				$tag = get_term( $_REQUEST[ 'tag_ID' ], $taxnow, OBJECT, 'edit' );
			}
			
			return apply_filters( 'aiom/taxonomy/fields', parent::get_fields(), $taxnow, $tag );
		}

		/**
		 * Setup generic actions
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		protected function hooks() {
			parent::hooks();

			if( is_admin() && isset( $_REQUEST['taxonomy'] ) ) {
				foreach ( $this->taxonomy as $taxonomy ) {
					add_action( $taxonomy . '_add_form_fields', array( $this, 'render_add_form' ), $this->priority, 1 );
					add_action( $taxonomy . '_edit_form', array( $this, 'render_edit_form' ), $this->priority, 1 );

					add_action( $taxonomy . '_add_form_fields', array( 'AIOM_Renderer', 'render_hash_field' ), 99999999 );
					add_action( $taxonomy . '_edit_form', array( 'AIOM_Renderer', 'render_hash_field' ), 99999999 );

					add_filter( 'manage_edit-' . $taxonomy . '_columns', array( $this, 'manage_list_table_columns' ), 10, 1 );
					add_filter( 'manage_' . $taxonomy . '_custom_column', array( $this, 'edit_list_table_column_content' ), 10, 3 );
				}
			}

			add_action( 'aiom_save_term', array( $this, 'save' ), 10, 2 );
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
				&& in_array( str_replace( 'edit-', '', $screen->id ), $this->taxonomy )
			);
		}

		/**
		 * Render meta box
		 * @param string $taxonomy Taxonomy slug
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		public function render_add_form( $taxonomy ) {

			if( $tax_object = get_taxonomy( $taxonomy ) ) {

				$this->maybe_include_renderer();
				$renderer = new AIOM_Renderer(
					$this->get_fields(),
					array(),
					AIOM_Config::get_tax_meta_key(),
					'add',
					array(
						'type'     => 'taxonomy',
						'taxonomy' => $taxonomy
					),
					array(
						'id'    => $this->id,
						'title' => $this->get_title( $tax_object ? $tax_object->labels->singular_name : '' )
					)
				);
				$renderer->render();
			}
		}

		/**
		 * Render meta box
		 * @param WP_Term $object Current Term
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		public function render_edit_form( $object ) {

			$meta_key = AIOM_Config::get_tax_meta_key();
			$data = (array)aiom_get_term_meta( $object->term_id, $meta_key );
			$tax_object = get_taxonomy( $object->taxonomy );

			// in case of absentee standalone fields in single meta data - use they real meta value
			foreach( $this->get_fields() as $tab_id => $tab_data ) {
				$standalone_fields = wp_list_filter( $tab_data[ 'fields' ], array( 'standalone' => true ) );
				foreach( $standalone_fields as $name => $field_data ) {
					if( ! isset( $data[ $name ] ) ) {
						$data[ $name ] = get_term_meta( $object->term_id, $name, true );
					}
				}
			}

			$this->maybe_include_renderer();
			$renderer = new AIOM_Renderer(
				$this->get_fields(),
				$data,
				$meta_key,
				'edit',
				$object,
				array(
					'id'    => $this->id,
					'title' => $this->get_title( $tax_object ? $tax_object->labels->singular_name : '' )
				)
			);
			$renderer->render();
		}

		/**
		 * Add data to data saver
		 *
		 * @param int    $term_id  Term ID
		 * @param string $taxonomy Taxonomy slug
		 * @param array  $data     Data to save: key->value pairs
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		private function add_saver_data( $term_id, $taxonomy, $data ) {

			$this->maybe_include_renderer();

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
						$value = apply_filters( 'aiom/' . $taxonomy . '/save_value?field=' . $key, $value, $term_id );

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
			$meta_values = apply_filters( 'aiom/' . $taxonomy . '/save_values', $meta_values, $term_id );

			$saver = AIOM_Data_Saver::get_instance();
			$saver->add_standalone_data( $standalone_values );
			$saver->add_data( $meta_values );

		}

		/**
		 * Save term meta data
		 *
		 * @param int    $term_id  Term ID.
		 * @param string $taxonomy Taxonomy slug.
		 *
		 * @return bool|int
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		public function save( $term_id, $taxonomy ) {

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
			if ( ! current_user_can( 'edit_term', $term_id ) ) {
				return false;
			}

			/** make sure we're working with needed post type. */
			if ( ! in_array( $taxonomy, $this->taxonomy ) ) {
				return false;
			}

			/** it's save to process */
			$meta_key = AIOM_Config::get_tax_meta_key();
			$meta_data = isset( $_POST[ $meta_key ] ) ? $_POST[ $meta_key ] : array();

			/** Add data to saver: it must take care */
			$this->add_saver_data( $term_id, $taxonomy, $meta_data );
		}

		/**
		 * Edit taxomony term custom column content
		 * @param mixed  $content     Current content
		 * @param string $column_name Column name
		 * @param int    $object_id   Current term ID
		 *
		 * @return mixed
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		public function edit_list_table_column_content( $content, $column_name, $object_id ) {
			$fields = $this->get_fields_with_columns();
			if( ! empty( $fields ) && array_key_exists( $column_name, $fields ) ) {
				$field = $fields[ $column_name ];
				$value = aiom_get_term_meta( $object_id, $column_name );
				$callback = $field[ 'table_col' ][ 'callback' ];
				$content = call_user_func( $callback, $value, $column_name, $object_id, $field );
			}

			return $content;
		}

	}

}