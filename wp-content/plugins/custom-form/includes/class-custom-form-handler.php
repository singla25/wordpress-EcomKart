<?php
if (!defined('ABSPATH')) exit;

class Custom_Form_Handler {
    public function __construct() {
        add_action('wp_enqueue_scripts', [$this, 'enqueue_assets']);
        add_shortcode('custom_form', [$this, 'render_custom_form_shortcode']);
        add_action('init', [$this, 'handle_submission']);
    }

    // Enqueue CSS/JS
    public function enqueue_assets() {
        wp_enqueue_style('custom-form-css', plugin_dir_url(__FILE__) . '../assets/css/custom-form-handler.css');
        // wp_enqueue_script('custom-form-js', plugin_dir_url(__FILE__) . '../assets/js/custom-form-handler.js', ['jquery'], null, true);
    }

    // Render shortcode
    public function render_custom_form_shortcode($atts) {
        if (!isset($atts['id'])) return 'Form ID not specified.';

        $form_id = intval($atts['id']);
        $form = get_post($form_id);

        if (!$form || $form->post_type !== 'custom_form') return 'Form not found.';

        $fields = get_post_meta($form_id, 'form_fields', true);
        $fields = is_array($fields) ? $fields : [];

        ob_start();
        ?>
        <div class="custom-form-container">
            <h2 class="form-heading"><?php echo esc_html($form->post_title); ?></h2>
            <form method="post" class="custom-form" action="">
                <?php wp_nonce_field('submit_custom_form', 'custom_form_nonce'); ?>
                <input type="hidden" name="custom_form_id" value="<?php echo esc_attr($form_id); ?>">

                <?php
                $combined_fields_group = [];  // Collect combined fields

                foreach ($fields as $index => $field) {
                    $label = sanitize_text_field($field['label'] ?? '');
                    $type = sanitize_text_field($field['type'] ?? 'text');
                    $required = !empty($field['required']) ? 'required' : '';
                    $width = $field['width'] ?? '100%';
                    $combined = $field['combined'] ?? false;

                    $width_class = ($width === '50%') ? 'field-width-50' : 'field-width-100';

                    if ($combined) {
                        $combined_fields_group[] = ['index' => $index, 'field' => $field];
                        continue;
                    }

                    echo '<div class="form-field ' . esc_attr($width_class) . '">';
                    echo '<label>' . esc_html(ucfirst($label)) . '</label>';

                    if (in_array($type, ['text', 'email', 'number', 'phone', 'password'])) {
                        echo '<input type="' . esc_attr($type) . '" name="field_' . $index . '" ' . $required . '>';
                    } elseif ($type === 'textarea') {
                        echo '<textarea name="field_' . $index . '" ' . $required . '></textarea>';
                    } elseif ($type === 'select') {
                        $options = explode(' ', trim($field['options'] ?? ''));
                        echo '<select name="field_' . $index . '" ' . $required . '>';
                        foreach ($options as $option) {
                            echo '<option value="' . esc_attr($option) . '">' . esc_html($option) . '</option>';
                        }
                        echo '</select>';
                    } elseif ($type === 'checkbox' || $type === 'radio') {
                        $options = explode(' ', trim($field['options'] ?? ''));
                        $group_class = $type === 'checkbox' ? 'checkbox-group' : 'radio-group';
                        echo '<div class="' . $group_class . '">';
                        foreach ($options as $option) {
                            $input_name = 'field_' . $index;
                            if ($type === 'checkbox') $input_name .= '[]';

                            echo '<label>';
                            echo '<input type="' . esc_attr($type) . '" name="' . esc_attr($input_name) . '" value="' . esc_attr($option) . '"> ';
                            echo " " . esc_html($option);
                            echo '</label>';
                        }
                        echo '</div>';
                    }

                    echo '</div>';
                }

                // Render combined fields in one row
                if (!empty($combined_fields_group)) {
                    echo '<div class="form-field field-width-100" style="display: flex; gap: 20px;">';
                    foreach ($combined_fields_group as $combined_field) {
                        $index = $combined_field['index'];
                        $field = $combined_field['field'];
                        $label = sanitize_text_field($field['label'] ?? '');
                        $type = sanitize_text_field($field['type'] ?? 'text');
                        $required = !empty($field['required']) ? 'required' : '';

                        echo '<div class="field-width-50">';
                        echo '<label>' . esc_html($label) . '</label>';

                        if (in_array($type, ['text', 'email', 'number', 'phone', 'password'])) {
                            echo '<input type="' . esc_attr($type) . '" name="field_' . $index . '" ' . $required . '>';
                        } elseif ($type === 'textarea') {
                            echo '<textarea name="field_' . $index . '" ' . $required . '></textarea>';
                        }
                        echo '</div>';
                    }
                    echo '</div>';
                }
                ?>
                <button type="submit" name="custom_form_submit">Submit</button>
            </form>
        </div>
        <?php
        return ob_get_clean();
    }

    // Handle submission
    public function handle_submission() {
        if (!isset($_POST['custom_form_submit'])) return;
        if (!isset($_POST['custom_form_id'])) return;

        $form_id = intval($_POST['custom_form_id']);
        $form = get_post($form_id);

        if (!$form || $form->post_type !== 'custom_form') return;
        if (!isset($_POST['custom_form_nonce']) || !wp_verify_nonce($_POST['custom_form_nonce'], 'submit_custom_form')) return;

        global $wpdb;
        $fields = get_post_meta($form_id, 'form_fields', true);
        $fields = is_array($fields) ? $fields : [];
        $user_id = get_current_user_id();

        // Insert main submission
        $wpdb->insert(
            $wpdb->prefix . 'custom_form_responses',
            [
                'user_id'          => $user_id,
                'form_id'          => $form_id,
                'form_name'        => sanitize_text_field($form->post_title),
                'form_description' => sanitize_textarea_field(get_post_meta($form_id, 'form_purpose', true)),
                'created_at'       => current_time('mysql')
            ]
        );

        $submission_id = $wpdb->insert_id;

        // Insert field responses
        foreach ($fields as $index => $field) {
            $label = sanitize_text_field($field['label'] ?? "field_{$index}");
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
                    'meta_key'      => $label,
                    'meta_value'    => $value
                ]
            );
        }

        wp_redirect(add_query_arg('form_submitted', 'true', get_permalink()));
        exit;
    }
}
