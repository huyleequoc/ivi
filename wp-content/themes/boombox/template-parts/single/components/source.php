<?php
/**
 * Template part to render post relations
 * @since 2.5.0
 * @version 2.5.0
 */

$post = get_post();

if( Boombox_Template::get_clean( 'protect_content' ) ) {
	return;
}

$source_url = boombox_get_post_meta( $post->ID, 'boombox_article_source_url' );
$source_label = boombox_get_post_meta( $post->ID, 'boombox_article_source_label' );
$source_follow = boombox_get_post_meta( $post->ID, 'boombox_article_source_follow' );
$source_target = boombox_get_post_meta( $post->ID, 'boombox_article_source_target' ) ? ' target="_blank" rel="noopener"' : '';
$source_title = apply_filters( 'boombox/single_post/source_title', __( 'Source:', 'boombox' ) );

$via_url = boombox_get_post_meta( $post->ID, 'boombox_article_via_url' );
$via_label = boombox_get_post_meta( $post->ID, 'boombox_article_via_label' );
$via_follow = boombox_get_post_meta( $post->ID, 'boombox_article_via_follow' );
$via_target = boombox_get_post_meta( $post->ID, 'boombox_article_via_target' ) ? ' target="_blank" rel="noopener"' : '';
$via_title = apply_filters( 'boombox/single_post/via_title', __( 'Via:', 'boombox' ) );

if( ( $source_url && $source_label ) || ( $via_url && $via_label ) ) { ?>
<div class="bb-source-via mb-xs mb-md bb-mb-el">
	<ul class="s-v-itm-list hvr-link-underline">
		<?php if( $source_url && $source_label ) { ?>
		<li class="s-v-itm">
			<?php if( $source_title ) { ?>
			<span class="s-v-title"><?php echo esc_html( $source_title ); ?></span>
			<?php } ?>
			<a href="<?php echo esc_url( $source_url ); ?>" class="s-v-link" rel="<?php echo esc_attr( $source_follow ); ?>"<?php echo $source_target; ?>><?php echo esc_html( $source_label ); ?></a>
		</li>
		<?php } ?>
		
		<?php if( $via_url && $via_label ) { ?>
		<li class="s-v-itm">
			<?php if( $via_title ) { ?>
			<span class="s-v-title"><?php esc_html_e( 'Via', 'boombox' ); ?>:</span>
			<?php } ?>
			<a href="<?php echo esc_url( $via_url ); ?>" class="s-v-link" rel="<?php echo esc_attr( $source_follow ); ?>"<?php echo $via_target; ?>><?php echo esc_html( $via_label ); ?></a>
		</li>
		<?php } ?>
	</ul>
</div>
<?php }