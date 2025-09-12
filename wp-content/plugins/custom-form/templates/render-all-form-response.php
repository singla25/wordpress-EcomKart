<?php if (!defined('ABSPATH')) exit; ?>

<div class="wrap">
    <h1>📋 All Custom Form Responses</h1>

    <!-- Filter Form -->
    <form method="get" style="margin-bottom: 20px;">
        <input type="hidden" name="page" value="<?php echo esc_attr($_GET['page']); ?>"> <!-- Preserve admin page slug -->
        
        <label for="form_name_filter">Filter by Form Name:</label>
        <select name="form_name_filter" id="form_name_filter">
            <option value="">-- All Forms --</option>
            <?php foreach ($forms as $form_name) : ?>
                <option value="<?php echo esc_attr($form_name->post_title); ?>" <?php selected(isset($_GET['form_name_filter']) ? $_GET['form_name_filter'] : '', $form_name->post_title); ?>>
                    <?php echo esc_html($form_name->post_title); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="button">🔍 Apply Filter</button>
    </form>

    <!-- Forms Table -->
    <table class="widefat striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Form Name</th>
                <th>Purpose</th>
                <th>View Form Responses</th>
                <th>Date of Form Created</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Apply Filter if Set
            $filtered_forms = $forms;
            
            if (!empty($_GET['form_name_filter'])) {
                $filtered_forms = array_filter($forms, function($form) {
                    return $form->post_title === sanitize_text_field($_GET['form_name_filter']);
                });
            }
            ?>

            <?php if (!empty($filtered_forms)) : ?>
                <?php foreach ($filtered_forms as $form) : ?>
                    <tr>
                        <td><?php echo esc_html($form->ID); ?></td>
                        <td><?php echo esc_html($form->post_title); ?></td>
                        <td><?php echo esc_html(get_post_meta($form->ID, 'form_purpose', true)); ?></td>
                        <td>
                            <form method="post" style="display:inline;">
                                <input type="hidden" name="form_id" value="<?php echo esc_attr($form->ID); ?>">
                                <button type="submit" class="button">📋 View Responses</button>
                            </form>
                        </td>
                        <td><?php echo esc_html(get_the_date('', $form->ID)); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr><td colspan="5">No forms found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <?php if (isset($responses)) : ?>
        <h2>All Responses for Form ID: <?php echo esc_html($form_id); ?></h2>

        <table class="widefat striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Submission ID</th>                    
                    <th>Form Name</th>
                    <th>Fields Data</th>
                    <th>Submitted At</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($responses)) : ?>
                    <?php $counter = 1; ?>
                    <?php foreach ($responses as $response) : ?>
                        <tr>
                            <td><?php echo $counter++; ?></td>
                            <td><?php echo esc_html($response->id); ?></td>
                            <td><?php echo esc_html($response->form_name); ?></td>
                            <td>
                                <?php
                                $meta_items = $wpdb->get_results(
                                    $wpdb->prepare(
                                        "SELECT meta_key, meta_value FROM {$wpdb->prefix}custom_form_responses_meta WHERE submission_id = %d",
                                        $response->id
                                    )
                                );

                                if (!empty($meta_items)) {
                                    echo '<ul>';
                                    foreach ($meta_items as $meta) {
                                        echo '<li><strong>' . esc_html($meta->meta_key) . ':</strong> ' . esc_html($meta->meta_value) . '</li>';
                                    }
                                    echo '</ul>';
                                } else {
                                    echo 'No fields submitted.';
                                }
                                ?>
                            </td>
                            <td><?php echo esc_html($response->created_at); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr><td colspan="7">No responses found for this form.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
