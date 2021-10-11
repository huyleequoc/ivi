<?php
// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct script access allowed' );
}

/**
 * @var $filter_name    string                  Filter query var
 * @var $current_filter string                  Current filter
 * @var $filter_choices array<string,string>    Dropdown choices
 * @version 1.0
 */
?>
<li id="gfy-bp-history-filter-select" class="last">
	<form action="" method="get">
		<label for="gfy-bp-history-filter-by"><?php _e( 'Show', 'gamify' ) ?>:</label>
		<select id="gfy-bp-history-filter-by" name="<?php echo $filter_name; ?>" onchange="this.form.submit();">
			<?php foreach( $filter_choices as $val => $label ) { ?>
			<option value="<?php echo $val; ?>" <?php selected( $current_filter, $val ) ?>><?php echo $label; ?></option>
			<?php } ?>
		</select>
	</form>
</li>