<?php
/**
 *
 */
class BackWPup_Pro_JobType_DBCheck extends BackWPup_JobType_DBCheck {


	/**
	 * @param $job_settings
	 */
	public function wizard_page( $job_settings ) {
		?>
		<table class="form-table">
			<tr>
				<td>
					<h3 class="title"><?php _e( 'Settings for database check', 'backwpup' ) ?></h3>
					<p></p>
					<fieldset>
						<label for="iddbcheckwponly"><input class="checkbox" value="1"
							   type="checkbox" <?php checked( $job_settings[ 'dbcheckwponly' ], TRUE ); ?>
							   name="dbcheckwponly" id="iddbcheckwponly" /> <?php _e( 'Check only WordPress Database tables', 'backwpup' ); ?></label><br />

						<label for="iddbcheckrepair"><input class="checkbox" value="1" id="iddbcheckrepair"
							   type="checkbox" <?php checked( $job_settings[ 'dbcheckrepair' ], TRUE ); ?>
							   name="dbcheckrepair" /> <?php _e( 'Try to repair defect table', 'backwpup' ); ?></label><br />
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
		$job_settings[ 'dbcheckwponly' ] = ( isset( $_POST[ 'dbcheckwponly' ] ) && $_POST[ 'dbcheckwponly' ] == 1 ) ? TRUE : FALSE ;
		$job_settings[ 'dbcheckrepair' ] = ( isset( $_POST[ 'dbcheckrepair' ] ) && $_POST[ 'dbcheckrepair' ] == 1 ) ? TRUE : FALSE ;

		return $job_settings;
	}

}
