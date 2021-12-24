<?php
/**
 * Easy Social Share Buttons plugin functions
 *
 * @package BoomBox_Theme
 * @since   1.0.0
 * @version 2.0.0
 */

// Prevent direct script access.
if ( !defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

if ( !class_exists( 'Boombox_Essb' ) ) {

	final class Boombox_Essb {

		/**
		 * Holds class single instance
		 * @var null
		 */
		private static $_instance = NULL;

		/**
		 * Get instance
		 * @return Boombox_Essb|null
		 */
		public static function get_instance () {

			if ( NULL == static::$_instance ) {
				static::$_instance = new self();
			}

			return static::$_instance;

		}

		/**
		 * Holds displaying position name
		 * @var string
		 */
		private $display_position_name = 'display-41';


		/**
		 * Holds displaying position name. Added after ESSB version 7
		 * @var string
		 */
		private $display_position_name_essb_v7 = 'positions|display-41';

		/**
		 * Holds position name
		 * @var string
		 */
		private $position_name = 'boombox';

		/**
		 * Holds rendering state
		 * @var bool
		 */
		private $rendering = false;

		/**
		 * Holds options
		 * @var array
		 */
		private $options;

		/**
		 * Boombox_Essb constructor.
		 */
		private function __construct () {
			if ( ( defined( 'WP_INSTALLING' ) && WP_INSTALLING ) ) {
				return;
			}

			if ( !boombox_plugin_management_service()->is_plugin_active( 'easy-social-share-buttons3/easy-social-share-buttons3.php' ) ) {
				return;
			}

			$this->options = get_option( ESSB3_OPTIONS_NAME );
			$this->hooks();

			do_action( 'boombox/essb/wakeup', $this );
		}

		/**
		 * A dummy magic method to prevent Boombox_Essb from being cloned.
		 *
		 */
		public function __clone () {
			throw new Exception( 'Cloning ' . __CLASS__ . ' is forbidden' );
		}

		/**
		 * Setup Hooks
		 */
		private function hooks () {
			add_action( 'init', array( $this, 'assign_positions' ), 99 );

			add_filter( 'essb4_custom_method_list', array( $this, 'register_position' ), 10, 1 );
			add_filter( 'essb4_custom_positions', array( $this, 'register_display_position' ), 10, 1 );
			add_filter( 'essb_is_theme_integrated', array( $this, 'essb_theme_integrated' ), 10, 1 );
			add_filter( 'essb4_button_positions', array( $this, 'position_options' ), 10, 1 );
			add_filter( 'essb4_button_positions_mobile', array( $this, 'position_options' ), 10, 1 );
			add_filter( 'essb4_position_style_boombox', array( $this, 'essb_position_style_boombox' ), 10, 1 );
			add_filter( 'essb4_get_cached_counters', array( $this, 'boombox_essb_get_cached_counters' ), 10, 1 );
			add_filter( 'essb_opengraph_desc', array( $this, 'fix_user_og_description' ), 10, 1 );

			add_filter( 'boombox/customizer/fields/extras_posts_ranking_system', array( $this, 'add_fake_share_count_to_customizer' ), 10, 3 );
			add_filter( 'boombox/customizer_default_values', array( $this, 'edit_customizer_default_values' ), 10, 1 );
			add_filter( 'boombox/fake_share_count', array( $this, 'get_fake_share_count' ), 10, 1 );
			add_filter( 'boombox/shares_meta_key', array( $this, 'edit_ranking_shares_meta_key' ), 10, 1 );
			add_filter( 'mashsb_opengraph_meta', array( $this, 'remove_mashshare_meta_tags' ), 999, 1 );
			add_filter( 'mashsb_twittercard_meta', array( $this, 'remove_mashshare_meta_tags' ), 999, 1 );
			add_action( 'boombox/tgma/update/easy-social-share-buttons3/easy-social-share-buttons3.php', array( $this, 'plugin_updated' ), 10, 1 );
			add_action( 'admin_notices', array( $this, 'render_amp_share_plugin_warning_message' ) );
			add_filter( 'boombox_conditions_choices', array( $this, 'add_most_shared_to_conditions' ), 10, 1 );
			add_filter( 'boombox_trending_conditions_choices', array( $this, 'add_most_shared_to_conditions' ), 10, 1 );

			if ( boombox_plugin_management_service()->is_plugin_active( 'amp/amp.php' ) && class_exists( 'ESSBAmpSupport' ) ) {
				add_action( 'pre_amp_render_post', array( $this, 'amp_setup_template_hooks' ), 10, 1 );
				add_action( 'wp', array( $this, 'try_to_remove_amp_buttons_from_non_amp_mobile_layout' ) );
			}
		}

		/**
		 * Remove activation notice and make plugin to be integrated from theme
		 *
		 * @param bool $is_integrated Current integration state
		 *
		 * @return bool
		 */
		public function essb_theme_integrated ( $is_integrated ) {
			$is_integrated = true;

			return $is_integrated;
		}

		/**
		 * Check if is rendering buttons
		 *
		 * @return bool
		 */
		public function is_rendering () {
			return (bool)$this->rendering;
		}

		/**
		 * Get plugin options
		 *
		 * @return array
		 */
		public function get_options () {
			return $this->options;
		}

		/**
		 * Setup button
		 *
		 * @param array $button_style Current style
		 *
		 * @return array
		 */
		public function essb_position_style_boombox ( $button_style ) {
			if ( $this->is_rendering() ) {
				$button_style[ 'show_counter' ] = in_array( 'share_count', boombox_get_theme_option( 'single_post_general_share_box_elements' ) );
			}

			return $button_style;
		}

		/**
		 * Overwrite plugin activation callback
		 */
		public function install () {
			$mail_salt_check = get_option( ESSB3_MAIL_SALT );
			if ( !$mail_salt_check || empty( $mail_salt_check ) ) {
				$new_salt = mt_rand();
				update_option( ESSB3_MAIL_SALT, $new_salt );
			}

			$exist_settings = get_option( ESSB3_OPTIONS_NAME );
			if ( !$exist_settings ) {
				$default_options = 'eyJidXR0b25fc3R5bGUiOiJidXR0b24iLCJzdHlsZSI6IjMyIiwiZnVsbHdpZHRoX3NoYXJlX2J1dHRvbnNfY29sdW1ucyI6IjEiLCJuZXR3b3JrcyI6WyJmYWNlYm9vayIsInR3aXR0ZXIiLCJnb29nbGUiLCJwaW50ZXJlc3QiLCJsaW5rZWRpbiJdLCJuZXR3b3Jrc19vcmRlciI6WyJmYWNlYm9vayIsInR3aXR0ZXIiLCJnb29nbGUiLCJwaW50ZXJlc3QiLCJsaW5rZWRpbiIsImRpZ2ciLCJkZWwiLCJzdHVtYmxldXBvbiIsInR1bWJsciIsInZrIiwicHJpbnQiLCJtYWlsIiwiZmxhdHRyIiwicmVkZGl0IiwiYnVmZmVyIiwibG92ZSIsIndlaWJvIiwicG9ja2V0IiwieGluZyIsIm9rIiwibXdwIiwibW9yZSIsIndoYXRzYXBwIiwibWVuZWFtZSIsImJsb2dnZXIiLCJhbWF6b24iLCJ5YWhvb21haWwiLCJnbWFpbCIsImFvbCIsIm5ld3N2aW5lIiwiaGFja2VybmV3cyIsImV2ZXJub3RlIiwibXlzcGFjZSIsIm1haWxydSIsInZpYWRlbyIsImxpbmUiLCJmbGlwYm9hcmQiLCJjb21tZW50cyIsInl1bW1seSIsInNtcyIsInZpYmVyIiwidGVsZWdyYW0iLCJzdWJzY3JpYmUiLCJza3lwZSIsIm1lc3NlbmdlciIsImtha2FvdGFsayIsInNoYXJlIl0sIm1vcmVfYnV0dG9uX2Z1bmMiOiIxIiwibW9yZV9idXR0b25faWNvbiI6InBsdXMiLCJ0d2l0dGVyX3NoYXJlc2hvcnRfc2VydmljZSI6IndwIiwibWFpbF9mdW5jdGlvbiI6ImZvcm0iLCJ3aGF0c2FwcF9zaGFyZXNob3J0X3NlcnZpY2UiOiJ3cCIsImZsYXR0cl9sYW5nIjoic3FfQUwiLCJjb3VudGVyX3BvcyI6InJpZ2h0bSIsImZvcmNlX2NvdW50ZXJzX2FkbWluX3R5cGUiOiJ3cCIsInRvdGFsX2NvdW50ZXJfcG9zIjoibGVmdGJpZyIsInVzZXJfbmV0d29ya19uYW1lX2ZhY2Vib29rIjoiRmFjZWJvb2siLCJ1c2VyX25ldHdvcmtfbmFtZV90d2l0dGVyIjoiVHdpdHRlciIsInVzZXJfbmV0d29ya19uYW1lX2dvb2dsZSI6Ikdvb2dsZSsiLCJ1c2VyX25ldHdvcmtfbmFtZV9waW50ZXJlc3QiOiJQaW50ZXJlc3QiLCJ1c2VyX25ldHdvcmtfbmFtZV9saW5rZWRpbiI6IkxpbmtlZEluIiwidXNlcl9uZXR3b3JrX25hbWVfZGlnZyI6IkRpZ2ciLCJ1c2VyX25ldHdvcmtfbmFtZV9kZWwiOiJEZWwiLCJ1c2VyX25ldHdvcmtfbmFtZV9zdHVtYmxldXBvbiI6IlN0dW1ibGVVcG9uIiwidXNlcl9uZXR3b3JrX25hbWVfdHVtYmxyIjoiVHVtYmxyIiwidXNlcl9uZXR3b3JrX25hbWVfdmsiOiJWS29udGFrdGUiLCJ1c2VyX25ldHdvcmtfbmFtZV9wcmludCI6IlByaW50IiwidXNlcl9uZXR3b3JrX25hbWVfbWFpbCI6IkVtYWlsIiwidXNlcl9uZXR3b3JrX25hbWVfZmxhdHRyIjoiRmxhdHRyIiwidXNlcl9uZXR3b3JrX25hbWVfcmVkZGl0IjoiUmVkZGl0IiwidXNlcl9uZXR3b3JrX25hbWVfYnVmZmVyIjoiQnVmZmVyIiwidXNlcl9uZXR3b3JrX25hbWVfbG92ZSI6IkxvdmUgVGhpcyIsInVzZXJfbmV0d29ya19uYW1lX3dlaWJvIjoiV2VpYm8iLCJ1c2VyX25ldHdvcmtfbmFtZV9wb2NrZXQiOiJQb2NrZXQiLCJ1c2VyX25ldHdvcmtfbmFtZV94aW5nIjoiWGluZyIsInVzZXJfbmV0d29ya19uYW1lX29rIjoiT2Rub2tsYXNzbmlraSIsInVzZXJfbmV0d29ya19uYW1lX213cCI6Ik1hbmFnZVdQLm9yZyIsInVzZXJfbmV0d29ya19uYW1lX21vcmUiOiJNb3JlIEJ1dHRvbiIsInVzZXJfbmV0d29ya19uYW1lX3doYXRzYXBwIjoiV2hhdHNBcHAiLCJ1c2VyX25ldHdvcmtfbmFtZV9tZW5lYW1lIjoiTWVuZWFtZSIsInVzZXJfbmV0d29ya19uYW1lX2Jsb2dnZXIiOiJCbG9nZ2VyIiwidXNlcl9uZXR3b3JrX25hbWVfYW1hem9uIjoiQW1hem9uIiwidXNlcl9uZXR3b3JrX25hbWVfeWFob29tYWlsIjoiWWFob28gTWFpbCIsInVzZXJfbmV0d29ya19uYW1lX2dtYWlsIjoiR21haWwiLCJ1c2VyX25ldHdvcmtfbmFtZV9hb2wiOiJBT0wiLCJ1c2VyX25ldHdvcmtfbmFtZV9uZXdzdmluZSI6Ik5ld3N2aW5lIiwidXNlcl9uZXR3b3JrX25hbWVfaGFja2VybmV3cyI6IkhhY2tlck5ld3MiLCJ1c2VyX25ldHdvcmtfbmFtZV9ldmVybm90ZSI6IkV2ZXJub3RlIiwidXNlcl9uZXR3b3JrX25hbWVfbXlzcGFjZSI6Ik15U3BhY2UiLCJ1c2VyX25ldHdvcmtfbmFtZV9tYWlscnUiOiJNYWlsLnJ1IiwidXNlcl9uZXR3b3JrX25hbWVfdmlhZGVvIjoiVmlhZGVvIiwidXNlcl9uZXR3b3JrX25hbWVfbGluZSI6IkxpbmUiLCJ1c2VyX25ldHdvcmtfbmFtZV9mbGlwYm9hcmQiOiJGbGlwYm9hcmQiLCJ1c2VyX25ldHdvcmtfbmFtZV9jb21tZW50cyI6IkNvbW1lbnRzIiwidXNlcl9uZXR3b3JrX25hbWVfeXVtbWx5IjoiWXVtbWx5IiwiZ2FfdHJhY2tpbmdfbW9kZSI6InNpbXBsZSIsInR3aXR0ZXJfY2FyZF90eXBlIjoic3VtbWFyeSIsIm5hdGl2ZV9vcmRlciI6WyJnb29nbGUiLCJ0d2l0dGVyIiwiZmFjZWJvb2siLCJsaW5rZWRpbiIsInBpbnRlcmVzdCIsInlvdXR1YmUiLCJtYW5hZ2V3cCIsInZrIl0sImZhY2Vib29rX2xpa2VfdHlwZSI6Imxpa2UiLCJnb29nbGVfbGlrZV90eXBlIjoicGx1cyIsInR3aXR0ZXJfdHdlZXQiOiJmb2xsb3ciLCJwaW50ZXJlc3RfbmF0aXZlX3R5cGUiOiJmb2xsb3ciLCJza2luX25hdGl2ZV9za2luIjoiZmxhdCIsInByb2ZpbGVzX2J1dHRvbl90eXBlIjoic3F1YXJlIiwicHJvZmlsZXNfYnV0dG9uX2ZpbGwiOiJmaWxsIiwicHJvZmlsZXNfYnV0dG9uX3NpemUiOiJzbWFsbCIsInByb2ZpbGVzX2Rpc3BsYXlfcG9zaXRpb24iOiJsZWZ0IiwicHJvZmlsZXNfb3JkZXIiOlsidHdpdHRlciIsImZhY2Vib29rIiwiZ29vZ2xlIiwicGludGVyZXN0IiwiZm91cnNxdWFyZSIsInlhaG9vIiwic2t5cGUiLCJ5ZWxwIiwiZmVlZGJ1cm5lciIsImxpbmtlZGluIiwidmlhZGVvIiwieGluZyIsIm15c3BhY2UiLCJzb3VuZGNsb3VkIiwic3BvdGlmeSIsImdyb292ZXNoYXJrIiwibGFzdGZtIiwieW91dHViZSIsInZpbWVvIiwiZGFpbHltb3Rpb24iLCJ2aW5lIiwiZmxpY2tyIiwiNTAwcHgiLCJpbnN0YWdyYW0iLCJ3b3JkcHJlc3MiLCJ0dW1ibHIiLCJibG9nZ2VyIiwidGVjaG5vcmF0aSIsInJlZGRpdCIsImRyaWJiYmxlIiwic3R1bWJsZXVwb24iLCJkaWdnIiwiZW52YXRvIiwiYmVoYW5jZSIsImRlbGljaW91cyIsImRldmlhbnRhcnQiLCJmb3Jyc3QiLCJwbGF5IiwiemVycGx5Iiwid2lraXBlZGlhIiwiYXBwbGUiLCJmbGF0dHIiLCJnaXRodWIiLCJjaGltZWluIiwiZnJpZW5kZmVlZCIsIm5ld3N2aW5lIiwiaWRlbnRpY2EiLCJiZWJvIiwienluZ2EiLCJzdGVhbSIsInhib3giLCJ3aW5kb3dzIiwib3V0bG9vayIsImNvZGVyd2FsbCIsInRyaXBhZHZpc29yIiwiYXBwbmV0IiwiZ29vZHJlYWRzIiwidHJpcGl0IiwibGFueXJkIiwic2xpZGVzaGFyZSIsImJ1ZmZlciIsInJzcyIsInZrb250YWt0ZSIsImRpc3F1cyIsImhvdXp6IiwibWFpbCIsInBhdHJlb24iLCJwYXlwYWwiLCJwbGF5c3RhdGlvbiIsInNtdWdtdWciLCJzd2FybSIsInRyaXBsZWoiLCJ5YW1tZXIiLCJzdGFja292ZXJmbG93IiwiZHJ1cGFsIiwib2Rub2tsYXNzbmlraSIsImFuZHJvaWQiLCJtZWV0dXAiLCJwZXJzb25hIl0sImFmdGVyY2xvc2VfdHlwZSI6ImZvbGxvdyIsImFmdGVyY2xvc2VfbGlrZV9jb2xzIjoib25lY29sIiwiZXNtbF90dGwiOiIxIiwiZXNtbF9wcm92aWRlciI6InNoYXJlZGNvdW50IiwiZXNtbF9hY2Nlc3MiOiJtYW5hZ2Vfb3B0aW9ucyIsInNob3J0dXJsX3R5cGUiOiJ3cCIsImRpc3BsYXlfaW5fdHlwZXMiOlsicG9zdCJdLCJkaXNwbGF5X2V4Y2VycHRfcG9zIjoidG9wIiwidG9wYmFyX2J1dHRvbnNfYWxpZ24iOiJsZWZ0IiwidG9wYmFyX2NvbnRlbnRhcmVhX3BvcyI6ImxlZnQiLCJib3R0b21iYXJfYnV0dG9uc19hbGlnbiI6ImxlZnQiLCJib3R0b21iYXJfY29udGVudGFyZWFfcG9zIjoibGVmdCIsImZseWluX3Bvc2l0aW9uIjoicmlnaHQiLCJzaXNfbmV0d29ya19vcmRlciI6WyJmYWNlYm9vayIsInR3aXR0ZXIiLCJnb29nbGUiLCJsaW5rZWRpbiIsInBpbnRlcmVzdCIsInR1bWJsciIsInJlZGRpdCIsImRpZ2ciLCJkZWxpY2lvdXMiLCJ2a29udGFrdGUiLCJvZG5va2xhc3NuaWtpIl0sInNpc19zdHlsZSI6ImZsYXQtc21hbGwiLCJzaXNfYWxpZ25feCI6ImxlZnQiLCJzaXNfYWxpZ25feSI6InRvcCIsInNpc19vcmllbnRhdGlvbiI6Imhvcml6b250YWwiLCJtb2JpbGVfc2hhcmVidXR0b25zYmFyX2NvdW50IjoiMiIsInNoYXJlYmFyX2NvdW50ZXJfcG9zIjoiaW5zaWRlIiwic2hhcmViYXJfdG90YWxfY291bnRlcl9wb3MiOiJiZWZvcmUiLCJzaGFyZWJhcl9uZXR3b3Jrc19vcmRlciI6WyJmYWNlYm9va3xGYWNlYm9vayIsInR3aXR0ZXJ8VHdpdHRlciIsImdvb2dsZXxHb29nbGUrIiwicGludGVyZXN0fFBpbnRlcmVzdCIsImxpbmtlZGlufExpbmtlZEluIiwiZGlnZ3xEaWdnIiwiZGVsfERlbCIsInN0dW1ibGV1cG9ufFN0dW1ibGVVcG9uIiwidHVtYmxyfFR1bWJsciIsInZrfFZLb250YWt0ZSIsInByaW50fFByaW50IiwibWFpbHxFbWFpbCIsImZsYXR0cnxGbGF0dHIiLCJyZWRkaXR8UmVkZGl0IiwiYnVmZmVyfEJ1ZmZlciIsImxvdmV8TG92ZSBUaGlzIiwid2VpYm98V2VpYm8iLCJwb2NrZXR8UG9ja2V0IiwieGluZ3xYaW5nIiwib2t8T2Rub2tsYXNzbmlraSIsIm13cHxNYW5hZ2VXUC5vcmciLCJtb3JlfE1vcmUgQnV0dG9uIiwid2hhdHNhcHB8V2hhdHNBcHAiLCJtZW5lYW1lfE1lbmVhbWUiLCJibG9nZ2VyfEJsb2dnZXIiLCJhbWF6b258QW1hem9uIiwieWFob29tYWlsfFlhaG9vIE1haWwiLCJnbWFpbHxHbWFpbCIsImFvbHxBT0wiLCJuZXdzdmluZXxOZXdzdmluZSIsImhhY2tlcm5ld3N8SGFja2VyTmV3cyIsImV2ZXJub3RlfEV2ZXJub3RlIiwibXlzcGFjZXxNeVNwYWNlIiwibWFpbHJ1fE1haWwucnUiLCJ2aWFkZW98VmlhZGVvIiwibGluZXxMaW5lIiwiZmxpcGJvYXJkfEZsaXBib2FyZCIsImNvbW1lbnRzfENvbW1lbnRzIiwieXVtbWx5fFl1bW1seSIsInNtc3xTTVMiLCJ2aWJlcnxWaWJlciIsInRlbGVncmFtfFRlbGVncmFtIiwic3Vic2NyaWJlfFN1YnNjcmliZSIsInNreXBlfFNreXBlIiwibWVzc2VuZ2VyfEZhY2Vib29rIE1lc3NlbmdlciIsImtha2FvdGFsa3xLYWthbyIsInNoYXJlfFNoYXJlIl0sInNoYXJlcG9pbnRfY291bnRlcl9wb3MiOiJpbnNpZGUiLCJzaGFyZXBvaW50X3RvdGFsX2NvdW50ZXJfcG9zIjoiYmVmb3JlIiwic2hhcmVwb2ludF9uZXR3b3Jrc19vcmRlciI6WyJmYWNlYm9va3xGYWNlYm9vayIsInR3aXR0ZXJ8VHdpdHRlciIsImdvb2dsZXxHb29nbGUrIiwicGludGVyZXN0fFBpbnRlcmVzdCIsImxpbmtlZGlufExpbmtlZEluIiwiZGlnZ3xEaWdnIiwiZGVsfERlbCIsInN0dW1ibGV1cG9ufFN0dW1ibGVVcG9uIiwidHVtYmxyfFR1bWJsciIsInZrfFZLb250YWt0ZSIsInByaW50fFByaW50IiwibWFpbHxFbWFpbCIsImZsYXR0cnxGbGF0dHIiLCJyZWRkaXR8UmVkZGl0IiwiYnVmZmVyfEJ1ZmZlciIsImxvdmV8TG92ZSBUaGlzIiwid2VpYm98V2VpYm8iLCJwb2NrZXR8UG9ja2V0IiwieGluZ3xYaW5nIiwib2t8T2Rub2tsYXNzbmlraSIsIm13cHxNYW5hZ2VXUC5vcmciLCJtb3JlfE1vcmUgQnV0dG9uIiwid2hhdHNhcHB8V2hhdHNBcHAiLCJtZW5lYW1lfE1lbmVhbWUiLCJibG9nZ2VyfEJsb2dnZXIiLCJhbWF6b258QW1hem9uIiwieWFob29tYWlsfFlhaG9vIE1haWwiLCJnbWFpbHxHbWFpbCIsImFvbHxBT0wiLCJuZXdzdmluZXxOZXdzdmluZSIsImhhY2tlcm5ld3N8SGFja2VyTmV3cyIsImV2ZXJub3RlfEV2ZXJub3RlIiwibXlzcGFjZXxNeVNwYWNlIiwibWFpbHJ1fE1haWwucnUiLCJ2aWFkZW98VmlhZGVvIiwibGluZXxMaW5lIiwiZmxpcGJvYXJkfEZsaXBib2FyZCIsImNvbW1lbnRzfENvbW1lbnRzIiwieXVtbWx5fFl1bW1seSIsInNtc3xTTVMiLCJ2aWJlcnxWaWJlciIsInRlbGVncmFtfFRlbGVncmFtIiwic3Vic2NyaWJlfFN1YnNjcmliZSIsInNreXBlfFNreXBlIiwibWVzc2VuZ2VyfEZhY2Vib29rIE1lc3NlbmdlciIsImtha2FvdGFsa3xLYWthbyIsInNoYXJlfFNoYXJlIl0sInNoYXJlYm90dG9tX25ldHdvcmtzX29yZGVyIjpbImZhY2Vib29rfEZhY2Vib29rIiwidHdpdHRlcnxUd2l0dGVyIiwiZ29vZ2xlfEdvb2dsZSsiLCJwaW50ZXJlc3R8UGludGVyZXN0IiwibGlua2VkaW58TGlua2VkSW4iLCJkaWdnfERpZ2ciLCJkZWx8RGVsIiwic3R1bWJsZXVwb258U3R1bWJsZVVwb24iLCJ0dW1ibHJ8VHVtYmxyIiwidmt8VktvbnRha3RlIiwicHJpbnR8UHJpbnQiLCJtYWlsfEVtYWlsIiwiZmxhdHRyfEZsYXR0ciIsInJlZGRpdHxSZWRkaXQiLCJidWZmZXJ8QnVmZmVyIiwibG92ZXxMb3ZlIFRoaXMiLCJ3ZWlib3xXZWlibyIsInBvY2tldHxQb2NrZXQiLCJ4aW5nfFhpbmciLCJva3xPZG5va2xhc3NuaWtpIiwibXdwfE1hbmFnZVdQLm9yZyIsIm1vcmV8TW9yZSBCdXR0b24iLCJ3aGF0c2FwcHxXaGF0c0FwcCIsIm1lbmVhbWV8TWVuZWFtZSIsImJsb2dnZXJ8QmxvZ2dlciIsImFtYXpvbnxBbWF6b24iLCJ5YWhvb21haWx8WWFob28gTWFpbCIsImdtYWlsfEdtYWlsIiwiYW9sfEFPTCIsIm5ld3N2aW5lfE5ld3N2aW5lIiwiaGFja2VybmV3c3xIYWNrZXJOZXdzIiwiZXZlcm5vdGV8RXZlcm5vdGUiLCJteXNwYWNlfE15U3BhY2UiLCJtYWlscnV8TWFpbC5ydSIsInZpYWRlb3xWaWFkZW8iLCJsaW5lfExpbmUiLCJmbGlwYm9hcmR8RmxpcGJvYXJkIiwiY29tbWVudHN8Q29tbWVudHMiLCJ5dW1tbHl8WXVtbWx5Iiwic21zfFNNUyIsInZpYmVyfFZpYmVyIiwidGVsZWdyYW18VGVsZWdyYW0iLCJzdWJzY3JpYmV8U3Vic2NyaWJlIiwic2t5cGV8U2t5cGUiLCJtZXNzZW5nZXJ8RmFjZWJvb2sgTWVzc2VuZ2VyIiwia2FrYW90YWxrfEtha2FvIiwic2hhcmV8U2hhcmUiXSwiY29udGVudF9wb3NpdGlvbiI6ImNvbnRlbnRfYm90dG9tIiwiZXNzYl9jYWNoZV9tb2RlIjoiZnVsbCIsInR1cm5vZmZfZXNzYl9hZHZhbmNlZF9ib3giOiJ0cnVlIiwiZXNzYl9hY2Nlc3MiOiJtYW5hZ2Vfb3B0aW9ucyIsImFwcGx5X2NsZWFuX2J1dHRvbnNfbWV0aG9kIjoiZGVmYXVsdCIsIm1haWxfc3ViamVjdCI6IlZpc2l0IHRoaXMgc2l0ZSAlJXNpdGV1cmwlJSIsIm1haWxfYm9keSI6IkhpLCB0aGlzIG1heSBiZSBpbnRlcmVzdGluZyB5b3U6ICUldGl0bGUlJSEgVGhpcyBpcyB0aGUgbGluazogJSVwZXJtYWxpbmslJSIsImZhY2Vib29rdG90YWwiOiJ0cnVlIiwiYWN0aXZhdGVfdG90YWxfY291bnRlcl90ZXh0Ijoic2hhcmVzIiwiZnVsbHdpZHRoX2FsaWduIjoibGVmdCIsInR3aXR0ZXJfbWVzc2FnZV9vcHRpbWl6ZV9tZXRob2QiOiIxIiwibWFpbF9mdW5jdGlvbl9jb21tYW5kIjoiaG9zdCIsIm1haWxfZnVuY3Rpb25fc2VjdXJpdHkiOiJsZXZlbDEiLCJ0d2l0dGVyX2NvdW50ZXJzIjoic2VsZiIsImNhY2hlX2NvdW50ZXJfcmVmcmVzaCI6IjEiLCJ0d2l0dGVyX3NoYXJlc2hvcnQiOiJ0cnVlIiwidXNlcl9uZXR3b3JrX25hbWVfc21zIjoiU01TIiwidXNlcl9uZXR3b3JrX25hbWVfdmliZXIiOiJWaWJlciIsInVzZXJfbmV0d29ya19uYW1lX3RlbGVncmFtIjoiVGVsZWdyYW0iLCJ1c2VyX25ldHdvcmtfbmFtZV9zdWJzY3JpYmUiOiJTdWJzY3JpYmUiLCJ1c2VyX25ldHdvcmtfbmFtZV9za3lwZSI6IlNreXBlIiwidXNlcl9uZXR3b3JrX25hbWVfbWVzc2VuZ2VyIjoiRmFjZWJvb2sgTWVzc2VuZ2VyIiwidXNlcl9uZXR3b3JrX25hbWVfa2FrYW90YWxrIjoiS2FrYW8iLCJ1c2VyX25ldHdvcmtfbmFtZV9zaGFyZSI6IlNoYXJlIiwic2hhcmVfYnV0dG9uX2Z1bmMiOiIxIiwic2hhcmVfYnV0dG9uX2NvdW50ZXIiOiJoaWRkZW4iLCJzdWJzY3JpYmVfZnVuY3Rpb24iOiJmb3JtIiwic3Vic2NyaWJlX29wdGluX2Rlc2lnbiI6ImRlc2lnbjEiLCJzdWJzY3JpYmVfb3B0aW5fZGVzaWduX3BvcHVwIjoiZGVzaWduMSIsImNvdW50ZXJfbW9kZSI6IjM2MCIsIm9wZW5ncmFwaF90YWdzIjoidHJ1ZSIsImFmZndwX2FjdGl2ZV9tb2RlIjoiaWQiLCJzaXNfcG9zaXRpb24iOiJ0b3AtbGVmdCIsImhlcm9zaGFyZV9zZWNvbmRfdHlwZSI6InRvcCIsInBvc3RiYXJfYnV0dG9uX3N0eWxlIjoicmVjb21tZW5kZWQiLCJwb3N0YmFyX2NvdW50ZXJfcG9zIjoiaGlkZGVuIiwicG9pbnRfcG9zaXRpb24iOiJib3R0b21yaWdodCIsInBvaW50X29wZW5fYXV0byI6Im5vIiwicG9pbnRfc3R5bGUiOiJzaW1wbGUiLCJwb2ludF9zaGFwZSI6InJvdW5kIiwicG9pbnRfYnV0dG9uX3N0eWxlIjoicmVjb21tZW5kZWQiLCJwb2ludF90ZW1wbGF0ZSI6IjYiLCJwb2ludF9jb3VudGVyX3BvcyI6Imluc2lkZSIsIm1vYmlsZV9uZXR3b3Jrc19vcmRlciI6WyJmYWNlYm9va3xGYWNlYm9vayIsInR3aXR0ZXJ8VHdpdHRlciIsImdvb2dsZXxHb29nbGUrIiwicGludGVyZXN0fFBpbnRlcmVzdCIsImxpbmtlZGlufExpbmtlZEluIiwiZGlnZ3xEaWdnIiwiZGVsfERlbCIsInN0dW1ibGV1cG9ufFN0dW1ibGVVcG9uIiwidHVtYmxyfFR1bWJsciIsInZrfFZLb250YWt0ZSIsInByaW50fFByaW50IiwibWFpbHxFbWFpbCIsImZsYXR0cnxGbGF0dHIiLCJyZWRkaXR8UmVkZGl0IiwiYnVmZmVyfEJ1ZmZlciIsImxvdmV8TG92ZSBUaGlzIiwid2VpYm98V2VpYm8iLCJwb2NrZXR8UG9ja2V0IiwieGluZ3xYaW5nIiwib2t8T2Rub2tsYXNzbmlraSIsIm13cHxNYW5hZ2VXUC5vcmciLCJtb3JlfE1vcmUgQnV0dG9uIiwid2hhdHNhcHB8V2hhdHNBcHAiLCJtZW5lYW1lfE1lbmVhbWUiLCJibG9nZ2VyfEJsb2dnZXIiLCJhbWF6b258QW1hem9uIiwieWFob29tYWlsfFlhaG9vIE1haWwiLCJnbWFpbHxHbWFpbCIsImFvbHxBT0wiLCJuZXdzdmluZXxOZXdzdmluZSIsImhhY2tlcm5ld3N8SGFja2VyTmV3cyIsImV2ZXJub3RlfEV2ZXJub3RlIiwibXlzcGFjZXxNeVNwYWNlIiwibWFpbHJ1fE1haWwucnUiLCJ2aWFkZW98VmlhZGVvIiwibGluZXxMaW5lIiwiZmxpcGJvYXJkfEZsaXBib2FyZCIsImNvbW1lbnRzfENvbW1lbnRzIiwieXVtbWx5fFl1bW1seSIsInNtc3xTTVMiLCJ2aWJlcnxWaWJlciIsInRlbGVncmFtfFRlbGVncmFtIiwic3Vic2NyaWJlfFN1YnNjcmliZSIsInNreXBlfFNreXBlIiwibWVzc2VuZ2VyfEZhY2Vib29rIE1lc3NlbmdlciIsImtha2FvdGFsa3xLYWthbyIsInNoYXJlfFNoYXJlIl0sImFmdGVyc2hhcmVfb3B0aW5fZGVzaWduIjoiZGVzaWduMSIsInNob3J0dXJsX2JpdGx5YXBpX3ZlcnNpb24iOiJwcmV2aW91cyIsInVzZV9taW5pZmllZF9jc3MiOiJ0cnVlIiwidXNlX21pbmlmaWVkX2pzIjoidHJ1ZSIsImxvYWRfanNfYXN5bmMiOiJ0cnVlIiwiY291bnRlcl9yZWNvdmVyX21vZGUiOiJ1bmNoYW5nZWQiLCJjb3VudGVyX3JlY292ZXJfcHJvdG9jb2wiOiJ1bmNoYW5nZWQifQ==';

				$options = json_decode( base64_decode( $default_options ), true );
				$options = apply_filters( 'boombox/essb/default_options', $options );
				$default_options = base64_encode( json_encode( $options ) );

				// set that we run plugin for first time
				update_option( ESSB3_FIRST_TIME_NAME, 'false' );
				$options_base = ESSB_Manager::convert_ready_made_option( $default_options );
				if ( $options_base ) {
					update_option( ESSB3_OPTIONS_NAME, $options_base );
				}
			}

			// clear stored add-ons on activation of plugin
			delete_option( 'essb3_addons' );

		}

		/**
		 * Register theme positions
		 *
		 * @param array $methods Current methods
		 *
		 * @return array
		 */
		public function register_position ( $methods ) {
			$methods[ $this->display_position_name ] = __( 'Boombox', 'boombox' );

			return $methods;
		}

		/**
		 * Register theme display positions
		 *
		 * @param array $positions Current positions
		 *
		 * @return array
		 */
		public function register_display_position ( $positions ) {
			$positions[ $this->position_name ] = __( 'Boombox', 'boombox' );

			return $positions;
		}

		/**
		 * Assign positions layouts
		 *
		 * @param array $positions Current positions
		 *
		 * @return array
		 */
		public function position_options ( $positions ) {
			$positions[ $this->position_name ] = array(
				'image' => 'assets/images/display-positions-09.png',
				'label' => __( 'Boombox Position', 'boombox' ),
			);

			return $positions;
		}

		/**
		 * Assign display positions to appropriate positions
		 */
		public function assign_positions () {
			if ( is_admin() && class_exists( 'ESSBOptionsStructureHelper' ) ) {
				essb_prepare_location_advanced_customization( 'where', $this->display_position_name_essb_v7, $this->position_name );
			}
		}

		/**
		 * Render theme position
		 */
		public function render () {
			$general_options = essb_core()->get_general_options();

			if ( is_array( $general_options ) ) {
				if ( in_array( $this->position_name, $general_options[ 'button_position' ] ) ) {
					$this->rendering = true;
					echo essb_core()->generate_share_buttons( $this->position_name );
					$this->rendering = false;
				}
			}
		}

		/**
		 * Adds fake count to total counter and each social network
		 *
		 * @param array $counters Current counters
		 *
		 * @return array
		 */
		public function boombox_essb_get_cached_counters ( $counters ) {
			$share_count = boombox_post_shares_count( $counters[ 'total' ], get_the_ID() );
			if ( $share_count > $counters[ 'total' ] ) {
				$to_add = ceil( $share_count / ( count( $counters ) - 1 ) );
				unset( $counters[ 'total' ] );

				foreach ( $counters as $network_name => $count ) {
					$counters[ $network_name ] = absint( $counters[ $network_name ] ) + $to_add;
				}

				$counters[ 'total' ] = $share_count;
			}

			return $counters;
		}

		/**
		 * Fix og:description meta for user profile
		 *
		 * @param string $og_description Current description
		 *
		 * @return string
		 */
		function fix_user_og_description ( $og_description ) {
			if ( boombox_plugin_management_service()->is_plugin_active( 'buddypress/bp-loader.php' ) && function_exists( 'bp_is_user' ) && bp_is_user() ) {
				$og_description = boombox_get_user_meta_description( bp_displayed_user_id() );
			}

			return $og_description;
		}

		/**
		 * Add options to customizer "Post Ranking System" section
		 *
		 * @param array  $fields   Current fields
		 * @param string $section  Section ID
		 * @param array  $defaults Default values
		 *
		 * @return array
		 */
		public function add_fake_share_count_to_customizer ( $fields, $section, $defaults ) {
			// "ESSB" Fake Share Count
			$fields[] = array(
				'settings' => 'extras_post_ranking_system_essb_fake_share_count',
				'label'    => esc_html__( '"ESSB" Fake Share Count', 'boombox' ),
				'section'  => $section,
				'type'     => 'number',
				'priority' => 205,
				'default'  => $defaults[ 'extras_post_ranking_system_essb_fake_share_count' ],
				'choices'  => array(
					'min'  => 0,
					'step' => 1,
				),
			);

			return $fields;
		}

		/**
		 * Setup customizer default values
		 *
		 * @param array $default_values Current
		 *
		 * @return array
		 */
		public function edit_customizer_default_values ( $default_values ) {
			$default_values[ 'extras_post_ranking_system_essb_fake_share_count' ] = 0;

			return $default_values;
		}

		/**
		 * Edit fake share count
		 *
		 * @param int $fake_count Current fake count
		 *
		 * @return int
		 */
		public function get_fake_share_count ( $fake_count ) {
			$fake_count = absint( boombox_get_theme_option( 'extras_post_ranking_system_essb_fake_share_count' ) );

			return $fake_count;
		}

		/**
		 * Use ESSB meta key for sharing calculation
		 *
		 * @param string $meta_key Current meta key
		 *
		 * @return string
		 */
		public function edit_ranking_shares_meta_key ( $meta_key ) {
			$meta_key = 'essb_c_total';

			return $meta_key;
		}

		/**
		 * Overwrite default options for theme
		 *
		 * @param array $options Current options
		 *
		 * @return array
		 */
		public function default_options ( $options ) {

			$boombox_options = array(
				'display_in_types'                        => array(
					'post', 'page',
				),
				'content_position'                        => 'content_manual',
				'button_position'                         => array(
					$this->position_name,
				),
				'boombox_activate'                        => 'true',
				'boombox_button_style'                    => 'button',
				'boombox_template'                        => '24',
				'boombox_more_button_icon'                => 'plus',
				'boombox_share_button_icon'               => 'share-alt',
				'boombox_share_button_counter'            => 'hidden',
				'boombox_counter_pos'                     => 'hidden',
				'boombox_total_counter_pos'               => 'leftbigicon',
				'boombox_fullwidth_align'                 => 'left',
				'boombox_fullwidth_share_buttons_columns' => 1,
				'boombox_networks'                        => array(
					'facebook',
					'twitter',
				),
				'boombox_button_pos'                      => 'center',
				'boombox_more_button_func'                => 4,
				'boombox_share_button_func'               => 4,
				'boombox_button_width'                    => 'flex',
			);

			if ( boombox_plugin_management_service()->is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
				$boombox_options[ 'display_in_types' ][] = 'product';
			}

			$options = array_merge( $options, $boombox_options );

			return $options;
		}

		/**
		 * Remove 'Mashshare' plugin meta tags
		 *
		 * @param string $meta_html Current html
		 *
		 * @return string
		 */
		public function remove_mashshare_meta_tags ( $meta_html ) {
			$meta_html = '';

			return $meta_html;
		}

		/**
		 * Add/remove hooks on amp template
		 *
		 * @param int $post_id Post ID
		 */
		public function amp_setup_template_hooks ( $post_id ) {
			if( method_exists('ESSBAmpSupport', 'getInstance') ){
				$essb_amp_instance = ESSBAmpSupport::getInstance();
			}else{
				$essb_amp_instance = ESSBAmpSupport::Instance();
			}

			remove_action( 'amp_post_template_css', array( $essb_amp_instance, 'amp_load_css' ) );
		}

		/**
		 * Try to remove AMP share buttons from non AMP mobile layouts
		 */
		public function try_to_remove_amp_buttons_from_non_amp_mobile_layout () {
			/**
			 * @var $essb_amp_instance ESSBAmpSupport
			 */
			if( method_exists('ESSBAmpSupport', 'getInstance') ){
				$essb_amp_instance = ESSBAmpSupport::getInstance();
			}else{
				$essb_amp_instance = ESSBAmpSupport::Instance();
			}

			if ( $essb_amp_instance->is_active_mobile_support() && !amp_is_request() ) {
				remove_filter( 'the_content', array( $essb_amp_instance, 'amp_display_share' ) );
			}
		}

		/**
		 * Plugin updated callback
		 *
		 * @param string $plugin_main_file Plugin main file
		 */
		public function plugin_updated ( $plugin_main_file ) {

			$plugin_data = array_change_key_case( get_plugin_data( trailingslashit( WP_PLUGIN_DIR ) . $plugin_main_file ) );
			$unsupported_plugin_main_file = 'essb-amp-support/essb-amp-support.php';
			if ( version_compare( $plugin_data[ 'version' ], '5.0', '>=' ) ) {

				$active_plugins_option_name = 'active_plugins';

				if ( is_multisite() ) {
					/***** Network installation */

					/***** Update all blogs in network */
					$blogs = get_sites( array( 'fields' => 'ids' ) );
					foreach ( $blogs as $blog_id ) {
						$active_plugins = get_blog_option( $blog_id, $active_plugins_option_name );
						$essb_amp_support_plugin_key = array_search( $unsupported_plugin_main_file, $active_plugins );
						if ( $essb_amp_support_plugin_key !== false ) {
							unset( $active_plugins[ $essb_amp_support_plugin_key ] );

							$active_plugins = array_values( $active_plugins );
							update_blog_option( $blog_id, $active_plugins_option_name, $active_plugins );

							$essb_options = get_blog_option( $blog_id, ESSB3_OPTIONS_NAME );
							$essb_options[ 'amp_positions' ] = 'true';
							if ( !isset( $essb_options[ 'content_position_amp' ] ) ) {
								$essb_options[ 'content_position_amp' ] = 'content_both';
							}
							update_blog_option( $blog_id, ESSB3_OPTIONS_NAME, $essb_options );
						}
					}

					/***** Disable unsupported plugin in network */
					$multisite_active_plugins_options_name = 'active_sitewide_plugins';
					$network_active_plugins = get_site_option( $multisite_active_plugins_options_name );
					if ( array_key_exists( $unsupported_plugin_main_file, $network_active_plugins ) ) {
						unset( $network_active_plugins[ $unsupported_plugin_main_file ] );

						update_site_option( $multisite_active_plugins_options_name, $network_active_plugins );
					}

				} else {
					/***** Single installation */

					$active_plugins = get_option( $active_plugins_option_name );
					$essb_amp_support_plugin_key = array_search( $unsupported_plugin_main_file, $active_plugins );
					if ( $essb_amp_support_plugin_key !== false ) {
						unset( $active_plugins[ $essb_amp_support_plugin_key ] );

						$active_plugins = array_values( $active_plugins );
						update_option( $active_plugins_option_name, $active_plugins );

						$essb_options = get_option( ESSB3_OPTIONS_NAME );
						$essb_options[ 'amp_positions' ] = 'true';
						if ( !isset( $essb_options[ 'content_position_amp' ] ) ) {
							$essb_options[ 'content_position_amp' ] = 'content_both';
						}
						update_option( ESSB3_OPTIONS_NAME, $essb_options );
					}

				}
			}

		}

		/**
		 * Render admin notice for preventing essb & essb amp support plugins conflict
		 */
		public function render_amp_share_plugin_warning_message () {
			$installed_plugins = get_plugins();
			$essb_plugin_main_file = 'easy-social-share-buttons3/easy-social-share-buttons3.php';
			$essb_amp_plugin_main_file = 'essb-amp-support/essb-amp-support.php';

			if ( !array_key_exists( $essb_amp_plugin_main_file, $installed_plugins ) ) {
				return;
			}

			$essb_plugin_data = array_change_key_case( $installed_plugins[ $essb_plugin_main_file ] );
			if ( version_compare( $essb_plugin_data[ 'version' ], '5.0', '<' ) ) {
				return;
			}

			$essb_amp_plugin_data = array_change_key_case( $installed_plugins[ $essb_amp_plugin_main_file ] );

			$message = sprintf(
				__( '<p>Starting from v5.0 "%1$s" plugin doesn\'t support "%2$s" plugin. Please delete it from <a href="%3$s">plugins</a>.</p><p>Now you can configure AMP share buttons in "%1$s -> Where to Display -> AMP Sharing" section. We strongly recommend do not activate "%2$s" plugin.</p>', 'boombox' ),
				$essb_plugin_data[ 'name' ],
				$essb_amp_plugin_data[ 'name' ],
				admin_url( 'plugins.php' )
			);
			?>
			<div class="notice notice-error"><p><b><?php echo $message; ?></b></p></div>
			<?php
		}

		/**
		 * Add most shared to conditions
		 *
		 * @param array $choices Current choices
		 *
		 * @return array
		 */
		public function add_most_shared_to_conditions ( $choices ) {
			$choices[ 'most_shared' ] = esc_html__( 'Most Shared', 'boombox' );

			return $choices;

		}

	}

}

/**
 * Overwrite "Easy Social Share Buttons" plugin installation function
 */
function essb_active_oninstall () {
	$instance = Boombox_Essb::get_instance();
	add_filter( 'boombox/essb/default_options', array( $instance, 'default_options' ), 10, 1 );
	$instance->install();
}

/**
 * Initialize single instance
 */
if ( boombox_plugin_management_service()->is_plugin_active( 'easy-social-share-buttons3/easy-social-share-buttons3.php' ) ) {
	Boombox_Essb::get_instance();
}