<?php
/**
 * Class BackWPup_Pro_Destination_S3_V1
 */
class BackWPup_Pro_Destination_S3_V1 extends BackWPup_Destination_S3_V1 {

	/**
	 * @param $job_settings
	 */
	public function wizard_page( $job_settings ) {

		?>
		<table class="form-table">
			<tr>
				<td>
					<fieldset>
						<label for="s3region"><?php _e( 'Select a S3 service:', 'backwpup' ); ?><br />
						<select name="s3region" id="s3region" title="<?php _e( 'Amazon S3 Region', 'backwpup' ); ?>">
							<option value="us-east-1" <?php selected( 'us-east-1', $job_settings[ 's3region' ], TRUE ) ?>><?php _e( 'Amazon S3: US Standard', 'backwpup' ); ?></option>
							<option value="us-west-1" <?php selected( 'us-west-1', $job_settings[ 's3region' ], TRUE ) ?>><?php _e( 'Amazon S3: US West (Northern California)', 'backwpup' ); ?></option>
							<option value="us-west-2" <?php selected( 'us-west-2', $job_settings[ 's3region' ], TRUE ) ?>><?php _e( 'Amazon S3: US West (Oregon)', 'backwpup' ); ?></option>
							<option value="eu-west-1" <?php selected( 'eu-west-1', $job_settings[ 's3region' ], TRUE ) ?>><?php _e( 'Amazon S3: EU (Ireland)', 'backwpup' ); ?></option>
							<option value="ap-northeast-1" <?php selected( 'ap-northeast-1', $job_settings[ 's3region' ], TRUE ) ?>><?php _e( 'Amazon S3: Asia Pacific (Tokyo)', 'backwpup' ); ?></option>
							<option value="ap-southeast-1" <?php selected( 'ap-southeast-1', $job_settings[ 's3region' ], TRUE ) ?>><?php _e( 'Amazon S3: Asia Pacific (Singapore)', 'backwpup' ); ?></option>
							<option value="ap-southeast-2" <?php selected( 'ap-southeast-2', $job_settings[ 's3region' ], TRUE ) ?>><?php _e( 'Amazon S3: Asia Pacific (Sydney)', 'backwpup' ); ?></option>
							<option value="sa-east-1" <?php selected( 'sa-east-1', $job_settings[ 's3region' ], TRUE ) ?>><?php _e( 'Amazon S3: South America (Sao Paulo)', 'backwpup' ); ?></option>
							<option value="google-storage" <?php selected( 'google-storage', $job_settings[ 's3region' ], TRUE ) ?>><?php _e( 'Google Storage (Interoperable Access)', 'backwpup' ); ?></option>
							<option value="hosteurope" <?php selected( 'hosteurope', $job_settings[ 's3region' ], TRUE ) ?>><?php _e( 'Hosteurope Cloud Storage', 'backwpup' ); ?></option>
							<option value="dreamhost" <?php selected( 'dreamhost', $job_settings[ 's3region' ], TRUE ) ?>><?php _e( 'Dream Host Cloud Storage', 'backwpup' ); ?></option>
						</select></label><br/>
						<label for="s3base_url"><?php _e( 'or set an S3 Server URL:', 'backwpup' ); ?>
							<input id="s3base_url" name="s3base_url" type="text"
								   value="<?php echo esc_attr( $job_settings[ 's3base_url' ] ); ?>" class="large-text" autocomplete="off" /></label><br/>
						<label for="s3accesskey"><strong><?php _e( 'Access Key:', 'backwpup' ); ?></strong>
							<input id="s3accesskey" name="s3accesskey" type="text"
								   value="<?php echo esc_attr( $job_settings[ 's3accesskey' ] );?>" class="large-text" autocomplete="off" /></label><br/>
						<label for="s3secretkey"><strong><?php _e( 'Secret Key:', 'backwpup' ); ?></strong><br/>
							<input id="s3secretkey" name="s3secretkey" type="password"
								   value="<?php echo esc_attr( BackWPup_Encryption::decrypt( $job_settings[ 's3secretkey' ] ) );?>" class="large-text" autocomplete="off" /></label><br/>
						<label for="s3bucketselected"><strong><?php _e( 'Bucket:', 'backwpup' ); ?></strong><br/>
							<input id="s3bucketselected" name="s3bucketselected" type="hidden" value="<?php echo esc_attr( $job_settings[ 's3bucket' ] ); ?>" />
							<?php if ( $job_settings[ 's3accesskey' ] && $job_settings[ 's3secretkey' ] ) $this->edit_ajax( array(
																																 's3accesskey'  => $job_settings[  's3accesskey' ],
																																 's3secretkey'  => BackWPup_Encryption::decrypt( $job_settings[ 's3secretkey' ] ),
																																 's3bucketselected'   => $job_settings[ 's3bucket' ],
																																 's3base_url' 	=> $job_settings[ 's3base_url' ],
																																 's3region' 	=> $job_settings[ 's3region' ]
																															) ); ?></label>

						&nbsp;&nbsp;&nbsp;<label for="s3newbucket"><?php _e('New Bucket:', 'backwpup'); ?><input id="s3newbucket" name="s3newbucket" type="text" value="" class="small-text" autocomplete="off" /></label><br/>
						<br/>
						<label for="ids3dir"><strong><?php _e( 'Folder in bucket:', 'backwpup' ); ?></strong><br/>
							<input name="s3dir" id="ids3dir" type="text" value="<?php echo esc_attr( $job_settings[ 's3dir' ] ); ?>"  class="large-text" /></label><br/>

						<?php
						if ( $job_settings[ 'backuptype' ] == 'archive' ) {
							?>
							<label id="ids3maxbackups"><input name="s3maxbackups" id="ids3maxbackups" type="text" size="3" value="<?php echo esc_attr( $job_settings[ 's3maxbackups' ] );?>" class="small-text help-tip" title="<?php esc_attr_e( 'Oldest files will be deleted first. 0 = no deletion', 'backwpup' ); ?>" />
								<?php  _e( 'Number of files to keep in folder.', 'backwpup' ); ?></label>
							<br/>
						<?php } else { ?>
							<label for="ids3syncnodelete"><input class="checkbox" value="1"
																 type="checkbox" <?php checked(  $job_settings[ 's3syncnodelete' ], TRUE ); ?>
																 name="s3syncnodelete" id="ids3syncnodelete" /> <?php _e( 'Do not delete files while syncing to destination!', 'backwpup' ); ?></label>
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

		$job_settings[ 's3ssencrypt' ] = '';
		$job_settings[ 's3storageclass' ] = '';
		$job_settings[ 's3multipart' ] = TRUE;
		if ( $job_settings[ 's3bucket' ] == 'google-storage' )
			$job_settings[ 's3multipart' ] = FALSE;

		$job_settings[ 's3accesskey' ] = isset( $_POST[ 's3accesskey' ] ) ? $_POST[ 's3accesskey' ] : '';
		$job_settings[ 's3secretkey' ] = isset( $_POST[ 's3secretkey' ] ) ? BackWPup_Encryption::encrypt( $_POST[ 's3secretkey' ] ) : '';
		$job_settings[ 's3base_url' ] = isset( $_POST[ 's3base_url' ] ) ? $_POST[ 's3base_url' ] : '';
		$job_settings[ 's3region' ] = isset( $_POST[ 's3region' ] ) ? $_POST[ 's3region' ] : '';
		$job_settings[ 's3bucket' ] = isset( $_POST[ 's3bucket' ] ) ? $_POST[ 's3bucket' ] : '';

		$_POST[ 's3dir' ] = trailingslashit( str_replace( '//', '/', str_replace( '\\', '/', trim( stripslashes( $_POST[ 's3dir' ] ) ) ) ) );
		if ( substr( $_POST[ 's3dir' ], 0, 1 ) == '/' )
			$_POST[ 's3dir' ] = substr( $_POST[ 's3dir' ], 1 );
		if ( $_POST[ 's3dir' ] == '/' )
			$_POST[ 's3dir' ] = '';
		$job_settings[ 's3dir' ] = $_POST[ 's3dir' ];

		if ( isset( $_POST[ 's3maxbackups' ] ) )
			$job_settings[ 's3maxbackups' ] = isset( $_POST[ 's3maxbackups' ] ) ? (int)$_POST[ 's3maxbackups' ] : 0;
		if ( isset( $_POST[ 's3syncnodelete' ] ) )
			$job_settings[ 's3syncnodelete'] = ( isset( $_POST[ 's3syncnodelete' ] ) && $_POST[ 's3syncnodelete' ] == 1 ) ? TRUE : FALSE;

		//create new bucket
		if ( !empty( $_POST[ 's3newbucket' ] ) ) {
			try {
				$s3 = new AmazonS3( array( 	'key' => $_POST[ 's3accesskey' ],
											'secret' => BackWPup_Encryption::decrypt( $_POST[ 's3secretkey' ] ),
											'certificate_authority'	=> TRUE ) );
				$base_url = $this->get_s3_base_url( $_POST[ 's3region' ], $_POST[ 's3base_url' ] );
				if ( stristr( $base_url, 'amazonaws.com' ) ) {
					$s3->set_region( str_replace( array( 'http://', 'https://' ), '', $base_url ) );
				} else {
					$s3->set_hostname( str_replace( array( 'http://', 'https://' ), '', $base_url ) );
					$s3->allow_hostname_override( FALSE );
					if ( substr( $base_url, -1 ) == '/')
						$s3->enable_path_style( TRUE );
				}
				if ( stristr( $base_url, 'http://' ) )
					$s3->disable_ssl();

				// set bucket creation region
				if ( $_POST[ 's3region' ] == 'google-storage' || $_POST[ 's3region' ] == 'hosteurope' )
					$region = 'EU';
				else
					$region = str_replace( array( 'http://', 'https://' ), '', $base_url );

				$bucket = $s3->create_bucket(  $_POST[ 's3newbucket' ], $region, 'private' );

				if ( $bucket->status == 200 )
					BackWPup_Admin::message( sprintf( __( 'Bucket %1$s created in %2$s.','backwpup'), $_POST[ 's3newbucket' ], $bucket[ 'Location' ] ) );
				else
					BackWPup_Admin::message( sprintf( __( 'Bucket %s could not be created.','backwpup'), $_POST[ 's3newbucket' ] ), TRUE );
			}
			catch ( Exception $e ) {
				BackWPup_Admin::message( $e->getMessage(), TRUE );
			}
			$job_settings[ 's3bucket' ] = $_POST[ 's3newbucket' ];
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
			$job_object->log( sprintf( __( '%d. Trying to sync files to S3 Service&#160;&hellip;', 'backwpup' ), $job_object->steps_data[ $job_object->step_working ][ 'STEP_TRY' ] ), E_USER_NOTICE );

		try {
			$s3 = new AmazonS3( array( 	'key' => $job_object->job[ 's3accesskey' ],
										'secret' => BackWPup_Encryption::decrypt( $job_object->job[ 's3secretkey' ] ),
										'certificate_authority'	=> TRUE ) );
			$base_url = $this->get_s3_base_url( $job_object->job[ 's3region' ], $job_object->job[ 's3base_url' ] );
			if ( stristr( $base_url, 'amazonaws.com' ) ) {
				$s3->set_region( str_replace( array( 'http://', 'https://' ), '', $base_url ) );
			} else {
				$s3->set_hostname( str_replace( array( 'http://', 'https://' ), '', $base_url ) );
				$s3->allow_hostname_override( FALSE );
				if ( substr( $base_url, -1 ) == '/')
					$s3->enable_path_style( TRUE );
			}
			if ( stristr( $base_url, 'http://' ) )
				$s3->disable_ssl();

			if ( $job_object->steps_data[ $job_object->step_working ]['SAVE_STEP_TRY'] != $job_object->steps_data[ $job_object->step_working ][ 'STEP_TRY' ] ) {
				if ( $s3->if_bucket_exists( $job_object->job[ 's3bucket' ] ) ) {
					$job_object->log( sprintf( __( 'Connected to S3 Bucket "%1$s" in %2$s', 'backwpup' ), $job_object->job[ 's3bucket' ], $base_url ), E_USER_NOTICE );
				}
				else {
					$job_object->log( sprintf( __( 'S3 Bucket "%s" does not exist!', 'backwpup' ), $job_object->job[ 's3bucket' ] ), E_USER_ERROR );

					return TRUE;
				}
			}


			// get files from S3
			$dest_files_save = $dest_files = $job_object->data_storage( 'files_s3' );
			if ( ! isset( $job_object->steps_data[ $job_object->step_working ][ 'file_list_results' ] ) || $job_object->steps_data[ $job_object->step_working ][ 'file_list_results' ] == 1000 ) {
				if ( ! isset( $job_object->steps_data[ $job_object->step_working ][ 'file_list_results' ] ) ) {
					$job_object->log( __( 'Retrieving file list from S3.', 'backwpup'  ), E_USER_NOTICE );
					$dest_files = array();
					$job_object->steps_data[ $job_object->step_working ][ 'file_list_results' ]	= 0;
					$args			= array(
						'prefix' => (string) $job_object->job[ 's3dir' ],
						'max-keys' => 1000
					);

					$objects = $s3->list_objects( $job_object->job[ 's3bucket' ], $args );
					if ( is_object( $objects ) ) {
						foreach ( $objects->body->Contents as $object ) {
							$dest_files[ utf8_encode( (string) $object->Key ) ] = (int) $object->Size;
							$job_object->steps_data[ $job_object->step_working ][ 'file_list_marker' ] = (string) $object->Key;
							$job_object->steps_data[ $job_object->step_working ][ 'file_list_results' ] ++;
						}
					}
					$job_object->data_storage( 'files_s3', $dest_files );
					$job_object->do_restart_time();
				}

				while ( $job_object->steps_data[ $job_object->step_working ][ 'file_list_results' ] == 1000 ) {
					$job_object->steps_data[ $job_object->step_working ][ 'file_list_results' ] = 0;

					$args			= array(
						'prefix' => (string) $job_object->job[ 's3dir' ],
						'marker' =>  $job_object->steps_data[ $job_object->step_working ][ 'file_list_marker' ],
						'max-keys' => 1000
					);

					$objects = $s3->list_objects( $job_object->job[ 's3bucket' ], $args );
					if ( is_object( $objects ) ) {
						foreach ( $objects->body->Contents as $object ) {
							$dest_files[ utf8_encode( (string) $object->Key ) ] = (int) $object->Size;
							$job_object->steps_data[ $job_object->step_working ][ 'file_list_marker' ] = (string) $object->Key;
							$job_object->steps_data[ $job_object->step_working ][ 'file_list_results' ] ++;
						}
					}
					$job_object->data_storage( 'files_s3', $dest_files );
					$job_object->do_restart_time();
				}
			}
			$job_object->substeps_done ++;


			//create Parameter
		    if ( $job_object->job[ 's3storageclass' ] == 'REDUCED_REDUNDANCY' ) //set reduced redundancy or not
				$storage=AmazonS3::STORAGE_REDUCED;
		    else
				$storage=AmazonS3::STORAGE_STANDARD;
			if ( empty( $job_object->job[ 's3ssencrypt' ] ) )
				$job_object->job[ 's3ssencrypt' ] = NULL;


			//Sync files
			//go folder by folder
			if ( $job_object->steps_data[ $job_object->step_working ]['SAVE_STEP_TRY'] != $job_object->steps_data[ $job_object->step_working ][ 'STEP_TRY' ] )
				$job_object->log( __( 'Upload changed files to S3.', 'backwpup'  ), E_USER_NOTICE );
			foreach( $job_object->get_folders_to_backup() as $folder_to_backup ) {
				$files_in_folder = $job_object->get_files_in_folder( $folder_to_backup );
				foreach( $files_in_folder as $file_in_folder ) {
					//crate file name on destination
					$dest_file_name =  $job_object->job[ 's3dir' ] . ltrim( str_replace( $job_object->remove_path, '', $file_in_folder ), '/' );
					//Upload file is not exits or the same
					if ( ! isset( $dest_files[ utf8_encode( $dest_file_name ) ] ) || ( isset( $dest_files[ utf8_encode( $dest_file_name ) ] ) && $dest_files[ utf8_encode( $dest_file_name ) ] != filesize( $file_in_folder ) ) ) {
						$s3->create_object( $job_object->job[ 's3bucket' ], $dest_file_name, array( 'fileUpload' => $file_in_folder, 'acl' => AmazonS3::ACL_PRIVATE, 'storage' => $storage, 'encryption' => $job_object->job[ 's3ssencrypt' ] ) );
						$job_object->log( sprintf( __( 'File %s uploaded to S3.', 'backwpup' ), $dest_file_name ), E_USER_NOTICE );
						$dest_files_save[ utf8_encode( $dest_file_name ) ] = filesize( $file_in_folder );
						$job_object->data_storage( 'files_s3', $dest_files_save );
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
					if ( isset( $dest_files[ utf8_encode( $job_object->job[ 's3dir' ] . basename( $file ) ) ] ) && filesize( $file ) ==  $dest_files[ utf8_encode( $job_object->job[ 's3dir' ] . basename( $file ) ) ] ) {
						unset( $dest_files[ utf8_encode( $job_object->job[ 's3dir' ] . basename( $file ) ) ]);
						$job_object->substeps_done ++;
						continue;
					}
					$s3->create_object( $job_object->job[ 's3bucket' ], $job_object->job[ 's3dir' ] . basename( $file ), array( 'fileUpload' => $file, 'acl' => AmazonS3::ACL_PRIVATE, 'storage' => $storage, 'encryption' => $job_object->job[ 's3ssencrypt' ] ) );
					$job_object->log( sprintf( __( 'Extra file %s uploaded to S3.', 'backwpup' ), basename( $file ) ) );
					$dest_files_save[utf8_encode( $job_object->job[ 's3dir' ] . basename( $file ) ) ] = filesize( $file );
					$job_object->data_storage( 'files_s3', $dest_files_save );
					if ( isset( $dest_files[ utf8_encode( $job_object->job[ 's3dir' ] . basename( $file ) ) ] ) )
						unset( $dest_files[ utf8_encode( $job_object->job[ 's3dir' ] . basename( $file ) ) ]);
					$job_object->do_restart_time();
					$job_object->substeps_done ++;
				}
			}

			//delete rest files
			if ( ! $job_object->job[ 's3syncnodelete' ] ) {
				if ( $job_object->steps_data[ $job_object->step_working ]['SAVE_STEP_TRY'] != $job_object->steps_data[ $job_object->step_working ][ 'STEP_TRY' ] )
					$job_object->log( __( 'Delete nonexistent files on S3', 'backwpup'  ), E_USER_NOTICE );
				$dest_files = array_keys( $dest_files );
				foreach( $dest_files as $dest_file ) {
					$s3->delete_object( $job_object->job[ 's3bucket' ], $job_object->job[ 's3dir' ] . utf8_decode( $dest_file ) );
					$job_object->log( sprintf( __( 'File %s deleted from S3.', 'backwpup' ), utf8_decode( $dest_file ) ) );
					unset( $dest_files_save[ $dest_file ] );
					$job_object->data_storage( 'files_s3', $dest_files_save );
					$job_object->do_restart_time();
				}
			}
			$job_object->substeps_done ++;

		}
		catch ( Exception $e ) {
			$job_object->log( E_USER_ERROR, sprintf( __( 'S3 Service API: %s', 'backwpup' ), htmlentities( $e->getMessage() ) ), $e->getFile(), $e->getLine() );

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
		function awsgetbucket() {
            var data = {
                action: 'backwpup_dest_s3',
                s3accesskey: $('input[name="s3accesskey"]').val(),
                s3secretkey: $('input[name="s3secretkey"]').val(),
                s3bucketselected: $('input[name="s3bucketselected"]').val(),
                s3base_url: $('input[name="s3base_url"]').val(),
                s3region: $('#s3region').val(),
                _ajax_nonce: $('#backwpupajaxnonce').val()
            };
            $.post(ajaxurl, data, function(response) {
                $('#s3bucketerror').remove();
                $('#s3bucket').remove();
                $('#s3bucketselected').after(response);
            });
        }
		$('input[name="s3accesskey"]').change(function() {awsgetbucket();});
		$('input[name="s3secretkey"]').change(function() {awsgetbucket();});
		$('input[name="s3base_url"]').change(function() {awsgetbucket();});
		$('#s3region').change(function() {awsgetbucket();});
		<?php
	}

}
