<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'wordpress-new' );

/** Database username */
define( 'DB_USER', 'admin' );

/** Database password */
define( 'DB_PASSWORD', 'redhat' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'd5B@+c>@Z!M-(v7h9;SrA+:H4G[=kARzyheq (!sd IMtZ?2-&94Or3EUuF,F`,g' );
define( 'SECURE_AUTH_KEY',  '8g)`shctcJ+c}KbAAeS#wq1`?c`N({yM*cw<LT<-i$SF`<m}Qz(Z^g2R{i8(Lzy<' );
define( 'LOGGED_IN_KEY',    'GQX<30#8+?&!/-/C~u[Vh-kngNRPGWgK$TNjgz.P5bEoQir~)dJfNsS7sO}XJYAM' );
define( 'NONCE_KEY',        '$lwS%vTOUslvZm73<zV|.Ll_8vH,;j}@P?~mJ[foMg~p$DAZxo&78iWke8{d)xmj' );
define( 'AUTH_SALT',        'D*wY5~39 Z/)FsMu&s.[o@v&dYE J$1<O,JK5f3tf95UuJv)q;J;vf&LRsLFs(J~' );
define( 'SECURE_AUTH_SALT', '$UFb>9g`A<bn!)<yR-IeMSLkTG(&$z+^|tps@@ [)k2:#5-iim:>]|~oSoPXLp1Y' );
define( 'LOGGED_IN_SALT',   'Q`aT~TrsgqiZJtn>&D;4pEIzI59DJ^rFOtyv6TL5lK Burf:4QEGFD>|JQtn f_p' );
define( 'NONCE_SALT',       ')a]Xr4Oog4}gBZ3]&S~v)0XJ#z: ^`TYTNi@jjYlH2MM?{X.na^SO.g}k5O}2`C ' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 *
 * At the installation time, database tables are created with the specified prefix.
 * Changing this value after WordPress is installed will make your site think
 * it has not been installed.
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/#table-prefix
 */
$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
 */
define( 'WP_DEBUG', true );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
