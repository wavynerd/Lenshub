<?php

// include composer auto loads.
if ( ! class_exists( 'TGM_Plugin_Activation' ) ) {
	require_once get_parent_theme_file_path( 'includes/class-tgm-plugin-activation.php' );
}
require get_parent_theme_file_path( 'vendor/autoload.php' );

require get_parent_theme_file_path() . '/includes/hooks.php';
require get_parent_theme_file_path() . '/includes/assets.php';
add_action(
	'after_setup_theme',
	function () {
		/**
		 * Load textdomain.
		 */
		load_theme_textdomain( 'lisfinity', get_parent_theme_file_path() . '/languages' );

		/**
		 * Load theme setup
		 */
		require get_parent_theme_file_path( '/includes/merlin/vendor/autoload.php' );
		require get_parent_theme_file_path( '/includes/functions/functions-theme.php' );
		if ( is_admin() ) {
			require get_parent_theme_file_path( '/includes/merlin/class-merlin.php' );
			require get_parent_theme_file_path( '/includes/merlin/includes/wizard-config.php' );
		}
		require get_parent_theme_file_path( '/includes/helpers/functions-templates.php' );
		require get_parent_theme_file_path( '/includes/functions/functions-posts.php' );
		require get_parent_theme_file_path( '/includes/functions/functions-user.php' );
		require get_parent_theme_file_path( '/includes/helpers/helper-theme.php' );
		require get_parent_theme_file_path( '/includes/setup/theme-support.php' );
		require get_parent_theme_file_path( '/includes/setup/menus.php' );
		require get_parent_theme_file_path( '/includes/setup/sidebars.php' );
	}
);

