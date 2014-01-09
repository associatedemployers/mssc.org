<?php
/**
 *
 */
class BackWPup_Pro_Destination_RSC extends BackWPup_Destination_RSC {

	/**
	 * @return array
	 */
	public function option_defaults() {
		return array( 'rscusername' => '', 'rscapikey' => '', 'rsccontainer' => '', 'rscregion' => 'DFW', 'rscdir' => trailingslashit( sanitize_file_name( get_bloginfo( 'name' ) ) ), 'rscmaxbackups' => 0, 'rscsyncnodelete' => TRUE );
	}

	/**
	 * @param $job_settings
	 */
	public function wizard_page( $job_settings ) {
		?>
		<table class="form-table">
			<tr>
				<td>
					<fieldset>
						<label for="rscusername"><?php _e( 'Username:', 'backwpup' ); ?><br/>
						<input id="rscusername" name="rscusername" type="text"
							   value="<?php echo esc_attr( $job_settings[ 'rscusername' ] ); ?>" class="large-text" /></label><br/>
						<?php _e( 'API Key:', 'backwpup' ); ?><br/>
						<label for="rscapikey"><input id="rscapikey" name="rscapikey" type="text"
							   value="<?php echo esc_attr( BackWPup_Encryption::decrypt( $job_settings[ 'rscapikey' ] ) );?>" class="large-text" /></label><br/>
						<label for="rscregion"><?php _e( 'Select region:', 'backwpup' ); ?><br />
						<select name="rscregion" id="rscregion" title="<?php _e( 'Rackspace Cloud Files Region', 'backwpup' ); ?>">
							<option value="DFW" <?php selected( 'DFW', $job_settings[ 'rscregion' ], TRUE ) ?>><?php _e( 'Dallas (DFW)', 'backwpup' ); ?></option>
							<option value="ORD" <?php selected( 'ORD', $job_settings[ 'rscregion' ], TRUE ) ?>><?php _e( 'Chicago (ORD)', 'backwpup' ); ?></option>
							<option value="SYD" <?php selected( 'SYD', $job_settings[ 'rscregion' ], TRUE ) ?>><?php _e( 'Sydney (SYD)', 'backwpup' ); ?></option>
							<option value="LON" <?php selected( 'LON', $job_settings[ 'rscregion' ], TRUE ) ?>><?php _e( 'London (LON)', 'backwpup' ); ?></option>
							<option value="IAD" <?php selected( 'IAD', $job_settings[ 'rscregion' ], TRUE ) ?>><?php _e( 'Northern Virginia (IAD)', 'backwpup' ); ?></option>

						</select></label><br/>
						<label for="rsccontainerselected"><?php _e( 'Container:', 'backwpup' ); ?><br/>
						<input id="rsccontainerselected" name="rsccontainerselected" type="hidden"
							   value="<?php echo esc_attr( $job_settings[ 'rsccontainer' ] );?>" /></label>
						<?php if ( $job_settings[ 'rscusername' ] && $job_settings[ 'rscapikey' ] ) $this->edit_ajax( array(
																															'rscusername' => $job_settings[ 'rscusername' ],
																															'rscregion' => $job_settings[ 'rscregion' ],
																															'rscapikey'   => BackWPup_Encryption::decrypt( $job_settings[ 'rscapikey' ] ),
																															'rscselected' => $job_settings[ 'rsccontainer' ]
																													   ) ); ?>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<label><?php _e( 'Create container:', 'backwpup' ); ?>
						<input name="newrsccontainer" type="text" value="" class="text" /></label><br/>
						<label for="idrscdir"><?php _e( 'Folder in container:', 'backwpup' ); ?><br/>
						<input name="rscdir" id="idrscdir" type="text" value="<?php echo esc_attr( $job_settings[ 'rscdir' ] );?>" class="large-text" /></label><br/>

						<?php
							if ( $job_settings[ 'backuptype' ] == 'archive' ) {
								?>
							<label for="idrscmaxbackups"><input name="rscmaxbackups" id="idrscmaxbackups" type="text" size="3" value="<?php echo esc_attr( $job_settings[ 'rscmaxbackups' ] );?>" class="small-text help-tip" title="<?php esc_attr_e( 'Oldest files will be deleted first. 0 = no deletion', 'backwpup' ); ?>" />
							<?php  _e( 'Number of files to keep in folder.', 'backwpup' ); ?></label>
							<br/>
						<?php } else { ?>
							<label for="idrscsyncnodelete"><input class="checkbox" value="1"
								   type="checkbox" <?php checked(  $job_settings[ 'rscsyncnodelete' ], TRUE ); ?>
								   name="rscsyncnodelete" id="idrscsyncnodelete" /> <?php _e( 'Do not delete files while syncing to destination!', 'backwpup' ); ?></label>
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

		$job_settings[ 'rscusername' ] = isset( $_POST[ 'rscusername' ] ) ? $_POST[ 'rscusername' ] : '';
		$job_settings[ 'rscapikey' ] = isset( $_POST[ 'rscapikey' ] ) ? BackWPup_Encryption::encrypt( $_POST[ 'rscapikey' ] ) : '';
		$job_settings[ 'rsccontainer' ] = isset( $_POST[ 'rsccontainer' ] ) ? $_POST[ 'rsccontainer' ] : '';
		$job_settings[ 'rscregion' ] = ! empty( $_POST[ 'rscregion' ] ) ? $_POST[ 'rscregion' ] : 'DFW';

		$_POST[ 'rscdir' ] = trailingslashit( str_replace( '//', '/', str_replace( '\\', '/', trim( stripslashes( $_POST[ 'rscdir' ] ) ) ) ) );
		if ( substr( $_POST[ 'rscdir' ], 0, 1 ) == '/' )
			$_POST[ 'rscdir' ] = substr( $_POST[ 'rscdir' ], 1 );
		if ( $_POST[ 'rscdir' ] == '/' )
			$_POST[ 'rscdir' ] = '';
		$job_settings[ 'rscdir' ] = $_POST[ 'rscdir' ];

		$job_settings[ 'rscmaxbackups' ] = isset( $_POST[ 'rscmaxbackups' ] ) ? (int)$_POST[ 'rscmaxbackups' ] : 0;
		$job_settings[ 'rscsyncnodelete' ] = ( isset( $_POST[ 'rscsyncnodelete' ] ) && $_POST[ 'rscsyncnodelete' ] == 1 ) ? TRUE : FALSE;

		if ( ! empty( $_POST[ 'rscusername' ] ) && ! empty( $_POST[ 'rscapikey' ] ) && ! empty( $_POST[ 'newrsccontainer' ] ) ) {
			try {
				$conn = new OpenCloud\Rackspace(
					self::get_auth_url_by_region( $_POST[ 'rscregion' ] ),
					array(
						 'username' =>  $_POST[ 'rscusername' ] ,
						 'apiKey' => $$_POST[ 'rscapikey' ]
					));
				$ostore = $conn->objectStoreService( 'cloudFiles' , $_POST[ 'rscregion' ], 'publicURL');

				$ostore->createContainer( $_POST[ 'newrsccontainer' ] );
				$job_settings[ 'rsccontainer' ] = $_POST[ 'newrsccontainer' ];
				BackWPup_Admin::message( sprintf( __( 'Rackspace Cloud container "%s" created.', 'backwpup' ), $_POST[ 'newrsccontainer' ] ) );

			}
			catch ( Exception $e ) {
				BackWPup_Admin::message( sprintf( __( 'Rackspace Cloud API: %s', 'backwpup' ), $e->getMessage() ), TRUE );
			}
		}

		return $job_settings;
	}



	/**
	 * @param $jobdest
	 * @return mixed
	 */
	public function file_get_list( $jobdest ) {

		return get_site_transient( 'backwpup_' . strtolower( $jobdest ) );
	}

	/**
	 * @param $job_object
	 * @return bool
	 */
	public function job_run_sync( &$job_object ) {

		$job_object->substeps_todo = $job_object->count_folder + count( $job_object->additional_files_to_backup ) + 2;
		$job_object->substeps_done = 0;

		if ( $job_object->steps_data[ $job_object->step_working ]['SAVE_STEP_TRY'] != $job_object->steps_data[ $job_object->step_working ][ 'STEP_TRY' ] )
			$job_object->log( sprintf( __( '%d. Trying to sync files to Rackspace cloud&#160;&hellip;', 'backwpup' ), $job_object->steps_data[ $job_object->step_working ][ 'STEP_TRY' ] ) );

		try {

			$conn = new OpenCloud\Rackspace(
				self::get_auth_url_by_region( $job_object->job[ 'rscregion' ] ),
				array(
					 'username' => $job_object->job[ 'rscusername' ],
					 'apiKey' => BackWPup_Encryption::decrypt( $job_object->job[ 'rscapikey' ] )
				));
			//connect to cloud files
			$ostore = $conn->objectStoreService( 'cloudFiles' , $job_object->job[ 'rscregion' ], 'publicURL');

			$container = $ostore->getContainer( $job_object->job[ 'rsccontainer' ] );
			if ( $job_object->steps_data[ $job_object->step_working ]['SAVE_STEP_TRY'] != $job_object->steps_data[ $job_object->step_working ][ 'STEP_TRY' ] )
				$job_object->log( sprintf(__( 'Connected to Rackspace cloud files container %s.', 'backwpup' ), $job_object->job[ 'rsccontainer' ] ) );
		}
		catch ( Exception $e ) {
			$job_object->log( E_USER_ERROR, sprintf( __( 'Rackspace Cloud API: %s', 'backwpup' ), htmlentities( $e->getMessage() ) ), $e->getFile(), $e->getLine() );

			return FALSE;
		}

		try {
			// get files from RSC
			$dest_files_save = $dest_files = $job_object->data_storage( 'files_rsc' );
			if ( empty( $job_object->steps_data[ $job_object->step_working ][ 'file_list_results' ] ) || $job_object->steps_data[ $job_object->step_working ][ 'file_list_results' ] == 10000 ) {
				if ( empty( $job_object->steps_data[ $job_object->step_working ][ 'file_list_results' ] ) ) {
					$job_object->log( __( 'Retrieving files list from Rackspace Cloud.', 'backwpup'  ), E_USER_NOTICE );
					$dest_files_save = array();
					$job_object->steps_data[ $job_object->step_working ][ 'file_list_results' ] = 0;
					$objlist    = $container->objectList( array( 'prefix' => $job_object->job[ 'rscdir' ] ) );
					while ( $object = $objlist->next() ) {
						$dest_files_save[ utf8_encode( $object->getName() ) ] = $object->getContentLength();
						$job_object->steps_data[ $job_object->step_working ][ 'file_list_results' ] ++;
					}
					$job_object->data_storage( 'files_rsc', $dest_files_save );
					$job_object->do_restart_time();
				}
				while ( $job_object->steps_data[ $job_object->step_working ][ 'file_list_results' ] == 10000 ) {
					$job_object->steps_data[ $job_object->step_working ][ 'file_list_results' ] = 0;
					$objlist        = $container->objectList( array( 'prefix' => $job_object->job[ 'rscdir' ], 'marker' => $job_object->steps_data[ $job_object->step_working ][ 'file_list_marker' ] ) );
					$job_object->steps_data[ $job_object->step_working ][ 'file_list_results' ] = $objlist->size();
					while ( $object = $objlist->next() ) {
						$dest_files_save[ utf8_encode( $object->getName() ) ] = $object->getContentLength();
						$job_object->steps_data[ $job_object->step_working ][ 'file_list_results' ] ++;
					}
					$job_object->data_storage( 'files_rsc', $dest_files_save );
					$job_object->do_restart_time();
				}
				$dest_files_save = $dest_files = $job_object->data_storage( 'files_rsc' );
			}
			$job_object->substeps_done ++;

			//Sync files
			//go folder by folder
			if ( $job_object->steps_data[ $job_object->step_working ]['SAVE_STEP_TRY'] != $job_object->steps_data[ $job_object->step_working ][ 'STEP_TRY' ] )
				$job_object->log( __( 'Upload changed files to Rackspace Cloud.', 'backwpup'  ) );
			foreach( $job_object->get_folders_to_backup() as $folder_to_backup ) {
				$files_in_folder = $job_object->get_files_in_folder( $folder_to_backup );
				foreach( $files_in_folder as $file_in_folder ) {
					//crate file name on destination
					$dest_file_name = $job_object->job[ 'rscdir' ] . ltrim( str_replace( $job_object->remove_path, '', $file_in_folder ), '/' );
					//Upload file is not exits or the same
					if ( ! isset( $dest_files[ utf8_encode( $dest_file_name ) ] ) || $dest_files[ utf8_encode( $dest_file_name ) ] != filesize( $file_in_folder ) ) {
						$handle = fopen( $file_in_folder, 'rb' );
						$uploded = $container->uploadObject( $dest_file_name, $handle );
						fclose( $handle );
						if ( $uploded ) {
							$job_object->log( sprintf( __( 'File %s uploaded to Rackspace Cloud.', 'backwpup' ), $dest_file_name ) );
							$dest_files_save[ utf8_encode( $dest_file_name ) ] = filesize( $file_in_folder );
							$job_object->data_storage( 'files_rsc', $dest_files_save );
							$job_object->do_restart_time();
						}
					}
					//remove from array
					if ( isset( $dest_files[ utf8_encode( $dest_file_name ) ] ) )
						unset( $dest_files[ utf8_encode( $dest_file_name ) ] );
				}
				$job_object->substeps_done ++;
			}


			//sync extra files
			if ( ! empty( $job_object->additional_files_to_backup ) ) {
				foreach ( $job_object->additional_files_to_backup as $file ) {
					if ( isset( $dest_files[ utf8_encode( $job_object->job[ 'rscdir' ] . basename( $file ) ) ] ) && filesize( $file ) ==  $dest_files[ utf8_encode( $job_object->job[ 'rscdir' ] . basename( $file ) ) ] ) {
						unset( $dest_files[ utf8_encode( $job_object->job[ 'rscdir' ] . basename( $file ) ) ]);
						$job_object->substeps_done ++;
						continue;
					}
					$handle = fopen( $file, 'rb' );
					$uploded = $container->uploadObject( $job_object->job[ 'rscdir' ] . basename( $file ), $handle );
					fclose( $handle );
					if ( $uploded ) {
						$job_object->log( sprintf( __( 'Extra file %s uploaded to Rackspace Cloud.', 'backwpup' ), basename( $file ) ) );
						if ( isset( $dest_files[ utf8_encode( $job_object->job[ 'rscdir' ] . basename( $file ) ) ] ) )
							unset( $dest_files[ utf8_encode( $job_object->job[ 'rscdir' ] . basename( $file ) ) ] );
						$dest_files_save[utf8_encode( $job_object->job[ 'rscdir' ] . basename( $file ) ) ] = filesize( $file );
						$job_object->data_storage( 'files_rsc', $dest_files_save );
						$job_object->do_restart_time();
						$job_object->substeps_done ++;
					}
					$job_object->substeps_done ++;
				}
			}

			//delete rest files
			if ( ! $job_object->job[ 'rscsyncnodelete' ] ) {
				$job_object->log( __( 'Delete nonexistent files from Rackspace Cloud.', 'backwpup'  ), E_USER_NOTICE );
				$dest_files = array_keys( $dest_files );
				foreach( $dest_files as $dest_file ) {
					$fileobject = $container->getObject( utf8_decode( $dest_file ) );
					if ( $fileobject->delete() ) {
						$job_object->log( sprintf( __( 'File %s deleted from Rackspace Cloud.', 'backwpup' ), utf8_decode( $dest_file ) ) );
						unset( $dest_files_save[ $dest_file ] );
						$job_object->data_storage( 'files_rsc', $dest_files_save );
						$job_object->do_restart_time();
					}
				}
			}
			$job_object->substeps_done ++;

		}
		catch ( Exception $e ) {
			$job_object->log( E_USER_ERROR, sprintf( __( 'Rackspace Cloud API: %s', 'backwpup' ), htmlentities( $e->getMessage() ) ), $e->getFile(), $e->getLine() );

			return FALSE;
		}


		return TRUE;
	}


	/**
	 *
	 */
	public function wizard_inline_js() {
		//<script type="text/javascript">
		?>
		function rscgetcontainer() {
			var data = {
				action: 'backwpup_dest_rsc',
				rscusername: $('#rscusername').val(),
				rscapikey: $('#rscapikey').val(),
    			rscregion: $('#rscregion').val(),
				rscselected: $('#rsccontainerselected').val(),
				_ajax_nonce: $('#backwpupajaxnonce').val()
			};
			$.post(ajaxurl, data, function(response) {
				$('#rsccontainererror').remove();
				$('#rsccontainer').remove();
				$('#rsccontainerselected').after(response);
			});
		}
    	$('#rscregion').change(function() {rscgetcontainer();});
		$('#rscusername').change(function() {rscgetcontainer();});
		$('#rscapikey').change(function() {rscgetcontainer();});
	<?php
	}
}
