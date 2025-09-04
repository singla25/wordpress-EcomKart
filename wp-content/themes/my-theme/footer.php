<footer class="site-footer">
    <div class="container">

        <!-- Row 1: Logo -->
        <div class="footer-top">
            <div class="footer-logo">
                <?php
                if ( function_exists( 'the_custom_logo' ) && has_custom_logo() ) {
                    the_custom_logo();
                } else {
                    echo '<h2 class="footer-site-title"><a href="' . esc_url( home_url('/') ) . '">' . get_bloginfo('name') . '</a></h2>';
                }
                ?>
            </div>
        </div>

        <!-- Row 2: Widgets -->
        <div class="footer-widgets">
            <div class="footer-column">
                <?php if ( is_active_sidebar( 'footer-1' ) ) : ?>
                    <?php dynamic_sidebar( 'footer-1' ); ?>
                <?php endif; ?>
            </div>
            
            <div class="footer-column">
                <?php if ( is_active_sidebar( 'footer-2' ) ) : ?>
                    <?php dynamic_sidebar( 'footer-2' ); ?>
                <?php endif; ?>
            </div>
            
            <div class="footer-column">
                <?php if ( is_active_sidebar( 'footer-3' ) ) : ?>
                    <?php dynamic_sidebar( 'footer-3' ); ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Row 3: Bottom -->
        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?>. All Rights Reserved.</p>
        </div>
    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
