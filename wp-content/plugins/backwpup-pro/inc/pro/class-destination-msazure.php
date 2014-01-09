<?php
/**
 * Documentation: http://www.windowsazure.com/en-us/develop/php/how-to-guides/blob-service/
 */
class BackWPup_Pro_Destination_MSAzure extends BackWPup_Destination_MSAzure {


	/**
	 * @param $job_settings
	 */
	public function wizard_page( $job_settings ) {
		?>
		<table class="form-table">
			<tr>
				<td>
					<fieldset>
						<label for="msazureaccname"><?php _e( 'Account Name:', 'backwpup' ); ?><br/>
						<input id="msazureaccname" name="msazureaccname" type="text" value="<?php echo esc_attr( $job_settings[ 'msazureaccname' ] );?>" class="large-text" autocomplete="off" /></label><br/>
						<label for="msazurekey"><?php _e( 'Access Key:', 'backwpup' ); ?><br/>
						<input id="msazurekey" name="msazurekey" type="password" value="<?php echo esc_attr( BackWPup_Encryption::decrypt( $job_settings[ 'msazurekey' ] ) );?>" class="large-text" autocomplete="off" /></label><br/>
						<label for="msazurecontainerselected"><?php _e( 'Container:', 'backwpup' ); ?><br/>
						<input id="msazurecontainerselected" name="msazurecontainerselected" type="hidden" value="<?php echo esc_attr( $job_settings[ 'msazurecontainer' ] );?>" /></label>
						<?php if ( $job_settings[ 'msazureaccname' ] && $job_settings[ 'msazurekey' ] ) $this->edit_ajax( array(
																															   'msazureaccname'  => $job_settings[ 'msazureaccname' ],
																															   'msazurekey'      => BackWPup_Encryption::decrypt( $job_settings[ 'msazurekey' ] ),
																															   'msazureselected' => $job_settings[ 'msazurecontainer' ]
																														  ) ); ?>
						&nbsp;&nbsp;&nbsp;<label><?php _e( 'Create container:', 'backwpup' ); ?>
						<input name="newmsazurecontainer" type="text" value="" class="text" /></label><br/>
						<label for="idmsazuredir"><?php _e( 'Folder in container:', 'backwpup' ); ?><br/>
						<input name="msazuredir" id="idmsazuredir" type="text" value="<?php echo esc_attr( $job_settings[ 'msazuredir' ] );?>" class="large-text" /></label><br/>
						<?php
							if ( $job_settings[ 'backuptype' ] == 'archive' ) {
								?>
							<label for="idmsazuremaxbackups"><input name="msazuremaxbackups" id="idmsazuremaxbackups" type="text" size="3" value="<?php echo  esc_attr( $job_settings[ 'msazuremaxbackups' ] );?>" class="small-text help-tip" title="<?php esc_attr_e( 'Oldest files will be deleted first. 0 = no deletion', 'backwpup' ); ?>" />
							<?php  _e( 'Number of files to keep in folder.', 'backwpup' ); ?></label>
							<br/>
							<?php } else { ?>
							<label for="idmsazuresyncnodelete"><input class="checkbox" value="1"
								   type="checkbox" <?php checked(  $job_settings[ 'msazuresyncnodelete' ], TRUE ); ?>
								   name="msazuresyncnodelete" id="idmsazuresyncnodelete" /> <?php _e( 'Do not delete files while syncing to destination!', 'backwpup' ); ?></label>
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

		$job_settings[ 'msazureaccname' ] = isset( $_POST[ 'msazureaccname' ] ) ? $_POST[ 'msazureaccname' ] : '';
		$job_settings[ 'msazurekey' ] = isset( $_POST[ 'msazurekey' ] ) ? BackWPup_Encryption::encrypt( $_POST[ 'msazurekey' ] ) : '';
		$job_settings[ 'msazurecontainer' ] = isset( $_POST[ 'msazurecontainer' ] ) ? $_POST[ 'msazurecontainer' ] : '';

		$_POST[ 'msazuredir' ] = trailingslashit( str_replace( '//', '/', str_replace( '\\', '/', trim( stripslashes( $_POST[ 'msazuredir' ] ) ) ) ) );
		if ( substr( $_POST[ 'msazuredir' ], 0, 1 ) == '/' )
			$_POST[ 'msazuredir' ] = substr( $_POST[ 'msazuredir' ], 1 );
		if ( $_POST[ 'msazuredir' ] == '/' )
			$_POST[ 'msazuredir' ] = '';
		$job_settings[ 'msazuredir' ] = $_POST[ 'msazuredir' ];

		$job_settings[ 'msazuremaxbackups' ] = isset( $_POST[ 'msazuremaxbackups' ] ) ? (int)$_POST[ 'msazuremaxbackups' ] : 0;
		$job_settings[ 'msazuresyncnodelete' ] = ( isset( $_POST[ 'msazuresyncnodelete' ] ) && $_POST[ 'msazuresyncnodelete' ] == 1 ) ? TRUE : FALSE;

		//create a new container
		if ( ! empty( $_POST[ 'newmsazurecontainer' ] ) && ! empty( $_POST[ 'msazureaccname' ] ) && ! empty( $_POST[ 'msazurekey' ] ) ) {
			try {
				$blobRestProxy = WindowsAzure\Common\ServicesBuilder::getInstance()->createBlobService( 'DefaultEndpointsProtocol=https;AccountName=' . $_POST[ 'msazureaccname' ] . ';AccountKey=' . $_POST[ 'msazurekey' ] );
				$container_options = new WindowsAzure\Blob\Models\CreateContainerOptions();
				$container_options->setPublicAccess( WindowsAzure\Blob\Models\PublicAccessType::NONE );
				$blobRestProxy->createContainer( $_POST[ 'newmsazurecontainer' ], $container_options );
				$job_settings[ 'msazurecontainer' ] = $_POST[ 'newmsazurecontainer' ];
				BackWPup_Admin::message( sprintf( __( 'MS Azure container "%s" created.', 'backwpup' ), $_POST[ 'newmsazurecontainer' ] ) );
			}
			catch ( Exception $e ) {
				BackWPup_Admin::message( sprintf( __( 'MS Azure container create: %s', 'backwpup' ), $e->getMessage() ), TRUE );
			}
		}

		return $job_settings;
	}


	/**
	 * @param $job_object
	 * @return bool
	 */
	public function job_run_sync( &$job_object ) {

		$job_object->substeps_todo = $job_object->count_folder + count( $job_object->additional_files_to_backup ) + 2;
		$job_object->substeps_done = 0;

		if ( $job_object->steps_data[ $job_object->step_working ]['SAVE_STEP_TRY'] != $job_object->steps_data[ $job_object->step_working ][ 'STEP_TRY' ] )
			$job_object->log( sprintf( __( '%d. Trying to sync files with Microsoft Azure (Blob) &hellip;', 'backwpup' ), $job_object->steps_data[ $job_object->step_working ][ 'STEP_TRY' ] ), E_USER_NOTICE );

		try {

			/** @var $blobRestProxy  WindowsAzure\Common\ServicesBuilder */
			$blobRestProxy = WindowsAzure\Common\ServicesBuilder::getInstance()->createBlobService('DefaultEndpointsProtocol=http;AccountName=' . $job_object->job[ 'msazureaccname' ] . ';AccountKey=' . BackWPup_Encryption::decrypt( $job_object->job[ 'msazurekey' ] ) );

			if ( $job_object->steps_data[ $job_object->step_working ]['SAVE_STEP_TRY'] != $job_object->steps_data[ $job_object->step_working ][ 'STEP_TRY' ] ) {
				//test vor existing container
				$containers    = $blobRestProxy->listContainers()->getContainers();

				$container_url = '';
				foreach( $containers as $container ) {
					if ( $container->getName() == $job_object->job[ 'msazurecontainer' ] ) {
						$container_url = $container->getUrl();
						break;
					}
				}

				if ( empty( $container_url ) ) {
					$job_object->log( sprintf( __( 'MS Azure container "%s" does not exist!', 'backwpup'), $job_object->job[ 'msazurecontainer' ] ), E_USER_ERROR );

					return TRUE;
				} else {
					$job_object->log( sprintf( __( 'Connected to MS Azure container "%s".', 'backwpup'), $job_object->job[ 'msazurecontainer' ] ), E_USER_NOTICE );
				}
			}

			// get files from Azure
			$dest_files_save = $dest_files = $job_object->data_storage( 'files_azure' );
			if ( empty( $dest_files_save ) ) {
				$job_object->log( __( 'Retrieving file list from MS Azure.', 'backwpup'  ), E_USER_NOTICE );
				$dest_files = array();
				$blob_options = new WindowsAzure\Blob\Models\ListBlobsOptions();
				$blob_options->setPrefix( $job_object->job[ 'msazuredir'  ] );
				$blobs        = $blobRestProxy->listBlobs( $job_object->job[ 'msazurecontainer' ], $blob_options )->getBlobs();
				if ( is_array( $blobs ) ) {
					foreach ( $blobs as $blob ) {
						$dest_files[ utf8_encode( $blob->getName() ) ] = $blob->getProperties()->getContentLength();
					}
				}
				$job_object->substeps_done ++;
				$job_object->data_storage( 'files_azure', $dest_files );
				$job_object->do_restart_time();
			}

			//Sync files
			//go folder by folder
			if ( $job_object->steps_data[ $job_object->step_working ]['SAVE_STEP_TRY'] != $job_object->steps_data[ $job_object->step_working ][ 'STEP_TRY' ] )
				$job_object->log( __( 'Upload changed files to MS Azure.', 'backwpup'  ) );
			foreach( $job_object->get_folders_to_backup() as $folder_to_backup ) {
				$files_in_folder = $job_object->get_files_in_folder( $folder_to_backup );
				foreach( $files_in_folder as $file_in_folder ) {
					//crate file name on destination
					$dest_file_name =  $job_object->job[ 'msazuredir' ] . ltrim( str_replace( $job_object->remove_path, '', $file_in_folder ), '/' );
					//Upload file is not exits or the same
					if ( ! isset( $dest_files[ utf8_encode( $dest_file_name ) ] ) || ( isset( $dest_files[ utf8_encode( $dest_file_name ) ] ) && $dest_files[ utf8_encode( $dest_file_name ) ] != filesize( $file_in_folder ) ) ) {
						$blobRestProxy->createBlockBlob( $job_object->job[ 'msazurecontainer' ], $dest_file_name,  fopen( $file_in_folder, 'r' ) );
						$job_object->log( sprintf( __( 'File %s uploaded to MS Azure.', 'backwpup' ), $dest_file_name ) );
						$dest_files_save[ utf8_encode( $dest_file_name ) ] = filesize( $file_in_folder );
						$job_object->data_storage( 'files_azure', $dest_files_save );
						$job_object->do_restart_time();
					}
					//remove from array
					if ( isset( $dest_files[ utf8_encode( $dest_file_name ) ] ) )
						unset( $dest_files[ utf8_encode( $dest_file_name ) ]);
				}
				$job_object->substeps_done ++;
			}

			//sync extra files
			if ( ! empty( $job_object->additional_files_to_backup ) ) {
				foreach ( $job_object->additional_files_to_backup as $file ) {
					if ( isset( $dest_files[ utf8_encode( $job_object->job[ 'msazuredir' ] . basename( $file ) ) ] ) && filesize( $file ) ==  $dest_files[ utf8_encode( $job_object->job[ 'msazuredir' ] . basename( $file ) ) ] ) {
						unset( $dest_files[ utf8_encode( $job_object->job[ 'msazuredir' ] . basename( $file ) ) ]);
						$job_object->substeps_done ++;
						continue;
					}
					$blobRestProxy->createBlockBlob( $job_object->job[ 'msazurecontainer' ], $job_object->job[ 'msazuredir' ] . basename( $file ),  fopen( $file, 'r' ) );
					$job_object->log( sprintf( __( 'Extra file %s uploaded to MS Azure.', 'backwpup' ), basename( $file ) ) );
					$dest_files_save[utf8_encode( $job_object->job[ 'msazuredir' ] . basename( $file ) ) ] = filesize( $file );
					$job_object->data_storage( 'files_azure', $dest_files_save );
					if ( isset( $dest_files[ utf8_encode( $job_object->job[ 'msazuredir' ] . basename( $file ) ) ] ) )
						unset( $dest_files[ utf8_encode( $job_object->job[ 'msazuredir' ] . basename( $file ) ) ]);
					$job_object->do_restart_time();
					$job_object->substeps_done ++;
				}
			}

			//delete rest files
			if ( ! $job_object->job[ 'msazuresyncnodelete' ] ) {
				if ( $job_object->steps_data[ $job_object->step_working ]['SAVE_STEP_TRY'] != $job_object->steps_data[ $job_object->step_working ][ 'STEP_TRY' ] )
					$job_object->log( __( 'Delete nonexistent files on MS Azure.', 'backwpup'  ), E_USER_NOTICE );
				foreach( $dest_files as $dest_file => $dest_file_size ) {
					$blobRestProxy->deleteBlob( $job_object->job[ 'msazurecontainer' ], utf8_decode( $dest_file ) );
					$job_object->log( sprintf( __( 'File %s deleted from MS Azure.', 'backwpup' ), utf8_decode( $dest_file ) ) );
					unset( $dest_files_save[ $dest_file ] );
					$job_object->data_storage( 'files_azure', $dest_files_save );
					$job_object->do_restart_time();
				}
			}
			$job_object->substeps_done ++;

		}
		catch ( Exception $e ) {
			$job_object->log( E_USER_ERROR, sprintf( __( 'Microsoft Azure API: %s', 'backwpup' ), htmlentities( $e->getMessage() ) ), $e->getFile(), $e->getLine() );

			return FALSE;
		}

		return TRUE;
	}

	/**
	 *
	 */
	public function wizard_inline_js() {

		$this->edit_inline_js();
	}

}