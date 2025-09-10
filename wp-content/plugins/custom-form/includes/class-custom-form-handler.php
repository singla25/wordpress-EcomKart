<?php
if (!defined('ABSPATH')) exit;

class Custom_Form_Handler {
    public function __construct() {
        add_action('init', [$this, 'handle_submission']);
        add_shortcode('custom_form', [$this, 'render_custom_form_shortcode']);
    }

    public function handle_submission() {
        if (!isset($_POST['custom_form_submit'])) return;

        global $wpdb;

        $form_id = intval($_POST['custom_form_id']);
        $form = get_post($form_id);

        if (!$form || $form->post_type !== 'form') return;

        $fields_json = get_post_meta($form_id, 'form_fields', true);
        $fields = json_decode($fields_json, true);

        $user_id = get_current_user_id();

        // Insert into main responses table
        $wpdb->insert(
            $wpdb->prefix . 'custom_form_responses',
            [
                'user_id'          => $user_id,
                'form_id'          => $form_id,
                'form_name'        => sanitize_text_field($form->post_title),
                'form_description' => sanitize_textarea_field($form->post_content),
                'created_at'       => current_time('mysql')
            ]
        );

        $submission_id = $wpdb->insert_id;

        // Insert each field response into meta table
        foreach ($fields as $index => $field) {
            $field_name = "field_{$index}";
            $value = '';

            if (isset($_POST[$field_name])) {
                if (is_array($_POST[$field_name])) {
                    $value = implode(', ', array_map('sanitize_text_field', $_POST[$field_name]));
                } else {
                    $value = sanitize_text_field($_POST[$field_name]);
                }
            }

            $wpdb->insert(
                $wpdb->prefix . 'custom_form_responses_meta',
                [
                    'submission_id' => $submission_id,
                    'meta_key'      => sanitize_text_field($field['label']),
                    'meta_value'    => $value
                ]
            );
        }

        wp_redirect(add_query_arg('form_submitted', 'true', get_permalink()));
        exit;
    }

    public function render_custom_form_shortcode($atts) {
        if (!isset($atts['id'])) {
            return 'Form ID not specified.';
        }

        $form_id = intval($atts['id']);
        $form = get_post($form_id);

        if (!$form || $form->post_type !== 'form') {
            return 'Form not found.';
        }

        $fields_json = get_post_meta($form_id, 'form_fields', true);
        $fields = json_decode($fields_json, true);

        // Make $form and $fields available to template
        ob_start();
        include plugin_dir_path(__FILE__) . 'templates/frontend-form.php';
        return ob_get_clean();
    }
}

