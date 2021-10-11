<?php
/**
 * The template part for displaying the reactions on single page
 *
 * @package BoomBox_Theme
 * @since   1.0.0
 * @version 2.5.0
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

if ( ! boombox_reactions_is_enabled() ) {
	return;
}

$post_id = get_the_ID();
$settings = boombox_get_post_reaction_settings( $post_id );
$total = $settings[ 'reaction_total' ];
$all_reactions = $settings[ 'all_reactions' ];
$restrictions = $settings[ 'reaction_restrictions' ];
$require_login = $settings[ 'reactions_login_require' ];
$item_class = $settings[ 'reaction_item_class' ];
$auth_url = $settings[ 'authentication_url' ];
$auth_class = $settings[ 'authentication_class' ];

$title = apply_filters( 'boombox/reaction-vote/title', esc_html__( "What's Your Reaction?", 'boombox' ) );

if ( ! empty( $all_reactions ) && is_array( $all_reactions ) ) { ?>
	
	<section class="bb-reaction-box mb-md bb-mb-el">
		
		<?php if( $title ) { ?>
		<h2 class="title"><?php echo esc_html( $title ); ?></h2>
		<?php } ?>

		<div class="reaction-sections" data-post_id="<?php echo $post_id; ?>">
			<?php foreach ( $all_reactions as $reaction ) { ?>
				
				<?php
				$disabled_class = '';
				if ( ( isset( $restrictions[ $reaction->term_id ] ) && ! $restrictions[ $reaction->term_id ][ 'can_react' ] ) ||
					( $require_login == true && ! is_user_logged_in() ) ) {
					$disabled_class = 'disabled';
				}
				$single_item_class = $restrictions[ $reaction->term_id ][ 'reacted' ] ? $item_class . ' voted' : $item_class;
				?>

				<div class="reaction-item <?php echo esc_attr( $single_item_class ); ?> "
				     data-reaction_id="<?php echo $reaction->term_id; ?>">
					<?php
					$reaction_icon_url = boombox_get_reaction_icon_url( $reaction->term_id );
					$image = ! empty( $reaction_icon_url ) ? ' <img src="' . esc_url( $reaction_icon_url ) . '" alt="' . $reaction->name . '">' : '';
					?>
					<span class="bb-badge badge <?php echo apply_filters( 'boombox_badge_wrapper_advanced_classes', $reaction->taxonomy, $reaction->taxonomy, $reaction->term_id ); ?>">
					    <span class="circle"><?php echo $image; ?></span>
					    <span class="text"><?php echo $reaction->name; ?></span>
					</span>

					<div class="reaction-bar">
						<?php
							$height = isset( $total[ $reaction->term_id ] ) ? $total[ $reaction->term_id ][ 'height' ] : 0;
							$total_count = isset( $total[ $reaction->term_id ] ) ? $total[ $reaction->term_id ][ 'total' ] : 0;
						?>
						<div class="reaction-stat" style="height:<?php echo absint( $height ); ?>%"></div>
						<div class="reaction-stat-count"><?php echo absint( $total_count ); ?></div>
					</div>
					<a href="<?php echo esc_url( $auth_url ); ?>" class="reaction-vote-btn <?php echo esc_attr( $disabled_class ); ?> <?php echo esc_attr( $auth_class ); ?>"><?php echo $reaction->name; ?></a>
				</div>
			<?php } ?>
		</div>
	</section>
<?php } ?>