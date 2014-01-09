<?php
/**
 *
 */
class BackWPup_Pro_Destination_SugarSync extends BackWPup_Destination_SugarSync {

	/**
	 * @param $job_settings
	 */
	public function wizard_page( $job_settings ) {
		?>
		<table class="form-table">
			<tr>
				<td>
					<fieldset>
					<?php if ( ! $job_settings[ 'sugarrefreshtoken' ] ) { ?>
						<label for="sugaremail"><strong><?php _e( 'Email address:', 'backwpup' ); ?></strong><br/>
						<input id="sugaremail" name="sugaremail" type="text" value="<?php if ( isset( $_POST[ 'sugaremail' ] ) ) echo $_POST[ 'sugaremail' ];?>" class="large-text" autocomplete="off" /></label>
						<br/>
						<label for="sugarpass"><strong><?php _e( 'Password:', 'backwpup' ); ?></strong><br/>
						<input id="sugarpass" name="sugarpass" type="password" value="<?php if ( isset( $_POST[ 'sugarpass' ] ) ) echo $_POST[ 'sugarpass' ];?>" class="large-text" autocomplete="off" /></label>
						<br/>
						<br/>
						<input type="submit" name="wizard_button" class="button-primary" accesskey="d"
							   value="<?php _e( 'Sugarsync authenticate!', 'backwpup' ); ?>"/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" name="wizard_button" class="button"
															 value="<?php _e( 'Create Sugarsync account', 'backwpup' ); ?>"/>
						<br/>
						<?php }
					else { ?>
						<strong><?php _e( 'Login:', 'backwpup' ); ?></strong>&nbsp;
						<span style="color:green;"><?php _e( 'Authenticated!', 'backwpup' ); ?></span>
						<input type="submit" name="wizard_button" class="button-primary" accesskey="d"
							   value="<?php _e( 'Delete Sugarsync authentication!', 'backwpup' ); ?>"/>
						<br/>
						<strong><?php _e( 'Root:', 'backwpup' ); ?></strong>
						<?php
						try {
							$sugarsync   = new BackWPup_Destination_SugarSync_API( $job_settings[ 'sugarrefreshtoken' ] );
							$user        = $sugarsync->user();
							$syncfolders = $sugarsync->get( $user->syncfolders );
							if ( ! is_object( $syncfolders ) )
								echo '<span style="color:red;">' . __( 'No Syncfolders found!', 'backwpup' ) . '</span>';
						}
						catch ( Exception $e ) {
							echo '<span style="color:red;">' . $e->getMessage() . '</span>';
						}
						if ( isset( $syncfolders ) && is_object( $syncfolders ) ) {
							echo '<select name="sugarroot" id="sugarroot">';
							foreach ( $syncfolders->collection as $roots ) {
								echo "<option " . selected( strtolower( $job_settings[ 'sugarroot' ] ), strtolower( $roots->ref ), FALSE ) . " value=\"" . $roots->ref . "\">" . $roots->displayName . "</option>";
							}
							echo '</select>';
						}
						?>
					<?php } ?>
					<br/>
					<label id="idsugardir"><strong><?php _e( 'Folder:', 'backwpup' ); ?></strong><br/>
					<input name="sugardir" id="idsugardir" type="text" value="<?php echo $job_settings[ 'sugardir' ];?>" class="large-text" /></label><br/>
					<?php if ( $job_settings[ 'backuptype' ] == 'archive' ) {
							_e( 'Maximum number of backup files to keep in folder:', 'backwpup' ); ?>
						<label for="idsugarmaxbackups"><input name="sugarmaxbackups" id="idsugarmaxbackups" type="text" size="3" value="<?php echo $job_settings[ 'sugarmaxbackups' ];?>" class="small-text" />
						<span class="description"><?php _e( '(Oldest files will be deleted first.)', 'backwpup' );?></span></label>
						<br/>
					<?php } else { ?>
						<label for="idsugarsyncnodelete"><input class="checkbox" value="1"
												  type="checkbox" <?php checked( $job_settings[ 'sugarsyncnodelete' ], TRUE ); ?>
												  name="sugarsyncnodelete" id="idsugarsyncnodelete" /> <?php _e( 'Do not delete files while syncing to destination!', 'backwpup' ); ?></label>
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

		if ( ! empty( $_POST[ 'sugaremail' ] ) && ! empty( $_POST[ 'sugarpass' ] ) && $_POST[ 'wizard_button' ] == __( 'Sugarsync authenticate!', 'backwpup' ) ) {
			try {
				$sugarsync     = new BackWPup_Destination_SugarSync_API();
				$refresh_token = $sugarsync->get_Refresh_Token( $_POST[ 'sugaremail' ], $_POST[ 'sugarpass' ] );
				if ( ! empty( $refresh_token ) )
					$job_settings[ 'sugarrefreshtoken' ] = $refresh_token ;
			}
			catch ( Exception $e ) {
				BackWPup_Admin::message( 'SUGARSYNC: ' . $e->getMessage(), TRUE );
			}
		}

		if ( isset( $_POST[ 'wizard_button' ] ) && $_POST[ 'wizard_button' ] == __( 'Delete Sugarsync authentication!', 'backwpup' ) ) {
			$job_settings[ 'sugarrefreshtoken' ] = '';
		}

		if ( isset( $_POST[ 'wizard_button' ] ) && $_POST[ 'wizard_button' ] == __( 'Create Sugarsync account', 'backwpup' ) ) {
			try {
				$sugarsync = new BackWPup_Destination_SugarSync_API();
				$sugarsync->create_account( $_POST[ 'sugaremail' ], $_POST[ 'sugarpass' ] );
			}
			catch ( Exception $e ) {
				BackWPup_Admin::message( 'SUGARSYNC: ' . $e->getMessage(), TRUE );
			}
		}

		$_POST[ 'sugardir' ] = trailingslashit( str_replace( '//', '/', str_replace( '\\', '/', trim( stripslashes( $_POST[ 'sugardir' ] ) ) ) ) );
		if ( substr( $_POST[ 'sugardir' ], 0, 1 ) == '/' )
			$_POST[ 'sugardir' ] = substr( $_POST[ 'sugardir' ], 1 );
		if ( $_POST[ 'sugardir' ] == '/' )
			$_POST[ 'sugardir' ] = '';
		$job_settings[ 'sugardir' ] = $_POST[ 'sugardir' ];

		$job_settings[ 'sugarroot' ] = isset( $_POST[ 'sugarroot' ] ) ? $_POST[ 'sugarroot' ] : '';
		$job_settings[ 'sugarmaxbackups' ] = isset( $_POST[ 'sugarmaxbackups' ] ) ? (int)$_POST[ 'sugarmaxbackups' ] : 0;
		$job_settings[ 'sugarsyncnodelete' ] = ( isset( $_POST[ 'sugarsyncnodelete' ] ) && $_POST[ 'sugarsyncnodelete' ] == 1 ) ? TRUE : FALSE;

		return $job_settings;
	}
}
