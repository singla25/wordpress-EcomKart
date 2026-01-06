<?php

use Mpdf\Tag\Div;

if ( ! defined( 'ABSPATH' ) ) exit;

class Questionnaire_Front {

    public function __construct() {
        add_shortcode('questionnaire_form', [$this, 'render_frontend_form']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_frontend_assets']);
  
    }

    public function enqueue_frontend_assets() {
        wp_enqueue_script('qm-front', QUESTIONNAIRE_PLUGIN_URL.'assets/js/frontend-questionnaire.js',['jquery'],'1.8',true);
        wp_enqueue_style('qm-front', QUESTIONNAIRE_PLUGIN_URL.'assets/css/frontend-questionnaire.css',[],'2');
        wp_enqueue_script('sweetalert2', 'https://cdn.jsdelivr.net/npm/sweetalert2@11', [], null, true);
        $nonce = wp_create_nonce('submit_questionnaire_nonce');
        wp_localize_script('qm-front', 'qm_ajax', [
            'ajaxurl' => admin_url('admin-ajax.php'),
            'security' => $nonce, 
        ]);
        
    }

    public function render_frontend_form($atts) {
        $atts = shortcode_atts(['id' => 0], $atts, 'questionnaire_form');
        $form_id = intval($atts['id']);
        $_form_description   = get_post_meta($form_id, '_form_description', true) ?: '';
        $_has_pre_inputs     = get_post_meta($form_id, '_has_pre_inputs', true) ?: false;
        $_pre_inputs_position= get_post_meta($form_id, '_pre_inputs_position', true) ?: '';
        $_pre_fields         = get_post_meta($form_id, '_pre_fields', true) ?: [];
        $_form_steps         = get_post_meta($form_id, '_form_steps', true) ?: [];
        $show_categories     = get_post_meta($form_id, '_show_categories', true) ? true : false;
        $show_step_titles    = get_post_meta($form_id, '_show_step_titles', true) ? true : false;
    
        $steps_data = [];
    
        // Add pre-inputs
        if ($_has_pre_inputs) {
           
            $steps_data[] = [
                'main_data'   => $_pre_fields,
                'is_pre_field'=> 1,
                'skip_prev'   => true,
                'step_title'  => '',
                'step_description'  => '',
                'show_step_titles' => false,
                'show_categories'  => false,
                'is_summary'       => false,
            ];
        }
    
        // Normal steps
        foreach ($_form_steps as $_form_step) {
            $step_title = $_form_step['title'] ?? '';
            $step_description = $_form_step['description'] ?? '';
    
            $step_data = [];
            foreach ($_form_step['questions'] as $post_id) {
                $_answer_type = get_post_meta($post_id, '_answer_type', true) ?: 'text';
                $_answers     = get_post_meta($post_id, '_answers', true) ?: [];
                $_is_required = get_post_meta($post_id, '_is_required', true) ?: false;
                $title        = get_the_title($post_id);
    
                // Category if enabled
                $category_label = '';
                $category_id    = '';
                if ($show_categories) {
                    $cats = wp_get_post_terms($post_id, 'question_category');
                    if (!is_wp_error($cats) && !empty($cats)) {
                        $category_label = $cats[0]->name;  // only first category name
                        $category_id    = $cats[0]->term_id;// only first category name
                    }
                }
    
                $step_data[] = [
                    "post_id"      => $post_id,
                    "label"        => $title,
                    "category_name"     => $category_label,
                    "category"     => $category_id,
                    "field_type"   => $_answer_type,
                    'required'     => $_is_required,
                    "anwer_option" => $_answers,
                ];
            }
    
            if (!empty($step_data)) {   
                $steps_data[] = [
                    'main_data'       => $step_data,
                    'step_title'      => $step_title,
                    'step_description'  => $step_description,
                    'show_step_titles'=> $show_step_titles,
                    'show_categories' => $show_categories,
                    'is_pre_field'    => 0,
                    'skip_prev'       => false,
                    'is_summary'      => false,
                ];
            }
        }
        $steps_data[] = [
            'main_data'       => [],
            'step_title'      => 'Summary',
            'step_description'  => '',
            'show_step_titles'=> true,
            'show_categories' => false,
            'is_pre_field'    => 0,
            'skip_prev'       => false,
            'is_summary'      => true,
        ];
    
        ob_start();
        ?>
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-11 col-sm-10 col-md-10 col-lg-6 col-xl-5 text-center p-0 mt-3 mb-2">
                    <div class="card px-0 pt-4 pb-0 mt-3 mb-3">
                        
                        <form id="questionnaire_form" data-formid="<?= esc_attr($form_id) ?>">
                            <?php
                            foreach ($steps_data as $i => $step) {
                                $is_last  = $i === array_key_last($steps_data);
                                $is_first = $i === 0;
                                echo $this->form_steps_html(
                                    $step['main_data'],
                                    $is_last,
                                    $is_first,
                                    $step['skip_prev'],
                                    $step['is_pre_field'],
                                    $step['step_title'],
                                    $step['step_description'],
                                    $step['show_step_titles'],
                                    $step['show_categories'],
                                    $step['is_summary']
                                );
                            }
                            ?>
                        </form>
                        <div id="thankyou-container" style="display: none;"></div>
                    </div>
                </div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
    
    function form_steps_html($fields, $is_last = false, $is_first = false, $skip_prev = false , $is_pre_field = 0, $step_title = '',$step_description = '', $show_step_titles = false, $show_categories = false , $is_summary = false) {
        if( $is_summary == true){
            echo $this->is_last_step($fields);
            return;
        }
        ?>
        <fieldset data-skip-prev="<?= $skip_prev ? '1' : '0' ?>">
        
        <div class="pg-header">
            <div class="pg-header-left">
            <h2>Experience Rating System</h2>
            <p>A tool to evaluate and improve facility design and user experience.</p>
            </div>
            <div class="pg-header-right">
            <img src="<?php echo QUESTIONNAIRE_PLUGIN_URL . 'assets/image/Five-at-Heart-Logo.jpg'; ?>" />
            </div>
        </div>
            <div class="form-card <?php echo !empty($is_pre_field) ? 'prefield-main' : ''; ?>">
                <?php if ($show_step_titles && !empty($step_title)): ?>
                    <h3   class="step-title"><?= esc_html($step_title) ?></h3>
                    <p style="display: none;" class="step-discriptions"><?= esc_html($step_description) ?></p>
                <?php endif; ?>
    
                <?php 
                // ✅ Group questions by category
                $grouped_fields = [];
                foreach ($fields as $field) {
                    $cat = $field['category_name'] ?? 'Uncategorized';
                    if (!isset($grouped_fields[$cat])) {
                        $grouped_fields[$cat] = [];
                    }
                    $grouped_fields[$cat][] = $field;
                }
    
                foreach ($grouped_fields as $category_label => $questions_group):
                    if ($show_categories && $category_label !== 'Uncategorized') {
                        echo '<div class="question-category">'. esc_html($category_label) .'</div>';
                    }
    
                    foreach ($questions_group as $field): 
                        $filed_extra_class = "";
                        if ( ! empty( $field['layout'] ) ) {
                            $layout = sanitize_title( $field['layout'] );
                        
                            if ( $layout === 'half' ) {
                                $filed_extra_class = 'add-half-width';
                            } elseif ( $layout === 'combine' ) {
                                $filed_extra_class = 'add-combine-width';
                            } else {
                                $filed_extra_class = 'add-full-width';
                            }
                        } else {
                            $filed_extra_class = 'add-full-width';
                        }
                        
                   
                    $label    = esc_html($field['label'] ?? '');
                    $type     = esc_attr($field['field_type'] ?? 'text');
                    $name     = str_replace('-', '_', sanitize_title($label));
                    $required = !empty($field['required']) ? 'required' : '';
                    $options  = $field['anwer_option'] ?? [];
                    $category_name = $field['category_name'] ?? '';
                    $category = $field['category'] ?? '';
                    $qu_id  =  $field['post_id'] ?? '';
                    $is_pre_field_class = $is_pre_field ? '' : 'question-title';
                    $has_images_in_field  = !empty($field['anwer_option']) && $this->check_has_images($field['anwer_option']);
                    $has_featured_image = false;
                    $featured_image_url = '';

                    if ( $qu_id && has_post_thumbnail( $qu_id ) ) {
                        $has_featured_image = true;
                        $featured_image_url = get_the_post_thumbnail_url( $qu_id, 'full' );
                    }
                ?>
                      <div class="field-warp-main <?php echo !empty($has_featured_image) ? 'q-with-image' : ''; ?> <?php echo !empty($filed_extra_class) ? $filed_extra_class : ''; ?>">
                 
    
                        <label class="fieldlabels <?= esc_attr($is_pre_field_class); ?>" ><?= $label ?></label>
                        <div class="field-warp-input <?= $has_images_in_field ? 'radio-group-container' : ''; ?>">
                            <?php

                                switch ($type) {

                                    case 'radio':
                                case 'checkbox':
                                    if (!empty($field['anwer_option'])) {
                                        foreach ($field['anwer_option'] as $index => $option) {
                                     
                                            $opt_text  = esc_html($option['text']);
                                            $opt_value = sanitize_title($option['text']);
                                            $opt_img   = !empty($option['image']) ? esc_url($option['image']) : '';
                                            $points    = intval($option['points'] ?? 0);
                                            $is_na     = intval($option['is_na'] ?? 0);
                                            $extra_class = !empty($opt_img) ? ' has_image' : '';
                                            $extra_class_lable = !empty($opt_img) ? ' radio-list' : 'custom-radio';
                                            $input_id = $index . '_' . $name ."_".$category;
                                            

                                            if ($opt_img) {
                                                echo '<label class="form-check '. $extra_class .'">';
                                                // echo '<div class="'.$extra_class_lable.'">';
                                                echo '<input type="'. $type .'" 
                                                id="'. $input_id .'"
                                                        name="'. $name . ($type == 'checkbox' ? '[]' : '') .'" 
                                                        value="'. $opt_value .'" 
                                                        data-points="'. $points .'" 
                                                        data-is-na="'. $is_na .'" 
                                                        data-is-pre_field="'. $is_pre_field .'" 
                                                        data-id="'. $qu_id .'" 
                                                        data-category="'. $category .'" 
                                                        '. $required .' />';
                                                echo '<div class="radio-group-content">';
                                                echo '<img src="'. $opt_img .'" alt="'. $opt_text .'" class="option-image" /> ';
                                                echo '<span>'. $opt_text .'</span></div>';
                                                echo '</label>'; // closes .form-check ✅ FIXED
                                            } else {
                                                echo '<div class="form-check '. $extra_class .'">';
                                                echo '<div class="'.$extra_class_lable.'">';
                                                echo '<input type="'. $type .'" 
                                                        name="'. $name . ($type == 'checkbox' ? '[]' : '') .'" 
                                                        id="'. $input_id .'"
                                                        value="'. $opt_value .'" 
                                                        data-points="'. $points .'" 
                                                        data-is-na="'. $is_na .'" 
                                                        data-is-pre_field="'. $is_pre_field .'" 
                                                        data-id="'. $qu_id .'" 
                                                        data-category="'. $category .'" 
                                                        '. $required .' />';
                                                echo '<label class="custom-radio-label" for="'. $input_id .'"><span>'. $opt_text .'</span></label>';
                                                echo '</div>'; // closes $extra_class_lable
                                                echo '</div>'; // closes $extra_class_lable
                                                
                                            }
                                
                                            // echo '</div>'; // closes $extra_class_lable
                                            
                                        
                                        }
                                    }else if (!empty($field['options'])) {
                                        foreach ($field['options'] as $option_n) {
                                        echo '<div class="form-check">';
                                        echo '<input type="'. $type .'"  '. $required .'   name="'. $name . ($type == 'checkbox' ? '[]' : '') .'" >';
                                        echo '<label><span>';
                                        echo $option_n .'></span></label';
                                        echo '</div>';
                                        }

                                    }
                                    break;
                                    case 'dropdown':
                                    case 'select':
                                        if (!empty($field['anwer_option'])) {
                                            echo '<select name="'. $name .'" '. $required .'>';
                                            foreach ($field['anwer_option'] as $option) {
                                                // $qu_id  = esc_html($option['post_id']);
                                                $opt_text  = esc_html($option['text']);
                                                $opt_value = sanitize_title($option['text']);
                                                $opt_img   = !empty($option['image']) ? esc_url($option['image']) : '';
                                                $points    = intval($option['points'] ?? 0);
                                                $is_na     = intval($option['is_na'] ?? 0);

                                                echo '<option value="'. $opt_value .'" 
                                                            data-points="'. $points .'" 
                                                            data-is-na="'. $is_na .'" 
                                                            data-is-pre_field="'. $is_pre_field .'" 
                                                            data-id="'. $qu_id .'" 
                                                            data-category="'. $category .'" 
                                                            data-image="'. $opt_img .'">'
                                                        . $opt_text .
                                                    '</option>';
                                            }
                                            echo '</select>';
                                        }else if (!empty($field['options'])) {
                                                
                                                    echo '<select name="'. $label .'"  data-is-pre_field="'. $is_pre_field .'"  '. $required .'>';
                                                    foreach ($field['options'] as $option) {

                                                        echo '<option value="'. $option .'" data-is-pre_field="'. $is_pre_field .'" >'
                                                                . $option .
                                                            '</option>';
                                                    }
                                                    echo '</select>';
                                                
                                            
                                        }
                                    break;
                                    case 'textarea':
                                        if (!empty($field['options'])) {
                                            foreach ($options as $option) {
                                                // $qu_id  = intval($option['post_id']?? 0);
                                                $opt_text  = esc_html($option['text']);
                                                $opt_value = sanitize_title($option['text']);
                                                $points    = intval($option['points'] ?? 0);
                                                $is_na     = intval($option['is_na'] ?? 0);
                                                echo '<label>'. $opt_text .'</label>';
                                                echo '<textarea name="'. $name .'['.$opt_value.'][value]" '. $required .'  data-points="'. $points .'" data-is-na="'. $is_na .'"  data-is-pre_field="'. $is_pre_field .'" data-id="'. $qu_id .'"  data-category="'. $category .'"  ></textarea>';

                                            }
                                        } else {

                                            echo '<textarea name="'. $name .'" '.$required.' data-is-pre_field="'. esc_attr($is_pre_field) .'"  ></textarea>';
                                        }
                                        break;

                                    case 'email':
                                        if (!empty($options)) {
                                            foreach ($options as $option) {
                                                // $qu_id  = esc_html($option['post_id']);
                                                $opt_text  = esc_html($option['text']);
                                                $opt_value = sanitize_title($option['text']);
                                                $points    = intval($option['points'] ?? 0);
                                                $is_na     = intval($option['is_na'] ?? 0);
                                                echo '<label>'. $opt_text .'</label>';
                                                echo '<input type="email" name="'. $name .'['.$opt_value.'][value]" '. $required .'  data-points="'. $points .'" data-is-na="'. $is_na .'"  data-is-pre_field="'. $is_pre_field .'" data-id="'. $qu_id .'"  data-category="'. $category .'"   />';

                                            }
                                        } else {
                                            echo '<input type="email" name="'. $name .'" '. $required .' data-is-pre_field="'. $is_pre_field .'" />';
                                        }
                                        break;

                                    case 'phone':
                                        if (!empty($options)) {
                                            foreach ($options as $option) {
                                                // $qu_id  = esc_html($option['post_id']);
                                                $opt_text  = esc_html($option['text']);
                                                $opt_value = sanitize_title($option['text']);
                                                $points    = intval($option['points'] ?? 0);
                                                $is_na     = intval($option['is_na'] ?? 0);
                                                echo '<label>'. $opt_text .'</label>';
                                                echo '<input type="tel" name="'. $name .'['.$opt_value.'][value]" pattern="[0-9]{10}" placeholder="Enter phone number" '. $required .'  data-points="'. $points .'" data-is-na="'. $is_na .'"  data-is-pre_field="'. $is_pre_field .'" data-id="'. $qu_id .'"  data-category="'. $category .'"  />';
                                            }
                                        } else {
                                            echo '<input type="tel" name="'. $name .'" pattern="[0-9]{10}" placeholder="Enter phone number" '. $required .' data-is-pre_field="'. $is_pre_field .'" />';
                                        }
                                        break;
                                    case 'date':
                                        if (!empty($options)) {
                                            foreach ($options as $option) {
                                                // $qu_id  = esc_html($option['post_id']);
                                                $opt_text  = esc_html($option['text']);
                                                $opt_value = sanitize_title($option['text']);
                                                $points    = intval($option['points'] ?? 0);
                                                $is_na     = intval($option['is_na'] ?? 0);
                                                echo '<label>'. $opt_text .'</label>';
                                                echo '<input type="date" pattern="\d{4}-\d{2}-\d{2}" name="'. $name .'['.$opt_value.'][value]"  '. $required .'  data-points="'. $points .'" data-is-na="'. $is_na .'"  data-is-pre_field="'. $is_pre_field .'" data-id="'. $qu_id .'"  data-category="'. $category .'"  />';
                                            }
                                        } else {
                                            echo '<input type="date" pattern="\d{4}-\d{2}-\d{2}" name="'. $name .'"   '. $required .' data-is-pre_field="'. $is_pre_field .'" />';
                                        }
                                        break;
                                    case 'text':
                                    default:
                                        if (!empty($options)) {
                                            foreach ($options as $option) {
                                                // $qu_id  = esc_html($option['post_id']);
                                                $opt_text  = esc_html($option['text']);
                                                $opt_value = sanitize_title($option['text']);
                                                $points    = intval($option['points'] ?? 0);
                                                $is_na     = intval($option['is_na'] ?? 0);
                                                echo '<label>'. $opt_text .'</label>';
                                                echo '<input type="text" name="'. $name .'['.$opt_value.'][value]" '. $required .' data-points="'. $points .'" data-is-na="'. $is_na .'"  data-is-pre_field="'. $is_pre_field .'" data-id="'. $qu_id .'"   data-category="'. $category .'"  />';

                                            }
                                        } else {
                                            echo '<input type="text" name="'. $name .'" '. $required .' data-is-pre_field="'. $is_pre_field .'" />';
                                        }
                                        break;




                                }
                            ?>
                        </div>
                        <div class="feature-image-wrap-right">
                        <?php if ($has_featured_image): ?>
                                <img src="<?= esc_url($featured_image_url) ?>" alt="<?= esc_attr($label) ?>" class="option-image" />
                            <?php endif; ?>
                            </div>
                    </div>
                   <?php endforeach; // end field ?>
            <?php endforeach; // end category group ?>
            </div> 
            <?php if(!$skip_prev){ ?>
             
            <div class="step-results">
            <div  class="step-points-main"><strong>Step Points:</strong><span class="step-points">0</span></div>
            <div class="step-percentage-main"><strong>Step Percentage:</strong><span class="step-percentage">0%</span></div>
            </div>
            <?php  } ?>
            <div class="button-group footer-btn">
                <?php if (!$is_first): ?>
                    <input type="button" name="previous" class="previous action-button" value="Previous" />
                <?php endif; ?>
                <?php if (!$is_last): ?>
                    <input type="button" name="next" class="next action-button" value="Next" />
                <?php else: ?>
                    <input type="button" name="submit" class="submit action-button" value="Submit" />
                <?php endif; ?>
            </div>
        </fieldset>
        <?php
    }

    function check_has_images($options) {
        if (!empty($options) && is_array($options)) {
            foreach ($options as $option) {
                if (!empty($option['image'])) {
                    return true; 
                }
            }
        }
        return false; 
    }

    function is_last_step($fields) {
        ob_start(); ?>
        <fieldset class="summary-step" style="display:none;">
            <div class="form-card">
                <h3 class="step-title">Summary</h3>
    
                <div class="summary-section">
                    <label>Overall total score</label>
                    <input type="text" name="overall_score"  class="overall-points" id="overall_score" readonly />
    
                    <label>Overall Achieved Percentage (%)</label>
                    <input type="text" name="overall_percentage" id="overall_percentage" readonly />
    
                    <label>Rank Achieved</label>
                    <input type="text" name="rank_achieved" id="rank_achieved" readonly />
                </div>
    

                <div class="button-group footer-btn">
                    <input type="button" name="previous" class="previous action-button" value="Previous" />
                    <input type="submit" name="final_submit" class="submit action-button" value="Submit" />
                </div>
            </div>
        </fieldset>
        <?php
        return ob_get_clean();
    }
    
}


