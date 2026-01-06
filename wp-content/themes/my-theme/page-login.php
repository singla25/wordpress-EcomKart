<?php
/**
 * Template Name: Login
 */
get_header();
?>

<div class="bg-dark bg-opacity-50 p-5 shadow-sm">
  <div class="row justify-content-center">
    <div class="col-md-6">
      <div class="card shadow-lg border-0 rounded-4 bg-light">
        <div class="card-body p-4">
          <h2 class="text-center mb-4">🔑 Login</h2>

          <form method="post">
            <!-- Username -->
            <div class="mb-3">
              <label for="username" class="form-label">Username</label>
              <input type="text" name="username" id="username" class="form-control" placeholder="Enter your username " required>
            </div>

            <!-- Password -->
            <div class="mb-3">
              <label for="password" class="form-label">Password</label>
              <input type="password" name="password" id="password" class="form-control" placeholder="Enter your password" required>
            </div>

            <!-- Remember Me
            <div class="form-check mb-3">
              <input type="checkbox" name="rememberme" id="rememberme" class="form-check-input">
              <label for="rememberme" class="form-check-label ps-2 pt-1">Remember Me</label>
            </div> -->

            <input type="hidden" name="login" value="1">

            <!-- Submit Button -->
            <div class="d-grid mb-3">
              <button type="submit" class="btn btn-success btn-lg rounded-3">
                Login
              </button>
            </div>
          </form>

          <!-- Sign Up Section -->
          <div class="text-center mt-3">
            <p>Don’t have an account?</p>
            <a href="<?php echo home_url('/signup'); ?>" class="btn btn-outline-secondary btn-sm rounded-3">
              Sign Up Here
            </a>
          </div>

        </div>
      </div>
    </div>
  </div>
</div>
<?php get_footer(); ?>

