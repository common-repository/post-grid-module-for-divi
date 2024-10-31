<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase

namespace SquadPostGrid\Base;

use SquadPostGrid\Manager\Modules;
use function load_plugin_textdomain;

/**
 * The Base class for Core
 *
 * @since       1.0.0
 * @package     post-grid-module-for-divi
 * @author      WP Squad <wp@thewpsquad.com>
 * @copyright   2023 WP Squad
 * @license     GPL-3.0-only
 */
abstract class Core {

	/** The instance of the module class.
	 *
	 * @var Modules
	 */
	protected $modules;

	/**
	 * The Plugin name.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $name;

	/**
	 * The plugin option prefix
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $option_prefix;

	/**
	 * The full file path to the directory containing translation files.
	 *
	 * @var string
	 */
	protected $localize_path;

	/**
	 * Initialize the plugin with required components.
	 *
	 * @param array $options Options.
	 *
	 * @return void
	 */
	abstract protected function init( $options = array() );

	/**
	 * Load all core components.
	 *
	 * @return void
	 */
	abstract protected function load_core_components();

	/**
	 * Load all divi modules.
	 *
	 * @return void
	 */
	abstract protected function load_divi_modules_for_builder();

	/**
	 * Get the instance of memory.
	 *
	 * @return Memory
	 */
	abstract public function get_memory();

	/**
	 * Set the instance of memory.
	 *
	 * @param string $prefix The prefix name for the plugin settings option.
	 *
	 * @return Memory
	 */
	abstract public function set_memory( $prefix );

	/**
	 * Get the plugin name.
	 *
	 * @return string
	 */
	public function get_name() {
		return $this->name;
	}

	/**
	 * Get the instance of modules.
	 *
	 * @return Modules
	 */
	public function get_modules() {
		return $this->modules;
	}

	/**
	 * Load the local text domain.
	 *
	 * @return void
	 */
	public function load_text_domain() {
		load_plugin_textdomain( $this->name, false, "{$this->name}/languages" );
	}

	/**
	 * Set the deactivation hook.
	 *
	 * @return void
	 */
	public function hook_deactivation() {
		$this->get_memory()->set( 'version', $this->get_version() );
		$this->get_memory()->set( 'deactivation_time', time() );
	}
}
