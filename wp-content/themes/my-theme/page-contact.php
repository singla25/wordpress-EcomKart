<?php 
/* 
Template Name: Contact Us 
*/ 
get_header(); 
?>

<main class="contact-page">
  <div class="bg-dark bg-opacity-50 text-light p-5 shadow-sm">

    <!-- Row 1: Heading -->
    <div class="row mb-5">
      <div class="col text-center">
        <h1 class="fw-bold mb-3">Need Help? Get in Touch</h1>
        <p class="lead">We’re here to answer your questions quickly and clearly.</p>
      </div>
    </div>

    <!-- Row 2: Contact Form + Map -->
    <div class="row m-5 p-5 g-4 shadow-lg border-0 rounded-3 bg-secondary text-light">
      <div class="col-md-6 d-flex">
        <?php echo do_shortcode('[custom_form id="357"]'); ?>

        <!-- <div class="card flex-fill bg-light">
          <div class="card-body">
            <h2 class="card-title mb-4 text-center">Contact Us</h2>

            <form method="POST" id="contact-form">
              <div class="row">
                <div class="col-md-4">
                  <label for="name" class="form-label">Name</label>
                  <input type="text" name="name" id="name" class="form-control" required>
                </div>
                <div class="col-md-4">
                  <label for="email" class="form-label">Email</label>
                  <input type="email" name="email" id="email" class="form-control" required>
                </div>
                <div class="col-md-4">
                  <label for="phone" class="form-label">Phone</label>
                  <input type="tel" name="phone" id="phone" class="form-control" pattern="[0-9]{10}" maxlength="10" required>
                  <?php if(isset($_GET['phone']) && (!is_numeric($_GET['phone']) || strlen($_GET['phone']) != 10)) :
                    echo '<small class="text-danger">Phone number must bes 10 numeric digit</small>';
                  endif; ?>
                </div>
                <div class="col-6">
                  <label for="subject" class="form-label">Subject</label>
                  <input type="text" name="subject" id="subject" class="form-control" required>
                </div>
                <div class="col-6">
                  <label for="topic" class="form-label">Topic</label>
                  <input type="text" name="topic" id="topic" class="form-control" required>
                </div>
                <div class="col-12">
                  <label for="query" class="form-label">Your Query</label>
                  <textarea name="query" id="query" rows="3" class="form-control" required></textarea>
                </div>
              </div>
              <input type="hidden" name="contact-form" value="1">
              <button type="submit" class="btn btn-outline-success w-100" id="contact-form-btn">Send Message</button>
            </form>

          </div>
        </div> -->
      </div>

      <div class="col-md-6 d-flex">
        <div class="card flex-fill bg-light">
          <div class="card-body d-flex flex-column">
            <h2 class="card-title mb-4 text-center">Our Location</h2>
            <div class="flex-fill">
              <iframe 
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d224345.8398690213!2d77.06889903703655!3d28.52728069164569!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x390d03f6f9f9f9f9%3A0xf7c3b0b7b0b0b0b0!2sNew+Delhi!5e0!3m2!1sen!2sin!4v1693387690376!5m2!1sen!2sin" 
                width="100%" height="100%" style="border:0; min-height:400px;" allowfullscreen="" loading="lazy">
              </iframe>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Row 3: Best Line -->
    <div class="row text-center text-dark">
      <div class="col">
        <h3 class="fw-bold">“Your questions matter. We’re here to provide answers fast and clearly!”</h3>
      </div>
    </div>

  </div>
</main>

<?php get_footer(); ?>
