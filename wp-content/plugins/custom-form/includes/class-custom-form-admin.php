<?php
if (!defined('ABSPATH')) {
    exit;
}

class Custom_Form_Admin {

    public function __construct() {
        add_action('admin_menu', [$this, 'add_admin_menu']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_assets']);
        add_action('admin_post_save_custom_form', [$this, 'handle_form_save']);
    }

    public function add_admin_menu() {
        add_menu_page(
            'Custom Forms',
            'Custom Forms',
            'manage_options',
            'custom_form_builder',
            [$this, 'render_admin_page'],
            'dashicons-forms',
            6
        );

        // Submenu for "All Forms"
        add_submenu_page(
            'custom_form_builder',
            'All Forms',
            'All Forms',
            'manage_options',
            'all_custom_forms',
            [$this, 'render_all_forms_page']
        );
    }

    public function enqueue_admin_assets() {
        wp_enqueue_script('custom-form-admin-js', plugin_dir_url(__FILE__) . '../assets/js/custom-form-admin.js', ['jquery'], null, true);
        wp_enqueue_style('custom-form-admin-css', plugin_dir_url(__FILE__) . '../assets/css/custom-form-admin.css');
    }

    public function render_admin_page() {
        ?>
        <div class="wrap">
            <h1>🛠️ Custom Form Builder</h1>
            <form method="post" action="<?php echo admin_url('admin-post.php'); ?>" class="custom-form">
                <div class="form-fields-section">
                    <h2>Make a Form</h2>
                    <table class="form-table">
                        <tr>
                            <th><label for="form_name">Form Name</label></th>
                            <td><input type="text" name="form_name" id="form_name" required class="regular-text"></td>
                        </tr>
                        <tr>
                            <th><label for="form_purpose">Form Purpose</label></th>
                            <td><input type="text" name="form_purpose" id="form_purpose" required class="regular-text"></td>
                        </tr>
                    </table>

                    <h3>Add Fields</h3>
                    <div id="form-fields-container"></div>
                    <button type="button" class="button" id="addField-btn">➕ Add Field</button>
                </div>

                <div class="form-publish-section">
                    <h2>Publish Form</h2>
                    <input type="hidden" name="action" value="save_custom_form">
                    <button type="submit" class="button-primary">✅ Publish Form</button>
                </div>
            </form>
        </div>
        <?php
    }

    public function handle_form_save() {
        if (!current_user_can('manage_options') || !isset($_POST['form_name'])) {
            wp_die('Unauthorized user');
        }

        $form_name    = sanitize_text_field($_POST['form_name']);
        $form_purpose = sanitize_textarea_field($_POST['form_purpose']);
        $fields       = $_POST['fields']; 

        $user_id = get_current_user_id();

        $post_id = wp_insert_post([
            'post_author'  => $user_id,
            'post_title'   => $form_name,
            'post_content' => $form_purpose,
            'post_status'  => 'publish',
            'post_type'    => 'form',
            'post_date'   => current_time('mysql'),
        ]);

        if ($post_id) {
            foreach ($fields as &$field) {
                if (!empty($field['options'])) {
                    $field['options'] = array_filter(array_map('sanitize_text_field', explode("\n", $field['options'])));
                } else {
                    $field['options'] = [];
                }
            }

            update_post_meta($post_id, 'form_fields', wp_json_encode($fields));

            echo '<div style="text-align:center; margin-top:50px; font-size:1.5em;">✅ Your form has been submitted successfully!</div>';
            echo '<script>
                    setTimeout(function() {
                        window.location.href = "' . admin_url('admin.php?page=custom_form_builder&message=success') . '";
                    }, 2000);
                </script>';
            exit;
        }

        wp_die('Failed to save form');
    }


    public function render_all_forms_page() {
        // Fetch all forms
        $forms = get_posts([
            'post_type'      => 'form',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
        ]);

        // Pass $forms to template
        include plugin_dir_path(__FILE__) . '../templates/admin/render-all-forms.php';
    }
}

