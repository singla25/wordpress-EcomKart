<?php
/* Template Name: About Page */
get_header();
?>

<main class="about-page bg-dark bg-opacity-50 text-light py-5">

    <div class="container">

        <!-- Hero Section -->
        <section class="text-center mb-5">
            <h1 class="fw-bold display-4 mb-3 text-dark"><?php the_title(); ?></h1>
            <p class="lead opacity-75">Welcome to our website 🚀 – Explore our collection of Cars, Books, Mobiles, and Tabs, all in one place.</p>
            <hr class="w-25 mx-auto border-light">
        </section>

        <!-- Page Content -->
        <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
            <div class="page-content text-center mb-5">
                <?php the_content(); ?>
            </div>
        <?php endwhile; endif; ?>

        <!-- Cars Section -->
        <section class="mb-5">
            <h2 class="fw-bold text-center mb-4">🚗 Cars</h2>
            <div class="row g-4">
                <?php
                $cars = new WP_Query(['post_type' => 'cars', 'posts_per_page' => 4]);
                if ( $cars->have_posts() ) :
                    while ( $cars->have_posts() ) : $cars->the_post(); ?>
                        <div class="col-md-3">
                            <div class="card h-100 border-0 shadow-sm">
                                <?php if ( has_post_thumbnail() ) : ?>
                                    <?php the_post_thumbnail('medium', ['class' => 'card-img-top rounded']); ?>
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
                    <?php endwhile; wp_reset_postdata();
                else : ?>
                    <p class="text-center">No Cars available yet.</p>
                <?php endif; ?>
            </div>
        </section>

        <!-- Books Section -->
        <section class="mb-5">
            <h2 class="fw-bold text-center mb-4">📚 Books</h2>
            <div class="row g-4">
                <?php
                $books = new WP_Query(['post_type' => 'books', 'posts_per_page' => 4]);
                if ( $books->have_posts() ) :
                    while ( $books->have_posts() ) : $books->the_post(); ?>
                        <div class="col-md-3">
                            <div class="card h-100 border-0 shadow-sm">
                                <?php if ( has_post_thumbnail() ) : ?>
                                    <?php the_post_thumbnail('medium', ['class' => 'card-img-top rounded']); ?>
                                <?php endif; ?>
                                <div class="card-body">
                                    <h5 class="card-title fw-bold">
                                        <a href="<?php the_permalink(); ?>" class="text-dark text-decoration-none"><?php the_title(); ?></a>
                                    </h5>
                                    <p class="card-text small text-muted"><?php echo wp_trim_words(get_the_excerpt(), 12); ?></p>
                                    <a href="<?php the_permalink(); ?>" class="btn btn-outline-dark btn-sm">Read More</a>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; wp_reset_postdata();
                else : ?>
                    <p class="text-center">No Books available yet.</p>
                <?php endif; ?>
            </div>
        </section>

        <!-- Mobiles Section -->
        <section class="mb-5">
            <h2 class="fw-bold text-center mb-4">📱 Mobiles</h2>
            <div class="row g-4">
                <?php
                $mobiles = new WP_Query(['post_type' => 'mobiles', 'posts_per_page' => 4]);
                if ( $mobiles->have_posts() ) :
                    while ( $mobiles->have_posts() ) : $mobiles->the_post(); ?>
                        <div class="col-md-3">
                            <div class="card h-100 border-0 shadow-sm">
                                <?php if ( has_post_thumbnail() ) : ?>
                                    <?php the_post_thumbnail('medium', ['class' => 'card-img-top rounded']); ?>
                                <?php endif; ?>
                                <div class="card-body">
                                    <h5 class="card-title fw-bold">
                                        <a href="<?php the_permalink(); ?>" class="text-dark text-decoration-none"><?php the_title(); ?></a>
                                    </h5>
                                    <p class="card-text small text-muted"><?php echo wp_trim_words(get_the_excerpt(), 12); ?></p>
                                    <a href="<?php the_permalink(); ?>" class="btn btn-outline-dark btn-sm">View Mobile</a>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; wp_reset_postdata();
                else : ?>
                    <p class="text-center">No Mobiles available yet.</p>
                <?php endif; ?>
            </div>
        </section>

        <!-- Tabs Section -->
        <section>
            <h2 class="fw-bold text-center mb-4">💻 Tabs</h2>
            <div class="row g-4">
                <?php
                $tabs = new WP_Query(['post_type' => 'tabs', 'posts_per_page' => 4]);
                if ( $tabs->have_posts() ) :
                    while ( $tabs->have_posts() ) : $tabs->the_post(); ?>
                        <div class="col-md-3">
                            <div class="card h-100 border-0 shadow-sm">
                                <?php if ( has_post_thumbnail() ) : ?>
                                    <?php the_post_thumbnail('medium', ['class' => 'card-img-top rounded']); ?>
                                <?php endif; ?>
                                <div class="card-body">
                                    <h5 class="card-title fw-bold">
                                        <a href="<?php the_permalink(); ?>" class="text-dark text-decoration-none"><?php the_title(); ?></a>
                                    </h5>
                                    <p class="card-text small text-muted"><?php echo wp_trim_words(get_the_excerpt(), 12); ?></p>
                                    <a href="<?php the_permalink(); ?>" class="btn btn-outline-dark btn-sm">View Tab</a>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; wp_reset_postdata();
                else : ?>
                    <p class="text-center">No Tabs available yet.</p>
                <?php endif; ?>
            </div>
        </section>

    </div>
</main>

<?php get_footer(); ?>
