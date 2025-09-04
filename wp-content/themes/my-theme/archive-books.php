<?php
/**
 * Template Name: Archive Books Page
 */
get_header();
?>
<div class="bg-dark bg-opacity-50 p-5 shadow-sm">
  <div class="row">
    <div class="col-md-3">
        <?php echo do_shortcode('[post_filters post_type="books" taxonomies="genre,author"]'); ?>
    </div>
    <div class="col-md-9 text-light">
        <?php echo do_shortcode('[post_results post_type="books" taxonomies="genre,author" per_page="9"]'); ?>
    </div>
  </div>
</div>
<?php get_footer(); ?>