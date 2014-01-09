<?php
/**
 *
 */
class BackWPup_Pro_Destination_Ftp extends BackWPup_Destination_Ftp {


	/**
	 * @param $job_settings
	 */
	public function wizard_page( $job_settings ) {
		?>
		<table class="form-table">
			<tr>
				<td>
					<fieldset>
						<label for="idftphost"><strong><?php _e( 'Hostname:', 'backwpup' ); ?></strong><br/>
						<input name="ftphost" id="idftphost" type="text" value="<?php echo esc_attr( $job_settings[ 'ftphost' ] );?>"
							   class="large-text" autocomplete="off" /></label>
						<br/>
						<label for="idftphostport"><strong><?php _e( 'Port:', 'backwpup' ); ?></strong><br/>
						<input name="ftphostport" type="text" value="<?php echo esc_attr( $job_settings[ 'ftphostport' ] );?>"
							   class="small-text" id="idftphostport" /></label>
						<br/>
						<label id="idftpuser"><strong><?php _e( 'Username:', 'backwpup' ); ?></strong><br/>
						<input name="ftpuser" type="text" value="<?php echo esc_attr( $job_settings[ 'ftpuser' ] );?>"
							   class="user large-text" autocomplete="off" id="idftpuser" /></label>
						<br/>
						<label for="idftppass"><strong><?php _e( 'Password:', 'backwpup' ); ?></strong><br/>
						<input name="ftppass" type="password" value="<?php echo esc_attr( BackWPup_Encryption::decrypt( $job_settings[ 'ftppass' ] ) );?>"
							   class="password large-text" autocomplete="off" id="idftppass" /></label>
						<br/>
						<label for="idftpdir"><strong><?php _e( 'Folder on server:', 'backwpup' ); ?></strong><br/>
						<input name="ftpdir" id="idftpdir" type="text" value="<?php echo esc_attr( $job_settings[ 'ftpdir' ] );?>" class="large-text" /></label>
						<br/>

						<?php
						if ( $job_settings[ 'backuptype' ] == 'archive' ) {
							_e( 'Maximum number of backup files to keep in folder:', 'backwpup' );
							?>
							<label for="idftpmaxbackups"><input name="ftpmaxbackups" id="idftpmaxbackups" class="small-text" type="text" size="3" value="<?php echo esc_attr( $job_settings[ 'ftpmaxbackups' ] );?>" />
							<span class="description"><?php _e( '(Oldest files will be deleted first.)', 'backwpup' );?></span></label><br/>
						<?php } else { ?>
							<label for="idftpsyncnodelete"><input class="checkbox" value="1"
													  type="checkbox" <?php checked(  $job_settings[ 'ftpsyncnodelete' ], TRUE ); ?>
													  name="ftpsyncnodelete" id="idftpsyncnodelete" /> <?php _e( 'Do not delete files while syncing to destination!', 'backwpup' ); ?></label>
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

		$_POST[ 'ftphost' ] = str_replace( array( 'http://', 'ftp://' ), '', $_POST[ 'ftphost' ] );
		$job_settings[ 'ftphost' ] = isset( $_POST[ 'ftphost' ] ) ? $_POST[ 'ftphost' ] : '';

		$job_settings[ 'ftphostport' ] = ! empty( $_POST[ 'ftphostport' ] ) ? (int)$_POST[ 'ftphostport' ] : 21;
		$job_settings[ 'ftpuser' ] = isset( $_POST[ 'ftpuser' ] ) ? $_POST[ 'ftpuser' ] : '';
		$job_settings[ 'ftppass' ] = isset( $_POST[ 'ftppass' ] ) ? BackWPup_Encryption::encrypt( $_POST[ 'ftppass' ] ) : '';

		if ( ! empty( $_POST[ 'ftpdir' ] ) )
			$_POST[ 'ftpdir' ] = trailingslashit( str_replace( '//', '/', str_replace( '\\', '/', trim( stripslashes( $_POST[ 'ftpdir' ] ) ) ) ) );
		$job_settings[ 'ftpdir' ] =  $_POST[ 'ftpdir' ] ;

		if ( isset( $_POST[ 'ftpmaxbackups' ] ) )
			$job_settings[ 'ftpmaxbackups'] = isset( $_POST[ 'ftpmaxbackups' ] ) ? (int)$_POST[ 'ftpmaxbackups' ] : 0 ;

		$job_settings[ 'ftpssl'] = FALSE;
		$job_settings[ 'ftppasv' ] = TRUE;
		$job_settings[ 'ftptimeout' ] = 90;

		if ( isset( $_POST[ 'ftpsyncnodelete' ] ))
			$job_settings[ 'ftpsyncnodelete' ] = ( isset( $_POST[ 'ftpsyncnodelete' ] ) && $_POST[ 'ftpsyncnodelete' ] == 1 ) ? TRUE : FALSE;

		return $job_settings;
	}

}
