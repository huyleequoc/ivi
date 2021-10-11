<?php
/**
 * BuddyPress - Members
 *
 * @package BuddyPress
 * @subpackage bp-legacy
 */
?>

<div id="buddypress">

    <?php
    /**
     * Fires at the top of the members directory template file.
     *
     * @since 1.5.0
     */
    do_action( 'bp_before_directory_members_page' ); ?>

    <div class="bbp-container bbp-padder">

	<?php

	/**
	 * Fires before the display of the members.
	 *
	 * @since 1.1.0
	 */
	do_action( 'bp_before_directory_members' ); ?>

    <div class="bbp-header">
        <h3><?php _e( 'Members', 'buddypress' ); ?></h3>
    </div>

	<?php

	/**
	 * Fires before the display of the members content.
	 *
	 * @since 1.1.0
	 */
	do_action( 'bp_before_directory_members_content' ); ?>

	<?php

	/**
	 * Fires before the display of the members list tabs.
	 *
	 * @since 1.8.0
	 */
	do_action( 'bp_before_directory_members_tabs' ); ?>

    <div class="bbp-main-nav item-list-tabs" role="navigation">
        <ul>
            <li class="selected" id="members-all"><a href="<?php bp_members_directory_permalink(); ?>"><?php printf( __( 'All Members %s', 'buddypress' ), '<span>' . bp_get_total_member_count() . '</span>' ); ?></a></li>

            <?php if ( is_user_logged_in() && bp_is_active( 'friends' ) && bp_get_total_friend_count( bp_loggedin_user_id() ) ) : ?>
                <li id="members-personal"><a href="<?php echo bp_loggedin_user_domain() . bp_get_friends_slug() . '/my-friends/'; ?>"><?php printf( __( 'My Friends %s', 'buddypress' ), '<span>' . bp_get_total_friend_count( bp_loggedin_user_id() ) . '</span>' ); ?></a></li>
            <?php endif; ?>

            <?php

            /**
             * Fires inside the members directory member types.
             *
             * @since 1.2.0
             */
            do_action( 'bp_members_directory_member_types' ); ?>

        </ul>
    </div><!-- .item-list-tabs -->

    <div class="item-list-tabs" id="subnav" role="navigation">
        <ul>
            <?php

            /**
             * Fires inside the members directory member sub-types.
             *
             * @since 1.5.0
             */
            do_action( 'bp_members_directory_member_sub_types' ); ?>
        </ul>
    </div>

    <div class="bbp-filters">
        <div class="row">
            <div class="col-lg-4 col-md-4 col-sm-6">
                <div class="bbp-filter">
                    <div id="members-order-select" class="last filter">
                        <label for="members-order-by"><?php _e( 'Order By:', 'buddypress' ); ?></label>
                        <select id="members-order-by">
                            <option value="active"><?php _e( 'Last Active', 'buddypress' ); ?></option>
                            <option value="newest"><?php _e( 'Newest Registered', 'buddypress' ); ?></option>

                            <?php if ( bp_is_active( 'xprofile' ) ) : ?>
                                <option value="alphabetical"><?php _e( 'Alphabetical', 'buddypress' ); ?></option>
                            <?php endif; ?>

                            <?php

                            /**
                             * Fires inside the members directory member order options.
                             *
                             * @since 1.2.0
                             */
                            do_action( 'bp_members_directory_order_options' ); ?>
                        </select>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-lg-offset-4 col-md-5 col-md-offset-3 col-sm-6">
                <div class="bbp-search">
                    <div id="members-dir-search" class="dir-search" role="search">
                        <?php bp_directory_members_search_form(); ?>
                    </div><!-- #members-dir-search -->
                </div>
            </div>
        </div>
    </div>

	<form action="" method="post" id="members-directory-form" class="dir-form">
		<div id="members-dir-list" class="members dir-list">
			<?php bp_get_template_part( 'members/members-loop' ); ?>
		</div><!-- #members-dir-list -->

		<?php

		/**
		 * Fires and displays the members content.
		 *
		 * @since 1.1.0
		 */
		do_action( 'bp_directory_members_content' ); ?>

		<?php wp_nonce_field( 'directory_members', '_wpnonce-member-filter' ); ?>

		<?php

		/**
		 * Fires after the display of the members content.
		 *
		 * @since 1.1.0
		 */
		do_action( 'bp_after_directory_members_content' ); ?>

	</form><!-- #members-directory-form -->

	<?php

	/**
	 * Fires after the display of the members.
	 *
	 * @since 1.1.0
	 */
	do_action( 'bp_after_directory_members' ); ?>

    </div><!-- .bbp-container -->

    <?php

    /**
     * Fires at the bottom of the members directory template file.
     *
     * @since 1.5.0
     */
    do_action( 'bp_after_directory_members_page' ); ?>

</div><!-- #buddypress -->