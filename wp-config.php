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
define( 'DB_NAME', 'wordpress' );

/** MySQL database username */
define( 'DB_USER', 'wordpress' );

/** MySQL database password */
define( 'DB_PASSWORD', 'wordpress' );

/** MySQL hostname */
define( 'DB_HOST', 'wp-db:3306' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

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
define('AUTH_KEY',         'b|jkD_,w7nn$tuIG?}aJ.NTlrX!Ocu?w ;}-.Z5EOurX1G*nn!0Z3S-I@/?1`AZ?');
define('SECURE_AUTH_KEY',  '&au.ityaED>f_E4yv!K|8TpK4p@y_A>c*n3-X:m6P`}oL|HapQ v95Gpyyp/R7[/');
define('LOGGED_IN_KEY',    '91cWwT/{6AL~B[%LQg$37b0.obY9HAco%A%azih@4/?#rs70gvRa)^5V+:9-r f&');
define('NONCE_KEY',        'BEH5($!|g@R#5@e+;@v9jUu1~gNoX&lUrm&24qQIm+%XPR|UL4T9H)L-R$HG8r!P');
define('AUTH_SALT',        'wk(FtPMlX]v|6a2C<P_DX/(H7gG]XfJt`a5*#^~T{~*I#5Q /b^^ic+G>ro6wI3q');
define('SECURE_AUTH_SALT', 'i-`5R{abRajF.GwNg1u])@4D6N|^g>L]MAyYS1Dwx`|9%(7|9e{*r :)a-3JyAp[');
define('LOGGED_IN_SALT',   '51Xs;583E!LWBh3+/|>CXiQJa&fNA0SVN65+8xwqf35t|Z|(Z.-*}| Z`u,Mvs`Y');
define('NONCE_SALT',       'f-g|Z7{72uvYyE#.BdVu6x(GVEk<h0MB#G/Fz;_)U4|G=[%R]kO*3SRZu/ !&$h_');

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
