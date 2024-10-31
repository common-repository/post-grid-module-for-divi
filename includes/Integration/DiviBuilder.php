<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase

/**
 * The main class for Post Grid Module for Divi.
 *
 * @since       1.0.0
 * @package     post-grid-module-for-divi
 * @author      WP Squad <wp@thewpsquad.com>
 * @copyright   2023 WP Squad
 * @license     GPL-3.0-only
 */

namespace SquadPostGrid\Integration;

use SquadPostGrid\Base\BuilderIntegrationAPI;
use function squadpostgrid;

/**
 * Post Grid Module for Divi Class.
 *
 * @since       1.0.0
 * @package     post-grid-module-for-divi
 */
class DiviBuilder extends BuilderIntegrationAPI {

	/**
	 * Get the plugin version number
	 *
	 * @return string
	 */
	public function get_version() {
		return squadpostgrid()->get_version();
	}

	/**
	 * Loads custom modules when the builder is ready.
	 *
	 * @since 1.0.0
	 */
	public function hook_et_builder_ready() {
		squadpostgrid()->get_modules()->load_divi_builder_4_modules( dirname( __DIR__ ) );
	}
}
