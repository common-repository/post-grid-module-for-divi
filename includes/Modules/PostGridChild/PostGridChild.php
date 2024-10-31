<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase

/**
 * Post-Grid Child Module Class which extend the Divi Builder Module Class.
 *
 * This class provides post-element adding functionalities for the parent module in the visual builder.
 *
 * @since       1.0.0
 * @package     post-grid-module-for-divi
 * @author      WP Squad <wp@thewpsquad.com>
 * @copyright   2023 WP Squad
 * @license     GPL-3.0-only
 */

namespace SquadPostGrid\Modules\PostGridChild;

use SquadPostGrid\Base\BuilderModule\Squad_Builder_Module;
use SquadPostGrid\Utils\Divi;
use SquadPostGrid\Utils\Module;
use function esc_attr__;
use function esc_html__;
use function et_builder_get_element_style_css;
use function et_pb_background_options;
use function wp_json_encode;

/**
 * Post-Grid Child Module Class.
 *
 * @since       1.0.0
 * @package     post-grid-module-for-divi
 */
class PostGridChild extends Squad_Builder_Module {
	/**
	 * The list of element types
	 *
	 * @var array
	 */
	protected $element_types;

	/**
	 * The list of icon eligible element
	 *
	 * @var array
	 */
	protected $icon_not_eligible_elements;

	/**
	 * Initiate Module.
	 * Set the module name on init.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function init() {
		$this->name   = esc_html__( 'Post Element', 'post-grid-module-for-divi' );
		$this->plural = esc_html__( 'Post Elements', 'post-grid-module-for-divi' );

		$this->slug       = 'squad_post_grid_child';
		$this->type       = 'child';
		$this->vb_support = 'on';

		$this->child_title_var          = 'element';
		$this->child_title_fallback_var = 'admin_label';

		$this->main_css_element = "%%order_class%%.$this->slug";

		$this->element_types = array(
			'title'       => esc_html__( 'Title', 'post-grid-module-for-divi' ),
			'image'       => esc_html__( 'Feature Image', 'post-grid-module-for-divi' ),
			'content'     => esc_html__( 'Content', 'post-grid-module-for-divi' ),
			'author'      => esc_html__( 'Author', 'post-grid-module-for-divi' ), // nick, display, first, last, username.
			'gravatar'    => esc_html__( 'Author Gravatar', 'post-grid-module-for-divi' ),
			'date'        => esc_html__( 'Date', 'post-grid-module-for-divi' ),
			'read_more'   => esc_html__( 'Read More', 'post-grid-module-for-divi' ),
			'comments'    => esc_html__( 'Comments Count', 'post-grid-module-for-divi' ),
			'categories'  => esc_html__( 'Categories', 'post-grid-module-for-divi' ),
			'tags'        => esc_html__( 'Tags', 'post-grid-module-for-divi' ),
			'custom_text' => esc_html__( 'Custom Text', 'post-grid-module-for-divi' ),
			'divider'     => esc_html__( 'Divider', 'post-grid-module-for-divi' ),
		);

		// initiate the divider.
		$this->squad_initiate_the_divider_element();

		// The icon eligible elements.
		$this->icon_not_eligible_elements = array( 'none', 'title', 'image', 'content', 'gravatar', 'divider' );

		// Declare settings modal toggles for the module.
		$this->settings_modal_toggles = array(
			'general'  => array(
				'toggles' => array(
					'elements'             => esc_html__( 'Element', 'post-grid-module-for-divi' ),
					'element_icon_element' => esc_html__( 'Icon Element', 'post-grid-module-for-divi' ),
					'element_divider'      => esc_html__( 'Divider', 'post-grid-module-for-divi' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'element_wrapper'      => esc_html__( 'Wrapper', 'post-grid-module-for-divi' ),
					'element_icon_element' => esc_html__( 'Icon', 'post-grid-module-for-divi' ),
					'element_icon_text'    => esc_html__( 'Icon Text', 'post-grid-module-for-divi' ),
					'element'              => esc_html__( 'Element', 'post-grid-module-for-divi' ),
					'element_text'         => esc_html__( 'Element Text', 'post-grid-module-for-divi' ),
					'element_divider'      => esc_html__( 'Divider', 'post-grid-module-for-divi' ),
				),
			),
		);

		// Declare advanced fields for the module.
		$this->advanced_fields = array(
			'fonts'                => array(
				'element_icon_text' => $this->squad_add_font_field(
					esc_html__( 'Icon', 'post-grid-module-for-divi' ),
					array(
						'font_size'       => array(
							'default' => '16px',
						),
						'text_align'      => array(
							'show_if' => array(
								'element_icon_type' => 'text',
							),
						),
						'text_shadow'     => array(
							'show_if' => array(
								'element_icon_type' => 'text',
							),
						),
						'css'             => array(
							'main'  => "$this->main_css_element div .post-elements span.squad-element-icon-wrapper .squad-element-icon-text",
							'hover' => "$this->main_css_element div .post-elements:hover span.squad-element-icon-wrapper .squad-element-icon-text",
						),
						'depends_show_if' => 'text',
					)
				),
				'element_text'      => $this->squad_add_font_field(
					esc_html__( 'Element', 'post-grid-module-for-divi' ),
					array(
						'font_size'   => array(
							'default' => '16px',
						),
						'line_height' => array(
							'default' => '1.7',
						),
						'font_weight' => array(
							'default' => '400',
						),
						'css'         => array(
							'main'  => "$this->main_css_element div .post-elements .squad-post-element",
							'hover' => "$this->main_css_element div .post-elements:hover .squad-post-element",
						),
					)
				),
			),
			'background'           => Module::selectors_background( $this->main_css_element ),
			'element_icon_element' => array(
				'css' => array(
					'main' => "$this->main_css_element div .post-elements span.squad-element-icon-wrapper",
				),
			),
			'filters'              => array(
				'child_filters_target' => array(
					'label'               => et_builder_i18n( 'Icon' ),
					'tab_slug'            => 'advanced',
					'toggle_slug'         => 'element_icon_element',
					'css'                 => array(
						'main'  => "$this->main_css_element div .post-elements span.squad-element-icon-wrapper",
						'hover' => "$this->main_css_element div .post-elements:hover span.squad-element-icon-wrapper",
					),
					'depends_on'          => array( 'element_icon_type' ),
					'depends_show_if_not' => array( 'none', 'icon', 'text' ),
				),
			),
			'borders'              => array(
				'default'              => Module::selectors_default( $this->main_css_element ),
				'element_wrapper'      => array(
					'label_prefix' => et_builder_i18n( 'Wrapper' ),
					'tab_slug'     => 'advanced',
					'toggle_slug'  => 'element_wrapper',
					'css'          => array(
						'main' => array(
							'border_radii'        => "$this->main_css_element div .post-elements",
							'border_radii_hover'  => "$this->main_css_element div .post-elements:hover",
							'border_styles'       => "$this->main_css_element div .post-elements",
							'border_styles_hover' => "$this->main_css_element div .post-elements:hover",
						),
					),
				),
				'element_icon_element' => array(
					'label_prefix'        => et_builder_i18n( 'Icon' ),
					'tab_slug'            => 'advanced',
					'toggle_slug'         => 'element_icon_element',
					'css'                 => array(
						'main' => array(
							'border_radii'        => "$this->main_css_element div .post-elements span.squad-element-icon-wrapper .icon-element",
							'border_radii_hover'  => "$this->main_css_element div .post-elements:hover span.squad-element-icon-wrapper .icon-element",
							'border_styles'       => "$this->main_css_element div .post-elements span.squad-element-icon-wrapper .icon-element",
							'border_styles_hover' => "$this->main_css_element div .post-elements:hover span.squad-element-icon-wrapper .icon-element",
						),
					),
					'depends_on'          => array( 'element_icon_type' ),
					'depends_show_if_not' => array( 'none' ),
				),
				'element'              => array(
					'label_prefix'        => esc_html__( 'Element', 'post-grid-module-for-divi' ),
					'tab_slug'            => 'advanced',
					'toggle_slug'         => 'element',
					'css'                 => array(
						'main' => array(
							'border_radii'        => "$this->main_css_element div .post-elements .squad-post-element",
							'border_radii_hover'  => "$this->main_css_element div .post-elements:hover .squad-post-element",
							'border_styles'       => "$this->main_css_element div .post-elements .squad-post-element",
							'border_styles_hover' => "$this->main_css_element div .post-elements:hover .squad-post-element",
						),
					),
					'depends_on'          => array( 'element' ),
					'depends_show_if_not' => array( 'avatar' ),
				),
			),
			'box_shadow'           => array(
				'default'              => Module::selectors_default( $this->main_css_element ),
				'element_wrapper'      => array(
					'label'             => esc_html__( 'Wrapper Box Shadow', 'post-grid-module-for-divi' ),
					'option_category'   => 'layout',
					'tab_slug'          => 'advanced',
					'toggle_slug'       => 'element_wrapper',
					'css'               => array(
						'main'  => "$this->main_css_element div .post-elements",
						'hover' => "$this->main_css_element div .post-elements:hover",
					),
					'default_on_fronts' => array(
						'color'    => 'rgba(0,0,0,0.3)',
						'position' => 'outer',
					),
				),
				'element_icon_element' => array(
					'label'               => esc_html__( 'Icon Box Shadow', 'post-grid-module-for-divi' ),
					'option_category'     => 'layout',
					'tab_slug'            => 'advanced',
					'toggle_slug'         => 'element_icon_element',
					'css'                 => array(
						'main'    => "$this->main_css_element div .post-elements span.squad-element-icon-wrapper .icon-element",
						'hover'   => "$this->main_css_element div .post-elements:hover span.squad-element-icon-wrapper .icon-element",
						'overlay' => 'inset',
					),
					'default_on_fronts'   => array(
						'color'    => '',
						'position' => '',
					),
					'depends_on'          => array( 'element_icon_type' ),
					'depends_show_if_not' => array( 'none' ),
				),
				'element'              => array(
					'label'               => esc_html__( 'Element Box Shadow', 'post-grid-module-for-divi' ),
					'option_category'     => 'layout',
					'tab_slug'            => 'advanced',
					'toggle_slug'         => 'element',
					'css'                 => array(
						'main'  => "$this->main_css_element div .post-elements .squad-post-element",
						'hover' => "$this->main_css_element div .post-elements:hover .squad-post-element",
					),
					'default_on_fronts'   => array(
						'color'    => 'rgba(0,0,0,0.3)',
						'position' => 'outer',
					),
					'depends_on'          => array( 'element' ),
					'depends_show_if_not' => array( 'avatar' ),
				),
			),
			'margin_padding'       => Module::selectors_margin_padding( $this->main_css_element ),
			'max_width'            => Module::selectors_max_width( $this->main_css_element ),
			'height'               => Module::selectors_default( $this->main_css_element ),
			'image_icon'           => false,
			'text'                 => false,
			'button'               => false,
			'link_options'         => false,
		);

		// Declare custom css fields for the module.
		$this->custom_css_fields = array(
			'element_title_icon' => array(
				'label'    => esc_html__( 'Title Icon', 'post-grid-module-for-divi' ),
				'selector' => 'div .post-elements .squad-post-element span.squad-element_title-icon.et-pb-icon',
			),
			'element_image'      => array(
				'label'    => esc_html__( 'Feature Image', 'post-grid-module-for-divi' ),
				'selector' => 'div .post-elements .squad-element-icon-wrapper .et_pb_image_wrap',
			),
			'element_wrapper'    => array(
				'label'    => esc_html__( 'Wrapper', 'post-grid-module-for-divi' ),
				'selector' => 'div .post-elements',
			),
			'element'            => array(
				'label'    => esc_html__( 'Element', 'post-grid-module-for-divi' ),
				'selector' => 'div .post-elements .squad-post-element',
			),
		);
	}

	/**
	 * Declare general fields for the module
	 *
	 * @return array[]
	 * @since 1.0.0
	 */
	public function get_fields() {
		// Text fields definitions.
		$text_fields = array(
			'element'                         => $this->squad_add_select_box_field(
				esc_html__( 'Element Type', 'post-grid-module-for-divi' ),
				array(
					'description'      => esc_html__( 'Choose an element type to display for current post.', 'post-grid-module-for-divi' ),
					'options'          => array_merge( array( 'none' => esc_html__( 'Choose a type', 'post-grid-module-for-divi' ) ), $this->element_types ),
					'default'          => 'none',
					'default_on_front' => 'none',
					'affects'          => array(
						'element_title_tag',
						'element_title_icon__enable',
						'element_image_fullwidth__enable',
						'element_excerpt__enable',
						'element_ex_con_length__enable',
						'element_author_name_type',
						'element_gravatar_size',
						'element_date_type',
						'element_read_more_text',
						'element_comments_before',
						'element_comments_after',
						'element_categories_sepa',
						'element_tags_sepa',
						'element_custom_text',
						'show_divider',
						'element_outside__enable',
						'element_icon_type',
						'element_icon_on_hover',
						'element_margin',
						'element_padding',
					),
					'tab_slug'         => 'general',
					'toggle_slug'      => 'elements',
				)
			),
			'element_title_tag'               => $this->squad_add_select_box_field(
				esc_html__( 'Title Tag', 'post-grid-module-for-divi' ),
				array(
					'description'     => esc_html__( 'Choose a tag to display with your title element.', 'post-grid-module-for-divi' ),
					'options'         => $this->squad_get_html_tag_elements(),
					'default'         => 'span',
					'depends_show_if' => 'title',
					'tab_slug'        => 'general',
					'toggle_slug'     => 'elements',
				)
			),
			'element_image_fullwidth__enable' => $this->squad_add_yes_no_field(
				esc_html__( 'Force Fullwidth', 'post-grid-module-for-divi' ),
				array(
					'description'      => esc_html__( 'Here you can choose whether or not the image element is full width.', 'post-grid-module-for-divi' ),
					'default_on_front' => 'off',
					'depends_show_if'  => 'image',
					'tab_slug'         => 'general',
					'toggle_slug'      => 'elements',
				)
			),
			'element_excerpt__enable'         => $this->squad_add_yes_no_field(
				esc_html__( 'Show Post Excerpt', 'post-grid-module-for-divi' ),
				array(
					'description'      => esc_html__( 'Here you can choose whether or not show post excerpt.', 'post-grid-module-for-divi' ),
					'default_on_front' => 'off',
					'depends_show_if'  => 'content',
					'tab_slug'         => 'general',
					'toggle_slug'      => 'elements',
				)
			),
			'element_ex_con_length__enable'   => $this->squad_add_yes_no_field(
				esc_html__( 'Enable Text Limit', 'post-grid-module-for-divi' ),
				array(
					'description'      => esc_html__( 'Here you can choose whether or not custom length for post content or excerpt text limit.', 'post-grid-module-for-divi' ),
					'default_on_front' => 'off',
					'depends_show_if'  => 'content',
					'affects'          => array(
						'element_ex_con_length',
					),
					'tab_slug'         => 'general',
					'toggle_slug'      => 'elements',
				)
			),
			'element_ex_con_length'           => $this->squad_add_range_field(
				esc_html__( 'Text Limit', 'post-grid-module-for-divi' ),
				array(
					'description'       => esc_html__( 'Here you can choose how much text you would like to display for post content or excerpt.', 'post-grid-module-for-divi' ),
					'type'              => 'range',
					'range_settings'    => array(
						'min'       => '1',
						'max'       => '1000',
						'step'      => '1',
						'min_limit' => '1',
					),
					'default'           => 20,
					'number_validation' => true,
					'fixed_range'       => true,
					'unitless'          => true,
					'hover'             => false,
					'mobile_options'    => false,
					'responsive'        => false,
					'depends_show_if'   => 'on',
					'tab_slug'          => 'general',
					'toggle_slug'       => 'elements',
				)
			),
			'element_author_name_type'        => $this->squad_add_select_box_field(
				esc_html__( 'Author Name Type', 'post-grid-module-for-divi' ),
				array(
					'description'     => esc_html__( 'Choose a author name type to display for current post.', 'post-grid-module-for-divi' ),
					'options'         => array(
						'nickname'     => esc_html__( 'Nickname', 'post-grid-module-for-divi' ),
						'display-name' => esc_html__( 'Display Name', 'post-grid-module-for-divi' ),
						'full-name'    => esc_html__( 'Full Name', 'post-grid-module-for-divi' ),
						'first-name'   => esc_html__( 'First Name', 'post-grid-module-for-divi' ),
						'last-name'    => esc_html__( 'Last Name', 'post-grid-module-for-divi' ),
					),
					'default'         => 'nickname',
					'depends_show_if' => 'author',
					'tab_slug'        => 'general',
					'toggle_slug'     => 'elements',
				)
			),
			'element_gravatar_size'           => $this->squad_add_range_field(
				esc_html__( 'Gravatar Size', 'post-grid-module-for-divi' ),
				array(
					'description'     => esc_html__( 'Choose a author name type to display for current post.', 'post-grid-module-for-divi' ),
					'range_settings'  => array(
						'min'  => '1',
						'max'  => '200',
						'step' => '1',
					),
					'default'         => 40,
					'unitless'        => true,
					'depends_show_if' => 'gravatar',
					'tab_slug'        => 'general',
					'toggle_slug'     => 'elements',
				)
			),
			'element_date_type'               => $this->squad_add_select_box_field(
				esc_html__( 'Date Type', 'post-grid-module-for-divi' ),
				array(
					'description'     => esc_html__( 'Choose a date type to display for current post.', 'post-grid-module-for-divi' ),
					'options'         => array(
						'publish'  => esc_html__( 'Publish', 'post-grid-module-for-divi' ),
						'modified' => esc_html__( 'Modified', 'post-grid-module-for-divi' ),
					),
					'default'         => 'publish',
					'depends_show_if' => 'date',
					'tab_slug'        => 'general',
					'toggle_slug'     => 'elements',
				)
			),
			'element_read_more_text'          => array(
				'label'           => esc_html__( 'Read More Text', 'post-grid-module-for-divi' ),
				'description'     => esc_html__( 'The text will appear in with your read more element.', 'post-grid-module-for-divi' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'depends_show_if' => 'read_more',
				'tab_slug'        => 'general',
				'toggle_slug'     => 'elements',
				'dynamic_content' => 'text',
				'hover'           => 'tabs',
				'mobile_options'  => true,
			),
			'element_comments_before'         => array(
				'label'           => esc_html__( 'Before Text', 'post-grid-module-for-divi' ),
				'description'     => esc_html__( 'The before text of your title will appear in with your post element.', 'post-grid-module-for-divi' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'depends_show_if' => 'comments',
				'tab_slug'        => 'general',
				'toggle_slug'     => 'elements',
				'dynamic_content' => 'text',
				'hover'           => 'tabs',
				'mobile_options'  => true,
			),
			'element_comments_after'          => array(
				'label'           => esc_html__( 'After Text', 'post-grid-module-for-divi' ),
				'description'     => esc_html__( 'The after text of your title will appear in with your post element.', 'post-grid-module-for-divi' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'depends_show_if' => 'comments',
				'tab_slug'        => 'general',
				'toggle_slug'     => 'elements',
				'dynamic_content' => 'text',
				'hover'           => 'tabs',
				'mobile_options'  => true,
			),
			'element_categories_sepa'         => array(
				'label'           => esc_html__( 'Categories Separator', 'post-grid-module-for-divi' ),
				'description'     => esc_html__( 'The seperator text of your categories will appear in with your categories element.', 'post-grid-module-for-divi' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'depends_show_if' => 'categories',
				'tab_slug'        => 'general',
				'toggle_slug'     => 'elements',
				'dynamic_content' => 'text',
				'hover'           => 'tabs',
				'mobile_options'  => true,
			),
			'element_tags_sepa'               => array(
				'label'           => esc_html__( 'Tags Separator', 'post-grid-module-for-divi' ),
				'description'     => esc_html__( 'The seperator text of your tags will appear in with your tags element.', 'post-grid-module-for-divi' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'depends_show_if' => 'tags',
				'tab_slug'        => 'general',
				'toggle_slug'     => 'elements',
				'dynamic_content' => 'text',
				'hover'           => 'tabs',
				'mobile_options'  => true,
			),
			'element_custom_text'             => array(
				'label'           => esc_html__( 'Custom Text', 'post-grid-module-for-divi' ),
				'description'     => esc_html__( 'The text will appear in with your custom text element.', 'post-grid-module-for-divi' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'depends_show_if' => 'custom_text',
				'tab_slug'        => 'general',
				'toggle_slug'     => 'elements',
				'dynamic_content' => 'text',
				'hover'           => 'tabs',
				'mobile_options'  => true,
			),
			'element_outside__enable'         => $this->squad_add_yes_no_field(
				esc_html__( 'Show Outside the Container', 'post-grid-module-for-divi' ),
				array(
					'description'         => esc_html__( 'Here you can choose whether or not show avatar in the outside container.', 'post-grid-module-for-divi' ),
					'default_on_front'    => 'off',
					'depends_show_if_not' => array( 'none' ),
					'tab_slug'            => 'general',
					'toggle_slug'         => 'elements',
				)
			),
		);

		$divider_fields = $this->squad_get_divider_element_fields(
			array(
				'depends_show_if' => 'divider',
				'toggle_slug'     => 'element_divider',
			)
		);

		// Icon & Image fields definitions.
		$icon_image_fields_all = array(
			'element_icon_type'                => $this->squad_add_select_box_field(
				esc_html__( 'Icon Type', 'post-grid-module-for-divi' ),
				array(
					'description'         => esc_html__( 'Choose an icon type to display with your post.', 'post-grid-module-for-divi' ),
					'options'             => array(
						'icon'  => esc_html__( 'Icon', 'post-grid-module-for-divi' ),
						'image' => et_builder_i18n( 'Image' ),
						'text'  => et_builder_i18n( 'Text' ),
						'none'  => esc_html__( 'None', 'post-grid-module-for-divi' ),
					),
					'default_on_front'    => 'icon',
					'default'             => 'icon',
					'depends_show_if_not' => array_merge( array( '' ), $this->icon_not_eligible_elements ),
					'affects'             => array(
						'element_icon',
						'element_image',
						'element_icon_text',
						'element_icon_text_font',
						'element_icon_text_text_color',
						'element_icon_text_text_align',
						'element_icon_text_font_size',
						'element_icon_text_letter_spacing',
						'element_icon_text_line_height',
						'element_icon_text_text_shadow_style',
						'element_icon_color',
						'element_icon_background_color',
						'element_icon_size',
						'element_image_width',
						'element_image_height',
						'alt',
						'element_icon_text_gap',
						'element_icon_placement',
						'element_icon_margin',
						'element_icon_padding',
					),
					'tab_slug'            => 'general',
					'toggle_slug'         => 'element_icon_element',
				)
			),
			'element_icon'                     => array(
				'label'            => esc_html__( 'Choose an icon', 'post-grid-module-for-divi' ),
				'description'      => esc_html__( 'Choose an icon to display with your post.', 'post-grid-module-for-divi' ),
				'type'             => 'select_icon',
				'option_category'  => 'basic_option',
				'class'            => array( 'et-pb-font-icon' ),
				'default_on_front' => '&#x4e;||divi||400',
				'depends_show_if'  => 'icon',
				'tab_slug'         => 'general',
				'toggle_slug'      => 'element_icon_element',
				'hover'            => 'tabs',
				'mobile_options'   => true,
			),
			'element_image'                    => array(
				'label'              => et_builder_i18n( 'Image' ),
				'description'        => esc_html__( 'Upload an image to display at the top of your post.', 'post-grid-module-for-divi' ),
				'type'               => 'upload',
				'option_category'    => 'basic_option',
				'upload_button_text' => et_builder_i18n( 'Upload an image' ),
				'choose_text'        => esc_attr__( 'Choose an Image', 'post-grid-module-for-divi' ),
				'update_text'        => esc_attr__( 'Set As Image', 'post-grid-module-for-divi' ),
				'depends_show_if'    => 'image',
				'tab_slug'           => 'general',
				'toggle_slug'        => 'element_icon_element',
				'hover'              => 'tabs',
				'dynamic_content'    => 'image',
				'mobile_options'     => true,
			),
			'alt'                              => array(
				'label'           => esc_html__( 'Image Alt Text', 'post-grid-module-for-divi' ),
				'description'     => esc_html__( 'Define the HTML ALT text for your image here.', 'post-grid-module-for-divi' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'depends_show_if' => 'image',
				'tab_slug'        => 'general',
				'toggle_slug'     => 'element_icon_element',
				'dynamic_content' => 'text',
			),
			'element_icon_text'                => array(
				'label'           => et_builder_i18n( 'Text' ),
				'description'     => esc_html__( 'The text of your post will appear in bold as icon.', 'post-grid-module-for-divi' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'depends_show_if' => 'text',
				'dynamic_content' => 'text',
				'tab_slug'        => 'general',
				'toggle_slug'     => 'element_icon_element',
			),
			'element_title_icon__enable'       => $this->squad_add_yes_no_field(
				esc_html__( 'Use Left Icon for Title', 'post-grid-module-for-divi' ),
				array(
					'description'      => esc_html__( 'Here you can choose whether or not use icon for the post title.', 'post-grid-module-for-divi' ),
					'default'          => 'off',
					'default_on_front' => 'off',
					'depends_show_if'  => 'title',
					'affects'          => array(
						'element_title_icon',
						'element_title_icon_show_on_hover',
						'element_title_icon_color',
						'element_title_icon_size',
						'element_title_icon_margin',
						'element_title_icon_padding',
					),
					'tab_slug'         => 'general',
					'toggle_slug'      => 'element_icon_element',
				)
			),
			'element_title_icon'               => array(
				'label'            => esc_html__( 'Choose an icon', 'post-grid-module-for-divi' ),
				'description'      => esc_html__( 'Choose an icon to display with your post title.', 'post-grid-module-for-divi' ),
				'type'             => 'select_icon',
				'option_category'  => 'basic_option',
				'class'            => array( 'et-pb-font-icon' ),
				'default_on_front' => '&#x24;||divi||400',
				'depends_show_if'  => 'on',
				'tab_slug'         => 'general',
				'toggle_slug'      => 'element_icon_element',
			),
			'element_title_icon_show_on_hover' => $this->squad_add_yes_no_field(
				esc_html__( 'Show Title Icon On Hover', 'post-grid-module-for-divi' ),
				array(
					'description'      => esc_html__( 'By default, post title icon on hover be displayed. If you would like post title icon is displayed all time, then you can enable this option.', 'post-grid-module-for-divi' ),
					'default_on_front' => 'off',
					'depends_show_if'  => 'on',
					'tab_slug'         => 'general',
					'toggle_slug'      => 'element_icon_element',
				)
			),
		);

		// Icon & Image associate fields definitions.
		$icon_image_associated_fields_all = array(
			'element_icon_color'                => $this->squad_add_color_field(
				esc_html__( 'Icon Color', 'post-grid-module-for-divi' ),
				array(
					'description'     => esc_html__( 'Here you can define a custom color for your icon.', 'post-grid-module-for-divi' ),
					'depends_show_if' => 'icon',
					'tab_slug'        => 'advanced',
					'toggle_slug'     => 'element_icon_element',
				)
			),
			'element_icon_background_color'     => $this->squad_add_color_field(
				esc_html__( 'Icon Background Color', 'post-grid-module-for-divi' ),
				array(
					'description'         => esc_html__( 'Here you can define a custom background color.', 'post-grid-module-for-divi' ),
					'depends_show_if_not' => array( 'none', 'image' ),
					'tab_slug'            => 'advanced',
					'toggle_slug'         => 'element_icon_element',
				)
			),
			'element_title_icon_color'          => $this->squad_add_color_field(
				esc_html__( 'Title Icon Color', 'post-grid-module-for-divi' ),
				array(
					'description'     => esc_html__( 'Here you can define a custom color for your icon.', 'post-grid-module-for-divi' ),
					'depends_show_if' => 'on',
					'tab_slug'        => 'advanced',
					'toggle_slug'     => 'element_icon_element',
				)
			),
			'element_icon_size'                 => $this->squad_add_range_field(
				esc_html__( 'Icon Size', 'post-grid-module-for-divi' ),
				array(
					'description'     => esc_html__( 'Here you can choose icon size.', 'post-grid-module-for-divi' ),
					'range_settings'  => array(
						'min'  => '1',
						'max'  => '200',
						'step' => '1',
					),
					'depends_show_if' => 'icon',
					'default'         => '16px',
					'default_unit'    => 'px',
					'tab_slug'        => 'advanced',
					'toggle_slug'     => 'element_icon_element',
				)
			),
			'element_image_width'               => $this->squad_add_range_field(
				esc_html__( 'Image Width', 'post-grid-module-for-divi' ),
				array(
					'description'     => esc_html__( 'Here you can choose image width.', 'post-grid-module-for-divi' ),
					'range_settings'  => array(
						'min'  => '1',
						'max'  => '200',
						'step' => '1',
					),
					'default'         => '16px',
					'depends_show_if' => 'image',
					'tab_slug'        => 'advanced',
					'toggle_slug'     => 'element_icon_element',
				)
			),
			'element_image_height'              => $this->squad_add_range_field(
				esc_html__( 'Image Height', 'post-grid-module-for-divi' ),
				array(
					'description'     => esc_html__( 'Here you can choose image height.', 'post-grid-module-for-divi' ),
					'range_settings'  => array(
						'min'  => '1',
						'max'  => '200',
						'step' => '1',
					),
					'default'         => '16px',
					'depends_show_if' => 'image',
					'tab_slug'        => 'advanced',
					'toggle_slug'     => 'element_icon_element',
				)
			),
			'element_title_icon_size'           => $this->squad_add_range_field(
				esc_html__( 'Title Icon Size', 'post-grid-module-for-divi' ),
				array(
					'description'     => esc_html__( 'Here you can choose icon size.', 'post-grid-module-for-divi' ),
					'range_settings'  => array(
						'min'  => '1',
						'max'  => '200',
						'step' => '1',
					),
					'depends_show_if' => 'on',
					'default_unit'    => 'px',
					'tab_slug'        => 'advanced',
					'toggle_slug'     => 'element_icon_element',
				)
			),
			'element_icon_text_gap'             => $this->squad_add_range_field(
				esc_html__( 'Gap Between Icon and Text', 'post-grid-module-for-divi' ),
				array(
					'description'         => esc_html__( 'Here you can choose gap between icon and text.', 'post-grid-module-for-divi' ),
					'range_settings'      => array(
						'min'  => '1',
						'max'  => '200',
						'step' => '1',
					),
					'default_unit'        => 'px',
					'depends_show_if_not' => array( 'none' ),
					'tab_slug'            => 'advanced',
					'toggle_slug'         => 'element_icon_element',
					'mobile_options'      => true,
				),
				array( 'use_hover' => false )
			),
			'element_icon_placement'            => $this->squad_add_placement_field(
				esc_html__( 'Icon Placement', 'post-grid-module-for-divi' ),
				array(
					'description'         => esc_html__( 'Here you can choose where to place the icon.', 'post-grid-module-for-divi' ),
					'options'             => array(
						'column'      => et_builder_i18n( 'Top' ),
						'row'         => et_builder_i18n( 'Left' ),
						'row-reverse' => et_builder_i18n( 'Right' ),
					),
					'default_on_front'    => 'row',
					'depends_show_if_not' => array( 'none' ),
					'affects'             => array(
						'element_icon_horizontal_alignment',
						'element_icon_vertical_alignment',
					),
					'tab_slug'            => 'advanced',
					'toggle_slug'         => 'element_icon_element',
				)
			),
			'element_icon_horizontal_alignment' => $this->squad_add_alignment_field(
				esc_html__( 'Icon Horizontal Alignment', 'post-grid-module-for-divi' ),
				array(
					'description'      => esc_html__( 'Align icon to the left, right or center.', 'post-grid-module-for-divi' ),
					'default_on_front' => 'left',
					'depends_show_if'  => 'column',
					'tab_slug'         => 'advanced',
					'toggle_slug'      => 'element_icon_element',
				)
			),
			'element_icon_vertical_alignment'   => $this->squad_add_select_box_field(
				esc_html__( 'Icon Vertical Placement', 'post-grid-module-for-divi' ),
				array(
					'description'         => esc_html__( 'Here you can choose where to place the icon.', 'post-grid-module-for-divi' ),
					'options'             => array(
						'flex-start' => esc_html__( 'Top', 'post-grid-module-for-divi' ),
						'center'     => esc_html__( 'Center', 'post-grid-module-for-divi' ),
						'flex-end'   => esc_html__( 'Bottom', 'post-grid-module-for-divi' ),
					),
					'default_on_front'    => 'flex-start',
					'depends_show_if_not' => array( 'column' ),
					'tab_slug'            => 'advanced',
					'toggle_slug'         => 'element_icon_element',
					'mobile_options'      => true,
				)
			),
			'element_icon_on_hover'             => $this->squad_add_yes_no_field(
				esc_html__( 'Show Icon On Hover', 'post-grid-module-for-divi' ),
				array(
					'description'         => esc_html__( 'By default, post element icon to always be displayed. If you would like post element icon are displayed on hover, then you can enable this option.', 'post-grid-module-for-divi' ),
					'default_on_front'    => 'off',
					'depends_show_if_not' => array_merge( array( '' ), $this->icon_not_eligible_elements ),
					'affects'             => array(
						'element_icon_hover_move_icon',
					),
					'tab_slug'            => 'advanced',
					'toggle_slug'         => 'element_icon_element',
				)
			),
			'element_icon_hover_move_icon'      => $this->squad_add_yes_no_field(
				esc_html__( 'Move Icon On Hover Only', 'post-grid-module-for-divi' ),
				array(
					'description'      => esc_html__( 'By default, icon and text are both move on hover. If you would like post element icon move on hover, then you can enable this option.', 'post-grid-module-for-divi' ),
					'default_on_front' => 'off',
					'depends_show_if'  => 'on',
					'tab_slug'         => 'advanced',
					'toggle_slug'      => 'element_icon_element',
				)
			),
			'element_icon_margin'               => $this->squad_add_margin_padding_field(
				esc_html__( 'Icon Margin', 'post-grid-module-for-divi' ),
				array(
					'description'         => esc_html__( 'Here you can define a custom margin size for the icon.', 'post-grid-module-for-divi' ),
					'type'                => 'custom_margin',
					'depends_show_if_not' => array( 'none' ),
					'tab_slug'            => 'advanced',
					'toggle_slug'         => 'element_icon_element',
				)
			),
			'element_icon_padding'              => $this->squad_add_margin_padding_field(
				esc_html__( 'Icon Padding', 'post-grid-module-for-divi' ),
				array(
					'description'         => esc_html__( 'Here you can define a custom padding size.', 'post-grid-module-for-divi' ),
					'type'                => 'custom_padding',
					'depends_show_if_not' => array( 'none' ),
					'tab_slug'            => 'advanced',
					'toggle_slug'         => 'element_icon_element',
				)
			),
			'element_title_icon_margin'         => $this->squad_add_margin_padding_field(
				esc_html__( 'Title Icon Margin', 'post-grid-module-for-divi' ),
				array(
					'description'     => esc_html__( 'Here you can define a custom margin size for the icon.', 'post-grid-module-for-divi' ),
					'type'            => 'custom_margin',
					'depends_show_if' => 'on',
					'default'         => '|||10px|false|false',
					'tab_slug'        => 'advanced',
					'toggle_slug'     => 'element_icon_element',
				)
			),
			'element_title_icon_padding'        => $this->squad_add_margin_padding_field(
				esc_html__( 'Title Icon Padding', 'post-grid-module-for-divi' ),
				array(
					'description'     => esc_html__( 'Here you can define a custom padding size.', 'post-grid-module-for-divi' ),
					'type'            => 'custom_padding',
					'depends_show_if' => 'on',
					'tab_slug'        => 'advanced',
					'toggle_slug'     => 'element_icon_element',
				)
			),
		);

		// The post-wrapper fields definitions.
		$wrapper_background_fields = $this->squad_add_background_field(
			esc_html__( 'Background', 'post-grid-module-for-divi' ),
			array(
				'base_name'   => 'element_wrapper_background',
				'context'     => 'element_wrapper_background_color',
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'element_wrapper',
			)
		);
		$element_wrapper_fields    = array(
			'element_text_orientation' => $this->squad_add_alignment_field(
				esc_html__( 'Content Alignment', 'post-grid-module-for-divi' ),
				array(
					'description' => esc_html__( 'This controls how your text is aligned within the module.', 'post-grid-module-for-divi' ),
					'type'        => 'text_align',
					'options'     => et_builder_get_text_orientation_options( array( 'justified' ) ),
					'default'     => '',
					'tab_slug'    => 'advanced',
					'toggle_slug' => 'element_wrapper',
				)
			),
			'element_wrapper_margin'   => $this->squad_add_margin_padding_field(
				esc_html__( 'Margin', 'post-grid-module-for-divi' ),
				array(
					'description' => esc_html__( 'Here you can define a custom margin size for the wrapper.', 'post-grid-module-for-divi' ),
					'type'        => 'custom_margin',
					'tab_slug'    => 'advanced',
					'toggle_slug' => 'element_wrapper',
				)
			),
			'element_wrapper_padding'  => $this->squad_add_margin_padding_field(
				esc_html__( 'Padding', 'post-grid-module-for-divi' ),
				array(
					'description' => esc_html__( 'Here you can define a custom padding size.', 'post-grid-module-for-divi' ),
					'type'        => 'custom_padding',
					'tab_slug'    => 'advanced',
					'toggle_slug' => 'element_wrapper',
				)
			),
		);

		// Element associate fields definitions.
		$element_background_fields = $this->squad_add_background_field(
			esc_html__( 'Background', 'post-grid-module-for-divi' ),
			array(
				'base_name'   => 'element_background',
				'context'     => 'element_background_color',
				'show_if_not' => array(
					'element' => array( 'avatar' ),
				),
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'element',
			)
		);
		$element_associated_fields = array(
			'element_margin'  => $this->squad_add_margin_padding_field(
				esc_html__( 'Margin', 'post-grid-module-for-divi' ),
				array(
					'description'         => esc_html__( 'Here you can define a custom margin size.', 'post-grid-module-for-divi' ),
					'type'                => 'custom_margin',
					'range_settings'      => array(
						'min'  => '1',
						'max'  => '100',
						'step' => '1',
					),
					'depends_show_if_not' => array( 'avatar' ),
					'tab_slug'            => 'advanced',
					'toggle_slug'         => 'element',
				)
			),
			'element_padding' => $this->squad_add_margin_padding_field(
				esc_html__( 'Padding', 'post-grid-module-for-divi' ),
				array(
					'description'         => esc_html__( 'Here you can define a custom padding size.', 'post-grid-module-for-divi' ),
					'type'                => 'custom_padding',
					'range_settings'      => array(
						'min'  => '1',
						'max'  => '100',
						'step' => '1',
					),
					'depends_show_if_not' => array( 'avatar' ),
					'tab_slug'            => 'advanced',
					'toggle_slug'         => 'element',
				)
			),
		);

		return array_merge(
			$text_fields,
			$divider_fields,
			$icon_image_fields_all,
			$icon_image_associated_fields_all,
			$wrapper_background_fields,
			$element_wrapper_fields,
			$element_background_fields,
			$element_associated_fields,
			$this->squad_get_general_fields()
		);
	}

	/**
	 * Get CSS fields transition.
	 *
	 * Add form field options group and background image on the fields list.
	 *
	 * @since 1.0.0
	 */
	public function get_transition_fields_css_props() {
		$fields = parent::get_transition_fields_css_props();

		// wrapper styles.
		$fields['element_wrapper_background_color'] = array( 'background' => "$this->main_css_element div .post-elements" );
		$fields['element_wrapper_margin']           = array( 'margin' => "$this->main_css_element div .post-elements" );
		$fields['element_wrapper_padding']          = array( 'padding' => "$this->main_css_element div .post-elements" );
		$this->squad_fix_border_transition( $fields, 'element_wrapper', "$this->main_css_element div .post-elements" );
		$this->squad_fix_box_shadow_transition( $fields, 'element_wrapper', "$this->main_css_element div .post-elements" );

		// icon styles.
		$fields['element_icon_background_color'] = array( 'background-color' => "$this->main_css_element div .post-elements span.squad-element-icon-wrapper .icon-element" );
		$fields['element_icon_color']            = array( 'color' => "$this->main_css_element div .post-elements span.squad-element-icon-wrapper .icon-element .et-pb-icon" );
		$fields['element_icon_size']             = array( 'font-size' => "$this->main_css_element div .post-elements span.squad-element-icon-wrapper .icon-element .et-pb-icon" );
		$fields['element_image_width']           = array( 'width' => "$this->main_css_element div .post-elements span.squad-element-icon-wrapper img" );
		$fields['element_image_height']          = array( 'height' => "$this->main_css_element div .post-elements span.squad-element-icon-wrapper img" );
		$fields['element_icon_margin']           = array( 'margin' => "$this->main_css_element div .post-elements span.squad-element-icon-wrapper .icon-element" );
		$fields['element_icon_padding']          = array( 'padding' => "$this->main_css_element div .post-elements span.squad-element-icon-wrapper .icon-element" );
		$this->squad_fix_border_transition( $fields, 'element_icon_element', "$this->main_css_element div .post-elements span.squad-element-icon-wrapper .icon-element" );
		$this->squad_fix_box_shadow_transition( $fields, 'element_icon_element', "$this->main_css_element div .post-elements span.squad-element-icon-wrapper .icon-element" );

		// element styles.
		$fields['element_background_color'] = array( 'background' => "$this->main_css_element div .post-elements .squad-post-element" );
		$fields['element_margin']           = array( 'margin' => "$this->main_css_element div .post-elements .squad-post-element" );
		$fields['element_padding']          = array( 'padding' => "$this->main_css_element div .post-elements .squad-post-element" );
		$this->squad_fix_border_transition( $fields, 'element', "$this->main_css_element div .post-elements .squad-post-element" );
		$this->squad_fix_box_shadow_transition( $fields, 'element', "$this->main_css_element div .post-elements .squad-post-element" );

		// title icon styles.
		$fields['element_title_icon_color']   = array( 'color' => "$this->main_css_element div .post-elements .squad-post-element span.squad-element_title-icon.et-pb-icon" );
		$fields['element_title_icon_size']    = array( 'font-size' => "$this->main_css_element div .post-elements .squad-post-element span.squad-element_title-icon.et-pb-icon" );
		$fields['element_title_icon_margin']  = array( 'margin' => "$this->main_css_element div .post-elements .squad-post-element span.squad-element_title-icon.et-pb-icon" );
		$fields['element_title_icon_padding'] = array( 'padding' => "$this->main_css_element div .post-elements .squad-post-element span.squad-element_title-icon.et-pb-icon" );

		// Default styles.
		$fields['background_layout'] = array( 'color' => "$this->main_css_element div .post-elements .squad-post-element" );

		return $fields;
	}

	/**
	 * Renders the module output.
	 *
	 * @param array  $attrs       List of attributes.
	 * @param string $content     Content being processed.
	 * @param string $render_slug Slug of module that is used for rendering output.
	 *
	 * @return string|null
	 */
	public function render( $attrs, $content, $render_slug ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundInExtendedClassBeforeLastUsed, Generic.CodeAnalysis.UnusedFunctionParameter.FoundInExtendedClassAfterLastUsed
		if ( 'none' !== $this->prop( 'element', 'none' ) ) {
			$this->squad_generate_all_styles( $attrs );
			$this->squad_generate_element_title_font_icon_styles( $attrs );
			$this->squad_generate_all_icon_styles( $attrs );
		}

		// Render json code to communicate with the parent module.
		return wp_json_encode( $this->props ) . ',||';
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
		$this->props  = array_merge( $attrs, $this->props );
		$element_type = ! empty( $attrs['element'] ) ? $attrs['element'] : 'none';

		if ( 'image' === $element_type && 'on' === $this->prop( 'element_image_fullwidth__enable', ' off' ) ) {
			self::set_style(
				$this->slug,
				array(
					'selector'    => "$this->main_css_element div .post-elements .squad-post-element__image, $this->main_css_element div .post-elements .squad-post-element__image img",
					'declaration' => et_builder_get_element_style_css( '100%', 'width', true ),
				)
			);
		}

		// background with default, responsive, hover.
		et_pb_background_options()->get_background_style(
			array(
				'base_prop_name'         => 'element_wrapper_background',
				'props'                  => $this->props,
				'selector'               => "$this->main_css_element div .post-elements",
				'selector_hover'         => "$this->main_css_element div .post-elements:hover",
				'selector_sticky'        => "$this->main_css_element div .post-elements",
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
				'selector'               => "$this->main_css_element div .post-elements .et_pb_with_background",
				'selector_hover'         => "$this->main_css_element div .post-elements:hover .et_pb_with_background",
				'selector_sticky'        => "$this->main_css_element div .post-elements .et_pb_with_background",
				'function_name'          => $this->slug,
				'important'              => ' !important',
				'use_background_video'   => false,
				'use_background_pattern' => false,
				'use_background_mask'    => false,
				'prop_name_aliases'      => array(
					'use_element_background_color_gradient' => 'element_background_use_color_gradient',
					'element_background' => 'element_background_color',
				),
			)
		);

		// content text aligns with default, responsive, hover.
		$this->generate_styles(
			array(
				'base_attr_name' => 'element_text_orientation',
				'selector'       => "$this->main_css_element div .post-elements",
				'css_property'   => 'justify-content',
				'render_slug'    => $this->slug,
				'type'           => 'align',
			)
		);
		$this->generate_styles(
			array(
				'base_attr_name' => 'element_text_orientation',
				'selector'       => "$this->main_css_element div .post-elements .squad-post-element",
				'css_property'   => 'text-align',
				'render_slug'    => $this->slug,
				'type'           => 'align',
			)
		);

		// Process the wrapper margin and padding with default, responsive, hover.
		$this->squad_generate_margin_padding_styles(
			array(
				'field'          => 'element_wrapper_margin',
				'selector'       => "$this->main_css_element div .post-elements",
				'hover_selector' => "$this->main_css_element div .post-elements:hover",
				'css_property'   => 'margin',
				'type'           => 'margin',
			)
		);
		$this->squad_generate_margin_padding_styles(
			array(
				'field'          => 'element_wrapper_padding',
				'selector'       => "$this->main_css_element div .post-elements",
				'hover_selector' => "$this->main_css_element div .post-elements:hover",
				'css_property'   => 'padding',
				'type'           => 'padding',
			)
		);

		// Process the wrapper margin and padding with default, responsive, hover.
		$this->squad_generate_margin_padding_styles(
			array(
				'field'          => 'element_margin',
				'selector'       => "$this->main_css_element div .post-elements .squad-post-element",
				'hover_selector' => "$this->main_css_element div .post-elements:hover .squad-post-element",
				'css_property'   => 'margin',
				'type'           => 'margin',
			)
		);
		$this->squad_generate_margin_padding_styles(
			array(
				'field'          => 'element_padding',
				'selector'       => "$this->main_css_element div .post-elements .squad-post-element",
				'hover_selector' => "$this->main_css_element div .post-elements:hover .squad-post-element",
				'css_property'   => 'padding',
				'type'           => 'padding',
			)
		);
	}

	/**
	 * Render post name icon.
	 *
	 * @param array $attrs List of attributes.
	 *
	 * @return void
	 */
	private function squad_generate_element_title_font_icon_styles( $attrs ) {
		if ( isset( $attrs['element_title_icon__enable'] ) && 'on' === $attrs['element_title_icon__enable'] && '' !== $attrs['element_title_icon'] ) {
			$this->props = array_merge( $this->props, $attrs );

			// Load font Awesome css for frontend.
			Divi::inject_fa_icons( $attrs['element_title_icon'] );

			$this->generate_styles(
				array(
					'utility_arg'    => 'icon_font_family',
					'render_slug'    => $this->slug,
					'base_attr_name' => 'element_title_icon',
					'important'      => true,
					'selector'       => "$this->main_css_element div .post-elements .squad-post-element span.squad-element_title-icon.et-pb-icon",
					'processor'      => array(
						'ET_Builder_Module_Helper_Style_Processor',
						'process_extended_icon',
					),
				)
			);
			$this->generate_styles(
				array(
					'base_attr_name' => 'element_title_icon_color',
					'selector'       => "$this->main_css_element div .post-elements .squad-post-element span.squad-element_title-icon.et-pb-icon",
					'hover_selector' => "$this->main_css_element div .post-elements:hover .squad-post-element span.squad-element_title-icon.et-pb-icon",
					'css_property'   => 'color',
					'render_slug'    => $this->slug,
					'type'           => 'color',
					'important'      => true,
				)
			);
			$this->generate_styles(
				array(
					'base_attr_name' => 'element_title_icon_size',
					'selector'       => "$this->main_css_element div .post-elements .squad-post-element span.squad-element_title-icon.et-pb-icon",
					'hover_selector' => "$this->main_css_element div .post-elements:hover .squad-post-element span.squad-element_title-icon.et-pb-icon",
					'css_property'   => 'font-size',
					'render_slug'    => $this->slug,
					'type'           => 'range',
					'important'      => true,
				)
			);

			// Process the margin and padding with default, responsive, hover.
			$this->squad_generate_margin_padding_styles(
				array(
					'field'          => 'element_title_icon_margin',
					'selector'       => "$this->main_css_element div .post-elements .squad-post-element span.squad-element_title-icon.et-pb-icon",
					'hover_selector' => "$this->main_css_element div .post-elements:hover .squad-post-element span.squad-element_title-icon.et-pb-icon",
					'css_property'   => 'margin',
					'type'           => 'margin',
				)
			);
			$this->squad_generate_margin_padding_styles(
				array(
					'field'          => 'element_title_icon_padding',
					'selector'       => "$this->main_css_element div .post-elements .squad-post-element span.squad-element_title-icon.et-pb-icon",
					'hover_selector' => "$this->main_css_element div .post-elements:hover .squad-post-element span.squad-element_title-icon.et-pb-icon",
					'css_property'   => 'padding',
					'type'           => 'padding',
				)
			);
		}
	}

	/**
	 * Render all styles for icon.
	 *
	 * @param array $attrs List of attributes.
	 *
	 * @return void
	 */
	private function squad_generate_all_icon_styles( $attrs ) {
		$this->props = array_merge( $attrs, $this->props );

		// render icon element for eligible element.
		$icon_type         = ! empty( $attrs['element_icon_type'] ) ? $attrs['element_icon_type'] : 'icon';
		$element_type      = ! empty( $attrs['element'] ) ? $attrs['element'] : 'none';
		$eligible_elements = $this->icon_not_eligible_elements;

		if ( 'none' !== $icon_type && ! in_array( $element_type, $eligible_elements, true ) ) {
			$this->generate_styles(
				array(
					'base_attr_name' => 'element_icon_text_gap',
					'selector'       => "$this->main_css_element div .post-elements",
					'css_property'   => 'gap',
					'render_slug'    => $this->slug,
					'type'           => 'range',
					'important'      => true,
				)
			);

			$this->generate_styles(
				array(
					'base_attr_name' => 'element_icon_placement',
					'selector'       => "$this->main_css_element div .post-elements",
					'css_property'   => 'flex-direction',
					'render_slug'    => $this->slug,
					'type'           => 'align',
					'important'      => true,
				)
			);

			if ( 'image' !== $icon_type ) {
				// Set icon background color.
				$this->generate_styles(
					array(
						'base_attr_name' => 'element_icon_background_color',
						'selector'       => "$this->main_css_element div .post-elements span.squad-element-icon-wrapper .icon-element",
						'hover_selector' => "$this->main_css_element div .post-elements:hover span.squad-element-icon-wrapper .icon-element",
						'css_property'   => 'background-color',
						'render_slug'    => $this->slug,
						'type'           => 'color',
						'important'      => true,
					)
				);
			}

			if ( 'icon' === $icon_type ) {
				// Load font Awesome css for frontend.
				Divi::inject_fa_icons( $this->props['element_icon'] );

				// Set font family for Icon.
				$this->generate_styles(
					array(
						'utility_arg'    => 'icon_font_family',
						'render_slug'    => $this->slug,
						'base_attr_name' => 'element_icon',
						'important'      => true,
						'selector'       => "$this->main_css_element div .post-elements span.squad-element-icon-wrapper .icon-element .et-pb-icon",
						'processor'      => array(
							'ET_Builder_Module_Helper_Style_Processor',
							'process_extended_icon',
						),
					)
				);

				// Set color for Icon.
				$this->generate_styles(
					array(
						'base_attr_name' => 'element_icon_color',
						'selector'       => "$this->main_css_element div .post-elements span.squad-element-icon-wrapper .icon-element .et-pb-icon",
						'hover_selector' => "$this->main_css_element div .post-elements:hover span.squad-element-icon-wrapper .icon-element .et-pb-icon",
						'css_property'   => 'color',
						'render_slug'    => $this->slug,
						'type'           => 'color',
						'important'      => true,
					)
				);

				// Set size for Icon.
				$this->generate_styles(
					array(
						'base_attr_name' => 'element_icon_size',
						'selector'       => "$this->main_css_element div .post-elements span.squad-element-icon-wrapper .icon-element .et-pb-icon",
						'css_property'   => 'font-size',
						'render_slug'    => $this->slug,
						'type'           => 'range',
						'important'      => true,
					)
				);
			}

			if ( 'image' === $icon_type ) {
				// Set width for Image.
				$this->generate_styles(
					array(
						'base_attr_name' => 'element_image_width',
						'selector'       => "$this->main_css_element div .post-elements span.squad-element-icon-wrapper img",
						'hover_selector' => "$this->main_css_element div .post-elements:hover span.squad-element-icon-wrapper img",
						'css_property'   => 'width',
						'render_slug'    => $this->slug,
						'type'           => 'range',
						'important'      => true,
					)
				);
				// Set height for Image.
				$this->generate_styles(
					array(
						'base_attr_name' => 'element_image_height',
						'selector'       => "$this->main_css_element div .post-elements span.squad-element-icon-wrapper img",
						'hover_selector' => "$this->main_css_element div .post-elements:hover span.squad-element-icon-wrapper img",
						'css_property'   => 'height',
						'render_slug'    => $this->slug,
						'type'           => 'range',
						'important'      => true,
					)
				);
			}

			// working with icon styles.
			$placement         = 'element_icon_placement';
			$placement_desktop = ! empty( $attrs[ $placement ] ) ? sanitize_text_field( $attrs[ $placement ] ) : 'row';
			$placement_tablet  = ! empty( $attrs[ "{$placement}_tablet" ] ) ? sanitize_text_field( $attrs[ "{$placement}_tablet" ] ) : $placement_desktop;
			$placement_mobile  = ! empty( $attrs[ "{$placement}_phone" ] ) ? sanitize_text_field( $attrs[ "{$placement}_phone" ] ) : $placement_tablet;

			// Icon placement with default, responsive, hover.
			if ( ( 'column' === $placement_desktop ) || ( 'column' === $placement_tablet ) || ( 'column' === $placement_mobile ) ) {
				$this->generate_styles(
					array(
						'base_attr_name' => 'element_icon_horizontal_alignment',
						'selector'       => "$this->main_css_element div .post-elements span.squad-element-icon-wrapper",
						'css_property'   => 'text-align',
						'render_slug'    => $this->slug,
						'type'           => 'align',
						'important'      => true,
					)
				);
			}

			if ( ( 'column' !== $placement_desktop ) || ( 'column' !== $placement_tablet ) || ( 'column' !== $placement_mobile ) ) {
				$this->generate_styles(
					array(
						'base_attr_name' => 'element_icon_vertical_alignment',
						'selector'       => "$this->main_css_element div .post-elements span.squad-element-icon-wrapper",
						'css_property'   => 'align-items',
						'render_slug'    => $this->slug,
						'type'           => 'align',
						'important'      => true,
					)
				);
			}

			if ( isset( $attrs['element_icon_on_hover'] ) && 'on' === $attrs['element_icon_on_hover'] ) {
				$mapping_values = array(
					'column'      => '0 0 -#px 0',
					'row'         => '0 -#px 0 0',
					'row-reverse' => '0 0 0 -#px',
				);

				if ( 'on' === $attrs['element_icon_hover_move_icon'] ) {
					$mapping_values = array(
						'column'      => '#px 0 -#px 0',
						'row'         => '0 -#px 0 #px',
						'row-reverse' => '0 #px 0 -#px',
					);
				}

				// set icon placement for button image with default, hover, and responsive.
				$this->squad_generate_show_icon_on_hover_styles(
					array(
						'props'          => $this->props,
						'field'          => 'element_icon_placement',
						'trigger'        => 'element_icon_type',
						'depends_on'     => array(
							'icon'  => 'element_icon_size',
							'image' => 'element_image_width',
							'text'  => 'element_icon_text_font_size',
						),
						'selector'       => "$this->main_css_element div .post-elements span.squad-element-icon-wrapper.show-on-hover",
						'hover'          => "$this->main_css_element div .post-elements:hover span.squad-element-icon-wrapper.show-on-hover",
						'css_property'   => 'margin',
						'type'           => 'margin',
						'mapping_values' => $mapping_values,
						'defaults'       => array(
							'icon'       => '16px',
							'image'      => '16px',
							'text'       => '16px',
							'field'      => 'row',
							'unit_value' => '0',
						),
					)
				);
			}

			// Icon margin with default, responsive, hover.
			$this->squad_generate_margin_padding_styles(
				array(
					'field'          => 'element_icon_margin',
					'selector'       => "$this->main_css_element div .post-elements span.squad-element-icon-wrapper .icon-element",
					'hover_selector' => "$this->main_css_element div .post-elements:hover span.squad-element-icon-wrapper .icon-element",
					'css_property'   => 'margin',
					'type'           => 'margin',
					'important'      => true,
				)
			);
			$this->squad_generate_margin_padding_styles(
				array(
					'field'          => 'element_icon_padding',
					'selector'       => "$this->main_css_element div .post-elements span.squad-element-icon-wrapper .icon-element",
					'hover_selector' => "$this->main_css_element div .post-elements:hover span.squad-element-icon-wrapper .icon-element",
					'css_property'   => 'padding',
					'type'           => 'padding',
					'important'      => true,
				)
			);

			// Images: Add CSS Filters and Mix Blend Mode rules (if set).
			$this->generate_css_filters(
				$this->slug,
				'child_',
				"$this->main_css_element div .post-elements span.squad-element-icon-wrapper"
			);
		}
	}
}

new PostGridChild();
