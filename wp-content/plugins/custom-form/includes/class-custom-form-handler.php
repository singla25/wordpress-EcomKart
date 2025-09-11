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
        <form method="post" class="custom-form" action="">
            <?php wp_nonce_field('submit_custom_form', 'custom_form_nonce'); ?>

            <input type="hidden" name="custom_form_id" value="<?php echo esc_attr($form_id); ?>">

            <?php
            if (!empty($fields)) {
                foreach ($fields as $index => $field) {
                    $label = isset($field['label']) ? $field['label'] : '';
                    $type = isset($field['type']) ? $field['type'] : 'text';
                    $required = !empty($field['required']) ? 'required' : '';
                    $width_class = (isset($field['width']) && $field['width'] === '50%') ? 'field-width-50' : 'field-width-100';

                    echo '<div class="' . esc_attr($width_class) . '">';

                    switch ($type) {
                        case 'text':
                            echo '<label>' . esc_html($label) . ' <input type="' . esc_attr($type) . '" name="field_' . $index . '" ' . $required . '></label>';
                            break;
                        case 'email':
                            echo '<label>' . esc_html($label) . ' <input type="' . esc_attr($type) . '" name="field_' . $index . '" ' . $required . '></label>';
                            break;
                        case 'number':
                            echo '<label>' . esc_html($label) . ' <input type="' . esc_attr($type) . '" name="field_' . $index . '" ' . $required . '></label>';
                            break;
                        case 'phone':
                            echo '<label>' . esc_html($label) . ' <input type="' . esc_attr($type) . '" name="field_' . $index . '" ' . $required . '></label>';
                            break;
                        case 'password':
                            echo '<label>' . esc_html($label) . ' <input type="' . esc_attr($type) . '" name="field_' . $index . '" ' . $required . '></label>';
                            break;
                        case 'textarea':
                            echo '<label>' . esc_html($label) . ' <textarea name="field_' . $index . '" ' . $required . '></textarea></label>';
                            break;
                        case 'select':
                            echo '<label>' . esc_html($label) . '<select name="field_' . $index . '" ' . $required . '>';
                            foreach ($options as $option) {
                                echo '<option value="' . esc_attr($option) . '">' . esc_html($option) . '</option>';
                            }
                            echo '</select></label>';
                            break;
                        case 'checkbox':
                        case 'radio':
                            echo '<label>' . esc_html($label) . '</label><br>';

                            $options = isset($field['options']) ? $field['options'] : '';

                            // Convert string into clean array of options
                            $options_array = explode(' ', trim($options));

                            foreach ($options_array as $option_value) {
                                $input_name = 'field_' . $index;
                                if ($type === 'checkbox') {
                                    $input_name .= '[]';  // For multiple checkbox values
                                }

                                echo '<label style="margin-right:15px;">';
                                echo '<input type="' . esc_attr($type) . '" name="' . esc_attr($input_name) . '" value="' . esc_attr($option_value) . '"> ';
                                echo esc_html($option_value);
                                echo '</label>';
                            }
                            break;


                    }
                    echo '</div>'; // close width wrapper
                }
            } else {
                echo '<p>No fields found.</p>';
            }
            ?>
            <br><button type="submit" name="custom_form_submit" class="button">Submit</button>
        </form>
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
