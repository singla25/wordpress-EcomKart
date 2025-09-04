<?php
/**
 * The template for displaying single posts
 *
 * @package YourThemeName
 */

get_header(); ?>

<div class="bg-dark bg-opacity-50 p-5 shadow-sm">
    <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
        
        <!-- Post Title -->
        <div class="row mb-4 text-center text-white">
            <div class="col-12">
                <h1 class="fw-bold display-5 mb-3"><?php the_title(); ?> 🚀</h1>
                <p class="text-light opacity-75 mb-0">
                    Posted on <?php echo get_the_date(); ?> by 
                    <span class="fw-semibold"><?php the_author(); ?></span>
                </p>
                <hr class="w-25 mx-auto border-light">
            </div>
        </div>

        <div class="row g-5">
            <div class="col-md-2"></div>
            <div class="col-md-8">
                <!-- White Content Box with Dark Text -->
                <div class="p-4 border rounded shadow-sm bg-white mb-4 text-dark">
                    
                    <!-- Featured Image -->
                    <?php if ( has_post_thumbnail() ) : ?>
                        <div class="text-center mb-4">
                            <?php the_post_thumbnail( 'large', ['class' => 'img-fluid rounded shadow-sm'] ); ?>
                        </div>
                    <?php endif; ?>

                    <!-- Post Content -->
                    <h4 class="fw-bold mb-3">About this Post</h4>
                    <div class="content">
                        <?php the_content(); ?>
                    </div>

                    <!-- Post Tags -->
                    <?php if ( get_the_tags() ) : ?>
                        <div class="mt-4">
                            <?php the_tags( '<p class="fw-semibold">Tags: ', ', ', '</p>' ); ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Success Message -->
                <?php if ( isset($_GET['submitted']) && $_GET['submitted'] == 1 ) : ?>
                    <div class="alert alert-success text-center mt-4 fw-semibold">
                        ✅ Your question has been submitted!
                    </div>
                <?php endif; ?>

                <!-- Question Form -->
                <div class="text-dark">
                    <?php echo do_shortcode('[question_form]'); ?>
                </div>
            </div>
            <div class="col-md-2"></div>
        </div>

    <?php endwhile; endif; ?>
</div>

<?php get_footer(); ?>
