<?php
/**
 * Library meta boxes rendering helper
 *
 * @package "All In One Meta" library
 * @since   1.0.0
 * @version 1.0.0
 */

// Prevent direct script access.
if ( !defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

if( ! class_exists( 'AIOM_Renderer' ) ) {

	/**
	 * Class AIOM_Renderer
	 * @since 1.0.0
	 * @version 1.0.0
	 */
	final class AIOM_Renderer {
		
		/**
		 * Holds structure for rendering
		 * @var array
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		private $structure = array();
		
		/**
		 * Sanitize fields structure
		 * @param array $structure structure to sanitize
		 * @return array
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		private function sanitize_structure( $structure ) {
			
			// remove empty tabs
			$filtered = array();
			foreach( $structure as $tab_id => $tab ) {
			    $tab = wp_parse_args( $tab, array(
                    'title'  => '',
                    'active' => false,
                    'icon'   => '',
                    'order'  => 10,
                    'fields' => array()
                ) );
				if( empty( $tab[ 'fields' ] ) ) {
					continue;
				}
				
				$filtered[ $tab_id ] = $tab;
			}
			
			// sort by order value
			uasort( $filtered, function ( $a, $b ) {
				return ( absint( $a[ 'order' ] ) - absint( $b[ 'order' ] ) );
			} );
			
			return $filtered;
		}
		
		/**
		 * Holds structure data
		 * @var array
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		private $data = array();
		
		/**
		 * Holds data single meta key
		 * @var string
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		private $meta_key = '';
		
		/**
		 * Holds meta box context
		 * @var string
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		private $context = '';
		
		/**
		 * The object instance where meta box is rendered
		 * @var null|WP_Post|WP_Term
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		private $object = null;
		
		/**
		 * Additional arguments
		 * @var array
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		private $args = array();
		
		/**
		 * @param string $type Field type
		 *
		 * @return string Field handler class name
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		public static function get_field_handler( $type ) {
			
			$type = strtr( strtolower( $type ), array( '-' => '_' ) );
			
			switch ( $type ) {
				case 'text':
					$data = array(
						'class' => 'AIOM_Text_Field',
						'path'  => dirname( __DIR__ ) . DIRECTORY_SEPARATOR . 'fields' . DIRECTORY_SEPARATOR . 'text.php'
					);
					
					break;
				case 'number':
					$data = array(
						'class' => 'AIOM_Number_Field',
						'path'  => dirname( __DIR__ ) . DIRECTORY_SEPARATOR . 'fields' . DIRECTORY_SEPARATOR . 'number.php'
					);
					
					break;
				case 'url':
					$data = array(
						'class' => 'AIOM_URL_Field',
						'path'  => dirname( __DIR__ ) . DIRECTORY_SEPARATOR . 'fields' . DIRECTORY_SEPARATOR . 'url.php'
					);
					
					break;
				case 'textarea':
					$data = array(
						'class' => 'AIOM_Textarea_Field',
						'path'  => dirname( __DIR__ ) . DIRECTORY_SEPARATOR . 'fields' . DIRECTORY_SEPARATOR . 'textarea.php'
					);
					
					break;
				case 'checkbox':
					$data = array(
						'class' => 'AIOM_Checkbox_Field',
						'path'  => dirname( __DIR__ ) . DIRECTORY_SEPARATOR . 'fields' . DIRECTORY_SEPARATOR . 'checkbox.php'
					);
					
					break;
				case 'radio':
					$data = array(
						'class' => 'AIOM_Radio_Field',
						'path'  => dirname( __DIR__ ) . DIRECTORY_SEPARATOR . 'fields' . DIRECTORY_SEPARATOR . 'radio.php'
					);
					
					break;
				case 'select':
					$data = array(
						'class' => 'AIOM_Select_Field',
						'path'  => dirname( __DIR__ ) . DIRECTORY_SEPARATOR . 'fields' . DIRECTORY_SEPARATOR . 'select.php'
					);
					
					break;
				case 'custom':
					$data = array(
						'class' => 'AIOM_Custom_Field',
						'path'  => dirname( __DIR__ ) . DIRECTORY_SEPARATOR . 'fields' . DIRECTORY_SEPARATOR . 'custom.php'
					);
					
					break;
				case 'radio_image':
					$data = array(
						'class' => 'AIOM_Radio_Image_Field',
						'path'  => dirname( __DIR__ ) . DIRECTORY_SEPARATOR . 'fields' . DIRECTORY_SEPARATOR . 'radio_image.php'
					);
					
					break;
				case 'multicheck':
					$data = array(
						'class' => 'AIOM_Multicheck_Field',
						'path'  => dirname( __DIR__ ) . DIRECTORY_SEPARATOR . 'fields' . DIRECTORY_SEPARATOR . 'multicheck.php'
					);
					
					break;
				case 'color':
					$data = array(
						'class' => 'AIOM_Color_Field',
						'path'  => dirname( __DIR__ ) . DIRECTORY_SEPARATOR . 'fields' . DIRECTORY_SEPARATOR . 'color.php'
					);
					
					break;
				case 'multicolor':
					$data = array(
						'class' => 'AIOM_Multicolor_Field',
						'path'  => dirname( __DIR__ ) . DIRECTORY_SEPARATOR . 'fields' . DIRECTORY_SEPARATOR . 'multicolor.php'
					);
					
					break;
				case 'image':
					$data = array(
						'class' => 'AIOM_Image_Field',
						'path'  => dirname( __DIR__ ) . DIRECTORY_SEPARATOR . 'fields' . DIRECTORY_SEPARATOR . 'image.php'
					);
					
					break;
				case 'gallery':
					$data = array(
						'class' => 'AIOM_Gallery_Field',
						'path'  => dirname( __DIR__ ) . DIRECTORY_SEPARATOR . 'fields' . DIRECTORY_SEPARATOR . 'gallery.php'
					);

					break;
				case 'video_url':
					$data = array(
						'class' => 'AIOM_Video_URL_Field',
						'path'  => dirname( __DIR__ ) . DIRECTORY_SEPARATOR . 'fields' . DIRECTORY_SEPARATOR . 'video_url.php'
					);
					
					break;
				case 'date':
					$data = array(
						'class' => 'AIOM_Date_Field',
						'path'  => dirname( __DIR__ ) . DIRECTORY_SEPARATOR . 'fields' . DIRECTORY_SEPARATOR . 'date.php'
					);

					break;
				default:
					// this is a not build case, so let's provide possibility to setup custom field type
					$default = array(
						'class' => '',
						'path'  => ''
					);
					
					$data = wp_parse_args( apply_filters( 'aiom/custom_field_type_config', $default, $type ), $default );
			}
			
			if(
                isset( $data[ 'path' ] )
                && $data[ 'path' ]
                && isset( $data[ 'class' ] )
                && $data[ 'class' ]
                && is_file( $data[ 'path' ] )
                && ! class_exists( $data[ 'class' ] )
            ) {
                require_once( $data[ 'path' ] );
			}
			
			return $data[ 'class' ];
		}

		/**
		 * Holds hash field rendered status
		 * @var bool
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		private static $did_hash_field = false;
		
		/**
		 * AIOM_Renderer constructor.
		 *
		 * @param array                         $structure Structure for rendering
		 * @param array                         $data      Structure data
		 * @param string                        $meta_key  Meta key for group
		 * @param string                        $context   Meta box context
		 * @param WP_Post|WP_Term|WP_User|array $object    Current object
		 * @param array                         $args      Additional arguments
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		public function __construct( array $structure, array $data, $meta_key, $context, $object, $args = array() ) {
			$this->structure = $this->sanitize_structure( $structure );
			$this->data      = $data;
			$this->meta_key  = $meta_key;
			$this->context   = $context;
			$this->object    = $object;
			$this->args      = wp_parse_args( $args, array(
				'id'    => '',
				'title' => ''
			) );
		}

		/**
         * Get active tab hash from URL
		 * @return string
         * @since 1.0.0
         * @version 1.0.0
		 */
		private static function get_tab_hash_from_query() {
		    return ( isset( $_GET[ 'aiom-tab' ] ) && $_GET[ 'aiom-tab' ] ) ? $_GET[ 'aiom-tab' ] : '';
        }

		/**
		 * Render structure
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		public function render() {
			
			$class = 'aiom-advanced-fields aiom-context-' . $this->context;
			$render_title = false;
			if( is_a( $this->object, 'WP_Post' ) ) {
				$class .= ' aiom-post-fields';
				if( $this->object->post_type != 'post' ) {
					$class .= ' aiom-' . $this->object->post_type . '-fields';
				}
			} else if( is_a( $this->object, 'WP_User' ) ) {
				$class .= ' aiom-user-fields';
				$render_title = true;
			} else if( is_a( $this->object, 'WP_Term' ) ) {
				$class .= ' aiom-term-fields aiom-' . $this->object->taxonomy . '-fields';
				$render_title = true;
			} else if( is_array( $this->object ) ) {
				if( $this->object[ 'type' ] == 'taxonomy' ) {
					$render_title = true;
					$class .= ' aiom-term-fields aiom-' . $this->object[ 'taxonomy' ] . '-fields';
				} elseif( $this->object[ 'type' ] == 'user' ) {
					$class .= ' aiom-user-fields';
					$render_title = true;
				}
			} ?>
			<div <?php if( $this->args[ 'id' ] ) { printf( 'id="%s"', esc_attr( $this->args[ 'id' ] ) ); } ?> class="<?php echo esc_attr( $class ); ?>">
				<?php if( $render_title && $this->args[ 'title' ] ) { ?>
					<h2 class="aiom-title"><?php echo esc_html( $this->args[ 'title' ] ); ?></h2>
				<?php } ?>

				<div class="aiom-inner">
					<?php
					$this->render_flagman();
					wp_nonce_field( 'aiom_nonce_action', 'aiom_nonce' );
					
					if( count( $this->structure ) > 1 ) {
						$this->as_tabs();
					} else {
						$this->as_single();
					} ?>
				</div>
			</div>
			<?php
		}

		/**
         * Get tab unique hash
		 * @param string $tab_id Current tab ID
		 *
		 * @return string
         * @since 1.0.0
         * @version 1.0.0
		 */
		private function get_tab_hash( $tab_id ) {
            return substr( md5( $this->args[ 'id' ] . '-' . $tab_id ), 0, 7 );
        }

		/**
         * Check for tab activity
		 * @param string $tab_id  Tab ID
		 * @param bool   $default Tab activity default state
		 *
		 * @return bool
         * @since 1.0.0
         * @version 1.0.0
		 */
		private function is_tab_active( $tab_id, $default ) {
            if( $hash = self::get_tab_hash_from_query() ) {
                return ( $hash == $this->get_tab_hash( $tab_id ) );
            }

            return $default;
		}

		/**
		 * Render structure as tabs
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		private function as_tabs() { ?>
            <div class="aiom-admin-tabs aiom-admin-tabs-horizontal">
                <ul class="aiom-admin-tabs-menu">
					<?php foreach ( $this->structure as $tab_id => $tab ) {
					    $is_active = $this->is_tab_active( $tab_id, $tab[ 'active' ] ); ?>
                        <li class="<?php echo $is_active ? 'active' : ''; ?>" data-hash="<?php echo $this->get_tab_hash( $tab_id ); ?>">
                            <a href="#<?php echo $tab_id; ?>">
								<?php echo ( isset( $tab[ 'icon' ] ) && $tab[ 'icon' ] ) ? $tab[ 'icon' ] : ''; ?>
								<?php echo $tab[ 'title' ]; ?>
                            </a>
                        </li>
					<?php } ?>
                </ul>
                <div class="aiom-admin-tabs-content">
					<?php foreach ( $this->structure as $tab_id => $tab ) {
						$this->tab_content( $tab, $tab_id );
					} ?>
                </div>
            </div>
			<?php
		}

		/**
		 * Render structure as single tab
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		private function as_single() {
			reset( $this->structure );
			$tab_id = key( $this->structure );

			$this->tab_content( $this->structure[ $tab_id ], $tab_id, true );
		}

		/**
		 * Render single tab content
		 *
		 * @param array      $tab       Tab data
		 * @param string     $tab_id    Tab ID
		 * @param bool|false $is_single If this is a single tab
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		private function tab_content( $tab, $tab_id, $is_single = false ) {
			$fields = $tab[ 'fields' ];
			uasort( $fields, function ( $a, $b ) {
				$a_order = isset( $a[ 'order' ] ) ? absint( $a[ 'order' ] ) : 0;
				$b_order = isset( $b[ 'order' ] ) ? absint( $b[ 'order' ] ) : 0;

				if( $a_order == $b_order ) {
					$a_order = isset( $a[ 'sub_order' ] ) ? absint( $a[ 'sub_order' ] ) : 0;
					$b_order = isset( $b[ 'sub_order' ] ) ? absint( $b[ 'sub_order' ] ) : 0;
				}

				return ( $a_order - $b_order );
			} );

			$class = 'aiom-admin-tab-content';
			if( $is_single || $this->is_tab_active( $tab_id, $tab[ 'active' ] ) ) {
				$class .= ' active';
			} ?>
            <div id="<?php echo esc_attr( $tab_id ); ?>" class="<?php echo esc_attr( $class ); ?>">
				<?php
				foreach ( $fields as $name => $field ) {
					$field[ 'id' ] = $name;
					if( ! ( isset( $field[ 'name' ] ) && $field[ 'name' ] ) ) {
						$field = array_merge( $field, array( 'name' => $name ) );
					}
					$this->field( $field, $tab_id );
				} ?>
            </div>
			<?php
		}

		/**
		 * Render flagman hidden field to indicate data save requirement
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		private function render_flagman() {
			echo '<input type="hidden" name="has_aiom_data" value="1" />';
		}

		/**
		 * Render active tab hidden hash field
         * @since 1.0.0
         * @version 1.0.0
		 */
		public static function render_hash_field() {
			if( self::$did_hash_field ) {
				return;
			}

			echo '<input type="hidden" id="aiom-active-tab-hash" name="aiom_active_tab_hash" value="' . self::get_tab_hash_from_query() . '" />';
			self::$did_hash_field = true;
        }

		/**
		 * Render field
		 *
		 * @param array       $field  Field arguments
		 * @param string|bool $tab_id Field tab ID
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		private function field( $field, $tab_id = false ) {

			if( $handler = self::get_field_handler( $field[ 'type' ] ) ) {
				/** @var $instance AIOM_Base_Field */
				$instance = new $handler( $field, $tab_id, $this->data, $this->structure, $this->meta_key );
				$instance->render();
			}

		}
		
	}
	
}