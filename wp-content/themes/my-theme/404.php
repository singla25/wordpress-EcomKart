<?php get_header(); ?>

<div class="bg-dark bg-opacity-50 text-light p-5 shadow-sm">
    <div class="container error-container">
        <h1 class="error-code">404</h1>
        <h2 class="error-message">Page Not Found</h2>
        <p class="error-description">Sorry, the page you are looking for doesn’t exist or has been moved.</p>
        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="error-btn">Back to Home</a>
    </div>
</div>


<?php get_footer(); ?>



