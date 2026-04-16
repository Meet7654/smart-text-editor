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
define( 'DB_NAME', 'text-editor' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', 'root' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

define('FS_METHOD', 'direct');
define( 'WP_FS__DEV_MODE', true );
define( 'WP_FS__SKIP_EMAIL_ACTIVATION', true );
define( 'WP_FS__smart-text-editor_SECRET_KEY', 'sk_GWAY8#q@fky~Mu8hB%Tyvy6bOP^U6' );
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
define( 'AUTH_KEY',         'y1ogV/E1l(}-0w<ZOXn=g$T!uO%59_o(dZoPe!lAVe43GvJ/>6uqvgH3`/q4STK`' );
define( 'SECURE_AUTH_KEY',  'TDL,nu+=HgRqH*GwTsGN?zQ^G+7QZ~}~]a-]/!X6<fF(]H#i)/Cvl,.Tad:Tj[e@' );
define( 'LOGGED_IN_KEY',    'ie=150F{Xjv4$2;}TrYFW:N!Z-3ZBqru7]?l&?ekYD=dtwq#Ei5<4-!jPttG(r1q' );
define( 'NONCE_KEY',        '7HahihFqR,35}SX16aD3:8u(s/D5-XsrFx2$2w!1`q)jI>H0]ar?Ry:U&#f^qY&n' );
define( 'AUTH_SALT',        '34s.1dSLwexxf;s4KmW*J9RdNzQ[fn,T4KP`+>%1j4R?-%7E+oSWesYdyHa6]dAH' );
define( 'SECURE_AUTH_SALT', 'txU:G4xH,?Yl7CY Qs/z!E:(alO*JP#ahRdUhICrbf9*r%L7]n6{^P_,OCjAVkKG' );
define( 'LOGGED_IN_SALT',   '^Qxr$#Beo9PwEO3z7;AN$)|`W-yck&^t^T84wQW!`f,?|a)xiAj!W==fT6{H1)F`' );
define( 'NONCE_SALT',       'q8lc0d$4dB>l=:_^Fy08:D?0G4iF`BIeq.;_sYQ)s/Rc!xdT5=opOjG6yg14n554' );

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
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
