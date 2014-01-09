<?php

/**
 *
 */
class BackWPup_Pro_Wizard_SystemTest extends BackWPup_Pro_Wizards {

	/**
	 *
	 */
	public function __construct() {

		$this->info[ 'ID' ]          = 'SYSTEMTEST';
		$this->info[ 'name' ]        = __( 'System Test', 'backwpup' );
		$this->info[ 'description' ] = __( 'Wizard to test if BackWPup can work properly', 'backwpup' );
		$this->info[ 'URI' ]         = translate( BackWPup::get_plugin_data( 'PluginURI' ), 'backwpup' );
		$this->info[ 'author' ]      = BackWPup::get_plugin_data( 'Author' );
		$this->info[ 'authorURI' ]   = translate( BackWPup::get_plugin_data( 'AuthorURI' ), 'backwpup' );
		$this->info[ 'version' ]     = BackWPup::get_plugin_data( 'Version' );
		$this->info[ 'cap' ] 		 = 'backwpup';

	}

	/**
	 * The name of the last button (execute button)
	 *
	 * @param $wizard_settings
	 * @return string
	 */
	public function get_last_button_name( $wizard_settings ) {

		return __( 'Run tests', 'backwpup' );
	}

	/**
	 * with steps has the wizard to to
	 */
	public function get_steps( $wizard_settings ) {

		$steps = array();
		$steps[0] = array( 'id' => 'ENV', 'name' => __( 'Environment', 'backwpup' ), 'description' => __( 'System Environment', 'backwpup' ) );

		return $steps;
	}


	/**
	 * called on page
	 */
	public function page( $wizard_settings ) {

		if ( $wizard_settings[ 'wizard' ][ 'step' ] == 'ENV' ) {

			printf( '<p>%s</p>', __('Test if BackWPup can work without problems.','backwpup') );
		}

	}

	/**
	 * called on page inline_js
	 */
	public function inline_js( $wizard_settings ) {

	}

	/**
	 * called on page load to save form data
	 */
	public function save( $wizard_settings ) {

		return $wizard_settings;
	}

	/**
	 * called if last button clicked
	 */
	public function execute( $wizard_settings ) {
		global $wpdb;
		/* @var wpdb $wpdb */

		$error 	 	= 0;
		$warning 	= 0;

		//WP Version check
		if ( version_compare( BackWPup::get_plugin_data( 'wp_version' ), '3.4', '<' ) ) {
			echo '<p class="error">' . sprintf( __( 'You must run WordPress version 3.4 or higher to use this plugin. You are using version %s now.','backwpup' ), BackWPup::get_plugin_data( 'wp_version' ) ) .'</p>';
			$error++;
		}
		// PHP Version check
		if ( version_compare( PHP_VERSION, '5.2.6', '<' ) ) {
			echo '<p class="error">' . sprintf( __( 'You must run PHP version 5.2.6 or higher to use this plugin. You are using version %s now.','backwpup' ), PHP_VERSION ) .'</p>';
			$error++;
		}
		elseif ( version_compare( PHP_VERSION, '5.2.6', '>=' ) && version_compare( PHP_VERSION, '5.3.2', '<' ) ) {
			echo '<p class="warning">' . sprintf( __( 'We recommend to run a PHP version above 5.3.2 to get the full plugin functionality. You are using version %s now.','backwpup' ), PHP_VERSION ) .'</p>';
			$warning++;
		}
		//mysql Version check
		if ( ! class_exists( 'mysqli' ) && version_compare( $wpdb->db_version( ), '5.0.7', '<' ) ) {
			echo '<p class="error">' . sprintf( __( 'You must have the MySQLi extension installed and a MySQL server version of 5.0.7 or higher to use this plugin. You are using version %s now.','backwpup' ), $wpdb->get_var( "SELECT VERSION() AS version" ) ) .'</p>';
			$error++;
		}
		//curl check
		if ( ! function_exists( 'curl_init' ) ) {
			echo '<p class="error">' . __( 'PHP cURL extension must be installed to use the full plugin functionality.','backwpup' ) .'</p>';
			$error++;
		}

		$extension_rec = _x(
			'We recommend to install the %1$s extension to generate %2$s archives.',
			'%1 = extension name, %2 = file suffix',
			'backwpup'
		);
		//ZIP Archive
		if ( ! class_exists( 'ZipArchive' ) ) {
			echo '<p class="warning">' . sprintf( $extension_rec, 'PHP ZIP', '.zip' ) .'</p>';
			$warning++;
		}
		//GZ
		if ( ! function_exists( 'gzopen' ) ) {
			echo '<p class="warning">' . sprintf( $extension_rec, 'PHP GZ', '.tar.gz' ) .'</p>';
			$warning++;
		}
		//bzip2
		if ( ! function_exists( 'bzopen' ) ) {
			echo '<p class="warning">' . sprintf( $extension_rec, 'PHP bzip2', '.tar.bz2' ) .'</p>';
			$warning++;
		}
		//safe mode
		if ( (bool)ini_get( 'safe_mode' ) ) {
			echo '<p class="error">'
			. str_replace( '\"','"', sprintf(
				_x( 'Please disable the deprecated <a href="%s">PHP safe mode</a>.', 'Link to PHP manual', 'backwpup' ),
				'http://php.net/manual/en/features.safe-mode.php'
			) )
			.'</p>';
			$error++;
		}
		//FTP
		if ( !function_exists( 'ftp_login' ) ) {
			echo '<p class="warning">' . __( 'We recommend to install the PHP FTP extension to use the FTP backup destination.','backwpup' ) .'</p>';
			$warning++;
		}
		//temp dir
		BackWPup_Job::check_folder( BackWPup::get_plugin_data( 'TEMP' ) );
		if ( is_dir( BackWPup::get_plugin_data( 'TEMP' ) ) ) {
			if ( ! is_writable( BackWPup::get_plugin_data( 'TEMP' ) ) || ! is_readable( BackWPup::get_plugin_data( 'TEMP' ) ) ) {
				echo '<p class="error">'
				. sprintf( __( 'Temp folder %s is not read or writable. Please set proper writing permissions.','backwpup' ), BackWPup::get_plugin_data( 'TEMP' ) )
				.'</p>';
				$error++;
			}
		} else {
			echo '<p class="error">' . sprintf( __( 'Temp folder %s does not exist and cannot be created. Please create it and set proper writing permissions.','backwpup' ), BackWPup::get_plugin_data( 'TEMP' ) ) .'</p>';
			$error++;
		}
		//log dir
		BackWPup_Job::check_folder( get_site_option( 'backwpup_cfg_logfolder' ) );
		if ( is_dir( get_site_option( 'backwpup_cfg_logfolder' ) ) ) {
			if ( ! is_writable( get_site_option( 'backwpup_cfg_logfolder' ) ) || ! is_readable( get_site_option( 'backwpup_cfg_logfolder' ) )  ) {
				echo '<p class="error">' . sprintf( __( 'Log folder %s is not readable or writable. Please set proper writing permissions.','backwpup' ), get_site_option( 'backwpup_cfg_logfolder' ) ) .'</p>';
				$error++;
			}
		} else {
			echo '<p class="error">' . sprintf( __( 'Log folder %s does not exist and cannot be created. Please create it and set proper writing permissions.','backwpup' ), get_site_option( 'backwpup_cfg_logfolder' ) ) .'</p>';
			$error++;
		}
		$raw_response = BackWPup_Job::get_jobrun_url( 'test' );
		if ( is_wp_error( $raw_response ) || 200 != wp_remote_retrieve_response_code( $raw_response ) || 204 != wp_remote_retrieve_response_code( $raw_response ) ) {
			if ( is_wp_error( $raw_response ) ) {
				echo '<p class="warning">' . sprintf( __( 'The HTTP response test result is an error: "%s".','backwpup' ), $raw_response->get_error_message() ) .'</p>';
				$warning++;
			}
			if ( 200 != wp_remote_retrieve_response_code( $raw_response ) && 204 != wp_remote_retrieve_response_code( $raw_response ) ) {
				echo '<p class="warning">' . sprintf( __( 'The HTTP response test result is a wrong HTTP status: %s. It should be status 200.','backwpup' ), wp_remote_retrieve_response_code( $raw_response ) ) .'</p>';
				$warning++;
			}
			$headers = wp_remote_retrieve_headers( $raw_response );
			if ( isset( $headers['x-backwpup-ver'] ) && $headers['x-backwpup-ver'] != BackWPup::get_plugin_data( 'version' ) ) {
				echo '<p class="warning">' . sprintf( __( 'The BackWPup HTTP response header returns a false value: "%s"','backwpup' ), $headers['x-backwpup-ver'] ) .'</p>';
				$warning++;
			}
		}
		//cron test
		$next_run = wp_next_scheduled( 'wp_update_plugins' );
		if ( ! $next_run )
			$next_run = wp_next_scheduled( 'wp_version_check' );
		if ( ! $next_run )
			$next_run = wp_next_scheduled( 'wp_update_themes' );
		if ( ! $next_run )
			$next_run = wp_next_scheduled( 'wp_scheduled_delete' );
		if ( $next_run && $next_run < ( time() - 3600 * 12 ) ) {
			echo '<p class="error">' .  __( 'WP-Cron seems to be broken. But it is needed to run scheduled jobs.','backwpup' ) .'</p>';
			$error++;
		}

		if ( empty( $error ) && empty( $warning ) ) {
			echo '<div id="message" class="updated below-h2"><p>'. __( 'All tests passed without errors.', 'backwpup') .'</p></div>';
		}
		if ( empty( $error ) && ! empty( $warning ) ) {
			_e( 'There is no error, but some warnings. BackWPup will work, but with limitations.', 'backwpup');
		}
		if ( ! empty( $error ) ) {
			_e( 'There are errors. Please correct them, or BackWPup cannot work.', 'backwpup');
		}
	}
}
