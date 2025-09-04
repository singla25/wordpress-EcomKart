<?php
/**
 * The main template file
 *
 * @package YourThemeName
 */

get_header(); ?>

<div class="bg-dark bg-opacity-50 p-5 shadow-sm">
    <?php if ( have_posts() ) : ?>
        
        <!-- Header -->
        <header class="mb-5 text-center text-white">
            <h1 class="fw-bold display-5">Welcome to <?php bloginfo('name'); ?> 🚀</h1>
            <p class="lead text-light opacity-75"><?php bloginfo('description'); ?></p>
            <hr class="w-25 mx-auto border-light">
        </header>

        <!-- Posts Loop -->
        <div class="row g-4">
            <?php while ( have_posts() ) : the_post(); ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class('col-md-6'); ?>>
                    <div class="card h-100 shadow-sm border-0 rounded-3">
                        
                        <!-- Thumbnail -->
                        <?php if ( has_post_thumbnail() ) : ?>
                            <a href="<?php the_permalink(); ?>">
                                <?php the_post_thumbnail('medium', ['class' => 'card-img-top rounded-top']); ?>
                            </a>
                        <?php endif; ?>

                        <!-- Card Body -->
                        <div class="card-body">
                            <h2 class="card-title h5 fw-bold">
                                <a href="<?php the_permalink(); ?>" class="text-decoration-none text-dark">
                                    <?php the_title(); ?>
                                </a>
                            </h2>
                            <p class="card-text text-muted">
                                <?php the_excerpt(); ?>
                            </p>
                            <a href="<?php the_permalink(); ?>" class="btn btn-outline-light btn-sm">Read More</a>
                        </div>

                        <!-- Card Footer -->
                        <div class="card-footer bg-light text-muted small">
                            Posted on <?php echo get_the_date(); ?> by 
                            <span class="fw-semibold"><?php the_author(); ?></span>
                        </div>
                    </div>
                </article>
            <?php endwhile; ?>
        </div>

        <!-- Pagination -->
        <div class="pagination my-5 text-center">
            <?php the_posts_pagination([
                'mid_size' => 2,
                'prev_text' => '« Prev',
                'next_text' => 'Next »',
            ]); ?>
        </div>

    <?php else : ?>
        <section class="no-results not-found text-center text-white">
            <h2>No posts found</h2>
            <p>It looks like we couldn’t find what you’re looking for.</p>
        </section>
    <?php endif; ?>
</div>

<?php get_footer(); ?>
