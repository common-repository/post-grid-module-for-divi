<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase

/**
 * Utils Common.
 *
 * @since       1.0.0
 * @package     post-grid-module-for-divi
 * @author      WP Squad <wp@thewpsquad.com>
 * @copyright   2023 WP Squad
 * @license     GPL-3.0-only
 */

namespace SquadPostGrid\Utils;

use function wp_strip_all_tags;

/**
 * Common trait.
 *
 * @since       1.0.0
 * @package     post-grid-module-for-divi
 */
class Module {

	/**
	 * Collect actual props from child module with escaping raw html.
	 *
	 * @param string $content The raw content form child element.
	 *
	 * @return string
	 */
	public static function collect_raw_props( $content ) {
		return wp_strip_all_tags( $content );
	}

	/**
	 * Collect actual props from child module with escaping raw html.
	 *
	 * @param string $content The raw content form child element.
	 *
	 * @return string
	 */
	public static function json_format_raw_props( $content ) {
		return sprintf( '[%s]', $content );
	}

	/**
	 * Collect actual props from child module with escaping raw html.
	 *
	 * @param string $content The raw content form child element.
	 *
	 * @return array
	 */
	public static function collect_child_json_props( $content ) {
		$raw_props   = static::json_format_raw_props( $content );
		$clean_props = str_replace( '},]', '}]', $raw_props );
		$child_props = json_decode( $clean_props, true );

		if ( JSON_ERROR_NONE !== json_last_error() ) {
			trigger_error( // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_trigger_error
				sprintf(
					esc_html(
					/* translators: 1: Error message. */
						__( 'Error when decoding child props: %1$s', 'post-grid-module-for-divi' )
					),
					json_last_error_msg() // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				)
			);

			return array();
		}

		return $child_props;
	}

	/**
	 * Get default selectors for main and hover
	 *
	 * @param string $main_css_element Main css selector of element.
	 *
	 * @return array[]
	 */
	public static function selectors_default( $main_css_element ) {
		return array(
			'css' => array(
				'main'  => $main_css_element,
				'hover' => "$main_css_element:hover",
			),
		);
	}

	/**
	 * Get margin and padding selectors for main and hover
	 *
	 * @param string $main_css_element Main css selector of element.
	 *
	 * @return array
	 */
	public static function selectors_margin_padding( $main_css_element ) {
		return array(
			'use_padding' => true,
			'use_margin'  => true,
			'css'         => array(
				'margin'    => $main_css_element,
				'padding'   => $main_css_element,
				'important' => 'all',
			),
		);
	}

	/**
	 * Get max_width selectors for main and hover
	 *
	 * @param string $main_css_element Main css selector of element.
	 *
	 * @return array[]
	 */
	public static function selectors_max_width( $main_css_element ) {
		return array_merge(
			self::selectors_default( $main_css_element ),
			array(
				'css' => array(
					'module_alignment' => "$main_css_element.et_pb_module",
				),
			)
		);
	}

	/**
	 * Get background selectors for main and hover
	 *
	 * @param string $main_css_element Main css selector of an element.
	 *
	 * @return array[]
	 */
	public static function selectors_background( $main_css_element ) {
		return array_merge(
			self::selectors_default( $main_css_element ),
			array(
				'settings' => array(
					'color' => 'alpha',
				),
			)
		);
	}

	/**
	 * Convert field name into css property name.
	 *
	 * @param string $field Field name.
	 *
	 * @return string|string[]
	 */
	public static function field_to_css_prop( $field ) {
		return str_replace( '_', '-', $field );
	}
}
