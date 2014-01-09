<?php

/**
 * Class for calling the BackWPup Pro Features
 */
final class BackWPup_Pro {

	private static $instance = NULL;

	/**
	 *
	 */
	private function __construct() {

		//Add menu page after logs
		add_filter( 'backwpup_admin_pages', array( $this, 'admin_page_wizards'), 4 );
		//Add menu page after logs
		add_filter( 'backwpup_admin_pages', array( $this, 'admin_page_documentation'), 11 );
		//Add or overwrite destinations
		add_filter( 'backwpup_register_destination', array( $this, 'register_destination' ), 5 );
		//Add or overwrite job types
		add_filter( 'backwpup_job_types', array( $this, 'job_types' ), 5 );
		//Add or overwrite wizards
		add_filter( 'backwpup_pro_wizards', array( $this, 'wizards' ), 5 );

		//Add Export Job things
		add_filter( 'backwpup_page_jobs_get_bulk_actions',array( 'BackWPup_Pro_Export_Jobs', 'page_jobs_get_bulk_actions' ) );
		add_filter( 'backwpup_page_jobs_actions',array( 'BackWPup_Pro_Export_Jobs', 'page_jobs_actions' ), 10, 3);
		add_action( 'backwpup_page_jobs_load', array( 'BackWPup_Pro_Export_Jobs', 'page_jobs_load' ) );

		//add admin menu points for prp
		if ( ! defined( 'DOING_CRON' ) )
			add_action( 'admin_bar_menu', array( $this, 'admin_bar_menu' ), 101 );

		//add owen API Keys
		if ( isset( $_REQUEST['page'] ) && $_REQUEST['page'] == 'backwpupsettings' )
			add_action( 'admin_init', array( 'BackWPup_Pro_Settings_APIKeys', 'get_instance' ), 5 );

		//add marketpress classes
		if ( is_admin() ) {
			if ( ! class_exists( 'marketpress_autoupdate', FALSE ) )
				require dirname( __FILE__ ) . '/class-auto-update.php';
			new marketpress_autoupdate( BackWPup::get_plugin_data( 'Slug' ) , BackWPup::get_plugin_data( 'MainFile' ) );
			// load documentation feature
			$this->documentation_object = new BackWPup_Pro_Marketpress_Documentation( BackWPup::get_plugin_data( 'Slug' ), BackWPup::get_plugin_data( 'MainFile' ) );
		}

	}

	/**
	 * @static
	 * @return \BackWPup_Pro
	 */
	public static function get_instance() {

		if (NULL === self::$instance) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	private function __clone() {}


	/**
	 * Add extra Destinations or overwrite
	 *
	 * @param $destinations
	 * @return array
	 */
	public function register_destination( $destinations ) {

		//add/overwrite BackWPup Destinations
		// to folder
		$destinations[ 'FOLDER' ][ 'class']	= 'BackWPup_Pro_Destination_Folder';
		$destinations[ 'FOLDER' ][ 'can_sync']	= TRUE;
		// backup with mail
		$destinations[ 'EMAIL' ][ 'class']	= 'BackWPup_Pro_Destination_Email';
		// backup to ftp
		$destinations[ 'FTP' ][ 'class'] = 'BackWPup_Pro_Destination_Ftp';
		// backup to dropbox
		$destinations[ 'DROPBOX' ][ 'class'] = 'BackWPup_Pro_Destination_Dropbox';
		$destinations[ 'DROPBOX' ][ 'can_sync']	= TRUE;
		// Backup to S3
		if ( version_compare( PHP_VERSION, '5.3.3', '>=' ) )
			$destinations[ 'S3' ][ 'class'] = 'BackWPup_Pro_Destination_S3';
		else
			$destinations[ 'S3' ][ 'class'] = 'BackWPup_Pro_Destination_S3_V1';
		$destinations[ 'S3' ][ 'can_sync']	= TRUE;
		// backup to MS Azure
		$destinations[ 'MSAZURE' ][ 'class'] = 'BackWPup_Pro_Destination_MSAzure';
		$destinations[ 'MSAZURE' ][ 'can_sync']	= TRUE;
		// backup to Rackspace Cloud
		$destinations[ 'RSC' ][ 'class'] = 'BackWPup_Pro_Destination_RSC';
		$destinations[ 'RSC' ][ 'can_sync']	= TRUE;
		// backup to Sugarsync
		$destinations[ 'SUGARSYNC' ][ 'class'] = 'BackWPup_Pro_Destination_SugarSync';
		// backup to Amazon Glacier
		$destinations[ 'GLACIER' ] 	= array(
			'class' => 'BackWPup_Pro_Destination_Glacier',
			'info'	=> array(
				'ID'        	=> 'GLACIER',
				'name'       	=> __( 'Glacier', 'backwpup' ),
				'description' 	=> __( 'Backup to Amazon Glacier', 'backwpup' ),
			),
			'can_sync' => FALSE,
			'needed' => array(
				'php_version'	=> '5.3.3',
				'functions'	=> array( 'curl_exec' ),
				'classes'	=> array()
			)
		);
		// backup to Google Drive
		$destinations[ 'GDRIVE' ] 	= array(
			'class' => 'BackWPup_Pro_Destination_GDrive',
			'info'	=> array(
				'ID'        	=> 'GDRIVE',
				'name'       	=> __( 'GDrive', 'backwpup' ),
				'description' 	=> __( 'Backup to Google Drive', 'backwpup' ),
			),
			'can_sync' => TRUE,
			'needed' => array(
				'php_version'	=> '',
				'functions'	=> array( 'curl_init', 'json_decode', 'http_build_query' ),
				'classes'	=> array()
			)
		);

		return $destinations;
	}

	/**
	 * Add extra Job types or overwrite
	 *
	 * @param $job_types
	 * @return array
	 */
	public function job_types( $job_types ) {

		if ( class_exists( 'mysqli' ) )
			$job_types[ 'DBDUMP' ] 	= new BackWPup_Pro_JobType_DBDump;
		$job_types[ 'FILE' ] 		= new BackWPup_Pro_JobType_File;
		$job_types[ 'WPEXP' ] 		= new BackWPup_Pro_JobType_WPEXP;
		$job_types[ 'WPPLUGIN' ]  	= new BackWPup_Pro_JobType_WPPlugin;
		$job_types[ 'DBCHECK' ]   	= new BackWPup_Pro_JobType_DBCheck;

		return $job_types;
	}

	/**
	 * Add extra Wizards or overwrite
	 *
	 * @param $wizards
	 * @return array
	 */
	public function wizards( $wizards ) {

		$wizards[ 'SYSTEMTEST' ]	= new BackWPup_Pro_Wizard_SystemTest; //first in the list
		$wizards[ 'JOB' ] 			= new BackWPup_Pro_Wizard_Job;
		$wizards[ 'JOBIMPORT' ] 	= new BackWPup_Pro_Wizard_JobImport;

		return $wizards;
	}

	/**
	 *  Add wizards Page
	 */
	public function admin_page_wizards( $page_hooks ) {

		$page_hooks[ 'backwpupwizards' ] = add_submenu_page( 'backwpup', __( 'Wizards', 'backwpup' ), __( 'Wizards', 'backwpup' ), 'backwpup', 'backwpupwizard', array( 'BackWPup_Pro_Page_Wizard', 'page' ) );
		add_action( 'load-' . $page_hooks[ 'backwpupwizards' ], array( 'BackWPup_Admin', 'init_generel' ) );
		add_action( 'load-' . $page_hooks[ 'backwpupwizards' ], array( 'BackWPup_Pro_Page_Wizard', 'load' ) );
		add_action( 'admin_print_styles-' . $page_hooks[ 'backwpupwizards' ], array( 'BackWPup_Pro_Page_Wizard', 'admin_print_styles' ) );
		add_action( 'admin_print_scripts-' . $page_hooks[ 'backwpupwizards' ], array( 'BackWPup_Pro_Page_Wizard', 'admin_print_scripts' ) );

		return $page_hooks;
	}

    /**
	 * Adds Documentation admin page
     */
	public function admin_page_documentation( $page_hooks ) {

		$page_hooks[ 'backwpupdocumentation' ] = add_submenu_page( 'backwpup', __( 'Documentation', 'backwpup' ), __( 'Documentation', 'backwpup' ), 'backwpup', 'backwpupdocumentation', array( $this->documentation_object, 'documentation_page') );
		add_action( 'load-' . $page_hooks[ 'backwpupdocumentation' ], array( 'BackWPup_Admin', 'init_generel' ) );
		add_action( 'admin_print_styles-' . $page_hooks[ 'backwpupdocumentation' ], array( 'BackWPup_Pro_Page_Wizard', 'admin_print_styles' ) );
		return $page_hooks;
	}

	/**
	 * Add admin bar menu points for pro
	 */
	public function admin_bar_menu() {
		global $wp_admin_bar;

		if ( ! current_user_can( 'backwpup' ) || ! get_site_option( 'backwpup_cfg_showadminbar' ) )
			return;

		/* @var WP_Admin_bar $wp_admin_bar */

		$wizards = BackWPup::get_wizards();

		$wp_admin_bar->add_menu( array(
								  'id'     => 'backwpup_wizard',
								  'parent' => 'backwpup',
								  'title'  => __( 'Wizards', 'backwpup' ),
								  'href'   => network_admin_url( 'admin.php' ) . '?page=backwpupwizard'
							 ) );


		foreach ( $wizards as $wizard_class ) {
			if ( ! current_user_can( $wizard_class->info[ 'cap' ] ) )
				continue;
			$wp_admin_bar->add_menu( array(
										  'id'     => 'backwpup_wizard_' . $wizard_class->info[ 'ID' ],
										  'parent' => 'backwpup_wizard',
										  'title'  => $wizard_class->info[ 'name' ],
										  'href'   => network_admin_url( 'admin.php' ) . '?page=backwpupwizard&wizard_start=' . $wizard_class->info[ 'ID' ]
									 ) );
		}

	}

}