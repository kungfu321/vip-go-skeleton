<div id="import_export_section_group" class="mts-opts-group-tab">
			<p>
				<a href="#" id="mts-opts-import-code-button" class="button-secondary">
					<?php esc_html_e( 'Import Code', 'cyprus' ); ?>
				</a>
				&nbsp;&nbsp;
				<a href="#" id="mts-opts-export-code-copy" class="button-secondary">
					<?php esc_html_e( 'Show Export Code', 'cyprus' ); ?>
				</a>
				&nbsp;&nbsp;
				<a href="<?php echo $url; ?>" id="mts-opts-export-code-download" class="button-secondary">
					<?php esc_html_e( 'Export as File', 'cyprus' ); ?>
				</a>
			</p>

			<div id="mts-opts-import-code-wrapper">

				<div class="mts-opts-section-desc">

					<p class="description" id="import-code-description">
						<?php esc_html_e( 'Insert your backup code below and hit Import to restore your site options from a backup . ', 'cyprus' ); ?>
					</p>

				</div>

				<div class="mts-opts-field-wrapper">
					<textarea id="import-code-value" name="cyprus_import_code" class="large-text" rows="8"></textarea>
					<br>
					<input type="submit" id="mts-opts-import" name="<?php echo cyprus()->settings->key; ?>[import]" class="button-primary" value="<?php esc_attr_e( 'Import', 'cyprus' ); ?>" data-import-confirm="<?php echo esc_html__( 'Are you sure you want to import options? All current options will be lost.', 'cyprus' ); ?>">
				</div>

			</div>

			<?php
				$backup_options = cyprus()->settings->raw;

				$backup_options['mts-opts-backup'] = '1';

				$encoded_options = wp_json_encode( $backup_options );

				$url = add_query_arg( array(
					'feed'   => 'mts_download_options',
					'secret' => md5( AUTH_KEY . SECURE_AUTH_KEY ),
					'action' => 'download_options',
				), site_url() );
			?>
			<div class="mts-opts-field-wrapper">
				<textarea class="large-text" id="mts-opts-export-code" rows="8"><?php print_r( $encoded_options ); ?></textarea>
			</div>

			<input type="text" class="large-text" id="mts-opts-export-link-value" value="<?php echo $url; ?>" />

		</div>
