<?php
/**
 *
 */
class BackWPup_Pro_Destination_Folder extends BackWPup_Destination_Folder {


	/**
	 * @param $job_settings
	 */
	public function wizard_page( $job_settings ) {

		?>
		<table class="form-table">
			<tr>
				<td>
					<fieldset>
						<label for="backupdir"><?php _e( 'Absolute path to folder for backup files:', 'backwpup' ); ?></label><br/>
						<input name="backupdir" id="backupdir" type="text" value="<?php echo esc_attr( $job_settings[ 'backupdir' ] );?>"
							   class="large-text"/>
						<br/>

						<?php if ( $job_settings[ 'backuptype' ] == 'archive' ) { ?>
							<label for="idmaxbackups"><input name="maxbackups" id="idmaxbackups" type="text" size="3" value="<?php echo esc_attr( $job_settings[ 'maxbackups' ] );?>" class="small-text help-tip" title="<?php esc_attr_e( 'Oldest files will be deleted first. 0 = no deletion', 'backwpup' ); ?>" />
							<?php  _e( 'Number of files to keep in folder.', 'backwpup' ); ?></label>
							<br/>
						<?php } else { ?>
							<label for="idbackupsyncnodelete"><input class="checkbox" value="1" id="idbackupsyncnodelete"
								   type="checkbox" <?php checked(  $job_settings[ 'backupsyncnodelete' ], TRUE ); ?>
								   name="backupsyncnodelete" /> <?php _e( 'Do not delete files while syncing to destination!', 'backwpup' ); ?></label>
							<br/>
						<?php } ?>
					</fieldset>
				</td>
			</tr>
		</table>
		<?php
	}


	/**
	 * @param $job_settings
	 */
	public function wizard_save( $job_settings ) {

		$_POST[ 'backupdir' ] = trailingslashit( str_replace( '//', '/', str_replace( '\\', '/', trim( stripslashes( $_POST[ 'backupdir' ] ) ) ) ) );
		if ( $_POST[ 'backupdir' ][ 0 ] == '.' || ( $_POST[ 'backupdir' ][ 0 ] != '/' && ! preg_match( '#^[a-zA-Z]:/#', $_POST[ 'backupdir' ] ) ) )
			$_POST[ 'backupdir' ] = trailingslashit( str_replace( '\\', '/', WP_CONTENT_DIR ) ) . $_POST[ 'backupdir' ];
		if ( $_POST[ 'backupdir' ] == '/' || $_POST[ 'backupdir' ] == trailingslashit( str_replace( '\\', '/', WP_CONTENT_DIR ) ) || $_POST[ 'backupdir' ] == trailingslashit( str_replace( '\\', '/', ABSPATH ) ) )
			$_POST[ 'backupdir' ] = '';
		$job_settings[ 'backupdir' ] = $_POST[ 'backupdir' ];
		if ( isset( $_POST[ 'maxbackups' ] ) )
			$job_settings[ 'maxbackups' ] = isset( $_POST[ 'maxbackups' ] ) ? (int)$_POST[ 'maxbackups' ] : 0;
		if ( isset( $_POST[ 'backupsyncnodelete' ] ) )
			$job_settings[ 'backupsyncnodelete' ] = ( isset( $_POST[ 'backupsyncnodelete' ] ) && $_POST[ 'backupsyncnodelete' ] == 1 ) ? TRUE : FALSE;

		return $job_settings;
	}


	/**
	 * @param BackWPup_Job $job_object
	 * @return bool
	 */
	public function job_run_sync( &$job_object ) {
		global $files_in_sync_folder;

		$job_object->substeps_todo = $job_object->count_folder + count( $job_object->additional_files_to_backup ) + 1;
		$job_object->substeps_done = 0;

		if ( $job_object->steps_data[ $job_object->step_working ]['SAVE_STEP_TRY'] != $job_object->steps_data[ $job_object->step_working ][ 'STEP_TRY' ] )
			$job_object->log( sprintf( __( '%d. Try to sync files to folder&#160;&hellip;', 'backwpup' ), $job_object->steps_data[ $job_object->step_working ][ 'STEP_TRY' ] ), E_USER_NOTICE );

		//make a list of files#
		if ( $job_object->steps_data[ $job_object->step_working ]['SAVE_STEP_TRY'] != $job_object->steps_data[ $job_object->step_working ][ 'STEP_TRY' ] )
			$job_object->log( __( 'Retrieving file list from folder', 'backwpup'  ), E_USER_NOTICE );
		$this->files_in_sync_folder( $job_object->job[ 'backupdir' ] );

		//Sync files
		//go folder by folder
		if ( $job_object->steps_data[ $job_object->step_working ]['SAVE_STEP_TRY'] != $job_object->steps_data[ $job_object->step_working ][ 'STEP_TRY' ] )
			$job_object->log( __( 'Copy changed files to folder', 'backwpup'  ), E_USER_NOTICE );
		foreach( $job_object->get_folders_to_backup() as $folder_to_backup ) {
			$files_in_folder = $job_object->get_files_in_folder( $folder_to_backup );
			foreach( $files_in_folder as $file_in_folder ) {
				//crate file name on destination
				$dest_file_name =  $job_object->job[ 'backupdir' ] . ltrim( str_replace( $job_object->remove_path, '', $file_in_folder ), '/' );
				//Upload file is not exits or the same
				if ( ! isset( $files_in_sync_folder[ utf8_encode( $dest_file_name ) ] ) || ( isset( $files_in_sync_folder[ utf8_encode( $dest_file_name ) ] ) && $files_in_sync_folder[ utf8_encode( $dest_file_name ) ] != filesize( $file_in_folder ) ) ) {
					//make dir if needed
					if ( ! is_dir( dirname( $dest_file_name ) ) )
						wp_mkdir_p( dirname( $dest_file_name ) );
					//copy file
					copy( $file_in_folder, $dest_file_name );
					$job_object->log( sprintf( __( 'File %s copied', 'backwpup' ), $dest_file_name ), E_USER_NOTICE );
					$job_object->do_restart_time();
				}
				//remove from array
				if ( isset( $files_in_sync_folder[ utf8_encode( $dest_file_name ) ] ) )
					unset( $files_in_sync_folder[ utf8_encode( $dest_file_name ) ]);
			}
			$job_object->substeps_done ++;
			$job_object->do_restart_time();
		}

		//sync extra files
		if ( ! empty( $job_object->additional_files_to_backup ) ) {
			$job_object->log( __( 'Delete not existing files from folder', 'backwpup'  ), E_USER_NOTICE );
			foreach ( $job_object->additional_files_to_backup as $file ) {
				if ( isset( $files_in_sync_folder[ utf8_encode( $job_object->job[ 'backupdir' ] . basename( $file ) ) ] ) && filesize( $file ) ==  $files_in_sync_folder[ utf8_encode( $job_object->job[ 'backupdir' ] . basename( $file ) ) ] ) {
					unset( $files_in_sync_folder[ utf8_encode( $job_object->job[ 'backupdir' ] . basename( $file ) ) ]);
					$job_object->substeps_done ++;
					continue;
				}
				copy( $file, $job_object->job[ 'backupdir' ] . basename( $file ) );
				$job_object->log( sprintf( __( 'Extra file %s copied', 'backwpup' ), basename( $file ) ), E_USER_NOTICE );
				if ( isset( $files_in_sync_folder[ utf8_encode( $job_object->job[ 'backupdir' ] . basename( $file ) ) ] ) )
					unset( $files_in_sync_folder[ utf8_encode( $job_object->job[ 'backupdir' ] . basename( $file ) ) ]);
				$job_object->substeps_done ++;
				$job_object->do_restart_time();
			}
		}

		//delete rest files
		if ( ! $job_object->job[ 'backupsyncnodelete' ] ) {
			$dest_files = array_keys( $files_in_sync_folder );
			foreach( $dest_files as $dest_file ) {
				unlink( utf8_decode( $dest_file ) );
				$job_object->log( sprintf( __( 'File %s deleted from folder', 'backwpup' ), utf8_decode( $dest_file ) ), E_USER_NOTICE );
				$job_object->do_restart_time();
			}
			//delete empty folder
			$this->delete_empty_folder_in_sync_folder( $job_object->job[ 'backupdir' ], $job_object );
		}
		$job_object->substeps_done ++;

		return TRUE;
	}

	/**
 * Helper method to get all files already in the folder
 *
 * @param $folder string Folder name
 * @return void
 */
	private function files_in_sync_folder( $folder ) {
		global $files_in_sync_folder;

		if ( $dir = opendir( $folder ) ) {
			while ( FALSE !== ( $file = readdir( $dir ) ) ) {
				if ( in_array( $file, array( '.', '..' ) ) )
					continue;
				if ( is_dir( $folder . $file ) ) {
					$this->files_in_sync_folder(  trailingslashit( $folder . $file ) );
				} elseif ( is_readable( $folder . $file ) && ! is_link( $folder . $file ) ) {
					$files_in_sync_folder[ utf8_encode( $folder . $file ) ] = filesize( $folder . $file );
				}
			}
			closedir( $dir );
		}
	}

	/**
	 * Helper method to delete empty folder
	 *
	 * @param $folder string Folder name
	 * @param $job_object BackWPup_job
	 * @return bool the folder is deleted
	 */
	private function delete_empty_folder_in_sync_folder( $folder, $job_object ) {
		$entry_count = 0;
		if ( $dir = opendir( $folder ) ) {
			while ( FALSE !== ( $file = readdir( $dir ) ) ) {
				if ( in_array( $file, array( '.', '..' ) ) )
					continue;
				if ( is_dir( $folder . $file ) ) {
					$deleted = $this->delete_empty_folder_in_sync_folder( trailingslashit( $folder . $file ), $job_object );
					if ( $deleted )
						$entry_count--;
				}
				$entry_count++;
			}
			closedir( $dir );
			if ( $entry_count <= 0  ) {
				rmdir( untrailingslashit( $folder )  );
				$job_object->log( sprintf( __( 'Empty folder %s deleted', 'backwpup' ), untrailingslashit( $folder ) ), E_USER_NOTICE );
				return TRUE;
			}
		}
		return FALSE;
	}


}
