<?php
// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

/**
 * @var string              $title  Title
 * @var myCRED_Badge_Module $addon  Badges module instance
 * @version 1.0
 */
?>
<div class="gfy-bp-component bp-widget mycred-field">
	<table class="profile-fields bbp-table-responsive">
		<tr id="mycred-user-achievements">
			<td class="label"><?php _e( 'Achievements', 'gamify' ); ?></td>
			<td class="data"><?php $addon->insert_into_buddypress(); ?></td>
		</tr>
	</table>
</div>