<?php
/**
 * BuddyPress - Members Home
 *
 * @package BuddyPress
 * @subpackage bp-legacy
 */

$member_cover_image_url = '';
if( bp_displayed_user_use_cover_image_header() ) {
	$member_cover_image_url = bp_attachments_get_attachment('url', array(
		'object_dir' => 'members',
		'item_id' => bp_displayed_user_id(),
	));
} ?>

<div id="buddypress" class="<?php echo $member_cover_image_url ? '' : 'bbp-no-cover-img'; ?>">

	<?php

	/**
	 * Fires before the display of member home content.
	 *
	 * @since 1.2.0
	 */
	do_action( 'bp_before_member_home_content' ); ?>

	<div id="item-header" role="complementary">

		<?php
		/**
		 * If the cover image feature is enabled, use a specific header
		 */
		if ( bp_displayed_user_use_cover_image_header() ) :
			bp_get_template_part( 'members/single/cover-image-header' );
		endif;
		?>

	</div><!-- #item-header -->

    <div class="bbp-container clearfix">
        <div class="bbp-item-sidebar">
            <div id="item-header-cover-image">
                <div id="item-header-avatar">
                    <a href="<?php bp_displayed_user_link(); ?>" class="item-header-avatar-link">

                        <?php bp_displayed_user_avatar( 'type=full' ); ?>

                    </a>
                </div><!-- #item-header-avatar -->

                <div id="item-header-content">
                    <div class="bbp-item-info">
                        <span class="member-activity"><?php bp_last_activity( bp_displayed_user_id() ); ?></span>
                        <span class="member-name"><?php bp_displayed_user_fullname(); ?></span>
                        <?php if ( bp_is_active( 'activity' ) && bp_activity_do_mentions() ) : ?>
                            <h2 class="user-nicename">@<?php bp_displayed_user_mentionname(); ?></h2>
                        <?php endif; ?>
                    </div>

                    <?php

                    /**
                     * Fires before the display of the member's header meta.
                     *
                     * @since 1.2.0
                     */
                    do_action( 'bp_before_member_header_meta' ); ?>

                    <div id="item-meta">

                        <?php if ( bp_is_active( 'activity' ) ) : ?>

                            <?php if( $bp_activity_latest_update = bp_get_activity_latest_update( bp_displayed_user_id() ) ) { ?>

                                <div id="latest-update">

                                    <?php echo $bp_activity_latest_update; ?>

                                </div>

                            <?php } ?>

                        <?php endif; ?>

                        <?php

                        /**
                         * Fires after the group header actions section.
                         *
                         * If you'd like to show specific profile fields here use:
                         * bp_member_profile_data( 'field=About Me' ); -- Pass the name of the field
                         *
                         * @since 1.2.0
                         */
                        do_action( 'bp_profile_header_meta' );

                        ?>

                    </div><!-- #item-meta -->

                    <div id="item-buttons" class="bbp-item-buttons"><?php

                        /**
                         * Fires in the member header actions section.
                         *
                         * @since 1.2.6
                         */
                        do_action( 'bp_member_header_actions' ); ?>
                    </div><!-- #item-buttons -->

                </div><!-- #item-header-content -->

            </div><!-- #item-header-cover-image -->
        </div><!-- .bbp-item-sidebar -->

        <div class="bbp-item-content">
            <div id="item-nav">
                <div class="bbp-main-nav item-list-tabs no-ajax" id="object-nav" role="navigation">
                    <ul>

                        <?php bp_get_displayed_user_nav(); ?>

                        <?php

                        /**
                         * Fires after the display of member options navigation.
                         *
                         * @since 1.2.4
                         */
                        do_action( 'bp_member_options_nav' ); ?>

                    </ul>
                </div>
            </div><!-- #item-nav -->

            <div id="item-body">
                <?php

                /** This action is documented in bp-templates/bp-legacy/buddypress/activity/index.php */
                do_action( 'template_notices' ); ?>

                <?php

                /**
                 * Fires before the display of member body content.
                 *
                 * @since 1.2.0
                 */
                do_action( 'bp_before_member_body' );

                if ( bp_is_user_front() ) :
                    bp_displayed_user_front_template_part();

                elseif ( bp_is_user_activity() ) :
                    bp_get_template_part( 'members/single/activity' );

                elseif ( bp_is_user_blogs() ) :
                    bp_get_template_part( 'members/single/blogs'    );

                elseif ( bp_is_user_friends() ) :
                    bp_get_template_part( 'members/single/friends'  );

                elseif ( bp_is_user_groups() ) :
                    bp_get_template_part( 'members/single/groups'   );

                elseif ( bp_is_user_messages() ) :
                    bp_get_template_part( 'members/single/messages' );

                elseif ( bp_is_user_profile() ) :
                    bp_get_template_part( 'members/single/profile'  );

                elseif ( bp_is_user_notifications() ) :
                    bp_get_template_part( 'members/single/notifications' );

                elseif ( bp_is_user_settings() ) :
                    bp_get_template_part( 'members/single/settings' );

                // If nothing sticks, load a generic template
                else :
                    bp_get_template_part( 'members/single/plugins'  );

                endif;

                /**
                 * Fires after the display of member body content.
                 *
                 * @since 1.2.0
                 */
                do_action( 'bp_after_member_body' ); ?>

            </div><!-- #item-body -->

        </div><!-- .bbp-item-content -->
    </div><!-- .bbp-container -->

    <?php

    /**
     * Fires after the display of member home content.
     *
     * @since 1.2.0
     */
    do_action( 'bp_after_member_home_content' ); ?>

</div><!-- #buddypress -->
