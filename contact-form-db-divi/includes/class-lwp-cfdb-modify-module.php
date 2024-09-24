<?php

/**
 * Class to modify the contact form module
 */
class Lwp_Cfdb_Modify_Module {

	//===========================================================================================

	/**
	 * Constructor function that registers the filter to modify the module
	 */
	public function __construct() {
		add_filter( 'et_pb_all_fields_unprocessed_et_pb_contact_form', array( $this, 'add_contact_form_setting' ) );
	}

	//===========================================================================================

	/**
	 * Make the unique ID field of the contact form module visible.
	 * @param array $fields_unprocessed The unprocessed fields array for the contact form module.
	 * @return array The modified fields array with the visible unique ID field added.
	 */
	function add_contact_form_setting( $fields_unprocessed ){

		$fields = [];

		$fields['_unique_id'] = [
			'label'           => __( 'Unique ID', 'contact-form-db-div' ),
			'type'            => 'text',
			'attributes'      => 'readonly',
			'option_category' => 'basic_option',
			'toggle_slug'     => 'main_content',
		];

		return array_merge( $fields_unprocessed, $fields );
	}

	//===========================================================================================

}
