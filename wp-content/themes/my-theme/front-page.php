<?php
/* Template Name: Front Page */
get_header();
?>

<main class="front-page">

    <!-- Hero Section -->
    <section class="hero d-flex align-items-center text-center text-white position-relative">
        <?php if ( has_post_thumbnail() ) : ?>
            <div class="hero-bg">
                <?php the_post_thumbnail('full', ['class' => 'w-100 h-100 object-fit-cover']); ?>
            </div>
        <?php endif; ?>
        <div class="hero-overlay"></div>
        <div class="container position-relative">
            <h1 class="display-4 fw-bold"><?php bloginfo('name'); ?> 🚀</h1>
            <p class="lead mb-4"><?php bloginfo('description'); ?></p>
            <a href="<?php echo site_url('/about-page'); ?>" class="btn btn-warning btn-lg">Explore More</a>
        </div>
    </section>

    <!-- Feature Cards Section -->
    <section class="feature-cards py-5 bg-dark text-white bg-opacity-50">
        <div class="container text-center">
            <h2 class="text-center mb-2">Why is it so great?</h2>
            <p class="text-center text-muted mb-5">
                A service is an intangible activity, process, or benefit that provides value to a customer without transferring ownership of a physical product.
            </p>
            <div class="row g-4 text-center">
                <?php if ( is_active_sidebar( 'feature-cards' ) ) : ?>
                    <?php dynamic_sidebar( 'feature-cards' ); ?>
                <?php else : ?>
                    <p class="text-muted">Add widgets in <strong>Appearance → Widgets → Feature Cards Section</strong></p>
                <?php endif; ?>
            </div>
        </div>
    </section>


    <!-- Latest Posts Section -->
    <?= do_shortcode("[get_blog_posts]");?>

    <!-- Banner Section -->
    <section class="banner py-5 text-center text-white bg-dark bg-opacity-50 shadow-sm">
        <div class="container">
            <h2 class="fw-bold mb-3">🚀 Grow Your Business with Us</h2>
            <p class="lead mb-4">We help you build modern, fast & scalable websites.</p>
            <a href="<?php echo site_url('/contact'); ?>" class="btn btn-light btn-lg">Get in Touch</a>
        </div>
    </section>

    <!-- Newsletter Section -->
    <?= do_shortcode('[get_subscribe_newsletter]'); ?>

</main>

<?php get_footer(); ?>
