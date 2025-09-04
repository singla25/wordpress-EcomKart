<?php get_header(); ?>

<div class="bg-dark bg-opacity-50 text-light p-5 shadow-sm">
    
    <!-- Search Title -->
    <div class="row mb-4">
        <div class="col-12 text-center">
            <h1 class="fw-bold display-5 text-white">🔍 Search Results</h1>
            <p class="text-muted">You searched for: <span class="fw-semibold text-white">"<?php echo get_search_query(); ?>"</span></p>
            <hr class="w-25 mx-auto border-light">
        </div>
    </div>

    <?php if ( have_posts() ) : ?>
        <div class="row g-4">
            <?php while ( have_posts() ) : the_post(); ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 shadow-lg border-0 rounded-3 bg-secondary text-light">
                        
                        <!-- Thumbnail -->
                        <?php if ( has_post_thumbnail() ) : ?>
                            <a href="<?php the_permalink(); ?>">
                                <?php the_post_thumbnail( 'medium', ['class' => 'card-img-top rounded-top'] ); ?>
                            </a>
                        <?php endif; ?>

                        <div class="card-body d-flex flex-column">
                            <!-- Title -->
                            <h5 class="card-title fw-bold mb-2 text-white">
                                <a href="<?php the_permalink(); ?>" class="text-decoration-none text-white">
                                    <?php the_title(); ?>
                                </a>
                            </h5>

                            <!-- Excerpt -->
                            <p class="card-text text-light opacity-75 mb-3">
                                <?php echo wp_trim_words( get_the_excerpt(), 20 ); ?>
                            </p>

                            <!-- Read More -->
                            <a href="<?php the_permalink(); ?>" class="mt-auto btn btn-outline-light btn-sm rounded-pill">
                                Read More →
                            </a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>

        <!-- Pagination -->
        <div class="row mt-5">
            <div class="col-12 text-center">
                <?php the_posts_pagination([
                    'mid_size'  => 2,
                    'prev_text' => '« Prev',
                    'next_text' => 'Next »',
                ]); ?>
            </div>
        </div>
        
    <?php else : ?>
        <!-- No Results -->
        <div class="row">
            <div class="col-md-8 offset-md-2 text-center">
                <div class="alert alert-danger p-4 rounded shadow-lg bg-secondary text-white border-0">
                    <h4 class="fw-bold">😢 No results found</h4>
                    <p class="opacity-75">We couldn’t find anything for "<span class="fw-semibold"><?php echo get_search_query(); ?></span>".</p>
                </div>
                <?php get_search_form(); ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php get_footer(); ?>
