<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase
/**
 * The DiviBuilderBackend integration helper for Divi Builder
 *
 * @since       1.0.0
 * @package     post-grid-module-for-divi
 * @author      WP Squad <wp@thewpsquad.com>
 * @copyright   2023 WP Squad
 * @license     GPL-3.0-only
 */

namespace SquadPostGrid\Base;

use function _x;

/**
 * Builder DiviBuilderBackend Placeholder class.
 *
 * @since       1.0.0
 * @package     post-grid-module-for-divi
 */
abstract class BuilderBackendPlaceholder {

	/** The instance of the current class.
	 *
	 * @var self
	 */
	protected static $instance;

	/**
	 * The default data for module.
	 *
	 * @var array
	 */
	protected $modules_defaults;

	/**
	 *  Get The defaults data for module.
	 *
	 * @return array
	 */
	public function get_modules_defaults() {
		return array(
			'custom_text'     => _x( 'Custom Text Here', 'Modules dummy content', 'post-grid-module-for-divi' ),
			'read_more'       => _x( 'Read More', 'Modules dummy content', 'post-grid-module-for-divi' ),
			'comments_before' => _x( 'Comments: ', 'Modules dummy content', 'post-grid-module-for-divi' ),
		);
	}

	/**
	 * Filters backend data passed to the Visual Builder.
	 * This function is used to add static helpers whose content rarely changes.
	 * eg: google fonts, module default, and so on.
	 *
	 * @param array $exists Exists definitions.
	 *
	 * @return array
	 */
	abstract public function static_asset_definitions( $exists = array() );

	/**
	 * Used to update the content of the cached definitions js file.
	 *
	 * @param string $content content.
	 *
	 * @return string
	 */
	abstract public function asset_definitions( $content );
}
