<?php
// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

if ( ! class_exists( 'Boombox_Rate_Query' ) ) {
	/**
	 * Class Boombox_Rate_Query
	 */
	class Boombox_Rate_Query
	{
		/**
		 * @var WP_Query
		 */
		protected $wp_query;

		/**
		 * @param WP_Query $wp_query
		 */
		protected function set_wp_query(WP_Query $wp_query)
		{
			$this->wp_query = $wp_query;
		}

		/**
		 * @return WP_Query
		 */
		public function get_wp_query()
		{
			return $this->wp_query;
		}

		/**
		 * @param array $args for WP_QUERY, meta_key will be set automatically depending on $job and orderby will be meta_value_num
		 * @param Boombox_Rate_Job $job
		 * @param string $fake_meta_key
		 */
		function __construct(array $args, Boombox_Rate_Job $job, $fake_meta_key = null)
		{
			Boombox_Rate_Cron::register_job($job);

			if(!!$fake_meta_key){
				$args['meta_query'] = array(
					'relation' => 'OR',
					array(
						'key'     => Boombox_Rate_Cron::get_meta_key($job),
						'compare' => 'IN'
					),
					array(
						'key'     => $fake_meta_key,
						'value'   => 0,
						'compare' => '>'
					)
				);
			}else{
				$args['meta_key'] = Boombox_Rate_Cron::get_meta_key($job);
			}
			
			$args['orderby'] = array( 'meta_value_num' => 'DESC', 'date' => 'DESC' );

			$this->set_wp_query(new WP_Query($args));
		}
	}
}