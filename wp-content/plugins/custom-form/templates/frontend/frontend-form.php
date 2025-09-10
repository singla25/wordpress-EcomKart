<?php if (!defined('ABSPATH')) exit; ?>

<form method="post" class="custom-form-frontend">
    <h3><?php echo esc_html($form->post_title); ?></h3>

    <input type="hidden" name="custom_form_id" value="<?php echo esc_attr($form_id); ?>">

    <?php foreach ($fields as $index => $field): ?>
        <div class="custom-form-field" style="width: <?php echo esc_attr($field['width']); ?>;">
            <label>
                <?php echo esc_html($field['label']); ?>
                <?php if ($field['required']): ?>
                    <span style="color: red;">*</span>
                <?php endif; ?>
            </label>

            <?php
            $field_name = "field_{$index}";
            $required_attr = $field['required'] ? 'required' : '';

            switch ($field['type']) {
                case 'textarea':
                    echo '<textarea name="' . esc_attr($field_name) . '" ' . $required_attr . '></textarea>';
                    break;

                case 'select':
                    echo '<select name="' . esc_attr($field_name) . '" ' . $required_attr . '>';
                    foreach ($field['options'] as $option) {
                        echo '<option value="' . esc_attr($option) . '">' . esc_html($option) . '</option>';
                    }
                    echo '</select>';
                    break;

                case 'checkbox':
                case 'radio':
                    foreach ($field['options'] as $option) {
                        echo '<label>';
                        echo '<input type="' . esc_attr($field['type']) . '" name="' . esc_attr($field_name) . '[]" value="' . esc_attr($option) . '" ' . $required_attr . '>';
                        echo esc_html($option);
                        echo '</label><br>';
                    }
                    break;

                default:
                    // For text, email, number, etc.
                    echo '<input type="' . esc_attr($field['type']) . '" name="' . esc_attr($field_name) . '" ' . $required_attr . '>';
                    break;
            }
            ?>
        </div>
    <?php endforeach; ?>

    <button type="submit" name="custom_form_submit">Submit</button>

    <?php if (isset($_GET['form_submitted']) && $_GET['form_submitted'] === 'true'): ?>
        <div class="custom-form-success">✅ Your form has been submitted successfully!</div>
    <?php endif; ?>
</form>
