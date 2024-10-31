<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase
/**
 * Squad Post Grid Module
 *
 * @package     post-grid-module-for-divi
 * @author      WP Squad <wp@thewpsquad.com>
 * @license     GPL-3.0-only
 */

namespace SquadPostGrid;

use SquadPostGrid\Base\Memory;

/**
 * Free Plugin Load class.
 *
 * @since           1.0.0
 * @package         post-grid-module-for-divi
 * @author          WP Squad <support@thewpsquad.com>
 * @license         GPL-3.0-only
 */
final class PostGridModule extends Integration\Core {

	/**
	 * The instance of current class.
	 *
	 * @var self
	 */
	private static $instance;

	/**
	 * The instance of Memory class.
	 *
	 * @var Memory
	 */
	protected $plugin_memory;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->name          = 'post-grid-module-for-divi';
		$this->option_prefix = 'squad_post_grid';

		// translations.
		$this->localize_path = __DIR__;
	}

	/**
	 * Get the plugin version number
	 *
	 * @return string
	 */
	public function get_version() {
		return SQUADPOSTGRID_VERSION;
	}

	/**
	 * Get the plugin version number
	 *
	 * @param string $prefix The prefix name for the plugin settings option.
	 *
	 * @return Memory
	 */
	public function set_memory( $prefix ) {
		$this->plugin_memory = new Memory( $prefix );

		return $this->get_memory();
	}

	/**
	 * Get the plugin version number
	 *
	 * @return Memory
	 */
	public function get_memory() {
		return $this->plugin_memory;
	}

	/**
	 *  The instance of current class.
	 *
	 * @return self
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof self ) ) {
			self::$instance = new self();

			self::$instance->set_memory( self::$instance->option_prefix );
			self::$instance->init();

			error_log( 'deubg');

			// Load the core.
			$wp = new Integration\WP();
			$wp->let_the_journey_start(
				static function () {
					self::$instance->load_text_domain();
					self::$instance->load_core_components();
					self::$instance->load_divi_modules_for_builder();
				}
			);
		}

		return self::$instance;
	}
}
