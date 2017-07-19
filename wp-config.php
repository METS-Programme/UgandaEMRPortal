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
define('DB_NAME', 'ugemr_portal');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', 'METSUganda321');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '2N%j9T1Ys+?XiVN*n*q=+q1sqj!;O!etP2>H/Rv]RI*UCv?&RVEJs1P8d.i@V*wu');
define('SECURE_AUTH_KEY',  'r`N71VVegnjUUe%g2Gv<k!:zSxaDT4jaQOPu-Qd|3h@&F!MH^# 0e6R%)iE{2D ;');
define('LOGGED_IN_KEY',    ' ~q3y<GWx-YrjVX7S,ih8BVI]`GU7eq9kT.3_B~BZ =7(FHBAcjRL2qE[ IK15&7');
define('NONCE_KEY',        '!TPHDM{Gk;}Io*preBzD:ia8f8z G9G4z|_[>nKQqMQ!>IiK:eW|28NE;ED6f}DF');
define('AUTH_SALT',        'cmGev$dspf):%G/o3a|E SY;Dx3i%ITW0q>?T.L)ct,8B!DQ3x!dV3z=Y{@e=a`j');
define('SECURE_AUTH_SALT', '+XT|Y.J/;SK&Hd<gmei$vO3iy)U[=-~JxOT(PsZ`E6WhcNaE/$Kgb.}M8k*izBr0');
define('LOGGED_IN_SALT',   '%bngS9*G7s{>Hljtvj4k~{.+uSWID366-BgT?<:56BvuDd$I4]m/aQNK-W?}&<w ');
define('NONCE_SALT',       'nCkVoK{8_&z:EGC;E2p$*a;WQ:a1mw!~E%O@c5]:!jetnHX,M*5)&#XnxehO(UTp');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

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
define('WP_DEBUG', true);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
define('WP_HOME','http://emrportal.mets.or.ug');
define('WP_SITEURL','http://emrportal.mets.or.ug');