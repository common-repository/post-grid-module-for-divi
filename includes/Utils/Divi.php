<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase

/**
 * Divi helper.
 *
 * @since       1.0.0
 * @package     post-grid-module-for-divi
 * @author      WP Squad <wp@thewpsquad.com>
 * @copyright   2023 WP Squad
 * @license     GPL-3.0-only
 */

namespace SquadPostGrid\Utils;

use function et_get_dynamic_assets_path;
use function et_pb_maybe_fa_font_icon;
use function et_use_dynamic_icons;
use function add_filter;

/**
 * Divi class.
 *
 * @since       1.0.0
 * @package     post-grid-module-for-divi
 */
class Divi {

	/**
	 * Add Icons css into the divi asset list when the Dynamic CSS option is turn on in current installation
	 *
	 * @param array $global_list The existed global asset list.
	 *
	 * @return array
	 */
	public static function global_assets_list( $global_list = array() ) {
		$assets_prefix = et_get_dynamic_assets_path();

		$assets_list = array(
			'et_icons_all' => array(
				'css' => "{$assets_prefix}/css/icons_all.css",
			),
		);

		return array_merge( $global_list, $assets_list );
	}

	/**
	 * Add Font Awesome css into the divi asset list when the Dynamic CSS option is turn on in current installation
	 *
	 * @param array $global_list The existed global asset list.
	 *
	 * @return array
	 */
	public static function global_fa_assets_list( $global_list = array() ) {
		$assets_prefix = et_get_dynamic_assets_path();

		$assets_list = array(
			'et_icons_fa' => array(
				'css' => "{$assets_prefix}/css/icons_fa_all.css",
			),
		);

		return array_merge( $global_list, $assets_list );
	}

	/**
	 * Add Font Awesome css support manually when the Dynamic CSS option is turn on in current installation.
	 *
	 * @param string $icon_data The icon value.
	 *
	 * @return void
	 */
	public static function inject_fa_icons( $icon_data ) {
		if ( function_exists( 'et_use_dynamic_icons' ) && 'on' === et_use_dynamic_icons() ) {
			add_filter( 'et_global_assets_list', array( __CLASS__, 'global_assets_list' ) );
			add_filter( 'et_late_global_assets_list', array( __CLASS__, 'global_assets_list' ) );

			if ( function_exists( 'et_pb_maybe_fa_font_icon' ) && et_pb_maybe_fa_font_icon( $icon_data ) ) {
				add_filter( 'et_global_assets_list', array( __CLASS__, 'global_fa_assets_list' ) );
				add_filter( 'et_late_global_assets_list', array( __CLASS__, 'global_fa_assets_list' ) );
			}
		}
	}
}
