<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase

namespace SquadPostGrid\Manager;

/**
 * Modules class
 *
 * @since       1.0.0
 * @package     post-grid-module-for-divi
 * @author      WP Squad <wp@thewpsquad.com>
 * @copyright   2023 WP Squad
 * @license     GPL-3.0-only
 */
class Modules {

	/**
	 *  Get available modules.
	 *
	 * @return array[]
	 */
	public function get_available_modules() {
		return array(
			array(
				'name'               => 'PostGrid',
				'label'              => esc_html__( 'Post Grid', 'post-grid-module-for-divi' ),
				'description'        => esc_html__( 'Display your blog posts in a stylish and organized grid layout.', 'post-grid-module-for-divi' ),
				'child_name'         => 'PostGridChild',
				'child_label'        => esc_html__( 'Post Element', 'post-grid-module-for-divi' ),
				'release_version'    => '1.0.0',
				'is_default_active'  => true,
				'is_premium_feature' => false,
			),
		);
	}

	/**
	 * Get the module path
	 *
	 * @param string $path        The module class path.
	 * @param string $module_name The module name.
	 *
	 * @return string
	 */
	protected function get_module_path( $path, $module_name ) {
		return sprintf( '%1$s/Modules/%2$s/%2$s.php', $path, $module_name );
	}

	/**
	 * Load the module class.
	 *
	 * @param string $path The module class path.
	 *
	 * @return void
	 */
	protected function load_module_files( $path ) {
		// Collect all active modules.
		$activated_modules = $this->get_available_modules();

		// Load all active modules.
		foreach ( $activated_modules as $active_module ) {
			// Load the parent module.
			if ( file_exists( $this->get_module_path( $path, $active_module['name'] ) ) ) {
				require_once $this->get_module_path( $path, $active_module['name'] );

				// Load the child module.
				if ( ! empty( $active_module['child_name'] ) && file_exists( $this->get_module_path( $path, $active_module['child_name'] ) ) ) {
					require_once $this->get_module_path( $path, $active_module['child_name'] );
				}
			}
		}
	}

	/**
	 * Load enabled modules for Divi Builder from defined directory.
	 *
	 * @param string $path The defined directory.
	 *
	 * @return void
	 */
	public function load_divi_builder_4_modules( $path ) {
		if ( ! class_exists( \ET_Builder_Element::class ) ) {
			return;
		}

		// Load enabled modules.
		$this->load_module_files( $path );
	}
}
