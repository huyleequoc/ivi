<?php
/**
 * BuddyPress - Groups
 *
 * @package BuddyPress
 * @subpackage bp-legacy
 */
?>

<div id="buddypress">

	<?php
	/**
	 * Fires at the top of the groups directory template file.
	 *
	 * @since 1.5.0
	 */
	do_action( 'bp_before_directory_groups_page' ); ?>

    <div class="bbp-container bbp-padder">

	<?php

	/**
	 * Fires before the display of the groups.
	 *
	 * @since 1.1.0
	 */
	do_action( 'bp_before_directory_groups' ); ?>

	<?php

	/**
	 * Fires before the display of the groups content.
	 *
	 * @since 1.1.0
	 */
	do_action( 'bp_before_directory_groups_content' ); ?>

    <div class="bbp-header clearfix">
        <h3><?php _e( 'Groups', 'buddypress' ); ?><?php if ( is_user_logged_in() && bp_user_can_create_groups() ) : ?> &nbsp;<a class="bbp-btn" href="<?php echo trailingslashit( bp_get_root_domain() . '/' . bp_get_groups_root_slug() . '/create' ); ?>"><?php _e( 'Create a Group', 'buddypress' ); ?></a><?php endif; ?></h3>
    </div>

	<form action="" method="post" id="groups-directory-form" class="dir-form">

		<?php

		/** This action is documented in bp-templates/bp-legacy/buddypress/activity/index.php */
		do_action( 'template_notices' ); ?>

		<div class="bbp-main-nav item-list-tabs" role="navigation">
			<ul>
				<li class="selected" id="groups-all"><a href="<?php bp_groups_directory_permalink(); ?>"><?php printf( __( 'All Groups %s', 'buddypress' ), '<span class="count">' . bp_get_total_group_count() . '</span>' ); ?></a></li>

				<?php if ( is_user_logged_in() && bp_get_total_group_count_for_user( bp_loggedin_user_id() ) ) : ?>
					<li id="groups-personal"><a href="<?php echo bp_loggedin_user_domain() . bp_get_groups_slug() . '/my-groups/'; ?>"><?php printf( __( 'My Groups %s', 'buddypress' ), '<span class="count">' . bp_get_total_group_count_for_user( bp_loggedin_user_id() ) . '</span>' ); ?></a></li>
				<?php endif; ?>

				<?php

				/**
				 * Fires inside the groups directory group filter input.
				 *
				 * @since 1.5.0
				 */
				do_action( 'bp_groups_directory_group_filter' ); ?>

			</ul>
		</div><!-- .item-list-tabs -->

		<div class="item-list-tabs" id="subnav" role="navigation">
			<ul>
				<?php

				/**
				 * Fires inside the groups directory group types.
				 *
				 * @since 1.2.0
				 */
				do_action( 'bp_groups_directory_group_types' ); ?>
			</ul>
		</div>

        <div class="bbp-filters">
            <div class="row">
                <div class="col-lg-4 col-md-4 col-sm-6">
                    <div class="bbp-filter">
                        <div id="groups-order-select" class="filter">
                            <label for="groups-order-by"><?php _e( 'Order By:', 'buddypress' ); ?></label>
                            <select id="groups-order-by">
                                <option value="active"><?php _e( 'Last Active', 'buddypress' ); ?></option>
                                <option value="popular"><?php _e( 'Most Members', 'buddypress' ); ?></option>
                                <option value="newest"><?php _e( 'Newly Created', 'buddypress' ); ?></option>
                                <option value="alphabetical"><?php _e( 'Alphabetical', 'buddypress' ); ?></option>
                                <?php
                                /**
                                 * Fires inside the groups directory group order options.
                                 *
                                 * @since 1.2.0
                                 */
                                do_action( 'bp_groups_directory_order_options' ); ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-lg-offset-4 col-md-5 col-md-offset-3 col-sm-6">
                    <div class="bbp-search">
                        <div id="group-dir-search" class="dir-search" role="search">
                            <?php bp_directory_groups_search_form(); ?>
                        </div><!-- #group-dir-search -->
                    </div>
                </div>
            </div>
        </div><!-- .bbp-filters -->

		<div id="groups-dir-list" class="groups dir-list">
			<?php bp_get_template_part( 'groups/groups-loop' ); ?>
		</div><!-- #groups-dir-list -->

		<?php

		/**
		 * Fires and displays the group content.
		 *
		 * @since 1.1.0
		 */
		do_action( 'bp_directory_groups_content' ); ?>

		<?php wp_nonce_field( 'directory_groups', '_wpnonce-groups-filter' ); ?>

		<?php

		/**
		 * Fires after the display of the groups content.
		 *
		 * @since 1.1.0
		 */
		do_action( 'bp_after_directory_groups_content' ); ?>

	</form><!-- #groups-directory-form -->

	<?php

	/**
	 * Fires after the display of the groups.
	 *
	 * @since 1.1.0
	 */
	do_action( 'bp_after_directory_groups' ); ?>

    </div><!-- .bbp-container -->

	<?php

	/**
	 * Fires at the bottom of the groups directory template file.
	 *
	 * @since 1.5.0
	 */
	do_action( 'bp_after_directory_groups_page' ); ?>

</div><!-- #buddypress -->
