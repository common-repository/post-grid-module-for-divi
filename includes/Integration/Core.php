<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase

/**
 * The Core class for Post Grid Module for Divi.
 *
 * @since       1.0.0
 * @package     post-grid-module-for-divi
 * @author      WP Squad <wp@thewpsquad.com>
 * @copyright   2023 WP Squad
 * @license     GPL-3.0-only
 */

namespace SquadPostGrid\Integration;

use SquadPostGrid\Manager;
use function add_action;
use function add_filter;
use function squadpostgrid;
use function register_activation_hook;
use function register_deactivation_hook;

/**
 * Post Grid Module for Divi Core Class.
 *
 * @since       1.0.0
 * @package     post-grid-module-for-divi
 */
abstract class Core extends \SquadPostGrid\Base\Core {

	/**
	 * Load all core components.
	 *
	 * @return void
	 */
	protected function load_core_components() {
		$this->modules = new Manager\Modules();
	}

	/**
	 * Initialize the plugin with required components.
	 *
	 * @param array $options Options data.
	 *
	 * @return void
	 */
	protected function init( $options = array() ) {
		// Register all hooks for plugin.
		register_activation_hook( SQUADPOSTGRID__FILE__, array( $this, 'hook_activation' ) );
		register_deactivation_hook( SQUADPOSTGRID__FILE__, array( $this, 'hook_deactivation' ) );
	}

	/**
	 * Set the activation hook.
	 *
	 * @return void
	 */
	public function hook_activation() {
		$this->get_memory()->set( 'activation_time', time() );
		if ( squadpostgrid()->get_version() !== $this->get_memory()->get( 'version' ) ) {
			$this->get_memory()->set( 'previous_version', $this->get_memory()->get( 'version' ) );
		}
		$this->get_memory()->set( 'version', squadpostgrid()->get_version() );
	}

	/**
	 * Load the divi custom modules for the divi builder.
	 *
	 * @return void
	 */
	protected function load_divi_modules_for_builder() {
		// Register all hooks for divi integration.
		add_action( 'wp_loaded', array( $this, 'initialize_divi_asset_definitions' ) );
		add_action( 'divi_extensions_init', array( $this, 'initialize_divi_extension' ) );

		// Force the legacy backend builder to reload its template cache.
		// This ensures that custom modules are available for use right away.
		if ( function_exists( '\et_pb_force_regenerate_templates' ) ) {
			\et_pb_force_regenerate_templates();
		}
	}

	/**
	 *  Load the extensions.
	 *
	 * @return void
	 */
	public function initialize_divi_extension() {
		if ( class_exists( DiviBuilder::class ) ) {
			new DiviBuilder( $this->name, SQUADPOSTGRID_DIR_PATH, SQUADPOSTGRID_DIR_URL );
		}
	}

	/**
	 * Used to update the content of the cached definitions js file.
	 *
	 * @return void
	 */
	public function initialize_divi_asset_definitions() {
		if ( function_exists( 'et_fb_process_shortcode' ) && class_exists( DiviBuilderBackend::class ) ) {
			$helpers = new DiviBuilderBackend();
			add_filter( 'et_fb_backend_helpers', array( $helpers, 'static_asset_definitions' ), 11 );
			add_filter( 'et_fb_get_asset_helpers', array( $helpers, 'asset_definitions' ), 11 );
		}
	}
}
