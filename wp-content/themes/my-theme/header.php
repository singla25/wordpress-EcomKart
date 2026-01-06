<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header class="site-header">
  <div class="header-container">
    
    <!-- Logo -->
    <div class="site-branding">
      <?php
      if ( function_exists( 'the_custom_logo' ) && has_custom_logo() ) {
          the_custom_logo();
      } else {
          echo '<a href="' . esc_url( home_url('/') ) . '" class="site-title">' . get_bloginfo('name') . '</a>';
      }
      ?>
    </div>

    <!-- Navigation -->
    <nav class="site-nav">
      <?php
      wp_nav_menu( array(
          'theme_location' => 'primary',
          'menu_class'     => 'nav-menu',
          'container'      => false,
      ) );
      ?>
    </nav>

    
    <!-- Login/Logout -->
    <div class="header-auth">
        <?php if ( is_user_logged_in() ) :  ?>
            <?php if (current_user_can('vendor')) : ?>
                <a href="<?php echo esc_url( home_url( '/vendor' ) ); ?>" 
                    class="text-white text-decoration-none px-5">
                    Vendor
                </a>
            <?php endif; ?>
            <a href="<?php echo esc_url( home_url( '?logout=logout' ) ); ?>" 
                class="logout-link text-white text-decoration-none">
                Logout
            </a>
        <?php else : ?>
        <a href="<?php echo esc_url( home_url('/login') ); ?>" 
            class="login-link text-white text-decoration-none">
            Login
        </a>    
        <?php endif; ?>
    </div>


    <!-- Search -->
    <div class="header-search">
        <form role="search" method="get" class="search-form" action="<?php echo home_url('/'); ?>">
            <div class="search-box">
                <input type="search" class="search-field" placeholder="Search…" value="<?php echo get_search_query(); ?>" name="s" />
                <button type="submit" class="search-submit">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M21.53 20.47l-5.27-5.27A7.92 7.92 0 0016 9a8 8 0 10-8 8 7.92 7.92 0 006.2-2.74l5.27 5.27a.75.75 0 101.06-1.06zM4 9a5 5 0 1110 0A5 5 0 014 9z"></path>
                    </svg>
                </button>
            </div>
        </form>
    </div>
  </div>
</header>





