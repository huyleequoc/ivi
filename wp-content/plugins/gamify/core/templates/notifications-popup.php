<?php
/**
 * Notification popup template
 * @var string $greetings Greetings title
 * @var string $description Description
 * @var string $trophy_title The trophy title
 * @var string $trophy_image The trophy image
 * @var string $trophy_image_url The trophy image src
 * @var string $share_message Message for share purposes
 * @since 2.1.3
 * @version 2.1.3
 */
?>
<div class="gfy-popup-overlay">
	<div class="gfy-popup-body">
		<a href="#" class="gfy-close js-gfy-close" rel="nofollow" onclick="closeGfyPopup();return false;"><i class="gfy-icon gfy-icon-close"></i></a>

		<?php if( $greetings || $description ) { ?>
		<div class="gfy-head">
			<?php if( $greetings ) { ?>
			<p class="gfy-sub-title"><?php echo esc_html( $greetings ); ?></p>
			<?php }

			if( $description ) { ?>
			<h2 class="gfy-title"><?php echo esc_html( $description ); ?></h2>
			<?php } ?>
		</div>
		<?php } ?>

		<div class="gfy-body">
			<?php if( $trophy_image ) { ?>
			<div class="gfy-badge-wrapper">
				<div class="gfy-earned-badge"><?php echo $trophy_image; ?></div>
			</div>
			<?php }

			if( $trophy_title ) { ?>
			<h3 class="gfy-badge-title"><?php echo esc_html( $trophy_title ); ?></h3>
			<?php } ?>
		</div>

		<div class="gfy-footer">

			<div class="gfy-share-button gfy-share-data"
			     data-title="<?php echo esc_html( $trophy_title ); ?>"
			     data-description="<?php echo esc_html( $share_message ); ?>"
			     data-image="<?php echo esc_url( $trophy_image_url ); ?>"
			     data-url="<?php echo get_author_posts_url( get_current_user_id() ); ?>">

				<?php if( gfy_get_fb_app_id() ) { ?>
				<a href="#" target="_blank" id="gfy-share-trophy-fb" class="gfy-share gfy-share-facebook"
				   rel="nofollow noopener"><?php _e( 'Share on Facebook', 'gamify' ); ?></a>
				<?php } ?>

				<a href="#" target="_blank" id="gfy-share-trophy-tw" class="gfy-share gfy-share-twitter"
				   rel="nofollow noopener"><?php _e( 'Share on Twitter', 'gamify' ); ?></a>
			</div>
		</div>

	</div>
</div>