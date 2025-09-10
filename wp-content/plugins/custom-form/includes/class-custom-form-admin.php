<?php
if (!defined('ABSPATH')) {
    exit;
}

class Custom_Form_Admin {

    public function __construct() {
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_assets']);
        add_action('admin_menu', [$this, 'add_admin_menu']);
        add_action('init', [$this, 'register_custom_form_cpt']);
        add_action('add_meta_boxes', [$this, 'add_custom_meta_boxes_custom']);
        add_action('admin_post_save_custom_form', [$this, 'handle_form_save']);   
    }

    public function enqueue_admin_assets() {
        wp_enqueue_script('custom-form-admin-js', plugin_dir_url(__FILE__) . '../assets/js/custom-form-admin.js', ['jquery'], null, true);
        wp_enqueue_style('custom-form-admin-css', plugin_dir_url(__FILE__) . '../assets/css/custom-form-admin.css');
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

        add_submenu_page(
            'custom_form_builder',
            'Responses',
            'Responses',
            'manage_options',
            'custom_form_responses',
            [$this, 'render_all_forms_page']
        );
    }

    public function register_custom_form_cpt() {
        $form_labels = [
            'name' => 'Your Forms',
            'singular_name' => 'Your Form',
            'add_new_item' => 'Add New Form',
            'edit_item' => 'Edit Form',
            'new_item' => 'New Form',
            'all_items' => 'Custom Forms',
            'menu_name' => 'Forms'
        ];
    
        $form_args = [
            'labels' => $form_labels,
            'public' => false,
            'show_ui' => true,
            'show_in_menu' => 'custom_form_builder', 
            'supports' => ['title'],
            'capability_type' => 'post'
        ];
    
        register_post_type('custom_form', $form_args);
    }

    public function add_custom_meta_boxes_custom() {
        add_meta_box(
            'form_details',
            'Form Details',
            [$this, 'render_admin_page'],
            'custom_form',
            'normal',
            'high'
        );
    }

    public function render_admin_page() {
        ?>
        <div class="wrap">
            <h1>🛠️ Custom Form Builder</h1>
            <form method="post" action="<?php echo admin_url('admin-post.php'); ?>" class="custom-form">
                <input type="hidden" name="post_id" value="<?php echo get_the_ID(); ?>">

                <div class="form-fields-section">
                    <div class="form-group">
                        <label for="form_purpose"><strong>Form Purpose</strong></label>
                        <input type="text" name="form_purpose" id="form_purpose"
                            value="<?php echo esc_attr(get_post_field('post_content', get_the_ID())); ?>" 
                            required class="regular-text form-purpose-input">
                    </div>

                    <h3>Add Fields</h3>
                    <div id="form-fields-container">
                        <!-- You can pre-populate existing fields here using get_post_meta() -->
                    </div>
                    <button type="button" class="button" id="addField-btn">➕ Add Field</button>
                </div>
            </form>
        </div>
        <?php
    }

    public function handle_form_save() {
        if (!current_user_can('manage_options') || !isset($_POST['form_name'])) {
            wp_die('Unauthorized user');
        }

        $post_id      = intval($_POST['post_id']);
        $form_purpose = sanitize_textarea_field($_POST['form_purpose']);
        $fields       = $_POST['fields']; 
        $user_id = get_current_user_id();

        wp_update_post([
            'ID'           => $post_id,
            'post_content' => $form_purpose,
            'post_author'  => $user_id,
            'post_status'  => 'publish',
            'post_type'    => 'form',
            'post_date'   => current_time('mysql'),
        ]);

        // Sanitize and store fields in post meta
        foreach ($fields as &$field) {
            if (!empty($field['options'])) {
                $field['options'] = array_filter(array_map('sanitize_text_field', explode("\n", $field['options'])));
            } else {
                $field['options'] = [];
            }
        }

        update_post_meta($post_id, 'form_fields', wp_json_encode($fields));

        echo '<div style="text-align:center; margin-top:50px; font-size:1.5em;">✅ Your form has been updated successfully!</div>';
        echo '<script>
                setTimeout(function() {
                    window.location.href = "' . admin_url('post.php?post=' . $post_id . '&action=edit') . '";
                }, 2000);
            </script>';
        exit;
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

