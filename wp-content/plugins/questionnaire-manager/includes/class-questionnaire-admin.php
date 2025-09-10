<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class Questionnaire_Admin {

    public function __construct() {
        add_action('add_meta_boxes', [$this, 'add_custom_meta_boxes']);
        add_action('save_post_question', [$this, 'save_question_meta']); 
        add_action('save_post_questionnaire_form', [$this, 'save_form_meta']); 
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_scripts']);
        add_action('admin_menu', [$this, 'register_admin_menu']);
        add_action('admin_init', [$this, 'register_global_settings']); 
        add_filter('manage_questionnaire_form_posts_columns', [$this, 'add_shortcode_column']);
        add_action('manage_questionnaire_form_posts_custom_column', [$this, 'render_shortcode_column'], 10, 2);
        // add_action('admin_init', [$this, 'register_global_settings']); 

        add_filter('manage_question_posts_columns', [$this, 'add_custom_column']);
        add_action('manage_question_posts_custom_column', [$this, 'render_custom_column'], 10, 2);

    }
    function enqueue_admin_scripts($hook) {
        global $post_type;
    
        
         // enqueue for question or questionnaire_form CPT editor
         if (
            ( $post_type === 'question' && in_array($hook, ['post-new.php', 'post.php']) ) ||
            ( $post_type === 'questionnaire_form' && in_array($hook, ['post-new.php', 'post.php']) ) ||
            ( $hook === 'toplevel_page_questionnaire_manager' ) // <-- add your page slug
        ) {
            wp_enqueue_media();
            wp_enqueue_script(
                'questionnaire-admin',
                QUESTIONNAIRE_PLUGIN_URL . 'assets/js/admin-questionnaire.js',
                ['jquery'],
                '1.0',
                true
            );
            wp_enqueue_style(
                'select2-css',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            );
            wp_enqueue_script(
                'select2-js',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
                ['jquery'],
                null,
                true
            );
        }
    }
    

 

    public function register_admin_menu() {
        add_menu_page(
            'Questionnaire Manager',
            'Questionnaire Manager',
            'manage_options',
            'questionnaire_manager',
            [$this, 'render_global_settings'],
            'dashicons-forms',
            26
        );
    

        add_submenu_page(
            'questionnaire_manager',
            'Settings',
            'Settings',
            'manage_options',
            'questionnaire_manager',
            [$this, 'render_global_settings']
        );

        add_submenu_page(
            'questionnaire_manager',
            'Submissions',
            'Submissions',
            'manage_options',
            'questionnaire_submissions',
            [$this, 'render_submissions_page']
        );
    }
    

    public function render_global_settings() {
        ?>
        <div class="wrap">
            <h1>Global Settings</h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('global_settings_group');
                do_settings_sections('global-settings');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }
    
    public function register_global_settings() {
        // --- Migration: Decode existing escaped HTML once ---
        $template = get_option('global_email_template');
        if ($template && strpos($template, '&lt;') !== false) {
            update_option('global_email_template', html_entity_decode($template));
        }
    
        $thankyou = get_option('global_thankyou_content');
        if ($thankyou && strpos($thankyou, '&lt;') !== false) {
            update_option('global_thankyou_content', html_entity_decode($thankyou));
        }
    
        /**
         * EMAIL TEMPLATE
         */
        register_setting(
            'global_settings_group',
            'global_email_template',
            [
                'sanitize_callback' => function($input) {
                    return wp_kses_post($input); // allow safe HTML
                }
            ]
        );
    
        add_settings_section('section_email', 'Email Template', null, 'global-settings');
        add_settings_field(
            'global_email_template',
            'Email Template Content',
            function() {
                $value = get_option('global_email_template', '');
                wp_editor($value, 'global_email_template', [
                    'textarea_name' => 'global_email_template',
                    'media_buttons' => true,
                    'textarea_rows' => 10,
                ]);
            },
            'global-settings',
            'section_email'
        );
    
        /**
         * THANK YOU PAGE
         */
        register_setting(
            'global_settings_group',
            'global_thankyou_content',
            [
                'sanitize_callback' => function($input) {
                    return wp_kses_post($input); // allow safe HTML
                }
            ]
        );
    
        add_settings_section('section_thankyou', 'Thank You Page', null, 'global-settings');
        add_settings_field(
            'global_thankyou_content',
            'Thank You Page Content',
            function() {
                $value = get_option('global_thankyou_content', '');
                wp_editor($value, 'global_thankyou_content', [
                    'textarea_name' => 'global_thankyou_content',
                    'media_buttons' => true,
                    'textarea_rows' => 10,
                ]);
            },
            'global-settings',
            'section_thankyou'
        );
    
        /**
         * SHOW PDF CHECKBOX
         */
        register_setting(
            'global_settings_group',
            'global_show_pdf',
            [
                'sanitize_callback' => function($input) {
                    return $input === '1' ? '1' : '0';
                }
            ]
        );
    
        add_settings_section('section_pdf', 'Submission Table', null, 'global-settings');
        add_settings_field(
            'global_show_pdf',
            'Show PDF in Submission Table',
            function() {
                $value = get_option('global_show_pdf', '0');
                ?>
                <label>
                    <input type="checkbox" name="global_show_pdf" value="1" <?php checked($value, '1'); ?> />
                    Enable showing PDF column in submission table
                </label>
                <?php
            },
            'global-settings',
            'section_pdf'
        );
    
        /**
         * NA FIELD POINTS
         */
        register_setting(
            'global_settings_group',
            'global_na_option',
            [
                'sanitize_callback' => function($input) {
                    $allowed = ['minus_field_points', 'do_nothing', 'minus_custom_points'];
                    return in_array($input, $allowed) ? $input : 'do_nothing';
                }
            ]
        );
    
        register_setting(
            'global_settings_group',
            'global_na_custom_points',
            [
                'sanitize_callback' => function($input) {
                    return is_numeric($input) ? intval($input) : 0;
                }
            ]
        );
 
    }
    

  
    public function add_custom_meta_boxes() {
        add_meta_box(
            'question_details',
            'Question Details',
            [$this, 'render_question_meta_box'],
            'question',
            'normal',
            'high'
        );
        add_meta_box(
            'form_description',
            'Form Description',
            function($post) {
                $desc = get_post_meta($post->ID, '_form_description', true);
                wp_nonce_field('save_form_meta', 'form_meta_nonce');
                echo '<textarea name="form_description" style="width:100%;height:100px;">' 
                     . esc_textarea($desc) . '</textarea>';
            },
            'questionnaire_form',
            'normal',
            'default'
        );
     

        add_meta_box(
            'form_steps',
            'Form Steps',
            [$this, 'render_form_meta_box'],
            'questionnaire_form',
            'normal',
            'default'
        );

        
    }

   

    public function render_question_meta_box($post) {
        $answer_type = get_post_meta($post->ID, '_answer_type', true);
        $points      = get_post_meta($post->ID, '_points', true);
        $is_required = get_post_meta($post->ID, '_is_required', true);
        $answers     = get_post_meta($post->ID, '_answers', true);
    
        if (!is_array($answers)) $answers = [];
    
        wp_nonce_field('save_question_meta', 'question_meta_nonce');
        ?>
    
        <p>
            <label><strong>Answer Type:</strong></label><br>
            <select name="answer_type" style="width:100%">
                <option value="radio" <?php selected($answer_type, 'radio'); ?>>Radio</option>
                <option value="checkbox" <?php selected($answer_type, 'checkbox'); ?>>Checkbox</option>
                <option value="dropdown" <?php selected($answer_type, 'dropdown'); ?>>Dropdown</option>
                <option value="text" <?php selected($answer_type, 'text'); ?>>Text Field</option>
                <option value="email" <?php selected($answer_type, 'email'); ?>>Email</option>
                <option value="textarea" <?php selected($answer_type, 'textarea'); ?>>Text Area</option>
                <option value="phone" <?php selected($answer_type, 'phone'); ?>>Phone Number</option>
            </select>
        </p>
    
        <p>
            <label><strong>Points (Weightage):</strong></label><br>
            <input type="number" name="points" value="<?php echo esc_attr($points); ?>" style="width:100%">
        </p>
    
        <p>
            <label>
                <input type="checkbox" name="is_required" value="1" <?php checked($is_required, 1); ?>> Required Question
            </label>
        </p>
    
        <hr>
    
        <h4>Answer Options</h4>
        <div id="answers-wrapper">
            <?php foreach ($answers as $index => $ans): ?>
                <div class="answer-row" style="margin-bottom:10px;padding:10px;border:1px solid #ddd;">
                    <input type="text" 
                           name="answers[<?php echo $index; ?>][text]" 
                           value="<?php echo esc_attr($ans['text']); ?>" 
                           placeholder="Answer Text" 
                           style="width:30%">
    
                    <input type="hidden" class="image-field" name="answers[<?php echo $index; ?>][image]" value="<?php echo esc_attr($ans['image']); ?>">
                    <button class="button upload-image">Upload Image</button>
                    <img src="<?php echo esc_url($ans['image']); ?>" style="max-width:50px; vertical-align:middle;">
    
                    <label style="margin-left:10px;">
                        <input type="checkbox" name="answers[<?php echo $index; ?>][is_na]" value="1" <?php checked(!empty($ans['is_na']), 1); ?>> Is N/A
                    </label>
    
                    <input type="number" 
                           name="answers[<?php echo $index; ?>][points]" 
                           value="<?php echo isset($ans['points']) ? esc_attr($ans['points']) : ''; ?>" 
                           placeholder="Points" 
                           style="width:80px; margin-left:10px;">
    
                    <button class="button remove-answer">Remove</button>
                </div>
            <?php endforeach; ?>
        </div>
    
        <p><button class="button" id="add-answer">+ Add Answer</button></p>
    <?php
    }
    
    public function save_question_meta($post_id) {
        if (!isset($_POST['question_meta_nonce']) || !wp_verify_nonce($_POST['question_meta_nonce'], 'save_question_meta')) {
            return;
        }
    
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    
        if (isset($_POST['answer_type'])) {
            update_post_meta($post_id, '_answer_type', sanitize_text_field($_POST['answer_type']));
        }
    
        if (isset($_POST['points'])) {
            update_post_meta($post_id, '_points', intval($_POST['points']));
        }
    
        // Save the required flag for the question
        update_post_meta($post_id, '_is_required', isset($_POST['is_required']) ? 1 : 0);
    
        if (isset($_POST['answers']) && is_array($_POST['answers'])) {
            $answers = [];
            foreach ($_POST['answers'] as $ans) {
                $answers[] = [
                    'text'   => sanitize_text_field($ans['text']),
                    'image'  => esc_url_raw($ans['image']),
                    'is_na'  => isset($ans['is_na']) ? 1 : 0,
                    'points' => isset($ans['points']) ? intval($ans['points']) : 0,
                ];
            }
            update_post_meta($post_id, '_answers', $answers);
        } else {
            delete_post_meta($post_id, '_answers');
        }
    }
    

        public function render_form_meta_box($post) {
            $has_pre_inputs       = get_post_meta($post->ID, '_has_pre_inputs', true);
            $pre_fields           = get_post_meta($post->ID, '_pre_fields', true) ?: [];
            $raw_steps            = get_post_meta($post->ID, '_form_steps', true) ?: [];
            $pre_inputs_position  = get_post_meta($post->ID, '_pre_inputs_position', true) ?: 'separate';
            $show_categories      = get_post_meta($post->ID, '_show_categories', true);
            $show_step_titles     = get_post_meta($post->ID, '_show_step_titles', true);

            $questions = get_posts([
                'post_type'   => 'question',
                'numberposts' => -1,
                'orderby'     => 'title',
                'order'       => 'ASC'
            ]);

            // Build steps array with title + questions
            $steps = [];
            if (empty($raw_steps)) {
                $steps = [1 => ['title'=>'','questions'=>[]]];
            } else {
                foreach ($raw_steps as $sn => $data) {
                    if (is_array($data)) {
                        $title = $data['title'] ?? '';
                        $questions_in_step = array_map('intval', (array) ($data['questions'] ?? []));
                        $steps[$sn] = [
                            'title'     => $title,
                            'questions' => $questions_in_step
                        ];
                    } else {
                        // backward compatibility
                        $steps[$sn] = [
                            'title'     => '',
                            'questions' => array_map('intval', (array) $data)
                        ];
                    }
                }
                ksort($steps, SORT_NUMERIC);
                if (empty($steps)) $steps = [1 => ['title'=>'','questions'=>[]]];
            }

            wp_nonce_field('save_form_meta', 'form_meta_nonce');

            echo '<label><input type="checkbox" id="has_pre_inputs" name="has_pre_inputs" value="1" '.checked($has_pre_inputs,1,false).' /> This form requires pre-input fields before questions</label><br><br>';
            echo '<label><input type="checkbox" id="show_categories" name="show_categories" value="1" '.checked($show_categories,1,false).' /> Show question categories as well </label><br><br>';
            echo '<label><input type="checkbox" id="show_step_titles" name="show_step_titles" value="1" '.checked($show_step_titles,1,false).' /> Show Steps Title </label><br><br>';

            // ------------------
            // Pre-inputs
            // ------------------
            echo '<div id="pre-inputs-wrapper-main" style="'.($has_pre_inputs ? '' : 'display:none;').'">';
            echo '<div id="pre-inputs-wrapper">';
            // echo '<p><strong>Display Options:</strong></p>';
            // echo '<label><input type="radio" name="pre_inputs_position" value="with_step1" '.checked($pre_inputs_position,'with_step1',false).' /> Show with Step 1 questions</label><br>';
            // echo '<label><input type="radio" name="pre_inputs_position" value="separate" '.checked($pre_inputs_position,'separate',false).' /> Show in separate step (Step 0)</label><br><br>';

            echo '<h4>Pre-Inputs</h4>';
            if (empty($pre_fields)) {
                $pre_fields[] = ['label'=>'','field_type'=>'text','required'=>0];
            }


            foreach ($pre_fields as $i => $f) {
                echo '<div class="pre-field" style="margin: 10px 0px 20px 0px;">';
                echo '<input type="text" name="pre_fields['.$i.'][label]" value="'.esc_attr($f['label']).'" placeholder="Field Label" style="margin: 0px 10px;" />';
                echo '<select name="pre_fields['.$i.'][field_type]" style="margin: 0px 10px;">';

                $types = [
                    'text'     => 'Text',
                    'email'    => 'Email',
                    'phone'    => 'Phone',
                    'textarea' => 'Textarea',
                    'date'     => 'Date',
                    'select'   => 'Select',
                    // 'radio'    => 'Radio',
                    'checkbox' => 'Checkbox'
                ];
                foreach ($types as $k=>$v) {
                    echo '<option value="'.$k.'" '.selected($f['field_type'],$k,false).'>'.$v.'</option>';
                }
                // echo '</select>';
                // echo '<label style="margin: 0px 10px;"><input type="checkbox" name="pre_fields['.$i.'][required]" value="1" '.checked($f['required'],1,false).' /> Required</label>';
                // echo '<button type="button" class="button remove-pre-field" style="margin: 0px 10px;">Remove</button>';
                // echo '</div>';
                echo '</select>';

                $layout = $f['layout'] ?? 'full';
                echo '<select name="pre_fields['.$i.'][layout]" style="margin: 0px 10px;">
                        <option value="full" '.selected($layout,'full',false).'>Full Width</option>
                        <option value="half" '.selected($layout,'half',false).'>Half Width</option>
                        <option value="combine" '.selected($layout,'combine',false).'>Combine Width</option>
                      </select>';

                echo '<label style="margin: 0px 10px;"><input type="checkbox" name="pre_fields['.$i.'][required]" value="1" '.checked($f['required'],1,false).' /> Required</label>';
                echo '<button type="button" class="button remove-pre-field" style="margin: 0px 10px;">Remove</button>';
            
                // Options textarea for select, radio, checkbox
                $options = isset($f['options']) ? implode("\n", (array)$f['options']) : '';
                echo '<div class="field-options" style="'.(in_array($f['field_type'],['select','radio','checkbox']) ? '' : 'display:none;').' margin:10px 0;">';
                echo '<textarea name="pre_fields['.$i.'][options]" placeholder="Enter options (one per line)" style="width:250px; height:80px;">'.esc_textarea($options).'</textarea>';
                echo '</div>';
            
                echo '</div>';
            }
            echo '</div>';
            echo '<button type="button" class="button add-pre-field"  style="margin-top: 13px;">+ Add Pre Field</button>';
            echo '</div>';

            // ------------------
            // Steps
            // ------------------
            // echo '<div id="form-steps-wrapper">';
            // foreach ($steps as $step_num => $stepData) {
            //     $stepTitle   = $stepData['title'] ?? '';
            //     $selectedIds = $stepData['questions'] ?? [];

            //     echo '<div class="form-step" data-step="'.intval($step_num).'">';
            //     echo '<h4>Step '.intval($step_num).'</h4>';
            //     echo '<input type="text" name="form_steps[' . intval($step_num) . '][title]" 
            //         value="' . esc_attr($stepTitle) . '" 
            //         placeholder="Step Title" 
            //         style="width:100%;margin-bottom:8px;" />';

            //     echo '<select class="form-step-select" name="form_steps['.intval($step_num).'][questions][]" multiple="multiple" style="width:100%;">';
            //     foreach ($questions as $q) {
            //         $already_chosen_elsewhere = false;
            //         foreach ($steps as $sn => $sdata) {
            //             if ($sn != $step_num && in_array($q->ID, $sdata['questions'], true)) {
            //                 $already_chosen_elsewhere = true;
            //                 break;
            //             }
            //         }
            //         if ($already_chosen_elsewhere) continue;

            //         $selected_attr = in_array($q->ID, $selectedIds, true) ? 'selected' : '';
            //         echo '<option value="'.intval($q->ID).'" '.$selected_attr.'>'.esc_html($q->post_title).'</option>';
            //     }
            //     echo '</select>';
            //     echo '</div>';
            // }
            // echo '</div>';
            // echo '<button type="button" class="button" id="add-step" style="margin-top: 13px;">+ Add Step</button>';
            // ------------------
// Steps
// ------------------
            echo '<div id="form-steps-wrapper">';
            foreach ($steps as $step_num => $stepData) {
                $stepTitle   = $stepData['title'] ?? '';
                $selectedIds = $stepData['questions'] ?? [];
                $stepDescription = $stepData['description'] ?? '';

                echo '<div class="form-step" data-step="'.intval($step_num).'">';
                echo '<h4>Step '.intval($step_num).'</h4>';
                echo '<input type="text" name="form_steps[' . intval($step_num) . '][title]" 
                    value="' . esc_attr($stepTitle) . '" 
                    placeholder="Step Title" 
                    style="width:100%;margin-bottom:8px;" />';
                     // Description
                echo '<textarea name="form_steps['.intval($step_num).'][description]" 
                placeholder="Step Description" 
                style="width:100%;height:60px;margin-bottom:8px;">'
                .esc_textarea($stepDescription).'</textarea>';

                // Action buttons
                echo '<div style="margin-bottom:6px;">';
                echo '<button type="button" class="button select-all">Select All</button> ';
                echo '<button type="button" class="button deselect-all">Clear</button>';
                echo '</div>';

                echo '<select class="form-step-select" name="form_steps['.intval($step_num).'][questions][]" multiple="multiple" style="width:100%;">';
                foreach ($questions as $q) {
                    $already_chosen_elsewhere = false;
                    foreach ($steps as $sn => $sdata) {
                        if ($sn != $step_num && in_array($q->ID, $sdata['questions'], true)) {
                            $already_chosen_elsewhere = true;
                            break;
                        }
                    }
                    if ($already_chosen_elsewhere) continue;

                    $selected_attr = in_array($q->ID, $selectedIds, true) ? 'selected' : '';
                    echo '<option value="'.intval($q->ID).'" '.$selected_attr.'>'.esc_html($q->post_title).'</option>';
                }
                echo '</select>';
                echo '</div>';
            }
            echo '</div>';
            echo '<button type="button" class="button" id="add-step" style="margin-top: 13px;">+ Add Step</button>';

        }


        // ------------------
        // Save Meta Box
        // ------------------
        public function save_form_meta($post_id) {
            if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
            if (! current_user_can('edit_post', $post_id)) return;
            if (! isset($_POST['form_meta_nonce']) || ! wp_verify_nonce($_POST['form_meta_nonce'], 'save_form_meta')) {
                return;
            }

            // Save description
            if (isset($_POST['form_description'])) {
                update_post_meta($post_id, '_form_description', sanitize_textarea_field($_POST['form_description']));
            }

            update_post_meta($post_id, '_has_pre_inputs', !empty($_POST['has_pre_inputs']) ? 1 : 0);
            update_post_meta($post_id, '_show_categories', !empty($_POST['show_categories']) ? 1 : 0);
            update_post_meta($post_id, '_show_step_titles', !empty($_POST['show_step_titles']) ? 1 : 0);

            if (!empty($_POST['pre_inputs_position'])) {
                update_post_meta($post_id, '_pre_inputs_position', sanitize_text_field($_POST['pre_inputs_position']));
            } else {
                delete_post_meta($post_id, '_pre_inputs_position');
            }

            if (!empty($_POST['pre_fields']) && is_array($_POST['pre_fields'])) {
                $fields = [];
                foreach ($_POST['pre_fields'] as $f) {
                    $options = [];
                    if (!empty($f['options'])) {
                        $lines = explode("\n", $f['options']);
                        $options = array_filter(array_map('trim', $lines));
                    }
        
                    $fields[] = [
                        'label'      => sanitize_text_field($f['label'] ?? ''),
                        'field_type' => sanitize_text_field($f['field_type'] ?? 'text'),
                        'required'   => !empty($f['required']) ? 1 : 0,
                        'layout'     => sanitize_text_field($f['layout'] ?? 'full'),
                        'options'    => $options
                    ];
                }
                update_post_meta($post_id, '_pre_fields', $fields);
            } else {
                delete_post_meta($post_id, '_pre_fields');
            }

            if (!empty($_POST['form_steps']) && is_array($_POST['form_steps'])) {
                $clean_steps = [];
                foreach ($_POST['form_steps'] as $step_num => $stepData) {
                    $clean_steps[$step_num] = [
                        'title'     => sanitize_text_field($stepData['title'] ?? ''),
                        'description' => sanitize_textarea_field($stepData['description'] ?? ''),
                        'questions' => array_map('intval', (array) ($stepData['questions'] ?? []))
                    ];
                }
                update_post_meta($post_id, '_form_steps', $clean_steps);
            } else {
                delete_post_meta($post_id, '_form_steps');
            }
        }


    public function add_shortcode_column($columns) {
        $new = [];
        foreach ($columns as $key => $value) {
            $new[$key] = $value;
            if ($key === 'title') {

                $new['shortcode'] = __('Shortcode');
            }
        }
        return $new;
    }

    public function render_shortcode_column($column, $post_id) {
        if ($column === 'shortcode') {
            echo '<code>[questionnaire_form id="' . $post_id . '"]</code>';
        }
    }
    
    public function add_custom_column($columns) {
        $new = [];
        foreach ($columns as $key => $value) {
            $new[$key] = $value;
            if ($key === 'title') {
                $new['weightage'] = __('Weightage');
            }
        }
        return $new;
    }

    public function render_custom_column($column, $post_id) {
        if ($column === 'weightage') {
            $weightage = get_post_meta($post_id, '_points', true);
            echo $weightage ? esc_html($weightage) : '<em>—</em>';
        }
    }

    public function render_submissions_page() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'questionnaire_entries';
        $entries = $wpdb->get_results("SELECT * FROM $table_name ORDER BY submitted_at DESC");

        $upload_dir = wp_upload_dir();

        echo '<div class="wrap">';
        echo '<h1>Questionnaire Submissions</h1>';

        if ($entries) {
            echo '<table class="widefat striped">';
            echo '<thead><tr><th>ID</th><th>Form ID</th><th>Total Points</th><th>Possible Points</th><th>Percentage</th><th>Medal</th><th>Submitted At</th><th>PDFs</th></tr></thead>';
            echo '<tbody>';
            
            foreach ($entries as $entry) {
                $pdf_links_html = '';
                $pdf_files = maybe_unserialize($entry->pdf_files);

                $form_data = maybe_unserialize($entry->form_data);

                // Extract only the relevant data (total_points, possible_points, percentage, medal)
                $total_points = isset($form_data['total_points']) ? $form_data['total_points'] : 0;
                $possible_points = isset($form_data['possible_points']) ? $form_data['possible_points'] : 0;
                $percentage = isset($form_data['percentage']) ? $form_data['percentage'] : 0;
                $medal = isset($form_data['medal']) ? $form_data['medal'] : 'None';
                $pdf_url = admin_url('admin-post.php?action=generate_pdf&type=admin&submission_id=' . $entry->submission_id);
                $pdf_url_user = admin_url('admin-post.php?action=generate_pdf&type=user&submission_id=' . $entry->submission_id);
                $pdf_links_html = '<a href="' . esc_url($pdf_url) . '" target="_blank">Generate PDF</a>';
                $pdf_links_html_user = '<a href="' . esc_url($pdf_url_user) . '" target="_blank">Generate User PDF</a>';
                // Determine color for the medal (Platinum, Gold, Silver, Bronze)
                $medal_color = '';
                switch ($medal) {
                    case 'Platinum':
                        $medal_color = 'style="color: #e5e4e2;"'; // Platinum (light gray)
                        break;
                    case 'Gold':
                        $medal_color = 'style="color: #FFD700;"'; // Gold (yellow)
                        break;
                    case 'Silver':
                        $medal_color = 'style="color: #C0C0C0;"'; // Silver (gray)
                        break;
                    case 'Bronze':
                        $medal_color = 'style="color: #cd7f32;"'; // Bronze (copper)
                        break;
                }

                // Only show rows with the required fields
                if ($entry) {
                    echo '<tr>';
                    echo '<td>' . esc_html($entry->submission_id) . '</td>';
                    echo '<td>' . esc_html($entry->form_id) . '</td>';
                    echo '<td>' . esc_html($total_points) . '</td>';
                    echo '<td>' . esc_html($possible_points) . '</td>';
                    echo '<td>' . esc_html($percentage) . '%</td>';
                    echo '<td ' . $medal_color . '>' . esc_html($medal) . '</td>';
                    echo '<td>' . esc_html($entry->submitted_at) . '</td>';
                    // echo '<td>' . $pdf_links_html . '</td>';
                    echo "<td>";                 
                    if(get_option('global_show_pdf')){
                        
                        echo  $pdf_links_html_user."<br>";
                    }
                    echo  $pdf_links_html ;
                    echo "</td>";
                    // echo '<td>' . $pdf_links_html . '</td>';
                    echo '</tr>';
                }
            }
            echo '</tbody>';
            echo '</table>';
        } else {
            echo '<p>No submissions found.</p>';
        }

        echo '</div>';
    }

    
}