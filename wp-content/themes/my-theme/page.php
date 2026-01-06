<?php
/**
 * The template for displaying all pages
 *
 * @package YourThemeName
 */

get_header(); ?>

<!-- Dark Background Wrapper -->
<div class="bg-dark bg-opacity-50 p-5 shadow-sm">
    <div class="container">
        <?php 
        if ( have_posts() ) :
            while ( have_posts() ) : the_post(); ?>
                
                <article id="page-<?php the_ID(); ?>" <?php post_class(); ?>>
                    
                    <!-- Page Title -->
                    <div class="row mb-4 text-center text-white">
                        <div class="col-12">
                            <h1 class="fw-bold display-5 mb-3"><?php the_title(); ?></h1>
                            <hr class="w-25 mx-auto border-light">
                        </div>
                    </div>

                    <div class="row g-5">
                        <div class="col-md-2"></div>
                        <div class="col-md-8">
                            <!-- White Card Box -->
                            <div class="p-4 border rounded shadow-sm bg-white text-dark">
                                <div class="page-content">
                                    <?php the_content(); ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2"></div>
                    </div>
                </article>

            <?php endwhile;
        else : ?>
            <p class="text-white text-center"><?php esc_html_e( 'Sorry, no pages found.', 'yourthemename' ); ?></p>
        <?php endif; ?>
    </div>
</div>

<?php get_footer(); ?>
