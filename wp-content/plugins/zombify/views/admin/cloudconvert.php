<h2><?php esc_html_e( 'CloudConvert', 'zombify' ); ?></h2>

<form method="post" action="options.php">

    <?php wp_nonce_field('update-options'); ?>
    <?php settings_fields( 'zf-settings-group-cloudconvert' ); ?>

    <table class="form-table">
        <tbody>
            <tr>
                <th scope="row"><?php esc_html_e( 'Cloudconvert API key', 'zombify' ); ?></th>
                <td>
                    <input type="text" name="zombify_cloudconvert_api_key" value="<?php echo zf_get_option('zombify_cloudconvert_api_key'); ?>" class="regular-text">
                </td>
            </tr>
            <tr>
                <th scope="row"><?php esc_html_e( 'Storage', 'zombify' ); ?></th>
                <td>
                    <?php $storage = zf_get_option("zombify_media_storage", "local"); ?>
                    <select name="zombify_media_storage" class="regular-text">
                        <option value="local" <?php if( $storage == 'local' ) echo 'selected'; ?>><?php esc_html_e( 'Local', 'zombify' ); ?></option>
                        <option value="aws_s3" <?php if( $storage == 'aws_s3' ) echo 'selected'; ?>><?php esc_html_e( 'Amazon', 'zombify' ); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php esc_html_e( 'Amazon access key ID', 'zombify' ); ?></th>
                <td>
                    <input type="text" name="zombify_aws_s3_access_key_id" value="<?php echo zf_get_option('zombify_aws_s3_access_key_id'); ?>" class="regular-text">
                </td>
            </tr>
            <tr>
                <th scope="row"><?php esc_html_e( 'Amazon secret access key', 'zombify' ); ?></th>
                <td>
                    <input type="text" name="zombify_aws_s3_secret_access_key" value="<?php echo zf_get_option('zombify_aws_s3_secret_access_key'); ?>" class="regular-text">
                </td>
            </tr>
            <tr>
                <th scope="row"><?php esc_html_e( 'Amazon bucket name', 'zombify' ); ?></th>
                <td>
                    <input type="text" name="zombify_aws_s3_bucket_name" value="<?php echo zf_get_option('zombify_aws_s3_bucket_name'); ?>" class="regular-text">
                </td>
            </tr>

        </tbody>
    </table>

	<hr size="3" color="#000">

	<h2><?php esc_html_e( 'Social networks keys', 'zombify' ); ?></h2>

	<table class="form-table">
		<tbody>
			<tr>
				<th scope="row"><?php esc_html_e( 'Facebook/Instagram application ID', 'zombify' ); ?></th>
				<td>
					<input type="text" name="zombify_facebook_app_id" value="<?php echo zf_get_option('zombify_facebook_app_id'); ?>" class="regular-text">
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Facebook/Instagram application secret', 'zombify' ); ?></th>
				<td>
					<input type="text" name="zombify_facebook_app_secret" value="<?php echo zf_get_option('zombify_facebook_app_secret'); ?>" class="regular-text">
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Twitch application ID', 'zombify' ); ?></th>
				<td>
					<input type="text" name="zombify_twitch_app_id" value="<?php echo zf_get_option('zombify_twitch_app_id'); ?>" class="regular-text">
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Twitch application secret', 'zombify' ); ?></th>
				<td>
					<input type="text" name="zombify_twitch_app_secret" value="<?php echo zf_get_option('zombify_twitch_app_secret'); ?>" class="regular-text">
				</td>
			</tr>
		</tbody>
	</table>

    <input type="hidden" name="action" value="update" />
    <input type="hidden" name="page_options" value="zombify_cloudconvert_api_key,zombify_media_storage,zombify_aws_s3_access_key_id,zombify_aws_s3_secret_access_key,zombify_aws_s3_bucket_name" />

    <p class="submit">
        <input type="submit" class="button-primary" value="<?php esc_html_e('Save Changes', 'zombify'); ?>" />
    </p>

</form>