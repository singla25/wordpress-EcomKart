<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Contact_Admin {

    public function __construct() {
        add_action('admin_menu', [$this, 'add_admin_menu']);
    }

    public function add_admin_menu() {
        add_menu_page(
            'Contact Submissions',
            'Contact Submissions',
            'manage_options',
            'contact_submissions',
            [$this, 'render_submissions_page'],
            'dashicons-email',
            6
        );
    }

    public function render_submissions_page() {
        global $wpdb;

        $table_main = $wpdb->prefix . 'contact_submissions';
        $table_meta = $wpdb->prefix . 'contact_submission_meta';

        // Get unique users for filter dropdown
        $users = $wpdb->get_results("SELECT DISTINCT user_id FROM {$table_main}");

        // Get filter values
        $selected_user = isset($_GET['filter_user']) ? intval($_GET['filter_user']) : -1;
        $sort_order = isset($_GET['sort_order']) && in_array($_GET['sort_order'], ['ASC','DESC']) ? $_GET['sort_order'] : 'DESC';

        // Build query with filter
        $query = "SELECT * FROM {$table_main}";
        if ($selected_user >= 0) {
            $query .= $wpdb->prepare(" WHERE user_id = %d", $selected_user);
        }
        $query .= " ORDER BY created_at {$sort_order}";

        $submissions = $wpdb->get_results($query);
        ?>
        <div class="wrap">
            <h1>📋 Contact Submissions</h1>

            <!-- Filter & Sort Form -->
            <form method="get" style="margin-bottom:20px;">
                <input type="hidden" name="page" value="contact_submissions">

                <select name="filter_user">
                    <option value="-1"<?php selected($selected_user, -1); ?>>All Users</option>
                    <?php foreach ($users as $user): 
                        $username = $user->user_id ? get_userdata($user->user_id)->user_login : 'Guest'; ?>
                        <option value="<?php echo esc_attr($user->user_id); ?>" <?php selected($selected_user, $user->user_id); ?>>
                            <?php echo esc_html($username); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <select name="sort_order">
                    <option value="DESC"<?php selected($sort_order, 'DESC'); ?>>Latest First</option>
                    <option value="ASC"<?php selected($sort_order, 'ASC'); ?>>Oldest First</option>
                </select>

                <button class="button" type="submit">Apply</button>
            </form>

            <?php if ($submissions): ?>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th>Sr.</th>
                            <th>User</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Subject</th>
                            <th>Topic</th>
                            <th>Query</th>
                            <th>Submitted At</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $sr = 1;
                        foreach ($submissions as $submission): 
                            $meta = $wpdb->get_results($wpdb->prepare(
                                "SELECT meta_key, meta_value FROM {$table_meta} WHERE submission_id = %d",
                                $submission->id
                            ));
                            $meta_data = [];
                            foreach ($meta as $m) {
                                $meta_data[$m->meta_key] = $m->meta_value;
                            }
                            $username = $submission->user_id ? get_userdata($submission->user_id)->user_login : 'Guest';
                        ?>
                            <tr>
                                <td><?php echo $sr++; ?></td>
                                <td><?php echo esc_html($username); ?></td>
                                <td><?php echo esc_html($submission->name); ?></td>
                                <td><?php echo esc_html($submission->email); ?></td>
                                <td><?php echo esc_html($meta_data['phone'] ?? ''); ?></td>
                                <td><?php echo esc_html($submission->subject); ?></td>
                                <td><?php echo esc_html($meta_data['topic'] ?? ''); ?></td>
                                <td><?php echo esc_html($meta_data['query'] ?? ''); ?></td>
                                <td><?php echo esc_html($submission->created_at); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No contact submissions found.</p>
            <?php endif; ?>
        </div>
        <?php
    }
}
