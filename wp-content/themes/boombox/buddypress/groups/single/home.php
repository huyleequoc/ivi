<?php
/**
 * BuddyPress - Groups Home
 *
 * @package BuddyPress
 * @subpackage bp-legacy
 */

?>
<div id="buddypress">

	<?php if ( bp_has_groups() ) : while ( bp_groups() ) : bp_the_group(); ?>

	<?php

	/**
	 * Fires before the display of the group home content.
	 *
	 * @since 1.2.0
	 */
	do_action( 'bp_before_group_home_content' ); ?>

	<div id="item-header" role="complementary">

		<?php
		/**
		 * If the cover image feature is enabled, use a specific header
		 */
		if ( bp_group_use_cover_image_header() ) :
			bp_get_template_part( 'groups/single/cover-image-header' );
		else :
			bp_get_template_part( 'groups/single/group-header' );
		endif;
		?>

	</div><!-- #item-header -->

    <div class="bbp-container clearfix">
        <div class="bbp-item-sidebar">
            <div id="item-header-cover-image">
                <?php if ( ! bp_disable_group_avatar_uploads() ) : ?>
                    <div id="item-header-avatar">
                        <a href="<?php echo esc_url( bp_get_group_permalink() ); ?>" class="item-header-avatar-link" title="<?php echo esc_attr( bp_get_group_name() ); ?>">

                            <?php bp_group_avatar(); ?>

                        </a>
                    </div><!-- #item-header-avatar -->
                <?php endif; ?>

                <div id="item-header-content">
                    <?php

                    /**
                     * Fires before the display of the group's header meta.
                     *
                     * @since 1.2.0
                     */
                    do_action( 'bp_before_group_header_meta' ); ?>

                    <div id="item-meta" class="bbp-item-info">

                        <?php

                        /**
                         * Fires after the group header actions section.
                         *
                         * @since 1.2.0
                         */
                        do_action( 'bp_group_header_meta' ); ?>
                        <span class="group-activity"><?php printf( __( 'active %s', 'buddypress' ), bp_get_group_last_active() ); ?></span>
                        <span class="group-name"><?php bp_group_name(); ?></span>
                        <span class="group-highlight">
                            <?php

                            $bbp_group_type = bp_get_group_type( false );

                            switch ($bbp_group_type) {
                                case "Public Group":
                                    echo "<span class='icon-unlock-alt'></span>Public Group";
                                    break;

                                case "Private Group":
                                    echo "<span class='icon-lock'></span>Private Group";
                                    break;

                                case "Hidden Group":
                                    echo "<span class='icon-eye-slash'></span>Hidden Group";
                                    break;

                                default:
                                    //bp_group_type();
                            }
                            ?>
                        </span>
                    </div>

                    <div class="bbp-item-desc">
                        <?php bp_group_description(); ?>
                    </div>


                    <div id="item-buttons" class="bbp-item-buttons"><?php
                        /**
                         * Fires in the group header actions section.
                         *
                         * @since 1.2.6
                         */
                        do_action( 'bp_group_header_actions' ); ?>
                    </div><!-- #item-buttons -->

                </div><!-- #item-header-content -->

                <div id="item-actions">

                    <?php if ( bp_group_is_visible() ) : ?>

                        <h3><?php _e( 'Group Admins', 'buddypress' ); ?></h3>

                        <?php bp_group_list_admins();

                        /**
                         * Fires after the display of the group's administrators.
                         *
                         * @since 1.1.0
                         */
                        do_action( 'bp_after_group_menu_admins' );

                        if ( bp_group_has_moderators() ) :

                            /**
                             * Fires before the display of the group's moderators, if there are any.
                             *
                             * @since 1.1.0
                             */
                            do_action( 'bp_before_group_menu_mods' ); ?>

                            <h3><?php _e( 'Group Mods' , 'buddypress' ); ?></h3>

                            <?php bp_group_list_mods();

                            /**
                             * Fires after the display of the group's moderators, if there are any.
                             *
                             * @since 1.1.0
                             */
                            do_action( 'bp_after_group_menu_mods' );

                        endif;

                    endif; ?>

                </div><!-- #item-actions -->

            </div><!-- #item-header-cover-image -->
        </div><!-- .bbp-item-sidebar -->

        <div class="bbp-item-content">

            <div id="item-nav">
                <div class="bbp-main-nav item-list-tabs no-ajax" id="object-nav" role="navigation">
                    <ul>

                        <?php bp_get_options_nav(); ?>

                        <?php

                        /**
                         * Fires after the display of group options navigation.
                         *
                         * @since 1.2.0
                         */
                        do_action( 'bp_group_options_nav' ); ?>

                    </ul>
                </div>
            </div><!-- #item-nav -->

            <div id="item-body">
                <?php /** This action is documented in bp-templates/bp-legacy/buddypress/activity/index.php */
                do_action( 'template_notices' );
                ?>

                <?php

                /**
                 * Fires before the display of the group home body.
                 *
                 * @since 1.2.0
                 */
                do_action( 'bp_before_group_body' );

                /**
                 * Does this next bit look familiar? If not, go check out WordPress's
                 * /wp-includes/template-loader.php file.
                 *
                 * @todo A real template hierarchy? Gasp!
                 */

                    // Looking at home location
                    if ( bp_is_group_home() ) :

                        if ( bp_group_is_visible() ) {

                            // Load appropriate front template
                            bp_groups_front_template_part();

                        } else {

                            /**
                             * Fires before the display of the group status message.
                             *
                             * @since 1.1.0
                             */
                            do_action( 'bp_before_group_status_message' ); ?>

                            <div id="message" class="info">
                                <p><?php bp_group_status_message(); ?></p>
                            </div>

                            <?php

                            /**
                             * Fires after the display of the group status message.
                             *
                             * @since 1.1.0
                             */
                            do_action( 'bp_after_group_status_message' );

                        }

                    // Not looking at home
                    else :
                        // Group Admin
                        if     ( bp_is_group_admin_page() ) : bp_get_template_part( 'groups/single/admin'        );

                        // Group Activity
                        elseif ( bp_is_group_activity()   ) : bp_get_template_part( 'groups/single/activity'     );

                        // Group Members
                        elseif ( bp_is_group_members()    ) : Boombox_Buddypress::get_instance()->groups_members_template_part();

                        // Group Invitations
                        elseif ( bp_is_group_invites()    ) : bp_get_template_part( 'groups/single/send-invites' );

                        // Membership request
                        elseif ( bp_is_group_membership_request() ) : bp_get_template_part( 'groups/single/request-membership' );

                        // Anything else (plugins mostly)
                        else                                : bp_get_template_part( 'groups/single/plugins'      );

                        endif;

                    endif;

                /**
                 * Fires after the display of the group home body.
                 *
                 * @since 1.2.0
                 */
                do_action( 'bp_after_group_body' ); ?>

            </div><!-- #item-body -->
        </div>
    </div><!-- .bbp-container -->

    <?php

    /**
     * Fires after the display of the group home content.
     *
     * @since 1.2.0
     */
    do_action( 'bp_after_group_home_content' ); ?>

    <?php endwhile; endif; ?>
</div><!-- #buddypress -->
