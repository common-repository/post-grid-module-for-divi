<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase

/**
 * Squad Post Grid Module
 *
 * @package     squad-post-grid
 * @author      WP Squad <wp@thewpsquad.com>
 * @license     GPL-3.0-only
 *
 * @wordpress-plugin
 * Plugin Name:         Squad Post Grid Module
 * Plugin URI:          https://squadmodules.com/
 * Description:         Display your blog posts in a stylish and organized grid layout.
 * Version:             1.1.0
 * Requires at least:   5.0.0
 * Requires PHP:        5.6.40
 * Author:              WP Squad
 * Author URI:          https://thewpsquad.com/
 * License:             GPL-3.0-only
 * License URI:         https://www.gnu.org/licenses/gpl-3.0.en.html
 * Text Domain:         post-grid-module-for-divi
 * Domain Path:         /languages
 */

defined( 'ABSPATH' ) || die();

/**
 * Autoload function.
 *
 * @param string $class_name Class name.
 *
 * @return void
 */
spl_autoload_register(
	static function ( $class_name ) {
		// Bail out if the class name doesn't start with our prefix.
		if ( 0 !== strpos( $class_name, 'SquadPostGrid\\' ) ) {
			return;
		}

		// Replace the namespace separator with the path prefix and the directory separator.
		$class_path_name = str_replace( 'SquadPostGrid\\', '/includes/', $class_name );
		$valid_path_name = str_replace( array( '\\', '//' ), DIRECTORY_SEPARATOR, $class_path_name );

		// Add the .php extension.
		$file_path = __DIR__ . $valid_path_name . '.php';

		if ( file_exists( $file_path ) ) {
			require_once $file_path;
		}
	}
);

// Fixed the free plugin load issue in the live site.
if ( ! class_exists( SquadPostGrid\PostGridModule::class ) ) {
	return;
}

// Define the core constants.
define( 'SQUADPOSTGRID__FILE__', __FILE__ );
define( 'SQUADPOSTGRID_DIR_PATH', __DIR__ );
define( 'SQUADPOSTGRID_PLUGIN_BASE', plugin_basename( SQUADPOSTGRID__FILE__ ) );
define( 'SQUADPOSTGRID_DIR_URL', plugin_dir_url( SQUADPOSTGRID__FILE__ ) );
define( 'SQUADPOSTGRID_ASSET_URL', trailingslashit( SQUADPOSTGRID_DIR_URL . 'build' ) );
define( 'SQUADPOSTGRID_MODULES_ICON_DIR_PATH', SQUADPOSTGRID_DIR_PATH . '/build/icons' );

// Define the general constants for the plugin.
define( 'SQUADPOSTGRID_VERSION', '1.1.0' );
define( 'SQUADPOSTGRID_MINIMUM_DIVI_VERSION', '4.14.0' );
define( 'SQUADPOSTGRID_MINIMUM_PHP_VERSION', '5.6.40' );
define( 'SQUADPOSTGRID_MINIMUM_WP_VERSION', '5.0.0' );

/**
 * The instance of Squad Post Grid Module Plugin.
 *
 * @return SquadPostGrid\PostGridModule
 */
function squadpostgrid() {
	return SquadPostGrid\PostGridModule::get_instance();
}

// Load the plugin.
squadpostgrid();
