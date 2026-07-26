<?php
/**
 * Modifies the Divi contact form module fields.
 *
 * @package Contact_Form_DB_Divi
 */

/**
 * Class to modify the contact form module
 */
class Lwp_Cfdb_Modify_Module {

	// ===========================================================================================

	/**
	 * Constructor function that registers the filter to modify the module
	 */
	public function __construct() {
		add_filter( 'et_pb_all_fields_unprocessed_et_pb_contact_form', array( $this, 'add_contact_form_setting' ) );
		add_action( 'divi_visual_builder_assets_before_enqueue_scripts', array( $this, 'enqueue_divi5_unique_id_script' ) );
	}

	// ===========================================================================================

	/**
	 * Make the unique ID field of the contact form module visible.
	 *
	 * @param array $fields_unprocessed The unprocessed fields array for the contact form module.
	 * @return array The modified fields array with the visible unique ID field added.
	 */
	public function add_contact_form_setting( $fields_unprocessed ) {

		$fields = array();

		$fields['_unique_id'] = array(
			'label'           => __( 'Unique ID', 'contact-form-db-divi' ),
			'type'            => 'text',
			'attributes'      => 'readonly',
			'option_category' => 'basic_option',
			'toggle_slug'     => 'main_content',
		);

		return array_merge( $fields_unprocessed, $fields );
	}

	// ===========================================================================================

	/**
	 * Enqueue Divi 5 Unique ID field script
	 */
	public function enqueue_divi5_unique_id_script() {
		// Only load in Divi 5 Visual Builder.
		if ( ! function_exists( 'et_core_is_fb_enabled' ) || ! et_core_is_fb_enabled() ) {
			return;
		}

		// Check if Divi 5 is enabled.
		if ( ! function_exists( 'et_builder_d5_enabled' ) || ! et_builder_d5_enabled() ) {
			return;
		}

		wp_enqueue_script(
			'lwp-cfdb-divi5-unique-id',
			plugin_dir_url( __DIR__ ) . 'assets/js/unique-id-field.js',
			array( 'divi-module-library', 'divi-vendor-wp-hooks' ),
			LWP_CFDB_VERSION,
			true
		);
	}

	// ===========================================================================================
}
