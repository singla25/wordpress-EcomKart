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
        add_action('save_post_custom_form', [$this, 'handle_form_save']);   
        add_filter('manage_custom_form_posts_columns', [$this, 'add_shortcode_column']);
        add_action('manage_custom_form_posts_custom_column', [$this, 'render_shortcode_column'], 10, 2);
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
            [$this, 'render_all_form_responses']
        );

        add_submenu_page(
            'custom_form_builder',
            'Settings',
            'Setting',
            'manage_options',
            'custom_form_setting',
            [$this, 'render_setting_page']
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

    public function render_admin_page($post) {
        $form_fields = get_post_meta($post->ID, 'form_fields', true) ?: [];
        $form_fields = is_array($form_fields) ? $form_fields : json_decode($form_fields, true);
        $fieldCount = count($form_fields);
        ?>
        <div class="wrap">
            <h1>🛠️ Custom Form Builder</h1>
            
            <?php wp_nonce_field('save_form_meta', 'form_meta_nonce'); ?>
            <input type="hidden" name="post_id" value="<?php echo esc_attr(get_the_ID()); ?>">

            <div class="form-fields-section">
                <div class="form-group">
                    <label for="form_purpose"><strong>Form Purpose</strong></label>
                    <input type="text" name="form_purpose" id="form_purpose"
                        value="<?php echo esc_attr(get_post_meta(get_the_ID(), 'form_purpose', true)); ?>" 
                        required class="regular-text form-purpose-input">
                </div>

                <h3>Add Fields</h3>
                
                <div id="form-fields-container">
                    <?php foreach ($form_fields as $index => $field): 
                        $options = isset($field['options']) ? implode("\n", (array) $field['options']) : '';
                    ?>
                        <div class="form-field-wrapper">
                            <div class="form-field-row" style="display: flex; flex-wrap: wrap; gap: 10px; align-items: center;">
                                <input type="text" name="fields[<?php echo $index; ?>][label]" 
                                    value="<?php echo esc_attr($field['label'] ?? ''); ?>" 
                                    placeholder="Field Label" required>

                                <select name="fields[<?php echo $index; ?>][type]" class="field-type-select" required>
                                    <option value="" <?php selected($field['type'] ?? '', ''); ?>>Choose type of Your field</option>
                                    <option value="text" <?php selected($field['type'], 'text'); ?>>Text</option>
                                    <option value="email" <?php selected($field['type'], 'email'); ?>>Email</option>
                                    <option value="phone" <?php selected($field['type'], 'phone'); ?>>Phone</option>
                                    <option value="number" <?php selected($field['type'], 'number'); ?>>Number</option>
                                    <option value="textarea" <?php selected($field['type'], 'textarea'); ?>>Textarea</option>
                                    <option value="select" <?php selected($field['type'], 'select'); ?>>Select</option>
                                    <option value="checkbox" <?php selected($field['type'], 'checkbox'); ?>>Checkbox</option>
                                    <option value="radio" <?php selected($field['type'], 'radio'); ?>>Radio</option>
                                    <option value="button" <?php selected($field['type'], 'button'); ?>>Button</option>
                                </select>

                                <select name="fields[<?php echo $index; ?>][width]" required>
                                    <option value="" <?php selected($field['width'] ?? '', ''); ?>>Choose width</option>
                                    <option value="50%" <?php selected($field['width'], '50%'); ?>>Half (50%)</option>
                                    <option value="100%" <?php selected($field['width'], '100%'); ?>>Full (100%)</option>
                                </select>

                                <label>
                                    <input type="hidden" name="fields[<?php echo $index; ?>][required]" value="0">
                                    <input type="checkbox" name="fields[<?php echo $index; ?>][required]" value="1" <?php checked($field['required'], 1); ?>> Required
                                </label>

                                <button type="button" class="remove-field-btn">❌ Remove</button>
                            </div>

                            <div class="form-field-options-row" style="margin-top: 10px; width: 30%;">
                                <textarea name="fields[<?php echo $index; ?>][options]" class="field-options-textarea" 
                                    placeholder="Enter one option per line" 
                                    style="<?php echo in_array($field['type'], ['select', 'checkbox', 'radio']) ? '' : 'display:none;'; ?> width: 100%; min-height: 80px;"><?php echo esc_textarea($options); ?></textarea>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <button type="button" class="button" id="addField-btn">➕ Add Field</button>
            </div>
        </div>

        <script>
            let fieldCount = <?php echo $fieldCount; ?>;
        </script>
        <?php
    }

    public function handle_form_save($post_id) {
        // Prevent autosave and unauthorized edits
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
        if (!current_user_can('edit_post', $post_id)) return;
        if (!isset($_POST['form_meta_nonce']) || !wp_verify_nonce($_POST['form_meta_nonce'], 'save_form_meta')) return;

        $form_purpose = sanitize_text_field($_POST['form_purpose'] ?? '');
        $fields       = isset($_POST['fields']) ? (array) $_POST['fields'] : [];

        if (!empty($fields)) {
            foreach ($fields as $index => &$f) {
                // Sanitize basic fields
                $f['label']    = sanitize_text_field($f['label'] ?? '');
                $f['type']     = sanitize_text_field($f['type'] ?? '');
                $f['width']    = sanitize_text_field($f['width'] ?? '');
                $f['required'] = !empty($f['required']) ? 1 : 0;

                // Clean and process options
                if (!empty($f['options'])) {
                    $lines = explode("\n", $f['options']);
                    $options_array = array_filter(array_map('trim', $lines));  // Remove empty lines

                    // Re-save as space-separated string for consistency
                    $f['options'] = implode(' ', $options_array);
                } else {
                    $f['options'] = '';
                }
            }
            unset($f);  // Prevent reference issues
        }
        
        // Save clean data into post meta
        update_post_meta($post_id, 'form_purpose', $form_purpose);
        update_post_meta($post_id, 'form_fields', $fields);
    }

    public function add_shortcode_column($columns) {
        $new = [];
        foreach ($columns as $key => $value) {
            $new[$key] = $value;
            if ($key === 'title') {
                $new['shortcode'] = __('Shortcode');
            }
        }
        return $new;
    }

    public function render_shortcode_column($column, $post_id) {
        if ($column === 'shortcode') {
            echo '<code>[custom_form id="' . $post_id . '"]</code>';
        }
    }

    public function render_all_form_responses() {
        global $wpdb;

        // Fetch all forms
        $forms = get_posts([
            'post_type'      => 'custom_form',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
        ]);

        // If user requested to view responses
        if (isset($_POST['form_id'])) {
            $form_id = intval($_POST['form_id']);

            // Fetch submissions for this form
            $responses = $wpdb->get_results(
                $wpdb->prepare(
                    "SELECT * FROM {$wpdb->prefix}custom_form_responses WHERE form_id = %d ORDER BY created_at DESC",
                    $form_id
                )
            );
        }

        // Pass data to template
        include plugin_dir_path(__FILE__) . '../templates/render-all-form-response.php';
    }

    public function render_setting_page() {
        global $wpdb;

        // Fetch all forms
        $forms = get_posts([
            'post_type'      => 'custom_form',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
        ]);

        $filtered_responses = [];
        $selected_form_id   = 0;
        $selected_form_name = '';

        // Handle form filter (GET)
        if (!empty($_GET['form_name_filter'])) {
            $form_name_filter = sanitize_text_field($_GET['form_name_filter']);

            // Find the form ID by name
            foreach ($forms as $form) {
                if ($form->post_title === $form_name_filter) {
                    $selected_form_id   = $form->ID;
                    $selected_form_name = $form->post_title;

                    // Fetch responses
                    $filtered_responses = $wpdb->get_results(
                        $wpdb->prepare(
                            "SELECT * FROM {$wpdb->prefix}custom_form_responses WHERE form_id = %d ORDER BY created_at DESC",
                            $selected_form_id
                        )
                    );

                    break;
                }
            }
        }

        // Handle CSV Export Request
        if (isset($_GET['export_csv']) && $selected_form_id) {
            $this->export_responses_as_csv($filtered_responses, $selected_form_name);
            exit;
        }

        // Pass data to template
        include plugin_dir_path(__FILE__) . '../templates/render-setting-page.php';
    }

    private function export_responses_as_csv($responses, $form_name) {
        if (empty($responses)) {
            wp_die('No responses to export.');
        }

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="form_responses_' . sanitize_title($form_name) . '.csv"');

        $output = fopen('php://output', 'w');

        // Add CSV headers
        fputcsv($output, ['Submission ID', 'Form Name', 'Fields Data (meta_key => meta_value)', 'Submitted At']);

        global $wpdb;

        foreach ($responses as $response) {
            $meta_items = $wpdb->get_results(
                $wpdb->prepare(
                    "SELECT meta_key, meta_value FROM {$wpdb->prefix}custom_form_responses_meta WHERE submission_id = %d",
                    $response->id
                )
            );

            $fields_data = [];
            foreach ($meta_items as $meta) {
                $fields_data[] = $meta->meta_key . ': ' . $meta->meta_value;
            }

            fputcsv($output, [
                $response->id,
                $response->form_name,
                implode(' | ', $fields_data),
                $response->created_at,
            ]);
        }

        fclose($output);
    }


}

