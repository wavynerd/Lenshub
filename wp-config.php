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
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'lenshubn_wp136' );

/** MySQL database username */
define( 'DB_USER', 'lenshubn_wp136' );

/** MySQL database password */
define( 'DB_PASSWORD', '5.p79SQ[9A' );

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
define( 'AUTH_KEY',         'n4cddmlt1ymj6pnpf70swaosmwhjtnl8jfaviwuaq4ststs6dwaekoxhm6krzlav' );
define( 'SECURE_AUTH_KEY',  'vswwj6rcczmnsgp6kfngf55inu7cbixh7jlnrr8zfz8oin2zgcohtjjhozu4cfkg' );
define( 'LOGGED_IN_KEY',    'ffbrwcwpu4ltdxrb8ebvb5nd2jwavvsas5jzrvt5qob24p5ol9mgwye6ogbbws2e' );
define( 'NONCE_KEY',        'nu6v3w2nmnfh00a2ullxoltrjmpoiy4hweucswg2fdamsqgr0n2mn94hnsk4oo2u' );
define( 'AUTH_SALT',        'k2fayiawg7pfpncbljmfey6keoiox6nxgy01fxzbsk4rk2k0fbwtn0reajtutpvf' );
define( 'SECURE_AUTH_SALT', 'iysbvcfsqfewfbxlpc0m9z38e47hwnjh9upifybhhvwkveihzi1cosqfhjjbz7ri' );
define( 'LOGGED_IN_SALT',   'e1xjaj5messfnfyirou349xrc6vlwy86m5weeotnf9mp8wvffne4tdndepxifep9' );
define( 'NONCE_SALT',       'vv6itrrh7jmf3mfl6bwbxf45zt6flre9bo8okqksvstt7jtxtkz02wz8h0mshpis' );

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wpyz_';

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
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );
	
define('WP_HOME','http://lenshub.ng');
define('WP_SITEURL','http://lenshub.ng');

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
