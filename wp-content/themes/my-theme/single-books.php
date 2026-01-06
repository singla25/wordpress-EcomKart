<?php get_header(); ?>

<div class="bg-dark bg-opacity-50 p-5 shadow-sm">
    <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
        
        <!-- Book Title -->
        <div class="row mb-4">
            <div class="col-12 text-center">
                <h1 class="fw-bold display-5 mb-3"><?php the_title(); ?></h1>
                <hr class="w-25 mx-auto">
            </div>
        </div>

        <div class="row g-5">
            <div class="col-md-2"></div>

            <!-- Book Content -->
            <div class="col-md-8">
                
                <!-- 📷 Uploaded Images -->
                <?php 
                $images = get_post_meta(get_the_ID(), 'book_images', true);
                if (!empty($images) && is_array($images)) : ?>
                    <div class="p-4 border rounded shadow-sm bg-white mb-4">
                        <h4 class="fw-bold mb-3 text-center">Book Gallery</h4>
                        <div class="d-flex flex-wrap justify-content-center gap-3">
                            <?php foreach ($images as $img_url) : ?>
                                <img src="<?php echo esc_url($img_url); ?>" 
                                     alt="<?php the_title_attribute(); ?>" 
                                     class="img-thumbnail shadow-sm"
                                     style="width:150px; height:200px; object-fit:cover;">
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- About -->
                <div class="p-4 border rounded shadow-sm bg-white mb-4">
                    <h4 class="fw-bold mb-3 text-center">About this Book</h4>
                    <div class="content text-muted">
                        <?php the_content(); ?>
                    </div>

                    <div class="row g-3 p-3">
                        <?php 
                        $price = get_post_meta(get_the_ID(), 'bookprice', true);
                        $tagline = get_post_meta(get_the_ID(), 'booktagline', true);
                        ?>
                        <?php if ($tagline) : ?>
                            <div class="col-md-6">                                
                                <h6 class="fw-bold">✨ Tagline</h6>
                                <p class="mb-0 text-muted"><?php echo esc_html($tagline); ?></p>
                            </div>
                        <?php endif; ?>

                        <?php if ($price) : ?>
                            <div class="col-md-6">                                
                                <h6 class="fw-bold">💰 Price</h6>
                                <p class="mb-0 text-success fw-bold">₹<?php echo number_format($price, 2); ?></p>                               
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Success Message -->
                <?php if ( isset($_GET['submitted']) && $_GET['submitted'] == 1 ) : ?>
                    <div class="alert alert-success text-center mt-4 fw-semibold">✅ Your question has been submitted!</div>
                <?php endif; ?>

                <!-- Question Form -->
                <?php echo do_shortcode('[question_form]'); ?>
            </div>

            <div class="col-md-2"></div>
        </div>
    <?php endwhile; endif; ?>
</div>

<?php get_footer(); ?>
