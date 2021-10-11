<?php

if( ! class_exists( 'Zombify_AMP' ) ) {

	class Zombify_AMP {
		
		/**
		 * @var object
		 * Zombify_AMP instance
		 */
		private static $instance;

		/**
		 * @var array
		 *
		 * AMP js scripts
		 */
		private $scripts = array(
			'amp-form'      => 'https://cdn.ampproject.org/v0/amp-form-0.1.js',
			'amp-bind'      => 'https://cdn.ampproject.org/v0/amp-bind-0.1.js',
			'amp-analytics' => 'https://cdn.ampproject.org/v0/amp-analytics-0.1.js',
		);

		/**
		 * @var array
		 *
		 * AMP js scripts which going to be loaded as set via `set_scripts()` method
		 */
		private $required_scripts = array();
		
		/**
		 * Return the only existing instance of Zombify object
		 *
		 * @return Zombify_AMP
		 */
		public static function get_instance() {
			if ( static::$instance == null ) {
				static::$instance = new static();
			}
			
			return static::$instance;
		}
		
		/**
		 * Zombify_AMP constructor.
		 */
		private function __construct() {
			$this->setup_actions();
		}
		
		/**
		 * Setup actions
		 */
		private function setup_actions() {
			add_filter( 'amp_content_sanitizers', array( $this, 'add_sanitizers' ), 10, 2 );
			add_filter( 'amp_post_template_data', array( $this, 'edit_template_data' ), 10, 1 );
			add_action( 'zf_flush_post_by_id', array( $this, 'flush_post_amp_cache' ), 10, 1 );
		}
		
		/**
		 * Check if it's and AMP endpoint
		 * @return bool
		 */
		public function is_amp_endpoint() {
			return ( function_exists( 'is_amp_endpoint' ) && is_amp_endpoint() );
		}
		
		/**
		 * Add sanitizers
		 * @param $sanitizers
		 * @param $post
		 *
		 * @return mixed
		 */
		public function add_sanitizers($sanitizers, $post) {
			
			if( $post && get_post_meta($post->ID, 'zombify_data_type', true) ){
				
				// Add allowed attributes
				$globally_allowed_attributes = AMP_Allowed_Tags_Generated::get_allowed_attributes();
				$allowed_attributes = array_merge ( $globally_allowed_attributes, [
					'zf-amp-class' => array(),
					'zf-amp-text' => array(),
					'hidden' => array(),
					'data-imgur-id' => array()
				]);
				$sanitizers[ 'AMP_Tag_And_Attribute_Sanitizer' ][ 'amp_globally_allowed_attributes' ] = $allowed_attributes;
				
				// Add allowed tags
				$globally_allowed_tags = AMP_Allowed_Tags_Generated::get_allowed_tags();
				$allowed_tags = array_merge ($globally_allowed_tags, [
					'amp-imgur' => array(
						array(
							'attr_spec_list' => array(),
							'tag_spec' => array(),
						
						),
					),
				]);
				$sanitizers[ 'AMP_Tag_And_Attribute_Sanitizer' ][ 'amp_allowed_tags' ] = $allowed_tags;
			}
			return $sanitizers;
		}
		
		/**
		 * Edit template data
		 * @param $data
		 *
		 * @return mixed
		 */
		public function edit_template_data( $data ) {

			/**
			 * Replace
			 */
			if( isset( $data[ 'post_amp_content' ] ) ) {
				$pattern = '/zf-amp-(\w+)=/i';
				$replacement = '[$1]=';
				$data[ 'post_amp_content' ] = preg_replace( $pattern, $replacement, $data[ 'post_amp_content' ] );
			}
			
			/**
			 * Add required scripts
			 */
			$data[ 'amp_component_scripts' ] = array_merge( $data[ 'amp_component_scripts' ], $this->required_scripts );
			
			return $data;
		}

		/**
		 * Given js scripts will be loaded
		 *
		 * @param array $script_names Script names [keys]
		 *
		 * @return void
		 */
		public function set_scripts( $script_names ) {
			if( ! is_array( $script_names ) ) return;

			foreach( $script_names as $script_name ) {
				$this->required_scripts[$script_name] = $this->scripts[$script_name];
			}
		}
		
		/**
		 * Flush post cache
		 * @param $post_id
		 */
		public function flush_post_amp_cache( $post_id ) {
			if( ! function_exists( 'amp_get_permalink' ) ) {
				return;
			}

			zf_flush_post_cache_by_url( amp_get_permalink( $post_id ) );
		}
		
	}
	
}
Zombify_AMP::get_instance();