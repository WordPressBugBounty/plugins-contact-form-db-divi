<?php

/**
 * Represents a custom post type for storing form submissions.
 */
class Lwp_Cfdb_Form_Submission_CPT {
    //===========================================================================================
    /**
     * Constructor function that registers the custom post type and filters the post row actions.
     */
    public function __construct() {
        global $is_free_version;
        // Register the custom post type on WordPress 'init' hook.
        add_action( 'init', array($this, 'register_post_type') );
        // Filter the post row actions to disable quick edit for form submissions.
        add_filter(
            'post_row_actions',
            array($this, 'disable_quick_edit_form_submission'),
            10,
            1
        );
        // Filter the post columns to add and remove columns on the post type page
        add_filter( 'manage_lwp_form_submission_posts_columns', array($this, 'updated_columns') );
        // Show the relevant data in the custom columns for the post type
        add_action(
            'manage_lwp_form_submission_posts_custom_column',
            array($this, 'updated_columns_data'),
            10,
            2
        );
        //
        if ( $is_free_version ) {
            add_action( 'admin_notices', array($this, 'free_version_notice') );
        }
    }

    //===========================================================================================
    /**
     * Registers the custom post type for form submissions.
     */
    public function register_post_type() {
        global $is_free_version;
        // Query form submissions where read status is false
        $args = array(
            'post_type'      => 'lwp_form_submission',
            'posts_per_page' => -1,
            'meta_query'     => array(array(
                'key'     => 'lwp_cfdb_read_status',
                'value'   => false,
                'compare' => '=',
            )),
        );
        $query = new WP_Query($args);
        // Get the count of unread form submissions
        $count = $query->found_posts;
        $labels = array(
            'name'               => _x( 'Divi Form DB', 'post type general name', 'contact-form-db-divi' ),
            'singular_name'      => _x( 'Divi Form Submission', 'post type singular name', 'contact-form-db-divi' ),
            'add_new'            => __( 'Add New', 'contact-form-db-divi' ),
            'add_new_item'       => __( 'Add New Divi Form Submission', 'contact-form-db-divi' ),
            'edit_item'          => __( 'View Divi Form Submission', 'contact-form-db-divi' ),
            'new_item'           => __( 'New Divi Form Submission', 'contact-form-db-divi' ),
            'view_item'          => __( 'View Divi Form Submission', 'contact-form-db-divi' ),
            'search_items'       => __( 'Search Divi Form Submissions', 'contact-form-db-divi' ),
            'all_items'          => ( $count && !$is_free_version ? sprintf( __( 'Divi Form DB <span class="menu-counter">%d</span>', 'contact-form-db-divi' ), $count ) : __( 'Divi Form DB', 'contact-form-db-divi' ) ),
            'not_found'          => __( 'No Divi form submissions found', 'contact-form-db-divi' ),
            'not_found_in_trash' => __( 'No Divi form submissions found in Trash', 'contact-form-db-divi' ),
            'parent_item_colon'  => '',
        );
        $args = array(
            'labels'              => $labels,
            'menu_icon'           => 'dashicons-email',
            'public'              => false,
            'exclude_from_search' => true,
            'publicly_queryable'  => false,
            'show_ui'             => true,
            'show_in_nav_menus'   => false,
            'show_in_menu'        => true,
            'query_var'           => true,
            'rewrite'             => false,
            'capability_type'     => 'post',
            'capabilities'        => array(
                'create_posts' => 'do_not_allow',
            ),
            'map_meta_cap'        => true,
            'has_archive'         => false,
            'hierarchical'        => false,
            'menu_position'       => 20,
            'supports'            => array(null),
        );
        register_post_type( 'lwp_form_submission', $args );
    }

    //===========================================================================================
    /**
     * Disables the quick edit action for form submission posts.
     *
     * @param array $actions An array of post row actions.
     * @return array The updated array of post row actions.
     */
    function disable_quick_edit_form_submission( $actions ) {
        global $post;
        if ( $post->post_type == 'lwp_form_submission' ) {
            unset($actions['inline hide-if-no-js']);
        }
        return $actions;
    }

    //===========================================================================================
    /**
     * Add new columns to the form submission CPT
     */
    function updated_columns( $columns ) {
        // Remove the default 'date' column
        unset($columns['date']);
        //Add custom columns
        $columns['read_status'] = __( 'Read', 'contact-form-db-divi' );
        $columns['email'] = __( 'Email', 'contact-form-db-divi' );
        $columns['date_submitted'] = __( 'Date Submitted', 'contact-form-db-divi' );
        //
        return $columns;
    }

    //===========================================================================================
    /**
     * Show relevant data in the updated columns on the form submission CPT
     */
    function updated_columns_data( $column, $post_id ) {
        //
        $additional_details = get_post_meta( $post_id, 'additional_details', true );
        $submission_details = get_post_meta( $post_id, 'processed_fields_values', true );
        $read_status = get_post_meta( $post_id, 'lwp_cfdb_read_status', true );
        $read_date = get_post_meta( $post_id, 'lwp_cfdb_read_date', true );
        switch ( $column ) {
            case 'page_name':
                break;
            case 'email':
                if ( isset( $submission_details['email'] ) && isset( $submission_details['email']['value'] ) ) {
                    echo esc_html( $submission_details['email']['value'] );
                }
                break;
            case 'date_submitted':
                if ( isset( $additional_details['date_submitted'] ) ) {
                    echo esc_html( $additional_details['date_submitted'] );
                }
                break;
            case 'read_status':
                if ( isset( $read_status ) && $read_status == false ) {
                    echo '<span class="dashicons dashicons-email-alt"></span>';
                } else {
                    echo '<span>' . esc_html( ( isset( $read_date ) ? $read_date : '' ) ) . '</span>';
                }
                break;
            default:
                // Break out of switch statement for unknown column names
                break;
        }
    }

    //===========================================================================================
    /**
     * Adds a notice to the free version of plugin on the single post page
     */
    function free_version_notice() {
        global $pagenow, $typenow;
        if ( $pagenow == 'post.php' && $typenow == 'lwp_form_submission' ) {
            echo '<div class="notice notice-info">
					<p>Free version: Only form fields with Field ID "name", "email", and "message" are saved upon form submission. Upgrade for full features. <a href="' . esc_url( lwp_cfdd_fs()->get_upgrade_url() ) . '">Upgrade Now!</a></p>
				</div>';
        }
    }

    //===========================================================================================
}
