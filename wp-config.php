<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/documentation/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'numinous' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

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
define( 'AUTH_KEY',         'fTk|P;xr.t,MgyLkklz% 3LQZpt^B#K,d6y3(r9x8X;q>kgFe<W4 1nenFyVFp&R' );
define( 'SECURE_AUTH_KEY',  'he8]Tn@J<c2t*A)DwC<>=6xUxQ3kI<R<fhv[<,%a!4F`ueUn!a0@N/O*O!$No.[c' );
define( 'LOGGED_IN_KEY',    'TIo!1Q|VfgXK%K,k&#C5X_OZ3IN)c+~`WxC#{+JT$Xc{688fe]dO+a03ml[_!o1o' );
define( 'NONCE_KEY',        ']~gE]!5S)oxIyf>.Q>[!(>&i<IYi|}(iGqE:zbO]K!u/}s{z$;lp^W0.<b9#4qdw' );
define( 'AUTH_SALT',        'AD=ABwO81hV{u 8.DaN{1HF)~2ZVs1:~i]4}79|/`RSAco!=_y],R5R$esm2}7(l' );
define( 'SECURE_AUTH_SALT', 'PtQaYsb>h_]_]!nKo/PLOOn7V(ZH!Fm]<;()XpDPe3FY-3:}tV>>/x4gO~TxcC6x' );
define( 'LOGGED_IN_SALT',   'm^rNbbSwVk}c%nhlRv9XOFtf/aa#</w|}t9_MpKerWi=sgD2W`u%r}=$(ars857J' );
define( 'NONCE_SALT',       '5oMoj#:[9L56Gwd(yr.iU}aF`!?2e:4MFXYj4Cswo#r5VOiVU72iz4%>Z/n^F6Ox' );

/**#@-*/

/**
 * WordPress database table prefix.
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
 * visit the documentation.
 *
 * @link https://wordpress.org/documentation/article/debugging-in-wordpress/
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
