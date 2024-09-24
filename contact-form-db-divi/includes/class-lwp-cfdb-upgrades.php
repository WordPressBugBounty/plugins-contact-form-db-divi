<?php

/**
 * A class to handle upgrades for the plugin
 */
class Lwp_Cfdb_Upgrades {

	//===========================================================================================

	/**
	 * Upgrade function for version 1.1
	 *
	 * Fetches all form submission post IDs and creates a new meta field for each post.
	 * The new meta field is based on the 'contact_form_unique_id' value retrieved from the existing 'additional_details' meta value.
	 *
	 * @since 1.1
	 */
    public static function upgrade_to_1_1() {

		// Fetch all form submissions
		$args = array(
			'post_type'      => 'lwp_form_submission',
			'posts_per_page' => -1,
			'fields'         => 'ids',
		);

		//
		$post_ids = get_posts($args);

		// Create a new meta field for each post
		foreach ($post_ids as $post_id) {
			// Get the existing $additional_details meta value
			$additional_details = get_post_meta($post_id, 'additional_details', true);

			//
			if (!empty($additional_details)) {
				// Get the 'contact_form_unique_id' value from the existing $additional_details
				$contact_form_unique_value = $additional_details['contact_form_unique_id'];

				// Create a new meta field for 'contact_form_unique_id'
				update_post_meta($post_id, 'lwp_cfdb_contact_form_unique_id', $contact_form_unique_value);
			}
		}

    }

	//===========================================================================================

	/**
	 * Upgrade function for version 1.2
	 *
	 * Fetches all form submission post IDs and creates new meta fields for each post.
	 * The new meta field are based on the 'page_id', 'read_status' and 'read_date' value retrieved from the existing 'additional_details' meta values.
	 *
	 * @since 1.2
	 */
    public static function upgrade_to_1_2() {

		// Fetch all form submissions which need to be updated
		$args = array(
			'post_type'      => 'lwp_form_submission',
			'posts_per_page' => -1,
			'fields'         => 'ids',
			'meta_query'     => array(
				'relation' => 'OR',
				array(
					'key'     => 'lwp_cfdb_page_id',
					'compare' => 'NOT EXISTS',
				),
				array(
					'key'     => 'lwp_cfdb_read_status',
					'compare' => 'NOT EXISTS',
				),
				array(
					'key'     => 'lwp_cfdb_read_date',
					'compare' => 'NOT EXISTS',
				),
			),
		);

		//
		$post_ids = get_posts($args);

		// Create a new meta field for each post
		foreach ($post_ids as $post_id) {
			// Get the existing $additional_details meta value
			$additional_details = get_post_meta($post_id, 'additional_details', true);

			//
			if (!empty($additional_details)) {
				// Get the existing fields
				$current_page_id = $additional_details['page_id'];
				$read_status     = $additional_details['read_status'];
				$read_date       = $additional_details['read_date'];

				// Add them as top level meta fields for the post
				update_post_meta( $post_id, 'lwp_cfdb_page_id',     $current_page_id );
				update_post_meta( $post_id, 'lwp_cfdb_read_status', $read_status     );
				update_post_meta( $post_id, 'lwp_cfdb_read_date',   $read_date       );
			}
		}

    }

	//===========================================================================================
}
