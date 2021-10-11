<?php
// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

/**
 * @var $component_classes      string Component HTML classes
 * @var $component_id           string Component unique id
 * @var $shortcode_config       array<string,mixed> Shortcode attributes
 * @version 1.0
 */
$creds_placeholder = '%cred_f%';
$creds_title = __( 'Points', 'gamify' );
if( apply_filters( 'gfy/render_current_creds', true, 'buddypress' ) ) {
	$creds_placeholder .= ' / %user_current_balance%';
	$creds_title = __( 'Total / Current', 'gamify' );
}
$local_config = array(
	'wrap'      => 'tr',
	'template'  => "<td class='user-position'>%position%</td>
					<td class='user-avatar'>%user_avatar%</td>
					<td class='user-name'>%user_profile_link%</td>
					<td class='user-rank'>%user_rank_logo%</td>
					<td class='user-points'>" . $creds_placeholder . "</td>"
);
$shortcode_config = array_merge( $shortcode_config, $local_config ); ?>
<div class="<?php echo $component_classes; ?>">
	<div class="table-responsive">
		<table class="table table-condensed mycred-table gfy-table">
			<thead>
				<tr>
					<th class='user-position'>#</th>
					<th class='user-avatar'><?php _e( 'Avatar', 'gamify' ); ?></th>
					<th class='user-name'><?php _e( 'Username', 'gamify' ); ?></th>
					<th class='user-rank'><?php _e( 'Rank', 'gamify' ); ?></th>
					<th class='user-points'><?php echo $creds_title; ?></th>
				</tr>
			</thead>
            <tbody>
                <?php
                    $output = gfy_do_shortcode( 'mycred_leaderboard', $shortcode_config );
                    echo GFY_BP_Leaderboard_Component_Template::get_instance()->get_total() ?
                        $output : sprintf( '<tr><td colspan="5">%s</td></tr>', $output );
                ?>
            </tbody>
		</table>
	</div>
</div>