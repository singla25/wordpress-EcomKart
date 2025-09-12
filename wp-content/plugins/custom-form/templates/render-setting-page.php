<?php if (!defined('ABSPATH')) exit; ?>

<div class="wrap">
    <h1>📋 All Custom Form Responses</h1>

    <!-- Filter Form -->
    <form method="get" style="margin-bottom: 20px;">
        <input type="hidden" name="page" value="<?php echo esc_attr($_GET['page']); ?>">
        
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

    <?php if (!empty($filtered_responses)) : ?>
        <h2>Responses for Form: <?php echo esc_html($selected_form_name); ?></h2>

        <!-- Export CSV Link -->
        <a href="<?php echo admin_url('admin.php?page=' . esc_attr($_GET['page']) . '&form_name_filter=' . urlencode($selected_form_name) . '&export_csv=1'); ?>" class="button button-primary" style="margin-bottom: 20px;">
            ⬇️ Export Responses as CSV
        </a>

        <table class="widefat striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Submission ID</th>
                    <th>Fields Data</th>
                    <th>Submitted At</th>
                </tr>
            </thead>
            <tbody>
                <?php $counter = 1; ?>
                <?php foreach ($filtered_responses as $response) : ?>
                    <tr>
                        <td><?php echo $counter++; ?></td>
                        <td><?php echo esc_html($response->id); ?></td>
                        <td>
                            <ul>
                                <?php
                                $meta_items = $wpdb->get_results(
                                    $wpdb->prepare(
                                        "SELECT meta_key, meta_value FROM {$wpdb->prefix}custom_form_responses_meta WHERE submission_id = %d",
                                        $response->id
                                    )
                                );

                                foreach ($meta_items as $meta) {
                                    echo '<li><strong>' . esc_html($meta->meta_key) . ':</strong> ' . esc_html($meta->meta_value) . '</li>';
                                }
                                ?>
                            </ul>
                        </td>
                        <td><?php echo esc_html($response->created_at); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

    <?php elseif (isset($_GET['form_name_filter'])) : ?>
        <p>No responses found for selected form.</p>
    <?php endif; ?>
</div>
