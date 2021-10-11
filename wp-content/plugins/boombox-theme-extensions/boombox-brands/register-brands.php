<?php
/**
 * Register Reactions
 *
 * @package BoomBox_Theme_Extensions
 */

// Prevent direct script access
if ( ! defined( 'ABSPATH' ) ) {
	die ( 'No direct script access allowed' );
}

class Boombox_Brands_Registrator {

	/**
	 * Holds current instance
	 * @var null
	 */
	private static $_instance = null;

	/**
	 * Get unique instance
	 * @return null
	 */
	public static function get_instance() {
		if( null === static::$_instance ) {
			static::$_instance = new self();
		}

		return static::$_instance;
	}

	/**
	 * Holds taxonomy key
	 * @var string
	 */
	private $_taxonomy = 'brand';

	/**
	 * Boombox_Brands_Registrator constructor.
	 */
	private function __construct() {
		$this->hooks();
	}

	/**
	 * A dummy magic method to prevent AIOM_Data_Saver from being cloned.
	 *
	 */
	public function __clone() {}

	/**
	 * Setup hooks
	 */
	private function hooks() {
		add_action( 'init', array( $this, 'register_taxonomy' ), 1 );
		add_action( 'save_post_post', array( $this, 'save_post_brand' ) );

		add_filter( 'boombox/admin/user/meta-boxes/structure', array( $this, 'edit_user_metabox_structure_add_brands' ), 10, 1 );
		add_filter( 'boombox/admin/taxonomy/meta-boxes/structure', array( $this, 'edit_taxonomy_metabox_structure_add_brands' ), 10, 3 );
		add_action( 'add_meta_boxes', array( $this, 'register_post_meta_boxes' ) );
	}

	/**
	 * Register 'post' post_type meta box
	 * @hooked in "add_meta_boxes" action
	 */
	public function register_post_meta_boxes() {
		add_meta_box(
		'post-brand-metabox',
			__( 'Brands', 'boombox-theme-extensions' ),
			array( $this, 'render_post_meta_box' ),
			'post',
			'side'
		);
	}

	/**
	 * Callback to register taxonomy
	 * @since 1.5.0
	 * @version 1.5.0
	 */
	public function register_taxonomy() {
		$labels = array(
			'name'                       => _x( 'Brands', 'Taxonomy General Name', 'boombox-theme-extensions' ),
			'singular_name'              => _x( 'Brand', 'Taxonomy Singular Name', 'boombox-theme-extensions' ),
			'menu_name'                  => __( 'Brands', 'boombox-theme-extensions' ),
			'all_items'                  => __( 'All Brands', 'boombox-theme-extensions' ),
			'parent_item'                => __( 'Parent Brand', 'boombox-theme-extensions' ),
			'parent_item_colon'          => __( 'Parent Brand:', 'boombox-theme-extensions' ),
			'new_item_name'              => __( 'New Brand Name', 'boombox-theme-extensions' ),
			'add_new_item'               => __( 'Add New Brand', 'boombox-theme-extensions' ),
			'edit_item'                  => __( 'Edit Brand', 'boombox-theme-extensions' ),
			'update_item'                => __( 'Update Brand', 'boombox-theme-extensions' ),
			'view_item'                  => __( 'View Brand', 'boombox-theme-extensions' ),
			'separate_items_with_commas' => __( 'Separate Brands with commas', 'boombox-theme-extensions' ),
			'add_or_remove_items'        => __( 'Add or Remove Brands', 'boombox-theme-extensions' ),
			'choose_from_most_used'      => __( 'Choose from the most used', 'boombox-theme-extensions' ),
			'popular_items'              => __( 'Popular Brands', 'boombox-theme-extensions' ),
			'search_items'               => __( 'Search Brands', 'boombox-theme-extensions' ),
			'not_found'                  => __( 'Not Found', 'boombox-theme-extensions' ),
			'no_terms'                   => __( 'No Brands', 'boombox-theme-extensions' ),
			'items_list'                 => __( 'Brands list', 'boombox-theme-extensions' ),
			'items_list_navigation'      => __( 'Brands list navigation', 'boombox-theme-extensions' )
		);
		$args = array(
			'labels'                     => $labels,
			'hierarchical'               => false,
			'public'                     => true,
			'show_ui'                    => true,
			'show_admin_column'          => false,
			'show_in_nav_menus'          => true,
			'show_tagcloud'              => false,
		);
		register_taxonomy( 'brand', array( 'post' ), $args );
	}

	/**
	 * Holds terms dropdown data
	 * @var null|array
	 * @since 1.5.0
	 * @version 1.5.0
	 */
	private $_terms_dropdown_data = null;

	/**
	 * Get terms dropdown data
	 * @return array|int|\WP_Error
	 */
	private function get_terms_dropdown_data() {
		if( is_null( $this->_terms_dropdown_data ) ) {

			$this->_terms_dropdown_data = array(
				'' => esc_html__( '- None -', 'boombox-theme-extensions' )
			);

			$terms = get_terms( array(
				'hide_empty' => false,
				'taxonomy'   => $this->_taxonomy,
				'fields'     => 'id=>name'
			) );

			if( ! is_wp_error( $terms ) ) {
				$this->_terms_dropdown_data += $terms;
			}
		}

		return $this->_terms_dropdown_data;
	}

	/**
	 * Callback to render custom meta box for brand taxonomy
	 * @param WP_Post $post Current post object
	 * @since 1.5.0
	 * @version 1.5.0
	 */
	public function render_post_meta_box( $post ) {

		$terms = get_terms( array(
			'hide_empty' => false,
			'taxonomy'   => $this->_taxonomy
		) );

		$post_brands = wp_get_object_terms( $post->ID, $this->_taxonomy, array(
			'orderby' => 'term_id',
			'order' => 'ASC'
		) );

		$brand = 0;
		if ( ! is_wp_error( $post_brands ) ) {
			if ( isset( $post_brands[0] ) && isset( $post_brands[0]->term_id ) ) {
				$brand = $post_brands[0]->term_id;
			}
		} ?>
		<p class="post-attributes-label-wrapper">
			<label class="post-attributes-label" for="post_brand"><?php esc_html_e( 'Select Brand', 'boombox-theme-extensions' ); ?></label>
		</p>
		<select name="post_brand" id="post_brand" class="regular-text">
		<option value=""><?php esc_html_e( '- None -', 'boombox-theme-extensions' ); ?></option>
		<?php foreach ( $terms as $term ) { ?>
			<option value="<?php echo esc_attr( $term->term_id ); ?>" <?php selected( $term->term_id, $brand ); ?>><?php echo esc_html( $term->name ); ?></option>
		<?php } ?>
		</select><?php
	}

	/**
	 * Save brand meta box data.
	 * @param int $post_id The ID of the post that's being saved.
	 * @since 1.5.0
	 * @version 1.5.0
	 */
	public function save_post_brand( $post_id ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! isset( $_POST['post_brand'] ) ) {
			return;
		}

		$brand_id = sanitize_text_field( $_POST['post_brand'] );
		if( $brand_id ) {
			$term = get_term_by( 'id', $brand_id, $this->_taxonomy );
			if( ! empty( $term ) && ! is_wp_error( $term ) ) {
				wp_set_object_terms( $post_id, $term->term_id, $this->_taxonomy, false );
			}
		} else {
			wp_delete_object_term_relationships( $post_id, $this->_taxonomy );
		}
	}

	/**
	 * Add brands choices to user advanced fields
	 * @param array $structure Current structure
	 *
	 * @return array
	 * @since 1.5.0
	 * @version 1.5.0
	 */
	public function edit_user_metabox_structure_add_brands( $structure ) {

		$structure[ 'tab_main' ][ 'fields' ][ 'boombox_user_brand_id' ] = array(
			'type'     => 'select',
			'label'    => esc_html__( 'Associated Brand', 'boombox-theme-extensions' ),
			'choices'  => $this->get_terms_dropdown_data(),
			'default'  => '',
			'order'    => 20,
		);

		return $structure;
	}

	/**
	 * Add brands choices to taxonomy advanced fields
	 * @param array $structure Current structure
	 * @param string $id Meta box ID
	 * @param string $taxonomy Taxonomy the structure associated with
	 *
	 * @return array
	 * @since 1.5.0
	 * @version 1.5.0
	 */
	public function edit_taxonomy_metabox_structure_add_brands( $structure, $id, $taxonomy ) {
		if( in_array( $taxonomy, array( 'category', 'post_tag' ) ) ) {
			// tab: Brands
			$structure['tab_brands'] = array(
				'title'  => esc_html__( 'Brands', 'boombox' ),
				'active' => false,
				'icon'   => false,
				'order'  => 50,
				'fields' => array(
					'boombox_' . $taxonomy . '_brand_id' => array(
						'type'       => 'select',
						'label'      => esc_html__( 'Associated Brand', 'boombox-theme-extensions' ),
						'choices'    => $this->get_terms_dropdown_data(),
						'standalone' => true,
						'default'    => '',
						'order'      => 20,
					)
					// other fields go here
				)
			);
		}
		return $structure;
	}

}

Boombox_Brands_Registrator::get_instance();