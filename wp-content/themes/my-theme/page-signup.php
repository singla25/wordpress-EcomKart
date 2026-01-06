<?php
/**
 * Template Name: Sign Up form
 */
get_header();

// Call the Sign Up function and get errors
$errors = signup_form();
?>

<div class="bg-dark bg-opacity-50 p-5 shadow-sm">
  <div class="row justify-content-center">
    <div class="col-md-6">
      <div class="card shadow-lg border-0 rounded-4 bg-light">
        <div class="card-body p-4">
          <h2 class="text-center mb-4">🛒 Sign Up</h2>
          <form method="post" class="needs-validation" novalidate>
            <div class="row g-3">

              <!-- Username -->
              <div class="col-6">
                <label for="username" class="form-label">Username</label>
                <input type="text" name="username" id="username" class="form-control" placeholder="Enter your username" required>
                <?php if(isset($errors['username'])): ?>
                    <small class="text-danger"><?php echo $errors['username']; ?></small>
                <?php endif; ?>
              </div>

              <!-- Name -->
              <div class="col-6">
                <label for="name" class="form-label">Name</label>
                <input type="text" name="name" id="name" class="form-control" placeholder="Enter your Name" required>
              </div>

              <!-- Email -->
              <div class="col-md-6">
                <label for="email" class="form-label">Email</label>
                <input type="email" name="email" id="email" class="form-control" placeholder="Enter your email" required>
                <?php if(isset($errors['email'])): ?>
                    <small class="text-danger"><?php echo $errors['email']; ?></small>
                <?php endif; ?>
              </div>

              <!-- Role -->
              <div class="col-md-6">
                <label for="role" class="form-label">Select Role</label>
                <select name="role" id="role" class="form-select" required>
                  <option value="">-- Choose Role --</option>
                  <option value="vendor">Vendor</option>
                  <option value="seller">Seller</option>
                </select>
              </div>

              <!-- Password -->
              <div class="col-6">
                <label for="password" class="form-label">Password</label>
                <input type="password" name="password" id="password" class="form-control" placeholder="Create a password" required>
              </div>

              <!-- Confirm Password -->
              <div class="col-6">
                <label for="confirmpassword" class="form-label">Confirm Password</label>
                <input type="password" name="confirmpassword" id="confirmpassword" class="form-control" placeholder="Confirm password" required>
                <?php if(isset($errors['password'])): ?>
                    <small class="text-danger"><?php echo $errors['password']; ?></small>
                <?php endif; ?>
              </div>

              <!-- Age -->
              <div class="col-6">
                <label for="age" class="form-label">Age</label>
                <input type="number" name="age" id="age" class="form-control" placeholder="Your age"
                min="1" step="1" required>
                <?php if(isset($_GET['age']) && !is_numeric($_GET['age'])) :
                  echo '<small class="text-danger">Age must be number</small>';
                endif; ?>
              </div>

              <!-- Phone -->
              <div class="col-6">
                <label for="phone" class="form-label">Phone</label>
                <input type="tel" name="phone" id="phone" class="form-control" placeholder="Your phone number" 
                pattern="[0-9]{10}" maxlength="10" required>
                <?php if(isset($_GET['phone']) && (!is_numeric($_GET['phone']) || strlen($_GET['phone']) != 10)) :
                  echo '<small class="text-danger">Phone number must bes 10 numeric digit</small>';
                endif; ?>
              </div>

              <!-- Hidden Field -->
              <input type="hidden" name="signup" value="1">

              <!-- Submit Button -->
              <div class="col-12 d-flex justify-content-center mt-3">
                <button type="submit" class="btn btn-success btn-lg rounded-3">
                  Sign Up
                </button>
              </div>

            </div>
          </form>

          <!-- Already Registered Section -->
          <div class="text-center mt-4">
            <p>Already have an account?</p>
            <a href="<?php echo home_url('/login'); ?>" class="btn btn-outline-secondary btn-sm rounded-3">
              Login Here
            </a>
          </div>

        </div>
      </div>
    </div>
  </div>
</div>

<?php get_footer(); ?>
