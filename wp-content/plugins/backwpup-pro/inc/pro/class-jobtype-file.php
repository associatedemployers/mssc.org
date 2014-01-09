<?php
/**
 *
 */
class BackWPup_Pro_JobType_File extends BackWPup_JobType_File {


	/**
	 * @param $job_settings
	 */
	public function wizard_page( $job_settings ) {
		?>
		<table class="form-table">
			<tr>
				<td>
					<fieldset>
						<label for="backuproot"><input class="checkbox" id="backuproot"
							   type="checkbox"<?php checked( $job_settings[ 'backuproot' ], TRUE, TRUE );?>
							   name="backuproot" value="1" /> <?php _e( 'Backup WordPress main files', 'backwpup' );?></label>
						<br />
						<label for="idbackupcontent"><input class="checkbox" id="idbackupcontent"
							   type="checkbox"<?php checked( $job_settings[ 'backupcontent' ], TRUE, TRUE );?>
							   name="backupcontent" value="1" /> <?php _e( 'Backup blog content folder', 'backwpup' );?></label>
						<br />
						<label for="idbackupplugins"><input class="checkbox" id="idbackupplugins"
							   type="checkbox"<?php checked($job_settings[ 'backupplugins' ], TRUE, TRUE );?>
							   name="backupplugins" value="1" /> <?php _e( 'Backup blog plugins', 'backwpup' );?></label>
						<br />
						<label for="idbackupthemes"><input class="checkbox" id="idbackupthemes"
							   type="checkbox"<?php checked( $job_settings[ 'backupthemes' ], TRUE, TRUE );?>
							   name="backupthemes" value="1" /> <?php _e( 'Backup blog themes', 'backwpup' );?></label></span>
						<br />
						<label for="idbackupuploads"><input class="checkbox" id="idbackupuploads"
							   type="checkbox"<?php checked( $job_settings[ 'backupuploads' ], TRUE, TRUE );?>
							   name="backupuploads" value="1" /> <?php _e( 'Backup blog uploads folder', 'backwpup' );?></label>
						<br />
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

		$job_settings[ 'fileexclude' ]				= '';
		$job_settings[ 'dirinclude' ] 				= '';
		$job_settings[ 'backupexcludethumbs' ] 		= FALSE;
		$job_settings[ 'backuproot' ] 				= ( isset( $_POST[ 'backuproot' ] ) && $_POST[ 'backuproot' ] == 1 ) ? TRUE : FALSE;
		if ( ! $job_settings[ 'backuproot' ] )
			$job_settings['backupspecialfiles'] 	= FALSE;
		else
			$job_settings['backupspecialfiles'] 	= TRUE;
		//exclude folder that nothing have to do with WordPress
		$job_settings[ 'backuprootexcludedirs' ] 	= array();
		$job_settings[ 'backupcontent' ] 			= ( isset( $_POST[ 'backupcontent' ] ) && $_POST[ 'backupcontent' ] == 1 ) ? TRUE : FALSE;
		$job_settings[ 'backupcontentexcludedirs' ] = array( 'upgrade' );
		$job_settings[ 'backupplugins' ] 			= ( isset( $_POST[ 'backupplugins' ] ) && $_POST[ 'backupplugins' ] == 1 ) ? TRUE : FALSE;
		$job_settings[ 'backuppluginsexcludedirs' ] = array();
		$job_settings[ 'backupthemes' ] 			= ( isset( $_POST[ 'backupthemes' ] ) && $_POST[ 'backupthemes' ] == 1 ) ? TRUE : FALSE;
		$job_settings[ 'backupthemesexcludedirs' ] 	= array();
		$job_settings[ 'backupuploads' ] 			= ( isset( $_POST[ 'backupuploads' ] ) && $_POST[ 'backupuploads' ] == 1 ) ? TRUE : FALSE;
		$job_settings[ 'backupuploadsexcludedirs' ] = array();

		return $job_settings;
	}

}
