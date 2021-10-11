<?php
/**
 * BuddyPress Zombify Submissions Component actions.
 *
 *
 * @package Zombify
 * @subpackage Buddypress Submissions
 */

/**
 * Add box for post type filtering
 */
add_action( 'bp_member_plugin_options_nav', 'zf_submissions_add_filtering_dropdown', 10 );
function zf_submissions_add_filtering_dropdown() {
	
	if( ! bp_is_current_component( zf_submissions_get_slug() ) ) {
	    return;
    }
    
    $options_html = '';
    $zombify_active_formats = array_unique( array_merge(
        array_values( zombify()->get_active_formats( true, 1 ) ),
        array_values( zombify()->get_active_formats( true, 2 ) )
    ) );
    if( empty( $zombify_active_formats ) ) {
        return;
    }

    $filter = isset( $_REQUEST['filter'] ) ? $_REQUEST['filter'] : '';

    $options = array(
		'' => __('All types', 'zombify')
	);
    if( current_user_can( 'editor' ) ) {
	    $options[ 'simple-post' ] = __('Posts', 'zombify');
	}

	$post_types = zombify()->get_active_post_types();
	foreach( $post_types as $post_type => $post_type_data ) {
		$value = $post_type_data['post_type_level'] == 1 ? $post_type_data['post_type_slug'] : 'subtype_' . $post_type_data['post_type_slug'];
		$options[ $value ] = $post_type_data['name'];
	}
	$options = apply_filters( 'zombify_bp_submissions_posts_filter_default_options', $options, $filter );

	if( empty( $options ) ) {
		return;
	} ?>
	<li id="submissions-filter-select" class="last">
		<label for="submissions-filter-by"><?php esc_html_e( 'Show', 'zombify' ) ?>:</label>
		<form action="" method="get">
			<select id="submissions-filter-by" name="filter" onchange="this.form.submit();">
				<?php foreach( $options as $value => $label ) { ?>
				<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $filter, $value ) ?>><?php echo esc_html( $label ); ?></option>
				<?php } ?>
			</select>
		</form>
	</li>
	<?php
}

/**
 * Add notification to email sections
 */
add_action( 'bp_notification_settings', 'zf_submissions_notification_settings', 10 );
function zf_submissions_notification_settings() {
	
	if ( ! $post_published = bp_get_user_meta( bp_displayed_user_id(), 'notification_zf_submission_post_published', true ) ) {
		$post_published = 'yes';
	}
 
	?>
	<table class="notification-settings" id="zf-sumbissions-notification-settings">
		<thead>
		<tr>
			<th class="icon">&nbsp;</th>
			<th class="title"><?php _e( 'Submissions', 'zombify' ) ?></th>
			<th class="yes"><?php _e( 'Yes', 'buddypress' ) ?></th>
			<th class="no"><?php _e( 'No', 'buddypress' )?></th>
		</tr>
		</thead>
		
		<tbody>
		
			<tr id="zf-sumbissions-notification-settings-post-published">
				<td>&nbsp;</td>
				<td><?php _e( 'Administrator publish your post', 'zombify' ) ?></td>
				<td class="yes">
					<input type="radio" name="notifications[notification_zf_submission_post_published]" id="notification-zf-submission-new-post-published-yes" value="yes" <?php checked( $post_published, 'yes', true ) ?>/>
					<label for="notification-zf-submission-new-post-published-yes" class="bp-screen-reader-text"><?php
						/* translators: accessibility text */
						_e( 'Yes, send email', 'buddypress' );
						?>
					</label>
				</td>
				<td class="no"><input type="radio" name="notifications[notification_zf_submission_post_published]" id="notification-zf-submission-new-post-published-no" value="no" <?php checked( $post_published, 'no', true ) ?>/>
					<label for="notification-zf-submission-new-post-published-no" class="bp-screen-reader-text">
						<?php
						/* translators: accessibility text */
						_e( 'No, do not send email', 'buddypress' );
						?>
					</label>
				</td>
			</tr>
			
			<?php
			
			/**
			 * Fires inside the closing </tbody> tag for zf-submissions screen notification settings.
			 *
			 * @since 1.2.0
			 */
			do_action( 'bp_zf_submission_screen_notification_settings' ) ?>
		</tbody>
	</table>
	<?php
}