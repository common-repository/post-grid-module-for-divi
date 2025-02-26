<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase

/**
 * The Post Grid Module Class which extend the Divi Builder Module Class.
 *
 * This class provides the post-element in the grid system with functionalities in the visual builder.
 *
 * @since       1.0.0
 * @package     post-grid-module-for-divi
 * @author      WP Squad <wp@thewpsquad.com>
 * @copyright   2023 WP Squad
 * @license     GPL-3.0-only
 */

namespace SquadPostGrid\Modules\PostGrid;

use SquadPostGrid\Base\BuilderModule\Squad_Builder_Module;
use SquadPostGrid\Utils\Divi;
use SquadPostGrid\Utils\Helper;
use SquadPostGrid\Utils\Module;
use SquadPostGrid\Utils\Polyfills\Str;
use ET_Builder_Module_Helper_MultiViewOptions;
use WP_Post;
use WP_Query;
use function esc_html__;
use function et_core_esc_previously;
use function et_pb_multi_view_options;
use function et_pb_background_options;
use function is_singular;
use function get_the_ID;
use function wp_get_post_categories;
use function wp_get_post_tags;
use function get_the_author_meta;
use function get_post_class;
use function get_userdata;
use function wp_kses_post;
use function wp_strip_all_tags;
use function get_the_post_thumbnail;
use function get_permalink;
use function wp_json_encode;
use function et_pb_media_options;
use function paginate_links;
use function get_query_var;
use function et_pb_get_extended_font_icon_value;

/**
 * The Post-Grid Module Class.
 *
 * @since       1.0.0
 * @package     post-grid-module-for-divi
 */
class PostGrid extends Squad_Builder_Module {

	/**
	 * Initiate Module.
	 * Set the module name on init.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function init() {
		$this->name      = esc_html__( 'Post Grid', 'post-grid-module-for-divi' );
		$this->plural    = esc_html__( 'Post Grids', 'post-grid-module-for-divi' );
		$this->icon_path = Helper::fix_slash( SQUADPOSTGRID_MODULES_ICON_DIR_PATH . '/post-grid.svg' );

		$this->slug       = 'squad_post_grid';
		$this->child_slug = 'squad_post_grid_child';
		$this->vb_support = 'on';

		$this->main_css_element = "%%order_class%%.$this->slug";

		// Declare settings modal toggles for the module.
		$this->settings_modal_toggles = array(
			'general'  => array(
				'toggles' => array(
					'wrapper'    => esc_html__( 'Post Settings', 'post-grid-module-for-divi' ),
					'layout'     => esc_html__( 'Layout Settings', 'post-grid-module-for-divi' ),
					'pagination' => esc_html__( 'Pagination Settings', 'post-grid-module-for-divi' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'wrapper'                => esc_html__( 'Post Wrapper', 'post-grid-module-for-divi' ),
					'elements'               => esc_html__( 'Element Wrapper', 'post-grid-module-for-divi' ),
					'element_element'        => esc_html__( 'Element', 'post-grid-module-for-divi' ),
					'pagination_wrapper'     => esc_html__( 'Pagination Wrapper', 'post-grid-module-for-divi' ),
					'pagination'             => esc_html__( 'Pagination', 'post-grid-module-for-divi' ),
					'pagination_text'        => esc_html__( 'Pagination Text', 'post-grid-module-for-divi' ),
					'active_pagination'      => esc_html__( 'Active Pagination', 'post-grid-module-for-divi' ),
					'active_pagination_text' => esc_html__( 'Active Pagination Text', 'post-grid-module-for-divi' ),
				),
			),
		);

		// Declare advanced fields for the module.
		$this->advanced_fields = array(
			'fonts'          => array(
				'pagination_text'        => $this->squad_add_font_field(
					esc_html__( 'Pagination', 'post-grid-module-for-divi' ),
					array(
						'font_size'       => array(
							'default' => '16px',
						),
						'text_align'      => array(
							'show_if' => array(
								'pagination__enable' => 'on',
							),
						),
						'text_shadow'     => array(
							'show_if' => array(
								'pagination__enable' => 'on',
							),
						),
						'hide_text_align' => true,
						'css'             => array(
							'main'  => "$this->main_css_element div .squad-pagination .pagination-numbers .page-numbers, $this->main_css_element div .squad-pagination .pagination-entries",
							'hover' => "$this->main_css_element div .squad-pagination .pagination-numbers .page-numbers:hover, $this->main_css_element div .squad-pagination .pagination-entries:hover",
						),
					)
				),
				'active_pagination_text' => $this->squad_add_font_field(
					esc_html__( 'Pagination', 'post-grid-module-for-divi' ),
					array(
						'font_size'       => array(
							'default' => '16px',
						),
						'text_align'      => array(
							'show_if' => array(
								'pagination__enable' => 'on',
							),
						),
						'text_shadow'     => array(
							'show_if' => array(
								'pagination__enable' => 'on',
							),
						),
						'hide_text_align' => true,
						'css'             => array(
							'main'  => "$this->main_css_element div .squad-pagination .pagination-numbers .page-numbers.current",
							'hover' => "$this->main_css_element div .squad-pagination .pagination-numbers .page-numbers.current:hover",
						),
					)
				),
			),
			'background'     => Module::selectors_background( $this->main_css_element ),
			'borders'        => array(
				'default'            => Module::selectors_default( $this->main_css_element ),
				'wrapper'            => array(
					'label_prefix' => esc_html__( 'Wrapper', 'post-grid-module-for-divi' ),
					'css'          => array(
						'main' => array(
							'border_radii'        => "$this->main_css_element .squad-post-container .post",
							'border_radii_hover'  => "$this->main_css_element .squad-post-container .post:hover",
							'border_styles'       => "$this->main_css_element .squad-post-container .post",
							'border_styles_hover' => "$this->main_css_element .squad-post-container .post:hover",
						),
					),
					'defaults'     => array(
						'border_styles' => array(
							'width' => '1px|1px|1px|1px',
							'color' => '#d8d8d8',
							'style' => 'solid',
						),
					),
					'tab_slug'     => 'advanced',
					'toggle_slug'  => 'wrapper',
				),
				'elements'           => array(
					'label_prefix' => esc_html__( 'Wrapper', 'post-grid-module-for-divi' ),
					'css'          => array(
						'main' => array(
							'border_radii'        => "$this->main_css_element .squad-post-container .post .post-elements",
							'border_radii_hover'  => "$this->main_css_element .squad-post-container .post:hover .post-elements",
							'border_styles'       => "$this->main_css_element .squad-post-container .post .post-elements",
							'border_styles_hover' => "$this->main_css_element .squad-post-container .post:hover .post-elements",
						),
					),
					'defaults'     => array(
						'border_styles' => array(
							'color' => '#333',
							'style' => 'solid',
						),
					),
					'tab_slug'     => 'advanced',
					'toggle_slug'  => 'elements',
				),
				'element_element'    => array(
					'label_prefix' => esc_html__( 'Element', 'post-grid-module-for-divi' ),
					'css'          => array(
						'main' => array(
							'border_radii'        => "$this->main_css_element .squad-post-container .post .squad-post-element",
							'border_radii_hover'  => "$this->main_css_element .squad-post-container .post:hover .squad-post-element",
							'border_styles'       => "$this->main_css_element .squad-post-container .post .squad-post-element",
							'border_styles_hover' => "$this->main_css_element .squad-post-container .post:hover .squad-post-element",
						),
					),
					'defaults'     => array(
						'border_styles' => array(
							'color' => '#333',
							'style' => 'solid',
						),
					),
					'tab_slug'     => 'advanced',
					'toggle_slug'  => 'element_element',
				),
				'pagination'         => array(
					'label_prefix'    => esc_html__( 'Pagination', 'post-grid-module-for-divi' ),
					'css'             => array(
						'main' => array(
							'border_radii'        => "$this->main_css_element div .squad-pagination .pagination-numbers .page-numbers, $this->main_css_element div .squad-pagination .pagination-entries",
							'border_radii_hover'  => "$this->main_css_element div .squad-pagination .pagination-numbers .page-numbers:hover, $this->main_css_element div .squad-pagination .pagination-entries:hover",
							'border_styles'       => "$this->main_css_element div .squad-pagination .pagination-numbers .page-numbers, $this->main_css_element div .squad-pagination .pagination-entries",
							'border_styles_hover' => "$this->main_css_element div .squad-pagination .pagination-numbers .page-numbers:hover, $this->main_css_element div .squad-pagination .pagination-entries:hover",
						),
					),
					'defaults'        => array(
						'border_styles' => array(
							'color' => '#333',
							'style' => 'solid',
						),
					),
					'depends_on'      => array( 'pagination__enable' ),
					'depends_show_if' => 'on',
					'tab_slug'        => 'advanced',
					'toggle_slug'     => 'pagination',
				),
				'pagination_wrapper' => array(
					'label_prefix'    => esc_html__( 'Wrapper', 'post-grid-module-for-divi' ),
					'css'             => array(
						'main' => array(
							'border_radii'        => "$this->main_css_element div .squad-pagination",
							'border_radii_hover'  => "$this->main_css_element div .squad-pagination:hover",
							'border_styles'       => "$this->main_css_element div .squad-pagination",
							'border_styles_hover' => "$this->main_css_element div .squad-pagination:hover",
						),
					),
					'defaults'        => array(
						'border_styles' => array(
							'color' => '#333',
							'style' => 'solid',
						),
					),
					'depends_on'      => array( 'pagination__enable' ),
					'depends_show_if' => 'on',
					'tab_slug'        => 'advanced',
					'toggle_slug'     => 'pagination_wrapper',
				),
				'active_pagination'  => array(
					'label_prefix'    => esc_html__( 'Pagination', 'post-grid-module-for-divi' ),
					'css'             => array(
						'main' => array(
							'border_radii'        => "$this->main_css_element div .squad-pagination .pagination-numbers .page-numbers.current",
							'border_radii_hover'  => "$this->main_css_element div .squad-pagination .pagination-numbers .page-numbers.current:hover",
							'border_styles'       => "$this->main_css_element div .squad-pagination .pagination-numbers .page-numbers.current",
							'border_styles_hover' => "$this->main_css_element div .squad-pagination .pagination-numbers .page-numbers.current:hover",
						),
					),
					'defaults'        => array(
						'border_styles' => array(
							'color' => '#333',
							'style' => 'solid',
						),
					),
					'depends_on'      => array( 'pagination__enable' ),
					'depends_show_if' => 'on',
					'tab_slug'        => 'advanced',
					'toggle_slug'     => 'active_pagination',
				),
			),
			'box_shadow'     => array(
				'default'            => Module::selectors_default( $this->main_css_element ),
				'wrapper'            => array(
					'label'             => esc_html__( 'Wrapper Box Shadow', 'post-grid-module-for-divi' ),
					'option_category'   => 'layout',
					'css'               => array(
						'main'  => "$this->main_css_element .squad-post-container .post",
						'hover' => "$this->main_css_element .squad-post-container .post:hover",
					),
					'default_on_fronts' => array(
						'color'    => 'rgba(0,0,0,0.3)',
						'position' => 'outer',
					),
					'tab_slug'          => 'advanced',
					'toggle_slug'       => 'wrapper',
				),
				'elements'           => array(
					'label'             => esc_html__( 'Wrapper Box Shadow', 'post-grid-module-for-divi' ),
					'option_category'   => 'layout',
					'css'               => array(
						'main'  => "$this->main_css_element .squad-post-container .post .post-elements",
						'hover' => "$this->main_css_element .squad-post-container .post:hover .post-elements",
					),
					'default_on_fronts' => array(
						'color'    => 'rgba(0,0,0,0.3)',
						'position' => 'outer',
					),
					'tab_slug'          => 'advanced',
					'toggle_slug'       => 'elements',
				),
				'element_element'    => array(
					'label'             => esc_html__( 'Element Box Shadow', 'post-grid-module-for-divi' ),
					'option_category'   => 'layout',
					'css'               => array(
						'main'  => "$this->main_css_element .squad-post-container .post .squad-post-element",
						'hover' => "$this->main_css_element .squad-post-container .post:hover .squad-post-element",
					),
					'default_on_fronts' => array(
						'color'    => 'rgba(0,0,0,0.3)',
						'position' => 'outer',
					),
					'tab_slug'          => 'advanced',
					'toggle_slug'       => 'element_element',
				),
				'pagination_wrapper' => array(
					'label'             => esc_html__( 'Wrapper Box Shadow', 'post-grid-module-for-divi' ),
					'option_category'   => 'layout',
					'css'               => array(
						'main'  => "$this->main_css_element div .squad-pagination",
						'hover' => "$this->main_css_element div .squad-pagination:hover",
					),
					'default_on_fronts' => array(
						'color'    => 'rgba(0,0,0,0.3)',
						'position' => 'outer',
					),
					'depends_on'        => array( 'pagination__enable' ),
					'depends_show_if'   => 'on',
					'tab_slug'          => 'advanced',
					'toggle_slug'       => 'pagination_wrapper',
				),
				'pagination'         => array(
					'label'             => esc_html__( 'Pagination Box Shadow', 'post-grid-module-for-divi' ),
					'option_category'   => 'layout',
					'css'               => array(
						'main'  => "$this->main_css_element div .squad-pagination .pagination-numbers .page-numbers, $this->main_css_element div .squad-pagination .pagination-entries",
						'hover' => "$this->main_css_element div .squad-pagination .pagination-numbers .page-numbers:hover, $this->main_css_element div .squad-pagination .pagination-entries:hover",
					),
					'default_on_fronts' => array(
						'color'    => 'rgba(0,0,0,0.3)',
						'position' => 'outer',
					),
					'depends_on'        => array( 'pagination__enable' ),
					'depends_show_if'   => 'on',
					'tab_slug'          => 'advanced',
					'toggle_slug'       => 'pagination',
				),
				'active_pagination'  => array(
					'label'             => esc_html__( 'Pagination Box Shadow', 'post-grid-module-for-divi' ),
					'option_category'   => 'layout',
					'css'               => array(
						'main'  => "$this->main_css_element div .squad-pagination .pagination-numbers .page-numbers.current",
						'hover' => "$this->main_css_element div .squad-pagination .pagination-numbers .page-numbers.current:hover",
					),
					'default_on_fronts' => array(
						'color'    => 'rgba(0,0,0,0.3)',
						'position' => 'outer',
					),
					'depends_on'        => array( 'pagination__enable' ),
					'depends_show_if'   => 'on',
					'tab_slug'          => 'advanced',
					'toggle_slug'       => 'active_pagination',
				),
			),
			'margin_padding' => Module::selectors_margin_padding( $this->main_css_element ),
			'max_width'      => Module::selectors_max_width( $this->main_css_element ),
			'height'         => Module::selectors_default( $this->main_css_element ),
			'image_icon'     => false,
			'link_options'   => false,
			'filters'        => false,
			'text'           => false,
			'button'         => false,
		);

		// Declare custom css fields for the module.
		$this->custom_css_fields = array(
			'wrapper'                  => array(
				'label'    => esc_html__( 'Post Wrapper', 'post-grid-module-for-divi' ),
				'selector' => "$this->main_css_element div .squad-post-container .post",
			),
			'pagination_wrapper'       => array(
				'label'    => esc_html__( 'Pagination Wrapper', 'post-grid-module-for-divi' ),
				'selector' => "$this->main_css_element div .squad-pagination",
			),
			'pagination_numbers'       => array(
				'label'    => esc_html__( 'Pagination', 'post-grid-module-for-divi' ),
				'selector' => "$this->main_css_element div .squad-pagination .pagination-numbers .page-numbers, $this->main_css_element div .squad-pagination .pagination-entries",
			),
			'pagination_active_number' => array(
				'label'    => esc_html__( 'Active Pagination', 'post-grid-module-for-divi' ),
				'selector' => "$this->main_css_element div .squad-pagination .pagination-numbers .page-numbers.current",
			),
		);
	}

	/**
	 * Return an added new item(module) text.
	 *
	 * @return string
	 */
	public function add_new_child_text() {
		return esc_html__( 'Add New Element', 'post-grid-module-for-divi' );
	}

	/**
	 * Declare general fields for the module.
	 *
	 * @return array[]
	 * @since 1.0.0
	 */
	public function get_fields() {
		$general_settings = array(
			'inherit_current_loop'     => $this->squad_add_yes_no_field(
				esc_html__( 'Posts For Current Page', 'post-grid-module-for-divi' ),
				array(
					'description'      => esc_html__( 'Display posts for the current page. Useful on all author pages.', 'post-grid-module-for-divi' ),
					'default'          => 'off',
					'show_if'          => array(
						'function.isTBLayout' => 'on',
					),
					'computed_affects' => array(
						'__posts',
					),
					'tab_slug'         => 'general',
					'toggle_slug'      => 'wrapper',
				)
			),
			'post_display_by'          => $this->squad_add_select_box_field(
				esc_html__( 'Display By', 'post-grid-module-for-divi' ),
				array(
					'description'      => esc_html__( 'Sort retrieved posts by parameter. Defaults to ‘recent’.', 'post-grid-module-for-divi' ),
					'options'          => array(
						'recent'   => esc_html__( 'Recent', 'post-grid-module-for-divi' ),
						'category' => esc_html__( 'Category', 'post-grid-module-for-divi' ),
						'tag'      => esc_html__( 'Tag', 'post-grid-module-for-divi' ),
					),
					'default'          => 'recent',
					'default_on_front' => 'recent',
					'computed_affects' => array(
						'__posts',
					),
					'affects'          => array(
						'post_include_categories',
						'post_include_tags',
					),
					'show_if_not'      => array(
						'inherit_current_loop' => 'on',
					),
					'tab_slug'         => 'general',
					'toggle_slug'      => 'wrapper',
				)
			),
			'post_include_categories'  => array(
				'label'            => esc_html__( 'Include Categories', 'post-grid-module-for-divi' ),
				'type'             => 'categories',
				'meta_categories'  => array(
					'all'     => esc_html__( 'All Categories', 'post-grid-module-for-divi' ),
					'current' => esc_html__( 'Current Category', 'post-grid-module-for-divi' ),
				),
				'renderer_options' => array(
					'use_terms' => true,
					'term_name' => 'category',
				),
				'taxonomy_name'    => 'category',
				'depends_show_if'  => 'category',
				'tab_slug'         => 'general',
				'toggle_slug'      => 'wrapper',
			),
			'post_include_tags'        => array(
				'label'            => esc_html__( 'Include Tags', 'post-grid-module-for-divi' ),
				'type'             => 'categories',
				'renderer_options' => array(
					'use_terms' => true,
					'term_name' => 'post_tag',
				),
				'taxonomy_name'    => 'post_tag',
				'depends_show_if'  => 'tag',
				'tab_slug'         => 'general',
				'toggle_slug'      => 'wrapper',
			),
			'post_order_by'            => $this->squad_add_select_box_field(
				esc_html__( 'Order By', 'post-grid-module-for-divi' ),
				array(
					'description'      => esc_html__( 'Sort retrieved posts by parameter. Defaults to ‘date’.', 'post-grid-module-for-divi' ),
					'options'          => array(
						'date'          => esc_html__( 'Publish Date', 'post-grid-module-for-divi' ),
						'modified'      => esc_html__( 'Modified Date', 'post-grid-module-for-divi' ),
						'name'          => esc_html__( 'Name', 'post-grid-module-for-divi' ),
						'title'         => esc_html__( 'Title', 'post-grid-module-for-divi' ),
						'author'        => esc_html__( 'Author', 'post-grid-module-for-divi' ),
						'comment_count' => esc_html__( 'Comments', 'post-grid-module-for-divi' ),
						'rand'          => esc_html__( 'Random', 'post-grid-module-for-divi' ),
					),
					'computed_affects' => array(
						'__posts',
					),
					'tab_slug'         => 'general',
					'toggle_slug'      => 'wrapper',
				)
			),
			'post_order'               => $this->squad_add_select_box_field(
				esc_html__( 'Order', 'post-grid-module-for-divi' ),
				array(
					'description'      => esc_html__( 'Designates the ascending or descending order of the ‘orderby‘ parameter. Defaults to ‘Ascending’..', 'post-grid-module-for-divi' ),
					'options'          => array(
						'ASC'  => esc_html__( 'Ascending', 'post-grid-module-for-divi' ),
						'DESC' => esc_html__( 'Descending', 'post-grid-module-for-divi' ),
					),
					'default'          => 'ASC',
					'computed_affects' => array(
						'__posts',
					),
					'tab_slug'         => 'general',
					'toggle_slug'      => 'wrapper',
				)
			),
			'post_count'               => $this->squad_add_range_field(
				esc_html__( 'Post Count', 'post-grid-module-for-divi' ),
				array(
					'description'       => esc_html__( 'Here you can choose how much posts you would like to display per page.', 'post-grid-module-for-divi' ),
					'type'              => 'range',
					'range_settings'    => array(
						'min_limit' => '1',
						'min'       => '1',
						'max_limit' => '1000',
						'max'       => '1000',
						'step'      => '1',
					),
					'default'           => 10,
					'number_validation' => true,
					'fixed_range'       => true,
					'unitless'          => true,
					'hover'             => false,
					'mobile_options'    => false,
					'responsive'        => false,
					'computed_affects'  => array(
						'__posts',
					),
					'tab_slug'          => 'general',
					'toggle_slug'       => 'wrapper',
				)
			),
			'post_offset'              => $this->squad_add_range_field(
				esc_html__( 'Post Offset', 'post-grid-module-for-divi' ),
				array(
					'description'       => esc_html__( 'Here you can choose how much post show in the current page.', 'post-grid-module-for-divi' ),
					'type'              => 'range',
					'range_settings'    => array(
						'min_limit' => '0',
						'min'       => '0',
						'max_limit' => '1000',
						'max'       => '1000',
						'step'      => '1',
					),
					'number_validation' => true,
					'fixed_range'       => true,
					'unitless'          => true,
					'hover'             => false,
					'mobile_options'    => false,
					'responsive'        => false,
					'computed_affects'  => array(
						'__posts',
					),
					'tab_slug'          => 'general',
					'toggle_slug'       => 'wrapper',
				)
			),
			'post_ignore_sticky_posts' => $this->squad_add_yes_no_field(
				esc_html__( 'Skip Sticky Posts', 'post-grid-module-for-divi' ),
				array(
					'description'      => esc_html__( ' Ignore sticky  posts for the current page.', 'post-grid-module-for-divi' ),
					'default'          => 'off',
					'computed_affects' => array(
						'__posts',
					),
					'tab_slug'         => 'general',
					'toggle_slug'      => 'wrapper',
				)
			),
			'thumbnail_size'           => array(
				'label'            => esc_html__( 'Feature Image Size', 'post-grid-module-for-divi' ),
				'description'      => esc_html__( 'If you would like to adjust the date format, input the appropriate PHP date format here.', 'post-grid-module-for-divi' ),
				'type'             => 'text',
				'option_category'  => 'configuration',
				'default'          => 'M j, Y',
				'computed_affects' => array(
					'__posts',
				),
				'tab_slug'         => 'general',
				'toggle_slug'      => 'wrapper',
			),
			'date_format'              => array(
				'label'            => esc_html__( 'Date Format', 'post-grid-module-for-divi' ),
				'description'      => esc_html__( 'If you would like to adjust the date format, input the appropriate PHP date format here.', 'post-grid-module-for-divi' ),
				'type'             => 'text',
				'option_category'  => 'configuration',
				'default'          => 'M j, Y',
				'computed_affects' => array(
					'__posts',
				),
				'tab_slug'         => 'general',
				'toggle_slug'      => 'wrapper',
			),
			'__posts'                  => array(
				'type'                => 'computed',
				'computed_callback'   => array( __CLASS__, 'get_post_html' ),
				'computed_depends_on' => array(
					'inherit_current_loop',
					'post_display_by',
					'post_include_categories',
					'post_include_tags',
					'post_order_by',
					'post_order',
					'post_count',
					'post_offset',
					'post_ignore_sticky_posts',
					'date_format',
				),
			),
		);
		$layout_settings  = array(
			'number_of_columns'  => $this->squad_add_range_field(
				esc_html__( 'Column Numbers', 'post-grid-module-for-divi' ),
				array(
					'description'       => esc_html__( 'Here you can choose list column for grid layout.', 'post-grid-module-for-divi' ),
					'range_settings'    => array(
						'min_limit' => '1',
						'min'       => '1',
						'max_limit' => '6',
						'max'       => '6',
						'step'      => '1',
					),
					'number_validation' => true,
					'fixed_range'       => true,
					'unitless'          => true,
					'default_on_front'  => '3',
					'default_on_tablet' => '2',
					'default_on_mobile' => '1',
					'tab_slug'          => 'general',
					'toggle_slug'       => 'layout',
					'hover'             => false,
				)
			),
			'item_gap'           => $this->squad_add_range_field(
				esc_html__( 'Columns Gap', 'post-grid-module-for-divi' ),
				array(
					'description'    => esc_html__( 'Here you can choose list item gap.', 'post-grid-module-for-divi' ),
					'type'           => 'range',
					'range_settings' => array(
						'min_limit' => '1',
						'min'       => '1',
						'max_limit' => '100',
						'max'       => '100',
						'step'      => '1',
					),
					'default'        => '10px',
					'default_unit'   => 'px',
					'hover'          => false,
					'tab_slug'       => 'general',
					'toggle_slug'    => 'layout',
				)
			),
			'pagination__enable' => $this->squad_add_yes_no_field(
				esc_html__( 'Show Pagination', 'post-grid-module-for-divi' ),
				array(
					'description'      => esc_html__( 'Here you can choose whether or not show the pagination.', 'post-grid-module-for-divi' ),
					'default_on_front' => 'off',
					'affects'          => array(
						'pagination_numbers__enable',
						'pagination_icon_only__enable',
						'pagination_old_entries_icon',
						'pagination_next_entries_icon',
						'pagination_text',
						'pagination_text_font',
						'pagination_text_text_color',
						'pagination_text_text_align',
						'pagination_text_font_size',
						'pagination_text_letter_spacing',
						'pagination_text_line_height',
						'active_pagination_text',
						'active_pagination_text_font',
						'active_pagination_text_text_color',
						'active_pagination_text_text_align',
						'active_pagination_text_font_size',
						'active_pagination_text_letter_spacing',
						'active_pagination_text_line_height',
						'pagination_wrapper_background_color',
						'pagination_background_color',
						'active_pagination_background_color',
						'pagination_icon_color',
						'pagination_icon_size',
						'pagination_horizontal_alignment',
						'pagination_elements_gap',
						'pagination_wrapper_margin',
						'pagination_wrapper_padding',
						'pagination_icon_margin',
						'pagination_icon_padding',
						'pagination_margin',
						'pagination_padding',
						'active_pagination_margin',
						'active_pagination_padding',
					),
					'tab_slug'         => 'general',
					'toggle_slug'      => 'layout',
				)
			),
		);

		$pagination_fields                    = array(
			'pagination_numbers__enable'   => $this->squad_add_yes_no_field(
				esc_html__( 'Show Numbers', 'post-grid-module-for-divi' ),
				array(
					'description'      => esc_html__( 'Here you can choose whether or not show the pagination.', 'post-grid-module-for-divi' ),
					'default_on_front' => 'off',
					'default'          => 'off',
					'depends_show_if'  => 'on',
					'tab_slug'         => 'general',
					'toggle_slug'      => 'pagination',
				)
			),
			'pagination_icon_only__enable' => $this->squad_add_yes_no_field(
				esc_html__( 'Show Icon Only for Older and Next Entries ', 'post-grid-module-for-divi' ),
				array(
					'description'      => esc_html__( 'Here you can choose whether or not show the pagination.', 'post-grid-module-for-divi' ),
					'default_on_front' => 'off',
					'depends_show_if'  => 'on',
					'affects'          => array(
						'pagination_old_entries_text',
						'pagination_next_entries_text',
						'pagination_icon_text_gap',
					),
					'computed_affects' => array(
						'__posts',
					),
					'tab_slug'         => 'general',
					'toggle_slug'      => 'pagination',
				)
			),
			'pagination_old_entries_text'  => array(
				'label'           => esc_html__( 'Old Entries Text', 'post-grid-module-for-divi' ),
				'description'     => esc_html__( 'The text will appear in with your old entries.', 'post-grid-module-for-divi' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'depends_show_if' => 'off',
				'tab_slug'        => 'general',
				'toggle_slug'     => 'pagination',
			),
			'pagination_old_entries_icon'  => array(
				'label'            => esc_html__( 'Old Entries Icon', 'post-grid-module-for-divi' ),
				'description'      => esc_html__( 'Choose an icon to display with your old entries.', 'post-grid-module-for-divi' ),
				'type'             => 'select_icon',
				'option_category'  => 'basic_option',
				'class'            => array( 'et-pb-font-icon' ),
				'default_on_front' => '&#x3c;||divi||400',
				'default'          => '&#x3c;||divi||400',
				'depends_show_if'  => 'on',
				'tab_slug'         => 'general',
				'toggle_slug'      => 'pagination',
				'hover'            => 'tabs',
				'mobile_options'   => true,
			),
			'pagination_next_entries_text' => array(
				'label'           => esc_html__( 'Next Entries Text', 'post-grid-module-for-divi' ),
				'description'     => esc_html__( 'The text will appear in with your next entries.', 'post-grid-module-for-divi' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'depends_show_if' => 'off',
				'tab_slug'        => 'general',
				'toggle_slug'     => 'pagination',
			),
			'pagination_next_entries_icon' => array(
				'label'            => esc_html__( 'Next Entries Icon', 'post-grid-module-for-divi' ),
				'description'      => esc_html__( 'Choose an icon to display with your next entries.', 'post-grid-module-for-divi' ),
				'type'             => 'select_icon',
				'option_category'  => 'basic_option',
				'class'            => array( 'et-pb-font-icon' ),
				'default_on_front' => '&#x3d;||divi||400',
				'default'          => '&#x3d;||divi||400',
				'depends_show_if'  => 'on',
				'tab_slug'         => 'general',
				'toggle_slug'      => 'pagination',
				'hover'            => 'tabs',
				'mobile_options'   => true,
			),
		);
		$pagination_wrapper_background_fields = $this->squad_add_background_field(
			esc_html__( 'Wrapper Background', 'post-grid-module-for-divi' ),
			array(
				'base_name'       => 'pagination_wrapper_background',
				'context'         => 'pagination_wrapper_background_color',
				'depends_show_if' => 'on',
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'pagination_wrapper',
			)
		);
		$pagination_background_fields         = $this->squad_add_background_field(
			esc_html__( 'Pagination Background', 'post-grid-module-for-divi' ),
			array(
				'base_name'       => 'pagination_background',
				'context'         => 'pagination_background_color',
				'depends_show_if' => 'on',
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'pagination',
			)
		);
		$active_pagination_background_fields  = $this->squad_add_background_field(
			esc_html__( 'Pagination Background', 'post-grid-module-for-divi' ),
			array(
				'base_name'       => 'active_pagination_background',
				'context'         => 'active_pagination_background_color',
				'depends_show_if' => 'on',
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'active_pagination',
			)
		);
		$pagination_associated_fields         = array(
			'pagination_icon_color'           => $this->squad_add_color_field(
				esc_html__( 'Entries Icon Color', 'post-grid-module-for-divi' ),
				array(
					'description'     => esc_html__( 'Here you can define a custom color for your icon.', 'post-grid-module-for-divi' ),
					'depends_show_if' => 'on',
					'tab_slug'        => 'advanced',
					'toggle_slug'     => 'pagination',
				)
			),
			'pagination_icon_size'            => $this->squad_add_range_field(
				esc_html__( 'Entries Icon Size', 'post-grid-module-for-divi' ),
				array(
					'description'     => esc_html__( 'Here you can choose icon size.', 'post-grid-module-for-divi' ),
					'range_settings'  => array(
						'min_limit' => '1',
						'min'       => '1',
						'max_limit' => '200',
						'max'       => '200',
						'step'      => '1',
					),
					'depends_show_if' => 'on',
					'default'         => '16px',
					'default_unit'    => 'px',
					'tab_slug'        => 'advanced',
					'toggle_slug'     => 'pagination',
				)
			),
			'pagination_icon_text_gap'        => $this->squad_add_range_field(
				esc_html__( 'Gap Between Entries Icon and Text', 'post-grid-module-for-divi' ),
				array(
					'description'     => esc_html__( 'Here you can choose gap between entries icon and text.', 'post-grid-module-for-divi' ),
					'range_settings'  => array(
						'min_limit' => '1',
						'min'       => '1',
						'max_limit' => '200',
						'max'       => '200',
						'step'      => '1',
					),
					'default'         => '10px',
					'default_unit'    => 'px',
					'depends_show_if' => 'off',
					'tab_slug'        => 'advanced',
					'toggle_slug'     => 'pagination',
					'hover'           => false,
					'mobile_options'  => true,
				)
			),
			'pagination_elements_gap'         => $this->squad_add_range_field(
				esc_html__( 'Gap Between Pagination Elements', 'post-grid-module-for-divi' ),
				array(
					'description'     => esc_html__( 'Here you can choose gap between pagination elements.', 'post-grid-module-for-divi' ),
					'range_settings'  => array(
						'min_limit' => '1',
						'min'       => '1',
						'max_limit' => '200',
						'max'       => '200',
						'step'      => '1',
					),
					'default'         => '10px',
					'default_unit'    => 'px',
					'depends_show_if' => 'on',
					'tab_slug'        => 'advanced',
					'toggle_slug'     => 'pagination',
					'hover'           => false,
					'mobile_options'  => true,
				)
			),
			'pagination_horizontal_alignment' => $this->squad_add_alignment_field(
				esc_html__( 'Pagination Alignment', 'post-grid-module-for-divi' ),
				array(
					'description'      => esc_html__( 'Align icon to the left, right or center.', 'post-grid-module-for-divi' ),
					'type'             => 'text_align',
					'default'          => 'center',
					'default_on_front' => 'center',
					'depends_show_if'  => 'on',
					'tab_slug'         => 'advanced',
					'toggle_slug'      => 'pagination_wrapper',
				)
			),
			'pagination_icon_margin'          => $this->squad_add_margin_padding_field(
				esc_html__( 'Entries Icon Margin', 'post-grid-module-for-divi' ),
				array(
					'description'     => esc_html__( 'Here you can define a custom margin size.', 'post-grid-module-for-divi' ),
					'type'            => 'custom_margin',
					'range_settings'  => array(
						'min_limit' => '1',
						'min'       => '1',
						'max_limit' => '100',
						'max'       => '100',
						'step'      => '1',
					),
					'depends_show_if' => 'on',
					'tab_slug'        => 'advanced',
					'toggle_slug'     => 'pagination',
				)
			),
			'pagination_icon_padding'         => $this->squad_add_margin_padding_field(
				esc_html__( 'Entries Icon Padding', 'post-grid-module-for-divi' ),
				array(
					'description'     => esc_html__( 'Here you can define a custom padding size.', 'post-grid-module-for-divi' ),
					'type'            => 'custom_padding',
					'range_settings'  => array(
						'min_limit' => '1',
						'min'       => '1',
						'max_limit' => '100',
						'max'       => '100',
						'step'      => '1',
					),
					'depends_show_if' => 'on',
					'tab_slug'        => 'advanced',
					'toggle_slug'     => 'pagination',
				)
			),
			'pagination_wrapper_margin'       => $this->squad_add_margin_padding_field(
				esc_html__( 'Wrapper Margin', 'post-grid-module-for-divi' ),
				array(
					'description'      => esc_html__( 'Here you can define a custom margin size.', 'post-grid-module-for-divi' ),
					'type'             => 'custom_margin',
					'range_settings'   => array(
						'min_limit' => '1',
						'min'       => '1',
						'max_limit' => '100',
						'max'       => '100',
						'step'      => '1',
					),
					'default'          => '20px|||||',
					'default_on_front' => '20px|||||',
					'depends_show_if'  => 'on',
					'tab_slug'         => 'advanced',
					'toggle_slug'      => 'pagination_wrapper',
				)
			),
			'pagination_wrapper_padding'      => $this->squad_add_margin_padding_field(
				esc_html__( 'Wrapper Padding', 'post-grid-module-for-divi' ),
				array(
					'description'     => esc_html__( 'Here you can define a custom padding size.', 'post-grid-module-for-divi' ),
					'type'            => 'custom_padding',
					'range_settings'  => array(
						'min_limit' => '1',
						'min'       => '1',
						'max_limit' => '100',
						'max'       => '100',
						'step'      => '1',
					),
					'depends_show_if' => 'on',
					'tab_slug'        => 'advanced',
					'toggle_slug'     => 'pagination_wrapper',
				)
			),
			'pagination_margin'               => $this->squad_add_margin_padding_field(
				esc_html__( 'Pagination Margin', 'post-grid-module-for-divi' ),
				array(
					'description'     => esc_html__( 'Here you can define a custom margin size.', 'post-grid-module-for-divi' ),
					'type'            => 'custom_margin',
					'range_settings'  => array(
						'min_limit' => '1',
						'min'       => '1',
						'max_limit' => '100',
						'max'       => '100',
						'step'      => '1',
					),
					'depends_show_if' => 'on',
					'tab_slug'        => 'advanced',
					'toggle_slug'     => 'pagination',
				)
			),
			'pagination_padding'              => $this->squad_add_margin_padding_field(
				esc_html__( 'Pagination Padding', 'post-grid-module-for-divi' ),
				array(
					'description'     => esc_html__( 'Here you can define a custom padding size.', 'post-grid-module-for-divi' ),
					'type'            => 'custom_padding',
					'range_settings'  => array(
						'min_limit' => '1',
						'min'       => '1',
						'max_limit' => '100',
						'max'       => '100',
						'step'      => '1',
					),
					'depends_show_if' => 'on',
					'tab_slug'        => 'advanced',
					'toggle_slug'     => 'pagination',
				)
			),
			'active_pagination_margin'        => $this->squad_add_margin_padding_field(
				esc_html__( 'Pagination Margin', 'post-grid-module-for-divi' ),
				array(
					'description'     => esc_html__( 'Here you can define a custom margin size.', 'post-grid-module-for-divi' ),
					'type'            => 'custom_margin',
					'range_settings'  => array(
						'min_limit' => '1',
						'min'       => '1',
						'max_limit' => '100',
						'max'       => '100',
						'step'      => '1',
					),
					'depends_show_if' => 'on',
					'tab_slug'        => 'advanced',
					'toggle_slug'     => 'active_pagination',
				)
			),
			'active_pagination_padding'       => $this->squad_add_margin_padding_field(
				esc_html__( 'Pagination Padding', 'post-grid-module-for-divi' ),
				array(
					'description'     => esc_html__( 'Here you can define a custom padding size.', 'post-grid-module-for-divi' ),
					'type'            => 'custom_padding',
					'range_settings'  => array(
						'min_limit' => '1',
						'min'       => '1',
						'max_limit' => '100',
						'max'       => '100',
						'step'      => '1',
					),
					'depends_show_if' => 'on',
					'tab_slug'        => 'advanced',
					'toggle_slug'     => 'active_pagination',
				)
			),
		);

		$post_wrapper_background_fields    = $this->squad_add_background_field(
			esc_html__( 'Wrapper Background', 'post-grid-module-for-divi' ),
			array(
				'base_name'   => 'post_wrapper_background',
				'context'     => 'post_wrapper_background_color',
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'wrapper',
			)
		);
		$element_wrapper_background_fields = $this->squad_add_background_field(
			esc_html__( 'Wrapper Background', 'post-grid-module-for-divi' ),
			array(
				'base_name'   => 'element_wrapper_background',
				'context'     => 'element_wrapper_background_color',
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'elements',
			)
		);
		$element_background_fields         = $this->squad_add_background_field(
			esc_html__( 'Element Background', 'post-grid-module-for-divi' ),
			array(
				'base_name'   => 'element_background',
				'context'     => 'element_background_color',
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'element_element',
			)
		);

		$post_wrapper_associated_fields    = array(
			'post_text_orientation' => $this->squad_add_alignment_field(
				esc_html__( 'Text Alignment', 'post-grid-module-for-divi' ),
				array(
					'description' => esc_html__( 'This controls how your text is aligned within the module.', 'post-grid-module-for-divi' ),
					'type'        => 'text_align',
					'options'     => et_builder_get_text_orientation_options(
						array( 'justified' ),
						array( 'justify' => 'Justified' )
					),
					'default'     => '',
					'tab_slug'    => 'advanced',
					'toggle_slug' => 'wrapper',
				)
			),
			'post_wrapper_margin'   => $this->squad_add_margin_padding_field(
				esc_html__( 'Wrapper Margin', 'post-grid-module-for-divi' ),
				array(
					'description' => esc_html__( 'Here you can define a custom margin size for the wrapper.', 'post-grid-module-for-divi' ),
					'type'        => 'custom_margin',
					'tab_slug'    => 'advanced',
					'toggle_slug' => 'wrapper',
				)
			),
			'post_wrapper_padding'  => $this->squad_add_margin_padding_field(
				esc_html__( 'Wrapper Padding', 'post-grid-module-for-divi' ),
				array(
					'description'      => esc_html__( 'Here you can define a custom padding size.', 'post-grid-module-for-divi' ),
					'type'             => 'custom_padding',
					'default'          => '19px|19px|19px|19px|true|true',
					'default_on_front' => '19px|19px|19px|19px|true|true',
					'tab_slug'         => 'advanced',
					'toggle_slug'      => 'wrapper',
				)
			),
		);
		$element_wrapper_associated_fields = array(
			'element_wrapper_margin'  => $this->squad_add_margin_padding_field(
				esc_html__( 'Wrapper Margin', 'post-grid-module-for-divi' ),
				array(
					'description' => esc_html__( 'Here you can define a custom margin size for the wrapper.', 'post-grid-module-for-divi' ),
					'type'        => 'custom_margin',
					'tab_slug'    => 'advanced',
					'toggle_slug' => 'elements',
				)
			),
			'element_wrapper_padding' => $this->squad_add_margin_padding_field(
				esc_html__( 'Wrapper Padding', 'post-grid-module-for-divi' ),
				array(
					'description' => esc_html__( 'Here you can define a custom padding size.', 'post-grid-module-for-divi' ),
					'type'        => 'custom_padding',
					'tab_slug'    => 'advanced',
					'toggle_slug' => 'elements',
				)
			),
		);
		$element_associated_fields         = array(
			'element_margin'  => $this->squad_add_margin_padding_field(
				esc_html__( 'Element Margin', 'post-grid-module-for-divi' ),
				array(
					'description'    => esc_html__( 'Here you can define a custom margin size.', 'post-grid-module-for-divi' ),
					'type'           => 'custom_margin',
					'range_settings' => array(
						'min_limit' => '1',
						'min'       => '1',
						'max_limit' => '100',
						'max'       => '100',
						'step'      => '1',
					),
					'tab_slug'       => 'advanced',
					'toggle_slug'    => 'element_element',
				)
			),
			'element_padding' => $this->squad_add_margin_padding_field(
				esc_html__( 'Element Padding', 'post-grid-module-for-divi' ),
				array(
					'description'    => esc_html__( 'Here you can define a custom padding size.', 'post-grid-module-for-divi' ),
					'type'           => 'custom_padding',
					'range_settings' => array(
						'min_limit' => '1',
						'min'       => '1',
						'max_limit' => '100',
						'max'       => '100',
						'step'      => '1',
					),
					'tab_slug'       => 'advanced',
					'toggle_slug'    => 'element_element',
				)
			),
		);

		return array_merge_recursive(
			$general_settings,
			$layout_settings,
			$pagination_fields,
			$pagination_wrapper_background_fields,
			$pagination_background_fields,
			$active_pagination_background_fields,
			$pagination_associated_fields,
			$post_wrapper_background_fields,
			$post_wrapper_associated_fields,
			$element_wrapper_background_fields,
			$element_wrapper_associated_fields,
			$element_background_fields,
			$element_associated_fields
		);
	}

	/**
	 * Get CSS fields transition.
	 *
	 * Add form field options group and background image on the field list.
	 *
	 * @since 1.0.0
	 */
	public function get_transition_fields_css_props() {
		$fields = parent::get_transition_fields_css_props();

		// wrapper styles.
		$fields['post_wrapper_background_color'] = array( 'background' => "$this->main_css_element .squad-post-container .post" );
		$fields['post_wrapper_margin']           = array( 'margin' => "$this->main_css_element .squad-post-container .post" );
		$fields['post_wrapper_padding']          = array( 'padding' => "$this->main_css_element .squad-post-container .post" );
		$this->squad_fix_border_transition( $fields, 'wrapper', "$this->main_css_element .squad-post-container .post" );
		$this->squad_fix_box_shadow_transition( $fields, 'wrapper', "$this->main_css_element .squad-post-container .post" );

		// element wrapper styles.
		$fields['element_wrapper_background_color'] = array( 'background' => "$this->main_css_element .squad-post-container .post .post-elements" );
		$fields['element_wrapper_margin']           = array( 'margin' => "$this->main_css_element .squad-post-container .post .post-elements" );
		$fields['element_wrapper_padding']          = array( 'padding' => "$this->main_css_element .squad-post-container .post .post-elements" );
		$this->squad_fix_border_transition( $fields, 'elements', "$this->main_css_element .squad-post-container .post .post-elements" );
		$this->squad_fix_box_shadow_transition( $fields, 'elements', "$this->main_css_element .squad-post-container .post .post-elements" );

		// element styles.
		$fields['element_background_color'] = array( 'background' => "$this->main_css_element .squad-post-container .post .squad-post-element" );
		$fields['element_margin']           = array( 'margin' => "$this->main_css_element .squad-post-container .post .squad-post-element" );
		$fields['element_padding']          = array( 'padding' => "$this->main_css_element .squad-post-container .post .squad-post-element" );
		$this->squad_fix_border_transition( $fields, 'element_element', "$this->main_css_element .squad-post-container .post .squad-post-element" );
		$this->squad_fix_box_shadow_transition( $fields, 'element_element', "$this->main_css_element .squad-post-container .post .squad-post-element" );

		// Default styles.
		$fields['background_layout'] = array( 'color' => "$this->main_css_element .squad-post-container .post" );

		return $fields;
	}

	/**
	 * Render module output.
	 *
	 * @param array  $attrs       List of unprocessed attributes.
	 * @param string $content     Content being processed.
	 * @param string $render_slug Slug of module that is used for rendering output.
	 *
	 * @return string module's rendered output.
	 * @since 1.0.0
	 */
	public function render( $attrs, $content, $render_slug ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundInExtendedClassAfterLastUsed
		// Show a notice message in the frontend if the list item is empty.
		if ( '' === $this->content ) {
			return sprintf(
				'<div class="squad_notice">%s</div>',
				esc_html__( 'Add one or more element(s).', 'post-grid-module-for-divi' )
			);
		}

		$multi_view = et_pb_multi_view_options( $this );
		$post_html  = self::get_post_html( array_merge( $attrs, $this->props ), $this->content, $multi_view );
		if ( null !== $post_html ) {
			$this->squad_generate_all_styles( $attrs );
			$this->squad_generate_layout_styles( $attrs );

			// Load font Awesome css for frontend.
			Divi::inject_fa_icons( $this->prop( 'pagination_old_entries_icon', '&#x3c;||divi||400' ) );
			Divi::inject_fa_icons( $this->prop( 'pagination_next_entries_icon', '&#x3d;||divi||400' ) );

			return $post_html;
		}

		return sprintf(
			'<div class="squad_notice">%s</div>',
			esc_html__( 'Posts not available according to your criteria', 'post-grid-module-for-divi' )
		);
	}

	/**
	 * Generate styles.
	 *
	 * @param array $attrs List of unprocessed attributes.
	 *
	 * @return void
	 */
	private function squad_generate_all_styles( $attrs ) {
		// Fixed: the custom background doesn't work at frontend.
		$this->props = array_merge( $attrs, $this->props );

		// Post columns default, responsive.
		$this->squad_generate_additional_styles(
			array(
				'field'          => 'number_of_columns',
				'selector'       => "$this->main_css_element .squad-post-container",
				'css_property'   => 'grid-template-columns',
				'type'           => 'grid',
				'mapping_values' => function ( $current_value ) {
					return sprintf( 'repeat( %1$s, 1fr )', $current_value );
				},
			)
		);
		// post gap.
		$this->generate_styles(
			array(
				'base_attr_name' => 'item_gap',
				'selector'       => "$this->main_css_element .squad-post-container",
				'css_property'   => 'gap',
				'render_slug'    => $this->slug,
				'type'           => 'input',
			)
		);

		// background with default, responsive, hover.
		et_pb_background_options()->get_background_style(
			array(
				'base_prop_name'         => 'post_wrapper_background',
				'props'                  => $this->props,
				'selector'               => "$this->main_css_element .squad-post-container .post",
				'selector_hover'         => "$this->main_css_element .squad-post-container .post:hover",
				'selector_sticky'        => "$this->main_css_element .squad-post-container .post",
				'function_name'          => $this->slug,
				'important'              => ' !important',
				'use_background_video'   => false,
				'use_background_pattern' => false,
				'use_background_mask'    => false,
				'prop_name_aliases'      => array(
					'use_post_wrapper_background_color_gradient' => 'post_wrapper_background_use_color_gradient',
					'post_wrapper_background' => 'post_wrapper_background_color',
				),
			)
		);
		et_pb_background_options()->get_background_style(
			array(
				'base_prop_name'         => 'element_wrapper_background',
				'props'                  => $this->props,
				'selector'               => "$this->main_css_element .squad-post-container .post .post-elements",
				'selector_hover'         => "$this->main_css_element .squad-post-container .post:hover .post-elements",
				'selector_sticky'        => "$this->main_css_element .squad-post-container .post .post-elements",
				'function_name'          => $this->slug,
				'important'              => ' !important',
				'use_background_video'   => false,
				'use_background_pattern' => false,
				'use_background_mask'    => false,
				'prop_name_aliases'      => array(
					'use_element_wrapper_background_color_gradient' => 'element_wrapper_background_use_color_gradient',
					'element_wrapper_background' => 'element_wrapper_background_color',
				),
			)
		);
		et_pb_background_options()->get_background_style(
			array(
				'base_prop_name'         => 'element_background',
				'props'                  => $this->props,
				'selector'               => "$this->main_css_element .squad-post-container .post .squad-post-element",
				'selector_hover'         => "$this->main_css_element .squad-post-container .post:hover .squad-post-element",
				'selector_sticky'        => "$this->main_css_element .squad-post-container .post .squad-post-element",
				'function_name'          => $this->slug,
				'use_background_video'   => false,
				'use_background_pattern' => false,
				'use_background_mask'    => false,
				'prop_name_aliases'      => array(
					'use_element_background_color_gradient' => 'element_background_use_color_gradient',
					'element_background' => 'element_background_color',
				),
			)
		);

		// text aligns with default, responsive, hover.
		$this->generate_styles(
			array(
				'base_attr_name' => 'post_text_orientation',
				'selector'       => "$this->main_css_element .squad-post-container .post",
				'hover_selector' => "$this->main_css_element .squad-post-container .post",
				'css_property'   => 'text-align',
				'render_slug'    => $this->slug,
				'type'           => 'align',
			)
		);
		$this->generate_styles(
			array(
				'base_attr_name' => 'element_text_orientation',
				'selector'       => "$this->main_css_element .squad-post-container .post .post-elements",
				'selector_hover' => "$this->main_css_element .squad-post-container .post:hover .post-elements",
				'css_property'   => 'text-align',
				'render_slug'    => $this->slug,
				'type'           => 'align',
			)
		);

		// margin, padding with default, responsive, hover.
		$this->squad_generate_margin_padding_styles(
			array(
				'field'          => 'post_wrapper_margin',
				'selector'       => "$this->main_css_element .squad-post-container .post",
				'hover_selector' => "$this->main_css_element .squad-post-container .post:hover",
				'css_property'   => 'margin',
				'type'           => 'margin',
			)
		);
		$this->squad_generate_margin_padding_styles(
			array(
				'field'          => 'post_wrapper_padding',
				'selector'       => "$this->main_css_element .squad-post-container .post",
				'hover_selector' => "$this->main_css_element .squad-post-container .post:hover",
				'css_property'   => 'padding',
				'type'           => 'padding',
			)
		);
		$this->squad_generate_margin_padding_styles(
			array(
				'field'          => 'element_wrapper_margin',
				'selector'       => "$this->main_css_element .squad-post-container .post .post-elements",
				'hover_selector' => "$this->main_css_element .squad-post-container .post .post-elements",
				'css_property'   => 'margin',
				'type'           => 'margin',
			)
		);
		$this->squad_generate_margin_padding_styles(
			array(
				'field'          => 'element_wrapper_padding',
				'selector'       => "$this->main_css_element .squad-post-container .post .post-elements",
				'hover_selector' => "$this->main_css_element .squad-post-container .post .post-elements",
				'css_property'   => 'padding',
				'type'           => 'padding',
			)
		);
		$this->squad_generate_margin_padding_styles(
			array(
				'field'          => 'element_margin',
				'selector'       => "$this->main_css_element .squad-post-container .post .squad-post-element",
				'hover_selector' => "$this->main_css_element .squad-post-container .post:hover .squad-post-element",
				'css_property'   => 'margin',
				'type'           => 'margin',
				'important'      => false,
			)
		);
		$this->squad_generate_margin_padding_styles(
			array(
				'field'          => 'element_padding',
				'selector'       => "$this->main_css_element .squad-post-container .post .squad-post-element",
				'hover_selector' => "$this->main_css_element .squad-post-container .post:hover .squad-post-element",
				'css_property'   => 'padding',
				'type'           => 'padding',
				'important'      => false,
			)
		);
	}

	/**
	 * Generate styles.
	 *
	 * @param array $attrs List of unprocessed attributes.
	 *
	 * @return void
	 */
	private function squad_generate_layout_styles( $attrs ) {
		// Fixed: the custom background doesn't work at frontend.
		$this->props = array_merge( $attrs, $this->props );

		if ( 'on' === $this->prop( 'pagination__enable', 'off' ) ) {
			// background with default, responsive, hover.
			et_pb_background_options()->get_background_style(
				array(
					'base_prop_name'         => 'pagination_wrapper_background',
					'props'                  => $this->props,
					'selector'               => "$this->main_css_element div .squad-pagination",
					'selector_hover'         => "$this->main_css_element div .squad-pagination:hover",
					'selector_sticky'        => "$this->main_css_element div .squad-pagination",
					'function_name'          => $this->slug,
					'important'              => ' !important',
					'use_background_video'   => false,
					'use_background_pattern' => false,
					'use_background_mask'    => false,
					'prop_name_aliases'      => array(
						'use_pagination_wrapper_background_color_gradient' => 'pagination_wrapper_background_use_color_gradient',
						'pagination_wrapper_background' => 'pagination_wrapper_background_color',
					),
				)
			);
			et_pb_background_options()->get_background_style(
				array(
					'base_prop_name'         => 'pagination_background',
					'props'                  => $this->props,
					'selector'               => "$this->main_css_element div .squad-pagination .pagination-numbers .page-numbers, $this->main_css_element div .squad-pagination .pagination-entries",
					'selector_hover'         => "$this->main_css_element div .squad-pagination .pagination-numbers .page-numbers:hover, $this->main_css_element div .squad-pagination .pagination-entries:hover",
					'selector_sticky'        => "$this->main_css_element div .squad-pagination .pagination-numbers .page-numbers, $this->main_css_element div .squad-pagination .pagination-entries",
					'function_name'          => $this->slug,
					'important'              => ' !important',
					'use_background_video'   => false,
					'use_background_pattern' => false,
					'use_background_mask'    => false,
					'prop_name_aliases'      => array(
						'use_pagination_background_color_gradient' => 'pagination_background_use_color_gradient',
						'pagination_background' => 'pagination_background_color',
					),
				)
			);
			et_pb_background_options()->get_background_style(
				array(
					'base_prop_name'         => 'active_pagination_background',
					'props'                  => $this->props,
					'selector'               => "$this->main_css_element div .squad-pagination .pagination-numbers .page-numbers.current",
					'selector_hover'         => "$this->main_css_element div .squad-pagination .pagination-numbers .page-numbers.current:hover",
					'selector_sticky'        => "$this->main_css_element div .squad-pagination .pagination-numbers .page-numbers.current",
					'function_name'          => $this->slug,
					'important'              => ' !important',
					'use_background_video'   => false,
					'use_background_pattern' => false,
					'use_background_mask'    => false,
					'prop_name_aliases'      => array(
						'use_active_pagination_background_color_gradient' => 'active_pagination_background_use_color_gradient',
						'active_pagination_background' => 'active_pagination_background_color',
					),
				)
			);

			// Pagination horizontal alignment with default, responsive, hover.
			$this->generate_styles(
				array(
					'base_attr_name' => 'pagination_horizontal_alignment',
					'selector'       => "$this->main_css_element div .squad-pagination",
					'hover_selector' => "$this->main_css_element div .squad-pagination:hover",
					'css_property'   => 'justify-content',
					'render_slug'    => $this->slug,
					'type'           => 'align',
					'important'      => true,
				)
			);

			// pagination icon with default, responsive, hover.
			$this->generate_styles(
				array(
					'utility_arg'    => 'icon_font_family',
					'render_slug'    => $this->slug,
					'base_attr_name' => 'pagination_old_entries_icon',
					'important'      => true,
					'selector'       => "$this->main_css_element div .squad-pagination .pagination-entries .squad-pagination_old_entries-icon.et-pb-icon",
					'processor'      => array(
						'ET_Builder_Module_Helper_Style_Processor',
						'process_extended_icon',
					),
				)
			);
			$this->generate_styles(
				array(
					'utility_arg'    => 'icon_font_family',
					'render_slug'    => $this->slug,
					'base_attr_name' => 'pagination_next_entries_icon',
					'important'      => true,
					'selector'       => "$this->main_css_element div .squad-pagination .pagination-entries .squad-pagination_next_entries-icon.et-pb-icon",
					'processor'      => array(
						'ET_Builder_Module_Helper_Style_Processor',
						'process_extended_icon',
					),
				)
			);

			$this->generate_styles(
				array(
					'base_attr_name' => 'pagination_icon_color',
					'selector'       => "$this->main_css_element div .squad-pagination .pagination-entries span.et-pb-icon",
					'hover_selector' => "$this->main_css_element div .squad-pagination .pagination-entries:hover span.et-pb-icon",
					'css_property'   => 'color',
					'render_slug'    => $this->slug,
					'type'           => 'color',
					'important'      => true,
				)
			);
			$this->generate_styles(
				array(
					'base_attr_name' => 'pagination_icon_size',
					'selector'       => "$this->main_css_element div .squad-pagination .pagination-entries span.et-pb-icon",
					'hover_selector' => "$this->main_css_element div .squad-pagination .pagination-entries:hover span.et-pb-icon",
					'css_property'   => 'font-size',
					'render_slug'    => $this->slug,
					'type'           => 'range',
					'important'      => true,
				)
			);
			$this->generate_styles(
				array(
					'base_attr_name' => 'pagination_icon_text_gap',
					'selector'       => "$this->main_css_element div .squad-pagination .pagination-entries",
					'hover'          => "$this->main_css_element div .squad-pagination .pagination-entries:hover",
					'css_property'   => 'gap',
					'render_slug'    => $this->slug,
					'type'           => 'input',
					'important'      => true,
				)
			);
			$this->generate_styles(
				array(
					'base_attr_name' => 'pagination_elements_gap',
					'selector'       => "$this->main_css_element div .squad-pagination, $this->main_css_element div .squad-pagination .pagination-numbers",
					'hover'          => "$this->main_css_element div .squad-pagination:hover, $this->main_css_element div .squad-pagination .pagination-numbers:hover",
					'css_property'   => 'gap',
					'render_slug'    => $this->slug,
					'type'           => 'input',
					'important'      => true,
				)
			);

			// wrapper margin with default, responsive, hover.
			$this->squad_generate_margin_padding_styles(
				array(
					'field'          => 'pagination_wrapper_margin',
					'selector'       => "$this->main_css_element div .squad-pagination",
					'hover_selector' => "$this->main_css_element div .squad-pagination:hover",
					'css_property'   => 'margin',
					'type'           => 'margin',
				)
			);
			$this->squad_generate_margin_padding_styles(
				array(
					'field'          => 'pagination_wrapper_padding',
					'selector'       => "$this->main_css_element div .squad-pagination",
					'hover_selector' => "$this->main_css_element div .squad-pagination:hover",
					'css_property'   => 'padding',
					'type'           => 'padding',
				)
			);

			// pagination margin with default, responsive, hover.
			$this->squad_generate_margin_padding_styles(
				array(
					'field'          => 'pagination_margin',
					'selector'       => "$this->main_css_element div .squad-pagination .pagination-numbers .page-numbers, $this->main_css_element div .squad-pagination .pagination-entries",
					'hover_selector' => "$this->main_css_element div .squad-pagination .pagination-numbers .page-numbers:hover, $this->main_css_element div .squad-pagination .pagination-entries:hover",
					'css_property'   => 'margin',
					'type'           => 'margin',
				)
			);
			$this->squad_generate_margin_padding_styles(
				array(
					'field'          => 'pagination_padding',
					'selector'       => "$this->main_css_element div .squad-pagination .pagination-numbers .page-numbers, $this->main_css_element div .squad-pagination .pagination-entries",
					'hover_selector' => "$this->main_css_element div .squad-pagination .pagination-numbers .page-numbers:hover, $this->main_css_element div .squad-pagination .pagination-entries:hover",
					'css_property'   => 'padding',
					'type'           => 'padding',
				)
			);

			// active pagination margin with default, responsive, hover.
			$this->squad_generate_margin_padding_styles(
				array(
					'field'          => 'active_pagination_margin',
					'selector'       => "$this->main_css_element div .squad-pagination .pagination-numbers .page-numbers.current",
					'hover_selector' => "$this->main_css_element div .squad-pagination .pagination-numbers .page-numbers.current:hover",
					'css_property'   => 'margin',
					'type'           => 'margin',
				)
			);
			$this->squad_generate_margin_padding_styles(
				array(
					'field'          => 'active_pagination_padding',
					'selector'       => "$this->main_css_element div .squad-pagination .pagination-numbers .page-numbers.current",
					'hover_selector' => "$this->main_css_element div .squad-pagination .pagination-numbers .page-numbers.current:hover",
					'css_property'   => 'padding',
					'type'           => 'padding',
				)
			);

			// icon margin with default, responsive, hover.
			$this->squad_generate_margin_padding_styles(
				array(
					'field'          => 'pagination_icon_margin',
					'selector'       => "$this->main_css_element div .squad-pagination .pagination-entries span.et-pb-icon",
					'hover_selector' => "$this->main_css_element div .squad-pagination .pagination-entries:hover span.et-pb-icon",
					'css_property'   => 'margin',
					'type'           => 'margin',
				)
			);
			$this->squad_generate_margin_padding_styles(
				array(
					'field'          => 'pagination_icon_padding',
					'selector'       => "$this->main_css_element div .squad-pagination .pagination-entries span.et-pb-icon",
					'hover_selector' => "$this->main_css_element div .squad-pagination .pagination-entries:hover span.et-pb-icon",
					'css_property'   => 'padding',
					'type'           => 'padding',
				)
			);
		}
	}

	/**
	 * Collect all posts from the database.
	 *
	 * @param array                                     $attrs      List of unprocessed attributes.
	 * @param string                                    $content    Content being processed.
	 * @param ET_Builder_Module_Helper_MultiViewOptions $multi_view Multiview object instance.
	 *
	 * @return string the html output for the post-grid.
	 * @since 1.0.0
	 */
	public static function get_post_html( $attrs, $content = null, $multi_view = null ) {
		global $paged;

		$query_args   = array();
		$is_load_more = ! empty( $attrs['is_load_more_query'] ) ? $attrs['is_load_more_query'] : 'off';
		$date_format  = ! empty( $attrs['date_format'] ) ? $attrs['date_format'] : 'M j, Y';
		$inherit_loop = ! empty( $attrs['inherit_current_loop'] ) ? $attrs['inherit_current_loop'] : 'off';

		// Verify the feature ability.
		$pagination = ! empty( $attrs['pagination__enable'] ) ? $attrs['pagination__enable'] : 'off';

		if ( 'on' === $inherit_loop && is_singular() ) {
			$post_id    = get_the_ID();
			$categories = wp_get_post_categories( $post_id, array( 'fields' => 'ids' ) );
			if ( is_array( $categories ) && array() !== $categories ) {
				$query_args['cat'] = implode( ',', $categories );
			} else {
				$tags = wp_get_post_tags( $post_id, array( 'fields' => 'ids' ) );
				if ( is_array( $tags ) && array() !== $tags ) {
					$query_args['tag__in'] = $tags;
				}
			}

			$query_args['post__not_in'] = array( $post_id );
			$query_args['author']       = get_the_author_meta( 'ID' );
		}

		if ( 'off' === $inherit_loop && ! is_singular() ) {
			$display_by = ! empty( $attrs['post_display_by'] ) ? $attrs['post_display_by'] : 'recent';
			if ( 'recent' !== $display_by ) {
				if ( 'category' === $display_by && ! empty( $attrs['post_include_categories'] ) ) {
					$query_args['cat'] = $attrs['post_include_categories'];
				}
				if ( 'tag' === $display_by && ! empty( $attrs['post_include_categories'] ) ) {
					$query_args['tag__in'] = $attrs['post_include_tags'];
				}
			}
		}

		if ( 'on' === $pagination ) {
			$query_args['paged'] = $paged;
		}

		if ( ! empty( $attrs['post_offset'] ) ) {
			$post_offset = (int) $attrs['post_offset'];
			if ( ! empty( $attrs['pagination__enable'] ) && 'on' === $attrs['pagination__enable'] && $paged > 1 ) {
				$query_args['offset'] = ( ( $paged - 1 ) * $post_offset ) + $post_offset;
			} else {
				$query_args['offset'] = $post_offset;
			}
		}

		// WP post query arguments.
		$query_args['orderby']        = ! empty( $attrs['post_order_by'] ) ? $attrs['post_order_by'] : 'date';
		$query_args['order']          = ! empty( $attrs['post_order'] ) ? $attrs['post_order'] : 'ASC';
		$query_args['posts_per_page'] = ! empty( $attrs['post_count'] ) ? (int) $attrs['post_count'] : 10;
		$query_args['post_status']    = array( 'publish' );

		// extra query parameters.
		$query_args['ignore_sticky_posts'] = ! empty( $attrs['post_ignore_sticky_posts'] ) && 'on' === $attrs['post_ignore_sticky_posts'];

		$post_query = new WP_Query( $query_args );

		if ( $post_query->have_posts() ) {
			$is_divi_builder = isset( $content ) && ! is_string( $content );

			ob_start();

			print '<ul class="squad-post-container" style="list-style-type: none;">';

			foreach ( $post_query->get_posts() as $post ) {
				$post_classes = get_post_class( 'post', $post );
				print sprintf( '<li class="%1$s">', esc_attr( implode( ' ', $post_classes ) ) );

				if ( $is_divi_builder ) {
					$date_replacement = str_replace( '\\\\', '\\', $date_format );

					$author            = get_userdata( $post->post_author );
					$author_first_name = $author->first_name;
					$author_last_name  = $author->last_name;

					$post_data = array(
						'id'         => $post->ID,
						'title'      => $post->post_title,
						'excerpt'    => $post->post_excerpt,
						'comments'   => $post->comment_count,
						'date'       => $post->post_date,
						'modified'   => $post->post_modified,
						'content'    => wp_strip_all_tags( $post->post_content ),
						'image'      => get_the_post_thumbnail( $post->ID, 'full' ),
						'categories' => wp_get_post_categories( $post->ID, array( 'fields' => 'names' ) ),
						'tags'       => wp_get_post_tags( $post->ID, array( 'fields' => 'names' ) ),
						'permalink'  => get_permalink( $post->ID ),
						'author'     => array(
							'nickname'     => $author->user_nicename,
							'display-name' => $author->display_name,
							'full-name'    => sprintf( '%1$s %2$s', $author_first_name, $author_last_name ),
							'first-name'   => $author_first_name,
							'last-name'    => $author_last_name,
						),
						'gravatar'   => get_avatar( $post->post_author, 40 ),
						'formatted'  => array(
							'publish'  => wp_date( $date_replacement, strtotime( $post->post_date ) ),
							'modified' => wp_date( $date_replacement, strtotime( $post->post_modified ) ),
						),
					);

					print sprintf(
						'<script style="display: none">%s</script>',
						wp_json_encode( $post_data )
					);
				}

				print '<div class="squad-post-outer">';

				do_action( 'squadpostgrid_query_current_outside_post_element', $post, $attrs, $content );

				print '</div>';

				print '<div class="squad-post-inner">';

				do_action( 'squadpostgrid_query_current_main_post_element', $post, $attrs, $content );

				print '</div></li>';
			}

			print '</ul>';

			// Verify the ability of load more of pagination.
			$is_posts_exists = $post_query->found_posts > $query_args['posts_per_page'];

			if ( ! $is_divi_builder && $is_posts_exists && 'off' === $is_load_more && 'on' === $pagination ) {
				$prev_text         = ''; // &#x3c;
				$next_text         = ''; // &#x3d;
				$icon_only__enable = ! empty( $attrs['pagination_icon_only__enable'] ) ? $attrs['pagination_icon_only__enable'] : 'off';
				$numbers__enable   = ! empty( $attrs['pagination_numbers__enable'] ) ? $attrs['pagination_numbers__enable'] : 'off';
				$old_entries_text  = ! empty( $attrs['pagination_old_entries_text'] ) ? esc_html( $attrs['pagination_old_entries_text'] ) : __( 'Old Entries', 'post-grid-module-for-divi' );
				$next_entries_text = ! empty( $attrs['pagination_next_entries_text'] ) ? esc_html( $attrs['pagination_next_entries_text'] ) : __( 'Next Entries', 'post-grid-module-for-divi' );

				// Set icon for pagination prev element.
				$prev_text .= $multi_view->render_element(
					array(
						'content'        => '{{pagination_old_entries_icon}}',
						'attrs'          => array(
							'class' => 'et-pb-icon squad-pagination_old_entries-icon',
						),
						'custom_props'   => $attrs,
						'hover_selector' => '%%order_class%%.squad_post_grid div .squad-pagination .pagination-entries.prev',
					)
				);

				// Set text for pagination prev and next element.
				if ( 'on' !== $icon_only__enable ) {
					$prev_text .= sprintf( '<span class="entries-text">%1$s</span>', esc_html( $old_entries_text ) );
					$next_text .= sprintf( '<span class="entries-text">%1$s</span>', esc_html( $next_entries_text ) );
				}

				// Set icon for pagination next element.
				$next_text .= $multi_view->render_element(
					array(
						'content'        => '{{pagination_next_entries_icon}}',
						'attrs'          => array(
							'class' => 'et-pb-icon squad-pagination_next_entries-icon',
						),
						'custom_props'   => $attrs,
						'hover_selector' => '%%order_class%%.squad_post_grid div .squad-pagination .pagination-entries.next',
					)
				);

				// Collect all links for pagination.
				$paginate_links = paginate_links(
					array(
						'format'    => '?paged=%#%', // ?page=%#% : %#% is replaced by the page number.
						'current'   => max( 1, get_query_var( 'paged' ) ),
						'total'     => $post_query->max_num_pages,
						'prev_text' => $prev_text,
						'next_text' => $next_text,
						'type'      => 'array',
					)
				);

				if ( isset( $paginate_links ) && count( $paginate_links ) ) {
					print '<div class="squad-pagination clearfix">';
					$is_prev_found  = false;
					$is_next_found  = false;
					$first_paginate = array_shift( $paginate_links );
					$last_paginate  = array_pop( $paginate_links );

					$paginate_prev_text = '';
					$paginate_next_text = '';

					// Update class name for the fist paginate link.
					if ( false !== strpos( $first_paginate, 'prev' ) ) {
						$is_prev_found      = true;
						$paginate_prev_text = str_replace( 'page-numbers', 'pagination-entries', $first_paginate );
					}

					// Update class name for the last paginate link.
					if ( false !== strpos( $last_paginate, 'next' ) ) {
						$is_next_found      = true;
						$paginate_next_text = str_replace( 'page-numbers', 'pagination-entries', $last_paginate );
					}

					// Show the fist paginate link.
					if ( $is_prev_found ) {
						print wp_kses_post( $paginate_prev_text );
					}

					// Show the last paginated numbers.
					if ( ( 'on' === $numbers__enable ) && ( false === $is_prev_found || false === $is_next_found || count( $paginate_links ) ) ) {
						print '<div class="pagination-numbers">';
						if ( false === $is_prev_found ) {
							print wp_kses_post( $first_paginate );
						}
						if ( count( $paginate_links ) ) {
							foreach ( $paginate_links as $paginate_link ) {
								print wp_kses_post( $paginate_link );
							}
						}
						if ( false === $is_next_found ) {
							print wp_kses_post( $last_paginate );
						}
						print '</div>';
					}

					// Show the last paginate link.
					if ( $is_next_found ) {
						print wp_kses_post( $paginate_next_text );
					}

					print '</div>';
				}
			}

			return ob_get_clean();
		}

		/* Restore original Post Data */
		wp_reset_postdata();

		return null;
	}

	/**
	 * Render the post-elements in the outside wrapper.
	 *
	 * @param WP_Post $post    The current post.
	 * @param array   $attrs   The parent attributes.
	 * @param string  $content The parent content.
	 *
	 * @return void
	 */
	public function wp_hook_squad_current_outside_post_element( $post, $attrs, $content ) {
		$callback = function ( $post, $child_prop ) {
			$outside_enable = isset( $child_prop['element_outside__enable'] ) ? $child_prop['element_outside__enable'] : 'off';

			if ( 'on' === $outside_enable ) {
				$output        = '';
				$element_type  = isset( $child_prop['element'] ) ? $child_prop['element'] : 'none';
				$icon_type     = isset( $child_prop['element_icon_type'] ) ? $child_prop['element_icon_type'] : 'icon';
				$icon_excludes = array( 'none', 'title', 'image', 'content', 'gravatar', 'divider' );

				// Verify icon element.
				if ( 'none' !== $icon_type && ! in_array( $element_type, $icon_excludes, true ) ) {
					$output .= et_core_esc_previously( $this->squad_render_element_icon( $child_prop ) );
				}

				// Append the element content.
				$output .= et_core_esc_previously( $this->squad_render_post_element_body( $child_prop, $post ) );

				return sprintf(
					'<div class="post-elements et_pb_with_background">%1$s</div>',
					et_core_esc_previously( $output )
				);
			}

			return false;
		};

		$this->squad_generate_props_content( $post, $attrs, $content, $callback );
	}

	/**
	 * Render the post-elements in the main wrapper.
	 *
	 * @param WP_Post $post    The WP POST object.
	 * @param array   $attrs   The parent attributes.
	 * @param string  $content The parent content.
	 *
	 * @return void
	 */
	public function wp_hook_squad_current_main_post_element( $post, $attrs, $content ) {
		$callback = function ( $post, $child_prop ) {
			$outside_enable = isset( $child_prop['element_outside__enable'] ) ? $child_prop['element_outside__enable'] : 'off';

			if ( 'off' === $outside_enable ) {
				$output        = '';
				$element_type  = isset( $child_prop['element'] ) ? $child_prop['element'] : 'none';
				$icon_type     = isset( $child_prop['element_icon_type'] ) ? $child_prop['element_icon_type'] : 'icon';
				$icon_excludes = array( 'none', 'title', 'image', 'content', 'gravatar', 'divider' );

				// Verify icon element.
				if ( 'none' !== $icon_type && ! in_array( $element_type, $icon_excludes, true ) ) {
					$output .= et_core_esc_previously( $this->squad_render_element_icon( $child_prop ) );
				}

				// Append the element content.
				$output .= et_core_esc_previously( $this->squad_render_post_element_body( $child_prop, $post ) );

				return sprintf(
					'<div class="post-elements et_pb_with_background">%1$s</div>',
					et_core_esc_previously( $output )
				);
			}

			return false;
		};

		$this->squad_generate_props_content( $post, $attrs, $content, $callback );
	}

	/**
	 * Generate content by props with dyanmic values.
	 *
	 * @param WP_Post  $post        The WP POST object.
	 * @param array    $parent_prop The parent attributes.
	 * @param string   $content     The parent content.
	 * @param callable $callback    The render callback.
	 *
	 * @return void
	 */
	public function squad_generate_props_content( $post, $parent_prop, $content, $callback ) {
		// Collect all child modules from Html content.
		$pattern = '/<div\s+class="[^"]*squad_post_grid_child[^"]*"[^>]*>.*?<\/div>\s+<\/div>/is';
		if ( is_string( $content ) && preg_match_all( $pattern, $content, $matches ) && isset( $matches[0] ) && count( $matches[0] ) ) {
			// Catch module with the main wrapper.
			$child_modules = $matches[0];

			// Output the split tags.
			foreach ( $child_modules as $child_module_content ) {
				$child_raw_props = Module::collect_raw_props( $child_module_content );
				$clean_props     = str_replace( '},||', '},', $child_raw_props );
				$child_props     = Module::collect_child_json_props( $clean_props );

				if ( count( $child_props ) && isset( $child_props[0] ) ) {
					$child_prop_markup = sprintf( '%s,||', wp_json_encode( $child_props[0] ) );
					$html_output       = $callback( $post, $child_props[0], $parent_prop );

					// check available content.
					if ( is_string( $html_output ) ) {
						// Merge with raw content.
						print wp_kses_post( str_replace( $child_prop_markup, $html_output, $child_module_content ) );
					}
				}
			}
		}

		print null;
	}

	/**
	 * Render element body.
	 *
	 * @param array         $attrs        List of attributes.
	 * @param false|WP_POST $post The current post-object.
	 *
	 * @return null|string
	 */
	private function squad_render_post_element_body( $attrs, $post ) {
		$element    = ! empty( $attrs['element'] ) ? $attrs['element'] : 'none';
		$class_name = sprintf( 'squad-post-element squad-post-element__%1$s et_pb_with_background', $element );
		$post_title = $post->post_title;
		$post_id    = $post->ID;

		$post_excerpt__enable = ! empty( $attrs['element_excerpt__enable'] ) ? $attrs['element_excerpt__enable'] : 'off';
		$post_content         = ( 'on' === $post_excerpt__enable ) ? $post->post_excerpt : wp_strip_all_tags( $post->post_content );

		$categories  = wp_get_post_categories( $post_id, array( 'fields' => 'names' ) );
		$tags        = wp_get_post_tags( $post_id, array( 'fields' => 'names' ) );
		$custom_text = ! empty( $attrs['element_custom_text'] ) ? $attrs['element_custom_text'] : '';

		if ( 'title' === $element && ! empty( $post_title ) ) {
			$title_tag = ! empty( $attrs['element_title_tag'] ) ? $attrs['element_title_tag'] : 'span';
			$content   = sprintf( '<span class="element-text">%1$s</span>', ucfirst( $post_title ) );

			if ( 'on' === $attrs['element_title_icon__enable'] && '' !== $attrs['element_title_icon'] ) {
				$title_icon = $this->squad_render_post_title_font_icon( $attrs );
			} else {
				$title_icon = '';
			}

			return sprintf(
				'<div class="%1$s"><%3$s class="element-text">%2$s</%3$s>%4$s</div>',
				$class_name,
				$content,
				$title_tag,
				$title_icon
			);
		}

		if ( 'image' === $element ) {
			$post_image = get_the_post_thumbnail( $post_id, 'full' );
			if ( ! empty( $post_image ) ) {
				return sprintf(
					'<div class="%1$s">%2$s</div>',
					$class_name,
					$post_image
				);
			}

			return '';
		}

		if ( 'content' === $element && ! empty( $post_content ) ) {
			$post_content_length__enable = ! empty( $attrs['element_ex_con_length__enable'] ) ? $attrs['element_ex_con_length__enable'] : 'off';
			$post_content_length         = ! empty( $attrs['element_ex_con_length'] ) ? (int) $attrs['element_ex_con_length'] : 20;

			$character_map      = 'äëïöüÄËÏÖÜáǽćéíĺńóŕśúźÁǼĆÉÍĹŃÓŔŚÚŹ';
			$character_map     .= 'àèìòùÀÈÌÒÙãẽĩõñũÃẼĨÕÑŨâêîôûÂÊÎÔÛăĕğĭŏœ̆ŭĂĔĞĬŎŒ̆Ŭ';
			$character_map     .= 'āēīōūĀĒĪŌŪőűŐŰąęįųĄĘĮŲåůÅŮæÆøØýÝÿŸþÞẞßđĐıIœŒ';
			$character_map     .= 'čďěľňřšťžČĎĚĽŇŘŠŤŽƒƑðÐłŁçģķļșțÇĢĶĻȘȚħĦċėġżĊĖĠŻʒƷǯǮŋŊŧŦ';
			$character_map     .= ':~^';
			$post_content_words = Str::word_count( $post_content, 2, $character_map );

			if ( 'on' === $post_content_length__enable && count( $post_content_words ) > $post_content_length ) {
				$content = implode( ' ', array_slice( $post_content_words, 0, $post_content_length ) );

				return sprintf(
					'<div class="%1$s"><span>%2$s</span></div>',
					$class_name,
					$content
				);
			}

			return sprintf(
				'<div class="%1$s"><span>%2$s</span></div>',
				$class_name,
				$post_content
			);
		}

		if ( 'author' === $element ) {
			$author_name_type = ! empty( $attrs['element_author_name_type'] ) ? $attrs['element_author_name_type'] : 'nickname';
			$author           = get_userdata( $post->post_author );

			switch ( $author_name_type ) {
				case 'nickname':
					$content = $author->nickname;
					break;
				case 'first-name':
					$content = $author->first_name;
					break;
				case 'last-name':
					$content = $author->last_name;
					break;
				case 'full-name':
					$content = sprintf( '%1$s %2$s', $author->first_name, $author->last_name );
					break;
				default:
					$content = $author->display_name;
			}

			return sprintf(
				'<div class="%1$s"><span>%2$s</span></div>',
				$class_name,
				$content
			);
		}

		if ( 'gravatar' === $element ) {
			$gravatar_size = ! empty( $attrs['element_gravatar_size'] ) ? (int) $attrs['element_gravatar_size'] : 40;
			$gravatar      = get_avatar( $post->post_author, $gravatar_size );

			return sprintf(
				'<div class="%1$s">%2$s</div>',
				esc_attr( $class_name ),
				wp_kses_post( $gravatar )
			);
		}

		if ( 'date' === $element ) {
			$element_date_type = ! empty( $attrs['element_date_type'] ) ? $attrs['element_date_type'] : 'publish';
			$date_format       = ! empty( $parent_prop['date_format'] ) ? $parent_prop['date_format'] : 'M j, Y';
			$date              = 'modified' === $element_date_type ? $post->post_modified : $post->post_date;

			return sprintf(
				'<div class="%1$s"><time datetime="%3$s">%2$s</time></div>',
				$class_name,
				wp_date( $date_format, strtotime( $date ) ),
				$date
			);
		}

		if ( 'read_more' === $element ) {
			$permalink_url  = get_permalink( $post_id );
			$read_more_text = ! empty( $attrs['element_read_more_text'] ) ? $attrs['element_read_more_text'] : esc_html__( 'Read More', 'post-grid-module-for-divi' );

			return sprintf(
				'<div class="%1$s"><a href="%2$s" title="Read the post">%3$s</a></div>',
				$class_name,
				$permalink_url,
				$read_more_text
			);
		}

		if ( 'comments' === $element ) {
			$comment_before_text = ! empty( $attrs['element_comment_before_text'] ) ? $attrs['element_comment_before_text'] : '';
			$comment_after_text  = ! empty( $attrs['element_comments_after'] ) ? $attrs['element_comments_after'] : '';

			return sprintf(
				'<div class="%1$s"><span class="element-text">%3$s%2$s%4$s</span></div>',
				$class_name,
				$post->comment_count,
				$comment_before_text,
				$comment_after_text
			);
		}

		if ( 'categories' === $element && count( $categories ) ) {
			$categories_separator = ! empty( $attrs['element_categories_sepa'] ) ? $attrs['element_categories_sepa'] : '';

			return sprintf(
				'<div class="%1$s"><span class="element-text">%2$s</span></div>',
				esc_attr( $class_name ),
				esc_attr( implode( $categories_separator, $categories ) )
			);
		}

		if ( 'tags' === $element && count( $tags ) ) {
			$tags_separator = ! empty( $attrs['element_tags_sepa'] ) ? $attrs['element_tags_sepa'] : '';

			return sprintf(
				'<div class="%1$s"><span class="element-text">%2$s</span></div>',
				esc_attr( $class_name ),
				esc_attr( implode( $tags_separator, $tags ) )
			);
		}

		if ( 'custom_text' === $element && ! empty( $custom_text ) ) {
			return sprintf(
				'<div class="%1$s"><span class="element-text">%2$s</span></div>',
				$class_name,
				$custom_text
			);
		}

		if ( 'divider' === $element && ( 'off' !== $attrs['show_divider'] ) ) {
			$divider_position = ! empty( $attrs['divider_position'] ) ? $attrs['divider_position'] : 'bottom';
			$divider_classes  = implode( ' ', array( 'divider-element', $divider_position ) );

			return sprintf(
				'<div class="%1$s"><span class="%2$s">%2$s</span></div>',
				$class_name,
				$divider_classes
			);
		}

		return null;
	}

	/**
	 * Render post name icon.
	 *
	 * @param array $attrs List of attributes.
	 *
	 * @return null|string
	 */
	private function squad_render_post_title_font_icon( $attrs ) {
		$multi_view   = et_pb_multi_view_options( $this );
		$element_type = isset( $attrs['element'] ) ? $attrs['element'] : 'none';
		$icon_classes = array( 'et-pb-icon', 'squad-element_title-icon' );

		if ( 'on' !== $attrs['element_title_icon_show_on_hover'] ) {
			$icon_classes[] = 'always_show';
		}

		return $multi_view->render_element(
			array(
				'custom_props'   => $attrs,
				'content'        => '{{element_title_icon}}',
				'attrs'          => array(
					'class' => implode( ' ', $icon_classes ),
				),
				'hover_selector' => "$this->main_css_element div .post-elements .squad-post-element.squad-element_$element_type",
			)
		);
	}

	/**
	 * Render icon which on is active.
	 *
	 * @param array $attrs List of attributes.
	 *
	 * @return string
	 */
	private function squad_render_element_icon( $attrs ) {
		$wrapper_classes = array( 'squad-element-icon-wrapper' );

		if ( isset( $attrs['element_icon_on_hover'] ) && 'on' === $attrs['element_icon_on_hover'] ) {
			$wrapper_classes[] = 'show-on-hover';
		}

		return sprintf(
			'<span class="%1$s"><span class="icon-element">%2$s%3$s%4$s</span></span>',
			implode( ' ', $wrapper_classes ),
			et_core_esc_previously( $this->squad_render_element_font_icon( $attrs ) ),
			et_core_esc_previously( $this->squad_render_element_icon_image( $attrs ) ),
			et_core_esc_previously( $this->squad_render_element_icon_text( $attrs ) )
		);
	}

	/**
	 * Render icon.
	 *
	 * @param array $attrs List of unprocessed attributes.
	 *
	 * @return null|string
	 */
	private function squad_render_element_font_icon( $attrs ) {
		if ( 'icon' === $attrs['element_icon_type'] ) {
			$multi_view   = et_pb_multi_view_options( $this );
			$element_type = isset( $attrs['element'] ) ? $attrs['element'] : 'none';
			$icon_classes = array( 'et-pb-icon', 'squad_icon' );

			return $multi_view->render_element(
				array(
					'custom_props'   => $attrs,
					'content'        => '{{element_icon}}',
					'attrs'          => array(
						'class' => implode( ' ', $icon_classes ),
					),
					'hover_selector' => "$this->main_css_element div .post-elements .squad-post-element.squad-element_$element_type",
				)
			);
		}

		return null;
	}

	/**
	 * Render image.
	 *
	 * @param array $attrs List of unprocessed attributes.
	 *
	 * @return null|string
	 */
	private function squad_render_element_icon_image( $attrs ) {
		if ( 'image' === $attrs['element_icon_type'] ) {
			$multi_view    = et_pb_multi_view_options( $this );
			$alt_text      = $this->_esc_attr( 'alt' );
			$title_text    = $this->_esc_attr( 'title_text' );
			$element_type  = isset( $attrs['element'] ) ? $attrs['element'] : 'none';
			$image_classes = array( 'squad_image', 'et_pb_image_wrap' );

			$image_attachment_class = et_pb_media_options()->get_image_attachment_class( $this->props, 'element_image' );

			if ( ! empty( $image_attachment_class ) ) {
				$image_classes[] = esc_attr( $image_attachment_class );
			}

			return $multi_view->render_element(
				array(
					'custom_props'   => $attrs,
					'tag'            => 'img',
					'attrs'          => array(
						'src'   => '{{element_image}}',
						'class' => implode( ' ', $image_classes ),
						'alt'   => $alt_text,
						'title' => $title_text,
					),
					'required'       => 'element_image',
					'hover_selector' => "$this->main_css_element div .post-elements .squad-post-element.squad-element_$element_type",
				)
			);
		}

		return null;
	}

	/**
	 * Render image.
	 *
	 * @param array $attrs List of unprocessed attributes.
	 *
	 * @return null|string
	 */
	private function squad_render_element_icon_text( $attrs ) {
		if ( 'text' === $attrs['element_icon_type'] ) {
			$multi_view        = et_pb_multi_view_options( $this );
			$element_type      = isset( $attrs['element'] ) ? $attrs['element'] : 'none';
			$icon_text_classes = array( 'squad-list-icon-text' );

			return $multi_view->render_element(
				array(
					'custom_props'   => $attrs,
					'content'        => '{{element_icon_text}}',
					'attrs'          => array(
						'class' => implode( ' ', $icon_text_classes ),
					),
					'hover_selector' => "$this->main_css_element div .post-elements .squad-post-element.squad-element_$element_type",
				)
			);
		}

		return null;
	}

	/**
	 * Filter multi view value.
	 *
	 * @param mixed $raw_value Props raw value.
	 * @param array $args      Props arguments.
	 *
	 * @return mixed
	 *
	 * @see   ET_Builder_Module_Helper_MultiViewOptions::filter_value
	 */
	public function multi_view_filter_value( $raw_value, $args ) {
		$name = isset( $args['name'] ) ? $args['name'] : '';

		// process font icon.
		$icon_fields = array(
			'element_icon',
			'element_title_icon',
			'pagination_old_entries_icon',
			'pagination_next_entries_icon',
		);
		if ( $raw_value && in_array( $name, $icon_fields, true ) ) {
			return et_pb_get_extended_font_icon_value( $raw_value, true );
		}

		// process others.
		return $raw_value;
	}
}

// Load the Post Grid Module.
$squadpostgrid_module = new PostGrid();

// Registers all hook for processing post-elements.
$squadpostgrid_element_outside_callback = array( $squadpostgrid_module, 'wp_hook_squad_current_outside_post_element' );
$squadpostgrid_element_inner_callback   = array( $squadpostgrid_module, 'wp_hook_squad_current_main_post_element' );
add_action( 'squadpostgrid_query_current_outside_post_element', $squadpostgrid_element_outside_callback, 10, 3 );
add_action( 'squadpostgrid_query_current_main_post_element', $squadpostgrid_element_inner_callback, 10, 3 );
