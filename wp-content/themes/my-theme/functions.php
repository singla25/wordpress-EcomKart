<?php
/**
 * Theme Functions
 *
 * @package myTheme
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Enqueue Styles & Scripts
 */
function myTheme_enqueue_assets() {
    // Google Fonts
    wp_enqueue_style(
        'mytheme-fonts',
        'https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&family=Roboto:wght@400;500&display=swap',
        [],   // An array of handles of other stylesheets that this stylesheet depends on
        null
    );

    // Bootstrap CSS
    wp_enqueue_style(
        'bootstrap-css',
        'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css',
        [],
        '5.3.3'
    );

    // Main Theme Stylesheet
    wp_enqueue_style(
        'theme-style',
        get_stylesheet_uri(),
        ['bootstrap-css'],
        wp_get_theme()->get( 'Version' )
    );

    // Custom CSS - header, footer
    wp_enqueue_style(
        'mytheme-header-footer',
        get_template_directory_uri() . '/assets/css/header-footer.css', // adjust path if needed
        [],
        filemtime( get_template_directory() . '/assets/css/header-footer.css' ) // versioning for cache-busting
    );

    // Custom CSS - Pages
    wp_enqueue_style(
        'mytheme-pages',
        get_template_directory_uri() . '/assets/css/style-pages.css',
        ['theme-style'], 
        filemtime( get_template_directory() . '/assets/css/style-pages.css' )
    );

    // Custom JS (depends on jQuery)
    wp_enqueue_script(
        'ajax-script',
        get_template_directory_uri() . '/assets/js/ajax-script.js',
        array('jquery'),
        filemtime(get_template_directory() . '/assets/js/ajax-script.js'),
        true
    );
    
    // Pass ajax URL + nonce to JS
    wp_localize_script('ajax-script', 'ajax_ajax', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('ajax_nonce'),
        'home_url'  => home_url('/'),
    ]);
    
    // Bootstrap JS (includes Popper)
    wp_enqueue_script(
        'bootstrap-js',
        'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js',
        ['jquery'], // An array of handles of other stylesheets that this stylesheet depends on
        '5.3.3',
        true
    );
}
add_action( 'wp_enqueue_scripts', 'myTheme_enqueue_assets' );


/**
 * Ajax Function - Action
 */
function mytheme_filter_data() {
    // Security check
    check_ajax_referer('ajax_nonce', 'nonce');
    
    $post_type = $_POST['ptype'];
    $search = $_POST['search'];
    $category = $_POST['category'];
    $tag = $_POST['tag'];
    $vendor = $_POST['vendor'];
    
    // $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
    
    // Base query
    $args = [
        'post_type'      => $post_type,
        // 'posts_per_page' => 9,
        // 'paged'          => $paged,
    ];

    if (!empty($search)) {
        $args['s'] = $search;
    }

    // Tax Query
    $tax_query = array('relation' => 'AND');

    if (!empty($category)) {
        $tax_query[] = array(
            'taxonomy' => get_current_attrname($post_type, 'category'),
            'field'    => 'slug',
            'terms'    =>  $category,
            'operator' => 'AND',

        );
    }

    if (!empty($tag)) {
        $tax_query[] = array(
            'taxonomy' => get_current_attrname($post_type, 'tag'),
            'field'    => 'slug',
            'terms'    => $tag,
            'operator' => 'AND',
        );
    }
  
    if (count($tax_query) > 1) {
        $args['tax_query'] = $tax_query;
    }

     // Vendor filter applied here
    if (!empty($vendor)) {
        $args['author__in'] = $vendor; 
    }

    $query = new WP_Query($args);
            
    ob_start();
    ?>
            
    <div class="row g-4">
        <h2 class="text-center"><?php echo ucfirst($post_type); ?></h2>
        <?php
        if ( $query->have_posts() ) :
            while ( $query->have_posts() ) : $query->the_post(); ?>
                <div class="col-md-3">
                    <div class="card h-100 border-0 shadow-sm">
                        <?php if ( has_post_thumbnail() ) : ?>
                            <?php the_post_thumbnail('medium', ['class' => 'card-img-top rounded', 'style' => 'height:150px; object-fit:cover;']); ?>
                        <?php endif; ?>
                        <div class="card-body">
                            <h5 class="card-title fw-bold">
                                <a href="<?php the_permalink(); ?>" class="text-dark text-decoration-none"><?php the_title(); ?></a>
                            </h5>
                            <p class="card-text small text-muted"><?php echo wp_trim_words(get_the_excerpt(), 12); ?></p>
                            <a href="<?php the_permalink(); ?>" class="btn btn-outline-dark btn-sm">View Car</a>
                        </div>
                    </div>
                </div>
            <?php endwhile; 
            wp_reset_postdata();
        else : ?>
            <p class="text-center">No data available yet.</p>
        <?php endif; ?>
    </div>
    
    <?php
    $output = $output = ob_get_clean();

    wp_send_json([
        'status'  => 'success',
        'html' => $output,
    ]);
    die;
}
add_action('wp_ajax_filter', 'mytheme_filter_data');        // when request comes from logged-in user
add_action('wp_ajax_nopriv_filter', 'mytheme_filter_data'); // when an AJAX request comes from a logged-out user.

function mytheme_addBook_vendor() {
    check_ajax_referer('ajax_nonce', 'nonce');

    $booktitle       = sanitize_text_field($_POST['booktitle']);
    $booktagline     = sanitize_text_field($_POST['booktagline']);
    $bookdescription = wp_kses_post($_POST['bookdescription']);
    $bookprice       = floatval($_POST['bookprice']);

    $new_book = array(
        'post_title'   => $booktitle,
        'post_content' => $bookdescription,
        'post_status'  => 'publish',
        'post_type'    => 'books',
        'post_author'  => get_current_user_id(),
    );

    $book_id = wp_insert_post($new_book);

    if (is_wp_error($book_id)) {
        wp_send_json_error(['message' => 'Error creating book: ' . $book_id->get_error_message()]);
    }

    require_once(ABSPATH . 'wp-admin/includes/file.php');
    require_once(ABSPATH . 'wp-admin/includes/image.php');

    // Handle feature image
    if (!empty($_FILES['featureimage']['name'])) {
        $upload_overrides = ['test_form' => false];

        $movefile = wp_handle_upload($_FILES['featureimage'], $upload_overrides);

        if ($movefile && !isset($movefile['error'])) {
            $filename  = $movefile['file'];
            $filetype  = wp_check_filetype(basename($filename), null);

            $attachment = array(
                'post_mime_type' => $filetype['type'],
                'post_title'     => sanitize_file_name(basename($filename)),
                'post_content'   => '',
                'post_status'    => 'inherit'
            );

            $attach_id = wp_insert_attachment($attachment, $filename, $book_id);
            $attach_data = wp_generate_attachment_metadata($attach_id, $filename);
            wp_update_attachment_metadata($attach_id, $attach_data);

            set_post_thumbnail($book_id, $attach_id);
        }
    }

    // Handle multiple gallery uploads
    if (!empty($_FILES['bookimage']['name'][0])) {
        $upload_overrides = ['test_form' => false];
        $uploaded_urls    = [];

        foreach ($_FILES['bookimage']['name'] as $key => $value) {
            if ($_FILES['bookimage']['name'][$key]) {
                $file = [
                    'name'     => $_FILES['bookimage']['name'][$key],
                    'type'     => $_FILES['bookimage']['type'][$key],
                    'tmp_name' => $_FILES['bookimage']['tmp_name'][$key],
                    'error'    => $_FILES['bookimage']['error'][$key],
                    'size'     => $_FILES['bookimage']['size'][$key],
                ];

                $movefile = wp_handle_upload($file, $upload_overrides);

                if ($movefile && !isset($movefile['error'])) {
                    $uploaded_urls[] = esc_url_raw($movefile['url']);
                }
            }
        }

        if (!empty($uploaded_urls)) {
            update_post_meta($book_id, 'book_images', $uploaded_urls);
        }
    }

    update_post_meta($book_id, 'bookprice', $bookprice);
    update_post_meta($book_id, 'booktagline', $booktagline);

    // Send success with redirect URL
    wp_send_json_success([
        'message' => '✅ Book added successfully!',
        'url'     => home_url('/vendor/?tab=books')
    ]);
}
add_action('wp_ajax_addBook_vendor', 'mytheme_addBook_vendor');
add_action('wp_ajax_nopriv_addBook_vendor', 'mytheme_addBook_vendor');

function mytheme_edit_book() {

    check_ajax_referer('ajax_nonce', 'nonce');

    $bookid = intval($_REQUEST['id']);
    $book = get_post($bookid);

    if (!$book) {
        wp_send_json_error(['message' => 'Book not found.']);
    }

    $booktitle       = $book->post_title;
    $bookdescription = $book->post_content;
    $booktagline     = get_post_meta($bookid, 'booktagline', true);
    $bookprice       = get_post_meta($bookid, 'bookprice', true);
    $images          = get_post_meta($bookid, 'book_images', true);

    ob_start(); // start buffering output
    ?>
    <form id="edit-form" method="post" enctype="multipart/form-data" 
          class="p-4 shadow-lg rounded-3 bg-secondary bg-opacity-75 text-white">
        <div class="row g-3">
            <div class="col-md-6">
                <label for="editedbooktitle" class="form-label fw-bold">📕 Book Title</label>
                <input id="editedbooktitle" type="text" name="editedbooktitle" class="form-control"
                       value="<?php echo esc_attr($booktitle); ?>" required>
            </div>

            <div class="col-md-6">
                <label for="editedbooktagline" class="form-label fw-bold">✨ Book Tagline</label>
                <input id="editedbooktagline" type="text" name="editedbooktagline" class="form-control"
                       value="<?php echo esc_attr($booktagline); ?>" required>
            </div>

            <div class="col-md-6">
                <label for="editedfeatureimage" class="form-label fw-bold">🖼️ Feature Image</label>
                <input id="editedfeatureimage" type="file" name="editedfeatureimage" class="form-control">

                <?php 
                $current_thumbnail_id = get_post_thumbnail_id($bookid);
                if ($current_thumbnail_id) :
                    $current_thumbnail_url = wp_get_attachment_url($current_thumbnail_id);
                ?>
                    <div class="mt-2">
                        <img src="<?php echo esc_url($current_thumbnail_url); ?>" 
                             alt="Feature Image" 
                             class="img-thumbnail" 
                             style="width:80px; height:80px; object-fit:cover;">
                    </div>
                <?php else: ?>
                    <p class="text-muted mt-2">No feature image set yet.</p>
                <?php endif; ?>
            </div>

            <div class="col-md-6">
                <label for="editedbookimage" class="form-label fw-bold">🖼️ Upload Photo</label>
                <input id="editedbookimage" type="file" name="editedbookimage[]" class="form-control" multiple>

                <?php if (!empty($images) && is_array($images)): ?>
                    <div class="mt-2 d-flex flex-wrap gap-2">
                        <?php foreach ($images as $img): ?>
                            <div class="position-relative" style="width:80px;">
                                <img src="<?php echo esc_url($img); ?>" 
                                     alt="Book Image" 
                                     class="img-thumbnail" 
                                     style="width:80px; height:80px; object-fit:cover;">
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="text-muted mt-2">No images uploaded yet.</p>
                <?php endif; ?>
            </div>

            <div class="col-md-6">
                <label for="editedbookprice" class="form-label fw-bold">💰 Price</label>
                <input id="editedbookprice" type="number" name="editedbookprice" class="form-control"
                       value="<?php echo esc_attr($bookprice); ?>" min="0" required>
            </div>

            <div class="col-12">
                <label for="editedbookdescription" class="form-label fw-bold">📝 Description</label>
                <textarea id="editedbookdescription" name="editedbookdescription" class="form-control"
                          rows="4" required><?php echo esc_textarea($bookdescription); ?></textarea>
            </div>
        </div>

        <input type="hidden" name="bookid" value="<?php echo esc_attr($bookid); ?>">

        <div class="d-grid mt-4">
            <button type="submit" name="vendor-edit-book" class="btn btn-success btn-lg">✅ Save Book</button>
        </div>
    </form>
    <?php

    $output = ob_get_clean(); // get buffered HTML and clear buffer

    wp_send_json([
        'status' => 'success',
        'html'   => $output,
    ]);
}
add_action('wp_ajax_edit_book', 'mytheme_edit_book');
add_action('wp_ajax_nopriv_edit_book', 'mytheme_edit_book');

function mytheme_editBook_vendor() {
    check_ajax_referer('ajax_nonce', 'nonce');

    $bookid        = intval($_POST['bookid']);
    $existingbook  = get_post($bookid);
    $bookpublisher = get_current_user_id();

    if ($existingbook && $existingbook->post_author == $bookpublisher && $existingbook->post_type == 'books') {
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/image.php');

        $booktitle       = sanitize_text_field($_POST['editedbooktitle']);
        $booktagline     = sanitize_text_field($_POST['editedbooktagline']);
        $bookdescription = wp_kses_post($_POST['editedbookdescription']);
        $bookprice       = floatval($_POST['editedbookprice']);

        // ✅ Handle gallery images
        if (!empty($_FILES['editedbookimage']['name'][0])) {
            $upload_overrides = ['test_form' => false];
            $uploaded_urls    = [];

            foreach ($_FILES['editedbookimage']['name'] as $key => $value) {
                $file = [
                    'name'     => $_FILES['editedbookimage']['name'][$key],
                    'type'     => $_FILES['editedbookimage']['type'][$key],
                    'tmp_name' => $_FILES['editedbookimage']['tmp_name'][$key],
                    'error'    => $_FILES['editedbookimage']['error'][$key],
                    'size'     => $_FILES['editedbookimage']['size'][$key]
                ];

                $movefile = wp_handle_upload($file, $upload_overrides);

                if ($movefile && !isset($movefile['error'])) {
                    $uploaded_urls[] = esc_url_raw($movefile['url']);
                }
            }

            if (!empty($uploaded_urls)) {
                update_post_meta($bookid, 'book_images', $uploaded_urls);
            }
        }

        // ✅ Handle feature image
        if (!empty($_FILES['editedfeatureimage']['name'])) {
            $upload_overrides = ['test_form' => false];
            $movefile = wp_handle_upload($_FILES['editedfeatureimage'], $upload_overrides);

            if ($movefile && !isset($movefile['error'])) {
                $filename  = $movefile['file'];
                $filetype  = wp_check_filetype(basename($filename), null);

                $attachment = [
                    'post_mime_type' => $filetype['type'],
                    'post_title'     => sanitize_file_name(basename($filename)),
                    'post_status'    => 'inherit'
                ];

                $attach_id   = wp_insert_attachment($attachment, $filename, $bookid);
                $attach_data = wp_generate_attachment_metadata($attach_id, $filename);
                wp_update_attachment_metadata($attach_id, $attach_data);

                set_post_thumbnail($bookid, $attach_id);
            }
        }

        // ✅ Update post
        $updated_book = [
            'ID'           => $bookid,
            'post_title'   => $booktitle,
            'post_content' => $bookdescription,
        ];

        $book_updated_detail = wp_update_post($updated_book);

        if (is_wp_error($book_updated_detail)) {
            wp_send_json_error(['message' => $book_updated_detail->get_error_message()]);
        } else {
            update_post_meta($bookid, 'bookprice', $bookprice);
            update_post_meta($bookid, 'booktagline', $booktagline);

            wp_send_json_success(['message' => '✅ Book updated successfully!', 'url' => home_url('/vendor')]);
        }
    } else {
        wp_send_json_error(['message' => '❌ You do not have permission to edit this book.']);
    }
}
add_action('wp_ajax_editBook_vendor', 'mytheme_editBook_vendor');
add_action('wp_ajax_nopriv_editBook_vendor', 'mytheme_editBook_vendor');

function mytheme_delete_book() {
    check_ajax_referer('ajax_nonce', 'nonce');

    $bookpublisher = get_current_user_id();
    $bookid = intval($_POST['vendor-delete-book']);
    $existingbook = get_post($bookid);

    if ($existingbook && $existingbook->post_author == $bookpublisher && $existingbook->post_type == 'books') {
        
        $delete = wp_delete_post($bookid, true);

        if ($delete) {
            wp_send_json_success([
                'message' => '✅ Book deleted successfully!',
                'url'     => home_url('/vendor/?tab=books')
            ]);
        } else {
            wp_send_json_error([
                'message' => '❌ Failed to delete the book. Please try again.'
            ]);
        }
    } else {
        wp_send_json_error([
            'message' => '❌ You do not have permission to delete this book or it does not exist.'
        ]);
    }
}
add_action('wp_ajax_delete_book', 'mytheme_delete_book');
add_action('wp_ajax_nopriv_delete_book', 'mytheme_delete_book');

// function mytheme_contact_form() {

//     check_ajax_referer('ajax_nonce', 'nonce');

//     $name = $_POST['name'];
//     $email = $_POST['email'];
//     $phone = $_POST['phone'];
//     $subject = $_POST['subject'];
//     $topic = $_POST['topic'];
//     $query = $_POST['query'];

//     $user_id = is_user_logged_in() ? get_current_user_id() : 0;

//     $commentdata = [
//         'comment_post_ID'      => 151,             // connect thsese with contact page
//         'comment_author'       => $name,
//         'comment_author_email' => $email,
//         'comment_date'         => current_time('mysql'),          // Local time
//         'comment_date_gmt'     => current_time('mysql', 1),       // GMT time
//         'comment_content'      => $subject,
//         'comment_type'         => 'contact_form',
//         'user_id'              => $user_id,          // Set logged-in user ID or 0
//     ];

//     $comment_id = wp_insert_comment($commentdata);

//     if ($comment_id) {
//         add_comment_meta($comment_id, 'phone', $phone);
//         add_comment_meta($comment_id, 'topic', $topic);
//         add_comment_meta($comment_id, 'query', $query);

//         wp_send_json_success([
//             'message' => 'Your form has been submitted. Our team will contact you soon!',
//         ]);
//     } else {
//         wp_send_json_error([
//             'message' => 'Failed to save form details.',
//         ]);
//     }
// }
// add_action('wp_ajax_contact_form', 'mytheme_contact_form');
// add_action('wp_ajax_nopriv_contact_form', 'mytheme_contact_form');


/**
 * Theme Setup
 */
function myTheme_setup() {
    // Title Tag
    add_theme_support( 'title-tag' );

    // Featured Images
    add_theme_support( 'post-thumbnails' );

    // Custom Logo
    add_theme_support( 'custom-logo', [
        'height'      => 100,
        'width'       => 400,
        'flex-height' => true,
        'flex-width'  => true,
    ] );

    // Navigation Menus
    register_nav_menus( [
        'primary' => __( 'Primary Menu', 'myTheme' ),
        'footer'  => __( 'Footer Menu', 'myTheme' ),
    ] );
}
add_action( 'after_setup_theme', 'myTheme_setup' );


/**
 * Widgets
 */
function myTheme_widgets() {
    // Feature Cards (Front Page)
    register_sidebar( [
        'name'          => __( 'Feature Cards Section', 'myTheme' ),
        'id'            => 'feature-cards',
        'description'   => __( 'Add widgets to display in the Feature Cards section', 'myTheme' ),
        'before_widget' => '<div class="col-md-3"><div class="card bg-dark bg-opacity-75 text-light shadow-sm h-100 p-4 text-center">',
        'after_widget'  => '</div></div>',
        'before_title'  => '<h5 class="fw-bold">',
        'after_title'   => '</h5>',
    ] );

    // Footer Widgets
    for ( $i = 1; $i <= 3; $i++ ) {
        register_sidebar( [
            'name'          => sprintf( __( 'Footer Column %d', 'myTheme' ), $i ),
            'id'            => 'footer-' . $i,
            'description'   => __( 'Add widgets for footer column ', 'myTheme' ) . $i,
            'before_widget' => '<div id="%1$s" class="footer-widget %2$s">',
            'after_widget'  => '</div>',
            'before_title'  => '<h3 class="footer-widget-title">',
            'after_title'   => '</h3>',
        ] );
    }
}
add_action( 'widgets_init', 'myTheme_widgets' );


/**
 * Custom Post Types
 */
function myTheme_custom_posts() {
    // Books CPT
    register_post_type( 'books', [
            'labels' => [
                'name'               => __( 'Books', 'myTheme' ),
                'singular_name'      => __( 'Book', 'myTheme' ),
                'add_new'            => __( 'Add New Book', 'myTheme' ),
                'add_new_item'       => __( 'Add New Book', 'myTheme' ),
                'edit_item'          => __( 'Edit Book', 'myTheme' ),
                'new_item'           => __( 'New Book', 'myTheme' ),
                'all_items'          => __( 'All Books', 'myTheme' ),
                'view_item'          => __( 'View Book', 'myTheme' ),
                'search_items'       => __( 'Search Books', 'myTheme' ),
                'not_found'          => __( 'No Books found', 'myTheme' ),
                'not_found_in_trash' => __( 'No Books found in Trash', 'myTheme' ),
                'menu_name'          => __( 'Books', 'myTheme' ),
            ],
            'public'      => true,
            'has_archive' => true,
            'show_in_nav_menus' => true,
            'menu_icon'   => 'dashicons-book',
            'supports'    => array('title', 'editor', 'thumbnail', 'excerpt'),
    ] );

    // Cars CPT
    register_post_type( 'cars', [
            'labels' => [
                'name'               => __( 'Cars', 'myTheme' ),
                'singular_name'      => __( 'Car', 'myTheme' ),
                'add_new'            => __( 'Add New Car', 'myTheme' ),
                'add_new_item'       => __( 'Add New Car', 'myTheme' ),
                'edit_item'          => __( 'Edit Car', 'myTheme' ),
                'new_item'           => __( 'New Car', 'myTheme' ),
                'all_items'          => __( 'All Cars', 'myTheme' ),
                'view_item'          => __( 'View Car', 'myTheme' ),
                'search_items'       => __( 'Search Cars', 'myTheme' ),
                'not_found'          => __( 'No Cars found', 'myTheme' ),
                'not_found_in_trash' => __( 'No Cars found in Trash', 'myTheme' ),
                'menu_name'          => __( 'Cars', 'myTheme' ),
            ],
            'public'      => true,
            'has_archive' => true,
            'show_in_nav_menus' => true,
            'menu_icon'   => 'dashicons-car', 
            'supports'    => array('title', 'editor', 'thumbnail'),
    ] );

    // Mobiles CPT
    register_post_type( 'mobiles', [
            'labels' => [
                'name'               => __( 'Mobile', 'myTheme' ),
                'singular_name'      => __( 'Mobile', 'myTheme' ),
                'add_new'            => __( 'Add New Mobile', 'myTheme' ),
                'add_new_item'       => __( 'Add New Mobile', 'myTheme' ),
                'edit_item'          => __( 'Edit Mobile', 'myTheme' ),
                'new_item'           => __( 'New Mobile', 'myTheme' ),
                'all_items'          => __( 'All Mobiles', 'myTheme' ),
                'view_item'          => __( 'View Mobile', 'myTheme' ),
                'search_items'       => __( 'Search Mobiles', 'myTheme' ),
                'not_found'          => __( 'No Mobiles found', 'myTheme' ),
                'not_found_in_trash' => __( 'No Mobiles found in Trash', 'myTheme' ),
                'menu_name'          => __( 'Mobiles', 'myTheme' ),
            ],
            'public'      => true,
            'has_archive' => true,
            'show_in_nav_menus' => true,
            'menu_icon'   => 'dashicons-smartphone',
            'supports'    => array('title', 'editor', 'thumbnail', 'excerpt'),
    ] );

    // Tabs CPT
    register_post_type( 'tabs', [
            'labels' => [
                'name'               => __( 'Tabs', 'myTheme' ),
                'singular_name'      => __( 'Tab', 'myTheme' ),
                'add_new'            => __( 'Add New Tab', 'myTheme' ),
                'add_new_item'       => __( 'Add New Tab', 'myTheme' ),
                'edit_item'          => __( 'Edit Tab', 'myTheme' ),
                'new_item'           => __( 'New Tab', 'myTheme' ),
                'all_items'          => __( 'All Tabs', 'myTheme' ),
                'view_item'          => __( 'View Tab', 'myTheme' ),
                'search_items'       => __( 'Search Tabs', 'myTheme' ),
                'not_found'          => __( 'No Tabs found', 'myTheme' ),
                'not_found_in_trash' => __( 'No Tabs found in Trash', 'myTheme' ),
                'menu_name'          => __( 'Tabs', 'myTheme' ),
            ],
            'public'      => true,
            'has_archive' => true,
            'show_in_nav_menus' => true,
            'menu_icon'   => 'dashicons-tablet', 
            'supports'    => array('title', 'editor', 'thumbnail'),
    ] );
        register_taxonomy( 'tab-feature', 'tabs', [
        'hierarchical' => true,
        'labels'       => [ 
            'name'          => __( 'Features', 'myTheme' ),
            'singular_name' => __( 'feature', 'myTheme' ),
        ],
        'show_ui'           => true,
        'show_admin_column' => true,
        'rewrite'           => [ 'slug' => 'tab-feature' ],
    ] );
}
add_action( 'init', 'myTheme_custom_posts' );


/**
 * Taxonomies
 */

function myTheme_taxonomy() {

    // register_taxonomy( 'slug', 'post_type', [$args] );

    // ----------  Books  ----------
    // hierarchical like categories - Genre
    register_taxonomy( 'book-genre', 'books', [
        'hierarchical' => true,
        'labels'       => [ 
            'name' => __( 'Genres', 'myTheme' ),
            'singular_name' => __('Genre', 'myTheme'),
        ],
        'show_ui'      => true,
        'show_admin_column' => true,
        'rewrite'      => [ 'slug' => 'book-genre' ],
    ] );

    // non-hierarchical like tags - Book Author
    register_taxonomy( 'book-author', 'books', [
        'hierarchical' => false,
        'labels'       => [ 
            'name' => __( 'Authors', 'myTheme' ),
            'singular_name' => __('Author', 'myTheme'),
         ],
        'show_ui'      => true,
        'show_admin_column' => true,
        'rewrite'      => [ 'slug' => 'book-author' ],
    ] );

    // ----------  Cars  ----------
    // hierarchical like categories - feature
    register_taxonomy( 'car-feature', 'cars', [
        'hierarchical' => true,
        'labels'       => [ 
            'name'          => __( 'Features', 'myTheme' ),
            'singular_name' => __( 'Feature', 'myTheme' ),
        ],
        'show_ui'           => true,
        'show_admin_column' => true,
        'rewrite'           => [ 'slug' => 'car-feature' ],
    ] );

    // non-hierarchical like tags - company
    register_taxonomy( 'car-company', 'cars', [
        'hierarchical' => false,
        'labels'       => [ 
            'name'          => __( 'Companies', 'myTheme' ),
            'singular_name' => __( 'Company', 'myTheme' ),
        ],
        'show_ui'           => true,
        'show_admin_column' => true,
        'rewrite'           => [ 'slug' => 'car-company' ],
    ] );

    // ----------  Mobile  ----------
    // hierarchical like categories - features
    register_taxonomy( 'mobile-feature', 'mobiles', [  
        'hierarchical' => true,
        'labels'       => [ 
            'name'          => __( 'Features', 'myTheme' ),
            'singular_name' => __( 'feature', 'myTheme' ),
        ],
        'show_ui'           => true,
        'show_admin_column' => true,
        'rewrite'           => [ 'slug' => 'mobile-feature' ],
    ] );

    // non-hierarchical like tags - price
    register_taxonomy( 'mobile-price', 'mobiles', [   
        'hierarchical' => false,
        'labels'       => [ 
            'name'          => __( 'Prices', 'myTheme' ),
            'singular_name' => __( 'Price', 'myTheme' ),
        ],
        'show_ui'           => true,
        'show_admin_column' => true,
        'rewrite'           => [ 'slug' => 'mobile-price' ],
    ] );

    // ----------  Tab  ----------
    // hierarchical like categories - features


    // non-hierarchical like tags - price
    register_taxonomy( 'tab-price', 'tabs' , [  
        'hierarchical' => false,
        'labels'       => [ 
            'name'          => __( 'Prices', 'myTheme' ),
            'singular_name' => __( 'Price', 'myTheme' ),
        ],
        'show_ui'           => true,
        'show_admin_column' => true,
        'rewrite'           => [ 'slug' => 'tab-price' ],
    ] );   
}
add_action( 'init', 'myTheme_taxonomy' );


/**
 * Shortcodes
 */
// Latest Blog Posts
function get_blog_post_fuction() {
    ob_start(); ?>
    <section class="bg-dark bg-opacity-25 p-5 shadow-sm">
        <div class="container">
            <h2 class="text-center mb-5">📰 Latest Posts</h2>
            <div class="row">
                <?php
                $latest = new WP_Query( [ 'posts_per_page' => 3 ] );
                if ( $latest->have_posts() ) :
                    while ( $latest->have_posts() ) : $latest->the_post(); ?>
                        <div class="col-md-4 mb-4">
                            <div class="card h-100 shadow-lg border-0 rounded-3 bg-secondary text-light">
                                <?php if ( has_post_thumbnail() ) : ?>
                                    <a href="<?php the_permalink(); ?>" >
                                        <?php the_post_thumbnail( 'medium', [ 'class' => 'card-img-top' ] ); ?>
                                    </a>
                                <?php endif; ?>
                                <div class="card-body">
                                    <h5 class="card-title text-center">
                                        <a href="<?php the_permalink(); ?>" class="text-dark text-decoration-none"><?php the_title(); ?></a>
                                    </h5>
                                    <p class="card-text">
                                        <?php echo wp_trim_words( get_the_content(), 15, '...' ); ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    <?php endwhile;
                    wp_reset_postdata();
                endif; ?>
            </div>
        </div>
    </section>
    <?php
    return ob_get_clean();
}
add_shortcode( 'get_blog_posts', 'get_blog_post_fuction' );

// Newsletter Subscribe
function get_subscribe_newsletter_function() {
    ob_start(); ?>
    <section class="newsletter py-5 bg-dark bg-opacity-25 shadow-sm text-center">
        <div class="container">
            <h2 class="fw-bold mb-3">📩 Subscribe to Our Newsletter</h2>
            <p class="mb-4">Stay updated with the latest articles & offers.</p>

            <!-- Newsletter Form -->
            <form class="newsletter-form d-flex justify-content-center">
                <div class="input-group w-50">
                    <input type="email" class="form-control" placeholder="Enter your email" required>
                    <button type="submit" class="btn btn-primary">Subscribe</button>
                </div>
            </form>
        </div>
    </section>
    <?php
    return ob_get_clean();
}
add_shortcode( 'get_subscribe_newsletter', 'get_subscribe_newsletter_function' );




/**
 * Filter Form Shortcode (used for building filter UI)
 */
function custom_post_filters_shortcode($atts) {

    $atts = shortcode_atts(array(
        'post_type'  => 'post',
        'taxonomies' => '',
        'posts_per_page' => 9,
    ), $atts, 'post_filters');

    global $wp;
    $current_url = home_url( add_query_arg( array(), $wp->request ) );

    $ptype = isset($_GET['ptype']) ? sanitize_text_field($_GET['ptype']) : '';

    $post_type = $atts['post_type'];

    $taxonomies = get_object_taxonomies( $post_type );

    ob_start();
    ?>

    <form method="get" id="filter-form" class="filter-form mb-4 p-3 shadow-lg border-0 rounded-3 bg-light">

        <h5 class="mb-3 text-center">Filter by</h5>
        <a href="<?php echo esc_url( $current_url ) ; ?>" class="text-danger text-decoration-none d-block mb-3 text-end"> 
            Clear All 
        </a>

        <input type="hidden" name="ptype" value="<?php echo esc_attr($post_type); ?>">

        <!-- Search Filter -->
        <div class="mb-3">
            <?php 
            $search = "";
            if(isset($_GET['search']) && $post_type == $ptype) {
                $search = esc_attr($_GET['search']);
            }
            ?>
            <input type="text" name="search" value="<?php echo $search; ?>" class="form-control" placeholder="Search">
            <?php 
            // show validation error under search box
            if (isset($_GET['search']) && strlen(sanitize_text_field($_GET['search'])) < 3 && $_GET['search'] !== '') {
                echo '<small class="text-danger">Please enter at least 3 characters.</small>';
            }
            ?>
        </div>
            
        <?php foreach ($taxonomies as $taxonomy): ?>
            <?php 
                // fetch terms of $taxonomy
                $terms = get_terms(array(
                    'taxonomy'   => $taxonomy,
                    'hide_empty' => false,
                ));

                if (!empty($terms) && !is_wp_error($terms)) { 
                    // Decide URL parameter name
                    $name = ($taxonomy == "mobile-price" || $taxonomy == "tab-price" || $taxonomy == "car-company" || $taxonomy == "book-author") ? "tag" : "category";

                    // Custom Function - original taxonomy of this postType to get selected terms
                    $current_taxonomy = get_current_attrname($post_type, $name);

                    // Get Names of terms from URL
                    $selected_terms =  [];
                    // This prevents mixing terms from unrelated taxonomies.
                    if($taxonomy === $current_taxonomy) {
                        $selected_terms = isset($_GET[$name]) ? (array) $_GET[$name] : []; 
                    }
            ?>
                    <div class="mb-3">
                        <strong class="d-block mb-2"><?php echo esc_html($taxonomy); ?></strong>
                                    
                        <?php foreach ($terms as $term): 
                            $checked = '';
                            if($post_type == $ptype ){
                                $checked = (isset($selected_terms) && in_array($term->slug, $selected_terms)) ? 'checked' : '';
                            }
                        ?>
                        <div class="form-check">
                            <!-- in this input we give name[] = category/tag and value = slug -->
                            <input class="form-check-input" type="checkbox" name="<?php echo esc_attr($name); ?>[]" 
                            value="<?php echo esc_attr($term->slug); ?>" 
                            id="<?php echo esc_attr($taxonomy.'-'.$term->term_id); ?>" <?php echo $checked; ?>>
                            
                            <label class="form-check-label"><?php echo esc_html($term->name); ?></label>
                        </div>
                        <?php endforeach; ?>
                    </div>
            <?php } ?>
        <?php endforeach; ?>

        <?php 
        // Only show vendor filter if post_type = books
        if ($post_type === 'books') {
            $args = [
                'post_type'      => 'books',
                'post_status'    => 'publish',
                'posts_per_page' => -1, // return ALL posts
            ];

            $books = get_posts($args);

            $vendors = []; // store unique Vendor IDs

            if ($books) {
                foreach ($books as $book) {
                    $vendor_id = $book->post_author;
                    $vendors[$vendor_id] = $vendor_id; // $vendors[3] = 3 store both key and id (in array [ 3 => 3, 5 => 5])
                }

                if (!empty($vendors)) {
                    echo '<div class="mb-3">';
                    echo '<strong class="d-block mb-2">Vendors</strong>';

                    // get selected vendors from URL
                    $selected_vendors = isset($_GET['vendor']) ? (array) $_GET['vendor'] : [];

                    foreach ($vendors as $vendor_id) {
                        $vendor_name = get_the_author_meta('display_name', $vendor_id);

                        // encode vendor id (so URL doesn’t expose raw user_id)
                        $encodedVendorId = base64_encode($vendor_id);

                        // check if selected
                        $checked = in_array($encodedVendorId, $selected_vendors) ? 'checked' : '';

                        echo '<div class="form-check">';
                        echo '<input class="form-check-input" type="checkbox" 
                                    name="vendor[]" 
                                    value="'.esc_attr($vendor_id).'" 
                                    id="vendor-'.esc_attr($vendor_id).'" '.$checked.'>';
                        echo '<label class="form-check-label" for="vendor-'.esc_attr($vendor_id).'">'
                                    .esc_html($vendor_name).'</label>';
                        echo '</div>';
                    }
                    echo '</div>';
                }
            }
        }
        ?>

        <button type="submit" class="btn btn-success mt-2" id="filter-button">
            Apply Filters
        </button>
    </form> 
<?php
    return ob_get_clean();
}
add_shortcode('post_filters', 'custom_post_filters_shortcode');


/* ========================================================
   🔴 OLD NON-AJAX POST RESULTS SHORTCODE (COMMENTED OUT)
   ========================================================

function custom_post_results_shortcode($atts) {
    
    $atts = shortcode_atts(array(
        'post_type'  => 'post',
        'taxonomies' => '',
        'per_page'   => 9,
    ), $atts, 'post_results');

    $post_type = $atts['post_type'];

    $taxonomies = array_map('trim', explode(',', $atts['taxonomies']));

    $search = isset($_GET['search']) ? sanitize_text_field($_GET['search']) : '';

    $ptype = isset($_GET['ptype']) ? sanitize_text_field($_GET['ptype']) : '';

    $vendors = [];
    if (!empty($_GET['vendor'])) {
        foreach ((array) $_GET['vendor'] as $vendor) {
            $vendors[] = intval(base64_decode($vendor));
        }
    }

    $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
    
    $args = array(
        'post_type'      => $ptype,
        'posts_per_page' => intval($atts['per_page']),
        'paged'          => $paged,
    );

    if (!empty($search)) {
        $args['s'] = $search;
    }

    // Tax Query
    $tax_query = array('relation' => 'AND');

    if (!empty($_GET['category'])) {
        $tax_query[] = array(
            'taxonomy' => get_current_attrname($ptype, 'category'),
            'field'    => 'slug',
            'terms'    => (array) $_GET['category'],
            'operator' => 'AND',

        );
    }

    if (!empty($_GET['tag'])) {
        $tax_query[] = array(
            'taxonomy' => get_current_attrname($ptype, 'tag'),
            'field'    => 'slug',
            'terms'    => (array) $_GET['tag'],
            'operator' => 'AND',
        );
    }
   
    $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
  
    if (count($tax_query) > 1) {
        $args['tax_query'] = $tax_query;
    }

     // Vendor filter applied here
    if (!empty($vendors)) {
        $args['author__in'] = $vendors; 
    }

    if($ptype != $post_type ){
         $args = array(
            'post_type'      => $post_type,
            'posts_per_page' => intval($atts['per_page']),
            'paged'          => $paged,
        );
    }

    $query = new WP_Query($args);

    ob_start();
    ?>

    <div id="results-container"></div>


    <h2 class="text-center mb-4"><?php echo ucfirst($atts['post_type']); ?></h2>

    <div class="row g-4">
        <?php if ($query->have_posts()): ?>
            <?php while ($query->have_posts()): 
                $query->the_post(); ?>
                <div class="col-md-4">
                    <div class="card h-100 shadow-lg border-0 rounded-3 bg-secondary">
                        <?php if (has_post_thumbnail()): ?>
                            <?php the_post_thumbnail('medium', ['class' => 'card-img-top']); ?>
                        <?php endif; ?>
                        <div class="card-body">
                            <h5 class="card-title text-center">
                                <a href="<?php the_permalink(); ?>" class="text-white text-decoration-none"><?php the_title(); ?></a>
                            </h5>
                            <p class="card-text"><?php echo wp_trim_words(get_the_excerpt(), 20); ?></p>
                            <a href="<?php the_permalink(); ?>" class="btn btn-outline-light btn-sm d-flex justify-content-center mt-2">Read More</a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="text-center">No <?php echo ucfirst($atts['post_type']); ?> found.</p>
        <?php endif; ?>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        <?php
        echo paginate_links(array(
            'total'   => $query->max_num_pages,
            'current' => $paged,
        ));
        ?>
    </div>

    <?php
    wp_reset_postdata();
    return ob_get_clean();
}
add_shortcode('post_results', 'custom_post_results_shortcode');

*/
function custom_post_results_shortcode($atts) {
    $atts = shortcode_atts(array(
        'post_type' => 'post', // default
    ), $atts, 'post_results');

    // make container ID unique to post_type
    $container_id = 'results-container-' . sanitize_html_class($atts['post_type']);

    ob_start(); ?>
        <div id="<?php echo $container_id; ?>" class="results-container"></div>
    <?php
    return ob_get_clean();
}
add_shortcode('post_results', 'custom_post_results_shortcode');

// Custom function to map attributes
function get_current_attrname($postType ,$attribute){ 
    if ($postType == "books"){ 
        return $attribute == "category" ? "book-genre" : (($attribute == "tag") ? "book-author" : ''); 
    } else if ($postType == "cars") {
        return $attribute == "category" ? "car-feature" : (($attribute == "tag") ? "car-company" : ''); 
    } else if ($postType == "tabs"){ 
        return ($attribute == "category") ? "tab-feature" : (($attribute == "tag") ? "tab-price" : '');     
    } else if ($postType == "mobiles"){ 
        return ($attribute == "category") ? "mobile-feature" : (($attribute == "tag") ? "mobile-price" : ''); 
    }
}


/**
 * Add Custom Columns in CPT in Admin Dashboard
 */
// Add new column
function add_vendor_column($columns) {
    $new = [];
    foreach ($columns as $key => $value) {
        $new[$key] = $value;
        if ($key === 'title') { // Insert Vendor column after Title
            $new['vendor'] = __('Vendor');
        }
    }
    return $new;
}
add_filter('manage_books_posts_columns', 'add_vendor_column');

// Render column content
function render_vendor_column($column, $post_id) {
    if ($column === 'vendor') {
        $vendor_id   = get_post_field('post_author', $post_id);
        $vendor_name = get_the_author_meta('display_name', $vendor_id);

        echo esc_html($vendor_name ? $vendor_name : '—');
    }
}
add_action('manage_books_posts_custom_column', 'render_vendor_column', 10, 2);


/**
 * Create custom vendor role
 */
function create_vendor_role() {
    // add_role( $role, $display_name, $capabilities );
    add_role(
        'vendor', // Slug
        'Vendor', // Display Name
        array(
            'read' => true,
            'upload_files' => true,
            'edit_posts' => true,
        )
    );

    if (current_user_can('vendor')) {       // gave him no access to dashboard
        show_admin_bar(false);
    }
}
add_action('init', 'create_vendor_role');


/**
 * Sign Up, LogIn and LogOut
 */
// SignUp Form
function signup_form() {
    $errors = []; // Array to store errors

    if ( isset($_POST['signup']) && $_POST['signup'] == 1 ) {

        $username        = sanitize_user($_POST['username']);
        $name            = sanitize_text_field($_POST['name']);
        $email           = $_POST['email'];
        $password        = $_POST['password'];
        $confirmpassword = $_POST['confirmpassword'];
        $age             = $_POST['age'];
        $phone           = $_POST['phone'];
        $role            = sanitize_text_field($_POST['role']); 

        // Password confirmation check
        if ( $password !== $confirmpassword ) {
            $errors['password'] = 'Confirm Password does not match Password.';
        }

        // Username check
        if ( username_exists($username) ) {
            $errors['username'] = 'Username already taken. Please choose another one.';
        }

        // Email check
        if ( email_exists($email) ) {
            $errors['email'] = 'Email already registered. Please login!';
        }

        // If no errors, create user
        if ( empty($errors) ) {
            $new_user = [
                'user_login'    => $username,
                'user_nicename' => $name,
                'user_email'    => $email,
                'user_pass'     => $password,
                'role'          => $role,
            ];

            $user_detail = wp_insert_user($new_user);

            if ( is_wp_error($user_detail) ) {
                $errors['general'] = $user_detail->get_error_message();
            } else {
                // Save extra fields
                update_user_meta($user_detail, 'age', $age);
                update_user_meta($user_detail, 'phone', $phone);

                // Prepare login credentials
                $credentials = [
                    'user_login'    => $username,
                    'user_password' => $password,
                ];

                // Log the user in
                $user = wp_authenticate( $username, $password );

                if ( !is_wp_error($user) ) {
                    $user = wp_signon($credentials, false);
                    wp_redirect(home_url('')); // redirect after login
                    exit;
                } else {
                    echo 'SignUp failed: ' . $user->get_error_message();
                }
            }
        }
    }
    return $errors;
}
add_action('init', 'signup_form');

// Login Form
function login_form() {
    if(isset($_POST['login']) && $_POST['login'] == 1) {
        
        $username = sanitize_user($_POST['username']);
        $password = $_POST['password'];
        // $remember   = isset($_POST['rememberme']) ? true : false;

        $user = wp_authenticate( $username, $password );  
          
        $credentials = array(
            'user_login'    => $username,
            'user_password' => $password,
            // 'remember'      => $remember 
        );

        if ( is_wp_error($user) ) {
            echo 'Login failed: ' . $user->get_error_message();
        } else {
            $user = wp_signon( $credentials, false ); 
            wp_redirect(home_url(''));
            exit;
        }
    }
}
add_action('init', 'login_form');

// Logout Function
function logout_form() {
    if ( isset($_GET['logout']) && $_GET['logout'] === 'logout' ) {
        wp_logout();
        wp_redirect( home_url('/') ); 
        exit;
    }
}
add_action('init', 'logout_form');


/**
 * Question and Query Form
 */
// Short Code - Question Form
function question_form_shortcode( $atts ) {
    global $post;

    // Default shortcode attributes
    $atts = shortcode_atts( array(
        'post_id'   => $post->ID,
        'post_type' => get_post_type( $post->ID ),
    ), $atts, 'question_form' );

    ob_start(); ?>
    
    <div class="p-4 border rounded shadow-sm bg-light">
        <h3 class="mb-4 text-center text-success fw-bold">💬 Any Question?</h3>
        
        <form method="post">
            <input type="hidden" name="question-post-id" value="<?php echo esc_attr( $atts['post_id'] ); ?>">
            <input type="hidden" name="question-post-type" value="<?php echo esc_attr( $atts['post_type'] ); ?>">

            <!-- User Info -->
            <div class="row mb-3 g-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Name</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Email</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Phone Number</label>
                    <input type="text" name="phone" class="form-control" required>
                </div>
            </div>

            <!-- Question -->
            <div class="mb-3">
                <label class="form-label fw-semibold">Your Question</label>
                <textarea name="question" class="form-control" rows="3" required></textarea>
            </div>

            <!-- Submit -->
            <div class="text-center">
                <button type="submit" name="submit-question" class="btn btn-success px-5 py-2 fw-semibold rounded-pill shadow-sm">
                    Submit Question
                </button>
            </div>
        </form>
    </div>

    <?php 
    return ob_get_clean();
}
add_shortcode( 'question_form', 'question_form_shortcode' );

// Question Form Backend Handler
function question_form_backend() {
    if ( isset($_POST['submit-question']) && !empty($_POST['question-post-id']) ) {

        $postid   = intval($_POST['question-post-id']);
        $posttype = sanitize_text_field($_POST['question-post-type']);

        $question = array(
            'name'     => sanitize_text_field($_POST['name']),
            'email'    => $_POST['email'],
            'phone'    => sanitize_text_field($_POST['phone']),
            'question' => sanitize_textarea_field($_POST['question']),
            'post_type'=> $posttype,
            'date'     => current_time('mysql'),
        );

        $existing_questions = get_post_meta($postid, 'questions', true);

        if ( !is_array($existing_questions) ) {
            $existing_questions = array();
        }

        // Append new question
        $existing_questions[] = $question;

        // Update meta
        update_post_meta($postid, 'questions', $existing_questions);

        // Redirect to prevent resubmission
        wp_redirect( get_permalink($postid) . '?submitted=1' );
        exit;
    }
}
add_action('init', 'question_form_backend');


/**
 * Vendor Dashboard
 */
// Add, Edit and Delete a Book
function vendor_book() {
    // Add a Book
    // if (isset($_POST['vendor-add-book']) && $_POST['vendor-add-book'] == 1) {
    
    //     $booktitle       = sanitize_text_field($_POST['booktitle']);
    //     $booktagline     = sanitize_text_field($_POST['booktagline']);
    //     $bookdescription = wp_kses_post($_POST['bookdescription']);
    //     $bookprice       = floatval($_POST['bookprice']); 

    //     // Create the book post
    //     $new_book = array(
    //         'post_title'   => $booktitle,
    //         'post_content' => $bookdescription,
    //         'post_status'  => 'publish',
    //         'post_type'    => 'books',
    //         'post_author'  => get_current_user_id(),
    //     );

    //     $book_id = wp_insert_post($new_book);

    //     if (is_wp_error($book_id)) {
    //         wp_die('Error creating book: ' . $book_id->get_error_message());
    //     }

    //     // ✅ Handle feature image
    //     if (!empty($_FILES['featureimage']['name'])) {
    //         require_once(ABSPATH . 'wp-admin/includes/file.php');
    //         require_once(ABSPATH . 'wp-admin/includes/image.php');

    //         $upload_overrides = array('test_form' => false);

    //         $movefile = wp_handle_upload($_FILES['featureimage'], $upload_overrides);

    //         if ($movefile && !isset($movefile['error'])) {
    //             $filename  = $movefile['file'];
    //             $filetype  = wp_check_filetype(basename($filename), null);

    //             $attachment = array(
    //                 'post_mime_type' => $filetype['type'],
    //                 'post_title'     => sanitize_file_name(basename($filename)),
    //                 'post_content'   => '',
    //                 'post_status'    => 'inherit'
    //             );

    //             // Insert into media library
    //             $attach_id = wp_insert_attachment($attachment, $filename, $book_id);

    //             // Generate attachment metadata
    //             $attach_data = wp_generate_attachment_metadata($attach_id, $filename);
    //             wp_update_attachment_metadata($attach_id, $attach_data);

    //             // ✅ Set as featured image
    //             set_post_thumbnail($book_id, $attach_id);
    //         }
    //     }

    //     // ✅ Handle multiple gallery uploads & save URLs in post meta
    //     if (!empty($_FILES['bookimage']['name'][0])) {
    //         require_once(ABSPATH . 'wp-admin/includes/file.php');

    //         $upload_overrides = array('test_form' => false);
    //         $uploaded_urls    = array();

    //         foreach ($_FILES['bookimage']['name'] as $key => $value) {
    //             if ($_FILES['bookimage']['name'][$key]) {
    //                 $file = array(
    //                     'name'     => $_FILES['bookimage']['name'][$key],
    //                     'type'     => $_FILES['bookimage']['type'][$key],
    //                     'tmp_name' => $_FILES['bookimage']['tmp_name'][$key],
    //                     'error'    => $_FILES['bookimage']['error'][$key],
    //                     'size'     => $_FILES['bookimage']['size'][$key]
    //                 );

    //                 $movefile = wp_handle_upload($file, $upload_overrides);

    //                 if ($movefile && !isset($movefile['error'])) {
    //                     $uploaded_urls[] = esc_url_raw($movefile['url']);
    //                 }
    //             }
    //         }

    //         if (!empty($uploaded_urls)) {
    //             update_post_meta($book_id, 'book_images', $uploaded_urls); 
    //         }
    //     }

    //     // ✅ Save other meta
    //     update_post_meta($book_id, 'bookprice', $bookprice);
    //     update_post_meta($book_id, 'booktagline', $booktagline);

    //     // Redirect after success
    //     wp_redirect(home_url('/vendor/?tab=books&success=1'));
    //     exit;
    // }


    // Edit a Book
    // if (isset($_POST['vendor-edit-book']) && $_POST['vendor-edit-book'] == 1) {

    //     $bookid        = intval($_GET['id']);
    //     $existingbook  = get_post($bookid);

    //     $booktitle       = sanitize_text_field($_POST['editedbooktitle']);
    //     $booktagline     = sanitize_text_field($_POST['editedbooktagline']);
    //     $bookdescription = wp_kses_post($_POST['editedbookdescription']);
    //     $bookprice       = floatval($_POST['editedbookprice']);
    //     $bookpublisher   = get_current_user_id();

    //     if ($existingbook && $existingbook->post_author == $bookpublisher && $existingbook->post_type == 'books') {

    //         require_once(ABSPATH . 'wp-admin/includes/file.php');
    //         require_once(ABSPATH . 'wp-admin/includes/image.php');

    //         // ✅ Handle multiple gallery images (book_images)
    //         if (!empty($_FILES['editedbookimage']['name'][0])) {
    //             $upload_overrides = array('test_form' => false);
    //             $uploaded_urls    = array();

    //             foreach ($_FILES['editedbookimage']['name'] as $key => $value) {
    //                 if ($_FILES['editedbookimage']['name'][$key]) {
    //                     $file = array(
    //                         'name'     => $_FILES['editedbookimage']['name'][$key],
    //                         'type'     => $_FILES['editedbookimage']['type'][$key],
    //                         'tmp_name' => $_FILES['editedbookimage']['tmp_name'][$key],
    //                         'error'    => $_FILES['editedbookimage']['error'][$key],
    //                         'size'     => $_FILES['editedbookimage']['size'][$key]
    //                     );

    //                     // Upload file
    //                     $movefile = wp_handle_upload($file, $upload_overrides);

    //                     if ($movefile && !isset($movefile['error'])) {
    //                         $uploaded_urls[] = esc_url_raw($movefile['url']);
    //                     } else {
    //                         error_log('Upload failed: ' . $movefile['error']); // log errors
    //                     }
    //                 }
    //             }

    //             if (!empty($uploaded_urls)) {
    //                 // Replace old gallery images
    //                 update_post_meta($bookid, 'book_images', $uploaded_urls);
    //             }
    //         }

    //         // ✅ Handle feature image (thumbnail)
    //         if (!empty($_FILES['editedfeatureimage']['name'])) {
    //             $upload_overrides = array('test_form' => false);

    //             $movefile = wp_handle_upload($_FILES['editedfeatureimage'], $upload_overrides);

    //             if ($movefile && !isset($movefile['error'])) {
    //                 $filename  = $movefile['file'];
    //                 $filetype  = wp_check_filetype(basename($filename), null);

    //                 $attachment = array(
    //                     'post_mime_type' => $filetype['type'],
    //                     'post_title'     => sanitize_file_name(basename($filename)),
    //                     'post_content'   => '',
    //                     'post_status'    => 'inherit'
    //                 );

    //                 // Insert into media library
    //                 $attach_id = wp_insert_attachment($attachment, $filename, $bookid);

    //                 // Generate attachment metadata
    //                 $attach_data = wp_generate_attachment_metadata($attach_id, $filename);
    //                 wp_update_attachment_metadata($attach_id, $attach_data);

    //                 // ✅ Set as featured image
    //                 set_post_thumbnail($bookid, $attach_id);
    //             } else {
    //                 error_log('Feature image upload failed: ' . $movefile['error']);
    //             }
    //         }

    //         // ✅ Update post details
    //         $updated_book = array(
    //             'ID'           => $bookid,
    //             'post_title'   => $booktitle,
    //             'post_content' => $bookdescription,
    //             'post_status'  => 'publish',
    //             'post_type'    => 'books',
    //             'post_author'  => $bookpublisher,
    //         );

    //         $book_updated_detail = wp_update_post($updated_book);

    //         if (is_wp_error($book_updated_detail)) {
    //             $errors['general'] = $book_updated_detail->get_error_message();
    //         } else {
    //             update_post_meta($bookid, 'bookprice', $bookprice);
    //             update_post_meta($bookid, 'booktagline', $booktagline);

    //             wp_redirect(home_url('/vendor/?tab=books&edited=1'));
    //             exit;
    //         }

    //     } else {
    //         echo '<div class="alert alert-danger">You do not have permission to edit this book.</div>';
    //         return;
    //     }
    // }


    // Delete a Book
    // if(isset($_POST['vendor-delete-book'])) {

    //     $bookpublisher = get_current_user_id();
    //     $bookid = $_POST['vendor-delete-book'];
    //     $existingbook = get_post($bookid);

    //     if ($existingbook && $existingbook->post_author == $bookpublisher && $existingbook->post_type == 'books') {
            
    //         $deleted = wp_delete_post($bookid, true);
    //         if ($deleted) {
    //             // Redirect with success message
    //             wp_redirect(add_query_arg('deleted', '1', home_url('/vendor')));
    //             exit;
    //         } else {
    //             echo '<div class="alert alert-danger">Failed to delete the book. Please try again.</div>';
    //         }
    //     } else {
    //         echo '<div class="alert alert-danger">You do not have permission to delete this book or book not found.</div>';
    //     }
    // }
}
add_action('init', 'vendor_book');

// Read Books
function vendor_view_books() {
    $current_user   = wp_get_current_user();
    $current_vendor = get_current_user_id();

    // Check if user has "vendor" role
    if ( ! in_array( 'vendor', (array) $current_user->roles ) ) {
        return '<div class="alert alert-warning">⚠️ You don\'t have access to this page. Please register as a vendor.</div>';
    }

    $args = [
        'post_type'   => 'books',
        'post_status' => 'publish',
        'author'      => $current_vendor,
    ];

    $book = new WP_Query($args);

    ob_start();

    // Show success message if book was deleted
    if (isset($_GET['deleted']) && $_GET['deleted'] == '1') {
        echo '<div class="alert alert-success">Book deleted successfully.</div>';
    }

    if ($book->have_posts()) {
        echo '<div class="row g-4">';
        while ($book->have_posts()) {
            $book->the_post();
            $post_id   = get_the_ID();
            $book_author_id = get_post_field('post_author', $post_id);

            if (intval($book_author_id) !== intval($current_vendor)) {
                continue;
            }

            $price = get_post_meta($post_id, 'bookprice', true);
            ?>
            <div class="col-md-4">
                <div class="card shadow-sm h-100">
                    <?php if ( has_post_thumbnail() ) : ?>
                    <div class="text-center mt-3">
                        <?php the_post_thumbnail( 'medium', ['class' => 'img-fluid rounded', 'style' => 'height:100px; object-fit:cover;'] ); ?>
                    </div>
                    <?php endif; ?>

                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title"><?php the_title(); ?></h5>

                        <!-- Short description -->
                        <p class="fw-bold">
                            <?php echo wp_trim_words( get_the_content(), 15, '...' ); ?>
                        </p>

                        <div class="mt-auto d-flex justify-content-between align-items-center">
                            <span class="fw-bold text-success">₹<?php echo esc_html($price); ?></span>
                            <a href="<?php the_permalink(); ?>" class="btn btn-outline-primary btn-sm">
                            📖 View
                            </a>
                            <a href="javascript:void(0)" data-type="edit" data-id="<?php echo esc_attr($post_id); ?>"
                                class="btn btn-outline-primary btn-sm" id="edit-button">✏️ Edit</a>                                
                        </div>
                    </div>
                </div>
            </div>
            <?php
        }
        echo '</div>';
    } else {
        echo '<div class="alert alert-info">📚 No books found. <a href="?tab=add">Add your first book</a></div>';
    }

    wp_reset_postdata();
    return ob_get_clean();
}
add_shortcode('vendor_books', 'vendor_view_books');


/**
 * Admin Dashboard Menus
 */
// Add "Questions" menu in Admin Dashboard
function register_book_questions_menu() {
    add_menu_page(
        'Book Questions',        // Page title
        'Book Questions',        // Menu title
        'manage_options',        // Capability
        'book-questions',        // Slug
        'book_questions_on_admin_page', // Callback
        'dashicons-format-chat', // Icon
        27,
    );
}
add_action('admin_menu', 'register_book_questions_menu');

// Book Questions on Admin Page
function book_questions_on_admin_page() {
    // Get all vendors (authors of "books")
    $vendors = get_users([
        'role'   => 'vendor',  // only vendor role
        'fields' => ['ID', 'display_name'], // returns Only the ID and display_name for each vendor
    ]);

    // Get selected vendor from dropdown
    $selected_vendor = isset($_GET['vendor_filter']) ? intval($_GET['vendor_filter']) : 0;
    ?>
    <div class="wrap">            
        <h2>Book Questions Details</h2>

        <!-- Vendor Dropdown Filter -->
        <form method="get" style="margin-bottom:20px;">

            <input type="hidden" name="page" value="<?php echo esc_attr($_GET['page']); ?>">
            
            <label for="vendor_filter"><strong>Filter by Vendor: </strong></label>
            <select name="vendor_filter" id="vendor_filter" onchange="this.form.submit()">
                <option value="0">All Vendors</option>
                <?php foreach ($vendors as $vendor): ?>
                    <option value="<?php echo $vendor->ID; ?>" <?php selected($selected_vendor, $vendor->ID); ?>>
                        <?php echo esc_html($vendor->display_name); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>

        <!-- Table -->
        <table class="widefat fixed striped">
            <thead>
                <tr>
                    <th>S.No</th>
                    <th>Book ID</th>
                    <th>Book Title</th>
                    <th>Vendor Name</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Question</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                // WP_Query args
                $args = [
                    'post_type'   => 'books',
                    'post_status' => 'publish',
                    'posts_per_page' => -1,
                ];

                if ($selected_vendor > 0) {
                    $args['author'] = $selected_vendor; // filter books by vendor
                }

                $book = new WP_Query($args);

                if ($book->have_posts()) {
                    $count = 1;
                    while ($book->have_posts()) {
                        $book->the_post();
                        $post_id    = get_the_ID();

                        $vendor_id   = get_post_field('post_author', $post_id);
                        $vendor_user = get_userdata($vendor_id);
                        $vendor_name = $vendor_user ? $vendor_user->display_name : 'Unknown';

                        $book_title  = get_the_title();

                        $questions = get_post_meta($post_id, 'questions', true);

                        if (is_array($questions) && !empty($questions)) {
                            foreach ($questions as $question) { ?>
                                <tr>
                                    <td><?php echo $count++; ?></td>
                                    <td><?php echo $post_id; ?></td>
                                    <td><?php echo esc_html($book_title); ?></td>
                                    <td><?php echo esc_html($vendor_name); ?></td>
                                    <td><?php echo esc_html($question['name']); ?></td>
                                    <td><?php echo esc_html($question['email']); ?></td>
                                    <td><?php echo esc_html($question['phone']); ?></td>
                                    <td><?php echo esc_html($question['question']); ?></td>
                                    <td><?php echo esc_html($question['date']); ?></td>
                                </tr>
                            <?php }
                        }
                    }
                    wp_reset_postdata();
                } else {
                    echo '<tr><td colspan="10">No questions found.</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
    <?php
}






