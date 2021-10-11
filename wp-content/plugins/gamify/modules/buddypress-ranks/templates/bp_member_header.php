<?php
// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

/**
 * @var $rank           myCRED_Rank User rank object
 * @var $user_total_balance   int User current balance
 * @version 1.0
 */
$user_total_balance = mycred_get_users_balance( bp_displayed_user_id() );
$tooltip_content = get_post_meta( $rank->post_id, 'gfy_rank_description', true );
$difference = ( $user_total_balance - $rank->minimum );
$range = ( absint( $rank->maximum ) - absint( $rank->minimum ) );
$progress = $range ? min( ( $difference / $range ) * 100, 100 ) : 0;
?>

<div class="gfy-bp-my-rank">
	<figure class="gfy-rank-item">
		<?php
		if( $rank->has_logo ) {
			echo mycred_get_rank_logo( $rank->post_id, 'post-thumbnail', array( 'title' => $rank->title ) );
		} ?>
		<figcaption class="rank-item-content">
			<div class="progress-bar-wrapper">
				<?php gfy_get_template_part( 'core/templates/progress-bar.php', array(
					'progress'          => $progress,
					'tooltip_content'   => sprintf( '%d / %d', min( $user_total_balance, $rank->maximum ), $rank->maximum )
				) ); ?>
			</div>
			<div class="title-block"><h4 class="rank-level">
				<?php printf( __( 'Rank: %s', 'gamify' ), $rank->title ); ?></h4>
				<?php
				if( $tooltip_content ) {
					gfy_get_template_part( 'core/templates/helper.php', array(
						'tooltip_content' => $tooltip_content
					) );
				}
				?>
			</div>
		</figcaption>
	</figure>
</div>