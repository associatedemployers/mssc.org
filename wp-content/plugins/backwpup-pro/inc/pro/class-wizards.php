<?php
/**
 *
 */
abstract class BackWPup_Pro_Wizards {

	public $info = array();

	/**
	 *
	 */
	abstract public function __construct();

	/**
	 * with steps has the wizard to to
	 */
	abstract public function get_steps( $wizard_settings );


	/**
	 * Initiate Wizard Settings
	 */
	public function initiate( $wizard_settings ) {

		return $wizard_settings;
	}

	/**
	 * called on page admin_print_styles
	 */
	public function admin_print_styles( $wizard_settings ) {

	}

	/**
	 * called on page admin_print_scripts
	 */
	public function admin_print_scripts( $wizard_settings ) {

	}


	/**
	 * called on page
	 */
	abstract public function page( $wizard_settings );

	/**
	 * called on page inline_js
	 */
	public function inline_js( $wizard_settings ) {

	}

	/**
	 * called on page load to save form data
	 */
	abstract public function save( $wizard_settings );

	/**
	 * called if last button clicked
	 */
	abstract public function execute( $wizard_settings );

	/**
	 * executed if cancel button clicked
	 */
	public function cancel( $wizard_settings ) {

	}

	/**
	 * The name of the last button (execute button)
	 *
	 * @param $wizard_settings
	 * @return string
	 */
	abstract public function get_last_button_name( $wizard_settings ) ;


	/**
	 * Should the wizard run step by step or can yup between steps
	 *
	 * @param $wizard_settings
	 * @return bool
	 */
	public function is_step_by_step( $wizard_settings ) {

		return TRUE;
	}


	/**
	 * Set Pre configurations
	 *
	 * @param $id
	 * @return array
	 */
	public function get_pre_configurations( $id = NULL ) {

		// every configuration must have a name field in array
		$pre_configurations = array();

		if ( empty( $pre_configurations ) )
			return FALSE;

		if ( $id == NULL ) {
			$pre_configurations_names = array();
			foreach ( $pre_configurations as $id => $values )
				$pre_configurations_names[ $id ] = $values[ 'name' ];
			return $pre_configurations_names;
		} else {
			return $pre_configurations[ $id ];
		}

	}
}
