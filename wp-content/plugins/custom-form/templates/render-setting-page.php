<?php if (!defined('ABSPATH')) exit; ?>

<div class="wrap">
    <h1>📋 Custom Form Responses</h1>

    <form method="get" style="margin-bottom: 20px;">
        <input type="hidden" name="page" value="<?php echo esc_attr($_GET['page']); ?>">

        <label for="form_id_filter">Select Form:</label>
        <select name="form_id_filter" id="form_id_filter">
            <option value="">-- Select Form --</option>
            <?php foreach ($forms as $form) : ?>
                <option value="<?php echo esc_attr($form->ID); ?>" <?php selected(isset($_GET['form_id_filter']) ? intval($_GET['form_id_filter']) : 0, $form->ID); ?>>
                    <?php echo esc_html($form->post_title); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button type="submit" class="button">Apply Filter</button>
    </form>

    <?php if ($selected_form && $responses): ?>
        <h2>Responses for: <?php echo esc_html($selected_form->post_title); ?></h2>

        <table class="widefat striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Submission ID</th>
                    <th>Submitted At</th>
                    <th>Fields</th>
                </tr>
            </thead>
            <tbody>
                <?php $counter = 1; ?>
                <?php foreach ($responses as $response): ?>
                    <tr>
                        <td><?php echo $counter++; ?></td>
                        <td><?php echo esc_html($response->id); ?></td>
                        <td><?php echo esc_html($response->created_at); ?></td>
                        <td>
                            <ul>
                                <?php
                                $meta_items = $wpdb->get_results(
                                    $wpdb->prepare(
                                        "SELECT meta_key, meta_value FROM {$wpdb->prefix}custom_form_responses_meta WHERE submission_id = %d",
                                        $response->id
                                    )
                                );

                                foreach ($meta_items as $meta):
                                    echo '<li><strong>' . esc_html($meta->meta_key) . ':</strong> ' . esc_html($meta->meta_value) . '</li>';
                                endforeach;
                                ?>
                            </ul>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Export CSV Button Using POST Method -->
        <form method="post" action="<?php echo admin_url('admin-post.php'); ?>" style="margin-top: 20px;">
            <input type="hidden" name="action" value="export_custom_form_csv">
            <input type="hidden" name="form_id_filter" value="<?php echo esc_attr($selected_form_id); ?>">
            <?php wp_nonce_field('export_csv_action', 'export_csv_nonce'); ?>
            <button type="submit" class="button button-primary">📥 Export Responses as CSV</button>
        </form>
        
    <?php elseif (isset($_GET['form_id_filter'])): ?>
        <p>No responses found for this form.</p>
    <?php endif; ?>
</div>
