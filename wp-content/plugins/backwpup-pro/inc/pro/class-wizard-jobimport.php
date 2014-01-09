<?php

/**
 *
 */
class BackWPup_Pro_Wizard_JobImport extends BackWPup_Pro_Wizards {

	/**
	 *
	 */
	public function __construct() {

		$this->info[ 'ID' ]          = 'JOBIMPORT';
		$this->info[ 'name' ]        = __( 'XML job import', 'backwpup' );
		$this->info[ 'description' ] = __( 'Wizard for importing BackWPup jobs from an XML file', 'backwpup' );
		$this->info[ 'URI' ]         = translate( BackWPup::get_plugin_data( 'PluginURI' ), 'backwpup' );
		$this->info[ 'author' ]      = BackWPup::get_plugin_data( 'Author' );
		$this->info[ 'authorURI' ]   = translate( BackWPup::get_plugin_data( 'AuthorURI' ), 'backwpup' );
		$this->info[ 'version' ]     = BackWPup::get_plugin_data( 'Version' );
		$this->info[ 'cap' ] 		 = 'backwpup_jobs_edit';

	}

	/**
	 * The name of the last button (execute button)
	 *
	 * @param $wizard_settings
	 * @return string
	 */
	public function get_last_button_name( $wizard_settings ) {

		return  __( 'Import', 'backwpup' );
	}

	/**
	 * with steps has the wizard to to
	 */
	public function get_steps( $wizard_settings ) {

		$steps = array();
		$steps[0] = array( 'id' => 'FILE', 'name' => __( 'Import File', 'backwpup' ), 'description' => __( 'Upload XML job file for import', 'backwpup' ) );
		$steps[1] = array( 'id' => 'SELECT', 'name' => __( 'Select items to import', 'backwpup' ), 'description' => __( 'Select which job should be imported or overwritten.', 'backwpup' ) );

		return $steps;
	}


	/**
	 * called on page
	 */
	public function page( $wizard_settings ) {

		$import_xml = NULL;

		if ( $wizard_settings[ 'wizard' ][ 'step' ] == 'FILE' ) {
			$bytes = apply_filters( 'import_upload_size_limit', wp_max_upload_size() );
			$size = size_format( $bytes );
			?>
			<table class="form-table">
				<tr>
					<td>
						<p><?php _e( 'Please upload your BackWPup job XML export file and we&#8217;ll import the jobs into BackWPup.', 'backwpup' ); ?></p>
						<p>
							<label for="upload"><?php _e( 'Choose a file from your computer:', 'backwpup' ); ?></label> (<?php printf( __('Maximum size: %s', 'backwpup' ), $size ); ?>)
							<input type="file" id="upload" name="import" />
							<input type="hidden" name="max_file_size" value="<?php echo $bytes; ?>" />
						</p>
					</td>
				</tr>
			</table>
			<?php
		}

		if ( $wizard_settings[ 'wizard' ][ 'step' ] == 'SELECT' ) {

			?>
			<table class="form-table">
				<tr>
					<td>
			<?php

			if ( !empty(  $wizard_settings[ 'file' ][ 'file' ] ) )
				$import_xml = simplexml_load_file( $wizard_settings[ 'file' ][ 'file' ] );

			if ( is_object($import_xml) && ! empty( $import_xml->job ) ) {
             	echo '<h3>' .  __( 'Import Jobs', 'backwpup' ) . '</h3>';
				$jobids = BackWPup_Option::get_job_ids();
				foreach ( $import_xml->job as $job ) {
					echo "<select name=\"importtype[" . $job->jobid . "]\" title=\"" . __( 'Import Type', 'backwpup' ) . "\"><option value=\"not\">" . __( 'No Import', 'backwpup' ) . "</option>";
					if ( in_array( $job->jobid, $jobids ) )
						echo "<option value=\"over\">" . __( 'Overwrite', 'backwpup' ) . "</option><option value=\"append\">" . __( 'Append', 'backwpup' ) . "</option>";
					else
						echo "<option value=\"over\">" . __( 'Import', 'backwpup' ) . "</option>";
					echo "</select>";
					echo '&nbsp;<span class="description">' . $job->jobid . ". " . $job->name . '</span><br />';
				}
			}

			if ( is_object($import_xml) && ! empty( $import_xml->config ) ) {
				?>
					</td>
				</tr>
				<tr>
					<td>
						<h3><?php _e( 'Import Config', 'backwpup' ); ?></h3>
						<p>
							<input type="checkbox" value="1" name="import_config" id="import-config" />
							<label for="import-config"><?php _e( 'Import BackWPup configuration', 'backwpup' ); ?></label>
						</p>
				<?php
			}
			?>
					</td>
				</tr>
			</table>
			<?php
		}

	}

	/**
	 * called on page load to save form data
	 */
	public function save( $wizard_settings ) {

		if ( isset( $wizard_settings[ 'wizard' ][ 'step'] ) && $wizard_settings[ 'wizard' ][ 'step' ] == 'FILE' ) {

			if ( empty( $_FILES['import'] ) ) {
				BackWPup_Admin::message( __( 'File is empty. Please upload something more substantial. This error could also caused by uploads being disabled in your php.ini or by post_max_size being defined as smaller than upload_max_filesize in php.ini.', 'backwpup' ), TRUE );

				return $wizard_settings;
			}

			$overrides = array( 'test_form' => FALSE, 'test_type' => FALSE );
			//$_FILES['import']['name'] .= '.txt';
			$wizard_settings[ 'file' ] = wp_handle_upload( $_FILES['import'], $overrides );

			if ( isset( $wizard_settings[ 'file' ][ 'error' ] ) ) {
				BackWPup_Admin::message( esc_html( $wizard_settings[ 'file' ][ 'error' ] ) );

				return $wizard_settings;
			}
			else if ( ! is_readable( $wizard_settings[ 'file' ][ 'file' ] ) ) {
				BackWPup_Admin::message( __( 'The export file could not be found at <code>%s</code>. This is likely due to an issue with permissions.', 'backwpup' ), esc_html( $wizard_settings[ 'file' ][ 'file' ] ), TRUE );

				return $wizard_settings;
			}

			$import_xml = simplexml_load_file( $wizard_settings[ 'file' ][ 'file' ] );
			if ( ! is_object( $import_xml ) ) {
				BackWPup_Admin::message( __( 'Sorry, there has been a phrase error.', 'backwpup' ), TRUE );

				return $wizard_settings;
			}


			if ( version_compare( $import_xml[ 'version' ], '3.0', '<=' ) ) {
				BackWPup_Admin::message( sprintf( __( 'This Export file (version %s) may not be supported by this version of the importer.', 'backwpup' ), esc_html( $import_xml[ 'version' ] ) ), TRUE );

				return $wizard_settings;
			}

			if ( !isset( $import_xml[ 'plugin' ] ) || $import_xml[ 'plugin' ] != 'BackWPup' ) {
				BackWPup_Admin::message( sprintf( __( 'This is not a BackWPup XML file', 'backwpup' ), esc_html( $import_xml[ 'version' ] ) ), TRUE );

				return $wizard_settings;
			}
		}

		if ( isset( $wizard_settings[ 'wizard' ][ 'step'] ) && $wizard_settings[ 'wizard' ][ 'step' ] == 'SELECT' ) {

			$wizard_settings[ 'select' ][ 'import_config' ] = !empty( $_POST[ 'import_config' ] ) ? TRUE : FALSE;

			if ( is_array( $_POST[ 'importtype' ] ) ) {
				$wizard_settings[ 'select' ][ 'importtype' ] = $_POST[ 'importtype' ];
			}
		}

		return $wizard_settings;
	}

	/**
	 * called if last button clicked
	 */
	public function execute( $wizard_settings ) {

		if ( !empty(  $wizard_settings[ 'file' ][ 'file' ] ) )
			$import_xml = simplexml_load_file( $wizard_settings[ 'file' ][ 'file' ] );

		if ( empty( $import_xml ) ) {

			return;
		}

		foreach ( $wizard_settings[ 'select' ][ 'importtype' ] as $id => $type ) {
			if ( $type == 'not' || empty( $type ) )
				continue;
			//get data from xml
			foreach ( $import_xml->job as $job ) {
				if ( $job->jobid != $id )
					continue;
				foreach ( $job as $key => $option ) {
					$import[ $id ][ $key ] = maybe_unserialize( (string)$option );
				}
				break;
			}
			if ( $type == 'append' ) {
				$newjobid = BackWPup_Option::get_job_ids();
				sort( $newjobid );
				$import[ $id ][ 'jobid' ] = end( $newjobid ) + 1;
			}
			$import[ $id ][ 'activetype' ] = '';
			unset( $import[ $id ][ 'cronnextrun' ] );
			unset( $import[ $id ][ 'starttime' ] );
			unset( $import[ $id ][ 'logfile' ] );
			unset( $import[ $id ][ 'lastrun' ] );
			unset( $import[ $id ][ 'lastruntime' ] );
			unset( $import[ $id ][ 'lastbackupdownloadurl' ] );
			foreach ( $import[ $id ] as $jobname => $jobvalue ) {
				BackWPup_Option::update( $import[ $id ][ 'jobid' ], $jobname, $jobvalue );
			}
			//delete xml file
			unlink( $wizard_settings[ 'file' ][ 'file' ] );

			echo '<div id="message" class="updated below-h2"><p>' . sprintf( __( 'Job %1$s with id %2$d imported', 'backwpup'), $import[ $id ][ 'name' ], $import[ $id ][ 'jobid' ] ) . '</p></div>';
		}

		//get data from xml
		if ( $wizard_settings[ 'select' ][ 'import_config' ] ) {
			foreach ( (array)$import_xml->config as $key => $option ) {
				update_site_option( 'backwpup_cfg_' . $key, maybe_unserialize( (string)$option ) );
			}
			echo '<div id="message" class="updated below-h2"><p>' . __( 'BackWPup config imported','backwpup') . '</p></div>';
		}

	}

	/**
	 * @param $wizard_settings
	 */
	public function cancel( $wizard_settings ) {

		//delete xml file
		if ( ! empty( $wizard_settings[ 'file' ][ 'file' ] ) )
			unlink( $wizard_settings[ 'file' ][ 'file' ] );
	}
}
