<?php
/**
 * Template Name: Vendor Dashboard
 */
get_header();

// ✅ Check if user is a vendor
$current_user = wp_get_current_user();
if ( ! in_array( 'vendor', (array) $current_user->roles ) ) {
    echo '<div class="container py-5">
            <div class="alert alert-warning text-center">
              ⚠️ You do not have access to the Vendor Dashboard.<br>
              Please <a href="' . esc_url( home_url('/signup') ) . '">register as a vendor</a>.
            </div>
          </div>';
    get_footer();
    exit; // stop loading the rest of the template
}

// Handle active tab
$tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'books';


// Edit a Book (only if id exists and tab is edit)
$bookid = $booktitle = $bookdescription = $bookprice = '';

if ($tab === 'edit' && isset($_GET['id'])) {
    $book_id = $_GET['id'];
    $book    = get_post($book_id);

    if ($book && $book->post_author == get_current_user_id()) {
        $bookid          = $book->ID;
        $booktitle       = $book->post_title;
        $bookdescription = $book->post_content;
        $bookprice       = get_post_meta($bookid, 'bookprice', true);
    }
}


// User Detail
$userid = get_current_user_id();
$user   = get_userdata($userid);
// echo "<pre>";
// print_r($user);

$user_username = $user->data->user_login;
$user_name   = $user->data->user_nicename;
$user_email  = $user->data->user_email;
$userage         = get_user_meta($userid, 'age', true);
$userphonenumber = get_user_meta($userid, 'phone_number', true);


// Book Detail in Tables
$current_vendor = get_current_user_id();

$args = [
  'post_type'   => 'books',
  'post_status' => 'publish',
  'author'      => $current_vendor,
];    

$booktable = new WP_Query($args);
?>

<div class="vendor-dashboard">
  <div class="container-fluid">
    <div class="row">
      
      <!-- Sidebar -->
      <div class="col-md-3 col-lg-2 bg-dark text-white border-end min-vh-100 p-3">
        <h4 class="fw-bold mb-4 text-center">Vendor</h4>
        <div class="mb-4">
          <p class="fw-semibold">👋 Hello, <?php echo $user_name; ?></p>
        </div>
        <ul class="nav flex-column nav-pills">
          <li class="nav-item mb-2">
            <a class="nav-link <?php echo ($tab == 'books') ? 'active' : ''; ?> text-white" href="?tab=books">📚 Books</a>
          </li>
          <li class="nav-item mb-2">
            <a class="nav-link <?php echo ($tab == 'add') ? 'active' : ''; ?> text-white" href="?tab=add">➕ Add Book</a>
          </li>
          <?php if ($tab == 'edit' && isset($_GET['id'])): ?>
            <li class="nav-item mb-2">
              <a class="nav-link active white" href="#">✏️ Edit Book</a>
            </li>
          <?php endif; ?>
          <li class="nav-item mb-2">
            <a class="nav-link <?php echo ($tab == 'settings') ? 'active' : ''; ?> text-white" href="?tab=settings">⚙️ Settings</a>
          </li>
          <li class="nav-item mb-2">
            <a class="nav-link <?php echo ($tab == 'questions') ? 'active' : ''; ?> text-white" href="?tab=questions">💬 Questions</a>
          </li>
        </ul>

        <div class="mt-2 pt-2">
          <a class="btn btn-outline-danger w-100" href="<?php echo esc_url( home_url( '?logout=logout' ) ); ?>">🚪 Logout</a>
        </div>
      </div>

      <!-- Main Content -->
      <div class="col-md-9 col-lg-10 p-4 bg-secondary">
        <div class="card shadow-sm rounded-3 p-4">
          
            <!-- Books -->
          <?php if ($tab == 'books'): ?>
            <h3 class="fw-bold text-center">📚 My Books</h3>
            <p class="text-muted text-center pb-4">List of all books</p>
            <?php echo do_shortcode('[vendor_books]'); ?>

            <!-- Add a Book -->
          <?php elseif ($tab == 'add'): ?>
            <h3 class="fw-bold text-center">➕ Add Book</h3>

            <form method="post" enctype="multipart/form-data" class="p-4 shadow-lg rounded-3 bg-secondary bg-opacity-75 text-white">
              <div class="row g-3">
               
                <div class="col-md-6">
                  <label for="booktitle" class="form-label fw-bold">📕 Book Title</label>
                  <input id="booktitle" type="text" name="booktitle" class="form-control" placeholder="Enter book title" required>
                </div>

                <div class="col-md-6">
                  <label for="booktagline" class="form-label fw-bold">✨ Book Tagline</label>
                  <input id="booktagline" type="text" name="booktagline" class="form-control" placeholder="Book Tagline" required>
                </div>

                <div class="col-md-6">
                  <label for="featureimage" class="form-label fw-bold">🖼️ Feature Image</label>
                  <input id="featureimage" type="file" name="featureimage" class="form-control" required>
                </div>

                <div class="col-md-6">
                  <label for="bookimage" class="form-label fw-bold">🖼️ Upload Photo</label>
                  <input id="bookimage" type="file" name="bookimage[]" class="form-control" multiple required>
                </div>

                <div class="col-md-4">
                  <label for="bookprice" class="form-label fw-bold">💰 Price</label>
                  <input id="bookprice" type="number" name="bookprice" class="form-control" placeholder="Enter price" min="0" required>
                </div>

                <div class="col-md-4">
                  <label for="bookprice" class="form-label fw-bold">Genre</label>
                </div>

                <div class="col-md-4">
                  <label for="bookprice" class="form-label fw-bold">Author</label>
                </div>

                <div class="col-12">
                  <label for="bookdescription" class="form-label fw-bold">📝 Description</label>
                  <textarea id="bookdescription" name="bookdescription" class="form-control" rows="4" placeholder="Write a detailed description..." required></textarea>
                </div>
              </div>

              <input type="hidden" name="vendor-add-book" value="1">

              <div class="d-grid mt-4">
                <button type="submit" class="btn btn-success btn-lg">✅ Save Book</button>
              </div>
            </form>

            <!-- Edit a Book -->
          <?php elseif ($tab == 'edit' && isset($_GET['id'])): ?>
            <h3 class="fw-bold text-center">✏️ Edit Book</h3>
            <?php
              $book = get_post($_GET['id']);
              if ($book && $book->post_author == get_current_user_id()) {
                $bookid = $book->ID;
                $booktitle = $book->post_title;
                $bookdescription = $book->post_content; 
                $bookprice = get_post_meta($bookid, 'bookprice', true);
                $booktagline = get_post_meta($bookid, 'booktagline', true);
                $images = get_post_meta($bookid, 'book_images', true)
            ?>

            <form method="post" enctype="multipart/form-data" class="p-4 shadow-lg rounded-3 bg-secondary bg-opacity-75 text-white">
              <div class="row g-3">
               
                <div class="col-md-6">
                  <label for="editedbooktitle" class="form-label fw-bold">📕 Book Title</label>
                  <input id="editedbooktitle" type="text" name="editedbooktitle" class="form-control" value="<?php echo esc_attr($booktitle); ?>" required>
                </div>

                <div class="col-md-6">
                  <label for="editedbooktagline" class="form-label fw-bold">✨ Book Tagline</label>
                  <input id="editedbooktagline" type="text" name="editedbooktagline" class="form-control" value="<?php echo esc_attr($booktagline); ?>" required>
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
                  <input id="editedbookprice" type="number" name="editedbookprice" class="form-control" value="<?php echo esc_attr($bookprice); ?>" min="0" required>
                </div>

                <div class="col-12">
                  <label for="editedbookdescription" class="form-label fw-bold">📝 Description</label>
                  <textarea id="editedbookdescription" name="editedbookdescription" class="form-control" rows="4" required><?php echo $bookdescription; ?></textarea>
                </div>
              </div>

              <input type="hidden" name="vendor-edit-book" value="1">

              <div class="d-grid mt-4">
                <button type="submit" class="btn btn-success btn-lg">✅ Save Book</button>
              </div>
            </form>
            <?php } else { ?>
              <div class="alert alert-danger">You do not have permission to edit this book.</div>
            <?php } ?>

            <!-- Setting -->
          <?php elseif ($tab == 'settings'): ?>
            <h3 class="fw-bold text-center">⚙️ Settings</h3>
            <p class="text-muted text-center">Manage your account settings here.</p>
            
            <div class="row g4 pt-3">
              <!-- User Details -->
              <div class="col-md-2"></div>
              <div class="col-8">
                <div class="card shadow-sm rounded-3 p-4">
                  <h5 class="fw-bold mb-3 text-center">👤 User Details</h5>
                  <div class="row">
                    <!-- Left Column -->
                    <div class="col-md-6 ps-5">
                      <ul class="list-unstyled mb-0">
                        <li><strong>Username:</strong> <?php echo esc_html($user_username); ?></li>
                        <li><strong>Full Name:</strong> <?php echo esc_html($user_name); ?></li>
                        <li><strong>Email:</strong> <?php echo esc_html($user_email); ?></li>
                      </ul>
                    </div>

                    <!-- Right Column -->
                    <div class="col-md-6">
                      <ul class="list-unstyled mb-0">
                        <li><strong>Age:</strong> <?php echo esc_html($userage); ?></li>
                        <li><strong>Phone:</strong> <?php echo esc_html($userphonenumber); ?></li>
                      </ul>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-md-2"></div>

              <!-- Book Table -->
               <div class="col-12 mt-5">
                <div class="card shadow-sm rounded-3 p-4">
                  <h5 class="fw-bold mb-3 text-center">📚 My Books</h5>
                  <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                      <thead class="table-dark">
                        <tr>
                          <th>Book ID</th>
                          <th>Title</th>
                          <th>Description</th>
                          <th>Price</th>
                          <th>Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php 
                        if ($booktable->have_posts()) {
                          echo '<div class="row g-4">';
                          while ($booktable->have_posts()) {
                            $booktable->the_post();
                            $post_id = get_the_ID();
                            $book_author_id = get_post_field('post_author', $post_id);

                            if (intval($book_author_id) !== intval($current_vendor)) {
                              continue;
                            }

                            $price = get_post_meta($post_id, 'bookprice', true);

                        ?>
                        <tr>
                          <td><?php echo $post_id; ?></td>
                          <td><?php echo the_title(); ?></td>
                          <td><?php echo the_content(); ?></td>
                          <td><?php echo esc_html($price); ?></td>
                          <td>
                            <a href="?tab=edit&id=<?php echo $post_id; ?>" class="btn btn-sm btn-primary my-2">✏️ Edit</a>
                            <form method="post" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this book?');">
                              <input type="hidden" name="vendor-delete-book" value="<?php echo $post_id; ?>">
                              <button type="submit" class="btn btn-sm btn-danger">🗑️ Delete</button>
                            </form>
                          </td>
                        </tr>
                        <?php
                        }
                       } else {
                          echo '<tr><td colspan="3" class="text-center text-muted">📚 No books found</td></tr>';
                       }
                       ?>
                     </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>

            <!-- Questions Tab -->
          <?php elseif ($tab == 'questions'): ?>
          <h3 class="fw-bold text-center">💬 Questions</h3>
          <p class="text-muted text-center pb-4">Here are all questions asked by users about your books.</p>

          <div class="table-responsive">
            <table class="table table-bordered align-middle">
              <thead class="table-dark">
                <tr>
                  <th>Book ID</th>
                  <th>Book Title</th>
                  <th>Questions</th>
                </tr>
              </thead>
              <tbody>
                <?php 
                  if ($booktable->have_posts()) {
                    echo '<div class="row g-4">';
                    while ($booktable->have_posts()) {
                      $booktable->the_post();
                      $post_id = get_the_ID();
                      $book_author_id = get_post_field('post_author', $post_id);

                      if (intval($book_author_id) !== intval($current_vendor)) {
                        continue;
                      }

                      $price = get_post_meta($post_id, 'bookprice', true);
                      $questions = get_post_meta($post_id, 'questions', true);
                  ?>                              
                <tr>
                  <td><?php echo esc_html($post_id); ?></td>
                  <td><?php echo the_title(); ?></td>
                  <td>
                    <?php if (!empty($questions)) : ?>
                      <div class="table-responsive">
                        <table class="table table-sm table-striped table-hover mb-0">
                          <thead class="table-light">
                            <tr>
                              <th>Name</th>
                              <th>Email</th>
                              <th>Phone</th>
                              <th>Rating</th>
                              <th>Question</th>
                              <th>Date</th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php foreach ($questions as $question) : ?>
                              <tr>
                                <td><?php echo $question['name']; ?></td>
                                <td><?php echo $question['email']; ?></td>
                                <td><?php echo $question['phone']; ?></td>
                                <td>
                                  <?php 
                                    $rating = intval($question['rating']);
                                    for ($i = 1; $i <= 5; $i++) {
                                      if ($i <= $rating) {
                                        echo '<span style="color:gold; font-size:16px;">★</span>';
                                      } else {
                                        echo '<span style="color:#ccc; font-size:16px;">☆</span>';
                                      }
                                    }
                                  ?>
                                </td>
                                <td><?php echo $question['question']; ?></td>
                                <td><?php echo $question['date']; ?></td>
                              </tr>
                            <?php endforeach; ?>
                          </tbody>
                        </table>
                      </div>
                    <?php else : ?>
                      <span class="text-muted">No questions yet</span>
                    <?php endif; ?>
                  </td>
                </tr>
                <?php
                  }
                } else {
                  echo '<tr><td colspan="3" class="text-center text-muted">📚 No books found</td></tr>';
                }
                ?>
              </tbody>
            </table>
          </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>


<?php get_footer(); ?>
