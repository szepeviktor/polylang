<?php

define( 'ABSPATH', '' );
define( 'WP_DEBUG', true );
define( 'WP_CONTENT_DIR', '' );
define( 'WP_PLUGIN_DIR', '' );
define( 'WP_LANG_DIR', '' );
define( 'COOKIEPATH', '' );
define( 'COOKIE_DOMAIN', '' );

define( 'MINUTE_IN_SECONDS', 60 );
define( 'HOUR_IN_SECONDS',   60 * MINUTE_IN_SECONDS );
define( 'DAY_IN_SECONDS',    24 * HOUR_IN_SECONDS );
define( 'WEEK_IN_SECONDS',    7 * DAY_IN_SECONDS );
define( 'MONTH_IN_SECONDS',  30 * DAY_IN_SECONDS );
define( 'YEAR_IN_SECONDS',  365 * DAY_IN_SECONDS );
define( 'OBJECT', 'OBJECT' );
define( 'OBJECT_K', 'OBJECT_K' );
define( 'ARRAY_A', 'ARRAY_A' );
define( 'ARRAY_N', 'ARRAY_N' );

define( 'POLYLANG_VERSION', '2.6-dev' );
define( 'PLL_MIN_WP_VERSION', '4.7' );
define( 'POLYLANG_FILE', __FILE__ );
define( 'POLYLANG_BASENAME', plugin_basename( POLYLANG_FILE ) );
define( 'POLYLANG_DIR', dirname( POLYLANG_FILE ) );
define( 'POLYLANG', ucwords( str_replace( '-', ' ', dirname( POLYLANG_BASENAME ) ) ) );
define( 'PLL_ADMIN_INC', POLYLANG_DIR . '/admin' );
define( 'PLL_FRONT_INC', POLYLANG_DIR . '/frontend' );
define( 'PLL_INC', POLYLANG_DIR . '/include' );
define( 'PLL_INSTALL_INC', POLYLANG_DIR . '/install' );
define( 'PLL_MODULES_INC', POLYLANG_DIR . '/modules' );
define( 'PLL_SETTINGS_INC', POLYLANG_DIR . '/settings' );

define( 'PLL_SETTINGS', true );
define( 'PLL_ADMIN', true );
define( 'PLL_COOKIE', 'pll_language' );

function vip_safe_wp_remote_get( $p ) {}
