<?php

/*
Plugin Name: Contact Form DB Divi
Plugin URI: https://www.learnhowwp.com/divi-contact-form-db/
Description: The plugin saves all form submission made to Divi forms in the WordPress backend.
Version: 1.2.4
Author: Learnhowwp.com
Author URI: https://learnhowwp.com
License: GPL2
Text Domain: contact-form-db-divi
*/
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
if ( function_exists( 'lwp_cfdd_fs' ) ) {
    lwp_cfdd_fs()->set_basename( false, __FILE__ );
} else {
    // A constant to store the current version of the plugin
    define( 'LWP_CFDB_VERSION', '1.2.4' );
    // A global variable to check if the version of the plugin is the free version
    global $is_free_version;
    //======================================================================================
    //======================================================================================
    if ( !function_exists( 'lwp_cfdd_fs' ) ) {
        // Create a helper function for easy SDK access.
        function lwp_cfdd_fs() {
            global $lwp_cfdd_fs;
            if ( !isset( $lwp_cfdd_fs ) ) {
                // Include Freemius SDK.
                require_once dirname( __FILE__ ) . '/freemius/start.php';
                $lwp_cfdd_fs = fs_dynamic_init( array(
                    'id'             => '12368',
                    'slug'           => 'contact-form-db-divi',
                    'type'           => 'plugin',
                    'public_key'     => 'pk_8adb28c6b3dfc2364477c03a441d8',
                    'is_premium'     => false,
                    'premium_suffix' => 'Pro',
                    'has_addons'     => false,
                    'has_paid_plans' => true,
                    'anonymous_mode' => true,
                    'trial'          => array(
                        'days'               => 7,
                        'is_require_payment' => false,
                    ),
                    'menu'           => array(
                        'slug' => 'edit.php?post_type=lwp_form_submission',
                    ),
                    'is_live'        => true,
                ) );
            }
            return $lwp_cfdd_fs;
        }

        // Init Freemius.
        lwp_cfdd_fs();
        // Signal that SDK was initiated.
        do_action( 'lwp_cfdd_fs_loaded' );
    }
    //======================================================================================
    //======================================================================================
    // Initialize the global variable to check the version of plugin being used
    $is_free_version = !lwp_cfdd_fs()->is__premium_only();
    //======================================================================================
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-lwp-cfdb-form-submission-cpt.php';
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-lwp-cfdb-form-submission-meta-boxes.php';
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-lwp-cfdb-form-submission-creator.php';
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-lwp-cfdb-modify-module.php';
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-lwp-cfdb-upgrades.php';
    //======================================================================================
    new Lwp_Cfdb_Form_Submission_CPT();
    new Lwp_Cfdb_Form_Submission_Meta_Boxes();
    new Lwp_Cfdb_Form_Submission_Creator();
    new Lwp_Cfdb_Modify_Module();
    //======================================================================================
    //
    add_action( 'admin_init', 'lwp_cfdb_check_upgrade_callback' );
    /**
     * Callback function to check and perform upgrades
     *
     * It checks if the 'lwp_cfdb_plugin_version' option is not set, indicating that an upgrade is required.
     * If an upgrade is required, it performs the necessary upgrade actions.
     * After the upgrade, it stores the current version in the 'lwp_cfdb_plugin_version' option.
     *
     * @since 1.1
     */
    function lwp_cfdb_check_upgrade_callback() {
        //
        $stored_version = get_option( 'lwp_cfdb_plugin_version', '1.0' );
        $current_version = LWP_CFDB_VERSION;
        if ( version_compare( $stored_version, $current_version, '<' ) ) {
            if ( version_compare( $stored_version, '1.1', '<' ) ) {
                Lwp_Cfdb_Upgrades::upgrade_to_1_1();
            }
            if ( version_compare( $stored_version, '1.2', '<' ) ) {
                Lwp_Cfdb_Upgrades::upgrade_to_1_2();
            }
            // Update the stored version
            update_option( 'lwp_cfdb_plugin_version', $current_version );
        }
    }

    //======================================================================================
    /**
     * Activation hook callback function which stores the current version to the database.
     *
     * @since 1.1
     */
    function lwp_cfdb_activation_hook() {
        // Perform upgrade tasks on plugin activation
        lwp_cfdb_check_upgrade_callback();
        // Save the current version in the database
        $current_version = LWP_CFDB_VERSION;
        update_option( 'lwp_cfdb_plugin_version', $current_version );
    }

    // Register the activation hook
    register_activation_hook( __FILE__, 'lwp_cfdb_activation_hook' );
    //======================================================================================
}