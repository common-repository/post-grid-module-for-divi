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

namespace SquadPostGrid\Integration;

use SquadPostGrid\Base\BuilderBackendPlaceholder;
use SquadPostGrid\Utils\Helper;
use function et_fb_process_shortcode;

/**
 * Define integration helper functionalities for this plugin.
 *
 * @since      1.0.0
 * @package    post-grid-module-for-divi
 */
class DiviBuilderBackend extends BuilderBackendPlaceholder {
	/**
	 * Filters backend data passed to the Visual Builder.
	 * This function is used to add static helpers whose content rarely changes.
	 * eg: google fonts, module defaults, and so on.
	 *
	 * @param array $exists The existed definitions.
	 *
	 * @return array
	 */
	public function static_asset_definitions( $exists = array() ) {
		// Defaults data for modules.
		$defaults = $this->get_modules_defaults();

		// child module default data.
		$post_grid_child_defaults = array(
			'element_image_fullwidth__enable' => 'off',
			'element_excerpt__enable'         => 'off',
			'element_ex_con_length__enable'   => 'on',
			'element_ex_con_length'           => '30',
			'element_author_name_type'        => 'nickname',
			'element_read_more_text'          => $defaults['read_more'],
			'element_comments_before'         => $defaults['comments_before'],
			'element_categories_sepa'         => ',',
			'element_tags_sepa'               => ',',
			'element_custom_text'             => $defaults['custom_text'],
		);

		// generate shortcode for post-grid child module.
		$post_grid_child1 = sprintf(
			'[squad_post_grid_child %s][/squad_post_grid_child]',
			Helper::implode_assoc_array(
				array_merge(
					array( 'element' => 'image' ),
					$post_grid_child_defaults
				)
			)
		);
		$post_grid_child2 = sprintf(
			'[squad_post_grid_child %s][/squad_post_grid_child]',
			Helper::implode_assoc_array(
				array_merge(
					array(
						'element'           => 'title',
						'element_title_tag' => 'h2',
					),
					$post_grid_child_defaults
				)
			)
		);
		$post_grid_child3 = sprintf(
			'[squad_post_grid_child %s][/squad_post_grid_child]',
			Helper::implode_assoc_array(
				array_merge(
					array( 'element' => 'content' ),
					$post_grid_child_defaults
				)
			)
		);

		$post_icons_common = array(
			'element_icon_text_gap'             => '10px',
			'element_icon_placement'            => 'row',
			'element_icon_horizontal_alignment' => 'left',
			'element_icon_vertical_alignment'   => 'center',
		);

		$post_grid_child4 = sprintf(
			'[squad_post_grid_child %s][/squad_post_grid_child]',
			Helper::implode_assoc_array(
				array_merge(
					array(
						'element'           => 'date',
						'element_icon'      => '&#xe023;||divi||400',
						'element_date_type' => 'modified',
					),
					$post_icons_common,
					$post_grid_child_defaults
				)
			)
		);
		$post_grid_child5 = sprintf(
			'[squad_post_grid_child %s][/squad_post_grid_child]',
			Helper::implode_assoc_array(
				array_merge(
					array(
						'element'      => 'read_more',
						'element_icon' => '&#x35;||divi||400',
					),
					$post_icons_common,
					$post_grid_child_defaults
				)
			)
		);

		$post_grid_child_shortcodes = implode( '', array( $post_grid_child1, $post_grid_child2, $post_grid_child3, $post_grid_child4, $post_grid_child5 ) );

		$definitions = array(
			'defaults' => array(
				'squad_post_grid'       => array(
					'content'                       => et_fb_process_shortcode( $post_grid_child_shortcodes ),
					'number_of_columns_last_edited' => 'on|desktop',
					'number_of_columns'             => '3',
					'number_of_columns_tablet'      => '2',
					'number_of_columns_phone'       => '1',
					'item_gap'                      => '20px',
					'pagination__enable'            => 'on',
					'pagination_numbers__enable'    => 'on',
					'pagination_old_entries_text'   => _x( 'Older', 'Modules dummy content', 'post-grid-module-for-divi' ),
					'pagination_next_entries_text'  => _x( 'Next', 'Modules dummy content', 'post-grid-module-for-divi' ),
					'load_more_button_text'         => _x( 'Load More', 'Modules dummy content', 'post-grid-module-for-divi' ),
				),
				'squad_post_grid_child' => $post_grid_child_defaults,
			),
		);

		return array_merge_recursive( $exists, $definitions );
	}

	/**
	 * Used to update the content of the cached definitions js file.
	 *
	 * @param string $content content.
	 *
	 * @return string
	 */
	public function asset_definitions( $content ) {
		return $content . sprintf(
			';window.SquadPostGridBuilderBackend=%1$s; if(window.jQuery) {jQuery.extend(true, window.ETBuilderBackend, window.SquadPostGridBuilderBackend);}',
			et_fb_remove_site_url_protocol( wp_json_encode( $this->static_asset_definitions() ) )
		);
	}
}
