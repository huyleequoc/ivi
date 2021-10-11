<?php
/**
 * Generic field for metaboxes
 *
 * @package "All In One Meta" library
 * @since   1.0.0
 * @version 1.0.0
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

/**
 * Class AIOM_Base_Field
 * @since 1.0.0
 * @version 1.0.0
 */
abstract class AIOM_Base_Field {

	/**
	 * Holds group name
	 * @var string
	 * @since 1.0.0
	 * @version 1.0.0
	 */
	private $group = '';

	/**
	 * Holds field name
	 * @var string
	 * @since 1.0.0
	 * @version 1.0.0
	 */
	private $name = '';

	/**
	 * Set field name
	 * @param array $args Field arguments
	 * @since 1.0.0
	 * @version 1.0.0
	 */
	private function set_name( $args ) {
		if( isset( $args[ 'name' ] ) && $args[ 'name' ] ) {
			$this->name = $args[ 'name' ];
		}
	}

	/**
	 * Get field name
	 * @param string $postfix Additional postfix
	 * @return string
	 * @since 1.0.0
	 * @version 1.0.0
	 */
	protected function get_name( $postfix = '' ) {
		return $this->group . '[' . $this->name . ']' . $postfix;
	}

	/**
	 * Holds field ID
	 * @var string
	 * @since 1.0.0
	 * @version 1.0.0
	 */
	private $id = '';

	/**
	 * Set field ID
	 * @param array $args Field arguments
	 * @since 1.0.0
	 * @version 1.0.0
	 */
	private function set_id( $args ) {
		if( isset( $args[ 'id' ] ) && $args[ 'id' ] ) {
			$this->id = $args[ 'id' ];
		}
	}

	/**
	 * Get field ID
	 * @return string
	 * @since 1.0.0
	 * @version 1.0.0
	 */
	protected function get_id() {
		return $this->id;
	}

	/**
	 * Holds field HTML classes
	 * @var string
	 * @since 1.0.0
	 * @version 1.0.0
	 */
	private $class = '';

	/**
	 * Set field HTML classes
	 * @param array $args Field arguments
	 * @since 1.0.0
	 * @version 1.0.0
	 */
	private function set_class( $args ) {
		if( isset( $args[ 'class' ] ) && ! empty( $args[ 'class' ] ) ) {
			if( is_string( $args[ 'class' ] ) ) {
				$this->class = sanitize_html_class( $args[ 'class' ] );
			} elseif( is_array( $args[ 'class' ] ) ) {
				$this->class = sanitize_html_class( implode( ' ', array_filter( $args[ 'class' ] ) ) );
			}
		}
	}

	/**
	 * Get field HTML classes
	 * @return string
	 * @since 1.0.0
	 * @version 1.0.0
	 */
	protected function get_class() {
		return $this->class;
	}

	/**
	 * Holds field label
	 * @var string
	 * @since 1.0.0
	 * @version 1.0.0
	 */
	private $label = '';

	/**
	 * Set field label
	 * @param array $args Field arguments
	 * @since 1.0.0
	 * @version 1.0.0
	 */
	private function set_label( $args ) {
		if( isset( $args[ 'label' ] ) && $args['label'] ) {
			$this->label = $args[ 'label' ];
		}
	}

	/**
	 * Get field label
	 * @return string
	 * @since 1.0.0
	 * @version 1.0.0
	 */
	protected function get_label() {
		return $this->label;
	}

	/**
	 * Holds field description
	 * @param array $args Field arguments
	 * @since 1.0.0
	 * @version 1.0.0
	 */
	private $description = '';

	/**
	 * Set field description
	 * @param array $args Field arguments
	 * @since 1.0.0
	 * @version 1.0.0
	 */
	private function set_description( $args ) {
		if( isset( $args[ 'description' ] ) && $args[ 'description' ] ) {
			$this->description = $args[ 'description' ];
		}
	}

	/**
	 * Get field description
	 * @return string
	 * @since 1.0.0
	 * @version 1.0.0
	 */
	protected function get_description() {
		return $this->description;
	}

	/**
	 * Holds form field field attributes
	 * @var array
	 * @since 1.0.0
	 * @version 1.0.0
	 */
	private $attributes = array();

	/**
	 * Set field HTML attributes
	 * @param array $args Field arguments
	 * @since 1.0.0
	 * @version 1.0.0
	 */
	private function set_attributes( $args ) {
		if( isset( $args[ 'attributes' ] ) && ! empty( $args[ 'attributes' ] ) && is_array( $args[ 'attributes' ] ) ) {
			foreach( array_filter( $args[ 'attributes' ] ) as $f_attr => $f_val ) {
				$this->attributes[ $f_attr ] = sprintf( '%s="%s"', $f_attr, esc_attr( $f_val ) );
			}
		}
	}

	/**
	 * Get form field attributes
	 * @return string
	 * @since 1.0.0
	 * @version 1.0.0
	 */
	protected function get_attributes() {
		return implode( ' ', array_values( $this->attributes ) );
	}

	/**
	 * Check if has attribute
	 * @param string $attribute Attribute name
	 *
	 * @return bool
	 * @since 1.0.0
	 * @version 1.0.0
	 */
	protected function has_attribute( $attribute ) {
		return array_key_exists( $attribute, $this->attributes );
	}

	/**
	 * Holds field value rendering callback
	 * @var callable|null
	 * @since 1.0.0
	 * @version 1.0.0
	 */
	private $render_callback = null;

	/**
	 * Set field render callback
	 * @param array $args Field arguments
	 * @since 1.0.0
	 * @version 1.0.0
	 */
	private function set_render_callback( $args ) {
		if( isset( $args[ 'render_callback' ] ) && is_callable( $args[ 'render_callback' ] ) ) {
			$this->render_callback = $args[ 'render_callback' ];
		}
	}

	/**
	 * Get field value rendering callback
	 * @return null|string
	 * @since 1.0.0
	 * @version 1.0.0
	 */
	protected function get_render_callback() {
		return $this->render_callback;
	}

	/**
	 * Holds field default value
	 * @var mixed
	 * @since 1.0.0
	 * @version 1.0.0
	 */
	private $default = null;

	/**
	 * Set field default value
	 * @param array $args Field arguments
	 * @since 1.0.0
	 * @version 1.0.0
	 */
	private function set_default( $args ) {
		if( isset( $args[ 'default' ] ) && $args[ 'default' ] ) {
			$this->default = $args[ 'default' ];
		}
	}

	/**
	 * Get field default value
	 * @return mixed
	 * @since 1.0.0
	 * @version 1.0.0
	 */
	protected function get_default() {
		return $this->default;
	}

	/**
	 * Holds field actual value
	 * @var mixed
	 * @since 1.0.0
	 * @version 1.0.0
	 */
	private $value = null;

	/**
	 * Set field value
	 * @param array $data Current data
	 * @since 1.0.0
	 * @version 1.0.0
	 */
	private function set_value( $data ) {
		$value = isset( $data[ $this->name ] ) ? maybe_unserialize( $data[ $this->name ] ) : $this->default;
		if( $this->render_callback ) {
			$value = call_user_func( $this->render_callback, $value );
		}
		$this->value = $value;
	}

	/**
	 * Get field actual value
	 * @return mixed
	 * @since 1.0.0
	 * @version 1.0.0
	 */
	protected function get_value() {
		return $this->value;
	}

	/**
	 * Holds field wrapper classes
	 * @var string
	 * @since 1.0.0
	 * @version 1.0.0
	 */
	private $wrapper_class = '';

	/**
	 * Set field wrapper classes
	 * @param array $args Field arguments
	 * @since 1.0.0
	 * @version 1.0.0
	 */
	private function set_wrapper_class( $args ) {
		if( isset( $args[ 'wrapper_class' ] ) && ! empty( $args[ 'wrapper_class' ] ) ) {
			if( is_string( $args[ 'wrapper_class' ] ) ) {
				$this->wrapper_class = sanitize_html_class( $args[ 'wrapper_class' ] );
			} elseif( is_array( $args[ 'wrapper_class' ] ) ) {
				$this->wrapper_class = sanitize_html_class( implode( ' ', array_filter( $args[ 'wrapper_class' ] ) ) );
			}
		}
	}

	/**
	 * Get field wrapper classes
	 * @return string
	 * @since 1.0.0
	 * @version 1.0.0
	 */
	protected function get_wrapper_class() {
		return $this->wrapper_class;
	}

	/**
	 * Holds field wrapper attributes
	 * @var string
	 * @since 1.0.0
	 * @version 1.0.0
	 */
	private $wrapper_attributes = '';

	/**
	 * Set field wrapper attributes
	 * @param array $args Field arguments
	 * @since 1.0.0
	 * @version 1.0.0
	 */
	private function set_wrapper_attributes( $args ) {
		if(
			isset( $args[ 'wrapper_attributes' ] )
			&& ! empty( $args[ 'wrapper_attributes' ] )
			&& is_array( $args[ 'wrapper_attributes' ] )
		) {
			$wrapper_attributes = array();
			foreach( array_filter( $args[ 'wrapper_attributes' ] ) as $attr => $val ) {
				$wrapper_attributes[ $attr ] = sprintf( '%s="%s"', $attr, esc_attr( $val ) );
			}
			$this->wrapper_attributes = implode( ' ', $wrapper_attributes );
		}
	}

	/**
	 * Get field wrapper attributes
	 * @return string
	 * @since 1.0.0
	 * @version 1.0.0
	 */
	protected function get_wrapper_attributes() {
		return $this->wrapper_attributes;
	}

	/**
	 * Holds field active callback generated element
	 * @var string
	 * @since 1.0.0
	 * @version 1.0.0
	 */
	private $active_callback = '';

	/**
	 * Set field active callback
	 * @param array  $args      Field arguments
	 * @param array  $structure Fields structure for current tab
	 * @param string $tab_id    Field tab ID
	 * @param array  $data      Current data
	 * @since 1.0.0
	 * @version 1.0.0
	 */
	private function set_active_callback( $args, $structure, $tab_id, $data ) {
		if( isset( $args[ 'active_callback' ] ) && is_array( $args[ 'active_callback' ] ) && ! empty( $args[ 'active_callback' ] ) ) {
			$active_callback_data = $this->generate_active_callback_data(
				$args,
				$structure[ $tab_id ][ 'fields' ],
				$data
			);

			$this->active_callback = $active_callback_data->element;

			if( ! $active_callback_data->visible ) {
				$this->wrapper_class .= ' aiom-hidden';
			}
		}
	}

	/**
	 * Get field active callback generated element
	 * @return string
	 * @since 1.0.0
	 * @version 1.0.0
	 */
	protected function get_active_callback() {
		return $this->active_callback;
	}

	/**
	 * Parse field arguments
	 * @param array $args Current arguments
	 *
	 * @return array
	 * @since 1.0.0
	 * @version 1.0.0
	 */
	public static function parse_field_args( $args ) {
		return $args;
	}

	/**
	 * AIOM_Base_Field constructor.
	 * @param array $args
	 * @param string|bool|null  $tab_id     Field tab ID
	 * @param array             $data       Meta data
	 * @param array             $structure  Fields structure
	 * @param string            $group      Field group
	 * @since 1.0.0
	 * @version 1.0.0
	 */
	public function __construct( array $args, $tab_id, array $data, array $structure, $group ) {

		/***** Parse field arguments */
		$args = static::parse_field_args( $args );

		/***** Group key */
		$this->group = $group;

		/***** Field Name */
		$this->set_name( $args );
		if( ! $this->name ) {
			return;
		}

		/***** Field ID */
		$this->set_id( $args );

		/***** Field class */
		$this->set_class( $args );

		/***** Field label */
		$this->set_label( $args );

		/***** Field description */
		$this->set_description( $args );

		/***** Field attributes */
		$this->set_attributes( $args );

		/***** Field value callback */
		$this->set_render_callback( $args );

		/***** Field default value */
		$this->set_default( $args );

		/***** Field value */
		$this->set_value( $data );

		/***** Field wrapper classes */
		$this->set_wrapper_class( $args );

		/***** Field wrapper attributes */
		$this->set_wrapper_attributes( $args );

		/***** Field active callback */
		$this->set_active_callback( $args, $structure, $tab_id, $data );

	}

	/**
	 * Enqueue required assets
	 * @since 1.0.0
	 * @version 1.0.0
	 */
	public static function enqueue() {}

	/**
	 * Get supported comparison operators
	 * @return array
	 * @since 1.0.0
	 * @version 1.0.0
	 */
	private function get_comparison_operators() {
		return array(
			'===',
			'==',
			'=',
			'!==',
			'!=',
			'>=',
			'<=',
			'>',
			'<',
			'IN',
			'NOT IN'
		);
	}

	/**
	 * Generate active callback data for current field
	 *
	 * @param array $args   Field arguments
	 * @param array $fields Current fields structure
	 * @param array $data   Current data
	 *
	 * @return stdClass
	 * @since 1.0.0
	 * @version 1.0.0
	 */
	private function generate_active_callback_data( $args, $fields, $data ) {
		$callback = $args[ 'active_callback' ];

		$relation = 'AND';
		if( isset( $callback['relation'] ) && in_array( strtoupper( $callback['relation'] ), array( 'AND', 'OR' ) ) ) {
			$relation = strtoupper( $callback['relation'] );
			unset( $callback['relation'] );
		}

		$superiors = array();
		$is_visible = ( $relation == 'AND' );
		$callback_data = array(
			'relation' => $relation,
			'fields'   => array(),
		);

		foreach( $callback as $requirement ) {
			$r = wp_parse_args( $requirement, array(
				'field_id' => false,
				'compare'  => '==',
				'value'    => ''
			) );

			$field_id = $r['field_id'];
			if( $field_id && isset( $fields[ $field_id ] ) && in_array( $r['compare'], $this->get_comparison_operators() ) ) {

				/***** check visibility */
				$parent_meta_key = isset( $fields[ $field_id ][ 'name' ] ) ? $fields[ $field_id ][ 'name' ] : $field_id;
				$parent_value = isset( $data[ $parent_meta_key ] ) ? $data[ $parent_meta_key ] : $fields[ $field_id ][ 'default' ];

				switch ( $r['compare'] ) {
					case '===':
						$show = ( $parent_value === $r['value'] ) ? true : false;
						break;
					case '==':
					case '=':
						$show = ( $parent_value == $r['value'] ) ? true : false;
						break;
					case '!==':
						$show = ( $parent_value !== $r['value'] ) ? true : false;
						break;
					case '!=':
						$show = ( $parent_value != $r['value'] ) ? true : false;
						break;
					case '>=':
						$show = ( $r['value'] >= $parent_value ) ? true : false;
						break;
					case '<=':
						$show = ( $r['value'] <= $parent_value ) ? true : false;
						break;
					case '>':
						$show = ( $r['value'] > $parent_value ) ? true : false;
						break;
					case '<':
						$show = ( $r['value'] < $parent_value ) ? true : false;
						break;
					case 'IN':
						$intersection = array_intersect( (array)$parent_value, (array)$r['value'] );
						$show = empty( $intersection ) ? false : true;
						break;
					case 'NOT IN':
						$intersection = array_intersect( (array)$parent_value, (array)$r['value'] );
						$show = empty( $intersection ) ? true : false;
						break;
					default:
						$show = ( $parent_value == $r['value'] ) ? true : false;
				}

				if( $relation == 'AND' ) {
					$is_visible = $is_visible && $show;
				} else {
					$is_visible = $is_visible || $show;
				}

				/***** Configure field */
				$r[ 'jq_selector' ] = '#' . $field_id;
				if( in_array( $fields[ $field_id ][ 'type' ], array( 'radio', 'radio-image', 'checkbox', 'multicheck' ) ) ) {
					$r[ 'jq_selector' ] .= ':checked';
				}

				/***** setup callback data */
				$callback_data[ 'fields' ][] = $r;
				$superiors[] = 'aiom-superior-' . $field_id;

			}
		}

		$return = new stdClass();
		$return->visible = $is_visible;
		$return->element = '';

		if( ! empty( $callback_data[ 'fields' ] ) ) {
			$superiors = implode( ' ', $superiors );
			$callback_data = json_encode( $callback_data );
			$return->element = sprintf( '<span class="aiom-hidden %s">%s</span>', $superiors, $callback_data );
		}

		return $return;
	}

	/**
	 * Render field
	 * @since 1.0.0
	 * @version 1.0.0
	 */
	abstract public function render();

}