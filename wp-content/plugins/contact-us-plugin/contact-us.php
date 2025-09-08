<?php
/**
 * Plugin Name: Contact Submissions Plugin
 * Description: Handles contact form submissions as a custom database table with admin management.
 * Version: 1.0
 * Author: Your Name
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Include core handler and admin files
require_once plugin_dir_path(__FILE__) . 'includes/class-contact-handler.php';
require_once plugin_dir_path(__FILE__) . 'admin/class-contact-admin.php';

// Activation Hook: Create custom tables
register_activation_hook(__FILE__, 'contact_plugin_create_tables');
function contact_plugin_create_tables() {
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();

    $table_main = $wpdb->prefix . 'contact_submissions';
    $table_meta = $wpdb->prefix . 'contact_submission_meta';

    $sql = "
    CREATE TABLE $table_main (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        user_id mediumint(9) NOT NULL,
        name varchar(255) NOT NULL,
        email varchar(255) NOT NULL,
        subject text NOT NULL,
        created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
        PRIMARY KEY (id)
    ) $charset_collate;

    CREATE TABLE $table_meta (
        meta_id mediumint(9) NOT NULL AUTO_INCREMENT,
        submission_id mediumint(9) NOT NULL,
        meta_key varchar(255) NOT NULL,
        meta_value text,
        PRIMARY KEY (meta_id),
        KEY submission_id (submission_id)
    ) $charset_collate;
    ";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql);
}

// Initialize plugin
new Contact_Handler();
new Contact_Admin();
