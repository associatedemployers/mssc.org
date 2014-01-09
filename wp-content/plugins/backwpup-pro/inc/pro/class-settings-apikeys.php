<?php
/**
 *
 */
class BackWPup_Pro_Settings_APIKeys {

	private static $instance = NULL;

	/**
	 *
	 */
	private function __construct() {

		add_action( 'backwpup_page_settings_tab_apikey', array( $this, 'backwpup_hash' ) );

		$destinations = BackWPup::get_registered_destinations();
		if ( isset( $destinations[ 'DROPBOX' ] ) )
			add_action( 'backwpup_page_settings_tab_apikey', array( $this, 'dropbox_keys_form' ) );
		if ( isset( $destinations[ 'SUGARSYNC' ] ) )
			add_action( 'backwpup_page_settings_tab_apikey', array( $this, 'sugarsync_keys_form' ) );
		if ( isset( $destinations[ 'GDRIVE' ] ) )
			add_action( 'backwpup_page_settings_tab_apikey', array( $this, 'google_keys_form' ) );

		//save settings
		add_action( 'backwpup_page_settings_save', array( $this, 'save_form' ) );
	}

	/**
	 * @return BackWPup_Pro_Settings_APIKeys|null
	 */
	public static function get_instance() {

		if (NULL === self::$instance) {
			self::$instance = new self;
		}
		return self::$instance;
	}


	public function backwpup_hash() {
		?>
		<h3 class="title"><?php _e( 'Hash key', 'backwpup' ); ?></h3>
		<p><?php _e( 'Hash Key for BackWPup. It will be used to have hashes in folder and file names. It must at least 6 chars long.', 'backwpup' ); ?></p>
		<table class="form-table">
			<tr>
				<th scope="row"><label for="hashid"><?php _e( 'Hash key:', 'backwpup' ); ?></label></th>
				<td>
					<input name="hash" type="text" id="hashid"
						   value="<?php echo get_site_option( 'backwpup_cfg_hash' );?>"
						   class="small-text" autocomplete="off" maxlength="12" />
				</td>
			</tr>
		</table>
	<?php
	}

	public function dropbox_keys_form() {
		?>
		<h3 class="title"><?php _e( 'Dropbox API Keys', 'backwpup' ); ?></h3>
		<p><?php _e( 'If you want to set your own Dropbox API Keys, you can do it here. Leave empty for default.', 'backwpup' ); ?></p>
		<table class="form-table">
			<tr>
				<th scope="row"><label for="dropboxappkey"><?php _e( 'Full Dropbox App key:', 'backwpup' ); ?></label></th>
				<td>
					<input name="dropboxappkey" type="text" id="dropboxappkey"
						   value="<?php echo get_site_option( 'backwpup_cfg_dropboxappkey' );?>"
						   class="regular-text" autocomplete="off" />
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="dropboxappsecret"><?php _e( 'Full Dropbox App secret:', 'backwpup' ); ?></label></th>
				<td>
					<input name="dropboxappsecret" type="password" id="dropboxappsecret"
						   value="<?php echo get_site_option( 'backwpup_cfg_dropboxappsecret' );?>"
						   class="regular-text" autocomplete="off" />
			</tr>

            <tr>
                <th scope="row"><label for="dropboxsandboxappkey"><?php _e( 'Sandbox App key:', 'backwpup' ); ?></label></th>
                <td>
                    <input name="dropboxsandboxappkey" type="text" id="dropboxsandboxappkey"
                           value="<?php echo get_site_option( 'backwpup_cfg_dropboxsandboxappkey' );?>"
                           class="regular-text" autocomplete="off" />
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="dropboxsandboxappsecret"><?php _e( 'Sandbox App secret:', 'backwpup' ); ?></label></th>
                <td>
                    <input name="dropboxsandboxappsecret" type="password" id="dropboxsandboxappsecret"
                           value="<?php echo get_site_option( 'backwpup_cfg_dropboxsandboxappsecret' );?>"
                           class="regular-text" autocomplete="off" />
            </tr>
		</table>
		<?php
	}


	public function sugarsync_keys_form() {
		?>
		<h3 class="title"><?php _e( 'SugarSync API Keys', 'backwpup' ); ?></h3>
		<p><?php _e( 'If you want to set your own SugarSync API keys you can do that here. Leave empty for default.', 'backwpup' ); ?></p>
		<table class="form-table">
			<tr>
				<th scope="row"><label for="sugarsynckey"><?php _e( 'Access Key ID:', 'backwpup' ); ?></label></th>
				<td>
					<input name="sugarsynckey" type="text" id="sugarsynckey"
						   value="<?php echo get_site_option( 'backwpup_cfg_sugarsynckey' );?>"
						   class="regular-text" autocomplete="off" />
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="sugarsyncsecret"><?php _e( 'Private Access Key:', 'backwpup' ); ?></label></th>
				<td>
					<input name="sugarsyncsecret" type="password" id="sugarsyncsecret"
						   value="<?php echo get_site_option( 'backwpup_cfg_sugarsyncsecret' );?>"
						   class="regular-text" autocomplete="off" />
			</tr>
            <tr>
                <th scope="row"><label for="sugarsyncappid"><?php _e( 'App ID:', 'backwpup' ); ?></label></th>
                <td>
                    <input name="sugarsyncappid" type="text" id="sugarsyncappid"
                           value="<?php echo get_site_option( 'backwpup_cfg_sugarsyncappid' );?>"
                           class="regular-text" autocomplete="off" />
                </td>
            </tr>
		</table>
	<?php
	}

	public function google_keys_form() {
		?>
		<h3 class="title"><?php _e( 'Google API Keys', 'backwpup' ); ?></h3>
		<p><a href="https://code.google.com/apis/console">https://code.google.com/apis/console</a></p>
		<table class="form-table">
			<tr>
				<th scope="row"><label for="googleclientid"><?php _e( 'Client ID:', 'backwpup' ); ?></label></th>
				<td>
					<input name="googleclientid" type="text" id="googleclientid"
						   value="<?php echo get_site_option( 'backwpup_cfg_googleclientid' );?>"
						   class="regular-text" autocomplete="off" />
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="googleclientsecret"><?php _e( 'Client secret:', 'backwpup' ); ?></label></th>
				<td>
					<input name="googleclientsecret" type="password" id="googleclientsecret"
						   value="<?php echo get_site_option( 'backwpup_cfg_googleclientsecret' );?>"
						   class="regular-text" autocomplete="off" />
			</tr>
            <tr>
                <th scope="row"><?php _e( 'Redirect URIs:', 'backwpup' ); ?></th>
                <td>
					<span class="code"><?php echo admin_url( 'admin-ajax.php' ) . '?action=backwpup_dest_gdrive'; ?></span>
					<br />
                    <span class="description"><?php _e( 'Add this URI in a new line to the field.', 'backwpup' ); ?></span>
                </td>
            </tr>
		</table>
	<?php
	}

	public function save_form() {

		if ( isset( $_POST[ 'default_settings' ] ) && $_POST[ 'default_settings' ] ) {
			//set default options if button clicked
			delete_site_option( 'backwpup_cfg_hash' );
			delete_site_option( 'backwpup_cfg_dropboxappkey' );
			delete_site_option( 'backwpup_cfg_dropboxappsecret' );
			delete_site_option( 'backwpup_cfg_dropboxsandboxappkey' );
			delete_site_option( 'backwpup_cfg_dropboxsandboxappsecret' );
			delete_site_option( 'backwpup_cfg_sugarsynckey' );
			delete_site_option( 'backwpup_cfg_sugarsyncsecret' );
			delete_site_option( 'backwpup_cfg_sugarsyncappid' );
		}

		if ( ! empty( $_POST[ 'hash' ] ) && strlen( $_POST[ 'hash' ] ) >= 6 )
			update_site_option( 'backwpup_cfg_hash', $_POST[ 'hash' ] );
		else
			delete_site_option( 'backwpup_cfg_hash' );

		if ( $_POST[ 'dropboxappkey' ] )
			update_site_option( 'backwpup_cfg_dropboxappkey', $_POST[ 'dropboxappkey' ] );
		else
			delete_site_option( 'backwpup_cfg_dropboxappkey' );

		if ( $_POST[ 'dropboxappsecret' ] )
			update_site_option( 'backwpup_cfg_dropboxappsecret', BackWPup_Encryption::encrypt( $_POST[ 'dropboxappsecret' ] ) );
		else
			delete_site_option( 'backwpup_cfg_dropboxappsecret' );

		if ( $_POST[ 'dropboxsandboxappkey' ] )
			update_site_option( 'backwpup_cfg_dropboxsandboxappkey', $_POST[ 'dropboxsandboxappkey' ] );
		else
			delete_site_option( 'backwpup_cfg_dropboxsandboxappkey' );

		if ( $_POST[ 'dropboxsandboxappsecret' ] )
			update_site_option( 'backwpup_cfg_dropboxsandboxappsecret',  BackWPup_Encryption::encrypt( $_POST[ 'dropboxsandboxappsecret' ] ) );
		else
			delete_site_option( 'backwpup_cfg_dropboxsandboxappsecret' );

		if ( $_POST[ 'sugarsynckey' ] )
			update_site_option( 'backwpup_cfg_sugarsynckey', $_POST[ 'sugarsynckey' ] );
		else
			delete_site_option( 'backwpup_cfg_sugarsynckey' );

		if ( $_POST[ 'sugarsyncsecret' ] )
			update_site_option( 'backwpup_cfg_sugarsyncsecret', BackWPup_Encryption::encrypt( $_POST[ 'sugarsyncsecret' ] ) );
		else
			delete_site_option( 'backwpup_cfg_sugarsyncsecret' );

		if ( $_POST[ 'sugarsyncappid' ] )
			update_site_option( 'backwpup_cfg_sugarsyncappid', $_POST[ 'sugarsyncappid' ] );
		else
			delete_site_option( 'backwpup_cfg_sugarsyncappid' );


		if ( $_POST[ 'googleclientsecret' ] )
			update_site_option( 'backwpup_cfg_googleclientsecret', BackWPup_Encryption::encrypt( $_POST[ 'googleclientsecret' ] ) );
		else
			delete_site_option( 'backwpup_cfg_googleclientsecret' );

		if ( $_POST[ 'googleclientid' ] )
			update_site_option( 'backwpup_cfg_googleclientid', $_POST[ 'googleclientid' ] );
		else
			delete_site_option( 'backwpup_cfg_googleclientid' );

	}


}
