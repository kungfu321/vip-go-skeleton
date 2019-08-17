<?php
use MyWPBackup\Admin\Job;
use MyWPBackup\Admin\Table\Job as JobTable;
use MyWPBackup\Admin\Admin as Admin;

if ( ! defined( 'ABSPATH' ) ) { die; }
?>
<div class="page-backups wrap" id="my-wp-backup">

    <h2><?php esc_html_e( 'Jobs', 'my-wp-backup' ); ?> <a href="<?php esc_attr_e( \MyWPBackup\Admin\Admin::get_page_url( 'jobs', array( 'action' => 'new', 'tour' => isset( $_GET['tour'] ) && 'yes' === $_GET['tour'] ? 'yes' : null ) ) ); ?>" class="add-new-h2"><?php esc_html_e( 'Add New', 'my-wp-backup' ); ?></a></h2>

	<?php settings_errors(); ?>

	<?php if ( isset( $_GET['action'] ) && isset( $_GET['id'] ) && 'run' === $_GET['action'] ) : ?>

		<form action="<?php echo esc_attr( admin_url( 'admin-post.php' ) ); ?>" method="POST" accept-charset="utf-8">
			<?php $job = Job::get( absint( $_GET['id'] ) ); ?>
			<?php wp_nonce_field( 'my-wp-backup-run-job' ); ?>
			<input type="hidden" name="id" value="<?php echo esc_attr( $job['id'] ); ?>">
			<input type="hidden" name="action" value="MyWPBackup_run_job">
			<p><?php echo esc_html( sprintf( __( 'You are about to run a job named %s.', 'my-wp-backup' ), $job['job_name'] ) ); ?></p>
			<?php submit_button( 'Start', null, 'submit', false ); ?>
			<a class="button button-cancel" href="<?php echo esc_attr( Admin::get_page_url( 'jobs' ) ); ?>"><?php esc_html_e( 'Cancel', 'my-wp-backup' ); ?></a>
		</form>

	<?php elseif ( isset( $_GET['action'] ) && isset( $_GET['id'] ) && 'view' === $_GET['action'] ) : ?>

		<?php
		$job_attributes = Job::get( intval( $_GET['id'] ) );
		?>

		<label for="show-verbose"><input type="checkbox" value="yes" id="show-verbose"><?php esc_html_e( 'Verbose Output', 'my-wp-backup' ); ?></label>
		<div class="spinner is-active"></div>
		<pre id="backup-progress" class="terminal"><code><?php esc_html_e( 'Please wait...', 'my-wp-backup' ); ?></code></pre>

	<?php elseif ( isset( $_GET['action'] ) && isset( $_GET['id'] ) && 'delete' === $_GET['action'] ) : ?>

		<?php $job = Job::get( absint( $_GET['id'] ) ); ?>
		<form action="<?php echo esc_attr( admin_url( 'admin-post.php' ) ); ?>" method="POST" accept-charset="utf-8">
			<?php wp_nonce_field( 'MyWPBackup_delete_job' ); ?>
			<input type="hidden" name="id[]" value="<?php echo esc_attr( $job['id'] ); ?>">
			<input name="action" value="MyWPBackup_delete_job" type="hidden"/>
			<p><?php esc_html_e( 'You are about to delete the job:', 'my-wp-backup' ); ?></p>
			<ul class="ul-disc">
				<li><strong><?php echo esc_html( $job['job_name'] ); ?></strong></li>
			</ul>
			<p><?php esc_html_e( 'Are you sure you want to do this?', 'my-wp-backup' ); ?></p>
			<?php submit_button( 'Yes, Delete this job', 'delete', 'submit', false ); ?>
			<a class="button button-cancel" href="<?php echo esc_attr( \MyWPBackup\Admin\Admin::get_page_url( 'jobs' ) ); ?>"><?php esc_html_e( 'No, Return me to job list', 'my-wp-backup' ); ?></a>
		</form>


    <?php elseif ( isset( $_GET['action'] ) && in_array( $_GET['action'], array( 'new', 'edit' ) ) ) : // input var okay, sanitization okay ?>
		<?php $action = sanitize_text_field( $_GET['action'] ); // input var okay ?>
		<?php $id = isset( $_GET['id'] ) ? intval( $_GET['id'] ) : 0; // input var okay ?>

        <h2 class="nav-tab-wrapper">
            <a href="#section-general" class="nav-tab nav-tab-active"><?php esc_html_e( 'General', 'my-wp-backup' ); ?></a>
            <a href="#section-content" class="nav-tab"><?php esc_html_e( 'Content', 'my-wp-backup' ); ?></a>
            <a href="#section-schedule" class="nav-tab"><?php esc_html_e( 'Schedule', 'my-wp-backup' ); ?></a>
            <a href="#section-destination" class="nav-tab"><?php esc_html_e( 'Destination', 'my-wp-backup' ); ?></a>
            <a href="#section-report" class="nav-tab"><?php esc_html_e( 'Report', 'my-wp-backup' ); ?></a>
        </h2>

        <form method="post" action="<?php echo esc_attr( admin_url( 'admin-post.php' ) ); ?>" id="my-wp-backup-form">

	        <?php wp_nonce_field( 'MyWPBackup_job' ); ?>

	        <input name="action" value="MyWPBackup_job" type="hidden"/>
	        <input type="hidden" name="my-wp-backup-jobs[id]" value="<?php echo esc_attr( $id ); ?>">
	        <input type="hidden" name="my-wp-backup-jobs[action]" value="<?php echo esc_attr( $action ); ?>">
	        <input type="hidden" name="tour" value="<?php echo esc_attr( isset( $_GET['tour'] ) && 'yes' === $_GET['tour'] ? 'yes' : 'no' ); ?>">

	        <?php
	        if ( 'edit' === $action ) {
		        $job = Job::get( $id );
	        } else {
		        $job = Job::$form_defaults;
	        }

	        ?>

            <div id="section-general" class="nav-tab-content nav-tab-content-active">
                <table class="form-table">
                    <tbody>
                    <tr>
                        <th scope="row">
                            <label for="job-name"><?php esc_html_e( 'Job Name', 'my-wp-backup' ); ?></label>
                        </th>
                        <td>
                            <input type="text" placeholder="Name" name="my-wp-backup-jobs[job_name]" id="job-name" value="<?php echo esc_attr( $job['job_name'] ); ?>"><br>
	                        <span class="description"><?php esc_html_e( 'e.g. Daily or Weekly Backup', 'my-wp-backup' ); ?></span>
                        </td>
                    </tr>
                    </tbody>
                </table>

                <h3 class="title"><?php esc_html_e( 'Archive', 'my-wp-backup' ); ?></h3>
                <table class="form-table">
                    <tbody>
                    <tr>
                        <th scope="row"><label for="filename"><?php esc_html_e( 'Filename', 'my-wp-backup' ); ?></label></th>
                        <td>
                            <input type="text" name="my-wp-backup-jobs[filename]" id="filename" value="<?php echo esc_attr( $job['filename'] ); ?>"><br>
                            <span class="description"><?php echo esc_html( sprintf( __( 'Filename: %s', 'my-wp-backup' ), is_array( $job ) ? 'my-wp-backup_2015-05-17T03:53:29+00:00' : $job->do_filename() ) ); ?></span>
                        </td>
                    </tr>
                    <tr>
	                    <th scope="row"><label for="compression"><?php esc_html_e( 'Compression', 'my-wp-backup' ); ?></label></th>
	                    <td>
		                    <select name="my-wp-backup-jobs[compression]" id="compression">
			                    <?php wpb_select_options( Job::$compression_methods, $job['compression'] ); ?>
		                    </select>
	                    </td>
                    </tr>
                    <!--<tr>
                        <th scope="row"><label for="pass"><?php esc_html_e( 'Password-protect', 'my-wp-backup' ); ?></label></th>
                        <td>
                            <input id="pass" name="my-wp-backup-jobs[password]" type="password" value="<?php echo esc_attr( $job['password'] ); ?>"><br>
	                        <span class="description">Leave empty to not not set a password.</span>
                        </td>
                    </tr>-->
                    <tr>
	                    <th scope="row">
		                    <label for="differential"><?php esc_html_e( 'Differential Backups', 'my-wp-backup' ); ?></label>
	                    </th>
	                    <td>
		                    <label for="differential"><input name="my-wp-backup-jobs[differential]" id="differential" type="checkbox" value="1" <?php checked( '1', $job['differential'], true ); ?>> <?php esc_html_e( 'Enable Differential Backups', 'my-wp-backup' ); ?></label><br>
		                    <span class="description"><?php esc_html_e( 'By enabling Differential Backups, only the files that have changed from the last backup are backed up saving you from bandwidth and storage.', 'my-wp-backup' ); ?></span>
	                    </td>
                    </tr>
                    <tr data-show-only-if-checked="#differential" <?php echo '1' === $job['differential'] ? '' : 'style="display:none"'; ?>>
	                    <th scope="row"><label for="full-interval"><?php esc_html_e( 'Full Backups', 'my-wp-backup' ); ?></label></th>
	                    <td>
		                    <label for="full-interval"><?php printf( esc_html__( 'Create a full backup after every %s differential backups.', 'my-wp-backup' ), '<input type="number" id="full-interval" style="width:100px" name="my-wp-backup-jobs[full_interval]" value="' . esc_attr( $job['full_interval'] ) . '">' ); ?></label>
	                    </td>
                    </tr>
                    <tr>
	                    <th scope="row">
		                    <label for="delete-full"><?php esc_html_e( 'Safe Keeping', 'my-wp-backup' ); ?></label>
	                    </th>
	                    <td>
		                    <label for="delete-full"><?php printf( esc_html__( 'Only keep the last %s full backups.', 'my-wp-backup' ), '<input type="number" id="delete-full" style="width:100px" name="my-wp-backup-jobs[last_full]" value="' . esc_attr( $job['last_full'] ) . '">' ); ?></label><br>
		                    <label for="delete-differential" data-show-only-if-checked="#differential" <?php echo '1' === $job['differential'] ? '' : 'style="display:none"'; ?>><?php echo sprintf( esc_html__( 'Only keep the last %s differential backups.', 'my-wp-backup' ), '<input type="number" id="delete-differential" style="width:100px" name="my-wp-backup-jobs[last_differential]" value="' . esc_attr( $job['last_differential'] ) . '">' ); ?><br></label>
		                    <span class="description">Set to <code>0</code> to keep all backups.</span><br><br>
		                    <label for="delete-remote"><input name="my-wp-backup-jobs[delete_remote]" id="delete-remote" type="checkbox" value="1" <?php checked( '1', $job['delete_remote'], true ); ?>><?php _e( 'Also delete backups from remote destinations.', 'my-wp-backup' ); ?></label>
	                    </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="volsize"><?php esc_html_e( 'Split into volumes', 'my-wp-backup' ); ?></label>
                        </th>
                        <td>
                            <label for="volsize"><?php esc_html_e( 'Split the backup into', 'my-wp-backup' ); ?><input type="number" name="my-wp-backup-jobs[volsize]" id="volsize" style="width:100px" value="<?php echo esc_attr( $job['volsize'] ); ?>"><?php esc_html_e( 'Mb volumes.', 'my-wp-backup' ); ?></label>
	                        <br>
                            <span class="description"><?php esc_html_e( 'Splitting the backup into smaller volumes specially helps on a slow/unreliable network which might cause failures or interrupt a large data upload.', 'my-wp-backup' ); ?></span>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
	        <div id="section-schedule" class="nav-tab-content">
		        <table class="form-table">
			        <tbody>
			        <tr>
				        <th scope="row">
					        <label><?php esc_html_e( 'Run job', 'my-wp-backup' ); ?></label>
					        <p class="description" style="font-weight:normal"><?php esc_html_e( 'Cron/Interval backup is available in Pro Version', 'my-wp-backup' ); ?></p>
				        </th>
				        <td>
					        <label for="schedule-manual"><input type="radio" value="manual" name="my-wp-backup-jobs[schedule_type]" id="schedule-manual" <?php echo 'manual' === $job['schedule_type'] ? 'checked' : ''; ?>><?php esc_html_e( 'Manual', 'my-wp-backup' ); ?></label><br/>
					        <label for="schedule-cron"><input type="radio" value="cron" name="my-wp-backup-jobs[schedule_type]" id="schedule-cron" <?php checked( 'cron', $job['schedule_type'], true ); ?>><?php esc_html_e( 'WP Cron', 'my-wp-backup' ); ?></label><br/>
					        <span class="description">
						        <em><?php esc_html_e( 'Manual', 'my-wp-backup' ); ?></em> - <?php esc_html_e( 'start the backup job on your own accord', 'my-wp-backup' ); ?><br>
						        <em><?php esc_html_e( 'WP Cron', 'my-wp-backup' ); ?></em> - <?php esc_html_e( 'start the backup job on time intervals', 'my-wp-backup' ); ?>
					        </span>
				        </td>
			        </tr>
			        </tbody>
		        </table>
		        <table class="form-table form-cron" style="<?php echo 'manual' === $job['schedule_type'] ? 'display:none' : ''; ?>">
			        <tbody>
			        <tr>
				        <th scope="row">
					        <?php esc_html_e( 'CRON Mode', 'my-wp-backup' ); ?>
				        </th>
				        <td>
					        <input type="radio" value="simple" name="my-wp-backup-jobs[cron_type]" id="schedule-simple" <?php checked( 'simple', $job['cron_type'], true ); ?>><label for="schedule-simple"><?php esc_html_e( 'Simple', 'my-wp-backup' ); ?></label>
					        <div class="schedule-simple schedule-option">
						        <select id="schedule-simple" name="my-wp-backup-jobs[schedule_simple]">
							        <?php wpb_select_options( Job::$simple_scheds, $job['schedule_simple'] ); ?>
						        </select>
					        </div>
					        <br>
					        <input type="radio" name="my-wp-backup-jobs[cron_type]" id="schedule-advanced" value="advanced" <?php echo checked( 'advanced', $job['cron_type'], true ); ?>><label
						        for="schedule-advanced"><?php esc_html_e( 'Advanced', 'my-wp-backup' ); ?></label>
					        <div class="schedule-advanced schedule-option">
						        <input type="text" name="my-wp-backup-jobs[schedule_advanced]" id="job-name" value="<?php echo isset( $job['schedule_advanced'] ) ? esc_attr( $job['schedule_advanced'] ) : ''; ?>"><br>
						        <span class="description"><?php esc_html_e( 'Specify a cron pattern.', 'my-wp-backup' ); ?></span>
					        </div>
				        </td>
			        </tr>
			        </tbody>
		        </table>
	        </div>
	        <div id="section-content" class="nav-tab-content">
		        <h3 class="title"><?php esc_html_e( 'Files', 'my-wp-backup' ); ?></h3>
		        <table class="form-table">
			        <tbody>
			        <tr>
				        <th scope="row"><label for="backup-files"><?php esc_html_e( 'Backup Files', 'my-wp-backup' ); ?></label></th>
				        <td><input type="checkbox" id="backup-files" name="my-wp-backup-jobs[backup_files]" value="1" <?php checked( '1', $job['backup_files'], true ); ?>><label
						        for="backup-files"><?php esc_html_e( 'Enable', 'my-wp-backup' ); ?></label></td>
			        </tr>
			        <tr>
				        <th scope="row"><label for="backup-uploads"><?php esc_html_e( 'Backup Uploads Dir', 'my-wp-backup' ); ?></label></th>
				        <td>
					        <input type="checkbox" id="backup-uploads" name="my-wp-backup-jobs[backup_uploads]" value="1" <?php checked( '1', $job['backup_uploads'], true ); ?>><label
						        for="backup-uploads"><?php esc_html_e( 'Enable', 'my-wp-backup' ); ?></label><br>
					        <p class="description"><?php esc_html_e( 'Note: Will not work if "Backup Files" is not checked.', 'my-wp-backup' ); ?></p>
				        </td>
			        </tr>
			        <tr>
				        <th scope="row"><label for="enable-exclude"><?php esc_html_e( 'Exclude File Filters', 'my-wp-backup' ); ?></label></th>
				        <td>
					        <input type="checkbox" class="enable-exclude" name="my-wp-backup-jobs[exclude_files]" id="enable-exclude-files" value="1" <?php checked( '1', $job['exclude_files'], true ); ?>><label for="enable-exclude-files"><?php esc_html_e( 'Enable', 'my-wp-backup' ); ?></label>
					        <div class="exclude-filters">
						        <p class="description"><?php esc_html_e( 'Exclude files whose path matches any of the following glob patterns (one per line):', 'my-wp-backup' ); ?></p><br>
						        <textarea id="file-filters" rows="8" cols="50" name="my-wp-backup-jobs[file_filters]"><?php echo isset( $job['file_filters'] ) ? esc_textarea( implode( "\n", $job['file_filters'] ) ) : ''; ?></textarea>
						        <a class="button thickbox" href="#TB_inline?width=1200&height=650&inlineId=my-wp-backup-test-filters" id="file-exclude-link">Test</a>
					        </div>
				        </td>
			        </tr>
			        </tbody>
		        </table>
		        <h3 class="title"><?php esc_html_e( 'Database', 'my-wp-backup' ); ?></h3>
		        <table class="form-table">
			        <tbody>
			        <tr>
				        <th scope="row"><label for="backup-db"><?php esc_html_e( 'Export Database', 'my-wp-backup' ); ?></label></th>
				        <td>
					        <input type="checkbox" id="backup-db" value="1" name="my-wp-backup-jobs[export_db]" <?php checked( '1', $job['export_db'], true ); ?>><label for="backup-db"><?php esc_html_e( 'Enable', 'my-wp-backup' ); ?></label><br>
				            <p class="description"><?php esc_html_e( 'Include a database export file to the backup.', 'my-wp-backup' ); ?></p>
				        </td>
			        </tr>
			        <tr>
				        <th scope="row"><label for="enable-exclude"><?php esc_html_e( 'Exclude Table Filters', 'my-wp-backup' ); ?></label></th>
				        <td>
					        <input type="checkbox" class="enable-exclude" name="my-wp-backup-jobs[exclude_tables]" id="enable-exclude-tables" value="1" <?php checked( '1', $job['exclude_tables'], true ); ?>><label for="enable-exclude-tables"><?php esc_html_e( 'Enable', 'my-wp-backup' ); ?></label>
					        <div class="exclude-filters">
						        <p class="description"><?php esc_html_e( 'Exclude the following database tables:', 'my-wp-backup' ); ?></p><br>
						        <?php foreach ( \MyWPBackup\Admin\Admin::get_tables() as $table ) : ?>
									<input name="my-wp-backup-jobs[table_filters][]" type="checkbox" id="table_<?php echo esc_attr( $table ); ?>" value="<?php echo esc_attr( $table ); ?>" <?php checked( true,  in_array( $table, $job['table_filters'] ), true ); ?>><label for="table_<?php echo esc_attr( $table ); ?>"><?php echo esc_html( $table ); ?></label>
								<?php endforeach; ?>
					        </div>
				        </td>
			        </tr>
			        </tbody>
		        </table>
	        </div>
            <div id="section-destination" class="nav-tab-content">
                <table class="form-table">
                    <tbody>
                    <tr>
	                    <th scope="row"><label for="dest-local"><?php esc_html_e( 'Local Copy', 'my-wp-backup' ); ?></label></th>
	                    <td>
		                    <input type="checkbox" id="dest-local" value="1" name="my-wp-backup-jobs[delete_local]" <?php checked( '1', $job['delete_local'], true ); ?>>
		                    <label for="dest-local"><?php esc_html_e( 'Delete the local copy of the archive when upload completes.', 'my-wp-backup' ); ?></label><br>
		                    <span class="description"><?php esc_html_e( 'You need to select atleast 1 destination below for this option to work.', 'my-wp-backup' ); ?></span>
	                    </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="destination"><?php esc_html_e( 'Backup Destination(Optional)', 'my-wp-backup' ); ?></label>
	                        <p class="description" style="font-weight:normal"><?php esc_html_e( 'If you want to save the backup on the same server then, you don\'t need to choose any listed service.', 'my-wp-backup' ); ?></p>
                        </th>

                        <td>
                            <select id="destination" name="my-wp-backup-jobs[destination][]" multiple size="<?php echo esc_attr( count( Job::$destinations ) + 1 ); ?>">
	                            <?php wpb_select_options( Job::$destinations, isset( $job['destination'] ) ? $job['destination'] : array() ); ?>
                            </select>
                            <br>
                            <span class="description"><?php esc_html_e( 'Press Ctrl to select more than 1 destination.', 'my-wp-backup' ); ?></span>
                        </td>
                    </tr>
                    </tbody>
                </table>

	            <div class="select-section section-ftp <?php echo in_array( 'ftp', $job['destination'] ) ? 'section-active' : ''; ?>">
		            <h3 class="title"><?php esc_html_e( 'FTP Details', 'my-wp-backup' ); ?></h3>
		            <table class="form-table">
			            <tbody>
			            <tr>
				            <th scope="row"><label for="ftp-host"><?php esc_html_e( 'Host', 'my-wp-backup' ); ?></label></th>
				            <td><input id="ftp-host" name="my-wp-backup-jobs[destination_options][ftp][host]" value="<?php echo esc_attr( $job['destination_options']['ftp']['host'] ); ?>" type="text"/></td>
			            </tr>
			            <tr>
				            <th scope="row"><label for="ftp-user"><?php esc_html_e( 'Username', 'my-wp-backup' ); ?></label></th>
				            <td><input id="ftp-user" name="my-wp-backup-jobs[destination_options][ftp][username]" value="<?php echo esc_attr( $job['destination_options']['ftp']['username'] ); ?>" type="text"/></td>
			            </tr>
			            <tr>
				            <th scope="row"><label for="ftp-password"><?php esc_html_e( 'Password', 'my-wp-backup' ); ?></label></th>
				            <td><input id="ftp-password" name="my-wp-backup-jobs[destination_options][ftp][password]" value="<?php echo esc_attr( $job['destination_options']['ftp']['password'] ); ?>" type="password"/></td>
			            </tr>
			            <tr>
				            <th scope="row"><label for="ftp-ssl"><?php esc_html_e( 'Explicit SSL', 'my-wp-backup' ); ?></label></th>
				            <td><input id="ftp-ssl" name="my-wp-backup-jobs[destination_options][ftp][ssl]" value="1" type="checkbox" <?php checked( '1', $job['destination_options']['ftp']['ssl'], true ); ?>/></td>
			            </tr>
			            <tr>
				            <th scope="row"><label for="ftp-port"><?php esc_html_e( 'Port', 'my-wp-backup' ); ?></label></th>
				            <td><input id="ftp-port" name="my-wp-backup-jobs[destination_options][ftp][port]" type="number" value="<?php echo esc_attr( $job['destination_options']['ftp']['port'] ); ?>"/></td>
			            </tr>
			            <tr>
				            <th scope="row"><label for="ftp-folder"><?php esc_html_e( 'Root Folder', 'my-wp-backup' ); ?></label></th>
				            <td>
					            <input id="ftp-folder" name="my-wp-backup-jobs[destination_options][ftp][folder]" value="<?php echo esc_attr( $job['destination_options']['ftp']['folder'] ); ?>" type="text"/>
				            </td>
			            </tr>
			            </tbody>
		            </table>
	            </div>

	            <div class="select-section section-googledrive <?php echo in_array( 'googledrive', $job['destination'] ) ? 'section-active' : ''; ?>">
		            <h3 class="title"><?php esc_html_e( 'Google Drive Details', 'my-wp-backup' ); ?></h3>
		            <table class="form-table">
			            <tbody>
			            <tr>
				            <th scope="row"><label for="drive-token"><?php esc_html_e( 'Access Token', 'my-wp-backup' ); ?></label></th>
				            <td>
					            <input value="<?php echo esc_attr( $job['destination_options']['googledrive']['token'] ); ?>" name="my-wp-backup-jobs[destination_options][googledrive][token]" id="drive-token" type="text"/>
					            <a target="_blank" id="drive-token-link" href="#TB_inline?width=600&height=140&inlineId=my-wp-backup-drive-content" class="thickbox button"><?php esc_html_e( 'Connect Google Drive Account', 'my-wp-backup' ); ?></a>
				                <input value="<?php echo filter_var( $job['destination_options']['googledrive']['token_json'], FILTER_SANITIZE_SPECIAL_CHARS ); ?>" name="my-wp-backup-jobs[destination_options][googledrive][token_json]" id="drive-token-json" type="hidden"/>
				            </td>
			            </tr>
			            <!--<tr>
				            <th scope="row"><label for="drive-folder">Google Drive Root Folder</label></th>
				            <td><input name="my-wp-backup-jobs[destination_options][googledrive][folder]" id="drive-folder" type="text" value="<?php /*echo esc_attr( $job['destination_options']['googledrive']['folder'] ); */?>"/></td>
			            </tr>-->
			            </tbody>
		            </table>
	            </div>

	            <div class="select-section section-dropbox <?php echo in_array( 'dropbox', $job['destination'] ) ? 'section-active' : ''; ?>">
		            <h3 class="title"><?php esc_html_e( 'Dropbox Details', 'my-wp-backup' ); ?></h3>
		            <table class="form-table">
			            <tbody>
			            <tr>
				            <th scope="row"><label for="dropbox-token"><?php esc_html_e( 'Access Token', 'my-wp-backup' ); ?></label></th>
				            <td><input name="my-wp-backup-jobs[destination_options][dropbox][token]" value="<?php echo esc_attr( $job['destination_options']['dropbox']['token'] ); ?>" id="dropbox-token" type="text"/> <a target="_blank" id="dropbox-token-link" href="#TB_inline?width=600&height=140&inlineId=my-wp-backup-dropbox-content" class="thickbox button"><?php esc_html_e( 'Connect Dropbox Account', 'my-wp-backup' ); ?></a></td>
			            </tr>
			            <!--<tr>
				            <th scope="row"><label for="dropbox-folder">Dropbox Root Folder</label></th>
				            <td><input name="my-wp-backup-jobs[destination_options][dropbox][folder]" id="dropbox-folder" type="text" value="<?php echo esc_attr( $job['destination_options']['dropbox']['folder'] ); ?>"/></td>
			            </tr>-->
			            </tbody>
		            </table>
	            </div>

	            <div class="select-section section-onedrive <?php echo in_array( 'onedrive', $job['destination'] ) ? 'section-active' : ''; ?>">
		            <h3 class="title"><?php esc_html_e( 'Onedrive Details', 'my-wp-backup' ); ?></h3>
		            <table class="form-table">
			            <tbody>
			            <tr>
				            <th scope="row"><label for="onedrive-token"><?php esc_html_e( 'Access Token', 'my-wp-backup' ); ?></label></th>
				            <td>
					            <input value="<?php echo esc_attr( $job['destination_options']['onedrive']['token'] ); ?>" name="my-wp-backup-jobs[destination_options][onedrive][token]" id="onedrive-token" type="text"/>
					            <a target="_blank" id="onedrive-token-link" href="#TB_inline?width=600&height=140&inlineId=my-wp-backup-onedrive-content" class="thickbox button"><?php esc_html_e( 'Connect OneDrive Account', 'my-wp-backup' ); ?></a>
					            <input value="<?php echo filter_var( $job['destination_options']['onedrive']['token_json'], FILTER_SANITIZE_SPECIAL_CHARS ); ?>" name="my-wp-backup-jobs[destination_options][onedrive][token_json]" id="onedrive-token-json" type="hidden"/>
				            </td>
			            </tr>
			            </tbody>
		            </table>
	            </div>

	            <div class="select-section section-s3 <?php echo in_array( 's3', $job['destination'] ) ? 'section-active' : ''; ?>">
		            <h3 class="title"><?php esc_html_e( 'Amazon S3 Details', 'wp-backup' ); ?></h3>
		            <table class="form-table">
			            <tbody>
			            <tr>
				            <th scope="row"><label for="s3-key"><?php esc_html_e( 'Access Key', 'wp-backup' ); ?></label></th>
				            <td><input id="s3-key" name="my-wp-backup-jobs[destination_options][s3][access_key]" value="<?php echo esc_attr( $job['destination_options']['s3']['access_key'] ); ?>" type="text"/></td>
			            </tr>
			            <tr>
				            <th scope="row"><label for="s3-secret"><?php esc_html_e( 'Secret Key', 'wp-backup' ); ?></label></th>
				            <td><input id="s3-secret" name="my-wp-backup-jobs[destination_options][s3][secret_key]" value="<?php echo esc_attr( $job['destination_options']['s3']['secret_key'] ); ?>" type="text"/></td>
			            </tr>
			            <tr>
				            <th scope="row"><label for="s3-region"><?php esc_html_e( 'Secret Key', 'wp-backup' ); ?></label></th>
				            <td>
					            <select name="my-wp-backup-jobs[destination_options][s3][region]" id="s3-region">
						            <?php wpb_select_options( Job::$s3_regions, $job['destination_options']['s3']['region'] ); ?>
					            </select>
				            </td>
			            </tr>
			            <tr>
				            <th scope="row"><label for="s3-bucket"><?php esc_html_e( 'Bucket Name', 'wp-backup' ); ?></label></th>
				            <td><input id="s3-bucket" name="my-wp-backup-jobs[destination_options][s3][bucket]" value="<?php echo esc_attr( $job['destination_options']['s3']['bucket'] ); ?>" type="text"/></td>
			            </tr>
			            <!--<tr>
							<th scope="row"><label for="s3-folder">Bucket Root Folder</label></th>
							<td><input id="s3-folder" name="wp-backup-jobs[destination_options][s3][folder]" value="<?php /*echo esc_attr( $job['destination_options']['s3']['folder'] ); */?>" type="text"/></td>
						</tr>-->
			            </tbody>
		            </table>
	            </div>

	            <div class="select-section section-sftp <?php echo in_array( 'sftp', $job['destination'] ) ? 'section-active' : ''; ?>">
		            <h3 class="title"><?php esc_html_e( 'SFTP Details', 'my-wp-backup' ); ?></h3>
		            <table class="form-table">
			            <tbody>
			            <tr>
				            <th scope="row"><label for="sftp-host"><?php esc_html_e( 'Host', 'my-wp-backup' ); ?></label></th>
				            <td><input id="sftp-host" name="my-wp-backup-jobs[destination_options][sftp][host]" value="<?php echo esc_attr( $job['destination_options']['sftp']['host'] ); ?>" type="text"/></td>
			            </tr>
			            <tr>
				            <th scope="row"><label for="sftp-user"><?php esc_html_e( 'Username', 'my-wp-backup' ); ?></label></th>
				            <td><input id="sftp-user" name="my-wp-backup-jobs[destination_options][sftp][username]" value="<?php echo esc_attr( $job['destination_options']['sftp']['username'] ); ?>" type="text"/></td>
			            </tr>
			            <tr>
				            <th scope="row"><label for="sftp-password"><?php esc_html_e( 'Password/Passphrase', 'my-wp-backup' ); ?></label></th>
				            <td>
					            <input id="sftp-password" name="my-wp-backup-jobs[destination_options][sftp][password]" value="<?php echo esc_attr( $job['destination_options']['sftp']['password'] ); ?>" type="password"/><br>
					            <span class="description"><?php esc_html_e( 'If a private key is provided below, this value will be used to read the private key incase a passphrase is needed.', 'my-wp-backup' ); ?></span>
				            </td>
			            </tr>
			            <tr>
				            <th scope="row"><label for="sftp-key"><?php esc_html_e( 'Private Key', 'my-wp-backup' ); ?></label></th>
				            <td><textarea id="sftp-key" name="my-wp-backup-jobs[destination_options][sftp][private_key]" cols="40" rows="10"><?php echo esc_textarea( $job['destination_options']['sftp']['private_key'] ); ?></textarea></td>
			            </tr>
			            <tr>
				            <th scope="row"><label for="sftp-port"><?php esc_html_e( 'Port', 'my-wp-backup' ); ?></label></th>
				            <td><input id="sftp-port" name="my-wp-backup-jobs[destination_options][sftp][port]" value="<?php echo esc_attr( $job['destination_options']['sftp']['port'] ); ?>" type="number"/></td>
			            </tr>
			            <tr>
				            <th scope="row"><label for="sftp-folder"><?php esc_html_e( 'Root Folder', 'my-wp-backup' ); ?></label></th>
				            <td><input id="sftp-folder" name="my-wp-backup-jobs[destination_options][sftp][folder]" value="<?php echo esc_attr( $job['destination_options']['sftp']['folder'] ); ?>" type="text"/></td>
			            </tr>
			            </tbody>
		            </table>
	            </div>

	            <div class="select-section section-rackspace <?php echo in_array( 'rackspace', $job['destination'] ) ? 'section-active' : ''; ?>">
		            <h3 class="title"><?php esc_html_e( 'Rackspace Cloud Files Details', 'my-wp-backup' ); ?></h3>
		            <table class="form-table">
			            <tbody>
			            <tr>
				            <th scope="row"><label for="rs-username"><?php esc_html_e( 'Username', 'my-wp-backup' ); ?></label></th>
				            <td>
					            <input id="rs-username" name="my-wp-backup-jobs[destination_options][rackspace][username]" value="<?php echo esc_attr( $job['destination_options']['rackspace']['username'] ); ?>" type="text"/><br>
					            <span class="description"><a target="_blank" href="https://mycloud.rackspace.com/cloud/968294/account#settings">Rackspace Account Page</a></span>
				            </td>
			            </tr>
			            <tr>
				            <th scope="row"><label for="rs-apikey"><?php esc_html_e( 'API Key', 'my-wp-backup' ); ?></label></th>
				            <td><input id="rs-apikey" name="my-wp-backup-jobs[destination_options][rackspace][apikey]" value="<?php echo esc_attr( $job['destination_options']['rackspace']['apikey'] ); ?>" type="text"/></td>
			            </tr>
			            <tr>
				            <th scope="row"><label for="rs-region"><?php esc_html_e( 'Region', 'my-wp-backup' ); ?></label></th>
				            <td>
					            <select name="my-wp-backup-jobs[destination_options][rackspace][region]" id="rs-region">
						            <?php wpb_select_options( Job::$rackspace_regions, $job['destination_options']['rackspace']['region'] ); ?>
					            </select>
				            </td>
			            </tr>
			            <tr>
				            <th scope="row"><label for="rs-container"><?php esc_html_e( 'Container Name', 'my-wp-backup' ); ?></label></th>
				            <td><input id="rs-container" name="my-wp-backup-jobs[destination_options][rackspace][container]" value="<?php echo esc_attr( $job['destination_options']['rackspace']['container'] ); ?>" type="text"/></td>
			            </tr>
			            </tbody>
		            </table>
	            </div>

	            <div class="select-section section-glacier <?php echo in_array( 'glacier', $job['destination'] ) ? 'section-active' : ''; ?>">
		            <h3 class="title"><?php esc_html_e( 'Amazon Glacier Details', 'my-wp-backup' ); ?></h3>
		            <table class="form-table">
			            <tbody>
			            <tr>
				            <th scope="row"><label for="glacier-key"><?php esc_html_e( 'Access Key', 'my-wp-backup' ); ?></label></th>
				            <td><input id="glacier-key" name="my-wp-backup-jobs[destination_options][glacier][access_key]" value="<?php echo esc_attr( $job['destination_options']['glacier']['access_key'] ); ?>" type="text"/></td>
			            </tr>
			            <tr>
				            <th scope="row"><label for="glacier-secret"><?php esc_html_e( 'Secret Key', 'my-wp-backup' ); ?></label></th>
				            <td><input id="glacier-secret" name="my-wp-backup-jobs[destination_options][glacier][secret_key]" value="<?php echo esc_attr( $job['destination_options']['glacier']['secret_key'] ); ?>" type="text"/></td>
			            </tr>
			            <tr>
				            <th scope="row"><label for="glacier-region"><?php esc_html_e( 'Secret Key', 'my-wp-backup' ); ?></label></th>
				            <td>
					            <select name="my-wp-backup-jobs[destination_options][glacier][region]" id="glacier-region">
						            <?php wpb_select_options( Job::$glacier_regions, $job['destination_options']['glacier']['region'] ); ?>
					            </select>
				            </td>
			            </tr>
			            <tr>
				            <th scope="row"><label for="glacier-vault"><?php esc_html_e( 'Vault Name', 'my-wp-backup' ); ?></label></th>
				            <td><input id="glacier-vault" name="my-wp-backup-jobs[destination_options][glacier][vault]" value="<?php echo esc_attr( $job['destination_options']['glacier']['vault'] ); ?>" type="text"/></td>
			            </tr>
			            </tbody>
		            </table>
	            </div>

            </div>
            <div id="section-report" class="nav-tab-content">
                <table class="form-table">
                    <tbody>
                    <tr>
                        <th scope="row"><label for="rep_destination"><?php esc_html_e( 'Reporters (optional)', 'my-wp-backup' ); ?></label></th>
                        <td>
                            <select id="rep_destination" name="my-wp-backup-jobs[rep_destination][]" multiple size="<?php echo esc_attr( count( Job::$reporters ) + 1 ); ?>">
	                            <?php wpb_select_options( Job::$reporters, $job['rep_destination'] ); ?>
                            </select>
                            <br>
                            <span class="description"><?php esc_html_e( 'Press Ctrl to select more than 1 reporter.', 'my-wp-backup' ); ?></span>
                        </td>
                    </tr>
                    </tbody>
                </table>

	            <div class="select-section section-mail <?php echo in_array( 'mail', $job['rep_destination'] ) ? 'section-active' : ''; ?>">
		            <h3 class="title"><?php esc_html_e( 'E-Mail Details', 'my-wp-backup' ); ?></h3>
		            <table class="form-table">
			            <tr>
				            <th scope="row"><label for="mail-from"><?php esc_html_e( 'Sender Address', 'my-wp-backup' ); ?></label></th>
				            <td><input id="mail-from" name="my-wp-backup-jobs[reporter_options][mail][from]" value="<?php echo esc_attr( $job['reporter_options']['mail']['from'] ); ?>" type="email"/></td>
			            </tr>
			            <tr>
				            <th scope="row"><label for="mail-name"><?php esc_html_e( 'Sender Name', 'my-wp-backup' ); ?></label></th>
				            <td><input id="mail-name" name="my-wp-backup-jobs[reporter_options][mail][name]" value="<?php echo esc_attr( $job['reporter_options']['mail']['name'] ); ?>" type="text"/></td>
			            </tr>
			            <tr>
				            <th scope="row"><label for="mail-address"><?php esc_html_e( 'Recipient Address', 'my-wp-backup' ); ?></label></th>
				            <td><input id="mail-address" name="my-wp-backup-jobs[reporter_options][mail][address]" value="<?php echo esc_attr( $job['reporter_options']['mail']['address'] ); ?>" type="email"/></td>
			            </tr>
			            <tr>
				            <th scope="row"><label for="mail-title"><?php esc_html_e( 'Subject', 'my-wp-backup' ); ?></label></th>
				            <td><input id="mail-title" name="my-wp-backup-jobs[reporter_options][mail][title]" value="<?php echo esc_attr( $job['reporter_options']['mail']['title'] ); ?>" type="text"/></td>
			            </tr>
			            <tr>
				            <th scope="row"><label for="mail-message"><?php esc_html_e( 'Body', 'my-wp-backup' ); ?></label></th>
				            <td><textarea name="my-wp-backup-jobs[reporter_options][mail][message]" id="mail-message" cols="40" rows="10"><?php echo esc_textarea( $job['reporter_options']['mail']['message'] ); ?></textarea></td>
			            </tr>
			            <tr>
				            <th scope="row"><label for="mail-attach"><?php esc_html_e( 'Log File', 'my-wp-backup' ); ?></label></th>
				            <td><input id="mail-attach" name="my-wp-backup-jobs[reporter_options][mail][attach]" value="1" type="checkbox" <?php echo checked( '1', $job['reporter_options']['mail']['attach'], true ); ?>/><label for="mail-attach">Attach log file</label></td>
			            </tr>
			            <tr>
				            <th scope="row"><label for="mail-method"><?php esc_html_e( 'Method', 'my-wp-backup' ); ?></label></th>
				            <td>
					            <select name="my-wp-backup-jobs[reporter_options][mail][method]" id="mail-method">
									<?php wpb_select_options( Job::$email_methods, $job['reporter_options']['mail']['method'] ); ?>
					            </select>
				            </td>
			            </tr>
		            </table>
		            <div class="select-section section-smtp <?php echo 'smtp' === $job['reporter_options']['mail']['method'] ? 'section-active' : ''; ?>">
			            <h4 class="title"><?php esc_html_e( 'SMTP Details', 'my-wp-backup' ); ?></h4>
						<table class="form-table">
							<tr>
								<th scope="row"><label for="smtp-server"><?php esc_html_e( 'Server', 'my-wp-backup' ); ?></label></th>
								<td><input id="smtp-server" name="my-wp-backup-jobs[reporter_options][mail][smtp_server]" value="<?php echo esc_attr( $job['reporter_options']['mail']['smtp_server'] ); ?>" type="text"/></td>
							</tr>
							<tr>
								<th scope="row"><label for="smtp-port"><?php esc_html_e( 'Port', 'my-wp-backup' ); ?></label></th>
								<td><input id="smtp-port" name="my-wp-backup-jobs[reporter_options][mail][smtp_port]" value="<?php echo esc_attr( $job['reporter_options']['mail']['smtp_port'] ); ?>" type="text"/></td>
							</tr>
							<tr>
								<th scope="row"><label for="smtp-security"><?php esc_html_e( 'Protocol', 'my-wp-backup' ); ?></label></th>
								<td>
									<select id="smtp-security" name="my-wp-backup-jobs[reporter_options][mail][smtp_protocol]">
										<?php wpb_select_options( array(
											'none' => __( 'None', 'my-wp-backup' ),
											'tls' => 'TLS',
											'ssl' => 'SSL',
										), $job['reporter_options']['mail']['smtp_protocol'] ); ?>
									</select>
								</td>
							</tr>
							<tr>
								<th scope="row"><label for="smtp-username"><?php esc_html_e( 'Username', 'my-wp-backup' ); ?></label></th>
								<td><input id="smtp-username" name="my-wp-backup-jobs[reporter_options][mail][smtp_username]" value="<?php echo esc_attr( $job['reporter_options']['mail']['smtp_username'] ); ?>" type="text"/></td>
							</tr>
							<tr>
								<th scope="row"><label for="smtp-password"><?php esc_html_e( 'Password', 'my-wp-backup' ); ?></label></th>
								<td><input id="smtp-password" name="my-wp-backup-jobs[reporter_options][mail][smtp_password]" value="<?php echo esc_attr( $job['reporter_options']['mail']['smtp_password'] ); ?>" type="password"/></td>
							</tr>
						</table>
		            </div>
	            </div>

	            <div class="select-section section-push <?php echo in_array( 'push', $job['rep_destination'] ) ? 'section-active' : ''; ?>">
		            <h3 class="title"><?php esc_html_e( 'Push Notification (via PushBullet) Details', 'my-wp-backup' ); ?></h3>
		            <table class="form-table">
			            <tr>
				            <th scope="row"><label for="push-token"><?php esc_html_e( 'Access Token', 'my-wp-backup' ); ?></label></th>
				            <td><input name="my-wp-backup-jobs[reporter_options][push][token]" value="<?php echo esc_attr( $job['reporter_options']['push']['token'] ); ?>" id="push-token" type="text"/> <a target="_blank" id="push-token-link" href="#TB_inline?width=600&height=140&inlineId=my-wp-backup-push-content" class="thickbox button"><?php esc_html_e( 'Connect PushBullet Account', 'my-wp-backup' ); ?></a></td>
			            </tr>
			            <tr>
				            <th scope="row"><label for="push-message"><?php esc_html_e( 'Message', 'my-wp-backup' ); ?></label></th>
				            <td><textarea name="my-wp-backup-jobs[reporter_options][push][message]" id="push-message" cols="40" rows="10"><?php echo esc_textarea( $job['reporter_options']['push']['message'] ); ?></textarea></td>
			            </tr>
		            </table>
	            </div>

	            <div class="select-section section-sms <?php echo in_array( 'sms', $job['rep_destination'] ) ? 'section-active' : ''; ?>">
		            <h3 class="title"><?php esc_html_e( 'SMS Details', 'my-wp-backup' ); ?></h3>
		            <table class="form-table">
			            <tr>
				            <th scope="row"><label for="sms-provider"><?php esc_html_e( 'SMS Provider', 'my-wp-backup' ); ?></label></th>
				            <td>
					            <select name="my-wp-backup-jobs[reporter_options][sms][provider]" id="sms-provider">
						            <?php wpb_select_options( \MyWPBackup\Admin\Job::$sms_providers, $job['reporter_options']['sms']['provider'] ); ?>
					            </select>
				            </td>
			            </tr>
			            <tr>
				            <th scope="row"><label for="twilio-sid"><?php esc_html_e( 'SID', 'my-wp-backup' ); ?></label></th>
				            <td>
					            <input name="my-wp-backup-jobs[reporter_options][sms][twilio_sid]" value="<?php echo esc_attr( $job['reporter_options']['sms']['twilio_sid'] ); ?>" id="twilio-sid" type="text"/><br>
					            <span class="description"><a target="_blank" href="https://www.twilio.com/user/account/settings">Visit accounts settings</a> page</span>
				            </td>
			            </tr>
			            <tr>
				            <th scope="row"><label for="twilio-token"><?php esc_html_e( 'Auth Token', 'my-wp-backup' ); ?></label></th>
				            <td><input name="my-wp-backup-jobs[reporter_options][sms][twilio_token]" value="<?php echo esc_attr( $job['reporter_options']['sms']['twilio_token'] ); ?>" id="twilio-token" type="text"/></td>
			            </tr>
			            <tr>
				            <th scope="row"><label for="twilio-from"><?php esc_html_e( 'From', 'my-wp-backup' ); ?></label></th>
				            <td><input placeholder="+xx xxx xxx xxxx" name="my-wp-backup-jobs[reporter_options][sms][twilio_from]" value="<?php echo esc_attr( $job['reporter_options']['sms']['twilio_from'] ); ?>" id="twilio-from" type="text"/></td>
			            </tr>
			            <tr>
				            <th scope="row"><label for="twilio-to"><?php esc_html_e( 'To', 'my-wp-backup' ); ?></label></th>
				            <td><input placeholder="+xx xxx xxx xxxx" name="my-wp-backup-jobs[reporter_options][sms][twilio_to]" value="<?php echo esc_attr( $job['reporter_options']['sms']['twilio_to'] ); ?>" id="twilio-to" type="text"/></td>
			            </tr>
			            <tr>
				            <th scope="row"><label for="twilio-message"><?php esc_html_e( 'Message', 'my-wp-backup' ); ?></label></th>
				            <td><textarea cols="40" rows="10" name="my-wp-backup-jobs[reporter_options][sms][twilio_message]" id="twilio-message"><?php echo esc_textarea( $job['reporter_options']['sms']['twilio_message'] ); ?></textarea></td>
			            </tr>
		            </table>
	            </div>

	            <div class="select-section section-slack <?php echo in_array( 'slack', $job['rep_destination'] ) ? 'section-active' : ''; ?>">
		            <h3 class="title"><?php esc_html_e( 'Slack Details', 'my-wp-backup' ); ?></h3>
		            <table class="form-table">
			            <tr>
				            <th scope="row"><label for="slack-hook"><?php esc_html_e( 'Webhook URL', 'my-wp-backup' ); ?></label></th>
				            <td>
					            <input type="text" id="slack-hook" name="my-wp-backup-jobs[reporter_options][slack][hook]" value="<?php echo esc_attr( $job['reporter_options']['slack']['hook'] ); ?>"><br>
					            <span class="description"><a target="_blank" href="https://my.slack.com/services/new/incoming-webhook">Click here to create one.</a></span>
				            </td>
			            </tr>
			            <tr>
				            <th scope="row"><label for="slack-room"><?php esc_html_e( 'Channel', 'my-wp-backup' ); ?></label></th>
				            <td>
					            <input type="text" id="slack-room" name="my-wp-backup-jobs[reporter_options][slack][channel]" value="<?php echo esc_attr( $job['reporter_options']['slack']['channel'] ); ?>"><br>
					            <span class="description"><?php esc_html_e( 'ID or name of the channel.', 'my-wp-backup' ); ?></span>
				            </td>
			            </tr>
			            <tr>
				            <th scope="row"><label for="slack-from"><?php esc_html_e( 'Name', 'my-wp-backup' ); ?></label></th>
				            <td>
					            <input type="text" id="slack-from" name="my-wp-backup-jobs[reporter_options][slack][username]" value="<?php echo esc_attr( $job['reporter_options']['slack']['username'] ); ?>"><br>
					            <span class="description"><?php esc_html_e( 'Name to send the report as.', 'my-wp-backup' ); ?></span>
				            </td>
			            </tr>
			            <tr>
				            <th scope="row"><label for="slack-message"><?php esc_html_e( 'Message', 'my-wp-backup' ); ?></label></th>
				            <td><textarea name="my-wp-backup-jobs[reporter_options][slack][message]" id="slack-message" cols="40" rows="10"><?php echo esc_textarea( $job['reporter_options']['slack']['message'] ); ?></textarea></td>
			            </tr>
		            </table>
	            </div>

	            <div class="select-section section-hipchat <?php echo in_array( 'hipchat', $job['rep_destination'] ) ? 'section-active' : ''; ?>">
		            <h3 class="title"><?php esc_html_e( 'Hipchat Details', 'my-wp-backup' ); ?></h3>
		            <table class="form-table">
			            <tr>
				            <th scope="row"><label for="hipchat-token"><?php esc_html_e( 'API Token', 'my-wp-backup' ); ?></label></th>
				            <td>
					            <input type="text" id="hipchat-token" name="my-wp-backup-jobs[reporter_options][hipchat][token]" value="<?php echo esc_attr( $job['reporter_options']['hipchat']['token'] ); ?>"><br>
					            <span class="description"><a target="_blank" href="https://hipchat.com/account/api"><?php esc_html_e( 'HipChat Account Page', 'my-wp-backup' ); ?></a>. <?php esc_html_e( 'Must have scope, "Send Notification".', 'my-wp-backup' ); ?></span>
				            </td>
			            </tr>
			            <tr>
				            <th scope="row"><label for="hipchat-room"><?php esc_html_e( 'Room', 'my-wp-backup' ); ?></label></th>
				            <td>
					            <input type="text" id="hipchat-room" name="my-wp-backup-jobs[reporter_options][hipchat][room]" value="<?php echo esc_attr( $job['reporter_options']['hipchat']['room'] ); ?>"><br>
					            <span class="description"><?php esc_html_e( 'ID or name of the room.', 'my-wp-backup' ); ?></span>
				            </td>
			            </tr>
			            <tr>
				            <th scope="row"><label for="hipchat-message"><?php esc_html_e( 'Message', 'my-wp-backup' ); ?></label></th>
				            <td><textarea name="my-wp-backup-jobs[reporter_options][hipchat][message]" id="hipchat-message" cols="40" rows="10"><?php echo esc_textarea( $job['reporter_options']['hipchat']['message'] ); ?></textarea></td>
			            </tr>
		            </table>
	            </div>

            </div>
	        <?php submit_button( __( 'Save Changes', 'my-wp-backup' ) ); ?>
        </form>

    <?php else : ?>

		<form action="<?php echo esc_attr( admin_url( 'admin-post.php' ) ); ?>" method="POST" accept-charset="utf-8">
			<?php wp_nonce_field( 'MyWPBackup_delete_job' ); ?>
			<?php $backups_table = new JobTable(); ?>
			<?php $backups_table->prepare_items(); ?>
			<?php $backups_table->display(); ?>
		</form>

    <?php endif; ?>

	<div id="my-wp-backup-dropbox-content" style="display:none">
		<form data-action="wp_backup_dropbox_token" data-target="#dropbox-token">
			<table class="form-table">
				<tbody>
				<tr>
					<th scope="row"><label for="dropbox-js-auth-code"><?php esc_html_e( 'Dropbox Authorization Code', 'my-wp-backup' ); ?></label></th>
					<td>
						<input id="dropbox-js-auth-code" class="my-wp-backup-access-token" type="text"/> <?php submit_button( 'Get Access Token', 'primary', 'submit', false ); ?>
						<br>
						<p class="description"><?php esc_html_e( 'You will have been directed to Dropbox on a new tab. Kindly click "Allow" and paste the authorization code here.', 'my-wp-backup' ); ?></p>
					</td>
				</tr>
				</tbody>
			</table>
		</form>
	</div>

	<div id="my-wp-backup-drive-content" style="display:none">
		<form data-action="wp_backup_drive_token" data-target="#drive-token" data-target-hidden="#drive-token-json">
			<table class="form-table">
				<tbody>
				<tr>
					<th scope="row"><label for="drive-js-auth-code"><?php esc_html_e( 'Google Drive Authorization Code', 'my-wp-backup' ); ?></label></th>
					<td>
						<input id="drive-js-auth-code" class="my-wp-backup-access-token" type="text"/> <?php submit_button( 'Get Access Token', 'primary', 'submit', false ); ?>
						<br>
						<p class="description"><?php esc_html_e( 'You will have been directed to Google on a new tab. Kindly click "Allow" and paste the authorization code here.', 'my-wp-backup' ); ?></p>
					</td>
				</tr>
				</tbody>
			</table>
		</form>
	</div>

	<div id="my-wp-backup-onedrive-content" style="display:none">
		<form data-action="wp_backup_onedrive_token" data-target="#onedrive-token" data-target-hidden="#onedrive-token-json">
			<table class="form-table">
				<tbody>
				<tr>
					<th scope="row"><label for="onedrive-js-auth-code"><?php esc_html_e( 'OneDrive Authorization Code', 'my-wp-backup' ); ?></label></th>
					<td>
						<input id="onedrive-js-auth-code" class="my-wp-backup-access-token" type="text"/> <?php submit_button( 'Get Access Token', 'primary', 'submit', false ); ?>
						<br>
						<p class="description"><?php esc_html_e( 'You will have been directed to OneDrive on a new tab. Kindly click "Yes" and paste the authorization code here.', 'my-wp-backup' ); ?></p>
					</td>
				</tr>
				</tbody>
			</table>
		</form>
	</div>

	<div id="my-wp-backup-push-content" style="display:none">
		<form data-action="wp_backup_push_token" data-target="#push-token">
			<table class="form-table">
				<tbody>
				<tr>
					<th scope="row"><label for="push-js-auth-code"><?php esc_html_e( 'PushBullet Authorization Code', 'my-wp-backup' ); ?></label></th>
					<td>
						<input id="push-js-auth-code" class="my-wp-backup-access-token" type="text"/> <?php submit_button( 'Get Access Token', 'primary', 'submit', false ); ?><br>
						<p class="description"><?php esc_html_e( 'You will have been directed to PushBullet on a new tab. Kindly click "Approve" and paste the authorization code here.', 'my-wp-backup' ); ?></p>
					</td>
				</tr>
				</tbody>
			</table>
		</form>
	</div>

	<div id="my-wp-backup-test-filters" style="display:none">
		<h4 style="text-align:center"><?php esc_html_e( 'Exclude Files', 'my-wp-backup' ); ?></h4>
		<p class="description"><?php esc_html_e( 'The following files/folders will be excluded from the backup:', 'my-wp-backup' ); ?></p>
		<pre class="terminal excluded" style="height:100%;max-height:36em"><code><?php esc_html_e( 'Please wait...', 'my-wp-backup' ); ?></code></pre>
	</div>

</div>
