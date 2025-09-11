<?php
if ( ! defined( 'ABSPATH' ) ) exit;

use Dompdf\Dompdf;
use Dompdf\Options;

class Questionnaire_Core {

    public function __construct() {
        add_action('init', [$this, 'register_question_cpt']);
        add_action('wp_ajax_submit_questionnaire',[$this, 'submit_questionnaire']);
        add_action('wp_ajax_nopriv_submit_questionnaire',[$this, 'submit_questionnaire']);


        add_action('admin_post_generate_pdf',[$this, 'generate_pdf_handler']);
        add_action('admin_post_nopriv_generate_pdf',[$this, 'generate_pdf_handler']);

        // add_action('admin_post_generate_pdf', 'generate_pdf_handler');
        // add_action('admin_post_nopriv_generate_pdf', 'generate_pdf_handler'); // if frontend
        add_filter('wp_mail_content_type', [$this, 'set_html_content_type']);

        add_filter( 'theme_page_templates', [ $this, 'add_template_to_dropdown' ] );
        add_filter( 'template_include', [ $this, 'load_plugin_template' ] );
    }
   

    public function add_template_to_dropdown( $templates ) {
        $templates['questionnaire-page-template.php'] = __( 'Questionnaire Page', ' questionnaire-page-template' );
        return $templates;
    }

    public function load_plugin_template( $template ) {
        if ( is_singular( 'page' ) ) {
            global $post;
            $page_template = get_post_meta( $post->ID, '_wp_page_template', true );

            if ( $page_template === 'questionnaire-page-template.php' ) {
                $plugin_template = plugin_dir_path( __FILE__ ) . 'templates/ questionnaire-page-template.php';
                if ( file_exists( $plugin_template ) ) {
                    return $plugin_template;
                }
            }
        }
        return $template;
    }
    
    public function set_html_content_type() {
        return "text/html";
    }

    function generate_pdf_handler() {
        if (!isset($_GET['submission_id'])) {
            wp_die('No submission ID provided');
        }
    
        global $wpdb;
        $table_name = $wpdb->prefix . 'questionnaire_entries';
    
        $submission_id = intval($_GET['submission_id']);
        $entry = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE submission_id = %d", $submission_id));
    
        if (!$entry) {
            wp_die('Submission not found');
        }
    
        $form_data = maybe_unserialize($entry->form_data);

 

        $submitted_at = maybe_unserialize($entry->submitted_at);
        if (isset($_GET['type']) && $_GET['type'] === 'admin') {
            $html_admin = $this->prepare_pdf_html_admin($form_data , $submitted_at);
        } else {
            $html_admin = $this->prepare_pdf_html_user($form_data , $submitted_at);
        }
        



        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', true);
        $options->set('isRemoteEnabled', true);
        $dompdf = new Dompdf($options);
        // Use Dompdf
        $dompdf->loadHtml($html_admin);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
    
        // Stream PDF (open in browser tab)
        $dompdf->stream("submission-$submission_id.pdf", ["Attachment" => false]); 
        exit;
    }
    

  
    function submit_questionnaire() {
        global $wpdb;
        check_ajax_referer('submit_questionnaire_nonce', 'security');

        $table_name = $wpdb->prefix . "questionnaire_entries";
        $form_id    = intval($_POST['form_id'] ?? 0);
        $total_points    = intval($_POST['total_points'] ?? 0);
        $percentage    = intval($_POST['overall_percentage'] ?? 0);
        $medal    = $_POST['overall_rank'] ?? 0;
        $form_data  = $_POST['form_data'] ?? [];
        $steps_data  = $_POST['steps_data'] ?? [];

        
    
        if(!$form_id || empty($form_data)){
            wp_send_json_error(['message' => 'Invalid submission']);
        }


        $na_option = get_option('global_na_option', 'do_nothing'); // default to do_nothing
        $na_custom_points = get_option('global_na_custom_points', 0); // default 0

        // Example usage:
        if ($na_option === 'minus_field_points') {
            // Subtract the points of the NA field
        } elseif ($na_option === 'do_nothing') {
            // Do nothing
        } elseif ($na_option === 'minus_custom_points') {
            // Subtract custom points
            $points_to_deduct = $na_custom_points;
        }



        $get_user_score = $this->count_user_percentage($form_data , $total_points);

        // $total_points = $get_user_score['total_score'];
        $possible_points = $get_user_score['total_possible'];
        $form_data['total_points']    = $total_points;
        $form_data['possible_points'] = $possible_points;
        $form_data['percentage']      = $percentage;
        $form_data['medal']           = $medal;
        $form_data['steps_data']           = $steps_data;


        $wpdb->insert(
            $table_name,
            [
                'form_id'     => $form_id,
                'form_data'   => maybe_serialize($form_data),
                'submitted_at'=> current_time('mysql')
            ],
            ['%d','%s','%s']
        );
    
        if(!$wpdb->insert_id){
            wp_send_json_error(['message' => 'Database insert failed']);
        }
        $entry_id = $wpdb->insert_id;
        // Build the PDF URL
        $pdf_url = add_query_arg([
            'action'        => 'generate_pdf',
            'type'        => 'user',
            'submission_id' => $entry_id
        ], admin_url('admin-post.php'));

        // // Get template & replace placeholders
        $email_template = get_option('global_email_template', '');
        if (empty($email_template)) {
            // Optional: set a sensible default if none saved
            $email_template = '<p>Hello,</p><p>Your report is ready: <a href="!pdf_url!">View PDF</a></p>';
        }
       
        // Replace both tokens just in case
        $search  = ['!pdf_url!', '!odf_ur!'];
        $replace = [esc_url($pdf_url), esc_url($pdf_url)];
        $message = str_replace($search, $replace, $email_template);
        $to_email = "";
        // Prepare and send email
        if(isset($form_data['email']) && $form_data['email']['is_pre_field'] == 1 ){
           $to_email =  $form_data['email']['value'];
        }
        if($to_email){
            $subject  = "Your Questionnaire Report";
            $headers  = ['Content-Type: text/html; charset=UTF-8'];
    
            wp_mail($to_email, $subject, $message, $headers);
        }


        $thankyou_content = get_option('global_thankyou_content', '');
        $thankyou_content = apply_filters('the_content', $thankyou_content); // format + shortcodes

        wp_send_json_success([
            'message' => 'Form submitted successfully!',
            'thankyou_content' => $thankyou_content,
            'pdf_url' => esc_url($pdf_url), // also expose the PDF link if frontend needs it
        ]);

    }
    public function count_user_percentage($form_data) {
        $total_score = 0;
        $total_possible = 0;
    
        foreach ($form_data as $field_key => $field) {
            if (!is_array($field) || empty($field['q_id'])) continue;
    
            $post_id = $field['q_id'];
            $selected_value = trim(strtolower($field['value'] ?? ''));
            $_answers = get_post_meta($post_id, '_answers', true) ?: [];
    
            if (empty($_answers)) continue;
    
            // Find the selected option and determine if it's N/A
            $is_na_selected = false;
            $selected_points = 0;
            $max_points = 0;
    
            foreach ($_answers as $option) {
                $option_text = trim(strtolower($option['text'] ?? ''));
                $option_points = intval($option['points'] ?? 0);
                $option_is_na = intval($option['is_na'] ?? 0);
    
                // Update max possible for this question
                if ($option_points > $max_points) {
                    $max_points = $option_points;
                }
    
                // If this is the selected value
                if ($this->normalize($option_text) === $this->normalize($selected_value)) {

                    if ($option_is_na === 1) {
                        $is_na_selected = true;
                    } else {
                        $selected_points = $option_points;
                    }
                }
            }
    
            // Only add to total_possible if not N/A
            if (!$is_na_selected) {
                $total_possible += $max_points;
                $total_score += $selected_points;
            }
        }
    
        // Prevent division by zero
        $percentage = $total_possible > 0 ? round(($total_score / $total_possible) * 100, 2) : 0;
    
        return [
            'total_score' => $total_score,
            'total_possible' => $total_possible,
            'percentage' => $percentage
        ];
    }
    function normalize($str) {
        return strtolower(trim(preg_replace('/[^a-z0-9]/', '', $str)));
    }
    public function generate_pdf($form_data, $entry_id) {
        $upload_dir = wp_upload_dir();
        $pdf_dir = $upload_dir['basedir'] . '/questionnaire_pdfs/';
        if (!file_exists($pdf_dir)) {
            wp_mkdir_p($pdf_dir);
        }
    
 
        $html_user = $this->prepare_pdf_html_user($form_data);
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', true);
        $dompdf_user = new Dompdf($options);
    
        // Generate User PDF
        $dompdf_user->loadHtml($html_user);
        $dompdf_user->setPaper('A4', 'portrait');
        $dompdf_user->render();
        $file_user = $pdf_dir . "entry_{$entry_id}_user.pdf";
        file_put_contents($file_user, $dompdf_user->output());
    
        return [$file_user];
    }
    
    
    public function register_question_cpt() {


        $labels = [
            'name' => 'Questions',
            'singular_name' => 'Question',
            'add_new' => 'Add New Question',
            'add_new_item' => 'Add New Question',
            'edit_item' => 'Edit Question',
            'new_item' => 'New Question',
            'all_items' => 'All Questions',
            'view_item' => 'View Question',
            'search_items' => 'Search Questions',
            'not_found' => 'No questions found',
            'menu_name' => 'Questions'
        ];

        $args = [
            'labels' => $labels,
            'public' => false,
            'show_ui' => true,
            // 'show_in_menu' => 'questionnaire_manager', 
            'show_in_menu' => true, 
            'supports'           => ['title', 'thumbnail'],
            'capability_type' => 'post',
            'menu_icon' => 'dashicons-editor-help',
        ];

        register_post_type('question', $args);
        register_taxonomy('question_category', 'question', [
            'label' => 'Question Categories',
            'hierarchical' => true,
            'show_ui' => true,
            'show_in_menu' => 'questionnaire_manager',  
            'show_admin_column' => true, 
            'rewrite' => ['slug' => 'question-category'],
        ]);
        

        register_taxonomy_for_object_type('question_category', 'question');
        
    

        $form_labels = [
            'name' => 'Forms',
            'singular_name' => 'Form',
            'add_new_item' => 'Add New Form',
            'edit_item' => 'Edit Form',
            'new_item' => 'New Form',
            'all_items' => 'All Forms',
            'menu_name' => 'Forms'
        ];
    
        $form_args = [
            'labels' => $form_labels,
            'public' => false,
            'show_ui' => true,
            'show_in_menu' => 'questionnaire_manager', 
            'supports' => ['title'],
            'capability_type' => 'post'
        ];
    
        register_post_type('questionnaire_form', $form_args);
    }

    private function separate_fields($data) {

        $pre_fields = array_filter($data, function($field){
            return is_array($field) && isset($field['is_pre_field']) && $field['is_pre_field'] == 1;
        });
    

        $other_fields = array_filter($data, function($field){
            return is_array($field) && isset($field['is_pre_field']) && $field['is_pre_field'] == 0;
        });
    

        $meta = array_filter($data, function($field){
            return !is_array($field) || !isset($field['is_pre_field']);
        });
    
        return compact('pre_fields', 'other_fields', 'meta');
    }

    private function group_by_category($fields) {
        $grouped = [];
    
        foreach ($fields as $key => $field) {
            // Only include fields that have a q_id
            if (empty($field['q_id']) || $field['q_id'] == 0) {
                continue;
            }
    
            $cat_id = !empty($field['category']) ? $field['category'] : 'no_category';
            $grouped[$cat_id][$key] = $field;
        }
    
        return $grouped;
    }
    
    
    public function prepare_pdf_html_admin($form_data, $submitted_at) {

        $separated = $this->separate_fields($form_data);
    

        $pre_fields   = $separated['pre_fields'];
        
        $other_fields = $this->group_by_category($separated['other_fields']);

        // echo "<pre>"; print_r($other_fields);die;
        $meta   = $separated['meta'];

        $percentage = $meta['percentage'];
        $timestamp = strtotime($submitted_at);
        // Format as d/m/y
        $state = $pre_fields["state_province"]["value"] ?? "";
        $Type = $pre_fields["Type"]["value"] ?? "";
        $client_name = $pre_fields["client_name"]["value"] ?? "";


        $formatted_date = date('d/m/y', $timestamp);
        $result = $this->get_percentage($percentage);
        $medal = $result['medal'];
        $stars =  $result['stars'];
    
        ob_start();
        include QUESTIONNAIRE_PLUGIN_DIR . '/templates/admin_pdf_template.php';
        return ob_get_clean();
    }
    
    
    public function prepare_pdf_html_user($form_data , $submitted_at) {

        $separated = $this->separate_fields($form_data);

        $pre_fields   = $separated['pre_fields'];
        $other_fields = $this->group_by_category($separated['other_fields']);
        $meta         = $separated['meta'];

       
        $percentage = $meta['percentage'];
        $timestamp = strtotime($submitted_at);
        // Format as d/m/y
        $state = $pre_fields["state_province"]["value"] ?? "";
        $Type = $pre_fields["Type"]["value"] ?? "";
        $client_name = $pre_fields["client_name"]["value"] ?? "";


        $formatted_date = date('d/m/y', $timestamp);

        $result = $this->get_percentage($percentage);
        $medal = $result['medal'];
        $stars =  $result['stars'];
        $badge_image =  $result['image'];
        $lastIndex = count($meta['steps_data']) - 1;
        $processed_steps = []; 
         foreach ($meta['steps_data'] as $key => $meta_d) {
                    if ($key === 0 || $key === $lastIndex) {
                        continue; // skip first and last
                    }

                    // Add medal & stars for each step based on its percentage
                    $stepResult = $this->get_percentage($meta_d['step_percentage']);
                    $meta_d['medal'] = $stepResult['medal'];
                    $meta_d['stars'] = $stepResult['stars'];
                    $meta_d['image'] = $stepResult['image'];

                    
                    $processed_steps[] = $meta_d;
                }


        ob_start();
        include QUESTIONNAIRE_PLUGIN_DIR . '/templates/user_pdf_template.php';
        return ob_get_clean();
    }
    
    function get_percentage($percentage){
        if ($percentage >= 90) {
            $medal = 'PLATINUM';
            $stars = 5;
            $image = QUESTIONNAIRE_PLUGIN_URL . 'assets/image/badge1.jpg';;

        } elseif ($percentage >= 80) {
            $medal = 'GOLD';
            $stars = 4;
            $image = QUESTIONNAIRE_PLUGIN_URL . 'assets/image/badge3.jpg';;
      
        } elseif ($percentage >= 67) {
            $medal = 'SILVER';
            $stars = 3;
            $image = QUESTIONNAIRE_PLUGIN_URL . 'assets/image/badge2.jpg';;

        } else {
            $medal = 'BRONZE';
            $stars = 2;
            $image = QUESTIONNAIRE_PLUGIN_URL . 'assets/image/badge5.jpg';
        }
    
        return [
            'medal' => $medal,
            'stars' => $stars,
            'image' => $image

        ];
    }
    
    
}

new Questionnaire_Core();
