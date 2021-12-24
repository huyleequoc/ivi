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

if( ! class_exists( 'AIOM_User_Metabox' ) ) {

	if( ! class_exists( 'AIOM_Object_Metabox' ) ) {
		require_once 'class-aiom-object.php';
	}

	/**
	 * Class AIOM_User_Metabox
	 * @since 1.0.0
	 * @version 1.0.0
	 */
	final class AIOM_User_Metabox extends AIOM_Object_Metabox {

		/**
		 * Hold user role: any role or * for all roles
		 * @var array
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		private $role = array();

		/**
		 * Holds priority for meta boxes form rendering hooks
		 * @var int
		 */
		private $priority = 10;

		/**
		 * Holds user fields owning status
		 * @var bool
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		private $has_fields = null;

		/**
		 * Holds current user
		 * @var null
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		private $user = null;

		/**
		 * Get current user
		 * @return null | WP_User
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		public function get_current_user() {
			if( null === $this->user ) {
				$this->user = wp_get_current_user();
			}

			return $this->user;
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
			$args = wp_parse_args( $args, array(
				'id'        => '',
				'title'     => '',
				'role'      => array( '*' ),
				'priority'  => $this->priority
			) );
			$args[ 'role' ] = (array)$args[ 'role' ];
			$args[ 'priority' ] = absint( $args[ 'priority' ] );
			if( ! $args[ 'id' ] && $args[ 'title' ] ) {
				$args[ 'id' ] = 'aiom-user-' . sanitize_title_with_dashes( $args[ 'title' ] );
			}

			return ( $args[ 'id' ] && $args[ 'title' ] && ! empty( $args[ 'role' ] ) );
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
			$this->role     = $args[ 'role' ];
			$this->priority = $args[ 'priority' ];
		}

		/**
		 * Check weather User has fields
		 * @param WP_User $user User instance
		 * @return bool
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		private function has_fields( $user ) {
			if( null === $this->has_fields ) {
				if( in_array( '*', $this->role ) ) {
					$has_fields = true;
				} else {
					$has_fields = false;
					foreach ( $this->role as $role ) {
						if( user_can( $user, $role ) ) {
							$has_fields = true;
							break;
						}
					}
				}
				$this->has_fields = $has_fields;
			}

			return $this->has_fields;
		}

		/**
		 * Get fields
		 * @return array
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		protected function get_fields(){
			return apply_filters( 'aiom/user/fields', parent::get_fields(), $this->get_current_user() );
		}

		/**
		 * Setup generic actions
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		protected function hooks() {
			parent::hooks();

			add_action( 'user_new_form', array( $this, 'render_add_form' ), $this->priority );
			add_action( 'show_user_profile', array( $this, 'render_edit_form' ), $this->priority );
			add_action( 'edit_user_profile', array( $this, 'render_edit_form' ), $this->priority );

			add_action( 'user_new_form', array( 'AIOM_Renderer', 'render_hash_field' ), 99999999 );
			add_action( 'show_user_profile', array( 'AIOM_Renderer', 'render_hash_field' ), 99999999 );
			add_action( 'edit_user_profile', array( 'AIOM_Renderer', 'render_hash_field' ), 99999999 );

			add_action( 'aiom_save_user', array( $this, 'save' ), 10, 1 );

			add_filter( 'manage_users_columns', array( $this, 'manage_list_table_columns' ) );
			add_filter( 'manage_users_custom_column', array( $this, 'edit_list_table_column_content' ), 10, 3 );
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
				&& in_array( $screen->id, array( 'user', 'user-edit', 'profile' ) )
			);
		}

		/**
		 * Enqueue Scripts and Styles
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		public function admin_enqueue_scripts() {
			if ( $this->has_fields( wp_get_current_user() ) ) {
				parent::admin_enqueue_scripts();
			}
		}

		/**
		 * Render meta box
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		public function render_add_form() {

			$this->maybe_include_renderer();

			$renderer = new AIOM_Renderer(
				$this->get_fields(),
				array(),
				AIOM_Config::get_tax_meta_key(),
				'add',
				array(
					'type' => 'user',
				),
				array(
					'id'    => $this->id,
					'title' => $this->title
				)
			);
			$renderer->render();
		}

		/**
		 * Render meta box
		 * @param WP_User $object
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		public function render_edit_form( $object ) {

			if( ! $this->has_fields( $object ) ) {
				return;
			}

			$meta_key = AIOM_Config::get_user_meta_key();
			$data = (array)aiom_get_user_meta( $object->ID, $meta_key );

			// in case of absentee standalone fields in single meta data - use they real meta value
			foreach( $this->get_fields() as $tab_id => $tab_data ) {
				$standalone_fields = wp_list_filter( $tab_data[ 'fields' ], array( 'standalone' => true ) );
				foreach( $standalone_fields as $name => $field_data ) {
					if( ! isset( $data[ $name ] ) ) {
						$data[ $name ] = get_user_meta( $object->ID, $name, true );
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
					'title' => $this->title
				)
			);
			$renderer->render();
		}

		/**
		 * Add data to data saver
		 *
		 * @param int    $user_id  User ID
		 * @param array  $data     Data to save: key->value pairs
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		private function add_saver_data( $user_id, $data ) {

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
						$value = apply_filters( 'aiom/user/save_value?field=' . $key, $value, $user_id );

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
			$meta_values = apply_filters( 'aiom/user/save_values', $meta_values, $user_id );

			$saver = AIOM_Data_Saver::get_instance();
			$saver->add_standalone_data( $standalone_values );
			$saver->add_data( $meta_values );

		}

		/**
		 * Save term meta data
		 * @param int $user_id  Term ID.
		 *
		 * @return bool|int
		 * @since 1.0.0
		 * @version 1.0.0
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		public function save( $user_id ) {

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
			if ( ! current_user_can( 'edit_user', $user_id ) ) {
				return false;
			}

			/** make sure we are working with valid user role */
			$user = get_user_by( 'ID', $user_id );
			if( ! $this->has_fields( $user ) ) {
				return false;
			}

			/** it's save to process */
			$meta_key = AIOM_Config::get_user_meta_key();
			$meta_data = isset( $_POST[ $meta_key ] ) ? $_POST[ $meta_key ] : array();

			/** Add data to saver: it must take care */
			$this->add_saver_data( $user_id, $meta_data );
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
				$value = aiom_get_user_meta( $object_id, $column_name );
				$callback = $field[ 'table_col' ][ 'callback' ];
				$content = call_user_func( $callback, $value, $column_name, $object_id, $field );
			}

			return $content;
		}

	}

}