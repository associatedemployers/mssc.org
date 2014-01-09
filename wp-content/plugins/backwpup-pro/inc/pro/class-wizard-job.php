<?php
/**
 *
 */
class BackWPup_Pro_Wizard_Job extends BackWPup_Pro_Wizards {

	private $destinations = array();

	/**
	 *
	 */
	public function __construct() {

		$this->info[ 'ID' ]          = 'JOB';
		$this->info[ 'name' ]        = __( 'Create a job', 'backwpup' );
		$this->info[ 'description' ] = __( 'Choose a job', 'backwpup' );
		$this->info[ 'URI' ]         = translate( BackWPup::get_plugin_data( 'PluginURI' ), 'backwpup' );
		$this->info[ 'author' ]      = BackWPup::get_plugin_data( 'Author' );
		$this->info[ 'authorURI' ]   = translate( BackWPup::get_plugin_data( 'AuthorURI' ), 'backwpup' );
		$this->info[ 'version' ]     = BackWPup::get_plugin_data( 'Version' );
		$this->info[ 'cap' ] 		 = 'backwpup_jobs_edit';

		$this->destinations = BackWPup::get_registered_destinations();

	}

	/**
	 * with steps has the wizard to to
	 */
	public function get_steps( $wizard_settings ) {

		$job_types = BackWPup::get_job_types();

		//generate steps
		$steps = array();
		if ( ! in_array( 'JOBTYPES', $wizard_settings[ 'wizard' ][ 'pre_config' ][ 'hide_steps' ] ) )
			$steps[] = array( 'id' => 'JOBTYPES', 'name' => __( 'Job Types', 'backwpup' ), 'description' => __( 'Select a task for your job.', 'backwpup' ) );
		// generate job type steps
		$job_crates_file = FALSE;
		foreach ( $job_types as $id => $type ) {
			if ( in_array( $id, $wizard_settings[ 'job_settings' ][ 'type'] ) ) {
				if (! in_array( 'JOBTYPE-' . $id, $wizard_settings[ 'wizard' ][ 'pre_config' ][ 'hide_steps' ] ))
					$steps[] = array( 'id' => 'JOBTYPE-' . $id, 'name' => $type->info[ 'name' ], 'description' => $type->info[ 'description' ] );
				if ( ! $job_crates_file )
					$job_crates_file = $type->creates_file();
			}
		}
		//steps when job creates files
		if ( $job_crates_file ) {
			if ( ! in_array( 'ARCHIVE', $wizard_settings[ 'wizard' ][ 'pre_config' ][ 'hide_steps' ] ) )
				$steps[] = array( 'id' => 'ARCHIVE', 'name' => __( 'Archive Settings', 'backwpup' ), 'description' => __( 'Settings for the Backup Archive', 'backwpup' ) );
			if ( ! in_array( 'DESTINATIONS', $wizard_settings[ 'wizard' ][ 'pre_config' ][ 'hide_steps' ] ) )
				$steps[] = array( 'id' => 'DESTINATIONS', 'name' => __( 'Destinations', 'backwpup' ), 'description' => __( 'Where would you like to store the backup file?', 'backwpup' ), 'create' => FALSE );
			// generate destinations
			if ( ! empty( $wizard_settings[ 'job_settings' ][ 'destinations' ] ) ) {
				foreach ( $this->destinations as $id => $dest ) {
					if ( in_array( $id, $wizard_settings[ 'job_settings' ][ 'destinations'] ) && ! in_array( 'DEST-' . $id, $wizard_settings[ 'wizard' ][ 'pre_config' ][ 'hide_steps' ] )  )
						$steps[] = array( 'id' => 'DEST-' . $id, 'name' => $dest[ 'info' ][ 'name' ], 'description' => $dest[ 'info' ][ 'description' ] );
				}
			}
		}
		if ( ! in_array( 'SCHEDULE', $wizard_settings[ 'wizard' ][ 'pre_config' ][ 'hide_steps' ] ) )
			$steps[] = array( 'id' => 'SCHEDULE', 'name' => __( 'Scheduling', 'backwpup' ), 'description' => __( 'When would you like to start the job?', 'backwpup' ) );

		return $steps;
	}


	/**
	 * Initiate Wizard Settings
	 */
	public function initiate( $wizard_settings ) {

		// get job settings if a existing job opened
		if ( empty( $wizard_settings[ 'job_settings' ] ) ) {
			$wizard_settings[ 'job_settings' ] = BackWPup_Option::defaults_job();
			if ( empty( $wizard_settings[ 'wizard' ][ 'pre_config' ] ) )
				$wizard_settings[ 'wizard' ][ 'pre_config' ] = $this->get_pre_configurations( 'all' );
			$wizard_settings[ 'job_settings' ] = array_merge( $wizard_settings[ 'job_settings' ], $wizard_settings[ 'wizard' ][ 'pre_config' ][ 'job_settings' ] );
		}

		return $wizard_settings;
	}

	/**
	 * @param $wizard_settings
	 */
	public function admin_print_styles( $wizard_settings ) {


		//add css for the first steps
		if ( $wizard_settings[ 'wizard' ][ 'step' ] == 'SCHEDULE' ) {
			if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
				wp_enqueue_style( 'backwpuptabcron', BackWPup::get_plugin_data( 'URL' ) . '/css/page_edit_tab_cron.css', '', time(), 'screen' );
			} else {
				wp_enqueue_style( 'backwpuptabcron', BackWPup::get_plugin_data( 'URL' ) . '/css/page_edit_tab_cron.min.css', '', BackWPup::get_plugin_data( 'Version' ), 'screen' );
			}
		}
		//add css for all other steps
		elseif ( strstr( $wizard_settings[ 'wizard' ][ 'step' ], 'DEST-' ) ) {
			$dests_object = BackWPup::get_destination( str_replace( 'DEST-', '', $wizard_settings[ 'wizard' ][ 'step' ] ) );
			$dests_object->wizard_admin_print_styles( );
		}
		elseif ( strstr( $wizard_settings[ 'wizard' ][ 'step' ], 'JOBTYPE-' ) ) {
			$job_type = BackWPup::get_job_types();
			$id       = strtoupper( str_replace( 'JOBTYPE-', '', $wizard_settings[ 'wizard' ][ 'step' ] ) );
			$job_type[ $id ]->wizard_admin_print_styles( );
		}

	}

	/**
	 * called on page admin_print_scripts
	 */
	public function admin_print_scripts( $wizard_settings ) {

		//add js for the first step
		if ( $wizard_settings[ 'wizard' ][ 'step' ] == 'JOB' ) {
			if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
				wp_enqueue_script( 'backwpuptabjob', BackWPup::get_plugin_data( 'URL' ) . '/assets/js/page_edit_tab_job.js', array('jquery'), time(), TRUE );
			} else {
				wp_enqueue_script( 'backwpuptabjob', BackWPup::get_plugin_data( 'URL' ) . '/assets/js/page_edit_tab_job.min.js', array('jquery'), BackWPup::get_plugin_data( 'Version' ), TRUE );
			}
		}
		//add js for all other steps
		elseif ( strstr( $wizard_settings[ 'wizard' ][ 'step' ], 'DEST-' ) ) {
			$dests_object = BackWPup::get_destination( str_replace( 'DEST-', '', $wizard_settings[ 'wizard' ][ 'step' ] ) );
			$dests_object->wizard_admin_print_scripts( );
		}
		elseif (  strstr( $wizard_settings[ 'wizard' ][ 'step' ], 'JOBTYPE-' ) ) {
			$job_type = BackWPup::get_job_types();
			$id       = strtoupper( str_replace( 'JOBTYPE-', '', $wizard_settings[ 'wizard' ][ 'step' ] ) );
			$job_type[ $id ]->wizard_admin_print_scripts( );
		}
	}


	/**
	 * called on page inline_js
	 */
	public function inline_js( $wizard_settings ) {

		if ( $wizard_settings[ 'wizard' ][ 'step' ] == 'ARCHIVE' )
			$this->inline_js_archive();
		if ( $wizard_settings[ 'wizard' ][ 'step' ] == 'SCHEDULE' )
			$this->inline_js_schedule();

		// add inline js
		if ( strstr( $wizard_settings[ 'wizard' ][ 'step' ], 'DEST-' ) ) {
			$dests_object = BackWPup::get_destination( str_replace( 'DEST-', '', $wizard_settings[ 'wizard' ][ 'step' ] ) );
			$dests_object->wizard_inline_js( );
		}
		if ( strstr( $wizard_settings[ 'wizard' ][ 'step' ], 'JOBTYPE-' ) ) {
			$job_types    = BackWPup::get_job_types();
			$id = strtoupper( str_replace( 'JOBTYPE-', '', $wizard_settings[ 'wizard' ][ 'step' ] ) );
			$job_types[ $id ]->wizard_inline_js( );
		}
	}

	/**
	 * called on save
	 */
	public function save( $wizard_settings ) {

		//call default wizard saves
		if ( $wizard_settings[ 'wizard' ][ 'step' ] == 'JOBTYPES' ) {
			$wizard_settings[ 'job_settings' ] = $this->save_jobtypes( $wizard_settings[ 'job_settings' ] );
		} elseif ( $wizard_settings[ 'wizard' ][ 'step' ] == 'SCHEDULE' ) {
			$wizard_settings[ 'job_settings' ] = $this->save_schedule( $wizard_settings[ 'job_settings' ] );
		} elseif ( $wizard_settings[ 'wizard' ][ 'step' ] == 'DESTINATIONS' ) {
			$wizard_settings[ 'job_settings' ] = $this->save_destinations( $wizard_settings[ 'job_settings' ] );
		} elseif ( $wizard_settings[ 'wizard' ][ 'step' ] == 'ARCHIVE' ) {
			$wizard_settings[ 'job_settings' ] = $this->save_archive( $wizard_settings[ 'job_settings' ] );
		}

		//call wizard saves for destination or jobtypes
		elseif ( strstr( $wizard_settings[ 'wizard' ][ 'step' ], 'DEST-' ) ) {
			$dests_object = BackWPup::get_destination( str_replace( 'DEST-', '', $wizard_settings[ 'wizard' ][ 'step' ] ) );
			$wizard_settings[ 'job_settings' ] = $dests_object->wizard_save( $wizard_settings[ 'job_settings' ] );
		}
		elseif ( strstr( $wizard_settings[ 'wizard' ][ 'step' ], 'JOBTYPE-' ) ) {
			$job_types    = BackWPup::get_job_types();
			$id = strtoupper( str_replace( 'JOBTYPE-', '', $wizard_settings[ 'wizard' ][ 'step' ] ) );
			$wizard_settings[ 'job_settings' ] = $job_types[ $id ]->wizard_save( $wizard_settings[ 'job_settings' ] );
		}

		return $wizard_settings;
	}

	/**
	 * called on wizard page
	 */
	public function page( $wizard_settings ) {

		//call default wizard pages
		if ( $wizard_settings[ 'wizard' ][ 'step' ] == 'JOBTYPES' )
			$this->page_jobtypes( $wizard_settings[ 'job_settings' ] );
		elseif ( $wizard_settings[ 'wizard' ][ 'step' ] == 'SCHEDULE' )
			$this->page_schedule( $wizard_settings[ 'job_settings' ] );
		elseif ( $wizard_settings[ 'wizard' ][ 'step' ] == 'DESTINATIONS' )
			$this->page_destinations( $wizard_settings[ 'job_settings' ] );
		elseif ( $wizard_settings[ 'wizard' ][ 'step' ] == 'ARCHIVE' )
			$this->page_archive( $wizard_settings[ 'job_settings' ] );
		//call wizard pages for destination or jobtypes
		elseif ( strstr( $wizard_settings[ 'wizard' ][ 'step' ], 'DEST-' ) ) {
			$dests_object = BackWPup::get_destination( str_replace( 'DEST-', '', $wizard_settings[ 'wizard' ][ 'step' ] ) );
			$dests_object->wizard_page( $wizard_settings[ 'job_settings' ] );
		}
		elseif ( strstr( $wizard_settings[ 'wizard' ][ 'step' ], 'JOBTYPE-' ) ) {
			$job_types    = BackWPup::get_job_types();
			$id = strtoupper( str_replace( 'JOBTYPE-', '', $wizard_settings[ 'wizard' ][ 'step' ] ) );
			$job_types[ $id ]->wizard_page( $wizard_settings[ 'job_settings' ] );
		}
	}

	/**
	 * called on wizard page jobtypes
	 */
	public function page_jobtypes( $job_settings ) {

		$job_types = BackWPup::get_job_types();
		?>
		<table class="form-table">
			<tr>
				<td>
					<h3 class="title"><?php _e( 'This job is a&#160;&hellip;', 'backwpup' ) ?></h3>
					<p><?php _e('Select one or more tasks for your backup job.','backwpup'); ?></p>
					<fieldset>
						<legend class="screen-reader-text"><span><?php _e( 'Job tasks', 'backwpup' ) ?></span></legend>
						<?php
						foreach ( $job_types as $id => $typeclass ) {
							$addclass = '';
							if ( $typeclass->creates_file( ) )
								$addclass = ' filetype';
							$title='';
							if ( ! empty( $typeclass->info[ 'help' ] ) ) {
								$title = ' title="' . esc_attr__( $typeclass->info[ 'help' ] ) . '"';
								$addclass .= ' help-tip';
							}
							echo '<label for="jobtype-select-' . strtolower( $id ) . '"><input class="jobtype-select checkbox' . $addclass . '"' . $title . ' id="jobtype-select-' . strtolower( $id ) . '" type="checkbox"' . checked( TRUE, in_array( $id, $job_settings[ 'type' ] ), FALSE ) . ' name="type[]" value="' . $id . '" /> ' . $typeclass->info[ 'description' ] . '</label><br />';
						}
						?>
					</fieldset>
				</td>
			</tr>
		</table>
		<?php
	}

	/**
	 * called on wizard page schedule
	 */
	public function page_schedule( $job_settings ) {

		list( $cronstr[ 'minutes' ], $cronstr[ 'hours' ], $cronstr[ 'mday' ], $cronstr[ 'mon' ], $cronstr[ 'wday' ] ) = explode( ' ', $job_settings[ 'cron' ], 5 );
		if ( strstr( $cronstr[ 'minutes' ], '*/' ) )
			$minutes = explode( '/', $cronstr[ 'minutes' ] );
		else
			$minutes = explode( ',', $cronstr[ 'minutes' ] );
		if ( strstr( $cronstr[ 'hours' ], '*/' ) )
			$hours = explode( '/', $cronstr[ 'hours' ] );
		else
			$hours = explode( ',', $cronstr[ 'hours' ] );
		if ( strstr( $cronstr[ 'mday' ], '*/' ) )
			$mday = explode( '/', $cronstr[ 'mday' ] );
		else
			$mday = explode( ',', $cronstr[ 'mday' ] );
		if ( strstr( $cronstr[ 'mon' ], '*/' ) )
			$mon = explode( '/', $cronstr[ 'mon' ] );
		else
			$mon = explode( ',', $cronstr[ 'mon' ] );
		if ( strstr( $cronstr[ 'wday' ], '*/' ) )
			$wday = explode( '/', $cronstr[ 'wday' ] );
		else
			$wday = explode( ',', $cronstr[ 'wday' ] );

		?>
		<table class="form-table">
			<tr>
				<td>
					<h3 class="title"><?php _e( 'Scheduling', 'backwpup' ); ?></h3>
					<label for="activetype"><input type="checkbox" id="activetype" <?php checked( $job_settings[ 'activetype' ], 'wpcron', TRUE ); ?> name="activetype" value="wpcron" /> <?php _e( 'Activate scheduling', 'backwpup') ?></label><br />
				</td>
			</tr>
			<tr>
				<td class="table_planung">
					<h3 class="title hasdests scheduler"><?php _e( 'Scheduler', 'backwpup' ); ?></h3>
						<table id="wpcronbasic" class="scheduler">
						<tr>
							<th>
								<?php _e( 'Type', 'backwpup' ); ?>
							</th>
							<th>
								&nbsp;
							</th>
							<th>
								<?php _e( 'Hour', 'backwpup' ); ?>
							</th>
							<th>
								<?php _e( 'Minute', 'backwpup' ); ?>
							</th>
						</tr>
						<tr>
							<td><label for="idcronbtype-mon"><?php echo '<input class="radio" type="radio"' . checked( TRUE, is_numeric( $mday[ 0 ] ), FALSE ) . ' name="cronbtype" id="idcronbtype-mon" value="mon" /> ' . __( 'monthly', 'backwpup' ); ?></label></td>
							<td><select name="moncronmday"><?php for ( $i = 1; $i <= 31; $i ++ ) {
								echo '<option ' . selected( in_array( "$i", $mday, TRUE ), TRUE, FALSE ) . '  value="' . $i . '" />' . __( 'on', 'backwpup' ) . ' ' . $i . '.</option>';
							} ?></select></td>
							<td><select name="moncronhours"><?php for ( $i = 0; $i < 24; $i ++ ) {
								echo '<option ' . selected( in_array( "$i", $hours, TRUE ), TRUE, FALSE ) . '  value="' . $i . '" />' . $i . '</option>';
							} ?></select></td>
							<td><select name="moncronminutes"><?php for ( $i = 0; $i < 60; $i = $i + 10 ) {
								echo '<option ' . selected( in_array( "$i", $minutes, TRUE ), TRUE, FALSE ) . '  value="' . $i . '" />' . $i . '</option>';
							} ?></select></td>
						</tr>
						<tr>
							<td><label for="idcronbtype-week"><?php echo '<input class="radio" type="radio"' . checked( TRUE, is_numeric( $wday[ 0 ] ), FALSE ) . ' name="cronbtype" id="idcronbtype-week" value="week" /> ' . __( 'weekly', 'backwpup' ); ?></label></td>
							<td><select name="weekcronwday">
								<?php     echo '<option ' . selected( in_array( "0", $wday, TRUE ), TRUE, FALSE ) . '  value="0" />' . __( 'Sunday', 'backwpup' ) . '</option>';
								echo '<option ' . selected( in_array( "1", $wday, TRUE ), TRUE, FALSE ) . '  value="1" />' . __( 'Monday', 'backwpup' ) . '</option>';
								echo '<option ' . selected( in_array( "2", $wday, TRUE ), TRUE, FALSE ) . '  value="2" />' . __( 'Tuesday', 'backwpup' ) . '</option>';
								echo '<option ' . selected( in_array( "3", $wday, TRUE ), TRUE, FALSE ) . '  value="3" />' . __( 'Wednesday', 'backwpup' ) . '</option>';
								echo '<option ' . selected( in_array( "4", $wday, TRUE ), TRUE, FALSE ) . '  value="4" />' . __( 'Thursday', 'backwpup' ) . '</option>';
								echo '<option ' . selected( in_array( "5", $wday, TRUE ), TRUE, FALSE ) . '  value="5" />' . __( 'Friday', 'backwpup' ) . '</option>';
								echo '<option ' . selected( in_array( "6", $wday, TRUE ), TRUE, FALSE ) . '  value="6" />' . __( 'Saturday', 'backwpup' ) . '</option>'; ?>
							</select></td>
							<td><select name="weekcronhours"><?php for ( $i = 0; $i < 24; $i ++ ) {
								echo '<option ' . selected( in_array( "$i", $hours, TRUE ), TRUE, FALSE ) . '  value="' . $i . '" />' . $i . '</option>';
							} ?></select></td>
							<td><select name="weekcronminutes"><?php for ( $i = 0; $i < 60; $i = $i + 10 ) {
								echo '<option ' . selected( in_array( "$i", $minutes, TRUE ), TRUE, FALSE ) . '  value="' . $i . '" />' . $i . '</option>';
							} ?></select></td>
						</tr>
						<tr>
							<td><label for="idcronbtype-day"><?php echo '<input class="radio" type="radio"' . checked( "**", $mday[ 0 ] . $wday[ 0 ], FALSE ) . ' name="cronbtype" id="idcronbtype-day" value="day" /> ' . __( 'daily', 'backwpup' ); ?></label></td>
							<td></td>
							<td><select name="daycronhours"><?php for ( $i = 0; $i < 24; $i ++ ) {
								echo '<option ' . selected( in_array( "$i", $hours, TRUE ), TRUE, FALSE ) . '  value="' . $i . '" />' . $i . '</option>';
							} ?></select></td>
							<td><select name="daycronminutes"><?php for ( $i = 0; $i < 60; $i = $i + 10 ) {
								echo '<option ' . selected( in_array( "$i", $minutes, TRUE ), TRUE, FALSE ) . '  value="' . $i . '" />' . $i . '</option>';
							} ?></select></td>
						</tr>
						<tr>
							<td><label for="idcronbtype-hour"><?php echo '<input class="radio" type="radio"' . checked( "*", $hours[ 0 ], FALSE, FALSE ) . ' name="cronbtype" id="idcronbtype-hour" value="hour" /> ' . __( 'hourly', 'backwpup' ); ?></label></td>
							<td></td>
							<td></td>
							<td><select name="hourcronminutes"><?php for ( $i = 0; $i < 60; $i = $i + 10 ) {
								echo '<option ' . selected( in_array( "$i", $minutes, TRUE ), TRUE, FALSE ) . '  value="' . $i . '" />' . $i . '</option>';
							} ?></select></td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		<?php
	}

	/**
	 * called on wizard page archive
	 */
	public function page_archive( $job_settings ) {

		?>
		<table class="form-table">
			<tr>
				<td>
					<h3 class="title hasdests"><?php _e( 'Backup type', 'backwpup' ); ?></h3>
					<p class="hasdests"></p>
					<fieldset>
						<legend class="screen-reader-text"><?php _e( 'Backup type', 'backwpup' ) ?></legend>
						<label for="backuptype-sync"><input class="radio"
							   type="radio"<?php checked( 'sync', $job_settings[ 'backuptype' ], TRUE ); ?>
							   name="backuptype" id="backuptype-sync"
							   value="sync"/> <?php _e( 'Sync file by file to destination', 'backwpup' ); ?></label><br/>
						<label for="backuptype-archive"><input class="radio"
							   type="radio"<?php checked( 'archive', $job_settings[ 'backuptype' ], TRUE ); ?>
							   name="backuptype" id="backuptype-archive"
							   value="archive"/> <?php _e( 'Create a backup archive', 'backwpup' ); ?></label><br/>
					</fieldset>
				</td>
			</tr>

			<tr class="archive">
				<td>
					<h3 class="title hasdests"><?php _e( 'Select a compression type for the backup archive', 'backwpup' ) ?></h3>
					<p class="hasdests"></p>
					<fieldset>
						<legend class="screen-reader-text"><?php _e( 'Archive compression type', 'backwpup' ) ?></legend>
						<?php
						if ( function_exists( 'gzopen' ) || class_exists( 'ZipArchive' ) )
							echo '<label for="idarchiveformat-zip"><input class="radio help-tip" type="radio"' . checked( '.zip', $job_settings[ 'archiveformat' ], FALSE ) . ' name="archiveformat" id="idarchiveformat-zip" value=".zip" title="' . esc_attr__( 'PHP Zip functions will be used if available (memory lees). Else PCLZip Class will used.', 'backwpup' ) . '" /> ' . __( 'Zip', 'backwpup' ) . '</label><br />';
						else
							echo '<label for="idarchiveformat-zip"><input class="radio help-tip" type="radio"' . checked( '.zip', $job_settings[ 'archiveformat' ], FALSE ) . ' name="archiveformat" id="idarchiveformat-zip" value=".zip" disabled="disabled" title="' . esc_attr__( 'Disabled because missing PHP function.', 'backwpup' ) . '" /> ' . __( 'Zip', 'backwpup' ) . '</label><br />';
						echo '<label for="idarchiveformat-tar"><input class="radio help-tip" type="radio"' . checked( '.tar', $job_settings[ 'archiveformat' ], FALSE ) . ' name="archiveformat" id="idarchiveformat-tar" value=".tar" title="' . esc_attr__( 'Tar (fast and memory less) uncompressed', 'backwpup' ) . '" /> ' . __( 'Tar', 'backwpup' ) . '</label><br />';
						if ( function_exists( 'gzopen' ) )
							echo '<label for="idarchiveformat-targz"><input class="radio help-tip" type="radio"' . checked( '.tar.gz', $job_settings[ 'archiveformat' ], FALSE ) . ' name="archiveformat" id="idarchiveformat-targz" value=".tar.gz" title="' . esc_attr__( 'A tared and GZipped archive (fast and memory less)', 'backwpup' ) . '" /> ' . __( 'Tar GZip', 'backwpup' ) . '</label><br />';
						else
							echo '<label for="idarchiveformat-targz"><input class="radio help-tip" type="radio "' . checked( '.tar.gz', $job_settings[ 'archiveformat' ], FALSE ) . ' name="archiveformat" id="idarchiveformat-targz" value=".tar.gz" disabled="disabled" title="' . esc_attr__( 'Disabled because missing PHP function.', 'backwpup' ) . '" /> ' . __( 'Tar GZip', 'backwpup' ) . '</label><br />';
						if ( function_exists( 'bzopen' ) )
							echo '<label for="idarchiveformat-tarbz2"><input class="radio help-tip" type="radio"' . checked( '.tar.bz2',$job_settings[ 'archiveformat' ], FALSE ) . ' name="archiveformat" id="idarchiveformat-tarbz2" value=".tar.bz2" title="' . esc_attr__( 'A tared and BZipped archive (fast and memory less)', 'backwpup' ) . '" /> ' . __( 'Tar BZip2', 'backwpup' ) . '</label><br />';
						else
							echo '<label for="idarchiveformat-tarbz2"><input class="radio help-tip" type="radio"' . checked( '.tar.bz2', $job_settings[ 'archiveformat' ], FALSE ) . ' name="archiveformat" id="idarchiveformat-tarbz2" value=".tar.bz2" disabled="disabled" title="' . esc_attr__( 'Disabled because missing PHP function.', 'backwpup' ) . '" /> ' . __( 'Tar BZip2', 'backwpup' ) . '</label><br />';
						?>
					</fieldset>
				</td>
			</tr>
		</table>
		<?php
	}

	/**
	 * called on wizard page destinations
	 */
	public function page_destinations( $job_settings ) {

		?>
		<table class="form-table">
			<tr>
				<td>
					<h3 class="title"><?php _e( 'Where to store the files', 'backwpup' ) ?></h3>
					<p></p>
					<fieldset>
						<legend class="screen-reader-text"><?php _e( 'Destinations', 'backwpup' ) ?></legend>
						<?php
						foreach ( $this->destinations as $id => $dest ) {
							if ( $job_settings[ 'backuptype' ] == 'archive' || ( $job_settings[ 'backuptype' ] == 'sync' && $dest[ 'can_sync' ] ) ) {
								echo '<label for="dest-select-' . strtolower( $id ) . '">';
								if ( ! empty( $dest[ 'error' ] ) )
									echo '<span class="description">' . $dest[ 'error' ] . '</span><br />';
								$addclass = '';
								$title = '';
								if ( ! empty( $dest[ 'info' ][ 'help' ] ) ) {
									$title = ' title="' . esc_attr__( $dest[ 'info' ][ 'help' ] ) . '"';
									$addclass .= ' help-tip';
								}
								echo '<input class="checkbox ' . $addclass . '"' . $title . ' id="dest-select-' . strtolower( $id ) . '" type="checkbox"' . checked( TRUE, in_array( $id, $job_settings[ 'destinations' ] ), FALSE ) . ' name="destinations[]" value="' . $id . '" ' . disabled( ! empty( $dest[ 'error' ] ), TRUE, FALSE ) . ' /> ' . $dest[ 'info' ][ 'description' ] . '</label><br />';

							}
						}
						?>
					</fieldset>
				</td>
			</tr>
		</table>
		<?php
	}

	/**
	 * @param $job_settings
	 */
	public function save_jobtypes( $job_settings ) {

		$job_types = BackWPup::get_job_types();

		if ( isset( $_POST[ 'type' ] ) && is_array( $_POST[ 'type' ] ) ) {
			foreach ( (array)$_POST[ 'type' ] as $typeid ) {
				if ( empty( $job_types[ $typeid ] ) )
					unset( $_POST[ 'type' ][ $typeid ] );
			}
			if ( is_array( $_POST[ 'type' ] ) )
				sort( $_POST[ 'type' ] );
		} else {
			$_POST[ 'type' ]= array();
		}

		$job_settings[ 'type' ] = $_POST[ 'type' ];

		$creates_file = FALSE;
		foreach ( $job_settings[ 'type' ] as $type) {
			if ( $job_types[ $type ]->creates_file( ) ) {
				$creates_file = TRUE;
				break;
			}
		}

		if ( ! $creates_file )
			$job_settings[ 'destinations' ] = array();

		return $job_settings;
	}

	/**
	 * @param $job_settings
	 */
	public function save_schedule( $job_settings ) {
		if ( isset( $_POST[ 'activetype' ] ) )
			$job_settings[ 'activetype' ] = ( $_POST[ 'activetype' ] == 'wpcron' ) ? 'wpcron' : '';
		else
			$job_settings[ 'activetype' ] = '';
		$job_settings[ 'cronselect' ] = 'basic';
		if ( $_POST[ 'cronbtype' ] == 'mon' )
			$job_settings[ 'cron' ] = $_POST[ 'moncronminutes' ] . ' ' . $_POST[ 'moncronhours' ] . ' ' . $_POST[ 'moncronmday' ] . ' * *';
		if ( $_POST[ 'cronbtype' ] == 'week' )
			$job_settings[ 'cron' ] = $_POST[ 'weekcronminutes' ] . ' ' . $_POST[ 'weekcronhours' ] . ' * * ' . $_POST[ 'weekcronwday' ];
		if ( $_POST[ 'cronbtype' ] == 'day' )
			$job_settings[ 'cron' ] =$_POST[ 'daycronminutes' ] . ' ' . $_POST[ 'daycronhours' ] . ' * * *';
		if ( $_POST[ 'cronbtype' ] == 'hour' )
			$job_settings[ 'cron' ] =  $_POST[ 'hourcronminutes' ] . ' * * * *';

		return $job_settings;
	}

	/**
	 * @param $job_settings
	 */
	public function save_destinations( $job_settings ) {

		if ( isset( $_POST[ 'destinations' ] ) && is_array( $_POST[ 'destinations' ] ) ) {
			foreach ( (array)$_POST[ 'destinations' ] as $dst_id ) {
				if ( empty( $this->destinations[ $dst_id ] ) )
					unset( $_POST[ 'destinations' ][ $dst_id ] );
			}
			if ( is_array( $_POST[ 'destinations' ] ) )
				sort( $_POST[ 'destinations' ] );
		} else {
			$_POST[ 'destinations' ]= array();
		}

		$job_settings[ 'destinations' ] = $_POST[ 'destinations' ];

		return $job_settings;
	}

	/**
	 * @param $job_settings
	 */
	public function save_archive( $job_settings ) {

		$job_settings[ 'archiveformat' ] 	= $_POST[ 'archiveformat' ];
		$job_settings[ 'archivename' ] 		= 'backwpup_' . BackWPup::get_plugin_data( 'hash' ) . '_%Y-%m-%d_%H-%i-%s';
		$job_settings[ 'backuptype' ]     	= $_POST[ 'backuptype' ];

		return $job_settings;
	}


	public function inline_js_archive() {
		// <script type="text/javascript">
		?>

		$('input[name="backuptype"]').change(function () {
			if ($(this).val() == 'sync') {
				$('.archive').hide();
				$('.sync').show();
			} else {
				$('.archive').show();
				$('.sync').hide();
			}
		});

		if ($('input[name="backuptype"]:checked').val() == 'sync') {
			$('.archive').hide();
			$('.sync').show();
		} else {
			$('.archive').show();
			$('.sync').hide();
		}

		<?php
	}

	public function inline_js_schedule() {
		// <script type="text/javascript">
		?>

		$( 'input[name="activetype"]' ).change(function () {
			if ( $( this ).prop( "checked" ) ) {
				$( '.scheduler' ).show();
			} else {
				$( '.scheduler' ).hide();
			}
		});

		if ( $( 'input[name="activetype"]' ).prop( "checked" ) ) {
            $( '.scheduler' ).show();
        } else {
            $( '.scheduler' ).hide();
        }

		<?php
	}


	/**
	 * called if last button clicked
	 */
	public function execute( $wizard_settings ) {

		//get new jobid for new jobs
		$exsitingjobids = BackWPup_Option::get_job_ids();
		sort( $exsitingjobids );
		$wizard_settings[ 'job_settings' ][ 'jobid' ] = end( $exsitingjobids ) + 1;

		// set job name
		$wizard_settings[ 'job_settings' ][ 'name' ] = sprintf( __('Wizard: %1$s','backwpup'), $wizard_settings[ 'wizard' ][ 'pre_config' ][ 'name'] );

		//some default settings
		$wizard_settings[ 'job_settings' ][ 'mailaddresslog' ] = sanitize_email( get_bloginfo( 'admin_email' ) );
		$wizard_settings[ 'job_settings' ][ 'mailerroronly' ]  = TRUE;

		//reschedule job
		$cron_next = BackWPup_Cron::cron_next( $wizard_settings[ 'job_settings' ][ 'cron' ] ) ;
		wp_clear_scheduled_hook( 'backwpup_cron', array( 'id' => $wizard_settings[ 'job_settings' ][ 'jobid' ] ) );
		if ( $wizard_settings[ 'job_settings' ][ 'activetype' ]== 'wpcron' ) {
			wp_schedule_single_event( $cron_next, 'backwpup_cron', array( 'id' => $wizard_settings[ 'job_settings' ][ 'jobid' ] ) );
		}

		// save
		foreach ( $wizard_settings[ 'job_settings' ] as $option_name => $option_value ) {
			BackWPup_Option::update( $wizard_settings[ 'job_settings' ][ 'jobid' ], $option_name, $option_value );
		}

		//text
		echo '<div id="message" class="updated below-h2"><p>' . sprintf( __( 'New job %s generated.', 'backwpup'),$wizard_settings[ 'job_settings' ][ 'name' ] ) . '</p></div>';

	}

	/**
	 * The name of the last button (execute button)
	 *
	 * @param $wizard_settings
	 * @return string
	 */
	public function get_last_button_name( $wizard_settings ) {

			return __( 'Create Job', 'backwpup' );
	}

	/**
	 * Should the wizard run step by step or can yup between steps
	 *
	 * @param $wizard_settings
	 * @return bool
	 */
	public function is_step_by_step( $wizard_settings ) {

		return TRUE;
	}

	/**
	 * Set Pre configurations
	 *
	 * @param null $id
	 * @return array
	 */
	public function get_pre_configurations( $id = NULL ) {
		global $wpdb;
		/* @var wpdb $wpdb */

		//pre config for Database backup
		$pre_configurations[ 'db' ][ 'name' ] = __( 'Database Backup and XML Export (Daily)', 'backwpup');
		$pre_configurations[ 'db' ][ 'description' ] = __( 'Database Backup and XML Export (Daily)', 'backwpup');
		//get tables that should not uses on DB configs
		$dbdumpexclude = array();
		$dbtables = $wpdb->get_results( 'SHOW TABLES FROM `' . DB_NAME . '`', ARRAY_N );
		foreach ( $dbtables as $dbtable) {
			if ( ! strstr( $dbtable[ 0 ], $wpdb->prefix) )
				$dbdumpexclude[] = $dbtable[ 0 ];
		}
		$pre_configurations[ 'db' ][ 'job_settings' ] = array(
				'type' => array( 'DBDUMP', 'WPEXP' ),
				'dbdumpexclude' => $dbdumpexclude,
				'cron' => '0 1 * * *',
				'activetype' => 'wpcron'
		);
		$pre_configurations[ 'db' ][ 'hide_steps' ] = array( 'JOBTYPES', 'JOBTYPE-DBDUMP', 'JOBTYPE-WPEXP', 'SCHEDULE' );

		//pre config for Database Check and optimize
		$pre_configurations[ 'dbchop' ][ 'name' ] = __( 'Database Check (Weekly)', 'backwpup');
		$pre_configurations[ 'dbchop' ][ 'description' ] =  __( 'Database Check (Weekly)', 'backwpup');
		$pre_configurations[ 'dbchop' ][ 'job_settings' ] = array(
			'type' => array( 'DBCHECK' ),
			'cron' => '30 3 * * 1',
			'activetype' => 'wpcron'
		);
		$pre_configurations[ 'dbchop' ][ 'hide_steps' ] = array( 'JOBTYPES', 'JOBTYPE-DBCHECK', 'SCHEDULE' );

		//pre config for uploads backup
		$pre_configurations[ 'upfile' ][ 'name' ] = __( 'Backup uploads folder', 'backwpup');
		$pre_configurations[ 'upfile' ][ 'description' ] =  __( 'Backup uploads folder', 'backwpup');
		$pre_configurations[ 'upfile' ][ 'job_settings' ] = array(
			'type' => array( 'FILE' ),
			'backupexcludethumbs'     => FALSE,
			'backupspecialfiles' 	  => FALSE,
			'backuproot'    => FALSE,
			'backupcontent' => FALSE,
			'backupplugins' => FALSE,
			'backupthemes'  => FALSE,
			'backupuploads' => TRUE
		);
		$pre_configurations[ 'upfile' ][ 'hide_steps' ] = array( 'JOBTYPES', 'JOBTYPE-FILE' );

		//pre config for all file backup
		$pre_configurations[ 'file' ][ 'name' ] = __( 'Backup all files', 'backwpup');
		$pre_configurations[ 'file' ][ 'description' ] =  __( 'Backup all files', 'backwpup');
		$pre_configurations[ 'file' ][ 'job_settings' ] = array(
				'type' => array( 'FILE' ),
				'backupexcludethumbs'     => FALSE,
				'backupspecialfiles' 	  => TRUE,
				'backuproot'    => TRUE,
				'backupcontent' => TRUE,
				'backupplugins' => TRUE,
				'backupthemes'  => TRUE,
				'backupuploads' => TRUE
		);
		$pre_configurations[ 'file' ][ 'hide_steps' ] = array( 'JOBTYPES', 'JOBTYPE-FILE' );

		//pre config for Needed files backup
		$pre_configurations[ 'neddedfile' ][ 'name' ] = __( 'Essential files + list of plugins', 'backwpup');
		$pre_configurations[ 'neddedfile' ][ 'description' ] =  __( 'Backup essential files and folders, plus a list of installed plugins.', 'backwpup');
		$pre_configurations[ 'neddedfile' ][ 'job_settings' ] = array(
				'type' => array( 'FILE', 'WPPLUGIN' ),
				'backupexcludethumbs'     => TRUE,
				'backupspecialfiles' 	  => TRUE,
				'backuproot'    => FALSE,
				'backuprootexcludedirs' => array( 'wp-includes', 'wp-admin' ),
				'backupcontent' => TRUE,
				'backupplugins' => FALSE,
				'backupthemes'  => TRUE,
				'backupuploads' => TRUE
		);
		$pre_configurations[ 'neddedfile' ][ 'hide_steps' ] = array( 'JOBTYPES', 'JOBTYPE-FILE', 'JOBTYPE-WPPLUGIN' );

		//Pre config where all must done self
		$pre_configurations[ 'all' ][ 'name' ] = __( 'Custom configuration', 'backwpup');
		$pre_configurations[ 'all' ][ 'description' ] =  __( 'Custom configuration', 'backwpup');
		$pre_configurations[ 'all' ][ 'job_settings' ] = array();
		$pre_configurations[ 'all' ][ 'hide_steps' ] = array( 'WPPLUGIN' );

		if ( $id == NULL ) {
			$pre_configurations_names = array();
			foreach ( $pre_configurations as $id => $values )
				$pre_configurations_names[ $id ] = $values[ 'name' ];
			return $pre_configurations_names;
		} else {
			return $pre_configurations[ $id ];
		}
	}

}
