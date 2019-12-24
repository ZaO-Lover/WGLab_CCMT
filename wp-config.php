<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'wglab' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', '' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'O1IsJ}}]z!{)wa(B}w(~F]IM9ZFBoMJ4W)cDd0PQr|02us<3LNP8omdwbj_nPC<2' );
define( 'SECURE_AUTH_KEY',  'cF0Dh:0lm_}2(l?dA/X-5<7%P{v$X+[Q4x>%4>9CGEp^=8W}0LyI}s@=qk[G@pnK' );
define( 'LOGGED_IN_KEY',    '/lz#&{?BIb#HL(]{i.30@V]@mo5FD*)ch%/-CT8*Z^SQ7v{Q&=K#tn!EI0&o).1#' );
define( 'NONCE_KEY',        'T&DkU|6$11V;K=DR3wV9WOeDgQvR6NdL03EWtbD`S2Oi;deGZthV7s5cwoPJ3~Of' );
define( 'AUTH_SALT',        'xD?U=m~4uK=zH]R!Rik(xF(59!8HGHSMqMt5<J1SzJD,=[>&jGPVv5C19jtX(!Fq' );
define( 'SECURE_AUTH_SALT', '3F.2}c[4cW,<#W-(T*]*h JqiE[qIvD31C&vj5&^^ZKLlff(N#!i@[$NcDbDEj H' );
define( 'LOGGED_IN_SALT',   'sT#%D3+/i5Di2`b9.cUN<)QF,HQ-MW31N,N{#y`7ZMr>-Xd6qzkT#D&g{|%@)?R.' );
define( 'NONCE_SALT',       'B9C-Wp5GDF+~,U5VPjy{##?M$h{=#s26IV]9oBha~D:c88@U!@f GkeUSJl;aC+1' );

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
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
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define( 'WP_DEBUG', false );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

/** Sets up WordPress vars and included files. */
require_once( ABSPATH . 'wp-settings.php' );
