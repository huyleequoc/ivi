<?php
/**
 * @var GFY_Shortcode_Leaderboard   $instance               Shortcode instance
 * @var array                       $shortcode_attributes   Predefined attributes
 * @var array                       $filter_choices         Possible filtering choices array
 * @var array                       $timeframe_choices      Possible time frame choices
 * @var string                      $current_url            Current URL
 * @version 1.0
 */
$creds_placeholder = '%cred_f%';
$creds_title = __( 'Points', 'gamify' );
if( apply_filters( 'gfy/render_current_creds', true, 'shortcode' ) ) {
	$creds_placeholder .= ' / %user_current_balance%';
	$creds_title = __( 'Total / Current', 'gamify' );
}
$local_attributes = array(
    'wrap'      => 'tr',
    'template'  => "<td class='user-position'>%position%</td>
					<td class='user-avatar'>%user_avatar%</td>
					<td class='user-name'><strong>%user_profile_link%</strong></td>
					<td class='user-rank'>%user_rank_logo%</td>
					<td class='user-points'><strong>" . $creds_placeholder . "</strong></td>"
);

$shortcode_attributes = array_merge( $shortcode_attributes, $local_attributes ); ?>
<div class="gfy-bp-component gfy-bp-leaderboard gfy-tabs tabs-md gfy-bp-leaderboard-shortcode">

	<?php if( count( $filter_choices ) > 1 ) { ?>
	<div class="filter-area">
		<form action="<?php echo esc_url( $current_url ); ?>" method="get">
			<select name="<?php echo $instance->get_filtering_query_var(); ?>" onchange="this.form.submit();">
				<?php foreach( $filter_choices as $val => $label ) { ?>
					<option value="<?php echo $val; ?>" <?php selected( $instance->get_current_filter( $shortcode_attributes[ 'type' ] ), $val ) ?>><?php echo $label; ?></option>
				<?php } ?>
			</select>

			<?php if( ! empty( $timeframe_choices ) ) {
				$active = wp_list_filter( $timeframe_choices, array( 'active' => true ) );
				$active = array_keys( $active );
				if( $active[ 0 ] ) { ?>
					<input type="hidden" name="timeframe" value="<?php echo esc_attr( $active[ 0 ] ); ?>"/>
				<?php }
			} ?>
		</form>
	</div>
	<?php } ?>

	<?php if( count( $timeframe_choices ) > 1 ) { ?>
		<ul class="tabs-menu">
			<?php foreach ( $timeframe_choices as $timeframe => $data ) { ?>
			<li class="tab-menu-item<?php echo $data['active'] ? ' active' : ''; ?>">
				<a href="#gfy-bp-leaderboard-shortcode-<?php echo esc_attr( $timeframe ); ?>"><?php echo esc_html( $data[ 'label' ] ); ?></a>
			</li>
			<?php } ?>
		</ul>

		<div class="tabs-content">
			<?php foreach ( $timeframe_choices as $timeframe => $data ) { ?>
			<div id="gfy-bp-leaderboard-shortcode-<?php echo esc_attr( $timeframe ); ?>" class="tab-content-item<?php echo $data['active'] ? ' active' : ''; ?>">
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
						global $mycred_leaderboard;
						$mycred_leaderboard = null;
						$shortcode_attributes[ 'timeframe' ] = $timeframe;
						$output = gfy_do_shortcode( 'mycred_leaderboard', $shortcode_attributes );
						echo $instance->get_total() ? $output : sprintf( '<tr><td colspan="5">%s</td></tr>', $output );
						?>
						</tbody>
					</table>
				</div>
			</div>
			<?php } ?>
		</div>
	<?php } else { ?>
		<div class="table-responsive">
			<table class="table table-condensed mycred-table gfy-table">
				<thead>
				<tr>
					<th class='user-position'>#</th>
					<th class='user-avatar'><?php _e( 'Avatar', 'gamify' ); ?></th>
					<th class='user-name'><?php _e( 'Username', 'gamify' ); ?></th>
					<th class='user-rank'><?php _e( 'Rank', 'gamify' ); ?></th>
					<th class='user-points'><?php _e( 'Points', 'gamify' ); ?></th>
				</tr>
				</thead>
				<tbody>
				<?php
				$output = gfy_do_shortcode( 'mycred_leaderboard', $shortcode_attributes );
				echo $instance->get_total() ? $output : sprintf( '<tr><td colspan="5">%s</td></tr>', $output );
				?>
				</tbody>
			</table>
		</div>
	<?php } ?>

    <?php if( $pagination = $instance->get_pagination() ) { ?>
        <div class="pagination-wrapper">
            <nav class="navigation pagination" role="navigation">
                <div class="nav-links"><?php echo $pagination; ?></div>
            </nav>
        </div>
    <?php } ?>
</div>
