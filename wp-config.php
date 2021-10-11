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
define( 'DB_NAME', 'boombox' );

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
define( 'AUTH_KEY',         '.wy1Kiv#dS>NT4SL]O/%1QJufuAoUU^e+/9HD;uoV`ImB*7,{{XXM45Sk.lk;Mi7' );
define( 'SECURE_AUTH_KEY',  ';7UHOKxvo)!4R (J:PnSY;3_}rQtk7&L.fWdzuc}l@Mj{:k#;^=L,eBuUN79X5)n' );
define( 'LOGGED_IN_KEY',    '*JZ(,a4?dZ`w-|Q*%&-MQ&zyI4 vDYo;2hAnD/^2=#gQ2?D.^I)+q^{Q~omH[x5Y' );
define( 'NONCE_KEY',        ';XPDIX  hesJ{)z1pNU.xs!(ic{d=%O4i5ETPPeh!H.U~{=#5uGt)$ir*%g*cL#>' );
define( 'AUTH_SALT',        '%>Sc#cqiAk5R@F~`xD9H1hT1Y~icX9@b? :82`<P8+hRG:p?QI9e>.kjr~4[:{^~' );
define( 'SECURE_AUTH_SALT', '_}%i4Ec)N&IFhm*+@<G&DfUhJBIJ]jy?S7Rd(i_Q_a?bh!lxkoVcH#<WQBK[kH8K' );
define( 'LOGGED_IN_SALT',   'F*&Zhp=+E`a$8kuZ:+V(=aiAgLLR#XST5ut$(ew=-oo,ILDO%2QYC&sRjU[cUjLU' );
define( 'NONCE_SALT',       'FxqNKZ-0kYl,Jn?*ArN8SB5R6;/f*`Y)v6jYFC%w{^~b>G^S9$j6d#t^wfjqMucF' );

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'boombox_';

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
