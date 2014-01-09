<?php
/**
 *
 */
class BackWPup_Pro_Destination_Email extends BackWPup_Destination_Email {


	/**
	 * @param $job_settings
	 */
	public function wizard_page( $job_settings ) {
		?>
		<table class="form-table">
			<tr>
				<td>
					<h3 class="title"><?php _e( 'Email address', 'backwpup' ); ?></h3>
					<fieldset>
						<label for="emailaddress"><strong><?php _e( 'Email address', 'backwpup' ); ?></strong><br/>
							<input name="emailaddress" id="emailaddress" type="text" title="<?php esc_attr_e('Email address to which Backups are sent.','backwpup'); ?>"
								   value="<?php echo esc_attr( $job_settings[ 'emailaddress' ] );?>" class="regular-text help-tip"/>
						</label><br />


						<label for="sendemailtest"><strong><?php _e( 'Send test email', 'backwpup' ); ?></strong><br/>
							<button id="sendemailtest" class="button secondary"><?php _e( 'Send test email', 'backwpup' ); ?></button>
						</label>
					</fieldset>
                </td>
            </tr>
		</table>
		<?php
    }


	public function wizard_inline_js() {
		//<script type="text/javascript">
		?>
		$('#sendemailtest').live('click', function() {
			$('#sendemailtest').after('&nbsp;<img id="emailsendtext" src="<?php echo get_admin_url().'images/loading.gif'; ?>" width="16" height="16" />');
			var data = {
				action: 'backwpup_dest_email',
				emailaddress: $('input[name="emailaddress"]').val(),
				emailsndemail: $('input[name="emailsndemail"]').val(),
				emailmethod: $('#emailmethod').val(),
				emailsendmail: $('input[name="emailsendmail"]').val(),
				emailhost: $('input[name="emailhost"]').val(),
				emailhostport: $('input[name="emailhostport"]').val(),
				emailsecure: $('#emailsecure').val(),
				emailuser: $('input[name="emailuser"]').val(),
				emailpass: $('input[name="emailpass"]').val(),
				_ajax_nonce: $('#backwpupajaxnonce').val()
			};
			$.post(ajaxurl, data, function(response) {
				$('#emailsendtext').replaceWith( response );
			});
    		return false;
		});
	<?php
	}

	/**
	 * @param $job_settings
	 */
	public function wizard_save( $job_settings ) {

		$job_settings[ 'emailaddress' ] = isset( $_POST[ 'emailaddress' ] ) ? sanitize_email( $_POST[ 'emailaddress' ] ) : '';

		$job_settings[ 'emailefilesize' ] = 25;
		$job_settings[ 'emailsndemail' ] = BackWPup::get_plugin_data( 'name' );
		$job_settings[ 'emailsndemailname' ] = 'BackWPup ' . get_bloginfo( 'name' );
		$job_settings[ 'emailmethod' ] = '';

		return $job_settings;
	}

}
