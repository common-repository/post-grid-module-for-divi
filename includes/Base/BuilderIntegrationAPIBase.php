<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase

/**
 * The Builder Integration API Base class.
 *
 * @since       1.0.0
 * @package     post-grid-module-for-divi
 * @author      WP Squad <wp@thewpsquad.com>
 * @copyright   2023 WP Squad
 * @license     GPL-3.0-only
 */

namespace SquadPostGrid\Base;

/**
 * Builder Integration API Base Class.
 *
 * @since       1.0.0
 * @package     post-grid-module-for-divi
 */
abstract class BuilderIntegrationAPIBase {

	/**
	 * The plugin name.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $name = '';

	/**
	 * Absolute path to the plugin's directory.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $plugin_dir = '';

	/**
	 * The plugin's directory URL.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $plugin_dir_url = '';

	/**
	 * The plugin's version
	 *
	 * @since 1.0.0
	 *
	 * @var string The plugin's version
	 */
	protected $version = '';

	/**
	 * The asset build for the plugin
	 *
	 * @since 1.0.0
	 *
	 * @var string The plugin's version
	 */
	protected $build_path = 'build/';

	/**
	 * Dependencies for the plugin's JavaScript bundles.
	 *
	 * @since 1.0.0
	 *
	 * @var array {
	 *                          JavaScript Bundle Dependencies
	 *
	 * @type string[] $builder  Dependencies for the builder bundle
	 * @type string[] $frontend Dependencies for the frontend bundle
	 *                          }
	 */
	protected $bundle_dependencies = array();

	/**
	 * Constructor.
	 *
	 * @param string $name           The plugin's WP Plugin name.
	 * @param string $plugin_dir     Absolute path to the plugin's directory.
	 * @param string $plugin_dir_url The plugin's directory URL.
	 */
	public function __construct( $name, $plugin_dir, $plugin_dir_url ) {
		// Set required variables as per definition.
		$this->name           = $name;
		$this->plugin_dir     = $plugin_dir;
		$this->plugin_dir_url = $plugin_dir_url;

		$this->initialize();
	}

	/**
	 * Get the plugin version number
	 *
	 * @return string
	 */
	abstract public function get_version();
}
