<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class Contact_Handler {

    public function __construct() {
        add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);
        add_action('wp_ajax_submit_contact_form', [$this, 'handle_form_submission']);
        add_action('wp_ajax_nopriv_submit_contact_form', [$this, 'handle_form_submission']);
        add_shortcode('contact_form_template', [$this, 'render_contact_form_shortcode']);
    }

    public function enqueue_scripts() {
        wp_enqueue_style('contact-form-css', plugin_dir_url(__FILE__) . '../assets/css/contact-us.css');
        wp_enqueue_script('contact-form-js', plugin_dir_url(__FILE__) . '../assets/js/contact-us.js', ['jquery'], null, true);

        wp_localize_script('contact-form-js', 'contact_ajax', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('contact_nonce'),
            'home_url' => home_url(),
        ]);
    }

    public function render_contact_form_shortcode() {
        ob_start(); ?>
        <div class="contact-us">
            <div class="form">
                <h2 class="heading">Contact Us</h2>
                <form id="contact-form">
                    <div class="row row-1">
                        <div class="input-group">
                            <label for="name">Name</label>
                            <input type="text" name="name" id="name" class="input" required>
                        </div>
                        <div class="input-group">
                            <label for="email">Email</label>
                            <input type="email" name="email" id="email" class="input" required>
                        </div>
                        <div class="input-group">
                            <label for="phone">Phone</label>
                            <input type="tel" name="phone" id="phone" pattern="[0-9]{10}" maxlength="10" class="input" required>
                        </div>
                    </div>

                    <div class="row row-2">
                        <div class="input-group">
                            <label for="subject">Subject</label>
                            <input type="text" name="subject" id="subject" class="input" required>
                        </div>
                        <div class="input-group">
                            <label for="topic">Topic</label>
                            <input type="text" name="topic" id="topic" class="input" required>
                        </div>
                    </div>

                    <div class="row row-3">
                        <div class="input-group full-width">
                            <label for="query">Your Query</label>
                            <textarea name="query" id="query" rows="4" class="input" required></textarea>
                        </div>
                    </div>

                    <div class="row row-4">
                        <button type="submit" class="submit-btn">Send Message</button>
                    </div>
                </form>
            </div>
        </div>

        <?php
        return ob_get_clean();
    }

    public function handle_form_submission() {
        check_ajax_referer('contact_nonce', 'nonce');

        global $wpdb;
        $table_main = $wpdb->prefix . 'contact_submissions';
        $table_meta = $wpdb->prefix . 'contact_submission_meta';

        $user_id = is_user_logged_in() ? get_current_user_id() : 0;
        $name    = sanitize_text_field($_POST['name']);
        $email   = sanitize_email($_POST['email']);
        $subject = sanitize_text_field($_POST['subject']);
        $phone   = sanitize_text_field($_POST['phone']);
        $topic   = sanitize_text_field($_POST['topic']);
        $query   = sanitize_textarea_field($_POST['query']);

        // Insert into main table
        $wpdb->insert($table_main, [
            'user_id'    => $user_id,
            'name'       => $name,
            'email'      => $email,
            'subject'    => $subject,
            'created_at' => current_time('mysql'),
        ]);

        $submission_id = $wpdb->insert_id;

        if ($submission_id) {
            // Insert meta fields
            $meta_fields = [
                'phone' => $phone,
                'topic' => $topic,
                'query' => $query,
                'user_agent' => $_SERVER['HTTP_USER_AGENT']
            ];

            foreach ($meta_fields as $key => $value) {
                $wpdb->insert($table_meta, [
                    'submission_id' => $submission_id,
                    'meta_key'      => $key,
                    'meta_value'    => $value,
                ]);
            }

            wp_send_json_success(['message' => 'Your form has been submitted. Our Team will connect you!']);
        } else {
            wp_send_json_error(['message' => 'Failed to save contact form data.']);
        }
    }
}
