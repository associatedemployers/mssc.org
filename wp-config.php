<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information
 * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', '2013_mssc');

/** MySQL database username */
define('DB_USER', 'ur_mssc_a');

/** MySQL database password */
define('DB_PASSWORD', '4Un#ubS5DgvA');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');


define('FTP_HOST', 'localhost');
define('FTP_USER', 'usr_mssc');
define('FTP_PASS', 'xp4!&NbX7M9y');


/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '~BAS06zpv-EGsRne=^*Hi0QM} >X16-DqRkKUH[2Tv_y|Q5HjOU0|wqcoox#uMH8');
define('SECURE_AUTH_KEY',  '&gc+<:JE*/>.-3-r#KBzm;Zx6|g*cL_4]%gd+|pE`Yi FxE{*sfy2+64<7FbB]EF');
define('LOGGED_IN_KEY',    '`Pl.,4~sv22H-Xm}w )Ho} KCOlqG]Q,Lf.PM!#|}sZg0h`P1j,$hGCcZ^Y90K+)');
define('NONCE_KEY',        '3)i6TL`nA5M|#%}tjQASv+j!9Ob6|>@Q^wUI|[W|P-i?/.Lw(e9=|`mlH.qkq|;2');
define('AUTH_SALT',        '{HQ--m=5UBiRW0|isS!]}F.1c9) 2/P|=7i]k<].Q0MT9B,-(umR9>^l=T/3^#_|');
define('SECURE_AUTH_SALT', '`yoZ1-!-W;xLnX`Ne;85P:K~L17>TYA[y6|_5bfJqqQO7iF3B7m?9ox3R[=+|f6!');
define('LOGGED_IN_SALT',   'Ee-sp~op3sSlmI{$SE5g.b3t|mT-+(zd+K@&#u$U5!uznDkei6q&-wkjaPK4]q/Z');
define('NONCE_SALT',       'Z0:}Dl1RH@sl8QDjt#=houN3ID,W^3iqLdV(&+(-$+!BGc`to#4R0-lmQchF+3!Z');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * WordPress Localized Language, defaults to English.
 *
 * Change this to localize WordPress. A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de_DE.mo to wp-content/languages and set WPLANG to 'de_DE' to enable German
 * language support.
 */
define('WPLANG', '');

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
