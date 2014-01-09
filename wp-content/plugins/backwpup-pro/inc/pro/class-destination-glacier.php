<?php
// Amazon Glacier SDK v2.4.11
// http://aws.amazon.com/de/sdkforphp2/
// https://github.com/aws/aws-sdk-php
include_once BackWPup::get_plugin_data( 'PluginDir' ) . '/vendor/autoloader.php';

/**
 * Documentation: http://docs.amazonwebservices.com/aws-sdk-php-2/latest/class-Aws.S3.S3Client.html
 */
class BackWPup_Pro_Destination_Glacier extends BackWPup_Destinations {

	/**
	 * @return array
	 */
	public function option_defaults() {

		return array( 'glacieraccesskey' => '', 'glaciersecretkey' => '', 'glaciervault' => '', 'glacierregion' => 'us-east-1',  'glaciermaxbackups' => 100 );
	}


	/**
	 * @param $jobid
	 */
	public function edit_tab( $jobid ) {

		?>
		<h3 class="title"><?php _e( 'Amazon Glacier', 'backwpup' ) ?></h3>
		<p></p>
		<table class="form-table">
			<tr>
				<th scope="row"><label for="glacierregion"><?php _e( 'Select a region:', 'backwpup' ) ?></label></th>
				<td>
					<select name="glacierregion" id="glacierregion" title="<?php _e( 'Amazon Glacier Region', 'backwpup' ); ?>">
						<option value="us-east-1" <?php selected( 'us-east-1', BackWPup_Option::get( $jobid, 'glacierregion' ), TRUE ) ?>><?php _e( 'US Standard', 'backwpup' ); ?></option>
						<option value="us-west-1" <?php selected( 'us-west-1', BackWPup_Option::get( $jobid, 'glacierregion' ), TRUE ) ?>><?php _e( 'US West (Northern California)', 'backwpup' ); ?></option>
						<option value="us-west-2" <?php selected( 'us-west-2', BackWPup_Option::get( $jobid, 'glacierregion' ), TRUE ) ?>><?php _e( 'US West (Oregon)', 'backwpup' ); ?></option>
						<option value="eu-west-1" <?php selected( 'eu-west-1', BackWPup_Option::get( $jobid, 'glacierregion' ), TRUE ) ?>><?php _e( 'EU (Ireland)', 'backwpup' ); ?></option>
						<option value="ap-northeast-1" <?php selected( 'ap-northeast-1', BackWPup_Option::get( $jobid, 'glacierregion' ), TRUE ) ?>><?php _e( 'Asia Pacific (Tokyo)', 'backwpup' ); ?></option>
						<option value="ap-southeast-1" <?php selected( 'ap-southeast-1', BackWPup_Option::get( $jobid, 'glacierregion' ), TRUE ) ?>><?php _e( 'Asia Pacific (Singapore)', 'backwpup' ); ?></option>
						<option value="ap-southeast-2" <?php selected( 'ap-southeast-2', BackWPup_Option::get( $jobid, 'glacierregion' ), TRUE ) ?>><?php _e( 'Asia Pacific (Sydney)', 'backwpup' ); ?></option>
						<option value="sa-east-1" <?php selected( 'sa-east-1', BackWPup_Option::get( $jobid, 'glacierregion' ), TRUE ) ?>><?php _e( 'South America (Sao Paulo)', 'backwpup' ); ?></option>
					</select>
				</td>
			</tr>
		</table>

		<h3 class="title"><?php _e( 'Amazon Access Keys', 'backwpup' ); ?></h3>
		<p></p>
		<table class="form-table">
			<tr>
				<th scope="row"><label for="glacieraccesskey"><?php _e( 'Access Key', 'backwpup' ); ?></label></th>
				<td>
					<input id="glacieraccesskey" name="glacieraccesskey" type="text"
						   value="<?php echo esc_attr( BackWPup_Option::get( $jobid, 'glacieraccesskey' ) );?>" class="regular-text" autocomplete="off" />
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="glaciersecretkey"><?php _e( 'Secret Key', 'backwpup' ); ?></label></th>
				<td>
					<input id="glaciersecretkey" name="glaciersecretkey" type="password"
						   value="<?php echo esc_attr( BackWPup_Encryption::decrypt( BackWPup_Option::get( $jobid, 'glaciersecretkey' ) ) ); ?>" class="regular-text" autocomplete="off" />
				</td>
			</tr>
		</table>

		<h3 class="title"><?php _e( 'Vault', 'backwpup' ); ?></h3>
		<p></p>
		<table class="form-table">
			<tr>
				<th scope="row"><label for="vaultselected"><?php _e( 'Vault selection', 'backwpup' ); ?></label></th>
				<td>
					<input id="vaultselected" name="vaultselected" type="hidden" value="<?php echo esc_attr( BackWPup_Option::get( $jobid, 'glaciervault' ) ); ?>" />
					<?php if ( BackWPup_Option::get( $jobid, 'glacieraccesskey' ) && BackWPup_Option::get( $jobid, 'glaciersecretkey' ) ) $this->edit_ajax( array(
																																					   'glacieraccesskey'  => BackWPup_Option::get( $jobid, 'glacieraccesskey' ),
																																					   'glaciersecretkey'  => BackWPup_Encryption::decrypt(BackWPup_Option::get( $jobid, 'glaciersecretkey' ) ),
																																					   'vaultselected'   => BackWPup_Option::get( $jobid, 'glaciervault' ),
																																					   'glacierregion' 	=> BackWPup_Option::get( $jobid, 'glacierregion' )
																																				  ) ); ?>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="newvault"><?php _e( 'Create a new vault', 'backwpup' ); ?></label></th>
				<td>
					<input id="newvault" name="newvault" type="text" value="" class="small-text" autocomplete="off" />
				</td>
			</tr>
		</table>

		<h3 class="title"><?php _e( 'Glacier Backup settings', 'backwpup' ); ?></h3>
		<p></p>
		<table class="form-table">
			<tr>
				<th scope="row"><?php _e( 'File deletion', 'backwpup' ); ?></th>
				<td>
					<label for="glaciermaxbackups"><input id="glaciermaxbackups" name="glaciermaxbackups" type="text" size="3" value="<?php echo esc_attr( BackWPup_Option::get( $jobid, 'glaciermaxbackups' ) ); ?>" class="small-text help-tip" title="<?php esc_attr_e( 'Oldest files will be deleted first. 0 = no deletion', 'backwpup' ); ?>" />&nbsp;
					<?php  _e( 'Number of files to keep in folder. (Archives deleted before 3 months after they have been stored may cause extra costs when deleted.)', 'backwpup' ); ?></label>
				</td>
			</tr>
		</table>

		<?php
	}


	/**
	 * @param $jobid
	 * @return string
	 */
	public function edit_form_post_save( $jobid ) {
		$message = '';
		BackWPup_Option::update( $jobid, 'glacieraccesskey', isset( $_POST[ 'glacieraccesskey' ] ) ? $_POST[ 'glacieraccesskey' ] : '' );
		BackWPup_Option::update( $jobid, 'glaciersecretkey', isset( $_POST[ 'glaciersecretkey' ] ) ? BackWPup_Encryption::encrypt( $_POST[ 'glaciersecretkey' ] ) : '' );
		BackWPup_Option::update( $jobid, 'glacierregion', isset( $_POST[ 'glacierregion' ] ) ? $_POST[ 'glacierregion' ] : '' );
		BackWPup_Option::update( $jobid, 'glaciervault', isset( $_POST[ 'glaciervault' ] ) ? $_POST[ 'glaciervault' ] : '' );
		BackWPup_Option::update( $jobid, 'glaciermaxbackups', isset( $_POST[ 'glaciermaxbackups' ] ) ? (int)$_POST[ 'glaciermaxbackups' ] : 0 );

		//create new bucket
		if ( !empty( $_POST[ 'newvault' ] ) ) {
			try {
				$glacier = Aws\Glacier\GlacierClient::factory( array( 	 'key'	=> $_POST[ 'glacieraccesskey' ],
																		 'secret'	=> $_POST[ 'glaciersecretkey' ],
																		 'region'	=> $_POST[ 'glacierregion' ],
																		 'scheme'	=> 'https',
																		 'ssl.certificate_authority' => BackWPup::get_plugin_data( 'plugindir' ) . '/vendor/cacert.pem') );

				$vault = $glacier->createVault( array( 'vaultName' => $_POST[ 'newvault' ] ) );
				$glacier->waitUntilVaultExists( array( 'vaultName' => $_POST[ 'newvault' ] ) );
				if ( $vault->get( 'location' ) )
					$message .= sprintf( __( 'Vault %1$s created.','backwpup'), $_POST[ 'newvault' ] ) . '<br />';
				else
					$message .= sprintf( __( 'Vault %s could not be created.','backwpup'), $_POST[ 'newvault' ] ) . '<br />';

			}
			catch ( Aws\S3\Exception\S3Exception $e ) {
				$message .= $e->getMessage();
			}
			BackWPup_Option::update( $jobid, 'glaciervault', $_POST[ 'newvault' ] );
		}

		return $message;
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
						<label for="glacierregion"><?php _e( 'Select an Amazon Glacier region:', 'backwpup' ); ?><br />
							<select name="glacierregion" id="glacierregion" title="<?php _e( 'Amazon Glacier Region', 'backwpup' ); ?>">
								<option value="us-east-1" <?php selected( 'us-east-1', $job_settings[ 'glacierregion' ], TRUE ) ?>><?php _e( 'US Standard', 'backwpup' ); ?></option>
								<option value="us-west-1" <?php selected( 'us-west-1', $job_settings[ 'glacierregion' ], TRUE ) ?>><?php _e( 'US West (Northern California)', 'backwpup' ); ?></option>
								<option value="us-west-2" <?php selected( 'us-west-2', $job_settings[ 'glacierregion' ], TRUE ) ?>><?php _e( 'US West (Oregon)', 'backwpup' ); ?></option>
								<option value="eu-west-1" <?php selected( 'eu-west-1', $job_settings[ 'glacierregion' ], TRUE ) ?>><?php _e( 'EU (Ireland)', 'backwpup' ); ?></option>
								<option value="ap-northeast-1" <?php selected( 'ap-northeast-1', $job_settings[ 'glacierregion' ], TRUE ) ?>><?php _e( 'Asia Pacific (Tokyo)', 'backwpup' ); ?></option>
								<option value="ap-southeast-1" <?php selected( 'ap-southeast-1', $job_settings[ 'glacierregion' ], TRUE ) ?>><?php _e( 'Asia Pacific (Singapore)', 'backwpup' ); ?></option>
								<option value="ap-southeast-2" <?php selected( 'ap-southeast-2', $job_settings[ 'glacierregion' ], TRUE ) ?>><?php _e( 'Asia Pacific (Sydney)', 'backwpup' ); ?></option>
								<option value="sa-east-1" <?php selected( 'sa-east-1', $job_settings[ 'glacierregion' ], TRUE ) ?>><?php _e( 'South America (Sao Paulo)', 'backwpup' ); ?></option>
							</select></label><br/>
						<label for="glacieraccesskey"><strong><?php _e( 'Access Key:', 'backwpup' ); ?></strong>
							<input id="glacieraccesskey" name="glacieraccesskey" type="text" value="<?php echo esc_attr( $job_settings[ 'glacieraccesskey' ] );?>" class="large-text" autocomplete="off" /></label><br/>
						<label for="glaciersecretkey"><strong><?php _e( 'Secret Key:', 'backwpup' ); ?></strong><br/>
							<input id="glaciersecretkey" name="glaciersecretkey" type="password" value="<?php echo esc_attr( BackWPup_Encryption::decrypt( $job_settings[ 'glaciersecretkey' ] ) );?>" class="large-text" autocomplete="off" /></label><br/>
						<label for="glaciervault"><strong><?php _e( 'Vault:', 'backwpup' ); ?></strong><br/>
							<input id="vaultselected" name="vaultselected" type="hidden" value="<?php echo esc_attr( $job_settings[ 'vaultselected' ] ); ?>" />
							<?php if ( $job_settings[ 'glacieraccesskey' ] && $job_settings[ 'glaciersecretkey' ] ) $this->edit_ajax( array(
																																 'glacieraccesskey'  	=> $job_settings[  'glacieraccesskey' ],
																																 'glaciersecretkey'  	=> BackWPup_Encryption::decrypt( $job_settings[ 'glaciersecretkey' ] ),
																																 'vaultselected'   		=> $job_settings[ 'glaciervault' ],
																																 'glacierregion' 		=> $job_settings[ 'glacierregion' ]
																															) ); ?></label>

						&nbsp;&nbsp;&nbsp;<label for="newvault"><?php _e('New Vault:', 'backwpup'); ?><input id="newvault" name="newvault" type="text" value="" class="small-text" autocomplete="off" /></label><br/>
						<br/>
						<label id="glaciermaxbackups"><input name="glaciermaxbackups" id="glaciermaxbackups" type="text" size="3" value="<?php echo esc_attr( $job_settings[ 'glaciermaxbackups' ] );?>" class="small-text help-tip" title="<?php esc_attr_e( 'Oldest files will be deleted first. 0 = no deletion', 'backwpup' ); ?>" />
							<?php  _e( 'Number of files to keep in folder. (Archives deleted before 3 months after they have been stored may cause extra costs when deleted.)', 'backwpup' ); ?></label>
						<br/>
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

		$job_settings[ 'glacieraccesskey' ] = isset( $_POST[ 'glacieraccesskey' ] ) ? $_POST[ 'glacieraccesskey' ] : '';
		$job_settings[ 'glaciersecretkey' ] = isset( $_POST[ 'glaciersecretkey' ] ) ? BackWPup_Encryption::encrypt( $_POST[ 'glaciersecretkey' ] ) : '';
		$job_settings[ 'glacierregion' ] = isset( $_POST[ 'glacierregion' ] ) ? $_POST[ 'glacierregion' ] : '';
		$job_settings[ 'glaciervault' ] = isset( $_POST[ 'glaciervault' ] ) ? $_POST[ 'glaciervault' ] : '';
		$job_settings[ 'glaciermaxbackups' ] = isset( $_POST[ 'glaciermaxbackups' ] ) ? (int)$_POST[ 'glaciermaxbackups' ] : 0;

		//create new bucket
		if ( !empty( $_POST[ 'newvault' ] ) ) {
			try {
				$glacier = Aws\Glacier\GlacierClient::factory( array( 	'key'		=> $job_settings[ 'glacieraccesskey' ],
																		'secret'	=> BackWPup_Encryption::decrypt( $job_settings[ 'glaciersecretkey' ] ),
																		'region'	=> $job_settings[ 'glacierregion' ],
																		'scheme'	=> 'https',
																		'ssl.certificate_authority' => BackWPup::get_plugin_data( 'plugindir' ) . '/vendor/cacert.pem') );

				$vault = $glacier->createVault( array( 'vaultName' => $_POST[ 'newvault' ] ) );
				$glacier->waitUntilVaultExists( array( 'vaultName' => $_POST[ 'newvault' ] ) );

				if ( $vault->get( 'location' ) )
					BackWPup_Admin::message( sprintf( __( 'Vault %1$s created.','backwpup'), $_POST[ 'newvault' ] ) );
				else
					BackWPup_Admin::message( sprintf( __( 'Vault %s could not be created.','backwpup'), $_POST[ 'newvault' ] ) );

			}
			catch ( Exception $e ) {
				BackWPup_Admin::message( $e->getMessage() );
			}
			$job_settings[ 'newvault' ] = $_POST[ 'newvault' ];
		}

		return $job_settings;
	}


	/**
	 * @param $jobdest
	 * @param $backupfile
	 */
	public function file_delete( $jobdest, $backupfile ) {

		$files =  get_option( 'backwpup_' . strtolower( $jobdest ), array() );
		list( $jobid, $dest ) = explode( '_', $jobdest );

		if ( BackWPup_Option::get( $jobid, 'glacieraccesskey' ) && BackWPup_Option::get( $jobid, 'glaciersecretkey' ) && BackWPup_Option::get( $jobid, 'glaciervault' ) ) {
			try {
				$glacier = Aws\Glacier\GlacierClient::factory( array(	'key'		=> BackWPup_Option::get( $jobid, 'glacieraccesskey' ),
																		'secret'	=> BackWPup_Encryption::decrypt( BackWPup_Option::get( $jobid, 'glaciersecretkey' ) ),
																		'region'	=> BackWPup_Option::get( $jobid, 'glacierregion' ),
																		'scheme'	=> 'https',
																		'ssl.certificate_authority' => BackWPup::get_plugin_data( 'plugindir' ) . '/vendor/cacert.pem') );

				$glacier->deleteArchive( array(
										 'vaultName' =>  BackWPup_Option::get( $jobid,'glaciervault' ),
										 'archiveId' => $backupfile
								   ) );
				//update file list
				foreach ( $files as $key => $file ) {
					if ( is_array( $file ) && $file[ 'file' ] == $backupfile )
						unset( $files[ $key ] );
				}
			}
			catch ( Exception $e ) {
				BackWPup_Admin::message( sprintf( __('AWS API: %s','backwpup'), $e->getMessage() ) );
			}
		}

		update_option( 'backwpup_'. strtolower( $jobdest ), $files );
	}

	/**
	 * @param $jobdest
	 * @return mixed
	 */
	public function file_get_list( $jobdest ) {

		return get_site_option( 'backwpup_' . strtolower( $jobdest ) );
	}

	/**
	 * @param $job_object
	 * @return bool
	 */
	public function job_run_archive( &$job_object ) {

		$job_object->substeps_todo = 2 + $job_object->backup_filesize;
		if ( $job_object->steps_data[ $job_object->step_working ][ 'SAVE_STEP_TRY' ] != $job_object->steps_data[ $job_object->step_working ][ 'STEP_TRY' ] )
			$job_object->log( sprintf( __( '%d. Trying to send backup file to Amazon Glacier&#160;&hellip;', 'backwpup' ), $job_object->steps_data[ $job_object->step_working ][ 'STEP_TRY' ] ), E_USER_NOTICE );

		try {
			$glacier = Aws\Glacier\GlacierClient::factory( array( 	'key'		=> $job_object->job[ 'glacieraccesskey' ],
																	'secret'	=> BackWPup_Encryption::decrypt( $job_object->job[ 'glaciersecretkey' ] ),
																	'region'	=> $job_object->job[ 'glacierregion' ],
																	'scheme'	=> 'https',
																	'ssl.certificate_authority' => BackWPup::get_plugin_data( 'plugindir' ) . '/vendor/cacert.pem') );

			$vault = $glacier->describeVault( array( 'vaultName' => $job_object->job[ 'glaciervault' ] ) );
			if ( $job_object->steps_data[ $job_object->step_working ]['SAVE_STEP_TRY'] != $job_object->steps_data[ $job_object->step_working ][ 'STEP_TRY' ] && $job_object->substeps_done < $job_object->backup_filesize ) {

				if ( $vault->get( 'VaultName' ) ) {
					$job_object->log( sprintf( __( 'Connected to Glacier vault "%1$s" with %2$d archives and size of %3$d', 'backwpup' ), $vault->get( 'VaultName' ), $vault->get( 'NumberOfArchives' ), size_format( $vault->get( 'SizeInBytes' ), 2 ) ), E_USER_NOTICE );
				} else {
					$job_object->log( sprintf( __( 'Glacier vault "%s" does not exist!', 'backwpup' ), $job_object->job[ 'glaciervault' ] ), E_USER_ERROR );

					return TRUE;
				}

				//transfer file to Glacier
				$job_object->log( __( 'Starting upload to Amazon Glacier&#160;&hellip;', 'backwpup' ), E_USER_NOTICE );
			}

			//Prepare Upload
			$job_object->steps_data[ $job_object->step_working ][ 'partSize' ] 	= 4194304; //4MB
			$parts = Aws\Glacier\Model\MultipartUpload\UploadPartGenerator::factory( fopen( $job_object->backup_folder . $job_object->backup_file, 'rb' ), $job_object->steps_data[ $job_object->step_working ][ 'partSize' ] );
			//UploadPartGenerator deletes $file_handel
			$file_handel = fopen( $job_object->backup_folder . $job_object->backup_file, 'rb' );

			try {

				if ( empty ( $job_object->steps_data[ $job_object->step_working ][ 'uploadId' ] ) ) {
					$upload = $glacier->initiateMultipartUpload( array(	'vaultName' => $job_object->job[ 'glaciervault' ],
																		'partSize' => $job_object->steps_data[ $job_object->step_working ][ 'partSize' ] ) );

					$job_object->steps_data[ $job_object->step_working ][ 'uploadId' ] = $upload->get( 'uploadId' );
					$job_object->steps_data[ $job_object->step_working ][ 'part' ] = 1;
				}

				$part_count = 1;
				foreach ( $parts as $part ) {
					$part_count ++;
					if ( $part_count <= $job_object->steps_data[ $job_object->step_working ][ 'part' ] )
						continue;
					$chunk_upload_start = microtime( TRUE );
					fseek( $file_handel, $part->getOffset() );
					$glacier->uploadMultipartPart( array( 	'vaultName' => $job_object->job[ 'glaciervault' ],
															'uploadId' => $job_object->steps_data[ $job_object->step_working ][ 'uploadId' ],
															'checksum' => $part->getChecksum(),
															'range' =>  $part->getFormattedRange(),
															'body' => fread( $file_handel, $part->getSize() ),
															'ContentSHA256' => $part->getContentHash() ) );
					$chunk_upload_time = microtime( TRUE ) - $chunk_upload_start;
					$job_object->substeps_done = $job_object->substeps_done + $part->getSize();
					$job_object->steps_data[ $job_object->step_working ][ 'part' ] ++;
					$time_remaining = $job_object->do_restart_time();
					if ( $time_remaining < $chunk_upload_time )
						$job_object->do_restart_time( TRUE );
					$job_object->update_working_data();
				}

				$result = $glacier->completeMultipartUpload( array(	'vaultName' => $job_object->job[ 'glaciervault' ],
																	'uploadId' => $job_object->steps_data[ $job_object->step_working ][ 'uploadId' ],
																	'archiveSize' => $parts->getArchiveSize(),
																	'checksum' => $parts->getRootChecksum() ) );

				if ( $result->get( 'archiveId' ) ) {
					$job_object->substeps_done = 1 + $job_object->backup_filesize;
					//sore file data
					$backup_files = get_site_option( 'backwpup_' . $job_object->job[ 'jobid' ] . '_glacier', array() );
					$backup_files[] = array( 	'folder' 	=> '/',
												'file' 	 	=> $result->get( 'archiveId' ),
												'filename' 	=> $job_object->backup_file,
												'info'		=> sprintf( __( 'Archive ID: %s', 'backwpup' ), $result->get( 'archiveId' ) ),
												'downloadurl' => '',
												'filesize'  => $job_object->backup_filesize,
												'time' 		=> current_time( 'timestamp', TRUE ) );
					update_site_option( 'backwpup_' . $job_object->job[ 'jobid' ] . '_glacier' , $backup_files );
					$job_object->substeps_done = 1 + $job_object->backup_filesize;
					$job_object->log( sprintf( __( 'Backup transferred to %s.', 'backwpup' ), $result->get( 'location' ) ), E_USER_NOTICE );
				} else {
					$job_object->log(
									sprintf(
										__( 'Error transfering backup to %$1s.', 'backwpup' ),
										__( 'Glacier', 'backwpup' )
									),
									E_USER_ERROR
									);
				}

			} catch ( Exception $e ) {
				$job_object->log( E_USER_ERROR, sprintf( __( 'AWS API: %s', 'backwpup' ), htmlentities( $e->getMessage() ) ), $e->getFile(), $e->getLine() );
				if ( ! empty( $job_object->steps_data[ $job_object->step_working ][ 'uploadId' ] ) )
					$glacier->abortMultipartUpload( array(	'vaultName' => $job_object->job[ 'glaciervault' ],
														 	'uploadId' => $job_object->steps_data[ $job_object->step_working ][ 'uploadId' ] ) );
				unset( $job_object->steps_data[ $job_object->step_working ][ 'uploadId' ] );
				unset( $job_object->steps_data[ $job_object->step_working ][ 'part' ] );
				$job_object->substeps_done = 0;
				if ( is_resource( $file_handel ) )
					fclose( $file_handel );
				return FALSE;
			}
			fclose( $file_handel );


		}
		catch ( Exception $e ) {
			$job_object->log( E_USER_ERROR, sprintf( __( 'AWS API: %s', 'backwpup' ), htmlentities( $e->getMessage() ) ), $e->getFile(), $e->getLine() );

			return FALSE;
		}

		try {
			$backupfilelist = array();

			foreach ( $backup_files as $file )
				$backupfilelist[ $file[ 'time' ] ] = $file[ 'file' ];

			if ( $job_object->job[ 's3maxbackups' ] > 0 && is_object( $glacier ) ) { //Delete old backups
				if ( count( $backupfilelist ) > $job_object->job[ 'glaciermaxbackups' ] ) {
					ksort( $backupfilelist );
					$numdeltefiles = 0;
					while ( $file = array_shift( $backupfilelist ) ) {
						if ( count( $backupfilelist ) < $job_object->job[ 'glaciermaxbackups' ] )
							break;
						//delete files on S3
						$args = array(
							'vaultName' => $job_object->job[ 'glaciervault' ],
							'archiveId' => $file
						);
						if (  $glacier->deleteArchive( $args )  ) {
							foreach ( $backup_files as $key => $filedata ) {
								if ( $filedata[ 'file' ] == $file )
									unset( $backup_files[ $key ] );
							}
							$numdeltefiles ++;
						} else {
							$job_object->log( sprintf( __( 'Cannot delete archive from %s.', 'backwpup' ), $job_object->job[ 'glaciervault' ] ), E_USER_ERROR );
						}
					}
					if ( $numdeltefiles > 0 )
						$job_object->log( sprintf( _n( 'One file deleted on vault.', '%d files deleted on vault', $numdeltefiles, 'backwpup' ), $numdeltefiles ), E_USER_NOTICE );
				}
			}
			update_site_option( 'backwpup_' . $job_object->job[ 'jobid' ] . '_glacier', $backup_files );
		}
		catch ( Exception $e ) {
			$job_object->log( E_USER_ERROR, sprintf( __( 'AWS API: %s', 'backwpup' ), htmlentities( $e->getMessage() ) ), $e->getFile(), $e->getLine() );

			return FALSE;
		}
		$job_object->substeps_done = 2 + $job_object->backup_filesize;

		return TRUE;
	}


	/**
	 * @param $job_object
	 * @return bool
	 */
	public function can_run( $job_object ) {

		if ( empty( $job_object->job[ 'glacieraccesskey' ] ) )
			return FALSE;

		if ( empty( $job_object->job[ 'glaciersecretkey' ] ) )
			return FALSE;

		if ( empty( $job_object->job[ 'glaciervault' ] ) )
			return FALSE;

		return TRUE;
	}

	/**
	 *
	 */
	public function edit_inline_js() {
		//<script type="text/javascript">
		?>
		function awsgetvault() {
            var data = {
                action: 'backwpup_dest_glacier',
				glacieraccesskey: $('input[name="glacieraccesskey"]').val(),
				glaciersecretkey: $('input[name="glaciersecretkey"]').val(),
                vaultselected: $('input[name="vaultselected"]').val(),
				glacierregion: $('#glacierregion').val(),
                _ajax_nonce: $('#backwpupajaxnonce').val()
            };
            $.post(ajaxurl, data, function(response) {
                $('#glacierbucketerror').remove();
                $('#glaciervault').remove();
                $('#vaultselected').after(response);
            });
        }
		$('input[name="glacieraccesskey"]').change(function() {awsgetvault();});
		$('input[name="glaciersecretkey"]').change(function() {awsgetvault();});
		$('#glacierregion').change(function() {awsgetvault();});
		<?php
	}


	/**
	 *
	 */
	public function wizard_inline_js() {

		$this->edit_inline_js();
	}

	/**
	 * @param string $args
	 */
	public function edit_ajax( $args = '' ) {

		$error = '';

		if ( is_array( $args ) ) {
			$ajax = FALSE;
		}
		else {
			if ( ! current_user_can( 'backwpup_jobs_edit' ) )
				wp_die( -1 );
			check_ajax_referer( 'backwpup_ajax_nonce' );
			$args[ 'glacieraccesskey' ]  	= $_POST[ 'glacieraccesskey' ];
			$args[ 'glaciersecretkey' ]  	= $_POST[ 'glaciersecretkey' ];
			$args[ 'vaultselected' ]		= $_POST[ 'vaultselected' ];
			$args[ 'glacierregion' ]  	 	= $_POST[ 'glacierregion' ];
			$ajax         					= TRUE;
		}
		echo '<span id="glacierbucketerror" style="color:red;">';

		if ( ! empty( $args[ 'glacieraccesskey' ] ) && ! empty( $args[ 'glaciersecretkey' ] ) ) {
			try {
				$glacier = Aws\Glacier\GlacierClient::factory( array( 	'key'		=> $args[ 'glacieraccesskey' ],
																		'secret'	=> BackWPup_Encryption::decrypt( $args[ 'glaciersecretkey' ] ),
																		'region'	=> $args[ 'glacierregion' ],
																		'scheme'	=> 'https',
																		'ssl.certificate_authority' => BackWPup::get_plugin_data( 'plugindir' ) . '/vendor/cacert.pem' ) );

				$vaults = $glacier->listVaults();
			}
			catch ( Exception $e ) {
				$error = $e->getMessage();
			}
		}

		if ( empty( $args[ 'glacieraccesskey' ] ) )
			_e( 'Missing access key!', 'backwpup' );
		elseif ( empty( $args[ 'glaciersecretkey' ] ) )
			_e( 'Missing secret access key!', 'backwpup' );
		elseif ( ! empty( $error ) )
			echo esc_html( $error );
		elseif ( ! isset( $vaults ) || count( $vaults['VaultList']  ) < 1 )
			_e( 'No vault found!', 'backwpup' );
		echo '</span>';

		if ( ! empty( $vaults['VaultList'] ) ) {
			echo '<select name="glaciervault" id="glaciervault">';
			foreach ( $vaults['VaultList']  as $vault ) {
				echo "<option " . selected( $args[ 'vaultselected' ], esc_attr( $vault['VaultName'] ), FALSE ) . ">" . esc_attr( $vault['VaultName'] ) . "</option>";
			}
			echo '</select>';
		}

		if ( $ajax )
			die();
	}
}
