<?php
// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

/**
 * @var $component_classes  string  Component HTML classes
 * @var $component_id       string  Component unique id
 * @var $badges             array   All badges array ( earned + unearned )
 * @var $pagination         string  Pagination HTML
 * @version 1.0
 */
?>
<div class="<?php echo $component_classes; ?>">
	<ul class="achievements-wrapper">
		<?php
		foreach( $badges as $badge_data ) {

			$badge = mycred_get_badge( $badge_data['badge_id'], $badge_data['level'] );
			if ( $badge === false ) { continue; }

			$template_name = 'modules/buddypress-achievements/bp-component/templates/badge-item-' . $badge_data['type'] . '.php';
			$template_data = array(
				'badge'     => $badge,
				'level'     => $badge_data['level'],
				'progress'  => $badge_data['progress'],
                'type'      => $badge_data['type']
			);

			gfy_get_template_part( $template_name, $template_data );
		} ?>
	</ul>

	<?php if( $pagination ) { ?>
		<nav class="navigation pagination" role="navigation">
			<div class="nav-links"><?php echo $pagination; ?></div>
		</nav>
	<?php } ?>
</div>