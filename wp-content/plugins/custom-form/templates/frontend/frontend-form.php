<?php if (!defined('ABSPATH')) exit; ?>

<form method="post" class="custom-form-frontend">
    <h3><?php echo esc_html($form->post_title); ?></h3>
    <p><?php echo esc_html($form->post_content); ?></p>

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

            if ($field['type'] === 'textarea'): ?>
                <textarea name="<?php echo esc_attr($field_name); ?>" <?php echo $required_attr; ?>></textarea>
            <?php elseif (in_array($field['type'], ['select', 'checkbox', 'radio'])): 
                $options = $field['options'];
                if ($field['type'] === 'select'): ?>
                    <select name="<?php echo esc_attr($field_name); ?>" <?php echo $required_attr; ?>>
                        <?php foreach ($options as $option): ?>
                            <option value="<?php echo esc_attr($option); ?>"><?php echo esc_html($option); ?></option>
                        <?php endforeach; ?>
                    </select>
                <?php else: ?>
                    <?php foreach ($options as $option): ?>
                        <label>
                            <input type="<?php echo esc_attr($field['type']); ?>" name="<?php echo esc_attr($field_name); ?>[]" value="<?php echo esc_attr($option); ?>" <?php echo $required_attr; ?>>
                            <?php echo esc_html($option); ?>
                        </label><br>
                    <?php endforeach; ?>
                <?php endif; ?>
            <?php else: ?>
                <input type="<?php echo esc_attr($field['type']); ?>" name="<?php echo esc_attr($field_name); ?>" <?php echo $required_attr; ?>>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>

    <button type="submit" name="custom_form_submit">Submit</button>

    <?php if (isset($_GET['form_submitted']) && $_GET['form_submitted'] === 'true'): ?>
        <div class="custom-form-success">✅ Your form has been submitted successfully!</div>
    <?php endif; ?>
</form>
