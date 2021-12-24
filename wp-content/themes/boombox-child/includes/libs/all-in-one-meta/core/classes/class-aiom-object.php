<?php
/**
 * Library object meta boxes handler
 *
 * @package "All In One Meta" library
 * @since   1.0.0
 * @version 1.0.0
 */

// Prevent direct script access.
if ( !defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

if( ! class_exists( 'AIOM_Object_Metabox' ) ) {

	/**
	 * Class AIOM_Object_Metabox
	 * @since 1.0.0
	 * @version 1.0.0
	 */
	abstract class AIOM_Object_Metabox {

		/**
		 * Holds meta box ID
		 * @var string|null
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		protected $id = NULL;

		/**
		 * Holds meta box title
		 * @var string
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		protected $title = '';

		/**
		 * Get meta box title
		 * @param string $default Default title
		 *
		 * @return string
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		protected function get_title( $default = '' ) {
			if( ! $this->title && $default ) {
				$this->title = sprintf( __( '%s Advanced Fields', 'aiom' ), $default );
			}

			return $this->title;
		}

		/**
		 * Holds fields configuration
		 * @var array
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		protected $fields = null;

		/**
		 * Holds fields structure
		 * @var array|callable
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		protected $fields_structure;

		/**
		 * Holds fields that have columns in taxonomy terms table
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		protected $fields_with_columns;

		/**
		 * AIOM_Taxonomy_Metabox constructor.
		 *
		 * @param array $args
		 * @param array|callable $fields_structure Fields structure
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		public function __construct( $args, $fields_structure ) {
			if( $this->validate_args( $args ) ) {
				$this->setup_args( $args );
				$this->setup_fields_structure( $fields_structure );
				$this->hooks();
			}
		}

		/**
		 * Parse and validate metabox configuration
		 *
		 * @param array $args
		 * @return bool
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		protected function validate_args( &$args ) {
			return true;
		}

		/**
		 * Setup metabox arguments
		 *
		 * @param array $args
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		abstract protected function setup_args( $args );

		/**
		 * Setup fields callback
		 *
		 * @param array|callable $fields_structure Fields structure
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		protected function setup_fields_structure( $fields_structure ) {
			$this->fields_structure = $fields_structure;
		}

		/**
		 * Get fields
		 * @return array
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		protected function get_fields() {
			if( null === $this->fields ) {
				if( is_callable( $this->fields_structure ) ) {
					$this->fields = call_user_func( $this->fields_structure );
				} elseif( is_array( $this->fields_structure ) ) {
					$this->fields = $this->fields_structure;
				} else {
					$this->fields = array();
				}
			}

			return $this->fields;
		}

		/**
		 * Get fields that have columns in taxonomy terms table
		 * @return array
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		protected function get_fields_with_columns() {
			if( null === $this->fields_with_columns ) {
				$fields = array();
				foreach( $this->get_fields() as $tab => $config ) {
					$fields = array_merge( $fields, array_filter( $config[ 'fields' ], function( $field ){
						return (
							isset( $field[ 'table_col' ] )
							&& isset( $field[ 'table_col' ][ 'heading' ] )
							&& isset( $field[ 'table_col' ][ 'callback' ] )
							&& is_callable( $field[ 'table_col' ][ 'callback' ] )
						);
					} ) );
				}

				if( ! empty( $fields ) ) {
					uasort( $fields, function( $a, $b ) {
						$a_order = isset( $a[ 'table_col' ][ 'order' ] ) ? absint( $a[ 'table_col' ][ 'order' ] ) : 10;
						$b_order = isset( $b[ 'table_col' ][ 'order' ] ) ? absint( $b[ 'table_col' ][ 'order' ] ) : 10;

						return $a_order - $b_order;
					} );
				}
				$this->fields_with_columns = $fields;
			}

			return $this->fields_with_columns;
		}

		/**
		 * Setup generic actions
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		protected function hooks() {
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ), 11 );
		}

		/**
		 * Include renderer class if it does not exists
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		protected function maybe_include_renderer() {
			if ( ! class_exists( 'AIOM_Renderer' ) ) {
				require_once( 'class-aiom-renderer.php' );
			}
		}

		/**
		 * Check whether current screen the required one
		 * @return bool
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		abstract protected function is_appropriate_screen();

		/**
		 * Enqueue Scripts and Styles
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		public function admin_enqueue_scripts() {

			if ( $this->is_appropriate_screen() ) {

				wp_enqueue_style( 'aiom-style' );
				wp_enqueue_script( 'aiom-script' );

				$this->maybe_include_renderer();

				$types = array();
				foreach( $this->get_fields() as $tab_id => $tab ) {
					$types = array_merge( $types, array_values( wp_list_pluck( $tab[ 'fields' ], 'type', '' ) ) );
				}
				foreach( $types as $type ) {
					if( $handler = AIOM_Renderer::get_field_handler( $type ) ) {
						call_user_func( array( $handler, 'enqueue' ) );
					}
				}
			}
		}

		/**
		 * Manage taxonomy terms list table columns
		 * @param array $columns Current columns
		 *
		 * @return array
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		public function manage_list_table_columns( $columns ) {
			$fields = $this->get_fields_with_columns();
			if( ! empty( $fields ) ) {
				foreach ( $fields as $id => $field ) {
					$columns[ $id ] = $field['table_col']['heading'];
				}
			}

			return $columns;
		}

	}

}