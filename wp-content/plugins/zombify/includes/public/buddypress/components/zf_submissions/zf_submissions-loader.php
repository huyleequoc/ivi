<?php
/**
 * BuddyPress Zombify Submissions Loader.
 *
 *
 * @package Zombify
 * @subpackage Buddypress Submissions
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Include submissions component if it is not included
 */
function bp_zf_include_submissions_component() {
	if( ! class_exists( 'BP_Zombify_Submissions_Component' ) ) {
		require_once 'classes/class-zf_submissions-component.php';
	}
}

/**
 * Add component to buddypress components list
 */
add_filter( 'bp_core_get_components', 'bp_zf_get_components', 10, 2 );
function bp_zf_get_components( $components, $type ){
	
	if( ! in_array( $type, array( 'required', 'retired' ) ) ) {
		bp_zf_include_submissions_component();
		
		$components = array_merge( $components, array(
			BP_Zombify_Submissions_Component::get_id() => BP_Zombify_Submissions_Component::get_component_info()
		) );
	}
	
	return $components;
}

/**
 * Setup buddypress email templates
 */
add_action( 'zombify_activation', 'bp_zf_submissions_setup_emails', 20 );
add_action( 'bp_core_install_emails', 'bp_zf_submissions_setup_emails' );
function bp_zf_submissions_setup_emails() {
	
	if( ! is_plugin_active( 'buddypress/bp-loader.php' ) ) {
		return;
	}
	
	$defaults = array(
		'post_status' => 'publish',
		'post_type'   => bp_get_email_post_type(),
	);
	
	$zf_submission_post_published_email_template_default = array(
		/* translators: do not remove {} brackets or translate its contents. */
		'post_title'   => __( '[{{{site.name}}}] {{moderator.name}} published your post', 'zombify' ),
		/* translators: do not remove {} brackets or translate its contents. */
		'post_content' => __( "{{moderator.name}} published your post:\n\n<blockquote>&quot;{{post.title}}&quot;</blockquote>\n\n<a href=\"{{{post.url}}}\">View Post</a>.", 'zombify' ),
		/* translators: do not remove {} brackets or translate its contents. */
		'post_excerpt' => __( "{{moderator.name}} published your post:\n\n\"{{post.title}}\"\n\nView Post: {{{post.url}}}", 'zombify' ),
	);
	$zf_submission_post_published_email_template = apply_filters( 'bp_zf_submission_post_published_email_template', $zf_submission_post_published_email_template_default );
	
	$emails = array(
		'zf_submission_post_published' => wp_parse_args( $zf_submission_post_published_email_template, $zf_submission_post_published_email_template_default ),
	);
	
	$descriptions = wp_list_pluck( bp_email_get_unsubscribe_type_schema(), 'description' );
	
	// Add these emails to the database.
	foreach ( $emails as $id => $email ) {
		
		if( ! is_wp_error( bp_get_email( $id ) ) ) {
			continue;
		}
		
		$post_id = wp_insert_post( bp_parse_args( $email, $defaults, 'install_email_' . $id ) );
		if ( ! $post_id ) {
			continue;
		}
		
		$tt_ids = wp_set_object_terms( $post_id, $id, bp_get_email_tax_type() );
		foreach ( $tt_ids as $tt_id ) {
			$term = get_term_by( 'term_taxonomy_id', (int) $tt_id, bp_get_email_tax_type() );
			wp_update_term( (int) $term->term_id, bp_get_email_tax_type(), array(
				'description' => $descriptions[ $id ],
			) );
		}
	}
}

/**
 * Make component active on plugin activation
 */
add_action( 'zombify_activation', 'bp_zf_submissions_activate_component', 30 );
function bp_zf_submissions_activate_component() {
	if( ! is_plugin_active( 'buddypress/bp-loader.php' ) ) {
		return;
	}
	
	bp_zf_include_submissions_component();
	$zf_submissions_component_id = BP_Zombify_Submissions_Component::get_id();
	$active_components = bp_get_option( 'bp-active-components' );

	if( ! array_key_exists( $zf_submissions_component_id, $active_components ) ) {
		$active_components[ $zf_submissions_component_id ] = 1;
		
		bp_update_option( 'bp-active-components', $active_components );
	}
}

/**
 * Activate ZF_Submissions component on new install
 */
add_filter( 'bp_new_install_default_components', 'bp_zf_new_install_default_components', 10, 1 );
function bp_zf_new_install_default_components( $components ) {
	bp_zf_include_submissions_component();
	
	$components[ BP_Zombify_Submissions_Component::get_id() ] = 1;
	
	return $components;
}

/**
 * Add email templates to buddypres email templates list
 */
add_filter( 'bp_email_get_unsubscribe_type_schema', 'bp_zf_submissions_email_get_unsubscribe_type_schema', 10, 1 );
function bp_zf_submissions_email_get_unsubscribe_type_schema( $emails ) {
	
	$emails['zf_submission_post_published'] = array(
		'description'	=> __( 'Administrator has published recipient\'s post.', 'zombify' ),
		'unsubscribe'	=> array(
			'meta_key'	=> 'notification_zf_submission_post_published',
			'message'	=> __( 'You will no longer receive emails when your post has published by administrator.', 'buddypress' ),
		),
	);
	return $emails;
	
}

/**
 * Add component styles
 */
add_action( 'bp_admin_enqueue_scripts', 'bp_zf_submissions_add_styles', 11 );
function bp_zf_submissions_add_styles() {
	wp_add_inline_style( 'bp-admin-common-css', '
		.settings_page_bp-components tr.zf_submissions td.plugin-title span:before {
			content:\'\';
            background-image: url( data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiPz4NCjwhRE9DVFlQRSBzdmcgUFVCTElDICItLy9XM0MvL0RURCBTVkcgMS4xLy9FTiIgImh0dHA6Ly93d3cudzMub3JnL0dyYXBoaWNzL1NWRy8xLjEvRFREL3N2ZzExLmR0ZCI+DQo8IS0tIENyZWF0b3I6IENvcmVsRFJBVyBYNiAtLT4NCjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWw6c3BhY2U9InByZXNlcnZlIiB3aWR0aD0iMTYwbW0iIGhlaWdodD0iMTYwbW0iIHZlcnNpb249IjEuMSIgc2hhcGUtcmVuZGVyaW5nPSJnZW9tZXRyaWNQcmVjaXNpb24iIHRleHQtcmVuZGVyaW5nPSJnZW9tZXRyaWNQcmVjaXNpb24iIGltYWdlLXJlbmRlcmluZz0ib3B0aW1pemVRdWFsaXR5IiBmaWxsLXJ1bGU9ImV2ZW5vZGQiIGNsaXAtcnVsZT0iZXZlbm9kZCINCnZpZXdCb3g9IjAgMCAxNjAwMCAxNjAwMCINCiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayI+DQogPGcgaWQ9IkxheWVyX3gwMDIwXzEiPg0KICA8bWV0YWRhdGEgaWQ9IkNvcmVsQ29ycElEXzBDb3JlbC1MYXllciIvPg0KICA8cGF0aCBmaWxsPSIjMjAxRTFFIiBkPSJNNzI2MiA0NzI0YzEyNiw5OSAyNzksMTcxIDQwNSwxMzMgMjM3LC03MCAzMzMsLTE1MCAzMzUsLTQxOGwtMzYxIC0xMTcyIC03NyAtMjIzIDU3OCAtODNjOTcsMzI1IDE1Nyw1NzYgMzIwLDg0NCAxMDgsMTI2IDI3MywxNzIgNDIzLDE1NSAyMTcsLTI0IDI2MiwtMTE4IDMxMCwtMjkyIC0yNiwtMjQ4IC05NiwtNDk1IC0yMzUsLTc0MyA2NDYsNDMgMTk2NywzOTEgMjU1MSw2NjEgMzA3LDEwNCA1NjQsLTIzMyAxNTIsLTQ5NiAtNDUwLC0yNDAgLTEwMzIsLTQ0MSAtMTUyMywtNTkzIDg5NywtNzExIDIxNzksLTE1NzEgMzEzNCwtMjE3MWwyMiAyOTZjMTM2LDEwNTUgMzYxLDIyOTEgNzgyLDMyNjIgODgzLDIwMzcgMTY3MiwzMzA5IDE2ODgsNDgxOSAyNywyNDk5IC0xMjQ4LDU1NzggLTUzMzYsNjU5OGwtMTcyIC00NzYgMzk5IC0xNDkgOTcgLTE1NyAtMTc0IC0xMzYgLTQzMCAxMDUgLTExMSAtNDkxYzE1LDU3IDQ0MiwtNzEgNTYxLC05MGwxMjUgLTIxMSAtMTk2IC0xNzcgLTU2MSAxMTAgLTIzIC0yMzMgNTExIC0xNDcgODYgLTI0MyAtMjA5IC0yNDYgLTU1NSAxNjIgLTEwNCAtNDkzIC00NDAgLTg0IC05NiAyMDkgMTc0IDQ1NiAtNzg2IDIzMyAtMTA0IDE1NCAxNzIgMTIyIDc1OSAtODQgODIgMjE2Yy0yMzcsMTIxIC00NjksMTQ4IC03MzksMTcxbC0xMDQgMzAyIDE3MyAxODEgNzY2IC0xNDQgMTQ2IDQxNCAtOTI4IDE2MiAtNzggMjM1IDE1MyAxMzAgODc1IC0yMzEgODcgNTY1Yy0xMTUyLDIzMiAtMjQ1NywxMDIgLTMzODQsLTg0IC04MzgsLTE2OCAtMTY1OSwtMzkwIC0zMDc2LC0xNDA4IC0xOTM5LC0xMzk0IC0yNjY3LC0zNzI5IC0yMzAwLC02MDQzIDkwLC01NjkgMTgxLC0xMTQ5IC0xMDgsLTIzNjQgLTE2NywtOTM2IC0zNTAsLTE4OTQgLTY1MiwtMjc5NWw1MDUgMTM5YzExNDUsNDEyIDIyOTcsNTIzIDM0MzUsOTIybC0xMTI1IDEwMzdjLTMwMSw1MDkgMTc2LDc2OSA0MTQsNDc4IDQyNCwtNTY4IDgzOCwtOTAyIDE0MzcsLTEyNTJsMjgzIC0yMDMgMjU3IDUxMWMzMTQsMzEgNzQzLDc2IDc3MywtMzE1bC0yNDEgLTU4NmMxOTUsLTEyNSA2MjEsLTIxOCA3OTksLTI3MSAxOTUsNDcwIDM0MiwxMDExIDQ1NiwxNTE5em00MDE3IDM3OTBjLTI1MywtNDIgLTMxNCwtNzU2IC0zNzksLTEwMjIgLTE2NywtNzgxIC02MSwtOTc1IDkwLC0xMDE3IDIxMCwtNTggNTMzLDMwNCA1NDksODkzIDkxLDQ1OSAyNzIsMTA2MyAtMjYxLDExNDV6bTI3NDUgLTE0OTJjMTA5LDQzMiAtMTUzLDE1NzYgLTQyNSwxOTQzIC00MTgsNTY0IC05NTgsNzg3IC0xNTYxLDEwNzlsNTkzIDE2N2MyODQsMTA2IDM5OSwyMDYgMzIxLDMzOSAtMTQxLDI0MiAtNTE2LDI4MiAtMTM5MSw2IC04MzEsLTI2MyAtMTE2MiwtMjQwIC0xNjc0LC00NzkgLTc3NCwtMzYxIC0xMzk5LC0xMDE0IC0xNjMwLC0xODMwIC0yMzksLTg0NiAyMSwtMTc1MiA2MTQsLTI0MTEgMTY1NiwtMTgzOSA0NjA3LC05NzcgNTE1MywxMTg3em0tMzIzMiAtMTQ1MGMtMTA4MCwyMjUgLTE4NjAsMTMzNCAtMTc4NywyMTY4IDgwLDkxNCAxMTA5LDE4NzIgMjMyNCwxODM1IDExMTEsLTM0IDE4MTksLTExNTUgMTg0NCwtMjAyMiAyOCwtOTcwIC05ODQsLTIyNzEgLTIzODEsLTE5ODB6bS0yMjEzIDUxNjNjNzcsMjUzIDM5Myw2MzAgMTcxLDgyNSAtNTQ5LDQ4MiAtNjg4LC0yMjYgLTUxNSwtNDc0bDM0NCAtMzUxem0zODQgLTkzYzQyLDIxMSA5LDExMDEgNTI4LDc4OCA1NzAsLTM0NCAtMzQyLC02NzcgLTUzNiwtODAwbDkgMTF6bS01MjUzIC0xNTYwYzI1MywxNDQgNDg0LDE2NCA3MzIsMzQwbC0xNTggNDgwYy0xMjksMzIyIC0yNjAsNTkwIDIwNyw3MzUgNjMzLDE5NiA2NzAsLTQ0MiA4ODUsLTg3OCAxOTQsNjMgNDUzLDE0MCA2NDAsMTk0IDIwNCw1OSA1NTAsLTc5IDYxNSwtMjY5IDExNywtMzQyIC0yODksLTQ3OSAtNTEwLC01NjkgLTEzMCwtNTYgLTMxNCwtMTAwIC00NDEsLTE2MGwyMDggLTY0OGMxNDIsLTQzMSAtNjc0LC05MTcgLTk1OSwtMTU1bC0xNzkgNDY0Yy0yMDgsLTEwOSAtNTA2LC0xNzAgLTczMiwtMjQ0IC0yMjcsLTczIC03MTcsNDc2IC0zMDgsNzA4eiIvPg0KIDwvZz4NCjwvc3ZnPg0K );
            width:18px;
            height:18px;
            display:block;
            background-repeat: no-repeat;
            background-position: center;
            -webkit-background-size: 18px auto;
            background-size: 18px auto;
	    }
	' );
}

/**
 * Set up the bp-messages component.
 */
add_action( 'bp_setup_components', 'bp_setup_submissions', 8 );
function bp_setup_submissions() {
	
	bp_zf_include_submissions_component();
	
	if( bp_is_active( BP_Zombify_Submissions_Component::get_id() ) ) {
		
		$bp = buddypress();
		$bp->zf_submissions = new BP_Zombify_Submissions_Component();
	}
}