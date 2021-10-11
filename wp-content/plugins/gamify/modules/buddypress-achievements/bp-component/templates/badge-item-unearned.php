<?php
// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

/**
 * @var $badge      myCRED_Badge    Badge Object
 * @var $level      int             Current level
 * @var $progress   array           Progress data
 * @version 1.0
 */
$badge->image_width  = MYCRED_BADGE_WIDTH;
$badge->image_height = MYCRED_BADGE_HEIGHT;
$badge_description = get_post_meta( $badge->post_id, 'gfy_badge_description', true );

$badge_image = $badge->get_image( 'main' );

if( $progress['type'] == 'percentage' ) {
	$tooltip_content = sprintf( '%d%%', floor( $progress['percentage'] ) );
} else {
	$tooltip_content = sprintf( '%d / %d',
		min( $progress['user_value'], $progress['next_level_value'] ), $progress['next_level_value']
	);
}

$badge_title_type = apply_filters( 'gfy/bp_achievements/badge_item/unearned_title_type', 'global-title' );
if( $badge_title_type === 'global-level' ) {
	if( $badge->level_label ) {
		$tmp_title = explode( ':', $badge->level_label );
		$title = isset( $tmp_title[0] ) && $tmp_title[0] ? trim( $tmp_title[0] ) : '';
		$subtitle = isset( $tmp_title[1] ) && $tmp_title[1] ? trim( $tmp_title[1] ) : '';
	} else {
		$title = $badge->title;
		$subtitle = sprintf( __( 'Level %d', 'gamify' ), ( $level + 1 ) );
	}
} else {
	$title = $badge->title;
	$subtitle = sprintf( __( 'Level %d', 'gamify' ), ( $level + 1 ) );
}
?>

<li class="col">
	<figure class="gfy-rank-item">
		<div class="unearned-badge-wrapper">
			<div class="gfy-unearned-badge unearned-badge">
				<?php if( $badge_image ) {
					echo $badge_image;
				} else { ?>
				<span class="gfy-icon-btn icon-lg gfy-icon-question badge-overlay" title="Be active to earn this badge!"></span>
				<?php } ?>
			</div>
		</div>
		<figcaption class="rank-item-content">

			<div class="progress-bar-wrapper">
			<?php
				gfy_get_template_part( 'core/templates/progress-bar.php', array(
					'progress'          => $progress['percentage'],
					'tooltip_content'   => $tooltip_content
				) );
			?>
			</div>

			<div class="title-block">
				<h4 class="rank-level"><?php echo $title; ?></h4>
				<?php
					if( $badge_description ) {
						gfy_get_template_part('core/templates/helper.php', array(
							'tooltip_content' => $badge_description
						));
					}
				?>
			</div>

			<?php if( $subtitle ) { ?>
			<div class="rank-desc"><?php echo $subtitle; ?></div>
			<?php } ?>

		</figcaption>
	</figure>
</li>