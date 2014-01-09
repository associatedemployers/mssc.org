<?php
/**
 * Documentation: https://code.google.com/p/google-api-php-client/, https://developers.google.com/drive/quickstart-php
 *
 * google-api-php-client Version 0.6.6
 */
if ( ! class_exists( 'Google_Client' ) )
	include BackWPup::get_plugin_data( 'plugindir' ) .'/vendor/Google/Google_Client.php';
if ( ! class_exists( 'Google_DriveService' ) )
	include BackWPup::get_plugin_data( 'plugindir' ) .'/vendor/Google/contrib/Google_DriveService.php';

class BackWPup_Pro_Destination_GDrive extends BackWPup_Destinations {

	private $gdrive_folders = array();
	/*
	 * @var $service Google_DriveService
	 */
	private $service = NULL;

	/**
	 * @return array
	 */
	public function option_defaults() {

		return array( 'gdriveaccesstoken' => '', 'gdrivemaxbackups' => 15, 'gdrivesyncnodelete' => TRUE, 'gdrivedir' => trailingslashit( sanitize_file_name( get_bloginfo( 'name' ) ) ) );
	}


	/**
	 * @param $jobid
	 */
	public function edit_tab( $jobid ) {

		if ( ! get_site_option( 'backwpup_cfg_googleclientid' ) || ! get_site_option( 'backwpup_cfg_googleclientsecret' ) ) {
			?>
			<div id="message" class="updated below-h2">
				<p><?php echo sprintf( __( 'Looks like you haven’t set up any API keys yet. Head over to <a href="%s">Settings | API-Keys</a> and get Google Drive all set up, then come back here.', 'backwpup' ), admin_url( 'admin.php' ) . '?page=backwpupsettings' ); ?></p>
			</div>
			<?php
		}

		//google authentication
		set_site_transient( 'backwpup_gdrive_jobid_' . get_current_user_id(), $jobid, 3600 );
		try {
			$client = new Google_Client();
			if ( is_file( BackWPup::get_plugin_data( 'plugindir' ) . '/vendor/cacert.pem' ) )
				$client->getIo()->setOptions( array( CURLOPT_CAINFO => BackWPup::get_plugin_data( 'plugindir' ) . '/vendor/cacert.pem') );
			$client->setApplicationName( 'BackWPup v.' . BackWPup::get_plugin_data( 'version' ) );
			$client->setClientId( get_site_option( 'backwpup_cfg_googleclientid' ) );
			$client->setClientSecret( BackWPup_Encryption::decrypt( get_site_option( 'backwpup_cfg_googleclientsecret' ) ) );
			$client->setScopes( array( 'https://www.googleapis.com/auth/drive') );
			$client->setUseObjects( TRUE );
			$client->setRedirectUri( admin_url( 'admin-ajax.php' ) . '?action=backwpup_dest_gdrive' );
			$access_token = BackWPup_Encryption::decrypt( BackWPup_Option::get( $jobid, 'gdriveaccesstoken' ) );
			$auth_url = $client->createAuthUrl();
		} catch ( Exception $e ) {
			echo '<div id="message" class="error"><p>' . sprintf( __( 'GDrive API: %s', 'backwpup' ), htmlentities( $e->getMessage() ) ) . '</p></div>';
		}
		?>

		<h3 class="title"><?php _e( 'Login', 'backwpup' ); ?></h3>
		<p></p>
		<table class="form-table">
			<tr>
				<th scope="row"><?php _e( 'Authenticate', 'backwpup' ); ?></th>
				<td><?php if ( empty( $access_token ) ) { ?>
						<span style="color:red;"><?php _e( 'Not authenticated!', 'backwpup' ); ?></span><br />
					<?php } else { ?>
						<span style="color:green;"><?php _e( 'Authenticated!', 'backwpup' ); ?></span><br />
					<?php } ?>
					<a class="button secondary" href="<?php echo $auth_url ;?>"><?php _e( 'Reauthenticate', 'backwpup' ); ?></a>
				</td>
			</tr>
		</table>


		<h3 class="title"><?php _e( 'Backup settings', 'backwpup' ); ?></h3>
		<p></p>
		<table class="form-table">
			<tr>
				<th scope="row"><label for="idgdrivedir"><?php _e( 'Folder in Google Drive', 'backwpup' ) ?></label></th>
				<td>
					<input id="idgdrivedir" name="gdrivedir" type="text" value="<?php echo esc_attr( BackWPup_Option::get( $jobid, 'gdrivedir' ) ); ?>" class="regular-text" />
				</td>
			</tr>
			<tr>
				<th scope="row"><?php _e( 'File Deletion', 'backwpup' ); ?></th>
				<td>
					<?php
					if ( BackWPup_Option::get( $jobid, 'backuptype' ) == 'archive' ) {
						?>
						<label for="idgdrivemaxbackups"><input id="idgdrivemaxbackups" name="gdrivemaxbackups" type="text" size="3" value="<?php echo esc_attr( BackWPup_Option::get( $jobid, 'gdrivemaxbackups' ) );?>" class="small-text help-tip" title="<?php esc_attr_e( 'Oldest files will be deleted first. 0 = no deletion', 'backwpup' ); ?>" />&nbsp;
						<?php  _e( 'Number of files to keep in folder.', 'backwpup' ); ?></label>
						<?php } else { ?>
						<label for="idgdrivesyncnodelete" ><input class="checkbox" value="1"
							   type="checkbox" <?php checked( BackWPup_Option::get( $jobid, 'gdrivesyncnodelete' ), TRUE ); ?>
							   name="gdrivesyncnodelete" id="idgdrivesyncnodelete" /> <?php _e( 'Do not delete files while syncing to destination!', 'backwpup' ); ?></label>
						<?php } ?>
				</td>
			</tr>
		</table>

		<?php
	}

	/**
	 * Authentication over ajax
	 */
	public function edit_ajax() {

		//google authentication
		if ( isset( $_GET[ 'code' ] ) ) {
			// on wizards
			$wiz_data_id = '';
			$wiz_data = array();
			if ( ! empty( $_COOKIE[ 'BackWPup_Wizard_ID' ] ) )
				$wiz_data_id = $_COOKIE[ 'BackWPup_Wizard_ID' ];
			if ( empty( $wiz_data_id ) && ! empty( $_POST[ 'BackWPup_Wizard_ID' ] ) )
				$wiz_data_id = $_POST['BackWPup_Wizard_ID'];

			//start using sessions
			if ( ! empty( $wiz_data_id ) )
				$wiz_data = get_site_transient( 'BackWPup_Wiz_' . $wiz_data_id );

			if ( ! empty( $wiz_data ) ) {
				try {
					$client = new Google_Client();
					if ( is_file( BackWPup::get_plugin_data( 'plugindir' ) . '/vendor/cacert.pem' ) )
						$client->getIo()->setOptions( array( CURLOPT_CAINFO => BackWPup::get_plugin_data( 'plugindir' ) . '/vendor/cacert.pem') );
					$client->setApplicationName( 'BackWPup v.' . BackWPup::get_plugin_data( 'version' ) );
					$client->setClientId( get_site_option( 'backwpup_cfg_googleclientid' ) );
					$client->setClientSecret( BackWPup_Encryption::decrypt( get_site_option( 'backwpup_cfg_googleclientsecret' ) ) );
					$client->setScopes( array( 'https://www.googleapis.com/auth/drive') );
					$client->setRedirectUri( admin_url( 'admin-ajax.php' ) . '?action=backwpup_dest_gdrive' );
					$client->setUseObjects( TRUE );
					$client->authenticate();
					$wiz_data[ 'job_settings' ][ 'gdriveaccesstoken' ] = BackWPup_Encryption::encrypt( $client->getAccessToken() );
					BackWPup_Admin::message( __( 'GDrive: Authenticated.', 'backwpup' ) );
					set_site_transient( 'BackWPup_Wiz_' . $wiz_data_id, $wiz_data, 3600 );
					wp_redirect( network_admin_url( 'admin.php' ) . '?page=backwpupwizard&BackWPup_Wizard_ID=' . $wiz_data_id .'&step=DEST-GDRIVE', 302 );
					wp_die();
				} catch ( Exception $e ) {
					BackWPup_Admin::message( sprintf( __( 'GDrive API: %s', 'backwpup' ), $e->getMessage() ) );
					wp_redirect( network_admin_url( 'admin.php' ) . '?page=backwpupwizard&BackWPup_Wizard_ID=' . $wiz_data_id .'&step=DEST-GDRIVE', 302 );
					wp_die();
				}

			}

			// on edit job
			$jobid = get_site_transient( 'backwpup_gdrive_jobid_' . get_current_user_id() );
			if ( ! empty( $jobid ) ) {
				try {
					$client = new Google_Client();
					if ( is_file( BackWPup::get_plugin_data( 'plugindir' ) . '/vendor/cacert.pem' ) )
						$client->getIo()->setOptions( array( CURLOPT_CAINFO => BackWPup::get_plugin_data( 'plugindir' ) . '/vendor/cacert.pem') );
					$client->setApplicationName( 'BackWPup v.' . BackWPup::get_plugin_data( 'version' ) );
					$client->setClientId( get_site_option( 'backwpup_cfg_googleclientid' ) );
					$client->setClientSecret( BackWPup_Encryption::decrypt( get_site_option( 'backwpup_cfg_googleclientsecret' ) ) );
					$client->setScopes( array( 'https://www.googleapis.com/auth/drive') );
					$client->setRedirectUri( admin_url( 'admin-ajax.php' ) . '?action=backwpup_dest_gdrive' );
					$client->setUseObjects( TRUE );
					$client->authenticate();
					BackWPup_Encryption::encrypt( BackWPup_Option::update( $jobid, 'gdriveaccesstoken', $client->getAccessToken() ) );
					BackWPup_Admin::message( __( 'GDrive: Authenticated.', 'backwpup' ) );
					wp_redirect( network_admin_url( 'admin.php' ) . '?page=backwpupeditjob&jobid=' . $jobid .'&tab=dest-gdrive&_wpnonce=' . wp_create_nonce( 'edit-job' ), 302 );
					wp_die();
				} catch ( Exception $e ) {
					BackWPup_Admin::message( sprintf( __( 'GDrive API: %s', 'backwpup' ), $e->getMessage() ) );
					BackWPup_Option::delete( $jobid, 'gdriveaccesstoken' );
					wp_redirect( network_admin_url( 'admin.php' ) . '?page=backwpupeditjob&jobid=' . $jobid .'&tab=dest-gdrive&_wpnonce=' . wp_create_nonce( 'edit-job' ), 302 );
					wp_die();
				}
			}
		}
	}


	/**
	 * @param $jobid
	 * @return string|void
	 */
	public function edit_form_post_save( $jobid ) {

		BackWPup_Option::update( $jobid, 'gdrivesyncnodelete', ( isset( $_POST[ 'gdrivesyncnodelete' ] ) && $_POST[ 'gdrivesyncnodelete' ] == 1 ) ? TRUE : FALSE );
		BackWPup_Option::update( $jobid, 'gdrivemaxbackups', isset( $_POST[ 'gdrivemaxbackups' ] ) ? (int)$_POST[ 'gdrivemaxbackups' ] : 0 );

		$_POST[ 'gdrivedir' ] = untrailingslashit( str_replace( '//', '/', str_replace( '\\', '/', trim( stripslashes( $_POST[ 'gdrivedir' ] ) ) ) ) );
		if ( substr( $_POST[ 'gdrivedir' ], 0, 1 ) != '/' )
			$_POST[ 'gdrivedir' ] = '/' . $_POST[ 'gdrivedir' ];
		BackWPup_Option::update( $jobid, 'gdrivedir', $_POST[ 'gdrivedir' ] );

	}

	/**
	 * @param $job_settings
	 */
	public function wizard_page( $job_settings ) {

		if ( ! get_site_option( 'backwpup_cfg_googleclientid' ) || ! get_site_option( 'backwpup_cfg_googleclientsecret' ) ) {
			?>
			<div id="message" class="updated below-h2">
				<p><?php echo sprintf( __( 'Looks like you haven’t set up any API keys yet. Head over to <a href="%s">Settings | API-Keys</a> and get Google Drive all set up, then come back here.', 'backwpup' ), admin_url( 'admin.php' ) . '?page=backwpupsettings' ); ?></p>
			</div>
		<?php
		}

		//google authentication
		try {
			$client = new Google_Client();
			if ( is_file( BackWPup::get_plugin_data( 'plugindir' ) . '/vendor/cacert.pem' ) )
				$client->getIo()->setOptions( array( CURLOPT_CAINFO => BackWPup::get_plugin_data( 'plugindir' ) . '/vendor/cacert.pem') );
			$client->setApplicationName( 'BackWPup v.' . BackWPup::get_plugin_data( 'version' ) );
			$client->setClientId( get_site_option( 'backwpup_cfg_googleclientid' ) );
			$client->setClientSecret( BackWPup_Encryption::decrypt( get_site_option( 'backwpup_cfg_googleclientsecret' ) ) );
			$client->setScopes( array( 'https://www.googleapis.com/auth/drive') );
			$client->setUseObjects( TRUE );
			$client->setRedirectUri( admin_url( 'admin-ajax.php' ) . '?action=backwpup_dest_gdrive' );
			$access_token = BackWPup_Encryption::decrypt( $job_settings[ 'gdriveaccesstoken' ] );
			$auth_url = $client->createAuthUrl();
		} catch ( Exception $e ) {
			echo '<div id="message" class="error"><p>' . sprintf( __( 'GDrive API: %s', 'backwpup' ), htmlentities( $e->getMessage() ) ) . '</p></div>';
		}

		?>
		<table class="form-table">
			<tr>
				<td>
					<fieldset>
						<?php
						//display if not automatized
						if ( empty( $access_token ) ) {
							?>
						<strong><?php _e( 'Login:', 'backwpup' ); ?></strong>&nbsp;
						<span style="color:red;"><?php _e( 'Not authenticated!', 'backwpup' ); ?></span>
						<a class="button secondary" href="<?php echo $auth_url ;?>"><?php _e( 'Authenticate', 'backwpup' ); ?></a>
						<?php
						} else {
							?>
						<strong><?php _e( 'Login:', 'backwpup' ); ?></strong>&nbsp;
						<span style="color:green;"><?php _e( 'Authenticated!', 'backwpup' ); ?></span>
						<a class="button secondary" href="<?php echo $auth_url ;?>"><?php _e( 'Reauthenticate', 'backwpup' ); ?></a>
						<br/>
						<br/>
						<strong><label for="idgdrivedir"><?php _e( 'Folder:', 'backwpup' ); ?></label></strong><br/>
						<input name="gdrivedir" id="idgdrivedir" type="text" value="<?php echo esc_attr( $job_settings[ 'gdrivedir' ] );?>" class="user large-text"/><br/>
						<?php
							if ( $job_settings[ 'backuptype' ] == 'archive' ) { ?>
								<label for="idgdrivemaxbackups"><input name="gdrivemaxbackups" id="idgdrivemaxbackups" type="text" size="3" value="<?php echo esc_attr( $job_settings[ 'gdrivemaxbackups' ] );?>" class="small-text help-tip" title="<?php esc_attr_e( 'Oldest files will be deleted first. 0 = no deletion', 'backwpup' ); ?>" />
								<?php  _e( 'Number of files to keep in folder.', 'backwpup' ); ?></label>
							<?php } else { ?>
								<label for="idgdrivesyncnodelete"><input class="checkbox" value="1" type="checkbox" <?php checked( $job_settings[ 'gdrivesyncnodelete' ], TRUE ); ?> name="gdrivesyncnodelete" id="idgdrivesyncnodelete" /> <?php _e( 'Do not delete files while syncing to destination!', 'backwpup' ); ?></label>
							<?php
							}
						} ?>
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

		if ( isset( $_POST[ 'gdrivedir' ] ) ) {
			$_POST[ 'gdrivedir' ] = untrailingslashit( str_replace( '//', '/', str_replace( '\\', '/', trim( stripslashes( $_POST[ 'gdrivedir' ] ) ) ) ) );
			if ( substr( $_POST[ 'gdrivedir' ], 0, 1 ) != '/' )
				$_POST[ 'gdrivedir' ] = '/' . $_POST[ 'gdrivedir' ];
			$job_settings[ 'gdrivedir' ] = $_POST[ 'gdrivedir' ];
		}

		if ( isset( $_POST[ 'gdrivesyncnodelete' ] ) )
			$job_settings[ 'gdrivesyncnodelete' ] = ( isset( $_POST[ 'gdrivesyncnodelete' ] ) && $_POST[ 'gdrivesyncnodelete' ] == 1 ) ? TRUE : FALSE;
		if ( isset( $_POST[ 'gdrivemaxbackups' ] ) )
			$job_settings[ 'gdrivemaxbackups' ] = (int) $_POST[ 'gdrivemaxbackups' ];

		return $job_settings;
	}

	/**
	 * @param $jobdest
	 * @param $backupfile
	 */
	public function file_delete( $jobdest, $backupfile ) {

		$files = get_site_transient( 'backwpup_' . strtolower( $jobdest ) );
		list( $jobid, $dest ) = explode( '_', $jobdest );

		if ( BackWPup_Option::get( $jobid, 'gdriveaccesstoken' ) ) {
			try {
				$client = new Google_Client();
				if ( is_file( BackWPup::get_plugin_data( 'plugindir' ) . '/vendor/cacert.pem' ) )
					$client->getIo()->setOptions( array( CURLOPT_CAINFO => BackWPup::get_plugin_data( 'plugindir' ) . '/vendor/cacert.pem') );
				$client->setApplicationName( 'BackWPup v.' . BackWPup::get_plugin_data( 'version' ) );
				$client->setClientId( get_site_option( 'backwpup_cfg_googleclientid' ) );
				$client->setClientSecret( BackWPup_Encryption::decrypt( get_site_option( 'backwpup_cfg_googleclientsecret' ) ) );
				$client->setScopes( array( 'https://www.googleapis.com/auth/drive') );
				$client->setUseObjects( TRUE );
				$access_token = BackWPup_Encryption::decrypt( BackWPup_Option::get( $jobid, 'gdriveaccesstoken' ) );
				$client->setAccessToken( $access_token );
				$service = new Google_DriveService( $client );
				$service->files->trash( $backupfile );
				//update file list
				foreach ( $files as $key => $file ) {
					if ( is_array( $file ) && $file[ 'file' ] == $backupfile )
						unset( $files[ $key ] );
				}
			}
			catch ( Exception $e ) {
				BackWPup_Admin::message( 'Google Drive: ' . $e->getMessage()  );
			}
		}
		set_site_transient( 'backwpup_' . strtolower( $jobdest ), $files, 60 * 60 * 24 * 7 );
	}


	/**
	 * @param $jobdest
	 * @return mixed
	 */
	public function file_get_list( $jobdest ) {

		return get_site_transient( 'BackWPup_' . $jobdest );
	}

	/**
	 * @param $job_object
	 * @return bool
	 */
	public function job_run_archive( &$job_object ) {

		$job_object->substeps_todo = 2 + $job_object->backup_filesize;
		if ( $job_object->steps_data[ $job_object->step_working ]['SAVE_STEP_TRY'] != $job_object->steps_data[ $job_object->step_working ][ 'STEP_TRY' ] )
			$job_object->log( sprintf( __( '%d. Try to send backup file to Google Drive&#160;&hellip;', 'backwpup' ), $job_object->steps_data[ $job_object->step_working ][ 'STEP_TRY' ] ), E_USER_NOTICE );


		try {

			$client = new Google_Client();
			if ( is_file( BackWPup::get_plugin_data( 'plugindir' ) . '/vendor/cacert.pem' ) )
				$client->getIo()->setOptions( array( CURLOPT_CAINFO => BackWPup::get_plugin_data( 'plugindir' ) . '/vendor/cacert.pem') );
			$client->setApplicationName( 'BackWPup v.' . BackWPup::get_plugin_data( 'version' ) );
			$client->setClientId( get_site_option( 'backwpup_cfg_googleclientid' ) );
			$client->setClientSecret( BackWPup_Encryption::decrypt( get_site_option( 'backwpup_cfg_googleclientsecret' ) ) );
			$client->setScopes( array( 'https://www.googleapis.com/auth/drive') );
			$client->setUseObjects( TRUE );
			$access_token = BackWPup_Encryption::decrypt( $job_object->job[ 'gdriveaccesstoken'] );
			$client->setAccessToken( $access_token );
			$this->service = new Google_DriveService( $client );

			//get the folder id and create folder
			if ( empty( $job_object->steps_data[ $job_object->step_working ][ 'folder_id' ] ) )
				$job_object->steps_data[ $job_object->step_working ][ 'folder_id' ] = $this->get_folder_id( $job_object->job[ 'gdrivedir' ] );

			// put the file
			if ( $job_object->steps_data[ $job_object->step_working ]['SAVE_STEP_TRY'] != $job_object->steps_data[ $job_object->step_working ][ 'STEP_TRY' ] && $job_object->substeps_done < $job_object->backup_filesize )
				$job_object->log( __( 'Uploading to Google Drive&#160;&hellip;', 'backwpup' ), E_USER_NOTICE );

			if ( $job_object->substeps_done < $job_object->backup_filesize ) {
				// Get resumable session url
				if ( empty ( $job_object->steps_data[ $job_object->step_working ][ 'resumable_uri' ] ) ) {
					if( $job_object->steps_data[ $job_object->step_working ][ 'folder_id' ] == 'root' )
						$post_fields = '{"title":"'.$job_object->backup_file.'","mimeType":"'.$job_object->get_mime_type( $job_object->backup_folder . $job_object->backup_file ).'"}' ;
					else
						$post_fields = '{"title":"'.$job_object->backup_file.'","mimeType":"'.$job_object->get_mime_type( $job_object->backup_folder . $job_object->backup_file ).'","parents": [{"kind":"drive#fileLink","id":"' . $job_object->steps_data[ $job_object->step_working ][ 'folder_id' ] . '"}]}' ;

					$ch = curl_init();
					curl_setopt( $ch, CURLOPT_URL, 'https://www.googleapis.com/upload/drive/v2/files?uploadType=resumable' );
					curl_setopt( $ch, CURLOPT_POST, TRUE );
					curl_setopt( $ch, CURLOPT_POSTFIELDS, $post_fields );
					curl_setopt( $ch, CURLOPT_HEADER, TRUE );
					curl_setopt( $ch, CURLOPT_RETURNTRANSFER, TRUE );
					curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, TRUE );
					curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 2 );
					curl_setopt( $ch, CURLOPT_SSLVERSION, 3 );
					if ( is_file( BackWPup::get_plugin_data( 'plugindir' ) . '/vendor/cacert.pem' ) )
						curl_setopt( $ch, CURLOPT_CAINFO, BackWPup::get_plugin_data( 'plugindir' ) . '/vendor/cacert.pem' );
					$access_token = json_decode( $client->getAccessToken() );
					curl_setopt( $ch, CURLOPT_HTTPHEADER, array( 'Authorization: ' . $access_token->token_type .' ' . $access_token->access_token,
																 'Content-Length: ' . strlen( $post_fields ),
																 'X-Upload-Content-Type: ' .$job_object->get_mime_type( $job_object->backup_folder . $job_object->backup_file ),
																 'X-Upload-Content-Length: ' . $job_object->backup_filesize,
																 'Content-Type: application/json; charset=UTF-8') );

					$response = curl_exec( $ch );
					$curlgetinfo = curl_getinfo( $ch );
					curl_close( $ch );


					if ( $curlgetinfo[ 'http_code' ] == 200  || $curlgetinfo[ 'http_code' ] == 201 ) {
						if ( preg_match( '/Location:(.*?)\r/i', $response, $matches ) )
							$job_object->steps_data[ $job_object->step_working ][ 'resumable_uri' ] = trim( $matches[ 1 ] );
					}

					// error checking
					if( empty( $job_object->steps_data[ $job_object->step_working ][ 'resumable_uri' ] ) ) {
						$job_object->log( __( 'Google Drive API: could not create resumable file', 'backwpup' ), E_USER_ERROR );

						return FALSE;

					}
				} else {
					//get actual position
					$ch = curl_init();
					curl_setopt( $ch, CURLOPT_URL, $job_object->steps_data[ $job_object->step_working ][ 'resumable_uri' ] );
					curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'PUT' );
					curl_setopt( $ch, CURLOPT_HEADER, TRUE );
					curl_setopt( $ch, CURLOPT_RETURNTRANSFER, TRUE );
					curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, TRUE );
					curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 2 );
					curl_setopt( $ch, CURLOPT_SSLVERSION, 3 );
					if ( is_file( BackWPup::get_plugin_data( 'plugindir' ) . '/vendor/cacert.pem' ) )
						curl_setopt( $ch, CURLOPT_CAINFO, BackWPup::get_plugin_data( 'plugindir' ) . '/vendor/cacert.pem' );
					$access_token = json_decode( $client->getAccessToken() );
					curl_setopt( $ch, CURLOPT_HTTPHEADER, array( 'Authorization: ' . $access_token->token_type .' ' . $access_token->access_token,
																 'Content-Length: 0',
																 'Content-Range: bytes */' . $job_object->backup_filesize ) );
					$response = curl_exec( $ch );
					$curlgetinfo = curl_getinfo( $ch );
					curl_close( $ch );
					if ( $curlgetinfo[ 'http_code'] == '308' && preg_match( '/Range:(.*?)\r/i', $response, $matches ) ) {
						$range = trim( $matches[ 1 ] );
						$ranges = explode( '-', $range );

						$job_object->substeps_done = $ranges[1] + 1;
					} else {
						$job_object->log( __( 'Can not resume transfer backup to Google Drive!', 'backwpup' ), E_USER_ERROR );

						return FALSE;
					}
				}

				//Upload in chunks
				$chunk_size = 4194304; //4194304 = 4MB
				$created_file = '';
				$file_handel = fopen( $job_object->backup_folder . $job_object->backup_file, 'rb' );
				//seek to file pos
				if ( ! empty( $job_object->substeps_done ) )
					fseek( $file_handel, $job_object->substeps_done );

				while ( $data_chunk = fread( $file_handel, $chunk_size ) ) {

					$chunk_upload_start = microtime( TRUE );

					$ch = curl_init();
					curl_setopt( $ch, CURLOPT_URL, $job_object->steps_data[ $job_object->step_working ][ 'resumable_uri' ] );
					curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'PUT' );
					curl_setopt( $ch, CURLOPT_POSTFIELDS, $data_chunk );
					curl_setopt( $ch, CURLOPT_RETURNTRANSFER, TRUE );
					curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, TRUE );
					curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 2 );
					curl_setopt( $ch, CURLOPT_SSLVERSION, 3 );
					if ( is_file( BackWPup::get_plugin_data( 'plugindir' ) . '/vendor/cacert.pem' ) )
						curl_setopt( $ch, CURLOPT_CAINFO, BackWPup::get_plugin_data( 'plugindir' ) . '/vendor/cacert.pem' );
					$access_token = json_decode( $client->getAccessToken() );
					$end_pos = $job_object->substeps_done + strlen( $data_chunk ) - 1;
					curl_setopt( $ch, CURLOPT_HTTPHEADER, array( 'Authorization: ' . $access_token->token_type . ' ' . $access_token->access_token,
																 'Content-Length: ' . strlen( $data_chunk ),
																 'Content-Range: bytes ' . $job_object->substeps_done . '-' . $end_pos . '/' . $job_object->backup_filesize ) );
					$response = curl_exec( $ch );
					$curlgetinfo = curl_getinfo( $ch );
					curl_close( $ch );
					$chunk_upload_time = microtime( TRUE ) - $chunk_upload_start;
					if ( $curlgetinfo[ 'http_code' ] == '200' || $curlgetinfo[ 'http_code' ] == '201' || $curlgetinfo[ 'http_code' ] == '308' ) {
						$created_file = json_decode( $response );
						$job_object->substeps_done = $end_pos + 1;
						if ( $curlgetinfo[ 'http_code' ] == '308' ) {
							$time_remaining = $job_object->do_restart_time();
							//calc next chunk
							if ( $time_remaining < $chunk_upload_time ) {
								$chunk_size = floor ( $chunk_size / $chunk_upload_time * ( $time_remaining - 3 ) );
								if ( $chunk_size < 0 )
									$chunk_size = 1024;
								if ( $chunk_size > 4194304 )
									$chunk_size = 4194304;
							}
						}
						$job_object->update_working_data();
					} else {
						$job_object->log(
										sprintf(
											__( 'Error transfering file chunks to %$1s.', 'backwpup' ),
											__( 'Google Drive', 'backwpup' )
										),
										E_USER_WARNING
										);

						return FALSE;
					}
				}
				fclose( $file_handel );
			}

			if ( is_object( $created_file ) && isset( $created_file->id ) && $created_file->fileSize == $job_object->backup_filesize ) {
				if ( ! empty( $job_object->job[ 'jobid' ] ) )
					BackWPup_Option::update(  $job_object->job[ 'jobid' ], 'lastbackupdownloadurl', str_replace( '&gd=true', '', $created_file->downloadUrl ) );
				$job_object->substeps_done = 1 + $job_object->backup_filesize;
				$job_object->log( sprintf( __( 'Backup transferred to %s', 'backwpup' ), $created_file->alternateLink ), E_USER_NOTICE );
			}
			else {
				if ( $created_file->fileSize != $job_object->backup_filesize )
					$job_object->log( __( 'Uploaded file size and local file size don\'t match.', 'backwpup' ), E_USER_ERROR );
				else
					$job_object->log(
									sprintf(
										__( 'Error transfering backup to %$1s.', 'backwpup' ),
										__( 'Google Drive', 'backwpup' )
									),
									E_USER_ERROR
									);

				return FALSE;
			}


			$backupfilelist = array();
			$filecounter    = 0;
			$files          = array();
			$metadata       = $this->search_files( "'" .  $job_object->steps_data[ $job_object->step_working ][ 'folder_id' ] . "' in parents and mimeType = '".$job_object->get_mime_type( $job_object->backup_folder . $job_object->backup_file )."' ", $this->service );
			if ( is_array( $metadata ) ) {
				foreach ( $metadata as $data ) {
					$file = $data->title;
					if ( $job_object->is_backup_archive( $file ) )
						$backupfilelist[ strtotime( $data->modifiedDate ) ] = $data->id;
					$files[ $filecounter ][ 'folder' ]      = $job_object->job[ 'gdrivedir' ];
					$files[ $filecounter ][ 'file' ]        = $data->id;
					$files[ $filecounter ][ 'filename' ]    = $data->title;
					$files[ $filecounter ][ 'downloadurl' ] = str_replace( '&gd=true', '', $data->downloadUrl );
					$files[ $filecounter ][ 'filesize' ]    = $data->fileSize;
					$files[ $filecounter ][ 'time' ]        = strtotime( $data->modifiedDate ) + ( get_option( 'gmt_offset' ) * 3600 );
					$filecounter ++;
				}
			}
			if ( $job_object->job[ 'gdrivemaxbackups' ] > 0 && is_object( $this->service ) ) { //Delete old backups
				if ( count( $backupfilelist ) > $job_object->job[ 'gdrivemaxbackups' ] ) {
					ksort( $backupfilelist );
					$numdeltefiles = 0;
					while ( $file = array_shift( $backupfilelist ) ) {
						if ( count( $backupfilelist ) < $job_object->job[ 'gdrivemaxbackups' ] )
							break;
						$response = $this->service->files->trash( $file ); //delete files on Cloud
						if ( $response ) {
							foreach ( $files as $key => $filedata ) {
								if ( $filedata[ 'file' ] ==  $file )
									unset( $files[ $key ] );
							}
							$numdeltefiles ++;
						}
						else
							$job_object->log( sprintf( __( 'Error while deleting file from Google Drive: %s', 'backwpup' ), $file ), E_USER_ERROR );
					}
					if ( $numdeltefiles > 0 )
						$job_object->log( sprintf( _n( 'One file deleted from Google Drive', '%d files deleted on Google Drive', $numdeltefiles, 'backwpup' ), $numdeltefiles ), E_USER_NOTICE );
				}
			}
			set_site_transient( 'backwpup_' . $job_object->job[ 'jobid' ] . '_gdrive', $files, 60 * 60 * 24 * 7 );
		}
		catch ( Exception $e ) {
			$job_object->log( E_USER_ERROR, sprintf( __( 'Google Drive API: %s', 'backwpup' ), htmlentities( $e->getMessage() ) ), $e->getFile(), $e->getLine() );

			return FALSE;
		}
		$job_object->substeps_done ++;

		return TRUE;
	}

	/**
	 * @param $job_object
	 * @return bool
	 */
	public function can_run( $job_object ) {

		if ( empty( $job_object->job[ 'gdriveaccesstoken' ] ) )
			return FALSE;

		return TRUE;
	}


	public function can_sync() {

		return TRUE;
	}


	public function job_run_sync( &$job_object ) {

		$job_object->substeps_todo = $job_object->count_folder + count( $job_object->additional_files_to_backup ) + 1;
		$job_object->substeps_done = 0;
		if ( $job_object->steps_data[ $job_object->step_working ]['SAVE_STEP_TRY'] != $job_object->steps_data[ $job_object->step_working ][ 'STEP_TRY' ] )
			$job_object->log( sprintf( __( '%d. Try to sync files to Google Drive&#160;&hellip;', 'backwpup' ), $job_object->steps_data[ $job_object->step_working ][ 'STEP_TRY' ] ), E_USER_NOTICE );

		try {
			$client = new Google_Client();

			if ( is_file( BackWPup::get_plugin_data( 'plugindir' ) . '/vendor/cacert.pem' ) )
				$client->getIo()->setOptions( array( CURLOPT_CAINFO => BackWPup::get_plugin_data( 'plugindir' ) . '/vendor/cacert.pem') );
			$client->setApplicationName( 'BackWPup v.' . BackWPup::get_plugin_data( 'version' ) );
			$client->setClientId( get_site_option( 'backwpup_cfg_googleclientid' ) );
			$client->setClientSecret( BackWPup_Encryption::decrypt( get_site_option( 'backwpup_cfg_googleclientsecret' ) ) );
			$client->setScopes( array( 'https://www.googleapis.com/auth/drive') );
			$client->setUseObjects( TRUE );
			$access_token = BackWPup_Encryption::decrypt( $job_object->job[ 'gdriveaccesstoken'] );
			$client->setAccessToken( $access_token );

			$this->service = new Google_DriveService( $client );

			//get Folders from dest
			$this->gdrive_folders = $job_object->data_storage( 'folders_gdrive' );
			if ( empty( $this->gdrive_folders ) ) {
				$job_object->log( __( 'Retrieving folder list from Google Drive', 'backwpup'  ) );
				$this->generate_folder_list();
				$job_object->data_storage( 'folders_gdrive', $this->gdrive_folders );
			}

			//remove entries that not in gdrive folder
			$gdrive_folders = $this->gdrive_folders;
			foreach( $gdrive_folders as $key => $folder ) {
				if ( substr( $folder[ 'path' ], 0 , strlen( $job_object->job[ 'gdrivedir' ] ) ) != $job_object->job[ 'gdrivedir' ] )
					unset( $gdrive_folders[ $key ] );
			}

			$backup_root_folder_id = $this->get_folder_id( $job_object->job[ 'gdrivedir' ] );
			$job_object->data_storage( 'folders_gdrive', $this->gdrive_folders );

			//Sync files
			if ( $job_object->steps_data[ $job_object->step_working ]['SAVE_STEP_TRY'] != $job_object->steps_data[ $job_object->step_working ][ 'STEP_TRY' ] )
				$job_object->log( __( 'Syncing changed files to Google Drive', 'backwpup' ) );


			foreach( $job_object->get_folders_to_backup() as $folder_to_backup_key => $folder_to_backup ) {
				//generate dest folder name
				$dest_folder_name = $job_object->job[ 'gdrivedir' ] . '/' .trim( str_replace( $job_object->remove_path, '', $folder_to_backup ), '/' );
				//get google folder id
				$folder_id = $this->get_folder_id( $dest_folder_name );
				$job_object->data_storage( 'folders_gdrive', $this->gdrive_folders );
				//remove folder form folder list for later deleting folders
				if ( isset( $gdrive_folders[ $folder_id ] ) )
					unset( $gdrive_folders[ $folder_id ] );
				//remove parent folder form list
				foreach( $gdrive_folders as $gdrive_folder_key => $gdrive_folder ) {
					if ( stristr( $dest_folder_name, $gdrive_folder[ 'path' ] ) )
						unset( $gdrive_folders[ $gdrive_folder_key ] );
				}
				//jump over if not the actual folder
				if ( $job_object->steps_data[ $job_object->step_working ][ 'on_folder_id' ] > $folder_to_backup_key ) {
					$job_object->substeps_done ++;
					continue;
				}
				//get files in folder
				$gdrive_files = $this->search_files( "'". $folder_id ."' in parents and mimeType != 'application/vnd.google-apps.folder'" );
				// get local files
				$files_in_folder = $job_object->get_files_in_folder( $folder_to_backup );
				foreach ( $files_in_folder as $file_in_folder ) {
					$dest_file_name = $job_object->job[ 'gdrivedir' ] . '/' . trim( str_replace( $job_object->remove_path, '', $file_in_folder ), '/' );
					foreach ( $gdrive_files as $gdrive_file_key => $gdrive_file ) {
						//file exists on gdrive
						if ( $gdrive_file->title == basename( $file_in_folder ) ) {
							//Upload file again if filesize not the same
							if ( $gdrive_file->fileSize != filesize( $file_in_folder ) ) {
								$this->service->files->update( $gdrive_file->id, $gdrive_file, array(
															  'data' => file_get_contents( $file_in_folder ),
															  'convert' => FALSE,
															  'mimeType' => $job_object->get_mime_type( $file_in_folder ) ) );
								$job_object->log( sprintf( __( 'File %s updated on Google Drive', 'backwpup' ), $dest_file_name  ) );
								$job_object->do_restart_time();
							}
							//remove found file from array
							unset( $gdrive_files[ $gdrive_file_key ] );
							continue 2;
						}
					}
					// if file not on gdrive upload it
					$file = new Google_DriveFile();
					$file->setTitle( basename( $file_in_folder ) );
					$file->setMimeType( $job_object->get_mime_type( $file_in_folder ) );
					$parent = new Google_ParentReference();
					$parent->setId( $folder_id );
					$file->setParents( array( $parent ) );
					$this->service->files->insert( $file, array(
															  'data' => file_get_contents( $file_in_folder ),
															  'convert' => FALSE,
															  'mimeType' => $job_object->get_mime_type( $file_in_folder ) ) );
					$job_object->log( sprintf( __( 'File %s uploaded to Google Drive', 'backwpup' ), $dest_file_name ) );
					$job_object->do_restart_time();
				}
				//remove extra files from file list so that the file can updated and will not deleted
				if ( $backup_root_folder_id == $folder_id && ! empty( $job_object->additional_files_to_backup ) ) {
					foreach ( $job_object->additional_files_to_backup as $additional_file ) {
						foreach( $gdrive_files as $gdrive_file_key => $gdrive_file ) {
							if ( $gdrive_file->title == basename( $additional_file ) )
								unset( $gdrive_files[ $gdrive_file_key ] );
						}
					}
				}
				//delete files that not longer exists
				if ( ! $job_object->job[ 'gdrivesyncnodelete' ] ) {
					foreach( $gdrive_files as $gdrive_file ) {
						if ( empty( $gdrive_file->id ) ) {
							$job_object->log( json_encode( $gdrive_file ) );
							continue;
						}
						$this->service->files->trash( $gdrive_file->id );
						$job_object->log( sprintf( __( 'File %s moved to trash in Google Drive', 'backwpup' ), $dest_folder_name . '/' .$gdrive_file->title ) );
						$job_object->do_restart_time();
					}
				}
				$job_object->steps_data[ $job_object->step_working ][ 'on_folder_id' ] = $folder_to_backup_key;
				$job_object->substeps_done ++;
				$job_object->do_restart_time();
				$job_object->update_working_data();
			}

			//delete folder that not longer exists
			if ( ! $job_object->job[ 'gdrivesyncnodelete' ] ) {
				foreach( $gdrive_folders as $gdrive_folder_key => $gdrive_folder ) {
					$this->service->files->trash( $gdrive_folder_key );
					$job_object->log( sprintf( __( 'Folder %s moved to trash in Google Drive', 'backwpup' ), $gdrive_folder[ 'path' ] ) );
					unset( $this->gdrive_folders[ $gdrive_folder_key ] );
					$job_object->data_storage( 'folders_gdrive', $this->gdrive_folders );
					$job_object->do_restart_time();
					$job_object->update_working_data();
				}
				$job_object->substeps_done ++;
			}

			//sync extra files
			if ( empty( $job_object->steps_data[ $job_object->step_working ][ 'on_file' ] ) )
				$job_object->steps_data[ $job_object->step_working ][ 'on_file' ] = 0;
			if ( ! empty( $job_object->additional_files_to_backup ) ) {
				$gdrive_files = $this->search_files( "'". $backup_root_folder_id ."' in parents and mimeType != 'application/vnd.google-apps.folder'" );
				for ( $i = $job_object->steps_data[ $job_object->step_working ][ 'on_file' ]; $i < count( $job_object->additional_files_to_backup); $i++ ) {
					$additional_file = $job_object->additional_files_to_backup[ $i ];
					foreach ( $gdrive_files as $gdrive_file ) {
						//file exists on gdrive
						if ( $gdrive_file->title == basename( $additional_file ) ) {
							//Update exciting file
							$responce = $this->service->files->update( $gdrive_file->id, $gdrive_file, array(
														  'data' => file_get_contents( $additional_file ),
														  'convert' => FALSE,
														  'mimeType' => $job_object->get_mime_type( $additional_file ) ) );
							if ( $responce->fileSize == filesize( $additional_file ) )
								$job_object->log( sprintf( __( 'Extra file %s updated on Google Drive', 'backwpup' ), $job_object->job[ 'gdrivedir' ] . '/'. basename( $additional_file )  ) );
							$job_object->substeps_done ++;
							$job_object->do_restart_time();
							$job_object->update_working_data();
							continue 2;
						}
					}
					$file = new Google_DriveFile();
					$file->setTitle( basename( $additional_file ) );
					$file->setMimeType( $job_object->get_mime_type( $additional_file ) );
					$parent = new Google_ParentReference();
					$parent->setId( $backup_root_folder_id );
					$file->setParents( array( $parent ) );
					$responce = $this->service->files->insert( $file, array(
															  'data' => file_get_contents( $additional_file ),
															  'convert' => FALSE,
															  'mimeType' => $job_object->get_mime_type( $additional_file ) ) );
					if ( $responce->fileSize == filesize( $additional_file ) )
						$job_object->log( sprintf( __( 'Extra file %s uploaded to Google Drive', 'backwpup' ), $job_object->job[ 'gdrivedir' ] . '/'. basename( $additional_file ) ) );
					$job_object->substeps_done ++;
					$job_object->steps_data[ $job_object->step_working ][ 'on_file' ] = $i;
					$job_object->do_restart_time();
					$job_object->update_working_data();
				}
			}


		} catch ( Exception $e ) {
			$job_object->log( E_USER_ERROR, sprintf( __( 'Google Drive API: %s', 'backwpup' ), htmlentities( $e->getMessage() ) ), $e->getFile(), $e->getLine() );

			return FALSE;
		}

		return TRUE;
	}


	/**
	 * https://developers.google.com/drive/search-parameters
	 *
	 * @param $query string
	 * @return array of results
	 */
	private function search_files( $query ) {

		$result = array();
		$pageToken = NULL;
		//exclude trashed from query
		if ( !empty( $query ) )
			$query .= ' and trashed != true';
		else
			$query = 'trashed != true';

		do {
			$parameters = array( 'q' => $query, 'maxResults' => 1000 );
			if ( $pageToken )
				$parameters['pageToken'] = $pageToken;
			$files = $this->service->files->listFiles( $parameters );
			$result = array_merge( $result, $files->items );
			$pageToken = !empty( $files->getNextPageToken ) ? $files->getNextPageToken : '';
		} while ( $pageToken );

		return $result;
	}

	/**
	 * Returns folder id of path and creates it if it not exists
	 *
	 * @param $path
	 * @return string
	 */
	private function get_folder_id( $path ) {

		$folder_id = 'root';
		if ( $path != '/' ) {
			if ( empty( $this->gdrive_folders ) )
				$this->generate_folder_list();
			$current_path = '';
			$folder_names = explode( '/', trim( $path, '/' ) );
			foreach( $folder_names as $folder_name ) {
				$current_path .=  '/' . $folder_name;
				foreach ( $this->gdrive_folders as $key => $folder ) {
					if ( $folder_id == $folder[ 'parent' ] && strtolower( $folder[ 'name'] ) == strtolower( trim( $folder_name ) ) ) {
						$folder_id = $key;
						continue 2;
					}
				}
				//create not existing folder
				$file = new Google_DriveFile();
				$file->setTitle( trim( $folder_name ) );
				$file->setMimeType( 'application/vnd.google-apps.folder' );
				if ( $folder_id != 'root') {
					$parent_reference = new Google_ParentReference();
					$parent_reference->setId( $folder_id );
					$file->setParents( array( $parent_reference ) );
				}
				$created_folder = $this->service->files->insert( $file, array( 'mimeType' => 'application/vnd.google-apps.folder' ) );
				$folder_id = $created_folder->id;
				$this->gdrive_folders[ $created_folder->id ] = array(   'name' => $created_folder->title,
																		'parent' => $created_folder->parents[0]->isRoot ? 'root' : $created_folder->parents[0]->id,
																		'path' => $current_path );
			}
		}

		return $folder_id;
	}

	/**
	 * Generate Google Drive folder list
	 */
	private function generate_folder_list() {

		$this->gdrive_folders = array();
		$search_folders = $this->search_files( "mimeType = 'application/vnd.google-apps.folder'" );
		//write array with folder information only
		foreach ( $search_folders as $search_folder ) {
			if ( isset( $search_folder->parents[0]->isRoot ) )
				$this->gdrive_folders[ $search_folder->id ] = array( 'name' => $search_folder->title,
																	 'parent' => $search_folder->parents[0]->isRoot ? 'root' : $search_folder->parents[0]->id );
		}

		//Generate full folder names
		foreach( $this->gdrive_folders as $key => $gdrive_folder ) {
			$this->gdrive_folders[ $key ][ 'path' ] = $gdrive_folder[ 'name' ];
			$start_folder = $gdrive_folder[ 'parent' ];

			while ( $start_folder != 'root' ) {
				$breake_on_secund_try = $start_folder;
				foreach ( $this->gdrive_folders as $f_key => $f_value ) {
					if ( $start_folder == $f_key ) {
						$this->gdrive_folders[ $key ][ 'path' ]  = $f_value[ 'name' ] . '/' . $this->gdrive_folders[ $key ][ 'path' ];
						$start_folder = $f_value[ 'parent' ];
						break;
					}
				}
				if ( $breake_on_secund_try == $start_folder )
					break;
			}
			$this->gdrive_folders[ $key ][ 'path' ] = '/' . $this->gdrive_folders[ $key ][ 'path' ];
		}
	}

}