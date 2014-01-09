<?php
/**
 * Documentation: https://www.dropbox.com/developers/reference/api
 */
class BackWPup_Pro_Destination_Dropbox extends BackWPup_Destination_Dropbox {

	/**
	 * @param $job_settings
	 */
	public function wizard_page( $job_settings ) {
		//drobox auth if getted from dropbox
		if ( isset( BackWPup_Pro_Page_Wizard::$wiz_data[ 'dropbox_auth' ][ 'oAuthRequestToken' ] ) ) {
			//Get Access Tokens
			try {
				$dropbox    = new BackWPup_Destination_Dropbox_API( 'sandbox' );
				$oAuthStuff = $dropbox->oAuthAccessToken( BackWPup_Pro_Page_Wizard::$wiz_data[ 'dropbox_auth' ][ 'oAuthRequestToken' ],  BackWPup_Pro_Page_Wizard::$wiz_data[ 'dropbox_auth' ][ 'oAuthRequestTokenSecret' ] );
				//Save Tokens
				echo '<input type="hidden" name="dropboxtoken" value="' . esc_attr( $oAuthStuff[ 'oauth_token' ] ) . '" />';
				echo '<input type="hidden" name="dropboxsecret" value="' . esc_attr( BackWPup_Encryption::encrypt( $oAuthStuff[ 'oauth_token_secret' ] ) ) . '" />';
				echo '<input type="hidden" name="dropboxroot" value="sandbox" />';
				echo '<div id="message" class="updated below-h2"><p>' .  __( 'Dropbox authentication complete!', 'backwpup' ) . '<p></div>';
			}
			catch ( Exception $e ) {
				echo '<div id="message" class="error"><p>' . sprintf( __( 'Dropbox API: %s', 'backwpup' ), $e->getMessage() ) . '</p></div>';
			}
		}
		if ( isset( $_GET[ 'not_approved' ] ) && $_GET[ 'not_approved' ] == 'true' ) {
			echo '<div id="message" class="error"><p>' . __( 'Dropbox authentication not approved!', 'backwpup' ) . '</p></div>';
		}

		//get auth url
		try {
			$dropbox = new BackWPup_Destination_Dropbox_API( 'sandbox' );
			// let the user authorize (user will be redirected)
			$response_sandbox = $dropbox->oAuthAuthorize( network_admin_url( 'admin.php' ) . '?page=backwpupwizard&BackWPup_Wizard_ID=' . BackWPup_Pro_Page_Wizard::$wiz_data_id );
			// save oauth_token_secret
			BackWPup_Pro_Page_Wizard::$wiz_data[ 'dropbox_auth' ] = array(
															 'oAuthRequestToken'       => $response_sandbox[ 'oauth_token' ],
															 'oAuthRequestTokenSecret' => $response_sandbox[ 'oauth_token_secret' ]
														);
		}
		catch ( Exception $e ) {
			echo '<div id="message" class="error"><p>' . sprintf( __( 'Dropbox API: %s', 'backwpup' ), $e->getMessage() ) . '</p></div>';
		}

		//display if not automatized
		?>
		<table class="form-table">
			<tr>
				<td>
					<fieldset>
					<?php
					if ( ! $job_settings[ 'dropboxtoken' ] && ! $job_settings[ 'dropboxsecret' ] && ! isset( $oAuthStuff[ 'oauth_token' ] ) ) {
						?>
						<strong><?php _e( 'Login:', 'backwpup' ); ?></strong>&nbsp;
						<span style="color:red;"><?php _e( 'Not authenticated!', 'backwpup' ); ?></span>
						<a class="button secondary" href="<?php echo esc_url( $response_sandbox[ 'authurl' ] );?>"><?php _e( 'Authenticate', 'backwpup' ); ?></a>
						<a href="http://db.tt/8irM1vQ0"><?php _e( 'Create Account', 'backwpup' ); ?></a>
						<br/>
						<?php
						} else {
							?>
						<strong><?php _e( 'Login:', 'backwpup' ); ?></strong>&nbsp;
						<span style="color:green;"><?php _e( 'Authenticated!', 'backwpup' ); ?></span>
						<a class="button secondary" href="<?php echo esc_url( $response_sandbox[ 'authurl' ] );?>"><?php _e( 'Reauthenticate', 'backwpup' ); ?></a>
						<br/>
						<strong><label for="iddropboxdir"><?php _e( 'Folder:', 'backwpup' ); ?></strong><br/>
						<input name="dropboxdir" id="iddropboxdir" type="text" value="<?php echo esc_attr( $job_settings[ 'dropboxdir' ] );?>" class="user large-text"/></label><br/>
						<?php
							if ( $job_settings[ 'backuptype' ] == 'archive' ) { ?>
								<label for="iddropboxmaxbackups"><input name="dropboxmaxbackups" id="iddropboxmaxbackups" type="text" size="3" value="<?php echo esc_attr( $job_settings[ 'dropboxmaxbackups' ] );?>" class="small-text help-tip" title="<?php esc_attr_e( 'Oldest files will be deleted first. 0 = no deletion', 'backwpup' ); ?>" />
								<?php  _e( 'Number of files to keep in folder.', 'backwpup' ); ?></label>
							<?php } else { ?>
								<label for="iddropboxsyncnodelete"><input class="checkbox" value="1" type="checkbox" <?php checked( $job_settings[ 'dropboxsyncnodelete' ], TRUE ); ?> name="dropboxsyncnodelete" id="iddropboxsyncnodelete" /> <?php _e( 'Do not delete files while syncing to destination!', 'backwpup' ); ?></label>
							<?php
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
	 * @return mixed
	 */
	public function wizard_save( $job_settings ) {

		$job_settings[ 'dropboxroot' ] = 'sandbox';
		if ( isset( $_POST[ 'dropboxtoken' ] ) ) {
			$job_settings[ 'dropboxtoken' ] = $_POST[ 'dropboxtoken' ];
			$job_settings[ 'dropboxsecret' ] = $_POST[ 'dropboxsecret' ];
		}

		if ( isset( $_POST[ 'dropboxdir' ] ) ) {
			$_POST[ 'dropboxdir' ] = trailingslashit( str_replace( '//', '/', str_replace( '\\', '/', trim( stripslashes( $_POST[ 'dropboxdir' ] ) ) ) ) );
			if ( substr( $_POST[ 'dropboxdir' ], 0, 1 ) == '/' )
				$_POST[ 'dropboxdir' ] = substr( $_POST[ 'dropboxdir' ], 1 );
			if ( $_POST[ 'dropboxdir' ] == '/' )
				$_POST[ 'dropboxdir' ] = '';
			$job_settings[ 'dropboxdir' ] = $_POST[ 'dropboxdir' ];
		}

		if ( isset( $_POST[ 'dropboxsyncnodelete' ] ) )
			$job_settings[ 'dropboxsyncnodelete' ] = ( isset( $_POST[ 'dropboxsyncnodelete' ] ) && $_POST[ 'dropboxsyncnodelete' ] == 1 ) ? TRUE : FALSE;
		if ( isset( $_POST[ 'dropboxmaxbackups' ] ) )
			$job_settings[ 'dropboxmaxbackups' ] = (int) $_POST[ 'dropboxmaxbackups' ];

		return $job_settings;
	}


	/**
	 * @param $job_object
	 * @return bool
	 */
	public function job_run_sync( &$job_object ) {
		global $folder_on_dropbox, $files_on_dropbox;

		$job_object->substeps_todo = $job_object->count_folder + $job_object->count_folder + count( $job_object->additional_files_to_backup ) + 1;
		$job_object->substeps_done = 0;

		BackWPup_Destination_Dropbox::$backwpup_job_object = &$job_object;

		if ( $job_object->steps_data[ $job_object->step_working ]['SAVE_STEP_TRY'] != $job_object->steps_data[ $job_object->step_working ][ 'STEP_TRY' ] )
			$job_object->log( sprintf( __( '%d. Try to sync files to Dropbox&#160;&hellip;', 'backwpup' ), $job_object->steps_data[ $job_object->step_working ][ 'STEP_TRY' ] ), E_USER_NOTICE );

		try {
			$dropbox = new BackWPup_Destination_Dropbox_API( $job_object->job[ 'dropboxroot' ] );
			// set the tokens
			$dropbox->setOAuthTokens( $job_object->job[ 'dropboxtoken' ], BackWPup_Encryption::decrypt( $job_object->job[ 'dropboxsecret' ] ) );

			//get account info
			if ( $job_object->steps_data[ $job_object->step_working ]['SAVE_STEP_TRY'] != $job_object->steps_data[ $job_object->step_working ][ 'STEP_TRY' ] ) {
				$info = $dropbox->accountInfo();
				if ( ! empty( $info[ 'uid' ] ) ) {
					$job_object->log( sprintf( __( 'Authenticated with Dropbox of user %s', 'backwpup' ), $info[ 'display_name' ] . ' (' . $info[ 'email' ] . ')' ), E_USER_NOTICE );
				}
				//Quota
				$dropboxfreespase = $info[ 'quota_info' ][ 'quota' ] - $info[ 'quota_info' ][ 'shared' ] - $info[ 'quota_info' ][ 'normal' ];
				$job_object->log( sprintf( __( '%s available on your Dropbox', 'backwpup' ), size_format( $dropboxfreespase, 2 ) ), E_USER_NOTICE );
			}

			//get files from dest
			$files_on_dropbox_save  = $files_on_dropbox  = $job_object->data_storage( 'dropbox_files', array() );
			$folder_on_dropbox_save = $folder_on_dropbox = $job_object->data_storage( 'dropbox_folder', array() );
			if ( empty( $job_object->steps_data[ $job_object->step_working ][ 'key_dropbox_folder' ] ) ) {
				$job_object->steps_data[ $job_object->step_working ][ 'key_dropbox_folder' ] = 0;
				$job_object->log( __( 'Retrieving file list from Dropbox', 'backwpup'  ), E_USER_NOTICE );
				$this->get_files_on_dropbox( $job_object->job[ 'dropboxdir' ], $job_object, $dropbox );
				$job_object->steps_data[ $job_object->step_working ][ 'key_dropbox_folder' ] = 0;
			}
			while ( $job_object->steps_data[ $job_object->step_working ][ 'key_dropbox_folder' ] < count( $folder_on_dropbox ) ) {
				$next_folder = $folder_on_dropbox[ $job_object->steps_data[ $job_object->step_working ][ 'key_dropbox_folder' ] ];
				$this->get_files_on_dropbox( $next_folder, $job_object, $dropbox );
				$job_object->substeps_done = $job_object->steps_data[ $job_object->step_working ][ 'key_dropbox_folder' ];
			}
			$job_object->substeps_done = $job_object->count_folder;

			//Sync files
			//go folder by folder
			if ( $job_object->steps_data[ $job_object->step_working ]['SAVE_STEP_TRY'] != $job_object->steps_data[ $job_object->step_working ][ 'STEP_TRY' ] )
				$job_object->log( __( 'Upload changed files to Dropbox', 'backwpup'  ), E_USER_NOTICE );
			foreach( $job_object->get_folders_to_backup() as $folder_to_backup ) {
				$files_in_folder = $job_object->get_files_in_folder( $folder_to_backup );
				//remove upload folder from list
				$dest_folder_name = $job_object->job[ 'dropboxdir' ] . trim( str_replace( $job_object->remove_path, '', $folder_to_backup ), '/' );
				foreach( $folder_on_dropbox as $key => $dest_folder ) {
					if ( strstr( $dest_folder, $dest_folder_name ) )
						unset( $folder_on_dropbox[ $key ] );
				}
				foreach( $files_in_folder as $file_in_folder ) {
					//crate file name on destination
					$dest_file_name =  $job_object->job[ 'dropboxdir' ] . ltrim( str_replace( $job_object->remove_path, '', $file_in_folder ), '/' );
					//Upload file is not exits or the same
					if ( ! isset( $files_on_dropbox[ utf8_encode( $dest_file_name ) ] ) || ( isset( $files_on_dropbox[ utf8_encode( $dest_file_name ) ] ) && $files_on_dropbox[ utf8_encode( $dest_file_name ) ] != filesize( $file_in_folder ) ) ) {
						$response = $dropbox->upload( $file_in_folder, $dest_file_name );
						if ( $response[ 'bytes' ] == filesize( $file_in_folder ) ) {
							$files_on_dropbox_save[ utf8_encode( $dest_file_name ) ] = filesize( $file_in_folder );
							$job_object->data_storage( 'dropbox_files', $files_on_dropbox_save );
							if ( ! in_array( trim( dirname( $dest_file_name ), '/') , $folder_on_dropbox_save ) ) {
								$folder_on_dropbox_save[] = trim( dirname( $dest_file_name ), '/' );
								$job_object->data_storage( 'dropbox_folder', $folder_on_dropbox_save );
							}
							$job_object->log( sprintf( __( 'File %s uploaded to Dropbox', 'backwpup' ), $dest_file_name ), E_USER_NOTICE );
							$job_object->do_restart_time();
						}
					}
					//remove from array
					if ( isset( $files_on_dropbox[ utf8_encode( $dest_file_name ) ] ) )
						unset( $files_on_dropbox[ utf8_encode( $dest_file_name ) ]);
				}
				$job_object->substeps_done ++;
				$job_object->do_restart_time();
			}

			//sync extra files
			if ( ! empty( $job_object->additional_files_to_backup ) ) {
				foreach ( $job_object->additional_files_to_backup as $file ) {
					if ( isset( $files_on_dropbox[ utf8_encode( $job_object->job[ 'dropboxdir' ] . basename( $file ) ) ] ) && filesize( $file ) ==  $files_on_dropbox[ utf8_encode( $job_object->job[ 'dropboxdir' ] . basename( $file ) ) ] ) {
						unset( $files_on_dropbox[ utf8_encode( $job_object->job[ 'dropboxdir' ] . basename( $file ) ) ]);
						$job_object->substeps_done ++;
						continue;
					}
					$response = $dropbox->upload( $file, $job_object->job[ 'dropboxdir' ] . basename( $file ) );
					if ( $response[ 'bytes' ] == filesize( $file ) ) {
						if ( isset( $files_on_dropbox[ utf8_encode( $job_object->job[ 'dropboxdir' ] . basename( $file ) ) ] ) )
							unset( $files_on_dropbox[ utf8_encode( $job_object->job[ 'dropboxdir' ] . basename( $file ) ) ] );
						$files_on_dropbox_save[ utf8_encode( $job_object->job[ 'dropboxdir' ] . basename( $file ) ) ] = filesize( $file );
						$job_object->data_storage( 'dropbox_files', $files_on_dropbox_save );
						$job_object->substeps_done ++;
						$job_object->log( sprintf( __( 'Extra file %s uploaded to Dropbox', 'backwpup' ), $job_object->job[ 'dropboxdir' ] . basename( $file ) ), E_USER_NOTICE );
					}
					$job_object->do_restart_time();
				}
			}

			//delete rest files
			if ( ! $job_object->job[ 'dropboxsyncnodelete' ] ) {
				if ( $job_object->steps_data[ $job_object->step_working ]['SAVE_STEP_TRY'] != $job_object->steps_data[ $job_object->step_working ][ 'STEP_TRY' ] )
					$job_object->log( __( 'Delete not existing files from Dropbox', 'backwpup'  ), E_USER_NOTICE );
				//delete folders with files
				foreach( $folder_on_dropbox as $dest_folder_name ) {
					$response = $dropbox->fileopsDelete( $dest_folder_name ); //delete folder on Cloud
					unset ( $files_on_dropbox_save[ array_search( $dest_folder_name, $files_on_dropbox_save ) ] );
					$job_object->data_storage( 'dropbox_files', $files_on_dropbox_save );
					if ( $response[ 'is_deleted' ] == 'true' ) {
						$job_object->log( sprintf( __( 'Folder %s deleted from Dropbox', 'backwpup' ), $dest_folder_name ), E_USER_NOTICE );
						//remove deleted files from lists
						foreach( $files_on_dropbox as $dest_file => $dest_file_size ) {
							if ( strstr( utf8_decode( $dest_file ), $dest_folder_name ) )
								unset( $files_on_dropbox[ $dest_file ] );
						}
						foreach( $files_on_dropbox_save as $dest_file => $dest_file_size ) {
							if ( strstr( utf8_decode( $dest_file ), $dest_folder_name ) )
								unset( $files_on_dropbox_save[ $dest_file ] );
						}
						$job_object->data_storage( 'dropbox_files', $files_on_dropbox_save );
					}
					$job_object->do_restart_time();
				}
				//delete files
				foreach( $files_on_dropbox as $dest_file => $dest_file_size ) {
					$response = $dropbox->fileopsDelete( utf8_decode( $dest_file ) ); //delete files on Cloud
					if ( $response[ 'is_deleted' ] == 'true' ) {
						$job_object->log( sprintf( __( 'File %s deleted from Dropbox', 'backwpup' ), utf8_decode( $dest_file ) ), E_USER_NOTICE );
						unset ( $files_on_dropbox_save[ $dest_file ]);
						$job_object->data_storage( 'dropbox_files', $files_on_dropbox_save );
					}
					$job_object->do_restart_time();
				}
			}
			$job_object->substeps_done ++;

		}
		catch ( Exception $e ) {
			$job_object->log( E_USER_ERROR, sprintf( __( 'Dropbox API: %s', 'backwpup' ), htmlentities( $e->getMessage() ) ), $e->getFile(), $e->getLine() );

			return FALSE;
		}

		return TRUE;
	}

	/**
	 * Helper method to get file list recursively from Dropbox
	 *
	 * @param $folder
	 * @param $job_object BackWPup_Job
	 * @param $dropbox BackWPup_Destination_Dropbox_API
	 */
	private function get_files_on_dropbox( $folder, $job_object, $dropbox ) {
		global $folder_on_dropbox, $files_on_dropbox;

		$metadata  	= $dropbox->metadata( $folder );
		$folder_key = count( $folder_on_dropbox );
		if ( is_array( $metadata ) ) {
			foreach ( $metadata[ 'contents' ] as $data ) {
				if ( $data[ 'is_dir' ] != TRUE ) {
					$files_on_dropbox[ utf8_encode( ltrim( $data[ 'path' ], '/' ) ) ] = $data[ 'bytes' ];
				} else {
					$folder_on_dropbox[ $folder_key ] = trim( $data[ 'path' ], '/' );
					$folder_key++;
				}
			}
		}
		$job_object->data_storage( 'dropbox_files', $files_on_dropbox );
		$job_object->data_storage( 'dropbox_folder', $folder_on_dropbox );
		$job_object->steps_data[ $job_object->step_working ][ 'key_dropbox_folder' ] ++;

		$job_object->do_restart_time();
		$job_object->update_working_data();
	}

}
