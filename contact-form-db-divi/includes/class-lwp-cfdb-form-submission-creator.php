<?php

/**
 * Class to add new posts on form submissions
 */
class Lwp_Cfdb_Form_Submission_Creator {

	//===========================================================================================

	/**
	 * Constructor function that registers the Divi contact form hook
	 */
	public function __construct() {
		add_action('et_pb_contact_form_submit', array( $this, 'add_new_post' ), 10, 3);
	}

	//===========================================================================================

	/**
	 * Adds a new post to the lwp_form_submission CPT when a Divi form is submitted
	 *
	 * @param array $processed_fields_values	Processed fields values
	 * @param array $et_contact_error	 		Whether there is an error on the form entry submit process or not
	 * @param array $contact_form_info	 		An array of post row actions.
	 */
	function add_new_post($processed_fields_values, $et_contact_error, $contact_form_info) {

		if ( $et_contact_error == true ) {
			return;
		}

		// Define the post data for form submission
		$post_data = array(
			'post_title'  => 'View Form Submission', // Set the post title
			'post_type'   => 'lwp_form_submission', // Set the custom post type
			'post_status' => 'publish' // Set the post status to 'publish'
		);

		// Insert the post and get the post ID
		$post_id = wp_insert_post($post_data);

		// Save the processed fields values as post meta
		update_post_meta($post_id, 'processed_fields_values', $processed_fields_values);

		//
		$current_page_id = get_the_ID();

		// page submitted on details
		$additional_details = array(
			'page_id'                => $current_page_id,
			'page_name'              => get_the_title($current_page_id),
			'page_url'               => get_permalink($current_page_id),
			'date_submitted'         => current_time( 'mysql' ),
			'read_status'            => false,
			'read_date'              => null,
			'contact_form_id'        => sanitize_text_field( $contact_form_info['contact_form_id'] ),
		);

		// Save the post meta data
		update_post_meta($post_id, 'additional_details', $additional_details);

		// Save the 'contact_form_unique_id'
		update_post_meta( $post_id, 'lwp_cfdb_contact_form_unique_id', $contact_form_info['contact_form_unique_id'] );

		// Save the page ID
		update_post_meta( $post_id, 'lwp_cfdb_page_id', $current_page_id );

		// Save the read status
		update_post_meta( $post_id, 'lwp_cfdb_read_status', false );

		// Save the read date
		update_post_meta( $post_id, 'lwp_cfdb_read_date', null );

	}

	//===========================================================================================

}
