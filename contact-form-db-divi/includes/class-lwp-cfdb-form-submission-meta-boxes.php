<?php

/**
 * Class to manage meta boxes on the lwp_form_submission post type
 */
class Lwp_Cfdb_Form_Submission_Meta_Boxes {
    // ===========================================================================================
    /**
     * Registers the metaboxes for form submission CPT based on the version being used (free/premium).
     *
     * @global bool $lwp_cfdb_is_free_version Whether the current version of the plugin is free or premium.
     */
    public function __construct() {
        global $lwp_cfdb_is_free_version;
        if ( $lwp_cfdb_is_free_version ) {
            add_action( 'add_meta_boxes', array($this, 'add_form_submission_meta_boxes__free') );
        }
    }

    // ===========================================================================================
    /**
     * Registers the meta boxes for free version.
     */
    public function add_form_submission_meta_boxes__free() {
        // Form Submission Details Meta Box.
        add_meta_box(
            'form-submission-details',
            __( 'Form Submission Details', 'contact-form-db-divi' ),
            array($this, 'render_form_submission_meta_box__free'),
            'lwp_form_submission',
            'normal',
            'high'
        );
        // Navigation Meta Box.
        add_meta_box(
            'form-submission-navigation',
            __( 'Navigation', 'contact-form-db-divi' ),
            array($this, 'render_form_submission_navigation_meta_box'),
            'lwp_form_submission',
            'side',
            'high'
        );
    }

    // ===========================================================================================
    /**
     * Callback function to render the Submission Details Meta Box for free version.
     *
     * @param WP_Post $post The current post being edited.
     */
    function render_form_submission_meta_box__free( $post ) {
        $submission_details = get_post_meta( $post->ID, 'processed_fields_values', true );
        $read_status = get_post_meta( $post->ID, 'lwp_cfdb_read_status', true );
        if ( false == $read_status ) {
            update_post_meta( $post->ID, 'lwp_cfdb_read_status', true );
            update_post_meta( $post->ID, 'lwp_cfdb_read_date', current_time( 'mysql' ) );
        }
        ?>

		<table class="wp-list-table widefat fixed striped" style="margin-bottom:10px;">
			<thead>
				<tr>
					<th scope="col"><?php 
        esc_attr_e( 'Field Name', 'contact-form-db-divi' );
        ?></th>
					<th scope="col"><?php 
        esc_attr_e( 'Value', 'contact-form-db-divi' );
        ?></th>
				</tr>
			</thead>
			<tbody>
				<?php 
        if ( isset( $submission_details['name'] ) ) {
            ?>
					<tr>
						<td><strong><?php 
            echo esc_html( $submission_details['name']['label'] );
            ?>:</strong></td>
						<td><?php 
            echo esc_html( $submission_details['name']['value'] );
            ?></td>
					</tr>
				<?php 
        }
        ?>
				<?php 
        if ( isset( $submission_details['email'] ) ) {
            ?>
					<tr>
						<td><strong><?php 
            echo esc_html( $submission_details['email']['label'] );
            ?>:</strong></td>
						<td><?php 
            echo esc_html( $submission_details['email']['value'] );
            ?></td>
					</tr>
				<?php 
        }
        ?>
				<?php 
        if ( isset( $submission_details['message'] ) ) {
            ?>
					<tr>
						<td><strong><?php 
            echo esc_html( $submission_details['message']['label'] );
            ?>:</strong></td>
						<td><?php 
            echo esc_html( $submission_details['message']['value'] );
            ?></td>
					</tr>
				<?php 
        }
        ?>
			</tbody>
		</table>

		<?php 
        if ( isset( $submission_details['email'] ) ) {
            ?>
			<a class="button button-primary" href="mailto:<?php 
            echo esc_attr( $submission_details['email']['value'] );
            ?>" type="button"><?php 
            echo esc_html__( 'Reply via Email', 'contact-form-db-divi' );
            ?></a>
		<?php 
        }
        ?>

		<?php 
    }

    // ===========================================================================================
    /**
     * Callback function to render the Navigation Meta Box.
     *
     * @param WP_Post $post The current post being edited.
     */
    function render_form_submission_navigation_meta_box( $post ) {
        $next_submission = get_adjacent_post( false, '', false );
        $previous_submission = get_adjacent_post( false, '', true );
        ?>

		<div class="submission-navigation">
			<?php 
        if ( $previous_submission ) {
            ?>
				<a href="<?php 
            echo esc_url( admin_url( 'post.php?post=' . $previous_submission->ID . '&action=edit' ) );
            ?>" class="button"><?php 
            esc_html_e( 'Previous', 'contact-form-db-divi' );
            ?></a>
			<?php 
        }
        ?>
			<?php 
        if ( $next_submission ) {
            ?>
				<a href="<?php 
            echo esc_url( admin_url( 'post.php?post=' . $next_submission->ID . '&action=edit' ) );
            ?>" class="button"><?php 
            esc_html_e( 'Next', 'contact-form-db-divi' );
            ?></a>
			<?php 
        }
        ?>
		</div>

		<?php 
    }

    // ===========================================================================================
}
