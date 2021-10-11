<?php
// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

if ( ! class_exists( 'Boombox_Reaction_Helper' ) && trait_exists( 'Boombox_Vote_Restriction_Trait' ) ) {
	/**
	 * Class Boombox_Reaction_Helper
	 */
	class Boombox_Reaction_Helper
	{
		use Boombox_Vote_Restriction_Trait;

		/**
		 * @return string DB table name prefix included
		 */
		public static function get_reaction_total_table_name()
		{
			global $wpdb;

			return $wpdb->prefix . 'reaction_total';
		}

		/**
		 * @var string
		 */
		protected static $total_restriction_name = 'reaction_total_count';

		/**
		 * @return string
		 */
		public static function get_total_restriction_name()
		{
			return static::$total_restriction_name;
		}

		/**
		 * @return bool
		 */
		public static function is_total_restriction_enabled()
		{
			return Boombox_Vote_Restriction::restriction_exists(static::get_total_restriction_name());
		}

		/**
		 * Adds new record about the post being reacted as given reaction by  user, session, ip
		 * @param int $post_id
		 * @param int $reaction_id
		 * @return bool
		 */
		protected static function insert_reaction($post_id, $reaction_id)
		{
			global $wpdb;
			$data = array(
				'post_id' => $post_id,
				'reaction_id' => $reaction_id,
				'ip_address' => Boombox_Vote_Restriction::get_ip(),
				'session_id' => Boombox_Vote_Restriction::get_session_id()
			);
			$format = array('%d', '%d', '%s', '%s');
			$user_id = Boombox_Vote_Restriction::get_user_id();
			if ($user_id) {
				$data['user_id'] = $user_id;
				$format[] = '%d';
			}
			$result = $wpdb->insert(static::get_table_name(), $data, $format);
			return (!$result) ? false : true;
		}

		/**
		 * Increments the post's given reaction total count
		 * @param int $post_id
		 * @param int $reaction_id
		 * @return bool
		 */
		protected static function update_reaction_total($post_id, $reaction_id)
		{
			global $wpdb;
			$scale = 1;
			$result = $wpdb->query($wpdb->prepare("
	            INSERT INTO `" . static::get_reaction_total_table_name() . "` (`post_id`, `reaction_id`, `total`)
	            VALUES (%d, %d, %d)
	            ON DUPLICATE KEY UPDATE `total` = `total` + %d
	        ", $post_id, $reaction_id, $scale, $scale));
			
			if( !!$result ) {
				do_action( 'boombox/reaction_total_updated', 1, $post_id );
			}
			return (!$result) ? false : true;
		}

        /**
         * @param int $post_id
         * @return array of reactions and total counts for each reaction for the given post
         */
		public static function get_reaction_total($post_id)
		{
			global $wpdb;
			$temp_total = $wpdb->get_results( $wpdb->prepare("
	            SELECT `reaction_id`, `total`
	            FROM " . static::get_reaction_total_table_name() . "
	            WHERE `post_id` = %d
	            ORDER BY `total` DESC
	        ", $post_id ), ARRAY_A );
			if (!$temp_total) {
				$temp_total = array();
			}

			$fake_reactions_base = absint( apply_filters( 'boombox/prs/fake_reactions_base', 0 ) );
			if( $fake_reactions_base ) {
				$temp_total = static::fakefy( $post_id, $temp_total, $fake_reactions_base, 100, [ 0.6, 0.3, 0.1 ], 100 );
			}

			$reaction_total = array();
			$max_height_co = 0;
			for ($i = 0; $i < count($temp_total); ++$i) {
				$max_height_co += $temp_total[$i]['total'];
			}
			$max_height_co = (0 < $max_height_co) ? (100 / $max_height_co) : 0;
			for ($i = 0; $i < count($temp_total); ++$i) {
				$height = $temp_total[$i]['total'] * $max_height_co;
				$height = floor($height * 10) / 10;

				$reaction_total[ $temp_total[$i]['reaction_id'] ] = array(
					'total'  => $temp_total[$i]['total'],
					'height' => $height
				);
			}

			return $reaction_total;
		}

		/**
		 * Usage fakefy(100, [array('total' => 10), array('total' => 30), , array('total' => 10), , array('total' => 50)], 100, 0.5, [0.6, 0.3, 0.1], 100)
		 *
		 * @param int $post_id
		 * @param mixed[] $reaction_total
		 *          each element is an array that contains 'total' and 'reaction_id' keys
		 * @param int $instant_fake_points
		 *          will be given to the reactions randomly in the range 0.3 * $instant_fake_points to 3 * $instant_fake_points
		 * @param int $smart_fake_points
		 *          will be diversed between top reactions
		 * @param int[] $smart_fake_point_diverse_for_top_reactions
		 *          keep the sum to be 1
		 *          keep ordered - should be $smart_fakes_for_top_reactions[$i] >= $smart_fakes_for_top_reactions[$i + 1]
		 *          [0.6, 0.3, 0.1] mean that 3 top voted reactions will receive
		 *          0.6 * $instant_fake_points, 0.3 * $instant_fake_points and 0.1 * $instant_fake_points appropriately
		 * @param int $real_point_max_to_scale
		 *          if the real reaction point is below this number
		 *          then the given fake points will scaled down like $fake = $fake * ($real_point / $real_point_max_to_scale)
		 *
		 * @return array
		 */
		protected static function fakefy($post_id,
			$reaction_total,
			$instant_fake_points,
			$smart_fake_points,
			$smart_fake_point_diverse_for_top_reactions,
			$real_point_max_to_scale)
		{
			$post_id = intval( $post_id );

			$top_reactions = array();

			// process the smart fakes
			if (0 < count($reaction_total) && 0 === count($top_reactions)) {
				$top_reactions[] = & $reaction_total[0];
			}

			for ($i = 1; $i < count($reaction_total); ++$i) {
				for ($j = 0; $j < min(count($top_reactions), count($smart_fake_point_diverse_for_top_reactions)); ++$j) {
					if (0 == intval($reaction_total[$i]['total'])) {
						continue;
					}
					if (intval($top_reactions[$j]['total']) < intval($reaction_total[$i]['total'])) {
						array_splice($top_reactions, $j, 0, [$reaction_total[$i]]);
						break;
					} else {
						if (count($top_reactions) < count($smart_fake_point_diverse_for_top_reactions)) {
							$top_reactions[] = & $reaction_total[$i];
							break;
						}
					}
				}
			}

			for ($i = 0; $i < min(count($top_reactions), count($smart_fake_point_diverse_for_top_reactions)); ++$i) {
				if ($real_point_max_to_scale > intval($top_reactions[$i]['total'])) {
					$point_scale = intval($top_reactions[$i]['total']) / $real_point_max_to_scale;
				} else {
					$point_scale = 1;
				}
				$top_reactions[$i]['total'] += (int)floor($smart_fake_points * $point_scale * $smart_fake_point_diverse_for_top_reactions[$i]);
			}

			// process the instant fakes
			$reaction_weight = array();
			foreach (self::$reactions as $reaction_id) {
				$weight_co = (string)(intval( $reaction_id ) + $post_id);
				$weight = (int) $weight_co[ strlen( $weight_co ) - 1 ];
				$weight = floor( $instant_fake_points * (10 - $weight) / 3 );
				$reaction_weight[$reaction_id] = array('weight' => $weight);
			}

			foreach ($reaction_weight as $reaction_id => $options) {
				$reaction_found = false;
				foreach ($reaction_total as & $reaction) {
					if ($reaction['reaction_id'] == $reaction_id) {
						$reaction['total'] += (int) $options['weight'];
						$reaction_found = true;
						break;
					}
				}

				if ($reaction_found === false) {
					$reaction_total[] = array(
						'reaction_id' => $reaction_id,
						'total' => (int)$options['weight']
					);
				}
			}

			return $reaction_total;
		}

		/**
		 * @param int $post_id
		 *
		 * @return array of reaction_id-s count of static::$category_per_post_count
		 */
		public static function get_post_reactions($post_id){
			global $wpdb;

			$reaction_total = false;
			if( is_array( static::$reactions ) && 0 < count( static::$reactions ) ){
				$reaction_total = $wpdb->get_results( $wpdb->prepare("
		            SELECT `reaction_id`, `total`
		            FROM " . static::get_reaction_total_table_name() . "
		            WHERE `post_id` = %d AND `total` >= %d AND `reaction_id` IN( " . join(',', static::$reactions) . " )
		            ORDER BY `total` DESC
		            LIMIT 0, %d
	            ", $post_id, static::$min_reaction_count_to_categorize, static::$category_per_post_count ), ARRAY_A );
			}

			if (!$reaction_total) {
				$reaction_total = array();
			}

			$temp_reaction_total = array();
			if (2 <= count($reaction_total)) {
				$temp_reaction_total = array($reaction_total[0]['reaction_id']);
				$firstTotal = $reaction_total[0]['total'];
				$secondTotal = $reaction_total[1]['total'];
				if ($firstTotal * 0.3 < $secondTotal) {
					$temp_reaction_total[] = $reaction_total[1]['reaction_id'];
				}
			} elseif (1 == count($reaction_total)) {
				$temp_reaction_total = array($reaction_total[0]['reaction_id']);
			}
			return $temp_reaction_total;
		}

		/**
		 * Sets post categories depended on reactions
		 * @param int $post_id
		 * @return bool
		 */
		protected static function update_post_categories($post_id){
			$reaction_total = static::get_post_reactions($post_id);

			$reactions = wp_get_post_terms($post_id, static::$taxonomy, array("fields" => "ids"));
			if (is_wp_error($reactions)) {
				return false;
			}

			$skip = true;
			foreach ($reaction_total as $reaction) {
				$index = array_search($reaction, $reactions);
				if (0 === $index || $index) {
					unset($reactions[$index]);
				} else {
					$skip = false;
					break;
				}
			}

			if (!$skip || 0 < count($reactions)) {
				wp_set_post_terms($post_id, $reaction_total, static::$taxonomy);
			}

			return true;
		}

		/**
		 * Locks tables and calls insert_reaction, update_reaction_total, update_post_categories
		 * @param int $post_id
		 * @param int $reaction_id
		 * @return bool
		 */
		public static function add_reaction($post_id, $reaction_id)
		{
			global $wpdb;
			$reaction_id = intval($reaction_id);
			$status = true;
			$locked = false;
			while (true) {
				if (!in_array($reaction_id, static::$reactions)) {
					$status = 'Invalid reaction id.';
					break;
				}
				$wpdb->query('LOCK TABLES ' . static::get_table_name() . ' WRITE');
				$locked = true;
				if (static::is_total_restriction_enabled() && !Boombox_Vote_Restriction::check(static::get_total_restriction_name(), $post_id)) {
					$status = 'Total limit of reactions is exceeded.';
					break;
				}
				if (static::is_restriction_enabled() && !Boombox_Vote_Restriction::check(static::get_restriction_name(), array(
						'post_id' => $post_id,
						'reaction_id' => $reaction_id
					))
				) {
					$status = 'Reaction is already chosen.';
					break;
				}
				$inserted = static::insert_reaction($post_id, $reaction_id);
				if (!$inserted) {
					$status = 'Could not insert reaction.';
					break;
				}
				$wpdb->query('UNLOCK TABLES');
				$locked = false;
				static::update_reaction_total($post_id, $reaction_id);
				static::update_post_categories($post_id);
				break;
			}
			if ($locked) {
				$wpdb->query('UNLOCK TABLES');
			}
			// echo $status;

			return (true === $status) ? true : false;
		}

		/**
		 * @param int $post_id
		 * @return array reactions and vote states for current request
		 */
		public static function get_post_reaction_restrictions($post_id)
		{
			$user_reactions = array();
			$is_restriction_enabled = static::is_restriction_enabled();
			$total_exceeded = false;
			$already_reacted = false;
			if (static::is_total_restriction_enabled()) {
				$total_exceeded = !Boombox_Vote_Restriction::check(static::get_total_restriction_name(), $post_id);
			}
			foreach (static::$reactions as $reaction) {
				if ($is_restriction_enabled) {
					$already_reacted = !Boombox_Vote_Restriction::check(static::get_restriction_name(), array(
						'post_id' => $post_id,
						'reaction_id' => $reaction
					));
				}
				$user_reactions[$reaction] = array(
					'can_react' => !$total_exceeded && !$already_reacted,
					'reacted' => $already_reacted
				);
			}

			return $user_reactions;
		}

		/**
		 *  Holds term ids as array of integers
		 * @var array
		 */
		protected static $reactions = array();

		/**
		 * @param int $reaction_id
		 * @return bool
		 */
		protected static function _add_reaction($reaction_id)
		{
			$reaction_id = intval($reaction_id);
			if (term_exists($reaction_id, static::$taxonomy) && !in_array($reaction_id, static::$reactions)) {
				static::$reactions[] = $reaction_id;
				return true;
			}
			return false;
		}

		/**
		 * Holds the minimum reaction count, that posts must have to be categorized as
		 * @var int
		 */
		protected static $min_reaction_count_to_categorize = 1;

		/**
		 * @param int $min_reaction_count_to_categorize holds the minimum reaction count, that posts must have to be categorized as
		 * @return bool
		 */
		protected static function set_min_reaction_count_to_categorize($min_reaction_count_to_categorize)
		{
			if (!Boombox_Exception_Helper::is_positive_or_zero($min_reaction_count_to_categorize)) {
				return false;
			}
			static::$min_reaction_count_to_categorize = $min_reaction_count_to_categorize;

			return true;
		}

		/**
		 * Maximum number of categories(reactions) post can be
		 * @var int
		 */
		protected static $category_per_post_count = 2;

		/**
		 * @param int $category_per_post_count maximum number of categories(reactions) post can be
		 * @return bool
		 */
		protected static function set_category_per_post_count($category_per_post_count)
		{
			if (!Boombox_Exception_Helper::is_positive_or_zero($category_per_post_count)) {
				return false;
			}
			static::$category_per_post_count = $category_per_post_count;

			return true;
		}

		/**
		 * Holds the reactions' taxonomy name
		 * @var string
		 */
		protected static $taxonomy = 'category';

		/**
		 * @param string $taxonomy
		 * @return bool
		 */
		protected static function set_taxonomy($taxonomy)
		{
			if (!taxonomy_exists($taxonomy)) {
				return false;
			}
			static::$taxonomy = $taxonomy;

			return true;
		}

		/**
		 * Holds static initiation status
		 * @var bool
		 */
		protected static $static_initiated = false;

		/**
		 * Initiates class statics
		 * @throws UnexpectedValueException
		 */
		public static function init_static_once()
		{
			if (false === static::$static_initiated) {
				static::$static_initiated = true;
				$reaction_settings = apply_filters('boombox_reaction_settings', array(
					'min_reaction_count_to_categorize' => static::$min_reaction_count_to_categorize,
					'category_per_post_count' => static::$category_per_post_count,
					'taxonomy' => static::$taxonomy,
					'active_reactions' => array()
				));

				if (!static::set_min_reaction_count_to_categorize($reaction_settings['min_reaction_count_to_categorize'])) {
					throw new UnexpectedValueException('$reaction_settings[ "min_reaction_count_to_categorize" ] must be a positive number or zero. Value: ' . $reaction_settings['min_reaction_count_to_categorize']);
				}

				if (!static::set_category_per_post_count($reaction_settings['category_per_post_count'])) {
					throw new UnexpectedValueException('$reaction_settings[ "category_per_post_count" ] must be a positive number or zero. Value: ' . $reaction_settings['category_per_post_count']);
				}

				if (!static::set_taxonomy($reaction_settings['taxonomy'])) {
					throw new UnexpectedValueException('$reaction_settings[ "taxonomy" ] must contain valid taxonomy name. Value: ' . $reaction_settings['taxonomy']);
				}

				$reactions = $reaction_settings['active_reactions'];
				if (!is_array($reactions)) {
					static::_add_reaction($reactions);
				} else {
					foreach ($reactions as $reaction) {
						static::_add_reaction($reaction);
					}
				}
			}
		}

		/**
		 * Initiates class statics
		 *
		 * @throws UnexpectedValueException
		 */
		public static function init()
		{
			static::$restriction_name = 'reaction_count';
			static::$table_name = 'reactions';
		}
	}
}
Boombox_Reaction_Helper::init();
add_action('init', array('Boombox_Reaction_Helper', 'init_static_once'));