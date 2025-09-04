<?php
/**
 * Template Name: Service Page - Electronics (Mobiles & Tabs)
 */
get_header();
?>

<div class="bg-dark bg-opacity-50 p-5 shadow-sm">
  <div class="row">
    <!-- Mobiles Section -->
    <div class="col-md-3">
        <?php echo do_shortcode('[post_filters post_type="mobiles" taxonomies="feature-mob,price-mob"]'); ?>
    </div>
    <div class="col-md-9 text-white">
        <?php echo do_shortcode('[post_results post_type="mobiles"]'); ?>
    </div>

    <!-- Tabs Section -->
    <div class="col-md-3">
        <?php echo do_shortcode('[post_filters post_type="tabs" taxonomies="feature-tab,price-tab"]'); ?>
    </div>
    <div class="col-md-9 text-white">
        <?php echo do_shortcode('[post_results post_type="tabs"]'); ?>
    </div>
  </div>
</div>

<?php get_footer(); ?>

