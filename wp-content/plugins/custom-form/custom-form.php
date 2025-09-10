<?php
/**
 * Plugin Name: Custom Form Plugin
 * Description: Make your custom forms by using this
 * Version: 1.0
 * Author: Sahil Singla
 */

if (!defined('ABSPATH')) {
    exit;
}

// Include core handler and admin files
require_once plugin_dir_path(__FILE__) . 'includes/class-custom-form-handler.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-custom-form-admin.php';

// Activation Hook: Create custom tables for storing responses
register_activation_hook(__FILE__, 'custom_form_create_tables');
function custom_form_create_tables() {
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();

    $table_main = $wpdb->prefix . 'custom_form_responses';
    $table_meta = $wpdb->prefix . 'custom_form_responses_meta';

    // Main table
    $sql = "
    CREATE TABLE $table_main (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        user_id mediumint(9) NOT NULL,
        form_id mediumint(9) NOT NULL,
        form_name text NOT NULL,
        form_description text NOT NULL,
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
new Custom_Form_Admin();
new Custom_Form_Handler();

