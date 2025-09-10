<?php if (!defined('ABSPATH')) exit; ?>

<div class="wrap">
    <h1>📋 All Custom Forms</h1>

    <?php if (isset($_GET['message']) && $_GET['message'] === 'success') : ?>
        <div class="notice notice-success is-dismissible">
            <p>✅ Your form has been registered successfully!</p>
        </div>
    <?php endif; ?>

    <table class="widefat striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Form Name</th>
                <th>Purpose</th>
                <th>Author</th>
                <th>Fields</th>
                <th>Actions</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($forms) : ?>
                <?php foreach ($forms as $form) : ?>
                    <tr>
                        <td><?php echo esc_html($form->ID); ?></td>
                        <td><?php echo esc_html($form->post_title); ?></td>
                        <td><?php echo esc_html($form->post_content); ?></td>
                        <td><?php echo esc_html(get_the_author_meta('display_name', $form->post_author)); ?></td>
                        <td>
                            <?php 
                                $meta_value = get_post_meta($form->ID, 'form_fields', true); 
                                $fields = json_decode($meta_value, true);

                                if (is_array($fields) && !empty($fields)) :
                                    echo '<table class="widefat striped" style="margin-top:10px;">';
                                    echo '<thead><tr>';
                                    echo '<th>Label</th>';
                                    echo '<th>Type</th>';
                                    echo '<th>Width</th>';
                                    echo '<th>Required</th>';
                                    echo '<th>Options</th>';
                                    echo '</tr></thead>';
                                    echo '<tbody>';
                                    foreach ($fields as $field) :
                                        ?>
                                        <tr>
                                            <td><?php echo esc_html($field['label']); ?></td>
                                            <td><?php echo esc_html($field['type']); ?></td>
                                            <td><?php echo esc_html($field['width']); ?></td>
                                            <td><?php echo ($field['required'] ? 'Yes' : 'No'); ?></td>
                                            <td><?php echo esc_html(implode(', ', $field['options'])); ?></td>
                                        </tr>
                                        <?php
                                    endforeach;
                                    echo '</tbody></table>';
                                else :
                                    echo 'No fields found.';
                                endif;
                            ?>
                        </td>
                        <td>
                            <a href="<?php echo admin_url('admin.php?page=custom_form_edit&form_id=' . esc_attr($form->ID)); ?>" class="button">✏️ Edit</a>

                            <form method="post" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this form?');">
                                <input type="hidden" name="action" value="delete_custom_form">
                                <input type="hidden" name="form_id" value="<?php echo esc_attr($form->ID); ?>">
                                <button type="submit" class="button button-danger">🗑️ Delete</button>
                            </form>
                        </td>
                        <td><?php echo esc_html(get_the_date('', $form->ID)); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr>
                    <td colspan="6">No forms found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
