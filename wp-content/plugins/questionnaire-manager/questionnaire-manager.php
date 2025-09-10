<?php
/**
 * Plugin Name: Questionnaire Manager
 * Description: Admin UI to add/edit questions with answer types, points, and categories.
 * Version: 1.0.0
 * Author: Your Name
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'QUESTIONNAIRE_PLUGIN_DIR', plugin_dir_path(__FILE__) );
define( 'QUESTIONNAIRE_PLUGIN_URL', plugin_dir_url(__FILE__) );
// require_once QUESTIONNAIRE_PLUGIN_DIR . 'fpdf/fpdf.php';
// include autoloader
require_once QUESTIONNAIRE_PLUGIN_DIR .'dompdf/autoload.inc.php';



require_once QUESTIONNAIRE_PLUGIN_DIR . 'includes/class-questionnaire-core.php';

if ( is_admin() ) {
    require_once QUESTIONNAIRE_PLUGIN_DIR . 'includes/class-questionnaire-admin.php';
    new Questionnaire_Admin();
} else {
    require_once QUESTIONNAIRE_PLUGIN_DIR . 'includes/class-questionnaire-front.php';
    new Questionnaire_Front();
}


/**
 * Create DB table for storing form submissions
 */
function questionnaire_create_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . "questionnaire_entries";
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        submission_id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        form_id BIGINT(20) UNSIGNED NOT NULL,
        form_data LONGTEXT NOT NULL,
        pdf_files VARCHAR(255) DEFAULT NULL,
        submitted_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY  (submission_id)
    ) $charset_collate;";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );
}


// ✅ Use __FILE__ (plugin file path), not URL
register_activation_hook( __FILE__, 'questionnaire_create_table' );
