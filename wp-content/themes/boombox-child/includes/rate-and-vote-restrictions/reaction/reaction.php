<?php
// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

if( boombox_plugin_management_service()->is_plugin_active('boombox-theme-extensions/boombox-theme-extensions.php') && boombox_reactions_is_enabled()){
	add_filter( 'boombox_reaction_settings', 'boombox_init_reaction_settings' );
	add_filter( 'boombox_vote_restrictions', 'boombox_init_reaction_restrictions' );
}

/**
 * @param mixed $reaction_settings holds some settings like min_reaction_count_to_categorize, category_per_post_count, taxonomy, active_reactions
 * @return mixed
 */
function boombox_init_reaction_settings( $reaction_settings ) {
	$taxonomy = boombox_get_reaction_taxonomy_name();

	$award_minimal_score = absint( boombox_get_theme_option( 'extras_post_reaction_system_award_minimal_score' ) );
	if( 0 > $award_minimal_score ){
		$award_minimal_score = 1;
	}
	$reaction_settings['min_reaction_count_to_categorize'] = $award_minimal_score;

	$reaction_settings['category_per_post_count'] = 2;

	$reaction_settings['taxonomy'] = $taxonomy;

	$args = array(
		'taxonomy'      => $taxonomy,
		'hide_empty'    => false,
		'fields'     => 'ids',
		'meta_query' => array(
			'relation' => 'OR',
			array(
				'key' => 'reaction_disable_vote',
				'value' => 1,
				'compare' => '!='
			),
			array(
				'key' => 'reaction_disable_vote',
				'compare' => 'NOT EXISTS'
			)
		),
		'orderby' => 'meta_value'
	);

	$reactions = get_terms( $args );

	if ( is_wp_error( $reactions ) ) {
		$reactions = array();
	}

	$reaction_settings['active_reactions'] = $reactions;

	return $reaction_settings;
}

/**
 * @param array $vote_restrictions of Boombox_Vote_Restriction objects
 * @return array of Boombox_Vote_Restriction objects
 */
function boombox_init_reaction_restrictions( $vote_restrictions ) {
	// total
	$signed_in_restrict = boombox_get_theme_option( 'extras_post_reaction_system_login_require' );
	$maximal_count_per_vote = absint( boombox_get_theme_option( 'extras_post_reaction_system_maximal_count_per_vote' ) );
	if( 0 > $maximal_count_per_vote ){
		$maximal_count_per_vote = 1;
	}

	if( $signed_in_restrict ){
		$settings = new Boombox_Vote_Settings( Boombox_Vote_Settings::USER_TOTAL, $maximal_count_per_vote, $maximal_count_per_vote, $maximal_count_per_vote, $maximal_count_per_vote, $maximal_count_per_vote );
	}else{
		$settings = new Boombox_Vote_Settings( Boombox_Vote_Settings::IP_DAILY, $maximal_count_per_vote, $maximal_count_per_vote, $maximal_count_per_vote, $maximal_count_per_vote, $maximal_count_per_vote );
	}
	$db_settings = new Boombox_Vote_Db_Settings();
	$db_settings->set_table_name( Boombox_Reaction_Helper::get_table_name() );
	$vote_restrictions[] = new Boombox_Vote_Restriction( Boombox_Reaction_Helper::get_total_restriction_name(), $settings, $db_settings );

	// per post
	if( $signed_in_restrict ){
		$settings = new Boombox_Vote_Settings( Boombox_Vote_Settings::USER_TOTAL, 1, 1, 1, 1, 1 );
	}else{
		$settings = new Boombox_Vote_Settings( Boombox_Vote_Settings::IP_DAILY, 1, 1, 1, 1, 1 );
	}
	$db_settings = new Boombox_Vote_Db_Settings();
	$db_settings->set_table_name( Boombox_Reaction_Helper::get_table_name() );
	$db_settings->set_key_column_names( array( 'post_id', 'reaction_id' ) );
	$vote_restrictions[] = new Boombox_Vote_Restriction( Boombox_Reaction_Helper::get_restriction_name(), $settings, $db_settings );

	return $vote_restrictions;
}